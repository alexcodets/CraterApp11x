<template>
  <div class="hold-invoice-modal">
    <div class="relative table-container h-96 overflow-auto ">
      <!-------------------------- Tabla -------------------------->
      <sw-table-component
          ref="table"
          :show-filter="false"
          :data="fetchData"
          table-class="table"
      >               
          <sw-table-column
              :sortable="true"
              :label="$t('core_pos.date')" 
              sort-as="invoice_date"
              show="invoice_date"
          >            
          </sw-table-column>
          <sw-table-column
              :sortable="true"
              :label="$t('core_pos.reference')" 
              sort-as="description"
              show="description"
          >                    
          </sw-table-column>
          <sw-table-column
              :sortable="true"
              :label="''"
              sort-as=""
              show=""
          >                 
          <template slot-scope="row" >
            <div class="flex ">
              <div @click="getHoldInvoice(row)" >
                <pencil-icon  class="h-10 text-gray-600  " />
              </div>
            </div>
          </template>   
          </sw-table-column>
          <sw-table-column
              :sortable="true"
              :label="''"
              sort-as=""
              show=""
          >                 
          <template slot-scope="row" >
            <div class="flex ">
              <div @click="openHoldInvoicesPdfModal(row)" >
                <printer-icon  class="h-10 text-gray-600  " />
              </div>
            </div>
          </template>   
          </sw-table-column>
          <sw-table-column
              :sortable="true"
              :label="''"
              sort-as="total"
              show="total"
          >                 
          <template slot-scope="row" >
            <div class="flex ">
              <div @click="deleteHoldInvoiceSelected(row)" >
                <trash-icon  class="h-10 ml-4 text-gray-600 " />
              </div>
            </div>
          </template>   
          </sw-table-column>

      
      </sw-table-component>

  </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
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
      isEdit: false,
      isLoading: false,
      selectType: null,
    
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

  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('corePos', [ 'fetchInvoicesHold', 'deleteHoldInvoice']),

    getHoldInvoice(invoice){
      invoice.is_hold_invoice = true
      invoice.hold_invoice_id = invoice.id
      window.hub.$emit('get_hold_invoice', invoice)
      this.closeHoldInvoiceModal()
    },

    async deleteHoldInvoiceSelected(invoice){

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('core_pos.message_delete_hold_invoice'),
        icon: '/assets/icon/file-alt-solid.svg',
       
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
    
          const response = await this.deleteInvoice(invoice)
          if(response.data.success){
            window.toastr['success'](this.$t('core_pos.delete_invoice_hold_message'))
          }else {
            window.toastr["error"](this.$t('general.action_failed'));
          }
          // this.closeHoldInvoiceModal()
          this.refreshTable()
        }
      })

    },

    async deleteInvoice(invoice){
      const data = {
        id: invoice.id
      }

      const response = await this.deleteHoldInvoice(data)
      return response
    },

    async fetchData({page, filter, sort}){
      try{
        const response = await this.fetchInvoicesHold()
        return {
            data: response.data.hold_invoices.data,
            pagination: {
                currentPage: page,
            },
        }
      }catch(error){
      }
    }, 
 
    closeHoldInvoiceModal() {
      this.closeModal()
    },

    openHoldInvoicesPdfModal(invoice) {
      invoice.print = true
      invoice.save_print = false
      window.hub.$emit('get_hold_invoice', invoice)
      // this.closeHoldInvoiceModal()
    },

    refreshTable() {
      this.$refs.table.refresh()
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
