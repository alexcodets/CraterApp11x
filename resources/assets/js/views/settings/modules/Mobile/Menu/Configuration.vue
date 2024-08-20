<template>
  <form class="relative h-full" @submit.prevent="saveData">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card variant="setting-card">
      <template slot="header">
        <h6 class="sw-section-title">
          {{ $t('settings.mobile.mobile') }}
        </h6>
      </template>

      <div class="grid gap-12 grid-col-1 md:grid-cols-3 mt-4">

          <sw-input-group :label="$t('settings.mobile.logo')">
            <!-- img logo -->
            <img class="mt-2 logo" v-if="logoexist == true" :src="logo_base64" alt=""> 

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
            <ul class="list-group mt-3">
              <li class="list-group-item" v-for="file in fileObject">
                <span v-on:click="deleteFile(file.name)" class="pointer">
                  <trash-icon class="h-5 mr-3 text-gray-600" style="position: absolute"/>
                </span>
                <span v-bind:class="{ marginFilename: true, downloadFilename: isEdit }" v-on:click="downloadFile(file.name)"> {{ file.name }} </span>
              </li>
            </ul>
          </sw-input-group>

          <sw-input-group :label="$t('settings.mobile.color_palette')">
            <vue-tailwind-color-picker v-model="formData.color_palette" ref="colorPalette" v-if="!isRequestOnGoing" :swatches.sync="swatches" :hide-swatches="false" @change="changedColor" @addSwatch="swatchAdded" @deleteSwatch="swatchDeleted" class="mt-3"/>
          </sw-input-group>

          <sw-input-group :label="$t('settings.mobile.dark_color_palette')">
            <vue-tailwind-color-picker v-model="formData.dark_color_palette" ref="darkColorPalette" v-if="!isRequestOnGoing" :swatches.sync="swatches" :hide-swatches="false" @change="changedColor" @addSwatch="swatchAdded" @deleteSwatch="swatchDeleted" class="mt-3"/>
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

          <!-- <sw-checkbox
            v-model="formData.vertical_menu.pbx_services"
            :label="$t('settings.mobile.pbx_services')"
            variant="primary"
            size="sm"            
          /> -->

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

          <!-- <sw-checkbox
            v-model="formData.horizontal_menu.pbx_services"
            :label="$t('settings.mobile.pbx_services')"
            variant="primary"
            size="sm"
            
          /> -->

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

          <!-- <sw-checkbox
            v-model="formData.dashboard.pbx_services"
            :label="$t('settings.mobile.pbx_services')"
            variant="primary"
            size="sm"
          /> -->

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
        </sw-input-group>
      </div>
      <div class="w-full flex flex-wrap">
            <sw-input-group
              :label="$t('settings.mobile.token_firebase_notification')"
              class="w-full md:w-1/2 mt-3"
            >
              <sw-input
                v-model="formData.firebase_token_notification"
                focus
                :type="showPassword ? 'text' : 'password'"
                name="firebase_token_notification"
                @keydown.space.prevent
              >
                <template v-slot:rightIcon>
                  <eye-off-icon
                    v-if="showPassword"
                    class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                    @click="showPassword = !showPassword"
                  />
                  <eye-icon
                    v-else
                    class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                    @click="showPassword = !showPassword"
                  />
                </template>
              </sw-input>
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
import VueTailwindColorPicker from 'vue-tailwind-color-picker'
import {
  DocumentDuplicateIcon,
  EyeOffIcon,
  EyeIcon,
  CheckIcon,
  TrashIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    CloudUploadIcon,
    VueTailwindColorPicker
  },

  data() {
    return {
      showPassword: false,
      color: '#00FF00FF',
      logo_base64: '',
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
        firebase_token_notification: null,
        logo: null ,
        color_palette: null,
        dark_color_palette: null,
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
          payments: false
        },
      },
      isLoading: false,
      language: null,
      isRequestOnGoing: false,
      fileObject: null
    }
  },

  validations: {
   /*  formData: {
      name: {
        required,
      },
      email: {
        required,
        email,
      },
      password: {
        minLength: minLength(8),
      },
      confirm_password: {
        sameAsPassword: sameAs('password'),
      },
    },
    language: {
      required,
    }, */
  },

  computed: {
    
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
      //console.log('Swatch Added', color)
    },
    swatchDeleted(color) {
      //console.log('Swatch Deleted', color)
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
      // console.log(fileObject);
      //console.log(fileObject[0].type);
      if ( fileObject[0].type != 'image/png' ) {
        window.toastr['error'](
          this.$t('settings.mobile.message_error_file_type')
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
            /* console.log('img: ', img.width);
            console.log('img: ', img.height);
            console.log('img: ', typeof img.width);
            console.log('img: ', typeof img.height); */
            // console.log('img: ', img.type);
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
        this.formData.dark_color_palette = response.data.mobileSetting[0].dark_color_palette;
        this.formData.firebase_token_notification = response.data.mobileSetting[0].firebase_token_notification;
        this.logo_base64 = response.data.mobileSetting[0].logo_base64;

         if(this.logo_base64 != "undefined"){
            this.logoexist = true;
         }
        
      } else {
        
      }

      /* this.formData.name = response.data.user.name
      this.formData.email = response.data.user.email

      if (response.data.user.avatar) {
        this.previewAvatar = response.data.user.avatar
      } else {
        this.previewAvatar = '/images/default-avatar.jpg'
      }

      let res = await this.fetchUserSettings(['language'])

      this.language = this.languages.find(
        (language) => language.code == res.data.language
      ) */
      this.isRequestOnGoing = false 
    },
    async saveData() {
      // this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true

      this.formData.color_palette = this.$refs.colorPalette.colorData.hexa
      this.formData.dark_color_palette = this.$refs.darkColorPalette.colorData.hexa

      let data = new FormData()

      data.append('color_palette', this.formData.color_palette)
      data.append('dark_color_palette', this.formData.dark_color_palette)
      data.append('vertical_menu', JSON.stringify(this.formData.vertical_menu) )
      data.append('horizontal_menu', JSON.stringify(this.formData.horizontal_menu) )
      data.append('dashboard', JSON.stringify(this.formData.dashboard) )
      data.append('firebase_token_notification', this.formData.firebase_token_notification)
      if (this.fileObject) {
        data.append('logo', this.fileObject[0] )
      }

      //console.log('data', data)
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