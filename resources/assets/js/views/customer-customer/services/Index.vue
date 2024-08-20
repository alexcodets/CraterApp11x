<template>
  <base-page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="$t('navigation.services')">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item :title="$t('general.home')" to="dashboard" />
          <sw-breadcrumb-item :title="$tc('navigation.services')" to="#" active />
        </sw-breadcrumb>
      </sw-page-header>

      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          v-show="totalServices > 0"
          variant="primary-outline"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          @click="toggleFilter"
        >
          {{ $t("general.filter") }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>
      </div>
    </div>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <div class="w-33">
          <sw-input-group
            :label="$tc('customers.service_number')"
            class="flex-1 mt-2 ml-0"
          >
            <sw-input
              v-model="filters.service_number"
              type="text"
              name="name"
              class="mt-2"
              >
            <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
          </sw-input>
          </sw-input-group>
          <sw-input-group
            :label="$tc('customers.tenants')"
            class="flex-1 mt-3 ml-0"
            style="min-width: 300px"
          >
            <span
              v-if="filters.tenant"
              class="absolute text-gray-400 cursor-pointer"
              style="top: 55%; right: 5%; z-index: 999999"
              @click="filters.tenant = null"
            >
              <x-circle-icon class="h-5" />
            </span>
            <sw-select
              v-model="filters.tenant"
              :options="pbxTenantsOptions"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('customers.tenants')"
              class="mt-2"
              label="name"
            />
          </sw-input-group>
        </div>

        <div class="w-33">
          <sw-input-group
            :label="$tc('customers.type_service')"
            class="flex-1 mt-3 ml-0 lg:ml-6"
            style="min-width: 300px"
          >
            <span
              v-if="filters.type"
              class="absolute text-gray-400 cursor-pointer"
              style="top: 55%; right: 1%; z-index: 999999"
              @click="filters.type = null"
            >
              <x-circle-icon class="h-5" />
            </span>
            <sw-select
              v-model="filters.type"
              :options="typeServicesOptions"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('customers.type_service')"
              class="mt-2"
            >
              <div>
                <span>icon</span>
              </div>
            </sw-select>
          </sw-input-group>
          <sw-input-group
            :label="$t('customers.date_act')"
            class="flex-1 mt-3 ml-0 lg:ml-6"
          >
            <base-date-picker
              v-model="filters.activation_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
            />
          </sw-input-group>
        </div>
        <div class="w-33 ml-6">

          <sw-input-group
            :label="$tc('customers.status')"
            class="flex-1 mt-3 ml-0 lg:ml-6"
            style="min-width: 300px"
          >
            <span
              v-if="filters.status"
              class="absolute text-gray-400 cursor-pointer"
              style="top: 55%; right: 1%; z-index: 999999"
              @click="filters.status = null"
            >
              <x-circle-icon class="h-5" />
            </span>
            <sw-select
              v-model="filters.status"
              :options="statusServicesOptions"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('customers.status')"
              class="mt-2"
              label="text"
            />
          </sw-input-group>



          <sw-input-group
            :label="$t('customers.renewal_date')"
            class="flex-1 mt-3 ml-0 lg:ml-6"
          >
            <base-date-picker
              v-model="filters.renewal_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
            />
          </sw-input-group>
        </div>
        <label
          class="absolute text-sm leading-snug text-gray-900 cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
        >
          {{ $t("general.clear_all") }}</label
        >
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <sw-table-component
      ref="table"
      :data="fetchData"
      :show-filter="false"
      table-class="table"
      class="-mt-2 md:mt-0"
    >
      <sw-table-column
        :sortable="true"
        :label="$t('customers.service_number')"
        show="service_number"
      >
        <template slot-scope="row">
          <span>{{ $t("customers.service_number") }}</span>
          <router-link
            :to="`/customer/pbx-service/${row.id}/view`"
            class="font-medium text-primary-500"
            v-if="row.type == 'PBX' "
          >
            {{ row.service_number }}
          </router-link>
          <router-link
            :to="`/customer/service/${row.id}/view`"
            class="font-medium text-primary-500"
            v-else-if="
              row.type == 'NORMAL'
            "
          >
            {{ row.service_number }}
          </router-link>
          <span v-else>
            {{ row.service_number }}
          </span>
        </template>
      </sw-table-column>

      <sw-table-column :sortable="true" :label="$t('customers.type_service')" show="type">
        <template slot-scope="row">
          <span>{{ $t("customers.type_service") }}</span>
          <span>{{ row.type }}</span>
        </template>
      </sw-table-column>


      <sw-table-column :sortable="true" :label="$t('customers.status')" show="status">
        <template slot-scope="row">
          <span>{{ $t("customers.status") }}</span>
          <span>


            <div v-if="row.status == 'A' || row.status == 'Active'">
              <sw-badge
              :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
              :color="$utils.getBadgeStatusColor('COMPLETED').color"
              class="px-3 py-1"
            >
            {{ $t('general.active') }}
            </sw-badge>

            </div>


            <div v-if="row.status == 'P' || row.status == 'Pending'">
              <sw-badge
              :bg-color="$utils.getBadgeStatusColor('VIEWED').bgColor"
              :color="$utils.getBadgeStatusColor('VIEWED').color"
              class="px-3 py-1"
            >
            {{ $t('general.pending') }}
            </sw-badge>
          </div>



          <div v-if="row.status == 'S' || row.status == 'Suspended'">
              <sw-badge
              :bg-color="$utils.getBadgeStatusColor('SENT').bgColor"
              :color="$utils.getBadgeStatusColor('SENT').color"
              class="px-3 py-1"
            >
           {{ $t('general.suspended') }}
            </sw-badge>
          </div>

          <div v-if="row.status == 'C' || row.status == 'Cancelled'">
              <sw-badge
              :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
              :color="$utils.getBadgeStatusColor('OVERDUE').color"
              class="px-3 py-1"
            >
            {{ $t('general.cancelled') }}
            </sw-badge>
          </div>
          </span>
        </template>
      </sw-table-column>


      <sw-table-column
        :sortable="true"
        :label="$t('customers.date_act')"
        show="activation_date"
      >
        <template slot-scope="row">
          <span>{{ $t("customers.date_act") }}</span>
          <span>{{ row.activation_date }}</span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :label="$t('customers.renewal_date')"
        show="renewal_date"
      >
        <template slot-scope="row">
          <span>{{ $t("customers.renewal_date") }}</span>
          <span>{{ row.renewal_date }}</span>
        </template>
      </sw-table-column>

    </sw-table-component>
  </base-page>
</template>

<script>
import { mapActions, mapGetters, mapState } from "vuex";
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
  XCircleIcon,
  CogIcon,
  HashtagIcon,
} from "@vue-hero-icons/solid";
import SatelliteIcon from "../../../components/icon/SatelliteIcon.vue";
import { CalculatorIcon } from "@vue-hero-icons/outline";

export default {
  components: {
    SatelliteIcon,
    FilterIcon,
    XIcon,
    PlusIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    CogIcon,
    CalculatorIcon,
    XCircleIcon,
    HashtagIcon,
  },

  data() {
    return {
      showFilters: false,
      isRequestOngoing: true,
      totalServices: 0,
      statusServicesOptions: [
        {
          value: "A",
          text: "Active",
        },
        {
          value: "S",
          text: "Suspended",
        },
        {
          value: "C",
          text: "Cancelled",
        },
        {
          value: "P",
          text: "Pending",
        },
      ],
      typeServicesOptions: ["PBX", "NORMAL"],
      pbxTenantsOptions: [],

      filters: {
        service_number: "",
        name_customer: "",
        type: "",
        status: "",
        activation_date: "",
        renewal_date: "",
        tenant: "",
      },
      timeOut: null,
      permissionModule: {
        createInvoices: false,
        readServicesNormal: false,
        readServicesPBX: false,
      },
    };
  },
  filters: {
    showStatus(value) {
      if (value === "A") {
        return "Active";
      } else if (value === "S") {
        return "Suspended";
      } else if (value === "C") {
        return "Cancelled";
      } else if (value === "P") {
        return "Pending";
      }
    },
  },

  computed: {
    ...mapGetters("company", ["defaultCurrency"]),
    ...mapState('user', ['currentUser', 'settingsCompany']),

    filterIcon() {
      return this.showFilters ? "x-icon" : "filter-icon";
    },
  },

  mounted() {
    this.permissionsUserModule();
  },

  watch: {
    filters: {
      handler: "setFilters",
      deep: true,
    },
  },
  created() {
    if ( this.settingsCompany.enable_service_customer === "0") {
      this.$router.push('./views/errors/404.vue')
    }
    this.getPbxTenants();
  },
  methods: {
    ...mapActions("service", ["fetchServiceAll"]),
    ...mapActions("customSearch", ["indexPbxTenantservice"]),
    ...mapActions("user", ["getUserModules"]),
    refreshTable() {
      this.$refs.table.refresh();
    },

    deselectItem() {
      this.itemSelect = null;
      this.$emit("deselect");
    },

    async fetchData({ page, filter, sort }) {
      try {
        this.isRequestOngoing = true;
        console.log("hello")
        console.log(this.filters, "filters")
        const data = {
          service_number: this.filters.service_number,
          name_customer: this.filters.name_customer,
          type: this.filters.type,
          status: this.filters.status?.value,
          activation_date: this.filters.activation_date,
          renewal_date: this.filters.renewal_date,
          tenant: this.filters.tenant,
          orderByField: sort.fieldName || "created_at",
          orderBy: sort.order || "desc",
          page,
          limit: 10,
        };

        let response = await this.fetchServiceAll(data);
       this.totalServices = response.data.services.total;
       console.log(response);
        return {
          data: response.data.services.data,
          pagination: {
            totalPages: response.data.services.last_page,
            currentPage: page,
          },
        };
      } catch (error) {
        // console.log(error)
      } finally {
        this.isRequestOngoing = false;
      }
    },

    async getPbxTenants() {
      try {
        const filters = {
          includeServicesSuspended: false,
        };
        // console.log(filters)
        const response = await this.indexPbxTenantservice(filters);
        console.log(response);
        this.pbxTenantsOptions = Object.values(response.data.data);
      } catch (e) {
        // console.log(e)
      }
    },
    setFilters() {
      if (this.timeOut) {
        clearTimeout(this.timeOut);
      }
      this.timeOut = setTimeout(() => {
        this.$refs.table.refresh();
      }, 500);
    },
    clearFilter() {
      this.filters = {
        service_number: "",
        name_customer: "",
        type: "",
        status: "",
        activation_date: "",
        renewal_date: "",
        tenant: "",
      };
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter();
      }
      this.showFilters = !this.showFilters;
    },
    routerPath(row) {
      if (row.type == "PBX") {
        return {
          name: "customers.pbx-service-view",
          params: {
            id: row.user.id,
            pbx_service_id: row.id,
          },
        };
      } else if (row.type == "NORMAL") {
        return {
          name: "customers.package-view",
          params: {
            id: row.user.id,
            customer_package_id: row.id,
          },
        };
      }
    },

    async permissionsUserModule() {
      // valida que el usuario tenga permiso para ingresar al modulo services
      const data = {
        module: "services",
      };


      // valida que el usuario tenga el permiso create, read, delete, update servicios normales

      const dataServicesNormal = {
        module: "services_normal",
      };
      const permissionsServicesNormal = await this.getUserModules(dataServicesNormal);
      if (permissionsServicesNormal.super_admin == true) {
        this.permissionModule.readServicesNormal = true;
      } else if (permissionsServicesNormal.exist == true) {
        const modulePermissions = permissionsServicesNormal.permissions[0];
        if (modulePermissions.read == 1 && modulePermissions.access == 1) {
          this.permissionModule.readServicesNormal = true;
        }
      }

      // valida que el usuario tenga el permiso create, read, delete, update servicios PBX

      const dataServicesPBX = {
        module: "pbx_services",
      };
      const permissionsServicesPBX = await this.getUserModules(dataServicesPBX);
      if (permissionsServicesPBX.super_admin == true) {
        this.permissionModule.readServicesPBX = true;
      } else if (permissionsServicesPBX.exist == true) {
        const modulePermissions = permissionsServicesPBX.permissions[0];
        if (modulePermissions.read == 1 && modulePermissions.access == 1) {
          this.permissionModule.readServicesPBX = true;
        }
      }

      // valida los permisos para crear facturas
      const dataInvoices = {
        module: "invoices",
      };
      const permissionsInvoices = await this.getUserModules(dataInvoices);
      if (permissionsServicesNormal.super_admin == true) {
        this.permissionModule.createInvoices = true;
      } else if (permissionsInvoices.exist == true) {
        const modulePermissionsInvoices = permissionsInvoices.permissions[0];
        if (
          modulePermissionsInvoices.create == 1 &&
          modulePermissionsInvoices.access == 1
        ) {
          this.permissionModule.createInvoices = true;
        }
      }
      // console.log(this.permissionModule)
    },
  },
};
</script>

