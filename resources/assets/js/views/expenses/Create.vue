<style>
.table-responsive-item2 {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.tablemin {
  min-width: 900px;
}
/* Additional media query for finer control (optional) */
@media (max-width: 768px) {
  .table-responsive-item2 {
    /* Adjust table width as needed for smaller screens */
    width: 100%; /* Example adjustment */
  }
}
</style>

<template>
  <base-page class="relative">
    <form action="" @submit.prevent="sendData">
      <!-- Page Header -->
      <sw-page-header :title="pageTitle" class="mb-5">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />

          <sw-breadcrumb-item
            :title="$tc('expenses.expense', 2)"
            to="/admin/expenses"
          />

          <sw-breadcrumb-item
            v-if="$route.name === 'expenses.edit'"
            :title="$t('expenses.edit_expense')"
            to="#"
            active
          />

          <sw-breadcrumb-item
            v-else
            :title="$t('expenses.new_expense')"
            to="#"
            active
          />
        </sw-breadcrumb>

        <template slot="actions">
          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary-outline"
            class="mr-3 text-sm hidden sm:flex"
            size="lg"
            @click="cancelForm()"
          >
            <x-circle-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ $t('general.cancel') }}
          </sw-button>

          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            class="hidden sm:flex"
            type="submit"
            size="lg"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{
              isEdit
                ? $t('expenses.update_expense')
                : $t('expenses.save_expense')
            }}
          </sw-button>
        </template>
      </sw-page-header>

      <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />

      <sw-card v-else>
        <div class="grid gap-6 grid-col-1 md:grid-cols-2">
          <!-- section 1 -->
          <sw-input-group
            :label="$t('expenses.expense_number')"
            :error="expenseNumError"
            required
          >
            <sw-input
              :prefix="`${expensePrefix} - `"
              :invalid="$v.expenseNumAttribute.$error"
              v-model.trim="expenseNumAttribute"
              class="mt-1"
              :disabled="isEdit"
              @input="$v.expenseNumAttribute.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('general.subject')"
            :error="expenseSubjectError"
            required
          >
            <sw-input
              :invalid="$v.formData.subject.$error"
              v-model.trim="formData.subject"
              class="mt-1"
              @input="$v.formData.subject.$touch()"
            />
          </sw-input-group>

          <!-- section 2 -->
          <sw-input-group :label="$t('payments.status')">
            <sw-select
              v-model="formData.status"
              :options="statusOptions"
              :allow-empty="false"
              class="mt-1"
              label="label"
              track-by="value"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('expenses.category')"
            :error="categoryError"
            required
          >
            <sw-select
              ref="baseSelect"
              v-model="category"
              :options="categories"
              :invalid="$v.category.$error"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('expenses.categories.select_a_category')"
              class="mt-2"
              label="name"
              track-by="id"
              @input="$v.category.$touch()"
            >
              <sw-button
                slot="afterList"
                type="button"
                variant="gray-light"
                class="flex items-center justify-center w-full px-4 py-3 bg-gray-200 border-none outline-none"
                @click="openCategoryModal"
              >
                <shopping-cart-icon class="h-5 text-center text-primary-400" />
                <label class="ml-2 text-xs leading-none text-primary-400">{{
                  $t('settings.expense_category.add_new_category')
                }}</label>
              </sw-button>
            </sw-select>
          </sw-input-group>

          <sw-input-group
            :label="$t('expenses.expense_date')"
            :error="dateError"
            required
          >
            <base-date-picker
              v-model="formData.expense_date"
              :invalid="$v.formData.expense_date.$error"
              :calendar-button="true"
              class="mt-2"
              calendar-button-icon="calendar"
              @input="$v.formData.expense_date.$touch()"
            />
          </sw-input-group>

          <div class="flex mb-4 mt-8">
            <div class="relative w-12 ml-1">
              <sw-switch
                v-model="formData.notification"
                class="absolute"
                style="top: -18px"
              />
            </div>

            <div class="ml-2 mt-1">
              <p class="p-0 mb-1 text-base leading-snug text-black">
                {{ $t('expenses.notification') }}
              </p>
            </div>
          </div>
        </div>

        <sw-divider class="my-8" />

        <!-- section 3  -->
        <div class="grid gap-6 grid-col-1 md:grid-cols-2">
          <sw-input-group :label="$t('expenses.customer')">
            <span
              v-if="customer"
              class="absolute text-gray-400 cursor-pointer"
              style="top: 36px; right: 5px; z-index: 999999"
              @click="customer = null"
            >
              <x-circle-icon class="h-5" />
            </span>
            <sw-select
              ref="baseSelect"
              v-model="customer"
              :options="customerOptions"
              :searchable="true"
              :show-labels="false"
              :disabled="isCreate"
              :placeholder="$t('customers.select_a_customer')"
              @select="CustomerSelected"
              class="mt-1"
              label="name"
              track-by="id"
            />
          </sw-input-group>

          <!-- Aqui va el nuevo item -->
          <sw-input-group :label="$t('corePbx.items')">
            <span
              v-if="item"
              class="absolute text-gray-400 cursor-pointer"
              style="top: 36px; right: 5px; z-index: 999999"
              @click="item = null"
            >
              <x-circle-icon class="h-5" />
            </span>
            <sw-select
              ref="baseSelect"
              v-model="item"
              :options="itemOptions"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('expenses.item_select')"
              class="mt-1"
              label="name"
              track-by="id"
            />
          </sw-input-group>
        </div>

        <sw-divider class="my-8" />

        <div class="grid gap-6 grid-col-1 md:grid-cols-2">
          <sw-input-group :label="$t('expenses.provider')">
            <span
              v-if="provider"
              class="absolute text-gray-400 cursor-pointer"
              style="top: 36px; right: 5px; z-index: 999999"
              @click="provider = null"
            >
              <x-circle-icon class="h-5" />
            </span>
            <sw-select
              ref="baseSelect"
              v-model="provider"
              :options="providerOptions"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('expenses.provider_select')"
              class="mt-1"
              label="title"
              track-by="id"
              :invalid="isProviderRequired"
            />
          </sw-input-group>

          <sw-input-group
            v-if="allow_invoice_form_pos"
            :label="$t('core_pos.store')"
            class="absolute text-gray-400 cursor-pointer"
          >
            <sw-select
              v-model="store_selected"
              :options="stores"
              :searchable="true"
              :show-labels="true"
              :allow-empty="true"
              :multiple="false"
              class="mt-2"
              track-by="id"
              label="name"
              :tabindex="1"
            />
          </sw-input-group>
        </div>

        <sw-divider class="my-8" />

        <!-- Aqui el nuevo componente -->

        <div class="table-responsive-item2">
          <table class="w-full text-center item-table tablemin">
            <colgroup>
              <col style="width: 15%" />
              <col style="width: 12.5%" />
              <col style="width: 35%" />
              <col style="width: 12.5%" />
              <col style="width: 15%" />
              <col style="width: 10%" />
            </colgroup>
            <thead class="bg-white border border-gray-200 border-solid">
              <tr>
                <th
                  class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid text-center"
                >
                  {{ $t('invoices.invoice_number') }}
                </th>

                <th
                  class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid text-center"
                >
                  <span class="pr-0">
                    {{ $t('invoices.invoices_subtotal') }}
                  </span>
                </th>

                <th
                  class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid text-center"
                >
                  {{ $t('packages.taxes') }}
                </th>

                <th
                  class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid text-center"
                >
                  {{ $t('invoices.total_tax') }}
                </th>

                <th
                  class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid text-center"
                >
                  {{ $t('invoices.total') }}
                </th>

                <th
                  class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid text-center"
                >
                  {{ $t('general.delete') }}
                </th>
              </tr>
            </thead>
            <expense-invoice
              v-for="(invoice, index) in formData.invoices"
              :key="index"
              :index="index"
              :invoice-data="invoice"
              :currency="defaultCurrency"
              :taxes-options="taxes"
              :provider="provider"
              @update="updateInvoice"
              @remove="removeInvoice"
              @invoiceValidate="checkInvoiceData"
            />
          </table>
        </div>

        <div
          class="flex items-center justify-center w-full px-6 py-3 text-base border-r border-b border-l border-gray-200 border-solid cursor-pointer text-primary-400 hover:bg-gray-200"
          @click="addItem"
        >
          <plus-sm-icon class="h-5 mr-2" />
          {{ $t('invoices.add_invoice') }}
        </div>

        <sw-divider class="my-8" />

        <!-- section 4 -->
        <div class="grid gap-6 grid-col-1 md:grid-cols-2">
          <!-- <sw-input-group
            :label="$t('expenses.expense_date')"
            :error="dateError"
            required
          >
            <base-date-picker
              v-model="formData.expense_date"
              :invalid="$v.formData.expense_date.$error"
              :calendar-button="true"
              class="mt-2"
              calendar-button-icon="calendar"
              @input="$v.formData.expense_date.$touch()"
            />
          </sw-input-group> -->

          <sw-input-group
            :label="$t('expenses.amount')"
            :error="amountError"
            required
          >
            <sw-money
              v-model.trim="amount"
              :currency="defaultCurrencyForInput"
              :invalid="$v.formData.amount.$error"
              class="focus:border focus:border-solid focus:border-primary-500"
              @input="$v.formData.amount.$touch()"
            />
          </sw-input-group>

          <!-- section 5 -->
          <sw-input-group :label="$t('expenses.select_payment')">
            <span
              v-if="payment"
              class="absolute text-gray-400 cursor-pointer"
              style="top: 36px; right: 5px; z-index: 999999"
              @click=";(payment = null), (formData.payment_method = null)"
            >
              <x-circle-icon class="h-5" />
            </span>
            <sw-select
              ref="baseSelect"
              v-model="payment"
              :options="payments"
              :searchable="true"
              :show-labels="false"
              :disabled="isCreate"
              :placeholder="$t('expenses.select_payment_holder')"
              class="mt-1"
              label="paynumber"
              track-by="id"
              @select="PaymentSelected"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('expenses.expense_date_pay')"
            :error="paymentDateError"
            v-bind:required="formData.status.value == 'Active'"
            class="d-flex justify-content-center"
          >
            <base-date-picker
              v-model="formData.payment_date"
              :invalid="$v.formData.payment_date.$error"
              :calendar-button="true"
              class="mt-2"
              calendar-button-icon="calendar"
              @input="$v.formData.payment_date.$touch()"
            />
            <sw-button
              variant="primary"
              size="sm"
              @click="clearPaymentDate()"
              class="mt-2"
            >
              <trash-icon class="mr-2 -ml-1" />
              Clear Payment Date
            </sw-button>
          </sw-input-group>

          <sw-input-group :label="$t('payments.payment_mode')">
            <sw-select
              v-model="formData.payment_method"
              :options="paymentModes"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('payments.select_payment_mode')"
              :max-height="150"
              label="name"
              class="mt-1"
            >
            </sw-select>
          </sw-input-group>
        </div>
        <sw-divider class="my-8" />

        <div class="grid gap-6 grid-col-1 mt-4">
          <sw-input-group :label="$t('expenses.note')" :error="notesError">
            <sw-editor
              v-model="formData.notes"
              :set-editor="formData.notes"
              rows="2"
              name="notes"
              @input="$v.formData.notes.$touch()"
            />
          </sw-input-group>

          <sw-input-group :label="$t('expenses.receipt')">
            <div v-if="previewReceipt" class="w-full flex justify-end mb-3">
              <a
                v-if="isReceiptAvailable"
                :href="getReceiptUrl"
                tag-name="a"
                class="text-gray-500 hover:text-success mx-1 rounded-full border border-gray-400 hover:border-success cursor-pointer p-2"
              >
                <DownloadIcon class="h-5 w-5" />
              </a>
              <div
                @click="$refs.receiptUpload.click()"
                class="text-gray-500 hover:text-warning mx-1 rounded-full border border-gray-400 hover:border-warning cursor-pointer p-2"
              >
                <SwitchHorizontalIcon class="h-5 w-5" />
              </div>
              <div
                @click="removePdfReceipt()"
                class="text-gray-500 hover:text-danger mx-1 rounded-full border border-gray-400 hover:border-danger cursor-pointer p-2"
              >
                <XIcon class="h-5 w-5" />
              </div>
            </div>
            <div id="receipt-box">
              <div
                v-if="previewReceipt"
                class="relative flex items-center justify-center h-24 bg-transparent border-2 border-gray-200 border-dashed rounded-md image-upload-box hover:bg-gray-200"
                v-bind:class="{ previewHeight: previewReceipt !== null }"
              >
                <iframe
                  v-if="previewReceipt !== null"
                  :src="previewReceipt"
                  width="100%"
                  height="100%"
                >
                </iframe>
                <img
                  v-else
                  :src="previewReceipt"
                  class="absolute opacity-100 preview-logo"
                  style="max-height: 80%; animation: fadeIn 2s ease"
                />
              </div>
              <div
                @click="$refs.receiptUpload.click()"
                v-else
                class="flex flex-col items-center border-2 border-gray-200 border-dashed rounded-md py-2"
              >
                <cloud-upload-icon
                  class="h-5 mb-2 text-xl leading-6 text-gray-400"
                />
                <p class="text-xs leading-4 text-center text-gray-400">
                  Drag a file here or
                  <span id="pick-avatar" class="cursor-pointer text-primary-500"
                    >browse</span
                  >
                  to choose a file
                </p>
              </div>
            </div>

            <input
              ref="receiptUpload"
              class="form-control hidden"
              type="file"
              multiple
              @change="onChange"
            />
          </sw-input-group>
        </div>

        <sw-divider class="my-8" />

        <!-- LISTA DE DOCUMENTOS -->
        <div class="w-full mt-4">
          <sw-input-group :label="$t('expenses.docs')">
            <label for="uploadDocs">
              <div
                id="docs-box "
                class="hover:bg-gray-200 py-2 cursor-pointer md:w-1/3 mt-5 bg-transparent border-2 border-gray-400 border-dashed rounded-md"
              >
                <input
                  class="form-control hidden uploadDocs"
                  id="uploadDocs"
                  type="file"
                  multiple
                  @change="onChangeDocs"
                />
                <div class="flex flex-col items-center">
                  <cloud-upload-icon
                    class="h-5 mb-2 text-xl leading-6 text-gray-400"
                  />

                  <p class="text-xs leading-4 text-center text-gray-400">
                    <span id="pick-avatar" class="text-primary-500"
                      >browse</span
                    >
                    to choose a file
                  </p>
                </div>
              </div>
            </label>

            <div class="flex flex-wrap">
              <div
                class="px-1 py-2 w-full md:w-1/3"
                v-for="(file, indexTr) in formData.docs"
                :key="indexTr"
              >
                <div
                  class="bg-transparent border-2 border-gray-400 border-dashed rounded-md"
                >
                  <div class="p-3 flex flex-wrap">
                    <h6
                      class="mb-2 w-full truncate"
                      v-bind:class="{ downloadFilename: isEdit }"
                      @click="downloadFile(file)"
                    >
                      {{ file.file_name }}
                    </h6>
                    <p class="mb-2 w-full">
                      {{ $t('expenses.size') }}: {{ sizeFile(file.size) }}
                    </p>

                    <!-- actions buttons -->
                    <div class="flex w-full">
                      <span
                        v-bind:class="{ downloadFilename: isEdit }"
                        v-on:click="OpenSeeDocumentExpensesModal(file)"
                        class="pointer"
                      >
                        <eye-icon class="h-5 mr-2 text-gray-600" />
                      </span>
                      <span v-on:click="deleteFile(indexTr)" class="pointer">
                        <trash-icon class="h-5 mr-2 text-gray-600" />
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </sw-input-group>

          <sw-input-group>
            <!-- <ul class="list-group mt-10">
              <li class="list-group-item" v-for="file in formData.docs">
                <span v-on:click="deleteFile" class="pointer"><trash-icon class="h-5 mr-3 text-gray-600" style="position: absolute"/></span>
                <span style="margin-left: 31px;"> {{ file.name }} </span>
              </li>
            </ul> -->
          </sw-input-group>
        </div>

        <div v-if="customFields.length > 0">
          <div class="grid gap-6 mt-6 grid-col-1 md:grid-cols-2">
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
        </div>

        <sw-button
          :loading="isLoading"
          :disabled="isLoading"
          variant="primary-outline"
          class="mr-3 flex w-full mt-4 sm:hidden md:hidden"
          size="lg"
          @click="cancelForm()"
        >
          <x-circle-icon v-if="!isLoading" class="mr-2 -ml-1" />
          {{ $t('general.cancel') }}
        </sw-button>

        <sw-button
          :loading="isLoading"
          :disabled="isLoading"
          variant="primary"
          class="flex w-full mt-4 mb-2 mb-md-0 sm:hidden md:hidden"
          type="submit"
          size="lg"
        >
          <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
          {{
            isEdit ? $t('expenses.update_expense') : $t('expenses.save_expense')
          }}
        </sw-button>
      </sw-card>
    </form>
  </base-page>
</template>

<script>
import ExpenseInvoice from './ExpenseInvoice.vue'
import Guid from 'guid'
import expenseInvoiceStub from '../../stub/expenseInvoice'
import moment from 'moment'
import { mapActions, mapGetters } from 'vuex'
const {
  required,
  requiredIf,
  minValue,
  numeric,
  maxLength,
} = require('vuelidate/lib/validators')
import {
  DownloadIcon,
  SwitchHorizontalIcon,
  XIcon,
} from '@vue-hero-icons/outline'
import { PlusSmIcon } from '@vue-hero-icons/solid'
import {
  CloudUploadIcon,
  ShoppingCartIcon,
  TrashIcon,
  EyeIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
import CustomFieldsMixin from '../../mixins/customFields'

export default {
  components: {
    CloudUploadIcon,
    ShoppingCartIcon,
    DownloadIcon,
    TrashIcon,
    EyeIcon,
    SwitchHorizontalIcon,
    XIcon,
    XCircleIcon,
    ExpenseInvoice,
    PlusSmIcon,
  },
  mixins: [CustomFieldsMixin],

  props: {
    addname: {
      type: String,
      default: '',
    },
  },

  data() {
    return {
      isProviderRequired: false,
      formData: {
        subject: '',
        expense_category_id: null,
        expense_date: new Date(),
        amount: '',
        notes: '',
        user_id: null,
        payment_id: null,
        providers_id: null,
        items_id: null,
        payment_date: new Date(),
        payment_method_id: null,
        payment_method: {
          name: null,
        },
        docs: [],
        docsSend: [],
        status: { label: 'Processed', value: 'Active' },
        notification: false,
        //
        invoices: [
          {
            ...expenseInvoiceStub,
            id_raw: Guid.raw(),
          },
        ],
        expense_id: null,
      },
      taxes: [],
      money: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        precision: 2,
        masked: false,
      },
      isRequestOnGoing: false,
      isReceiptAvailable: false,
      isLoading: false,
      isCreate: false,
      category: null,
      previewReceipt: null,
      isPDF: false,
      fileSendUrl: '/api/v1/expenses',
      customer: null,
      payment: null,
      provider: null,
      item: null,
      fileObject: null,
      expenseNumAttribute: null,
      expensePrefix: '',
      statusOptions: [
        { label: 'Processed', value: 'Active' },
        { label: 'Pending', value: 'Pending' },
      ],
      allow_invoice_form_pos: false,
      stores: [],
      store_selected: {},
      customerOptions: [],

      itemOptions: [],

      providerOptions: [],
    }
  },

  validations: {
    category: {
      required,
    },
    formData: {
      expense_date: {
        required,
      },
      payment_date: {
        requiredIfStatus: requiredIf(function () {
          return this.formData.status.value == 'Active'
        }),
      },

      subject: {
        required,
      },

      amount: {
        required,
        numeric,
        minValue: minValue(0.1),
        maxLength: maxLength(20),
      },

      notes: {
        maxLength: maxLength(65000),
      },
    },
    expenseNumAttribute: {
      required,
      numeric,
    },
  },

  computed: {
    ...mapGetters('company', ['defaultCurrency', 'defaultCurrencyForInput']),
    ...mapGetters('payment', ['paymentModes', 'selectedNote']),

    amount: {
      get: function () {
        return this.formData.amount / 100
      },
      set: function (newValue) {
        this.formData.amount = Math.round(newValue * 100)
      },
    },

    invoices_amount() {
      return this.formData.invoices.reduce(function (a, b) {
        return a + b['total']
      }, 0)
    },

    pageTitle() {
      if (this.$route.name === 'expense.edit') {
        return this.$t('expenses.edit_expense')
      }
      return this.$t('expenses.new_expense')
    },

    isEdit() {
     // console.log(this.$route.name)
      if (this.$route.name === 'expense.edit') {
        return true
      }
      return false
    },

    ...mapGetters('category', ['categories']),

    ...mapGetters('company', ['getSelectedCompany']),

    ...mapGetters('providers', ['providers']),

    ...mapGetters('payment', ['payments']),

    getReceiptUrl() {
      if (this.isEdit) {
        return `/expenses/${this.$route.params.id}/receipt`
      }
    },

    categoryError() {
      if (!this.$v.category.$error) {
        return ''
      }
      if (!this.$v.category.required) {
        return this.$t('validation.required')
      }
    },

    dateError() {
      if (!this.$v.formData.expense_date.$error) {
        return ''
      }
      if (!this.$v.formData.expense_date.required) {
        return this.$t('validation.required')
      }
    },
    paymentDateError() {
      if (!this.$v.formData.payment_date.$error) {
        return ''
      }
      if (!this.$v.formData.payment_date.required) {
        return this.$t('validation.required')
      }
    },

    expenseSubjectError() {
      if (!this.$v.formData.subject.$error) {
        return ''
      }
      if (!this.$v.formData.subject.required) {
        return this.$t('validation.required')
      }
    },

    amountError() {
      if (!this.$v.formData.amount.$error) {
        return ''
      }
      if (!this.$v.formData.amount.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.amount.maxLength) {
        return this.$t('validation.price_maxlength')
      }
      if (!this.$v.formData.amount.minValue) {
        return this.$t('validation.price_minvalue')
      }
    },

    notesError() {
      if (!this.$v.formData.notes.$error) {
        return ''
      }
      if (!this.$v.formData.notes.maxLength) {
        return this.$t('validation.notes_maxlength')
      }
    },

    expenseNumError() {
      if (!this.$v.expenseNumAttribute.$error) {
        return ''
      }

      if (!this.$v.expenseNumAttribute.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.expenseNumAttribute.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
  },

  watch: {
    category(newValue) {
      this.formData.expense_category_id = newValue.id
    },
    provider: {
      handler: 'validateProvider',
      deep: true,
    },
  },

  async created() {
    this.loadTaxes()
  },

  async mounted() {
    this.$v.formData.$reset()
    this.loadData()
    window.hub.$on('newCategory', (val) => {
      this.category = val
    })
  },

  methods: {
    ...mapActions('expense', [
      'getExpenseReceipt',
      'getExpenseDocs',
      'addExpense',
      'updateExpense',
      'fetchExpense',
      'downloadExpenseDoc',
    ]),
    ...mapActions('modal', ['openModal']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('category', ['fetchCategories']),
    ...mapActions('customer', ['fetchCustomers', 'fetchOnlyCustomer']),
    ...mapActions('company', ['fetchCompanySettings']),
    ...mapActions('providers', ['fetchProviders', 'fetchOnlyProvider']),
    ...mapActions('item', ['fetchItems', 'fetchOnlyitem']),
    ...mapActions('taxType', ['fetchTaxTypes']),
    ...mapActions('payment', ['fetchPayments', 'fetchPaymentModes']),
    ...mapActions('corePos', ['fetchStores']),
    ...mapActions('modules', ['getModules']),

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

    //
    async loadTaxes() {
      let response = await this.fetchTaxTypes({ limit: 'all' })

      if (response.data.hasOwnProperty('taxTypes')) {
        if (response.data.taxTypes.data.length > 0) {
          this.taxes = response.data.taxTypes.data.map((tax) => {
            return {
              id: tax.id,
              name: `${tax.name} - ${tax.percent}%`,
              percent: tax.percent,
            }
          })
        }
      }
    },

    validateProvider() {
      if (this.provider != null) this.isProviderRequired = false
    },

    addItem() {
      this.formData.invoices.push({
        ...expenseInvoiceStub,
        id: Guid.raw(),
      })
    },

    updateInvoice(data) {
      Object.assign(this.formData.invoices[data.index], { ...data.invoice })
      this.formData.amount = this.invoices_amount
    },

    removeInvoice(index) {
      this.formData.invoices.splice(index, 1)
      this.formData.amount = this.invoices_amount
    },
    //

    openCategoryModal() {
      this.openModal({
        title: this.$t('settings.expense_category.add_category'),
        componentName: 'CategoryModal',
      })
    },
    sizeFile(size) {
      if (size < 1024) {
        return size + ' B'
      }
      if (size < 1048576) {
        return (size / 1024).toFixed(2) + ' KB'
      }
      if (size < 1073741824) {
        return (size / 1048576).toFixed(2) + ' MB'
      }
      return (size / 1073741824).toFixed(2) + ' GB'
    },
    OpenSeeDocumentExpensesModal(file) {
      // let media = this.formData.docs.find(file => file.id === file.id);
      this.openModal({
        title: this.$t('expenses.see_document_expenses'),
        componentName: 'SeeDocumentExpensesModal',
        props: {
          media: file,
        },
      })
    },
    onChange(e) {
      if (e == null) return
      if (e.target.files[0].type == 'application/pdf') {
        const reader = new FileReader()
        reader.readAsDataURL(e.target.files[0])
        reader.onload = (event) => {
          this.previewReceipt = event.target.result
        }

        this.fileObject = e.target.files[0]
        this.isReceiptAvailable = false
      } else {
        window.toastr['error'](this.$t('expenses.error_not_pdf'))
      }
    },

    onChangeDocs(data) {
      if (data == null) return
      data.target.files.forEach((file) => {
        const fileReader = new FileReader()
        fileReader.readAsDataURL(file)
        fileReader.onload = (event) => {
          this.formData.docs.push({
            file_name: file.name,
            size: file.size,
            type: file.type,
            base64: event.target.result,
            typeFile: file.name.split('.').pop(),
          })
          this.formData.docsSend.push(file)
        }
      })
    },

    deleteFile(index) {
      this.formData.docs.splice(index, 1)
      this.formData.docsSend.splice(index, 1)
    },

    async downloadFile(file) {
      if (!this.isEdit) {
        window.toastr['error'](this.$t('expenses.error_download_on_create'))
        return true
      }

      let res = await this.downloadExpenseDoc(file)
      const url = window.URL.createObjectURL(new Blob([res.data]))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', file.file_name)
      document.body.appendChild(link)
      link.click()
      //link.remove();
    },

    // async getReceipt() {
    //   let res = await this.getExpenseReceipt(this.$route.params.id)
    //   if (res.data.error) {
    //     this.isReceiptAvailable = false
    //     return true
    //   }
    //   this.isReceiptAvailable = true
    //   this.previewReceipt = res.data.image
    //   this.isPDF = true;
    // },

    async getDocs() {
      let res = await this.getExpenseDocs(this.$route.params.id)

      res.data.files.forEach((file) => {
        var oFile = this.dataURLtoFile(file.base64, file.file_name)
        oFile.id = file.id
        oFile.model_id = file.model_id
        oFile.base64 = file.base64
        oFile.file_name = file.file_name
        oFile.typeFile = file.file_name.split('.').pop()
        this.formData.docs.push(oFile)
        this.formData.docsSend.push(oFile)
      })
    },

    dataURLtoFile(dataurl, filename) {
      var arr = dataurl.split(','),
        mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]),
        n = bstr.length,
        u8arr = new Uint8Array(n)

      while (n--) {
        u8arr[n] = bstr.charCodeAt(n)
      }

      return new File([u8arr], filename, { type: mime })
    },

    setExpenseCustomer(id) {
      this.customer = this.customers.find((c) => {
        return c.id == id
      })
    },

    setExpenseProvider(id) {
      this.provider = this.providers.find((c) => {
        return c.id == id
      })
    },

    setExpenseItem(id) {
      this.item = this.items.find((c) => {
        return c.id == id
      })
    },

    async checkAutoGenerate() {
      let response = await this.fetchCompanySettings(['expense_auto_generate'])

      let response1 = await axios.get('/api/v1/next-number?key=expense')

      if (response.data && response.data.expense_auto_generate === 'YES') {
        if (response1.data) {
          this.expenseNumAttribute = response1.data.nextNumber
          this.expensePrefix = response1.data.prefix
          return true
        }
      } else {
        this.expensePrefix = response1.data.prefix
      }
    },

    async loadData() {
      this.isRequestOnGoing = true
      // Define el módulo a buscar
      const modules = ['corePOS']
      // Obtiene la lista de módulos
      const modulesArray = await this.getModules(modules)

      // Encuentra el módulo 'corePOS' si existe en la lista de módulos
      const moduleCorePos = modulesArray.modules?.find(
        (element) => element.name === 'corePOS'
      )

      // Inicializa la variable para permitir el formulario de factura desde POS
      this.allow_invoice_form_pos = false

      // Si el módulo 'corePOS' está activo, procede a obtener la configuración de la compañía
      if (moduleCorePos?.status === 'A') {
        // Obtiene la configuración de la compañía
        const res = await this.fetchCompanySettings(['allow_invoice_form_pos'])
        // Asigna el valor booleano directamente
        this.allow_invoice_form_pos = res.data.allow_invoice_form_pos !== '0'

        // Define los datos para obtener las tiendas
        const dataStore = {
          limit: 'all',
        }
        // Obtiene las tiendas
        const responseStore = await this.fetchStores(dataStore)
        // Asigna las tiendas a la variable correspondiente
        this.stores = responseStore.data.stores.data
      }
      // end - is module corepos allowed

      const data = {
        module: 'expenses',
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

      let responsecustomer = await this.fetchOnlyCustomer({ limit: 'all' })

      // Comprueba si las propiedades existen antes de asignar los datos a customerOptions
      if (
        responsecustomer &&
        responsecustomer.data &&
        responsecustomer.data.customers &&
        responsecustomer.data.customers.data
      ) {
        this.customerOptions = responsecustomer.data.customers.data
      }

      let responseitem = await this.fetchOnlyitem({ limit: 'all' })

      if (
        responseitem &&
        responseitem.data &&
        responseitem.data.items &&
        responseitem.data.items.data
      ) {
        this.itemOptions = responseitem.data.items.data
      }
      //itemOptions

      let responseProviders = await this.fetchOnlyProvider({ limit: 'all' })

      if (
        responseProviders &&
        responseProviders.data &&
        responseProviders.data.providers &&
        responseProviders.data.providers.data
      ) {
        this.providerOptions = responseProviders.data.providers.data
      }

      await this.fetchCategories({ limit: 'all' })
      await this.fetchPaymentModes({ limit: 'all' })

      if (this.isEdit) {
        this.isRequestOnGoing = true
        let response = await this.fetchExpense(this.$route.params.id)

        if (response.data.expense.receiptPdfUrl !== null) {
          this.isReceiptAvailable = true
          this.previewReceipt = response.data.expense.receiptPdfUrl
        }

        this.formData = { ...this.formData, ...response.data.expense }
        this.formData.docs = []
        this.formData.docsSend = []

        this.formData.expense_date = moment(this.formData.expense_date).format(
          'YYYY-MM-DD'
        )
        this.store_selected = this.stores.find(
          (item) => item.id == response.data.expense.store_id
        )

        this.formData.status = this.statusOptions.filter(
          (element) => element.value == this.formData.status
        )[0]
        this.expensePrefix = response.data.expense_prefix
        this.expenseNumAttribute = response.data.nextExpenseNumber
        this.formData.amount = response.data.expense.amount
        this.fileSendUrl = `/api/v1/expenses/${this.$route.params.id}`

        if (response.data.expense.expense_category_id) {
          this.category = this.categories.find(
            (category) =>
              category.id === response.data.expense.expense_category_id
          )
        }

        if (response.data.expense.user_id) {
          /*this.customer = this.customers.find(
            (customer) => customer.id === response.data.expense.user_id
          )*/

          this.customer = this.customerOptions.find(
            (customer) => customer.id === response.data.expense.user_id
          )
        }

        if (response.data.expense.providers_id) {
          this.provider = this.providerOptions.find(
            (provider) => provider.id === response.data.expense.providers_id
          )
        }

        if (response.data.expense.items_id) {
          this.item = this.itemOptions.find(
            (item) => item.id === response.data.expense.items_id
          )
        }
        if (response.data.expense.payment_method_id) {
          this.formData.payment_method = this.paymentModes.find(
            (paymentMode) =>
              paymentMode.id === response.data.expense.payment_method_id
          )
        }

        if (response.data.expense.payment_id && response.data.expense.user_id) {
          await this.fetchPayments({
            limit: 'all',
            customer_id: response.data.expense.user_id,
          })

          this.isCreate = true

          this.payment = this.payments.find(
            (payment) => payment.id === response.data.expense.payment_id
          )
        }
        if (this.payment) {
          if (!this.payment.payment_date) {
            this.formData.payment_date = new Date()
          } else {
            this.formData.payment_date = moment(
              this.payment.payment_date
            ).format('YYYY-MM-DD')
          }
        }

        let res = await this.fetchCustomFields({
          type: 'Expense',
          limit: 'all',
        })

        this.setEditCustomFields(
          response.data.expense.fields,
          res.data.customFields.data
        )

        // this.getReceipt()
        this.getDocs()
        this.isRequestOnGoing = false
        return true
      }
      this.checkAutoGenerate()
      await this.setInitialCustomFields('Expense')
      if (this.$route.query.customer) {
        this.setExpenseCustomer(parseInt(this.$route.query.customer))
      }

      if (this.$route.query.provider) {
        this.provider = this.providers.find(
          (provider) => provider.id === parseInt(this.$route.query.provider)
        )
      }

      this.isRequestOnGoing = false
    },
    async CustomerSelected(val) {
      await this.fetchPayments({
        limit: 'all',
        expenses: 'yes',
        customer_id: val.id,
      })
    },

    async PaymentSelected(val) {
     // console.log(val)
      if (val.payment_date) {
        this.formData.payment_date = null
        /*this.formData.payment_date =  new Date(val.payment_date);

        this.formData.payment_date = this.formData.payment_date.toString();*/

        this.formData.payment_date = moment(val.payment_date).format(
          'YYYY-MM-DD'
        )
      }

      if (val.payment_method) {
        this.formData.payment_method = val.payment_method
      }

      if (val.amount) {
        this.formData.amount = val.amount
      }
    },

    removePdfReceipt() {
      this.previewReceipt = null
      this.fileObject = null
    },
    async sendData() {
      // validations
      if (!this.checkInvoices()) {
        return
      }

      // this.$v.category.$touch();
      this.$v.formData.$touch()
      this.$v.category.$touch()

      if (this.$v.$invalid) {
        return false
      }

      let text = ''
      if (this.isEdit) {
        text = 'expenses.edit_expense_text'
      } else {
        text = 'expenses.create_expense_text'
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc(text),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let validate = await this.touchCustomField()

          this.formData.expense_number =
            this.expensePrefix + '-' + this.expenseNumAttribute

          let data = new FormData()

          if (this.fileObject) {
            data.append('attachment_receipt', this.fileObject)
          }
          if (this.previewReceipt == null) {
            data.append('attachment_receipt', 'none')
          }
          // if (this.formData.docs) {
          //   this.formData.docs.forEach(file => {
          //     data.append('attachment_docs[]', file)
          //   });
          // }
          if (this.formData.docsSend) {
            this.formData.docsSend.forEach((file) => {
              data.append('attachment_docs[]', file)
            })
          }
          data.append('expense_category_id', this.formData.expense_category_id)
          data.append(
            'expense_date',
            moment(this.formData.expense_date).format('YYYY-MM-DD')
          )
          data.append('expense_number', this.formData.expense_number)
          data.append('amount', this.formData.amount)
          data.append('notes', this.formData.notes ? this.formData.notes : '')
          data.append('user_id', this.customer ? this.customer.id : '')
          data.append(
            'customFields',
            JSON.stringify(this.formData.customFields)
          )
          //
          data.append('providers_id', this.provider ? this.provider.id : '')
          data.append('invoices', JSON.stringify(this.formData.invoices))
          //

          data.append('items_id', this.item ? this.item.id : '')
          data.append('payment_id', this.payment ? this.payment.id : '')
          data.append(
            'status',
            this.formData.status ? this.formData.status.value : ''
          )
          data.append(
            'subject',
            this.formData.subject ? this.formData.subject : ''
          )
          data.append('notification', this.formData.notification ? 1 : 0)

          data.append(
            'payment_date',
            this.formData.payment_date
              ? moment(this.formData.payment_date).format('YYYY-MM-DD')
              : ''
          )

          data.append(
            'payment_method_id',
            this.formData.payment_method ? this.formData.payment_method.id : ''
          )

          data.append(
            'store_id',
            this.store_selected ? this.store_selected.id : ''
          )

          if (this.isEdit) {
            this.isLoading = true
            data.append('_method', 'PUT')
            let response = await this.updateExpense({
              id: this.$route.params.id,
              editData: data,
            })

            if (response.data.success) {
              window.toastr['success'](this.$t('expenses.updated_message'))
              this.$router.push('/admin/expenses')
              return true
            }
            this.isLoading = false
            window.toastr['error'](response.data.error)
          } else {
            this.isLoading = true
            let response = await this.addExpense(data)
            this.isLoading = false

            if (response.data.success) {
              window.toastr['success'](this.$t('expenses.created_message'))
              this.$router.push('/admin/expenses')
              return true
            }
            window.toastr['success'](response.data.success)
          }
        }
      })
    },

    checkInvoices() {
      if (
        this.formData.invoices.length === 1 &&
        this.formData.invoices[0].invoice_number == '' &&
        this.formData.invoices[0].subtotal == 0
      ) {
        this.formData.invoices = []
        return true
      }

      if (
        this.formData.invoices.length === 1 &&
        this.formData.invoices[0].invoice_number == '' &&
        this.formData.invoices[0].subtotal != 0 &&
        this.provider != null
      ) {
        window.toastr['error'](`Invoice number and subtotal are required`)
        return false
      } else if (
        this.formData.invoices.length === 1 &&
        this.formData.invoices[0].invoice_number != '' &&
        this.formData.invoices[0].subtotal == 0 &&
        this.provider != null
      ) {
        window.toastr['error'](`Invoice number and subtotal are required`)
        return false
      }

      if (
        this.formData.invoices.length >= 1 &&
        (this.formData.invoices[0].invoice_number != '' ||
          this.formData.invoices[0].subtotal != 0) &&
        this.provider == null
      ) {
        this.isProviderRequired = true
        window.toastr['error'](`Provider is required`)
        return false
      }

      return true
    },

    checkInvoiceData(index, isValid) {
      this.formData.invoices[index].valid = isValid
    },

    irRuta() {
      this.$router.push(this.getReceiptUrl)
    },

    clearPaymentDate() {
      this.formData.payment_date = ''
    },
  },
}
</script>

<style scoped>
.previewHeight {
  height: 600px;
}
.pointer {
  cursor: pointer;
}

.marginFilename {
  margin-left: 31px;
}

.downloadFilename {
  cursor: pointer;
  color: rgba(88, 81, 216, var(1));
}
</style>
