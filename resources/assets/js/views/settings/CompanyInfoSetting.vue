<template>
  <form @submit.prevent="updateCompanyData" class="relative h-full">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card variant="setting-card">
      <template slot="header">
        <h6 class="sw-section-title">
          {{ $t('settings.company_info.company_info') }}
        </h6>
        <p class="mt-2 text-sm leading-snug text-gray-500" style="max-width: 680px">
          {{ $t('settings.company_info.section_description') }}
        </p>
      </template>

      <div class="grid mb-2 md:grid-cols-2">
        <sw-input-group :label="$tc('settings.company_info.company_logo')">
          <div id="logo-box" class="
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
            ">
            <img v-if="previewLogo" :src="previewLogo" class="absolute opacity-100 preview-logo"
              style="max-height: 80%; animation: fadeIn 2s ease" />
            <div v-else class="flex flex-col items-center">
              <cloud-upload-icon class="h-5 mb-2 text-xl leading-6 text-gray-400" />
              <p class="text-xs leading-4 text-center text-gray-400">
                {{ $tc('settings.company_info.drag_file') }}
                
                <span id="pick-avatar" class="cursor-pointer text-primary-500">
                  {{ $tc('settings.company_info.browser') }}
                </span>
                {{ $tc('settings.company_info.choose_file') }}
              </p>
            </div>
          </div>

          <sw-avatar trigger="#logo-box" class="mb-2" :preview-avatar="previewLogo" @changed="onChange"
            @uploadHandler="onUploadHandler" @handleUploadError="onHandleUploadError">
            <template v-slot:icon>
              <cloud-upload-icon class="h-5 mb-2 text-xl leading-6 text-gray-400" />
            </template>
          </sw-avatar>
          <span class="text-danger text-sm">{{ $tc('settings.company_info.supported_files') }}</span>
          <br>
          <span class="text-danger text-sm">{{ $tc('settings.company_info.file_size') }}</span>
          <br>
          <span class="text-danger text-sm">{{ $tc('settings.company_info.maximun_file') }}</span>
        </sw-input-group>
      </div>

      <div class="grid mb-6 md:grid-cols-2">
        <sw-input-group :label="$tc('settings.company_info.wallpaper_login')">
          <div id="wallpaper-box" class="
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
            ">
            <img v-if="previewWallpaper" :src="previewWallpaper" class="absolute opacity-100 preview-logo"
              style="max-height: 80%; animation: fadeIn 2s ease" />
            <div v-else class="flex flex-col items-center">
              <cloud-upload-icon class="h-5 mb-2 text-xl leading-6 text-gray-400" />
              <p class="text-xs leading-4 text-center text-gray-400">
                {{ $tc('settings.company_info.drag_file') }}
                <span id="pick-avatar" class="cursor-pointer text-primary-500">
                  {{ $tc('settings.company_info.browser') }}
                </span>
                {{ $tc('settings.company_info.choose_file') }}
              </p>
            </div>

          </div>


          <sw-avatar trigger="#wallpaper-box" class="mb-2" :preview-avatar="previewWallpaper" @changed="onChangeWallpaper"
            @uploadHandler="onUploadHandlerWallpaper" @handleUploadError="onHandleUploadError">
            <template v-slot:icon>
              <cloud-upload-icon class="h-5 mb-2 text-xl leading-6 text-gray-400" />
            </template>
          </sw-avatar>
           <span class="text-danger text-sm">{{ $tc('settings.company_info.supported_files') }}</span>
          <br>
          <span class="text-danger text-sm">{{ $tc('settings.company_info.file_size_2') }}</span>
          <br>
          <span class="text-danger text-sm">{{ $tc('settings.company_info.maximun_file_2') }}</span>
        </sw-input-group>
      </div>

      <div class="grid gap-6 sm:grid-col-1 md:grid-cols-2">
        <sw-input-group :label="$tc('settings.company_info.company_name')" :error="nameError" required>
          <sw-input v-model="formData.name" :invalid="$v.formData.name.$error"
            :placeholder="$t('settings.company_info.company_name')" class="mt-2" @input="$v.formData.name.$touch()" />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.company_identifier')" :error="company_identifierError"
          required>
          <sw-input v-model="formData.company_identifier" :invalid="$v.formData.company_identifier.$error"
            :placeholder="$t('settings.company_info.company_identifier')" class="mt-2"
            @input="$v.formData.company_identifier.$touch()" />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.phone')">
          <sw-input v-model="formData.phone" class="mt-2" :placeholder="$t('settings.company_info.phone')" />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.country')" :error="countryError" required>
          <sw-select v-model="country" :options="countries" :class="{ error: $v.formData.country_id.$error }"
            :searchable="true" :show-labels="false" :allow-empty="false" :placeholder="$t('general.select_country')"
            class="mt-2" label="name" track-by="id" @select="countrySelected($event)" />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.state')" :error="statusCustomerError" required>
          <sw-select v-model="formData.billing_state" :options="billing_states"
            :invalid="$v.formData.billing_state.$error" :searchable="true" :show-labels="false" :allow-empty="true"
            :tabindex="8" :placeholder="$t('general.select_state')" label="name" track-by="id" />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.city')" :error="cityError" required>
          <sw-input v-model="formData.city" :invalid="$v.formData.city.$error"
            :placeholder="$tc('settings.company_info.city')" name="city" class="mt-2" type="text" />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.county')" :error="countyError" required>
          <sw-input v-model="formData.county" :invalid="$v.formData.county.$error"
            :placeholder="$tc('settings.company_info.county')" name="county" class="mt-2" type="text" />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.zip')" :error="zipError" required>
          <sw-input v-model="formData.zip" :invalid="$v.formData.zip.$error"
            :placeholder="$tc('settings.company_info.zip')" class="mt-2" />
        </sw-input-group>

        <div>
          <sw-input-group :label="$tc('settings.company_info.address')" :error="address1Error">
            <sw-textarea v-model="formData.address_street_1" :placeholder="$tc('general.street_1')"
              :class="{ invalid: $v.formData.address_street_1.$error }" rows="2"
              @input="$v.formData.address_street_1.$touch()" />
          </sw-input-group>

          <sw-input-group :error="address2Error" class="my-2">
            <sw-textarea v-model="formData.address_street_2" :placeholder="$tc('general.street_2')"
              :class="{ invalid: $v.formData.address_street_2.$error }" rows="2"
              @input="$v.formData.address_street_2.$touch()" />
          </sw-input-group>
        </div>

        <!-- billing validation button -->
        <sw-input-group v-if="isAvalaraAvailable" class="mt-6">
          <sw-button variant="primary-outline" size="lg" type="button" @click="checkBilling" :loading="isLoading">
            <check-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ $t('customers.billing_validation') }}
          </sw-button>
        </sw-input-group>
        <!-- <div class="flex w-full md:w-1/2 my-2">
          <sw-button
            variant="primary-outline"
            size="lg"
            type="button"
            @click="checkBilling"
            :loading="isLoading"
          >
            <check-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ $t('customers.billing_validation') }}
          </sw-button>
        </div> -->
      </div>

      <br />

      <div class="grid gap-6 sm:grid-col-1 md:grid-cols-2">
        <sw-input-group :label="$tc('settings.company_info.company_page_title')">
          <sw-input v-model="formData.company_page_title" class="mt-2"
            :placeholder="$t('settings.company_info.company_page_title')" />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.company_favicon')">
          <div id="favicon-box" class="
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
            ">
            <img v-if="previewFavicon" :src="previewFavicon" class="absolute opacity-100 preview-logo"
              style="max-height: 80%; animation: fadeIn 2s ease" />
            <div v-else class="flex flex-col items-center">
              <cloud-upload-icon class="h-5 mb-2 text-xl leading-6 text-gray-400" />
              <p class="text-xs leading-4 text-center text-gray-400">
                {{ $tc('settings.company_info.drag_file') }}
                <span id="pick-avatar" class="cursor-pointer text-primary-500">
                  {{ $tc('settings.company_info.browser') }}
                </span>
                {{ $tc('settings.company_info.choose_file') }}
              </p>
            </div>
          </div>

          <sw-avatar trigger="#favicon-box" :preview-avatar="previewFavicon" @changed="onChange2"
            @uploadHandler="onUploadHandler2" @handleUploadError="onHandleUploadError2">
            <template v-slot:icon>
              <cloud-upload-icon class="h-5 mb-2 text-xl leading-6 text-gray-400" />
            </template>
          </sw-avatar>
        </sw-input-group>
      </div>

      <!-- input text -->
      <sw-input-group :label="$t('settings.company_info.company_report_info')" class="mt-6 mb-4">
        <base-custom-input v-model="formData.company_report_info" :fields="companyFields" class="mt-2" />
      </sw-input-group>

      <sw-divider class="mb-5 md:mb-8" />
      <div class="grid gap-6 sm:grid-col-1 md:grid-cols-2">
        <sw-input-group :label="$tc('settings.company_info.title_header')" :error="tittleHeadeError" required>
          <sw-input v-model="formData.title_header" :placeholder="$tc('settings.company_info.title_header')"
            name="title_header" class="mt-2" type="text" />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.subtitle_header')" :error="subtittleHeadeError" required>
          <sw-input v-model="formData.subtitle_header" :placeholder="$tc('settings.company_info.subtitle_header')"
            name="subtitle_header" class="mt-2" type="text" />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.footer_text')" required>
          <sw-input v-model="formData.footer_text_value" :invalid="$v.formData.footer_text_value.$error"
            :placeholder="$tc('settings.company_info.footer_text')" name="footer_text_value" class="mt-2" type="text" />
          <p class="mt-1 ml-1 p-0 m-0 text-xs leading-4 text-gray-500" style="max-width: 480px">
            {{ $tc('settings.company_info.example_powered') }}
          </p>
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.footer_url')" required>
          <sw-input v-model="formData.footer_url_value" :invalid="$v.formData.footer_url_value.$error"
            :placeholder="$tc('settings.company_info.footer_url')" name="footer_url_value" class="mt-2" type="url" />
          <p class="mt-1 ml-1 p-0 m-0 text-xs leading-4 text-gray-500" style="max-width: 480px">
            {{ $tc('settings.company_info.example_website') }}
          </p>
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.footer_url_name')" required>
          <sw-input v-model="formData.footer_url_name" :invalid="$v.formData.footer_url_name.$error"
            :placeholder="$tc('settings.company_info.footer_url_name')" name="footer_url_name" class="mt-2" type="text" />
          <p class="mt-1 ml-1 p-0 m-0 text-xs leading-4 text-gray-500" style="max-width: 480px">
            {{ $tc('settings.company_info.example_company_name') }}
          </p>
        </sw-input-group>

        <div class="flex mt-10 ml-1">
          <div class="relative w-12">
            <sw-switch v-model="formData.current_year" class="absolute" style="top: -20px" />
          </div>

          <div class="ml-2">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('settings.company_info.include_current_year') }}
            </p>
          </div>
        </div>


        <div class="flex mt-10 ml-1">
          <div class="relative w-12">
            <sw-switch v-model="formData.hide_title" class="absolute" style="top: -20px" />
          </div>

          <div class="ml-2">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('settings.company_info.hide_title') }}
            </p>
          </div>
        </div>

      </div>

      <sw-divider class="mb-5 md:my-8" />


      <div class="grid gap-6 sm:grid-col-1 md:grid-cols-2">
        <sw-input-group :label="$tc('settings.company_info.header_color')">
          <!-- {{formData.header_color}} -->
          <vue-tailwind-color-picker ref="colorPickerHeader" v-if="showPicker" v-model="formData.header_color"
            :swatches.sync="swatches" :hide-swatches="true" @addSwatch="swatchAdded" @deleteSwatch="swatchDeleted" />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.primary_color')" class="mt-2">
          <vue-tailwind-color-picker ref="colorPickerPrimary" v-if="showPicker" v-model="formData.primary_color"
            :swatches.sync="swatches" :hide-swatches="true" @change="changedColorPrimary" @addSwatch="swatchAdded"
            @deleteSwatch="swatchDeleted" />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.color_invoice')" class="mt-2">
          <vue-tailwind-color-picker ref="colorPickerInvoice" v-if="showPicker" v-model="formData.color_invoice"
            :swatches.sync="swatches" :hide-swatches="true" @addSwatch="swatchAdded" @deleteSwatch="swatchDeleted" />
        </sw-input-group>
      </div>

      <sw-button class="mt-4" :loading="isLoading" :disabled="isLoading" variant="primary">
        <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
        {{ $tc('settings.company_info.save') }}
      </sw-button>
    </sw-card>
  </form>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { CloudUploadIcon, CheckIcon } from '@vue-hero-icons/solid'
import VueTailwindColorPicker from 'vue-tailwind-color-picker'
const { required, email, maxLength, minLength } = require('vuelidate/lib/validators')
import changeColorPrimaryTailwind from '@/helpers/changeColorPrimaryTailwind'

export default {
  components: {
    CloudUploadIcon,
    CheckIcon,
    VueTailwindColorPicker
  },
  data() {
    return {
      isAvalaraAvailable: false,
      companyFields: ['company', 'invoiceCustom'],
      isFetchingData: false,
      formData: {
        company_report_info: '',
        address_street_1: '',
        address_street_2: '',
        billing_state: null,
        city: '',
        county: '',
        company_page_title: '',
        company_identifier: '',
        country_id: null,
        name: null,
        email: '',
        phone: '',
        zip: '',
        website: '',
        state_id: '',
        title_header: '',
        subtitle_header: '',
        header_color: '#5851D8',
        primary_color: '#5851D8',
        color_invoice: '#5851D8',
        footer_text_value: '',
        footer_url_value: '',
        footer_url_name: '',
        current_year: false,
        hide_title: false

      },
      customer_avalara_location_id: null,
      isLoading: false,
      country: null,
      passData: [],
      fileSendUrl: '/api/v1/settings/company',
      previewLogo: null,
      previewFavicon: null,
      fileObject: null,
      fileObject2: null,
      cropperOutputMime: '',
      cropperOutputMimeWallpaper: '',
      previewWallpaper: null,
      cropperOutputMime2: '',
      isRequestOnGoing: false,
      isIco: false,
      billing_states: [],
      userId: null,
      color: '#5851D8',
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
      showPicker: false
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
      city: {
        required,
      },
      county: {
        required,
      },
      zip: {
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

      footer_text_value: {
        minLength: minLength(3),
        maxLength: maxLength(250),
        required,
      },
      footer_url_value: {
        required,
        url: true,
      },
      footer_url_name: {
        minLength: minLength(3),
        maxLength: maxLength(250),
        required,
      },
      current_year: {
        required,
      },

    },
  },
  computed: {
    ...mapGetters(['countries']),
    ...mapGetters('avalara', ['avalaraLocationSaved']),

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
    footerTextError() {
      if (!this.$v.formData.footer_text_value.required) {
        return this.$tc('validation.required')
      }
    },
    footerUrlError() {
      if (!this.$v.formData.footer_url_value.required) {
        return this.$tc('validation.required')
      }
      // valid url
      if (!this.$v.formData.footer_url_value.url) {
        return this.$tc('validation.url')
      }
    },
    subtittleHeadeError() {
      if (!this.$v.formData.subtitle_header.required) {
        return this.$tc('validation.required')
      }
    },
    cityError() {
      if (!this.$v.formData.city.required) {
        return this.$tc('validation.required')
      }
    },
    countyError() {
      if (!this.$v.formData.county.required) {
        return this.$tc('validation.required')
      }
    },
    zipError() {
      if (!this.$v.formData.zip.required) {
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
  beforeDestroy() {
    this.unsubscribe()
  },
  mounted() {
    this.setInitialData()
  },
  created() {
    this.getStatusModuleAvalara()
    this.subscribeAvalaraBillingInfo()
  },
  methods: {
    ...mapActions('company', [
      'updateCompany',
      'updateCompanyLogo',
      'updateCompanyWallpaper',
      'updateCompanyPageTitle',
      'updateCompanyFavicon',
      'fetchCompanySettings',
    ]),
    ...mapActions('user', ['fetchCurrentUser']),
    ...mapActions('modules', ['getModules']),

    ...mapActions('customer', ['billingValidation']),

    ...mapActions('modal', ['openModal']),

    ...mapActions('avalara', ['fetchAvalaraItemsTaxes', 'checkStatusAvalara']),

    async checkBilling() {
      this.isLoading = true
      this.$v.formData.$touch()
      // console.log('Llego aqui')
      if (this.$v.$invalid) {
        this.isLoading = false
        // console.log('Llego aqui 2')
        return true
      }
      this.$v.formData.country_id.$touch()
      this.$v.formData.billing_state.$touch()

      if (
        this.$v.formData.country_id.$invalid ||
        this.$v.formData.billing_state.$invalid
      ) {
        return true
      }

      let data = {
        country: this.country.code,
        state: this.formData.billing_state.code,
        city: this.formData.city,
        zip_code: this.formData.zip,
      }

      let response = await this.billingValidation(data)

      if (response.data.check.success) {
        let dataModal = [...response.data.check.data]
        dataModal.forEach((element) => {
          element.customerAvalaraLocationId = this.customer_avalara_location_id
          for (const key in element) {

            // validar si hay un campo en null (nulo)
            if (
              (!element[key] || element[key] === '') &&
              key != 'customerAvalaraLocationId'
            ) {
              element.valid = false
              break
            } else {
              element.valid = true
            }
          }
        })
        // Information that the company currently has
        dataModal[0].company_geo_info = {
          country: this.country,
          state: this.formData.billing_state,
          city: this.formData.city,
          zip: this.formData.zip,
        }

        // de traer direcciones invocar modal
        this.openModal({
          title: this.$t('avalara.billing_location_modal.title'),
          componentName: 'AvalaraBillingLocationModal',
          id: this.userId,
          data: dataModal,
          variant: 'lg',
          company: 1,
        })

        this.isLoading = false
        return true

      } else {
        this.isLoading = false
        window.toastr['error'](response.data.check.message)
        return true
      }

    },
    onUploadHandler(cropper) {
      this.previewLogo = cropper
        .getCroppedCanvas()
        .toDataURL(this.cropperOutputMime)
    },
    onUploadHandlerWallpaper(cropper) {
      this.previewWallpaper = cropper
        .getCroppedCanvas()
        .toDataURL(this.cropperOutputMime)
    },
    onHandleUploadError() {
      window.toastr['error']('Something went wrong, please check that the format type, size or dimensions of the image are correct.')
    },
    onChange(file) {
      this.cropperOutputMime = file.type
      this.fileObject = file
    },
    onChangeWallpaper(file) {
      this.cropperOutputMimeWallpaper = file.type
      this.fileObjectWallpaper = file
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
        let userCompany = response.data.user.company
        this.userId = response.data.user.id
        this.formData.name = userCompany.name = userCompany.name

        if (userCompany.company_identifier) {
          this.formData.company_identifier = userCompany.company_identifier
        }
        if (userCompany.title_header) {
          this.formData.title_header = userCompany.title_header
        }

        if (userCompany.subtitle_header) {
          this.formData.subtitle_header = userCompany.subtitle_header
        }
        this.formData.address_street_1 = userCompany.address ? userCompany.address.address_street_1 : ''
        this.formData.address_street_2 = userCompany.address ? userCompany.address.address_street_2 : ''
        this.formData.zip = userCompany.address ? userCompany.address.zip : ''
        this.formData.phone = userCompany.address ? userCompany.address.phone : ''

        this.formData.city = userCompany.address ? userCompany.address.city : ''
        this.formData.county = userCompany.address
          ? userCompany.address.county
          : ''
        this.country = userCompany.address ? userCompany.address.country : ''

        let res2 = await window.axios.get('/api/v1/states/' + this.country.code)

        if (res2) {
          this.billing_states = []
          this.billing_states = res2.data.states
          if (userCompany.address && userCompany.address.state_id) {
            let myDog = this.billing_states.find(
              (state) => userCompany.address.state_id === state.id
            )

            this.billing_state = myDog
            this.formData.billing_state = myDog

          }
        }

        this.previewLogo = userCompany.logo
        this.previewWallpaper = userCompany.wallpaper_login
        this.previewFavicon = userCompany.favicon
      }
      if (response.data.setting) {
        this.formData.company_page_title = response.data.setting.value
      }

      let res = await this.fetchCompanySettings(['header_color', 'primary_color', 'color_invoice', 'footer_text_value', 'footer_url_value', 'footer_url_name', 'current_year', 'company_report_info','hide_title'])

      this.showPicker = false
      if (res.data.hasOwnProperty('company_report_info')) {
        this.formData.company_report_info = res.data.company_report_info ? res.data.company_report_info : ''
      }
      if (res.data.hasOwnProperty('header_color') && res.data.header_color) {
        this.formData.header_color = res.data.header_color
      }
      if (res.data.hasOwnProperty('primary_color') && res.data.primary_color) {
        this.formData.primary_color = res.data.primary_color
      }
      if (res.data.hasOwnProperty('color_invoice') && res.data.color_invoice) {
        this.formData.color_invoice = res.data.color_invoice
      }
      if (res.data.hasOwnProperty('footer_text_value') && res.data.footer_text_value) {
        this.formData.footer_text_value = res.data.footer_text_value
      }
      if (res.data.hasOwnProperty('footer_url_value') && res.data.footer_url_value) {
        this.formData.footer_url_value = res.data.footer_url_value
      }

      if (res.data.hasOwnProperty('footer_url_name') && res.data.footer_url_name) {
        this.formData.footer_url_name = res.data.footer_url_name
      }

      if (res.data.hasOwnProperty('current_year') && res.data.current_year) {
        this.formData.current_year = res.data.current_year == "1" ? true : false
      }

      if (res.data.hasOwnProperty('hide_title') && res.data.hide_title) {
        this.formData.hide_title = res.data.hide_title == "1" ? true : false
      }


      this.showPicker = true

      this.isRequestOnGoing = false
    },
    async subscribeAvalaraBillingInfo() {
      this.unsubscribe = this.$store.subscribe((mutation, state) => {
        if (mutation.type === 'avalara/AVALARA_LOCATION_SAVED') {
          /*console.log(
            'location selected and saved: ',
            this.avalaraLocationSaved
          )*/

          if (this.avalaraLocationSaved) {
            if (this.avalaraLocationSaved.country) {
              /* let country = this.countries.filter(c => c.code === this.avalaraLocationSaved.country)
              // console.log('country', country);
              this.formData.billing.country_id = country.id
              this.$v.formData.billing.country_id.$touch() */
            }
            if (this.avalaraLocationSaved.state) {
              // this.formData.billing.country_id =
            }

            if (this.avalaraLocationSaved.locality) {
              this.formData.city = this.avalaraLocationSaved.locality
            }
            if (this.avalaraLocationSaved.county) {
              this.formData.county = this.avalaraLocationSaved.county
            }
            if (this.avalaraLocationSaved.zip) {
              this.formData.zip = this.avalaraLocationSaved.zip
            }
          }
        }
      })
    },
    async updateCompanyData() {
      this.$v.formData.$touch()
      // console.log('Llego aqui')
      if (this.$v.$invalid) {
        // console.log('Llego aqui 2')
        return true
      }
      this.isLoading = true

      this.formData.header_color = this.$refs.colorPickerHeader.colorData.hexa
      this.formData.primary_color = this.$refs.colorPickerPrimary.colorData.hexa
      this.formData.color_invoice = this.$refs.colorPickerInvoice.colorData.hexa
      this.formData.company_report_info = this.formData.company_report_info

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

        if (this.fileObjectWallpaper && this.previewWallpaper) {
          let wallpaperLogin = new FormData()
          wallpaperLogin.append(
            'url_wallpaper_login',
            JSON.stringify({
              name: this.fileObjectWallpaper.name,
              data: this.previewWallpaper,
            })
          )
          await this.updateCompanyWallpaper(wallpaperLogin)
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
    changedColor(color) {
      //console.log('Changed Color', color)
    },
    changedColorPrimary(color) {
      changeColorPrimaryTailwind(color)
    },
    swatchAdded(color) {
      //console.log('Swatch Added', color)
    },
    swatchDeleted(color) {
      //console.log('Swatch Deleted', color)
    },
    async getStatusModuleAvalara() {

      const modules = [
        "Avalara",
      ]
      const modulesArray = await this.getModules(modules)
      const moduleAvalara = modulesArray.modules.find(element => element.name === 'Avalara')
      if (moduleAvalara && moduleAvalara.status == 'A') {
        const response = await this.checkStatusAvalara()
        this.isAvalaraAvailable = response.data.success
      } else {
        this.isAvalaraAvailable = false
      }
    },


  },
  destroyed() {
    const colorStorage = localStorage.getItem('primary_color') || '#5851D8' // default color
    changeColorPrimaryTailwind(colorStorage)
  }
}
</script>
