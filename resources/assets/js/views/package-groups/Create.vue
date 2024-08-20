<template>
  <base-page v-if="isSuperAdmin" class="item-create">
    <form action="" @submit.prevent="submitGroup">
      <sw-page-header class="mb-3" :title="pageTitle">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            to="/admin/dashboard"
            :title="$t('general.home')"
          />
          <sw-breadcrumb-item
            to="/admin/groups"
            :title="$tc('groups.group', 2)"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'groups.edit'"
            to="#"
            :title="$t('groups.edit_group')"
            active
          />
          <sw-breadcrumb-item
            v-else
            to="#"
            :title="$t('groups.new_group')"
            active
          />
        </sw-breadcrumb>
        <template slot="actions">
          <sw-button
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
            tabindex="3"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ isEdit ? $t('groups.update_group') : $t('groups.save_group') }}
          </sw-button>
        </template>
      </sw-page-header>

      <div class="grid grid-cols-12">
        <div class="col-span-12 md:col-span-12">
          <sw-card>
            <sw-input-group
              :label="$t('groups.name')"
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
                tabindex="1"
                @input="$v.formData.name.$touch()"
              />
            </sw-input-group>

            <sw-input-group :label="$t('groups.description')" class="mt-6 mb-4">
              <base-custom-input
                v-model.trim="formData.description"
                class="mb-4"
                :tabindex="1"
              />
            </sw-input-group>

            <sw-checkbox
              v-model.trim="formData.allow_upgrades"
              :label="$t('groups.allow_upgrades_downgrades')"
              variant="primary"
              size="sm"
              :tabindex="2"
            >
            </sw-checkbox>

            <sw-input-group :label="$t('packages.title')" class="mt-6 mb-4">
            </sw-input-group>

            <div class="grid grid-cols-5 gap-4 mb-8">
              <div
                class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
              >
                <sw-divider class="col-span-12" />

                <div class="col-span-3">
                  <h6>{{ $t('groups.member_packages') }}</h6>
                  <div
                    class="grid gap-6 grid-col-1 md:grid-cols-2"
                    style="
                      height: 100px;
                      overflow-y: scroll;
                      border-color: #cbd5e0;
                      border-width: 1px;
                      border-style: solid;
                    "
                  >
                    <ul>
                      <li
                        v-for="(item, index) in packageLeft"
                        :key="item.id"
                        @click="moveToRight(item, index)"
                      >
                        <div class="cursor-pointer">
                          {{ item.name }}
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>

                <div style="text-align: center; padding: 20px"></div>

                <div class="col-span-7">
                  <h6>{{ $t('groups.available_packages') }}</h6>
                  <div
                    class="grid gap-6 grid-col-1 md:grid-cols-2"
                    style="
                      height: 100px;
                      overflow-y: scroll;
                      border-color: #cbd5e0;
                      border-width: 1px;
                      border-style: solid;
                    "
                  >
                    <ul>
                      <li
                        v-for="(item, index) in packageRight"
                        :key="item.id"
                        @click="moveToLeft(item, index)"
                      >
                        <div class="cursor-pointer">
                          {{ item.name }}
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-6 mb-4"></div>
          </sw-card>

          <sw-button
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
            tabindex="3"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ isEdit ? $t('groups.update_group') : $t('groups.save_group') }}
          </sw-button>
        </div>
      </div>
    </form>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import {
  TrashIcon,
  ChevronDownIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
import CustomFieldsMixin from '../../mixins/customFields'
const { required } = require('vuelidate/lib/validators')
export default {
  mixins: [CustomFieldsMixin],
  components: {
    XCircleIcon,
  },
  data() {
    return {
      isLoading: false,
      title: 'Add Group',
      packageRight: [],
      packageLeft: [],
      formData: {
        name: '',
        description: '',
        allow_upgrades: false,
        package_id: null,
        package: null,
      },
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    pageTitle() {
      if (this.$route.name === 'groups.edit') {
        return this.$t('groups.edit_group')
      }
      return this.$t('groups.new_group')
    },
    isEdit() {
      if (this.$route.name === 'groups.edit') {
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
  },
  created() {
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
  },
  mounted() {
    this.$v.formData.$reset()
    if (this.isEdit) {
      this.loadEditData()
    }
    this.loadPackageMembership()
  },
  validations: {
    formData: {
      name: {
        required,
      },
    },
  },
  methods: {
    ...mapActions('group', [
      'addGroup',
      'fetchGroup',
      'updateGroup',
      'fetchPackageMembership',
    ]),

    async loadEditData() {
      let response = await this.fetchGroup(this.$route.params.id)
      if (response.data) {
        this.formData = { ...this.formData, ...response.data.groups }
      }
      //console.log('formdatab loadeditdata', this.formData);
      if (response.data.packages) {
        this.packageLeft = response.data.packages
        for (var i = 0; i < this.packageLeft.length; i++) {
          for (var j = 0; j < this.packageRight.length; j++) {
            if (this.packageLeft[i].id === this.packageRight[j].id) {
              this.packageRight.splice(j, 1)
            }
          }
        }
      }
    },
    async loadPackageMembership() {
      let res = await this.fetchPackageMembership()
      this.packageRight = res.data
    },
    async submitGroup() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      let text = ''
      if (this.isEdit) {
        text = 'packages.edit_packages_text'
      } else {
        text = 'packages.create_packages_text'
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc(text),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          this.formData.packageLeft = this.packageLeft
          //console.log('formDataaa', this.formData);

          try {
            let response
            this.isLoading = true
            if (this.isEdit) {
              response = await this.updateGroup(this.formData)
              let data
              if (response.status === 200) {
                window.toastr['success'](this.$tc('groups.updated_message'))
                this.$router.push('/admin/groups')
                this.isLoading = false
              }
            } else {
              response = await this.addGroup(this.formData)
              let data
              if (response.status === 200) {
                this.isLoading = false
                if (!this.isEdit) {
                  window.toastr['success'](this.$tc('groups.created_message'))
                  this.$router.push('/admin/groups')
                  return true
                }
              }
            }
          } catch (err) {}
        }
      })
    },
    moveToLeft(item, index) {
      if (this.isEdit) {
        item.new = true
      }
      this.packageLeft.push(item)
      this.packageRight.splice(index, 1)
    },
    cancelForm() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.lose_unsaved_information'),
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          this.$router.go(-1)
        }
      })
    },
    moveToRight(item, index) {
      this.packageRight.push(item)
      this.packageLeft.splice(index, 1)
    },
  },
}
</script>