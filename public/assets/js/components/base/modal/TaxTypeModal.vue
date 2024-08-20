<template>
  <div class="tax-type-modal">
    <form action="" @submit.prevent="submitTaxTypeData">
      <div class="p-8 sm:p-6">
        <sw-input-group
          :label="$t('tax_types.name')"
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

        <sw-input-group
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
            class="relative w-full focus:border focus:border-solid focus:border-primary"
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
            class="relative w-full focus:border focus:border-solid focus:border-primary"
            track-by="tax_group_id"
            label="tax_group_name"
          />
        </sw-input-group>
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-solid border--200 border-modal-bg"
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
const {
  required,
  minLength,
  between,
  maxLength,
} = require('vuelidate/lib/validators')
export default {
  data() {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        id: null,
        name: null,
        percent: 0,
        description: null,
        compound_tax: false,
        collective_tax: 0,
        tax_groups: [],
      },
      defaultInput: {
        decimal: '.',
        thousands: ',',
        prefix: '% ',
        precision: 2,
        masked: false,
      },
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
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    }
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('taxType', ['addTaxType', 'updateTaxType', 'fetchTaxType']),
    ...mapActions('taxGroups', ['fetchTaxGroups']),
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
    async loadData() {
      if (!this.isEdit) {
        this.fetchTaxGroups({ limit: 'all' })
      }
    },
    async submitTaxTypeData() {
      console.log('Entrando en submitTaxTypeData')
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        console.log('Formulario inválido')
        return true
      }
      this.isLoading = true
      let response
      if (!this.isEdit) {
        response = await this.addTaxType(this.formData)
      } else {
        response = await this.updateTaxType(this.formData)
      }
      if (response.data) {
        if (!this.isEdit) {
          console.log('Tipo de impuesto creado exitosamente')
          window.toastr['success'](
            this.$t('settings.tax_types.created_message')
          )
        } else {
          console.log('Tipo de impuesto actualizado exitosamente')
          window.toastr['success'](
            this.$t('settings.tax_types.updated_message')
          )
        }

        if (this.props) {
          if (this.props.emitName) {
            console.log('Emitiendo evento personalizado')
            window.hub.$emit(this.props.emitName, response.data.taxType)
          } else {
            console.log("Emitiendo evento 'newTax'")
            window.hub.$emit('newTax', response.data.taxType)
          }
        }

        this.refreshData ? this.refreshData() : ''
        this.closeTaxModal()
        this.isLoading = false
        return true
      }
      console.log('Error al procesar la respuesta del servidor')
      window.toastr['error'](response.data.error)
      this.isLoading = false
    },
    async setData() {
      this.formData = {
        id: this.modalData.id,
        name: this.modalData.name,
        percent: this.modalData.percent,
        description: this.modalData.description,
        compound_tax: this.modalData.compound_tax ? true : false,
        tax_groups: this.modalData.tax_groups,
      }
      this.formData.tax_groups = this.modalData.tax_groups.map((tax) => {
        return { ...tax, tax_group_id: tax.id, tax_group_name: tax.name }
      })
    },
    closeTaxModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>
