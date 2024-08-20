<template>
  <div class="tax-type-modal">
    <form action="" @submit.prevent="submitTaxCategoryData">
      <div class="p-8 sm:p-6">
        <sw-input-group
          :label="$t('settings.tax_categories.name')"
          :error="nameError"
          class="mt-3"
          variant="horizontal"
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

        <!-- <sw-input-group
          :label="$t('tax_types.percent')"
          :error="percentError"
          class="mt-3"
          variant="horizontal"
          required
        >
          <sw-money
            v-model="formData.percent"
            :currency="defaultInput"
            :invalid="$v.formData.percent.$error"
            class="
              relative
              w-full
              focus:border focus:border-solid focus:border-primary
            "
            @input="$v.formData.percent.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('tax_types.description')"
          :error="descriptionError"
          class="mt-3"
          variant="horizontal"
        >
          <sw-textarea
            v-model="formData.description"
            rows="4"
            cols="50"
            @input="$v.formData.description.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('tax_types.compound_tax')"
          class="mt-3"
          variant="horizontal"
        >
          <sw-switch
            v-model="formData.compound_tax"
            class="flex items-center mt-1"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('tax_groups.title')"
          class="mt-3"
          variant="horizontal"
        >
          <sw-select
            v-model="formData.tax_groups"
            :options="getTaxGroups"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :multiple="true"
            class="
              relative
              w-full
              focus:border focus:border-solid focus:border-primary
            "
            track-by="tax_group_id"
            label="tax_group_name"
          />
        </sw-input-group>

        <sw-input-group :label="$t('customers.country')" class="mt-3"   variant="horizontal">
          <span
            v-if="formData.country"
            class="absolute text-gray-400 cursor-pointer"
            style="top: 418px; right: 28px; z-index: 999999"
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

        <sw-input-group :label="$t('customers.state')"
          class="mt-3" variant="horizontal"
        >
          <span
            v-if="formData.state"
            class="absolute text-gray-400 cursor-pointer"
            style="top: 470px; right: 28px; z-index: 999999"
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
        </sw-input-group> -->

      </div>

      <div
        class="
          z-0
          flex
          justify-end
          p-4
          border-t border-solid border--200 border-modal-bg
        "
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
    XCircleIcon
  },
  data() {
    return {
      isEdit: false,
      isLoading: false,
      isLoadingCountryState: false,
      formData: {
        id: null,
        name: null,
        /* percent: 0,
        description: null,
        compound_tax: false,
        collective_tax: 0,
        tax_groups: [],
        country: null,
        state: null,
        country_id: null,
        state_id: null, */
      },
     /*  defaultInput: {
        decimal: '.',
        thousands: ',',
        prefix: '% ',
        precision: 2,
        masked: false,
      },
      countries: [],
      states: [], */
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
    // ...mapGetters('taxGroups', ['taxGroups']),
    /* getTaxGroups() {
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
    }, */
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
    /* percentError() {
      if (!this.$v.formData.percent.$error) {
        return ''
      }

      if (!this.$v.formData.percent.required) {
        return this.$t('validation.required')
      } else {
        return this.$t('validation.enter_valid_tax_rate')
      }
    }, */
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
      /* percent: {
        required,
        between: between(0, 100),
      },
      description: {
        maxLength: maxLength(255),
      }, */
    },
  },
  async mounted() {
    this.$refs.name.focus = true
    
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('taxCategories', 
        [
          'addTaxCategory', 
          'updateTaxCategory', 
          /*'fetchTaxType' */
        ]),
    // ...mapActions('taxGroups', ['fetchTaxGroups']),
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
   /*  async deleteCountry(){
      this.isLoading = true
      this.formData.country = null
      this.formData.state = null
      // console.log('country form data: ', this.formData.country);
      this.isLoading = false
    },
    async deleteState(){
      this.isLoading = true
      this.formData.state = null
      // console.log('state form data: ', this.formData.state);
      this.isLoading = false
    }, */
    async loadData() {

      if (this.modalDataID) {
        this.isEdit = true
        this.setData()
      }
     /*  let res = await window.axios.get('/api/v1/countries')

      if (res) {
        this.countries = res.data.countries
        if (this.modalDataID) {
          this.isEdit = true
          this.setData()
        }
      }

      if (!this.isEdit) {
        this.fetchTaxGroups({ limit: 'all' })
      } */
    },
    async submitTaxCategoryData() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true
      let response

     /*  if(this.formData.country){
        this.formData.country_id = this.formData.country.id
      }

      if(this.formData.state){
        this.formData.state_id = this.formData.state.id
      } */


    /* visa: 3.39
    master: 2.86 */

    //console.log('edit: ', this.isEdit);
    //console.log('FORM DATA: ', this.formData);

      if (!this.isEdit) {
        //console.log(this.formData)
        response = await this.addTaxCategory(this.formData)
      } else {
        response = await this.updateTaxCategory(this.formData)
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
        this.closeTaxModal()
        this.isLoading = false
        return true
      }
      window.toastr['error'](response.data.error)
    },
    async setData() {
      // this.isLoading = true
      //console.log('modal data: ', this.modalData);
      /* console.log('form data: ', this.formData); */
      this.formData = {
        name: this.modalData.name,
        id: this.modalDataID
      }

    },
     async countrySelected(country) {
      // console.log('country: ', country);
      // const vm = this
      // vm.isLoading = true
    
      let res = await window.axios.get('/api/v1/states/' + country.code)
      this.states = res.data.states
      // vm.isLoading = false
      return true
    },
    stateSelected(state){
      this.isLoadingCountryState = true
      this.formData.state = state
     // console.log(this.formData.state);
      this.isLoadingCountryState = false
    },
    closeTaxModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>
