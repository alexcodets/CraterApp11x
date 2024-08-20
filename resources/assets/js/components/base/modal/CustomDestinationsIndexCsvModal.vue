<template>
  <form action="" @submit.prevent="EventSubir">
    <div v-if="!banCsv">
      <div class="py-4 px-6 form-group-row">
        <h3>{{ $t('corePbx.internacional.text_csv') }}</h3>
      </div>

      <div class="py-4 px-6 form-group-row">
        <div class="col-sm-10">
          <input type="file" id="file" ref="file" v-on:change="handleFileUpload()" accept=".XLSX, .CSV"
                 class="form-control mb-2">
          <!-- <a
             class="relative my-0 ml-0 mr-6 text-sm font-medium cursor-pointer text-primary-500"
             @click="exampleArch"
           >
             {{ $t('general.deselect') }}
           </a> -->
          <download-csv
            class="relative my-0 ml-0 text-sm font-medium cursor-pointer text-primary-500 inline"
            :data="json_data"
          >
          {{ $t('expenses.download_exmaple') }}
          </download-csv>
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

      <br>
      <div class="pt-4 pb-40 px-6">
        <!-- <div class="flex my-4"> -->
        <!-- <div class="relative w-9">
          <sw-checkbox
              v-model="import_existing"
              class="absolute"
              @change="setExisting"
              tabindex="5"
          />
        </div> -->

        <div>
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            <!--  {{ $t('items.no_taxable') }} -->
          </p>
        </div>
        <!-- </div> -->

        <sw-input-group
          class="font-normal"
        >
          <sw-radio
            :label="$t('corePbx.custom_did_groups.import_to_existing_group')"
            v-model="formData.type_group"
            size="sm"
            name="filter"
            value="existing_group"
            @change="'onSearch'"
          />
        </sw-input-group>

        <sw-input-group
          v-if="formData.type_group === 'existing_group'"
          :error="idError"
          class="my-4"
        >
          <sw-select
            :disabled="import_new"
            v-model="formData.prefixrate_groups_id"
            :invalid="$v.formData.prefixrate_groups_id.$error"
            :options="group"
            :searchable="true"
            :show-labels="false"
            :tabindex="16"
            :allow-empty="true"
            :placeholder="$t('general.select_category')"
            label="name"
            track-by="id"
            @input="$v.formData.prefixrate_groups_id.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          class="font-normal"
        >
          <sw-radio
            :label="$t('corePbx.custom_did_groups.import_to_new_group')"
            v-model="formData.type_group"
            size="sm"
            name="filter"
            value="new_group"
            @change="'onSearch'"
          />
        </sw-input-group>

        <div v-if="formData.type_group === 'new_group'">
          <sw-input-group
            :error="groupNameError"
            class="my-4"
            required
          >
            <sw-input
              v-model="formData.prefix_group_name"
              :invalid="$v.formData.prefix_group_name.$error"
              class="mt-2"
              :placeholder="'Destination group name'"
              type="text"
              name="name"
              @input="$v.formData.prefix_group_name.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            class="mb-4"
            :error="groupTypeError"
            required
          >
            <sw-select
              :invalid="$v.formData.prefix_group_type.$error"
              v-model="formData.prefix_group_type"
              :options="types"
              :searchable="true"
              :show-labels="false"
              :allow-empty="true"
              :placeholder="$t('corePbx.custom_did_groups.select_a_type')"
              class="mt-2"
              label="name"
              @input="$v.formData.prefix_group_type.$touch()"
            />
          </sw-input-group>
        </div>

        <!-- <sw-select
          :disabled="import_new"
          v-model="formData.prefixrate_groups_id"
          :invalid="$v.formData.prefixrate_groups_id.$error"
          :options="group"
          :searchable="true"
          :show-labels="false"
          :tabindex="16"
          :allow-empty="true"
          :placeholder="$t('general.select_category')"
          label="name"
          track-by="id"
        /> -->
      </div>
      <!-- <div class="py-4 px-6">
       <div class="flex my-4">
          <div class="relative w-9">
              <sw-checkbox
                  v-model="import_new"
                  class="absolute"
                  @change="setNew"
                  tabindex="5"
              />
          </div>

          <div>
              <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                   {{ $t('items.no_taxable') }} 
                  Import to New Group
              </p>
          </div>
        </div>
        <sw-input
          ref="name"
          :disabled="import_existing"
          v-model="formData.name"
          type="text"
        />
      </div> -->
    </div>

    <div v-if="banCsv">
      <!--<table class="w-full item-table bg-white border border-gray-200 border-solid">
        <colgroup>
         
          <col style="width: 3%"/>
          <col style="width: 3%"/>
          <col style="width: 3%"/>
          <col style="width: 25%"/>
          <col style="width: 22%"/>
          <col style="width: 22%"/>
          <col style="width: 22%"/>
        </colgroup>

        <tbody>
        <tr class="py-3">
          <td class="px-5">

          </td>
          <td class="px-5">
            <sw-select
              v-model="formData.prefijo"
              :options="prefijo"
              :searchable="true"
              :show-labels="false"
              :tabindex="16"
              :allow-empty="true"
              label="text"
              track-by="value"
            />
          </td>
          <td>

            <sw-select
              v-model="formData.name"
              :options="name"
              :searchable="true"
              :show-labels="false"
              :tabindex="16"
              :allow-empty="true"
              label="text"
              track-by="value"
            />
          </td>
          <td class="px-5">
            <sw-select
              v-model="formData.rate_per_minutes"
              :options="rate_per_minutes"
              :searchable="true"
              :show-labels="false"
              :tabindex="16"
              :allow-empty="true"
              label="text"
              track-by="value"
            />
          </td>
          <td class="pr-5">
            <sw-select
              v-model="formData.category"
              :options="category"
              :searchable="true"
              :show-labels="false"
              :tabindex="16"
              :allow-empty="true"
              :placeholder="$t('general.select_category')"
              label="text"
              track-by="value"
            />
          </td>
        </tr>
        </tbody>
      </table>
      -->

      <div v-if="banCsv" style="overflow-y:scroll; height:500px;">
        <table class="w-full item-table bg-white border border-gray-200 border-solid">
          <colgroup>
            <!-- <col style="width: 26%" />
            <col style="width: 22%" />
            <col style="width: 26%" />
            <col style="width: 26%" /> -->
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
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span>
                  {{ this.formData.listCsv[0][0] }}
                </span>
              </th>
              
              <th
                class="py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span>
                  {{ this.formData.listCsv[0][1] }}
                </span>
              </th>
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span>
                  {{ 'from / to' }}
                </span>
              </th>              
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span>
                  {{ this.formData.listCsv[0][4] }}
                </span>
              </th>
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span>
                  {{ this.formData.listCsv[0][5] }}
                </span>
              </th>
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span>
                  {{ this.formData.listCsv[0][6] }}
                </span>
              </th>
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span>
                  {{ this.formData.listCsv[0][7] }}
                </span>
              </th>
            </tr>
          </thead>

          <tbody>
          <!-- <tr class="py-3" v-for="(item,index) in this.listCsv" :key="index"> -->
          <tr v-for="(item,index) in this.formData.listCsv" :key="index" class="py-3">
            <td v-if="index>0" class="px-5 py-4 text-center">
              {{ index }}
            </td>
            <td v-if="index>0" class="px-5 py-4 text-center">
              {{ item[0] == 'P' ? 'Prefix' : 'From / To' }}
            </td>
            <td v-if="index>0" class="px-5 py-4 text-center">
              {{ item[1] }}
            </td>
            <td v-if="index>0" class="px-5 py-4 text-center">
              <span class="whitespace-nowrap">{{ `${item[2] || '' }${item[3] ? ' / ':''}${item[3] || ''}` }}</span>
            </td>
            <td v-if="index>0" class="px-5 py-4 text-center">
              {{ item[4] }}
            </td>
            <td v-if="index>0" class="px-5 py-4 text-center">
              {{ item[5] }}
            </td>
            <td v-if="index>0" class="px-5 py-4 text-center">
              {{ showCategory(item[6]) }}
            </td>
            <td v-if="index>0" class="px-5 py-4 text-center">
              {{ item[7] }}
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
            ? 'Save'
            : 'Next'
        }}

        <arrow-right-icon v-if="!this.banCsv" class="h-5 ml-2 -mr-1"/>
      </sw-button>
    </div>
    <!--
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
          class="flex my-4 mx-6  w-50"
        >
          <save-icon class="mr-2 -ml-1" />
                  {{
                    this.banCsv
                      ? 'Save'
                      : 'Next'
                  }}
            <arrow-right-icon class="h-5 ml-2 -mr-1" />
        </sw-button> -->

    <!-- <button v-on:click="EventSubir()" class="btn btn-primary">Subir</button> -->
  </form>

</template>

<script>
import { mapActions, mapGetters } from 'vuex'
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
      isInvalidFormat: false,
      json_data: [
        {
          'type': 'P',
          'prefix': '12345',
          'from': '',
          'to': '',
          'name': 'New Test 1',
          'rate': '0.9',
          'category': 'C',
          'country': 'US',
        },
        {
          'type': 'P',
          'prefix': '67890',
          'from': '',
          'to': '',
          'name': 'New Test 2',
          'rate': '8',
          'category': 'T',
          'country': 'US',
        },
        {
          'type': 'FT',
          'prefix': '',
          'from': '123',
          'to': '456',
          'name': 'New Test 3',
          'rate': '8',
          'category': 'T',
          'country': 'AF',
        }
      ],
      file:'',
      banCsv:false,
      banCargArch:false,
      isEdit: false,
      isLoading: false,
      group:[],
      typeArch:'',
      name1: '',
      prefix: '',
      rate:'',
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
        listCsv:[],
        status:'A',
        prefixrate_groups_id:'',
        prefijo: { value: 'prefix', text: 'Prefix' },
        name: { value: 'name', text: 'Name' },
        category: { value: 'category', text: 'Category' },
        rate_per_minutes: { value: 'rate_per_minute', text: 'Rate' },
        country_id: null,
        type_group: 'existing_group',
        prefix_group_name: null,
        prefix_group_type: null,
      },
      types: [
        { name: 'Inbound', value: 'Inbound' },
        { name: 'Outbound', value: 'Outbound' },
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
    formData: {
      prefixrate_groups_id: {
        required: requiredIf(function () {
          return this.formData.type_group === 'existing_group'
        }),
      },
      prefix_group_name: {
        required: requiredIf(function () {
          return this.formData.type_group === 'new_group'
        }),
        minLength: minLength(3),
        maxLength: maxLength(120),
      },
      prefix_group_type: {
        required: requiredIf(function () {
          return this.formData.type_group === 'new_group'
        }),
      },
    },
  },
  async mounted() {
      let response = await this.fetchPrefixGroups({ limit: 'all' })
      this.group=[...response.data.prefixGroups.data]
    /* if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    } */
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('internacionalrate', ['fetchInternacionals','CargarCsvDestination','ImportarCsvDestination','ExportCsvDestination','updateInternacional','addInternacional']),
    ...mapActions('prefixGroup', [
      'fetchPrefixGroups',
    ]),
    ...mapActions('payment', ['addPaymentMode', 'updatePaymentMode']),
    resetFormData() {
      this.formData = {
        id: null,
        name: null,
      }
      this.$v.formData.$reset()
    },

    showCategory(category){
       const categorysOptions = {
          'C': 'Custom',
          'I': 'International',
          'T': 'Toll Free'
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
     presionar(){
       this.CargarCsvDestination()
     },
    setNew() {
            if (this.import_new) {
                this.import_existing = false;
            }else{
                this.import_existing = true;
            }
     },
      /* Prueba Cvs */
      async EventSubir(){
        //console.log("EventSubir");
          if(!this.banCsv && this.file){
            //console.log("EventSubir 2");
            this.$v.formData.$touch()
            if (this.$v.$invalid) {
                return true
            }

            //console.log("EventSubir 3");
              this.typeArch = this.file.name.split('.')
              this.typeArch = this.typeArch[1]
              let formData = new FormData()
              //console.log("EventSubir 4");
              formData.append('file', this.file);
              //console.log("EventSubir 5");

              let response = await this.ImportarCsvDestination(formData)

              // Validate Csv Format
              this.validateCsv(response.data.result[0])
              if(this.isInvalidFormat)
              {                
                this.file = this.$refs.file.value= ""
                this.banCargArch= false
                this.isInvalidFormat = false                
                window.toastr['error']("The imported file format is invalid")                
                return 
              }
              //

              if(this.typeArch === 'xlsx'){
               // console.log("xlsx")
              this.formData.listCsv=[...response.data.result[0]]
              }else{
                //console.log("else")
                if(response.data.result[0][0].length===1){
                  let vect = [],aux=[]
                  for (let i = 0; i < response.data.result[0].length; i++) {
                    aux = response.data.result[0][i][0].split(';');
                    vect.push(aux)
                  }
                  this.formData.listCsv=[...vect]
                }else{                 
                  this.formData.listCsv=[...response.data.result[0]]
                }  
              }
              //console.log("EventSubir 6");
              this.banCsv = true
          }else if(this.banCsv){
             /* prefijo */
            if(this.formData.prefijo.value===this.formData.name.value){
                window.toastr['error']("the selected must be unique")
                return false
            } 
            if(this.formData.prefijo.value===this.formData.rate_per_minutes.value){
              window.toastr['error']("the selected must be unique")
              return false
            }
            if(this.formData.prefijo.value===this.formData.category.value){
              window.toastr['error']("the selected must be unique")
              return false
            }
            /* name */
            if(this.formData.name.value===this.formData.prefijo.value){
                  window.toastr['error']("the selected must be unique")
                  return false
            } 
            if(this.formData.name.value===this.formData.rate_per_minutes.value){
              window.toastr['error']("the selected must be unique")
              return false
            }
            if(this.formData.name.value===this.formData.category.value){
              window.toastr['error']("the selected must be unique")
              return false
            }
            /* rate */
            if(this.formData.rate_per_minutes.value===this.formData.name.value){
                window.toastr['error']("the selected must be unique")
                return false
            } 
            if(this.formData.rate_per_minutes.value===this.formData.prefijo.value){
              window.toastr['error']("the selected must be unique")
              return false
            }
            if(this.formData.rate_per_minutes.value===this.formData.category.value){
              window.toastr['error']("the selected must be unique")
              return false
            }
            /* category */
            if(this.formData.category.value===this.formData.name.value){
                window.toastr['error']("the selected must be unique")
                return false
            } 
            if(this.formData.category.value===this.formData.prefijo.value){
              window.toastr['error']("the selected must be unique")
              return false
            }
            if(this.formData.category.value===this.formData.rate_per_minutes.value){
              window.toastr['error']("the selected must be unique")
              return false
            }
            
           let bandera=0,pref,nam,rate_p,cat,sentencia;
            
            for (let j = 0; j < 4; j++) {
              if(j===0){
                  if(this.formData.prefijo.value==="prefix"){
                    pref=0
                  }else if(this.formData.prefijo.value==="name"){
                    pref=1
                  }else if(this.formData.prefijo.value==="rate_per_minute"){
                    pref=2
                  }else if(this.formData.prefijo.value==="category"){
                    pref=3
                  }
              }
              if(j===1){
                  if(this.formData.name.value==="prefix"){
                    nam=0
                  }else if(this.formData.name.value==="name"){
                    nam=1
                  }else if(this.formData.name.value==="rate_per_minute"){
                    nam=2
                  }else if(this.formData.name.value==="category"){
                    nam=3
                  }
              }
              if(j===2){
                  if(this.formData.rate_per_minutes.value==="prefix"){
                    rate_p=0
                  }else if(this.formData.rate_per_minutes.value==="name"){
                    rate_p=1
                  }else if(this.formData.rate_per_minutes.value==="rate_per_minute"){
                    rate_p=2
                  }else if(this.formData.rate_per_minutes.value==="category"){
                    rate_p=3
                  }
              }
              if(j===3){
                  if(this.formData.category.value==="prefix"){
                    cat=0
                  }else if(this.formData.category.value==="name"){
                    cat=1
                  }else if(this.formData.category.value==="rate_per_minute"){
                    cat=2
                  }else if(this.formData.category.value==="category"){
                    cat=3
                  }
              }
                 
              /* for (let index = 0; index < this.formData.listCsv.length; index++) {
                  if(index>0){
                    if(j===0){
                      if(!/^([0-9*|A-Za-z *|#|+]+)$/.test(this.formData.listCsv[index][0])){
                        window.toastr['error']("Error in row "+ (index) + " column 1. Numbers, letters, blank space and  special characters (* # +)")
                        bandera=1
                        break
                      }
                    }
                    if(j===2){
                      if(!Number.isInteger(parseInt(this.formData.listCsv[index][2]))){
                        window.toastr['error']("Error in row "+ (index) +" column 3.Numbers")
                        bandera=1
                        break
                      }
                    }
                    if(j===3){ 
                      if(!Number.isInteger(parseInt(this.formData.listCsv[index][3])) || this.formData.listCsv[index][3]<1 || this.formData.listCsv[index][3]>3){
                        window.toastr['error']("Error in row "+ (index)+" column 4.Numbers beetwing 1 in 3")
                        bandera=1
                      break
                      }
                    }
                  }
              } */
            }
            /* if(bandera===1){
              return false
            } */
            this.formData.listCsv.shift()

            let ar=[]
            this.formData.name=this.formData.name.value
            this.formData.rate_per_minutes=this.formData.rate_per_minutes.value
            this.formData.prefijo=this.formData.prefijo.value
            this.formData.category=this.formData.category.value
            ar.push(this.formData.prefixrate_groups_id)
            this.formData.prefixrate_groups_id=ar
           /*  console.log("Form",this.formData) */

            let res           
            res = await this.addInternacional(this.formData);
            window.toastr['success'](res.data.success);
            this.refreshData ? this.refreshData() : ''
            this.closePaymentModeModal()
            /* this.$router.push('/admin/corePBX/billing-templates/international-rate')
            return true */
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
        exampleArch(){
            this.ExportCsvDestination()
        },
    
    closePaymentModeModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },

    validateCsv(data){
      if(data[0][0] != "type"
         || data[0][1] != "prefix"
         || data[0][2] != "from"
         || data[0][3] != "to"
         || data[0][4] != "name"
         || data[0][5] != "rate"
         || data[0][6] != "category"
         || data[0][7] != "country")
      {
        this.isInvalidFormat = true                
      }
    }
  },
}
</script>

<style scoped>

</style>

