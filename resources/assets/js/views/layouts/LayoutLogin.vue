<template>
  <div class="grid h-full grid-cols-12 overflow-y-hidden bg-gray-100">
    <div
      class="flex items-center justify-center w-full max-w-sm col-span-12 p-4 mx-auto text-gray-900 md:p-8 md:col-span-6 lg:col-span-4 flex-2 md:pb-48 md:pt-40"
    >
      <div class="w-full">
        <div
          :class="[
            loadingInfo
              ? 'relative bg-indigo-100 rounded-xl px-20 py-10 mb-32  flex justify-center items-center'
              : '',
          ]"
        >
          <base-loader class="absolute m-auto" v-if="loadingInfo" />
          <a v-if="!loadingInfo" href="/admin">
            <img
              v-if="previewLogo"
              :src="previewLogo"
              class="block w-48 h-auto max-w-full mb-32 text-primary-400"
            />
            <img
              v-else
              src="/assets/img/logo-corebill.png"
              class="block w-48 h-auto max-w-full mb-32 text-primary-400"
              alt="Crater Logo"
            />
          </a>
        </div>
        <router-view></router-view>
        <div
          class="pt-24 mt-0 text-sm not-italic font-medium leading-relaxed text-left text-gray-500 md:pt-40"
        >
          <div v-if="footer_text_value">
            <p class="mb-3 ml-12">
              {{ footer_text_value }}
              <a target="_blank" :href="footer_url_value">
                {{ footer_url_name }}
              </a>
              <span v-if="current_year == 1"> © {{ footer_year }} </span>
            </p>
          </div>
          <p class="mb-3 ml-12" v-else>
            {{ $t('layout_login.copyright_crater') }}
          </p>
        </div>
      </div>
    </div>

    <div
      :style="wallpaperLogin"
      class="relative flex-col items-center justify-center hidden w-full h-full pl-10 bg-no-repeat bg-cover md:col-span-6 lg:col-span-8 md:flex"
    >
      <div
        :class="[
          loadingInfo
            ? 'relative bg-indigo-100 rounded-xl px-20 py-10  flex justify-center items-center'
            : 'pl-20 xl:pl-0 w-1/2',
        ]"
      >
        <!-- loading -->
        <base-loader class="absolute m-auto" v-if="loadingInfo" />
        <h1
          v-if="title_header == null && !loadingInfo"
          class="hidden mb-3 text-3xl font-bold leading-normal text-white xl:text-5xl xl:leading-tight md:none lg:block"
        >
          {{ $t('layout_login.super_simple_invoicing') }} <br />
          {{ $t('layout_login.for_freelancer') }} <br />
          {{ $t('layout_login.small_businesses') }} <br />
        </h1>
        <h1
          v-else
          class="hidden mb-3 text-3xl font-bold leading-normal text-white xl:text-5xl xl:leading-tight md:none lg:block"
        >
          {{ title_header }}
        </h1>

        <div
          class="hidden text-sm not-italic font-normal leading-normal text-gray-100 xl:text-base xl:leading-6 md:none lg:block"
        >
          <p v-if="subtitle_header == null && !loadingInfo">
            {{ $t('layout_login.crater_help') }}<br />
            {{ $t('layout_login.invoices_and_estimates') }}<br />
          </p>
          <p v-else>
            {{ subtitle_header }}
          </p>
        </div>
      </div>

      <div class="absolute z-50 w-full bg-no-repeat content-bottom" />
    </div>
  </div>
</template>

<script type="text/babel">
import { mapGetters, mapActions } from 'vuex'

export default {
  data() {
    return {
      previewLogo: null,
      title_header: null,
      subtitle_header: null,
      loadingInfo: false,
      wallpaperLogin: null,
      footer_text_value: '',
      footer_url_value: '',
      footer_url_name: '',
      current_year: '',
      footer_year: new Date().getFullYear(),
    }
  },
  // computed: {
  //   wallpaperLoginComputed() {

  //     this.wallpaperLogin = this.wallpaperLogin ? this.wallpaperLogin : 'images/login-vector1.png'

  //     return {
  //       'background-image': `url(${this.wallpaperLogin})`
  //     }
  //   },
  // },
  created() {
    this.setInitialData()
  },
  methods: {
    ...mapActions('company', ['fetchCompanySettingsLogin']),
    ...mapActions('user', ['fetchCompanyLogo']),

    async setInitialData() {
      try {
        this.loadingInfo = true
        const response = await this.fetchCompanyLogo()
        if (response.data.success) {
          const {
            logo,
            title_header,
            subtitle_header,
            wallpaper_login,
            wallpaper_login_exists,
          } = response.data
          this.title_header = title_header
          this.subtitle_header = subtitle_header
          this.previewLogo = logo
          if (wallpaper_login_exists) {
            this.wallpaperLogin = `background-image: url(${wallpaper_login})`
          } else {
            this.wallpaperLogin = `background-image: url('/images/login-vector1.svg')`
          }
        } else {
          this.wallpaperLogin = `background-image: url('/images/login-vector1.svg')`
        }

        let res = await this.fetchCompanySettingsLogin([
          'footer_text_value',
          'footer_url_value',
          'footer_url_name',
          'current_year',
          'hide_title',
        ])
        if (res.data.footer_text_value) {
          this.footer_text_value = res.data.footer_text_value
        }
        if (res.data.footer_url_value) {
          this.footer_url_value = res.data.footer_url_value
        }
        if (res.data.footer_url_value) {
          this.footer_url_name = res.data.footer_url_name
        }
        if (res.data.footer_url_value) {
          this.current_year = res.data.current_year
        }

        if (res.data.hide_title) {
         
          if (res.data.hide_title == 1 || res.data.hide_title == true) {
            this.title_header = ''
            this.subtitle_header = ''
          }
        }
      } catch (error) {
        this.wallpaperLogin = `background-image: url('/images/login-vector1.svg')`
      } finally {
        this.loadingInfo = false
      }
    },
  },
}
</script>

<style lang="scss" scoped>
.content-box {
  //background-image: url('/images/login-vector1.svg');
}

.content-bottom {
  // background-image: url('/images/login-vector3.svg');
  background-size: 100% 100%;
  height: 300px;
  right: 32%;
  bottom: 0;
}

.content-box::before {
  //background-image: url('/images/frame.svg');
  content: '';
  background-size: 100% 100%;
  background-repeat: no-repeat;
  height: 300px;
  right: 0;
  position: absolute;
  top: 0;
  width: 420px;
  z-index: 1;
}

.content-box::after {
  //background-image: url('/images/login-vector2.svg');
  content: '';
  background-size: cover;
  background-repeat: no-repeat;
  height: 100%;
  width: 100%;
  right: 7.5%;
  position: absolute;
}
</style>
