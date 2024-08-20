<template>
  <!-- Base  -->
  <base-page v-if="isSuperAdmin" class="option-group-create">
    <!--------- Form ---------->
    <form action="" @submit.prevent="submitNote">
      <!-- Header  -->
      <sw-page-header class="mb-3" :title="pageTitle">
        <template slot="actions">
          <sw-button
            tag-name="router-link"
            :to="`/admin/leads/${this.$route.params.idlead}/view`"
            class="mr-3 text-sm hidden sm:flex"
            variant="primary-outline"
          >
            {{ $t('customer_notes.cancel') }}
          </sw-button>

          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="hidden sm:flex"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{
              isEdit
                ? $t('customer_notes.update_items_note')
                : $t('customer_notes.save_items_note')
            }}
          </sw-button>
        </template>
      </sw-page-header>

      <div class="grid grid-cols-12">
        <div class="col-span-12">
          <sw-card class="mb-8">
            <sw-input-group
              :label="$t('customer_notes.name')"
              :error="nameError"
              class="mb-4"
              required
            >
              <sw-input
                v-model.trim="formData.summary"
                :invalid="$v.formData.summary.$error"
                class="mt-2"
                focus
                type="text"
                name="name"
                @input="$v.formData.summary.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('leads.message')"
              :error="descriptionError"
              class="mb-4"
            >
              <sw-textarea
                v-model="formData.note"
                rows="2"
                name="note"
                @input="$v.formData.note.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('leads.followupdate')"
              
            >
              <base-date-picker
                v-model="formData.followdate"
                :calendar-button="true"
                calendar-button-icon="calendar"
                class="mt-2"
               
              />

              <div class="ml-4">
         

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{ $t('leads.followupdatedesc') }}
          </p>
        </div>
            </sw-input-group>
          </sw-card>
        </div>
      </div>

      <sw-button
        tag-name="router-link"
        :to="`/admin/leads/${this.$route.params.idlead}/view`"
        class="mr-3 flex w-full mt-4 sm:hidden md:hidden"
        variant="primary-outline"
      >
        {{ $t('customer_notes.cancel') }}
      </sw-button>

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        type="submit"
        size="lg"
        class="flex w-full mt-4 mb-2 mb-md-0 sm:hidden md:hidden"
      >
        <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
        {{
          isEdit
            ? $t('customer_notes.update_items_note')
            : $t('customer_notes.save_items_note')
        }}
      </sw-button>
    </form>
  </base-page>
</template>

<script>
import draggable from 'vuedraggable'

import { mapActions, mapGetters } from 'vuex'
import Guid from 'guid'
import {
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
} from '@vue-hero-icons/solid'

const { required, minLength, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    draggable,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
  },
  data() {
    return {
      isLoading: false,
      title: 'Add Note',
      
      formData: {
        summary: '',
        note: '',
        subject: '',
        body: '',
        stiky: false,
        lead_id: 0,
        id: null,
        
        followdate: null,
      },
    }
  },
  validations: {
    formData: {
      summary: {
        required,
        maxLength: maxLength(120),
      },

      note: {
        maxLength: maxLength(65000),
      },
    },
  },
  computed: {
    ...mapGetters('user', ['currentUser']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    pageTitle() {
      //console.log(this.$route.name)
      if (this.$route.name === 'leadnotes.edit') {
        return this.$t('leads.editnote')
      }
      return this.$t('leads.createnote')
    },

    isEdit() {
      if (this.$route.name === 'leadnotes.edit') {
        return true
      }
      return false
    },

    nameError() {
      if (!this.$v.formData.summary.$error) {
        return ''
      }

      if (!this.$v.formData.summary.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.summary.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.summary.$params.minLength.min,
          { count: this.$v.formData.summary.$params.minLength.min }
        )
      }

      if (!this.$v.formData.summary.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },

    descriptionError() {
      if (!this.$v.formData.note.$error) {
        return ''
      }

      if (!this.$v.formData.note.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
  },
  created() {

  },
  mounted() {
    this.$v.formData.$reset()
    //console.log(this.$route.params.idlead)
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
    this.permissionsUserModules()
   
    if (this.isEdit) {
      this.loadEditCustomerNote()
    }
  },
  methods: {
    ...mapActions('customerNote', [
      'addCustomerNote',
      'fetchCustomerNote',
      'updateCustomerNote',
    ]),

    ...mapActions('leadNote', ['addLead', 'fetchLeadNote', 'updateLeadNote']),

    ...mapActions('user', ['getUserModules']),

    async loadEditCustomerNote() {
      try {
        this.isLoading = true
       // console.log('edit')
       // console.log(this.$route.params)

        let response = await this.fetchLeadNote(this.$route.params.idlnote)
        //console.log(response)

        if (
          response.status === 200 &&
          response.data &&
          response.data.leadnote
        ) {
          this.formData.summary = response.data.leadnote.subject
          this.formData.note = response.data.leadnote.body
          this.formData.id = response.data.leadnote.id
        } else {
          this.handleError()
        }
      } catch (error) {
        console.error(error)
        this.handleError()
      } finally {
        this.isLoading = false
      }
    },

    handleError() {
      window.toastr['error']('System error')
      this.$router.go(-1)
    },

    async submitNote() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      try {
        this.isLoading = true
        this.formData.subject = this.formData.summary
        this.formData.body = this.formData.note

        let response
        this.formData.lead_id = this.$route.params.idlead
       // console.log(this.isEdit)
        if (this.isEdit) {
          this.formData.id = this.$route.params.idlnote
         // console.log(this.formData)
          response = await this.updateLeadNote(this.formData)
        } else {
          response = await this.addLead(this.formData)
         // console.log(response)
        }

        this.isLoading = false

        if (response.status === 200) {
          if (response.data.success) {
            window.toastr['success'](
              this.$t(
                'leads.' +
                  (this.isEdit
                    ? 'updated_message_note'
                    : 'created_message_note')
              )
            )
            this.$router.push(
              '/admin/leads/' + this.$route.params.idlead + '/view'
            )
          } else if (response.data.error) {
            window.toastr['error'](response.data.error)
            return true
          }
        } else {
          this.handleError()
        }
      } catch (err) {
        console.error(err)
        this.handleError()
      } finally {
        this.isLoading = false
      }
    },

    handleError() {
      window.toastr['error']('System error')
      this.$router.go(-1)
    },
    async permissionsUserModules() {
      const data = {
        module: 'lead_notes',
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
    },
  },
}
</script>

<style scoped>
</style>