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



                <div v-if="serviceStatus " >


<div v-if="serviceStatus.value=== 'A'" >

<sw-badge
:bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
:color="$utils.getBadgeStatusColor('COMPLETED').color"
class="px-3 py-1"
>
{{ $t('general.active') }}
</sw-badge>
</div>


<div v-if="serviceStatus.value === 'P'" >
<sw-badge
:bg-color="$utils.getBadgeStatusColor('VIEWED').bgColor"
:color="$utils.getBadgeStatusColor('VIEWED').color"
class="px-3 py-1"
>
{{ $t('general.pending') }}
</sw-badge>
</div>

<div v-if="serviceStatus.value == 'S'">
<sw-badge
:bg-color="$utils.getBadgeStatusColor('SENT').bgColor"
:color="$utils.getBadgeStatusColor('SENT').color"
class="px-3 py-1"
>
{{ $t('general.suspended') }}
</sw-badge>
</div>

<div v-if="serviceStatus.value == 'C' ">
<sw-badge
:bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
:color="$utils.getBadgeStatusColor('OVERDUE').color"
class="px-3 py-1"
>
{{ $t('general.cancelled') }}
</sw-badge>
</div>

</div>



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


                <div v-if="allowDiscount=== 'YES'" >

<sw-badge
          :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
          :color="$utils.getBadgeStatusColor('COMPLETED').color"
          class="px-3 py-1"
        >
        {{ $t('general.yes') }}
        </sw-badge>
</div>

<div v-if="allowDiscount=== 'NO'">
          <sw-badge
          :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
          :color="$utils.getBadgeStatusColor('OVERDUE').color"
          class="px-3 py-1"
        >
        {{ $t('general.not') }}
        </sw-badge>
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
                <div v-if="selectedPbxService" >
  <div v-if="selectedPbxService.pbx_package" >

    <div v-if="selectedPbxService.pbx_package.status_payment  == 'postpaid'" >
      {{ $t('packages.item.postpaid') }}
    </div>
    <div v-if="selectedPbxService.pbx_package.status_payment  == 'prepaid'" >
      {{ $t('packages.item.prepaid') }}
    </div>
  </div>
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
                <div v-if="selectedPbxService" >

  <div v-if="selectedPbxService.auto_suspension === 1" >

    <sw-badge
            :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
            :color="$utils.getBadgeStatusColor('COMPLETED').color"
            class="px-3 py-1"
          >
          {{ $t('general.active') }}
          </sw-badge>
  </div>


  <div v-if="selectedPbxService.auto_suspension != 1" >

    <sw-badge
            :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
            :color="$utils.getBadgeStatusColor('OVERDUE').color"
            class="px-3 py-1"
          >
          {{ $t('general.inactive') }}
          </sw-badge>
  </div>

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
                    <div v-if="package.extensions === 1" >

<sw-badge
          :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
          :color="$utils.getBadgeStatusColor('COMPLETED').color"
          class="px-3 py-1"
        >
        {{ $t('general.yes') }}
        </sw-badge>
</div>

<div v-if="package.extensions != 1">
          <sw-badge
          :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
          :color="$utils.getBadgeStatusColor('OVERDUE').color"
          class="px-3 py-1"
        >
        {{ $t('general.not') }}
        </sw-badge>

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

<div v-if=" package.did === 1" >

<sw-badge
          :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
          :color="$utils.getBadgeStatusColor('COMPLETED').color"
          class="px-3 py-1"
        >
        {{ $t('general.yes') }}
        </sw-badge>
</div>

<div v-if=" package.did  != 1">
          <sw-badge
          :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
          :color="$utils.getBadgeStatusColor('OVERDUE').color"
          class="px-3 py-1"
        >
        {{ $t('general.not') }}
        </sw-badge>
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
                    <div v-if="package.call_ratings === 1" >

<sw-badge
          :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
          :color="$utils.getBadgeStatusColor('COMPLETED').color"
          class="px-3 py-1"
        >
        {{ $t('general.yes') }}
        </sw-badge>
</div>

<div v-if="package.call_ratings  != 1">
          <sw-badge
          :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
          :color="$utils.getBadgeStatusColor('OVERDUE').color"
          class="px-3 py-1"
        >
        {{ $t('general.not') }}
        </sw-badge>
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
                    <div v-if=" package.modify_server=== 1" >

<sw-badge
          :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
          :color="$utils.getBadgeStatusColor('COMPLETED').color"
          class="px-3 py-1"
        >
        {{ $t('general.yes') }}
        </sw-badge>
</div>

<div v-if=" package.modify_server != 1">
          <sw-badge
          :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
          :color="$utils.getBadgeStatusColor('OVERDUE').color"
          class="px-3 py-1"
        >
        {{ $t('general.not') }}
        </sw-badge>
      </div>
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
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-9 xl:gap-8">
          <!-- Total Calls -->
          <router-link
            slot="item-title"
            class="
              relative
              flex
              justify-between
              p-2
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
                  text-sm
                  font-semibold
                  leading-tight
                  text-black
                  xl:text-2xl
                "
              >
                <span
                  v-html="
                    selectedPbxService
                      ? $utils.formatMoney(
                          selectedPbxService.total,
                          defaultCurrency
                        )
                      : ''"
                />
              </span>
              <span
                class="
                  block
                  mt-1
                  text-sm
                  leading-tight
                  text-gray-500
                  xl:text-lg"
              >
                {{ $t('customers.recurring_charge') }}
              </span>
            </div>
            <div class="flex items-center">
              <estimate-icon class="w-9 h-9 xl:w-12 xl:h-12" />
            </div>
          </router-link>

          <!-- Call Rating -->
          <router-link
            slot="item-title"
            class="
              relative
              flex
              justify-between
              p-2
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
                  text-sm
                  font-semibold
                  leading-tight
                  text-black
                  xl:text-2xl
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
              <contact-icon class="w-9 h-9 xl:w-12 xl:h-12" />
            </div>
          </router-link>

          <!-- Renew Date -->
          <router-link
            slot="item-title"
            class="
              relative
              flex
              justify-between
              p-2
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
              <invoice-icon class="w-9 h-9 xl:w-12 xl:h-12" />
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
              p-2
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
                  text-sm
                  font-semibold
                  leading-tight
                  text-black
                  xl:text-2xl
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
              <dollar-icon class="w-9 h-9 xl:w-12 xl:h-12" />
            </div>
          </router-link>
        </div>
        <div class="relative flex flex-wrap bg-gray-200 mt-4 rouded-lg p-4">
            <sw-input-group :label="$t('general.date_range')" class="mt-2 md:w-1/2 md:pr-2">
              <!-- <sw-date-picker
                v-show="true"
                ref="dateRangePicker"
                v-model="dateRange"
                :config="configDatepicker"
                @on-change="setFecha"
                :disabled="false"
                :invalid="false"
                :name="null"
                :tabindex="null"
              /> -->

              <!-- <VueCtkDateTimePicker
                only-date
                :range="true"
                color="#5953D8"
                noButton
                no-header
                v-model="dateRange"
                :locale="$i18n.locale"
                :no-value-to-custom-elem="true"
                format="YYYY-MM-DD_HH:mm:ss"
                :custom-shortcuts="customShortcuts"
               >
                <sw-input  type="text" :value="`${formatDate(dateRange)}`" @blur="DataRangeInput"></sw-input>
               </VueCtkDateTimePicker> -->
               <date-range-picker
                class="w-full rounded date-range-picker"
                v-model="dateRange"
                time-picker
                :time-picker-increment="5"
                time-picker24-hour
                append-to-body
                :ranges="rangesPresets"
               >
                <template v-slot:input="picker">
                    {{ formatDate(picker) }}
                </template>
                <div slot="footer" slot-scope="data" class="slot flex mb-1 mr-1">
                  <div class="ml-auto flex items-center ">
                    <sw-button
                      @click="data.clickApply"
                      v-if="!data.in_selection"
                      :variant="'primary-outline'"
                      size="sm"
                      class="ml-2"
                    >
                      {{ $t('general.apply') }}
                    </sw-button>

                  </div>
                </div>
               </date-range-picker>

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
              <sw-input v-model="filters.id">ay
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
              />
            </sw-input-group>

            <sw-input-group :label="$t('general.cdr_category')" class="mt-2 md:w-1/4 md:pr-2">
              <sw-select
                v-model="filters.type_custom"
                :options="cdrCategoryOptions"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('general.cdr_category')"
                :allow-empty="false"
                track-by="value"
                label="name"
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
                  v-model="limit"
                  :options="pagesOptions"
                  :group-select="false"
                  :allow-empty="false"
                />
              </sw-input-group>
            </div>

            <div>

              <download-csv
                    :data="cdrListExportCsv"
                    :fields="csvFields"
                    :name="`Service_${this.selectedPbxService.pbx_services_number}-${ this.dateNow}.csv`"
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

          <sw-table-component
            v-if="showTableCallHistory"
            ref="cdr_table"
            :show-filter="false"
            :data="fetchCallHistoryData"
            table-class="table"
            class="w-full"
          >

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

            <!-- DESDE AQUI -->
            <sw-table-column
              :sortable="true"
              :label="$tc('corePbx.dashboard.status')"
              show="status"
            />

            <sw-table-column
              :sortable="true"
              :label="$tc('corePbx.dashboard.unique_id')"
               show="unique_id"
            />
            <!-- HASTA AQUI -->
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
                  {{ $t('pbx_services.total_cdr') }}
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
              <!-- <div class="flex items-center justify-between w-full">
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
              </div> -->

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
                  {{ $t('pbx_services.total_time') }}
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
                  {{ $t('pbx_services.total_cost') }}:
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

import VueCtkDateTimePicker from 'vue-ctk-date-time-picker'
import 'vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css'

import DateRangePicker from 'vue2-daterange-picker'
//you need to import the CSS manually
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

export default {
  components: {
    DollarIcon,
    ContactIcon,
    InvoiceIcon,
    EstimateIcon,
    PencilIcon,
    HashtagIcon,
    SwDatePicker,
    downloadCsv: JsonCSV,
    VueCtkDateTimePicker,
    DateRangePicker,
  },

  data() {
    let today = new Date()
    today.setHours(0, 0, 0, 0)

    let todayEnd = new Date()
    todayEnd.setHours(23, 59, 59, 999)

    let yesterday = new Date()
    yesterday.setDate(today.getDate() - 1)
    yesterday.setHours(0, 0, 0, 0)

    return {
      showTableCallHistory: false,
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
        { name: 'All', value: 3 },
        { name: 'Inbound', value: 2 },
        { name: 'Outbound', value: 1 },
      ],
      paidOptions: [
        { name: 'All', value: 'all' },
        { name: 'Pending', value: 'pending' },
        { name: 'Billed', value: 'billed' },
      ],
      cdrStatusOptions: [
        { label: 'Answered', value: 'Answered', color: 'success' },
        { label: 'Not Answered', value: 'Not Answered', color: 'warning' },
        { label: 'Busy', value: 'Busy', color: 'danger' },
        { label: 'Error', value: 'Error', color: 'danger' },
      ],
      cdrStatusOptionsClass: {
        8: 'Answered',
        4: 'Not Answered',
        2: 'Busy',
        1: 'Error',
      },
      cdrCategoryOptions: [
        { name: 'All', value: 3 },
        { name: 'Only CDR', value: 1 },
        { name: 'Only Custom', value: 2 },
      ],
      dateRange: {
        startDate: '',
        endDate: '',
      },
      filters: {
        number: '',
        id: '',
        cdrType: { name: 'All', value: 3 },
        paid: { name: 'All', value: 'all' },
        status: [],
        from: '',
        to: '',
        type_custom: { name: 'All', value: 3 },
      },
      limit: 10,
      pagesOptions: [10, 25, 50, 100],
      configDatepicker: {
        mode: 'range',
        enableTime: true,
        enable: [],
        defaultDate: [
          moment().subtract(1, 'month').format('YYYY-MM-DD'),
          moment().format('YYYY-MM-DD'),
        ],
        time_24hr: true,
      },
      selectedHistoryCall: [],
      selectAllFieldStatus: false,
      cdrList: [],
      cdrListExportCsv: [],
      csvFields: [
        'from',
        'to',
        'dateTime',
        'type',
        'totalDuration',
        'ratingDuration',
        'cost',
        'unique_id',
        'status',
      ],
      nameCsvCallHistory: 'call_history.csv',
      dateNow: moment().format('YYYY-MM-DD'),
      // Today
      // Yesterday
      // Last 7 days
      // Last 30 days
      // Last 90 days
      // This month
      // Last month
      rangesPresets: {
        Today: [
          moment().startOf('day').toDate(),
          moment().endOf('day').toDate(),
        ],
        Yesterday: [
          moment().subtract(1, 'days').startOf('day').toDate(),
          moment().subtract(1, 'days').endOf('day').toDate(),
        ],
        'Last 7 days': [
          moment().subtract(6, 'days').startOf('day').toDate(),
          moment().endOf('day').toDate(),
        ],
        'Last 30 days': [
          moment().subtract(29, 'days').startOf('day').toDate(),
          moment().endOf('day').toDate(),
        ],
        'Last 90 days': [
          moment().subtract(89, 'days').startOf('day').toDate(),
          moment().endOf('day').toDate(),
        ],
        'This month': [
          moment().startOf('month').toDate(),
          moment().endOf('month').toDate(),
        ],
        'Last month': [
          moment().subtract(1, 'month').startOf('month').toDate(),
          moment().subtract(1, 'month').endOf('month').toDate(),
        ],
      },
      customShortcuts: [
        { label: `Today`, value: 'day', isSelected: false },
        { label: 'Yesterday', value: '-day', isSelected: false },
        { label: 'Last 7 days', value: 7, isSelected: false },
        { label: 'Last 30 days', value: 30, isSelected: false },
        { label: 'Last 90 days', value: 90, isSelected: false },
        { label: 'This month', value: 'month', isSelected: false },
        { label: 'Last month', value: '-month', isSelected: false },
      ],
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
    labelStatusCrp() {
      const statusLabelArr = this.filters.status.map((item) => {
        return this.cdrStatusOptions.find((status) => status.value === item)
          .label
      })
      // si inlcluye en 0 muestra All
      if (statusLabelArr.includes('ALL')) {
        return 'All'
      } else {
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
    this.getCallHistory()
  },
  watch: {
    selectedPbxService(newVal) {
      this.$emit('status', this.serviceStatus.value)
    },
    limit() {
      this.reloadCdrTable()
    },
  },
  methods: {
    ...mapActions('pbxService', [
      'fetchPbxService',
      'fetchExtensions',
      'fetchDIDs',
      'fetchItemsPbxService',
      'fetchAdditionalCharges',
      'fetchCallHistorySearch',
      'fetchCommandos',
      'fetchPrepaidCharges',
      'fetchCallHistory',
    ]),

    async loadService() {
      let response = await this.fetchPbxService(this.$route.params.idPbxService)
      this.package = response.data.response.pbx_service.pbx_package

      // Extraer los cdr status para el filtro
      this.cdrStatusExtract(this.package)

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

      this.total_consume = parseFloat(this.selectedPbxService.total_consume)
      this.paid_consume = parseFloat(this.selectedPbxService.paid_consume)
      this.unpaid_consume = parseFloat(this.selectedPbxService.unpaid_consume)

      this.total_deb = parseFloat(this.selectedPbxService.total_deb)

      // contar diferencias de dias en moment js
      const today = moment()
      let end = moment(this.selectedPbxService.date_begin)
      let diff = end.diff(today, 'days')
      diff = diff * -1
      let from
      if (diff > 90) {
        // Hora 00.00.00
        from = moment().subtract(90, 'days')
      } else {
        from = moment().subtract(diff, 'days')
      }
      this.dateRange = {
        startDate: from,
        endDate: today,
      }
      this.showTableCallHistory = true
    },
    cdrStatusExtract(packageService) {
      // create function filtrar los cdr status
      const arrKeyCdrStatusClass = Object.keys(this.cdrStatusOptionsClass).map(
        Number
      )

      function filterStatus(cdr_status) {
        return cdr_status.filter((item) =>
          arrKeyCdrStatusClass.includes(item.status)
        )
      }

      const cdrStatusPackage = filterStatus(packageService.cdr_status)
      const cdrStatusServer = filterStatus(packageService.server.cdr_status)

      if (cdrStatusPackage.length > 0) {
        this.filters.status = cdrStatusPackage.map((item) => {
          return this.cdrStatusOptionsClass[item.status]
        })
      } else if (cdrStatusServer.length > 0) {
        this.filters.status = cdrStatusServer.map((item) => {
          return this.cdrStatusOptionsClass[item.status]
        })
      }
    },
    selectAllHistoryCall(val) {
      this.selectAllFieldStatus = val
      this.selectedHistoryCall = val
        ? this.cdrList.map((item) => item.unique_id)
        : []
    },
    selectHistoryCall(val) {
      if (val.length === this.cdrList.length) {
        this.selectAllFieldStatus = true
      } else {
        this.selectAllFieldStatus = false
      }
      this.selectedHistoryCall = val
    },
    searchMetho() {
      this.reloadCdrTable()
    },
    async exportAllFiltersCsv({ page }) {
      try {
        this.exportexportAllFiltersCsvLoading = true

        let data = {
          pbx_service_id: this.$route.params.idPbxService,
          order_by: 'created_at',
          order: 'desc',
          page: page ? page : 1,
          custom: 0,
          ...this.filters,
          limit: this.limit,
          cdrType: this.filters.cdrType.value,
          paid: this.filters.paid.value,
          type_custom: this.filters.type_custom.value,
          status:
            this.filters.status.length > 0 ? this.filters.status.join(',') : '',
          start_date: moment(this.dateRange.startDate).format(
            'YYYY-MM-DD_HH:mm:ss'
          ),
          end_date: moment(this.dateRange.endDate).format(
            'YYYY-MM-DD_HH:mm:ss'
          ),
        }
        data.limit = 50000

        let response = await this.fetchCallHistorySearch(data)
        const datosCdrList = response.data.response.cdr.data

        const last_page = response.data.response.cdr.last_page

        const dataPush = datosCdrList.map((item) => {
          return {
            from: item.from,
            to: item.to,
            dateTime: item.formatted_start_date,
            type: item.type ? 'Outbound' : 'Inbound',
            totalDuration: item.formatted_duration,
            ratingDuration: item.formatted_round_duration,
            cost: item.total_exclusive_cost,
            unique_id: item.unique_id,
            status: item.status,
          }
        })

        this.cdrListExportCsv.push(...dataPush)

        // this.nameCsvCallHistory = `Service_${this.selectedPbxService.pbx_services_number}-${ this.dateNow} - ${data.page} of ${last_page} .csv`

        // dilay para que se cargue la tabla antes de exportar
        setTimeout(() => {
          if (last_page > data.page) {
            this.exportAllFiltersCsv({ page: data.page + 1 })
          } else {
            this.$refs.downloadCsv.generate()
            this.cdrListExportCsv = []
          }
        }, 1000)
        // this.cdrListExportCsv = this.cdrList.filter( item => this.selectedHistoryCall.includes(item.unique_id))
      } catch (error) {
      } finally {
        this.exportexportAllFiltersCsvLoading = false
      }
    },

    clearFilter() {
      this.filters = {
        number: '',
        id: '',
        cdrType: { name: 'All', value: 3 },
        paid: { name: 'All', value: 'all' },
        type_custom: { name: 'All', value: 3 },
        status: [],
        from: '',
        to: '',
      }
    },

    DataRangeInput(val) {
      const start = val.split(' - ')[0]
      const end = val.split(' - ')[1]
      this.dateRange = {
        startDate: moment(start).format('YYYY-MM-DD_HH:mm'),
        endDate: moment(end).format('YYYY-MM-DD_HH:mm'),
      }
    },

    formatDate(dateRange) {
      const start = moment(dateRange.startDate).format('MM/DD/YYYY HH:mm')
      const end = moment(dateRange.endDate).format('MM/DD/YYYY HH:mm')
      return `${start} - ${end}`
    },
    async fetchCallHistoryData({ page, filter, sort }) {
      try {
        let data = {
          pbx_service_id: this.$route.params.idPbxService,
          order_by: sort.fieldName || 'created_at',
          order: sort.order || 'desc',
          page,
          custom: 0,
          ...this.filters,
          limit: this.limit,
          cdrType: this.filters.cdrType.value,
          paid: this.filters.paid.value,
          type_custom: this.filters.type_custom.value,
          status:
            this.filters.status.length > 0 ? this.filters.status.join(',') : '',
          start_date: moment(this.dateRange.startDate).format(
            'YYYY-MM-DD_HH:mm:ss'
          ),
          end_date: moment(this.dateRange.endDate).format(
            'YYYY-MM-DD_HH:mm:ss'
          ),
        }

        this.searchLoading = true
        let response = await this.fetchCallHistorySearch(data)
        const totalCost = parseFloat(response.data.response.total.cost)
        this.callHistoryCount = response.data.response.total.quantity
        this.callHistoryTotal = totalCost
        this.callHistoryTotalcustom = this.callHistoryTotal
        this.billed_cost = totalCost
        // this.inclusive_minutes_cosumed = response.data.response.total.time
        this.billed_cdr = totalCost
        this.billed_time = response.data.response.total.time

        this.cdrList = response.data.response.cdr.data

        return {
          data: response.data.response.cdr.data,
          pagination: {
            totalPages: response.data.response.cdr.last_page,
            currentPage: page,
            count: response.data.response.cdr.count,
          },
        }
      } catch (e) {
      } finally {
        this.searchLoading = false
      }
    },

    async getCallHistory() {
      // carga de minutos inclusivos global
      //  metodo aparte
      let data = {
        pbx_service_id: this.$route.params.idPbxService,
        order_by: 'created_at',
        order: 'desc',
        limit: 10,
        custom: 0,
      }

      let response = await this.fetchCallHistory(data)
      this.inclusive_minutes_cosumed =
        response.data.response.totals.inclusive_minutes_consumed
    },

    reloadCdrTable() {
      this.$refs.cdr_table.refresh()
    },

    reloadExtTable() {
      this.$refs.ext_table.refresh()
    },
    changeStatus(status) {
      if (status.includes(0)) {
        const statusArr = this.cdrStatusOptions.map((item) => item.value)
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
