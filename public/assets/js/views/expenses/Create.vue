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
          <div class="hidden md:block">
            <sw-button
              :loading="isLoading"
              :disabled="isLoading"
              variant="primary"
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
          </div>
        </template>
      </sw-page-header>

      <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />

      <sw-card v-else>
        <div class="grid gap-6 grid-col-1 md:grid-cols-2">
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

          <sw-input-group :label="$t('expenses.provider')">
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
                class="
                  flex
                  items-center
                  justify-center
                  w-full
                  px-4
                  py-3
                  bg-gray-200
                  border-none
                  outline-none
                "
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

          <sw-input-group :label="$t('expenses.select_payment')">
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

          <sw-input-group :label="$t('expenses.expense_date_pay')">
            <base-date-picker
              v-model="formData.payment_date"
              :calendar-button="true"
              class="mt-2"
              calendar-button-icon="calendar"
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
        </div>

        <div class="grid gap-6 grid-col-1 mt-4">
          <sw-input-group :label="$t('expenses.note')" :error="notesError">
            <sw-textarea
              v-model="formData.notes"
              rows="4"
              @input="$v.formData.notes.$touch()"
            />
          </sw-input-group>

          <sw-input-group :label="$t('expenses.receipt')">
            <div v-if="previewReceipt" class="w-full flex justify-end mb-3">
              <a v-if="isReceiptAvailable" :href="getReceiptUrl" tag-name="a" class="text-gray-500 hover:text-success mx-1 rounded-full border border-gray-400 hover:border-success cursor-pointer p-2">
                <DownloadIcon class="h-5 w-5" />
              </a>
              <div @click="$refs.receiptUpload.click()" class="text-gray-500 hover:text-warning mx-1 rounded-full border border-gray-400 hover:border-warning cursor-pointer p-2">
                <SwitchHorizontalIcon  class="h-5 w-5" />
              </div>
              <div  @click="removePdfReceipt()" class="text-gray-500 hover:text-danger mx-1 rounded-full border border-gray-400 hover:border-danger cursor-pointer p-2">
                <XIcon  class="h-5 w-5" />
              </div>
            </div>
            <div id="receipt-box">
              <div
                v-if="previewReceipt"
                class="
                  relative
                  flex
                  items-center
                  justify-center
                  h-24
                  bg-transparent
                  border-2 border-gray-200
                  border-dashed
                  rounded-md
                  image-upload-box
                  hover:bg-gray-200
                "
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
              <div @click="$refs.receiptUpload.click()" v-else class="flex flex-col items-center border-2 border-gray-200 border-dashed rounded-md py-2">
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

            <input ref="receiptUpload" class="form-control hidden"  type="file" multiple @change="onChange">

          </sw-input-group>
        </div>


        <!-- LISTA DE DOCUMENTOS -->
        <div class="w-full mt-4">

          <sw-input-group :label="$t('expenses.docs')">
            <label for="uploadDocs">
              <div id="docs-box " 
                  class="
                    hover:bg-gray-200
                    py-2
                    cursor-pointer
                    md:w-1/3
                    mt-5 bg-transparent 
                    border-2 border-gray-400
                    border-dashed
                    rounded-md">
                <input class="form-control hidden uploadDocs" id="uploadDocs" type="file" multiple @change="onChangeDocs">
                <div  class="flex flex-col items-center" >
                  <cloud-upload-icon
                    class="h-5 mb-2 text-xl leading-6 text-gray-400"
                  />
                  
                    <p class="text-xs leading-4 text-center text-gray-400">
                      <span id="pick-avatar" class=" text-primary-500" >browse</span>
                      to choose a file
                    </p>
                </div>   
              </div>
            </label>

            <div class="flex flex-wrap">
              <div class="px-1 py-2 w-full md:w-1/3" 
                v-for="(file, indexTr) in formData.docs" :key="indexTr">
                <div 
                  class="bg-transparent 
                  border-2 border-gray-400
                  border-dashed
                  rounded-md">
                  <div  class="p-3 flex flex-wrap">
                      <h6 class="mb-2 w-full truncate"
                      v-bind:class="{downloadFilename: isEdit }"
                      @click="downloadFile(file.name)"
                      >{{ file.name }}</h6>
                      <p class="mb-2 w-full " >{{ $t('expenses.size')}}: {{ sizeFile(file.size) }}</p>

                      <!-- actions buttons -->
                      <div  class="flex w-full">
                        <span v-bind:class="{downloadFilename: isEdit }"
                         v-on:click="OpenSeeDocumentExpensesModal(file)" class="pointer">
                          <eye-icon class="h-5 mr-2 text-gray-600"/>
                        </span>
                        <span v-on:click="deleteFile(file.name)" class="pointer">
                          <trash-icon class="h-5 mr-2 text-gray-600"/>
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

        <div class="block mt-2 md:hidden">
          <sw-button
            :disabled="isLoading"
            :loading="isLoading"
            :tabindex="6"
            variant="primary"
            type="submit"
            size="lg"
            class="flex w-full"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{
              isEdit
                ? $t('expenses.update_expense')
                : $t('expenses.save_expense')
            }}
          </sw-button>
        </div>
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
import { DownloadIcon, SwitchHorizontalIcon, XIcon } from '@vue-hero-icons/outline'
import { CloudUploadIcon, ShoppingCartIcon, TrashIcon, EyeIcon } from '@vue-hero-icons/solid'
import CustomFieldsMixin from '../../mixins/customFields'

export default {
  components: {
    CloudUploadIcon,
    ShoppingCartIcon,
    DownloadIcon,
    TrashIcon,
    EyeIcon,
    SwitchHorizontalIcon,
    XIcon
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
      formData: {
        expense_category_id: null,
        expense_date: new Date(),
        amount: 100,
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
        docs: []
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

      amount: {
        required,
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
        return this.$t('expenses.edit_expense')
      }
      return this.$t('expenses.new_expense')
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
      'addExpense',
      'updateExpense',
      'fetchExpense',
      'downloadExpenseDoc'
    ]),

    ...mapActions('modal', ['openModal']),

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
    sizeFile(size){
      if(size < 1024){
        return size + ' B'
      }
      if(size < 1048576){
        return (size / 1024).toFixed(2) + ' KB'
      }
      if(size < 1073741824){
        return (size / 1048576).toFixed(2) + ' MB'
      }
      return (size / 1073741824).toFixed(2) + ' GB'
    },
    OpenSeeDocumentExpensesModal({id}){
      let media = this.formData.docs.find(file => file.id === id);
      this.openModal({
        title: this.$t('expenses.see_document_expenses'),
        componentName: 'SeeDocumentExpensesModal',
        props: {
          media
        }
      })
      
    },
    onChange(e) {
      if(e == null) return;
      if (e.target.files[0].type == 'application/pdf') {
        const reader = new FileReader();
        reader.readAsDataURL(e.target.files[0]);
        reader.onload = (event) => {
          this.previewReceipt = event.target.result
        }

        this.fileObject = e.target.files[0]
        this.isReceiptAvailable = false
      } else {
        window.toastr['error'](this.$t('expenses.error_not_pdf'));
      }
    },

    onChangeDocs(data) {
      // console.log('file data: ', data.target.files);
      data.target.files.forEach(file => {
        this.formData.docs.push(file);
      });   
      // console.log('file data: ', this.formData.docs);
    },

    deleteFile(cFileName){
      this.formData.docs = this.formData.docs.filter(file => file.name !== cFileName);
    },

    async downloadFile(cFileName){
      if (!this.isEdit){
        window.toastr['error'](this.$t('expenses.error_download_on_create'))
        return true;
      }
      let media = this.formData.docs.filter(file => file.name === cFileName);
      // console.log('media: ', media)
      let res = await this.downloadExpenseDoc(media);
      // console.log('res file: ', res);
      const url = window.URL.createObjectURL(new Blob([res.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', media[0].name);
      document.body.appendChild(link);
      link.click();
    },

    // async getReceipt() {
    //   let res = await this.getExpenseReceipt(this.$route.params.id)
    //     console.log(res);
    //   if (res.data.error) {
    //     this.isReceiptAvailable = false
    //     return true
    //   }
    //  console.log('res receipt: ', res.data);
    //   this.isReceiptAvailable = true
    //   this.previewReceipt = res.data.image
    //   this.isPDF = true;
    // },

    async getDocs() {
      let res = await this.getExpenseDocs(this.$route.params.id);

      res.data.files.forEach(file => {
        var oFile = this.dataURLtoFile(file.base64, file.file_name);
        oFile.id = file.id;
        oFile.model_id = file.model_id;
        oFile.base64 = file.base64;
        oFile.file_name = file.name;
        oFile.typeFile = file.file_name.split('.').pop()
        this.formData.docs.push(oFile);
      });
    },

    dataURLtoFile(dataurl, filename) {
        var arr = dataurl.split(','),
            mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]), 
            n = bstr.length, 
            u8arr = new Uint8Array(n);
            
        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }
        
        return new File([u8arr], filename, {type:mime});
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
      await this.fetchCategories({ limit: 'all' })
      await this.fetchCustomers({ limit: 'all' })
      await this.fetchProviders({ limit: 'all' })
      await this.fetchItems({ limit: 'all' })
      await this.fetchPaymentModes({ limit: 'all' })

      if (this.isEdit) {
        this.isRequestOnGoing = true
        let response = await this.fetchExpense(this.$route.params.id)
        await this.fetchPayments({ limit: 'all', expenses: 'no' })

        if(response.data.expense.receiptPdfUrl !== null){
          this.isReceiptAvailable = true
          this.previewReceipt = response.data.expense.receiptPdfUrl
        }
        this.formData = { ...this.formData, ...response.data.expense }
        this.formData.docs = []
    
        this.formData.expense_date = moment(
          this.formData.expense_date
        ).toString()

        if(!response.data.expense.payment_date){
          this.formData.payment_date = new Date()
        } else {
          this.formData.payment_date = moment(
            this.formData.payment_date
          ).toString()

        }

       /*  console.log('payment date: ', this.formData.payment_date);
        console.log('date: ', new Date() ); */

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
          this.customer = this.customers.find(
            (customer) => customer.id === response.data.expense.user_id
          )
        }

        if (response.data.expense.providers_id) {
          this.provider = this.providers.find(
            (provider) => provider.id === response.data.expense.providers_id
          )
        }

        if (response.data.expense.items_id) {
          this.item = this.items.find(
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
            expenses: 'yes',
            customer_id: response.data.expense.user_id,
          })
          this.isCreate = true
          this.payment = this.payments.find(
            (payment) => payment.id === response.data.expense.payment_id
          )
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
        // console.log('form data docs', this.formData.docs);
        return true
      }
      this.checkAutoGenerate()
      await this.setInitialCustomFields('Expense')
      if (this.$route.query.customer) {
        this.setExpenseCustomer(parseInt(this.$route.query.customer))
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
      
        this.formData.payment_date = null;
        this.formData.payment_date =  new Date(val.payment_date); 

        this.formData.payment_date = this.formData.payment_date.toString();
         
      }
      if (val.payment_method) {
        this.formData.payment_method = val.payment_method;
      }
    },

    removePdfReceipt(){
      this.previewReceipt = null
      this.fileObject = null
    },
    async sendData() {
      let validate = await this.touchCustomField()
      this.$v.category.$touch()
      this.$v.formData.$touch()
      if (this.$v.$invalid || validate.error) {
        return true
      }

      this.formData.expense_number =
        this.expensePrefix + '-' + this.expenseNumAttribute

      let data = new FormData()

      if (this.fileObject) {
        data.append('attachment_receipt', this.fileObject)
      }
      if(this.previewReceipt == null){
        data.append('attachment_receipt', 'none')
      }
      if (this.formData.docs) {
        this.formData.docs.forEach(file => {
          data.append('attachment_docs[]', file)
        });
        // console.log('data docs', data.attachment_docs);

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
      data.append('customFields', JSON.stringify(this.formData.customFields))
      data.append('providers_id', this.provider ? this.provider.id : '')
      data.append('items_id', this.item ? this.item.id : '')
      data.append('payment_id', this.payment ? this.payment.id : '')

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

      // console.log('data for save/edit: ', data)
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
    },
    irRuta() {
      console.log(this.getReceiptUrl)
      this.$router.push(this.getReceiptUrl)
    },
  },
}
</script>

<style scoped>
  .previewHeight{
    height: 600px;
  }
  .pointer{
    cursor: pointer;
  }

  .marginFilename{
    margin-left: 31px;
  }

  .downloadFilename{
    cursor: pointer;
    color: rgba(88, 81, 216, var(1));
  }
</style>
