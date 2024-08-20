<style>
.scrollable-div3 {
  overflow-x: auto; /* Habilita el desplazamiento horizontal si el contenido excede el ancho del div */
  width: 100%; /* El div toma el 100% del ancho de su contenedor */
  -webkit-overflow-scrolling: touch; /* Permite un desplazamiento táctil fluido en iOS */
}
.scrollable-div3 table {
  width: 100%; /* Opcional: asegura que la tabla se expanda para llenar el div */
  /* Tus estilos de tabla aquí */
}

.table-wrapperestper {
  min-width: 600px; /* Establece un ancho mínimo de 800 píxeles para la tabla */
}
</style>

<template>
  <base-page class="item-create">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-page-header :title="$t('users.user_permisions')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item to="/admin/users" :title="$tc('users.user', 2)" />
        <sw-breadcrumb-item
          :to="'/admin/users/' + idUser + '/view'"
          :title="userData.name"
        />
        <sw-breadcrumb-item to="#" :title="$t('roles.permissions')" active />
      </sw-breadcrumb>
    </sw-page-header>
    <div class="grid gap-4 grid-cols-12">
      <div class="col-span-12 xl:col-span-4">
        <sw-card>
          <div class="grid grid-cols-2 gap-4 xl:grid-cols-1">
            <div class="mb-5">
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('users.name') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ userData.name }}
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('users.role') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ userData.role2 }}
              </p>
            </div>
          </div>
        </sw-card>
      </div>
      <div class="col-span-12 xl:col-span-8">
        <sw-card>
          <base-loader v-if="isRequestOnGoing" />
          <div
            class="flex flex-col md:flex-row md:justify-between items-center mb-5 md:mb-0"
          >
            <h6 class="sw-section-title mb-5 md:mb-0">
              {{ $t('roles.permissions') }}
            </h6>
            <div
              class="flex flex-wrap justify-center md:justify-end w-full md:w-auto"
            >
              <sw-button
                type="button"
                @click="cancelButton"
                variant="primary"
                class="mr-2 mb-2 md:mb-0 w-full md:w-auto"
              >
                {{ $t('roles.modules.cancel') }}
              </sw-button>
              <sw-button
                v-if="moduleOptionsFilter.length !== 0"
                @click="addAllPermissions"
                variant="primary-outline"
                class="mr-2 mb-2 md:mb-0 w-full md:w-auto"
              >
                {{ $t('roles.modules.add_all') }}
              </sw-button>
              <sw-button
                variant="primary"
                @click="updateUser()"
                class="mr-2 mb-2 md:mb-0 w-full md:w-auto"
              >
                <save-icon class="mr-2 -ml-1" />
                {{ $t('general.update') }}
              </sw-button>
            </div>
          </div>

          <div class="scrollable-div3">
            <table
              class="w-full text-center item-table table-permission table-wrapperestper"
            >
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
                <tr
                  v-for="(permission, indexTr) in userData.permissions"
                  :key="indexTr"
                  class="border border-gray-200 border-solid"
                >
                  <td>
                    <div class="flex justify-center items-center p-3">
                      <sw-input-group>
                        <sw-select
                          v-model="permission.module"
                          :options="moduleOptionsFilter"
                          :searchable="true"
                          :show-labels="false"
                          :placeholder="$t('roles.module')"
                          label="label"
                          @input="addPermision"
                        />
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
                    <div
                      v-if="permission.module.value !== ''"
                      class="flex justify-center p-3 cursor-pointer"
                      @click="removeModule(indexTr)"
                    >
                      <trash-icon class="h-5 text-gray-700" />
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
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
      userData: {
        permissions: [],
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
        },
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
    }
  },
  computed: {
    ...mapGetters('modules', ['modules']),


    moduleOptionsFilter() {
      // Inicio del método moduleOptionsFilter
      //console.log('Inicio del método moduleOptionsFilter')

      // Se asignan las opciones de módulo y los permisos del usuario a variables locales
      const moduleOptions = this.moduleOptions
      const permissions = this.userData.permissions

      // Se llama al método loadModules para cargar los módulos
      this.loadModules()

      // Se filtran las opciones de módulo que no están presentes en los permisos del usuario
      const moduleOptionsFilter = moduleOptions.filter((option) => {
        const isPermissionAbsent =
          permissions.findIndex((permission) => {
            // Se verifica si el módulo del permiso es no nulo y no está vacío
            if (permission.module != null && permission.module != '') {
              // Se compara el valor del módulo del permiso con el valor de la opción actual
              return permission.module.value === option.value
            }
          }) === -1 // Si el permiso no se encuentra, se incluye la opción en el filtro

        // Se registra el resultado de la búsqueda de cada opción
      /*  console.log(
          `Opción ${option.value} está ausente en los permisos: ${isPermissionAbsent}`
        )*/
        return isPermissionAbsent
      })

      // Se devuelve el array filtrado de opciones de módulo
      //console.log('Opciones de módulo filtradas:', moduleOptionsFilter)
     // console.log('Fin del método moduleOptionsFilter')
      // Fin del método moduleOptionsFilter
      return moduleOptionsFilter
    },

    idUser() {
      return this.$route.params.id
    },
  },
  mounted() {
    this.loadUserData()
  },
  methods: {
    ...mapActions('users', ['showUserId', 'updatePermissionsUser']),
    ...mapActions('modules', ['getModules']),

    async loadModules() {
      try {
        let modules = this.$store.state.modules.modulesData.modules
        for (const module of modules) {
          switch (module.name) {
            case 'PBXware':
              if (module.status === 'A') {
                if (
                  this.moduleOptions.find(
                    (element) => element.value == 'PBXware'
                  ) == undefined
                ) {
                  this.moduleOptions.push({
                    label: this.$t('roles.modules.PBXware'),
                    value: 'PBXware',
                  })
                }
              }
              break
            case 'Avalara':
              if (module.status === 'A') {
                if (
                  this.moduleOptions.find(
                    (element) => element.value == 'Avalara'
                  ) == undefined
                ) {
                  this.moduleOptions.push({
                    label: this.$t('roles.modules.Avalara'),
                    value: 'Avalara',
                  })
                }
              }

              break
            case 'BillPay':
              if (module.status === 'A') {
                if (
                  this.moduleOptions.find(
                    (element) => element.value == 'BillPay'
                  ) == undefined
                ) {
                  this.moduleOptions.push({
                    label: this.$t('roles.modules.BillPay'),
                    value: 'BillPay',
                  })
                }
              }

              break
            case 'Bandwidth':
              // if(module.status === 'A'){
              //   this.moduleOptions.push()
              // }

              break
            case 'corePOS':
              if (module.status === 'A') {
                if (
                  this.moduleOptions.find(
                    (element) => element.value == 'corePOS_module'
                  ) == undefined
                ) {
                  this.moduleOptions.push({
                    label: this.$t('roles.modules.corePos_module'),
                    value: 'corePOS_module',
                  })
                }
                if (
                  this.moduleOptions.find(
                    (element) => element.value == 'corePOS_index'
                  ) == undefined
                ) {
                  this.moduleOptions.push({
                    label: this.$t('roles.modules.corePos_index'),
                    value: 'corePOS_index',
                  })
                }
                if (
                  this.moduleOptions.find(
                    (element) => element.value == 'corePOS_dashboard'
                  ) == undefined
                ) {
                  this.moduleOptions.push({
                    label: this.$t('roles.modules.corePos_dashboard'),
                    value: 'corePOS_dashboard',
                  })
                }
              }

              break
          }
        }
      } catch (error) {}
    },
    async loadUserData() {
     // console.log('Inicio del método loadUserData')
      try {
        // Indica que una solicitud está en curso
        this.isRequestOnGoing = true
        // Obtiene los datos del usuario mediante una llamada a la API
        const res = await this.showUserId(this.idUser)
        // Asigna los datos del usuario a la variable userData
        this.userData = res.data?.data
        // Mapea los permisos del usuario para asociarlos con el módulo correspondiente
        this.userData.permissions.map((permission) => {
          let module = this.moduleOptions.find(
            (module) => module.value === permission.module
          )
          // Si no existe el módulo, lo crea y lo asigna al permiso
          if (!module) {
            module = {
              id: permission.id,
              label: permission.module,
              value: permission.module,
            }
          }
          permission.module = module
        })
        // Activa los console.log para depuración
        //console.log(this.userData.permissions.length)
        //console.log(this.moduleOptionsFilter.length)
        //console.log(this.moduleOptionsFilter)
        // Añade un permiso en blanco si es necesario
        if (this.userData.permissions.length <= 64) {
          this.addPermissionBlank()
        }
      } catch (error) {
        // Aquí deberías manejar el error, por ejemplo, mostrando un mensaje al usuario
        //console.error(error)
      } finally {
        // Indica que la solicitud ha terminado
        this.isRequestOnGoing = false
      //  console.log('Fin del método loadUserData')
      }
    },
    async updateUser() {
      try {
        this.isRequestOnGoing = true
        let payload = {
          id: this.$route.params.id,
          permissions: this.userData.permissions,
        }
        // eliminar el en blanco
        payload.permissions = payload.permissions.filter((permission) => {
          return permission.module.value !== ''
        })
        const res = await this.updatePermissionsUser(payload)

        this.$router.push({
          name: 'users.view',
          params: { idUser: this.idUser },
        })
      } catch (error) {
      } finally {
        this.isRequestOnGoing = false
      }
    },

    addAllPermissions() {
      this.userData.permissions.pop()
      let permissionBlank = JSON.parse(JSON.stringify(this.permissionBlank))
      this.moduleOptionsFilter.forEach((module) => {
        permissionBlank.module = module
        this.userData.permissions.push(
          JSON.parse(JSON.stringify(permissionBlank))
        )
      })
    },
    addPermissionBlank() {
      const permissionBlank = JSON.parse(JSON.stringify(this.permissionBlank))
      this.userData.permissions.push(permissionBlank)
    },
    addPermision() {
      const moduleEmpyt = this.userData.permissions.findIndex(
        (permission) => permission.module.value === ''
      )
      if (moduleEmpyt == -1 && this.moduleOptionsFilter.length !== 0) {
        this.addPermissionBlank()
      }
    },
    removeModule(index) {
      this.userData.permissions.splice(index, 1)
      this.addPermision()
    },

    cancelButton() {
      this.$utils.cancelFormOrBack(this, this.$router, 'cancel')
    },
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
