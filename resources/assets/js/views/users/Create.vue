<template>
  <base-page v-if="isSuperAdmin" class="item-create">
    <form action="" @submit.prevent="submitUser">
      <sw-page-header class="mb-3" :title="pageTitle">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            to="/admin/dashboard"
            :title="$t('general.home')"
          />
          <sw-breadcrumb-item to="/admin/users" :title="$tc('users.user', 2)" />
          <sw-breadcrumb-item
            v-if="$route.name === 'users.edit'"
            to="#"
            :title="$t('users.edit_user')"
            active
          />
          <sw-breadcrumb-item
            v-else
            to="#"
            :title="$t('users.new_user')"
            active
          />
        </sw-breadcrumb>
        <template slot="actions">
          <sw-button
            :loading="isLoading"
            variant="primary-outline"
            type="button"
            size="lg"
            @click="cancelButton"
            class="mr-3 text-sm hidden sm:flex"
          >
            <!-- <save-icon v-if="!isLoading" class="mr-2 -ml-1" /> -->
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
            {{ isEdit ? $t('users.update_user') : $t('users.save_user') }}
          </sw-button>
        </template>
      </sw-page-header>

      <div class="grid grid-cols-12">
        <div class="col-span-12 md:col-span-8">
          <sw-card>
            <sw-input-group
              :label="$t('users.name')"
              :error="nameError"
              class="mb-4"
              required
            >
              <sw-input
                v-model.trim="formData.name"
                :invalid="$v.formData.name.$error"
                autocomplete="off"
                class="mt-2"
                focus
                type="text"
                name="name"
                @input="$v.formData.name.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('users.email')"
              class="mt-4"
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
            <div class="w-full flex flex-wrap">
              <sw-input-group
                :label="$tc('users.password')"
                :error="passwordError"
                required
                class="w-full md:w-1/2 mt-3"
              >
              <sw-input
v-model="formData.password"
:invalid="$v.formData.password.$error"
:type="getInputType"
autocomplete="new-password"
class="mt-2"
@input="$v.formData.password.$touch()"
>
<template v-slot:rightIcon>
<eye-off-icon
v-if="isShowPassword"
class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
@click="isShowPassword = !isShowPassword"
/>
<eye-icon
v-else
class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
@click="isShowPassword = !isShowPassword"
/>
</template>
</sw-input>
              </sw-input-group>

              <div class="w-full md:w-1/2 flex items-end">
                <sw-button
                  variant="primary-outline"
                  size="lg"
                  @click="generate()"
                  class="md:ml-2"
                  type="button"
                >
                  {{ $t('customers.generate_password') }}
                </sw-button>
              </div>
            </div>

            <sw-input-group :label="$t('users.phone')" class="mt-4 mb-6">
              <sw-input
                v-model.trim="formData.phone"
                autocomplete="off"
                type="text"
                name="phone"
                tab-index="4"
              />
            </sw-input-group>

            <!-- roles -->
            <sw-input-group
              :label="$t('users.roles')"
              :error="roleError"
              required
            >
              <sw-select
                required
                :invalid="$v.formData.role.$error"
                @input="$v.formData.role.$touch()"
                v-model="formData.role"
                :options="roles"
                :searchable="true"
                :show-labels="false"
                :allow-empty="false"
                :placeholder="$t('users.roles')"
                label="name"
                class="mt-1"
                track-by="id"
              />
            </sw-input-group>

            <sw-input-group :label="$t('users.departaments')" class="mb-4 mt-4">
              <sw-select
                v-model="formData.departament_groups"
                :options="getDepartamentGroups"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :multiple="true"
                class="mt-2"
                track-by="dep_group_id"
                label="dep_group_name"
              />
            </sw-input-group>

            <!-- Check Slide -->

            <div class="flex my-8 mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.pbx_notification"
                  class="absolute"
                  style="top: -20px"
                  :tabindex="6"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('users.pbx_server_notification') }}
                </p>
              </div>
              <!-- -->
              <div class="relative w-12 ml-12">
                <sw-switch
                  v-model="formData.email_estimates"
                  class="absolute ml-8"
                  style="top: -20px"
                  :tabindex="6"
                />
              </div>

              <div class="ml-12">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('users.email_estimates_notification') }}
                </p>
              </div>
            </div>

            <div class="flex my-8 mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.email_invoices"
                  class="absolute"
                  style="top: -20px"
                  :tabindex="6"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('users.email_invoices_notification') }}
                </p>
              </div>
              <!-- -->
              <div class="relative w-12 ml-12">
                <sw-switch
                  v-model="formData.email_payments"
                  class="absolute ml-8"
                  style="top: -20px"
                  :tabindex="6"
                />
              </div>

              <div class="ml-12">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('users.email_payments_notification') }}
                </p>
              </div>
            </div>

            <div class="flex my-8 mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.email_services"
                  class="absolute"
                  style="top: -20px"
                  :tabindex="6"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('users.email_services_notification') }}
                </p>
              </div>
              <!-- -->
              <div class="relative w-12 ml-12">
                <sw-switch
                  v-model="formData.email_pbx_services"
                  class="absolute ml-8"
                  style="top: -20px"
                  :tabindex="6"
                />
              </div>

              <div class="ml-12">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('users.email_pbxservices_notification') }}
                </p>
              </div>
            </div>

            <div class="flex my-8 mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.email_tickets"
                  class="absolute"
                  style="top: -20px"
                  :tabindex="6"
                />
              </div>

              <div class="ml-4 mr-3">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('users.email_tickets_notification') }}
                </p>
              </div>
              <!-- -->
              <div class="relative w-12 ml-12">
                <sw-switch
                  v-model="formData.email_expenses"
                  class="absolute ml-8"
                  style="top: -20px"
                  :tabindex="6"
                />
              </div>

              <div class="ml-12">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('users.email_expenses_notification') }}
                </p>
              </div>
            </div>

            <!-- Check Slide -->

            <div class="mt-6 mb-4">
              <sw-button
                :loading="isLoading"
                variant="primary-outline"
                type="button"
                size="lg"
                @click="cancelButton"
                class="mr-3 flex w-full mt-4 sm:hidden md:hidden"
              >
                <!-- <save-icon v-if="!isLoading" class="mr-2 -ml-1" /> -->
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
                {{ isEdit ? $t('users.update_user') : $t('users.save_user') }}
              </sw-button>
            </div>
          </sw-card>
        </div>
      </div>
    </form>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { EyeIcon, EyeOffIcon } from '@vue-hero-icons/outline'
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
  },
  props: {
    type: {
      type: String,
      default: 'text',
    },
    size: {
      type: String,
      default: '12',
    },
    characters: {
      type: String,
      default: 'a-z,A-Z,0-9,#',
    },
    placeholder: {
      type: String,
      default: 'Password',
    },
    auto: [String, Boolean],
    value: '',
  },
  data() {
    return {
      isLoading: false,
      title: 'Add User',
      bandGeneratePassword: false,
      roles: [],
      formData: {
        name: '',
        email: null,
        password: null,
        phone: null,
        role: null,
        departament_groups: [],
        pbx_notification: false,
        email_estimates: false,
        email_invoices: false,
        email_payments: false,
        email_services: false,
        email_pbx_services: false,
        email_expenses: false,
        email_tickets: false,
      },
      isShowPassword: false,
    }
  },

  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('ticketDepartament', ['departaments']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    pageTitle() {
      if (this.$route.name === 'users.edit') {
        return this.$t('users.edit_user')
      }
      return this.$t('users.new_user')
    },

    isEdit() {
      if (this.$route.name === 'users.edit') {
        return true
      }
      return false
    },

    getDepartamentGroups() {
      return this.departaments.map((dep) => {
        return {
          ...dep,
          dep_group_id: dep.id,
          dep_group_name: dep.name,
        }
      })
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
    },

    roleError() {
      if (!this.$v.formData.role.$error) {
        return ''
      }
      if (!this.$v.formData.role.required) {
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

    passwordError() {
      if (!this.$v.formData.password.$error) {
        return ''
      }
      if (!this.$v.formData.password.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.password.minLength) {
        return this.$tc(
          'validation.password_min_length',
          this.$v.formData.password.$params.minLength.min,
          { count: this.$v.formData.password.$params.minLength.min }
        )
      }
    },

    getInputType() {
      if (this.isShowPassword) {
        return 'text'
      }
      return 'password'
    },
  },

  created() {
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
    this.getRoles()

    if (this.isEdit) {
      this.loadEditData()
    }
    this.getDepartament()
  },

  mounted() {
    this.$v.formData.$reset()
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },
      email: {
        email,
        required,
      },
      role: {
        required,
      },
      password: {
        required: requiredIf(function () {
          return !this.isEdit
        }),
        minLength: minLength(8),
      },
    },
  },

  methods: {
    ...mapActions('users', ['addUser', 'fetchUser', 'updateUser']),
    ...mapActions('roles', ['fetchRoles']),
    ...mapActions('ticketDepartament', ['fetchDepartaments']),

    async loadEditData() {
      let response = await this.fetchUser(this.$route.params.id)

      /*   this.formData.role=response.data.user.role2;
         console.log("Esto es data", this.formData.role); */

      if (response.data) {
        this.formData = { ...this.formData, ...response.data.user }
      }

      // console.log("response",response.data);
      // console.log("formtdat",this.formData);

      if (response.data.user.role2) {
        this.formData.role = this.roles.find(
          (rol) => rol.name === response.data.user.role2
        )
      }

      //console.log("Nuevo rol",this.formData.role);

      if (this.formData.departament_groups) {
        this.getDepartament()

        this.formData.departament_groups = response.data.user.item_groups.map(
          (itemGroup) => {
            return {
              ...itemGroup,
              dep_group_id: itemGroup.id,
              dep_group_name: itemGroup.name,
            }
          }
        )
      }
    },

    async getRoles() {
      this.roles = await this.fetchRoles({ list: true })
    },

    async getDepartament() {
      let data = {
        name: '',
        orderByField: 'created_at',
        orderBy: 'desc',
      }

      await this.fetchDepartaments(data)
    },

    async submitUser() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      let text = ''
      if (this.isEdit) {
        text = 'users.edit_user_save'
      } else {
        text = 'users.create_user'
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc(text),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          try {
            let response
            this.isLoading = true
            if (this.isEdit) {
              response = await this.updateUser(this.formData)
              let data
              if (response.data.success) {
                window.toastr['success'](this.$tc('users.updated_message'))
                this.$router.push('/admin/users')
                this.isLoading = false
              }
              if (response.data.error) {
                window.toastr['error'](
                  this.$t('validation.email_already_taken')
                )
              }
            } else {
              response = await this.addUser(this.formData)
              let data
              if (response.data.success) {
                this.isLoading = false
                if (!this.isEdit) {
                  window.toastr['success'](this.$tc('users.created_message'))
                  this.$router.push('/admin/users')
                  return true
                }
              }
            }
          } catch (err) {
            if (err.response.data.errors.email) {
              this.isLoading = false
            }
          }
        }
      })
    },

    cancelButton() {
      this.$utils.cancelFormOrBack(this, this.$router, 'cancel')
    },

    generate() {
      this.bandGeneratePassword = true
      let charactersArray = this.characters.split(',')
      let CharacterSet = ''
      let password = ''

      if (charactersArray.indexOf('a-z') >= 0) {
        CharacterSet += 'abcdefghijklmnopqrstuvwxyz'
      }
      if (charactersArray.indexOf('A-Z') >= 0) {
        CharacterSet += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
      }
      if (charactersArray.indexOf('0-9') >= 0) {
        CharacterSet += '0123456789'
      }
      if (charactersArray.indexOf('#') >= 0) {
        CharacterSet += '!%&*$#@'
      }

      for (let i = 0; i < this.size; i++) {
        password += CharacterSet.charAt(
          Math.floor(Math.random() * CharacterSet.length)
        )
      }
      this.formData.password = password
    },
  },
}
</script>
