<template>
    <div>
        <div class="flex flex-wrap justify-end mt-8 lg:flex-nowrap">
            <sw-button
                v-show="totalModuleLogs"
                size="lg"
                variant="primary-outline"
                @click="toggleFilter"
            >
                {{ $t('general.filter') }}
                <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
            </sw-button>
        </div>

        <!----------------------   Filters    ------------------->
        <slide-y-up-transition>
            <sw-filter-wrapper
                v-show="showFilters"
                class="relative grid grid-rows grid-flow-col gap-4"
            >
                <div class="grid grid-cols-3 gap-4">
                    <sw-input-group :label="$t('general.from')" class="mt-2">
                        <base-date-picker
                            v-model="filters.from_date"
                            :calendar-button="true"
                            calendar-button-icon="calendar"
                        />
                    </sw-input-group>

                    <sw-input-group :label="$t('general.to')" class="mt-2">
                        <base-date-picker
                            v-model="filters.to_date"
                            :calendar-button="true"
                            calendar-button-icon="calendar"
                        />
                    </sw-input-group>

                    <sw-input-group
                        :label="$t('logs.module_logs.username')"
                        class="mt-2"
                    >
                        <sw-input
                            v-model="filters.user"
                            type="text"
                            name="name"
                            autocomplete="off"
                        />
                    </sw-input-group>

                    <sw-input-group :label="$t('logs.module_logs.module')" class="mt-2">
                        <sw-select
                            v-model="filters.module"
                            :options="listModules"
                            :searchable="true"
                            :show-labels="false"
                            :placeholder="$t('logs.module_logs.select_a_module')"
                            :allow-empty="false"
                            track-by="module"
                            label="module"
                        />
                    </sw-input-group>

                    <sw-input-group :label="$t('logs.module_logs.task')" class="mt-2">
                        <sw-select
                            v-model="filters.task"
                            :options="listTasks"
                            :searchable="true"
                            :show-labels="false"
                            :placeholder="$t('logs.module_logs.select_a_task')"
                            :allow-empty="false"
                            track-by="task"
                            label="task"
                        />
                    </sw-input-group>
                </div>

                <label
                    class="absolute text-sm leading-snug text-black cursor-pointer"
                    @click="clearFilter"
                    style="top: 10px; right: 15px"
                >{{ $t('general.clear_all') }}</label
                >
            </sw-filter-wrapper>
        </slide-y-up-transition>

        <!----------------------   Table     ----------------------->
        <div class="table-content">
            <!-- Table -->
            <sw-table-component
                ref="table"
                variant="gray"
                :show-filter="false"
                :data="fetchData"
            >
                <sw-table-column
                    :sortable="true"
                    :label="$t('logs.module_logs.module')"
                    show="module"
                />
                <sw-table-column
                    :sortable="true"
                    :label="$t('logs.module_logs.task')"
                    show="task"
                />
                <sw-table-column
                    :sortable="true"
                    :label="$t('logs.module_logs.slug')"
                    show="slug"
                />
                <sw-table-column
                    :sortable="true"
                    :label="$t('logs.module_logs.username')"
                    show="username"
                />
                <sw-table-column
                    :sortable="true"
                    :label="$t('logs.module_logs.created_at')"
                    sort-as="created_at"
                    show="formattedCreatedAt"
                />
            </sw-table-component>
            <div class="py-4">
                <strong>{{ $t('logs.time_shown') }}</strong>
            </div>
        </div>
    </div>
</template>

<script>

import { mapActions, mapGetters } from 'vuex'
import {
    FilterIcon,
    XIcon
} from '@vue-hero-icons/solid'

export default {
    components: {
        FilterIcon,
        XIcon,
    },

    data() {
        return {
            showFilters: false,
            moduleList: [],
            filters: {
                module: '',
                task: '',
                from_date: '',
                to_date: '',
                user: '',
            },
        }
    },

    computed: {
        ...mapGetters('log', [
            'totalModuleLogs',
            'listModules',
            'listTasks'
        ]),

        filterIcon() {
            return this.showFilters ? 'x-icon' : 'filter-icon'
        },
    },
    created() {
        this.loadData()
    },
    watch: {
        filters: {
            handler: 'setFilters',
            deep: true,
        },
    },
    methods: {
        ...mapActions('log', [
            'fetchModuleLogs',
            'fetchSearchLists'
        ]),

        async loadData(){
            this.fetchSearchLists({limit: 'all'});
        },

        async fetchData({ page, filter, sort }) {
            let data = {
                module: this.filters.module ? this.filters.module.module : '',
                task: this.filters.task ? this.filters.task.task : '',
                username: this.filters.user,
                from_date: this.filters.from_date,
                to_date: this.filters.to_date,
                orderByField: sort.fieldName || 'created_at',
                orderBy: sort.order || 'desc',
                page,
            }

            let response = await this.fetchModuleLogs(data);

            return {
                data: response.data.moduleLogs.data,
                pagination: {
                    totalPages: response.data.moduleLogs.last_page,
                    currentPage: page,
                },
            }
        },

        toggleFilter() {
            if (this.showFilters) {
                this.clearFilter()
            }

            this.showFilters = !this.showFilters
        },

        clearFilter() {
            this.filters = {
                module: '',
                task: '',
                from_date: '',
                to_date: '',
                user: '',
            }
        },

        setFilters() {
            this.refreshTable()
        },

        refreshTable() {
            this.$refs.table.refresh()
        },
    }
}
</script>

<style scoped>

    .table-content{
        overflow-x: scroll;
    }

</style>