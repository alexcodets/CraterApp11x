<template>
  <base-page class="item-create">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-page-header :title="pageTitle">
      <sw-breadcrumb slot="breadcrumbs">
        <!--Titles-->
        <sw-breadcrumb-item
          :title="$tc('corePbx.submenu_title.custom_app_rate')"
          to="/admin/corePBX/billing-templates/custom-app-rate"
          active
        />
      </sw-breadcrumb>
      <template slot="actions">
        <sw-button
          v-if="isView"
          tag-name="router-link"
          :to="`/admin/corePBX/billing-templates/custom-app-rate`"
          variant="primary"
          size="lg"
          class="ml-4"
        >
          {{ $t('general.go_back') }}
        </sw-button>

        <sw-button
          v-if="isView"
          variant="primary"
          size="lg"
          class="ml-4"
          @click="removeCustomAppRate()"
        >
          <XIcon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('general.delete') }}
        </sw-button>
        <sw-button
          v-if="isView"
          tag-name="router-link"
          :to="`/admin/corePBX/billing-templates/custom-app-rate/${$route.params.id}/edit`"
          variant="primary"
          size="lg"
          class="ml-4"
        >
          <PencilIcon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('general.edit') }}
        </sw-button>
      </template>
    </sw-page-header>
    <sw-card>
      <!--Form-->
      <div class="flex mt-2 col-span-12">
        <sw-input-group
          :label="$t('general.name')"
          :error="nameError"
          class="md:col-span-3"
          required
        >
          <sw-input
            v-model.trim="formData.name"
            :invalid="$v.formData.name.$error"
            :placeholder="$t('general.name')"
            focus
            type="text"
            name="name"
            tabindex="1"
            placer
            @input="$v.formData.name.$touch()"
            :class="{ 'pointer-events-none': isView }"
          />
        </sw-input-group>
      </div>

      <br />

      <div class="flex flex-wrap">
        <div class="w-full md:w-1/2">
          <!-- Office -->
          <div class="my-8 mb-4 md:pr-5">
            <div class="flex">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.office"
                  class="absolute"
                  style="top: -20px"
                  :disabled="isView"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('general.office') }}
                </p>
              </div>
            </div>
            <sw-input-group
              v-if="formData.office"
              class="mt-3"
              :label="$t('general.office_price')"
              :error="nameError"
            >
              <sw-money
                v-model="formData.office_price"
                :currency="customerCurrency"
                name="price"
                :class="{ 'pointer-events-none': isView }"
              />
            </sw-input-group>
          </div>

          <!-- bussiness -->
          <div class="my-8 mb-4 md:pr-5">
            <div class="flex">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.bussiness"
                  class="absolute"
                  style="top: -20px"
                  :disabled="isView"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('general.bussiness') }}
                </p>
              </div>
            </div>
            <sw-input-group
              v-if="formData.bussiness"
              class="mt-3"
              :label="$t('general.bussiness_price')"
              :error="nameError"
            >
              <sw-money
                v-model="formData.bussiness_price"
                :currency="customerCurrency"
                name="price"
                :class="{ 'pointer-events-none': isView }"
              />
            </sw-input-group>
          </div>

          <!-- web -->
          <div class="my-8 mb-4 md:pr-5">
            <div class="flex">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.web"
                  class="absolute"
                  style="top: -20px"
                  :disabled="isView"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('general.web') }}
                </p>
              </div>
            </div>
            <sw-input-group
              v-if="formData.web"
              class="mt-3"
              :label="$t('general.web_price')"
              :error="nameError"
            >
              <sw-money
                v-model="formData.web_price"
                :currency="customerCurrency"
                name="price"
                :class="{ 'pointer-events-none': isView }"
              />
            </sw-input-group>
          </div>

          <!-- agent -->
          <div class="my-8 mb-4 md:pr-5">
            <div class="flex">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.agent"
                  class="absolute"
                  style="top: -20px"
                  :disabled="isView"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('general.agent') }}
                </p>
              </div>
            </div>
            <sw-input-group
              v-if="formData.agent"
              class="mt-3"
              :label="$t('general.agent_price')"
              :error="nameError"
            >
              <sw-money
                v-model="formData.agent_price"
                :currency="customerCurrency"
                name="price"
                :class="{ 'pointer-events-none': isView }"
              />
            </sw-input-group>
          </div>
        </div>
        <div class="w-full md:w-1/2">
          <!-- supervisor -->
          <div class="my-8 mb-4 md:pr-5">
            <div class="flex">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.supervisor"
                  class="absolute"
                  style="top: -20px"
                  :disabled="isView"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('general.supervisor') }}
                </p>
              </div>
            </div>
            <sw-input-group
              v-if="formData.supervisor"
              class="mt-3"
              :label="$t('general.agent_price')"
              :error="nameError"
            >
              <sw-money
                v-model="formData.supervisor_price"
                :currency="customerCurrency"
                name="price"
                :class="{ 'pointer-events-none': isView }"
              />
            </sw-input-group>
          </div>

          <!-- mobile -->
          <div class="my-8 mb-4 md:pr-5">
            <div class="flex">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.mobile"
                  class="absolute"
                  style="top: -20px"
                  :disabled="isView"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('general.mobile') }}
                </p>
              </div>
            </div>
            <sw-input-group
              v-if="formData.mobile"
              class="mt-3"
              :label="$t('general.mobile_price')"
              :error="nameError"
            >
              <sw-money
                v-model="formData.mobile_price"
                :currency="customerCurrency"
                name="price"
                :class="{ 'pointer-events-none': isView }"
              />
            </sw-input-group>
          </div>

          <!-- crm -->
          <div class="my-8 mb-4 md:pr-5">
            <div class="flex">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.crm"
                  class="absolute"
                  style="top: -20px"
                  :disabled="isView"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('general.crm') }}
                </p>
              </div>
            </div>
            <sw-input-group
              v-if="formData.crm"
              class="mt-3"
              :label="$t('general.crm_price')"
              :error="nameError"
            >
              <sw-money
                v-model="formData.crm_price"
                :currency="customerCurrency"
                name="price"
                :class="{ 'pointer-events-none': isView }"
              />
            </sw-input-group>
          </div>

          <!-- call_pop_up -->
          <div class="my-8 mb-4 md:pr-5">
            <div class="flex">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.call_pop_up"
                  class="absolute"
                  style="top: -20px"
                  :disabled="isView"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('general.call_pop_up') }}
                </p>
              </div>
            </div>
            <sw-input-group
              v-if="formData.call_pop_up"
              class="mt-3"
              :label="$t('general.call_pop_up_price')"
              :error="nameError"
            >
              <sw-money
                v-model="formData.call_pop_up_price"
                :currency="customerCurrency"
                name="price"
                :class="{ 'pointer-events-none': isView }"
              />
            </sw-input-group>
          </div>
        </div>
      </div>

      <br />

      <div class="pt-4 py-2" v-if="!isView">
        <div class="d-flex flex-column flex-md-row">
          <sw-button
            :loading="isLoading"
            type="submit"
            variant="primary"
            size="lg"
            class="mb-2 mb-md-0 flex-fill"
            @click="submitCustomAppRate()"
          >
            <save-icon class="w-6 h-6 mr-1 -ml-2 mr-2" v-if="!isLoading" />
            {{ isEdit ? $t('general.update') : $t('general.save') }}
          </sw-button>

          <sw-button
            variant="primary-outline"
            type="button"
            size="lg"
            class="mt-2 mt-md-0 ml-md-4 flex-fill"
            @click="cancelForm()"
          >
            <x-circle-icon class="w-6 h-6 mr-1 -ml-2" />
            {{ $t('general.cancel') }}
          </sw-button>
        </div>
      </div>

      <!-- List of associated packages  -->
      <div v-if="isView">
        <h6 class="sw-section-title mt-6">
          {{ $t('corePbx.packages.list_of_associated_packages') }}
        </h6>

        <sw-table-component
          ref="table"
          :data="fetchDataPackage"
          :show-filter="false"
          table-class="table"
        >
          <sw-table-column
            :sortable="true"
            :label="$t('packages.item.package_id')"
            show="packages_number"
          >
            <template slot-scope="row">
              <span>{{ $t('packages.item.package_id') }}</span>

              <router-link
                :to="{ path: `/admin/corePBX/packages/${row.id}/view` }"
                class="font-medium text-primary-500"
              >
                {{ row.packages_number }}
              </router-link>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('packages.item.name')"
            show="pbx_package_name"
          />
          <sw-table-column
            :sortable="true"
            :label="$t('packages.item.type_service')"
            show="status_payment"
          >
            <template slot-scope="row">
              <div v-if="row.status_payment == 'postpaid'">
                {{ $t('packages.item.postpaid') }}
              </div>
              <div v-if="row.status_payment == 'prepaid'">
                {{ $t('packages.item.prepaid') }}
              </div>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('packages.item.status')"
            show="status"
          >
            <template slot-scope="row">
              <div v-if="row.status == 'A'">
                <sw-badge
                  :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
                  :color="$utils.getBadgeStatusColor('COMPLETED').color"
                  class="px-3 py-1"
                >
                  {{ $t('general.active') }}
                </sw-badge>
              </div>
              <div v-if="row.status == 'I'">
                <sw-badge
                  :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
                  :color="$utils.getBadgeStatusColor('OVERDUE').color"
                  class="px-3 py-1"
                >
                  {{ $t('general.inactive') }}
                </sw-badge>
              </div>
            </template>
          </sw-table-column>
        </sw-table-component>
      </div>
    </sw-card>
  </base-page>
</template>


<script>
/*IMPORT COMPONENTS*/
import RightArrow from '@/components/icon/RightArrow'
import MoreIcon from '@/components/icon/MoreIcon'
import draggable from 'vuedraggable'
import { mapActions, mapGetters } from 'vuex'
import {
  TrashIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
  XIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
/*VALIDATORS*/
const {
  required,
  minLength,
  numeric,
  minValue,
  maxLength,
} = require('vuelidate/lib/validators')
/*EXPORT DEFAULT*/
export default {
  components: {
    draggable,
    MoreIcon,
    TrashIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusIcon,
    RightArrow,
    XIcon,
    XCircleIcon,
  },

  data() {
    return {
      isRequestOnGoing: false,
      isLoading: false,
      index: 0,
      id: '',
      formData: {
        id: null,
        name: '',
        price: 0.0,
        office: false,
        office_price: 0.0,
        bussiness: false,
        bussiness_price: 0.0,
        web: false,
        web_price: 0.0,
        agent: false,
        agent_price: 0.0,
        supervisor: false,
        supervisor_price: 0.0,
        mobile: false,
        mobile_price: 0.0,
        crm: false,
        crm_price: 0.0,
        call_pop_up: false,
        call_pop_up_price: 0.0,
      },
    }
  },

  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),
    pageTitle() {
      if (this.isEdit) {
        return this.$t('general.edit_template')
      } else if (this.isView && !this.isEdit) {
        return this.$t('general.view_template')
      } else {
        return this.$t('general.new_template')
      }
    },
    customerCurrency() {
      return this.defaultCurrencyForInput
    },
    isEdit() {
      return this.$route.name === 'corepbx.custom-app-rate.edit'
    },
    isView() {
      return this.$route.name === 'corepbx.custom-app-rate.view'
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
  },

  methods: {
    /**MODULES ENDPOINTS**/
    ...mapActions('customAppRate', [
      'updateCustomAppRate',
      'addCustomAppRate',
      'showCustomAppRate',
      'deleteCustomAppRate',
      'packageAssociateCustomAppRate',
    ]),
    ...mapActions('user', ['getUserModules']),
    async fetchDataPackage({ page, filter, sort }) {
      try {
        const params = {
          orderByField: sort.fieldName || 'created_at',
          orderBy: sort.order || 'desc',
          page,
          customAppRateId: this.$route.params.id,
        }

        const res = await this.packageAssociateCustomAppRate(params)
        return {
          data: res.data.packages.data || {},
          pagination: {
            totalPages: res.data.packages.last_page,
            currentPage: page,
            count: res.data.packages.total,
          },
        }
      } catch (e) {}
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
    /**FUNCTIONS**/
    async submitCustomAppRate() {
      ///validacion
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }

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
          try {
            this.isLoading = true

            const data = {
              module: 'pbx_app_rate',
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
              this.formData.id = this.$route.params.id
              await this.updateCustomAppRate(this.formData)
              window.toastr['success'](
                this.$t('corePbx.packages.update_custom_app_rate_success')
              )
              this.$router.push(
                '/admin/corePBX/billing-templates/custom-app-rate/' +
                  this.formData.id +
                  '/view'
              )
            } else {
              const res = await this.addCustomAppRate(this.formData)
              window.toastr['success'](
                this.$t('corePbx.packages.save_custom_app_rate_success')
              )
              this.$router.push(
                '/admin/corePBX/billing-templates/custom-app-rate'
              )
            }
          } catch (error) {
            window.toastr['error'](error.res.data.message)
            return false
          } finally {
            this.isLoading = false
          }
        }
      })
    },
    async loadDataEdit() {
      try {
        this.isRequestOnGoing = true
        const response = await this.$store.dispatch(
          'customAppRate/showCustomAppRate',
          this.$route.params.id
        )
        let data = response.data.customAppRate
        data.office_price = parseFloat(data.office_price)
        data.bussiness_price = parseFloat(data.bussiness_price)
        data.web_price = parseFloat(data.web_price)
        data.agent_price = parseFloat(data.agent_price)
        data.supervisor_price = parseFloat(data.supervisor_price)
        data.mobile_price = parseFloat(data.mobile_price)
        data.crm_price = parseFloat(data.crm_price)
        data.call_pop_up_price = parseFloat(data.call_pop_up_price)
        this.formData = data
      } catch (error) {
        window.toastr['error'](error.res.data.message)
        return false
      } finally {
        this.isRequestOnGoing = false
      }
    },
    async removeCustomAppRate() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('packages.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        // eliminado con try, catch y finally y loading
        try {
          if (willDelete) {
            this.isLoading = true
            await this.deleteCustomAppRate(this.$route.params.id)
            window.toastr['success'](
              this.$t('corePbx.packages.delete_custom_app_rate_success')
            )
            this.$router.push(
              '/admin/corePBX/billing-templates/custom-app-rate'
            )
          }
        } catch (error) {
          window.toastr['error'](
            this.$t('corePbx.packages.delete_custom_app_rate_error')
          )
        } finally {
          this.isLoading = false
        }
      })
    },
  },

  mounted() {
    if (this.isEdit || this.isView) {
      this.loadDataEdit()
    }
    this.$v.formData.$reset()
  },

  validations: {
    formData: {
      name: {
        required,
      },
      price: {
        minValue: minValue(0.0),
      },
    },
  },
}
</script>