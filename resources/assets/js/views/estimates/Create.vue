<style>
.scrollable-div2 {
overflow-x: auto; /* Habilita el desplazamiento horizontal si el contenido excede el ancho del div */
width: 100%; /* El div toma el 100% del ancho de su contenedor */
-webkit-overflow-scrolling: touch; /* Permite un desplazamiento táctil fluido en iOS */
}
.scrollable-div2 table {
width: 100%; /* Opcional: asegura que la tabla se expanda para llenar el div */
/* Tus estilos de tabla aquí */
}

.table-wrapperest {
min-width: 800px; /* Establece un ancho mínimo de 800 píxeles para la tabla */
}
</style>


<template>
  <base-page class="relative estimate-create-page">
    <form
      v-if="isLoadingEstimate && isLoadingData"
      @submit.prevent="submitForm"
    >
      <sw-page-header :title="pageTitle">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />
          <sw-breadcrumb-item
            :title="$tc('estimates.estimate', 2)"
            to="/admin/estimates"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'estimates.edit'"
            :title="$t('estimates.edit_estimate')"
            to="#"
            active
          />
          <sw-breadcrumb-item
            v-else
            :title="$t('estimates.new_estimate')"
            to="#"
            active
          />
        </sw-breadcrumb>

        <template slot="actions">
          <sw-button
            variant="primary-outline"
            type="button"
            size="lg"
            class="mr-3 text-sm hidden sm:flex"
            @click="cancelForm"
          >
            <x-circle-icon class="w-6 h-6 mr-1 -ml-2" v-if="!isLoading" />
            {{ $t('general.cancel') }}
          </sw-button>

          <sw-button
            v-if="$route.name === 'estimates.edit'"
            :href="`/estimates/pdf/${newEstimate.unique_hash}`"
            tag-name="a"
            target="_blank"
            class="mr-3 hidden sm:flex"
            variant="primary-outline"
            type="button"
          >
            <span class="flex">
              {{ $t('general.view_pdf') }}
            </span>
          </sw-button>

          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="mr-3 hidden sm:flex"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ $t('estimates.save_estimate') }}
          </sw-button>
        </template>
      </sw-page-header>

      <!-- Select Customer & Basic Fields  -->
      <div class="grid-cols-12 gap-8 mt-6 mb-5 lg:grid">
        <customer-select
          :valid="$v.selectedCustomer"
          :customer-id="customerId"
          class="col-span-5 pr-0"
        />

        <div
          class="grid grid-cols-1 col-span-7 gap-4 mt-8 lg:gap-6 lg:mt-0 lg:grid-cols-2"
        >
          <sw-input-group
            :label="$t('reports.estimates.estimate_date')"
            :erorr="estimateDateError"
            required
          >
            <base-date-picker
              v-model="newEstimate.estimate_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
              class="mt-2"
              @input="setExpiryDate"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('estimates.expiry_date')"
            :error="expiryDateError"
            required
          >
            <base-date-picker
              v-model="newEstimate.expiry_date"
              :invalid="$v.newEstimate.expiry_date.$error"
              :calendar-button="true"
              calendar-button-icon="calendar"
              class="mt-2"
              ref="expiryDate"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('estimates.estimate_number')"
            :error="estimateNumError"
            class="lg:mt-0"
            required
          >
            <sw-input
              :prefix="`${estimatePrefix} - `"
              v-model="estimateNumAttribute"
              :invalid="$v.estimateNumAttribute.$error"
              class="mt-2"
              @input="$v.estimateNumAttribute.$touch()"
            >
              <hashtag-icon slot="leftIcon" class="h-4 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>

          <sw-input-group
            :label="$t('estimates.ref_number')"
            :error="referenceNumError"
            class="lg:mt-0"
          >
            <sw-input
              v-model="newEstimate.reference_number"
              :invalid="$v.newEstimate.reference_number.$error"
              class="mt-2"
              @input="$v.newEstimate.reference_number.$touch()"
            >
              <hashtag-icon slot="leftIcon" class="h-4 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>
        </div>
      </div>
      <div class="w-full flex flex-wrap">
        <div class="w-full md:w-1/3">
          <sw-input-group
            :label="$t('estimates.assigned_to')"
            :error="assignUserError"
            class="mb-4 md:mr-2"
            required
          >
            <sw-select
              :placeholder="$t('estimates.select_a_user')"
              v-model="assignUser"
              :options="usersOptions"
              :searchable="true"
              :show-labels="false"
              class="mt-2"
              label="name"
              track-by="id"
              :invalid="$v.assignUser.$error"
              @input="$v.assignUser.$touch()"
            />
          </sw-input-group>
        </div>
        <div class="w-full md:w-2/3">
          <sw-input-group :label="$t('estimates.users')" class="mb-4 md:ml-2">
            <sw-select
              v-model="assignUserGroup"
              :options="usersOptions"
              :searchable="true"
              :show-labels="false"
              :allow-empty="true"
              :multiple="true"
              class="mt-2"
              track-by="id"
              label="name"
            />
          </sw-input-group>
        </div>
      </div>

      <div class="scrollable-div2">
      <!-- Items -->
      <table class="w-full text-center item-table table-wrapper table-wrapperest">
        <colgroup>
          <col style="width: 40%" />
          <col style="width: 10%" />
          <col style="width: 15%" />
          <col v-if="discountPerItem === 'YES'" style="width: 15%" />
          <col style="width: 15%" />
        </colgroup>
        <thead class="bg-white border border-gray-200 border-solid">
          <tr>
            <th
              class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
            >
              <span class="pl-12">
                {{ $tc('items.item', 2) }}
              </span>
            </th>
            <th
              class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
            >
              <span>
                {{ $t('estimates.item.quantity') }}
              </span>
            </th>
            <th
              class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
            >
              <span>
                {{ $t('estimates.item.price') }}
              </span>
            </th>
            <th
              v-if="discountPerItem === 'YES'"
              class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
            >
              <span>
                {{ $t('estimates.item.discount') }}
              </span>
            </th>
            <th
              class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
            >
              <span class="pr-10 column-heading">
                {{ $t('estimates.item.total') }}
              </span>
            </th>
          </tr>
        </thead>

        <draggable
          v-model="newEstimate.items"
          class="item-body"
          tag="tbody"
          handle=".handle"
        >
          <estimate-item
            v-for="(item, index) in newEstimate.items"
            :key="item.id"
            :index="index"
            :item-data="item"
            :currency="currency"
            :estimate-items="newEstimate.items"
            :tax-per-item="taxPerItem"
            :discount-per-item="discountPerItem"
            @remove="removeItem"
            @update="updateItem"
            @itemValidate="checkItemsData"
          />
        </draggable>
      </table>
    </div>
      <div
        class="flex items-center justify-center w-full px-6 py-3 text-base border-b border-gray-200 border-solid cursor-pointer text-primary-400 hover:bg-gray-200"
        @click="addItem"
      >
        <shopping-cart-icon class="h-5 mr-2" />
        {{ $t('estimates.add_item') }}
      </div>

      <!-- Notes, Custom Fields & Total Section -->
      <div
        class="block my-10 estimate-foot lg:flex lg:justify-between lg:items-start"
      >
        <div class="w-full lg:w-1/2">
          <div class="mb-6">
            <sw-popup
              ref="notePopup"
              class="z-10 text-sm font-semibold leading-5 text-primary-400"
            >
              <div slot="activator" class="float-right mt-1">
                + {{ $t('general.insert_note') }}
              </div>
              <note-select-popup type="Estimate" @select="onSelectNote" />
            </sw-popup>

            <sw-input-group :label="$t('estimates.notes')">
              <base-custom-input
                v-model="newEstimate.notes"
                :fields="EstimateFields"
              />
            </sw-input-group>
          </div>

          <div
            v-if="customFields.length > 0"
            class="grid gap-x-4 gap-y-2 md:gap-x-8 md:gap-y-4 grid-col-1 md:grid-cols-2"
          >
            <sw-input-group
              v-for="(field, index) in customFields"
              :label="field.label"
              :required="field.is_required ? true : false"
              :key="index"
            >
              <component
                :type="field.type.label"
                :field="field"
                :is-edit="isEdit"
                :is="field.type + 'Field'"
                :invalid-fields="invalidFields"
                @update="setCustomFieldValue"
              />
            </sw-input-group>
          </div>

          <!-- <sw-input-group
            :label="$t('estimates.estimate_template')"
            class="mt-6 mb-1"
            required
          >
            <sw-button
              type="button"
              class="flex justify-center w-full text-sm lg:w-auto hover:bg-gray-400"
              variant="gray"
              @click="openTemplateModal"
            >
              <span class="flex text-black">
                {{ $t('estimates.estimate_template') }} {{ getTemplateId }}
                <pencil-icon class="h-5 ml-2 -mr-1" />
              </span>
            </sw-button>
          </sw-input-group> -->
        </div>
        <div
          class="px-5 py-4 mt-6 bg-white border border-gray-200 border-solid rounded estimate-total lg:mt-0"
        >
          <div class="flex items-center justify-between w-full">
            <label
              class="text-sm font-semibold leading-5 text-gray-500 uppercase"
              >{{ $t('estimates.sub_total') }}</label
            >
            <label
              class="flex items-center justify-center m-0 text-lg text-black uppercase"
            >
              <div v-html="$utils.formatMoney(subtotal, currency)" />
            </label>
          </div>
          <div
            v-for="tax in allTaxes"
            :key="tax.tax_type_id"
            class="flex items-center justify-between w-full"
          >
            <label
              class="m-0 text-sm font-semibold leading-5 text-gray-500 uppercase"
              >{{ tax.name }} - {{ tax.percent }}%
            </label>
            <label
              class="flex items-center justify-center m-0 text-lg text-black uppercase"
              style="font-size: 18px"
            >
              <div v-html="$utils.formatMoney(tax.amount, currency)" />
            </label>
          </div>

          <div
            v-if="discountPerItem === 'NO' || discountPerItem === null"
            class="flex items-center justify-between w-full mt-2"
          >
            <label
              class="text-sm font-semibold leading-5 text-gray-500 uppercase"
              >{{ $t('estimates.discount') }}</label
            >
            <div class="flex" style="width: 105px" role="group">
              <sw-input
                v-model="discount"
                :invalid="$v.newEstimate.discount_val.$error"
                class="border-r-0 rounded-tr-sm rounded-br-sm"
                @input="$v.newEstimate.discount_val.$touch()"
                onkeydown="javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) ? true : !isNaN(Number(event.key)) && event.code!=='Space'"
              />
              <sw-dropdown position="bottom-end" id="dropdown">
                <sw-button
                  slot="activator"
                  type="button"
                  data-toggle="dropdown"
                  size="discount"
                  aria-haspopup="true"
                  aria-expanded="false"
                  style="height: 43px"
                  variant="white"
                  @click="dropdownSelect('click')"
                  @focusout="dropdownSelect('focusOut')"
                  tabindex="0"
                  ref="button"
                >
                  <span class="flex">
                    {{
                      newEstimate.discount_type == 'fixed'
                        ? currency.symbol
                        : '%'
                    }}
                    <chevron-down-icon class="h-5" />
                  </span>
                </sw-button>

                <sw-dropdown-item @click="selectFixed">
                  {{ $t('general.fixed') }}
                </sw-dropdown-item>

                <sw-dropdown-item @click="selectPercentage">
                  {{ $t('general.percentage') }}
                </sw-dropdown-item>
              </sw-dropdown>
            </div>
          </div>

          <div :style="activeDropdownDiscount ? 'margin-top: 30%' : ''">
            <div v-if="taxPerItem === 'NO' || taxPerItem === null">
              <tax
                v-for="(tax, index) in newEstimate.taxes"
                :index="index"
                :total="subtotalWithDiscount"
                :key="tax.id"
                :tax="tax"
                :taxes="newEstimate.taxes"
                :currency="currency"
                :total-tax="totalSimpleTax"
                @remove="removeEstimateTax"
                @update="updateTax"
              />
            </div>

            <sw-popup
              v-if="taxPerItem === 'NO' || taxPerItem === null"
              ref="taxModal"
              class="my-3 text-sm font-semibold leading-5 text-primary-400"
            >
              <div slot="activator" class="float-right pt-2 pb-4">
                + {{ $t('estimates.add_tax') }}
              </div>
              <tax-select-popup
                :taxes="newEstimate.taxes"
                @select="onSelectTax"
              />
            </sw-popup>
          </div>

          <div
            class="flex items-center justify-between w-full pt-2 mt-5 border-t border-gray-200 border-solid"
          >
            <label
              class="m-0 text-sm font-semibold leading-5 text-gray-500 uppercase"
              >{{ $t('estimates.total') }} {{ $t('estimates.amount') }}:</label
            >
            <label
              class="flex items-center justify-center text-lg uppercase text-primary-400"
            >
              <div v-html="$utils.formatMoney(total, currency)" />
            </label>
          </div>
        </div>
      </div>


       <!-- botones mama -->

      <sw-button
        variant="primary-outline"
        type="button"
        size="lg"
        class="mr-3 w-full mt-4 sm:hidden md:hidden"
        @click="cancelForm"
      >
        <x-circle-icon class="w-6 h-6 mr-1 -ml-2" v-if="!isLoading" />
        {{ $t('general.cancel') }}
      </sw-button>

      <sw-button
        v-if="$route.name === 'estimates.edit'"
        :href="`/estimates/pdf/${newEstimate.unique_hash}`"
        tag-name="a"
        target="_blank"
        class="mr-3 w-full mt-4 sm:hidden md:hidden"
        variant="primary-outline"
        type="button"
      >
        <span class="flex">
          {{ $t('general.view_pdf') }}
        </span>
      </sw-button>

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        type="submit"
        size="lg"
        class="mr-3 w-full mt-4 sm:hidden md:hidden"
      >
        <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
        {{ $t('estimates.save_estimate') }}
      </sw-button>
    </form>
    <base-loader v-else />
  </base-page>
</template>

<script>
import draggable from 'vuedraggable'
import EstimateItem from './Item'
import EstimateStub from '../../stub/estimate'
import CustomerSelect from './CustomerSelect'
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import Guid from 'guid'
import TaxStub from '../../stub/tax'
import Tax from './EstimateTax'
import { PlusSmIcon, PencilIcon } from '@vue-hero-icons/outline'
import CustomFieldsMixin from '../../mixins/customFields'
import {
  ChevronDownIcon,
  HashtagIcon,
  ShoppingCartIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'

const {
  required,
  between,
  maxLength,
  numeric,
} = require('vuelidate/lib/validators')

export default {
  components: {
    EstimateItem,
    CustomerSelect,
    Tax,
    draggable,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusSmIcon,
    HashtagIcon,
    XCircleIcon,
  },
  mixins: [CustomFieldsMixin],

  data() {
    return {
      activeDropdownDiscount: false,
      newEstimate: {
        estimate_date: null,
        expiry_date: null,
        estimate_number: null,
        user_id: null,
        estimate_template_id: 1,
        sub_total: null,
        total: null,
        tax: null,
        notes: null,
        discount_type: 'fixed',
        discount_val: 0,
        reference_number: null,
        discount: 0,
        assigne_user_id: null,
        users_groups: [],
        items: [
          {
            ...EstimateStub,
            id: Guid.raw(),
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          },
        ],
        taxes: [],
      },
      selectedCurrency: '',
      assignUser: null,
      assignUserGroup: [],
      taxPerItem: null,
      discountPerItem: null,
      isLoadingEstimate: false,
      isLoadingData: false,
      isLoading: false,
      maxDiscount: 0,
      estimateNumAttribute: null,
      estimatePrefix: null,
      EstimateFields: [
        'customer',
        'company',
        'customerCustom',
        'estimate',
        'estimateCustom',
      ],
      customerId: null,
    }
  },

  validations() {
    return {
      newEstimate: {
        estimate_date: {
          required,
        },
        expiry_date: {
          required,
        },
        discount_val: {
          between: between(0, this.subtotal),
        },
        reference_number: {
          maxLength: maxLength(255),
        },
      },
      selectedCustomer: {
        required,
      },
      estimateNumAttribute: {
        required,
        numeric,
      },
      assignUser: {
        required,
      },
    }
  },

  computed: {
    ...mapGetters('company', ['itemDiscount']),

    ...mapGetters('company', ['defaultCurrency']),

    ...mapGetters('estimate', [
      'getTemplateId',
      'selectedCustomer',
      'selectedNote',
      'usersOptions',
    ]),

    ...mapGetters('estimateTemplate', ['getEstimateTemplates']),
    ...mapGetters('company', {
      carbonFormat: 'getCarbonDateFormat',
      momentFormat: 'getMomentDateFormat',
    }),

    currency() {
      return this.selectedCurrency
    },

    pageTitle() {
      if (this.isEdit) {
        return this.$t('estimates.edit_estimate')
      }
      return this.$t('estimates.new_estimate')
    },

    isEdit() {
      if (this.$route.name === 'estimates.edit') {
        return true
      }
      return false
    },

    subtotalWithDiscount() {
      return this.subtotal - this.newEstimate.discount_val
    },

    total() {
      return this.subtotalWithDiscount + this.totalTax
    },

    subtotal() {
      return this.newEstimate.items.reduce(function (a, b) {
        return a + b['total']
      }, 0)
    },

    discount: {
      get: function () {
        return this.newEstimate.discount
      },
      set: function (newValue) {
        if (this.newEstimate.discount_type === 'percentage') {
          this.newEstimate.discount_val = (this.subtotal * newValue) / 100
        } else {
          this.newEstimate.discount_val = Math.round(newValue * 100)
        }

        this.newEstimate.discount = newValue
      },
    },

    totalSimpleTax() {
      return window._.sumBy(this.newEstimate.taxes, function (tax) {
        if (!tax.compound_tax) {
          return tax.amount
        }

        return 0
      })
    },

    totalCompoundTax() {
      return window._.sumBy(this.newEstimate.taxes, function (tax) {
        if (tax.compound_tax) {
          return tax.amount
        }

        return 0
      })
    },

    totalTax() {
      if (this.taxPerItem === 'NO' || this.taxPerItem === null) {
        return this.totalSimpleTax + this.totalCompoundTax
      }

      return window._.sumBy(this.newEstimate.items, function (tax) {
        return tax.tax
      })
    },

    allTaxes() {
      let taxes = []

      this.newEstimate.items.forEach((item) => {
        item.taxes.forEach((tax) => {
          let found = taxes.find((_tax) => {
            return _tax.tax_type_id === tax.tax_type_id
          })

          if (found) {
            found.amount += tax.amount
          } else if (tax.tax_type_id) {
            taxes.push({
              tax_type_id: tax.tax_type_id,
              amount: tax.amount,
              percent: tax.percent,
              name: tax.name,
            })
          }
        })
      })

      return taxes
    },

    estimateDateError() {
      if (!this.$v.newEstimate.estimate_date.$error) {
        return ''
      }

      if (!this.$v.newEstimate.estimate_date.required) {
        return this.$t('validation.required')
      }
    },

    estimateNumError() {
      if (!this.$v.estimateNumAttribute.$error) {
        return ''
      }

      if (!this.$v.estimateNumAttribute.required) {
        return this.$tc('estimates.errors.required')
      }

      if (!this.$v.estimateNumAttribute.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },

    expiryDateError() {
      if (!this.$v.newEstimate.expiry_date.$error) {
        return ''
      }

      if (!this.$v.newEstimate.expiry_date.required) {
        return this.$t('validation.required')
      }
    },
    referenceNumError() {
      if (!this.$v.newEstimate.reference_number.$error) {
        return ''
      }

      if (!this.$v.newEstimate.reference_number.maxLength) {
        return this.$tc('validation.ref_number_maxlength')
      }
    },
    assignUserError() {
      if (!this.$v.assignUser.$error) {
        return ''
      }

      if (!this.$v.assignUser.required) {
        return this.$t('validation.required')
      }
    },
  },

  watch: {
    selectedCustomer(newVal) {
      if (newVal && newVal.currency) {
        this.selectedCurrency = newVal.currency
      } else {
        this.selectedCurrency = this.defaultCurrency
      }
    },
    selectedNote() {
      if (this.selectedNote) {
        this.newEstimate.notes = this.selectedNote
      }
    },
    subtotal(newValue) {
      if (this.newEstimate.discount_type === 'percentage') {
        this.newEstimate.discount_val =
          (this.newEstimate.discount * newValue) / 100
      }
    },
  },

  async created() {
    await this.fetchInitialItems()
    await this.loadData()
    window.hub.$on('newTax', this.onSelectTax)
    // if (this.$route.query.customer) {
    //   console.log(576)
    //   console.log(this.$route.query.customer)
    //   this.customerId = parseInt(this.$route.query.customer)
    //   console.log(this.customerId)
    // }
    this.getUsersOptions()
  },
  mounted() {
    if (this.$route.query.customer) {
      this.customerId = parseInt(this.$route.query.customer)
    }
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('estimate', [
      'addEstimate',
      'fetchEstimate',
      'getEstimateNumber',
      'selectCustomer',
      'updateEstimate',
      'resetSelectedNote',
      'getUsersOptions',
    ]),

    ...mapActions('item', ['fetchItems']),

    ...mapActions('taxType', ['fetchTaxTypes']),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('estimateTemplate', ['fetchEstimateTemplates']),

    ...mapActions('customFields', ['fetchCustomFields']),

    dropdownSelect(val) {
      if (val == 'focusOut') {
        setTimeout(() => {
          this.activeDropdownDiscount = !this.activeDropdownDiscount
        }, 200)
      } else {
        this.activeDropdownDiscount = !this.activeDropdownDiscount
      }
    },

    setExpiryDate() {
      setTimeout(() => {
        this.configCalendarExpiryDate({
          minDate: new Date(this.newEstimate.estimate_date).fp_incr(1),
          // altFormat: 'Y/m/d',
          allowInput: true,
        })
      }, 1)
    },

    configCalendarExpiryDate({
      minDate,
      altFormat = this.carbonFormat,
      allowInput,
    }) {
      this.$refs.expiryDate.$refs.BaseDatepicker.$refs.BaseDatepicker.config.minDate =
        minDate
      this.$refs.expiryDate.$refs.BaseDatepicker.$refs.BaseDatepicker.config.altFormat =
        altFormat
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

    selectFixed() {
      if (this.newEstimate.discount_type === 'fixed') {
        return
      }

      this.newEstimate.discount_val = Math.round(
        this.newEstimate.discount * 100
      )
      this.newEstimate.discount_type = 'fixed'
    },

    selectPercentage() {
      if (this.newEstimate.discount_type === 'percentage') {
        return
      }

      this.newEstimate.discount_val =
        (this.subtotal * this.newEstimate.discount) / 100

      this.newEstimate.discount_type = 'percentage'
    },

    updateTax(data) {
      Object.assign(this.newEstimate.taxes[data.index], { ...data.item })
    },

    async fetchInitialItems() {
      if (!this.isEdit) {
        let response = await this.fetchCompanySettings([
          'discount_per_item',
          'tax_per_item',
        ])

        if (response.data) {
          this.discountPerItem = response.data.discount_per_item
          this.taxPerItem = response.data.tax_per_item
        }
      }

      Promise.all([
        this.fetchItems({
          filter: {},
          orderByField: '',
          orderBy: '',
          limit: 'all',
        }),
        this.resetSelectedNote(),
        this.fetchEstimateTemplates(),
        this.getEstimateNumber(),
        this.fetchCompanySettings(['estimate_auto_generate']),
      ])
        .then(async ([res1, res2, res3, res4, res5]) => {
          // if (res5.data) {
          //   this.discountPerItem = res5.data.discount_per_item
          //   this.taxPerItem = res5.data.tax_per_item
          // }

          if (
            !this.isEdit &&
            res5.data &&
            res5.data.estimate_auto_generate === 'YES'
          ) {
            if (res4.data) {
              this.estimateNumAttribute = res4.data.nextNumber
              this.estimatePrefix = res4.data.prefix
            }
          } else {
            this.estimatePrefix = res4.data.prefix
          }

          this.isLoadingData = true
        })
        .catch((error) => {})
    },

    async loadData() {
      if (this.isEdit) {
        Promise.all([
          this.fetchEstimate(this.$route.params.id),
          this.fetchCustomFields({
            type: 'Estimate',
            limit: 'all',
          }),
          this.fetchTaxTypes({ limit: 'all' }),
        ])
          .then(async ([res1, res2]) => {
            if (res1.data) {
              this.customerId = res1.data.estimate.user_id
              this.assignUser = res1.data.estimate.assing_user
              this.assignUserGroup = res1.data.estimate.assing_users_groups
              this.newEstimate = res1.data.estimate
              this.formData = { ...this.formData, ...res1.data.estimate }
              this.newEstimate.estimate_date = moment(
                res1.data.estimate.estimate_date
              ).format('YYYY-MM-DD')
              this.newEstimate.expiry_date = moment(
                res1.data.estimate.expiry_date
              ).format('YYYY-MM-DD')
              this.discountPerItem = res1.data.estimate.discount_per_item
              this.taxPerItem = res1.data.estimate.tax_per_item
              this.selectedCurrency = this.defaultCurrency
              this.estimateTemplates = res1.data.estimate.estimateTemplates
              this.estimateNumAttribute = res1.data.nextEstimateNumber
              this.estimatePrefix = res1.data.estimatePrefix
              let fields = res1.data.estimate.fields

              if (res2.data) {
                let customFields = res2.data.customFields.data
                this.setEditCustomFields(fields, customFields)
              }
            }

            this.isLoadingEstimate = true
            return
          })
          .catch((error) => {})
        return true
      }

      await this.setInitialCustomFields('Estimate')
      await this.fetchTaxTypes({ limit: 'all' })
      this.selectedCurrency = this.defaultCurrency
      this.newEstimate.estimate_date = moment().format('YYYY-MM-DD')
      this.newEstimate.expiry_date = moment()
        .add(7, 'days')
        .format('YYYY-MM-DD')
      this.isLoadingEstimate = true
      // aqui es ExpiryDate (min date)
      setTimeout(() => {
        this.configCalendarExpiryDate({
          minDate: new Date(this.newEstimate.estimate_date).fp_incr(1),
          // altFormat: 'Y/m/d',
          allowInput: true,
        })
      }, 300)
    },

    openTemplateModal() {
      this.openModal({
        title: this.$t('general.choose_template'),
        componentName: 'EstimateTemplate',
        data: this.getEstimateTemplates,
      })
    },

    addItem() {
      this.newEstimate.items.push({
        ...EstimateStub,
        id: Guid.raw(),
        taxes: [{ ...TaxStub, id: Guid.raw() }],
      })
    },

    removeItem(index) {
      this.newEstimate.items.splice(index, 1)
    },

    updateItem(data) {
      Object.assign(this.newEstimate.items[data.index], { ...data.item })
    },

    async submitForm() {
      let validate = await this.touchCustomField()
      if (!this.checkValid() || validate.error) {
        return false
      }

      this.isLoading = true
      this.newEstimate.estimate_number =
        this.estimatePrefix + '-' + this.estimateNumAttribute

      let data = {
        ...this.formData,
        ...this.newEstimate,
        estimate_date: moment(this.newEstimate.estimate_date).format(
          'YYYY-MM-DD'
        ),
        expiry_date: moment(this.newEstimate.expiry_date).format('YYYY-MM-DD'),
        sub_total: this.subtotal,
        total: this.total,
        tax: this.totalTax,
        user_id: null,
        estimate_template_id: this.getTemplateId,
      }

      if (this.selectedCustomer != null) {
        data.user_id = this.selectedCustomer.id
      }
      if (this.assignUser != null) {
        data.assigne_user_id = this.assignUser.id
      }
      if (this.assignUserGroup != null) {
        data.users_groups = this.assignUserGroup.map((user) => user.id)
      }

      let text = ''
      if (this.isEdit) {
        text = 'estimates.update_estimate'
      } else {
        text = 'estimates.create_estimate'
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t(text),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          if (this.$route.name === 'estimates.edit') {
            this.submitUpdate(data)
            return
          }
          this.submitCreate(data)
        } else {
          this.isLoading = false
        }
      })
    },

    submitCreate(data) {
      this.addEstimate(data)
        .then((res) => {
          if (res.data) {
            this.$router.push(`/admin/estimates/${res.data.estimate.id}/view`)
            window.toastr['success'](this.$t('estimates.created_message'))
          }

          this.isLoading = false
        })
        .catch((error) => {
          if (error.response.data.errors.hasOwnProperty('estimate_number')) {
            if (
              error.response.data.errors.estimate_number[0] ==
              'Invalid number passed.'
            ) {
              this.alertEstimateNumberAlreadyExists()
            }
          }
          this.isLoading = false
        })
    },

    // Estimate Number Exists
    alertEstimateNumberAlreadyExists() {
      this.$swal({
        title: this.$t('general.estimate_number_exists_title'),
        text: this.$t('general.estimate_number_exists_text'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: this.$t('general.automatic'),
        confirmButtonColor: '#5851D8',
        cancelButtonText: this.$t('general.manual'),
        //cancelButtonColor: "#efefef",
        //showCloseButton: true,
        showLoaderOnConfirm: true,
      }).then((result) => {
        if (result.value) {
          this.generateAutomaticEstimateNumber()
        }
      })
    },

    async generateAutomaticEstimateNumber() {
      let response_next_number = await axios.get(
        '/api/v1/next-number?key=estimate'
      )

      this.isLoading = true
      this.newEstimate.estimate_number =
        this.estimatePrefix + '-' + response_next_number.data.nextNumber

      let data = {
        ...this.formData,
        ...this.newEstimate,
        estimate_date: moment(this.newEstimate.estimate_date).format(
          'YYYY-MM-DD'
        ),
        expiry_date: moment(this.newEstimate.expiry_date).format('YYYY-MM-DD'),
        sub_total: this.subtotal,
        total: this.total,
        tax: this.totalTax,
        user_id: null,
        estimate_template_id: this.getTemplateId,
      }

      if (this.selectedCustomer != null) {
        data.user_id = this.selectedCustomer.id
      }
      if (this.assignUser != null) {
        data.assigne_user_id = this.assignUser.id
      }
      if (this.assignUserGroup != null) {
        data.users_groups = this.assignUserGroup.map((user) => user.id)
      }

      if (this.isEdit) {
        this.submitUpdate(data)
      } else {
        this.submitCreate(data)
      }
    },
    //

    submitUpdate(data) {
      this.updateEstimate(data)
        .then((res) => {
          this.isLoading = false
          if (res.data) {
            this.$router.push(`/admin/estimates/${res.data.estimate.id}/view`)
            window.toastr['success'](this.$t('estimates.updated_message'))
          }
        })
        .catch((error) => {
          if (error.response.data.errors.hasOwnProperty('estimate_number')) {
            if (
              error.response.data.errors.estimate_number[0] ==
              'Invalid number passed.'
            ) {
              this.alertEstimateNumberAlreadyExists()
            }
          }
          this.isLoading = false
        })
    },

    checkItemsData(index, isValid) {
      this.newEstimate.items[index].valid = isValid
    },

    onSelectTax(selectedTax) {
      let amount = 0

      if (selectedTax.compound_tax && this.subtotalWithDiscount) {
        amount = Math.round(
          ((this.subtotalWithDiscount + this.totalSimpleTax) *
            selectedTax.percent) /
            100
        )
      } else if (this.subtotalWithDiscount && selectedTax.percent) {
        amount = Math.round(
          (this.subtotalWithDiscount * selectedTax.percent) / 100
        )
      }

      this.newEstimate.taxes.push({
        ...TaxStub,
        id: Guid.raw(),
        name: selectedTax.name,
        percent: selectedTax.percent,
        compound_tax: selectedTax.compound_tax,
        tax_type_id: selectedTax.id,
        amount,
      })

      if (this.$refs) {
        this.$refs.taxModal.close()
      }
    },

    removeEstimateTax(index) {
      this.newEstimate.taxes.splice(index, 1)
    },

    checkValid() {
      this.$v.newEstimate.$touch()
      this.$v.selectedCustomer.$touch()
      this.$v.estimateNumAttribute.$touch()
      this.$v.assignUser.$touch()
      window.hub.$emit('checkItems')
      let isValid = true
      this.newEstimate.items.forEach((item) => {
        if (!item.valid) {
          isValid = false
        }
      })
      if (
        !this.$v.selectedCustomer.$invalid &&
        !this.$v.assignUser.$invalid &&
        !this.$v.estimateNumAttribute.$invalid &&
        this.$v.newEstimate.$invalid === false &&
        isValid === true
      ) {
        return true
      }
      return false
    },
    onSelectNote(data) {
      this.newEstimate.notes = '' + data.notes
      this.$refs.notePopup.close()
    },
  },
}
</script>
<style lang="scss">
.estimate-create-page {
  .estimate-foot {
    .estimate-total {
      min-width: 390px;
    }
  }

  @media (max-width: 480px) {
    .estimate-foot {
      .estimate-total {
        min-width: 384px !important;
      }
    }
  }
}
</style>
