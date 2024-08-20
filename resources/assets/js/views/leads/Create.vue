<template>
  <base-page v-if="isSuperAdmin" class="item-create">
    <form action="" @submit.prevent="submitData">
      <sw-page-header class="mb-3" :title="pageTitle">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            to="/admin/dashboard"
            :title="$t('general.home')"
          />
          <sw-breadcrumb-item
            to="/admin/leads"
            :title="$tc('leads.title', 2)"
          />
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
            :loading="isLoading"
            variant="primary-outline"
            type="button"
            size="lg"
            class="mr-3 text-sm hidden sm:flex"
            @click="cancelForm"
          >
            <x-circle-icon class="w-6 h-6 mr-1 -ml-2" v-if="!isLoading" />
            {{ $t('general.cancel') }}
          </sw-button>

          <sw-button
            :loading="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="hidden sm:flex"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ isEdit ? $t('leads.update_lead') : $t('leads.save_lead') }}
          </sw-button>
        </template>
      </sw-page-header>

      <sw-card variant="customer-card">
        <div
          class="grid col-span-12 gap-y-6 gap-x-4 lg:grid-cols-3 md:grid-cols-3 sm:grid-cols-1"
        >
          <sw-input-group
            :label="$t('leads.customer_type')"
            :error="statusError"
            required
          >
            <sw-select
              v-model="formData.customer_type"
              :options="customer_type_options"
              :searchable="true"
              :show-labels="false"
              :allow-empty="true"
              :multiple="false"
              class="mt-2"
              track-by="value"
              label="text"
              @input="$v.formData.customer_type.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            v-if="isCustomerBusiness"
            :label="$t('leads.company_name')"
            :error="companyNameError"
            required
          >
            <sw-input
              v-model.trim="formData.company_name"
              :invalid="$v.formData.company_name.$error"
              autocomplete="off"
              class="mt-2"
              focus
              type="text"
              name="company_name"
              @input="$v.formData.company_name.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            v-if="isCustomerBusiness"
            :label="$t('leads.primary_contact_name')"
            :error="primaryContactNameError"
            required
          >
            <sw-input
              :invalid="$v.formData.primary_contact_name.$error"
              v-model.trim="formData.primary_contact_name"
              autocomplete="off"
              type="text"
              name="primary_contact_name"
              tab-index="3"
              @input="$v.formData.primary_contact_name.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            v-if="!isCustomerBusiness"
            :label="$t('leads.first_name')"
            :error="firstNameError"
            required
          >
            <sw-input
              :invalid="$v.formData.first_name.$error"
              v-model.trim="formData.first_name"
              autocomplete="off"
              type="text"
              name="first_name"
              tab-index="3"
              @input="$v.formData.first_name.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            v-if="!isCustomerBusiness"
            :label="$t('leads.last_name')"
            :error="lastNameError"
            required
          >
            <sw-input
              :invalid="$v.formData.last_name.$error"
              v-model.trim="formData.last_name"
              autocomplete="off"
              type="text"
              name="last_name"
              tab-index="3"
              @input="$v.formData.last_name.$touch()"
            />
          </sw-input-group>

          <sw-input-group :label="$t('leads.type')" :error="typeError">
            <sw-select
              v-model="formData.type"
              :options="lead_type_options"
              :show-labels="false"
              class="mt-2"
              track-by="value"
              label="text"
              @input="$v.formData.type.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('leads.email')"
            :error="emailError"
            required
          >
            <sw-input
              :invalid="$v.formData.email.$error"
              v-model.trim="formData.email"
              autocomplete="off"
              type="text"
              name="email"
              tab-index="3"
              @input="$v.formData.email.$touch()"
            />
          </sw-input-group>

          <sw-input-group :label="$t('leads.phone')" :error="phoneError">
            <sw-input
              :invalid="$v.formData.phone.$error"
              v-model.trim="formData.phone"
              autocomplete="off"
              type="text"
              name="phone"
              tab-index="3"
              @input="$v.formData.phone.$touch()"
            />
          </sw-input-group>

          <sw-input-group :label="$t('leads.status')" :error="statusError">
            <sw-select
              v-model="formData.status"
              :options="status_options"
              :searchable="true"
              :show-labels="false"
              :allow-empty="true"
              :multiple="false"
              class="mt-2"
              track-by="value"
              label="text"
              :disabled="true"
              @input="$v.formData.status.$touch()"
            />
          </sw-input-group>

          <sw-input-group :label="$t('leads.website')">
            <sw-input
              v-model.trim="formData.website"
              autocomplete="off"
              type="text"
              name="website"
              tab-index="3"
            />
          </sw-input-group>
        </div>

        <sw-button
          :loading="isLoading"
          variant="primary-outline"
          type="button"
          size="lg"
          class="mr-3 flex w-full mt-4 sm:hidden md:hidden"
          @click="cancelForm"
        >
          <x-circle-icon class="w-6 h-6 mr-1 -ml-2" v-if="!isLoading" />
          {{ $t('general.cancel') }}
        </sw-button>

        <sw-button
          :loading="isLoading"
          variant="primary"
          type="submit"
          size="lg"
          class="flex w-full mt-4 mb-2 mb-md-0 sm:hidden md:hidden"
        >
          <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
          {{ isEdit ? $t('leads.update_lead') : $t('leads.save_lead') }}
        </sw-button>
      </sw-card>
    </form>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { EyeIcon, EyeOffIcon } from '@vue-hero-icons/outline'
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
      return this.$t('leads.add_lead')
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

    if (this.isEdit) {
      this.loadData()
    }

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
    ...mapActions('user', ['getUserModules']),

    async loadData() {
      let response = await this.fetchLead(this.$route.params.id)
      //console.log(response)
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

    cancelForm() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: 'You may lose unsaved information',
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          this.$router.go(-1)
        }
      })
    },

    async permissionsUserModule() {
      const data = {
        module: 'lead',
      }

      try {
        const permissions = await this.getUserModules(data)
       // console.log('Permissions:', permissions)

        if (permissions.super_admin) {
         // console.log('User is super admin')
          return // No need for further checks
        }

        if (!permissions.exist) {
         // console.log('User does not have the module')
          this.$router.push('/admin/dashboard')
          return
        }

        const modulePermissions = permissions.permissions[0]
       // console.log('Module permissions:', modulePermissions)

        if (!modulePermissions || modulePermissions.access === 0) {
         // console.log('User does not have access to the module')
          this.$router.push('/admin/dashboard')
          return
        }

        if (this.isEdit) {
          if (modulePermissions.update === 0) {
           // console.log('User cannot edit the module')
            this.$router.push('/admin/dashboard')
          }
        } else {
          if (modulePermissions.create === 0) {
           // console.log('User cannot create in the module')
            this.$router.push('/admin/dashboard')
          }
        }
      } catch (error) {
        //console.error('Error fetching permissions:', error)
        this.$router.push('/admin/dashboard')
      }
    },
  },
}
</script>
