<template>
  <base-page>
    <form action="" @submit.prevent="submitPackage">
      <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
      <sw-page-header :title="pageTitle">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />
          <sw-breadcrumb-item
            :title="$tc('packages.package_name', 2)"
            to="/admin/packages"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'packages.edit'"
            :title="$t('packages.edit_package')"
            to="#"
            active
          />
          <sw-breadcrumb-item
            v-else
            :title="$t('packages.new_package')"
            to="#"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>

      <sw-card>
        <!-- Basic  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.basic') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <h1 class="md:col-span-6" v-text="$t('packages.lang_name')"></h1>

            <sw-input-group
              :label="$t('packages.name_package')"
              :error="nameError"
              class="md:col-span-3"
              required
            >
              <sw-input
                v-if="!isCopy"
                v-model="formData.name"
                :invalid="$v.formData.name.$error"
                focus
                type="text"
                name="name"
                tabindex="1"
                @input="$v.formData.name.$touch()"
              />
              <sw-input
                v-else
                :value="formData.name + ' - Copy'"
                :invalid="$v.formData.name.$error"
                focus
                type="text"
                name="name"
                v-model="formData.name"
                tabindex="1"
                @input="$v.formData.name.$touch()"
              />
            </sw-input-group>

            <div class="tabs mb-5 grid md:col-span-6">
              <div class="border-b tab">
                <div class="border-l-2 border-transparent relative">
                  <input
                    class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-6"
                    type="checkbox"
                    id="chck1"
                    tabindex="1"
                  />
                  <header
                    class="col-span-5 flex justify-between items-center p-3 pl-0 pr-8 cursor-pointer select-none tab-label"
                    for="chck1"
                  >
                    <span class="text-grey-darkest font-thin text-xl">
                      {{ $t('packages.description') }}
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
                  <div class="tab-content">
                    <div class="pl-0 pr-8 pb-5 text-grey-darkest">
                      <ul class="pl-0">
                        <li class="pb-2">
                          <sw-tabs :active-tab="activeTab">
                            <sw-tab-item title="HTML">
                              <base-custom-input
                                v-model="formData.descriptionHTML"
                                :fields="[]"
                              />
                            </sw-tab-item>
                            <sw-tab-item title="Text">
                              <base-custom-input
                                v-model="formData.descriptionText"
                                :fields="[]"
                              />
                            </sw-tab-item>
                          </sw-tabs>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>

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
                :tabindex="3"
                :allow-empty="true"
                :placeholder="$t('general.select_status')"
                label="text"
                track-by="value"
                @input="$v.formData.status_payment.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('packages.status')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="formData.status"
                :invalid="$v.formData.status.$error"
                :options="status"
                :searchable="true"
                :show-labels="false"
                :tabindex="4"
                :allow-empty="true"
                :placeholder="$t('general.select_status')"
                label="text"
                track-by="value"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('packages.qty')"
              class="md:col-span-3"
              :error="qtyError"
            >
              <sw-input
                v-model="qty"
                :invalid="$v.qty.$error"
                focus
                type="text"
                name="qty"
                :placeholder="$t('packages.unlimited')"
                tabindex="5"
                @input="$v.qty.$touch()"
                numeric
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('packages.client_qty')"
              class="md:col-span-3"
              :error="clientQtyError"
            >
              <sw-input
                v-model="client_qty"
                :invalid="$v.client_qty.$error"
                focus
                type="text"
                name="client_qty"
                :placeholder="$t('packages.unlimited')"
                tabindex="6"
                @input="$v.client_qty.$touch()"
              />
            </sw-input-group>

            <!-- <sw-divider class="my-0 md:col-span-6 opacity-0" /> -->

            <!-- <div class="flex mt-2 col-span-12">
              <div class="relative w-12">
                <sw-switch
                  v-model="upgrades_use_renewal"
                  class="absolute"
                  style="top: -20px"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('packages.upgrades_use_renewal') }}
                </p>

                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('packages.upgrades_use_renewal_description') }}
                </p>
              </div>
            </div> -->
          </div>
        </div>

        <sw-divider class="mb-5 md:mb-8" />

        <!-- Group  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.title_group') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <div class="md:col-span-3 md:mt-12">
              <h6>{{ $t('packages.member_groups') }}</h6>
              <div
                class="grid gap-6 grid-col-1 md:grid-cols-2"
                style="
                  height: 100px;
                  overflow-y: scroll;
                  border: 1px solid;
                  border-radius: 5px;
                  border-color: #cbd5e0;
                  border-width: 1px;
                  border-style: solid;
                "
              >
                <ul>
                  <li
                    v-for="(item, index) in groupLeft"
                    :key="item.id"
                    @click="moveToRight(item, index)"
                  >
                    <div class="cursor-pointer">
                      {{ item.name }}
                    </div>
                  </li>
                </ul>
              </div>
            </div>

            <!-- <div style="text-align: center;padding: 20px;">
                  <div>
                    <left-arrow/>
                  </div>
                  <div>
                    <right-arrow/>
                  </div>
              </div> -->

            <div class="md:col-span-3">
              <div
                class="
                  flex
                  items-center
                  justify-center
                  w-full
                  px-6
                  py-3
                  text-base
                  border-b border-gray-200 border-solid
                  cursor-pointer
                  text-primary-400
                  hover:bg-gray-200
                "
                @click="createPackageGroup"
                tabindex="7"
              >
                {{ $t('packages.modal.button') }}
              </div>
              <h6>{{ $t('packages.available_groups') }}</h6>
              <div
                class="grid gap-6 grid-col-1 md:grid-cols-2"
                style="
                  height: 100px;
                  overflow-y: scroll;
                  border: 1px solid;
                  border-radius: 5px;
                  border-color: #cbd5e0;
                  border-width: 1px;
                  border-style: solid;
                "
              >
                <ul>
                  <li
                    v-for="(item, index) in groupRight"
                    :key="item.id"
                    @click="moveToLeft(item, index)"
                  >
                    <div class="cursor-pointer">
                      {{ item.name }}
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <sw-divider class="mb-5 md:mb-8" />

        <!-- Packages Group Tax -->
        <div class="grid grid-cols-5 gap-4 mb-8" v-if="false">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.title_tax_group') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <sw-divider class="col-span-12" />
            <sw-table-component
              class="col-span-12"
              ref="table"
              :show-filter="false"
              :data="formData.groupLeftTax"
              table-class="table"
              variant="gray"
            >
              <sw-table-column
                :sortable="true"
                :label="$t('packages.packages_group_tax.name')"
                show="name"
              >
                <template slot-scope="row">
                  <span>{{ $t('packages.packages_group_tax.name') }}</span>
                  <span class="mt-6">{{ row.name }}</span>
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :label="$t('packages.packages_group_tax.description')"
                show="description"
              >
                <template slot-scope="row">
                  <span>{{
                    $t('packages.packages_group_tax.description')
                  }}</span>
                  <span class="mt-6">{{ row.description }}</span>
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :filterable="true"
                :label="$t('packages.packages_group_tax.status')"
              >
                <template slot-scope="row">
                  <span>{{ $t('packages.packages_group_tax.status') }}</span>
                  <sw-badge
                    :bg-color="
                      $utils.getBadgeStatusColor(row.status ? 'YES' : 'NO')
                        .bgColor
                    "
                    :color="
                      $utils.getBadgeStatusColor(row.status ? 'YES' : 'NO')
                        .color
                    "
                  >
                    {{ row.status ? 'Active' : 'Unactive'.replace('_', ' ') }}
                  </sw-badge>
                </template>
              </sw-table-column>

              <sw-table-column :sortable="true" :filterable="true">
                <template slot-scope="row">
                  <trash-icon
                    @click="removeGroupTax(row)"
                    class="h-5 mr-3 text-gray-600"
                  />
                </template>
              </sw-table-column>
            </sw-table-component>
            <div class="col-span-12"></div>
            <sw-input-group
              :label="$t('packages.member_tax_groups')"
              class="md:col-span-3"
              required
            >
              <sw-select
                v-model="groupLeftTaxModel"
                :options="groupLeftTax"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$tc('packages.add_group_tax')"
                class="mt-2"
                label="name"
                track-by="id"
                @select="groupLeftTaxSeleted"
              />
            </sw-input-group>
          </div>
        </div>

        <!-- Group discount -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.title_discount') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <div class="col-span-6" v-if="false">
              <sw-divider class="col-span-12" />
              <div class="flex mt-3 mb-4">
                <div class="relative w-12">
                  <sw-switch
                    v-model="packages_discount_none_status"
                    class="absolute"
                    style="top: -20px"
                    @change="onNoneDiscounts"
                    tabindex="9"
                  />
                </div>
                <div class="ml-4">
                  <p
                    class="p-0 mb-1 text-base leading-snug text-black box-title"
                  >
                    Apply none discount
                  </p>
                </div>
              </div>
            </div>

            <div class="col-span-3">
              <div class="flex mt-3 mb-4">
                <div class="relative w-12">
                  <sw-switch
                    :disabled="disableValueDiscounts"
                    v-model="formData.packages_discount"
                    class="absolute"
                    style="top: -20px"
                    @change="onDiscounts"
                    tabindex="10"
                  />
                </div>
                <div class="ml-4">
                  <p
                    class="p-0 mb-1 text-base leading-snug text-black box-title"
                  >
                    {{ $t('packages.apply_general_discount') }}
                  </p>
                </div>
              </div>
            </div>
            <div class="col-span-3">
              <div class="flex mt-3 mb-4">
                <div class="relative" style="width: 8em">
                  <label>
                    {{ $t('packages.title_discounts') }}
                  </label>
                  <sw-select
                    :disabled="showSelectdiscounts"
                    v-model="formData.discounts"
                    :options="discounts"
                    :searchable="true"
                    :show-labels="false"
                    :tabindex="11"
                    :allow-empty="true"
                    :placeholder="$t('packages.title_discounts')"
                    label="text"
                    track-by="value"
                  />
                </div>
                <div class="relative" style="width: 8em; margin-left: 30px">
                  <label>
                    {{ $t('packages.value_discount') }}
                  </label>
                  <sw-input
                    :disabled="showSelectdiscounts"
                    v-model="value_discount"
                    focus
                    type="text"
                    name="qty"
                    :placeholder="$t('packages.value_discount')"
                    tabindex="12"
                    @input="$v.qty.$touch()"
                    numeric
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <sw-divider class="mb-5 md:mb-8" />

        <!-- Taxes -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.taxes') }}
          </h6>

          <div class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6">
            <!--  <div class="col-span-3">
              <div class="flex mt-3">
                <div class="relative">
                  <label>
                    {{ $t('packages.apply_tax_type_by') }}
                  </label>
                  <sw-select
                    v-model="formData.apply_tax_type"
                    :options="appy_tax_type_options"
                    :searchable="true"
                    :show-labels="false"
                    :tabindex="16"
                    :allow-empty="true"
                    :placeholder="$t('packages.apply_tax_type_by')"
                    label="text"
                    track-by="value"
                    @select="onSelectApplyTaxType"
                  />
                </div>
              </div>
            </div> -->
            <sw-table-component
              class="md:col-span-6"
              ref="table"
              :show-filter="false"
              :data="paralelo"
              table-class="table"
              variant="gray"
            >
              <sw-table-column
                :sortable="true"
                :label="$t('settings.tax_types.tax_name')"
                show="name"
              >
                <template slot-scope="row">
                  <span>{{ $t('settings.tax_types.tax_name') }}</span>
                  <span class="mt-6">{{ row.name }}</span>
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :filterable="true"
                :label="$t('settings.tax_types.compound_tax')"
              >
                <template slot-scope="row">
                  <span>{{ $t('settings.tax_types.compound_tax') }}</span>
                  <sw-badge
                    :bg-color="
                      $utils.getBadgeStatusColor(
                        row.compound_tax ? 'YES' : 'NO'
                      ).bgColor
                    "
                    :color="
                      $utils.getBadgeStatusColor(
                        row.compound_tax ? 'YES' : 'NO'
                      ).color
                    "
                  >
                    {{ row.compound_tax ? 'Yes' : 'No'.replace('_', ' ') }}
                  </sw-badge>
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :filterable="true"
                :label="$t('settings.tax_types.percent')"
              >
                <template slot-scope="row">
                  <span>{{ $t('settings.tax_types.percent') }}</span>
                  {{ row.percent }} %
                </template>
              </sw-table-column>

              <sw-table-column :sortable="true" :filterable="true">
                <template slot-scope="row">
                  <trash-icon
                    @click="removeTax(row)"
                    class="h-5 mr-3 text-gray-600"
                  />
                </template>
              </sw-table-column>
            </sw-table-component>

            <sw-input-group
              :label="$t('packages.taxes')"
              class="md:col-span-3">
                <sw-select
                  v-model="tax"
                  :options="taxes"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :placeholder="$tc('packages.add_tax')"
                  class="mt-2"
                  label="name_por"
                  track-by="id"
                  @select="taxSeleted"
                />
            </sw-input-group>

            <sw-input-group
              :label="$t('packages.member_tax_groups')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="groupTax"
                :options="groupTaxes"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$tc('packages.add_group_tax')"
                class="mt-2"
                label="name"
                track-by="id"
                @select="groupTaxSeleted"
              />
            </sw-input-group>
          </div>
        </div>

        <sw-divider class="mb-5 md:mb-8" />

        <!-- Items -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.packages_items') }}
          </h6>
          <div class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6">
            <sw-input-group
              :label="$t('packages.item_groups')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="item_group"
                :options="item_groups"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$tc('packages.item_groups_select')"
                class="mt-2"
                label="name"
                track-by="id"
                @select="itemGroupSelected"
              />
            </sw-input-group>

            <div
              class="md:col-span-6"
              v-if="
                undefined !== formData.item_groups &&
                formData.item_groups.length > 0
              "
            >
              <div class="flex flex-wrap justify-start">
                <div
                  v-for="(item, index) in formData.item_groups"
                  :key="index"
                  class="
                    flex
                    justify-center
                    items-center
                    m-1
                    font-medium
                    py-1
                    px-2
                    bg-white
                    rounded-full
                    text-indigo-100
                    bg-indigo-700
                    border border-indigo-700
                  "
                >
                  <div
                    class="
                      text-xs text-base
                      leading-none
                      max-w-full
                      flex-initial
                      py-2
                      pl-2
                    "
                    v-text="item.name"
                  />
                  <div class="flex flex-auto flex-row-reverse">
                    <div>
                      <svg
                        @click="removeItemGroup(item)"
                        xmlns="http://www.w3.org/2000/svg"
                        width="100%"
                        height="100%"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="4"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="
                          feather feather-x
                          cursor-pointer
                          hover:text-indigo-400
                          rounded-full
                          w-6
                          h-4
                          ml-2
                          pr-1
                        "
                      >
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="md:col-span-6 overflow-x-auto">
              <!-- Items -->
              <table class="w-full text-center item-table">
                <colgroup>
                  <col style="width: 40%" />
                  <col style="width: 10%" />
                  <col style="width: 15%" />
                  <col v-if="discountPerItem === 'YES'" style="width: 20%" />
                  <col style="width: 15%" />
                </colgroup>
                <thead class="bg-white border border-gray-200 border-solid">
                <tr>
                  <th
                    class="
                        px-5
                        py-3
                        text-sm
                        not-italic
                        font-medium
                        leading-5
                        text-left text-gray-700
                        border-t border-b border-gray-200 border-solid
                      "
                  >
                      <span class="pl-12">
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
                        border-t border-b border-gray-200 border-solid
                      "
                  >
                      <span>
                        {{ $t('estimates.item.quantity') }}
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
                        border-t border-b border-gray-200 border-solid
                      "
                  >
                      <span>
                        {{ $t('estimates.item.price') }}
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
                        border-t border-b border-gray-200 border-solid
                      "
                  >
                      <span>
                        {{ $t('estimates.item.discount') }}
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
                        border-t border-b border-gray-200 border-solid
                      "
                  >
                      <span class="pr-10 column-heading">
                        {{ $t('estimates.item.amount') }}
                      </span>
                  </th>
                </tr>
                </thead>
                <draggable
                  v-model="formData.items"
                  class="item-body"
                  tag="tbody"
                  handle=".handle"
                >
                  <package-item
                    v-for="(item, index) in formData.items"
                    :key="item.id"
                    :index="index"
                    :item-data="item"
                    :currency="currency"
                    :isView="false"
                    :isNoGeneralTaxes="isTaxPerItem"
                    :package-items="formData.items"
                    :tax-per-item="taxPerItem"
                    :discount-per-item="discountPerItem"
                    @remove="removeItem"
                    @update="updateItem"
                    @itemValidate="checkItemsData"
                  />
                </draggable>
              </table>
              <div
                class="
                  flex
                  items-center
                  justify-center
                  w-full
                  px-6
                  py-3
                  text-base
                  border-b border-gray-200 border-solid
                  cursor-pointer
                  text-primary-400
                  hover:bg-gray-200
                "
                @click="addItem"
              >
                <shopping-cart-icon class="h-5 mr-2" />
                {{ $t('estimates.add_item') }}
              </div>
            </div>
          </div>
        </div>

        <!-- Amount -->
        <div class="grid grid-cols-5 gap-4 mb-8" v-if="false">
          <sw-divider class="col-span-12" />

          <h6 class="col-span-5 sw-section-title lg:col-span-1"></h6>
          <div
            class="
              col-span-5
              lg:col-span-4
              gap-y-6 gap-x-4
              md:grid-cols-6
              flex
              wrap
              content-end
              justify-end
            "
          >
            <div
              class="
                px-5
                py-4
                mt-6
                bg-white
                border border-gray-200 border-solid
                rounded
                package-total
                lg:mt-0
              "
            >
              <div class="flex items-center justify-between w-full">
                <label
                  class="
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                  >{{ $t('estimates.sub_total') }}</label
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
                  <div v-html="$utils.formatMoney(subtotal, currency)" />
                </label>
              </div>
              <template v-if="true">
                <template v-if="false">
                  <div
                    v-for="tax in allTaxes"
                    :key="tax.tax_type_id"
                    class="flex items-center justify-between w-full"
                  >
                    <label
                      class="
                        m-0
                        text-sm
                        font-semibold
                        leading-5
                        text-gray-500
                        uppercase
                      "
                      >{{ tax.name }} - {{ tax.percent }}%
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
                      style="font-size: 18px"
                    >
                      <div v-html="$utils.formatMoney(tax.amount, currency)" />
                    </label>
                  </div>
                </template>
                <template v-if="!isTaxPerItem">
                  <div
                    class="
                      flex
                      items-center
                      content-center
                      justify-between
                      w-full
                      mt-2
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
                      >{{ $t('packages.general_taxes') }}</label
                    >
                  </div>
                  <div
                    v-for="(tax, index) in formData.taxes"
                    :key="index"
                    class="
                      flex
                      items-center
                      content-center
                      justify-between
                      w-full
                      mt-2
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
                      v-text="`${tax.name} - ${tax.percent}%`"
                    />
                    <label
                      class="
                        flex
                        items-center
                        justify-center
                        m-0
                        text-lg text-black
                        uppercase
                      "
                      style="font-size: 18px"
                    >
                      <div
                        v-html="
                          $utils.formatMoney(
                            calTax(tax.percent, subtotal),
                            currency
                          )
                        "
                      />
                    </label>
                  </div>
                </template>
                <div
                  v-if="!showSelectdiscounts"
                  class="
                    flex
                    items-center
                    content-center
                    justify-between
                    w-full
                    mt-2
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
                    >{{ $t('packages.general_discounts') }}</label
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
                    style="font-size: 18px"
                  >
                    <div
                      v-html="
                        `${parseFloat(value_discount).toFixed(2)} ${
                          formData.discounts.value == 'fixed' ? '' : '%'
                        }`
                      "
                    />
                  </label>
                </div>
              </template>
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
                    m-0
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                  >{{ $t('estimates.total') }}
                  {{ $t('estimates.amount') }}:</label
                >
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
                  <div v-html="$utils.formatMoney(total, currency)" />
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-6 mb-4">
          <sw-button
            :loading="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="flex justify-center w-full md:w-auto"
          >
            <save-icon class="mr-2 -ml-1" v-if="!isLoading" />
            {{
              isEdit
                ? $t('packages.update_package')
                : $t('packages.save_package')
            }}
          </sw-button>
        </div>
      </sw-card>
    </form>
  </base-page>
</template>

<script>
import RightArrow from '@/components/icon/RightArrow'
import MoreIcon from '@/components/icon/MoreIcon'
import LeftArrow from '@/components/icon/LeftArrow'
import draggable from 'vuedraggable'
import PackageItem from './Item'
import PackageStub from '../../stub/package'
import Guid from 'guid'
import TaxStub from '../../stub/tax'

import { mapActions, mapGetters } from 'vuex'
import {
  TrashIcon,
  ChevronDownIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
} from '@vue-hero-icons/solid'
const {
  required,
  minLength,
  between,
  email,
  numeric,
  minValue,
  maxLength,
  requiredIf,
} = require('vuelidate/lib/validators')
export default {
  components: {
    MoreIcon,
    draggable,
    PackageItem,
    ChevronDownIcon,
    TrashIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusIcon,
    RightArrow,
    LeftArrow,
  },
  data() {
    return {
      item_group: null,
      itemGroupsFetch: [],
      item_groups: [],
      discountPerItemStore: null,
      recomputeTotal: false,
      discountPerItem: null,
      taxPerItem: null,
      selectedCurrency: '',
      disableValueDiscounts: false,
      showSelectdiscounts: true,
      tax: null,
      taxesFetch: [],
      taxes: [],
      itemsF: [],
      isNoGeneralTaxes: false,
      isRequestOnGoing: false,
      groupRight: [],
      groupTax: null,
      groupTaxes: [],
      groupLeft: [],
      groupLeftTax: [],
      groupLeftTaxFetch: [],
      groupLeftTaxModel: null,
      isLoading: false,
      discount_type: 'fixed',
      discount_val: 0,
      EstimateFields: [],
      paralelo: [],
      activeTab: 'Text',
      appy_tax_type_options: [
        {
          value: 'none',
          text: 'None',
        },
        {
          value: 'item',
          text: 'Item',
        },
        {
          value: 'general',
          text: 'General',
        },
      ],
      discounts: [
        {
          value: 'fixed',
          text: 'Fixed',
        },
        {
          value: 'percentage',
          text: 'Percentage',
        },
      ],
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
          value: 'R',
          text: 'Restricted',
        },
      ],
      status_payment: [
        { value: 'R', text: 'Prepaid' },
        { value: 'O', text: 'Postpaid' },
      ],
      client_qty: '',
      qty: '',
      value_discount: 0,
      discount: 0,
      upgrades_use_renewal: false,
      packages_discount_none_status: false,
      apply_tax_type_pre: {
        value: 'general',
        text: 'General',
      },
      formData: {
        id: null,
        apply_tax_type: 'general',
        apply_discount_type: null,
        discount_val: 0,
        packages_discount_none: false,
        packages_discount: false,
        groupLeftTax: [],
        item_groups: [],
        name: this.isCo,
        descriptionHTML: null,
        descriptionText: null,
        status_payment: { value: 'R', text: 'Prepaid' },
        password: null,
        phone: null,
        status: {
          value: 'A',
          text: 'Active',
        },
        discounts: {
          value: 'fixed',
          text: 'Fixed',
        },
        client_qty: null,
        qty: null,
        upgrades_use_renewal: false,
        taxes: [],
        items: [],
        itemsPre: [
          {
            ...PackageStub,
            id: Guid.raw(),
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          },
        ],
      },
    }
  },
  computed: {
    ...mapGetters('pack', { packageNameGroup: 'packageNameGroup' }),
    allTaxes() {
      let taxes = []

      this.formData.items.forEach((item) => {
        item.no_taxable = item.no_taxable
        item.taxes.forEach((tax) => {
          let found = taxes.find((_tax) => {
            return _tax.tax_type_id === tax.tax_type_id
          })

          if (found) {
            found.amount += tax.amount
          } else if (tax.tax_type_id) {
            taxes.push({
              tax_type_id: tax.tax_type_id,
              amount: tax.amount,
              percent: tax.percent,
              name: tax.name,
            })
          }
        })
      })

      return taxes
    },

    getListTaxes: function () {
      return this.formData.taxes
    },

    subtotalWithDiscount() {
      if (this.formData.packages_discount) {
        return this.subtotal - this.formData.discount_val
      } else {
        return this.subtotal
      }
    },
    total() {
      let vm = this
      let total = vm.subtotalWithDiscount

      if (vm.formData.apply_tax_type == 'item') {
        total = vm.subtotalWithDiscount + vm.totalTax
      }

      let value_discount = vm.value_discount * 100
      let totalCal = 0
      if (vm.formData.packages_discount) {
        if (vm.formData.discounts.value == 'fixed') {
          totalCal = total - value_discount
        } else {
          let calc = (total * vm.value_discount) / 100
          totalCal = total - calc
        }
      } else {
        totalCal = total
      }

      let taxCalTotal = 0
      if (vm.formData.apply_tax_type == 'general') {
        vm.formData.taxes.forEach((tax) => {
          taxCalTotal += vm.calTax(tax.percent, totalCal)
        })
        totalCal = totalCal + taxCalTotal
      }
      vm.recomputeTotal = false
      return totalCal
    },
    totalTax() {
      /* if (this.taxPerItem === 'NO' || this.taxPerItem === null) {
        return this.totalSimpleTax + this.totalCompoundTax
      } */

      return window._.sumBy(this.formData.items, function (tax) {
        return tax.tax
      })
    },
    subtotal() {
      return this.formData.items.reduce(function (a, b) {
        return a + b['total']
      }, 0)
    },
    currency() {
      return this.selectedCurrency
    },
    pageTitle() {
      if (this.isEdit) {
        return this.$t('packages.edit_package')
      } else if (this.isCopy) {
        return this.$t('packages.copy_package')
      }
      return this.$t('packages.new_package')
    },
    isEdit() {
      if (this.$route.name === 'packages.edit') {
        return true
      }
      return false
    },
    isCopy() {
      if (this.$route.name === 'packages.copy') {
        return true
      }
      return false
    },
    statusPaymentError() {
      if (!this.$v.formData.status_payment.$error) {
        return ''
      }
      if (!this.$v.formData.status_payment.required) {
        return this.$t('validation.required')
      }
    },
    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
    },
    qtyError() {
      if (!this.$v.qty.$error) {
        return ''
      }
      if (!this.$v.qty.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
    clientQtyError() {
      if (!this.$v.client_qty.$error) {
        return ''
      }
      if (!this.$v.client_qty.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
    isTaxPerItem() {
      return this.taxPerItem === 'YES'
    },
  },
  watch: {
    packageNameGroup(val) {
      this.groupRight.push({ name: val })
    },

    paralelo() {},

    getListTaxes: {
      handler: function (val, oldVal) {
        console.log('Lectura de Taxes', val, oldVal)
      },
    },

    subtotal(newValue) {
      if (this.formData.discount_type === 'percentage') {
        this.formData.discount_val = (this.formData.discount * newValue) / 100
      }
    },
  },
  methods: {
    ...mapActions('pack', [
      'addPackage',
      'fetchPackage',
      'fetchItemGroups',
      'updatePackage',
      'fetchGroupMembership',
      'saveGroup',
      'fetchGroupTaxMembership',
      'destroyItemPackagesGroup',
    ]),
    ...mapActions('taxType', [
      'indexLoadData',
      'deleteTaxType',
      'fetchTaxType',
      'fetchTaxTypes',
      'packageGroup',
    ]),

    ...mapGetters('company', ['itemDiscount']),

    ...mapGetters('company', ['defaultCurrency']),

    ...mapActions('item', ['fetchItems']),

    ...mapActions('taxType', ['fetchTaxTypes']),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('modal', ['openModal']),

    calTax(taxPercent, amount) {
      return Math.round((amount * taxPercent) / 100)
    },
    onSelectApplyTaxType(type) {
      this.taxPerItem = 'NO'
      setTimeout(() => {
        if (type.value == 'none' || type.value == 'general') {
          this.taxPerItem = 'YES'
          this.isNoGeneralTaxes = type.value == 'none' ? true : false
          setTimeout(() => {
            this.recomputeTotal = true
            this.taxPerItem = 'NO'
          }, 50)
        } else {
          this.recomputeTotal = true
          this.taxPerItem = 'YES'
          this.isNoGeneralTaxes = true
        }
      }, 50)
    },
    onSelectTax(selectedTax) {
      let amount = 0

      if (selectedTax.compound_tax && this.subtotalWithDiscount) {
        amount = Math.round(
          ((this.subtotalWithDiscount + this.totalSimpleTax) *
            selectedTax.percent) /
            100
        )
      } else if (this.subtotalWithDiscount && selectedTax.percent) {
        amount = Math.round(
          (this.subtotalWithDiscount * selectedTax.percent) / 100
        )
      }

      this.formData.taxes.push({
        ...TaxStub,
        id: Guid.raw(),
        name: selectedTax.name,
        percent: selectedTax.percent,
        compound_tax: selectedTax.compound_tax,
        tax_type_id: selectedTax.id,
        amount,
      })

      if (this.$refs) {
        this.$refs.taxModal.close()
      }
    },

    selectFixed() {
      if (this.formData.discount_type === 'fixed') {
        return
      }

      this.formData.discount_val = Math.round(this.formData.discount * 100)
      this.formData.discount_type = 'fixed'
    },

    selectPercentage() {
      if (this.formData.discount_type === 'percentage') {
        return
      }

      this.formData.discount_val =
        (this.subtotal * this.formData.discount) / 100

      this.formData.discount_type = 'percentage'
    },

    createPackageGroup() {
      this.openModal({
        title: this.$t('packages.modal.button'),
        componentName: 'CreatePackageGroup',
        data: null,
      })
    },
    addItem() {
      if (this.formData.items.length > 0) {
        // if (this.formData.items[0].item_id == null) {
        const isId = (element) => element.item_id == null
        const index = this.formData.items.findIndex(isId)
        // console.log('find tax', index)
        if (index != -1) {
          window.toastr['error']('Select an item before adding another')
          return false
        }
      }
      this.formData.items.push({
        ...PackageStub,
        id: Guid.raw(),
        taxes: [{ ...TaxStub, id: Guid.raw() }],
      })
    },

    removeItem(index) {
      this.formData.items.splice(index, 1)
    },

    updateItem(data) {
      Object.assign(this.formData.items[data.index], { ...data.item })
    },

    checkItemsData(index, isValid) {
      this.formData.items[index].valid = isValid
    },

    async fetchInitialItems() {
      this.isLoadingData = true

      if (!this.isEdit) {
        let response = await this.fetchCompanySettings([
          'discount_per_item',
          'tax_per_item',
        ])

        if (response.data) {
          this.discountPerItemStore = response.data.discount_per_item
          this.discountPerItem = response.data.discount_per_item
          this.taxPerItem = response.data.tax_per_item
          this.formData.apply_tax_type = this.isTaxPerItem ? 'item' : 'general'
        }
      }

      Promise.all([
        this.fetchItems({
          filter: {},
          orderByField: '',
          orderBy: '',
        }),
        this.fetchCompanySettings(['estimate_auto_generate']),
      ])
        .then(async ([res1, res2]) => {
          // console.log('REs 1', res1)
          this.itemsF = res1.data.items.data
          // if (res5.data) {
          //   this.discountPerItem = res5.data.discount_per_item
          //   this.taxPerItem = res5.data.tax_per_item
          // }
        })
        .catch((error) => {
          console.log(error)
        })
    },
    onDiscounts(val) {
      this.showSelectdiscounts = !val
      this.discountPerItem = val ? 'NO' : 'YES'
    },
    onNoneDiscounts(val) {
      this.formData.packages_discount_none = val
      this.disableValueDiscounts = val
      if (val) {
        this.formData.packages_discount = !val
        this.showSelectdiscounts = val
        this.discountPerItem = 'NO'
      } else {
        this.discountPerItem = this.discountPerItemStore
      }
    },
    removeGroupTax(tax) {
      let myArray = this.formData.groupLeftTax

      for (var i = myArray.length - 1; i >= 0; --i) {
        if (myArray[i].id == tax.id) {
          myArray.splice(i, 1)
        }
      }

      this.formData.groupLeftTax = myArray

      const filterByReference = (arr1, arr2) => {
        let res = []
        res = arr1.filter((el) => {
          return !arr2.find((element) => {
            return element.id === el.id
          })
        })
        return res
      }

      this.groupLeftTax = filterByReference(
        this.groupLeftTaxFetch,
        this.formData.groupLeftTax
      )
      this.groupLeftTaxModel = null
      // console.log('Format Taxes', this.formData.groupLeftTax)
    },
    removeTax(tax) {
      // console.log('removeTax', tax)
      let myArray = this.formData.taxes

      console.log('removeTax', myArray)
      // console.log('myArray', myArray)

      for (var i = myArray.length - 1; i >= 0; --i) {
        if (myArray[i].id == tax.id) {
          // console.log('Encontro')
          myArray.splice(i, 1)
        }
      }

      this.formData.taxes = [...myArray]
      this.paralelo = [...myArray]

      /* console.log("removeTax result",this.formData.taxes); */

      const filterByReference = (arr1, arr2) => {
        let res = []
        res = arr1.filter((el) => {
          return !arr2.find((element) => {
            return element.id === el.id
          })
        })
        return res
      }

      this.taxes = filterByReference(this.taxesFetch, this.formData.taxes)
      this.tax = null
      // console.log('Format Taxes', this.formData.taxes)
    },
    removeItemGroup(item) {
      // console.log('removeItemGroup', item)
      let myArray = this.formData.item_groups

      for (var i = myArray.length - 1; i >= 0; --i) {
        if (myArray[i].id == item.id) {
          myArray.splice(i, 1)
        }
      }

      this.formData.item_groups = myArray

      const filterByReference = (arr1, arr2) => {
        let res = []
        res = arr1.filter((el) => {
          return !arr2.find((element) => {
            return element.id === el.id
          })
        })
        return res
      }

      this.item_groups = filterByReference(
        this.itemGroupsFetch,
        this.formData.item_groups
      )
      this.item_group = null

      for (var i = this.formData.items.length - 1; i >= 0; --i) {
        if (this.formData.items[i].item_group_id == item.id) {
          this.formData.items.splice(i, 1)
        }
      }
      // console.log('Format Taxes', this.formData.taxes)
    },
    filterDuplicate(arrayWithDuplicates) {
      const uniqByProp_map = (prop) => (arr) =>
        Array.from(
          arr
            .reduce(
              (acc, item) => (
                item && item[prop] && acc.set(item[prop], item), acc
              ), // using map (preserves ordering)
              new Map()
            )
            .values()
        )

      // usage (still the same):

      const uniqueById = uniqByProp_map('id')

      const unifiedArray = uniqueById(arrayWithDuplicates)
      return unifiedArray
    },
    filterByReference(arr1, arr2) {
      let res = []
      res = arr1.filter((el) => {
        return !arr2.find((element) => {
          return element.id === el.id
        })
      })
      return res
    },
    itemGroupSelected(val) {
      let vm = this
      const isId = (element) => element.id == val.id

      const index = vm.formData.item_groups.findIndex(isId)
      if (index == -1) {
        vm.formData.item_groups.push(val)
      } else {
        window.toastr['error']('This item group was already selected')
        return false
      }

      vm.item_groups = vm.filterByReference(
        vm.itemGroupsFetch,
        vm.formData.item_groups
      )
      vm.formData.item_groups.forEach((item_group) => {
        item_group.items.forEach((item) => {
          item.item_id = item.id
          item.id = Guid.raw()
          ;(item.discount_type = 'fixed'),
            (item.quantity = 1),
            (item.discount_val = 0),
            (item.discount = 0),
            (item.total = item.price),
            (item.totalTax = 0),
            (item.totalSimpleTax = 0),
            (item.totalCompoundTax = 0),
            (item.tax = 0),
            (item.item_group_id = item_group.id)
          item.taxes = [{ ...TaxStub, id: Guid.raw() }]
          vm.formData.items.push(item)
        })
        vm.formData.items = this.filterDuplicate(vm.formData.items)
      })
      setTimeout(() => {
        this.item_group = null
      }, 100)
    },
    groupLeftTaxSeleted(val) {
      const isId = (element) => element.id == val.id
      const index = this.formData.groupLeftTax.findIndex(isId)
      if (index == -1) {
        this.formData.groupLeftTax.push(val)
      } else {
        window.toastr['error']('This group Tax was already selected')
        return false
      }

      this.groupLeftTax = this.filterByReference(
        this.groupLeftTaxFetch,
        this.formData.groupLeftTax
      )

      setTimeout(() => {
        this.groupLeftTaxModel = null
      }, 100)
    },
    groupTaxSeleted(val) {
      const vm = this
      let taxC = []
      const isId = (element) => element.id == val.id
      const index = vm.groupLeftTax.findIndex(isId)
      if (index != -1) {
        // vm.formData.taxes = vm.groupTaxes[index].tax_groups_tax_types; ??
        vm.groupTaxes[index].tax_groups_tax_types.forEach((tax) => {
          taxC = { ...tax, id: tax.pivot.tax_types_id }
          /* vm.formData.taxes.push(taxC) */
          this.formData.taxes.push(taxC)
          this.paralelo.push(taxC)
        })
        this.formData.taxes = this.filterDuplicate(this.formData.taxes)
        /* vm.formData.taxes = vm.filterDuplicate(vm.formData.taxes) */
        this.paralelo = [...this.formData.taxes]

        vm.taxes = vm.filterByReference(
          this.taxes,
          vm.groupTaxes[index].tax_groups_tax_types
        )
      } else {
        vm.formData.taxes = []
        /* vm.formData.taxes = [] */
        this.paralelo = []
      }

      // this.groupLeftTax = this.filterByReference(this.groupLeftTaxFetch, this.formData.groupLeftTax)

      setTimeout(() => {
        vm.groupTax = null
      }, 100)
    },
    taxSeleted(val) {
      const isId = (element) => element.id == val.id
      const index = this.formData.taxes.findIndex(isId)

      /*  console.log("taxSeleted",index); */

      if (index == -1) {
        this.formData.taxes.push(val)
        this.paralelo.push(val)
        /* console.log("taxSeleted Agergado",this.formData.taxes); */
      } else {
        window.toastr['error']('This tax was already selected')
        return false
      }

      this.taxes = this.filterByReference(this.taxesFetch, this.formData.taxes)

      this.recomputeTotal = true
      setTimeout(() => {
        this.tax = null
      }, 100)
    },
    async fetchTax() {
      let data = {
        orderByField: 'created_at',
        orderBy: 'asc',
        limit: 1000,
      }

      let response = await this.fetchTaxTypes(data)

      let taxes = response.data.taxTypes.data
      taxes.forEach((element) => {
        element.name_por = `${element.name} - ${element.percent}%`
      })
      this.taxes = taxes
      this.taxesFetch = taxes
    },
    async fetchInitialItemGroups() {
      let data = {
        orderByField: 'created_at',
        orderBy: 'asc',
      }

      let res = await this.fetchItemGroups(data)

      this.item_groups = res.data.response
      this.itemGroupsFetch = res.data.response
    },
    async loadPackage() {
      let vm = this
      let res = await this.fetchPackage(this.$route.params.id)
      let resGroupRight = await this.fetchGroupMembership()

      this.groupRight = resGroupRight
      this.groupLeft = res.data.response.groupLeft

      vm.formData = res.data.response

      if (this.isEdit || this.isCopy) {
        let statusPackageDiscount =
          res.data.response.packages_discount == 0 ? false : true
        this.onDiscounts(statusPackageDiscount)

        // this.formData.packages_discount = res.data.response.packages_discount
        // this.formData.packages_discount_none = res.data.response.packages_discount_none
        for (var i = 0; i < this.groupLeft.length; i++) {
          for (var j = 0; j < this.groupRight.length; j++) {
            if (this.groupLeft[i].id === this.groupRight[j].id) {
              this.groupRight.splice(j, 1)
            }
          }
        }

        vm.value_discount = res.data.response.discount_general
        vm.formData.discount_val = res.data.response.discount_general

        let discount_general_type = this.discounts.filter((discount) => {
          return discount.value == res.data.response.discount_general_type
        })

        vm.formData.discounts = discount_general_type[0]
      }

      let {
        qty,
        client_qty,
        status,
        status_payment,
        upgrades_use_renewal,
        html,
        text,
        tax_types,
        groupLeftTax,
        items,
        item_groups,
        apply_tax_type,
      } = res.data.response

      // Load Apply Tax Type By
      /*let apply_tax_type_filter = vm.appy_tax_type_options.filter((option) => {
        return option.value == apply_tax_type
      })*/
      vm.formData.apply_tax_type = apply_tax_type
      vm.taxPerItem = apply_tax_type === 'item' ? 'YES' : 'NO'
      vm.recomputeTotal = true
      //vm.onSelectApplyTaxType(apply_tax_type_filter[0])

      const filterByReference = (arr1, arr2) => {
        let res = []
        res = arr1.filter((el) => {
          return !arr2.find((element) => {
            return element.id === el.id
          })
        })
        return res
      }
      this.taxes = filterByReference(this.taxes, tax_types)
      /* console.log("Estos son los taxes",this.taxes) */
      this.groupLeftTax = filterByReference(this.taxes, groupLeftTax)

      let itemsArray = []
      this.formData.items = []
      items.forEach((item) => {
        /* if (item.taxes.length == 0) {
          item.taxes = [{ ...TaxStub, id: Guid.raw() }]
        } */
        item.quantity = item.pivot.quantity
        // item.id = item.pivot.id
        item.item_id = item.pivot.items_id
        item.item_group_id = item.pivot.item_group_id
        item.price = item.pivot.price
        item.discount_type = item.pivot.discount_type
        item.discount = item.pivot.discount
        item.discount_val = item.pivot.discount_val
        item.tax = item.pivot.tax
        item.description = item.pivot.description
        item.package_id = item.pivot.package_id
        item.total = item.pivot.total
        item.totalTax = 0
        item.totalSimpleTax = 0
        item.totalCompoundTax = 0
        item.no_taxable = item.no_taxable

        if (item.taxes.length == 0) {
          this.formData.items.push({
            ...item,
            id: Guid.raw(),
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          })
        } else {
          this.formData.items.push({
            ...item,
          })
        }

        itemsArray.push(item)
      })

      let arrayItemGroups = [],
        arrayItemGroupsItems = [],
        arrayItemGroupsSelect = []

      if (item_groups.length > 0) {
        item_groups.forEach((itemG) => {
          for (let i = 0; i < vm.itemGroupsFetch.length; i++) {
            const element = vm.itemGroupsFetch[i]
            if (element.id === itemG.item_group_id) {
              for (let h = 0; h < itemsArray.length; h++) {
                const item = itemsArray[h]
                if (item.item_group_id == itemG.id) {
                  arrayItemGroupsItems.push(item)
                }
              }
              element.items = arrayItemGroupsItems
              arrayItemGroups.push(element)
            } else {
              arrayItemGroupsSelect.push(element)
            }
          }
        })
        vm.formData.item_groups = arrayItemGroups
        vm.item_groups = arrayItemGroupsSelect
      }

      this.qty = qty
      this.client_qty = client_qty
      ;(this.formData.status = this.status.filter(
        (element) => element.value == status
      )[0]),
        (this.formData.status_payment = this.status_payment.filter(
          (element) => element.value == status_payment
        )[0]),
        (this.formData.descriptionHTML = html)
      this.formData.descriptionText = text

      this.formData.taxes = tax_types
      this.paralelo = [...this.formData.taxes]

      this.upgrades_use_renewal = upgrades_use_renewal == 1 ? true : false

      this.isRequestOnGoing = false
    },
    async submitPackage() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      // Si es diferente a fijo evalua el % mayor a 100
      if (this.formData.discounts.value != 'fixed') {
        if (this.value_discount > 100) {
          window.toastr['error']('Discount value cannot be greater than 100')
          return false
        }
      }
 
      this.formData.status_payment = this.formData.status_payment.value
       
      /* console.log(this.formData.status_payment); */
      this.formData.value_discount = this.value_discount
      //this.apply_tax_type_pre = this.formData.apply_tax_type
      //this.formData.apply_tax_type = this.formData.apply_tax_type.value

      var variable = this.formData.discounts
      if (typeof variable == 'undefined' && variable == null) {
        variable = 'percentage'
      } else {
        variable = variable.value
      }

      this.formData.discount_general_type = variable
      this.formData.discount_general = this.value_discount
      this.formData.groupLeft = this.groupLeft
      this.formData.status = this.formData.status.value
      this.formData.qty = this.qty == '' ? 0 : this.qty
      this.formData.client_qty = this.client_qty == '' ? 0 : this.client_qty
      this.formData.upgrades_use_renewal = this.upgrades_use_renewal ? 1 : 0
      if (this.formData.items.length > 0) {
        // if (this.formData.items[0].item_id == null) {
        const isId = (element) => element.item_id == null
        const index = this.formData.items.findIndex(isId)

        if (index != -1) {
          // window.toastr['error']('Select an item before adding another')
          this.formData.items.splice(index, 1)
        }
      }

      try {
        let res
        this.isLoading = true
        if (this.isEdit) {
          this.formData.id = this.$route.params.id
          res = await this.updatePackage(this.formData)
          window.toastr['success'](res.data.response)
          this.$router.push('/admin/packages')
          return true
        } else {
          res = await this.addPackage(this.formData)
          this.isLoading = false
          if (!this.isEdit) {
            window.toastr['success'](res.data.response)
            this.$router.push('/admin/packages')
            return true
          }
        }
      } catch (error) {
        console.log('Error data', error)
        window.toastr['error'](error.response.data.response)
        //this.formData.apply_tax_type = this.apply_tax_type_pre
        //this.onSelectApplyTaxType(this.apply_tax_type_pre)
        this.status = [
          {
            value: 'A',
            text: 'Active',
          },
          {
            value: 'I',
            text: 'Inactive',
          },
          {
            value: 'R',
            text: 'Restricted',
          },
        ]
        this.formData.status = {
          value: 'A',
          text: 'Active',
        }
        this.isLoading = false
        return false
      }
    },
    moveToLeft(item, index) {
      if (this.isEdit || this.isCopy) {
        item.new = true
      }
      this.groupLeft.push(item)
      this.groupRight.splice(index, 1)
    },
    moveToRight(item, index) {
      this.deleteItemPackagesGroup(item.id)
      this.groupRight.push(item)
      this.groupLeft.splice(index, 1)
    },
    async deleteItemPackagesGroup(PackagesGroupId) {
      await this.destroyItemPackagesGroup({
        packagesGroupId: PackagesGroupId,
        packagesId: this.$route.params.id,
      })
    },
    async loadGroupMembership() {
      this.groupRight = await this.fetchGroupMembership()
    },
    async loadGroupTax() {
      this.groupTaxes = await this.fetchGroupTaxMembership()
      this.groupLeftTax = await this.fetchGroupTaxMembership()
      this.groupLeftTaxFetch = await this.fetchGroupTaxMembership()
    },
  },
  mounted() {
    this.fetchTax()
    this.fetchInitialItemGroups()
    this.fetchInitialItems()
    if (this.isEdit || this.isCopy) {
      this.isRequestOnGoing = true
      this.loadPackage()
    }
    this.loadGroupMembership()
    this.loadGroupTax()
    /*setTimeout(() => {
      this.onSelectApplyTaxType(this.formData.apply_tax_type)
    }, 500)*/
  },
  validations() {
    return {
      qty: {
        numeric,
      },
      client_qty: {
        numeric,
      },
      formData: {
        name: {
          required,
          minLength: minLength(3),
        },
        discount_val: {
          between: between(0, this.subtotal),
        },
        status: {
          required,
        },
        client_qty: {
          numeric,
        },
        status_payment: {
          required,
        },
      },
    }
  },
}
</script>


<style lang="scss">
.package-create-page {
  .package-foot {
    .package-total {
      min-width: 390px;
    }
  }
  @media (max-width: 480px) {
    .package-foot {
      .package-total {
        min-width: 384px;
      }
    }
  }
}

// Dropdown
.tab {
  overflow: hidden;
}
.tab-content {
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
input:checked ~ .tab-content {
  max-height: 100vh;
}
.package-total {
  min-width: 390px;
}
</style>