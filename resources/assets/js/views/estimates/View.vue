<template>
  <base-page v-if="estimate" class="xl:pl-96">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="pageTitle"> </sw-page-header>

      <div class="flex flex-wrap items-center justify-end">
        <div
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          v-if="permissionModule.update"
        >
          <sw-button
            v-if="estimate.status === 'DRAFT'"
            :disabled="isMarkAsSent"
            variant="primary-outline"
            @click="onMarkAsSent"
            class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          >
            {{ $t('estimates.mark_as_sent') }}
          </sw-button>
        </div>
        <sw-button
          v-if="estimate.status === 'DRAFT'"
          :disabled="isSendingEmail"
          variant="primary"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          @click="onSendEstimate"
        >
          {{ $t('estimates.send_estimate') }}
        </sw-button>
        <sw-dropdown class="w-full md:w-auto md:ml-4 mb-2 md:mb-0">
          <sw-button
            slot="activator"
            variant="primary"
            class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          >
            <dots-horizontal-icon class="h-5" />
          </sw-button>

          <sw-dropdown-item @click="copyPdfUrl">
            <link-icon class="h-5 mr-3 text-primary-800" />
            {{ $t('general.copy_pdf_url') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            :to="`/admin/estimates/${$route.params.id}/edit`"
            tag-name="router-link"
            v-if="permissionModule.update"
          >
            <pencil-icon class="h-5 mr-3 text-primary-800" />
            {{ $t('general.edit') }}
          </sw-dropdown-item>

           <!-- sms estimte -->
           <sw-dropdown-item
               
                @click="sendSMSEstimate()"
              >
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.sendsms_estimate') }}
          </sw-dropdown-item>


          <sw-dropdown-item
            :to="`/admin/customers/${this.estimate.user_id}/view`"
            tag-name="router-link"
            v-if="permissionModule.readCustomer"
          >
            <users-icon class="h-5 mr-3 text-primary-800" />
            {{ $t('customers.customer_go') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            tag-name="router-link"
            :to="{ path: `/admin/users/${this.estimate.assigne_user_id}/view` }"
          >
            <users-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.go_to_asiggned') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            @click="removeEstimate($route.params.id)"
            v-if="permissionModule.delete"
          >
            <trash-icon class="h-5 mr-3 text-primary-800" />
            {{ $t('general.delete') }}
          </sw-dropdown-item>
        </sw-dropdown>
      </div>
    </div>
    <!-- sidebar -->
    <div
      class="fixed top-0 left-0 hidden h-full pt-16 pb-4 ml-56 bg-white xl:ml-64 w-88 xl:block"
    >
      <div
        class="flex items-center justify-between px-4 pt-8 pb-2 border border-gray-200 border-solid height-full"
      >
        <sw-input
          v-model="searchData.searchText"
          :placeholder="$t('general.search')"
          class="mb-6"
          type="text"
          variant="gray"
          @input="onSearched()"
        >
          <search-icon slot="rightIcon" class="h-5" />
        </sw-input>

        <div class="flex mb-6 ml-3" role="group" aria-label="First group">
          <sw-dropdown class="ml-3" position="bottom-start">
            <sw-button slot="activator" size="md" variant="gray-light">
              <filter-icon class="h-5" />
            </sw-button>

            <div
              class="px-2 py-1 pb-2 mb-1 mb-2 text-sm border-b border-gray-200 border-solid"
            >
              {{ $t('general.sort_by') }}
            </div>

            <sw-dropdown-item class="flex px-1 py-2 cursor-pointer">
              <sw-input-group class="-mt-3 font-normal">
                <sw-radio
                  id="filter_estimate_date"
                  v-model="searchData.orderByField"
                  :label="$t('reports.estimates.estimate_date')"
                  size="sm"
                  name="filter"
                  value="estimate_date"
                  @change="onSearched"
                />
              </sw-input-group>
            </sw-dropdown-item>

            <sw-dropdown-item class="flex px-1 py-2 cursor-pointer">
              <sw-input-group class="-mt-3 font-normal">
                <sw-radio
                  id="filter_due_date"
                  v-model="searchData.orderByField"
                  :label="$t('estimates.due_date')"
                  value="expiry_date"
                  size="sm"
                  name="filter"
                  @change="onSearched"
                />
              </sw-input-group>
            </sw-dropdown-item>

            <sw-dropdown-item class="flex px-1 py-2 cursor-pointer">
              <sw-input-group class="-mt-3 font-normal">
                <sw-radio
                  id="filter_estimate_number"
                  v-model="searchData.orderByField"
                  :label="$t('estimates.estimate_number')"
                  value="estimate_number"
                  size="sm"
                  name="filter"
                  @change="onSearched"
                />
              </sw-input-group>
            </sw-dropdown-item>
          </sw-dropdown>

          <sw-button
            v-tooltip.top-center="{ content: getOrderName }"
            class="ml-1"
            size="md"
            variant="gray-light"
            @click="sortData"
          >
            <sort-ascending-icon v-if="getOrderBy" class="h-5" />
            <sort-descending-icon v-else class="h-5" />
          </sw-button>
        </div>
      </div>

      <base-loader v-if="isSearching" :show-bg-overlay="true" />

      <div
        v-else
        class="h-full pb-32 overflow-y-scroll border-l border-gray-200 border-solid sw-scroll"
      >
        <router-link
          v-for="(estimate, index) in estimates"
          :to="`/admin/estimates/${estimate.id}/view`"
          :id="'estimate-' + estimate.id"
          :key="index"
          :class="[
            'flex justify-between side-estimate p-4 cursor-pointer hover:bg-gray-100 items-center border-l-4 border-transparent',
            {
              'bg-gray-100 border-l-4 border-primary-500 border-solid':
                hasActiveUrl(estimate.id),
            },
          ]"
          style="border-bottom: 1px solid rgba(185, 193, 209, 0.41)"
        >
          <div class="flex-2">
            <div
              class="pr-2 mb-2 text-sm not-italic font-normal leading-5 text-black capitalize truncate"
            >
              
              {{ formatName(estimate.user.name) }}
            </div>

            <div
              class="mt-1 mb-2 text-xs not-italic font-medium leading-5 text-gray-600"
            >
              {{ estimate.estimate_number }}
            </div>

            <sw-badge
              :bg-color="$utils.getBadgeStatusColor(estimate.status).bgColor"
              :color="$utils.getBadgeStatusColor(estimate.status).color"
              class="px-1 text-xs"
            >
              {{ estimate.status }}
            </sw-badge>
          </div>

          <div class="flex-1 whitespace-nowrap right">
            <div
              class="mb-2 text-xl not-italic font-semibold leading-8 text-right text-gray-900"
              v-html="
                $utils.formatMoney(estimate.total, estimate.user.currency)
              "
            />

            <div
              class="text-sm not-italic font-normal leading-5 text-right text-gray-600 est-date"
            >
              {{ estimate.formattedEstimateDate }}
            </div>
          </div>
        </router-link>

        <p
          v-if="!estimates.length"
          class="flex justify-center px-4 mt-5 text-sm text-gray-600"
        >
          {{ $t('estimates.no_matching_estimates') }}
        </p>
      </div>
    </div>

    <div
      class="flex flex-col min-h-0 mt-8 overflow-hidden sw-scroll"
      style="height: 75vh"
    >
      <iframe
        :src="`${shareableLink}`"
        class="flex-1 border border-gray-400 border-solid rounded-md frame-style"
      />
    </div>
  </base-page>
</template>

<script>
import { mapActions } from 'vuex'
import {
  DotsHorizontalIcon,
  FilterIcon,
  SortAscendingIcon,
  SortDescendingIcon,
  SearchIcon,
  LinkIcon,
  TrashIcon,
  PencilIcon,
  UsersIcon,
} from '@vue-hero-icons/solid'
const _ = require('lodash')

export default {
  components: {
    DotsHorizontalIcon,
    FilterIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    SearchIcon,
    LinkIcon,
    TrashIcon,
    PencilIcon,
    UsersIcon,
  },
  data() {
    return {
      id: null,
      count: null,
      estimates: [],
      estimate: null,
      currency: null,
      searchData: {
        orderBy: null,
        orderByField: null,
        searchText: null,
      },
      status: ['DRAFT', 'SENT', 'VIEWED', 'EXPIRED', 'ACCEPTED', 'REJECTED'],
      isMarkAsSent: false,
      isSendingEmail: false,
      isRequestOnGoing: false,
      isSearching: false,
      permissionModule: {
        create: false,
        read: false,
        update: false,
        delete: false,
        readCustomer: false,
      },
    }
  },
  computed: {
    pageTitle() {
      return this.estimate.estimate_number
    },
    getOrderBy() {
      if (
        this.searchData.orderBy === 'asc' ||
        this.searchData.orderBy == null
      ) {
        return true
      }
      return false
    },
    getOrderName() {
      if (this.getOrderBy) {
        return this.$t('general.ascending')
      }
      return this.$t('general.descending')
    },
    shareableLink() {
      return `/estimates/pdf/${this.estimate.unique_hash}`
    },
    getCurrentEstimateId() {
      if (this.estimate && this.estimate.id) {
        return this.estimate.id
      }
      return null
    },
  },
  watch: {
    $route(to, from) {
      this.loadEstimate()
    },
  },
  created() {
    this.loadEstimates()
    this.loadEstimate()
    this.onSearched = _.debounce(this.onSearched, 500)
  },
  mounted() {
    this.permissionsUserModule()
  },
  methods: {
    ...mapActions('estimate', [
      'fetchEstimates',
      'getRecord',
      'searchEstimate',
      'markAsSent',
      'sendEmail',
      'deleteEstimate',
      'selectEstimate',
      'fetchViewEstimate',
    ]),

    ...mapActions('modal', ['openModal']),
    ...mapActions('user', ['getUserModules']),

    /**
     * Formatea el nombre para mostrar hasta 21 caracteres seguidos de puntos suspensivos si es más largo.
     * @param {string} name - El nombre a formatear.
     * @return {string} El nombre formateado.
     */
    formatName(name) {
      // Verifica si el nombre es más largo de 21 caracteres
      if (name.length > 20) {
        // Retorna los primeros 21 caracteres y concatena puntos suspensivos
        return name.substring(0, 20) + '...'
      }
      // Retorna el nombre completo si es igual o menor a 21 caracteres
      return name
    },

    hasActiveUrl(id) {
      return this.$route.params.id == id
    },

    async loadEstimates() {
      let response = await this.fetchEstimates({ limit: 'all' })
      if (response.data) {
        this.estimates = response.data.estimates.data
      }
      setTimeout(() => {
        this.scrollToEstimate()
      }, 500)
    },
    scrollToEstimate() {
      const el = document.getElementById(`estimate-${this.$route.params.id}`)

      if (el) {
        el.scrollIntoView({ behavior: 'smooth' })
        el.classList.add('shake')
      }
    },
    async loadEstimate() {
      let response = await this.fetchViewEstimate(this.$route.params.id)

      if (response.data) {
        this.estimate = { ...response.data.estimate }
      }
    },
    copyPdfUrl() {
      let pdfUrl = `${window.location.origin}/estimates/pdf/${this.estimate.unique_hash}`

      let response = this.$utils.copyTextToClipboard(pdfUrl)

      window.toastr['success'](this.$tc('general.copied_pdf_url_clipboard'))
    },
    async onSearched() {
      let data = ''
      if (
        this.searchData.searchText !== '' &&
        this.searchData.searchText !== null &&
        this.searchData.searchText !== undefined
      ) {
        data += `search=${this.searchData.searchText}&`
      }

      if (
        this.searchData.orderBy !== null &&
        this.searchData.orderBy !== undefined
      ) {
        data += `orderBy=${this.searchData.orderBy}&`
      }

      if (
        this.searchData.orderByField !== null &&
        this.searchData.orderByField !== undefined
      ) {
        data += `orderByField=${this.searchData.orderByField}`
      }
      this.isSearching = true
      let response = await this.searchEstimate(data)
      this.isSearching = false
      if (response.data) {
        this.estimates = response.data.estimates.data
      }
    },
    sortData() {
      if (this.searchData.orderBy === 'asc') {
        this.searchData.orderBy = 'desc'
        this.onSearched()
        return true
      }
      this.searchData.orderBy = 'asc'
      this.onSearched()
      return true
    },
    async onMarkAsSent() {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('estimates.confirm_mark_as_sent'),
          icon: '/assets/icon/check-circle-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (value) => {
          if (value) {
            this.isMarkAsSent = true
            let response = await this.markAsSent({
              id: this.estimate.id,
              status: 'SENT',
            })
            this.isMarkAsSent = false
            if (response.data) {
              this.estimate.status = 'SENT'
              window.toastr['success'](
                this.$tc('estimates.mark_as_sent_successfully')
              )
            }
          }
        })
    },
    async onSendEstimate(id) {
      this.openModal({
        title: this.$t('estimates.send_estimate'),
        componentName: 'SendEstimateModal',
        id: this.estimate.id,
        data: this.estimate,
      })
    },
    copyPdfUrl() {
      let pdfUrl = `${window.location.origin}/estimates/pdf/${this.estimate.unique_hash}`

      let response = this.$utils.copyTextToClipboard(pdfUrl)

      window.toastr['success'](this.$tc('general.copied_pdf_url_clipboard'))
    },
    async removeEstimate(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: 'you will not be able to recover this estimate!',
          icon: '/assets/icon/trash-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (value) => {
          if (value) {
            let request = await this.deleteEstimate({ ids: [id] })
            if (request.data.success) {
              window.toastr['success'](this.$tc('estimates.deleted_message', 1))
              this.$router.push('/admin/estimates')
            } else if (request.data.error) {
              window.toastr['error'](request.data.message)
            }
          }
        })
    },

    async permissionsUserModule() {
      const data = {
        module: 'estimates',
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if (permissions.super_admin == false) {
        if (permissions.exist == false) {
          this.$router.push('/admin/dashboard')
        } else {
          const modulePermissions = permissions.permissions[0]
          if (modulePermissions == null) {
            this.$router.push('/admin/dashboard')
          } else if (modulePermissions.access == 0) {
            this.$router.push('/admin/dashboard')
          }
        }
      }

      // valida que el usuario tenga el permiso create, read, delete, update
      if (permissions.super_admin == true) {
        this.permissionModule.create = true
        this.permissionModule.update = true
        this.permissionModule.delete = true
        this.permissionModule.read = true
        this.permissionModule.readCustomer = true
      } else if (
        permissions.exist == true &&
        permissions.permissions[0] != null
      ) {
        const modulePermissions = permissions.permissions[0]
        if (modulePermissions.create == 1) {
          this.permissionModule.create = true
        }
        if (modulePermissions.update == 1) {
          this.permissionModule.update = true
        }
        if (modulePermissions.delete == 1) {
          this.permissionModule.delete = true
        }
        if (modulePermissions.read == 1) {
          this.permissionModule.read = true
        }
      }

      const dataCustomer = {
        module: 'customers',
      }
      const permissionsCustomer = await this.getUserModules(dataCustomer)

      // valida que el usuario tenga el permiso create, read, delete, update
      if (permissionsCustomer.super_admin == true) {
        this.permissionModule.readCustomer = true
      } else if (permissionsCustomer.exist == true) {
        const modulePermissions = permissionsCustomer.permissions[0]
        if (modulePermissions == null) {
          this.permissionModule.readCustomer = false
        } else if (modulePermissions.read == 1) {
          this.permissionModule.readCustomer = true
        }
      }
    },

    async sendSMSEstimate() {
      
      this.openModal({
        title: this.$t('estimates.sendsms_estimate_title'),
        componentName: 'SendEstimateSMSModal',
        id: this.estimate.id,
        data: this.estimate,
        variant: 'lg',
      })
    },

    buttonBack() {
      this.$utils.cancelFormOrBack(this, this.$router, 'back')
    },
  },
}
</script>