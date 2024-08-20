<template>
  <base-page class="item-create">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-page-header :title="$t('roles.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="/" />
        <sw-breadcrumb-item :title="$t('roles.title')" to="/admin/roles" />
        <sw-breadcrumb-item :title="$t('general.create')" to="/admin/roles/create" active />
      </sw-breadcrumb>
    </sw-page-header>
    <div class="grid gap-4 grid-cols-12">
      <div class="col-span-12 xl:col-span-4">
        <form action="" @submit.prevent="submitRole">
          <sw-card>
            <!-- User Name -->
            <sw-input-group :label="$t('roles.name')" :error="nameError" class="mb-4" required>
              <sw-input v-model.trim="formData.name" :invalid="$v.formData.name.$error" class="mt-2" focus type="text"
                @input="$v.formData.name.$touch()" />
            </sw-input-group>

            <!-- Description -->
            <sw-input-group :label="$t('items.description')" class="mb-4">
              <sw-textarea v-model="formData.description" rows="2" name="description"
                @input="$v.formData.description.$touch()" />
            </sw-input-group>

            <!-- permissions -->
            <!--          <sw-input-group :label="$t('roles.permissions')" >
            <sw-select
              v-model="formData.permissions"
              :options="permissions"
              :multiple="true"
              :searchable="true"
              :show-labels="false"
              :allow-empty="false"
              :placeholder="$t('roles.permissions')"
              label="name"
              class="mt-1"
              track-by="id"
            />
          </sw-input-group>-->
            <!-- Description -->

            <div class="mt-6 mb-4">
              <sw-button variant="primary" type="submit" size="lg" class="flex justify-center w-full md:w-auto">
                <save-icon class="mr-2 -ml-1" />
                {{ isEdit ? $t('general.update') : $t('general.save') }}
              </sw-button>
            </div>
          </sw-card>
        </form>
      </div>
      <div class="col-span-12 xl:col-span-8">
        <sw-card>
          <div class="flex flex-wrap justify-between items-center">
            <h6 class="sw-section-title mb-5">{{ $t('roles.permissions') }}</h6>
            <sw-button v-if="moduleOptionsFilter.length !== 0" @click="addAllPermissions" variant="primary-outline"
              size="sm">
              {{ $t('roles.modules.add_all') }}
            </sw-button>
          </div>
          <table class="w-full text-center item-table table-permission">
            <thead class="border border-gray-200 border-solid theadClass">
              <th style="width: 70%" class="py-3 px-2 text-sm text-gray-700">
                {{ $t('roles.module') }}
              </th>
              <th class="py-3 px-2 text-sm text-gray-700">
                {{ $t('roles.modules.access') }}
              </th>
              <th class="py-3 px-2 text-sm text-gray-700">
                {{ $t('roles.modules.create') }}
              </th>
              <th class="py-3 px-2 text-sm text-gray-700">
                {{ $t('roles.modules.read') }}
              </th>
              <th class="py-3 px-2 text-sm text-gray-700">
                {{ $t('roles.modules.update') }}
              </th>
              <th class="py-3 px-2 text-sm text-gray-700">
                {{ $t('roles.modules.delete') }}
              </th>
              <th class="py-3 px-2 text-sm text-gray-700"></th>
            </thead>
            <tbody>
              <tr v-for="(permission, indexTr) in formData.permissionss" :key="indexTr"
                class="border border-gray-200 border-solid">
                <td>
                  <div class="flex justify-center items-center p-3">
                    <sw-input-group>
                      <sw-select v-model="permission.module" :options="moduleOptionsFilter" :searchable="true"
                        :show-labels="false" :placeholder="$t('roles.module')" label="label" @input="addPermision" />
                    </sw-input-group>
                  </div>
                </td>
                <td>
                  <div class="flex justify-center pb-6">
                    <sw-switch v-model="permission.access" />
                  </div>
                </td>
                <td>
                  <div class="flex justify-center pb-6">
                    <sw-switch v-model="permission.create" />
                  </div>
                </td>
                <td>
                  <div class="flex justify-center pb-6">
                    <sw-switch v-model="permission.read" />
                  </div>
                </td>
                <td>
                  <div class="flex justify-center pb-6">
                    <sw-switch v-model="permission.update" />
                  </div>
                </td>
                <td>
                  <div class="flex justify-center pb-6">
                    <sw-switch v-model="permission.delete" />
                  </div>
                </td>
                <td>
                  <div v-if="permission.module.value !== ''" class="flex justify-center p-3 cursor-pointer"
                    @click="removeModule(indexTr)">
                    <trash-icon class="h-5 text-gray-700" />
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <!-- <div class="
              flex
              items-center
              justify-center
              w-full
              py-3
              text-base
              border-b border-l border-r  border-gray-200 border-solid
              cursor-pointer
              text-primary-400
              hover:bg-gray-200
            " @click="addPermision">
            <plus-icon class="h-5 mr-2" />
            {{ $t('roles.modules.add_module') }}
          </div> -->
        </sw-card>
      </div>
    </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const {
  required,
  minLength,
  url,
  email,
  numeric,
  minValue,
  maxLength,
  requiredIf,
} = require('vuelidate/lib/validators')
import { TrashIcon, PlusIcon } from '@vue-hero-icons/solid'
export default {
  components: {
    TrashIcon,
    PlusIcon,
  },
  data() {
    return {
      isRequestOnGoing: false,
      formData: {
        name: null,
        description: null,
        permissionss: [],
      },
      moduleOptions: [
        {
          label: this.$t('roles.modules.customers'),
          value: 'customers',
        },
        {
          label: this.$t('roles.modules.leads'),
          value: 'lead',
        },
        {
          label: this.$t('roles.modules.lead_notes'),
          value: 'lead_notes',
        },
        {
          label: this.$t('roles.modules.providers'),
          value: 'providers',
        },
        {
          label: this.$t('roles.modules.estimates'),
          value: 'estimates',
        },
        {
          label: this.$t('roles.modules.invoices'),
          value: 'invoices',
        },
        {
          label: this.$t('roles.modules.payments'),
          value: 'payments',
        },
        {
          label: this.$t('roles.modules.items'),
          value: 'items',
        },
        {
          label: this.$t('roles.modules.expenses'),
          value: 'expenses',
        },
        {
          label: this.$t('roles.modules.packages'),
          value: 'packages',
        },
        {
          label: this.$t('roles.modules.corepbx'),
          value: 'corepbx',
        },
        {
          label: this.$t('roles.modules.tickets'),
          value: 'tickets',
        },
        {
          label: this.$t('roles.modules.users'),
          value: 'users',
        },
        {
          label: this.$t('roles.modules.reports'),
          value: 'reports',
        },
        {
          label: this.$t('roles.modules.settings'),
          value: 'settings',
        },
        {
          label: this.$t('roles.modules.account_settings'),
          value: 'account_settings',
        },
        {
          label: this.$t('roles.modules.company_info'),
          value: 'company_info',
        },
        {
          label: this.$t('roles.modules.preferences'),
          value: 'preferences',
        },
        {
          label: this.$t('roles.modules.customizations'),
          value: 'customizations',
        },
        {
          label: this.$t('roles.modules.notifications'),
          value: 'notifications',
        },
        {
          label: this.$t('roles.modules.tax_Groups'),
          value: 'tax_Groups',
        },
        {
          label: this.$t('roles.modules.tax_types'),
          value: 'tax_types',
        },
        {
          label: this.$t('roles.modules.payment_modes'),
          value: 'payment_modes',
        },
        {
          label: this.$t('roles.modules.custom_fields'),
          value: 'custom_fields',
        },
        {
          label: this.$t('roles.modules.notes'),
          value: 'notes',
        },
        {
          label: this.$t('roles.modules.expense_categories'),
          value: 'expense_categories',
        },
        {
          label: this.$t('roles.modules.mail_configuration'),
          value: 'mail_configuration',
        },
        {
          label: this.$t('roles.modules.file_disk'),
          value: 'file_disk',
        },
        {
          label: this.$t('roles.modules.backup'),
          value: 'backup',
        },
        {
          label: this.$t('roles.modules.logs'),
          value: 'logs',
        },
        {
          label: this.$t('roles.modules.Modules'),
          value: 'Modules',
        },
        {
          label: this.$t('roles.modules.roles'),
          value: 'roles',
        },
        {
          label: this.$t('roles.modules.payment_gateways'),
          value: 'payment_gateways',
        },
        {
          label: this.$t('roles.modules.Authorize'),
          value: 'Authorize',
        },
        {
          label: this.$t('roles.modules.Paypal'),
          value: 'Paypal',
        },
        {
          label: this.$t('roles.modules.services'),
          value: 'services',
        },
        {
          label: this.$t('roles.modules.pbx_services'),
          value: 'pbx_services',
        },
        {
          label: this.$t('roles.modules.services_normal'),
          value: 'services_normal',
        },
        {
          label: this.$t('roles.modules.retentions'),
          value: 'retentions',
        },
        {
          label: this.$t('roles.modules.pbx_services'),
          value: 'pbx_services',
        },
        {
          label: this.$t('roles.modules.services_normal'),
          value: 'services_normal',
        },
        {
          label: this.$t('roles.modules.retentions'),
          value: 'retentions',
        },
        {
          label: this.$t('roles.modules.pbx_packages'),
          value: 'pbx_packages',
        },
        {
          label: this.$t('roles.modules.pbx_extension'),
          value: 'pbx_extension',
        },
        {
          label: this.$t('roles.modules.pbx_did'),
          value: 'pbx_did',
        },
        {
          label: this.$t('roles.modules.pbx_app_rate'),
          value: 'pbx_app_rate',
        },
        {
          label: this.$t('roles.modules.pbx_custom_did'),
          value: 'pbx_custom_did',
        },
        {
          label: this.$t('roles.modules.pbx_custom_destination'),
          value: 'pbx_custom_destination',
        },
        {
          label: this.$t('roles.modules.pbx_customization'),
          value: 'pbx_customization',
        },
        {
          label: this.$t('roles.modules.pbx_report'),
          value: 'pbx_report',
        },
        {
          label: this.$t('roles.modules.pbx_tenant'),
          value: 'pbx_tenant',
        },
        {
          label: this.$t('roles.modules.tickets_depa'),
          value: 'tickets_depa',
        },
        {
          label: this.$t('roles.modules.tickets_email_temp'),
          value: 'tickets_email_temp',
        },
        {
          label: this.$t('roles.modules.cust_address'),
          value: 'cust_address',
        },
        {
          label: this.$t('roles.modules.cust_contacts'),
          value: 'cust_contacts',
        },
        {
          label: this.$t('roles.modules.cust_payment_acc'),
          value: 'cust_payment_acc',
        },
        {
          label: this.$t('roles.modules.cust_mnotes'),
          value: 'cust_mnotes',
        },
        {
          label: this.$t('roles.modules.open_close_cash_register'),
          value: 'open_close_cash_register',
        },
        {
          label: this.$t('roles.modules.income_withdrawal_cash'),
          value: 'income_withdrawal_cash',
        },
        {
          label: this.$t('roles.modules.assign_user_cash_register'),
          value: 'assign_user_cash_register',
        }
        
      ],
      permissionBlank: {
        module: {
          label: '',
          value: '',
        },
        access: 1,
        create: 1,
        read: 1,
        update: 1,
        delete: 1,
      },
      permissionModule:{
        create: false,
        read: false,
        delete: false,
        update: false,
      }
    }
  },
  computed: {

    ...mapGetters('modules',['modules']),

    isEdit() {
      return this.$route.name === 'roles.edit'
    },
    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (this.$v.formData.name.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.name.minLength) {
        return this.$tc(
          'validation.min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
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
    moduleOptionsFilter() {
      this.loadModules()
      const moduleOptions = this.moduleOptions
      const permissionss = this.formData.permissionss

      const moduleOptionsFilter = moduleOptions.filter((option) => {
        return (
          permissionss.findIndex((permission) => {
            return permission.module.value === option.value
          }) === -1
        )
      })
      return moduleOptionsFilter
    },
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },
      description: {
        maxLength: maxLength(65000),
      },
    },
  },
  mounted() {
    this.permissionsUserModule()
  
    if (this.isEdit) {
      this.showRole()
    } else {
      this.addPermissionBlank()
    }
  },
  methods: {
    ...mapActions('roles', ['addRole', 'fetchRole', 'updateRole']),
    ...mapActions('user',['getUserModules']),

    async loadModules(){

try {
  
let modules = this.$store.state.modules.modulesData.modules
  for (const module of modules) {
    
    switch (module.name) {
      case "PBXware":
        if(module.status === 'A'){
          if (this.moduleOptions.find(element => element.value == 'PBXware') == undefined) {
          this.moduleOptions.push(
            {
              label: this.$t("roles.modules.PBXware"),
              value: "PBXware",
            }
          )
          }
        }
      break;
      case "Avalara":
        if(module.status === 'A'){
          if (this.moduleOptions.find(element => element.value == 'Avalara') == undefined) {
          this.moduleOptions.push(
           {
            label: this.$t("roles.modules.Avalara"),
            value: "Avalara",
          })
        }
      }
        
      break;
      case "BillPay":
        if(module.status === 'A'){
          if (this.moduleOptions.find(element => element.value == 'BillPay') == undefined) {
          this.moduleOptions.push(
          {
            label: this.$t("roles.modules.BillPay"),
            value: "BillPay",
          })
        }
      }
        
      break;
      case "Bandwidth":
        // if(module.status === 'A'){
        //   this.moduleOptions.push()
        // }
        
      break;
      case "corePOS":
        if (module.status === 'A') {
          if (this.moduleOptions.find(element => element.value == 'corePOS_module') == undefined) {

            this.moduleOptions.push(
              {
                label: this.$t("roles.modules.corePos_module"),
                value: "corePOS_module",
              })
          }
          if (this.moduleOptions.find(element => element.value == 'corePOS_index') == undefined) {
          this.moduleOptions.push(
            {
              label: this.$t("roles.modules.corePos_index"),
              value: "corePOS_index",
            })}
            if (this.moduleOptions.find(element => element.value == 'corePOS_dashboard') == undefined) {
          this.moduleOptions.push(
            {
              label: this.$t("roles.modules.corePos_dashboard"),
              value: "corePOS_dashboard",
            })
          }
        }
        
      break;
    }
  }
  
} catch (error) {
}
},
    async showRole() {
      try {
        this.isRequestOnGoing = true
        this.formData = await this.fetchRole(this.$route.params.id)
        this.formData.permissionss.map((permission) => {
          let module = this.moduleOptions.find(
            (module) => module.value === permission.module
          )
          // si no existe el modulo
          if (!module) {
            module = {
              label: permission.module,
              value: permission.module,
            }
          }
          permission.module = module
        })
        if (this.moduleOptionsFilter.length > 0) this.addPermissionBlank()
      } catch (error) {
    
        window.toastr['error'](error.response.data.response)
      } finally {
        this.isRequestOnGoing = false
      }
    },
    addAllPermissions() {
      this.formData.permissionss.pop()
      let permissionBlank = JSON.parse(JSON.stringify(this.permissionBlank))
      this.moduleOptionsFilter.forEach((module) => {
        permissionBlank.module = module
        this.formData.permissionss.push(
          JSON.parse(JSON.stringify(permissionBlank))
        )
      })
    },
    addPermissionBlank() {
      const permissionBlank = JSON.parse(JSON.stringify(this.permissionBlank))
      this.formData.permissionss.push(permissionBlank)
    },
    addPermision() {
      const moduleEmpyt = this.formData.permissionss.findIndex(
        (permission) => permission.module.value === ''
      )
      if (moduleEmpyt == -1 && this.moduleOptionsFilter.length !== 0) {
        this.addPermissionBlank()
      }
    },
    removeModule(index) {
      this.formData.permissionss.splice(index, 1)
      this.addPermision()
    },
    async submitRole() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      try {
        this.isRequestOnGoing = true

        const moduleEmpyt = this.formData.permissionss.findIndex(
          (permission) => permission.module.value === ''
        )
        if (moduleEmpyt !== -1)
          this.formData.permissionss.splice(moduleEmpyt, 1)
        this.formData.permissionss.map(
          (permission) => (permission.module = permission.module.value)
        )
        if (this.isEdit) {
          await this.updateRole(this.formData)
          window.toastr['success'](this.$t('roles.update_message'))
        } else {
          await this.addRole(this.formData)
          window.toastr['success'](this.$t('roles.create_message'))
        }
        this.$router.push('/admin/roles')
      } catch (error) {
        
        window.toastr['error'](error.response.data.response)
      } finally {
        this.isRequestOnGoing = false
      }
    },

    async permissionsUserModule(){
      const data = {
        module: "roles" 
      }
      const permissions = await this.getUserModules(data)

      // valida que el usuario tenga permiso para ingresar al modulo
      if(permissions.super_admin == false){
        if(permissions.exist == false ){
          this.$router.push('/admin/dashboard')
        }else {
        const modulePermissions = permissions.permissions[0]
          if(modulePermissions.create == 0 && this.isEdit == false){
            this.$router.push('/admin/dashboard')
          }else if(modulePermissions.update == 0 && this.isEdit == true ){
            this.$router.push('/admin/dashboard')
          }
        }
      }
    }
  },
}
</script>

<style lang="scss" scoped>
.table-permission {
  .theadClass {
    position: sticky;
    top: -35px;
    background-color: #fff;
    border-radius: 4px;
    box-shadow: 0 1px 4px 0 rgba(0, 0, 0, 0.05);
    z-index: 1;
  }
}
</style>