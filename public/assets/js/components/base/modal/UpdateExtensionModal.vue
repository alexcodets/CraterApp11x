<template>
  <div class="item-modal">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <form action="" @submit.prevent="submitData">
      <div class="px-8 py-8 sm:p-6">
        <!--<sw-input-group
          :label="$t('pbx_services.ext_tenant_id')"
          :error="tenantError"
          class="mb-4"
          variant="horizontal"
          required
        >
          <sw-input
            :invalid="$v.formData.tenant_id.$error"
            v-model="formData.tenant_id"
            type="text"
            @input="$v.formData.tenant_id.$touch()"
          />
        </sw-input-group>-->

        <sw-input-group
          :label="$t('pbx_services.ext_name')"
          :error="nameError"
          class="mb-4"
          variant="horizontal"
          required
        >
          <sw-input
            :invalid="$v.formData.name.$error"
            v-model="formData.name"
            type="text"
            @input="$v.formData.name.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('login.email')"
          :error="emailError"
          class="mb-4"
          variant="horizontal"
          required
        >
          <sw-input
            :invalid="$v.formData.email.$error"
            v-model.trim="formData.email"
            type="text"
            name="email"
            class="mt-1 md:mt-0"
            @input="$v.formData.email.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('pbx_services.ext_extension')"
          :error="extError"
          class="mb-4"
          variant="horizontal"
          required
        >
          <sw-input
            :invalid="$v.formData.ext.$error"
            v-model="formData.ext"
            type="text"
            @input="$v.formData.ext.$touch()"
          />
        </sw-input-group>

        <!--<sw-input-group
          :label="$t('pbx_services.ext_location')"
          :error="locationError"
          class="mb-4"
          variant="horizontal"
          required
        >
          <sw-input
            :invalid="$v.formData.location.$error"
            v-model="formData.location"
            type="text"
            @input="$v.formData.location.$touch()"
          />
        </sw-input-group>-->

        <!--<sw-input-group
          :label="$t('pbx_services.ext_ua_id')"
          :error="uaIdError"
          class="mb-4"
          variant="horizontal"
          required
        >
          <sw-input
            :invalid="$v.formData.ua_id.$error"
            v-model="formData.ua_id"
            type="text"
            @input="$v.formData.ua_id.$touch()"
          />
        </sw-input-group>-->

        <sw-input-group
          :label="$t('pbx_services.ext_status')"
          class="mb-4"
          variant="horizontal"
          :error="statusError"
          required
        >
          <sw-select
            :invalid="$v.formData.status.$error"
            v-model="formData.status"
            :options="statuses"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :max-height="200"
            :placeholder="$t('pbx_services.ext_select_status')"
            label="name"
            @input="$v.formData.status.$touch()"
          />
        </sw-input-group>

        <!--<sw-input-group
          :label="$t('pbx_services.ext_protocol')"
          :error="protocolError"
          class="mb-4"
          variant="horizontal"
          required
        >
          <sw-input
            :invalid="$v.formData.protocol.$error"
            v-model="formData.protocol"
            type="text"
            @input="$v.formData.protocol.$touch()"
          />
        </sw-input-group>-->

        <sw-input-group
          :label="$t('pbx_services.ext_pin')"
          :error="pinError"
          class="mb-4"
          variant="horizontal"
          required
        >
          <sw-input
            :invalid="$v.formData.pin.$error"
            v-model="formData.pin"
            type="text"
            @input="$v.formData.pin.$touch()"
            onkeypress='return event.charCode >= 48 && event.charCode <= 57'
            pattern="[0-9]{4}"
            maxlength="4"
          />
        </sw-input-group>

        <div
          class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
        >
          <sw-button
            class="mr-3"
            variant="primary-outline"
            type="button"
            @click="closeExtensionModal"
          >
            {{ $t('general.cancel') }}
          </sw-button>
          <sw-button variant="primary" type="submit">
            <save-icon class="mr-2" />
            {{ $t('general.save') }}
          </sw-button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const {
  required,
  minLength,
  numeric,
  maxLength,
  minValue,
  email,
} = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      isLoading: false,
      isRequestOnGoing: false,
      formData: {
        tenant_id: null,
        name: null,
        email: null,
        ext: null,
        location: null,
        ua_id: null,
        status: null,
        protocol: null,
        pin: null,
        macaddress: null,
      },
      statuses: [
        { name: 'Enabled', value: 'enabled' },
        { name: 'Disabled', value: 'disabled' },
      ],
    }
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },
      tenant_id: {
        required,
        numeric,
      },
      email: {
        required,
        email,
      },
      ext: {
        required,
        numeric,
      },
      location: {
        required,
      },
      ua_id: {
        required,
        numeric,
      },
      status: {
        required,
      },
      protocol: {
        required,
      },
      pin: {
        required,
      },
    },
  },
  computed: {
    ...mapGetters('modal', ['modalDataID', 'modalData', 'modalActive']),

    tenantError() {
      if (!this.$v.formData.tenant_id.$error) {
        return ''
      }
      if (!this.$v.formData.tenant_id.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.tenant_id.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
    },

    emailError() {
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

    extError() {
      if (!this.$v.formData.ext.$error) {
        return ''
      }
      if (!this.$v.formData.ext.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.ext.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },

    locationError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },

    uaIdError() {
      if (!this.$v.formData.tenant_id.$error) {
        return ''
      }
      if (!this.$v.formData.tenant_id.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.tenant_id.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },

    statusError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },

    protocolError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },

    pinError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },
  },
  mounted() {
    this.setInitialData()
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('pbxService', ['updateExtension']),

    setInitialData() {
      if (this.modalData) {
       // console.log(this.modalData)
        this.formData.tenant_id = this.modalData.pbx_tenant_id
        this.formData.name = this.modalData.name
        this.formData.email = this.modalData.email
        this.formData.ext = this.modalData.ext
        this.formData.location = this.modalData.location
        this.formData.ua_id = this.modalData.ua_id
        this.formData.status = this.statuses.find(
          (_status) => this.modalData.status === _status.value
        )
        this.formData.protocol = this.modalData.protocol

        if (this.modalData.pin) {
          this.formData.pin = this.modalData.pin
        }
      }
    },

    resetFormData() {
      this.formData = {
        tenant_id: null,
        name: null,
        email: null,
        ext: null,
        location: null,
        ua_id: null,
        status: null,
        protocol: null,
        pin: null,
        macaddress: null,
      }
      this.$v.$reset()
    },

    closeExtensionModal() {
      this.resetFormData()
      this.closeModal()
      this.resetModalData()
    },

    onlyNumber($event) {
     // console.log($event.keyCode) //keyCodes value
      let keyCode = $event.keyCode ? $event.keyCode : $event.which
      if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
        // 46 is dot
        $event.preventDefault()
      }
    },

    async submitData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.formData.status = this.formData.status.value

      let params = {
        pbx_service_id: this.$route.params.pbx_service_id,
        pbx_ext_id: this.modalDataID,
        data: this.formData,
      }

      this.isRequestOnGoing = true
      //console.log(params)
      let response = await this.updateExtension(params)
     // console.log(response)
      if (response.data.success) {
        window.toastr['success'](this.$tc('pbx_services.ext_update_success'))
        window.hub.$emit('updateExt')
        this.isRequestOnGoing = false
        this.resetModalData()
        this.resetFormData()
        this.closeModal()
        return true
      }

      this.isRequestOnGoing = false
      if (response.data.error) {
        window.toastr['error'](response.data.error)
      } else {
        window.toastr['error'](response.data.message)
      }
    },
  },
}
</script>

<style scoped>
</style>