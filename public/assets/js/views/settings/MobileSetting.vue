<template>
  <form class="relative h-full" @submit.prevent="saveData">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card variant="setting-card">
      <template slot="header">
        <h6 class="sw-section-title">
          {{ $t('settings.mobile.mobile') }}
        </h6>
        
      </template>

      <div class="grid gap-6 grid-col-1 md:grid-cols-2 mt-4">

          <sw-input-group :label="$t('settings.mobile.logo')">
            <!-- img logo -->
            <img class="mt-2 logo" v-if="logoexist == true" :src="file_url" alt=""> 

            <div id="docs-box" class="mt-9">
              <input class="form-control inputFile" id="uploadLogo" type="file" @change="onChangeLogo" accept="image/png">
              <div class="flex flex-col items-center" >
                <cloud-upload-icon
                  class="h-5 mb-2 text-xl leading-6 text-gray-400"
                />
                <label for="uploadLogo">
                  <p class="text-xs leading-4 text-center text-gray-400">
                    <span id="pick-avatar" class="cursor-pointer text-primary-500" >browse</span>
                    to choose a file
                  </p>
                </label>
              </div>   
            </div>

            <!-- list -->
            <!-- <ul class="list-group mt-3">
              <li class="list-group-item" v-for="file in fileObject">
                <span v-on:click="deleteFile(file.name)" class="pointer">
                  <trash-icon class="h-5 mr-3 text-gray-600" style="position: absolute"/>
                </span>
                <span v-bind:class="{ marginFilename: true, downloadFilename: true }" v-on:click="downloadFile(file.name)"> {{ file.name }} </span>
              </li>
            </ul> -->
          </sw-input-group>

          <sw-input-group :label="$t('settings.mobile.color_palette')">
            <vue-tailwind-color-picker v-model="formData.color_palette" v-if="!isRequestOnGoing" :swatches.sync="swatches" :hide-swatches="false" @change="changedColor" @addSwatch="swatchAdded" @deleteSwatch="swatchDeleted" class="mt-3"/>
          </sw-input-group>
          
        </div>

      <div class="grid gap-6 sm:grid-col-1 md:grid-cols-3 mt-8">
        <!-- :error="nameError" -->
        <sw-input-group
          :label="$tc('settings.mobile.menu_vertical')"
        >
          <sw-checkbox
            v-model="formData.vertical_menu.services"
            :label="$t('settings.mobile.services')"
            variant="primary"
            size="sm"            
          />

          <sw-checkbox
            v-model="formData.vertical_menu.pbx_services"
            :label="$t('settings.mobile.pbx_services')"
            variant="primary"
            size="sm"            
          />

          <sw-checkbox
            v-model="formData.vertical_menu.invoices"
            :label="$t('settings.mobile.invoices')"
            variant="primary"
            size="sm"            
          />

          <sw-checkbox
            v-model="formData.vertical_menu.estimates"
            :label="$t('settings.mobile.estimates')"
            variant="primary"
            size="sm"            
          />

          <sw-checkbox
            v-model="formData.vertical_menu.payments"
            :label="$t('settings.mobile.payments')"
            variant="primary"
            size="sm"
            
          />

          <sw-checkbox
            v-model="formData.vertical_menu.payment_accounts"
            :label="$t('settings.mobile.payment_accounts')"
            variant="primary"
            size="sm"
            
          />

          <sw-checkbox
            v-model="formData.vertical_menu.tickets"
            :label="$t('settings.mobile.tickets')"
            variant="primary"
            size="sm"
          />
        </sw-input-group>

        <!-- :error="emailError" -->
        
        <!-- menu horizontal -->
        <sw-input-group
          :label="$tc('settings.mobile.menu_horizontal')"
        >
          <sw-checkbox
            v-model="formData.horizontal_menu.services"
            :label="$t('settings.mobile.services')"
            variant="primary"
            size="sm"
          />

          <sw-checkbox
            v-model="formData.horizontal_menu.pbx_services"
            :label="$t('settings.mobile.pbx_services')"
            variant="primary"
            size="sm"
            
          />

          <sw-checkbox
            v-model="formData.horizontal_menu.invoices"
            :label="$t('settings.mobile.invoices')"
            variant="primary"
            size="sm"
            
          />

          <sw-checkbox
            v-model="formData.horizontal_menu.estimates"
            :label="$t('settings.mobile.estimates')"
            variant="primary"
            size="sm"
          />

          <sw-checkbox
            v-model="formData.horizontal_menu.payments"
            :label="$t('settings.mobile.payments')"
            variant="primary"
            size="sm"
          />

          <sw-checkbox
            v-model="formData.horizontal_menu.payment_accounts"
            :label="$t('settings.mobile.payment_accounts')"
            variant="primary"
            size="sm"
          />

          <sw-checkbox
            v-model="formData.horizontal_menu.tickets"
            :label="$t('settings.mobile.tickets')"
            variant="primary"
            size="sm"
          />

        </sw-input-group>

        <!-- :error="passwordError" -->
        
        <!-- dashboard -->
        <sw-input-group
          :label="$tc('settings.mobile.dashboard')"
        >
          <sw-checkbox
            v-model="formData.dashboard.services"
            :label="$t('settings.mobile.services')"
            variant="primary"
            size="sm"
          />

          <sw-checkbox
            v-model="formData.dashboard.pbx_services"
            :label="$t('settings.mobile.pbx_services')"
            variant="primary"
            size="sm"
          />

          <sw-checkbox
            v-model="formData.dashboard.invoices"
            :label="$t('settings.mobile.invoices')"
            variant="primary"
            size="sm"
          />

          <sw-checkbox
            v-model="formData.dashboard.estimates"
            :label="$t('settings.mobile.estimates')"
            variant="primary"
            size="sm"
          />

          <sw-checkbox
            v-model="formData.dashboard.payments"
            :label="$t('settings.mobile.payments')"
            variant="primary"
            size="sm"
          />

          <sw-checkbox
            v-model="formData.dashboard.tickets"
            :label="$t('settings.mobile.tickets')"
            variant="primary"
            size="sm"
          />
        </sw-input-group>
      </div>

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
        class="mt-10"
        variant="primary"
      >
        <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
        {{ $tc('settings.account_settings.save') }}
      </sw-button>
    </sw-card>
  </form>
</template>

<script>
import { mapActions, mapGetters, mapState } from 'vuex'
import { CloudUploadIcon } from '@vue-hero-icons/solid'
import BaseLoader from '../../components/base/BaseLoader.vue'
import VueTailwindColorPicker from '../../../../../node_modules/vue-tailwind-color-picker/src/vue-tailwind-color-picker.vue'

export default {
  components: {
    CloudUploadIcon,
    BaseLoader,
    VueTailwindColorPicker
  },

  data() {
    return {
      color: '#00FF00FF',
      file_url: '',
      logoexist:false,
      swatches: [
        '#f94144',
        '#f3722c',
        '#f8961e',
        '#f9c74f',
        '#90be6d',
        '#43aa8b',
        '#577590',
        '#22223b',
        '#4a4e69',
        '#c9ada7'
      ],
      formData: {
        logo: null ,
        color_palette: null,
        vertical_menu: {
          services: false,
          pbx_services: false,
          invoices: false,
          estimates: false,
          payments: false,
          payment_accounts: false,
          tickets: false
        },
        horizontal_menu: {
          services: false,
          pbx_services: false,
          invoices: false,
          estimates: false,
          payments: false,
          payment_accounts: false,
          tickets: false
        },
        dashboard: {
          services: false,
          pbx_services: false,
          invoices: false,
          estimates: false,
          payments: false,
          tickets: false
        },
      },
      isLoading: false,
      language: null,
      isRequestOnGoing: false,
      fileObject: null
    }
  },

  validations: {
  },

  computed: {
    /* isEdit() {
      if (this.$route.name === 'customers.edit') {
        return true
      }
      return false
    }, */
    // ...mapGetters('mobileSettings',['fetchMobileSetting']),

    /* emailError() {
      if (!this.$v.formData.email.$error) {
        return ''
      }
      if (!this.$v.formData.email.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },

    passwordError() {
      if (!this.$v.formData.password.$error) {
        return ''
      }
      if (!this.$v.formData.password.minLength) {
        return this.$tc(
          'validation.password_min_length',
          this.$v.formData.password.$params.minLength.min,
          { count: this.$v.formData.password.$params.minLength.min }
        )
      }
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },

    confirmPasswordError() {
      if (!this.$v.formData.confirm_password.$error) {
        return ''
      }

      if (!this.$v.formData.confirm_password.sameAsPassword) {
        return this.$tc('validation.password_incorrect')
      }
    },

    languageError() {
      if (!this.$v.language.$error) {
        return ''
      }
      if (!this.$v.language.required) {
        return this.$tc('validation.required')
      }
    }, */
  },

  watch: {
    'formData.password'(val) {
      if (!val) {
        this.formData.confirm_password = ''
      }
    },
  },

  mounted() {
    this.setInitialData()
    // this.fetchLanguages()
  },

  methods: {
    ...mapActions('mobileSettings', [
      'saveMobileSettings',
      'fetchMobileSetting'
    ]),

    ...mapActions(['fetchLanguages']),

    changedColor(color) {
      console.warn('Changed Color', color)
    },
    swatchAdded(color) {
      console.log('Swatch Added', color)
    },
    swatchDeleted(color) {
      console.log('Swatch Deleted', color)
    },
    onUploadHandler(cropper) {
      this.previewAvatar = cropper
        .getCroppedCanvas()
        .toDataURL(this.cropperOutputMime)
    },
    onHandleUploadError() {
      window.toastr['error']('Oops! Something went wrong...')
    },
    onChangeLogo(file) {
      let fileObject = this.fileObject = file.target.files;
      console.log('file object: ', fileObject);
      // console.log(fileObject[0].type);
      if ( fileObject[0].type != 'image/png' ) {
        window.toastr['error'](
          this.$t('settings.mobile.message_error_file_type')
        )
        this.fileObject = [];
        return;
      }
      // validar logo size
      if (fileObject[0].size > 2097152) {
        window.toastr['error'](
          this.$t('settings.mobile.message_error_file_size')
        )
        this.fileObject = [];
        return;
      }

      if (fileObject.length) {
        // console.log('entroooo');
        let reader = new FileReader();
        reader.readAsDataURL(fileObject[0]);
        reader.onload = evt => {
          let img = new Image();
          img.onload = () => {

            // console.log('img: ', img);

            if (img.width != img.height) {
              window.toastr['error'](
                this.$t('settings.mobile.message_error_file_dimensions')
              )
              this.fileObject = [];
              // return;
            }
            // console.log(fileObject);
          }
          img.src = evt.target.result;
        }
      }
    },
    async setInitialData() {
      this.isRequestOnGoing = true
      let response = await this.fetchMobileSetting()
      // console.log('response: ', response)

      if (response.data.success && response.data.mobileSetting.length) {
        this.formData.horizontal_menu = response.data.mobileSetting[0].horizontal_menu;
        this.formData.vertical_menu = response.data.mobileSetting[0].vertical_menu;
        this.formData.dashboard = response.data.mobileSetting[0].dashboard;
        this.formData.color_palette = response.data.mobileSetting[0].color_palette;
        // this.logo_base64 = response.data.mobileSetting[0].logo_base64;
        this.file_url = response.data.mobileSetting[0].file_url;
        // console.log(response.data.mobileSetting[0]);

        if(this.file_url != "undefined"){
          this.logoexist = true;
        }
      
      } else {
        
      }


      this.isRequestOnGoing = false 
    },
    async saveData() {
      // this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true

      let data = new FormData()

      data.append('color_palette', this.formData.color_palette)
      data.append('vertical_menu', JSON.stringify(this.formData.vertical_menu) )
      data.append('horizontal_menu', JSON.stringify(this.formData.horizontal_menu) )
      data.append('dashboard', JSON.stringify(this.formData.dashboard) )
      if (this.fileObject) {
        data.append('logo', this.fileObject[0] )
      }

      // console.log('data', data)
      try {

        let response = await this.saveMobileSettings(data)

        if (response.data.success) {
          this.isLoading = false

          /* if (this.fileObject && this.previewAvatar) {
            let avatarData = new FormData()

            avatarData.append(
              'admin_avatar',
              JSON.stringify({
                name: this.fileObject.name,
                data: this.previewAvatar,
              })
            )

            this.uploadAvatar(avatarData)
          } */

          window.toastr['success'](
            this.$t('settings.account_settings.updated_message')
          )
          this.setInitialData();
        }
      } catch (error) {
        this.isLoading = false
        return true
      }
    },
  },
}
</script>
<style scoped>
  .inputFile {
    opacity: 0;
    overflow: hidden;
    position: absolute;
    z-index: -1;
  }

  .logo{
    height: 300px;
    width: 300px;
    border-radius: 150px;
    margin-left: 19%;
  }

</style>