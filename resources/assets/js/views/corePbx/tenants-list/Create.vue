<template>
  <div>
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <div class="flex justify-end -mb-7">
      <sw-button
        tag-name="router-link"
        :to="`/admin/corePBX/tenant/tenants-list`"
        variant="primary-outline"
      >
        {{ $t('general.go_back') }}
      </sw-button>
    </div>
    <sw-page-header class="mb-5" :title="$tc('corePbx.tenants.new_tenant')" />

    <sw-card>
      <!--Form-->
      <form action="" @submit.prevent="submitTenant">
        <div class="flex justify-end">
          <label
            class="text-sm leading-snug text-gray-900 cursor-pointer"
            @click="clearAll"
          >
            {{ $t('general.clear_all') }}</label
          >
        </div>
        <div class="mb-5 col-span-6">
          <sw-input-group
            :label="$t('corePbx.tenants.pbx_server')"
            class="col-span-6"
            :error="pbxServerError"
            required
          >
            <sw-select
              v-model.trim="formData.pbxServer"
              @select="selectServerPbxMetod"
              :options="serverPbxOptions"
              :invalid="$v.formData.pbxServer.$error"
              :searchable="true"
              :show-labels="false"
              :tabindex="2"
              :allow-empty="true"
              label="server_label"
              track-by="id"
              @input="$v.formData.pbxServer.$touch()"
            />
          </sw-input-group>
        </div>
        <div class="grid grid-cols-12 gap-4 gap-y-6">
          <sw-input-group
            :label="$t('corePbx.tenants.package')"
            :error="packageError"
            class="col-span-12 md:col-span-3"
            required
          >
            <sw-select
              v-model.trim="formData.package"
              :disabled="!formData.pbxServer"
              :options="packagesOptions"
              :invalid="$v.formData.package.$error"
              :searchable="true"
              :show-labels="false"
              :tabindex="2"
              :allow-empty="true"
              :placeholder="$t('packages.select_package')"
              label="name"
              track-by="code"
              @input="$v.formData.package.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('corePbx.tenants.country')"
            class="col-span-12 md:col-span-3"
            :error="countryError"
            required
          >
            <sw-select
              v-model.trim="formData.country"
              :disabled="!formData.pbxServer"
              :invalid="$v.formData.country.$error"
              :options="countryOptions"
              :searchable="true"
              :show-labels="false"
              :tabindex="2"
              :allow-empty="true"
              :placeholder="$t('general.select_country')"
              label="name"
              track-by="code"
              @input="$v.formData.country.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('corePbx.tenants.international')"
            class="col-span-12 md:col-span-3"
            :error="internationalError"
            required
          >
            <sw-input
              v-model.trim="formData.international"
              :placeholder="$t('corePbx.tenants.international')"
              :invalid="$v.formData.international.$error"
              :disabled="!formData.pbxServer"
              type="text"
              tabindex="1"
              placer
              @input="$v.formData.international.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('corePbx.tenants.national')"
            class="col-span-12 md:col-span-3"
            :error="nationalError"
            required
          >
            <sw-input
              v-model.trim="formData.national"
              :placeholder="$t('corePbx.tenants.national')"
              :invalid="$v.formData.national.$error"
              :disabled="!formData.pbxServer"
              type="text"
              tabindex="1"
              placer
              @input="$v.formData.national.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('corePbx.tenants.name')"
            :error="nameError"
            class="col-span-12 md:col-span-4"
            required
          >
            <sw-input
              v-model.trim="formData.tenant_name"
              :invalid="$v.formData.tenant_name.$error"
              :placeholder="$t('items.name')"
              :disabled="!formData.pbxServer"
              focus
              type="tex!"
              name="name"
              tabindex="1"
              placer
              @input="$v.formData.tenant_name.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('corePbx.tenants.code')"
            class="col-span-12 md:col-span-4"
            :error="codeError"
            required
          >
            <sw-input
              v-model.trim="formData.tenant_code"
              :placeholder="$t('corePbx.tenants.code')"
              :invalid="$v.formData.tenant_code.$error"
              :disabled="!formData.pbxServer"
              type="text"
              tabindex="1"
              placer
              @input="$v.formData.tenant_code.$touch()"
            />
          </sw-input-group>

          <div class="col-span-12 md:col-span-4">
            <sw-input-group
              :label="$t('corePbx.tenants.ext_length')"
              :error="ExtLengthError"
              required
            >
              <sw-input
                v-model.trim="formData.ext_length"
                :placeholder="$t('corePbx.tenants.ext_length')"
                :disabled="!formData.pbxServer"
                :invalid="$v.formData.ext_length.$error"
                type="number"
                :min="2"
                :max="16"
                tabindex="1"
                placer
                @input="$v.formData.ext_length.$touch()"
              />
            </sw-input-group>
          </div>
        </div>

        <div class="flex flex-wrap items-center mt-8">
          <sw-button
            variant="primary-outline"
            type="button"
            size="lg"
            class="w-full md:w-auto mb-2 md:mb-0"
            @click="cancelForm()"
          >
            <x-circle-icon class="w-6 h-6 mr-1 -ml-2" />
            {{ $t('general.cancel') }}
          </sw-button>

          <sw-button
            :loading="isLoading"
            type="submit"
            variant="primary"
            size="lg"
            class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          >
            <save-icon class="w-6 h-6 mr-1 -ml-2 mr-2" v-if="!isLoading" />
            {{ $t('corePbx.tenants.save_tenants_button') }}
          </sw-button>
        </div>
      </form>
    </sw-card>
  </div>
</template>

<script>
/*IMPORT COMPONENTS*/
import RightArrow from '@/components/icon/RightArrow'
import MoreIcon from '@/components/icon/MoreIcon'
import LeftArrow from '@/components/icon/LeftArrow'
import draggable from 'vuedraggable'
import { mapActions, mapGetters } from 'vuex'
import {
  TrashIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
/*VALIDATORS*/
const {
  required,
  minLength,
  numeric,
  minValue,
  maxLength,
  maxValue,
} = require('vuelidate/lib/validators')
/*EXPORT DEFAULT*/
export default {
  components: {
    draggable,
    MoreIcon,
    TrashIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusIcon,
    RightArrow,
    LeftArrow,
    XCircleIcon,
  },

  data() {
    return {
      showSelect: false,
      isRequestOnGoing: false,
      isLoading: false,
      serverPbxOptions: [],
      packagesOptions: [],
      countryOptions: [],
      Selection: [
        { value: 'A', text: 'Active' },
        { value: 'I', text: 'Inactive' },
        { value: 'R', text: 'Restricted' },
      ],
      formData: {
        pbxServer: '',
        tenant_name: '',
        tenant_code: '',
        ext_length: 2,
        international: '',
        national: '',
        package: '',
        country: '',
      },
    }
  },

  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),
    pageTitle() {
      if (this.isEdit) {
        return this.$t('corePbx.extensions.edit_extension')
      } else if (this.isCopy) {
        return this.$t('corePbx.extensions.copy_extension')
      }
      return this.$t('corePbx.extensions.new_extension')
    },
    isEdit() {
      if (this.$route.name === 'corepbx.extensions.edit') {
        return true
      }
      return false
    },
    isCopy() {
      if (this.$route.name === 'corepbx.extensions.copy') {
        return true
      }
      return false
    },
    pbxServerError() {
      if (!this.$v.formData.pbxServer.$error) {
        return ''
      }
      if (!this.$v.formData.pbxServer.required) {
        return this.$t('validation.required')
      }
    },
    packageError() {
      if (!this.$v.formData.package.$error) {
        return ''
      }
      if (!this.$v.formData.package.required) {
        return this.$t('validation.required')
      }
    },
    countryError() {
      if (!this.$v.formData.country.$error) {
        return ''
      }
      if (!this.$v.formData.country.required) {
        return this.$t('validation.required')
      }
    },
    internationalError() {
      if (!this.$v.formData.international.$error) {
        return ''
      }
      if (!this.$v.formData.international.numeric) {
        return this.$tc('validation.numbers_only')
      }
      if (!this.$v.formData.international.required) {
        return this.$t('validation.required')
      }
    },
    nationalError() {
      if (!this.$v.formData.national.$error) {
        return ''
      }
      if (!this.$v.formData.national.numeric) {
        return this.$tc('validation.numbers_only')
      }
      if (!this.$v.formData.national.required) {
        return this.$t('validation.required')
      }
    },
    nameError() {
      if (!this.$v.formData.tenant_name.$error) {
        return ''
      }
      if (!this.$v.formData.tenant_name.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.tenant_name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.tenant_name.$params.minLength.min,
          { count: this.$v.formData.tenant_name.$params.minLength.min }
        )
      }
    },
    codeError() {
      if (!this.$v.formData.tenant_code.$error) {
        return ''
      }
      if (!this.$v.formData.tenant_code.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.tenant_code.numeric) {
        return this.$tc('validation.numbers_only')
      }
      if (
        !this.$v.formData.tenant_code.min ||
        !this.$v.formData.tenant_code.max
      ) {
        return this.$tc('validation.min_max_number_tenant_code')
      }
    },
    ExtLengthError() {
      if (!this.$v.formData.ext_length.$error) {
        return ''
      }
      if (!this.$v.formData.ext_length.required) {
        return this.$t('validation.required')
      }
      if (
        !this.$v.formData.ext_length.min ||
        !this.$v.formData.ext_length.max
      ) {
        return this.$tc('validation.min_max_number_tenant_ext_length')
      }
    },
  },

  methods: {
    /**MODULES ENDPOINTS**/
    ...mapActions('tenants', [
      'fetchPbxServices',
      'fetchPbxServerPackages',
      'fetchPbxServerCountry',
      'addTenants',
    ]),

    async PbxServer() {
      const params = { limit: 100000, status: 'A' }

      let res = await this.fetchPbxServices(params)
      if (res) {
        this.serverPbxOptions = res.pbxServers.data
      }
    },

    async selectServerPbxMetod(server) {
      this.packagesOptions = []
      this.formData.package = ''
      this.formData.country = ''

      let dataPackagesOptions = await this.fetchPbxServerPackages(server.id)
      this.packagesOptions = Object.entries(dataPackagesOptions.data).map(
        ([key, value]) => {
          return { code: key, name: value }
        }
      )

      let dataCountryOptions = await this.fetchPbxServerCountry(server.id)
      this.countryOptions = await this.transformedData(dataCountryOptions.data)
    },

    async transformedData(data) {
      let newDate = await Object.keys(data).map((key) => ({
        code: key,
        name: data[key][1],
      }))
      return newDate
    },

    transformedDataError(data) {
      if (typeof data === 'string') return []
      const values = Object.values(data)

      const flatValues = values.flat()

      return flatValues
    },

    async submitTenant() {
      try {
        this.$v.formData.$touch()
        if (this.$v.$invalid) {
          return true
        }

        this.isLoading = true
        const data = {
          tenant_name: this.formData.tenant_name,
          tenant_code: this.formData.tenant_code,
          package: this.formData.package.code,
          ext_length: this.formData.ext_length,
          country: this.formData.country.code,
          national: this.formData.national,
          international: this.formData.international,
          pbxServerId: this.formData.pbxServer.id,
        }

        let res = await this.addTenants(data)
        if (res.status == 200) {
          window.toastr['success'](this.$t('corePbx.tenants.add_tenants'))
          this.$router.push('/admin/corePBX/tenant/tenants-list')
        }
        this.isLoading = false
      } catch (error) {
        this.isLoading = false
        //console.log('error =', error.response.data)
        if (error.response.data.error) {
          const errorsShow = this.transformedDataError(
            error.response.data.error
          )
          errorsShow.forEach((element) => {
            window.toastr['error'](element)
          })
        } else {
          window.toastr['error'](error.response.data.message)
        }
      } finally {
        this.isLoading = false
      }
    },

    clearAll() {
      this.formData = {
        pbxServer: '',
        tenant_name: '',
        tenant_code: '',
        ext_length: 2,
        international: '',
        national: '',
        package: '',
        country: '',
      }
    },
    /**FUNCTIONS**/
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
  },
  mounted() {
    this.$v.formData.$reset()
    if (this.isEdit || this.isCopy) {
      this.isRequestOnGoing = true
    }
  },

  created() {
    this.PbxServer()
  },

  validations: {
    formData: {
      pbxServer: {
        required,
      },
      package: {
        required,
      },
      country: {
        required,
      },
      international: {
        required,
        numeric,
        min: minValue(1),
      },
      national: {
        required,
        numeric,
        min: minValue(1),
      },
      tenant_name: {
        required,
      },
      tenant_code: {
        required,
        numeric,
        min: minValue(200),
        max: maxValue(999),
      },
      ext_length: {
        required,
        min: minValue(2),
        max: maxValue(16),
      },
    },
  },
}
</script>
