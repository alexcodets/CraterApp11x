<template>
  <base-page>
    <base-loader v-if="isLoading" :show-bg-overlay="true" />

    <div v-else>
      <sw-page-header class="mb-3" :title="'Expenses'">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            to="/admin/dashboard"
            :title="$t('general.home')"
          />
          <sw-breadcrumb-item
            to="/admin/expenses"
            :title="$t('navigation.expenses')"
          />
          <sw-breadcrumb-item
            :to="`/admin/expenses/${$route.params.id}/docs`"
            :title="$t('general.docs')"
            active
          />
        </sw-breadcrumb>

        <template slot="actions">
          <sw-button
            tag-name="router-link"
            :to="`/admin/expenses`"
            class="mr-3"
            variant="primary-outline"
          >
            {{ $t('general.go_back') }}
          </sw-button>

          <sw-button
            tag-name="router-link"
            :to="`/admin/expenses/${$route.params.id}/view`"
            class="mr-3"
            variant="primary"
          >
            <eye-icon class="h-5 mr-1 -ml-2" />
            {{ $t('general.view') }}
          </sw-button>

          <sw-button
            tag-name="router-link"
            :to="`/admin/expenses/${$route.params.id}/edit`"
            class="mr-3"
            variant="primary"
          >
            <pencil-icon class="h-5 mr-1 -ml-2" />
            {{ $t('general.edit') }}
          </sw-button>
        </template>
      </sw-page-header>

      <div class="w-full">
        <div class="col-span-12">
          <sw-card>
            <div>
              <p class="text-gray-500 uppercase sw-section-title">
                {{ $t('general.general_data') }}
              </p>

              <div class="flex flex-wrap mt-3 md:mt-3">
                <div class="w-full md:w-1/4">
                  <div class="font-bold py-2">
                    {{ $t('expenses.expense_number') }}
                  </div>
                  <div>
                    <p class="text-gray-700 text-sm">
                      {{ formData.expense_number }}
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
                    {{ $t('expenses.subject') }}
                  </div>
                  <div>
                    <p class="text-gray-700 text-sm">
                      {{ formData.subject }}
                    </p>
                  </div>
                </div>

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
              </div>
            </div>

            <br /><br />

            <!--
                        <div class="flex z-40 justify-center mt-8 font-bold">
                            <span>{{ $t('expenses.docs')}}</span>
                        </div>
                        -->

            <!-- CARROUSEL -->
            <div v-if="count > 0">
              <div class="flex z-40 justify-center overflow-auto">
                <div class="slider">
                  <a
                    class="text-gray-600 text-success mx-1 rounded-full border border-gray-400 border-success cursor-pointer p-5"
                  >
                    <span style="font-size: 25px">
                      {{ file.position + 1 }}
                    </span>
                  </a>
                  <div class="slides">
                    <div id="slide-1">
                      <iframe
                        v-if="isPdf"
                        :src="file.base64"
                        width="100%"
                        height="100%"
                      >
                      </iframe>
                      <img
                        v-if="isImage"
                        :src="file.base64"
                        class="m-10 rounded-md"
                        style="
                          animation: fadeIn 2s ease;
                          position: relative !important;
                        "
                      />
                      <div
                        v-if="!isImage && !isPdf"
                        class="flex flex-wrap justify-center items-center text-center p-3"
                      >
                        <img
                          :src="file.src"
                          alt="ext"
                          style="
                            height: auto !important;
                            width: auto !important;
                            animation: fadeIn 2s ease;
                            position: relative !important;
                          "
                        />
                        <h2 class="w-full text-xl font-bold text-primary mt-3">
                          {{ $t('expenses.no_preview') }}
                        </h2>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="justify-center mt-5">
                <h6 class="mb-2 w-full truncate text-center">
                  {{ file.file_name }}
                </h6>
                <p class="mb-2 w-full text-center">
                  {{ $t('expenses.size') }}: {{ sizeFile(file.size) }}
                </p>
              </div>

              <div class="flex w-full justify-center">
                <a
                  @click="downloadFile(file)"
                  tag-name="a"
                  class="text-gray-600 hover:text-success mx-1 rounded-full border border-gray-400 hover:border-success cursor-pointer p-2"
                >
                  <DownloadIcon class="h-5 w-5" />
                </a>
              </div>

              <div class="flex z-40 justify-center mt-8">
                <sw-button
                  v-if="count > 1"
                  variant="primary"
                  type="button"
                  @click="nextFile()"
                >
                  <span style="font-size: 17px"
                    >{{ $t('general.next') }} &raquo;</span
                  >
                </sw-button>
              </div>
            </div>

            <div v-else class="flex w-full justify-center">
              <sw-badge
                class="no_document"
                :bg-color="$utils.getBadgeStatusColor('I').bgColor"
                :color="$utils.getBadgeStatusColor('I').color"
              >
                {{ $t('expenses.no_documents') }}
              </sw-badge>
            </div>
            <!-- CARROUSEL -->
          </sw-card>
        </div>
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
        customer: '',
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
      files: [],
      file: {},
      count: 0,
      currentPosition: 0,
      isPdf: true,
      isImage: false,
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
      this.isLoading = true
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
            ? response.data.expense.provider.title
            : 'Empty'
        this.formData.customer =
          response.data.expense.user != null
            ? response.data.expense.user.name
            : 'Empty'
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
        await this.getReceipt()
        await this.getDocs()

        this.formData.files = [...this.formData.receipt, ...this.formData.docs]

        let length = this.formData.files.length
        if (length > 0) {
          this.file = this.formData.files[0]
          this.file.position = this.currentPosition
          this.formatTypeValidation(this.file.typeFile)
          this.count = length
        }
      }
      this.isLoading = false
    },

    nextFile() {
      if (this.currentPosition == this.count - 1) {
        this.currentPosition = 0
      } else {
        this.currentPosition += 1
      }

      this.file = this.formData.files[this.currentPosition]
      this.file.position = this.currentPosition
      this.formatTypeValidation(this.file.typeFile)
    },

    formatTypeValidation(typeFile) {
      if (typeFile == 'pdf' || typeFile == 'application/pdf') {
        this.isPdf = true
        this.isImage = false
      } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(typeFile)) {
        this.isImage = true
        this.isPdf = false
      } else {
        this.file.src = this.imageExt(typeFile)
        this.isImage = false
        this.isPdf = false
      }
    },

    imageExt(ext) {
      return `/images/icon-ext/${ext}.png`
    },

    // Receipt
    async getReceipt() {
      let res = await this.getExpenseReceipt(this.$route.params.id)

      res.data.files.forEach((file) => {
        var oFile = this.dataURLtoFile(file.base64, file.file_name)
        oFile.id = file.id
        oFile.typeDoc = 'Receipt'
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
        oFile.typeDoc = 'Doc'
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
      if (file.typeDoc == 'Receipt') {
        const a = document.createElement('a')
        a.href = `/expenses/${this.$route.params.id}/receipt`
        //a.setAttribute("download", file.file_name);
        document.body.appendChild(a)
        a.click()
      } else {
        let res = await this.downloadExpenseDoc(file)
        const url = window.URL.createObjectURL(new Blob([res.data]))
        const a = document.createElement('a')
        a.href = url
        a.setAttribute('download', file.file_name)
        document.body.appendChild(a)
        a.click()
      }
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
.next {
  background-color: #07b676;
  color: white;
  text-decoration: none;
  display: inline-block;
  padding: 8px 16px;
  transition: all 250ms;
}

.next:hover {
  background-color: #03935e;
  transform: scale(1.075);
}

* {
  box-sizing: border-box;
}

.slider {
  width: 575px;
  height: 550px;
  text-align: center;
  overflow: hidden;
}

.slides {
  display: flex;
}

.slides > div {
  scroll-snap-align: start;
  flex-shrink: 0;
  width: 575px;
  height: 500px;
  margin-right: 50px;
  border-radius: 50px;
  background: #eee;
  transform-origin: center center;
  transform: scale(1);
  transition: transform 0.5s;
  position: relative;

  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 100px;
}
.slides > div:target {
  /*   transform: scale(0.8); */
}
.author-info {
  background: rgba(0, 0, 0, 0.75);
  color: white;
  padding: 0.75rem;
  text-align: center;
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  margin: 0;
}
.author-info a {
  color: white;
}

img {
  object-fit: contain;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.slider > a {
  display: inline-flex;
  width: 1.5rem;
  height: 1.5rem;
  background: white;
  text-decoration: none;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  margin: 0 0 0.5rem 0;
  position: relative;
}

html,
body {
  height: 100%;
  overflow: hidden;
}
body {
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(to bottom, #74abe2, #5563de);
  font-family: 'Ropa Sans', sans-serif;
}

.no_document {
  width: 700px;
  padding: 30px;
  font-size: 25px;
  font-family: CerebriSans-Regular, -apple-system, system-ui, Roboto, sans-serif;
  font-style: italic;
}
</style>