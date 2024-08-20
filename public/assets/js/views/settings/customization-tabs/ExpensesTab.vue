<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updateExpensesSetting">
      <sw-input-group
        :label="$t('settings.customization.expenses.expense_prefix')"
        :error="ExpensePrefixError">
        <sw-input
          v-model="expenses.expense_prefix"
          :invalid="$v.expenses.expense_prefix.$error"
          style="max-width: 30%"
          @input="$v.expenses.expense_prefix.$touch()"
          @keyup="changeToUppercase('EXPENSE')" />
      </sw-input-group>
      
        <div class="flex mt-3 mb-4">
            <div class="relative w-12">
                <sw-switch
                v-model="expenses.expenses_prefix_general"
                class="absolute"
                style="top: -20px"/>
            </div>
            <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                     {{ $t('settings.customization.items.apply_general_prefix') }}               </p>
            </div>
        </div>

        <sw-divider class="mt-6 mb-8" />

              <sw-input-group
        :label="$t('settings.customization.expenses.provider_prefix')"
        :error="ProviderPrefixError">
        <sw-input
          v-model="expenses.prov_prefix"
          :invalid="$v.expenses.prov_prefix.$error"
          style="max-width: 30%"
          @input="$v.expenses.prov_prefix.$touch()"
          @keyup="changeToUppercase('PROVIDER')" />
      </sw-input-group>
      
        <div class="flex mt-3 mb-4">
            <div class="relative w-12">
                <sw-switch
                v-model="expenses.provider_prefix_general"
                class="absolute"
                style="top: -20px"/>
            </div>
            <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                   {{ $t('settings.customization.items.apply_general_prefix') }}              </p>
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

    <div class="flex">
      <div class="relative w-12">
        <sw-switch
          v-model="expenseAutogenerate"
          class="absolute"
          style="top: -20px"
          @change="setExpenseSetting"
        />
      </div>

      <div class="ml-4">
        <p class="p-0 mb-1 text-base leading-snug text-black">
          {{
            $t('settings.customization.expenses.autogenerate_expense_number')
          }}
        </p>

        <p
          class="p-0 m-0 text-xs leading-tight text-gray-500"
          style="max-width: 480px"
        >
          {{
            $t('settings.customization.expenses.expense_setting_description')
          }}
        </p>
      </div>
    </div>
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
      expenseAutogenerate: false,
      expenses: {
        expense_prefix: null,
        prov_prefix:null,
        expenses_prefix_general: null,
        provider_prefix_general:null
      },
      isLoading: false,
    }
  },

  computed: {
    ExpensePrefixError() {
      if (!this.$v.expenses.expense_prefix.$error) {
        return ''
      }

      if (!this.$v.expenses.expense_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.expenses.expense_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.expenses.expense_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },
    ProviderPrefixError() {
      if (!this.$v.expenses.prov_prefix.$error) {
        return ''
      }

      if (!this.$v.expenses.prov_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.expenses.prov_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.expenses.prov_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },
  },

  validations: {
    expenses: {
      expense_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
       prov_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
    },
  },

  watch: {
    settings(val) {
      this.expenses.expense_prefix = val ? val.expense_prefix : ''   
      
      this.expenses.prov_prefix = val ? val.prov_prefix : ''

      this.expense_auto_generate = val ? val.expense_auto_generate : ''  

      if (this.expense_auto_generate === 'YES') {
        this.expenseAutogenerate = true
      } else {
        this.expenseAutogenerate = false
      }
    },
  },

  methods: {
    ...mapActions('company', ['updateCompanySettings']),
    ...mapActions('expense', ['setPrefix']),
    ...mapActions('provider', ['setPrefixpro']),

    changeToUppercase(currentTab) {
      if (currentTab === 'EXPENSE') {
        this.expenses.expense_prefix = this.expenses.expense_prefix.toUpperCase()
        return true
      }
      if(currentTab === 'PROVIDER'){
        this.expenses.prov_prefix = this.expenses.prov_prefix.toUpperCase()
        return true
      }
    },


    async setExpenseSetting() {
      let data = {
        settings: {
          expense_auto_generate: this.expenseAutogenerate ? 'YES' : 'NO',
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },

    async updateExpensesSetting() {
      this.$v.expenses.$touch()

      if (this.$v.expenses.$invalid) {
        return false
      }

      let data = {
        settings: {
          expense_prefix: this.expenses.expense_prefix,
          prov_prefix: this.expenses.prov_prefix,
          provider_prefix_general: this.expenses.provider_prefix_general,
        },
      }

      if (this.updateSetting(data)) {
        window.toastr['success'](
          this.$t('settings.customization.expenses.expense_setting_updated_general')
        )
      }
    },

    async updateSetting(data) {
      this.isLoading = true
      let res = await this.updateCompanySettings(data)
     
      if (this.expenses.expenses_prefix_general) {
        let res = await this.setPrefix(this.expenses)
      }

      if (this.expenses.provider_prefix_general) {
 

         let res = await this.setPrefixpro(this.expenses)
       }

      if (res.data.success) {
        this.isLoading = false
        return true
      }

      return false
    },
  },
}
</script>