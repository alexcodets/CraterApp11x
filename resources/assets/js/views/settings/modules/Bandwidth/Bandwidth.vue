<template>
  <div class="relative">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card variant="setting-card">
      <sw-page-header
        class="mb-3"
        :title="$t('bandwidth.title')"
      >
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            to="/admin/dashboard"
            :title="$t('general.home')"
          />
          <sw-breadcrumb-item
            to="/admin/settings/modules"
            :title="$t('settings.customization.modules.title')"
          />
          <sw-breadcrumb-item
            to="#"
            :title="$t('bandwidth.bandwidth')"
            active
          />
        </sw-breadcrumb>

        <template slot="actions">
          <sw-button
            tag-name="router-link"
            to="bandwidth/add-config"
            variant="primary"
            size="lg"
            class="ml-4"
          >
            <plus-sm-icon class="w-6 h-6 mr-1 -ml-2" />
            {{ $t('bandwidth.add_config') }}
          </sw-button>
        </template>
      </sw-page-header>

      <div class="relative table-container overflow-x-scroll">
        <div class="relative flex items-center justify-between h-10 mt-5 border-b-2 border-gray-200 border-solid">
          <p class="text-sm">
            {{ $t('general.showing') }}: <b>{{ bandwidths.length }}</b>
            {{ $t('general.of') }} <b>{{ totalBandwidths }}</b>
          </p>
        </div>

        <sw-table-component
          ref="table"
          :show-filter="false"
          :data="fetchData"
          table-class="table"
        >

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('bandwidth.account')"
            show="account_name"
          />

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('bandwidth.user')"
            show="user"
          />

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('bandwidth.url')"
            show="url"
          />

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('bandwidth.account')"
            show="account_id"
          />

          <sw-table-column
            :sortable="true"
            :label="$t('bandwidth.default')"
          >
            <template slot-scope="row">
              <div class="relative w-12">
                <sw-switch
                  :ref="`switch-${row.id}`"
                  :value="row.is_default"
                  class="absolute"
                  style="top: -30px"
                  @change="setDefault(row.id, $event)"
                />
              </div>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="false"
            :filterable="false"
            cell-class="action-dropdown"
          >
            <template slot-scope="row">
              <span> {{ $t('general.actions') }} </span>

              <sw-dropdown>
                <dot-icon slot="activator" />

                <sw-dropdown-item
                  :to="`bandwidth/${row.id}/edit-config`"
                  tag-name="router-link"
                >
                  <pencil-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.edit') }}
                </sw-dropdown-item>

                <sw-dropdown-item @click="removeBandwidth(row.id)">
                  <trash-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.delete') }}
                </sw-dropdown-item>
              </sw-dropdown>
            </template>
          </sw-table-column>

        </sw-table-component>
      </div>
    </sw-card>
  </div>
</template>

<script>
import {
  PlusSmIcon,
  TrashIcon,
  PencilIcon,
} from '@vue-hero-icons/solid'
import { mapGetters, mapActions } from "vuex";

export default {
  components: {
    PlusSmIcon,
    TrashIcon,
    PencilIcon
  },

  data() {
    return {
      isRequestOnGoing: false,
    }
  },

  computed: {
    ...mapGetters('bandwidth', [
      'bandwidths',
      'totalBandwidths'
    ]),
  },

  methods: {
    ...mapActions('bandwidth', [
      'fetchBandwidths',
      'deleteBandwidth',
      'updateDefaultBandwidth'
    ]),

    async fetchData({page, filter, sort}) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchBandwidths(data)
      this.isRequestOngoing = false

      return {
        data: response.data.bandwidths.data,
        pagination: {
          totalPages: response.data.bandwidths.last_page,
          currentPage: page,
        },
      }
    },

    async removeBandwidth(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('bandwidth.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          try {
            await this.deleteBandwidth({ id: id })
            this.$refs.table.refresh()
            window.toastr['success'](this.$tc('bandwidth.deleted_message', 1))
          } catch (error) {
            const objectErrors = error.response.data.errors
            if (objectErrors) {
              Object.keys(objectErrors).map((key) => {
                objectErrors[key].map((error) => {
                  window.toastr['error'](error)
                })
              })
            }
          }
        }
      })
    },

    async setDefault(id, value) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: 'Bandwidth default will be changed',
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true
      }).then(async (result) => {
        if (result) {
          try {
            let data = {
              id: id,
              default: value
            }
            await this.updateDefaultBandwidth(data)
            this.$refs.table.refresh()
            window.toastr['success'](this.$t('bandwidth.updated_message'))
          } catch (error) {
            const objectErrors = error.response.data.errors
            if (objectErrors) {
              Object.keys(objectErrors).map((key) => {
                objectErrors[key].map((error) => {
                  window.toastr['error'](error)
                })
              })
            }
          }
        } else {
          this.$refs['switch-'+id].$data.checkValue = !value
        }
      })
    }

  }
}
</script>

<style scoped>

</style>