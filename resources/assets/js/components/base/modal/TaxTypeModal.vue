<template>
  <div class="overflow-auto" style="height:60vh;" data-modal-toggle="large-modal">
    <form
      action=""
      @submit.prevent="submitTaxTypeData"
      style="padding-top: 17.5px; padding-right: 37.5px; padding-left: 30px"
    >
      <div class="grid grid-cols-2 gap-2 mb-2">
        <sw-input-group
          :label="$t('tax_types.name')"
          :error="nameError"
          required
        >
          <sw-input
            ref="name"
            :invalid="$v.formData.name.$error"
            v-model="formData.name"
            type="text"
            @input="$v.formData.name.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('tax_types.percent')"
          :error="percentError"
          required
        >
          <sw-money
            v-model="formData.percent"
            :currency="defaultInput"
            :invalid="$v.formData.percent.$error"
            class="relative w-full focus:border focus:border-solid focus:border-primary"
            @input="$v.formData.percent.$touch()"
          />
        </sw-input-group>

        <!-- DESCRIPTION TAG -->

        <sw-input-group
          :label="$t('tax_types.description')"
          :error="descriptionError"
        >
          <sw-textarea
            v-model="formData.description"
            @input="$v.formData.description.$touch()"
            style="height: 113%; width: 255%"
          />
        </sw-input-group>

        <!-- DESCRIPTION TAG -->
        <br />

        <sw-input-group
          :label="$t('tax_types.compound_tax')"
          class="relative mt-2 mb-1"
        >
          <sw-switch v-model="formData.compound_tax" class="relative" />
        </sw-input-group>

        <!--<sw-input-group
          :label="$t('tax_types.for_cdr')"
          class="relative"
          variant="horizontal"
        >-->
        <sw-input-group
          :label="$t('tax_types.for_cdr')"
          class="relative mt-2 mb-1"
        >
          <sw-switch v-model="formData.for_cdr" class="relative" />
        </sw-input-group>
      </div>

      <div class="grid grid-cols-12">
      <div class="col-span-12 md:col-span-12">
        <sw-input-group
          :label="$t('tax_groups.title')"
          class="mb-4"
        >
          <sw-select
            v-model="formData.tax_groups"
            :options="getTaxGroups"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :multiple="true"
            :placeholder="$t('general.select_taxgroup')"
            class="relative w-full"
            track-by="tax_group_id"
            label="tax_group_name"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('customers.category')"
          class="mb-4"
        >
          <span
            v-if="formData.category"
            class="absolute text-gray-400 cursor-pointer"
            style="top: 10.55px; right: 5px; z-index: 999999"
            @click="deleteCategory()"
          >
            <x-circle-icon class="h-5" />
          </span>

          <sw-select
            v-if="!isLoadingCategory"
            v-model="formData.category"
            :options="categories"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :placeholder="$t('general.select_category')"
            label="name"
            track-by="id"
            z-index="9999"
            @select="categorySelected($event)"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('customers.agency')"
          class="mb-4"
        >
          <span
            v-if="formData.agency"
            class="absolute text-gray-400 cursor-pointer"
            style="top: 10.55px; right: 5px; z-index: 999999"
            @click="deleteAgency()"
          >
            <x-circle-icon class="h-5" />
          </span>

          <sw-select
            v-if="!isLoadingAgency"
            v-model="formData.agency"
            :options="agencies"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :placeholder="$t('general.select_agency')"
            label="name"
            track-by="id"
            z-index="9999"
            @select="agencySelected($event)"
          />
        </sw-input-group>

        <!-- DESDE AQUI -->

        <sw-input-group
          :label="$t('customers.country')"
          class="mb-4"
        >
          <span
            v-if="formData.country"
            class="absolute text-gray-400 cursor-pointer"
            style="top: 10.55px; right: 5px; z-index: 999999"
            @click="deleteCountry()"
          >
            <x-circle-icon class="h-5" />
          </span>

          <sw-select
            v-if="!isLoadingCountryState"
            v-model="formData.country"
            :options="countries"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :placeholder="$t('general.select_country')"
            label="name"
            track-by="id"
            @select="countrySelected($event)"
          />
        </sw-input-group>

        <!-- HASTA AQUI -->

        <sw-input-group
          :label="$t('customers.state')"
          class="mb-4"
        >
          <span
            v-if="formData.state"
            class="absolute text-gray-400 cursor-pointer"
            style="top: 10.55px; right: 5px; z-index: 999999"
            @click="deleteState()"
          >
            <x-circle-icon class="h-5" />
          </span>

          <sw-select
            v-if="!isLoadingCountryState"
            v-model="formData.state"
            :options="states"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            :placeholder="$t('general.select_state')"
            label="name"
            track-by="id"
            @select="stateSelected($event)"
          />
        </sw-input-group>
      </div>
    </div>

      
      <div class="grid grid-cols-2 gap-2">
        <sw-input-group :label="$t('customers.county')" class="relative">
          <sw-input ref="county" v-model="formData.county" type="text" />
        </sw-input-group>

        <sw-input-group :label="$t('customers.city')" class="relative">
          <sw-input ref="city" v-model="formData.city" type="text" />
        </sw-input-group>
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-solid border--200 border-modal-bg mt-8"
      >
        <sw-button
          class="mr-3 text-sm"
          variant="primary-outline"
          type="button"
          @click="closeTaxModal"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button :loading="isLoading" variant="primary" type="submit">
          <save-icon class="mr-2" v-if="!isLoading" />
          {{ !isEdit ? $t('general.save') : $t('general.update') }}
        </sw-button>
      </div>
    </form>
  </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { XCircleIcon } from '@vue-hero-icons/solid'
const {
  required,
  minLength,
  between,
  maxLength,
} = require('vuelidate/lib/validators')
export default {
  components: {
    XCircleIcon,
  },
  data() {
    return {
      isEdit: false,
      isLoading: false,
      isLoadingCountryState: false,
      isLoadingCategory: false,
      isLoadingAgency: false,
      formData: {
        agency: null,
        id: null,
        name: null,
        percent: 0.01,
        description: null,
        compound_tax: false,
        collective_tax: 0,
        tax_groups: [],
        country: null,
        state: null,
        category: null,
        country_id: null,
        state_id: null,
        tax_category_id: null,
        city: null,
        county: null,
        for_cdr: false,
      },
      defaultInput: {
        decimal: '.',
        thousands: ',',
        prefix: '% ',
        precision: 2,
        masked: false,
      },
      agencies: [],
      countries: [],
      states: [],
      categories: [],
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
      'props',
    ]),
    ...mapGetters('taxGroups', ['taxGroups']),
    getTaxGroups() {
      return this.taxGroups.map((group) => {
        return {
          ...group,
          tax_group_id: group.id,
          tax_group_name: group.name,
        }
      })
    },
    descriptionError() {
      if (!this.$v.formData.description.$error) {
        return ''
      }

      if (!this.$v.formData.description.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      } else {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
    },
    percentError() {
      if (!this.$v.formData.percent.$error) {
        return ''
      }

      if (!this.$v.formData.percent.required) {
        return this.$t('validation.required')
      } else {
        return this.$t('validation.enter_valid_tax_rate')
      }
    },
  },
  created() {
    this.loadData()
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },
      percent: {
        required,
        between: between(0, 100),
      },
      description: {
        maxLength: maxLength(255),
      },
    },
  },
  async mounted() {
    this.$refs.name.focus = true
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('taxType', [
      'addTaxType',
      'updateTaxType' /* 'fetchTaxType' */,
    ]),
    ...mapActions('taxGroups', ['fetchTaxGroups']),
    ...mapActions('taxCategories', ['fetchTaxCategory', 'fetchTaxCategories']),
    ...mapActions('taxAgency', ['fetchTaxAgencies']),

    resetFormData() {
      this.formData = {
        id: null,
        name: null,
        percent: 0,
        description: null,
        collective_tax: 0,
      }
      this.$v.formData.$reset()
    },
    async deleteCountry() {
      this.isLoading = true
      this.formData.country = null
      this.formData.state = null
      // console.log('country form data: ', this.formData.country);
      this.isLoading = false
    },
    async deleteState() {
      this.isLoading = true
      this.formData.state = null
      // console.log('state form data: ', this.formData.state);
      this.isLoading = false
    },
    async deleteCategory() {
      this.isLoadingCategory = true
      this.formData.category = null
      // console.log('state form data: ', this.formData.state);
      this.isLoadingCategory = false
    },
    async deleteAgency() {
      this.isLoadingAgency = true
      this.formData.agency = null
      this.isLoadingAgency = false
    },
    async loadData() {
      let res = await window.axios.get('/api/v1/countries')
      // cargar categorias
      let resCategories = await window.axios.get('/api/v1/tax-categories')
      // load agencies
      let resAgencies = await this.fetchTaxAgencies({ limit: 'all' })

      if (res) {
        this.countries = res.data.countries
      }

      if (resCategories) {
        this.categories = resCategories.data.taxCategories.data
      }

      if (resAgencies) {
        this.agencies = resAgencies.data.taxAgencies.data
      }

      if (this.modalDataID) {
        this.isEdit = true
        this.setData()
      }

      if (!this.isEdit) {
        this.fetchTaxGroups({ limit: 'all' })
      }
    },
    async submitTaxTypeData() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true
      let response

      if (this.formData.country) {
        this.formData.country_id = this.formData.country.id
      }

      if (this.formData.state) {
        this.formData.state_id = this.formData.state.id
      }

      if (this.formData.category) {
        this.formData.tax_category_id = this.formData.category.id
      }

      if (this.formData.agency) {
        this.formData.tax_agency_id = this.formData.agency.id
      }
      if (this.formData.for_cdr == true) {
        this.formData.for_cdr = 1
      }

      if (!this.isEdit) {
        //console.log(this.formData)
        response = await this.addTaxType(this.formData)
      } else {
        //console.log(this.formData)
        response = await this.updateTaxType(this.formData)
      }
      if (response.data) {
        if (!this.isEdit) {
          window.toastr['success'](
            this.$t('settings.tax_types.created_message')
          )
        } else {
          window.toastr['success'](
            this.$t('settings.tax_types.updated_message')
          )
        }

        if (this.props) {
          if (this.props.emitName) {
            window.hub.$emit(this.props.emitName, response.data.taxType)
          } else {
            window.hub.$emit('newTax', response.data.taxType)
          }
        }

        this.refreshData ? this.refreshData() : ''
        this.closeTaxModal2()
        this.isLoading = false
        return true
      }
      this.isLoading = false
      window.toastr['error'](response.data.error)
    },
    async setData() {
      // this.isLoading = true
      //console.log('modal data: ', this.modalData)
      //console.log('form data: ', this.formData)
      this.formData = {
        country_id: this.modalData.country_id,
        state_id: this.modalData.state_id,
        tax_category_id: this.modalData.tax_category_id,
        id: this.modalData.id,
        name: this.modalData.name,
        percent: this.modalData.percent,
        description: this.modalData.description,
        compound_tax: this.modalData.compound_tax ? true : false,
        tax_groups: this.modalData.tax_groups,
        city: this.modalData.city,
        county: this.modalData.county,
        for_cdr: this.modalData.for_cdr,
      }
      this.formData.tax_groups = this.modalData.tax_groups.map((tax) => {
        return { ...tax, tax_group_id: tax.id, tax_group_name: tax.name }
      })

      if (this.modalData.tax_agency_id) {
        this.formData.agency = this.agencies.filter(
          (agency) => agency.id === this.modalData.tax_agency_id
        )
      }

      if (this.modalData.tax_category_id) {
        this.formData.category = this.categories.filter(
          (cat) => cat.id == this.modalData.tax_category_id
        )
      }

      if (this.modalData.country_id) {
        this.formData.country = this.countries.filter(
          (c) => c.id == this.modalData.country_id
        )
        let res = await this.countrySelected(this.formData.country[0])
        if (res && this.modalData.state_id) {
          this.isLoadingCountryState = true
          this.formData.state = this.states.filter(
            (s) => s.id == this.modalData.state_id
          )
          this.isLoadingCountryState = false
          // console.log('state: ', this.formData.state);
        }
      }

      /* console.log('country id: ', this.modalData.country_id);*/
      // console.log('state id: ', this.formData.state_id);
      // this.isLoading = false
    },
    agencySelected(value) {
      this.isLoadingAgency = true
      this.formData.agency = value
      this.isLoadingAgency = false
    },
    async countrySelected(country) {
      //console.log('country: ', country);
      // const vm = this
      // vm.isLoading = true

      let res = await window.axios.get('/api/v1/states/' + country.code)
      this.states = res.data.states
      // vm.isLoading = false
      return true
    },
    stateSelected(state) {
      this.isLoadingCountryState = true
      this.formData.state = state
      //console.log(this.formData.state)
      this.isLoadingCountryState = false
    },
    categorySelected(category) {
      this.isLoadingCategory = true
      this.formData.category = category
      this.isLoadingCategory = false
    },
    closeTaxModal() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.cancel_text'),
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          this.resetModalData()
          this.resetFormData()
          this.closeModal()
        }
      })
    },

    closeTaxModal2() {
      this.resetModalData()
          this.resetFormData()
          this.closeModal()
    },
  },
}
</script>
