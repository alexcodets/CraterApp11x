<template>
  <base-page class="bg-white">
    <sw-page-header :title="pageTitle">
      <template slot="actions">
        <sw-button
          tag-name="router-link"
          :to="routeRedirect"
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('general.cancel') }}
        </sw-button>
      </template>
    </sw-page-header>

    <!-- Basic Info -->
    <customer-info />

    <div
      class="
        flex flex-col
        items-center
        justify-between
        w-full
        h-32
        step-indicator
      "
    >
      <sw-wizard
        :steps="3"
        :currentStep.sync="step"
        :allow-navigation-redirect="false"
      >
        <component :is="tab" @next="setTab" @back="back" />
      </sw-wizard>
    </div>
  </base-page>
</template>


<script>
import CustomerInfo from './partials/CustomerInfo'
import FormCorePbxServices from './partials/WizardCorePbxParameters'
import CorePbxServicesDetail from './partials/WizardCorePbxDetail'
import CorePbxServicesResume from './partials/WizardCorePbxResume'
import { mapActions, mapGetters } from 'vuex'

export default {
  components: {
    CustomerInfo,
    step_1: FormCorePbxServices,
    step_2: CorePbxServicesDetail,
    step_3: CorePbxServicesResume,
  },
  data() {
    return {
      routeRedirect: '',
      loading: false,
      tab: 'step_1',
      step: 1,
    }
  },
  computed: {
    ...mapGetters('customer', ['selectedViewCustomer']),

    pageTitle() {
      if (this.$route.name === 'pbxServices.edit') {
        return this.$t('services.edit_service')
      }
      return this.selectedViewCustomer.customer
        ? this.$t('customers.add_corepbx_services')
        : ''
    },
  },
  created() {
    this.loadCustomer()
    this.redirect()
  },
  methods: {
    ...mapActions('customer', ['fetchViewCustomer']),

    async loadCustomer() {
      this.fetchViewCustomer({ id: this.$route.params.id })
    },

    async setTab() {
      this.step++

      if (this.step <= 3) {
        this.tab = 'step_' + this.step
      } else {
        // window.location.reload()
      }
    },

    async back() {
      this.step--

      if (this.step >= 1) {
        this.tab = 'step_' + this.step
      }
    },

    redirect(){
      if(this.$route.name === 'pbxServices.edit'){
        this.routeRedirect = `/admin/customers/${this.$route.params.id}/pbx-service/${this.$route.params.customer_pbx_service_id}/view`
      } else {
        this.routeRedirect = `/admin/customers/${this.$route.params.id}/view`
      }
    }
  },
}
</script>
