<template>
    <!--------- Form ---------->
    <form action="" @submit.prevent="submitData">
        <!-- Header  -->
        <sw-page-header class="mb-3" :title="$t('core_pos.tables.new_table')">
            <sw-breadcrumb slot="breadcrumbs">
                <sw-breadcrumb-item to="/admin/corePOS" :title="$t('core_pos.home')" />
                <sw-breadcrumb-item to="/admin/corePOS/tables" :title="$t('general.tables')" />
                <sw-breadcrumb-item v-if="$route.name === 'corepos.createtable.edit'" to="#" :title="'Edit Table'" active />
                <sw-breadcrumb-item v-else to="#" :title="$t('core_pos.tables.new_table')" active />
            </sw-breadcrumb>
            <!--------- Buttons Cancel and Submit Cash Register ---------->
            <template slot="actions">
                <sw-button @click="redirectDashboard" variant="primary-outline" size="lg" type="button" class="mr-2">
                    {{ $t("core_pos.cancel") }}
                </sw-button>
                <sw-button variant="primary" type="submit" size="lg" class="flex justify-center w-full md:w-auto">
                    <save-icon class="mr-2 -ml-1" />
                    {{ $t('core_pos.tables.save_table') }}
                </sw-button>
            </template>
            <!--------- /Buttons Cancel and Submit Cash Register ---------->
        </sw-page-header>

        <hr />
        <div class="grid grid-cols-12">
            <div class="col-span-12">
                <sw-card class="mb-8">
                    <sw-input-group :label="$t('core_pos.tables.table_name')" class="mb-4" required>
                        <sw-input v-model="formData.name" class="mt-2" focus type="text" name="name" />
                    </sw-input-group>

                    <!-- <sw-input-group :label="$t('core_pos.tables.user')" class="mb-4" required>
                        <sw-input v-model="formData.user_id" class="mt-2" focus type="number" name="user_id" tabindex="1"
                            @input="$v.formData.user_id.$touch()" />
                    </sw-input-group> -->

                    <sw-input-group :label="$t('core_pos.cash_registers')" class="mb-4" required>
                        <sw-select v-model="formData.cash_registers" :options="cash_register_options" :searchable="true"
                            :show-labels="false" class="mt-2" track-by="id" label="name" :multiple="true" :tabindex="10" />
                    </sw-input-group>
                </sw-card>
            </div>
        </div>
        <!-- <hr /> -->
    </form>
</template>

<script>

import draggable from 'vuedraggable'
import { mapActions, mapGetters } from 'vuex'
import Guid from 'guid'
import {
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
    CloudUploadIcon,
    XCircleIcon
} from '@vue-hero-icons/solid'

const {
    required,
    minLength,
    maxLength,
} = require('vuelidate/lib/validators')

export default {
    components: {
        draggable,
        ChevronDownIcon,
        PencilIcon,
        ShoppingCartIcon,
        HashtagIcon,
        CloudUploadIcon,
        XCircleIcon
    },
    data() {
        return {
            isLoading: false,
            formData: {
                id: null,
                name: '',
                user_id: '',
                cash_registers: []
            },
            cash_register_options: []
        }
    },

    computed: {
        ...mapGetters('user', ['currentUser']),


        isSuperAdmin() {
            return this.currentUser.role == 'super admin'
        },

    },

    created() {
        this.loadData()

        if (this.$route.name == 'tables.edit') {
            this.isEdit = true
            this.loadTable()
        }
    },

    methods: {
        ...mapActions('modal', ['openModal']),
        ...mapActions('customer', ['fetchCustomers']),
        ...mapActions('users', ['fetchUsers']),
        ...mapActions('corePos', ['fetchStores', 'deleteStore', 'fetchCashRegisterUserall', 'fetchTable']),

        async loadData() {
            const response = await this.fetchCashRegisterUserall()
            this.cash_register_options = response.data.data
        },

        async loadTable(){
            let responseTable = await this.fetchTable(this.$route.params)
            this.formData.name = responseTable.data.table.name
            this.formData.cash_registers = responseTable.data.table.cash_register
        },

        async submitData() {

            swal({
                title: this.$t('general.are_you_sure'),
                text: this.$t('core_pos.tables.add_table_message'),
                icon: '/assets/icon/file-alt-solid.svg',
                buttons: [this.$t('core_pos.cancel'), true],
                dangerMode: true,
            }).then(async (value) => {


                try {
                    let response;
                    this.isLoading = true;
                    if (this.isEdit) {
                        this.formData.id = this.$route.params.id;

                        response = await window.axios.put(`/api/v1/core-pos/tables/${this.$route.params.id}`,
                            this.formData)

                        if (response.data.success) {
                            window.toastr['success'](response.data.message);
                            this.$router.push('/admin/corePOS/tables');
                        }
                        if (response.data.error) {
                            this.isLoading = false;
                            window.toastr['error'](response.data.error);
                            return true;
                        }
                    } else {
                        response = await window.axios.post('/api/v1/core-pos/tables', this.formData)

                        if (response.data.success) {
                            window.toastr['success'](response.data.message);
                            this.$router.push('/admin/corePOS/tables');
                        }
                        if (response.data.error) {
                            this.isLoading = false;
                            window.toastr['error'](response.data.error);
                            return true;
                        }
                    }

                } catch (err) {
                    this.isLoading = false;
                }

            })
        },
        redirectDashboard() {
            swal({
                title: this.$t('general.are_you_sure'),
                text: this.$t('core_pos.back_cash_register'),
                icon: '/assets/icon/file-alt-solid.svg',
                //buttons: true,
                buttons: [this.$t('core_pos.cancel'), true],
                dangerMode: true,
            }).then(async (value) => {
                if (value) {
                    this.$router.push('/admin/corePOS/tables')
                }
            })
        },
    }
}
</script>

<style scoped></style>