<template>
  <base-page class="option-group-create">
    <form action="" @submit.prevent="submitData">
      <sw-page-header class="mb-3" :title="title"></sw-page-header>
      <div class="grid grid-cols-12">
        <div class="col-span-12">
          <sw-card class="mb-8">
            <sw-input-group
              :label="$t('customer_ticket.reference')"
              class="mb-4"
            >
              <sw-input v-model="formData.reference" type="text" readonly />
            </sw-input-group>

            <sw-input-group
              :label="$t('customer_ticket.subject')"
              class="mb-4"
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
              :error="messageError"
              required
            >
              <sw-textarea
                v-model="formData.message"
                rows="10"
                cols="50"
                style="resize: none"
                @input="$v.formData.message.$touch()"
              />
            </sw-input-group>

            <div class="flex mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.public"
                  class="absolute"
                  style="top: -18px"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black">
                  {{
                    $t(
                      'customer_ticket.note_ticket_view'
                    )
                  }}
                </p>

                <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
                  {{
                    $t(
                      'customer_ticket.note_ticket_view_desc'
                    )
                  }}
                </p>
              </div>
            </div>

            <sw-input-group
              :label="$t('general.datetime')"
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
                :disabled="true"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('general.datetime')"
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
                :disabled="true"
              />
            </sw-input-group>

            <div
              class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
            >
              <sw-button
                class="mr-3"
                variant="primary-outline"
                type="button"
                @click="cancelNote()"
              >
                {{ $t('general.cancel') }}
              </sw-button>
              <sw-button
                :loading="isLoading"
                :disabled="isLoading"
                variant="primary"
                type="submit"
              >
                <save-icon v-if="!isLoading" class="mr-2" />
                {{ $t('general.save') }}
              </sw-button>
            </div>
          </sw-card>
        </div>
      </div>
    </form>
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

export default {
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
        public: 0,
      },
      ticket: null,
      note: null,
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
    ...mapActions('customerTicket', ['addNoteTicket', 'fetchTicketNote']),

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
        this.formData.public = this.note.public
      } else {
        this.title = this.$t('customer_ticket.create_note')
        this.ticket = data.data.ticket
        const number_note = data.data.notes_quantity + 1
        this.formData.reference = this.ticket.ticket_number + '-0' + number_note
        this.formData.date = moment(this.ticket.created_at).format('YYYY-MM-DD')
        this.formData.time = moment().format('HH:mm:ss')
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

    cancelNote() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.cancel_text'),
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          this.$router.push(
            `/admin/tickets/main/${this.$route.params.id}/${this.$route.params.id1}/view-ticket`
          )
        }
      })
    },

    async submitData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }
      let response = null
      this.isLoading = true

      let text = ''
      if (this.isEdit) {
        text = 'customer_ticket.ticket_edit'
      } else {
        text = 'customer_ticket.ticket_create'
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc(text),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          if (this.isEdit) {
            this.isRequestOnGoing = true

            if(this.formData.public == false){
              this.formData.public =0;
            }

            response = await this.fetchTicketNote(this.formData)
          } else {
            this.isRequestOnGoing = true
            this.formData.time = moment().format('HH:mm:ss')
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
        }else{
          this.isLoading = false
        }
      })
    },
  },
}
</script>
  
<style scoped></style>
  