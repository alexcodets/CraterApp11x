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
            <!-- User Name -->
            <sw-input-group
              :label="$t('roles.name')"
              :error="nameError"
              class="mb-4"
              required
            >
              <sw-input
                v-model.trim="formData.name"
                :invalid="$v.formData.name.$error"
                class="mt-2"
                focus
                type="text"
                @input="$v.formData.name.$touch()"
              />
            </sw-input-group>

            <!-- Description -->
            <sw-input-group
              :label="$t('items.description')"
              class="mb-4"
            >
              <sw-textarea
                v-model="formData.description"
                rows="2"
                name="description"
                @input="$v.formData.description.$touch()"
              />
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
              <sw-button
                :loading="isLoading"
                variant="primary"
                type="submit"
                size="lg"
                class="flex justify-center w-full md:w-auto"
              >
                <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
                {{ isEdit ? $t('general.update') : $t('general.save') }}
              </sw-button>
            </div>
          </sw-card>
        </form>
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
export default {
  data() {
    return {
      permissions:[],
      isRequestOnGoing: false,
      isLoading: false,
      formData: {
        name: null,
        description: null,
        permissions:[],
      },
    }
  },
  watch: {},
  computed: {
    isEdit() {
      if (this.$route.name === 'roles.edit') {  
        return true
      }
      return false
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
  mounted(){
    if(this.isEdit){
      this.firstPermissions()
      this.getPermissions()
    } else {
      this.getPermissionsAll()
    }
  },
  methods: {
    async getPermissions(){
      this.permissions = await this.fetchPermissions()
    },
     async getPermissionsAll(){
      this.permissions = await this.fetchPermissions()
    },
    async firstPermissions(){
      this.formData = await this.fetchRole(this.$route.params.id)
    },
    ...mapActions('roles', ['addRole', 'fetchPermissions', 'fetchRole', 'updateRole']),
    async submitRole() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      try {
        if(this.isEdit){
          let res
          this.isLoading = true
          res = await this.updateRole({ formData: this.formData, roleId: this.$route.params.id })
          this.isLoading = false
          window.toastr['success'](this.$t('roles.update_message'))
          this.$router.push('/admin/roles')
          return true
        } else {
          let res
          this.isLoading = true
          res = await this.addRole(this.formData)
          this.isLoading = false
          window.toastr['success'](this.$t('roles.create_message'))
          this.$router.push('/admin/roles')
          return true
        }
        
      } catch (error) {
        console.log('Error', error)
        window.toastr['error'](error.response.data.response)
        this.isLoading = false
        return false
      }
    },
  },
}
</script>

<style lang="scss" scoped>
</style>