<template>
    <base-page>
        <sw-page-header class="mb-3" :title="$tc('providers.title')">
        <sw-breadcrumb slot="breadcrumbs">
            <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
            <sw-breadcrumb-item to="/admin/providers" :title="$tc('providers.provider', 2)" />
        </sw-breadcrumb>      
        <template slot="actions">
            <sw-button
            tag-name="router-link"
            :to="`/admin/providers/${$route.params.id}/edit`"
            class="mr-3"
            variant="primary-outline"
            >
            {{ $t('general.edit') }}
            </sw-button>
            <sw-button slot="activator" variant="primary" @click="removeProvider($route.params.id)">
                {{ $t('general.delete') }}
            </sw-button>
        </template>
        </sw-page-header>
        <sw-card class="flex flex-col mt-3">
            <div class="pt-6 mt-5 " >
                <div class="col-span-12">
                    <p class="text-gray-500 uppercase sw-section-title">
                    {{ $t('providers.basic_info') }}
                    </p>
                    <div class="grid grid-cols-1 gap-4 mt-5 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1" >
                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.titl') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic">
                              {{ formData.title }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.name') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic">
                              {{ formData.first_name }} 
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.last_name') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic">
                              {{ formData.last_name }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.email') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic">
                              {{ formData.email }}
                            </p>
                        </div>



                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.country') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-if="formData.country">
                              {{ formData.country }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-else>
                              {{ $t('providers.not_selected') }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.state') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-if="formData.state">
                              {{ formData.state }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-else>
                              {{ $t('providers.not_selected') }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.city') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-if="formData.city">
                              {{ formData.city }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-else>
                              {{ $t('providers.empty') }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.street') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-if="formData.street">
                              {{ formData.street }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-else>
                              {{ $t('providers.empty') }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.zip_code') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-if="formData.zip_code">
                              {{ formData.zip_code }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-else>
                              {{ $t('providers.empty') }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.phone') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-if="formData.phone">
                              {{ formData.phone }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-else>
                              {{ $t('providers.empty') }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.mobile') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-if="formData.mobile">
                              {{ formData.mobile }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-else>
                              {{ $t('providers.empty') }}
                            </p>
                        </div>
         
                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.webside') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-if="formData.webside">
                              {{ formData.webside }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-else>
                              {{ $t('providers.empty') }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.terms') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-if="formData.terms">
                              {{ formData.terms }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-else>
                              {{ $t('providers.empty') }}
                            </p>
                        </div>

                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.account_no') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-if="formData.account_no">
                              {{ formData.account_no }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-else>
                              {{ $t('providers.empty') }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.business_no') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-if="formData.business_no">
                              {{ formData.business_no }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-else>
                              {{ $t('providers.empty') }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.track_payments') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-if="formData.track_payments">
                              {{ $t('providers.yes') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-else>
                              {{ $t('providers.no') }}
                            </p>
                        </div>
            
                        <div>
                            <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800" >
                              {{ $t('providers.description') }}
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-if="formData.description" v-html="formData.description">
                            </p>
                            <p class="text-sm leading-5 text-black non-italic" v-else>
                              {{ $t('providers.empty') }}
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </sw-card>
    </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
  data() {
    return {
      formData: {
        title: '',
        first_name: '',
        middle_name: '',
        last_name: '',
        email: '',
        suffix: '',
        company: '',
        display_name: '',
        display_name_check: '',
        country: '',
        state: '',
        city: '',
        street: '',
        zip_code: '',
        description: '',
        phone: '',
        mobile: '',
        fax: '',
        other: '',
        webside: '',
        terms: '',
        opening_balance: '',
        as_of: '',
        account_no: '',
        business_no: '',
        track_payments: '',
        default_expense_account: '',
      },
    }
  },
  computed: {
    ...mapGetters('providers', ['selectedViewProvider']),
  },
  
  created() {
    this.loadData()
  },

  watch: {
    $route(to, from) {
      this.Provider = this.selectedViewProvider  
    },    
  },
  methods: {
    ...mapActions('providers', [
      'fetchProvider',
      'deleteProvider',
    ]),

    async loadData() {      
      let response = await this.fetchProvider(this.$route.params.id) 
      
      if (response.data) {
        this.formData = { ...this.formData, ...response.data.providers }    
      }

      console.log('loadData', this.formData);
      
    },

    async removeProvider(id) { 
      this.id = id      
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteProvider({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('providers.deleted_message', 1))
            this.$router.push('/admin/providers')
          }
          window.toastr['error'](res.data.message)
          return true
        }
      })
    },
  },
}
</script>
