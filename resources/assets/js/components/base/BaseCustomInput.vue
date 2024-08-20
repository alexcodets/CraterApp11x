<template>
  <div class="relative">
    <div class="absolute bottom-0 right-0 z-10">
      <sw-dropdown
        v-if="fieldList.length > 0"
        :close-on-select="true"
        max-height="220"
        position="bottom-end"
        class="mb-2"
      >
        <sw-button
          slot="activator"
          variant="primary-outline"
          type="button"
          class="mr-2"
        >
          <plus-sm-icon class="h-5 mr-1 -ml-2" />
          {{ $t('settings.customization.addresses.insert_fields') }}
        </sw-button>
        <div class="flex p-2">
          <ul v-for="(type, index) in fieldList" :key="index" class="list-none">
            <li class="mb-1 ml-2 text-xs font-semibold text-gray-500 uppercase">
              {{ type.label }}
            </li>
            <li
              v-for="(field, index) in type.fields"
              :key="index"
              class="w-48 text-sm font-normal cursor-pointer hover:bg-gray-200"
              @click="insertField(field.value)"
            >
              <div class="flex">
                <chevron-double-right-icon class="h-3 mt-1 text-gray-400" />{{
                  field.label
                }}
              </div>
            </li>
          </ul>
        </div>
      </sw-dropdown>
    </div>
    <sw-editor
      v-model="inputValue"
      :set-editor="inputValue"
      :disabled="disabled"
      :invalid="isFieldValid"
      :placeholder="placeholder"
      variant="header-editor"
      input-class="border-none"
      class="text-area-field"
      @input="handleInput"
      @change="handleChange"
      @keyup="handleKeyupEnter"
    />
  </div>
</template>

<script>
import { PlusSmIcon } from '@vue-hero-icons/outline'
import { ChevronDoubleRightIcon } from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'
import customFields from '../../mixins/customFields'

export default {
  components: {
    PlusSmIcon,
    ChevronDoubleRightIcon,
  },
  props: {
    value: {
      type: [String, Number, File],
      default: '',
    },
    types: {
      type: Array,
      default: null,
    },
    placeholder: {
      type: String,
      default: '',
    },
    rows: {
      type: String,
      default: '10',
    },
    cols: {
      type: String,
      default: '30',
    },
    invalid: {
      type: Boolean,
      default: false,
    },
    fields: {
      // En caso de que se quiera filtrar los fields,
      // [
      //   'customer', // si se quiere mostrar todos los fields 
      //   'customer:except:CONTACT_EMAIL,CONTACT_PHONE', // si se quiere mostrar todos los fields excepto CONTACT_EMAIL y CONTACT_PHONE
      //   'customer:select:CONTACT_EMAIL,CONTACT_PHONE', // solo muestra los field COMPANY_COUNTRY y COMPANY_CITY
      // ]
      type: Array,
      default: null,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      fieldList: [],
      ticketFields:[],
      servicesFields:[],
      invoiceFields: [],      
      estimateFields: [],
      expenseCustomFields: [],
      paymentFields: [],
      paymentAFields: [],
      customerFields: [],
      customerEFields: [],
      customerCFields: [],
      position: null,
      inputValue: this.value,
    }
  },
  computed: {
    ...mapGetters('customFields', ['getCustomFields']),
    isFieldValid() {
      return this.invalid
    },
  },
  watch: {
    value() {
      this.inputValue = this.value
    },
    fields() {
      if (this.fields && this.fields.length > 0) {
        this.getFields()
      }
    },
    getCustomFields(newValue) {
      this.invoiceFields = newValue
        ? newValue.filter((field) => field.model_type === 'Invoice')
        : []
      this.ticketFields = newValue
        ? newValue.filter((field) => field.model_type === 'ticket')
        : []
      this.servicesFields = newValue
        ? newValue.filter((field) => field.model_type === 'service')
        : []
      this.customerFields = newValue
        ? newValue.filter((field) => field.model_type === 'Customer')
        : []
      this.customerEFields = newValue
        ? newValue.filter((field) => field.model_type === 'CustomerE')
        : []
      this.customerCFields = newValue
        ? newValue.filter((field) => field.model_type === 'CustomerC')
        : []
      this.paymentFields = newValue
        ? newValue.filter((field) => field.model_type === 'Payment')
        : []
      this.paymentAFields = newValue
        ? newValue.filter((field) => field.model_type === 'PaymentA')
        : []
      this.estimateFields = newValue.filter(
        (field) => field.model_type === 'Estimate'
      )
      this.expenseCustomFields = newValue.filter(
        (field) => field.model_type === 'expenseCustom'
      )
      this.getFields()
    },
  },
  async mounted() {
    this.getFields()
    await this.fetchNoteCustomFields({ limit: 'all' })
  },
  methods: {
    ...mapActions('customFields', ['fetchNoteCustomFields']),

    getFields() {
      this.fieldList = []

      if (this.fields && this.fields.length > 0) {

        this.fields.forEach((fieldGroupArr) => {

            const fieldsGroup = fieldGroupArr.split(':');

            if (fieldsGroup[0] == 'shipping') {
              const fields = [
                  { label: 'Address name', value: 'SHIPPING_ADDRESS_NAME' },
                  { label: 'Country', value: 'SHIPPING_COUNTRY' },
                  { label: 'State', value: 'SHIPPING_STATE' },
                  { label: 'City', value: 'SHIPPING_CITY' },
                  { label: 'Address Street 1', value: 'SHIPPING_ADDRESS_STREET_1' },
                  { label: 'Address Street 2', value: 'SHIPPING_ADDRESS_STREET_2' },
                  { label: 'Phone', value: 'SHIPPING_PHONE' },
                  { label: 'Zip Code', value: 'SHIPPING_ZIP_CODE' },
                ]
              const fieldsFilter = this.filterFields( fieldsGroup, fields )

              this.fieldList.push({
                label: 'Shipping Address',
                fields: fieldsFilter,
              })
            }

            if (fieldsGroup[0] == 'billing') {
              const fields = [
                  { label: 'Address name', value: 'BILLING_ADDRESS_NAME' },
                  { label: 'Country', value: 'BILLING_COUNTRY' },
                  { label: 'State', value: 'BILLING_STATE' },
                  { label: 'City', value: 'BILLING_CITY' },
                  { label: 'Address Street 1', value: 'BILLING_ADDRESS_STREET_1' },
                  { label: 'Address Street 2', value: 'BILLING_ADDRESS_STREET_2' },
                  { label: 'Phone', value: 'BILLING_PHONE' },
                  { label: 'Zip Code', value: 'BILLING_ZIP_CODE' },
                ]
              const fieldsFilter = this.filterFields( fieldsGroup, fields )

              this.fieldList.push({
                label: 'Billing Address',
                fields: fieldsFilter,
              })
            }

            if (fieldsGroup[0] == 'customer') {
              const fields = [
                  { label: 'Company Name', value: 'CONTACT_DISPLAY_NAME' },
                  { label: 'Contact Name', value: 'PRIMARY_CONTACT_NAME' },
                  { label: 'Email', value: 'CONTACT_EMAIL' },
                  { label: 'Phone', value: 'CONTACT_PHONE' },
                  { label: 'Website', value: 'CONTACT_WEBSITE' },
                  { label: 'Login', value: 'CUSTOMER_LOGIN' },
                  ...this.customerFields.map((i) => ({
                    label: i.label,
                    value: i.slug,
                  })),
                ]
              const fieldsFilter = this.filterFields( fieldsGroup, fields )

              this.fieldList.push({
                label: 'Customer',
                fields: fieldsFilter,
              })
            }
            if (fieldsGroup[0] == 'customerE') {
              const fields = [
                  { label: 'Company Name', value: 'CONTACT_DISPLAY_NAME' },
                  { label: 'Contact Name', value: 'PRIMARY_CONTACT_NAME' },
                  { label: 'Email', value: 'CONTACT_EMAIL' },
                  { label: 'Username', value: 'CONTACT_USERNAME' },
                  { label: 'Phone', value: 'CONTACT_PHONE' },
                  { label: 'Website', value: 'CONTACT_WEBSITE' },
                  { label: 'Password', value: 'CONTACT_PASSWORD' },
                  { label: 'Role', value: 'CONTACT_ROLE' },
                  { label: 'Login', value: 'CUSTOMER_LOGIN' },
                  ...this.customerEFields.map((i) => ({
                    label: i.label,
                    value: i.slug,
                  })),
                ]
              const fieldsFilter = this.filterFields( fieldsGroup, fields )

              this.fieldList.push({
                label: 'Customer',
                fields: fieldsFilter,
              })
            }
            if (fieldsGroup[0] == 'customerC') {
              const fields = [
                  { label: 'Company Name', value: 'CONTACT_DISPLAY_NAME' },
                  { label: 'Contact Name', value: 'PRIMARY_CONTACT_NAME' },
                  { label: 'Email', value: 'CONTACT_EMAIL' },
                  { label: 'Username', value: 'CONTACT_USERNAME' },
                  { label: 'Phone', value: 'CONTACT_PHONE' },
                  { label: 'Website', value: 'CONTACT_WEBSITE' },
                  { label: 'Password', value: 'CONTACT_PASSWORD' },
                  { label: 'Role', value: 'CONTACT_ROLE' },
                  { label: 'Balance', value: 'CONTACT_BALANCE' },
                  { label: 'Email low balance notification', value: 'CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION' },
                  { label: 'Auto replenish amoun', value: 'CONTACT_AUTO_REPLENISH_AMOUNT' },
                  { label: 'Custom code', value: 'CONTACT_CUSTOM_CODE' },
                  { label: 'Status customer', value: 'CONTACT_STATUS_CUSTOMER' },
                  { label: 'Minimun balance', value: 'CONTACT_MINIMUN_BALANCE' },
                  { label: 'Login', value: 'CUSTOMER_LOGIN' },
                  ...this.customerCFields.map((i) => ({
                    label: i.label,
                    value: i.slug,
                  })),
                ]
              const fieldsFilter = this.filterFields( fieldsGroup, fields )

              this.fieldList.push({
                label: 'Customer',
                fields: fieldsFilter,
              })
            }

            if (fieldsGroup[0] == 'invoice') {
              const fields = [
                  { label: 'Date', value: 'INVOICE_DATE' },
                  { label: 'Due Date', value: 'INVOICE_DUE_DATE' },
                  { label: 'Number', value: 'INVOICE_NUMBER' },
                  { label: 'Ref Number', value: 'INVOICE_REF_NUMBER' },
                  { label: 'Invoice Link', value: 'INVOICE_LINK' },
                  { label: 'Pay Link', value: 'PAY_LINK' },
                  { label: 'Pay Link Login', value: 'PAY_LINK_LOGIN' },
                  ...this.invoiceFields.map((i) => ({
                    label: i.label,
                    value: i.slug,
                  })),
                ]
              const fieldsFilter = this.filterFields( fieldsGroup, fields )

              this.fieldList.push({
                label: 'Invoice',
                fields: fieldsFilter,
              })
            }

            if (fieldsGroup[0] == 'estimate') {
              const fields = [
                  { label: 'Date', value: 'ESTIMATE_DATE' },
                  { label: 'Expiry Date', value: 'ESTIMATE_EXPIRY_DATE' },
                  { label: 'Number', value: 'ESTIMATE_NUMBER' },
                  { label: 'Ref Number', value: 'ESTIMATE_REF_NUMBER' },
                  { label: 'Estimate Link', value: 'ESTIMATE_LINK' },
                  { label: 'Approval Link', value: 'APPROVAL_LINK' },
                  ...this.estimateFields.map((i) => ({
                    label: i.label,
                    value: i.slug,
                  })),
                ]
              const fieldsFilter = this.filterFields( fieldsGroup, fields )

              this.fieldList.push({
                label: 'Estimate',
                fields: fieldsFilter,
              })
            }

            if (fieldsGroup[0] == 'expenseCustom') {
              const fields = [
                  { label: 'Expense Number', value: 'EXPENSE_NUMBER' },
                  { label: 'Payment Number', value: 'PAYMENT_NUMBER' },
                  { label: 'Payment Method', value: 'PAYMENT_METHOD' },
                  { label: 'Payment Date', value: 'PAYMENT_DATE' },
                  { label: 'Provider Name', value: 'PROVIDER_NAME' },
                  { label: 'Provider Number', value: 'PROVIDER_NUMBER' },
                  { label: 'Customer Number', value: 'CUSTOMER_NUMBER' },
                  { label: 'Customer Name', value: 'CUSTOMER_NAME' },
                  { label: 'Amount', value: 'AMOUNT' },                  
                  { label: 'Category', value: 'CATEGORY' },
                  { label: 'Due Date', value: 'DUE_DATE' },
                  { label: 'Item', value: 'ITEM' },
                  ...this.expenseCustomFields.map((i) => ({
                    label: i.label,
                    value: i.slug,
                  })),
                ]
              const fieldsFilter = this.filterFields( fieldsGroup, fields )

              this.fieldList.push({
                label: 'Expense',
                fields: fieldsFilter,
              })
            }

            if (fieldsGroup[0] == 'payment') {
              const fields = [
                  { label: 'Date', value: 'PAYMENT_DATE' },
                  { label: 'Number', value: 'PAYMENT_NUMBER' },
                  { label: 'Mode', value: 'PAYMENT_MODE' },
                  { label: 'Amount', value: 'PAYMENT_AMOUNT' },
                  { label: 'Payment Link', value: 'PAYMENT_LINK' },              
                  { label: 'Transaction ID', value: 'TRANSACTION' },
                  ...this.paymentFields.map((i) => ({
                    label: i.label,
                    value: i.slug,
                  })),
                ]
              const fieldsFilter = this.filterFields( fieldsGroup, fields )

              this.fieldList.push({
                label: 'Payment',
                fields: fieldsFilter,
              })
            }
            if (fieldsGroup[0] == 'paymentA') {
              const fields = [
                  { label: 'Card Number', value: 'CARD_NUMBER' },
                  
                  { label: 'Credit Card', value: 'CREDIT_CARD' },
                  ...this.paymentAFields.map((i) => ({
                    label: i.label,
                    value: i.slug,
                  })),
                ]
              const fieldsFilter = this.filterFields( fieldsGroup, fields )

              this.fieldList.push({
                label: 'Credit Card',
                fields: fieldsFilter,
              })
            }
            if (fieldsGroup[0] == 'ticket') {
              const fields = [
                  { label: 'Departament', value: 'TICKECT_DEPARTAMENT' },
                  { label: 'Assigned To', value: 'TICKECT_ASSIGNED_TO' },
                  { label: 'Priority', value: 'TICKECT_PRIORITY' },
                  { label: 'Status', value: 'TICKECT_STATUS' },
                  { label: 'Details', value: 'TICKECT_DETAILS' },
                  
                  { label: 'Ticket Number', value: 'TICKECT_NUMBER' },
                  ...this.ticketFields.map((i) => ({
                    label: i.label,
                    value: i.slug,
                  })),
                ]
              const fieldsFilter = this.filterFields( fieldsGroup, fields )

              this.fieldList.push({
                label: 'Ticket',
                fields: fieldsFilter,
              })
            }
            if (fieldsGroup[0] == 'service') {
              const fields = [
                  { label: 'Status', value: 'SERVICE_STATUS' },
                  { label: 'Term', value: 'SERVICE_TERM' },
                  { label: 'Date begin', value: 'SERVICE_DATE_BEGIN' },
                  { label: 'Renewal date', value: 'SERVICE_RENEWAL_DATE' },
                  { label: 'Allow discount', value: 'SERVICE_ALLOW_DISCOUNT' },
                  { label: 'Auto suspension', value: 'SERVICE_AUTO_SUSPENSION' },
                  { label: 'Allow discount value', value: 'SERVICE_ALLOW_VALUE' },
                  { label: 'Time period value', value: 'SERVICE_TIME_PERIOD_VALUE' },
                  { label: 'Pbx tenant code', value: 'SERVICE_PBX_TENANT_CODE' },
                  { label: 'Pbx tenant name', value: 'SERVICE_PBX_TENANT_NAME' },
                  { label: 'Pbx services number', value: 'SERVICE_PBX_SERVICES_NUMBER' },
                  { label: 'Pbx packages price', value: 'SERVICE_PBX_PACKAGES_PRICE' },
                  // { label: 'Services number', value: 'SERVICES_NUMBER' },
                  { label: 'Packages price', value: 'PACKAGES_PRICE' },
                  { label: 'Total', value: 'SERVICE_TOTAL' },
                  // { label: 'Total', value: 'SERVICE_TOTAL' },
                  ...this.servicesFields.map((i) => ({
                    label: i.label,
                    value: i.slug,
                  })),
                ]
              const fieldsFilter = this.filterFields( fieldsGroup, fields )

              this.fieldList.push({
                label: 'Service',
                fields: fieldsFilter,
              })
            }

            if (fieldsGroup[0] == 'company') {
              const fields = [
                  { label: 'Company Name', value: 'COMPANY_NAME' },
                  { label: 'Company Number', value: 'COMPANY_NUMBER' },
                  { label: 'Country', value: 'COMPANY_COUNTRY' },
                  { label: 'State', value: 'COMPANY_STATE' },
                  { label: 'City', value: 'COMPANY_CITY' },
                  { label: 'Address Street 1', value: 'COMPANY_ADDRESS_STREET_1' },
                  { label: 'Address Street 2', value: 'COMPANY_ADDRESS_STREET_2' },
                  { label: 'Phone', value: 'COMPANY_PHONE' },
                  { label: 'Zip Code', value: 'COMPANY_ZIP_CODE' },
                  { label: 'State Code', value: 'STATE_CODE' },
                ]
              const fieldsFilter = this.filterFields( fieldsGroup, fields )

              this.fieldList.push({
                label: 'Company',
                fields: fieldsFilter,
              })
            }

            if (fieldsGroup[0] == 'pbx_server') {
              const fields = [
                  { label: 'Server Label', value: 'SERVER_LABEL' },
                  { label: 'Hostname / IP', value: 'HOST_IP' },                  
                  { label: 'Time Zone', value: 'TIME_ZONE' },                
                  { label: 'Time Server Down', value: 'HOUR_DOWM' },                
                  { label: 'Time Server Up', value: 'HOUR_UP' },
                ]
              const fieldsFilter = this.filterFields( fieldsGroup, fields )

              this.fieldList.push({
                label: 'pbx_server',
                fields: fieldsFilter,
              })
            }

            if (fieldsGroup[0] == 'pbx_extension') {
              const fields = [
                  { label: 'Name', value: 'EXT_NAME' },
                  { label: 'Extension', value: 'EXT_EXT' },                  
                  { label: 'Email', value: 'EXT_EMAIL' },                
                  { label: 'Status', value: 'EXT_STATUS' },                
                  { label: 'UA Name', value: 'EXT_FULLNAME' },
                  { label: 'Extension Down', value: 'HOUR_DOWM' },                
                  { label: 'Extension Up', value: 'HOUR_UP' },
                ]
              const fieldsFilter = this.filterFields( fieldsGroup, fields )

              this.fieldList.push({
                label: 'pbx_extension',
                fields: fieldsFilter,
              })
            }
        })
      }
    },
    filterFields(fieldsGroup, fields) {
      if(fieldsGroup[1] === "select"){
        const fieldsValue = fieldsGroup[2].split(',')
        return fields.filter((field) => fieldsValue.includes(field.value))
      }
      else if(fieldsGroup[1] === "except"){
        const fieldsValue = fieldsGroup[2].split(',')
        return fields.filter((field) => !fieldsValue.includes(field.value))
      }
      else{
        return fields
      }
    },

    insertField(varName) {
      if (this.inputValue) {
        this.inputValue += `{${varName}}`
      } else {
        this.inputValue = `{${varName}}`
      }
      this.$emit('input', this.inputValue)
    },

    handleInput(e) {
      this.$emit('input', this.inputValue)
    },

    handleChange(e) {
      this.$emit('change', this.inputValue)
    },

    handleKeyupEnter(e) {
      this.$emit('keyup', this.inputValue)
    },

    handleKeyDownEnter(e) {
      this.$emit('keydown', e, this.inputValue)
    },

    handleFocusOut(e) {
      this.$emit('blur', this.inputValue)
    },
  },
}
</script>
