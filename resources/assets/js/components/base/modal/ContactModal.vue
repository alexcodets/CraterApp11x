<template>
  <div class="contact-modal">
  <div class="mt-2 mr-2 flex justify-end">
      <div class="">
        <sw-button :loading="isLoading" variant="primary" @click="copyContactInvoice">
          <clipboard-copy-icon v-if="!isLoading" class="mr-2" />
          {{ $t('core_pos.hold_get_contact_invoice') }}
        </sw-button>
      </div>
    </div>
    <form action="" @submit.prevent="submitContact">
      <sw-card class="">
        <div class="">
          <sw-input-group :label="$t('core_pos.contact.principal_phone')" class="mt-2">
            <sw-input v-model="formData.second_phone" type="text" />
          </sw-input-group>

          <sw-input-group :label="$t('core_pos.contact.name')" :error="nameError" class="mt-4">
            <sw-input v-model="formData.name" :invalid="$v.formData.name.$error" type="text" />
          </sw-input-group>

          <sw-input-group :label="$t('core_pos.contact.last_name')" class="mt-4">
            <sw-input v-model="formData.last_name" type="text" />
          </sw-input-group>

          <sw-input-group :label="$t('core_pos.contact.optional_phone')" class="mt-4" :error="phoneError">
            <sw-input v-model="formData.phone" :invalid="$v.formData.phone.$error" type="text" />
          </sw-input-group>

          <!-- OPTIONAL -->
          <sw-input-group :label="$t('core_pos.contact.identification')" class="mt-4">
            <sw-input v-model="formData.identification" type="text" />
          </sw-input-group>


          <sw-input-group :label="$t('core_pos.contact.email')" class="mt-4" :error="emailError">
            <sw-input v-model="formData.email" :invalid="$v.formData.email.$error" type="text" />
          </sw-input-group>

          <div class="mt-5">

            <sw-button :loading="isLoading" variant="primary" icon="save">
              <save-icon v-if="!isLoading" class="mr-2" />
              {{ $t('general.save') }}
            </sw-button>
          </div>
        </div>
      </sw-card>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, minLength, numeric, email } = require('vuelidate/lib/validators')
import {
  PrinterIcon,
  ShoppingCartIcon,
  PlusIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  ClipboardCopyIcon,
  PencilIcon,
  TrashIcon,
  MenuIcon,
  UserIcon,
  ViewGridIcon,
  DocumentTextIcon
} from '@vue-hero-icons/solid'
export default {
  components: {
    PrinterIcon,
    ShoppingCartIcon,
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    EyeIcon,
    ClipboardCopyIcon,
    PencilIcon,
    TrashIcon,
    MenuIcon,
    UserIcon,
    ViewGridIcon,
    DocumentTextIcon
  },
  data() {
    return {
      isEdit: false,
      isLoading: false,
      contact_invoice: [],
      formData: {
        name: '',
        last_name: '',
        email: '',
        phone: '',
        second_phone: '',
        identification: ''
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

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },
    phoneError() {
      if (!this.$v.formData.phone.$error) {
        return ''
      }
      if (!this.$v.formData.phone.required) {
        return this.$tc('validation.required')
      }
    },
    secondPhoneError() {
      if (!this.$v.formData.second_phone.$error) {
        return ''
      }
      if (!this.$v.formData.second_phone.required) {
        return this.$tc('validation.required')
      }
    },
    emailError() {
      if (!this.$v.formData.email.$error) {
        return ''
      }
      if (!this.$v.formData.email.email) {
        return this.$tc('validation.email_invalid')
      }
    },
  },

  validations() {
    return {
      formData: {
        name: {
        },
        phone: {

        },
        second_phone: {

        },
        email: {
          email,
        },

      },
    }
  },
  created() {
    this.loadData()
  },

  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),

    loadData() {
      const response = this.modalData
      this.contact_invoice = response.contact_invoice
      const contact = response.contact
      if (contact != null) {
        this.formData.name = contact.name
        this.formData.last_name = contact.last_name
        this.formData.email = contact.email
        this.formData.phone = contact.phone
        this.formData.second_phone = contact.second_phone
        this.formData.identification = contact.identification

        if(this.formData.phone == undefined){
          this.formData.phone = this.contact_invoice.phone
        }
      }
      else {
        this.formData.name = ''
        this.formData.last_name = ''
        this.formData.email = ''
        this.formData.phone = this.contact_invoice.phone
        this.formData.second_phone = ''
        this.formData.identification = ''
      }
    },
    async submitContact() {


      
      this.$v.formData.$touch()

      if (this.$v.$invalid ) {
        return true
      }

      const data = {}
      for (let key in this.formData) {
        if (this.formData[key] != '') {
          data[key] = this.formData[key]
        }
      }
      window.hub.$emit('contact_invoice_emit', data)
      this.closeNoteModal()
    },
    closeNoteModal() {
      this.closeModal()
    },

    copyContactInvoice(){

        swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.cancel_text'),
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          
          this.formData.name = this.contact_invoice.name != null ?  this.contact_invoice.name : ''
          this.formData.last_name = this.contact_invoice.last_name != null ? this.contact_invoice.last_name : ''
          this.formData.email = this.contact_invoice.email != null ? this.contact_invoice.email : ''
          this.formData.phone = this.contact_invoice.phone != null ? this.contact_invoice.phone : ''
          // this.formData.second_phone = this.contact_invoice.second_phone != null ? this.contact_invoice.second_phone : ''
          // this.formData.identification = this.contact_invoice.identification != null ? this.contact_invoice.identification : ''
        }
      })
    
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
