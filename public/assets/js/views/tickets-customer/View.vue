<template>
    <!-- Base  -->
    <base-page>
            <!-- Header  -->
            <sw-page-header class="mb-3" :title="$t('customer_ticket.view_ticket')">
                <sw-breadcrumb slot="breadcrumbs">
                    <sw-breadcrumb-item to="/customer/tickets" :title="$tc('customer_ticket.title', 2)" />
                    <sw-breadcrumb-item :title="$t('customer_ticket.view_ticket')" active />
                </sw-breadcrumb>   

                <!-- <template slot="actions">
                    editar
                    <sw-button
                        @click="editTicket(ticket)"
                        class="mr-3"
                        variant="primary-outline"
                    >
                        <pencil-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.edit') }}
                    </sw-button>
                        
                    eliminar
                    <sw-button @click="deleteTicket(ticket.id)" type="button" class="btn btn-primary" :loading="isLoading"  :disabled="isLoading">
                        <trash-icon class="h-5 mr-3 text-gray-600" />
                        <span v-if="!isLoading">{{ $tc('general.delete') }}</span>
                    </sw-button>
                </template> -->
            </sw-page-header>

            <div class="w-full">
                <div class="col-span-12">
                    <sw-card v-if="ticket.customer" >
                        <div>
                             <p class="text-gray-500 uppercase sw-section-title">
                                 {{ $t('customer_ticket.information_ticket') }}
                            </p>

                            <div class="flex flex-wrap mt-5 md:mt-7">
                                <div class="w-full md:w-1/2">
                                    <p class="font-bold"> {{ $t('customer_ticket.customer') }} </p>
                                    <p class="text-gray-700 text-sm"> {{ ticket.customer.name }} </p>
                                </div>
                                <div class="w-full md:w-1/2 mt-3 md:mt-0">
                                    <p class="font-bold"> {{ $t('customer_ticket.summary') }} </p>
                                    <p class="text-gray-700 text-sm"> {{ ticket.summary }} </p>
                                </div>
                            </div>

                            <div class="flex flex-wrap mt-5 md:mt-7">
                                <div class="w-full md:w-1/4">
                                    <div class="font-bold py-2"> {{ $tc('customer_ticket.departament') }} </div>
                                    <div>
                                        <p class="text-gray-700 text-sm"> {{ ticket.ticket_departament.name }} </p>
                                    </div>
                                </div>
                                <div class="w-full md:w-1/4">
                                    <div class="font-bold py-2"> {{ $tc('customer_ticket.assignedTo') }} </div>
                                    <div>
                                        <p class="text-gray-700 text-sm"> {{ ticket.assigned.name }} </p>
                                    </div>
                                </div>
                                <div class="w-full md:w-1/4">
                                    <div class="font-bold py-2"> {{ $tc('customer_ticket.priority') }} </div>
                                    <div>
                                        <p class="text-gray-700 text-sm"> {{ prioritysOptions[ticket.priority]}} </p>
                                    </div>
                                </div>
                                <div class="w-full md:w-1/4">
                                    <div class="font-bold py-2"> {{ $tc('customer_ticket.status') }} </div>
                                    <div>
                                        <p class="text-gray-700 text-sm"> {{ statusOptions[ticket.status] }} </p>
                                    </div>
                                </div>
                            </div>

                           
                            <div class="w-full mt-5 md:mt-7">
                                <p class="font-bold"> {{ $t('customer_ticket.details') }} </p>
                                <p class="text-gray-700 text-sm"> {{ ticket.note }} </p>
                            </div>

                            <div class="w-full mt-5 md:mt-7">
                                <p class="font-bold mb-2"> {{ $t('customer_ticket.user') }} </p>
                                <div>
                                    <!-- avatar and name user -->
                                    <div class="flex my-2" v-for="(tr, indexTr) in ticket.users" :key="indexTr">
                                        <div  >
                                            <img class="rounded-full w-8 h-8" :src="tr.avatar" :alt="indexTr">
                                        </div>
                                        <div class="ml-2">
                                            <p class="text-gray-700 text"> {{ tr.name }} </p>
                                            <p class="text-gray-700 text-sm"> {{ tr.email }} </p>
                                        </div>
                                    </div>

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
import {
    PencilIcon,
    TrashIcon,
} from '@vue-hero-icons/outline'
export default {
    components: {
        PencilIcon,
        TrashIcon,
    },
    data() {
        return {
            isLoading: false,
            ticket: {},
            prioritysOptions:{
                E: 'Emergency',
                C: 'Critical',
                H: 'High',
                M: 'Medium',
                L: 'Low',
            },
            statusOptions:{
                S: 'Awaiting Staff Reply',
                C: 'Awaiting Client Reply',
                I: 'In Progress',
                O: 'On Hold',
                M: 'Completed'
            },
            
        }
    },
    computed:{
        ...mapGetters('user', ['currentUser']),
    },
    created() {
        this.getTicket()
    },
    methods: {
        ...mapActions('customerTicket', [
            'fetchCustomerTicket',
            'deleteCustomerTicket'
        ]),
        editTicket({id, user_id}) {
            this.$router.push({
                name: 'tickets.customer.edit',
                params: {
                    id:user_id,
                    id1:id,
                }
            })
        },
        async getTicket() {
            try {
                this.isLoading = true
                const res = await this.fetchCustomerTicket(this.$route.params.id1)
                this.ticket = res.data.customerTicket
            } catch (e) {
                this.ticket = {}
            }finally {
                this.isLoading = false
            }
        },
         async deleteTicket(id) {
            swal({
                title: this.$t('general.are_you_sure'),
                text: this.$tc('customer_ticket.confirm_delete'),
                icon: '/assets/icon/trash-solid.svg',
                buttons: true,
                dangerMode: true,
            }).then(async (willDelete) => {
                try{
                    if (willDelete) {
                        this.isLoading = false
                        let res = await this.deleteCustomerTicket({ ids: [id] })

                        if (res.data.success) {
                            window.toastr['success'](this.$tc('customer_ticket.deleted_message', 1))
                            this.$router.push({ name: 'tickets.customer' })
                        }
                    }
                }catch(e){
                    window.toastr['error'](res.data.message)
                }finally{
                    this.isLoading = false
                }
            })
        },
        
    },
}
</script>
