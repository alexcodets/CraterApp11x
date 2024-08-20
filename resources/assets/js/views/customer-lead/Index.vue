<template>
  <base-page v-if="isSuperAdmin" class="items">
    <sw-page-header :title="$t('leads.customers_leads')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item to="#" :title="$tc('leads.customers_leads', 2)" active />
      </sw-breadcrumb>
      
      <template slot="actions">
        
        <sw-button
          v-show="!showEmptyScreen"
          variant="primary-outline"
          size="lg"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>
        <sw-button
          class="ml-1"
          variant="primary"
          size="lg"
          to="leads"
          type="button"
          tag-name="router-link"
          >
          {{ $t('leads.title') }}
        </sw-button>
        <sw-button
          class="ml-1"
          variant="primary"
          size="lg"
          to="customers"
          type="button"
          tag-name="router-link"
        >
        {{ $t('leads.customer') }}
        </sw-button>
      </template>
    </sw-page-header>
   
 <!-- Filters -->
    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="relative grid grid-flow-col grid-rows">


        <div class="w-100" style="margin-left: 1em; margin-right: 1em">
          <sw-input-group :label="$tc('leads.search')" >
            <sw-input
              v-model="filters.search"
              type="text"
              name="name"
              class="mt-2"
              autocomplete="off"
              style="min-width: 300px"
            />
          </sw-input-group>
        </div>

        
        <div class="w-100" style="margin-left: 1em; margin-right: 1em">
          <sw-input-group :label="$t('leads.type')" class="mt-2">
            <sw-select
              v-model="filters.type"
              :options="type_options"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('leads.type')"
              :allow-empty="false"
              track-by="value"
              label="name"
              style="min-width: 300px"
            />
          </sw-input-group>
        </div>
        
     
        
       <label
          class="absolute text-sm leading-snug text-gray-900 cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
        >
          {{ $t('general.clear_all') }}</label
        >
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <sw-table-component
      ref="table"
      :data="fetchData"
      :show-filter="false"
      table-class="table"
    >
      <sw-table-column :sortable="false" :filterable="false" cell-class="no-click">
        <div slot-scope="row" class="custom-control custom-checkbox">
          <sw-checkbox
            :id="row.company_name"
            v-model="selectField"
            :value="row.company_name"
            variant="primary"
            size="sm"
          />
        </div>
      </sw-table-column>

      <sw-table-column :sortable="true" :label="$t('leads.name')" show="company_name">
        <template slot-scope="row">
          <span>{{ $t("leads.name") }}</span>
          <span v-if="row.company_name != null">
            {{ row.company_name }}
          </span>
          <span v-else-if="row.first_name != null">
            {{ row.first_name + " " + row.last_name }}
          </span>
          <span v-else> N/A </span>
        </template>
      </sw-table-column>

      <sw-table-column :sortable="true" :label="$t('leads.email')" show="email" />

      <sw-table-column :sortable="true" :label="$t('leads.phone')">
        <template slot-scope="row">
          <span>{{ $t("leads.phone") }}</span>
          <span>{{ row.phone ? row.phone : "No Contact" }} </span>
        </template>
      </sw-table-column>

      <sw-table-column :sortable="true" :label="$t('leads.type')" >
        <template slot-scope="row">
          <span>{{ $t("leads.type") }}</span>
          <span>{{ row.type_from }} </span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :label="$t('leads.customer_type')"
        sort-as="customer_type"
        show="customer_type"
      >
        <template slot-scope="row">
          <span>{{ row.customer_type }}</span>
          <span v-if="row.customer_type === 'N'">None</span>
          <span v-else-if="row.customer_type === 'B'">Business</span>
          <span v-else>Residential</span>
        </template>
      </sw-table-column>

      <sw-table-column :sortable="true" :filterable="false" cell-class="action-dropdown">
        <template slot-scope="row">
          <span> {{ $t("leads.action") }} </span>
          <sw-dropdown>
            <dot-icon slot="activator" />
            <div v-if="row.type_from == 'lead'">
              <sw-dropdown-item tag-name="router-link" :to="`leads/${row.id}/edit`">
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t("general.edit") }}
              </sw-dropdown-item>
              <div v-if="row.status === 'A'">
                <sw-dropdown-item @click="convertCustomer(row)">
                  <pencil-icon class="h-5 mr-3 text-gray-600" />
                    {{ $t("leads.convert_customer") }}
                </sw-dropdown-item>
              </div>
            </div>
            <div v-else>
              <sw-dropdown-item tag-name="router-link" :to="`customers/${row.id}/edit`">
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t("general.edit") }}
              </sw-dropdown-item>
              <sw-dropdown-item tag-name="router-link" :to="`customers/${row.id}/view`">
                <eye-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t("general.view") }}
              </sw-dropdown-item>
              
            </div>
          </sw-dropdown>
        </template>
      </sw-table-column>
    </sw-table-component>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import AstronautIcon from "@/components/icon/AstronautIcon";
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
  EyeIcon,
} from "@vue-hero-icons/solid";

export default {
  components: {
    AstronautIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    PlusIcon,
    EyeIcon,
  },

  data() {
    return {
      id: null,
      showFilters: false,
      sortedBy: "created_at",
      isRequestOngoing: true,
      showEmptyScreen: false,
      filters: {
      search: '',
      type: ''
      },
      permissionModule: {
        create: false,
        update: false,
        delete: false,
        read: false,
      },
      type_options: [
        {
          name: 'Customer',
          value: 'customer'
        },
        {
          name: 'Lead',
          value: 'lead'
        }
      ]
    };
  },
  computed: {
    ...mapGetters("user", ["currentUser"]),
    ...mapGetters("users", ["users", "selectedUsers", "selectAllField"]),
    isSuperAdmin() {
      return this.currentUser.role == "super admin";
    },

      filterIcon() {
      return this.showFilters ? "x-icon" : "filter-icon";
    },

    selectField: {
      get: function () {
        return this.selectedUsers;
      },
      set: function (val) {
        this.selectedUser(val);
      },
    },

    selectAllFieldStatus: {
      get: function () {
        return this.selectAllField;
      },
      set: function (val) {
        this.setSelectAllState(val);
      },
    },
  },
  created() {
    if (!this.isSuperAdmin) {
      this.$router.push("/admin/dashboard");
    }
  },
  watch: {
    filters: {
      handler: "setFilters",
      deep: true,
    },
  },

  methods: {
    ...mapActions("lead", ["fetchCustomersLeads"]),

    ...mapActions("user", ["getUserModules"]),

    refreshTable() {
      this.$refs.table.refresh();
    },

    async fetchData({ page, filter, sort } ) {

      let search = ''
      let response = null;
      this.isRequestOngoing = true;

      if(this.filters.search !== ''){
        search = this.filters.search
        if(this.$route.query.param !== undefined){
          this.$router.replace({ query: { } })
        }
      }

      if(this.$route.query.param !== undefined){
        search = this.$route.query.param
      } 
     
      try {
        let data = {}
        data = {
            is_url: true,
            type: this.filters.type.value !== undefined ? this.filters.type.value : "",
            param: search,
            orderByField: sort.fieldName || "company_name",
            orderBy: sort.order || "desc",
            page,
          };
          response = await this.fetchCustomersLeads(data);
        
      } catch (error) {
        //console.log(error);
      }
      this.isRequestOngoing = false;
      this.showEmptyScreen = response.data.data.data == 0 ?? false;
      return {
        data: response.data.data.data,
        pagination: {
          totalPages: response.data.data.last_page,
          currentPage: page,
        },
      };
    },
    setFilters() {
      this.refreshTable();
    },

    clearFilter() {
      this.filters = {
        search: "",
      };
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter();
      }

      this.showFilters = !this.showFilters;
    },

    convertCustomer(row) {
      swal({
        title: this.$t("general.are_you_sure"),
        text: this.$tc("leads.convert_message"),
        icon: "warning",
        buttons: true,
        // showCancelButton: true,
      }).then(async (confirm) => {
        if (confirm) {
          this.$emit("lead_to_customer", row);
          this.$router.push({ name: "customers.create", params: row });
        }
      });
    },
  },
};
</script>
