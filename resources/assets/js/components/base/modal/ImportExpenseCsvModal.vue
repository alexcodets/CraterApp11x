<template>
  <form v-if="!isLoadingData" action="" @submit.prevent="EventSubir">
    <div v-if="!banCsv">
      <div class="py-4 px-6 form-group-row">
        <div class="col-sm-10">
          <input type="file" id="file" ref="file" v-on:change="handleFileUpload()" accept=".XLSX, .CSV"
                 class="form-control">
          <download-csv
            class="relative my-0 ml-0 text-sm font-medium cursor-pointer text-primary-500 inline"
            :data="json_data"
            filename="exampleData"
          >
          {{ $t('expenses.download_exmaple') }}
          </download-csv>
          <sw-button
            v-if="banCargArch"
            class="flex mr-2 w-50 mt-2"
            variant="primary"
            type="button"
            @click="cancelLoadArch"
          >
            {{ $t('general.remove') }}
          </sw-button>
        </div>
      </div>

      <br>
      </div>
    </div>

    <div v-if="banCsv">
      <div v-if="banCsv" style="overflow-y:scroll; height:500px;">
        <table class="w-full item-table bg-white border border-gray-200 border-solid">
          <colgroup>
            <col style="width: 3%"/>
            <col style="width: 3%"/>
            <col style="width: 3%"/>
            <col style="width: 25%"/>
            <col style="width: 22%"/>
            <col style="width: 22%"/>
            <col style="width: 22%"/>
          </colgroup>
          <thead>
            <tr>
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span> # </span>
              </th>
              <th v-for="(tr, indexTr) in headerColumn" class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span>
                  {{ tr }}
                </span>
              </th>
            </tr>
          </thead>

          <tbody>
          <!-- <tr class="py-3" v-for="(item,index) in this.listCsv" :key="index"> -->
          <tr v-for="(item,index) in this.formData.listCsv" :key="index" class="py-3">
            <td class="px-5 py-4 text-center">
              {{ index+1 }}
            </td>
            <td class="px-5 py-4 text-center">
              {{ item.subject }}
            </td>
            <td class="px-5 py-4 text-center">
              

              <div v-html="$utils.formatMoney(item.amount_due , defaultCurrency)" />
            </td>
            <td class="px-5 py-4 text-center">
              <span class="whitespace-nowrap">{{ item.date }}</span>
            </td>
            <td class="px-5 py-4 text-center">
     
              <div v-if="item.payment_method == 'CC'">  {{ $t('general.credit_card') }}</div>
              <div v-if="item.payment_method == 'CHECK'"> {{ $t('general.check') }}</div>
            </td>
            <td class="px-5 py-4 text-center">
              {{ item.expense_category }}
            </td>
            <td class="px-5 py-4 text-center">
              {{ item.payment_date }}
            </td>

          </tr>
          </tbody>
        </table>
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
        class="flex my-4 my-4 mr-6 ml-2  w-50"
      >
        <save-icon v-if="this.banCsv" class="mr-2 -ml-1"/>
        {{
          this.banCsv
            ? $t('general.save') 
            : $t('general.next') 
        }}

        <arrow-right-icon v-if="!this.banCsv" class="h-5 ml-2 -mr-1"/>
      </sw-button>
    </div>
  </form>
  <base-loader v-else/>
</template>

<script>
import { mapActions, mapGetters, mapState } from 'vuex'
import { VueCsvImport }  from 'vue-csv-import';
import {
  ArrowRightIcon,
} from '@vue-hero-icons/solid'
const {
  required,
  minLength,
  numeric,
  maxLength,
  minValue,
  requiredIf
} = require('vuelidate/lib/validators')

export default {
  components: {
    ArrowRightIcon,
  },
  data() {
    return {
      isLoadingData: false,
      headerColumn: [
        'subject',
        'amount_due',
        'date',
        'payment_method',
        'expense_category',
        'payment_date'
      ],
      json_data: [
        {
          'subject': 'SubjectA',
          'amount_due': '1000',
          'date': '2023-02-01',
          'payment_method': 'CC',
          'expense_category': 'Name Category 1',
          'payment_date': '2023-02-01',
        },
        {
          'subject': 'SubjectB',
          'amount_due': '1100',
          'date': '2023-02-02',
          'payment_method': 'CHECK',
          'expense_category': 'Name Category 2',
          'payment_date': '2023-02-02',
        },
        {
          'subject': 'SubjectC',
          'amount_due': '1010',
          'date': '2023-02-03',
          'payment_method': 'CC',
          'expense_category': 'Name Category 3',
          'payment_date': '2023-02-03',
        },
      ],
      file:'',
      banCsv:false,
      banCargArch:false,
      isEdit: false,
      isLoading: false,
      typeArch:'',      
      formData: {
        listCsv:[],
      },
    }
  },
  computed: {

    ...mapGetters('company', ['defaultCurrency']),

    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),
    ...mapState('expense', ['paymentMethods']),
  },
  mounted() {
    this.fetchValidPaymentMethods()
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('expense', ['saveMassiveExpenses', 'fetchValidPaymentMethods']),

    resetFormData() {
      this.formData = {
        listCsv:[],
      }
    },
      /* Prueba Cvs */
      async EventSubir(){
          if(!this.banCsv && this.file){
              this.isLoadingData = true
              let validPaymentMethods = false
              this.typeArch = this.file.name.split('.')
              this.typeArch = this.typeArch[this.typeArch.length-1]

              const file = this.file
              const reader = new FileReader()
              reader.readAsText(file)

              reader.onload = async (e) => {
                const csvData  = e.target.result

                let rows = csvData.split('\n');

                let headers = rows[0].split(',');
                // quitar los elementos vacios
                headers =  headers.filter(item => item != '\r');
                
                // quitar los espacios en blanco
                headers = headers.map(item => item.trim());
                
                const dataArray = [];

                rows = rows.filter(item => item != '');

                if(rows.length > 5001)
                {
                    this.isLoadingData = false
                    window.toastr['error'](`The number of records is greater than those that can be processed per file, maximum 5 thousand.`);
                    return false
                }

                for (let i = 1; i < rows.length; i++) {
                  let values = rows[i];

                  values =  values.split(',');
                  values = values.filter(item => item != '\r');
                      
                  if(values.length != 6)
                  {
                    window.toastr['error'](`Format error on line ${i}. Check subject and data format.`);
                    this.isLoadingData = false
                    return false
                  }

                  const obj = {};
                  for (let j = 0; j < headers.length; j++) {
                    if(!values[j]){
                      window.toastr['error'](`The row ${i} is missing data in the column ${headers[j]}`);
                      this.isLoadingData = false
                      return false;
                    }

                    // validar si si el field payment_method es CC o CHECK
                    if(headers[j] == 'payment_method'){
                      if(values[j].trim() == 'CC'){
                        // validar que exista en paymentMethods el valor C 
                      //  console.log(this.paymentMethods);
                        if(!this.paymentMethods.paymentMethods.find(item => item.account_accepted == 'C')){
                        //  console.log(280);
                          window.toastr['error'](`The row ${i} has a payment method that does not exist`);
                          if(!validPaymentMethods){
                            validPaymentMethods = true;
                          }
                          this.isLoadingData = false
                          return false;
                        }
                      }else if(values[j].trim() == 'CHECK'){
                        // validar que exista en paymentMethods el valor C 
                        if(!this.paymentMethods.paymentMethods.find(item => item.account_accepted == 'A')){
                          window.toastr['error'](`The row ${i} has a payment method that does not exist`);
                          if(!validPaymentMethods){
                            validPaymentMethods = true;
                          }
                          this.isLoadingData = false
                          return false;
                        }
                      }
                    }
                    obj[headers[j]] = values[j].trim();
                  }
                  dataArray.push(obj);
                  this.isLoadingData = false
                }

                if(validPaymentMethods){
                  this.isLoadingData = false
                  return false;
                }
                let validColumns = false;      
                this.headerColumn.forEach((item, index) => {
                  if(!headers.includes(item)){
                    window.toastr['error'](`The column ${item} is missing`);
                    if(!validColumns){
                      validColumns = true;
                    }
                  }
                });
                if(validColumns){
                  this.isLoadingData = false
                  return false;
                }

                // validar que 
                this.formData.listCsv = dataArray;
                this.banCsv = true
              }         
              
          }else if(this.banCsv){
            const res = await this.saveMassiveExpenses(this.formData);
            window.toastr['success'](this.$t('expenses.expense_massive_success'));
            this.refreshData ? this.refreshData() : ''
            this.closePaymentModeModal()
          }
        },
        handleFileUpload(){
            this.file = this.$refs.file.files[0];
            this.banCargArch=true
        },
        
        cancelLoadArch(){
          this.file = this.$refs.file.value=""
          this.banCargArch=false
        },

    
    closePaymentModeModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>

<style scoped>

</style>

