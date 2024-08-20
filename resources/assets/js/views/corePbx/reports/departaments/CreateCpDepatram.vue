<template>
  <!-- Base  -->
  <base-page>
    <!--------- Form ---------->
    <form action="" @submit.prevent="submitMetho">
      <!-- Header  -->
      <sw-page-header class="mb-3" :title="pageTitle">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            to="/admin/dashboard"
            :title="$t('general.home')"
          />
          <sw-breadcrumb-item
            to="/admin/corePBX/reports/departaments/"
            :title="$t('tickets.menu_title.departaments')"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'corepbx.DepartamentsPbxEdit'"
            to="#"
            :title="$t('tickets.departaments.edit_departament')"
            active
          />
          <sw-breadcrumb-item
            v-else
            to="#"
            :title="$t('tickets.departaments.new_departament')"
            active
          />
        </sw-breadcrumb>

        <template slot="actions">
          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="flex justify-center  md:w-auto"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{
              isEdit
                ? $t('tickets.departaments.update_departament')
                : $t('tickets.departaments.save_departament')
            }}
          </sw-button>

          <sw-button
            variant="primary-outline"
            type="button"
            size="lg"
            class="mr-4"
            @click="cancelForm()"
          >
            
            {{ $t('general.cancel') }}
          </sw-button>
        </template>
      </sw-page-header>

      <div class="grid grid-cols-12">
        <div class="col-span-12">
          <sw-card class="mb-8">
            <sw-input-group
              :label="$t('tickets.departaments.name')"
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
                name="name"
                @input="$v.formData.name.$touch()"
              />
            </sw-input-group>
            <sw-input-group
              :label="$t('tickets.departaments.description')"
              class="mb-4"
            >
              <sw-editor
                v-model="formData.description"
                :set-editor="formData.description"
                rows="2"
                name="description"
                @input="$v.formData.description.$touch()"
              />
            </sw-input-group>
            <sw-input-group
              :label="$t('pbx_services.tenant')"
              class="mb-4"
              :error="pbx_tenant_idError"
            >
              <sw-select
                v-model="formData.pbx_tenant_id"
                :options="pbxTenantsOptions"
                :searchable="true"
                :invalid="$v.formData.pbx_tenant_id.$error"
                :show-labels="false"
                :allow-empty="true"
                class="mt-2"
                track-by="id"
                label="name"
                @select="tenantSelectedMethod"
                :disabled="isEdit"
              />
            </sw-input-group>

            <sw-divider class="my-8" />

            <div class="flex flex-wrap">
              <sw-input-group
                :label="$t('corePbx.extensions.add_extensions')"
                class="md:w-1/2"
              >
                <sw-select
                  v-model="extensionSelected"
                  :options="extensionOptionsComputed"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :placeholder="$tc('corePbx.extensions.add_extensions')"
                  class="mt-2"
                  label="name"
                  track-by="id"
                  @select="extensionSelectedMethod"
                  :tabindex="9"
                />
              </sw-input-group>
            </div>

            <sw-table-component
              ref="ext_table"
              :show-filter="false"
              :data="formData.extensionAdded"
              table-class="table"
            >
              <sw-table-column
                :sortable="true"
                :label="$t('pbx_services.name')"
                show="name"
              >
                <template slot-scope="row">
                  {{ row.name }}
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :label="$tc('pbx_services.number')"
                show="ext"
              />

              <sw-table-column
                :sortable="true"
                :label="$tc('pbx_services.location')"
                show="location"
              />

              <sw-table-column
                :sortable="true"
                :label="$tc('pbx_services.status')"
                show="status"
              />
              <sw-table-column
                :sortable="false"
                :filterable="false"
                cell-class="action-dropdown no-click"
              >
                <template slot-scope="row">
                  <span>{{ $t('general.actions') }}</span>
                  <x-icon
                    class="h-5 mr-3 text-gray-600 cursor-pointer"
                    @click="removeExtension(row)"
                  />
                </template>
              </sw-table-column>
            </sw-table-component>

            <div class="mt-6 mb-4"></div>
          </sw-card>
        </div>
      </div>
    </form>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

import {
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
  XIcon,
} from '@vue-hero-icons/solid'

const {
  required,
  minLength,
  maxLength,
  email,
} = require('vuelidate/lib/validators')

export default {
  components: {
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
    XIcon,
  },
  data() {
    return {
      isLoading: false,

      pbxTenantsOptions: [],
      extensionOptions: [],
      extensionSelected: null,
      formData: {
        id: 0,
        name: '',
        description: '',
        pbx_tenant_id: null,
        extensionAdded: [],
      },
    }
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
        maxLength: maxLength(120),
      },

      description: {
        maxLength: maxLength(65000),
      },
      pbx_tenant_id: {
        required,
      },
    },
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('company', ['defaultCurrency']),
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    pageTitle() {
      if (this.$route.name === 'corepbx.DepartamentsPbxEdit') {
        return this.$t('tickets.departaments.edit_departament')
      }
      return this.$t('tickets.departaments.new_departament')
    },

    isEdit() {
      if (this.$route.name === 'corepbx.DepartamentsPbxEdit') {
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
    },
    pbx_tenant_idError() {
      if (!this.$v.formData.pbx_tenant_id.$error) {
        return ''
      }

      if (!this.$v.formData.pbx_tenant_id.required) {
        return this.$t('validation.required')
      }
    },
    extensionOptionsComputed() {
      return this.extensionOptions.filter((extension) => {
        return !this.formData.extensionAdded.find((extensionAdded) => {
          return extension.id == extensionAdded.id
        })
      })
    },

  },
  created() {
    this.getPbxTenants()
    this.showCustomSearchMetho()
  },
  mounted() {
    this.$v.formData.$reset()
  },
  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('customSearch', [
      'indexPbxTenants',
      'indexExtensions',
      'createCustomSearch',
      'updateCustomSearch',
      'showCustomSearch',
    ]),

    async submitMetho() {
      this.$v.formData.$touch()
      if (this.$v.formData.$invalid) return

      ///pregunta
      let text = ''
      if (this.isEdit) {
        text = 'corePbx.extensions.edit_text'
      } else {
        text = 'corePbx.extensions.create_text'
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
          this.formData.pbx_tenant_id = this.formData.pbx_tenant_id?.id
          try {
            if (this.isEdit) {
              await this.updateCustomSearch(this.formData)
              this.$router.push({ name: 'corepbx.departaments' })
            } else {
              swal({
                title: this.$t('general.are_you_sure'),
                text: this.$tc(
                  'general.after_the_template_has_been_created_it_is_not_possible_to_change_the_tenant'
                ),
                icon: '/assets/icon/check-circle-solid.svg',
                buttons: true,
                dangerMode: true,
              }).then(async (value) => {
                if (value) {
                  await this.createCustomSearch(this.formData)
                  this.$router.push({ name: 'corepbx.departaments' })
                }
              })
            }
          } catch (error) {
           // console.log(error)
          }
          this.isLoading = false
        }
      })
    },

    extensionSelectedMethod(extension) {
      this.formData.extensionAdded.push(extension)
      setTimeout(() => {
        this.extensionSelected = null
      }, 100)
    },
    tenantSelectedMethod(tenant) {
      this.getExtensions(tenant)
      this.formData.extensionAdded = []
    },
    async getExtensions(tenant) {
      try {
        const response = await this.indexExtensions(tenant)
        this.extensionOptions = response.data.data
      } catch (error) {
        //console.log(error)
      }
    },
    async getPbxTenants() {
      try {
        const response = await this.indexPbxTenants()
        this.pbxTenantsOptions = Object.values(response.data.data)
      } catch (e) {
        console.log(e)
      }
    },
    removeExtension({ id }) {
      this.formData.extensionAdded = this.formData.extensionAdded.filter(
        (item) => item.id != id
      )
    },
    
    cancelForm() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.cancel_text'),
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          this.$router.go(-1)
        }
      })
    },
    async showCustomSearchMetho() {
      if (!this.isEdit) return

      try {
        const response = await this.showCustomSearch(this.$route.params.id)
        const { id, name, description, pbx_tenant, pbx_extension } =
          response.data.customSearch
        this.formData = {
          id: id,
          name: name,
          description: description,
          pbx_tenant_id: pbx_tenant,
          extensionAdded: pbx_extension,
        }
        this.getExtensions(pbx_tenant)
      } catch (error) {
       // console.log(error)
      }
    },
  },
}
</script>

<style scoped>
</style>