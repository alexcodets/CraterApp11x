<template>
    <base-page>
        <sw-page-header title="Call History Pbx Service">
            <template slot="actions">
                <sw-button
                    tag-name="router-link"
                    :to="`/admin/customers/${$route.params.idCustomer}/pbx-service/${$route.params.idPbxService}/view`"
                    class="mr-3"
                    variant="primary-outline"
                >
                    {{ $t('general.go_back') }}
                </sw-button>
            </template>
        </sw-page-header>

        <sw-card class="flex flex-col mt-6">
            <!-- Customer basic info -->
            <customer-info/>

            <!-- Service details -->
            <headerPbxServices/>

        </sw-card>

    </base-page>
</template>

<script>

import {
    DotsHorizontalIcon,
    PencilIcon,
    TrashIcon,
} from '@vue-hero-icons/solid'
import CustomerInfo from '../partials/CustomerInfo'
import headerPbxServices from "./headerPbxServices";
import {mapActions, mapGetters} from "vuex";

export default {
    components: {
        DotsHorizontalIcon,
        PencilIcon,
        TrashIcon,
        CustomerInfo,
        headerPbxServices
    },
    data: () => ({

    }),
    computed: {
        ...mapGetters('customer', ['selectedViewCustomer']),
    },
    created() {
        this.loadCustomer()
    },
    methods: {
        ...mapActions('customer', ['fetchViewCustomer']),
        ...mapActions('pbxService', ['updateServiceStatus', 'deleteService']),

        async loadCustomer() {
            // console.log('$route.params: ', this.$route);
            await this.fetchViewCustomer({id: this.$route.params.idCustomer})
        },

        
    }
}
</script>