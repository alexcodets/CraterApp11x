<template>
  <div class="item-modal">
    <form action="" @submit.prevent="submitExtensionData">
      <div class="px-8 py-8 sm:p-6">
        <sw-input-group
          :label="$t('corePbx.extensions.name')"
          :error="nameError"
          class="mb-4"
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
          :label="$t('items.description')"
          :error="descriptionError"
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
          :label="$t('corePbx.extensions.charge')"
          :error="ChargeError"
          class="mb-4"
          variant="horizontal"
          required
        >
          <sw-input
            ref="amount"
            :invalid="$v.formData.amount.$error"
            v-model="formData.amount"
            type="text"
            @input="$v.formData.amount.$touch()"
          />
        </sw-input-group>
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <sw-button
          class="mr-3"
          variant="primary-outline"
          type="button"
          @click="closeExtensionModal"
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
          {{ isEdit ? $t('general.update') : $t('general.save') }}
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
      isEdit: false,
      isLoading: false,
      tempData: null,
      taxes: [],
      formData: {
        name: null,
        amount: null,
        description: null,
      },
    }
  },

  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },
      amount: {
        required,
        minValue: minValue(0.1),
        maxLength: maxLength(100),
      },
      description: {
        maxLength: maxLength(255),
      },
    },
  },

  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),
    price: {
      get: function () {
        return this.formData.price / 100
      },
      set: function (newValue) {
        this.formData.price = Math.round(newValue * 100)
      },
    },

    ...mapGetters('modal', ['modalDataID', 'modalData']),
    ...mapGetters('item', ['getItemById', 'itemUnits']),
    ...mapGetters('taxType', ['taxTypes']),
    isTexPerItem() {
      return this.modalData.taxPerItem === 'YES'
    },

    getTaxTypes() {
      return this.taxTypes.map((tax) => {
        return { ...tax, tax_name: tax.name + ' (' + tax.percent + '%)' }
      })
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
    },

    ChargeError() {
      if (!this.$v.formData.amount.$error) {
        return ''
      }

      if (!this.$v.formData.amount.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.formData.amount.maxLength) {
        return this.$t('validation.price_maxlength')
      }

      if (!this.$v.formData.amount.minValue) {
        return this.$t('validation.price_minvalue')
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
  },

  watch: {
    modalDataID() {
      this.isEdit = true
      this.fetchEditData()
    },
  },

  created() {
    if (this.modalDataID) {
      this.isEdit = true
      this.fetchEditData()
    }

    if (this.isEdit) {
      this.loadEditData()
    }
  },

  mounted() {
    this.$v.formData.$reset()
    this.$refs.name.focus = true
    this.fetchExtensionUnits({ limit: 'all' })
  },

  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('extensions', [
      'addCharge',
      'updateCharge',
      'fetchExtensionUnits',
      'setExtension',
    ]),

    resetFormData() {
      this.formData = {
        name: null,
        amount: null,
        description: null,
        unit: null,
        id: null,
      }
      this.$v.$reset()
    },

    fetchEditData() {
      this.tempData = this.getItemById(this.modalDataID)
      if (this.tempData) {
        this.formData.name = this.tempData.name
        this.formData.price = this.tempData.price
        this.formData.description = this.tempData.description
        this.formData.unit = this.tempData.unit
        this.formData.id = this.tempData.id
      }
    },

    async submitExtensionData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true
      let response
      if (this.isEdit) {
        response = await this.updateCharge(this.formData)
      } else {
        let data = {
          ...this.formData,
        }
        response = await this.addCharge(data)
      }
      if (response.data) {
        window.toastr['success'](this.$tc('items.created_message'))
        this.setExtension(response.data.additional_charges)

        window.hub.$emit('newItem', response.data.additional_charges)
        this.isLoading = false
        this.resetModalData()
        this.resetFormData()
        this.closeModal()
        return true
      }
      window.toastr['error'](response.data.error)
    },

    closeExtensionModal() {
      this.resetFormData()
      this.closeModal()
      this.resetModalData()
    },
  },
}
</script>
