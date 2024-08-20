<template>
  <!------------ ESTIMATES ----------->
  <div class="tabs mb-5 grid col-span-12 pt-6">
    <div class="border-b tab">
      <div class="relative">
        <input
          class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
          type="checkbox"
          id="chck3"
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
          for="chck3"
        >
          <span class="text-gray-500 uppercase sw-section-title">
            {{ $tc('estimates.estimate_assigned') }}
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
        <div class="tab-content">
          <div class="text-grey-darkest">
            <sw-tabs
              :active-tab="activeEstimateTab"
              @update="setEstimateStatusFilter"
            >
            <sw-tab-item :title="$t('general.all')" filter="" />
            <sw-tab-item :title="$t('general.draft')" filter="DRAFT" />
            <sw-tab-item :title="$t('general.sent')" filter="SENT" />
            <sw-tab-item :title="$t('general.viewed')" filter="VIEWED" />
            <sw-tab-item :title="$t('general.expired')" filter="EXPIRED" />
            <sw-tab-item :title="$t('general.accepted')" filter="ACCEPTED" />
            <sw-tab-item :title="$t('general.rejected')" filter="REJECTED" />
            </sw-tabs>
          </div>

          <sw-table-component
            ref="estimates_table"
            :show-filter="false"
            :data="fetchEstimatesData"
            table-class="table"
          >
            <sw-table-column
              :sortable="true"
              :label="$t('estimates.date')"
              sort-as="estimate_date"
              show="formattedEstimateDate"
            />

            <sw-table-column
              :sortable="true"
              :label="$tc('estimates.estimate', 1)"
              show="estimate_number"
            >
              <template slot-scope="row">
                <span>{{ $tc('estimates.estimate', 1) }}</span>
                <router-link :to="`/admin/estimates/${row.id}/view`" class="font-medium text-primary-500"
                 v-if="readEstimates">
                  {{ row.estimate_number }}
                </router-link>
                <span v-else>
                  {{ row.estimate_number }}
                </span>
              </template>
            </sw-table-column>

            <sw-table-column
              :sortable="true"
              :label="$t('estimates.status')"
              show="status"
            >
              <template slot-scope="row">
                <span> {{ $t('estimates.status') }}</span>
                <sw-badge
                  :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
                  :color="$utils.getBadgeStatusColor(row.status).color"
                  class="px-3 py-1"
                >
                  {{ row.status }}
                </sw-badge>
              </template>
            </sw-table-column>

            <sw-table-column
              :sortable="true"
              :label="$t('estimates.total')"
              sort-as="total"
            >
              <template slot-scope="row">
                <span> {{ $t('estimates.total') }}</span>
                <div
                  v-html="$utils.formatMoney(row.total, row.user.currency)"
                />
              </template>
            </sw-table-column>
          </sw-table-component>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
  props: {
    userId: {
      type: Number,
      required: true,
    },
    readEstimates: {
      type: Boolean
    }
  },
  data: () => ({
    estimate_status: '',
    activeEstimateTab: 'All',
  }),
  methods: {
    ...mapActions('users', ['getStimatesAssignedUser']),

    setEstimateStatusFilter(val) {
      if (this.activeTab == val.title) {
        return true
      }

      this.activeTab = val.title
      switch (val.title) {
        case this.$t('general.draft'):
          this.estimate_status = 'DRAFT'
          this.$router.push({
            query: {
              status: 'DRAFT',
            },
          })
          break

        case this.$t('general.sent'):
          this.estimate_status = 'SENT'
          this.$router.push({
            query: {
              status: 'SENT',
            },
          })
          break

        case this.$t('general.viewed'):
          this.estimate_status = 'VIEWED'
          this.$router.push({
            query: {
              status: 'VIEWED',
            },
          })
          break

        case this.$t('general.expired'):
          this.estimate_status = 'EXPIRED'
          this.$router.push({
            query: {
              status: 'EXPIRED',
            },
          })
          break

        case this.$t('general.accepted'):
          this.estimate_status = 'ACCEPTED'
          this.$router.push({
            query: {
              status: 'ACCEPTED',
            },
          })
          break

        case this.$t('general.rejected'):
          this.estimate_status = 'REJECTED'
          this.$router.push({
            query: {
              status: 'REJECTED',
            },
          })
          break

        default:
          this.estimate_status = ''
          
          this.$router.push({
            query: {},
          })
          break
      }
      this.$refs.estimates_table.refresh()
    },
    // setEstimateStatusFilter(val) {
    //   if (this.activeEstimateTab === val.title) return true

    //   if(val.title == this.$t('general.all')) {
    //     this.estimate_status = ''
    //     this.activeEstimateTab = this.$t('general.all')
    //   } else if(val.title == this.$t('general.sent')) {
    //     this.estimate_status = 'SENT'
    //     this.activeEstimateTab = this.$t('general.sent')
    //   } else if(val.title == this.$t('general.pending')) {
    //     this.estimate_status = 'PENDING'
    //     this.activeEstimateTab = this.$t('general.pending')
    //   }
    //   this.$refs.estimates_table.refresh()
    // },
    async fetchEstimatesData({ page, filter, sort }) {
      try {
          let data = {
                id: this.userId,
                status: this.estimate_status,
                orderByField: sort.fieldName || 'created_at',
                orderBy: sort.order || 'desc',
                page,
                perPage: 10,
            }

            let response = await this.getStimatesAssignedUser(data)

            let list = response.data.estimates.data.map((estimate) => {
                return {
                ...estimate,
                }
            })

            return {
                data: list,
                pagination: {
                    totalPages: response.data.estimates.last_page,
                    currentPage: page,
                    count: response.data.estimates.count,
                },
            }
      } catch (error) {
       // console.log(error) 
      }
    },
  },
}
</script>

<style>
</style>