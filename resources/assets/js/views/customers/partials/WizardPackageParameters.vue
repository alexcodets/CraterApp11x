<template>
  <sw-wizard-step :title="$t('customers.package_parameters')">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <div class="grid grid-cols-12 mt-5">
      <div class="col-span-12">
        <sw-input-group
          :label="$tc('packages.package_name', 1)"
          class="mb-4"
          :error="packageError"
          required
        >
          <sw-select
            :invalid="$v.formData.package.$error"
            v-model="formData.package"
            :options="groups"
            :group-select="false"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('customers.select_a_package')"
            :allow-empty="false"
            group-values="options"
            group-label="label"
            class="mt-2"
            track-by="package_name"
            label="package_name"
            @input="$v.formData.package.$touch()"
            @select="(item) => loadPackageData(item)"
            :disabled="isEdit"
          />
        </sw-input-group>

        <sw-input-group :label="$tc('customers.status', 2)" class="mb-4">
          <sw-select
            v-model="formData.status"
            :options="status"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('customers.select_a_status')"
            class="mt-2"
            label="name"
            @select="setBeginDate"
          />
        </sw-input-group>

        <sw-input-group :label="$t('customers.term')" class="mb-4">
          <sw-select
            v-model="formData.term"
            :options="term"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('customers.select_a_term')"
            class="mt-2"
            label="name"
          />
        </sw-input-group>

        <div>
          <sw-input-group
            :label="$t('customers.date_act')"
            class="mb-4"
            :error="dateBeginError"
            required
          >
            <base-date-picker
              v-model="formData.date_begin"
              :invalid="$v.formData.date_begin.$error"
              :calendar-button="true"
              calendar-button-icon="calendar"
              :disabled="isEdit"
              ref="dateBegin"
            />

            <!-- warning message -->
            <p v-if="error_activation_date">
              <small style="color: red"
                > {{ $t('general.warning_service2') }}</small
              >
            </p>
          </sw-input-group>

          <!-- renew  al date -->
          <sw-input-group
            :label="$t('customers.renewal_date')"
            class="mb-4"
            required
            v-if="isEdit"
          >
            <base-date-picker
              v-if="!formData.renewal_date_switch"
              v-model="formData.renewal_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
              :disabled="isEdit"
              ref="RenewalDate"
            />
            
          </sw-input-group>
        </div>
        
        <!-- New Switch Date  -->

        <div v-if="isEdit">
          <div class="flex mt-5 mb-5" >
          <div class="relative w-12">
            <sw-switch
              v-model="formData.renewal_date_switch"
              class="absolute"
              style="top: -20px"
              @change="setDiscountParamsEdit"
            />
          </div>

          <div class="ml-4">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('general.warning_service3') }}
            </p>
          </div>
        </div>

        <div v-if="formData.renewal_date_switch">
          <sw-input-group
            class="mb-4"
            v-if="isEdit"
            :error="newEditRenewalDateError"
            required
          >
            <base-date-picker
              v-model="formData.new_edit_renewal_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
              ref="RenewalDateSwitch"
              :invalid="$v.formData.new_edit_renewal_date.$error"
            />
            
          </sw-input-group>
        </div>
        </div>
        
        <!-- New Switch Date  -->

        <sw-input-group :label="$t('customers.tax_type')" class="mb-4">
          <sw-select
            v-model="formData.tax_type"
            :options="discount_type"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('customers.select_a_tax_type')"
            class="mt-2"
            label="name"
            :disabled="!tax_per_item_yes"
          />
          <p class="mt-2 ml-1 text-sm leading-snug text-gray-500" style="max-width: 900px">
            {{ $t('general.warning_service') }}
        </p>
        </sw-input-group>

        <sw-input-group :label="$t('customers.address')" class="mb-4 relative">
          <sw-select
            v-model="formData.address"
            :options="addressOptions"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('customers.address')"
            class="mt-2"
            label="name"
          >
            <template slot="singleLabel" slot-scope="option">
              <div class="flex items-center">
                <div v-if="option.option.state" class="text-sm">{{ option.option.state.name + " - " +  option.option.address_street_1 }}</div>
              </div>
            </template>
            <template slot="option" slot-scope="option">
              <div class="flex items-center">
                <div v-if="option.option.state" class="text-sm">{{ option.option.state.name + " - " +  option.option.address_street_1 }}</div>
              </div>
            </template>
          </sw-select>
          <x-circle-icon
            @click="formData.address = {}"
            v-if="formData.address && formData.address.state"
            style="top: 42px; right: 28px"
            class="h-5 absolute text-gray-400 cursor-pointer"
          />
        </sw-input-group>

        <div class="flex my-8 mb-4">
          <div class="relative w-12">
            <sw-switch
              v-model="formData.allow_discount"
              class="absolute"
              style="top: -20px"
              @change="setDiscountParams"
            />
          </div>

          <div class="ml-4">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('customers.allow_discount') }}
            </p>
          </div>
        </div>

        <div v-if="formData.allow_discount">
          <sw-input-group
            :label="$tc('customers.select_discount', 2)"
            class="mb-4"
          >
            <sw-select
              v-model="formData.discount_type"
              :options="discount_type"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('customers.select_a_discount')"
              class="mt-2"
              label="name"
              :disabled="true"
            />
          </sw-input-group>

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
            />
          </sw-input-group>

          <div
            v-if="discountBetweenDates"
            class="relative grid grid-flow-col grid-rows"
          >
            <sw-input-group :label="$t('general.from')" class="mt-2" :error="starDateError">
              <base-date-picker
                :invalid="$v.formData.discount_start_date.$error"
                v-model="formData.discount_start_date"
                :calendar-button="true"
                calendar-button-icon="calendar"
                ref="dateFrom"
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

            <sw-input-group :label="$t('general.to')" class="mt-2" :error="endDateError">
              <base-date-picker
                :invalid="$v.formData.discount_end_date.$error"
                v-model="formData.discount_end_date"
                :calendar-button="true"
                calendar-button-icon="calendar"
                ref="dateTo"
              />
            </sw-input-group>
          </div>

          <sw-input-group
            v-if="!discountBetweenDates"
            :label="$t('customers.time_unit_number')"
            class="mt-2"
          >
            <div class="flex"  role="group">
              <sw-input
                v-model="formData.discount_time_units"
                :invalid="$v.formData.discount_time_units.$error"
                class="border-r-0 rounded-tr-sm rounded-br-sm"
                @input="$v.formData.discount_time_units.$touch()"
              />
              <sw-select
                v-model="formData.discount_term"
                :options="discount_term"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('customers.select_a_term')"
                label="name"
                style= "margin-left:1em;"
              />
            </div>
          </sw-input-group>
        </div>

        <div class="flex my-8 mb-4">
          <div class="relative w-12">
            <sw-switch
              v-model="formData.service_auto_suspension"
              class="absolute"
              style="top: -20px"
            />
          </div>

          <div class="ml-4">
            <p class="p-0 mb-1 text-base leading-snug text-black box-title">
              {{ $t('services.auto_suspension') }}
            </p>
          </div>
        </div>

        <sw-button
          :loading="isLoading"
          variant="primary"
          class="mt-4 pull-right"
          @click="next"
        >
          {{ $t('general.continue') }}
          <arrow-right-icon class="h-5 ml-2 -mr-1" />
        </sw-button>
      </div>
    </div>
  </sw-wizard-step>
</template>

<script>
import { ArrowRightIcon, ArrowLeftIcon, XCircleIcon } from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'

const { required, between } = require('vuelidate/lib/validators')

export default {
  components: {
    ArrowRightIcon,
    ArrowLeftIcon,
    XCircleIcon,
  },
  data() {
    return {
      addressOptions: [],
      isLoading: false,
      isRequestOnGoing: false,
      service: null,
      package: '',
      formData: {
        address: {},
        customer_id: '',
        package: '',
        status: { name: 'Active', value: 'A' },
        date_begin: new Date(),
        renewal_date: '',
        discount_type: { name: 'General', value: 'G' },
        discount_term_type: { name: 'Between dates', value: 'D' },
        discount_term: { name: 'Days', value: 'days' },
        discount_time_units: 0,
        allow_discount: false,
        discount_start_date: null,
        discount_end_date: null,
        tax_type: { name: 'General', value: 'G' },
        term: { name: 'Monthly', value: 'monthly' },
        service_auto_suspension: false,
        renewal_date_switch: false,
        new_edit_renewal_date: null
      },
      groups: [],
      status: [
        { name: 'Active', value: 'A' },
        { name: 'Pending', value: 'P' },
      ],
      discount_type: [
        { name: 'General', value: 'G' },
        { name: 'Item', value: 'I' },
      ],
      config_calendar: {
        dateFormat: 'Y-m-d',
        minDate: 'today',
        maxDate: new Date().fp_incr(14),
      },
      term: [
        { name: 'Daily', value: 'daily' },
        { name: 'Weekly', value: 'weekly' },
        { name: 'Monthly', value: 'monthly' },
        { name: 'Bimonthly', value: 'bimonthly' },
        { name: 'Quarterly', value: 'quarterly' },
        { name: 'Biannual', value: 'biannual' },
        { name: 'Yearly', value: 'yearly' },
      ],
      discount_term_type: [
        { name: 'Between dates', value: 'D' },
        { name: 'Time units', value: 'U' },
      ],
      discount_term: [
        { name: 'Days', value: 'days' },
        { name: 'Weeks', value: 'weeks' },
        { name: 'Months', value: 'months' },
        { name: 'Years', value: 'years' },
      ],
      showDates: true,
      error_activation_date: false,
      tax_per_item_yes: false,
    }
  },
  validations: {
    formData: {
        package: {
          required,
        },
        discount_time_units: {
          between: between(1, 1000000),
        },
        date_begin: {
          required,
        },
        new_edit_renewal_date: {
          required,
        },
        discount_start_date: {
          required,
        },
        discount_end_date: {
          required,
      },
    }
  },
  computed: {
    ...mapGetters('customer', ['selectedViewCustomer', 'packageParameters']),
    ...mapGetters('pack', ['packagesByGroup']),
    ...mapGetters('company', ['defaultCurrency']),   
    ...mapGetters('company', {carbonFormat: 'getCarbonDateFormat', momentFormat: 'getMomentDateFormat',}),
    
    
    newEditRenewalDateError() {

        if (!this.$v.formData.new_edit_renewal_date.$error) {
        return ''
        }
        if (!this.$v.formData.new_edit_renewal_date.required) {
        return this.$tc('validation.required')
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

    getPackagesByGroup() {
      return this.packagesByGroup.map((group) => {
        return {
          ...group,
        }
      })
    },
    packageError() {
      if (!this.$v.formData.package.$error) {
        return ''
      }
      if (!this.$v.formData.package.required) {
        return this.$t('validation.required')
      }
    },
    isPending() {
      return this.formData.status.value === 'P'
    },
    isEdit() {
      if (this.$route.name === 'services.edit') {
        return true
      }
      return false
    },
    discountBetweenDates() {
      return this.formData.discount_term_type.value === 'D'
    },
    ///////
    starDateError() {
      if (!this.$v.formData.discount_start_date.$error) {
        return ''
      }
      if (!this.$v.formData.discount_start_date.required) {
        return this.$t('validation.required')
      }
    },
    endDateError() {
      if (!this.$v.formData.discount_end_date.$error) {
        return ''
      }
      if (!this.$v.formData.discount_end_date.required) {
        return this.$t('validation.required')
      }
    },
    ///////
    activationDate: {
      get: function () {
        return this.formData.date_begin
      },
      set: function (newValue) {
        this.formData.date_begin = newValue
      },
    },
  },
  created() {
    this.loadData()
    this.indexCustomerAddresses()
  },
  mounted() {
    this.configCalendar({
      minDate: new Date().fp_incr(-90),
      // altFormat: 'YY-MM-DD',
      allowInput: false,
    })
  },
  methods: {
    ...mapActions('pack', ['fetchPackage', 'fetchPackagesByGroup']),
    ...mapActions('customer', ['setPackageParameters']),
    ...mapActions('service', ['fetchService']),
    ...mapActions('company', ['fetchCompanySettings']),
    ...mapActions('customerAddress', ['fetchCustomerAddresses']),
    

    async loadData() {
      this.isRequestOnGoing = true
      let params = {
        customer_id: this.$route.params.id,
      }
      let response = await this.fetchPackagesByGroup(params)
  
      let groups = response.data.packagesByGroup

      for (let index in groups) {
        let options = []
        for (let group in groups[index]) {
          options.push({
            package_name: groups[index][group].package_name,
            value: groups[index][group].package_id,
          })
        }
        this.groups.push({
          label: index ? index : 'Without group',
          isDisable: true,
          options: options,
        })
      }
      if (Object.keys(this.packageParameters).length > 0) {
        // aca tenemos este problema cuando intentamos cargar una data existente
        // do not mutate vuex store state outside mutation handlers
        // this.formData = this.packageParameters.parameters
      }

      if (this.isEdit) {
        this.showDates = false
        let res = await this.fetchService(this.$route.params.customer_package_id)
        this.service = res.data.service
        let service = this.service
        let pkg = service.package
        let discount = service.discounts[0]

        this.formData.package = { package_name: pkg.name, value: pkg.id, package_number: pkg.package_number }
        this.formData.status = this.status.find(
          (_status) => service.status === _status.value
        )
        this.formData.term = this.term.find(
          (_term) => service.term === _term.value
        )
        this.formData.tax_type = this.discount_type.find(
          (_type) => service.tax_by === _type.value
        )
        this.formData.discount_type = this.discount_type.find(
          (_type) => service.discount_by === _type.value
        )
     
        if (service.allow_discount && discount) {
          this.formData.discount_term_type = this.discount_term_type.find(
            (_type) => discount.term_type === _type.value
          )
          if (discount.term_type === 'U') {
            this.formData.discount_term = this.discount_term.find(
              (_term) => discount.term === _term.value
            )
            this.formData.discount_time_units = discount.time_unit_number
          } else {
            this.formData.discount_start_date = moment(
              discount.start_date).format('YYYY-MM-DD')

            this.formData.discount_end_date = moment(
              discount.end_date).format('YYYY-MM-DD')
          }
        }

        this.formData.date_begin = moment(service.activation_date).format('YYYY-MM-DD')

        // <renowal date isEdit        

        this.formData.renewal_date = moment(service.renewal_date).format('YYYY-MM-DD');
        
        this.showDates = true
        this.formData.allow_discount = service.allow_discount
        this.formData.service_auto_suspension = service.service_auto_suspension 
        
       this.formData.address = this.addressOptions.find((element) => {
        return element.id === this.service.addresses_id 
        })

      } else {
        let settings = await this.fetchCompanySettings(['tax_per_item'])
         
        if (settings.data.tax_per_item === 'YES') {
          this.tax_per_item_yes = true
          this.formData.tax_type = { name: 'General', value: 'G' }          
        }else{
          this.formData.tax_type = { name: 'General', value: 'G' }
        }
        
      }
      this.isRequestOnGoing = false      
    },

    async indexCustomerAddresses(){
      const {data} = await this.fetchCustomerAddresses({
        customer_id: this.$route.params.id
      })

      this.addressOptions = data.customerAddress.data.map(address => ({
        id: address.id,
        address_street_1: address.address_street_1,
        state: address.state
      }))
    },

    async loadPackageData(item) {

      this.isRequestOnGoing = true   
      let response = await this.fetchPackage(item.value)
      this.package = response.data.response
      //this.formData.allow_discount = this.package.packages_discount

      switch (this.package.apply_tax_type) {
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
      this.isRequestOnGoing = false
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
    configCalendar({ minDate, altFormat = this.carbonFormat, allowInput }) {
      if (!this.isEdit) {        
        this.$refs.dateBegin.$refs.BaseDatepicker.$refs.BaseDatepicker.config.minDate =  minDate
        this.$refs.dateBegin.$refs.BaseDatepicker.$refs.BaseDatepicker.config.altFormat = altFormat
      }
    },
   
    configCalendarEdit({ minDate, altFormat  = this.carbonFormat, allowInput })
    {
      if(this.isEdit){   
        this.$refs.RenewalDateSwitch.$refs.BaseDatepicker.$refs.BaseDatepicker.config.minDate = minDate
        this.$refs.RenewalDateSwitch.$refs.BaseDatepicker.$refs.BaseDatepicker.config.altFormat = altFormat
      }
    },

    setDiscountParamsEdit()
    {
      this.renewal_date_switch = !this.renewal_date_switch;
      if(this.renewal_date_switch)
      {
        this.configCalendarEdit({
          minDate: new Date().fp_incr(1),
          // altFormat: 'Y-m-d',
          allowInput: true,
        })
      }
    },   

    configCalendarDTPFrom({ minDate, altFormat = this.carbonFormat, allowInput }) {
      if (!this.isEdit) {   
        this.$refs.dateFrom.$refs.BaseDatepicker.$refs.BaseDatepicker.config.minDate =  minDate
        this.$refs.dateFrom.$refs.BaseDatepicker.$refs.BaseDatepicker.config.altFormat = altFormat   
      }
    },

    configCalendarDTPTo({ minDate, altFormat = this.carbonFormat, allowInput }) {
      if (!this.isEdit) {
        this.$refs.dateTo.$refs.BaseDatepicker.$refs.BaseDatepicker.config.minDate =  minDate
        this.$refs.dateTo.$refs.BaseDatepicker.$refs.BaseDatepicker.config.altFormat = altFormat
      }
    },

    setDiscountParams() {

      this.configCalendarDTPFrom({
          minDate: new Date().fp_incr(0),
          // altFormat: 'Y-m-d',
          allowInput: true,
      })

      this.configCalendarDTPTo({
          minDate: new Date().fp_incr(32),
          // altFormat: 'Y-m-d',
          allowInput: true,
      })

      this.formData.discount_type = {
        name: 'General',
        value: 'G',
      }
      
      this.formData.discount_start_date = ''
      this.formData.discount_end_date = ''
      this.formData.discount_term_type = { name: 'Between dates', value: 'D' }
      this.formData.discount_term = { name: 'Days', value: 'days' }

      this.formData.discount_time_units = 1
    },

    async next() {
     
      //Convert Date (YYYY-MM-DD)

      let date_begin_convert = moment(this.formData.date_begin).format('YYYY-MM-DD')

      this.formData.date_begin = date_begin_convert
      if(this.discountBetweenDates){
        if(this.formData.discount_start_date < this.formData.date_begin){
          window.toastr['error'](this.$t('validation.date_discount_minor'))
          return true 
        }
      }

      this.$v.formData.$touch()     

      // validations 

      if(this.$v.formData.package.$invalid)
      {
        return true
      }

      if(this.formData.allow_discount && this.discountBetweenDates)
      {
        if(this.$v.formData.discount_start_date.$invalid && this.$v.formData.discount_end_date.$invalid)
        {
          return true
        }  
      }

      // validations

      if(this.renewal_date_switch == true && this.$v.formData.new_edit_renewal_date.$invalid)
      {
        return true;      
      }     
      if(this.renewal_date_switch)
      {
        this.formData.renewal_date = this.formData.new_edit_renewal_date;
      }

      this.isLoading = true
      this.formData.addresses_id = this.formData.address?.id || null   
      this.formData.customer_id = this.selectedViewCustomer.customer.id
      
      let data = {
        parameters: this.formData,
        service: this.service,
      }
     
      this.setPackageParameters(data)
      await this.$emit('next')
      this.isLoading = false
    },
  },
}
</script>

<style>
.multiselect__option > span {
  padding-left: 1rem;
}
.multiselect__option--group.multiselect__option > span {
  padding-left: 0;
  font-weight: bold;
}
</style>