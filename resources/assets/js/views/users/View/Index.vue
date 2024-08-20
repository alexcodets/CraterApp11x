<template>
  <base-page v-if="isSuperAdmin" class="items">
    <!-- Page Header -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
     
     
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item to="/admin/users" :title="$tc('users.user', 2)" />
        <sw-breadcrumb-item
          to=""
          :title="name_user"
          active
          @click.native="$router.go()"
        />
      </sw-breadcrumb>

      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          tag-name="router-link"
          :to="`/admin/users`"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary-outline"
        >
          {{ $t('general.go_back') }}
        </sw-button>
        <sw-button
          tag-name="router-link"
          :to="`/admin/users/${userData.id}/edit`"
  
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary-outline"
          v-if="permissionModule.update"
        >
          <pencil-icon class="h-5 mr-3 text-gray-600" />
          {{ $t('general.edit') }}
        </sw-button>
        <sw-dropdown v-if="permissionModule.update">
          <sw-button slot="activator" variant="primary" 
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0">
            <dots-horizontal-icon class="h-5 -ml-1 -mr-1" />
          </sw-button>

          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/users/${idUser}/permissions`"
            v-if="permissionModule.update"
          >
            <lock-closed-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('roles.permissions') }}
          </sw-dropdown-item>
        </sw-dropdown>
      </div>
    </div>

    <sw-card>
      <base-loader v-if="loadding" />
      <div class="col-span-12">
        <p class="text-gray-500 uppercase sw-section-title">
          {{ $t('corePbx.custom_did_groups.basic_info') }}
        </p>

        <div class="grid grid-cols-1 gap-4 mt-5 md:grid-cols-3">
          <div
            class="grid grid-cols-1 gap-4 mt-5 md:grid-cols-2 sm:grid-cols-1 col-span-2"
          >
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('users.name') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ userData.name }}
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('users.email') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ userData.email }}
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('users.phone') }}
              </p>
              <div v-if="userData.phone">
                <p class="text-sm font-bold leading-5 text-black non-italic">
                  {{ userData.phone }}
                </p>
              </div>
              <div v-else>
                <p
                  class="text-sm font-bold leading-5 text-black non-italic mt-2"
                >
                  N/A
                </p>
              </div>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('roles.role') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ userData.role2 }}
              </p>
            </div>
          </div>
          <div class="grid grid-cols-1 gap-4 mt-5 col-span-1">
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('users.departaments') }}
              </p>
              <div
                v-if="userData.item_groups && userData.item_groups.length > 0"
              >
                <p
                  v-for="(dep, indexDep) in userData.item_groups"
                  :key="indexDep"
                  class="text-sm font-bold leading-5 text-black non-italic mt-2"
                >
                  asdasas
                </p>
              </div>
              <div v-else>
                <p
                  class="text-sm font-bold leading-5 text-black non-italic mt-2"
                >
                  N/A
                </p>
              </div>
            </div>
          </div>
        </div>

        <sw-divider class="my-8" />

        <div v-if="permissionModule.accessEstimates">
          <table-estimates
            v-if="userData.id"
            :userId="userData.id"
            :readEstimates="permissionModule.readEstimates"
          />
        </div>
        <div v-if="permissionModule.accessTickets">
          <table-tickets
            v-if="userData.id"
            :userId="userData.id"
            :readTickets="permissionModule.readTickets"
          />
        </div>
      </div>
    </sw-card>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import {
  PencilIcon,
  ArrowLeftIcon,
  LockClosedIcon,
} from '@vue-hero-icons/solid'
import { DotsHorizontalIcon } from '@vue-hero-icons/outline'
import tableEstimates from './TableEstimates.vue'
import tableTickets from './TableTickets.vue'

export default {
  components: {
    PencilIcon,
    ArrowLeftIcon,
    LockClosedIcon,
    DotsHorizontalIcon,
    tableEstimates,
    tableTickets,
  },
  data() {
    return {
      loadding: false,
      customDidGroup: {
        custom_dids: [],
        status: null,
      },
      userData: {},
      permissionModule: {
        create: false,
        read: false,
        delete: false,
        update: false,
        readTickets: false,
        accessTickets: false,
        readEstimates: false,
        accessEstimates: false,
      },
      name_user: '',
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('customDidGroup', ['selectedViewCustomDidGroup']),
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    showEmptyTable() {
      return !this.customDidGroup.custom_dids.length
    },

    getGroupedDIDs() {
      let groupedDIDs = {}

      this.customDidGroup.custom_dids.forEach((did) => {
        if (!groupedDIDs.hasOwnProperty(did.category_name)) {
          groupedDIDs[did.category_name] = []
        }
        groupedDIDs[did.category_name].push({ ...did })
      })

      return groupedDIDs
    },

    getSortedObject() {
      let object = this.getGroupedDIDs
      // New object which will be returned with sorted keys
      var sortedObject = {}

      // Get array of keys from the old/current object
      var keys = Object.keys(object)
      // Sort keys (in place)
      keys.sort()

      // Use sorted keys to copy values from old object to the new one
      for (var i = 0, size = keys.length; i < size; i++) {
        let key = keys[i]
        let value = object[key]
        sortedObject[key] = value
      }

      // Return the new object
      return sortedObject
    },
    idUser() {
      return this.$route.params.id
    },
  },
  created() {
    this.permissionsUserModule()
    this.loadUserData()
  },
  methods: {
    ...mapActions('users', ['showUserId']),
    ...mapActions('user', ['getUserModules']),

    async loadUserData() {
      try {
        this.loadding = true
        const res = await this.showUserId(this.idUser)
        this.name_user = res.data.data.name
        this.userData = res.data?.data
      } catch (error) {
      } finally {
        this.loadding = false
      }
    },
    async permissionsUserModule() {
      const data = {
        module: 'users',
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
        this.permissionModule.update = true
        this.permissionModule.delete = true
        this.permissionModule.read = true
      } else if (permissions.exist == true) {
        const modulePermissions = permissions.permissions[0]
        if (modulePermissions.create == 1) {
          this.permissionModule.create = true
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

      const dataEstimates = {
        module: 'estimates',
      }
      const permissionsEstimates = await this.getUserModules(dataEstimates)
      if (permissionsEstimates.super_admin == true) {
        this.permissionModule.readEstimates = true
        this.permissionModule.accessEstimates = true
      } else if (permissionsEstimates.exist == true) {
        const modulePermissions = permissionsEstimates.permissions[0]
        if (modulePermissions == null) {
          this.permissionModule.readEstimates = false
          this.permissionModule.accessEstimates = false
        } else {
          if (modulePermissions.access == 1) {
            this.permissionModule.accessEstimates = true
          }
          if (modulePermissions.read == 1) {
            this.permissionModule.readEstimates = true
          }
        }
      }

      const dataTickets = {
        module: 'tickets',
      }
      const permissionsTickets = await this.getUserModules(dataTickets)

      if (permissionsTickets.super_admin == true) {
        this.permissionModule.readTickets = true
        this.permissionModule.accessTickets = true
      } else if (permissionsTickets.exist == true) {
        const modulePermissions = permissionsTickets.permissions[0]
        if (modulePermissions == null) {
          this.permissionModule.readTickets = false
          this.permissionModule.accessTickets = false
        } else {
          if (modulePermissions.access == 1) {
            this.permissionModule.accessTickets = true
          }
        }
        if (modulePermissions.read == 1) {
          this.permissionModule.readTickets = true
        }
      }
    },
  },
}
</script>

<style scoped>
</style>