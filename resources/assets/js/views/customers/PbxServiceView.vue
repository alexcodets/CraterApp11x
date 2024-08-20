<template>
    <base-page>
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
        <sw-page-header :title="$t('customers.pbx_service_view')">
            <template slot="actions">
                <sw-button
                    tag-name="router-link"
                    :to="`/admin/customers/${$route.params.id}/view`"
                    class="mr-3"
                    variant="primary-outline"
                >
                    {{ $t('general.go_back') }}
                </sw-button>

                <sw-select
                    v-model="status_selected"
                    :options="getStatuses"
                    :searchable="true"
                    :show-labels="false"
                    :placeholder="$t('customers.select_a_status')"
                    class="mr-3"
                    label="status_name"
                    @select="(item) => changeStatus(item)"
                    :disabled="isHiddenStatus"
                />

                <sw-dropdown>
                    <sw-button slot="activator" variant="primary">
                        <dots-horizontal-icon class="h-5 -ml-1 -mr-1"/>
                    </sw-button>

                    <div v-if="!isHiddenStatus">

                        <sw-dropdown-item
                            :to="`/admin/customers/${$route.params.id}/pbx-service/${$route.params.pbx_service_id}/edit`"
                            tag-name="router-link"
                        >
                            <pencil-icon class="h-5 mr-3 text-gray-600"/>
                            {{ $t('general.edit') }}
                        </sw-dropdown-item>

                        <sw-dropdown-item
                            :to="`/admin/tickets/main/add-ticket`"
                            tag-name="router-link"
                        >
                            <ticket-icon class="h-5 mr-3 text-gray-600" />
                            {{ $t('customer_ticket.new_ticket') }}
                        </sw-dropdown-item>

                        <!-- {{pbxService}} -->

                        <sw-dropdown-item v-if="pbxService"
                            :to="{
                            name: 'invoices.create',
                            query: {
                                from: 'pbx_services',
                                code: pbxService.pbx_services_number,
                                pbx_service_id: pbxService.id,
                                customer_id: pbxService.customer_id,
                                package_id: pbxService.pbx_package_id,
                            },
                            }"
                            tag-name="router-link"
                        >
                            <calculator-icon class="h-5 mr-3 text-gray-600" />
                            {{ $t('invoices.new_invoice') }}
                        </sw-dropdown-item>

                        <sw-dropdown-item
                            :to="`/admin/customers/${$route.params.id}/pbx-service/${$route.params.pbx_service_id}/callHistory`"
                            tag-name="router-link"
                        >
                            <SearchIcon class="h-5 mr-3 text-gray-600" />
                            {{ $t('general.search_calls') }}
                        </sw-dropdown-item>

                    </div>
                    <sw-dropdown-item
                        :to="`/admin/pbx/packages/${this.pbxService.pbx_package_id}/view/detail`"
                        tag-name="router-link"
                    >
                        <ticket-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.go_to_packages') }}
                    </sw-dropdown-item>
             
                </sw-dropdown>
            </template>
        </sw-page-header>

        <sw-card class="flex flex-col mt-6">
            <!-- Customer basic info -->
            <customer-info/>

            <!-- Service details -->
            <service-detail
                @status="setStatus"
            />

        </sw-card>

    </base-page>
</template>

<script>
import {
    DotsHorizontalIcon,
    PencilIcon,
    TrashIcon,
    CalculatorIcon
} from '@vue-hero-icons/solid'
import { TicketIcon, SearchIcon } from "@vue-hero-icons/outline"
import CustomerInfo from './partials/CustomerInfo'
import ServiceDetail from "./partials/PbxServiceDetail";
import {mapActions, mapGetters} from "vuex";

export default {
    components: {
        DotsHorizontalIcon,
        PencilIcon,
        TrashIcon,
        TicketIcon,
        SearchIcon,
        CustomerInfo,
        ServiceDetail,
        CalculatorIcon
    },
    data() {
        return {
            isRequestOnGoing: false,
            isHiddenStatus: false,
            status_selected: null,
            pbxService: [],
            status: [
                {name: 'Active', value: 'A'},
                {name: 'Pending', value: 'P'},
                {name: 'Suspend', value: 'S'},
                {name: 'Cancelled', value: 'C'}
            ]
        }
    },
    computed: {
        ...mapGetters('customer', ['selectedViewCustomer']),
        ...mapGetters('pbxService', ['selectedPbxService']),

        getStatuses() {
            return this.status.map((status) => {
                return {
                    ...status,
                    status_name: 'Status: ' + status.name
                }
            })
        }

    },
    created() {
        this.loadCustomer()
    },
    methods: {
        ...mapActions('customer', ['fetchViewCustomer']),
        ...mapActions('pbxService', ['updateServiceStatus', 'deleteService', 'fetchPbxService']),

        async loadCustomer() {
            await this.fetchViewCustomer({id: this.$route.params.id})
            await this.fetchPbxService(this.$route.params.pbx_service_id)
            
            this.pbxService = this.selectedPbxService;

            if(this.status_selected.value == 'C'){
                this.isHiddenStatus = true
            }
        },

        async removeService(id) {
            window
                .swal({
                    title: this.$t('general.are_you_sure'),
                    text: 'you will not be able to recover this service!',
                    icon: '/assets/icon/trash-solid.svg',
                    buttons: true,
                    dangerMode: true,
                })
                .then(async (value) => {
                    if (value) {
                        let request = await this.deleteService({ids: [id]})
                        if (request.data.success) {
                            window.toastr['success'](this.$t('pbx_services.deleted_message'))
                            this.$router.push(`/admin/customers/${this.$route.params.id}/view`)
                        } else if (request.data.error) {
                            window.toastr['error'](request.data.message)
                        }
                    }
                })
        },

        async setStatus(val) {
            this.status_selected = this.getStatuses.find(
                (_status) => val === _status.value
            )
        },

        async changeStatus(item) {
           
            let old_status = this.status_selected.value
            
            swal({
                title: this.$t('general.are_you_sure'),
                text: this.$tc('general.change_status_confirm'),
                icon: '/assets/icon/check-circle-solid.svg',
                buttons: true
            }).then(async (result) => {
                if (result) { 
                    this.isRequestOnGoing= true
                    let data = {
                        id: this.$route.params.pbx_service_id,
                        status: item.value,
                    }
                    let response = await this.updateServiceStatus(data)

                    if (response.data.success) {
                        this.$router.go();
                        window.toastr['success'](this.$t('pbx_services.updated_message'))
                    }
                    if(this.status_selected.value == 'C'){
                        this.isHiddenStatus = true
                    }
                } else {
                    isRequestOnGoing = false
                  await this.setStatus(old_status)
                }
            })
        }
    }
}
</script>

<style scoped>

</style>