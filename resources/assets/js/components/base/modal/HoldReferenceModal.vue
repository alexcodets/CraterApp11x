<template>
  <div class="hold-invoice-modal">
    <div class="relative table-container overflow-auto ">

      <sw-card class="text-center">
        <h1 class="text-xl font-bold"> {{ $t('general.are_you_sure') }}</h1>
        <h3 class="text-lg"> {{ $t('core_pos.hold_invoice_message') }}</h3>
        <div class="">
          <sw-input-group :label="$t('core_pos.description_input')" class="mt-4">
            <sw-input v-model="reference" type="text" />
          </sw-input-group>
          <div>
            <p class="text-xs text-red-600 " >{{ $t('core_pos.hold_invoice_message_warning') }}</p>
          </div>

          <div class="mt-5 flex justify-center">

            <sw-button class="mr-2" @click="submitHold" variant="primary" icon="save">
              <save-icon  class="mr-2" />
              {{ $t('core_pos.accept_alert') }}
            </sw-button>
            <sw-button class="mr-2" @click="printHold" variant="primary">
              <printer-icon class="mr-2" />
              {{ $t('core_pos.print') }}
            </sw-button>
            <sw-button class="mr-2" @click="cancelHold" variant="primary">

              {{ $t('core_pos.cancel_alert') }}
            </sw-button>
          </div>
        </div>
      </sw-card>

    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import Swal from 'sweetalert2'
const { required, minLength } = require('vuelidate/lib/validators')
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  TrashIcon,
  PencilIcon,
  EyeIcon,
  EyeOffIcon,
  CreditCardIcon,
  PrinterIcon,
} from "@vue-hero-icons/solid";

export default {
  components: {
    ChevronDownIcon,
    FilterIcon,
    XIcon,
    TrashIcon,
    PencilIcon,
    EyeIcon,
    EyeOffIcon,
    CreditCardIcon,
    PrinterIcon
  },
  data() {
    return {
      data: {},
      reference: ''
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),
  },

  mounted() {
    this.loadData()
  },

  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData', 'openModal']),
    ...mapActions('corePos', ['holdInvoice']),

    loadData(){
      this.data = this.modalData
      this.reference = this.data.description
    },

    submitHold() {

      if (this.reference == '') {
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        })

        Toast.fire({
          icon: 'error',
          title: this.$t('core_pos.message_error_input')
        })

        return
      }
      this.data.description = this.reference
      let data = this.data

      this.holdInvoice(data)
        .then((res) => {
          window.toastr['success'](this.$t('general.created_successfully'))
          // this.clearFields()
          let data = {
            clear_data: true
          }
          window.hub.$emit('clear_data_emit', data)
          this.ref = ''
          this.closeModal()
        })
        .catch((err) => {
          window.toastr["success"](this.$t('general.action_failed'));
        })
    },

    printHold() {

      if (this.reference == '') {
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        })

        Toast.fire({
          icon: 'error',
          title: this.$t('core_pos.message_error_input')
        })

        return
      }
      
      let data = this.data
      data.print_pdf = true
      data.save_print = true
      this.data.description = this.reference
      this.openHoldInvoicesPdfModal(data)

      let data2 = {
            clear_data: true
          }
      window.hub.$emit('clear_data_emit', data2)
    },

    cancelHold() {
      this.ref = ''
      this.closeModal()
    },

    openHoldInvoicesPdfModal(data) {
      this.openModal({
        title: this.$t('core_pos.hold_invoices_title'),
        componentName: 'holdInvoicePdfModal',
        data: data
      })
    },

  },
}
</script>
<style lang="scss">
.note-modal {
  .header-editior .editor-menu-bar {
    margin-left: 0.5px;
    margin-right: 0px;
  }
}
</style>
