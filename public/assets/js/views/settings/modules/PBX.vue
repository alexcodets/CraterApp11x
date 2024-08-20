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
        </sw-page-header>
      <sw-table-component
        ref="serversTable"
        :show-filter="false"
        :data="fetchData"
        table-class="table"
      >

        <sw-table-column
          :label="$t('settings.customization.modules.server_label')"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.customization.modules.server_label') }}</span>
              {{ row.server_label }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('settings.customization.modules.hostname')"
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
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
  TrashIcon,
  PlusSmIcon,
  EyeIcon
} from '@vue-hero-icons/solid'
const { required, minLength, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    draggable,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
    TrashIcon,
    PlusSmIcon,
    EyeIcon
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
    /* this.loadPbxServers() */
    // this.fetchInitDataCountry()
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }

  },
  mounted() {
    this.$v.formData.$reset()
  },
  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('modules', ['fetchServers','deletePbxServer']),
   
    async countrySeleted(val) {
      let res = await window.axios.get('/api/v1/states/' + val.code)
      if (res) {
        this.states = res.data.states
      }

      this.formData.countries = val

      // let isId = (element) => element.id == val.id
      // let index = this.formData.countries.findIndex(isId)

      // if (index == -1) {
      //     this.formData.countries.push(val)
      // } else {
      //     window.toastr['error']('This country was already selected')
      //     return false
      // }
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
            // this.loadPbxServers()
            return true
          }
          // window.toastr['error'](this.$t('settings.tax_types.already_in_use'))
        }
      })
    },

    // metodo para remover registro
    async deletePbxServere(id){
      let response = {}
      // endpoint
      /* let res = await window.axios.post(
        '/api/v1/pbx/servers/delete/' + id
      ) */
      let res = await this.deletePbxServer(id);
      if (res) {
        response = {
          data: {
            success: true
          }
        }
      }
      
      return response
    }

  },
}
</script>

<style scoped>
</style>