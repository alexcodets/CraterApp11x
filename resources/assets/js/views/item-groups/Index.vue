<template>
    <base-page class="option-groups-create">

        <!------------------- Cabecera  ----------------->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <sw-breadcrumb slot="breadcrumbs">
                <sw-breadcrumb-item :title="$t('general.home')" to="dashboard"/>
                <sw-breadcrumb-item
                    :title="$tc('item_groups.item_group')"
                    to="#"
                    active
                />
            </sw-breadcrumb>

            <div class="flex flex-wrap items-center justify-end">             
                <sw-button
                    tag-name="router-link"
                    :to="`/admin/items`"
                    class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
                    variant="primary-outline"
                    size="lg"
                >
                    {{ $t('general.go_back') }}
                </sw-button>
                
                <sw-button
                    v-show="totalItemGroups"
                    size="lg"
                    variant="primary-outline"
                    @click="toggleFilter"
                    class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
                >
                    {{ $t('general.filter') }}
                    <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold"/>
                </sw-button>

                <sw-button
                    tag-name="router-link"
                    to="item-groups/create"
                    size="lg"
                    variant="primary"
                    class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
                >
                    <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold"/>
                    {{ $t('item_groups.new_items_group') }}
                </sw-button>
            </div>
        </div>

        <!--   Fitros     -->

        <slide-y-up-transition>
            <sw-filter-wrapper v-show="showFilters">
                <sw-input-group
                    :label="$t('item_groups.name')"
                    class="flex-1 mt-2"
                >
                    <sw-input
                        v-model="filters.name"
                        type="text"
                        name="name"
                        class="mt-2"
                        autocomplete="off"
                    />
                </sw-input-group>

                <sw-input-group
                    :label="$t('item_groups.description')"
                    class="flex-1 mt-2 ml-0 lg:ml-6"
                >
                    <sw-input
                        v-model="filters.description"
                        type="text"
                        name="description"
                        class="mt-2"
                        autocomplete="off"
                    />
                </sw-input-group>

                <label
                    class="absolute text-sm leading-snug text-black cursor-pointer"
                    style="top: 10px; right: 15px"
                    @click="clearFilter"
                >{{ $t('general.clear_all') }}</label
                >
            </sw-filter-wrapper>
        </slide-y-up-transition>

        <!--   Si la tabla esta vacia     -->

        <sw-empty-table-placeholder
            v-show="showEmptyScreen"
            :title="$t('item_groups.no_items_groups')"
            :description="$t('item_groups.list_of_items_groups')"
        >
            <astronaut-icon class="mt-5 mb-4" />

            <sw-button
                slot="actions"
                tag-name="router-link"
                to="/admin/item-groups/create"
                size="lg"
                variant="primary-outline"
            >
                {{ $t('item_groups.add_new_items_group') }}
            </sw-button>
        </sw-empty-table-placeholder>

        <!--   Si hay informacion para la tabla     -->

        <div v-show="!showEmptyScreen" class="relative table-container">

            <!-- Fila de utilidades -->
            <div class="relative flex items-center justify-between h-10 mt-5 border-b-2 border-gray-200 border-solid">

                <!-- Informacion de paginacion -->
                <p class="text-sm">
                    {{ $t('general.showing') }}: <b>{{ itemGroups.length }}</b>
                    {{ $t('general.of') }} <b>{{ totalItemGroups }}</b>
                </p>

                <!-- Dropdown para eliminar multiples grupos -->
                <sw-transition type="fade">
                    <sw-dropdown v-if="selectedItemGroups.length">
                        <span
                            slot="activator"
                            class="flex block text-sm font-medium cursor-pointer select-none text-primary-400"
                        >
                            {{ $t('general.actions') }}
                            <chevron-down-icon class="h-5" />
                        </span>

                        <sw-dropdown-item @click="removeMultipleItemGroups">
                            <trash-icon class="h-5 mr-3 text-gray-600" />
                            {{ $t('general.delete') }}
                        </sw-dropdown-item>
                    </sw-dropdown>
                </sw-transition>
            </div>

            <!-- Seleccionar todos los elementos -->
            <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12">
                <sw-checkbox
                    v-model="selectAllFieldStatus"
                    variant="primary"
                    size="sm"
                    class="hidden md:inline"
                    @change="selectAllItemGroups"
                />

                <sw-checkbox
                    v-model="selectAllFieldStatus"
                    :label="$t('general.select_all')"
                    variant="primary"
                    size="sm"
                    class="md:hidden"
                    @change="selectAllItemGroups"
                />
            </div>

            <!-------------------------- Tabla -------------------------->

            <sw-table-component
                ref="table"
                :show-filter="false"
                :data="fetchData"
                table-class="table"
            >
                <sw-table-column
                    :sortable="false"
                    :filterable="false"
                    cell-class="no-click"
                >
                    <div slot-scope="row" class="relative block">
                        <sw-checkbox
                            :id="row.id"
                            v-model="selectField"
                            :value="row.id"
                            variant="primary"
                            size="sm"
                        />
                    </div>
                </sw-table-column>

                <sw-table-column
                    :sortable="true"
                    :filterable="true"
                    :label="$t('item_groups.name')"
                    show="name"
                >
                    <template slot-scope="row">
                        <span>{{ $t('item_groups.name') }}</span>
                        <router-link
                            :to="{ path: `item-groups/${row.id}/view` }"
                            class="font-medium text-primary-500"
                        >
                            {{ row.name }}
                        </router-link>
                    </template>
                </sw-table-column>

                <sw-table-column
                    :sortable="true"
                    :filterable="true"
                    :label="$t('item_groups.description')"
                    show="description"
                >
                    <template slot-scope="row">
                        <span>{{ $t('item_groups.description') }}</span>
                        <span v-html="row.description ? row.description : $t('item_groups.empty')">
                        </span>
                    </template>
                </sw-table-column>

                <sw-table-column
                    :sortable="false"
                    :filterable="false"
                    cell-class="action-dropdown"
                >
                    <template slot-scope="row">
                        <span> {{ $t('customers.action') }} </span>

                        <sw-dropdown>
                            <dot-icon slot="activator" />

                            <sw-dropdown-item
                                :to="`item-groups/${row.id}/edit`"
                                tag-name="router-link"
                            >
                                <pencil-icon class="h-5 mr-3 text-gray-600" />
                                {{ $t('general.edit') }}
                            </sw-dropdown-item>

                            <sw-dropdown-item
                                :to="`item-groups/${row.id}/view`"
                                tag-name="router-link"
                            >
                                <eye-icon class="h-5 mr-3 text-gray-600" />
                                {{ $t('general.view') }}
                            </sw-dropdown-item>

                            <sw-dropdown-item @click="removeItemGroup(row.id)">
                                <trash-icon class="h-5 mr-3 text-gray-600" />
                                {{ $t('general.delete') }}
                            </sw-dropdown-item>
                        </sw-dropdown>
                    </template>
                </sw-table-column>
            </sw-table-component>

        </div>
    </base-page>
</template>

<script>
import {mapActions, mapGetters} from 'vuex'
import {PlusSmIcon} from '@vue-hero-icons/solid'
import {
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    TrashIcon,
    PencilIcon,
    EyeIcon,
} from '@vue-hero-icons/solid'
import AstronautIcon from '../../components/icon/AstronautIcon'

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
    },
    data() {
        return {
            showFilters: false,
            isRequestOngoing: true,
            filters: {
                name: '',
                description: '',
            },
        }
    },
    computed: {
        showEmptyScreen() {
            return !this.totalItemGroups && !this.isRequestOngoing
        },
        filterIcon() {
            return this.showFilters ? 'x-icon' : 'filter-icon'
        },
        ...mapGetters('itemGroups', [
            'itemGroups',
            'selectedItemGroups',
            'totalItemGroups',
            'selectAllField'
        ]),
        selectField: {
            get: function () {
                return this.selectedItemGroups
            },
            set: function (val) {
                this.selectItemGroup(val)
            },
        },
        selectAllFieldStatus: {
            get: function () {
                return this.selectAllField
            },
            set: function (val) {
                this.setSelectAllState(val)
            },
        }
    },
    watch: {
        filters: {
            handler: 'setFilters',
            deep: true,
        },
    },
    destroyed() {
        if (this.selectAllField) {
            this.selectAllItemGroups()
        }
    },
    methods: {
        ...mapActions('itemGroups', [
            'fetchItemGroups',
            'selectAllItemGroups',
            'selectItemGroup',
            'deleteItemGroup',
            'deleteMultipleItemGroups',
            'setSelectAllState'
        ]),
        refreshTable() {
            this.$refs.table.refresh()
        },
        async fetchData({page, filter, sort}) {

            let data = {
                name: this.filters.name,
                description: this.filters.description,
                orderByField: sort.fieldName || 'created_at',
                orderBy: sort.order || 'desc',
                page,
            }

            this.isRequestOngoing = true
            let response = await this.fetchItemGroups(data)
            this.isRequestOngoing = false

            return {
                data: response.data.itemGroups.data,
                pagination: {
                    totalPages: response.data.itemGroups.last_page,
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
                description: ''
            }
        },
        toggleFilter() {
            if (this.showFilters) {
                this.clearFilter()
            }

            this.showFilters = !this.showFilters
        },
        async removeItemGroup(id) {
            swal({
                title: this.$t('general.are_you_sure'),
                text: this.$tc('item_groups.confirm_delete', 1),
                icon: 'error',
                iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
                buttons: {
                    cancel: true,
                    confirm: true,
                }
            }).then(async (result) => {
                if (result) {
                    let res = await this.deleteItemGroup({ids: [id]})

                    if (res.data.success) {
                        window.toastr['success'](this.$tc('item_groups.deleted_message', 1))
                        this.$refs.table.refresh()
                        return true
                    }

                    window.toastr['error'](res.data.error)
                    return true
                }
            })
        },
        async removeMultipleItemGroups() {
            swal({
                title: this.$t('general.are_you_sure'),
                text: this.$tc('item_groups.confirm_delete', 2),
                icon: 'error',
                iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
                buttons: {
                    cancel: true,
                    confirm: true,
                }
            }).then(async (result) => {
                if (result) {
                    let res = await this.deleteMultipleItemGroups()

                    if (res.data.success || res.data.itemGroups ) {
                        window.toastr['success'](this.$tc('item_groups.deleted_message', 2))
                        this.$refs.table.refresh()
                    } else if (res.data.error) {
                        window.toastr['error'](res.data.message)
                    }
                }
            })
        },
    }
}
</script>
