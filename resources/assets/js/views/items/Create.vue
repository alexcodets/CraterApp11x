<template>
  <base-page>
    <form action="" @submit.prevent="submitItem">
      <!-- Page Header -->
      <sw-page-header :title="pageTitle" class="mb-3">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />
          <sw-breadcrumb-item :title="$tc('items.item', 2)" to="/admin/items" />
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

        <template slot="actions">
          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary-outline"
            type="button"
            class="mr-3 text-sm hidden sm:flex"
            size="lg"
            @click="cancelForm()"
          >
            <x-circle-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ $t('general.cancel') }}
          </sw-button>

          <sw-button
            :loading="isLoading"
            variant="primary"
            size="lg"
            :tabindex="10"
            class="hidden sm:flex"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ isEdit ? $t('items.update_item') : $t('items.save_item') }}
          </sw-button>
        </template>
      </sw-page-header>

      <sw-card>
        <div class="grid md:grid-cols-2 lg:p-8 sm:p-1 col-span-5">
          <sw-input-group
            :label="$t('items.name')"
            :error="nameError"
            class="mt-4 pr-3"
            variant="vertical"
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
            class="mt-4 pr-3"
            variant="vertical"
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

          <sw-input-group :label="$t('items.unit')" class="mt-4 pr-3">
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

          <sw-input-group :label="$t('items.items_groups')" class="mt-4 pr-3">
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

          <sw-input-group
            :label="$t('items.items_category_main')"
            class="mt-4 pr-3"
          >
            <sw-select
              v-model="formData.item_category_id"
              :options="getItemCategories"
              :searchable="true"
              :show-labels="false"
              :allow-empty="true"
              :multiple="false"
              class="mt-2"
              track-by="id"
              label="name"
              :tabindex="4"
            />
          </sw-input-group>

          <sw-input-group :label="$t('items.items_category')" class="mt-4 pr-3">
            <sw-select
              v-model="formData.item_categories"
              :options="getItemCategories"
              :searchable="true"
              :show-labels="false"
              :allow-empty="true"
              :multiple="true"
              class="mt-2"
              track-by="id"
              label="name"
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

              <p
                class="p-0 m-0 text-xs leading-4 text-gray-500"
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
            class="mt-4 pr-3"
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

          <div class="flex my-8 mb-4" v-if="retention_platform_active">
            <sw-divider class="mb-5 md:mb-8" />
            <div class="relative w-12">
              <sw-switch
                v-model="formData.retentions_bool"
                class="absolute"
                style="top: -20px"
                @change="getRetention"
                :tabindex="6"
              />
            </div>

            <div class="ml-4">
              <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                {{ $t('items.allow_retentions') }}
              </p>
            </div>
          </div>

          <sw-input-group
            v-if="formData.retentions_bool"
            :label="$t('items.retentions')"
            class="mt-4 pr-3"
          >
            <sw-select
              v-model="formData.retentions"
              :options="retentionsOptions"
              :searchable="true"
              :show-labels="false"
              :allow-empty="true"
              class="mt-2"
              track-by="id"
              label="label"
              :tabindex="7"
            >
              <template slot="singleLabel" slot-scope="option">
                <div class="flex items-center">
                  <div v-if="option.option" class="text-sm">
                    {{
                      option.option.concept +
                      ' ' +
                      option.option.percentage +
                      '%'
                    }}
                  </div>
                </div>
              </template>

              <template slot="option" slot-scope="option">
                <div class="flex items-center">
                  <div v-if="option.option" class="text-sm">
                    {{
                      option.option.concept +
                      ' ' +
                      option.option.percentage +
                      '%'
                    }}
                  </div>
                </div>
              </template>
            </sw-select>
          </sw-input-group>
        </div>

        <div v-if="isPosAvailable">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('items.section_pos') }}
          </h6>
          <div class="flex my-8 mb-4">
            <div class="relative w-12">
              <sw-switch
                v-model="formData.allow_pos"
                class="absolute"
                style="top: -20px"
                :tabindex="6"
              />
            </div>

            <div class="ml-4">
              <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                {{ $t('items.allow_pos') }}
              </p>
            </div>
          </div>

          <sw-divider  />

          <div class="grid md:grid-cols-2 lg:p-8 sm:p-1 col-span-5">
            <sw-input-group :label="$t('core_pos.store')" class="mt-4  pr-3">
              <sw-select
                v-model="formData.item_store"
                :options="store_options"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :multiple="true"
                class="mt-2"
                track-by="id"
                label="name"
                :tabindex="1"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('core_pos.core_pos_section')"
              class="mt-4  pr-3"
            >
              <sw-select
                v-model="formData.item_section"
                :options="section_options"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :multiple="true"
                class="mt-2"
                track-by="id"
                label="name"
                :tabindex="1"
              />
            </sw-input-group>
          </div>
        </div>

        <div v-if="isAvalaraAvailable">
          <h6 class="col-span-5 sw-section-title lg:col-span-1 mt-sm-4">
            {{ $t('items.section_avalar') }}
          </h6>
          <sw-divider  />

          <div class="grid md:grid-cols-2 lg:p-8 sm:p-1 col-span-5">
            <div class="flex mt-4 mb-4">
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
              class="mt-4 pr-3"
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
                :disabled="isEdit && isItemUsed"
              />
            </sw-input-group>

            <sw-input-group
              v-if="isAddAvalara"
              :label="$t('items.avalara_service_type')"
              class="mt-4 pr-3"
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
                label="service_type_name_label"
                :tabindex="11"
                @select="serviceSeleted"
                :disabled="isEdit && isItemUsed"
              />
              <br />
              <div v-if="formData.avalara_payment_type != null">
                <label
                  v-if="formData.avalara_payment_type.value == 'TAXABLE_AMOUNT'"
                  >{{
                    $t(
                      'items.this_services_es_calculated_as_taxable_amount_and_if_apply_to_cdrs_app_rates_services_price_and_additional_charges'
                    )
                  }}</label
                >
                <label v-if="formData.avalara_payment_type.value == 'LINES'">{{
                  $t(
                    'items.this_services_is_calculated_as_line_and_is_apply_to_Did_extension'
                  )
                }}</label>
              </div>
            </sw-input-group>

            <!-- avalara sale type -->
            <sw-input-group
              v-if="isAddAvalara"
              :label="$t('items.avalara_sale_type')"
              class="mt-4 pr-3"
              required
            >
              <sw-select
                v-model="formData.avalara_sale_type"
                :options="avalara_sale_types"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('items.avalara_sale_type')"
                class="mt-2"
                label="name"
                :tabindex="9"
                :disabled="isEdit && isItemUsed"
              />
            </sw-input-group>

            <!-- avalara discount type -->
            <sw-input-group
              v-if="isAddAvalara"
              :label="$t('items.avalara_discount_type')"
              class="mt-4 pr-3"
              required
            >
              <sw-select
                v-model="formData.avalara_discount_type"
                :options="avalara_discount_types"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('items.avalara_discount_type')"
                class="mt-2"
                label="name"
                :tabindex="9"
                :disabled="isEdit && isItemUsed"
              />
            </sw-input-group>

            <!-- avalara tax inclusion -->
            <div class="flex my-8 mb-4" v-if="isAddAvalara">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.tax_inclusion"
                  class="absolute"
                  style="top: -20px"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('items.avalara_is_tax_inclusion') }}
                </p>
                <p
                  class="p-0 m-0 text-xs leading-4 text-red-500"
                  style="max-width: 480px"
                >
                  {{ $t('items.message_tax_description') }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <sw-divider class="mb-5 md:mb-8" />

        <div class="col">
          <sw-input-group
            :label="$t('items.description')"
            :error="descriptionError"
            class="mt-4"
          >
            <sw-textarea
              v-model="formData.description"
              rows="2"
              name="description"
              @input="$v.formData.description.$touch()"
              :tabindex="8"
            />
          </sw-input-group>

          <sw-input-group :label="$tc('items.image')" class="mt-6 mb-4">
            <div
              id="logo-box"
              class="relative flex items-center justify-center h-24 p-5 mt-2 bg-transparent border-2 border-gray-200 border-dashed rounded-md image-upload-box"
            >
              <img
                v-if="previewPicture != '0'"
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
                  <span
                    id="pick-avatar"
                    class="cursor-pointer text-primary-500"
                  >
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
        </div>
        <sw-button
          :loading="isLoading"
          :disabled="isLoading"
          variant="primary-outline"
          type="button"
          class="mr-3 flex w-full mt-4 sm:hidden md:hidden"
          size="lg"
          @click="cancelForm()"
        >
          <x-circle-icon v-if="!isLoading" class="mr-2 -ml-1" />
          {{ $t('general.cancel') }}
        </sw-button>

        <sw-button
          :loading="isLoading"
          variant="primary"
          size="lg"
          :tabindex="10"
          class="flex w-full mt-4 mb-2 mb-md-0 sm:hidden md:hidden"
        >
          <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
          {{ isEdit ? $t('items.update_item') : $t('items.save_item') }}
        </sw-button>
      </sw-card>
    </form>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import {
  ShoppingCartIcon,
  CloudUploadIcon,
  TrashIcon,
  PencilIcon,
  PlusIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
// import TheSiteHeaderVue from '../layouts/partials/TheSiteHeader.vue'
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
    CloudUploadIcon,
    XCircleIcon,
  },

  data() {
    return {
      store_options: [],
      section_options: [],
      isPosAvailable: false,
      isAvalaraAvailable: false,
      retention_platform_active: false,
      isLoading: false,
      itemUsage: {
        pbx_package_item: [],
        avalara_config_item: [],
      },
      title: 'Add Item',
      units: [],
      taxes: [],
      taxPerItem: '',
      taxable: 'YES',
      showNoTaxable: 'YES',
      add_avalara: false,
      avalara_type_selected: null,

      avalara_service_types_desc: '',
      isServiceData: false,
      formData: {
        name: '',
        description: '',
        price: '',
        unit_id: null,
        item_category_id: null,
        unit: null,
        allow_taxes: false,
        allow_pos: false,
        no_taxable: false,
        taxes: [],
        item_groups: [],
        avalara_bool: false,
        avalara_type: '',
        avalara_sale_type: { name: 'Retail', value: 'Retail' },
        avalara_discount_type: { name: 'None', value: '0' },
        avalara_service_types: [],
        avalara_service_type: null,
        avalara_service_type_name: '',
        avalara_payment_type: '',
        retentions_bool: false,
        retentions: null,
        tax_inclusion: false,
        item_categories: [],
        item_store: [],
        item_section: [],
      },
      money: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        precision: 2,
        masked: false,
      },
      avalara_sale_types: [
        { name: 'Wholesale', value: 'Wholesale' },
        { name: 'Retail', value: 'Retail' },
        { name: 'Consumed', value: 'Consumed' },
        { name: 'Vendor Use', value: 'Vendor Use' },
      ],
      avalara_discount_types: [
        { name: 'None', value: '0' },
        { name: 'Retail Product', value: '1' },
        { name: 'Manufacturer Product', value: '2' },
        { name: 'Account Level', value: '3' },
        { name: 'Subsidized', value: '4' },
        { name: 'Goodwil', value: '5' },
      ],
      avalara_payment_type: [
        { name: 'Taxable Amount', value: 'TAXABLE_AMOUNT' },
        { name: 'Minutes', value: 'MINUTES' },
        { name: 'Lines', value: 'LINES' },
      ],
      avalara_types: [
        { name: 'No Tax (0)', value: '0' },
        { name: 'Interstate (1)', value: '1' },
        { name: 'Intrastate (2)', value: '2' },
        { name: 'Other (3)', value: '3' },
        { name: 'Non-recurring (4)', value: '4' },
        { name: 'Paging (5)', value: '5' },
        { name: 'internet (6)', value: '6' },
        { name: 'Local (7)', value: '7' },
        { name: 'Fax (8)', value: '8' },
        { name: 'Voice Mail (9)', value: '9' },
        { name: 'Sales (10)', value: '10' },
        { name: 'Shipping (11)', value: '11' },
        { name: 'Cellular (13)', value: '13' },
        { name: 'International (14)', value: '14' },

        { name: 'Telephony (15)', value: '15' },
        { name: 'Cable Television (16)', value: '16' },
        { name: 'Satellite Television (18)', value: '18' },
        { name: 'VoIP (19)', value: '19' },
        { name: 'VoIPA (20)', value: '20' },
        { name: 'Payphone (21)', value: '21' },
        { name: 'Software (24)', value: '24' },
        { name: 'Timesharing (25)', value: '25' },

        { name: 'Electronic Equipment & Computer Hardware (32)', value: '32' },
        { name: 'General Merchandise (34)', value: '34' },

        { name: 'Magazines (36)', value: '36' },
        { name: 'Newspaper (42)', value: '42' },
        { name: 'Rentals & Leasing (44)', value: '44' },
        { name: 'Services Printing (47)', value: '47' },
        { name: 'Services Professional (48)', value: '48' },
        { name: 'Services Repair (50)', value: '50' },
        { name: 'Digital Goods (57)', value: '57' },
        { name: 'Dark Fiber (58)', value: '58' },
        { name: '(VoIP- Nomadic) (59)', value: '59' },
        { name: 'Satellite Phone (60)', value: '60' },

        { name: 'VPN (61)', value: '61' },
        { name: 'Conferencing (64)', value: '64' },
        { name: '(Non-Interconnected VoIP) (65)', value: '65' },
        { name: 'Override (66)', value: '66' },
      ],
      avalara_service_types: [],
      previewPicture: null,
      fileObject: null,
      cropperOutputMime: '',
      retentionsOptions: [],
      getItemCategories: [],
    }
  },

  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),

    ...mapGetters('item', ['itemUnits']),

    ...mapGetters('taxType', ['taxTypes']),

    ...mapGetters('itemGroups', ['itemGroups']),
    ...mapGetters('modules', ['modules']),

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

    isItemUsed() {
      return this.itemUsage.pbx_package_item.length > 0 ||
        this.itemUsage.avalara_config_item.length > 0
        ? true
        : false
    },

    isTaxable() {
      return this.taxable === 'YES' ? 1 : 0
    },

    noTaxable() {
      return this.showNoTaxable === 'YES' ? 1 : 0
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
          item_group_name: group.name,
        }
      })
    },
    // getItemCategoriesData() {
    //   console.log(this.itemCategories)
    //   return this.itemCategories.map((category) => {
    //     console.log(category)
    //     return {
    //       ...category,
    //       item_category_id: category.id,
    //       item_category_name: category.name,
    //     }
    //   })
    // },
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
        return this.$t('validation.message_maxlength')
      }
    },
    isAddAvalara() {
      this.add_avalara = this.formData.avalara_bool
      return this.add_avalara
    },
  },

  created() {
    this.loadData()
    // this.getStatusModuleAvalara()
    this.fetchModulePos()
  },

  mounted() {
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
        maxLength: maxLength(255),
      },
    },
  },

  methods: {
    ...mapActions('item', [
      'addItem',
      'fetchItem',
      'updateItem',
      'fetchItemUnits',
      'uploadPicture',
      'fetchItemUsage',
      'fetchItemCategories',
    ]),

    ...mapActions('taxType', ['fetchTaxTypes']),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('modal', ['openModal']),

    ...mapActions('itemGroups', ['fetchItemGroups']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('avalara', ['checkStatusAvalara']),

    ...mapActions('modules', ['getModules']),
    ...mapActions('corePos', ['fetchStores', 'fetchSections']),

    //Obtener info modulo POS
    async fetchModulePos() {
      const modules = ['corePOS', 'Avalara']
      const modulesArray = await this.getModules(modules)
      const moduleCorePos = modulesArray.modules.find(
        (element) => element.name === 'corePOS'
      )
      const moduleAvalara = modulesArray.modules.find(
        (element) => element.name === 'Avalara'
      )

      if (moduleCorePos && moduleCorePos.status == 'A') {
        this.isPosAvailable = true
      }
      if (moduleAvalara && moduleAvalara.status == 'A') {
        this.isAvalaraAvailable = true
      }
    },

    async fetchStoresData() {
      let data = {
        limit: 'all',
      }

      const response = await this.fetchStoresData(data)
    },

    getTypeOfPaymentbyService(service_type_name) {
      let payment_type = ''
      switch (service_type_name) {
        case 'Access Charge':
          payment_type = 'TAXABLE_AMOUNT'
          break
        case 'Access-Local Only Service':
          payment_type = 'TAXABLE_AMOUNT'
          break
        case 'Activation':
          payment_type = 'TAXABLE_AMOUNT'
          break
        case 'Enhanced Feature Charge':
          payment_type = 'TAXABLE_AMOUNT'
          break
        case 'Equipment Rental':
          payment_type = 'TAXABLE_AMOUNT'
          break
        case 'Equipment Repair':
          payment_type = 'TAXABLE_AMOUNT'
          break
        case 'Install':
          payment_type = 'TAXABLE_AMOUNT'
          break
        case 'Invoice':
          payment_type = 'NONE'
          break
        case 'Late Charge':
          payment_type = 'TAXABLE_AMOUNT'
          break
        case 'Lines':
          payment_type = 'LINES'
          break
        case 'LNP (Local Number Portability)':
          payment_type = 'TAXABLE_AMOUNT'
          break
        case 'Local Feature Charge':
          payment_type = 'TAXABLE_AMOUNT'
          break
        case 'PBX':
          payment_type = 'LINES'
          break
        case 'PBX Extension':
          payment_type = 'LINES'
          break
        case 'PBX High Capacity':
          payment_type = 'LINES'
          break
        case 'PBX Outbound Channel':
          payment_type = 'LINES'
          break
        case 'Toll-Free Number':
          payment_type = 'TAXABLE_AMOUNT'
          break
        case 'Wireless Access Charge':
          payment_type = 'TAXABLE_AMOUNT'
          break
        case 'Wireless Lines':
          payment_type = 'LINES'
          break
        default:
          break
      }

      return payment_type
    },

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
        this.taxPerItem = 'YES'
        this.showNoTaxable = 'NO'
        this.formData.no_taxable = false
      } else {
        this.taxPerItem = 'NO'
        this.showNoTaxable = 'YES'
        this.formData.taxes = []
      }
    },

    setTaxable() {
      if (this.formData.no_taxable) {
        this.taxable = 'NO'
        this.formData.allow_taxes = false
        this.formData.taxes = []
      } else {
        this.taxable = 'YES'
      }
    },
    async getRetention(active) {
      try {
        if (!active) return
        const res = await this.$store.dispatch('retentions/fetchRetentions', {
          limit: 1000,
        })
        this.retentionsOptions = res.data.retentions.data
      } catch (e) {}
    },
    async transactionSeleted(val) {
      this.isServiceData = false
      if (val == null) {
        return false
      }

      if (this.avalara_service_types != null) {
        this.formData.avalara_service_types = null
      }

      let res = await window.axios.get(
        '/api/v1/avalara-service-types/' + val.value
      )
      if (res) {
        this.avalara_service_types = res.data.avalara_service_types
        this.avalara_service_types.forEach((s) => {
          s.service_type_name_label =
            s.service_type_name + ' (' + s.service_type + ')'
        })
      }
    },

    async serviceSeleted(val) {
      this.isServiceData = false

      if (this.avalara_payment_type != null) {
        this.formData.avalara_payment_type = null
      }
      this.formData.avalara_service_types = val

      let countRepeated = 0

      if (val.taxable_amount) {
        countRepeated++
        this.formData.avalara_payment_type = this.avalara_payment_type.find(
          (s) => s.value == 'TAXABLE_AMOUNT'
        )
        return false
      }
      if (val.lines) {
        countRepeated++
        this.formData.avalara_payment_type = this.avalara_payment_type.find(
          (s) => s.value == 'LINES'
        )
        return false
      }

      if (val.minutes) {
        countRepeated++
        this.formData.avalara_payment_type = this.avalara_payment_type.find(
          (s) => s.value == 'MINUTES'
        )
      }

      if (countRepeated > 1) {
        this.isServiceData = true
      }
    },

    async avalaraPaymentSeleted(val) {
      this.formData.avalara_payment_type = val.value
    },

    async loadData() {
      // validar el access
      // verificar si edicion = update
      // verificar si edicion false =  create

      let dataSection = {
        limit: 'all',
      }

      const responseSection = await this.fetchSections(dataStore)
      this.section_options = responseSection.data.sections.data

      let dataStore = {
        limit: 'all',
      }

      const responseStore = await this.fetchStores(dataStore)
      this.store_options = responseStore.data.stores.data

      this.isModulesAvailalable
      let response = await this.fetchCompanySettings([
        'retention_platform_active',
      ])

      if (response.data) {
        this.retention_platform_active =
          response.data.retention_platform_active === 'YES'
      }

      const data = {
        module: 'items',
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if (permissions.super_admin == false) {
        if (permissions.exist == false) {
          this.$router.push('/admin/dashboard')
        } else {
          const modulePermissions = permissions.permissions[0]
          if (modulePermissions.create == 0 && this.isEdit == false) {
            this.$router.push('/admin/dashboard')
          } else if (modulePermissions.update == 0 && this.isEdit == true) {
            this.$router.push('/admin/dashboard')
          }
        }
      }

      const response_item_categories = await this.fetchItemCategories()
      if (response_item_categories.data.item_categories.length > 0) {
        this.getItemCategories = [
          ...response_item_categories.data.item_categories,
        ]
      }

      if (this.isEdit) {
        let response = await this.fetchItem(this.$route.params.id)
        this.formData = { ...response.data.item, unit: null }

        this.formData.avalara_sale_type = this.avalara_sale_types.find(
          (opcion) => opcion.value === response.data.item.avalara_sale_type
        )
        this.formData.avalara_discount_type = this.avalara_discount_types.find(
          (opcion) => opcion.value === response.data.item.avalara_discount_type
        )
        this.formData.tax_inclusion =
          response.data.item.avalara_tax_inclusion == 1 ? true : false
        // item category
        // this.formData.item_category =
        //   response.data.item.item_category_id != null
        //     ? this.getItemCategory(response.data.item.item_category_id)
        //     : null

        this.formData.item_categories = response.data.item.item_categories.map(
          (itemCat) => {
            return {
              id: itemCat.id,
              name: itemCat.name,
            }
          }
        )

        this.previewPicture = response.data.item.picture.toString()

        this.fractional_price = response.data.item.price

        if (response.data.item.retentions_bool) {
          this.getRetention(true)
          this.formData.retentions_bool = response.data.item.retentions_bool
          this.formData.retentions = response.data.item.retentions
        }

        // console.log(this.formData);
        if (this.formData.unit_id) {
          await this.fetchItemUnits({ limit: 'all' })
          this.formData.unit = this.itemUnits.find(
            (_unit) => response.data.item.unit_id === _unit.id
          )
        }

        if (this.formData.item_category_id) {
          this.formData.item_category_id = this.getItemCategories.find(
            (_unit) => this.formData.item_category_id === _unit.id
          )
        }

        if (this.formData.taxes) {
          await this.fetchTaxTypes({ limit: 'all' })
          this.formData.taxes = response.data.item.taxes.map((tax) => {
            return { ...tax, tax_name: tax.name + '(' + tax.percent + '%)' }
          })
        }

        if (this.formData.item_groups) {
          await this.fetchItemGroups({ limit: 'all' })
          this.formData.item_groups = response.data.item.item_groups.map(
            (itemGroup) => {
              return {
                ...itemGroup,
                item_group_id: itemGroup.id,
                item_group_name: itemGroup.name,
              }
            }
          )
        }

        this.formData.allow_taxes === 1
          ? (this.formData.allow_taxes = true)
          : (this.formData.allow_taxes = false)

        this.formData.no_taxable === 1
          ? (this.formData.no_taxable = true)
          : (this.formData.no_taxable = false)

        this.setTax()
        this.setTaxable()

        let a_type_name = ''

        switch (this.formData.avalara_type) {
          case '0':
            a_type_name = 'No Tax'
            break

          case '1':
            a_type_name = 'Interstate'
            break
          case '2':
            a_type_name = 'Intrastate'
            break

          case '3':
            a_type_name = 'Other'
            break

          case '4':
            a_type_name = 'Non-recurring'
            break

          case '5':
            a_type_name = 'Internet'
            break

          case '6':
            a_type_name = 'Paging'
            break

          case '7':
            a_type_name = 'Local'
            break
          case '8':
            a_type_name = 'Fax'
            break
          case '9':
            a_type_name = 'Voice Mail'
            break
          case '10':
            a_type_name = 'Sales'
            break

          case '11':
            a_type_name = 'Shipping'
            break

          case '13':
            a_type_name = 'Cellular'
            break

          case '14':
            a_type_name = 'International'
            break

          case '15':
            a_type_name = 'Telephony'
            break

          case '16':
            a_type_name = 'Cable Television'
            break

          case '18':
            a_type_name = 'Satellite Television'
            break

          case '19':
            a_type_name = 'VoIP'
            break
          case '20':
            a_type_name = 'VoIPA'
            break

          case '21':
            a_type_name = 'Payphone'
            break
          case '24':
            a_type_name = 'Software'
            break
          case '25':
            a_type_name = 'Timesharing'
            break

          case '32':
            a_type_name = 'Electronic Equipment & Computer Hardware'
            break

          case '34':
            a_type_name = 'General Merchandise'
            break

          case '36':
            a_type_name = 'Magazines'
            break

          case '42':
            a_type_name = 'Newspaper'
            break

          case '44':
            a_type_name = 'Rentals & Leasing'
            break

          case '47':
            a_type_name = 'Services Printing'
            break

          case '48':
            a_type_name = 'Services Professional'
            break

          case '50':
            a_type_name = 'Services Repair'
            break

          case '57':
            a_type_name = 'Digital Goods'
            break

          case '58':
            a_type_name = 'Dark Fiber'
            break

          case '59':
            a_type_name = '(VoIP- Nomadic)'
            break

          case '60':
            a_type_name = 'Satellite Phone'
            break

          case '61':
            a_type_name = 'VPN'
            break

          case '64':
            a_type_name = 'Conferencing'
            break

          case '65':
            a_type_name = '(Non-Interconnected VoIP)'
            break

          case '66':
            a_type_name = 'Override'
            break
          default:
            break
        }

        if (a_type_name != '') {
          this.avalara_type_selected = {
            name: a_type_name + ' (' + this.formData.avalara_type + ')',
            value: this.formData.avalara_type,
          }
        }

        if (response.data.item.avalara_bool) {
          let res = await window.axios.get(
            '/api/v1/avalara-service-types/' + response.data.item.avalara_type
          )
          if (res) {
            this.avalara_service_types = res.data.avalara_service_types
            this.avalara_service_types.forEach((s) => {
              s.service_type_name_label =
                s.service_type_name + ' (' + s.service_type + ')'
            })
            this.formData.avalara_service_types =
              this.avalara_service_types.find(
                (s) => s.id == this.formData.avalara_service_type
              )
          }
          // fetch items on avalar_config and pbx_pacakges
          let resItemUsage = await this.fetchItemUsage(response.data.item.id)
          this.itemUsage = { ...resItemUsage.data }
        }

        let countRepeated = 0
        if (response.data.item.avalara_service_types) {
          countRepeated++
        }

        if (response.data.item.hasOwnProperty('avalara_service_types')) {
          if (response.data.item.avalara_service_types.minutes) {
            countRepeated++
          }
        }

        if (response.data.item.hasOwnProperty('avalara_service_types')) {
          if (response.data.item.avalara_service_types.lines) {
            countRepeated++
          }
        }

        if (countRepeated > 1) {
          this.isServiceData = true
        }
        if (countRepeated > 0) {
          let type_name = ''
          switch (this.formData.avalara_payment_type) {
            case 'TAXABLE_AMOUNT':
              type_name = 'Taxable Amount'
              break
            case 'LINES':
              type_name = 'Lines'
              break
            case 'MINUTES':
              type_name = 'Minutes'
              break
            default:
              break
          }
          this.formData.avalara_payment_type = {
            name: type_name,
            value: this.formData.avalara_payment_type,
          }
        }
      } else {
        this.fetchItemUnits({ limit: 'all' })
        this.fetchTaxTypes({ limit: 'all' })
        this.fetchItemGroups({ limit: 'all' })
      }
    },

    // getItemCategory(id) {
    //   let category = this.getItemCategories.filter(
    //     (category) => category.id == id
    //   )
    //   return category.length > 0 ? category[0] : null
    // },

    async submitItem() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return false
      }

      if (this.formData.unit) {
        this.formData.unit_id = this.formData.unit.id
      }

      if (this.formData.item_category_id) {
        this.formData.item_category_id = this.formData.item_category_id.id
      }
      if (this.formData.retentions_bool) {
        if (this.formData.retentions == null) {
          window.toastr['error'](this.$tc('items.retentions_required'))
          return false
        }
        this.formData.retentions_id = this.formData.retentions?.id
      }

      if (this.avalara_type_selected && this.isAddAvalara) {
        this.formData.avalara_type = this.avalara_type_selected.value
      } else {
        this.formData.avalara_type = null
      }

      if (this.formData.avalara_service_types && this.isAddAvalara) {
        this.formData.avalara_service_type =
          this.formData.avalara_service_types.id
        this.formData.avalara_service_type_name =
          this.formData.avalara_service_types.service_type_name
      } else {
        this.formData.avalara_service_type = null
      }

      this.formData.avalara_payment_type =
        this.formData.avalara_payment_type?.value

      // if (this.formData.avalara_service_types && this.isAddAvalara) {
      //   if (
      //     this.isServiceData) {
      //     this.formData.avalara_payment_type = this.formData.avalara_payment_type.value
      //   } else {
      //     if(this.formData.avalara_service_types.taxable_amount){
      //       this.formData.avalara_payment_type = 'TAXABLE_AMOUNT'
      //     }
      //     if(this.formData.avalara_service_types.lines){
      //       this.formData.avalara_payment_type = 'LINES'
      //     }
      //     if(this.formData.avalara_service_types.minutes){
      //       this.formData.avalara_payment_type = 'MINUTES'
      //     }
      //   }
      // }

      //return

      let text = ''
      if (this.isEdit) {
        text = 'items.edit_item_text'
      } else {
        text = 'items.create_item_text'
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc(text),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response
          this.isLoading = true

          if (this.isEdit) {
            try {
              if (
                this.formData.avalara_service_type == null &&
                this.formData.avalara_type == null &&
                this.isAddAvalara
              ) {
                window.toastr['error'](this.$tc('items.avalara_errors'))
                this.isLoading = false
                response = false
              } else if (
                this.formData.avalara_service_type == null &&
                this.isAddAvalara
              ) {
                window.toastr['error'](this.$tc('items.avalara_errors'))
                this.isLoading = false
                response = false
              } else if (
                this.formData.avalara_type == null &&
                this.isAddAvalara
              ) {
                window.toastr['error'](this.$tc('items.avalara_errors'))
                this.isLoading = false
                response = false
              } else {
                let response = await this.updateItem(this.formData)
                if (this.fileObject && this.previewPicture) {
                  let pictureData = new FormData()
                  pictureData.append(
                    'picture',
                    JSON.stringify({
                      name: this.fileObject.name,
                      data: this.previewPicture,
                      item_id: response.data.item.id,
                    })
                  )
                  await this.uploadPicture(pictureData)
                }
                window.toastr['success'](this.$tc('items.updated_message'))
                this.$router.push('/admin/items')
              }
            } catch (error) {
              const objectErrors = error.response.data.errors
              if (objectErrors) {
                Object.keys(objectErrors).map((key) => {
                  objectErrors[key].map((error) => {
                    window.toastr['error'](error)
                  })
                })
              } else {
                window.toastr['error'](this.$tc('items.error_message'))
              }
            } finally {
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

            if (
              this.formData.avalara_service_type == null &&
              this.formData.avalara_type == null &&
              this.isAddAvalara
            ) {
              window.toastr['error'](this.$tc('items.avalara_errors'))
              this.isLoading = false
              response = false
            } else if (
              this.formData.avalara_service_type == null &&
              this.isAddAvalara
            ) {
              window.toastr['error'](this.$tc('items.avalara_errors'))
              this.isLoading = false
              response = false
            } else if (
              this.formData.avalara_type == null &&
              this.isAddAvalara
            ) {
              window.toastr['error'](this.$tc('items.avalara_errors'))
              this.isLoading = false
              response = false
            } else {
              try {
                // create
                let responseData = await this.addItem(data)

                if (this.fileObject && this.previewPicture) {
                  let pictureData = new FormData()
                  pictureData.append(
                    'picture',
                    JSON.stringify({
                      name: this.fileObject.name,
                      data: this.previewPicture,
                      item_id: responseData.data.item.id,
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
                } else {
                  window.toastr['error'](this.$tc('items.error_message'))
                }
              } finally {
                this.isLoading = false
              }
            }
          }
        }
      })
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

    cancelForm() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.lose_unsaved_information'),
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          this.$router.go(-1)
        }
      })
    },
    async getStatusModuleAvalara() {
      const response = await this.checkStatusAvalara()
      //this.isAvalaraAvailable = response.data.success
      this.isAvalaraAvailable = true
    },
  },
}
</script>
