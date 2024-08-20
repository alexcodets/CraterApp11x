<template>
  <form action="">
    <sw-table-component
      ref="table"
      :data="fetchData"
      :show-filter="true"
      table-class="table"
    >
      <sw-table-column :sortable="true" :label="$t('items.name')" show="name">
        <template slot-scope="row">
          <span>{{ $t('items.name') }}</span>
          {{ row.name }}
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :label="$t('corePbx.extensions.prepaidpostpaid')"
        show="status_payment"
      >
        <template slot-scope="row">
          <span>{{ $t('corePbx.extensions.prepaidpostpaid') }}</span>

          <span>
            {{ row.status_payment ? row.status_payment : 'Not selected' }}
          </span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :filterable="false"
        cell-class="action-dropdown"
      >
        <template slot-scope="row">
          <span> {{ $t('items.action') }} </span>

          <sw-dropdown>
            <dot-icon slot="activator" />

            <sw-dropdown-item @click="ConfirmExtension(row.id)">
              <save-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.save') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </template>
      </sw-table-column>
    </sw-table-component>
    <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
      <sw-button
        class="mr-3"
        variant="primary-outline"
        type="button"
        @click="closeExtensionModal"
      >
        {{ $t('general.cancel') }}
      </sw-button>
    </div>
  </form>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, minLength } = require('vuelidate/lib/validators')
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
} from '@vue-hero-icons/solid';

export default {
  components: {
    FilterIcon,
    XIcon,
    PlusIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
  },

  data() {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        id: null,
        name: null,
      },
      id: null,
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      name: '',
      status_payment: '',
      description: '',

      filters: {
        name: '',
        status_payment: '',
        description: '',
      },
      pbxService: {},
      parameters: {},
      parametersTenant: {},
      pbxEXT: {},
      pbxTenantBd: {}
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),

    ...mapGetters('extensions', [
      'totalExtensions',
      'extensions',
      'selectedExtensions',
    ]),

    ...mapGetters('customer', [
      'extensions',
      'corePbxServicesParameters',
      'selectedPbxExtensions',
      'selectAllFieldExtensions',
      'pbxServiceSaved',
      'pbxEXTSaved',
      'pbxTenantSaved'
    ]),

    selectField: {
      get: function () {
        return this.selectedExtensions
      },
      set: function (val) {
        this.selectExtension(val)
      },
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(2),
      },
    },
  },

  async mounted() {
    // this.$refs.name.focus = true
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    }
  },

  created() {
    this.loadData();
  },
  
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),

    ...mapActions('item', ['addItemUnit', 'updateItemUnit', 'fatchItemUnit']),

    ...mapActions('extensions', [
      'fetchExtensions',
      'selectExtension',
      'deleteExtension',
    ]),

    ...mapActions('customer', ['fetchExtensionPBX', 'fetchPbxExtensionBD', 'addPbxExt', 'addPbxServiceExt']),

    async loadData(){
      this.pbxService = this.pbxServiceSaved;
      this.parametersTenant = this.corePbxServicesParameters.parameters.tenant;
      this.pbxTenantBd = this.pbxTenantSaved;

    },

    resetFormData() {
      this.formData = {
        id: null,
        name: null,
      }
      this.$v.formData.$reset()
    },

    async setData() {
      this.formData = {
        id: this.modalData.id,
        name: this.modalData.name,
      }
    },
    /*CONSULTAR DATA*/
    async fetchData({ page, filter, sort }) {
      let data = {
        name: this.filters.name !== null ? this.filters.name : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
    
      this.isRequestOngoing = true
      let response = await this.fetchExtensions(data)
      this.isRequestOngoing = false


      return {
        data: response.data.profileExtensions || {},
        pagination: {
          currentPage: page,
        },
      }
    },
    /*CONSULTAR EXTENSION POR TENANT*/
    async fetchDataExtension() {
      this.parameters = this.corePbxServicesParameters.parameters
      
      let params = {
        pbx_package_id: this.parameters.pbx_package_id,
        pbx_tenant_id: this.parameters.tenant_api_id,
      }
      this.isRequestOngoing = true
      let response = await this.fetchExtensionPBX(params)
      this.isRequestOngoing = false

      let SelectExt = this.selectedPbxExtensions
      let ExtPbx = []
      for (const property in response.data.ExtensionByTenantList) {
        for (let i = 0; i < SelectExt.length; i++) {
          const element = SelectExt[i]
          
          if (property === element) {
            ExtPbx.push({
              id: property,
              email: response.data.ExtensionByTenantList[property].email,
              ext: response.data.ExtensionByTenantList[property].ext,
              ext_id: response.data.ExtensionByTenantList[property].ext_id,
              linenum: response.data.ExtensionByTenantList[property].linenum,
              location: response.data.ExtensionByTenantList[property].location,
              macaddress:
                response.data.ExtensionByTenantList[property].macaddress,
              name: response.data.ExtensionByTenantList[property].name,
              protocol: response.data.ExtensionByTenantList[property].protocol,
              status: response.data.ExtensionByTenantList[property].status,
              ua_fullname:
                response.data.ExtensionByTenantList[property].ua_fullname,
              ua_id: response.data.ExtensionByTenantList[property].ua_id,
              ua_name: response.data.ExtensionByTenantList[property].ua_name,
            })
          }
        }
      }
      return ExtPbx
    },

    // saving pbx ext
    async savePbxEXT(data, pbx_profile_ext_id) {
      // consultar extension en bd
      let pbxExtBd = await this.fetchPbxExtensionBD(data);
      // verificar si existe
       if (Object.entries(pbxExtBd).length === 0) {
         // registrar
         await this.addPbxExt(data);
         this.pbxEXT = this.pbxEXTSaved;
       
       } else {
         this.pbxEXT = pbxExtBd[0];
       }


      let dataPbxServiceExt = {
        pbx_profile_extension_id: parseInt(pbx_profile_ext_id),
        pbx_extension_id: this.pbxEXT.id,
        pbx_service_id: this.pbxService.id
      }

      let resPbxServiceExt = await this.addPbxServiceExt(dataPbxServiceExt);
     
      if (resPbxServiceExt.data.success) {
        window.toastr['success'](resPbxServiceExt.data.message);
      }

    },
    
    /* CONFIRMAR DID / INSERT PBX SERVICES*/
    async ConfirmExtension(row) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('corePbx.did.confirm'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (Confirm) => {
        if (Confirm) {
          /*EXT PBXServices Selecteds*/
          this.isRequestOngoing = true
          let pbx_ext = await this.fetchDataExtension()
          // pbx did body request
          pbx_ext.forEach(element => {
            let dataPbxExt = {
              ext_name: element.name,
              ext_email: element.email,
              ext_status: element.status,
              ext_details: element,
              pbx_tenant_id: this.pbxTenantBd[0].id
              /* tenant_name: this.parametersTenant.name,
              tenant_code: this.parametersTenant.tenantcode,
              tenant_details: this.parametersTenant */
            };

            this.savePbxEXT(dataPbxExt, row);

          });


          this.isRequestOngoing = false         
          this.$refs.table.refresh()
          return true

          
        }
      })
    },

    closeExtensionModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>
