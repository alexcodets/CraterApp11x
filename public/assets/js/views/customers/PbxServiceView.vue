<template>
    <base-page>
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
                />

                <sw-dropdown>
                    <sw-button slot="activator" variant="primary">
                        <dots-horizontal-icon class="h-5 -ml-1 -mr-1"/>
                    </sw-button>

                    <sw-dropdown-item
                        :to="`/admin/customers/${$route.params.id}/pbx-service/${$route.params.pbx_service_id}/edit`"
                        tag-name="router-link"
                    >
                        <pencil-icon class="h-5 mr-3 text-gray-600"/>
                        {{ $t('general.edit') }}
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
            status_selected: null,
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
        ...mapActions('pbxService', ['updateServiceStatus', 'deleteService']),

        async loadCustomer() {
            // console.log('$route.params: ', this.$route);
            await this.fetchViewCustomer({id: this.$route.params.id})
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
                    let data = {
                        id: this.$route.params.pbx_service_id,
                        status: item.value,
                    }
                    let response = await this.updateServiceStatus(data)
                    //  console.log()
                    // this.loadCustomer();

                    if (response.data.success) {
                        // this.$router.push(`/admin/customers/${this.$route.params.id}/view`);
                        // http://crater.local/admin/customers/45/pbx-service/21/view
                        this.$router.go();
                        window.toastr['success'](this.$t('pbx_services.updated_message'))
                    }
                } else {
                  await this.setStatus(old_status)
                }
            })
        }
    }
}
</script>

<style scoped>

</style>