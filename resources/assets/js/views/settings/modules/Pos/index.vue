<template>
  <base-page v-if="isSuperAdmin">
  <div :class="{ 'xl:pl-64': showSideBar}">
     <div class="w-full flex justify-end">
       <div class="mb-3 hidden xl:block">  
         <sw-button
            variant="primary-outline"
            @click="toggleSideBar"
            
          >
            {{ $t('tickets.departaments.menu') }}
            <component :is="listIcon" class="w-4 h-4 ml-2 -mr-1" />
          </sw-button>
       </div>
      
    </div>

    <slide-x-left-transition>
      <SidebartModules v-show="showSideBar" />
    </slide-x-left-transition>

    <transition name="fade" mode="out-in">
      <router-view />
    </transition>      
  </div>
  </base-page>
</template>

<script>
import SidebartModules from './SidebartModules'
import {
  PlusIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  ClipboardListIcon,
  PencilIcon,
  TrashIcon,
} from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'
export default {
  components: {
    SidebartModules,
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    EyeIcon,
    ClipboardListIcon,
    PencilIcon,
    TrashIcon,
  },
  data: () =>({
    showSideBar: true,
  }),
  computed: {
   ...mapGetters('user', ['currentUser']),
 
    listIcon() {
      return this.showSideBar ? 'x-icon' : 'clipboard-list-icon'
    },
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
  },
  methods:{
    toggleSideBar() {
      this.showSideBar = !this.showSideBar
    },
  },
  created() {
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
  },
}
</script>