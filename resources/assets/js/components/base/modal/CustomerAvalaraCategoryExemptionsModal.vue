<template>
  <div>
    <!-- baseloader -->
    <base-loader v-if="isLoading" />

    <div class="p-6 grid md:grid-cols-2 gap-4 mb-1">
      <div>
        <p class="mb-1 text-sm font-bold">
          {{ $t('settings.company_info.country') }}
        </p>
        <p class="text-sm">
          {{ dataModal.country ? dataModal.country.name : '' }}
        </p>
      </div>

      <div>
        <p class="mb-1 text-sm font-bold">
          {{ $t('settings.company_info.state') }}
        </p>
        <p class="text-sm">
          {{ dataModal.state ? dataModal.state.name : '' }}
        </p>
      </div>

      <div>
        <p class="mb-1 text-sm font-bold">
          {{ $t('settings.company_info.city') }}
        </p>
        <p class="text-sm">
          {{ dataModal.city || '' }}
        </p>
      </div>

      <div>
        <p class="mb-1 text-sm font-bold">
          {{ $t('avalara.pcode') }}
        </p>
        <p class="text-sm">
          {{ dataModal.pcode || '' }}
        </p>
      </div>

      <div>
        <sw-button variant="primary" @click="showModalException">
          {{ $t('avalara.add_exemptions') }}
        </sw-button>
      </div>
    </div>

        <div class="overflow-auto h-80">
           <sw-table-component
            ref="table"
            :show-filter="false"
            :data="exemptionsCategorys"
            table-class="table"
          >
       <!-- <sw-table-column
              :sortable="false"
              :label="$tc('avalara.force')"
              show="frc"
            >
              <template slot-scope="row">
                <span>{{ $tc('avalara.force') }}</span>
                {{ row.frc !== null ? frcOptions[row.frc] : '' }}
              </template>
            </sw-table-column> -->


            <sw-table-column
              :sortable="false"
              :label="$tc('avalara.exemption_name')"
              show="name"
            >
              <template slot-scope="row">
                <span>{{ $tc('avalara.exemption_name') }}</span>
                {{ row.exemption_name !== null ? row.exemption_name : '' }}
              </template>
            </sw-table-column>
            <sw-table-column
              :sortable="false"
              :label="$t('avalara.exemption_scope')"
              show="scp"
            >
              <template slot-scope="row">
                <span>{{ $t('avalara.exemption_scope') }}</span>
                {{ row.scp !== null ? scpOptions[row.scp] : '' }}
              </template>
            </sw-table-column>

            <sw-table-column
              :sortable="false"
              :label="$t('avalara.exempt_non-billable')"
              show="exnb"
            >
              <template slot-scope="row">
                <span>{{ $t('avalara.exempt_non-billable') }}</span>
                {{ row.exnb !== null ? exnbOptions[row.exnb] : '' }}
              </template>
            </sw-table-column>

            <sw-table-column :sortable="false" :label="$t('general.actions')">
              <template slot-scope="row">
                <span>{{ $t('general.actions') }}</span>
                <div class="flex">
                  <sw-switch
                    v-model="row.enable"
                    @change="changeExemptionStatus(row)"
                  />
                </div>
              </template>
            </sw-table-column>
          </sw-table-component>
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
        {{ $t('general.close') }}
      </sw-button>      
    </div>

    <CustomerAvalaraCategoryExemptionsModalList ref="CustomerAvalaraCategoryExemptionsRef"/>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required } = require('vuelidate/lib/validators')
import CustomerAvalaraCategoryExemptionsModalList from '@/components/base/modal/CustomerAvalaraCategoryExemptionsModalList.vue'
export default {
  components: {
    CustomerAvalaraCategoryExemptionsModalList,
  },
  data() {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        user_id: null,
        avalara_locations_id: null,
      },
      dataModal: {},
      exemptionsCategorys: [],
      frcOptions: {
        1: 'Yes',
        0: 'No',
      },
      domOptions: {
        0: 'Federal',
        1: 'State',
        2: 'County',
        3: 'City',
      },
      scpOptions: {
        128: 'Federal',
        256: 'State',
        512: 'County',
        1024: 'City',
      },
      exnbOptions: {
        1: 'Can Be Exempted',
        0: 'Cannot Be Exempted',
      },
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),
    ...mapGetters(['countries'])
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
  created() {
    window.hub.$on('exemption_added_modal_avalara', this.getExemptionCategoryMetho)
    this.getExemptionCategoryMetho()
  },

  methods: {
    ...mapActions('modal', ['closeModal', 'openModal']),
    ...mapActions('avalara', [
      'saveAvalaraLocation',
      'updateAddress',
      'getTaxTypes',
      'fetchAvalaraDefault',
      'exemptionAdded',
      'getExemptionCategory',
      'disableExemptionCategory',
      'enableExemptionCategory',
    ]),
    showModalException() {
      // showModalException
      window.hub.$emit('showModalException_event', this.modalData)
    },
    async changeExemptionStatus({ id, user_id, enable }) {
      try {
        if (enable) {
          await this.enableExemptionCategory({ id, user_id })
          window.toastr['success'](this.$t('avalara.exemption_enable'))
        } else {
          await this.disableExemptionCategory({ id, user_id })
          window.toastr['success'](this.$t('avalara.exemption_disabled'))
        }
      } catch (e) {
        //console.log(e)
        window.toastr['error'](this.$t('general.error_message'))
      }
    },
    
    async setData() {
      this.dataModal = this.modalData.billing
      this.formData.user_id = this.modalData.userId
      this.formData.avalara_locations_id = this.modalData.avalara_location_id
    },
    async submitAvalaraCategoryExemption() {
      try {
        this.$v.formData.$touch()
        this.isLoading = true
        
        const response = await this.exemptionAdded(this.formData)
     
        if (response.data.success) {
          window.toastr['success'](
            this.$tc('avalara.exemption_added_succesfully')
          )
          window.hub.$emit('exemption_added', response.data)
          this.closeAvalaraLocationsModal()
        } else {
          window.toastr['error'](response.data.message)
        }
      } catch (e) {
       // console.log(e)
        window.toastr['error'](e.message)
      } finally {
        this.isLoading = false
      }
    },
    closeAvalaraLocationsModal() {
      this.closeModal()
    },
    async getExemptionCategoryMetho() {
      try {
        const res = await this.getExemptionCategory({user_id: this.modalData.userId, avalara_locations_id: this.modalData.avalara_location_id})
        if(!res.success){
          window.toastr['error'](res.message)
          return 
        }
        this.exemptionsCategorys = res.data
      } catch (e) {
       // console.log(e)
      } finally {
      }
    },
  },
}
</script>
