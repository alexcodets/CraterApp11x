<template>
  <base-page v-if="isSuperAdmin">
  

     <transition name="fade" mode="out-in">
          <router-view />
      </transition>

  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import SidebartDepartaments from './SidebartTickets'
import AstronautIcon from '@/components/icon/AstronautIcon'
import {
  EyeIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  ClipboardListIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
} from '@vue-hero-icons/solid'
export default {
  components: {
    EyeIcon,
    AstronautIcon,
    FilterIcon,
    SidebartDepartaments,
    XIcon,
    ChevronDownIcon,
    ClipboardListIcon,
    PencilIcon,
    TrashIcon,
    PlusIcon,
  },
  data() {
    return {
      id: null,
    } 
  },
  computed: {
   ...mapGetters('user', ['currentUser']),
 
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
  
  created() {
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
  },
  
  }
}
</script>