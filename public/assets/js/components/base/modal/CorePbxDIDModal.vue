<template>
  <form action="">
    <sw-table-component
      ref="table"
      :data="fetchData"
      :show-filter="false"
      table-class="table"
    >
      <sw-table-column
        :sortable="true"
        :label="$t('did.item.did_id')"
        show="hg"
      >
        <template slot-scope="row">
          <span>{{ $t('did.item.did_id') }}</span>
          {{ row.id }}
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :label="$t('did.item.name')"
        show="name"
      />

      <sw-table-column
        :sortable="true"
        :label="$t('did.item.status')"
        show="status"
      >
        <template slot-scope="row">
          {{ row.status == null ? 'No status' : row.status }}
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :label="$t('did.item.rate')"
        show="did_rate"
      />

      <sw-table-column
        :sortable="true"
        :filterable="false"
        cell-class="action-dropdown"
      >
        <template slot-scope="row">
          <span> {{ $t('packages.action') }} </span>
          <sw-dropdown>
            <dot-icon slot="activator" />
            <sw-dropdown-item @click="ConfirmDID(row.id)">
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
} from '@vue-hero-icons/solid'

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
      filters: {
        name: '',
        status: '',
        did_rate: '',
      },
      pbxService: {},
      parameters: {},
      parametersTenant: {},
      pbxDID: {},
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
    ...mapGetters('did', ['did', 'totalDID', 'selectedDID', 'selectAllField']),

    ...mapGetters('customer', [
      'extensions',
      'corePbxServicesParameters',
      'selectedPbxDID',
      'selectAllFieldExtensions',
      'pbxServiceSaved',
      'pbxDIDSaved',
      'pbxTenantSaved'
    ]),

    selectField: {
      get: function () {
        return this.selectedDID
      },
      set: function (val) {
        this.selectDID(val)
      },
    },
    selectAllFieldStatus: {
      get: function () {
        return this.selectAllField
      },
      set: function (val) {
        this.setSelectAllState(val)
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
     this.loadData()
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),

    ...mapActions('did', [
      'deleteDID',
      'selectDID',
      'fetchDIDs',
      'selectAllDID',
      'setSelectAllState',
    ]),

    ...mapActions('customer', ['fetchDIDPBX', 'fetchPbxDidBD', 'addPbxDid', 'addPbxServiceDid']),

    async loadData() {
        this.isRequestOnGoing = true
        this.pbxService = this.pbxServiceSaved
       
        this.parameters = this.corePbxServicesParameters.parameters
        this.parametersTenant = this.corePbxServicesParameters.parameters.tenant;
        this.pbxTenantBd = this.pbxTenantSaved;
        

        this.isRequestOnGoing = false
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
        search: this.filters.name !== null ? this.filters.name : '',
        status: this.filters.status !== null ? this.filters.status : '',
        description:
          this.filters.did_rate !== null ? this.filters.did_rate : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      this.isRequestOngoing = true
      let response = await this.fetchDIDs(data)
      this.isRequestOngoing = false
      return {
        data: response.data.profileDID || {},
        pagination: {
          currentPage: page,
        },
      }
    },

    /*CONSULTAR DID POR TENANT*/
    async fetchDataDID() {
      this.parameters = this.corePbxServicesParameters.parameters
      //
      let params = {
        pbx_package_id: this.parameters.pbx_package_id,
        pbx_tenant_id: this.parameters.tenant_api_id,
      }
      this.isRequestOngoing = true
      let response = await this.fetchDIDPBX(params)
      this.isRequestOngoing = false

      let SelectDID = this.selectedPbxDID

      let didPbx = []
      for (const property in response.data.DIDByTenantList) {
        for (let i = 0; i < SelectDID.length; i++) {
          const element = SelectDID[i]
        
          if (property === element) {
            didPbx.push({
              id: property,
              e164: response.data.DIDByTenantList[property].e164,
              e164_2: response.data.DIDByTenantList[property].e164_2,
              ext: response.data.DIDByTenantList[property].ext,
              number: response.data.DIDByTenantList[property].number,
              number2: response.data.DIDByTenantList[property].number2,
              server: response.data.DIDByTenantList[property].server,
              status: response.data.DIDByTenantList[property].status,
              type: response.data.DIDByTenantList[property].type,
              trunk: response.data.DIDByTenantList[property].trunk,
            })
          }
        }
      }
      return didPbx
    },


    // saving pbx did
    async savePbxDID(data, pbx_profile_did_id) {      
      // consultar did en bd
      let pbxDidBd = await this.fetchPbxDidBD(data);
      // verificar si existe
      if (Object.entries(pbxDidBd).length === 0) {
        // registrar
        await this.addPbxDid(data);
        this.pbxDID = this.pbxDIDSaved;
      
      } else {
        this.pbxDID = pbxDidBd[0];

      }
   

      let dataPbxServiceDid = {
        pbx_profile_did_id: pbx_profile_did_id,
        pbx_did_id: this.pbxDID.id,
        pbx_service_id: this.pbxService.id
      }

      let resPbxServiceDid = await this.addPbxServiceDid(dataPbxServiceDid);
     
      if (resPbxServiceDid.data.success) {
        window.toastr['success'](resPbxServiceDid.data.message);
      }

    },

    /* CONFIRMAR DID / INSERT PBX SERVICES*/
    async ConfirmDID(row) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('corePbx.did.confirm'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (Confirm) => {
        if (Confirm) {
          /*DID PBXServices Selecteds*/
          this.isRequestOngoing = true
          let pbx_did = await this.fetchDataDID()
     
          // pbx did body request
          pbx_did.forEach(element => {
            let dataPbxDid = {
              did_number: element.number,
              did_server: element.server,
              did_status: element.status,
              did_details: element,
              pbx_tenant_id: this.pbxTenantBd[0].id
             /*  tenant_name: this.parametersTenant.name,
              tenant_code: this.parametersTenant.tenantcode,
              tenant_details: this.parametersTenant */
            };

            this.savePbxDID(dataPbxDid, row);

          });


          this.isRequestOngoing = false         
          this.$refs.table.refresh()
          return true

          /* 1.Inserción recorriendo for por cada DIDPbx */
          // this.selectedPbxExtensions
          /*2.Inserción de DIDPbx API*/
          /*3.Obtención de ID de DIDPbx*/
          /*4.Inserción recorriendo for por cada DIDPbx al DID-Profile*/

          //let res = await this.SetdidPbxServices

          /* CODIGO GUÍA --vvv--
          let res = await this.deleteExtension(id)
          if (res.data.success) {
            window.toastr['success'](
              this.$tc('corePbx.extensions.success_confirm', 1)
            )
            this.$refs.table.refresh()
            return true
          }
          if (res.data.error === 'user_attached') {
            window.toastr['error'](
              this.$tc('packages.user_attached_message'),
              this.$t('general.action_failed')
            )
            return true
          }
          window.toastr['error'](res.data.message)
          return true */
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
