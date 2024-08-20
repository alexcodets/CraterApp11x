<template>
  <base-page class="customer-create">
    <form v-if="!initLoad" @submit.prevent="submitCustomerData">
      <sw-page-header class="mb-5" :title="pageTitle">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            to="/admin/dashboard"
            :title="$t('general.home')"
          />
          <sw-breadcrumb-item
            to="/admin/customers"
            :title="$tc('customers.customer', 2)"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'customers.edit'"
            to="#"
            :title="$t('customers.edit_customer')"
            active
          />
          <sw-breadcrumb-item
            v-else
            to="#"
            :title="$t('customers.new_customer')"
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
            class="hidden md:relative md:flex"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />

            {{
              isEdit
                ? $t('customers.update_customer')
                : $t('customers.save_customer')
            }}
          </sw-button>
        </template>
      </sw-page-header>

      <sw-card variant="customer-card">
        <!-- Basic Info  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('customers.basic_info') }}
          </h6>

          <div class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6">
            <sw-input-group
              :label="$t('customers.customer_type')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="customer_type_selected"
                :options="types"
                :searchable="true"
                :show-labels="false"
                class="mt-2"
                label="name"
                @select="CutomerTypeSelected"
              >
              </sw-select>
            </sw-input-group>

            <sw-input-group
              :label="$t('tax_groups.status')"
              class="md:col-span-3 mb-4"
              :error="statusCustomerError"
              required
            >
              <sw-select
                v-model="formData.status_customer"
                :invalid="$v.formData.status_customer.$error"
                :error="displayFirstNameError"
                :allow-empty="true"
                :options="status"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('tax_groups.status')"
                label="text"
              />
            </sw-input-group>

            <sw-input-group
              v-if="this.isResidential && !this.isBusiness"
              :label="$t('customers.first_name')"
              class="md:col-span-3"
              required
            >
              <sw-input
                :invalid="$v.formData.first_name.$error"
                v-model="formData.first_name"
                focus
                type="text"
                name="name"
                @input="$v.formData.first_name.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              v-if="this.isResidential && !this.isBusiness"
              :label="$t('customers.last_name')"
              class="md:col-span-3"
              :error="displayLastNameError"
              required
            >
              <sw-input
                :invalid="$v.formData.last_name.$error"
                v-model="formData.last_name"
                focus
                type="text"
                name="name"
                @input="$v.formData.last_name.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              v-if="this.isBusiness && !this.isResidential"
              :label="$t('customers.display_name')"
              class="md:col-span-3"
              :error="nameError"
              required
            >
              <sw-input
                :invalid="$v.formData.name.$error"
                v-model="formData.name"
                focus
                type="text"
                name="name"
                @input="$v.formData.name.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              v-if="this.isBusiness && !this.isResidential"
              :label="$t('customers.primary_contact_name')"
              class="md:col-span-3"
            >
              <sw-input
                v-model.trim="formData.contact_name"
                :label="$t('customers.contact_name')"
                type="text"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('corePbx.packages.type')"
              :error="statusPaymentError"
              class="md:col-span-3"
              required
            >
              <sw-select
                v-model.trim="formData.status_payment"
                :options="status_payment"
                :invalid="$v.formData.status_payment.$error"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :disabled="isServiceON"
                :placeholder="$t('general.select_status')"
                label="text"
                track-by="value"
                @input="$v.formData.status_payment.$touch()"
              />
              <p v-if="isServiceON" class="mt-1 text-danger text-sm">
                {{ $t('customers.customer_type_warning') }}
              </p>
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.email')"
              class="md:col-span-3"
              :error="emailError"
              required
            >
              <sw-input
                :invalid="$v.formData.email.$error"
                v-model.trim="formData.email"
                type="text"
                name="email"
                @input="$v.formData.email.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.phone')"
              class="md:col-span-3"
            >
              <sw-input
                v-model.trim="formData.phone"
                type="tel"
                name="phone"
                pattern="^[0-9]+$"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.primary_currency')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="currency"
                :options="currencies"
                :custom-label="currencyNameWithCode"
                :allow-empty="false"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('customers.select_currency')"
                label="name"
                track-by="id"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.website')"
              :error="urlError"
              class="md:col-span-3"
            >
              <sw-input
                v-model="formData.website"
                :invalid="$v.formData.website.$error"
                type="url"
                @input="$v.formData.website.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$tc('customers.primary_language')"
              :error="languageError"
              class="md:col-span-3"
            >
              <sw-select
                v-model="formData.language"
                :options="getLanguages"
                :class="{ error: $v.formData.language.$error }"
                :searchable="true"
                :show-labels="false"
                :allow-empty="false"
                :placeholder="$tc('settings.preferences.select_language')"
                class="mt-2"
                label="name"
                track-by="code"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('settings.customization.modules.timezone')"
              class="md:col-span-3 mb-4"
              required
            >
              <div class="flex flex-wrap">
                <div class="mb-2 xl:mb-0 w-full xl:w-1/2">
                  <sw-select
                    v-model="continent"
                    :options="continentOptions"
                    :searchable="true"
                    :show-labels="false"
                    :allow-empty="true"
                    :placeholder="
                      $t(
                        'settings.customization.modules.place_select_continent'
                      )
                    "
                    class="xl:mr-2"
                  />
                </div>

                <div class="mt-2 xl:mt-0 w-full xl:w-1/2">
                  <sw-select
                    v-model="timezone"
                    :options="timezonesOptiones"
                    :searchable="true"
                    :show-labels="false"
                    :allow-empty="true"
                    :placeholder="
                      $t('settings.customization.modules.please_select_zone')
                    "
                    label="label"
                    class="xl:ml-2"
                    :invalid="$v.formData.timezone.$error"
                    @input="$v.formData.timezone.$touch()"
                  />
                </div>
              </div>
            </sw-input-group>
            
            <br>
            <!-- AVALARA SWITCH -->
            <div class="flex md:col-span-3 my-8 mb-4" v-if="avalara_module_backend">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.avalara_bool"
                  class="absolute"
                  style="top: -20px"
                  @change="slideChange"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('customers.avalara_field') }}
                </p>
              </div>
            </div>

            <!-- PREPAID OPTION SWITCH -->
            <div class="flex md:col-span-3 my-8 mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="prepaid_option_status"
                  class="absolute"
                  style="top: -20px"
                  @change="prepaidOptionChange"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('customers.prepaid_options') }}
                </p>
              </div>
            </div>

            <div class="flex md:col-span-3 mt-8 mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.auto_suspension"
                  class="absolute"
                  style="top: -20px"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('customers.auto_suspension') }}
                </p>
              </div>
            </div>

            <!-- AUTHENTICATION SWITCH -->
            <div class="flex md:col-span-3 my-8 mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="authentication_status"
                  class="absolute"
                  style="top: -20px"
                  @change="authenticationChange"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('customers.authentication') }}
                </p>
              </div>
            </div>

            <!-- Activate shipping address     @change="authenticationAddShippingAddres"-->
            <div class="flex md:col-span-3 my-8 mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="add_shipping_addre"
                  class="absolute"
                  style="top: -20px"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('customers.activate_shipping_address') }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <sw-divider v-if="isAddAvalara" class="mb-5 md:mb-8" />

        <!-- Avalara Options  -->
        <div v-if="isAddAvalara" class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('customers.avalara_options') }}
          </h6>

          <div class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6">
            <!-- <div class="flex md:col-span-3 my-8 mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="auto_debit_status"
                  class="absolute"
                  style="top: -20px"
                  @change="autoDebitChange"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('customers.auto_debit') }}
                </p>
              </div>
            </div> -->

            <sw-input-group
              v-if="isAddAvalara"
              :label="$t('customers.avalara_type')"
              class="md:col-span-3"
              :error="avalaraTypeSelectedError"
            >
              <sw-select
                v-model="formData.avalara_type_selected"
                :options="avalara_types"
                :searchable="true"
                :show-labels="false"
                :invalid="$v.formData.avalara_type_selected.$error"
                @input="$v.formData.avalara_type_selected.$touch()"
                class="mt-2"
                label="name"
              >
              </sw-select>
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.sale_type')"
              :error="statusPaymentError"
              class="md:col-span-3"
              
            >
              <sw-select
                v-model.trim="formData.sale_type"
                :options="sale_type"
                :invalid="$v.formData.sale_type.$error"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$t('general.select_status')"
                label="text"
                track-by="value"
                @input="$v.formData.sale_type.$touch()"
              />
            </sw-input-group>
          </div>
        </div>


        <sw-divider v-if="isPrepaidOption" class="mb-5 md:mb-8" />

        <!-- Prepaid Options  -->
        <div v-if="isPrepaidOption" class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('customers.prepaid_options') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <div class="flex md:col-span-3 my-8 mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="auto_debit_status"
                  class="absolute"
                  style="top: -20px"
                  @change="autoDebitChange"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('customers.auto_debit') }}
                </p>
              </div>
            </div>

            <sw-input-group
              v-if="!auto_debit_status"
              :error="emailLowBalanceNotificationError"
              :label="$t('customers.email_low_balance_notification')"
              class="md:col-span-3"
            >
              <sw-input
                v-model="formData.email_low_balance_notification"
                :invalid="$v.formData.email_low_balance_notification.$error"
                type="text"
                name="email_low_balance_notification"
                @input="$v.formData.email_low_balance_notification.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              v-if="auto_debit_status"
              :label="$t('customers.minimun_balance')"
              class="md:col-span-3"
            >
              <sw-input
                v-model="formData.minimun_balance"
                numeric
                name="minimun_balance"
              />
            </sw-input-group>

            <sw-input-group
              v-if="auto_debit_status"
              :error="autoReplenishAmountError"
              :label="$t('customers.auto_replenish_amount')"
              class="md:col-span-3"
            >
              <sw-input
                v-model="formData.auto_replenish_amount"
                :invalid="$v.formData.auto_replenish_amount.$error"
                type="text"
                name="auto_replenish_amount"
                @input="$v.formData.auto_replenish_amount.$touch()"
              />
            </sw-input-group>
          </div>
        </div>
        <!-- AUTHENTICATION  -->

        <sw-divider v-if="isAuthentication" class="mb-5 md:mb-8" />

        <div v-if="isAuthentication" class="flex flex-wrap">
          <h6 class="w-full md:w-1/5">
            {{ $t('customers.authentication') }}
          </h6>

          <div class="w-full md:w-4/5">
            <div class="flex mt-2 w-full">
              <div class="relative w-12">
                <sw-switch
                  v-model="username_status"
                  class="absolute"
                  style="top: -20px"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('customers.specify_username') }}
                </p>

                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500 texto-customer"
                >
                  {{ $t('customers.specify_username_info') }}
                </p>
              </div>
            </div>

            <sw-input-group
              v-if="isUsername"
              :label="$t('customers.username')"
              class="w-full md:w-1/2"
              :error="usernameError"
              required
            >
              <sw-input
                :invalid="$v.formData.customer_username.$error"
                v-model="formData.customer_username"
                focus
                type="text"
                name="name"
                @keydown.space.prevent
                @input="$v.formData.customer_username.$touch()"
              />
              <span
                v-if="userView"
                class="mt-1 text-sm"
                :class="{
                  'text-success': userValid,
                  'text-danger': !userValid,
                }"
              >
                {{ userValidText }}
                <!-- {{ userValid ? $t('customers.username_available_yes') : $t('customers.username_available_no') }} -->
              </span>
            </sw-input-group>

            <sw-divider v-if="isUsername" class="my-0 col-span-12 opacity-0" />

            <div class="w-full flex flex-wrap">
              <sw-input-group
                :label="$t('customers.password')"
                class="w-full md:w-1/2 mt-3"
                :error="passwordError"
                required
              >
                <sw-input
                  :invalid="$v.formData.password.$error"
                  v-model="formData.password"
                  focus
                  :type="showPassword ? 'text' : 'password'"
                  name="name"
                  @keydown.space.prevent
                  @input="$v.formData.password.$touch()"
                >
                  <template v-slot:rightIcon>
                    <eye-off-icon
                      v-if="showPassword"
                      class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="showPassword = !showPassword"
                    />
                    <eye-icon
                      v-else
                      class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="showPassword = !showPassword"
                    />
                  </template>
                </sw-input>
              </sw-input-group>

              <div class="w-full md:w-1/2 flex items-end">
                <sw-button
                  variant="primary-outline"
                  size="lg"
                  @click="generate()"
                  class="md:ml-2"
                >
                  {{ $t('customers.generate_password') }}
                </sw-button>
              </div>
            </div>

            <sw-input-group
              :label="$t('customers.confirm_password')"
              class="w-full md:w-1/2 my-3"
              :error="confirmPasswordError"
              required
            >
              <sw-input
                :invalid="$v.formData.confirm_password.$error"
                v-model="formData.confirm_password"
                focus
                :type="showPassword ? 'text' : 'password'"
                name="name"
                @keydown.space.prevent
                @input="$v.formData.confirm_password.$touch()"
              >
                <template v-slot:rightIcon>
                  <eye-off-icon
                    v-if="showPassword"
                    class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                    @click="showPassword = !showPassword"
                  />
                  <eye-icon
                    v-else
                    class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                    @click="showPassword = !showPassword"
                  />
                </template>
              </sw-input>
            </sw-input-group>

            <sw-divider class="my-0 col-span-12 opacity-0" />

            <sw-input-group
              :label="$t('customers.security_pin')"
              class="w-full md:w-1/2"
            >
              <sw-input
                v-model="formData.security_pin"
                name="security_pin"
                type="text"
              />
            </sw-input-group>

            <div
              class="flex w-full md:w-1/2 my-7 mb-4"
              v-if="isEdit && setPass"
            >
              <sw-button
                :disabled="isLoading"
                :loading="isLoading"
                variant="primary-outline"
                size="lg"
                @click="sendPass()"
              >
                {{ $t('customers.send_password') }}
              </sw-button>
            </div>
          </div>
        </div>

        <sw-divider class="mb-5 md:mb-8" />

        <!-- Billing Address  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('customers.billing_info') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <!-- <sw-input-group :label="$t('customers.name')" class="md:col-span-3">
              <sw-input
                v-model.trim="formData.billing.name"
                type="text"
                name="address_name"
                tabindex="7"
              />
            </sw-input-group> -->

            <sw-input-group
              :error="countryIdError"
              :label="$t('customers.country')"
              class="md:col-span-3"
              required
            >
              <sw-select
                v-model="billing_country"
                :invalid="$v.formData.billing.country_id.$error"
                :options="countries"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('general.select_country')"
                label="name"
                track-by="id"
                @select="countrySelected($event, 'billing')"
              />
            </sw-input-group>

            <sw-input-group
              :error="stateIdError"
              :label="$t('customers.state')"
              class="md:col-span-3"
              required
            >
              <sw-select
                v-model="billing_state"
                :invalid="$v.formData.billing.state_id.$error"
                :options="billing_states"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$t('general.select_state')"
                label="name"
                track-by="id"
              />
            </sw-input-group>

            <div class="md:col-span-3">
              <sw-input-group
                :label="$t('customers.address')"
                :error="billAddress1Error"
                required
              >
                <sw-textarea
                  v-model.trim="formData.billing.address_street_1"
                  :invalid="$v.formData.billing.address_street_1.$error"
                  :placeholder="$t('general.street_1')"
                  type="text"
                  name="billing_street1"
                  rows="1"
                  @input="$v.formData.billing.address_street_1.$touch()"
                />
              </sw-input-group>
            </div>

            <div class="md:col-span-3">
              <sw-input-group
                :label="$t('customers.address')"
                :error="billAddress2Error"
              >
                <sw-textarea
                  v-model.trim="formData.billing.address_street_2"
                  :placeholder="$t('general.street_2')"
                  type="text"
                  name="billing_street2"
                  rows="1"
                  @input="$v.formData.billing.address_street_2.$touch()"
                />
              </sw-input-group>
            </div>

            <sw-input-group
              :label="$t('customers.city')"
              class="md:col-span-3"
              :error="cityError"
              required
            >
              <sw-input
                v-model="formData.billing.city"
                :invalid="$v.formData.billing.city.$error"
                name="formData.billing.city"
                type="text"
                @input="$v.formData.billing.city.$touch()"
              />
            </sw-input-group>

            <div class="md:col-span-3">
              <!-- <sw-input-group
                :label="$t('customers.phone')"
                class="mb-6"
                :error="phoneError"
                required
              >
                <sw-input
                  v-model.trim="formData.billing.phone"
                  :invalid="$v.formData.billing.phone.$error"
                  type="tel"
                  name="phone"
                  pattern="^[0-9]+$"
                  tabindex="13"
                  @input="$v.formData.billing.phone.$touch()"
                />
              </sw-input-group> -->

              <sw-input-group
                :label="$t('customers.zip_code')"
                :error="zipError"
                required
              >
                <sw-input
                  v-model.trim="formData.billing.zip"
                  :invalid="$v.formData.billing.zip.$error"
                  type="text"
                  name="zip"
                  @input="$v.formData.billing.zip.$touch()"
                />
              </sw-input-group>
            </div>

            <div class="flex md:col-span-3 my-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.billing.tax_exempt"
                  class="absolute"
                  style="top: -20px"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('customers.tax_exempt') }}
                </p>
              </div>
            </div>

            <sw-input-group
              :label="$t('customers.tax_id_vatin')"
              class="md:col-span-3"
            >
              <sw-input
                v-model="formData.billing.tax_id_vatin"
                type="text"
                name="tax_id_vatin"
              />
            </sw-input-group>

            <sw-input-group
              :label="$tc('customers.delivery_methods')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="billing_delivery_method"
                :options="delivery_methods"
                :searchable="true"
                :show-labels="false"
                :allow-empty="false"
                :placeholder="$tc('customers.select_method')"
                class="mt-2"
                label="name"
                track-by="id"
              />
            </sw-input-group>

            <div class="flex md:col-span-3 mt-8 mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.billing.payment_notices"
                  class="absolute"
                  style="top: -20px"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('customers.payment_notices') }}
                </p>
              </div>
            </div>

            <div v-if="formData.avalara_bool" class="flex w-full md:w-1/2 my-2">
              <sw-button
                variant="primary-outline"
                size="lg"
                type="button"
                @click="checkBilling"
              >
                <check-icon v-if="!isLoading" class="mr-2 -ml-1" />
                {{ $t('customers.billing_validation') }}
              </sw-button>
            </div>
          </div>
        </div>

        <sw-divider class="mb-5 md:mb-8" />

        <!-- Billing Address Copy Button  -->
        <div
          v-if="add_shipping_addre"
          class="flex items-center justify-start mb-6 md:justify-end md:mb-0"
        >
          <div class="p-1">
            <sw-button
              ref="sameAddress"
              variant="primary"
              type="button"
              class="h-8 px-3 py-1 mb-4"
              @click="copyAddress(true)"
            >
              <document-duplicate-icon class="h-4 mr-1 -ml-2" />
              <span class="text-xs">
                {{ $t('customers.copy_billing_address') }}
              </span>
            </sw-button>
          </div>
        </div>

        <!-- Shipping Address  -->
        <div v-if="add_shipping_addre" class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('customers.shipping_address') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <!-- <sw-input-group :label="$t('customers.name')" class="md:col-span-3">
              <sw-input
                v-model.trim="formData.shipping.name"
                type="text"
                name="address_name"
                tabindex="15"
              />
            </sw-input-group> -->

            <sw-input-group
              :label="$t('customers.country')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="shipping_country"
                :options="countries"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$t('general.select_country')"
                label="name"
                track-by="id"
                @select="countrySelected($event, 'shipping')"
              />
            </sw-input-group>

            <div class="md:col-span-3">
              <sw-input-group :label="$t('customers.address')">
                <sw-textarea
                  v-model.trim="formData.shipping.address_street_1"
                  :placeholder="$t('general.street_1')"
                  type="text"
                  name="street_1"
                  rows="1"
                  @input="$v.formData.shipping.address_street_1.$touch()"
                />
              </sw-input-group>

              <div v-if="$v.formData.shipping.address_street_1.$error">
                <span
                  v-if="!$v.formData.shipping.address_street_1.maxLength"
                  class="text-sm text-danger"
                  >{{ $t('validation.address_maxlength') }}</span
                >
              </div>
            </div>

            <div class="md:col-span-3">
              <sw-input-group
                :label="$t('customers.address')"
                :error="billAddress2Error"
              >
                <sw-textarea
                  v-model.trim="formData.shipping.address_street_2"
                  :placeholder="$t('general.street_2')"
                  type="text"
                  name="street2"
                  rows="1"
                  @input="$v.formData.shipping.address_street_2.$touch()"
                />
              </sw-input-group>

              <div v-if="$v.formData.shipping.address_street_2.$error">
                <span
                  v-if="!$v.formData.shipping.address_street_2.maxLength"
                  class="text-danger"
                  >{{ $t('validation.address_maxlength') }}</span
                >
              </div>
            </div>

            <sw-input-group :label="$t('customers.city')" class="md:col-span-3">
              <sw-input
                v-model="formData.shipping.city"
                name="formData.shipping.city"
                type="text"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.state')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="shipping_state"
                :options="shipping_states"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$t('general.select_state')"
                label="name"
                track-by="id"
              />
            </sw-input-group>

            <div class="md:col-span-3">
              <!-- <sw-input-group :label="$t('customers.phone')" class="mb-6">
                <sw-input
                  v-model.trim="formData.shipping.phone"
                  type="text"
                  name="phone"
                  tabindex="21"
                />
              </sw-input-group> -->

              <sw-input-group :label="$t('customers.zip_code')">
                <sw-input
                  v-model.trim="formData.shipping.zip"
                  type="text"
                  name="zip"
                />
              </sw-input-group>
            </div>
          </div>
        </div>

        <sw-divider v-if="customFields.length > 0" class="mb-5 md:mb-8" />

        <!-- Custom Fields  -->
        <div v-if="customFields.length > 0" class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('settings.custom_fields.title') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <sw-input-group
              class="md:col-span-3"
              v-for="(field, index) in customFields"
              :label="field.label"
              :required="field.is_required ? true : false"
              :key="index"
            >
              <component
                :type="field.type.label"
                :field="field"
                :isEdit="isEdit"
                :is="field.type + 'Field'"
                :invalid-fields="invalidFields"
                @update="setCustomFieldValue"
              />
            </sw-input-group>
          </div>
        </div>

        <!-- Botonazo  -->
        <div class="grid grid-cols-4 gap-4 mb-8">
          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="hidden md:relative md:flex"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />

            {{
              isEdit
                ? $t('customers.update_customer')
                : $t('customers.save_customer')
            }}
          </sw-button>
        </div>

        <!-- Mobile Submit Button  -->
        <sw-button
          :disabled="isLoading"
          :loading="isLoading"
          variant="primary"
          type="submit"
          size="lg"
          class="flex w-full sm:hidden md:hidden"
        >
          <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
          {{
            isEdit
              ? $t('customers.update_customer')
              : $t('customers.save_customer')
          }}
        </sw-button>
      </sw-card>
    </form>
    <base-loader v-else />
  </base-page>
</template>

<script>
import AddressStub from '../../stub/address'
import { mapActions, mapGetters } from 'vuex'
import _ from 'lodash'
import CustomFieldsMixin from '../../mixins/customFields'
import {
  DocumentDuplicateIcon,
  EyeOffIcon,
  EyeIcon,
  CheckIcon,
} from '@vue-hero-icons/solid'

const {
  required,
  numeric,
  minValue,
  minLength,
  email,
  url,
  maxLength,
  sameAs,
} = require('vuelidate/lib/validators')

export default {
  components: {
    DocumentDuplicateIcon,
    EyeOffIcon,
    EyeIcon,
    CheckIcon,
  },
  mixins: [CustomFieldsMixin],
  props: {
    type: {
      type: String,
      default: 'text',
    },
    size: {
      type: String,
      default: '12',
    },
    characters: {
      type: String,
      default: 'a-z,A-Z,0-9,#',
    },
    placeholder: {
      type: String,
      default: 'Password',
    },
    auto: [String, Boolean],
    value: '',
  },
  data() {
    return {
      avalara_company_bscl_selected: null,
      avalara_company_svcl_selected: null,
      avalara_company_fclt_selected: null,
      avalara_company_reg_selected: null,
      avalara_company_bscl_options: [
        {
          value: '0',
          name: 'Incumbent Local Exchange Carrier (ILEC)',
        },
        {
          value: '1',
          name: 'Not an ILEC',
        },
      ],
      avalara_company_svcl_options: [
        {
          value: '0',
          name: 'Primary Local',
        },
        {
          value: '1',
          name: 'Primary Long Distance',
        },
      ],
      avalara_company_fclt_options: [
        {
          value: 1,
          name: 'Seller is facilities-based (cable operators and telephone companies)',
        },
        {
          value: 0,
          name: 'Seller is not facilities-based (Internet based providers)',
        },
      ],
      avalara_company_reg_options: [
        {
          value: 1,
          name: 'Seller is regulated',
        },
        {
          value: 0,
          name: 'Seller is not regulated',
        },
      ],
      avalara_types: [
        { name: 'Residential', value: '0' },
        { name: 'Business', value: '1' },
        { name: 'Senior Citizen', value: '2' },
        { name: 'Industrial', value: '3' },
      ],
      avalara_module_backend: false,
      userView: false,
      userValid: false,
      userValidText: '',
      isCopyFromBilling: false,
      isLoading: false,
      initLoad: false,
      add_avalara: false,
      prepaid_option_status: false,
      authentication_status: false,
      add_shipping_addre: false,
      username_status: false,
      auto_debit_status: true,
      currency: null,
      company_id: null,
      isResidential: false,
      isBusiness: true,
      formData: {
        addresses: [],
        avalara_bool: false,
        avalara_type_selected: {},
        name: null,
        first_name: null,
        last_name: null,
        contact_name: null,
        email: null,
        phone: null,
        currency_id: null,
        website: null,
        customer_type: '',
        prepaid_option: 0,
        auto_debit: '0',
        avalara_type: '',
        bscl: null,
        svcl: null,
        fclt: null,
        reg: null,
        email_low_balance_notification: 0,
        auto_replenish_amount: 0,
        minimun_balance: 0,
        specify_username: false,
        customer_username: null,
        password: null,
        confirm_password: null,
        authentication: false,
        add_shipping_addres: false,
        username_status: false,
        security_pin: null,
        status_payment: null,
        sale_type: null,
        timezone: '',
        billing: {
          name: null,
          country_id: null,
          state_id: null,
          city: null,
          phone: null,
          zip: null,
          address_street_1: null,
          address_street_2: null,
          type: 'billing',
          tax_exempt: false,
          tax_id_vatin: null,
          delivery_method: null,
          payment_notices: false,
        },
        shipping: {
          name: null,
          country_id: null,
          state_id: null,
          city: null,
          phone: null,
          zip: null,
          address_street_1: null,
          address_street_2: null,
          type: 'shipping',
        },
        status_customer: {
          value: 'A',
          text: 'Active',
        },
        language: null,
        auto_suspension: false,
      },
      continentOptions: [],
      timezonesOptiones: [],
      continent: '',
      timezone: '',
      status: [
        {
          value: 'A',
          text: 'Active',
        },
        {
          value: 'I',
          text: 'Inactive',
        },
        {
          value: 'F',
          text: 'Archive',
        },
      ],
      showPassword: false,
      currencyList: [],
      billing_state: null,
      shipping_state: null,
      billing_country: null,
      billing_delivery_method: { name: 'Email', value: 'Email' },
      shipping_country: null,
      countries: [],
      billing_states: [],
      shipping_states: [],
      types: [
        // { name: 'None', value: 'N' },
        { name: 'Business', value: 'B' },
        { name: 'Residential', value: 'R' },
      ],
      customer_type_selected: { name: 'Business', value: 'B' },
      status_payment: [
        { value: 'prepaid', text: 'Prepaid' },
        { value: 'postpaid', text: 'Postpaid' },
      ],
      sale_type: [
        { value: 'Retail', text: 'Retail' },
        { value: 'Wholesale', text: 'Wholesale' },
      ],
      delivery_methods: [
        { id: 1, name: 'Email', value: 'Email' },
        { id: 2, name: 'Paper', value: 'Paper' },
        { id: 3, name: 'InterFax', value: 'InterFax' },
        { id: 4, name: 'PostalMethods', value: 'PostalMethods' },
      ],
      bandGeneratePassword: false,
      bandSendPass: false,
      isServiceON: false,
    }
  },
  validations() {
    /* console.log('auth', this.isAuthentication);
    console.log('username', this.isUsername);
    console.log('add avalara', this.isAddAvalara);
    console.log('bussines', this.isBusiness); */

    if (
      this.isAuthentication &&
      this.isUsername &&
      this.isAddAvalara &&
      this.isBusiness
    ) {
      return {
        formData: {
          status_customer: {
            required,
          },
          name: {
            required,
            minLength: minLength(3),
          },
          status_payment: {
            required,
          },
          sale_type: {

          },
          email: {
            required,
            email,
          },
          website: {
            url,
          },
          email_low_balance_notification: {
            required,
            numeric,
            minValue: minValue(0),
          },
          auto_replenish_amount: {
            required,
            numeric,
            minValue: minValue(0),
          },
          customer_username: {
            required,
            minLength: minLength(5),
          },
          password: {
            required,
            minLength: minLength(8),
          },
          confirm_password: {
            required,
            sameAsPassword: sameAs('password'),
          },
          billing: {
            country_id: {
              required,
            },
            city: {
              required,
            },
            zip: {
              required,
            },
            state_id: {
              required,
            },
            address_street_1: {
              required,
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          shipping: {
            address_street_1: {
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          language: {
            required,
          },
          timezone: {
            required,
          },
        },
      }
    } else if (this.isAuthentication && this.isUsername && this.isAddAvalara) {
      return {
        formData: {
          status_customer: {
            required,
          },
          name: {
            required,
            minLength: minLength(3),
          },
          first_name: {
            required,
            minLength: minLength(3),
          },
          last_name: {
            required,
            minLength: minLength(3),
          },
          status_payment: {
            required,
          },
          avalara_type_selected: { 
            required
          },
          sale_type: {
            required,
          },
          email: {
            required,
            email,
          },
          website: {
            url,
          },
          // bscl: {
          //   required,
          // },
          // svcl: {
          //   required,
          // },
          // fclt: {
          //   required,
          // },
          // reg: {
          //   required,
          // },
          email_low_balance_notification: {
            required,
            numeric,
            minValue: minValue(0),
          },
          auto_replenish_amount: {
            required,
            numeric,
            minValue: minValue(0),
          },
          customer_username: {
            required,
            minLength: minLength(5),
          },
          password: {
            required,
            minLength: minLength(8),
          },
          confirm_password: {
            required,
            sameAsPassword: sameAs('password'),
          },
          billing: {
            country_id: {
              required,
            },
            city: {
              required,
            },
            zip: {
              required,
            },
            state_id: {
              required,
            },
            address_street_1: {
              required,
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          shipping: {
            address_street_1: {
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          language: {
            required,
          },
          timezone: {
            required,
          },
        },
      }
    } else if (this.isAuthentication && this.isAddAvalara && this.isBusiness) {
      return {
        formData: {
          status_customer: {
            required,
          },
          name: {
            required,
            minLength: minLength(3),
          },
          status_payment: {
            required,
          },
          avalara_type_selected: { 
            required
          },
          sale_type: {
            required,
          },
          email: {
            required,
            email,
          },
          website: {
            url,
          },
          email_low_balance_notification: {
            required,
            numeric,
            minValue: minValue(0),
          },
          auto_replenish_amount: {
            required,
            numeric,
            minValue: minValue(0),
          },
          password: {
            required,
            minLength: minLength(8),
          },
          confirm_password: {
            required,
            sameAsPassword: sameAs('password'),
          },
          billing: {
            country_id: {
              required,
            },
            city: {
              required,
            },
            zip: {
              required,
            },
            state_id: {
              required,
            },
            address_street_1: {
              required,
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          shipping: {
            address_street_1: {
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          language: {
            required,
          },
          timezone: {
            required,
          },
        },
      }
    } else if (this.isAuthentication && this.isAddAvalara) {
      return {
        formData: {
          status_customer: {
            required,
          },
          name: {
            required,
            minLength: minLength(3),
          },
          first_name: {
            required,
            minLength: minLength(3),
          },
          last_name: {
            required,
            minLength: minLength(3),
          },
          status_payment: {
            required,
          },
          avalara_type_selected: { 
            required
          },
          sale_type: {
            required,
          },
          email: {
            required,
            email,
          },
          website: {
            url,
          },
          // bscl: {
          //   required,
          // },
          // svcl: {
          //   required,
          // },
          // fclt: {
          //   required,
          // },
          // reg: {
          //   required,
          // },
          email_low_balance_notification: {
            required,
            numeric,
            minValue: minValue(0),
          },
          auto_replenish_amount: {
            required,
            numeric,
            minValue: minValue(0),
          },
          password: {
            required,
            minLength: minLength(8),
          },
          confirm_password: {
            required,
            sameAsPassword: sameAs('password'),
          },
          billing: {
            country_id: {
              required,
            },
            city: {
              required,
            },
            zip: {
              required,
            },
            state_id: {
              required,
            },
            address_street_1: {
              required,
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          shipping: {
            address_street_1: {
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          language: {
            required,
          },
          timezone: {
            required,
          },
        },
      }
    } else if (this.isAddAvalara && this.isBusiness) {
      return {
        formData: {
          status_customer: {
            required,
          },
          name: {
            required,
            minLength: minLength(3),
          },
          status_payment: {
            required,
          },
          avalara_type_selected: { 
            required
          },
          sale_type: {
            required,
          },
          email: {
            required,
            email,
          },
          website: {
            url,
          },
          email_low_balance_notification: {
            required,
            numeric,
            minValue: minValue(0),
          },
          auto_replenish_amount: {
            required,
            numeric,
            minValue: minValue(0),
          },
          billing: {
            country_id: {
              required,
            },
            city: {
              required,
            },
            zip: {
              required,
            },
            state_id: {
              required,
            },
            address_street_1: {
              required,
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          shipping: {
            address_street_1: {
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          language: {
            required,
          },
          timezone: {
            required,
          },
        },
      }
    } else if (this.isAddAvalara) {
      return {
        formData: {
          status_customer: {
            required,
          },
          name: {
            required,
            minLength: minLength(3),
          },
          first_name: {
            required,
            minLength: minLength(3),
          },
          last_name: {
            required,
            minLength: minLength(3),
          },
          status_payment: {
            required,
          },
          avalara_type_selected: { 
            required
          },
          sale_type: {
            required,
          },
          email: {
            required,
            email,
          },
          website: {
            url,
          },
          email_low_balance_notification: {
            required,
            numeric,
            minValue: minValue(0),
          },
          auto_replenish_amount: {
            required,
            numeric,
            minValue: minValue(0),
          },
          billing: {
            country_id: {
              required,
            },
            city: {
              required,
            },
            zip: {
              required,
            },
            state_id: {
              required,
            },
            address_street_1: {
              required,
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          shipping: {
            address_street_1: {
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          language: {
            required,
          },
          timezone: {
            required,
          },
        },
      }
    } else if (this.isAuthentication && this.isUsername && this.isBusiness) {
      return {
        formData: {
          status_customer: {
            required,
          },
          name: {
            required,
            minLength: minLength(3),
          },
          status_payment: {
            required,
          },
          sale_type: {
            
          },
          email: {
            required,
            email,
          },
          website: {
            url,
          },
          email_low_balance_notification: {
            required,
            numeric,
            minValue: minValue(0),
          },
          auto_replenish_amount: {
            required,
            numeric,
            minValue: minValue(0),
          },
          customer_username: {
            required,
            minLength: minLength(5),
          },
          password: {
            required,
            minLength: minLength(8),
          },
          confirm_password: {
            required,
            sameAsPassword: sameAs('password'),
          },
          billing: {
            country_id: {
              required,
            },
            state_id: {
              required,
            },
            city: {
              required,
            },
            zip: {
              required,
            },
            address_street_1: {
              required,
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          shipping: {
            address_street_1: {
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          language: {
            required,
          },
          timezone: {
            required,
          },
        },
      }
    } else if (this.isAuthentication && this.isUsername) {
      return {
        formData: {
          status_customer: {
            required,
          },
          name: {
            required,
            minLength: minLength(3),
          },
          first_name: {
            required,
            minLength: minLength(3),
          },
          last_name: {
            required,
            minLength: minLength(3),
          },
          status_payment: {
            required,
          },
          sale_type: {
            
          },
          email: {
            required,
            email,
          },
          website: {
            url,
          },
          email_low_balance_notification: {
            required,
            numeric,
            minValue: minValue(0),
          },
          auto_replenish_amount: {
            required,
            numeric,
            minValue: minValue(0),
          },
          customer_username: {
            required,
            minLength: minLength(5),
          },
          password: {
            required,
            minLength: minLength(8),
          },
          confirm_password: {
            required,
            sameAsPassword: sameAs('password'),
          },
          billing: {
            country_id: {
              required,
            },
            state_id: {
              required,
            },
            city: {
              required,
            },
            zip: {
              required,
            },
            address_street_1: {
              required,
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          shipping: {
            address_street_1: {
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          language: {
            required,
          },
          timezone: {
            required,
          },
        },
      }
    } else if (this.isAuthentication && this.isBusiness) {
      return {
        formData: {
          status_customer: {
            required,
          },
          name: {
            required,
            minLength: minLength(3),
          },
          status_payment: {
            required,
          },
          sale_type: {
            
          },
          email: {
            required,
            email,
          },
          website: {
            url,
          },
          email_low_balance_notification: {
            required,
            numeric,
            minValue: minValue(0),
          },
          auto_replenish_amount: {
            required,
            numeric,
            minValue: minValue(0),
          },
          password: {
            required,
            minLength: minLength(8),
          },
          confirm_password: {
            required,
            sameAsPassword: sameAs('password'),
          },
          billing: {
            country_id: {
              required,
            },
            state_id: {
              required,
            },
            city: {
              required,
            },
            zip: {
              required,
            },
            address_street_1: {
              required,
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          shipping: {
            address_street_1: {
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          language: {
            required,
          },
          timezone: {
            required,
          },
        },
      }
    } else if (this.isAuthentication) {
      return {
        formData: {
          status_customer: {
            required,
          },
          name: {
            required,
            minLength: minLength(3),
          },
          first_name: {
            required,
            minLength: minLength(3),
          },
          last_name: {
            required,
            minLength: minLength(3),
          },
          status_payment: {
            required,
          },
          sale_type: {
            
          },
          email: {
            required,
            email,
          },
          website: {
            url,
          },
          email_low_balance_notification: {
            required,
            numeric,
            minValue: minValue(0),
          },
          auto_replenish_amount: {
            required,
            numeric,
            minValue: minValue(0),
          },
          password: {
            required,
            minLength: minLength(8),
          },
          confirm_password: {
            required,
            sameAsPassword: sameAs('password'),
          },
          billing: {
            country_id: {
              required,
            },
            state_id: {
              required,
            },
            city: {
              required,
            },
            zip: {
              required,
            },
            address_street_1: {
              required,
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          shipping: {
            address_street_1: {
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          language: {
            required,
          },
          timezone: {
            required,
          },
        },
      }
    } else if (this.isBusiness) {
      return {
        formData: {
          status_customer: {
            required,
          },
          name: {
            required,
            minLength: minLength(3),
          },
          status_payment: {
            required,
          },
          sale_type: {
            
          },
          email: {
            required,
            email,
          },
          website: {
            url,
          },
          email_low_balance_notification: {
            required,
            numeric,
            minValue: minValue(0),
          },
          auto_replenish_amount: {
            required,
            numeric,
            minValue: minValue(0),
          },
          billing: {
            country_id: {
              required,
            },
            state_id: {
              required,
            },
            city: {
              required,
            },
            zip: {
              required,
            },
            address_street_1: {
              required,
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          shipping: {
            address_street_1: {
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          language: {
            required,
          },
          timezone: {
            required,
          },
        },
      }
    } else {
      return {
        formData: {
          status_customer: {
            required,
          },
          name: {
            required,
            minLength: minLength(3),
          },
          first_name: {
            required,
            minLength: minLength(3),
          },
          last_name: {
            required,
            minLength: minLength(3),
          },
          status_payment: {
            required,
          },
          sale_type: {
            
          },
          email: {
            required,
            email,
          },
          website: {
            url,
          },
          email_low_balance_notification: {
            required,
            numeric,
            minValue: minValue(0),
          },
          auto_replenish_amount: {
            required,
            numeric,
            minValue: minValue(0),
          },
          billing: {
            country_id: {
              required,
            },
            state_id: {
              required,
            },
            city: {
              required,
            },
            zip: {
              required,
            },
            address_street_1: {
              required,
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          shipping: {
            address_street_1: {
              maxLength: maxLength(255),
            },
            address_street_2: {
              maxLength: maxLength(255),
            },
          },
          language: {
            required,
          },
          timezone: {
            required,
          },
        },
      }
    }
  },
  computed: {
    ...mapGetters(['currencies']),
    ...mapGetters('company', ['defaultCurrency']),
    ...mapGetters(['languages']),

    isPrepaidOption() {
      return this.prepaid_option_status
    },
    isAuthentication() {
      return this.authentication_status
    },
    isUsername() {
      return this.username_status
    },
    isEdit() {
      if (this.$route.name === 'customers.edit') {
        return true
      }
      return false
    },
    setPass() {
      if (this.formData.password) return true
      return false
    },
    pageTitle() {
      if (this.$route.name === 'customers.edit') {
        return this.$t('customers.edit_customer')
      }
      return this.$t('customers.new_customer')
    },
    hasBillingAdd() {
      let billing = this.formData.billing
      if (
        billing.name ||
        billing.country_id ||
        billing.state ||
        billing.city ||
        billing.phone ||
        billing.zip ||
        billing.address_street_1 ||
        billing.address_street_2 ||
        billing.tax_exempt ||
        billing.tax_id_vatin ||
        billing.delivery_method ||
        billing.payment_notices
      ) {
        return true
      }
      return false
    },
    hasShippingAdd() {
      let shipping = this.formData.shipping
      if (
        shipping.name ||
        shipping.country_id ||
        shipping.state_id ||
        shipping.city ||
        shipping.phone ||
        shipping.zip ||
        shipping.address_street_1 ||
        shipping.address_street_2
      ) {
        return true
      }
      return false
    },
    getLanguages() {
      return this.languages.filter(
        (language) => language.code === 'en' || language.code === 'es'
      )
    },

    statusCustomerError() {
      /* console.log(this.$v.formData) */
      if (this.$v.formData != null && this.$v.formData != 'undefined') {
        if (!this.$v.formData.status_customer.$error) {
          return ''
        }
        if (!this.$v.formData.status_customer.required) {
          return this.$tc('validation.required')
        }
      }
    },
    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      } else {
        return this.$tc(
          'validation.first_name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
    },
    emailLowBalanceNotificationError() {
      if (!this.$v.formData.email_low_balance_notification.$error) {
        return ''
      }
      if (!this.$v.formData.email_low_balance_notification.numeric) {
        return this.$tc('validation.numbers_only')
      }
      if (!this.$v.formData.email_low_balance_notification.minValue) {
        return this.$t('validation.min_number')
      }
      if (!this.$v.formData.email_low_balance_notification.required) {
        return this.$t('validation.required')
      }
    },
    autoReplenishAmountError() {
      if (!this.$v.formData.auto_replenish_amount.$error) {
        return ''
      }
      if (!this.$v.formData.auto_replenish_amount.numeric) {
        return this.$tc('validation.numbers_only')
      }
      if (!this.$v.formData.auto_replenish_amount.minValue) {
        return this.$t('validation.min_number')
      }
      if (!this.$v.formData.auto_replenish_amount.required) {
        return this.$t('validation.required')
      }
    },
    displayFirstNameError() {
      if (this.isResidential) {
        if (!this.$v.formData.first_name.$error) {
          return ''
        }
        if (!this.$v.formData.first_name.required) {
          return this.$tc('validation.required')
        } else {
          return this.$tc(
            'validation.first_name_min_length',
            this.$v.formData.first_name.$params.minLength.min,
            { count: this.$v.formData.first_name.$params.minLength.min }
          )
        }
      }
    },
    displayLastNameError() {
      if (this.isResidential) {
        if (!this.$v.formData.last_name.$error) {
          return ''
        }
        if (!this.$v.formData.last_name.required) {
          return this.$tc('validation.required')
        } else {
          return this.$tc(
            'validation.last_name_min_length',
            this.$v.formData.last_name.$params.minLength.min,
            { count: this.$v.formData.last_name.$params.minLength.min }
          )
        }
      }
    },
    emailError() {
      if (!this.$v.formData.email.$error) {
        return ''
      }

      if (!this.$v.formData.email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },
    urlError() {
      if (!this.$v.formData.website.$error) {
        return ''
      }

      if (!this.$v.formData.website.url) {
        return this.$tc('validation.invalid_url')
      }
    },
    countryIdError() {
      if (!this.$v.formData.billing.country_id.$error) {
        return ''
      }
      if (!this.$v.formData.billing.country_id.required) {
        return this.$tc('validation.required')
      }
    },
    stateIdError() {
      if (!this.$v.formData.billing.state_id.$error) {
        return ''
      }
      if (!this.$v.formData.billing.state_id.required) {
        return this.$tc('validation.required')
      }
    },
    cityError() {
      if (!this.$v.formData.billing.city.$error) {
        return ''
      }
      if (!this.$v.formData.billing.city.required) {
        return this.$tc('validation.required')
      }
    },
    zipError() {
      if (!this.$v.formData.billing.zip.$error) {
        return ''
      }
      if (!this.$v.formData.billing.zip.required) {
        return this.$tc('validation.required')
      }
    },
    billAddress1Error() {
      if (!this.$v.formData.billing.address_street_1.$error) {
        return ''
      }
      if (!this.$v.formData.billing.address_street_1.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.billing.address_street_1.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    billAddress2Error() {
      if (!this.$v.formData.billing.address_street_2.$error) {
        return ''
      }

      if (!this.$v.formData.billing.address_street_2.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    shipAddress1Error() {
      if (!this.$v.formData.shipping.address_street_1.$error) {
        return ''
      }

      if (!this.$v.formData.shipping.address_street_1.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    shipAddress2Error() {
      if (!this.$v.formData.shipping.address_street_2.$error) {
        return ''
      }

      if (!this.$v.formData.shipping.address_street_2.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    isAddAvalara() {
      this.add_avalara = this.formData.avalara_bool
      return this.add_avalara
    },
    usernameError() {
      if (!this.$v.formData.customer_username.$error) {
        return ''
      }
      if (!this.$v.formData.customer_username.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.customer_username.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.customer_username.$params.minLength.min,
          { count: this.$v.formData.customer_username.$params.minLength.min }
        )
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
          'validation.name_min_length',
          this.$v.formData.password.$params.minLength.min,
          { count: this.$v.formData.password.$params.minLength.min }
        )
      }
    },
    confirmPasswordError() {
      if (!this.$v.formData.confirm_password.$error) {
        return ''
      }
      if (!this.$v.formData.confirm_password.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.confirm_password.sameAsPassword) {
        return this.$tc('validation.password_incorrect')
      }
    },
    statusPaymentError() {
      if (!this.$v.formData.status_payment.$error) {
        return ''
      }
      if (!this.$v.formData.status_payment.required) {
        return this.$t('validation.required')
      }
    },
    avalaraTypeSelectedError() {
      if (!this.$v.formData.avalara_type_selected.$error) {
        return ''
      }
      if (!this.$v.formData.avalara_type_selected.required) {
        return this.$t('validation.required')
      }
    },
    saleTypeError() {
      if (!this.$v.formData.sale_type.$error) {
        return ''
      }
      if (!this.$v.formData.sale_type.required) {
        return this.$t('validation.required')
      }
    },
    languageError() {
      if (!this.$v.formData.language.$error) {
        return ''
      }
      if (!this.$v.language.required) {
        return this.$tc('validation.required')
      }
    },
  },
  watch: {
    continent(val) {
      this.listZoneByContinent(val)
      this.formData.timezone = ''
      this.timezone = ''
    },
    timezone(val) {
      this.formData.timezone = val.value
    },
    billing_country(newCountry) {
      if (newCountry) {
        this.formData.billing.country_id = newCountry.id
        this.isDisabledBillingState = false
      } else {
        this.formData.billing.country_id = null
      }
    },
    billing_state(newState) {
      if (newState) {
        this.formData.billing.state_id = newState.id
      } else {
        this.formData.billing.state_id = null
      }
    },
    shipping_state(newState) {
      if (newState) {
        this.formData.shipping.state_id = newState.id
      } else {
        this.formData.shipping.state_id = null
      }
    },
    billing_delivery_method(newMethod) {
      if (newMethod) {
        this.formData.billing.delivery_method = newMethod.value
      } else {
        this.formData.billing.delivery_method = null
      }
    },
    shipping_country(newCountry) {
      if (newCountry) {
        this.formData.shipping.country_id = newCountry.id
        return true
      } else {
        this.formData.shipping.country_id = null
      }
    },
    /* formData:{
      'customer_username':function(value , oldValue){
        console.log(value,oldValue)
      },
    } */
    'formData.customer_username'(value, oldValue) {
      if (value.length < 5) {
        this.userView = false
        this.userValidText = ''
      } else {
        this.userView = true
        this.usernameAvailable()
      }
    },
  },
  created() {
    this.fetchInitData()
    this.listContienents()
  },
  mounted() {
    this.fetchAvalaraModule();
    if (this.isEdit) {
      this.loadCustomer()
      return true
    }
    if (this.auto == 'true' || this.auto == 1) {
      this.generate()
    }
    this.currency = this.defaultCurrency
    this.setInitialCustomFields('Customer');
    
  },
  methods: {
    ...mapActions('customer', [
      'addCustomer',
      'fetchCustomer',
      'updateCustomer',
      'fetchViewCustomer',
      'sendPassword',
      'billingValidation',
    ]),

    ...mapActions('customFields', ['fetchCustomFields']),
    ...mapActions(['fetchLanguages']),
    ...mapActions('modules', ['getContienents', 'getZoneByContinent']),

    async fetchAvalaraModule(){
      let res = await window.axios.get('/api/v1/module/avalara');
      console.log('res avalara module', res);
      this.avalara_module_backend = res.data; 
    },

    async usernameAvailable() {
      this.userValidText = this.$t('customers.searching')
      this.userValid = true
      if (this.formData.customer_username) {
        let res = await window.axios.get(
          '/api/v1/customers/username/' + this.formData.customer_username
        )
        if (res.data.username) {
          ;(this.userValid = false),
            (this.userValidText = this.$t('customers.username_available_no'))
          /* window.toastr['error'](this.$t('customers.username_available_no')) */
        } else {
          ;(this.userValid = true),
            (this.userValidText = this.$t('customers.username_available_yes'))
          /*  window.toastr['success'](this.$t('customers.username_available_yes')) */
        }
      }
    },

    generate() {
      this.bandGeneratePassword = true
      let charactersArray = this.characters.split(',')
      let CharacterSet = ''
      let password = ''

      if (charactersArray.indexOf('a-z') >= 0) {
        CharacterSet += 'abcdefghijklmnopqrstuvwxyz'
      }
      if (charactersArray.indexOf('A-Z') >= 0) {
        CharacterSet += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
      }
      if (charactersArray.indexOf('0-9') >= 0) {
        CharacterSet += '0123456789'
      }
      if (charactersArray.indexOf('#') >= 0) {
        CharacterSet += '!%&*$#@'
      }

      for (let i = 0; i < this.size; i++) {
        password += CharacterSet.charAt(
          Math.floor(Math.random() * CharacterSet.length)
        )
      }
      this.formData.password = password
      this.formData.confirm_password = password
    },
    sendPass() {
      this.$v.formData.password.$touch()
      this.$v.formData.confirm_password.$touch()

      if (
        this.$v.formData.password.$invalid ||
        this.$v.formData.confirm_password.$invalid
      ) {
        return true
      }

      this.bandSendPass = true
      let data = {
        userId: this.$route.params.id,
        password: this.formData.password,
      }

      this.sending(data)
    },
    async sending(data) {
      this.isLoading = true
      let response = await this.sendPassword(data)
      console.log('send pass: ', response)
      if (response.data.success) {
        window.toastr['success'](response.data.message)
      } else {
        window.toastr['error'](response.data)
      }
      this.isLoading = false
    },
    async countrySelected(country, type) {
      const vm = this
      vm.isLoading = true
      if (type == 'billing') {
        vm.billing_state = null
        vm.billing_states = []
      } else {
        vm.shipping_state = null
        vm.shipping_states = []
      }
      let res = await window.axios.get('/api/v1/states/' + country.code)
      if (res) {
        if (type == 'billing') {
          vm.billing_states = res.data.states
        } else {
          vm.shipping_states = res.data.states
        }
      }
      vm.isLoading = false
    },
    companySelected(val, type) {
      const vm = this
      switch (type) {
        case 'bscl':
          vm.formData.bscl = val.value
          break
        case 'svcl':
          vm.formData.svcl = val.value
          break
        case 'fclt':
          vm.formData.fclt = val.value
          break
        case 'reg':
          vm.formData.reg = val.value
          break
      }
    },
    currencyNameWithCode({ name, code }) {
      return `${code} - ${name}`
    },
    async loadCustomer() {
      let params = {
        id: this.$route.params.id,
      }
      let response = await this.fetchCustomer(params)
      console.log('fetchCustomer', response)
      this.add_shipping_addre = response.data.customer.add_shipping_addres
        ? true
        : false
      this.company_id = response.data.customer.company_id

      // load timezone
      if (response.data.customer.timezone) {
        const labelArr = response.data.customer.timezone.split('/')
        this.continent = labelArr[0]
        let label = `${labelArr[1]} ${
          labelArr.length > 2 ? ' - ' + labelArr[2] : ''
        }`
        setTimeout(() => {
          this.timezone = {
            label: label,
            value: response.data.customer.timezone,
          }
        }, 600)
      }
      this.formData = { ...this.formData, ...response.data.customer }
      console.log('auto suspension: ', this.formData.auto_suspension);
      this.formData.minimun_balance = parseFloat(
        response.data.customer.minimun_balance
      )

      this.authentication_status = this.formData.authentication
      this.username_status = this.formData.username_status
      this.formData.password = this.formData.password_encrypted
      this.formData.confirm_password = this.formData.password_encrypted

      this.formData.language = this.getLanguages.find(
        (lang) => lang.code === response.data.customer.language
      )

      // console.log('loadCustomer', response.data.customer)

      if (response.data.customer.servicescount) {
        if (response.data.customer.servicescount == 1) {
          this.isServiceON = true
        }
      }

      if (response.data.customer.status_payment) {
        // console.log('loadCustomer 2', response.data.customer.status_payment)

        if (response.data.customer.status_payment == 'postpaid') {
          this.formData.status_payment = { value: 'postpaid', text: 'Postpaid' }
        } else {
          if (response.data.customer.status_payment == 'prepaid') {
            this.formData.status_payment = { value: 'prepaid', text: 'Prepaid' }
          }
        }
      }

      if (response.data.customer.sale_type) {
        // console.log('loadCustomer 2', response.data.customer.status_payment)

        if (response.data.customer.sale_type == 'Retail') {
          this.formData.sale_type = { value: 'Retail', text: 'Wholesale' }
        } else {
          if (response.data.customer.sale_type == 'Wholesale') {
            this.formData.sale_type = { value: 'Retail', text: 'Wholesale' }
          }
        }
      }
      // Company Section load
      this.avalara_company_bscl_selected =
        this.avalara_company_bscl_options.find((bscl) => {
          return bscl.value == this.formData.bscl
        })
      this.avalara_company_svcl_selected =
        this.avalara_company_svcl_options.find((svcl) => {
          return svcl.value == this.formData.svcl
        })
      this.avalara_company_fclt_selected =
        this.avalara_company_fclt_options.find((fclt) => {
          return fclt.value == this.formData.fclt
        })
      this.avalara_company_reg_selected = this.avalara_company_reg_options.find(
        (reg) => {
          return reg.value == this.formData.reg
        }
      )

      // Prepaid Options
      this.prepaid_option_status =
        this.formData.prepaid_option == '1' ? true : false
      this.auto_debit_status = this.formData.auto_debit == '1' ? true : false

      if (response.data.customer.billing_address) {
        this.formData.billing = response.data.customer.billing_address

        if (response.data.customer.billing_address.country_id) {
          this.billing_country = response.data.customer.billing_address.country
          this.countrySelected(this.billing_country, 'billing')
        }

        if (response.data.customer.billing_address.state_id) {
          this.billing_state = response.data.customer.billing_address.state
        }

        if (response.data.customer.billing_address.delivery_method) {
          this.billing_delivery_method = {
            name: response.data.customer.billing_address.delivery_method,
            value: response.data.customer.billing_address.delivery_method,
          }
        }
      }

      if (response.data.customer.shipping_address) {
        this.formData.shipping = response.data.customer.shipping_address

        if (response.data.customer.shipping_address.country_id) {
          this.shipping_country =
            response.data.customer.shipping_address.country
          this.countrySelected(this.shipping_country, 'shipping')
        }

        if (response.data.customer.shipping_address.state_id) {
          this.shipping_state = response.data.customer.shipping_address.state
        }
      }

      if (response.data.customer.customer_type) {
        let c_type_name = ''

        switch (response.data.customer.customer_type) {
          // case 'N':
          //   c_type_name = 'None'
          //   break
          case 'B':
            c_type_name = 'Business'
            break
          case 'R':
            c_type_name = 'Residential'
            break
          default:
            break
        }
        this.customer_type_selected = {
          name: c_type_name,
          value: response.data.customer.customer_type,
        }
        this.CutomerTypeSelected(this.customer_type_selected)
        // console.log('customer type: ', this.customer_type_selected);
      }

      if (
        response.data.customer.avalara_type !== '' &&
        response.data.customer.avalara_type !== null
      ) {
        let a_type_name = ''
        switch (response.data.customer.avalara_type) {
          case '0':
            a_type_name = 'Residential'
            break
          case '1':
            a_type_name = 'Business'
            break
          case '2':
            a_type_name = 'Senior Citizen'
            break
          case '3':
            a_type_name = 'Industrial'
            break
          default:
            break
        }
        this.formData.avalara_type_selected = {
          name: a_type_name,
          value: response.data.customer.avalara_type,
        }
      }

      this.formData.currency_id = response.data.customer.currency_id
      this.currency = response.data.customer.currency

      let res = await this.fetchCustomFields({ type: 'Customer', limit: 'all' })
      let customFields = res.data.customFields.data
      this.formData.fields = response.data.customer.fields
      this.setEditCustomFields(response.data.customer.fields, customFields)
    },
    async fetchInitData() {
      this.initLoad = true
      let res = await window.axios.get('/api/v1/countries')
      if (res) {
        this.countries = res.data.countries
      }
      await this.fetchLanguages()
      this.formData.language = this.languages.find(
        (language) => language.code === 'en'
      )
      this.initLoad = false
    },
    copyAddress(val) {
      if (val === true) {
        this.isCopyFromBilling = true
        this.formData.shipping = { ...this.formData.billing, type: 'shipping' }
        this.shipping_country = this.billing_country
        this.shipping_state = this.billing_state
        this.shipping_city = this.billing_city
      } else {
        this.formData.shipping = { ...AddressStub, type: 'shipping' }
        this.shipping_country = null
        this.shipping_state = null
        this.shipping_city = null
      }
    },
    async CutomerTypeSelected(val) {
      // console.log('CutomerTypeSelected val', val)
      if (val.value === 'R') {
        this.isResidential = true
        this.isBusiness = false
      } else if (val.value === 'B') {
        this.isBusiness = true
        this.isResidential = false
      }
    },
    async submitCustomerData() {

    
      if (this.bandGeneratePassword) {
        this.bandGeneratePassword = false
        return true
      }

      if (this.bandSendPass) {
        this.bandSendPass = false
        return true
      }

      if (this.isResidential) {
        this.formData.name =
          this.formData.first_name + ' ' + this.formData.last_name
        this.formData.contact_name =
          this.formData.first_name + ' ' + this.formData.last_name
      }

      

      this.$v.formData.$touch()
      let validate = await this.touchCustomField();

      if (this.$v.$invalid || validate.error) {
         
        return true
      }


      let valor =
        this.formData.minimun_balance != 0
          ? Number.isInteger(this.formData.minimun_balance)
            ? this.formData.minimun_balance
            : this.formData.minimun_balance.split(',')
          : 0

 

      this.formData.minimun_balance =
        valor.length > 1
          ? this.formData.minimun_balance.replace(',', '.')
          : this.formData.minimun_balance

         
      this.formData.authentication = this.isAuthentication
      let statusBackup = this.formData.status_customer // backup del objeto inicial del campo
      this.formData.status_customer = this.formData.status_customer.value
      this.formData.username_status = this.isUsername
      let statusPaymentBackup = this.formData.status_payment // backup del objeto inicial del campo
      this.formData.status_payment = this.formData.status_payment.value
      let saleTypeBackup = this.formData.sale_type // backup del objeto inicial del campo
      if(this.formData.sale_type){
this.formData.sale_type = this.formData.sale_type.value
      
      }
      let languageBackup = this.formData.language // backup del objeto inicial del campo
      this.formData.language = this.formData.language.code

       
      if (this.hasBillingAdd && this.hasShippingAdd) {
        this.formData.billing.company_id = this.company_id
        this.formData.shipping.company_id = this.company_id
        this.formData.addresses = [
          { ...this.formData.billing },
          { ...this.formData.shipping },
        ]
      } else if (this.hasBillingAdd) {
        this.formData.billing.company_id = this.company_id
        this.formData.addresses = [{ ...this.formData.billing }]
      } else if (this.hasShippingAdd) {
        this.formData.shipping.company_id = this.company_id
        this.formData.addresses = [{ ...this.formData.shipping }]
        this.formData.addresses.company_id = this.company_id
      }

      this.formData.customer_type = this.customer_type_selected.value
      this.formData.add_shipping_addres = this.add_shipping_addre ? '1' : '0'

      if (this.formData.avalara_type_selected && this.isAddAvalara) {
        this.formData.avalara_type = this.formData.avalara_type_selected.value
      } else {
        this.formData.avalara_type = ''
      }

      try {
        let response = null
        this.isLoading = true
        if (this.currency) {
          this.formData.currency_id = this.currency.id
        }

        if (this.isEdit) {
          if (this.formData.status_customer != 'A') {
            swal({
              title: this.$t('general.are_you_sure'),
              text: this.$tc('customers.change_status'),
              icon: 'warning',
              buttons: true,
              // showCancelButton: true,
            }).then(async (willDelete) => {
              if (willDelete) {
                response = await this.updateCustomer(this.formData)
                if (response.data.success) {
                  this.$router.push(
                    `/admin/customers/${response.data.customer.id}/view`
                  )
                  window.toastr['success'](this.$t('customers.updated_message'))
                }
                if (response.data.error) {
                  window.toastr['error'](
                    this.$t('validation.email_already_taken')
                  )
                }
              }
            })
          } else {
            response = await this.updateCustomer(this.formData)
            if (response.data.success) {
              this.$router.push(
                `/admin/customers/${response.data.customer.id}/view`
              )
              window.toastr['success'](this.$t('customers.updated_message'))
            }
            if (response.data.error) {
              window.toastr['error'](this.$t('validation.email_already_taken'))
            }
          }
        } else {
          response = await this.addCustomer(this.formData)
          console.log('addCustomer', response)

          if (response.data.success) {
            this.$router.push(
              `/admin/customers/${response.data.customer.id}/view`
            )
            window.toastr['success'](this.$t('customers.created_message'))
          }
        }

        this.isLoading = false
        return true
      } catch (error) {
        this.isLoading = false

        this.formData.status_customer = statusBackup
        this.formData.sale_type = saleTypeBackup
        this.formData.status_payment = statusPaymentBackup
        this.formData.language = languageBackup

      }
    },
    async saveConfirmation(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customers.confirm_delete'),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result) {
          let res = await this.deleteCustomer({ ids: [id] })
          console.log('deleteCustomer', res)

          if (res.data.success) {
            window.toastr['success'](this.$tc('customers.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](res.data.message)
          return true
        }
      })
    },
    slideChange() {
      this.formData.avalara_type_selected = this.add_avalara
        ? { name: 'Residential', value: 0 }
        : {}
    },
    prepaidOptionChange(val) {
      this.prepaid_option_status = val
      this.formData.prepaid_option = val ? '1' : '0'
    },
    autoDebitChange(val) {
      this.auto_debit_status = val
      this.formData.auto_debit = val ? '1' : '0'
    },
    authenticationChange(val) {
      this.authentication_status = val
      this.formData.authentication = val ? '1' : '0'
    },
    /* authenticationAddShippingAddres(val) {
      this.add_shipping_addres = val
      this.formData.add_shipping_addres = val ? '1' : '0'
    }, */

    // metodo para traer todos los contienentes
    async listContienents() {
      let response = await this.getContienents()
      if (response.success) {
        this.continentOptions = response.contienents
      }
    },

    // metodo para traer todas las zonas por continente
    async listZoneByContinent(continent) {
      let response = await this.getZoneByContinent(continent)
      if (response.success) {
        this.timezonesOptiones = response.timezones
      }
    },

    async checkBilling() {
      this.$v.formData.billing.country_id.$touch()
      this.$v.formData.billing.state_id.$touch()
      this.$v.formData.billing.city.$touch()
      this.$v.formData.billing.zip.$touch()

      if (
        this.$v.formData.billing.country_id.$invalid ||
        this.$v.formData.billing.state_id.$invalid ||
        this.$v.formData.billing.city.$invalid ||
        this.$v.formData.billing.zip.$invalid
      ) {
        return true
      }

      let data = {
        country: this.billing_country.name,
        state: this.billing_state.name,
        city: this.formData.billing.city,
        zip_code: this.formData.billing.zip,
      }

      let response = await this.billingValidation(data)

      if (response.data.check.success) {
        window.toastr['success']('Billing address is valid')
        return true
      }

      window.toastr['error']('Billing address is invalid')
    },
  },
}
</script>
