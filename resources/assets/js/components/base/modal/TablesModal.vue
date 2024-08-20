<template>
  <div class="hold-invoice-modal w-auto " >
    <div class="table-container h-96  overflow-auto ">
      <!-------------------------- Tabla -------------------------->
      <div class="">

        <sw-table-component ref="table" :show-filter="false" :data="fetchData" table-class="table">

          <sw-table-column :sortable="false">
            <template slot-scope="row">
              <sw-checkbox :id="row.id" v-model="row.selected" :value="row.id" variant="primary" size="lg" class="  border-gray-500 rounded-sm "/>
            </template>
          </sw-table-column>

          <sw-table-column :sortable="false" :label="$t('core_pos.name')" sort-as="name" show="name" />

          <sw-table-column :sortable="false" :label="$t('core_pos.persons')" sort-as="persons" show="Persons">
            <template slot-scope="row">
              <div class="flex flex-row content-center ">
                <span class="text-sm ">
                  <sw-button :disabled="row.quantity == 0" class="ml-2 w-1/12" variant="primary-outline"
                    @click="decrementQuantity(row)">
                    -
                  </sw-button>
                </span>
                <span class="ml-2 mt-1">
                  {{ row.quantity }}
                </span>
                <!-- {{ item.quantity }} -->
                <span class="text-sm ml-2 ">
                  <sw-button class="ml-2 mt w-1/12" variant="primary-outline" @click="incrementQuantity(row)">
                    +
                  </sw-button>
                </span>
              </div>
            </template>
          </sw-table-column>

        </sw-table-component>
      </div>
      <div class="z-0 flex justify-end px-4 py-4 border-t border-solid border-gray-light" v-if="isEmptyData">
        <sw-button class="mr-2" variant="primary-outline" type="button" @click="closeTableModal">
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button :loading="isLoading" variant="primary" icon="save" @click="saveTables">
          <save-icon v-if="!isLoading" class="mr-2" />
          {{ $t('general.save') }}
        </sw-button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  TrashIcon,
  PencilIcon,
  EyeIcon,
  EyeOffIcon,
  CreditCardIcon,
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
  },
  data() {
    return {
      isEdit: false,
      isLoading: false,
      isEmptyData: true,
      list_tables: []
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

  created(){
    
  },

  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('corePos', ['fetchTablesCashRegister']),

    getHoldInvoice(invoice) {
      window.hub.$emit('get_hold_invoice', invoice)
      this.closeHoldInvoiceModal()
    },

    async fetchData({ page, filter, sort }) {
      try {
        const res = this.modalData
        const data = {
          id: res.cash_register.id
        }
        const response = await this.fetchTablesCashRegister(data)
        this.list_tables = response.data.data.map(table => ({ ...table, quantity: 0 }));
        if (res.tables.lenght != 0) {
          this.list_tables.forEach((element, index) => {
            const exists = res.tables.find(item => item.table_id == element.id)
            if (exists) {
              this.list_tables[index].quantity = exists.quantity
              this.list_tables[index].selected = true
            }
          })
        }

        return {
          data: this.list_tables,
          pagination: {
            currentPage: page,
          },
        }
      } catch (error) {
       // console.log(error)
      }
    },

    closeTableModal() {
      this.closeModal()
    },

    incrementQuantity(row) {
      let index = this.list_tables.findIndex(obj => obj.id === row.id)
      let quantity = this.list_tables[index].quantity
      this.list_tables[index].quantity = quantity + 1


    },

    decrementQuantity(row) {
      let index = this.list_tables.findIndex(obj => obj.id === row.id)
      let quantity = this.list_tables[index].quantity
      this.list_tables[index].quantity = quantity - 1
    },

    saveTables() {
      const data = []
      this.list_tables.forEach(element => {
        if (element.selected) data.push(element)
      })


      window.hub.$emit('tables_selected_emit', data)
      this.closeTableModal()
    },

    addTable(row) {
      this.tables_selected.push(row)
    }

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
