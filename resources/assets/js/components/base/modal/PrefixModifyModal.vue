<template>
    <div class="custom-destination">
      <form action="" @submit.prevent="submitData">
        <div class="px-8 py-8 sm:p-6">         
         
          <div class="mb-2">
                <p class="p-0 mb-0 text-base leading-snug text-black">
                  {{ $t('general.add_group') }}
                </p>
                <sw-switch
                    v-model="add_or_delete_bool"
                />
                <sw-input-group 
                    v-if="add_or_delete_bool"
                    :error="statusError"
                    :label="$t('invoices.status')"
                    class="mt-2">
                    <sw-select                  
                      v-model="formData.status"
                      :invalid="$v.formData.status.$error"
                      :options="status"
                      :group-select="false"
                      :searchable="true"
                      :show-labels="false"
                      :placeholder="'Choose an option'"
                      :allow-empty="false"
                      class="mt-2"
                      group-values="options"
                      group-label="label"
                      track-by="name"
                      label="name"
                    />
                </sw-input-group>

                <sw-input-group 
                  v-if="add_or_delete_bool"
                  :error="prefixRateGroupsIdError"
                  :label="'Custom Destination Groups'"
                  class="mt-2" >
                      <sw-select                  
                        v-model="formData.prefixrate_groups"
                        :invalid="$v.formData.prefixrate_groups.$error"
                        :options="prefixrate_groups"
                        :searchable="true"
                        :show-labels="false"
                        :allow-empty="true"
                        :placeholder="'Select Group'"
                        :multiple="true"
                        class="mt-2"
                        label="name"
                        track-by="id"                    
                      />
                </sw-input-group>
          </div>

          <br v-if="!country_bool">

          <div>
                <p class="p-0 mb-0 mt text-base leading-snug text-black">
                  {{ $t('corePbx.prefix_groups.category') }}
                </p>
                <sw-switch              
                  v-model="category_bool"
                />
                <sw-input-group
                  v-if="category_bool"            
                  class="mb-4 mt-6"            
                  :error="categoryError"
                  required
                >
                  <sw-select
                    :invalid="$v.formData.category.$error"
                    v-model="formData.category"
                    :options="categories"
                    :searchable="true"
                    :show-labels="false"
                    :placeholder="$t('corePbx.prefix_groups.select_a_category')"
                    label="name"
                    @input="$v.formData.category.$touch()"
                  />              
                </sw-input-group> 
          </div>

          <br v-if="!category_bool">

          <div class="mb-2">
                <p class="p-0 mb-0 text-base leading-snug text-black">
                  {{ $tc('settings.company_info.country') }}
                </p>
                <sw-switch              
                  v-model="country_bool"              
                />           
                <sw-input-group
                  v-if="country_bool"              
                  class="mb-4 mt-6" 
                  :error="countryIdError"
                >
                  <sw-select
                    :invalid="$v.formData.country_id.$error"
                    v-model="formData.country_id"
                    :options="countries"
                    :searchable="true"
                    :show-labels="false"
                    :max-height="200"
                    :placeholder="'Select a country'"
                    label="name"
                    track-by="id"
                    @input="$v.formData.country_id.$touch()"
                  />
                </sw-input-group>
          </div>

          <br v-if="!rate_bool">

          <div class="mb-2">
                <p class="p-0 mb-0 text-base leading-snug text-black">
                  {{ $tc('corePbx.internacional.rate_per_minutes') }}
                </p>
                <sw-switch
                    v-model="rate_bool"
                />
                <sw-input-group
                    v-if="rate_bool"                
                    class="mb-4 mt-6"                
                    :error="rateError"
                    required
                >
                  <sw-money
                    v-model="formData.rate_per_minutes"
                    :currency="defaultCurrency"
                    :invalid="$v.formData.rate_per_minutes.$error"
                    name="rate_per_minutes_selected"
                    @input="$v.formData.rate_per_minutes.$touch()"
                  />
                </sw-input-group>
          </div>

          <br v-if="!order_bool">

          <div>
                <p class="p-0 mb-0 text-base leading-snug text-black">
                  {{ $t('general.order') }}
                </p>
                <sw-switch
                    v-model="order_bool"
                />
                <sw-input-group
                    v-if="order_bool"                
                    class="mb-4 mt-6"  
                    required
                    :error="orderError"
                >
                  <sw-input
                    v-model="formData.order"
                    :invalid="$v.formData.order.$error"
                    type="number"
                    @input="$v.formData.order.$touch()"
                  />
                </sw-input-group>
          </div>
            
          </div>
    
          <div
            class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
          >
            <sw-button
              class="mr-3"
              variant="primary-outline"
              type="button"
              @click="closeItemModal"
            >
              {{ $t('general.cancel') }}
            </sw-button>
            <sw-button
              :loading="isLoading"
              :disabled="isLoading"
              variant="primary"
              type="submit"
            >
              <save-icon v-if="!isLoading" class="mr-2" />
              {{ $t('general.save') }}
            </sw-button>
          </div>

      </form>
    </div>
  </template>
  
  <script>
  import { mapActions, mapGetters } from 'vuex'
  import { ShoppingCartIcon } from '@vue-hero-icons/solid'
  
  const {
    required,
    minLength,
    numeric,
    maxLength,
    minValue,
  } = require('vuelidate/lib/validators')
  
  export default {
    components: {
      ShoppingCartIcon,
    },
    data() {
      return {
        country_bool: false,
        category_bool: false,
        rate_bool: false,        
        order_bool: false,
        add_or_delete_bool: false,
        //
        isLoading: false,
        typecustomOptions: [
          {
            label: 'Prefix',
            value: 'P',
          },
          {
            label: 'From / To',
            value: 'FT',
          },
        ],
        formData: {          
          country_id: null,
          category: null,
          rate_per_minutes: 0,
          status: null,
          IsOrder: false,
          order: 0,
          prefixrate_groups: null
        },
        categories: [
          { name: 'Toll Free', value: 'T' },
          { name: 'International', value: 'I' },
          { name: 'Custom', value: 'C' },
        ],
        prefixrate_groups: [],
        status: [
        {
          label: 'Options',
          isDisable: true,
          options: [
            { name: 'Add', value: 'A' },
            { name: 'Delete', value: 'D' },
          ],
        },       
      ],
      }
    },
    validations: {
      formData: {        
        category: {
          required,
        },       
        country_id: {
          required,
        },
        rate_per_minutes: {
          required,
          minValue: minValue(0.00001),
          maxLength: maxLength(20),
        },
        order:{
          required,
          numeric
        },
        status: {
          required,
        },
        prefixrate_groups: {
          required,
        },
      },
    },
    computed: {
      ...mapGetters('modal', [
        'modalDataID',
        'modalData',
        'modalActive',
        'refreshData',
      ]),
      ...mapGetters(['countries']),
      ...mapGetters('company', ['defaultCurrencyForInput']),
  
      defaultCurrency() {
        return {
          ...this.defaultCurrencyForInput,
          precision: 5,
        }
      },
   
      countryIdError() {
        if (!this.$v.formData.country_id.$error) {
          return ''
        }
        if (!this.$v.formData.country_id.required) {
          return this.$t('validation.required')
        }
      }, 

      categoryError() {
        if (!this.$v.formData.category.$error) {
          return ''
        }
        if (!this.$v.formData.category.required) {
          return this.$t('validation.required')
        }
      }, 
      rateError() {
        if (!this.$v.formData.rate_per_minutes.$error) {
          return ''
        }
  
        if (!this.$v.formData.rate_per_minutes.required) {
          return this.$tc('validation.required')
        }
  
        if (!this.$v.formData.rate_per_minutes.maxLength) {
          return this.$t('validation.rate_maxlength')
        }
  
        if (!this.$v.formData.rate_per_minutes.minValue) {
          return this.$t('validation.rate_minvalue')
        }
      },

      orderError() {
        if (!this.$v.formData.order.$error) {
          return ''
        }
        if (!this.$v.formData.order.required) {
          return this.$t('validation.required')
        }
        if (!this.$v.formData.order.numeric) {
          return 'Only numbers integers'
        }        
      }, 

      statusError() {
        if (!this.$v.formData.status.$error) {
          return ''
        }
        if (!this.$v.formData.status.required) {
          return this.$t('validation.required')
        }
      }, 
      
      prefixRateGroupsIdError() {
        if (!this.$v.formData.prefixrate_groups.$error) {
          return ''
        }
        if (!this.$v.formData.prefixrate_groups.required) {
          return this.$t('validation.required')
        }
      }, 

    },
    async mounted() {
      let res = await this.CargarCustomDestination();
      this.prefixrate_groups= [...res.data.internacional];

      this.$v.formData.$reset() 
    },
    methods: {
      ...mapActions('modal', ['closeModal', 'resetModalData']),
      ...mapActions('internacionalrate', ['addInternacional', 'modifySelected', 'modifyAll', 'CargarCustomDestination']),     

      resetFormData() {
        this.formData = {
          country_id: null,         
          category: null,
          rate_per_minutes: 0,
        }
        this.$v.$reset()
      },
  
      closeItemModal() {
        this.resetFormData()
        this.closeModal()
        this.resetModalData()
      },
  
      async submitData() {

        this.$v.formData.$touch()
        // validations
        if(this.country_bool && this.formData.country_id == null) return 

        if(this.category_bool && this.formData.category == null) return 

        if(this.rate_bool && this.formData.rate_per_minutes == 0) return 

        if(this.order_bool && this.formData.order < 0) return 

        if(this.add_or_delete_bool && this.formData.status == null) return

        if(this.add_or_delete_bool && this.formData.prefixrate_groups == null) return

        if(!this.country_bool && !this.category_bool && !this.rate_bool && !this.add_or_delete_bool 
           && !this.order_bool
          )
        {          
            window.toastr['error']('Select any option')
            return
        }
        //

        this.formData.current_group_id = this.modalData

        this.formData.country_id = this.formData.country_id
                                      ? this.formData.country_id.id
                                      : null 
                                    
        this.formData.category = this.formData.category
                                    ? this.formData.category.value
                                    : null   
                                    
        this.formData.status = this.formData.status
                                    ? this.formData.status.value
                                    : null 

        if(!this.order_bool)
        {
          this.formData.IsOrder = false
        }else{
          this.formData.IsOrder = true
          this.formData.order = parseInt(this.formData.order)
        }        
        // Alert
        swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t(
          'Any changes can affect the billing and collection processes.'
        ),
        icon: '/assets/icon/alert-svgrepo-com.svg',
        buttons: true,
        dangerMode: true,
        }).then(async (value) => {
          if (value) {          
            try {

            this.isLoading = true
            let response
            response = await this.modifySelected(this.formData)
            // if(this.modalData.type == "Selected"){
            //   response = await this.modifySelected(this.formData)
            // }

            // if(this.modalData.type == "All"){
            //   response = await this.modifyAll(this.formData)
            // }                        
            if (response.data.success)
            {
              window.toastr['success'](response.data.message)             
              this.isLoading = false
              this.refreshData ? this.refreshData() : ''
              this.closeItemModal()
              return true
            }

            } catch (error) {
              this.isLoading = false
            }
            
          }
          if(!value){
              this.resetModalData()
              this.resetFormData()
              this.closeModal()
              return true
          }          
        })
      },
    },
  }
  </script>