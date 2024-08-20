<template>
  <base-page class="item-create">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-page-header :title="this.$t('roles.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="/" />
        <sw-breadcrumb-item :title="$t('roles.title')" to="/admin/roles" />
        <sw-breadcrumb-item :title="$t('general.create')" to="/admin/roles/create" active />
      </sw-breadcrumb>
    </sw-page-header>
    <div class="grid grid-cols-12">
      <div class="col-span-12 md:col-span-8">
        <form action="" @submit.prevent="submitRole">
          <sw-card>
          <sw-input-group :label="$t('roles.role')" :error="roleError" required>
            <sw-select
              v-model="formData.role_id"
              :invalid="$v.formData.role_id.$error"
              :options="roles"
              :searchable="true"
              :show-labels="false"
              :allow-empty="false"
              :placeholder="$t('roles.role')"
              label="name"
              class="mt-1"
              track-by="id"/>
          </sw-input-group>

          <sw-input-group :label="$t('roles.users')" :error="userError" required>
            <sw-select
              v-model="formData.user_id"
              :invalid="$v.formData.user_id.$error"
              :options="users"
              :multiple="true"
              :searchable="true"
              :show-labels="false"
              :allow-empty="false"
              :placeholder="$t('roles.users')"
              label="name"
              class="mt-1"
              track-by="id"/>
          </sw-input-group>

            <div class="mt-6 mb-4">
              <sw-button
                :loading="isLoading"
                variant="primary"
                type="submit"
                size="lg"
                class="flex justify-center w-full md:w-auto">
                <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
                {{ $t('general.save') }}
              </sw-button>
            </div>
          </sw-card>
        </form>
      </div>
    </div>
  </base-page>
</template>

<script>
import { mapActions } from 'vuex'
const { required, } = require('vuelidate/lib/validators')
export default {
  data() {
    return {
      users: [],
      roles: [],
      isRequestOnGoing: false,
      isLoading: false,
      formData: {
        user_id: [],
        role_id: null,
      },
    }
  },
  computed: {
    roleError() {
      if (!this.$v.formData.role_id.$error) {
        return ''
      }
      if (!this.$v.formData.role_id.required) {
        return this.$t('validation.required')
      }
    },
    userError() {
      if (!this.$v.formData.user_id.$error) {
        return ''
      }
      if (!this.$v.formData.user_id.required) {
        return this.$t('validation.required')
      }
    },
  },
  validations: {
    formData: {
      user_id: {
        required,
      },
      role_id: {
        required,
      },
    },
  },
  mounted(){
    this.getUsers()
    this.getRoles()
  },
  methods: {
    ...mapActions('roles', ['fetchRoles', 'fetchUsers', 'assignRole']),
    async getUsers(){
      this.users = await this.fetchUsers({ list: true })
    },
    async getRoles(){
      this.roles = await this.fetchRoles({ list: true })
    },
    async submitRole() {
      this.$v.formData.$touch()
      
      if (this.$v.$invalid) {
        return true
      }

      try {
        this.isLoading = true
        let res = await this.assignRole(this.formData)
        if(res){
          this.isLoading = false
        }
        window.toastr['success'](this.$t('roles.create_assign_role_message'))
        this.$router.push('/admin/roles')
        return true
      } catch (error) {
        this.isLoading = false
        console.log('Error', error)
        window.toastr['error'](error.response.data.response)
      }
    },
  },
}
</script>