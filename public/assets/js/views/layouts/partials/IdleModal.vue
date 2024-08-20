<template>
  <div
    class="session_expired_overlay"
  >
    <div class="session_expired_modal">
      <div class="session_expired_modal__title">
        <span>Session Expired</span>
      </div>
      <div class="p-3">
        <p>You have left this browser idle for {{ idleTime }} minutes.</p>
        <p>{{ second }} seconds left</p>
      </div>
    </div>
  </div>
</template>

<script>
import {mapActions} from "vuex";

export default {
  props: {
    idleTime: {
      type: Number,
      default: 5
    }
  },
  data() {
    return {
      time: 10000
    }
  },
  computed: {
    second() {
      return this.time / 1000;
    }
  },
  created() {
    let timerId = setInterval(() => {
      this.time -= 1000;

      if (!this.$store.state.isIdle)
        clearInterval(timerId);

      if (this.time < 1) {
        clearInterval(timerId);
        this.logout()
      }
    }, 1000);
  },
  methods: {
    ...mapActions('auth', ['logout']),
  }
}
</script>

<style lang="scss">
.session_expired_overlay {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background-color: rgba(0, 0, 0, 0.3);
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: center;
}
.session_expired_modal {
  width: 500px;
  box-shadow: 1px 2px 4px rgba(153, 155, 168, 0.12);
  border-radius: 4px;
  @apply bg-white p-2;
}
.session_expired_modal__title {
  color: #38404f;
  @apply flex items-center justify-between p-3 font-bold;
}
</style>