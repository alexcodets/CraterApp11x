<template>
    <div class="relative">
        <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
        <sw-card>
            <sw-tabs class="p-2">
                <!-- Logs per Module -->
                <sw-tab-item :title="$t('logs.module_logs.title')">
                    <module-logs />
                </sw-tab-item>

                <!-- Logs per email -->
                <sw-tab-item :title="$t('logs.email_logs.title')">
                    <email-logs />
                </sw-tab-item>

                <!-- Failed payment history -->
                <sw-tab-item :title="$t('logs.failed_payment_history.title')">
                    <failed-payment-history />
                </sw-tab-item>

            </sw-tabs>
        </sw-card>
    </div>
</template>

<script>

import ModuleLogs from "./log-tabs/ModuleLogs.vue";
import EmailLogs from "./log-tabs/EmailLogs.vue";
import FailedPaymentHistory from "./log-tabs/FailedPaymentHistory.vue";

export default {
    components: {
        ModuleLogs,
        EmailLogs,
        FailedPaymentHistory
    },

    data() {
        return {
            settings: {},
            isRequestOnGoing: false,
        }
    },
}
</script>

<style scoped>

</style>