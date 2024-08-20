<template>
    <!-- Base  -->
    <base-page v-if="isSuperAdmin" class="option-group-create">
        <!--------- Form ---------->
        <form action="" @submit.prevent="submitItemGroup">
            <!-- Header  -->
            <sw-page-header class="mb-3" :title="pageTitle">
                <sw-breadcrumb slot="breadcrumbs">
                    <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
                    <sw-breadcrumb-item to="/admin/tickets/departaments" :title="$t('tickets.title')" />
                    <sw-breadcrumb-item
                        v-if="$route.name === 'tickets.departaments.edit'"
                        to="#"
                        :title="$t('tickets.departaments.edit_departament')"
                        active
                    />
                    <sw-breadcrumb-item
                        v-else
                        to="#"
                        :title="$t('tickets.departaments.new_departament')"
                        active
                    />
                </sw-breadcrumb>
                
                <template slot="actions">
                    <!-- <sw-button
                        :loading="isLoading"
                        :disabled="isLoading"
                        variant="primary"
                        type="submit"
                        size="lg"
                        class="flex justify-center w-full md:w-auto"
                    >
                        <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
                        {{ isEdit ? $t('tickets.departaments.update_departament') : $t('tickets.departaments.save_departament') }}
                    </sw-button> -->
                </template>
            </sw-page-header>

            <div class="grid grid-cols-12">
                <div class="col-span-12">
                    <sw-card class="mb-8">
                        <sw-input-group
                            :label="$t('tickets.departaments.name')"
                            :error="nameError"
                            class="mb-4"
                            required
                        >
                            <sw-input
                                v-model.trim="formData.name"
                                :invalid="$v.formData.name.$error"
                                class="mt-2"
                                focus
                                type="text"
                                name="name"
                                @input="$v.formData.name.$touch()"
                            />
                        </sw-input-group>
                        <sw-input-group
                            :label="$t('tickets.departaments.description')"
                            :error="descriptionError"
                            class="mb-4"
                        >
                            <sw-editor
                                v-model="formData.description"
                                :set-editor="formData.description"
                                rows="2"
                                name="description"
                                @input="$v.formData.description.$touch()"
                            />
                        </sw-input-group>
                        <div class="flex my-8">
                            <div class="relative w-12">
                                <sw-checkbox
                                    v-model="formData.client_permission"
                                    class="absolute"
                                    
                                />
                            </div>

                            <div class="ml-4">
                                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                    {{ $t('tickets.departaments.client_permission_checkbox') }}
                                </p>
                            </div>
                        </div>
                        <sw-input-group
                            :label="$t('tickets.departaments.email')"
                            class="mb-4"
                            :error="emailError"
                            required
                            >
                            <sw-input
                                :invalid="$v.formData.email.$error"
                                v-model.trim="formData.email"
                                class="mt-2"
                                type="text"
                                name="email"
                                tabindex="3"
                                @input="$v.formData.email.$touch()"
                            />
                        </sw-input-group>
<!-- Oculto -->
                    <div v-if="contenido">
                        <div class="flex my-8">
                            <div class="relative w-12">
                                <sw-checkbox
                                    v-model="formData.sender_override"
                                    class="absolute"
                                    
                                />
                            </div>

                            <div class="ml-4">
                                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                    {{ $t('tickets.departaments.sender_override_checkbox') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex my-8">
                            <div class="relative w-12">
                                <sw-checkbox
                                    v-model="formData.send_emails" 
                                    class="absolute"
                                    
                                />
                            </div>

                            <div class="ml-4">
                                <p class="p-0 mb-1 text-base leading-snug text-black box-title">  {{ formData.send_emails}}
                                    {{ $t('tickets.departaments.send_emails_checkbox') }}
                                </p>
                            </div>
                        </div>

                         <div class="flex my-8">
                            <div class="relative w-12">
                                <sw-checkbox
                                    v-model="formData.automatically_transition_admin"
                                    class="absolute"
                                    
                                />
                            </div>

                            <div class="ml-4">
                                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                    {{ $t('tickets.departaments.transition_ticket_checkbox') }}
                                </p>
                            </div>
                        </div>

                        <sw-input-group
                                :label="$t('tickets.departaments.default_priority')"
                                class="mb-4"
                                >
                                <sw-select
                                    v-model="formData.default_priorit"
                                    :options="default_prioritys"
                                    :searchable="true"
                                    :show-labels="false"
                                    :tabindex="16"
                                    :allow-empty="true"
                                    class="mt-2"
                                    :placeholder="$t('tickets.departaments.default_priority')"
                                    label="text"
                                    track-by="value"
                                />
                        </sw-input-group>

                        <sw-input-group
                                :label="$t('tickets.departaments.email_handling')"
                                class="mb-4"
                                >
                                <sw-select
                                    v-model="formData.email_handlin"
                                    :options="email_handlings"
                                    :searchable="true"
                                    :show-labels="false"
                                    :tabindex="16"
                                    :allow-empty="true"
                                    class="mt-2"
                                    :placeholder="$t('tickets.departaments.email_handling')"
                                    label="text"
                                    track-by="value"
                                />
                        </sw-input-group>

                        <sw-input-group
                                :label="$t('tickets.departaments.automatically_close_tickets')"
                                class="mb-4"
                                >
                                <sw-select
                                    v-model="formData.automatically_clos"
                                    :options="days"
                                    :searchable="true"
                                    :show-labels="false"
                                    :tabindex="16"
                                    :allow-empty="true"
                                    class="mt-2"
                                    :placeholder="$t('tickets.departaments.automatically_close_tickets')"
                                    label="text"
                                    track-by="value"
                                />
                        </sw-input-group>

                        <sw-input-group
                                :label="$t('tickets.departaments.automatically_delete_tickets')"
                                class="mb-4"
                                >
                                <sw-select
                                    v-model="formData.automatically_delet"
                                    :options="days"
                                    :searchable="true"
                                    :show-labels="false"
                                    :tabindex="16"
                                    :allow-empty="true"
                                    class="mt-2"
                                    :placeholder="$t('tickets.departaments.automatically_delete_tickets')"
                                    label="text"
                                    track-by="value"
                                />
                        </sw-input-group>

                        <sw-input-group
                            :label="$t('providers.status')"
                            class="mb-4"
                            >
                            <sw-select
                                v-model="formData.statu"
                                :options="status"
                                :searchable="true"
                                :show-labels="false"
                                :tabindex="16"
                                :allow-empty="true"
                                class="mt-2"
                                :placeholder="$t('providers.status')"
                                label="text"
                                track-by="value"
                            />
                        </sw-input-group>

                        <sw-input-group
                            :label="$t('tickets.departaments.schedule')"
                            class="mb-4"
                        >
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
                                        {{ $tc('tickets.departaments.day') }}
                                    </span>
                                    </th>
                                    <th
                                    class="py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                                    >
                                    <span>
                                        {{ $t('tickets.departaments.all_day') }}
                                    </span>
                                    </th>
                                    <th
                                    class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                                    >
                                    <span>
                                        {{ $t('tickets.departaments.start_time') }}
                                    </span>
                                    </th>
                                    <th
                                    class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                                    >
                                    <span>
                                        {{ $t('tickets.departaments.end_time') }}
                                    </span>
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr class="py-3" v-for="(item,index) in days_week" :key="index">
                                    <td class="px-5">{{ item }}</td>
                                    <td>
                                         <sw-checkbox
                                            v-model="formData.day_check"
                                            :value="item" 
                                        />
                                    </td>
                                    <td class="px-5"> 
                                        <sw-select
                                            v-model="formData.star_tim[index]"
                                            :options="schedule"
                                            :searchable="true"
                                            :show-labels="false"
                                            :tabindex="16"
                                            :allow-empty="true"
                                            class="mt-2"
                                            label="text"
                                            track-by="value"
                                        />
                                    </td>
                                    <td class="px-5">
                                        <sw-select
                                            v-model="formData.end_tim[index]"
                                            :options="schedule"
                                            :searchable="true"
                                            :show-labels="false"
                                            :tabindex="16"
                                            :allow-empty="true"
                                            class="mt-2"
                                            label="text"
                                            track-by="value"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Aqui -->
                        <sw-input-group
                            :label="$t('tickets.departaments.receive_tickets_emails')"
                            class="mb-4"
                            :error="emailError"
                            required
                            >
                        </sw-input-group>

                        <div class="flex">
                                <div class="flex my-8" v-for="(item,index) in options_settings" :key="index">
                                        <div class="relative w-8">
                                            <sw-checkbox
                                                v-model="formData.receive_tickets_emailss"
                                                :value="item" 
                                                class="absolute"
                                            />
                                        </div>

                                        <div>
                                            <p class="p-0 mb-1 mr-4 text-base leading-snug text-black box-title">
                                                {{ item }}
                                            </p>
                                        </div>
                                </div>
                        </div>
                        
                        <!-- Aqui -->
                        <sw-input-group
                            :label="$t('tickets.departaments.receive_mobile_tickets_emails')"
                            class="mb-4"
                            :error="emailError"
                            required
                            >
                        </sw-input-group>

                        <div class="flex">
                                <div class="flex my-8" v-for="(item,index) in options_settings" :key="index">
                                        <div class="relative w-8">
                                            <sw-checkbox
                                                v-model="formData.receive_mobile_tickets_emailss"
                                                 :value="item" 
                                                class="absolute"
                                            />
                                        </div>

                                        <div>
                                            <p class="p-0 mb-1 mr-4  text-base leading-snug text-black box-title">
                                                {{ item }}
                                            </p>
                                        </div>
                                </div>
                        </div>
                        <!-- Aqui -->
                        <sw-input-group
                            :label="$t('tickets.departaments.receive_tickets_messenger_notifications')"
                            class="mb-4"
                            :error="emailError"
                            required
                            >
                        </sw-input-group>

                        <div class="flex">
                                <div class="flex my-8" v-for="(item,index) in options_settings" :key="index">
                                        <div class="relative w-8" >
                                           
                                            <sw-checkbox
                                                v-model="formData.receive_tickets_messenger_notificationss"
                                                :value="item" 
                                            />
                                        </div>

                                        <div>
                                            <p class="p-0 mb-1 mr-4  text-base leading-snug text-black box-title">
                                                {{ item }}
                                            </p>
                                        </div>
                                </div>
                        </div>     
                    </div>                           
                        <div class="mt-6 mb-4">
                            <sw-button
                                :loading="isLoading"
                                :disabled="isLoading"
                                variant="primary"
                                type="submit"
                                size="lg"
                                class="flex justify-center w-full md:w-auto"
                            >
                                <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
                                {{ isEdit ? $t('tickets.departaments.update_departament') : $t('tickets.departaments.save_departament') }}
                            </sw-button>
                        </div>
                    </sw-card>
                </div>
            </div>
            <template slot="actions">
                    <sw-button
                        :loading="isLoading"
                        :disabled="isLoading"
                        variant="primary"
                        type="submit"
                        size="lg"
                        class="flex justify-center w-full md:w-auto"
                    >
                        <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
                        {{ isEdit ? $t('tickets.departaments.update_departament') : $t('tickets.departaments.save_departament') }}
                    </sw-button>
            </template>
        </form>
    </base-page>
</template>

<script>

import { mapActions, mapGetters } from 'vuex'

import {
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
} from '@vue-hero-icons/solid'
import LayoutLoginVue from '../../layouts/LayoutLogin.vue'

const {
    required,
    minLength,
    maxLength,
    email,
} = require('vuelidate/lib/validators')

export default {
    components: {
        ChevronDownIcon,
        PencilIcon,
        ShoppingCartIcon,
        HashtagIcon,
    },
    data() {
        return {
            isLoading: false,
            contenido:false,
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
            email_handlings:[
                {
                    value:'N',
                    text:'None'
                },
                {
                    value:'P',
                    text:'Piping'
                },
                {
                    value:'O',
                    text:'POP3'
                },
                {
                    value:'I',
                    text:'IMAP'
                },
            ],
            status: [
                {
                    value: 'A',
                    text: 'Active',
                },
                {
                    value: 'I',
                    text: 'Inactive',
                },
            ],

            formData: {
                id:0,
                name: '',
                description: '',
                nuevo:true,
                client_permission:false,
                email: '',
                sender_override:false,
                day_check:[],
                receive_tickets_emailss:[],
                receive_mobile_tickets_emailss:[],
                receive_tickets_messenger_notificationss:[],
                send_emails:false,
                automatically_transition_admin:false,
                default_priorit:{
                    value:'E',
                    text: 'Emergency',
                },
                email_handlin:{
                    value: 'N',
                    text: 'None',
                },
                statu: {
                    value: 'A',
                    text: 'Active',
                },
                automatically_delet: {
                    value: 0,
                    text: 'Never',
                }, 
         
                star_tim:[{ "value": 0, "text": "--:--:--" },{ "value": 0, "text": "--:--:--" },{ "value": 0, "text": "--:--:--" },{ "value": 0, "text": "--:--:--" },{ "value": 0, "text": "--:--:--" },{ "value": 0, "text": "--:--:--" },{ "value": 0, "text": "--:--:--" }],
                end_tim:[{ "value": 0, "text": "--:--:--" },{ "value": 0, "text": "--:--:--" },{ "value": 0, "text": "--:--:--" },{ "value": 0, "text": "--:--:--" },{ "value": 0, "text": "--:--:--" },{ "value": 0, "text": "--:--:--" },{ "value": 0, "text": "--:--:--" }],
                automatically_clos: {
                    value: 0,
                    text: 'Never',
                },
                status: '',
                star_time:'',
                end_time:'',
                default_priority:'',
                email_handling:'',
                automatically_close:'',
                automatically_delete:'',
                schedule_data:'',
                receive_tickets_emails:'',
                receive_mobile_tickets_emails:'',
                receive_tickets_messenger_notifications:''
            },
            days: [],
            days_week:[],
            options_settings:[],
            schedule:[],
        }
    },
    validations: {
        formData: {
            name: {
                required,
                minLength: minLength(3),
                maxLength: maxLength(120)
            },

            description: {
                maxLength: maxLength(65000)
            },
            email: {
                required,
                email,
            },
        },
    },
    computed:{
        ...mapGetters('user', ['currentUser']),

        ...mapGetters('company', ['defaultCurrency']),


        isSuperAdmin() {
            return this.currentUser.role == 'super admin'
        },

        pageTitle() {
            if (this.$route.name === 'tickets.departaments.edit') {
                return this.$t('tickets.departaments.edit_departament')
            }
            return this.$t('tickets.departaments.new_departament')
        },

        isEdit() {
            if (this.$route.name === 'tickets.departaments.edit') {
                return true
            }
            return false
        },

        nameError() {
            if (!this.$v.formData.name.$error) {
                return ''
            }

            if (!this.$v.formData.name.required) {
                return this.$t('validation.required')
            }

            if (!this.$v.formData.name.minLength) {
                return this.$tc(
                    'validation.name_min_length',
                    this.$v.formData.name.$params.minLength.min,
                    { count: this.$v.formData.name.$params.minLength.min }
                )
            }

            if (!this.$v.formData.name.maxLength) {
                return this.$t('validation.description_maxlength')
            }
        },

        descriptionError() {
            if (!this.$v.formData.description.$error) {
                return ''
            }

            if (!this.$v.formData.description.maxLength) {
                return this.$t('validation.description_maxlength')
            }
        },
        emailError() {
            if (!this.$v.formData.email.$error) {
                return ''
            }

            if (!this.$v.formData.email.email) {
                return this.$tc('validation.email_incorrect')
            }
        },
    },
    created() {
        let hr=[],cont=0;
        hr={value:cont,text:`--:--:--`}
        this.schedule.push(hr)
        cont++;
        for (let i = 0; i < 24; i++) {
            for (let j = 0; j < 56; j++) {
               if(j%5 === 0){
                   hr={value:cont,text:`${i<10 ? "0"+i:i}:${j<10 ? "0"+j: j}:00`}
                   this.schedule.push(hr)
                   cont++;
               }
            }
        }

        if (!this.isSuperAdmin) {
            this.$router.push('/admin/dashboard')
        }
        if (this.isEdit) {
            /* Ojo falta editar */
            this.loadEditItemGroup()
        }
        this.fetchItems({
            filter: {},
            orderByField: '',
            orderBy: '',
        })


        this.days_week=[
           'Sunday',
           'Monday',
           'Tuesday',
           'Wednesday',
           'Thursday',
           'Friday',
           'Saturday',
        ];

        this.options_settings=[
           'Emergency',
           'Critical',
           'High',
           'Medium',
           'Low',
        ];

        let vec=[];
        for (let i = 1; i < 31; i++) {
            vec={value: i ,text:`${i} Days`}
            this.days.push(vec);
        }
    },
    mounted() {
        this.$v.formData.$reset();
    },
    methods: {
     /*    ...mapActions('modal', ['openModal']), */


        ...mapActions('ticketDepartament', [
            'addDepartament',
            'fetchDepartament',
            'updateDepartament'
        ]),

        ...mapActions('item', ['fetchItems']),

            setItemRef(el) {
                if (el) {
                    this.formData.star_tim.push(el)
                }
            },
        

        async loadEditItemGroup() {
            let response = await this.fetchDepartament(this.$route.params.id)
            if (response.data) {
              
                this.formData = { ...this.formData, ...response.data.departaments }

                const inf=JSON.parse(response.data.departaments.schedule_data);
                const inf1=JSON.parse(response.data.departaments.receive_tickets_emails);
                const inf2=JSON.parse(response.data.departaments.receive_mobile_tickets_emails);
                const inf3=JSON.parse(response.data.departaments.receive_tickets_messenger_notifications);
              
                this.formData= {
                    name: response.data.departaments.name,
                    description: response.data.departaments.description,
                    email:response.data.departaments.email,
                    default_priorit:{ value:response.data.departaments.default_priority.value, text:response.data.departaments.default_priority.text},
                    automatically_delet: {value:response.data.departaments.automatically_delete,text: response.data.departaments.automatically_delete+" Days" },
                    automatically_clos: {value:response.data.departaments.automatically_close,text: response.data.departaments.automatically_close+" Days" }, 
                    email_handlin:{ value: response.data.departaments.email_handling.value, text: response.data.departaments.email_handling.text},
                    statu: { value: response.data.departaments.status.value, text: response.data.departaments.status.text},
                    sender_override: response.data.departaments.sender_override ? true : false,
                    send_emails: response.data.departaments.send_emails ? true : false,
                    automatically_transition_admin: response.data.departaments.automatically_transition_admin ? true : false,
                    client_permission: response.data.departaments.client_permission ? true : false,
                    star_tim:inf.start_date,
                    end_tim:inf.end_date,
                    day_check:inf.day,
                    receive_tickets_emailss:inf1.vec,
                    receive_mobile_tickets_emailss:inf2.vec,
                    receive_tickets_messenger_notificationss:inf3.vec
                }

            }
        },
        

        removeItem(index) {
            this.formData.items.splice(index, 1)
        },

        cargarHorario(checkbox,start,end){
        
            return{
                checkbox:checkbox,
                start:start,
                end:end
            }
        },

        async submitItemGroup() {
            this.$v.formData.$touch()
            this.isLoading = true;
             
            if (this.$v.$invalid) {
                return true
            }

            try {
                let response;
                 /* console.log(this.formData.star_tim.length); !this.formData.star_tim[index].value.includes("--") &&*/
    
                for (let index = 0; index < this.formData.star_tim.length; index++) {
                    let x=new Date("12-12-2021 "+this.formData.star_tim[index].text);
                    let y=new Date("12-12-2021 "+this.formData.end_tim[index].text);
                    console.log(x,y);
                    if( x>y ){
                        window.toastr['error'](this.$t('tickets.departaments.check_schedule')+" "+this.days_week[index]);
                        this.isLoading = false;
                        return;
                    }
                }
                
                
                const inf={day: this.formData.day_check, start_date:this.formData.star_tim, end_date:this.formData.end_tim };

                
                
                console.log(JSON.stringify({vec:this.formData.receive_tickets_emailss}),JSON.stringify({vec:this.formData.receive_mobile_tickets_emailss}),JSON.stringify({vec:this.formData.receive_tickets_messenger_notificationss}));
                this.formData.schedule_data=JSON.stringify(inf);
                this.formData.receive_tickets_emails=JSON.stringify({vec:this.formData.receive_tickets_emailss});
                this.formData.receive_mobile_tickets_emails=JSON.stringify({vec:this.formData.receive_mobile_tickets_emailss});
                this.formData.receive_tickets_messenger_notifications=JSON.stringify({vec:this.formData.receive_tickets_messenger_notificationss});
                this.formData.default_priority=this.formData.default_priorit.value
                this.formData.email_handling=this.formData.email_handlin.value
                this.formData.automatically_close=this.formData.automatically_clos.value
                this.formData.automatically_delete=this.formData.automatically_delet.value
                this.formData.status = this.formData.statu.value

                console.log(this.formData);

                 if (this.isEdit) {
                     
                    this.formData.id= this.$route.params.id;
                    response = await this.updateDepartament(this.formData)
              
                    if (response.data.success) {
                        window.toastr['success'](this.$t('item_groups.updated_message'));
                        this.$router.push('/admin/tickets/departaments');
                    }
                    if (response.data.error) {
                        this.isLoading = false;
                        window.toastr['error'](response.data.error);
                        return true;
                    }
                } else {
                    response = await this.addDepartament(this.formData);
                    if (response.data.success) {
                        window.toastr['success'](this.$tc('tickets.departaments.created_message'));
                        this.$router.push('/admin/tickets/departaments');
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