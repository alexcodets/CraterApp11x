<template>
  <div class="customer-create">
    <sw-page-header :title="$t('customers.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="dashboard" />
        <sw-breadcrumb-item
          :title="$tc('customers.customer', 2)"
          to="#"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-show="totalCustomers"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>

      </template>

      
    </sw-page-header>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <sw-input-group
          :label="$t('customers.display_nametable')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.display_name"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('customers.contact_name')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <sw-input
            v-model="filters.contact_name"
            type="text"
            name="address_name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('customers.phone')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <sw-input
            v-model="filters.phone"
            type="text"
            name="phone"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('customers.status')"
          color="black-light"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <base-status-select
            ref="statusSelect"
            @select="onSelectStatus"
            @deselect="clearStatusSearch"
          />
        </sw-input-group>

        <label
          class="absolute text-sm leading-snug text-black cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
          >{{ $t('general.clear_all') }}</label
        >
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <div class="w-full flex justify-end">

        <sw-button
          size="lg"
          variant="primary-outline"
          @click="notificationsToAllCustomers()"
        >
          {{ $tc('settings.mobile.messaging.send_notification', 2) }}
          
        </sw-button>

    </div>

    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('customers.no_customers')"
      :description="$t('customers.list_of_customers')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/customers/create"
        size="lg"
        variant="primary-outline"
      >
        {{ $t('customers.add_new_customer') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="
          relative
          flex
          items-center
          justify-between
          h-10
          mt-5
          border-b-2 border-gray-200 border-solid
        "
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ messagingCustomersTo }}</b>
          {{ $t('general.of') }} <b>{{ messagingCustomersTotal }}</b>
        </p>

        <!-- <sw-transition type="fade">
          <sw-dropdown v-if="selectedCustomersMessaging.length">
            <span
              slot="activator"
              class="
                flex
                block
                text-sm
                font-medium
                cursor-pointer
                select-none
                text-primary-400
              "
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item @click="showModal()">
              <trash-icon class="h-5 mr-3 text-gray-600" /> 
              {{ $t('customers.deletecustomer') }} 
              Send Notification
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition> -->
      </div>

      <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12">
        <sw-checkbox
          v-model="selectAllFieldStatus"
          variant="primary"
          size="sm"
          class="hidden md:inline"
          @change="selectAllCustomers"
        />

        <!-- <sw-checkbox
          v-model="selectAllFieldStatus"
          :label="$t('general.select_all')"
          variant="primary"
          size="sm"
          class="md:hidden"
          @change="selectAllCustomers"
        /> -->
      </div>

      <sw-table-component
        ref="table"
        :show-filter="false"
        :data="fetchData"
        table-class="table"
      >
        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="no-click"
        >
          <div slot-scope="row" class="relative block">
            <sw-checkbox
              :id="row.id"
              v-model="selectField"
              :value="row.id"
              variant="primary"
              size="sm"
            />
          </div>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('customers.customer_number')"
          show="customcode"
        >
          <template slot-scope="row">
            <span>{{ $t('customers.customer_number') }}</span>
            <span class="font-medium text-primary-500 cursor-pointer">
              {{ row.customcode }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('customers.display_nametable')"
          show="name"
        >
          <template slot-scope="row">
            <span>{{ $t('customers.display_nametable') }}</span>
            <span class="font-medium text-primary-500 cursor-pointer">
              {{ row.name }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('customers.contact_name')"
          show="contact_name"
        >
          <template slot-scope="row">
            <span>{{ $t('customers.contact_name') }}</span>
            <span>
              {{
                row.contact_name
                  ? row.contact_name
                  : $t('customers.no_contact_name')
              }}
            </span>
          </template>
        </sw-table-column>

        <!-- <sw-table-column
          :sortable="true"
          :label="$t('customers.phone')"
          show="phone"
        >
          <template slot-scope="row">
            <span>{{ $t('customers.phone') }}</span>
            <span>
              {{ row.phone ? row.phone : $t('customers.no_contact') }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('customers.amount_due')"
          show="due_amount"
        >
          <template slot-scope="row">
            <span> {{ $t('customers.amount_due') }} </span>
            <div v-html="$utils.formatMoney(row.due_amount, row.currency)" />
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('customers.added_on')"
          sort-as="created_at"
          show="formattedCreatedAt"
        /> -->

        <sw-table-column
          :sortable="true"
          :label="$t('customers.status')"
          show="status"
        >
          <template slot-scope="row">
            <span>{{ $t('customers.status') }}</span>
            <span v-if="row.status_customer === 'A'">
              {{ $t('customers.active') }}
            </span>
            <span v-else-if="row.status_customer === 'I'">
              {{ $t('customers.inactive') }}
            </span>
            <span v-else>
              {{ $t('customers.archive') }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('customers.action')"
          show=""
        >
          <template slot-scope="row">
            <sw-button
                class="mr-3"
                variant="primary-outline"
                type="button"
                @click="showModal(row.firebase_code)"
              >
                {{ $tc('settings.mobile.messaging.send_notification', 1) }}
              </sw-button>
          </template>
        </sw-table-column>

      </sw-table-component>

      <sw-modal ref="seenNotificationModal" variant="primary">
        <template v-slot:header>
          <div
            class="absolute flex content-center justify-center w-5 cursor-pointer"
            style="top: 20px; right: 15px"
            @click="closeModal"
          >
            <x-icon />
          </div>
          <span>{{ $t('general.notification') }}</span>
        </template>
        <!-- {{ component modal }} -->
        <form @submit.prevent="createNotification">
            <div class="p-6">
              <sw-input-group
                :label="$t('general.notification_title')"
                required
                class="mb-3"
                :error="notificationTitleError"
              >
                <sw-input
                  ref="title"
                  v-model="notificationData.title"
                  type="text"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('general.notification_text')"
                required
                class="mb-3"
                :error="notificationTextError"
              >
                <sw-textarea
                  ref="title"
                  v-model="notificationData.text"
                  type="text"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('general.notification_image')"
                class="mb-3"
              >
                <sw-input
                  ref="title"
                  v-model="notificationData.image"
                  type="text"
                  placeholder="https://example.com/image.jpg"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('general.notification_name')"
              >
                <sw-input
                  ref="title"
                  v-model="notificationData.name"
                  type="text"
                />
              </sw-input-group>
            </div>
            <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
              <sw-button
                class="mr-3"
                variant="primary-outline"
                type="button"
                @click="closeModal"
              >
                {{ $t('general.cancel') }}
              </sw-button>
              <sw-button
                :loading="createNotificationLoading"
                variant="primary"
                type="submit"
                :disabled="createNotificationLoading"
              >
                  <svg mlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                  </svg>
                  Send
              </sw-button>
            </div>
        </form>
      </sw-modal>

      
    </div>
  </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { PlusSmIcon } from '@vue-hero-icons/solid'
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  TrashIcon,
  PencilIcon,
  EyeIcon,
  EyeOffIcon,
  CreditCardIcon,
} from '@vue-hero-icons/solid'
import AstronautIcon from '@/components/icon/AstronautIcon'
const { required, email, minLength } = require('vuelidate/lib/validators')

export default {
  components: {
    AstronautIcon,
    ChevronDownIcon,
    PlusSmIcon,
    FilterIcon,
    XIcon,
    TrashIcon,
    PencilIcon,
    EyeIcon,
    EyeOffIcon,
    CreditCardIcon,
  },
  data() {
    return {
      createNotificationLoading: false,
      showFilters: false,
      isRequestOngoing: true,
      messagingCustomersTotal: 0,
      messagingCustomersTo: 0,
      filters: {
        display_name: '',
        contact_name: '',
        phone: '',
        status: '',
      },
      notificationData: {
        title: '',
        text: '',
        image: '',
        name: '',
      },
      sendNotificationTo: null
    }
  },
  computed: {
    showEmptyScreen() {
      return !this.totalCustomers && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    ...mapGetters('customer', [
      'customers',
      'totalCustomers',
    ]),

    ...mapGetters('mobileSettings', [
      'selectedCustomersMessaging',
      'selectAllField',
    ]),

    notificationTitleError() {
      if (!this.$v.notificationData.title.$error) {
        return ''
      }
      if (!this.$v.notificationData.title.required) {
        return this.$tc('validation.required')
      }
    },
    notificationTextError() {
      if (!this.$v.notificationData.text.$error) {
        return ''
      }
      if (!this.$v.notificationData.text.required) {
        return this.$tc('validation.required')
      }
    },
    selectField: {
      get: function () {
        return this.selectedCustomersMessaging
      },
      set: function (val) {
        this.selectCustomer(val)
      },
    },
    selectAllFieldStatus: {
      get: function () {
        return this.selectAllField
      },
      set: function (val) {
        this.setSelectAllState(val)
      },
    },
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  destroyed() {
    if (this.selectAllField) {
      this.selectAllCustomers()
    }
  },
  methods: {
    ...mapActions('customer', [
      'deleteCustomer',
      'deleteMultipleCustomers',
      'setSelectAllState',
    ]),

    ...mapActions('mobileSettings', [
      'selectCustomer',
      'selectAllCustomers',
      'fetchMessagingCustomers',
      'fetchMobileLogs',
      'createPushNotification'
    ]),

    ...mapActions('notification', ['showNotification']),

    refreshTable() {
      this.$refs.table.refresh()
    },
    async createNotification(){
      this.$v.notificationData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('settings.mobile.messaging.confirm_notification_msg'),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result) {
          let data = {
            title: this.notificationData.title,
            text: this.notificationData.text,
            image: this.notificationData.image,
            name: this.notificationData.name,
            fcm_token: this.sendNotificationTo
          }
          
          this.isRequestOngoing = true
          this.createNotificationLoading = true
    
          try {
            let res = await this.createPushNotification(data)
            if (res.status === 200) {
              window.toastr['success']('Push sended successfully')
              this.closeModal();
              this.clearModalInputs();
            }
            this.createNotificationLoading = false
            this.isRequestOngoing = false
            return true
          
          } catch (error) {
            window.toastr['error'](error.res.data.response)
            this.createNotificationLoading = false
            
            this.isRequestOngoing = false
            return false
          }
          
        }
      })



     

    },
    closeModal(){
      this.$refs.seenNotificationModal.close()
    },
    showModal(sendTo = null){
      this.sendNotificationTo = sendTo ? sendTo : this.selectedCustomersMessaging;

      this.$refs.seenNotificationModal.open()
    },
    async fetchData({ page, filter, sort }) {
      let data = {
        display_name: this.filters.display_name,
        contact_name: this.filters.contact_name,
        phone: this.filters.phone,
        status_customer: this.filters.status ? this.filters.status.value : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }


      this.isRequestOngoing = true
      let response = await this.fetchMessagingCustomers(data)
      this.isRequestOngoing = false
      this.messagingCustomersTotal = response.data.mobileMessagingCustomers.data.length;
      this.messagingCustomersTo = response.data.mobileMessagingCustomers.to;

      return {
        data: response.data.mobileMessagingCustomers.data,
        pagination: {
          totalPages: response.data.mobileMessagingCustomers.last_page,
          currentPage: page,
        },
      }
    },
    setFilters() {
      this.refreshTable()
    },
    clearModalInputs(){
      this.notificationData = {
        title: '',
        text: '',
        image: '',
        name: '',
      }
    },
    clearFilter() {
      this.filters = {
        display_name: '',
        contact_name: '',
        phone: '',
        status: '',
      }
      if (this.filters.status) {
        this.$refs.statusSelect.$refs.baseSelect.removeElement(
          this.filters.status
        )
      }
    },
    notificationsToAllCustomers(){
      // validar si esta seleccionado uno o mas customers
      if (this.selectedCustomersMessaging.length > 0 ){
        this.showModal();
      } else {
        window.toastr['error'](this.$tc('settings.mobile.messaging.error_not_customer_selected'));
      }
      // console.log("🚀 ~ file: Messaging.vue ~ line 621 ~ notificationsToAllCustomers ~ selectedCustomersMessaging", this.selectedCustomersMessaging)

      // mstrar modal
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },
    onSelectStatus(status) {
      this.filters.status = status
    },
    async clearStatusSearch(removedOption, id) {
      this.filters.status = ''
      this.refreshTable()
    },
    async removeCustomer(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customers.confirm_delete'),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result) {
          let res = await this.deleteCustomer({ ids: [id] })

          if (res.data.type === 'success') {
            window.toastr['success'](this.$tc('customers.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }

          window.toastr[res.data.type](res.data.message)
          return true
        }
      })
    },

  },
  validations: {
    notificationData: {
      title: {
        required
      },
      text: {
        required
      },
    },
 /*    loginData: {
      email: {
        required,
        // email,
      },
      password: {
        required,
        minLength: minLength(8),
      },
    }, */
  },
}
</script>
