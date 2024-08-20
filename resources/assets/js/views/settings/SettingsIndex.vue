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
        <sw-page-header :title="$tc('settings.setting', 1)">
          <sw-breadcrumb slot="breadcrumbs">
            <sw-breadcrumb-item
              :title="$t('general.home')"
              to="/admin/dashboard"
            />
            <sw-breadcrumb-item
              :title="$tc('settings.setting', 2)"
              to="/admin/settings/user-profile"
              active
            />
          </sw-breadcrumb>
        </sw-page-header>
      </div>
      
      <div class="w-full mb-6 select-wrapper xl:hidden">
        <sw-select
          :options="menuItems"
          v-model="currentSetting"
          :searchable="true"
          :show-labels="false"
          :allow-empty="false"
          :custom-label="getCustomLabel"
          @input="navigateToSetting"
        />
      </div>

      <slide-x-left-transition v-show="showSideBar">
        <div
          class="fixed hidden h-full top-0 left-0 pt-16 pb-4 bg-white xl:ml-64 w-64 xl:block overflow-auto sw-scroll"
        >
          <div class="p-6">
            <sw-page-header :title="$tc('settings.setting', 1)">
              <sw-breadcrumb slot="breadcrumbs">
                <sw-breadcrumb-item
                  :title="$t('general.home')"
                  to="/admin/dashboard"
                />
                <sw-breadcrumb-item
                  :title="$tc('settings.setting', 2)"
                  to="/admin/settings/user-profile"
                  active
                />
              </sw-breadcrumb>
            </sw-page-header>

            <div class="grid md:grid-cols-12">
              <div class="hidden col-span-12 mt-1 xl:block">
                <sw-list>
                  <div v-for="(item, index) in menuItems" :key="index">
                    <sw-list-item
                      v-if="item.show"
                      :title="$t(item.title)"
                      :to="item.link"
                      :active="hasActiveUrl(item.link)"
                      tag-name="router-link"
                      class="py-3"
                    >
                      <component slot="icon" :is="item.icon" class="h-5" />
                    </sw-list-item>
                  </div>
                </sw-list>
              </div>
            </div>
          </div>
        </div>
      </slide-x-left-transition>

      <div class="col-span-12 xl:col-span-9">
        <transition name="fade" mode="out-in">
          <router-view />
        </transition>
      </div>
    </div>
    <!-- </div> -->
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
  CreditCardIcon,
  CashIcon,
  DeviceMobileIcon,
} from '@vue-hero-icons/outline'

import {
  RefreshIcon,
  ReceiptTaxIcon,
  CogIcon,
  MailIcon,
  PencilAltIcon,
  CloudUploadIcon,
  FolderIcon,
  DatabaseIcon,
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
    DeviceMobileIcon,
    CreditCardIcon,
    ClipboardCheckIcon,
    BadgeCheckIcon,
    EyeIcon,
    ReceiptTaxIcon,
    CashIcon,
    XIcon,
  },

  data() {
    return {
      showSideBar: true,
      currentSetting: {
        link: '/admin/settings/user-profile',
        title: 'settings.menu_title.account_settings',
        icon: 'user-icon',
      },
      accessModule: {
        retentionActive: false,
        account_settings: false,
        company_info: false,
        preferences: false,
        customizations: false,
        notifications: false,
        tax_Groups: false,
        tax_types: false,
        payment_modes: false,
        custom_fields: false,
        notes: false,
        expense_categories: false,
        mail_configuration: false,
        file_disk: false,
        backup: false,
        logs: false,
        Modules: false,
        roles: false,
        payment_gateways: false,
        retentions: false,
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
          link: '/admin/settings/user-profile',
          title: 'settings.menu_title.account_settings',
          icon: 'user-icon',
          show: this.accessModule.account_settings,
        },
        {
          link: '/admin/settings/company-info',
          title: 'settings.menu_title.company_information',
          icon: 'office-building-icon',
          show: this.accessModule.company_info,
        },
        {
          link: '/admin/settings/preferences',
          title: 'settings.menu_title.preferences',
          icon: 'cog-icon',
          show: this.accessModule.preferences,
        },
        {
          link: '/admin/settings/customization',
          title: 'settings.menu_title.customization',
          icon: 'pencil-alt-icon',
          show: this.accessModule.customizations,
        },
        {
          link: '/admin/settings/notifications',
          title: 'settings.menu_title.notifications',
          icon: 'bell-icon',
          show: this.accessModule.notifications,
        },
        {
          link: '/admin/settings/tax-groups',
          title: 'settings.menu_title.tax_groups',
          icon: 'badge-check-icon',
          show: this.accessModule.tax_Groups,
        },
        {
          link: '/admin/settings/tax-types',
          title: 'settings.menu_title.tax_types',
          icon: 'check-circle-icon',
          show: this.accessModule.tax_types,
        },
        {
          link: '/admin/settings/payment-mode',
          title: 'settings.menu_title.payment_modes',
          icon: 'credit-card-icon',
          show: this.accessModule.payment_modes,
        },
        {
          link: '/admin/settings/custom-fields',
          title: 'settings.menu_title.custom_fields',
          icon: 'cube-icon',
          show: this.accessModule.custom_fields,
        },
        {
          link: '/admin/settings/notes',
          title: 'settings.menu_title.notes',
          icon: 'clipboard-check-icon',
          show: this.accessModule.notes,
        },
        {
          link: '/admin/settings/expense-category',
          title: 'settings.menu_title.expense_category',
          icon: 'clipboard-list-icon',
          show: this.accessModule.expense_categories,
        },

        {
          link: '/admin/settings/mail-configuration',
          title: 'settings.mail.mail_config',
          icon: 'mail-icon',
          show: this.accessModule.mail_configuration,
        },
        {
          link: '/admin/settings/file-disk',
          title: 'settings.menu_title.file_disk',
          icon: 'folder-icon',
          show: this.accessModule.file_disk,
        },
        {
          link: '/admin/settings/backup',
          title: 'settings.menu_title.backup',
          icon: 'database-icon',
          show: this.accessModule.backup,
        },

        {
          link: '/admin/settings/logs',
          title: 'settings.menu_title.logs',
          icon: 'eye-icon',
          show: this.accessModule.logs,
        },
        {
          link: '/admin/settings/modules',
          title: 'settings.menu_title.modules',
          icon: 'cog-icon',
          show: this.accessModule.Modules,
        },
        {
          link: '/admin/roles',
          title: 'navigation.roles',
          icon: 'user-icon',
          show: this.accessModule.roles,
        },
        {
          link: '/admin/settings/payment-gateways',
          title: 'settings.menu_title.payments_gateways',
          icon: 'cash-icon',
          show: this.accessModule.payment_gateways,
        },
        {
          link: '/admin/settings/retentions',
          title: 'settings.menu_title.retentions',
          icon: 'receipt-tax-icon',
          show: this.accessModule.retentions,
        },
      ]
      return menu
    },
  },

  watch: {
    '$route.path'(newValue) {
      this.redirectPageSettings(this.menuItems)
      // if (newValue === '/admin/settings') {
      //   this.$router.push('/admin/settings/user-profile')
      // }
    },
  },

  mounted() {
    this.currentSetting = this.menuItems.find(
      (item) => item.link == this.$route.path
    )
  },

  created() {
    this.permissionsUserModule()
    this.fetchUserPermission()
  },

  methods: {
    ...mapActions('user', ['getUserModules']),
    ...mapActions('company', ['fetchCompanySettings']),

    getCustomLabel({ title }) {
      return this.$t(title)
    },
    hasActiveUrl(url) {
      return this.$route.path.indexOf(url) > -1
    },
    toggleSideBar() {
      this.showSideBar = !this.showSideBar
    },
    navigateToSetting(setting) {
      this.$router.push(setting.link)
    },
    async fetchUserPermission() {
      const data = {
        module: 'COMPLETE_ALL',
      }

      const permissions = await this.getUserModules(data)
      if (permissions.super_admin == true) {
        ;(this.accessModule.account_settings = true),
          (this.accessModule.company_info = true),
          (this.accessModule.preferences = true),
          (this.accessModule.customizations = true),
          (this.accessModule.notifications = true),
          (this.accessModule.tax_Groups = true),
          (this.accessModule.tax_types = true),
          (this.accessModule.payment_modes = true),
          (this.accessModule.custom_fields = true),
          (this.accessModule.notes = true),
          (this.accessModule.expense_categories = true),
          (this.accessModule.mail_configuration = true),
          (this.accessModule.file_disk = false),
          (this.accessModule.backup = false),
          (this.accessModule.logs = true),
          (this.accessModule.Modules = true),
          (this.accessModule.roles = true),
          (this.accessModule.payment_gateways = true),
          (this.accessModule.retentions = true)
      } else {
        const data = {
          account_settings: false,
          company_info: false,
          preferences: false,
          customizations: false,
          notifications: false,
          tax_Groups: false,
          tax_types: false,
          payment_modes: false,
          custom_fields: false,
          notes: false,
          expense_categories: false,
          mail_configuration: false,
          file_disk: false,
          backup: false,
          logs: false,
          Modules: false,
          roles: false,
          payment_gateways: false,
          retentions: false,
        }

        const arrayPermissions = permissions.permissions

        arrayPermissions.forEach((element) => {
          if (element.access == 1) {
            data[element.module] = true
          }
        })
        ;(this.accessModule.account_settings = data.account_settings),
          (this.accessModule.company_info = data.company_info),
          (this.accessModule.preferences = data.preferences),
          (this.accessModule.customizations = data.customizations),
          (this.accessModule.notifications = data.notifications),
          (this.accessModule.tax_Groups = data.tax_Groups),
          (this.accessModule.tax_types = data.tax_types),
          (this.accessModule.payment_modes = data.payment_modes),
          (this.accessModule.custom_fields = data.custom_fields),
          (this.accessModule.notes = data.notes),
          (this.accessModule.expense_categories = data.expense_categories),
          (this.accessModule.mail_configuration = data.mail_configuration),
          (this.accessModule.file_disk = false),
          (this.accessModule.backup = false),
          (this.accessModule.logs = data.logs),
          (this.accessModule.Modules = data.Modules),
          (this.accessModule.roles = data.roles),
          (this.accessModule.payment_gateways = data.payment_gateways),
          (this.accessModule.retentions = data.retentions)
      }
      let response = await this.fetchCompanySettings([
        'retention_platform_active',
      ])
      let retentionActive =
        response.data.retention_platform_active == 'YES' ? true : false
      if (response.data.retention_platform_active != undefined) {
        if (this.accessModule.retentions && retentionActive) {
          this.accessModule.retentions = true
        } else {
          this.accessModule.retentions = false
        }
      } else {
        this.accessModule.retentions = false
      }
      this.redirectPageSettings(this.menuItems)
    },

    async permissionsUserModule() {
      const data = {
        module: 'settings',
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
      for (let index = 0; index < menu.length; index++) {
        const element = menu[index]
        if (element.show == true) {
          if (this.$route.path === '/admin/settings') {
            this.$router.push(element.link)
            break
          }
        }
      }
    },
  },
}
</script>
