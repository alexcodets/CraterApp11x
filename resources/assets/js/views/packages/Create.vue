<style>
.table-responsive-item2 {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.tablemin {
  min-width: 900px;
}

/* Additional media query for finer control (optional) */
@media (max-width: 768px) {
  .table-responsive-item2 {
    /* Adjust table width as needed for smaller screens */
    width: 100%; /* Example adjustment */
  }
}
</style>

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

        <template slot="actions">
          <sw-button
            variant="primary-outline"
            type="button"
            size="lg"
            class="mr-3 text-sm hidden sm:flex"
            @click="cancelForm"
          >
            <x-circle-icon class="w-6 h-6 mr-1 -ml-2" v-if="!isLoading" />
            {{ $t('general.cancel') }}
          </sw-button>

          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="hidden sm:flex"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{
              isEdit
                ? $t('packages.update_package')
                : $t('packages.save_package')
            }}
          </sw-button>
        </template>
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

            <!-- tax type -->

            <sw-input-group
              :label="$t('customers.tax_type')"
              class="md:col-span-3"
            >
              <sw-select
                v-model.trim="formData.apply_tax_type"
                :options="apply_tax_type"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('customers.select_a_tax_type')"
                class="mt-2"
                label="name"
                :disabled="!tax_per_item_yes"
              />
            </sw-input-group>
          </div>
        </div>

        <sw-divider class="mb-5 md:mb-8" />

        <!-- Group  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.title_group') }}
          </h6>

          <sw-input-group
            :label="$t('packages.title_group')"
            class="md:col-span-3"
          >
            <sw-select
              v-model="formData.groupLeft"
              :options="groupRight"
              track-by="id"
              :searchable="true"
              :multiple="true"
              :show-labels="false"
              :placeholder="$t('packages.select_packages')"
              class="mt-2"
              label="name"
            />
          </sw-input-group>
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

        <!-- SECTION -->

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
                  <!-- /input -->
                  <sw-money
                    :disabled="showSelectdiscounts"
                    v-model="value_discount"
                    :currency="customPrefixDescount"
                    name="qty"
                  />
                  <!-- input -->
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION -->

        <sw-divider class="mb-5 md:mb-8" />

        <!-- Taxes -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.taxes') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
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

            <!-- TAXES -->

            <sw-input-group :label="$t('packages.taxes')" class="md:col-span-3">
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

            <!-- TAX GROUPS -->

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
                :multiple="true"
                class="mt-2"
                label="name"
                track-by="id"
                @select="groupTaxSeleted"
                @remove="removeTaxGroup"
              />
            </sw-input-group>
          </div>
        </div>

        <sw-divider class="mb-5 md:mb-8" />

        <!-- Items -->
        <div class="grid grid-cols-1 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.packages_items') }}
          </h6>
          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
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
                  class="flex justify-center items-center m-1 font-medium py-1 px-2 bg-white rounded-full text-indigo-100 bg-indigo-700 border border-indigo-700"
                >
                  <div
                    class="text-xs text-base leading-none max-w-full flex-initial py-2 pl-2"
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
                        class="feather feather-x cursor-pointer hover:text-indigo-400 rounded-full w-6 h-4 ml-2 pr-1"
                      >
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- formulario de items -->

        <!-- Items -->
        <div class="table-responsive-item2">
          <div class="tablemin">
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
                    class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                  >
                    <span class="pl-12">
                      {{ $tc('items.item', 1) }}
                    </span>
                  </th>
                  <th
                    class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
                  >
                    <span>
                      {{ $t('estimates.item.quantity') }}
                    </span>
                  </th>
                  <th
                    class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                  >
                    <span>
                      {{ $t('estimates.item.price') }}
                    </span>
                  </th>
                  <th
                    v-if="discountPerItem === 'YES'"
                    class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                  >
                    <span>
                      {{ $t('estimates.item.discount') }}
                    </span>
                  </th>
                  <th
                    class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
                  >
                    <span class="pr-10 column-heading">
                      {{ $t('estimates.item.total') }}
                    </span>
                  </th>
                </tr>
              </thead>
              <draggable
                v-model="formData.items"
                class=" "
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
                  :tax-per-item="isTaxPerItem"
                  :discount-per-item="discountPerItem"
                  @remove="removeItem"
                  @update="updateItem"
                  @itemValidate="checkItemsData"
                />
              </draggable>
            </table>

            <div
              class="flex items-center justify-center w-full px-6 py-3 text-base border-b border-gray-200 border-solid cursor-pointer text-primary-400 hover:bg-gray-200"
              @click="addItem"
            >
              <shopping-cart-icon class="h-5 mr-2" />
              {{ $t('estimates.add_item') }}
            </div>
          </div>
        </div>

        <!-- Amount -->
        <div class="grid grid-cols-5 gap-4 mb-8" v-if="false">
          <sw-divider class="col-span-12" />

          <h6 class="col-span-5 sw-section-title lg:col-span-1"></h6>
          <div
            class="col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6 flex wrap content-end justify-end"
          >
            <div
              class="px-5 py-4 mt-6 bg-white border border-gray-200 border-solid rounded package-total lg:mt-0"
            >
              <div class="flex items-center justify-between w-full">
                <label
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                  >{{ $t('estimates.sub_total') }}</label
                >
                <label
                  class="flex items-center justify-center m-0 text-lg text-black uppercase"
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
                      class="m-0 text-sm font-semibold leading-5 text-gray-500 uppercase"
                      >{{ tax.name }} - {{ tax.percent }}%
                    </label>
                    <label
                      class="flex items-center justify-center m-0 text-lg text-black uppercase"
                      style="font-size: 18px"
                    >
                      <div v-html="$utils.formatMoney(tax.amount, currency)" />
                    </label>
                  </div>
                </template>
                <template v-if="!isTaxPerItem">
                  <div
                    class="flex items-center content-center justify-between w-full mt-2"
                  >
                    <label
                      class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                      >{{ $t('packages.general_taxes') }}</label
                    >
                  </div>
                  <div
                    v-for="(tax, index) in formData.taxes"
                    :key="index"
                    class="flex items-center content-center justify-between w-full mt-2"
                  >
                    <label
                      class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                      v-text="`${tax.name} - ${tax.percent}%`"
                    />
                    <label
                      class="flex items-center justify-center m-0 text-lg text-black uppercase"
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
                  class="flex items-center content-center justify-between w-full mt-2"
                >
                  <label
                    class="text-sm font-semibold leading-5 text-gray-500 uppercase"
                    >{{ $t('packages.general_discounts') }}</label
                  >
                  <label
                    class="flex items-center justify-center m-0 text-lg text-black uppercase"
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
                class="flex items-center justify-between w-full pt-2 mt-5 border-t border-gray-200 border-solid"
              >
                <label
                  class="m-0 text-sm font-semibold leading-5 text-gray-500 uppercase"
                  >{{ $t('estimates.total') }}
                  {{ $t('estimates.amount') }}:</label
                >
                <label
                  class="flex items-center justify-center text-lg uppercase text-primary-400"
                >
                  <div v-html="$utils.formatMoney(total, currency)" />
                </label>
              </div>
            </div>
          </div>
        </div>

        <sw-button
          variant="primary-outline"
          type="button"
          size="lg"
          class="mr-3 flex w-full mt-4 sm:hidden md:hidden"
          @click="cancelForm"
        >
          <x-circle-icon class="w-6 h-6 mr-1 -ml-2" v-if="!isLoading" />
          {{ $t('general.cancel') }}
        </sw-button>

        <sw-button
          :loading="isLoading"
          :disabled="isLoading"
          variant="primary"
          type="submit"
          size="lg"
          class="flex w-full mt-4 mb-2 mb-md-0 sm:hidden md:hidden"
        >
          <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
          {{
            isEdit ? $t('packages.update_package') : $t('packages.save_package')
          }}
        </sw-button>
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
  XCircleIcon,
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
    XCircleIcon,
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
      tax_per_item_yes: false,
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
      ],
      status_payment: [
        { value: 'R', text: 'Prepaid' },
        { value: 'O', text: 'Postpaid' },
      ],
      apply_tax_type: [
        { name: 'General', value: 'general' },
        { name: 'Item', value: 'item' },
      ],
      client_qty: 0,
      qty: 0,
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
        apply_tax_type: { name: 'General', value: 'general' },
        status_payment: { value: 'R', text: 'Prepaid' },
        apply_discount_type: null,
        discount_val: 0,
        packages_discount_none: false,
        packages_discount: false,
        groupLeftTax: [],
        item_groups: [],
        name: this.isCo,
        descriptionHTML: null,
        descriptionText: null,
        password: null,
        phone: null,
        status: {
          value: 'A',
          text: 'Active',
        },
        discounts: { value: 'fixed', text: 'Fixed' },
        client_qty: null,
        qty: null,
        upgrades_use_renewal: false,
        taxes: [],
        tax_groups: [],
        items: [],
        groups: [],
      },
    }
  },
  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),
    customerCurrency() {
      return this.defaultCurrencyForInput
    },
    customPrefixDescount() {
      return {
        ...this.customerCurrency,
        prefix:
          this.formData.discounts?.value == 'percentage'
            ? '% '
            : this.customerCurrency.prefix,
      }
    },

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

    isTaxPerItem() {
      if (this.formData.apply_tax_type.value == 'item') {
        return 'YES'
      }
      return 'NO'
    },
  },
  watch: {
    // packageNameGroup(val) {
    //   this.groupRight.push({ name: val })
    // },

    paralelo() {},

    getListTaxes: {
      handler: function (val, oldVal) {},
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
      this.formData.items.push({
        ...PackageStub,
        id: Guid.raw(),
        end_period_act: false,
        end_period_number: 1,
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
        }

        if (response.data.tax_per_item === 'YES') {
          this.tax_per_item_yes = true
        }
      }

      Promise.all([
        this.fetchItems({
          filter: {},
          orderByField: '',
          orderBy: '',
          limit: 1000,
        }),
        this.fetchCompanySettings(['estimate_auto_generate']),
      ])
        .then(async ([res1, res2]) => {
          this.itemsF = res1.data.items.data
        })
        .catch((error) => {})
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

    removeTaxGroup(val) {
      this.paralelo = this.filterByReference(
        this.paralelo,
        val.tax_groups_tax_types
      )
      this.taxes = this.filterByReference(this.taxesFetch, this.paralelo)

      this.formData.taxes = [...this.paralelo]
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
    },
    removeTax(tax) {
      let myArray = this.formData.taxes

      for (var i = myArray.length - 1; i >= 0; --i) {
        if (myArray[i].id == tax.id) {
          myArray.splice(i, 1)
        }
      }

      this.formData.taxes = [...myArray]
      this.paralelo = [...myArray]

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

      if (this.paralelo.length == 0) this.groupTax = null

      this.tax = null
    },
    removeItemGroup(item) {
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
          ;(item.id = Guid.raw()),
            (item.end_period_act = false),
            (item.end_period_number = 1),
            (item.discount_type = 'fixed'),
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
      //taxSeleted
      let taxC = []
      const isId = (element) => element.id == val.id
      const index = this.groupLeftTax.findIndex(isId)

      if (index != -1) {
        // vm.formData.taxes = vm.groupTaxes[index].tax_groups_tax_types; ??
        this.groupTaxes[index].tax_groups_tax_types.forEach((tax) => {
          const found = !this.paralelo.find((element) => element.id === tax.id)

          if (found) {
            taxC = { ...tax, id: tax.id }
            this.formData.taxes.push(taxC)
            this.paralelo.push(taxC)
          }
        })

        this.taxes = this.filterByReference(
          this.taxes,
          this.groupTaxes[index].tax_groups_tax_types
        )
      } else {
        this.formData.taxes = []
        this.paralelo = []
      }
    },

    cancelForm() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.lose_unsaved_information'),
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          this.$router.go(-1)
        }
      })
    },

    taxSeleted(val) {
      const isId = (element) => element.id == val.id
      const index = this.formData.taxes.findIndex(isId)

      if (index == -1) {
        this.formData.taxes.push(val)
        this.paralelo.push(val)
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
      let response = await this.fetchCompanySettings(['tax_per_item'])

      if (response.data.tax_per_item === 'YES') {
        this.tax_per_item_yes = true
      }

      let vm = this
      let res = await this.fetchPackage(this.$route.params.id)

      let resGroupRight = await this.fetchGroupMembership()

      this.groupRight = resGroupRight
      this.groupLeft = res.data.response.groupLeft

      vm.formData = res.data.response

      if (this.isEdit || this.isCopy) {
        this.groupTax = res.data.tax_groups

        let statusPackageDiscount =
          res.data.response.packages_discount == 0 ? false : true
        this.onDiscounts(statusPackageDiscount)

        // OJO CON ESTA PARTE ESTA POR VALIDAR QUE NO AFECTE EN LOS CALCULOS REALIZADOS PARA EL PACKAGE
        // this.formData.packages_discount_none = res.data.response.packages_discount_none
        // for (var i = 0; i < this.groupLeft.length; i++) {
        //   for (var j = 0; j < this.groupRight.length; j++) {
        //     if (this.groupLeft[i].id === this.groupRight[j].id) {
        //       this.groupRight.splice(j, 1)
        //     }
        //   }
        // }

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

      vm.taxPerItem = apply_tax_type === 'item' ? 'YES' : 'NO'
      vm.recomputeTotal = true

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

      let itemsArray = []
      this.formData.items = []
      items.forEach((item) => {
        item.quantity = item.quantity
        item.item_id = item.items_id
        item.item_group_id = item.item_group_id
        item.price = item.price
        item.discount_type = item.discount_type
        item.discount = item.discount
        item.discount_val = item.discount_val
        item.tax = item.tax
        item.description = item.description
        item.package_id = item.package_id
        item.total = item.total
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
        (this.formData.apply_tax_type = this.apply_tax_type.filter(
          (element) => element.value == apply_tax_type
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

      let text = ''
      if (this.isEdit) {
        text = 'packages.edit_packages_text'
      } else {
        text = 'packages.create_packages_text'
      }

      // Si es diferente a fijo evalua el % mayor a 100
      if (this.formData.discounts.value != 'fixed') {
        if (this.value_discount > 100) {
          window.toastr['error'](
            'Discount value cannot be greater than 100',
            this.formData
          )
          return false
        }
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc(text),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          this.formData.value_discount = this.value_discount
          var variable = this.formData.discounts
          if (typeof variable == 'undefined' && variable == null) {
            variable = 'percentage'
          } else {
            variable = variable.value
          }

          this.formData.discount_general_type = variable
          this.formData.discount_general = this.value_discount
          // this.formData.groupLeft = this.groupLeft

          this.formData.qty = this.qty == '' ? 0 : this.qty
          this.formData.client_qty = this.client_qty == '' ? 0 : this.client_qty
          this.formData.upgrades_use_renewal = this.upgrades_use_renewal ? 1 : 0

          this.formData.status_payment = this.formData.status_payment.value
          this.formData.status = this.formData.status.value
          this.formData.apply_tax_type = this.formData.apply_tax_type.value

          // tax_groups
          this.formData.tax_groups = this.groupTax

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

              if (res.data.nameExists != undefined && res.data.nameExists) {
                this.formData.apply_tax_type = this.apply_tax_type.find(
                  (element) => {
                    return element.value == this.formData.apply_tax_type
                  }
                )
                this.formData.status = this.status.find((element) => {
                  return element.value == this.formData.status
                })
                this.formData.status_payment = this.status_payment.find(
                  (element) => {
                    return element.value == this.formData.status_payment
                  }
                )

                window.toastr['error'](res.data.response)
                return false
              }

              if (!this.isEdit) {
                window.toastr['success'](res.data.response)
                this.$router.push('/admin/packages')
                // return true
              }
            }
          } catch (error) {
            window.toastr['error'](error.response.data.response)

            this.formData.apply_tax_type = this.apply_tax_type.find(
              (element) => {
                return element.value == this.formData.apply_tax_type
              }
            )
            this.formData.status = this.status.find((element) => {
              return element.value == this.formData.status
            })
            this.formData.status_payment = this.status_payment.find(
              (element) => {
                return element.value == this.formData.status_payment
              }
            )

            this.isLoading = false
            return false
          }
        }
      })
    },
    // moveToLeft(item, index) {
    //   if (this.isEdit || this.isCopy) {
    //     item.new = true
    //   }
    //   this.groupLeft.push(item)
    //   this.groupRight.splice(index, 1)
    // },
    // moveToRight(item, index) {
    //   this.deleteItemPackagesGroup(item.id)
    //   this.groupRight.push(item)
    //   this.groupLeft.splice(index, 1)
    // },
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
        /*
        discount_val: {
          between: between(0, 100),
        },*/
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
