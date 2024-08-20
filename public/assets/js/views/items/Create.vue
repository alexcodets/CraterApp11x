<template>
    <base-page>
        <!-- Page Header -->
        <sw-page-header :title="pageTitle" class="mb-3">
            <sw-breadcrumb slot="breadcrumbs">
                <sw-breadcrumb-item :title="$t('general.home')" to="/admin/dashboard"/>
                <sw-breadcrumb-item :title="$tc('items.item', 2)" to="/admin/items"/>
                <sw-breadcrumb-item
                    v-if="$route.name === 'items.edit'"
                    :title="$t('items.edit_item')"
                    to="#"
                    active
                />
                <sw-breadcrumb-item
                    v-else
                    :title="$t('items.new_item')"
                    to="#"
                    active
                />
            </sw-breadcrumb>
        </sw-page-header>

        <div class="grid grid-cols-12">
            <div class="col-span-12 md:col-span-6">
                <form action="" @submit.prevent="submitItem">
                    <sw-card>
                        <sw-input-group
                            :label="$t('items.name')"
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
                                :tabindex="1"
                                @input="$v.formData.name.$touch()"
                            />
                        </sw-input-group>

                        <sw-input-group
                            :label="$t('items.price')"
                            :error="priceError"
                            class="mb-4"
                            required
                        >
                            <sw-money
                                v-model.trim="price"
                                :invalid="$v.formData.price.$error"
                                :currency="defaultCurrencyForInput"
                                :tabindex="2"
                                class="relative w-full focus:border focus:border-solid focus:border-primary-500"
                                @input="$v.formData.price.$touch()"
                            />
                        </sw-input-group>

                        <sw-input-group :label="$t('items.unit')" class="mb-4">
                            <sw-select
                                v-model="formData.unit"
                                :options="itemUnits"
                                :searchable="true"
                                :show-labels="false"
                                :placeholder="$t('items.select_a_unit')"
                                class="mt-2"
                                label="name"
                                :tabindex="3"
                            >
                                <div
                                    slot="afterList"
                                    class="flex items-center justify-center w-full px-6 py-3 text-base bg-gray-200 cursor-pointer text-primary-400"
                                    @click="addItemUnit"
                                >
                                    <shopping-cart-icon
                                        class="h-5 mr-2 -ml-2 text-center text-primary-400"
                                    />

                                    <label class="ml-2 text-sm leading-none text-primary-400">{{
                                            $t('settings.customization.items.add_item_unit')
                                        }}</label>
                                </div>
                            </sw-select>
                        </sw-input-group>

                        <sw-input-group
                            :label="$t('items.items_groups')"
                            class="mb-4"
                        >
                            <sw-select
                                v-model="formData.item_groups"
                                :options="getItemGroups"
                                :searchable="true"
                                :show-labels="false"
                                :allow-empty="true"
                                :multiple="true"
                                class="mt-2"
                                track-by="item_group_id"
                                label="item_group_name"
                                :tabindex="4"
                            />
                        </sw-input-group>

                        <div v-if="noTaxable" class="flex my-8">
                            <div class="relative w-12">
                                <sw-checkbox
                                    v-model="formData.no_taxable"
                                    class="absolute"
                                    @change="setTaxable"
                                    tabindex="5"
                                />
                            </div>

                            <div class="ml-4">
                                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                    {{ $t('items.no_taxable') }}
                                </p>

                                <p class="p-0 m-0 text-xs leading-4 text-gray-500"
                                    style="max-width: 480px"
                                >
                                    {{ $t('items.no_tax_description') }}
                                </p>
                            </div>
                        </div>

                        <div v-if="isTaxable" class="flex my-8 mb-4">
                            <div class="relative w-12">
                                <sw-switch
                                    v-model="formData.allow_taxes"
                                    class="absolute"
                                    style="top: -20px"
                                    @change="setTax"
                                    :tabindex="6"
                                />
                            </div>

                            <div class="ml-4">
                                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                    {{ $t('items.allow_taxes') }}
                                </p>
                            </div>
                        </div>

                        <sw-input-group
                            v-if="isTaxPerItem"
                            :label="$t('items.taxes')"
                            class="mb-4"
                        >
                            <sw-select
                                v-model="formData.taxes"
                                :options="getTaxTypes"
                                :searchable="true"
                                :show-labels="false"
                                :allow-empty="true"
                                :multiple="true"
                                class="mt-2"
                                track-by="tax_type_id"
                                label="tax_name"
                                :tabindex="7"
                            />
                        </sw-input-group>

                        <sw-input-group
                            :label="$t('items.description')"
                            :error="descriptionError"
                            class="mb-4"
                        >
                            <sw-textarea
                                v-model="formData.description"
                                rows="2"
                                name="description"
                                @input="$v.formData.description.$touch()"
                                :tabindex="8"
                            />
                        </sw-input-group>

                        <div class="flex my-8 mb-4">
                            <div class="relative w-12">
                                <sw-switch
                                    v-model="formData.avalara_bool"
                                    class="absolute"
                                    style="top: -20px"
                                />
                            </div>

                            <div class="ml-4">
                                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                                    {{ $t('items.add_avalara') }}
                                </p>
                            </div>
                        </div>

                        <sw-input-group
                            v-if="isAddAvalara"
                            :label="$t('items.item_avalara_type')"
                            class="mb-4"
                        >
                            <sw-select
                                v-model="avalara_type_selected"
                                :options="avalara_types"
                                :searchable="true"
                                :show-labels="false"
                                :placeholder="$t('items.select_a_type')"
                                class="mt-2"
                                label="name"
                                @select="transactionSeleted"
                                :tabindex="9"
                            />
                        </sw-input-group>

                        <sw-input-group
                            v-if="isAddAvalara"
                            :label="$t('items.avalara_service_type')"
                            class="mb-4"
                        >
                            <sw-select
                                v-model="formData.avalara_service_types"
                                :options="avalara_service_types"
                                :searchable="true"
                                :show-labels="false"
                                :allow-empty="true"
                                :placeholder="$t('items.select_a_type')"
                                class="mt-2"
                                track-by="id"
                                label="service_type_name"
                                :tabindex="11"
                                @select="serviceSeleted"
                            />
                        </sw-input-group>

                        <sw-input-group
                            v-if="this.isServiceData"
                            :label="$t('items.avalara_payment_type')"
                            class="mb-4"
                        >
                            <sw-select
                                v-model="formData.avalara_payment_type"
                                :options="avalara_payment_type"
                                :searchable="true"
                                :show-labels="false"
                                :allow-empty="true"
                                :placeholder="$t('items.select_a_type')"
                                class="mt-2"
                                label="name"
                                :tabindex="12"
                                @select="avalaraPaymentSeleted"
                            />
                        </sw-input-group>

                        <sw-input-group
                            :label="$tc('items.image')"
                            class="mb-4"
                        >
                            <div
                                id="logo-box"
                                class="
                                relative
                                flex
                                items-center
                                justify-center
                                h-24
                                p-5
                                mt-2
                                bg-transparent
                                border-2
                                border-gray-200
                                border-dashed
                                rounded-md
                                image-upload-box
                            "
                            >
                                <img
                                    v-if="previewPicture"
                                    :src="previewPicture"
                                    class="absolute opacity-100 preview-logo"
                                    style="max-height: 80%; animation: fadeIn 2s ease"
                                />
                                <div v-else class="flex flex-col items-center">
                                    <cloud-upload-icon
                                        class="h-5 mb-2 text-xl leading-6 text-gray-400"
                                    />
                                    <p class="text-xs leading-4 text-center text-gray-400">
                                        Drag a file here or
                                        <span id="pick-avatar" class="cursor-pointer text-primary-500">
                                        browse
                                    </span>
                                        to choose a file
                                    </p>
                                </div>
                            </div>

                            <sw-avatar
                                trigger="#logo-box"
                                :preview-avatar="previewPicture"
                                @changed="onChange"
                                @uploadHandler="onUploadHandler"
                                @handleUploadError="onHandleUploadError"
                            >
                                <template v-slot:icon>
                                    <cloud-upload-icon
                                        class="h-5 mb-2 text-xl leading-6 text-gray-400"
                                    />
                                </template>
                            </sw-avatar>
                        </sw-input-group>

                        <div class="mb-4">
                            <sw-button
                                :loading="isLoading"
                                variant="primary"
                                size="lg"
                                :tabindex="10"
                                class="flex justify-center w-full md:w-auto"
                            >
                                <save-icon v-if="!isLoading" class="mr-2 -ml-1"/>
                                {{ isEdit ? $t('items.update_item') : $t('items.save_item') }}
                            </sw-button>
                        </div>
                    </sw-card>
                </form>
            </div>
        </div>
    </base-page>
</template>

<script>
import {mapActions, mapGetters} from 'vuex'
import {ShoppingCartIcon, CloudUploadIcon} from '@vue-hero-icons/solid'
import TheSiteHeaderVue from '../layouts/partials/TheSiteHeader.vue'

const {
    required,
    minLength,
    numeric,
    minValue,
    maxLength,
} = require('vuelidate/lib/validators')

export default {
    components: {
        ShoppingCartIcon,
        CloudUploadIcon
    },

    data() {
        return {
            isLoading: false,
            title: 'Add Item',
            units: [],
            taxes: [],
            taxPerItem: '',
            taxable: 'YES',
            showNoTaxable: 'YES',
            add_avalara: false,
            avalara_type_selected: {},
            isServiceData: false,

            formData: {
                name: '',
                description: '',
                price: '',
                unit_id: null,
                unit: null,
                allow_taxes: false,
                no_taxable: false,
                taxes: [],
                item_groups: [],
                avalara_bool: false,
                avalara_type: '',
                avalara_service_types: [],
                avalara_service_type: '',
                avalara_service_type_name: '',
                avalara_payment_type: '',
            },

            money: {
                decimal: '.',
                thousands: ',',
                prefix: '$ ',
                precision: 2,
                masked: false,
            },

            avalara_payment_type: [
                { name: 'Taxable Amount', value: 'TAXABLE_AMOUNT' },
                { name: 'Minutes', value: 'MINUTES' },
            ],

            avalara_types: [
                { name: 'VoIP', value: '19' },
                { name: 'VoIPA', value: '20' },
                { name: '(VoIP- Nomadic)', value: '59' },
                { name: '(Non-Interconnected VoIP)', value: '65' }
            ],

            avalara_service_types: [],

            previewPicture: null,
            fileObject: null,
            cropperOutputMime: '',
        }
    },

    computed: {
        ...mapGetters('company', ['defaultCurrencyForInput']),

        ...mapGetters('item', ['itemUnits']),

        ...mapGetters('taxType', ['taxTypes']),

        ...mapGetters('itemGroups', ['itemGroups']),

        price: {
            get: function () {
                return this.formData.price / 100
            },
            set: function (newValue) {
                this.formData.price = Math.round(newValue * 100)
            },
        },

        pageTitle() {
            if (this.$route.name === 'items.edit') {
                return this.$t('items.edit_item')
            }
            return this.$t('items.new_item')
        },

        ...mapGetters('taxType', ['taxTypes']),

        isEdit() {
            if (this.$route.name === 'items.edit') {
                return true
            }
            return false
        },

        isTaxPerItem() {
            return this.taxPerItem === 'YES' ? 1 : 0
        },

        isTaxable() {
            return this.taxable === 'YES' ? 1 : 0;
        },

        noTaxable() {
            return this.showNoTaxable === 'YES' ? 1 : 0;
        },

        getTaxTypes() {
            return this.taxTypes.map((tax) => {
                return {
                    ...tax,
                    tax_type_id: tax.id,
                    tax_name: tax.name + ' (' + tax.percent + '%)',
                }
            })
        },

        getItemGroups() {
            return this.itemGroups.map((group) => {
                return {
                    ...group,
                    item_group_id: group.id,
                    item_group_name: group.name
                }
            })
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
                    {count: this.$v.formData.name.$params.minLength.min}
                )
            }
        },

        priceError() {
            if (!this.$v.formData.price.$error) {
                return ''
            }

            if (!this.$v.formData.price.required) {
                return this.$t('validation.required')
            }

            if (!this.$v.formData.price.maxLength) {
                return this.$t('validation.price_maxlength')
            }

            if (!this.$v.formData.price.minValue) {
                return this.$t('validation.price_minvalue')
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

        isAddAvalara() {
            this.add_avalara = this.formData.avalara_bool
            return this.add_avalara;
        }
    },

    created() {
        this.loadData()
    },

    mounted() {
        // No se va a usar este setting general
        //this.setTaxPerItem()

        this.$v.formData.$reset()
    },

    validations: {
        formData: {
            name: {
                required,
                minLength: minLength(3),
            },

            price: {
                required,
                numeric,
                maxLength: maxLength(20),
                minValue: minValue(0.1),
            },

            description: {
                maxLength: maxLength(65000),
            },
        },
    },

    methods: {
        ...mapActions('item', [
            'addItem',
            'fetchItem',
            'updateItem',
            'fetchItemUnits',
            'uploadPicture'
        ]),

        ...mapActions('taxType', ['fetchTaxTypes']),

        ...mapActions('company', ['fetchCompanySettings']),

        ...mapActions('modal', ['openModal']),

        ...mapActions('itemGroups', ['fetchItemGroups']),

        async setTaxPerItem() {
            let response = await this.fetchCompanySettings(['tax_per_item'])

            if (response.data) {
                response.data.tax_per_item === 'YES'
                    ? (this.taxPerItem = 'YES')
                    : (this.taxPerItem = 'NO')
            }
        },

        setTax(val) {
            if (this.formData.allow_taxes) {
                this.taxPerItem = 'YES';
                this.showNoTaxable = 'NO';
                this.formData.no_taxable = false;
            } else {
                this.taxPerItem = 'NO';
                this.showNoTaxable = 'YES';
                this.formData.taxes = [];
            }
        },

        setTaxable() {
            if (this.formData.no_taxable) {
                this.taxable = 'NO';
                this.formData.allow_taxes = false;
                this.formData.taxes = [];
            } else {
                this.taxable = 'YES';
            }
        },

        async transactionSeleted(val) {
            
            this.isServiceData = false

            if (this.avalara_service_types != null) {
                this.formData.avalara_service_types = null
            }

            let res = await window.axios.get('/api/v1/avalara-service-types/'+ val.value)
            if (res) {
                this.avalara_service_types = res.data.avalara_service_types
            }
        },

        async serviceSeleted(val) {
            this.isServiceData = false
            if (this.avalara_payment_type != null) {
                this.formData.avalara_payment_type = null
            }
            this.formData.avalara_service_types = val;
            if (val.service_type_name == 'International Usage' || val.service_type_name == 'Interstate Usage' || val.service_type_name == 'Intrastate Usage') {
                this.isServiceData = true
            }
        },

        async avalaraPaymentSeleted(val) {
            this.formData.avalara_payment_type = val.value;
        },

        async loadData() {
            if (this.isEdit) {
                let response = await this.fetchItem(this.$route.params.id)

                this.formData = {...response.data.item, unit: null}

                this.previewPicture = response.data.item.picture

                this.fractional_price = response.data.item.price

                if (this.formData.unit_id) {
                    await this.fetchItemUnits({limit: 'all'})
                    this.formData.unit = this.itemUnits.find(
                        (_unit) => response.data.item.unit_id === _unit.id
                    )
                }

                if (this.formData.taxes) {
                    await this.fetchTaxTypes({limit: 'all'})
                    console.log(response.data.item.taxes);
                    this.formData.taxes = response.data.item.taxes.map((tax) => {
                        return {...tax, tax_name: tax.name + '(' + tax.percent + '%)'}
                    })
                }

                if (this.formData.item_groups) {
                    await this.fetchItemGroups({limit: 'all'});
                     console.log(response.data.item.item_group);
                    this.formData.item_groups = response.data.item.item_groups.map((itemGroup) => {
                        return {
                            ...itemGroup,
                            item_group_id: itemGroup.id,
                            item_group_name: itemGroup.name
                        }
                    });
                }

                this.formData.allow_taxes === 1
                    ? this.formData.allow_taxes = true
                    : this.formData.allow_taxes = false;

                this.formData.no_taxable === 1
                    ? this.formData.no_taxable = true
                    : this.formData.no_taxable = false;

                this.setTax();
                this.setTaxable();

                let a_type_name = ''

                switch (this.formData.avalara_type) {
                    case '19':
                        a_type_name = 'VoIP'
                        break
                    case '20':
                        a_type_name = 'VoIPA'
                        break
                    case '59':
                        a_type_name= '(VoIP- Nomadic)'
                        break
                    case '65':
                        a_type_name= '(Non-Interconnected VoIP)'
                        break
                    default:
                        break
                }
                this.avalara_type_selected = {
                    name: a_type_name,
                    value: this.formData.avalara_type
                }


                if (response.data.item.avalara_bool) {
                    let res = await window.axios.get('/api/v1/avalara-service-types/'+ response.data.item.avalara_type)
                    if (res) {
                        this.avalara_service_types = res.data.avalara_service_types
                    }
                } 

                if (response.data.item.avalara_service_type === 31 || response.data.item.avalara_service_type === 30 || response.data.item.avalara_service_type === 32) {
                    this.isServiceData = true
                    let type_name = ''
                    
                    switch (this.formData.avalara_payment_type) {
                        case 'TAXABLE_AMOUNT':
                            type_name = 'Taxable Amount'
                            break
                        case 'MINUTES':
                            type_name = 'Minutes'
                            break
                        default:
                            break
                    }
                    this.formData.avalara_payment_type = {
                        name: type_name,
                        value: this.formData.avalara_payment_type
                    }
                }

            } else {
                this.fetchItemUnits({limit: 'all'})
                this.fetchTaxTypes({limit: 'all'})
                this.fetchItemGroups({limit: 'all'})
            }
        },

        async submitItem() {
            this.$v.formData.$touch()

            if (this.$v.$invalid) {
                return false
            }

            if (this.formData.unit) {
                this.formData.unit_id = this.formData.unit.id
            }

            if (this.avalara_type_selected && this.isAddAvalara) {
                this.formData.avalara_type = this.avalara_type_selected.value
            } else {
                this.formData.avalara_type = ''
            }
            
            if (this.formData.avalara_service_types && this.isAddAvalara) {
                this.formData.avalara_service_type = this.formData.avalara_service_types.id;
                this.formData.avalara_service_type_name = this.formData.avalara_service_types.service_type_name;
            } else {
                this.formData.avalara_service_type = null;
            }

            if (this.formData.avalara_service_types && this.isAddAvalara) {

            if (this.formData.avalara_service_types.service_type_name == 'International Usage' || this.formData.avalara_service_types.service_type_name == 'Interstate Usage' || this.formData.avalara_service_types.service_type_name == 'Intrastate Usage') {            
                this.formData.avalara_payment_type = this.formData.avalara_payment_type.value;
            } else {
                switch (this.formData.avalara_service_type_name) {
                    case 'Access Charge':
                        this.formData.avalara_payment_type = 'TAXABLE_AMOUNT'
                        break
                    case 'Access-Local Only Service':
                        this.formData.avalara_payment_type = 'TAXABLE_AMOUNT'
                        break
                    case 'Activation':
                        this.formData.avalara_payment_type = 'TAXABLE_AMOUNT'
                        break
                    case 'Enhanced Feature Charge':
                        this.formData.avalara_payment_type = 'TAXABLE_AMOUNT'
                        break
                    case 'Equipment Rental':
                        this.formData.avalara_payment_type = 'TAXABLE_AMOUNT'
                        break
                    case 'Equipment Repair':
                        this.formData.avalara_payment_type = 'TAXABLE_AMOUNT'
                        break
                    case 'Install':
                        this.formData.avalara_payment_type = 'TAXABLE_AMOUNT'
                        break
                    case 'Invoice':
                        this.formData.avalara_payment_type = 'NONE'
                        break
                    case 'Late Charge':
                        this.formData.avalara_payment_type = 'TAXABLE_AMOUNT'
                        break
                    case 'Lines':
                        this.formData.avalara_payment_type = 'LINES'
                        break
                    case 'LNP (Local Number Portability)':
                        this.formData.avalara_payment_type = 'TAXABLE_AMOUNT'
                        break
                    case 'Local Feature Charge':
                        this.formData.avalara_payment_type = 'TAXABLE_AMOUNT'
                        break
                    case 'PBX':
                        this.formData.avalara_payment_type = 'LINES'
                        break
                    case 'PBX Extension':
                        this.formData.avalara_payment_type = 'LINES'
                        break
                    case 'PBX High Capacity':
                        this.formData.avalara_payment_type = 'LINES'
                        break
                    case 'PBX Outbound Channel':
                        this.formData.avalara_payment_type = 'LINES'
                        break
                    case 'Toll-Free Number':
                        this.formData.avalara_payment_type = 'TAXABLE_AMOUNT'
                        break
                    case 'Wireless Access Charge':
                        this.formData.avalara_payment_type = 'TAXABLE_AMOUNT'
                        break
                    case 'Wireless Lines':
                        this.formData.avalara_payment_type = 'LINES'
                        break
                    default:
                        break
                }
            }

             }


            let response
            this.isLoading = true

            if (this.isEdit) {
                try {
                    let response = await this.updateItem(this.formData)

                    if (this.fileObject && this.previewPicture) {
                        let pictureData = new FormData()
                        pictureData.append(
                            'picture',
                            JSON.stringify({
                                name: this.fileObject.name,
                                data: this.previewPicture,
                                item_id: response.data.item.id
                            })
                        )
                        await this.uploadPicture(pictureData)
                    }
                    window.toastr['success'](this.$tc('items.updated_message'))
                    this.$router.push('/admin/items')
                } catch (error) {
                    const objectErrors = error.response.data.errors
                    if (objectErrors) {
                        Object.keys(objectErrors).map((key) => {
                        objectErrors[key].map((error) => {
                            window.toastr['error'](error)
                        })
                        })
                    }else {
                        window.toastr['error'](this.$tc('items.error_message'))
                    }   
                }finally {
                        this.isLoading = false
                    }
            } else {
                let data = {
                    ...this.formData,
                    taxes: this.formData.taxes.map((tax) => {
                        return {
                            tax_type_id: tax.id,
                            amount: (this.formData.price * tax.percent) / 100,
                            percent: tax.percent,
                            name: tax.name,
                            collective_tax: 0,
                        }
                    }),
                }

                if (this.formData.avalara_service_type == null && this.formData.avalara_type == null && this.isAddAvalara) {
                    window.toastr['error'](this.$tc('items.avalara_errors'))
                    this.isLoading = false
                    response = false
                } 
                else if (this.formData.avalara_service_type == null && this.isAddAvalara) {
                    window.toastr['error'](this.$tc('items.avalara_errors'))
                    this.isLoading = false
                    response = false
                }
                else if (this.formData.avalara_type == null && this.isAddAvalara) {
                    window.toastr['error'](this.$tc('items.avalara_errors'))
                    this.isLoading = false
                    response = false
                } else {
                    try {
                        let response = await this.addItem(data)

                        if (this.fileObject && this.previewPicture) {
                            let pictureData = new FormData()
                            pictureData.append(
                                'picture',
                                JSON.stringify({
                                    name: this.fileObject.name,
                                    data: this.previewPicture,
                                    item_id: response.data.item.id
                                })
                            )
                            await this.uploadPicture(pictureData)
                        }
                         window.toastr['success'](this.$tc('items.created_message'))
                         this.$router.push('/admin/items')
                    } catch (error) {
                        const objectErrors = error.response.data.errors
                        if (objectErrors) {
                            Object.keys(objectErrors).map((key) => {
                            objectErrors[key].map((error) => {
                                window.toastr['error'](error)
                            })
                            })
                        }else {
                            window.toastr['error'](this.$tc('items.error_message'))
                        }
                    }finally {
                        this.isLoading = false
                    }
                }
                
            }
        },

        async addItemUnit() {
            this.openModal({
                title: this.$t('settings.customization.items.add_item_unit'),
                componentName: 'ItemUnit',
            })
        },

        onChange(file) {
            this.cropperOutputMime = file.type
            this.fileObject = file
        },

        onUploadHandler(cropper) {
            this.previewPicture = cropper
                .getCroppedCanvas()
                .toDataURL(this.cropperOutputMime)
        },

        onHandleUploadError() {
            window.toastr['error']('Oops! Something went wrong...')
        },
    },
}
</script>
