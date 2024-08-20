<template>
  <form action="" @submit.prevent="submitAvalaraCategoryExemption">
    <!-- baseloader -->
    <base-loader v-if="isLoading" />

    <div class="p-6 grid md:grid-cols-2 gap-4 mb-1">
      <div>
        <p class="mb-1 text-sm font-bold">
          {{ $t('settings.company_info.country') }}
        </p>
        <p class="text-sm">
          {{ dataModal.country ? dataModal.country.name : '' }}
        </p>
      </div>

      <div>
        <p class="mb-1 text-sm font-bold">
          {{ $t('settings.company_info.state') }}
        </p>
        <p class="text-sm">
          {{ dataModal.state ? dataModal.state.name : '' }}
        </p>
      </div>

      <div>
        <p class="mb-1 text-sm font-bold">
          {{ $t('settings.company_info.city') }}
        </p>
        <p class="text-sm">
          {{ dataModal.city || '' }}
        </p>
      </div>

      <div>
        <p class="mb-1 text-sm font-bold">
          {{ $t('avalara.pcode') }}
        </p>
        <p class="text-sm">
          {{ dataModal.pcode || '' }}
        </p>
      </div>
    </div>

    <div class="sm:p-6 grid md:grid-cols-2 gap-4 mb-1">
      <sw-input-group
        :label="$tc('avalara.exemption_type')"
        class="mt-2"
        required
      >
        <sw-select
          v-model="exemptionTypeSelect"
          :options="exemptionTypeOptions"
          :show-labels="false"
          :allow-empty="false"
          class="mt-2"
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
          @change="selectCategoryDefinition()"
          @input="$v.formData.categoryDefinitions.$touch()"
          :invalid="$v.formData.categoryDefinitions.$error"
        />
      </sw-input-group>

      <sw-input-group
        :label="$tc('avalara.tax_type')"
        class="mt-2"
        required
        :error="taxTypeError"
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
          v-model="formData.scp"
          :options="scpOptions"
          :show-labels="false"
          :allow-empty="true"
          class="mt-2"
          :placeholder="$t('avalara.exemption_scope')"
          label="text"
          track-by="value"
          @input="$v.formData.scp.$touch()"
          :invalid="$v.formData.scp.$error"
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
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required } = require('vuelidate/lib/validators')
export default {
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
        categoryException: null,
        categoryDefinitions: null,
        taxType: null,
        frc: null,
        dom: null,
        scp: null,
        exnb: null,
        user_id: null,
        avalara_locations_id: null,
      },
      dataModal: {},
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),
    ...mapGetters(['countries']),
    taxTypeOptionsFilter() {
      return this.taxTypeOptions.filter(
        (e) => e.CategoryType == this.formData.categoryDefinitions?.id
      )
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
      if (!this.$v.formData.taxType.$error) {
        return ''
      }
      if (!this.$v.formData.taxType.required) {
        return this.$tc('validation.required')
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
    scpError() {
      if (!this.$v.formData.scp.$error) {
        return ''
      }
      if (!this.$v.formData.scp.required) {
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
        required,
      },
      categoryDefinitions: {
        required,
      },
      taxType: {
        required,
      },
      frc: {
        required,
      },
      dom: {
        required,
      },
      scp: {
        required,
      },
      exnb: {
        required,
      },
    },
  },
  watch: {
    modalDataID(val) {
      if (val) {
        this.isEdit = true
        this.setData()
      } else {
        this.isEdit = false
      }
    },
    modalActive(val) {
      if (!this.modalActive) {
        this.resetFormData()
      }
    },
  },

  mounted() {
    // this.$refs.name.focus = true
    if (this.modalDataID) {
      this.isEdit = true
    }
    this.setData()
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
        if (avalaraDefault.data?.success) {
          const getTaxTypes = await this.getTaxTypes(
            avalaraDefault.data.data.id
          )
          this.taxTypeOptions = getTaxTypes.data.data
        }
      } catch (e) {
       // console.log(e)
      } finally {
        this.isLoading = false
      }
    },
    selectCategoryDefinition() {
      this.formData.taxType = null
    },
    async setData() {
      this.dataModal = this.modalData.billing
      this.formData.user_id = this.modalData.userId
      this.formData.avalara_locations_id = this.modalData.avalara_location_id
    },
    async submitAvalaraCategoryExemption() {
      try {
        this.$v.formData.$touch()
        this.isLoading = true

        this.formData.categoryException = this.formData.categoryException?.id
        this.formData.categoryDefinitions =
          this.formData.categoryDefinitions?.id
        this.formData.taxType = this.formData.taxType?.value
        this.formData.frc = this.formData.frc?.value
        this.formData.dom = this.formData.dom?.value
        this.formData.scp = this.formData.scp?.value
        this.formData.exnb = this.formData.exnb?.value

        //console.log(this.formData)
        const response = await this.exemptionAdded(this.formData)
        //console.log(response)
        if (response.data.success) {
          window.toastr['success'](
            this.$tc('avalara.exemption_added_succesfully')
          )
          window.hub.$emit('exemption_added', response.data)
          this.closeAvalaraLocationsModal()
        } else {
          window.toastr['error'](response.data.message)
        }
      } catch (e) {
       // console.log(e)
        window.toastr['error'](e.message)
      } finally {
        this.isLoading = false
      }
    },
    clearFormData() {
      this.formData = {
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
    closeAvalaraLocationsModal() {
      this.clearFormData()
      this.closeModal()
    },
  },
}
</script>
