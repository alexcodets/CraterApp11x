<template>
    <!-- Base  -->
    <base-page class="option-group-create">
        <!--------- Form ---------->
        <form action="" @submit.prevent="submitTicket">
            <!-- Header  -->
            <sw-page-header class="mb-3" :title="pageTitle">

                <template slot="actions">
                      <sw-button
                            tag-name="router-link"
                            :to="`/customer/tickets`"
                            class="mr-3"
                            variant="primary-outline"
                        >
                            {{ $t('customer_ticket.cancel') }}
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
                            {{ isEdit ? $t('customer_ticket.update_items_ticket') : $t('customer_ticket.save_items_ticket') }}
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
                :disabled="true"
                autocomplete="off"
                @input="$v.ticketNumAttribute.$touch()"
              />
            </sw-input-group>

                        <table class="w-full item-table bg-white border border-gray-200 border-solid">
                        <thead>
                                <tr>
                                    <th
                                    class="px-2 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                                    >
                                    <span>
                                        {{ $tc('customer_ticket.departament') }}
                                    </span>
                                    <span class="text-danger">
                                        {{ '*' }}
                                    </span>
                                    </th>
                                    <th
                                    class="px-2 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                                    >
                                    <span>
                                        {{ $t('customer_ticket.priority') }}
                                    </span>
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <!-- <tr class="py-3" v-for="(item,index) in days_week" :key="index"> -->
                                <tr class="py-3">
                                    <td class="px-2">
                                        <sw-input-group
                                            class="mb-4" :error="selectDepartamentError"
                                            >
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
                                            />
                                    </sw-input-group>

                                    </td>
                                    <td class="px-2"> 
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
                                </tr>
                            </tbody>
                        </table>

                        <sw-input-group
                            :label="$t('customer_ticket.details')"
                            :error="descriptionError"
                            class="mb-4 mt-4 "
                            required
                           
                        >
                            <sw-textarea
                                v-model="formData.note"
                                :invalid="$v.formData.note.$error"
                                rows="5"
                                name="note"
                                style="resize: none;"
                                @input="$v.formData.note.$touch()"
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
/* import ItemsGroupItem from '../../customers/Item ItemsGroupItem,'
import ItemGroupStub from '../../../stub/itemGroup' */
import { mapActions, mapGetters } from 'vuex'

import {
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
} from '@vue-hero-icons/solid'
import twitterVue from '../../components/icon/twitter.vue'

const {
    required,
    minLength,
    maxLength,
} = require('vuelidate/lib/validators')

export default {
    components: {
        draggable,
        ChevronDownIcon,
        PencilIcon,
        ShoppingCartIcon,
        HashtagIcon,
    },
    data() {
        return {
            isLoading: false,

            default_prioritys:[
                {
                    value: 'E',
                    text: 'Emergency',
                },
                {
                    value: 'C',
                    text: 'Critical',
                },
                {
                    value: 'H',
                    text: 'High',
                },
                {
                    value: 'M',
                    text: 'Medium',
                },
                {
                    value: 'L',
                    text: 'Low',
                },
            ],

            status:[
                {
                    value: 'S',
                    text: 'Awaiting Staff Reply',
                },
                {
                    value: 'C',
                    text: 'Awaiting Client Reply',
                },
                {
                    value: 'I',
                    text: 'In Progress',
                },
                {
                    value: 'O',
                    text: 'On Hold',
                },
                {
                    value: 'M',
                    text: 'Completed',
                },
            ],
            formData: {
                summary: '',                
                status:
                {
                    value: 'S',
                    text: 'Awaiting Staff Reply',
                },
                user_groups:[],  
                note: '',
                assigned_id:'',
                user_id: 0,
                services: [],
                pbxServices: [],
                ticket_number: null,
            },
            //
            dep_id:'',
            priority:{
                    value:'H',
                    text: 'High',
            },
            //
            services: [],
            pbxServices: [],
            prevRoute: null,
            ticketNumAttribute: null,
            ticketPrefix: '',
        }
    },
    beforeRouteEnter(to, from, next) {
        next(vm => {
            vm.prevRoute = from
        })
    },
    validations: {
        formData: {
            summary: {
                required,
                maxLength: maxLength(120)
            },            
            note: {
                required,
                maxLength: maxLength(65000)
            },   
            ticket_number: {
                required,
            },
        },
        dep_id:{
                required,
        },
    },
    computed:{
        ...mapGetters('ticketDepartament', ['departaments']),
        ...mapGetters('users', ['users']),
        ...mapGetters('user', ['currentUser']),
        
        pageTitle() {
            if (this.$route.name === 'tickets.customer.edit') {
                return this.$t('customer_ticket.edit_ticket')
            }
            return this.$t('customer_ticket.new_ticket')
        },

        isEdit() {
            if (this.$route.name === 'tickets.customer.edit') {
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

            if (!this.$v.formData.note.maxLength) {
                return this.$t('validation.description_maxlength')
            }
        },

        selectDepartamentError(){
            if (!this.$v.dep_id.$error) {
                return ''
            }
            if (!this.$v.dep_id.required) {
                return this.$tc('validation.required')
            }
        },

        getServices() {
            return this.services.map((service) => {
                return {
                    service_id: service.id,
                    service_code: service.code
                }
            })
        },

        getPbxServices() {
            return this.pbxServices.map((service) => {
                return {
                    service_id: service.id,
                    service_code: service.pbx_services_number
                }
            })
        },
    },
    created() {
        this.getDepartament()

        if (this.isEdit== false) {
    this.getPrefix()
  }
        if (this.isEdit) {
            this.loadEditTicketNote()
        }
        this.fetchServices()
        this.fetchPbxServices()
    },
    mounted() {
        this.$v.formData.$reset();
    },
    methods: {
        ...mapActions('company', ['fetchCompanySettings']),
        ...mapActions('customerTicket', [
            'addCustomerTicket',
            'getListUsersCustomers',
            'fetchCustomerTicket',
            'updateCustomerTicket',
            'getServicesByCustomer',
            'getPbxServicesByCustomer'
        ]),

        ...mapActions('ticketDepartament', ['fetchDepartaments','fetchDepartament']),
         ...mapActions('users', ['fetchUsers']),
         ...mapActions('roles', ['fetchRoles']),
         ...mapActions('customerTicket', ['getListUsers']),
       

        async loadEditTicketNote() {
            let response = await this.fetchCustomerTicket(this.$route.params.id1)            
            if (response.data) {
                this.formData = { ...this.formData, ...response.data.customerTicket }
                let stringvec= response.data.customerTicket.ticket_number.split('-');
        this.ticketNumAttribute=stringvec[1]
        this.ticketPrefix=stringvec[0]
            }
             this.formData.status = this.status.filter(
                (element) => element.value == this.formData.status
            )[0]
             this.priority = this.default_prioritys.filter(
                (element) => element.value == this.priority
            )[0]

            this.dep_id= this.formData.ticket_departament
        },
        
        async getDepartament(){
            let data = {
                name: '',
                orderByField: 'created_at',
                orderBy: 'desc',
                containsUsers: true,
            }
            await this.fetchDepartaments(data);
        },

        async submitTicket() {
            
            this.$v.dep_id.$touch()           
            this.$v.formData.$touch()

            if (this.$v.$invalid) {
                return true
            }
            
            try {
                this.isLoading = true;  
                let formData2 = this.formData

                // Data
                formData2.dep_id = this.dep_id.id
                //formData2.assigned_id = this.assigned_id.id
                formData2.priority = this.priority.value
                formData2.status = 'S';
                formData2.user_id = this.currentUser.id
                formData2.ticket_number_selected = this.ticketPrefix + '-' + this.ticketNumAttribute
     
                if (this.isEdit) {
                    await this.updateCustomerTicket(formData2)
                    this.$router.push('/customer/tickets');
                } else {
                    await this.addCustomerTicket(formData2);
                    this.$router.push('/customer/tickets');
                }

            } catch (err) {
                this.isLoading = false;
                window.toastr['error'](err.message);
            }
        },

        async fetchServices() {
            let response = await this.getServicesByCustomer({ customer_id: this.currentUser.id })
            this.services = [ ...response.data.services ]

            if (this.prevRoute.name === 'customer.package-view') {
                this.services.forEach((service) => {
                    if (service.id == this.prevRoute.params.customer_package_id) {
                        this.formData.services.push({
                            service_id: service.id,
                            service_code: service.code
                        })
                    }
                })
            }
        },

        async fetchPbxServices() {
            let response = await this.getPbxServicesByCustomer({ customer_id: this.currentUser.id })
            this.pbxServices = [ ...response.data.pbxServices ]

            if (this.prevRoute.name === 'customer.pbx-service-view') {
                this.pbxServices.forEach((service) => {
                    if (service.id == this.prevRoute.params.pbx_service_id) {
                        this.formData.pbxServices.push({
                            service_id: service.id,
                            service_code: service.pbx_services_number
                        })
                    }
                })
            }
        },
        async getPrefix() {
      let response = await this.fetchCompanySettings(['TTW_prefix'])
      let response1 = await axios.get('/api/v1/next-number?key=TTW')
      this.ticketPrefix = 'TTW'

      if (response1.data) {
        this.ticketNumAttribute = response1.data.nextNumber
        this.ticketPrefix = response1.data.prefix
        this.formData.ticket_number =
          this.ticketPrefix + '-' + this.ticketNumAttribute
      }
    },

    }

}
</script>
