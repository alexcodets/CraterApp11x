<template>
  <div class="item-modal">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <form action="" @submit.prevent="submitData">
      <div
        class="px-8 py-10 sm:p-6 relative overflow-auto sw-scroll h-96 grid grid-cols-12 gap-5"
      >
        <sw-input-group
          :label="$t('pbx_services.ext_name')"
          :error="nameError"
          class="col-span-12 md:col-span-12"
          variant="horizontal"
          required
        >
          <sw-input
            :invalid="$v.formData.name.$error"
            v-model="formData.name"
            type="text"
            @input="$v.formData.name.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('pbx_services.ext_voicemail_pin')"
          :error="pinError"
          class="col-span-12 md:col-span-12"
          variant="horizontal"
          required
        >
          <sw-input
            :invalid="$v.formData.pin.$error"
            v-model="formData.pin"
            type="text"
            @input="$v.formData.pin.$touch()"
            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
            pattern="[0-9]{4}"
            maxlength="4"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('pbx_services.ext_protocol')"
          :error="protocolError"
          class="col-span-12 md:col-span-12"
          variant="horizontal"
          required
        >
          <sw-select
            v-model="formData.protocol"
            :invalid="$v.formData.protocol.$error"
            :options="potrocolOptions"
            :searchable="true"
            :show-labels="false"
            :tabindex="2"
            :allow-empty="true"
            label="name"
            track-by="value"
            @input="$v.formData.protocol.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('login.email')"
          :error="emailError"
          class="col-span-12 md:col-span-12"
          variant="horizontal"
          required
        >
          <sw-input
            :invalid="$v.formData.email.$error"
            v-model.trim="formData.email"
            type="text"
            name="email"
            class="mt-1 md:mt-0"
            @input="$v.formData.email.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('pbx_services.ext_extension')"
          :error="extError"
          class="col-span-12 md:col-span-12"
          variant="horizontal"
          required
        >
          <sw-input
            :invalid="$v.formData.ext.$error"
            v-model="formData.ext"
            type="text"
            @input="$v.formData.ext.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('pbx_services.ext_uad')"
          class="col-span-12 md:col-span-12"
          variant="horizontal"
        >
          <sw-select
            v-model="formData.ua_id"
            :options="uadOptions"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :max-height="200"
            label="name"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('pbx_services.ext_uad_location')"
          class="col-span-12 md:col-span-12"
          :error="uadLocationError"
          variant="horizontal"
          required
        >
          <sw-select
            :invalid="$v.formData.location.$error"
            v-model="formData.location"
            :options="uadLocationOptions"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :max-height="200"
            label="name"
            @input="$v.formData.location.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('pbx_services.ext_status')"
          class="col-span-12 md:col-span-12"
          variant="horizontal"
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
          :label="$t('pbx_services.ext_auto_provisioning')"
          class="col-span-12 md:col-span-12"
          variant="horizontal"
        >
          <sw-switch
            v-model="formData.auto_provisioning"
            class="-mt-3"
            @change="changeAutoProvisioning"
          />
        </sw-input-group>

        <sw-input-group
          v-if="formData.auto_provisioning"
          :label="$t('pbx_services.ext_mac_address')"
          :error="macAddressError"
          class="col-span-12 md:col-span-12"
          variant="horizontal"
          required
        >
          <sw-input
            :invalid="$v.formData.mac_address.$error"
            v-model="formData.mac_address"
            type="text"
            maxLength="12"
            @input="$v.formData.mac_address.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          v-if="formData.auto_provisioning"
          :label="$t('pbx_services.ext_time_zone')"
          class="col-span-12 md:col-span-12"
          variant="horizontal"
        >
          <sw-select
            v-model="formData.time_zone"
            :options="optionsTimeZone"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :max-height="200"
            label="name"
          />
          <span
            v-if="formData.auto_provisioning"
            class="text-yellow-700 text-xs"
            >{{ $t('pbx_services.ext_time_zone_message_warning') }}</span
          >
        </sw-input-group>

        <sw-input-group
          v-if="formData.auto_provisioning"
          :label="$t('pbx_services.ext_DHCP')"
          class="col-span-12 md:col-span-12"
          variant="horizontal"
        >
          <sw-switch v-model="formData.dhcp" class="-mt-3" />
        </sw-input-group>

        <sw-input-group
          v-if="formData.dhcp == 0"
          :label="$t('pbx_services.ext_IP_static')"
          :error="staticIpError"
          class="col-span-12 md:col-span-12"
          variant="horizontal"
        >
          <sw-input
            :invalid="$v.formData.static_ip.$error"
            v-model="formData.static_ip"
            type="text"
            @input="$v.formData.static_ip.$touch()"
          />

          <span v-if="formData.dhcp == 0" class="text-yellow-700 text-xs">{{
            $t('pbx_services.ext_IP_static_message_warning')
          }}</span>
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
      isRequestOnGoing: false,
      isNew: false,
      viewType: '',
      componentID: null,
      uadOptions: [],
      formData: {
        name: null,
        email: null,
        ext: null,
        ua_id: null,
        location: null,
        status: null,
        protocol: null,
        pin: null,
        auto_provisioning: false,
        dhcp: true,
        mac_address: null,
        time_zone: null,
        static_ip: null,
      },
      statuses: [
        { name: 'Enabled', value: 'enabled' },
        { name: 'Disabled', value: 'disabled' },
      ],
      potrocolOptions: [
        { name: 'Sip', value: 'sip' },
        { name: 'Iax', value: 'iax' },
      ],
      optionsTimeZone: [
        { name: 'System default', value: null },
        { name: 'UTC', value: 'utc' },
      ],
      uadLocationOptions: [
        { name: 'Local', value: 'local' },
        { name: 'Remote', value: 'remote' },
      ],
    }
  },
  validations() {
    return {
      formData: {
        name: {
          required,
        },
        email: {
          required,
          email,
        },
        ext: {
          required,
          numeric,
        },
        location: {
          required,
        },
        status: {
          required,
        },
        protocol: {
          required,
        },
        pin: {
          required,
          min: minLength(4),
        },
        mac_address: {
          required: requiredIf(() => this.formData.auto_provisioning),
          min: minLength(12),
        },
        static_ip: {
          required: requiredIf(() => this.formData.dhcp == 0),
        },
      },
    }
  },
  computed: {
    ...mapGetters('modal', ['modalDataID', 'modalData', 'modalActive']),

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },

    emailError() {
      if (!this.$v.formData.email.$error) {
        return ''
      }
      if (!this.$v.formData.email.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },

    extError() {
      if (!this.$v.formData.ext.$error) {
        return ''
      }
      if (!this.$v.formData.ext.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.ext.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },

    uadLocationError() {
      if (!this.$v.formData.location.$error) {
        return ''
      }
      if (!this.$v.formData.location.required) {
        return this.$tc('validation.required')
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

    protocolError() {
      if (!this.$v.formData.protocol.$error) {
        return ''
      }
      if (!this.$v.formData.protocol.required) {
        return this.$tc('validation.required')
      }
    },

    pinError() {
      if (!this.$v.formData.pin.$error) {
        return ''
      }
      if (!this.$v.formData.pin.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.pin.min) {
        return this.$tc('validation.min_max_characters_pin')
      }
    },

    macAddressError() {
      if (this.formData.auto_provisioning) {
        if (!this.$v.formData.mac_address.$error) {
          return ''
        }
        if (!this.$v.formData.mac_address.required) {
          return this.$tc('validation.required')
        }
        if (!this.$v.formData.mac_address.min) {
          this.clearMacAddress()
          return this.$tc('validation.min_max_characters_mac_address')
        }
      }
    },

    staticIpError() {
      if (this.formData.dhcp == 0) {
        if (!this.$v.formData.static_ip.$error) {
          return ''
        }
        if (!this.$v.formData.static_ip.required) {
          return this.$tc('validation.required')
        }
      }
    },
  },
  async mounted() {
    await this.setInitialData()
    console.log('setInitialData completado')
    this.optionsUAD()
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData', 'refreshData']),
    ...mapActions('tenants', ['updateExtensions', 'selectUserAgentDevicesExt']),

    ...mapActions('modules', [
      'addServer',
      'getContienents',
      'getZoneByContinent',
      'testServerConection',
      'getTimeZone',
    ]),

    changeAutoProvisioning(value) {
      if (!value) {
        this.formData.dhcp = true
      }
    },

    async clearMacAddress() {
      const mac_address = await this.formData.mac_address
      const regex = /[^a-zA-Z0-9]/g
      this.formData.mac_address = mac_address.replace(regex, '')
    },

    setInitialData() {
      return new Promise(async (resolve, reject) => {
        try {
          // Obtener la lista de zonas horarias de forma asíncrona
          let timezonelist = await this.getTimeZone()
         // console.log("linea 474")
          // Añadir las zonas horarias a las opciones disponibles
          this.optionsTimeZone.push(
            ...timezonelist.timezones.map((item) => {
              return {
                name: item,
                value: item,
              }
            })
          )

          // Verificar si modalData está presente
          if (this.modalData) {
            // Asignaciones directas de valores desde modalData
           // console.log("linea 488")
            this.formData.name = this.modalData.name
            this.formData.email = this.modalData.email
            this.formData.ext = this.modalData.ext
            this.formData.ua_id = this.modalData.ua_id
            this.formData.location = this.modalData.location
            this.formData.auto_provisioning = this.modalData.auto_provisioning
            this.formData.static_ip = this.modalData.static_ip
            this.formData.pin = this.modalData.pin
            this.isNew = this.modalData.isNew
            this.formData.dhcp = this.modalData.dhcp
            this.formData.mac_address = this.modalData.macaddress
            this.formData.time_zone = this.modalData.time_zone

            // Asignar la ubicación basada en modalData o por defecto
            if (this.modalData.location) {
             // console.log("linea 504")
              this.formData.location = this.uadLocationOptions.find(
                (status) => this.modalData.location === status.value
              )
            } else {
             // console.log("linea 509")
              this.formData.location = this.uadLocationOptions.find(
                (status) => status.value === 'remote'
              )
            }

            // Asignar la zona horaria basada en modalData o por defecto
            if (this.modalData.time_zone) {
              //console.log("linea 517")
              this.formData.time_zone = this.optionsTimeZone.find(
                (_time_zone) => {
                  return this.modalData.time_zone === _time_zone.value
                }
              )
            } else {
              //console.log("linea 524")
              this.formData.time_zone = this.optionsTimeZone.find(
                (time_zone) => time_zone.value === null
              )
            }

            // Asignar el estado y el protocolo basados en modalData
           // console.log("linea 531")
            this.formData.status = this.statuses.find(
              (status) => this.modalData.status === status.value
            )
            //console.log("linea 534")
            this.formData.protocol = this.potrocolOptions.find(
              (protocol) => this.modalData.protocol === protocol.value
            )

            // Asignaciones condicionales para IP estática y PIN
            if (this.modalData.static_ip) {
             // console.log("linea 542")
              this.formData.static_ip = this.modalData.static_ip
            }
            if (this.modalData.pin) {
             // console.log("linea 546")
              this.formData.pin = this.modalData.pin
            }

            // Asignar isNew y viewType basados en modalData
            this.isNew = this.modalData.isNew
            this.viewType = this.modalData.from
            //console.log("linea 553")
            // Asignar componentID basado en modalData o $route.params
            if (this.modalData.pbxservertenant_id) {
              //console.log("linea 556")
              this.componentID = this.modalData.pbxservertenant_id
            } else {
              //console.log("linea 559")
              this.componentID = this.$route.params.id
            }

            // Logs para depuración
            //console.log('Tipo de vista:', this.viewType)
           // console.log('ID del componente:', this.componentID)

            // Resolver la promesa con el formData actualizado
            resolve(this.formData)
          } else {
            // Si no hay modalData, resolver la promesa con un valor por defecto
            resolve({})
          }
        } catch (error) {
          // En caso de error, rechazar la promesa
          reject(error)
        }
      })
    },

    resetFormData() {
      this.formData = {
        name: null,
        email: null,
        ext: null,
        ua_id: null,
        location: null,
        status: null,
        protocol: null,
        pin: null,
        auto_provisioning: false,
        dhcp: true,
        mac_address: null,
        time_zone: null,
        static_ip: null,
      }
      this.$v.$reset()
    },

    onlyNumber($event) {
      let keyCode = $event.keyCode ? $event.keyCode : $event.which
      if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
        $event.preventDefault()
      }
    },

    transformedDataError(data) {
      if (typeof data === 'string') return []
      const values = Object.values(data)

      const flatValues = values.flat()

      return flatValues
    },

    async submitData() {
      try {
        await this.$v.formData.$touch()

        if (this.$v.$invalid) {
          return true
        }

        if (this.formData.auto_provisioning === false) {
          this.formData.auto_provisioning = 0
        }

        if (this.formData.dhcp === false) {
          this.formData.dhcp = 0
        }

        if (this.formData.auto_provisioning === false) {
          this.formData.auto_provisioning = 0
        }

        if (this.formData.dhcp === false) {
          this.formData.dhcp = 0
        }

        let payload = {
          pbx_server_id: this.componentID,
          pbx_ext_id: this.modalDataID,
          data: {
            ...this.formData,
            protocol: this.formData?.protocol?.value,
            status: this.formData?.status?.value,
            time_zone: this.formData?.time_zone?.value,
            ua_id: this.formData?.ua_id?.key,
            location: this.formData?.location?.value,
          },
        }

        this.isRequestOnGoing = true
        let response = await this.updateExtensions(payload)
        if (response.data?.success) {
          window.toastr['success'](this.$tc('pbx_services.ext_update_success'))

          if (this.viewType == 'viewPBX') {
            window.hub.$emit('updateExt')
            this.refreshData ? this.refreshData() : ''
            this.$router.go()
          } else {
            window.hub.$emit('updateListExt')
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

    async optionsUAD() {
      try {
        //console.log('Valor actual de this.componentID:', this.componentID)
        
        let response = await this.selectUserAgentDevicesExt(this.componentID)
       // console.log('Respuesta recibida:', response)

      //  console.log('Transformando los datos recibidos...')
        this.uadOptions = await this.transformedData(response.data)
      //  console.log('Datos transformados:', this.uadOptions)

        if (this.modalData.ua_id) {
         // console.log('Buscando ua_id en las opciones transformadas...')
          this.formData.ua_id = this.uadOptions.find(
            (status) => this.modalData.ua_id == status.key
          )
         // console.log('ua_id encontrado:', this.formData.ua_id)
        } else {
          //console.log('No se proporcionó ua_id en modalData.')
        }
      } catch (error) {
        console.error(
          'Se ha producido un error al obtener los dispositivos de agente de usuario:',
          error
        )
      }
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
              fullname: item.fullname,
              enabled: item.enabled,
            })
          }
        }
        return result
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
