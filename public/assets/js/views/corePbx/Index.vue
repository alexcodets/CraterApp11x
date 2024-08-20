<template>
  <base-page>
    <div class="pb-6">
      <sw-page-header :title="$tc('corePbx.title', 1)">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />
          <sw-breadcrumb-item
            :title="$tc('corePbx.corePbx', 2)"
            to="#"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>
    </div>
    <div class="pb-6">
      {{ $t('corePbx.pbx') }}
    </div>
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

    <div class="grid md:grid-cols-12">
      <div class="hidden col-span-3 mt-1 xl:block" v-if="this.$route.path != '/admin/corePBX/PBXwareDashboard'">
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

      <div v-if="this.$route.path != '/admin/corePBX/PBXwareDashboard'" class="col-span-12 xl:col-span-9">
        <transition name="fade" mode="out-in">
          <router-view />
        </transition>
      </div>
      <div v-if="this.$route.path === '/admin/corePBX/PBXwareDashboard'" class="col-span-12 xl:col-span-12">
        <transition name="fade" mode="out-in">
          <router-view />
        </transition>
      </div>
    </div>
  </base-page>
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
      isShow: true,
      currentPbx: {
        link: '/admin/corePBX',
        title: 'corePbx.menu_title.packages',
        icon: 'collection-icon',
      },
      menuItems: [
        {
          link: '/admin/corePBX/packages',
          title: 'corePbx.menu_title.packages',
          icon: 'collection-icon',
        },
       /*  {
          link: '/admin/corePBX/PBXwareDashboard',
          title: 'corePbx.menu_title.pbxwareDashboard',
          icon: 'cube-icon',
        }, */
        {
          link: '/admin/corePBX/billing-templates/extensions',
          title: 'corePbx.menu_title.billingTemplates',
          icon: 'view-grid-icon',
          subMenuItems: [
            {
              link: '/admin/corePBX/billing-templates/extensions',
              title: 'corePbx.submenu_title.extensions',
              icon: 'cog-icon',
            },
            {
              link: '/admin/corePBX/billing-templates/did',
              title: 'corePbx.submenu_title.did',
              icon: 'cube-icon',
            },
            {
              link: '/admin/corePBX/billing-templates/custom-app-rate',
              title: 'corePbx.submenu_title.custom_app_rate',
              icon: 'cube-icon',
            },
            {
              link: '/admin/corePBX/billing-templates/custom-did-groups',
              title: 'corePbx.submenu_title.didCustom',
              icon: 'collection-icon',
            },
            {
              link: '/admin/corePBX/billing-templates/prefix-groups',
              title: 'corePbx.submenu_title.Internacional',
              icon: 'cube-icon',
            },
          ],
        },{
          link:'/admin/corePBX/config/customization',
          title: 'corePbx.menu_title.config',
          icon: 'cog-icon',
           subMenuItems:[
             {
              link: '/admin/corePBX/config/customization',
              title: 'corePbx.submenu_title.customization',
              icon: 'view-grid-icon',
             },
           ]
        }
      ],
    }
  },

  watch: {
    '$route.path'(newValue) {
      if (newValue === '/admin/corePBX') {
        this.$router.push('/admin/corePBX')
      }
    },
  },

  mounted() {
    this.currentPbx = this.menuItems.find(
      (item) => item.link == this.$route.path
    )
  },

  created() {
    
    if (this.$route.path === '/admin/corePBX') {
      this.$router.push('/admin/corePBX/packages')
    }
  },

  methods: {
    getCustomLabel({ title }) {
      return this.$t(title)
    },
    hasActiveUrl(url) {
      //console.log(this.$route.path)
      return this.$route.path.indexOf(url) > -1
    },
    navigateToPBX(pbx) {
      this.$router.push(pbx.link)

    },
  },
}
</script>