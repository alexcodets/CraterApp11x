<style>
 input:checked~.tab-content {
    max-height: 100vh;
    min-height: 60vh;
}

</style>

<template>
  <div
    :class="{
      'pt-6 mt-5 border-t-2 border-solid lg:pt-8 md:pt-4': !isServiceView,
    }"
    :style="{ 'border-top-color: #f9fbff': !isServiceView }"
  >
    <div class="col-span-12">
      <p class="text-gray-500 uppercase sw-section-title">
        {{ $t('customers.basic_info') }}
      </p>

      <div
        class="grid grid-cols-1 gap-4 mt-5 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1"
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
            {{ $t('customers.customer_number') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              selectedViewCustomer.customer &&
              selectedViewCustomer.customer.customcode
                ? selectedViewCustomer.customer.customcode
                : ''
            }}
          </p>
        </div>

        <div>
          <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800 " >
            {{ $t('customers.display_name') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              selectedViewCustomer.customer &&
              selectedViewCustomer.customer.name
                ? selectedViewCustomer.customer.name
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
            {{ $t('customers.primary_contact_name') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              selectedViewCustomer.customer &&
              selectedViewCustomer.customer.contact_name
                ? selectedViewCustomer.customer.contact_name
                : ''
            }}
          </p>
        </div>
      </div>

      <!-- <div
        v-if="$route.name === 'customers.view'"
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
            {{ $t('customers.email') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              selectedViewCustomer.customer &&
              selectedViewCustomer.customer.email
                ? selectedViewCustomer.customer.email
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
            {{ $t('wizard.currency') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              selectedViewCustomer.customer &&
              selectedViewCustomer.customer.currency
                ? `${selectedViewCustomer.customer.currency.code} (${selectedViewCustomer.customer.currency.symbol})`
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
            {{ $t('customers.phone_number') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              selectedViewCustomer.customer &&
              selectedViewCustomer.customer.phone
                ? selectedViewCustomer.customer.phone
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
            {{ $t('customers.type_customer') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic" style="text-transform: capitalize;">
            {{
              selectedViewCustomer.customer &&
              selectedViewCustomer.customer.status_payment
                ? selectedViewCustomer.customer.status_payment
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
            {{ $t('customers.website') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              selectedViewCustomer.customer &&
              selectedViewCustomer.customer.website
                ? selectedViewCustomer.customer.website
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
            {{ $t('customers.security_pin') }}
          </p>
          <p class="text-sm font-bold leading-5 text-black non-italic">
            {{
              selectedViewCustomer.customer &&
              selectedViewCustomer.customer.security_pin
                ? selectedViewCustomer.customer.security_pin
                : ''
            }}
          </p>
        </div>
      </div> -->

      <!-- <p
        v-if="
          (getFormattedShippingAddress.length ||
            getFormattedBillingAddress.length) &&
          $route.name === 'customers.view'
        "
        class="mt-8 text-gray-500 uppercase sw-section-title"
      >
        {{ $t('customers.address') }}
      </p> -->

     <!-- <div
        v-if="$route.name === 'customers.view' || $route.name === 'customers.add-corepbx-services'"
        class="
          grid grid-cols-1
          gap-4
          md:grid-cols-2
          sm:grid-cols-1
          lg:grid-cols-2
        "
      >
        <div v-if="getFormattedBillingAddress.length" class="mt-5">
          <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800" >
            {{ $t('customers.billing_address') }}
          </p>
          <p
            class="text-sm font-bold leading-5 text-black non-italic"
            v-html="getFormattedBillingAddress"
          />
        </div>

         shipping address 
        <div v-if="getFormattedShippingAddress.length" class="mt-5">
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
            {{ $t('customers.shipping_address') }}
          </p>
          <p
            class="text-sm font-bold leading-5 text-black non-italic"
            v-html="getFormattedShippingAddress"
          />
        </div>
      </div>-->

      <!-- Custom Fields -->
      <!-- <p
        v-if="getCustomField.length > 0 && $route.name === 'customers.view'"
        class="mt-8 text-gray-500 uppercase sw-section-title"
      >
        {{ $t('settings.custom_fields.title') }}
      </p>

      <div
        v-if="$route.name === 'customers.view'"
        class="
          grid grid-cols-1
          gap-4
          mt-5
          lg:grid-cols-3
          md:grid-cols-2
          sm:grid-cols-1
        "
      >
        <div
          v-for="(field, index) in getCustomField"
          :key="index"
          :required="field.is_required ? true : false"
        >
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
            {{ field.custom_field.label }}
          </p>
          <p
            v-if="field.type === 'Switch'"
            class="text-sm font-bold leading-5 text-black non-italic"
          >
            <span v-if="field.defaultAnswer === 1"> Yes </span>
            <span v-else> No </span>
          </p>
          <p v-else class="text-sm font-bold leading-5 text-black non-italic">
            {{ field.defaultAnswer }}
          </p>
        </div>
      </div> -->

      <!------------ Sticky Notes ------------>

      <!-- <p
        v-if="getStickyNotes.length > 0 && $route.name === 'customers.view'"
        class="mt-8 text-gray-500 uppercase sw-section-title"
      >
        {{ $t('customers.sticky_notes') }}
      </p>

      <div
        v-if="$route.name === 'customers.view'"
        class="grid grid-cols-1 gap-3 mt-5"
      >
        <div v-for="(note, index) in getStickyNotes" :key="index" class="flex">
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
            <router-link
              :to="{
                path: `/admin/customers/${note.user_id}/${note.id}/edit-note`,
              }"
              class="font-medium text-primary-500"
            >
              {{ note.summary }}
            </router-link>
          </p>
          <div
            class="flex items-center justify-center w-6 h-5 mx-3 cursor-pointer"
          >
            <x-icon class="h-5 text-gray-600" @click="removeNote(note.id)" />
          </div>
        </div>
      </div> -->

      <!------------ Packages ------------>
<!--
      <div
        v-if="$route.name === 'customers.view'"
        class="tabs mb-5 grid col-span-12 border-t-2 border-solid pt-6"
        style="border-top-color: #f9fbff"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck1"
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
              for="chck1"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $t('customers.services') }}
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
                 icon by feathericons.com 
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
              <div class="text-grey-darkest">
                <sw-tabs :active-tab="activeTab" @update="setStatusFilter">
                  <sw-tab-item :title="$t('customers.active')" filter="A" />
                  <sw-tab-item :title="$t('customers.pending')" filter="P" />
                  <sw-tab-item :title="$t('customers.suspend')" filter="S" />
                  <sw-tab-item :title="$t('customers.cancelled')" filter="C" />
                </sw-tabs>
              </div>
              <sw-table-component
                ref="table"
                :show-filter="false"
                :data="fetchPackagesData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('services.service_number')"
                  show="code"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$tc('packages.package', 1)"
                  show="package.name"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.amount')"
                  sort-as="total"
                >
                  <template slot-scope="row">
                    <span>{{ $t('customers.amount') }}</span>
                    <div
                      v-html="$utils.formatMoney(row.total, row.user.currency)"
                    />
                  </template>
                </sw-table-column>

                <sw-table-column :sortable="true" :label="$t('customers.term')">
                  <template slot-scope="row">
                    <span>{{ $t('customers.term') }}</span>
                    <span>{{ capitalizeFirstLetter(row.term) }}</span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.activation_date')"
                  sort-as="activation_date"
                  show="formattedActivationDate"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.renewal_date')"
                  show="formattedRenewalDate"
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

                      <sw-dropdown-item
                        :to="{
                          name: 'invoices.create',
                          query: {
                            from: 'customer',
                            code: row.code,
                            customer_packages_id: row.id,
                            customer_id: row.customer_id,
                            package_id: row.package_id,
                          },
                        }"
                        tag-name="router-link"
                      >
                        <calculator-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('invoices.new_invoice') }}
                      </sw-dropdown-item>

                      <sw-dropdown-item
                        :to="`/admin/customers/${row.customer_id}/service/${row.id}/view`"
                        tag-name="router-link"
                      >
                        <cog-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.manage') }}
                      </sw-dropdown-item>
                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>
            
           
            </div>

           
          </div>
        </div>
      </div>-->

      <!------------ INVOICES ----------->

       <!--<div
        v-if="$route.name === 'customers.view'"
        class="tabs mb-5 grid col-span-12 pt-8"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck2"
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
              for="chck2"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $tc('invoices.invoice', 2) }}
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
                icon by feathericons.com 
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
              <div class="text-grey-darkest">
                <sw-tabs
                  :active-tab="activeInvoiceTab"
                  @update="setInvoiceStatusFilter"
                >
                  <sw-tab-item :title="$t('general.all')" filter="" />
                  <sw-tab-item :title="$t('general.due')" filter="DUE" />
                  <sw-tab-item :title="$t('general.draft')" filter="DRAFT" />
                  <sw-tab-item
                    :title="$t('general.pending')"
                    filter="PENDING"
                  />
                  <sw-tab-item
                    :title="$t('general.completed')"
                    filter="COMPLETED"
                  />
                </sw-tabs>
              </div>

              <sw-table-component
                ref="invoices_table"
                :show-filter="false"
                :data="fetchInvoicesData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('invoices.date')"
                  sort-as="invoice_date"
                  show="formattedInvoiceDate"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('invoices.number')"
                  show="invoice_number"
                >
                  <template slot-scope="row">
                    <span>{{ $t('invoices.number') }}</span>
                    <router-link
                      :to="{ path: `/admin/invoices/${row.id}/view` }"
                      class="font-medium text-primary-500"
                    >
                      {{ row.invoice_number }}
                    </router-link>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('invoices.status')"
                  sort-as="status"
                >
                  <template slot-scope="row">
                    <span> {{ $t('invoices.status') }}</span>

                    <sw-badge
                      :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
                      :color="$utils.getBadgeStatusColor(row.status).color"
                    >
                      {{ row.status.replace('_', ' ') }}
                    </sw-badge>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('invoices.paid_status')"
                  sort-as="paid_status"
                >
                  <template slot-scope="row">
                    <span>{{ $t('invoices.paid_status') }}</span>

                    <sw-badge
                      :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
                      :color="$utils.getBadgeStatusColor(row.status).color"
                    >
                      {{ row.paid_status.replace('_', ' ') }}
                    </sw-badge>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('invoices.amount_due')"
                  sort-as="due_amount"
                >
                  <template slot-scope="row">
                    <span>{{ $t('invoices.amount_due') }}</span>

                    <div
                      v-html="
                        $utils.formatMoney(row.due_amount, row.user.currency)
                      "
                    />
                  </template>
                </sw-table-column>

              <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown no-click"
        >
          <template slot-scope="row">
            <span>{{ $t('invoices.action') }}</span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                tag-name="router-link"
                :to="`invoices/${row.id}/edit`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                tag-name="router-link"
                :to="`invoices/${row.id}/view`"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="row.status == 'DRAFT'"
                @click="sendInvoice(row)"
              >
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.send_invoice') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="row.status === 'SENT' || row.status === 'VIEWED'"
                @click="sendInvoice(row)"
              >
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.resend_invoice') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="row.status == 'DRAFT'"
                @click="markInvoiceAsSent(row.id)"
              >
                <check-circle-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.mark_as_sent') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="
                  row.status === 'SENT' ||
                  row.status === 'VIEWED' ||
                  row.status === 'OVERDUE'
                "
                tag-name="router-link"
                :to="`/admin/payments/${row.id}/create`"
              >
                <credit-card-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('payments.record_payment') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="onCloneInvoice(row.id)">
                <document-duplicate-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.clone_invoice') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeInvoice(row.id)">
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.delete') }}
              </sw-dropdown-item>
            </sw-dropdown>
          </template>
        </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>-->

      <!------------ ESTIMATES ----------->
<!--
      <div
        v-if="$route.name === 'customers.view'"
        class="tabs mb-5 grid col-span-12 pt-6"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck3"
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
              for="chck3"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $tc('estimates.estimate', 2) }}
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
                 icon by feathericons.com 
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
              <div class="text-grey-darkest">
                <sw-tabs
                  :active-tab="activeEstimateTab"
                  @update="setEstimateStatusFilter"
                >
                  <sw-tab-item :title="$t('general.all')" filter="" />
                  <sw-tab-item :title="$t('general.draft')" filter="DRAFT" />
                  <sw-tab-item :title="$t('general.sent')" filter="SENT" />
                  <sw-tab-item
                    :title="$t('general.pending')"
                    filter="PENDING"
                  />
                </sw-tabs>
              </div>

              <sw-table-component
                ref="estimates_table"
                :show-filter="false"
                :data="fetchEstimatesData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('estimates.date')"
                  sort-as="estimate_date"
                  show="formattedEstimateDate"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$tc('estimates.estimate', 1)"
                  show="estimate_number"
                >
                  <template slot-scope="row">
                    <span>{{ $tc('estimates.estimate', 1) }}</span>
                    <router-link
                      :to="{ path: `/admin/estimates/${row.id}/view` }"
                      class="font-medium text-primary-500"
                    >
                      {{ row.estimate_number }}
                    </router-link>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('estimates.status')"
                  show="status"
                >
                  <template slot-scope="row">
                    <span> {{ $t('estimates.status') }}</span>
                    <sw-badge
                      :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
                      :color="$utils.getBadgeStatusColor(row.status).color"
                      class="px-3 py-1"
                    >
                      {{ row.status }}
                    </sw-badge>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('estimates.total')"
                  sort-as="total"
                >
                  <template slot-scope="row">
                    <span> {{ $t('estimates.total') }}</span>
                    <div
                      v-html="$utils.formatMoney(row.total, row.user.currency)"
                    />
                  </template>
                </sw-table-column>-->

               <!--  <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown no-click"
                >
                  <template slot-scope="row">
                    <span>{{ $t('general.actions') }}</span>
                    <router-link
                      :to="{ path: `/admin/estimates/${row.id}/edit` }"
                      class="font-medium text-primary-500"
                    >
                      {{ $t('general.edit') }}
                    </router-link>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown"
                >
                  <template slot-scope="row">
                    <span>{{ $t('payments.action') }}</span>
                    <sw-dropdown>
                      <dot-icon slot="activator" />
                      <sw-dropdown-item
                        tag-name="router-link"
                        :to="{ path: `/admin/estimates/${row.id}/edit` }"
                      >
                        <pencil-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.edit') }}
                      </sw-dropdown-item>

                      <sw-dropdown-item @click="removeEstimate(row.id)">
                        <trash-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.delete') }}
                      </sw-dropdown-item>

                      <sw-dropdown-item
                        tag-name="router-link"
                        :to="{ path: `/admin/estimates/${row.id}/view` }"
                      >
                        <eye-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.view') }}
                      </sw-dropdown-item>

                       <sw-dropdown-item @click="convertInToinvoice(row.id)">
                        <document-text-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('estimates.convert_to_invoice') }}
                      </sw-dropdown-item>
                      
                       <sw-dropdown-item
                        v-if="row.status !== 'SENT'"
                        @click="onMarkAsSent(row.id)"
                      >
                        <check-circle-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('estimates.mark_as_sent') }}
                      </sw-dropdown-item>
                      
                      <sw-dropdown-item
                        v-if="row.status !== 'SENT'"
                        @click="sendEstimate(row)"
                      >
                        <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('estimates.send_estimate') }} 
                      </sw-dropdown-item>  -->

                      <!-- resend estimte 
                        <sw-dropdown-item
                        v-if="row.status == 'SENT' || row.status == 'VIEWED'"
                        @click="sendEstimate(row)"
                      >
                        <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('estimates.resend_estimate') }}
                      </sw-dropdown-item>

                      
                      <sw-dropdown-item
                        v-if="row.status !== 'ACCEPTED'"
                        @click="onMarkAsAccepted(row.id)"
                      >
                        <check-circle-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('estimates.mark_as_accepted') }}
                      </sw-dropdown-item>
                      
                      <sw-dropdown-item
                        v-if="row.status !== 'REJECTED'"
                        @click="onMarkAsRejected(row.id)"
                      >
                        <x-circle-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('estimates.mark_as_rejected') }}
                      </sw-dropdown-item>
                      
                    </sw-dropdown>
                  </template>
                </sw-table-column>
                
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>-->

      <!------------ EXPENSES ----------->
 <!--
      <div
        v-if="$route.name === 'customers.view'"
        class="tabs mb-5 grid col-span-12 pt-6"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck5"
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
              for="chck5"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $tc('expenses.expense', 2) }}
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
                icon by feathericons.com 
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
              <div class="text-grey-darkest">
                <div class="flex base-tabs"></div>
              </div>

              <sw-table-component
                ref="expenses_table"
                :show-filter="false"
                :data="fetchExpensesData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('expenses.date')"
                  sort-as="expense_date"
                  show="formattedExpenseDate"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('expenses.expense_number')"
                  show="expense_number"
                >
                  <template slot-scope="row">
                    <span>{{ $t('expenses.expense_number') }}</span>
                    <router-link
                      :to="{ path: `/admin/expenses/${row.id}/edit` }"
                      class="font-medium text-primary-500"
                    >
                      {{ row.expense_number }}
                    </router-link>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$tc('expenses.categories.category', 1)"
                  sort-as="name"
                  show="category.name"
                >
                  <template slot-scope="row">
                    <span>{{ $tc('expenses.categories.category', 1) }}</span>
                    <span> {{ row.category.name }} </span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('expenses.provider')"
                  sort-as="provider_title"
                  show="provider_title"
                >
                  <template slot-scope="row">
                    <span>{{ $t('expenses.provider') }}</span>
                    <span>
                      {{
                        row.provider_title ? row.provider_title : 'Not selected'
                      }}
                    </span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('expenses.note')"
                  sort-as="expense_date"
                >
                  <template slot-scope="row">
                    <span>{{ $t('expenses.note') }}</span>
                    <div class="notes">
                      <div class="truncate note">{{ row.notes }}</div>
                    </div>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('expenses.amount')"
                  sort-as="amount"
                  show="category.amount"
                >
                  <template slot-scope="row">
                    <span>{{ $t('expenses.amount') }}</span>
                    <div
                      v-html="$utils.formatMoney(row.amount, row.user.currency)"
                    />
                  </template>
                </sw-table-column>
                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown no-click"
                >
                  <template slot-scope="row">
                    <span>{{ $t('expenses.action') }}</span>
                    <sw-dropdown>
                      <dot-icon slot="activator" />

                      <sw-dropdown-item
                        tag-name="router-link"
                        :to="{ path: `/admin/expenses/${row.id}/edit` }"
                      >
                        <pencil-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.edit') }}
                      </sw-dropdown-item>

                      <sw-dropdown-item @click="removeExpense(row.id)">
                        <trash-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.delete') }}
                      </sw-dropdown-item>
                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>
      -->
      <!------------ PAYMENTS ----------->
<!-- 
      <div
        v-if="$route.name === 'customers.view'"
        class="tabs mb-5 grid col-span-12 pt-6"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck6"
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
              for="chck6"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $tc('payments.payment', 2) }}
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
                icon by feathericons.com 
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
              <div class="text-grey-darkest">
                <div class="flex base-tabs"></div>
              </div>

              <sw-table-component
                ref="payments_table"
                :show-filter="false"
                :data="fetchPaymentsData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('payments.date')"
                  sort-as="payment_date"
                  show="formattedPaymentDate"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('payments.payment_number')"
                  show="payment_number"
                >
                  <template slot-scope="row">
                    <span>{{ $t('payments.payment_number') }}</span>
                    <router-link
                      :to="{ path: `/admin/payments/${row.id}/view` }"
                      class="font-medium text-primary-500"
                    >
                      {{ row.payment_number }}
                    </router-link>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('payments.payment_mode')"
                  show="payment_mode"
                >
                  <template slot-scope="row">
                    <span>{{ $t('payments.payment_mode') }}</span>
                    <span>
                      {{ row.payment_mode ? row.payment_mode : 'Not selected' }}
                    </span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('payments.invoice')"
                  sort-as="invoice_id"
                  show="invoice_number"
                >
                  <template slot-scope="row">
                    <span>{{ $t('invoices.invoice_number') }}</span>
                    <span>
                      {{
                        row.invoice_number ? row.invoice_number : 'No Invoice'
                      }}
                    </span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('payments.amount')"
                >
                  <template slot-scope="row">
                    <span>{{ $t('payments.amount') }}</span>
                    <div
                      v-html="$utils.formatMoney(row.amount, row.user.currency)"
                    />
                  </template>
                </sw-table-column>-->

                <!-- <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown no-click"
                >
                  <template slot-scope="row">
                    <span>{{ $t('general.actions') }}</span>
                    <router-link
                      :to="{ path: `/admin/payments/${row.id}/edit` }"
                      class="font-medium text-primary-500"
                    >
                      {{ $t('general.edit') }}
                    </router-link>
                  </template>
                </sw-table-column> 

                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown"
                >
                  <template slot-scope="row">
                    <span>{{ $t('payments.action') }}</span>
                    <sw-dropdown>
                      <dot-icon slot="activator" />

                      <sw-dropdown-item
                        tag-name="router-link"
                        :to="{ path: `/admin/payments/${row.id}/edit` }"
                      >
                        <pencil-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.edit') }}
                      </sw-dropdown-item>

                      <sw-dropdown-item
                        tag-name="router-link"
                        :to="{ path: `/admin/payments/${row.id}/view` }"
                      >
                        <eye-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.view') }}
                      </sw-dropdown-item>

                      <sw-dropdown-item @click="removePayment(row.id)">
                        <trash-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.delete') }}
                      </sw-dropdown-item>
                    </sw-dropdown>
                  </template>
                </sw-table-column>
                
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>-->

      <!------------ SERVICES PBX ------------>

      <!--<div
        v-if="$route.name === 'customers.view'"
        class="tabs mb-5 grid col-span-12 pt-6"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck4"
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
              for="chck4"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $t('customers.services_pbx') }}
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
                 icon by feathericons.com 
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
              <div class="text-grey-darkest">
                <sw-tabs
                  :active-tab="activeServicesPbxTab"
                  @update="setServicesPbxStatusFilter"
                >
                  <sw-tab-item :title="$t('customers.active')" filter="A" />
                  <sw-tab-item :title="$t('customers.pending')" filter="P" />
                  <sw-tab-item :title="$t('customers.suspend')" filter="S" />
                  <sw-tab-item :title="$t('customers.cancelled')" filter="C" />
                </sw-tabs>
              </div>

              <sw-table-component
                ref="services_pbx_table"
                :show-filter="false"
                :data="fetchPbxServicesData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('services.service_number')"
                  show="pbx_services_number"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$tc('packages.package', 1)"
                  show="pbx_package.pbx_package_name"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.amount')"
                  sort-as="total"
                >
                  <template slot-scope="row">
                    <span>{{ $t('customers.amount') }}</span>
                    <div
                      v-html="$utils.formatMoney(row.total, row.user.currency)"
                    />
                  </template>
                </sw-table-column>

                <sw-table-column :sortable="true" :label="$t('customers.term')">
                  <template slot-scope="row">
                    <span>{{ $t('customers.term') }}</span>
                    <span>{{ capitalizeFirstLetter(row.term) }}</span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.activation_date')"
                  sort-as="activation_date"
                  show="formattedActivationDate"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.renewal_date')"
                  show="formattedRenewalDate"
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

                      <sw-dropdown-item
                        :to="{
                          name: 'invoices.create',
                          query: {
                            from: 'pbx_services',
                            code: row.pbx_services_number,
                            pbx_service_id: row.id,
                            customer_id: row.customer_id,
                            package_id: row.pbx_package_id
                          },
                        }"
                        tag-name="router-link"
                      >
                        <calculator-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('invoices.new_invoice') }}
                      </sw-dropdown-item>-->

                      <!-- <sw-dropdown-item
                        :to="`/admin/invoices/create`"
                        tag-name="router-link"
                      >
                        <calculator-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('invoices.new_invoice') }}
                      </sw-dropdown-item> 

                      <sw-dropdown-item
                        :to="`/admin/customers/${row.customer_id}/pbx-service/${row.id}/view`"
                        tag-name="router-link"
                      >
                        <cog-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.manage') }}
                      </sw-dropdown-item>
                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>-->
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { CogIcon, XIcon } from '@vue-hero-icons/solid'
import { CalculatorIcon } from '@vue-hero-icons/outline'
import { DotsHorizontalIcon } from '@vue-hero-icons/outline'
import MoonWalkerIcon from '@/components/icon/MoonwalkerIcon'

import {
  PencilIcon,
  DocumentDuplicateIcon,
  CreditCardIcon,
  FilterIcon,
  ChevronDownIcon,
  EyeIcon,
  PlusIcon,
  DocumentTextIcon,
  PaperAirplaneIcon,
  CheckCircleIcon,
  TrashIcon,
  XCircleIcon,
  HashtagIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    CalculatorIcon,
    CogIcon,
    XIcon,
        MoonWalkerIcon,
    PlusIcon,
    FilterIcon,
    ChevronDownIcon,
    DotsHorizontalIcon,
    PencilIcon,
    DocumentDuplicateIcon,
    TrashIcon,
    CheckCircleIcon,
    PaperAirplaneIcon,
    DocumentTextIcon,
    XCircleIcon,
    EyeIcon,
    CreditCardIcon,
    HashtagIcon,
  },
  data() {
    return {
      notes: [],
      customer: null,
      customFields: [],
      activeTab: this.$t('customers.active'),
      activeInvoiceTab: this.$t('general.all'),
      activeEstimateTab: this.$t('general.all'),
      activeServicesPbxTab: this.$t('customers.active'),
      status: { name: 'Active', value: 'A' },
      invoice_status: { name: '', value: '' },
      estimate_status: { name: '', value: '' },
      services_pbx_status: { name: 'Active', value: 'A' },
      isRequestOngoing: false,
    }
  },
  computed: {
    ...mapGetters('customer', ['selectedViewCustomer']),

    /* getFormattedBillingAddress() {
      let billingAddress = ``

      if (!this.selectedViewCustomer.customer) {
        return billingAddress
      }

      if (!this.selectedViewCustomer.customer.billing_address) {
        return billingAddress
      }

      console.log(this.selectedViewCustomer.customer.billing_address);
      if (this.selectedViewCustomer.customer.billing_address.address_street_1) {
        billingAddress += `<span>${this.selectedViewCustomer.customer.billing_address.address_street_1},</span><br>`
      }
      if (this.selectedViewCustomer.customer.billing_address.address_street_2) {
        billingAddress += `<span>${this.selectedViewCustomer.customer.billing_address.address_street_2},</span><br>`
      }
      if (this.selectedViewCustomer.customer.billing_address.city) {
        billingAddress += `<span>${this.selectedViewCustomer.customer.billing_address.city},</span> `
      }
      if (this.selectedViewCustomer.customer.billing_address.state) {
        billingAddress += `<span>${this.selectedViewCustomer.customer.billing_address.state},</span>`
      }

      if (this.selectedViewCustomer.customer.billing_address.zip) {
        billingAddress += `<span>${this.selectedViewCustomer.customer.billing_address.zip}.</span><br>`
      }
     if (this.selectedViewCustomer.customer.billing_address.country) {
        billingAddress += `<span>${this.selectedViewCustomer.customer.billing_address.country.name}.</span> `
      }

      return billingAddress
    }, */

   /*  getFormattedShippingAddress() {
      let shippingAddress = ``

      if (!this.selectedViewCustomer.customer) {
        return shippingAddress
      }

      if (!this.selectedViewCustomer.customer.shipping_address) {
        return shippingAddress
      }

      if (
        this.selectedViewCustomer.customer.shipping_address.address_street_1
      ) {
        shippingAddress += `<span>${this.selectedViewCustomer.customer.shipping_address.address_street_1},</span><br>`
      }
      if (
        this.selectedViewCustomer.customer.shipping_address.address_street_2
      ) {
        shippingAddress += `<span>${this.selectedViewCustomer.customer.shipping_address.address_street_2},</span><br>`
      }
      if (this.selectedViewCustomer.customer.shipping_address.city) {
        shippingAddress += `<span>${this.selectedViewCustomer.customer.shipping_address.city},</span> `
      }
      if (this.selectedViewCustomer.customer.shipping_address.state) {
        shippingAddress += `<span>${this.selectedViewCustomer.customer.shipping_address.state},</span><br>`
      }
      if (this.selectedViewCustomer.customer.shipping_address.country) {
        shippingAddress += `<span>${this.selectedViewCustomer.customer.shipping_address.country.name}.</span> `
      }
      if (this.selectedViewCustomer.customer.shipping_address.zip) {
        shippingAddress += `<span>${this.selectedViewCustomer.customer.shipping_address.zip}.</span> `
      }
      return shippingAddress
    }, */

    /* getCustomField() {
      if (
        this.selectedViewCustomer.customer &&
        this.selectedViewCustomer.customer.fields
      ) {
        return this.selectedViewCustomer.customer.fields
      }
      return []
    }, */

    isServiceView() {
      if (this.$route.name ==='customer.package-view') {
          return true
      }
      return false
    },

    /* getStickyNotes() {
      if (this.notes) {
        let notes = this.notes
        return notes
          .filter((note) => note.stiky === 1)
          .sort((a, b) => b.id - a.id)
          .slice(0, 10)
      }
      return []
    }, */
  },
  /* watch: { */
    /* $route(to, from) {
      this.customer = this.selectedViewCustomer
      this.refreshTable()
    }, */
    /* selectedViewCustomer(newVal) {
      this.notes = newVal.customer.notes.map((note) => {
        return {
          id: note.id,
          summary: note.summary,
          stiky: note.stiky,
          user_id: note.user_id,
        }
      })
    }, */
  /* }, */
  /* created() {
    if (this.$route.name === 'customers.view') {
      this.notes = this.selectedViewCustomer.customer.notes.map((note) => {
        return {
          id: note.id,
          summary: note.summary,
          stiky: note.stiky,
          user_id: note.user_id,
        }
      })
    }
  }, */
  methods: {
    ...mapActions('customer', [
      'fetchPackages',
      'fetchInvoices',
      'fetchEstimates',
      'fetchPbxServices',
    ]),
    ...mapActions('customerNote', ['deleteCustomerNote']),
    ...mapActions('expense', ['fetchExpenses','deleteExpense']),
    ...mapActions('payment', ['fetchPayments','deletePayment']),
     ...mapActions('modal', ['openModal']),
       ...mapActions('invoice', [
     
      'getRecord',
      'selectInvoice',
      'resetSelectedInvoices',
      'selectAllInvoices',
      'deleteInvoice',
      'deleteMultipleInvoices',
      'sendEmail',
      'markAsSent',
      'setSelectAllState',
      'cloneInvoice',
    ]),

    ...mapActions('estimate', [
      'fetchEstimates',
      'resetSelectedEstimates',
      'getRecord',
      'selectEstimate',
      'selectAllEstimates',
      'deleteEstimate',
      'deleteMultipleEstimates',
      'markAsSent',
      'convertToInvoice',
      'setSelectAllState',
      'markAsAccepted',
      'markAsRejected',
      'sendEmail',
    ]),

    /* async fetchPackagesData({ page, filter, sort }) {
      let data = {
        customer_id: this.$route.params.id,
        status: this.status.value,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      this.isRequestOngoing = true
      let response = await this.fetchPackages(data)
      this.isRequestOngoing = false

      let list = response.data.packagesList.data.map((pack) => {
        let discount_type = ''

        switch (pack.discount_type) {
          case 'N':
            discount_type = 'None'
            break
          case 'G':
            discount_type = 'General'
            break
          case 'I':
            discount_type = 'By item'
            break
        }

        return {
          ...pack,
          discount_type_name: discount_type,
        }
      })

      return {
        data: list,
        pagination: {
          totalPages: response.data.packagesList.last_page,
          currentPage: page,
          count: response.data.packagesList.count,
        },
      }
    }, */

    /* async fetchInvoicesData({ page, filter, sort }) {
      let data = {
        customer_id: this.$route.params.id,
        status: this.invoice_status.value,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchInvoices(data)

      let list = response.data.invoicesList.data.map((invoice) => {
        return {
          ...invoice,
        }
      })

      return {
        data: list,
        pagination: {
          totalPages: response.data.invoicesList.last_page,
          currentPage: page,
          count: response.data.invoicesList.count,
        },
      }
    }, */

    /* async fetchEstimatesData({ page, filter, sort }) {
      let data = {
        customer_id: this.$route.params.id,
        status: this.estimate_status.value,
        from_date: '',
        to_date: '',
        estimate_number: '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
     

      let response = await this.fetchEstimates(data)
    
      let list = response.data.estimates.data.map((estimate) => {
        return {
          ...estimate,
        }
      })

      return {
        data: list,
        pagination: {
          totalPages: response.data.estimates.last_page,
          currentPage: page,
          count: response.data.estimates.count,
        },
      }
    }, */

     /* async markInvoiceAsSent(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.invoice_mark_as_sent'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          const data = {
            id: id,
            status: 'SENT',
          }
          let response = await this.markAsSent(data)
          this.refreshTable()
          if (response.data) {
            window.toastr['success'](
              this.$tc('invoices.mark_as_sent_successfully')
            )
          }
        }
      })
    }, */

    /* async fetchExpensesData({ page, filter, sort }) {
      let data = {
        user_id: this.$route.params.id,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      let response = await this.fetchExpenses(data)
      return {
        data: response.data.expenses.data,
        pagination: {
          totalPages: response.data.expenses.last_page,
          currentPage: page,
          count: response.data.expenses.count,
        },
      }
    }, */

    /* async fetchPaymentsData({ page, filter, sort }) {
      let data = {
        customer_id: this.$route.params.id,
        status: this.estimate_status.value,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      let response = await this.fetchPayments(data)
      return {
        data: response.data.payments.data,
        pagination: {
          totalPages: response.data.payments.last_page,
          currentPage: page,
          count: response.data.payments.count,
        },
      }
    }, */

    /* async fetchPbxServicesData({ page, filter, sort }) {
      let data = {
        customer_id: this.$route.params.id,
        status: this.services_pbx_status.value,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      let response = await this.fetchPbxServices(data)

      return {
        data: response.data.pbxServices.data,
        pagination: {
          totalPages: response.data.pbxServices.last_page,
          currentPage: page,
          count: response.data.pbxServices.count,
        },
      }
    }, */

    /* async onCloneInvoice(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.confirm_clone'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.cloneInvoice({ id })

          this.refreshTable()

          if (response.data) {
            window.toastr['success'](this.$tc('invoices.cloned_successfully'))
            this.$router.push(
              `/admin/invoices/${response.data.invoice.id}/edit`
            )
          }
        }
      })
    }, */


    /* async removeInvoice(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let res = await this.deleteInvoice({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('invoices.deleted_message'))
            this.$refs.table.refresh()
            return true
          }

          if (res.data.error === 'payment_attached') {
            window.toastr['error'](
              this.$t('invoices.payment_attached_message'),
              this.$t('general.action_failed')
            )
            return true
          }

          window.toastr['error'](res.data.error)
          return true
        }
        this.resetSelectedInvoices()
      })
    }, */

    

    /* async removeExpense(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('expenses.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteExpense({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('expenses.deleted_message', 1))
            // this.refreshTableExpense()
            this.$refs.expenses_table.refresh()
            return true
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    }, */

    /* async removePayment(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('payments.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deletePayment({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('payments.deleted_message', 1))
            this.$refs.payments_table.refresh()
            return true
          }
          window.toastr['error'](res.data.message)
          return true
        }
      })
    }, */

     /* async removeEstimate(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('estimates.confirm_delete', 1),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteEstimate({ ids: [this.id] })

          if (res.data.success) {
            this.$refs.estimates_table.refresh()
            this.resetSelectedEstimates()
            window.toastr['success'](this.$tc('estimates.deleted_message', 1))
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    }, */

    /* async convertInToinvoice(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_conversion'),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willConvertInToinvoice) => {
        if (willConvertInToinvoice) {
          let res = await this.convertToInvoice(id)

          if (res.data) {
            window.toastr['success'](this.$t('estimates.conversion_message'))
             this.$router.replace(`/admin/invoices/${res.data.invoice.id}/edit`)
            // this.$router.push(`invoices/${res.data.invoice.id}/edit`)
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    }, */

    /* async onMarkAsSent(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_sent'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willMarkAsSent) => {
        if (willMarkAsSent) {
          const data = {
            id: id,
            status: 'SENT',
          }

          let response = await this.markAsSent(data)
          this.$refs.estimates_table.refresh()

          if (response.data) {
            window.toastr['success'](
              this.$tc('estimates.mark_as_sent_successfully')
            )
          }
        }
      })
    }, */

    /* async sendEstimate(estimate) {
      this.openModal({
        title: this.$t('estimates.send_estimate'),
        componentName: 'SendEstimateModal',
        id: estimate.id,
        data: estimate,
        variant: 'lg',
      })
    }, */

    /* async onMarkAsAccepted(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_accepted'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (markedAsRejected) => {
        if (markedAsRejected) {
          const data = {
            id: id,
            status: 'ACCEPTED',
          }

          let response = await this.markAsAccepted(data)

          if (response.data) {
            this.$refs.estimates_table.refresh()
            window.toastr['success'](
              this.$tc('estimates.marked_as_accepted_message')
            )
          }
        }
      })
    }, */

    /* async onMarkAsRejected(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_rejected'),
        icon: '/assets/icon/times-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (markedAsRejected) => {
        if (markedAsRejected) {
          const data = {
            id: id,
            status: 'REJECTED',
          }

          let response = await this.markAsRejected(data)

          if (response.data) {
            this.$refs.estimates_table.refresh()
            window.toastr['success'](
              this.$tc('estimates.marked_as_rejected_message')
            )
          }
        }
      })
    }, */

    /* setStatusFilter(val) {
      if (this.activeTab === val.title) {
        return true
      }
      this.activeTab = val.title
      switch (val.title) {
        case this.$t('customers.active'):
          this.status = {
            name: 'Active',
            value: 'A',
          }
          break

        case this.$t('customers.pending'):
          this.status = {
            name: 'Pending',
            value: 'P',
          }
          break

        case this.$t('customers.suspend'):
          this.status = {
            name: 'Suspend',
            value: 'S',
          }
          break

        case this.$t('customers.cancelled'):
          this.status = {
            name: 'Cancelled',
            value: 'C',
          }
          break
      }

      this.refreshTable()
    }, */

    /* setInvoiceStatusFilter(val) {
      if (this.activeInvoiceTab === val.title) {
        return true
      }
      this.activeInvoiceTab = val.title
      switch (val.title) {
        case this.$t('general.due'):
          this.invoice_status = {
            name: 'DUE',
            value: 'DUE',
          }
          break

        case this.$t('general.draft'):
          this.invoice_status = {
            name: 'DRAFT',
            value: 'DRAFT',
          }
          break

        case this.$t('general.pending'):
          this.invoice_status = {
            name: 'PENDING',
            value: 'PENDING',
          }
          break

        case this.$t('general.completed'):
          this.invoice_status = {
            name: 'COMPLETED',
            value: 'COMPLETED',
          }
          break

        default:
          this.invoice_status = {
            name: '',
            value: '',
          }
          break
      }
      this.$refs.invoices_table.refresh()
    }, */

    /* setEstimateStatusFilter(val) {
      if (this.activeEstimateTab === val.title) {
        return true
      }
      this.activeEstimateTab = val.title
      switch (val.title) {
        case this.$t('general.draft'):
          this.estimate_status = {
            name: 'DRAFT',
            value: 'DRAFT',
          }
          break

        case this.$t('general.sent'):
          this.estimate_status = {
            name: 'SENT',
            value: 'SENT',
          }
          break

        case this.$t('general.pending'):
          this.estimate_status = {
            name: 'PENDING',
            value: 'PENDING',
          }
          break

        default:
          this.estimate_status = {
            name: '',
            value: '',
          }
          break
      }
      this.$refs.estimates_table.refresh()
    }, */

    /* refreshTable() {
      this.$refs.table.refresh()
    }, */

    /* capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1)
    }, */

    /* async sendInvoice(invoice) {
      this.openModal({
        title: this.$t('invoices.send_invoice'),
        componentName: 'SendInvoiceModal',
        id: invoice.id,
        data: invoice,
        variant: 'lg',
      })
    }, */

   /*  async removeNote(id) {
      let res = await this.deleteCustomerNote({ ids: [id] })

      if (res.data.success) {
        let index = this.notes.findIndex((note) => note.id === id)
        this.notes.splice(index, 1)
        window.toastr['success'](this.$tc('customer_notes.deleted_message', 1))
        return true
      }

      window.toastr['error'](res.data.message)
      return true
    }, */

    /* setServicesPbxStatusFilter(val) {
      if (this.activeServicesPbxTab === val.title) {
        return true
      }
      this.activeServicesPbxTab = val.title
      switch (val.title) {
        case this.$t('customers.active'):
          this.services_pbx_status = {
            name: 'Active',
            value: 'A',
          }
          break

        case this.$t('customers.pending'):
          this.services_pbx_status = {
            name: 'Pending',
            value: 'P',
          }
          break

        case this.$t('customers.suspend'):
          this.services_pbx_status = {
            name: 'Suspend',
            value: 'S',
          }
          break

        case this.$t('customers.cancelled'):
          this.services_pbx_status = {
            name: 'Cancelled',
            value: 'C',
          }
          break
      }

      this.$refs.services_pbx_table.refresh()
    }, */
  },
}
</script>

<style lang="scss">
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
  max-height: 400vh;
}
</style>
