<template>
  <div>
    <!-- OVERLAY -->
    <sw-transition type="fade">
      <div v-show="isSidebarOpen" class="fixed top-0 left-0 z-20 w-full h-full"
        style="background: rgba(48, 75, 88, 0.5)" @click.prevent="toggleSidebar"></div>
    </sw-transition>

    <!-- DESKTOP MENU -->
    <div class="
        hidden
        w-56
        h-screen
        pb-32
        overflow-y-auto
        bg-white
        border-r border-gray-200 border-solid
        xl:w-64
        sw-scroll
        md:block
      ">
      <sw-list v-for="(menuItems, groupIndex) in menuItems" :key="groupIndex" variant="sidebar">
        <div v-for="(item, index) in menuItems" :key="index">
          <sw-list-item v-if="item.show" :title="$t(item.title)" :key="index" :active="hasActiveUrl(item.route)"
            :to="item.route" tag-name="router-link">
            <component slot="icon" :is="item.icon" class="h-5" />
          </sw-list-item>
        </div>
      </sw-list>

      <div class="md:block">
        <sw-button variant="primary" size="lg" :tabindex="10" class="flex justify-center w-full md:w-auto" style="
            margin-top: 2em;
            margin-left: 1em;
            background-color: #dbd7d2;
            color: black;
            padding:1px;
          ">
          {{ this.codecu }}
        </sw-button>


        <div class="md:block">
          <sw-button variant="primary" size="lg" :tabindex="10" class="flex justify-center w-full md:w-auto" style="
            margin-top: 1em;
            margin-left: 1em;
            background-color: #1e90ff;
            color: black;
          ">
            Balance: {{ formattedBalance }}
          </sw-button>
        </div>

        <div class="md:block">
          <sw-button variant="primary" size="lg" :tabindex="10" class="flex justify-center w-full md:w-auto" style="
              margin-top: 1em;
              margin-left: 1em;
              background-color: #9acd32;
              color: black;
            ">
            Credit: {{ formattedCredit }}
          </sw-button>
        </div>
      </div>
    </div>

    <!-- MOBILE MENU -->
    <transition enter-class="-translate-x-full" enter-active-class="transition duration-300 ease-in-out transform"
      enter-to-class="translate-x-0" leave-active-class="transition duration-300 ease-in-out transform"
      leave-class="translate-x-0" leave-to-class="-translate-x-full">
      <div v-show="isSidebarOpen" class="
          fixed
          top-0
          z-30
          w-64
          h-screen
          pt-16
          pb-32
          overflow-y-auto
          bg-white
          border-r border-gray-200 border-solid
          sw-scroll
          md:hidden
        ">
        <sw-list v-for="(menuItems, groupIndex) in menuItems" :key="groupIndex" variant="sidebar">
          <sw-list-item v-for="(item, index) in menuItems" :title="$t(item.title)" :key="index"
            :active="hasActiveUrl(item.route)" :to="item.route" tag-name="router-link" @click.native="toggleSidebar">
            <component slot="icon" :is="item.icon" class="h-5" />
          </sw-list-item>
        </sw-list>
      </div>
    </transition>
  </div>
</template>

<script type="text/babel">
import {
  HomeIcon,
  UserIcon,
  StarIcon,
  DocumentIcon,
  DocumentTextIcon,
  CreditCardIcon,
  CalculatorIcon,
  ChartBarIcon,
  CogIcon,
  UsersIcon,
  CollectionIcon,
  DocumentDuplicateIcon,
  ViewGridIcon,
  UserGroupIcon,
} from '@vue-hero-icons/outline'
import { mapGetters, mapActions, mapState } from 'vuex'

export default {
  components: {
    HomeIcon,
    UserIcon,
    StarIcon,
    DocumentIcon,
    DocumentTextIcon,
    CreditCardIcon,
    CalculatorIcon,
    ChartBarIcon,
    CogIcon,
    UsersIcon,
    CollectionIcon,
    DocumentDuplicateIcon,
    ViewGridIcon,
    UserGroupIcon,
  },

  computed: {
    ...mapGetters(['isSidebarOpen']),
    ...mapGetters('user', ['currentUser', 'settingsCompany']),
    ...mapState('customerProfile', {
      loggedInCustomer: state => state.loggedInCustomer,
      balance: state => state.balance,
      credit: state => state.credit,
    }),
    ...mapState('company', {
      defaultCurrency: state => state.defaultCurrency,
    }),
    formattedBalance() {
      const balance = this.balance || 0;
      return `${this.codesym} ${(Math.round(balance * 100) / 100).toFixed(2)} ${this.codecu}`;
    },
    formattedCredit() {
      const credit = this.credit || 0;
      return `${this.codesym} ${(Math.round(credit * 100) / 100).toFixed(2)} ${this.codecu}`;
    },
    menuItems() {
      let menu = [
        [
          {
            title: 'navigation.dashboard',
            icon: 'home-icon',
            route: '/customer/dashboard',
            show: true,
          },
        ],
        [
          {
            title: 'navigation.estimates',
            icon: 'document-icon',
            route: '/customer/estimates',
            slug: 'navigation.estimates.index',
            show: this.showQuotes,
          },
          {
            title: 'navigation.invoices',
            icon: 'document-text-icon',
            route: '/customer/invoices',
            slug: 'navigation.invoices.index',
            show: this.showInvoices,
          },
          {
            title: 'navigation.payments',
            icon: 'credit-card-icon',
            route: '/customer/payments',
            slug: 'navigation.payments.index',
            show: this.showPayments,
          },
          {
            title: 'corePbx.menu_title.reports',
            icon: 'document-text-icon',
            route: '/customer/reports/departaments',
            slug: 'navigation.reports.index',
            show: this.showReports,
          },
          {
            title: 'navigation.services',
            icon: 'document-duplicate-icon',
            route: '/customer/services',
            show: this.showServices,
          },
        ],
      ]

      return menu
    },
  },

  data() {
    return {
      permissions: [],
 /*      credit: '0.0  $',
      baalnce: '0.0  $', */
      codecu: 'USD',
      codesym: '$',
      showPayments: false,
      showQuotes: false,
      showInvoices: false,
      showReports: false,
      showServices: false,
    }
  },
  watch: {
    settingsCompany: {
      immediate: true,
      handler(newVal) {
        if (newVal) {
          this.updateMenuVisibility();
        }
      },
    },
  },
  created() {
    this.fetchUserPermission()
  },

  mounted() {



  },

  methods: {
    ...mapActions(['toggleSidebar']),
    ...mapActions('user', ['getUserPermission']),
    ...mapActions('customerProfile', ['fetchLoggedInCustomer', 'updateBalance', 'updateCredit']),

    async fetchUserPermission() {
      let slugs = []

      this.menuItems.forEach((all) => {
        all.forEach((itemMenu) => {
          if (itemMenu.slug) {
            slugs.push(itemMenu.slug)
          }
        })
      })

      this.permissions = await this.getUserPermission({ slugs: slugs })
    },


    updateMenuVisibility() {
      if (!this.settingsCompany) return;

      this.showPayments = this.settingsCompany.enable_payment_customer === "1";
      this.showQuotes = this.settingsCompany.enable_quotes_customer === "1";
      this.showInvoices = this.settingsCompany.enable_invoice_customer === "1";
      this.showReports = this.settingsCompany.enable_report_customer === "1";
      this.showServices = this.settingsCompany.enable_service_customer === "1";
    },
    hasActiveUrl(url) {
      this.isActive = true
      return this.$route.path.indexOf(url) > -1
    },
    hasStaticUrl(url) {
      return this.$route.path.indexOf(url)
    },
  },
}
</script>
