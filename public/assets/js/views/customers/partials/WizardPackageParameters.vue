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

        <div v-if="showDates">
          <sw-input-group :label="$t('customers.date_begin')" class="mb-4">
            <base-date-picker
              v-model="formData.date_begin"
              :calendar-button="true"
              calendar-button-icon="calendar"
            />
          </sw-input-group>

          <sw-input-group
            v-if="isEdit"
            :label="$t('customers.renewal_date')"
            class="mb-4"
          >
            <base-date-picker
              v-model="formData.renewal_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
            />
          </sw-input-group>
        </div>

        <!--<sw-input-group :label="$t('customers.tax_type')" class="mb-4">
          <sw-select
            v-model="formData.tax_type"
            :options="discount_type"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('customers.select_a_tax_type')"
            class="mt-2"
            label="name"
            :disabled="true"
          />
        </sw-input-group>-->

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
            <sw-input-group :label="$t('general.from')" class="mt-2">
              <base-date-picker
                v-model="formData.discount_start_date"
                :calendar-button="true"
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
                calendar-button-icon="calendar"
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
import { ArrowRightIcon, ArrowLeftIcon } from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'

const { required, between } = require('vuelidate/lib/validators')

export default {
  components: {
    ArrowRightIcon,
    ArrowLeftIcon,
  },
  data() {
    return {
      isLoading: false,
      isRequestOnGoing: false,
      service: null,
      package: '',
      formData: {
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
        tax_type: { name: 'None', value: 'N' },
        term: { name: 'Monthly', value: 'monthly' },
        service_auto_suspension: false,
      },
      groups: [],
      status: [
        { name: 'Active', value: 'A' },
        { name: 'Pending', value: 'P' },
        { name: 'Suspend', value: 'S' },
        { name: 'Cancelled', value: 'C' },
      ],
      discount_type: [
        { name: 'None', value: 'N' },
        { name: 'General', value: 'G' },
        { name: 'By item', value: 'I' },
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
        { name: 'One time', value: 'one time' },
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
    }
  },
  validations: {
    formData: {
      package: {
        required,
      },
      discount_time_units: {
        between: between(0, 1000000),
      },
    },
  },
  computed: {
    ...mapGetters('customer', ['selectedViewCustomer', 'packageParameters']),
    ...mapGetters('pack', ['packagesByGroup']),
    ...mapGetters('company', ['defaultCurrency']),

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
  },
  methods: {
    ...mapActions('pack', ['fetchPackage', 'fetchPackagesByGroup']),
    ...mapActions('customer', ['setPackageParameters']),
    ...mapActions('service', ['fetchService']),
    ...mapActions('company', ['fetchCompanySettings']),

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
        //this.formData = this.packageParameters.parameters
      }

      if (this.isEdit) {
        this.showDates = false
        let res = await this.fetchService(this.$route.params.customer_package_id)
        this.service = res.data.service
        let service = this.service
        let pkg = service.package
        let discount = service.discounts[0]

        this.formData.package = { package_name: pkg.name, value: pkg.id }
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
              discount.start_date,
              'YYYY-MM-DD'
            ).toString()

            this.formData.discount_end_date = moment(
              discount.end_date,
              'YYYY-MM-DD'
            ).toString()
          }
        }

        this.formData.date_begin = moment(
          service.activation_date,
          'YYYY-MM-DD'
        ).toString()

        this.formData.renewal_date = moment(
          service.renewal_date,
          'YYYY-MM-DD'
        ).toString()

        this.showDates = true
        this.formData.allow_discount = service.allow_discount
        this.formData.service_auto_suspension = service.service_auto_suspension

      } else {
        let settings = await this.fetchCompanySettings(['tax_per_item'])
        settings.data.tax_per_item === 'YES'
          ? this.formData.tax_type = { name: 'By item', value: 'I' }
          : this.formData.tax_type = { name: 'General', value: 'G' }
      }

      this.isRequestOnGoing = false
    },

    async loadPackageData(item) {
      this.isRequestOnGoing = true
       console.log('item', item);
      let response = await this.fetchPackage(item.value)
      this.package = response.data.response
      this.formData.allow_discount = this.package.packages_discount

      /*switch (this.package.apply_tax_type) {
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
      }*/
      this.isRequestOnGoing = false
    },

    setBeginDate() {
      if (this.isPending) {
        this.formData.date_begin = ''
      }
    },

    setDiscountParams() {
      this.formData.discount_type = {
        name: 'General',
        value: 'G',
      }
      this.formData.discount_start_date = ''
      this.formData.discount_end_date = ''
      this.formData.discount_term_type = { name: 'Between dates', value: 'D' }
      this.formData.discount_term = { name: 'Days', value: 'days' }
      this.formData.discount_time_units = 0
    },

    async next() {
      this.$v.formData.$touch()
      if (this.$v.formData.$invalid) {
        return true
      }
      this.isLoading = true
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