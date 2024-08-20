<template>
  <div class="fixed top-0 left-0 hidden h-full pt-16 pb-4 ml-56 bg-white xl:ml-64 w-88 xl:block">
      <div class="p-8">
        <sw-page-header :title="$tc('bandwidth.title')">
            <sw-breadcrumb slot="breadcrumbs">
            <sw-breadcrumb-item
                :title="$t('general.home')"
                to="/admin/dashboard"
            />
            <sw-breadcrumb-item
                :title="$tc('bandwidth.title')"
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
            </sw-list>

      </div>
        <div class="pb-6 xl:hidden">
        <sw-page-header :title="$tc('bandwidth.title', 1)">
            <sw-breadcrumb slot="breadcrumbs">
            <sw-breadcrumb-item
                :title="$t('general.home')"
                to="/admin/dashboard"
            />
            <sw-breadcrumb-item
                :title="$tc('bandwidth.title')"
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
        link: '/admin/bandwidth/desconnect',
        title: 'bandwidth.desconnect',
        icon: 'collection-icon',
      },
      menuItems: [
        {
          link: '/admin/bandwidth/desconnect',
          title: 'bandwidth.desconnect',
          icon: 'collection-icon',
        },
        {
          link: '/admin/bandwidth/ordering',
          title: 'bandwidth.ordering',
          icon: 'collection-icon',
        },
        {
          link: '/admin/bandwidth/e911management',
          title: 'bandwidth.e911management',
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
