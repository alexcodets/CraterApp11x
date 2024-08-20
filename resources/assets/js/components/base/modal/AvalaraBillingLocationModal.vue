<template>
  <form action="" @submit.prevent="submitAvalaraLocation">
    <div class="sm:p-6 grid md:grid-cols-2 mb-1" v-if="!isLoading">
      <sw-input-group
        :label="$tc('settings.company_info.country')"
        class="mt-2"
      >
        <sw-label class="text-xs text-gray-400 uppercase">
          {{ formData.country ? formData.country.name : '' }}
        </sw-label>
      </sw-input-group>

      <sw-input-group
        :label="$tc('settings.company_info.state')"
        class="ml-3 mt-2"
      >
        <sw-label class="text-xs text-gray-400 uppercase">
          {{ formData.state ? formData.state.name : '' }}
        </sw-label>
      </sw-input-group>

      <sw-input-group :label="$tc('settings.company_info.city')" class="mt-2">
        <!-- <sw-input
          v-if="!isLoading"
          v-model="formData.city"
          :placeholder="$tc('settings.company_info.city')"
          name="city"
          class="mt-2"
          type="text"
          :disabled="true"
        /> -->
        <sw-label class="text-xs text-gray-400 uppercase">
          {{ formData.city || '' }}
        </sw-label>
      </sw-input-group>

      <sw-input-group
        :label="$tc('settings.company_info.zip')"
        class="ml-3 mt-2"
      >
        <sw-label class="text-xs text-gray-400 uppercase">
          {{ formData.zip || '' }}
        </sw-label>
        <!-- <sw-input
          v-model="formData.zip"
          :placeholder="$tc('settings.company_info.zip')"
          class="mt-2"
          :disabled="true"
        /> -->
      </sw-input-group>
    </div>

    <sw-divider class="mb-3 md:mb-3" />

    <div class="p-6 sm:p-6">
      <sw-input-group
        :label="$t('avalara.billing_location_modal.locations')"
        variant=""
      >
        <div
          v-for="(location, indexTr) in modalData"
          :key="indexTr"
          class="mt-2"
        >
          <div v-if="location.valid">
            <input
              type="radio"
              :id="location.CountryIso"
              :value="location"
              v-model="locationSelected"
            />

            <label :for="location.CountryIso" class="ml-2" v-if="location.County != '' && location.Locality != ''">
              County: &nbsp; {{ location.County }} &nbsp;&nbsp;&nbsp;
              City: &nbsp; {{ location.Locality }} &nbsp;&nbsp;&nbsp;
              PCode: &nbsp;
              {{ location.PCode }}
            </label>

            <label :for="location.CountryIso" class="ml-2" v-else>
              County: &nbsp; N/A &nbsp;&nbsp;&nbsp;
              City: &nbsp; N/A &nbsp;&nbsp;&nbsp;
              PCode: &nbsp;
              {{ location.PCode }}
            </label>

          </div>
        </div>
      </sw-input-group>
    </div>
    <div
      class="
        z-0
        flex
        justify-end
        p-4
        border-t border-gray-200 border-solid border-modal-bg
      "
    >
      <sw-button
        type="button"
        variant="primary-outline"
        class="mr-3 text-sm"
        @click="closeAvalaraLocationsModal"
      >
        {{ $t('general.cancel') }}
      </sw-button>
      <sw-button
        variant="primary"
        type="submit"
        :loading="isLoading"
        :disabled="locationSelected.County == null ? true : false"
      >
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ !isEdit ? $t('general.save') : $t('general.update') }}
      </sw-button>
    </div>
  </form>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
// const { required, minLength, maxLength } = require('vuelidate/lib/validators')
export default {
  data() {
    return {
      states: [],
      // country: null,
      isEdit: false,
      isLoading: false,
      type_company: 0,
      locationSelected: {
        County: null,
        Country: null,
        State: null,
        City: null,
        Address: null,
        zip: null,
        Incorporated: null,
        PCode: null,
        Fips: null,
        Geo: null,
        Type: null,
        customerAvalaraLocationId: null,
      },
      formData: {
        billing_state: null,
        country: null,
        state: null,
        city: null,
        zip: null,
        type: null,
        location: {
          county: null,
          country: null,
          state: null,
          city: null,
          address: null,
          zip: null,
          incorporated: null,
          pcd: null,
          fips: null,
          geo: null,
          type: null,
        },
      },
    }
  },
  computed: {
      ...mapGetters('modal', [
        'modalDataID',
        'modalData',
        'modalCompany',
        'modalActive',
        'refreshData',
      ]),
      ...mapGetters(['countries']),
    },
    watch: {
      modalDataID(val) {
        if (val) {
          this.isEdit = true
          this.setData()
        } else {
          this.isEdit = false
        }
      },
      modalActive(val) {
        if (!this.modalActive) {
          this.resetFormData()
        }
      },
    },

    mounted() {
      // this.$refs.name.focus = true
      if (this.modalDataID) {
        this.isEdit = true
      }
      this.setData()
    },

    methods: {
      ...mapActions('modal', ['closeModal']),
      ...mapActions('avalara', ['saveAvalaraLocation', 'updateAddress', 'updateAddressLocation']),

      async setData() {        
        for (let i = 0; i < this.modalData.length; i++) {
          const element = this.modalData[i]
          if (i === 0) {
            // set country
            this.formData.country = element.company_geo_info
              ? element.company_geo_info.country
              : null
            // set state
            this.formData.state = element.company_geo_info
              ? element.company_geo_info.state
              : null
            // set city
            this.formData.city = element.company_geo_info
              ? element.company_geo_info.city
              : null
            // set zip
            this.formData.zip = element.company_geo_info
              ? element.company_geo_info.zip
              : null

            this.formData.type = element.company_geo_info
              ? element.company_geo_info.type
              : null
          }
        }
        this.isLoading = false
      },
      async submitAvalaraLocation() {

        this.isLoading = true
        let response
        let dataAvalaraLocation = {
          county: this.locationSelected.County != "" 
            ? this.locationSelected.County
            : this.modalData[0].company_geo_info.county,
          country: this.locationSelected.CountryIso,
          locality: this.locationSelected.Locality,
          state: this.locationSelected.State,
          city: this.locationSelected.Locality != "" 
            ? this.locationSelected.Locality
            : this.modalData[0].company_geo_info.city,
          address: this.locationSelected.address || '',
          zip: this.locationSelected.zip,
          incorporated: null,
          pcd: String(this.locationSelected.PCode),
          fips: null,
          geo: null,
          type: this.locationSelected.PCode != null
            ? 0
            : null,
          avalara_location_id: this.locationSelected.customerAvalaraLocationId,
          user_id: this.modalDataID,
        }

        const dataAddress = {
          id: this.modalData[0].company_geo_info.id,
          address_street_1: this.modalData[0].company_geo_info.address_street_1,
          address_street_2: this.modalData[0].company_geo_info.address_street_2,
          county: this.locationSelected.County != "" 
            ? this.locationSelected.County
            : this.modalData[0].company_geo_info.county,
          country_id: this.modalData[0].company_geo_info.country.id,
          city: this.locationSelected.Locality != "" 
            ? this.locationSelected.Locality
            : this.modalData[0].company_geo_info.city,
          state_id: this.modalData[0].company_geo_info.state.id,
          zip: this.locationSelected.zip || '',
          type: this.modalData[0].company_geo_info.type,
          pcode: String(this.locationSelected.PCode),
          user_id: this.modalDataID
        }

        dataAvalaraLocation.dataAddress = dataAddress

        // response = await this.saveAvalaraLocation(data) // save avalara location

        // validar que se esté editando el customer para salvar el address desde acá
        if (this.isEdit)
        {
          /*
          let responseAddress = await this.updateAddressLocation(dataAvalaraLocation) // save address
          if (responseAddress.data.success) {
            window.toastr['success'](
              this.$t('avalara.billing_location_modal.address_created_message')
            )            
          }
          */
          window.hub.$emit('save-address', dataAddress)
          this.refreshData ? this.refreshData() : ''
          this.closeAvalaraLocationsModal()
          this.isLoading = false
          return true

        }else{
          window.hub.$emit('save-address', dataAddress)
          // mutar AVALARA_LOCATION_SAVED
          //this.$store.commit('avalara/AVALARA_LOCATION_SAVED', dataAvalaraLocation)
          this.refreshData ? this.refreshData() : ''
          this.closeAvalaraLocationsModal()
          this.isLoading = false
        }        

        // if (response.data) {
        //   if (!this.isEdit) {
        //     window.toastr['success'](
        //       this.$t('avalara.billing_location_modal.created_message')
        //     )
        //   }
        //   window.hub.$emit('save-address', dataAddress)
        //   // emit refresh data
        //   this.refreshData ? this.refreshData() : ''
        //   this.closeAvalaraLocationsModal()
        //   this.isLoading = false
        //   return true
      // },
      // window.toastr['error'](response.data.error)
    },

    closeAvalaraLocationsModal() {
      // this.resetFormData()
      this.closeModal()
    },
  },
}
</script>
