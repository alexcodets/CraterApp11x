<template>
  <form action="" @submit.prevent="submitSection">
    <div class="p-8 sm:p-6">
      <sw-input-group :label="$t('settings.core_pos.core_pos_section_name')" :error="nameError" variant="horizontal"
        required>
        <sw-input ref="name" :invalid="$v.formData.name.$error" v-model="formData.name" type="text"
          @input="$v.formData.name.$touch()" />
      </sw-input-group>
    </div>
    <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
      <sw-button class="mr-3" variant="primary-outline" type="button" @click="closeSectionModal">
        {{ $t('general.cancel') }}
      </sw-button>
      <sw-button :loading="isLoading" variant="primary" icon="save" type="submit">
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ !isEdit ? $t('general.save') : $t('general.update') }}
      </sw-button>
    </div>
  </form>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, minLength } = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        id: null,
        name: null,
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
    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(2),
      },
    },
  },

  async mounted() {
    this.$refs.name.focus = true
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    }
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('corePos', ['addSection', 'updateSection']),

    resetFormData() {
      this.formData = {
        id: null,
        name: null,
      }
      this.$v.formData.$reset()
    },
    async submitSection() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true
      let response

      try {
        if (!this.isEdit) {
          response = await this.addSection(this.formData)
        } else {
          response = await this.updateSection(this.formData)
        }

        if (response.data.success) {
          this.isLoading = false
          if (this.isEdit) {

            window.toastr['success'](
                this.$t('settings.core_pos.core_pos_update_section')
              )
              this.refreshData ? this.refreshData() : ''
              this.closeSectionModal()
              return true
            } else {
              if (response.data.exists) {
                window.toastr['error'](
                  this.$t('settings.core_pos.core_pos_exists_section')
                  )
                  
                  this.refreshData ? this.refreshData() : ''
                  this.closeSectionModal()
                  return true
                } else {
                  window.toastr['success'](
                this.$t('settings.core_pos.core_pos_create_section')
              )
            }
          }
          this.refreshData ? this.refreshData() : ''
          this.closeSectionModal()
          return true
        }
      } catch (error) {
        this.isLoading = false
        window.toastr['error'](response.data.error)
      }
    },
    async setData() {
      this.formData = {
        id: this.modalData.id,
        name: this.modalData.name,
      }
    },
    closeSectionModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>
