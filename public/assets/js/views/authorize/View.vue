<template>
  <sw-card variant="setting-card">
  <base-page>
    <sw-page-header class="mb-3" :title="$tc('authorize.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="/admin/settings/payment-gateways" :title="$tc('settings.payment_gateways.title')" />
        <sw-breadcrumb-item to="/admin/settings/authorize" :title="$tc('authorize.title', 2)" />
      </sw-breadcrumb>      
      <template slot="actions">
        <sw-button
          tag-name="router-link"
          :to="`/admin/settings/authorize/${$route.params.id}/edit`"
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('general.edit') }}
        </sw-button>
          <sw-button slot="activator" variant="primary" @click="removeAuthorize($route.params.id)">
            {{ $t('general.delete') }}
          </sw-button>
      </template>
    </sw-page-header>
    <sw-card class="flex flex-col mt-3">
      <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
        <div class="col-span-12">
          <p class="text-gray-500 uppercase sw-section-title">
            {{ $t('authorize.basic_info') }}
          </p>
          <div
            class="grid grid-cols-1 gap-4 mt-5" style="display: flex;"
          >
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('authorize.login_id') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{formData.login_id}}
              </p>
            </div>
            <div>
              <div class="flex">
                <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800 mr-1">
                  {{ $t('authorize.transaction_key') }}
                </p>
                <eye-off-icon
                  v-if="isShowTrasactionKey"
                  class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                  @click="isShowTrasactionKey = !isShowTrasactionKey"
                />
                <eye-icon
                  v-else
                  class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                  @click="isShowTrasactionKey = !isShowTrasactionKey"
                />
              </div>
              <p class="text-sm font-bold font-bold leading-5 text-black non-italic">
                {{ isShowTrasactionKey ? formData.transaction_key : '*********************' }}
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('authorize.test_mode') }}
              </p>
              <p v-if="formData.test_mode" class="text-sm font-bold font-bold leading-5 text-black non-italic">
                {{
                  $t('authorize.yes')
                }}
              </p>
              <p v-else class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  $t('authorize.no')
                }}
              </p>
            </div>
          </div>
        </div>
    </sw-card>
  </base-page>
  </sw-card>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import {
  EyeIcon,
  EyeOffIcon
} from '@vue-hero-icons/solid'
export default {
  components: {
    EyeIcon,
    EyeOffIcon
  },
  data() {
    return {    
      isShowTrasactionKey: false, 
      isRequestOnGoing: false, 
      formData: {
        login_id: '',
        transaction_key: '',
        payment_API: '',
        payment_account_validation_mode: '',
        test_mode: '',
        developer_mode: '',
        currency: '',
        status: '',
        // email: '',
        // amount: '',
        // cc_number: '',
        // expiry_month: '',
        // expiry_year: '',
        // cvv: '',
      },
    }
  },  
  created() {
    this.loadData()
  },
  methods: {
    ...mapActions('authorizations', [
      'fetchAuthorization',
      'deleteAuthorization',
    ]),

    ...mapActions('modal', ['openModal']),

    async loadData() { 
      try {
        this.isRequestOnGoing = true
        const response = await this.fetchAuthorization(this.$route.params.id)
        this.formData = { ...this.formData, ...response.data.authorize }    
      
        this.formData.status = this.formData.status.text
        this.formData.currency = this.formData.currency.text
        this.formData.payment_API = this.formData.payment_API.text
        this.formData.payment_account_validation_mode = this.formData.payment_account_validation_mode.text
      } catch (error) {
        console.log('Error', error)
        window.toastr['error'](error.response.data.response)
        return false
      }finally{
        this.isRequestOnGoing = false
      }      
    },
    async removeAuthorize(id) { 
      this.id = id      
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteAuthorization({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('tax_groups.deleted_message', 1))
            this.$router.push('/admin/settings/authorize')
          }
          return true
        }
      })
    },

    openAddPaymentFeesModal() {
      console.log( this.formData)
      this.formData.typeAction = "create"
       this.formData.typeFrom = "Authorize"

      this.openModal({
        title: this.$t('payment_fees.add_new'),
        componentName: 'CreateEditPaymentFees',
        data: this.formData,
      })
    },
  },
}
</script>
