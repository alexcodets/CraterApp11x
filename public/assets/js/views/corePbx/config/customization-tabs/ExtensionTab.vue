<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updateSetting">
      <sw-input-group
        :label="$t('corePbx.customization.extension_prefix')"
        :error="ExpensePrefixError">
        <sw-input
          v-model="expenses.extension_pbx_prefix"
          :invalid="$v.expenses.extension_pbx_prefix.$error"
          style="max-width: 30%"
          @input="$v.expenses.extension_pbx_prefix.$touch()"
          @keyup="changeToUppercase('EXPENSE')" />
      </sw-input-group>
      
        <div class="flex mt-3 mb-4">
            <div class="relative w-12">
                <sw-switch
                v-model="expenses.extension_prefix_general"
                class="absolute"
                style="top: -20px"/>
            </div>
            <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                     {{ $t('settings.customization.items.apply_general_prefix') }}                </p>
            </div>
        </div>

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        type="submit"
        class="mt-4">
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>
    
    <sw-divider class="mt-6 mb-8" />

  
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, maxLength, alpha } = require('vuelidate/lib/validators')

export default {
  props: {
    settings: {
      type: Object,
      require: true,
      default: false,
    },
  },

  data() {
    return {
      expenses: {
        extension_pbx_prefix: null,
        extension_prefix_general: false,
      },
      isLoading: false,
    }
  },

  computed: {
    ExpensePrefixError() {
      if (!this.$v.expenses.extension_pbx_prefix.$error) {
        return ''
      }

      if (!this.$v.expenses.extension_pbx_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.expenses.extension_pbx_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.expenses.extension_pbx_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },
  },

  validations: {
    expenses: {
      extension_pbx_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
    },
  },

  watch: {
    settings(val) {
      this.expenses.extension_pbx_prefix = val ? val.extension_pbx_prefix : ''    
    },
  },

  methods: {
    ...mapActions('company', ['updateCompanySettings']),

    changeToUppercase(currentTab) {
      if (currentTab === 'EXPENSE') {
        this.expenses.extension_pbx_prefix = this.expenses.extension_pbx_prefix.toUpperCase()
        return true
      }
    },
    async updateSetting(){
      try {
        this.$v.expenses.$touch()
        if (this.$v.expenses.$invalid) return false

        this.isLoading = true
        const data = {
          settings: {
            extension_pbx_prefix: this.expenses.extension_pbx_prefix,
            extension_prefix_general:this.expenses.extension_prefix_general,
          },
      }
        await this.updateCompanySettings(data)
        window.toastr['success'](
          this.$t('corePbx.customization.extension_prefix_updated')
        )

      } catch (error) {
        window.toastr['error'](
          this.$t('corePbx.customization.extension_prefix_update_error')
        )
      } finally {
        this.isLoading = false
      }
    }
  },
}
</script>