<template>
  <sw-wizard-step :title="$t('customers.corepbx_parameters')">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <div class="grid grid-cols-12 mt-5">
      <div class="col-span-12">
        <sw-input-group :label="$tc('packages.package', 1)" class="mb-4" :error="packageError" required>
          <sw-select v-model="formData.package" :invalid="$v.formData.package.$error" :options="groups" :searchable="true"
            :show-labels="false" :placeholder="$t('customers.select_a_pbx_package')" class="mt-2" label="pbx_package_name"
            @select="setTenant" @input="$v.formData.package.$touch()" :disabled="isEdit" />
        </sw-input-group>
        <!-- package info -->
        <div>
          <p v-if="packageInfo.serverName" class="
              text-sm
              not-italic
              font-medium
              leading-5
              text-primary-800 text-sm
            ">
            {{ $t('customers.server') }}
          </p>
          <p class="
              mb-1
              text-sm
              font-normal
              leading-5
              non-italic
              text-primary-800
            ">
            <!-- {{ this.parameters ? this.parameters.term : '' }} -->
            {{ packageInfo.serverName }}
          </p>
        </div>

        <div>
          <p v-if="packageInfo.extName" class="
              text-sm
              not-italic
              font-medium
              leading-5
              text-primary-800 text-sm
            ">
            {{ $t('customers.extesion_profile') }}
          </p>
          <p class="
              mb-1
              text-sm
              font-normal
              leading-5
              non-italic
              text-primary-800
            ">
            <!-- {{ this.parameters ? this.parameters.term : '' }} -->
            {{ packageInfo.extName }}
          </p>
        </div>

        <div>
          <p v-if="packageInfo.didName" class="
              text-sm
              not-italic
              font-medium
              leading-5
              text-primary-800 text-sm
            ">
            {{ $t('customers.did_package') }}
          </p>
          <p class="
              mb-1
              text-sm
              font-normal
              leading-5
              non-italic
              text-primary-800
            ">
            {{ packageInfo.didName }}
          </p>
        </div>

        <div v-if="packageInfo.call_ratings">
          <p class="
              text-sm
              not-italic
              font-medium
              leading-5
              text-primary-800 text-sm
            ">
            {{ $t('customers.call_ratings') }}
          </p>
          <p class="
              mb-1
              text-sm
              font-normal
              leading-5
              non-italic
              text-primary-800
            ">
            <!-- {{ this.parameters ? this.parameters.term : '' }} -->
            {{ $t('general.active') }}
          </p>
          <br />
        </div>

        <sw-input-group :label="$tc('customers.status', 2)" class="mb-4">
          <sw-select v-model="formData.status" :options="statusOptions" :searchable="true" :show-labels="false"
            :placeholder="$t('customers.select_a_status')" class="mt-2" label="name" @select="setBeginDate" />
        </sw-input-group>

        <sw-input-group :label="$t('customers.term')" class="mb-4">
          <sw-select v-model="formData.term" :options="term" :searchable="true" :show-labels="false"
            :placeholder="$t('customers.select_a_term')" class="mt-2" label="name" :disabled="isEdit" />
        </sw-input-group>

        <div v-if="showDates">
          <sw-input-group :label="$t('customers.date_act')" class="mb-4" :error="dateBeginError" required>
            <base-date-picker v-model="formData.date_begin" :invalid="$v.formData.date_begin.$error"
              :calendar-button="true" calendar-button-icon="calendar" :disabled="isEdit" ref="dateBegin" />

            <!-- warning message -->
            <p v-if="error_activation_date">
              <small style="color: red">{{ $t('general.warning_service2') }}</small>
            </p>
          </sw-input-group>

          <!-- renew  al date -->
          <sw-input-group :label="$t('customers.renewal_date')" class="mb-4" v-if="isEdit">
            <base-date-picker v-model="formData.renewal_date" :calendar-button="true" calendar-button-icon="calendar"
              @input="validateRenewalDate" ref="renewalDate" />

            <!-- warning message -->
            <p v-if="error_renewal_date">
              <small style="color: red">{{ $t('general.warning_service4') }}</small>
            </p>
          </sw-input-group>
        </div>

        <span v-if="isTenant">
          <sw-input-group :label="$t('customers.tenant')" class="mb-4" :error="tenatError">
            <sw-select v-model="formData.tenant" :invalid="$v.formData.tenant.$error" :options="tenant" :searchable="true"
              :show-labels="false" :placeholder="$t('customers.select_a_tenant')" class="mt-2" label="name"
              :disabled="isEdit" @input="$v.formData.tenant.$touch()" />
          </sw-input-group>
          <!-- warning message -->
          <p v-if="!isEdit &&
            formData.package.call_ratings &&
            !formData.package.template_did_id &&
            !formData.package.template_extension_id
            ">
            <small style="color: red">
              {{ $t('general.warning_service5') }}
            </small>
          </p>
          <small v-if="formData.package.all_cdrs" style="color: red">
            {{ $t('general.warning_service6') }}
          </small>

          <p v-if="tenant.length <= 0 && showMessageTenants" class="text-danger">
            {{ $t('general.warning_service7') }}
          </p>
        </span>

        <!-- prefix rate groups -->
        <sw-input-group :label="$t('customers.prefix_rate_group')" class="mb-4 relative" v-if="prefixrateGroups.length">
          <sw-select v-model="prefixrate_groups_id" :options="prefixrateGroupsInbound" :searchable="true"
            :show-labels="false" :allow-empty="true" :placeholder="$t('customers.select_a_prefix_rate_group')"
            class="mt-2" label="name" track-by="id" :tabindex="10" :multiple="true"
            @input="$v.formData.prefix_rate_groups.$touch()" />
        </sw-input-group>

        <!-- prefix rate groups outbound-->
        <sw-input-group :label="$t('customers.prefix_rate_group_outbound')" class="mb-4 relative"
          v-if="prefixrateGroups.length">
          <sw-select v-model="prefixrate_groups_outbound_id" :options="prefixrateGroupsOutbound" :searchable="true"
            :show-labels="false" :allow-empty="true" :placeholder="$t('customers.select_a_prefix_rate_group')"
            class="mt-2" label="name" track-by="id" :tabindex="10" :multiple="true"
            @input="$v.formData.prefix_rate_groups_outbound.$touch()" />
        </sw-input-group>

        <!-- Custom App rate-->
        <sw-input-group :label="$t('customers.service_custom_app_rate')" class="mb-4 relative">
          <sw-select v-model="selectCustom_app_rate" :options="customAppRate" :searchable="true" :show-labels="false"
            :allow-empty="false" :placeholder="$t('customers.service_custom_app_rate')" class="mt-2" label="name"
            @input="selectCustomAppRateMetho" />
          <x-circle-icon @click="selectCustom_app_rate = { name: 'None' }" v-if="selectCustom_app_rate && selectCustom_app_rate.name !== 'None'
            " style="top: 42px; right: 28px" class="h-5 absolute text-gray-400 cursor-pointer" />
        </sw-input-group>

        <sw-input-group :label="$t('customers.tax_type')" class="mb-4">
          <sw-select v-model="formData.tax_type" :options="tax_type" :searchable="true" :show-labels="false"
            :allow-empty="false" :placeholder="$t('customers.select_a_tax_type')" class="mt-2" label="name"
            :disabled="taxPerItem == 'NO' || isEdit" />
          <small class="text-secondary">
            {{ $t('general.warning_service8') }}
          </small>
        </sw-input-group>

        <sw-input-group :label="$t('customer_address.title_service')" class="mb-4 relative">
          <sw-select v-model="formData.address" :options="addressOptions" :searchable="true" :show-labels="false"
            :placeholder="$t('customer_address.title_service')" class="mt-2" label="name">
            <template slot="singleLabel" slot-scope="option">
              <div class="flex items-center">
                <div v-if="option.option.state" class="text-sm">{{ option.option.state.name + " " +
                  option.option.address_street_1 }}</div>
              </div>
            </template>
            <template slot="option" slot-scope="option">
              <div class="flex items-center">
                <div v-if="option.option.state" class="text-sm">{{ option.option.state.name + " " +
                  option.option.address_street_1 }}</div>
              </div>
            </template>
          </sw-select>
          <x-circle-icon @click="formData.address = {}" v-if="formData.address && formData.address.state"
            style="top: 42px; right: 28px" class="h-5 absolute text-gray-400 cursor-pointer" />
        </sw-input-group>

        <div class="flex my-8 mb-4">
          <div class="relative w-12">
            <sw-switch v-model="formData.auto_suspension" class="absolute" style="top: -20px"
              @change="setDiscountParams" />
          </div>

          <div class="ml-4">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('customers.auto_suspension') }}
            </p>
          </div>
        </div>

        <!-- suspension type -->
        <sw-input-group :label="$tc('customers.suspension_type')" class="mb-4"
          v-if="this.formData.auto_suspension == true">
          <sw-select v-model="formData.suspension_type_op" :options="SuspensionTypeOptions" :searchable="true"
            :show-labels="false" :placeholder="$t('customers.suspension_type')" class="mt-2" label="name" />
        </sw-input-group>



        <br />

        <!-- MAIN UPDATE DIDS AND EXTENSION -->
        <div class="flex mb-4">
          <div class="relative w-12">
            <sw-switch v-model="formData.main_update" class="absolute" style="top: -20px" />
          </div>

          <div class="ml-4">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('customers.main_update_dids_and_extensions') }}
            </p>
          </div>
        </div>

        <!-- MAIN UPDATE DIDS AND EXTENSION -->
        <div class="flex my-8 mb-4">
          <div class="relative w-12">
            <sw-switch v-model="formData.allow_pbx_packages_update" class="absolute" style="top: -20px" />
          </div>

          <div class="ml-4">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('customers.allow_pbx_packages_updates') }}
            </p>
          </div>
        </div>

        <h6 class="col-span-5 sw-section-title lg:col-span-1">
          {{ $t('customers.invoice_pdf_format') }}
        </h6>
        <hr />

        <!-- pbx package -->
        <div class="flex my-8 mb-4">
          <div class="relative w-12">
            <sw-switch v-model="formData.allow_pbxpackages" class="absolute" style="top: -20px" />
          </div>

          <div class="ml-4">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('customers.allow_pbxpackage') }}
            </p>
          </div>
        </div>

        <!-- items -->
        <div class="flex my-8 mb-4">
          <div class="relative w-12">
            <sw-switch v-model="formData.allow_items" class="absolute" style="top: -20px" />
          </div>

          <div class="ml-4">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('customers.allow_items') }}
            </p>
          </div>
        </div>

        <!-- extensions -->
        <div class="flex my-8 mb-4">
          <div class="relative w-12">
            <sw-switch v-model="formData.allow_extensions" class="absolute" style="top: -20px" />
          </div>

          <div class="ml-4">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('customers.allow_extensions') }}
            </p>
          </div>
        </div>

        <!-- DID -->
        <div class="flex my-8 mb-4">
          <div class="relative w-12">
            <sw-switch v-model="formData.allow_did" class="absolute" style="top: -20px" />
          </div>

          <div class="ml-4">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('customers.allow_did') }}
            </p>
          </div>
        </div>

        <!-- aditional charges -->
        <div class="flex my-8 mb-4">
          <div class="relative w-12">
            <sw-switch v-model="formData.allow_aditionalcharges" class="absolute" style="top: -20px" />
          </div>

          <div class="ml-4">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('customers.allow_aditionalcharges') }}
            </p>
          </div>
        </div>

        <!-- usge summary -->
        <div class="flex my-8 mb-4">
          <div class="relative w-12">
            <sw-switch v-model="formData.allow_usagesummary" class="absolute" style="top: -20px" />
          </div>

          <div class="ml-4">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('customers.allow_usagesummary') }}
            </p>
          </div>
        </div>

        <!-- Allow custom app rate  -->
        <div class="flex my-8 mb-4">
          <div class="relative w-12">
            <sw-switch v-model="formData.allow_customapp" class="absolute" style="top: -20px" />
          </div>

          <div class="ml-4">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('customers.allow_customapprate') }}
            </p>
          </div>
        </div>

        <hr />

        <!-- allow discount -->
        <div class="flex my-8 mb-4">
          <div class="relative w-12">
            <sw-switch v-model="formData.allow_discount" class="absolute" style="top: -20px"
              @change="setDiscountParams" />
          </div>

          <div class="ml-4">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('customers.allow_discount') }}
            </p>
          </div>
        </div>

        <sw-input-group v-if="formData.allow_discount" :label="$tc('customers.select_time_period', 2)" class="mb-4">
          <sw-select v-model="formData.time_period" :options="time_period" :searchable="true" :show-labels="false"
            :placeholder="$t('customers.select_time_period')" class="mt-2" label="name" />
        </sw-input-group>

        <span v-if="formData.allow_discount">
          <div v-if="formData.time_period && formData.time_period.value == 'D'"
            class="relative grid grid-flow-col grid-rows">
            <sw-input-group :label="$t('general.from')" class="mt-2">
              <base-date-picker v-model="formData.discount_start_date" :calendar-button="true"
                calendar-button-icon="calendar" />
            </sw-input-group>

            <div class="
                hidden
                w-8
                h-0
                ml-8
                border border-gray-400 border-solid
                xl:block
              " style="margin-top: 3.5rem" />

            <sw-input-group :label="$t('general.to')" class="mt-2">
              <base-date-picker v-model="formData.discount_end_date" :calendar-button="true"
                calendar-button-icon="calendar" />
            </sw-input-group>
          </div>

          <div v-if="formData.time_period && formData.time_period.value == 'T'"
            class="relative grid grid-flow-col grid-rows">
            <sw-input-group>
              <sw-input v-model="formData.time_period_value" :invalid="$v.formData.time_period_value.$error"
                :placeholder="$t('customers.time_period_value')" class="rounded-tr-sm rounded-br-sm" label="name"
                @input="$v.formData.time_period_value.$touch()" />
            </sw-input-group>

            <div class="
                hidden
                w-8
                h-0
                ml-8
                border border-gray-400 border-solid
                xl:block
              " style="margin-top: 1.5rem" />

            <sw-input-group>
              <sw-select v-model="formData.time_period_type" :options="time_period_type" :searchable="true"
                :show-labels="false" :placeholder="$t('customers.time_period_type')"
                class="mt-2 border-r-0 rounded-tr-sm rounded-br-sm" label="name" />
            </sw-input-group>
          </div>
        </span>

        <sw-button :loading="isLoading" variant="primary" class="mt-4 pull-right" @click="nextPage">
          {{ $t('general.continue') }}
          <arrow-right-icon class="h-5 ml-2 -mr-1" />
        </sw-button>
      </div>
    </div>
  </sw-wizard-step>
</template>

<script>
import {
  ArrowRightIcon,
  ArrowLeftIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
const { required, numeric, requiredIf } = require('vuelidate/lib/validators')

export default {
  components: {
    ArrowRightIcon,
    ArrowLeftIcon,
    XCircleIcon,
  },
  data() {
    return {
      taxPerItem: 'NO',
      isLoading: false,
      isRequestOnGoing: false,
      addressOptions: [],
      formData: {
        main_update: false,
        allow_pbx_packages_update: false,
        customer_id: '',
        package: { pbx_package_name: 'None' },
        status: {
          name: 'Active',
          value: 'A',
        },
        address: {},
        custom_app_rate: {},
        date_begin: new Date(),
        discount_type: { name: 'General', value: 'G' },
        tax_type: {},
        term: { name: 'Monthly', value: 'monthly' },
        tenant: { name: 'None' },
        pbx_tenant_id: '',
        tenant_api_id: '',
        custom_app_rate_id: '',
        time_period: { name: 'Date', value: 'D' },
        time_period_value: 0,
        time_period_type: { name: 'Days', value: 'D' },
        discount_start_date: '',
        discount_end_date: '',
        renewal_date: '',
        allow_pbxpackages: true,
        allow_items: true,
        allow_extensions: true,
        allow_did: true,
        allow_aditionalcharges: true,
        allow_usagesummary: true,
        allow_customapp: true,
        suspension_type: 'T',
        suspension_type_op: {
          name: 'Tenant',
          value: 'T',
        },
        custom_destination_groups: [],
      },
      //
      prefixrate_groups_id: [],
      prefixrate_groups_outbound_id: [],
      //
      selectPrefix_rate_groups: { name: 'None' },
      selectPrefix_rate_groups_outbound: { name: 'None', value: '' },
      selectCustom_app_rate: { name: 'None', value: '' },
      packageInfo: {
        serverName: null,
        didName: null,
        extName: null,
        call_ratings: null,
      },
      tenant: [],
      showMessageTenants: false,
      groups: [],
      prefixrateGroups: [],
      prefixrateGroupsOutbound: [],
      prefixrateGroupsInbound: [],
      customAppRate: [],
      time_period: [
        { name: 'Date', value: 'D' },
        { name: 'Time period', value: 'T' },
      ],
      time_period_type: [
        { name: 'Days', value: 'D' },
        { name: 'Weeks', value: 'W' },
        { name: 'Months', value: 'M' },
        { name: 'Years', value: 'Y' },
      ],
      term: [
        { name: 'Daily', value: 'daily' },
        { name: 'Weekly', value: 'weekly' },
        { name: 'Monthly', value: 'monthly' },
        { name: 'Bimonthly', value: 'bimonthly' },
        { name: 'Quarterly', value: 'quarterly' },
        { name: 'Biannual', value: 'biannual' },
        { name: 'Yearly', value: 'yearly' },
      ],
      pbx_tenant: {},
      discount_type: [
        { name: 'General', value: 'G' },
        { name: 'By item', value: 'I' },
      ],
      tax_type: [
        { name: 'General', value: 'G' },
        { name: 'By item', value: 'I' },
      ],
      discount_term_type: [
        { name: 'Between dates', value: 'D' },
        { name: 'Time units', value: 'U' },
      ],

      SuspensionTypeOptions: [
        { name: 'Tenant', value: 'T' },
        { name: 'Extension', value: 'E' },
      ],

      showDates: true,
      error_renewal_date: false,
      error_activation_date: false,
    }
  },

  computed: {
    ...mapGetters('customer', ['CorePbxServicesParameters', 'pbxTenantSaved']),
    ...mapGetters('company', {carbonFormat: 'getCarbonDateFormat',momentFormat: 'getMomentDateFormat',}),

    packageError() {
      if (!this.$v.formData.package.$error) {
        return ''
      }
      if (!this.$v.formData.package.id.required) {
        return this.$t('validation.required')
      }
    },
    dateBeginError() {
      if (!this.$v.formData.date_begin.$error) {
        return ''
      }
      if (!this.$v.formData.date_begin.required) {
        return this.$t('validation.required')
      }
    },
    tenatError() {
      if (!this.$v.formData.tenant.$error) {
        return ''
      }
      if (!this.$v.formData.tenant.required) {
        return this.$t('validation.required')
      }
    },
    isPending() {
      return this.formData.status.value === 'P'
    },
    isTenant() {
      return this.formData.package.pbx_package_name != ''
    },
    isEdit() {
      if (this.$route.name === 'pbxServices.edit') {
        return true
      }
      return false
    },
    statusOptions() {
      const statusOptionsCreate = [
        { name: 'Active', value: 'A' },
        { name: 'Pending', value: 'P' },
      ]
      const statusOptionsEdit = [
        { name: 'Active', value: 'A' },
        { name: 'Pending', value: 'P' },
        { name: 'Suspend', value: 'S' },
        { name: 'Cancelled', value: 'C' },
      ]
      return this.isEdit ? statusOptionsEdit : statusOptionsCreate
    },
  },
  validations: {
    formData: {
      package: {
        id: {
          numeric,
          required,
        },
      },
      time_period_value: {
        numeric,
      },
      date_begin: {
        required,
      },
      tenant: {
        requiredTenantId: function (value) {
        
          if (value.code != undefined && value.code != null) {
            return true
          }
          return false
        },
      },
      prefix_rate_groups: {
        name: '',
        value: '',
      },
      prefix_rate_groups_outbound: {
        name: '',
        value: '',
      },
    },
  },
  created() {
    this.loadData()
  },
  mounted() {
    this.configCalendar({
      minDate: new Date().fp_incr(-90),
      // altFormat: 'd/m/Y',
      allowInput: false,
    })
    this.indexCustomerAddresses()
  },
  methods: {
    ...mapActions('pbxService', ['fetchPbxService']),
    ...mapActions('pbx', [
      'fetchPackagesCreatePbx',
      'fetchTenant',
      'fetchPackageInfo',
    ]),
    ...mapActions('customer', [
      'setCorePbxServicesParameters',
      'setCorePBXServices',
      'setPbxTenant',
      'getPbxTenant',
      'addPbxTenant',
      'fetchPrefixrateGroups',
      'getDaysToRenewal',
    ]),
    ...mapActions('customAppRate', ['fetchCustomAppRate']),
    ...mapActions('customerAddress', [
      'fetchCustomerAddresses',
    ]),
    ...mapActions('company', ['fetchCompanySettings']),

    async loadData() {
      this.isRequestOnGoing = true
      let response = await this.fetchPackagesCreatePbx(this.$route.params.id)
      this.groups = response.data.pbxPackages
      this.isRequestOnGoing = false
      let responsePrefix = await this.fetchPrefixrateGroups()
      this.prefixrateGroups = responsePrefix.data.response
      this.prefixrateGroupsOutbound = this.prefixrateGroups.filter(
        (prefix) => prefix.type === 'Outbound'
      )
      this.prefixrateGroupsInbound = this.prefixrateGroups.filter(
        (prefix) => prefix.type === 'Inbound'
      )

      let companySettings = await this.fetchCompanySettings([
        'tax_per_item',
      ])

      if (companySettings.data) {
        this.taxPerItem = companySettings.data.tax_per_item
      }

      let responseCustomApp = await this.fetchCustomAppRate({ limit: 10000000 })
      this.customAppRate = responseCustomApp.data.customAppRates.data

      if (this.isEdit) {

        this.showDates = false
        let res = await this.fetchPbxService(
          this.$route.params.customer_pbx_service_id
        )

        this.prefixrate_groups_id =
          res.data.response.custom_destination_groups.filter(group => group.type == "Inbound")

        this.prefixrate_groups_outbound_id =
          res.data.response.custom_destination_groups.filter(group => group.type == "Outbound")

        this.formData = { ...res.data.response.pbx_service }
        this.formData.discount_start_date = res.data.response.pbx_service.date_from
        this.formData.discount_end_date = res.data.response.pbx_service.date_to
        this.formData.pbx_services_id =
          this.$route.params.customer_pbx_service_id
        let service = JSON.parse(JSON.stringify(this.formData))
        let discount = {}

        this.formData.time_period_value = service.time_period
        // validate time_period
        if (service.discount_term_type === 'D') {
          this.formData.discount_term_type = 'D'
          this.formData.time_period = { name: 'Date', value: 'D' }
        } else {
          this.formData.time_period = { name: 'Time period', value: 'T' }
        }
        this.setTimePeriodType(service.time_period_type) // set time period type
        this.formData.package = JSON.parse(
          JSON.stringify(res.data.response.pbx_service.pbx_package)
        )
        this.formData.package.custom_did_groups =
          res.data.response.pbx_service.custom_did_groups
        this.setTenant() // set tenaant
        this.formData.status = this.statusOptions.find(
          (_status) => service.status === _status.value
        )
        this.formData.term = this.term.find(
          (_term) => service.term === _term.value
        )
        this.formData.tax_type = this.tax_type.find(
          (option) => option.value === service.apply_tax_type
        )

        this.formData.discount_type = this.discount_type.find(
          (_type) => service.discount_by === _type.value
        )
        // validte prefixrate_groups
        if (service.prefixrate_groups_id) {
          let prefix_rate_groups = this.prefixrateGroups.find(
            (prefix) => service.prefixrate_groups_id == prefix.id
          )
          const { id, name } = JSON.parse(JSON.stringify(prefix_rate_groups))
          this.selectPrefix_rate_groups = {
            name: name,
            value: id,
          }
        }
        if (service.prefixrate_groups_outbound_id) {
          const prefix_rate_groups_outbound = this.prefixrateGroups.find(
            (prefix) => service.prefixrate_groups_outbound_id == prefix.id
          )
          const { name, id } = JSON.parse(
            JSON.stringify(prefix_rate_groups_outbound)
          )
          this.selectPrefix_rate_groups_outbound = {
            name: name,
            value: id,
          }
        }
        //custom app rate

        if (service.custom_app_rate_id) {
          const prefix_rate_groups_outbound = this.customAppRate.find(
            (prefix) => service.custom_app_rate_id == prefix.id
          )
          const customAppRateCopy = JSON.parse(
            JSON.stringify(prefix_rate_groups_outbound)
          )
          this.selectCustom_app_rate = {
            name: customAppRateCopy.name,
            value: customAppRateCopy.id,
          }
          this.formData.custom_app_rate = customAppRateCopy
        }

        if (service.auto_suspension) {
          if (service.suspension_type == 'T') {
            this.formData.suspension_type_op = {
              name: 'Tenant',
              value: 'T',
            }
          } else {
            this.formData.suspension_type_op = {
              name: 'Extension',
              value: 'E',
            }
          }
        }
        if (service.address && service.address.id) {
          this.formData.address = {
            id: service.address.id,
            address_street_1: service.address.address_street_1,
            state: service.address.state
          }
        }
        // validate discount
        if (service.allow_discount) {
          this.formData.discount_term_type = this.discount_term_type.find(
            (_type) => service.discount_term_type === _type.value
          )
          if (service.discount_term_type === 'U') {
            this.formData.discount_term = this.discount_term.find(
              (_term) => discount.term === _term.value
            )
            this.formData.discount_time_units = discount.time_unit_number
          } else {
            this.formData.discount_start_date = moment(service.date_from).format('YYYY-MM-DD')

            this.formData.discount_end_date = moment(service.date_to).format('YYYY-MM-DD')
          }
        }

        // validate dates
        this.formData.date_begin = moment(service.date_begin).format('YYYY-MM-DD')
        this.formData.renewal_date = moment(service.renewal_date).format('YYYY-MM-DD')
        this.showDates = true
        this.formData.allow_discount = service.allow_discount ? true : false
      }
    },
    setBeginDate({ value }) {
      if (value == 'P') {
        // this.formData.date_begin = new Date().fp_incr(1)
        this.configCalendar({
          minDate: new Date().fp_incr(1),
          // altFormat: 'm-d-Y',
          allowInput: true,
        })
      } else if (value == 'A') {
        this.configCalendar({
          minDate: new Date().fp_incr(-90),
          // altFormat: 'm/d/Y',
          allowInput: false,
        })
      }
    },
    configCalendar({ minDate, altFormat = carbonFormat, allowInput }) {
      if (!this.isEdit) {
        this.$refs.dateBegin.$refs.BaseDatepicker.$refs.BaseDatepicker.config.minDate = minDate
        this.$refs.dateBegin.$refs.BaseDatepicker.$refs.BaseDatepicker.config.altFormat = altFormat
      }
    },

    setTimePeriodType(time_period_type) {
      switch (time_period_type) {
        case 'Weeks':
          this.formData.time_period_type = { name: 'Weeks', value: 'W' }
          break

        case 'Months':
          this.formData.time_period_type = { name: 'Months', value: 'M' }
          break

        case 'Years':
          this.formData.time_period_type = { name: 'Years', value: 'Y' }
          break

        default:
          this.formData.time_period_type = { name: 'Days', value: 'D' }
          break
      }
    },

    sleep(ms) {
      return new Promise((resolve) => {
        setTimeout(resolve, ms)
      })
    },

    selectCustomAppRateMetho(customApp) {
      this.formData.custom_app_rate = customApp
    },

    async fetchPbxTenant(tenant) {
      let params = {
        code: tenant.code,
        tenant_id: tenant.tenant_id || tenant.id,
      }

      let response = await this.getPbxTenant(params)
      return response
    },

    async setTenant() {
      this.tenant = []
      await this.sleep(500)

      if (this.formData.package.id != null) {
        if (this.isTenant) {
          this.isRequestOnGoing = true
          let response = await this.fetchTenant(this.formData.package.id)
          if (response.data.error != undefined && response.data.error) {
            window.toastr['error'](response.data.message)
          }
          this.isRequestOnGoing = false
          for (const property in response.data.tenantList) {
            this.tenant.push({
              tenant_id: property,
              package_id: response.data.tenantList[property].package_id,
              package: response.data.tenantList[property].package,
              name: response.data.tenantList[property].name,
              ext_length: response.data.tenantList[property].ext_length,
              country_id: response.data.tenantList[property].country_id,
              country_code: response.data.tenantList[property].country_code,
              code: response.data.tenantList[property].tenantcode,
            })
          }

          if (this.tenant.length > 0) {
            this.showMessageTenants = false
          } else {
            this.showMessageTenants = true
          }
        }

        if (!this.isEdit) {
          if (this.formData.package.apply_tax_type == 'item') {
            this.formData.tax_type = { name: 'By item', value: 'I' }
          } else {
            this.formData.tax_type = { name: 'General', value: 'G' }
          }
        }

        // varficar auto suspension en el paquete seleccionado
        if (this.formData.package.automatic_suspension) {
          this.formData.auto_suspension = true

          if (this.formData.package.suspension_type == 'T') {
            this.formData.suspension_type_op = {
              name: 'Tenant',
              value: 'T',
            }
          } else {
            this.formData.suspension_type_op = {
              name: 'Extension',
              value: 'E',
            }
          }
        }

        if (!this.isEdit && this.formData.package.update_child_services) {
          this.formData.allow_pbx_packages_update = true
        }

        //
        // verificar discount
        if (!this.isEdit && this.formData.package.package_discount) {
          this.showDates = false
          this.formData.allow_discount = true
          if (this.formData.package.discount_term_type === 'D') {
            this.formData.discount_start_date =
              this.formData.package.discount_start_date
            this.formData.discount_end_date =
              this.formData.package.discount_end_date
          } else {
            this.formData.time_period = { name: 'Time period', value: 'T' }
            this.formData.time_period_value =
              this.formData.package.discount_time_units
            this.setTimePeriodType(this.formData.package.discount_term)
          }
          this.showDates = true
        }
        // verificar prefixrate_group
        if (!this.isEdit) {
          if (!this.isEdit && this.formData.package.prefixrate_groups_id) {
            const { name, id } = JSON.parse(
              JSON.stringify(this.formData.package.prefixrate_group)
            )
            this.selectPrefix_rate_groups = {
              name,
              value: id,
            }
          } else {
            this.selectPrefix_rate_groups = {
              name: 'None',
              value: '',
            }
          }
        }

        // verificar prefixrate_group_outbound
        if (!this.isEdit) {
          if (
            !this.isEdit &&
            this.formData.package.prefixrate_groups_outbound_id
          ) {
            const { name, id } = JSON.parse(
              JSON.stringify(this.formData.package.prefixrate_group_outbound)
            )
            this.selectPrefix_rate_groups_outbound = {
              name: name,
              value: id,
            }
          } else {
            this.selectPrefix_rate_groups_outbound = {
              name: 'None',
              value: '',
            }
          }
        }

        // verificar  custom app rate
        if (!this.isEdit) {
          if (!this.isEdit && this.formData.package.custom_app_rate_id) {
            const customAppRate = this.customAppRate.find(
              (prefix) => this.formData.package.custom_app_rate_id == prefix.id
            )

            const customAppRateCopy = JSON.parse(JSON.stringify(customAppRate))
            this.selectCustom_app_rate = {
              name: customAppRateCopy.name,
              value: customAppRateCopy.id,
            }
            this.formData.custom_app_rate = customAppRateCopy
          } else {
            this.selectCustom_app_rate = {
              name: 'None',
              value: '',
            }
          }

          this.prefixrate_groups_id =
            this.formData.package.custom_destination_groups.filter(group => group.type == "Inbound")

          this.prefixrate_groups_outbound_id =
            this.formData.package.custom_destination_groups.filter(group => group.type == "Outbound")
        }

        let responseag = await this.fetchPackageInfo(this.formData.package.id)
        this.packageInfo.serverName = responseag.data.pbxPackage.server_label
        this.packageInfo.didName = responseag.data.pbxPackage.did_name
        this.packageInfo.extName = responseag.data.pbxPackage.extension_name
        this.packageInfo.call_ratings = responseag.data.pbxPackage.call_ratings
        this.packageInfo.pbx_server_id =
          responseag.data.pbxPackage.pbx_server_id
      }
    },
    clearPrefixRateGroup() {
      this.selectPrefix_rate_groups = { name: 'None' }
    },
    setTaxType(apply_tax_type = 'general') {
      switch (apply_tax_type) {
        case 'none':
          this.formData.tax_type = { name: 'General', value: 'G' }
          break
        case 'general':
          this.formData.tax_type = { name: 'General', value: 'G' }
          break
        case 'item':
          this.formData.tax_type = { name: 'By item', value: 'I' }
          break
        default:
          break
      }
    },
    async indexCustomerAddresses() {
      const { data } = await this.fetchCustomerAddresses({
        customer_id: this.$route.params.id
      })
      this.addressOptions = data.customerAddress.data.map(address => ({
        id: address.id,
        address_street_1: address.address_street_1,
        state: address.state
      }))
    },

    setDiscountParams() {
      this.formData.time_period = {
        name: 'None',
        value: 'N',
      }
      this.formData.discount_start_date = ''
      this.formData.discount_end_date = ''
    },

    async nextPage() {
      this.isLoading = true
      // this.$emit('next')
      this.isLoading = false
      this.nextValid()
    },

    // next screen
    async nextValid() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      // validar
      if (
        !this.isEdit &&
        (this.$v.formData.$invalid ||
          this.formData.tenant.name === 'None' ||
          this.error_activation_date)
      ) {
        return true
      }
      // consultar tenant
      // let tenant_bd = await this.fetchPbxTenant(this.formData.tenant);
      let tenant_bd_id = this.formData.tenant.id
      // si no existe, insertarlo en bd
      if (!this.isEdit) {
        // if (Object.entries(tenant_bd).length === 0) {
        let params = {
          name: this.formData.tenant.name,
          code: this.formData.tenant.code,
          details: this.formData.tenant,
          pbx_server_id: this.formData.package.pbx_server_id,
        }

        await this.addPbxTenant(params)
        this.pbx_tenant = this.pbxTenantSaved
        tenant_bd_id = this.pbx_tenant.id
        this.formData.pbx_tenant_code = JSON.parse(
          JSON.stringify(this.pbx_tenant.details)
        ).tenant_id
        // } else {
        //   this.setPbxTenant(tenant_bd)
        //   tenant_bd_id = tenant_bd[0].id
        // }
      }

      this.isLoading = true
      this.formData.customer_id = this.$route.params.id
      this.formData.pbx_package_id = this.formData.package.id
      this.formData.status = this.formData.status.value
      this.formData.term = this.formData.term.value
      let time_period = JSON.parse(
        JSON.stringify(this.formData.time_period.value)
      )
      this.formData.time_period = this.formData.time_period_value
      this.formData.time_period_value = time_period
      this.formData.time_period_type = this.formData.time_period_type
        ? this.formData.time_period_type.name
        : null
      this.formData.pbx_tenant_id = tenant_bd_id
      this.formData.tenant_api_id = this.isEdit
        ? this.formData.tenant.details.tenant_id
        : this.formData.tenant.tenant_id
      this.formData.allow_discount = this.formData.allow_discount || false
      this.formData.auto_suspension = this.formData.auto_suspension || false
      this.formData.allow_pbx_packages_update = this.formData.allow_pbx_packages_update || false
      this.formData.main_update = this.formData.main_update || false

      if (this.formData.auto_suspension == true) {
        this.formData.suspension_type = this.formData.suspension_type_op.value
      }

      /*
      if (this.selectPrefix_rate_groups.name == 'None') {
        this.formData.prefixrate_groups_id = null
      } else {
        this.formData.prefixrate_groups_id =
          this.selectPrefix_rate_groups.id ||
          this.selectPrefix_rate_groups.value
      }
      */

      if (this.selectCustom_app_rate.name == 'None') {
        this.formData.custom_app_rate_id = null
      } else {
        this.formData.custom_app_rate_id = this.selectCustom_app_rate.id || this.selectCustom_app_rate.value
      }

      /*
      if (this.selectPrefix_rate_groups_outbound.name == 'None') {
        this.formData.prefixrate_groups_outbound_id = null
      } else {
        this.formData.prefixrate_groups_outbound_id = this
          .selectPrefix_rate_groups_outbound
          ? this.selectPrefix_rate_groups_outbound.value
            ? this.selectPrefix_rate_groups_outbound.value
            : this.selectPrefix_rate_groups_outbound.id
          : this.selectPrefix_rate_groups_outbound.id
      }
      */
      this.formData.addresses_id = this.formData.address?.id || null

      this.formData.custom_destination_groups = [...this.prefixrate_groups_id,
      ...this.prefixrate_groups_outbound_id]

      let data = { parameters: this.formData }

      await this.setCorePbxServicesParameters(data)

      if (this.isEdit) {
        const dataRenewal = {
          term: this.formData.term,
          activation_date: this.formData.date_begin,
          renewal_date: this.formData.renewal_date,
        }
        await this.getDaysToRenewal(dataRenewal)
      }



      this.$emit('next', 2)
      this.isLoading = false
    },

    validateRenewalDate() {
      let today = new Date()
      let renewal = moment(this.formData.renewal_date)
      let message = 'The renewal date cannot be less than today'
      // comparar fechas
      if (renewal <= today) {
        window.toastr['error'](message);
        // this.showDates = false
        setTimeout(() => {
          this.formData.renewal_date = moment(today).add(1, 'days').format('YYYY-MM-DD')
          const dateRenew = this.$refs.renewalDate
          // evento para que vuelva a salir el calendario y se pueda seleccionar la fecha          
        }, 1000)
        // this.error_renewal_date = this.showDates = true
      } else {
        // this.error_renewal_date = false
      }
    },
  },


}
</script>

<style>
.multiselect__option>span {
  padding-left: 1rem;
}

.multiselect__option--group.multiselect__option>span {
  padding-left: 0;
  font-weight: bold;
}
</style>