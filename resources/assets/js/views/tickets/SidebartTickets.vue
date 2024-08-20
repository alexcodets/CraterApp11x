<template>
  <div
    class="fixed top-0 left-0 hidden h-full pt-16 pb-4 ml-56 bg-white xl:ml-64 w-64 xl:block"
  >
    <div class="p-5">
      <sw-page-header :title="$tc('tickets.title', 1)">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />
          <sw-breadcrumb-item
            :title="$tc('tickets.tickets', 2)"
            to="#"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>

      <sw-list
        v-for="(menuItem, index) in menuItems"
        :title="$t(menuItem.title)"
        :key="index"
      >
        <!-- MENU PRINCIPAL -->
        <sw-list-item
          :title="$t(menuItem.title)"
          :to="menuItem.link"
          :active="hasActiveUrl(menuItem.link)"
          tag-name="router-link"
          class="py-3"
          v-if="menuItem.show"
        >
          <component slot="icon" :is="menuItem.icon" class="h-5" />
        </sw-list-item>
        <span v-if="menuItem.subMenuItems != null">
          <!-- SUB MENÚ -->
          <sw-list-item
            v-for="(subMenuItem, idx) in menuItem.subMenuItems"
            :title="$t(subMenuItem.title)"
            :key="idx"
            :to="subMenuItem.link"
            :active="hasActiveUrl(subMenuItem.link)"
            tag-name="router-link"
            class="px-5"
          >
            <component slot="icon" :is="subMenuItem.icon" class="h-5" />
          </sw-list-item>
        </span>
        <!--
                        <span v-else>
                MENU PRINCIPAL 
                <sw-list-item
                :title="$t(menuItem.title)"
                :key="index"
                :to="menuItem.link"
                :active="hasActiveUrl(menuItem.link)"
                tag-name="router-link"
                class="py-3"
                >
                <component slot="icon" :is="menuItem.icon" class="h-5" />
                </sw-list-item>
            </span>
            -->
      </sw-list>
    </div>
    <!-- -------------------------------- -->
    <!-- Contenido adicional en modo móvil -->

    <div class="pb-6 xl:hidden">
      <sw-page-header :title="$tc('tickets.title', 1)">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />
          <sw-breadcrumb-item
            :title="$tc('tickets.tickets', 2)"
            to="#"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>
    </div>

    <!--  <div class="pb-6">-->

    <div class="w-full mb-6 select-wrapper xl:hidden">
      <sw-select
        :options="menuItems"
        v-model="currentPbx"
        :searchable="true"
        :show-labels="false"
        :allow-empty="false"
        :custom-label="getCustomLabel"
        @input="navigateToPBX"
      />
    </div>

    <!-- <div class="hidden mt-1 xl:block"> -->
    <div class="pb-6 xl:hidden">
      4
      <sw-list
        v-for="(menuItem, index) in menuItems"
        :title="$t(menuItem.title)"
        :key="index"
      >
        <!-- MENU PRINCIPAL -->
        <sw-list-item
          :title="$t(menuItem.title)"
          :to="menuItem.link"
          :active="hasActiveUrl(menuItem.link)"
          tag-name="router-link"
          class="py-3"
        >
          <component slot="icon" :is="menuItem.icon" class="h-5" />
        </sw-list-item>
        <span v-if="menuItem.subMenuItems != null">
          <!-- SUB MENÚ -->
          <sw-list-item
            v-for="(subMenuItem, idx) in menuItem.subMenuItems"
            :title="$t(subMenuItem.title)"
            :key="idx"
            :to="subMenuItem.link"
            :active="hasActiveUrl(subMenuItem.link)"
            tag-name="router-link"
            class="px-5"
            v-if="subMenuItem.show"
          >
            <component slot="icon" :is="subMenuItem.icon" class="h-5" />
          </sw-list-item>
        </span>
      </sw-list>
    </div>
  </div>
</template>

<script>
import {
  UserIcon,
  OfficeBuildingIcon,
  BellIcon,
  CheckCircleIcon,
  ClipboardListIcon,
  CubeIcon,
  ClipboardCheckIcon,
  EyeIcon,
  CollectionIcon,
  ViewGridIcon,
} from '@vue-hero-icons/outline'
import { mapGetters, mapActions } from 'vuex'

import {
  RefreshIcon,
  CogIcon,
  MailIcon,
  PencilAltIcon,
  CloudUploadIcon,
  FolderIcon,
  DatabaseIcon,
  CreditCardIcon,
  BadgeCheckIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    UserIcon,
    OfficeBuildingIcon,
    PencilAltIcon,
    CogIcon,
    CheckCircleIcon,
    ClipboardListIcon,
    MailIcon,
    BellIcon,
    FolderIcon,
    RefreshIcon,
    CubeIcon,
    CloudUploadIcon,
    DatabaseIcon,
    CreditCardIcon,
    ClipboardCheckIcon,
    BadgeCheckIcon,
    EyeIcon,
    CollectionIcon,
    ViewGridIcon,
  },

  data() {
    return {
      currentPbx: {
        link: '/admin/tickets',
        title: 'tickets.menu_title.departaments',
        icon: 'collection-icon',
      },
      accessModule: {
        tickets_depa: false,
        tickets_email_temp: false,
        tickets: false,
      },
    }
  },

  computed: {
    menuItems() {
      const menu = [
        {
          link: '/admin/tickets/departaments',
          title: 'tickets.menu_title.departaments',
          icon: 'collection-icon',
          show: this.accessModule.tickets_depa,
        },
        {
          link: '/admin/tickets/main',
          title: 'tickets.title',
          icon: 'collection-icon',
          show: this.accessModule.tickets,
        },
        {
          link: '/admin/tickets/email',
          title: 'tickets.email.title',
          icon: 'collection-icon',
          show: this.accessModule.tickets_email_temp,
        },
      ]
      return menu
    },
  },

  watch: {},

  mounted() {
    this.currentPbx = this.menuItems.find(
      (item) => item.link == this.$route.path
    )
  },

  created() {
    this.fetchUserPermission()
    if (this.$route.path === '/admin/tickets') {
      /* this.$router.push('/admin/tickets/departaments') */
      this.$router.push('/admin/tickets/main')
    }
  },

  methods: {
    ...mapActions('user', ['getUserModules']),

    getCustomLabel({ title }) {
      return this.$t(title)
    },
    hasActiveUrl(url) {
      return this.$route.path.indexOf(url) > -1
    },
    navigateToPBX(pbx) {
      this.$router.push(pbx.link)
    },

    async fetchUserPermission() {
      const data = {
        module: 'COMPLETE_ALL',
      }

      const permissions = await this.getUserModules(data)
      if (permissions.super_admin == true) {
        ;(this.accessModule.tickets = true),
          (this.accessModule.tickets_depa = true),
          (this.accessModule.tickets_email_temp = true)
      } else {
        const data = {
          tickets: false,
          tickets_depa: false,
          tickets_email_temp: false,
        }

        const arrayPermissions = permissions.permissions
        arrayPermissions.forEach((element) => {
          if (element.access == 1) {
            data[element.module] = true
          }
        })
        ;(this.accessModule.tickets = data.tickets),
          (this.accessModule.tickets_depa = data.tickets_depa),
          (this.accessModule.tickets_email_temp = data.tickets_email_temp)
      }
    },
    async permissionsUserModule() {
      const data = {
        module: 'tickets',
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if (permissions.super_admin == false) {
        if (permissions.permissions[0] == null) {
          this.$router.push('/admin/dashboard')
        } else if (permissions.exist == false) {
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
    },
  },
}
</script>
