<template>
  <sw-card variant="setting-card">
    <!-- Base  -->
    <base-page v-if="isSuperAdmin" class="tax-group-create">
      <!--------- Form ---------->
      <form action="" @submit.prevent="submitTaxGroup">
        <!-- Header  -->
        <sw-page-header class="mb-3" :title="pageTitle">
          <sw-breadcrumb slot="breadcrumbs">
            <sw-breadcrumb-item
              to="/admin/dashboard"
              :title="$t('general.home')"
            />
            <sw-breadcrumb-item
              to="/admin/settings/tax-groups"
              :title="$t('tax_groups.tax_group')"
            />
            <sw-breadcrumb-item
              v-if="$route.name === 'tax-groups.edit'"
              to="#"
              :title="$t('tax_groups.edit_tax_group')"
              active
            />
            <sw-breadcrumb-item
              v-else
              to="#"
              :title="$t('tax_groups.new_tax_group')"
              active
            />
          </sw-breadcrumb>

          <template slot="actions">
            <sw-button
              @click="cancelButton"
              :loading="isLoading"
              :disabled="isLoading"
              variant="primary-outline"
              type="button"
              size="lg"
              class="flex justify-center w-full md:w-auto mr-2"
            >
              {{ $t('general.cancel') }}
            </sw-button>
            <sw-button
              :loading="isLoading"
              :disabled="isLoading"
              variant="primary"
              type="submit"
              size="lg"
              class="flex justify-center w-full md:w-auto"
            >
              <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
              {{
                isEdit
                  ? $t('tax_groups.update_tax_group')
                  : $t('tax_groups.save_tax_group')
              }}
            </sw-button>
          </template>
        </sw-page-header>

        <div class="grid grid-cols-12">
          <div class="col-span-12">
            <sw-card class="mb-8">
              <sw-input-group
                :label="$t('tax_groups.name')"
                :error="nameError"
                class="mb-4"
                required
              >
                <sw-input
                  v-model.trim="formData.name"
                  :invalid="$v.formData.name.$error"
                  class="mt-2"
                  focus
                  type="text"
                  name="name"
                  @input="$v.formData.name.$touch()"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('tax_groups.description')"
                :error="descriptionError"
                class="mb-4"
              >
                <sw-textarea
                  v-model.trim="formData.description"
                  :placeholder="$t('tax_groups.description')"
                  type="text"
                  name="description"
                  rows="3"
                  tabindex="11"
                  @input="$v.formData.description.$touch()"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('tax_groups.status')"
                class="md:col-span-3 mb-4"
                :error="statusError"
                required
              >
                <sw-select
                  v-model="formData.status"
                  :invalid="$v.formData.status.$error"
                  :options="status"
                  :searchable="true"
                  :show-labels="false"
                  :tabindex="16"
                  :allow-empty="true"
                  :placeholder="$t('tax_groups.status')"
                  label="text"
                  track-by="value"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('tax_groups.country')"
                class="md:col-span-3 mb-4"
              >
                <sw-select
                  v-model="formData.countries"
                  :options="countries"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :tabindex="8"
                  :placeholder="$t('general.select_country')"
                  label="name"
                  track-by="id"
                  @select="countrySeleted"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('tax_groups.state')"
                class="md:col-span-3 mb-4"
              >
                <sw-select
                  v-model="formData.states"
                  :options="states"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :tabindex="8"
                  :placeholder="$t('general.select_state')"
                  label="name"
                  track-by="id"
                  select="stateSeleted"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('customers.county')"
                class="mb-4"
                
              >
                <sw-input ref="county" v-model="formData.county" type="text" />
              </sw-input-group>

              <sw-input-group
                :label="$t('customers.city')"
                class="mb-4"
                
              >
                <sw-input ref="city" v-model="formData.city" type="text" />
              </sw-input-group>

              <div class="grid grid-cols-5 gap-4 mb-8">
                <!-- General Tax  -->
                <div class="grid col-span-12 gap-y-1 gap-x-4 md:grid-cols-6">
                   
                  <sw-input-group :label="$t('packages.taxes')" class="md:col-span-3" :error="taxesError" required>
                    <sw-select
                      v-model="tax"
                      :options="taxes"
                      :searchable="true"
                      :show-labels="false"
                      :allow-empty="true"
                      :placeholder="$tc('packages.add_tax')"
                      class="mt-2"
                      label="name_por"
                      track-by="id"
                      @select="taxSeleted"
                      :tabindex="9"                     
                      name="taxes"
                      :invalid="$v.formData.taxes.$error"
                    />
                  </sw-input-group>

                  <div class="col-span-12"></div>

                  <sw-table-component
                    class="col-span-12"
                    ref="table"
                    :show-filter="false"
                    :data="paralelo"
                    table-class="table"
                    variant="gray"
                  >
                    <sw-table-column
                      :sortable="true"
                      :label="$t('settings.tax_types.tax_name')"
                      show="name"
                    >
                      <template slot-scope="row">
                        <span>{{ $t('settings.tax_types.tax_name') }}</span>
                        <span class="mt-6">{{ row.name }}</span>
                      </template>
                    </sw-table-column>

                    <sw-table-column
                      :sortable="true"
                      :filterable="true"
                      :label="$t('settings.tax_types.compound_tax')"
                    >
                      <template slot-scope="row">
                        <span>{{ $t('settings.tax_types.compound_tax') }}</span>
                        <sw-badge
                          :bg-color="
                            $utils.getBadgeStatusColor(
                              row.compound_tax ? 'YES' : 'NO'
                            ).bgColor
                          "
                          :color="
                            $utils.getBadgeStatusColor(
                              row.compound_tax ? 'YES' : 'NO'
                            ).color
                          "
                        >
                          {{ row.compound_tax ? 'Yes' : 'No'.replace('_', ' ') }}
                        </sw-badge>
                      </template>
                    </sw-table-column>

                    <sw-table-column
                      :sortable="true"
                      :filterable="true"
                      :label="$t('settings.tax_types.percent')"
                    >
                      <template slot-scope="row">
                        <span>{{ $t('settings.tax_types.percent') }}</span>
                        {{ row.percent }} %
                      </template>
                    </sw-table-column>

                    <sw-table-column :sortable="true" :filterable="true">
                      <template slot-scope="row">
                        <trash-icon
                          @click="removeTax(row)"
                          class="h-5 mr-3 text-gray-600"
                        />
                      </template>
                    </sw-table-column>
                  </sw-table-component>

                </div>
              </div>
            </sw-card>
          </div>
        </div>
      </form>
    </base-page>
  </sw-card>
</template>

<script>
import draggable from 'vuedraggable'
import { mapActions, mapGetters } from 'vuex'
import {
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
  TrashIcon
} from '@vue-hero-icons/solid'
const { required, minLength, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    draggable,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
    TrashIcon
  },
  data() {
    return {
      countries: [],
      groupTax: null,
      groupTaxes: [],
      isLoading: false,
      isNoGeneralTaxes: false,
      title: 'Add Tax Group',
      tax: null,
      taxGroupRight: [],
      taxGroupLeft: [],
      taxes: [],
      taxesFetch: [],
      paralelo: [],
      states: [],
      status: [
        {
          value: 'A',
          text: 'Active',
        },
        {
          value: 'I',
          text: 'Inactive',
        },
      ],
      formData: {
        name: '',
        description: '',
        status: {
          value: 'A',
          text: 'Active',
        },
        country_id: '',
        state_id: '',
        country_name: '',
        state_name: '',
        countries: [],
        states: [],
        county: null,
        city: null,    
        taxes: []    
      },
    }
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
        maxLength: maxLength(255),
      },
      description: {
        maxLength: maxLength(255),
      },
      status: {
        required,
      },
      taxes: {
        required,
      },
    },
  },
  computed: {
    ...mapGetters('user', ['currentUser']),

    ...mapGetters('company', ['defaultCurrency']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    pageTitle() {
      if (this.$route.name === 'tax-groups.edit') {
        return this.$t('tax_groups.edit_tax_group')
      }
      return this.$t('tax_groups.new_tax_group')
    },

    isEdit() {
      if (this.$route.name === 'tax-groups.edit') {
        return true
      }
      return false
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }

      if (!this.$v.formData.name.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
    
    taxesError() {
      if (!this.$v.formData.taxes.$error) {
        return ''
      }

      if (!this.$v.formData.taxes.required) {
        return this.$t('validation.required_taxes')
      }      
    },

    descriptionError() {
      if (!this.$v.formData.description.$error) {
        return ''
      }

      if (!this.$v.formData.description.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },

    statusError() {
      if (!this.$v.formData.status.$error) {
        return ''
      }
    },
  },
  created() {
    this.loadTaxMembership()
    this.fetchInitDataCountry()
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
    if (this.isEdit) {
      this.loadEditTaxGroup()
    }
  },
  mounted() {
    this.fetchTax()
    this.$v.formData.$reset()
  },
  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('taxGroups', [
      'addTaxGroup',
      'fetchTaxGroup',
      'updateTaxGroup',
      'fetchTaxMembership',
    ]),

    ...mapActions('taxType', [ 'fetchTaxTypes',]),
    ...mapActions('user', [ 'getUserModules',]),

    async countrySeleted(val) {
      let res = await window.axios.get('/api/v1/states/' + val.code)
      if (res) {
        this.states = res.data.states
      }
      this.formData.countries = val
    },
    async fetchInitDataCountry() {
      this.initLoad = true
      let res = await window.axios.get('/api/v1/countries')
      if (res) {
        this.countries = res.data.countries
      }
      this.initLoad = false
    },
    async fetchTax() {
      let data = {
        orderByField: 'created_at',
        orderBy: 'asc',
        limit: 1000,
      }
     
      // taxes list 
      let response = await this.fetchTaxTypes(data)       
      let taxes = await response.data.taxTypes.data
   
      // Generate taxes list (for_cdr == 0)
      const taxes_for_cdr_0 =  taxes.filter(tax => tax.for_cdr == 0);   

      let res = []
        res = taxes_for_cdr_0.filter((el) => {
          return !this.taxesFetch.find((element) => {
            return element.id === el.id
          })
        return res
      })  

      this.taxesFetch = res
      this.taxes = this.taxesFetch

      this.taxes.forEach((tax) => {       
        tax.name_por = `${tax.name} - ${tax.percent}%`     
      })      

    },    
    filterByReference(arr1, arr2) {   
      let res = []
      res = arr1.filter((el) => {
        return !arr2.find((element) => {
          return element.id === el.id
        })
      })
      return res
    },
    async taxSeleted(val) {

      const isId = (element) => element.id == val.id
      const index = this.formData.taxes.findIndex(isId)

      if (index == -1) 
      {
        this.formData.taxes.push(val)   
      }
      else 
      {
        window.toastr['error']('This tax was already selected')
        this.taxes = this.filterByReference(this.taxesFetch, this.formData.taxes)        
      }

      this.paralelo = this.formData.taxes
    
      this.taxes = this.filterByReference(this.taxesFetch, this.formData.taxes)
      setTimeout(() => {
        this.tax = null
      }, 100)
    },

    async stateSeleted(val) {
      this.formData.states = val
    },

    async loadTaxMembership() {
      let res = await this.fetchTaxMembership()
      this.taxGroupRight = res.data
    },

    async loadEditTaxGroup() {
      let response = await this.fetchTaxGroup(this.$route.params.id)

      this.formData = response.data.tax_groups

      if (response.data.tax_groups.countries) {
        let res = await window.axios.get(
          '/api/v1/states/' + response.data.tax_groups.countries.code
        )
        if (res) {
          this.states = res.data.states
        }
      }

      this.taxGroupLeft = this.formData.taxes = this.paralelo = response.data.taxes
    
      for (var i = 0; i < this.taxGroupLeft.length; i++) {
        for (var j = 0; j < this.taxGroupRight.length; j++) {
          if (this.taxGroupLeft[i].id === this.taxGroupRight[j].id) {
            this.taxGroupRight.splice(j, 1)
          }
        }
      }
    },

    async removeTax(tax) {
    
      let myArray = this.formData.taxes

      for (var i = myArray.length - 1; i >= 0; --i) {
        if (myArray[i].id == tax.id) {
          myArray.splice(i, 1)
        }
      }

      this.formData.taxes = myArray
      this.paralelo = [...myArray]
     
      const filterByReference = (arr1, arr2) => {
        let res = []
        res = arr1.filter((el) => {
          return !arr2.find((element) => {
            return element.id === el.id
          })
        })
        return res
      }

      this.taxes = filterByReference(this.taxesFetch, this.formData.taxes)
      this.tax = null
      
    },
    async submitTaxGroup() {
      
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.formData.status = this.formData.status.value

      if (this.taxGroupLeft.length > 0) {
        this.formData.taxGroupLeft = this.taxGroupLeft
      }

      if (this.formData.countries) {
        this.formData.country_id = this.formData.countries.id
      }

      if (this.formData.states) {
        this.formData.state_id = this.formData.states.id
      } 
      
      try {
        let response
        this.isLoading = true

        const data = {
         module: "tax_Groups" 
        }
        const permissions = await this.getUserModules(data)
        // valida que el usuario tenga permiso para ingresar al modulo
        if(permissions.super_admin == false){
          if(permissions.exist == false ){
            this.$router.push('/admin/dashboard')
          }else {
            const modulePermissions = permissions.permissions[0]
            if(modulePermissions.create == 0 && this.isEdit == false){
            this.$router.push('/admin/dashboard')
          }else if(modulePermissions.update == 0 && this.isEdit == true ){
            this.$router.push('/admin/dashboard')
          }
          }
        }
        if (this.isEdit){
          response = await this.updateTaxGroup(this.formData)
          if (response.status === 200) {
            window.toastr['success'](this.$t('tax_groups.updated_message'))
            this.$router.push('/admin/settings/tax-groups')
          }
          if (response.data.error) {
            window.toastr['error'](response.data.error)
          }
        } else{          
          response = await this.addTaxGroup(this.formData)
          if (response.status === 200) {           
            window.toastr['success'](this.$tc('tax_groups.created_message'))
            this.$router.push('/admin/settings/tax-groups')
          }
          if (response.data.error) {            
            window.toastr['error'](response.data.error)
          }
        }

        this.isLoading = false
        return true
      } catch (err) {}
    },
    moveToLeft(item, index) {
      if (this.isEdit) {
        item.new = true
      }
      this.taxGroupLeft.push(item)
      this.taxGroupRight.splice(index, 1)
    },
    moveToRight(item, index) {
      this.taxGroupRight.push(item)
      this.taxGroupLeft.splice(index, 1)
    },
    cancelButton(){
      this.$utils.cancelFormOrBack(this, this.$router, 'cancel')
    }
    
  },
}
</script>

