<template>
  <base-page class="item-create">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-page-header :title="pageTitle">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          :title="$tc('corePbx.internacional.new_internacional', 2)"
          to="/admin/corePBX/billing-templates/international-rate"
        />
        <sw-breadcrumb-item
          v-if="$route.name === 'corepbx.internationalRate.edit'"
          :title="$t('corePbx.internacional.edit_internacional')"
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
        class="grid gap-2 grid-cols-1 md:grid-cols-2 xl:grid-cols-3 border border-grey-700 rounded-lg mb-5 p-3"
      >
        <!-- CATEGORYS -->
        <sw-input-group
          :label="$t('expenses.category')"
          :error="categoryError"
          required
        >
          <sw-select
            v-model="formData.category"
            :options="category"
            :invalid="$v.formData.category.$error"
            :searchable="true"
            :show-labels="false"
            :tabindex="16"
            :allow-empty="true"
            :placeholder="$t('general.select_category')"
            label="text"
            track-by="value"
            @input="$v.formData.category.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('didFree.item.custom_destination_group')"
          :error="customError"
          required
        >
          <sw-select
            v-model="formData.prefixrate_groups_id"
            :options="getDestinationGroups"
            :class="{ error: $v.formData.prefixrate_groups_id.$error }"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :multiple="true"
            track-by="id"
            label="name"
            :tabindex="4"
          />
        </sw-input-group>

        <!-- STATUS required class="md:col-span-3 ml-2"-->
        <sw-input-group
          :label="$t('packages.status')"
          :error="statusError"
          required
        >
          <sw-select
            v-model="formData.status"
            :options="status"
            :searchable="true"
            :show-labels="false"
            :tabindex="16"
            :allow-empty="false"
            :placeholder="$t('general.select_status')"
            label="text"
            track-by="value"
          />
        </sw-input-group>
      </div>

      <div 
      class="grid gap-2 grid-cols-1 md:grid-cols-2 xl:grid-cols-2 border border-grey-700 rounded-lg mb-5 p-3"
      >
        <sw-input-group :label="$tc('settings.company_info.country')">
          <sw-select
            v-model="formData.country_id"
            :options="countries"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            :placeholder="$t('general.select_country')"
            label="name"
            track-by="id"
          />
        </sw-input-group>
      </div>

      <div
        v-for="(prefix, index) in formData.multiple"
        :key="index"
        class="grid gap-2 grid-cols-1 md:grid-cols-2 xl:grid-cols-3 border border-grey-700 rounded-lg mt-5 p-3"
      >
        <sw-input-group :label="$t('corePbx.internacional.prefix_type')">
          <sw-select
            v-model="prefix.typecustom"
            :options="typecustomOptions"
            :searchable="true"
            :show-labels="true"
            :allow-empty="false"
            :placeholder="$t('corePbx.internacional.prefix_type')"
            label="label"
            track-by="value"
          />
        </sw-input-group>
        <!--PREFIJO required class="md:col-span-3"-->
        <sw-input-group
          v-if="prefix.typecustom.value == 'P'"
          :label="$t('didFree.item.prefijo')"
          required
          :error="prefixValidate($v.formData.multiple.$each[index].prefijo)"
        >
          <sw-input
            v-model="prefix.prefijo"
            :placeholder="$t('didFree.item.prefijo')"
            focus
            type="text"
            name="prefijo"
            pattern="[0-9*|A-Za-z *|#|+]+"
            title="Numbers, letters, blank space and  special characters (* # +)"
            tabindex="1"
            placer
            :invalid="$v.formData.multiple.$each[index].prefijo.$error"
          />
        </sw-input-group>

        <!-- FROM -->
        <sw-input-group
          v-if="prefix.typecustom.value == 'FT'"
          :label="$t('corePbx.internacional.from')"
          required
          :error="fromValidate($v.formData.multiple.$each[index].from)"
        >
          <sw-input
            v-model="prefix.from"
            :placeholder="$t('corePbx.internacional.from')"
            focus
            type="text"
            name="from"
            pattern="[0-9*|A-Za-z *|#|+]+"
            title="Numbers, letters, blank space and  special characters (* # +)"
            tabindex="1"
            placer
            :invalid="$v.formData.multiple.$each[index].from.$error"
          />
        </sw-input-group>
        <!-- TO -->
        <sw-input-group
          v-if="prefix.typecustom.value == 'FT'"
          :label="$t('corePbx.internacional.to')"
        >
          <sw-input
            v-model="prefix.to"
            :placeholder="$t('corePbx.internacional.to')"
            focus
            type="text"
            name="to"
            pattern="[0-9*|A-Za-z *|#|+]+"
            title="Numbers, letters, blank space and  special characters (* # +)"
            tabindex="1"
            placer
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('didFree.item.name')"
          required
          :error="nameValidate($v.formData.multiple.$each[index].name)"
        >
          <sw-input
            v-model="prefix.name"
            :placeholder="$t('didFree.item.name')"
            focus
            type="text"
            name="name"
            tabindex="1"
            placer
            :invalid="$v.formData.multiple.$each[index].name.$error"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('corePbx.packages.rate_per_minutes')"
          required
        >
          <sw-money
            v-model="prefix.rate_per_minutes"
            :currency="defaultCurrency"
            name="rate_per_minutes_selected"
            :invalid="$v.formData.multiple.$each[index].rate_per_minutes.$error"
          />
        </sw-input-group>

        <div
          class="md:col-span-2 xl:col-span-3 flex justify-end mt-3"
          v-if="!isEdit"
        >
          <sw-button
            v-if="formData.multiple.length - 1 == index"
            :variant="`primary-outline`"
            size="sm"
            @click="addPrefixe"
          >
            <PlusIcon class="h-4 w-4" />
          </sw-button>
          <sw-button
            v-if="formData.multiple.length - 1 !== index"
            :variant="`danger-outline`"
            size="sm"
            class="ml-4"
            @click="removePrefixe(index)"
          >
            <TrashIcon class="h-4 w-4" />
          </sw-button>
        </div>
      </div>
    </sw-card>

    <div class="pt-8 py-2 flex flex-col md:flex-row md:space-x-4">
      <sw-button
        :loading="isLoading"
        type="submit"
        variant="primary"
        size="lg"
        class="w-full md:w-auto"
        @click="submitRATE"
      >
        <save-icon class="w-6 h-6 mr-1 -ml-2 mr-2" v-if="!isLoading" />
        {{
          isEdit
            ? $t('corePbx.internacional.update_internacional')
            : $t('corePbx.internacional.save_internacional')
        }}
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
const {
  required,
  minLength,
  maxLength,
  minValue,
  helpers,
} = require('vuelidate/lib/validators')

export default {
  components: {
    //  MoreIcon,
    //   draggable,
    TrashIcon,
    //   PencilIcon,
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
      prefixrate_groups: [],
      isLoading: false,
      addMode: 'single',
      status: [
        { value: 'A', text: 'Active' },
        { value: 'I', text: 'Inactive' },
      ],
      category: [
        { value: 'C', text: 'Custom' },
        { value: 'I', text: 'International' },
        { value: 'T', text: 'Toll Free' },
      ],
      rate_per_minutes_selected: 0,
      country: null,
      typecustomOptions: [
        {
          label: 'Prefix',
          value: 'P',
        },
        {
          label: 'From / To',
          value: 'FT',
        },
      ],
      formData: {
        id: '',
        multiple: [
          {
            typecustom: {
              label: 'Prefix',
              value: 'P',
            },
            from: '',
            to: '',
            prefijo: '',
            name: '',
            rate_per_minutes: 0,
          },
        ],
        prefixrate_groups_id: [],
        country_id: null,
        status: { value: 'A', text: 'Active' },
        category: '',
      },
    }
  },
  computed: {
    ...mapGetters(['countries']),
    ...mapGetters('company', ['defaultCurrencyForInput']),

    defaultCurrency() {
      return {
        ...this.defaultCurrencyForInput,
        precision: 5,
      }
    },

    pageTitle() {
      if (this.isEdit) {
        return this.$t('corePbx.didFree.edit_did_free')
      } else {
        return this.$t('corePbx.didFree.add_did_free')
      }
    },
    isEdit() {
      if (this.$route.name === 'corepbx.internationalRate.edit') {
        return true
      }
      return false
    },
    customError() {
      if (!this.$v.formData.prefixrate_groups_id.$error) {
        return ''
      }
      if (!this.$v.formData.prefixrate_groups_id.required) {
        return this.$tc('validation.required')
      }
    },
    statusError() {
      /* if (!this.$v.formData.status.$error) {
        return ''
      } */
      if (!this.$v.formData.status.required) {
        return this.$tc('validation.required')
      }
    },
    categoryError() {
      if (!this.$v.formData.category.$error) {
        return ''
      }
      if (!this.$v.formData.category.required) {
        return this.$tc('validation.required')
      }
    },

    getDestinationGroups() {
      return this.prefixrate_groups.map((group) => {
        return {
          ...group,
          id: group.id,
          name: group.name,
        }
      })
    },
  },

  methods: {
    ...mapActions('internacionalrate', [
      'fetchInternacional',
      'updateInternacional',
      'addInternacional',
      'CargarCustomDestination',
    ]),

    async loadDID() {
      this.isRequestOnGoing = true
      let res = await this.CargarCustomDestination()
      this.prefixrate_groups = [...res.data.internacional]

      if (this.isEdit) {
        let res = await this.fetchInternacional(this.$route.params.id)
        let {
          prefix,
          rate_per_minute,
          status,
          category,
          country_id,
          name,
          rate_prefix_groups,
          typecustom,
          from,
          to,
        } = res.data.internacional

        const typePrefixIndex = this.typecustomOptions.findIndex(
          (option) => option.value === typecustom
        )
        this.formData.multiple = [
          {
            typecustom: this.typecustomOptions[typePrefixIndex],
            from: from,
            to: to,
            prefijo: prefix,
            name: name,
            rate_per_minutes: parseFloat(rate_per_minute),
          },
        ]
        /* this.formData.prefixrate_groups_id = this.prefixrate_groups.filter(item => item.id=prefixrate_groups_id)[0] */
        this.formData.status = this.status.filter(
          (element) => element.value == status
        )[0]
        this.formData.category = this.category.filter(
          (element) => element.value == category
        )[0]
        this.formData.country_id = this.countries.find(
          (county) => county.id === country_id
        )
        if (rate_prefix_groups) {
          this.formData.prefixrate_groups_id = rate_prefix_groups.map(
            (itemGroup) => {
              return {
                ...itemGroup,
                id: itemGroup.id,
                name: itemGroup.name,
              }
            }
          )
        }
      }

      this.isRequestOnGoing = false
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

    async submitRATE() {
      this.$v.formData.$touch()
      const validateForm = this.$v.formData
      if (
        !validateForm.prefixrate_groups_id.required ||
        !validateForm.status.required ||
        !validateForm.category.required
      )
        return false

      const searhInvalid = this.formData.multiple.findIndex((item) => {
        if (item.name == '' || item.name == null) return true
        if (item.typecustom == 'P') {
          if (item.prefijo == '' || item.prefijo == null) return true
        } else if (item.typecustom == 'FT') {
          if (item.from == '' || item.from == null) return true
        }
      })
      if (searhInvalid != -1) return false

      ///pregunta

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
          this.formData.country_id = this.formData.country_id
            ? this.formData.country_id.id
            : this.formData.country_id
          this.formData.status = this.formData.status.value
          this.formData.category = this.formData.category.value

          this.formData.multiple.map((item) => {
            item.typecustom = item.typecustom.value
          })
          try {
            let res
            this.isLoading = true

            if (this.isEdit) {
           
              const multiple = this.formData.multiple[0]
              this.formData.typecustom = multiple.typecustom
              this.formData.from = multiple.from
              this.formData.to = multiple.to
              this.formData.prefijo = multiple.prefijo
              this.formData.name = multiple.name
              this.formData.rate_per_minutes = multiple.rate_per_minutes
             
              this.formData.id = this.$route.params.id
              res = await this.updateInternacional(this.formData)
              window.toastr['success'](res.data.message)
              this.$router.push(
                '/admin/corePBX/billing-templates/international-rate'
              )
              return true
            } else {
             
              res = await this.addInternacional(this.formData)
              this.isLoading = false
              window.toastr['success'](res.data.success)
              this.$router.push(
                '/admin/corePBX/billing-templates/international-rate'
              )
              return true
            }
          } catch (error) {
            /* window.toastr['error'](error.ReferenceError) */
            window.toastr['error']('vaya error')
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
            this.formData.status = {
              value: 'A',
              text: 'Active',
            }
            this.isLoading = false
            return false
          }
        }
      })
    },

    addPrefixe() {
      this.formData.multiple.push({
        typecustom: { label: 'Prefix', value: 'P' },
        from: '',
        to: '',
        prefijo: '',
        name: '',
        rate_per_minutes: 0,
      })
    },
    removePrefixe(index) {
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
    fromValidate(value) {
      if (!value.$error) {
        return ''
      }
      if (!value.required) {
        return this.$tc('validation.required')
      }
    },
    nameValidate(value) {
      if (!value.$error) {
        return ''
      }
      if (!value.required) {
        return this.$tc('validation.required')
      }
    },
  },

  mounted() {
    this.$v.formData.$reset()
    this.loadDID()
  },
  validations: {
    formData: {
      multiple: {
        $each: {
          typecustom: {
            required,
          },
          from: {
            required,
          },
          prefijo: {
            required,
            minLength: minLength(1),
            maxLength: maxLength(32),
          },
          name: {
            required,
          },
          rate_per_minutes: {
            required,
          },
        },
      },
      prefixrate_groups_id: {
        required,
      },
      status: {
        required,
      },
      category: {
        required,
      },
    },
  },
}
</script>
