<template>
  <base-page>

    <stats-info />
    <dashboard-info />
    <table-info />

  </base-page>
</template>

<script>
import StatsInfo from "../customer-profile/StatsInfo.vue";
import DashboardInfo from '../customer-profile/DashboardInfo.vue'
import Memo from '../customer-profile/Memo.vue'
import TableInfo from '../customer-profile/TableInfo.vue'
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
