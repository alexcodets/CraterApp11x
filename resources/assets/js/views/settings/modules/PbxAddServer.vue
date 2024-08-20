<template>
  <sw-card variant="setting-card">
    <!-- Base  -->
    <base-page v-if="isSuperAdmin" class="">
      <!--------- Form ---------->
      <form action="" @submit.prevent="submitPBX">
        <!-- Header  -->
        <sw-page-header
          class="mb-3"
          :title="$t('settings.customization.modules.add_server')"
        >
          <sw-breadcrumb slot="breadcrumbs">
            <sw-breadcrumb-item
              to="/admin/dashboard"
              :title="$t('general.home')"
            />
            <sw-breadcrumb-item
              to="/admin/settings/modules"
              :title="$t('settings.customization.modules.title')"
            />
            <sw-breadcrumb-item
              to="/admin/settings/pbx"
              :title="$t('settings.customization.modules.edit_module')"
            />
            <sw-breadcrumb-item
              to="#"
              :title="$t('settings.customization.modules.add_server')"
              active
            />
          </sw-breadcrumb>

          <template slot="actions">
            <sw-button
              :disabled="isLoading || isFormDisabled"
              variant="primary-outline"
              type="button"
              size="lg"
              class="mr-3"
              @click="cancelForm()"
            >
              {{ $t('general.cancel') }}
            </sw-button>

            <sw-button
              :loading="isLoading"
              :disabled="isLoading"
              variant="primary"
              type="submit"
              size="lg"
              class="flex justify-center w-full md:w-auto"
            >
              <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
              {{ $t('settings.customization.modules.save_server') }}
            </sw-button>
          </template>
        </sw-page-header>

        <div class="grid grid-cols-12">
          <div class="col-span-12">
            <sw-card class="mb-8">
              <sw-input-group
                :label="$t('settings.customization.modules.server_label')"
                :error="serverlabelError"
                class="mb-4"
                required
              >
                <sw-input
                  v-model.trim="formData.server_label"
                  :invalid="$v.formData.server_label.$error"
                  class="mt-2"
                  focus
                  type="text"
                  name="server_label"
                  @input="$v.formData.server_label.$touch()"
                  autocomplete="off"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('settings.customization.modules.hostname')"
                :error="hostnameError"
                class="mb-4"
                required
              >
                <sw-input
                  v-model.trim="formData.hostname"
                  :invalid="$v.formData.hostname.$error"
                  class="mt-2"
                  focus
                  type="text"
                  name="hostname"
                  @input="$v.formData.hostname.$touch()"
                  autocomplete="off"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('settings.customization.modules.ssl_port')"
                :error="sslError"
                class="mb-4"
                required
              >
                <sw-input
                  v-model.trim="formData.ssl_port"
                  :invalid="$v.formData.ssl_port.$error"
                  :placeholder="$t('settings.customization.modules.443')"
                  class="mt-2"
                  focus
                  type="text"
                  name="ssl_port"
                  @input="$v.formData.ssl_port.$touch()"
                  autocomplete="off"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('settings.customization.modules.api_key')"
                :error="apikeyError"
                class="mb-4"
                required
              >
                <sw-input
                  v-model.trim="formData.api_key"
                  class="mt-2"
                  focus
                  :type="showApiKey ? 'text' : 'password'"
                  name="api_key"
                  :invalid="$v.formData.api_key.$error"
                  @input="$v.formData.api_key.$touch()"
                  autocomplete="off"
                >
                  <template v-slot:rightIcon>
                    <eye-off-icon
                      v-if="showApiKey"
                      class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="showApiKey = !showApiKey"
                    />
                    <eye-icon
                      v-else
                      class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="showApiKey = !showApiKey"
                    />
                  </template>
                </sw-input>
              </sw-input-group>

              <sw-input-group
                :label="$t('tax_groups.country')"
                class="md:col-span-3 mb-4"
                :error="countryError"
              >
                <sw-select
                  v-model="formData.countries"
                  :options="countries"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :tabindex="8"
                  :placeholder="$t('general.select_country')"
                  label="name"
                  track-by="id"
                  @change="countrySeleted"
                  :invalid="$v.formData.countries.$error"
                  @input="$v.formData.countries.$touch()"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('settings.customization.modules.timezone')"
                class="md:col-span-3 mb-4"
                required
              >
                <div class="flex flex-wrap">
                  <div class="mb-2 md:mb-0 w-full md:w-1/2">
                    <sw-select
                      v-model="continent"
                      :options="continentOptions"
                      :searchable="true"
                      :show-labels="false"
                      :allow-empty="true"
                      :tabindex="8"
                      :placeholder="
                        $t(
                          'settings.customization.modules.place_select_continent'
                        )
                      "
                      class="md:mr-2"
                      :invalid="$v.formData.timezone.$error"
                    />
                  </div>

                  <div class="mt-2 md:mt-0 w-full md:w-1/2">
                    <sw-select
                      v-model="timezone"
                      :options="timezonesOptiones"
                      :searchable="true"
                      :show-labels="false"
                      :allow-empty="true"
                      :tabindex="8"
                      :placeholder="
                        $t('settings.customization.modules.please_select_zone')
                      "
                      label="label"
                      class="md:ml-2"
                      :invalid="$v.formData.timezone.$error"
                      @input="$v.formData.timezone.$touch()"
                    />
                  </div>
                </div>
              </sw-input-group>

              <!-- select con checklist -->
              <sw-input-group
                :label="$t('settings.customization.modules.status')"
                class="md:col-span-3 mb-4"
              >
                <sw-select :options="[]" :searchable="false" placeholder="">
                  <template v-slot:selection>
                    <p>{{ labelStatusCrp }}</p>
                  </template>
                  <!-- template beforeList -->
                  <template v-slot:beforeList>
                    <div class="flex flex-wrap">
                      <sw-checkbox
                        v-for="(item, index) in cdrStatusOptions"
                        :key="index"
                        v-model="formData.cdrStatus"
                        variant="danger"
                        :label="item.label"
                        :value="item.value"
                        class="w-full p-2 px-4 hover:bg-gray-100"
                        @change="changeStatus"
                      />
                    </div>
                  </template>
                </sw-select>
              </sw-input-group>

              <sw-input-group
                :label="
                  $t('settings.customization.modules.national_dialing_code')
                "
                :error="nationalError"
                class="mb-4"
                required
              >
                <sw-input
                  v-model.trim="formData.national_dialing_code"
                  :invalid="$v.formData.national_dialing_code.$error"
                  class="mt-2"
                  focus
                  type="text"
                  name="national_dialing_code"
                  @input="$v.formData.national_dialing_code.$touch()"
                  autocomplete="off"
                />
              </sw-input-group>

              <sw-input-group
                :label="
                  $t(
                    'settings.customization.modules.international_dialing_code'
                  )
                "
                :error="internationalError"
                class="mb-4"
                required
              >
                <sw-input
                  v-model.trim="formData.international_dialing_code"
                  :invalid="$v.formData.international_dialing_code.$error"
                  class="mt-2"
                  focus
                  type="text"
                  name="international_dialing_code"
                  @input="$v.formData.international_dialing_code.$touch()"
                  autocomplete="off"
                />
              </sw-input-group>
            </sw-card>
          </div>
        </div>
      </form>
    </base-page>
  </sw-card>
</template>

<script>
import draggable from 'vuedraggable'
import { mapActions, mapGetters } from 'vuex'
import {
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
  EyeOffIcon,
  EyeIcon,
} from '@vue-hero-icons/solid'
const { required, minLength, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    draggable,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
    EyeOffIcon,
    EyeIcon,
  },
  data() {
    return {
      showApiKey: false,
      isLoading: false,
      title: 'Add Server',
      cdrStatusOptions: [
        {
          label: 'ALL',
          value: 0,
          color: 'dark',
        },
        {
          label: 'Failed',
          value: 1,
          color: 'danger',
        },
        {
          label: 'Busy',
          value: 2,
          color: 'warning',
        },
        {
          label: 'Unanswered',
          value: 4,
          color: 'primary',
        },
        {
          label: 'Answered',
          value: 8,
          color: 'success',
        },
      ],
      countries: [],
      status: [
        {
          value: 'A',
          text: 'Active',
        },
        {
          value: 'I',
          text: 'Inactive',
        },
      ],
      continentOptions: [],
      timezonesOptiones: [],
      continent: '',
      timezone: '',

      formData: {
        company_id: '1',
        creator_id: '1',
        country_id: '',
        server_label: '',
        hostname: '',
        ssl_port: '',
        api_key: '',
        national_dialing_code: '',
        international_dialing_code: '',
        country_id: '',
        country_name: '',
        countries: [],
        status: '',
        timezone: '',
        cdrStatus: [8],
      },
    }
  },
  validations: {
    formData: {
      server_label: {
        required,
        minLength: minLength(1),
        maxLength: maxLength(255),
      },
      hostname: {
        required,
        minLength: minLength(1),
        maxLength: maxLength(255),
      },
      ssl_port: {
        required,
        maxLength: maxLength(5),
      },
      countries: {
        required,
      },
      api_key: {
        required,
        minLength: minLength(5),
        maxLength: maxLength(255),
      },
      national_dialing_code: {
        required,
        maxLength: maxLength(255),
      },
      international_dialing_code: {
        required,
        maxLength: maxLength(255),
      },
      timezone: {
        required,
      },
    },
  },
  computed: {
    ...mapGetters('user', ['currentUser']),

    ...mapGetters('company', ['defaultCurrency']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    pageTitle() {
      if (this.$route.name === 'tax-groups.edit') {
        return this.$t('tax_groups.edit_tax_group')
      }
      return this.$t('tax_groups.new_tax_group')
    },

    serverlabelError() {
      if (!this.$v.formData.server_label.$error) {
        return ''
      }

      if (!this.$v.formData.server_label.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.server_label.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.server_label.$params.minLength.min,
          { count: this.$v.formData.server_label.$params.minLength.min }
        )
      }

      if (!this.$v.formData.server_label.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
    hostnameError() {
      if (!this.$v.formData.hostname.$error) {
        return ''
      }

      if (!this.$v.formData.hostname.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.hostname.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.hostname.$params.minLength.min,
          { count: this.$v.formData.hostname.$params.minLength.min }
        )
      }

      if (!this.$v.formData.hostname.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
    sslError() {
      if (!this.$v.formData.ssl_port.$error) {
        return ''
      }

      if (!this.$v.formData.ssl_port.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.ssl_port.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
    apikeyError() {
      if (!this.$v.formData.api_key.$error) {
        return ''
      }

      if (!this.$v.formData.api_key.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.api_key.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.api_key.$params.minLength.min,
          { count: this.$v.formData.api_key.$params.minLength.min }
        )
      }

      if (!this.$v.formData.api_key.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
    countryError() {
      if (!this.$v.formData.countries.$error) {
        return ''
      }

      if (!this.$v.formData.countries.required) {
        return this.$t('validation.required')
      }

    },
    nationalError() {
      if (!this.$v.formData.national_dialing_code.$error) {
        return ''
      }

      if (!this.$v.formData.national_dialing_code.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.national_dialing_code.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
    internationalError() {
      if (!this.$v.formData.international_dialing_code.$error) {
        return ''
      }

      if (!this.$v.formData.international_dialing_code.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.international_dialing_code.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
    labelStatusCrp() {
      const statusLabelArr = this.formData.cdrStatus.map((item) => {
        return this.cdrStatusOptions.find((status) => status.value === item)
          .label
      })
      // si inlcluye en 0 muestra All
      if (statusLabelArr.includes('ALL')) {
        return 'All'
      } else {
        return statusLabelArr.join(' - ')
      }
    },
  },
  watch: {
    continent(val) {
      this.listZoneByContinent(val)
      this.formData.timezone = ''
      this.timezone = ''
    },
    timezone(val) {
      this.formData.timezone = val.value
    },
  },
  created() {
    this.fetchInitDataCountry()
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
    this.listContienents()

  },
  mounted() {
    this.$v.formData.$reset()
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('modules', [
      'addServer',
      'getContienents',
      'getZoneByContinent',
      'testServerConection',
    ]),

    async fetchInitDataCountry() {
      this.initLoad = true
      let res = await window.axios.get('/api/v1/countries')
      if (res) {
        this.countries = res.data.countries
      }
      this.initLoad = false
    },

    async countrySeleted(val) {
      let res = await window.axios.get('/api/v1/states/' + val.code)
      if (res) {
        this.states = res.data.states
      }
      this.formData.countries = val
    },

    async submitPBX() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      /* 
      this.formData.server_label = this.formData.server_label
      this.formData.hostname = this.formData.hostname
      this.formData.ssl_port = this.formData.ssl_port
      this.formData.api_key = this.formData.api_key */

      if (this.formData.countries) {
        this.formData.country_id = this.formData.countries.id
        this.formData.country_name = this.formData.countries.name
      }

      /* this.formData.national_dialing_code = this.formData.national_dialing_code
      this.formData.international_dialing_code =
        this.formData.international_dialing_code */

      this.formData.status = 'I'
      let text = 'settings.customization.modules.servidor_creation'

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc(text),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          try {
            let res
            this.isLoading = true

            res = await this.addServer(this.formData)

            this.isLoading = false
            window.toastr['success']('Server created successfully')
            this.$t('tax_groups.updated_message')
            this.$router.push('/admin/settings/pbx')
            return true
          } catch (error) {
            window.toastr['error'](error.response.data.response)
            this.status = [
              {
                value: 'A',
                text: 'Active',
              },
              {
                value: 'I',
                text: 'Inactive',
              },
              {
                value: 'R',
                text: 'Restricted',
              },
            ]
            this.formData.status = {
              value: 'I',
              text: 'Inactive',
            }
            this.isLoading = false
            return false
          }
        }
      })
    },
    // Find all continents
    async listContienents() {
      let response = await this.getContienents()
      if (response.success) {
        this.continentOptions = response.contienents
      }
    },

    cancelForm() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.cancel_text'),
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          this.$router.go(-1)
        }
      })
    },

    // Find zones for continent
    async listZoneByContinent(continent) {
      let response = await this.getZoneByContinent(continent)
      if (response.success) {
        this.timezonesOptiones = response.timezones
      }
    },
    changeStatus(status) {
      if (status.includes(0)) {
        const statusArr = this.cdrStatusOptions.map((item) => item.value)
        this.formData.cdrStatus = statusArr
      }
    },
  },
}
</script>

<style scoped>
</style>