<template>
  <div class="item-modal">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <form action="" @submit.prevent="submitData">
      <div
        class="px-8 py-10 sm:p-6 relative overflow-auto sw-scroll h-80 grid grid-cols-12 gap-4"
      >
        <sw-input-group
          :label="$t('pbx_services.did_number')"
          :error="numberError"
          class="col-span-12 md:col-span-6"
          required
        >
          <sw-input
            :invalid="$v.formData.number.$error"
            v-model="formData.number"
            type="text"
            @input="$v.formData.number.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('pbx_services.did_status')"
          class="col-span-12 md:col-span-6"
          :error="statusError"
          required
        >
          <sw-select
            :invalid="$v.formData.status.$error"
            v-model="formData.status"
            :options="statuses"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :max-height="200"
            :placeholder="$t('pbx_services.ext_select_status')"
            label="name"
            @input="$v.formData.status.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('pbx_services.did_dest_type')"
          :error="destTypeError"
          class="col-span-12 md:col-span-12 md:-mt-10"
          required
        >
          <sw-select
            v-model="formData.dest_type"
            :invalid="$v.formData.dest_type.$error"
            :options="typeDestOptions"
            @select="optionsSelectNumber"
            :searchable="true"
            :show-labels="false"
            :tabindex="2"
            :allow-empty="true"
            label="name"
            track-by="value"
            @input="$v.formData.dest_type.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          v-if="formData?.dest_type?.value !== 5"
          :label="$t('pbx_services.did_value')"
          :error="valueError"
          class="col-span-12 md:col-span-12 md:-mt-10"
          required
        >
          <sw-select
            :invalid="$v.formData.destination.$error"
            v-model="formData.destination"
            :options="optionsValue"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :max-height="200"
            label="label"
            @input="$v.formData.destination.$touch()"
          />
          <span v-if="messageError" class="text-red-600 text-xs">{{
            messageError
          }}</span>
        </sw-input-group>

        <sw-input-group
          v-if="formData?.dest_type?.value == 5"
          :label="$t('pbx_services.did_external_number')"
          :error="externalNumberError"
          class="col-span-12 md:col-span-12 md:-mt-10"
          required
        >
          <sw-input
            :invalid="$v.formData.external_number.$error"
            v-model="formData.external_number"
            type="text"
            @input="$v.formData.external_number.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('pbx_services.did_trunk')"
          class="col-span-12 md:col-span-12 md:-mt-10"
          
        >
          <sw-select
            v-model="formData.trunk"
            :options="trunkOptions"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :max-height="200"
            label="name"
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
      formData: {
        number: null,
        status: null,
        dest_type: null,
        destination: null,
        external_number: null,
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
          this.serviceID= this.modalData.pbx_service_id

          
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
      try {
        this.$v.formData.$touch()

        if (this.$v.$invalid) {
          return true
        }

       // console.log(this.formData)

       // console.log('Iniciando la validación de formData.dest_type')

        // Validar si 'dest_type' es un objeto y si tiene el campo 'value'  guardar extenal number en destination
        if (typeof this.formData.dest_type === 'object') {
          if (this.formData.dest_type?.name) {
            // Verificar si el contenido de 'value' es 'External Number'
            if (this.formData.dest_type.name === 'External Number') {
              // Si se cumple la condición, asignar 'external_number' a 'destination.value'
              this.$set(this.formData, 'destination', {
                value: this.formData.external_number,
              })
            } else {
            }
          } else {
          }
        } else {
        }

       // console.log(this.formData)
        let payload = {
          pbx_service_id:this.formData.pbx_service_id,
          pbx_server_id: this.componentID,
          pbx_did_id: this.modalDataID,
          data: {
            ...this.formData,
            dest_type: this.formData?.dest_type?.value,
            destination: this.formData?.destination?.value,
            disabled: this.formData?.status?.value,
            trunk:this.formData?.trunk?.key,
          },
        }

        //console.log(payload)
        this.isRequestOnGoing = true
        let response = await this.createDids(payload)
        if (response?.success) {
          window.toastr['success'](this.$tc('pbx_services.did_created_success'))
          if (this.viewType == 'viewPBX') {
            window.hub.$emit('updateExt')
            this.refreshData ? this.refreshData() : ''
            this.$router.go()
          } else {
            window.hub.$emit('updateListDid')
          }
          this.formData.status = this.formData.status.value
          this.isRequestOnGoing = false
          this.closeExtensionModal()
          return true
        } else {
          window.toastr['error'](response.message)
          if (response.data) {
            let errorsShow = this.transformedDataError(response.data)
            errorsShow.forEach((element) => {
              window.toastr['error'](element)
            })
          }
        }

        this.isRequestOnGoing = false
      } catch (error) {
        console.log(error)
      } finally {
        this.isRequestOnGoing = false
      }
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

    closeExtensionModal() {
      this.resetFormData()
      this.closeModal()
      this.resetModalData()
    },
  },
}
</script>
