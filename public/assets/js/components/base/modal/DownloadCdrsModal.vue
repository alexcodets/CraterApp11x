<template>
  <div class="download-cdrs">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <form action="" @submit.prevent="submitData">
      <div class="grid grid-cols-12 p-8 gap-y-6 gap-x-4">
        <label class="text-sm not-italic font-medium leading-5 text-gray-500 col-span-12">
          {{ $t('pbx_services.cdr_download_description') }}
        </label>

        <sw-input-group
          :label="$t('pbx_services.start_date')"
          class="mb-4 col-span-12 md:col-span-6"
          :error="startDateError"
          required
        >
          <base-date-picker
            :invalid="$v.formData.start_date.$error"
            v-model="formData.start_date"
            :calendar-button="true"
            :min-date="min_date"
            :max-date="max_date"
            calendar-button-icon="calendar"
            @input="$v.formData.start_date.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('pbx_services.end_date')"
          class="mb-4 col-span-12 md:col-span-6"
          :error="endDateError"
          required
        >
          <base-date-picker
            :invalid="$v.formData.end_date.$error"
            v-model="formData.end_date"
            :calendar-button="true"
            :min-date="min_date"
            :max-date="max_date"
            calendar-button-icon="calendar"
            @input="$v.formData.end_date.$touch()"
          />
        </sw-input-group>
      </div>

      <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
        <sw-button
          class="mr-3"
          variant="primary-outline"
          type="button"
          @click="closeImportModal"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button
          variant="primary"
          type="submit"
        >
          <download-icon class="h-4 mr-2"/>
          {{ $t('general.download') }}
        </sw-button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import { DownloadIcon } from '@vue-hero-icons/outline'
import moment from "moment";
const {
  required,
  minLength,
  numeric,
  maxLength,
  minValue,
  requiredIf
} = require('vuelidate/lib/validators')

export default {
  components: {
    DownloadIcon,
  },
  data() {
    return {
      isRequestOnGoing: false,
      formData: {
        start_date: null,
        end_date: new Date()
      },
      min_date: null,
      max_date: null,
    }
  },
  validations: {
    formData: {
      start_date: {
        required
      },
      end_date: {
        required
      },
    }
  },
  created() {
    this.formData.start_date = moment(
      this.selectedPbxService.date_begin,
      'YYYY-MM-DD'
    ).toString()

    this.min_date = moment(
      this.selectedPbxService.date_begin,
      'YYYY-MM-DD'
    ).format('YYYY-MM-DD')

    this.max_date = new Date()
  },
  computed: {
    ...mapGetters('pbxService', ['selectedPbxService']),

    startDateError() {
      if (!this.$v.formData.start_date.$error) {
        return ''
      }
      if (!this.$v.formData.start_date.required) {
        return this.$t('validation.required')
      }
    },

    endDateError() {
      if (!this.$v.formData.end_date.$error) {
        return ''
      }
      if (!this.$v.formData.end_date.required) {
        return this.$t('validation.required')
      }
    },
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('pbxService', ['downloadCalls']),

    resetFormData() {
      this.formData = {
        start_date: null,
        end_date: null
      }
      this.$v.$reset()
    },

    closeImportModal() {
      this.resetFormData()
      this.closeModal()
      this.resetModalData()
    },

    async submitData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return false
      }

      let data = {
        service_id: this.selectedPbxService.id,
        ...this.formData
      }

      swal({
        title: this.$t('pbx_services.download_calls_confirm'),
        text: this.$tc('pbx_services.download_calls_confirm_description'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true
      }).then(async (result) => {
        if (result) {
          try {
            this.isRequestOnGoing = true
            let response = await this.downloadCalls(data)
            if (response.data.success) {
              window.toastr['success'](this.$tc('pbx_services.download_calls_success'))
              window.hub.$emit('newCalls')
              this.isRequestOnGoing = false
              this.resetModalData()
              this.resetFormData()
              this.closeModal()
              return true
            }
          } catch (err) {
            this.isRequestOnGoing = false
          }
        }
      })
    }
  }
}
</script>

<style scoped>

</style>