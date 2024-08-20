<template>
  <div class="overflow-auto" style="height:85vh;">
    <div>
      <form action="" @submit.prevent="sendEstimateData">
      <div class="px-8 py-8 sm:p-6">
        <sw-input-group
          :label="$t('general.from')"
          :error="fromError"
          class="mb-4"
          variant="vertical"
          required
        >
          <sw-input
            v-model="formData.from"
            :invalid="$v.formData.from.$error"
            type="text"
            @input="$v.formData.from.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('general.to')"
          :error="toError"
          class="mb-4"
          variant="vertical"
          required
        >
          <sw-input
            v-model="formData.to"
            :invalid="$v.formData.to.$error"
            type="text"
            @input="$v.formData.to.$touch()"
          />
        </sw-input-group>
        
        <sw-input-group
          :label="$t('general.body')"
          :error="bodyError"
          class="mb-4"
          variant="vertical"
          required
        >
          <!-- <sw-editor
            v-model="formData.body"
            :set-editor="formData.body"
            :invalid="$v.formData.body.$error"
            @input="$v.formData.body.$touch()"
          /> -->
          <base-custom-input
            v-model="formData.body"
            :fields="estimateMailFields"
            :invalid="$v.formData.body.$error"
            @input="$v.formData.body.$touch()"
            class="mt-2"
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
          @click="closeSendEstimateModal"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button
          :loading="isLoading"
          :disabled="isLoading"
          variant="primary"
          type="submit"
        >
          <paper-airplane-icon v-if="!isLoading" class="h-5 mr-2" />
          {{ $t('general.send') }}
        </sw-button>
      </div>
      </form>
    </div>
  </div>  
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { PaperAirplaneIcon } from '@vue-hero-icons/solid'
const { required, email } = require('vuelidate/lib/validators')
const _ = require('lodash')

export default {
  components: {
    PaperAirplaneIcon,
  },
  data() {
    return {
      isLoading: false,
      estimateMailFields: [
        'customer',
        'customerCustom',
        'estimate',
        'estimateCustom',
        'company',
      ],
      formData: {
        from: null,
        to: null,
        subject: null,
        body: null,
      },
    }
  },
  validations: {
    formData: {
      from: {
        required,
        email,
      },
      to: {
        required,
        email,
      },
      subject: {
        required,
      },
      body: {
        required,
      },
    },
  },
  computed: {
    ...mapGetters('modal', ['modalDataID', 'modalData', 'modalActive']),
    ...mapGetters('user', ['currentUser']),
    getEmailUrl() {
      return this.url
    },
    fromError() {
      if (!this.$v.formData.from.$error) {
        return ''
      }

      if (!this.$v.formData.from.required) {
        return this.$tc('validation.required')
      }

     
    },
    toError() {
      if (!this.$v.formData.to.$error) {
        return ''
      }

      if (!this.$v.formData.to.required) {
        return this.$tc('validation.required')
      }


    },
    subjectError() {
      if (!this.$v.formData.subject.$error) {
        return ''
      }

      if (!this.$v.formData.subject.required) {
        return this.$tc('validation.required')
      }
    },
    bodyError() {
      if (!this.$v.formData.body.$error) {
        return ''
      }

      if (!this.$v.formData.body.required) {
        return this.$tc('validation.required')
      }
    },
  },
  mounted() {
    this.setInitialData()
  },
  methods: {
    ...mapActions('modal', ['closeModal']),

    ...mapActions('estimate', ['sendEmail']),

    ...mapActions('company', ['fetchCompanySettings', 'fetchMailConfig']),

    async setInitialData() {
      let admin = await this.fetchMailConfig()

      if (this.modalData) {
       // console.log(this.modalData.user)
        //this.formData.from = admin.data.from_mail
        this.formData.to = this.modalData.user.phone
      }

      let res = await this.fetchCompanySettings([
        'default_estimate_sms_body',
        'phoneFrom'
      ])

      this.formData.body = res.data.default_estimate_sms_body
      this.formData.from = res.data.phoneFrom
     
    },
    resetFormData() {
      this.formData = {
        from: null,
        to: null,
        subject: null,
        body: null,
      }
    },
    async sendEstimateData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_send_estimate'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        try {
          if (value) {
            let data = {
              ...this.formData,
              id: this.modalDataID,
              status: 'SENT',
            }

            const result = this.validateFieldIsEmpty()

            if(!result.status){
              window.toastr['error'](
                this.$tc(result.message)
              )
            } else {
              this.isLoading = true
            let res = await this.sendEmail(data)
            this.closeModal()
            if (res.data.success) {
              this.isLoading = false
              window.toastr['success'](
                this.$tc('estimates.send_estimate_successfully')
              )
              // this.$router.push('/admin/estimates')
              location.reload();
              return true
            }
            if (res.data.error === 'estimates.user_email_does_not_exist') {
              window.toastr['error'](
                this.$tc('estimates.user_email_does_not_exist')
              )
              return false
            }
            }
          }
        } catch (error) {
          this.isLoading = false
          window.toastr['error'](this.$tc('estimates.something_went_wrong'))
        }
      })
    },
    closeSendEstimateModal() {
      this.resetFormData()
      this.closeModal()
      // this.$store.$dispatch('modal', this.modalActive);
      // this.$store.dispatch('modal')
      // console.log(this.modalActive);
      // localStorage.setItem('modalActive', this.modalActive);
    },
    
    validateFieldIsEmpty() {
      if ( this.formData.subject == null || this.formData.subject == undefined || this.formData.subject.length <= 10) {
        return {status: false, message: 'validation.validation_subject'}
      } 
      if ( this.formData.body == null || this.formData.body == undefined || this.formData.body.length <= 10) {
        return {status: false, message: 'validation.validation_body_email'}
      } else {
        return {status: true}
      }
    }
  },
}
</script>
