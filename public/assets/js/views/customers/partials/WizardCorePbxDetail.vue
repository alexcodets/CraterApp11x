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
        {{ $t('customers.pbxservices_details') }}
      </p>
    </div>
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <br />
    <br />

    <!-- header resume -->
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
          class="mb-1text-sm font-normal leading-5 non-italic text-primary-800"
        >
          {{ $t('services.name') }}
        </p>
        <p class="text-sm font-bold leading-5 text-black non-italic">
          {{ this.parameters ? this.parameters.package.pbx_package_name : '' }}
        </p>
      </div>

      <div>
        <p
          class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
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
          class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
        >
          {{ $t('invoices.discount') }}
        </p>
        <p class="text-sm font-bold leading-5 text-black non-italic">
          {{ this.parameters.allow_discount ? 'Yes' : 'None' }}
        </p>
      </div>

      <div>
        <p
          class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
        >
          {{ $t('customers.date_act') }}
        </p>
        <p class="text-sm font-bold leading-5 text-black non-italic">
          {{
            this.parameters.date_begin.length > 0
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
          class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
        >
          {{ $t('customers.server') }}
        </p>
        <p class="text-sm font-bold leading-5 text-black non-italic">
          {{
            this.parameters.package.server
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
          {{
            this.parameters.package.status_payment.length > 0
              ? this.parameters.package.status_payment
              : 'None'
          }}
        </p>
      </div>
    </div>
    <br />

    <!-- Extension -->
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
      v-if="this.parameters.package.extensions"
    >
      <p>
        {{ $t('customers.pbxservices_extensions') }}
      </p>
      <div
        class="
          relative
          flex
          items-center
          justify-between
          h-10
          mt-4
          list-none
          border-b-2 border-gray-200 border-solid
        "
      >
        <sw-transition>
          <sw-dropdown
            v-if="selectFieldExtIncluded.length > 0"
            style="z-index: 99"
          >
            <span
              slot="activator"
              class="
                flex
                block
                text-sm
                font-medium
                cursor-pointer
                select-none
                text-primary-400
                left:
                4em;
              "
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>
            <sw-dropdown-item @click="excludeExtensions()">
              <!--  <pencil-icon class="h-5 mr-3 text-gray-600" /> -->
              {{ $t('general.exclude') }}
            </sw-dropdown-item>
            <!-- clear button -->
            <!-- <sw-dropdown-item @click="OpenDID">
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.clear') }}
            </sw-dropdown-item>
 -->
          </sw-dropdown>
        </sw-transition>
      </div>

      <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12">
        <sw-checkbox
          v-model="SelectAllFieldExtIncluded"
          variant="primary"
          :label="$t('general.select_all')"
          class="md:hidden"
        />
        <sw-checkbox
          v-model="SelectAllFieldExtIncluded"
          variant="primary"
          class="hidden md:inline"
        />
      </div>
      <!-- include extensions -->
      <sw-table-component
        ref="tablesExt"
        :data="dataExtensionsIncludedMetho"
        :show-filter="false"
        table-class="tablesDidsExt"
      >
        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="no-click"
        >
          <div slot-scope="row" class="custom-control custom-checkbox flex">
            <sw-checkbox
              :id="row.ext"
              v-model="selectFieldExtIncluded"
              :value="row.ext"
              variant="primary"
            />
            <button
              type="button"
              @click="excludeExtensions(row.ext)"
              class="
                ml-1
                text-sm text-gray-600
                bg-transparent
                border border-danger-200
                rounded
                p-1
                leading-none
                font-medium
                hover:text-gray-900
                focus:outline-none
                focus:border-danger-300
                focus:shadow-outline-danger
                active:bg-gray-50
                transition
                duration-150
              "
            >
              <minus-icon class="text-danger h-5 w-5" />
            </button>
          </div>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.extensions.extension')"
          show="ext"
          sortBy="sortBy"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.extensions.extension') }}</span>

            <span :class="{ only_api: row.only_api }">
              {{ row.ext ? row.ext : 'Not selected' }}
            </span>
            <div v-if="row.date_prorate !== null">
              <p class="whitespace-nowrap">
                <b>Prorate Date: </b> {{ row.date_prorate | formatDate }}
              </p>
              <p class="whitespace-nowrap">
                <b>Prorate Price: </b>
                <span v-html="$utils.formatMoney(row.prorate, currency)" />
              </p>
            </div>
            <br />
            <small v-if="row.only_api"> (New from server) </small>
            <small v-else-if="row.db_available"> (New from database) </small>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.extensions.email')"
          show="email"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.extensions.email') }}</span>

            <span :class="{ only_api: row.only_api }">
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

            <span :class="{ only_api: row.only_api }">
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

            <span :class="{ only_api: row.only_api }">
              {{ row.ua_fullname ? row.ua_fullname : 'Not selected' }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.extensions.macaddress')"
          show="macaddress"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.extensions.macaddress') }}</span>

            <span :class="{ only_api: row.only_api }">
              {{ row.macaddress ? row.macaddress : 'Not selected' }}
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

            <span :class="{ only_api: row.only_api }">
              {{ row.status ? row.status : 'Not selected' }}
            </span>
          </template>
        </sw-table-column>

        <!-- switch -->
        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.extensions.prorate')"
          show="id"
          v-if="isEdit"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.extensions.prorate') }}</span>
            <!-- <sw-switch style="top: 2px" @change="prorateSwitchChange(row.id)" /> -->
            <sw-switch
              :disabled="row.invoice_prorate == 1 || formData.term == 'daily'"
              style="top: 2px"
              v-model="row.statusProrate"
              @change="switchExtProrate(row)"
            />
          </template>
        </sw-table-column>

        <!-- calendar -->
        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.extensions.prorate_date')"
          show=""
          v-if="isEdit && formData.term !== 'daily'"
        >
          <template slot-scope="row" v-if="row.statusProrate">
            <span>{{ $t('corePbx.extensions.prorate_date') }}</span>
            <sw-date-picker
              ref="ProrateDatepicker"
              v-model="row.date_prorate"
              :config="{
                altInput: true,
                altFormat: 'd/m/Y',
                enableTime: false,
                time_24hr: false,
              }"
              :disabled="row.invoice_prorate == 1"
              :invalid="false"
              @input="switchExtProrate(row)"
            />
          </template>
        </sw-table-column>
      </sw-table-component>

      <br /><br /><br />

      <p>
        {{ $t('customers.pbxservices_extensions_excluded') }}
      </p>
      <div
        class="
          relative
          flex
          items-center
          justify-between
          h-10
          mt-5
          list-none
          border-b-2 border-gray-200 border-solid
        "
      >
        <sw-transition>
          <sw-dropdown
            v-if="selectFieldExtExcluded.length > 0"
            style="z-index: 99"
          >
            <span
              slot="activator"
              class="
                flex
                block
                text-sm
                font-medium
                cursor-pointer
                select-none
                text-primary-400
                left:
                4em;
              "
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>
            <sw-dropdown-item @click="includeExtensions()">
              <!-- <pencil-icon class="h-5 mr-3 text-gray-600" /> -->
              {{ $t('general.include') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12">
        <sw-checkbox
          v-model="SelectAllFieldExtExcluded"
          variant="primary"
          size="sm"
          :label="$t('general.select_all')"
          class="md:hidden"
        />
        <sw-checkbox
          v-model="SelectAllFieldExtExcluded"
          variant="primary"
          class="hidden md:inline"
        />
      </div>

      <!-- excluded extensions -->
      <sw-table-component
        ref="table"
        :data="dataExtensionsExcluded"
        :show-filter="false"
        table-class="table"
      >
        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="no-click"
        >
          <div slot-scope="row" class="custom-control custom-checkbox flex">
            <sw-checkbox
              :id="row.ext"
              v-model="selectFieldExtExcluded"
              :value="row.ext"
              variant="primary"
            />
            <button
              type="button"
              @click="includeExtensions(row.ext)"
              class="
                ml-1
                text-sm text-gray-600
                bg-transparent
                border border-success-200
                rounded
                p-1
                leading-none
                font-medium
                hover:text-gray-900
                focus:outline-none
                focus:border-success-300
                focus:shadow-outline-success
                active:bg-gray-50
                transition
                duration-150
              "
            >
              <plus-icon class="text-success h-5 w-5" />
            </button>
          </div>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.extensions.extension')"
          show="ext"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.extensions.extension') }}</span>

            <span>
              {{ row.ext ? row.ext : 'Not selected' }}
            </span>
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
          :label="$t('corePbx.extensions.macaddress')"
          show="macaddress"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.extensions.macaddress') }}</span>

            <span>
              {{ row.macaddress ? row.macaddress : 'Not selected' }}
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

    <!-- DID -->
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
      v-if="parameters.package.did"
    >
      <p>{{ $t('customers.pbxservices_did') }}</p>
      <div
        class="
          relative
          flex
          items-center
          justify-between
          h-10
          mt-5
          list-none
          border-b-2 border-gray-200 border-solid
        "
      >
        <sw-transition>
          <sw-dropdown
            v-if="
              selectFieldDidIncluded.length > 0 && dataDidIncluded.length > 0
            "
            style="z-index: 99"
          >
            <span
              slot="activator"
              class="
                flex
                block
                text-sm
                font-medium
                cursor-pointer
                select-none
                text-primary-400
              "
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item @click="excludeDid()">
              <!-- <pencil-icon class="h-5 mr-3 text-gray-600" /> -->
              {{ $t('general.exclude') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12">
        <sw-checkbox
          v-model="SelectAllFieldDidIncluded"
          variant="primary"
          class="hidden md:inline"
          @change="selectAllDID"
        />

        <sw-checkbox
          v-model="SelectAllFieldDidIncluded"
          :label="$t('general.select_all')"
          variant="primary"
          class="md:hidden"
          @change="selectAllDID"
        />
      </div>
      <!-- include did -->
      <sw-table-component
        ref="tablesDids"
        :data="dataDidIncludedMetho"
        :show-filter="false"
        table-class="table"
      >
        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="no-click"
        >
          <div slot-scope="row" class="custom-control custom-checkbox flex">
            <sw-checkbox
              :id="row.number"
              v-model="selectFieldDidIncluded"
              :value="row.number"
              variant="primary"
            />
            <!-- button small -->
            <button
              type="button"
              @click="excludeDid(row.number)"
              class="
                ml-1
                text-sm text-gray-600
                bg-transparent
                border border-danger-200
                rounded
                p-1
                leading-none
                font-medium
                hover:text-gray-900
                focus:outline-none
                focus:border-danger-300
                focus:shadow-outline-danger
                active:bg-gray-50
                transition
                duration-150
              "
            >
              <minus-icon class="text-danger h-5 w-5" />
            </button>
          </div>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.did.did_channel')"
          show="number"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.did.did_channel') }}</span>

            <span :class="{ only_api: row.only_api }">
              {{ row.number ? row.number : 'Not selected' }}
            </span>
            <div v-if="row.date_prorate !== null">
              <p class="whitespace-nowrap">
                <b>Prorate Date: </b> {{ row.date_prorate | formatDate }}
              </p>
              <p class="whitespace-nowrap">
                <b>Prorate Price: </b>
                <span v-html="$utils.formatMoney(row.prorate, currency)" />
              </p>
            </div>
            <br />
            <small v-if="row.only_api"> (New from server) </small>
            <small v-else-if="row.db_available"> (New from database) </small>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.did.destination')"
          show="ext"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.did.destination') }}</span>

            <span :class="{ only_api: row.only_api }">
              {{ row.ext == null ? 'Ext Not Found' : row.ext }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.did.type')"
          show="type"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.did.type') }}</span>
            <span :class="{ only_api: row.only_api }">
              {{ row.type == null ? 'No Type' : row.type }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.did.status')"
          show="status"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.did.status') }}</span>
            <span :class="{ only_api: row.only_api }">
              {{ row.status == null ? 'Not Selected' : row.status }}
            </span>
          </template>
        </sw-table-column>

        <!-- switch -->
        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.extensions.prorate')"
          show="id"
          v-if="isEdit && formData.term !== 'daily'"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.extensions.prorate') }}</span>
            <sw-switch
              :disabled="row.invoice_prorate == 1 || formData.term == 'daily'"
              style="top: 2px"
              v-model="row.statusProrate"
              @change="prorateDidSwitchChange(row)"
            />
          </template>
        </sw-table-column>

        <!-- calendar -->
        <sw-table-column
          
          :sortable="true"
          :label="$t('corePbx.extensions.prorate_date')"
          show=""
          v-if="isEdit && formData.term !== 'daily'"
        >
          <template slot-scope="row" v-if="row.statusProrate">
            <span>{{ $t('corePbx.extensions.prorate_date') }}</span>
            <sw-date-picker
              ref="ProrateDatepicker"
              v-model="row.date_prorate"
              :config="{
                altInput: true,
                altFormat: 'd/m/Y',
                enableTime: false,
                time_24hr: false,
              }"
              :disabled="row.invoice_prorate == 1"
              :invalid="false"
              @input="prorateDidSwitchChange(row)"
            />
          </template>
        </sw-table-column>

        <!-- <sw-table-column
          :sortable="true"
          :label="$t('corePbx.extensions.prorate')"
          v-if="isEdit"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.extensions.prorate') }}</span>
            <sw-switch
              style="top: 2px"
              @change="prorateDidSwitchChange(row.number)"
            />
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.extensions.prorate_date')"
          v-if="isEdit"
        >
          <template slot-scope="row" v-if="viewDateRateDid">
            <span>{{ $t('corePbx.extensions.prorate_date') }}</span>
            <base-date-picker
              v-model="modelDid[row.number].value"
              :calendar-button="true"
              calendar-button-icon="calendar"
              :disabled="modelDid[row.number].disabled"
            />
          </template>
        </sw-table-column> -->
      </sw-table-component>

      <br /><br /><br />
      <p>{{ $t('customers.pbxservices_did_excluded') }}</p>

      <div
        class="
          relative
          flex
          items-center
          justify-between
          h-10
          mt-5
          list-none
          border-b-2 border-gray-200 border-solid
        "
      >
        <sw-transition>
          <sw-dropdown
            v-if="
              selectFieldDidExcluded.length > 0 && dataDidExcluded.length > 0
            "
            style="z-index: 99"
          >
            <span
              slot="activator"
              class="
                flex
                block
                text-sm
                font-medium
                cursor-pointer
                select-none
                text-primary-400
              "
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item @click="includeDid()">
              <!-- <pencil-icon class="h-5 mr-3 text-gray-600" /> -->
              {{ $t('general.include') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12">
        <sw-checkbox
          v-model="SelectAllFieldDidExcluded"
          variant="primary"
          class="hidden md:inline"
        />

        <sw-checkbox
          v-model="SelectAllFieldDidExcluded"
          :label="$t('general.select_all')"
          variant="primary"
          class="md:hidden"
        />
      </div>

      <!-- exclude did -->
      <sw-table-component
        ref="table"
        :data="dataDidExcluded"
        :show-filter="false"
        table-class="table"
      >
        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="no-click"
        >
          <div slot-scope="row" class="custom-control custom-checkbox flex">
            <sw-checkbox
              :id="row.number"
              v-model="selectFieldDidExcluded"
              :value="row.number"
              variant="primary"
            />
            <!-- button small -->
            <button
              type="button"
              @click="includeDid(row.number)"
              class="
                ml-1
                text-sm text-gray-600
                bg-transparent
                border border-success-200
                rounded
                p-1
                leading-none
                font-medium
                hover:text-gray-900
                focus:outline-none
                focus:border-success-300
                focus:shadow-outline-success
                active:bg-gray-50
                transition
                duration-150
              "
            >
              <plus-icon class="text-success h-5 w-5" />
            </button>
          </div>
        </sw-table-column>

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
          :label="$t('corePbx.did.status')"
          show="status"
        >
          <template slot-scope="row">
            {{ row.status == null ? 'Not Selected' : row.status }}
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>

    <!-- Items -->
    <div class="grid grid-cols-5 gap-4 mb-8">
      <sw-divider class="col-span-12" />

      <h6 class="col-span-5 sw-section-title lg:col-span-1">
        {{ $t('packages.packages_items') }}
      </h6>

      <div class="grid col-span-12 gap-y-6 gap-x-4 md:grid-cols-6">
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
                      m-2
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
                :isNoGeneralTaxes="isNoGeneralTaxes"
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

    <div
      class="block my-10 invoice-foot lg:justify-between lg:flex lg:items-start"
    >
      <div class="w-full lg:w-1/2"></div>

      <div
        v-if="parameters.package.call_ratings"
        class="
          px-5
          py-5
          bg-white
          border border-gray-200 border-solid
          rounded
          invoice-total
          lg:mt-0
        "
      >
        <!-------------- TAXES CDR ------------->
        <p>{{ $t('corePbx.tax_for_cdrs') }}</p>
        <br />

        <div class="flex items-center justify-between w-full">
          <div
            v-if="packageCustomer.taxCdr.name !== 'none'"
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
            >
              {{ packageCustomer.taxCdr.name }} -
              {{ packageCustomer.taxCdr.percent }}%
            </label>

            <trash-icon class="h-5 ml-2" @click="removeTaxCdr()" />
          </div>
          <sw-popup
            v-if="packageCustomer.taxCdr.name == 'none'"
            ref="taxModalForCdrs"
            class="text-sm font-semibold leading-5 text-primary-400"
          >
            <div slot="activator" class="float-right">
              + {{ $t('invoices.add_tax') }}
            </div>
            <tax-select-popup
              :taxes="taxesList"
              @select="selectTaxCDR"
              emitName="tax_for_cdrs"
            />
          </sw-popup>
        </div>
      </div>
    </div>

    <!------------------------ TOTALS -------------------------->
    <div
      class="block my-10 invoice-foot lg:justify-between lg:flex lg:items-start"
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
          invoice-total
          lg:mt-0
        "
      >
        <!------------- PRORATE SWITCH ----------->
        <div
          class="flex items-center justify-between w-full mt-2"
          v-if="isEdit"
        >
          <label
            class="text-sm font-semibold leading-5 text-gray-500 uppercase"
            >{{ $t('customers.generate_prorate_invoice') }}</label
          >
          <div
            class="flex items-center justify-center m-0 text-lg text-black"
            role="group"
          >
            <sw-switch
              style="position: relative; top: -12px"
              v-model="formData.invoice_prorate"
            />
          </div>
        </div>

        <!------------- PRICE ----------->
        <div class="flex items-center justify-between w-full mt-2">
          <label
            class="text-sm font-semibold leading-5 text-gray-500 uppercase"
            >{{ $t('customers.price') }}</label
          >
          <div
            class="flex items-center justify-center m-0 text-lg text-black"
            role="group"
          >
            <sw-input
              v-model="price"
              class="rounded-tr-sm rounded-br-sm"
              type="number"
            />
          </div>
        </div>

        <!------------- CAP BY EXTENSION ----------->
        <div class="flex items-center justify-between w-full mt-2">
          <label
            class="text-sm font-semibold leading-5 text-gray-500 uppercase"
            >{{ $t('customers.cap_by_extension') }}</label
          >
          <div
            class="flex items-center justify-center m-0 text-lg text-black"
            role="group"
          >
            <sw-input
              v-model="capByExtension"
              class="rounded-tr-sm rounded-br-sm"
              type="number"
            />
          </div>
        </div>
        <br />
        <!------------- CAP TOTAL ----------->
        <!-- <div class="flex items-center justify-between w-full mt-2">
          <label
            class="text-sm font-semibold leading-5 text-gray-500 uppercase"
            >{{ $t('customers.cap_total') }}</label
          >
          <div
            class="flex items-center justify-center m-0 text-lg text-black"
            role="group"
          >
            <sw-input
              v-model="capTotal"
              class="rounded-tr-sm rounded-br-sm"
              type="number"
            />
          </div>
        </div> -->
        <!------------- DISCOUNT ----------->
        <div
          v-if="
            (discountPerItem === 'NO' || discountPerItem === null) &&
            parameters.allow_discount
          "
          class="flex items-center justify-between w-full mt-2"
        >
          <label
            class="text-sm font-semibold leading-5 text-gray-500 uppercase"
            >{{ $t('invoices.discount') }}</label
          >
          <div
            class="flex items-center justify-center m-0 text-lg text-black"
            role="group"
          >
            <sw-input
              v-model="discountService"
              :invalid="$v.packageCustomer.discount_val.$error"
              class="border-r-0 rounded-tr-sm rounded-br-sm"
              @input="$v.packageCustomer.discount_val.$touch()"
              type="number"
            />
            <sw-dropdown position="bottom-end" style="z-index: 99">
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
                    packageCustomer.discount_type === 'fixed'
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
        <!----------- SUB TOTAL ----------->
        <!-- <div class="flex items-center justify-between w-full">
              <label
                  class="text-sm font-semibold leading-5 text-gray-500 uppercase"
              >{{ $t('invoices.sub_total') }}</label
              >
              <label
                  class="flex items-center justify-center m-0 text-lg text-black uppercase"
              >
                  <div v-html="$utils.formatMoney(subtotal, currency)" />
              </label>
          </div> -->
        <!-------------- TAXES ------------->
        <div
          v-for="(tax, index) in packageCustomer.taxes"
          :key="tax.tax_type_id"
          class="flex items-center justify-between w-full"
        >
          <label
            class="m-0 text-sm font-semibold leading-5 text-gray-500 uppercase"
          >
            {{ tax.name }} - {{ tax.percent }}%
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
            <!--  <div v-html="$utils.formatMoney(tax.amount, currency)" /> -->
            <trash-icon class="h-5 ml-2" @click="removeInvoiceTax(index)" />
          </label>
        </div>
        <br />
        <!------------- ADD TAXES ----------->

        <!-- <div v-if="taxPerItem === 'NO'">
          <tax
            v-for="(tax, index) in packageCustomer.taxes"
            :index="index"
            :total="subtotalWithDiscount"
            :key="tax.id"
            :tax="tax"
            :taxes="packageCustomer.taxes"
            :currency="currency"
            :total-tax="totalSimpleTax"
            @remove="removeInvoiceTax"
            @update="updateTax"
          />
        </div> -->

        <sw-popup
          v-if="
            (taxPerItem === 'NO' || taxPerItem === null) &&
            parameters.tax_type.value === 'G'
          "
          ref="taxModal"
          class="my-3 text-sm font-semibold leading-5 text-primary-400"
        >
          <div slot="activator" class="float-right pt-2 pb-5">
            + {{ $t('invoices.add_tax') }}
          </div>
          <tax-select-popup :taxes="taxesList" @select="onSelectTax" />
        </sw-popup>
      </div>
    </div>

    <!-- buttons -->
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
      class="mt-4 pull-right"
      @click="next"
    >
      {{ $t('general.continue') }}
      <arrow-right-icon class="h-5 ml-2 -mr-1" />
    </sw-button>
  </div>
</template>
<!-- REALIZAR DOS TABLA CON CHECK PARA ASÍ PODER SEGUIR EL FORMULARIO -->
<script>
import { mapActions, mapGetters } from 'vuex'
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
  ShoppingCartIcon,
  ArrowLeftIcon,
  ArrowRightIcon,
  MinusIcon,
} from '@vue-hero-icons/solid'

import SatelliteIcon from '../../../components/icon/SatelliteIcon.vue'
import RightArrow from '@/components/icon/RightArrow'
import LeftArrow from '@/components/icon/LeftArrow'
import draggable from 'vuedraggable'
import Guid from 'guid'
import TaxStub from '../../../stub/tax'
import PackageItem from '../../corePbx/packages/Item'
import PackageStub from '../../../stub/customerPackage'
import moment from 'moment'
import SwDatePicker from '@bytefury/spacewind/src/components/SwDatePicker'

const { between } = require('vuelidate/lib/validators')

export default {
  components: {
    PackageItem,
    SatelliteIcon,
    FilterIcon,
    XIcon,
    PlusIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    draggable,
    ShoppingCartIcon,
    RightArrow,
    LeftArrow,
    ArrowLeftIcon,
    ArrowRightIcon,
    SwDatePicker,
    MinusIcon,
  },

  data() {
    return {
      item_group: null,
      itemGroupsFetch: [],
      item_groups: [],
      isRequestOnGoing: false,
      isLoading: false,
      parameters: {},
      pbxService: {},
      extensionsApi: [],
      // listados
      selectFieldExtIncluded: [],
      selectFieldExtExcluded: [],

      dataExtensionsExcluded: [],
      dataExtensionsIncluded: [],

      selectFieldDidIncluded: [],
      selectFieldDidExcluded: [],
      dataDidExcluded: [],
      dataDidIncluded: [],
      // items
      discountPerItem: null,
      discountService: 0,
      // form data
      formData: {
        item_groups: [],
        items: [],
        invoice_prorate: false,
      },
      model: {},
      modelDid: {},
      isNoGeneralTaxes: false,
      generateinvoicenow: false,
      taxPerItem: null,
      selectedCurrency: '',
      packageCustomer: {
        sub_total: null,
        total: null,
        tax: [],
        discount_type: 'fixed',
        discount_val: 0,
        discount: 0,
        items: [
          {
            ...PackageStub,
            id: Guid.raw(),
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          },
        ],
        taxes: [],
        taxCdr: {
          name: 'none',
        },
      },
      taxesList: [],
      price: null,
      rate: null,
      capByExtension: 0,
      disabledProrateDate: true,
      viewDateRate: false,
      viewDateRateDid: true,
    }
  },
  filters: {
    formatDate(value) {
      return moment(value).format('DD/MM/YYYY')
    },
  },
  validations() {
    return {
      packageCustomer: {
        discount_val: {
          between: between(0, this.subtotal),
        },
      },
    }
  },

  computed: {
    ...mapGetters('customer', [
      'extensionsInclude',
      'did',
      'didInclude',
      'corePbxServicesParameters',
      'selectedPbxDID',
      'selectedPbxDIDToInclude',
      'selectedPbxExtensions',
      'selectedPbxExtensionsToInclude',
      'selectAllFieldDID',
      'selectAllFieldExtensions',
      'selectAllFieldDIDInclude',
      'selectAllFieldExtInclude',
      'pbxServiceSaved',
      'daysToRenewal',
    ]),

    ...mapGetters('company', ['defaultCurrency']),
    ...mapGetters('pbxService', ['selectedPbxService']),

    selectFieldDID: {
      get: function () {
        // console.log('selectFieldDID', this.selectedPbxDID)
        return this.selectedPbxDID
      },
      set: function (val) {
        this.selectDID(val)
      },
    },

    // DIDs inicio
    // Excluded
    SelectAllFieldDidExcluded: {
      get() {
        const longInclude = this.dataDidExcluded.filter((item) => {
          return this.selectFieldDidExcluded.includes(item.did)
        }).length
        const lobgExclude = this.dataDidExcluded.length

        return longInclude > 0 ? longInclude === lobgExclude : false
      },
      set(val) {
        if (val) {
          this.selectFieldDidExcluded = this.dataDidExcluded.map(
            (did) => did.number
          )
        } else {
          this.selectFieldDidExcluded = []
        }
      },
    },
    // Include
    SelectAllFieldDidIncluded: {
      get() {
        const longInclude = this.dataDidIncluded.filter((item) => {
          return this.selectFieldDidIncluded.includes(item.did)
        }).length
        const lobgExclude = this.dataDidIncluded.length

        return longInclude > 0 ? longInclude === lobgExclude : false
      },
      set(val) {
        if (val) {
          this.selectFieldDidIncluded = this.dataDidIncluded.map(
            (did) => did.number
          )
        } else {
          this.selectFieldDidIncluded = []
        }
      },
    },
    // DIDs fin

    // EXTENSIONES
    SelectAllFieldExtExcluded: {
      get() {
        const longInclude = this.dataExtensionsExcluded.filter((item) => {
          return this.selectFieldExtExcluded.includes(item.ext)
        }).length
        const lobgExclude = this.dataExtensionsExcluded.length
        return longInclude > 0 ? longInclude === lobgExclude : false
      },
      set(val) {
        if (val) {
          this.selectFieldExtExcluded = this.dataExtensionsExcluded.map(
            (ext) => ext.ext
          )
        } else {
          this.selectFieldExtExcluded = []
        }
      },
    },
    SelectAllFieldExtIncluded: {
      get() {
        const longInclude = this.dataExtensionsIncluded.filter((item) => {
          return this.selectFieldExtIncluded.includes(item.ext)
        }).length
        const lobgExclude = this.dataExtensionsIncluded.length
        return longInclude > 0 ? longInclude === lobgExclude : false
      },
      set(val) {
        if (val) {
          this.selectFieldExtIncluded = this.dataExtensionsIncluded.map(
            (ext) => ext.ext
          )
        } else {
          this.selectFieldExtIncluded = []
        }
      },
    },

    discount: {
      get: function () {
        return this.packageCustomer.discount
      },
      set: function (newValue) {
        if (this.packageCustomer.discount_type === 'percentage') {
          this.packageCustomer.discount_val = (this.subtotal * newValue) / 100
        } else {
          this.packageCustomer.discount_val = Math.round(newValue * 100)
        }
        this.packageCustomer.discount = newValue
      },
    },

    subtotalWithDiscount() {
      return this.subtotal - this.discountService
    },

    totalSimpleTax() {
      return Math.round(
        window._.sumBy(this.packageCustomer.taxes, function (tax) {
          if (!tax.compound_tax) {
            return tax.amount
          }
          return 0
        })
      )
    },

    currency() {
      return this.defaultCurrency
    },

    subtotal() {
      return this.formData.items.reduce(function (a, b) {
        return a + b['total']
      }, 0)
    },

    isEdit() {
      if (this.$route.name === 'pbxServices.edit') {
        return true
      }
      return false
    },
  },

  watch: {
    subtotal(newValue) {
      if (this.packageCustomer.discount_type === 'percentage') {
        this.packageCustomer.discount_val =
          (this.packageCustomer.discount * newValue) / 100
      }
    },
  },

  created() {
    this.loadData()
    window.hub.$on('tax_for_cdrs', this.selectTaxCDR)
    window.hub.$on('newTax', this.onSelectTax)
  },

  destroyed() {
    if (this.selectAllFieldDID) {
      this.selectAllDID()
    }

    if (this.selectAllFieldDIDInclude) {
      this.selectAllDIDInclude()
    }

    if (this.selectAllFieldExtensions) {
      this.selectAllExtensions()
    }

    if (this.selectAllFieldExtInclude) {
      this.selectAllExtensionsInclude()
    }
  },

  methods: {
    ...mapActions('customer', [
      'fetchExtensionPBX',
      'fetchDIDPBX',
      'selectDID',
      'selectDIDInclude',
      'selectAllDID',
      'selectAllDIDInclude',
      'selectExtensions',
      'selectExtensionsInclude',
      'selectAllExtensions',
      'selectAllExtensionsInclude',
      'setSelectAllStateDID',
      'setSelectAllStateExtensions',
      'setSelectAllStateDIDInclude',
      'setSelectAllStateExtInclude',
      'fetchCompanySettings',
      'fetchItemGroups',
      'setPbxServicesIncludedData',
      'setDataExtensionState',
      'setDataDidState',
    ]),
    ...mapActions('taxType', ['fetchTaxTypes']),

    ...mapActions('modal', ['openModal']),

    ...mapActions('item', ['fetchItems']),

    ...mapActions('pbx', ['fetchPackages']),

    ...mapActions('pbxService', [
      'fetchDIDs',
      'fetchExtensions',
      'fetchItemsPbxService',
    ]),

    async loadData() {
      this.isLoading = true
      this.fetchInitialItemGroups()
      this.fetchInitialItems()
      this.parameters = this.corePbxServicesParameters.parameters
      const taxesData = await this.fetchTaxTypes({ limit: 'all' })
      this.taxesList = JSON.parse(JSON.stringify(taxesData.data.taxTypes.data))

      // edit
      if (this.isEdit) {
        // get service from state
        let service = JSON.parse(JSON.stringify(this.selectedPbxService))
        let params = {
          pbx_service_id: service.id,
          page: 1,
          limit: 100,
        }

        if (this.parameters.package.extensions) {
          const extensionsIncluded = await this.fetchExtensions(params)
          const extensionsIncludesMolded = this.fillModelFormExtensions(
            extensionsIncluded.data.response.service_extensions.data
          )
          this.dataExtensionsIncluded = JSON.parse(
            JSON.stringify(extensionsIncludesMolded)
          )

          let extIncluded = []
          this.dataExtensionsIncluded.forEach((extension) => {
            extIncluded.push(String(extension.ext))
          })
          let extsApi = await this.fetchDataExtension({
            page: 1,
            filter: null,
            sort: null,
          })
          const ExtensionesExcluidas = extsApi.filter(
            (extension) => !extIncluded.includes(extension.ext)
          )
          const extensionsExcluidasMolded =
            this.fillModelFormExtensions(ExtensionesExcluidas)
          this.dataExtensionsExcluded = JSON.parse(
            JSON.stringify(extensionsExcluidasMolded)
          )
        }

        // fetch exclude
        if (this.parameters.package.did) {
          // fecth include DIDs
          const didIncluded = await this.fetchDIDs(params)
          const didIncludedMolded = this.fillModelDid(
            didIncluded.data.response.service_did.data
          )
          this.dataDidIncluded = JSON.parse(JSON.stringify(didIncludedMolded))

          let didsNumberIncluded = []
          this.dataDidIncluded.forEach((did) => {
            didsNumberIncluded.push(did.number)
          })
          let didsApi = await this.fetchDataDID({
            page: 1,
            filter: null,
            sort: null,
          })

          const didExcluidas = didsApi.filter(
            (did) => !didsNumberIncluded.includes(did.number)
          )
          const didExcluidasMolded = this.fillModelDid(didExcluidas)
          this.dataDidExcluded = JSON.parse(JSON.stringify(didExcluidasMolded))
          // ordenar por numero
          this.dataDidIncluded.sort((a, b) => {
            if (a.number > b.number) return 1
            if (a.number < b.number) return -1
            return 0
          })
          this.dataDidExcluded.sort((a, b) => {
            if (a.number > b.number) return 1
            if (a.number < b.number) return -1
            return 0
          })
        }

        // fetch items
        let items = await this.fetchItemsPbxService(params)
        service.items = items.data.response.service_items.data
        console.log(
          items.data.response.service_items.data,
          'items.data.response.service_items.data'
        )
        //
        this.price = service.pbx_package.rate
        this.capByExtension = service.cap_extension
        this.discountService = service.allow_discount_value
        this.packageCustomer.discount_type = service.allow_discount_type
        this.formData = { ...service }

        // Un servicio creado SIN descuento pero luego se edita CON descuento
        // if (this.parameters.allow_discount) {
        //   // let response = await this.fetchPackages(this.parameters.package.id)
        //   // let pack_data = response.data.response
        //   // console.log('paquete: ', this.parameters.discount_type);
        //   console.log('paquete: ', this.parameters.package);
        //   console.log(this.packageCustomer.discount_type, "packageCustomer.discount_type")

        //   if (this.parameters.package.discount_term_type === 'G') {
        //     console.log(this.parameters.package, "this.parameters.package.")
        //     this.formData.discount_type = this.parameters.package.discount_term_type;
        //     this.discount = pack_data.discount;
        //   }
        // }

        // Un servicio creado CON descuento pero luego se edita SIN descuento
        if (!this.parameters.allow_discount) {
          this.packageCustomer.discount_type
          this.formData.discount_type = 'percentage'
          this.discount = 0
        }

        this.formData.items = []
        service.items.forEach((item) => {
            if (this.taxPerItem === 'YES') {
              console.log(item, 'item YES YES edit')
              this.formData.items.push({
                taxes: item.taxes.length == 0 ? [{ ...TaxStub }] : item.taxes,
                ...item,
                amount: item.amount,
                compound_tax: item.compound_tax,
                id: item.id,
                quantity: item.quantity,
                description: item.description,
                item_group_id: item.item_group_id,
                item_group_name: item.item_group_name,
                total: item.total,
                unit_price: item.unit_price,
                name: item.name,
                tax_type_id: item.tax_type_id,
                percent: item.percent,
              })
            } else {
              this.formData.items.push({
                ...item,
                taxes: [],
                amount: item.amount,
                compound_tax: item.compound_tax,
                id: item.id,
                quantity: item.quantity,
                description: item.description,
                item_group_id: item.item_group_id,
                item_group_name: item.item_group_name,
                total: item.total,
                unit_price: item.unit_price,
                name: item.name,
                tax_type_id: item.tax_type_id,
                percent: item.percent,
              })
            }
        })


        this.formData.item_groups = []
        if (service.items.length > 0) {
          this.item_groups.forEach((itemGroup) => {
            // validar item group para asignar el que se registró
            if (itemGroup.id === service.items[0].item_group_id) {
              this.item_group = itemGroup
              this.formData.item_groups.push(itemGroup)
            }
          })
        }

        this.formData.taxes = []
        if (this.parameters.tax_type.value === 'G' && service.taxes) {
          service.taxes.forEach((_tax) => {
            this.packageCustomer.taxes.push({
              name: _tax.name,
              percent: _tax.percent,
              compound_tax: _tax.compound_tax,
              tax_types_id: _tax.tax_types_id,
              amount: 0,
            })
          })
        }
        // tax cdr
        const taxCdrFinded = this.taxesList.find(
          (tax) => tax.id === service.tax_type_id
        )
        if (taxCdrFinded) {
          this.packageCustomer.taxCdr = {
            id: taxCdrFinded.id,
            name: taxCdrFinded.name,
            percent: taxCdrFinded.percent,
            compound_tax: taxCdrFinded.compound_tax,
            tax_types_id: taxCdrFinded.id,
            amount: 0,
          }
        }

        this.isLoading = false
      }
      // register
      else {
        //
        this.price = this.parameters.package.rate
        this.capByExtension =
          this.parameters.package.inclusive_minutes == null
            ? 0
            : this.parameters.package.inclusive_minutes

       

        

        // extensions
        if (this.parameters.package.extensions) {
          const extensionesIncluidas = await this.fetchDataExtension({
            page: 1,
            filter: null,
            sort: null,
          })
          const extensionsIncluidasMolded =
            this.fillModelFormExtensions(extensionesIncluidas)
          this.dataExtensionsIncluded = JSON.parse(
            JSON.stringify(extensionsIncluidasMolded)
          )
        }

        // did
        if (this.parameters.package.did) {
          let didsApi = await this.fetchDataDID({
            page: 1,
            filter: null,
            sort: null,
          })
          const didIncluidasMolded = this.fillModelDid(didsApi)
          this.dataDidIncluded = JSON.parse(JSON.stringify(didIncluidasMolded))
        }
        // items
        if (this.parameters.package.items.length > 0) {
          this.formData.items = []
          this.parameters.package.items.forEach((item) => {
            if (this.taxPerItem === 'YES') {
              console.log(item, 'item')
              this.formData.items.push({
                taxes: item.taxes.length == 0 ? [{ ...TaxStub }] : item.taxes,
                ...item,
                amount: item.amount,
                compound_tax: item.compound_tax,
                id: item.id,
                quantity: item.quantity,
                description: item.description,
                item_group_id: item.item_group_id,
                item_group_name: item.item_group_name,
                total: item.total,
                unit_price: item.unit_price,
                name: item.name,
                tax_type_id: item.tax_type_id,
                percent: item.percent,
              })
            } else {
              this.formData.items.push({
                ...item,
                taxes: [],
                amount: item.amount,
                compound_tax: item.compound_tax,
                id: item.id,
                quantity: item.quantity,
                description: item.description,
                item_group_id: item.item_group_id,
                item_group_name: item.item_group_name,
                total: item.total,
                unit_price: item.unit_price,
                name: item.name,
                tax_type_id: item.tax_type_id,
                percent: item.percent,
              })
            }
          })

          this.formData.item_groups = []
          this.item_groups.forEach((itemGroup) => {
            // validar item group para asignar el que se registró
            if (itemGroup.id === this.parameters.package.item_group_id) {
              this.item_group = itemGroup
              this.formData.item_groups.push(itemGroup)
            }
          })
        }

        // discount
        if (this.parameters.package.value_discount) {
          this.discountService = this.parameters.package.value_discount
          this.packageCustomer.discount_type =
            this.parameters.package.type || 'percentage'
          console.log(
            this.parameters.package.type,
            'this.parameters.package.type'
          )
        }
        // taxes
        this.formData.taxes = []
        if (
          this.parameters.tax_type.value === 'G' &&
          this.parameters.package.tax_types
        ) {
          this.parameters.package.tax_types.forEach((_tax) => {
            this.onSelectTax(_tax)
          })
        }
        this.isLoading = false
      } // fin register

      this.$refs.tablesDids.refresh()
      this.$refs.tablesExt.refresh()
    },

    fillModelFormExtensions(data) {
      data.forEach((ext) => {
        ext.invoice_prorate = ext.invoice_prorate
        ext.pbxext_id = ext.pbxext_id
        ext.pbx_tenant_code =
          ext.pbx_tenant_code || this.parameters.pbx_tenant_code
        ext.pbx_server_id =
          ext.pbx_server_id || this.parameters.package.pbx_server_id
        ext.statusProrate = ext.date_prorate ? true : false
        ;(ext.date_prorate = ext.date_prorate
          ? moment(ext.date_prorate)
          : null),
          (ext.profile_rate = this.parameters.package.profile_extensions.rate)
        ext.cost_per_day = this.getCostPerDay(
          this.parameters.term,
          this.parameters.package.profile_extensions.rate
        )
        ext.prorate = ext.prorate || 0
      })
      return data
    },

    fillModelDid(data) {
      data.forEach((did) => {
        did.invoice_prorate = did.invoice_prorate
        did.pbxdid_id = did.pbxdid_id
        did.pbx_tenant_code =
          did.pbx_tenant_code || this.parameters.pbx_tenant_code
        did.pbx_server_id =
          did.pbx_server_id || this.parameters.package.pbx_server_id
        did.statusProrate = did.date_prorate ? true : false
        did.date_prorate = did.date_prorate ? moment(did.date_prorate) : null
        did.cost_per_day = this.getCostPerDay(
          this.parameters.term,
          this.parameters.package.profile_did2.did_rate
        )
        did.prorate = did.prorate || 0
      })
      return data
    },

    async fetchDataExtension({ page, filter, sort }) {
      this.parameters = this.corePbxServicesParameters.parameters
      let params = {
        pbx_package_id: this.parameters.pbx_package_id,
        pbx_tenant_id: this.parameters.tenant_api_id,
        pbx_server_id: this.parameters.package.pbx_server_id,
        tenant_code: this.parameters.pbx_tenant_code,
        isEdit: this.isEdit,
      }
      this.isRequestOngoing = true
      let response = await this.fetchExtensionPBX(params)
      this.isRequestOngoing = false

      let ExtPbx = []
      for (const property in response.data.ExtensionByTenantList) {
        ExtPbx.push({
          id: property,
          pbxext_id: response.data.ExtensionByTenantList[property].pbxext_id,
          email: response.data.ExtensionByTenantList[property].email,
          ext: response.data.ExtensionByTenantList[property].ext,
          ext_id: response.data.ExtensionByTenantList[property].ext_id,
          linenum: response.data.ExtensionByTenantList[property].linenum,
          location: response.data.ExtensionByTenantList[property].location,
          macaddress: response.data.ExtensionByTenantList[property].macaddress,
          name: response.data.ExtensionByTenantList[property].name,
          protocol: response.data.ExtensionByTenantList[property].protocol,
          status: response.data.ExtensionByTenantList[property].status,
          ua_fullname:
            response.data.ExtensionByTenantList[property].ua_fullname,
          ua_id: response.data.ExtensionByTenantList[property].ua_id,
          ua_name: response.data.ExtensionByTenantList[property].ua_name,
          only_api: response.data.ExtensionByTenantList[property].only_api,
          db_available:
            response.data.ExtensionByTenantList[property].db_available,
          profile_rate: this.parameters.package.profile_extensions,
        })
      }

      this.ExtensionExist = false
      if (ExtPbx > 0) {
        this.ExtensionExist = true
      }
      // console.log('extensiones: ', ExtPbx);
      return ExtPbx
    },

    async fetchDataDID({ page, filter, sort }) {
      this.parameters = this.corePbxServicesParameters.parameters
      let params = {
        pbx_package_id: this.parameters.pbx_package_id,
        pbx_tenant_id: this.parameters.tenant_api_id,
        pbx_server_id: this.parameters.package.pbx_server_id,
        tenant_code: this.parameters.pbx_tenant_code,
        isEdit: this.isEdit,
      }
      //
      this.isRequestOngoing = true
      let response = await this.fetchDIDPBX(params)
      this.isRequestOngoing = false
      let didPbx = []

      for (const property in response.data.DIDByTenantList) {
        //
        didPbx.push({
          id: property,
          pbxdid_id: response.data.DIDByTenantList[property].pbxdid_id,
          e164: response.data.DIDByTenantList[property].e164,
          e164_2: response.data.DIDByTenantList[property].e164_2,
          ext: response.data.DIDByTenantList[property].ext,
          number: response.data.DIDByTenantList[property].number,
          number2: response.data.DIDByTenantList[property].number2,
          server: response.data.DIDByTenantList[property].server,
          status: response.data.DIDByTenantList[property].status,
          type: response.data.DIDByTenantList[property].type,
          trunk: response.data.DIDByTenantList[property].trunk,
          only_api: response.data.DIDByTenantList[property].only_api,
          db_available: response.data.DIDByTenantList[property].db_available,
        })
      }

      this.didExist = false
      if (didPbx > 0) {
        this.didExist = true
      }

      return didPbx
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
        this.taxPerItem = response.data.tax_per_item
      }
      // }

      Promise.all([
        this.fetchItems({
          filter: {},
          orderByField: '',
          orderBy: '',
          limit: 100,
        }),
        this.fetchCompanySettings(['estimate_auto_generate']),
      ])
        .then(async ([res1, res2]) => {
          this.itemsF = res1.data.items.data
        })
        .catch((error) => {
          console.log(error)
        })
    },

    onSelectTax(selectedTax) {
      let amount = 0

      if (typeof this.packageCustomer.taxes === 'undefined') {
        this.packageCustomer.taxes = []
      }

      this.packageCustomer.taxes.push({
        name: selectedTax.name,
        percent: selectedTax.percent,
        compound_tax: selectedTax.compound_tax,
        tax_types_id: selectedTax.id,
        amount,
      })
      if (this.$refs) {
        if (this.$refs.taxModal) {
          this.$refs.taxModal.close()
        }
      }
    },

    removeInvoiceTax(index) {
      this.packageCustomer.taxes.splice(index, 1)
    },

    selectTaxCDR(selectedTax) {
      let amount = 0
      this.packageCustomer.taxCdr = {
        id: selectedTax.id,
        name: selectedTax.name,
        percent: selectedTax.percent,
        compound_tax: selectedTax.compound_tax,
        tax_types_id: selectedTax.id,
        amount,
      }
      this.$refs.taxModalForCdrs.close()
    },
    removeTaxCdr() {
      this.packageCustomer.taxCdr = {
        name: 'none',
      }
    },

    updateTax(data) {
      Object.assign(this.packageCustomer.taxes[data.index], { ...data.item })
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

    async includeExtensions(ext = null) {
      if (ext !== null) this.selectFieldExtExcluded = [ext]

      this.selectFieldExtExcluded.forEach((ext) => {
        const indexExtensionInclude = this.dataExtensionsExcluded.findIndex(
          (element) => element.ext == ext
        )
        if (indexExtensionInclude > -1) {
          // antes de eliminar de la lista de extensiones incluidas incluyela en la lista de extensiones excluidas
          this.dataExtensionsIncluded.push(
            this.dataExtensionsExcluded[indexExtensionInclude]
          )
          this.dataExtensionsExcluded.splice(indexExtensionInclude, 1)
        }
      })
      this.$refs.tablesDids.refresh()
      this.$refs.tablesExt.refresh()
    },

    async excludeExtensions(ext = null) {
      if (ext !== null) this.selectFieldExtIncluded = [ext]

      this.selectFieldExtIncluded.forEach((ext) => {
        const indexExtensionInclude = this.dataExtensionsIncluded.findIndex(
          (element) => element.ext == ext
        )
        if (indexExtensionInclude > -1) {
          // antes de eliminar de la lista de extensiones incluidas incluyela en la lista de extensiones excluidas
          this.dataExtensionsExcluded.push(
            this.dataExtensionsIncluded[indexExtensionInclude]
          )
          this.dataExtensionsIncluded.splice(indexExtensionInclude, 1)
        }
      })
      this.$refs.tablesDids.refresh()
      this.$refs.tablesExt.refresh()
    },
    async excludeDid(number = null) {
      if (number !== null) this.selectFieldDidIncluded = [number]

      this.selectFieldDidIncluded.forEach((did) => {
        const indexDidInclude = this.dataDidIncluded.findIndex(
          (element) => element.number == did
        )
        if (indexDidInclude > -1) {
          // antes de eliminar de la lista de did incluidas incluyela en la lista de did excluidas
          this.dataDidExcluded.push(this.dataDidIncluded[indexDidInclude])
          this.dataDidIncluded.splice(indexDidInclude, 1)
        }
      })
      this.$refs.tablesDids.refresh()
      this.$refs.tablesExt.refresh()
    },
    async includeDid(number = null) {
      if (number !== null) this.selectFieldDidExcluded = [number]

      this.selectFieldDidExcluded.forEach((did) => {
        const indexDidInclude = this.dataDidExcluded.findIndex(
          (element) => element.number == did
        )
        if (indexDidInclude > -1) {
          // antes de eliminar de la lista de did incluidas incluyela en la lista de did excluidas
          this.dataDidIncluded.push(this.dataDidExcluded[indexDidInclude])
          this.dataDidExcluded.splice(indexDidInclude, 1)
        }
      })
      this.$refs.tablesDids.refresh()
      this.$refs.tablesExt.refresh()
    },

    addItem() {
      if (this.formData.items.length > 0) {
        const isId = this.isEdit
          ? (element) => element.items_id == null
          : (element) => element.item_id == null
        const index = this.formData.items.findIndex(isId)

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

      // if (!this.isEdit) {
      let response = await this.fetchCompanySettings([
        'discount_per_item',
        'tax_per_item',
      ])

      if (response.data) {
        this.discountPerItemStore = response.data.discount_per_item
        this.discountPerItem = response.data.discount_per_item
        this.taxPerItem = response.data.tax_per_item
      }
      // }

      Promise.all([
        this.fetchItems({
          filter: {},
          orderByField: '',
          orderBy: '',
        }),
        this.fetchCompanySettings(['estimate_auto_generate']),
      ])
        .then(async ([res1, res2]) => {
          this.itemsF = res1.data.items.data
        })
        .catch((error) => {
          console.log(error)
        })
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
        /* console.log('TaxStub', TaxStub);
          console.log('items', this.formData.items); */
      })
      setTimeout(() => {
        this.item_group = null
      }, 100)
    },

    total() {
      return this.subtotalWithDiscount + this.totalTax
    },

    selectFixed() {
      if (this.packageCustomer.discount_type === 'fixed') {
        return
      }
      this.packageCustomer.discount_val = Math.round(
        this.packageCustomer.discount * 100
      )
      this.packageCustomer.discount_type = 'fixed'
    },

    selectPercentage() {
      if (this.packageCustomer.discount_type === 'percentage') {
        return
      }
      this.packageCustomer.discount_val =
        (this.subtotal * this.packageCustomer.discount) / 100
      this.packageCustomer.discount_type = 'percentage'
    },

    dataExtensionsIncludedMetho({ sort }) {
      // ordenar el array this.dataExtensionsIncluded. por el campo que se selecciono en el filtro
      this.dataExtensionsIncluded.sort((a, b) => {
        if (a[sort.fieldName] < b[sort.fieldName]) {
          return sort.order === 'asc' ? -1 : 1
        }
        if (a[sort.fieldName] > b[sort.fieldName]) {
          return sort.order === 'asc' ? 1 : -1
        }
        return 0
      })
      return {
        data: this.dataExtensionsIncluded,
      }
    },

    dataDidIncludedMetho({ sort }) {
      // ordenar el array this.dataDidIncluded. por el campo que se selecciono en el filtro
      this.dataDidIncluded.sort((a, b) => {
        if (a[sort.fieldName] < b[sort.fieldName]) {
          return sort.order === 'asc' ? -1 : 1
        }
        if (a[sort.fieldName] > b[sort.fieldName]) {
          return sort.order === 'asc' ? 1 : -1
        }
        return 0
      })
      return {
        data: this.dataDidIncluded,
      }
    },

    async serRatePricePerDid(didIncluded) {
      let did = []
      let rate_per_minute = 0

      if (
        (typeof this.parameters.package.profile_did2 !== 'undefined' &&
          this.parameters.package.profile_did2 !== null) ||
        (typeof this.parameters.package.profile_did !== 'undefined' &&
          this.parameters.package.profile_did !== null)
      ) {
        if (
          typeof this.parameters.package.profile_did2 !== 'undefined' &&
          this.parameters.package.profile_did2 !== null
        ) {
          rate_per_minute = this.parameters.package.profile_did2.did_rate
        } else {
          rate_per_minute = this.parameters.package.profile_did.did_rate
        }

        let rate_per_minute2 = rate_per_minute

        let custom_did_group = JSON.parse(
          JSON.stringify(this.parameters.package.custom_did_groups)
        )
        let custom_did_group_id = 0
        for (const property in didIncluded) {
          rate_per_minute = rate_per_minute2
          custom_did_group_id = 0
          for (let i = 0; i < custom_did_group.length; i++) {
            // comparar prefijo
            if (
              String(custom_did_group[i].prefijo) ==
              didIncluded[property].number.substring(
                0,
                String(custom_did_group[i].prefijo).length
              )
            ) {
              rate_per_minute = custom_did_group[i].rate_per_minute
               rate_per_minute  = (Math.round(rate_per_minute * 100) / 100).toFixed(2)
              custom_did_group_id = custom_did_group[i].id
              break
            }
          }
          //
          did.push({
            id: didIncluded[property].id,
            invoice_prorate: didIncluded[property].invoice_prorate,
            pbx_tenant_code: didIncluded[property].pbx_tenant_code,
            pbx_server_id: didIncluded[property].pbx_server_id,
            pbxdid_id: didIncluded[property].pbxdid_id,
            api_id: didIncluded[property].api_id,
            e164: didIncluded[property].e164,
            e164_2: didIncluded[property].e164_2,
            ext: didIncluded[property].ext,
            number: didIncluded[property].number,
            number2: didIncluded[property].number2,
            server: didIncluded[property].server,
            status: didIncluded[property].status,
            type: didIncluded[property].type,
            trunk: didIncluded[property].trunk,
            prorate: didIncluded[property].prorate,
            date_prorate: didIncluded[property].date_prorate,
            cost_per_day: didIncluded[property].cost_per_day,
            only_api: didIncluded[property].only_api,
            rate_per_minute,
            custom_did_group_id,
          })
        }
      }
      return did
    },
    switchExtProrate({ date_prorate, cost_per_day, ext, statusProrate }) {
      const indexExtension = this.dataExtensionsIncluded.findIndex(
        (p) => p.ext === ext
      )
      if (statusProrate) {
        const dateMol = date_prorate ? date_prorate : new Date()
        this.dataExtensionsIncluded[indexExtension].date_prorate = dateMol
        const date1 = moment(dateMol)
        const date2 = moment(this.parameters.renewal_date)
        const diferenDias = date2.diff(date1, 'days')
        this.dataExtensionsIncluded[indexExtension].prorate =
          cost_per_day * diferenDias * 100

        //  restringir date menos un dia de la renovacion del servicio
        this.$refs.ProrateDatepicker.$refs.BaseDatepicker.fp.config.maxDate =
          moment(date2).add(-1, 'days').format('YYYY-MM-DD')
      } else {
        this.dataExtensionsIncluded[indexExtension].date_prorate = null
        this.dataExtensionsIncluded[indexExtension].prorate = null
      }
    },

    prorateDidSwitchChange({
      date_prorate,
      cost_per_day,
      number,
      statusProrate,
    }) {
      const indexDid = this.dataDidIncluded.findIndex(
        (p) => p.number === number
      )
      if (statusProrate) {
        const dateMol = date_prorate ? date_prorate : new Date()
        this.dataDidIncluded[indexDid].date_prorate = dateMol
        const date1 = moment(dateMol)
        const date2 = moment(this.parameters.renewal_date)
        const diferenDias = date2.diff(date1, 'days')
        this.dataDidIncluded[indexDid].prorate =
          cost_per_day * diferenDias * 100

        //  restringir date menos un dia de la renovacion del servicio
        this.$refs.ProrateDatepicker.$refs.BaseDatepicker.fp.config.maxDate =
          moment(date2).add(-1, 'days').format('YYYY-MM-DD')
      } else {
        this.dataDidIncluded[indexDid].date_prorate = null
        this.dataDidIncluded[indexDid].prorate = null
      }
    },

    getCostPerDay(term, rate) {
      let costPerDay = 0
      switch (term) {
        case 'daily':
          costPerDay = rate / 1
          break

        case 'weekly':
          costPerDay = rate / 7
          break

        case 'monthly':
          costPerDay = rate / 30
          break

        case 'bimonthly':
          costPerDay = rate / 60
          break

        case 'quarterly':
          costPerDay = rate / 90
          break

        case 'yearly':
          costPerDay = rate / 365
          break

        case 'biannual':
          costPerDay = rate / 730
          break
      }
      return costPerDay
    },
    /* next screen */
    async next() {
      // set rate-price / did
      this.dataDidIncluded = await this.serRatePricePerDid(this.dataDidIncluded)
      // armar data
      let includedData = {
        ext: this.dataExtensionsIncluded,
        did: this.dataDidIncluded,
        subtotal: this.subtotal,
        discount_value: this.discountService,
        discount_calc: this.packageCustomer.discount_type,
        price: this.price,
        items: this.formData.items,
        taxes: this.packageCustomer.taxes,
        cap_extension: this.capByExtension,
        invoice_prorate: this.formData.invoice_prorate,
        taxCdr: this.parameters.package.call_ratings
          ? this.packageCustomer.taxCdr
          : null,
      }
      // enviar "included" data el estado
      this.setPbxServicesIncludedData(includedData)
      this.isLoading = true
      this.$emit('next')
      this.isLoading = false
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
.only_api {
  /* color: #bcb9ef; */
  color: purple;
  font-weight: bold;
}
</style>