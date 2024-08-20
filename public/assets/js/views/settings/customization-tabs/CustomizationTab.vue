<template>
    <div>
        <form action="" class="mt-6" @submit.prevent="updateCustomerSetting">
            <sw-input-group
                :label="
                $t('settings.customization.customer_customer_creation')
                "
                class="mt-6 mb-4"
            >
            
                <base-custom-input
                v-model="emailCustomer.customer_customer_creation"
                :fields="InvoiceMailFieldsCreation"
                class="mt-2"
                />
            </sw-input-group>
            <sw-input-group
                :label="
                $t('settings.customization.customer_account_registration')
                "
                class="mt-6 mb-4"
            >
            
                <base-custom-input
                v-model="emailCustomer.customer_account_registration"
                :fields="InvoiceMailFields"
                class="mt-2"
                />
            </sw-input-group>
            <!-- <sw-input-group
                :label="
                $t('settings.customization.customer_password_creation')
                "
                class="mt-6 mb-4"
            >
            
                <base-custom-input
                v-model="emailCustomer.customer_password_creation"
                :fields="InvoiceMailFields"
                class="mt-2"
                />
            </sw-input-group> -->
            <sw-input-group
                :label="
                $t('settings.customization.customer_password_reset')
                "
                class="mt-6 mb-4"
            >
            
                <base-custom-input
                v-model="emailCustomer.customer_password_reset"
                :fields="InvoiceMailFields"
                class="mt-2"
                />
            </sw-input-group>
            <sw-input-group
                :label="
                $t('settings.customization.customer_email_verification')
                "
                class="mt-6 mb-4"
            >
            
                <base-custom-input
                v-model="emailCustomer.customer_email_verification"
                :fields="InvoiceMailFields"
                class="mt-2"
                />
            </sw-input-group>
            <sw-input-group
                :label="
                $t('settings.customization.customer_forgetting_username')
                "
                class="mt-6 mb-4"
            >
            
                <base-custom-input
                v-model="emailCustomer.customer_forgetting_username"
                :fields="InvoiceMailFields"
                class="mt-2"
                />
            </sw-input-group>
            <sw-input-group
                :label="
                $t('settings.customization.customer_email_notification')
                "
                class="mt-6 mb-4"
            >
            
                <base-custom-input
                v-model="emailCustomer.customer_email_notification"
                :fields="InvoiceMailFieldsCreation"
                class="mt-2"
                />
            </sw-input-group>

            <sw-button
                :loading="isLoadingCustomer"
                :disabled="isLoadingCustomer"
                variant="primary"
                type="submit"
                class="mt-4">
                <save-icon v-if="!isLoadingCustomer" class="mr-2"/>
                {{ $t('settings.customization.save') }}
            </sw-button>

            <sw-divider class="mt-6 mb-4" />

        </form>

        <form action="" class="mt-6" @submit.prevent="updatePrefix">
            <sw-input-group
                :label="$t('settings.customization.customer_prefix')">
                <sw-input
                    v-model="customers.customers_prefix"
                    style="max-width: 30%"
                />
            </sw-input-group>
            <div class="flex mt-3 mb-4">
                <div class="relative w-12">
                    <sw-switch
                        v-model="customers.customers_prefix_general"
                        class="absolute"
                        style="top: -20px"/>
                </div>
                <div class="ml-4">
                    <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                         {{ $t('settings.customization.items.apply_general_prefix') }}
                    </p>
                </div>
            </div>

            <sw-input-group
                :label="$t('settings.customization.service_prefix')"
                :error="servicePrefixError"
            >
                <sw-input
                    v-model="customers.service_prefix"
                    :invalid="$v.customers.service_prefix.$error"
                    style="max-width: 30%"
                    @input="$v.customers.service_prefix.$touch()"
                    @keyup="changeToUppercase()"
                />
            </sw-input-group>

            <div class="flex mt-3 mb-4">
                <div class="relative w-12">
                    <sw-switch
                        v-model="customers.general_service_prefix"
                        class="absolute"
                        style="top: -20px"
                    />
                </div>
                <div class="ml-4">
                    <p class="p-0 mb-1 text-base leading-snug text-black">
                        {{ $t('settings.customization.general_service_prefix') }}
                    </p>
                </div>
            </div>

            <sw-button
                :loading="isLoading"
                :disabled="isLoading"
                variant="primary"
                type="submit"
                class="mt-4">
                <save-icon v-if="!isLoading" class="mr-2"/>
                {{ $t('settings.customization.save') }}
            </sw-button>

            <sw-divider class="mt-6 mb-4" />

        </form>

        <form action="" class="mt-6" @submit.prevent="updateScheduling">

            <h3 class="mb-5 text-lg font-medium text-black">
                {{ $t('settings.customization.customer.services_renewal') }}
            </h3>

            <div class="flex mb-4">
                <div class="relative w-12">
                    <sw-switch
                        v-model="allow_renewal_date_job"
                        class="absolute"
                        style="top: -18px"
                        @change="(option) => slideRenewalDate(option)"
                    />
                </div>

                <div class="ml-4">
                    <p class="p-0 mb-1 text-base leading-snug text-black">
                        {{ $t('settings.customization.customer.allow_renewal_date_job') }}
                    </p>

                    <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
                        {{ $t('settings.customization.customer.renewal_date_job_desc') }}
                    </p>
                </div>
            </div>

            <sw-input-group
                :label="$t('settings.customization.customer.time_run_renewal_date_job')"
                :error="timeJobError"
                class="mb-4"
                required
            >
                <base-time-picker
                    v-model="time_run_renewal_date_job"
                    :calendar-button="true"
                    style="max-width: 30%"
                    :placeholder="'HH:mm'"
                    calendar-button-icon="calendar"
                    @input="$v.time_run_renewal_date_job.$touch()"
                />

            </sw-input-group>

            <sw-button
                :loading="isLoading"
                :disabled="isLoading"
                variant="primary"
                type="submit"
                class="mt-4"
            >
                <save-icon v-if="!isLoading" class="mr-2"/>
                {{ $t('settings.customization.customer.save_renewal') }}
            </sw-button>
            <sw-divider class="mt-6 mb-4" />
        </form>
        <!-- Customer Autodebit -->
        <form action="" class="mt-6" @submit.prevent="updateAutoDebitScheduling">

            <h3 class="mb-5 text-lg font-medium text-black">
                {{ $t('settings.customization.customer.customer_autodebit') }}
            </h3>

            <div class="flex mb-4">
                <div class="relative w-12">
                    <sw-switch
                        v-model="allow_autodebit_customer_job"
                        class="absolute"
                        style="top: -18px"
                        @change="(option) => slideAutoDebitDate(option)"
                    />
                </div>

                <div class="ml-4">
                    <p class="p-0 mb-1 text-base leading-snug text-black">
                        {{ $t('settings.customization.customer.allow_autodebit_date_job') }}
                    </p>

                    <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
                        {{ $t('settings.customization.customer.autodebit_date_job_desc') }}
                    </p>
                </div>
            </div>

            <sw-input-group
                :label="$t('settings.customization.customer.time_run_autodebit_date_job')"
                :error="timeAutoDebitJobError"
                class="mb-4"
                required
            >
                <base-time-picker
                    v-model="time_run_autodebit_customer_job"
                    :invalid="$v.time_run_autodebit_customer_job.$error"
                    :calendar-button="true"
                    style="max-width: 30%"
                    :placeholder="'HH:mm'"
                    calendar-button-icon="calendar"
                    @input="$v.time_run_autodebit_customer_job.$touch()"
                />

            </sw-input-group>

            <sw-button
                :loading="isLoadingAutoDebit"
                :disabled="isLoadingAutoDebit"
                variant="primary"
                type="submit"
                class="mt-4"
            >
                <save-icon v-if="!isLoadingAutoDebit" class="mr-2"/>
                {{ $t('settings.customization.customer.auto_debit') }}
            </sw-button>
        </form>
    </div>
</template>

<script>
import {requiredIf} from "vuelidate/lib/validators";

const {required, maxLength, alpha} = require('vuelidate/lib/validators')
import {mapActions, mapGetters} from 'vuex'
import moment from 'moment'
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
            customerAutogenerate: false,
            emailCustomer:{
                customer_customer_creation:null,
                customer_account_registration:null,
                /* customer_password_creation:null, */
                customer_password_reset:null,
                customer_email_verification:null,
                customer_forgetting_username:null,
                customer_email_notification:null
            },
            customers: {
                customers_prefix: null,
                customers_prefix_general: null,
                service_prefix: "",
                general_service_prefix: false
            },
            InvoiceMailFields: [
                'customerE',
                'company',
            ],
            InvoiceMailFieldsCreation: [
                'customerC',
                'company',
            ],
            allow_renewal_date_job: false,
            allow_autodebit_customer_job:false,
            time_run_renewal_date_job: null,
            time_run_autodebit_customer_job:null,
            isLoading: false,
            isLoadingCustomer: false,
            isLoadingAutoDebit: false,
        }
    },
    mounted() {
         this.getFirstUserPrefix()
    },
    computed: {
        customerPrefixError() {
            if (!this.$v.customers.customers_prefix.$error) {
                return ''
            }
            if (!this.$v.customers.customers_prefix.required) {
                return this.$t('validation.required')
            }
        },
        servicePrefixError() {
            if (!this.$v.customers.service_prefix.$error) {
                return ''
            }
            if (!this.$v.customers.service_prefix.required) {
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
        timeAutoDebitJobError() {
            if (!this.$v.time_run_autodebit_customer_job.$error) {
                return ''
            }
            if (!this.$v.time_run_autodebit_customer_job.required) {
                return this.$t('validation.required')
            }
        },
    },
    watch: {
        settings(val) {
            this.customers.service_prefix = val ? val.service_prefix : ''
            this.customers.customers_prefix = val ? val.customer_prefix : ''
            this.time_run_renewal_date_job = val ? val.time_run_renewal_date_job : null
            this.time_run_autodebit_customer_job = val ? val.time_run_autodebit_customer_job : null
            this.allow_renewal_date_job = val ? val.allow_renewal_date_job == 1 : false
            this.allow_autodebit_customer_job = val ? val.allow_autodebit_customer_job == 1 : false
            this.emailCustomer.customer_account_registration = val ? val.customer_account_registration : ''
            this.emailCustomer.customer_customer_creation = val ? val.customer_customer_creation : ''
            /* this.emailCustomer.customer_password_creation = val ? val.customer_password_creation : '' */
            this.emailCustomer.customer_password_reset = val ? val.customer_password_reset : ''
            this.emailCustomer.customer_email_verification = val ? val.customer_email_verification : ''
            this.emailCustomer.customer_forgetting_username = val ? val.customer_forgetting_username : ''
            this.emailCustomer.customer_email_notification = val ? val.customer_email_notification : ''
        }
    },
    validations: {
        customers: {
            customers_prefix: {
                required,
                alpha,
            },
            service_prefix: {
                required,
                alpha,
            }
        },
        time_run_renewal_date_job: {
            required: requiredIf(function () {
                return this.allow_renewal_date_job
            }),
        },
        time_run_autodebit_customer_job: {
            required
        },
    },
    methods: {
        ...mapActions('customer', ['setPrefix', 'fetchPrefix']),
        ...mapActions('company', ['updateCompanySettings']),

        async updateCustomerSetting() {
     
            let data = {
                settings: {
                customer_account_registration: this.emailCustomer.customer_account_registration,
                customer_customer_creation: this.emailCustomer.customer_customer_creation,
                /* customer_password_creation: this.emailCustomer.customer_password_creation, */
                customer_password_reset: this.emailCustomer.customer_password_reset,
                customer_email_verification: this.emailCustomer.customer_email_verification,
                customer_forgetting_username: this.emailCustomer.customer_forgetting_username,
                customer_email_notification: this.emailCustomer.customer_email_notification,
                },
            }

            if (this.updateSetting(data)) {
                window.toastr['success'](
                this.$t('corePbx.customization.services_updated')
                )
            }
        },

        async updateSetting(data) {
            this.isLoadingCustomer = true
            let res = await this.updateCompanySettings(data)

            if (res.data.success) {
                this.isLoadingCustomer = false
                return true
            }

            return false
        },

        async getFirstUserPrefix() {
            this.isLoading = true
            let res = await this.fetchPrefix({})
            if (res.data.success) {
                this.isLoading = false
                console.log(res.data);
                this.customers.customers_prefix = res.data.customcode
            } else {
                this.isLoading = false
            }
        },

        async updatePrefix() {
            this.isLoading = true
            let res = await this.setPrefix(this.customers)
            if (res.data.success) {
                this.isLoading = false
                window.toastr['success'](
                this.$t('settings.customization.customer.customer_setting_updated')
                )
                this.getFirstUserPrefix()
                return true
            } else {
                this.isLoading = false
                return true
            }
            return false
        },

        changeToUppercase() {
            this.customers.service_prefix = this.customers.service_prefix.toUpperCase()
            return true
        },

        slideRenewalDate(val) {
            if (!val) {
                this.time_run_renewal_date_job = null;
            }
        },

        async updateScheduling() {
            this.$v.time_run_renewal_date_job.$touch()
            if (this.$v.time_run_renewal_date_job.$error) {
                return true
            }
            let data = {
                settings: {
                    allow_renewal_date_job: this.allow_renewal_date_job,
                    time_run_renewal_date_job: this.time_run_renewal_date_job
                },
            }
            let response = await this.updateCompanySettings(data)
            if (response.data.success) {
                window.toastr['success'](this.$t('general.setting_updated'))
            }
        },

         slideAutoDebitDate(val) {
            if (!val) {
                this.time_run_autodebit_customer_job = null;
            }
        },
        async updateAutoDebitScheduling() {
            this.$v.time_run_autodebit_customer_job.$touch()
                if (this.$v.time_run_autodebit_customer_job.$error) {
                    return true
                }
            let data = {
                settings: {
                    allow_autodebit_customer_job: this.allow_autodebit_customer_job,
                    time_run_autodebit_customer_job: this.time_run_autodebit_customer_job
                },
            }
            let response = await this.updateCompanySettings(data)
            if (response.data.success) {
                window.toastr['success'](this.$t('general.setting_updated'))
            }
        },
    },
}
</script>