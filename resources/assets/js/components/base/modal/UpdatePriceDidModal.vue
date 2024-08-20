<template>
  <div class="item-modal">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <form action="" @submit.prevent="submitData">
      <div class="px-8 py-8 sm:p-6">
        <sw-input-group
          :label="$t('pbx_services.did_channel')"
          variant="horizontal"
        >
          <span class="text-sm text-gray-600">{{ formData.name }}</span>
        </sw-input-group>

        <sw-input-group
          :label="$t('pbx_services.did_channel')"
          variant="horizontal"
          class="mt-4"
        >
          <span class="text-sm text-gray-600">{{ formData.number }}</span>
        </sw-input-group>

        <sw-input-group
          :label="$t('general.price')"
          variant="horizontal"
          class="my-4"
        >
          <sw-money
            v-model="formData.price"
            :currency="defaultCurrencyForInput"
          />
        </sw-input-group>

        <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
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
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  data() {
    return {
      isRequestOnGoing: false,
      formData: {
        name: null,
        number: null,
        price: 0,
      },
    }
  },
  computed: {
    ...mapGetters('modal', ['modalDataID', 'modalData', 'modalActive', 'refreshData',]),
    ...mapGetters('company', ['defaultCurrencyForInput']),
  },
  mounted() {
    this.setInitialData()
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('pbxService', ['updatePriceDid']),

    setInitialData() {
      if (this.modalData) {
        this.formData.id = this.modalData.pbx_service_did_id
        this.formData.name = this.modalData.profile_name
        // this.formData.email = this.modalData.email
        this.formData.number = this.modalData.number
        this.formData.price = Number(this.modalData.profile_rate)
      }
    },

    closeExtensionModal() {
      this.closeModal()
      this.resetModalData()
    },

    async submitData() {
      try {
        let payload = {
          pbx_services_did_id: this.formData.id,
          price: this.formData.price,
        }

        this.isRequestOnGoing = true
        let response = await this.updatePriceDid(payload)
        window.toastr['success'](this.$tc('pbx_services.did_update_success'))
        window.hub.$emit('updateExt')
        this.refreshData ? this.refreshData() : ''
        this.closeExtensionModal()
        this.$router.go()
      } catch (error) {
       // console.log(error)
        window.toastr['error'](error.data.message)
      } finally {
        this.isRequestOnGoing = false
      }
    },
  },
}
</script>