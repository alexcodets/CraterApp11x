<template>
  <base-page class="relative payment-create" ref="form">
    <form action="" @submit.prevent="submitPaymentData">
      <!-- Inicio de header -->
      <sw-page-header :title="pageTitle" class="mb-5">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />
          <sw-breadcrumb-item
            :title="$tc('payments.payment', 2)"
            to="/admin/payments"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'payments.edit'"
            :title="$t('payments.edit_payment')"
            to="#"
            active
          />
          <sw-breadcrumb-item
            v-else
            :title="$t('payments.new_payment')"
            to="#"
            active
          />
        </sw-breadcrumb>

        <template slot="actions">
          <sw-button
            class="mr-3 text-sm hidden sm:flex"
            variant="primary-outline"
            type="button"
            @click="goBack()"
          >
            {{ $t('general.cancel') }}
          </sw-button>

          <div v-if="!formData.payment_method.paypal_button">
            <sw-button
              v-if="!notEditable"
              :loading="isLoading"
              :disabled="isLoading"
              variant="primary"
              type="submit"
              size="lg"
              class="hidden sm:flex"
            >
              <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
              {{ isEdit ? $t('payments.update_payment') : 'Add Credit' }}
            </sw-button>
            <sw-button v-else style="display: none"> </sw-button>
          </div>
        </template>
      </sw-page-header>
      <!-- Fin de header -->

      <base-loader v-if="isRequestOnGoing" />
      <!-- Inicio de cuerpo -->
      <sw-card v-else>
        <div class="grid gap-6 grid-col-1 md:grid-cols-2 grid-col-1">
          <sw-input-group
            :label="$t('payments.date')"
            :error="DateError"
            required
          >
            <base-date-picker
              v-model="formData.payment_date"
              :invalid="$v.formData.payment_date.$error"
              :calendar-button="true"
              class="mt-1"
              calendar-button-icon="calendar"
              :disabled="isEdit"
              @input="$v.formData.payment_date.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('payments.payment_number')"
            :error="paymentNumError"
            required
          >
            <sw-input
              :prefix="`${paymentPrefix} - `"
              :invalid="$v.paymentNumAttribute.$error"
              v-model.trim="paymentNumAttribute"
              class="mt-1"
              :disabled="isEdit"
              autocomplete="off"
              @input="$v.paymentNumAttribute.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('payments.customer')"
            :error="customerError"
            required
          >
            <sw-select
              v-model="customer"
              :options="customers"
              :searchable="true"
              :show-labels="false"
              :allow-empty="false"
              :disabled="true"
              :placeholder="$t('customers.select_a_customer')"
              label="name"
              class="mt-1"
              track-by="id"
            />
          </sw-input-group>

          <sw-input-group
            v-if="
              creditv &&
              formData.amount / 100 <= customer.balance &&
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

          <sw-input-group
            :label="$t('payments.amount')"
            :error="amountError"
            required
          >
            <div class="relative w-full mt-1">
              <sw-money
                v-model="amount"
                :currency="customerCurrency"
                :invalid="$v.formData.amount.$error"
                :disabled="isEdit"
                class="relative w-full focus:border focus:border-solid focus:border-primary-500"
                @input="$v.formData.amount.$touch()"
              />
            </div>
          </sw-input-group>

          <sw-input-group :label="$t('payments.payment_mode')">
            <sw-select
              v-if="!fetchingPaymentMethod"
              v-model="formData.payment_method"
              :options="options_payment_methods"
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
              <div slot="afterList">
                <button
                  type="button"
                  class="flex items-center justify-center w-full px-2 py-2 bg-gray-200 border-none outline-none text-primary-400"
                  @click="addPaymentMode"
                >
                  <shopping-cart-icon class="h-5 mr-3 text-primary-400" />
                  <label>{{
                    $t('settings.customization.payments.add_payment_mode')
                  }}</label>
                </button>
              </div>
            </sw-select>
          </sw-input-group>

          <sw-input-group
            :label="$t('general.stay_on_page')"
            required
            class="mb-md-3"
          >
            <sw-switch v-model="stayOnPage" class="absolute" @change="" />
            <br />
          </sw-input-group>

          <div v-if="add_payment_gateway_select">
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
                :invalid="
                  $v.formData.status ? $v.formData.status.$error : false
                "
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

            <div v-if="isEdit && voidStatusChange" class="flex mt-6">
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
            </div>

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
        </div>

        <!-- payapl boton-->

        <div
          v-if="
            formData.payment_method && formData.payment_method.paypal_button
          "
        >
          <paypal
            :formData="formData"
            :codePayment="codePayment"
            :invoice_number="invoice_number"
            :customer="customer"
            @paypalSuccess="paypalSuccess"
          ></paypal>
        </div>

        <!-- Bank Account Information  -->
        <div
          v-if="this.type_ach"
          class="col-span-2 w-full flex flex-wrap mt-sm-6"
        >
          <h6 class="sw-section-title mb-2 mt-4">
            {{ $t('payment_accounts.bank_account_info') }}
          </h6>


          <div v-if="this.type_ach" class="w-full flex flex-wrap">
            <div class="w-full  mb-4 md:pr-2">
              <sw-input-group :label="$t('settings.payment_gateways.title')">
                <sw-select
                  v-model="formData.payment_gateways_ach"
                  :options="payment_gateways_ach"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :disabled="isEdit || isFormDisabled"
                  :placeholder="$t('items.select_a_type')"
                  class="mt-1"
                  track-by="id"
                  label="name"
                  @select="PaymentSelectedFeesAch"
                />
              </sw-input-group>
            </div>
          </div>


          
          <div
            v-if="
              this.paymentFeesListACHflag && this.paymentFeesListACH.length > 0
            "
            class="w-full md:pr-2"
          >
            <div>
              <!-- Título antes de la tabla -->
              <h2 class="text-lg font-bold mb-4">Payment Fees</h2>

              <!-- Tabla de registros -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div
                  v-for="(item, index) in paymentFeesListACH"
                  :key="index"
                  class="p-2 border rounded"
                >
                  <div class="flex justify-between">
                    <p>{{ item.name }}</p>
                    -
                    <p>{{ item.type }}</p>
                  </div>
                  <p
                  v-if="item.type == 'fixed'"
                  >
                    <div v-if="item.type == 'fixed'" v-html="$utils.formatMoney(item.amount,  defaultCurrency)" />

                  </p>

                  <p
                  v-if="item.type == 'percentage'"
                  >
                   {{ item.amount/100 }} %

                  </p>
                </div>
              </div>

              <!-- Leyenda después de la tabla -->
              <p class="mt-4 text-sm" style="color: rgb(197, 48, 48);">
                These payment fees will be applied at the time of charging.
              </p>
            </div>
            <br />
          </div>






          <div class="w-full flex flex-wrap">

            <div class="w-full md:w-1/2 mb-4 md:pl-2">
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
        </div>
            <div class="w-full md:w-1/2 mb-4 md:pl-2">
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
                  type="password"
                  name="account_number"
                  tabindex="1"
                  autocomplete="off"
                  @input="$v.formData.account_number.$touch()"
                >
                  <template v-slot:rightIcon>
                    <!-- <eye-off-icon v-if="showAccountFieldHide" class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                        @click="showAccountFieldHide = !showAccountFieldHide" />
                      <eye-icon v-else class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                        @click="showAccountFieldHide = !showAccountFieldHide" /> -->
                  </template>
                </sw-input>
              </sw-input-group>
            </div>

            <div class="w-full md:w-1/2 mb-4 md:pl-2">
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
                  type="password"
                  name="routing_number"
                  tabindex="1"
                  autocomplete="off"
                  @input="$v.formData.routing_number.$touch()"
                >
                  <template v-slot:rightIcon>
                    <!-- <eye-off-icon v-if="showRoutingFieldHide" class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                        @click="showRoutingFieldHide = !showRoutingFieldHide" />
                      <eye-icon v-else class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                        @click="showRoutingFieldHide = !showRoutingFieldHide" /> -->
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

            <div class="w-full md:w-1/2 mb-4 md:pl-2">
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
          <div class="w-full md:w-1/2 md:pl-2 mt-4">
            <sw-input-group
              :label="$t('customers.address_2')"
              :error="billAddress2Error"
            >
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

          <!-- state -->
          <div class="w-full md:w-1/2 md:pl-2 mt-4">
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
          <div
            class="w-full md:w-1/2 md:pr-2 flex flex-wrap mt-2 items-end justify-start mt-4"
          >
            <sw-switch
              v-model="updatebillinginformation"
              @change="Updateoptionchace"
            />
            <p class="leading-snug text-black box-title ml-4">
              {{ $t('customers.update_billing_info') }}
            </p>
          </div>

          <!-- save create account -->
          <div
            class="w-full md:w-1/2 md:pl-2 flex mt-2 items-end justify-start mt-4"
          >
            <sw-switch v-model="createaccount" @change="Createoptionchace" />
            <p class="leading-snug text-black box-title ml-4">
              {{ $t('customers.save_create_account') }}
            </p>
          </div>

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

        <div
          v-if="
            (this.is_authorize &&
              this.isEdit &&
              !this.type_ach &&
              this.type_cc) ||
            (this.is_paypal && this.isEdit && !this.type_ach && this.type_cc) ||
            (this.is_auxVault &&
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
              this.type_cc) ||
            (this.is_auxVault && !this.isEdit && !this.type_ach && this.type_cc)
          "
          class="w-full flex flex-wrap col-span-2 mt-5 mt-sm-6"
        >
          <h6 class="sw-section-title mb-2 mt-4 mt-sm-6">
            {{ $t('payments.credit_card_information') }}
          </h6>

          <br />

          <!-- PAYMENT GATEWAYS -->
          <sw-input-group
            v-if="
              this.add_payment_gateway_select && !this.type_ach && this.type_cc
            "
            :label="$t('settings.payment_gateways.title')"
            class="mb-4"
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
              @select="PaymentSelectedFees"
            />
          </sw-input-group>

          <div
            v-if="
              this.paymentFeesListCCflag && this.paymentFeesListCC.length > 0
            "
            class="w-full md:pr-2"
          >
            <div>
              <!-- Título antes de la tabla -->
              <h2 class="text-lg font-bold mb-4">Payment Fees</h2>

              <!-- Tabla de registros -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div
                  v-for="(item, index) in paymentFeesListCC"
                  :key="index"
                  class="p-2 border rounded"
                >
                  <div class="flex justify-between">
                    <p>{{ item.name }}</p>
                    -
                    <p>{{ item.type }}</p>
                  </div>
                  <p
                  v-if="item.type == 'fixed'"
                  >
                    <div v-if="item.type == 'fixed'" v-html="$utils.formatMoney(item.amount,  defaultCurrency)" />

                  </p>

                  <p
                  v-if="item.type == 'percentage'"
                  >
                   {{ item.amount/100 }} %

                  </p>
                </div>
              </div>

              <!-- Leyenda después de la tabla -->
              <p class="mt-4 text-sm" style="color: rgb(197, 48, 48);">
                These payment fees will be applied at the time of charging.
              </p>
            </div>
            <br />
          </div>

          <!-- Select card -->
          <div class="w-full md:w-1/2 md:pr-2">
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
                (this.is_auxVault &&
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
                  this.type_cc) ||
                (this.is_auxVault &&
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
          </div>

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
            (this.is_paypal &&
              !this.isEdit &&
              !this.type_ach &&
              this.type_cc) ||
            (this.is_auxVault && !this.isEdit && !this.type_ach && this.type_cc)
          "
        >
          <div class="w-full md:w-1/2 md:pr-2 mb-4">
            <sw-input-group :label="$t('authorize.cc_number')">
              <sw-input
                v-model="authorize.card_number"
                :disabled="isEdit"
                class="mt-1"
                focus
                type="password"
                name="card_number"
                autocomplete="off"
              >
                <template v-slot:rightIcon>
                  <!-- <eye-off-icon v-if="showCardFieldHide" class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="showCardFieldHide = !showCardFieldHide" />
                    <eye-icon v-else class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="showCardFieldHide = !showCardFieldHide" /> -->
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
                type="password"
                name="cvv"
                autocomplete="off"
                @input="$v.authorize.cvv.$touch()"
              >
                <template v-slot:rightIcon>
                  <!-- <eye-off-icon v-if="showCvvFieldHide" class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="showCvvFieldHide = !showCvvFieldHide" />
                    <eye-icon v-else class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="showCvvFieldHide = !showCvvFieldHide" /> -->
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
          <div class="w-full md:w-1/2 md:pl-2 mt-4">
            <sw-input-group
              :label="$t('customers.address_2')"
              :error="billAddress2Error"
            >
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

          <!-- state -->
          <div class="w-full md:w-1/2 md:pl-2 mt-4">
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
          <div
            class="w-full md:w-1/2 md:pr-2 flex flex-wrap mt-2 items-end justify-start mt-4"
          >
            <sw-switch
              v-model="updatebillinginformation"
              @change="Updateoptionchace"
            />
            <p class="leading-snug text-black box-title ml-4">
              {{ $t('customers.update_billing_info') }}
            </p>
          </div>

          <!-- save create account -->
          <div
            class="w-full md:w-1/2 md:pl-2 flex mt-2 items-end justify-start mt-4"
          >
            <sw-switch v-model="createaccount" @change="Createoptionchace" />
            <p class="leading-snug text-black box-title ml-4">
              {{ $t('customers.save_create_account') }}
            </p>
          </div>
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

        <!-- END AUTHORIZE  -->

        <!-- nota inicio -->

        <sw-popup
          ref="notePopup"
          class="my-6 text-sm font-semibold leading-5 text-primary-400"
        >
          <div slot="activator" class="float-right mt-1">
            + {{ $t('general.insert_note') }}
          </div>
          <note-select-popup type="Payment" @select="onSelectNote" />
        </sw-popup>

        <sw-input-group :label="$t('payments.note')" class="mt-6 mb-4 mt-sm-6">
          <base-custom-input
            v-model="formData.notes"
            :fields="PaymentFields"
            class="mb-4 mt-sm-6"
          />
        </sw-input-group>

        <!-- nota inicio -->

        <!-- botones de movil -->

        <sw-button
          class="mr-3 flex w-full mt-4 sm:hidden md:hidden"
          variant="primary-outline"
          type="button"
          @click="goBack()"
        >
          {{ $t('general.cancel') }}
        </sw-button>

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
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import { ShoppingCartIcon, XCircleIcon } from '@vue-hero-icons/solid'
import CustomFieldsMixin from '../../mixins/customFields'
import { VueDatePicker } from '@mathieustan/vue-datepicker'
import '@mathieustan/vue-datepicker/dist/vue-datepicker.min.css'
import ItemModalVue from '../../components/base/modal/ItemModal.vue'
import { EyeIcon, EyeOffIcon } from '@vue-hero-icons/outline'
import Paypal from '../payments/Paypal.vue'

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
    EyeOffIcon,
    Paypal,
  },
  mixins: [CustomFieldsMixin],

  data() {
    return {
      options_payment_methods: [],
      creditv: false,
      isdisableed: true,
      isShowPassword: false,
      isShowPassword1: false,
      isShowPassword2: false,
      isedtiablefalse: false,
      isFormDisabled: false,
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

        payment_gateways_ach: [],
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
        credit_card: '',
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
        fees: [],

        has_fees: false,
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
      payment_gateways_ach: [],
      isAuthorizeEdit: false,
      is_authorize: false,
      is_paypal: false,
      is_auxVault: false,
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
      status: [],

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
      stayOnPage: true,
      paymentFeesListCCflag: false,
      paymentFeesListACHflag: false,
      paymentFeesListCC: [],
      paymentFeesListACH: [],
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
    ...mapGetters('company', ['defaultCurrencyForInput', 'defaultCurrency']),
    // ...mapGetters('payment', ['paymentModes', 'selectedNote']),
    ...mapGetters('payment', ['selectedNote']),
    ...mapGetters('customer', ['customers']),
    dateExpirationYear: {
      get() {
        return this.authorize.date.split('-')[0]
      },
      set(year) {
        this.authorize.date = year + '-' + this.authorize.date.split('-')[1]
        this.authorize.expiration_date =
          year + '-' + this.authorize.date.split('-')[1]

        const currentYear = new Date().getFullYear()
        if (year == currentYear) {
          this.authorize.date = year + '-' + this.monthsOptions[0]
          this.authorize.expiration_date = year + '-' + this.monthsOptions[0]
        }
      },
    },
    dateExpirationMonth: {
      get() {
        return this.authorize.date.split('-')[1]
      },
      set(month) {
        this.authorize.date = this.authorize.date.split('-')[0] + '-' + month
        this.authorize.expiration_date =
          this.authorize.date.split('-')[0] + '-' + month
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
          }
          this.is_authorize = true
          this.is_paypal = false
          this.is_auxVault = false
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
          this.is_auxVault = false
          return true
        } else if (this.formData.payment_gateways.name === 'AuxVault') {
          if (this.customer) {
            let params = {
              id: this.customer.id,
            }
            this.loadCustomerData(params)
          }
          this.is_paypal = false
          this.is_authorize = false
          this.is_auxVault = true
          return true
        } else {
          this.is_paypal = false
          this.is_authorize = false
          this.is_auxVault = false
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
    getInputType() {
      if (this.isShowPassword) {
        return 'text'
      }
      return 'password'
    },
    getInputType1() {
      if (this.isShowPassword1) {
        return 'text'
      }
      return 'password'
    },
    getInputType2() {
      if (this.isShowPassword2) {
        return 'text'
      }
      return 'password'
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

      if (!this.isEdit) {
        if (this.isSettingInitialData) {
          this.isSettingInitialData = false
        } else {
          this.invoice = null
          this.formData.invoice_id = null
        }
        this.formData.amount = 0
        this.invoiceList = []
        this.fetchCustomerInvoices(newValue.id)
        this.accountList = []
        this.fetchCustomerAccounts(newValue.id)
      }
    },
    selectedNote() {
      if (this.selectedNote) {
        this.formData.notes = this.selectedNote
      }
    },
    invoice(newValue) {
      if (newValue) {
        this.formData.invoice_id = newValue.id
        this.authorize.invoice_number = newValue.invoice_number
        if (!this.isEdit) {
          this.setPaymentAmountByInvoiceData(newValue.id)
        }
        // en caso de que el monto de la factura sea menor que el credit del cliente
        if (newValue.due_amount / 100 <= this.customer.balance) {
          this.creditv = true
          this.formData.payment_method.name = null
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
    this.$v.formData.$reset()
    this.resetSelectedNote()
    this.$nextTick(() => {
      this.loadData()
      //  if (this.$route.params.id && !this.isEdit) {
      //    this.setInvoicePaymentData()
      //  }
    })
  },
  created() {
    this.fetchInitData()
  },
  methods: {
    ...mapActions('invoice', ['fetchInvoice', 'fetchInvoices']),

    ...mapActions('paymentAccounts', [
      'fetchPaymentAccounts',
      'fetchPaymentAccount',
    ]),

    ...mapActions('payment', [
      'addPayment',
      'updatePayment',
      'fetchPayment',
      'fetchPaymentModes',
      'resetSelectedNote',
      'processPayment',
      'paymentsMethodActiveCustomerCredit',
    ]),
    ...mapActions('paymentGateways', [
      'fetchPaymentGateways',
      'fetchPaymentGatewaysAch',
    ]),

    ...mapActions('authorizations', [
      'addAuthorize',
      'saveAuthorizeDB',
      'voidAuthorize',
      'refundedAuthorize',
      'addAuthorizeACH',
      'saveAuthorizeACH',
      'addAuthorizePaypal',
      'chargePaypalPro',
    ]),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('modal', ['openModal']),

    ...mapActions('customer', ['fetchCustomers', 'fetchCustomer']),

    ...mapActions('failedPaymentHistory', ['addFailedPaymentHistory']),

    /**
     * Formatea y devuelve el número de factura con el monto debido.
     * Si se proporciona un número de factura, devuelve el número y el monto formateado.
     * En caso contrario, devuelve una cadena para seleccionar una opción.
     * @param {Object} param0 - Objeto que contiene el número de factura y el monto debido.
     * @param {string} param0.invoice_number - El número de la factura.
     * @param {number} param0.due_amount - El monto debido de la factura.
     * @return {string} El número de factura y el monto debido formateado o una cadena para selección.
     */
    invoiceWithAmount({ invoice_number, due_amount }) {
      if (invoice_number) {
        // Devuelve el número de factura y el monto debido formateado si existe un número de factura
        return `${invoice_number} (${this.$utils.formatGraphMoney(
          due_amount,
          this.customer.currency
        )})`
      } else {
        // Devuelve una cadena para seleccionar si no hay número de factura
        return '- Seleccione -'
      }
    },

    /**
     * Selecciona un país y carga sus estados asociados para un tipo específico de dirección.
     * Este método asincrónico se activa cuando un usuario selecciona un país en el formulario.
     * Luego realiza una solicitud para obtener los estados relacionados con ese país y los asigna a la variable correspondiente.
     * @param {Object} country - El país seleccionado que contiene el código del país.
     * @param {string} type - El tipo de dirección ('billing' para facturación).
     */
    async countrySelected(country, type) {
      // Inicio del método countrySelected
      const vm = this
      vm.isLoading = true // Indica que se está cargando la información

      // Limpiar los estados de facturación si el tipo es 'billing'
      if (type == 'billing') {
        vm.billing_states = []
      }

      // Realizar la solicitud para obtener los estados del país seleccionado
      let res = await window.axios.get('/api/v1/states/' + country.code)
      if (res) {
        // Asignar los estados obtenidos a la variable correspondiente
        if (type == 'billing') {
          vm.billing_states = res.data.states
        }
      }
      vm.isLoading = false // Finaliza la indicación de carga
      // Fin del método countrySelected
    },

    /**
     * Carga los datos del cliente basándose en los parámetros proporcionados y actualiza el estado de autorización.
     * Este método asincrónico recupera los datos del cliente y asigna los valores relevantes a las propiedades
     * del objeto 'authorize' para su uso en procesos de autorización de pagos.
     * @param {Object} params - Parámetros utilizados para la solicitud de datos del cliente.
     */
    async loadCustomerData(params) {
      // Inicio del método loadCustomerData
      let response = await this.fetchCustomer(params)
      // Establecer el correo electrónico del pagador en el objeto 'authorize'
      this.authorize.payer_email = response.data.customer.email

      // Inicializar las propiedades del objeto 'authorize' a null
      this.authorize.name = null
      // ... (resto de inicializaciones)

      // Asignar los datos del cliente al objeto 'authorize'
      this.authorize.first_name = response.data.customer.first_name
      this.authorize.last_name = response.data.customer.last_name
      this.authorize.company_name = response.data.customer.company.name
      this.authorize.email = response.data.customer.email

      // Si existe una dirección de facturación, asignar sus valores a las propiedades correspondientes
      if (response.data.customer.billing_address) {
        this.authorize.name = response.data.customer.contact_name
        // ... (resto de asignaciones de la dirección de facturación)

        // Si hay un país o estado en la dirección de facturación, actualizar los estados 'billing_country' y 'billing_state'
        if (response.data.customer.billing_address.country_id) {
          this.billing_country = response.data.customer.billing_address.country
          // Llamar al método 'countrySelected' para cargar los estados relacionados con el país de facturación
          this.countrySelected(this.billing_country, 'billing')
        }
        if (response.data.customer.billing_address.state_id) {
          this.billing_state = response.data.customer.billing_address.state
        }
      }
      // Fin del método loadCustomerData
    },
    /**
     * Abre un modal para agregar un nuevo modo de pago.
     * Este método asincrónico activa un modal con un componente específico para la creación o edición de modos de pago.
     */
    async addPaymentMode() {
      // Inicio del método addPaymentMode
      // Abre un modal con el título y componente especificado para agregar un modo de pago
      this.openModal({
        title: this.$t('settings.customization.payments.add_payment_mode'),
        componentName: 'PaymentMode',
      })
      // Fin del método addPaymentMode
    },

    /**
     * Verifica si la generación automática de números de pago está activada y obtiene el siguiente número disponible.
     * Este método asincrónico consulta la configuración de la compañía para la generación automática de pagos
     * y recupera el siguiente número de pago disponible si la generación automática está habilitada.
     * @return {boolean} Retorna verdadero si la generación automática está habilitada y se obtuvo el siguiente número.
     */
    async checkAutoGenerate() {
      // Inicio del método checkAutoGenerate
      // Obtener la configuración de la compañía para la generación automática de pagos
      let response = await this.fetchCompanySettings(['payment_auto_generate'])

      // Obtener el siguiente número de pago disponible
      let response1 = await axios.get('/api/v1/next-number?key=payment')

      // Si la generación automática está activada y se obtuvo el siguiente número
      if (response.data && response.data.payment_auto_generate === 'YES') {
        if (response1.data) {
          // Asignar el siguiente número y el prefijo al estado local
          this.paymentNumAttribute = response1.data.nextNumber
          this.paymentPrefix = response1.data.prefix
          // Retornar verdadero indicando que la generación automática está habilitada
          return true
        }
      } else {
        // Si la generación automática no está habilitada, asignar solo el prefijo
        this.paymentPrefix = response1.data.prefix
      }
      // Fin del método checkAutoGenerate
    },

    /**
     * Regresa a la vista anterior de clientes.
     * Este método utiliza el enrutador de Vue para regresar a la vista detallada del cliente especificado por el ID en la ruta.
     */
    goBack() {
      // Inicio del método goBack
      // Utilizar el enrutador de Vue para regresar a la vista anterior
      this.$router.push(`/admin/customers/${this.$route.params.id}/view`)
      // Fin del método goBack
    },

    async loadData() {
      if (this.isEdit) {
        this.isRequestOnGoing = true

        let response = await this.fetchPayment(this.$route.params.id)

        this.formData = { ...this.formData, ...response.data.payment }

        if (this.formData.payment_method_id) {
          this.fetchingPaymentMethod = true
          let payments = await this.fetchPaymentModes({ limit: 'all' })

          //  console.log('payments load data')
          //console.log(payments)

          this.formData.payment_method =
            payments.data.paymentMethods.data.filter(
              (payment) => payment.id == this.formData.payment_method_id
            )
          this.PaymentModeSelected(this.formData.payment_method[0])
          this.fetchingPaymentMethod = false
        }

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

        if (
          this.invoice != null &&
          this.formData.payment_method &&
          this.formData.transaction_status == 'Approved'
        ) {
          let gateway = 0
          let typegateway = 'N'
          Array.prototype.forEach.call(this.formData.payment_method, (user) => {
            // ...

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
        }

        if (
          this.invoice != null &&
          this.formData.payment_method == null &&
          this.formData.transaction_status == 'Approved'
        ) {
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
          this.status = [
            { value: 'Approved', text: 'Approved' },
            { value: 'Void', text: 'Void' },
          ]
        }

        if (this.formData.transaction_status != 'Approved') {
          this.notEditable = true
          this.isedtiablefalse = true
        }

        let res = await this.fetchCustomFields({
          type: 'Payment',
          limit: 'all',
        })

        this.setEditCustomFields(
          response.data.payment.fields,
          res.data.customFields.data
        )

        this.isRequestOnGoing = false
      } else {
        this.isRequestOnGoing = true
        this.checkAutoGenerate()
        this.setInitialCustomFields('Payment')
        this.formData.payment_date = moment().format('YYYY-MM-DD')
        let resPaymentModes = await this.paymentsMethodActiveCustomerCredit()
        //console.log('resPaymentModes 1787')
        // console.log(resPaymentModes)

        this.options_payment_methods = resPaymentModes.data.payment_methods

        await this.fetchCustomers({ limit: 'all' })
        if (this.$route.params.id) {
          this.setPaymentCustomer(parseInt(this.$route.params.id))
        }
        this.isRequestOnGoing = false
      }
      return true
    },
    /**
     * Inicializa los datos necesarios al cargar el componente.
     * Realiza una solicitud GET para obtener la lista de países y los almacena en 'this.countries'.
     */
    async fetchInitData() {
      this.initLoad = true
      let res = await window.axios.get('/api/v1/countries')
      if (res) {
        this.countries = res.data.countries
      }
      this.initLoad = false
    },
    /**
     * Establece el cliente seleccionado para el pago.
     * Busca en la lista de clientes por ID y asigna el cliente encontrado a 'this.customer'.
     * @param {number} id - El ID del cliente a establecer.
     */
    setPaymentCustomer(id) {
      this.customer = this.customers.find((c) => {
        return c.id === id
      })
    },
    /**
     * Asigna los datos de pago de la factura al estado local.
     * Este método asincrónico recupera los datos de la factura basándose en el ID proporcionado en la ruta,
     * y luego establece la información del usuario de la factura en el estado local.
     */
    async setInvoicePaymentData() {
      // Inicio del proceso de recuperación de datos de la factura
      //console.log('-----setInvoicePaymentData inicio')

      // Espera a que la promesa de fetchInvoice se resuelva y asigna el resultado a 'data'
      let data = await this.fetchInvoice(this.$route.params.id)
      // Muestra en consola los datos recuperados (opcional)
      //console.log(data)

      // Asigna el usuario de la factura recuperada al estado local 'customer'
      this.customer = data.data.invoice.user

      // Fin del proceso de asignación de datos de la factura
      //console.log('-----setInvoicePaymentData fin')
    },
    /**
     * Establece el monto del pago basado en los datos de una factura específica.
     * Obtiene los detalles de la factura mediante su ID y actualiza el formulario con el monto debido.
     * @param {number} id - El ID de la factura a consultar.
     */
    async setPaymentAmountByInvoiceData(id) {
      // Inicio del método setPaymentAmountByInvoiceData
      let data = await this.fetchInvoice(id)
      this.formData.amount = data.data.invoice.due_amount
      this.maxPayableAmount = data.data.invoice.due_amount
      // Fin del método setPaymentAmountByInvoiceData
    },
    /**
     * Obtiene las facturas impagas de un cliente específico.
     * Realiza una solicitud para obtener las facturas con estado 'UNPAID' y las añade a 'this.invoiceList'.
     * @param {number} userId - El ID del usuario cuyas facturas se van a buscar.
     */
    async fetchCustomerInvoices(userId) {
      let data = {
        customer_id: userId,
        status: 'UNPAID',
        limit: 'all',
      }
      /*let response = await this.fetchInvoicespayments(data)

      response.data.invoices.data.forEach((element) => {
        this.invoiceList.push(element)
      })*/
    },

    /**
     * Recupera las cuentas de pago de un cliente específico.
     * Realiza una solicitud para obtener las cuentas con estado 'UNPAID' y procesa la lista para ACH y tarjetas de crédito.
     * @param {number} userId - El ID del usuario cuyas cuentas de pago se van a buscar.
     */
    async fetchCustomerAccounts(userId) {
      // Inicio del método fetchCustomerAccounts
      let data = {
        customer_id: userId,
        status: 'UNPAID',
      }
      let response = await this.fetchPaymentAccounts(data)
      // Filtra y procesa las cuentas ACH
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
      // Filtra y procesa las tarjetas de crédito
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
      // Fin del método fetchCustomerAccounts
    },

    /////////////Seleccionar payment mode inicio
    /**
     * Selecciona y maneja el modo de pago basado en la opción elegida por el usuario.
     * Actualiza los montos del formulario y ejecuta funciones específicas según el tipo de cuenta aceptada.
     * @param {object} val - Objeto que contiene los detalles del modo de pago seleccionado.
     */
    async PaymentModeSelected(val) {
      // Inicio del método PaymentModeSelected
      try {
        this.creditv = false
        this.paymentFeesListCCflag = false
        this.paymentFeesListACHflag = false
        this.paymentFeesListCC = []
        this.paymentFeesListACH = []

        if (this.invoice !== null) {
          this.formData.amount = this.maxAmountIsNotCustomerCreditBalance
          this.maxPayableAmount = this.maxAmountIsNotCustomerCreditBalance
        }

        if (val.account_accepted === 'A') {
          this.handleACHMode(val)
        } else if (val.account_accepted === 'C') {
          this.handleCreditCardMode(val)
        } else {
          this.handleOtherMode()
        }

        let band = false

        if (val.add_payment_gateway) {
          band = await this.handlePaymentGateways(val)
        } else {
          this.handleNoPaymentGateway()
        }

        this.resetFormFields()

        return band
      } catch (error) {
        // console.log(error)
      }
      // Fin del método PaymentModeSelected
    },

    /**
     * Maneja la selección del modo de pago con tarjeta de crédito.
     * Establece las variables de control para el tipo de pago y procesa el pago a través de un gateway si es necesario.
     * @param {object} val - Objeto que contiene los detalles del modo de pago seleccionado.
     */
    async handleCreditCardMode(val) {
      // Inicio del método handleCreditCardMode
      this.type_cc = true
      this.type_ach = false
      if (val.add_payment_gateway) {
        await this.handlePaymentGatewayCreditCard(val)
      } else {
        this.handleNoPaymentGateway()
      }
      // Fin del método handleCreditCardMode
    },

    /**
     * Maneja la selección del modo de pago ACH (Automated Clearing House).
     * Establece las variables de control para el tipo de pago ACH y procesa el pago a través de un gateway si es necesario.
     * @param {object} val - Objeto que contiene los detalles del modo de pago ACH seleccionado.
     */
    async handleACHMode(val) {
      // Inicio del método handleACHMode
      this.type_ach = true
      this.type_cc = false
      if (val.add_payment_gateway) {
        await this.handlePaymentGatewayACH(val)
      } else {
        this.handleNoPaymentGateway()
      }
      // Fin del método handleACHMode
    },

    /**
     * Procesa el pago con tarjeta de crédito a través del gateway de pago seleccionado.
     * Obtiene la lista de gateways de pago disponibles y establece el gateway predeterminado para la tarjeta de crédito.
     * @param {object} val - Objeto que contiene los detalles del modo de pago con tarjeta de crédito seleccionado.
     */
    async handlePaymentGatewayCreditCard(val) {
      // Inicio del método handlePaymentGatewayCreditCard
      let res = await this.fetchPaymentGateways()
      if (res) {
        this.payment_gateways = res.data.payment_gateways
        this.setDefaultPaymentGateway(val, this.payment_gateways)
        this.add_payment_gateway_select = true
      }
      // Fin del método handlePaymentGatewayCreditCard
    },

    /**
     * Procesa el pago ACH a través del gateway de pago seleccionado.
     * Obtiene la lista de gateways de pago ACH disponibles y establece el gateway predeterminado para ACH.
     * @param {object} val - Objeto que contiene los detalles del modo de pago ACH seleccionado.
     */
    async handlePaymentGatewayACH(val) {
      // Inicio del método handlePaymentGatewayACH
      let res = await this.fetchPaymentGatewaysAch()
      if (res) {
        this.payment_gateways_ach = res.data.payment_gateways
        this.setDefaultPaymentGateway(val, this.payment_gateways_ach)
        this.add_payment_gateway_select = true
      }
      // Fin del método handlePaymentGatewayACH
    },

    /**
     * Maneja la selección de un modo de pago que no es ni tarjeta de crédito ni ACH.
     * Desactiva los tipos de pago y llama a la función para manejar la ausencia de un gateway de pago.
     */
    handleOtherMode() {
      // Inicio del método handleOtherMode
      this.type_cc = false
      this.type_ach = false
      this.handleNoPaymentGateway()
      // Fin del método handleOtherMode
    },

    /**
     * Procesa la selección de un gateway de pago.
     * Obtiene la lista de gateways de pago disponibles y establece el gateway predeterminado.
     * @param {object} val - Objeto que contiene los detalles del gateway de pago seleccionado.
     * @returns {boolean} band - Indica si se ha establecido correctamente un gateway de pago.
     */
    async handlePaymentGateways(val) {
      // Inicio del método handlePaymentGateways
      let band = false
      let res = await this.fetchPaymentGateways()
      if (res) {
        this.payment_gateways = res.data.payment_gateways
        this.setDefaultPaymentGateway(val, this.payment_gateways)
        this.add_payment_gateway_select = true
        band = true
      }
      return band
      // Fin del método handlePaymentGateways
    },

    /**
     * Maneja la situación cuando no se selecciona un gateway de pago.
     * Desactiva la selección de gateway de pago y restablece los campos relacionados en el formulario.
     */
    handleNoPaymentGateway() {
      // Inicio del método handleNoPaymentGateway
      this.add_payment_gateway_select = false
      this.formData.payment_gateways = null
      this.is_authorize = false
      // Fin del método handleNoPaymentGateway
    },

    /**
     * Restablece los campos del formulario según el tipo de pago seleccionado.
     * Llama a métodos específicos para restablecer los campos de ACH o tarjeta de crédito.
     */
    resetFormFields() {
      // Inicio del método resetFormFields
      if (this.type_ach && this.account) {
        this.resetACHFormFields()
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
        this.resetCreditCardFormFields()
      }
      // Fin del método resetFormFields
    },

    /**
     * Restablece los campos del formulario relacionados con el pago ACH.
     * Limpia todos los campos del formulario asociados con la información de la cuenta ACH y la dirección de facturación.
     */
    resetACHFormFields() {
      // Inicio del método resetACHFormFields
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
      // Fin del método resetACHFormFields
    },

    /**
     * Restablece los campos del formulario relacionados con el pago con tarjeta de crédito.
     * Limpia todos los campos del formulario asociados con la información de la tarjeta de crédito y la dirección de facturación.
     */
    resetCreditCardFormFields() {
      // Inicio del método resetCreditCardFormFields
      this.card = null
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
      // Fin del método resetCreditCardFormFields
    },
    /**
     * Establece el gateway de pago predeterminado basado en la selección del usuario o el valor por defecto.
     * Itera sobre la lista de gateways de pago y asigna el gateway seleccionado o el predeterminado al formulario.
     * @param {object} val - Objeto que contiene los detalles de la selección del usuario.
     * @param {array} gateways - Arreglo de gateways de pago disponibles.
     */
    setDefaultPaymentGateway(val, gateways) {
      // Inicio del método setDefaultPaymentGateway
      gateways.forEach((element) => {
        // Asigna el gateway de pago basado en la selección del usuario
        if (
          element.id === val.payment_gateways_id &&
          val.payment_gateways_id === 1
        ) {
          if (this.type_ach) {
            this.formData.payment_gateways_ach = element
            if (
              this.formData.payment_gateways_ach &&
              typeof this.formData.payment_gateways_ach === 'object' &&
              !Array.isArray(this.formData.payment_gateways_ach) &&
              Object.keys(this.formData.payment_gateways_ach).length > 0
            ) {
              console.log('payment_gateways_ach es válido')
              console.log(this.formData.payment_gateways_ach)
              this.setPaymentFees(this.formData.payment_gateways_ach, 'A')
            }
          } else {
            this.formData.payment_gateways = element
            if (
              this.formData.payment_gateways &&
              typeof this.formData.payment_gateways === 'object' &&
              !Array.isArray(this.formData.payment_gateways) &&
              Object.keys(this.formData.payment_gateways).length > 0
            ) {
              console.log('payment_gateways es válido')
              console.log(this.formData.payment_gateways)
              this.setPaymentFees(this.formData.payment_gateways, 'C')
            }
          }
        }
        // Asigna el gateway de pago predeterminado
        if (element.default === 1) {
          if (this.type_ach) {
            this.formData.payment_gateways_ach = element
            if (
              this.formData.payment_gateways_ach &&
              typeof this.formData.payment_gateways_ach === 'object' &&
              !Array.isArray(this.formData.payment_gateways_ach) &&
              Object.keys(this.formData.payment_gateways_ach).length > 0
            ) {
              console.log('payment_gateways_ach es válido')
              console.log(this.formData.payment_gateways_ach)
              this.setPaymentFees(this.formData.payment_gateways_ach, 'A')
            }
          } else {
            this.formData.payment_gateways = element
            if (
              this.formData.payment_gateways &&
              typeof this.formData.payment_gateways === 'object' &&
              !Array.isArray(this.formData.payment_gateways) &&
              Object.keys(this.formData.payment_gateways).length > 0
            ) {
              console.log('payment_gateways es válido')
              console.log(this.formData.payment_gateways)
              this.setPaymentFees(this.formData.payment_gateways, 'C')
            }
          }
        }
      })

      // Fin del método setDefaultPaymentGateway
    },

    setPaymentFees(paymentgateway, type) {
      // Log the input parameters
      console.log('setPaymentFees called with:', paymentgateway, type)

      // Check if the type is 'C' (Credit Card)
      if (type == 'C') {
        console.log('Type is C (Credit Card)')

        // Check if fee charges are enabled for the payment gateway
        if (paymentgateway.IsPaymentFeeActive == 'YES') {
          console.log('Fee charges are enabled for Credit Card')

          // Set the payment fees list for Credit Card
          this.paymentFeesListCC = paymentgateway.registrationdatafees
          console.log('paymentFeesListCC set to:', this.paymentFeesListCC)

          // Set the flag to true
          this.paymentFeesListCCflag = true
          console.log('paymentFeesListCCflag set to true')
        } else {
          console.log('Fee charges are not enabled for Credit Card')
        }
      }

      // Check if the type is 'A' (ACH)
      if (type == 'A') {
        console.log('Type is A (ACH)')

        // Check if fee charges are enabled for the payment gateway
        if (paymentgateway.IsPaymentFeeActive == 'YES') {
          console.log('Fee charges are enabled for ACH')

          // Set the payment fees list for ACH
          this.paymentFeesListACH = paymentgateway.registrationdatafees
          console.log('paymentFeesListACH set to:', this.paymentFeesListACH)

          // Set the flag to true
          this.paymentFeesListACHflag = true
          console.log('paymentFeesListACHflag set to true')
        } else {
          console.log('Fee charges are not enabled for ACH')
        }
      }
    },

    ///// seleccionar payment mode fin

    /////////////Seleccionar payment mode inicio
    /**
     * Establece el estado de la transacción basado en la selección del usuario.
     * Activa o desactiva la verificación del estado de la transacción si el texto seleccionado es 'Void' o 'Refunded'.
     * @param {object} val - Objeto que contiene el texto del estado de la transacción seleccionado.
     */
    async transactionStatusSelected(val) {
      // Inicio del método transactionStatusSelected
      if (val.text === 'Void' || val.text === 'Refunded') {
        this.transactionStatusCheck = true
      } else {
        this.transactionStatusCheck = false
      }
      // Fin del método transactionStatusSelected
    },

    /////////////////////////// INICIO sumitpaymentdata
    async submitPaymentData() {
      try {
        // console.log('Submiting process')
        //console.log(this.formData)

        if (!this.isEdit) {
          if (!this.validateNewPayment()) {
            return false
          }
        }

        if (!(await this.createPayment())) {
          throw new Error('Failed to create payment.')
        }

        return true // Success
      } catch (error) {
        // console.log(error)
        //console.error('Error:', error.message)
        this.handlePaymentError(error.message)
        return false
      }
    },

    //Valida el formulario
    validateNewPayment() {
      this.$v.customer.$touch()
      this.$v.formData.$touch()

      if (this.isAuthorize) {
        this.$v.authorize.$touch()
      }

      if (this.formData.amount === 0) {
        throw new Error(this.$t('general.invalid_form_amount'))
      }

      if (this.formData.payment_method == null) {
        throw new Error(this.$t('payments.select_a_payment_method'))
      }

      if (this.$v.$invalid) {
        throw new Error(this.$t('general.invalid_form_data'))
      }

      if (!this.formData.payment_method.name) {
        throw new Error(this.$t('payments.select_a_payment_method'))
      }

      return true // Validación exitosa
    },

    /// crea nuevo metodos
    /**
     * Determina y ejecuta el proceso de creación de pago dependiendo de si hay facturas asociadas.
     * Este método asincrónico decide si se debe manejar un pago sin factura o con factura basándose en la presencia de una lista de facturas.
     * @return {Promise} Una promesa que se resuelve con el resultado del proceso de pago correspondiente.
     */
    async createPayment() {
      // Inicio del método createPayment
      // Verificar si hay una lista de facturas asociada al pago
      if (
        !this.formData.invoice_list ||
        this.formData.invoice_list.length === 0
      ) {
        // Si no hay facturas, manejar el pago como un pago sin factura
        return this.handlePaymentWithoutInvoice()
      } else {
        // Si hay facturas, manejar el pago como un pago con factura
        return this.handlePaymentWithInvoice()
      }
      // Fin del método createPayment
    },

    /**
     * Inicia el proceso de pago sin asociar a una factura.
     * Este método muestra una ventana de confirmación y, si el usuario acepta, procede a realizar el pago.
     * @return {Promise} Una promesa que se resuelve si el pago se procesa correctamente o se rechaza si el usuario cancela.
     */
    handlePaymentWithoutInvoice() {
      // Inicio del método handlePaymentWithoutInvoice
      // Retorna una nueva promesa
      return new Promise((resolve, reject) => {
        // Mostrar una ventana de confirmación al usuario para pagos sin factura
        swal({
          title: this.$t('general.are_you_sure'),
          text: this.$tc('payments.payment_invoice_message'), // Mensaje para pagos sin factura
          icon: 'warning',
          buttons: true,
        }).then((value) => {
          // Si el usuario confirma la acción
          if (value) {
            // Procesar el pago y resolver la promesa
            this.processPaymentvue().then(resolve).catch(reject)
          } else {
            // Rechazar la promesa si el usuario cancela
            return false
          }
        })
      })
      // Fin del método handlePaymentWithoutInvoice
    },
    /**
     * Crea un proceso de pago asociado a una factura.
     * Muestra una ventana de confirmación y procesa el pago si el usuario confirma.
     * Retorna una promesa que se resuelve o rechaza basada en la acción del usuario.
     */
    handlePaymentWithInvoice() {
      // Inicio del método handlePaymentWithInvoice
      // Retorna una nueva promesa
      return new Promise((resolve, reject) => {
        // Mostrar una ventana de confirmación al usuario para pagos con factura
        swal({
          title: this.$t('general.are_you_sure'),
          text: this.$tc('payments.create_payment'), // Mensaje para pagos con factura
          icon: 'warning',
          buttons: true,
        }).then((value) => {
          // Si el usuario confirma la acción
          if (value) {
            // Procesar el pago y resolver la promesa
            this.processPaymentvue().then(resolve).catch(reject)
          } else {
            // Rechazar la promesa si el usuario cancela
            return false
          }
        })
      })
      // Fin del método handlePaymentWithInvoice
    },

    ///proceso de pago
    async processPaymentvue() {
      // Simula un proceso de pago exitoso
      // Preparar los datos necesarios para el pago con factura
      // console.log('3071')
      this.authorize.user_id = this.formData.user_id
      this.authorize.invoice_id = this.formData.invoice_id
      this.authorize.amount = this.formData.amount

      // agregar el numero de pago
      this.formData.payment_number =
        this.paymentPrefix + '-' + this.paymentNumAttribute

      this.authorize.payment_number = this.formData.payment_number

      // Determinar los nombres y la dirección según el método de pago
      if (this.formData.payment_method.account_accepted !== 'N') {
        this.authorize.first_name =
          this.authorize.first_name != null
            ? this.authorize.first_name
            : this.customer.name
        this.authorize.last_name =
          this.authorize.last_name != null
            ? this.authorize.last_name
            : this.customer.name
      } else {
        this.authorize.company_name =
          this.customer.first_name != null
            ? this.customer.first_name
            : this.customer.contact_name
        this.authorize.name =
          this.customer.last_name != null
            ? this.customer.last_name
            : this.customer.name
      }

      if (this.authorize.name != null) {
        this.authorize.name = this.authorize.name.substring(0, 21)
      }

      if (this.authorize.company_name != null) {
        this.authorize.company_name = this.authorize.company_name.substring(
          0,
          21
        )
      }

      this.authorize.address_1 = this.authorize.address_street_1
      this.authorize.address_2 = this.authorize.address_street_2

      // Determinar el tipo de cuenta para el pago
      if (this.formData.payment_method.account_accepted === 'C') {
        // Si el método de pago es con tarjeta de crédito
        this.authorize.payment_method_id = this.formData.payment_method.id
        this.authorize.payment_account_type =
          this.card == null ? 'CC' : this.card.payment_account_type

        this.authorize.payment_gateway_id = this.formData.payment_gateways.id

        if (this.formData.credit_cards && this.formData.credit_cards.name) {
          this.authorize.credit_card = this.formData.credit_cards.name
        }
        if (this.paymentFeesListCC.length > 0) {
          this.authorize.fees = this.paymentFeesListCC.map((fee) => fee.id)
        }
        console.log(this.authorize.fees)
        this.authorize.has_fees = 1
      } else {
        // Si el método de pago es con transferencia bancaria (ACH)
        this.authorize.payment_account_type = 'ACH'

        this.authorize.ACH_type = this.formData.ACH_type.value
        this.authorize.account_number = this.formData.account_number
        this.authorize.routing_number = this.formData.routing_number
        this.authorize.num_check = this.formData.num_check
        this.authorize.bank_name = this.formData.bank_name

        this.authorize.payment_gateway_id =
          this.formData.payment_gateways_ach.id

        if (this.paymentFeesListACHflag) {
          if (this.paymentFeesListACH.length > 0) {
            this.authorize.fees = this.paymentFeesListACH.map((fee) => fee.id)
          }
          this.authorize.has_fees = 1
        }
      }

      this.authorize.status = 'A'
      this.authorize.company_id = this.customer.company_id
      this.authorize.client_id = this.customer.id
      this.authorize.payment_date = this.formData.payment_date
      this.authorize.date = this.formData.payment_date

      this.authorize.payer_id = this.$store.state.user.currentUser.id

      try {
        // Procesar el pago según el método de pago seleccionado
        if (this.formData.payment_method.add_payment_gateway === 0) {
          // Si no se necesita agregar una pasarela de pago externa
          let data = {
            ...this.formData,
            payment_method_id: this.formData.payment_method
              ? this.formData.payment_method.id
              : null,
            payment_date: moment(this.formData.payment_date).format(
              'YYYY-MM-DD'
            ),
          }
          // Agregar el pago y obtener la respuesta
          this.isLoading = true
          let responseAddPayment = await this.addPayment(data)
          // console.log(responseAddPayment)
          // Mostrar mensaje de éxito o error según la respuesta del servidor
          if (responseAddPayment.data.success) {
            window.toastr['success'](this.$t('payments.created_message'))

            if (this.stayOnPage) {
              window.location.reload()
            } else {
              //   console.log(responseAddPayment)
              this.$router.push(
                `/admin/payments/${responseAddPayment.data.payment.id}/view`
              )
            }

            this.isLoading = false
            return true
          } else {
            //console.log(responseAddPayment)
            window.toastr['error'](responseAddPayment.data.error)
            this.isLoading = false
            return false
          }
        } else {
          //console.log('entro payment activo')
          // Si se necesita procesar el pago con ACH
          this.isLoading = true
          // Establecer detalles para el pago con ACH
          this.authorize.customcode = this.formData.customcode
          // Procesar el pago con ACH y obtener la respuesta
          this.authorize.nameOnAccount = this.authorize.name

          if (this.authorize.nameOnAccount != null) {
            this.authorize.nameOnAccount =
              this.authorize.nameOnAccount.substring(0, 21)
          }
          // console.log(this.authorize);
          const response = await this.processPayment(this.authorize)
          // Mostrar mensaje de éxito o error según la respuesta del servidor
          if (response.data.success) {
            window.toastr['success'](this.$t('payments.created_message'))
            if (this.stayOnPage) {
              window.location.reload()
            } else {
              this.$router.push(
                `/admin/payments/${response.data.payment_id}/view`
              )
            }
            this.isLoading = false
            return true
          } else {
            window.toastr['error'](response.data.message)
            this.isLoading = false
            return false
          }
        }
      } catch (error) {
        // Manejar errores
        this.isLoading = false

        if (error.response && error.response.status === 422) {
          // console.log('Error de validación:', error.response)

          // Si el formato del error es el primero
          if (error.response.data.hasOwnProperty('errors')) {
            for (let key in error.response.data.errors) {
              let message = key + ': ' + error.response.data.errors[key][0]
              window.toastr['error'](message)
            }
          }
          // Si el formato del error es el segundo
          else if (error.response.data.hasOwnProperty('data')) {
            for (let key in error.response.data.data) {
              let message = key + ': ' + error.response.data.data[key][0]
              window.toastr['error'](message)
            }
          }
        } else {
          // Manejar otros tipos de errores
          // console.log('Error desconocido:', error)
          return false
        }

        //console.log(error)
        return false
      }
    },
    /**
     * Muestra un mensaje de error utilizando la librería toastr.
     * Este método se utiliza para informar al usuario sobre errores ocurridos durante el proceso de pago.
     * @param {string} errorMessage - El mensaje de error que se mostrará al usuario.
     */
    handlePaymentError(errorMessage) {
      // Inicio del método handlePaymentError
      // Mostrar el mensaje de error al usuario
      window.toastr['error'](errorMessage)
      // Fin del método handlePaymentError
    },

    /**
     * Muestra un mensaje de éxito utilizando la librería toastr.
     * Este método se utiliza para informar al usuario sobre el éxito de una operación de pago.
     * @param {string} successMessage - El mensaje de éxito que se mostrará al usuario.
     */
    handlePaymentSuccess(successMessage) {
      // Inicio del método handlePaymentSuccess
      // Mostrar el mensaje de éxito al usuario
      window.toastr['success'](successMessage)
      // Fin del método handlePaymentSuccess
    },
    ///////////////////// FIN sumitpaymentdata

    alertPaymentNumberAlreadyExists() {
      this.$swal({
        title: this.$t('general.payment_number_exists_title'),
        text: this.$t('general.payment_number_exists_text'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: this.$t('general.automatic'),
        confirmButtonColor: '#5851D8',
        cancelButtonText: this.$t('general.manual'),
        showLoaderOnConfirm: true,
      }).then((result) => {
        this.isFormDisabled = true
        if (result.value) {
          this.generateAutomaticPaymentNumber()
        }
      })
    },

    async generateAutomaticPaymentNumber() {
      let response_next_number = await axios.get(
        '/api/v1/next-number?key=payment'
      )

      this.paymentNumAttribute = response_next_number.data.nextNumber
      this.formData.payment_number =
        this.paymentPrefix + '-' + this.paymentNumAttribute

      if (this.creditv && this.formData.amount / 100 > this.customer.balance) {
        this.formData.amount = this.customer.balance * 100
      }

      let data = {
        ...this.formData,
        payment_method_id: this.formData.payment_method
          ? this.formData.payment_method.id
          : null,
        payment_date: moment(this.formData.payment_date).format('YYYY-MM-DD'),
      }

      this.isLoading = true
      let response = await this.addPayment(data)

      if (response.data.success) {
        window.toastr['success'](this.$t('payments.created_message'))
        this.$router.push(`/admin/payments/${response.data.payment.id}/view`)
      } else {
        window.toastr['error']('Error')
      }
    },

    onSelectNote(data) {
      this.formData.notes = '' + data.notes
      this.$refs.notePopup.close()
    },
    Updateoptionchace(val) {
      this.updatebillinginformation = val
      this.formData.updatebillinginformation = val ? true : false
    },

    Createoptionchace(val) {
      this.createaccount = val
      this.formData.createaccount = val ? true : false
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
      this.authorize.card_number = item.card_number
      this.formData.credit_cards = { name: item.credit_card }
      this.authorize.cvv = item.cvv
      if (item.expiration_date) {
        this.authorize.date = item.expiration_date
        this.authorize.expiration_date = item.expiration_date
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
      let response = await this.addPayment(data)
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

    async PaymentSelectedFees(val) {
      this.paymentFeesListCCflag = false
      this.paymentFeesListACHflag = false
      this.paymentFeesListCC = []
      this.paymentFeesListACH = []
      console.log('Set payment fees credit')
      this.setPaymentFees(val, 'C')
    },

    async PaymentSelectedFeesAch(val) {
      this.paymentFeesListCCflag = false
      this.paymentFeesListACHflag = false
      this.paymentFeesListCC = []
      this.paymentFeesListACH = []
      console.log('Set payment fees ach')
      this.setPaymentFees(val, 'A')
    },
  },
}
</script>
