<template>
  <div class="hold-invoice-pdf-modal">
    <div
      class="flex flex-col min-h-0 mt-8 overflow-hidden"
      style="height: 75vh"
    >
      <iframe
        id="iframe"
        name="iframe"
        :src="pdfHoldInvoice"
        class="
          flex-1
          border border-gray-400 border-solid
          rounded-md
          frame-style
        "
      />
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, minLength, numeric, email } = require('vuelidate/lib/validators')
export default {
  data() {
    return {
      isEdit: false,
      isLoading: false,
      pdfBase64: '',
      data: {}
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),

    pdfHoldInvoice() {
      if (this.pdfBase64) {
        return `data:application/pdf;base64,${this.pdfBase64}`;
      }
      return '';
    },
 
  },


  created() {
    this.loadData()
  },

  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('corePos', ['fetchCashRegisterUser', 'fetchPosItemCategory' ,'holdInvoice', 'deleteHoldInvoice']),

   async loadData() {
      const response = this.modalData
      this.data = response

      let responsePdf = await axios.post('/api/hold_invoice/pdf', this.data)

      this.pdfBase64 = responsePdf.data.pdfBase64

      // this.holdInvoice(this.data)
      //       .then((res) => {
      //         console.log('hold invoice pdf modal')
      //         console.log(res)
      //         this.pdf = res.data.pdf
      //         window.toastr['success'](this.$t('general.created_successfully'))
      //         this.clearFields()
      //       })
      //       .catch((err) => {
      //         window.toastr["success"](this.$t('general.action_failed'));
      //       })

    },
   
    closeNoteModal() {
      this.closeModal()
    },
  },
}
</script>
<style lang="scss">

</style>
