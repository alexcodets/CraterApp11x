<template>
  <base-page class="relative payment-create h-screen">

    <div v-if="!paymentSuccess && !invoiceIsCompleted && !invoiceIsCanceled">
      <form  @submit.prevent="submitPaymentData" class="flex flex-wrap justify-center w-full">
        <div class="flex flex-wrap justify-center w-full">
          <img
            :src="urlLogo"
            class="block w-48 h-auto max-w-full mb-5 text-primary-400"
          />
        </div>
        <base-loader v-if="isRequestOnGoing" />

        <sw-card class="md:w-2/3" v-else>
          
          <div class="grid gap-6 grid-col-1 md:grid-cols-2">
            <sw-input-group
              :label="$t('payments.date')"
              :error="DateError"
              required
              class="pointer-events-none"
            >
              <base-date-picker
                v-model="formData.payment_date"
                :invalid="$v.formData.payment_date.$error"
                :calendar-button="true"
                class="mt-1"
                calendar-button-icon="calendar"
                @input="$v.formData.payment_date.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('payments.payment_number')"
              :error="paymentNumError"
              required
              class="pointer-events-none"
            >
              <sw-input
                :prefix="`${paymentPrefix} - `"
                :invalid="$v.paymentNumAttribute.$error"
                v-model.trim="paymentNumAttribute"
                class="mt-1"
                autocomplete="off"
                @input="$v.paymentNumAttribute.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('payments.customer')"
              :error="customerError"
              required
              class="pointer-events-none"
            >
              <sw-select
                v-model="customer"
                :options="customers"
                :searchable="true"
                :show-labels="false"
                :allow-empty="false"
                :placeholder="$t('customers.select_a_customer')"
                label="name"
                class="mt-1"
                track-by="id"
              />
            </sw-input-group>

            <sw-input-group :label="$t('payments.invoice_title')" class="pointer-events-none">
              <sw-select
                v-model="invoice"
                :options="invoiceList"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$t('invoices.select_invoice')"
                :custom-label="invoiceWithAmount"
                class="mt-1"
                track-by="invoice_number"
              />
            </sw-input-group>

            <sw-input-group
              v-if="
                creditv &&
                customer.balance > 1  &&
                formData.payment_method == null
              "
            >
              <div class="flex flex-wrap justify-between">
                <span class="flex flex-wrap justify-end"
                  >{{ $t('payments.account_avalable_credit') }}:
                  <div
                    class="text-success text-xl ml-2"
                    v-html="
                      $utils.formatMoney(
                        customer.balance * 100,
                        customer.currency
                      )
                    "
                  />
                </span>
                <!-- boton Apply Credit -->
                <sw-button
                  v-if="!isEdit"
                  :loading="isLoading"
                  :disabled="isLoading"
                  variant="primary"
                  type="submit"
                >
                  {{ $t('payments.apply_credit') }}
                </sw-button>
              </div>
            </sw-input-group>

            <sw-divider
              v-if="
                formData.user_id != null &&
                formData.invoice_id != null &&
                creditv &&
                this.customer.balance > 1 &&
                formData.payment_method === null
              "
              class="opacity-0"
            />

            <sw-input-group
              :label="$t('payments.amount')"
              :error="amountError"
              required
              class="pointer-events-none"
            >
              <div class="relative w-full mt-1">
                <sw-money
                  v-model="amount"
                  :currency="customerCurrency"
                  :invalid="$v.formData.amount.$error"
                  class="
                    relative
                    w-full
                    focus:border focus:border-solid focus:border-primary-500
                  "
                  @input="$v.formData.amount.$touch()"
                />
              </div>
            </sw-input-group>
            <sw-input-group :label="$t('payments.payment_mode')">
              <sw-select
                v-if="!fetchingPaymentMethod"
                v-model="formData.payment_method"
                :options="paymentMethodsOption"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('payments.select_payment_mode')"
                :max-height="150"
                label="name"
                class="mt-1"
                :error="paymentMethodError"
                @select="PaymentModeSelected"
                :disabled="isEdit"
                required
              >                
              </sw-select>
            </sw-input-group>

            <!-- <div
              v-if="
                formData.payment_method &&
                formData.payment_method.name == 'PayPal'
              "
            >
              <paypal
                :formData="formData"
                :codePayment="codePayment"
                :invoice_number="invoice_number"
                :customer="customer"
                @paypalSuccess="paypalSuccess"
              ></paypal>
            </div> -->

            <div v-if="add_payment_gateway_select">

            <sw-input-group
              v-if="this.type_ach"
              :label="$t('payments.select_accounts')"
            >
              <sw-select
                v-model="account"
                :options="accountList"
                :searchable="true"
                :show-labels="false"
                :allow-empty="false"
                :disabled="isEdit"
                :placeholder="$t('payments.select_accounts')"
                label="name_account_number"
                class="mt-1"
                track-by="id"
                @select="selectItemAccount"
                :loading="isLoadingPayments"
              />
            </sw-input-group>
            <!-- PAYMENT GATEWAYS -->
            <!-- <sw-input-group
              v-if="
                this.add_payment_gateway_select && !this.type_ach && this.type_cc
              "
              :label="$t('settings.payment_gateways.title')"
            >
              <sw-select
                v-model="formData.payment_gateways"
                :options="payment_gateways"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :disabled="isEdit"
                :placeholder="$t('items.select_a_type')"
                class="mt-1"
                track-by="id"
                label="name"
                select=""
              />
            </sw-input-group> -->

            <!-- BALANCE CUSTOMER -->
            <sw-input-group
              v-if="isEdit"
              :label="$t('tax_groups.status')"
              class="mt-1"
              :error="statusError"
              required
            >
              <sw-select
                v-model="formData.status"
                :invalid="$v.formData.status ? $v.formData.status.$error : false"
                :options="status"
                :disabled="isTransactionStatus || this.isedtiablefalse"
                :searchable="true"
                :show-labels="false"
                :tabindex="16"
                :allow-empty="true"
                :placeholder="$t('tax_groups.status')"
                label="text"
                track-by="value"
                @select="transactionStatusSelected"
              />
            </sw-input-group>

            <!--<div v-if="isEdit && voidStatusChange" class="flex mt-6">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.void_status_change"
                  class="absolute"
                  style="top: -20px"
                />
              </div>

              <div class="ml-4">
                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('payments.process_status_change') }}
                </p>
              </div>
            </div>

            <div v-if="isEdit && refundedStatusChange" class="flex mt-6">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.refunded_status_change"
                  class="absolute"
                  style="top: -20px"
                />
              </div>

              <div class="ml-4">
                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('payments.process_status_change') }}
                </p>
              </div>
            </div>-->

            <div v-if="transactionStatusCheck" class="flex my-8">
              <div class="relative w-12">
                <sw-checkbox
                  v-model="formData.status_with_authorize"
                  class="absolute"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('payments.transaction_status_authorize_message') }}
                </p>
              </div>
            </div>

            <sw-divider
              v-if="isEdit && !transactionStatusCheck"
              class="mt-1 opacity-0"
            />
          </div>
          <!-- END BALANCE CUSTOMER -->

          <!-- Bank Account Information  -->
          <div v-if="this.type_ach && !this.isEdit" class="col-span-2 w-full flex flex-wrap">
            <h6 class="sw-section-title mb-2 mt-4">
              {{ $t('payment_accounts.bank_account_info') }}
            </h6>

            <div class="w-full flex flex-wrap">
              <div class="w-full md:w-1/2 mb-4 md:pr-2">
                <sw-input-group
                  :label="$t('payment_accounts.ACH_type')"
                  class="md:mr-2"
                  :error="ACHTypeError"
                  required
                >
                  <sw-select
                    v-model="formData.ACH_type"
                    :options="bank_account_type"
                    :invalid="$v.formData.ACH_type.$error"
                    :searchable="true"
                    :show-labels="false"
                    :tabindex="16"
                    :allow-empty="true"
                    label="text"
                    track-by="value"
                  />
                </sw-input-group>
              </div>

              <div class="w-full md:w-1/2 mb-4 md:pl-2">
                <sw-input-group
                  :label="$t('payment_accounts.account_number')"
                  class="md:ml-2"
                  :error="accountNumberError"
                  required
                >
                  <sw-input
                    :invalid="$v.formData.account_number.$error"
                    v-model="formData.account_number"
                    focus
                    :type="showAccountFieldHide ? 'password' : 'text'"
                    name="account_number"
                    tabindex="1"
                    autocomplete="off"
                    @input="$v.formData.account_number.$touch()"
                  >
                    <template v-slot:rightIcon>
                      <eye-off-icon
                        v-if="showAccountFieldHide"
                        class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                        @click="showAccountFieldHide = !showAccountFieldHide"
                      />
                      <eye-icon
                        v-else
                        class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                        @click="showAccountFieldHide = !showAccountFieldHide"
                      />
                    </template>
                  </sw-input>
                </sw-input-group>
              </div>

              <div class="w-full md:w-1/2 mb-4 md:pr-2">
                <sw-input-group
                  :label="$t('payment_accounts.routing_number')"
                  class="md:mr-2"
                  :error="routingNumberError"
                  required
                >
                  <sw-input
                    :invalid="$v.formData.routing_number.$error"
                    v-model="formData.routing_number"
                    focus
                    :type="showRoutingFieldHide ? 'password' : 'text'"
                    name="routing_number"
                    tabindex="1"
                    autocomplete="off"
                    @input="$v.formData.routing_number.$touch()"
                  >
                    <template v-slot:rightIcon>
                      <eye-off-icon
                        v-if="showRoutingFieldHide"
                        class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                        @click="showRoutingFieldHide = !showRoutingFieldHide"
                      />
                      <eye-icon
                        v-else
                        class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                        @click="showRoutingFieldHide = !showRoutingFieldHide"
                      />
                    </template>
                  </sw-input>
                </sw-input-group>
              </div>

              <div class="w-full md:w-1/2 mb-4 md:pl-2">
                <sw-input-group
                  :label="$t('payment_accounts.bankname')"
                  class="md:ml-2"
                >
                  <sw-input
                    v-model="formData.bank_name"
                    focus
                    type="text"
                    name="bank_name"
                    tabindex="1"
                    autocomplete="off"
                  />
                </sw-input-group>
              </div>

              <div class="w-full md:w-1/2 mb-4 md:pr-2">
                <sw-input-group
                  :label="$t('payment_accounts.num_check')"
                  class="md:mr-2"
                >
                  <sw-input
                    v-model="formData.num_check"
                    focus
                    type="text"
                    name="num_check"
                    tabindex="1"
                  />
                </sw-input-group>
              </div>
            </div>
            <!-- Billing Address  -->

            <h6 class="my-4">
              {{ $t('customers.billing_address') }}
            </h6>
            <sw-divider class="w-full" />

            <!-- Name on account -->
            <div class="w-full md:w-1/2 md:pr-2 mt-4">
              <sw-input-group :label="$t('customers.customer_nameACH')">
                <sw-input
                  v-model.trim="authorize.name"
                  type="text"
                  name="address_name"
                  tabindex="7"
                  autocomplete="off"
                />
              </sw-input-group>
            </div>

            <!-- Country -->
            <div class="w-full md:w-1/2 md:pl-2 mt-4">
              <sw-input-group
                :error="countryIdError"
                :label="$t('customers.country')"
                required
              >
                <sw-select
                  v-model="billing_country"
                  :invalid="$v.authorize.country_id.$error"
                  :options="countries"
                  :searchable="true"
                  :show-labels="false"
                  :placeholder="$t('general.select_country')"
                  label="name"
                  track-by="id"
                  @select="countrySelected($event, 'billing')"
                />
              </sw-input-group>
            </div>

            <!-- state -->
            <div class="w-full md:w-1/2 md:pr-2 mt-4">
              <sw-input-group
                :error="stateIdError"
                :label="$t('customers.state')"
                required
              >
                <sw-select
                  v-model="billing_state"
                  :invalid="$v.authorize.state_id.$error"
                  :options="billing_states"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :tabindex="8"
                  :placeholder="$t('general.select_state')"
                  label="name"
                  track-by="id"
                />
              </sw-input-group>
            </div>

            <!-- City -->
            <div class="w-full md:w-1/2 md:pl-2 mt-4">
              <sw-input-group
                :error="cityError"
                :label="$t('customers.city')"
                required
              >
                <sw-input
                  v-model="authorize.city"
                  name="city"
                  type="text"
                  tabindex="10"
                  autocomplete="off"
                />
              </sw-input-group>
            </div>

            <!-- Address -->
            <div class="w-full md:w-1/2 md:pr-2 mt-4">
              <sw-input-group
                :label="$t('customers.address')"
                :error="billAddress1Error"
                required
              >
                <sw-textarea
                  v-model.trim="authorize.address_street_1"
                  :invalid="$v.authorize.address_street_1.$error"
                  :placeholder="$t('general.street_1')"
                  type="text"
                  name="billing_street1"
                  rows="3"
                  tabindex="11"
                  @input="$v.authorize.address_street_1.$touch()"
                />
              </sw-input-group>
            </div>

            <!-- street 2 -->
            <div class="w-full md:w-1/2 md:pl-2 pt-6 mt-4">
              <sw-input-group :error="billAddress2Error">
                <sw-textarea
                  v-model.trim="authorize.address_street_2"
                  :placeholder="$t('general.street_2')"
                  type="text"
                  name="billing_street2"
                  rows="3"
                  tabindex="12"
                  @input="$v.authorize.address_street_2.$touch()"
                />
              </sw-input-group>
            </div>

            <!-- phone -->
            <div class="w-full md:w-1/2 md:pr-2 mt-4">
              <sw-input-group :label="$t('customers.phone')">
                <sw-input
                  v-model.trim="authorize.phone"
                  type="text"
                  name="phone"
                  tabindex="13"
                  autocomplete="off"
                />
              </sw-input-group>
            </div>

            <!-- zip code -->
            <div class="w-full md:w-1/2 md:pl-2 mt-4">
              <sw-input-group
                :label="$t('customers.zip_code')"
                :error="zipError"
                required
              >
                <sw-input
                  tabindex="14"
                  v-model.trim="authorize.zip"
                  :invalid="$v.authorize.zip.$error"
                  type="text"
                  name="zip"
                  autocomplete="off"
                />
              </sw-input-group>
            </div>

            <!-- update billing info -->
            <!-- <div
              class="
                w-full
                md:w-1/2 md:pr-2
                flex flex-wrap
                mt-2
                items-end
                justify-start
                mt-4
              "
            >
              <sw-switch
                v-model="updatebillinginformation"
                @change="Updateoptionchace"
              />
              <p class="leading-snug text-black box-title ml-4">
                {{ $t('customers.update_billing_info') }}
              </p>
            </div> -->

            <!-- save create account -->
            <!-- <div
              class="
                w-full
                md:w-1/2 md:pl-2
                flex
                mt-2
                items-end
                justify-start
                mt-4
              "
            >
              <sw-switch v-model="createaccount" @change="Createoptionchace" />
              <p class="leading-snug text-black box-title ml-4">
                {{ $t('customers.save_create_account') }}
              </p>
            </div> -->

            <sw-button
              :loading="isLoading"
              :disabled="isLoading"
              variant="primary"
              type="submit"
              size="lg"
              class="hidden sm:flex mt-4"
            >
              <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
              {{ $t('payments.record_payment') }}
            </sw-button>
          </div>
          <div class="w-full flex flex-wrap mt-5">
            <!-- Select card -->
            <!-- <div class="w-full md:w-1/2 md:pr-2">
              <sw-input-group
                class="mb-4"
                v-if="
                  (this.is_authorize &&
                    this.isEdit &&
                    !this.type_ach &&
                    this.type_cc) ||
                  (this.is_paypal &&
                    this.isEdit &&
                    !this.type_ach &&
                    this.type_cc) ||
                  (this.is_authorize &&
                    !this.isEdit &&
                    !this.type_ach &&
                    this.type_cc) ||
                  (this.is_paypal &&
                    !this.isEdit &&
                    !this.type_ach &&
                    this.type_cc)
                "
                :label="$t('payments.select_cards')"
              >
                <sw-select
                  v-model="card"
                  :options="cardList"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="false"
                  :disabled="isEdit"
                  :placeholder="$t('payments.select_cards')"
                  label="card_number_cvv"
                  track-by="id"
                  @select="selectItemCard"
                  :loading="isLoadingPayments"
                  class="mt-2"
                />
              </sw-input-group>
            </div> -->

            <!-- type of card -->
            <div
              v-if="
                this.add_payment_gateway_select &&
                this.isEdit &&
                !this.type_ach &&
                this.type_cc
              "
              class="w-full md:w-1/2 md:pl-2"
            >
              <sw-input-group
                :label="$t('settings.payment_gateways.credit_cards')"
                class="mb-4"
              >
                <sw-select
                  v-model="formData.credit_cards"
                  :options="credit_cards"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :disabled="isEdit"
                  :placeholder="$t('items.select_a_type')"
                  class="mt-2"
                  label="name"
                />
              </sw-input-group>
            </div>

            <!-- type of card -->
            <div
              v-if="
                this.add_payment_gateway_select &&
                !this.isEdit &&
                !this.type_ach &&
                this.type_cc
              "
              class="w-full md:w-1/2 md:pl-2"
            >
              <sw-input-group
                :label="$t('settings.payment_gateways.credit_cards')"
                class="mb-4"
                required
              >
                <sw-select
                  v-model="formData.credit_cards"
                  :options="credit_cards"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :disabled="isEdit"
                  :placeholder="$t('items.select_a_type')"
                  class="mt-2"
                  label="name"
                />
              </sw-input-group>
            </div>
          </div>
          <!-- END PAYMENT GATEWAYS -->

          <div
            class="w-full flex flex-wrap col-span-2"
            v-if="
              (this.is_authorize && !this.type_ach && this.type_cc) ||
              (this.is_paypal && !this.isEdit && !this.type_ach && this.type_cc)
            "
          >
            <div class="w-full md:w-1/2 md:pr-2 mb-4">
              <sw-input-group :label="$t('authorize.cc_number')">
                <sw-input
                  v-model="authorize.card_number"
                  :disabled="isEdit"
                  class="mt-1"
                  focus
                  :type="showCardFieldHide ? 'password' : 'text'"
                  name="card_number"
                  autocomplete="off"
                >
                  <template v-slot:rightIcon>
                    <eye-off-icon
                      v-if="showCardFieldHide"
                      class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="showCardFieldHide = !showCardFieldHide"
                    />
                    <eye-icon
                      v-else
                      class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="showCardFieldHide = !showCardFieldHide"
                    />
                  </template>
                </sw-input>
              </sw-input-group>
            </div>

            <div class="w-full md:w-1/2 md:pl-2 mb-4">
              <sw-input-group
                :label="$t('authorize.date')"
                :error="expirationDateError"
                required
              >
                <div class="flex">
                  <sw-select
                    placeholder="MM"
                    class="mr-1"
                    :searchable="true"
                    :allow-empty="false"
                    v-model="dateExpirationMonth"
                    :options="monthsOptions"
                  />

                  <sw-select
                    placeholder="YYYY"
                    class="ml-1"
                    :searchable="true"
                    :allow-empty="false"
                    v-model="dateExpirationYear"
                    :options="yearsOptions"
                  />
                </div>
              </sw-input-group>
            </div>

            <div class="w-full md:w-1/2 md:pr-2 mb-4">
              <sw-input-group
                :label="$t('authorize.cvv')"
                :error="CvVError"
                required
                class="mt-1"
              >
                <sw-input
                  v-model="authorize.cvv"
                  :invalid="$v.authorize.cvv.$error"
                  class="mt-1"
                  focus
                  :type="showCvvFieldHide ? 'password' : 'text'"
                  name="cvv"
                  autocomplete="off"
                  @input="$v.authorize.cvv.$touch()"
                >
                  <template v-slot:rightIcon>
                    <eye-off-icon
                      v-if="showCvvFieldHide"
                      class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="showCvvFieldHide = !showCvvFieldHide"
                    />
                    <eye-icon
                      v-else
                      class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="showCvvFieldHide = !showCvvFieldHide"
                    />
                  </template>
                </sw-input>
              </sw-input-group>
            </div>

            <div class="w-full md:w-1/2 md:pl-2 mb-4">
              <sw-input-group
                :label="$t('authorize.email')"
                :error="emailError"
                required
                class="mt-1"
              >
                <sw-input
                  v-model.trim="authorize.payer_email"
                  :invalid="$v.authorize.payer_email.$error"
                  class="mt-1"
                  type="text"
                  name="payer_email"
                  tabindex="3"
                  autocomplete="off"
                  @input="$v.authorize.payer_email.$touch()"
                />
              </sw-input-group>
            </div>
            <!-- END AUTHORIZE -->

            <!-- Billing Address  -->
            <h6 class="w-full my-4">
              {{ $t('customers.billing_address') }}
            </h6>
            <sw-divider />

            <div class="w-full md:w-1/2 md:pr-2 mt-4">
              <sw-input-group :label="$t('customers.customer_addres_name')">
                <sw-input
                  v-model.trim="authorize.name"
                  type="text"
                  name="address_name"
                  tabindex="7"
                  autocomplete="off"
                />
              </sw-input-group>
            </div>

            <!-- Country -->
            <div class="w-full md:w-1/2 md:pl-2 mt-4">
              <sw-input-group
                :error="countryIdError"
                :label="$t('customers.country')"
                required
              >
                <sw-select
                  v-model="billing_country"
                  :invalid="$v.authorize.country_id.$error"
                  :options="countries"
                  :searchable="true"
                  :show-labels="false"
                  :placeholder="$t('general.select_country')"
                  label="name"
                  track-by="id"
                  @select="countrySelected($event, 'billing')"
                />
              </sw-input-group>
            </div>

            <!-- state -->
            <div class="w-full md:w-1/2 md:pr-2 mt-4">
              <sw-input-group
                :error="stateIdError"
                :label="$t('customers.state')"
                required
              >
                <sw-select
                  v-model="billing_state"
                  :invalid="$v.authorize.state_id.$error"
                  :options="billing_states"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :tabindex="8"
                  :placeholder="$t('general.select_state')"
                  label="name"
                  track-by="id"
                />
              </sw-input-group>
            </div>

            <!-- City -->
            <div class="w-full md:w-1/2 md:pl-2 mt-4">
              <sw-input-group
                :error="cityError"
                :label="$t('customers.city')"
                required
              >
                <sw-input
                  v-model="authorize.city"
                  name="city"
                  type="text"
                  tabindex="10"
                  autocomplete="off"
                />
              </sw-input-group>
            </div>

            <!-- Address -->
            <div class="w-full md:w-1/2 md:pr-2 mt-4">
              <sw-input-group
                :label="$t('customers.address')"
                :error="billAddress1Error"
                required
              >
                <sw-textarea
                  v-model.trim="authorize.address_street_1"
                  :invalid="$v.authorize.address_street_1.$error"
                  :placeholder="$t('general.street_1')"
                  type="text"
                  name="billing_street1"
                  rows="3"
                  tabindex="11"
                  @input="$v.authorize.address_street_1.$touch()"
                />
              </sw-input-group>
            </div>

            <!-- street 2 -->
            <div class="w-full md:w-1/2 md:pl-2 pt-6 mt-4">
              <sw-input-group :error="billAddress2Error">
                <sw-textarea
                  v-model.trim="authorize.address_street_2"
                  :placeholder="$t('general.street_2')"
                  type="text"
                  name="billing_street2"
                  rows="3"
                  tabindex="12"
                  @input="$v.authorize.address_street_2.$touch()"
                />
              </sw-input-group>
            </div>

            <!-- phone -->
            <div class="w-full md:w-1/2 md:pr-2 mt-4">
              <sw-input-group :label="$t('customers.phone')">
                <sw-input
                  v-model.trim="authorize.phone"
                  type="text"
                  name="phone"
                  tabindex="13"
                  autocomplete="off"
                />
              </sw-input-group>
            </div>

            <!-- zip code -->
            <div class="w-full md:w-1/2 md:pl-2 mt-4">
              <sw-input-group
                :label="$t('customers.zip_code')"
                :error="zipError"
                required
              >
                <sw-input
                  tabindex="14"
                  v-model.trim="authorize.zip"
                  :invalid="$v.authorize.zip.$error"
                  type="text"
                  name="zip"
                  autocomplete="off"
                />
              </sw-input-group>
            </div>

            <!-- update billing info -->
            <!-- <div
              class="
                w-full
                md:w-1/2 md:pr-2
                flex flex-wrap
                mt-2
                items-end
                justify-start
                mt-4
              "
            >
              <sw-switch
                v-model="updatebillinginformation"
                @change="Updateoptionchace"
              />
              <p class="leading-snug text-black box-title ml-4">
                {{ $t('customers.update_billing_info') }}
              </p>
            </div> -->

            <!-- save create account -->
            <!-- <div
              class="
                w-full
                md:w-1/2 md:pl-2
                flex
                mt-2
                items-end
                justify-start
                mt-4
              "
            >
              <sw-switch v-model="createaccount" @change="Createoptionchace" />
              <p class="leading-snug text-black box-title ml-4">
                {{ $t('customers.save_create_account') }}
              </p>
            </div> -->
            <sw-button
              :loading="isLoading"
              :disabled="isLoading"
              variant="primary"
              type="submit"
              size="lg"
              class="hidden sm:flex md:mt-4"
            >
              <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
              {{ $t('payments.record_payment') }}
            </sw-button>
          </div>

          </div>

          <!-- END AUTHORIZE  

          <div v-if="customFields.length > 0">
            <div class="grid gap-6 mt-6 grid-col-1 md:grid-cols-2">
              <sw-input-group
                v-for="(field, index) in customFields"
                :label="field.label"
                :required="field.is_required ? true : false"
                :key="index"
              >
                <component
                  :type="field.type.label"
                  :field="field"
                  :is-edit="isEdit"
                  :is="field.type + 'Field'"
                  :invalid-fields="invalidFields"
                  @update="setCustomFieldValue"
                />
              </sw-input-group>
            </div>
          </div>-->

          <!-- integracion de paypal -->

          <!-- <sw-popup
            ref="notePopup"
            class="my-6 text-sm font-semibold leading-5 text-primary-400"
          >
            <div slot="activator" class="float-right mt-1">
              + {{ $t('general.insert_note') }}
            </div>
            <note-select-popup type="Payment" @select="onSelectNote" />
          </sw-popup>

          <sw-input-group :label="$t('payments.note')" class="mt-6 mb-4">
            <base-custom-input
              v-model="formData.notes"
              :fields="PaymentFields"
              class="mb-4"
            />
          </sw-input-group> -->

          <sw-button
            :disabled="isLoading"
            :loading="isLoading"
            variant="primary"
            type="submit"
            class="flex w-full mt-4 sm:hidden md:hidden"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{
              isEdit ? $t('payments.update_payment') : $t('payments.save_payment')
            }}
          </sw-button>
        </sw-card>
      </form>
    </div>

    <div class="flex flex-wrap justify-center w-full" v-if="paymentSuccess">
      <sw-card class="md:w-2/3">
        <div class="flex flex-col items-center justify-center">
          <div class="flex items-center justify-center w-24 h-24 mb-4 bg-green-100 rounded-full">
            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h2 class="mb-4 text-2xl font-bold leading-normal text-gray-800">
            {{ $t('payments.payment_success') }}
          </h2>
          <p class="mb-4 text-sm font-normal leading-normal text-gray-600">
            {{ $t('payments.payment_success_message') }}
          </p>
        </div>
      </sw-card>
      
    </div>

    <div class="flex flex-wrap justify-center w-full" v-if="invoiceIsCompleted">
      <sw-card class="md:w-2/3">
        <div class="flex flex-col items-center justify-center">
          <div class="flex items-center justify-center w-24 h-24 mb-4 bg-green-100 rounded-full">
            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h2 class="mb-4 text-2xl font-bold leading-normal text-gray-800">
            The invoice with the number {{invoiceNumber}} has already been paid.
          </h2>
          <p class="mb-4 text-sm font-normal leading-normal text-gray-600">
            Greetings
          </p>
        </div>
      </sw-card>
    </div>

    <div class="flex flex-wrap justify-center w-full" v-if="invoiceIsCanceled">
      <sw-card class="md:w-2/3">
        <div class="flex flex-col items-center justify-center">
          <div class="flex items-center justify-center w-24 h-24 mb-4 bg-red-100 rounded-full">
            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h2 class="mb-4 text-2xl font-bold leading-normal text-gray-800">
            The invoice has not been found, please contact the administration
          </h2>
          <p class="mb-4 text-sm font-normal leading-normal text-gray-600">
            Greetings
          </p>
        </div>
      </sw-card>
    </div>
   
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import { ShoppingCartIcon, XCircleIcon } from '@vue-hero-icons/solid'
import CustomFieldsMixin from '../../mixins/customFields'
import { VueDatePicker } from '@mathieustan/vue-datepicker'
import '@mathieustan/vue-datepicker/dist/vue-datepicker.min.css'
import { EyeIcon, EyeOffIcon } from '@vue-hero-icons/outline'

const {
  required,
  between,
  numeric,
  email,
  minLength,
  maxLength,
} = require('vuelidate/lib/validators')

export default {
  components: {
    ShoppingCartIcon,
    XCircleIcon,
    VueDatePicker,
    EyeIcon,
    EyeOffIcon
  },
  mixins: [CustomFieldsMixin],

  data() {
    return {
      paymentMethodsOption: [],
      paymentSuccess: false,
      previewLogo: '',
      creditv: false,
      company_id: '',
      isdisableed: true,
      isedtiablefalse: false,
      formData: {
        user_id: null,
        payment_number: null,
        account_number: null,
        ACH_type: {
          value: 'checking',
          text: 'Checking',
        },
        routing_number: null,
        bank_name: null,
        num_check: null,
        payment_date: new Date(),
        amount: 100,
        customer_credit: false,
        payment_method: {
          name: null,
          account_accepted: null,
          add_payment_gateway: null,
          company_id: null,
          created_at: null,
          deleted_at: null,
          for_customer_use: null,
          generate_expense: null,
          generate_expense_id: null,
          id: null,
          payment_gateways_id: null,
          status: null,
          updated_at: null,
          void_refund: null,
          void_refund_expense_id: null,
        },
        invoice_id: null,
        notes: null,
        payment_method_id: null,
        payment_gateways: [],
        authorize: null,
        authorize_id: null,
        credit_cards: [],
        credit_card: null,
        updatebillinginformation: false,
        createaccount: false,
        transaction_status: null,
        isTransactionStatus: false,
        status_with_authorize: true,
        add_payment_gateway: 0,
        status: {
          value: 'Approved',
          text: 'Approved',
        },
        void_status_change: false,
        refunded_status_change: false,
      },
      authorize: {
        payer_email: '',
        card_number: '',
        credit_cards: '',
        cvv: '',
        date: '',
        name: null,
        country_id: null,
        state_id: null,
        city: null,
        phone: null,
        zip: null,
        address_street_1: null,
        address_street_2: null,
        country: null,
        state: null,
        type: 'billing',
        first_name: null,
        last_name: null,
        company_name: null,
        email: null,
      },
      money: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        precision: 2,
        masked: false,
      },
      customer: null,
      account: null,
      card: null,
      invoice: null,
      invoiceList: [],
      accountList: [],
      cardList: [],
      isLoading: false,
      isRequestOnGoing: false,
      fetchingPaymentMethod: false,
      maxPayableAmount: Number.MAX_SAFE_INTEGER,
      isSettingInitialData: true,
      paymentNumAttribute: null,
      paymentPrefix: '',
      PaymentFields: [
        'customer',
        'company',
        'customerCustom',
        'payment',
        'paymentCustom',
      ],
      add_payment_gateway_select: false,
      type_ach: false,
      type_cc: false,
      payment_gateways: [],
      isAuthorizeEdit: false,
      is_authorize: false,
      is_paypal: false,
      locale: { lang: 'en' },
      isTransactionStatus: false,
      transactionStatusCheck: false,
      notEditable: false,
      updatebillinginformation: false,
      createaccount: false,

      billing_state: null,
      billing_country: null,

      countries: [],
      billing_states: [],

      credit_cards: [
        { name: 'VISA', value: 'VISA' },
        { name: 'MASTERCARD', value: 'MASTERCARD' },
        { name: 'AMERICAN EXPRESS', value: 'AMERICAN EXPRESS' },
        { name: 'DISCOVER', value: 'DISCOVER' },
      ],
      status: [
        { value: 'Approved', text: 'Approved' },
        { value: 'Void', text: 'Void' },
        { value: 'Refunded', text: 'Refunded' },
        { value: 'Unapply', text: 'Unapply' },
      ],
      isTransactionFail: false,
      transactionFail: {
        payment_gateway: null,
        transaction_number: null,
        date: null,
        amount: null,
        payment_number: null,
        customer_id: null,
        invoice_id: null,
        description: null,
      },

      bank_account_type: [
        {
          value: 'checking',
          text: 'Checking',
        },
        {
          value: 'Savings',
          text: 'Savings',
        },
      ],
      paymentSuccess: false,
      isLoadingPayments: false,
      paymentPaypalProccess: false,

      // variables para show input fiedl hide or show
      showCardFieldHide: true,
      showCvvFieldHide: true,
      showAccountFieldHide: true,
      showRoutingFieldHide: true,
      // Invoice Status
      invoiceIsCompleted: false,
      invoiceIsCanceled: false,
      invoiceNumber: 0
    }
  },
  validations() {
    if (this.type_ach) {
      return {
        customer: {
          required,
        },
        formData: {
          payment_date: {
            required,
          },
          ACH_type: {
            required,
          },
          account_number: {
            required,
            minLength: minLength(9),
            maxLength: maxLength(16),
          },
          routing_number: {
            required,
            minLength: minLength(8),
            maxLength: maxLength(10),
          },
          num_check: {
            maxLength: maxLength(20),
          },
          amount: {
            required,
            between: between(1, this.maxPayableAmount + 1),
          },
        },
        authorize: {
          country_id: {
            required,
          },
          state_id: {
            required,
          },
          city: {
            required,
          },
          address_street_1: {
            required,
            maxLength: maxLength(255),
          },
          address_street_2: {
            maxLength: maxLength(255),
          },
          zip: {
            required,
          },
        },
        paymentNumAttribute: {
          required,
          numeric,
        },
      }
    } else if (this.isEdit) {
      return {
        customer: {
          required,
        },
        formData: {
          payment_date: {
            required,
          },
          amount: {
            required,
            between: between(1, this.maxPayableAmount + 1),
          },
          status: {
            required,
          },
          payment_method: {
            required,
          },
        },
        paymentNumAttribute: {
          required,
          numeric,
        },
      }
    } else if (this.isAuthorize && !this.type_ach) {
      return {
        customer: {
          required,
        },
        formData: {
          payment_date: {
            required,
          },
          amount: {
            required,
            between: between(1, this.maxPayableAmount + 1),
          },
          status: {
            required,
          },
          credit_cards: {
            required,
          },
        },
        authorize: {
          payer_email: {
            required,
            email,
          },
          card_number: {
            required,
            numeric,
            minLength: minLength(13),
            maxLength: maxLength(19),
          },
          cvv: {
            required,
            numeric,
            minLength: minLength(3),
            maxLength: maxLength(4),
          },
          date: {
            required,
          },
          country_id: {
            required,
          },
          state_id: {
            required,
          },
          city: {
            required,
          },
          address_street_1: {
            required,
            maxLength: maxLength(255),
          },
          address_street_2: {
            maxLength: maxLength(255),
          },
          zip: {
            required,
          },
        },
        paymentNumAttribute: {
          required,
          numeric,
        },
      }
    } else {
      return {
        customer: {
          required,
        },
        formData: {
          payment_date: {
            required,
          },
          amount: {
            required,
            between: between(1, this.maxPayableAmount + 1),
          },
          status: {
            required,
          },
          payment_method: {
            required,
          },
        },
        paymentNumAttribute: {
          required,
          numeric,
        },
      }
    }
  },
  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),
    ...mapGetters('payment', ['paymentModes', 'selectedNote']),
    ...mapGetters('customer', ['customers']),
    dateExpirationYear: {
      get() {
        return this.authorize.date.split('-')[0]
      },
      set(year) {
        this.authorize.date = year + '-' + this.authorize.date.split('-')[1]
        const currentYear = new Date().getFullYear()
        if (year == currentYear) {
          this.authorize.date = year + '-' + this.monthsOptions[0]
        }
      },
    },
    dateExpirationMonth: {
      get() {
        return this.authorize.date.split('-')[1]
      },
      set(month) {
        this.authorize.date = this.authorize.date.split('-')[0] + '-' + month
      },
    },
    // generador de los 15 años para el select de fecha de expiración de la tarjeta de crédito
    yearsOptions() {
      const years = []
      const currentYear = new Date().getFullYear()
      for (let i = currentYear; i < currentYear + 15; i++) {
        years.push(`${i}`)
      }
      return years
    },
    // generador de los 12 meses del año formato MM
    monthsOptions() {
      const months = []
      const yearSelect = this.authorize.date.split('-')[0]
      const currentMonth =
        yearSelect == new Date().getFullYear() ? new Date().getMonth() + 1 : 1
      for (let i = 1; i <= 12; i++) {
        months.push(i < 10 ? `0${i}` : `${i}`)
      }
      return months
    },

    amount: {
      get: function () {
        return this.formData.amount / 100
      },
      set: function (newValue) {
        this.formData.amount = Math.round(newValue * 100)
      },
    },
    pageTitle() {
      if (this.$route.name === 'payments.edit') {
        return this.$t('payments.edit_payment')
      }
      return this.$t('payments.new_payment')
    },
    isEdit() {
      if (this.$route.name === 'payments.edit') {
        return true
      }
      return false
    },
    customerCurrency() {
      if (this.customer && this.customer.currency) {
        return {
          decimal: this.customer.currency.decimal_separator,
          thousands: this.customer.currency.thousand_separator,
          prefix: this.customer.currency.symbol + ' ',
          precision: this.customer.currency.precision,
          masked: false,
        }
      } else {
        return this.defaultCurrencyForInput
      }
    },
    customerError() {
      if (!this.$v.customer.$error) {
        return ''
      }

      if (!this.$v.customer.required) {
        return this.$tc('validation.required')
      }
    },
    DateError() {
      if (!this.$v.formData.payment_date.$error) {
        return ''
      }
      if (!this.$v.formData.payment_date.required) {
        return this.$t('validation.required')
      }
    },
    amountError() {
      if (!this.$v.formData.amount.$error) {
        return ''
      }

      if (!this.$v.formData.amount.required) {
        return this.$t('validation.required')
      }

      if (
        !this.$v.formData.amount.between &&
        this.$v.formData.amount.numeric &&
        this.amount <= 0
      ) {
        return this.$t('validation.payment_greater_than_zero')
      }

      if (!this.$v.formData.amount.between && this.amount > 0) {
        return this.$t('validation.payment_greater_than_due_amount')
      }
    },
    paymentNumError() {
      if (!this.$v.paymentNumAttribute.$error) {
        return ''
      }

      if (!this.$v.paymentNumAttribute.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.paymentNumAttribute.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
    creditCardError() {
      if (!this.isEdit) {
        if (!this.$v.formData.credit_cards.required) {
          return this.$tc('validation.required')
        }
      }
    },
    emailError() {
      if (this.isAuthorize) {
        if (!this.$v.authorize.payer_email.$error) {
          return ''
        }
        if (!this.$v.authorize.payer_email.required) {
          return this.$tc('validation.required')
        }
        if (!this.$v.authorize.payer_email.email) {
          return this.$tc('validation.email_incorrect')
        }
      }
    },
    ccNumberError() {
      if (this.isAuthorize) {
        if (!this.$v.authorize.card_number.$error) {
          return ''
        }
        if (!this.$v.authorize.card_number.required) {
          return this.$tc('validation.required')
        }
        if (!this.$v.authorize.card_number.numeric) {
          return this.$tc('validation.numbers_only')
        }
        if (!this.$v.authorize.card_number.minLength) {
          return this.$tc(
            'validation.name_min_length',
            this.$v.authorize.card_number.$params.minLength.min,
            { count: this.$v.authorize.card_number.$params.minLength.min }
          )
        }
        if (!this.$v.authorize.card_number.maxLength) {
          return this.$t('authorize.cc_number_maxLength')
        }
      }
    },
    CvVError() {
      if (this.isAuthorize) {
        if (!this.$v.authorize.cvv.$error) {
          return ''
        }
        if (!this.$v.authorize.cvv.required) {
          return this.$tc('validation.required')
        }
        if (!this.$v.authorize.cvv.numeric) {
          return this.$tc('validation.numbers_only')
        }
        if (!this.$v.authorize.cvv.minLength) {
          return this.$tc(
            'validation.name_min_length',
            this.$v.authorize.cvv.$params.minLength.min,
            { count: this.$v.authorize.cvv.$params.minLength.min }
          )
        }
        if (!this.$v.authorize.cvv.maxLength) {
          return this.$t('authorize.cvv_maxLength')
        }
      }
    },
    countryIdError() {
      if (this.isAuthorize) {
        if (!this.$v.authorize.country_id.$error) {
          return ''
        }
        if (!this.$v.authorize.country_id.required) {
          return this.$tc('validation.required')
        }
      }
    },
    stateIdError() {
      if (this.isAuthorize) {
        if (!this.$v.authorize.state_id.$error) {
          return ''
        }
        if (!this.$v.authorize.state_id.required) {
          return this.$tc('validation.required')
        }
      }
    },
    cityError() {
      if (this.isAuthorize) {
        if (!this.$v.authorize.city.$error) {
          return ''
        }
        if (!this.$v.authorize.city.required) {
          return this.$tc('validation.required')
        }
      }
    },
    billAddress1Error() {
      if (this.isAuthorize) {
        if (!this.$v.authorize.address_street_1.$error) {
          return ''
        }
        if (!this.$v.authorize.address_street_1.required) {
          return this.$tc('validation.required')
        }
        if (!this.$v.authorize.address_street_1.maxLength) {
          return this.$t('validation.address_maxlength')
        }
      }
    },
    billAddress2Error() {
      if (this.isAuthorize) {
        if (!this.$v.authorize.address_street_2.$error) {
          return ''
        }
        if (!this.$v.authorize.address_street_2.maxLength) {
          return this.$t('validation.address_maxlength')
        }
      }
    },
    zipError() {
      if (this.isAuthorize) {
        if (!this.$v.authorize.zip.$error) {
          return ''
        }
        if (!this.$v.authorize.zip.required) {
          return this.$tc('validation.required')
        }
      }
    },
    expirationDateError() {
      if (this.isAuthorize) {
        if (this.isAuthorize) {
          if (!this.$v.authorize.date.$error) {
            return ''
          }
          if (!this.$v.authorize.date.required) {
            return this.$tc('validation.required')
          }
        }
      }
    },
    statusError() {
      if (this.isEdit) {
        if (this.$v.formData.status && !this.$v.formData.status.$error) {
          return ''
        }
        if (this.$v.formData.status && !this.$v.formData.status.required) {
          return this.$tc('validation.required')
        }
      }
    },
    paymentMethodError() {
      if (this.isEdit) {
        if (
          this.$v.formData.payment_method &&
          !this.$v.formData.payment_method.$error
        ) {
          return ''
        }
        if (
          this.$v.formData.payment_method &&
          !this.$v.formData.payment_method.required
        ) {
          return this.$tc('validation.required')
        }
      }
    },
    isAuthorize() {
      if (this.isEdit && this.isAuthorizeEdit) {
        this.is_authorize = true
        return true
      }
      if (this.formData.payment_gateways) {
        if (this.formData.payment_gateways.name === 'Authorize') {
          if (this.customer) {
            let params = {
              id: this.customer.id,
            }
            this.loadCustomerData(params)
            //console.log('si carga authorize')
          }
          this.is_authorize = true
          this.is_paypal = false
          return true
        } else if (this.formData.payment_gateways.name === 'Paypal') {
          if (this.customer) {
            let params = {
              id: this.customer.id,
            }
            this.loadCustomerData(params)
          }
          this.is_paypal = true
          this.is_authorize = false
          //console.log('yes paypal')
          return true
        } else {
          this.is_paypal = false
          this.is_authorize = false
          return false
        }
      }
      return false
    },
    ACHTypeError() {
      if (!this.$v.formData.ACH_type.$error) {
        return ''
      }
      if (!this.$v.formData.ACH_type.required) {
        return this.$tc('validation.required')
      }
    },
    accountNumberError() {
      if (!this.$v.formData.account_number.$error) {
        return ''
      }
      if (!this.$v.formData.account_number.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.account_number.minLength) {
        return this.$t('validation.account_number_minLength')
      }
      if (!this.$v.formData.account_number.maxLength) {
        return this.$t('validation.account_number_maxLength')
      }
    },
    numCheckError() {},
    routingNumberError() {
      if (!this.$v.formData.routing_number.$error) {
        return ''
      }
      if (!this.$v.formData.routing_number.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.routing_number.minLength) {
        return this.$t('validation.routing_number_minLength')
      }
      if (!this.$v.formData.routing_number.maxLength) {
        return this.$t('validation.routing_number_maxLength')
      }
    },
    codePayment() {
      return this.paymentPrefix + '-' + this.paymentNumAttribute
    },
    invoice_number() {
      return this.invoice ? this.invoice.invoice_number : ''
    },
    voidStatusChange() {
      return this.formData.status.value === 'Void'
    },
    refundedStatusChange() {
      return this.formData.status.value === 'Refunded'
    },
    urlLogo() {
      if(this.previewLogo == ''){
        return ''
      }else if(this.previewLogo){
        return this.previewLogo
      }else{
        return '/assets/img/logo-corebill.png'
      }
    },
  },
  watch: {
    creditv(val) {
      this.formData.customer_credit = val
    },
    billing_country(newCountry) {
      if (newCountry) {
        this.authorize.country_id = newCountry.id
        this.authorize.country = newCountry.name
        this.isDisabledBillingState = false
      } else {
        this.authorize.country_id = null
      }
    },
    billing_state(newState) {
      if (newState) {
        this.authorize.state_id = newState.id
        this.authorize.state = newState.name
      } else {
        this.authorize.state_id = null
      }
    },
    customer(newValue) {
      this.isLoadingPayments = true
      this.formData.user_id = newValue.id
      this.formData.customcode = newValue.customcode
      this.creditv = false

      // if (!this.isEdit) {
      //   if (this.isSettingInitialData) {
      //     this.isSettingInitialData = false
      //   } else {
      //     this.invoice = null
      //     this.formData.invoice_id = null
      //   }
      //   this.formData.amount = 0
      //   this.invoiceList = []
      //   this.fetchCustomerInvoices(newValue.id)
      //   this.accountList = []
      //   this.fetchCustomerAccounts(newValue.id)
      // }
    },
    selectedNote() {
      if (this.selectedNote) {
        this.formData.notes = this.selectedNote
      }
    },
    invoice(newValue) {
      //console.log('invoice watch')
      //console.log(newValue)
      if (newValue) {
        this.formData.invoice_id = newValue.id
        this.authorize.invoice_number = newValue.invoice_number
        // if (!this.isEdit) {
        //   this.setPaymentAmountByInvoiceData(newValue.id)
        // }
        // en caso de que el monto de la factura sea menor que el credit del cliente
        if (
          this.customer.balance > 1  &&
          !this.isEdit
        ) {
          this.creditv = true
          this.formData.payment_method = null
        } else {
          this.creditv = false
        }
      } else {
        this.creditv = false
        this.formData.amount = 100 // 1
      }
    },
  },
  async mounted() {   
    // this.$v.formData.$reset()
    // this.resetSelectedNote()
    // this.$nextTick(() => {
    //   this.loadData()
      if (this.$route.params.unique_hash) {       
        await this.setInvoicePaymentData(this.$route.params.unique_hash)       
        await this.checkAutoGenerate()       
        const paymentCreditcard = await this.fetchPaymentModesWithoutLogin({ 
          whitoutLogin: true,
          search: 'Credit Card',
          limit: 'all'
         })        
         await this.PaymentModeSelected(paymentCreditcard.data.paymentMethods)
      }
    // })
  },
  created() {
    this.fetchInitData()
    this.loadDataPayment()
    this.getLogo()
  },
  methods: {
    ...mapActions('user', ['fetchCompanyLogo']),
    ...mapActions('invoice', ['fetchInvoice', 'fetchInvoices', 'fetchInvoiceForUniqueHash']),

    ...mapActions('paymentAccounts', [
      'fetchPaymentAccounts',
      'fetchPaymentAccount',
    ]),

    ...mapActions('payment', [
      'addPaymentWithoutLogin',
      'updatePayment',
      'fetchPayment',
      'fetchPaymentModesWithoutLogin',
      'resetSelectedNote',
      'paymentModesWithoutLogin',
      'processPayment'
    ]),
    ...mapActions('paymentGateways', ['fetchPaymentGateways', 'fetchPaymentGatewaysWithoutLogin']),

    ...mapActions('authorizations', [
      'addAuthorizeWithoutLogin',
      'saveAuthorizeDBWithoutLogin',
      'voidAuthorize',
      'refundedAuthorize',
      'addAuthorizeACH',
      'saveAuthorizeACH',
      'addAuthorizePaypal',
      'chargePaypalPro',
    ]),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('modal', ['openModal']),

    ...mapActions('customer', ['fetchCustomers', 'fetchCustomer', 'fetchCustomerWithoutLogin']),

    ...mapActions('failedPaymentHistory', ['SavePayFailedWithoutLogin']),

    async loadDataPayment() {
      let data = {
        limit: 'all',
        orderBy: 'asc',
      }

      let responsePaymentWithOutLogin = await this.paymentModesWithoutLogin(data)
      //console.log('responsePaymentWithLogin')
      //console.log(responsePaymentWithOutLogin)
      this.paymentMethodsOption = responsePaymentWithOutLogin.data.paymentMethods
    },

    async getLogo() {
      try{
        // this.loadingInfo = true
        const response = await this.fetchCompanyLogo()
          this.previewLogo = response.data?.logo
      } catch (error) {
        console.log(error)
      }finally{
        // this.loadingInfo = false
      }
    },

    invoiceWithAmount({ invoice_number, due_amount }) {
      if (invoice_number) {
        return `${invoice_number} (${this.$utils.formatGraphMoney(
          due_amount,
          this.customer.currency
        )})`
      } else {
        return '- Seleccione -'
      }
    },

    async countrySelected(country, type) {
      const vm = this
      vm.isLoading = true
      if (type == 'billing') {
        vm.billing_states = []
      }
      let res = await window.axios.get('/api/v1/states/' + country.code)
      if (res) {
        if (type == 'billing') {
          vm.billing_states = res.data.states
        }
      }
      vm.isLoading = false
    },

    async loadCustomerData(params) {
     
      let response = await this.fetchCustomerWithoutLogin(params)
      this.authorize.payer_email = response.data.customer.email

      this.authorize.name = null
      this.authorize.country_id = null
      this.authorize.state_id = null
      this.authorize.city = null
      this.authorize.phone = null
      this.authorize.zip = null
      this.authorize.address_street_1 = null
      this.authorize.address_street_2 = null
      this.authorize.country = null
      this.authorize.state = null
      this.authorize.first_name = null
      this.authorize.last_name = null
      this.authorize.company_name = null
      this.billing_country = null
      this.billing_state = null

      this.authorize.first_name = response.data.customer.first_name
      this.authorize.last_name = response.data.customer.last_name
      this.authorize.company_name = response.data.customer.company.name
      this.authorize.email = response.data.customer.email

      if (response.data.customer.billing_address) {
        this.authorize.name = response.data.customer.contact_name
        this.authorize.country_id =
          response.data.customer.billing_address.country_id
        this.authorize.state_id =
          response.data.customer.billing_address.state_id
        this.authorize.city = response.data.customer.billing_address.city
        this.authorize.phone = response.data.customer.billing_address.phone
        this.authorize.zip = response.data.customer.billing_address.zip
        this.authorize.address_street_1 =
          response.data.customer.billing_address.address_street_1
        this.authorize.address_street_2 =
          response.data.customer.billing_address.address_street_2
        this.authorize.country =
          response.data.customer.billing_address.country.name
        this.authorize.state = response.data.customer.billing_address.state.name

        if (response.data.customer.billing_address.country_id) {
          this.billing_country = response.data.customer.billing_address.country
          this.countrySelected(this.billing_country, 'billing')
        }
        if (response.data.customer.billing_address.state_id) {
          this.billing_state = response.data.customer.billing_address.state
        }
      }
    },
    async addPaymentMode() {
      this.openModal({
        title: this.$t('settings.customization.payments.add_payment_mode'),
        componentName: 'PaymentMode',
      })
    },
    async checkAutoGenerate() {
      let response1 = await axios.get('/api/v1/next-number?key=payment', {
        headers: {
          company: this.company_id,
        },
      })

      this.paymentPrefix = response1.data.prefix 
      this.paymentNumAttribute = response1.data.nextNumber

    },
    async loadData() {
      if (this.isEdit) {
        this.isRequestOnGoing = true

        let response = await this.fetchPayment(this.$route.params.id)

        // console.log(response)
        this.formData = { ...this.formData, ...response.data.payment }

        if (this.formData.payment_method_id) {
          //console.log(this.formData)
          this.fetchingPaymentMethod = true
          let payments = await this.fetchPaymentModes({ limit: 'all' })
          //console.log(this.formData)
          this.formData.payment_method =
            payments.data.paymentMethods.data.filter(
              (payment) => payment.id == this.formData.payment_method_id
            )
          //console.log(this.formData)
          this.PaymentModeSelected(this.formData.payment_method[0])
          this.fetchingPaymentMethod = false
        }
        //return false;
        if (this.formData.user_id) {
          await this.fetchCustomers({ limit: 'all' })
        }
        this.customer = response.data.payment.user
        this.formData.payment_date = moment(
          response.data.payment.payment_date
        ).format('YYYY-MM-DD')
        this.formData.credit_cards = response.data.payment.credit_card
        this.formData.amount = parseFloat(response.data.payment.amount)
        this.paymentPrefix = response.data.payment_prefix
        this.paymentNumAttribute = response.data.nextPaymentNumber
        // this.formData.payment_method = response.data.payment.payment_method
        this.formData.payment_method_id =
          response.data.payment.payment_method_id
        // console.log(this.formData);

        //console.log(this.formData.payment_method)

        if (response.data.payment.invoice !== null) {
          this.maxPayableAmount =
            parseInt(response.data.payment.amount) +
            parseInt(response.data.payment.invoice.due_amount)
          this.invoice = response.data.payment.invoice
        }

        if (response.data.payment.credit_card) {
          let type_name = ''

          switch (this.formData.credit_cards) {
            case 'AMERICAN EXPRESS':
              type_name = 'AMERICAN EXPRESS'
              break
            case 'VISA':
              type_name = 'VISA'
              break
            case 'MASTERCARD':
              type_name = 'MASTERCARD'
              break
            case 'DISCOVER':
              type_name = 'DISCOVER'
              break
            default:
              break
          }
          this.formData.credit_cards = {
            name: type_name,
            value: this.formData.credit_cards,
          }
        }

        const findIndex = this.status.findIndex(
          (item) => item.value == this.formData.transaction_status
        )

        if (findIndex !== -1) {
          this.formData.status = this.status[findIndex]
        }

        if (
          this.invoice != null &&
          this.formData.payment_method &&
          this.formData.transaction_status == 'Approved'
        ) {
          //console.log('entrow con mmetodo 1 invoice')
          //console.log(this.formData.payment_method)
          let gateway = 0
          let typegateway = 'N'
          let payments = this.formData.payment_method
          Array.prototype.forEach.call(payments, (user) => {
            // ...
            //console.log(user)

            if (user.add_payment_gateway == 1 && user.account_accepted != 'N') {
              this.status = [
                { value: 'Approved', text: 'Approved' },
                { value: 'Void', text: 'Void' },
                { value: 'Refunded', text: 'Refunded' },
                { value: 'Unapply', text: 'Unapply' },
              ]
            }

            if (
              user.add_payment_gateway == null ||
              user.add_payment_gateway == 0 ||
              user.account_accepted == 'N'
            ) {
              this.status = [
                { value: 'Approved', text: 'Approved' },

                { value: 'Unapply', text: 'Unapply' },
              ]
            }
          })
          //console.log(this.formData.payment_method)
        }

        if (
          this.invoice != null &&
          this.formData.payment_method == null &&
          this.formData.transaction_status == 'Approved'
        ) {
          //console.log('entro sin metodo')
          this.status = [
            {
              value: 'Approved',
              text: 'Approved',
            },
            {
              value: 'Unapply',
              text: 'Unapply',
            },
          ]
        }

        if (
          this.invoice == null &&
          this.formData.payment_method &&
          this.formData.transaction_status == 'Approved'
        ) {
          //console.log('entrow con mmetodo sin invoice')
          //console.log(this.formData.payment_method)
          this.status = [
            { value: 'Approved', text: 'Approved' },
            { value: 'Void', text: 'Void' },
          ]
        }

        if (this.formData.transaction_status != 'Approved') {
          this.notEditable = true
          this.isedtiablefalse = true
          this.status = [
            {
              value: this.formData.transaction_status,
              text: this.formData.transaction_status,
            },
            {
              value: this.formData.transaction_status,
              text: this.formData.transaction_status,
            },
          ]
        }

        let res = await this.fetchCustomFields({
          type: 'Payment',
          limit: 'all',
        })

        this.setEditCustomFields(
          response.data.payment.fields,
          res.data.customFields.data
        )
        //console.log('Console final')
        //console.log(this.formData.payment_method)
        this.isRequestOnGoing = false
      } else {
        this.isRequestOnGoing = true
        this.checkAutoGenerate()
        this.setInitialCustomFields('Payment')
        this.formData.payment_date = moment().format('YYYY-MM-DD')
        this.fetchPaymentModes({ limit: 'all' })
        await this.fetchCustomers({ limit: 'all' })
        if (this.$route.query.customer) {
          this.setPaymentCustomer(parseInt(this.$route.query.customer))
        }
        this.isRequestOnGoing = false
      }
      return true
    },
    async fetchInitData() {
      this.initLoad = true
      let res = await window.axios.get('/api/v1/countries')
      if (res) {
        this.countries = res.data.countries
      }
      this.initLoad = false
    },
    setPaymentCustomer(id) {
      this.customer = this.customers.find((c) => {
        return c.id === id
      })
    },
    async setInvoicePaymentData(unique_hash) {
          
      let data = await this.fetchInvoiceForUniqueHash(unique_hash)
      
      if(data.data.invoice == null)
      {
        this.invoiceIsCanceled = true
        return
      }     

      this.invoiceNumber = data.data.invoice.invoice_number

      if(data.data.invoice.status === "COMPLETED" && data.data.invoice.paid_status === "PAID")
      {    
        this.invoiceIsCompleted = true
      }    

      this.customer = data.data.invoice.user
      this.invoice = data.data.invoice
      this.formData.amount = data.data.invoice.due_amount
      this.maxPayableAmount = data.data.invoice.due_amount
      this.company_id = data.data.invoice.company_id
    },
    async setPaymentAmountByInvoiceData(id) {
      let data = await this.fetchInvoice(id)
      this.formData.amount = data.data.invoice.due_amount
      this.maxPayableAmount = data.data.invoice.due_amount
    },
    async fetchCustomerInvoices(userId) {
      let data = {
        customer_id: userId,
        status: 'UNPAID',
      }
      let response = await this.fetchInvoices(data)
      /* this.invoiceList = [{
        id: null,
        invoice_number: null,
        due_amount: null
      }] */
      response.data.invoices.data.forEach((element) => {
        this.invoiceList.push(element)
      })

      // console.log('invoice list: ', this.invoiceList);
    },

    async fetchCustomerAccounts(userId) {
      let data = {
        customer_id: userId,
        status: 'UNPAID',
      }
      let response = await this.fetchPaymentAccounts(data)
      this.accountList = response.data.payment_accounts.data
        .filter((el) => {
          return el.payment_account_type == 'ACH'
        })
        .map((el) => {
          const auxAccountNumber = el.account_number.toString().split('')
          let showAccountNumber = ''
          const limit = auxAccountNumber.length - 4
          auxAccountNumber.forEach((el, i) => {
            if (i < limit) showAccountNumber = showAccountNumber + '*'
            else showAccountNumber = showAccountNumber + el
          })
          return {
            ...el,
            name_account_number: el.bank_name + ' - ' + showAccountNumber,
          }
        })
      this.cardList = response.data.payment_accounts.data
        .filter((el) => {
          return el.payment_account_type == 'CC'
        })
        .map((el) => {
          const auxCardNumber = el.card_number.toString().split('')
          let showCardNumber = ''
          const limit = auxCardNumber.length - 4
          auxCardNumber.forEach((el, i) => {
            if (i < limit) showCardNumber = showCardNumber + '*'
            else showCardNumber = showCardNumber + el
          })
          return {
            ...el,
            card_number_cvv: el.first_name + ' - ' + showCardNumber,
          }
        })
      this.isLoadingPayments = false
    },

    async PaymentModeSelected(val) {
      this.formData.payment_method = val
      //console.log(val, 'val')
      this.$v.customer.$touch()
      if (val.account_accepted === 'A') {
        this.type_ach = true
        this.type_cc = false
        let params = {}
        if (this.customer) {
          params = {
            id: this.customer.id,
          }
          this.loadCustomerData(params)
        }       
      } else if (val.account_accepted === 'C') {
        this.type_cc = true
        this.type_ach = false
      } else {
        this.type_cc = false
        this.type_ach = false
      }    
      let band = false
      if (val.add_payment_gateway === 1) {
        let res = await this.fetchPaymentGatewaysWithoutLogin()
        if (res) {
          this.payment_gateways = res.data.payment_gateways
          this.payment_gateways.forEach((element) => {
            if (
              element.id === val.payment_gateways_id &&
              val.payment_gateways_id === 1
            ) {
              this.formData.payment_gateways = element
            }

            if (element.default == 1) {
              this.formData.payment_gateways = element
            }
          })
          this.add_payment_gateway_select = true
          //'paso2')
          //console.log(this.add_payment_gateway_select)
          band = true
        }
      } else {
        this.add_payment_gateway_select = false
        this.formData.payment_gateways = null
        this.is_authorize = false
        //return false
      }
      if (this.type_ach && this.account) {
        this.account = null
        this.formData.ACH_type = null
        this.formData.account_number = null
        this.formData.routing_number = null
        this.formData.num_check = null
        this.formData.bank_name = null
        this.authorize.name = null
        this.billing_country = null
        this.billing_state = null
        this.authorize.city = null
        this.authorize.address_street_1 = null
        this.authorize.address_street_2 = null
        this.authorize.zip = null
        this.billing_states = []
      }
      if (
        this.card &&
        ((this.is_authorize && this.isEdit && !this.type_ach && this.type_cc) ||
          (this.is_paypal && this.isEdit && !this.type_ach && this.type_cc) ||
          (this.is_authorize &&
            !this.isEdit &&
            !this.type_ach &&
            this.type_cc) ||
          (this.is_paypal && !this.isEdit && !this.type_ach && this.type_cc))
      ) {
        this.card = null
        //this.formData.payment_gateways = null
        this.authorize.card_number = null
        this.formData.credit_cards = null
        this.authorize.payer_email = null
        this.authorize.cvv = null
        this.authorize.date = null
        this.authorize.name = null
        this.billing_country = null
        this.billing_state = null
        this.authorize.city = null
        this.authorize.address_street_1 = null
        this.authorize.address_street_2 = null
        this.authorize.zip = null
        this.billing_states = []
      }
      return band
      // if (val.name === 'Credit Card') {
      //   let res = await this.fetchPaymentGateways()
      //   if (res) {
      //     this.payment_gateways = res.data.payment_gateways
      //     this.payment_gateways.forEach(element => {
      //       if (element.default) {
      //         this.formData.payment_gateways = element
      //       }
      //     });
      //     this.add_payment_gateway_select = true
      //     return this.add_payment_gateway_select;
      //   }
      // } else if (this.formData.payment_method != 'Credit Card') {
      //   this.add_payment_gateway_select = false
      //   this.formData.payment_gateways = null
      //   this.is_authorize = false
      //   return this.add_payment_gateway_select;
      // }
    },

    async transactionStatusSelected(val) {
      //console.log('entroaqui')
      if (val.text === 'Void' || val.text === 'Refunded') {
        this.transactionStatusCheck = true
      } else {
        this.transactionStatusCheck = false
      }
    },

    async submitPaymentData() {      
      let validate = await this.touchCustomField()
      this.$v.customer.$touch()
      this.$v.formData.$touch()

      // Editar un pago

      if (this.isEdit) {
        this.formData.transaction_status = this.formData.status.value
        this.formData.isTransactionStatus = this.isTransactionStatus

        let data = {
          editData: {
            ...this.formData,
            payment_method_id: this.formData.payment_method
              ? this.formData.payment_method.id
              : null,
            payment_date: moment(this.formData.payment_date).format(
              'YYYY-MM-DD'
            ),
          },
          id: this.$route.params.id,
        }

        try {
          if (this.formData.transaction_status != 'Approved') {
            swal({
              title: this.$t('general.are_you_sure'),
              text: this.$tc('payments.transaction_status_message'),
              icon: 'warning',
              buttons: true,
            }).then(async (willDelete) => {
              if (willDelete) {
                this.isLoading = true

                if (this.formData.transaction_status === 'Void') {
                  //console.log(this.formData.transaction_status)
                  //console.log(this.formData.status_with_authorize)

                  if (this.formData.status_with_authorize) {
                    //console.log(data)
                    let resVoid = await this.voidAuthorize(data.editData)
                    if (resVoid.data) {
                      if (resVoid.data.transactionResponse) {
                        if (
                          resVoid.data.transactionResponse.messages &&
                          resVoid.data.transactionResponse.messages[0]
                            .description ===
                            'This transaction has been approved.'
                        ) {
                          window.toastr['success'](
                            'This transaction has been approved.'
                          )
                          //console.log(data)
                          let response = await this.updatePayment(data)

                          if (
                            response.data.success ||
                            response.status === 200
                          ) {
                            this.isLoading = false
                            this.$router.push(
                              `/admin/payments/${response.data.payment.id}/view`
                            )
                            window.toastr['success'](
                              this.$t('payments.updated_message')
                            )
                            return true
                          }

                          if (response.data.error === 'invalid_amount') {
                            window.toastr['error'](
                              this.$t('invalid_amount_message')
                            )
                            return false
                          }

                          window.toastr['error'](response.data.error)
                        } else if (resVoid.data.transactionResponse.errors) {
                          window.toastr['error'](
                            resVoid.data.transactionResponse.errors[0].errorText
                          )
                          this.isLoading = false
                          return true
                        }
                      }

                      this.isLoading = false
                    }
                  } else {
                    //console.log(data)
                    let response = await this.updatePayment(data)

                    if (response.data.success || response.status === 200) {
                      this.isLoading = false
                      this.$router.push(
                        `/admin/payments/${response.data.payment.id}/view`
                      )
                      window.toastr['success'](
                        this.$t('payments.updated_message')
                      )
                      return true
                    }

                    if (response.data.error === 'invalid_amount') {
                      window.toastr['error'](this.$t('invalid_amount_message'))
                      return false
                    }

                    window.toastr['error'](response.data.error)
                  }
                } else if (this.formData.transaction_status === 'Refunded') {
                  if (this.formData.status_with_authorize) {
                    let resRefunded = await this.refundedAuthorize(
                      data.editData
                    )
                    if (resRefunded.data.transactionResponse?.errors) {
                      window.toastr['error'](
                        resRefunded.data.transactionResponse.errors[0].errorText
                      )
                      this.isLoading = false
                      return true
                    } else if (
                      resRefunded.data.transactionResponse.messages &&
                      resRefunded.data.transactionResponse.messages[0]
                        .description === 'This transaction has been approved.'
                    ) {
                      window.toastr['success'](
                        'This transaction has been approved.'
                      )
                      let response = await this.updatePayment(data)

                      if (response.data.success) {
                        this.isLoading = false
                        this.$router.push(
                          `/admin/payments/${response.data.payment.id}/view`
                        )
                        window.toastr['success'](
                          this.$t('payments.updated_message')
                        )
                        return true
                      }

                      if (response.data.error === 'invalid_amount') {
                        window.toastr['error'](
                          this.$t('invalid_amount_message')
                        )
                        return false
                      }

                      window.toastr['error'](response.data.error)
                    }
                  } else {
                    //console.log(data)
                    let response = await this.updatePayment(data)

                    if (response.data.success || response.status === 200) {
                      this.isLoading = false
                      this.$router.push(
                        `/admin/payments/${response.data.payment.id}/view`
                      )
                      window.toastr['success'](
                        this.$t('payments.updated_message')
                      )
                      return true
                    }

                    if (response.data.error === 'invalid_amount') {
                      window.toastr['error'](this.$t('invalid_amount_message'))
                      return false
                    }

                    window.toastr['error'](response.data.error)
                  }
                } else if (this.formData.transaction_status === 'Unapply') {
                  let response = await this.updatePayment(data)

                  if (response.data.success) {
                    this.isLoading = false
                    this.$router.push(
                      `/admin/payments/${response.data.payment.id}/view`
                    )
                    window.toastr['success'](
                      this.$t('payments.updated_message')
                    )
                    return true
                  }

                  if (response.data.error === 'invalid_amount') {
                    window.toastr['error'](this.$t('invalid_amount_message'))
                    return false
                  }

                  window.toastr['error'](response.data.error)
                }
              }
            })
          } else {
            this.isLoading = true
            let response = await this.updatePayment(data)

            if (response.data.success) {
              this.isLoading = false
              this.$router.push(
                `/admin/payments/${response.data.payment.id}/view`
              )
              window.toastr['success'](this.$t('payments.updated_message'))
              return true
            }

            if (response.data.error === 'invalid_amount') {
              window.toastr['error'](this.$t('invalid_amount_message'))
              return false
            }

            window.toastr['error'](response.data.error)
            this.isLoading = false
          }
        } catch (err) {
          console.log(err)
          this.isLoading = false

          if (err.response.data?.errors?.payment_number) {
            window.toastr['error'](err.response.data.errors.payment_number)
            this.isLoading = false
            return true
          }

          window.toastr['error'](err.response.data.message)
        } finally {
          this.isLoading = false
        }
      }
      // nuevo pago
      else {
        //console.log('entro aqui 4')
        if (!this.formData.payment_method && !this.creditv) {
          window.toastr['error'](this.$t('payments.select_a_payment_method'))
          return false
        }

        //console.log('entro aqui 5')

        if (this.formData.ACH_type) {
          this.formData.ACH_type = this.formData.ACH_type.value
        }

        if (this.is_authorize && !this.isEdit) {
          this.$v.authorize.$touch()
        }

        if (this.is_authorize && (this.$v.$invalid || validate.error)) {
          this.formData.ACH_type = this.bank_account_type.find((el) => {
            return el.value == this.formData.ACH_type
          })
          return true
        }

        this.formData.payment_number =
          this.paymentPrefix + '-' + this.paymentNumAttribute

        this.authorize.payment_number = this.formData.payment_number

        if (this.formData.credit_cards) {
          this.formData.credit_card = this.formData.credit_cards.name
          this.authorize.credit_cards = this.formData.credit_cards.name
        }

        /// credito
        if (this.formData.payment_method == null && this.creditv) {
         //console.log(this.formData, "this.formData")
         //console.log(this.formData.amount / 100, "amount")
         //console.log(this.customer.balance, "this.customer.balance")
         
        const confirmPay = await swal({
            title: this.$t('general.are_you_sure'),
            text: this.$tc('general.do_you_want_applied_credit_invoice'),
            icon: 'warning',
            buttons: true,
          }) 
          if (!confirmPay) {
            return false
          }

          if(this.formData.amount / 100 > this.customer.balance){
            this.formData.amount = this.customer.balance * 100
         }
          
          //console.log('entro a credito')
          let data = {
            ...this.formData,
            payment_method_id: this.formData.payment_method
              ? this.formData.payment_method.id
              : null,
            payment_date: moment(this.formData.payment_date).format(
              'YYYY-MM-DD'
            ),
          }
          let response = await this.addPaymentWithoutLogin(data)

          if (response.data.success) {
            this.$router.push(
              `/admin/payments/${response.data.payment.id}/view`
            )
            window.toastr['success'](this.$t('payments.created_message'))
            this.isLoading = true
            return true
          }
        } else {
          this.formData.customer_credit = false
        }

        try {
          // en caso de que sea el pago sin factura
          if (this.formData.invoice_id === null) {
            this.isLoading = true
            swal({
              title: this.$t('payments.are_you_sure_text'),
              text: this.$tc('payments.payment_invoice_message'),
              icon: 'warning',
              buttons: true,
            }).then(async (willDelete) => {
              if (willDelete) {
                // pago sin factura con tarjeta de credito
                if (this.isAuthorize && !this.type_ach && !this.is_paypal) {
                  this.authorize.isAuthorize = this.isAuthorize
                  this.authorize.user_id = this.formData.user_id
                  this.authorize.amount = this.formData.amount
                  this.authorize.customcode = this.formData.customcode
                  this.authorize.creator_id =
                    this.formData.payment_gateways.creator_id
                  this.formData.authorize = this.authorize
                  //console.log('formDataCC', this.formData)
                  let res = await this.addAuthorizeWithoutLogin(this.formData.authorize)
                  //console.log(res.data.transactionResponse)
                  if (res.data?.transactionResponse?.errors) {
                    this.transactionFail.description =
                      res.data.transactionResponse.errors[0].errorText
                    window.toastr['error'](
                      res.data.transactionResponse.errors[0].errorText
                    )
                    //this.isLoading = false
                    // this.isTransactionFail = true
                  }
                  //AVS RESPONSES

                  if (res.data?.transactionResponse?.responseCode != '1') {

                    this.transactionFail.description =
                      'This Transaction has been declined.'
                    this.isLoading = false
                    this.isTransactionFail = true

                    if (res.data?.transactionResponse?.avsResultCode === 'E') {
                      this.transactionFail.description =
                        'AVS data provided is invalid or AVS is not allowed for the card type that was used.'
                      //window.toastr['error']('Please wait 10 minutes.')
                      this.isLoading = false
                      this.isTransactionFail = true
                    }
                    if (res.data?.transactionResponse?.avsResultCode === 'P') {
                      this.transactionFail.description = 'Is NOT Processed'
                      //window.toastr['error']('Please wait 10 minutes.')
                      this.isLoading = false
                      this.isTransactionFail = true
                    }
                    if (res.data?.transactionResponse?.avsResultCode === 'R') {
                      this.transactionFail.description =
                        'The AVS system was unavailable at the time of processing.'
                      //window.toastr['error']('Please wait 10 minutes.')
                      this.isLoading = false
                      this.isTransactionFail = true
                    }
                    if (res.data?.transactionResponse?.avsResultCode === 'G') {
                      this.transactionFail.description =
                        'The card issuing bank is of non-U.S. origin and does not support AVS.'
                      //window.toastr['error']('Please wait 10 minutes.')
                      this.isLoading = false
                      this.isTransactionFail = true
                    }
                    if (res.data?.transactionResponse?.avsResultCode === 'U') {
                      this.transactionFail.description =
                        'The address information for the cardholder is unavailable.'
                      //window.toastr['error']('Please wait 10 minutes.')
                      this.isLoading = false
                      this.isTransactionFail = true
                    }
                    if (res.data?.transactionResponse?.avsResultCode === 'S') {
                      this.transactionFail.description =
                        'The U.S. card issuing bank does not support AVS.'
                      //window.toastr['error']('Please wait 10 minutes.')
                      this.isLoading = false
                      this.isTransactionFail = true
                    }
                    if (
                      res.data &&
                      (res.data.transactionResponse?.avsResultCode === 'N' ||
                        res.data.transactionResponse?.avsResultCode === 'A' ||
                        res.data.transactionResponse?.avsResultCode === 'Z')
                    ) {
                      this.transactionFail.description =
                        'Address: No Match. ZIP Code: Match.'
                      //window.toastr['error']('Please wait 10 minutes.')
                      this.isLoading = false
                      this.isTransactionFail = true
                    }
                    if (
                      res.data?.transactionResponse?.avsResultCode === 'W' ||
                      res.data.transactionResponse?.avsResultCode === 'X'
                    ) {
                      this.transactionFail.description =
                        'Address: Match. ZIP Code: Matched 9 digits.'
                      //window.toastr['error']('Please wait 10 minutes.')
                      this.isLoading = false
                      this.isTransactionFail = true
                    }

                    //CVV RESPONSES
                    if (res.data?.transactionResponse?.cvvResultCode === 'N') {
                      this.transactionFail.description = 'Does NOT Match.'
                      //window.toastr['error']('Please wait 10 minutes.')
                      this.isLoading = false
                      this.isTransactionFail = true
                    }
                    if (res.data?.transactionResponse?.cvvResultCode === 'U') {
                      this.transactionFail.description =
                        'Issuer is not certified or has not provided encryption key.'
                      //window.toastr['error']('Please wait 10 minutes.')
                      this.isLoading = false
                      this.isTransactionFail = true
                    }
                    if (res.data?.transactionResponse?.cvvResultCode === 'S') {
                      this.transactionFail.description =
                        'Should be on card, but is not indicated.'
                      //window.toastr['error']('Please wait 10 minutes.')
                      this.isLoading = false
                      this.isTransactionFail = true
                    }
                    if (res.data?.transactionResponse?.cvvResultCode === 'P') {
                      this.transactionFail.description =
                        'Error card code: Is NOT Processed.'

                      this.isLoading = false
                      this.isTransactionFail = true
                    }
                    if (res.data.messages?.resultCode == 'Error') {
                      this.transactionFail.description =
                        res.data.messages.message[0].text

                      this.isLoading = false
                      this.isTransactionFail = true
                      return true
                    }
                  }

                  if (this.isTransactionFail) {
                    window.toastr['error'](this.transactionFail.description)

                    this.transactionFail.payment_gateway =
                      this.formData.payment_gateways.name
                    this.transactionFail.transaction_number =
                      res.data.transactionResponse.transId
                    this.transactionFail.date = this.formData.payment_date
                    this.transactionFail.amount = this.formData.amount
                    this.transactionFail.payment_number =
                      this.formData.payment_number
                    this.transactionFail.customer_id = this.formData.user_id
                    this.transactionFail.invoice_id = this.formData.invoice_id
                    this.transactionFail.type_trasaction = 'CC'

                    //console.log('this.formData', this.formData)
                    //console.log('this.transactionFail', this.transactionFail)

                    let resAddFailedPaymentHistory =
                      await this.SavePayFailedWithoutLogin(this.transactionFail)
                    /*console.log(
                      'addFailedPaymentHistoryCC',
                      resAddFailedPaymentHistory
                    )*/
                    //console.log("si llega hasta aqui")
                    //location.reload()
                    //this.$router.go(0)
                    this.isTransactionFail = false
                    return true
                  }
                  //console.log('llega aqui?')
                  if (
                    res.data.messages.resultCode === 'Ok' ||
                    res.data?.transactionResponse?.responseCode === '1'
                  ) {
                    let authorizeData = {
                      ...this.formData.authorize,
                      transId: res.data.transactionResponse.transId,
                      company_id: this.formData.company_id,
                    }

                    let res2 = await this.saveAuthorizeDBWithoutLogin(authorizeData)
                    //console.log('si llega aqui :(')
                    this.formData.authorize_id = res2.data.authorize.id
                  }
                }
                //  pago sin factura con ach
                else if (this.formData.routing_number && this.type_ach) {
                  this.authorize.ACH_type = this.formData.ACH_type
                  this.authorize.account_number = this.formData.account_number
                  this.authorize.routing_number = this.formData.routing_number
                  this.authorize.bank_name = this.formData.bank_name
                  this.authorize.num_check = this.formData.num_check
                  this.authorize.customcode = this.formData.customcode
                  this.authorize.user_id = this.formData.user_id
                  this.authorize.amount = this.formData.amount
                  this.authorize.creator_id =
                    this.formData.payment_gateways.creator_id
                  this.formData.authorize = this.authorize
                  //console.log(this.formData)
                  let res = await this.addAuthorizeACH(this.formData.authorize)

                  //console.log('addAuthorizeACH', res)

                  if (res.data?.transactionResponse?.errors) {
                    this.transactionFail.description =
                      res.data.transactionResponse.errors[0].errorText
                    window.toastr['error'](this.transactionFail.description)
                    //console.log('si es aqui donde va FAIL')
                    this.isLoading = false

                    this.transactionFail.payment_gateway =
                      this.formData.payment_gateways.name
                    this.transactionFail.transaction_number =
                      res.data.transactionResponse.transId
                    this.transactionFail.date = this.formData.payment_date
                    this.transactionFail.amount = this.formData.amount
                    this.transactionFail.payment_number =
                      this.formData.payment_number
                    this.transactionFail.customer_id = this.formData.user_id
                    this.transactionFail.invoice_id = this.formData.invoice_id
                    this.transactionFail.type_trasaction = 'ACH'

                    let resAddFailedPaymentHistory =
                      await this.SavePayFailedWithoutLogin(this.transactionFail)
                    //location.reload()
                    //this.$router.go(0)

                    return true
                  }


                  if (res.data?.transactionResponse?.responseCode != '1') {

                    this.transactionFail.description =
                      'This Transaction has been declined.'
                    this.isLoading = false
                    this.isTransactionFail = true
                    //console.log('codigo 2')
                  }
                  if (res.data.messages?.resultCode == 'Error') {
                    this.transactionFail.description =
                      res.data.messages.message[0].text

                    this.isLoading = false
                    this.isTransactionFail = true
                    return true
                  }

                  if (this.isTransactionFail) {
                    window.toastr['error'](this.transactionFail.description)

                    this.transactionFail.payment_gateway =
                      this.formData.payment_gateways.name
                    this.transactionFail.transaction_number =
                      res.data.transactionResponse.transId
                    this.transactionFail.date = this.formData.payment_date
                    this.transactionFail.amount = this.formData.amount
                    this.transactionFail.payment_number =
                      this.formData.payment_number
                    this.transactionFail.customer_id = this.formData.user_id
                    this.transactionFail.invoice_id = this.formData.invoice_id

                    //console.log('this.formData', this.formData)
                    //console.log('this.transactionFail', this.transactionFail)

                    let resAddFailedPaymentHistory =
                      await this.SavePayFailedWithoutLogin(this.transactionFail)
                    /*console.log(
                      'SavePayFailedWithoutLogin',
                      resAddFailedPaymentHistory
                    )*/
                    //console.log("si llega hasta aqui")
                    //location.reload()
                    //this.$router.go(0)
                    return true
                  }

                  if (res.data.messages.resultCode === 'Ok') {
                    //console.log('si')
                    let authorizeData = {
                      ...this.formData.authorize,
                      transId: res.data.transactionResponse.transId,
                    }

                    let res2 = await this.saveAuthorizeACH(authorizeData)
                    //console.log(res2)
                    this.formData.authorize_id = res2.data.authorize.id
                  }
                }                
                // pago sin factura credit card con paypal pro
                else if (
                  this.formData.payment_method.account_accepted == 'C' &&
                  this.formData.payment_gateways.name == 'Paypal'
                ) {
                  this.authorize.isAuthorize = this.isAuthorize
                  this.authorize.user_id = this.formData.user_id
                  this.authorize.amount = this.formData.amount
                  this.authorize.customcode = this.customer.customcode
                  this.authorize.invoice_id = this.formData.invoice_id
                  this.authorize.creator_id =
                    this.formData.payment_gateways.creator_id
                  this.authorize.payment_number = this.formData.payment_number

                  // add customcode to authorize
                  this.formData.authorize = this.authorize
                  try {
                    const resultPaypalPro = await this.chargePaypalPro(
                      this.formData.authorize
                    )
                    this.formData.payment_paypal_id =
                      resultPaypalPro.data.data.id
                  } catch (e) {
                    this.transactionFail.description = e.response.data.message
                    window.toastr['error'](this.transactionFail.description)
                    this.transactionFail.payment_gateway =
                      this.formData.payment_gateways.name
                    this.transactionFail.date = this.formData.payment_date
                    this.transactionFail.amount = this.formData.amount
                    this.transactionFail.payment_number =
                      this.formData.payment_number
                    this.transactionFail.customer_id = this.formData.user_id
                    this.transactionFail.invoice_id = this.formData.invoice_id
                    this.isLoading = false
                    await this.SavePayFailedWithoutLogin(this.transactionFail)

                    return true
                  }
                }

                let data = {
                  ...this.formData,
                  payment_method_id: this.formData.payment_method
                    ? this.formData.payment_method.id
                    : null,
                  payment_date: moment(this.formData.payment_date).format(
                    'YYYY-MM-DD'
                  ),
                }
                let response = await this.addPaymentWithoutLogin(data)
                //console.log('response', response)
                if (response.data.success) {
                  //console.log('por aqui esta el error')
                  this.$router.push(
                    `/admin/payments/${response.data.payment.id}/view`
                  )
                  window.toastr['success'](this.$t('payments.created_message'))
                  this.isLoading = true
                  return true
                }

                if (response.data.error === 'invalid_amount') {
                  window.toastr['error'](this.$t('invalid_amount_message'))
                  return false
                }

                window.toastr['error'](response.data.error)
              } else {
                this.isLoading = false
                return true
              }
            })
          }
          // en caso de que sea un pago con factura
          else {
            if (this.formData.amount > this.maxPayableAmount) return false
            this.isLoading = true
            // pago con factura con tarjeta de credito
            if (this.isAuthorize && !this.type_ach && !this.is_paypal) {
              this.authorize.isAuthorize = this.isAuthorize
              this.authorize.user_id = this.formData.user_id
              this.authorize.amount = this.formData.amount
              this.authorize.customcode = this.formData.customcode
              this.authorize.creator_id =
                this.formData.payment_gateways.creator_id

              this.formData.authorize = this.authorize
              //console.log('formdataInvoice', this.formData)
             // console.log('authoriceInovice', this.formData.authorize)
              let res = await this.addAuthorizeWithoutLogin(this.formData.authorize)
              //console.log('respuesta authorize invoice', res)
              if (res.data.messages.resultCode === 'Error') {
                this.transactionFail.description =
                  res.data.messages.message[0].text
                this.isLoading = false
                this.isTransactionFail = true
                //console.log('entrandiTO', this.isTransactionFail)
              } else {
                //console.log('failes', res.data.transactionResponse.errors)
                if (res.data?.transactionResponse?.errors) {
                  this.transactionFail.description =
                    res.data.transactionResponse.errors[0].errorText
                  window.toastr['error'](
                    res.data.transactionResponse.errors[0].errorText
                  )
                  //this.isLoading = false
                  //this.isTransactionFail = true
                }
                //AVS RESPONSES

                if (res.data?.transactionResponse?.responseCode != '1') {

                  this.transactionFail.description =
                    'This Transaction has been declined.'
                  //window.toastr['error']('Please wait 10 minutes.')
                  this.isLoading = false
                  this.isTransactionFail = true

                  if (res.data?.transactionResponse?.avsResultCode === 'E') {
                    this.transactionFail.description =
                      'AVS data provided is invalid or AVS is not allowed for the card type that was used.'
                    //window.toastr['error']('Please wait 10 minutes.')
                    this.isLoading = false
                    this.isTransactionFail = true
                  }
                  if (res.data?.transactionResponse?.avsResultCode === 'R') {
                    this.transactionFail.description =
                      'The AVS system was unavailable at the time of processing.'
                    //window.toastr['error']('Please wait 10 minutes.')
                    this.isLoading = false
                    this.isTransactionFail = true
                  }
                  if (res.data?.transactionResponse?.avsResultCode === 'G') {
                    this.transactionFail.description =
                      'The card issuing bank is of non-U.S. origin and does not support AVS.'
                    //window.toastr['error']('Please wait 10 minutes.')
                    this.isLoading = false
                    this.isTransactionFail = true
                  }
                  if (res.data?.transactionResponse?.avsResultCode === 'U') {
                    this.transactionFail.description =
                      'The address information for the cardholder is unavailable.'
                    //window.toastr['error']('Please wait 10 minutes.')
                    this.isLoading = false
                    this.isTransactionFail = true
                  }
                  if (res.data?.transactionResponse?.avsResultCode === 'S') {
                    this.transactionFail.description =
                      'The U.S. card issuing bank does not support AVS.'
                    //window.toastr['error']('Please wait 10 minutes.')
                    this.isLoading = false
                    this.isTransactionFail = true
                  }
                  if (
                    res.data?.transactionResponse?.avsResultCode === 'N' ||
                    res.data?.transactionResponse?.avsResultCode === 'A' ||
                    res.data?.transactionResponse?.avsResultCode === 'Z'
                  ) {
                    this.transactionFail.description =
                      'Address: No Match. ZIP Code: Match.'
                    //window.toastr['error']('Please wait 10 minutes.')
                    this.isLoading = false
                    this.isTransactionFail = true
                  }
                  if (
                    res.data?.transactionResponse?.avsResultCode === 'W' ||
                    res.data?.transactionResponse?.avsResultCode === 'X'
                  ) {
                    this.transactionFail.description =
                      'Address: Match. ZIP Code: Matched 9 digits.'
                    //window.toastr['error']('Please wait 10 minutes.')
                    this.isLoading = false
                    this.isTransactionFail = true
                  }

                  //CVV RESPONSES
                  if (res.data?.transactionResponse?.cvvResultCode === 'N') {
                    this.transactionFail.description = 'Does NOT Match.'
                    //window.toastr['error']('Please wait 10 minutes.')
                    this.isLoading = false
                    this.isTransactionFail = true
                  }
                  if (res.data?.transactionResponse?.cvvResultCode === 'U') {
                    this.transactionFail.description =
                      'Issuer is not certified or has not provided encryption key.'
                    //window.toastr['error']('Please wait 10 minutes.')
                    this.isLoading = false
                    this.isTransactionFail = true
                  }
                  if (res.data?.transactionResponse?.cvvResultCode === 'S') {
                    this.transactionFail.description =
                      'Should be on card, but is not indicated.'
                    //window.toastr['error']('Please wait 10 minutes.')
                    this.isLoading = false
                    this.isTransactionFail = true
                  }
                  if (res.data?.transactionResponse?.cvvResultCode === 'P') {
                    this.transactionFail.description =
                      'Error card code: Is NOT Processed.'
                    //window.toastr['error']('Please wait 10 minutes.')
                    this.isLoading = false
                    this.isTransactionFail = true
                  }
                }
              }

              if (this.isTransactionFail) {
                //console.log('siENTRAA FAIL')
                window.toastr['error'](this.transactionFail.description)
                //console.log('respuesta authorize', res)

                this.transactionFail.payment_gateway =
                  this.formData.payment_gateways.name
                //this.transactionFail.transaction_number = res.data.transactionResponse.transId
                this.transactionFail.date = this.formData.payment_date
                this.transactionFail.amount = this.formData.amount
                this.transactionFail.payment_number =
                  this.formData.payment_number
                this.transactionFail.customer_id = this.formData.user_id
                this.transactionFail.invoice_id = this.formData.invoice_id

                //console.log('this.formData', this.formData)
                //console.log('this.transactionFail', this.transactionFail)

                let resAddFailedPaymentHistory =
                  await this.SavePayFailedWithoutLogin(this.transactionFail)
                /*console.log(
                  'SavePayFailedWithoutLogin',
                  resAddFailedPaymentHistory
                )*/
                this.isTransactionFail = false
                return true
              }

              if (
                res.data.messages.resultCode === 'Ok' ||
                res.data?.transactionResponse?.responseCode === '1'
              ) {
                let authorizeData = {
                  ...this.formData.authorize,
                  transId: res.data.transactionResponse.transId,
                  company_id: this.formData.company_id,
                }

                let res2 = await this.saveAuthorizeDBWithoutLogin(authorizeData)

                this.formData.authorize_id = res2.data.authorize.id
              }
            }
            // pago con factura con ach
            else if (this.formData.routing_number && this.type_ach) {
              this.authorize.ACH_type = this.formData.ACH_type
              this.authorize.account_number = this.formData.account_number
              this.authorize.routing_number = this.formData.routing_number
              this.authorize.bank_name = this.formData.bank_name
              this.authorize.customcode = this.formData.customcode
              this.authorize.num_check = this.formData.num_check
              this.authorize.user_id = this.formData.user_id
              this.authorize.amount = this.formData.amount
              this.authorize.creator_id =
                this.formData.payment_gateways.creator_id
              this.formData.authorize = this.authorize
              //console.log(this.formData)
              let res = await this.addAuthorizeACH(this.formData.authorize)

              //console.log('addAuthorizeACH', res)

              if (res.data.transactionResponse?.errors) {
                this.transactionFail.description =
                  res.data.transactionResponse.errors[0].errorText
                window.toastr['error'](this.transactionFail.description)
                this.isLoading = false

                this.transactionFail.payment_gateway =
                  this.formData.payment_gateways.name
                this.transactionFail.transaction_number =
                  res.data.transactionResponse.transId
                this.transactionFail.date = this.formData.payment_date
                this.transactionFail.amount = this.formData.amount
                this.transactionFail.payment_number =
                  this.formData.payment_number
                this.transactionFail.customer_id = this.formData.user_id
                this.transactionFail.invoice_id = this.formData.invoice_id

                let resAddFailedPaymentHistory =
                  await this.SavePayFailedWithoutLogin(this.transactionFail)

                return true
              }

              if (res.data.transactionResponse?.responseCode === '2') {
                this.transactionFail.description =
                  'This Transaction has been declined.'
                this.isLoading = false
                this.isTransactionFail = true
                //console.log('codigo 2')
              }

              if (this.isTransactionFail) {
                window.toastr['error'](this.transactionFail.description)
                //console.log('respuesta authorize', res)

                this.transactionFail.payment_gateway =
                  this.formData.payment_gateways.name
                this.transactionFail.transaction_number =
                  res.data.transactionResponse.transId
                this.transactionFail.date = this.formData.payment_date
                this.transactionFail.amount = this.formData.amount
                this.transactionFail.payment_number =
                  this.formData.payment_number
                this.transactionFail.customer_id = this.formData.user_id
                this.transactionFail.invoice_id = this.formData.invoice_id

                //console.log('this.formData', this.formData)
                //console.log('this.transactionFail', this.transactionFail)

                let resAddFailedPaymentHistory =
                  await this.SavePayFailedWithoutLogin(this.transactionFail)
                /*console.log(
                  'SavePayFailedWithoutLogin',
                  resAddFailedPaymentHistory
                )*/
                this.isTransactionFail = false
                return true
              }

              if (res.data.messages.resultCode === 'Ok') {
                let authorizeData = {
                  ...this.formData.authorize,
                  transId: res.data.transactionResponse.transId,
                }

                let res2 = await this.saveAuthorizeACH(authorizeData)

                this.formData.authorize_id = res2.data.authorize.id
              }
            }
            // pago con factura credit card con paypal pro
            else if (
              this.formData.payment_method.account_accepted == 'C' &&
              this.formData.payment_gateways.name == 'Paypal'
            ) {
              this.authorize.isAuthorize = this.isAuthorize
              this.authorize.user_id = this.formData.user_id
              this.authorize.amount = this.formData.amount
              this.authorize.customcode = this.customer.customcode
              this.authorize.invoice_id = this.formData.invoice_id
              this.authorize.creator_id =
                this.formData.payment_gateways.creator_id
              this.authorize.payment_number = this.formData.payment_number

              // add customcode to authorize
              this.formData.authorize = this.authorize
              try {
                const resultPaypalPro = await this.chargePaypalPro(
                  this.formData.authorize
                )
                this.formData.payment_paypal_id = resultPaypalPro.data.data.id
              } catch (e) {
                this.transactionFail.description = e.response.data.message
                window.toastr['error'](this.transactionFail.description)
                this.transactionFail.payment_gateway =
                  this.formData.payment_gateways.name
                this.transactionFail.date = this.formData.payment_date
                this.transactionFail.amount = this.formData.amount
                this.transactionFail.payment_number =
                  this.formData.payment_number
                this.transactionFail.customer_id = this.formData.user_id
                this.transactionFail.invoice_id = this.formData.invoice_id
                this.isLoading = false
                await this.SavePayFailedWithoutLogin(this.transactionFail)

                return true
              }
            }
            // pago con factura con paypal
            else if (this.is_paypal) {
              this.paypal.user_id = this.formData.user_id
              this.paypal.amount = this.formData.amount
              this.formData.nonce = document.querySelector('#nonce').value
              this.formData.paypal = this.paypal
              //console.log('authorizePaypal', this.formData)
              let res = await this.savePaypalDB(this.formData)
              //console.log('paypal res', res)
              if (res.data === 'Error') {
                this.transactionFail.description =
                  res.data.transactionResponse.errors[0].errorText
                window.toastr['error'](this.transactionFail.description)
                this.isLoading = false

                let resAddFailedPaymentHistory =
                  await this.SavePayFailedWithoutLogin(this.transactionFail)

                return true
              }

              if (this.isTransactionFail) {
                window.toastr['error'](this.transactionFail.description)

                this.transactionFail.payment_gateway =
                  this.formData.payment_gateways.name
                this.transactionFail.transaction_number =
                  res.data.transactionResponse.transId
                this.transactionFail.date = this.formData.payment_date
                this.transactionFail.amount = this.formData.amount
                this.transactionFail.payment_number =
                  this.formData.payment_number
                this.transactionFail.customer_id = this.formData.user_id
                this.transactionFail.invoice_id = this.formData.invoice_id

                //console.log('this.formData', this.formData)
                //console.log('this.transactionFail', this.transactionFail)

                let resAddFailedPaymentHistory =
                  await this.SavePayFailedWithoutLogin(this.transactionFail)
                /*console.log(
                  'SavePayFailedWithoutLogin',
                  resAddFailedPaymentHistory
                )*/
                return true
              }

              if (res.data.authorize.payment_status === 'Captured') {
                //console.log('captured')
                let authorizeData = {
                  ...this.formData.authorize,
                  transId: res.data.authorize.transaction_id,
                }

                this.formData.paypal_id = res.data.authorize.transaction_id
              }
            }
            let data = {
              ...this.formData,
              payment_method_id: this.formData.payment_method
                ? this.formData.payment_method.id
                : null,
              payment_date: moment(this.formData.payment_date).format(
                'YYYY-MM-DD'
              ),
            }

            //console.log(data, "aqui entra")
            let response = await this.addPaymentWithoutLogin(data)

            if (response.data.success) {
              this.paymentSuccess = true
              window.toastr['success'](this.$t('payments.created_message'))
              this.isLoading = true
              return true
            }

            if (response.data.error === 'invalid_amount') {
              window.toastr['error'](this.$t('invalid_amount_message'))
              return false
            }

            window.toastr['error'](response.data.error)
          }
        } catch (err) {
          this.isLoading = false
          console.log(err)
          //console.log(err.response, "err.response.data")
          window.toastr['error'](err.response.data.message)
        } finally {
          this.isLoading = false
        }
      }
    },
    Updateoptionchace(val) {
      this.updatebillinginformation = val
      //console.log(this.updatebillinginformation)
      this.formData.updatebillinginformation = val ? true : false
      //console.log(this.formData)
    },

    Createoptionchace(val) {
      this.createaccount = val
      //console.log(this.createaccount)
      this.formData.createaccount = val ? true : false
      //console.log(this.formData)
    },

    async selectItemAccount(item) {
      this.formData.ACH_type = this.bank_account_type.find((el) => {
        return (
          el.value.toString().toLowerCase() ==
          item.ACH_type.toString().toLowerCase()
        )
      })
      this.formData.account_number = item.account_number
      this.formData.routing_number = item.routing_number
      this.formData.num_check = item.num_check
      this.formData.bank_name = item.bank_name
      this.authorize.name = item.first_name
      this.billing_country = this.countries.find((el) => {
        return el.id == item.country_id
      })
      if (this.billing_country) {
        await this.countrySelected(this.billing_country, 'billing')
      }
      this.billing_state = this.billing_states.find((el) => {
        return el.id == item.state_id
      })
      this.authorize.city = item.city
      this.authorize.address_street_1 = item.address_1
      this.authorize.address_street_2 = item.address_2
      this.authorize.zip = item.zip
    },

    async selectItemCard(item) {
      //console.log('item', item)
      //this.formData.payment_gateways = null
      this.authorize.card_number = item.card_number
      //console.log(this.authorize.card_number)
      this.formData.credit_cards = { name: item.credit_card }
      //this.authorize.payer_email = null
      this.authorize.cvv = item.cvv
      if (item.expiration_date) {
        this.authorize.date = item.expiration_date
      }
      this.authorize.name = item.first_name
      this.billing_country = this.countries.find((el) => {
        return el.id == item.country_id
      })
      if (this.billing_country) {
        await this.countrySelected(this.billing_country, 'billing')
      }
      this.billing_state = this.billing_states.find((el) => {
        return el.id == item.state_id
      })
      this.authorize.city = item.city
      this.authorize.address_street_1 = item.address_1
      this.authorize.address_street_2 = item.address_2
      this.authorize.zip = item.zip
      //console.log('date', this.authorize.date)
    },
    async paypalSuccess(payment_paypal_id) {
      this.isRequestOnGoing = true
      this.paymentPaypalProccess = true
      this.formData.payment_number = this.codePayment
      let data = {
        ...this.formData,
        payment_method_id: this.formData.payment_method
          ? this.formData.payment_method.id
          : null,
        payment_date: moment(this.formData.payment_date).format('YYYY-MM-DD'),
        payment_paypal_id,
      }
      let response = await this.addPaymentWithoutLogin(data)
      this.isRequestOnGoing = false

      if (response.data.success) {
        this.$router.push(`/admin/payments/${response.data.payment.id}/view`)
        window.toastr['success'](this.$t('payments.created_message'))
        this.isLoading = true
        return true
      }

      if (response.data.error === 'invalid_amount') {
        window.toastr['error'](this.$t('invalid_amount_message'))
        return false
      }

      window.toastr['error'](response.data.error)
    },
  },
}
</script>
