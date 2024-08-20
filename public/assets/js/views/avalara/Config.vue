<template>
  <base-page class="item-create">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-page-header :title="this.$t('avalara.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="/admin/dashboard" />
        <sw-breadcrumb-item :title="$t('avalara.title')" to="#" />
        <sw-breadcrumb-item :title="$t('avalara.config')" to="#" active />
      </sw-breadcrumb>
    </sw-page-header>
    <div class="grid grid-cols-12">
      <div class="col-span-12 md:col-span-8">
        <form action="" @submit.prevent="submitAvalara">
          <sw-card>
            <!-- Conexion Type -->
            <div class="flex mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="production"
                  class="absolute"
                  style="top: -18px"
                  @change="setConexion"
                />
              </div>

              <div class="ml-15">
                <p
                  class="p-0 mb-1 text-base leading-snug text-black"
                  v-text="conexion"
                />
                <p
                  class="p-0 m-0 text-xs leading-tight text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('avalara.conexion_description') }}
                </p>
              </div>
            </div>
            <!-- User Name -->
            <sw-input-group
              :label="$t('avalara.user_name')"
              :error="userNameError"
              class="mb-4"
              required
            >
              <sw-input
                v-model.trim="formData.user_name"
                :invalid="$v.formData.user_name.$error"
                class="mt-2"
                focus
                type="text"
                @input="$v.formData.user_name.$touch()"
              />
            </sw-input-group>

            <!-- Password -->
            <sw-input-group
              :label="$tc('general.password')"
              :error="passwordError"
              required
              class="mb-4"
            >
              <sw-input
                v-model="formData.password"
                :invalid="$v.formData.password.$error"
                type="password"
                class="mt-2"
                @input="$v.formData.password.$touch()"
              />
            </sw-input-group>

            <!-- Client -->
            <sw-input-group
              :label="$t('avalara.client_id')"
              :error="clientError"
              horizontal
              required
              class="mb-4"
            >
              <sw-input
                v-model="formData.client_id"
                :invalid="$v.formData.client_id.$error"
                type="text"
                class="mt-2"
                @input="$v.formData.client_id.$touch()"
              />
            </sw-input-group>

            <!-- Url -->
            <sw-input-group
              label="Url"
              :error="urlError"
              horizontal
              required
              class="mb-4"
            >
              <sw-input
                v-model="formData.url"
                :invalid="$v.formData.url.$error"
                type="url"
                class="mt-2"
                @input="$v.formData.url.$touch()"
              />
            </sw-input-group>

            <!-- Host -->
            <sw-input-group
              label="Host"
              :error="hostError"
              horizontal
              required
              class="mb-4"
            >
              <sw-input
                v-model="formData.host"
                :invalid="$v.formData.host.$error"
                type="text"
                class="mt-2"
                @input="$v.formData.host.$touch()"
              />
            </sw-input-group>

            <div class="mt-6 mb-4">
              <sw-button
                :loading="isLoading"
                variant="primary"
                type="submit"
                size="lg"
                class="flex justify-center w-full md:w-auto"
              >
                <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
                {{ isEdit ? $t('general.edit') : $t('general.save') }}
              </sw-button>
            </div>
          </sw-card>
        </form>
      </div>
    </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const {
  required,
  minLength,
  url,
  email,
  numeric,
  minValue,
  maxLength,
  requiredIf,
} = require('vuelidate/lib/validators')
export default {
  data() {
    return {
      isRequestOnGoing: false,
      production: false, // false is Sandbox
      isLoading: false,
      conexion: this.$t('avalara.conexion_sandbox'),
      formData: {
        conexion: 'sandbox',
        user_name: '',
        password: null,
        client_id: null,
        url: null,
        host: null,
      },
    }
  },
  watch: {},
  computed: {
    isEdit() {
      if (this.$route.name === 'connection-data.edit') {
        return true
      }
      return false
    },
    userNameError() {
      if (!this.$v.formData.user_name.$error) {
        return ''
      }
      if (!this.$v.formData.user_name.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.user_name.minLength) {
        return this.$tc(
          'validation.min_length',
          this.$v.formData.user_name.$params.minLength.min,
          { count: this.$v.formData.user_name.$params.minLength.min }
        )
      }
    },
    passwordError() {
      if (!this.$v.formData.password.$error) {
        return ''
      }
      if (!this.$v.formData.password.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.password.minLength) {
        return this.$tc(
          'validation.password_min_length',
          this.$v.formData.password.$params.minLength.min,
          { count: this.$v.formData.password.$params.minLength.min }
        )
      }
    },
    clientError() {
      if (!this.$v.formData.client_id.$error) {
        return ''
      }
      if (!this.$v.formData.client_id.required) {
        return this.$t('validation.required')
      }
    },
    urlError() {
      if (!this.$v.formData.url.$error) {
        return ''
      }
      if (!this.$v.formData.url.url) {
        return this.$t('validation.invalid_url')
      }
      if (!this.$v.formData.url.required) {
        return this.$t('validation.required')
      }
    },
    hostError() {
      if (!this.$v.formData.url.$error) {
        return ''
      }
      if (!this.$v.formData.url.required) {
        return this.$t('validation.required')
      }
    },
  },
  validations: {
    formData: {
      user_name: {
        required,
        minLength: minLength(3),
      },
      password: {
        required: requiredIf(function () {
          return !this.isEdit
        }),
        minLength: minLength(8),
      },
      client_id: {
        required
      },
      url: {
        url,
        required
      },
      host: {
        required
      },
    },
  },
  methods: {
    ...mapActions('avalara', [
      'addAvalaraConfig',
    ]),
    async submitAvalara() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      try {
        let res
        this.isLoading = true
        res = await this.addAvalaraConfig(this.formData)
        this.isLoading = false
        window.toastr['success'](res.data.response)
        this.$router.push('/admin')
        return true
      } catch (error) {
        console.log('Error', error)
        window.toastr['error'](error.response.data.response)
        this.isLoading = false
        return false
      }
    },
    setConexion(val) {
   
      this.production = val
      this.conexion = val
        ? this.$t('avalara.conexion_production')
        : this.$t('avalara.conexion_sandbox')
      this.formData.conexion = val
        ? 'production'
        : 'sandbox'
    },
  },
}
</script>

<style lang="scss" scoped>
</style>
