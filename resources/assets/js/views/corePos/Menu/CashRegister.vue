<template>
    <base-page class="customer-create">

        <!------------------- Cabecera  ----------------->
        <sw-page-header :title="$t('core_pos.cash_registers')">
            <sw-breadcrumb slot="breadcrumbs">
                <sw-breadcrumb-item :title="$t('general.home')" to="/admin/corePOS" />
                <sw-breadcrumb-item :title="$t('core_pos.cash_registers')" to="cash-register" active />
            </sw-breadcrumb>

            <template slot="actions">
                <sw-button size="lg" variant="primary-outline" @click="toggleFilter">
                    {{ $t('general.filter') }}
                    <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
                </sw-button>
                <sw-button tag-name="router-link" to="create-cash-register" size="lg" variant="primary" class="ml-4">
                    <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
                    {{ $t('core_pos.add_cash_register') }}
                </sw-button>
            </template>
        </sw-page-header>

        <!--   Filtros     -->

        <slide-y-up-transition>
            <sw-filter-wrapper v-show="showFilters">
                <sw-input-group :label="$t('item_groups.name')" class="flex-1 mt-2">
                    <sw-input v-model="filters.name" type="text" name="name" class="mt-2" autocomplete="off" />
                </sw-input-group>

                <sw-input-group :label="$t('item_groups.description')" class="flex-1 mt-2 ml-0 lg:ml-6">
                    <sw-input v-model="filters.description" type="text" name="description" class="mt-2"
                        autocomplete="off" />
                </sw-input-group>

                <!-- filtrado por nombre de tienda ////////////////////////////////////////////////////////////////////////// -->

                <sw-input-group :label="$t('item_groups.stores')" class="flex-1 mt-2 ml-0 lg:ml-6">
                    <!-- <sw-input v-model="filters.store_name" type="text" class="mt-2" autocomplete="off" /> -->

                    <sw-select v-model="filters.store_name" :options="options_stores" :searchable="true"
                        :show-labels="false" class="mt-2" track-by="id" label="name" :multiple="false" :tabindex="10" />
                </sw-input-group>

                <label class="absolute text-sm leading-snug text-black cursor-pointer" style="top: 10px; right: 15px"
                    @click="clearFilter">{{ $t('general.clear_all') }}</label>
            </sw-filter-wrapper>
        </slide-y-up-transition>
        <hr />
        <div class="relative table-container">

            <!-------------------------- Tabla -------------------------->

            <sw-table-component ref="table" :show-filter="false" :data="fetchData" table-class="table">

                <sw-table-column :sortable="true" :label="$t('item_groups.name')" sort-as="name" show="name">
                    <template slot-scope="row">
                        <span>{{ $t('core_pos.cash_register') }}</span>
                        <router-link :to="{
                            path: `/admin/corePOS/cash-register/${row.id}/view`,
                        }" class="font-medium text-primary-500">
                            {{ row.name }}
                        </router-link>
                    </template>
                </sw-table-column>

                <sw-table-column :sortable="true" :label="$t('item_groups.description')" sort-as="description"
                    show="description">
                    <template slot-scope="row">
                        {{ row.description ? row.description : 'None' }}
                    </template>
                </sw-table-column>

                <!-- <sw-table-column :sortable="true" :label="$t('item_groups.stores')" sort-as="store_id" show="store_id">
                    <template slot-scope="row"> -->
                <!-- {{ row.store_name ? row.store_name : 'None' }} -->
                <!-- {{ getStore(row.store_id ? row.store_id : 0) }} -->
                <!-- </template>
                </sw-table-column> -->

                <!-- Tabla por nombre de tienda ////////////////////////////////////////////////////////////////////////// -->

                <sw-table-column :sortable="true" :label="$t('item_groups.store')" sort-as="store_name" show="store_name">
                    <template slot-scope="row">
                        <span>{{ $t('item_groups.store') }}</span>
                        {{ row.store_name }}
                    </template>
                </sw-table-column>

                <sw-table-column :sortable="true" :label="$t('item_groups.device')" sort-as="device" show="device">
                </sw-table-column>

                <sw-table-column :sortable="true" :label="$t('core_pos.open_close_cash_modal.status')" sort-as="open_cash"
                    show="open_cash">
                    <template slot-scope="row">
                        <!-- <div v-if="row.cash_history.length != 0"> -->
                        <div>
                            <!-- <sw-badge :bg-color="getStatus(row.cash_history[row.cash_history.length - 1].open).bgColor"
                                :color="getStatus(row.cash_history[row.cash_history.length - 1].open).color">
                                {{ $t(getStatus(row.cash_history[row.cash_history.length - 1].open).text) }}
                            </sw-badge> -->
                            <sw-badge :bg-color="getStatus(row.open_cash).bgColor" :color="getStatus(row.open_cash).color">
                                {{ $t(getStatus(row.open_cash).text) }}
                            </sw-badge>

                        </div>
                        <!-- <div v-else>
                            <sw-badge bg-color="#FED7D7" color="#c53030">
                                {{ $t('core_pos.open_close_cash_modal.closed') }}
                            </sw-badge>
                        </div> -->
                    </template>
                </sw-table-column>

                <sw-table-column :sortable="false" :filterable="false">
                    <template slot-scope="row">
                        <sw-dropdown>
                            <dot-icon slot="activator" />

                            <span>
                                <sw-dropdown-item v-if="permissionModule.access" @click="cashRegisterModal(row)">
                                    <span>

                                        <span v-if="row.open_cash == 1" class="flex">
                                            <x-circle-icon class="h-5 mr-1 text-gray-600" />
                                            <span>
                                                {{ $t('core_pos.close_cash_register') }}
                                            </span>
                                        </span>
                                        <span v-else class="flex">
                                            <check-circle-icon class="h-5 mr-1 text-gray-600" />
                                            <span>
                                                {{ $t('core_pos.open_cash_register') }}
                                            </span>
                                        </span>
                                    </span>
                                    <!-- <div v-else class="flex">
                                        <check-circle-icon class="h-5 mr-1 text-gray-600" />
                                        <span>
                                            {{ $t('core_pos.open_cash_register') }}
                                        </span>
                                    </div> -->
                                </sw-dropdown-item>

                                <sw-dropdown-item @click="redirectCashRegisterInformation(row)">
                                    <eye-icon class="h-5 mr-3 text-gray-600" />
                                    {{ $t('core_pos.information_cash_register') }}
                                </sw-dropdown-item>

                                <sw-dropdown-item tag-name="router-link" :to="`create-cash-register/${row.id}/edit`">
                                    <pencil-icon class="h-5 mr-3 text-gray-600" />
                                    {{ $t('general.edit') }}
                                </sw-dropdown-item>

                                <sw-dropdown-item @click="deleteMoney(row.id)">
                                    <trash-icon class="h-5 mr-3 text-gray-600" />
                                    {{ $t('general.delete') }}
                                </sw-dropdown-item>
                            </span>

                        </sw-dropdown>
                    </template>
                </sw-table-column>

            </sw-table-component>
        </div>
    </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { PlusSmIcon } from '@vue-hero-icons/solid'
import {
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    TrashIcon,
    PencilIcon,
    EyeIcon,
} from '@vue-hero-icons/solid'
import { CheckCircleIcon, XCircleIcon } from "@vue-hero-icons/outline"
import AstronautIcon from '../../../components/icon/AstronautIcon'

export default {
    components: {
        AstronautIcon,
        ChevronDownIcon,
        PlusSmIcon,
        FilterIcon,
        XIcon,
        TrashIcon,
        PencilIcon,
        EyeIcon,
        CheckCircleIcon,
        XCircleIcon
    },
    data() {
        return {
            options_stores: [],
            showFilters: false,
            isRequestOngoing: true,
            permissionModule: {
                access: false
            },
            filters: {
                name: '',
                description: '',
                store_name: '',
            },
        }
    },
    computed: {
        ...mapGetters(['store']),
        filterIcon() {
            return this.showFilters ? 'x-icon' : 'filter-icon'
        },
    },
    created() {
        this.permissionsUserModule()
        this.loadData()
    },
    watch: {
        filters: {
            handler: 'setFilters',
            deep: true,
        },
    },
    destroyed() {
    },
    methods: {

        ...mapActions('corePos', ['fetchStores']),
        ...mapActions('modal', ['openModal', 'closeModal']),
        ...mapActions('user', ['getUserModules']),

        async loadData() {
            let data = {
                orderByField: 'created_at',
                orderBy: 'desc',
                limit: "all",
            }

            this.isRequestOngoing = true
            const response = await this.fetchStores(data)
            //console.log(response);
            this.options_stores = response.data.stores.data


        },

        refreshTable() {
            this.$refs.table.refresh()
        },
        async fetchData({ page, filter, sort }) {

            let data = {
                name: this.filters.name,
                description: this.filters.description,
                store_name: this.filters.store_name == null ? "" : this.filters.store_name.name,
                //store_name: this.filters.store_name,
                orderByField: sort.fieldName || 'id',
                orderBy: sort.order || 'desc',
                page,
            }

            this.isRequestOngoing = true
            let response = await window.axios.post(`/api/v1/core-pos/cash-register/getCashRegisters`, data)

            this.isRequestOngoing = false

            return {
                data: response.data.cash_registers.data,
                pagination: {
                    totalPages: response.data.cash_registers.last_page,
                    currentPage: page,
                },
            }

        },
        setFilters() {
            this.refreshTable()
        },
        clearFilter() {
            this.filters = {
                name: '',
                description: '',
                store_name: '',
            }
        },
        toggleFilter() {
            if (this.showFilters) {
                this.clearFilter()
            }
            this.showFilters = !this.showFilters
        },

        async deleteMoney(id) {
            swal({
                title: this.$t('general.are_you_sure'),
                text: 'You will not be able to retrieve this record',
                icon: '/assets/icon/trash-solid.svg',
                buttons: true,
                dangerMode: true,
            }).then(async (value) => {
                if (value) {
                    let res = await window.axios.get(`/api/v1/core-pos/cash-register/deleteCashRegister/${id}`)

                    if (res.data.success) {
                        window.toastr['success'](res.data.message)
                        this.$refs.table.refresh()
                        return true
                    }

                    window.toastr['error']('Error')
                    return true
                }
            })
        },


        cashRegisterModal(row) {

            let title = 'core_pos.open_cash_register'
            if (row.open_cash != 0) {
                if (row.open_cash == 1) {
                    title = 'core_pos.close_cash_register'
                }
            }
            this.openModal({
                title: this.$t(title),
                componentName: 'openCloseCashRegisterModal',
                data: row
            })
        },

        getStatus(isOpen) {
            switch (isOpen) {
                case 1:
                    return {
                        bgColor: '#D5EED0',
                        color: '#276749',
                        text: "core_pos.open_close_cash_modal.opened"
                    }
                case 0:
                    return {
                        bgColor: '#FED7D7',
                        color: '#c53030',
                        text: "core_pos.open_close_cash_modal.closed"
                    }
            }
        },

        redirectCashRegisterInformation(row) {
            try {
                this.$router.push(`/admin/corePOS/cash-register/${row.id}/view`);
            } catch (error) {

            }
        },

        async permissionsUserModule() {
            const data = {
                module: "open_close_cash_register",
            };
            const permissions = await this.getUserModules(data);
            // valida que el usuario tenga el permiso create, read, delete, update
            if (permissions.super_admin == true) {
                this.permissionModule.access = true;
            } else if (permissions.exist == true && permissions.permissions[0] != null) {
                const modulePermissions = permissions.permissions[0];
                if (modulePermissions.access == 1) {
                    this.permissionModule.access = true;
                }
            }
        },

    }
}
</script>
