<template>
  <base-page class="profit-loss-reports reports">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="$tc('reports.report', 2)">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item :title="$t('general.home')" :to="`/admin/dashboard`" />
          <sw-breadcrumb-item
            :title="$tc('reports.report', 2)"
            :to="`/admin/reports`"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>

      <div class="flex flex-wrap items-center justify-end">
        <sw-button         
          :loading="isDownloadOnGoing"
          :disabled="isDownloadOnGoing" 
          @click="onDownload()"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary"
        >
          <download-icon v-if="!isDownloadOnGoing" class="h-5 mr-1 -ml-2" />
          {{ $t("reports.download_pdf") }}
        </sw-button>
      </div>
    </div>

    <div class="row">
      <!-- Tabs -->
      <sw-tabs>
        <sw-tab-item :title="$t('reports.sales.sales')" route="/admin/reports/sales">
        </sw-tab-item>

        <sw-tab-item
          :title="$t('reports.profit_loss.profit_loss')"
          route="/admin/reports/profit-loss"
        >
        </sw-tab-item>

        <sw-tab-item
          :title="$t('reports.expenses.expenses')"
          route="/admin/reports/expenses"
        >
        </sw-tab-item>

        <sw-tab-item :title="$t('reports.taxes.taxes')" route="/admin/reports/taxes">
        </sw-tab-item>
      </sw-tabs>
    </div>
    <transition name="fade" mode="out-in">
      <div v-if="activeTab === 'SALES' || 'PROFIT_LOSS' || 'EXPENSES' || 'TAXES'">
        <router-view ref="report" />
      </div>
    </transition>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import { DownloadIcon } from "@vue-hero-icons/solid";
export default {
  components: {
    DownloadIcon,
  },
  data() {
    return {
      activeTab: "EXPENSES",
      isDownloadOnGoing: false
    };
  },
  watch: {
    "$route.path"(newValue) {
      if (newValue === "/admin/reports") {
        this.$router.push("/admin/reports/sales");
      }
    },
  },
  created() {
    if (this.$route.path === "/admin/reports") {
      this.$router.push("/admin/reports/sales");
    }
  },
  mounted() {
    this.permissionsUserModule();
  },
  methods: {    
    ...mapActions("user", ["getUserModules"]),

    async onDownload() {
      this.isDownloadOnGoing = true
      let url = this.$refs.report.downloadReport();  
      await this.setDownload(url)
      this.isDownloadOnGoing = false
    },

    async setDownload(url){      
      await fetch(url).then(function(t) {
        return t.blob().then((b)=> {
          var a = document.createElement("a");
          a.href = URL.createObjectURL(b);
          // setPdfName
          let pdf_name = ""
          if(url.includes('/reports/sales/customers')) {
            pdf_name = "customer_sales_report.pdf"
          }
          if(url.includes('/reports/sales/items')) {
            pdf_name = "items_sales_report.pdf"
          }
          if(url.includes('/reports/profit-loss')) {
            pdf_name = "profit_and_loss_report.pdf"
          }
          if(url.includes('/reports/expenses')){
            pdf_name = "expenses_report.pdf"
          }
          if(url.includes('/reports/tax-summary')) {
            pdf_name = "taxes_report.pdf"
          }
          //         
          a.setAttribute("download", pdf_name);
          a.click();
        });
      });      
    }, 

    setActiveTab(val) {
      this.activeTab = val;
    },
    async permissionsUserModule() {
      const data = {
        module: "reports",
      };
      const permissions = await this.getUserModules(data);
      // valida que el usuario tenga permiso para ingresar al modulo
      if (permissions.super_admin == false) {
        if (permissions.exist == false) {
          this.$router.push("/admin/dashboard");
        } else {
          const modulePermissions = permissions.permissions[0];
          if (modulePermissions == null) {
            this.$router.push("/admin/dashboard");
          } else if (modulePermissions.access == 0) {
            this.$router.push("/admin/dashboard");
          }
        }
      }
    },
  },
};
</script>

<style scoped>
.tab {
  padding: 0 !important;
}

.tab-link {
  padding: 10px 30px;
  display: block;
}
</style>
