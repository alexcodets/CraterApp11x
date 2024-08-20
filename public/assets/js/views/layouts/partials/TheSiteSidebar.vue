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
          tag-name="router-link">
          <component slot="icon" :is="item.icon" class="h-5" />
        </sw-list-item>
      </div>
      </sw-list>
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
    menuItems() {
      let menu = [
        [
          {
            title: 'navigation.dashboard',
            icon: 'home-icon',
            route: '/admin/dashboard',
            show: true
          },
          {
            title: 'navigation.customers',
            icon: 'user-icon',
            route: '/admin/customers',
            slug: 'navigation.customers.index',
            show: true
          },
          {
            title: 'navigation.providers',
            icon: 'user-group-icon',
            route: '/admin/providers',
            show: true
          },
        ],
        [
          {
            title: 'navigation.estimates',
            icon: 'document-icon',
            route: '/admin/estimates',
            show: true
          },
          {
            title: 'navigation.invoices',
            icon: 'document-text-icon',
            route: '/admin/invoices',
            slug: 'navigation.invoices.index',
            show: true
          },
          {
            title: 'navigation.payments',
            icon: 'credit-card-icon',
            route: '/admin/payments',
            show: true

          },
          {
            title: 'navigation.expenses',
            icon: 'calculator-icon',
            route: '/admin/expenses',
            show: true
          },
        ],
        [

           {
            title: 'navigation.items',
            icon: 'star-icon',
            route: '/admin/items',
            show: true
          },
       
          {
            title: 'navigation.packages',
            icon: 'document-duplicate-icon',
            route: '/admin/packages',
            show: true
          },
    
         
        ],
        [
          {
            title: 'navigation.reports',
            icon: 'chart-bar-icon',
            route: '/admin/reports',
            show: true
          },
          {
            title: 'navigation.settings',
            icon: 'cog-icon',
            route: '/admin/settings',
            show: true
          },
        ],
      ]

      if (this.currentUser.role == 'super admin') {
        menu[3] = [
          {
            title: 'navigation.pbx',
            icon: 'star-icon',
            route: '/admin/corePBX/packages',
            show: true
          },
          {
            title: 'navigation.tickets',
            icon: 'document-text-icon',
            /* route: '/admin/tickets/departaments' ,*/
            route: '/admin/tickets/main' ,
            show: true
          },
          {
            title: 'navigation.users',
            icon: 'users-icon',
            route: '/admin/users',
            show: true
          },
          ...menu[3],
        ]
      }

      /* let permissions = this.permissions

      let permissionArr = menu.map(Menu => {
        return Menu.map(item => {
         if(item.slug){
            permissions.forEach(permission => {
              if(permission.slug == item.slug){
                item.show = permission.show
              }
            })
           return item
         } else {
           return item
         }
        })
      })

      return permissionArr */

      return menu
    },
  },

  data(){
    return {
      permissions: []
    }
  },

  mounted(){
    this.fetchUserPermission()
  },

  methods: {
    ...mapActions(['toggleSidebar']),
    //...mapActions('user', ['getUserPermission']),
    
    async fetchUserPermission(){

      let slugs = []

      this.menuItems.forEach(all => {
       all.forEach(itemMenu => {
         if(itemMenu.slug){
           slugs.push(itemMenu.slug)
         }
       })
      })


      //this.permissions = await this.getUserPermission({slugs: slugs})
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

