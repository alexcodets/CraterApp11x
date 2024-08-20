<template>
  <base-page>
    <div :class="{ 'xl:pl-64': showSideBar }">
      <div class="w-full flex justify-end">
        <div class="mb-3 hidden xl:block">
          <sw-button variant="primary-outline" @click="toggleSideBar">
            {{ $t('tickets.departaments.menu') }}
            <component :is="listIcon" class="w-4 h-4 ml-2 -mr-1" />
          </sw-button>
        </div>
      </div>

      <div class="mt-5 pb-6 xl:hidden">
        {{ $t('corePbx.pbx') }}
      </div>
      <div class="w-full mb-6 select-wrapper xl:hidden">
        <sw-select
          :options="menuItemsSelect"
          v-model="currentPbx"
          :searchable="true"
          :show-labels="false"
          :allow-empty="false"
          :custom-label="getCustomLabel"
          @input="navigateToPBX"
        />
      </div>

      <slide-x-left-transition v-show="showSideBar">
        <div class="fixed hidden h-full top-0 left-0 pt-16 pb-4 bg-white xl:ml-64 w-64 xl:block">
          <div class="p-5">
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

            <div class="grid md:grid-cols-12">
              <div
                class="hidden col-span-12 mt-1 xl:block"
                v-if="this.$route.path != '/admin/corePBX/PBXwareDashboard'"
              >
                <sw-list
                  v-for="(menuItem, index) in menuItems"
                  :title="$t(menuItem.title)"
                  :key="index"
                  v-if="menuItem.show"
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
            </div>
          </div>
        </div>
      </slide-x-left-transition>

      <transition name="fade" mode="out-in">
          <router-view />
      </transition>

      <!-- <div
        v-if="this.$route.path != '/admin/corePBX/PBXwareDashboard'"
        class="col-span-12 xl:col-span-9"
      >

      </div>
      <div
        v-if="this.$route.path === '/admin/corePBX/PBXwareDashboard'"
        class="col-span-12 xl:col-span-12"
      >
        <transition name="fade" mode="out-in">
          <router-view />
        </transition>
      </div> -->
    </div>
  </base-page>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

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
  XIcon,
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
    XIcon,
  },

  data() {
    return {
      showSideBar: true,
      isShow: true,
      currentPbx: {
        link: '/admin/corePBX',
        title: 'corePbx.menu_title.packages',
        icon: 'collection-icon',
      },
      accessModule: {
        pbx_packages: false,
        pbx_extension: false,
        pbx_did: false,
        pbx_app_rate: false,
        pbx_custom_did: false,
        pbx_custom_destination: false,
        pbx_customization: false,
        pbx_report: false,
        pbx_tenant: false,
        pbx_tenant_list: false,
      },
    }
  },

  computed: {
    listIcon() {
      return this.showSideBar ? 'x-icon' : 'clipboard-list-icon'
    },
    menuItems() {
      const menu = [
        {
          link: '/admin/corePBX/packages',
          title: 'corePbx.menu_title.packages',
          icon: 'collection-icon',
          show: this.accessModule.pbx_packages,
        },
        /*  {
          link: '/admin/corePBX/PBXwareDashboard',
          title: 'corePbx.menu_title.pbxwareDashboard',
          icon: 'cube-icon',
        }, */
        {
          link: '#',
          title: 'corePbx.menu_title.billingTemplates',
          icon: 'view-grid-icon',
          show: true,
          subMenuItems: [
            {
              link: '/admin/corePBX/billing-templates/extensions',
              title: 'corePbx.submenu_title.extensions',
              icon: 'cog-icon',
              show: this.accessModule.pbx_extension,
            },
            {
              link: '/admin/corePBX/billing-templates/did',
              title: 'corePbx.submenu_title.did',
              icon: 'cube-icon',
              show: this.accessModule.pbx_did,
            },
            {
              link: '/admin/corePBX/billing-templates/custom-app-rate',
              title: 'corePbx.submenu_title.custom_app_rate',
              icon: 'cube-icon',
              show: this.accessModule.pbx_app_rate,
            },
            {
              link: '/admin/corePBX/billing-templates/custom-did-groups',
              title: 'corePbx.submenu_title.didCustom',
              icon: 'collection-icon',
              show: this.accessModule.pbx_custom_did,
            },
            {
              link: '/admin/corePBX/billing-templates/prefix-groups',
              title: 'corePbx.submenu_title.Internacional',
              icon: 'cube-icon',
              show: this.accessModule.pbx_custom_destination,
            },
          ],
        },
        {
          link: '#',
          title: 'corePbx.menu_title.config',
          icon: 'cog-icon',
          show: true,
          subMenuItems: [
            {
              link: '/admin/corePBX/config/customization',
              title: 'corePbx.submenu_title.customization',
              icon: 'view-grid-icon',
              show: this.accessModule.pbx_customization,
            },
          ],
        },
        {
          link: '/admin/corePBX/reports/reports-pbx',
          title: 'corePbx.menu_title.reports',
          icon: 'collection-icon',
          show: this.accessModule.pbx_report,
        },
        {
          link: '/admin/corePBX/tenant/tenants-list',
          title: 'corePbx.menu_title.tenants_list',
          icon: 'collection-icon',
          show: this.accessModule.pbx_tenant_list,
        },
      
      ]
      return menu
    },

    menuItemsSelect() {
      let flatMenu = []
      this.menuItems.forEach(item => {
        // Añadir el prefijo al título del menú principal
        flatMenu.push({
            link: item.link,
            title: '📂 ' + this.$t(item.title),
            icon: item.icon,
            show: item.show
        });

        // Verificar si hay submenús y añadirlos al arreglo
        if (item.subMenuItems) {
            item.subMenuItems.forEach(subItem => {
                flatMenu.push({
                    link: subItem.link,
                    title: '📄 ' + this.$t(subItem.title),
                    icon: subItem.icon,
                    show: subItem.show
                });
            });
        }
    });

    return flatMenu;
    },
  },
  watch: {
    '$route.path'(newValue) {
      if (newValue === '/admin/corePBX') {
        // this.$router.push('/admin/corePBX')
        this.redirectPageSettings(this.menuItems)
      }
    },
  },

  mounted() {
    this.currentPbx = this.menuItems.find(
      (item) => item.link == this.$route.path
    )
  },

  created() {
    // this.redirectPageSettings(this.menuItems)
    this.permissionsUserModule()
    this.fetchUserPermission()
    if (this.$route.path === '/admin/corePBX') {
    this.$router.push('/admin/corePBX/packages')
    }
  },

  methods: {
    ...mapActions('user', ['getUserModules']),

    getCustomLabel({ title }) {
      return title
    },
    hasActiveUrl(url) {
      //console.log(this.$route.path)
      return this.$route.path.indexOf(url) > -1
    },
    navigateToPBX(pbx) {
      if (pbx.link === '#') return
      this.$router.push(pbx.link)
    },
    toggleSideBar() {
      this.showSideBar = !this.showSideBar
    },
    async fetchUserPermission() {
      const data = {
        module: 'COMPLETE_ALL',
      }

      const permissions = await this.getUserModules(data)

      if (permissions.super_admin == true) {
          (this.accessModule.pbx_packages = true),
          (this.accessModule.pbx_extension = true),
          (this.accessModule.pbx_did = true),
          (this.accessModule.pbx_app_rate = true),
          (this.accessModule.pbx_custom_did = true),
          (this.accessModule.pbx_custom_destination = true),
          (this.accessModule.pbx_customization = true),
          (this.accessModule.pbx_report = true),
          (this.accessModule.pbx_tenant = true),
          (this.accessModule.pbx_tenant_list = true)
      } else {
        const data = {
          pbx_packages: false,
          pbx_extension: false,
          pbx_did: false,
          pbx_app_rate: false,
          pbx_custom_did: false,
          pbx_custom_destination: false,
          pbx_customization: false,
          pbx_report: false,
          pbx_tenant: false,
          pbx_tenant_list: false,
        }

        const arrayPermissions = permissions.permissions

        arrayPermissions.forEach((element) => {
          if (element.access == 1) {
            data[element.module] = true
          }
        })
          (this.accessModule.pbx_packages = data.pbx_packages),
          (this.accessModule.pbx_extension = data.pbx_extension),
          (this.accessModule.pbx_did = data.pbx_did),
          (this.accessModule.pbx_app_rate = data.pbx_app_rate),
          (this.accessModule.pbx_custom_did = data.pbx_custom_did),
          (this.accessModule.pbx_custom_destination = data.pbx_custom_destination),
          (this.accessModule.pbx_customization = data.pbx_customization),
          (this.accessModule.pbx_report = data.pbx_report),
          (this.accessModule.pbx_tenant = data.pbx_tenant),
          (this.accessModule.pbx_tenant_list = data.pbx_tenant_list)
      }
      this.redirectPageSettings(this.menuItems)
    },
    async permissionsUserModule() {
      const data = {
        module: 'corepbx',
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

    redirectPageSettings(menu) {
      const status = false
      for (let index = 0; index < menu.length; index++) {
        const element = menu[index]
        if (element.show == true && element.subMenuItems != undefined) {
          for (let index2 = 0; index2 < element.subMenuItems.length; index2++) {
            const item = element.subMenuItems[index2]
            if (item.show) {
              this.$router.push(item.link)
              status = true
              break
            }
          }

          if (status) {
            break
          }
        } else if (element.show == true && element.subMenuItems == undefined) {
          if (element.link == '/admin/corePBX/reports/reports-pbx') {
            this.$router.push('/admin/corePBX')
            break
          } else {
            this.$router.push(element.link)
            break
          }
        }
      }
    },
  },
}
</script>
