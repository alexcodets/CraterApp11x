<template>
  <base-page>
    <!-- Header  -->
    <sw-page-header class="mb-3" :title="'View Note'">
      <template slot="actions">
        <!-- Go back -->
        <sw-button
          class="mr-3"
          variant="primary-outline"
          type="button"
          @click="cancelNote()"
        >
          {{ $t('general.go_back') }}
        </sw-button>

        <!-- editar -->
        <sw-button
          @click="editNote(ticket, note)"
          class="mr-3"
          variant="primary-outline"
        >
          <pencil-icon class="h-5 mr-3 text-gray-600" />
          {{ $t('general.edit') }}
        </sw-button>

        <!-- eliminar -->
        <sw-button
          @click="deleteNote(note.note_id)"
          type="button"
          class="btn btn-primary"
          :loading="isLoading"
          :disabled="isLoading"
        >
          <trash-icon class="h-5 mr-3 text-gray-600" />
          <span v-if="!isLoading">{{ $tc('general.delete') }}</span>
        </sw-button>
      </template>
    </sw-page-header>

    <div class="w-full">
      <div class="col-span-12">
        <sw-card>
          <div>
            <p class="text-gray-500 uppercase sw-section-title">
              {{ $t('general.note_information') }}
            </p>

            <div class="flex flex-wrap mt-4 md:mt-6">
              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $t('general.reference') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    {{ note.reference }}
                  </p>
                </div>
              </div>
              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $t('expenses.subject') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    {{ note.subject }}
                  </p>
                </div>
              </div>
              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $t('general.datetime') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    {{ note.date }} {{ note.time }}
                  </p>
                </div>
              </div>
            </div>

            <div class="w-full md:w-1/4">
              <div class="font-bold py-2">
                {{ $t('customer_ticket.note_ticket_view') }}
              </div>
              <div>
                <p class="text-gray-700 text-sm">
                  {{ note.public === 1 ? 'YES' : 'NOT' }}
                </p>
              </div>
            </div>

            <div class="flex flex-wrap mt-5 md:mt-7">
              <div class="w-full md:w-1/2">
                <p class="font-bold">{{ $t('general.message') }}</p>
                <p class="text-gray-700 text-sm">{{ note.message }}</p>
              </div>
            </div>
          </div>
        </sw-card>
      </div>
    </div>
  </base-page>
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
import {
  PencilIcon,
  TrashIcon,
  RefreshIcon,
  FilterIcon,
  XIcon,
  EyeIcon,
} from '@vue-hero-icons/outline'
export default {
  components: {
    PencilIcon,
    TrashIcon,
    RefreshIcon,
    FilterIcon,
    XIcon,
    EyeIcon,
  },
  data() {
    return {
      isEdit: false,
      isLoading: false,
      isRequestOnGoing: false,
      title: '',
      formData: {
        id: null,
        subject: '',
        message: '',
        reference: '',
        customer_ticket_id: null,
        date: null,
        time: null,
      },
      ticket: {},
      note: {},
      id: null,
      id1: null,
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
        required,
      },
    },
  },

  mounted() {
    this.note = this.$route.params.data.note
    this.ticket = this.$route.params.data.ticket
    this.id = this.$route.params.id
    this.id1 = this.$route.params.id1
    this.formData.customer_ticket_id = this.$route.params.id1
  },
  created() {
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
        required,
      },
      message: {
        required,
      },
      date: {
        required,
      },
      time: {
        required,
      },
    },
  },

  methods: {
    ...mapActions('customerTicket', [
      'addNoteTicket',
      'fetchTicketNote',
      'deleteNoteTicket',
    ]),

    loadData() {
      this.isEdit = this.$route.params.data.isEdit
      const data = this.$route.params

      if (this.isEdit) {
        this.title = this.$t('customer_ticket.update_note')
        this.note = data.note
        this.formData.subject = this.note.subject
        this.formData.message = this.note.message
        this.formData.reference = this.note.reference
        this.formData.id = this.note.note_id
        this.formData.date = moment(this.note.date).format('YYYY-MM-DD')
        this.formData.time = this.note.time
      } else {
        this.title = this.$t('customer_ticket.create_note')
        this.ticket = data.data.ticket
        const number_note = data.data.notes_quantity + 1
        this.formData.reference = this.ticket.ticket_number + '-0' + number_note
        this.formData.date = moment(this.ticket.created_at).format('YYYY-MM-DD')
        this.formData.time = moment().format('HH:mm:ss')
      }
    },

    cancelNote() {
      this.$router.push(
        `/admin/tickets/main/${this.$route.params.id}/${this.$route.params.id1}/view-ticket`
      )
    },

    editNote({ id, user_id }, note) {
      const data = {
        ticket: this.ticket,
        notes_quantity: this.notes_count,
        isEdit: true,
      }
      this.$router.push({
        name: 'note.create',
        params: {
          id: user_id,
          id1: id,
          note: note,
          data: data,
        },
      })
    },

    async deleteNote(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customer_ticket.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        try {
          if (willDelete) {
            this.isLoading = false
            let res = await this.deleteNoteTicket({ id: id })
            if (res.data.success) {
              window.toastr['success'](
                this.$tc('customer_ticket.deleted_message', 1)
              )
              this.$router.push(
                `/admin/tickets/main/${this.id}/${this.id1}/view-ticket`
              )
              return true
            }
          }
        } catch (e) {
          window.toastr['error'](res.data.message)
        } finally {
          this.isLoading = false
        }
      })
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

    async submitData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }
      let response = null
      this.isLoading = true

      if (this.isEdit) {
        this.isRequestOnGoing = true
        response = await this.fetchTicketNote(this.formData)
      } else {
        this.isRequestOnGoing = true
        response = await this.addNoteTicket(this.formData)
      }
      if (response.data.success) {
        window.toastr['success'](
          this.$tc('customer_ticket.message_create_note')
        )
        // this.refreshData ? this.refreshData() : ''
        this.isRequestOnGoing = false
        this.$router.push(
          `/admin/tickets/main/${this.$route.params.id}/${this.$route.params.id1}/view-ticket`
        )
      }

      this.isRequestOnGoing = false
      if (response.data.error) {
        this.isLoading = false
        window.toastr['error'](response.data.error)
        return true
      }
    },
  },
}
</script>
    
 
    