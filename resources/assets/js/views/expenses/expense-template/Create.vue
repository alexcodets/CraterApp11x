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
            to="/admin/expenses-template"
          />

          <sw-breadcrumb-item
            v-if="$route.name === 'expenses-template.edit'"
            :title="$t('expenses.edit_expense_template')"
            to="#"
            active
          />

          <sw-breadcrumb-item
            v-else
            :title="$t('expenses.new_expense_template')"
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
            class="hidden sm:flex"
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            type="submit"
            size="lg"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{
              isEdit
                ? $t('expenses.update_expense_template')
                : $t('expenses.save_expense_template')
            }}
          </sw-button>
        </template>
      </sw-page-header>

      <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />

      <sw-card v-else>
        <div class="grid gap-6 grid-col-1 md:grid-cols-2">
          <sw-input-group
            :label="$t('expenses.name')"
            :error="expenseNameError"
            required
          >
            <sw-input
              :invalid="$v.formData.name.$error"
              v-model.trim="formData.name"
              class="mt-1"
              @input="$v.formData.name.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('expenses.expense_template_number')"
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

          <sw-input-group :label="$t('general.status')">
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
            :label="$t('expenses.expense_template_date_begin')"
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

          <sw-input-group :label="$t('expenses.term')">
            <sw-select
              v-model="formData.term"
              :options="termOptions"
              :allow-empty="false"
              class="mt-1"
              label="label"
              track-by="value"
            />
          </sw-input-group>

          <br />
          <hr />
          <hr />

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

          <sw-input-group :label="$t('expenses.initial_status')">
            <sw-select
              v-model="formData.initial_status"
              :options="initialStatusOptions"
              :allow-empty="false"
              class="mt-1"
              label="label"
              track-by="value"
              @select="onChangeInitialStatus($event)"
            />
          </sw-input-group>

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
              :options="providers"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('expenses.provider_select')"
              class="mt-1"
              label="title"
              track-by="id"
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
            :label="$t('expenses.amount')"
            :error="amountError"
            required
          >
            <sw-money
              v-model="amount"
              :currency="defaultCurrencyForInput"
              :invalid="$v.formData.amount.$error"
              class="focus:border focus:border-solid focus:border-primary-500"
              @input="$v.formData.amount.$touch()"
            />
          </sw-input-group>

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
              :options="customers"
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
          <sw-input-group :label="$t('expenses.item')">
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
              :options="items"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('expenses.item_select')"
              class="mt-1"
              label="name"
              track-by="id"
            />
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

          <sw-input-group
            v-if="isStatusPending"
            :label="$t('expenses.day_after_payment_date')"
          >
            <sw-input
              v-model.trim="formData.day_after_payment_date"
              class="mt-1"
            />
          </sw-input-group>
        </div>

        <div class="grid gap-6 grid-col-1 mt-4">
          <sw-input-group
            :label="$t('expenses.note')"
            :error="descriptionError"
          >
            <sw-editor
              v-model="formData.description"
              :set-editor="formData.description"
              rows="2"
              name="description"
              @input="$v.formData.description.$touch()"
            />
          </sw-input-group>

          <!-- 
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
                <cloud-upload-icon class="h-5 mb-2 text-xl leading-6 text-gray-400" />
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
          </sw-input-group> -->
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
          class="flex w-full mt-4 mb-2 mb-md-0 sm:hidden md:hidden"
          :loading="isLoading"
          :disabled="isLoading"
          variant="primary"
          type="submit"
          size="lg"
        >
          <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
          {{
            isEdit
              ? $t('expenses.update_expense_template')
              : $t('expenses.save_expense_template')
          }}
        </sw-button>
      </sw-card>
    </form>
  </base-page>
</template>

<script>
import moment from 'moment'
import { mapActions, mapGetters } from 'vuex'
const {
  required,
  minValue,
  numeric,
  maxLength,
} = require('vuelidate/lib/validators')
import {
  DownloadIcon,
  SwitchHorizontalIcon,
  XIcon,
} from '@vue-hero-icons/outline'
import {
  CloudUploadIcon,
  ShoppingCartIcon,
  TrashIcon,
  EyeIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
import CustomFieldsMixin from '../../../mixins/customFields'

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
      isStatusPending: false,
      formData: {
        name: '',
        subject: '',
        expense_category_id: null,
        expense_date: new Date(),
        amount: 0,
        description: '',
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
        status: { label: 'Active', value: 'Active' },
        initial_status: { label: 'Processed', value: 'Active' },
        term: {},
        day_after_payment_date: null,
        notification: false,
      },

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
      initialStatusOptions: [
        { label: 'Processed', value: 'Active' },
        { label: 'Pending', value: 'Pending' },
      ],
      statusOptions: [
        { label: 'Active', value: 'Active' },
        { label: 'Inactive', value: 'Inactive' },
      ],
      termOptions: [
        { label: 'daily', value: 'daily' },
        { label: 'weekly', value: 'weekly' },
        { label: 'monthly', value: 'monthly' },
        { label: 'bimonthly', value: 'bimonthly' },
        { label: 'quarterly', value: 'quarterly' },
        { label: 'biannual', value: 'biannual' },
        { label: 'yearly', value: 'yearly' },
      ],
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

      name: {
        required,
      },
      subject: {
        required,
      },

      amount: {
        required,
        minValue: minValue(0.1),
        maxLength: maxLength(20),
      },

      description: {
        maxLength: maxLength(65000),
      },
    },
    expenseNumAttribute: {
      required,
      numeric,
    },
  },

  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),
    ...mapGetters('payment', ['paymentModes', 'selectedNote']),

    amount: {
      get: function () {
        return this.formData.amount / 100
      },
      set: function (newValue) {
        this.formData.amount = Math.round(newValue * 100)
      },
    },

    pageTitle() {
      if (this.$route.name === 'expenses.edit') {
        return this.$t('expenses.edit_expense_template')
      }
      return this.$t('expenses.new_expense_template')
    },

    isEdit() {
      if (this.$route.name === 'expenses.edit') {
        return true
      }
      return false
    },

    ...mapGetters('category', ['categories']),

    ...mapGetters('customer', ['customers']),

    ...mapGetters('company', ['getSelectedCompany']),

    ...mapGetters('providers', ['providers']),

    ...mapGetters('item', ['items']),
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

    expenseSubjectError() {
      if (!this.$v.formData.subject.$error) {
        return ''
      }
      if (!this.$v.formData.subject.required) {
        return this.$t('validation.required')
      }
    },
    expenseNameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
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

    descriptionError() {
      if (!this.$v.formData.description.$error) {
        return ''
      }
      if (!this.$v.formData.description.maxLength) {
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
  },

  mounted() {
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
      'addExpenseTemplate',
      'updateExpense',
      'updateExpenseTemplate',
      'fetchExpense',
      'fetchExpenseTemplate',
      'downloadExpenseDoc',
    ]),

    ...mapActions('modal', ['openModal']),

    ...mapActions('user', ['getUserModules']),

    ...mapActions('category', ['fetchCategories']),

    ...mapActions('customer', ['fetchCustomers']),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('providers', ['fetchProviders']),

    ...mapActions('item', ['fetchItems']),

    ...mapActions('payment', ['fetchPayments', 'fetchPaymentModes']),

    openCategoryModal() {
      this.openModal({
        title: this.$t('settings.expense_category.add_category'),
        componentName: 'CategoryModal',
      })
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

    onChangeInitialStatus(e) {
      if (e.value == 'Pending') {
        this.isStatusPending = true
      } else {
        this.isStatusPending = false
      }
    },

    onChangeDocs(data) {
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

    async downloadFile(cFileName) {
      if (!this.isEdit) {
        window.toastr['error'](this.$t('expenses.error_download_on_create'))
        return true
      }
      let media = this.formData.docs.filter(
        (file) => file.file_name === cFileName
      )
      let res = await this.downloadExpenseDoc(media)
      const url = window.URL.createObjectURL(new Blob([res.data]))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', media[0].file_name)
      document.body.appendChild(link)
      link.click()
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
        oFile.file_name = file.name
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
      let response = await this.fetchCompanySettings([
        'expense_template_auto_generate',
      ])

      let response1 = await axios.get(
        '/api/v1/next-number?key=expense_template'
      )

      if (
        response.data &&
        response.data.expense_template_auto_generate === 'YES'
      ) {
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
      // validar el access
      // verificar si edicion = update
      // verificar si edicion false =  create

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

      this.isRequestOnGoing = true
      await this.fetchCategories({ limit: 'all' })
      await this.fetchCustomers({ limit: 'all' })
      await this.fetchProviders({ limit: 'all' })
      await this.fetchItems({ limit: 'all' })
      await this.fetchPaymentModes({ limit: 'all' })

      if (this.isEdit) {
        this.isRequestOnGoing = true
        let response = await this.fetchExpenseTemplate(this.$route.params.id)
        await this.fetchPayments({ limit: 100, expenses: 'no' })

        // if (response.data.expense.receiptPdfUrl !== null) {
        //   this.isReceiptAvailable = true;
        //   this.previewReceipt = response.data.expense.receiptPdfUrl;
        // }
        this.formData = { ...this.formData, ...response.data.expense_template }
        this.formData.docs = []
        this.formData.docsSend = []

        this.formData.expense_date = moment(this.formData.expense_date).format(
          'YYYY-MM-DD'
        )

        this.formData.status = this.statusOptions.filter(
          (element) => element.value == this.formData.status
        )[0]

        this.formData.initial_status = this.initialStatusOptions.filter(
          (element) => element.value == this.formData.initial_status
        )[0]

        this.formData.term = this.termOptions.filter(
          (element) => element.value == this.formData.term
        )[0]

        if (this.formData.initial_status.value == 'Pending') {
          this.isStatusPending = true
        } else {
          this.isStatusPending = false
        }

        this.expensePrefix = response.data.expense_prefix
        this.description = response.data.description
        this.expenseNumAttribute = response.data.nextExpenseNumber
        this.formData.amount = response.data.expense_template.amount
        this.formData.notification = response.data.expense_template.notification
        this.formData.day_after_payment_date =
          response.data.expense_template.days_after_payment_date
        // this.fileSendUrl = `/api/v1/expenses/${this.$route.params.id}`;

        if (response.data.expense_template.expense_category_id) {
          this.category = this.categories.find(
            (category) =>
              category.id === response.data.expense_template.expense_category_id
          )
        }

        if (response.data.expense_template.customer_id) {
          this.customer = this.customers.find(
            (customer) =>
              customer.id === response.data.expense_template.customer_id
          )
        }

        if (response.data.expense_template.providers_id) {
          this.provider = this.providers.find(
            (provider) =>
              provider.id === response.data.expense_template.providers_id
          )
        }

        if (response.data.expense_template.items_id) {
          this.item = this.items.find(
            (item) => item.id === response.data.expense_template.items_id
          )
        }

        if (response.data.expense_template.payment_method_id) {
          this.formData.payment_method = this.paymentModes.find(
            (paymentMode) =>
              paymentMode.id ===
              response.data.expense_template.payment_method_id
          )
        }

        if (
          response.data.expense_template.payment_id &&
          response.data.expense_template.user_id
        ) {
          await this.fetchPayments({
            limit: 'all',
            expenses: 'yes',
            customer_id: response.data.expense_template.user_id,
          })
          this.isCreate = true
          this.payment = this.payments.find(
            (payment) =>
              payment.id === response.data.expense_template.payment_id
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

        // let res = await this.fetchCustomFields({
        //   type: "Expense",
        //   limit: "all",
        // });

        // this.setEditCustomFields(
        //   response.data.expense_template.fields,
        //   res.data.customFields.data
        // );

        // this.getReceipt()
        // this.getDocs();
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
    },

    removePdfReceipt() {
      this.previewReceipt = null
      this.fileObject = null
    },
    async sendData() {
      if (!this.isEdit) {
        if (
          this.formData.expense_date < new Date().toISOString().split('T')[0]
        ) {
          window.toastr['error'](this.$t('validation.date_before_current'))
          return true
        }
      }
      let validate = await this.touchCustomField()
      this.$v.category.$touch()
      this.$v.formData.$touch()
      if (this.$v.$invalid || validate.error) {
        return true
      }

      this.formData.expense_number =
        this.expensePrefix + '-' + this.expenseNumAttribute

      ///pregunta

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
          /// guardado iniciuo
          let data = new FormData()

          // if (this.fileObject) {
          //   data.append("attachment_receipt", this.fileObject);
          // }
          // if (this.previewReceipt == null) {
          //   data.append("attachment_receipt", "none");
          // }
          // if (this.formData.docs) {
          //   this.formData.docs.forEach(file => {
          //     data.append('attachment_docs[]', file)
          // //   });
          // // }
          // if (this.formData.docsSend) {
          //   this.formData.docsSend.forEach((file) => {
          //     data.append("attachment_docs[]", file);
          //   });
          // }
          data.append('expense_category_id', this.formData.expense_category_id)
          data.append(
            'expense_date',
            moment(this.formData.expense_date).format('YYYY-MM-DD')
          )
          data.append('template_expense_number', this.formData.expense_number)
          data.append('amount', this.formData.amount)
          data.append('name', this.formData.name)
          data.append(
            'description',
            this.formData.description ? this.formData.description : ''
          )
          data.append('customer_id', this.customer ? this.customer.id : '')
          data.append(
            'company_id',
            this.$store.state.company.selectedCompany.id
          )
          // data.append("customFields", JSON.stringify(this.formData.customFields));
          data.append('providers_id', this.provider ? this.provider.id : '')
          data.append('items_id', this.item ? this.item.id : '')
          // data.append("payment_method_id", this.payment ? this.payment.id : "");
          data.append(
            'status',
            this.formData.status ? this.formData.status.value : ''
          )
          data.append(
            'subject',
            this.formData.subject ? this.formData.subject : ''
          )
          data.append(
            'initial_status',
            this.formData.initial_status
              ? this.formData.initial_status.value
              : ''
          )
          data.append(
            'term',
            this.formData.term ? this.formData.term.value : ''
          )
          data.append('notification', this.formData.notification)
          data.append(
            'days_after_payment_date',
            this.formData.day_after_payment_date
              ? this.formData.day_after_payment_date
              : ''
          )

          data.append(
            '',
            this.formData.payment_date
              ? moment(this.formData.payment_date).format('YYYY-MM-DD')
              : ''
          )

          data.append(
            'payment_method_id',
            this.formData.payment_method ? this.formData.payment_method.id : ''
          )

          if (this.isEdit) {
            this.isLoading = true
            data.append('_method', 'PUT')
            let response = await this.updateExpenseTemplate({
              id: this.$route.params.id,
              editData: data,
            })
            if (response.data.success) {
              window.toastr['success'](this.$t('expenses.updated_message'))
              this.$router.push('/admin/expenses-template')
              return true
            }
            this.isLoading = false
            window.toastr['error'](response.data.error)
          } else {
            this.isLoading = true
            let response = await this.addExpenseTemplate(data)
            this.isLoading = false

            if (response.data.success) {
              window.toastr['success'](this.$t('expenses.created_message'))
              this.$router.push('/admin/expenses-template')
              return true
            }
            window.toastr['success'](response.data.success)
          }

          //guardado fin
        }
      })
      //
    },
    irRuta() {
      this.$router.push(this.getReceiptUrl)
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
