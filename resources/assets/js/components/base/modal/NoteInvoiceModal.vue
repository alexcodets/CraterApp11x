<template>
  <div class="note-modal">
    <form action="" @submit.prevent="submitNote">
      <div class="px-8 py-8 sm:p-6">
        
        <div class="mb-6">
          <sw-popup ref="notePopup" class="z-10 text-sm font-semibold leading-5 text-primary-400">
            <div slot="activator" class="float-right mt-1">
              + {{ $t('general.insert_note') }}
            </div>
            <note-select-popup type="Invoice" @select="onSelectNote" />
          </sw-popup>
          <sw-input-group style="z-index:1" :label="$t('invoices.notes')">
            <base-custom-input v-model="formData.notes" :fields="InvoiceFields" @input="addNotes"/>
          </sw-input-group>
        </div>

      </div>
      <div
        class="z-0 flex justify-end px-4 py-4 border-t border-solid border-gray-light"
      >
        <sw-button
          class="mr-2"
          variant="primary-outline"
          type="button"
          @click="closeNoteModal"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button
          :loading="isLoading"
          variant="primary"
          icon="save"
          type="submit"
        >
          <save-icon v-if="!isLoading" class="mr-2" />
          {{ !isEdit ? $t('general.save') : $t('general.update') }}
        </sw-button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, minLength } = require('vuelidate/lib/validators')
export default {
  data() {
    return {
      isEdit: false,
      isLoading: false,
      selectType: null,
      formData: {
        notes: '',
      },
      notes_data: [],
      InvoiceFields: [
        'customer',
        'customerCustom',
        'company',
        'invoice',
        'invoiceCustom',
      ],
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
this.loadData()
  },

  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),

    loadData(){
      const response = this.modalData
      this.formData.notes = response
    }, 
    async submitNote() {
      window.hub.$emit('note_invoice_emit', this.formData.notes)
      this.closeNoteModal()
    },
    closeNoteModal() {
      this.closeModal()
    },

    onSelectNote(data) {
      this.notes_data.push(data.notes)
      this.formData.notes  = this.notes_data.join('\n ')
      
      this.$refs.notePopup.close()
    },

    addNotes(){
      this.notes_data = []
      this.notes_data.push(this.formData.notes)
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
