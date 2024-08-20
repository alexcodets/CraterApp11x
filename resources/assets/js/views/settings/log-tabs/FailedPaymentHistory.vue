<template>
    <div>
        <div class="flex flex-wrap justify-end mt-8 lg:flex-nowrap">
            <sw-button
                v-show="totalFailedPaymentHistory"
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
                        :label="$t('logs.failed_payment_history.payment_number')"
                        class="mt-2"
                    >
                        <sw-input
                            v-model="filters.payment_number"
                            type="text"
                            name="name"
                            autocomplete="off"
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
                    :label="$t('logs.failed_payment_history.payment_gateway')"
                    show="payment_gateway"
                />
                <sw-table-column
                    :sortable="true"
                    :label="$t('logs.failed_payment_history.transaction_number')"
                    show="transaction_number"
                />
                <sw-table-column
                    :sortable="true"
                    :label="$t('logs.failed_payment_history.date')"
                    show="date"
                />
                <sw-table-column
                    :sortable="true"
                    :label="$t('logs.failed_payment_history.amount')"
                    show="amount"
                />
                <sw-table-column
                    :sortable="true"
                    :label="$t('logs.failed_payment_history.payment_number')"
                    show="payment_number"
                />
                <sw-table-column
                    :sortable="true"
                    :label="$t('logs.failed_payment_history.customer')"
                    show="company_name"
                />
                <sw-table-column
                    :sortable="true"
                    :label="$t('logs.failed_payment_history.invoice')"
                    show="invoice_number"
                />
                <sw-table-column
                    :sortable="true"
                    :label="$t('logs.failed_payment_history.description')"
                    show="description"
                />
            </sw-table-component>
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
                from_date: '',
                to_date: '',
                payment_number: '',
            },
        }
    },

    computed: {
        ...mapGetters('failedPaymentHistory', [
            'totalFailedPaymentHistory',
        ]),

        filterIcon() {
            return this.showFilters ? 'x-icon' : 'filter-icon'
        },
    },
    created() {
        // this.loadData()
    },
    watch: {
        filters: {
            handler: 'setFilters',
            deep: true,
        },
    },
    methods: {
        ...mapActions('failedPaymentHistory', [
            'fetchFailedPaymentHistory',
        ]),

        async fetchData({ page, filter, sort }) {
            let data = {
                payment_number: this.filters.user,
                from_date: this.filters.from_date,
                to_date: this.filters.to_date,
                orderByField: sort.fieldName || 'created_at',
                orderBy: sort.order || 'desc',
                page,
            }

            let response = await this.fetchFailedPaymentHistory(data);
          
            response.data.failed_payment_history.data.forEach(element => {
                //console.log(element);
                if(element.transaction_number == 0){
                    element.transaction_number = "Not Number";
                }
            });
            //console.log('fetchFailedPaymentHistory', response.data.failed_payment_history.data[0].transaction_number);
            
            

            return {
                data: response.data.failed_payment_history.data,
                pagination: {
                    totalPages: response.data.failed_payment_history.last_page,
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
                from_date: '',
                to_date: '',
                payment_number: '',
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