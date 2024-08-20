<template>
    <base-page class="bg-white">
        <form @submit.prevent="submitConfigData">
            <sw-page-header :title="$t('customers.customer_config')">
                <template slot="actions">
                    <sw-button
                        tag-name="router-link"
                        :to="`/admin/customers/${$route.params.id}/view`"
                        class="mr-3"
                        variant="primary-outline"
                    >
                        {{ $t('general.back') }}
                    </sw-button>
                    <sw-button
                        :loading="isLoading"
                        variant="primary"
                        type="submit"
                        class="flex justify-center w-full md:w-auto"
                    >
                        <save-icon class="mr-2 -ml-1" />
                        {{ $t('general.save') }}
                    </sw-button>
                </template>
            </sw-page-header>

            <!-- Basic info -->
            <customer-info />

            <sw-divider class="my-6"/>

            <div class="grid grid-cols-5 gap-4 mb-8">
                <h6 class="col-span-5 sw-section-title lg:col-span-1">
                    {{ $t('customers.invoice_charge_options') }}
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
                                Days
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
                                Days
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
                                Days
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

                    <!-- 
                        Removed
                        <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.set_invoice_method"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.set_invoice_method') }}
                            </p>
                        </div>
                    </div> -->

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

                    <!-- 
                        Removed
                        <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.display_range_date"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.display_range_date') }}
                            </p>
                        </div>
                    </div> -->

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

                   <!--
                       Removed  
                       <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.synchronize_addons"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.synchronize_addons') }}
                            </p>
                        </div>
                    </div> -->

                    <!--
                        Removed   
                        <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.client_create_addons"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.client_create_addons') }}
                            </p>
                        </div>
                    </div> -->

                    <!-- 
                        Removed  
                        <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.client_change_service_term"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.client_change_service_term') }}
                            </p>
                        </div>
                    </div> -->

                    <!-- 
                        Removed
                        <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.client_change_service_package"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.client_change_service_package') }}
                            </p>
                        </div>
                    </div> -->

                    <!-- 
                        Removed
                        <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.client_prorate_credits"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.client_prorate_credits') }}
                            </p>
                        </div>
                    </div> -->

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

                   <!--
                       Removed  
                       <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.auto_paid_pending_services"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.auto_paid_pending_services') }}
                            </p>
                        </div>
                    </div> -->

                   <!--  
                       Removed 
                       <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.void_invoice_canceled_service"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.void_invoice_canceled_service') }}
                            </p>
                        </div>
                    </div> -->

                    <!--
                        Removed 
                        <sw-input-group
                        :label="$t('customers.void_invoice_canceled_service_days')"
                        class="md:col-span-3"
                    >
                        <div
                            class="flex"
                            role="group"
                        >
                            <sw-input
                                v-model="formData.void_invoice_canceled_service_days"
                                :invalid="$v.formData.void_invoice_canceled_service_days.$error"
                                type="number"
                                @input="$v.formData.void_invoice_canceled_service_days.$touch()"
                            />
                            <div class="ml-2 pt-2">
                                Days
                            </div>
                        </div>
                    </sw-input-group> -->

                    <!-- 
                        Removed
                        <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.show_client_tax_id"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.show_client_tax_id') }}
                            </p>
                        </div>
                    </div> -->

                    <!-- 
                        Removed
                        <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.queue_service_changes"
                                class="absolute"
                                @change=""
                            />
                        </div>

                        <div class="ml-4">
                            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                {{ $t('customers.queue_service_changes') }}
                            </p>
                        </div>
                    </div> -->

                </div>
            </div>

           <!--  <sw-divider class="mb-5 md:mb-8" />

            <div class="grid grid-cols-5 gap-4 mb-8">
                <h6 class="col-span-5 sw-section-title lg:col-span-1">
                    {{ $t('customers.notices') }}
                </h6>

                <div
                    class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
                >
                    <div class="flex md:col-span-3 my-4">
                        <div class="relative w-12">
                            <sw-checkbox
                                v-model="formData.send_cancellation_notice"
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
                                v-model="formData.send_payment_notices"
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
                                style="width: 40%"
                            >
                                <div
                                    class="flex"
                                    role="group"
                                >
                                    <sw-input
                                        v-model="formData.notice_1"
                                        :invalid="$v.formData.notice_1.$error"
                                        type="number"
                                        @input="$v.formData.notice_1.$touch()"
                                    />
                                    <div class="ml-2 pt-2">
                                        Days
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
                                        v-model="formData.notice_1_type"
                                        :value="$t('customers.before')"
                                    />
                                    <div class="ml-2 pt-2">
                                        {{ $t('customers.before') }}
                                    </div>
                                    <sw-radio
                                        v-model="formData.notice_1_type"
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
                                style="width: 40%"
                            >
                                <div
                                    class="flex"
                                    role="group"
                                >
                                    <sw-input
                                        v-model="formData.notice_2"
                                        :invalid="$v.formData.notice_2.$error"
                                        type="number"
                                        @input="$v.formData.notice_2.$touch()"
                                    />
                                    <div class="ml-2 pt-2">
                                        Days
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
                                        v-model="formData.notice_2_type"
                                        :value="$t('customers.before')"
                                    />
                                    <div class="ml-2 pt-2">
                                        {{ $t('customers.before') }}
                                    </div>
                                    <sw-radio
                                        v-model="formData.notice_2_type"
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
                                style="width: 40%"
                            >
                                <div
                                    class="flex"
                                    role="group"
                                >
                                    <sw-input
                                        v-model="formData.notice_3"
                                        :invalid="$v.formData.notice_3.$error"
                                        type="number"
                                        @input="$v.formData.notice_3.$touch()"
                                    />
                                    <div class="ml-2 pt-2">
                                        Days
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
                                        v-model="formData.notice_3_type"
                                        :value="$t('customers.before')"
                                    />
                                    <div class="ml-2 pt-2">
                                        {{ $t('customers.before') }}
                                    </div>
                                    <sw-radio
                                        v-model="formData.notice_3_type"
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
                        class="md:col-span-3"
                    >
                        <div
                            class="flex"
                            role="group"
                        >
                            <sw-input
                                v-model="formData.auto_debit_pending_notice"
                                :invalid="$v.formData.auto_debit_pending_notice.$error"
                                type="number"
                                @input="$v.formData.auto_debit_pending_notice.$touch()"
                            />
                            <div class="ml-2 pt-2">
                                Days
                            </div>
                        </div>
                    </sw-input-group>

                </div>

            </div> -->
        </form>
    </base-page>
</template>

<script>

import CustomerInfo from './partials/CustomerInfo'
import { mapActions, mapGetters } from "vuex";
const {
    required,
    minValue,
    between,
    maxValue,
} = require('vuelidate/lib/validators')

export default {
    components: {
        CustomerInfo,
    },
    data() {
        return {
            isLoading: false,
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
            }
        }
    },
    computed: {
        ...mapGetters('customer', ['selectedViewCustomer']),
    },
    validations() {
        return {
            formData: {
                invoice_days_before_renewal: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(30)
                },
                auto_debit_days_before_due: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(30)
                },
                suspend_services_days_after_due: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(30)
                },
                auto_debit_attempts: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(30)
                },
                cancel_service_changes_days: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(30)
                },
                apply_invoice_late_fees: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(30)
                },
                /* void_invoice_canceled_service_days: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(30)
                }, */
                /* notice_1: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(30)
                },
                notice_2: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(30)
                },
                notice_3: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(30)
                },
                auto_debit_pending_notice: {
                    required,
                    minValue: minValue(0),
                    maxValue: maxValue(30)
                }, */
            }
        }
    },
    created() {
        this.loadData()
    },
    methods: {
        ...mapActions('customer', [
            'fetchViewCustomer',
            'setConfig',
            'getConfig'
        ]),

        async loadData() {
            this.fetchViewCustomer({ id: this.$route.params.id })
            let response = await this.getConfig(this.$route.params.id)
            if (response.data.config) {
                this.formData = response.data.config
                this.formData.enable_auto_debit = this.formData.enable_auto_debit === 1
                this.formData.set_invoice_method = this.formData.set_invoice_method === 1
                this.formData.invoice_suspended_services = this.formData.invoice_suspended_services === 1
                this.formData.invoice_service_together = this.formData.invoice_service_together === 1
                this.formData.display_range_date = this.formData.display_range_date === 1
                this.formData.cancel_services = this.formData.cancel_services === 1
                this.formData.synchronize_addons = this.formData.synchronize_addons === 1
                this.formData.client_create_addons = this.formData.client_create_addons === 1
                this.formData.client_change_service_term = this.formData.client_change_service_term === 1
                this.formData.client_change_service_package = this.formData.client_change_service_package === 1
                this.formData.client_prorate_credits = this.formData.client_prorate_credits === 1
                this.formData.auto_apply_credits = this.formData.auto_apply_credits === 1
                this.formData.auto_paid_pending_services = this.formData.auto_paid_pending_services === 1
                this.formData.void_invoice_canceled_service = this.formData.void_invoice_canceled_service === 1
                this.formData.show_client_tax_id = this.formData.show_client_tax_id === 1
                this.formData.queue_service_changes = this.formData.queue_service_changes === 1
                this.formData.send_cancellation_notice = this.formData.send_cancellation_notice === 1
                this.formData.send_payment_notices = this.formData.send_payment_notices === 1
            }
        },

        async submitConfigData() {
            this.$v.formData.$touch()
            if (this.$v.$invalid) {
                return true
            }

            this.isLoading = true;

            let data = {
                customer_id: this.$route.params.id,
                ...this.formData
            }

            let response = await this.setConfig(data)
            this.isLoading = false;

            if (response.data.success) {
                window.toastr['success'](this.$t('customers.config_saved'))
                this.$router.push('/admin/customers/'+this.$route.params.id+'/view')
                return true
            }

            window.toastr['error'](response.data.message)
            return true;
        }
    }
}
</script>

<style scoped>

</style>