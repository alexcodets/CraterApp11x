<template>
  <form @submit.prevent="submitBandwidth" class="relative h-full">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card variant="setting-card">
      <sw-page-header
        class="mb-3"
        :title="pageTitle"
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
            to="/admin/settings/bandwidth"
            :title="$t('bandwidth.bandwidth')"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'bandwidth.edit-config'"
            to="#"
            :title="$t('bandwidth.edit_config')"
            active
          />
          <sw-breadcrumb-item
            v-else
            to="#"
            :title="$t('bandwidth.new_config')"
            active
          />
        </sw-breadcrumb>

        <template slot="actions">
          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="flex justify-center w-full md:w-auto"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ $t('bandwidth.save_config') }}
          </sw-button>
        </template>
      </sw-page-header>

      <div class="grid grid-cols-12">
        <div class="col-span-12">
          <sw-input-group
            :label="$t('bandwidth.account')"
            :error="accountNameError"
            class="mb-4"
            required
          >
            <sw-input
              v-model.trim="formData.account_name"
              :invalid="$v.formData.account_name.$error"
              focus
              type="text"
              :tabindex="1"
              @input="$v.formData.account_name.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('bandwidth.url')"
            :error="urlError"
            class="mb-4"
            required
          >
            <sw-input
              v-model.trim="formData.url"
              :invalid="$v.formData.url.$error"
              type="text"
              :tabindex="2"
              @input="$v.formData.url.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('bandwidth.user')"
            :error="userError"
            class="mb-4"
            required
          >
            <sw-input
              v-model.trim="formData.user"
              :invalid="$v.formData.user.$error"
              type="text"
              :tabindex="3"
              @input="$v.formData.user.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('bandwidth.password')"
            :error="passwordError"
            class="mb-4"
            required
          >
            <sw-input
              v-model.trim="formData.password"
              :invalid="$v.formData.password.$error"
              :type="getInputType"
              :tabindex="4"
              @input="$v.formData.password.$touch()"
            >
              <template v-slot:rightIcon>
                <eye-off-icon
                  v-if="isShowPassword"
                  class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                  @click="isShowPassword = !isShowPassword"
                />
                <eye-icon
                  v-else
                  class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                  @click="isShowPassword = !isShowPassword"
                />
              </template>
            </sw-input>
          </sw-input-group>

          <sw-input-group
            :label="$t('bandwidth.account_id')"
            :error="accountIdError"
            class="mb-4"
            required
          >
            <sw-input
              v-model.trim="formData.account_id"
              :invalid="$v.formData.account_id.$error"
              focus
              type="text"
              :tabindex="1"
              @input="$v.formData.account_id.$touch()"
            />
          </sw-input-group>
        </div>
      </div>

    </sw-card>
  </form>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { EyeIcon, EyeOffIcon } from '@vue-hero-icons/outline'
const {
  required,
  minLength,
  numeric,
  minValue,
  maxLength,
  requiredIf
} = require('vuelidate/lib/validators')

export default {

  components: {
    EyeIcon,
    EyeOffIcon,
  },
  data() {
    return {
      isRequestOnGoing: false,
      isLoading: false,
      formData: {
        account_id: null,
        account_name: null,
        url: null,
        user: null,
        password: null
      },
      isShowPassword: false,
    }
  },

  validations: {
    formData: {
      account_name: {
        required
      },
      account_id: {
        required
      },
      url: {
        required
      },
      user: {
        required
      },
      password: {
        required: requiredIf(function () {
          return !this.isEdit
        }),
        minLength: minLength(4),
      }
    }
  },

  computed: {
    pageTitle() {
      if (this.$route.name === 'bandwidth.edit-config') {
        return this.$t('bandwidth.title_edit')
      }
      return this.$t('bandwidth.title_new')
    },

    isEdit() {
      if (this.$route.name === 'bandwidth.edit-config') {
        return true
      }
      return false
    },

    accountNameError() {
      if (!this.$v.formData.account_name.$error) {
        return ''
      }

      if (!this.$v.formData.account_name.required) {
        return this.$t('validation.required')
      }
    },
    accountIdError() {
      if (!this.$v.formData.account_id.$error) {
        return ''
      }

      if (!this.$v.formData.account_id.required) {
        return this.$t('validation.required')
      }
    },

    urlError() {
      if (!this.$v.formData.url.$error) {
        return ''
      }

      if (!this.$v.formData.url.required) {
        return this.$t('validation.required')
      }
    },

    userError() {
      if (!this.$v.formData.user.$error) {
        return ''
      }

      if (!this.$v.formData.user.required) {
        return this.$t('validation.required')
      }
    },

    passwordError() {
      if (!this.$v.formData.password.$error) {
        return ''
      }
      if (!this.$v.formData.password.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.password.minLength) {
        return this.$tc(
          'validation.password_min_length',
          this.$v.formData.password.$params.minLength.min,
          { count: this.$v.formData.password.$params.minLength.min }
        )
      }
    },

    getInputType() {
      if (this.isShowPassword) {
        return 'text'
      }
      return 'password'
    },
  },

  created() {
    if (this.isEdit) {
      this.loadEditConfig()
    }
  },

  methods: {
    ...mapActions('bandwidth', [
      'addBandwidth',
      'fetchBandwidth',
      'updateBandwidth'
    ]),

    async loadEditConfig() {
      let response = await this.fetchBandwidth(this.$route.params.id)

      if (response.data) {
        this.formData = { ...this.formData, ...response.data.bw_config }
      }
    },

    async submitBandwidth() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return false
      }

      try {
        let response;
        this.isLoading = true;

        if (this.isEdit) {
          response = await this.updateBandwidth(this.formData)
          window.toastr['success'](this.$t('bandwidth.updated_message'))
          this.$router.push('/admin/settings/bandwidth');
        } else {
          response = await this.addBandwidth(this.formData)
          if (response.data.success) {
            window.toastr['success'](this.$t('bandwidth.created_message'))
            this.$router.push('/admin/settings/bandwidth');
          }
        }
      } catch (e) {
        let objectErrors = e.response.data.errors
        if (objectErrors) {
          Object.keys(objectErrors).map((key) => {
            objectErrors[key].map((error) => {
              window.toastr['error'](error)
            })
          })
        } else {
          window.toastr['error'](this.$tc('bandwidth.error_message'))
        }
      } finally {
        this.isLoading = false
      }

    }

  }
}
</script>

