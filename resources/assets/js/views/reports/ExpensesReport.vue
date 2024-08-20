<template>
  <div class="grid gap-8 md:grid-cols-12">
    <div class="col-span-8 mt-12 md:col-span-4">
      <div class="grid grid-cols-12 mb-2">
        <sw-input-group
          :label="$t('reports.expenses.date_range')"
          :error="dateRangeError"
          class="col-span-12 md:col-span-8"
        >
          <sw-select
            v-model="selectedRange"
            :options="dateRange"
            :allow-empty="false"
            :show-labels="false"
            class="mt-2"
            @input="onChangeDateRange"
          />
        </sw-input-group>
      </div>

      <div class="grid grid-cols-1 mt-6 md:gap-10 md:grid-cols-2 mb-2">
        <sw-input-group :label="$t('reports.expenses.from_date')" :error="fromDateError">
          <base-date-picker
            v-model="formData.from_date"
            :invalid="$v.formData.from_date.$error"
            :calendar-button="true"
            calendar-button-icon="calendar"
            class="mt-2"
            @input="$v.formData.from_date.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('reports.expenses.to_date')"
          :error="toDateError"
          class="mt-5 md:mt-0"
        >
          <base-date-picker
            v-model="formData.to_date"
            :invalid="$v.formData.to_date.$error"
            :calendar-button="true"
            calendar-button-icon="calendar"
            class="mt-2"
            @input="$v.formData.to_date.$touch()"
          />
        </sw-input-group>
      </div>

      <div class="grid grid-cols-12 mb-2 mt-5">
        <sw-input-group :label="$t('expenses.provider')" class="col-span-12">
          <sw-select
            v-model="selected.providerSelected"
            :options="providers"
            :multiple="true"
            class="mt-2"
            :searchable="true"
            :show-labels="true"
            :placeholder="$t('expenses.provider_select')"
            label="title"
            track-by="id"
          />
        </sw-input-group>
      </div>

      <div class="grid grid-cols-12 mb-2">
        <sw-input-group :label="$t('expenses.customer')" class="col-span-12">
          <sw-select
            v-model="selected.customerSelected"
            :options="customers"
            :multiple="true"
            class="mt-2"
            :placeholder="$t('customers.select_a_customer')"
            :searchable="true"
            :show-labels="true"
            label="name"
            track-by="id"
          />
        </sw-input-group>
      </div>

      <div class="grid grid-cols-12 mb-2">
        <sw-input-group :label="$t('payments.payment_mode')" class="col-span-12">
          <sw-select
            v-model="selected.paymentModeSelected"
            :multiple="true"
            class="mt-2"
            :placeholder="$t('payments.select_payment_mode')"
            :searchable="true"
            :options="paymentModes"
            :show-labels="true"
            label="name"
            track-by="id"
          />
        </sw-input-group>
      </div>

      <div class="grid grid-cols-12 mb-2">
        <sw-input-group :label="$t('expenses.category')" class="col-span-12">
          <sw-select
            v-model="selected.categorySelected"
            :options="categories"
            :multiple="true"
            class="mt-2"
            :placeholder="$t('expenses.categories.select_a_category')"
            label="name"
            track-by="id"
          />
        </sw-input-group>
      </div>

      <div v-if="allow_invoice_form_pos" class="grid grid-cols-12 mb-2">
        <sw-input-group :label="$t('core_pos.store')"  class="col-span-12">
          <sw-select v-model="stores_selected" :options="stores" :searchable="true" :show-labels="false"
            :allow-empty="true" :multiple="true" class="mt-2" track-by="id" label="name" :tabindex="1" />
        </sw-input-group>
      </div>

      <div class="grid grid-cols-12 mb-2">
        <sw-input-group :label="'Status'" class="col-span-12">
          <sw-select
            v-model="selected.statusSelected"
            :options="statusOptions"
            :multiple="true"
            class="mt-2"
            label="label"
            track-by="value"
          />
        </sw-input-group>
      </div>

      <div class="grid grid-cols-12 mb-2">
        <sw-input-group :label="$t('expenses.item')" class="col-span-12">
          <sw-select
            v-model="selected.itemSelected"
            :options="items"
            :multiple="true"
            class="mt-2"
            :placeholder="$t('expenses.item_select')"
            label="name"
            track-by="id"
          />
        </sw-input-group>
      </div>

      <sw-button
        variant="primary-outline"
        class="content-center hidden mt-0 w-md md:flex md:mt-8"
        @click="getReports()"
      >
        {{ $t("reports.update_report") }}
      </sw-button>
    </div>

    <div class="col-span-8 mt-0 md:mt-12">
      <iframe
        :src="getReportUrl"
        class="hidden w-full h-screen border-gray-100 border-solid rounded md:flex"
      />

      <a
        class="flex items-center justify-center h-10 px-5 py-1 text-sm font-medium leading-none text-center text-white whitespace-nowrap rounded md:hidden bg-primary-500"
        @click="viewReportsPDF"
      >
        <document-text-icon />

        <span>{{ $t("reports.view_pdf") }}</span>
      </a>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import moment from "moment";
import { DocumentTextIcon } from "@vue-hero-icons/solid";

const { required } = require("vuelidate/lib/validators");

export default {
  components: {
    DocumentTextIcon,
  },

  data() {
    return {
      range: new Date(),
      dateRange: [
        "Today",
        "This Week",
        "This Month",
        "This Quarter",
        "This Year",
        "Previous Week",
        "Previous Month",
        "Previous Quarter",
        "Previous Year",
        "Custom",
      ],

      selectedRange: "This Month",
      formData: {
        from_date: moment().startOf("month").format("YYYY-MM-DD"),
        to_date: moment().endOf("month").format("YYYY-MM-DD"),
      },
      url: null,
      siteURL: null,
      selected: {
        providerSelected: [],
        customerSelected: [],
        paymentModeSelected: [],
        categorySelected: [],
        statusSelected: [],
        itemSelected: [],
      },
      providerOptions: [],
      statusOptions: [
        { label: "ALL", value: "all" },
        { label: "Processed", value: "Active" },
        { label: "Pending", value: "Pending" },
      ],
      stores: [],
      stores_selected: [],
      allow_invoice_form_pos: false
    };
  },
  validations: {
    range: {
      required,
    },
    formData: {
      from_date: {
        required,
      },
      to_date: {
        required,
      },
    },
  },

  computed: {
    ...mapGetters("providers", ["providers"]),
    ...mapGetters("customer", ["customers"]),
    ...mapGetters("category", ["categories"]),
    ...mapGetters("item", ["items"]),
    ...mapGetters("payment", ["paymentModes"]),

    ...mapGetters("company", ["getSelectedCompany"]),
    getReportUrl() {
      return this.url;
    },

    dateRangeError() {
      if (!this.$v.range.$error) {
        return "";
      }

      if (!this.$v.range.required) {
        return this.$t("validation.required");
      }
    },

    fromDateError() {
      if (!this.$v.formData.from_date.$error) {
        return "";
      }

      if (!this.$v.formData.from_date.required) {
        return this.$t("validation.required");
      }
    },

    toDateError() {
      if (!this.$v.formData.to_date.$error) {
        return "";
      }

      if (!this.$v.formData.to_date.required) {
        return this.$t("validation.required");
      }
    },

    dateRangeUrl() {
      return `${this.siteURL}?from_date=${moment(this.formData.from_date).format(
        "YYYY-MM-DD"
      )}&to_date=${moment(this.formData.to_date).format("YYYY-MM-DD")}`;
    },
    urlAddFilter() {
      let url = this.dateRangeUrl;
      if (this.selected.providerSelected.length > 0) {
        url += `&provider=${this.selected.providerSelected
          .map((item) => item.id)
          .join(",")}`;
      }
      if (this.selected.customerSelected.length > 0) {
        url += `&customer=${this.selected.customerSelected
          .map((item) => item.id)
          .join(",")}`;
      }
     
      if (this.stores_selected.length > 0) {
        url += `&stores_id=${this.stores_selected
          .map((store) => store.id)
          .join(",")}`;
      }

      if (this.selected.paymentModeSelected.length > 0) {
        url += `&payment_mode=${this.selected.paymentModeSelected
          .map((item) => item.id)
          .join(",")}`;
      }
      if (this.selected.categorySelected.length > 0) {
        url += `&category=${this.selected.categorySelected
          .map((item) => item.id)
          .join(",")}`;
      }
      if (this.selected.statusSelected.length > 0) {
        url += `&status=${this.selected.statusSelected
          .map((item) => item.value)
          .join(",")}`;
      }
      if (this.selected.itemSelected.length > 0) {
        url += `&item=${this.selected.itemSelected.map((item) => item.id).join(",")}`;
      }
      return url;
    },
  },

  watch: {
    range(newRange) {
      this.formData.from_date = moment(newRange).startOf("year").format("YYYY-MM-DD");
      this.formData.to_date = moment(newRange).endOf("year").format("YYYY-MM-DD");
    },
  },

  async mounted() {

    
    // start - is module corepos allowed
    const modules = ['corePOS']
    const modulesArray = await this.getModules(modules)

    var moduleCorePos = null

    if (typeof modulesArray.modules != 'undefined') {
      moduleCorePos = modulesArray.modules.find(
        (element) => element.name === 'corePOS'
      )
    }

    if (moduleCorePos && moduleCorePos.status == 'A') {
      let res = await this.fetchCompanySettings(['allow_invoice_form_pos'])
      this.allow_invoice_form_pos =
        res.data.allow_invoice_form_pos == '0' ? false : true
    } else {
      this.allow_invoice_form_pos = false
    }
    // end - is module corepos allowed


    this.siteURL = `/reports/expenses/${this.getSelectedCompany.unique_hash}`;
    this.url = this.dateRangeUrl;
    this.getOptionsReportsSelected();

    let dataStore = {
        limit: 'all'
      }
      
      const responseStore = await this.fetchStores(dataStore)
      this.stores = responseStore.data.stores.data

  },

  methods: {
    ...mapActions("providers", ["fetchProviders"]),
    ...mapActions("category", ["fetchCategories"]),
    ...mapActions("customer", ["fetchCustomers"]),
    ...mapActions("item", ["fetchItems"]),
    ...mapActions("payment", ["fetchPaymentModes"]),
    ...mapActions('corePos', ['fetchStores']),
    ...mapActions('modules', ['getModules']),
    ...mapActions('company', ['fetchCompanySettings']),

    async getOptionsReportsSelected() {
      await this.fetchCategories({ limit: "all" });
      await this.fetchCustomers({ limit: "all" });
      await this.fetchProviders({ limit: "all" });
      await this.fetchItems({ limit: "all" });
      await this.fetchPaymentModes({ limit: "all" });
    },

    getThisDate(type, time) {
      return moment()[type](time).format("YYYY-MM-DD");
    },
    getPreDate(type, time) {
      return moment().subtract(1, time)[type](time).format("YYYY-MM-DD");
    },
    onChangeDateRange() {
      switch (this.selectedRange) {
        case "Today":
          this.formData.from_date = moment().format("YYYY-MM-DD");
          this.formData.to_date = moment().format("YYYY-MM-DD");
          break;

        case "This Week":
          this.formData.from_date = this.getThisDate("startOf", "isoWeek");
          this.formData.to_date = this.getThisDate("endOf", "isoWeek");
          break;

        case "This Month":
          this.formData.from_date = this.getThisDate("startOf", "month");
          this.formData.to_date = this.getThisDate("endOf", "month");
          break;

        case "This Quarter":
          this.formData.from_date = this.getThisDate("startOf", "quarter");
          this.formData.to_date = this.getThisDate("endOf", "quarter");
          break;

        case "This Year":
          this.formData.from_date = this.getThisDate("startOf", "year");
          this.formData.to_date = this.getThisDate("endOf", "year");
          break;

        case "Previous Week":
          this.formData.from_date = this.getPreDate("startOf", "isoWeek");
          this.formData.to_date = this.getPreDate("endOf", "isoWeek");
          break;

        case "Previous Month":
          this.formData.from_date = this.getPreDate("startOf", "month");
          this.formData.to_date = this.getPreDate("endOf", "month");
          break;

        case "Previous Quarter":
          this.formData.from_date = this.getPreDate("startOf", "quarter");
          this.formData.to_date = this.getPreDate("endOf", "quarter");
          break;

        case "Previous Year":
          this.formData.from_date = this.getPreDate("startOf", "year");
          this.formData.to_date = this.getPreDate("endOf", "year");
          break;

        default:
          break;
      }
    },

    getProvider() {},

    setRangeToCustom() {
      this.selectedRange = "Custom";
    },

    async viewReportsPDF() {
      let data = await this.getReports();
      window.open(this.getReportUrl, "_blank");
      return data;
    },

    async getReports(isDownload = false) {
      this.$v.range.$touch();
      this.$v.formData.$touch();

      // if (this.$v.$invalid) {
      //   return true;
      // }

      // urlAddFilter add filter to url
      this.url = this.urlAddFilter;
      
      return true;
    },

    downloadReport() {
      if (!this.getReports()) {
        return false;
      }
      return this.getReportUrl
      /*
      window.open(this.getReportUrl + "&download=true");
      setTimeout(() => {
        this.url = this.dateRangeUrl;
      }, 200);
      */
    },
  },
};
</script>
