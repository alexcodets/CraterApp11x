<template>
    <base-page>
        <sw-page-header :title="$t('customers.pbx_service_view')">
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
                    :to="`/customer/tickets/add`"
                    class="mr-3"
                >
                    {{ $t('customer_ticket.new_ticket') }}
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
            <service-detail v-if="bandera"
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
import ServiceDetail from "./partials/PbxServiceDetail";
import {mapActions, mapGetters} from "vuex";

export default {
    components: {
        DotsHorizontalIcon,
        PencilIcon,
        TrashIcon,
        CustomerInfo,
        ServiceDetail
    },
    data() {
        return {
            bandera:false,
            cancel_service: false,
            status_cancelled: 'C'
        }
    },
    computed: {
        ...mapGetters('customer', ['selectedViewCustomer']),
        ...mapGetters('user', ['currentUser']),
        ...mapGetters('pbxService', ['selectedPbxService']),

        async getInfoClient() {
            if(this.currentUser){
             this.fetchViewCustomer({ id: this.currentUser.id })
                let response = await this.fetchPbxService(
                    this.$route.params.pbx_service_id
                )
                if(response.data.response.pbx_service.customer_id===this.currentUser.id){
                    this.bandera=true
                }
             return true
            }
            return false 
        },

        isCancelled() {
            return this.selectedPbxService && this.selectedPbxService.status === this.status_cancelled
        },
    },
    created() {
        this.loadData()
    },
    methods: {
        ...mapActions('customer', ['fetchViewCustomer']),
        ...mapActions('pbxService', ['updateServiceStatus', 'deleteService']),
        ...mapActions('pbxService', [
            'fetchPbxService',
            'fetchExtensions',
            'fetchDIDs',
            'fetchItemsPbxService',
            'fetchAdditionalCharges',
            'fetchCallHistory',
            ]),
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
                        id: this.$route.params.pbx_service_id,
                        status: this.status_cancelled,
                    }
                    let response = await this.updateServiceStatus(data)

                    if (response.data.success) {
                        this.$router.go();
                        window.toastr['success'](this.$t('pbx_services.updated_message'))
                    }
                }
            })

        }
    }
}
</script>

<style scoped>

</style>