<template>
     <sw-card>
      <form action="" @submit.prevent="submitPrefix">
      <div
        class="
          grid
          gap-2
          grid-cols-1
          md:grid-cols-2
          xl:grid-cols-3
          border border-grey-700
          rounded-lg
          mb-5
          p-3
        "
      >
        <sw-input-group :label="$tc('settings.company_info.country')">               

          <sw-select            
            v-model="formData.country"
            :options="countries"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :placeholder="$t('general.select_country')"
            label="name"
            track-by="id"
            :disabled="isCountryActive"   
          />

        </sw-input-group>

        <!-- STATUS required class="md:col-span-3 ml-2"-->
        <sw-input-group
          :label="$t('packages.status')"
          :error="statusError"
          required
        >
          <sw-select
            v-model="formData.status"
            :options="status"
            :searchable="true"
            :show-labels="false"
            :tabindex="16"
            :allow-empty="false"
            :placeholder="$t('general.select_status')"
            label="text"
            track-by="value"
          />
        </sw-input-group>

        <!-- CATEGORYS -->
        <sw-input-group
          :label="$t('expenses.category')"
          :error="categoryError"
          required
        >
          <sw-select
            v-model="formData.category"
            :options="category"
            :invalid="$v.formData.category.$error"
            :searchable="true"
            :show-labels="false"
            :tabindex="16"
            :allow-empty="true"
            :placeholder="$t('general.select_category')"
            label="text"
            track-by="value"
            @input="$v.formData.category.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('didFree.item.custom_destination_group')"          
          required
        >          
          <sw-input
            v-model="getDestinationGroups"     
            :disabled="isEditPrefixGroup"          
          />
        </sw-input-group>
      </div>
      <div
        v-for="(prefix, index) in formData.multiple"
        :key="index"
        class="
          grid
          gap-2
          grid-cols-1
          md:grid-cols-2
          xl:grid-cols-3
          border border-grey-700
          rounded-lg
          mt-5
          p-3
        "
      >
        <sw-input-group :label="$t('corePbx.internacional.prefix_type')">
          <sw-select
            v-model="prefix.typecustom"
            :options="typecustomOptions"
            :searchable="true"
            :show-labels="true"
            :allow-empty="false"
            :placeholder="$t('corePbx.internacional.prefix_type')"
            label="label"
            track-by="value"
          />
        </sw-input-group>
        <!--PREFIJO required class="md:col-span-3"-->
        <sw-input-group
          v-if="prefix.typecustom.value == 'P'"
          :label="$t('didFree.item.prefijo')"
          required
          :error="prefixValidate($v.formData.multiple.$each[index].prefijo)"
        >
          <sw-input
            v-model="prefix.prefijo"
            :placeholder="$t('didFree.item.prefijo')"
            focus
            type="text"
            name="prefijo"
            pattern="[0-9*|A-Za-z *|#|+]+"
            title="Numbers, letters, blank space and  special characters (* # +)"
            tabindex="1"
            placer
            :invalid="$v.formData.multiple.$each[index].prefijo.$error"
          />
        </sw-input-group>

        <!-- FROM -->
        <sw-input-group
          v-if="prefix.typecustom.value == 'FT'"
          :label="$t('corePbx.internacional.from')"
          required
          :error="fromValidate($v.formData.multiple.$each[index].from)"
        >
          <sw-input
            v-model="prefix.from"
            :placeholder="$t('corePbx.internacional.from')"
            focus
            type="text"
            name="from"
            pattern="[0-9*|A-Za-z *|#|+]+"
            title="Numbers, letters, blank space and  special characters (* # +)"
            tabindex="1"
            placer
            :invalid="$v.formData.multiple.$each[index].from.$error"
          />
        </sw-input-group>
        <!-- TO -->
        <sw-input-group
          v-if="prefix.typecustom.value == 'FT'"
          :label="$t('corePbx.internacional.to')"
        >
          <sw-input
            v-model="prefix.to"
            :placeholder="$t('corePbx.internacional.to')"
            focus
            type="text"
            name="to"
            pattern="[0-9*|A-Za-z *|#|+]+"
            title="Numbers, letters, blank space and  special characters (* # +)"
            tabindex="1"
            placer
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('didFree.item.name')"
          required
          :error="nameValidate($v.formData.multiple.$each[index].name)"
        >
          <sw-input
            v-model="prefix.name"
            :placeholder="$t('didFree.item.name')"
            focus
            type="text"
            name="name"
            tabindex="1"
            placer
            :invalid="$v.formData.multiple.$each[index].name.$error"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('corePbx.packages.rate_per_minutes')"
          required
        >
          <sw-money
            v-model="prefix.rate_per_minutes"
            :currency="defaultCurrency"
            name="rate_per_minutes_selected"
            :invalid="$v.formData.multiple.$each[index].rate_per_minutes.$error"
          />
        </sw-input-group>

        <div
          class="md:col-span-2 xl:col-span-3 flex justify-end "         
        >
            <sw-button :loading="isLoading" variant="primary" type="submit">
            <save-icon class="mr-2" v-if="!isLoading" />
                {{ !isEditPrefix ? $t('general.save') : $t('general.update') }}
        </sw-button>

        </div>
      </div>
    </form>
    </sw-card>
</template>
  
  <script>
  import { mapActions, mapGetters } from 'vuex'
  const {
    required,
    minLength,
    maxLength,
    minValue,
    helpers,
  } = require('vuelidate/lib/validators')  
  
  export default {
    data() {
      return {
        isCountryActive: false,
        isEditPrefixGroup: false,
        isEditPrefix: false,
        showSelect: false,
        isRequestOnGoing: false,
        prefixrate_groups: [],
        isLoading: false,
        addMode: 'single',
        status: [
            { value: 'A', text: 'Active' },
            { value: 'I', text: 'Inactive' },
        ],
        category: [
            { value: 'C', text: 'Custom' },
            { value: 'I', text: 'International' },
            { value: 'T', text: 'Toll Free' },
        ],
        rate_per_minutes_selected: 0,
        country: null,
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
            id: '',
            multiple: [
            {
                typecustom: {
                label: 'Prefix',
                value: 'P',
                },
                from: '',
                to: '',
                prefijo: '',
                name: '',
                rate_per_minutes: 0,
            },
            ],
            prefixrate_groups_id: [],
            country: null,
            country_id: null,
            status: { value: 'A', text: 'Active' },
            category: '',                     
        },
        countries: [],
        states: [], 
    }
    },
    computed: {      
      ...mapGetters('modal', [
        'modalDataID',
        'modalData',
        'modalActive',
        'refreshData',
      ]),
      ...mapGetters('category', ['categories']),

      defaultCurrency() {
      return {
        ...this.defaultCurrencyForInput,
        precision: 5,
      }
    },
      
    statusError() {
      
      if (!this.$v.formData.status.required) {
        return this.$tc('validation.required')
      }
    },
    categoryError() {
      if (!this.$v.formData.category.$error) {
        return ''
      }
      if (!this.$v.formData.category.required) {
        return this.$tc('validation.required')
      }
    },

    getDestinationGroups() {
        if(this.prefixrate_groups.length > 0 )
        {
            let custom_destination_group = this.prefixrate_groups.filter(
            group => group.id == this.$route.params.id        
            )

            this.formData.prefixrate_groups_id = custom_destination_group.map(
                (itemGroup) => {
                    return {
                    ...itemGroup,
                    id: itemGroup.id,
                    name: itemGroup.name,
                    }
                }
            )      
        return custom_destination_group[0].name
      }     
    },

      nameError() {
        if (!this.$v.formData.name.$error) {
          return ''
        }
        if (!this.$v.formData.name.required) {
          return this.$tc('validation.required')
        }
      },
  
      accountAcceptedError() {
        if (!this.$v.formData.account_accepted.$error) {
          return ''
        }
        if (!this.$v.formData.account_accepted.required) {
          return this.$tc('validation.required')
        }
      },
  
      expenseError() {
        if (!this.$v.formData.generate_expense_id.$error) {
          return ''
        }
        if (!this.$v.formData.generate_expense_id.required) {
          return this.$tc('validation.required')
        }
      },
  
      voidRefundExpenseError() {
        if (!this.$v.formData.void_refund_expense_id.$error) {
          return ''
        }
        if (!this.$v.formData.void_refund_expense_id.required) {
          return this.$tc('validation.required')
        }
      },
  
      getExpenseCategories() {
        return this.categories.map((category) => {
          return {
            ...category,
          }
        })
      },
    },
    validations: {
    formData: {
      multiple: {
        $each: {
          typecustom: {
            required,
          },
          from: {
            required,
          },
          prefijo: {
            required,
            minLength: minLength(1),
            maxLength: maxLength(32),
          },
          name: {
            required,
          },
          rate_per_minutes: {
            required,
          },
        },
      },      
      status: {
        required,
      },
      category: {
        required,
      },
    },
  },
    async mounted() {
        this.isEditPrefixGroup = true
        await this.loadPrefixGroups()     
        if (this.modalDataID) {           
            this.isEditPrefix = true
            await this.setData()
        }
    },
    
    methods: {
      ...mapActions('modal', ['closeModal', 'resetModalData']),
      ...mapActions('payment', ['addPaymentMode', 'updatePaymentMode']),
      ...mapActions('category', ['fetchCategories']),
      ...mapActions('internacionalrate', [
      'fetchInternacional',
      'updatePrefixInternational',
      'addInternacional',
      'CargarCustomDestination',
    ]),

      async loadPrefixGroups() {      

      let res_countries = await window.axios.get('/api/v1/countries')
      
      if (res_countries) {        
        this.countries = res_countries.data.countries       
      }
      
      let res = await this.CargarCustomDestination()      
      this.prefixrate_groups = [...res.data.internacional]     
    },

      resetFormData() {        
        this.formData = {
            id: '',
            multiple: [
            {
                typecustom: {
                label: 'Prefix',
                value: 'P',
                },
                from: '',
                to: '',
                prefijo: '',
                name: '',
                rate_per_minutes: 0,
            },
            ],
            prefixrate_groups_id: [],
            country_id: null,
            status: { value: 'A', text: 'Active' },
            category: '',
        }
        this.$v.formData.$reset();
      },
  
      async submitPrefix() {            
        
        this.$v.formData.$touch()
        const validateForm = this.$v.formData

        if (     
            !validateForm.status.required ||
            !validateForm.category.required
        ) return false

        const searhInvalid = this.formData.multiple.findIndex((item) => {
        if (item.name == '' || item.name == null) return true
            if (item.typecustom == 'P') {
                if (item.prefijo == '' || item.prefijo == null) return true
            } else if (item.typecustom == 'FT') {
                if (item.from == '' || item.from == null) return true
            }
        })

        if (searhInvalid != -1) return false
        
        if (this.formData.country) {          
          // save
          if(!this.isEditPrefix){           
            this.formData.country_id = this.formData.country.id               
          }
          // update (con pais previo en "none")
          if(this.isEditPrefix && !this.isCountryActive){           
            this.formData.country_id = this.formData.country.id            
          }
          // update (con pais previo existente)        
          if(this.isEditPrefix && this.isCountryActive){          
            this.formData.country_id = this.formData.country[0].id          
          }  
        }
        
        this.formData.status = this.formData.status.value
        this.formData.category = this.formData.category.value

        this.formData.multiple.map((item) => {
        item.typecustom = item.typecustom.value
        })

        if (this.isEditPrefix) {  
            let res = await this.updatePrefixInternational(this.formData)       
            window.toastr['success'](res.data.message)            
            this.refreshData ? this.refreshData() : ''
            this.closePaymentModeModal()
            return true              
        }else{
            try {        
                let res = await this.addInternacional(this.formData)          
                this.isLoading = false
                window.toastr['success']('Prefix save successfully')
                this.refreshData ? this.refreshData() : ''
                this.closePaymentModeModal()
                return true
            } catch (error) {
                window.toastr['error']('error')
            }  
        }   
        
      },

      closePaymentModeModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },  

    prefixValidate(value) {
      if (!value.$error) {
        return ''
      }
      if (!value.required) {
        return this.$tc('validation.required')
      }
    },

    fromValidate(value) {
      if (!value.$error) {
        return ''
      }
      if (!value.required) {
        return this.$tc('validation.required')
      }
    },

    nameValidate(value) {
      if (!value.$error) {
        return ''
      }
      if (!value.required) {
        return this.$tc('validation.required')
      }
    },
   
    async setData() {

        // load category
        let category = ''
            switch (this.modalData.category)
            {
                case 'C':
                    category = 'Custom'
                break
                case 'I':
                    category = 'International'
                break
                case 'T':
                    category = 'Toll Free'
                break
                
            }

        // load status
        let status = ''
            switch (this.modalData.status)
            {
                case 'A':
                    status = 'Active'
                break
                case 'I':
                    status = 'Inactive'
                break                
                
            }

        // load type (prefix or from / to) 
        let label = ''
        let value = ''
          switch (this.modalData.typecustom)
          {
              case 'P':
                label = 'Prefix'
                value = 'P'
              break
              case 'FT':
                label = 'From / To'
                value = 'FT'
              break                
                
          }      
       
        this.formData = 
        {
            id: this.modalData.id,
            multiple: [
                {
                    typecustom: {
                    label: label,
                    value: value,
                    },
                    from: this.modalData.from,
                    to: this.modalData.to,
                    prefijo: this.modalData.prefix,
                    name: this.modalData.name,
                    rate_per_minutes: this.modalData.rate_per_minute,
                },
            ],        
            country_id: this.modalData.country_id,
            status: {value:this.modalData.status, text: status },
            category: {value:this.modalData.category, text: category },
        }

        // load country
        if (this.modalData.country_id)
        {
          this.isCountryActive = true          
          this.formData.country = this.countries.filter(
            (country) => country.id == this.modalData.country_id
          )
        }

      },     
      
    },
    
  }
  </script>
  