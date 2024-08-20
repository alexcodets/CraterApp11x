<template>
  <div class="item-modal">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <form action="" @submit.prevent="submitData">
      <div
        class="px-8 py-10 sm:p-6 relative overflow-auto sw-scroll h-80 grid grid-cols-1 gap-4"
      >
        <sw-input-group :label="$t('corePbx.items')">
          <sw-select
            ref="baseSelect"
            v-model="item"
            :options="items"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('expenses.item_select')"
            class="mt-1"
            label="name"
            track-by="id"
            @select="optionsSelect"
          />
          <!-- Leyenda agregada debajo del componente sw-select -->
          <!-- Leyenda en cursiva agregada debajo del componente sw-select -->
          <!-- Leyenda en cursiva con padding agregado -->
          <em class="text-sm text-gray-600 mt-1 px-2">
            {{ $t('packages.warning_item') }}
          </em>
        </sw-input-group>

        <sw-input-group :label="$t('items.name')" class="mt-4 pr-3" required>
          <sw-input
            v-model.trim="formData.name"
            class="mt-2"
            focus
            type="text"
            name="name"
            :tabindex="1"
          />
        </sw-input-group>

        <sw-input-group :label="$t('users.description')" class="mt-4 pr-3">
          <sw-input
            v-model.trim="formData.description"
            class="mt-2"
            focus
            type="text"
            name="description"
            :tabindex="1"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('items.price')"
          class="mt-4 pr-3"
          variant="vertical"
          required
        >
          <sw-money
            v-model.trim="price"
            :currency="defaultCurrencyForInput"
            :tabindex="2"
            class="relative w-full focus:border focus:border-solid focus:border-primary-500"
          />
        </sw-input-group>

        <div class="flex mb-4">
          <div class="relative w-12">
            <sw-switch
              v-model="formData.end_period_act"
              class="absolute"
              style="top: -18px"
            />
          </div>

          <div class="ml-4">
            <p class="p-0 mb-1 text-base leading-snug text-black">
              {{
                $t('settings.customization.customer.enable_addcredit_payment')
              }}
            </p>

            <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
              {{
                $t(
                  'settings.customization.customer.enable_addcredit_payment_desc'
                )
              }}
            </p>
          </div>
        </div>

        <sw-input-group
          v-if="formData.end_period_act == true || formData.end_period_act == 1"
          :label="$t('packages.end_period_num')"
          class="mt-4 pr-3"
          variant="vertical"
          required
        >
          <sw-input
            v-model="formData.end_period_number"
            type="number"
            min="1"
            small
            onkeydown="javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) ? true : !isNaN(Number(event.key)) && event.code!=='Space'"
          />
        </sw-input-group>
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <sw-button
          class="mr-3"
          variant="primary-outline"
          type="button"
          @click="closeExtensionModal"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button variant="primary" type="submit">
          <save-icon class="mr-2" />
          {{ $t('general.save') }}
        </sw-button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const {
  required,
  minLength,
  numeric,
  maxLength,
  minValue,
  email,
  requiredIf,
} = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      isLoading: false,
      isRequestOnGoing: false,
      optionsGroupDid: [],
      optionsValue: [],
      messageError: '',
      viewType: '',
      componentID: null,
      serviceID: null,
      item: null,
      formData: {
        items_id: null,
        name: null,
        description: null,
        price: null,
        external_number: null,
        end_period_act: false,
        end_period_number: 1,
        pbx_service_id: null,
        trunk: null,
      },
      typeDestOptions: [
        { name: 'Extension', value: 0 },
        { name: 'Forward DID to Extension (Mul User)', value: 1 },
        { name: 'Ring Group', value: 2 },
        { name: 'IVR', value: 3 },
        { name: 'Queues', value: 4 },
        { name: 'External Number', value: 5 },
        { name: 'IVR tree', value: 6 },
      ],
      disabledOptions: [
        { name: 0, value: 0 },
        { name: 1, value: 1 },
      ],
      statuses: [
        { name: 'Enabled', value: 0 },
        { name: 'Disabled', value: 1 },
      ],
      trunkOptions: [],
    }
  },
  validations: {
    formData: {
      number: {
        required,
        numeric,
      },
      status: {
        required,
      },
      dest_type: {
        required,
      },
      destination: {
        requiredIf: requiredIf(function () {
          return this.formData?.dest_type?.value !== 5
        }),
      },
      external_number: {
        numeric,
        requiredIf: requiredIf(function () {
          return this.formData?.dest_type?.value == 5
        }),
      },
    },
  },
  computed: {
    ...mapGetters('modal', ['modalDataID', 'modalData', 'modalActive']),
    ...mapGetters('item', ['items']),
    ...mapGetters('company', ['defaultCurrencyForInput']),

    price: {
      get: function () {
        return this.formData.price / 100
      },
      set: function (newValue) {
        this.formData.price = Math.round(newValue * 100)
      },
    },

    numberError() {
      if (!this.$v.formData.number.$error) {
        return ''
      }
      if (!this.$v.formData.number.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.number.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },

    statusError() {
      if (!this.$v.formData.status.$error) {
        return ''
      }
      if (!this.$v.formData.status.required) {
        return this.$tc('validation.required')
      }
    },

    destTypeError() {
      if (!this.$v.formData.dest_type.$error) {
        return ''
      }
      if (!this.$v.formData.dest_type.required) {
        return this.$tc('validation.required')
      }
      // if (!this.$v.formData.dest_type.numeric) {
      //   return this.$tc('validation.numbers_only')
      // }
    },

    valueError() {
      if (!this.$v.formData.destination.$error) {
        return ''
      }
      if (!this.$v.formData.destination.required) {
        return this.$tc('validation.required')
      }
    },

    externalNumberError() {
      if (!this.$v.formData.external_number.$error) {
        return ''
      }
      if (!this.$v.formData.external_number.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.external_number.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
  },
  async mounted() {
    await this.setInitialData()
    if (this.formData.dest_type) {
      this.optionsSelectNumber(this.formData.dest_type)
    }
    await this.fetchItems({ limit: 'all' })
    this.optionstrunks()
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData', 'refreshData']),
    ...mapActions('tenants', [
      'updateDids',
      'selectGroupDid',
      'selectExtensionDid',
      'selectRingGroupsDid',
      'selectIvrDid',
      'selectQueuesDid',
      'selectTrunksDids',
      'createDids',
    ]),
    ...mapActions('item', ['fetchItems']),
    ...mapActions('pbxService', ['addPbxServicesItem']),

    setInitialData() {
      return new Promise((resolve, reject) => {
        if (this.modalData) {
          this.viewType = this.modalData.from

          if (this.modalData.pbxservertenant_id) {
            this.componentID = this.modalData.pbxservertenant_id
          } else {
            this.componentID = this.$route.params.id
          }
          this.formData.pbx_service_id = this.modalData.pbx_service_id
          this.serviceID = this.modalData.pbx_service_id

          
          resolve({
            viewType: this.viewType,
            componentID: this.componentID,
            formData: this.formData,
          })
        } else {
          reject(new Error('No modal data available'))
        }
      })
    },

    resetFormData() {
      ;(this.formData = {
        number: null,
        status: null,
        dest_type: null,
        destination: null,
        external_number: null,
      }),
        this.$v.$reset()
    },

    transformedDataError(data) {
      if (typeof data === 'string') return []
      const values = Object.values(data)

      const flatValues = values.flat()

      return flatValues
    },

    async optionstrunks() {
      let response = await this.selectTrunksDids(this.serviceID)

      this.trunkOptions = await this.transformedData(response.data)
    },

    async transformedData(data) {
      try {
        const result = []
        for (const code in data) {
          if (data.hasOwnProperty(code)) {
            const item = data[code]
            result.push({
              key: parseInt(code, 10),
              name: item.name,
              provider_name: item.provider_name,
              enabled: item.enabled,
            })
          }
        }
        return result
      } catch (error) {
        console.log(error)
      }
    },

    async submitData() {
     // console.log(this.formData)
     // console.log(this.item)
      this.isRequestOnGoing = true

      this.isRequestOnGoing = true

      if (!this.validateFormData()) {
        this.isRequestOnGoing = false
        return false
      }

      this.prepareFormData()
     // console.log(this.formData)

      let response = await this.addPbxServicesItem(this.formData)

     // console.log(response)

      if (response.data.success) {
        window.toastr['success'](this.$tc('pbx_services.item_added_success'))
        this.isRequestOnGoing = false
        this.$router.go()
        this.closeExtensionModal()
      } else {
        this.isRequestOnGoing = false
        window.toastr['error'](response.data.message)
      }
    },

    // Función para validar los datos del formulario
    validateFormData() {
      // Validación del nombre en el formulario
      if (!this.formData.name || this.formData.name.length < 3) {
        this.showToastrError('settings.customization.warning_wrong_format_name')
        return false
      }

      // Validación del precio en el formulario
      if (
        this.formData.price === null ||
        this.formData.price === 0 ||
        typeof this.formData.price !== 'number'
      ) {
        this.showToastrError(
          'settings.customization.warning_wrong_format_price'
        )
        return false
      }

      // Si todas las validaciones son correctas, retorna verdadero
      return true
    },
    // Función para preparar los datos del formulario antes de la solicitud
    prepareFormData() {
      // Asignación del ID del ítem si existe, si no, null
      this.formData.items_id = this.item ? this.item.id : null
      // Asignación del ID del servicio de PBX
      this.formData.pbx_service_id = this.serviceID
    },
    // Función para mostrar notificaciones de error
    showToastrError(message) {
      window.toastr['error'](this.$t(message))
    },

    async optionsSelectNumber(server) {
      try {
        this.optionsValue = []
        this.formData.destination = []
        this.messageError = ''
        let response
        if (server.value == 0) {
          response = await this.selectExtensionDid(this.componentID)
          this.optionsValue = response.data
        } else if (server.value == 2) {
          response = await this.selectRingGroupsDid(this.componentID)
          this.optionsValue = response.data
        } else if (server.value == 3) {
          response = await this.selectIvrDid(this.componentID)
          this.optionsValue = response.data
        } else if (server.value == 4) {
          response = await this.selectQueuesDid(this.componentID)
          this.optionsValue = response.data
          this.messageError = response.message
        } else {
          this.optionsValue = []
        }
      } catch (error) {
        console.log(error)
      }
    },

    async optionsSelect(val) {
     // console.log(val)
      this.formData.name = val ? val.name : ''
      this.formData.price = val ? val.price : 0
    },

    closeExtensionModal() {
      this.resetFormData()
      this.closeModal()
      this.resetModalData()
    },
  },
}
</script>
