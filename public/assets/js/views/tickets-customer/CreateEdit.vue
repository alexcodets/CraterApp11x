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
                                            class="mb-4"
                                            >
                                            <sw-select
                                                ref="baseSelect"
                                                v-model="formData.dep_id"
                                                :options="departaments"
                                                :invalid="$v.formData.dep_id.$error"
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
                                                v-model="formData.priority"
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
                priority:{
                    value:'H',
                    text: 'High',
                },
                status:
                {
                    value: 'S',
                    text: 'Awaiting Staff Reply',
                },
                user_groups:[],  
                note: '',
                assigned_id:'',
                dep_id:'',
                user_id: 0
            },
        }
    },
    validations: {
        formData: {
            summary: {
                required,
                maxLength: maxLength(120)
            },
            dep_id:{
                required,
            },
            note: {
                required,
                maxLength: maxLength(65000)
            },
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
            if (!this.$v.formData.dep_id.$error) {
                return ''
            }
            if (!this.$v.formData.dep_id.required) {
                return this.$tc('validation.required')
            }
        },
    },
    created() {
        this.getDepartament()
        if (this.isEdit) {
            this.loadEditTicketNote()
        }
    },
    mounted() {
        this.$v.formData.$reset();
    },
    methods: {
        
        ...mapActions('customerTicket', [
            'addCustomerTicket',
            'getListUsersCustomers',
            'fetchCustomerTicket',
            'updateCustomerTicket'
        ]),

        ...mapActions('ticketDepartament', ['fetchDepartaments','fetchDepartament']),
         ...mapActions('users', ['fetchUsers']),
         ...mapActions('roles', ['fetchRoles']),
         ...mapActions('customerTicket', ['getListUsers']),
       

        async loadEditTicketNote() {
            let response = await this.fetchCustomerTicket(this.$route.params.id1)            
            if (response.data) {
                this.formData = { ...this.formData, ...response.data.customerTicket }
            }
             this.formData.status = this.status.filter(
                (element) => element.value == this.formData.status
            )[0]
             this.formData.priority = this.default_prioritys.filter(
                (element) => element.value == this.formData.priority
            )[0]

            this.formData.dep_id= this.formData.ticket_departament
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
            // this.$v.formData.$touch()

            if (this.$v.$invalid) {
                return true
            }
            
            try {
                this.isLoading = true;
  
                this.formData.dep_id = this.formData.dep_id.id;
                // this.formData.assigned_id=this.formData.assigned_id.id;
                this.formData.priority = this.formData.priority.value;
                this.formData.status = 'S';
                this.formData.user_id=this.currentUser.id;

     
                if (this.isEdit) {
                     await this.updateCustomerTicket(this.formData)
                    this.$router.push('/customer/tickets');
                } else {
                     await this.addCustomerTicket(this.formData);
                    this.$router.push('/customer/tickets');
                }

            } catch (err) {
                this.isLoading = false;
                window.toastr['error'](err.message);
            }
        }
    }

}
</script>
