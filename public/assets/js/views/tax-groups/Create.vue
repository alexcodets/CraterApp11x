<template>
  <sw-card variant="setting-card">
    <!-- Base  -->
    <base-page v-if="isSuperAdmin" class="tax-group-create">
        <!--------- Form ---------->
        <form action="" @submit.prevent="submitTaxGroup">
            <!-- Header  -->
            <sw-page-header class="mb-3" :title="pageTitle">
                <sw-breadcrumb slot="breadcrumbs">
                    <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
                    <sw-breadcrumb-item to="/admin/settings/tax-groups" :title="$t('tax_groups.tax_group')" />
                    <sw-breadcrumb-item
                        v-if="$route.name === 'tax-groups.edit'"
                        to="#"
                        :title="$t('tax_groups.edit_tax_group')"
                        active
                    />
                    <sw-breadcrumb-item
                        v-else
                        to="#"
                        :title="$t('tax_groups.new_tax_group')"
                        active
                    />
                </sw-breadcrumb>

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
                        {{ isEdit ? $t('tax_groups.update_tax_group') : $t('tax_groups.save_tax_group') }}
                    </sw-button>
                </template>
            </sw-page-header>

            <div class="grid grid-cols-12">
                <div class="col-span-12">
                    <sw-card class="mb-8">
                        <sw-input-group
                            :label="$t('tax_groups.name')"
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
                            :label="$t('tax_groups.description')"
                            :error="descriptionError"
                            class="mb-4"
                        >
                            <sw-textarea
                            v-model.trim="formData.description"
                            :placeholder="$t('tax_groups.description')"
                            type="text"
                            name="description"
                            rows="3"
                            tabindex="11"
                            @input="$v.formData.description.$touch()"
                            />
                        </sw-input-group>
        
                        <sw-input-group
                            :label="$t('tax_groups.status')"
                            class="md:col-span-3 mb-4"
                            :error="statusError"
                            required
                        >
                            <sw-select
                                v-model="formData.status"
                                :invalid="$v.formData.status.$error"
                                :options="status"
                                :searchable="true"
                                :show-labels="false"
                                :tabindex="16"
                                :allow-empty="true"
                                :placeholder="$t('tax_groups.status')"
                                label="text"
                                track-by="value"
                            />
                        </sw-input-group>

                        <sw-input-group
                        :label="$t('tax_groups.country')"
                        class="md:col-span-3 mb-4"
                        >
                            <sw-select
                                v-model="formData.countries"
                                :options="countries"
                                :searchable="true"
                                :show-labels="false"
                                :allow-empty="true"
                                :tabindex="8"
                                :placeholder="$t('general.select_country')"
                                label="name"
                                track-by="id"
                                @select="countrySeleted"
                            />
                        </sw-input-group>

                        <sw-input-group
                        :label="$t('tax_groups.state')"
                        class="md:col-span-3 mb-4"
                        >
                            <sw-select
                                v-model="formData.states"
                                :options="states"
                                :searchable="true"
                                :show-labels="false"
                                :allow-empty="true"
                                :tabindex="8"
                                :placeholder="$t('general.select_state')"
                                label="name"
                                track-by="id"
                                select="stateSeleted"
                            />
                        </sw-input-group>


                        <div class="grid grid-cols-5 gap-4 mb-8">

                            <div class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6">
                                <sw-divider class="col-span-12" />
                                            
                                <div class="col-span-3">
                                <h6>{{ $t('tax_groups.member_taxes') }}</h6>
                                <div class="grid gap-6 grid-col-1 md:grid-cols-2" style="
                                    height: 100px;
                                    overflow-y: scroll;
                                    border-color: #cbd5e0;
                                    border-width: 1px;
                                    border-style: solid;">
                                    <ul>
                                    <li v-for="(item, index) in taxGroupLeft" :key="item.id" @click="moveToRight(item, index)">
                                        <div class="cursor-pointer">
                                        {{ item.name }}
                                        </div>
                                    </li>
                                    </ul>
                                </div>
                                </div>

                                <div style="text-align: center;padding: 20px;">
                                </div>

                                <div class="col-span-7">
                                <h6>{{ $t('tax_groups.available_taxes') }}</h6>
                                <div class="grid gap-6 grid-col-1 md:grid-cols-2" style="
                                    height: 100px;
                                    overflow-y: scroll;                      
                                    border-color: #cbd5e0;
                                    border-width: 1px;
                                    border-style: solid;">
                                    <ul>
                                    <li v-for="(item, index) in taxGroupRight" :key="item.id" @click="moveToLeft(item, index)">
                                        <div class="cursor-pointer">
                                        {{ item.name }}
                                        </div>
                                    </li>
                                    </ul>
                                </div>
                                </div>
                            </div>
                        </div>

                    </sw-card>

                </div>
            </div>
        </form>
    </base-page>
  </sw-card>
</template>

<script>

import draggable from 'vuedraggable'
import { mapActions, mapGetters } from 'vuex'
import {
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
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
    },
    data() {
        return {
            isLoading: false,
            title: 'Add Tax Group',
            taxGroupRight: [],
            taxGroupLeft: [],
            
            countries: [],
            states: [],
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
                name: '',
                description: '',
                status: {
                    value: 'A',
                    text: 'Active',
                },
                country_id: '',
                state_id: '',
                country_name: '',
                state_name: '',
                countries: [],
                states: [],
            },
        }
    },
    validations: {
        formData: {
            name: {
                required,
                minLength: minLength(1),
                maxLength: maxLength(255)
            },
            description: {
                maxLength: maxLength(255)
            },
            status: {
                required,
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
            if (this.$route.name === 'tax-groups.edit') {
                return this.$t('tax_groups.edit_tax_group')
            }
            return this.$t('tax_groups.new_tax_group')
        },

        isEdit() {
            if (this.$route.name === 'tax-groups.edit') {
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

        statusError() {
            if (!this.$v.formData.status.$error) {
                return ''
            }
        },
    },
    created() {
        this.loadTaxMembership()
        this.fetchInitDataCountry()
        if (!this.isSuperAdmin) {
            this.$router.push('/admin/dashboard')
        }
        if (this.isEdit) {
            this.loadEditTaxGroup()
        }
    },
    mounted() {
        this.$v.formData.$reset();
    },
    methods: {
        ...mapActions('modal', ['openModal']),

        ...mapActions('taxGroups', [
            'addTaxGroup',
            'fetchTaxGroup',
            'updateTaxGroup',
        	'fetchTaxMembership',
        ]),
        
        async fetchInitDataCountry() {
            this.initLoad = true
            let res = await window.axios.get('/api/v1/countries')
            if (res) {
                this.countries = res.data.countries
            }
            this.initLoad = false
        },

        async countrySeleted(val) {

            let res = await window.axios.get('/api/v1/states/'+ val.code)
            if (res) {
                this.states = res.data.states
            }

            this.formData.countries = val;

            // let isId = (element) => element.id == val.id
            // let index = this.formData.countries.findIndex(isId)

            // if (index == -1) {
            //     this.formData.countries.push(val)
            // } else {
            //     window.toastr['error']('This country was already selected')
            //     return false
            // }

        },

        async stateSeleted(val) {

            this.formData.states = val;

            // let isId = (element) => element.id == val.id
            // let index = this.formData.states.findIndex(isId)
            // if (index == -1) {
            //     this.formData.states.push(val)
            // } else {
            //     window.toastr['error']('This state was already selected')
            //     return false
            // }
        },
        
        async loadTaxMembership() {      
            let res = await this.fetchTaxMembership()     
            this.taxGroupRight = res.data 
        },

        async loadEditTaxGroup() {
            let response = await this.fetchTaxGroup(this.$route.params.id)
            
            this.formData = response.data.tax_groups

            if (response.data.tax_groups.countries) {
                let res = await window.axios.get('/api/v1/states/'+ response.data.tax_groups.countries.code)
                if (res) {
                    this.states = res.data.states
                }                
            }            
            //console.log(response.data.taxes);
            this.taxGroupLeft = response.data.taxes;
            //console.log(this.taxGroupLeft);

            for(var i = 0; i < this.taxGroupLeft.length; i++ ) {
                for(var j = 0; j < this.taxGroupRight.length;j++) {
                    if (this.taxGroupLeft[i].id === this.taxGroupRight[j].id) {
                        this.taxGroupRight.splice(j,1);
                    } 
                }
            }

        },

        async submitTaxGroup() {
            this.$v.formData.$touch()

            if (this.$v.$invalid) {
                return true
            }

            this.formData.status = this.formData.status.value

            if (this.taxGroupLeft.length > 0) {
                this.formData.taxGroupLeft = this.taxGroupLeft
            }
            
            if (this.formData.countries) {
                this.formData.country_id = this.formData.countries.id;   
            }
            
            if (this.formData.states) {
                this.formData.state_id = this.formData.states.id;
            }

            try {
                let response;
                this.isLoading = true;
                if (this.isEdit) {
                    response = await this.updateTaxGroup(this.formData)
                    if (response.status === 200) {
                        window.toastr['success'](this.$t('tax_groups.updated_message'));
                        this.$router.push('/admin/settings/tax-groups');
                    }
                    if (response.data.error) {
                        window.toastr['error'](response.data.error);
                    }
                } else {
                    response = await this.addTaxGroup(this.formData);
                    if (response.status === 200) {
                        window.toastr['success'](this.$tc('tax_groups.created_message'));
                        this.$router.push('/admin/settings/tax-groups');
                    }
                    if (response.data.error) {
                        window.toastr['error'](response.data.error);
                    }
                }

                this.isLoading = false;
                return true;

            } catch (err) {

            }
        },

        moveToLeft(item, index){
            if (this.isEdit) {
                item.new = true
            }
            this.taxGroupLeft.push(item)
            this.taxGroupRight.splice(index, 1)
        },
        moveToRight(item, index){
            this.taxGroupRight.push(item)
            this.taxGroupLeft.splice(index, 1)
        },
    }

}
</script>

<style scoped>

</style>