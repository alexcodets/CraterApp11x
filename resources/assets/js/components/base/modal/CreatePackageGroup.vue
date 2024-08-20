<template>
  <div class="mail-config-modal">
    <form action="" @submit.prevent="savePackagesGroup">
      <div class="p-4 md:p-8">
        <sw-input-group
          :label="$t('packages.modal.name')"
          class="mt-3"
          :error="name"
          variant="horizontal"
          required>
          <sw-input
            ref="name"
            :invalid="$v.formData.name.$error"
            v-model="formData.name"
            type="text"
            @input="$v.formData.name.$touch()"/>
        </sw-input-group>
      </div>
      <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
        <sw-button type="button"
          variant="primary-outline"
          class="mr-3"
          @click="closeTaxModal">
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button variant="primary" type="submit" :loading="isLoading">
          {{ !isEdit ? $t('general.save') : $t('general.update') }}
        </sw-button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { PaperAirplaneIcon } from '@vue-hero-icons/outline'
const {
  required,
  minLength,
  email,
  maxLength,
} = require('vuelidate/lib/validators')
export default {
  components: {
    PaperAirplaneIcon,
  },
  data() {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        name: null,
      },
    }
  },
  computed: {
    ...mapGetters('modal', ['modalDataID', 'modalData', 'modalActive']),
    name() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.name.maxLength) {
        return this.$tc('validation.message_maxlength')
      }
    },
  },
  validations: {
    formData: {
      name: {
        required,
         maxLength: maxLength(100),
      },
    },
  },
  async mounted() {
    this.$refs.name.focus = true
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('pack', ['saveName']),
    ...mapActions('group', ['addGroup']),

    resetFormData() {
      this.formData = {
        name: null,
      }
      this.$v.formData.$reset()
    },
    async savePackagesGroup() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true

      let response = await this.saveName(this.formData.name)

      let data = {
        name: this.formData.name,
        allow_upgrades: false,
        packageLeft: [],
      }

      response = await this.addGroup(data)

      if (response.status === 200) {
        window.toastr['success'](this.$tc('packages.modal.successfully'))
        this.closeTaxModal()
        this.isLoading = false
        return true
      }
    },
    closeTaxModal() {
      // this.resetModalData()
      // this.resetFormData()
      this.closeModal()
    },
  },
}
</script>
