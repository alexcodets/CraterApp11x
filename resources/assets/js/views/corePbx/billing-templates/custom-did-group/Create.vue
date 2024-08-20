<style>
.table-responsive-item2 {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.tablemin {
  min-width: 900px;
}

/* Additional media query for finer control (optional) */
@media (max-width: 768px) {
  .table-responsive-item2 {
    /* Adjust table width as needed for smaller screens */
    width: 100%; /* Example adjustment */
  }
}
</style>

<template>
  <div class="relative">
    <!-- Page Header -->
    <sw-page-header :title="pageTitle" class="mb-3">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          :title="$t('corePbx.corePbx')"
          to="/admin/corePBX"
        />
        <sw-breadcrumb-item
          :title="$t('corePbx.custom_did_groups.title')"
          to="/admin/corePBX/billing-templates/custom-did-groups"
        />
        <sw-breadcrumb-item
          v-if="isEdit"
          :title="$t('corePbx.custom_did_groups.edit_custom_did_group')"
          to="#"
          active
        />
        <sw-breadcrumb-item
          v-else
          :title="$t('corePbx.custom_did_groups.new_custom_did_group')"
          to="#"
          active
        />
      </sw-breadcrumb>
    </sw-page-header>

    <!--  Form  -->
    <div class="grid grid-cols-12">
      <div class="col-span-12">
        <form action="" @submit.prevent="submitCustomDidGroup">
          <sw-input-group
            :label="$t('corePbx.custom_did_groups.name')"
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
            :label="$t('corePbx.custom_did_groups.status')"
            class="mb-4"
          >
            <sw-select
              v-model="formData.status"
              :options="statuses"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('corePbx.custom_did_groups.select_a_status')"
              class="mt-2"
              label="name"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('corePbx.custom_did_groups.type')"
            class="mb-4"
            :error="typeError"
            required
          >
            <sw-select
              :invalid="$v.formData.type.$error"
              v-model="formData.type"
              :options="types"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('corePbx.custom_did_groups.select_a_type')"
              class="mt-2"
              label="name"
              @input="$v.formData.type.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('corePbx.custom_did_groups.description')"
            :error="descriptionError"
            class="mb-4"
          >
            <sw-textarea
              v-model="formData.description"
              rows="2"
              name="description"
              @input="$v.formData.description.$touch()"
            />
          </sw-input-group>

          <!-- Custom DIDs-->
          <label
            class="text-sm not-italic font-medium leading-5 text-primary-800 text-sm"
          >
            {{ $t('corePbx.custom_did_groups.custom_dids') }}
          </label>

          <div class="table-responsive-item2">
            <div class="tablemin">
              <table class="w-full text-center item-table mt-2">
                <colgroup>
                  <col style="width: 40%" />
                  <col style="width: 30%" />
                  <col style="width: 30%" />
                </colgroup>
                <thead class="bg-white border border-gray-200 border-solid">
                  <th
                    class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                  >
                    <span class="pl-12">
                      {{ $t('corePbx.custom_did_groups.prefix') }}
                    </span>
                  </th>
                  <th
                    class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid"
                  >
                    {{ $t('corePbx.custom_did_groups.category') }}
                  </th>
                  <th
                    class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
                  >
                    <span class="pr-5">
                      {{ $t('corePbx.custom_did_groups.price') }}
                    </span>
                  </th>
                </thead>
                <draggable
                  v-model="formData.customDids"
                  class="item-body"
                  tag="tbody"
                  handle=".handle"
                >
                  <custom-did
                    v-for="(did, index) in formData.customDids"
                    :key="did.id"
                    :index="index"
                    :custom-did-data="did"
                    :custom-did-group="formData.customDids"
                    @remove="removeCustomDid"
                    @update="updateCustomDid"
                    @checkExists="checkExistCustomDid"
                  />
                </draggable>
              </table>
            </div>
          </div>

          <div
            class="flex items-center justify-center w-full px-6 py-3 text-base border-b border-gray-200 border-solid cursor-pointer text-primary-400 hover:bg-gray-200"
            @click="addCustomDid"
          >
            <shopping-cart-icon class="h-5 mr-2" />
            {{ $t('corePbx.custom_did_groups.add_custom_did') }}
          </div>

          <div class="pt-8 py-2 flex flex-col md:flex-row md:space-x-4">
            <sw-button
              :loading="isLoading"
              type="submit"
              variant="primary"
              size="lg"
              class="w-full md:w-auto"
            >
              <save-icon class="w-6 h-6 mr-1 -ml-2 mr-2" v-if="!isLoading" />
              {{
                isEdit
                  ? $t('corePbx.custom_did_groups.update')
                  : $t('corePbx.custom_did_groups.save')
              }}
            </sw-button>

            <sw-button
              variant="primary-outline"
              type="button"
              size="lg"
              class="w-full md:w-auto mt-2 md:mt-0"
              @click="cancelForm()"
            >
              <x-circle-icon class="w-6 h-6 mr-1 -ml-2" />
              {{ $t('general.cancel') }}
            </sw-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { maxLength, minLength, required } from 'vuelidate/lib/validators'
import draggable from 'vuedraggable'
import CustomDid from './CustomDid'
import Guid from 'guid'
import {
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    CustomDid,
    draggable,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
    XCircleIcon,
  },
  data() {
    return {
      isLoading: false,
      formData: {
        name: '',
        description: '',
        status: { name: 'Active', value: 'A' },
        type: {},
        customDids: [
          {
            id: Guid.raw(),
            custom_did_id: null,
            custom_did_group_id: null,
            prefijo: null,
            category: null,
          },
        ],
      },
      statuses: [
        { name: 'Active', value: 'A' },
        { name: 'Inactive', value: 'T' },
      ],
      types: [
        { name: 'International', value: 'IN' },
        { name: 'Local', value: 'LO' },
        { name: 'Toll free', value: 'TF' },
      ],
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

      type: {
        required,
      },
    },
  },
  computed: {
    ...mapGetters('customDidGroup', ['clonedDidGroup']),

    pageTitle() {
      if (this.isEdit) {
        return this.$t('corePbx.custom_did_groups.edit_custom_did_group')
      }
      return this.$t('corePbx.custom_did_groups.new_custom_did_group')
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

      if (!this.$v.formData.name.maxLength) {
        return this.$t('validation.description_maxlength')
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

    typeError() {
      if (!this.$v.formData.type.$error) {
        return ''
      }
      if (!this.$v.formData.type.required) {
        return this.$t('validation.required')
      }
    },

    isEdit() {
      return this.$route.name === 'corepbx.custom-did-groups.edit'
    },
  },
  created() {
    if (this.isEdit) {
      this.loadEditCustomDidGroup()
    }
    this.fetchDIDTOLLFREEs()
  },
  mounted() {
    this.$v.formData.$reset()
    this.checkClonedDidGroup()
  },
  methods: {
    ...mapActions('didtollfree', ['fetchDIDTOLLFREEs']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('customDidGroup', [
      'addCustomDidGroup',
      'fetchCustomDidGroup',
      'updateCustomDidGroup',
      'resetClonedData',
    ]),

    addCustomDid() {
      this.formData.customDids.push({
        id: Guid.raw(),
        custom_did_id: null,
        custom_did_group_id: null,
        prefijo: null,
        category: null,
      })
    },

    async loadEditCustomDidGroup() {
      let response = await this.fetchCustomDidGroup(this.$route.params.id)

      if (response.data) {
        let custom_did_group = response.data.customDidGroup

        this.formData = {
          ...this.formData,
          ...custom_did_group,
          customDids: custom_did_group.custom_dids.map((ctmDid) => {
            return {
              ...ctmDid,
              custom_did_id: ctmDid.id,
              category: ctmDid.category_name,
            }
          }),
        }

        this.formData.status = this.statuses.find(
          (_status) => custom_did_group.status === _status.value
        )

        this.formData.type = this.types.find(
          (_type) => custom_did_group.type === _type.value
        )
      }
    },

    checkExistCustomDid(index, newCustomDid) {
      let pos = this.formData.customDids.findIndex(
        (_ctmDid) => _ctmDid.custom_did_id === newCustomDid.id
      )
      if (pos !== -1) {
        this.formData.customDids.splice(index, 1)
      }
    },

    updateCustomDid(data) {
      Object.assign(this.formData.customDids[data.index], { ...data.customDid })
    },

    removeCustomDid(index) {
      this.formData.customDids.splice(index, 1)
    },

    async checkClonedDidGroup() {
      if (this.clonedDidGroup) {
        this.formData = {
          name: this.clonedDidGroup.name,
          description: this.clonedDidGroup.description,
          customDids: this.clonedDidGroup.custom_dids.map((ctmDid) => {
            return {
              id: Guid.raw(),
              custom_did_group_id: null,
              custom_did_id: ctmDid.id,
              prefijo: ctmDid.prefijo,
              category: ctmDid.category_name,
              rate_per_minute: ctmDid.rate_per_minute,
            }
          }),
        }

        this.formData.status = this.statuses.find(
          (_status) => this.clonedDidGroup.status === _status.value
        )

        this.formData.type = this.types.find(
          (_type) => this.clonedDidGroup.type === _type.value
        )
      }
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

    async submitCustomDidGroup() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }

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
          try {
            let response
            this.isLoading = true
            const data = {
              module: 'pbx_custom_did',
            }
            const permissions = await this.getUserModules(data)
            //console.log(permissions)
            // valida que el usuario tenga permiso para ingresar al modulo
            if (permissions.super_admin == false) {
              if (permissions.exist == false) {
                this.$router.push('/admin/dashboard')
              } else {
                const modulePermissions = permissions.permissions[0]
                if (modulePermissions.create == 0 && this.isEdit == false) {
                  this.$router.push('/admin/dashboard')
                } else if (
                  modulePermissions.update == 0 &&
                  this.isEdit == true
                ) {
                  this.$router.push('/admin/dashboard')
                }
              }
            }
            if (this.isEdit) {
              response = await this.updateCustomDidGroup(this.formData)
              if (response.data.success) {
                window.toastr['success'](
                  this.$t('corePbx.custom_did_groups.updated_message')
                )
                this.$router.push(
                  '/admin/corePBX/billing-templates/custom-did-groups'
                )
              }
              if (response.data.error) {
                this.isLoading = false
                window.toastr['error'](response.data.error)
                return true
              }
            } else {
              response = await this.addCustomDidGroup(this.formData)
              if (response.data.success) {
                window.toastr['success'](
                  this.$tc('corePbx.custom_did_groups.created_message')
                )
                this.$router.push(
                  '/admin/corePBX/billing-templates/custom-did-groups'
                )
              }
              if (response.data.error) {
                this.isLoading = false
                window.toastr['error'](response.data.error)
                return true
              }
            }
          } catch (err) {
            this.isLoading = false
          }
        }
      })
    },
  },
  destroyed() {
    this.resetClonedData()
  },
}
</script>

<style scoped>
</style>