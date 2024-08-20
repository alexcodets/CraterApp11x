<template>
  <div class="relative">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card variant="setting-card">
      <template slot="header">
        <h6 class="sw-section-title">
          {{ $t('settings.notification.title') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.notification.description') }}
        </p>
      </template>
      <form action="" @submit.prevent="saveEmail()">
        <div class="grid-cols-2 col-span-1">
          <sw-input-group
            :label="$t('settings.notification.email')"
            :error="notificationEmailError"
            class="my-2"
            required
          >
            <sw-input
              :invalid="$v.notification_email.$error"
              v-model.trim="notification_email"
              :placeholder="$tc('settings.notification.please_enter_email')"
              type="text"
              name="notification_email"
              icon="envelope"
              @input="$v.notification_email.$touch()"
            />
          </sw-input-group>

          <sw-button
            :disabled="isLoading"
            :loading="isLoading"
            variant="primary"
            type="submit"
            class="my-6"
            v-if="permissionModule.update"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ $tc('settings.notification.save') }}
          </sw-button>
        </div>
      </form>

      <sw-divider class="mt-1 mb-6" />

      <div class="flex mt-3 mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="notify_invoice_viewed"
            class="absolute"
            style="top: -20px"
            @change="setInvoiceViewd"
          />
        </div>
        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('settings.notification.invoice_viewed') }}
          </p>
          <p
            class="p-0 m-0 text-xs leading-tight text-gray-500"
            style="max-width: 480px"
          >
            {{ $t('settings.notification.invoice_viewed_desc') }}
          </p>
        </div>
      </div>
      <div class="flex mb-2">
        <div class="relative w-12">
          <sw-switch
            v-model="notify_estimate_viewed"
            class="absolute"
            style="top: -20px"
            @change="setEstimateViewd"
          />
        </div>
        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('settings.notification.estimate_viewed') }}
          </p>
          <p
            class="p-0 m-0 text-xs leading-tight text-gray-500"
            style="max-width: 480px"
          >
            {{ $t('settings.notification.estimate_viewed_desc') }}
          </p>
        </div>
      </div>

    <form action="" class="mt-6" @submit.prevent="updateCustomerSetting">
             <sw-divider class="mb-5 md:mb-8" />

            <div class="grid grid-cols-5 gap-4 mb-8">
              <!-- lg:col-span-1 -->
                <h6 class="col-span-5 sw-section-title">
                    {{ $t('customers.notices') }}
                </h6>
              <!-- md:grid-cols-6 -->
                <div
                    class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 "
                >
                    <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="notices.invoice_notices_settings_send_cancellation"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.send_cancellation_notice') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="notices.invoice_notices_settings_send_payment"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.send_payment_notices') }}
                            </p>
                            <p class="p-0 m-0 text-xs leading-4 text-gray-500"
                               style="max-width: 480px"
                            >
                                {{ $t('customers.send_payment_notices_detail') }}
                            </p>
                        </div>
                    </div>

                    <sw-input-group
                        class="md:col-span-3"
                    >
                        <div
                            class="flex"
                            role="group"
                        >
                            <sw-input-group
                                :label="$t('customers.first_notice')"
                                :error="notificationNotice1Error"
                                style="width: 40%"
                            >
                                <div
                                    class="flex"
                                    role="group"
                                >
                                <!-- 
                                  
                              
                                 -->
                                    <sw-input
                                        v-model="notices.invoice_notices_settings_notice_1"
                                        :invalid="$v.notices.invoice_notices_settings_notice_1.$error"
                                        type="number"
                                        @input="$v.notices.invoice_notices_settings_notice_1.$touch()"
                                    />
                                    <div class="ml-2 pt-2">
                                        {{ $t('settings.notification.days') }}
                                    </div>
                                </div>
                            </sw-input-group>
                            <sw-input-group
                                :label="$t('customers.invoice_due_date')"
                                class="ml-4"
                            >
                                <div
                                    class="flex"
                                    role="group"
                                >
                                    <sw-radio
                                        v-model="notices.invoice_notices_settings_notice_1_type"
                                        :value="$t('customers.before')"
                                    />
                                    <div class="ml-2 pt-2">
                                        {{ $t('customers.before') }}
                                    </div>
                                    <sw-radio
                                        v-model="notices.invoice_notices_settings_notice_1_type"
                                        :value="$t('customers.after')"
                                        class="ml-4"
                                    />
                                    <div class="ml-2 pt-2">
                                        {{ $t('customers.after') }}
                                    </div>
                                </div>
                            </sw-input-group>
                        </div>
                    </sw-input-group>

                    <sw-input-group
                        class="md:col-span-3"
                    >
                        <div
                            class="flex"
                            role="group"
                        >
                            <sw-input-group
                                :label="$t('customers.second_notice')"
                                :error="notificationNotice2Error"
                                style="width: 40%"
                            >
                                <div
                                    class="flex"
                                    role="group"
                                >
                                    <sw-input
                                        v-model="notices.invoice_notices_settings_notice_2"
                                        :invalid="$v.notices.invoice_notices_settings_notice_2.$error"
                                        type="number"
                                        @input="$v.notices.invoice_notices_settings_notice_2.$touch()"
                                        
                                    />
                                    <div class="ml-2 pt-2">
                                      {{ $t('customers.days') }}
                                    </div>
                                </div>
                            </sw-input-group>
                            <sw-input-group
                                :label="$t('customers.invoice_due_date')"
                                class="ml-4"
                            >
                                <div
                                    class="flex"
                                    role="group"
                                >
                                    <sw-radio
                                        v-model="notices.invoice_notices_settings_notice_2_type"
                                        :value="$t('customers.before')"
                                    />
                                    <div class="ml-2 pt-2">
                                        {{ $t('customers.before') }}
                                    </div>
                                    <sw-radio
                                        v-model="notices.invoice_notices_settings_notice_2_type"
                                        :value="$t('customers.after')"
                                        class="ml-4"
                                    />
                                    <div class="ml-2 pt-2">
                                        {{ $t('customers.after') }}
                                    </div>
                                </div>
                            </sw-input-group>
                        </div>
                    </sw-input-group>

                    <sw-input-group
                        class="md:col-span-3"
                    >
                        <div
                            class="flex"
                            role="group"
                        >
                            <sw-input-group
                                :label="$t('customers.third_notice')"
                                :error="notificationNotice3Error"
                                style="width: 40%"
                            >
                                <div
                                    class="flex"
                                    role="group"
                                >
                                    <sw-input
                                        v-model="notices.invoice_notices_settings_notice_3"
                                        :invalid="$v.notices.invoice_notices_settings_notice_3.$error"
                                        type="number"
                                        @input="$v.notices.invoice_notices_settings_notice_3.$touch()"
                                    />
                                    <div class="ml-2 pt-2">
                                      {{ $t('customers.days') }}
                                    </div>
                                </div>
                            </sw-input-group>
                            <sw-input-group
                                :label="$t('customers.invoice_due_date')"
                                class="ml-4"
                            >
                                <div
                                    class="flex"
                                    role="group"
                                >
                                    <sw-radio
                                        v-model="notices.invoice_notices_settings_notice_3_type"
                                        :value="$t('customers.before')"
                                    />
                                    <div class="ml-2 pt-2">
                                        {{ $t('customers.before') }}
                                    </div>
                                    <sw-radio
                                        v-model="notices.invoice_notices_settings_notice_3_type"
                                        :value="$t('customers.after')"
                                        class="ml-4"
                                    />
                                    <div class="ml-2 pt-2">
                                        {{ $t('customers.after') }}
                                    </div>
                                </div>
                            </sw-input-group>
                        </div>
                    </sw-input-group>

                    <sw-input-group
                        :label="$t('customers.auto_debit_pending_notice')"
                        :error="notificationNotice4Error"
                        class="md:col-span-3"
                    >
                        <div
                            class="flex"
                            role="group"
                        >
                            <sw-input
                                v-model="notices.invoice_notices_settings_auto_debit_pending"
                                :invalid="$v.notices.invoice_notices_settings_auto_debit_pending.$error"
                                type="number"
                                @input="$v.notices.invoice_notices_settings_auto_debit_pending.$touch()"
                            />
                            <div class="ml-2 pt-2">
                              {{ $t('customers.days') }}
                            </div>
                        </div>
                    </sw-input-group>

                </div>
            </div>

            <sw-button
                :loading="isLoadingInvoice"
                :disabled="isLoadingInvoice"
                variant="primary"
                type="submit"
                class="mt-4"
                v-if="permissionModule.update"
                >
                <save-icon v-if="!isLoadingInvoice" class="mr-2"/>
                {{ $t('settings.customization.save') }}
            </sw-button>

            <sw-divider class="mt-6 mb-4" />

        </form>  
        <form @submit.prevent="submitConfigData">
          <div class="grid gap-4 mb-8">
                <h6 class="col-span-5 sw-section-title lg:col-span-1">
                    {{ $t('customers.invoice_charge_options_general') }}
                </h6>

                <div
                    class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
                >
                    <sw-input-group
                        :label="$t('customers.invoice_days_before_renewal')"
                        class="md:col-span-3"
                    >
                        <div
                            class="flex"
                            role="group"
                        >
                            <sw-input
                                v-model="formData.invoice_days_before_renewal"
                                :invalid="$v.formData.invoice_days_before_renewal.$error"
                                type="number"
                                @input="$v.formData.invoice_days_before_renewal.$touch()"
                            />
                            <div class="ml-2 pt-2">
                              {{ $t('customers.days') }}
                            </div>
                        </div>
                    </sw-input-group>

                    <sw-input-group
                        :label="$t('customers.auto_debit_days_before_due_date')"
                        class="md:col-span-3"
                    >
                        <div
                            class="flex"
                            role="group"
                        >
                            <sw-input
                                v-model="formData.auto_debit_days_before_due"
                                :invalid="$v.formData.auto_debit_days_before_due.$error"
                                type="number"
                                @input="$v.formData.auto_debit_days_before_due.$touch()"
                            />
                            <div class="ml-2 pt-2">
                              {{ $t('customers.days') }}
                            </div>
                        </div>
                    </sw-input-group>

                    <sw-input-group
                        :label="$t('customers.suspend_services_days_after_due')"
                        class="md:col-span-3"
                    >
                        <div
                            class="flex"
                            role="group"
                        >
                            <sw-input
                                v-model="formData.suspend_services_days_after_due"
                                :invalid="$v.formData.suspend_services_days_after_due.$error"
                                type="number"
                                @input="$v.formData.suspend_services_days_after_due.$touch()"
                            />
                            <div class="ml-2 pt-2">
                              {{ $t('customers.days') }}
                            </div>
                        </div>
                    </sw-input-group>

                    <sw-input-group
                        :label="$t('customers.auto_debit_attempts')"
                        class="md:col-span-3"
                    >
                        <div
                            class="flex"
                            role="group"
                        >
                            <sw-input
                                v-model="formData.auto_debit_attempts"
                                :invalid="$v.formData.auto_debit_attempts.$error"
                                type="number"
                                @input="$v.formData.auto_debit_attempts.$touch()"
                            />
                        </div>
                    </sw-input-group>

                    <!-- <sw-input-group
                        :label="$t('customers.cancel_service_changes_days_after_due')"
                        class="md:col-span-3"
                    >
                        <div
                            class="flex"
                            role="group"
                        >
                            <sw-input
                                v-model="formData.cancel_service_changes_days"
                                :invalid="$v.formData.cancel_service_changes_days.$error"
                                type="number"
                                @input="$v.formData.cancel_service_changes_days.$touch()"
                            />
                            <div class="ml-2 pt-2">
                                Days
                            </div>
                        </div>
                    </sw-input-group>-->

                    <!-- <sw-input-group
                        :label="$t('customers.apply_late_fee_open_invoices_after_due')"
                        class="md:col-span-3"
                    >
                        <div
                            class="flex"
                            role="group"
                        >
                            <sw-input
                                v-model="formData.apply_invoice_late_fees"
                                :invalid="$v.formData.apply_invoice_late_fees.$error"
                                type="number"
                                @input="$v.formData.apply_invoice_late_fees.$touch()"
                            />
                            <div class="ml-2 pt-2">
                                Days
                            </div>
                        </div>
                    </sw-input-group>-->

                  <!-- Switch -->
                    <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.enable_auto_debit"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.enable_auto_debit') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.invoice_suspended_services"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.invoice_suspended_services') }}
                            </p>
                        </div>
                    </div>

                    <!-- <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.invoice_service_together"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.invoice_service_together') }}
                            </p>
                        </div>
                    </div>-->

                    <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.cancel_services"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.cancel_services') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.auto_apply_credits"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.auto_apply_credits') }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
            <sw-button
                :loading="isLoadingGeneralCustomer"
                :disabled="isLoadingGeneralCustomer"
                variant="primary"
                type="submit"
                class="mt-4"
                v-if="permissionModule.update"
                >
                <save-icon v-if="!isLoadingGeneralCustomer" class="mr-2"/>
                {{ $t('settings.customization.save') }}
            </sw-button>
        </form>
    </sw-card>
  </div>
</template>

<script>
import { mapActions , mapGetters} from 'vuex'
const { required, email, minValue, maxValue } = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      isLoading: false,
      notification_email: null,
      notify_invoice_viewed: null,
      notify_estimate_viewed: null,
      isRequestOnGoing: false,
      isLoadingInvoice: false,
      isLoadingGeneralCustomer:false,
      notices:{
        invoice_notices_settings_send_cancellation:false,
        invoice_notices_settings_send_payment:false,
        invoice_notices_settings_notice_1:0,
        invoice_notices_settings_notice_1_type:"",
        invoice_notices_settings_notice_2:0,
        invoice_notices_settings_notice_2_type:"",
        invoice_notices_settings_notice_3:0,
        invoice_notices_settings_notice_3_type:"",
        invoice_notices_settings_auto_debit_pending:0
      },
      formData: {
                invoice_days_before_renewal: 0,
                auto_debit_days_before_due: 0,
                suspend_services_days_after_due: 0,
                auto_debit_attempts: 0,
                cancel_service_changes_days: 0,
                apply_invoice_late_fees: 0,
                enable_auto_debit: false,
                set_invoice_method: false,
                invoice_suspended_services: false,
                invoice_service_together: false,
                display_range_date: false,
                cancel_services: false,
                synchronize_addons: false,
                client_create_addons: false,
                client_change_service_term: false,
                client_change_service_package: false,
                client_prorate_credits: false,
                auto_apply_credits: false,
                auto_paid_pending_services: false,
                void_invoice_canceled_service: false,
                void_invoice_canceled_service_days: 0,
                show_client_tax_id: false,
                queue_service_changes: false,
                send_cancellation_notice: false,
                send_payment_notices: false,
                notice_1: 0,
                notice_1_type: null,
                notice_2: 0,
                notice_2_type: null,
                notice_3: 0,
                notice_3_type: null,
                auto_debit_pending_notice: 0
      },
      permissionModule: {
        create: false,
        read: false,
        update: false,
        delete: false,
      }

    }
  },
  validations: {
    notification_email: {
      required,
      email,
    },
    notices:{
        invoice_notices_settings_notice_1: {
            required,
            minValue: minValue(0),
            maxValue: maxValue(300)
        },
        invoice_notices_settings_notice_2: {
            required,
            minValue: minValue(0),
            maxValue: maxValue(300)
        },
        invoice_notices_settings_notice_3: {
            required,
            minValue: minValue(0),
            maxValue: maxValue(300)
        },
        invoice_notices_settings_auto_debit_pending: {
            required,
            minValue: minValue(0),
            maxValue: maxValue(300)
        },
    },
    formData: {
                invoice_days_before_renewal: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(300)
                },
                auto_debit_days_before_due: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(300)
                },
                suspend_services_days_after_due: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(300)
                },
                auto_debit_attempts: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(300)
                },
                 cancel_service_changes_days: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(300)
                },
                apply_invoice_late_fees: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(300)
                },
    }
  },

  computed: {
    ...mapGetters('user', ['currentUser']),
    notificationEmailError() {
      if (!this.$v.notification_email.$error) {
        return ''
      }

      if (!this.$v.notification_email.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.notification_email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },
    notificationNotice1Error() {
      if (!this.$v.notices.invoice_notices_settings_notice_1.$error) {
        return ''
      }
      if (!this.$v.notices.invoice_notices_settings_notice_1.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.notices.invoice_notices_settings_notice_1.min) {
        return this.$tc('validation.min_max_number')
      }
    },
    notificationNotice2Error() {
      if (!this.$v.notices.invoice_notices_settings_notice_2.$error) {
        return ''
      }
      if (!this.$v.notices.invoice_notices_settings_notice_2.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.notices.invoice_notices_settings_notice_2.min) {
        return this.$tc('validation.min_max_number')
      }
    },
    notificationNotice3Error() {
      if (!this.$v.notices.invoice_notices_settings_notice_3.$error) {
        return ''
      }
      if (!this.$v.notices.invoice_notices_settings_notice_3.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.notices.invoice_notices_settings_notice_3.min) {
        return this.$tc('validation.min_max_number')
      }
    },
    notificationNotice4Error() {
      if (!this.$v.notices.invoice_notices_settings_auto_debit_pending.$error) {
        return ''
      }
      if (!this.$v.notices.invoice_notices_settings_auto_debit_pending.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.notices.invoice_notices_settings_auto_debit_pending.min) {
        return this.$tc('validation.min_max_number')
      }
    },
  },

  created(){
    this.permissionsUserModule()
  },

  mounted() {
    this.fetchData()
    this.loadData()
  },
  methods: {
    ...mapActions('users', ['getUsersAdm']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('company', ['fetchCompanySettings', 'updateCompanySettings']),
    ...mapActions('customer', [
            'fetchViewCustomer',
            'setConfig',
            'getConfig'
        ]),


    async fetchData() {
      this.isRequestOnGoing = true
      let response = await this.fetchCompanySettings([
        'notify_invoice_viewed',
        'notify_estimate_viewed',
        'notification_email',
        'invoice_notices_settings_send_cancellation',
        'invoice_notices_settings_send_payment',
        'invoice_notices_settings_notice_1',
        'invoice_notices_settings_notice_1_type',
        'invoice_notices_settings_notice_2',
        'invoice_notices_settings_notice_2_type',
        'invoice_notices_settings_notice_3',
        'invoice_notices_settings_notice_3_type',
        'invoice_notices_settings_auto_debit_pending'
      ])

      if (response.data) {
        if(response.data.notification_email=='support@corebill.co' || response.data.notification_email=='noreply@crater.in'){
          let us= await this.getUsersAdm()
          this.notification_email = us.data.user.email 
              let data = {
        settings: {
          notification_email: this.notification_email,
          },
        }
          await this.updateCompanySettings(data)
        }else
        {
          this.notification_email = response.data.notification_email
        }

        response.data.notify_invoice_viewed === 'YES'
          ? (this.notify_invoice_viewed = true)
          : (this.notify_invoice_viewed = false)

        response.data.notify_estimate_viewed === 'YES'
          ? (this.notify_estimate_viewed = true)
          : (this.notify_estimate_viewed = false)
          /* console.log("Response--->",response.data); */
        response.data.invoice_notices_settings_send_cancellation === "1"
          ? (this.notices.invoice_notices_settings_send_cancellation = true)
          : (this.notices.invoice_notices_settings_send_cancellation = false)
        
        response.data.invoice_notices_settings_send_payment === "1"
          ? (this.notices.invoice_notices_settings_send_payment = true)
          : (this.notices.invoice_notices_settings_send_payment = false)

        this.notices.invoice_notices_settings_notice_1=parseInt(response.data.invoice_notices_settings_notice_1)
        
        response.data.invoice_notices_settings_notice_1_type === 'Before'
        ? (this.notices.invoice_notices_settings_notice_1_type = response.data.invoice_notices_settings_notice_1_type)
        : (this.notices.invoice_notices_settings_notice_1_type = "After")

        this.notices.invoice_notices_settings_notice_2=parseInt(response.data.invoice_notices_settings_notice_2)
        
        response.data.invoice_notices_settings_notice_2_type === 'Before'
        ? (this.notices.invoice_notices_settings_notice_2_type = response.data.invoice_notices_settings_notice_2_type)
        : (this.notices.invoice_notices_settings_notice_2_type = "After")

        this.notices.invoice_notices_settings_notice_3=parseInt(response.data.invoice_notices_settings_notice_3)
        
        response.data.invoice_notices_settings_notice_3_type === 'Before'
        ? (this.notices.invoice_notices_settings_notice_3_type = response.data.invoice_notices_settings_notice_3_type)
        : (this.notices.invoice_notices_settings_notice_3_type = "After")

        this.notices.invoice_notices_settings_auto_debit_pending=parseInt(response.data.invoice_notices_settings_auto_debit_pending)
      }
      this.isRequestOnGoing = false
    },

    async loadData() {
            let response = await this.getConfig(0)
            // console.log("loadData",response.data.config)
            if (response.data.config) {
                this.formData = response.data.config
                this.formData.enable_auto_debit = this.formData.enable_auto_debit === 1
               /*  this.formData.set_invoice_method = this.formData.set_invoice_method === 1 */
                this.formData.invoice_suspended_services = this.formData.invoice_suspended_services === 1
                this.formData.invoice_service_together = this.formData.invoice_service_together === 1
                /* this.formData.display_range_date = this.formData.display_range_date === 1 */
                this.formData.cancel_services = this.formData.cancel_services === 1
                /* this.formData.synchronize_addons = this.formData.synchronize_addons === 1 */
                /* this.formData.client_create_addons = this.formData.client_create_addons === 1 */
                /* this.formData.client_change_service_term = this.formData.client_change_service_term === 1 */
                /* this.formData.client_change_service_package = this.formData.client_change_service_package === 1 */
                /* this.formData.client_prorate_credits = this.formData.client_prorate_credits === 1 */
                this.formData.auto_apply_credits = this.formData.auto_apply_credits === 1
                /* this.formData.auto_paid_pending_services = this.formData.auto_paid_pending_services === 1 */
                /* this.formData.void_invoice_canceled_service = this.formData.void_invoice_canceled_service === 1 */
                /* this.formData.show_client_tax_id = this.formData.show_client_tax_id === 1 */
                /* this.formData.queue_service_changes = this.formData.queue_service_changes === 1 */
                /* this.formData.send_cancellation_notice = this.formData.send_cancellation_notice === 1 */
                /* this.formData.send_payment_notices = this.formData.send_payment_notices === 1 */
            }
        },

    async saveEmail() {
      this.$v.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true

      let data = {
        settings: {
          notification_email: this.notification_email,
        },
      }

      let response = await this.updateCompanySettings(data)

      if (response.data.success) {
        this.isLoading = false

        window.toastr['success'](
          this.$tc('settings.notification.email_save_message')
        )
      }
    },

    async updateCustomerSetting() {
            this.$v.notices.$touch()
            if (this.$v.$invalid) {
                return true
            } 
     
            let data = {
                settings: {
                invoice_notices_settings_send_cancellation:this.notices.invoice_notices_settings_send_cancellation,
                invoice_notices_settings_send_payment:this.notices.invoice_notices_settings_send_payment,
                invoice_notices_settings_notice_1:this.notices.invoice_notices_settings_notice_1 ? this.notices.invoice_notices_settings_notice_1 : 0,
                invoice_notices_settings_notice_1_type:this.notices.invoice_notices_settings_notice_1_type,
                invoice_notices_settings_notice_2:this.notices.invoice_notices_settings_notice_2 ? this.notices.invoice_notices_settings_notice_2 : 0,
                invoice_notices_settings_notice_2_type:this.notices.invoice_notices_settings_notice_2_type,
                invoice_notices_settings_notice_3:this.notices.invoice_notices_settings_notice_3 ? this.notices.invoice_notices_settings_notice_3 : 0,
                invoice_notices_settings_notice_3_type:this.notices.invoice_notices_settings_notice_3_type,
                invoice_notices_settings_auto_debit_pending:this.notices.invoice_notices_settings_auto_debit_pending ? this.notices.invoice_notices_settings_auto_debit_pending : 0
                },
            }

            if (this.updateSetting(data)) {
                window.toastr['success'](
                this.$t('corePbx.customization.services_updated')
                )
            }
        },

        async updateSetting(data) {
            this.isLoadingInvoice = true
            let res = await this.updateCompanySettings(data)

            if (res.data.success) {
                this.isLoadingInvoice = false
                return true
            }

            return false
        },

    async setInvoiceViewd(val) {
      this.$v.$touch()

      if (this.$v.$invalid) {
        this.notify_invoice_viewed = !this.notify_invoice_viewed
        return true
      }

      let data = {
        settings: {
          notify_invoice_viewed: this.notify_invoice_viewed ? 'YES' : 'NO',
        },
      }

      let response = await this.updateCompanySettings(data)

      if (response.data.success) {
        window.toastr['success'](this.$tc('general.setting_updated'))
      }
    },

    async setEstimateViewd(val) {
      this.$v.$touch()

      if (this.$v.$invalid) {
        this.notify_estimate_viewed = !this.notify_estimate_viewed
        return true
      }

      let data = {
        settings: {
          notify_estimate_viewed: this.notify_estimate_viewed ? 'YES' : 'NO',
        },
      }

      let response = await this.updateCompanySettings(data)

      if (response.data) {
        window.toastr['success'](this.$tc('general.setting_updated'))
      }
    },
    async submitConfigData() {
            this.$v.formData.$touch()
            if (this.$v.$invalid) {
                return true
            }

            this.isLoading = true;

            let data = {
                customer_id: 0,
                ...this.formData
            }

            let response = await this.setConfig(data)
            this.isLoading = false;

            if (response.data.success) {
                window.toastr['success'](this.$t('customers.config_saved'))
                this.$router.push('/admin/settings/notifications')
                return true
            }

            window.toastr['error'](response.data.message)
            return true;
        },

    async permissionsUserModule(){
      const data = {
         module: "notifications" 
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if(permissions.super_admin == false){
        if(permissions.exist == false ){
          this.$router.push('/admin/dashboard')
        }else {
         const modulePermissions = permissions.permissions[0]
          if(modulePermissions == null){
            this.$router.push('/admin/dashboard')
          }else if(modulePermissions.access == 0 || modulePermissions.read == 0){
            this.$router.push('/admin/dashboard')
          }
        }
      }

      // valida que el usuario tenga el permiso create, read, delete, update
      if(permissions.super_admin == true){
        this.permissionModule.create = true
        this.permissionModule.update = true
        this.permissionModule.delete = true
        this.permissionModule.read = true
      }else if(permissions.exist == true ){
        const modulePermissions = permissions.permissions[0]
        if(modulePermissions.create == 1){
            this.permissionModule.create = true
        }
        if(modulePermissions.update == 1){
            this.permissionModule.update = true
        }
        if(modulePermissions.delete == 1){
            this.permissionModule.delete = true
        }
        if(modulePermissions.read == 1){
            this.permissionModule.read = true
        }
      }

    }
  },
}
</script>
