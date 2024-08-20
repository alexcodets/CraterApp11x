<template>
  <div class="pbx-service-details">
    <sw-divider class="my-6" />

    <p class="text-gray-500 uppercase sw-section-title">
      {{ $t('customers.pbxservices_resume') }}
    </p>

    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />

    <div class="grid grid-cols-12">
      <div class="col-span-12">
          <div
            class="
              grid grid-cols-1
              gap-4
              mt-5
              lg:grid-cols-3
              md:grid-cols-2
              sm:grid-cols-1
            "
          >
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('customers.service_number') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  selectedPbxService
                    ? selectedPbxService.pbx_services_number
                    : ''
                }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('customers.package_name') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  selectedPbxService && selectedPbxService.pbx_package
                    ? selectedPbxService.pbx_package.pbx_package_name
                    : ''
                }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('packages.status') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ serviceStatus ? serviceStatus.name : '' }}
              </p>
            </div>
          </div>

          <div
            class="
              grid grid-cols-1
              gap-4
              mt-5
              lg:grid-cols-3
              md:grid-cols-2
              sm:grid-cols-1
            "
          >
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('customers.term') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ selectedPbxService ? selectedPbxService.term : '' }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('customers.discount') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ allowDiscount }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('customers.date_begin') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ selectedPbxService ? selectedPbxService.date_begin : '' }}
              </p>
            </div>
          </div>

          <div
            class="
              grid grid-cols-1
              gap-4
              mt-5
              lg:grid-cols-3
              md:grid-cols-2
              sm:grid-cols-1
            "
          >
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('pbx_services.type_service') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  selectedPbxService && selectedPbxService.pbx_package
                    ? selectedPbxService.pbx_package.status_payment
                    : ''
                }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('pbx_services.server') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  selectedPbxService &&
                  selectedPbxService.pbx_package &&
                  selectedPbxService.pbx_package.server
                    ? selectedPbxService.pbx_package.server.server_label
                    : ''
                }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('pbx_services.tenant') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  selectedPbxService && selectedPbxService.tenant
                    ? selectedPbxService.tenant.name
                    : ''
                }}
              </p>
            </div>
          </div>

          <div
            class="
              grid grid-cols-1
              gap-4
              mt-5
              lg:grid-cols-3
              md:grid-cols-2
              sm:grid-cols-1
            "
          >
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('pbx_services.package_price') }}
              </p>
              <p
                class="text-sm font-bold leading-5 text-black non-italic"
                v-html="
                  selectedPbxService
                    ? $utils.formatMoney(
                        selectedPbxService.pbxpackages_price * 100,
                        defaultCurrency
                      )
                    : ''
                "
              />
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('pbx_services.package_number') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  selectedPbxService && selectedPbxService.pbx_package
                    ? selectedPbxService.pbx_package.packages_number
                    : ''
                }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('pbx_services.auto_suspension') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  selectedPbxService
                    ? selectedPbxService.auto_suspension === 1
                      ? 'Active'
                      : 'Inactive'
                    : ''
                }}
              </p>
            </div>

                        <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('pbx_services.cap_by_extension') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                 {{ selectedPbxService ? selectedPbxService.cap_extension : '' }}
              </p>
            </div>

                                 <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('pbx_services.total_by_extension') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                 {{ selectedPbxService ? selectedPbxService.cap_total : '' }}
              </p>
            </div>

              <div>
                      <p
                        class="
                          mb-1
                          text-sm
                          font-normal
                          leading-5
                          non-italic
                          text-primary-800
                        "
                      >
                        {{ $t('pbx_services.inclusive_minutes_consume') }}
                      </p>
                      <p
                        class="
                          text-sm
                          font-bold
                          leading-5
                          text-black
                          non-italic
                        "
                      >
                       

                        <div v-if="this.inclusive_minutes_cosumed == null">
                                    0
                          </div>

 <div v-if="this.inclusive_minutes_cosumed != null">
                                   {{ this.inclusive_minutes_cosumed.toFixed(2) }}
                          </div>


                      </p>
                    </div>

            
                                 <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('pbx_services.prorateotal') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                 {{ selectedPbxService ? (selectedPbxService.total_prorate/100) : '' }}
              </p>
            </div>
          </div>

          <!--    CDR Processes      -->
          <p class="text-gray-500 uppercase sw-section-title mt-6">
            {{ $t('pbx_services.cdr_processes') }}
          </p>

          <div
            class="grid grid-cols-1 gap-4 mt-5 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1"
          >
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('pbx_services.calculated') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ commandosDataCdr.calculated }}
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('pbx_services.calculated_today') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ commandosDataCdr.calculated_today }}
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('pbx_services.uncalculated') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ commandosDataCdr.unCalculated }}
              </p>
            </div>
          </div>

         
          <!--    Prepaid information      -->
          <p
            v-if="isPrepaid"
            class="text-gray-500 uppercase sw-section-title mt-6"
          >
            {{ $t('pbx_services.prepaid_information') }}
          </p>

          <div
            v-if="isPrepaid"
            class="grid grid-cols-1 gap-4 mt-5 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1"
          >
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('pbx_services.total_consume') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ defaultCurrency.symbol + ' ' + total_consume.toFixed(2) }}
              </p>
            </div>

            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('pbx_services.paid_consume') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ defaultCurrency.symbol + ' ' + paid_consume.toFixed(2) }}
              </p>
            </div>

            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('pbx_services.unpaid_consume') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ defaultCurrency.symbol + ' ' + unpaid_consume.toFixed(2) }}
              </p>
            </div>

                <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('pbx_services.deb_consume') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ defaultCurrency.symbol + ' ' + total_deb.toFixed(2) }}
              </p>
            </div>


         
          </div>

        <sw-divider class="my-8" />

        <!------------------- PBXWARE INFO ------------------->
        <div class="tabs mb-8 pb-3 grid col-span-12">
          <div class="border-b tab">
            <div class="relative">
              <input
                class="
                  w-full
                  absolute
                  z-10
                  cursor-pointer
                  opacity-0
                  h-5
                  top-4
                "
                type="checkbox"
              />
              <header
                class="
                  col-span-5
                  flex
                  justify-between
                  items-center
                  py-3
                  cursor-pointer
                  select-none
                  tab-label
                "
              >
                <span class="text-gray-500 uppercase sw-section-title">
                  {{ $t('pbx_services.pbxware_info') }}
                </span>
                <div
                  class="
                    rounded-full
                    border border-grey
                    w-7
                    h-7
                    flex
                    items-center
                    justify-center
                    test
                  "
                >
                  <!-- icon by feathericons.com -->
                  <svg
                    aria-hidden="true"
                    class=""
                    data-reactid="266"
                    fill="none"
                    height="24"
                    stroke="#606F7B"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    viewbox="0 0 24 24"
                    width="24"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </div>
              </header>

              <div class="tab-content-slide">
                <div class="text-grey-darkest">
                  <div class="flex base-tabs"></div>
                </div>

                <div
                  class="
                    grid grid-cols-1
                    gap-4
                    mt-5
                    lg:grid-cols-4
                    md:grid-cols-2
                    sm:grid-cols-1
                  "
                >
                  <div>
                    <p
                      class="
                        mb-1
                        text-sm
                        font-normal
                        leading-5
                        non-italic
                        text-primary-800
                      "
                    >
                      {{ $t('pbx_services.act_extensions') }}
                    </p>
                    <p
                      class="
                        text-sm
                        font-bold
                        leading-5
                        text-black
                        non-italic
                      "
                    >
                      {{
                        package.extensions === 1
                          ? $t('general.yes')
                          : $t('general.not')
                      }}
                    </p>
                  </div>
                  <div>
                    <p
                      class="
                        mb-1
                        text-sm
                        font-normal
                        leading-5
                        non-italic
                        text-primary-800
                      "
                    >
                      {{ $t('pbx_services.act_did') }}
                    </p>
                    <p
                      class="
                        text-sm
                        font-bold
                        leading-5
                        text-black
                        non-italic
                      "
                    >
                      {{
                        package.did === 1
                          ? $t('general.yes')
                          : $t('general.not')
                      }}
                    </p>
                  </div>
                  <div>
                    <p
                      class="
                        mb-1
                        text-sm
                        font-normal
                        leading-5
                        non-italic
                        text-primary-800
                      "
                    >
                      {{ $t('pbx_services.act_call_rating') }}
                    </p>
                    <p
                      class="
                        text-sm
                        font-bold
                        leading-5
                        text-black
                        non-italic
                      "
                    >
                      {{
                        package.call_ratings === 1
                          ? $t('general.yes')
                          : $t('general.not')
                      }}
                    </p>
                  </div>
                  <div>
                    <p
                      class="
                        mb-1
                        text-sm
                        font-normal
                        leading-5
                        non-italic
                        text-primary-800
                      "
                    >
                      {{ $t('pbx_services.fixed_server') }}
                    </p>
                    <p
                      class="
                        text-sm
                        font-bold
                        leading-5
                        text-black
                        non-italic
                      "
                    >
                      {{
                        package.modify_server === 1
                          ? $t('general.yes')
                          : $t('general.not')
                      }}
                    </p>
                  </div>
                </div>

                <div
                  v-if="package.extensions || package.did"
                  class="
                    grid grid-cols-1
                    gap-4
                    mt-5
                    lg:grid-cols-4
                    md:grid-cols-2
                    sm:grid-cols-1
                  "
                >
                  <div v-if="package.extensions">
                    <p
                      class="
                        mb-1
                        text-sm
                        font-normal
                        leading-5
                        non-italic
                        text-primary-800
                      "
                    >
                      {{ $t('pbx_services.pro_ext_name') }}
                    </p>
                    <p
                      class="
                        text-sm
                        font-bold
                        leading-5
                        text-black
                        non-italic
                      "
                    >
                      {{ package.profile_extensions.name }}
                    </p>
                  </div>
                  <div v-if="package.extensions">
                    <p
                      class="
                        mb-1
                        text-sm
                        font-normal
                        leading-5
                        non-italic
                        text-primary-800
                      "
                    >
                      {{ $t('pbx_services.pro_ext_price') }}
                    </p>
                    <p
                      class="
                        text-sm
                        font-bold
                        leading-5
                        text-black
                        non-italic
                      "
                    >
                      {{
                        defaultCurrency.symbol +
                        ' ' +
                        package.profile_extensions.rate
                      }}
                    </p>
                  </div>
                  <div v-if="package.did">
                    <p
                      class="
                        mb-1
                        text-sm
                        font-normal
                        leading-5
                        non-italic
                        text-primary-800
                      "
                    >
                      {{ $t('pbx_services.pro_ext_name2') }}
                    </p>
                    <p
                      class="
                        text-sm
                        font-bold
                        leading-5
                        text-black
                        non-italic
                      "
                    >
                      {{ package.profile_did.name }}
                    </p>
                  </div>
                  <div v-if="package.did">
                    <p
                      class="
                        mb-1
                        text-sm
                        font-normal
                        leading-5
                        non-italic
                        text-primary-800
                      "
                    >
                      {{ $t('pbx_services.pro_ext_price2') }}
                    </p>
                    <p
                      class="
                        text-sm
                        font-bold
                        leading-5
                        text-black
                        non-italic
                      "
                    >
                      {{
                        defaultCurrency.symbol +
                        ' ' +
                        package.profile_did.did_rate
                      }}
                    </p>
                  </div>
                </div>

                <div
                  v-if="package.call_ratings"
                  class="
                    grid grid-cols-1
                    gap-4
                    mt-5
                    lg:grid-cols-4
                    md:grid-cols-2
                    sm:grid-cols-1
                  "
                >
                  <div>
                    <p
                      class="
                        mb-1
                        text-sm
                        font-normal
                        leading-5
                        non-italic
                        text-primary-800
                      "
                    >
                      {{ $t('pbx_services.int_dialing_code') }}
                    </p>
                    <p
                      class="
                        text-sm
                        font-bold
                        leading-5
                        text-black
                        non-italic
                      "
                    >
                      {{ package.international_dialing_code }}
                    </p>
                  </div>
                  <div>
                    <p
                      class="
                        mb-1
                        text-sm
                        font-normal
                        leading-5
                        non-italic
                        text-primary-800
                      "
                    >
                      {{ $t('pbx_services.nac_dialing_code') }}
                    </p>
                    <p
                      class="
                        text-sm
                        font-bold
                        leading-5
                        text-black
                        non-italic
                      "
                    >
                      {{ package.national_dialing_code }}
                    </p>
                  </div>

                      <div
                  v-if="package.call_ratings"
              
                >
                  <div>
                    <p
                      class="
                        mb-1
                        text-sm
                        font-normal
                        leading-5
                        non-italic
                        text-primary-800
                      "
                    >
                      {{ $t('pbx_services.rate_per_min') }}
                    </p>
                    <p
                      class="
                        text-sm
                        font-bold
                        leading-5
                        text-black
                        non-italic
                      "
                    >
                      {{
                        defaultCurrency.symbol +
                        ' ' +
                        package.rate_per_minutes
                      }}
                    </p>
                  </div>
                  
                </div>

      <div
                  v-if="package.call_ratings"
              
                >

<div>
                    <p
                      class="
                        mb-1
                        text-sm
                        font-normal
                        leading-5
                        non-italic
                        text-primary-800
                      "
                    >
                      {{ $t('pbx_services.min_increments') }}
                    </p>
                    <p
                      class="
                        text-sm
                        font-bold
                        leading-5
                        text-black
                        non-italic
                      "
                    >
                      {{
                        package.minutes_increments +
                        ' ' +
                        package.type_time_increment
                      }}
                    </p>
                  </div>

                  </div>
                
                </div>

              

                <div class="mb-5 pb-3"></div>
              </div>
            </div>
          </div>
        </div>

        <!------------------- STATS ------------------->
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-9 xl:gap-8">
          <!-- Total Calls -->
          <router-link
            slot="item-title"
            class="
              relative
              flex
              justify-between
              p-3
              bg-white
              rounded
              shadow
              hover:bg-gray-100
              lg:col-span-2
              xl:p-4
            "
            to=""
          >
            <div>
              <span
                class="
                  text-xl
                  font-semibold
                  leading-tight
                  text-black
                  xl:text-3xl
                "
              >
                <span
                  v-html="
                    selectedPbxService
                      ? $utils.formatMoney(
                          selectedPbxService.total,
                          defaultCurrency
                        )
                      : ''
                  "
                />
              </span>
              <span
                class="
                  block
                  mt-1
                  text-sm
                  leading-tight
                  text-gray-500
                  xl:text-lg
                "
              >
                {{ $t('customers.recurring_charge') }}
              </span>
            </div>
            <div class="flex items-center">
              <estimate-icon class="w-10 h-10 xl:w-12 xl:h-12" />
            </div>
          </router-link>

          <!-- Call Rating -->
          <router-link
            slot="item-title"
            class="
              relative
              flex
              justify-between
              p-3
              bg-white
              rounded
              shadow
              hover:bg-gray-100
              lg:col-span-3
              xl:p-4
            "
            to=""
          >
            <div>
              <span
                class="
                  text-xl
                  font-semibold
                  leading-tight
                  text-black
                  xl:text-3xl
                "
              >
                {{ defaultCurrency.symbol + ' ' + total_consume.toFixed(2) }}
              </span>
              <span
                class="
                  block
                  mt-1
                  text-sm
                  leading-tight
                  text-gray-500
                  xl:text-lg
                "
              >
                {{ $t('pbx_services.additional_month') }}
              </span>
            </div>
            <div class="flex items-center">
              <contact-icon class="w-10 h-10 xl:w-12 xl:h-12" />
            </div>
          </router-link>

          <!-- Renew Date -->
          <router-link
            slot="item-title"
            class="
              relative
              flex
              justify-between
              p-3
              bg-white
              rounded
              shadow
              hover:bg-gray-100
              lg:col-span-2
              xl:p-4
            "
            to=""
          >
            <div>
              <span class="text-lg font-semibold leading-tight text-black">
                <span>
                  {{
                    selectedPbxService ? selectedPbxService.renewal_date : ''
                  }}
                </span>
              </span>
              <span
                class="
                  block
                  mt-1
                  text-sm
                  leading-tight
                  text-gray-500
                  xl:text-lg
                "
              >
                {{ $t('pbx_services.renewal_date') }}
              </span>
            </div>
            <div class="flex items-center">
              <invoice-icon class="w-10 h-10 xl:w-12 xl:h-12" />
            </div>
          </router-link>

          <!-- Credit -->
          <router-link
            v-if="getStatusPayment == 'prepaid'"
            slot="item-title"
            class="
              relative
              flex
              justify-between
              p-3
              bg-white
              rounded
              shadow
              hover:bg-gray-100
              lg:col-span-2
              xl:p-4
            "
            to=""
          >
            <div>
              <span
                class="
                  text-xl
                  font-semibold
                  leading-tight
                  text-black
                  xl:text-3xl
                "
              >
                {{
                  selectedViewCustomer && selectedViewCustomer.customer
                    ? defaultCurrency.symbol +
                      ' ' +
                      selectedViewCustomer.customer.balance.toFixed(2)
                    : ''
                }}
              </span>
              <span
                class="
                  block
                  mt-1
                  text-sm
                  leading-tight
                  text-gray-500
                  xl:text-lg
                "
              >
                {{ $t('pbx_services.credit') }}
              </span>
            </div>
            <div class="flex items-center">
              <dollar-icon class="w-10 h-10 xl:w-12 xl:h-12" />
            </div>
          </router-link>
        </div>


        <div class="relative flex flex-wrap bg-gray-200 mt-4 rouded-lg p-4">
            <sw-input-group :label="$t('general.date_range')" class="mt-2 md:w-1/2 md:pr-2">
              <sw-date-picker
                v-model="dateRange"
                :config="configDatepicker"
                @on-change="setFecha"
                :disabled="false"
                :invalid="false"
                :name="null"
                :tabindex="null"
              />
            </sw-input-group>
            
            <sw-input-group
              :label="$t('general.from')"
              class="mt-2 md:w-1/4 md:pr-2"
            >
              <sw-input v-model="filters.from"></sw-input>
            </sw-input-group>

            <sw-input-group
              :label="$t('general.to')"
              class="mt-2 md:w-1/4 md:pr-2"
            >
              <sw-input v-model="filters.to"></sw-input>
            </sw-input-group>
            <sw-input-group
              :label="$t('general.id')"
              class="mt-2 md:w-1/4 md:pr-2"
            >
              <sw-input v-model="filters.id">
                <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
              </sw-input>
            </sw-input-group>

            <!-- select con checklist -->
              <sw-input-group :label="$t('general.status')" class="mt-2 md:w-1/4 md:pr-2">
                <sw-select
                  :options="[]"
                  :searchable="false"
                  placeholder=""
                >
                  <template v-slot:selection>
                    <p>{{labelStatusCrp}}</p>
                  </template>
                <!-- template beforeList -->
                <template v-slot:beforeList>
                  <div class="flex flex-wrap">
                      <sw-checkbox
                        v-for="(item, index) in cdrStatusOptions"
                        :key="index"
                        v-model="filters.status"
                        :variant="item.color"
                        :label="item.label"
                        :value="item.value"
                        class="w-full p-1 px-4 hover:bg-gray-100"
                        @change="changeStatus"
                      />
                  </div>
                </template>
                </sw-select>
              </sw-input-group>

            <sw-input-group :label="$t('general.cdr_type')" class="mt-2 md:w-1/4 md:pr-2">
              <sw-select
                v-model="filters.cdrType"
                :options="dcrTypeOptions"
                :group-select="false"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('general.cdr_type')"
                :allow-empty="false"
                track-by="value"
                label="name"
                @remove="clearCdrType()"
              />
            </sw-input-group>


            <sw-input-group :label="$t('general.paid')" class="mt-2 md:w-1/4 md:pr-2">
              <sw-select
                v-model="filters.paid"
                :options="paidOptions"
                :group-select="false"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('general.paid')"
                :allow-empty="false"
                track-by="value"
                label="name"
                @remove="clearCdrType()"
              />
            </sw-input-group>

            <label
              class="absolute text-sm leading-snug text-black cursor-pointer"
              @click="clearFilter"
              style="top: 10px; right: 15px"
              >{{ $t('general.clear_all') }}
            </label>

            <div class="w-full flex flex-wrap items-end justify-end mt-3">
              <sw-button
                @click="searchMetho()"
                :loading="searchLoading"
                :disabled="searchLoading"
                class="md:mx-1"
                :variant="'primary-outline'"
              >
                {{ $t('general.search') }}
              </sw-button>
            </div>

        </div>


          <div class="w-full flex justify-between mt-3 items-end">
            <div>
              <sw-input-group :label="$t('general.per_page')" class="mt-2 md:pr-2">
                <sw-select
                  v-model="filters.perPage"
                  :options="pagesOptions"
                  :group-select="false"
                  :allow-empty="false"
                  @input="selectPerPage"
                />
              </sw-input-group>
            </div>

            <div>
              
              <download-csv
                    :data="cdrListExportCsv"
                    :fields="csvFields"
                    :name="`Service_${selectedPbxService.pbx_services_number}-${ dateNow}.csv`"
                    ref="downloadCsv"      
              >
                  <p></p>
              </download-csv>
              <sw-button
                @click="exportAllFiltersCsv"
                :loading="exportexportAllFiltersCsvLoading"
                :disabled="exportexportAllFiltersCsvLoading"
                :variant="exportexportAllFiltersCsvLoading ? 'success' : 'primary'"
              >
                {{ $t('general.export') }}
              </sw-button>
            </div>
          </div>


        <div class="relative">
          <div class="absolute z-5 items-center pl-4 mt-2 md:mt-6">
            <sw-checkbox
              v-model="selectAllFieldStatus"
              variant="primary"
              size="sm"
              class="hidden md:inline"
              @change="selectAllHistoryCall"
            />

            <sw-checkbox
              v-model="selectAllFieldStatus"
              :label="$t('general.select_all')"
              variant="primary"
              size="sm"
              class="md:hidden"
              @change="selectAllHistoryCall"
            />
          </div>

          <sw-table-component
                    ref="cdr_table"
                    :show-filter="false"
                    :data="fetchCallHistoryData"
                    table-class="table"
                    class="w-full"
                  >
                  <sw-table-column
                    :sortable="false"
                    :filterable="false"
                    cell-class="no-click"
                  >
                    <div slot-scope="row" class="relative block">
                      <sw-checkbox
                        :id="row.unique_id"
                        v-model="selectField"
                        :value="row.unique_id"
                        variant="primary"
                        size="sm"
                      />
                    </div>
                  </sw-table-column>
                    <sw-table-column
                      :sortable="true"
                      :label="$t('corePbx.dashboard.from')"
                      show="from"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('corePbx.dashboard.to')"
                      show="to"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('corePbx.dashboard.date')"
                      show="formatted_start_date"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.type')"
                      show="type"
                    >
                      <template slot-scope="row">
                        <span>Type</span>

                        <div v-if="row.type == 0">Inbound</div>
                        <div v-if="row.type == 1">Outbound</div>
                      </template>
                    </sw-table-column>

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('corePbx.dashboard.totald')"
                      show="formatted_duration"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('corePbx.dashboard.totalr')"
                      show="formatted_round_duration"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.amount')"
                      show="cost"
                    >
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.amount') }}</span>

                        <div v-if="row.billed_at == null">Pending</div>

                        <div v-if="row.billed_at != null">
                          <div
                            v-if="row.exclusive_seconds == 0"
                          >
                            0
                          </div>

                          <div
                            v-if="row.exclusive_seconds != 0"
                          >
                            {{ row.getFormattedExcusiveCost }}
                          </div>
                        </div>
                      </template>
                    </sw-table-column>

                    <!-- <sw-table-column
                      :sortable="false"
                      :filterable="false"
                      cell-class="action-dropdown no-click"
                    >
                      <template slot-scope="row">
                        <span>{{ $t('general.actions') }}</span>

                        <sw-dropdown>
                          <dot-icon slot="activator" />
                        </sw-dropdown>
                      </template>
                    </sw-table-column> -->
          </sw-table-component>
          <!------------  TOTALS  ------------>
          <div
            class="
              block
              mt-5
              mb-10
              table-foot
              lg:justify-between
              lg:flex
              lg:items-start
            "
          >
            <div class="w-full lg:w-1/2"></div>

            <div
              class="
                px-5
                py-4
                mt-6
                bg-white
                border border-gray-200 border-solid
                rounded
                table-total
                lg:mt-0
              "
            >
              <!------- COUNT ALL CDR------->
              <div class="flex items-center justify-between w-full">
                <label
                  class="
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                >
                  {{ $t('pbx_services.all_cdr') }}
                </label>
                <label
                  class="
                    flex
                    items-center
                    justify-center
                    m-0
                    text-lg text-black
                    uppercase
                  "
                >
                  <span>{{ this.callHistoryCount }}</span>
                </label>
              </div>

              <!------- COUNT ALL CDR------->
              <div class="flex items-center justify-between w-full">
                <label
                  class="
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                >
                  {{ $t('pbx_services.billed_cdr') }}
                </label>
                <label
                  class="
                    flex
                    items-center
                    justify-center
                    m-0
                    text-lg text-black
                    uppercase
                  "
                >
                  <span>{{ this.billed_cdr.toFixed(2) }}</span>
                </label>
              </div>

              <!------- COUNT ALL CDR------->
              <div class="flex items-center justify-between w-full">
                <label
                  class="
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                >
                  {{ $t('pbx_services.billed_time') }}
                </label>
                <label
                  class="
                    flex
                    items-center
                    justify-center
                    m-0
                    text-lg text-black
                    uppercase
                  "
                >
                  <span>{{ this.billed_time }}</span>
                </label>
              </div>

              <!------- AMOUNT ------->
              <div
                class="
                  flex
                  items-center
                  justify-between
                  w-full
                  pt-2
                  mt-5
                  border-t border-gray-200 border-solid
                "
              >
                <label
                  class="
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                >
                  {{ $t('pbx_services.billed_cost') }}:
                </label>
                <label
                  class="
                    flex
                    items-center
                    justify-center
                    text-lg
                    uppercase
                    text-primary-400
                  "
                >
                  <!--<div v-html="$utils.formatMoney(this.extensionTotal, defaultCurrency)" />-->
                  <span> $ {{ this.callHistoryTotal.toFixed(2) }}</span>
                </label>
              </div>
            </div>
          </div>
        </div>
            
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import DollarIcon from '../../../components/icon/DollarIcon'
import ContactIcon from '../../../components/icon/ContactIcon'
import InvoiceIcon from '../../../components/icon/InvoiceIcon'
import EstimateIcon from '../../../components/icon/EstimateIcon'
import moment from 'moment'
import SwDatePicker from '@bytefury/spacewind/src/components/SwDatePicker'
import { PencilIcon, HashtagIcon } from '@vue-hero-icons/solid'
import JsonCSV from 'vue-json-csv'

export default {
  components: {
    DollarIcon,
    ContactIcon,
    InvoiceIcon,
    EstimateIcon,
    PencilIcon,
    HashtagIcon,
    SwDatePicker,
    'downloadCsv': JsonCSV,
  },

  data() {
    return {
      searchLoading: false,
      exportAllLoading: false,
      exportexportAllFiltersCsvLoading: false,
      isRequestOnGoing: false,
      status: [
        { name: 'Active', value: 'A' },
        { name: 'Pending', value: 'P' },
        { name: 'Suspend', value: 'S' },
        { name: 'Cancelled', value: 'C' },
      ],
      extensionTotal: 0,
      extensionCount: 0,
      didTotal: 0,
      didCount: 0,
      itemTotal: 0,
      itemCount: 0,
      chargesTotal: 0,
      chargesCount: 0,
      callHistoryTotal: 0,
      callHistoryTotalcustom: 0,
      callHistoryCount: 0,
      billed_cost: 0,
      billed_cdr: 0,
      prepaidtotalcurrent: 0,
      prepaidtotalcurrenttax: 0,
      inclusive_minutes_cosumed: 0,
      billed_time: 0,
      customDestinationTotal: 0,
      AddmonthTotal: 0,
      customDestinationCount: 0,
      ctmDestBilledCost: 0,
      ctmDestBilledCDR: 0,
      ctmDestBilledTime: 0,
      ctmDestBilledIncMin: 0,
      package: {
        extensions: 0,
        did: 0,
        modify_server: 0,
        call_ratings: 0,
        profile_did: {
          name: '',
          did_rate: 0,
        },
        profile_extensions: {
          name: '',
          rate: 0,
        },
        international_dialing_code: '',
        national_dialing_code: '',
        inclusive_minutes: 0,
        rate_per_minutes: 0,
        minutes_increments: 0,
        type_time_increment: '',
      },
      commandosDataCdr: {
        calculated: 0,
        calculated_today: 0,
        unCalculated: 0,
      },
      commandosDataJobs: {
        calculate: 0,
        import: 0,
      },
      total_consume: 0,
      paid_consume: 0,
      unpaid_consume: 0,
      total_deb: 0,
      dcrTypeOptions: [
        { name: 'All', value: 'all' },
        { name: 'Inbound', value: 0 },
        { name: 'Outbound', value: 1 },
      ],
      paidOptions: [
        { name: 'All', value: 'all' },
        { name: 'Pending', value: 'Pending' },
        { name: 'Billed', value: 'Billed' },
      ],
      cdrStatusOptions: [
          {label: 'Answered', value: 'Answered', color: 'success'},
          {label: 'Not Answered', value: 'NotAnswered', color: 'warning'},
          {label: 'Busy', value: 'Busy', color: 'danger'},
          {label: 'Error', value: 'Error', color: 'danger'}
      ],
      dateRange: null,
      filters: {
        number: '',
        id: '',
        cdrType: { name: 'All', value: 'all' },
        paid: { name: 'All', value: 'all' },
        status: [],
        dateRange: {
          from: null,
          to: null,
        },
        from: "",
        to: "",
        perPage: 10,
      },
      pagesOptions: [10, 25, 50, 100],
      configDatepicker:{
        mode: 'range',
        enableTime: true,
        enable: [],
        defaultDate: [moment().subtract(1, 'month').format('YYYY-MM-DD'), moment().format('YYYY-MM-DD')],

      },
      selectedHistoryCall: [],
      selectAllFieldStatus: false,
      cdrList: [],
      cdrListExportCsv: [],
      csvFields:[
        'from',
        'to',
        'dateTime',
        'type',
        'totalDuration',
        'ratingDuration',
        'cost',
        'unique_id',
        'estatus',
        ],
      dateNow: moment().format('YYYY-MM-DD'),
    }
  },

  computed: {
    ...mapGetters('customer', ['selectedViewCustomer']),
    ...mapGetters('pbxService', ['selectedPbxService']),
    ...mapGetters('company', ['defaultCurrency']),

    serviceStatus() {
      return this.status.find(
        (_status) => this.selectedPbxService.status === _status.value
      )
    },

    allowDiscount() {
      return this.selectedPbxService.allow_discount === 1 ? 'YES' : 'NO'
    },

    getStatusPayment() {
      return this.selectedViewCustomer && this.selectedViewCustomer.customer
        ? this.selectedViewCustomer.customer.status_payment
        : ''
    },

    backgroundCalculateStatus() {
      return this.commandosDataJobs.calculate ? 'I' : 'A'
    },

    backgroundImportStatus() {
      return this.commandosDataJobs.import ? 'I' : 'A'
    },

    isPrepaid() {
      return this.package.status_payment === 'prepaid'
    },
    labelStatusCrp(){
        const statusLabelArr = this.filters.status.map(item => {
          return this.cdrStatusOptions.find(status => status.value === item).label
        })
        // si inlcluye en 0 muestra All
        if(statusLabelArr.includes('ALL')){
          return 'All'
        }else{
          return statusLabelArr.join(' - ')
        }
        
    },
    selectField: {
      get: function () {
        return this.selectedHistoryCall
      },
      set: function (val) {
        this.selectHistoryCall(val)
      },
    },
  },

  created() {
    window.hub.$on('newCalls', this.reloadCdrTable)
    window.hub.$on('updateExt', this.reloadExtTable)
    this.loadService()
  },
  watch: {
    selectedPbxService(newVal) {
      this.$emit('status', this.serviceStatus.value)
    }
  },
  methods: {
    ...mapActions('pbxService', [
      'fetchPbxService',
      'fetchExtensions',
      'fetchDIDs',
      'fetchItemsPbxService',
      'fetchAdditionalCharges',
      'fetchCallHistory',
      'fetchCommandos',
      'fetchPrepaidCharges',
    ]),

    async loadService() {
      let response = await this.fetchPbxService(this.$route.params.idPbxService)
      this.package = response.data.response.pbx_service.pbx_package

      let commandosResponse = await this.fetchCommandos(
        this.$route.params.idPbxService
      )

      this.commandosDataCdr = {
        ...this.commandosDataCdr,
        ...commandosResponse.data.response.data.cdr,
      }
      this.commandosDataJobs = {
        ...this.commandosDataJobs,
        ...commandosResponse.data.response.data.jobs,
      }

      this.total_consume = this.selectedPbxService.total_consume
      this.paid_consume = this.selectedPbxService.paid_consume
      this.unpaid_consume = this.selectedPbxService.unpaid_consume

      this.total_deb = this.selectedPbxService.total_deb

      // contar diferencias de dias en moment js
      const today = moment()
      let end = moment(this.selectedPbxService.date_begin)
      let diff = end.diff(today, 'days')
      diff = diff * -1
      let from

      if( diff > 90){
        from = moment().subtract(90, 'days')
      }else{
        from = moment().subtract(diff, 'days')
      }
      this.configDatepicker.enable = [
            {
                from: from.format('YYYY-MM-DD'),
                to: today.format('YYYY-MM-DD')
            },
        ]
    },
    selectAllHistoryCall(val) {
      this.selectAllFieldStatus = val
      this.selectedHistoryCall = val ? this.cdrList.map(item => item.unique_id) : []
    },
    selectHistoryCall(val) {
      if (val.length === this.cdrList.length) {
        this.selectAllFieldStatus = true
      } else {
        this.selectAllFieldStatus = false
      }
      this.selectedHistoryCall = val
    },

    selectPerPage(perPage) {
      this.filters.perPage = perPage
      this.reloadCdrTable()
    },
    searchMetho(){
      this.reloadCdrTable()
    },
    exportAll(){
      this.exportAllLoading = true
      setTimeout(() => {
        this.exportAllLoading = false
      }, 3000)
    },
    async exportAllFiltersCsv(){

      try {
        this.exportexportAllFiltersCsvLoading = true

        let data = {
          pbx_service_id: this.$route.params.idPbxService,
          order_by: 'created_at',
          order:  'desc',
          custom: 0,
          filters: JSON.parse(JSON.stringify(this.filters)),
        }
        data.filters.perPage = 1000000000000

        let response = await this.fetchCallHistory(data)
        const datosCdrList = response.data.response.service_cdrs.data
        this.cdrListExportCsv = datosCdrList.map(item => {
          return {
            from: item.from,
            to: item.to,
            dateTime: item.formatted_start_date,
            type: item.type ? 'Inbound' : 'Outbound',
            totalDuration: item.formatted_duration,
            ratingDuration: item.formatted_round_duration,
            cost: item.total_exclusive_cost,
            unique_id: item.unique_id,
            estatus: item.status,
          }
        })
        // dilay para que se cargue la tabla antes de exportar
        setTimeout(() => {
          this.$refs.downloadCsv.generate()
        }, 1000)
        // this.cdrListExportCsv = this.cdrList.filter( item => this.selectedHistoryCall.includes(item.unique_id))
        

        console.log(response.data.response.service_cdrs.data)
        
      } catch (error) {
        console.log(error)
      }finally{
        this.exportexportAllFiltersCsvLoading = false
      }
    },

    clearFilter(){
      this.filters = {
        number: '',
        id: '',
        cdrType: { name: 'All', value: 'all' },
        paid: { name: 'All', value: 'all' },
        status: [],
        dateRange: {
          from: null,
          to: null,
        },
        from: "",
        to: "",
      }
      this.dateRange = null
    },


    setFecha(val){
      if(val.length === 2){
        this.filters.dateRange.from = moment(val[0]).format('YYYY-MM-DD HH:mm:ss')
        this.filters.dateRange.to = moment(val[1]).format('YYYY-MM-DD HH:mm:ss')
      }
    },
    async fetchCallHistoryData({ page, filter, sort }) {
      try{
        let data = {
          pbx_service_id: this.$route.params.idPbxService,
          order_by: sort.fieldName || 'created_at',
          order: sort.order || 'desc',
          page,
          limit: 10,
          custom: 0,
          filters: this.filters,
        }

        this.searchLoading = true

        let response = await this.fetchCallHistory(data)
        this.callHistoryCount = response.data.response.totals.billed_cdr
        this.callHistoryTotal = response.data.response.totals.total_cost
        this.callHistoryTotalcustom = this.callHistoryTotal
        //console.log(this.callHistoryTotal)
        this.billed_cost = response.data.response.totals.total_cost
        this.inclusive_minutes_cosumed =
          response.data.response.totals.inclusive_minutes_consumed
        this.billed_cdr = response.data.response.totals.current.exclusive_cost
        this.billed_time = response.data.response.totals.current.exclusive_time

        this.AddmonthTotal = this.AddmonthTotal + this.callHistoryTotal
        this.cdrList = response.data.response.service_cdrs.data
        return {
          data: response.data.response.service_cdrs.data,
          pagination: {
            totalPages: response.data.response.service_cdrs.last_page,
            currentPage: page,
            count: response.data.response.service_cdrs.count,
          },
        }

      }catch(e){
        console.log(e)
      }finally{
        this.searchLoading = false
      }
    },

    reloadCdrTable() {
      this.$refs.cdr_table.refresh()
    },

    reloadExtTable() {
      this.$refs.ext_table.refresh()
    },
    async clearCdrType(removedOption, id) {
      this.filters.cdrType = ''
    },
    changeStatus(status) {
      if(status.includes(0)){
       const statusArr = this.cdrStatusOptions.map(item => item.value)
        this.filters.status = statusArr
      }
    },
  },
}
</script>

<style lang="scss">
.pbx-service-details {
  .table-foot {
    .table-total {
      min-width: 390px;
    }
  }
  @media (max-width: 480px) {
    .table-foot {
      .table-total {
        min-width: 384px;
      }
    }
  }
}

// Dropdown
.tab {
  overflow: hidden;
}
.tab-content-slide {
  max-height: 0;
  transition: all 0.5s;
}
input:checked + .tab-label .test {
  background-color: #000;
}
input:checked + .tab-label .test svg {
  transform: rotate(180deg);
  stroke: #fff;
}
input:checked + .tab-label::after {
  transform: rotate(90deg);
}
input:checked ~ .tab-content-slide {
  max-height: 400vh;
}
</style>
