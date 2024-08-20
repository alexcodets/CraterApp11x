<template>
  <div class="custom-did">
    <form action="" @submit.prevent="submitData">
      <div class="px-8 py-8 sm:p-6">
        <sw-input-group
          :label="$t('didFree.item.prefijo')"
          :error="prefixError"
          class="mb-4"
          variant="horizontal"
          required
        >
          <sw-input
            v-model="formData.prefijo"
            :placeholder="$t('didFree.item.prefijo')"
            :invalid="$v.formData.prefijo.$error"
            type="text"
            name="prefijo"
            @input="$v.formData.prefijo.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('corePbx.didFree.status')"
          class="mb-4"
          variant="horizontal"
        >
          <sw-select
            v-model="formData.statu"
            :options="statuses"
            :searchable="true"
            :show-labels="false"
            label="name"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('corePbx.didFree.category')"
          class="mb-4"
          variant="horizontal"
          :error="categoryError"
          required
        >
          <sw-select
            :invalid="$v.formData.category.$error"
            v-model="formData.category"
            :options="categoriesTollFree"
            :searchable="true"
            :show-labels="false"
            :max-height="200"
            :placeholder="$t('corePbx.didFree.select_a_category')"
            label="name"
            @input="$v.formData.category.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('corePbx.custom_did_groups.price')"
          class="mb-4"
          variant="horizontal"
          :error="rateError"
          required
        >
          <sw-money
            v-model="formData.rate_per_minutes"
            :currency="defaultCurrencyForInput"
            :invalid="$v.formData.rate_per_minutes.$error"
            class="relative w-full focus:border focus:border-solid focus:border-primary"
            @input="$v.formData.rate_per_minutes.$touch()"
          />
        </sw-input-group>
      </div>

      <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
        <sw-button
          class="mr-3"
          variant="primary-outline"
          type="button"
          @click="closeCustomModal"
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
      isLoading: false,
      formData: {
        prefijo: null,
        statu: { name: 'Active', value: 'A' },
        category: null,
        rate_per_minutes: 0,
        toll_free_category_id: null
      },
      statuses: [
        { name: 'Active', value: 'A' },
        { name: 'Inactive', value: 'I' },
      ],
    }
  },
  validations: {
    formData: {
      prefijo: {
        maxLength: maxLength(32),
        required
      },
      category: {
        required
      },
      rate_per_minutes: {
        required,
        minValue: minValue(0.00001),
        maxLength: maxLength(20),
      },
    }
  },
  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),
    ...mapGetters('categoriesTollF', ['categoriesTollFree']),

    prefixError() {
      if (!this.$v.formData.prefijo.$error) {
        return ''
      }
      if (!this.$v.formData.prefijo.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.prefijo.maxLength) {
        return this.$tc(
          'validation.prefijo_max_length_character',
          this.$v.formData.prefijo.$params.maxLength,
          { count: 32 }
        )
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

  },
  mounted() {
    this.$v.formData.$reset()
    this.fetchCategories({ limit: 'all' })
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('categoriesTollF', ['fetchCategories']),
    ...mapActions('didtollfree', ['addDIDTOLLFREE']),

    resetFormData() {
      this.formData = {
        prefijo: null,
        statu: { name: 'Active', value: 'A' },
        category: null,
        rate_per_minute: 0,
        toll_free_category_id: null
      }
      this.$v.$reset()
    },

    closeCustomModal() {
      this.resetFormData()
      this.closeModal()
      this.resetModalData()
    },

    async submitData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.formData.toll_free_category_id = this.formData.category.id

      try {
        this.isLoading = true
        let response = await this.addDIDTOLLFREE(this.formData)

        if (response.data.success) {
          window.toastr['success'](this.$t('corePbx.didFree.created_message'))
          window.hub.$emit('newCustomDid', response.data.ProfileDidTollFree)

          this.isLoading = false
          this.resetModalData()
          this.resetFormData()
          this.closeModal()
          return true
        }

        window.toastr['error'](response.data.error)

      } catch (err) {
        this.isLoading = false;
      }

    }
  }
}
</script>

<style scoped>

</style>