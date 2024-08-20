<template>
  <sw-card variant="setting-card">
    <!-- Base  -->
    <base-page v-if="isSuperAdmin" class="">
        <!-- Header  -->
        <sw-page-header
          class="mb-3"
          :title="$t('settings.customization.modules.manage_host')"
        >
          <sw-breadcrumb slot="breadcrumbs">
            <sw-breadcrumb-item
              to="/admin/dashboard"
              :title="$t('general.home')"
            />
            <sw-breadcrumb-item
              to="/admin/settings/modules"
              :title="$t('settings.customization.modules.title')"
            />
            <sw-breadcrumb-item
              to="#"
              :title="$t('settings.customization.modules.edit_module')"
              active
            />
          </sw-breadcrumb>
          <!-- BOTON LOGS -->
          <template slot="actions">
            <sw-button
              tag-name="router-link"
              to="pbx/jobs/logs"
              size="lg"
              variant="primary"
              class="ml-4"
            >
              <eye-icon class="h-6 mr-1 -ml-2 font-bold" />
              {{ $t('settings.customization.modules.view_log') }}
            </sw-button>
          </template>

          <template slot="actions">
            <sw-button
              tag-name="router-link"
              to="pbx/addrow"
              size="lg"
              variant="primary"
              class="ml-4"
            >
              <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
              {{ $t('settings.customization.modules.add_server') }}
            </sw-button>
          </template>

          <template slot="actions">
            <sw-dropdown class="ml-4" variant="primary">
              <sw-button slot="activator">
                <dots-horizontal-icon class="h-5 -ml-1 -mr-1" style="height: 25px;"/>
              </sw-button>

              <sw-dropdown-item
                :to="`/admin/settings/add-ons`"
                tag-name="router-link"
              >
                <credit-card-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('settings.customization.modules.add_ons') }}
              </sw-dropdown-item>

              
            </sw-dropdown>
            
          </template>

        </sw-page-header>

      <!-- table -->
      <sw-table-component
        ref="serversTable"
        :show-filter="false"
        :data="fetchData"
        table-class="table"
      >

        <sw-table-column
          :label="$t('settings.customization.modules.server_label')"
          show="server_label"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.customization.modules.server_label') }}</span>
              {{ row.server_label }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('settings.customization.modules.hostname')"
          show="hostname"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.customization.modules.hostname') }}</span>
            <span v-if="row.hostname" v-html="row.hostname">
              {{ row.hostname }}
            </span>
            <span v-else>
              {{ $t('tax_groups.empty') }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('settings.customization.modules.status_server')"
          show="status"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.customization.modules.status_server') }}</span>
           
            <sw-badge
              v-if=" row.status == 'A' " 
              :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
              :color="$utils.getBadgeStatusColor(row.status).color"
              class="px-3 py-1"
            >
            {{ $t('settings.customization.modules.server_online')}}
            </sw-badge>

            <sw-badge
              v-if=" row.status == 'I' " 
              :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
              :color="$utils.getBadgeStatusColor(row.status).color"
              class="px-3 py-1"
            >
            {{ $t('settings.customization.modules.server_offline')}}
            </sw-badge>            

          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('tax_groups.action') }} </span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                :to="`pbx/${row.id}/edit`"
                tag-name="router-link"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                @click="testConection(row.id)"
              >
                <shield-check-icon class="h-5 mr-3 text-gray-600" />
                <!-- {{ $t('general.edit') }} -->
                {{ $t('settings.customization.modules.test_conection')}}
              </sw-dropdown-item>

              <sw-dropdown-item
                @click="removePbxServer(row.id)"
              >
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.delete') }}
              </sw-dropdown-item>

            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </base-page>
  </sw-card>
</template>

<script>
import draggable from 'vuedraggable'
import { mapActions, mapGetters } from 'vuex'
import {
  DotsHorizontalIcon,
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
  TrashIcon,
  PlusSmIcon,
  EyeIcon,
  ShieldCheckIcon 
} from '@vue-hero-icons/solid'
const { required, minLength, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    DotsHorizontalIcon,
    draggable,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
    TrashIcon,
    PlusSmIcon,
    EyeIcon,
    ShieldCheckIcon
  },
  data() {
    return {
      isLoading: false,
      title: 'Add Tax Group',
      pbxServers: [],
      countries: [],
      states: [],
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

      formData: {
        name: '',
        description: '',
        status: {
          value: 'A',
          text: 'Active',
        },
        country_id: '',
        state_id: '',
        country_name: '',
        state_name: '',
        countries: [],
        states: [],
      },
    }
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(1),
        maxLength: maxLength(255),
      },

      description: {
        maxLength: maxLength(255),
      },
    },
  },
  computed: {
    ...mapGetters('user', ['currentUser']),

    ...mapGetters('company', ['defaultCurrency']),

    ...mapGetters('modules', ['modules']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    pageTitle() {
      if (this.$route.name === 'tax-groups.edit') {
        return this.$t('tax_groups.edit_tax_group')
      }
      return this.$t('tax_groups.new_tax_group')
    },

    isEdit() {
      if (this.$route.name === 'tax-groups.edit') {
        return true
      }
      return false
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }

      if (!this.$v.formData.name.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },

    descriptionError() {
      if (!this.$v.formData.description.$error) {
        return ''
      }

      if (!this.$v.formData.description.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
  },
  created() {
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }

  },
  mounted() {
    this.$v.formData.$reset()
  },
  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('modules', ['fetchServers','deletePbxServer', 'testServerConection']),
   
    async countrySeleted(val) {
      let res = await window.axios.get('/api/v1/states/' + val.code)
      if (res) {
        this.states = res.data.states
      }

      this.formData.countries = val
    },

    async stateSeleted(val) {
      this.formData.states = val
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

       let response = await this.fetchServers(data);
      return {
        data: response.data.pbxServers.data || {},
        pagination: {
          totalPages: response.data.pbxServers.last_page,
          currentPage: page,
          count: response.data.pbxServers.total,
        },
      }
    },

    async submitTaxGroup() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.formData.status = this.formData.status.value

      if (this.taxGroupLeft.length > 0) {
        this.formData.taxGroupLeft = this.taxGroupLeft
      }

      if (this.formData.countries) {
        this.formData.country_id = this.formData.countries.id
      }

      if (this.formData.states) {
        this.formData.state_id = this.formData.states.id
      }

      try {
        let response
        this.isLoading = true
        if (this.isEdit) {
          response = await this.updateTaxGroup(this.formData)
          if (response.status === 200) {
            window.toastr['success'](this.$t('tax_groups.updated_message'))
            this.$router.push('/admin/settings/tax-groups')
          }
          if (response.data.error) {
            window.toastr['error'](response.data.error)
          }
        } else {
          response = await this.addTaxGroup(this.formData)
          if (response.status === 200) {
            window.toastr['success'](this.$tc('tax_groups.created_message'))
            this.$router.push('/admin/settings/tax-groups')
          }
          if (response.data.error) {
            window.toastr['error'](response.data.error)
          }
        }

        this.isLoading = false
        return true
      } catch (err) {}
    },

    async testConection(serverId = null){
      if (serverId){
        let res = await this.testServerConection(serverId);
        // console.log(res)
        if (res.success){
          window.toastr['success']("Server is alive")
        
        } else {
          window.toastr['error']("Server not found")
        }
      }

      this.$refs.serversTable.refresh()  

    },

    // metodo para confirmar remover registro
    async removePbxServer(id){
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.customization.modules.server_confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          // metodo para remover registro
          let response = await this.deletePbxServere(id)
          if (response.data.success) {
            window.toastr['success'](
              this.$t('settings.customization.modules.server_deleted_message')
            )
            // actualizar listado
            this.$refs.serversTable.refresh()           
            return true
          }
       
        }
      })
    },

    // metodo para remover registro
    async deletePbxServere(id){
      try {
        let response = {}        
        let res = await this.deletePbxServer(id);
        if (res) {
          response = {
            data: {
              success: true
            }
          }
        }
        return response
      } catch (err) {
      //  window.toastr['error'](err.message)
      }
    },

  },
}
</script>

<style scoped>
</style>