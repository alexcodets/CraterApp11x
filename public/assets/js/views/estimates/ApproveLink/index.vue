<template>
  <div class="grid h-full grid-cols-12 overflow-y-hidden bg-gray-100">
    <div class="relative flex-col items-center justify-center hidden w-full h-full pl-10 bg-no-repeat bg-cover md:col-span-4 lg:col-span-4 md:flex content-box"/>

    <div class="flex items-center  justify-center w-full  mx-auto text-gray-900 col-span-12  md:col-span-8 px-2 md:px-5 text-center">
      <div class="w-full">
       <base-loader v-if="loading" :show-bg-overlay="false" />
        <a class="w-full flex flex-wrap justify-center mb-7" href="/admin">
          <img
            v-if="previewLogo"
            :src="previewLogo"
            class="block w-48 h-auto max-w-full text-primary-400"
          />
        <!--    <img
            v-else
            src="/assets/img/logo-corebill.png"
            class="block w-48 h-auto max-w-full text-primary-400"
            alt="Crater Logo"
          />-->
        </a>
        <h1 class="text-4xl font-bold text-primary  my-5">
          {{message}}
        </h1>
        <!-- botton para iniciar session -->
        <sw-button
          v-if="login && success"
          variant="primary"
          @click="$router.push({ name: 'login-customer' })"
        >
          {{ $t('login.log_in') }}
        </sw-button>
      </div>
    </div>
  </div>
</template>

<script type="text/babel">
import { mapActions } from 'vuex'
import BaseLoader from '../../../components/base/BaseLoader.vue'

export default {
  components: { BaseLoader },
  data() {
    return {
      previewLogo: null,
      loading: false,
      message: '',
      success: false,
      login: false,
    }
  },
  created() {
    this.setInitialData()
  },
  methods: {
    ...mapActions('user', ['fetchCompanyLogo']),
    ...mapActions('estimate', ['approval']),
    async setInitialData() {
      let response = await this.fetchCompanyLogo()
      if (response.data.user) {
        this.previewLogo = response.data.user.company.logo
      }
    },
    async approvalEstimate() {
     try {
      this.loading = true
      const res = await this.approval(this.$route.params.unique_hash)
      this.success = res.data.success
      this.message = res.data.message
      this.login = res.data.login
     } catch (error) {
        window.toastr['error'](error.message)
     }finally{
       this.loading = false
     }
    },
  },
  created() {
    this.approvalEstimate()
  },
}
</script>

<style lang="scss" scoped>
.content-box {
  background-image: url('/images/login-vector1.svg');
  transform: scaleX(-1);
  right: 0;

}

.content-box::before {
  background-image: url('/images/frame.svg');
  content: '';
  background-size: 85% 85%;
  background-repeat: no-repeat;
  height: 200px;
  right: -100px;
  position: absolute;
  top: 0;
  width: 420px;
  z-index: 1;
}

.content-box::after {
  background-image: url('/images/login-vector2.svg');
  content: '';
  background-size: cover;
  background-repeat: no-repeat;
  height: 100%;
  width: 100%;
  right: 20%;
  position: absolute;
}
</style>
