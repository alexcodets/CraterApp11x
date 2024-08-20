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
  <base-page class="item-create">
    <form action="" @submit.prevent="submitDID">
      <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
      <sw-page-header :title="pageTitle">
        <sw-breadcrumb slot="breadcrumbs">
          <!-- <sw-breadcrumb-item
            :title="$tc('corePbx.menu_title.did', 2)"
            to="/admin/corePBX/billing-templates/did"
          /> -->
          <sw-breadcrumb-item
            to="/admin/corePBX/billing-templates/did"
            :title="$tc('corePbx.menu_title.did', 2)"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>

      <!--FORM-->
      <div class="flex mt-2 col-span-12">
        <!--NAME-->
        <sw-input-group
          :label="$t('corePbx.did.name')"
          class="md:col-span-3"
          :error="nameError"
          required
        >
          <sw-input
            v-model="formData.name"
            :invalid="$v.formData.name.$error"
            :placeholder="$t('corePbx.did.name')"
            focus
            type="text"
            name="name"
            tabindex="1"
            placer
            @input="$v.formData.name.$touch()"
          />
        </sw-input-group>
        <!-- STATUS -->
        <sw-input-group
          :label="$t('packages.status')"
          class="md:col-span-3"
          :error="statusError"
          style="padding-left: 1em"
          required
        >
          <sw-select
            v-model="formData.status"
            :invalid="$v.formData.status.$error"
            :options="status"
            :searchable="true"
            :show-labels="false"
            :tabindex="2"
            :allow-empty="true"
            :placeholder="$t('general.select_status')"
            label="text"
            track-by="value"
          />
        </sw-input-group>
      </div>

      <div class="flex mt-2 col-span-12">
        <!-- DESCRIPTION -->
        <sw-input-group :label="$t('item_groups.description')" class="mb-4">
          <!-- <sw-editor
            v-model="description"
            :set-editor="formData.description"
            rows="2"
            name="description"
            @input="$v.formData.description.$touch()"
          /> -->
          <sw-textarea
            v-model="description"
            rows="5"
            name="description"
            style="resize: none"
            tabindex="3"
            @input="$v.formData.description.$touch()"
          />
        </sw-input-group>
      </div>

      <div class="flex mt-2 col-span-12">
        <!-- RATE "ml-12"-->
        <!-- <div class="w-full relative"> -->
        <sw-input-group
          :label="$t('corePbx.did.did_price')"
          class="md:col-span-3"
          :error="rateError"
        >
          <sw-money
            v-model="formData.did_rate"
            :currency="defaultCurrencyForInput"
            @input="$v.formData.did_rate.$touch()"
            :invalid="$v.formData.did_rate.$error"
            name="did_rate"
          />
        </sw-input-group>
        <!-- class="mt-2" -->
        <sw-input-group
          :label="$t('corePbx.did.custom_groups_dids')"
          class="md:col-span-3"
          style="padding-left: 1em"
        >
          <sw-select
            v-model="formData.item_groups"
            :options="getItemGroups"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :multiple="true"
            track-by="custom_did_group_id"
            label="custom_did_group_name"
            :tabindex="4"
          />
        </sw-input-group>
        <!-- </div> -->
        <!-- TOLL FREE DID RATE -->
        <!-- <div class="ml-12">
          <sw-input-group
            :label="$t('corePbx.did.toll_free_did_price')"
            class="md:col-span-3"
          >
            <sw-input
              v-model="formData.toll_free_did_rate"
              :invalid="$v.formData.toll_free_did_rate.$error"
              focus
              type="text"
              name="toll_free_did_rate"
              :placeholder="$t('packages.unlimited')"
              tabindex="5"
              @input="$v.formData.toll_free_did_rate.$touch()"
              numeric
            />
          </sw-input-group>
        </div> -->
        <!-- INTERNATIONAL DID RATE -->
        <!-- <div class="ml-12">
          <sw-input-group
            :label="$t('corePbx.did.international_did_price')"
            class="md:col-span-3"
          >
            <sw-input
              v-model="formData.international_did_rate"
              :invalid="$v.formData.international_did_rate.$error"
              focus
              type="text"
              name="international_did_rate"
              :placeholder="$t('packages.unlimited')"
              tabindex="6"
              @input="$v.formData.international_did_rate.$touch()"
              numeric
            />
          </sw-input-group>
        </div> -->
      </div>

      <br /><br />

      <!-- <div class="flex mt-2 col-span-12"> -->
      <!-- INBOUND PER MINUTE RATE SWITCH -->
      <!--  <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('corePbx.did.inbound_per_minute_rate') }}
          </p>
        </div>
        <div class="relative w-12">
          <sw-switch
            v-model="formData.inbound_per_minute_rate"
            class="absolute"
            style="top: -20px"
            @change="slideInbound"
          />
        </div> -->

      <!-- EMERGENCY SERVICES RATE SWITCH -->
      <!--  <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('corePbx.did.emergency_services_rate') }}
          </p>
        </div>
        <div class="relative w-12">
          <sw-switch
            v-model="formData.emergency_services_rate"
            class="absolute"
            style="top: -20px"
            @change="slideEmergency"
          />
        </div> -->

      <!-- CNAM RATE SWITCH -->
      <!-- <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('corePbx.did.cnam_rate') }}
          </p>
        </div>
        <div class="relative w-12">
          <sw-switch
            v-model="formData.cnam_rate"
            class="absolute"
            style="top: -20px"
            @change="slideCNAM"
          />
        </div> -->

      <!--Unmetered -->
      <!--  <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('corePbx.did.unmetered') }}
          </p>
        </div>
        <div class="relative w-12">
          <sw-switch
            v-model="formData.unmetered"
            class="absolute"
            style="top: -20px"
            @change="slideEmergency"
          />
        </div>

      </div> -->

      <!-- Aqui fue movido ADDITIONAL CHARGES -->
      <!--  <br />
             <br />
         <br /> -->

      <h6 class="col-span-5 sw-section-title lg:col-span-1">
        {{ $t('corePbx.extensions.additional_charges_titile') }}
      </h6>

      <br />
      <!-- ADDITIONAL CHARGES -->

      <div class="table-responsive-item2">
        <div class="tablemin">
          <table class="w-full text-center item-table">
            <colgroup>
              <col style="width: 50%" />
              <col style="width: 20%" />
              <col style="width: 30%" />
            </colgroup>
            <thead class="bg-white border border-gray-200 border-solid">
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span class="pl-12">
                  {{ $tc('corePbx.extensions.description', 2) }}
                </span>
              </th>
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                {{ $t('corePbx.extensions.charge') }}
              </th>
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span class="pr-10">
                  {{ $t('corePbx.extensions.status') }}
                </span>
              </th>
            </thead>

            <draggable
              v-model="formData.aditional_charges"
              class="item-body"
              tag="tbody"
              handle=".handle"
            >
              <tr
                v-for="(charge, index) in formData.aditional_charges"
                :key="charge.id"
                :index="index"
                :item-data="charge"
                :group-items="formData.aditional_charges"
                :currency="currency"
                @remove="removeDIDs"
                @update="updateExt"
                @ExtensionValidate="checkExtsData"
                @checkExists="checkExistExt"
                class="box-border bg-white border border-gray-200 border-solid rounded-b"
              >
                <td colspan="5" class="p-0 text-left align-top">
                  <table class="w-full">
                    <colgroup>
                      <col style="width: 50%" />
                      <col style="width: 20%" />
                      <col style="width: 30%" />
                    </colgroup>
                    <tbody>
                      <tr>
                        <td class="px-5 py-4 text-left align-top">
                          <div class="flex justify-start">
                            <div
                              class="flex items-center justify-center w-12 h-5 mt-2 text-gray-400 cursor-move handle"
                            ></div>
                            <sw-input-group
                              :label="$t('corePbx.extensions.description')"
                            >
                              <sw-input
                                v-model="charge.description"
                                :invalid="
                                  $v.formData.aditional_charges.description
                                    .$error
                                "
                                :placeholder="
                                  $t('corePbx.extensions.description')
                                "
                                focus
                                type="text"
                                name="description"
                                tabindex="8"
                                @input="
                                  $v.formData.aditional_charges.description.$touch()
                                "
                              />
                            </sw-input-group>
                          </div>
                        </td>
                        <td class="px-5 py-4 text-left align-top">
                          <div class="flex justify-start">
                            <sw-input-group
                              :label="$t('corePbx.extensions.amount')"
                              :error="ChargeError"
                            >
                              <sw-money
                                v-model="charge.amount"
                                :currency="defaultCurrencyForInput"
                                @input="
                                  $v.formData.aditional_charges.amount.$touch()
                                "
                                :invalid="
                                  $v.formData.aditional_charges.amount.$error
                                "
                                name="amount"
                              />
                            </sw-input-group>
                          </div>
                        </td>
                        <td class="px-5 py-4 text-right align-top">
                          <div class="flex items-center justify-end text-sm">
                            <div class="relative w-12">
                              <sw-switch
                                v-model="charge.status"
                                class="absolute"
                                style="top: -20px"
                              />
                            </div>

                            <div
                              class="flex items-center justify-center w-6 h-10 mx-2 cursor-pointer"
                            >
                              <trash-icon
                                v-if="showRemoveExtIcon"
                                class="h-5 text-gray-700"
                                @click="removeDIDs(index)"
                              />
                            </div>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
            </draggable>
          </table>
        </div>
      </div>

      <div
        class="flex items-center justify-center w-full px-6 py-3 text-base border-b border-gray-200 border-solid cursor-pointer text-primary-400 hover:bg-gray-200"
        @click="addDIDs"
      >
        <plus-icon class="h-5 mr-2" />
        {{ $t('corePbx.extensions.add_charge') }}
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
          {{ isEdit ? $t('did.update_did') : $t('did.save_did') }}
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

      <!-- En veremos -->

      <!--  Aqui empezo el ADDITIONAL CHARGES -->
    </form>
  </base-page>
</template>

<script>
import RightArrow from '@/components/icon/RightArrow'
import MoreIcon from '@/components/icon/MoreIcon'
import LeftArrow from '@/components/icon/LeftArrow'
import draggable from 'vuedraggable'
import AddChargesStub from '../../../../stub/additionalChargesDID'
import { mapActions, mapGetters } from 'vuex'
import {
  TrashIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
const {
  required,
  minLength,
  email,
  numeric,
  minValue,
  maxLength,
  requiredIf,
  float,
} = require('vuelidate/lib/validators')
export default {
  components: {
    MoreIcon,
    draggable,
    TrashIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusIcon,
    RightArrow,
    LeftArrow,
    XCircleIcon,
  },
  props: {
    ExtensionData: {
      type: Object,
      default: null,
    },
    index: {
      type: Number,
      default: null,
    },
    type: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      showSelect: false,
      isRequestOnGoing: false,
      isLoading: false,
      status: [
        { value: 'A', text: 'Active' },
        { value: 'I', text: 'Inactive' },
      ],
      description: '',
      formData: {
        id: '',
        name: '',
        description: '',
        status: { value: 'A', text: 'Active' },
        did_rate: 0,
        toll_free_did_rate: 0,
        international_did_rate: 0,
        international_inbound_per_minute_rate: 0,
        inbound_per_minute_rate: false,
        inbound_per_minute_rate_value: 0,
        emergency_services_rate: false,
        emergency_services_rate_value: 0,
        emergency_services_address: '',
        emergency_services_city: '',
        emergency_services_state: '',
        emergency_services_zip: '',
        cnam_rate: false,
        cnam_name: '',
        cnam_price: 0,
        unmetered: false,
        aditional_charges: [{ ...AddChargesStub }],
        item_groups: [],
      },
      aditional_charges: { ...this.ExtensionData },
      selectedCurrency: '',
    }
  },
  computed: {
    ...mapGetters('customDidGroup', ['customDidGroups']),
    ...mapGetters('company', ['defaultCurrencyForInput']),
    pageTitle() {
      if (this.isEdit) {
        return this.$t('corePbx.did.edit_did')
      } else if (this.isCopy) {
        return this.$t('corePbx.did.copy_did')
      }
      return this.$t('corePbx.did.new_did')
    },
    isEdit() {
      if (this.$route.name === 'corepbx.did.edit') {
        return true
      }
      return false
    },
    isCopy() {
      if (this.$route.name === 'corepbx.did.copy') {
        return true
      }
      return false
    },
    currency() {
      return this.selectedCurrency
    },
    getItemGroups() {
      return this.customDidGroups.map((group) => {
        return {
          ...group,
          custom_did_group_id: group.id,
          custom_did_group_name: group.name,
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
    rateError() {
      if (!this.$v.formData.did_rate.$error) {
        return ''
      }
      if (!this.$v.formData.did_rate.minValue.min) {
        return this.$tc('validation.numbers_only')
      }
    },
    ChargeError() {
      if (!this.$v.formData.aditional_charges.amount.$error) {
        return ''
      }
      if (!this.$v.formData.aditional_charges.amount.minValue) {
        return this.$t('validation.price_minvalue')
      }
    },
    statusError() {
      if (!this.$v.formData.status.$error) {
        return ''
      }
      if (!this.$v.formData.status.required) {
        return this.$t('validation.required')
      }
    },
    isInbound() {
      this.add_inbound = this.formData.inbound_per_minute_rate
      return this.add_inbound
    },
    isEmergency() {
      this.add_emergency = this.formData.emergency_services_rate
      return this.add_emergency
    },
    isCNAM() {
      this.add_cnam = this.formData.cnam_rate
      return this.add_cnam
    },
    showRemoveExtIcon() {
      return true
    },
  },
  methods: {
    ...mapActions('did', ['fetchOneDID', 'updateDID', 'addDID']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('customDidGroup', ['fetchCustomDidGroups']),
    slideInbound() {
      this.formData.inbound_per_minute_rate_value = this.add_inbound
        ? this.formData.inbound_per_minute_rate_value
        : ''
    },
    slideEmergency() {
      this.formData.emergency_services_rate_value = this.add_emergency
        ? this.formData.emergency_services_rate_value
        : ''
      this.formData.emergency_services_address = this.add_emergency
        ? this.formData.emergency_services_address
        : ''
      this.formData.emergency_services_city = this.add_emergency
        ? this.formData.emergency_services_city
        : ''
      this.formData.emergency_services_state = this.add_emergency
        ? this.formData.emergency_services_state
        : ''
      this.formData.emergency_services_zip = this.add_emergency
        ? this.formData.emergency_services_zip
        : ''
    },
    slideCNAM() {
      this.formData.cnam_name = this.add_cnam ? this.formData.cnam_name : ''
      this.formData.cnam_price = this.add_cnam ? this.formData.cnam_price : ''
    },
    async loadCustomItemsGrous() {
      await this.fetchCustomDidGroups({ limit: 'all' })
    },
    async loadDID() {
      let res = await this.fetchOneDID(this.$route.params.id)
      this.formData.unmetered =
        res.data.profileDID.unmetered === 1 ? true : false
      this.formData = res.data.profileDID
      let { description, status } = res.data.profileDID
      this.description = description
      this.formData.status = this.status.filter(
        (element) => element.value == status
      )[0]
      if (this.formData.item_groups) {
        this.formData.item_groups = res.data.profileDID.item_groups.map(
          (itemGroup) => {
            return {
              ...itemGroup,
              custom_did_group_id: itemGroup.id,
              custom_did_group_name: itemGroup.name,
            }
          }
        )
      }
      this.isRequestOnGoing = false
    },
    async submitDID() {
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
          this.formData.unmetered = this.formData.unmetered ? 1 : 0
          this.formData.status = this.formData.status.value
          this.formData.description = this.description
          // console.log('data--->', this.formData)
          try {
            let res
            this.isLoading = true

            const dataPermission = {
              module: 'pbx_did',
            }
            const permissions = await this.getUserModules(dataPermission)
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
              res = await this.updateDID(this.formData)
              window.toastr['success'](
                this.$t('corePbx.did.did_update_message')
              )
              this.$router.push('/admin/corePBX/billing-templates/did')
              return true
            } else {
              res = await this.addDID(this.formData)
              this.isLoading = false
              if (!this.isEdit) {
                window.toastr['success'](
                  this.$t('corePbx.did.did_save_message')
                )
                this.$router.push('/admin/corePBX/billing-templates/did')
                return true
              }
            }
          } catch (error) {
            window.toastr['error'](error.response.data.message)
            this.status = [
              {
                value: 'A',
                text: 'Active',
              },
              {
                value: 'I',
                text: 'Inactive',
              },
            ]
            this.formData.status = {
              value: 'A',
              text: 'Active',
            }
            this.isLoading = false
            return false
          }
        }
      })
    },
    addDIDs() {
      this.formData.aditional_charges.push({
        ...AddChargesStub,
      })
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
    removeDIDs(index) {
      this.formData.aditional_charges.splice(index, 1)
    },
    /* removeExtension() {
      this.$emit('remove', this.index)
    }, */
    updateExt(data) {
      Object.assign(this.formData.aditional_charges[data.index], {
        ...data.aditionalCharges,
      })
    },
    checkExtsData(index, isValid) {
      this.formData.aditional_charges[index].valid = isValid
    },
    checkExistExt(index, newCharge) {
      let pos = this.formData.aditional_charges.findIndex(
        (charge) => charge.id === newCharge.id
      )
      if (pos !== -1) {
        this.formData.aditional_charges.splice(index, 1)
      }
    },
  },
  mounted() {
    this.$v.formData.$reset()
    this.loadCustomItemsGrous()
    if (this.isEdit || this.isCopy) {
      this.isRequestOnGoing = true
      this.loadDID()
    }
  },
  validations: {
    formData: {
      name: {
        minLength: minLength(3),
        required,
      },
      status: {
        required,
      },
      did_rate: {
        minValue: minValue(0.0),
      },
      toll_free_did_rate: {
        minValue: minValue(0.0),
      },
      international_did_rate: {
        minValue: minValue(0.0),
      },
      description: {
        minLength: minLength(0),
      },
      international_inbound_per_minute_rate: {
        minValue: minValue(0.0),
      },
      did_rate: {
        minValue: minValue(0.0),
      },
      toll_free_did_rate: {
        minValue: minValue(0.0),
      },
      international_did_rate: {
        minValue: minValue(0.0),
      },
      international_inbound_per_minute_rate: {
        minValue: minValue(0.0),
      },
      inbound_per_minute_rate_value: {
        minValue: minValue(0.0),
      },
      emergency_services_rate_value: {
        minValue: minValue(0.0),
      },
      emergency_services_address: {
        minLength: minLength(0),
      },
      emergency_services_city: {
        minLength: minLength(0),
      },
      emergency_services_state: {
        minLength: minLength(0),
      },
      emergency_services_zip: {
        minLength: minLength(0),
      },
      cnam_name: {
        minLength: minLength(3),
      },
      cnam_price: {
        minValue: minValue(0.0),
      },
      aditional_charges: {
        description: {
          maxLength: maxLength(255),
        },
        amount: {
          minValue: minValue(0.0),
        },
      },
    },
  },
}
</script>