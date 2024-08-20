<template>
    <base-page class="bg-white">
        <sw-page-header :title="pageTitle">
            <template slot="actions">
                <sw-button
                    tag-name="router-link"
                    :to="`/admin/customers/${$route.params.id}/view`"
                    class="mr-3"
                    variant="primary-outline"
                >
                    {{ $t('general.cancel') }}
                </sw-button>
            </template>
        </sw-page-header>

        <!-- Basic info -->
        <customer-info />

        <div class="flex flex-col items-center justify-between w-full h-32 step-indicator">
            <sw-wizard
                :steps="3"
                :currentStep.sync="step"
                :allow-navigation-redirect="false"
            >
                <component
                    :is="tab"
                    @next="setTab"
                    @back="back"
                />
            </sw-wizard>
        </div>

    </base-page>
</template>

<script>

import CustomerInfo from './partials/CustomerInfo'
import FormPackage from "./partials/WizardPackageParameters";
import PackageDetail from "./partials/WizardPackageDetail"
import ConfirmPackage from "./partials/WizardConfirmPackage";
import { mapActions, mapGetters } from 'vuex'

export default {
    components: {
        CustomerInfo,
        step_1: FormPackage,
        step_2: PackageDetail,
        step_3: ConfirmPackage,
    },
    data() {
        return {
            loading: false,
            tab: 'step_1',
            step: 1,
        }
    },
    computed: {
        ...mapGetters('customer', ['selectedViewCustomer']),

        pageTitle() {
            if (this.$route.name === 'services.edit') {
                return this.$t('services.edit_service')
            }
            return this.$t('services.add_service')
        }
    },
    created() {
        this.loadCustomer()
    },
    methods: {
        ...mapActions('customer', ['fetchViewCustomer']),

        async loadCustomer() {
            this.fetchViewCustomer({ id: this.$route.params.id })
        },

        async setTab() {
            this.step++

            if (this.step <= 3) {
                this.tab = 'step_' + this.step
            } else {
                // window.location.reload()
            }
        },

        async back() {
            this.step--

            if (this.step >= 1 ) {
                this.tab = 'step_' + this.step
            }
        }
    },
}
</script>
