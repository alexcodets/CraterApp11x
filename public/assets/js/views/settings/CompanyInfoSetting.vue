<template>
  <form @submit.prevent="updateCompanyData" class="relative h-full">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card variant="setting-card">
      <template slot="header">
        <h6 class="sw-section-title">
          {{ $t('settings.company_info.company_info') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.company_info.section_description') }}
        </p>
      </template>

      <div class="grid mb-6 md:grid-cols-2">
        <sw-input-group :label="$tc('settings.company_info.company_logo')">
          <div
            id="logo-box"
            class="
              relative
              flex
              items-center
              justify-center
              h-24
              p-5
              mt-2
              bg-transparent
              border-2 border-gray-200 border-dashed
              rounded-md
              image-upload-box
            "
          >
            <img
              v-if="previewLogo"
              :src="previewLogo"
              class="absolute opacity-100 preview-logo"
              style="max-height: 80%; animation: fadeIn 2s ease"
            />
            <div v-else class="flex flex-col items-center">
              <cloud-upload-icon
                class="h-5 mb-2 text-xl leading-6 text-gray-400"
              />
              <p class="text-xs leading-4 text-center text-gray-400">
                Drag a file here or
                <span id="pick-avatar" class="cursor-pointer text-primary-500">
                  browse
                </span>
                to choose a file
              </p>
            </div>
          </div>

          <sw-avatar
            trigger="#logo-box"
            :preview-avatar="previewLogo"
            @changed="onChange"
            @uploadHandler="onUploadHandler"
            @handleUploadError="onHandleUploadError"
          >
            <template v-slot:icon>
              <cloud-upload-icon
                class="h-5 mb-2 text-xl leading-6 text-gray-400"
              />
            </template>
          </sw-avatar>
        </sw-input-group>
      </div>

      <div class="grid gap-6 sm:grid-col-1 md:grid-cols-2">
        <sw-input-group
          :label="$tc('settings.company_info.company_name')"
          :error="nameError"
          required
        >
          <sw-input
            v-model="formData.name"
            :invalid="$v.formData.name.$error"
            :placeholder="$t('settings.company_info.company_name')"
            class="mt-2"
            @input="$v.formData.name.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('settings.company_info.company_identifier')"
          :error="company_identifierError"
          required
        >
          <sw-input
            v-model="formData.company_identifier"
            :invalid="$v.formData.company_identifier.$error"
            :placeholder="$t('settings.company_info.company_identifier')"
            class="mt-2"
            @input="$v.formData.company_identifier.$touch()"
          />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.phone')">
          <sw-input
            v-model="formData.phone"
            class="mt-2"
            :placeholder="$t('settings.company_info.phone')"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('settings.company_info.country')"
          :error="countryError"
          required
        >
          <sw-select
            v-model="country"
            :options="countries"
            :class="{ error: $v.formData.country_id.$error }"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            :placeholder="$t('general.select_country')"
            class="mt-2"
            label="name"
            track-by="id"
            @select="countrySelected($event)"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('settings.company_info.state')"
          :error="statusCustomerError"
          required
        >
          <sw-select
            v-model="formData.billing_state"
            :options="billing_states"
            :invalid="$v.formData.billing_state.$error"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :tabindex="8"
            :placeholder="$t('general.select_state')"
            label="name"
            track-by="id"
          />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.city')">
          <sw-input
            v-model="formData.city"
            :placeholder="$tc('settings.company_info.city')"
            name="city"
            class="mt-2"
            type="text"
          />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.zip')">
          <sw-input
            v-model="formData.zip"
            :placeholder="$tc('settings.company_info.zip')"
            class="mt-2"
          />
        </sw-input-group>

        <div>
          <sw-input-group
            :label="$tc('settings.company_info.address')"
            :error="address1Error"
          >
            <sw-textarea
              v-model="formData.address_street_1"
              :placeholder="$tc('general.street_1')"
              :class="{ invalid: $v.formData.address_street_1.$error }"
              rows="2"
              @input="$v.formData.address_street_1.$touch()"
            />
          </sw-input-group>

          <sw-input-group :error="address2Error" class="my-2">
            <sw-textarea
              v-model="formData.address_street_2"
              :placeholder="$tc('general.street_2')"
              :class="{ invalid: $v.formData.address_street_2.$error }"
              rows="2"
              @input="$v.formData.address_street_2.$touch()"
            />
          </sw-input-group>
        </div>
      </div>

      <div class="grid gap-6 sm:grid-col-1 md:grid-cols-2">
        <sw-input-group
          :label="$tc('settings.company_info.company_page_title')"
        >
          <sw-input
            v-model="formData.company_page_title"
            class="mt-2"
            :placeholder="$t('settings.company_info.company_page_title')"
          />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.company_favicon')">
          <div
            id="favicon-box"
            class="
              relative
              flex
              items-center
              justify-center
              h-24
              p-5
              mt-2
              bg-transparent
              border-2 border-gray-200 border-dashed
              rounded-md
              image-upload-box
            "
          >
            <img
              v-if="previewFavicon"
              :src="previewFavicon"
              class="absolute opacity-100 preview-logo"
              style="max-height: 80%; animation: fadeIn 2s ease"
            />
            <div v-else class="flex flex-col items-center">
              <cloud-upload-icon
                class="h-5 mb-2 text-xl leading-6 text-gray-400"
              />
              <p class="text-xs leading-4 text-center text-gray-400">
                Drag a file here or
                <span id="pick-avatar" class="cursor-pointer text-primary-500">
                  browse
                </span>
                to choose a file
              </p>
            </div>
          </div>

          <sw-avatar
            trigger="#favicon-box"
            :preview-avatar="previewFavicon"
            @changed="onChange2"
            @uploadHandler="onUploadHandler2"
            @handleUploadError="onHandleUploadError2"
          >
            <template v-slot:icon>
              <cloud-upload-icon
                class="h-5 mb-2 text-xl leading-6 text-gray-400"
              />
            </template>
          </sw-avatar>
        </sw-input-group>
      </div>
      <sw-divider class="mb-5 md:mb-8" />
      <div class="grid gap-6 sm:grid-col-1 md:grid-cols-2">
        <sw-input-group
          :label="$tc('settings.company_info.title_header')"
          :error="tittleHeadeError"
          required
        >
          <sw-input
            v-model="formData.title_header"
            :placeholder="$tc('settings.company_info.title_header')"
            name="title_header"
            class="mt-2"
            type="text"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('settings.company_info.subtitle_header')"
          :error="subtittleHeadeError"
          required
        >
          <sw-input
            v-model="formData.subtitle_header"
            :placeholder="$tc('settings.company_info.subtitle_header')"
            name="subtitle_header"
            class="mt-2"
            type="text"
          />
        </sw-input-group>
      </div>

      <sw-button
        class="mt-4"
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
      >
        <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
        {{ $tc('settings.company_info.save') }}
      </sw-button>
    </sw-card>
  </form>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { CloudUploadIcon } from '@vue-hero-icons/solid'
const { required, email, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    CloudUploadIcon,
  },
  data() {
    return {
      isFetchingData: false,
      formData: {
        name: null,
        email: '',
        phone: '',
        zip: '',
        address_street_1: '',
        address_street_2: '',
        website: '',
        country_id: null,
        state_id: '',
        billing_state: null,
        city: '',
        company_page_title: '',
        company_identifier: '',
        title_header: '',
        subtitle_header: '',
      },
      isLoading: false,
      country: null,
      passData: [],
      fileSendUrl: '/api/v1/settings/company',
      previewLogo: null,
      previewFavicon: null,
      fileObject: null,
      fileObject2: null,
      cropperOutputMime: '',
      cropperOutputMime2: '',
      isRequestOnGoing: false,
      isIco: false,
      billing_states: [],
    }
  },
  watch: {
    country(newCountry) {
      this.formData.country_id = newCountry.id
      if (this.isFetchingData) {
        return true
      }
    },
    state(newCountry) {
      this.formData.billing_state = newCountry.id
      if (this.isFetchingData) {
        return true
      }
    },
  },
  validations: {
    formData: {
      name: {
        required,
      },
      company_identifier: {
        required,
      },
      country_id: {
        required,
      },
      billing_state: {
        required,
      },
      email: {
        email,
      },
      address_street_1: {
        maxLength: maxLength(250),
      },
      address_street_2: {
        maxLength: maxLength(250),
      },
      title_header: {
        maxLength: maxLength(250),
        required,
      },
      subtitle_header: {
        maxLength: maxLength(250),
        required,
      },
    },
  },
  computed: {
    ...mapGetters(['countries']),
    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },
    company_identifierError() {
      if (!this.$v.formData.company_identifier.$error) {
        return ''
      }
      if (!this.$v.formData.company_identifier.required) {
        return this.$tc('validation.required')
      }
    },
    countryError() {
      if (!this.$v.formData.country_id.$error) {
        return ''
      }
      if (!this.$v.formData.country_id.required) {
        return this.$tc('validation.required')
      }
    },
    address1Error() {
      if (!this.$v.formData.address_street_1.$error) {
        return ''
      }

      if (!this.$v.formData.address_street_1.maxLength) {
        return this.$tc('validation.address_maxlength')
      }
    },
    tittleHeadeError() {
      if (!this.$v.formData.title_header.required) {
        return this.$tc('validation.required')
      }
    },
    subtittleHeadeError() {
      if (!this.$v.formData.subtitle_header.required) {
        return this.$tc('validation.required')
      }
    },
    address2Error() {
      if (!this.$v.formData.address_street_2.$error) {
        return ''
      }

      if (!this.$v.formData.address_street_2.maxLength) {
        return this.$tc('validation.address_maxlength')
      }
    },
    statusCustomerError() {
      if (this.$v.formData != null && this.$v.formData != 'undefined') {
        if (!this.$v.formData.billing_state.$error) {
          return ''
        }
        if (!this.$v.formData.billing_state.required) {
          return this.$tc('validation.required')
        }
      }
    },
  },
  mounted() {
    this.setInitialData()
  },
  methods: {
    ...mapActions('company', [
      'updateCompany',
      'updateCompanyLogo',
      'updateCompanyPageTitle',
      'updateCompanyFavicon',
    ]),
    ...mapActions('user', ['fetchCurrentUser']),
    onUploadHandler(cropper) {
      this.previewLogo = cropper
        .getCroppedCanvas()
        .toDataURL(this.cropperOutputMime)
    },
    onHandleUploadError() {
      window.toastr['error']('Oops! Something went wrong...')
    },
    onChange(file) {
      this.cropperOutputMime = file.type
      this.fileObject = file
    },

    onUploadHandler2(cropper2) {
      if (this.isIco) {
        this.previewFavicon = cropper2
          .getCroppedCanvas()
          .toDataURL(this.cropperOutputMime2)
      }
    },
    onHandleUploadError2() {
      window.toastr['error']('Oops! Something went wrong...')
    },
    onChange2(file) {
      let name = file.name
      let ico = name.split('.')
      if (ico[1] == 'ico') {
        this.cropperOutputMime2 = file.type
        this.fileObject2 = file
        this.isIco = true
      } else {
        this.isIco = false
        window.toastr['error'](this.$tc('validation.ico_type'))
      }
    },
    async setInitialData() {
      this.isRequestOnGoing = true
      let response = await this.fetchCurrentUser()

      this.isFetchingData = true
      if (response.data.user) {
        this.formData.name = response.data.user.company.name =
          response.data.user.company.name
        if (response.data.user.company.company_identifier) {
          this.formData.company_identifier =
            response.data.user.company.company_identifier
        }
        if (response.data.user.company.title_header) {
          this.formData.title_header = response.data.user.company.title_header
        }

        if (response.data.user.company.subtitle_header) {
          this.formData.subtitle_header =
            response.data.user.company.subtitle_header
        }
        this.formData.address_street_1 =
          response.data.user.company.address.address_street_1
        this.formData.address_street_2 =
          response.data.user.company.address.address_street_2
        this.formData.zip = response.data.user.company.address.zip
        this.formData.phone = response.data.user.company.address.phone
        //console.log(response.data.user.company.address.state_id)
        //this.formData.state = response.data.user.company.address.state
        this.formData.city = response.data.user.company.address.city
        this.country = response.data.user.company.address.country
        let res2 = await window.axios.get('/api/v1/states/' + this.country.code)

        if (res2) {
          this.billing_states = []
          this.billing_states = res2.data.states
          if (response.data.user.company.address.state_id) {
            let myDog = this.billing_states.find(
              (state) =>
                response.data.user.company.address.state_id === state.id
            )

            this.billing_state = myDog
            this.formData.billing_state = myDog
            //console.log( myDog);
          }
        }

        this.previewLogo = response.data.user.company.logo
        this.previewFavicon = response.data.user.company.favicon
      }
      if (response.data.setting) {
        this.formData.company_page_title = response.data.setting.value
      }
      this.isRequestOnGoing = false
    },
    async updateCompanyData() {
      this.$v.formData.$touch()
     // console.log('Llego aqui')
      if (this.$v.$invalid) {
       // console.log('Llego aqui 2')
        return true
      }
      this.isLoading = true

      let response = await this.updateCompany(this.formData)
      if (response.data.success) {
        this.isLoading = false
        if (this.fileObject && this.previewLogo) {
          let logoData = new FormData()
          logoData.append(
            'company_logo',
            JSON.stringify({
              name: this.fileObject.name,
              data: this.previewLogo,
            })
          )
          await this.updateCompanyLogo(logoData)
        }
        if (this.formData.company_page_title) {
          await this.updateCompanyPageTitle(this.formData)
        }
        if (this.fileObject2 && this.previewFavicon) {
          let logoData = new FormData()
          logoData.append(
            'company_favicon',
            JSON.stringify({
              name: this.fileObject2.name,
              data: this.previewFavicon,
            })
          )
          await this.updateCompanyFavicon(logoData)
        }
        this.isLoading = false
        window.toastr['success'](
          this.$t('settings.company_info.updated_message')
        )
        location.reload()
      } else {
        this.isLoading = false
        window.toastr['error'](response.data.error)
        return true
      }
    },

    async countrySelected(country) {
      let res = await window.axios.get('/api/v1/states/' + country.code)
      //console.log(res.data.states)
      if (res) {
        this.billing_state = null
        this.billing_states = []
        this.billing_states = res.data.states
      }
    },
  },
}
</script>
