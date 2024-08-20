<template>
  <!-- Base  -->
  <base-page v-if="isSuperAdmin" class="option-group-create">
    <!--------- Form ---------->
    <form action="" @submit.prevent="submitCustomerContact">
      <!-- Header  -->
      <sw-page-header class="mb-3" :title="pageTitle">
        <template slot="actions">
          <sw-button
            class="mr-3 text-sm hidden sm:flex"
            variant="primary-outline"
            type="button"
            @click="cancelForm()"
          >
            <x-circle-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ $t('general.cancel') }}
          </sw-button>

          <sw-button
            :loading="isLoading"
            variant="primary"
            type="submit"
            class="hidden sm:flex"
          >
            <save-icon class="mr-2" v-if="!isLoading" />
            {{ !isEdit ? $t('general.save') : $t('general.update') }}
          </sw-button>
        </template>
      </sw-page-header>
      <sw-card class="mb-8">
        <div class="grid md:grid-cols-2 lg:p-8 sm:p-1 col-span-5">
          <sw-input-group
            :label="$t('contacts.name')"
            :error="nameError"
            class="mt-4"
            required
            variant="horizontal"
          >
            <sw-input
              v-model="formData.name"
              :invalid="$v.formData.name.$error"
              name="formData.name"
              type="text"
              @input="$v.formData.name.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('contacts.last_name')"
            class="mt-4"
            variant="horizontal"
            :error="lastNameError"
            required
          >
            <sw-input
              v-model="formData.last_name"
              name="formData.last_name"
              type="text"
              :invalid="$v.formData.last_name.$error"
              @input="$v.formData.last_name.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('contacts.phone')"
            class="mt-4"
            :error="phoneError"
            required
            variant="horizontal"
          >
            <sw-input
              v-model="formData.phone"
              :invalid="$v.formData.phone.$error"
              name="formData.phone"
              type="text"
              @input="$v.formData.phone.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('contacts.position')"
            class="mt-4"
            variant="horizontal"
            :error="positionError"
            required
          >
            <sw-input
              v-model="formData.position"
              name="formData.position"
              type="text"
              :autocomplete="false"
              :invalid="$v.formData.position.$error"
              @input="$v.formData.position.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('contacts.email')"
            class="mt-4"
            :error="emailError"
            required
            variant="horizontal"
          >
            <sw-input
              v-model="formData.email"
              :invalid="$v.formData.email.$error"
              name="formData.email"
              type="text"
              @input="$v.formData.email.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('contacts.allow_receive_emails')"
            class="mt-4"
            variant="horizontal"
          >
            <sw-switch
              v-model="formData.allow_receive_emails"
              name="formData.allow_receive_emails"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('contacts.type')"
            class="mt-4"
            variant="horizontal"
          >
            <!-- :class="{ error: $v.formData.currency.$error }" -->
            <!-- :custom-label="currencyNameWithCode" -->
            <sw-select
              v-model="formData.type"
              :options="types"
              :searchable="true"
              :show-labels="false"
              :allow-empty="false"
              :placeholder="$tc('contacts.select_type')"
              class="mt-2"
              label="option"
              track-by="id"
            >
            </sw-select>
          </sw-input-group>

          <sw-input-group
            :label="$t('contacts.log_in_credentials')"
            class="mt-4"
            variant="horizontal"
          >
            <sw-switch
              v-model="formData.log_in_credentials"
              name="formData.log_in_credentials"
            />
          </sw-input-group>
          <!-- {{this.formData.log_in_credentials}} -->
        </div>

        <!--  -->

        <h6
          v-if="isAllowReceiveEmails"
          class="col-span-5 sw-section-title lg:col-span-1 mt-3 mt-md-0"
        >
          {{ $t('contacts.allow_receive_emails') }}
        </h6>
        <div
          v-if="isAllowReceiveEmails"
          class="grid md:grid-cols-2 lg:p-8 sm:p-1 col-span-5"
        >
          <sw-input-group :label="$t('general.receive_email_estimates')">
            <sw-switch
              v-model="formData.email_estimates"
              name="formData.email_estimates"
            />
          </sw-input-group>

          <sw-input-group :label="$t('general.receive_email_invoices')">
            <sw-switch
              v-model="formData.email_invoices"
              name="formData.email_invoices"
            />
          </sw-input-group>

          <sw-input-group :label="$t('general.receive_email_payments')">
            <sw-switch
              v-model="formData.email_payments"
              name="formData.email_payments"
            />
          </sw-input-group>

          <sw-input-group :label="$t('general.receive_email_services')">
            <sw-switch
              v-model="formData.email_services"
              name="formData.email_services"
            />
          </sw-input-group>

          <sw-input-group :label="$t('general.receive_email_pbxservices')">
            <sw-switch
              v-model="formData.email_pbx_services"
              name="formData.email_pbx_services"
            />
          </sw-input-group>

          <sw-input-group :label="$t('general.receive_email_tickets')">
            <sw-switch
              v-model="formData.email_tickets"
              name="formData.email_tickets"
            />
          </sw-input-group>
        </div>

        <!--  -->

        <h6
          v-if="isLogInCredentials"
          class="col-span-5 sw-section-title lg:col-span-1 mt-3 mt-md-0"
        >
          {{ $t('customers.login') }}
        </h6>

        <div
          v-if="isLogInCredentials"
          class="grid md:grid-cols-2 lg:p-8 sm:p-1 col-span-5"
        >
          <sw-input-group :label="$t('contacts.password')" required>
            <sw-input
              v-model="formData.password"
              :invalid="$v.formData.password.$error"
              name="formData.password"
              :type="showPassword ? 'text' : 'password'"
              @input="$v.formData.password.$touch()"
            >
              <template v-slot:rightIcon>
                <eye-off-icon
                  v-if="showPassword"
                  class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                  @click="showPassword = !showPassword"
                />
                <eye-icon
                  v-else
                  class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                  @click="showPassword = !showPassword"
                />
              </template>
            </sw-input>
          </sw-input-group>

          <sw-input-group :label="$t('contacts.repeat_password')" required>
            <sw-input
              v-model="formData.repeat_password"
              :invalid="$v.formData.repeat_password.$error"
              name="formData.repeat_password"
              :type="showPassword ? 'text' : 'password'"
              @input="$v.formData.repeat_password.$touch()"
            >
              <template v-slot:rightIcon>
                <eye-off-icon
                  v-if="showPassword"
                  class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                  @click="showPassword = !showPassword"
                />
                <eye-icon
                  v-else
                  class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                  @click="showPassword = !showPassword"
                />
              </template>
            </sw-input>
            <!-- @change="checkRepeatPassword" -->
            <small v-if="errorPasswordsEquals"
              ><p style="color: red">Passwords must coincide</p></small
            >
          </sw-input-group>

          <sw-input-group :label="$t('contacts.generate_password')">
            <sw-button
              class="mr-3 text-sm"
              variant="primary-outline"
              type="button"
              @click="generate()"
            >
              generate password
            </sw-button>
          </sw-input-group>

          <sw-input-group :label="$t('contacts.invoices')">
            <sw-switch v-model="formData.invoices" name="formData.invoices" />
          </sw-input-group>

          <sw-input-group :label="$t('contacts.estimates')" class="mt-4">
            <sw-switch v-model="formData.estimates" name="formData.estimates" />
          </sw-input-group>

          <sw-input-group :label="$t('contacts.payments')" class="mt-4">
            <sw-switch v-model="formData.payments" name="formData.payments" />
          </sw-input-group>

          <sw-input-group :label="$t('contacts.tickets')">
            <sw-switch v-model="formData.tickets" name="formData.tickets" />
          </sw-input-group>

          <sw-input-group :label="$t('contacts.payments_accounts')">
            <sw-switch
              v-model="formData.payments_accounts"
              name="formData.payments_accounts"
            />
          </sw-input-group>
        </div>

        <sw-button
          class="mr-3 flex w-full mt-4 sm:hidden md:hidden"
          variant="primary-outline"
          type="button"
          @click="cancelForm()"
        >
          <x-circle-icon v-if="!isLoading" class="mr-2 -ml-1" />
          {{ $t('general.cancel') }}
        </sw-button>

        <sw-button
          :loading="isLoading"
          variant="primary"
          type="submit"
          class="flex w-full mt-4 mb-2 mb-md-0 sm:hidden md:hidden"
        >
          <save-icon class="mr-2" v-if="!isLoading" />
          {{ !isEdit ? $t('general.save') : $t('general.update') }}
        </sw-button>
      </sw-card>
    </form>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import {
  CheckIcon,
  EyeOffIcon,
  EyeIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
// import { required, alphaNum, helpers } from '@vuelidate/validators'

const {
  required,
  helpers,
  numeric,
  email,
} = require('vuelidate/lib/validators')
// const number = helpers.regex(/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/)

export default {
  components: {
    XCircleIcon,
    CheckIcon,
    EyeOffIcon,
    EyeIcon,
  },
  props: {
    characters: {
      type: String,
      default: 'a-z,A-Z,0-9,#',
    },
    size: {
      type: String,
      default: '12',
    },
  },
  data() {
    return {
      // isEdit: false,
      isLoading: false,
      showPassword: false,
      formData: {
        id: null,
        name: null,
        last_name: null,
        phone: null,
        email: null,
        position: null,
        allow_receive_emails: null,
        type: null,
        log_in_credentials: false,
        allow_receive_emails: false,
        password: null,
        repeat_password: null,
        invoices: true,
        estimates: true,
        payments: true,
        tickets: true,
        payments_accounts: true,
        reports: true,
        email_estimates: false,
        email_invoices: false,
        email_payments: false,
        email_services: false,
        email_pbx_services: false,
        email_tickets: false,
      },
      user_id: null,
      errorPasswordsEquals: false,
      types: [
        { id: 1, value: 'B', option: this.$t('contacts.types.billing') },
        { id: 2, value: 'S', option: this.$t('contacts.types.support') },
        { id: 3, value: 'R', option: this.$t('contacts.types.reports') },
      ],
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    isEdit() {
      if (this.$route.name === 'customers.edit-contact') {
        return true
      }
      return false
    },

    isLogInCredentials() {
      if (this.formData.log_in_credentials) {
        return true
      }
      return false
    },

    isAllowReceiveEmails() {
      if (this.formData.allow_receive_emails) {
        return true
      }
      return false
    },

    pageTitle() {
      if (this.$route.name === 'customers.edit-contact') {
        return this.$t('contacts.edit_contact')
      }
      return this.$t('contacts.add_new_contact')
    },
    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },
    lastNameError() {
      if (!this.$v.formData.last_name.$error) {
        return ''
      }
      if (!this.$v.formData.last_name.required) {
        return this.$tc('validation.required')
      }
    },
    phoneError() {
      if (!this.$v.formData.phone.$error) {
        return ''
      }
      if (!this.$v.formData.phone.required) {
        return this.$tc('validation.required')
      }
    },
    positionError() {
      if (!this.$v.formData.position.$error) {
        return ''
      }
      if (!this.$v.formData.position.required) {
        return this.$tc('validation.required')
      }
    },
    emailError() {
      if (!this.$v.formData.email.$error) {
        return ''
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
        return this.$tc('validation.required')
      }
    },
    repeatPasswordError() {
      if (!this.$v.formData.repeatPassword.$error) {
        return ''
      }
      if (!this.$v.formData.repeatPassword.required) {
        return this.$tc('validation.required')
      }
    },
  },
  beforeDestroy() {
    // this.unsubscribe()
  },
  created() {
    this.loadData()
    // this.subscribeAvalaraBillingInfo()
  },
  validations() {
    if (true) {
      return {
        formData: {
          name: {
            required,
          },
          last_name: {
            required,
          },
          phone: {
            required,
            numeric,
          },
          position: {
            required,
          },
          email: {
            required,
            email,
          },
          password: {
            required,
          },
          repeat_password: {
            required,
          },
        },
      }
    } else {
      return {
        formData: {
          name: {
            required,
          },
          phone: {
            required,
            numeric,
          },
          email: {
            required,
            email,
          },
        },
      }
    }
  },
  async mounted() {
    this.user_id = this.$route.params.id
    // let resCustomer = await this.fetchCustomer(this.$route.params)
    // this.isAvalara = resCustomer.data.customer.avalara_bool
    // this.$refs.name.focus = true
  },
  watch: {
    'formData.repeat_password': {
      deep: true,
      handler: 'checkRepeatPassword',
    },
  },
  methods: {
    ...mapActions('customerContacts', [
      'addCustomerContact',
      'updateCustomerContact',
      'fetchCustomerContact',
    ]),
    ...mapActions('customer', [
      'fetchCustomer',
      'billingValidation',
      /* 'addCustomer',
      'updateCustomer', */
    ]),

    ...mapActions('user', ['getUserModules']),

    resetFormData() {
      this.$v.formData.$reset()
    },
    checkRepeatPassword() {
      if (this.formData.password != this.formData.repeat_password) {
        this.errorPasswordsEquals = true
      } else {
        this.errorPasswordsEquals = false
      }
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

      this.formData.repeat_password = password
    },
    async loadData() {
      const data = {
        module: 'cust_contacts',
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if (permissions.super_admin == false) {
        if (permissions.exist == false) {
          this.$router.push('/admin/dashboard')
        } else {
          const modulePermissions = permissions.permissions[0]
          if (modulePermissions.create == 0 && this.isEdit == false) {
            this.$router.push('/admin/dashboard')
          } else if (modulePermissions.update == 0 && this.isEdit == true) {
            this.$router.push('/admin/dashboard')
          }
        }
      }
      if (this.isEdit) {
        // this.isEdit = true
        this.setData()
        // this.$v.formData.$reset()
      }
    },
    async submitCustomerContact() {
      this.$v.formData.$touch()

      const validNumberPhone = (number) => {
        return /^\d+$/.test(this.formData.phone)
      }
      if (
        (this.$v.formData.email.$invalid &&
          this.$v.formData.name.$invalid &&
          this.$v.formData.phone.$invalid,
        !validNumberPhone(this.formData.phone))
      ) {
        return true
      }

      if (
        this.formData.log_in_credentials &&
        this.$v.formData.password.$invalid &&
        this.$v.formData.repeat_password.$invalid
      ) {
        return true
      }

      let text = ''
      if (this.isEdit) {
        text = 'contacts.update_contact_text'
      } else {
        text = 'contacts.create_contact_text'
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc(text),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          this.isLoading = true
          let response
          this.formData.customer_id = this.user_id
          this.formData.type = this.formData.type?.value

          if (!this.isEdit) {
            response = await this.addCustomerContact(this.formData)
          } else {
            this.formData.id = this.$route.params.idContact
            response = await this.updateCustomerContact(this.formData)
          }
          if (response.data) {
            if (!this.isEdit) {
              window.toastr['success'](this.$t('contacts.created_message'))
            } else {
              window.toastr['success'](this.$t('contacts.updated_message'))
            }

            this.$router.push(
              '/admin/customers/' + this.$route.params.id + '/contacts'
            )
            this.isLoading = false
            return true
          }
          window.toastr['error'](response.data.error)
        }
      })
    },

    async setData() {
      this.isLoading = true
      let res = await this.fetchCustomerContact(this.$route.params.idContact)
      if (res.data.success) {
        this.formData.name = res.data.contact.name
        this.formData.last_name = res.data.contact.last_name
        this.formData.phone = res.data.contact.phone
        this.formData.position = res.data.contact.position
        this.formData.email = res.data.contact.email
        this.formData.allow_receive_emails =
          res.data.contact.allow_receive_emails
        this.formData.log_in_credentials = res.data.contact.log_in_credentials
        this.formData.password = res.data.contact.PasswordDecode
        this.formData.repeat_password = res.data.contact.PasswordDecode
        this.formData.invoices = res.data.contact.invoices
        this.formData.estimates = res.data.contact.estimates
        this.formData.payments = res.data.contact.payments
        this.formData.tickets = res.data.contact.tickets
        this.formData.payments_accounts = res.data.contact.payments_accounts
        this.formData.reports = res.data.contact.reports

        this.formData.email_estimates = res.data.contact.email_estimates
        this.formData.email_invoices = res.data.contact.email_invoices
        this.formData.email_payments = res.data.contact.email_payments
        this.formData.email_services = res.data.contact.email_services
        this.formData.email_pbx_services = res.data.contact.email_pbx_services
        this.formData.email_tickets = res.data.contact.email_tickets

        if (res.data.contact.type) {
          let optionSelected = ''
          let idTypeSelected = ''
          let typeSelected = false
          switch (res.data.contact.type) {
            case 'B':
              idTypeSelected = 1
              optionSelected = 'Billing'
              typeSelected = true
              break
            case 'S':
              optionSelected = 'Support'
              idTypeSelected = 2
              typeSelected = true
              break
            case 'R':
              optionSelected = 'Reports'
              idTypeSelected = 3
              typeSelected = true
              break

            default:
              break
          }

          this.formData.type = {
            id: idTypeSelected,
            value: res.data.contact.type,
            option: optionSelected,
            selected: typeSelected,
          }
        }
      } else {
        this.formData = {
          name: null,
          last_name: null,
          position: null,
          email: null,
          phone: null,
          allow_receive_emails: null,
          password: null,
          log_in_credentials: null,
          repeat_password: null,
          invoices: true,
          estimates: true,
          payments: true,
          tickets: true,
          payments_accounts: true,
          reports: true,
          email_estimates: false,
          email_invoices: false,
          email_payments: false,
          email_services: false,
          email_pbx_services: false,
          email_tickets: false,
        }
      }
      this.isLoading = false
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
  },
}
</script>

<style scoped></style>
