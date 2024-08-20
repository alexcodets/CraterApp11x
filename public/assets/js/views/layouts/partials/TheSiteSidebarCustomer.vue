<template>
  <div>
    <!-- OVERLAY -->
    <sw-transition type="fade">
      <div
        v-show="isSidebarOpen"
        class="fixed top-0 left-0 z-20 w-full h-full"
        style="background: rgba(48, 75, 88, 0.5)"
        @click.prevent="toggleSidebar"
      ></div>
    </sw-transition>

    <!-- DESKTOP MENU -->
    <div
      class="
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
      "
    >
      <sw-list
        v-for="(menuItems, groupIndex) in menuItems"
        :key="groupIndex"
        variant="sidebar"
      >
        <div v-for="(item, index) in menuItems" :key="index">
          <sw-list-item
            v-if="item.show"
            :title="$t(item.title)"
            :key="index"
            :active="hasActiveUrl(item.route)"
            :to="item.route"
            tag-name="router-link"
          >
            <component slot="icon" :is="item.icon" class="h-5" />
          </sw-list-item>
        </div>
      </sw-list>

            <div class="md:block">
        <sw-button
          variant="primary"
          size="lg"
          :tabindex="10"
          class="flex justify-center w-full md:w-auto"
          style="
            margin-top: 2em;
            margin-left: 1em;
            background-color: #dbd7d2;
            color: black;
            padding:1px;
          "
        >
          {{ this.codecu }}
        </sw-button>
        

      <div class="md:block">
        <sw-button
          variant="primary"
          size="lg"
          :tabindex="10"
          class="flex justify-center w-full md:w-auto"
          style="
            margin-top: 1em;
            margin-left: 1em;
            background-color: #1e90ff;
            color: black;
          "
        >
          Balance: {{ this.balance }}
        </sw-button>
        </div>

        <div class="md:block">
          <sw-button
            variant="primary"
            size="lg"
            :tabindex="10"
            class="flex justify-center w-full md:w-auto"
            style="
              margin-top: 1em;
              margin-left: 1em;
              background-color: #9acd32;
              color: black;
            "
          >
            Credit: {{ this.credit }}
          </sw-button>
        </div>
      </div>
    </div>

    <!-- MOBILE MENU -->
    <transition
      enter-class="-translate-x-full"
      enter-active-class="transition duration-300 ease-in-out transform"
      enter-to-class="translate-x-0"
      leave-active-class="transition duration-300 ease-in-out transform"
      leave-class="translate-x-0"
      leave-to-class="-translate-x-full"
    >
      <div
        v-show="isSidebarOpen"
        class="
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
        "
      >
        <sw-list
          v-for="(menuItems, groupIndex) in menuItems"
          :key="groupIndex"
          variant="sidebar"
        >
          <sw-list-item
            v-for="(item, index) in menuItems"
            :title="$t(item.title)"
            :key="index"
            :active="hasActiveUrl(item.route)"
            :to="item.route"
            tag-name="router-link"
            @click.native="toggleSidebar"
          >
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
import { mapGetters, mapActions } from 'vuex'

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
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('customerProfile', ['loggedInCustomer']),
    ...mapGetters('company', ['defaultCurrency']),
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
            show: true,
          },
          {
            title: 'navigation.invoices',
            icon: 'document-text-icon',
            route: '/customer/invoices',
            slug: 'navigation.invoices.index',
            show: true,
          },
          {
            title: 'navigation.payments',
            icon: 'credit-card-icon',
            route: '/customer/payments',
            slug: 'navigation.payments.index',
            show: true,
          },
        ],
      ]

      return menu
    },
  },

  data() {
    return {
      permissions: [],
      credit: '0.0  $',
      balance: '0.0  $',
      codecu: 'USD',
      codesym: '$',
    }
  },
  created() {},

  mounted() {
    this.fetchUserPermission()
    this.getCredit()
  },

  methods: {
    ...mapActions(['toggleSidebar']),
    ...mapActions('user', ['getUserPermission']),
    ...mapActions('customerProfile', ['fetchLoggedInCustomer']),

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

    async getCredit() {
      this.credit = this.codesym + ' 0.0 ' + this.codecu
      this.balance = this.codesym + ' 0.0' + this.codecu

      if (!this.currentUser.balance == null) {
        return false
      }

      if (this.defaultCurrency != null) {
        this.codecu = this.defaultCurrency.code
        this.codesym = this.defaultCurrency.symbol
      }

      this.credit =
        this.codesym +
        ' ' +
        (Math.round(this.currentUser.balance * 100) / 100).toFixed(2) +
        ' ' +
        this.codecu
      let response = await this.fetchLoggedInCustomer()

      if (response) {
        if (response.statusText == 'OK') {
          console.log(response.statusText)
          if (response.data) {
            if (response.data.statsData) {
              console.log('p') 
              console.log(response.data.statsData) 
              let totalpop = (response.data.statsData.totalAmountDue  /100) + response.data.statsData.callRegisterTotalAmount
             console.log(totalpop);
             this.balance =
                this.codesym +
                ' ' +
                totalpop.toFixed(2)  +
                ' ' +
                this.codecu

                console.log(totalpop);
            }
          }
        }
      }
      console.log(this.defaultCurrency)

      return true
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
