<template>
  <div
    class="fixed top-0 left-0 hidden h-full pt-16 pb-4 ml-56 bg-white xl:ml-64 xl:block"
  >
      <div class="p-5">
        <sw-page-header :title="$tc('reports.reports_PBX')">
            <sw-breadcrumb slot="breadcrumbs">
            <sw-breadcrumb-item
                :title="$t('general.home')"
                to="/admin/dashboard"
            />
            <sw-breadcrumb-item
                :title="$tc('corePbx.menu_title.reports', 2)"
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
    <!--  <div class="pb-6">
        {{ $t('corePbx.pbx') }}
        </div> -->

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
        <div class="hidden mt-1 xl:hidden">
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
        link: '/admin/corePBX/reports/departaments',
        title: 'tickets.menu_title.departaments',
        icon: 'collection-icon',
      },
      menuItems: [
        {
          link: '/admin/corePBX/reports/departaments',
          title: 'tickets.menu_title.departaments',
          icon: 'collection-icon',
        },
        {
          link: '/admin/corePBX/reports/reports-pbx',
          title: 'reports.reports_PBX',
          icon: 'collection-icon',
        },
        {
          link: '/admin/corePBX/reports/tenants',
          title: 'corePbx.menu_title.tenants',
          icon: 'collection-icon',
        },

      ],
    }
  },

  watch: {
  },

  mounted() {
    this.currentPbx = this.menuItems.find(
      (item) => item.link == this.$route.path
    )
  },

  created() {
    if (this.$route.path === '/admin/tickets') {
      /* this.$router.push('/admin/tickets/departaments') */
      this.$router.push('/admin/tickets/main')
    }
  },

  methods: {
    getCustomLabel({ title }) {
      return this.$t(title)
    },
    hasActiveUrl(url) {
      return this.$route.path.indexOf(url) > -1
    },
    navigateToPBX(pbx) {
      this.$router.push(pbx.link)
    },
  },
}
</script>
