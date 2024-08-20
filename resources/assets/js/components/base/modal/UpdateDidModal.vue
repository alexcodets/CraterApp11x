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
      formData: {
        number: null,
        status: null,
        dest_type: null,
        destination: null,
        external_number: null,
        ext: null,
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
        { name: 'Enabled', value: 'enabled' },
        { name: 'Disabled', value: 'disabled' },
      ],
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
  mounted() {
    this.setInitialData()
    if (this.formData.dest_type) {
      this.optionsSelectNumber(this.formData.dest_type)
    }
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
    ]),

    setInitialData() {
      if (this.modalData) {
        //console.log(this.modalData)
        this.formData.number = this.modalData.number
        this.formData.status = this.statuses.find(
          (_status) => this.modalData.status === _status.value
        )
        this.formData.dest_type = this.typeDestOptions.find(
          (_type) => this.modalData.type === _type.name
        )
        this.formData.destination = this.modalData.destination
        this.formData.external_number = this.modalData.external_number

        // Validar si 'dest_type' es un objeto y si tiene el campo 'value'  guardar extenal number en destination
        if (typeof this.formData.dest_type === 'object') {
          if (this.formData.dest_type?.name) {
            // Verificar si el contenido de 'value' es 'External Number'
            if (this.formData.dest_type.name === 'External Number') {
              // Si se cumple la condición, asignar 'external_number' a 'destination.value'
              if (this.modalData.ext) {
                this.formData.external_number = this.modalData.ext
              } else {
                this.formData.external_number = this.modalData.external_number
              }
            } else {
            }
          } else {
          }
        } else {
        }

        this.viewType = this.modalData.from

        if (this.modalData.ext) {
          this.formData.ext = this.modalData.ext
        }

        if (this.modalData.pbxservertenant_id) {
          this.componentID = this.modalData.pbxservertenant_id
        } else {
          this.componentID = this.$route.params.id
        }

       
      }
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

    async submitData() {
      try {
        this.$v.formData.$touch()

        if (this.$v.$invalid) {
          return true
        }

      //  console.log(this.formData)

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
          pbx_server_id: this.componentID,
          pbx_did_id: this.modalDataID,
          data: {
            ...this.formData,
            dest_type: this.formData?.dest_type?.value,
            destination: this.formData?.destination?.value,
            status: this.formData?.status?.value,
          },
        }
        this.isRequestOnGoing = true
        let response = await this.updateDids(payload)
        if (response.data?.success) {
          window.toastr['success'](this.$tc('pbx_services.ext_update_success'))
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
          //console.log('Iniciando la validación de response.data...')
          if (Array.isArray(response.data) && response.data.length) {
           
            this.optionsValue = response.data

          //  console.log('Validando this.formData.ext...')
            // Convertir this.formData.ext a número si es un string que representa un número
            let ext = isNaN(this.formData.ext)
              ? this.formData.ext
              : Number(this.formData.ext)
           // console.log(`this.formData.ext convertido a ${typeof ext}:`, ext)

            if (ext !== null) {
           
              for (let option of this.optionsValue) {
               // console.log('Verificando opción:', option)
                if (option.label && option.value) {
                
                  if (ext === option.value) {
                   
                    this.formData.destination = {
                      label: option.label,
                      value: option.value,
                    }
                   
                    break // Salir del bucle una vez que se encuentra la coincidencia
                  }
                }
              }
            } else {
             // console.log('this.formData.ext es NULL o no existe.')
            }
          } else {
            //console.log('response.data no es un array o está vacío.')
          }
        } else if (server.value == 2) {
          response = await this.selectRingGroupsDid(this.componentID)
         // console.log('Iniciando la validación de response.data...')
          if (Array.isArray(response.data) && response.data.length) {
           
            this.optionsValue = response.data

          //  console.log('Validando this.formData.ext...')
            let ext = isNaN(this.formData.ext)
              ? this.formData.ext
              : Number(this.formData.ext)
           // console.log(`this.formData.ext convertido a ${typeof ext}:`, ext)

            if (ext !== null) {
             
              for (let option of this.optionsValue) {
              //  console.log('Verificando opción:', option)

                // Validar que option.value sea un número entero, si no, intentar convertirlo
                let value = option.value
                if (value !== '' && !Number.isInteger(value)) {
                  value = parseInt(value, 10)
                 // console.log(`option.value convertido a entero:`, value)
                }

                if (option.label && value) {
                  
                  if (ext === value) {
                    
                    this.formData.destination = {
                      label: option.label,
                      value: value,
                    }
                   
                    break // Salir del bucle una vez que se encuentra la coincidencia
                  }
                }
              }
            } else {
             // console.log('this.formData.ext es NULL o no existe.')
            }
          } else {
            //console.log('response.data no es un array o está vacío.')
          }
        } else if (server.value == 3) {
          response = await this.selectIvrDid(this.componentID)
         // console.log('Iniciando la validación de response.data...')
          if (Array.isArray(response.data) && response.data.length) {
           
            this.optionsValue = response.data

          //  console.log('Validando this.formData.ext...')
            let ext = isNaN(this.formData.ext)
              ? this.formData.ext
              : Number(this.formData.ext)
          //  console.log(`this.formData.ext convertido a ${typeof ext}:`, ext)

            if (ext !== null) {
              
              for (let option of this.optionsValue) {
                //console.log('Verificando opción:', option)

                // Validar que option.value sea un número entero, si no, intentar convertirlo
                let value = option.value
                if (value !== '' && !Number.isInteger(value)) {
                  value = parseInt(value, 10)
                  //console.log(`option.value convertido a entero:`, value)
                }

                if (option.label && value) {
                 
                  if (ext === value) {
                    
                    this.formData.destination = {
                      label: option.label,
                      value: value,
                    }
                    
                    break // Salir del bucle una vez que se encuentra la coincidencia
                  }
                }
              }
            } else {
             // console.log('this.formData.ext es NULL o no existe.')
            }
          } else {
            //console.log('response.data no es un array o está vacío.')
          }
        } else if (server.value == 4) {
          response = await this.selectQueuesDid(this.componentID)
         // console.log('Iniciando la validación de response.data...')
          if (Array.isArray(response.data) && response.data.length) {
          
            this.optionsValue = response.data

          //  console.log('Validando this.formData.ext...')
            // Convertir this.formData.ext a número si es un string que representa un número
            let ext = isNaN(this.formData.ext)
              ? this.formData.ext
              : Number(this.formData.ext)
            //console.log(`this.formData.ext convertido a ${typeof ext}:`, ext)

            if (ext !== null) {
             
              for (let option of this.optionsValue) {
                //console.log('Verificando opción:', option)
                if (option.label && option.value) {
                  
                  if (ext === option.value) {
                    
                    this.formData.destination = {
                      label: option.label,
                      value: option.value,
                    }
              
                    break // Salir del bucle una vez que se encuentra la coincidencia
                  }
                }
              }
            } else {
            //  console.log('this.formData.ext es NULL o no existe.')
            }
          } else {
          //  console.log('response.data no es un array o está vacío.')
          }
          this.messageError = response.message
        } else {
          this.optionsValue = []
        }
      } catch (error) {
        //console.log(error)
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
