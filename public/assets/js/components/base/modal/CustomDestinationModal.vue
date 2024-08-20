<template>
  <div class="custom-destination">
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
            focus
            type="text"
            name="prefijo"
            pattern="[0-9*|A-Za-z *|#|+]+"
            title="Numbers and special characters (* # +)"
            tabindex="1"
            placer
            @input="$v.formData.prefijo.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('settings.company_info.country')"
          class="mb-4"
          variant="horizontal"
        >
          <sw-select
            v-model="formData.country_id"
            :options="countries"
            :searchable="true"
            :show-labels="false"
            :max-height="200"
            label="name"
            track-by="id"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('didFree.item.name')"
          :error="nameError"
          class="mb-4"
          variant="horizontal"
          required
        >
          <sw-input
            v-model.trim="formData.name"
            :invalid="$v.formData.name.$error"
            :placeholder="$t('didFree.item.name')"
            type="text"
            name="name"
            @input="$v.formData.name.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('corePbx.prefix_groups.status')"
          class="mb-4"
          variant="horizontal"
        >
          <sw-select
            v-model="formData.status"
            :options="statuses"
            :searchable="true"
            :show-labels="false"
            class="mt-2"
            label="name"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('corePbx.prefix_groups.category')"
          class="mb-4"
          variant="horizontal"
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

<!--        <sw-input-group
          :label="$t('didFree.item.custom_destination_group')"
          class="mb-4"
          variant="horizontal"
          :error="customError"
          required
        >
          <sw-select
            :invalid="$v.formData.prefixrate_groups_id.$error"
            v-model="formData.prefixrate_groups_id"
            :options="getPrefixGroups"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :max-height="200"
            :placeholder="$t('didFree.item.custom_destination_group')"
            label="name"
            @input="$v.formData.name.$touch()"
          />
        </sw-input-group>-->

        <sw-input-group
          :label="$t('corePbx.internacional.rate_per_minutes')"
          class="mb-4"
          variant="horizontal"
          :error="rateError"
          required
        >
          <sw-input
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
      isLoading: false,
      formData: {
        prefijo: null,
        name: null,
        prefixrate_groups_id: null,
        country_id: null,
        status: { name: 'Active', value: 'A' },
        category: null,
        rate_per_minutes: 0,
      },
      statuses: [
        { name: 'Active', value: 'A' },
        { name: 'Inactive', value: 'I' },
      ],
      categories: [
        {  name: 'Toll Free', value: 'T' },
        {  name: 'International', value: 'I' },
        {  name: 'Custom', value: 'C' }
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
      /*prefixrate_groups_id:{
        required,
      },*/
      name: {
        required,
      },
      rate_per_minutes: {
        required,
        minValue: minValue(0.00001),
        maxLength: maxLength(20),
      },
    }
  },
  computed: {
    ...mapGetters(['countries']),
    ...mapGetters('company', ['defaultCurrencyForInput']),
    /*...mapGetters('prefixGroup', ['prefixGroups']),*/

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

    customError() {
      if (!this.$v.formData.prefixrate_groups_id.$error) {
        return ''
      }
      if (!this.$v.formData.prefixrate_groups_id.required) {
        return this.$tc('validation.required')
      }
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
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

    /*getPrefixGroups() {
      return this.prefixGroups.map((group) => {
        return {
          ...group,
        }
      })
    },*/
  },
  mounted() {
    this.$v.formData.$reset()
    /*this.fetchPrefixGroups({ limit: 'all' })*/
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    /*...mapActions('prefixGroup', ['fetchPrefixGroups']),*/
    ...mapActions('internacionalrate', ['addInternacional']),

    resetFormData() {
      this.formData = {
        prefijo: null,
        name: null,
        prefixrate_groups_id: null,
        country_id: null,
        status: { name: 'Active', value: 'A' },
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

      if (this.$v.$invalid) {
        return true
      }

      this.formData.country_id = this.formData.country_id.id
      /*this.formData.prefixrate_groups_id = this.formData.prefixrate_groups_id.id*/
      this.formData.status = this.formData.status.value
      this.formData.category = this.formData.category.value

      try {
        this.isLoading = true
        let response = await this.addInternacional(this.formData)

        if (response.data) {
          window.toastr['success'](response.data.success)
          window.hub.$emit('newPrefix', response.data.internacional)

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