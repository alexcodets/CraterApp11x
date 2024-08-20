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
                <sw-tab-item v-if="false" :title="$t('logs.failed_payment_history.title')">
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
    created(){
        this.permissionsUserModule()
    },
    methods:{
        
        async permissionsUserModule(){
            const data = {
            module: "logs" 
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
                    }else if(modulePermissions.access == 0 ){
                    this.$router.push('/admin/dashboard')
                    }
                }
            }
        }
    },
}
</script>

<style scoped>

</style>