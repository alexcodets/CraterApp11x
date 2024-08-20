<template>
  <base-page>
    <transition name="fade" mode="out-in">
      <router-view />
    </transition>
  </base-page>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  computed: {
    ...mapGetters('user', ['activeModules']),
  },
  mounted() {
    if(!this.activeModules.bandwidth){
      this.$router.push('/404');
    }
  },
}

</script>