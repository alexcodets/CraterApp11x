<template>
  <!------------ SERVICES PBX ------------>

      <div
        class="tabs mb-5 grid col-span-12 pt-6"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck4"
            />
            <header
              class="
                col-span-5
                flex
                justify-between
                items-center
                py-3
                cursor-pointer
                select-none
                tab-label
              "
              for="chck4"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $t('customers.services_pbx') }}
              </span>
              <div
                class="
                  rounded-full
                  border border-grey
                  w-7
                  h-7
                  flex
                  items-center
                  justify-center
                  test
                "
              >
                <!-- icon by feathericons.com -->
                <svg
                  aria-hidden="true"
                  class=""
                  data-reactid="266"
                  fill="none"
                  height="24"
                  stroke="#606F7B"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  viewbox="0 0 24 24"
                  width="24"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
            </header>
            <div class="tab-content-customer">
              <div class="text-grey-darkest">
                <sw-tabs
                  :active-tab="activeServicesPbxTab"
                  @update="setServicesPbxStatusFilter"
                >
                  <sw-tab-item :title="$t('customers.active')" filter="A" />
                  <sw-tab-item :title="$t('customers.pending')" filter="P" />
                  <sw-tab-item :title="$t('customers.suspend')" filter="S" />
                  <sw-tab-item :title="$t('customers.cancelled')" filter="C" />
                </sw-tabs>
              </div>

              <sw-table-component
                ref="services_pbx_table"
                :show-filter="false"
                :data="fetchPbxServicesData"
                table-class="table"
              >
                 <sw-table-column
                  :sortable="true"
                  :label="$t('services.service_number')"
                >
                  <template slot-scope="row">
                    <span>{{ $t('services.service_number') }}</span>
                    <router-link
                      class="font-medium text-primary-500"
                      :to="row.deleted_at == null ? { path: `/admin/customers/${row.customer_id}/pbx-service/${row.id}/view` } : ''"
                    >
                      {{ row.pbx_services_number }}
                    </router-link>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$tc('packages.package', 1)"
                  show="pbx_package.pbx_package_name"
                />
                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.amount')"
                  sort-as="total"
                >
                  <template slot-scope="row">
                    <span>{{ $t('customers.amount') }}</span>
                    <div
                      v-html="$utils.formatMoney(row.total, row.user.currency)"
                    />
                  </template>
                </sw-table-column>

                <sw-table-column :sortable="true" :label="$t('customers.term')">
                  <template slot-scope="row">
                    <span>{{ $t('customers.term') }}</span>
                    <span>{{ capitalizeFirstLetter(row.term) }}</span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.activation_date')"
                  sort-as="activation_date"
                  show="formattedActivationDate"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.renewal_date')"
                  show="formattedRenewalDate"
                />
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>
</template>

<script>
import {mapActions} from "vuex";
export default {
   data() {
    return {
        activeServicesPbxTab: this.$t('customers.active'),
        services_pbx_status: { name: 'Active', value: 'A' },
    }
   },
    methods: {
        ...mapActions('pbx', [
            'fetchServicesPackages'
          ]),
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1)
        },
        async fetchPbxServicesData({ page, filter, sort }) {
            let data = {
                status: this.services_pbx_status?.value,
                orderByField: sort.fieldName || 'created_at',
                orderBy: sort.order || 'desc',
                page,
                idPackage: this.$route.params.id
            }
            let response = await this.fetchServicesPackages(data)
                //console.log(response, "response")

            return {
                data: response.data.pbxServices.data,
                pagination: {
                totalPages: response.data.pbxServices.last_page,
                currentPage: page,
                count: response.data.pbxServices.count,
                },
            }
        },
        setServicesPbxStatusFilter(val) {
            if (this.activeServicesPbxTab === val.title) {
                return true
            }
            this.activeServicesPbxTab = val.title
            switch (val.title) {
                case this.$t('customers.active'):
                this.services_pbx_status = {
                    name: 'Active',
                    value: 'A',
                }
                break

                case this.$t('customers.pending'):
                this.services_pbx_status = {
                    name: 'Pending',
                    value: 'P',
                }
                break

                case this.$t('customers.suspend'):
                this.services_pbx_status = {
                    name: 'Suspend',
                    value: 'S',
                }
                break

                case this.$t('customers.cancelled'):
                this.services_pbx_status = {
                    name: 'Cancelled',
                    value: 'C',
                }
                break
            }

            this.$refs.services_pbx_table.refresh()
            },

    },
}
</script>

<style>

</style>