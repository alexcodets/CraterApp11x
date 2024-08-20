<template>
  <div class="contact-modal">
    <form action="" @submit.prevent="submit">
      <sw-card class="customer-card">
        <div class="grid grid-cols-5 gap-4 mb-8 ">
          <div class=" grid grid-cols-1 col-span-12 gap-3 mt-8 lg:gap-3 lg:mt-0 lg:grid-cols-2">
            <sw-input-group :label="$t('core_pos.open_close_cash_modal.reference')" class="mt-4 ">
              <sw-input v-model="formData.ref" type="text" disabled />
            </sw-input-group>
            <sw-input-group :label="$t('core_pos.open_close_cash_modal.open_date')" class="mt-4 ">
              <sw-input v-model="formData.open_date" type="text" disabled />
            </sw-input-group>
          </div>
        </div>
        <div v-if="!isConfirmed">
          <sw-input-group v-if="permissionModule.access" :label="$t('core_pos.users')" class="mb-4">
            <sw-select v-model="formData.users" :options="users_options" :searchable="true" :show-labels="false"
              class="mt-2" track-by="id" label="name" :placeholder="'Select an User'" :multiple="true" :tabindex="10"
              :disabled="disabledOpen" />
          </sw-input-group>
        </div>
        <div v-if="isOpenCashRegister" class="grid grid-cols-5 gap-4 mb-8 ">
          <div class=" grid grid-cols-1 col-span-12 gap-3 mt-8 lg:gap-3 lg:mt-0 lg:grid-cols-2">
            <sw-input-group :label="$t('core_pos.open_close_cash_modal.initial_amount')" class="mt-4 ">
              <sw-money :currency="defaultCurrencyForInput" disabled
                class="focus:border focus:border-solid focus:border-primary" :value="calculateInitialAmount" />

            </sw-input-group>
            <div v-if="!isConfirmed">
              <sw-input-group v-if="isOpenCashRegister" :label="$t('core_pos.open_close_cash_modal.close_date')"
                class="mt-4">
                <sw-input v-model="formData.close_date" type="text" disabled />
              </sw-input-group>
            </div>
          </div>
        </div>
        <sw-input-group v-if="!isOpenCashRegister" :label="$t('core_pos.open_close_cash_modal.last_amount')" class="mt-4">
          <sw-money :currency="defaultCurrencyForInput" class="focus:border focus:border-solid focus:border-primary"
            v-model.number="last_amount" />
          <p class="text-xs text-red-600">{{ $t("core_pos.last_amount_message") }}</p>
        </sw-input-group>
        <sw-input-group v-if="!isOpenCashRegister" :label="$t('core_pos.open_close_cash_modal.cash_received')"
          class="mt-4">
          <sw-money :currency="defaultCurrencyForInput" :disabled="disabledOpen"
            class="focus:border focus:border-solid focus:border-primary" v-model.number="formData.cash_received" />
          <p class="text-xs text-red-600">{{ $t("core_pos.cash_received_message") }}</p>
        </sw-input-group>
        <sw-input-group v-if="!isOpenCashRegister" :label="$t('core_pos.open_close_cash_modal.initial_amount')"
          class="mt-4 ">
          <sw-money :currency="defaultCurrencyForInput" disabled
            class="focus:border focus:border-solid focus:border-primary" :value="calculateInitialAmount" />
          <p class="text-xs text-red-600">{{ $t("core_pos.initial_amount_message") }}</p>
        </sw-input-group>

        <sw-input-group v-if="!isOpenCashRegister" :label="$t('core_pos.open_close_cash_modal.open_note')" class="mt-4">
          <sw-textarea rows="4" cols="50" v-model="formData.open_note" :disabled="disabledOpen" />
        </sw-input-group>
        <hr>
        <div v-if="!isConfirmed">
          <div class="grid grid-cols-5 gap-4 mb-8 ">
            <div class=" grid grid-cols-1 col-span-12 gap-3 mt-8 lg:gap-3 lg:mt-0 lg:grid-cols-2">
              <sw-input-group v-if="isOpenCashRegister" :label="$t('core_pos.cash_received')" class="mt-4 ">
                <sw-money :currency="defaultCurrencyForInput" :disabled="disabledOpen"
                  class="focus:border focus:border-solid focus:border-primary" v-model.number="received" />
              </sw-input-group>
              <sw-input-group v-if="isOpenCashRegister" :label="$t('core_pos.cash_returned')" class="mt-4 ">
                <sw-money :currency="defaultCurrencyForInput" :disabled="disabledOpen"
                  class="focus:border focus:border-solid focus:border-primary" v-model.number="returned" />
              </sw-input-group>
              <sw-input-group v-if="isOpenCashRegister" :label="$t('core_pos.income')" class="mt-4 ">
                <sw-money :currency="defaultCurrencyForInput" :disabled="disabledOpen"
                  class="focus:border focus:border-solid focus:border-primary" v-model.number="income" />
              </sw-input-group>
              <sw-input-group v-if="isOpenCashRegister" :label="$t('core_pos.withdrawal')" class="mt-4 ">
                <sw-money :currency="defaultCurrencyForInput" :disabled="disabledOpen"
                  class="focus:border focus:border-solid focus:border-primary" v-model.number="withdrawal" />
              </sw-input-group>

            </div>
          </div>


          <sw-input-group v-if="isOpenCashRegister" :label="$t('core_pos.open_close_cash_modal.final_amount')"
            class="mt-4 w-1/2">
            <sw-money :currency="defaultCurrencyForInput" class="focus:border focus:border-solid focus:border-primary"
              v-model="formData.final_amount" :value="calculateFinalAmount" :disabled="isConfirmed" />
          </sw-input-group>
          <sw-input-group v-if="isOpenCashRegister" :label="$t('core_pos.open_close_cash_modal.close_note')" class="mt-4">
            <sw-textarea rows="4" cols="50" v-model="formData.close_note" />
          </sw-input-group>
        </div>
        <div class="my-2" v-if="isConfirmed">
          <span class="text-xs text-red-700"> {{ $t('core_pos.open_close_cash_modal.message_confirm_open_cash_register')
          }}</span>
        </div>
        <div class="mt-5 flex">
          <sw-button :loading="isLoading" variant="primary" icon="save">
            <save-icon v-if="!isLoading" class="mr-2" />
            <div v-if="isConfirmed">
              {{ $t('general.confirm') }}
            </div>
            <div v-else>
              {{ $t('general.save') }}
            </div>
          </sw-button>
          <sw-button type="button" class="ml-1" :loading="isLoading" variant="primary-outline"
            @click="closeModalCashRegister">
            {{ $t('general.cancel') }}
          </sw-button>
        </div>
      </sw-card>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { CheckCircleIcon } from "@vue-hero-icons/outline"
import moment from 'moment';
const { required, minLength, numeric, email } = require('vuelidate/lib/validators')
export default {
  components: {
    CheckCircleIcon
  },
  data() {
    return {
      isConfirmed: false,
      isEdit: false,
      isLoading: false,
      isOpenCashRegister: null,
      disabledOpen: null,
      last_amount: 0,
      cash: 0,
      received: 0,
      returned: 0,
      income: 0,
      withdrawal: 0,
      users_options: [],
      permissionModule: {
        access: false
      },
      formData: {
        id: '',
        open: true,
        ref: '',
        cash_received: 0,
        initial_amount: '',
        open_note: '',
        open_date: '',
        final_amount: 0,
        close_note: '',
        close_date: '',
        cash_register_id: null,
        users: []
      }

    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),
    ...mapGetters('company', ['defaultCurrency', 'defaultCurrencyForInput']),

    calculateInitialAmount() {
      let initialAmount = this.last_amount + this.formData.cash_received
      this.formData.initial_amount = initialAmount
      return initialAmount
    },

    calculateFinalAmount(amount) {
    ///  console.log(amount)
      if (!this.formData.open) {
        let finalAmount = (this.formData.initial_amount + this.income + this.received) - (this.withdrawal + this.returned)
        this.formData.final_amount = finalAmount
        return finalAmount
      }
    },
  },

  validations() {
    return {
    }
  },
  created() {
    this.loadData()
    this.permissionsUserModule()
  },

  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('corePos', ['addCashHistory']),
    ...mapActions('user', ['getUserModules']),

    async loadData() {

      const data = {
        cash_register_id: this.modalData.id
      }

      this.isConfirmed = this.modalData.is_confirmed
      const resUsersAssignCashRegister = await window.axios.post('/api/v1/core-pos/cash-register/getUserAssignCashRegister', { data })

      this.users_options = resUsersAssignCashRegister.data.users

      if (this.modalData.cash_history.length != 0) {
        if (this.modalData.cash_history[this.modalData.cash_history.length - 1].open == 1) {

          if (this.modalData.cash_history[this.modalData.cash_history.length - 2] != undefined) {
            const lastCashHistory = this.modalData.cash_history[this.modalData.cash_history.length - 2]
            this.last_amount = parseFloat(lastCashHistory.final_amount)
          }
          const history = this.modalData.cash_history[this.modalData.cash_history.length - 1]
          this.isOpenCashRegister = true
          this.disabledOpen = true
          this.formData.cash_received = parseFloat(history.cash_received)
          this.formData.ref = history.ref
          this.formData.initial_amount = parseFloat(history.initial_amount)
          this.formData.open_note = history.open_note
          this.formData.open_date = history.open_date
          this.formData.cash_register_id = this.modalData.id
          this.formData.id = history.id
          this.formData.open = false
          this.formData.close_date = this.dateNow()


          const params = {
            open_date: this.formData.open_date,
            cash_register_id: this.modalData.id
          }


          const resInvoicesCashAmount = await window.axios.get('/api/v1/core-pos/cash-register/getCashAmountPayments', { params })

          const params2 = {
            cash_history_id: history.id,
            cash_register_id: this.modalData.id
          }

          const resGetUserAssign = await window.axios.post('/api/v1/core-pos/cash-register/userAssignCashRegister', { params2 })
          this.formData.users = resGetUserAssign.data.users

          if (resInvoicesCashAmount.data.success) {
            this.received = parseFloat(resInvoicesCashAmount.data.invoices_amount.received / 100)
            this.returned = parseFloat(resInvoicesCashAmount.data.invoices_amount.returned / 100)
            this.income = parseFloat(resInvoicesCashAmount.data.amount_income / 100)
            this.withdrawal = parseFloat(resInvoicesCashAmount.data.amount_withdrawal / 100)
          }
         // console.log('close');


        } else {


          if (this.modalData.cash_history[this.modalData.cash_history.length - 1] != undefined) {
            const lastCashHistory = this.modalData.cash_history[this.modalData.cash_history.length - 1]
            this.last_amount = parseInt(lastCashHistory.final_amount)
          }
          this.isOpenCashRegister = false
          this.disabledOpen = false
          this.formData.open = true
          this.formData.cash_register_id = this.modalData.id
          this.formData.open_date = this.dateNow()
          this.formData.ref = this.generateReference()
         // console.log('open');
        }

      } else {
        this.isOpenCashRegister = false
        this.disabledOpen = false
        this.formData.open = true
        this.formData.cash_register_id = this.modalData.id
        this.formData.open_date = this.dateNow()
        this.formData.ref = this.generateReference()
       // console.log('open');
      }



    },

    generateReference() {
      const prefix = "REF-";

      const randomDigits = Math.floor(Math.random() * 1000).toString().padStart(4, '0')
      const currentTime = Math.floor(Date.now() / 1000).toString()
      const uniqueReference = prefix + randomDigits + currentTime

      return uniqueReference;
    },

    async submit() {
      let message = ''

      if (this.isConfirmed) {
        message = 'core_pos.open_close_cash_modal.message_confirm_open_cash'
      } else if (this.formData.open) {
        message = 'core_pos.open_close_cash_modal.message_open_cash'
      } else {
        message = 'core_pos.open_close_cash_modal.message_close_cash'
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t(message),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {

          if (this.isConfirmed) {
            const resCashHistory = await window.axios.post(`/api/v1/core-pos/confirm-open/${this.formData.id}`)
            if (resCashHistory.data.success) {
              window.toastr['success'](this.$t('core_pos.open_close_cash_modal.success_message_open_cash'))
              this.closeModal()
              // window.location.reload()
              return true
            }
          } else {

            let cash_register_selected = JSON.parse(sessionStorage.getItem("cash_register"));

            if (this.isOpenCashRegister) {
              if (cash_register_selected != undefined && cash_register_selected.id === this.modalData.id) {
                sessionStorage.removeItem('cash_register');
                //console.log('close CR');
              }
            }

            const res = await this.addCashHistory(this.formData)

            if (res.data.success) {
              window.toastr['success'](this.$t('core_pos.open_close_cash_modal.success_message_open_cash'))
              this.closeModal()
              window.location.reload()
              return true
            }
          }

          window.toastr['error']('Error')
          return true
        }
      })
    },

    dateNow() {
      return moment().format("YYYY-MM-DD H:mm:ss")
    },

    async permissionsUserModule() {
      const data = {
        module: "assign_user_cash_register",
      };
      const permissions = await this.getUserModules(data);
      // valida que el usuario tenga el permiso create, read, delete, update
      if (permissions.super_admin == true) {
        this.permissionModule.access = true;
      } else if (permissions.exist == true && permissions.permissions[0] != null) {
        const modulePermissions = permissions.permissions[0];
        if (modulePermissions.access == 1) {
          this.permissionModule.access = true;
        }
      }
    },

    closeModalCashRegister() {

      if (this.isConfirmed) {
        this.closeModal()
        this.$router.push(`/admin/dashboard`)
        return false
      }

      this.closeModal()
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
