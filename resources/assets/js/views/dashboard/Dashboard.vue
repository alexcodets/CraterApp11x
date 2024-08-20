<template>
  <base-page>
    <dashboard-stats />
    <dashboard-chart />
    <dashboard-table />
  </base-page>
</template>

<script>
import DashboardStats from '../dashboard/DashboardStats.vue'
import DashboardChart from '../dashboard/DashboardChart.vue'
import DashboardTable from '../dashboard/DashboardTable.vue'

export default {
  components: {
    DashboardStats,
    DashboardChart,
    DashboardTable,
  },
}
</script>
