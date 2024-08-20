<template>
  <div
    class="
      w-full
      mb-8
      bg-white
      border border-gray-200 border-solid
      rounded
      p-8
      relative
      package-details
    "
  >
    <div class="heading-section">
      <p class="text-2xl not-italic font-semibold leading-7 text-black">
        {{ $t('customers.pbxservices_resume') }}
      </p>
    </div>
    <br />
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <br />

    <div class="grid grid-cols-12">
      <div class="col-span-12">
        <div class="mt-8" v-if="this.parameters">
          <!-- header resume -->
          <div
            class="
              grid grid-cols-1
              gap-4
              mt-5
              lg:grid-cols-3
              md:grid-cols-2
              sm:grid-cols-1"
          >
            <div>
              <p
                class="
                  mb-1text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('packages.name') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  this.parameters
                    ? this.parameters.package.pbx_package_name
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
                {{
                  this.parameters
                    ? this.parameters.status === 'A'
                      ? 'Active'
                      : 'Disabled'
                    : 'Not available'
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
                {{ $t('customers.term') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ this.parameters ? this.parameters.term : '' }}
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
                {{ $t('invoices.discount') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  this.parameters && this.parameters.allow_discount
                    ? 'Yes'
                    : 'None'
                }}
              </p>
            </div>

            <div>
              <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                {{ $t('customers.date_begin') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  this.parameters && this.parameters.date_begin.length > 0
                    ? this.parameters.date_begin
                    : 'None'
                }}
              </p>
            </div>
            <div v-if="isEdit">
                <p
                  class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
                >
                  {{ $t('customers.date_renow') }}
                </p>
                <p class="text-sm font-bold leading-5 text-black non-italic">
                  {{
                    this.parameters.renewal_date.length > 0
                      ? this.parameters.renewal_date
                      : 'None'
                  }}
                </p>
              </div>
            <!-- server -->
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
                {{ $t('customers.server') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  this.parameters.package.server.server_label.length > 0
                    ? this.parameters.package.server.server_label
                    : 'None'
                }}
              </p>
            </div>

            <!-- tenant -->
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
                {{ $t('customers.tenant') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  this.parameters.tenant.name.length > 0
                    ? this.parameters.tenant.name
                    : 'None'
                }}
              </p>
            </div>

                <!-- type service -->
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
                {{ $t('customers.type_service') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  this.parameters.package.status_payment.length > 0
                    ? this.parameters.package.status_payment
                    : 'None'
                }}
              </p>
            </div>
          </div>

          <!------------------- extensions ----------------------->
          <div v-if="this.parameters.package.extensions">
            <sw-divider class="my-8" />

            <p class="text-gray-500 uppercase sw-section-title">
              {{ $t('customers.pbxservices_extensions') }}
            </p>

            <div
              class="w-full mb-8 bg-white rounded p-8 relative package-details"
            >
              <sw-table-component
                ref="table"
                :data="fetchDataExtension"
                :show-filter="false"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('corePbx.extensions.extension')"
                  show="name"
                >
                  <template slot-scope="row">
                    <span>{{ $t('corePbx.extensions.extension') }}</span>

                    <span>
                      {{ row.ext ? row.ext : 'Not selected' }}
                    </span>
                    <div v-if="row.date_prorate !== null">
                      <p class="whitespace-nowrap"><b>Prorate Date: </b> {{ row.date_prorate | formatDate }}</p>
                      <p class="whitespace-nowrap"><b>Prorate Price: </b> <span v-html="$utils.formatMoney(row.prorate, defaultCurrency)"/></p>
                    </div>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('corePbx.extensions.email')"
                  show="email"
                >
                  <template slot-scope="row">
                    <span>{{ $t('corePbx.extensions.email') }}</span>

                    <span>
                      {{ row.email ? row.email : 'Not selected' }}
                    </span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('corePbx.extensions.location')"
                  show="location"
                >
                  <template slot-scope="row">
                    <span>{{ $t('corePbx.extensions.location') }}</span>

                    <span>
                      {{ row.location ? row.location : 'Not selected' }}
                    </span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('corePbx.extensions.ua_fullname')"
                  show="ua_fullname"
                >
                  <template slot-scope="row">
                    <span>{{ $t('corePbx.extensions.ua_fullname') }}</span>

                    <span>
                      {{ row.ua_fullname ? row.ua_fullname : 'Not selected' }}
                    </span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('corePbx.extensions.extension_price')"
                  show="price"
                >
                  <template slot-scope="row">
                    <span>{{ $t('corePbx.extensions.extension_price') }}</span>
                    <span>
                      <div v-html="$utils.formatMoney((row.profile_rate * 100), defaultCurrency)" />
                    </span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('corePbx.extensions.status')"
                  show="status"
                >
                  <template slot-scope="row">
                    <span>{{ $t('corePbx.extensions.status') }}</span>

                    <span>
                      {{ row.status ? row.status : 'Not selected' }}
                    </span>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>

          <!------------------- did ----------------------->
          <div v-if="this.parameters.package.did">
            <sw-divider class="my-8" />

            <p class="text-gray-500 uppercase sw-section-title">
              {{ $t('did.title') }}
            </p>

            <sw-table-component
              ref="table"
              :data="fetchDataDID"
              :show-filter="false"
              table-class="table"
            >
              <sw-table-column
                :sortable="true"
                :label="$t('corePbx.did.did_channel')"
                show="number"
              >
                <template slot-scope="row">
                  <span>{{ $t('corePbx.did.did_channel') }}</span>

                  <span>
                    {{ row.number ? row.number : 'Not selected' }}
                  </span>
                  <div v-if="row.date_prorate !== null">
                    <p class="whitespace-nowrap"><b>Prorate Date: </b> {{ row.date_prorate | formatDate }}</p>
                    <p class="whitespace-nowrap"><b>Prorate Price: </b> <span v-html="$utils.formatMoney(row.prorate, defaultCurrency)"/></p>
                  </div>
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :label="$t('corePbx.did.destination')"
                show="ext"
              >
                <template slot-scope="row">
                  {{ row.ext == null ? 'Ext Not Found' : row.ext }}
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :label="$t('corePbx.did.type')"
                show="type"
              >
                <template slot-scope="row">
                  {{ row.type == null ? 'No Type' : row.type }}
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :label="$t('corePbx.did.did_rate')"
                show="rate_per_minute"
              >
                <template slot-scope="row">
                  {{
                    row.rate_per_minute == null
                      ? 'No Rate'
                      : row.rate_per_minute
                  }}
                  
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :label="$t('corePbx.did.status')"
                show="status"
              >
                <template slot-scope="row">
                  {{ row.status == null ? 'Not Selected' : row.status }}
                </template>
              </sw-table-column>
            </sw-table-component>
          </div>

          <!------------------- ITEMS ----------------------->
          <sw-divider class="my-8" />
          <p class="text-gray-500 uppercase sw-section-title">
            {{ $t('items.title') }}
          </p>

          <div class="mt-8">
            <table class="w-full text-center item-table">
              <colgroup>
                <col style="width: 40%" />
                <col style="width: 10%" />
                <col style="width: 15%" />
                <col v-if="discountPerItem === 'YES'" style="width: 15%" />
                <col style="width: 15%" />
              </colgroup>
              <thead class="bg-white">
                <th
                  class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-left text-gray-700
                  "
                >
                  <span class="">
                    {{ $tc('items.item', 1) }}
                  </span>
                </th>
                <th
                  class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-right text-gray-700
                  "
                >
                  <span class="">
                    {{ $t('item_groups.item.qty') }}
                  </span>
                </th>
                <th
                  class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-left text-gray-700
                  "
                >
                  <span class="">
                    {{ $t('item_groups.item.price') }}
                  </span>
                </th>
                <th
                  v-if="discountPerItem === 'YES'"
                  class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-left text-gray-700
                  "
                >
                  {{ $t('invoices.item.discount') }}
                </th>
                <th
                  class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-right text-gray-700
                  "
                >
                  <span class="">
                    {{ $t('invoices.item.amount') }}
                  </span>
                </th>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in pbxService.itemsIncluded"
                  :key="index"
                  class="border-t border-gray-200 border-solid"
                >
                  <td colspan="5" class="p-0 text-left align-top">
                    <table class="w-full">
                      <colgroup>
                        <col style="width: 40%" />
                        <col style="width: 10%" />
                        <col style="width: 15%" />
                        <col
                          v-if="discountPerItem === 'YES'"
                          style="width: 15%"
                        />
                        <col style="width: 15%" />
                      </colgroup>
                      <tbody>
                        <tr>
                          <td class="px-5 py-4 text-left align-top">
                            <div class="flex justify-start">
                              <span>{{ item.name }}</span>
                            </div>
                          </td>
                          <td class="px-5 py-4 text-right align-top">
                            <span>{{
                              isView ? item.pivot.quantity : item.quantity
                            }}</span>
                          </td>
                          <td class="px-5 py-4 text-left align-top">
                            <div class="flex flex-col">
                              <div class="flex-auto flex-fill bd-highlight">
                                <div class="relative w-full">
                                  <span
                                    v-html="
                                      $utils.formatMoney(
                                        item.price,
                                        defaultCurrency
                                      )
                                    "
                                  >
                                  </span>
                                </div>
                              </div>
                            </div>
                          </td>
                          <td
                            v-if="discountPerItem === 'YES'"
                            class="px-5 py-4 text-left align-top"
                          >
                            <div class="flex flex-col">
                              <div class="flex flex-auto" role="group">
                                <span>{{ item.discount }}</span>
                                <span class="flex">
                                  {{
                                    item.discount_type === 'fixed'
                                      ? defaultCurrency.symbol
                                      : '%'
                                  }}
                                </span>
                              </div>
                            </div>
                          </td>
                          <td class="px-5 py-4 text-right align-top">
                            <div class="flex items-center justify-end text-sm">
                              <span>
                                <div
                                  v-html="
                                    $utils.formatMoney(
                                      isView ? item.pivot.total : item.total,
                                      defaultCurrency
                                    )
                                  "
                                />
                              </span>
                            </div>
                          </td>
                        </tr>
                        <tr v-if="taxPerItem === 'YES'" class="tax-tr">
                          <td class="px-5 py-4 text-left align-top" />
                          <td colspan="4" class="px-5 py-4 text-left align-top">
                            <div
                              v-for="(tax, index) in item.taxes"
                              :key="index"
                              class="flex items-center justify-between mb-3"
                            >
                            <div v-if="tax.amount">
                              <div class="flex items-center" style="flex: 4">
                                <label class="pr-2 mb-0" align="right">
                                  {{ $t('general.tax') }}
                                </label>
                                <span>{{
                                  '(' + tax.name + ' - ' + tax.percent + '%)'
                                }}</span>
                              </div>
                              <div
                                class="text-sm text-right"
                                style="flex: 3"
                                v-html="
                                  $utils.formatMoney(
                                    tax.amount,
                                    defaultCurrency
                                  )
                                "
                              />
                            </div>

                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <sw-divider class="my-8" />

          <!--------------- ADDITIONAL CHARGES --------------->
          <div class="tabs mb-5 grid col-span-12 pt-6">
            <div class="border-b tab">
              <div class="relative">
                <input
                  class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
                  type="checkbox"
                />
                <header
                  class="col-span-5  flex justify-between items-center py-3 cursor-pointer select-none tab-label"
                >
                  <span class="text-gray-500 uppercase sw-section-title">
                    {{ $t('corePbx.charges') }}
                  </span>
                  <div  class="rounded-full  border border-grey w-7 h-7 flex items-center justify-center test" >
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
                    :data="additionalChargesData"
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
                  <div class="block my-10  table-foot lg:justify-between lg:flex lg:items-start">
                    <div class="w-full lg:w-1/2"></div>

                    <div
                      class="px-5 py-4  mt-6 bg-white border border-gray-200 border-solid rounded table-total lg:mt-0"
                    >
                      <!------- COUNT ------->
                      <div class="flex items-center justify-between w-full">
                        <label
                          class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                        >
                          {{ $t('pbx_services.count') }}
                        </label>
                        <label
                          class="flex items-center justify-center m-0 text-lg text-black uppercase"
                        >
                          <span>{{ this.chargesCount }}</span>
                        </label>
                      </div>

                      <!------- AMOUNT ------->
                      <div
                        class="flex items-center justify-between w-full pt-2 mt-5 border-t border-gray-200 border-solid"
                      >
                        <label
                          class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                        >
                          {{ $t('pbx_services.total') }}
                          {{ $t('pbx_services.amount') }}:
                        </label>
                        <label
                          class="flex items-center justify-center text-lg uppercase text-primary-400"
                        >
                          <span class="ml-1">{{
                            defaultCurrency.symbol +
                            ' ' + this.chargesTotal.toFixed(2)
                          }}</span>
                        </label>
                        <!--   -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>   

          <!------------------------ TOTALS -------------------------->
          <div
            class="block  my-10 invoice-foot lg:justify-between lg:flex lg:items-start"
          >
            <div class="w-full lg:w-1/2"></div>
            <div class="px-5 py-4 mt-6 bg-white border border-gray-200 border-solid rounded invoice-total lg:mt-0">
              <!------------- PRICE ----------->
              <div class="flex items-center justify-between w-full mb-10">
                <label
                  class="
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                >{{ $t('customers.price') }}</label>
                <div
                  class="flex items-center justify-center m-0 text-lg text-black"
                  role="group"
                >
                  <span
                    v-html="$utils.formatMoney(price * 100, defaultCurrency)"
                  ></span>
                </div>
              </div>
              <br />
              <!------------- TOTAL ITEMS ----------->
              <div class="flex items-center justify-between w-full mb-10">
                <label
                  class="
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                >{{ $t('customers.total_items') }}</label>
                <div
                  class="flex items-center justify-center m-0 text-lg text-black"
                  role="group"
                >
                  <span
                    v-html="$utils.formatMoney(total_items_price * 100, defaultCurrency)"
                  ></span>
                </div>
              </div>
              <br />
              <!------------- CAP TOTAL ----------->
              <div class="flex items-center justify-between w-full mb-10">
                <label
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                  >{{ $t('customers.cap_total') }}</label
                >
                <div
                  class="flex items-center justify-center m-0 text-lg text-black"
                  role="group"
                >
                  <span>
                    {{ capTotal }}
                  </span>
                </div>
              </div>
              <br/>
              <!------------- EXT QTY ----------->
              <div class="flex items-center justify-between w-full mb-10">
                <label class="text-sm font-semibold leading-5 text-gray-500 uppercase">
                  {{ $t('customers.extension_qty') }}
                </label>
                <div
                  class="flex items-center justify-center m-0 text-lg text-black"
                  role="group"
                >
                  <span v-if="!isView">{{
                    this.corePbxServicesIncludedData.ext.length
                  }}</span>
                  <span v-if="isView">{{
                    this.parameters.extensions.length
                  }}</span>
                </div>
              </div>
              <br />
              <!------------- EXT PRICE ----------->
              <div class="flex items-center justify-between w-full mb-10">
                <label
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                  >{{ $t('customers.extension_price') }}</label
                >
                <div
                  class="flex items-center justify-center m-0 text-lg text-black"
                  role="group"
                >
                  <span
                    v-html="
                      $utils.formatMoney(
                        this.extensionsPrice * 100,
                        defaultCurrency
                      )
                    "
                  ></span>
                </div>
              </div>
              <br />
              <!------------- DID QTY ----------->
              <div class="flex items-center justify-between w-full mb-10">
                <label
                  class="
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                  >{{ $t('customers.did_qty') }}</label
                >
                <div
                  class="
                    flex
                    items-center
                    justify-center
                    m-0
                    text-lg text-black
                  "
                  role="group"
                >
                  <span v-if="!isView">{{
                    this.corePbxServicesIncludedData.did.length
                  }}</span>
                  <span v-if="isView">{{ this.parameters.did.length }}</span>
                </div>
              </div>
              <br />
              <!------------- DID PRICE ----------->
              <div class="flex items-center justify-between w-full mb-10">
                <label
                  class="
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                  >{{ $t('customers.did_price') }}</label
                >
                <div
                  class="
                    flex
                    items-center
                    justify-center
                    m-0
                    text-lg text-black
                  "
                  role="group"
                >
                  <span
                    v-html="$utils.formatMoney(this.didPrice, defaultCurrency)"
                  ></span>
                </div>
              </div>
              <br />
              <!------------- ADITIONAL CHARGES ----------->
              <div class="flex items-center justify-between w-full mb-10">
                <label
                  class="
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                  >{{ $t('customers.aditional_charges') }}</label
                >
                <div
                  class="
                    flex
                    items-center
                    justify-center
                    m-0
                    text-lg text-black
                  "
                  role="group"
                >
                  <span
                    v-html="
                      $utils.formatMoney(aditionalCharges, defaultCurrency)
                    "
                  ></span>
                </div>
              </div>
              <br />
              <!----------- SUB TOTAL ----------->
              <div class="flex items-center justify-between w-full mb-10">
                <label
                  class="
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                  >{{ $t('invoices.sub_total') }}</label
                >
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
                  <div v-html="$utils.formatMoney(subtotal, defaultCurrency)" />
                </label>
              </div>
              <br />
              <!------------- DISCOUNT ----------->
              <div
                v-if="
                  (discountPerItem === 'NO' || discountPerItem === null) &&
                  this.parameters.allow_discount
                "
                class="flex items-center justify-between w-full mb-10"
              >
                <label
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                  >{{ $t('invoices.discount') }}</label
                >
                <div
                  class="flex items-center justify-center m-0  text-lg text-black"
                  role="group"
                >
                  <span>{{ discount }}</span>
                  <span class="">
                    {{
                      this.corePbxServicesIncludedData.discount_calc === 'fixed'
                        ? defaultCurrency.symbol
                        : '%'
                    }}
                  </span>
                </div>
              </div>

              <!-------------- TAXES ------------->
              <div v-if="pbxService.taxes.length">
                <div
                  v-for="(tax, index) in pbxService.taxes"
                  :key="index"
                  class="mt-4 flex items-center justify-between w-full text-sm"
                >
                  <label
                    class="font-semibold leading-5 text-gray-500 uppercase"
                  >
                    {{ tax.name }} ({{ tax.percent }}%)
                  </label>
                  <label
                    class="flex items-center justify-center text-lg text-black"
                  >
                    <div
                      v-html="$utils.formatMoney(tax.amount, defaultCurrency)"
                    />
                  </label>
                </div>
              </div>
              <br />

              <!----------- TOTAL PRORRATE ----------->
              <div v-if="isEdit" class="flex items-center justify-between w-full mb-10">
                <label
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                >
                  Total Prorrate
                </label>
                <label
                  class="flex items-center justify-center m-0 text-lg text-black uppercase"
                >
                  <div v-html="$utils.formatMoney(totalProrrate, defaultCurrency)" />
                </label>
              </div>
              <br />

              <!----------- TOTAL ----------->
              <div class="flex items-center justify-between w-full mb-10">
                <label
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                >
                  {{ parameters.term }} Fee
                </label>
                <label
                  class="flex items-center justify-center m-0 text-lg text-black uppercase"
                >
                  <div v-html="$utils.formatMoney(total, defaultCurrency)" />
                </label>
              </div>

              <br />
            </div>
          </div>
          <!------------------- ACTIONS --------------------->
          <div class="mb-4">
            <sw-button
              :disabled="isLoading"
              variant="primary-outline"
              size="lg"
              class="flex justify-center w-full md:w-auto align-bottom"
              @click="back()"
            >
              <arrow-left-icon class="h-5 mr-2 -ml-1" />
              {{ $t('general.back') }}
            </sw-button>

            <sw-button
              :loading="isLoading"
              :disabled="isLoading"
              variant="primary"
              type="button"
              size="lg"
              class="flex justify-center w-full md:w-auto ml-4 align-bottom"
              @click="savePbxService()"
              v-if="!isView"
            >
              <!--  -->
              <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
              {{ isEdit ? $t('general.update') : $t('services.add_service') }}
            </sw-button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import {
  FilterIcon,
  ArrowLeftIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
} from '@vue-hero-icons/solid'
import SatelliteIcon from '../../../components/icon/SatelliteIcon.vue'
import moment from 'moment'

export default {
  components: {
    SatelliteIcon,
    FilterIcon,
    XIcon,
    PlusIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    ArrowLeftIcon
  },

  data() {
    return {
      isRequestOnGoing: false,
      isLoading: false,
      parameters: '',
      includedData: {},
      dataExtensionsIncluded: [],
      discountPerItem: null,
      taxPerItem: null,
      pbxService: {
        itemsIncluded: [],
        taxes: [],
      },
      discount: null,
      price: null,
      didPrice: null,
      extensionsPrice: null,
      aditionalCharges: null,
      subtotal: null,
      total_did_price: null,
      total: null,
      totalTax: null,
      totalProrrate: 0,
      capTotal: 0,
      additionalChargesData: [],
      chargesCount: 0,
    }
  },

  filters: {
    formatDate(value) {
      return moment(value).format('DD/MM/YYYY')
    },
  },

  computed: {
    ...mapGetters('customer', [
      'corePbxServicesParameters',
      'selectedPbxExtensions',
      'selectedDID',
      'selectedPbxDID',
      'pbxServiceSaved',
      'corePbxServicesIncludedData',
    ]),

    ...mapGetters('company', ['defaultCurrency']),

    ...mapGetters('pbxService', ['selectedPbxService']),

    selectFieldExtensions: {
      get: function () {
        return this.selectedPbxExtensions
      },
      set: function (val) {
        this.selectExtensions(val)
      },
    },
    total_items_price(){
      return this.pbxService.itemsIncluded.reduce((total, item) => {
          return total + item.total
        }, 0) / 100
    },

    

    selectFieldDID: {
      get: function () {
        return this.selectedPbxDID
      },
      set: function (val) {
        this.selectDID(val)
      },
    },

    selectAllFieldStatusDID: {
      get: function () {
        return this.selectAllFieldDID
      },
      set: function (val) {
        this.setSelectAllStateDID(val)
      },
    },

    selectAllFieldStatusExtension: {
      get: function () {
        return this.selectAllFieldExtensions
      },
      set: function (val) {
        this.setSelectAllStateExtension(val)
      },
    },

    isView() {
      if (this.$route.name === 'customers.pbx-package-view') {
        return true
      }
      return false
    },

    isEdit() {
      if (this.$route.name === 'pbxServices.edit') {
        return true
      }
      return false
    },
  },

  created() {
    this.loadData()
  },

  methods: {
    ...mapActions('customer', [
      'fetchExtPbxService',
      'fetchDIDPbxService',
      'selectDID',
      'selectExtension',
      'selectAllDID',
      'selectAllExtension',
      'setSelectAllStateDID',
      'setSelectAllStateExtension',
      'setCorePBXServices',
      'updatePBXServices',
    ]),

    ...mapActions('servicePbx', ['fetchViewPbxService']),

    ...mapActions('pbxService', ['fetchAdditionalCharges']),

    async loadData() {
      this.isRequestOnGoing = true
      this.chargesTotal = 0;
      if (this.isView) {
        let response = await this.fetchViewPbxService(
          this.$route.params.customer_pbx_package_id
        )
        let pbxService = response.data.pbxService
        // console.log(pbxService);
        this.parameters = response.data.pbxService
        this.corePbxServicesIncludedData.did = pbxService.did
        this.corePbxServicesIncludedData.ext = pbxService.extensions
        this.pbxService.itemsIncluded = pbxService.items
        this.price = pbxService.pbxpackages_price
        this.didPrice =
          pbxService.did.length *
          (pbxService.package.template_did_id
            ? pbxService.package.profile_did.did_rate
            : 0)
        this.extensionsPrice =
          pbxService.extensions.length *
          (pbxService.package.profile_extensions
            ? pbxService.package.profile_extensions.rate
            : 0)

        // this.aditionalCharges = await this.aditionalChargesAmount(pbxService.package);
        this.subtotal = pbxService.sub_total
        this.total = pbxService.total
      } else {
        // data
        this.parameters = JSON.parse(
          JSON.stringify(this.corePbxServicesParameters.parameters)
        )
        this.pbxService.itemsIncluded = this.corePbxServicesIncludedData.items
        //
        this.pbxService.taxes =
          typeof this.corePbxServicesIncludedData.taxes != 'undefined'
            ? JSON.parse(JSON.stringify(this.corePbxServicesIncludedData.taxes))
            : []
        this.taxPerItem =
          this.parameters && this.parameters.tax_type.value === 'I'
            ? 'YES'
            : 'NO'
        // montos
        this.discount = this.corePbxServicesIncludedData.discount_value
        this.price = parseFloat(this.corePbxServicesIncludedData.price)
        // cargos adicionales
        const resultCharge = await this.getAditionalChargeArray(this.parameters.package);
        this.additionalChargesData = resultCharge.arrayCharge
        if (this.isEdit){
          // this.didPrice = this.corePbxServicesIncludedData.did.length * (this.parameters.package.profile_did2 ? this.parameters.package.profile_did2.did_rate : 0);
          this.totalProrrate = await this.getTotalProrrate(this.corePbxServicesIncludedData.did, this.corePbxServicesIncludedData.ext);
        }
        this.didPrice = await this.didPriceCalc( this.corePbxServicesIncludedData.did );
        this.extensionsPrice = this.corePbxServicesIncludedData.ext.length * 
        (this.parameters.package.profile_extensions
            ? this.parameters.package.profile_extensions.rate
            : 0)
        this.aditionalCharges = await this.getAditionalChargesAmount({amountDid: resultCharge.amountDid , amountExt: resultCharge.amountExt});

        this.subtotal =
          this.corePbxServicesIncludedData.subtotal +
          this.aditionalCharges +
          this.price * 100 +
          this.extensionsPrice * 100 +
          this.didPrice
        await this.taxAmountCalc(this.subtotal) // calculo de cada tax
        let discountCalc = await this.discountCalc(
          parseFloat(this.discount),
          this.subtotal
        )
        this.totalTax = await this.taxesCalc(this.pbxService.taxes) // suma de todos los impuestos
        this.total = this.subtotal + this.totalTax - discountCalc;
        this.capTotal = this.corePbxServicesIncludedData.ext.length * this.corePbxServicesIncludedData.cap_extension;
      }

      this.isRequestOnGoing = false;
    },

    async didPriceCalc(dids) {
      let amount = 0
      dids.forEach((did) => {
        amount += parseFloat(did.rate_per_minute)
      })
      //
      return amount * 100
    },

    async getAditionalChargesAmount({amountDid, amountExt}) {
      // let amount = 0
      // if (this.isEdit) {
      //   // get service from state
      //   let service = JSON.parse(JSON.stringify(this.selectedPbxService))
      //   let params = {
      //     pbx_service_id: service.id,
      //     page: 1,
      //     limit: 100,
      //   }
      //   let res = await this.fetchAdditionalCharges(params)
      //   // console.log('cargos adicionales: ', res);
      //   amount = await this.aditionalChargesAmountEdit(res.data.charges.data)
      // } else {
      //   amount = await this.aditionalChargesAmount(this.parameters.package);
      // }

      const amountTotalDid = this.corePbxServicesIncludedData.did.length * amountDid
      const amountTotalExt = this.corePbxServicesIncludedData.ext.length * amountExt

      
      return (amountTotalDid + amountTotalExt) * 100
    },

    // metodo para armar objeto de cargos adicionales //
    async getAditionalChargeArray(pbxPackage = null){
      let objetoFinal = [];
      let totalAmountExt = 0
      let totalAmountDID = 0
      let count = 0;
      let amount = 0;
      if (pbxPackage.profile_did_aditional_charges != null && pbxPackage.profile_extensions_aditional_charges != null){
        pbxPackage.profile_did_aditional_charges.forEach((charge) => {
          if(charge.status){
            charge.type_from = 'DID';
            charge.profile_name = pbxPackage.profile_did2.name;
            objetoFinal.push(charge);
            count++;
            amount = amount + parseFloat(charge.amount);
            totalAmountDID = totalAmountDID + parseFloat(charge.amount);
          }
        })
        pbxPackage.profile_extensions_aditional_charges.forEach((charge) => {
          if(charge.status){
            charge.type_from = 'Extension';
            charge.profile_name = pbxPackage.profile_extensions.name;
            objetoFinal.push(charge);
            count++;
            amount = amount + parseFloat(charge.amount);
            totalAmountExt = totalAmountExt + parseFloat(charge.amount);
          }
        });
        //
        this.chargesCount = count;
        this.chargesTotal = amount;
      }else if(pbxPackage.profile_did && pbxPackage.profile_extensions){
          pbxPackage.profile_did.aditional_charges_a.forEach((charge) => {
            if(charge.status){
              charge.type_from = 'DID';
              charge.profile_name = pbxPackage.profile_did2.name;
              objetoFinal.push(charge);
              count++;
              amount = amount + parseFloat(charge.amount);
              totalAmountDID = totalAmountDID + parseFloat(charge.amount);
            }
          })

          pbxPackage.profile_extensions.aditional_charges_a.forEach((charge) => {
            if(charge.status){
              charge.type_from = 'Extension';
              charge.profile_name = pbxPackage.profile_extensions.name;
              objetoFinal.push(charge);
              count++;
              amount = amount + parseFloat(charge.amount);
              totalAmountExt = totalAmountExt + parseFloat(charge.amount);
            }
          });
        //
        this.chargesCount = count;
        this.chargesTotal = amount;
      }
      return {
        arrayCharge: objetoFinal,
        amountDid: totalAmountDID,
        amountExt: totalAmountExt
      };      
    },

    async getTotalProrrate(dataDid = [], dataExt = []){
      let totalProrateExt = 0
      let totalProrateDid = 0
      if (dataExt.length > 0) {
        totalProrateExt = dataExt.reduce((acc, cur) => {
          if(!cur.invoice_prorate) {
            return acc + parseFloat(cur.prorate);
          }else{
            return acc;
          }
        }, 0);          
      }

      if (dataDid.length > 0) {
        totalProrateDid = dataDid.reduce((acc, cur) => {
          if(!cur.invoice_prorate) {
            return acc + parseFloat(cur.prorate);
          }else{
            return acc;
          }
        }, 0);          
      }
      // sumar los dos totales
      return totalProrateExt + totalProrateDid;
    },

    async aditionalChargesAmount(pbxPackage) {
      let amountExt = 0;
      let amountDid = 0;
      let amountExtTotal = 0;
      let amountDidTotal = 0;
      // validar perfiles did
      if (pbxPackage.profile_did2) {
        // recorrer cargos adicionales por perfil y tomar monto
        pbxPackage.profile_did_aditional_charges.forEach((charge) => {
          if (charge.status) {
            amountDid += parseFloat(charge.amount)
          }
        })
        amountDidTotal = this.corePbxServicesIncludedData.did.length * amountDid
      }
      // validar perfiles ext
      if (pbxPackage.profile_extensions) {
        pbxPackage.profile_extensions_aditional_charges.forEach((charge) => {
          if (charge.status) {
            amountExt += parseFloat(charge.amount)
          }
        })
        amountExtTotal = this.corePbxServicesIncludedData.ext.length * amountExt
      }
      return amountDidTotal + amountExtTotal
    },

    async aditionalChargesAmountEdit(charges) {
      let amount = 0
      charges.forEach((charge) => {
        //        console.log('charge: ', charge);
        if (charge.status) {
          amount += parseFloat(charge.amount)
        }
      })

      return amount
    },

    async discountCalc(discount, subtotal) {
      // descuento (Aplicados al subtotal)
      if (this.corePbxServicesIncludedData.discount_calc === 'percentage') {
        let discountCalc = subtotal * (discount / 100)
        return discountCalc
      }
      if (this.corePbxServicesIncludedData.discount_calc === 'fixed') {
        return discount * 100
      }
    },
    // calcula monto de cada impuesto en base al subtotal
    taxAmountCalc(subtotal) {
      let amount = 0
      let arrayLength = this.pbxService.taxes.length

      this.pbxService.taxes.forEach((element) => {
        // console.log('tax: ', element);
        element.amount = (element.percent * subtotal) / 100
      })
    },
    // suma total de los montos de cada impuesto
    async taxesCalc(taxes) {
      let amount = 0
      taxes.forEach((tax) => {
        amount += parseFloat(tax.amount)
      })
      return amount
    },

    async fetchDataExtension({ page, filter, sort }) {
      let ExtPbx = this.corePbxServicesIncludedData.ext
      return {
        data: ExtPbx || {},
        pagination: {
          currentPage: page,
        },
      }
    },

    async fetchDataDID({ page, filter, sort }) {
      let didPbxService = this.corePbxServicesIncludedData.did

      return {
        data: didPbxService || {},
        pagination: {
          currentPage: page,
        },
      }
    },

    /**
     * final save of pbx service
     */
    async savePbxService() {
      this.isLoading = true;
      let resPbxService
      // request object
      this.parameters.items = this.corePbxServicesIncludedData.items;
      this.parameters.dids = this.corePbxServicesIncludedData.did;
      this.parameters.extensions = this.corePbxServicesIncludedData.ext;
      this.parameters.allow_discount_value =
        this.corePbxServicesIncludedData.discount_value;
      this.parameters.allow_discount_type =
        this.corePbxServicesIncludedData.discount_calc;
      this.parameters.pbxpackages_price = this.price;
      this.parameters.cap_extension = this.corePbxServicesIncludedData.cap_extension;
      this.parameters.generateinvoice = this.corePbxServicesIncludedData.generateinvoice;
      this.parameters.cap_total = this.capTotal;
      this.parameters.inclusive_minutes_seconds_consumed = this.capTotal * 60;
      this.parameters.taxes = this.pbxService.taxes;
      this.parameters.sub_total = this.subtotal;
      this.parameters.total = this.total;
      this.parameters.total_prorate = this.totalProrrate;
      this.parameters.tax = this.totalTax;
      this.parameters.taxCdr = this.corePbxServicesIncludedData.taxCdr;
      this.parameters.only_callrating = this.parameters.package.all_cdrs
        ? true
        : false;
      this.parameters.invoice_prorate = this.corePbxServicesIncludedData.invoice_prorate;
  
      let message = this.isEdit
        ? this.$t('services.confirm_update')
        : 'Are you sure of create this service?'

      swal({
        title: message,
        text: 'The load of the CDRs may take some time',
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: {
          cancel: true,
          confirm: true,
          /* cancelButtonText: "No, cancel it!",
          confirmButtonText: 'Yes, I am sure!' */
        },
      }).then(async (result) => {
        if (result) {
          if (this.isEdit) {
            let data = { parameters: this.parameters  }
            resPbxService = await this.updatePBXServices(data);
          } else {
            // action to save
            resPbxService = await this.setCorePBXServices(this.parameters);
          }
          //  
          if (resPbxService.data.success) {
            window.toastr['success'](resPbxService.data.message)
            // push view
            this.$router.push('/admin/customers/' + this.$route.params.id + '/view')
          } else {
            window.toastr['error'](resPbxService.data.message)
          }
        }
        this.isLoading = false;
      });
    },

    // go to back
    async back() {
      this.$emit('back')
      this.package = ''
      this.isLoading = false
    },
  },
}
</script>

<style>
  .mb-10 {
    margin-bottom: -10px;
  }
</style>