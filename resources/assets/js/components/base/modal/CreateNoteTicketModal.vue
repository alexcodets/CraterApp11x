<template>
  <div class="item-modal">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <form action="" @submit.prevent="submitData">
      <div class="px-8 py-8 sm:p-6">
        <sw-input-group
          :label="$t('customer_ticket.reference')"
          class="mb-4"
          variant="horizontal"
          
        >
          <sw-input v-model="formData.reference" type="text" readonly/>
        </sw-input-group>

        <sw-input-group
          :label="$t('customer_ticket.subject')"
          class="mb-4"
          variant="horizontal"
          :error="subjectError"
          required
        >
          <sw-input 
          v-model="formData.subject" 
          type="text" 
          @input="$v.formData.subject.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('customer_ticket.message')"
          class="mb-4"
          variant="horizontal"
          :error="messageError"
          required
        >
          <sw-textarea v-model="formData.message" type="text"
          @input="$v.formData.message.$touch()"
          />
        </sw-input-group>

        <sw-input-group
            :label="'Date'"
            :error="dateError"
            class="mb-4"
            required
            variant="horizontal"
          >
            <base-date-picker
              v-model="formData.date"
              :calendar-button="true"
              calendar-button-icon="calendar"
              style="max-width: 100%"
              @input="$v.formData.date.$touch()"
              :disabled="isEdit"
            />
        </sw-input-group>

        <sw-input-group
            :label="'Time'"
            :error="timeError"
            class="mb-4"
            required
            variant="horizontal"
          >
            <base-time-picker
              v-model="formData.time"
              :invalid="$v.formData.time.$error"
              :calendar-button="true"
              style="max-width: 100%"
              :placeholder="'HH:mm'"
              calendar-button-icon="calendar"
              @input="$v.formData.time.$touch()"
              :disabled="isEdit"
            />
        </sw-input-group>

        <div
          class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
        >
          <sw-button
            class="mr-3"
            variant="primary-outline"
            type="button"
            @click="closeExtensionModal"
          >
            {{ $t('general.cancel') }}
          </sw-button>
          <sw-button variant="primary" type="submit">
            <save-icon class="mr-2" />
            {{ $t('general.save') }}
          </sw-button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
const {
  required,
  minLength,
  numeric,
  maxLength,
  minValue,
  email,
} = require('vuelidate/lib/validators')

export default {
  data() {
    return {      
      isEdit: false,
      isLoading: false,
      isRequestOnGoing: false,
      formData: {
        id: null,
        subject: '',
        message: '',
        reference: '',
        customer_ticket_id: null,
        date: null,
        time: null
      },
      ticket: null,
      note: null
    }
  },
  validations: {
    formData: {
      subject: {
        required,
      },
      message: {
        required,
      },
      reference: {
        required,
      },
      date: {
        required,
      },
      time: {
        required
      }
    },
  },
  
  mounted() {
    this.formData.customer_ticket_id = this.$route.params.id1
  },
  created(){
    this.loadData()
  },
  computed: {
    ...mapGetters('modal', [
        'modalDataID',
        'modalData',
        'modalActive',
        'refreshData',
      ]),

    subjectError() {
      if (!this.$v.formData.subject.$error) {
        return ''
      }

      if (!this.$v.formData.subject.required) {
        return this.$tc('validation.required')
      } 
    },
    messageError() {
      if (!this.$v.formData.message.$error) {
        return ''
      }

      if (!this.$v.formData.message.required) {
        return this.$tc('validation.required')
      } 
    },
    dateError() {
      if (!this.$v.formData.date.$error) {
        return ''
      }
      if (!this.$v.formData.date.required) {
        return this.$t('validation.required')
      }
    },
    timeError() {
      if (!this.$v.formData.time.$error) {
        return ''
      }
      if (!this.$v.formData.time.required) {
        return this.$t('validation.required')
      }
    },
  },
  validations: {
    formData: {
      subject: {
        required
      },
      message: {
        required
      },
      date: {
          required,
      },
      time: {
        required
      }
    }
  },

  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('customerTicket', ['addNoteTicket', 'fetchTicketNote']),

    loadData(){

      this.isEdit = this.$store.state.modal.data.isEdit

      if(this.isEdit){
        this.note = this.$store.state.modal.data.note

        this.formData.subject = this.note.subject
        this.formData.message = this.note.message
        this.formData.reference = this.note.reference
        this.formData.id = this.note.note_id
        //
        this.formData.date = moment(this.note.date).format('YYYY-MM-DD')
        this.formData.time = this.note.time   
        //      
      }else {
        this.ticket = this.$store.state.modal.data.ticket
        //
        this.formData.date = moment(this.ticket.created_at).format('YYYY-MM-DD')
        this.formData.time = moment().format('LTS');        
        //
        const number_note = this.$store.state.modal.data.notes_quantity + 1
        this.formData.reference = this.ticket.ticket_number + '-0' + number_note
      }
    },
    resetFormData() {
      this.formData = {
        subject: '',
        message: '',
        reference: '',
        customer_ticket_id: null,
      }
      this.$v.$reset()
    },

    closeExtensionModal() {
      this.resetFormData()
      this.closeModal()
      this.resetModalData()
    },

    async submitData() {
      this.$v.formData.$touch()
      
      if (this.$v.$invalid) {
        return true
      }
      let response = null

      if(this.isEdit){
        this.isRequestOnGoing = true
        response = await this.fetchTicketNote(this.formData)
      }else {
        this.isRequestOnGoing = true
        response = await this.addNoteTicket(this.formData)
      }
      if (response.data.success) {
        window.toastr['success']('Note created Successfully')
        this.refreshData ? this.refreshData() : ''
        this.isRequestOnGoing = false
        this.resetModalData()
        this.resetFormData()
        this.closeModal()
        return true
      }

      this.isRequestOnGoing = false
      if (response.data.error) {
        window.toastr['error'](response.data.error)
      } else {
        window.toastr['error'](response.data.message)
      }
    },
  },
}
</script>

<style scoped></style>
