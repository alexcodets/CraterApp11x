<template>
    <!-- Base  -->
    <base-page v-if="isSuperAdmin" class="option-group-create">
        <!--------- Form ---------->
        <form action="" @submit.prevent="submitNote">
            <!-- Header  -->
            <sw-page-header class="mb-3" :title="pageTitle">

                <template slot="actions">
                      <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/note`"
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('customer_notes.cancel') }}
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
                        {{ isEdit ? $t('customer_notes.update_items_note') : $t('customer_notes.save_items_note') }}
                    </sw-button>
                </template>
            </sw-page-header>

            <div class="grid grid-cols-12">
                <div class="col-span-12">
                    <sw-card class="mb-8">
                        <sw-input-group
                            :label="$t('customer_notes.name')"
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

                        <div class="flex my-8">
                            <div class="relative w-12">
                                <sw-checkbox
                                    v-model="formData.stiky"
                                    class="absolute"
                                />
                            </div>
                        
                            <div class="ml-4">
                                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                    {{ $t('customer_notes.sticky') }}
                                </p>
                            </div>
                        </div>

                   
                        <sw-input-group
                            :label="$t('customer_notes.description')"
                            :error="descriptionError"
                            class="mb-4"
                        >
                            <sw-textarea
                                v-model="formData.note"
                                rows="2"
                                name="note"
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
            title: 'Add Note',

            formData: {
                summary: '',
                note: '',
                stiky: false,
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

        isSuperAdmin() {
            return this.currentUser.role == 'super admin'
        },

        pageTitle() {
            if (this.$route.name === 'customers.edit-note') {
                return this.$t('customer_notes.edit_note')
            }
            return this.$t('customer_notes.new_note')
        },

        isEdit() {
            if (this.$route.name === 'customers.edit-note') {
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
    },
    created() {
        if (!this.isSuperAdmin) {
            this.$router.push('/admin/dashboard')
        }
        if (this.isEdit) {
            this.loadEditCustomerNote()
        }
    },
    mounted() {
        this.$v.formData.$reset();
    },
    methods: {

        ...mapActions('customerNote', [
            'addCustomerNote',
            'fetchCustomerNote',
            'updateCustomerNote'
        ]),
       

        async loadEditCustomerNote() {
            let response = await this.fetchCustomerNote(this.$route.params.id1)
            

            if (response.data) {
                this.formData = { ...this.formData, ...response.data.customerNote }
            }
            if(response.data.customerNote.stiky===1){
                this.formData.stiky = true
            }else{
                this.formData.stiky = false
            }

        },


        async submitNote() {
            this.$v.formData.$touch()

            if (this.$v.$invalid) {
                return true
            }

            try {
                let response;
                this.isLoading = true;
         
                if (this.isEdit) {
               
                    if(this.formData.stiky){
                        this.formData.stiky=1;
                    }else{
                        this.formData.stiky=0;
                    }
                    
                    this.formData.user_id=this.$route.params.id;
                    console.log(this.formData.user_id);
                    response = await this.updateCustomerNote(this.formData)
            
                    if (response.status === 200) {
                        window.toastr['success'](this.$t('customer_notes.updated_message'));
                        this.$router.push('/admin/customers/'+this.$route.params.id+'/view');
                    }
                    if (response.data.error) {
                        this.isLoading = false;
                        window.toastr['error'](response.data.error);
                        return true;
                    }
                } else {
                    if(this.formData.stiky){
                        this.formData.stiky=1;
                    }else{
                        this.formData.stiky=0;
                    }
                    this.formData.user_id=this.$route.params.id;
                    console.log(this.formData.user_id);
                    response = await this.addCustomerNote(this.formData);
                    if (response.status === 200) {
                        window.toastr['success'](this.$tc('customer_notes.created_message'));
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