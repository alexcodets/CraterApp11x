<template>
    <!-- Base  -->
    <base-page v-if="isSuperAdmin" class="option-group-create">
        <!--------- Form ---------->
        <form action="" @submit.prevent="submitTicket">
            <!-- Header  -->
            <sw-page-header class="mb-3" :title="pageTitle">

                <template slot="actions">
                      <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/ticket`"
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
                                    </th>
                                    <th
                                    class="py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                                    >
                                    <span>
                                        {{ $t('customer_ticket.assignedTo') }}
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
                                        <sw-input-group
                                            class="mb-4"
                                    >
                                            <sw-select
                                                ref="baseSelect"
                                                v-model="formData.dep_id"
                                                :options="departaments"
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
                                        <sw-input-group
                                            class="mb-4"
                                    >
                                            <sw-select
                                                v-model="formData.assigned_id"
                                                :options="assignedTo"
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
                                    <td class="px-5"> 
                                    <sw-input-group
                                            class="mb-4"
                                    >
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
                                    <td class="px-5">
                                        <sw-input-group
                                            class="mb-4"
                                    >
                                            <sw-select
                                                v-model="formData.status"
                                                :options="status"
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
                           
                        >
                            <sw-textarea
                                v-model="formData.note"
                                rows="5"
                                name="note"
                                style="resize: none;"
                                @input="$v.formData.note.$touch()"
                            />
                        </sw-input-group>

                        <sw-input-group
                            :label="$t('customer_ticket.user')"
                            class="mb-4"
                        >
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
} from '@vue-hero-icons/solid'
import InvoiceStub from "../../stub/invoice";
import TaxStub from "../../stub/tax";
const {
    required,
    minLength,
    maxLength,
} = require('vuelidate/lib/validators')

export default {
    components: {
        ItemsGroupItem,
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
            
            users_select:[],

            
            assignedTo:[],

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

            note: {
                maxLength: maxLength(65000)
            },
        },
    },
    computed:{
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

            if (!this.$v.formData.note.maxLength) {
                return this.$t('validation.description_maxlength')
            }
        },

        getItemUsers(){
             return this.users_select.map((group) => {
                return {
                    ...group,
                    item_user_id: group.id,
                    item_user_name: group.name
                }
            })
        },
    },
    created() {
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
        this.$v.formData.$reset();
    },
    methods: {
        
        ...mapActions('customerTicket', [
            'addCustomerTicket',
            'fetchCustomerTicket',
            'updateCustomerTicket'
        ]),

        ...mapActions('ticketDepartament', ['fetchDepartaments','fetchDepartament']),
         ...mapActions('users', ['fetchUsers']),
         ...mapActions('roles', ['fetchRoles']),
         ...mapActions('customerTicket', ['getListUsers']),
       

        async loadEditTicketNote() {
            let response = await this.fetchCustomerTicket(this.$route.params.id1)
            console.log("loadEditTicketNote",response);

            if (response.data) {
                this.formData = { ...this.formData, ...response.data.customerTicket }
            }
             this.formData.status = this.status.filter(
                (element) => element.value == this.formData.status
            )[0]
             this.formData.priority = this.default_prioritys.filter(
                (element) => element.value == this.formData.priority
            )[0]

            this.formData.dep_id= this.departaments.filter(
                (element) => element.id == this.formData.dep_id
            )[0]

            let res2 = await this.fetchDepartament(this.formData.dep_id.id);
            
            if(res2){
                this.assignedTo =[];
                this.assignedTo = res2.data.departaments.users
                this.formData.assigned_id= this.assignedTo.filter(
                    (element) => element.id == this.formData.assigned_id
                )[0]
            }

            // if (this.formData.users_select) { 
                    this.formData.user_groups = response.data.customerTicket.tickets_groups.map((group) => {
                        return {
                            ...group,
                            item_user_id: group.id,
                            item_user_name: group.name
                        }
                    });
                // }
        
        },

        async getUserDep(val){
            
            let response = await this.fetchDepartament(val.id);

            if (response) {
                this.assignedTo = response.data.departaments.users
            }
            
        },
        
        async getDepartament(){
            let data = {
                name: '',
                orderByField: 'created_at',
                orderBy: 'desc',
            }

            await this.fetchDepartaments(data);
      
    
        },

        async getUsers(){
            let cargaUser= await this.getListUsers();
            console.log('getUsers',cargaUser);
            this.users_select=[...cargaUser.data.list];
        
            /* console.log("Estos son los usuarios",this.users_select); */
        },


        async submitTicket() {
            this.$v.formData.$touch()

            if (this.$v.$invalid) {
                return true
            }
            
            try {
                let response;
                this.isLoading = true;
  
                this.formData.dep_id=this.formData.dep_id.id;
                this.formData.assigned_id=this.formData.assigned_id.id;
                this.formData.priority=this.formData.priority.value;
                this.formData.status=this.formData.status.value;
                this.formData.user_id=this.$route.params.id;

     
                if (this.isEdit) {
                   
                    response = await this.updateCustomerTicket(this.formData)
            
                    if (response.status === 200) {
                        window.toastr['success'](this.$t('customer_ticket.updated_message'));
                        this.$router.push('/admin/customers/'+this.$route.params.id+'/view');
                    }
                    if (response.data.error) {
                        this.isLoading = false;
                        window.toastr['error'](response.data.error);
                        return true;
                    }
                } else {
            
                    response = await this.addCustomerTicket(this.formData);
                    if (response.status === 200) {
                        window.toastr['success'](this.$tc('customer_ticket.created_message'));
                        this.$router.push('/admin/customers/'+this.$route.params.id+'/view');
                    }
                    if (response.data.error) {
                        this.isLoading = false;
                        window.toastr['error'](response.data.error);
                        return true;
                    }
                }

            } catch (err) {
                this.isLoading = false;
            }
        }
    }

}
</script>

<style scoped>

</style>