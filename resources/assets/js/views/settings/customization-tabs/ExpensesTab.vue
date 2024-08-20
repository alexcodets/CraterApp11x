<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updateExpensesSetting">
      <sw-input-group :label="$t('settings.customization.expenses.expense_template_prefix')"
        :error="ExpenseTemplatePrefixError">
        <sw-input
          class="input-expand"
          v-model="expenses.expense_template_prefix" :invalid="$v.expenses.expense_template_prefix.$error"
          style="max-width: 30%" @input="$v.expenses.expense_template_prefix.$touch()"
          @keyup="changeToUppercase('EXPENSE_TEMPLATE')" />
      </sw-input-group>
      <div class="flex mt-3 mb-4">
        <div class="relative w-12">
          <sw-switch v-model="expenses.expenses_template_prefix_general" class="absolute" style="top: -20px" />
        </div>
        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('settings.customization.items.apply_general_prefix') }}
          </p>
        </div>
      </div>
      <sw-input-group :label="$t('settings.customization.expenses.expense_prefix')" :error="ExpensePrefixError">
        <sw-input v-model="expenses.expense_prefix" :invalid="$v.expenses.expense_prefix.$error" style="max-width: 30%"
          @input="$v.expenses.expense_prefix.$touch()" @keyup="changeToUppercase('EXPENSE')" />
      </sw-input-group>

      <div class="flex mt-3 mb-4">
        <div class="relative w-12">
          <sw-switch v-model="expenses.expenses_prefix_general" class="absolute" style="top: -20px" />
        </div>
        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('settings.customization.items.apply_general_prefix') }}
          </p>
        </div>
      </div>

      <sw-divider class="mt-6 mb-8" />

      <sw-input-group :label="$t('settings.customization.expenses.provider_prefix')" :error="ProviderPrefixError">
        <sw-input v-model="expenses.prov_prefix" :invalid="$v.expenses.prov_prefix.$error" style="max-width: 30%"
          @input="$v.expenses.prov_prefix.$touch()" @keyup="changeToUppercase('PROVIDER')" />
      </sw-input-group>

      <div class="flex mt-3 mb-4">
        <div class="relative w-12">
          <sw-switch v-model="expenses.provider_prefix_general" class="absolute" style="top: -20px" />
        </div>
        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('settings.customization.items.apply_general_prefix') }}
          </p>
        </div>
      </div>
      <sw-divider class="mt-6 mb-4" />

      <div class="grid gap-6 mt-5 mb-4 sm:grid-col-1 md:grid-cols-2">
        <sw-input-group :label="$tc('settings.customization.expenses.warning_days_before_due_date')">
          <sw-select v-model="expenses.select_days_before_due_date" :options="days_before_due_date" label="name" track-by="id" :searchable="true" 
              class="mt-2"
            />
        </sw-input-group>
      </div>

      <sw-button :loading="isLoading" :disabled="isLoading" variant="primary" type="submit" class="mt-4"
        v-if="permission.update">
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>

    <sw-divider class="mt-6 mb-4" />

    <!--<h1>Expense Notifications</h1>-->

    <form action="" class="mt-4" @submit.prevent="updateExpenseNotifications">
      <sw-input-group :label="$tc('settings.customization.expenses.expense_bcc_email')" class="md:col-span- mt-2" :error="emailError">
        <sw-input style="max-width: 30%" :invalid="$v.email.$error" v-model="email" type="text" name="email"
          @input="$v.email.$touch()" />
      </sw-input-group>

      <sw-input-group class="mt-4" :label="$tc('settings.customization.expenses.expense_subject')">
        <base-custom-input v-model="expense.expense_mail_subject" :fields="InvoiceMailFields" />
      </sw-input-group>

      <sw-input-group :label="$tc('settings.customization.expenses.expense_body')" class="mt-6 mb-4">
        <base-custom-input v-model="expense.expense_mail_body" :fields="InvoiceMailFields" class="mt-2" />
      </sw-input-group>

      <h3 class="mb-5 text-lg font-medium text-black">{{ $t('settings.customization.expenses.expense_notification') }}</h3>

      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch v-model="allow_expense_notifications" class="absolute" style="top: -18px"
            @change="(option) => slideCardExpirationDate(option)" />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.customization.expenses.allow_expense_notification') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{ $t('settings.customization.expenses.enable_expense_notification') }}
            
          </p>
        </div>
      </div>

      <sw-input-group :label="$tc('settings.customization.expenses.time_expense_job')" :error="expenseNotificationsRun" class="mb-4"
        required>
        <base-time-picker v-model="expense_notifications_run" :invalid="$v.expense_notifications_run.$error"
          :calendar-button="true" style="max-width: 30%" :placeholder="'HH:mm'" calendar-button-icon="calendar"
          @input="$v.expense_notifications_run.$touch()" />
      </sw-input-group>

      <sw-button :loading="isLoading" :disabled="isLoading" variant="primary" type="submit" class="mt-4">
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>

    <sw-divider class="mt-6 mb-8" />

    <div class="flex">
      <div class="relative w-12">
        <sw-switch v-model="expenseAutogenerate" class="absolute" style="top: -20px" @change="setExpenseSetting" />
      </div>

      <div class="ml-4">
        <p class="p-0 mb-1 text-base leading-snug text-black">
          {{
            $t('settings.customization.expenses.autogenerate_expense_number')
          }}
        </p>

        <p class="p-0 m-0 text-xs leading-tight text-gray-500" style="max-width: 480px">
          {{
            $t('settings.customization.expenses.expense_setting_description')
          }}
        </p>
      </div>
    </div>
    <div class="flex">
      <div class="relative w-12">
        <sw-switch v-model="expenseTemplateAutogenerate" class="absolute" style="top: -20px"
          @change="setExpenseTemplateSetting" />
      </div>

      <div class="ml-4">
        <p class="p-0 mb-1 text-base leading-snug text-black">
          {{
            $t('settings.customization.expenses.autogenerate_expense_template_number')
          }}
        </p>

        <p class="p-0 m-0 text-xs leading-tight text-gray-500" style="max-width: 480px">
          {{
            $t('settings.customization.expenses.expense_template_setting_description')
          }}
        </p>
      </div>
    </div>

    <sw-divider class="mt-6 mb-8" />
    <!-- switch recurring expenses -->
    <form action="" class="mt-6" @submit.prevent="updateScheduling">
      <h3 class="mb-5 text-lg font-medium text-black">
        {{ $t('settings.customization.expenses.recurring_expenses') }}
      </h3>

      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch v-model="job_expense_template_enable" class="absolute" style="top: -18px"
            @change="(option) => slideRecurringExpenseDate(option)" />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.customization.expenses.allow_recurring_date_job') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{ $t('settings.customization.expenses.recurring_date_job_desc') }}
          </p>
        </div>
      </div>

      <sw-input-group :label="
        $t('settings.customization.expenses.time_run_recurring_expenses')
      " class="mb-4" required>
        <base-time-picker v-model="job_expense_template_time_run" :calendar-button="true" style="max-width: 30%"
          :placeholder="'HH:mm'" calendar-button-icon="calendar" />
      </sw-input-group>

      <sw-button :loading="isLoading" :disabled="isLoading" variant="primary" type="submit" class="mt-4 margin-buttonl">
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('settings.customization.expenses.save_recurring') }}
      </sw-button>
      <sw-divider class="mt-6 mb-4" />
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, maxLength, alpha, requiredIf, email } = require('vuelidate/lib/validators')

export default {
  props: {
    settings: {
      type: Object,
      require: true,
      default: false,
    },
    permission: {
      type: Object,
      require: true,
    },
  },

  data() {
    return {
      expenseAutogenerate: false,
      expenseTemplateAutogenerate: false,
      expenses: {
        expense_prefix: null,
        expense_template_prefix: null,
        prov_prefix: null,
        expenses_prefix_general: null,
        expenses_template_prefix_general: null,
        provider_prefix_general: null,
        select_days_before_due_date: null,
      },
      isLoading: false,
      //
      email: null,
      expense: {
        expense_mail_body: null,
        expense_mail_subject: null,
      },
      InvoiceMailFields: [
        'company',
        'expenseCustom',
      ],
      allow_expense_notifications: false,
      isLoadingCardExpiration: false,
      expense_notifications_run: null,
      job_expense_template_enable: false,
      job_expense_template_time_run: null,
      days_before_due_date: [
        {id: '0', name:"0 days" },
        {id: '1', name:"1 days" },
        {id: '2', name:"2 days" },
        {id: '3', name:"3 days" },
        {id: '4', name:"4 days" },
        {id: '5', name:"5 days" },
        {id: '6', name:"6 days" },
        {id: '7', name: "7 days"}
      ]
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
    ExpenseTemplatePrefixError() {
      if (!this.$v.expenses.expense_template_prefix.$error) {
        return ''
      }

      if (!this.$v.expenses.expense_template_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.expenses.expense_template_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.expenses.expense_template_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },

    emailError() {
      if (!this.$v.email.$error) {
        return ''
      }

      if (!this.$v.email.email) {
        return this.$tc('validation.email_incorrect')
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

    expenseNotificationsRun() {
      if (!this.$v.expense_notifications_run.$error) {
        return ''
      }
      if (!this.$v.expense_notifications_run.required) {
        return this.$t('validation.required')
      }
    },

    timeJobError() {
      if (!this.$v.time_run_renewal_date_job.$error) {
        return ''
      }
      if (!this.$v.time_run_renewal_date_job.required) {
        return this.$t('validation.required')
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
      expense_template_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
      prov_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
      job_expense_template_time_run: {
        required: requiredIf(function () {
          return this.job_expense_template_enable
        }),
      },
    },
    email: {
      required,
      email,
    },
    expense_notifications_run: {
      required,
    },
  },

  watch: {
    settings(val) {

      this.expenses.expense_prefix = val ? val.expense_prefix : ''
      this.expenses.expense_template_prefix = val ? val.expense_template_prefix : ''

      this.expenses.prov_prefix = val ? val.prov_prefix : ''

      this.expense_auto_generate = val ? val.expense_auto_generate : ''
      this.expense_template_auto_generate = val ? val.expense_template_auto_generate : ''
      this.job_expense_template_time_run = val ? val.job_expense_template_time_run : null

      this.job_expense_template_enable = val ? val.job_expense_template_enable : false

      if (this.expense_auto_generate === 'YES') {
        this.expenseAutogenerate = true
      } else {
        this.expenseAutogenerate = false
      }
      if (this.expense_template_auto_generate === 'YES') {
        this.expenseTemplateAutogenerate = true
      } else {
        this.expenseTemplateAutogenerate = false
      }

      // Expense Notifications
      this.email = val.job_expense_pending_mail_bbc
      this.expense.expense_mail_subject = val.job_expense_pending_mail_subject
      this.expense.expense_mail_body = val.job_expense_pending_mail_body
      this.allow_expense_notifications = val.job_expense_pending_enable
        ? true
        : false
      this.expense_notifications_run = val.job_expense_pending_time_run
      //

   
      if(val.warning_before_due_date){
   
        this.expenses.select_days_before_due_date =
        this.days_before_due_date.find((bscl) => {
         
          return bscl.id == val.warning_before_due_date
        })

      }
    },
  },

  methods: {
    ...mapActions('company', ['updateCompanySettings']),
    ...mapActions('expense', ['setPrefix', 'setPrefixTemplate', 'setDaysBeforeDueDate']),
    ...mapActions('provider', ['setPrefixpro']),

    changeToUppercase(currentTab) {
      if (currentTab === 'EXPENSE') {
        this.expenses.expense_prefix =
          this.expenses.expense_prefix.toUpperCase()
        return true
      }
      if (currentTab === 'EXPENSE_TEMPLATE') {
        this.expenses.expense_template_prefix =
          this.expenses.expense_template_prefix.toUpperCase()
        return true
      }
      if (currentTab === 'PROVIDER') {
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
    async setExpenseTemplateSetting() {
      let data = {
        settings: {
          expense_template_auto_generate: this.expenseTemplateAutogenerate ? 'YES' : 'NO',
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
          warning_before_due_date: this.expenses.select_days_before_due_date.id,
          expense_prefix: this.expenses.expense_prefix,
          expense_template_prefix: this.expenses.expense_template_prefix,
          prov_prefix: this.expenses.prov_prefix,
          provider_prefix_general: this.expenses.provider_prefix_general
        },
      }

      if (this.updateSetting(data)) {
        window.toastr['success'](
          this.$t(
            'general.setting_updated'
          )
        )
      }
    },

    async updateSetting(data) {
      this.isLoading = true
      let res = await this.updateCompanySettings(data)
      if (this.expenses.expenses_template_prefix_general) {
        let res = await this.setPrefixTemplate(this.expenses)
      }
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

    async updateExpenseNotifications() {

      this.$v.expense_notifications_run.$touch()
      if (this.$v.expense_notifications_run.$error) {
        return true
      }

      let data = {
        settings: {
          job_expense_pending_mail_bbc: this.email,
          job_expense_pending_mail_subject: this.expense.expense_mail_subject,
          job_expense_pending_mail_body: this.expense.expense_mail_body,
          job_expense_pending_enable: this.allow_expense_notifications,
          job_expense_pending_time_run: this.expense_notifications_run,
        },
      }

      let response = await this.updateCompanySettings(data)
      if (response.data.success) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }

    },

    slideRecurringExpenseDate(val) {
      if (!val) {
        this.job_expense_template_time_run = null
      }
    },
    async updateScheduling() {
      // this.$v.job_expense_template_time_run.$touch()
      // if (this.$v.job_expense_template_time_run.$error) {
      //   return true
      // }
      let data = {
        settings: {
          job_expense_template_enable: this.job_expense_template_enable,
          job_expense_template_time_run: this.job_expense_template_time_run,
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data.success) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }

    },

    slideCardExpirationDate(val) {
      if (!val) {
        this.expense_notifications_run = null
      }
    },


  },
}
</script>
