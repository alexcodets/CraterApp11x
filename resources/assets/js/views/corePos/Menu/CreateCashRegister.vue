<template>
    <!-- Base  -->
    <base-page v-if="isSuperAdmin" class="option-group-create">
        <!--------- Form ---------->
        <form @submit.prevent="submitCashRegister">
            <!-- Header  -->
            <sw-page-header class="mb-3" :title="pageTitle">
                <sw-breadcrumb slot="breadcrumbs">
                    <sw-breadcrumb-item to="/admin/corePOS" :title="$t('core_pos.home')" />
                    <sw-breadcrumb-item to="/admin/corePOS/cash-register" :title="$t('core_pos.cash_registers')" />
                    <sw-breadcrumb-item v-if="$route.name === 'corepos.cashregister.edit'" to="#"
                        :title="'Edit Cash Register'" active />
                    <sw-breadcrumb-item v-else to="#" :title="$t('core_pos.new_cash_register')" active />
                </sw-breadcrumb>
                <!--------- Buttons Cancel and Submit Cash Register ---------->
                <template slot="actions">
                    <sw-button @click="redirectDashboard" variant="primary-outline" size="lg" type="button" class="mr-2">
                        {{ $t("core_pos.cancel") }}
                    </sw-button>
                    <sw-button :loading="isLoading" :disabled="isLoading" variant="primary" type="submit" size="lg"
                        class="flex justify-center w-full md:w-auto">
                        <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
                        {{ isEdit ? 'Update Cash Register' : $t('core_pos.save_cash_register') }}
                    </sw-button>
                </template>
                <!--------- /Buttons Cancel and Submit Cash Register ---------->
            </sw-page-header>

            <hr />
            <div class="grid grid-cols-12">
                <div class="col-span-12">
                    <sw-card class="mb-8">
                        <sw-input-group :label="$t('core_pos.name')" :error="nameError" class="mb-4" required>
                            <sw-input v-model.trim="formData.name" :invalid="$v.formData.name.$error" class="mt-2" focus
                                type="text" name="name" tabindex="1" @input="$v.formData.name.$touch()" />
                        </sw-input-group>

                        <sw-input-group :label="$t('core_pos.device')" :error="deviceError" class="mb-4" required>
                            <sw-input v-model.trim="formData.device" :invalid="$v.formData.device.$error" class="mt-2" focus
                                type="text" name="device" tabindex="1" @input="$v.formData.device.$touch()" />
                        </sw-input-group>

                        <sw-input-group :label="$t('core_pos.description')" :error="descriptionError" class="mb-4 mt-4">
                            <sw-textarea v-model="formData.description" :invalid="$v.formData.description.$error" rows="5"
                                name="note" style="resize: none" @input="$v.formData.description.$touch()" />
                        </sw-input-group>

                        <sw-input-group :error="userError" :label="$t('core_pos.users')" class="mb-4" required>
                            <sw-select v-model="formData.users_id" :options="users" :searchable="true" :show-labels="false"
                                class="mt-2" track-by="id" label="name" :placeholder="'Select an User'" :multiple="true"
                                :tabindex="10" @input="$v.formData.users_id.$touch()" />
                        </sw-input-group>

                        <sw-input-group :error="customerError" :label="$t('core_pos.customer')" class="mb-4" required>
                            <sw-select v-model="formData.customer_id" :options="customers" :searchable="true"
                                :show-labels="false" class="mt-2" track-by="id" label="name"
                                :placeholder="'Select an Customer'" :multiple="false" :tabindex="10"
                                @input="$v.formData.customer_id.$touch()" />
                        </sw-input-group>

                        <sw-input-group :error="storeError" :label="$t('core_pos.store')" class="mb-4" required>
                            <sw-select v-model="formData.store_id" :options="stores" :searchable="true" :show-labels="false"
                                class="mt-2" track-by="id" label="name" :placeholder="'Select an Store'"
                                :multiple="false" :tabindex="10" @input="$v.formData.store_id.$touch()" />
                        </sw-input-group>
                    </sw-card>
                </div>
            </div>
            <hr />
        </form>
    </base-page>
</template>

<script>

import draggable from 'vuedraggable'
import { mapActions, mapGetters } from 'vuex'
import Guid from 'guid'
import {
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
    CloudUploadIcon,
    XCircleIcon
} from '@vue-hero-icons/solid'

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
        CloudUploadIcon,
        XCircleIcon
    },
    data() {
        return {
            isLoading: false,
            formData: {
                id: null,
                name: '',
                device: '',
                description: '',
                users_id: [],
                customer_id: '',
                store_id: ''
            },
            users: [],
            customers: [],
            stores: []
        }
    },
    validations: {
        formData: {
            name: {
                required,
                minLength: minLength(3),
                maxLength: maxLength(120)
            },
            device: {
                required,
            },
            customer_id: {
                required,
            },
            store_id: {
                required,
            },
            users_id: {
                required,
                minLength: minLength(1),
            },
            description: {
                maxLength: maxLength(65000)
            },
        },
    },
    computed: {
        ...mapGetters('user', ['currentUser']),

        ...mapGetters('company', ['defaultCurrency']),

        isSuperAdmin() {
            return this.currentUser.role == 'super admin'
        },

        pageTitle() {
            if (this.$route.name === 'corepos.cashregister.edit') {
                return 'Edit Cash Register'
            }
            return this.$t('core_pos.new_cash_register')
        },

        isEdit() {
            if (this.$route.name === 'corepos.cashregister.edit') {
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
        deviceError() {
            if (!this.$v.formData.device.$error) {
                return ''
            }

            if (!this.$v.formData.device.required) {
                return this.$t('validation.required')
            }

        },
        storeError() {
            if (!this.$v.formData.store_id.$error) {
                return ''
            }

            if (!this.$v.formData.store_id.required) {
                return this.$t('validation.required')
            }

        },
        userError() {
            if (!this.$v.formData.users_id.$error) {
                return ''
            }

            if (!this.$v.formData.users_id.required) {
                return this.$t('validation.required')
            }

            if (!this.$v.formData.users_id.minLength) {
                return this.$t('validation.required')
            }

        },
        customerError() {
            if (!this.$v.formData.customer_id.$error) {
                return ''
            }

            if (!this.$v.formData.customer_id.required) {
                return this.$t('validation.required')
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
    },
    created() {
        this.loadData()
        if (!this.isSuperAdmin) {
            this.$router.push('/admin/dashboard')
        }


    },
    async mounted() {

        this.$v.formData.$reset();
        // MultiSelect Users
        let response = await window.axios.post(`/api/v1/core-pos/cash-register/getUsers`)
        this.users = [...response.data.users]


    },
    methods: {
        ...mapActions('modal', ['openModal']),
        ...mapActions('customer', ['fetchCustomers']),
        ...mapActions('users', ['fetchUsers']),
        ...mapActions('corePos', ['fetchStores', 'deleteStore']),

        async loadData() {
            let responseCustomer = await this.fetchCustomers()
            this.customers = responseCustomer.data.customers.data
            const responseStores = await this.fetchStores()
            this.stores = responseStores.data.stores.data

            if (this.isEdit) {
                this.loadCashRegister()
            }

        },
        async loadCashRegister() {
            let response
            response = await window.axios.get(`/api/v1/core-pos/cash-register/getCashRegister/` + this.$route.params.id)
            this.formData.name = response.data.cash_register.name
            this.formData.device = response.data.cash_register.device
            this.formData.description = response.data.cash_register.description
            this.formData.customer_id = this.customers.find(item => item.id === response.data.cash_register.customer_id)
            this.formData.store_id = this.stores.find(item => item.id === response.data.cash_register.store_id)
            if (response.data.users.length > 0) {
                this.formData.users_id = [...response.data.users]
            }
        },

        async submitCashRegister() {
            this.$v.formData.$touch()
            if (this.$v.$invalid) {
                return true
            }

            swal({
                title: this.$t('general.are_you_sure'),
                text: this.$t('core_pos.add_cash_register_message'),
                icon: '/assets/icon/file-alt-solid.svg',
                buttons:  [this.$t('core_pos.cancel'), true],
                dangerMode: true,
            }).then(async (value) => {
                if (value) {
                    // Users Id
                    this.formData.users_id = this.formData.users_id.length > 0
                        ? this.formData.users_id.map(user => user.id)
                        : null

                    this.formData.customer_id = this.formData.customer_id.id
                    this.formData.store_id = this.formData.store_id.id
                    try {
                        let response;
                        this.isLoading = true;
                        if (this.isEdit) {
                            this.formData.id = this.$route.params.id;

                            response = await window.axios.post(`/api/v1/core-pos/cash-register/updateCashRegister`,
                                this.formData)

                            if (response.data.success) {
                                window.toastr['success'](response.data.message);
                                this.$router.push('/admin/corePOS/cash-register');
                            }
                            if (response.data.error) {
                                this.isLoading = false;
                                window.toastr['error'](response.data.error);
                                return true;
                            }
                        } else {
                            response = await window.axios.post(`/api/v1/core-pos/cash-register/addCashRegister`, this.formData)

                            if (response.data.success) {
                                window.toastr['success'](response.data.message);
                                this.$router.push('/admin/corePOS/cash-register');
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
            })



        },
        redirectDashboard() {
            swal({
                title: this.$t('general.are_you_sure'),
                text: this.$t('core_pos.back_cash_register'),
                icon: '/assets/icon/file-alt-solid.svg',
                //buttons: true,
                buttons: [this.$t('core_pos.cancel'), true],
                dangerMode: true,
            }).then(async (value) => {
                if (value) {
                    this.$router.push('/admin/corePOS/cash-register')
                }
            })
        },
    }
}
</script>

<style scoped></style>