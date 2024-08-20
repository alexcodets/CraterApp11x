<template>
  <base-page>
    <stats-info />
    <dashboard-info />
    <memo v-if="Object.keys(this.loggedInCustomer).length" />
    <table-info />
  </base-page>
</template>

<script>
import StatsInfo from "../customer-profile/StatsInfo";
import DashboardInfo from '../customer-profile/DashboardInfo'
import Memo from '../customer-profile/Memo'
import TableInfo from '../customer-profile/TableInfo'
import {mapActions, mapGetters} from "vuex";

export default {
  components: {
    StatsInfo,
    DashboardInfo,
    Memo,
    TableInfo
  },
  computed: {
    ...mapGetters('customerProfile', ['loggedInCustomer']),
  },
  created() {
    this.fetchLoggedInCustomer()
  },
  methods: {
  ...mapActions('customerProfile', ['fetchLoggedInCustomer']),
  }
}
</script>
