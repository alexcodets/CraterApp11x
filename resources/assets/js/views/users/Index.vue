<template>
  <base-page v-if="isSuperAdmin" class="items">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="$t('users.title')">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item to="dashboard" :title="$t('general.home')" />
          <sw-breadcrumb-item to="#" :title="$tc('users.title', 2)" active />
        </sw-breadcrumb>
      </sw-page-header>


      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          v-show="totalUsers"
          variant="primary-outline"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          @click="toggleFilter"
        >
          {{ $t("general.filter") }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="users/create"
          variant="primary"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          v-if="permissionModule.create"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t("users.add_user") }}
        </sw-button>
      </div>
    </div>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">
        <sw-input-group :label="$tc('users.name')" class="flex-1 mt-2 mr-4">
          <sw-input
            v-model="filters.name"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group :label="$tc('users.email')" class="flex-1 mt-2 mr-4">
          <sw-input
            v-model="filters.email"
            type="text"
            name="email"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group :label="$tc('users.phone')" class="flex-1 mt-2 mr-4">
          <sw-input
            v-model="filters.phone"
            type="text"
            name="phone"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group :label="$tc('users.roles')" class="flex-1 mt-2">
          <sw-select
            v-model="filters.roles"
            :options="roles"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :placeholder="$t('users.select_role')"
            :multiple="true"
            class="mt-2"
            label="name"
            track-by="id"                    
          />
        </sw-input-group>
        

        <label
          class="absolute text-sm leading-snug text-gray-900 cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
        >
          {{ $t("general.clear_all") }}</label
        >
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('users.no_users')"
      :description="$t('users.list_of_users')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/users/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t("users.add_user") }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div class="relative table-container" v-show="!showEmptyScreen">
      <div
        class="relative flex items-center justify-between h-10 mt-5 list-none border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t("general.showing") }}: <b>{{ users.length }}</b>

          {{ $t("general.of") }}

          <b>{{ totalUsers }}</b>
        </p>

        <sw-transition type="fade">
          <sw-dropdown v-if="selectedUsers.length">
            <span
              slot="activator"
              class="flex block text-sm font-medium cursor-pointer select-none text-primary-400"
            >
              {{ $t("general.actions") }}
              <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item @click="removeMultipleUsers">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t("general.delete") }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-7">
        <sw-checkbox
          v-model="selectAllFieldStatus"
          variant="primary"
          size="sm"
          class="hidden md:inline"
          @change="selectAllUsers"
        />

        <sw-checkbox
          v-model="selectAllFieldStatus"
          :label="$t('general.select_all')"
          variant="primary"
          size="sm"
          class="md:hidden"
          @change="selectAllUsers"
        />
      </div>

      <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
        class="-mt-10 md:mt-0"
      >
        <sw-table-column :sortable="false" :filterable="false" cell-class="no-click">
          <div slot-scope="row" class="custom-control custom-checkbox">
            <sw-checkbox
              :id="row.id"
              v-model="selectField"
              :value="row.id"
              variant="primary"
              size="sm"
            />
          </div>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('users.name')" show="name">
          <template slot-scope="row">
            <span>{{ $t("users.name") }}</span>
            <router-link
              :to="{ path: `users/${row.id}/view` }"
              class="font-medium text-primary-500"
              v-if="permissionModule.read"
            >
              {{ row.name }}
            </router-link>
            <span v-else>
              {{ row.name }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('users.email')" show="email" />

        <sw-table-column :sortable="true" :label="$t('users.phone')" show="phone">
          <template slot-scope="row">
            <span>{{ $t("users.phone") }}</span>
            <span>{{ row.phone ? row.phone : "No Contact" }} </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('users.added_on')"
          sort-as="created_at"
          show="formattedCreatedAt"
        />

        <sw-table-column :sortable="true" :label="$t('users.role')" show="role2">
          <template slot-scope="row">
            <span>{{ $t("users.role") }}</span>
            <span>{{ row.role2 ? row.role2 : "No Role" }} </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t("users.action") }} </span>
            <sw-dropdown>
              <dot-icon slot="activator" />
              <sw-dropdown-item
                tag-name="router-link"
                :to="`users/${row.id}/view`"
                v-if="permissionModule.read"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t("general.view") }}
              </sw-dropdown-item>
              <sw-dropdown-item
                tag-name="router-link"
                :to="`users/${row.id}/edit`"
                v-if="permissionModule.update"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t("general.edit") }}
              </sw-dropdown-item>

              <sw-dropdown-item
                @click="removeUser(row.id)"
                v-if="permissionModule.delete"
              >
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t("general.delete") }}
              </sw-dropdown-item>
              <sw-dropdown-item
                tag-name="router-link"
                :to="`users/${row.id}/permissions`"
                v-if="permissionModule.update"
              >
                <lock-closed-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('roles.permissions') }}
              </sw-dropdown-item>              
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>
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
  LockClosedIcon,
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
    LockClosedIcon,
},

  data() {
    return {
      id: null,
      showFilters: false,
      sortedBy: "created_at",
      isRequestOngoing: true,

      filters: {
        name: "",
        email: "",
        phone: "",
        roles: ''
      },

      permissionModule: {
        create: false,
        update: false,
        delete: false,
        read: false,
      },
      
      roles: []
    };
  },
  computed: {
    ...mapGetters("user", ["currentUser"]),
    ...mapGetters("users", ["users", "selectedUsers", "totalUsers", "selectAllField"]),
    isSuperAdmin() {
      return this.currentUser.role == "super admin";
    },
    showEmptyScreen() {
      return !this.totalUsers && !this.isRequestOngoing;
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
  async created() {
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

  destroyed() {
    if (this.selectAllField) {
      this.selectAllUsers();
    }
  },
  mounted() {
    this.permissionsUserModule();
  },

  methods: {
    ...mapActions("users", [
      "fetchUsers",
      "selectAllUsers",
      "selectedUser",
      "deleteUser",
      "deleteMultipleUsers",
      "setSelectAllState",
    ]),

    ...mapActions("user", ["getUserModules"]),

    ...mapActions("roles", ["fetchRoles"]),

    refreshTable() {
      this.$refs.table.refresh();
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        display_name: this.filters.name !== null ? this.filters.name : "",        
        email: this.filters.email !== null ? this.filters.email : "",
        phone: this.filters.phone !== null ? this.filters.phone : "",
        roles: this.filters.roles.length > 0 
               ? this.filters.roles.map((rol) => {return rol.id})
               : '',
        orderByField: sort.fieldName || "created_at",
        orderBy: sort.order || "desc",
        page,
      };

      this.isRequestOngoing = true;

      let response = await this.fetchUsers(data);      
      this.roles = [ ...response.data.roles ]
      
      this.isRequestOngoing = false;

      return {
        data: response.data.users.data,
        pagination: {
          totalPages: response.data.users.last_page,
          currentPage: page,
        },
      };
    },

    setFilters() {
      this.refreshTable();
    },

    clearFilter() {
      this.filters = {
        name: "",
        email: "",
        phone: "",
        roles: "",
      };
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter();
      }

      this.showFilters = !this.showFilters;
    },

    async removeUser(id) {
      let user = [];
      user.push(id);

      swal({
        title: this.$t("general.are_you_sure"),
        text: this.$tc("users.confirm_delete"),
        icon: "/assets/icon/trash-solid.svg",
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteUser(user);

          if (res.data.success) {
            window.toastr["success"](this.$tc("users.deleted_message", 1));
            this.$refs.table.refresh();
            return true;
          }

          if (res.data.error === "user_attached") {
            window.toastr["error"](
              this.$tc("users.user_attached_message"),
              this.$t("general.action_failed")
            );
            return true;
          }

          window.toastr["error"](res.data.message);
          return true;
        }
      });
    },

    async removeMultipleUsers() {
      swal({
        title: this.$t("general.are_you_sure"),
        text: this.$tc("users.confirm_delete", 2),
        icon: "/assets/icon/trash-solid.svg",
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteMultipleUsers();

          if (res.data.success || res.data.users) {
            window.toastr["success"](this.$tc("users.deleted_message", 2));
            this.$refs.table.refresh();
          } else if (res.data.error) {
            window.toastr["error"](res.data.message);
          }
        }
      });
    },

    async permissionsUserModule() {
      const data = {
        module: "users",
      };
      const permissions = await this.getUserModules(data);
      // valida que el usuario tenga permiso para ingresar al modulo
      if (permissions.super_admin == false) {
        if (permissions.exist == false) {
          this.$router.push("/admin/dashboard");
        } else {
          const modulePermissions = permissions.permissions[0];
          if (modulePermissions == null) {
            this.$router.push("/admin/dashboard");
          } else if (modulePermissions.access == 0) {
            this.$router.push("/admin/dashboard");
          }
        }
      }

      // valida que el usuario tenga el permiso create, read, delete, update
      if (permissions.super_admin == true) {
        this.permissionModule.create = true;
        this.permissionModule.update = true;
        this.permissionModule.delete = true;
        this.permissionModule.read = true;
      } else if (permissions.exist == true) {
        const modulePermissions = permissions.permissions[0];
        if (modulePermissions.create == 1) {
          this.permissionModule.create = true;
        }
        if (modulePermissions.update == 1) {
          this.permissionModule.update = true;
        }
        if (modulePermissions.delete == 1) {
          this.permissionModule.delete = true;
        }
        if (modulePermissions.read == 1) {
          this.permissionModule.read = true;
        }
      }
    },
  },
};
</script>
