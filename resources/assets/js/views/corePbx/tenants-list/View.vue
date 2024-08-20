<template>
  <!-- Base  -->
  <base-page class="tckets-departaments-view">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <!-- Header  -->
    <sw-page-header class="mb-3" :title="$t('corePbx.tenants.view_tenant')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item
          to="/admin/corePBX/tenant/tenants-list"
          :title="$t('general.pbx_tenant')"
        />
      </sw-breadcrumb>
      <template slot="actions">
        <sw-button
          tag-name="router-link"
          :to="`/admin/corePBX/tenant/tenants-list`"
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('general.go_back') }}
        </sw-button>

        <!-- <sw-button
            tag-name="router-link"
            :to="`/admin/corePBX/packages/${$route.params.id}/edit`"
            class="mr-3"
            variant="primary-outline"
            v-if="permissionModule.update"
          >
            {{ $t('general.edit') }}
          </sw-button>
          <sw-button
            @click="removePackage($route.params.id)"
            slot="activator"
            variant="primary"
            v-if="permissionModule.delete"
          >
            {{ $t('general.delete') }}
          </sw-button> -->
      </template>
    </sw-page-header>
    <sw-card>
      <div class="col-span-12">
        <span class="text-gray-500 uppercase sw-section-title">
          {{ $t('item_groups.basic_info') }}
        </span>

        <div class="grid grid-cols-12 gap-4 mt-5 text-center">
          <div class="col-span-6 md:col-span-3 sm:col-span-3">
            <span
              class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
            >
              {{ $t('corePbx.tenants.name') }}
            </span>
            <p class="text-sm font-bold leading-5 text-black non-italic">
              {{ basicInfo?.name }}
            </p>
          </div>

          <div class="col-span-6 md:col-span-3 sm:col-span-3">
            <span
              class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
            >
              {{ $t('corePbx.tenants.code') }}
            </span>
            <div class="text-sm font-bold leading-5 text-black non-italic">
              <p>{{ basicInfo?.tenant_code }}</p>
            </div>
          </div>

          <div class="col-span-6 md:col-span-3 sm:col-span-3">
            <span
              class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
            >
              {{ $t('general.server') }}
            </span>
            <p class="text-sm font-bold leading-5 text-black non-italic">
              {{ basicInfo?.metadata?.server_name }}
            </p>

            <div>
              <span
                v-if="
                  basicInfo &&
                  basicInfo.metadata &&
                  basicInfo.metadata.server_status === 'A'
                "
                class="text-success fs-6"
                style="font-size: 14px"
              >
                {{ $t('settings.customization.modules.server_online') }}
              </span>
              <span v-else class="text-danger fs-6" style="font-size: 14px">
                {{ $t('settings.customization.modules.server_offline') }}
              </span>
            </div>
          </div>

          <div class="col-span-6 md:col-span-3 sm:col-span-3">
            <span
              class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
            >
              {{ $t('corePbx.tenants.created_at') }}
            </span>

            <p
              v-if="basicInfo.created_at"
              class="text-sm font-bold leading-5 text-black non-italic"
            >
              {{ formatDate(basicInfo?.created_at) || '' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Table Pbx Services -->
      <div v-if="basicInfo.name" class="grid col-span-12 gap-1 md:grid-cols-6">
        <sw-divider class="col-span-12 my-8" />
        <div class="col-span-12">
          <h3 class="text-lg font-medium">
            {{ $t('corePbx.tenants.pbx_services') }}
          </h3>
        </div>

        <sw-table-component
          class="col-span-12 md:-mt-0 -mt-12"
          ref="table"
          :data="pbxServerTable"
          :show-filter="false"
          table-class="table"
          variant="gray"
        >
          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.service_number')"
            show="pbx_services_number"
          >
            <template slot-scope="row">
              <span class="bg-danger">{{
                $t('corePbx.tenants.table_view.service_number')
              }}</span>

              <router-link
                :to="routerPath(row)"
                class="font-medium text-primary-500"
              >
                {{ row?.pbx_services_number }}
              </router-link>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.status')"
            show="status"
          >
            <template slot-scope="row">
              <span>{{ $t('corePbx.tenants.table_view.status') }}</span>
              <div v-if="row.status == 'A'">
                <sw-badge
                  :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
                  :color="$utils.getBadgeStatusColor('COMPLETED').color"
                  class="px-3 py-1"
                >
                  {{ $t('general.active') }}
                </sw-badge>
              </div>
              <div v-if="row.status == 'C'">
                <sw-badge
                  :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
                  :color="$utils.getBadgeStatusColor('OVERDUE').color"
                  class="px-3 py-1"
                >
                  {{ $t('general.cancelled') }}
                </sw-badge>
              </div>
              <div v-if="row.status == 'S'">
                <sw-badge
                  :bg-color="$utils.getBadgeStatusColor('VIEWED').bgColor"
                  :color="$utils.getBadgeStatusColor('VIEWED').color"
                  class="px-3 py-1"
                >
                  {{ $t('general.suspended') }}
                </sw-badge>
              </div>
              <div v-if="row.status == 'P'">
                <sw-badge
                  :bg-color="$utils.getBadgeStatusColor('DRAFT').bgColor"
                  :color="$utils.getBadgeStatusColor('DRAFT').color"
                  class="px-3 py-1"
                >
                  {{ $t('general.pending') }}
                </sw-badge>
              </div>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.term')"
            show="term"
          >
            <template slot-scope="row">
              <span>{{ $t('corePbx.tenants.table_view.term') }}</span>
              <p>{{ row?.term }}</p>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.activation_date')"
            show="date_begin"
          >
            <template slot-scope="row">
              <span>{{
                $t('corePbx.tenants.table_view.activation_date')
              }}</span>
              <p>{{ row?.date_begin }}</p>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.date_of_renovation')"
            show="renewal_date"
          >
            <template slot-scope="row">
              <span>{{
                $t('corePbx.tenants.table_view.date_of_renovation')
              }}</span>
              {{ row?.renewal_date }}
            </template>
          </sw-table-column>
        </sw-table-component>
      </div>

      <!-- Table Associated Extensions -->
      <div v-if="basicInfo.name" class="grid col-span-12 gap-1 md:grid-cols-6">
        <sw-divider class="col-span-12 my-8" />
        <div class="col-span-12">
          <h3 class="text-lg font-medium">
            {{ $t('corePbx.tenants.associated_extensions') }}
          </h3>
        </div>

        <sw-table-component
          class="col-span-12 md:-mt-0 -mt-12"
          ref="ext_table"
          :data="pbxExtensionsTable"
          :show-filter="false"
          table-class="table"
          variant="gray"
        >
          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.ext')"
            show="ext"
          >
            <template slot-scope="row">
              <span class="bg-danger">{{
                $t('corePbx.tenants.table_view.ext')
              }}</span>
              <p>{{ row?.ext }}</p>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.name')"
            show="name"
          >
            <template slot-scope="row">
              <span class="bg-danger">{{
                $t('corePbx.tenants.table_view.name')
              }}</span>
              <p>{{ row?.name }}</p>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.status')"
            show="status"
          >
            <template slot-scope="row">
              <span>{{ $t('corePbx.tenants.table_view.status') }}</span>
              <div v-if="row.status == 'enabled'">
                <sw-badge
                  :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
                  :color="$utils.getBadgeStatusColor('COMPLETED').color"
                  class="px-3 py-1"
                >
                  {{ $t('general.active') }}
                </sw-badge>
              </div>
              <div v-if="row.status == 'disabled'">
                <sw-badge
                  :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
                  :color="$utils.getBadgeStatusColor('OVERDUE').color"
                  class="px-3 py-1"
                >
                  {{ $t('general.inactive') }}
                </sw-badge>
              </div>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.email')"
            show="email"
          >
            <template slot-scope="row">
              <span>{{ $t('corePbx.tenants.table_view.email') }}</span>
              <span>{{ row?.email }}</span>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.service_number')"
            show="pbx_services_number"
          >
            <template slot-scope="row">
              <span>{{ $t('corePbx.tenants.table_view.service_number') }}</span>
              <p v-if="row?.service?.pbx_services_number">
                <router-link
                  :to="routerPath(row.service)"
                  class="font-medium text-primary-500"
                >
                  {{ row?.service?.pbx_services_number }}
                </router-link>
              </p>
              <p v-else>N/A</p>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="false"
            :filterable="false"
            cell-class="action-dropdown"
          >
            <template slot-scope="row">
              <span> {{ $t('packages.action') }} </span>
              <sw-dropdown>
                <dot-icon slot="activator" />
                <sw-dropdown-item
                  @click="openUpdateExtModal(row)"
                  v-if="permissionModule.update"
                >
                  <pencil-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.edit') }}
                </sw-dropdown-item>
              </sw-dropdown>
            </template>
          </sw-table-column>
        </sw-table-component>
      </div>

      <!-- Table Associated DIDs -->
      <div v-if="basicInfo.name" class="grid col-span-12 gap-1 md:grid-cols-6">
        <sw-divider class="col-span-12 my-8" />
        <div class="col-span-12">
          <h3 class="text-lg font-medium">
            {{ $t('corePbx.tenants.associated_dids') }}
          </h3>
        </div>

        <sw-table-component
          class="col-span-12 md:-mt-0 -mt-12"
          ref="did_table"
          :data="pbxDidsTable"
          :show-filter="false"
          table-class="table"
          variant="gray"
        >
          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.number')"
            show="number"
          >
            <template slot-scope="row">
              <span class="bg-danger">{{
                $t('corePbx.tenants.table_view.number')
              }}</span>
              <p>{{ row?.number }}</p>
              <p v-if="!row.number">-</p>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.status')"
            show="status"
          >
            <template slot-scope="row">
              <span class="bg-danger">{{
                $t('corePbx.tenants.table_view.status')
              }}</span>
              <div v-if="row.status == 'enabled'">
                <sw-badge
                  :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
                  :color="$utils.getBadgeStatusColor('COMPLETED').color"
                  class="px-3 py-1"
                >
                  {{ $t('general.active') }}
                </sw-badge>
              </div>
              <div v-if="row.status == 'disabled'">
                <sw-badge
                  :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
                  :color="$utils.getBadgeStatusColor('OVERDUE').color"
                  class="px-3 py-1"
                >
                  {{ $t('general.inactive') }}
                </sw-badge>
              </div>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.trunk')"
            show="trunk"
          >
            <template slot-scope="row">
              <span>{{ $t('corePbx.tenants.table_view.trunk') }}</span>
              <p>{{ row?.trunk }}</p>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.type')"
            show="type"
          >
            <template slot-scope="row">
              <span>{{ $t('corePbx.tenants.table_view.type') }}</span>
              <p>{{ row?.type }}</p>
              <p v-if="!row.type">-</p>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.ext')"
            show="ext"
          >
            <template slot-scope="row">
              <span>{{ $t('corePbx.tenants.table_view.ext') }}</span>
              <p>{{ row?.ext }}</p>
              <p v-if="!row.ext">-</p>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.service_number')"
            show="pbx_services_number"
          >
            <template slot-scope="row">
              <span>{{ $t('corePbx.tenants.table_view.service_number') }}</span>
              <p v-if="row?.pbxService?.pbx_services_number">
                <router-link
                  :to="routerPath(row.pbxService)"
                  class="font-medium text-primary-500"
                >
                  {{ row?.pbxService?.pbx_services_number }}
                </router-link>
              </p>
              <p v-else>N/A</p>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="false"
            :filterable="false"
            cell-class="action-dropdown"
          >
            <template slot-scope="row">
              <span> {{ $t('packages.action') }} </span>
              <sw-dropdown>
                <dot-icon slot="activator" />
                <sw-dropdown-item
                  @click="openUpdateDIDModal(row)"
                  v-if="permissionModule.update"
                >
                  <pencil-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.edit') }}
                </sw-dropdown-item>
              </sw-dropdown>
            </template>
          </sw-table-column>
        </sw-table-component>
      </div>

      <!-- Table PBX Services Applications -->
      <div v-if="basicInfo.name" class="grid col-span-12 gap-1 md:grid-cols-6">
        <sw-divider class="col-span-12 my-8" />
        <div class="col-span-12">
          <h3 class="text-lg font-medium">
            {{ $t('corePbx.tenants.pbx_services_applications') }}
          </h3>
        </div>

        <sw-table-component
          class="col-span-12 md:-mt-0 -mt-12"
          ref="table"
          :data="pbxAppTable"
          :show-filter="false"
          table-class="table"
          variant="gray"
        >
          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.app_name')"
            show="app_name"
          >
            <template slot-scope="row">
              <span class="bg-danger">{{
                $t('corePbx.tenants.table_view.app_name')
              }}</span>
              <p>{{ row.app_name }}</p>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.amount')"
            show="quantity"
          >
            <template slot-scope="row">
              <span class="bg-danger">{{
                $t('corePbx.tenants.table_view.amount')
              }}</span>
              <p>{{ row.quantity }}</p>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.associated_cost')"
            show="costo"
          >
            <template slot-scope="row">
              <span>{{
                $t('corePbx.tenants.table_view.associated_cost')
              }}</span>
              <p>{{ row.costo }}</p>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('corePbx.tenants.table_view.service_number')"
            show="pbx_services_number"
          >
            <template slot-scope="row">
              <span>{{ $t('corePbx.tenants.table_view.service_number') }}</span>
              <p v-if="row?.pbxService?.pbx_services_number">
                <router-link
                  :to="routerPath(row.pbxService)"
                  class="font-medium text-primary-500"
                >
                  {{ row?.pbxService?.pbx_services_number }}
                </router-link>
              </p>
              <p v-else>N/A</p>
            </template>
          </sw-table-column>

          <!-- <sw-table-column
            :sortable="true"
            :filterable="false"
            cell-class="action-dropdown"
          >
            <template slot-scope="row">
              <span> {{ $t('packages.action') }} </span>
              <sw-dropdown>
                <dot-icon slot="activator" />
                <sw-dropdown-item
                  tag-name="router-link"
                  :to="`/admin/corePBX/tenant/tenants-list/2/view`"
                  v-if="permissionModule.update"
                >
                  <pencil-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.edit') }}
                </sw-dropdown-item>
              </sw-dropdown>
            </template>
          </sw-table-column> -->
        </sw-table-component>
      </div>
    </sw-card>
  </base-page>
</template>

<script>
import { mapActions } from 'vuex'
import moment from 'moment'

import {
  TrashIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    TrashIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusIcon,
  },
  data() {
    return {
      basicInfo: {},
      isRequestOnGoing: false,
      page: 1,
      permissionModule: {
        create: false,
        read: false,
        delete: false,
        update: false,
      },
    }
  },

  computed: {},
  created() {
    this.permissionsUserModule()
    this.fetchData({ page: 1 })
  },

  mounted() {
    setTimeout(() => {
      window.hub.$on('updateListDid', () => {
        this.$refs.did_table?.refresh()
      })
      window.hub.$on('updateListExt', () => {
        this.$refs.ext_table?.refresh()
      })
    }, 2000)
  },

  methods: {
    ...mapActions('tenants', [
      'fetchPbxTenantsShow',
      'tablePbxServer',
      'tablePbxExtensions',
      'tablePbxDids',
      'tablePbxApp',
    ]),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('modal', ['openModal']),

    async fetchData({ page, filter, sort }) {
      try {
        this.isRequestOngoing = true
        let data = {
          id: this.$route.params.id,
          page,
          limit: 10,
        }
        let response = await this.fetchPbxTenantsShow(data)
        this.basicInfo = response.data

        return {
          data: response.data || {},
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.isRequestOngoing = false
      }
    },

    async pbxServerTable({ page, sort }) {
      try {
        this.isRequestOngoing = true
        if (sort) {
          sort = `${sort.order === 'desc' ? '-' : ''}${sort.fieldName}`
        }
        let data = {
          id: this.$route.params.id,
          page,
          sort,
        }
        let response = await this.tablePbxServer(data)
        return {
          data: response.data || {},
          pagination: {
            totalPages: response.meta.last_page,
            currentPage: page,
          },
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.isRequestOngoing = false
      }
    },

    async pbxExtensionsTable({ page, sort }) {
      try {
        this.isRequestOngoing = true
        if (sort) {
          sort = `${sort.order === 'desc' ? '-' : ''}${sort.fieldName}`
        }
        let response = await this.tablePbxExtensions({
          id: this.$route.params.id,
          page,
          sort,
        })
        return {
          data: response.data || {},
          pagination: {
            totalPages: response.meta.last_page,
            currentPage: page,
          },
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.isRequestOngoing = false
      }
    },

    async pbxDidsTable({ page, sort }) {
      try {
        if (sort) {
          sort = `${sort.order === 'desc' ? '-' : ''}${sort.fieldName}`
        }
        this.isRequestOngoing = true
        let response = await this.tablePbxDids({
          id: this.$route.params.id,
          page,
          sort,
        })
        return {
          data: response.data || {},
          pagination: {
            totalPages: response.meta.last_page,
            currentPage: page,
          },
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.isRequestOngoing = false
      }
    },

    async pbxAppTable({ page, sort }) {
      try {
        this.isRequestOngoing = true
        if (sort) {
          sort = `${sort.order === 'desc' ? '-' : ''}${sort.fieldName}`
        }
        let response = await this.tablePbxApp({
          id: this.$route.params.id,
          page,
          sort,
        })
        return {
          data: response.data.flat() || {},
          pagination: {
            totalPages: response.meta.last_page,
            perPage: page,
          },
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.isRequestOngoing = false
      }
    },

    routerPath(row) {
      return {
        name: 'customers.pbx-service-view',
        params: {
          id: row.customer_id,
          pbx_service_id: row.id,
        },
      }
    },

    openUpdateExtModal(extension) {
      extension.from = "viewTenant";
      this.openModal({
        title: this.$t('pbx_services.edit_extension'),
        componentName: 'EditModalExtensions',
        id: extension.id,
        data: extension,
      })
    },

    openUpdateDIDModal(did) {
      did.from = "viewTenant";
      this.openModal({
        title: this.$t('pbx_services.edit_did'),
        componentName: 'UpdateDidModal',
        id: did.id,
        data: did,
      })
    },

    formatDate(value) {
      return moment(value).format('DD/MM/YYYY HH:mm')
    },

    async permissionsUserModule() {
      const data = {
        module: 'pbx_tenant',
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if (permissions.super_admin == false) {
        if (permissions.exist == false) {
          this.$router.push('/admin/dashboard')
        } else {
          const modulePermissions = permissions.permissions[0]
          if (modulePermissions == null) {
            this.$router.push('/admin/dashboard')
          } else if (modulePermissions.access == 0) {
            this.$router.push('/admin/dashboard')
          }
        }
      }

      // valida que el usuario tenga el permiso create, read, delete, update
      if (permissions.super_admin == true) {
        this.permissionModule.create = true
        this.permissionModule.access = true
        this.permissionModule.update = true
        this.permissionModule.delete = true
        this.permissionModule.read = true
      } else if (permissions.exist == true) {
        const modulePermissions = permissions.permissions[0]
        if (modulePermissions.create == 1) {
          this.permissionModule.create = true
        }
        if (modulePermissions.access == 1) {
          this.permissionModule.access = true
        }
        if (modulePermissions.update == 1) {
          this.permissionModule.update = true
        }
        if (modulePermissions.delete == 1) {
          this.permissionModule.delete = true
        }
        if (modulePermissions.read == 1) {
          this.permissionModule.read = true
        }
      }

      const dataPBXServices = {
        module: 'pbx_services',
      }
      const permissionsPBXServices = await this.getUserModules(dataPBXServices)

      // valida que el usuario tenga el permiso create, read, delete, update
      if (permissionsPBXServices.super_admin == true) {
        this.permissionModule.accessPBXServices = true
        this.permissionModule.readPBXServices = true
      } else if (permissionsPBXServices.exist == true) {
        const modulePermissions = permissionsPBXServices.permissions[0]
        if (modulePermissions.access == 1) {
          this.permissionModule.accessPBXServices = true
        }
        if (modulePermissions.read == 1 && modulePermissions.access == 1) {
          this.permissionModule.readPBXServices = true
        }
      }
    },
  },
}
</script>
