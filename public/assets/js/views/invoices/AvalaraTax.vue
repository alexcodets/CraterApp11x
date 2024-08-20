<template>
  <td v-if="taxes.length" colspan="4" class="px-2 py-4 text-left align-top">
    <avalara-tax-item
      v-for="tax in taxes"
      :key="tax.id"
      :currency="currency"
      :tax-data="tax"
    />
  </td>
</template>

<script>
import { CheckCircleIcon, TrashIcon } from '@vue-hero-icons/solid'
import avalaraTaxItem from './AvalaraTaxItem.vue'
import { mapActions } from 'vuex'

export default {
  components: {
    CheckCircleIcon,
    TrashIcon,
    avalaraTaxItem,
  },
  props: {
    item: {
      type: Object,
      default: null,
    },
    total: {
      type: Number,
      default: 0,
    },
    user: {
      type: Object,
      default: null,
    },
    totalTax: {
      type: Number,
      default: 0,
    },
    currency: {
      type: [Object, String],
      required: true,
    },
  },
  data() {
    return {
      tax: { ...this.taxData },
      selectedTax: null,
      taxes: [],
    }
  },
  watch: {
    total: {
      handler: 'updateItemTaxes',
    },
    user: {
      handler: 'updateItemTaxes',
    },
    item: {
      handler: 'updateItemTaxes',
    },
  },
  created() {},
  computed: {
    totalTaxes() {
      return this.taxes.reduce(function (accumulator, currentValue) {
        return accumulator + parseFloat(currentValue.tax)
      }, 0)
    },
  },
  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('avalara', ['fetchAvalaraItemTaxes']),
    customLabel({ name, percent }) {
      return `${name} - ${percent}%`
    },
    updateItemTaxes() {
      if (this.total <= 0 || this.user == null) {
        this.taxes = []
        return
      }
      this.isLoading = true
      let data = {
        data: {
          quantity: this.item.quantity,
          price: this.item.price,
        },
        id: this.item.item_id || this.item.items_id,
        user_id: this.user.id,
      }
      console.log('id - user_id', data.id, data.user_id)
      this.fetchAvalaraItemTaxes(data)
        .then((res) => {
          this.isLoading = false
          if (res.data.success) {
            //console.log(res.data.data.items[0].txs)
            this.taxes = res.data.data.items[0].txs
            //window.toastr['success'](this.$t('invoices.updated_message'))
          }

          if (!res.data.success) {
            console.log(res.data.message, res.data.errors)
            window.toastr['error'](res.data.message, res.data.status)
            this.taxes = []
            //window.toastr['error'](
            //this.$t('invoices.invalid_due_amount_message')
            // )
          }
          this.$emit('update', { total: this.totalTaxes, taxes: this.taxes })
        })
        .catch((err) => {
          console.log(err)
          this.isLoading = false
        })
    },
  },
}
</script>
