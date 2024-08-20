<template>
  <div class="overflow-auto" style="height:85vh;">
    <form action="" @submit.prevent="sendPaymentData">
      <div class="px-8 py-8 sm:p-6">
        <sw-input-group
          :label="$t('general.from')"
          class="mb-4"
          variant="vertical"
          :error="fromError"
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
            type="text"
            :invalid="$v.formData.to.$error"
            @input="$v.formData.to.$touch()"
          />
        </sw-input-group>
        <!-- <sw-input-group
          :label="$t('general.subject')"
          :error="subjectError"
          class="mb-4"
          variant="vertical"
          required
        >
          <sw-input
            v-model="formData.subject"
            :invalid="$v.formData.subject.$error"
            type="text"
            @input="$v.formData.subject.$touch()"
          />
        </sw-input-group> -->

        <sw-input-group class=" mt-2" :label="$t('general.subject')" >
        <base-custom-input v-model="formData.subject" 
        :fields="mailFields" 
          />
      </sw-input-group>

        <sw-input-group
          :label="$t('general.body')"
          :error="bodyError"
          class="mb-4 mt-3"
          variant="vertical"
          required
        >

          <base-custom-input v-model="formData.body" 
        :fields="mailFields" 
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
          @click="closeSendPaymentModal"
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
      InvoiceMailFields: [
        'customer',
        'customerCustom',
        'invoice',
        'invoiceCustom',
        'company',
      ],
      mailFields: [
        'customer',
        'customerCustom',
        'company',
        'payment',
        'paymentA',
        'paymentCustom',
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

    fromError() {
      if (!this.$v.formData.from.$error) {
        return ''
      }

      if (!this.$v.formData.from.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.formData.from.email) {
        return this.$tc('validation.email_incorrect')
      }
    },

    toError() {
      if (!this.$v.formData.to.$error) {
        return ''
      }

      if (!this.$v.formData.to.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.formData.to.email) {
        return this.$tc('validation.email_incorrect')
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

    ...mapActions('payment', ['sendEmail']),

    ...mapActions('company', ['fetchCompanySettings', 'fetchMailConfig']),

    async setInitialData() {
      let admin = await this.fetchMailConfig()

      if (this.modalData) {
        this.formData.from = admin.data.from_mail
        this.formData.to = this.modalData.user.email
      }

      let res = await this.fetchCompanySettings([
        'payment_mail_body',
        'payment_subject',
      ])

      this.formData.body = res.data.payment_mail_body
      if (res.data.payment_subject) {
        if (res.data.payment_subject != null) {
          this.formData.subject = res.data.payment_subject
        }
      }
    },

    resetFormData() {
      this.formData = {
        from: null,
        to: null,
        subject: null,
        body: null,
      }
    },

    async sendPaymentData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('payments.confirm_send_payment'),
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
                  this.$tc('payments.send_payment_successfully')
                )
                return true
              }

              if (res.data.error === 'payments.user_email_does_not_exist') {
                window.toastr['error'](
                  this.$tc('payments.user_email_does_not_exist')
                )
                return false
              }
            }
          }
        } catch (error) {
          this.isLoading = false
          window.toastr['error'](this.$tc('payments.something_went_wrong'))
        }
      })
    },
    closeSendPaymentModal() {
      this.resetFormData()
      this.closeModal()
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
