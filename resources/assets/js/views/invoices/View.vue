<template>
  <base-page v-if="invoice" :class="isSuperAdmin ? 'xl:pl-96' : 'xl:pl-100'">
    <sw-page-header :title="pageTitle">
      <template v-if="!isArchived" slot="actions">
        <div class="mr-3 text-sm" v-if="permissionModule.update">
          <sw-button
            v-if="invoice.status === 'DRAFT'"
            :disabled="isMarkingAsSent"
            variant="primary-outline"
            @click="onMarkAsSent"
          >
            {{ $t('invoices.mark_as_sent') }}
          </sw-button>
        </div>

        <div class="mr-3 text-sm" v-if="permissionModule.update">
          <sw-button
            :disabled="isSendingEmail"
            variant="primary"
            class="text-sm"
            @click="onSendInvoice"
          >
            <div
              v-if="invoice.status == 'DRAFT' || invoice.status == 'SAVE_DRAFT'"
            >
              {{ $t('invoices.send_invoice') }}
            </div>

            <div
              v-if="invoice.status != 'DRAFT' && invoice.status != 'SAVE_DRAFT'"
            >
              {{ $t('invoices.resend_invoice') }}
            </div>
          </sw-button>
        </div>

        <div v-if="permissionModule.createPayment">
          <sw-button
            v-if="
              invoice.status === 'SENT' ||
              invoice.status === 'OVERDUE' ||
              invoice.status === 'VIEWED'
            "
            :to="`/admin/payments/${$route.params.id}/create`"
            tag-name="router-link"
            variant="primary"
            class="text-sm"
          >
            {{ $t('payments.record_payment') }}
          </sw-button>
        </div>

        <div class="ml-3" v-if="permissionModule.createPayment">
          <!-- /admin/payments/multiple/customer/${this.selectedCustomer.id}/invoice/${res.data.invoice.id}/create -->
          <div v-if="activate_pay_button">
            <sw-button
              v-if="
                invoice.status === 'SENT' ||
                invoice.status === 'OVERDUE' ||
                invoice.status === 'VIEWED'
              "
              :to="`/admin/payments/multiple/customer/${invoice.user_id}/invoice/${$route.params.id}/create`"
              tag-name="router-link"
              variant="primary"
              class="text-sm"
            >
              {{ $t('invoices.pay_invoice') }}
            </sw-button>
          </div>
        </div>

        <sw-dropdown class="ml-3" v-if="isSuperAdmin">
          <sw-button slot="activator" variant="primary" class="h-10">
            <dots-horizontal-icon class="h-5" />
          </sw-button>

          <sw-dropdown-item @click="copyPdfUrl">
            <link-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.copy_pdf_url') }}
          </sw-dropdown-item>

          <div v-if="permissionModule.update">
            <sw-dropdown-item
              :to="`/admin/invoices/${$route.params.id}/edit`"
              tag-name="router-link"
              v-if="!isProrate && invoice.noeditable == 0"
            >
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.edit') }}
            </sw-dropdown-item>
          </div>

          <sw-dropdown-item
            v-if="
              permissionModule.update &&
              (invoice.status == 'DRAFT' || invoice.status == 'SAVE_DRAFT')
            "
            @click="onMarkAsSent"
          >
            <check-circle-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('invoices.mark_as_sent') }}
          </sw-dropdown-item>

          <sw-dropdown-item
               v-if="invoice.status != 'DRAFT'"
                @click="sendSMSInvoice()"
              >
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.send_invoice_sms') }}
              </sw-dropdown-item>


          <sw-dropdown-item
            :to="`/admin/customers/${invoice.user_id}/view`"
            tag-name="router-link"
            v-if="permissionModule.readCustomer"
          >
            <users-icon class="h-5 mr-3 text-primary-800" />
            {{ $t('customers.customer_go') }}
          </sw-dropdown-item>

          <div v-if="permissionModule.delete && invoice.inv_avalara_bool == 0">
            <sw-dropdown-item
              @click="removeInvoice($route.params.id)"
              v-if="
                invoice.status != 'COMPLETED' &&
                invoice.paid_status === 'UNPAID'
              "
            >
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </div>

          <div v-if="permissionModule.delete">
            <div v-if="invoice.inv_avalara_bool">
              <sw-dropdown-item @click="removeInvoiceAvalara($route.params)">
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.invoice_delete_avalara') }}
              </sw-dropdown-item>
            </div>
          </div>

          <hr v-if="codeservice != null" />
          <label for="" v-if="codeservice != null">
            {{ $t('navigation.services') }}</label
          >
          <div v-if="permissionModule.readNormalServices">
            <sw-dropdown-item
              v-if="codeservice != null && invoice.customer_packages_id != null"
              :to="`/admin/customers/${invoice.user_id}/service/${invoice.customer_packages_id}/view`"
              tag-name="router-link"
            >
              <credit-card-icon class="h-5 mr-3 text-primary-800" />
              {{ $t('general.go_to') }}: {{ codeservice }}
            </sw-dropdown-item>
          </div>

          <div v-if="permissionModule.readPBXServices">
            <sw-dropdown-item
              v-if="codeservice != null && invoice.pbx_service_id != null"
              :to="`/admin/customers/${invoice.user_id}/pbx-service/${invoice.pbx_service_id}/view`"
              tag-name="router-link"
            >
              <credit-card-icon class="h-5 mr-3 text-primary-800" />
              {{ $t('general.go_to') }}: {{ codeservice }}
            </sw-dropdown-item>
          </div>

          <hr />
          <div v-if="permissionModule.readPayment">
            <label for="" v-if="payments.length != 0">
              {{ $t('navigation.payments') }}</label
            >
            <sw-dropdown-item
              v-for="(item, index) in payments"
              :key="index"
              :to="`/admin/payments/${item.id}/view`"
              tag-name="router-link"
            >
              <credit-card-icon class="h-5 mr-3 text-primary-800" />
              {{ $t('general.go_to') }}: {{ item.payment_number }}
            </sw-dropdown-item>
          </div>
        </sw-dropdown>
      </template>
      <!-- AvalaraVoidButton -->
      <template
        v-if="
          isArchived &&
          isAvalaraInvoice == 1 &&
          statusAvalaraVoid != 2 &&
          statusAvalaraVoid != 3
        "
        slot="actions"
      >
        <div class="mr-3 text-sm">
          <sw-button variant="primary" class="text-sm" @click="AvalaraVoid()">
            <div>Avalara Void</div>
          </sw-button>
        </div>
      </template>
      <!-- !!! -->
    </sw-page-header>

    <!-- sidebar -->
    <div
      v-if="isSuperAdmin"
      class="fixed top-0 left-0 hidden h-full pt-16 pb-5 ml-56 bg-white xl:ml-64 w-88 xl:block"
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
          @input="onSearch"
        >
          <search-icon slot="rightIcon" class="h-5" />
        </sw-input>

        <div class="flex mb-6 ml-3" role="group" aria-label="First group">
          <sw-dropdown
            :close-on-select="false"
            :align="left"
            position="bottom-start"
          >
            <sw-button slot="activator" size="md" variant="gray-light">
              <filter-icon class="h-5" />
            </sw-button>

            <div class="px-2 py-1 mb-2 border-b border-gray-200 border-solid">
              {{ $t('general.sort_by') }}
            </div>

            <sw-dropdown-item class="flex px-1 py-1 cursor-pointer">
              <sw-input-group class="-mt-2 text-sm font-normal">
                <sw-radio
                  id="filter_invoice_date"
                  v-model="searchData.orderByField"
                  :label="$t('invoices.invoice_date')"
                  name="filter"
                  size="sm"
                  value="invoice_date"
                  @change="onSearch"
                />
              </sw-input-group>
            </sw-dropdown-item>

            <sw-dropdown-item class="flex px-1 py-1 cursor-pointer">
              <sw-input-group class="-mt-2 font-normal">
                <sw-radio
                  id="filter_due_date"
                  :label="$t('invoices.due_date')"
                  v-model="searchData.orderByField"
                  name="filter"
                  size="sm"
                  value="due_date"
                  @change="onSearch"
                />
              </sw-input-group>
            </sw-dropdown-item>

            <sw-dropdown-item class="flex px-1 py-1 cursor-pointer">
              <sw-input-group class="-mt-2 font-normal">
                <sw-radio
                  id="filter_invoice_number"
                  v-model="searchData.orderByField"
                  :label="$t('invoices.invoice_number')"
                  size="sm"
                  type="radio"
                  name="filter"
                  value="invoice_number"
                  @change="onSearch"
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
          v-for="(invoice, index) in invoices.slice(0, 10)"
          :to="`/admin/invoices/${invoice.id}/view`"
          :id="'invoice-' + invoice.id"
          :key="index"
          :class="[
            'flex justify-between p-4 items-center cursor-pointer hover:bg-gray-100  border-l-4 border-transparent',
            {
              'bg-gray-100 border-l-4 border-primary-500 border-solid':
                hasActiveUrl(invoice.id),
            },
          ]"
          style="border-bottom: 1px solid rgba(185, 193, 209, 0.41)"
        >
          <div class="flex-2">
            <div
              class="pr-2 mb-2 text-sm not-italic font-normal leading-5 text-black capitalize truncate"
            >
              {{ formatName(invoice.user.name) }}
            </div>

            <div
              class="mt-1 mb-2 text-xs not-italic font-medium leading-5 text-gray-600"
            >
              {{ invoice.invoice_number }}
            </div>

            <sw-badge
              :bg-color="$utils.getBadgeStatusColor(invoice.status).bgColor"
              :color="$utils.getBadgeStatusColor(invoice.status).color"
              :font-size="$utils.getBadgeStatusColor(invoice.status).fontSize"
              class="px-1 text-xs"
            >
              {{ invoice.status }}
            </sw-badge>
          </div>

          <div class="flex-1 whitespace-nowrap right">
            <div
              class="mb-2 text-xl not-italic font-semibold leading-8 text-right text-gray-900"
              v-html="
                $utils.formatMoney(invoice.due_amount, invoice.user.currency)
              "
            />
            <div
              class="text-sm not-italic font-normal leading-5 text-right text-gray-600"
            >
              {{ invoice.formattedInvoiceDate }}
            </div>
          </div>
        </router-link>

        <p
          v-if="!invoices.length"
          class="flex justify-center px-4 mt-5 text-sm text-gray-600"
        >
          {{ $t('invoices.no_matching_invoices') }}
        </p>
      </div>
    </div>

    <div
      class="flex flex-col min-h-0 mt-8 overflow-hidden"
      style="height: 75vh"
    >
      <iframe
        id="iframe"
        name="iframe"
        :src="`${shareableLink}`"
        class="flex-1 border border-gray-400 border-solid rounded-md frame-style"
      />
    </div>
  </base-page>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
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
  CreditCardIcon,
  CheckCircleIcon,
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
    PencilIcon,
    TrashIcon,
    UsersIcon,
    CreditCardIcon,
    CheckCircleIcon,
  },
  data() {
    return {
      activate_pay_button: false,
      id: null,
      count: null,
      invoices: [],
      invoice: null,
      currency: null,
      payments: [],
      codeservice: null,
      searchData: {
        orderBy: null,
        orderByField: null,
        searchText: null,
      },
      isRequestOnGoing: false,
      isSearching: false,
      isSendingEmail: false,
      isMarkingAsSent: false,
      isProrate: false,
      permissionModule: {
        create: false,
        read: false,
        delete: false,
        update: false,
        readCustomer: false,
        createPayment: false,
        readPayment: false,
        readPBXServices: false,
        readNormalServices: false,
      },
      statusAvalaraVoid: 0,
      isAvalaraInvoice: 0,
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin' ? true : false
    },
    pageTitle() {
      return this.invoice.invoice_number
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
      return `/invoices/pdf/${this.invoice.unique_hash}`
    },
    getCurrentInvoiceId() {
      if (this.invoice && this.invoice.id) {
        return this.invoice.id
      }
      return null
    },
    isArchived() {
      return this.invoice.deleted_at !== null
    },
  },
  watch: {
    $route(to, from) {
      this.loadInvoice()
    },
  },
  async created() {
    this.permissionsUserModule()
    await this.loadInvoices()
    await this.loadInvoice()
    await this.autoPrintPdfPos()
    this.onSearch = _.debounce(this.onSearch, 500)
  },
  methods: {
    ...mapActions('invoice', [
      'fetchInvoices',
      'getRecord',
      'searchInvoice',
      'markAsSent',
      'sendEmail',
      'deleteInvoice',
      'selectInvoice',
      'fetchInvoice',
      'fetchInvoiceArchived',
      'AvalaraVoidFetch',
      'AvalaraVoidFetchStatus',
    ]),

    ...mapActions('modal', ['openModal']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('company', ['fetchCompanySettings']),
    ...mapActions('modules', ['getModules']),

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

    async autoPrintPdfPos() {
      // AutoPrint only for invoicesPos
      if (this.invoice.is_invoice_pos) {
        let response = await this.getModules({ name: 'corePOS' })
        if (response.success) {
          let res = await this.fetchCompanySettings(['autoprint_pdf_pos'])
          if (res.data?.autoprint_pdf_pos == '1') {
            document.getElementById('iframe').contentWindow.print() //Iframe Print
          }
        }
      }
    },

    hasActiveUrl(id) {
      return this.$route.params.id == id
    },

    async loadInvoices() {
      let response = await this.fetchInvoices({ limit: 10 })
      if (response.data) {
        this.invoices = response.data.invoices.data
      }
      setTimeout(() => {
        this.scrollToInvoice()
      }, 500)
    },
    scrollToInvoice() {
      const el = document.getElementById(`invoice-${this.$route.params.id}`)

      if (el) {
        el.scrollIntoView({ behavior: 'smooth' })
        el.classList.add('shake')
      }
    },
    async loadInvoice() {
      let response
      if (this.$route.params.deleted_at) {
        let res = await this.AvalaraVoidFetchStatus(this.$route.params.id)
        this.statusAvalaraVoid = res.data.response.status
        //
        response = await this.fetchInvoiceArchived(this.$route.params.id)
        this.isAvalaraInvoice = response.data.invoice.inv_avalara_bool
        //
      } else {
        response = await this.fetchInvoice(this.$route.params.id)
      }

      if (response.data) {
        this.invoice = response.data.invoice

        if (response.data.payments) {
          this.payments = response.data.payments
        }

        if (response.data.codeservice) {
          this.codeservice = response.data.codeservice
        }
        if (this.invoice) {
          if (this.invoice.invoiceprorate == 1) {
            this.isProrate = true
          } else {
            this.isProrate = false
          }
        }
      }
    },
    async onSearch() {
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
      let response = await this.searchInvoice(data)
      this.isSearching = false
      if (response.data) {
        this.invoices = response.data.invoices.data
      }
    },
    sortData() {
      if (this.searchData.orderBy === 'asc') {
        this.searchData.orderBy = 'desc'
        this.onSearch()
        return true
      }
      this.searchData.orderBy = 'asc'
      this.onSearch()
      return true
    },
    async onMarkAsSent() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.invoice_mark_as_sent'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          this.isMarkingAsSent = true
          let response = await this.markAsSent({
            id: this.invoice.id,
            status: 'SENT',
          })
          this.isMarkingAsSent = false
          if (response.data) {
            this.invoice.status = 'SENT'
            window.toastr['success'](
              this.$tc('invoices.marked_as_sent_message')
            )
          }
        }
      })
    },
    async onSendInvoice() {
      this.openModal({
        title: this.$t('invoices.send_invoice'),
        componentName: 'SendInvoiceModal',
        id: this.invoice.id,
        data: this.invoice,
      })
    },

    async sendSMSInvoice() {
      this.openModal({
        title: this.$t('invoices.send_invoice_sms'),
        componentName: 'SendInvoiceSMSModal',
        id: this.invoice.id,
        data: this.invoice,
        variant: 'lg',
      })
    },
    copyPdfUrl() {
      let pdfUrl = `${window.location.origin}/invoices/pdf/${this.invoice.unique_hash}`

      let response = this.$utils.copyTextToClipboard(pdfUrl)

      window.toastr['success'](this.$t('general.copied_pdf_url_clipboard'))
    },
    async removeInvoice(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: 'you will not be able to recover this invoice!',
          icon: '/assets/icon/trash-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (value) => {
          if (value) {
            let request = await this.deleteInvoice({ ids: [id] })
            if (request.data.success) {
              window.toastr['success'](this.$tc('invoices.deleted_message', 1))
              this.$router.push('/admin/invoices')
            } else if (request.data.error) {
              window.toastr['error'](request.data.message)
            }
          }
        })
    },

    async removeInvoiceAvalara(row) {
      this.id = row.id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete_avalara'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let res = await this.deleteInvoice({ ids: [row.id] })
          if (res.data.success) {
            window.toastr['success'](this.$tc('invoices.deleted_message', 1))
            let resVoidAvalara = await this.AvalaraVoidFetch({
              data: row,
              id: row.id,
            })
            if (resVoidAvalara.data.success == true) {
              window.toastr['success'](
                this.$tc('invoices.deleted_message_avalara')
              )
              this.$router.push('/admin/invoices')
            } else if (resVoidAvalara.data.success == false) {
              window.toastr['error'](
                this.$tc('invoices.something_went_wrong_avalara')
              )
              this.$router.push('/admin/invoices')
            }
            return true
          }

          if (res.data.error === 'payment_attached') {
            window.toastr['error'](
              this.$t('invoices.payment_attached_message'),
              this.$t('general.action_failed')
            )
            return true
          }

          window.toastr['error'](res.data.error)
          return true
        }
        this.resetSelectedInvoices()
      })
    },

    async permissionsUserModule() {
      const modules = ['corePOS']
      const modulesArray = await this.getModules(modules)
      var moduleCorePos = null

      if (typeof modulesArray.modules != 'undefined') {
        moduleCorePos = modulesArray.modules.find(
          (element) => element.name === 'corePOS'
        )
      }

      if (moduleCorePos && moduleCorePos.status == 'A') {
        let res = await this.fetchCompanySettings(['activate_pay_button'])
        this.activate_pay_button =
          res.data.activate_pay_button == '0' ? false : true
      } else {
        this.activate_pay_button = false
      }

      const data = {
        module: 'invoices',
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

      const dataPayments = {
        module: 'payments',
      }
      const permissionsPayments = await this.getUserModules(dataPayments)

      // valida que el usuario tenga el permiso create, read, delete, update
      if (permissionsPayments.super_admin == true) {
        this.permissionModule.readPayment = true
        this.permissionModule.createPayment = true
      } else if (permissionsPayments.exist == true) {
        const modulePermissions = permissionsPayments.permissions[0]
        if (modulePermissions == null) {
          this.permissionModule.readPayment = false
          this.permissionModule.createPayment = false
        } else if (modulePermissions.read == 1) {
          this.permissionModule.readPayment = true
          this.permissionModule.createPayment = true
        }
      }

      const dataPBXServices = {
        module: 'pbx_services',
      }
      const permissionsPBXServices = await this.getUserModules(dataPBXServices)

      // valida que el usuario tenga el permiso create, read, delete, update
      if (permissionsPBXServices.super_admin == true) {
        this.permissionModule.readPBXServices = true
      } else if (permissionsPBXServices.exist == true) {
        const modulePermissions = permissionsPBXServices.permissions[0]
        if (modulePermissions == null) {
          this.permissionModule.readPBXServices = false
        } else if (modulePermissions.read == 1) {
          this.permissionModule.readPBXServices = true
        }
      }

      const dataNormalServices = {
        module: 'services_normal',
      }
      const permissionsNormalServices = await this.getUserModules(
        dataNormalServices
      )

      // valida que el usuario tenga el permiso create, read, delete, update
      if (permissionsNormalServices.super_admin == true) {
        this.permissionModule.readNormalServices = true
      } else if (permissionsNormalServices.exist == true) {
        const modulePermissions = permissionsNormalServices.permissions[0]
        if (modulePermissions == null) {
          this.permissionModule.readNormalServices = false
        } else if (modulePermissions.read == 1) {
          this.permissionModule.readNormalServices = true
        }
      }
    },

    // Avalara Void
    async AvalaraVoid() {
      let id = this.$route.params.id

      swal({
        title: this.$t('general.are_you_sure'),
        text: 'This will remove the invoice in avalara',
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (Confirm) => {
        if (Confirm) {
          let res = await this.AvalaraVoidFetch(id)

          if (res.data.success) {
            window.toastr['success'](res.data.message)
            this.$refs.table.refresh()
            return true
          } else {
            window.toastr['error'](res.data.message)
            return true
          }
        }
      })
    },
  },
}
</script>
