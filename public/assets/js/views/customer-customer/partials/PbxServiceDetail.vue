<template>
  <div class="pbx-service-details">
    <sw-divider class="my-6" />

    <p class="text-gray-500 uppercase sw-section-title">
      {{ $t('customers.pbxservices_resume') }}
    </p>

    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />

    <div class="grid grid-cols-12">
      <div class="col-span-12">
        <div class="mt-8">
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
          <!--  <div>
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
            </div> -->
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
      <!--      <div>
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
            </div> -->
          </div>

          <sw-divider class="my-8" />

          <!------------------- PBXWARE INFO ------------------->
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
                  $ {{  this.callHistoryTotal.toFixed(2) }}
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
                        selectedViewCustomer.customer.balance
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

          <!------------------- EXTENSIONS ------------------->
          <div class="tabs mb-5 grid col-span-12 pt-8">
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
                    {{ $t('corePbx.dashboard.extensions') }}
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

                  <sw-table-component
                    ref="ext_table"
                    :show-filter="false"
                    :data="fetchExtensionsData"
                    table-class="table"
                  >
                    <sw-table-column
                      :sortable="true"
                      :label="$t('pbx_services.name')"
                      show="name"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.number')"
                      show="ext"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.location')"
                      show="location"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.status')"
                      show="status"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.pro_ext_name')"
                      show="profile_name"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.pro_ext_price')"
                      show="profile_rate"
                    >
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.pro_ext_price') }}</span>
                        <!--<div
                                                    v-html="$utils.formatMoney(row.profile_rate, defaultCurrency)"
                                                />-->
                        <span>{{
                          defaultCurrency.symbol +
                          ' ' +
                          row.profile_rate.toFixed(2)
                        }}</span>
                      </template>
                    </sw-table-column>

                    <!-- <sw-table-column
                                            :sortable="true"
                                            :label="$tc('pbx_services.call_rating')"
                                            show="call_rating"
                                        >
                                            <template slot-scope="row">
                                                <span>{{ $tc('pbx_services.call_rating') }}</span>
                                                <span>{{ 0 }}</span>
                                            </template>
                                        </sw-table-column> -->

                    <sw-table-column
                      :sortable="false"
                      :filterable="false"
                      cell-class="action-dropdown no-click"
                    >
                      <template slot-scope="row">
                        <span>{{ $t('general.actions') }}</span>

                        <sw-dropdown>
                          <dot-icon slot="activator" />

                          <sw-dropdown-item
                            @click="openUpdateExtModal(row)"
                          >
                            <pencil-icon class="h-5 mr-3 text-gray-600" />
                            {{ $t('general.edit') }}
                          </sw-dropdown-item>
                        </sw-dropdown>
                      </template>
                    </sw-table-column>
                  </sw-table-component>

                  <!------------  TOTALS  ------------>
                  <div
                    class="
                      block
                      my-10
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
                      <!------- COUNT ------->
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
                          {{ $t('pbx_services.count') }}
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
                          <span>{{ this.extensionCount }}</span>
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
                          {{ $t('pbx_services.total') }}
                          {{ $t('pbx_services.amount') }}:
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
                          <span>{{
                            defaultCurrency.symbol+
                            ' ' +
                            this.extensionTotal.toFixed(2)
                          }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!--------------------- DIDs ----------------------->
          <div class="tabs mb-5 grid col-span-12 pt-6">
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
                    {{ $t('corePbx.dashboard.did') }}
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

                  <sw-table-component
                    ref="table"
                    :show-filter="false"
                    :data="fetchDIDsData"
                    table-class="table"
                  >
                    <sw-table-column
                      :sortable="true"
                      :label="$t('pbx_services.did_channel')"
                      show="number"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.destination')"
                      show="ext"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.type')"
                      show="type"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.pro_ext_name2')"
                      show="profile_name"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.pro_ext_price2')"
                      show="profile_rate"
                    >
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.pro_ext_price2') }}</span>
                        <!--  <div
                                                    v-html="$utils.formatMoney(row.profile_rate, defaultCurrency)"
                                                /> -->
                        <span>{{
                          defaultCurrency.symbol +
                          ' ' +
                          row.profile_rate
                        }}</span>
                      </template>
                    </sw-table-column>

                    <!-- <sw-table-column
                                            :sortable="true"
                                            :label="$tc('pbx_services.call_rating')"
                                            show="call_rating"
                                        >
                                            <template slot-scope="row">
                                                <span>{{ $tc('pbx_services.call_rating') }}</span>
                                                <span>{{ 0 }}</span>
                                            </template>
                                        </sw-table-column> -->

                    <sw-table-column
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
                    </sw-table-column>
                  </sw-table-component>

                  <!------------  TOTALS  ------------>
                  <div
                    class="
                      block
                      my-10
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
                      <!------- COUNT ------->
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
                          {{ $t('pbx_services.count') }}
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
                          <span>{{ this.didCount }}</span>
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
                          {{ $t('pbx_services.total') }}
                          {{ $t('pbx_services.amount') }}:
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
                          <!--<div v-html="$utils.formatMoney(this.didTotal, defaultCurrency)" />-->
                          <span>{{
                            defaultCurrency.symbol +
                            ' ' +
                            this.didTotal.toFixed(2)
                          }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!--------------------- ITEMS ----------------------->
          <div class="tabs mb-5 grid col-span-12 pt-6">
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
                    {{ $t('corePbx.items') }}
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

                  <sw-table-component
                    ref="table"
                    :show-filter="false"
                    :data="fetchItemsData"
                    table-class="table"
                  >
                    <sw-table-column
                      :sortable="true"
                      :label="$t('pbx_services.name')"
                      show="name"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.description')"
                      show="description"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.quantity')"
                      show="quantity"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.discount')"
                      show="discount"
                    >
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.discount') }}</span>
                        <div class="flex flex-auto" role="group">
                          <span>{{ row.discount }}</span>
                          <span class="flex">
                            {{
                              row.discount_type === 'fixed'
                                ? defaultCurrency.symbol
                                : '%'
                            }}
                          </span>
                        </div>
                      </template>
                    </sw-table-column>

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.total')"
                      show="total"
                    >
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.total') }}</span>
                        <span>
                          <div
                            v-html="
                              $utils.formatMoney(row.total, defaultCurrency)
                            "
                          />
                        </span>
                      </template>
                    </sw-table-column>

                    <sw-table-column
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
                    </sw-table-column>
                  </sw-table-component>

                  <!------------  TOTALS  ------------>
                  <div
                    class="
                      block
                      my-10
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
                      <!------- COUNT ------->
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
                          {{ $t('pbx_services.count') }}
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
                          <span>{{ this.itemCount }}</span>
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
                          {{ $t('pbx_services.total') }}
                          {{ $t('pbx_services.amount') }}:
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
                          <div
                            v-html="
                              $utils.formatMoney(
                                this.itemTotal,
                                defaultCurrency
                              )
                            "
                          />
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!--------------- ADDITIONAL CHARGES --------------->
          <div class="tabs mb-5 grid col-span-12 pt-6">
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
                    {{ $t('corePbx.charges') }}
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

                  <sw-table-component
                    ref="table"
                    :show-filter="false"
                    :data="fetchAdditionalChargesData"
                    table-class="table"
                  >
                    <sw-table-column
                      :sortable="true"
                      :label="$t('pbx_services.name')"
                      show="description"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.price')"
                      show="amount"
                    >
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.price') }}</span>
                        <!--<div
                                                    v-html="$utils.formatMoney(row.amount, defaultCurrency)"
                                                />-->
                        <span>{{
                         defaultCurrency.symbol + ' ' + row.amount
                        }}</span>
                      </template>
                    </sw-table-column>

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.type_from')"
                      show="type_from"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.profile_name')"
                      show="profile_name"
                    />

                    <sw-table-column
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
                    </sw-table-column>
                  </sw-table-component>

                  <!------------  TOTALS  ------------>
                  <div
                    class="
                      block
                      my-10
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
                      <!------- COUNT ------->
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
                          {{ $t('pbx_services.count') }}
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
                          <span>{{ this.chargesCount }}</span>
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
                          {{ $t('pbx_services.total') }}
                          {{ $t('pbx_services.amount') }}:
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
                          <span>{{
                            defaultCurrency.symbol+
                            ' ' +
                            this.chargesTotal.toFixed(2)
                          }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!------------------ CALL HISTORY ------------------>
          <div class="tabs mb-5 grid col-span-12 pt-6">
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
                    {{ $t('corePbx.dashboard.call_history') }}
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

                  <sw-table-component
                    ref="table"
                    :show-filter="false"
                    :data="fetchCallHistoryData"
                    table-class="table"
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

                   <!--  <sw-table-column
                      :sortable="true"
                      :label="$tc('corePbx.dashboard.totald')"
                      show="formatted_duration"
                    /> -->

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
                        <!---------------     <div
                                                    v-html="$utils.formatMoney(row.cost, defaultCurrency)"
                                                />  --------------->

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
                            {{ row.exclusive_cost }}
                          </div>
                        </div>
                      </template>
                    </sw-table-column>

                    <sw-table-column
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
                    </sw-table-column>
                  </sw-table-component>

                  <!------------  TOTALS  ------------>
                  <div
                    class="
                      block
                      my-10
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
                          <span>{{ this.billed_cdr }}</span>
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

          <!--------------- INTERNATIONAL CALLS --------------->
          <!--------------- 
          <div class="tabs mb-5 grid col-span-12 pt-6">
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
                    {{ $t('corePbx.dashboard.international_calls') }}
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

                  <sw-table-component
                    ref="table"
                    :show-filter="false"
                    :data="[]"
                    table-class="table"
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
                      show="start_date"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('corePbx.dashboard.totald')"
                      show="duration"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.amount')"
                      show="cost"
                    >
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.amount') }}</span>
                        <div
                          v-html="$utils.formatMoney(row.cost, defaultCurrency)"
                        />
                      </template>
                    </sw-table-column>

                    <sw-table-column
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
                    </sw-table-column>
                  </sw-table-component>
                </div>
              </div>
            </div>
          </div>

          --------------->

          <!------------------ CUSTOM DESTINATION ------------------>
          <div class="tabs mb-5 grid col-span-12 pt-6">
            <div class="border-b tab">
              <div class="relative">
                <input
                  class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
                  type="checkbox"
                />
                <header
                  class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label"
                >
                  <span class="text-gray-500 uppercase sw-section-title">
                    {{ $t('pbx_services.custom_destinations') }}
                  </span>
                  <div
                    class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test"
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

                  <sw-table-component
                    ref="table"
                    :show-filter="false"
                    :data="fetchCustomDestinationData"
                    table-class="table"
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

                    <!-- <sw-table-column
                      :sortable="true"
                      :label="$tc('corePbx.dashboard.totald')"
                      show="formatted_duration"
                    /> -->

                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.rate')"
                      show="rate"
                                      >
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.amount') }}</span>
                        <!-- <div v-html="$utils.formatMoney(row.cost, defaultCurrency)"/> -->

                          <div
                           v-if="row.custom_rate != null"
                          >
                            {{ row.custom_rate.rate_per_minute }}
                          </div>
                      </template>
                    </sw-table-column>

                    

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
                        <!-- <div v-html="$utils.formatMoney(row.cost, defaultCurrency)"/> -->

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
                            {{ row.exclusive_cost }}
                          </div>
                        </div>
                      </template>
                    </sw-table-column>

                    <sw-table-column
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
                    </sw-table-column>

                  </sw-table-component>

                  <!------------  TOTALS  ------------>
                  <div
                    class="block my-10 table-foot lg:justify-between lg:flex lg:items-start"
                  >
                    <div class="w-full lg:w-1/2"></div>

                    <div
                      class="px-5 py-4 mt-6 bg-white border border-gray-200 border-solid rounded table-total lg:mt-0"
                    >
                      <!------- COUNT ALL CDR------->
                      <div class="flex items-center justify-between w-full">
                        <label
                          class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                        >
                          {{ $t('pbx_services.all_cdr') }}
                        </label>
                        <label
                          class="flex items-center justify-center m-0 text-lg text-black uppercase"
                        >
                          <span>{{ this.customDestinationCount }}</span>
                        </label>
                      </div>

                      <!------- COUNT ALL CDR------->
                      <div class="flex items-center justify-between w-full">
                        <label
                          class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                        >
                          {{ $t('pbx_services.billed_cdr') }}
                        </label>
                        <label
                          class="flex items-center justify-center m-0 text-lg text-black uppercase"
                        >
                          <span>{{ this.ctmDestBilledCDR }}</span>
                        </label>
                      </div>

                      <!------- COUNT ALL CDR------->
                      <div class="flex items-center justify-between w-full">
                        <label
                          class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                        >
                          {{ $t('pbx_services.billed_time') }}
                        </label>
                        <label
                          class="flex items-center justify-center m-0 text-lg text-black uppercase"
                        >
                          <span>{{ this.ctmDestBilledTime }}</span>
                        </label>
                      </div>

                      <!------- AMOUNT ------->
                      <div
                        class="flex items-center justify-between w-full pt-2 mt-5 border-t border-gray-200 border-solid"
                      >
                        <label
                          class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                        >
                          {{ $t('pbx_services.billed_cost') }}:
                        </label>
                        <label
                          class="flex items-center justify-center text-lg uppercase text-primary-400"
                        >
                          <!--<div v-html="$utils.formatMoney(this.extensionTotal, defaultCurrency)" />-->
                          <span> $ {{ this.customDestinationTotal.toFixed(2) }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
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
import { selectedViewCustomer } from '../../../store/modules/customer/getters'
import { PencilIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    DollarIcon,
    ContactIcon,
    InvoiceIcon,
    EstimateIcon,
    PencilIcon,
  },

  data() {
    return {
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
      callHistoryCount: 0,
      billed_cost: 0,
      billed_cdr: 0,
      inclusive_minutes_cosumed: 0,
      billed_time: 0,
      customDestinationTotal: 0,
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

    simbol() {
      if(this.defaultCurrency){
        return this.defaultCurrency
      }
      return '$'
    },

    allowDiscount() {
      return this.selectedPbxService.allow_discount === 1 ? 'YES' : 'NO'
    },

    getStatusPayment() {
      return this.selectedViewCustomer && this.selectedViewCustomer.customer
        ? this.selectedViewCustomer.customer.status_payment
        : ''
    },
  },

  created() {
    window.hub.$on('updateExt', this.reloadExtTable)
    this.loadService()
  },
  watch: {
    selectedPbxService(newVal) {
      this.$emit('status', this.serviceStatus.value)
    },
  },
  methods: {
    ...mapActions('pbxService', [
      'fetchPbxService',
      'fetchExtensions',
      'fetchDIDs',
      'fetchItemsPbxService',
      'fetchAdditionalCharges',
      'fetchCallHistory',
    ]),

    ...mapActions('modal', ['openModal']),

    async loadService() {
      let response = await this.fetchPbxService(
        this.$route.params.pbx_service_id
      )
      this.package = response.data.response.pbx_service.pbx_package
    },

    async fetchExtensionsData({ page, filter, sort }) {
      let data = {
        pbx_service_id: this.$route.params.pbx_service_id,
        page,
        limit: 10,
      }

      let response = await this.fetchExtensions(data)

      this.extensionCount = response.data.response.totals.extension
      this.extensionTotal = response.data.response.totals.cost

      return {
        data: response.data.response.service_extensions.data,
        pagination: {
          totalPages: response.data.response.service_extensions.last_page,
          currentPage: page,
          count: response.data.response.service_extensions.count,
        },
      }
    },

    async fetchDIDsData({ page, filter, sort }) {
      let data = {
        pbx_service_id: this.$route.params.pbx_service_id,
        page,
        limit: 10,
      }

      let response = await this.fetchDIDs(data)

      this.didCount = response.data.response.totals.did
      this.didTotal = response.data.response.totals.cost
     

      return {
        data: response.data.response.service_did.data,
        pagination: {
          totalPages: response.data.response.service_did.last_page,
          currentPage: page,
          count: response.data.response.service_did.count,
        },
      }
    },

    async fetchItemsData({ page, filter, sort }) {
      let data = {
        pbx_service_id: this.$route.params.pbx_service_id,
        page,
        limit: 10,
      }

      let response = await this.fetchItemsPbxService(data)

      this.itemTotal = response.data.response.totals.total_amount
      this.itemCount = response.data.response.totals.count

      return {
        data: response.data.response.service_items.data,
        pagination: {
          totalPages: response.data.response.service_items.last_page,
          currentPage: page,
          count: response.data.response.service_items.count,
        },
      }
    },

    async fetchAdditionalChargesData({ page, filter, sort }) {
      let data = {
        pbx_service_id: this.$route.params.pbx_service_id,
        page,
        limit: 10,
      }
      let response = await this.fetchAdditionalCharges(data)

      this.chargesTotal = response.data.total_amount
      this.chargesCount = response.data.count

      let charges = response.data.charges.data.map((charge) => {
        return {
          ...charge,
          type_from: charge.profile_did_id ? 'DID' : 'Extension',
        }
      })
      return {
        data: charges,
        pagination: {
          totalPages: response.data.charges.last_page,
          currentPage: page,
          count: response.data.charges.count,
        },
      }
    },

    async fetchCallHistoryData({ page, filter, sort }) {
      let data = {
        pbx_service_id: this.$route.params.pbx_service_id,
        page,
        limit: 10,
      }

      let response = await this.fetchCallHistory(data)
     
      this.callHistoryCount = response.data.response.totals.billed_cdr
      this.callHistoryTotal = response.data.response.totals.billed_cost
      //console.log(this.callHistoryTotal)
      this.billed_cost = response.data.response.totals.total_cost
      this.inclusive_minutes_cosumed =
        response.data.response.totals.inclusive_minutes_consumed
      this.billed_cdr = response.data.response.totals.billed_cdr
      this.billed_time = response.data.response.totals.billed_time

      return {
        data: response.data.response.service_cdrs.data,
        pagination: {
          totalPages: response.data.response.service_cdrs.last_page,
          currentPage: page,
          count: response.data.response.service_cdrs.count,
        },
      }
    },

    async fetchCustomDestinationData({ page, filter, sort }) {
      let data = {
        pbx_service_id: this.$route.params.pbx_service_id,
        page,
        limit: 10,
        only_custom: 1
      }

      let response = await this.fetchCallHistory(data)
      let values = response.data.response

      this.customDestinationCount = values.totals.billed_cdr
      this.customDestinationTotal = values.totals.billed_cost
      this.ctmDestBilledCost = values.totals.total_cost
      this.ctmDestBilledIncMin = values.totals.inclusive_minutes_consumed
      this.ctmDestBilledCDR = values.totals.billed_cdr
      this.ctmDestBilledTime = values.totals.billed_time

      return {
        data: values.service_cdrs.data,
        pagination: {
          totalPages: values.service_cdrs.last_page,
          currentPage: page,
          count: values.service_cdrs.count,
        },
      }
    },

    dateConvert(val) {
      return moment(val).format('YYYY-MM-DD HH:mm:ss')
    },

    openUpdateExtModal(extension) {
      this.openModal({
        title: this.$t('pbx_services.edit_extension'),
        componentName: 'UpdateExtensionModal',
        id: extension.id,
        data: extension,
      })
    },

    reloadExtTable() {
      this.$refs.ext_table.refresh()
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
