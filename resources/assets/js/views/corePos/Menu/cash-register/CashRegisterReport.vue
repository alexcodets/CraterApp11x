<template>

  <base-page class="customer-create">

  <!------------------- Cabecera  ----------------->
  <sw-page-header :title="$t('reports.cash_register.title')">
      <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item :title="$t('general.home')" to="/admin/corePOS" />
          <sw-breadcrumb-item :title="$t('core_pos.cash_registers')" to="cash-register" active />
      </sw-breadcrumb>

      <template slot="actions">
        
          <sw-button tag-name="router-link" to="/admin/corePOS/cash-register" size="lg" variant="primary" class="ml-4">
              <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
              {{ $t('general.back') }}
          </sw-button>
      </template>
  </sw-page-header>


  <!-- <div class="grid gap-8 md:grid-cols-12"> -->
    <div>
    <div class="col-span-8 mt-12 md:col-span-4">

      <!--  -->

      <div class="col-span-8 mt-12 md:col-span-4">
        <div class="grid grid-cols-12 mb-2">
          <sw-input-group :label="$t('reports.cash_register.report_cash_register')" class="col-span-12 md:col-span-8">
            <sw-select v-model="cash_historie_id" :options="cash_histories" :allow-empty="false" :show-labels="false"
              class="mt-2" track-by="id" label="formattedSelectReport" :multiple="false" />
          </sw-input-group>
        </div>
        <span>{{ $t("reports.cash_register.info_select") }}</span>

        <sw-button variant="primary-outline" class="content-center hidden mt-0 w-md md:flex md:mt-8"
          @click="getReports()">
          {{ $t("reports.update_report") }}
        </sw-button>
      </div>
    </div>


    <div class="col-span-8 mt-0 md:mt-12">
      <iframe :src="getReportUrl" class="hidden w-full h-screen border-gray-100 border-solid rounded md:flex" />

      <a class="flex items-center justify-center h-10 px-5 py-1 text-sm font-medium leading-none text-center text-white whitespace-nowrap rounded md:hidden bg-primary-500"
        @click="viewReportsPDF">
        <document-text-icon />
        <span>{{ $t("reports.view_pdf") }}</span>
      </a>
    </div>
  </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import { DocumentTextIcon } from "@vue-hero-icons/solid";
const { required } = require("vuelidate/lib/validators");

export default {
  components: {
    DocumentTextIcon,
  },
  data() {
    return {
      cash_histories: [],
      cash_historie_id: null,
      url: '',
      siteURL: ''

    }
  },

  computed: {

    ...mapGetters("company", ["getSelectedCompany"]),

    getReportUrl() {
      return this.url;
    },

    dateRangeUrl() {
      return this.siteURL;
    },

    urlAddFilter() {
      let url = this.dateRangeUrl;
      if (this.cash_historie_id) {
        url += `?cash_history_id=${this.cash_historie_id.id}`
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

  mounted() {
    this.loadData()
    this.siteURL = `/reports/cash-register/${this.$route.params.id}/company/${this.getSelectedCompany.unique_hash}`;
    this.url = this.dateRangeUrl;
  },

  methods: {

    async loadData() {

      const data = {
        'cash_register_id': this.$route.params.id
      }

      const response = await window.axios.get('/api/v1/core-pos/cash-history', { params: data })
      this.cash_histories = response.data.data
    //  console.log(this.cash_histories)
      if(this.cash_histories.length != 0){
        this.cash_historie_id = this.cash_histories[0]
      }
    },

    async viewReportsPDF() {
      let data = await this.getReports();
      // window.open(this.getReportUrl, "_blank");
      return data;
    },

    async getReports(isDownload = false) {
      this.url = this.urlAddFilter;
      return true;
    },
  }

}
</script>
