<template>
  <div
    class="w-full mb-8 bg-white border border-gray-200 border-solid rounded p-8 relative package-details"
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
            class="grid grid-cols-1 gap-4 mt-5 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1"
          >
            <div>
              <p
                class="mb-1text-sm font-normal leading-5 non-italic text-primary-800"
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
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('packages.status') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                <div v-if="this.parameters" >
  <div v-if="this.parameters.status === 'A'" >

    <sw-badge
              :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
              :color="$utils.getBadgeStatusColor('COMPLETED').color"
              class="px-3 py-1"
            >
            {{ $t('general.active') }}
            </sw-badge> 
  </div>


  <div v-if="this.parameters.status === 'P'" >
    <sw-badge
              :bg-color="$utils.getBadgeStatusColor('VIEWED').bgColor"
              :color="$utils.getBadgeStatusColor('VIEWED').color"
              class="px-3 py-1"
            >
            {{ $t('general.pending') }}
            </sw-badge>
</div>

<div v-if="this.parameters.status == 'S'">
              <sw-badge
              :bg-color="$utils.getBadgeStatusColor('SENT').bgColor"
              :color="$utils.getBadgeStatusColor('SENT').color"
              class="px-3 py-1"
            >
           {{ $t('general.suspended') }}
            </sw-badge>
          </div>
             
          <div v-if="this.parameters.status == 'C' ">
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

            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('customers.term') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ this.parameters ? this.parameters.term : '' }}
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
                {{ $t('invoices.discount') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
              

{{ this.parameters && this.parameters.allow_discount ? $t('general.yes') : $t('general.not')  }}
              </p>
            </div>

            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('customers.date_begin') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ this.parameters.date_begin | formatDate }}
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
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
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
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
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
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('customers.type_service') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                <div v-if="this.parameters" >
  <div v-if="this.parameters.package.status_payment.length > 0" >
  
<div v-if="this.parameters.package.status_payment == 'postpaid'">
              {{ $t('packages.item.postpaid') }}
            </div>
            <div v-if="this.parameters.package.status_payment == 'prepaid'">
              {{ $t('packages.item.prepaid') }}
            </div>
  
  </div>

</div>
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
                      <p class="whitespace-nowrap">
                        <b>Prorate Date: </b>
                        {{ row.date_prorate | formatDate }}
                      </p>
                      <p class="whitespace-nowrap">
                        <b>Prorate Price: </b>
                        <span
                          v-html="
                            $utils.formatMoney(row.prorate, defaultCurrency)
                          "
                        />
                      </p>
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
                    <span v-if="isEdit">
                      <div
                        v-html="
                          $utils.formatMoney(row.price * 100, defaultCurrency)
                        "
                      />
                    </span>
                    <span v-else>
                      <div
                        v-html="
                          $utils.formatMoney(
                            row.profile_rate * 100,
                            defaultCurrency
                          )
                        "
                      />
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
                    <p class="whitespace-nowrap">
                      <b>Prorate Date: </b> {{ row.date_prorate | formatDate }}
                    </p>
                    <p class="whitespace-nowrap">
                      <b>Prorate Price: </b>
                      <span
                        v-html="
                          $utils.formatMoney(row.prorate, defaultCurrency)
                        "
                      />
                    </p>
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
                  <div v-if="isEdit">
                    {{
                      row.price != undefined ? row.price : row.rate_per_minute
                    }}
                  </div>
                  <div v-else>
                    {{
                      row.rate_per_minute === null
                        ? 'No Rate'
                        : row.rate_per_minute
                    }}
                  </div>
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

          <div
            class="w-full mb-8 mt-3 bg-white border border-gray-200 border-solid rounded p-8 relative package-details"
            v-if="parameters.custom_app_rate_id != null"
          >
            <p>{{ $t('general.warning_service15') }}</p>
            <sw-table-component
              ref="table"
              :data="this.corePbxServicesIncludedData.pbx_services_app_rate"
              :show-filter="false"
              table-class="table"
            >
              <sw-table-column :sortable="true" :label="'APPS'" show="app_name">
                <template slot-scope="row">
                  <span>{{ 'APPS' }}</span>
                  <span>
                    {{ row.app_name }}
                  </span>
                </template>
              </sw-table-column>

              <sw-table-column :sortable="true" :label="'TOTAL'" show="total">
                <template slot-scope="row">
                  <span>{{ 'TOTAL' }}</span>
                  <span>
                    {{ row.total }}
                  </span>
                </template>
              </sw-table-column>

              <sw-table-column :sortable="true" :label=" $t('general.warning_service16')" show="in_use">
                <template slot-scope="row">
                  <span>{{ $t('general.warning_service16') }}</span>
                  <span>
                    {{ row.in_use }}
                  </span>
                </template>
              </sw-table-column>

              <sw-table-column :sortable="true" :label="$t('general.warning_service18')" show="price">
                <template slot-scope="row">
                  <span>{{ $t('general.warning_service18') }}</span>
                  <div
                    v-html="
                      $utils.formatMoney(row.price * 100, defaultCurrency)
                    "
                  />
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :label="$t('general.warning_service19') "
                show="quantity"
              >
                <template slot-scope="row">
                  <span>{{ $t('general.warning_service19') }}</span>
                  <span>
                    {{ row.quantity }}
                  </span>
                </template>
              </sw-table-column>

              <sw-table-column :sortable="true" :label="'COSTO'" show="costo">
                <template slot-scope="row">
                  <span>{{ 'Total' }}</span>
                  <div
                    v-html="
                      $utils.formatMoney(
                        row.price * row.quantity * 100,
                        defaultCurrency
                      )
                    "
                  />
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
                  class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700"
                >
                  <span class="">
                    {{ $tc('items.item', 1) }}
                  </span>
                </th>
                <th
                  class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700"
                >
                  <span class="">
                    {{ $t('item_groups.item.qty') }}
                  </span>
                </th>
                <th
                  class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700"
                >
                  <span class="">
                    {{ $t('item_groups.item.price') }}
                  </span>
                </th>
                <th
                  v-if="discountPerItem === 'YES'"
                  class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700"
                >
                  {{ $t('invoices.item.discount') }}
                </th>
                <th
                  class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700"
                >
                  <span class="">
                    {{ $t('invoices.item.total') }}
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

                                                <!--perido deo faturacion /> -->
          <tr style="display: flex; align-items: center; margin-bottom: 10px">
            <td style="margin-right: 10px; margin-left: 10px">
              <sw-switch
                v-model="item.end_period_act"
                class="relative"
                :disabled="true"
                style="top: -10px; margin-bottom: 10px"
              />
            </td>
            <td style="flex-grow: 1; margin-left: 15px; margin-bottom: 10px">
              <div>
                <p
                  class="p-0 mb-1 text-base leading-snug text-black"
                  style="margin-top: 10px"
                >
                  {{ $t('packages.end_period_act') }}
                </p>
                <p
                  class="p-0 m-0 text-xs leading-tight text-gray-500"
                  style="
                    max-width: 480px;
                    margin-left: 10px;
                    margin-bottom: 10px;
                  "
                >
                  {{ $t('packages.end_period_act_desp') }}
                </p>
              </div>
            </td>
            <td style="margin-left: 10px">
              <sw-input
                :label="$t('packages.end_period_num')"
                v-model="item.end_period_number"
              
                :disabled="true"
                type="number"
                min="1"
                small
                style="margin-top: 10px; margin-bottom: 10px"
                onkeydown="javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) ? true : !isNaN(Number(event.key)) && event.code!=='Space'"
              />
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
                                  <div
                                    class="text-sm text-right ms-1"
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
                  class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label"
                >
                  <span class="text-gray-500 uppercase sw-section-title">
                    {{ $t('corePbx.charges') }}
                  </span>
                  <div
                    class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test"
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
                    :data="additionalChargesData"
                    table-class="table"
                  >
                    <sw-table-column
                      :sortable="true"
                      :label="$tc('pbx_services.name')"
                      show="description"
                    >
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.name') }}</span>
                        <span>{{ row.description }}</span>
                        <div>
                          <span v-if="row.isNew">New Charge</span>
                        </div>
                      </template>
                    </sw-table-column>

                    <!-- <sw-table-column
                      :sortable="true"
                      :label="$t('pbx_services.name')"
                      show="description"
                    /> -->

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
                      :label="$tc('pbx_services.template')"
                      show="profile_name"
                    />

                    <!-- data aditional -->
                    <sw-table-column
                      v-if="isEdit"
                      :sortable="true"
                      :label="$tc('pbx_services.quantity')"
                      show="quantity"
                    />

                    <sw-table-column
                      v-if="isEdit"
                      :sortable="true"
                      :label="$tc('pbx_services.price')"
                      show="total"
                    >
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.total') }}</span>
                        <span>{{
                          defaultCurrency.symbol + ' ' + row.total
                        }}</span>
                      </template>
                    </sw-table-column>
                    <!--  -->

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
                            ' ' +
                            this.aditionalCharges.toFixed(2)
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
            class="block my-10 invoice-foot lg:justify-between lg:flex lg:items-start"
          >
            <div class="w-full lg:w-1/2"></div>
            <div
              class="px-5 py-4 mt-6 bg-white border border-gray-200 border-solid rounded invoice-total lg:mt-0"
            >
              <!------------- PRICE ----------->
              <div class="flex items-center justify-between w-full mb-10">
                <label
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                  >{{ $t('customers.price') }}</label
                >
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

              <!------------- CUSTOM_APP_RATE ----------->
              <div class="flex items-center justify-between w-full mb-10">
                <label
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                  >{{ $t('customers.total_apps') }}</label
                >
                <div
                  class="flex items-center justify-center m-0 text-lg text-black"
                  role="group"
                >
                  <span
                    v-html="
                      $utils.formatMoney(
                        totalCustomAppRate * 100,
                        defaultCurrency
                      )
                    "
                  ></span>
                </div>
              </div>

              <br />
              <!------------- TOTAL ITEMS ----------->
              <div class="flex items-center justify-between w-full mb-10">
                <label
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                  >{{ $t('customers.total_items') }}</label
                >
                <div
                  class="flex items-center justify-center m-0 text-lg text-black"
                  role="group"
                >
                  <span
                    v-html="
                      $utils.formatMoney(totalItems * 100, defaultCurrency)
                    "
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
              <br />
              <!------------- EXT QTY ----------->
              <div class="flex items-center justify-between w-full mb-10">
                <label
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                >
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
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                  >{{ $t('customers.did_qty') }}</label
                >
                <div
                  class="flex items-center justify-center m-0 text-lg text-black"
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
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                  >{{ $t('customers.did_price') }}</label
                >
                <div
                  class="flex items-center justify-center m-0 text-lg text-black"
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
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                  >{{ $t('customers.aditional_charges') }}</label
                >
                <div
                  class="flex items-center justify-center m-0 text-lg text-black"
                  role="group"
                >
                  <span
                    v-html="
                      $utils.formatMoney(
                        aditionalCharges * 100,
                        defaultCurrency
                      )
                    "
                  ></span>
                </div>
              </div>
              <br />
              <!----------- SUB TOTAL ----------->
              <div class="flex items-center justify-between w-full mb-10">
                <label
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                  >{{ $t('invoices.sub_total') }}</label
                >
                <label
                  class="flex items-center justify-center m-0 text-lg text-black uppercase"
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
                <div class="flex items-center justify-center m-0 text-lg text-black" role="group">
<span v-if="this.corePbxServicesIncludedData.discount_calc === 'fixed'">
{{ $t('general.fixed') }} - {{ discount }}
</span>
<span v-else>
{{ $t('general.percentage') }} - {{ discount }}
</span>
<span>
{{
this.corePbxServicesIncludedData.discount_calc === 'fixed'
? defaultCurrency.symbol
: '%'
}}
</span>
</div>

              </div>

              <div
                v-if="
                  (discountPerItem === 'NO' || discountPerItem === null) &&
                  this.parameters.allow_discount
                "
                class="flex items-center justify-between w-full mb-10"
              >
                <label
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                  >{{ $t('invoices.discount_app') }}</label
                >
                <div
                  class="flex items-center justify-center m-0 text-lg text-black"
                  role="group"
                >
                <div v-html="$utils.formatMoney(discountapplied, defaultCurrency)" />
                </div>
              </div>

              <div
v-if="(discountPerItem === 'NO' || discountPerItem === null) && this.parameters.allow_discount"
class="flex items-center justify-between w-full mb-10"
>
<div class="flex items-center justify-center m-0" style="margin-top:2px" role="group">
<span
v-if="this.showdiscountwarning === true"
class="text-red-500 italic font-bold text-sm"
>
{{ $t('general.message_discount_warning_r') }}<br>
<small>{{ $t('general.period_discount') }}: {{this.discountbegin }} - {{this.discountend }}</small>
</span>
<span
v-else
class="text-green-500"
>
{{ $t('general.message_discount_warning_a') }}<br>
<small>{{ $t('general.period_discount') }}: {{this.discountbegin }} - {{this.discountend }}</small>
</span>
</div>
</div>
              <!-------------- GENERAL TAXES ------------->
              <div v-if="pbxService.taxes.length">
                <sw-divider class="my-8" />
                <div>
                  <p
                    class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
                  >
       

                    {{ $t('general.general_taxes') }}
                  </p>
                </div>
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
              <!-------------- ITEMS TAXES ------------->
              <div v-if="itemTaxes.length">
                <sw-divider class="my-8" />
                <div>
                  <p
                    class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
                  >
                  {{ $t('general.item_taxes') }}
                  </p>
                </div>
                <div
                  v-for="(tax, index) in itemTaxes"
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
              <sw-divider class="my-8" />

              <!----------- TOTAL PRORRATE ----------->
              <div
                v-if="isEdit"
                class="flex items-center justify-between w-full mb-10"
              >
                <label
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                >
                {{ $t('general.total_prorateo') }}
                </label>
                <label
                  class="flex items-center justify-center m-0 text-lg text-black uppercase"
                >
                  <div
                    v-html="$utils.formatMoney(totalProrrate, defaultCurrency)"
                  />
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
    ArrowLeftIcon,
  },

  data() {
    return {
      totalItems: 0,
      itemTaxes: null,
      totalItemsTaxes: 0,
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
      aditionalCharges: 0,
      subtotal: null,
      total_did_price: null,
      total: null,
      totalTax: null,
      totalProrrate: 0,
      capTotal: 0,
      additionalChargesData: [],
      chargesCount: 0,
      discountapplied: 0,
      discountbegin: null,
      discountend: null,
      showdiscountwarning: false,
    }
  },

  filters: {
    formatDate(value) {
      return moment(value).format('YYYY-MM-DD')
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
    total_items_price() {
      return (
        this.pbxService.itemsIncluded.reduce((total, item) => {
          return total + item.total
        }, 0) / 100
      )
    },
    totalCustomAppRate() {
      const APPS = this.corePbxServicesIncludedData.pbx_services_app_rate
      const total = APPS.reduce((total, app) => {
        return total + app.costo
      }, 0)
      return total
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
    this.calculateItemTaxes()
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

    ...mapActions('pbxService', [
      'fetchAdditionalCharges',
      'fetchAdditionalChargesService',
    ]),
    async loadData() {
      //  console.log('loadData iniciado')
      this.isRequestOnGoing = true
      //console.log('isRequestOnGoing:', this.isRequestOnGoing)
      this.chargesTotal = 0
      // console.log('chargesTotal inicializado en:', this.chargesTotal)

      if (this.isView) {
        // console.log('Modo vista activado')

        let response = await this.fetchViewPbxService(
          this.$route.params.customer_pbx_package_id
        )
        // console.log('Respuesta de fetchViewPbxService:', response)
        let pbxService = response.data.pbxService
        // console.log('pbxService:', pbxService)

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
        //console.log('didPrice calculado:', this.didPrice)
        this.extensionsPrice =
          pbxService.extensions.length *
          (pbxService.package.profile_extensions
            ? pbxService.package.profile_extensions.rate
            : 0)
        // console.log('extensionsPrice calculado:', this.extensionsPrice)

        // this.aditionalCharges = await this.aditionalChargesAmount(pbxService.package);
        this.subtotal = pbxService.sub_total
        //console.log('subtotal calculado:', this.subtotal)
        this.total = pbxService.total
        //console.log('Total calculado en modo vista:', this.total)
      } else {
        /*console.log(
          'Modo vista no activado, entrando en modo edición o creación'
        )*/

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
        // console.log('taxPerItem:', this.taxPerItem)
        // montos
        this.discount = this.corePbxServicesIncludedData.discount_value
        this.price = parseFloat(this.corePbxServicesIncludedData.price)
        // cargos adicionales
        const resultCharge = await this.getAditionalChargeArray(
          this.parameters.package
        )

        if (this.isEdit) {
          // this.didPrice = this.corePbxServicesIncludedData.did.length * (this.parameters.package.profile_did2 ? this.parameters.package.profile_did2.did_rate : 0);
          this.totalProrrate = await this.getTotalProrrate(
            this.corePbxServicesIncludedData.did,
            this.corePbxServicesIncludedData.ext
          )
          // console.log('totalProrrate calculado:', this.totalProrrate)
          this.didPrice = this.calculatePricesDids()
          // console.log('didPrice recalculado:', this.didPrice)
          this.extensionsPrice = this.calculatePricesExtensions()
          // console.log('extensionsPrice recalculado:', this.extensionsPrice)
        } else {
          this.didPrice = await this.didPriceCalc(
            this.corePbxServicesIncludedData.did
          )
          //console.log('didPrice calculado:', this.didPrice)
          this.extensionsPrice =
            this.corePbxServicesIncludedData.ext.length *
            (this.parameters.package.profile_extensions
              ? this.parameters.package.profile_extensions.rate
              : 0)
          //  console.log('extensionsPrice calculado:', this.extensionsPrice)
        }

        this.aditionalCharges = await this.getAditionalChargesAmount(
          {
            amountDid: resultCharge.amountDid,
            amountExt: resultCharge.amountExt,
          },
          resultCharge.arrayCharge,
          this.parameters.package
        )
        // console.log('aditionalCharges calculado:', this.aditionalCharges)
        this.subtotal =
          this.corePbxServicesIncludedData.subtotal +
          this.aditionalCharges * 100 +
          this.price * 100 +
          this.extensionsPrice * 100 +
          this.didPrice +
          this.totalCustomAppRate * 100
        //  console.log('subtotal recalculado:', this.subtotal)

        let discountCalculate = await this.discountCalc(
          parseFloat(this.discount),
          this.subtotal
        )
        //console.log('discountCalculate:', discountCalculate)

        this.discountapplied = discountCalculate
        await this.taxAmountCalc(this.subtotal - discountCalculate) // calculo de cada tax
        //console.log('taxAmountCalc ejecutado')

        this.totalTax = await this.taxesCalc(this.pbxService.taxes) // suma de todos los impuestos
        //console.log('totalTax calculado:', this.totalTax)
        this.total =
          this.subtotal +
          this.totalTax -
          discountCalculate +
          this.totalItemsTaxes * 100
        // console.log('Total final calculado:', this.total)
        this.capTotal =
          this.corePbxServicesIncludedData.ext.length *
          this.corePbxServicesIncludedData.cap_extension
        //console.log('capTotal calculado:', this.capTotal)
      }

      this.isRequestOnGoing = false
      //console.log('loadData finalizado')
      //console.log('isRequestOnGoing:', this.isRequestOnGoing)
    },

    calculatePricesExtensions() {
      let sum = 0
      const extensions = this.corePbxServicesIncludedData.ext
      for (let i = 0; i < extensions.length; i++) {
        let value = extensions[i].price
        sum += value
      }
      return sum
    },

    calculatePricesDids() {
      let sum = 0
      const dids = this.corePbxServicesIncludedData.did
      for (let i = 0; i < dids.length; i++) {
        let value = dids[i].price
        sum += value
      }
      return sum * 100
    },

    async didPriceCalc(dids) {
      let amount = 0
      dids.forEach((did) => {
        amount += parseFloat(did.rate_per_minute)
      })
      return amount * 100
    },

    async getAditionalChargesAmount(
      { amountDid, amountExt },
      dataCharge,
      pbxPackage
    ) {
      let total = 0
      if (this.isEdit) {
        const response = await this.fetchAdditionalChargesService({
          service_id: this.$route.params.customer_pbx_service_id,
        })
        let chargesArray = []

        if (!this.parameters.allow_pbx_packages_update) {
          let countDid = 0
          let countExtension = 0
          if (response.data.data.dids.length != 0) {
            countDid = response.data.data.dids.length
            let newcountdid = 0

            if (this.corePbxServicesIncludedData.did != 'undefined') {
              newcountdid = this.corePbxServicesIncludedData.did.length
            }

            for (let ii = 0; ii < response.data.data.dids.length; ii++) {
              response.data.data.dids[ii]['quantity'] = newcountdid
              response.data.data.dids[ii]['total'] =
                response.data.data.dids[ii]['quantity'] *
                response.data.data.dids[ii]['amount']
              chargesArray.push(response.data.data.dids[ii])
            }
          }
          if (response.data.data.extensions.length != 0) {
            countExtension = response.data.data.extensions.length
            let newcountext = 0
            if (this.corePbxServicesIncludedData.ext != 'undefined') {
              newcountext = this.corePbxServicesIncludedData.ext.length
            }

            for (let ii = 0; ii < response.data.data.extensions.length; ii++) {
              response.data.data.extensions[ii]['quantity'] = newcountext
              response.data.data.extensions[ii]['total'] =
                response.data.data.extensions[ii]['quantity'] *
                response.data.data.extensions[ii]['amount']

              chargesArray.push(response.data.data.extensions[ii])
            }
          }

          this.chargesCount = countDid + countExtension
        } else {
          //validate if service have pbx additional charges, else get additional charges of the package
          if (response.data.data.dids.length !== 0) {
            for (const did of pbxPackage.profile_did.aditional_charges_a) {
              let res = response.data.data.dids.find(
                (item) => item.profile_did_id === did.profile_did_id
              )
              if (res != undefined) {
                did.quantity = this.parameters.CountServicesDid
                did.total = this.parameters.CountServicesDid * did.amount
                chargesArray.push(did)
              }
            }
          } else if (
            pbxPackage.profile_did &&
            pbxPackage.profile_did.aditional_charges_a !== undefined &&
            pbxPackage.profile_did.aditional_charges_a !== null
          ) {
            if (pbxPackage.profile_did.aditional_charges_a.lenght !== 0) {
              for (const item of pbxPackage.profile_did.aditional_charges_a) {
                item.profile_name = pbxPackage.profile_did.name
                item.type_from = 'DID'
                item.quantity = this.parameters.CountServicesDid
                item.total = this.parameters.CountServicesDid * item.amount
                item.isNew = true
                chargesArray.push(item)
              }
            }
          }

          //validate if service have pbx additional charges, else get additional charges of the package
          if (response.data.data.extensions.length !== 0) {
            for (const extension of pbxPackage.profile_extensions
              .aditional_charges_a) {
              let res = response.data.data.extensions.find(
                (item) =>
                  item.profile_extension_id === extension.profile_extension_id
              )
              if (res != undefined) {
                extension.quantity = this.parameters.CountServicesExtension
                extension.total =
                  this.parameters.CountServicesExtension * extension.amount
                chargesArray.push(extension)
              }
            }
          } else if (
            pbxPackage.profile_extensions &&
            pbxPackage.profile_extensions.aditional_charges_a !== undefined &&
            pbxPackage.profile_extensions.aditional_charges_a !== null
          ) {
            if (pbxPackage.profile_extensions.aditional_charges_a !== 0) {
              for (const item of pbxPackage.profile_extensions
                .aditional_charges_a) {
                item.profile_name = pbxPackage.profile_extensions.name
                item.type_from = 'EXTENSION'
                item.quantity = this.parameters.CountServicesExtension
                item.total =
                  this.parameters.CountServicesExtension * item.amount
                item.isNew = true
                chargesArray.push(item)
              }
            }
          }
        }

        total = chargesArray.reduce((amount, obj) => {
          return amount + parseFloat(obj.total)
        }, 0)
        this.additionalChargesData = chargesArray
      } else {
        this.additionalChargesData = dataCharge
        const amountTotalDid =
          this.corePbxServicesIncludedData.did.length * amountDid
        const amountTotalExt =
          this.corePbxServicesIncludedData.ext.length * amountExt
        total = amountTotalDid + amountTotalExt //* 100
      }
      return total
    },

    // metodo para armar objeto de cargos adicionales //
    async getAditionalChargeArray(pbxPackage = null) {
      let objetoFinal = []
      let totalAmountExt = 0
      let totalAmountDID = 0
      let count = 0
      let amount = 0
      if (this.parameters.allow_pbx_packages_update) {
        if (
          pbxPackage.profile_did_aditional_charges != null &&
          pbxPackage.profile_extensions_aditional_charges != null
        ) {
          pbxPackage.profile_did_aditional_charges.forEach((charge) => {
            if (charge.status) {
              charge.type_from = 'DID'
              charge.profile_name = pbxPackage.profile_did2.name
              objetoFinal.push(charge)
              count++
              amount = amount + parseFloat(charge.amount)
              totalAmountDID = totalAmountDID + parseFloat(charge.amount)
            }
          })
          pbxPackage.profile_extensions_aditional_charges.forEach((charge) => {
            if (charge.status) {
              charge.type_from = 'Extension'
              charge.profile_name = pbxPackage.profile_extensions.name
              objetoFinal.push(charge)
              count++
              amount = amount + parseFloat(charge.amount)
              totalAmountExt = totalAmountExt + parseFloat(charge.amount)
            }
          })
          //
          this.chargesCount = count
          this.chargesTotal = amount
        } else if (pbxPackage.profile_did && pbxPackage.profile_extensions) {
          pbxPackage.profile_did.aditional_charges_a.forEach((charge) => {
            if (charge.status) {
              charge.type_from = 'DID'
              charge.profile_name = pbxPackage.profile_did2.name
              objetoFinal.push(charge)
              count++
              amount = amount + parseFloat(charge.amount)
              totalAmountDID = totalAmountDID + parseFloat(charge.amount)
            }
          })

          pbxPackage.profile_extensions.aditional_charges_a.forEach(
            (charge) => {
              if (charge.status) {
                charge.type_from = 'Extension'
                charge.profile_name = pbxPackage.profile_extensions.name
                objetoFinal.push(charge)
                count++
                amount = amount + parseFloat(charge.amount)
                totalAmountExt = totalAmountExt + parseFloat(charge.amount)
              }
            }
          )
          //
          this.chargesCount = count
          this.chargesTotal = amount
        }
      }
      return {
        arrayCharge: objetoFinal,
        amountDid: totalAmountDID,
        amountExt: totalAmountExt,
      }
    },

    async getTotalProrrate(dataDid = [], dataExt = []) {
      let totalProrateExt = 0
      let totalProrateDid = 0
      if (dataExt.length > 0) {
        totalProrateExt = dataExt.reduce((acc, cur) => {
          if (!cur.invoice_prorate) {
            return acc + parseFloat(cur.prorate)
          } else {
            return acc
          }
        }, 0)
      }

      if (dataDid.length > 0) {
        totalProrateDid = dataDid.reduce((acc, cur) => {
          if (!cur.invoice_prorate) {
            return acc + parseFloat(cur.prorate)
          } else {
            return acc
          }
        }, 0)
      }
      // sumar los dos totales
      return totalProrateExt + totalProrateDid
    },

    async aditionalChargesAmount(pbxPackage) {
      let amountExt = 0
      let amountDid = 0
      let amountExtTotal = 0
      let amountDidTotal = 0
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
        if (charge.status) {
          amount += parseFloat(charge.amount)
        }
      })

      return amount
    },

    async discountCalc(discount, preSubTotal) {
      /*console.log(
        'Iniciando discountCalc con descuento:',
        discount,
        'y preSubTotal:',
        preSubTotal
      )*/

      const allow_discount_type =
        this.corePbxServicesIncludedData.allow_discount_type
      //  console.log('Tipo de descuento permitido:', allow_discount_type)

      const service = this.parameters
      let discountValue = 0
      //console.log('Valor inicial del descuento:', discountValue)

      //   discountbegin: null,
      // discountend: null,
      // showdiscountwarning:false,

      if (service.allow_discount) {
        // console.log('Descuentos permitidos por el servicio')

        switch (allow_discount_type) {
          case 'percentage':
            discountValue = preSubTotal * (discount / 100)
            //    console.log('Descuento por porcentaje aplicado:', discountValue)
            break
          case 'fixed':
            discountValue = discount * 100
            //  console.log('Descuento fijo aplicado:', discountValue)
            break
        }
        //console.log('tipo de descuento: ',service.time_period_value)
        //console.log('date_to: ',service.date_to)
        //console.log('date_from: ',service.date_from)
        //console.log('discount_start_date: ',service.discount_start_date)
        //console.log('discount_end_date: ',service.discount_end_date)

        if (
          service.time_period_value === 'D' &&
          service.discount_start_date &&
          service.discount_end_date
        ) {
          //console.log('El período de tiempo del servicio es por fechas')
          const date = new Date().toISOString().slice(0, 10)
          //console.log('Fecha actual para comparación:', date)
          /*console.log(
            'Fecha de inicio de descuento:',
            service.discount_start_date
          )*/
          // console.log('Fecha de fin de descuento:', service.discount_end_date)
          this.discountbegin = service.discount_start_date
          this.discountend = service.discount_end_date
          if (
            date >= service.discount_start_date &&
            date <= service.discount_end_date
          ) {
            //console.log('La fecha actual está dentro del rango de descuento')
            this.showdiscountwarning = false
          } else {
            /*console.log(
              'La fecha actual no está dentro del rango de descuento, no se aplica descuento'
            )*/
            this.showdiscountwarning = true
            discountValue = 0
          }
        }

        if (service.time_period_value === 'T') {
          /*console.log(
            'El período de tiempo del servicio es por un período específico'
          )*/
          const today = new Date().toISOString().slice(0, 10)
          //console.log('Fecha de hoy para comparación:', today)

          let dateFrom = new Date(service.date_begin).toISOString().slice(0, 10)
          let dateTo = new Date(dateFrom)

          switch (service.time_period_type) {
            case 'Days':
              dateTo.setDate(dateTo.getDate() + service.time_period)
              break
            case 'Weeks':
              dateTo.setDate(dateTo.getDate() + service.time_period * 7)
              break
            case 'Months':
              dateTo.setMonth(dateTo.getMonth() + service.time_period)
              break
            case 'Years':
              dateTo.setFullYear(dateTo.getFullYear() + service.time_period)
              break
            default:
              // console.log('Tipo de período de tiempo no reconocido')
              break
          }

          //console.log('dateFrom: ', dateFrom)
          //console.log('dateTo: ', dateTo.toISOString().slice(0, 10))

          this.discountbegin = dateFrom
          this.discountend = dateTo.toISOString().slice(0, 10)

          if (today >= dateFrom && today <= dateTo.toISOString().slice(0, 10)) {
            /*console.log(
              'Hoy está dentro del rango de fechas del período de tiempo'
            )*/
            this.showdiscountwarning = false
          } else {
            /*console.log(
              'Hoy no está dentro del rango de fechas del período de tiempo, no se aplica descuento'
            )*/
            this.showdiscountwarning = true
            discountValue = 0
          }
        }
      } else {
        // console.log('No se permiten descuentos por el servicio')
      }

      //console.log('Valor final del descuento:', discountValue)
      return discountValue
    },

    // calcula monto de cada impuesto en base al subtotal
    taxAmountCalc(subtotal) {
      // console.log('taxAmountCalc iniciado con subtotal:', subtotal)
      let amount = 0
      let arrayLength = this.pbxService.taxes.length
      //console.log('Número de impuestos a calcular:', arrayLength)

      this.pbxService.taxes.forEach((element, index) => {
        /*console.log(
          `Calculando impuesto ${index + 1}/${arrayLength} con porcentaje:`,
          element.percent
        )*/
        element.amount = Math.round((element.percent * subtotal) / 100)
        /*console.log(
          `Impuesto calculado para el elemento ${index + 1}:`,
          element.amount
        )*/
      })

      // console.log('taxAmountCalc finalizado')
    },
    // suma total de los montos de cada impuesto
    async taxesCalc(taxes) {
      let amount = 0
      taxes.forEach((tax) => {
        amount += parseFloat(tax.amount)
      })
      return Math.round(amount)
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
      this.isLoading = true
      let resPbxService
      // request object
      this.parameters.items = this.corePbxServicesIncludedData.items
      this.parameters.dids = this.corePbxServicesIncludedData.did
      this.parameters.extensions = this.corePbxServicesIncludedData.ext
      this.parameters.allow_discount_value =
        this.corePbxServicesIncludedData.discount_value
      this.parameters.allow_discount_type =
        this.corePbxServicesIncludedData.discount_calc
      this.parameters.pbxpackages_price = this.price
      this.parameters.cap_extension =
        this.corePbxServicesIncludedData.cap_extension
      this.parameters.generateinvoice =
        this.corePbxServicesIncludedData.generateinvoice
      this.parameters.cap_total = this.capTotal
      this.parameters.inclusive_minutes_seconds_consumed = this.capTotal * 60
      this.parameters.taxes = this.pbxService.taxes
      this.parameters.sub_total = this.subtotal
      this.parameters.total = this.total
      this.parameters.total_prorate = this.totalProrrate
      this.parameters.tax = this.totalTax
      this.parameters.taxCdr = this.corePbxServicesIncludedData.taxCdr
      this.parameters.taxesCdr = this.corePbxServicesIncludedData.taxesCdr
      this.parameters.only_callrating = this.parameters.package.all_cdrs
        ? true
        : false
      this.parameters.invoice_prorate =
        this.corePbxServicesIncludedData.invoice_prorate
      this.parameters.pbx_services_app_rate =
        this.corePbxServicesIncludedData.pbx_services_app_rate
      this.parameters.additionalChargesData = this.additionalChargesData

      this.parameters.date_to = this.parameters.discount_end_date
      this.parameters.date_from = this.parameters.discount_start_date
      this.parameters.discount_val = this.discountapplied
      let message = this.isEdit
        ? this.$t('services.confirm_update')
        : this.$t('general.are_you')

      swal({
        title: message,
        text: this.$t('general.cdr_load'),
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
            let data = { parameters: this.parameters }
            //  console.log(data)
            resPbxService = await this.updatePBXServices(data)
          } else {
            //console.log(this.parameters)
            resPbxService = await this.setCorePBXServices(this.parameters)
          }

          if (resPbxService.data.success) {
            window.toastr['success'](resPbxService.data.message)
            // push view

            if (this.isEdit) {
              this.$router.push(
                `/admin/customers/${this.$route.params.id}/pbx-service/${resPbxService.data.service.id}/view`
              )
            } else {
              this.$router.push(
                `/admin/customers/${this.$route.params.id}/pbx-service/${resPbxService.data.pbxService.id}/view`
              )
            }
          } else {
            window.toastr['error'](resPbxService.data.message)
          }
        }
        this.isLoading = false
      })
    },

    // go to back
    async back() {
      this.$emit('back')
      this.package = ''
      this.isLoading = false
    },

    calculateItemTaxes() {
      let result = []
      const items = this.pbxService.itemsIncluded
      items.forEach((item) => {
        item.taxes.forEach((tax) => {
          if (tax.amount != null) {
            result.push(tax)
          }
        })
      })
      this.itemTaxes = result
      this.groupTaxes(this.itemTaxes)
      //this.totalItems = this.total_items_price + this.totalItemsTaxes
      this.total = this.total + this.totalItemsTaxes
      this.totalItems = this.total_items_price
    },

    groupTaxes(taxes) {
      this.totalItemsTaxes = taxes.reduce((total, item) => {
        return total + item.amount / 100
      }, 0)
    },
  },
}
</script>

<style>
.mb-10 {
  margin-bottom: -10px;
}
</style>