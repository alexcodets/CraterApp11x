<template>
  <div class="flex items-center justify-between mb-3">
    <div class="flex items-center" style="flex: 4">
      
      <sw-select
        v-model="selectedRetention"
        :options="retentionsOptions"
        :allow-empty="false"
        :show-labels="false"
        :placeholder="$t('settings.retentions.title')"
        track-by="id"
        label="label"
        @select="(val) => onSelectRetention(val)"
      >
        <template slot="singleLabel" slot-scope="option">
            <div class="flex items-center">
                <div v-if="option.option.concept" class="text-sm">{{ option.option.concept + " " +  option.option.percentage + '%' }}</div>
            </div>
        </template>

        <template slot="option" slot-scope="option">
            <div class="flex items-center">
                <div v-if="option.option" class="text-sm">{{ option.option.concept + " " +  option.option.percentage + '%' }}</div>
            </div>
        </template>
      </sw-select>
      <br />
    </div>
    <div
      class="text-sm text-right"
      style="flex: 3"
      v-html="$utils.formatMoney(selectedRetention.retention_amount, currency)"
    />
    <div class="flex items-center justify-center w-6 h-10 mx-2 cursor-pointer">
      <trash-icon
        v-if="selectedRetention.id"
        class="h-5 text-gray-700"
        @click="removeRetention"
      />
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { CheckCircleIcon, TrashIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    CheckCircleIcon,
    TrashIcon,
  },
  props: {
    total: {
      type: Number,
      default: 0,
    },
    retentionData: {
      type: Object,
      default: {},
    },
    currency: {
      type: [Object, String],
      required: true,
    },
  },
  data() {
    return {
      selectedRetention: {...this.retentionData},
      retentionsOptions: [],
    }
  },
  computed: {
    ...mapGetters('taxType', ['taxTypes']),
    filteredTypes() {
      return []
    },
    retention_amount() {
      if (this.total && this.selectedRetention.percentage) {
        return Math.round((this.total * this.selectedRetention.percentage) / 100)
      }
      return 0
    },
  },
  watch: {
    retentionData: {
      handler(val) {
        this.selectedRetention = {...val}
      },
      deep: true,
    },
    retention_amount() {
      this.updateRetention()
    },
  },
  created() {
    this.getRetentionsOptions()
    this.updateRetention()
  },
  methods: {
    ...mapActions('modal', ['openModal']),

    async getRetentionsOptions(){
      try{
        const res = await this.$store.dispatch('retentions/fetchRetentions', {limit:10000})
        this.retentionsOptions = res.data.retentions.data
      }catch(e){
        // console.log(e)
      }
    },
    onSelectRetention(val) {
      this.selectedRetention = val
      this.updateRetention()
    },
    updateRetention() {
      this.selectedRetention.retention_amount = this.retention_amount
      this.$emit('update',this.selectedRetention)
    },
    removeRetention() {
      this.selectedRetention = {}
      this.$emit('remove')
    },
  },
}
</script>
