<template>
    <base-page>
        <sw-page-header :title="$t('customers.service_view')">
            <template slot="actions">
                <sw-button
                  v-if="cancel_service && !isCancelled"
                  class="mr-3"
                  variant="danger"
                  @click="cancelService"
                >
                  {{ $t('customer_profile.cancel_service') }}
                </sw-button>

                <sw-button
                    tag-name="router-link"
                    :to="`/customer/dashboard`"
                    class="mr-3"
                    variant="primary-outline"
                >
                    {{ $t('general.go_back') }}
                </sw-button>

            </template>
        </sw-page-header>

        <sw-card class="flex flex-col mt-6">
            <!-- Customer basic info -->
            <customer-info v-if="getInfoClient"/>

            <!-- Service details -->
            <confirm-package
                @status=""
            />

        </sw-card>

    </base-page>
</template>

<script>

import {
    DotsHorizontalIcon,
    PencilIcon,
    TrashIcon,
} from '@vue-hero-icons/solid'
import CustomerInfo from './partials/CustomerInfo'
import ConfirmPackage from "./partials/WizardConfirmPackage";
import {mapActions, mapGetters} from "vuex";

export default {
    components: {
        DotsHorizontalIcon,
        PencilIcon,
        TrashIcon,
        CustomerInfo,
        ConfirmPackage
    },
    data() {
        return {
            cancel_service: false,
            status_cancelled: 'C'
        }
    },
    computed: {
        ...mapGetters('customer', ['selectedViewCustomer']),
        ...mapGetters('user', ['currentUser']),
        ...mapGetters('service', ['selectedService']),


        async getInfoClient() {
            if (this.currentUser) {
                this.fetchViewCustomer({ id: this.currentUser.id })
                return true
            }
            return false 
        },

        isCancelled() {
            return this.selectedService && this.selectedService.status === this.status_cancelled
        },
    },

    created() {
      this.loadData()
    },
   
    methods: {
        ...mapActions('customer', ['fetchViewCustomer']),
        ...mapActions('service', ['fetchViewService', 'updateService']),
        ...mapActions('customerProfile', ['getConfig']),

        async loadData() {
            let response = await this.getConfig(this.currentUser.id)

            if (response.data.config) {
                this.cancel_service = response.data.config.cancel_services === 1
            }
        },

        async cancelService() {
            swal({
                title: this.$t('general.are_you_sure'),
                text: 'you will not be able to recover this service!',
                icon: '/assets/icon/trash-solid.svg',
                buttons: true
            }).then(async (result) => {
                if (result) {
                    let data = {
                        id: this.$route.params.customer_package_id,
                        status: this.status_cancelled,
                        oneKey: true,
                        key: 'status'
                    }
                    let response = await this.updateService(data)

                    if (response.data.success) {
                        this.$router.go();
                        window.toastr['success'](this.$t('services.updated_message'))
                    }
                }
            })

        }
    }
}
</script>

<style scoped>

</style>