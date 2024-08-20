<template>
  <sw-card variant="setting-card">
  <base-page>
    <sw-page-header class="mb-3" :title="$tc('paypal.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="/admin/settings/payment-gateways" :title="$tc('settings.payment_gateways.title')" />
        <sw-breadcrumb-item to="/admin/settings/paypal" :title="$tc('paypal.title', 2)" />
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
          <sw-button slot="activator" variant="primary" @click="removePaypal($route.params.id)">
            {{ $t('general.delete') }}
          </sw-button>
      </template>
    </sw-page-header>
    <sw-card class="flex flex-col mt-3">
      <div
        class="pt-6 mt-5 "
      >
        <div class="col-span-12">
          <p class="text-gray-500 uppercase sw-section-title">
            {{ $t('paypal.basic_info') }}
          </p>
          <div
            class="grid grid-cols-1 gap-4 mt-5 "
          >
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('paypal.merchant_id') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  formData.merchant_id
                }}
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('paypal.email') }}
              </p>
              <p class="text-sm font-bold font-bold leading-5 text-black non-italic">
                {{
                  formData.email
                }}
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('paypal.public_key') }}
              </p>
              <p class="text-sm font-bold font-bold leading-5 text-black non-italic">
                {{
                  formData.public_key
                }}
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('paypal.private_key') }}
                <sw-button slot="activator" v-on:click="isHidden = !isHidden">
                  {{ $t('general.show') }}
                </sw-button>
              </p>
              <p class="text-sm font-bold font-bold leading-5 text-black non-italic" v-if="isHidden">
                {{
                  formData.private_key
                }}
              </p>
              <p class="text-sm font-bold font-bold leading-5 text-black non-italic" v-if="!isHidden">
                **************
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('paypal.enviroment') }}
              </p>
              <p class="text-sm font-bold font-bold leading-5 text-black non-italic">
                {{
                  formData.enviroment
                }}
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('paypal.status') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  formData.status
                }}
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('paypal.currency') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  formData.currency
                }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </sw-card>
  </base-page>
  </sw-card>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
  data() {
    return {
      isHidden: false,
      formData: {
        paypal_id: '',
        email: '',
        paypal_secret: '',
        paypal_signature: '',
        test_mode: '',
        developer_mode: '',
        currency: '',
        status: ''
      },
    }
  },  
  created() {
    this.loadData()
  },
  methods: {
    ...mapActions('paypal', [
      'fetchPaypal',
      'deletePaypal',
    ]),

    async loadData() {      
      let response = await this.fetchPaypal(this.$route.params.id) 
      
      if (response.data) {
        this.formData = { ...this.formData, ...response.data.paypal }  
      
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
  },
}
</script>
