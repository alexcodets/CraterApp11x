<template>
  <div class="tax-type-modal" data-modal-toggle="large-modal">
    <form action="" @submit.prevent="submitData" class="mb-5"
      style="padding-top: 17.5px; padding-right: 37.5px; padding-left: 30px;">
      <sw-input-group :label="$t('core_pos.cash_register')" class="md:col-span-3 mb-5" required>
        <sw-select v-model="cash_register_selected" :options="cash_register_options" :searchable="true"
          :show-labels="false" :allow-empty="false" :placeholder="$t('core_pos.select_cash_register')" class="mt-2" label="name"
          track-by="id" :tabindex="11" />
        <p v-if="cash_register_required" class="text-red-800 text-xs">{{$t('core_pos.cash_register_error')}}</p>
      </sw-input-group>
      <div class="
        z-0
        flex
        justify-end
        p-4
        border-t border-solid border--200 border-modal-bg
        mt-8
      ">
        <sw-button :loading="isLoading" variant="primary" type="submit">
          {{ $t('general.continue') }}
        </sw-button>

      </div>
    </form>
  </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { XCircleIcon } from '@vue-hero-icons/solid'
import { fetchCashRegisterUser } from '../../../store/modules/core-pos/actions'
const {
  required,
  minLength,
  between,
  maxLength,
} = require('vuelidate/lib/validators')
export default {
  components: {
    XCircleIcon,
  },
  data() {
    return {
      cashRegistersUser: [],
      cash_register_selected: {},
      cash_register_options: [],
      isLoading: false,
      cash_register_required: false,
    }
  },
  validation: {
    cash_register_selected: {
      required
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
      'props',
    ]),

    cashRegisterError() {

      // if (!this.cash_register_selected) {
      //   console.log('Realiza las validaciones que necesites con cash_register_selected')
      //     if (!this.$v.cash_register_selected.$error) {
      //       return ''
      //   }

      //   if (!this.$v.cash_register_selected.required) {
      //     return this.$t('validation.required')
      //   }
      //   return this.cash_register_selected;
      // } else {
      //   console.log(' o algún otro valor por defecto en caso de que cash_register_selected no esté definido')
      //   return null;
      // }
    },

  },
  created() {
    this.loadData()
  },

  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('corePos', ['fetchCashRegisterUser']),

    async loadData() {
      const responseCashRegistersUser = await this.fetchCashRegisterUser()
      this.cash_register_options = responseCashRegistersUser.data.data

      if (this.cash_register_options.length == 0) {
        swal({
          title: $t('general.error_message'),
          text: $t('core_pos.error_create_cash_register'),
          icon: '/assets/icon/file-alt-solid.svg',
          confirmButtonText: $t('general.continue'),
          allowOutsideClick: false,
        }).then((result) => {
          this.closeSelectModal()
          this.$router.push(`/admin/dashboard`)
        })
      }

    },

    async submitData() {
      if (Object.entries(this.cash_register_selected).length === 0) {
        this.cash_register_required = true
        return true
      }
      window.hub.$emit('cash_register_emit', this.cash_register_selected)
      window.hub.$emit('confirm_open_cash_register_emit', {open: true})
    //  console.log('select CR 117');
      sessionStorage.setItem("cash_register", JSON.stringify(this.cash_register_selected));
      this.closeSelectModal()
    },

    closeSelectModal() {
      this.closeModal()
    },
  },
}
</script>
