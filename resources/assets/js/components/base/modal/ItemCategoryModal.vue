<template>
  <form action="" @submit.prevent="submitItemCategory">
    <div class="p-8 sm:p-6">
      <sw-input-group
        :label="$t('settings.customization.items.name')"
        :error="nameError"
        variant="horizontal"
        required
      >
        <sw-input
          ref="name"
          :invalid="$v.formData.name.$error"
          v-model="formData.name"
          type="text"
          @input="$v.formData.name.$touch()"
        />
      </sw-input-group>
    </div>
    <div class="flex mt-3 mb-4 p-8 sm:p-6">
        <div class="relative w-12">
          <sw-switch
            v-model="formData.is_group"
            class="absolute"
            style="top: -20px"
          />
        </div>
        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('settings.customization.items.group') }}
          </p>
        </div>
      </div>

      <div class="flex mt-3 mb-4 p-8 sm:p-6">
        <div class="relative w-12">
          <sw-switch
            v-model="formData.is_item"
            class="absolute"
            style="top: -20px"
          />
        </div>
        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('settings.customization.items.item') }}
          </p>
        </div>
      </div>

    <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
      <sw-button
        class="mr-3"
        variant="primary-outline"
        type="button"
        @click="closeItemCategoryModal"
      >
        {{ $t('general.cancel') }}
      </sw-button>
      <sw-button
        :loading="isLoading"
        variant="primary"
        icon="save"
        type="submit"
      >
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
        is_group: false,
        is_item: false
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
    ...mapActions('item', ['addItemCategory', 'updateItemCategory']),
    resetFormData() {
      this.formData = {
        id: null,
        name: null,
        is_group: false,
        is_item: false
      }
      this.$v.formData.$reset()
    },
    async submitItemCategory() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      // validar que is_group o is_item esten activos al menos uno
      if (!this.formData.is_group && !this.formData.is_item) {
        window.toastr['error'](
          this.$t('settings.customization.items.item_category_error')
        )
        return true
      }

      this.isLoading = true
      let response

      try {
        if (!this.isEdit) {
          response = await this.addItemCategory(this.formData)
        } else {
          response = await this.updateItemCategory(this.formData)
        }

        if (response.data) {
          this.isLoading = false
          if (!this.isEdit) {
            window.toastr['success'](
              this.$t('settings.customization.items.item_category_added')
            )
          } else {
            window.toastr['success'](
              this.$t('settings.customization.items.item_category_updated')
            )
          }
          this.refreshData ? this.refreshData() : ''
          this.closeItemCategoryModal()
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
        is_group: this.modalData.is_group,
        is_item: this.modalData.is_item
      }
    },
    closeItemCategoryModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>
