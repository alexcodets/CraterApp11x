<template>
  <base-page class="item-create">
    <form action="" @submit.prevent="submitExtension">
      <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
      <sw-page-header :title="pageTitle">
        <sw-breadcrumb slot="breadcrumbs">
          <!--Titles-->
              <sw-breadcrumb-item
                :title="$tc('corePbx.menu_title.extensions', 2)"
                to="/admin/corePBX/billing-templates/extensions"
                active
              />
              <!-- <sw-breadcrumb-item
                v-if="$route.name === 'corepbx.extensions.edit'"
                :title="$t('corePbx.extensions.edit_extension')"
                to="#"
                active
              />
              <sw-breadcrumb-item
                v-else-if="$route.name === 'corepbx.extensions.copy'"
                :title="$t('corePbx.extensions.copy_extension')"
                to="#"
                active
              /> -->
         <!--  <sw-breadcrumb-item
            v-else
            :title="$t('corePbx.extensions.new_extension')"
            to="#"
            active
          /> -->
        </sw-breadcrumb>
      </sw-page-header>
      <sw-card>
        <!--Form-->
        <div class="flex mt-2 col-span-12">
          <sw-input-group
            :label="$t('corePbx.extensions.name')"
            :error="nameError"
            class="md:col-span-3"
            required
          >
            <sw-input
              v-model.trim="formData.name"
              :invalid="$v.formData.name.$error"
              :placeholder="$t('items.name')"
              focus
              type="text"
              name="name"
              tabindex="1"
              placer
              @input="$v.formData.name.$touch()"
            />
          </sw-input-group>

          <!-- STATUS  @change="slidePrepaid" -->
          <sw-input-group
            :label="$t('packages.status')"
            class="md:col-span-3"
            :error="statusError"
            style="padding-left:1em;"
            required
          >
            <sw-select
              v-model.trim="formData.status"
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
          <!-- RATE -->
          <sw-input-group
            :label="$t('corePbx.extensions.price')"
            class="md:col-span-3"
            :error="rateError"
            required
          >
            <sw-input
              v-model.trim="formData.rate"
              :invalid="$v.formData.rate.$error"
              focus
              type="text"
              name="rate"
              :placeholder="$t('packages.unlimited')"
              tabindex="3"
              @input="$v.formData.rate.$touch()"
              numeric
            />
          </sw-input-group>
        </div>

        <div class="flex mt-2 col-span-12">
          <!-- <sw-input-group :label="$t('item_groups.description')" class="mb-4">
            <sw-editor
              v-model.trim="description"
              :set-editor="formData.description"
              rows="2"
              name="description"
              @input="$v.formData.description.$touch()"
            />
          </sw-input-group> -->
          <sw-input-group
            :label="$t('item_groups.description')" class="mb-4"
          >
            <sw-textarea
                v-model.trim="description"
                rows="5"
                tabindex="4"
                name="description"
                style="resize: none;"
                @input="$v.formData.description.$touch()"
            />
        </sw-input-group>

        </div>


        <div class="flex mt-2 col-span-12">
          <!-- MINUTES CAP -->
          <!-- <div class="ml-12">
            <sw-input-group
              :label="$t('corePbx.extensions.minutes_cap')"
              class="md:col-span-3"
            >
              <sw-input
                v-model.trim="formData.minutes_cap"
                :invalid="$v.formData.minutes_cap.$error"
                focus
                type="text"
                name="minutes_cap"
                :placeholder="$t('packages.unlimited')"
                tabindex="1"
                @input="$v.formData.minutes_cap.$touch()"
                numeric
              />
            </sw-input-group>
          </div> -->
          <!-- MINUTES INCREMENT -->
          <!-- <div class="ml-12">
            <sw-input-group
              :label="$t('corePbx.extensions.minutes_increments')"
              class="md:col-span-3"
            >
              <sw-input
                v-model.trim="formData.minutes_increments"
                :invalid="$v.formData.minutes_increments.$error"
                focus
                type="text"
                name="minutes_increments"
                :placeholder="$t('packages.unlimited')"
                tabindex="1"
                @input="$v.formData.minutes_increments.$touch()"
                numeric
              />
            </sw-input-group>
          </div> -->
          <!-- SELECT TYPE INCREMENT -->
          <!-- <div class="ml-12">
            <sw-input-group :label="$t('corePbx.extensions.select')">
              <sw-select
                v-model.trim="formData.type_time_increment"
                :invalid="$v.formData.type_time_increment.$error"
                :options="type_time_increment"
                :searchable="true"
                :show-labels="false"
                :tabindex="16"
                :allow-empty="true"
                :placeholder="$t('corePbx.extensions.select')"
                label="text"
                track-by="value"
              />
            </sw-input-group>
          </div> -->
        </div>
        <div class="flex mt-2 col-span-12">
          <!--  OUTBOUND PER MIN RATE -->
          <!-- <div class="ml-12">
            <sw-input-group
              :label="$t('corePbx.extensions.outbound_per_minute_rate')"
              class="md:col-span-3"
            >
              <sw-input
                v-model.trim="formData.outbound_per_minute_rate"
                :invalid="$v.formData.outbound_per_minute_rate.$error"
                focus
                type="text"
                name="outbound_per_minute_rate"
                @input="$v.formData.outbound_per_minute_rate.$touch()"
                numeric
              />
            </sw-input-group>
          </div> -->
          <!--  INBOUND PER MIN RATE -->
          <!-- <div class="ml-12">
            <sw-input-group
              :label="$t('corePbx.extensions.inbound_per_minute_rate')"
              class="md:col-span-3"
            >
              <sw-input
                v-model.trim="formData.inbound_per_minute_rate"
                :invalid="$v.formData.inbound_per_minute_rate.$error"
                focus
                type="text"
                name="inbound_per_minute_rate"
                @input="$v.formData.inbound_per_minute_rate.$touch()"
                numeric
              />
            </sw-input-group>
          </div> -->
          <!--  EXTENSION BALANCE -->
          <!-- <div class="ml-12" v-if="isPrepaid">
            <sw-input-group :label="$t('corePbx.extensions.extension_balance')">
              <sw-input
                v-model.trim="formData.extension_balance"
                :invalid="$v.formData.extension_balance.$error"
                focus
                type="text"
                name="extension_balance"
                @input="$v.formData.extension_balance.$touch()"
                numeric
              />
            </sw-input-group>
          </div> -->
        </div>
        <!-- <div class="flex mt-2 col-span-12"> -->
        <!-- MINIMUM BALANCE EXTENSION -->
        <!-- <div class="ml-12" v-if="isPrepaid">
            <sw-input-group
              :label="$t('corePbx.extensions.minimum_extension_balance')"
            >
              <sw-input
                v-model.trim="formData.minimum_extension_balance"
                :invalid="$v.formData.minimum_extension_balance.$error"
                focus
                type="text"
                name="minimum_extension_balance"
                @input="$v.formData.minimum_extension_balance.$touch()"
                numeric
              />
            </sw-input-group>
          </div> -->
        <!-- </div> -->
        <br />
        <br />

        <h6 class="col-span-5 sw-section-title lg:col-span-1">
          {{ $t('corePbx.extensions.additional_charges_titile') }}
        </h6>

        <br />

        <!-- ADDITIONAL CHARGES -->
        <table class="w-full text-center item-table">
          <colgroup>
            <col style="width: 50%" />
            <col style="width: 20%" />
            <col style="width: 30%" />
          </colgroup>
          <thead class="bg-white border border-gray-200 border-solid">
            <th
              class="
                px-5
                py-3
                text-sm
                not-italic
                font-medium
                leading-5
                text-left text-gray-700
                border-t border-b border-gray-200 border-solid
              "
            >
              <span class="pl-12">
                {{ $tc('corePbx.extensions.description', 2) }}
              </span>
            </th>
            <th
              class="
                px-5
                py-3
                text-sm
                not-italic
                font-medium
                leading-5
                text-center text-gray-700
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ $t('corePbx.extensions.charge') }}
            </th>
            <th
              class="
                px-5
                py-3
                text-sm
                not-italic
                font-medium
                leading-5
                text-right text-gray-700
                border-t border-b border-gray-200 border-solid
              "
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
              @remove="removeExt"
              @update="updateExt"
              @ExtensionValidate="checkExtsData"
              @checkExists="checkExistExt"
              class="
                box-border
                bg-white
                border border-gray-200 border-solid
                rounded-b
              "
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
                            class="
                              flex
                              items-center
                              justify-center
                              w-12
                              h-5
                              mt-2
                              text-gray-400
                              cursor-move
                              handle
                            "
                          ></div>
                          <sw-input-group
                            :label="$t('corePbx.extensions.description')"
                          >
                            <sw-input
                              v-model="charge.description"
                              :invalid="
                                $v.formData.aditional_charges.description.$error
                              "
                              focus
                              type="text"
                              name="description"
                              tabindex="6"
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
                            <sw-input
                              ref="amount"
                              v-model="charge.amount"
                              :invalid="
                                $v.formData.aditional_charges.amount.$error
                              "
                              focus
                              type="text"
                              name="amount"
                              tabindex="7"
                              @input="
                                $v.formData.aditional_charges.amount.$touch()
                              "
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
                            class="
                              flex
                              items-center
                              justify-center
                              w-6
                              h-10
                              mx-2
                              cursor-pointer
                            "
                          >
                            <trash-icon
                              v-if="showRemoveExtIcon"
                              class="h-5 text-gray-700"
                              @click="removeExt(index)"
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

        <div
          class="
            flex
            items-center
            justify-center
            w-full
            px-6
            py-3
            text-base
            border-b border-gray-200 border-solid
            cursor-pointer
            text-primary-400
            hover:bg-gray-200
          "
          @click="addExt"
        >
          <plus-icon class="h-5 mr-2" />
          {{ $t('corePbx.extensions.add_charge') }}
        </div>

        <div class="mt-6 mb-4">
          <sw-button
            :loading="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            tabindex="8"
            class="flex justify-center w-full md:w-auto"
          >
            <save-icon class="mr-2 -ml-1" v-if="!isLoading" />
            {{
              isEdit
                ? $t('corePbx.extensions.update_extension_button')
                : $t('corePbx.extensions.save_extension_button')
            }}
          </sw-button>
        </div>
      </sw-card>
    </form>
  </base-page>
</template>


<script>
/*IMPORT COMPONENTS*/
import RightArrow from '@/components/icon/RightArrow'
import MoreIcon from '@/components/icon/MoreIcon'
import LeftArrow from '@/components/icon/LeftArrow'
import draggable from 'vuedraggable'
import AddChargesStub from '../../../../stub/additionalChargesExtension'
import { mapActions } from 'vuex'
import {
  TrashIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
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
    LeftArrow,
  },
  props: {
    extensionData: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      showSelect: false,
      isRequestOnGoing: false,
      isLoading: false,
      index: 0,
      id: '',
      name: '',
      status: [
        { value: 'A', text: 'Active' },
        { value: 'I', text: 'Inactive' },
        { value: 'R', text: 'Restricted' },
      ],
      /* status_payment: [
        { value: 'prepaid', text: 'Prepaid' },
        { value: 'postpaid', text: 'Postpaid' },
      ], */
      type_time_increment: [
        { value: 'sec', text: 'Seconds' },
        { value: 'min', text: 'Minutes' },
      ],
      rate: 0.0,
      description: '',
      minutes_cap: 0,
      minutes_increments: 0,
      outbound_per_minute_rate: 0,
      // inbound_per_minute_rate: 0,
      // extension_balance: 0,
      // minimum_extension_balance: 0,
      inbound_per_minute_rate: null,
      extension_balance: null,
      minimum_extension_balance: null,
      formData: {
        id: '',
        name: '',
        /* status_payment: { value: 'prepaid', text: 'Prepaid' }, */
        status:  { value: 'A', text: 'Active' },
        rate: 0.0,
        description: '',
        minutes_cap: 0,
        minutes_increments: 0,
        type_time_increment: { value: 'sec', text: 'Seconds' },
        outbound_per_minute_rate: 0,
        // inbound_per_minute_rate: 0,
        // extension_balance: 0,
        // minimum_extension_balance: 0,
        unmetered: false,
        inbound_per_minute_rate: null,
        extension_balance: null,
        minimum_extension_balance: null,
        status: { value: 'A', text: 'Active' },
        aditional_charges: [{ ...AddChargesStub }],
      },
      aditional_charges: { ...this.extensionData },
      selectedCurrency: '',
    }
  },

  computed: {
    pageTitle() {
      if (this.isEdit) {
        return this.$t('corePbx.extensions.edit_extension')
      } else if (this.isCopy) {
        return this.$t('corePbx.extensions.copy_extension')
      }
      return this.$t('corePbx.extensions.new_extension')
    },
    isEdit() {
      if (this.$route.name === 'corepbx.extensions.edit') {
        return true
      }
      return false
    },
    isCopy() {
      if (this.$route.name === 'corepbx.extensions.copy') {
        return true
      }
      return false
    },
    currency() {
      return this.selectedCurrency
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
      if (!this.$v.formData.rate.$error) {
        return ''
      }
      if (!this.$v.formData.rate.minValue.min) {
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
    /* statusPaymentError() {
      if (!this.$v.formData.status_payment.$error) {
        return ''
      }
      if (!this.$v.formData.status_payment.required) {
        return this.$t('validation.required')
      }
    }, */
    statusError() {
      if (!this.$v.formData.status.$error) {
        return ''
      }
      if (!this.$v.formData.status.required) {
        return this.$t('validation.required')
      }
    },
    showRemoveExtIcon() {
      return true
    },

   /*  isPrepaid() {
      if (this.formData.status_payment.value == 'prepaid') {
        this.add_prepaid = true
      } else {
        this.add_prepaid = false
      }
      return this.add_prepaid
    }, */
  },

  methods: {
    /**MODULES ENDPOINTS**/
    ...mapActions('extensions', [
      'fetchOneExtension',
      'updateExtension',
      'addExtension',
      'fetchAditionalCharges',
    ]),

    /*switch in select*/
    /* slidePrepaid() {
      this.formData.extension_balance = this.add_prepaid
        ? this.formData.extension_balance
        : ''
      this.formData.minimum_extension_balance = this.add_prepaid
        ? this.formData.minimum_extension_balance
        : ''
    }, */

    /**FUNCTIONS**/
    async loadExtension() {
      let res = await this.fetchOneExtension(this.$route.params.id)

      this.formData = res.data.profileExtension
      console.log('formData ', this.formData)
      if (this.isEdit || this.isCopy) {
        if (res.data.profileExtension.type || this.value_discount) {
          this.showSelect = false
        }
      }

      let {
        id,
        name,
        /* status_payment, */
        status,
        type_time_increment,
        rate,
        description,
        minutes_cap,
        minutes_increments,
        outbound_per_minute_rate,
        // inbound_per_minute_rate,
        // extension_balance,
        // minimum_extension_balance,
      } = res.data.profileExtension

      this.id = id
      this.name = name
      /* this.formData.status_payment = this.status_payment.filter(
        (element) => element.value == status_payment
      )[0] */
      if(status==='A'){
       this.formData.status = { value: 'A', text: 'Active' }
      }else if(status==='I'){
        this.formData.status = { value: 'I', text: 'Inactive' }
      }else{
        this.formData.status = { value: 'R', text: 'Restricted' }
      }
      
      this.formData.type_time_increment = this.type_time_increment.filter(
        (element) => element.value == type_time_increment
      )[0]
      this.rate = rate
      this.description = description
      this.minutes_cap = minutes_cap
      this.minutes_increments = minutes_increments
      this.outbound_per_minute_rate = outbound_per_minute_rate

      if (typeof inbound_per_minute_rate !== 'undefined') {
        this.inbound_per_minute_rate = inbound_per_minute_rate
      } else {
        this.inbound_per_minute_rate = 0
      }
      if (typeof extension_balance !== 'undefined') {
        this.extension_balance = extension_balance
      } else {
        this.extension_balance = 0
      }

      if (typeof minimum_extension_balance !== 'undefined') {
        this.minimum_extension_balance = minimum_extension_balance
      } else {
        this.minimum_extension_balance = 0
      }

      this.isRequestOnGoing = false
    },

    async submitExtension() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      
      this.formData.unmetered = this.formData.unmetered ? 1 : 0
      this.formData.status = this.formData.status.value
      /* this.formData.status_payment = this.formData.status_payment.value */
      this.formData.description = this.description
      /* this.formData.status = 'A' */
      this.formData.status_payment = 'prepaid';
      if (this.formData.type_time_increment.text == 'Minutes') {
        this.formData.type_time_increment = 'min'
      } else {
        this.formData.type_time_increment = 'sec'
      }

      try {
        let res
        this.isLoading = true
        if (this.isEdit) {
          this.formData.id = this.$route.params.id
          res = await this.updateExtension(this.formData)
          this.isLoading = false
          window.toastr['success'](this.$t('corePbx.extensions.update_extension'))
          this.$router.push('/admin/corePBX/billing-templates/extensions')
          return true
        } else {
          res = await this.addExtension(this.formData)
          this.isLoading = false
          if (!this.isEdit) {
            window.toastr['success'](this.$t('corePbx.extensions.save_extension'))
            this.$router.push('/admin/corePBX/billing-templates/extensions')
            return true
          }
        }
      } catch (error) {
        console.log('Error', error)
        window.toastr['error'](error.res.data.message)
        /* this.status = [
          {
            value: 'A',
            text: 'Active',
          },
          {
            value: 'I',
            text: 'Inactive',
          },
          {
            value: 'R',
            text: 'Restricted',
          },
        ]
        this.formData.status = {
          value: 'A',
          text: 'Active',
        } */
        this.isLoading = false
        return false
      }
    },

    addExt() {
      this.formData.aditional_charges.push({
        ...AddChargesStub,
      })
    },

    removeExt(index) {
      console.log('SE CONTACTÓ AL REMOVE EXT', index)
      this.formData.aditional_charges.splice(index, 1)
    },

    /* removeExtension(index) {
      // console.log('REMOVE EXTENSION CUAL ES ?', this.index)
      console.log('REMOVE EXTENSION CUAL ES ?', index)
      // this.$emit('remove', this.index)
      this.$emit('remove', index)
    }, */

    updateExt(data) {
      Object.assign(this.formData.aditional_charges[data.index], {
        ...data.item,
      })
    },
    checkExtsData(index, isValid) {
      this.formData.aditional_charges[index].valid = isValid
    },

    checkExistExt(index, newCharge) {
      let pos = this.formData.aditional_charges.findIndex(
        (_item) => _item.id === newCharge.id
      )
      if (pos !== -1) {
        this.formData.aditional_charges.splice(index, 1)
      }
    },

    checkExistItem(index, newItem) {
      let pos = this.formData.items.findIndex(
        (_item) => _item.item_id === newItem.id
      )
      if (pos !== -1) {
        this.formData.items.splice(index, 1)
      }
    },
  },

  mounted() {
    this.$v.formData.$reset()
    if (this.isEdit || this.isCopy) {
      this.isRequestOnGoing = true
      this.loadExtension()
    }
  },

  validations: {
    formData: {
      name: {
        required,
      },
      status: {
        required,
      },
      status: {
        required,
      },
      /* status_payment: {
        required,
      }, */
      rate: {
        minValue: minValue(0.0),
      },
      description: {
        minLength: minLength(0),
      },
      minutes_cap: {
        numeric,
      },
      minutes_increments: {
        numeric,
      },
      /* type_time_increment: {
        required,
      }, */
      /* outbound_per_minute_rate: {
        minValue: minValue(0.0),
      }, */
      // inbound_per_minute_rate: {
      //   minValue: minValue(0.0),
      // },
      // extension_balance: {
      //   numeric,
      // },
      // minimum_extension_balance: {
      //   numeric,
      // },
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