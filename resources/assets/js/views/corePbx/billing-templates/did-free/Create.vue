<template>
  <base-page class="item-create">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-page-header :title="pageTitle">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          :title="$tc('corePbx.didFree.new_custom_did', 2)"
          to="/admin/corePBX/billing-templates/did-free"
        />
        <sw-breadcrumb-item
          v-if="$route.name === 'corepbx.didFree.edit'"
          :title="$t('corePbx.didFree.edit_did_free')"
          to="#"
          active
        />

        <sw-breadcrumb-item
          v-else
          :title="$t('corePbx.didFree.add_did_free')"
          to="#"
          active
        />
      </sw-breadcrumb>
    </sw-page-header>

    <!--FORM-->

    <!-- <div class="flex mt-2 col-span-12"> -->
    <sw-card>
      <div
        class="grid gap-6 grid-col-1 md:grid-cols-2 border border-grey-700 rounded-lg p-5"
      >
        <!-- STATUS required class="md:col-span-3 ml-2"-->
        <sw-input-group :label="$t('packages.status')">
          <sw-select
            v-model="formData.statu"
            :options="status"
            :searchable="true"
            :show-labels="false"
            :tabindex="16"
            :allow-empty="true"
            :placeholder="$t('general.select_status')"
            label="text"
            track-by="value"
          />
        </sw-input-group>

        <!-- Prueba -->
        <!-- STATUS required @input="$v.category.$touch()"-->

        <sw-input-group
          :label="$t('expenses.category')"
          :error="categoryError"
          required
        >
          <sw-select
            ref="baseSelect"
            v-model="category"
            :options="categoriesTollFree"
            :invalid="$v.category.$error"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('expenses.categories.select_a_category')"
            class="mt-2"
            label="name"
            track-by="id"
            @input="$v.category.$touch()"
          >
            <sw-button
              slot="afterList"
              type="button"
              variant="gray-light"
              class="flex items-center justify-center w-full px-4 py-3 bg-gray-200 border-none outline-none"
              @click="openCategoryModal"
            >
              <shopping-cart-icon class="h-5 text-center text-primary-400" />
              <label class="ml-2 text-xs leading-none text-primary-400">{{
                $t('settings.expense_category.add_new_category')
              }}</label>
            </sw-button>
          </sw-select>
        </sw-input-group>
      </div>
      <div
        v-for="(prefix, index) in formData.multiple"
        :key="index"
        class="grid gap-6 grid-col-1 md:grid-cols-2 mt-4 border border-grey-700 rounded-lg p-5"
      >
        <!--PREFIJO required class="md:col-span-3"-->
        <sw-input-group
          :label="$t('didFree.item.prefijo')"
          :error="prefixValidate($v.formData.multiple.$each[index].prefijo)"
          required
        >
          <sw-input
            v-model="prefix.prefijo"
            :placeholder="$t('didFree.item.prefijo')"
            focus
            type="text"
            name="prefijo"
            tabindex="1"
            placer
            :invalid="$v.formData.multiple.$each[index].prefijo.$error"
          />
        </sw-input-group>

        <!-- class="md:col-span-3" -->
        <sw-input-group
          :label="$t('didFree.price')"
          :error="ratePerError"
          required
        >
          <sw-money
            v-model="prefix.rate_per_minutes"
            :currency="defaultCurrencyForInput"
            name="rate_per_minutes"
          />
        </sw-input-group>

        <div class="col-span-1 md:col-span-2 flex justify-end" v-if="!isEdit">
          <sw-button
            v-if="formData.multiple.length - 1 == index"
            :variant="`primary-outline`"
            size="sm"
            @click="addCustom"
          >
            <PlusIcon class="h-4 w-4" />
          </sw-button>
          <sw-button
            v-if="formData.multiple.length - 1 !== index"
            :variant="`danger-outline`"
            size="sm"
            class="ml-4"
            @click="removeCustom(index)"
          >
            <TrashIcon class="h-4 w-4" />
          </sw-button>
        </div>
      </div>
    </sw-card>

    <div class="pt-8 py-2 flex flex-col md:flex-row md:space-x-4">
      <sw-button
        @click="submitDID"
        :loading="isLoading"
        type="submit"
        variant="primary"
        size="lg"
        class="w-full md:w-auto"
      >
        <save-icon class="w-6 h-6 mr-1 -ml-2 mr-2" v-if="!isLoading" />
        {{ isEdit ? $t('didFree.update_did') : $t('didFree.save_did') }}
      </sw-button>

      <sw-button
        variant="primary-outline"
        type="button"
        size="lg"
        class="w-full md:w-auto mt-2 md:mt-0"
        @click="cancelForm()"
      >
        <x-circle-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('general.cancel') }}
      </sw-button>
    </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import {
  TrashIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
const { required, minLength, minValue } = require('vuelidate/lib/validators')

export default {
  components: {
    //  MoreIcon,
    //   draggable,
    TrashIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusIcon,
    XCircleIcon,
    // RightArrow,
    // LeftArrow,
  },

  data() {
    return {
      showSelect: false,
      isRequestOnGoing: false,
      category: null,
      isLoading: false,
      rate_per_minutes_selected: 0,
      status: [
        { value: 'A', text: 'Active' },
        { value: 'I', text: 'Inactive' },
      ],
      formData: {
        id: '',
        statu: { value: 'A', text: 'Active' },
        status: '',
        toll_free_category_id: null,
        multiple: [
          {
            prefijo: '',
            rate_per_minutes: 0,
          },
        ],
      },
    }
  },
  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),
    pageTitle() {
      if (this.isEdit) {
        return this.$t('corePbx.didFree.edit_did_free')
      } else {
        return this.$t('corePbx.didFree.add_did_free')
      }
    },
    isEdit() {
      if (this.$route.name === 'corepbx.didFree.edit') {
        return true
      }
      return false
    },

    prefijoError() {
      if (!this.$v.formData.prefijo.$error) {
        return ''
      }
      if (!this.$v.formData.prefijo.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.prefijo.minLength) {
        return this.$tc(
          'validation.prefijo_min_length_number',
          this.$v.formData.prefijo.$params.minLength.min,
          { count: this.$v.formData.prefijo.$params.minLength.min }
        )
      }
      if (!this.$v.formData.prefijo.minValue.min) {
        return this.$tc('validation.numbers_only')
      }
    },
    ...mapGetters('categoriesTollF', ['categoriesTollFree']),

    categoryError() {
      if (!this.$v.category.$error) {
        return ''
      }
      if (!this.$v.category.required) {
        return this.$t('validation.required')
      }
    },

    ratePerError() {
      if (this.$v.rate_per_minutes_selected.$error) {
        return ''
      }
      if (!this.$v.rate_per_minutes_selected.required) {
        return this.$tc('validation.required')
      }
    },
  },

  methods: {
    ...mapActions('didtollfree', [
      'fetchOneDIDTOLLFREE',
      'updateDIDTOLLFREE',
      'addDIDTOLLFREE',
    ]),
    ...mapActions('modal', ['openModal']),
    ...mapActions('categoriesTollF', ['fetchCategories']),

    openCategoryModal() {
      this.openModal({
        title: this.$t('settings.expense_category.add_category'),
        componentName: 'CategoryModalTollFree',
      })
    },

    async loadDID() {
      this.isRequestOnGoing = true
      const Cat = await this.fetchCategories({ limit: 'all' })
      // console.log(Cat);
      if (this.isEdit) {
        let res = await this.fetchOneDIDTOLLFREE(this.$route.params.id)

        let { prefijo, status, toll_free_category_id, rate_per_minute } =
          res.data.ProfileDidTollFree

        this.formData.statu = this.status.filter(
          (element) => element.value == status
        )[0]

        if (toll_free_category_id) {
          this.category = Cat.data.categories.data.filter(
            (element) => element.id == toll_free_category_id
          )[0]
        }

        this.formData.multiple[0].prefijo = prefijo
        this.formData.multiple[0].rate_per_minutes = parseFloat(rate_per_minute)
      }

      this.isRequestOnGoing = false
    },

    async submitDID() {
      this.$v.category.$touch()
      /* console.log( this.formData.toll_free_category_id); */
      this.$v.formData.$touch()
      // if (this.$v.$invalid) {
      //   return true
      // }

      const searhInvalid = this.formData.multiple.findIndex((item) => {
        if (item.prefijo == '' || item.prefijo == null) return true
      })
      if (searhInvalid != -1) return false
      // Extraer los prefijos de this.formData.multiple y verificar si hay repetidos
      const prefijos = this.formData.multiple.map((item) => {
        return item.prefijo
      })
      const repetidos = prefijos.filter((item, index) => {
        return prefijos.indexOf(item) != index
      })
      if (repetidos.length > 0) {
        window.toastr['error'](this.$t('didFree.error_prefijo_duplicated'))
        return false
      }

      ///validacion

      let text = ''
      if (this.isEdit) {
        text = 'corePbx.extensions.edit_text'
      } else {
        text = 'corePbx.extensions.create_text'
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc(text),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          try {
            let res
            this.isLoading = true
            if (this.isEdit) {
              this.formData.id = this.$route.params.id
              this.formData.prefijo = this.formData.multiple[0].prefijo
              this.formData.rate_per_minutes =
                this.formData.multiple[0].rate_per_minutes
              delete this.formData.multiple

              res = await this.updateDIDTOLLFREE(this.formData)
              window.toastr['success'](res.data.message)
              this.$router.push('/admin/corePBX/billing-templates/toll-free')
              return true
            } else {
              res = await this.addDIDTOLLFREE(this.formData)
              this.isLoading = false
              if (!this.isEdit) {
                window.toastr['success'](res.data.message)
                this.$router.push('/admin/corePBX/billing-templates/toll-free')
                return true
              }
            }
          } catch (error) {
            window.toastr['error'](error.ReferenceError)
            this.status = [
              {
                value: 'A',
                text: 'Active',
              },
              {
                value: 'I',
                text: 'Inactive',
              },
            ]
            this.formData.statu = {
              value: 'A',
              text: 'Active',
            }
            this.isLoading = false
            return false
          }
        }
      })
    },
    cancelForm() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.cancel_text'),
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          this.$router.go(-1)
        }
      })
    },
    addCustom() {
      this.formData.multiple.push({
        prefijo: '',
        rate_per_minutes: 0,
      })
    },
    removeCustom(index) {
      this.formData.multiple.splice(index, 1)
    },
    prefixValidate(value) {
      if (!value.$error) {
        return ''
      }
      if (!value.required) {
        return this.$tc('validation.required')
      }
    },
  },

  watch: {
    category(newValue) {
      this.formData.toll_free_category_id = newValue.id
    },
  },

  mounted() {
    this.$v.formData.$reset()

    this.loadDID()
    window.hub.$on('newCategory', (val) => {
      this.category = val
    })
  },
  validations: {
    rate_per_minutes_selected: {
      required,
    },
    category: {
      required,
    },

    formData: {
      multiple: {
        $each: {
          prefijo: {
            required,
          },
        },
      },
    },
  },
}
</script>
