<template>
  <base-page>
    <sw-page-header class="mb-3" :title="$tc('paypal.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          to="/admin/settings/payment-gateways"
          :title="$tc('settings.payment_gateways.title')"
        />
        <sw-breadcrumb-item
          to="/admin/settings/paypal"
          :title="$tc('paypal.title', 2)"
        />
      </sw-breadcrumb>
      <template slot="actions">
        <sw-button
          tag-name="router-link"
          :to="`/admin/settings/paypal/${$route.params.id}/edit`"
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('general.edit') }}
        </sw-button>
        <sw-button
          slot="activator"
          variant="primary"
          @click="removePaypal($route.params.id)"
        >
          {{ $t('general.delete') }}
        </sw-button>
      </template>
    </sw-page-header>

    <div class="w-full">
      <div class="col-span-12">
        <sw-card>
          <p class="text-gray-500 uppercase sw-section-title">
            {{ $t('authorize.basic_info') }}
          </p>
          <div class="flex flex-wrap mt-5 md:mt-7">
            <div class="w-full md:w-1/3">
              <p class="font-bold">{{ $t('paypal.merchant_id') }}</p>
              <p class="text-gray-700 text-sm">{{ formData.merchant_id }}</p>
            </div>

            <div class="w-full md:w-1/3">
              <p class="font-bold">{{ $t('paypal.email') }}</p>
              <p class="text-gray-700 text-sm">{{ formData.email }}</p>
            </div>

          
            <div class="w-full md:w-1/3">
              <p class="font-bold">{{ $t('paypal.enviroment') }}</p>
              <p class="text-gray-700 text-sm">{{ formData.enviroment }}</p>
            </div>

            <div class="w-full md:w-1/3">
              <p class="font-bold">{{ $t('paypal.status') }}</p>
              <p class="text-gray-700 text-sm">{{ formData.status }}</p>
            </div>
            <div class="w-full md:w-1/3">
              <p class="font-bold">{{ $t('paypal.currency') }}</p>
              <p class="text-gray-700 text-sm">{{ formData.currency }}</p>
            </div>
          </div>
        </sw-card>
      </div>
    </div>

    <sw-page-header class="mt-5" title=" ">
      <template slot="actions">
        <div class="w-full">
          <div class="col-span-12">
            <sw-button
              slot="activator"
              variant="primary"
              @click="openPaymentFee()"
            >
              {{ $t('general.add') }}
            </sw-button>
          </div>
        </div>
      </template>
    </sw-page-header>
    <div class="w-full mt-5">
      <div class="col-span-12">
        <sw-card>
          <div class="relative table-container">
            <div
              class="relative flex items-center justify-between h-10 list-none border-b-2 border-gray-200 border-solid"
            >
              <p class="text-sm"></p>
            </div>

            <sw-table-component
              ref="notes_table"
              :show-filter="false"
              table-class="table"
              :data="fetchPaymentFeesData"
            >
              <sw-table-column
                :sortable="true"
                :label="$t('payment_fees.name')"
                show="name"
              >
                <template slot-scope="row">
                  <span>{{ $t('payment_fees.name') }}</span>
                  {{ row.name }}
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :label="$t('payment_fees.type')"
                show="type"
              >
                <template slot-scope="row">
                  <span>{{ $t('payment_fees.type') }}</span>
                  {{ row.type }}
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :label="$t('authorize.amount')"
                show="amount"
              >
                <template slot-scope="row">
                  <span>{{ $t('authorize.amount') }}</span>

                  <div
                    v-html="$utils.formatMoney(row.amount, defaultCurrency)"
                  />
                </template>
              </sw-table-column>

              <sw-table-column cell-class="action-dropdown no-click">
                <template slot-scope="row">
                  <span>{{ $t('general.actions') }}</span>

                  <sw-dropdown>
                    <dot-icon slot="activator" />

                    <sw-dropdown-item @click="openPaymentFeeEdit(row)">
                      <pencil-icon class="h-5 mr-3 text-gray-600" />
                      {{ $t('general.edit') }}
                    </sw-dropdown-item>
                    <!-- seccion para cambiar el precio -->
                    <sw-dropdown-item @click="deleteNote(row.id)">
                      <trash-icon class="h-5 mr-3 text-gray-600" />
                      {{ $t('general.delete') }}
                    </sw-dropdown-item>
                  </sw-dropdown>
                </template>
              </sw-table-column>
            </sw-table-component>
          </div>
        </sw-card>
      </div>
    </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import {
  EyeIcon,
  EyeOffIcon,
  PencilIcon,
  TrashIcon,
  RefreshIcon,
  FilterIcon,
  XIcon,
  HashtagIcon,
} from '@vue-hero-icons/solid'
export default {
  components: {
    EyeIcon,
    EyeOffIcon,
    PencilIcon,
    TrashIcon,
    RefreshIcon,
    FilterIcon,
    XIcon,
    HashtagIcon,
  },
  data() {
    return {
      isHidden: false,
      inModaldata: { from: 'Paypal', id: null, action: 'create' },
      formData: {
        paypal_id: '',
        email: '',
        paypal_secret: '',
        paypal_signature: '',
        test_mode: '',
        developer_mode: '',
        currency: '',
        status: '',
      },
    }
  },
  computed: {
    ...mapGetters('company', ['defaultCurrency']),
  },
  created() {
    this.loadData()
  },
  methods: {
    ...mapActions('paypal', ['fetchPaypal', 'deletePaypal']),
    ...mapActions('modal', ['openModal']),

    ...mapActions('paymentFee', [
      'addPaymentFee',
      'fetchViewPaymentFee',
      'updatePaymentFee',
      'fetchPaymentFees',
      'deletePaymentFee',
    ]),

    async loadData() {
      let response = await this.fetchPaypal(this.$route.params.id)

      if (response.data) {
        this.formData = { ...this.formData, ...response.data.paypal }
        //console.log(this.formData)
        this.formData.status = this.formData.status.text
        this.formData.currency = this.formData.currency.text
      }
    },

    async removePaypal(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deletePaypal({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('paypal.deleted_message', 1))
            this.$router.push('/admin/settings/paypal')
          }
          return true
        }
      })
    },
    openPaymentFee() {
      this.inModaldata.id = this.$route.params.id
      this.inModaldata.row = null
      this.inModaldata.action = 'create'
      this.openModal({
        title: this.$t('authorize.payment_feestitle'),
        componentName: 'CreateEditPaymentFees',
        data: this.inModaldata,
      })
    },

    openPaymentFeeEdit(row) {
      this.inModaldata.id = this.$route.params.id
      this.inModaldata.row = row
      this.inModaldata.action = 'edit'
      this.openModal({
        title: this.$t('authorize.payment_feestitle'),
        componentName: 'CreateEditPaymentFees',
        data: this.inModaldata,
      })
    },

    async fetchPaymentFeesData({ page, filter, sort }) {
      const params = {
        paypal_settings_id: this.$route.params.id,
        payment_gateway: 'Paypal',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let res = await this.fetchPaymentFees(params)
      this.isRequestOngoing = false

      return {
        data: res.data.response.PaymentGatewaysFee.data,
        pagination: {
          totalPages: res.data.response.PaymentGatewaysFee.last_page,
          currentPage: page,
        },
      }
    },

    refreshTable() {
      this.$refs.notes_table.refresh()
    },

    async deleteNote(id) {
      let response = await this.deletePaymentFee({ id })

     // console.log(response)

      if (!response || !response.data || response.data.success !== true) {
        window.toastr['error']('Operation error')

        return false
      } else {
        window.toastr['success'](this.$tc('pbx_services.item_delete_success'))

        this.refreshTable()
      }
    },
  },
}
</script>
