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
                    {{ this.leadnotedata ? this.leadnotedata.leadnote_number : " " }}
                  </p>
                </div>
              </div>
              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $t('expenses.subject') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    {{ this.leadnotedata ? this.leadnotedata.subject : " " }}
                  </p>
                </div>
              </div>
              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $t('general.datetime') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    {{ this.leadnotedata ? this.leadnotedata.formattedAddeDate : " " }}
                  </p>
                </div>
              </div>
            </div>

            <div class="w-full md:w-1/4">
              <div class="font-bold py-2">
                {{ $t('customer_notes.creator') }}
              </div>
              <div>
                <p class="text-gray-700 text-sm">
                  {{ this.leadnotedata ? this.leadnotedata.formattedUserName : " " }}
                </p>
              </div>
            </div>

            <div class="flex flex-wrap mt-5 md:mt-7">
              <div class="w-full md:w-1/2">
                <p class="font-bold">{{ $t('general.message') }}</p>
                <p class="text-gray-700 text-sm"> {{ this.leadnotedata ? this.leadnotedata.body: " " }}</p>
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
      leadnotedata:null,
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

  mounted() {},
  created() {
    this.permissionsUserModules()
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

    ...mapActions('leadNote', [
      'addLead',
      'fetchLeadNotes',
      'deleteLeadNote',
      'fetchLeadSingleNote',
    ]),
    ...mapActions('user', ['getUserModules']),

    async loadData() {
    //  console.log(this.$route.params)

      let response = await this.fetchLeadSingleNote(this.$route.params.idlnote)
      //console.log(response)

      try {
        if (response.status === 200) {
          if (response.data && response.data.leadnote) {
            // Asigna el objeto leadnote a una variable
           this.leadnotedata = response.data.leadnote
          //  console.log('Objeto leadnote:', this.leadnotedata)
          } else {
            throw new Error(
              'El objeto response no contiene la estructura esperada.'
            )
          }
        } else {
          throw new Error('El status no es igual a 200.')
        }
      } catch (error) {
        // Manejo del error (puedes personalizar el mensaje de error aquí)
      // console.error('Error:', error.message)
      }
    },

    cancelNote() {
      this.$router.push(`/admin/leads/${this.$route.params.idlead}/view/`)
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

    async permissionsUserModules() {
      const data = {
        module: 'lead_notes',
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if (permissions.super_admin == false) {
        if (permissions.exist == false) {
          this.$router.push('/admin/dashboard')
        } else {
          const modulePermissions = permissions.permissions[0]
          if (modulePermissions.read == 0) {
            this.$router.push('/admin/dashboard')
          } 
        }
      }
    },
  },
}
</script>
    
 
    