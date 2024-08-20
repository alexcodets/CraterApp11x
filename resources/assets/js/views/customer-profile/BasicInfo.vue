<style>
input:checked ~ .tab-content {
  max-height: 100vh;
  min-height: 60vh;
}
</style>

<template>
  <div>
    <div class="col-span-12">
      <p class="text-gray-500 uppercase sw-section-title">
        {{ $t('customers.basic_info') }}
      </p>

      <div
        class="grid grid-cols-1 gap-4 mt-5 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1"
      >
        <div>
          <p
            class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
          >
            {{ $t('customers.customer_number') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              loggedInCustomer.customer && loggedInCustomer.customer.customcode
                ? loggedInCustomer.customer.customcode
                : ''
            }}
          </p>
        </div>

        <div>
          <p
            class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
          >
            {{ $t('customers.display_name') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              loggedInCustomer.customer && loggedInCustomer.customer.name
                ? loggedInCustomer.customer.name
                : ''
            }}
          </p>
        </div>

        <div>
          <p
            class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
          >
            {{ $t('customers.primary_contact_name') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              loggedInCustomer.customer &&
              loggedInCustomer.customer.contact_name
                ? loggedInCustomer.customer.contact_name
                : ''
            }}
          </p>
        </div>
      </div>

      <div
        class="grid grid-cols-1 gap-4 mt-5 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1"
      >
        <div>
          <p
            class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
          >
            {{ $t('customers.email') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              loggedInCustomer.customer && loggedInCustomer.customer.email
                ? loggedInCustomer.customer.email
                : ''
            }}
          </p>
        </div>
        <div>
          <p
            class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
          >
            {{ $t('wizard.currency') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              loggedInCustomer.customer && loggedInCustomer.customer.currency
                ? `${loggedInCustomer.customer.currency.code} (${loggedInCustomer.customer.currency.symbol})`
                : ''
            }}
          </p>
        </div>
        <div>
          <p
            class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
          >
            {{ $t('customers.phone_number') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              loggedInCustomer.customer && loggedInCustomer.customer.phone
                ? loggedInCustomer.customer.phone
                : ''
            }}
          </p>
        </div>

        <div>
          <p
            class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
          >
            {{ $t('customers.type_customer') }}
          </p>
          <p
            class="text-sm font-bold leading-5 text-black non-italic"
            style="text-transform: capitalize"
          >
            {{
              loggedInCustomer.customer &&
              loggedInCustomer.customer.status_payment
                ? loggedInCustomer.customer.status_payment
                : ''
            }}
          </p>
        </div>

        <div>
          <p
            class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
          >
            {{ $t('customers.website') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              loggedInCustomer.customer && loggedInCustomer.customer.website
                ? loggedInCustomer.customer.website
                : ''
            }}
          </p>
        </div>

        <div>
          <p
            class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
          >
            {{ $t('customers.security_pin') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              loggedInCustomer.customer &&
              loggedInCustomer.customer.security_pin
                ? loggedInCustomer.customer.security_pin
                : ''
            }}
          </p>
        </div>
      </div>

      <p
        v-if="
          getFormattedShippingAddress.length ||
          getFormattedBillingAddress.length
        "
        class="mt-8 text-gray-500 uppercase sw-section-title"
      >
        {{ $t('customers.address') }}
      </p>

      <div
        class="grid grid-cols-1 gap-4 md:grid-cols-2 sm:grid-cols-1 lg:grid-cols-2"
      >
        <div v-if="getFormattedBillingAddress.length" class="mt-5">
          <p
            class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
          >
            {{ $t('customers.billing_address') }}
          </p>
          <p
            class="text-sm font-bold leading-5 text-black non-italic"
            v-html="getFormattedBillingAddress"
          />
        </div>

        <!-- shipping address -->
        <div v-if="getFormattedShippingAddress.length" class="mt-5">
          <p
            class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
          >
            {{ $t('customers.shipping_address') }}
          </p>
          <p
            class="text-sm font-bold leading-5 text-black non-italic"
            v-html="getFormattedShippingAddress"
          />
        </div>
      </div>

      <!-- Custom Fields -->
      <p
        v-if="getCustomField.length > 0"
        class="mt-8 text-gray-500 uppercase sw-section-title"
      >
        {{ $t('settings.custom_fields.title') }}
      </p>

      <div
        class="grid grid-cols-1 gap-4 mt-5 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1"
      >
        <div
          v-for="(field, index) in getCustomField"
          :key="index"
          :required="field.is_required ? true : false"
        >
          <p
            class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
          >
            {{ field.custom_field.label }}
          </p>
          <p
            v-if="field.type === 'Switch'"
            class="text-sm font-bold leading-5 text-black non-italic"
          >
            <span v-if="field.defaultAnswer === 1"> Yes </span>
            <span v-else> No </span>
          </p>
          <p v-else class="text-sm font-bold leading-5 text-black non-italic">
            {{ field.defaultAnswer }}
          </p>
        </div>
      </div>

    <!--  <sw-button
        v-if="loggedInCustomer.customer.formattedAddCredit == 'YES'"
        tag-name="router-link"
        to="/customer/payments/addcredit"
        variant="primary"
        size="lg"
        class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
      >
        
        {{ $t('customers.add_credit') }}
      </sw-button> -->
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  data() {
    return {
      customer: null,
      customFields: [],
    }
  },
  computed: {
    ...mapGetters('customerProfile', ['loggedInCustomer']),

    getFormattedBillingAddress() {
      let billingAddress = ``
      if (!this.loggedInCustomer.customer) {
        return billingAddress
      }

      if (!this.loggedInCustomer.customer.billing_address) {
        return billingAddress
      }

      if (this.loggedInCustomer.customer.billing_address.address_street_1) {
        billingAddress += `<span>${this.loggedInCustomer.customer.billing_address.address_street_1},</span><br>`
      }
      if (this.loggedInCustomer.customer.billing_address.address_street_2) {
        billingAddress += `<span>${this.loggedInCustomer.customer.billing_address.address_street_2},</span><br>`
      }
      if (this.loggedInCustomer.customer.billing_address.city) {
        billingAddress += `<span>${this.loggedInCustomer.customer.billing_address.city},</span> `
      }

      if (this.loggedInCustomer.customer.billing_address.state) {
        billingAddress += `<span>${this.loggedInCustomer.customer.billing_address.state.name},</span>`
      }

      if (this.loggedInCustomer.customer.billing_address.zip) {
        billingAddress += `<span>${this.loggedInCustomer.customer.billing_address.zip}.</span><br>`
      }
      if (this.loggedInCustomer.customer.billing_address.country) {
        billingAddress += `<span>${this.loggedInCustomer.customer.billing_address.country.name}.</span> `
      }

      return billingAddress
    },

    getFormattedShippingAddress() {
      let shippingAddress = ``

      if (!this.loggedInCustomer.customer) {
        return shippingAddress
      }

      if (!this.loggedInCustomer.customer.shipping_address) {
        return shippingAddress
      }

      if (this.loggedInCustomer.customer.shipping_address.address_street_1) {
        shippingAddress += `<span>${this.loggedInCustomer.customer.shipping_address.address_street_1},</span><br>`
      }
      if (this.loggedInCustomer.customer.shipping_address.address_street_2) {
        shippingAddress += `<span>${this.loggedInCustomer.customer.shipping_address.address_street_2},</span><br>`
      }
      if (this.loggedInCustomer.customer.shipping_address.city) {
        shippingAddress += `<span>${this.loggedInCustomer.customer.shipping_address.city},</span> `
      }
      if (this.loggedInCustomer.customer.shipping_address.state) {
        shippingAddress += `<span>${this.loggedInCustomer.customer.shipping_address.state},</span><br>`
      }
      if (this.loggedInCustomer.customer.shipping_address.country) {
        shippingAddress += `<span>${this.loggedInCustomer.customer.shipping_address.country.name}.</span> `
      }
      if (this.loggedInCustomer.customer.shipping_address.zip) {
        shippingAddress += `<span>${this.loggedInCustomer.customer.shipping_address.zip}.</span> `
      }
      return shippingAddress
    },

    getCustomField() {
      if (
        this.loggedInCustomer.customer &&
        this.loggedInCustomer.customer.fields
      ) {
        return this.loggedInCustomer.customer.fields
      }
      return []
    },

    isServiceView() {
      if (
        this.$route.name === 'customers.package-view' ||
        this.$route.name === 'customers.pbx-service-view'
      ) {
        return true
      }
      return false
    },
  },
}
</script>
