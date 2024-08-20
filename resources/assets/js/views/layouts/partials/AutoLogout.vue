<template>
  <idle-modal
    v-if="warningZone"
    :idle-time="idleTime"
  />
</template>

<script>
import IdleModal from './IdleModal'
export default {
  components: {
    IdleModal
  },
  props: {
    idleTime: {
      type: Number,
      default: 5
    }
  },
  data() {
    return {
      events: ['click', 'mousemove', 'mousedown', 'scroll', 'keypress', 'load', 'blur'],
      warningTimer: null,
      //logoutTimer: null,
      warningZone: false,
    }
  },
  mounted() {
    this.events.forEach(function (event) {
      window.addEventListener(event, this.resetTimer)
    }, this)

    this.setTimer()
  },
  destroyed() {
    this.events.forEach(function (event) {
      window.removeEventListener(event, this.resetTimer)
    }, this)

    this.resetTimer()
  },
  methods: {
    setTimer: function () {
      this.warningTimer = setTimeout(this.warningMessage,  this.idleTime * 60000)
      //this.logoutTimer = setTimeout(this.logoutUser, 10 * 1000)
      this.$store.state.isIdle = false
      this.warningZone = false
    },

    warningMessage: function () {
      this.warningZone = true
      this.$store.state.isIdle = true
    },

    logoutUser: function () {
      alert('Logout');
    },

    resetTimer: function () {
      clearTimeout(this.warningTimer)
      //clearTimeout(this.logoutTimer)
      if (document.hasFocus()) {
        this.setTimer()
      }
    }
  }
}
</script>