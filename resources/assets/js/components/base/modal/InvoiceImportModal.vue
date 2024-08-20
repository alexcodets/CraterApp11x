<style>
#tabla-especial {
  border-collapse: collapse;
  width: 100%;
  border: 3px solid black;
}

#tabla-especial th,
#tabla-especial td {
  border: 1px solid black;
  padding: 10px;
}

/* Propiedades para el overflow */
.table-container_modal_inv {
  max-height: 300px;
  max-width: 500px;
  overflow-y: auto;
  overflow-x: auto;
}

.error-box {
  border: 1px solid black;
  padding: 10px;
  margin: 10px;
}

.error-box h2 {
  color: red;
  margin-left: 10px;
  margin-bottom: 2px;
}

.error-box p {
  margin-left: 20px;
  margin-bottom: 2px;
}

.error-box p:nth-child(3) {
  font-weight: bold;
  font-style: italic;
}

.error-box ol {
  margin-left: 35px;
  margin-bottom: 2px;
}

.error-box li {
  color: gray;
  margin-bottom: 1px;
}
</style>

<template>
  <form action="" @submit.prevent="EventSubir">
    <div v-if="!banCsv">
      <div class="py-4 px-6 form-group-row">
        <h3 style="font-weight: bold;
font-style: italic;">{{ $t('general.text_csv_inv') }}</h3>
      </div>

      <div class="py-4 px-6 form-group-row">
        <div class="col-sm-10">
          <input
            type="file"
            id="file"
            ref="file"
            v-on:change="handleFileUpload()"
            accept=".CSV"
            class="form-control mb-2"
          />
          <!-- <a
             class="relative my-0 ml-0 mr-6 text-sm font-medium cursor-pointer text-primary-500"
             @click="exampleArch"
           >
             {{ $t('general.deselect') }}
           </a> -->
      
          <a    class="relative my-0 ml-0 text-sm font-medium cursor-pointer text-primary-500 inline"  style="text-transform: uppercase; margin-left: 2em;"   href="#" @click="downloadCsv"> {{ $t('expenses.download_exmaple') }}</a>

      

         
    <a    class="relative my-0 ml-0 text-sm font-medium cursor-pointer text-primary-500 inline"  style="text-transform: uppercase; margin-left: 2em;"   href="#" @click="downloadGuide"> {{ $t('general.download_guide') }}</a>

      

          <sw-button
            v-if="banCargArch"
            class="flex mr-2 w-50"
            variant="primary"
            type="button"
            @click="cancelLoadArch"
          >
            {{ $t('general.remove') }}
          </sw-button>
        </div>
      </div>

      <br />

   

        <div  v-if="this.showErrors == true && this.listErrors != null" class="error-box" style="max-height: 400px; overflow-y: scroll;">
            <div v-for="(error, key) in this.listErrors" :key="key">
                  <h2  v-if="key== 'date_due' " style="color: red;">{{ $t('general.due_date') }}</h2>
                  <h2  v-if="key== 'date_invoice' " style="color: red;">{{ $t('general.date_invoice')  }}</h2>
                  <h2  v-if="key== 'date_service' " style="color: red;">{{ $t('general.date_service')  }}</h2>
                  <h2  v-if="key== 'discount_type' " style="color: red;">{{ $t('general.discount_type_e')  }}</h2>
                  <h2  v-if="key== 'users_no_found' " style="color: red;">{{ $t('general.users_no_found')  }}</h2>
                  <h2  v-if="key== 'status' " style="color: red;">{{ $t('general.statust')  }}</h2>
                  <h2  v-if="key== 'paid_status' " style="color: red;">{{ $t('general.paid_status')  }}</h2>
                  <h2  v-if="key== 'per_discount' " style="color: red;">{{ $t('general.per_discount')  }}</h2>
                  <h2  v-if="key== 'tax_type' " style="color: red;">{{ $t('general.tax_type')  }}</h2>
                  <h2  v-if="key== 'users_multiple_diff' " style="color: red;">{{ $t('general.users_multiple_diff')  }}</h2>
                  <h2  v-if="key== 'users_multiple_same' " style="color: red;">{{ $t('general.users_multiple_same')  }}</h2>
                  <h2  v-if="key== 'invoice' " style="color: red;">{{ $t('general.invoice_inv')  }}</h2>
                  <h2  v-if="key== 'due_biggest' " style="color: red;">{{ $t('general.due_biggest')  }}</h2>
                  <h2  v-if="key== 'discounts_invoice_positive' " style="color: red;">{{ $t('general.discounts_invoice_positive')  }}</h2>
                  <h2  v-if="key== 'status_unpaid_total_diff' " style="color: red;">{{ $t('general.status_unpaid_total_diff')  }}</h2>
                  <h2  v-if="key== 'taxes_no_found' " style="color: red;">{{ $t('general.taxes_no_found')  }}</h2>
                  <h2  v-if="key== 'discount_line_int' " style="color: red;">{{ $t('general.discount_line_int')  }}</h2>
                  <h2  v-if="key== 'discount_line_val' " style="color: red;">{{ $t('general.discount_line_val')  }}</h2>

                  <h2  v-if="key== 'total_int' " style="color: red;">{{ $t('general.total_int')  }}</h2>
                  <h2  v-if="key== 'quantity_no_int' " style="color: red;">{{ $t('general.quantity_no_int')  }}</h2>


                  
                  
                  
                  

                  <p> {{ $t('general.list_error') }} :</p>
                  <p>{{ error.title }}</p>

                    <ol type="1">
                      <li v-for="item in error.items" :key="item">{{ item }}</li>
                    </ol>
            </div>
        </div>
      
    </div>

      <div  v-if="this.banCsv" class="">
        <div >
          <sw-input-group
            :label="$t('general.warningdate')"
            class="mt-2 xl:mx-8"
          >
            <sw-select
              v-model="formData.formatDate"
              :options="dateOptions"
              :placeholder="$t('general.warningdate')"
              track-by="name"
              label="name"
              style="max-width: 300px; margin-bottom: 10px"
            />
          </sw-input-group>
        </div>

        <div v-if="this.banCsv" style="overflow-y: scroll; height: 500px">
          <!-- Tabla dinámica -->
          <table id="tabla-especial">
            <!-- Cabeza de la tabla -->
            <thead>
              <!-- Fila con los nombres de las columnas -->
              <tr>
                <!-- Iterar sobre la primera fila de la matriz -->
                <th
                  v-for="(column, index) in formData.listCsv[0]"
                  v-bind:key="index"
                >
                  <!-- Mostrar el nombre de la columna -->
                  {{ column }}
                </th>
              </tr>
            </thead>
            <!-- Cuerpo de la tabla -->
            <tbody>
              <!-- Iterar sobre las filas de la matriz, excepto la primera -->
              <tr
                v-for="(row, rowIndex) in formData.listCsv.slice(1)"
                v-bind:key="rowIndex"
              >
                <!-- Iterar sobre las celdas de la fila -->
                <td v-for="(cell, cellIndex) in row" v-bind:key="cellIndex">
                  <!-- Mostrar el valor de la celda -->
                  {{ cell }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="z-0 flex justify-end p-4">
      <sw-button
        class="flex my-4 ml-6 mr-2 w-50"
        variant="primary-outline"
        type="button"
        @click="closePaymentModeModal"
      >
        {{ $t('general.cancel') }}
      </sw-button>
      <sw-button
        variant="primary"
        type="submit"
        size="lg"
        class="flex my-4 my-4 mr-6 ml-2 w-50"
        :loading="isLoading"
      >
        <save-icon v-if="this.banCsv" class="mr-2 -ml-1" />
        {{ this.banCsv ? $t('general.save') : $t('general.next')}}

        <arrow-right-icon v-if="!this.banCsv" class="h-5 ml-2 -mr-1" />
      </sw-button>
    </div>
  </form>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { VueCsvImport } from 'vue-csv-import'
import { ArrowRightIcon } from '@vue-hero-icons/solid'
const {
  required,
  minLength,
  numeric,
  maxLength,
  minValue,
  requiredIf,
} = require('vuelidate/lib/validators')

export default {
  components: {
    ArrowRightIcon,
  },
  data() {
    return {
      isInvalidFormat: false,
      json_data: [
        {
          type: 'P',
          prefix: '12345',
          from: '',
          to: '',
          name: 'New Test 1',
          rate: '0.9',
          category: 'C',
          country: 'US',
        },
        {
          type: 'P',
          prefix: '67890',
          from: '',
          to: '',
          name: 'New Test 2',
          rate: '8',
          category: 'T',
          country: 'US',
        },
        {
          type: 'FT',
          prefix: '',
          from: '123',
          to: '456',
          name: 'New Test 3',
          rate: '8',
          category: 'T',
          country: 'AF',
        },
      ],
      file: '',
      banCsv: false,
      banCargArch: false,
      listErrors: null,
      showErrors: false,
      isEdit: false,
      isLoading: false,
      group: [],
      typeArch: '',
      name1: '',
      prefix: '',
      rate: '',
      import_existing: true,
      import_new: false,
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
      prefijo: [
        {
          value: 'prefix',
          text: 'Prefix',
        },
        {
          value: 'name',
          text: 'Name',
        },
        {
          value: 'rate_per_minute',
          text: 'Rate',
        },
        {
          value: 'category',
          text: 'Category',
        },
      ],
      name: [
        {
          value: 'prefix',
          text: 'Prefix',
        },
        {
          value: 'name',
          text: 'Name',
        },
        {
          value: 'rate_per_minute',
          text: 'Rate',
        },
        {
          value: 'category',
          text: 'Category',
        },
      ],
      rate_per_minutes: [
        {
          value: 'prefix',
          text: 'Prefix',
        },
        {
          value: 'name',
          text: 'Name',
        },
        {
          value: 'rate_per_minute',
          text: 'Rate',
        },
        {
          value: 'category',
          text: 'Category',
        },
      ],
      category: [
        {
          value: 'prefix',
          text: 'Prefix',
        },
        {
          value: 'name',
          text: 'Name',
        },
        {
          value: 'rate_per_minute',
          text: 'Rate',
        },
        {
          value: 'category',
          text: 'Category',
        },
      ],
      categorysOptions: [
        { value: 'C', text: 'Custom' },
        { value: 'I', text: 'International' },
        { value: 'T', text: 'Toll Free' },
      ],

      formData: {
        listCsv: [],
        file: null,
        formatDate: null,
      },
      types: [
        { name: 'Inbound', value: 'Inbound' },
        { name: 'Outbound', value: 'Outbound' },
      ],

      dateOptions: [
        { name: 'd/m/Y - Example: 05/03/2024', value: 'd/m/Y' },
        { name: 'm/d/Y - Example: 03/05/2024', value: 'm/d/Y' },
        { name: 'Y-m-d - Example: 2024-03-05', value: 'Y-m-d' },
        { name: 'j-m-Y - Example: 5-3-2024', value: 'j-m-Y' },
        { name: 'Y.m.d - Example: 2024.03.05', value: 'Y.m.d' },
      ],
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),
    idError() {
      if (!this.$v.formData.prefixrate_groups_id.$error) {
        return ''
      }
      if (!this.$v.formData.prefixrate_groups_id.required) {
        return this.$tc('validation.required')
      }
    },

    groupNameError() {
      if (!this.$v.formData.prefix_group_name.$error) {
        return ''
      }

      if (!this.$v.formData.prefix_group_name.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.prefix_group_name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.prefix_group_name.$params.minLength.min,
          { count: this.$v.formData.prefix_group_name.$params.minLength.min }
        )
      }

      if (!this.$v.formData.prefix_group_name.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },

    groupTypeError() {
      if (!this.$v.formData.prefix_group_type.$error) {
        return ''
      }
      if (!this.$v.formData.prefix_group_type.required) {
        return this.$t('validation.required')
      }
    },
  },
  validations: {
    formData: {},
  },
  async mounted() {
    let response = await this.fetchPrefixGroups({ limit: 'all' })
    this.group = [...response.data.prefixGroups.data]
    /* if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    } */
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('internacionalrate', [
      'fetchInternacionals',
      'CargarCsvDestination',
      'ImportarCsvDestination',
      'ExportCsvDestination',
      'updateInternacional',
      'addInternacional',
    ]),
    ...mapActions('prefixGroup', ['fetchPrefixGroups']),
    ...mapActions('payment', ['addPaymentMode', 'updatePaymentMode']),
    ...mapActions('invoice', ['importProcessInvoice']),

    resetFormData() {
      this.formData = {
        id: null,
        name: null,
      }
      this.$v.formData.$reset()
    },

    downloadGuide() {
      // Llamar al endpoint para descargar el PDF
      window.open('/download-guide', '_blank')
    },

    downloadCsv() {
      // Llamar al endpoint para descargar el PDF
      window.open('/download-csvexample', '_blank')
    },

    showCategory(category) {
      const categorysOptions = {
        C: 'Custom',
        I: 'International',
        T: 'Toll Free',
      }
      return categorysOptions[category]
    },

    /* setExisting() {
            if (this.import_existing) {
                this.import_new = false;
            }else{
                this.import_new = true;
            }
     }, */
    presionar() {
      this.CargarCsvDestination()
    },
    setNew() {
      if (this.import_new) {
        this.import_existing = false
      } else {
        this.import_existing = true
      }
    },
    /* Prueba Cvs */
    async EventSubir() {
    //  console.log('EventSubir')
      if (!this.banCsv && this.file) {
        this.isLoading = true
      //  console.log('EventSubir 2')
        this.typeArch = this.file.name.split('.')
        this.typeArch = this.typeArch[1]
        let formData = new FormData()
        //console.log('EventSubir 4')
        formData.append('file', this.file)
        //console.log('EventSubir 5')

        let response = await this.ImportarCsvDestination(formData)

       // console.log(response)
        // Validate Csv Format
        this.isInvalidFormat = false
        let messageindex = this.validateCsv(response.data.result[0])
       // console.log(messageindex)
        if (messageindex.success == false) {
          this.file = this.$refs.file.value = ''
          this.banCargArch = false
          this.isInvalidFormat = false

          if (messageindex.type == 0) {
            window.toastr['error'](
              messageindex.message + ': ' + messageindex.variable
            )
          }

          if (messageindex.type == 2) {
            window.toastr['error'](
              messageindex.message + ': ' + messageindex.variable
            )
          }

          if (messageindex.type == 1) {
            window.toastr['error'](messageindex.message)
          }
          this.isLoading = false
          return
        }
        //

        if (this.typeArch === 'xlsx') {
       //   console.log('xlsx')
          this.formData.listCsv = [...response.data.result[0]]
        } else {
         // console.log('else')
          if (response.data.result[0][0].length === 1) {
            let vect = [],
              aux = []
            for (let i = 0; i < response.data.result[0].length; i++) {
              aux = response.data.result[0][i][0].split(';')
              vect.push(aux)
            }
            this.formData.listCsv = [...vect]
          } else {
            this.formData.listCsv = [...response.data.result[0]]
          }

          this.formData.file = this.file
        }
        //console.log("EventSubir 6");
        this.banCsv = true
        this.isLoading = false
      } else if (this.banCsv) {
        /* prefijo */

       // console.log(this.formData)
        this.isLoading = true

        if (this.formData.formatDate == null) {
          window.toastr['error'](this.$tc('general.warningdate2'))
          return false
        }

        let data = new FormData()

        data.append('file', this.formData.file)
        data.append('date_format', this.formData.formatDate?.value)
        data.append('listCsv', this.formData.listCs)
      //  console.log(data)
        let res

        res = await this.importProcessInvoice(data)
      //  console.log(res)

        if (res.data.success == false) {
          window.toastr['error'](res.data.message)
          this.listErrors = res.data.data
          this.banCsv = false
          this.banCargArch = false
          this.file = ''
          this.showErrors = true
          this.isLoading = false
          return false
        } else {
          window.toastr['success'](this.$tc('general.inv_upload'))
          this.closePaymentModeModal()
        }
        // window.toastr['success'](res.data.success)
        //this.refreshData ? this.refreshData() : ''
        // this.closePaymentModeModal()
        /* this.$router.push('/admin/corePBX/billing-templates/international-rate')
            return true */
      }
    },
    handleFileUpload() {
    //  console.log(this.$refs.file)
      //console.log(this.$refs.file.files)
      this.file = this.$refs.file.files[0]
      this.banCargArch = true
    },

    cancelLoadArch() {
      this.file = this.$refs.file.value = ''
      this.banCargArch = false
    },
    exampleArch() {
      this.ExportCsvDestination()
    },

    closePaymentModeModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },

    validateCsv(data) {
      //console.log('validateCsv')
      //console.log(data[0])
      // Array con los nombres de las columnas esperadas
      const expectedColumns = [
        'InvoiceNo',
        'CustomerNumber',
        'Customer',
        'InvoiceDate',
        'DueDate',
        'Notes',
        'Status',
        'PaidStatus',
        'TaxPerItem',
        'DiscountPerItem',
        'Subtotal',
        'DiscountType',
        'DiscountVal',
        'Discounts',
        'TotalTax',
        'Total',
        'AmountDue',
        'PbxServiceNumber',
        'ServiceNumber',
        'ServicePeriodFrom',
        'ServicePeriodTo',
        'ItemType',
        'ItemNumber',
        'Item(Product/Service)',
        'ItemDescription',
        'ItemQuantity',
        'ItemRate',
        'ItemDiscountType',
        'ItemDiscountVal',
        'ItemDiscount',
        'ItemAmount',
        'TaxName',
        'TaxPercentage',
        'TaxAmount',
        'TaxItem',
      ]
      // Número máximo de líneas permitidas
      const maxLines = 1000
      // Objeto para retornar el resultado de la validación
      let response = {
        success: true,
        message: '',
        variable: null,
        type: 0,
      }

      const csvColumnsNumber = data[0].length
      const expectedColumnsNumber = expectedColumns.length
      //console.log(csvColumnsNumber)
      //console.log(expectedColumnsNumber)
      // Validar el formato del archivo CSV
      // Variable que almacena el resultado de la comparación
      let validFormat = data[0].every(
        (value, index) => value === expectedColumns[index]
      )
      //console.log(validFormat)
      if (!validFormat) {
        //console.log('entro erro')
        // Si el formato no coincide, asignar false a success y un mensaje de error
        response.success = false
        this.isInvalidFormat = true
        // Índice que recorre el array de las columnas esperadas
        let index = 0
        // Bucle que busca el valor que no coincide
        while (index < expectedColumns.length && !validFormat) {
          // Comparar el valor de data con el valor de expectedColumns
          //console.log('data')
          //console.log(data[0][index])

          //console.log(expectedColumns[index])
          if (data[0][index] !== expectedColumns[index]) {
            //console.log('diferentes')
            //console.log(data[0][index])
            //console.log(expectedColumns[index])
            // Si no coinciden, cambiar el valor de validFormat a false
            validFormat = false

            // Agregar el nombre de la columna que falla al mensaje de error
            response.message =
              'El formato del archivo CSV no es válido. Por favor, verifique la columna '
            response.variable = expectedColumns[index]

            return response
          }
          // Incrementar el índice
          index++
        }
      }
      // Validar el número de líneas del archivo CSV
      else if (data.length > maxLines) {
        // Si el número de líneas supera el máximo, asignar false a success y un mensaje de error
        this.isInvalidFormat = true
        response.success = false
        response.type = 1
        response.message =
          'El archivo CSV supera el límite de líneas permitidas. Por favor, reduzca el tamaño del archivo.'

        return response
      }

      if (csvColumnsNumber !== expectedColumnsNumber) {
        // Si el número de columnas no coincide, asignar false a success y un mensaje de error
        this.isInvalidFormat = true
        response.success = false
        response.type = 3
        response.variable = expectedColumnsNumber

        response.message =
          'El archivo CSV tiene un número de columnas distinto al esperado. Por favor, verifique que el archivo tenga '

        return response
      }

     /// console.log(this.isInvalidFormat)
      // Retornar el objeto response
      return response
    },
  },
}
</script>

<style scoped>
</style>

