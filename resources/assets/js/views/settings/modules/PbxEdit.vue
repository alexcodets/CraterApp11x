<template>
  <sw-card variant="setting-card">
    <!-- Base  -->
    <base-page v-if="isSuperAdmin" class="">
      <!--------- Form ---------->
      <form action="" @submit.prevent="submitPBX">
        <!-- Header  -->
        <sw-page-header
          class="mb-3"
          :title="$t('settings.customization.modules.edit_server')"
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
              {{ $t('settings.customization.modules.update_server') }}
            </sw-button>

            <sw-button
              :loading="isLoadingTest"
              :disabled="isLoadingTest"
              @click="testConection($route.params.id)"
              variant="primary"
              type="button"
              size="lg"
              class="flex justify-center ml-4 w-full md:w-auto"
            >
              <!-- {{ $t('settings.customization.modules.update_server') }} -->
              {{ $t('settings.customization.modules.test_conection') }}
            </sw-button>

            <sw-button
              :disabled="isLoading"
              @click="removePbxServer($route.params.id)"
              variant="primary"
              type="button"
              size="lg"
              class="flex justify-center ml-4 w-full md:w-auto"
            >
              <!-- {{ $t('settings.customization.modules.update_server') }} -->
              {{ $t('general.delete') }}
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
                  class="mt-2"
                  focus
                  type="text"
                  name="server_label"
                  @input="$v.formData.server_label.$touch()"
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
                  class="mt-2"
                  focus
                  type="text"
                  name="hostname"
                  @input="$v.formData.hostname.$touch()"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('settings.customization.modules.ssl_port')"
                :error="sslError"
                class="mb-4"
              >
                <sw-input
                  v-model.trim="formData.ssl_port"
                  :placeholder="$t('settings.customization.modules.443')"
                  class="mt-2"
                  focus
                  type="text"
                  name="ssl_port"
                  @input="$v.formData.ssl_port.$touch()"
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
                  @input="$v.formData.api_key.$touch()"
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
                  @select="countrySeleted"
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
                      @select="selectContinent"
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
                  class="mt-2"
                  focus
                  type="text"
                  name="national_dialing_code"
                  @input="$v.formData.national_dialing_code.$touch()"
                />
              </sw-input-group>

              <!-- select con checklist -->
              <sw-input-group
                :label="$t('settings.customization.modules.status')"
                class="md:col-span-3 mb-4"
              >
                <sw-select :options="[]" :searchable="false" placeholder="">
                  <template v-slot:selection>
                    <p>{{ labelStatusCdr }}</p>
                  </template>
                  <!-- template beforeList -->
                  <template v-slot:beforeList>
                    <div class="flex flex-wrap">
                      <sw-checkbox
                        v-for="(item, index) in cdrStatusOptions"
                        :key="index"
                        v-model="formData.cdrStatus"
                        :variant="item.color"
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
                  class="mt-2"
                  focus
                  type="text"
                  name="international_dialing_code"
                  @input="$v.formData.international_dialing_code.$touch()"
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
  ShieldCheckIcon,
  BanIcon,
} from '@vue-hero-icons/solid'
const { required, minLength, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    BanIcon,
    draggable,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
    EyeOffIcon,
    EyeIcon,
    ShieldCheckIcon,
  },
  data() {
    return {
      showApiKey: false,
      isLoading: false,
      isLoadingTest: false,
      countries: [],
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
        id: '',
        company_id: '',
        creator_id: '',
        country_id: '',
        server_label: '',
        hostname: '',
        ssl_port: '',
        api_key: '',
        national_dialing_code: '',
        international_dialing_code: '',
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
        maxLength: maxLength(5),
      },
      api_key: {
        required,
        minLength: minLength(5),
        maxLength: maxLength(255),
      },
      national_dialing_code: {
        maxLength: maxLength(255),
      },
      international_dialing_code: {
        maxLength: maxLength(255),
      },
      timezone: {
        required,
      },
    },
  },
  watch: {
    timezone(val) {
      this.formData.timezone = val.value
    },
  },

  computed: {
    ...mapGetters('user', ['currentUser']),

    ...mapGetters('company', ['defaultCurrency']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    pageTitle() {
      if (this.$route.name === 'pbx.edit') {
        return this.$t('settings.customization.modules.edit_server')
      }
      return this.$t('settings.customization.modules.add_server')
    },

    isEdit() {
      if (this.$route.name === 'pbx.edit') {
        return true
      }
      return false
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
    labelStatusCdr() {
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

  created() {
    this.fetchInitDataCountry()
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
    if (this.isEdit) {
      this.loadEditPbxServer()
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
      'fetchPbxServer',
      'updatePbxServer',
      'deletePbxServer',
      'getContienents',
      'getZoneByContinent',
      'testServerConection',
    ]),

    // Remove Register
    async removePbxServer(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.customization.modules.server_confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.deletePbxServere(id)
          if (response.data.success) {
            window.toastr['success'](
              this.$t('settings.customization.modules.server_deleted_message')
            )
            // Redirect Index
            this.$router.push('/admin/settings/pbx')
            return true
          }
        }
      })
    },

    // Remove Register
    async deletePbxServere(id) {
      try {
        let response = {}
        let res = await this.deletePbxServer(id)
        if (res) {
          response = {
            data: {
              success: true,
            },
          }
        }
        return response
      } catch (err) {
        ///window.toastr['error'](err.message)
      }
    },

    async loadEditPbxServer() {
      if (this.isEdit) {
        let response = await this.fetchPbxServer(this.$route.params.id)

        // load timezone
        if (response.data.pbxServer.timezone) {
          const labelArr = response.data.pbxServer.timezone.split('/')
          this.continent = labelArr[0]
          let label = `${labelArr[1]} ${
            labelArr.length > 2 ? ' - ' + labelArr[2] : ''
          }`
          this.timezone = {
            label: label,
            value: response.data.pbxServer.timezone,
          }
        }
        // load cdrStatus
        this.formData.cdrStatus = response.data.pbxServer.cdr_status.map(
          (item) => item.status
        )

        this.formData = { ...this.formData, ...response.data.pbxServer }
        this.formData.countries = {
          code: response.data.pbxServer.country_code,
          id: response.data.pbxServer.country_id,
          name: response.data.pbxServer.country_name,
          phonecode: response.data.pbxServer.country_phonecode,
        }

        if (response.data.pbxServer.country_code) {
          let res = await window.axios.get(
            '/api/v1/states/' + response.data.pbxServer.country_code
          )
          if (res) {
            this.states = res.data.states
          }
        }
      }
    },

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

    async testConection(serverId = null) {
      if (serverId) {
        this.isLoadingTest = true
        let res = await this.testServerConection(serverId)
        if (res.success) {
          window.toastr['success']('Server is alive')
        } else {
          window.toastr['error']('Server not found')
        }
        this.isLoadingTest = false
      }
      return
    },

    async submitPBX() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.formData.server_label = this.formData.server_label
      this.formData.hostname = this.formData.hostname
      this.formData.ssl_port = this.formData.ssl_port
      this.formData.api_key = this.formData.api_key

      if (this.formData.countries) {
        this.formData.country_id = this.formData.countries.id
        this.formData.country_name = this.formData.countries.name
      }

      this.formData.national_dialing_code = this.formData.national_dialing_code
      this.formData.international_dialing_code =
        this.formData.international_dialing_code

      let text = 'settings.customization.modules.servidor_edit'

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc(text),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          try {
            let response
            this.isLoading = true
            if (this.isEdit) {
              this.formData.id = this.$route.params.id

              response = await this.updatePbxServer(this.formData)

              let res = await this.testServerConection(this.$route.params.id)

              if (response.data.success) {
                window.toastr['success'](
                  this.$t('settings.customization.modules.updated_message')
                )

                if (res.success) {
                  window.toastr['success']('Server is alive')
                } else {
                  window.toastr['error']('Server not found')
                }

                this.$router.push('/admin/settings/pbx')
              }
              if (response.data.error) {
                this.isLoading = false
                window.toastr['error'](response.data.error)
                return true
              }
            }
          } catch (err) {
            this.isLoading = false
            return false
          }
        }
      })
    },
    // metodo para traer todos los contienentes
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

    // metodo para traer todas las zonas por continente
    async listZoneByContinent(continent) {
      let response = await this.getZoneByContinent(continent)
      if (response.success) {
        this.timezonesOptiones = response.timezones
      }
    },
    selectContinent(val) {
      this.listZoneByContinent(val)
      this.formData.timezone = ''
      this.timezone = ''
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