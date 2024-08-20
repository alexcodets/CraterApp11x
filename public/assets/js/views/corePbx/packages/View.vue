<template>
    <!-- Base  -->
    <base-page class="tckets-departaments-view">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
        <!-- Header  -->
        <sw-page-header class="mb-3" :title="$t('tickets.departaments.view_pbx_packages')">
            <sw-breadcrumb slot="breadcrumbs">
                <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
                <sw-breadcrumb-item to="/admin/corePBX/packages" :title="$t('corePbx.menu_title.packages')" />
            <!--    <sw-breadcrumb-item to="#" :title="itemGroup ? itemGroup.name : ''" active/>  -->
            </sw-breadcrumb>
             <template slot="actions">
                <sw-button
                    tag-name="router-link"
                    :to="`/admin/corePBX/packages/${$route.params.id}/edit`"
                    class="mr-3"
                    variant="primary-outline"
                >
                    {{ $t('general.edit') }}
                </sw-button>
                 <!-- @click="removeticketdepart($route.params.id)" -->
                <sw-button slot="activator" variant="primary">
                    {{ $t('general.delete') }}
                </sw-button>
             </template>

        </sw-page-header>

        <sw-card>
            <div class="col-span-12">
                <p class="text-gray-500 uppercase sw-section-title">
                    {{ $t('item_groups.basic_info') }}
                </p>
                <div class="grid grid-cols-1 gap-4 mt-5 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1">
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{ $t('packages.name_package') }}
                            </p>
                            <p class="text-sm font-bold leading-5 text-black non-italic">
                                {{ formData.pbx_package_name }}
                            </p>
                        </div>
                    
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{ $t('corePbx.packages.type') }}
                            </p>
                            <p v-if="banBasic" class="text-sm font-bold leading-5 text-black non-italic">
                                {{ formData.status_payment.text }}
                            </p>
                        </div>
               
                   
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{$t('packages.status')}}
                            </p>
                            <p v-if="banBasic" class="text-sm font-bold leading-5 text-black non-italic">
                                {{ formData.status.text }}
                            </p>
                        </div>
                    
                    
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{$t('corePbx.packages.dropdown_server')}}
                            </p>
                            <p v-if="banBasic"  class="text-sm font-bold leading-5 text-black non-italic">
                                {{ formData.dropdown_server.server_label }}
                            </p>
                        </div>
                    
                    
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{$t('corePbx.packages.price')}}
                            </p>
                            <p class="text-sm font-bold leading-5 text-black non-italic">
                                {{ formData.rate }}  
                            </p>
                        </div>
                </div>
                <!-- ******* Description ******* -->
                <!-- <div class="tabs mt-5  mb-5 grid col-span-12">
                    <div class="border-b tab">
                        <div class="border-l-2 border-transparent relative">
                        <input
                            class="
                            w-full
                            absolute
                            z-10
                            cursor-pointer
                            opacity-0
                            h-5
                            top-6
                            "
                            type="checkbox"
                            checked
                            id="chck1"
                        />
                        <header
                            class="
                            col-span-5
                            flex
                            justify-between
                            items-center
                            p-3
                            pl-0
                            pr-8
                            cursor-pointer
                            select-none
                            tab-label
                            "
                            for="chck1"
                        >
                            <span class="text-grey-darkest font-thin text-xl">
                            Description
                            </span>
                         
                        </header>
                        <div style="min-height:0vh">
                            <div class="pl-0 pr-8 pb-5 text-grey-darkest">
                            <ul class="pl-0">
                                <li class="pb-2">
                                <sw-tabs :active-tab="activeTab">
                                    <sw-tab-item title="HTML">
                                        <sw-tab-item title="HTML">
                                            <base-custom-input
                                                v-model="formData.html"
                                                :fields="[]"
                                        />
                                        
                                        </sw-tab-item>
                                    
                                    </sw-tab-item>
                                    <sw-tab-item title="Text">
                                    
                                    <base-custom-input
                                        v-model="formData.text"
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
                </div> -->
            <sw-divider class="my-8" />

            <div class="grid grid-cols-1 gap-4 mt-5 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1">
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{ $t('corePbx.packages.automatic_suspension') }}
                            </p>
                            <p class="text-sm font-bold leading-5 text-black non-italic">
                                {{ automatic_suspension ? 'Active' : 'Inactive' }}
                            </p>
                        </div>
                    
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{ $t('corePbx.packages.unmetered') }}
                            </p>
                            <p v-if="banBasic" class="text-sm font-bold leading-5 text-black non-italic">
                                {{ unmetered ? 'Active' : 'Inactive' }}
                            </p>
                        </div>
               
                   
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{$t('corePbx.packages.extensions')}}
                            </p>
                            <p v-if="banBasic" class="text-sm font-bold leading-5 text-black non-italic">
                                {{ extensions ? 'Active' : 'Inactive' }}
                            </p>
                        </div>
                    
                    
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{$t('corePbx.packages.did')}}
                            </p>
                            <p v-if="banBasic"  class="text-sm font-bold leading-5 text-black non-italic">
                                {{ did ? 'Active' : 'Inactive' }}
                            </p>
                        </div>
                    
                    
                        <!-- <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{$t('corePbx.packages.fixed_server')}}
                            </p>
                            <p class="text-sm font-bold leading-5 text-black non-italic">
                                {{ modify_server ? 'Active' : 'Inactive' }}
                            </p>
                        </div> -->
                        
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{$t('corePbx.packages.call_ratings')}}
                            </p>
                            <p class="text-sm font-bold leading-5 text-black non-italic">
                                {{ call_ratings ? 'Active' : 'Inactive' }}
                            </p>
                        </div>
                        
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{$t('corePbx.menu_title.extensions')}}
                            </p>
                            <p class="text-sm font-bold leading-5 text-black non-italic">
                                {{ extension_i ? extension_i.name : 'None' }}
                            </p>
                        </div>

                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{$t('corePbx.menu_title.did')}}
                            </p>
                            <p class="text-sm font-bold leading-5 text-black non-italic">
                                {{ did_i ? did_i.name : 'None' }}
                            </p>
                        </div>

                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{$t('corePbx.packages.international_dialing_code')}}
                            </p>
                            <p class="text-sm font-bold leading-5 text-black non-italic">
                                {{ international_dialing_code_selected ? international_dialing_code_selected : 'None' }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{$t('packages.inclusive_minutes')}}
                            </p>
                            <p class="text-sm font-bold leading-5 text-black non-italic">
                                {{ formData.inclusive_minutes ? formData.inclusive_minutes : 0 }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{$t('corePbx.packages.national_dialing_code')}}
                            </p>
                            <p class="text-sm font-bold leading-5 text-black non-italic">
                                {{ national_dialing_code_selected ? national_dialing_code_selected : 0 }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{$t('corePbx.packages.rate_per_minutes')}}
                            </p>
                            <p class="text-sm font-bold leading-5 text-black non-italic">
                                {{ rate_per_minutes_selected ? rate_per_minutes_selected : 0 }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{$t('corePbx.packages.minutes_increments')}}
                            </p>
                            <p class="text-sm font-bold leading-5 text-black non-italic">
                                {{ minutes_increments_selected ? minutes_increments_selected : 0 }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                                {{$t('corePbx.packages.select')}}
                            </p>
                            <p class="text-sm font-bold leading-5 text-black non-italic">
                                {{ formData.type_time_increment ? formData.type_time_increment.text : 'None' }}
                            </p>
                        </div>
                </div>

              

           

            <!-- <sw-divider class="my-8" /> -->
            <!-- Seccion de TAXES -->
            <div class="grid grid-cols-5 gap-4 mb-8">
        
          <div class="grid col-span-12 gap-y-6 gap-x-4 md:grid-cols-6">
            <sw-divider class="col-span-12 mt-5" />
            <sw-table-component
              class="col-span-12"
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

              <!-- <sw-table-column :sortable="true" :filterable="true">
                <template slot-scope="row">
                  <trash-icon
                    @click="removeTax(row)"
                    class="h-5 mr-3 text-gray-600"
                  />
                </template>
              </sw-table-column> -->
            </sw-table-component>
            <div class="col-span-12"></div>
                    <!-- <sw-input-group
                    :label="$t('packages.taxes')"
                    class="md:col-span-3"
                    
                    >
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
                    </sw-input-group> -->
                    <!-- <sw-input-group
                    :label="$t('packages.member_tax_groups')"
                    class="md:col-span-3"
                    
                    >
                    <sw-select
                        :disabled="isNoGeneralTaxes"
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
                    </sw-input-group> -->
          </div>
        </div>
                
        <sw-divider class="my-8" />
        <!-- Seccion de Descuento -->
        <!-- Group discount -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.title_discount') }}
          </h6>

          <div class="grid col-span-12 gap-y-6 gap-x-4 md:grid-cols-6">
            <div class="col-span-3">
              <sw-divider class="col-span-12 mt-5" />
              <div class="flex mt-3 mb-4">
                <div class="relative w-12">
                  <sw-switch
                    v-model="formData.package_discount"
                    class="absolute"
                    style="top: -20px"
                    @change="onDiscounts"
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
                    v-model="formData.type"
                    :options="discounts"
                    :searchable="true"
                    :show-labels="false"
                    :tabindex="16"
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
                    v-model="value_discount"
                    focus
                    type="text"
                    name="value_discount"
                    :placeholder="$t('packages.value_discount')"
                    tabindex="1"
                    
                    numeric
                    :disabled="bandView"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="formData.package_discount">
          <sw-input-group
            :label="$t('customers.discount_term_type')"
            class="mb-4"
          >
            <sw-select
              v-model="formData.discount_term_type"
              :options="discount_term_type"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('customers.select_a_discount_term')"
              class="mt-2"
              label="name"
              :disabled="bandView"
            />
          </sw-input-group>

          <div
            v-if="discountBetweenDates"
            class="relative grid grid-flow-col grid-rows"
          >
            <sw-input-group :label="$t('general.from')" class="mt-2">
              <base-date-picker
                v-model="formData.discount_start_date"
                :calendar-button="true"
                 :disabled="bandView"
                calendar-button-icon="calendar"
              />
            </sw-input-group>

            <div
              class="
                hidden
                w-8
                h-0
                ml-8
                border border-gray-400 border-solid
                xl:block
              "
              style="margin-top: 3.5rem"
            />

            <sw-input-group :label="$t('general.to')" class="mt-2">
              <base-date-picker
                v-model="formData.discount_end_date"
                :calendar-button="true"
                 :disabled="bandView"
                calendar-button-icon="calendar"
              />
            </sw-input-group>
          </div>
          <sw-input-group
            v-if="!discountBetweenDates"
            :label="$t('customers.time_unit_number')"
            class="mt-2"
          >
          
            <div class="flex" style="width: 50%" role="group">
              <sw-input
                v-model="formData.discount_time_units"
                
                 :disabled="bandView"
                class="border-r-0 rounded-tr-sm rounded-br-sm"
                
              />
              <sw-select
                v-model="formData.discount_term"
                :options="discount_term"
                 :disabled="bandView"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('customers.select_a_term')"
                label="name"
              />
            </div>
          </sw-input-group>
        </div>

      
        <!-- Seccion Item -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <sw-divider class="col-span-12 mt-5" />

          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.packages_items') }}
          </h6>
          <div class="grid col-span-12 gap-y-6 gap-x-4 md:grid-cols-6">
            <sw-input-group
              :label="$t('packages.item_groups')"
              class="md:col-span-3"
            
            >
             <!--  <sw-select
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
              /> -->
            </sw-input-group>
            <div
              class="col-span-12"
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
                  <!--@click="removeItemGroup(item)"  -->
                  <div class="flex flex-auto flex-row-reverse">
                    <div>
                      <svg
                  
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
            <div class="grid col-span-12">
              <!-- Items -->
              <table class="w-full text-center item-table">
                <colgroup>
                  <col style="width: 40%" />
                  <col style="width: 10%" />
                  <col style="width: 15%" />
                  <col v-if="discountPerItem === 'YES'" style="width: 15%" />
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
                <!-- <draggable
                  v-model="formData.items"
                  class="item-body"
                  tag="tbody"
                  handle=".handle"
                > -->
                <!--  @remove="removeItem"
                   
                     -->
                  <!-- <package-item
                    v-for="(item, index) in formData.items"
                    :key="item.id"
                    :index="index"
                    :item-data="item"
                    :currency="currency"
                    :isView="true"
                    :isNoGeneralTaxes="isNoGeneralTaxes"
                    :package-items="formData.items"
                    :tax-per-item="taxPerItem"
                    :discount-per-item="discountPerItem"
                    @itemValidate="checkItemsData" 
                    @update="updateItem"
                  /> -->
                  <package-item
                    v-for="(item, index) in formData.items"
                    :key="item.id"
                    :index="index"
                    :item-data="item"
                    :currency="currency"
                    :isView="true"
                    :isNoGeneralTaxes="isNoGeneralTaxes"
                    :package-items="formData.items"
                    :tax-per-item="taxPerItem"
                    :discount-per-item="discountPerItem"
                    
                    @update="updateItem"
                    @itemValidate="checkItemsData"
                  />
                <!-- </draggable> -->
              </table>
              <!-- <div
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
              </div> -->
            </div>
          </div>
        </div>
        <sw-divider class="my-8" />
            </div>
        </sw-card>

    </base-page>
</template>

<script>
import RightArrow from '@/components/icon/RightArrow'
import MoreIcon from '@/components/icon/MoreIcon'
import LeftArrow from '@/components/icon/LeftArrow'
import draggable from 'vuedraggable'
import PackageItem from './Item'
import PackageStub from '../../../stub/package'
import Guid from 'guid'
import TaxStub from '../../../stub/tax'
import moment from 'moment'
import {mapActions, mapGetters} from "vuex";
import {
  TrashIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
} from '@vue-hero-icons/solid'
export default {
    components: {
    MoreIcon,
    draggable,
    TrashIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusIcon,
    RightArrow,
    LeftArrow,
    PackageItem,
  },
  data() {
    return {
      bandView:true,
      banBasic:false,
      isNoGeneralTaxes: false,
      add_call_rating: false,
      showSelectdiscounts: true,
      discountPerItemStore: null,
      discountPerItem: null,
      taxPerItem: 'YES',
      selectedCurrency: '',
      prefixrate_groups:[],
      tax: null,
      taxesFetch: [],
      taxes: [],
      dropdown_server: [],
      itemsF: [],
      isRequestOnGoing: true,
      isLoading: false,
      EstimateFields: [],
      activeTab: 'Text',
      apply_tax_type_pre: {
        value: 'general',
        text: 'General',
      },
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
      apply_tax_type_options: [
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
      discount_term_type: [
        { name: 'Between dates', value: 'D' },
        { name: 'Time units', value: 'U' },
      ],
      client_limit: '',
      qty_available: '',
      value_discount: '',
      extensions: false,
      did: false,
      call_ratings: false,
      unmetered: false,
      automatic_suspension:false,
      modify_server: false,
      type_time_increment: [
        { value: 'sec', text: 'Seconds' },
        { value: 'min', text: 'Minutes' },
      ],
      status_payment: [
        { value: 'prepaid', text: 'Prepaid' },
        { value: 'postpaid', text: 'Postpaid' },
      ],
      discount_term: [
        { name: 'Days', value: 'days' },
        { name: 'Weeks', value: 'weeks' },
        { name: 'Months', value: 'months' },
        { name: 'Years', value: 'years' },
      ],
      national_dialing_code_selected: null,
      international_dialing_code_selected: null,
      national_dialing_code: null,
      international_dialing_code: null,
      rate_per_minutes_selected: 0,
      minutes_increments_selected: null,
      rate_per_minutes: null,
      minutes_increments: null,
      groupRight: [],
      groupTax: null,
      groupTaxes: [],
      groupLeft: [],
      groupLeftTax: [],
      groupLeftTaxFetch: [],
      groupLeftTaxModel: null,
      did_i: null,
      extension_i: null,
      did_ar: [],
      extension_ar: [],
      item_group: null,
      itemGroupsFetch: [],
      item_groups: [],
      paralelo:[],
      bandServices:false,
      formData: {
        id: null,
        apply_tax_type: {
          value: 'general',
          text: 'General',
        },
        apply_discount_type: null,
        discount_val: 0,
        pbx_package_name: '',
        html: ' ',
        text: ' ',
        status: null,
        type: null,
       /*  discounts: null, */
        value_discount: null,
        status_payment: null,
        client_limit: null,
        qty_available: null,
        extensions: false,
        did: false,
        call_ratings: false,
        unmetered: false,
        automatic_suspension:false,
        prefixrate_groups_id:'',
        modify_server: false,
        package_discount: false,
        taxes: [],
        items: [],
        itemsPre: [
          {
            ...PackageStub,
            id: Guid.raw(),
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          },
        ],
        groupLeftTax: [],
        item_group: null,
        item_groups: [],
        itemGroupsFetch: [],
        items: [],
        dropdown_server: null,
        rate: null,
        inclusive_minutes: null,
        national_dialing_code: null,
        international_dialing_code: null,
        rate_per_minutes: null,
        minutes_increments: null,
        type_time_increment: null,
        template_did_id: null,
        template_extension_id: null,
        discount_term_type: { name: 'Between dates', value: 'D' },
        discount_time_units: 0,
        discount_start_date: null,
        discount_end_date: null,
        discount_term: { name: 'Days', value: 'days' },
      },
    }
  },

    
    computed: {
        ...mapGetters('ticketDepartament', ['selectedViewDepartament']),

        ...mapGetters('company', ['defaultCurrency']),

        isAddCallRating() {
            if (!this.unmetered) {
                this.add_call_rating = this.call_ratings
                return this.add_call_rating
            }
        },
        isExtensionSl() {
            return this.extensions ? 1 : 0
        },
        isDidSl() {
            return this.did ? 1 : 0
        },
        currency() {
          return this.selectedCurrency
        },
        discountBetweenDates() {
            if (this.formData.discount_term_type.value === 'D') {
                this.formData.discount_term = { name: 'Days', value: 'days' }
                this.formData.discount_time_units = 0
                return this.formData.discount_term_type.value === 'D'
            } else {
                ;(this.formData.discount_start_date = null),
                (this.formData.discount_end_date = null)
            }
        },

    },
    created() {
        this.fetchTax()
        this.fetchInitialItemGroups()
        this.fetchInitialItems()
        this.fetchInitialCustomDestination()
        this.loadPackage();
        this.loadGroupMembership()
        this.loadGroupTax()
        setTimeout(() => {
          this.onSelectApplyTaxType(this.formData.apply_tax_type)
        }, 500)
    },
    methods: {
        
        ...mapActions('extensions', ['fetchExtensionsnp']),
       /*  ...mapActions('pbx', ['fetchPackage','fetchItemGroups']), */
         ...mapActions('pbx', [
            'addPackage',
            'fetchPackage',
            'fetchItemGroups',
            'updatePackage',
            'fetchGroupMembership',
            'saveGroup',
            'fetchGroupTaxMembership',
          ]),
          ...mapActions('taxType', [
            'indexLoadData',
            'deleteTaxType',
            'fetchTaxType',
            'fetchTaxTypes',
            'packageGroup',
          ]),
          ...mapActions('company', ['fetchCompanySettings']),
          ...mapActions('internacionalrate', ['CargarCustomDestination']),
         ...mapActions('did', ['fetchDIDsnp']),
         ...mapActions('item', ['fetchItems']),

    async fetchInitialItemGroups() {
      let data = {
        orderByField: 'created_at',
        orderBy: 'asc',
      }

      let res = await this.fetchItemGroups(data)

      this.item_groups = res.data.response
      this.itemGroupsFetch = res.data.response
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
    updateItem(data) {
      Object.assign(this.formData.items[data.index], { ...data.item })
    },
    checkItemsData(index, isValid) {
      this.formData.items[index].valid = isValid
    },
    async fetchTax() {
      let data = {
        orderByField: 'created_at',
        orderBy: 'asc',
      }

      let response = await this.fetchTaxTypes(data)
      // console.log('fetchTax', response.data.taxTypes.data)
      let taxes = response.data.taxTypes.data
      taxes.forEach((element) => {
        element.name_por = `${element.name} - ${element.percent}%`
      })
      this.taxes = taxes
      this.taxesFetch = taxes
    },
    async fetchInitialItems() {
      this.isLoadingData = true

      // if (!this.isEdit) {
      let response = await this.fetchCompanySettings([
        'discount_per_item',
        'tax_per_item',
      ])

      if (response.data) {
        this.discountPerItemStore = response.data.discount_per_item
        this.discountPerItem = response.data.discount_per_item
        // this.taxPerItem = response.data.tax_per_item
      }
      // }

      Promise.all([
        this.fetchItems({
          filter: {},
          orderByField: '',
          orderBy: '',
          limit:'all' ,
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
    async fetchInitialCustomDestination(){
        let cargarCustomD = await this.CargarCustomDestination();
        this.prefixrate_groups=[...cargarCustomD.data.internacional];
    },
    async loadGroupMembership() {
      this.groupRight = await this.fetchGroupMembership()
    },
    async loadGroupTax() {
      this.groupTaxes = await this.fetchGroupTaxMembership()
      this.groupLeftTax = await this.fetchGroupTaxMembership()
      this.groupLeftTaxFetch = await this.fetchGroupTaxMembership()
    },
    async loadPackage() {
       /* this.initLoad = true */
      let vm = this
      let res = await this.fetchPackage(this.$route.params.id)   
      // console.log('LOAD PACKAGE CARGANDO EL PAQUETE: ', res)
      let ext = await this.fetchExtensionsnp()
      let extdid = await this.fetchDIDsnp()
      let resDro = await window.axios.get('/api/v1/pbx/servers')
      if (resDro) {
        this.dropdown_server = resDro.data.pbxServers.data
      }
      this.did_ar = [...extdid.data.profileDID]
      this.extension_ar = [...ext.data.profileExtensions]

      
        this.formData.package_discount = res.data.pbxPackage.package_discount
        this.formData.value_discount = res.data.pbxPackage.value_discount
        this.discount = res.data.pbxPackage.discount
        this.value_discount = res.data.pbxPackage.value_discount
       /*  if (res.data.pbxPackage.type || this.value_discount) {
          this.showSelectdiscounts = false
        } */
        if (this.formData.package_discount) {
          this.showSelectdiscounts = false
        }

        
      

      this.formData = res.data.pbxPackage
      let {
        qty_available,
        client_limit,
        html,
        text,
        tax_types,
        status,
        extensions,
        status_payment,
        did,
        package_discount,
        call_ratings,
        /* pbx_package_name, */
        unmetered,
        automatic_suspension,
        prefixrate_groups_id,
        discount_term,
        discount_time_units,
        discount_end_date,
        discount_start_date,
        discount_term_type,
        /* discounts, */
        modify_server,
        pbx_server_id,
        national_dialing_code,
        international_dialing_code,
        rate_per_minutes,
        minutes_increments,
        type_time_increment,
        template_did_id,
        template_extension_id,
         items,
        item_groups,
        bandServices,
        type,
      } = res.data.pbxPackage

      /*console.log("Obervando bandera",bandServices, pbx_package_name );*/
      /* if(bandServices>0){ */
        this.bandServices= true;
        /* window.toastr['success']("Some attributes cannot be edited because services have already been created"); */
        /* window.toastr['success']("Algunos atributos no pueden ser editados porque ya se crearon servicios"); */
      /* } */

      // console.log('PBXSERVER_ID', pbx_server_id)

      if (template_did_id) {
        this.did_i = this.did_ar.find((extd) => extd.id === template_did_id)
        //console.log('did_id', this.did_i)
      }

      if (template_extension_id) {
        this.extension_i = this.extension_ar.find(
          (ex) => ex.id === template_extension_id
        )
      }
      if (prefixrate_groups_id) {
        this.formData.prefixrate_groups_id = this.prefixrate_groups.find(
          (ex) => ex.id === prefixrate_groups_id
        )
      }

      //  console.log('Aqui lo nuevo',discount_term,discount_time_units, discount_end_date, discount_start_date,discount_term_type);

      if (package_discount) {
        this.formData.discount_term_type = this.discount_term_type.find(
          (_type) => discount_term_type === _type.value
        )
        if (discount_term_type === 'U') {
          this.formData.discount_term = this.discount_term.find(
            (_term) => discount_term === _term.value
          )
          this.formData.discount_time_units = discount_time_units
        } else {
          this.formData.discount_start_date = moment(
            discount_start_date,
            'YYYY-MM-DD'
          ).toString()

          this.formData.discount_end_date = moment(
            discount_end_date,
            'YYYY-MM-DD'
          ).toString()
          this.formData.discount_term = this.discount_term.find(
            (_term) => 'days' === _term.value
          )
        }
      } else {
        this.formData.discount_term_type = this.discount_term_type.find(
          (_type) => discount_term_type === _type.value
        )
      }

      //console.log('taxes', this.taxes)
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
      // this.groupLeftTax = filterByReference(this.taxes, groupLeftTax)

      // console.log('filterByReference', filterByReference(this.taxes, tax_types));

      this.qty_available = qty_available
      this.client_limit = client_limit
      this.national_dialing_code = national_dialing_code
      this.national_dialing_code_selected = national_dialing_code
      this.international_dialing_code = international_dialing_code
      this.international_dialing_code_selected = international_dialing_code
      this.rate_per_minutes_selected = parseFloat(rate_per_minutes)
      this.minutes_increments_selected = minutes_increments
      this.minutes_increments = minutes_increments
      this.rate_per_minutes = parseFloat(rate_per_minutes)
      this.formData.status = this.status.filter(
        (element) => element.value == status
      )[0]
      this.formData.status_payment = this.status_payment.filter(
        (element) => element.value == status_payment
      )[0]
      this.formData.type_time_increment = this.type_time_increment.filter(
        (element) => element.value == type_time_increment
      )[0]
      // dropdown server
      this.formData.dropdown_server = this.dropdown_server.filter(
        (elm) => elm.id == pbx_server_id
      )[0]
      /* this.formData.discounts = this.discounts.filter(
        (elm) => elm.id == discounts
      )[0] */
      this.formData.type = this.discounts.filter(
        (element) => element.value === type
      )[0]
      this.formData.html = html
      this.formData.text = text
      this.formData.taxes = tax_types
      this.paralelo = [...this.formData.taxes]
      this.extensions = extensions == 1 ? true : false
      this.did = did == 1 ? true : false
      this.package_discount = package_discount == 1 ? true : false
      this.call_ratings = call_ratings == 1 ? true : false
      ;(this.unmetered = unmetered == 1 ? true : false),
      (this.automatic_suspension = automatic_suspension == 1 ? true : false),
        (this.modify_server = modify_server == 1 ? true : false)

      this.isRequestOnGoing = false

      let itemsArray = []
      this.formData.items = []
      items.forEach((item) => {
      //  console.log("ESTE ES EL ITEM", item)
        /* if (item.taxes.length == 0) {
          item.taxes = [{ ...TaxStub, id: Guid.raw() }]
        } */
        item.quantity = item.quantity
        // item.id = item.pivot.id
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

        this.formData.items.push({
            ...item,
          })

        /* if (item.taxes.length == 0) {
          this.formData.items.push({
            ...item,
            id: Guid.raw(),
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          })
        } else {
          this.formData.items.push({
            ...item,
          })
        } */
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
                if (item.item_group_id == itemG.item_group_id) {
                  arrayItemGroupsItems.push(item)
                }
              }
              element.items = arrayItemGroupsItems
              arrayItemGroups.push(element)
              arrayItemGroupsItems = []
            } else {
              arrayItemGroupsSelect.push(element)
            }
          }
        })
  
        vm.formData.item_groups = arrayItemGroups
        
       var array = [];
          for (var i = 0; i < vm.itemGroupsFetch.length; i++) {
              var igual=false;
              for (var j = 0; j < item_groups.length & !igual; j++) {
                  if(vm.itemGroupsFetch[i]['id'] == item_groups[j]['item_group_id']) 
                          igual=true;
              }
              if(!igual)array.push(vm.itemGroupsFetch[i]);
          }
      
        vm.item_groups = array
       
      }
      

      /* this.initLoad = false */
      this.banBasic = true
       this.isRequestOnGoing = false
      // console.log('formData', this.formData.taxes)
    },
        onDiscounts(val) {
        this.showSelectdiscounts = !val
        },
        slideChange() {
            this.national_dialing_code_selected = this.add_call_rating
                ? this.national_dialing_code
                : ''
            this.international_dialing_code_selected = this.add_call_rating
                ? this.international_dialing_code
                : ''
            this.national_dialing_code_selected = this.add_call_rating
                ? this.national_dialing_code
                : ''
            this.international_dialing_code_selected = this.add_call_rating
                ? this.international_dialing_code
                : ''
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
          /* item.id = Guid.raw() */
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
         /*  item.taxes = [{ ...TaxStub, id: Guid.raw() }] */
          vm.formData.items.push(item)
        })
        vm.formData.items = this.filterDuplicate(vm.formData.items)
      })
      /* console.log("Prueba itemGroupSelected ",vm.formData.items);
      return */
      setTimeout(() => {
        this.item_group = null
      }, 100)
    },
    }
}
</script>