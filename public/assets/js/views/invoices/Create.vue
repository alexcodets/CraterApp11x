<template>
  <base-page class="relative invoice-create-page">
    <form
      v-if="!isLoadingInvoice && !isLoadingData"
      @submit.prevent="submitForm"
    >
      <sw-page-header :title="pageTitle">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />
          <sw-breadcrumb-item
            :title="$tc('invoices.invoice', 2)"
            to="/admin/invoices"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'invoice.edit'"
            :title="$t('invoices.edit_invoice')"
            to="#"
            active
          />
          <sw-breadcrumb-item
            v-else
            :title="$t('invoices.new_invoice')"
            to="#"
            active
          />
        </sw-breadcrumb>

        <template slot="actions">
          <sw-button
            v-if="$route.name === 'invoices.edit'"
            :disabled="isLoading"
            :href="`/invoices/pdf/${newInvoice.unique_hash}`"
            tag-name="a"
            variant="primary-outline"
            class="mr-3"
            target="_blank"
          >
            {{ $t('general.view_pdf') }}
          </sw-button>

          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            class="flex justify-center w-full lg:w-auto"
            type="submit"
            size="lg"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ $t('invoices.save_invoice') }}
          </sw-button>
        </template>
      </sw-page-header>

      <!-- Select Customer & Basic Fields  -->
      <div class="grid-cols-12 gap-8 mt-6 mb-8 lg:grid">
        <customer-select
          :valid="$v.selectedCustomer"
          :customer-id="customerId"
          :avalara_bool="newInvoice.avalara_bool"
          class="col-span-5 pr-0"
        />

        <div
          class="
            grid grid-cols-1
            col-span-7
            gap-4
            mt-8
            lg:gap-6 lg:mt-0 lg:grid-cols-2
          "
        >
          <sw-input-group
            :label="$t('invoices.invoice_date')"
            :error="invoiceDateError"
            required
          >
            <base-date-picker
              v-model="newInvoice.invoice_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
              class="mt-2"
              @input="$v.newInvoice.invoice_date.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('invoices.due_date')"
            :error="dueDateError"
            required
          >
            <base-date-picker
              v-model="newInvoice.due_date"
              :invalid="$v.newInvoice.due_date.$error"
              :calendar-button="true"
              calendar-button-icon="calendar"
              class="mt-2"
              @input="$v.newInvoice.due_date.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('invoices.invoice_number')"
            :error="invoiceNumError"
            class="lg:mt-0"
            required
          >
            <sw-input
              :prefix="`${invoicePrefix} - `"
              v-model="invoiceNumAttribute"
              :invalid="$v.invoiceNumAttribute.$error"
              class="mt-2"
              @input="$v.invoiceNumAttribute.$touch()"
            >
              <hashtag-icon slot="leftIcon" class="h-4 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>

          <sw-input-group
            :label="$t('invoices.ref_number')"
            :error="referenceError"
            class="lg:mt-0"
          >
            <sw-input
              v-model="newInvoice.reference_number"
              :invalid="$v.newInvoice.reference_number.$error"
              class="mt-2"
              @input="$v.newInvoice.reference_number.$touch()"
            >
              <hashtag-icon slot="leftIcon" class="h-4 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>
        </div>
      </div>
      <!--
      <div class="flex" v-if="packages.length > 0">
        <div class="relative w-12">
          <sw-switch
            v-model="newInvoice.package_bool"
            class="absolute"
            style="top: -18px"
          />
        </div>

        <div class="ml-15">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('customers.add_packages') }}
          </p>
          <p
            class="p-0 m-0 text-xs leading-tight text-gray-500"
            style="max-width: 480px"
          >
            {{ $t('customers.add_packages_descriptions') }}
          </p>
        </div>
      </div>
      <br v-if="packages.length > 0" />
-->
      <!-- Packages  -->
      <!--
      <div
        class="grid grid-cols-5 p-5 bg-white gap-4 mb-8"
        v-if="newInvoice.package_bool"
      >
        <div
          class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
        >
          <sw-table-component
            v-if="false"
            class="col-span-12"
            ref="table"
            :show-filter="false"
            :data="newInvoice.packages"
            table-class="table"
            variant="gray"
          >
            <sw-table-column
              :sortable="true"
              :label="$t('navigation.package')"
              show="name"
            >
              <template slot-scope="row">
                <span>{{ $t('settings.tax_types.tax_name') }}</span>
                <span class="mt-6">{{ row.name }}</span>
              </template>
            </sw-table-column>

            <sw-table-column :sortable="true" :filterable="true">
              <template slot-scope="row">
                <trash-icon
                  @click="removePackage(row.id)"
                  class="h-5 mr-3 text-gray-600"
                />
              </template>
            </sw-table-column>
          </sw-table-component>
          <div class="col-span-12"></div>
          <sw-input-group
            :label="$t('navigation.package')"
            class="md:col-span-3"
            required
          >
            <sw-select
              v-model="packag"
              :options="packages"
              :searchable="true"
              :show-labels="false"
              :allow-empty="true"
              :placeholder="$tc('customers.add_package')"
              class="mt-2"
              label="name"
              track-by="id"
              @select="packageSeleted"
            />
          </sw-input-group>
        </div>
      </div>
-->
      <br />
      <br />

      <div class="flex">
        <div class="relative w-12">
          <sw-switch
            v-model="newInvoice.avalara_bool"
            class="absolute"
            style="top: -18px"
          />
        </div>

        <div class="ml-15">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('invoices.avalara.switch_bool_name') }}
          </p>
          <p
            class="p-0 m-0 text-xs leading-tight text-gray-500"
            style="max-width: 480px"
          >
            {{ $t('invoices.avalara.switch_bool_description') }}
          </p>
        </div>
      </div>

      <br />
      <br />
      <br />
      <!-- Items -->

      <div style="min-width: 50rem">
        <table class="w-full text-center item-table" >
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
                {{ $tc('items.item', 2) }}
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
              {{ $t('invoices.item.quantity') }}
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
              {{ $t('invoices.item.price') }}
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
                border-t border-b border-gray-200 border-solid
              "
            >
              <span class="pr-10">
                {{ $t('invoices.item.amount') }}
              </span>
            </th>
          </tr>
        </thead>

        <draggable
          v-model="newInvoice.items"
          class="item-body"
          tag="tbody"
          handle=".handle"
        >
          <invoice-item
            v-for="(item, index) in newInvoice.items"
            :key="item.id"
            :index="index"
            :item-data="item"
            :invoice-items="newInvoice.items"
            :currency="currency"
            :tax-per-item="taxPerItem"
            :avalara-is-taxable="newInvoice.avalara_bool"
            :discount-per-item="discountPerItem"
            :user="selectedCustomer"
            :ban-type="newInvoice.pbx_service_id"
            @remove="removeItem"
            @update="updateItem"
            @itemValidate="checkItemsData"
          />
        </draggable>
        </table>
      </div>


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
        {{ $t('invoices.add_item') }}
      </div>
      <!-- text-center -->
      <div v-if="this.newInvoice.pbx_service_id!=null" class="w-full  item-table mt-8" style="min-width: 50rem">
          <div>
           
          <div
            class="bg-white rounded shadow px-4 py-5 sm:px-8 sm:py-4"
          >
          <h3 class=" sw-page-title text-center"> {{ $t('invoices.pbx_services.title_service_detail') }}</h3> 
            <sw-input-group
              :label="$t('invoices.pbx_services.package')"
              class="mb-1"
            >
            </sw-input-group>

            <!-- table pbx_services package -->
              <table class="w-full text-center item-table">
                <colgroup>
                      <col style="width: 25%" />
                      <col style="width: 25%" />
                      <col style="width: 25%" />
                      <col style="width: 25%" />
                </colgroup>
              <thead class="bg-white border border-gray-200 border-solid">
                  <tr>
                    <th
                      class="
                        px-5
                        text-sm
                        not-italic
                        font-medium
                        leading-5
                        text-gray-700
                        border-t border-b border-gray-200 border-solid
                      "
                    >
                      <span>
                        {{ $tc('invoices.pbx_services.service', 2) }}
                      </span>
                    </th>
                    <th
                      class="
                        px-5
                        text-sm
                        not-italic
                        font-medium
                        leading-5
                        text-gray-700
                        border-t border-b border-gray-200 border-solid
                      "
                    >
                      {{ $t('invoices.pbx_services.service_id') }}
                    </th>
                    <th
                      class="
                        px-5
                        text-sm
                        not-italic
                        font-medium
                        leading-5
                        text-gray-700
                        border-t border-b border-gray-200 border-solid
                      "
                    >
                      {{ $t('invoices.item.price') }}
                    </th>
                    <th
                      class="
                        px-5
                        text-sm
                        not-italic
                        font-medium
                        leading-5
                        text-gray-700
                        border-t border-b border-gray-200 border-solid
                      "
                    >
                      {{ $t('invoices.item.inclusive_minutes') }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <!--"pattern="^[0-9]+([.][0-9]+)?$"
                              title="only whole numbers and decimals(Ej: 9.5 7,9)"
                              -->
                    <tr class="py-3">
                        <td class="px-5 py-2">
                          {{ this.info_pbx_pack.pbx_package_name }}
                        </td>
                        <td class="px-5 py-2">
                          {{ this.info_pbx_pack.packages_number }}
                        </td>
                        <td class="px-10 py-2"> 
                          
                            <sw-input
                              value=""
                              min=0
                              step='any'
                              type="number"
                              v-model.number="packagesPrice"
                              @input="modifManual()"
                            >
                            </sw-input>
                        <!-- {{ is_decimal(this.info_pbx_pack.rate) }} -->
                        </td>
                        <td class="px-5 py-2"> 
                        {{ this.info_pbx_pack.inclusive_minutes ? is_decimal(this.info_pbx_pack.inclusive_minutes) : 0 }}
                        </td>
                    </tr>
                </tbody>
              </table>

            <sw-input-group
              :label="$t('invoices.pbx_services.extension_templates')"
              class="mb-1"
            >
            </sw-input-group>
            <table class="w-full text-center item-table">
              <colgroup>
                <col style="width: 40%" />
                <col style="width: 40%" />
                <col style="width: 20%" />
              </colgroup>
              <thead class="bg-white border border-gray-200 border-solid">
                <tr>
                  <th
                    class="
                      px-5
                      text-sm
                      not-italic
                      font-medium
                      leading-5
                      text-gray-700
                      border-t border-b border-gray-200 border-solid
                    "
                  >
                    <span>
                      {{ $tc('invoices.pbx_services.name', 2) }}
                    </span>
                  </th>

                  <th
                    class="
                      px-5
                      text-sm
                      not-italic
                      font-medium
                      leading-5
                      text-gray-700
                      border-t border-b border-gray-200 border-solid
                    "
                  >
                    {{ $t('invoices.item.price') }}
                  </th>
                  <th
                    class="
                      px-5
                      text-sm
                      not-italic
                      font-medium
                      leading-5
                      text-gray-700
                      border-t border-b border-gray-200 border-solid
                    "
                  >
                    {{ $t('invoices.total') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="!this.info_pbx_pack.profile_extensions" class="py-3">
                  <td class="px-5 py-2">
                    {{ 'None' }}
                  </td>
                  <td class="px-5 py-2">
                    {{ 'None' }}
                  </td>
                  <td class="px-5 py-2">
                    {{ 'None' }}
                  </td>
                </tr>
                <tr v-else class="py-3">
                  <td class="flex px-5 py-2">
                    {{ `${this.info_pbx_pack.profile_extensions.name} (` }}
                    <div style="width: 20%">
                      <sw-input
                        step="any"
                        type="number"
                        v-model.number="countServicesExtension"
                        @input="modifManual()"
                      >
                      </sw-input>
                    </div>
                    {{ `)` }}
                  </td>
                  <td class="px-20 py-2">
                    <sw-input
                      step="any"
                      type="number"
                      v-model.number="profileExtensionRate"
                      @input="modifManual()"
                    >
                    </sw-input>
                  </td>
                  <td class="px-5 py-2">
                    {{ is_decimal(totalExtensionsDetail) }}
                  </td>
                </tr>
              </tbody>
            </table>
            <sw-input-group
              :label="$t('invoices.pbx_services.did_templates')"
              class="mb-1"
            >
            </sw-input-group>
            <table class="w-full text-center item-table">
              <colgroup>
                <col style="width: 40%" />
                <col style="width: 40%" />
                <col style="width: 20%" />
              </colgroup>
              <thead class="bg-white border border-gray-200 border-solid">
                <tr>
                  <th
                    class="
                      px-5
                      text-sm
                      not-italic
                      font-medium
                      leading-5
                      text-gray-700
                      border-t border-b border-gray-200 border-solid
                    "
                  >
                    <span>
                      {{ $tc('invoices.pbx_services.name', 2) }}
                    </span>
                  </th>

                  <th
                    class="
                      px-5
                      text-sm
                      not-italic
                      font-medium
                      leading-5
                      text-gray-700
                      border-t border-b border-gray-200 border-solid
                    "
                  >
                    {{ $t('invoices.item.price') }}
                  </th>
                  <th
                    class="
                      px-5
                      text-sm
                      not-italic
                      font-medium
                      leading-5
                      text-gray-700
                      border-t border-b border-gray-200 border-solid
                    "
                  >
                    {{ $t('invoices.total') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-if="!this.info_pbx_pack.profile_did && !this.info_did"
                  class="py-3"
                >
                  <td class="px-5 py-2">
                    {{ 'None' }}
                  </td>
                  <td class="px-5 py-2">
                    {{ 'None' }}
                  </td>
                  <td class="px-5 py-2">
                    {{ 'None' }}
                  </td>
                </tr>

                <tr v-else v-for="(item, i) in info_did" :key="i" class="py-3">
                  <td class="flex px-5 py-2">
                    {{ `${item[4]} (` }}
                    <div style="width: 20%">
                      <sw-input
                        step="any"
                        type="number"
                        v-model.number="item[1]"
                        @input="
                          editServicesDetail(
                            i,
                            1,
                            item[1],
                            /^[0-9]+([,.][0-9]+)?$/g
                          )
                        "
                      >
                      </sw-input>
                    </div>
                    {{ `)` }}
                  </td>

                  <td class="px-20 py-2">
                    <sw-input
                      step="any"
                      type="number"
                      v-model.number="item[2]"
                      @input="
                        editServicesDetail(
                          i,
                          2,
                          item[2],
                          /^[0-9]+([.][0-9]+)?$/g
                        )
                      "
                    >
                    </sw-input>
                  </td>
                  <td class="px-5 py-2">
                    {{ is_decimal(item[3]) }}
                  </td>
                </tr>
              </tbody>
            </table>
            <sw-input-group
              :label="$t('invoices.pbx_services.addicional_charges')"
              class="mb-1"
            >
            </sw-input-group>
            <table class="w-full text-center item-table">
              <colgroup>
                <col style="width: 30%" />
                <col style="width: 20%" />
                <col style="width: 10%" />
                <col style="width: 20%" />
                <col style="width: 20%" />
              </colgroup>
              <thead class="bg-white border border-gray-200 border-solid">
                <tr>
                  <th
                    class="
                      px-5
                      text-sm
                      not-italic
                      font-medium
                      leading-5
                      text-gray-700
                      border-t border-b border-gray-200 border-solid
                    "
                  >
                    <span>
                      {{ $tc('invoices.pbx_services.name', 2) }}
                    </span>
                  </th>

                  <th
                    class="
                      px-5
                      text-sm
                      not-italic
                      font-medium
                      leading-5
                      text-gray-700
                      border-t border-b border-gray-200 border-solid
                    "
                  >
                    {{ $t('invoices.pbx_services.type') }}
                  </th>
                  <th
                    class="
                      px-5
                      text-sm
                      not-italic
                      font-medium
                      leading-5
                      text-gray-700
                      border-t border-b border-gray-200 border-solid
                    "
                  >
                    {{ $t('invoices.pbx_services.quantity') }}
                  </th>
                  <th
                    class="
                      px-5
                      text-sm
                      not-italic
                      font-medium
                      leading-5
                      text-gray-700
                      border-t border-b border-gray-200 border-solid
                    "
                  >
                    {{ $t('invoices.item.price') }}
                  </th>
                  <th
                    class="
                      px-5
                      text-sm
                      not-italic
                      font-medium
                      leading-5
                      text-gray-700
                      border-t border-b border-gray-200 border-solid
                    "
                  >
                    {{ $t('invoices.item.total') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="!this.addicional_charges_extension" class="py-3">
                  <td class="px-5 py-2">
                    {{ 'None' }}
                  </td>
                  <td class="px-5 py-2">
                    {{ 'None' }}
                  </td>
                  <td class="px-5 py-2">
                    {{ 'None' }}
                  </td>
                  <td class="px-5 py-2">
                    {{ 'None' }}
                  </td>
                  <td class="px-5 py-2">
                    {{ 'None' }}
                  </td>
                </tr>

                <tr
                  v-else
                  v-for="d in this.addicional_charges_extension"
                  :key="d.id"
                  class="py-3"
                >
                  <td class="px-5 py-2">
                    {{ d.description }}
                  </td>
                  <td class="px-5 py-2">
                    {{
                      d.profile_extension_id != null
                        ? $t('invoices.pbx_services.extension_templates')
                        : $t('invoices.pbx_services.did_templates')
                    }}
                  </td>
                  <td class="px-5 py-2">
                    {{
                      d.profile_extension_id != null
                        ? countServicesExtension
                        : countServicesDid
                    }}
                  </td>
                  <td class="px-5 py-2">
                    <sw-input
                      step="any"
                      type="number"
                      v-model.number="d.amount"
                      @input="editadditionalCharges(), modifManual()"
                    >
                    </sw-input>
                  </td>
                  <td class="px-5 py-2">
                    {{
                      d.profile_extension_id != null
                        ? is_decimal(
                            (countServicesExtension
                              ? countServicesExtension
                              : 0) * parseFloat(d.amount)
                          )
                        : is_decimal(countServicesDid * parseFloat(d.amount))
                    }}
                  </td>
                </tr>
              </tbody>
            </table>
            <hr />

            <div class="mt-4">
              <label>{{ $t('invoices.pbx_services.rating_duration') }}:</label>
              <span class="ml-1">{{ this.totalDuration }}</span>
            </div>

            <!-- <sw-input-group
                :label="$t('invoices.pbx_services.calls')"
            >
            {{this.sumCall}}
            </sw-input-group>
            <sw-input-group
                :label="$t('invoices.pbx_services.exclusive_minute_consumed')"
                class="mb-4"
            >
            {{this.exclusiveSeconds}}
            </sw-input-group> -->
          </div>
        </div>
      </div>
      <!-- Notes, Custom Fields & Total Section -->
      <div
        class="
          block
          my-10
          invoice-foot
          lg:justify-between lg:flex lg:items-start
        "
      >
        <div class="w-full lg:w-1/2">
          <div class="mb-6">
            <sw-popup
              ref="notePopup"
              class="z-10 text-sm font-semibold leading-5 text-primary-400"
            >
              <div slot="activator" class="float-right mt-1">
                + {{ $t('general.insert_note') }}
              </div>
              <note-select-popup type="Invoice" @select="onSelectNote" />
            </sw-popup>
            <sw-input-group :label="$t('invoices.notes')">
              <base-custom-input
                v-model="newInvoice.notes"
                :fields="InvoiceFields"
              />
            </sw-input-group>
          </div>

          <div
            v-if="customFields.length > 0"
            class="
              grid
              gap-x-4 gap-y-2
              md:gap-x-8 md:gap-y-4
              grid-col-1
              md:grid-cols-2
            "
          >
            <sw-input-group
              v-for="(field, index) in customFields"
              :label="field.label"
              :required="field.is_required ? true : false"
              :key="index"
            >
              <component
                :type="field.type.label"
                :field="field"
                :is-edit="isEdit"
                :is="field.type + 'Field'"
                :invalid-fields="invalidFields"
                @update="setCustomFieldValue"
              />
            </sw-input-group>
          </div>

          <sw-input-group
            :label="$t('invoices.invoice_template')"
            class="mt-6 mb-1"
            required
          >
            <sw-button
              type="button"
              class="
                flex
                justify-center
                w-full
                text-sm text-black
                lg:w-auto
                hover:bg-gray-400
              "
              variant="gray"
              @click="openTemplateModal"
            >
              <span class="flex text-black">
                {{ $t('invoices.template') }} {{ getTemplateId }}
                <pencil-icon class="h-5 ml-2 -mr-1" />
              </span>
            </sw-button>
          </sw-input-group>
        </div>

        <div
          class="
            px-5
            py-4
            mt-6
            bg-white
            border border-gray-200 border-solid
            rounded
            invoice-total
            lg:mt-0
          "
        >
          <div
            v-if="this.packagesPrice > 0"
            class="flex items-center justify-between w-full"
          >
            <label
              class="text-sm font-semibold leading-5 text-gray-500 uppercase"
              >{{ $t('invoices.packages_price') }}</label
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
              <div
                v-html="$utils.formatMoney(this.logicopackagesPrice, currency)"
              />
            </label>
          </div>
          <div
            v-if="TotalServicesExtension > 0"
            class="flex items-center justify-between w-full"
          >
            <label
              class="text-sm font-semibold leading-5 text-gray-500 uppercase"
              >{{ $t('invoices.total_extension') }}</label
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
              <div
                v-html="$utils.formatMoney(TotalServicesExtension, currency)"
              />
            </label>
          </div>
          <div
            v-if="this.TotalServicesDid > 0"
            class="flex items-center justify-between w-full"
          >
            <label
              class="text-sm font-semibold leading-5 text-gray-500 uppercase"
              >{{ $t('invoices.total_did') }}</label
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
              <div
                v-html="$utils.formatMoney(this.TotalServicesDid, currency)"
              />
            </label>
          </div>
          <div
            v-if="this.TotalAddicionalCharges > 0"
            class="flex items-center justify-between w-full"
          >
            <label
              class="text-sm font-semibold leading-5 text-gray-500 uppercase"
              >{{ $t('invoices.addicional_charges') }}</label
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
              <div
                v-html="
                  $utils.formatMoney(this.TotalAddicionalCharges, currency)
                "
              />
            </label>
          </div>
          <div
            v-if="this.TotalCdrBilling > 0"
            class="flex items-center justify-between w-full"
          >
            <label
              class="text-sm font-semibold leading-5 text-gray-500 uppercase"
              >{{ $t('invoices.cdr_billing') }}</label
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
              <div
                v-html="$utils.formatMoney(this.TotalCdrBilling, currency)"
              />
            </label>
          </div>
          <div class="flex items-center justify-between w-full">
            <label
              class="text-sm font-semibold leading-5 text-gray-500 uppercase"
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
              <div v-html="$utils.formatMoney(subtotal, currency)" />
            </label>
          </div>
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
          <div
            v-if="newInvoice.avalara_bool == true"
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
              >Avalara Tax
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
              <div v-html="$utils.formatMoney(totalAvalaraTax, currency)" />
            </label>
          </div>
          <div
            v-if="discountPerItem === 'NO' || discountPerItem === null"
            class="flex items-center justify-between w-full mt-2"
          >
            <label
              class="text-sm font-semibold leading-5 text-gray-500 uppercase"
              >{{ $t('invoices.discount') }}</label
            >
            <div class="flex" style="width: 105px" role="group">
              <sw-input
                v-model="discount"
                :invalid="$v.newInvoice.discount_val.$error"
                class="border-r-0 rounded-tr-sm rounded-br-sm"
                @input="$v.newInvoice.discount_val.$touch()"
              />
              <sw-dropdown position="bottom-end">
                <sw-button
                  slot="activator"
                  type="button"
                  data-toggle="dropdown"
                  size="discount"
                  aria-haspopup="true"
                  aria-expanded="false"
                  style="height: 43px"
                  variant="white"
                >
                  <span class="flex">
                    {{
                      newInvoice.discount_type == 'fixed'
                        ? currency.symbol
                        : '%'
                    }}
                    <chevron-down-icon class="h-5" />
                  </span>
                </sw-button>

                <sw-dropdown-item @click="selectFixed">
                  {{ $t('general.fixed') }}
                </sw-dropdown-item>

                <sw-dropdown-item @click="selectPercentage">
                  {{ $t('general.percentage') }}
                </sw-dropdown-item>
              </sw-dropdown>
            </div>
          </div>
          <div v-if="taxPerItem ? 'NO' : null">
            <tax
              v-for="(tax, index) in newInvoice.taxes"
              :index="index"
              :total="subtotalWithDiscount"
              :key="tax.id"
              :tax="tax"
              :taxes="newInvoice.taxes"
              :currency="currency"
              :total-tax="totalSimpleTax"
              @remove="removeInvoiceTax"
              @update="updateTax"
            />
          </div>

          <sw-popup
            v-if="taxPerItem === 'NO' || taxPerItem === null"
            ref="taxModal"
            class="my-3 text-sm font-semibold leading-5 text-primary-400"
          >
            <div slot="activator" class="float-right pt-2 pb-5">
              + {{ $t('invoices.add_tax') }}
            </div>
            <tax-select-popup :taxes="newInvoice.taxes" @select="onSelectTax" />
          </sw-popup>
          <!-- GENERAL TAXES
          <div
            v-if="apply_tax_type == 'general'"
            class="flex items-center content-center justify-between w-full mt-2"
          >
            <label
              class="text-sm font-semibold leading-5 text-gray-500 uppercase"
              >{{ $t('packages.general_taxes') }}</label
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
              <div v-html="$utils.formatMoney(taxes_general, currency)" />
            </label>
          </div>-->
          <!-- GENERAL DISCOUNT -->
          <!--    <div
            v-if="showSelectdiscounts"
            class="flex items-center content-center justify-between w-full mt-2"
          >
            <label
              class="text-sm font-semibold leading-5 text-gray-500 uppercase"
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
                  `${parseFloat(discount_general).toFixed(2)} ${
                    discount_general_type == 'fixed' ? '' : '%'
                  }`
                "
              />
            </label>
          </div>
          TOTAL MOUNT -->
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
              class="text-sm font-semibold leading-5 text-gray-500 uppercase"
            >
              {{ $t('invoices.total') }} {{ $t('invoices.amount') }}:
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
              <div v-html="$utils.formatMoney(total, currency)" />
            </label>
          </div>
        </div>
      </div>
    </form>
    <base-loader v-else />
  </base-page>
</template>

<script>
import draggable from 'vuedraggable'
import InvoiceItem from './Item'
import CustomerSelect from './CustomerSelect'
import InvoiceStub from '../../stub/invoice'
import { mapActions, mapGetters, mapState } from 'vuex'
import moment from 'moment'
import Guid from 'guid'
import TaxStub from '../../stub/tax'
import Tax from './InvoiceTax'
import { PlusSmIcon } from '@vue-hero-icons/outline'
import {
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  ShoppingCartIcon,
  HashtagIcon,
} from '@vue-hero-icons/solid'
import CustomFieldsMixin from '../../mixins/customFields'
import invoice from '../../stub/invoice'
import additionalChargesDID from '../../stub/additionalChargesDID'

const {
  required,
  between,
  maxLength,
  numeric,
} = require('vuelidate/lib/validators')

export default {
  components: {
    InvoiceItem,
    CustomerSelect,
    Tax,
    draggable,
    PlusSmIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    ShoppingCartIcon,
    HashtagIcon,
  },
  mixins: [CustomFieldsMixin],
  data() {
    return {
      packag: {},
      packages: [],
      packagesPbx: [],
      itemsTemp: [],
      packagesPrice: 0,
      logicopackagesPrice: 0,
      TotalServicesExtension: 0,
      TotalServicesDid: 0,
      logicoserviceDid: 0,
      TotalAddicionalCharges: 0,
      TotalCdrBilling: 0,
      TotalItem: 0,
      pbx_amount_cal: [],
      info_pbx_pack: {},
      info_did: [],
      invoice_pbx_modify: 0,
      quantityExtension: 0,
      quantityDid: 0,
      addicional_charges_extension: null,
      addicional_charges_did: null,
      countServicesDid: null,
      countServicesExtension: null,
      totalExtensions: null,
      logicocountServicesExtension: null,
      profileExtensionRate: null,
      totalDuration: null,
      exclusiveSeconds: null,
      sumCall: null,
      newInvoice: {
        avalara_bool: false,
        package_bool: false,
        invoice_date: null,
        due_date: null,
        invoice_number: null,
        user_id: null,
        invoice_template_id: 1,
        customer_packages_id: null,
        pbx_service_id: null,
        pbx_service_price: 0,
        pbx_total_items: null,
        pbx_total_extension: null,
        pbx_total_did: null,
        pbxservice_date_prev: null,
        pbxservice_date_renewal: null,
        pbx_total_aditional_charges: 0,
        pbx_total_cdr: 0,
        sub_total: null,
        total: null,
        tax: null,
        avalara_tax: null,
        notes: null,
        discount_type: 'fixed',
        discount_val: 0,
        discount: 0,
        reference_number: null,
        get_call_detail_register_total: [],
        items: [
          {
            ...InvoiceStub,
            id: Guid.raw(),
            taxes: [{ ...TaxStub, id: Guid.raw() }],
            avalaraTax: 0,
            avalaraTaxes: [],
          },
        ],
        taxes: [],
        packages: [],
        invoice_ext: [],
        invoice_did: [],
        invoice_additional: [],
      },
      selectedCurrency: '',
      taxPerItem: null,
      discount_general_type: 'fixed',
      discount_general: 0,
      apply_tax_type: '',
      taxes_general: 0,
      showSelectdiscounts: false,
      discountPerItem: null,
      isLoadingInvoice: false,
      isLoadingData: false,
      isLoading: false,
      maxDiscount: 0,
      invoicePrefix: null,
      invoiceNumAttribute: null,
      InvoiceFields: [
        'customer',
        'customerCustom',
        'company',
        'invoice',
        'invoiceCustom',
      ],
      customerId: null,
      temporal: null,
    }
  },

  validations() {
    return {
      newInvoice: {
        invoice_date: {
          required,
        },
        due_date: {
          required,
        },
        discount_val: {
          between: between(0, this.subtotal),
        },
        reference_number: {
          maxLength: maxLength(255),
        },
      },
      selectedCustomer: {
        required,
      },
      invoiceNumAttribute: {
        required,
        numeric,
      },
    }
  },

  computed: {
    /*  ...mapState({
        info_did: state => state.selectedPbxService.InfoDid
      }), */
    ...mapGetters('company', ['itemDiscount']),

    ...mapGetters('company', ['defaultCurrency']),

    ...mapGetters('notes', ['notes']),

    ...mapGetters('invoice', [
      'getTemplateId',
      'selectedCustomer',
      'selectedNote',
    ]),

    ...mapGetters('invoiceTemplate', ['getInvoiceTemplates']),

    ...mapGetters('service', ['selectedService']),

    ...mapGetters('pbxService', ['selectedPbxService']),

    currency() {
      return this.selectedCurrency
    },

    pageTitle() {
      if (this.isEdit) {
        return this.$t('invoices.edit_invoice')
      }
      return this.$t('invoices.new_invoice')
    },

    isEdit() {
      if (this.$route.name === 'invoices.edit') {
        return true
      }
      return false
    },

    subtotalWithDiscount() {
      return this.subtotal - this.newInvoice.discount_val
    },

    total() {
      let discount_general = this.showSelectdiscounts
        ? parseFloat(this.discount_general) * 100
        : 0

      return this.subtotalWithDiscount + this.totalTax
    },

    subtotal() {
      let aux = this.newInvoice.items.reduce(function (a, b) {
        return a + b['total']
      }, 0)
      this.TotalItem = aux

      if (this.packagesPrice != null) {
        aux += this.logicopackagesPrice
        if (this.TotalServicesExtension) {
          aux += this.TotalServicesExtension
        }
        if (this.TotalServicesDid) {
          aux += this.TotalServicesDid
        }
        if (this.TotalAddicionalCharges) {
          aux += this.TotalAddicionalCharges
        }
        if (this.TotalCdrBilling) {
          aux += this.TotalCdrBilling
        }
      }
      return aux
    },

    discount: {
      get: function () {
        return this.newInvoice.discount
      },
      set: function (newValue) {
        if (this.newInvoice.discount_type === 'percentage') {
          this.newInvoice.discount_val = (this.subtotal * newValue) / 100
        } else {
          this.newInvoice.discount_val = Math.round(newValue * 100)
        }

        this.newInvoice.discount = newValue
      },
    },

    totalExtensionsDetail() {
      this.TotalServicesExtension =
        this.logicocountServicesExtension * this.profileExtensionRate
      return this.countServicesExtension * this.profileExtensionRate
    },
    totalSimpleTax() {
      return Math.round(
        window._.sumBy(this.newInvoice.taxes, function (tax) {
          if (!tax.compound_tax) {
            return tax.amount
          }
          return 0
        })
      )
    },

    totalCompoundTax() {
      return Math.round(
        window._.sumBy(this.newInvoice.taxes, function (tax) {
          if (tax.compound_tax) {
            return tax.amount
          }
          return 0
        })
      )
    },

    sumAllTaxes() {
      return Math.round(
        window._.sumBy(this.allTaxes, function (tax) {
          return tax.amount
        })
      )
    },

    totalAvalaraTax() {
      return Math.round(
        window._.sumBy(this.newInvoice.items, function (item) {
          return item.avalaraTax
        })
      )
    },

    totalTax() {
      let sumAllTaxes = parseFloat(this.sumAllTaxes)
      if (this.taxPerItem === 'NO' || this.taxPerItem === null) {
        return (
          this.totalSimpleTax +
          this.totalCompoundTax +
          (isNaN(this.totalAvalaraTax) ? 0 : this.totalAvalaraTax) +
          sumAllTaxes
        )
      }

      return Math.round(
        window._.sumBy(this.newInvoice.items, function (item) {
          return item.tax
        })
      )
    },

    allTaxes() {
      /*if (this.checkCustomerPackage()) {*/
        let taxes = []

        this.newInvoice.items.forEach((item) => {
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
      /*}*/
    },

    invoiceDateError() {
      if (!this.$v.newInvoice.invoice_date.$error) {
        return ''
      }
      if (!this.$v.newInvoice.invoice_date.required) {
        return this.$t('validation.required')
      }
    },

    dueDateError() {
      if (!this.$v.newInvoice.due_date.$error) {
        return ''
      }
      if (!this.$v.newInvoice.due_date.required) {
        return this.$t('validation.required')
      }
    },

    invoiceNumError() {
      if (!this.$v.invoiceNumAttribute.$error) {
        return ''
      }

      if (!this.$v.invoiceNumAttribute.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.invoiceNumAttribute.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },

    referenceError() {
      if (!this.$v.newInvoice.reference_number.$error) {
        return ''
      }

      if (!this.$v.newInvoice.reference_number.maxLength) {
        return this.$tc('validation.ref_number_maxlength')
      }
    },
  },

  watch: {
    packagesPrice(value, oldValue) {
      this.logicopackagesPrice = value * 100
    },
    countServicesExtension(value, oldValue) {
      this.logicocountServicesExtension = value * 100

      if (
        this.addicional_charges_extension.find(
          (element) => element.profile_extension_id != null
        )
      )
        this.editadditionalCharges()
    },
    'newInvoice.avalara_bool': function () {
      this.fetchInitialData()
    },
    selectedCustomer(newVal) {
      this.packages =
        this.selectedCustomer != null ? this.selectedCustomer.packages : []
      if (this.checkCustomerPackage()) {
        this.packag = this.packages.filter((pack) => {
          return pack.id == this.$route.query.package_id
        })[0]
        this.packageSeleted(this.packag)
      }
      if (newVal && newVal.currency) {
        this.selectedCurrency = newVal.currency
      } else {
        this.selectedCurrency = this.defaultCurrency
      }
    },
    selectedPbxService(newVal) {
      if (this.selectedPbxService != null) {
        this.packagesPbx.push(this.selectedPbxService.pbx_package)
      } else {
        this.packagesPbx = []
      }

      if (this.checkCustomerPbx()) {
        this.packag = this.packagesPbx.filter((pack) => {
          return pack.id == this.$route.query.package_id
        })[0]
        this.packageSeletedPbx(this.packag)
      }
      if (newVal && newVal.user.currency) {
        this.selectedCurrency = newVal.user.currency
      } else {
        this.selectedCurrency = this.defaultCurrency
      }
    },

    selectedNote() {
      if (this.selectedNote) {
        this.newInvoice.notes = this.selectedNote
      }
    },

    subtotal(newValue) {
      if (this.newInvoice.discount_type === 'percentage') {
        this.newInvoice.discount_val =
          (this.newInvoice.discount * newValue) / 100
      }
    },
  },

  created() {
    this.loadData()
    this.fetchInitialData()
    window.hub.$on('newTax', this.onSelectTax)
    if (this.$route.query.customer) {
      this.customerId = parseInt(this.$route.query.customer)
    }
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('invoice', [
      'addInvoice',
      'fetchInvoice',
      'getInvoiceNumber',
      'selectCustomer',
      'updateInvoice',
      'resetSelectedNote',
    ]),

    ...mapActions('invoiceTemplate', ['fetchInvoiceTemplates']),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('pack', ['fetchPackage']),

    ...mapActions('item', ['fetchItems']),

    ...mapActions('taxType', ['fetchTaxTypes']),

    ...mapActions('customFields', ['fetchCustomFields']),

    ...mapActions('service', ['fetchViewService', 'fetchResponse']),

    ...mapActions('pbxService', [
      'fetchPbxService',
      'fetchTaxType',
      'fetchDIDs',
      'fetchExtensions',
    ]),

    is_decimal(val) {
      if (val) {
        if (val % 1 == 0) return val
        return val.toFixed(2)
      }
    },

    modifManual() {
      if (this.invoice_pbx_modify == 0) this.invoice_pbx_modify = 1
    },
    async editServicesDetail(i, pos, event, regexp) {
      if (this.invoice_pbx_modify == 0) this.invoice_pbx_modify = 1

      this.logicoserviceDid = 0
      this.TotalServicesDid = 0

      if (pos === 1) {
        this.info_did[i][3] = this.info_did[i][pos] * this.info_did[i][2]
      } else {
        this.info_did[i][3] = this.info_did[i][1] * this.info_did[i][pos]
      }

      this.info_did.forEach((inf) => {
        this.TotalServicesDid += inf[3] * 100
        this.logicoserviceDid = inf[3] * 100
      })
    },

    async editadditionalCharges() {
      this.TotalAddicionalCharges = 0
      this.addicional_charges_extension.forEach((inf) => {
        if ((inf.profile_extension_id = 1)) {
          this.TotalAddicionalCharges +=
            inf.amount * this.countServicesExtension * 100
        } else {
          this.TotalAddicionalCharges +=
            inf.amount * this.countServicesDid * 100
        }
      })
    },

    onDiscounts(val) {
      this.showSelectdiscounts = val
      this.discountPerItem = val ? 'NO' : 'YES'
    },

    packageSeletedPbx(val) {
      this.newInvoice.packages = []
      this.newInvoice.packages.push(val)

      let statusPackageDiscount = val.packages_discount == 0 ? false : true
      this.onDiscounts(statusPackageDiscount)
      /* General */
      this.discount_general_type = this.selectedPbxService.allow_discount_type
      this.newInvoice.discount_type =
        this.selectedPbxService.allow_discount_type
      this.packagesPrice = this.selectedPbxService.pbx_package.rate
      this.TotalServicesExtension =
        this.selectedPbxService.TotalServicesExtension != 0
          ? this.selectedPbxService.TotalServicesExtension * 100
          : 0
      this.totalExtensions = this.selectedPbxService.TotalServicesExtension
      this.TotalServicesDid =
        this.selectedPbxService.TotalServicesDid != 0
          ? this.selectedPbxService.TotalServicesDid * 100
          : 0
      this.TotalAddicionalCharges =
        this.selectedPbxService.TotalAddicionalCharges != 0
          ? this.selectedPbxService.TotalAddicionalCharges * 100
          : 0
      /* this.TotalCdrBilling= this.selectedPbxService.TotalCdrBilling !=0 ? (this.selectedPbxService.TotalCdrBilling*100) : 0; */
      this.TotalCdrBilling =
        this.selectedPbxService.TotalCdrBilling != 0 &&
        this.selectedPbxService.user.status_payment != 'prepaid'
          ? this.selectedPbxService.TotalCdrBilling * 100
          : 0

      this.discount_general = Math.trunc(
        this.selectedPbxService.allow_discount_value
      )
      this.discount = this.discount_general

      this.resetItemsTemp()
      this.newInvoice.items.splice(0, 1)

      if (this.selectedPbxService) {
        if (this.selectedPbxService.get_items) {
          // items = this.selectedService.items
          this.reconfigItemss(this.selectedPbxService.get_items, true)
        }
      }

      if (this.selectedPbxService.pbx_service_tax_types.length > 0) {
        let TotalGeneralTax = 0.0
        /* console.log("Tercera entrada",this.subtotal) */
        this.selectedPbxService.pbx_service_tax_types.forEach((tax) => {
          TotalGeneralTax += Math.round((this.subtotal * tax.percent) / 100)
        })

        this.taxes_general = TotalGeneralTax
      }
    },

    packageSeleted(val) {
      this.newInvoice.packages = []
      this.newInvoice.packages.push(val)

      this.getPackage(val)
    },

    async getPackage(val) {
      let res = await this.fetchPackage(val.id)
      const {
        items,
        apply_tax_type,
        tax_types,
        packages_discount,
        discount_general_type,
        discount_general,
      } = res.data.response

      let statusPackageDiscount = packages_discount == 0 ? false : true
      this.onDiscounts(statusPackageDiscount)

      this.discount_general_type = this.selectedService.discount_type
      this.newInvoice.discount_type = this.selectedService.discount_type
      this.discount_general = Math.trunc(this.selectedService.discount)
      this.discount = this.discount_general

      this.resetItemsTemp()
      this.newInvoice.items.splice(0, 1)

      if (this.selectedService) {
        if (this.selectedService.items) {
          // items = this.selectedService.items
          this.reconfigItems(this.selectedService.items, true)
        }
      } else {
        this.reconfigItems(items, true)
      }

      /*TAX GENERAL */
      if (tax_types.length > 0) {
        this.apply_tax_type = apply_tax_type
        let TotalGeneralTax = 0.0
        /* console.log("Cuarta entrada") */
        tax_types.forEach((tax) => {
          TotalGeneralTax += Math.round((this.subtotal * tax.percent) / 100)
        })

        this.taxes_general = TotalGeneralTax
      }
    },

    reconfigItems(items, temp = false) {
      let itemsArray = []
      items.forEach((item) => {
        /* if (item.taxes.length == 0) {
          item.taxes = [{ ...TaxStub, id: Guid.raw() }]
        } */
        //console.log("----spd---");
        //console.log(item);
        item.temp = temp
        item.quantity = item.pivot ? item.pivot.quantity : item.quantity
        item.item_id = item.pivot ? item.pivot.items_id : item.item_id
        item.item_group_id = item.pivot
          ? item.pivot.item_group_id
          : item.item_group_id
        item.price = item.pivot ? item.pivot.price : item.price
        item.discount_type = item.pivot
          ? item.pivot.discount_type
          : item.discount_type
        item.discount = item.pivot ? item.pivot.discount : item.discount
        item.discount_val = item.pivot
          ? item.pivot.discount_val
          : item.discount_val
        item.tax = item.pivot ? item.pivot.tax : item.tax
        item.description = item.pivot
          ? item.pivot.description
          : item.description
        item.package_id = item.pivot ? item.pivot.package_id : item.package_id
        item.total = item.pivot ? item.pivot.total : item.total
        item.avalaraTax = 0
        item.totalTax = 0
        item.totalSimpleTax = 0
        item.invoice_id = null
        item.totalCompoundTax = 0
        item.no_taxable = item.no_taxable

        if (item.taxes.length == 0) {
          this.newInvoice.items.push({
            ...item,
            id: Guid.raw(),
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          })
        } else {
          this.newInvoice.items.push({
            ...item,
          })
        }

        itemsArray.push(item)
      })
    },

    reconfigItemss(items, temp = false) {
      let itemsArray = []
      items.forEach((item) => {
        /* if (item.taxes.length == 0) {
          item.taxes = [{ ...TaxStub, id: Guid.raw() }]
        } */
        //console.log("----spd---");
        //console.log(item);
        item.temp = temp
        item.quantity = item.pivot ? item.pivot.quantity : item.quantity
        item.item_id = item.pivot ? item.pivot.items_id : item.item_id
        item.item_group_id = item.pivot
          ? item.pivot.item_group_id
          : item.item_group_id
        item.price = item.pivot ? item.pivot.price : item.price
        item.discount_type = item.pivot
          ? item.pivot.discount_type
          : item.discount_type
        item.discount = item.pivot ? item.pivot.discount : item.discount
        item.discount_val = item.pivot
          ? item.pivot.discount_val
          : item.discount_val
        item.tax = item.pivot ? item.pivot.tax : item.tax
        item.description = item.pivot
          ? item.pivot.description
          : item.description
        item.total = item.pivot ? item.pivot.total : item.total
        item.avalaraTax = 0
        item.totalTax = 0
        item.totalSimpleTax = 0
        item.invoice_id = null
        item.totalCompoundTax = 0
        item.no_taxable = item.no_taxable
        item.taxes = []
        if (item.taxes.length == 0) {
          this.newInvoice.items.push({
            ...item,
            id: Guid.raw(),
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          })
        } else {
          this.newInvoice.items.push({
            ...item,
          })
        }

        itemsArray.push(item)
      })
    },

    reconfigItemsTax(items) {
      let itemsArray = []
      items.forEach((item) => {
        item.temp = false
        item.avalaraTax = 0
        item.totalTax = 0
        item.totalSimpleTax = 0
        item.invoice_id = null
        item.totalCompoundTax = 0
        item.no_taxable = item.no_taxable

        if (item.taxes.length == 0) {
          itemsArray.push({
            ...item,
            id: Guid.raw(),
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          })
        } else {
          itemsArray.push({
            ...item,
          })
        }

        this.newInvoice.items = itemsArray
      })
    },

    resetItemsTemp() {
      this.newInvoice.items = this.newInvoice.items.filter((item) => {
        if (item.temp != true) return item
      })
    },

    selectFixed() {
      if (this.newInvoice.discount_type === 'fixed') {
        return
      }

      this.newInvoice.discount_val = Math.round(this.newInvoice.discount * 100)
      this.newInvoice.discount_type = 'fixed'
    },

    selectPercentage() {
      if (this.newInvoice.discount_type === 'percentage') {
        return
      }

      this.newInvoice.discount_val =
        (this.subtotal * this.newInvoice.discount) / 100

      this.newInvoice.discount_type = 'percentage'
    },

    updateTax(data) {
      Object.assign(this.newInvoice.taxes[data.index], { ...data.item })
    },

    async fetchInitialData() {
      this.isLoadingData = true

      if (!this.isEdit) {
        let response = await this.fetchCompanySettings([
          'discount_per_item',
          'tax_per_item',
        ])

        if (response.data) {
          this.discountPerItem = response.data.discount_per_item
          this.taxPerItem = response.data.tax_per_item
        }
      }

      Promise.all([
        this.fetchItems({
          filter: {},
          orderByField: '',
          orderBy: '',
          avalara_bool: this.newInvoice.avalara_bool,
          limit: 'all',
        }),
        this.fetchInvoiceTemplates(),
        this.resetSelectedNote(),
        this.getInvoiceNumber(),
        this.fetchCompanySettings(['invoice_auto_generate']),
      ])
        .then(async ([res1, res2, res3, res4, res5]) => {
          if (
            !this.isEdit &&
            res5.data &&
            res5.data.invoice_auto_generate === 'YES'
          ) {
            if (res4.data) {
              this.invoiceNumAttribute = res4.data.nextNumber
              this.invoicePrefix = res4.data.prefix
            }
          } else {
            this.invoicePrefix = res4.data.prefix
          }
   
          this.newInvoice.invoice_date = moment().toString()
         
          this.newInvoice.due_date = moment().add(7, 'days').toString()

          // this.discountPerItem = res5.data.discount_per_item
          // this.taxPerItem = res5.data.tax_per_item
          this.isLoadingData = false
        })
        .catch((error) => {
          console.log(error)
        })
    },
    checkCustomerPackage() {
      if (this.$route.query.from && this.$route.query.from == 'customer') {
        return true
      }

      return false
    },
    checkCustomerPbx() {
      if (this.$route.query.from && this.$route.query.from == 'pbx_services') {
        return true
      }

      return false
    },
    calDuration(mil) {
      if (mil > 0) {
        let days = Math.floor(mil / (1000 * 60 * 60 * 24)),
          hours = (
            '0' + Math.floor((mil % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
          ).slice(-2),
          minutes = (
            '0' + Math.floor((mil % (1000 * 60 * 60)) / (1000 * 60))
          ).slice(-2),
          seconds = ('0' + Math.floor((mil % (1000 * 60)) / 1000)).slice(-2)
        return `${days}:${hours}:${minutes}:${seconds}`
      }
      return `00:00:00:00`
    },

    async loadData() {
      console.log("entro aqui");
      if (this.checkCustomerPackage()) {
        this.customerId = parseInt(this.$route.query.customer_id)
        this.newInvoice.package_bool = true
        this.newInvoice.notes = `Service ${this.$route.query.code} ${
          this.customerId
        } ${moment(new Date()).format('DD/MM/YYYY')}`
        this.newInvoice.customer_packages_id = parseInt(
          this.$route.query.customer_packages_id
        )
        await this.fetchViewService(this.$route.query.customer_packages_id)

        this.newInvoice.taxes = this.selectedService.taxes
        this.newInvoice.pbxservice_date_prev = this.selectedService.date_prev
        this.newInvoice.pbxservice_date_renewal = this.selectedService.renewal_date
      }

      if (this.checkCustomerPbx()) {
        this.customerId = parseInt(this.$route.query.customer_id)
        this.newInvoice.package_bool = true
        this.newInvoice.notes = `Service ${this.$route.query.code} ${
          this.customerId
        } ${moment(new Date()).format('DD/MM/YYYY')}`
        this.newInvoice.pbx_service_id = parseInt(
          this.$route.query.pbx_service_id
        )
        await this.fetchPbxService(this.$route.query.pbx_service_id)
        //Prueba de taxes
        let subDes, amount
        let nuev_taxes = [...this.selectedPbxService.pbx_service_tax_types]
        this.info_pbx_pack = { ...this.selectedPbxService.pbx_package }
        /* this.info_did=this.selectedPbxService.InfoDid.slice(); */
        this.info_did = [...this.selectedPbxService.InfoDid]
        this.profileExtensionRate = this.info_pbx_pack.profile_extensions.rate
        this.countServicesExtension =
          this.selectedPbxService.CountServicesExtension
        this.countServicesDid = this.selectedPbxService.CountServicesDid
        this.totalDuration = this.selectedPbxService.TotalDuration
          ? this.calDuration(this.selectedPbxService.TotalDuration * 1000)
          : `00:00:00:00`
        this.newInvoice.pbxservice_date_prev = this.selectedPbxService.date_prev
        this.newInvoice.pbxservice_date_renewal = moment(new Date()).format(
          'YYYY-MM-DD'
        )

        /* this.sumCall = this.selectedPbxService.SumCalls ? this.selectedPbxService.SumCalls : 0
        this.exclusiveSeconds= this.selectedPbxService.ExclusiveSeconds ? this.calDuration((this.selectedPbxService.ExclusiveSeconds*1000)) : 0; */
        //invoices_extension
        if (this.selectedPbxService.pbx_service_extensions.length > 0) {
          this.selectedPbxService.pbx_service_extensions.forEach((imp) => {
            if (imp.extension != null) {
              this.newInvoice.invoice_ext.push({
                template_extension_id: this.info_pbx_pack.profile_extensions.id,
                template_extension_name:
                  this.info_pbx_pack.profile_extensions.name,
                template_extension_rate:
                  this.info_pbx_pack.profile_extensions.rate,
                pbx_extension_id: imp.extension.id,
                pbx_extension_name: imp.extension.name,
                pbx_extension_ext: imp.extension.ext,
                pbx_extension_email: imp.extension.email,
                pbx_extension_ua_fullname: imp.extension.ua_fullname,
              })
            }
          })
        }
        //invoices_dids
        if (this.selectedPbxService.pbx_service_dids.length > 0) {
          this.selectedPbxService.pbx_service_dids.forEach((imp) => {
            if (imp.did != null) {
              this.info_did.forEach((element) => {
                let val = imp.custom_did_id ? imp.custom_did_id : 0
                if (val == element[0]) {
                  this.newInvoice.invoice_did.push({
                    template_did_id: this.info_pbx_pack.profile_did.id,
                    template_did_name: this.info_pbx_pack.profile_did.name,
                    template_did_rate: this.info_pbx_pack.profile_did.did_rate,
                    pbx_did_id: imp.did.id,
                    custom_did_id: element[0],
                    custom_did_rate: element[2],
                    pbx_did_number: imp.did.number,
                    pbx_did_server: imp.did.server,
                    pbx_did_trunk: imp.did.trunk,
                    pbx_did_type: imp.did.type,
                  })
                }
              })
            }
          })
        }
        //invoices_additional_charges extension
        if (this.info_pbx_pack.profile_extensions != null) {
          if (
            this.info_pbx_pack.profile_extensions.aditional_charges_a.length > 0
          ) {
            this.info_pbx_pack.profile_extensions.aditional_charges_a.forEach(
              (imp) => {
                this.newInvoice.invoice_additional.push({
                  additional_charge_name: imp.description,
                  additional_charge_amount: parseFloat(imp.amount),
                  template_name: this.info_pbx_pack.profile_extensions.name,
                  additional_charge_type: 'extension',
                  qty: this.countServicesExtension,
                  total: this.countServicesExtension * parseFloat(imp.amount),
                })
              }
            )
          }
        }
        //invoices_additional_charges did
        if (this.info_pbx_pack.profile_did != null) {
          if (this.info_pbx_pack.profile_did.aditional_charges_a.length > 0) {
            this.info_pbx_pack.profile_did.aditional_charges_a.forEach(
              (imp) => {
                this.newInvoice.invoice_additional.push({
                  additional_charge_name: imp.description,
                  additional_charge_amount: parseFloat(imp.amount),
                  template_name: this.info_pbx_pack.profile_did.name,
                  additional_charge_type: 'did',
                  qty: this.countServicesDid,
                  total: this.countServicesDid * parseFloat(imp.amount),
                })
              }
            )
          }
        }

        if (
          this.selectedPbxService.pbx_package.profile_extensions != null &&
          this.selectedPbxService.pbx_package.profile_did != null
        ) {
          if (
            this.selectedPbxService.pbx_package.profile_extensions
              .aditional_charges_a != null &&
            this.selectedPbxService.pbx_package.profile_did.aditional_charges_a
          ) {
            this.addicional_charges_extension = [
              ...this.selectedPbxService.pbx_package.profile_extensions
                .aditional_charges_a,
              ...this.selectedPbxService.pbx_package.profile_did
                .aditional_charges_a,
            ]
            this.quantityExtension =
              this.selectedPbxService.pbx_package.profile_extensions.aditional_charges_a.length
            this.quantityDid =
              this.selectedPbxService.pbx_package.profile_did.aditional_charges_a.length
          }
        } else if (
          this.selectedPbxService.pbx_package.profile_extensions != null
        ) {
          if (
            this.selectedPbxService.pbx_package.profile_extensions
              .aditional_charges_a != null
          ) {
            this.addicional_charges_extension = [
              ...this.selectedPbxService.pbx_package.profile_extensions
                .aditional_charges_a,
            ]
            this.quantityExtension = this.addicional_charges_extension.length
          }
        } else if (this.selectedPbxService.pbx_package.profile_did != null) {
          if (
            this.selectedPbxService.pbx_package.profile_did
              .aditional_charges_a != null
          ) {
            this.addicional_charges_extension = [
              ...this.selectedPbxService.pbx_package.profile_did
                .aditional_charges_a,
            ]
            this.quantityDid = this.addicional_charges_extension.length
          }
        }

        if (this.selectedPbxService.allow_discount_type == 'percentage') {
          subDes =
            (this.subtotal / 100) *
            (parseFloat(this.selectedPbxService.allow_discount_value) / 100)
        } else {
          subDes =
            this.subtotal / 100 -
            parseFloat(this.selectedPbxService.allow_discount_value)
        }
        if (nuev_taxes.length > 0) {
          this.selectedPbxService.pbx_service_tax_types.forEach(
            (imp, index) => {
              if (!nuev_taxes[index].amount) {
                delete nuev_taxes[index].amount
                nuev_taxes[index].amount = 0
              }

              if (this.selectedPbxService.allow_discount_type == 'percentage') {
                amount =
                  (this.subtotal / 100 - subDes) *
                  (parseFloat(imp.percent) / 100)
              } else {
                amount = subDes * (parseFloat(imp.percent) / 100)
              }

              nuev_taxes[index].amount = amount * 100
              nuev_taxes[index].tax_type_id = nuev_taxes[index].tax_types_id
              /* console.log(nuev_taxes[index].percent,parseFloat(nuev_taxes[index].percent)) */
              /*  nuev_taxes[index].percent=parseFloat(nuev_taxes[index].percent); */
            }
          )
        }

        this.newInvoice.taxes = nuev_taxes
      }

      if (this.isEdit) {
        this.isLoadingInvoice = true
        Promise.all([
          this.fetchInvoice(this.$route.params.id),
          this.fetchCustomFields({
            type: 'Invoice',
            limit: 'all',
          }),
          this.fetchTaxTypes({ limit: 'all' }),
        ])
          .then(async ([res1, res2]) => {
            if (res1.data) {
              this.customerId = res1.data.invoice.user_id
              this.newInvoice = res1.data.invoice
              this.formData = { ...this.formData, ...res1.data.invoice }

              this.packag = this.formData.packages[0]
              //this.packageSeleted(this.formData.packages[0])
              this.reconfigItemsTax(this.formData.items)

              this.newInvoice.invoice_date = moment(
                res1.data.invoice.invoice_date,
                'YYYY-MM-DD'
              ).toString()

              this.newInvoice.due_date = moment(
                res1.data.invoice.due_date,
                'YYYY-MM-DD'
              ).toString()
              console.log( this.newInvoice);
              
              this.discountPerItem = res1.data.invoice.discount_per_item
              this.selectedCurrency = this.defaultCurrency
              this.invoiceNumAttribute = res1.data.nextInvoiceNumber
              this.invoicePrefix = res1.data.invoicePrefix
              this.taxPerItem = res1.data.invoice.tax_per_item
              let fields = res1.data.invoice.fields
              if (res1.data.invoice.pbx_service_id != null) {
                ;(this.newInvoice.pbx_service_id =
                  res1.data.invoice.pbx_service_id),
                  await this.fetchPbxService(this.newInvoice.pbx_service_id)
                this.info_pbx_pack = { ...this.selectedPbxService.pbx_package }
                this.profileExtensionRate =
                  this.info_pbx_pack.profile_extensions.rate
                /* this.info_did=[...res1.data.invoice.service_details] */
                this.invoice_pbx_modify = res1.data.invoice.invoice_pbx_modify
                if (this.invoice_pbx_modify) {
                  this.countServicesExtension =
                    res1.data.invoice.service_details.find(
                      (element) => element.count_extension != null
                    ).count_extension
                  this.info_did = res1.data.invoice.service_details.map(
                    (item) => {
                      const vect = []
                      vect.push(item.count)
                      vect.push(item.count_did)
                      vect.push(item.price_did)
                      vect.push(item.count_did * item.price_did)
                      vect.push(item.name)
                      vect.push(item.id)
                      return vect
                    }
                  )
                } else {
                  this.countServicesExtension =
                    this.selectedPbxService.CountServicesExtension
                  this.info_did = [...this.selectedPbxService.InfoDid]
                }
                this.newInvoice.pbxservice_date_prev =
                  this.selectedPbxService.date_prev

                if (
                  this.selectedPbxService.pbx_package.profile_extensions !=
                    null &&
                  this.selectedPbxService.pbx_package.profile_did != null
                ) {
                  if (
                    this.selectedPbxService.pbx_package.profile_extensions
                      .aditional_charges_a != null &&
                    this.selectedPbxService.pbx_package.profile_did
                      .aditional_charges_a
                  ) {
                    this.addicional_charges_extension = [
                      ...this.selectedPbxService.pbx_package.profile_extensions
                        .aditional_charges_a,
                      ...this.selectedPbxService.pbx_package.profile_did
                        .aditional_charges_a,
                    ]
                    this.quantityExtension =
                      this.selectedPbxService.pbx_package.profile_extensions.aditional_charges_a.length
                    this.quantityDid =
                      this.selectedPbxService.pbx_package.profile_did.aditional_charges_a.length
                  }
                } else if (
                  this.selectedPbxService.pbx_package.profile_extensions != null
                ) {
                  if (
                    this.selectedPbxService.pbx_package.profile_extensions
                      .aditional_charges_a != null
                  ) {
                    this.addicional_charges_extension = [
                      ...this.selectedPbxService.pbx_package.profile_extensions
                        .aditional_charges_a,
                    ]
                    this.quantityExtension =
                      this.addicional_charges_extension.length
                  }
                } else if (
                  this.selectedPbxService.pbx_package.profile_did != null
                ) {
                  if (
                    this.selectedPbxService.pbx_package.profile_did
                      .aditional_charges_a != null
                  ) {
                    this.addicional_charges_extension = [
                      ...this.selectedPbxService.pbx_package.profile_did
                        .aditional_charges_a,
                    ]
                    this.quantityDid = this.addicional_charges_extension.length
                  }
                }

                /* this.countServicesExtension = this.selectedPbxService.CountServicesExtension */

                this.countServicesDid = this.selectedPbxService.CountServicesDid
                this.totalDuration = this.selectedPbxService.TotalDuration
                  ? this.calDuration(
                      this.selectedPbxService.TotalDuration * 1000
                    )
                  : `00:00:00:00`
                /* this.sumCall = this.selectedPbxService.SumCalls ? this.selectedPbxService.SumCalls : 0
                this.exclusiveSeconds= this.selectedPbxService.ExclusiveSeconds ? this.calDuration((this.selectedPbxService.ExclusiveSeconds*1000)) : 0; */

                ;(this.packagesPrice = parseFloat(
                  res1.data.invoice.pbx_service_price
                )) /* Ojo */,
                  (this.TotalServicesExtension = parseFloat(
                    res1.data.invoice.pbx_total_extension
                  )),
                  (this.TotalServicesDid = parseFloat(
                    res1.data.invoice.pbx_total_did
                  )),
                  (this.TotalAddicionalCharges = parseFloat(
                    res1.data.invoice.pbx_total_aditional_charges
                  )),
                  (this.TotalCdrBilling =
                    parseFloat(res1.data.invoice.pbx_total_cdr) * 100)
              }

              if (res2.data) {
                let customFields = res2.data.customFields.data
                this.setEditCustomFields(fields, customFields)
              }
            }

            this.isLoadingInvoice = false
          })
          .catch((error) => {
            console.log(error)
          })

        return true
      }
      this.isLoadingInvoice = true
      await this.setInitialCustomFields('Invoice')
      await this.fetchTaxTypes({ limit: 'all' })
      this.selectedCurrency = this.defaultCurrency
      console.log(this.newInvoice.invoice_date)
      this.newInvoice.invoice_date = moment().toString()
      console.log(this.newInvoice.invoice_date)
      this.newInvoice.due_date = moment().add(7, 'days').toString()
      this.isLoadingInvoice = false
    },

    openTemplateModal() {
      this.openModal({
        title: this.$t('general.choose_template'),
        componentName: 'InvoiceTemplate',
        data: this.getInvoiceTemplates,
      })
    },

    addItem() {
      this.newInvoice.items.push({
        ...InvoiceStub,
        id: Guid.raw(),
        taxes: [{ ...TaxStub, id: Guid.raw() }],
        avalaraTax: 0,
        temp: false,
        avalaraTaxes: [],
      })
    },

    removePackage(idPackage) {
      for (var i = this.newInvoice.packages.length - 1; i >= 0; --i) {
        if (this.newInvoice.packages[i].id == idPackage) {
          this.newInvoice.packages.splice(i, 1)
        }
      }
    },

    removeItem(index) {
      this.newInvoice.items.splice(index, 1)
    },

    updateItem(data) {
      Object.assign(this.newInvoice.items[data.index], { ...data.item })
    },

    async submitForm() {
      let validate = await this.touchCustomField()

      if (!this.checkValid() || validate.error) {
        return false
      }

      this.isLoading = true
      this.newInvoice.invoice_number =
        this.invoicePrefix + '-' + this.invoiceNumAttribute

      this.info_pbx_pack.rate = this.packagesPrice
        ? parseFloat(this.packagesPrice)
        : 0
      if (this.info_pbx_pack.profile_extensions != null)
        this.info_pbx_pack.profile_extensions.rate = this.profileExtensionRate
          ? parseFloat(this.profileExtensionRate)
          : 0
      let data = {
        ...this.formData,
        ...this.newInvoice,
        sub_total: this.subtotal,
        total: this.total,
        tax: this.totalTax,
        user_id: null,
        pbx_service_price: this.packagesPrice,
        pbx_total_items: this.TotalItem,
        pbx_total_extension: this.TotalServicesExtension,
        pbx_total_did: this.TotalServicesDid,
        pbx_total_aditional_charges: this.TotalAddicionalCharges,
        pbx_total_cdr: this.TotalCdrBilling,
        invoice_template_id: this.getTemplateId,
        banType: this.newInvoice.pbx_service_id ? false : true,
        pbx_packages: this.info_pbx_pack,
        pbx_service_detail: this.info_did,
        count_extension: this.countServicesExtension,
        invoice_pbx_modify: this.invoice_pbx_modify,
      }

      if (this.selectedCustomer != null) {
        data.user_id = this.selectedCustomer.id
      } else {
        data.user_id = this.customerId
      }

      if (this.$route.name === 'invoices.edit') {
        this.submitUpdate(data)
        return
      }

      this.submitCreate(data)
    },

    submitCreate(data) {
      this.addInvoice(data)
        .then((res) => {
          if (res.data) {
            this.$router.push(`/admin/invoices/${res.data.invoice.id}/view`)

            window.toastr['success'](this.$t('invoices.created_message'))
          }

          this.isLoading = false
        })
        .catch((err) => {
          this.isLoading = false
        })
    },

    submitUpdate(data) {
      this.updateInvoice(data)
        .then((res) => {
          this.isLoading = false
          if (res.data.success) {
            this.$router.push(`/admin/invoices/${res.data.invoice.id}/view`)
            window.toastr['success'](this.$t('invoices.updated_message'))
          }

          if (res.data.error === 'invalid_due_amount') {
            window.toastr['error'](
              this.$t('invoices.invalid_due_amount_message')
            )
          }
        })
        .catch((err) => {
          this.isLoading = false
        })
    },

    checkItemsData(index, isValid) {
      this.newInvoice.items[index].valid = isValid
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

      this.newInvoice.taxes.push({
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

    removeInvoiceTax(index) {
      this.newInvoice.taxes.splice(index, 1)
    },

    checkValid() {
      this.$v.newInvoice.$touch()
      this.$v.selectedCustomer.$touch()
      this.$v.invoiceNumAttribute.$touch()

      window.hub.$emit('checkItems')
      let isValid = true
      if (
        !this.newInvoice.pbx_service_id ||
        (this.newInvoice.pbx_service_id && this.newInvoice.items.length > 0)
      ) {
        this.newInvoice.items.forEach((item) => {
          if (!item.valid) {
            isValid = false
          }
        })
      }
      if (
        !this.$v.selectedCustomer.$invalid &&
        !this.$v.invoiceNumAttribute.$invalid &&
        this.$v.newInvoice.$invalid === false &&
        isValid === true
      ) {
        return true
      }
      return false
    },
    onSelectNote(data) {
      this.newInvoice.notes = '' + data.notes
      this.$refs.notePopup.close()
    },
  },
}
</script>

<style lang="scss">
.invoice-create-page {
  .invoice-foot {
    .invoice-total {
      min-width: 390px;
    }
  }
  @media (max-width: 480px) {
    .invoice-foot {
      .invoice-total {
        min-width: 384px;
      }
    }
  }
}
</style>
