<template>
  <!-- Base  -->
  <base-page v-if="isSuperAdmin" class="option-group-create">
    <!--------- Form ---------->
    <form action="" @submit.prevent="submitTicket">
      <!-- Header  -->
      <sw-page-header class="mb-3" :title="pageTitle">
        <template slot="actions">
         
          <sw-button :loading="isLoading" :disabled="isLoading" variant="primary-outline"
              class="flex justify-center w-full lg:w-auto mr-2" type="button" size="lg"
              @click="cancelForm()"
          >
            <x-circle-icon v-if="!isLoading" class="mr-2 -ml-1" />
              {{ $t('general.cancel') }}
          </sw-button>

          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="flex justify-center w-full md:w-auto"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{
              isEdit
                ? $t('customer_ticket.update_items_ticket')
                : $t('customer_ticket.save_items_ticket')
            }}
          </sw-button>
        </template>
      </sw-page-header>

      <div class="grid grid-cols-12">
        <div class="col-span-12">
          <sw-card class="mb-8">
            <sw-input-group
              :label="$t('customer_ticket.summary')"
              :error="nameError"
              class="mb-4"
              required
            >
              <sw-input
                v-model.trim="formData.summary"
                :invalid="$v.formData.summary.$error"
                class="mt-2"
                focus
                type="text"
                name="name"
                @input="$v.formData.summary.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customer_ticket.ticket_number')"
              required
            >
              <sw-input
                :prefix="`${ticketPrefix} - `"
                v-model.trim="ticketNumAttribute"
                class="mt-2"
                :disabled="isEdit"
                autocomplete="off"
              />
            </sw-input-group>

            <table
              class="w-full item-table bg-white border border-gray-200 border-solid mt-4"
            >
              <colgroup>
                <col style="width: 26%" />
                <col style="width: 22%" />
                <col style="width: 26%" />
                <col style="width: 26%" />
              </colgroup>
              <thead>
                <tr>
                  <th
                    class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                  >
                    <span>
                      {{ $tc('customer_ticket.departament') }}
                    </span>
                    <span class="text-danger">
                      {{ '*' }}
                    </span>
                  </th>
                  <th
                    class="py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                  >
                    <span>
                      {{ $t('customer_ticket.assignedTo') }}
                    </span>
                    <span class="text-danger">
                      {{ '*' }}
                    </span>
                  </th>
                  <th
                    class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                  >
                    <span>
                      {{ $t('customer_ticket.priority') }}
                    </span>
                  </th>
                  <th
                    class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                  >
                    <span>
                      {{ $t('customer_ticket.status') }}
                    </span>
                  </th>
                </tr>
              </thead>

              <tbody>
                <!-- <tr class="py-3" v-for="(item,index) in days_week" :key="index"> -->
                <tr class="py-3">
                  <td class="px-5">
                    <sw-input-group class="mb-4" :error="selectDepartamentError">
                      <sw-select
                        ref="baseSelect"
                        v-model="dep_id"                       
                        :options="departaments"
                        :invalid="$v.dep_id.$error"
                        :searchable="true"
                        :show-labels="false"
                        :tabindex="16"
                        :allow-empty="true"
                        class="mt-2"
                        label="name"
                        track-by="id"
                        @select="getUserDep"
                      />
                    </sw-input-group>
                  </td>
                  <td>
                    <sw-input-group class="mb-4" :error="selectAssignedError">
                      <sw-select
                        v-model="assigned_id"
                        :options="assignedTo"
                        :searchable="true"
                        :show-labels="false"
                        :tabindex="16"
                        :allow-empty="true"
                        class="mt-2"
                        label="name"
                        track-by="id"
                        :invalid="$v.assigned_id.$error"
                      />
                    </sw-input-group>
                  </td>
                  <td class="px-5">
                    <sw-input-group class="mb-4">
                      <sw-select
                        v-model="priority"
                        :options="default_prioritys"
                        :searchable="true"
                        :show-labels="false"
                        :tabindex="16"
                        :allow-empty="true"
                        class="mt-2"
                        label="text"
                        track-by="value"
                      />
                    </sw-input-group>
                  </td>
                  <td class="px-5">
                    <sw-input-group class="mb-4">
                      <sw-select
                        v-model="status"
                        :options="status_options"
                        :searchable="true"
                        :show-labels="false"
                        :tabindex="16"
                        :allow-empty="true"
                        class="mt-2"
                        label="text"
                        track-by="value"
                      />
                    </sw-input-group>
                  </td>
                </tr>
              </tbody>
            </table>

            <sw-input-group
              :label="$t('customer_ticket.details')"
              :error="descriptionError"
              class="mb-4 mt-4"
              required
            >
              <sw-textarea
                v-model="formData.note"
                rows="5"
                name="note"
                style="resize: none"               
                :invalid="$v.formData.note.$error"
              />
            </sw-input-group>

            <sw-input-group :label="$t('customer_ticket.user')" class="mb-4">
              <sw-select
                v-model="formData.user_groups"
                :options="getItemUsers"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :multiple="true"
                class="mt-2"
                track-by="item_user_id"
                label="item_user_name"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customer_ticket.services')"
              class="mb-4"
            >
              <sw-select
                v-model="formData.services"
                :options="getServices"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :multiple="true"
                class="mt-2"
                track-by="service_id"
                label="service_code"
                :tabindex="7"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customer_ticket.pbx_services')"
              class="mb-4"
            >
              <sw-select
                v-model="formData.pbxServices"
                :options="getPbxServices"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :multiple="true"
                class="mt-2"
                track-by="service_id"
                label="service_code"
                :tabindex="7"
              />
            </sw-input-group>

          </sw-card>
        </div>
      </div>
    </form>
  </base-page>
</template>

<script>
import draggable from 'vuedraggable'
import ItemsGroupItem from './Item'
import ItemGroupStub from '../../stub/itemGroup'
import { mapActions, mapGetters } from 'vuex'
import Guid from 'guid'
import {
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
  XCircleIcon
} from '@vue-hero-icons/solid'
import InvoiceStub from '../../stub/invoice'
import TaxStub from '../../stub/tax'
const { required, minLength, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    ItemsGroupItem,
    draggable,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
    XCircleIcon
  },
  data() {
    return {
      isLoading: false,

      default_prioritys: [
        {
          value: 'E',
          text: this.$t('customer_ticket.priority_options.emergency')
        },
        {
          value: 'C',
          text: this.$t('customer_ticket.priority_options.critical')
        },
        {
          value: 'H',
          text: this.$t('customer_ticket.priority_options.high')
        },
        {
          value: 'M',
          text: this.$t('customer_ticket.priority_options.medium')
        },
        {
          value: 'L',
          text: this.$t('customer_ticket.priority_options.low')
        },
      ],

      status_options: [
        {
          value: 'S',
          text: this.$t('customer_ticket.status_options.staff')
        },
        {
          value: 'C',
          text: this.$t('customer_ticket.status_options.client')
        },
        {
          value: 'I',
          text: this.$t('customer_ticket.status_options.progress')
        },
        {
          value: 'O',
          text: this.$t('customer_ticket.status_options.hold')
        },
        {
          value: 'M',
          text: this.$t('customer_ticket.status_options.completed')
        },
      ],

      users_select: [],

      assignedTo: [],

      formData: {
        summary: '',        
        user_groups: [],
        services: [],
        pbxServices: [],
        note: '',
            
        user_id: 0,
      },
      dep_id: '',
      assigned_id: '',   
      priority: {
        value: 'H',
        text: this.$t('customer_ticket.priority_options.high')
      },
      status: {
        value: 'S',
        text: this.$t('customer_ticket.status_options.staff')
      }, 
      ticketPrefix: null,
      ticketNumAttribute: null,
      services: [],
      pbxServices: [],
    }
  },
  validations: {
    formData: {
      summary: {
        required,
        maxLength: maxLength(120),
      },   
      note: {
        required,
        minLength: minLength(10),
        maxLength: maxLength(65000),
      },
    },    
    dep_id: {
        required,
    }, 
    assigned_id: {
          required,
    },
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('ticketDepartament', ['departaments']),
    ...mapGetters('users', ['users']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    pageTitle() {
      if (this.$route.name === 'customers.edit-ticket') {
        return this.$t('customer_ticket.edit_ticket')
      }
      return this.$t('customer_ticket.new_ticket')
    },

    isEdit() {
      if (this.$route.name === 'customers.edit-ticket') {
        return true
      }
      return false
    },

    nameError() {
      if (!this.$v.formData.summary.$error) {
        return ''
      }

      if (!this.$v.formData.summary.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.summary.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.summary.$params.minLength.min,
          { count: this.$v.formData.summary.$params.minLength.min }
        )
      }

      if (!this.$v.formData.summary.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },

    descriptionError() {
      if (!this.$v.formData.note.$error) {
        return ''
      }

      if (!this.$v.formData.note.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.note.minLength) {
        return this.$tc(
          'validation.min_length',
          this.$v.formData.note.$params.minLength.min,
          { count: this.$v.formData.note.$params.minLength.min }
        )
      }

      if (!this.$v.formData.note.maxLength) {
        return this.$t('validation.notes_maxlength')
      }
    },

    selectDepartamentError() {
      if (!this.$v.dep_id.$error) {
        return ''
      }
      if (!this.$v.dep_id.required) {
        return this.$tc('validation.required')
      }
    },

    selectAssignedError() {
      if (!this.$v.assigned_id.$error) {
        return ''
      }
      if (!this.$v.assigned_id.required) {
        return this.$tc('validation.required')
      }
    },

    getItemUsers() {
      return this.users_select.map((group) => {
        return {
          ...group,
          item_user_id: group.id,
          item_user_name: group.name,
        }
      })
    },
    
    getServices() {
      return this.services.map((service) => {
        return {
          service_id: service.id,
          service_code: service.code,
        }
      })
    },

    getPbxServices() {
      return this.pbxServices.map((service) => {
        return {
          service_id: service.id,
          service_code: service.pbx_services_number,
        }
      })
    },

  },
  created() {
    this.getPrefix()
    this.fetchServices()
    this.fetchPbxServices()
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
    this.getDepartament()
    this.getUsers()
    if (this.isEdit) {
      this.loadEditTicketNote()
    }
  },
  mounted() {
    this.$v.formData.$reset()    
  },
  methods: {
    ...mapActions('customerTicket', [
      'addCustomerTicket',
      'fetchCustomerTicket',
      'updateCustomerTicket',
      'getServicesByCustomer',
      'getPbxServicesByCustomer',
    ]),

    ...mapActions('ticketDepartament', [
      'fetchDepartaments',
      'fetchDepartament',
    ]),
    ...mapActions('users', ['fetchUsers']),
    ...mapActions('roles', ['fetchRoles']),
    ...mapActions('customerTicket', ['getListUsers']),

    ...mapActions('company', ['fetchCompanySettings']),

    cancelForm() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.cancel_text'),
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          this.$router.go(-1)
        }
      })
    },

    async loadEditTicketNote() {
      let response = await this.fetchCustomerTicket(this.$route.params.id1)

      if (response.data) {
        this.formData = { ...this.formData, ...response.data.customerTicket }
      }
      this.status = this.status_options.filter(
        (element) => element.value == response.data.customerTicket.status
      )[0]
      this.priority = this.default_prioritys.filter(
        (element) => element.value == response.data.customerTicket.priority
      )[0]

      this.dep_id = this.departaments.filter(
        (element) => element.id == response.data.customerTicket.dep_id
      )[0]

      let res2 = await this.fetchDepartament(this.dep_id.id)

      if (res2) {
        this.assignedTo = []
        this.assignedTo = res2.data.departaments.users
        this.assigned_id = this.assignedTo.filter(
          (element) => element.id == response.data.customerTicket.assigned_id
        )[0]
      }

      // if (this.formData.users_select) {
      this.formData.user_groups =
        response.data.customerTicket.tickets_groups.map((group) => {
          return {
            ...group,
            item_user_id: group.id,
            item_user_name: group.name,
          }
        })
      // }
      
      this.formData.services = this.formData.services.map((service) => {
        return {
          service_id: service.id,
          service_code: service.code,
        }
      })

      this.formData.pbxServices = this.formData.pbx_services.map((service) => {
        return {
          service_id: service.id,
          service_code: service.pbx_services_number,
        }
      })
      
    },

    async fetchServices() {
      let response = await this.getServicesByCustomer({
        customer_id: this.$route.params.id,
      })
      this.services = [...response.data.services]
/*
      if (this.prevRoute.name === 'customers.package-view') {
        this.services.forEach((service) => {
          if (service.id == this.prevRoute.params.customer_package_id) {
            this.formData.services.push({
              service_id: service.id,
              service_code: service.code,
            })
          }
        })
      }*/
    },

    async fetchPbxServices() {
      let response = await this.getPbxServicesByCustomer({
        customer_id: this.$route.params.id,
      })
      this.pbxServices = [...response.data.pbxServices]
      /*
      if (this.prevRoute.name === 'customers.pbx-service-view') {
        this.pbxServices.forEach((service) => {
          if (service.id == this.prevRoute.params.pbx_service_id) {
            this.formData.pbxServices.push({
              service_id: service.id,
              service_code: service.pbx_services_number,
            })
          }
        })
      }*/
    },

    async getUserDep(val) {
      let response = await this.fetchDepartament(val.id)

      if (response) {
        this.assignedTo = response.data.departaments.users
      }
    },

    async getDepartament() {
      let data = {
        name: '',
        orderByField: 'created_at',
        orderBy: 'desc',
      }

      await this.fetchDepartaments(data)
    },

    async getUsers() {
      let cargaUser = await this.getListUsers()
      this.users_select = [...cargaUser.data.list]

    },

    async getPrefix() {
      let response = await axios.get('/api/v1/next-number?key=TTW')   
      if(response.data)
      {
        this.ticketPrefix = response.data.prefix
        this.ticketNumAttribute = response.data.nextNumber
      }
    },

    async submitTicket() {

      this.$v.dep_id.$touch()
      this.$v.assigned_id.$touch()
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      let text = ''
      if(this.isEdit){
        text = 'customer_ticket.ticket_edit'
      }else{
        text = 'customer_ticket.ticket_create'
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t(text),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
          if (value)
          {
            try {
              let response
              this.isLoading = true
              let formData2 = this.formData

              // Data
              formData2.dep_id = this.dep_id.id
              formData2.assigned_id = this.assigned_id.id
              formData2.priority = this.priority.value
              formData2.status = this.status.value
              formData2.user_id = this.$route.params.id
              formData2.ticket_number_selected = this.ticketPrefix + '-' + this.ticketNumAttribute

              if (this.isEdit) {
                response = await this.updateCustomerTicket(formData2)

                if (response.status === 200) {
                  window.toastr['success'](this.$t('customer_ticket.updated_message'))
                  this.$router.push(               
                    `/admin/customers/${this.$route.params.id}/ticket`
                  )
                }
                if (response.data.error) {
                  this.isLoading = false
                  window.toastr['error'](response.data.error)
                  return true
                }
              } else {
                response = await this.addCustomerTicket(formData2)

                if(!response.data.success)
                {                
                  this.isLoading = false
                  window.toastr['error'](response.data.message)
                  if(response.data.message == "Ticket number already exists")
                  {
                    this.alertTicketNumberAlreadyExists()     
                  }
                  return true
                }

                if (response.status === 200) {
                  window.toastr['success'](
                    this.$tc('customer_ticket.created_message')
                  )
                  this.$router.push(               
                    `/admin/customers/${this.$route.params.id}/ticket`
                  )
                }
                if (response.data.error) {
                  this.isLoading = false
                  window.toastr['error'](response.data.error)
                  return true
                }
              }
            } catch (error) {
              this.isLoading = false
            }
          }
        })
    },

    // Ticket Number Exists
    alertTicketNumberAlreadyExists()
      {        
        this.$swal({
          title: this.$t('general.ticket_number_exists_title'),          
          text:  this.$t('general.ticket_number_exists_text'),          
          icon: 'warning',
          showCancelButton: true,          
          confirmButtonText: this.$t('general.automatic'), 
          confirmButtonColor: "#5851D8",
          cancelButtonText: this.$t('general.manual'),
          //cancelButtonColor: "#efefef", 
          //showCloseButton: true,
          showLoaderOnConfirm: true
          }).then((result) => {
            if(result.value)
            {
              this.generateAutomaticTicketNumber()              
            } 
        })        
      },

      async generateAutomaticTicketNumber()
      {    
        let response_next_number = await axios.get('/api/v1/next-number?key=TTW')

        this.ticketNumAttribute = response_next_number.data.nextNumber

        let formData2 = this.formData

        // Data
        formData2.dep_id = this.dep_id.id
        formData2.assigned_id = this.assigned_id.id
        formData2.priority = this.priority.value
        formData2.status = this.status.value
        formData2.user_id = this.$route.params.id
        formData2.ticket_number_selected = this.ticketPrefix + '-' + this.ticketNumAttribute
        
        this.isLoading = true
        let response = await this.addCustomerTicket(formData2)

        if(!response.data.success)
        {                
          this.isLoading = false
          window.toastr['error'](response.data.message)         
          return true
        }

        if (response.status === 200)
        {
          window.toastr['success'](this.$tc('customer_ticket.created_message'))
          this.$router.push(`/admin/customers/${this.$route.params.id}/ticket`)
        }  

      },
      //
  },
}
</script>

<style scoped></style>
