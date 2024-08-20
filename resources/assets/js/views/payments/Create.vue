<template>
  <base-page class="relative payment-create">
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
            :disabled="isLoading || isFormDisabled"
            variant="primary-outline"
            type="button"
            size="lg"
            class="mr-3 hidden sm:flex"
            @click="cancelForm()"
          >
            <x-circle-icon class="w-6 h-6 mr-1 -ml-2" />
            {{ $t('general.cancel') }}
          </sw-button>

          <!-- button back que salga unicamente si isShowIdentificationVerification esta activo -->
          <sw-button
            v-if="isShowIdentificationVerification"
            :disabled="isLoading"
            variant="primary-outline"
            type="button"
            size="lg"
            class="mr-3 flex"
            @click="backToForm()"
          >
            <ArrowLeftIcon class="w-6 h-6 mr-1 -ml-2" />
            {{ $t('general.back') }}
          </sw-button>

          <div
            v-if="
              !formData.payment_method || !formData.payment_method.paypal_button
            "
          >
            <sw-button
              v-if="!notEditable"
              :loading="isLoading"
              :disabled="
                isLoading ||
                (isShowIdentificationVerification && !verificationSuccessful)
              "
              variant="primary"
              type="submit"
              size="lg"
              class="flex"
            >
              <save-icon
                v-if="!isLoading && !showIconsModeVerification"
                class="mr-2 -ml-1"
              />
              <ArrowRightIcon
                v-if="!isLoading && showIconsModeVerification"
                class="mr-2 -ml-1"
              />
              {{ showTextButtonSubmit }}
            </sw-button>
            <sw-button v-else style="display: none"></sw-button></div
        ></template>
      </sw-page-header>
      <!-- Fin de header -->

      <base-loader v-if="isRequestOnGoing" />

      <div v-if="!isShowIdentificationVerification">
        <!-- Inicio de cuerpo -->

        <sw-card v-if="!isRequestOnGoing">
          <div class="grid gap-6 grid-col-1 md:grid-cols-2 grid-col-1">
            <!-- campso basicos -->
            <sw-input-group
              :label="$t('payments.date')"
              :error="DateError"
              required
            >
              <base-date-picker
                v-model="formData.payment_date"
                :calendar-button="true"
                class="mt-1"
                calendar-button-icon="calendar"
                :disabled="isEdit || isFormDisabled"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('payments.payment_number')"
              :error="paymentNumError"
              required
            >
              <sw-input
                :prefix="`${paymentPrefix} - `"
                v-model.trim="paymentNumAttribute"
                class="mt-1"
                :disabled="isEdit"
                autocomplete="off"
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
                :disabled="isEdit || isFormDisabled"
                :placeholder="$t('customers.select_a_customer')"
                label="name"
                class="mt-1"
                track-by="id"
              >
                <template v-slot:singleLabel="{ option }">
                  <div class="flex items center">
                    <div v-if="option" class="flex">
                      <span>{{ option.name }}</span>
                      <BadgeCheckIcon
                        v-if="option && option.verified"
                        class="h-5 ml-2 text-success"
                      />
                    </div>
                  </div>
                </template>

                <template v-slot:option="{ option }">
                  <div class="flex items center px-4">
                    <div v-if="option" class="flex">
                      <span>{{ option.name }}</span>
                      <BadgeCheckIcon
                        v-if="option && option.verified"
                        class="h-5 ml-2 text-success"
                      />
                    </div>
                  </div>
                </template>
              </sw-select>
            </sw-input-group>

            <sw-input-group :label="$t('payments.invoice_title')">
              <div
                @focusin="isActiveSelect = true"
                @focusout="isActiveSelect = false"
              >
                <sw-select
                  v-model="invoice_list"
                  :options="invoiceList"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :multiple="true"
                  :disabled="isEdit || isFormDisabled"
                  :placeholder="$t('invoices.select_invoice')"
                  :custom-label="invoiceWithAmount"
                  class="mt-1"
                  track-by="invoice_number"
                />
              </div>
            </sw-input-group>

            <!-- MONTO DEL PAGO -->
            <sw-input-group
              :label="$t('payments.amount')"
              :error="amountError"
              required
            >
              <div class="relative w-full mt-1">
                <sw-money
                  v-model="amount"
                  :currency="customerCurrency"
                  :disabled="isEdit || isFormDisabled"
                  class="relative w-full focus:border focus:border-solid focus:border-primary-500"
                />
              </div>
            </sw-input-group>

            <!-- SELECT METODO DE PAGO  -->
            <sw-input-group :label="$t('payments.payment_mode')">
              <div
                @focusin="isActiveSelect = true"
                @focusout="isActiveSelect = false"
              >
                <sw-select
                  v-if="!fetchingPaymentMethod"
                  v-model="formData.payment_method"
                  :options="paymentModesWithSettings"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="false"
                  :placeholder="$t('payments.select_payment_mode')"
                  :max-height="150"
                  label="name"
                  class="mt-1"
                  :error="paymentMethodError"
                  @select="PaymentModeSelected"
                  :disabled="isEdit || isFormDisabled"
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
              </div>
              <div class="p-2">
                <p
                  class="p-0 m-0 text-xs leading-tight text-gray-500"
                  :style="{ color: existSettingsForPaymentModes.color }"
                  style="font-size: 14px"
                >
                  {{ existSettingsForPaymentModes.message }}
                </p>
              </div>
            </sw-input-group>

            <!-- SECCION CREDITO - CUANDO SE SELECCIONA FACTURA Y EL CLIENTE TIENE CREDITO DISPONIBLE PARA USAR -->
            <sw-input-group v-if="creditv">
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
                  type="button"
                  @click="paymentWithCustomerBalance"
                >
                  {{ $t('payments.apply_credit') }}
                </sw-button>
              </div>
            </sw-input-group>
            <!-- FIN SECCION CREDITO -->

            <!--  BOTON PAYPAL  -->
            <div
              v-if="
                formData.payment_method &&
                formData.payment_method.paypal_button &&
                customer != null &&
                formData.amount > 0 &&
                !isEdit &&
                !isFormDisabled
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
            <!-- FIN BOTON PAYPAL  -->

            <!--  BOTON STRIPE  -->
            <div
              v-if="
                formData.payment_method &&
                formData.payment_method.stripe_button &&
                customer != null &&
                formData.amount > 0 &&
                !isEdit &&
                !isFormDisabled
              "
            >
              <stripe-button
                :formData="formData"
                :codePayment="codePayment"
                :invoice_number="invoice_number"
                :customer="customer"
                @stripeSuccess="stripeSuccess"
              ></stripe-button>
            </div>
            <!-- FIN BOTON STRIPE  -->

            <!-- SELECT DE ESTADO DE LA TRANSACCION  -->
            <sw-input-group
              v-if="isEdit"
              :label="$t('tax_groups.status')"
              class="mt-1"
              required
            >
              <sw-select
                v-model="formData.status"
                :options="status"
                :disabled="isTransactionStatus || isEditAbleFalse"
                :searchable="true"
                :show-labels="false"
                :tabindex="16"
                :allow-empty="false"
                :placeholder="$t('tax_groups.status')"
                label="text"
                track-by="value"
                @select="transactionStatusSelected"
              />
            </sw-input-group>
            <!-- FIN SELECT DE ESTADO DE LA TRANSACCION  -->

            <!-- SELECTORES CUANDO EL PAYMENT NO ES EDICION -->
            <div v-if="add_payment_gateway_select && !isEdit">
              <!-- CHECKBOX PARA CAMBIAR EL ESTADO DE LA TRANSACCION CON AUTHORIZE -->
              <div
                v-if="transactionStatusCheck && isPaymentTypeAuthorize"
                class="flex my-8"
              >
                <div class="relative w-12">
                  <sw-checkbox
                    v-model="formData.status_with_authorize"
                    class="absolute"
                  />
                </div>

                <div class="ml-4">
                  <p
                    class="p-0 mb-1 text-base leading-snug text-black box-title"
                  >
                    {{ $t('payments.transaction_status_authorize_message') }}
                  </p>
                </div>
              </div>
              <!-- FIN CHECKBOX PARA CAMBIAR EL ESTADO DE LA TRANSACCION CON AUTHORIZE -->

              <!-- CHECKBOX PARA APLICAR COMO CREDITO AL CUSTOMER -->
              <div v-if="transactionStatusCheckUnapply" class="flex my-8">
                <div class="relative w-12">
                  <sw-checkbox
                    v-model="formData.applied_credit_customer"
                    class="absolute"
                  />
                </div>

                <div class="ml-4">
                  <p
                    class="p-0 mb-1 text-base leading-snug text-black box-title"
                  >
                    {{
                      $t(
                        'payments.transaction_status_authorize_message_unapply'
                      )
                    }}
                  </p>
                </div>
              </div>
              <!-- FIN CHECKBOX PARA APLICAR COMO CREDITO AL CUSTOMER -->
            </div>
            <!-- FIN SELECTORES PAYMENT CUANDO NO ES EDICION -->

            <!-- SELECTORES PAYMENT CUANDO ES EDICION -->
            <div v-if="add_payment_gateway_select && isEdit">
              <!-- SELECTOR DE CUENTAS BANCARIAS  -->
              <sw-input-group
                v-if="this.type_ach && !isEdit"
                :label="$t('payments.select_accounts')"
              >
                <sw-select
                  v-model="account"
                  :options="accountList"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="false"
                  :disabled="isEdit || isFormDisabled"
                  :placeholder="$t('payments.select_accounts')"
                  label="name_account_number"
                  class="mt-1"
                  track-by="id"
                  @select="selectItemAccount"
                  :loading="isLoadingPayments"
                />
              </sw-input-group>
              <!-- FIN SELECTOR DE CUENTAS BANCARIAS  -->

              <!-- SELECTOR PAYMENT GATEWAYS -->
              <sw-input-group
                v-if="
                  isEdit && add_payment_gateway_select && !type_ach && type_cc
                "
                :label="$t('settings.payment_gateways.title')"
              >
                <sw-select
                  v-model="formData.payment_gateways"
                  :options="payment_gateways"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :disabled="isEdit || isFormDisabled"
                  :placeholder="$t('items.select_a_type')"
                  class="mt-1"
                  track-by="id"
                  label="name"
                  select=""
                />
                <div class="p-2" v-if="type_cc">
                  <p
                    class="p-0 m-0 text-xs leading-tight text-gray-500"
                    :style="{ color: existSettingsForPaymentGateways.color }"
                    style="font-size: 14px"
                  >
                    {{ existSettingsForPaymentGateways.message }}
                  </p>
                </div>
              </sw-input-group>
              <!-- FIN SELECTOR PAYMENT GATEWAYS -->

              <!-- CHECKBOX PARA CAMBIAR EL ESTADO DE LA TRANSACCION CON AUTHORIZE -->
              <div
                v-if="transactionStatusCheck && isPaymentTypeAuthorize"
                class="flex my-8"
              >
                <div class="relative w-12">
                  <sw-checkbox
                    v-model="formData.status_with_authorize"
                    class="absolute"
                  />
                </div>

                <div class="ml-4">
                  <p
                    class="p-0 mb-1 text-base leading-snug text-black box-title"
                  >
                    {{ $t('payments.transaction_status_authorize_message') }}
                  </p>
                </div>

                <div
                  v-if="
                    (isPaymentTypeAuthorize &&
                      this.formData.status_with_authorize) ||
                    (isPaymentTypeAuxVault &&
                      this.formData.status_with_aux_vault)
                  "
                >
                  <div
                    v-if="
                      Array.isArray(payment_list_associated) &&
                      payment_list_associated.length
                    "
                  >
                    <h2 style="color: red; font-weight: bold">
                      {{ $t('payments.transaction_warning') }} :
                    </h2>
                    <p style="color: red">
                      <span
                        v-for="(payment, index) in payment_list_associated"
                        :key="payment.id"
                      >
                        {{ payment.payment_number
                        }}<span
                          v-if="index < payment_list_associated.length - 1"
                          >,
                        </span>
                      </span>
                    </p>
                  </div>
                </div>
              </div>
              <!-- FIN CHECKBOX PARA CAMBIAR EL ESTADO DE LA TRANSACCION CON AUTHORIZE -->

              <!-- CHECKBOX PARA CAMBIAR EL ESTADO DE LA TRANSACCION CON AUXVAULT -->
              <div
                v-if="transactionStatusCheck && isPaymentTypeAuxVault"
                class="flex my-8"
              >
                <div class="relative w-12">
                  <sw-checkbox
                    v-model="formData.status_with_aux_vault"
                    class="absolute"
                  />
                </div>

                <div class="ml-4">
                  <p
                    class="p-0 mb-1 text-base leading-snug text-black box-title"
                  >
                    {{ $t('payments.transaction_status_aux_vault_message') }}
                  </p>
                </div>

                <div
                  v-if="
                    (isPaymentTypeAuthorize &&
                      this.formData.status_with_authorize) ||
                    (isPaymentTypeAuxVault &&
                      this.formData.status_with_aux_vault)
                  "
                >
                  <div
                    v-if="
                      Array.isArray(payment_list_associated) &&
                      payment_list_associated.length
                    "
                  >
                    <h2 style="color: red; font-weight: bold">
                      {{ $t('payments.transaction_warning') }} :
                    </h2>
                    <p style="color: red">
                      <span
                        v-for="(payment, index) in payment_list_associated"
                        :key="payment.id"
                      >
                        {{ payment.payment_number
                        }}<span
                          v-if="index < payment_list_associated.length - 1"
                          >,
                        </span>
                      </span>
                    </p>
                  </div>
                </div>
              </div>
              <!-- FIN CHECKBOX PARA CAMBIAR EL ESTADO DE LA TRANSACCION CON AUXVAULT -->

              <!-- CHECKBOX PARA APLICAR COMO CREDITO AL CUSTOMER -->
              <div v-if="transactionStatusCheckUnapply" class="flex my-8">
                <div class="relative w-12">
                  <sw-checkbox
                    v-model="formData.applied_credit_customer"
                    class="absolute"
                  />
                </div>

                <div class="ml-4">
                  <p
                    class="p-0 mb-1 text-base leading-snug text-black box-title"
                  >
                    {{
                      $t(
                        'payments.transaction_status_authorize_message_unapply'
                      )
                    }}
                  </p>
                </div>
              </div>
              <!-- FIN CHECKBOX PARA APLICAR COMO CREDITO AL CUSTOMER -->
            </div>

            <!-- FIN SELECTORES PAYMENT CUANDO ES EDICION -->

            <!--   FIN SELECTS GATEWAYS PARA REALIZAR PAGOS CON TARJETAS DE CREDITO   -->
          </div>

          <!-- INFORMACION PARA PAGOS CON GATEWAYS  CON TARJETAS DE CREDITO INICIO-->

          <h6
            v-if="
              (this.is_authorize && !this.type_ach && this.type_cc) ||
              (this.is_paypal &&
                !this.isEdit &&
                !this.type_ach &&
                this.type_cc) ||
              (this.is_auxVault &&
                !this.isEdit &&
                !this.type_ach &&
                this.type_cc)
            "
            class="mb-4 mt-2em"
          >
            {{ $t('payments.credit_card_information') }}
          </h6>
          <sw-divider
            v-if="
              (this.is_authorize && !this.type_ach && this.type_cc) ||
              (this.is_paypal &&
                !this.isEdit &&
                !this.type_ach &&
                this.type_cc) ||
              (this.is_auxVault &&
                !this.isEdit &&
                !this.type_ach &&
                this.type_cc)
            "
            class="w-full"
          />

          <div
            v-if="
              (this.is_authorize && !this.type_ach && this.type_cc) ||
              (this.is_paypal &&
                !this.isEdit &&
                !this.type_ach &&
                this.type_cc) ||
              (this.is_auxVault &&
                !this.isEdit &&
                !this.type_ach &&
                this.type_cc)
            "
            class="grid gap-6 grid-col-1 md:grid-cols-2 grid-col-1"
          >
            <sw-input-group
              v-if="
                this.add_payment_gateway_select &&
                !this.type_ach &&
                this.type_cc
              "
              :label="$t('settings.payment_gateways.title')"
            >
              <!-- Payment gateways  -->
              <sw-select
                v-model="formData.payment_gateways"
                :options="payment_gateways"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :disabled="isEdit || isFormDisabled"
                :placeholder="$t('items.select_a_type')"
                class="mt-1"
                track-by="id"
                label="name"
                @select="PaymentSelectedFees"
              />
              <div class="pt-2 pl-2 pr-2" v-if="type_cc">
                <p
                  class="p-0 m-0 text-xs leading-tight text-gray-500"
                  :style="{ color: existSettingsForPaymentGateways.color }"
                  style="font-size: 14px"
                >
                  {{ existSettingsForPaymentGateways.message }}
                </p>
              </div>
            </sw-input-group>

            <!-- Select cards  -->
            <sw-input-group
              v-if="
                (this.is_authorize &&
                  this.isEdit &&
                  !this.type_ach &&
                  this.type_cc) ||
                (this.is_authorize &&
                  !this.isEdit &&
                  !this.type_ach &&
                  this.type_cc) ||
                (this.is_paypal &&
                  this.isEdit &&
                  !this.type_ach &&
                  this.type_cc) ||
                (this.is_paypal &&
                  !this.isEdit &&
                  !this.type_ach &&
                  this.type_cc) ||
                (this.is_auxVault &&
                  this.isEdit &&
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
                :disabled="isEdit || isFormDisabled"
                :placeholder="$t('payments.select_cards')"
                label="card_number_cvv"
                track-by="id"
                @select="selectItemCard"
                :loading="isLoadingPayments"
              />
            </sw-input-group>




            <!-- Select type cards  -->

            <sw-input-group
              :label="$t('settings.payment_gateways.credit_cards')"
            >
              <sw-select
                v-model="formData.credit_cards"
                :options="credit_cards"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :disabled="isEdit"
                :placeholder="$t('items.select_a_type')"
                label="name"
              />
            </sw-input-group>
            <!-- CC number  -->

            <sw-input-group
              :label="$t('authorize.cc_number')"
              required
              :error="ccNumberError"
            >
              <sw-input
                v-model="authorize.card_number"
                :disabled="isEdit || isFormDisabled"
                class="mt-1"
                focus
                :type="showCardFieldHide ? 'password' : 'text'"
                name="card_number"
                autocomplete="off"
                :invalid="$v.authorize.card_number.$error"
              >
              </sw-input>
            </sw-input-group>

            <!-- Date -->

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
                  :invalid="$v.authorize.date.$error"
                  :disabled="isFormDisabled"
                />

                <sw-select
                  placeholder="YYYY"
                  class="ml-1"
                  :searchable="true"
                  :allow-empty="false"
                  v-model="dateExpirationYear"
                  :options="yearsOptions"
                  :invalid="$v.authorize.date.$error"
                  :disabled="isFormDisabled"
                />
              </div>
            </sw-input-group>

            <!-- CVV -->
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
                :disabled="isFormDisabled"
              >
              </sw-input>
            </sw-input-group>
            <!-- Email -->
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
                :disabled="isFormDisabled"
                @input="$v.authorize.payer_email.$touch()"
              />
            </sw-input-group>
          </div>


          <div
            v-if="
              this.paymentFeesListCCflag && this.paymentFeesListCC.length > 0
            "
            class="w-full md:pr-2"
          >
            <div>
              <br />
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
          <!-- Billing Address para tarjeta de credito -->
          <div
            v-if="
              (this.is_authorize && !this.type_ach && this.type_cc) ||
              (this.is_paypal &&
                !this.isEdit &&
                !this.type_ach &&
                this.type_cc) ||
              (this.is_auxVault &&
                !this.isEdit &&
                !this.type_ach &&
                this.type_cc)
            "
          >
            <h6 class="w-full my-4">
              {{ $t('customers.billing_address') }}
            </h6>
            <sw-divider />
            <div class="grid gap-6 grid-col-1 md:grid-cols-2 grid-col-1">
              <!--nombre -->
              <sw-input-group :label="$t('customers.customer_addres_name')">
                <sw-input
                  v-model.trim="authorize.name"
                  type="text"
                  name="address_name"
                  tabindex="7"
                  autocomplete="off"
                  :disabled="isFormDisabled"
                />
              </sw-input-group>

              <!-- phone -->
              <sw-input-group :label="$t('customers.phone')">
                <sw-input
                  v-model.trim="authorize.phone"
                  type="text"
                  name="phone"
                  tabindex="13"
                  autocomplete="off"
                  :disabled="isFormDisabled"
                />
              </sw-input-group>

              <!-- Address -->
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
                  :disabled="isFormDisabled"
                  @input="$v.authorize.address_street_1.$touch()"
                />
              </sw-input-group>

              <!-- street 2 -->
              <sw-input-group
                :label="$t('general.street_2')"
              :error="billAddress2Error">
                <sw-textarea
                  v-model.trim="authorize.address_street_2"
                  :placeholder="$t('general.street_2')"
                  type="text"
                  name="billing_street2"
                  rows="3"
                  tabindex="12"
                  :disabled="isFormDisabled"
                  @input="$v.authorize.address_street_2.$touch()"
                />
              </sw-input-group>

              <!-- city -->
              <sw-input-group
                :error="cityError"
                :label="$t('customers.city')"
                required
              >
                <sw-input
                  v-model="authorize.city"
                  :disabled="isFormDisabled"
                  name="city"
                  type="text"
                  tabindex="10"
                  autocomplete="off"
                />
              </sw-input-group>

              <!-- state -->

              <sw-input-group
                :error="stateIdError"
                :label="$t('customers.state')"
                required
              >
                <sw-select
                  v-model="billing_state"
                  :invalid="$v.authorize.state_id.$error"
                  :options="billing_states"
                  :disabled="isFormDisabled"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :tabindex="8"
                  :placeholder="$t('general.select_state')"
                  label="name"
                  track-by="id"
                />
              </sw-input-group>

              <!-- country -->
              <sw-input-group
                :error="countryIdError"
                :label="$t('customers.country')"
                required
              >
                <sw-select
                  v-model="billing_country"
                  :disabled="isFormDisabled"
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

              <!-- zip code -->
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
                  :disabled="isFormDisabled"
                />
              </sw-input-group>

              <!-- update billing infos-->
              <sw-input-group>
                <div
                  class="w-full md:w-1/2 md:pr-2 flex flex-wrap mt-2 items-end justify-start mt-4"
                >
                  <sw-switch
                    v-model="updatebillinginformation"
                    @change="Updateoptionchace"
                    :disabled="isFormDisabled"
                  />
                  <p class="leading-snug text-black box-title ml-4">
                    {{ $t('customers.update_billing_info') }}
                  </p>
                </div>
              </sw-input-group>

              <!-- save create account -->
              <sw-input-group>
                <div
                  class="w-full md:w-1/2 md:pl-2 flex mt-2 items-end justify-start mt-4"
                >
                  <sw-switch
                    v-model="createaccount"
                    @change="Createoptionchace"
                    :disabled="isFormDisabled"
                  />
                  <p class="leading-snug text-black box-title ml-4">
                    {{ $t('customers.save_create_account') }}
                  </p>
                </div>
              </sw-input-group>
            </div>
          </div>

          <!-- INFORMACION PARA PAGOS CON GATEWAYS  CON TARJETAS DE CREDITO FIN-->

          <!-- INFORMACION PARA PAGOS CON GATEWAYS  CON ACH INICIO-->

          <h6
            v-if="this.type_ach && !this.isEdit"
            class="sw-section-title mb-2 mt-4"
          >
            {{ $t('payment_accounts.bank_account_info') }}
          </h6>

          <sw-divider v-if="this.type_ach && !this.isEdit" class="w-full" />

          <div
            v-if="this.type_ach && !this.isEdit"
            class="grid gap-6 grid-col-1 md:grid-cols-2 grid-col-1"
          >
            <!-- SELECTOR DE CUENTAS BANCARIAS  -->
            <sw-input-group
              v-if="type_ach"
              :label="$t('payments.select_accounts')"
            >
              <sw-select
                v-model="account"
                :options="accountList"
                :searchable="true"
                :show-labels="false"
                :allow-empty="false"
                :disabled="isEdit || isFormDisabled"
                :placeholder="$t('payments.select_accounts')"
                label="name_account_number"
                class="mt-1"
                track-by="id"
                @select="selectItemAccount"
                :loading="isLoadingPayments"
              />
            </sw-input-group>
            <!-- FIN SELECTOR DE CUENTAS BANCARIAS  -->

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

            <sw-input-group
              :label="$t('payment_accounts.ACH_type')"
              class="md:mr-2"
              required
            >
              <sw-select
                v-model="formData.ACH_type"
                :options="bank_account_type"
                :searchable="true"
                :show-labels="false"
                :tabindex="16"
                :allow-empty="false"
                label="text"
                track-by="value"
                :disabled="isFormDisabled"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('payment_accounts.account_number')"
              class="md:ml-2"
              required
            >
              <sw-input
                v-model="formData.account_number"
                focus
                :type="showAccountFieldHide ? 'password' : 'text'"
                name="account_number"
                tabindex="1"
                autocomplete="off"
                :disabled="isFormDisabled"
              >
              </sw-input>
            </sw-input-group>

            <sw-input-group
              :label="$t('payment_accounts.routing_number')"
              class="md:mr-2"
              required
            >
              <sw-input
                v-model="formData.routing_number"
                focus
                :type="showRoutingFieldHide ? 'password' : 'text'"
                name="routing_number"
                tabindex="1"
                autocomplete="off"
                :disabled="isFormDisabled"
              >
              </sw-input>
            </sw-input-group>

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
                :disabled="isFormDisabled"
              />
            </sw-input-group>

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
                autocomplete="off"
                :disabled="isFormDisabled"
              />
            </sw-input-group>
          </div>




          <div
            v-if="
              this.paymentFeesListACHflag && this.paymentFeesListACH.length > 0
            "
            class="w-full md:pr-2"
          >
            <div>
              <br />
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




          <!-- Billing Address  -->

          <h6 v-if="this.type_ach && !this.isEdit" class="my-4">
            {{ $t('customers.billing_address') }}
          </h6>
          <sw-divider v-if="this.type_ach && !this.isEdit" class="w-full" />

          <div
            v-if="this.type_ach && !this.isEdit"
            class="grid gap-6 grid-col-1 md:grid-cols-2 grid-col-1"
          >
            <!-- Name on account -->

            <sw-input-group :label="$t('customers.customer_nameACH')">
              <sw-input
                v-model.trim="authorize.name"
                type="text"
                name="address_name"
                tabindex="7"
                autocomplete="off"
                :disabled="isFormDisabled"
              />
            </sw-input-group>

            <!-- phone -->
            <sw-input-group :label="$t('customers.phone')">
              <sw-input
                v-model.trim="authorize.phone"
                type="text"
                name="phone"
                tabindex="13"
                autocomplete="off"
                :disabled="isFormDisabled"
              />
            </sw-input-group>

            <!-- Address -->
            <sw-input-group :label="$t('customers.address')" required>
              <sw-textarea
                v-model.trim="authorize.address_street_1"
                :placeholder="$t('general.street_1')"
                type="text"
                name="billing_street1"
                rows="3"
                tabindex="11"
                :disabled="isFormDisabled"
              />
            </sw-input-group>

            <!-- street 2 -->
            <sw-input-group :label="$t('general.street_2')">
              <sw-textarea
                v-model.trim="authorize.address_street_2"
                :placeholder="$t('general.street_2')"
                type="text"
                name="billing_street2"
                rows="3"
                tabindex="12"
                :disabled="isFormDisabled"
              />
            </sw-input-group>

            <!-- City -->
            <sw-input-group :label="$t('customers.city')" required>
              <sw-input
                v-model="authorize.city"
                name="city"
                type="text"
                tabindex="10"
                autocomplete="off"
                :disabled="isFormDisabled"
              />
            </sw-input-group>

            <!-- state -->
            <sw-input-group :label="$t('customers.state')" required>
              <sw-select
                v-model="billing_state"
                :options="billing_states"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :tabindex="8"
                :placeholder="$t('general.select_state')"
                label="name"
                track-by="id"
                :disabled="isFormDisabled"
              />
            </sw-input-group>

            <!-- Country -->

            <sw-input-group :label="$t('customers.country')" required>
              <sw-select
                v-model="billing_country"
                :options="countries"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('general.select_country')"
                label="name"
                track-by="id"
                :disabled="isFormDisabled"
                @select="countrySelected($event, 'billing')"
              />
            </sw-input-group>

            <!-- zip code -->
            <sw-input-group :label="$t('customers.zip_code')" required>
              <sw-input
                tabindex="14"
                v-model.trim="authorize.zip"
                type="text"
                name="zip"
                autocomplete="off"
                :disabled="isFormDisabled"
              />
            </sw-input-group>

            <!-- update billing info -->
            <sw-input-group>
              <div
                class="w-full md:w-1/2 md:pr-2 flex flex-wrap mt-2 items-end justify-start mt-4"
              >
                <sw-switch
                  v-model="updatebillinginformation"
                  @change="Updateoptionchace"
                  :disabled="isFormDisabled"
                />
                <p class="leading-snug text-black box-title ml-4">
                  {{ $t('customers.update_billing_info') }}
                </p>
              </div>
            </sw-input-group>

            <!-- save create account -->
            <sw-input-group>
              <div
                class="w-full md:w-1/2 md:pl-2 flex mt-2 items-end justify-start mt-4"
              >
                <sw-switch
                  v-model="createaccount"
                  @change="Createoptionchace"
                  :disabled="isFormDisabled"
                />
                <p class="leading-snug text-black box-title ml-4">
                  {{ $t('customers.save_create_account') }}
                </p>
              </div>
            </sw-input-group>
          </div>

          <!-- INFORMACION PARA PAGOS CON GATEWAYS  CON ACH FIN-->

          <!-- INTEGRACION DE PAYPAL -->

          <sw-popup
            v-if="!isActiveSelect && !isFormDisabled"
            ref="notePopup"
            class="my-6 text-sm font-semibold leading-5 text-primary-400"
          >
            <div slot="activator" class="float-right mt-1">
              + {{ $t('general.insert_note') }}
            </div>
            <note-select-popup type="Payment" @select="onSelectNote" />
          </sw-popup>

          <sw-input-group
            v-if="!isFormDisabled"
            :label="$t('payments.note')"
            class="mt-6 mb-4"
          >
            <base-custom-input
              v-model="formData.notes"
              :fields="PaymentFields"
              class="mb-4"
            />
          </sw-input-group>

          <!-- boton de guardado dektops -->
          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="hidden sm:flex md:mt-4"
          >
            <save-icon
              v-if="!isLoading && !isIdentificationVerification"
              class="mr-2 -ml-1"
            />
            <ArrowRightIcon
              v-if="!isLoading && isIdentificationVerification"
              class="mr-2 -ml-1"
            />
            {{ showTextButtonSubmit }}
          </sw-button>

          <!-- boton de cancelado movil -->

          <sw-button
            :disabled="isLoading || isFormDisabled"
            variant="primary-outline"
            type="button"
            size="lg"
            class="mr-3 flex w-full mt-4 sm:hidden md:hidden"
            @click="cancelForm()"
          >
            <x-circle-icon class="w-6 h-6 mr-1 -ml-2" />
            {{ $t('general.cancel') }}
          </sw-button>

          <!-- boton de guardado movil -->
          <sw-button
            :disabled="isLoading"
            :loading="isLoading"
            variant="primary"
            type="submit"
            class="flex w-full mt-4 mb-2 mb-md-0 sm:hidden md:hidden"
          >
            <save-icon
              v-if="!isLoading && !showIconsModeVerification"
              class="mr-2 -ml-1"
            />
            <ArrowRightIcon
              v-if="!isLoading && showIconsModeVerification"
              class="mr-2 -ml-1"
            />
            {{ showTextButtonSubmit }}
          </sw-button>
        </sw-card>
        <!-- fin de cuerpo -->
      </div>
      <!-- validar documentation  -->
      <div v-if="isShowIdentificationVerification">
        <IdentityVerification
          :customer="customer"
          :date="formData.payment_date"
          :paymentMethod="formData.payment_method"
          :paymentGateway="formData.payment_gateways"
          :invoiceCredit="invoice_list"
          :isVerificationSuccessful="verificationSuccessful"
          @verificationSuccessful="verificationSuccessful = true"
          @goToPayment="submitPaymentData"
          @cancelValidateEvent="backToForm"
        />
      </div>
    </form>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import {
  ShoppingCartIcon,
  XCircleIcon,
  ArrowLeftIcon,
  ArrowRightIcon,
  BadgeCheckIcon,
} from '@vue-hero-icons/solid'

import CustomFieldsMixin from '../../mixins/customFields'
import { VueDatePicker } from '@mathieustan/vue-datepicker'
import '@mathieustan/vue-datepicker/dist/vue-datepicker.min.css'
import ItemModalVue from '../../components/base/modal/ItemModal.vue'
import { EyeIcon, EyeOffIcon } from '@vue-hero-icons/outline'
import Paypal from './Paypal.vue'
import StripeButton from './StripeButton.vue'
import IdentityVerification from './IdentityVerification/index.vue'

const {
  required,
  between,
  numeric,
  email,
  minLength,
  maxLength,
  requiredIf,
} = require('vuelidate/lib/validators')

export default {
  components: {
    ShoppingCartIcon,
    XCircleIcon,
    ArrowLeftIcon,
    ArrowRightIcon,
    BadgeCheckIcon,
    VueDatePicker,
    EyeIcon,
    EyeOffIcon,
    Paypal,
    StripeButton,
    IdentityVerification,
  },
  mixins: [CustomFieldsMixin],

  data() {
    return {
      isShowIdentificationVerification: false,
      isIdentificationVerification: false,
      verificationSuccessful: false,
      isUserAction: false,
      invoiceobjet: null,
      isFormDisabled: false,
      isActiveSelect: false,
      isPaymentTypeAuthorize: false,
      isPaymentTypeAuxVault: false,
      creditv: false,
      isdisableed: true,
      isShowPassword: false,
      isShowPassword1: false,
      isShowPassword2: false,
      isEditAbleFalse: false,
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
        status_with_aux_vault: true,
        applied_credit_customer: true,
        add_payment_gateway: 0,
        status: {
          value: 'Approved',
          text: 'Approved',
        },
        void_status_change: false,
        refunded_status_change: false,
        invoice_list: [],
        invoices: [],
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
        invoice_list: [],
        invoices: [],
        fees: [],
        has_fees: 0,
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
      maxAmountIsNotCustomerCreditBalance: 0,
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
      transactionStatusCheckUnapply: false,
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
        { value: 'Declined', text: 'Declined' },
        { value: 'Void', text: 'Void' },
        { value: 'Error', text: 'Error' },
        { value: 'Pending', text: 'Pending' },
        { value: 'Refunded', text: 'Refunded' },
        { value: 'Returned', text: 'Returned' },
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
        credit_card_number: null,
        credit_card_type: null,
        credit_card_expiration_date: null,
        authorize_object: null,
        type_trasaction: null,
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
      invoice_list: [],
      invoices_validatio: false,
      firstExecution: false,
      payment_list_associated: [],
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
            between: between(1, this.maxPayableAmount),
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
            between: between(1, this.maxPayableAmount),
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
        } /*
        invoice: {
          required
        },*/,
        formData: {
          payment_date: {
            required,
          },
          amount: {
            required,
            between: between(1, this.maxPayableAmount),
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
    } else if (this.isPaypal) {
      return {
        customer: {
          required,
        } /*
        invoice: {
          required,
        },*/,
        formData: {
          payment_date: {
            required,
          },
          amount: {
            required,
            between: between(1, this.maxPayableAmount),
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
            between: between(1, this.maxPayableAmount),
          },
          status: {
            required,
          },
          /*
          payment_method: {
            required,
          },*/
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
    ...mapGetters('payment', [
      'paymentModes',
      'selectedNote',
      'paymentModesWithSettings',
      'existAuthorizeSetting',
      'existPaypalSetting',
      'existAuxVaultSetting',
    ]),
    ...mapGetters('customer', ['customers']),

    showIconsModeVerification() {
      // si isIdentificationVerification es true y isShowIdentificationVerification es false y isLoading es false entonces se muestra el icono de verificacion
      if (
        this.isIdentificationVerification &&
        !this.isShowIdentificationVerification
      ) {
        return true
      }
      return false
    },
    showTextButtonSubmit() {
      if (this.showIconsModeVerification) {
        return this.$t('payments.verify_identification')
      } else if (this.isEdit) {
        return this.$t('payments.update_payment')
      } else {
        return this.$t('payments.save_payment')
      }
    },

    isAuthorizeSetting() {
      if (this.existAuthorizeSetting > 0) return true
      return false
    },

    isPaypalSetting() {
      if (this.existPaypalSetting > 0) return true
      return false
    },

    isAuxVaultSetting() {
      if (this.existAuxVaultSetting > 0) return true
      return false
    },

    isAuthorizeAndPaypalSetting() {
      if (this.existAuthorizeSetting > 0 && this.existPaypalSetting > 0)
        return true
      return false
    },

    existSettingsForPaymentModes() {
      if (
        this.isAuthorizeSetting &&
        this.isPaypalSetting &&
        this.isAuxVaultSetting
      ) {
        // Código cuando los tres son verdaderos
        return {
          message: this.$t(
            'payments.payment_mode_settings.authorize_and_paypal_auxpay'
          ),
          color: 'green',
        }
      } else if (
        !this.isAuthorizeSetting &&
        !this.isPaypalSetting &&
        !this.isAuxVaultSetting
      ) {
        // Código cuando los tres son falsos
        return {
          message: this.$t('payments.payment_mode_settings.none'),
          color: 'red',
        }
      } else if (
        this.isAuthorizeSetting &&
        this.isPaypalSetting &&
        !this.isAuxVaultSetting
      ) {
        // Código cuando Authorize y Paypal son verdaderos, AuxVault es falso
        return {
          message: this.$t(
            'payments.payment_mode_settings.authorize_and_paypal'
          ),
          color: 'green',
        }
      } else if (
        this.isAuthorizeSetting &&
        !this.isPaypalSetting &&
        this.isAuxVaultSetting
      ) {
        // Código cuando Authorize y AuxVault son verdaderos, Paypal es falso
        return {
          message: this.$t(
            'payments.payment_mode_settings.authorize_and_auxpay'
          ),
          color: 'green',
        }
      } else if (
        !this.isAuthorizeSetting &&
        this.isPaypalSetting &&
        this.isAuxVaultSetting
      ) {
        // Código cuando Paypal y AuxVault son verdaderos, Authorize es falso
        return {
          message: this.$t('payments.payment_mode_settings.auxpay_and_paypal'),
          color: 'green',
        }
      } else if (
        this.isAuthorizeSetting &&
        !this.isPaypalSetting &&
        !this.isAuxVaultSetting
      ) {
        // Código cuando solo Authorize es verdadero

        return {
          message: this.$t('payments.payment_mode_settings.authorize'),
          color: 'green',
        }
      } else if (
        !this.isAuthorizeSetting &&
        this.isPaypalSetting &&
        !this.isAuxVaultSetting
      ) {
        // Código cuando solo Paypal es verdadero
        return {
          message: this.$t('payments.payment_mode_settings.paypal'),
          color: 'green',
        }
      } else if (
        !this.isAuthorizeSetting &&
        !this.isPaypalSetting &&
        this.isAuxVaultSetting
      ) {
        // Código cuando solo AuxVault es verdadero
        return {
          message: this.$t('payments.payment_mode_settings.auxpay'),
          color: 'green',
        }
      }
    },

    existSettingsForPaymentGateways() {
      if (
        this.isAuthorizeSetting &&
        this.isPaypalSetting &&
        this.isAuxVaultSetting
      ) {
        // Código cuando los tres son verdaderos
        return {
          message: this.$t(
            'payments.payment_gateways_settings.authorize_and_paypal_auxpay'
          ),
          color: 'green',
        }
      } else if (
        !this.isAuthorizeSetting &&
        !this.isPaypalSetting &&
        !this.isAuxVaultSetting
      ) {
        // Código cuando los tres son falsos
        return {
          message: this.$t('payments.payment_gateways_settings.none'),
          color: 'red',
        }
      } else if (
        this.isAuthorizeSetting &&
        this.isPaypalSetting &&
        !this.isAuxVaultSetting
      ) {
        // Código cuando Authorize y Paypal son verdaderos, AuxVault es falso
        return {
          message: this.$t(
            'payments.payment_gateways_settings.authorize_and_paypal'
          ),
          color: 'green',
        }
      } else if (
        this.isAuthorizeSetting &&
        !this.isPaypalSetting &&
        this.isAuxVaultSetting
      ) {
        // Código cuando Authorize y AuxVault son verdaderos, Paypal es falso
        return {
          message: this.$t(
            'payments.payment_gateways_settings.authorize_and_auxpay'
          ),
          color: 'green',
        }
      } else if (
        !this.isAuthorizeSetting &&
        this.isPaypalSetting &&
        this.isAuxVaultSetting
      ) {
        // Código cuando Paypal y AuxVault son verdaderos, Authorize es falso
        return {
          message: this.$t(
            'payments.payment_gateways_settings.auxpay_and_paypal'
          ),
          color: 'green',
        }
      } else if (
        this.isAuthorizeSetting &&
        !this.isPaypalSetting &&
        !this.isAuxVaultSetting
      ) {
        // Código cuando solo Authorize es verdadero

        return {
          message: this.$t('payments.payment_gateways_settings.authorize'),
          color: 'green',
        }
      } else if (
        !this.isAuthorizeSetting &&
        this.isPaypalSetting &&
        !this.isAuxVaultSetting
      ) {
        // Código cuando solo Paypal es verdadero
        return {
          message: this.$t('payments.payment_gateways_settings.paypal'),
          color: 'green',
        }
      } else if (
        !this.isAuthorizeSetting &&
        !this.isPaypalSetting &&
        this.isAuxVaultSetting
      ) {
        // Código cuando solo AuxVault es verdadero
        return {
          message: this.$t('payments.payment_gateways_settings.auxpay'),
          color: 'green',
        }
      }
    },

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
      if (this.isAuthorize) {
        const months = []
        const yearSelect = this.authorize.date.split('-')[0]
        const currentMonth =
          yearSelect == new Date().getFullYear() ? new Date().getMonth() + 1 : 1
        for (let i = 1; i <= 12; i++) {
          months.push(i < 10 ? `0${i}` : `${i}`)
        }
        return months
      }
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
      if (this.isShowIdentificationVerification) {
        return this.$t('payments.verify_identification')
      }
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
    isPaypal() {
      if (
        this.formData.payment_method &&
        this.formData.payment_method.paypal_button
      ) {
        return true
      }
      return false
    },
    isAuthorize() {
      //console.log('is authorize')
      if (this.isEdit && this.isAuthorizeEdit) {
        //console.log('is authorize 1446')
        this.is_authorize = true
        return true
      }
      if (this.formData.payment_gateways) {
        //console.log('is authorize 1451')
        if (this.formData.payment_gateways.name === 'Authorize') {
          // console.log('is authorize 1453')
          if (this.customer) {
            let params = {
              id: this.customer.id,
            }
            this.loadCustomerData(params)
            // console.log('si carga authorize')
          }
          this.is_authorize = true
          this.is_paypal = false
          this.is_auxVault = false
          return true
        } else if (this.formData.payment_gateways.name === 'Paypal') {
          // console.log('is authorize 1466')
          if (this.customer) {
            let params = {
              id: this.customer.id,
            }
            this.loadCustomerData(params)
          }
          this.is_paypal = true
          this.is_authorize = false
          this.is_auxVault = false
          // console.log('yes paypal')
          return true
        } else if (this.formData.payment_gateways.name === 'AuxVault') {
          //console.log('is authorize 1479')
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
          //  console.log('is authorize 1492')
          this.is_auxVault = false
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
    invoice_list: {
      handler(newVal, oldVal) {
        if (this.isUserAction) {
          // Comparar la longitud para determinar si se agregó o se eliminó un elemento
          if (newVal.length > oldVal.length) {
            // Se agregó un elemento
            const addedItem = newVal.find((item) => !oldVal.includes(item))
            //console.log('Se agregó:', addedItem)
            // Agregar el elemento a formData.invoice_list y authorize.invoice_list
            this.$set(this.formData, 'invoice_list', newVal.slice())
            this.$set(this.authorize, 'invoice_list', newVal.slice())
            // Llamar al método updateProperties después de que se haya completado el proceso de actualización

            this.$nextTick(() => {
              this.updateProperties()
            })
          } else if (newVal.length < oldVal.length) {
            // Se eliminó un elemento
            const removedItem = oldVal.find((item) => !newVal.includes(item))
            // console.log('Se removió:', removedItem)
            // Remover el elemento de formData.invoice_list y authorize.invoice_list
            const index = this.formData.invoice_list.indexOf(removedItem)
            if (index !== -1) {
              this.formData.invoice_list.splice(index, 1)
              this.authorize.invoice_list.splice(index, 1)
            }
            // Llamar al método updateProperties después de que se haya completado el proceso de actualización

            this.$nextTick(() => {
              this.updateProperties()
            })
          }
          // console.log(this.formData)
          // console.log(this.authorize)
        }
      },
      deep: true, // Este watcher verificará la profundidad de los cambios en invoice_list
    },
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
    async customer(newValue) {
      //console.log('Método customer iniciado con newValue:', newValue)
      this.isLoadingPayments = true
      // console.log('isLoadingPayments establecido en true')
      this.formData.user_id = newValue.id
      //console.log('formData.user_id establecido en:', newValue.id)
      this.formData.customcode = newValue.customcode
      //console.log('formData.customcode establecido en:', newValue.customcode)
      this.creditv = false
      //console.log('creditv establecido en false')

      if (!this.isEdit) {
        //console.log('isEdit es false, procediendo con la configuración inicial')
        if (this.isSettingInitialData) {
          //console.log('isSettingInitialData es true, se establecerá en false')
          this.isSettingInitialData = false
        } else {
          this.invoice = null
          this.formData.invoice_id = null
        }
        this.formData.amount = 0
        //console.log('formData.amount reseteado a 0')
        this.invoiceList = []
        //console.log('invoiceList reseteado a un array vacío')
        this.invoice_list = []
        //console.log('invoice_list reseteado a un array vacío')
        //console.log('Llamando a fetchCustomerInvoices con id:', newValue.id)
        await this.fetchCustomerInvoices(newValue.id)
        this.accountList = []
        //console.log('accountList reseteado a un array vacío')
        //console.log('Llamando a fetchCustomerAccounts con id:', newValue.id)
        this.fetchCustomerAccounts(newValue.id)
        this.formData.invoice_list = []
        //console.log('formData.invoice_list reseteado a un array vacío')
        this.authorize.invoice_list = []
        //console.log('authorize.invoice_list reseteado a un array vacío')
        this.creditv = false
        //console.log('creditv reestablecido en false')
        //console.log('Llamando a selectFirstInvoices')
        this.selectFirstInvoices()
      } else {
        // console.log('isEdit es true, no se realiza la configuración inicial')
      }
      // console.log('Método customer finalizado')
    },
    selectedNote() {
      if (this.selectedNote) {
        this.formData.notes = this.selectedNote
      }
    },
  },

  created() {
    ;(this.isShowIdentificationVerification = false),
      (this.isIdentificationVerification = false),
      (this.verificationSuccessful = false),
      this.fetchInitData()
    window.hub.$on('newPaymentMode', (val) => {
      this.formData.payment_method = val
      this.PaymentModeSelected(val)
    })
  },

  async mounted() {
    this.$v.formData.$reset()
    this.resetSelectedNote()
    this.$nextTick(() => {
      this.loadData()
      if (this.$route.params.id && !this.isEdit) {
        this.setInvoicePaymentData()
      }

      this.isUserAction = true
    })
  },
  methods: {
    ...mapActions('invoice', [
      'fetchInvoice',
      'fetchInvoices',
      'fetchInvoicespayments',
    ]),

    ...mapActions('paymentAccounts', [
      'fetchPaymentAccounts',
      'fetchPaymentAccount',
    ]),

    ...mapActions('payment', [
      'processPayment',
      'addPayment',
      'updatePayment',
      'fetchPayment',
      'fetchPaymentModes',
      'resetSelectedNote',
      'loadPaymentMethodsWithExistingSettings',
      'processPaymentVoid',
      'processPaymentRefund',
      'paymentAsociated',
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

    backToForm() {
      this.isShowIdentificationVerification = false
      this.verificationSuccessful = false
    },

    /**
     * Carga el listado inicial de facturas múltiples.
     * Este método asincrónico selecciona la primera factura basada en el ID de la ruta si no está en modo edición.
     * Luego, verifica si el objeto de la factura es válido y, si no está ya en la lista de facturas, lo agrega.
     */
    async selectFirstInvoices() {
      // Inicio del método selectFirstInvoices
      //console.log('selectFirstInvoices: Iniciando método.')

      if (this.$route.params.id && !this.isEdit) {
        //console.log(this.$route.params.id)
        let data = await this.fetchInvoice(this.$route.params.id)
        //console.log(data.data)
        this.invoiceobjet = data.data.invoice
        //console.log(this.invoiceobjet)

        // Verificar si invoiceObject no es null y es un objeto
        if (
          this.invoiceobjet !== null &&
          this.invoiceobjet !== 'undefined' &&
          typeof this.invoiceobjet === 'object'
        ) {
          //console.log('selectFirstInvoices: invoiceObject no es null.')

          // Verificar si invoiceObject no está ya en invoiceList
          const invoiceExists = this.invoiceList.some(
            (invoice) => invoice.id === this.invoiceobjet.id
          )

          // Si invoiceObject no está en invoiceList, agregarlo
          if (!invoiceExists) {
            this.invoiceList.push(this.invoiceobjet)
          }

          // Agregar invoiceObject a invoice_list
          this.invoice_list.push(this.invoiceobjet)

          // Ejecutar las siguientes líneas después de agregar con éxito
          this.formData.invoice_list = this.invoice_list
          this.authorize.invoice_list = this.invoice_list

          // Llamando a updateProperties para actualizar las propiedades relacionadas
          //console.log('selectFirstInvoices: Llamando a updateProperties.')
          this.updateProperties()
        } else {
          // Manejo de casos donde invoiceObject no es válido
          if (this.invoiceobjet === null) {
            // console.log('this.invoiceObject es null')
          }
          if (typeof this.invoiceobjet === 'undefined') {
            // console.log('this.invoiceObject es undefined')
          }
          if (typeof this.invoiceobjet !== 'object') {
            // console.log('this.invoiceObject no es un objeto')
          }
        }
      }
      // Fin del método selectFirstInvoices
      //console.log('selectFirstInvoices: Método finalizado.')
    },

    /**
     * Actualiza las propiedades de la instancia basándose en el saldo del cliente y el estado de edición.
     * Si el cliente tiene un saldo pendiente y no está en modo edición, se activa el crédito y se limpian los métodos de pago.
     * En caso contrario, se desactiva el crédito y se establece el monto máximo pagable a un valor seguro.
     * Además, si no está en modo edición, se ejecuta lógica adicional para establecer el monto de pago.
     */
    updateProperties() {
      // Comienza la verificación del saldo del cliente y el estado de edición
      if (this.customer.balance >= 1 && !this.isEdit) {
        // Activa el crédito y limpia las propiedades del método de pago si hay saldo pendiente
        this.creditv = true
        this.formData.payment_method = {}
        this.type_ach = false
        this.type_cc = false
        this.formData.payment_gateways = []
      } else {
        // Desactiva el crédito y establece el monto máximo pagable si no hay saldo o está en modo edición
        this.creditv = false
        this.maxPayableAmount = Number.MAX_SAFE_INTEGER
      }
      // Ejecuta lógica adicional para establecer el monto de pago si no está en modo edición
      if (!this.isEdit) {
        this.setPaymentAmountByInvoiceData()
      }
      // Finaliza la actualización de propiedades
    },

    /**
     * Procesa el pago utilizando el saldo del cliente.
     * Este método asincrónico valida primero la lista de facturas y los campos del formulario.
     * Si las validaciones son exitosas, procede a realizar el pago con el saldo del cliente.
     */
    async paymentWithCustomerBalance() {
      // Inicio del método paymentWithCustomerBalance
      // console.log('Iniciando método paymentWithCustomerBalance')

      // Validar si invoice_list está vacío
      if (!this.invoice_list || this.invoice_list.length === 0) {
        window.toastr['error'](this.$t('general.invoice_list_empty'))
        // console.log('invoice_list está vacío o no definido')
        return false
      }

      // Validar campos usando $touch
      //console.log('Validando campos personalizados')
      await this.touchCustomField()
      // console.log('Validando campo customer')
      this.$v.customer.$touch()
      //console.log('Validando campo formData')
      this.$v.formData.$touch()

      // Validar que el monto no sea cero
      if (this.formData.amount == 0) {
        window.toastr['error'](this.$t('general.invalid_form_amount'))
        // console.log('El monto es cero, no se puede proceder')
        return false
      }

      // console.log('El monto es mayor que cero, continuando con la validación')

      // Validar si hay campos inválidos
      if (this.$v.$invalid) {
        return false
      }

      // console.log('Todos los campos son válidos, continuando con el proceso')

      // Establecer isLoading a true para indicar que el proceso está en curso
      this.isLoading = true

      // Mostrar confirmación si el formulario no está desactivado
      if (!this.isFormDisabled) {
        //console.log('Formulario activo, mostrando mensaje de confirmación')
        swal({
          title: this.$t('general.are_you_sure_customer_credit'),
          icon: 'warning',
          buttons: true,
        }).then(async (result) => {
          if (result) {
            // Si el usuario confirma, preformatar el pago con el saldo del cliente
            this.preFormatPaymentWithCustomerBalance()
          } else {
            // Si el usuario cancela, detener el proceso y establecer isLoading en false
            this.isLoading = false
          }
        })
      } else {
        // Si el formulario está desactivado, llamar directamente a preFormatPaymentWithCustomerBalance
        this.preFormatPaymentWithCustomerBalance()
      }
      // Fin del método paymentWithCustomerBalance
    },

    /**
     * Incrementa un número representado como una cadena de texto.
     * Si el número incrementado excede el límite de 999999, se expande a 7 dígitos.
     * @param {string} numberString - El número como cadena de texto que se incrementará.
     * @return {string} El número incrementado como cadena de texto, manteniendo el formato original.
     */
    incrementNumberString(numberString) {
      // Convertir el número de cadena a un entero
      let number = parseInt(numberString, 10)

      // Incrementar el número en 1
      number++

      // Verificar si el número alcanzó el límite de 999999
      if (number > 999999) {
        // Expandir el número a 7 dígitos
        return number.toString().padStart(7, '0')
      }

      // Convertir el número de nuevo a una cadena con el mismo formato (rellenando con ceros a la izquierda)
      let incrementedString = number
        .toString()
        .padStart(numberString.length, '0')

      return incrementedString
    },

    async preFormatPaymentWithCustomerBalance() {
      try {
        // 1) Validar que this.customer exista y su balance sea mayor a cero
        if (!this.customer || this.customer.balance <= 0) {
          window.toastr['error'](this.$t('general.invalid_customer_balance'))
          //console.error('Customer does not exist or has zero balance')
          return false
        }

        // Variable para almacenar los invoice_numbers procesados
        let processedInvoices = []

        // Variable para almacenar el balance restante del cliente
        let remainingBalance = this.customer.balance
        //console.log('Balance inicial ' + remainingBalance)
        // 2) Recorrer invoice_list
        for (let i = 0; i < this.invoice_list.length; i++) {
          //console.log(this.invoice_list[i])

          // 3) Establecer el valor de payment_number
          this.formData.payment_number = `${this.paymentPrefix}-${this.paymentNumAttribute}`

          // 4) Establecer el valor de amount y invoice_id desde invoice_list
          this.formData.amount = this.invoice_list[i].due_amount
          this.formData.invoice_id = this.invoice_list[i].id

          // 5) Establecer el valor de transaction_status
          this.formData.transaction_status = this.formData.status.value

          // 6) Si el cliente tiene saldo suficiente, ajustar el amount
          if (this.creditv && this.formData.amount / 100 > remainingBalance) {
            this.formData.amount = remainingBalance * 100
          }

          // 7) Establecer customer_credit como true
          this.formData.customer_credit = true

          // Preparar los datos para enviar
          let data = {
            ...this.formData,
            payment_method_id: null,
            payment_date: moment(this.formData.payment_date).format(
              'YYYY-MM-DD'
            ),
          }
          // console.log('Monto' + this.formData.amount)

          // 8) Realizar la operación de pago
          let successResponse = false // Variable para rastrear si la operación de pago fue exitosa
          let response
          do {
            response = await this.addPayment(data)
            successResponse = response.data.success // Verificar si la operación de pago fue exitosa

            // Si la operación de pago falla debido a un error relacionado con payment_number, ajustar paymentNumAttribute y reintentar
            if (
              !successResponse &&
              response.data.errors.hasOwnProperty('payment_number') &&
              response.data.errors.payment_number[0] ===
                'Invalid number passed.'
            ) {
              // Ajustar paymentNumAttribute
              this.paymentNumAttribute = this.incrementNumberString(
                this.paymentNumAttribute
              )

              // Actualizar payment_number con el nuevo paymentNumAttribute
              this.formData.payment_number = `${this.paymentPrefix}-${this.paymentNumAttribute}`

              // Reintentar la operación de pago con el nuevo payment_number
              //console.log('Retrying payment with adjusted payment number...')
              response = await this.addPayment(data)
            }
          } while (!successResponse) // Repetir el bucle hasta que la operación de pago sea exitosa

          // 9) Manejar la respuesta
          if (successResponse) {
            //console.log('Payment successful')
            // Si hay un pago exitoso, agregar el invoice_number a la lista de procesados
            processedInvoices.push(this.invoice_list[i].invoice_number)

            // Descuentos del balance del cliente
            remainingBalance -= this.formData.amount / 100
            // console.log('Balance final: ' + remainingBalance)
            this.paymentNumAttribute = this.incrementNumberString(
              this.paymentNumAttribute
            )
            // Si se alcanza el último elemento de invoice_list, redirigir al usuario
            if (i === this.invoice_list.length - 1 || remainingBalance <= 0) {
              // 10) Redirigir al usuario según el tipo de factura
              if (this.invoice_list[i] != null) {
                if (this.invoice_list[i].is_invoice_pos == '1') {
                  this.$router.push(
                    `/admin/invoices/${this.invoice_list[i].id}/view`
                  )
                } else {
                  this.$router.push(
                    `/admin/payments/${response.data.payment.id}/view`
                  )
                }
              } else {
                this.$router.push(
                  `/admin/payments/${response.data.payment.id}/view`
                )
              }
              // 14) Mostrar mensaje de éxito con los invoice_numbers procesados
              let successMessage = `${this.$t(
                'payments.created_message'
              )}. ${this.$t(
                'general.processed_invoices'
              )}: ${processedInvoices.join(', ')}`
              window.toastr['success'](successMessage)
              return true
            }
          } else {
            // 11) Manejar errores de amount inválido
            if (response.data.error === 'invalid_amount') {
              window.toastr['error'](this.$t('invalid_amount_message'))
              console.error('Invalid amount error')
              return false
            }

            // 12) Manejar errores generales
            window.toastr['error'](response.data.error)
            console.error('General error:', response.data.error)
            this.isLoading = false
            // Redirigir al usuario a la ruta de pagos
            this.$router.push('/admin/payments')
          }
        }
      } catch (error) {
        // Manejar cualquier error inesperado
        console.error('Unexpected error:', error)
        window.toastr['error'](this.$t('general.unknown_error'))
        this.isLoading = false
        this.$router.push('/admin/payments')
      }
    },

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

    setpaymentModes(id) {
      this.paymentModesWithSettings = this.paymentModesWithSettings.find(
        (c) => {
          return c.id == id
        }
      )
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
     * Carga y asigna los datos del cliente al estado local.
     * Este método asincrónico recupera los datos del cliente basándose en los parámetros proporcionados
     * y actualiza el estado local con la información relevante del cliente, incluyendo la dirección de facturación.
     * @param {Object} params - Parámetros utilizados para recuperar los datos del cliente.
     */
    async loadCustomerData(params) {
      // Inicio del método loadCustomerData
      //console.log(params)

      // Recuperar los datos del cliente usando los parámetros proporcionados
      let response = await this.fetchCustomer(params)

      // Inicializar las propiedades del objeto 'authorize' a null
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

      // Asignar los datos básicos del cliente al objeto 'authorize'
      this.authorize.first_name = response.data.customer.first_name
      this.authorize.last_name = response.data.customer.last_name
      this.authorize.company_name = response.data.customer.company.name
      this.authorize.email = response.data.customer.email

      // Si existe una dirección de facturación, asignar sus valores a las propiedades correspondientes
      if (response.data.customer.billing_address) {
        // Asignar los datos de la dirección de facturación al objeto 'authorize'
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

        // Si hay un país o estado en la dirección de facturación, actualizar los estados 'billing_country' y 'billing_state'
        if (response.data.customer.billing_address.country_id) {
          this.billing_country = response.data.customer.billing_address.country
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

    async loadData() {
      if (this.isEdit) {
        this.isRequestOnGoing = true

        let response = await this.fetchPayment(this.$route.params.id)

        // Payment Authorize
        this.isPaymentTypeAuthorize =
          response.data.payment.authorize_id != null ? true : false
        this.isPaymentTypeAuxVault =
          response.data.payment.aux_vault_id != null ? true : false
        this.formData = { ...this.formData, ...response.data.payment }

        if (this.formData.payment_method_id) {
          this.fetchingPaymentMethod = true
          let payments = await this.fetchPaymentModes({ limit: 'all' })

          this.PaymentModeSelected(this.formData.payment_method)
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
        this.formData.payment_method_id =
          response.data.payment.payment_method_id

        // console.log(response.data.payment.invoice);
        if (response.data.payment.invoice !== null) {
          //console.log("entro aaaaa invoice")
          this.maxPayableAmount =
            parseInt(response.data.payment.amount) +
            parseInt(response.data.payment.invoice.due_amount)
          this.invoice = response.data.payment.invoice
          this.invoice_list.push(response.data.payment.invoice)
          // console.log(this.invoice_list)
          this.formData.invoice_id = response.data.payment.invoice.id
          this.formData.invoice_list = this.invoice_list
          this.authorize.invoice_list = this.invoice_list
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

        // Desabilitar el select de status (solo permite cambiar status del payment, siempre y cuando sea alguno de estos status)
        if (
          this.formData.transaction_status != 'Approved' &&
          this.formData.transaction_status != 'Pending'
        ) {
          this.notEditable = true
          this.isEditAbleFalse = true
        }

        let res = await this.fetchCustomFields({
          type: 'Payment',
          limit: 'all',
        })

        this.setEditCustomFields(
          response.data.payment.fields,
          res.data.customFields.data
        )
        this.formData.payment_gateways = {
          name: response.data.payment_gateway_name,
        }

        let data = {
          payment_id: this.formData.id,
        }

        if (
          this.formData.payment_gateways &&
          this.formData.payment_gateways.name &&
          this.formData.payment_gateways.name !== 'Balance'
        ) {
          // El objeto payment_gateways existe, la propiedad name también existe
          // y el valor de name es diferente de 'Balance'
          // Aquí puedes ejecutar tu lógica si la validación es exitosa

          let responseobjet = await this.paymentAsociated(data)

          // Suponiendo que responseobjet es tu objeto de respuesta y ya está definido
          if (
            responseobjet &&
            responseobjet.data &&
            responseobjet.data.data &&
            responseobjet.data.data.length > 0
          ) {
            // Si responseobjet.data.data existe y tiene elementos
            this.payment_list_associated = [
              ...this.payment_list_associated,
              ...responseobjet.data.data,
            ]
            // console.log(this.payment_list_associated)
          } else {
            // Si responseobjet.data.data no existe, está vacío o es nulo
            //  console.log('No hay datos para agregar')
          }
        }

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
    async setPaymentAmountByInvoiceData() {
      // console.log('Inicio del método setPaymentAmountByInvoiceData')

      // Calculamos la suma de los due_amount del array invoice_list
      let totalDueAmount =
        this.invoice_list?.reduce((total, invoice) => {
          return total + invoice.due_amount
        }, 0) || 0

      // Verificamos si invoice_list está vacío, es null o si totalDueAmount es 0
      if (
        !this.invoice_list ||
        this.invoice_list.length === 0 ||
        totalDueAmount === 0
      ) {
        this.maxPayableAmount = Number.MAX_SAFE_INTEGER
        this.maxAmountIsNotCustomerCreditBalance = Number.MAX_SAFE_INTEGER
      } else {
        // Resto del código existente...
        if (
          this.creditv &&
          totalDueAmount / 100 > this.customer.balance &&
          this.customer.balance >= 1
        ) {
          // Actualizamos formData.amount y maxPayableAmount con el saldo del cliente
          this.formData.amount = this.customer.balance * 100
          this.maxPayableAmount = this.customer.balance * 100
        } else if (1 > this.customer.balance) {
          // Verificar si el saldo del cliente es menor que 1
          this.formData.amount = totalDueAmount
          this.maxPayableAmount = totalDueAmount
        } else {
          // Usamos la suma total de due_amount como formData.amount y maxPayableAmount
          this.formData.amount = totalDueAmount
          this.maxPayableAmount = totalDueAmount
        }
      }

      // Establecemos maxAmountIsNotCustomerCreditBalance
      this.maxAmountIsNotCustomerCreditBalance = totalDueAmount

      // console.log('Fin del método setPaymentAmountByInvoiceData')
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
      let response = await this.fetchInvoicespayments(data)

      response.data.invoices.data.forEach((element) => {
        this.invoiceList.push(element)
      })
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

    /////////////Seleccionar payment mode inicio

    async PaymentModeSelected(val) {
      try {
        this.verificationSuccessful = false
        this.isIdentificationVerification = false
        this.isShowIdentificationVerification = false

        // this.creditv = false

        // Inicializar totalDueAmount
        let totalDueAmount = 0

        // Verificar si invoice_list no es null y tiene registros
        if (this.invoice_list && this.invoice_list.length > 0) {
          // Recorrer cada registro y sumar los valores de due_amount
          totalDueAmount = this.invoice_list.reduce((total, invoice) => {
            return total + invoice.due_amount
          }, 0)

          // console.log('Sumatoria de due_amount completada:', totalDueAmount)

          // Asignar el valor de totalDueAmount a las variables formData.amount y maxPayableAmount
          this.formData.amount = totalDueAmount
          this.maxPayableAmount = totalDueAmount
        }
        // Manejar el modo de pago seleccionado
        if (val.account_accepted === 'A') {
          //console.log('Modo de pago ACH seleccionado')
          this.handleACHMode(val)
        } else if (val.account_accepted === 'C') {
          //console.log('Modo de pago con tarjeta de crédito seleccionado')
          this.handleCreditCardMode(val)
        } else {
          // console.log('Otro modo de pago seleccionado')
          this.handleOtherMode()
        }

        let band = false

        // Manejar pasarela de pago si está presente
        if (val.add_payment_gateway) {
          //console.log('Pasarela de pago añadida, manejando pasarela de pago')
          band = await this.handlePaymentGateways(val)
        } else {
          this.handleNoPaymentGateway()
        }

        // Restablecer campos del formulario
        //console.log('Restableciendo campos del formulario')

        this.resetFormFields()

        // console.log('Finalizando método PaymentModeSelected con band:', band)
        return band
      } catch (error) {}
    },

    async handleCreditCardMode(val) {
      this.type_cc = true
      this.type_ach = false
      if (val.add_payment_gateway) {
        await this.handlePaymentGatewayCreditCard(val)
      } else {
        this.handleNoPaymentGateway()
      }
    },

    async handleACHMode(val) {
      this.type_ach = true
      this.type_cc = false
      if (val.add_payment_gateway) {
        await this.handlePaymentGatewayACH(val)
      } else {
        this.handleNoPaymentGateway()
      }
    },

    async handlePaymentGatewayCreditCard(val) {
      let res = await this.fetchPaymentGateways()
      if (res) {
        this.payment_gateways = res.data.payment_gateways
        this.setDefaultPaymentGateway(val, this.payment_gateways)
        this.add_payment_gateway_select = true
      }
    },

    async handlePaymentGatewayACH(val) {
      let res = await this.fetchPaymentGatewaysAch()
      if (res) {
        this.payment_gateways_ach = res.data.payment_gateways
        this.setDefaultPaymentGateway(val, this.payment_gateways_ach)
        this.add_payment_gateway_select = true
      }
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
              // console.log('payment_gateways_ach es válido')
              // console.log(this.formData.payment_gateways_ach)
              this.setPaymentFees(this.formData.payment_gateways_ach, 'A')
            }
          } else {
            this.formData.payment_gateways = element
            this.isIdentificationVerification =
              element.isidentificationverification == 'YES'
                ? this.customer.verified
                  ? false
                  : true
                : false
            if (
              this.formData.payment_gateways &&
              typeof this.formData.payment_gateways === 'object' &&
              !Array.isArray(this.formData.payment_gateways) &&
              Object.keys(this.formData.payment_gateways).length > 0
            ) {
              //   console.log('payment_gateways es válido')
              // console.log(this.formData.payment_gateways)
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
              // console.log('payment_gateways_ach es válido')
              //console.log(this.formData.payment_gateways_ach)
              this.setPaymentFees(this.formData.payment_gateways_ach, 'A')
            }
          } else {
            this.formData.payment_gateways = element
            this.isIdentificationVerification =
              element.isidentificationverification == 'YES'
                ? this.customer.verified
                  ? false
                  : true
                : false
            if (
              this.formData.payment_gateways &&
              typeof this.formData.payment_gateways === 'object' &&
              !Array.isArray(this.formData.payment_gateways) &&
              Object.keys(this.formData.payment_gateways).length > 0
            ) {
              // console.log('payment_gateways es válido')
              // console.log(this.formData.payment_gateways)
              this.setPaymentFees(this.formData.payment_gateways, 'C')
            }
          }
        }
      })
      // Fin del método setDefaultPaymentGateway
    },

    ///// seleccionar payment mode fin
    /**
     * Actualiza el estado de la transacción y los campos del formulario basados en la selección del usuario.
     * Cambia los estados relacionados con la transacción y el formulario dependiendo si la transacción es 'Void', 'Refunded' o 'Unapply'.
     * @param {object} val - Objeto que contiene el texto del estado de la transacción seleccionado.
     */
    async transactionStatusSelected(val) {
      // Inicio del método transactionStatusSelected
      if (val.text === 'Void' || val.text === 'Refunded') {
        // Si la transacción es anulada o reembolsada
        if (this.formData.payment_method != null) {
          if (this.formData.aux_vault_id != null) {
            // Si hay un aux_vault_id, actualiza los estados correspondientes
            this.formData.applied_credit_customer = false
            this.formData.status_with_authorize = false
            this.formData.status_with_aux_vault = true
            this.transactionStatusCheck = true
            this.transactionStatusCheckUnapply = false
          } else if (this.formData.authorize_id != null) {
            // Si hay un authorize_id, actualiza los estados según si hay un gateway de pago
            if (this.formData.payment_method.add_payment_gateway == 0) {
              this.formData.applied_credit_customer = false
              this.formData.status_with_authorize = false
              this.formData.status_with_aux_vault = false
              this.transactionStatusCheck = false
              this.transactionStatusCheckUnapply = false
            } else {
              this.formData.applied_credit_customer = false
              this.formData.status_with_authorize = true
              this.formData.status_with_aux_vault = false
              this.transactionStatusCheck = true
              this.transactionStatusCheckUnapply = false
            }
          }
        } else {
          // Si no hay un método de pago seleccionado, actualiza los estados a valores por defecto
          this.formData.applied_credit_customer = false
          this.formData.status_with_authorize = false
          this.formData.status_with_aux_vault = false
          this.transactionStatusCheck = true
          this.transactionStatusCheckUnapply = false
        }
      } else if (val.text === 'Unapply') {
        // Si la transacción es desaplicada
        this.formData.status_with_authorize = false
        this.transactionStatusCheck = false
        if (this.formData.invoice_id != null) {
          // Si hay un invoice_id, actualiza los estados para reflejar la desaplicación
          this.formData.applied_credit_customer = true
          this.transactionStatusCheckUnapply = true
        } else {
          // Si no hay un invoice_id, actualiza los estados a valores por defecto
          this.formData.applied_credit_customer = false
          this.transactionStatusCheckUnapply = false
        }
      } else {
        // Para cualquier otro estado, restablece todos los estados a valores por defecto
        this.formData.applied_credit_customer = false
        this.formData.status_with_authorize = false
        this.transactionStatusCheckUnapply = false
        this.transactionStatusCheck = false
      }
      // Fin del método transactionStatusSelected
    },

    /////////////////////////// INICIO sumitpaymentdata
    async submitPaymentData() {
      try {
        if (!this.isEdit) {
          if (!this.validateNewPayment()) {
            return false
          }
        }

        // Si el isIdentificationVerification es verdadero, se ejecuta la verificación de identificación
        if (
          this.isIdentificationVerification &&
          !this.isShowIdentificationVerification
        ) {
          this.isShowIdentificationVerification = true
          return false
        }

        if (this.isEdit) {
          if (!(await this.editPayment())) {
            throw new Error('Failed to edit payment.')
          }
        } else {
          if (!(await this.createPayment())) {
            throw new Error('Failed to create payment.')
          }
        }

        return true // Success
      } catch (error) {
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

    //metodo para editar pagos
    async editPayment() {
      this.prepareEditData()

      let response

      if (this.formData.transaction_status !== 'Approved') {
        response = await this.handleNonApprovedTransaction()
      } else {
        response = await this.updatePayment(this.editData)
      }

      if (response.data.success) {
        if (this.formData.transaction_status === 'Void') {
          // Si el estado de la transacción es 'Void', ejecuta el mensaje de éxito correspondiente
          this.handlePaymentSuccess(this.$t('payments.updated_message_void'))
        } else if (this.formData.transaction_status === 'Refunded') {
          // Si el estado de la transacción es 'Refunded', ejecuta el mensaje de éxito correspondiente
          this.handlePaymentSuccess(
            this.$t('payments.updated_message_refunded')
          )
        } else {
          // Para cualquier otro estado de la transacción, ejecuta el mensaje de éxito general
          this.handlePaymentSuccess(this.$t('payments.updated_message'))
        }

        const redirectPath = `/admin/payments/${this.formData.id}/view`
        this.$router.push(redirectPath)
        return true
      } else {
        throw new Error(response.data.description || response.data.error)
      }
    },

    prepareEditData() {
      // Establecer el estado de la transacción y si se ha modificado
      this.formData.transaction_status = this.formData.status.value
      this.formData.isTransactionStatus = this.isTransactionStatus

      // Crear un objeto con los datos editados del pago
      this.editData = {
        editData: {
          // Copiar todos los datos del formulario de pago
          ...this.formData,
          // Obtener el ID del método de pago seleccionado, si existe
          payment_method_id: this.formData.payment_method
            ? this.formData.payment_method.id
            : null,
          // Formatear la fecha del pago
          payment_date: moment(this.formData.payment_date).format('YYYY-MM-DD'),
          // Asignar el ID del pago actual
          payment_id: this.formData.id,
        },
        // Pasar el ID del pago actual
        id: this.$route.params.id,
      }
    },

    async handleNonApprovedTransaction() {
      // Mostrar una ventana de confirmación al usuario
      const willDelete = await swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('payments.transaction_status_message'),
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      })

      // Si el usuario confirma la acción
      if (willDelete) {
        // Mostrar el indicador de carga
        this.isLoading = true

        try {
          let response

          // Verificar si el estado del pago es "Void"
          if (this.formData.transaction_status === 'Void') {
            // Establecer el flag "only_local" según el método de pago

            if (
              ['Authorize', 'AuxVault'].includes(
                this.formData.payment_gateways.name
              )
            ) {
              this.editData.editData.only_local =
                !this.formData[
                  `status_with_${this.formData.payment_gateways.name.toLowerCase()}`
                ]
            }

            // Procesar la anulación del pago y obtener la respuesta
            if (
              this.formData.status_with_authorize == true ||
              this.formData.status_with_aux_vault == true
            ) {
              this.editData.editData.only_local = false
            }
            // console.log(this.editData.editData)
            response = await this.processPaymentVoid(this.editData.editData)
          }
          // Si el estado del pago es "Refunded"
          else if (this.formData.transaction_status === 'Refunded') {
            // Establecer el flag "only_local" y el tipo de reembolso
            if (
              ['Authorize', 'AuxVault'].includes(
                this.formData.payment_gateways.name
              )
            ) {
              this.editData.editData.only_local =
                !this.formData[
                  `status_with_${this.formData.payment_gateways.name.toLowerCase()}`
                ]
            }
            this.editData.editData.type = 'total'

            if (
              this.formData.status_with_authorize == true ||
              this.formData.status_with_aux_vault == true
            ) {
              this.editData.editData.only_local = false
            }

            // Procesar el reembolso del pago y obtener la respuesta
            response = await this.processPaymentRefund(this.editData.editData)
          }
          // Si el estado del pago es "Unapply" o cualquier otro estado
          else {
            // Actualizar el pago y obtener la respuesta
            response = await this.updatePayment(this.editData)
          }

          //  console.log(response)
          // Manejar la respuesta del servidor
          if (response.data.success) {
            this.isLoading = false
            this.$router.push(
              `/admin/payments/${response.data.payment_id}/view`
            )
            //window.toastr['success'](this.$t('payments.updated_message'))

            return response
          } else {
            this.isLoading = false
            //throw new Error(response.data.description || response.data.error)
            return response
          }
        } catch (error) {
          this.isLoading = false
          window.toastr['error'](error.message)
        }
      }
    },

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
     * Inicia el proceso de pago asociado a una factura.
     * Este método muestra una ventana de confirmación y, si el usuario acepta, procede a realizar el pago.
     * @return {Promise} Una promesa que se resuelve si el pago se procesa correctamente o se rechaza si el usuario cancela.
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

        if (this.paymentFeesListCCflag) {
          console.log(this.paymentFeesListCC)
          if (this.paymentFeesListCC.length > 0) {
            this.authorize.fees = this.paymentFeesListCC.map(
              (fee) => fee.id
            )
          }
          console.log(this.authorize.fees)
          this.authorize.has_fees = 1
        }
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
            this.authorize.fees = this.paymentFeesListACH.map(
              (fee) => fee.id
            )
          }
          this.authorize.has_fees= 1
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
          //console.log('Inicio del método pago simple ')
          this.formData.payment_number = this.codePayment
          this.isLoading = true

          //console.log('Datos actuales en formData:', this.formData)

          let originalAmount = this.formData.amount
          //  console.log('Monto original a procesar:', originalAmount)

          if (
            this.formData.invoice_list &&
            this.formData.invoice_list.length > 0
          ) {
            const totalDueAmount = this.formData.invoice_list.reduce(
              (total, invoice) => total + invoice.due_amount,
              0
            )

            if (originalAmount > totalDueAmount) {
              window.toastr['error'](
                'The amount to be processed is higher than the amount of the selected invoices'
              )

              this.isLoading = false
              return
            }
          } else {
            this.formData.invoice_id = null

            let data = {
              ...this.formData,
              invoice_id: null,
              payment_method_id: this.formData.payment_method
                ? this.formData.payment_method.id
                : null,
              payment_date: moment(this.formData.payment_date).format(
                'YYYY-MM-DD'
              ),
            }

            let response = await this.addPayment(data)
            this.isRequestOnGoing = false
            if (response.data.success) {
              lastSuccessfulResponse = response
            }

            if (lastSuccessfulResponse) {
              const redirectPath = `/admin/payments/${lastSuccessfulResponse.data.payment.id}/view`
              this.$router.push(redirectPath)
              window.toastr['success'](this.$t('payments.created_message'))
              this.isLoading = false
              return true
            } else {
              if (
                lastSuccessfulResponse &&
                lastSuccessfulResponse.data.error === 'invalid_amount'
              ) {
                window.toastr['error'](this.$t('invalid_amount_message'))
              } else {
                window.toastr['error']('Error, contact administration')
              }
              this.isLoading = false
              return false
            }
          }

          //   console.log('Datos de formData después de ajustes:', this.formData)

          let lastSuccessfulResponse = null
          let lastSuccessfulInvoice = null

          for (let invoice of this.formData.invoice_list || [{ id: null }]) {
            //  console.log('Procesando factura con ID:', invoice.id)

            if (originalAmount > invoice.due_amount) {
              this.formData.amount = invoice.due_amount
            } else {
              this.formData.amount = originalAmount
            }

            let data = {
              ...this.formData,
              invoice_id: invoice.id,
              payment_method_id: this.formData.payment_method
                ? this.formData.payment_method.id
                : null,
              payment_date: moment(this.formData.payment_date).format(
                'YYYY-MM-DD'
              ),
            }

            //console.log('Datos enviados en la solicitud de pago:', data)

            let response = await this.addPayment(data)
            this.isRequestOnGoing = false

            //  console.log('Respuesta de la solicitud de pago:', response)

            if (response.data.success) {
              lastSuccessfulResponse = response
              lastSuccessfulInvoice = invoice
              originalAmount -= invoice.due_amount

              if (originalAmount <= 0) {
                //console.log('Monto total procesado. Saliendo del bucle.')
                break
              }
            } else {
              break
            }
          }

          if (lastSuccessfulResponse) {
            const redirectPath =
              lastSuccessfulInvoice &&
              lastSuccessfulInvoice.is_invoice_pos == '1'
                ? `/admin/invoices/${lastSuccessfulInvoice.id}/view`
                : `/admin/payments/${lastSuccessfulResponse.data.payment.id}/view`
            this.$router.push(redirectPath)
            window.toastr['success'](this.$t('payments.created_message'))
            // console.log('Final del método pago simple')
            this.isLoading = false
            return true
          } else {
            if (
              lastSuccessfulResponse &&
              lastSuccessfulResponse.data.error === 'invalid_amount'
            ) {
              window.toastr['error'](this.$t('invalid_amount_message'))
            } else {
              window.toastr['error']('Error, contact administration')
            }
            this.$router.push('/admin/payments')
            //  console.log('Final del método pago simple')
            this.isLoading = false
            return false
          }
        } else {
          // console.log('entro payment activo')
          // Si se necesita procesar el pago con ACH
          this.isLoading = true
          // Establecer detalles para el pago con ACH
          this.authorize.customcode = this.formData.customcode
          // Procesar el pago con ACH y obtener la respuesta
          this.authorize.nameOnAccount = this.authorize.name
          this.authorize.invoice_list = this.formData.invoice_list
          this.authorize.amount = this.formData.amount
          this.authorize.admin = 'admin'
          //   console.log('linea 3174:  ' + this.authorize)
          // console.log(this.authorize)
          //console.log(this.formData)

          if (this.authorize.nameOnAccount != null) {
            this.authorize.nameOnAccount =
              this.authorize.nameOnAccount.substring(0, 21)
          }

          // Comprobar si 'invoice_list' existe, no es null y no está vacío
          if (
            this.authorize.invoice_list &&
            Array.isArray(this.authorize.invoice_list) &&
            this.authorize.invoice_list.length > 0
          ) {
            // Inicializar 'invoices' como un array vacío
            this.authorize.invoices = []

            // Recorrer 'invoice_list' y obtener los 'id'
            this.authorize.invoice_list.forEach((invoice) => {
              if (invoice && invoice.id) {
                this.authorize.invoices.push(invoice.id)
              }
            })
          }
          console.log(this.authorize)
          const response = await this.processPayment(this.authorize)
          // Mostrar mensaje de éxito o error según la respuesta del servidor

          //console.log(response)
          if (response.data.success) {
            window.toastr['success'](this.$t('payments.created_message'))
            this.$router.push(
              `/admin/payments/${response.data.payment_id}/view`
            )
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
          //  console.log('Error de validación:', error.response)

          // Si el formato del error es el primero
          if (error.response.data.hasOwnProperty('errors')) {
            for (let key in error.response.data.errors) {
              let message = key + ': ' + error.response.data.errors[key][0]
              window.toastr['error'](message)
            }
          }
          // Si el formato del error es el segundo
          else if (error.response.data.hasOwnProperty('data')) {
            // Comprobar si 'data' es un string
            if (typeof error.response.data.data === 'string') {
              let message = error.response.data.data
              window.toastr['error'](message)
            } else if (Array.isArray(error.response.data.data)) {
              // Si 'data' es un array, iterar sobre él
              for (let key in error.response.data.data) {
                let message = key + ': ' + error.response.data.data[key][0]
                window.toastr['error'](message)
              }
            }
          }
        } else {
          // Manejar otros tipos de errores
          //console.log('Error desconocido:', error)
          return false
        }
        // console.log('error 2971')
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
    // FIN sumitpaymentdata

    /**
     * Cancela el formulario actual y regresa a la vista anterior.
     * Este método muestra una alerta de confirmación antes de cancelar el formulario y volver atrás.
     */
    cancelForm() {
      // Inicio del método cancelForm
      // Mostrar alerta de confirmación al usuario
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.cancel_text'),
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        // Si el usuario confirma, regresar a la vista anterior
        if (result) {
          this.$router.go(-1)
        }
      })
      // Fin del método cancelForm
    },

    // Payment Number Exists
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
        } else {
          this.isFormDisabled = true
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
    //

    onSelectNote(data) {
      this.formData.notes = '' + data.notes
      this.$refs.notePopup.close()
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

      //  console.log('3740 selecitemaccount')
      //console.log(this.formData)
    },

    /**
     * Selecciona una tarjeta de crédito de un listado y asigna sus datos al estado local.
     * Este método asincrónico toma un objeto 'item' que representa una tarjeta de crédito y actualiza
     * el estado local con la información de la tarjeta seleccionada para su uso en procesos de pago.
     * @param {Object} item - Objeto que contiene los detalles de la tarjeta de crédito seleccionada.
     */
    async selectItemCard(item) {
      // Inicio del método selectItemCard
      //console.log('select item card', item)

      // Asignar el número de tarjeta y CVV del objeto 'item' al objeto 'authorize'
      this.authorize.card_number = item.card_number
      //console.log(this.authorize.card_number)
      this.authorize.cvv = item.cvv

      // Asignar el nombre de la tarjeta de crédito al objeto 'formData'
      this.formData.credit_cards = { name: item.credit_card }

      // Si existe una fecha de expiración, asignarla al objeto 'authorize'
      if (item.expiration_date) {
        this.authorize.date = item.expiration_date
        this.authorize.expiration_date = item.expiration_date
      }

      // Asignar el nombre del titular de la tarjeta al objeto 'authorize'
      this.authorize.name = item.first_name

      // Buscar y asignar el país y estado de facturación basándose en los IDs proporcionados
      this.billing_country = this.countries.find((el) => {
        return el.id == item.country_id
      })
      if (this.billing_country) {
        await this.countrySelected(this.billing_country, 'billing')
      }
      this.billing_state = this.billing_states.find((el) => {
        return el.id == item.state_id
      })

      // Asignar la ciudad, dirección y código postal del objeto 'item' al objeto 'authorize'
      this.authorize.city = item.city
      this.authorize.address_street_1 = item.address_1
      this.authorize.address_street_2 = item.address_2
      this.authorize.zip = item.zip
      // Fin del método selectItemCard
      //console.log('date', this.authorize.date)
    },

    /**
     * Maneja el proceso de confirmación de éxito de un pago a través de PayPal.
     * Este método asincrónico procesa la respuesta de un pago exitoso de PayPal, actualiza el estado de carga,
     * y realiza las operaciones necesarias para registrar el pago en el sistema.
     * @param {string} payment_paypal_id - El identificador del pago de PayPal.
     */
    async paypalSuccess(payment_paypal_id) {
      // Inicio del método paypalSuccess
      // console.log('Inicio del método paypalSuccess')

      // Establecer el estado de carga y procesamiento de pago a verdadero
      this.isLoading = true
      this.isRequestOnGoing = true
      this.paymentPaypalProccess = true
      this.formData.payment_number = this.codePayment

      // Obtener el monto original a procesar
      let originalAmount = this.formData.amount
      // console.log('Monto original a procesar:', originalAmount)

      // Validar el monto a procesar contra el total de las facturas seleccionadas
      if (this.formData.invoice_list && this.formData.invoice_list.length > 0) {
        // Calcular el monto total debido de las facturas seleccionadas
        const totalDueAmount = this.formData.invoice_list.reduce(
          (total, invoice) => total + invoice.due_amount,
          0
        )

        // Si el monto a procesar es mayor que el total debido, mostrar error y detener el proceso
        if (originalAmount > totalDueAmount) {
          window.toastr['error'](
            'The amount to be processed is higher than the amount of the selected invoices'
          )
          this.isLoading = false
          return
        }
      } else {
        // Si no hay facturas seleccionadas, procesar el pago sin asociarlo a una factura específica
        let data = {
          ...this.formData,
          invoice_id: null,
          payment_method_id: this.formData.payment_method
            ? this.formData.payment_method.id
            : null,
          payment_date: moment(this.formData.payment_date).format('YYYY-MM-DD'),
        }

        // Realizar la operación de pago y manejar la respuesta
        let response = await this.addPayment(data)
        this.isRequestOnGoing = false
        if (response.data.success) {
          // Si el pago es exitoso, redirigir al usuario a la vista de detalles del pago
          const redirectPath = `/admin/payments/${response.data.payment.id}/view`
          this.$router.push(redirectPath)
          window.toastr['success'](this.$t('payments.created_message'))
          this.isLoading = false
          return true
        } else {
          // Si hay un error, mostrar el mensaje correspondiente y detener el proceso
          this.handlePaymentErrors(response)
          this.isLoading = false
          return false
        }
      }

      // Procesar cada factura de la lista de facturas seleccionadas
      let lastSuccessfulResponse = null
      let lastSuccessfulInvoice = null
      for (let invoice of this.formData.invoice_list || [{ id: null }]) {
        // Ajustar el monto a procesar según el monto debido de la factura actual
        this.formData.amount =
          originalAmount > invoice.due_amount
            ? invoice.due_amount
            : originalAmount

        // Preparar los datos para la operación de pago
        let data = {
          ...this.formData,
          invoice_id: invoice.id,
          payment_method_id: this.formData.payment_method
            ? this.formData.payment_method.id
            : null,
          payment_date: moment(this.formData.payment_date).format('YYYY-MM-DD'),
          payment_paypal_id,
        }

        // Realizar la operación de pago y manejar la respuesta
        let response = await this.addPayment(data)
        this.isRequestOnGoing = false
        if (response.data.success) {
          // Si el pago es exitoso, actualizar el monto original y continuar con la siguiente factura
          lastSuccessfulResponse = response
          lastSuccessfulInvoice = invoice
          originalAmount -= invoice.due_amount
          if (originalAmount <= 0) {
            break
          }
        } else {
          // Si hay un error, detener el proceso de pago
          break
        }
      }

      // Finalizar el proceso de pago
      if (lastSuccessfulResponse) {
        // Si el último pago fue exitoso, redirigir al usuario a la vista de detalles del pago o de la factura
        const redirectPath =
          lastSuccessfulInvoice && lastSuccessfulInvoice.is_invoice_pos == '1'
            ? `/admin/invoices/${lastSuccessfulInvoice.id}/view`
            : `/admin/payments/${lastSuccessfulResponse.data.payment.id}/view`
        this.$router.push(redirectPath)
        window.toastr['success'](this.$t('payments.created_message'))
      } else {
        // Si hubo un error en el último pago, mostrar el mensaje correspondiente y redirigir al usuario
        this.handlePaymentErrors(lastSuccessfulResponse)
        this.$router.push('/admin/payments')
      }

      // console.log('Final del método paypalSuccess')
      this.isLoading = false
    },

    // stripeSuccess
    async stripeSuccess(payment_stripe_id) {
      // Inicio del método stripeSuccess
      console.log('Inicio del método stripeSuccess')

      // Establecer el estado de carga y procesamiento de pago a verdadero
      this.isLoading = true
      this.isRequestOnGoing = true
      this.paymentStripeProccess = true
      this.formData.payment_number = this.codePayment

      // Obtener el monto original a procesar
      let originalAmount = this.formData.amount
      // console.log('Monto original a procesar:', originalAmount)

      // Validar el monto a procesar contra el total de las facturas seleccionadas
      if (this.formData.invoice_list && this.formData.invoice_list.length > 0) {
        // Calcular el monto total debido de las facturas seleccionadas
        const totalDueAmount = this.formData.invoice_list.reduce(
          (total, invoice) => total + invoice.due_amount,
          0
        )

        // Si el monto a procesar es mayor que el total debido, mostrar error y detener el proceso
        if (originalAmount > totalDueAmount) {
          window.toastr['error'](
            'The amount to be processed is higher than the amount of the selected invoices'
          )
          this.isLoading = false
          return
        }
      } else {
        // Si no hay facturas seleccionadas, procesar el pago sin asociarlo a una factura específica
        let data = {
          ...this.formData,
          invoice_id: null,
          payment_method_id: this.formData.payment_method
            ? this.formData.payment_method.id
            : null,
          payment_date: moment(this.formData.payment_date).format('YYYY-MM-DD'),
        }

        // Realizar la operación de pago y manejar la respuesta
        let response = await this.addPayment(data)
        this.isRequestOnGoing = false
        if (response.data.success) {
          // Si el pago es exitoso, redirigir al usuario a la vista de detalles del pago
          const redirectPath = `/admin/payments/${response.data.payment.id}/view`
          this.$router.push(redirectPath)
          window.toastr['success'](this.$t('payments.created_message'))
          this.isLoading = false
          return true
        } else {
          // Si hay un error, mostrar el mensaje correspondiente y detener el proceso
          this.handlePaymentErrors(response)
          this.isLoading = false
          return false
        }
      }

      // Procesar cada factura de la lista de facturas seleccionadas
      let lastSuccessfulResponse = null
      let lastSuccessfulInvoice = null
      for (let invoice of this.formData.invoice_list || [{ id: null }]) {
        // Ajustar el monto a procesar según el monto debido de la factura actual
        this.formData.amount =
          originalAmount > invoice.due_amount
            ? invoice.due_amount
            : originalAmount

        // Preparar los datos para la operación de pago
        let data = {
          ...this.formData,
          invoice_id: invoice.id,
          payment_method_id: this.formData.payment_method
            ? this.formData.payment_method.id
            : null,
          payment_date: moment(this.formData.payment_date).format('YYYY-MM-DD'),
          payment_stripe_id,
        }

        // Realizar la operación de pago y manejar la respuesta
        let response = await this.addPayment(data)
        this.isRequestOnGoing = false
        if (response.data.success) {
          // Si el pago es exitoso, actualizar el monto original y continuar con la siguiente factura
          lastSuccessfulResponse = response
          lastSuccessfulInvoice = invoice
          originalAmount -= invoice.due_amount
          if (originalAmount <= 0) {
            break
          }
        } else {
          // Si hay un error, detener el proceso de pago
          break
        }
      }

      // Finalizar el proceso de pago
      if (lastSuccessfulResponse) {
        // Si el último pago fue exitoso, redirigir al usuario a la vista de detalles del pago o de la factura
        const redirectPath =
          lastSuccessfulInvoice && lastSuccessfulInvoice.is_invoice_pos == '1'
            ? `/admin/invoices/${lastSuccessfulInvoice.id}/view`
            : `/admin/payments/${lastSuccessfulResponse.data.payment.id}/view`
        this.$router.push(redirectPath)
        window.toastr['success'](this.$t('payments.created_message'))
      } else {
        // Si hubo un error en el último pago, mostrar el mensaje correspondiente y redirigir al usuario
        this.handlePaymentErrors(lastSuccessfulResponse)
        this.$router.push('/admin/payments')
      }

      // console.log('Final del método stripeSuccess')
      this.isLoading = false
    },

    async PaymentSelectedFees(val) {
      this.paymentFeesListCCflag = false
      this.paymentFeesListACHflag = false
      this.paymentFeesListCC = []
      this.paymentFeesListACH = []
      //console.log('Set payment fees credit')
      this.setPaymentFees(val, 'C')
    },

    async PaymentSelectedFeesAch(val) {
      this.paymentFeesListCCflag = false
      this.paymentFeesListACHflag = false
      this.paymentFeesListCC = []
      this.paymentFeesListACH = []
      //console.log('Set payment fees ach')
      this.setPaymentFees(val, 'A')
    },

    setPaymentFees(paymentgateway, type) {
      // Log the input parameters
      //  console.log('setPaymentFees called with:', paymentgateway, type)

      // Check if the type is 'C' (Credit Card)
      if (type == 'C') {
        //  console.log('Type is C (Credit Card)')

        // Check if fee charges are enabled for the payment gateway
        if (paymentgateway.IsPaymentFeeActive == 'YES') {
          //  console.log('Fee charges are enabled for Credit Card')

          // Set the payment fees list for Credit Card
          this.paymentFeesListCC = paymentgateway.registrationdatafees
          //console.log('paymentFeesListCC set to:', this.paymentFeesListCC)

          // Set the flag to true
          this.paymentFeesListCCflag = true
          //console.log('paymentFeesListCCflag set to true')
        } else {
          //console.log('Fee charges are not enabled for Credit Card')
        }
      }

      // Check if the type is 'A' (ACH)
      if (type == 'A') {
        // console.log('Type is A (ACH)')

        // Check if fee charges are enabled for the payment gateway
        if (paymentgateway.IsPaymentFeeActive == 'YES') {
          // console.log('Fee charges are enabled for ACH')

          // Set the payment fees list for ACH
          this.paymentFeesListACH = paymentgateway.registrationdatafees
          // console.log('paymentFeesListACH set to:', this.paymentFeesListACH)

          // Set the flag to true
          this.paymentFeesListACHflag = true
          //console.log('paymentFeesListACHflag set to true')
        } else {
          //console.log('Fee charges are not enabled for ACH')
        }
      }
    },
  },
}
</script>
<style scoped>
.w-20 {
  width: 20% !important;
}
</style>
