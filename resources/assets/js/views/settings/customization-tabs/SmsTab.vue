<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updateSMSSetting">
      <sw-input-group
        :label="$t('settings.customization.phonefrom')"
        class="md:col-span- mt-2"
      >
        <sw-input
          class="input-expand"
          style="max-width: 30%"
          v-model="phoneFrom"
          type="text"
          name="phone"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.default_estimate_sms_body')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="default_estimate_sms_body"
          :fields="mailFields"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.default_invoice_sms_body')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="default_invoice_sms_body"
          :fields="InvoiceMailFields"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.default_lead_sms_body')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="default_lead_sms_body"
          :fields="LeadMailFields"
        />
      </sw-input-group>

      <sw-button
        variant="primary"
        type="submit"
        class="mt-4"
        v-if="permission.update"
      >
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>
  </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { TrashIcon, PencilIcon, PlusIcon } from '@vue-hero-icons/solid'
const { required, maxLength, alpha } = require('vuelidate/lib/validators')

export default {
  props: {
    settings: {
      type: Object,
      require: true,
      default: false,
    },
    permission: {
      type: Object,
      require: true,
    },
  },

  data() {
    return {
      items: {
        item_prefix: null,
        itme_prefix_general: null,
      },
      isLoading: false,
      phoneFrom: null,
      default_estimate_sms_body: null,
      default_invoice_sms_body: null,
      default_lead_sms_body: null,
      mailFields: [
        'customer',
        'estimate',
        'company',
        'customerCustom',
        'estimateCustom',
      ],
      InvoiceMailFields: [
        'customer',
        'customerCustom',
        'invoice',
        'invoiceCustom',
        'company',
      ],
      LeadMailFields: ['customer', 'customerCustom', 'company'],
    }
  },

  computed: {},

  validations: {},

  watch: {
    settings(val) {
      this.phoneFrom = val ? val.phoneFrom : ''
      this.default_estimate_sms_body = val ? val.default_estimate_sms_body : ''
      this.default_invoice_sms_body = val ? val.default_invoice_sms_body : ''

      this.default_lead_sms_body = val ? val.default_lead_sms_body : ''
    },
  },
  components: {
    TrashIcon,
    PlusIcon,
    PencilIcon,
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('company', ['updateCompanySettings']),
    ...mapActions('item', [
      'deleteItemUnit',
      'fetchItemUnits',
      'fetchItemsCategories',
    ]),
    ...mapActions('item', ['setPrefix']),

    async updateSMSSetting() {
      let result = this.validarFormatoTelefonico(this.phoneFrom)

      if (result == false) {
        window.toastr['error'](
          this.$t('settings.customization.warning_wrong_format')
        )
        return false
      }

      result = this.validarLongitudString(this.default_estimate_sms_body)

      if (result == false) {
        window.toastr['error'](
          this.$t('settings.customization.warning_wrong_format_size')
        )
        return false
      }

      result = this.validarLongitudString(this.default_invoice_sms_body)

      if (result == false) {
        window.toastr['error'](
          this.$t('settings.customization.warning_wrong_format_size')
        )
        return false
      }

      result = this.validarLongitudString(this.default_lead_sms_body)

      if (result == false) {
        window.toastr['error'](
          this.$t('settings.customization.warning_wrong_format_size')
        )
        return false
      }

      let data = {
        settings: {
          phoneFrom: this.phoneFrom,
          default_estimate_sms_body: this.default_estimate_sms_body,
          default_invoice_sms_body: this.default_invoice_sms_body,
          default_lead_sms_body: this.default_lead_sms_body,
        },
      }

      let res = await this.updateCompanySettings(data)

      if (res.data.success) {
        window.toastr['success'](
          this.$t('corePbx.customization.services_updated')
        )
      }
    },

    validarFormatoTelefonico(valor) {
      // Expresión regular para validar el formato telefónico
      const regex = /^(\+)?\d+$/
      return regex.test(valor)
    },

    validarLongitudString(valor) {
      // Verifica si el string tiene 450 caracteres o menos
      return valor.length <= 450
    },
  },
}
</script>
