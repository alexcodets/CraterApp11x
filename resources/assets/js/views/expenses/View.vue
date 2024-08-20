<template>
  <base-page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header class="mb-3" :title="'Expenses'">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            to="/admin/dashboard"
            :title="$t('general.home')"
          />
          <sw-breadcrumb-item to="/admin/expenses" :title="'Expenses'" />
        </sw-breadcrumb>
      </sw-page-header>

      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          tag-name="router-link"
          :to="`/admin/expenses`"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary-outline"
          size="lg"
        >
          <pencil-icon class="h-5 mr-1 -ml-2" />
          {{ $t('general.go_back') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="`/admin/expenses/${$route.params.id}/edit`"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary"
          v-if="permissionModule.update"
        >
          <pencil-icon class="h-5 mr-1 -ml-2" />
          {{ $t('general.edit') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="`/admin/expenses/${$route.params.id}/docs`"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary"
        >
          <document-text-icon class="h-5 mr-1 -ml-2" />
          {{ $t('general.docs') }}
        </sw-button>

        <sw-button
          slot="activator"
          variant="primary"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          @click="removeExpense($route.params.id)"
          v-if="permissionModule.delete"
        >
          <trash-icon class="h-5 mr-1 -ml-2" />
          {{ $t('general.delete') }}
        </sw-button>
      </div>
    </div>

    <div class="w-full">
      <div class="col-span-12">
        <sw-card>
          <div>
            <p class="text-gray-500 uppercase sw-section-title">
              {{ $t('general.general_data') }}
            </p>

            <div class="flex flex-wrap mt-5 md:mt-7">
              <div class="w-full md:w-1/2">
                <p class="font-bold">{{ $t('expenses.expense_number') }}</p>
                <p class="text-gray-700 text-sm">
                  {{ formData.expense_number }}
                </p>
              </div>
            </div>

            <div class="flex flex-wrap mt-5 md:mt-7">
              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $t('expenses.status_name') }}
                </div>
                <div>
                  <p
                    v-if="formData.status == 'Active'"
                    class="text-gray-700 text-sm"
                  >
                    {{ $t('expenses.status_pr') }}
                  </p>

                  <p
                    v-if="formData.status != 'Active'"
                    class="text-gray-700 text-sm"
                  >
                    {{ $t('expenses.status_pe') }}
                  </p>
                </div>
              </div>
              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $t('expenses.expense_date') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    {{ formData.expense_date }}
                  </p>
                </div>
              </div>

              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $t('expenses.expense_category') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    {{ formData.category }}
                  </p>
                </div>
              </div>

              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $t('expenses.subject') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    {{ formData.subject }}
                  </p>
                </div>
              </div>
            </div>

            <div class="flex flex-wrap mt-5 md:mt-7">
              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $t('expenses.provider') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    <span
                      v-if="formData.provider != null && isLoading == false"
                      class="text-gray-700 text-sm"
                    >
                      <router-link
                        :to="{
                          path: `/admin/providers/${formData.provider.id}/view`,
                        }"
                        class="font-medium text-primary-500"
                      >
                        {{ formData.provider.title }}
                      </router-link>
                      <p style="font-size: 15px">
                        {{ formData.provider.providers_number }}
                      </p>
                    </span>

                    <span
                      v-if="formData.provider == null && isLoading == false"
                      class="text-gray-700 text-sm"
                    >
                      {{ $t('expenses.empty') }}
                    </span>
                  </p>
                </div>
              </div>

              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $t('expenses.customer') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    <span
                      v-if="formData.customer != null && isLoading == false"
                      class="text-gray-700 text-sm"
                    >
                      <router-link
                        :to="{
                          path: `/admin/customers/${formData.customer.id}/view`,
                        }"
                        class="font-medium text-primary-500"
                      >
                        {{ formData.customer.name }}
                      </router-link>

                      <p style="font-size: 15px">
                        {{ formData.customer.customcode }}
                      </p>
                    </span>

                    <span
                      v-if="formData.customer == null && isLoading == false"
                      class="text-gray-700 text-sm"
                    >
                      {{ $t('expenses.empty') }}
                    </span>
                  </p>
                </div>
              </div>

              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $t('expenses.item') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    {{ formData.item }}
                  </p>
                </div>
              </div>
            </div>

            <div class="w-full mt-5 md:mt-7">
              <p class="font-bold">{{ $t('expenses.note') }}</p>
              <p
                class="text-gray-700 text-sm"
                v-html="formData.note != null ? formData.note : ''"
              ></p>
            </div>

            <div class="w-full mt-5 md:mt-7">
              <p class="text-gray-500 uppercase sw-section-title">
                {{ $t('expenses.invoice') }}
              </p>
            </div>

            <div
              class="w-full mt-5 md:mt-7"
              v-if="formData.invoices.length > 0"
            >
              <div style="min-width: 50rem">
                <table class="w-full text-center item-table">
                  <colgroup>
                    <col style="width: 15%" />
                    <col style="width: 15%" />
                    <col style="width: 40%" />
                    <col style="width: 15%" />
                    <col style="width: 15%" />
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
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      class="box-border bg-white border border-gray-200 border-solid rounded-b"
                      v-for="(invoice, index) in formData.invoices"
                    >
                      <td class="pt-4 pr-4 pb-4 pl-4 align-center">
                        {{ invoice.invoice_number }}
                      </td>
                      <td class="pt-4 pr-4 pb-4 pl-4 align-center">
                        <div
                          v-html="
                            $utils.formatMoney(
                              invoice.subtotal,
                              defaultCurrency
                            )
                          "
                        />
                      </td>

                      <td
                        class="pt-4 pr-4 pb-4 pl-4 align-center"
                        v-if="invoice.taxes.length > 0"
                      >
                        <p class="pt-1 pb-1" v-for="(tax, i) in invoice.taxes">
                          {{ tax.name }} - {{ tax.percent }}%
                        </p>
                      </td>
                      <td class="pt-4 pr-4 pb-4 pl-4 align-center" v-else>
                        <p>{{ $t('expenses.expense_no_taxes') }}</p>
                      </td>

                      <td class="pt-4 pr-4 pb-4 pl-4 align-center">
                        <div
                          v-html="
                            $utils.formatMoney(
                              invoice.total_tax,
                              defaultCurrency
                            )
                          "
                        />
                      </td>
                      <td class="pt-4 pr-4 pb-4 pl-4 align-center">
                        <div
                          v-html="
                            $utils.formatMoney(invoice.total, defaultCurrency)
                          "
                        />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="w-full mt-5 md:mt-7">
              <p class="text-gray-500 uppercase sw-section-title">
                {{ $t('expenses.asodocuments') }}
              </p>
            </div>

            <!-- Receipt -->
            <div class="w-full mt-5 md:mt-7">
              <p class="font-bold">{{ $t('expenses.receipt') }}</p>
              <div class="flex flex-wrap" v-if="formData.receipt.length > 0">
                <div
                  class="px-1 py-2 w-full md:w-1/3"
                  v-for="(file, indexTr) in formData.receipt"
                  :key="indexTr"
                >
                  <div
                    class="bg-transparent border-2 border-gray-400 border-dashed rounded-md div-zoom-effect"
                  >
                    <div class="p-3 flex flex-wrap hover:text-success">
                      <h6 class="mb-2 w-full truncate text-center">
                        {{ file.file_name }}
                      </h6>
                      <p class="mb-2 w-full text-center">
                        {{ $t('expenses.size') }}: {{ sizeFile(file.size) }}
                      </p>

                      <!-- actions buttons -->
                      <div class="flex w-full justify-center">
                        <a
                          @click="OpenSeeDocumentExpensesModal(file)"
                          tag-name="a"
                          class="text-gray-600 hover:text-success mx-1 rounded-full border border-gray-400 hover:border-success cursor-pointer p-2"
                        >
                          <eye-icon class="h-5 w-5" />
                        </a>
                        <a
                          :href="`/expenses/${$route.params.id}/receipt`"
                          tag-name="a"
                          class="text-gray-500 hover:text-success mx-1 rounded-full border border-gray-400 hover:border-success cursor-pointer p-2"
                        >
                          <DownloadIcon class="h-5 w-5" />
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else>
                <p class="text-gray-700 text-sm mt-1">
                  {{ $t('expenses.empty') }}
                </p>
              </div>
            </div>
            <!-- Receipt -->

            <!-- Docs -->
            <div class="w-full mt-5 md:mt-7">
              <p class="font-bold">Docs</p>
              <div class="flex flex-wrap" v-if="formData.docs.length > 0">
                <div
                  class="px-1 py-2 w-full md:w-1/3"
                  v-for="(file, indexTr) in formData.docs"
                  :key="indexTr"
                >
                  <div
                    class="bg-transparent border-2 border-gray-400 border-dashed rounded-md div-zoom-effect"
                  >
                    <div class="p-3 flex flex-wrap hover:text-success">
                      <h6 class="mb-2 w-full truncate text-center">
                        {{ file.file_name }}
                      </h6>
                      <p class="mb-2 w-full text-center">
                        {{ $t('expenses.size') }}: {{ sizeFile(file.size) }}
                      </p>

                      <!-- actions buttons -->
                      <div class="flex w-full justify-center">
                        <a
                          @click="OpenSeeDocumentExpensesModal(file)"
                          tag-name="a"
                          class="text-gray-600 hover:text-success mx-1 rounded-full border border-gray-400 hover:border-success cursor-pointer p-2"
                        >
                          <eye-icon class="h-5 w-5" />
                        </a>
                        <a
                          @click="downloadFile(file)"
                          tag-name="a"
                          class="text-gray-600 hover:text-success mx-1 rounded-full border border-gray-400 hover:border-success cursor-pointer p-2"
                        >
                          <DownloadIcon class="h-5 w-5" />
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else>
                <p class="text-gray-700 text-sm mt-1">
                  {{ $t('expenses.empty') }}
                </p>
              </div>
            </div>
            <!-- Docs -->
          </div>
        </sw-card>
      </div>
    </div>
  </base-page>
</template>
  
<script>
import { mapActions, mapGetters } from 'vuex'
import ExpenseInvoice from './ExpenseInvoice.vue'
import expenseInvoiceStub from '../../stub/expenseInvoice'
import ObservatoryIcon from '../../components/icon/ObservatoryIcon'
import {
  CalculatorIcon,
  PencilIcon,
  TrashIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PlusIcon,
  DownloadIcon,
  EyeIcon,
} from '@vue-hero-icons/solid'

import { DocumentTextIcon } from '@vue-hero-icons/outline'

export default {
  components: {
    CalculatorIcon,
    ObservatoryIcon,
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    DownloadIcon,
    EyeIcon,
    DocumentTextIcon,
  },
  data() {
    return {
      formData: {
        expense_number: '',
        status: '',
        expense_date: '',
        subject: '',
        category: '',
        provider: '',
        customer: {},
        item: '',
        note: '',
        invoices: [],
        docs: [],
        docsSend: [],
        receipt: [],
      },
      permissionModule: {
        update: false,
        delete: false,
        read: false,
        create: false,
        createExpenses: false,
        accessExpenses: false,
        updateExpenses: false,
        readExpenses: false,
        deleteExpenses: false,
      },
      taxes: [],
      provider: null,
      //
      isLoading: false,
    }
  },
  computed: {
    ...mapGetters('providers', ['selectedViewProvider', 'providers']),
    ...mapGetters('company', ['defaultCurrency']),
    currency() {
      return this.defaultCurrency
    },
  },

  created() {
    this.isLoading = true
    this.loadTaxes()
    this.permissionsUserModule()
    this.loadData()
  },

  methods: {
    ...mapActions('expense', [
      'fetchExpenses',
      'deleteExpense',
      'fetchExpense',
      'getExpenseReceipt',
      'getExpenseDocs',
      'downloadExpenseDoc',
    ]),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('modal', ['openModal']),
    ...mapActions('taxType', ['fetchTaxTypes']),

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

    async loadData() {
      let response = await this.fetchExpense(this.$route.params.id)

      if (response.data) {
        this.formData.expense_number = response.data.expense.expense_number
        this.formData.status = response.data.expense.status
        this.formData.expense_date = response.data.expense.expense_date
        this.formData.subject =
          response.data.expense.subject != ''
            ? response.data.expense.subject
            : 'Empty'
        this.formData.category =
          response.data.expense.category != null
            ? response.data.expense.category.name
            : 'Empty'
        this.formData.provider =
          response.data.expense.provider != null
            ? response.data.expense.provider
            : null
        this.formData.customer =
          response.data.expense.user != null ? response.data.expense.user : null
        this.formData.item =
          response.data.expense.item != null
            ? response.data.expense.item.name
            : 'Empty'
        this.formData.note =
          response.data.expense.notes != null
            ? response.data.expense.notes
            : 'Empty'

        this.formData.invoices = response.data.expense.invoices
        this.provider = this.providers.find(
          (provider) => provider.id === response.data.expense.providers_id
        )

        // Get Docs
        this.formData.docs = []
        this.formData.docsSend = []
        this.getReceipt()
        this.getDocs()
        this.isLoading = false
      }
    },

    // Receipt
    async getReceipt() {
      let res = await this.getExpenseReceipt(this.$route.params.id)

      res.data.files.forEach((file) => {
        var oFile = this.dataURLtoFile(file.base64, file.file_name)
        oFile.id = file.id
        oFile.model_id = file.model_id
        oFile.base64 = file.base64
        oFile.file_name = file.file_name
        oFile.typeFile = file.file_name.split('.').pop()
        this.formData.receipt.push(oFile)
      })
    },

    // Docs
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
      this.openModal({
        title: this.$t('expenses.see_document_expenses'),
        componentName: 'SeeDocumentExpensesModal',
        props: {
          media: file,
        },
      })
    },

    async downloadFile(file) {
      let res = await this.downloadExpenseDoc(file)
      const url = window.URL.createObjectURL(new Blob([res.data]))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', file.file_name)
      document.body.appendChild(link)
      link.click()
    },
    //

    async removeExpense(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('expenses.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteExpense({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('expenses.deleted_message', 1))
            this.$router.push('/admin/expenses')
            return true
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },
    async permissionsUserModule() {
      const data = {
        module: 'providers',
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if (permissions.super_admin == false) {
        if (permissions.exist == false) {
          this.$router.push('/admin/dashboard')
        } else {
          const modulePermissions = permissions.permissions[0]
          if (modulePermissions == null) {
            this.$router.push('/admin/dashboard')
          } else if (modulePermissions.access == 0) {
            this.$router.push('/admin/dashboard')
          }
        }
      }

      // valida que el usuario tenga el permiso create, read, delete, update
      if (permissions.super_admin == true) {
        this.permissionModule.create = true
        this.permissionModule.update = true
        this.permissionModule.delete = true
        this.permissionModule.read = true
      } else if (permissions.exist == true) {
        const modulePermissions = permissions.permissions[0]
        if (modulePermissions.create == 1) {
          this.permissionModule.create = true
        }
        if (modulePermissions.update == 1) {
          this.permissionModule.update = true
        }
        if (modulePermissions.delete == 1) {
          this.permissionModule.delete = true
        }
        if (modulePermissions.read == 1) {
          this.permissionModule.read = true
        }
      }

      const dataExpenses = {
        module: 'expenses',
      }
      const permissionsExpenses = await this.getUserModules(dataExpenses)
      if (permissionsExpenses.super_admin == true) {
        this.permissionModule.accessExpenses = true
        this.permissionModule.createExpenses = true
        this.permissionModule.updateExpenses = true
      } else if (permissionsExpenses.exist == true) {
        const modulePermissions = permissionsExpenses.permissions[0]
        if (modulePermissions == null || modulePermissions.access == 0) {
          this.permissionModule.accessExpenses = false
          this.permissionModule.createExpenses = false
          this.permissionModule.updateExpenses = false
          this.permissionModule.deleteExpenses = false
          this.permissionModule.readExpenses = false
        } else {
          if (modulePermissions.access == 1) {
            this.permissionModule.accessExpenses = true
          }
          if (modulePermissions.update == 1) {
            this.permissionModule.updateExpenses = true
          }
          if (modulePermissions.create == 1) {
            this.permissionModule.createExpenses = true
          }
          if (modulePermissions.delete == 1) {
            this.permissionModule.deleteExpenses = true
          }
          if (modulePermissions.read == 1) {
            this.permissionModule.readExpenses = true
          }
        }
      }
    },
  },
}
</script>

<style scoped>
.div-zoom-effect div {
  -webkit-transition: all 0.9s ease; /* Safari y Chrome */
  -moz-transition: all 0.9s ease; /* Firefox */
  -o-transition: all 0.9s ease; /* IE 9 */
  -ms-transition: all 0.9s ease; /* Opera */
  width: 100%;
}
.div-zoom-effect:hover div {
  -webkit-transform: scale(1.1);
  -moz-transform: scale(1.1);
  -ms-transform: scale(1.1);
  -o-transform: scale(1.1);
  transform: scale(1.1);
}
.div-zoom-effect {
  /*Ancho y altura son modificables al requerimiento de cada uno*/
  width: auto;
  height: auto;
  overflow: hidden;
}
.downloadFilename {
  cursor: pointer;
  color: rgba(88, 81, 216, var(1));
}
</style>
  