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
    ...mapGetters('user', ['currentUser', 'activeModules']),
    menuItems() {
      let menu = [
        [
          {
            title: 'navigation.dashboard',
            icon: 'home-icon',
            route: '/admin/dashboard',
            show: this.accessModule.dashboardShow
          },
          {
            title: 'navigation.customers',
            icon: 'user-icon',
            route: '/admin/customers',
            slug: 'navigation.customers.index',
            show: this.accessModule.customers
          },
          {
            title: 'navigation.leads',
            icon: 'user-icon',
            route: '/admin/leads',
            show: this.accessModule.lead
          },
          {
            title: 'navigation.providers',
            icon: 'user-group-icon',
            route: '/admin/providers',
            show: this.accessModule.providers
          },
        ],
        [
          {
            title: 'navigation.estimates',
            icon: 'document-icon',
            route: '/admin/estimates',
            show: this.accessModule.estimates
          },
          {
            title: 'navigation.invoices',
            icon: 'document-text-icon',
            route: '/admin/invoices',
            slug: 'navigation.invoices.index',
            show: this.accessModule.invoices
          },
          {
            title: 'navigation.payments',
            icon: 'credit-card-icon',
            route: '/admin/payments',
            show: this.accessModule.payments

          },
          {
            title: 'navigation.expenses',
            icon: 'calculator-icon',
            route: '/admin/expenses',
            show: this.accessModule.expenses
          },
        ],
        [
           {
            title: 'navigation.items',
            icon: 'star-icon',
            route: '/admin/items',
            show: this.accessModule.items
          },
       
          {
            title: 'navigation.packages',
            icon: 'document-duplicate-icon',
            route: '/admin/packages',
            show: this.accessModule.packages
          },
          {
            title: 'navigation.services',
            icon: 'document-duplicate-icon',
            route: '/admin/services',
            show: this.accessModule.services
          },             
        ],
        [
          
          {
            title: 'navigation.pbx',
            icon: 'star-icon',
            route: '/admin/corePBX',
            show: this.accessModule.corepbx
          },
          {
            title: 'navigation.core_pos',
            icon: 'star-icon',
            route: '/admin/corePos/main',
            show: this.accessModule.corePOS_index
          },
          // {
          //   title: 'navigation.bandwidth',
          //   icon: 'collection-icon',
          //   route: '/admin/bandwidth/desconnect',
          //   show: this.activeModules.bandwidth,
          // },
          {
            title: 'navigation.tickets',
            icon: 'document-text-icon',
            /* route: '/admin/tickets/departaments' ,*/
            route: '/admin/tickets/main' ,
            show: this.accessModule.tickets
          },
          {
            title: 'navigation.users',
            icon: 'users-icon',
            route: '/admin/users',
            show: this.accessModule.users
          },
          
        ],
        [
          {
            title: 'navigation.reports',
            icon: 'chart-bar-icon',
            route: '/admin/reports',
            show: this.accessModule.reports
          },
          {
            title: 'navigation.settings',
            icon: 'cog-icon',
            route: '/admin/settings',
            show: this.accessModule.settings
          },
        ],
      ]
      return menu
    },
    
  },

  data(){
    return {
      modules: null,
      permissions: [],
      showModuleBanckwidth: false,
      accessModule: {
        dashboardShow: true,
        customers: false,
        providers: false,
        estimates: false,
        invoices: false,
        payments: false,
        expenses: false,
        items: false,
        packages: false,
        services: false,
        reports: false,
        settings: false,
        corepbx: false,
        corePOS_index: false,
        tickets: false,
        users: false,
        
        lead: false
      }
    }
  },
  // created(){
  //   this.fetchUserPermission()
  // },
  mounted(){
    this.fetchUserPermission()
  },
  methods: {
    ...mapActions(['toggleSidebar']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('modules', ['getModules']),

    //Obtener info de permisos modulos del usuario 
    async fetchUserPermission(){

      const modules = [
        "PBXware",
        "Avalara",
        "BillPay",
        "Bandwidth",
        "corePOS",
      ]
      const modulesArray = await this.getModules(modules)
      const moduleCorePos = modulesArray.modules.find(element => element.name === 'corePOS')
      const modulePbxware = modulesArray.modules.find(element => element.name === 'PBXware')

      const data = {
        module: "COMPLETE_ALL"
      }
      
      const permissions = await this.getUserModules(data)
      if(permissions.super_admin == true){
  
          this.accessModule.dashboard = true
          this.accessModule.customers = true
          this.accessModule.providers = true
          this.accessModule.estimates = true
          this.accessModule.invoices = true
          this.accessModule.payments = true
          this.accessModule.expenses = true
          this.accessModule.items = true
          this.accessModule.packages = true
          this.accessModule.services = true
          this.accessModule.reports = true
          this.accessModule.settings = true
          this.accessModule.tickets = true
          this.accessModule.users = true
          this.accessModule.lead = true
          
          if(moduleCorePos && moduleCorePos.status == 'A'){
            this.accessModule.corePOS_index = true
          }else {
            this.accessModule.corePOS_index = false
          }

          if(modulePbxware && modulePbxware.status == 'A'){
            this.accessModule.corepbx = true
          }else {
            this.accessModule.corepbx = false
          }

      
      }else  {
       
        const data = {
         customers : false,
         providers : false,
         estimates : false,
         invoices : false,
         payments : false,
         expenses : false,
         items : false,
         packages : false,
         services : false,
         reports : false,
         settings : false,
         corepbx : false,
         corePOS_index : false,
         tickets : false,
         users : false,
         lead:false
        }
        
        const arrayPermissions = permissions.permissions
       // console.log(arrayPermissions)
        arrayPermissions.forEach((element) => {
          if(element.access == 1){
            data[element.module] = true
          }
        })
        
        this.accessModule.customers = data.customers
        this.accessModule.providers = data.providers
        this.accessModule.estimates = data.estimates
        this.accessModule.invoices = data.invoices
        this.accessModule.payments = data.payments
        this.accessModule.expenses = data.expenses
        this.accessModule.items = data.items
        this.accessModule.packages = data.packages
        this.accessModule.services = data.services
        this.accessModule.reports = data.reports
        this.accessModule.settings = data.settings
        this.accessModule.tickets = data.tickets
        this.accessModule.users = data.users
        this.accessModule.lead = data.lead
        
        if(moduleCorePos && moduleCorePos.status == 'A'){
          this.accessModule.corePOS_index = data.corePOS_index
        }

        if(modulePbxware && modulePbxware.status == 'A'){
          this.accessModule.corepbx = data.corepbx
        }
      }
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

