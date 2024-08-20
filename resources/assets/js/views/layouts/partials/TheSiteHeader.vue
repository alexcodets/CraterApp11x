<template>
  <header
    class="fixed top-0 left-0 z-40 flex items-center justify-between w-full px-4 py-3 md:h-16 md:px-8"
    :class="[
      isOriginalHeaderColor
        ? 'bg-gradient-to-r from-primary-500 to-primary-400'
        : 'header-bg-color',
    ]"
    :style="getColor"
  >
    <a
      href="/admin/dashboard"
      class="float-none text-lg not-italic font-black tracking-wider text-white brand-main md:float-left font-base"
    >
      <img
        v-if="previewLogo"
        id="logo-white"
        :src="previewLogo"
        class="hidden h-6 md:block"
      />
      <!--
        <img
        v-else
        id="logo-white"
        src="/assets/img/logo-corebill.png"
        alt="Crater Logo"
        class="hidden h-6 md:block"
      />-->
      <img
        v-if="previewLogo"
        id="logo-mobile"
        :src="previewLogo"
        class="block h-8 md:hidden"
      />
      <!--
        <img
        v-else
        id="logo-mobile"
        src="/assets/img/logo-corebill.png"
        alt="Crater Logo"
        class="block h-8 md:hidden"
      />-->
    </a>

    <ul class="float-right h-8 m-0 list-none md:h-9">
      <a
        v-if="allow_invoice_form_pos"
        to="/admin/corePOS/dashboard"
        href="/admin/corePOS/dashboard"
        class="flex float-left px-2 text-white bg-black border-0 rounded cursor-pointer md:mr-2 h-full"
      >
        <div class="flex items-center text-sm">
          <plus-icon class="h-4 -mx-1" />
          POS
        </div>
      </a>

      <global-search class="hidden float-left mr-2 md:block" />

      <a
        :class="{ 'is-active': isSidebarOpen }"
        href="#"
        class="flex float-left p-1 ml-3 overflow-visible text-sm text-black ease-linear bg-white border-0 rounded cursor-pointer md:hidden md:ml-0 hamburger hamburger--arrowturn"
        @click="toggleSidebar"
      >
        <div class="relative inline-block w-6 h-6">
          <div class="block hamburger-inner top-1/2" />
        </div>
      </a>

      <li class="relative hidden float-left m-0 md:block">
        <div
          v-if="
            permissionModule.create_invoice ||
            permissionModule.create_estimate ||
            permissionModule.create_customer
          "
        >
          <sw-dropdown>
            <a
              slot="activator"
              href="#"
              style="padding: 6px"
              class="inline-block text-sm text-black bg-white rounded-sm"
            >
              <plus-icon class="w-6 h-6" />
            </a>

            <div v-if="permissionModule.create_invoice">
              <sw-dropdown-item
                tag-name="router-link"
                to="/admin/invoices/create"
              >
                <document-text-icon class="h-5 mr-2 text-gray-600" />
                {{ $t('invoices.new_invoice') }}
              </sw-dropdown-item>
            </div>

            <div v-if="permissionModule.create_estimate">
              <sw-dropdown-item
                tag-name="router-link"
                to="/admin/estimates/create"
              >
                <document-icon class="h-5 mr-2 text-gray-600" />
                {{ $t('estimates.new_estimate') }}
              </sw-dropdown-item>
            </div>

            <div v-if="permissionModule.create_customer">
              <sw-dropdown-item
                tag-name="router-link"
                to="/admin/customers/create"
              >
                <user-icon class="h-5 mr-2 text-gray-600" />
                {{ $t('customers.new_customer') }}
              </sw-dropdown-item>
            </div>
          </sw-dropdown>
        </div>
      </li>

      <li class="relative block float-left ml-2">
        <sw-dropdown>
          <a
            slot="activator"
            href="#"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            class="inline-block text-sm text-black bg-white rounded-sm avatar"
          >
            <img
              :src="profilePicture"
              alt="Avatar"
              class="w-8 h-8 rounded-sm md:h-9 md:w-9"
            />
          </a>

          <sw-dropdown-item
            tag-name="router-link"
            to="/admin/settings"
            v-if="!isCustomer"
          >
            <cog-icon class="w-4 h-4 mr-2 text-gray-600" />
            {{ $t('navigation.settings') }}
          </sw-dropdown-item>

          <sw-dropdown-item @click="logout">
            <logout-icon class="w-4 h-4 mr-2 text-gray-600" />
            {{ $t('navigation.logout') }}
          </sw-dropdown-item>
        </sw-dropdown>
      </li>
    </ul>
  </header>
</template>

<script type="text/babel">
import { mapGetters, mapActions } from 'vuex'
import {
  PlusIcon,
  DocumentTextIcon,
  DocumentIcon,
  UserIcon,
  CogIcon,
  CreditCardIcon,
} from '@vue-hero-icons/solid'

import { LogoutIcon } from '@vue-hero-icons/outline'

export default {
  components: {
    PlusIcon,
    DocumentTextIcon,
    DocumentIcon,
    UserIcon,
    CogIcon,
    LogoutIcon,
    CreditCardIcon,
  },
  data() {
    return {
      allow_invoice_form_pos: false,
      previewLogo: null,
      permissionModule: {
        create_invoice: false,
        create_estimate: false,
        create_customer: false,
      },
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('company', {
      headerColor: 'getHeaderColor',
    }),

    isSuperAdmin() {
      return this.currentUser && this.currentUser.role == 'super admin'
        ? true
        : false
    },

    isCustomer() {
      return this.currentUser && this.currentUser.role == 'customer'
        ? true
        : false
    },

    ...mapGetters(['isSidebarOpen']),
    profilePicture() {
      if (
        this.currentUser &&
        this.currentUser.avatar !== null &&
        this.currentUser.avatar !== 0
      ) {
        return this.currentUser.avatar
      } else {
        return '/images/default-avatar.jpg'
      }
    },

    isOriginalHeaderColor() {
      return this.headerColor == '#5851D8' || !this.headerColor
    },

    getColor() {
      return {
        '--header-bg-color': this.isOriginalHeaderColor
          ? null
          : this.headerColor,
      }
    },
  },
  created() {
    //this.fetchCurrentUser()
  },
  mounted() {
    this.setInitialData()
    this.permissionsUserModule()
  },
  methods: {
    ...mapActions('user', ['fetchCurrentUser']),
    ...mapActions('auth', ['logout']),
    ...mapActions('modal', ['openModal']),
    ...mapActions(['toggleSidebar']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('company', ['fetchCompanySettings']),
    ...mapActions('modules', ['getModules']),

    async setInitialData() {
      this.isRequestOnGoing = true
      let response = await this.fetchCurrentUser()

      if (response.data.user) {
        this.previewLogo = response.data.user.company.logo
      }
      this.isRequestOnGoing = false
    },
    async permissionsUserModule() {
      // let res = await this.fetchCompanySettings([
      //   'allow_invoice_form_pos',
      // ])
      // this.allow_invoice_form_pos = res.data.allow_invoice_form_pos == '0' ? false : true

      const modules = ['corePOS']
      const modulesArray = await this.getModules(modules)

      var moduleCorePos = null

      if (typeof modulesArray.modules != 'undefined') {
        moduleCorePos = modulesArray.modules.find(
          (element) => element.name === 'corePOS'
        )
      }

      if (moduleCorePos && moduleCorePos.status == 'A') {
        let res = await this.fetchCompanySettings(['allow_invoice_form_pos'])
        this.allow_invoice_form_pos =
          res.data.allow_invoice_form_pos == '0' ? false : true
      } else {
        this.allow_invoice_form_pos = false
      }

      //Customer Permissions

      const data_customer = {
        module: 'customers',
      }

      const permissions_customer = await this.getUserModules(data_customer)

      if (permissions_customer.super_admin == true) {
        this.permissionModule.create_customer = true
      } else if (
        permissions_customer.exist == true &&
        permissions_customer.permissions[0] != null
      ) {
        const modulePermissions = permissions_customer.permissions[0]
        if (modulePermissions.create == 1) {
          this.permissionModule.create_customer = true
        }
      }

      //Invoice Permissions

      const data_invoice = {
        module: 'invoices',
      }

      const permissions_invoice = await this.getUserModules(data_invoice)

      if (permissions_invoice.super_admin == true) {
        this.permissionModule.create_invoice = true
      } else if (
        permissions_invoice.exist == true &&
        permissions_invoice.permissions[0] != null
      ) {
        const modulePermissions = permissions_invoice.permissions[0]
        if (modulePermissions.create == 1) {
          this.permissionModule.create_invoice = true
        }
      }

      //Estimate Permissions

      const data_estimate = {
        module: 'estimates',
      }

      const permissions_estimate = await this.getUserModules(data_estimate)

      if (permissions_estimate.super_admin == true) {
        this.permissionModule.create_estimate = true
      } else if (
        permissions_estimate.exist == true &&
        permissions_estimate.permissions[0] != null
      ) {
        const modulePermissions = permissions_estimate.permissions[0]
        if (modulePermissions.create == 1) {
          this.permissionModule.create_estimate = true
        }
      }

      //
    },
  },
}
</script>
<style lang="scss">
.hamburger {
  transition-property: opacity, filter;
  transition-duration: 0.15s;
}

.hamburger-inner {
  top: 50%;
  left: 4.5px;
  right: 4.5px;
}

.hamburger-inner,
.hamburger-inner::before,
.hamburger-inner::after {
  height: 2px;
  background-color: black;
  border-radius: 2px;
  position: absolute;
  transition-property: transform;
  transition-duration: 0.15s;
  transition-timing-function: ease;
}

.hamburger-inner::before,
.hamburger-inner::after {
  content: '';
  display: block;
  width: 100%;
}

.hamburger-inner::before {
  top: -5px;
}

.hamburger-inner::after {
  bottom: -5px;
}

.hamburger--arrowturn.is-active .hamburger-inner {
  transform: rotate(-180deg);
}

.hamburger--arrowturn.is-active .hamburger-inner::before {
  transform: translate3d(5px, 3px, 0) rotate(45deg) scale(0.5, 1);
}

.hamburger--arrowturn.is-active .hamburger-inner::after {
  transform: translate3d(5px, -3px, 0) rotate(-45deg) scale(0.5, 1);
}

.header-bg-color {
  background-color: var(--header-bg-color);
}
</style>
