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
  },
  created() {
    this.permissionsUserModule()
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
  },

  methods:{
    ...mapActions('user',['getUserModules']),

    async permissionsUserModule(){
      const data = {
        module: "pbx_report" 
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if(permissions.super_admin == false){
        if(permissions.exist == false ){
          this.$router.push('/admin/dashboard')
        }else {
        const modulePermissions = permissions.permissions[0]
        if(modulePermissions == null){
            this.$router.push('/admin/dashboard')
          } else if(modulePermissions.access == 0){
            this.$router.push('/admin/dashboard')
          }
        }
      }
    },
  },
}
</script>