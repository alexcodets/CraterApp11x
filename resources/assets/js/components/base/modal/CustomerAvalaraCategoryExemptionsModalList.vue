<template>
<sw-modal ref="baseModalExceptionAvalara" variant="lg">
      <template v-slot:header>
        <div
          class="absolute flex content-center justify-center w-5 cursor-pointer"
          style="top: 20px; right: 15px"
          @click="closeAvalaraLocationsModal"
        >
          <x-icon />
        </div>
        <span>{{ $t('avalara.category_exemptions') }}</span>
      </template>
     <!-- {{component}} -->
    
  <form action="" @submit.prevent="submitAvalaraCategoryExemption">
    <!-- baseloader -->
    <base-loader v-if="isLoading" />
    <div class="sm:p-6 grid md:grid-cols-2 gap-4 mb-1">
      <!-- exemption name -->
    <sw-input-group
        :label="$t('avalara.exemption_name')"
        class="mt-2"
        :error="exemptionNameError"
        required
      >
        <sw-input
        v-model="formData.exemption_name"
        :invalid="$v.formData.exemption_name.$error"
        name="formData.exemption_name"
        type="text"
        @input="$v.formData.exemption_name.$touch()"
      />
    </sw-input-group>
      <sw-input-group
        :label="$tc('avalara.exemption_type')"
        class="mt-2"
      >
        <sw-select
          v-model="exemptionTypeSelect"
          :options="exemptionTypeOptions"
          class="mt-2 "
          :placeholder="$t('avalara.exemption_type')"
          label="text"
          track-by="value"
        />
      </sw-input-group>
    </div>

    <sw-divider />

    <div
      class="sm:p-6 grid md:grid-cols-2 gap-4 mb-1"
      v-if="exemptionTypeSelect.value == 1"
    >
      <sw-input-group
        :label="$tc('avalara.category_exemptions')"
        class="mt-2"
        required
        :error="categoryExceptionError"
      >
        <sw-select
          v-model="formData.categoryException"
          :options="categoryExemptionsOptions"
          :show-labels="false"
          :allow-empty="true"
          class="mt-2"
          :placeholder="$t('avalara.category_exemptions')"
          label="name"
          track-by="id"
          @input="$v.formData.categoryException.$touch()"
          :invalid="$v.formData.categoryException.$error"
        />
      </sw-input-group>
    </div>

    <!-- AQUI -->

    <div
      class="sm:p-6 grid md:grid-cols-2 gap-4 mb-1"
      v-if="exemptionTypeSelect.value == 2"
    >
      <h6 class="sw-section-title md:col-span-2">
        {{ $t('avalara.tax_type') }}
      </h6>

      <sw-input-group
        :label="$tc('avalara.category')"
        class="mt-2"
        required
        :error="categoryDefinitionsError"
      >
        <sw-select
          v-model="formData.categoryDefinitions"
          :options="categoryDefinitionsOptions"
          :show-labels="false"
          :allow-empty="true"
          class="mt-2"
          :placeholder="$t('avalara.category')"
          label="name"
          track-by="id"
          @input="$v.formData.categoryDefinitions.$touch()"
          :invalid="$v.formData.categoryDefinitions.$error"
        />
      </sw-input-group>

      <sw-input-group
        :label="$tc('avalara.tax_type')"
        class="mt-2"
        :error="taxTypeError" required
      >
        <sw-select
          v-model="formData.taxType"
          :options="taxTypeOptionsFilter"
          :show-labels="false"
          :allow-empty="true"
          class="mt-2"
          :placeholder="$t('avalara.tax_type')"
          label="TaxDescription"
          track-by="TaxType"
          @input="$v.formData.taxType.$touch()"
          :invalid="$v.formData.taxType.$error"
        />
      </sw-input-group>
    </div>

     <!-- AQUI -->

    <sw-divider />

    <div class="sm:p-6 grid md:grid-cols-3 gap-4 mb-1">
      <sw-input-group
        :label="$tc('avalara.force')"
        class="mt-2"
        required
        :error="frcError"
      >
        <sw-select
          v-model="formData.frc"
          :options="frcOptions"
          :show-labels="false"
          :allow-empty="true"
          class="mt-2"
          :placeholder="$t('avalara.force')"
          label="text"
          track-by="value"
          @input="$v.formData.frc.$touch()"
          :invalid="$v.formData.frc.$error"
        />
      </sw-input-group>

      <sw-input-group
        :label="$tc('avalara.exemption_domain')"
        class="mt-2"
        required
        :error="domError"
      >
        <sw-select
          v-model="formData.dom"
          :options="domOptions"
          :show-labels="false"
          :allow-empty="true"
          class="mt-2"
          :placeholder="$t('avalara.exemption_domain')"
          label="text"
          track-by="value"
          @input="$v.formData.dom.$touch()"
          :invalid="$v.formData.dom.$error"
        />
      </sw-input-group>

      <sw-input-group
        :label="$tc('avalara.exemption_scope')"
        class="mt-2"
        required
       :error="scpError"
      >
        <sw-select
          v-model="formData.scp_selected"
          :options="scpOptions"
          :show-labels="false"
          :allow-empty="true"
          class="mt-2 multi-select-item"
          :placeholder="$t('avalara.exemption_scope')"
          label="text"
          track-by="value"
          @input="$v.formData.scp_selected.$touch()"
          :multiple="true"
        />
      </sw-input-group>

      <sw-input-group
        :label="$tc('avalara.exempt_non-billable')"
        class="mt-2"
        required
        :error="exnbError"
      >
        <sw-select
          v-model="formData.exnb"
          :options="exnbOptions"
          :show-labels="false"
          :allow-empty="true"
          class="mt-2"
          :placeholder="$t('avalara.exempt_non-billable')"
          label="text"
          track-by="value"
          @input="$v.formData.exnb.$touch()"
          :invalid="$v.formData.exnb.$error"
        />
      </sw-input-group>
    </div>

    <div
      class="
        z-0
        flex
        justify-end
        p-4
        border-t border-gray-200 border-solid border-modal-bg
      "
    >
      <sw-button
        type="button"
        variant="primary-outline"
        class="mr-3 text-sm"
        @click="closeAvalaraLocationsModal"
      >
        {{ $t('general.cancel') }}
      </sw-button>
      <sw-button variant="primary" type="submit" :loading="isLoading">
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('general.save') }}
      </sw-button>
    </div>
  </form>

  </sw-modal>
</template>

<script>
import { XIcon } from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'
const { required, requiredIf } = require('vuelidate/lib/validators')
export default {
  components: {
    XIcon,
  },
  data() {
    return {
      exemptionTypeOptions: [
        {
          value: 1,
          text: 'Category exemption',
        },
        {
          value: 2,
          text: 'Tax type exemption',
        },
      ],
      exemptionTypeSelect: {
          value: 1,
          text: 'Category exemption',
        },
      categoryDefinitionsOptions: [
        {
          id: 1,
          name: 'Sales and Use Taxes',
        },
        {
          id: 2,
          name: 'Business Taxes',
        },
        {
          id: 3,
          name: 'Gross Receipts Taxes',
        },
        {
          id: 4,
          name: 'Excise Taxes',
        },
        {
          id: 5,
          name: 'Connectivity Charges',
        },
        {
          id: 6,
          name: 'Regulatory Charges',
        },
        {
          id: 7,
          name: 'E-911 Charges',
        },
        {
          id: 8,
          name: 'Utility User Taxes',
        },
        {
          id: 9,
          name: 'Right of Way Fees',
        },
        {
          id: 10,
          name: 'Communication Services Tax',
        },
        {
          id: 11,
          name: 'Cable Regulatory Fees',
        },
        {
          id: 12,
          name: 'Reserved',
        },
        {
          id: 13,
          name: 'Value Added Taxes',
        },
      ],
      taxTypeOptions: [],
      taxesFiltered: [],
      categoryExemptionsOptions: [
        {
          id: 0,
          name: 'Wildcard',
        },
        {
          id: 1,
          name: 'Sales and Use Tax',
        },
        {
          id: 2,
          name: 'Business Taxes',
        },
        {
          id: 3,
          name: 'Gross Receipts Taxes',
        },
        {
          id: 4,
          name: 'Excise Taxes',
        },
        {
          id: 5,
          name: 'Connectivity Charges',
        },
        {
          id: 6,
          name: 'Regulatory Charges',
        },
        {
          id: 7,
          name: 'E-911 Charges',
        },
        {
          id: 8,
          name: 'Utility User Taxes',
        },
        {
          id: 9,
          name: 'Right of Way Fees',
        },
        {
          id: 10,
          name: 'Communications Services Tax',
        },
        {
          id: 11,
          name: 'Cable Regulatory Fees',
        },
        {
          id: 12,
          name: 'Reserved',
        },
        {
          id: 13,
          name: 'Value Added Taxes',
        },
      ],
      frcOptions: [
        {
          value: 1,
          text: 'Yes',
        },
        {
          value: 0,
          text: 'No',
        },
      ],
      domOptions: [
        {
          value: 0,
          text: 'Federal',
        },
        {
          value: 1,
          text: 'State',
        },
        {
          value: 2,
          text: 'County',
        },
        {
          value: 3,
          text: 'City',
        },
      ],
      scpOptions: [
        {
          value: 128,
          text: 'Federal',
        },
        {
          value: 256,
          text: 'State',
        },
        {
          value: 512,
          text: 'County',
        },
        {
          value: 1024,
          text: 'Local',
        },
      ],
      exnbOptions: [
        {
          value: 1,
          text: 'Can Be Exempted',
        },
        {
          value: 0,
          text: 'Cannot Be Exempted',
        },
      ],
      isEdit: false,
      isLoading: false,
      formData: {
        exemption_name: '',
        categoryException: null,
        categoryDefinitions: null,
        taxType: null,
        cat: null,
        tpe: null,
        frc: null,
        dom: null,
        scp: null,
        exnb: null,
        user_id: null,
        avalara_locations_id: null,
      },
      scp_selected: [],
      dataModal: {},
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalActive',
      'refreshData',
    ]),
    ...mapGetters(['countries']),
   
    calculateScp(){
      if(this.formData.scp_selected.lenght != 0){

        this.formData.scp = this.formData.scp_selected.reduce((acc, current) => {
          return acc + current.value
        }, null)
      }
    },

    taxTypeOptionsFilter(){
        if(this.exemptionTypeSelect.value == 2){
        
          this.formData.taxType = null
         // console.log(this.formData);
         // console.log(this.taxTypeOptions);
          return this.taxTypeOptions.filter(
            (e) => e.CategoryType == this.formData.categoryDefinitions?.id
            )
        }
      
    },
    categoryExceptionError() {
      if (!this.$v.formData.categoryException.$error) {
        return ''
      }
      if (!this.$v.formData.categoryException.required) {
        return this.$tc('validation.required')
      }
    },
    categoryDefinitionsError() {
      if (!this.$v.formData.categoryDefinitions.$error) {
        return ''
      }
      if (!this.$v.formData.categoryDefinitions.required) {
        return this.$tc('validation.required')
      }
    },
    taxTypeError() {
      /*
      if (!this.$v.formData.taxType.$error) {
        return ''
      }
      if (!this.$v.formData.taxType.required) {
        return this.$tc('validation.required')
      }
      */

      if (!this.$v.formData.taxType.$error) {
        return ''
      }

      if (!this.$v.formData.taxType.required) {
        return this.$t('validation.required_taxes')
      }   

    },
    frcError() {
      if (!this.$v.formData.frc.$error) {
        return ''
      }
      if (!this.$v.formData.frc.required) {
        return this.$tc('validation.required')
      }
    },
    domError() {
      if (!this.$v.formData.dom.$error) {
        return ''
      }
      if (!this.$v.formData.dom.required) {
        return this.$tc('validation.required')
      }
    },
    exemptionNameError() {
      if (!this.$v.formData.exemption_name.$error) {
        return ''
      }
      if (!this.$v.formData.exemption_name.required) {
        return this.$tc('validation.required')
      }
    },
    scpError() {
      if (!this.$v.formData.scp_selected.$error) {
        return ''
      }
      if (!this.$v.formData.scp_selected.required) {
        return this.$tc('validation.required')
      }
    },
    exnbError() {
      if (!this.$v.formData.exnb.$error) {
        return ''
      }
      if (!this.$v.formData.exnb.required) {
        return this.$tc('validation.required')
      }
    },
  },
  validations: {
    formData: {
      categoryException: {
        requiredIfCategoryException: requiredIf(function () {
          return this.exemptionTypeSelect.value === 1
        })
      },
      categoryDefinitions: {
        requiredIfCategoryDefinitions: requiredIf(function () {
          return this.exemptionTypeSelect.value === 2
        })
      },
      taxType: {
        requiredIfExemptionTypeSelect: requiredIf(function () {
          return this.exemptionTypeSelect.value === 2
        })
      },
      frc: {
        required,
      },
      dom: {
        required,
      },
      exemption_name: {
        required,
      },
      scp_selected: {
        required,
      },
      exnb: {
        required,
      },
    },
  },
  watch: {
  },

  mounted() {
    this.getTypeTaxOptions()
  },

  methods: {
    ...mapActions('modal', ['closeModal']),
    ...mapActions('avalara', [
      'saveAvalaraLocation',
      'updateAddress',
      'getTaxTypes',
      'fetchAvalaraDefault',
      'exemptionAdded',
    ]),
    
    async getTypeTaxOptions() {
      try {
        this.isLoading = true
        const avalaraDefault = await this.fetchAvalaraDefault()
       // console.log(avalaraDefault);
        if (avalaraDefault.data?.success) {
          //console.log(avalaraDefault.data.data.id);
          const getTaxTypes = await this.getTaxTypes(
            avalaraDefault.data.data.id
            )

            //console.log(getTaxTypes);
          this.taxTypeOptions = getTaxTypes.data.data
        }
      } catch (e) {
       // console.log(e)
      } finally {
        this.isLoading = false
      }
    },
    async setData(data) {
      this.dataModal = data.billing
      this.formData.user_id = data.userId
      this.formData.avalara_locations_id = data.avalara_location_id
      this.$refs.baseModalExceptionAvalara.open()
    },
    closeAvalaraLocationsModal() {
      this.$refs.baseModalExceptionAvalara.close()
    },
    async submitAvalaraCategoryExemption() {

      this.$v.formData.$touch()
      if (this.$v.$invalid) {
         return true
      }
      
      // if(this.$v.formData.categoryDefinitions.$invalid && this.$v.formData.categoryException.$invalid)
      // {
      //   return true;      
      // }
      // if(this.$v.formData.frc.$invalid || this.$v.formData.dom.$invalid || this.$v.formData.scp.$invalid || this.$v.formData.exnb.$invalid)
      // {
      //   return true;
      // }
      // if(this.$v.formData.categoryDefinitions.$invalid == false)
      // {
      //   if(this.$v.formData.taxType.$invalid)
      //   {
      //     return true;
      //   }
      // }  

      try {
      // console.log(this.formData)
        this.isLoading = true                
        this.formData.categoryException = this.formData.categoryException?.id
        this.formData.categoryDefinitions = this.formData.categoryDefinitions?.id 

        this.formData.tpe = this.formData.taxType != null ? this.formData.taxType.TaxType : null
        //= this.formData.taxType.TaxType 

        this.formData.frc = this.formData.frc?.value
        this.formData.dom = this.formData.dom?.value
        
        this.formData.exnb = this.formData.exnb?.value

        if(this.formData.categoryException != null && this.formData.categoryException != "undefined"){
          this.formData.cat= this.formData.categoryException;

        }else{
            if(this.formData.categoryDefinitions != null && this.formData.categoryDefinitionsn != "undefined")
            {
               this.formData.cat= this.formData.categoryDefinitions;
            }
        }
        
        const response = await this.exemptionAdded(this.formData)

        if (response.data.success) {
            
            window.toastr['success'](           
            this.$tc('avalara.exemption_added_succesfully')
          )
          window.hub.$emit('exemption_added_modal_avalara', response.data)
          this.$refs.baseModalExceptionAvalara.close()
        } else {
          window.toastr['error'](response.data.message)
        }

        this.isLoading = false

      } catch (e) {
      //  console.log(e)
        window.toastr['error'](e.message)
      } finally {
        this.clearFormData()
        this.$v.formData.$reset()
        this.exemptionTypeSelect = {
          value: 1,
          text: 'Category exemption',
        },
        this.isLoading = false
      }
    },
    clearFormData() {
      this.formData = {
        scp_selected: [],
        categoryException: null,
        categoryDefinitions: null,
        taxType: null,
        frc: null,
        dom: null,
        scp: null,
        exnb: null,
        user_id: null,
        avalara_locations_id: null,
      }
    },
    
  },
}
</script>
