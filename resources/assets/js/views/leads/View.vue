<template>
  <base-page v-if="isSuperAdmin" class="item-create">
    <sw-page-header class="mb-3" :title="pageTitle">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item to="/admin/leads" :title="$tc('leads.title', 2)" />
        <sw-breadcrumb-item
          v-if="$route.name === 'leads.edit'"
          to="#"
          :title="$t('leads.title')"
          active
        />
        <sw-breadcrumb-item v-else to="#" :title="$t('leads.title')" active />
      </sw-breadcrumb>
      <template slot="actions">
        <sw-button
          tag-name="router-link"
          variant="primary-outline"
          type="button"
          size="lg"
          class="mr-3 text-sm hidden sm:flex"
          :to="`/admin/leads`"
        >
          <x-circle-icon class="w-6 h-6 mr-1 -ml-2" v-if="!isLoading" />
          {{ $t('contacts.clientgoback') }}
        </sw-button>

        <sw-button
          v-if="permissionModule2.update"
          tag-name="router-link"
          variant="primary"
          type="submit"
          size="lg"
          class="hidden sm:flex"
          :to="`/admin/leads/${$route.params.id}/edit`"
        >
          <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
          {{ $t('general.edit') }}
        </sw-button>
      </template>
    </sw-page-header>

    <sw-card variant="customer-card">
      <div class="pt-6 mt-5">
        <div class="col-span-12">
          <p class="text-gray-500 uppercase sw-section-title">
            {{ $t('providers.basic_info') }}
          </p>
          <div
            class="grid grid-cols-1 gap-4 mt-5 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1"
          >
            <div>
              <p
                class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('leads.customer_type') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.customer_type.text }}
              </p>
            </div>

            <div v-if="isCustomerBusiness">
              <p
                class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('leads.company_name') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.company_name }}
              </p>
            </div>

            <div v-if="isCustomerBusiness">
              <p
                class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('leads.primary_contact_name') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.primary_contact_name }}
              </p>
            </div>

            <div v-if="!isCustomerBusiness">
              <p
                class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('leads.first_name') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.first_name }}
              </p>
            </div>

            <div v-if="!isCustomerBusiness">
              <p
                class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('leads.last_name') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.last_name }}
              </p>
            </div>

            <div>
              <p
                class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('leads.type') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.type.text }}
              </p>
            </div>

            <div>
              <p
                class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('leads.email') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.email }}
              </p>
            </div>

            <div>
              <p
                class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('leads.phone') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.phone }}
              </p>
            </div>

            <div>
              <p
                class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('leads.status') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.status.text }}
              </p>
            </div>

            <div>
              <p
                class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('leads.last_contacted_date') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.formattedLastDate }}
              </p>
            </div>

            <div>
              <p
                class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('leads.followupdate') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.formattedFollowDate }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <sw-page-header class="mt-5" title=" ">
        <template slot="actions">
          <div class="w-full">
            <div class="col-span-12">
              <sw-button
                v-if="permissionModule.create"
                tag-name="router-link"
                :to="`/admin/leadnotes/${this.$route.params.id}/create`"
                class="mr-3"
                variant="primary"
              >
                {{ $t('customer_ticket.create_note') }}
              </sw-button>
            </div>
          </div>
        </template>
      </sw-page-header>

      <div class="relative table-container">
        <div
          class="relative flex items-center justify-between h-10 mt-5 list-none border-b-2 border-gray-200 border-solid"
        >
          <p class="text-sm"></p>
        </div>

        <sw-table-component
          ref="notes_table"
          :show-filter="false"
          table-class="table"
          :data="fetchLeadNote"
        >
          <sw-table-column
            :sortable="true"
            :label="$t('customer_ticket.reference')"
            show="leadnote_number"
          >
            <template slot-scope="row">
              <span>{{ $t('customer_ticket.reference') }}</span>
              <span class="font-medium text-primary-500 cursor-pointer">
                {{ row.leadnote_number }}
              </span>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('customer_ticket.subject')"
            show="subject"
          >
            <template slot-scope="row">
              <span>{{ $t('customer_ticket.subject') }}</span>
              {{ row.subject }}
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('customer_ticket.message')"
            show="body"
          >
            <template slot-scope="row">
              <span>{{ $t('customer_ticket.message') }}</span>
              {{ row.body }}
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('customer_ticket.user')"
            show="creator_id"
          >
            <template slot-scope="row">
              <span>{{ $t('customer_ticket.user') }}</span>
              {{ row.formattedUserName }}
            </template>
          </sw-table-column>
          
          <sw-table-column :sortable="true" :label="'Datetime'"  show="created_at">
            <template slot-scope="row">
              <span> {{ $t('general.datetime') }} </span>
              {{ row.formattedAddeDate }}
            </template>
          </sw-table-column>

          <sw-table-column cell-class="action-dropdown no-click">
            <template slot-scope="row">
              <span>{{ $t('general.actions') }}</span>

              <sw-dropdown>
                <dot-icon slot="activator" />

                <sw-dropdown-item
                 v-if="permissionModule.read"
                  :to="`/admin/leadnotes/${row.lead_id}/${row.id}/view`"
                  tag-name="router-link"
                >
                  <eye-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.view') }}
                </sw-dropdown-item>

                <sw-dropdown-item
                  v-if="permissionModule.update"
                  :to="`/admin/leadnotes/${row.lead_id}/${row.id}/edit`"
                  tag-name="router-link"
                >
                  <pencil-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.edit') }}
                </sw-dropdown-item>
                <!-- seccion para cambiar el precio -->
                <sw-dropdown-item 
                    v-if="permissionModule.delete"
                @click="deleteNote(row.id)">
                  <trash-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.delete') }}
                </sw-dropdown-item>
              </sw-dropdown>
            </template>
          </sw-table-column>
        </sw-table-component>
      </div>
    </sw-card>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import {
  EyeIcon,
  EyeOffIcon,
  TrashIcon,
  PencilIcon,
} from '@vue-hero-icons/outline'
import {
  ChevronDownIcon,
  HashtagIcon,
  ShoppingCartIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
const {
  required,
  minLength,
  email,
  numeric,
  minValue,
  maxLength,
  requiredIf,
} = require('vuelidate/lib/validators')

export default {
  components: {
    EyeIcon,
    EyeOffIcon,
    XCircleIcon,
    TrashIcon,
    PencilIcon,
    EyeIcon,
  },
  data() {
    return {
      isLoading: false,
      isBusiness: true,
      formData: {
        company_name: '',
        email: '',
        phone: '',
        customer_type: {
          text: this.$t('leads.customer_types_options.business'),
          value: 'B',
        },
        first_name: '',
        last_name: '',
        status: {
          text: this.$t('leads.status_options.active'),
          value: 'A',
        },
        type: {
          text: this.$t('leads.lead_type_options.none'),
          value: 'None',
        },
        website: '',
        primary_contact_name: '',
        id: null,
      },
      isShowPassword: false,
      lead_type_options: [
        {
          text: this.$t('leads.lead_type_options.none'),
          value: 'None',
        },
        {
          text: this.$t('leads.lead_type_options.prepaid'),
          value: 'Prepaid',
        },
        {
          text: this.$t('leads.lead_type_options.postpaid'),
          value: 'Postpaid',
        },
      ],
      customer_type_options: [
        {
          text: this.$t('leads.customer_types_options.business'),
          value: 'B',
        },
        {
          text: this.$t('leads.customer_types_options.residential'),
          value: 'R',
        },
      ],
      status_options: [
        {
          text: this.$t('leads.status_options.active'),
          value: 'A',
        },
        {
          text: this.$t('leads.status_options.completed'),
          value: 'C',
        },
      ],
      permissionModule: {
        create: false,
        update: false,
        delete: false,
        read: false,
      },
      permissionModule2: {
        create: false,
        update: false,
        delete: false,
        read: false,
      },
    }
  },

  computed: {
    ...mapGetters('user', ['currentUser']),

    isCustomerBusiness() {
      return this.formData.customer_type.value == 'B' ? true : false
    },

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    pageTitle() {
      if (this.$route.name === 'leads.edit') {
        return this.$t('leads.edit_lead')
      }
      return this.$t('leads.view')
    },

    isEdit() {
      if (this.$route.name === 'leads.edit') {
        return true
      }
      return false
    },

    companyNameError() {
      if (!this.$v.formData.company_name.$error) {
        return ''
      }
      if (!this.$v.formData.company_name.required) {
        return this.$t('validation.required')
      }
    },

    phoneError() {
      if (!this.$v.formData.phone.$error) {
        return ''
      }
      if (!this.$v.formData.phone.required) {
        return this.$t('validation.required')
      }
    },

    customerTypeError() {
      if (!this.$v.formData.customer_type.$error) {
        return ''
      }
      if (!this.$v.formData.customer_type.required) {
        return this.$t('validation.required')
      }
    },

    firstNameError() {
      if (!this.$v.formData.first_name.$error) {
        return ''
      }
      if (!this.$v.formData.first_name.required) {
        return this.$t('validation.required')
      }
    },

    lastNameError() {
      if (!this.$v.formData.last_name.$error) {
        return ''
      }
      if (!this.$v.formData.last_name.required) {
        return this.$t('validation.required')
      }
    },

    statusError() {
      if (!this.$v.formData.status.$error) {
        return ''
      }
      if (!this.$v.formData.status.required) {
        return this.$t('validation.required')
      }
    },

    typeError() {
      if (!this.$v.formData.status.$error) {
        return ''
      }
      if (!this.$v.formData.status.required) {
        return this.$t('validation.required')
      }
    },

    primaryContactNameError() {
      if (!this.$v.formData.primary_contact_name.$error) {
        return ''
      }
      if (!this.$v.formData.primary_contact_name.required) {
        return this.$t('validation.required')
      }
    },

    emailError() {
      if (!this.$v.formData.email.$error) {
        return ''
      }

      if (!this.$v.formData.email.email) {
        return this.$tc('validation.email_incorrect')
      }

      if (!this.$v.formData.email.required) {
        return this.$tc('validation.required')
      }
    },
  },

  created() {
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }

    this.loadData()
    this.permissionsUserModule()
  },

  mounted() {
    this.$v.formData.$reset()
  },
  validations: {
    formData: {
      company_name: {
        requiredIfCompanyName: requiredIf(function () {
          return this.formData.customer_type.value === 'B'
        }),
      },
      email: {
        email,
        required,
      },
      phone: {},
      customer_type: {
        required,
      },
      first_name: {
        requiredIfFirstName: requiredIf(function () {
          return this.formData.customer_type.value !== 'B'
        }),
      },
      last_name: {
        requiredIfLastName: requiredIf(function () {
          return this.formData.customer_type.value !== 'B'
        }),
      },
      status: {
        required,
      },
      type: {},
      primary_contact_name: {
        requiredIfPrimaryContactName: requiredIf(function () {
          return this.formData.customer_type.value === 'B'
        }),
      },
    },
  },

  methods: {
    ...mapActions('lead', ['fetchLead', 'addLead', 'updateLead']),
    ...mapActions('leadNote', ['addLead', 'fetchLeadNotes', 'deleteLeadNote']),
    ...mapActions('user', ['getUserModules']),

    async loadData() {
      // console.log(this.$route.params.id)
      let response = await this.fetchLead(this.$route.params.id)
      //  console.log(response)
      if (response.data) {
        this.formData = { ...response.data.lead }

        this.formData.type = this.lead_type_options.find(
          (item) => item.value == response.data.lead.type
        )
        this.formData.status = this.status_options.find(
          (item) => item.value == response.data.lead.status
        )
        this.formData.customer_type = this.customer_type_options.find(
          (item) => item.value == response.data.lead.customer_type
        )
      }
    },

    async submitData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      try {
        let response
        this.isLoading = true

        const data = {
          company_name: this.formData.company_name,
          email: this.formData.email,
          phone: this.formData.phone,
          customer_type: this.formData.customer_type.value,
          first_name: this.formData.first_name,
          last_name: this.formData.last_name,
          status: this.formData.status.value,
          type: this.formData.type.value,
          website: this.formData.website,
          primary_contact_name: this.formData.primary_contact_name,
        }

        if (this.isEdit) {
          data.id = this.formData.id
          response = await this.updateLead(data)
          //  console.log('response')
          //console.log(response)
          if (response.data.success) {
            this.isLoading = false
            window.toastr['success'](this.$tc('leads.updated_message'))
            this.$router.push('/admin/leads')
          }
        } else {
          response = await this.addLead(data)
          if (response.data.success) {
            this.isLoading = false
            window.toastr['success'](this.$tc('leads.created_message'))
            this.$router.push('/admin/leads')
          }
        }
      } catch (err) {
        window.toastr['success'](this.$tc('leads.error'))
        this.isLoading = false
        // console.log(err);
      }
    },

    async fetchLeadNote({ page, filter, sort }) {
      const params = {
        lead_id: this.$route.params.id,
        limit: 10,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true

      let res = await this.fetchLeadNotes(params)
      //console.log(res)

      this.isRequestOngoing = false

      return {
        data: res.data.leadnotes.data,
        pagination: {
          totalPages: res.data.leadnotes.last_page,
          currentPage: page,
        },
      }
    },

    refreshTable() {
      this.$refs.notes_table.refresh()
    },

    async deleteNote(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customer_ticket.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        try {
          if (willDelete) {
            this.isLoading = false
            let res = await this.deleteLeadNote({ id: id })
            if (res.data.success) {
              window.toastr['success'](
                this.$tc('customer_ticket.deleted_message', 1)
              )
              this.$refs.notes_table.refresh()
              return true
            }
          }
        } catch (e) {
          window.toastr['error']('System error')
        } finally {
          this.isLoading = false
        }
      })
    },

    cancelForm() {
      this.$router.go(-1)
    },

    async permissionsUserModule() {
      try {
        // Fetch permissions for both modules in one request
        const permissions = await Promise.all([
          this.getUserModules({ module: 'lead' }),
          this.getUserModules({ module: 'lead_notes' }),
        ])

        const [permissionsLead, permissionsNotes] = permissions

        console.log('Permissions: lead', permissionsLead)
        console.log('Permissions: lead notes', permissionsNotes)

        // Check super admin for both modules
        if (permissionsLead.super_admin || permissionsNotes.super_admin) {
          console.log('User is super admin')
          // Set all permissions to true for super admin
          this.permissionModule.create = true
          this.permissionModule.update = true
          this.permissionModule.delete = true
          this.permissionModule.read = true
          this.permissionModule2.create = true
          this.permissionModule2.update = true
          this.permissionModule2.delete = true
          this.permissionModule2.read = true
          return // No need for further checks
        }

        // Check module existence for both modules (combined logic)
        if (!permissionsLead.exist && !permissionsNotes.exist) {
          console.log('User does not have access to any modules')
          this.$router.push('/admin/dashboard')
          return
        }

        // Check individual module permissions
        const checkPermissions = (permissionsData, permissionModule) => {
          const modulePermissions = permissionsData.permissions[0]

          if (
            !modulePermissions ||
            modulePermissions.access === 0 ||
            modulePermissions.read === 0
          ) {
            console.log(
              `User does not have access to module: ${permissionsData.module}`
            )
            this.$router.push('/admin/dashboard')
            return
          }

          permissionModule.create = modulePermissions.create === 1
          permissionModule.update = modulePermissions.update === 1
          permissionModule.delete = modulePermissions.delete === 1
          permissionModule.read = modulePermissions.read === 1
        }

        checkPermissions(permissionsLead, this.permissionModule2)
        //checkPermissions(permissionsNotes, this.permissionModule)
        const modulePermissions = permissionsNotes.permissions[0]
        this.permissionModule.create = modulePermissions.create === 1
        this.permissionModule.update = modulePermissions.update === 1
        this.permissionModule.delete = modulePermissions.delete === 1
        this.permissionModule.read = modulePermissions.read === 1

        //console.log(this.permissionModule2)
        //console.log(this.permissionModule)
      } catch (error) {
        console.error('Error fetching permissions:', error)
        this.$router.push('/admin/dashboard')
      }
    },
  },
}
</script>
