<template>
  <sw-card variant="setting-card">
    <!-- Base  -->
    <base-page v-if="isSuperAdmin" class="tax-group-create">
      <!--------- Form ---------->
      <form @submit.prevent="submitRetention">
        <!-- Header  -->
        <sw-page-header class="mb-3" :title="pageTitle">
          <sw-breadcrumb slot="breadcrumbs">
            <!-- <sw-breadcrumb-item
              to="/admin/dashboard"
              :title="$t('general.home')"
            /> -->
            <sw-breadcrumb-item
              to="/admin/settings/retentions"
              :title="$t('settings.retentions.retention')"
            />
            <sw-breadcrumb-item
              v-if="$route.name === 'retentions.edit'"
              to="#"
              :title="$t('settings.retentions.edit_retention')"
              active
            />
            <sw-breadcrumb-item
              v-else
              to="#"
              :title="$t('settings.retentions.new_retention')"
              active
            />
          </sw-breadcrumb>

          <template slot="actions">
            <sw-button
              :loading="isLoading"
              :disabled="isLoading"
              variant="primary"
              type="submit"
              size="lg"
              class="flex justify-center w-full md:w-auto"
            >
              <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
              {{
                isEdit
                  ? $t('settings.retentions.update_retention')
                  : $t('settings.retentions.save_retention')
              }}
            </sw-button>
          </template>
        </sw-page-header>

        <!-- sm:grid-cols-1 md:grid-cols-2 -->
        <div class="grid grid-cols-12">
          <div class="col-span-12">
            <sw-input-group
              :label="$t('settings.retentions.concept')"
              :error="conceptError"
              class="mb-4"
              required
            >
              <sw-textarea
                v-model.trim="formData.concept"
                :invalid="$v.formData.concept.$error"
                :placeholder="$t('settings.retentions.concept')"
                type="text"
                name="concept"
                rows="3"
                tabindex="1"
                @input="$v.formData.concept.$touch()"
              />
            </sw-input-group>

            <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-3">
              <sw-input-group
                :label="$t('settings.retentions.minimium_base_per_unit')"
                :error="minimiunBasePerUnitError"
                class="mb-4"
                required
              >
                <sw-input
                  v-model.trim="formData.minimium_base_per_unit"
                  :invalid="$v.formData.minimium_base_per_unit.$error"
                  :placeholder="
                    $t('settings.retentions.minimium_base_per_unit')
                  "
                  type="number"
                  name="minimium_base_per_unit"
                  @input="$v.formData.minimium_base_per_unit.$touch()"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('settings.retentions.minimium_base_in_currency')"
                :error="minimiunBaseInCurrencyError"
                class="mb-4"
                required
              >
                <sw-input
                  v-model.trim="formData.minimium_base_in_currency"
                  :invalid="$v.formData.minimium_base_in_currency.$error"
                  :placeholder="
                    $t('settings.retentions.minimium_base_in_currency')
                  "
                  type="number"
                  name="minimium_base_in_currency"
                  @input="$v.formData.minimium_base_in_currency.$touch()"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('settings.retentions.type')"
                class="mb-4"
                :error="typeError"
                required
              >
                <sw-select
                  v-model="formData.type"
                  :invalid="$v.formData.type.$error"
                  :options="type"
                  :searchable="true"
                  :show-labels="false"
                  :tabindex="3"
                  :allow-empty="false"
                  :placeholder="$t('settings.retentions.type')"
                  label="text"
                  track-by="value"
                />
              </sw-input-group>
            </div>

            <sw-input-group
              :label="$t('settings.retentions.percentage')"
              :error="percentageError"
              class="mb-4"
              required
            >
              <sw-money
                v-model="formData.percentage"
                :currency="defaultInput"
                :invalid="$v.formData.percentage.$error"
                class="relative w-full focus:border focus:border-solid focus:border-primary"
                @input="$v.formData.percentage.$touch()"
              />
              <!-- <sw-input
                v-model.trim="formData.percentage"
                :invalid="$v.formData.percentage.$error"
                :placeholder="$t('settings.retentions.percentage')"
                type="number"
                name="percentage"
                @input="$v.formData.percentage.$touch()"
              /> -->
            </sw-input-group>

            <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2">
              <sw-input-group
                :label="$t('settings.retentions.foreing')"
                :error="foreingError"
                class="mb-4"
                required
              >
                <sw-select
                  v-model="formData.foreing"
                  :invalid="$v.formData.foreing.$error"
                  :options="foreing"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="false"
                  :placeholder="$t('settings.retentions.foreing')"
                  label="text"
                />
                <!-- <sw-input
                  v-model.trim="formData.foreing"
                  :invalid="$v.formData.foreing.$error"
                  :placeholder="$t('settings.retentions.foreing')"
                  type="number"
                  name="foreing"
                  @input="$v.formData.foreing.$touch()"
                /> -->
              </sw-input-group>

              <sw-input-group
                :label="$t('settings.retentions.country')"
                class="mb-4"
                :error="countryError"
                required
              >
                <sw-select
                  v-model="formData.countries"
                  :invalid="$v.formData.country_id.$error"
                  :options="countries"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="false"
                  :tabindex="5"
                  :placeholder="$t('general.select_country')"
                  label="name"
                  track-by="id"
                  @select="countrySeleted"
                />
              </sw-input-group>

                <!-- v-if="setting.retention_active" -->
              <sw-input-group
                :label="$tc('customers.responsabilidad_fiscal')"
                class="md:col-span-3"
                required
              >
                <div class="flex grid md:grid-cols-2">
                  <sw-checkbox
                    v-model="formData.great_contributor"
                    class="mt-2"
                    :label="$t('customers.great_contributor')"
                    @change="changeFiscalResponsibility"
                    :disabled="
                      formData.not_applicable_others ||
                      this.type_vat_regime.value == '0'
                    "
                  />
                  <label class="mt-3 sm:ml-4"> 0 - 13 </label>

                  <sw-checkbox
                    v-model="formData.self_retaining"
                    class="mt-2"
                    :label="$t('customers.self_retaining')"
                    @change="changeFiscalResponsibility"
                    :disabled="
                      formData.not_applicable_others ||
                      this.type_vat_regime.value == '0'
                    "
                  />
                  <label class="mt-3 sm:ml-4"> 0 - 15 </label>

                  <sw-checkbox
                    v-model="formData.vat_withholding_agent"
                    class="mt-2"
                    :label="$t('customers.vat_withholding_agent')"
                    @change="changeFiscalResponsibility"
                    :disabled="
                      formData.not_applicable_others ||
                      this.type_vat_regime.value == '0'
                    "
                  />
                  <label class="mt-3 sm:ml-4"> 0 - 23 </label>

                  <sw-checkbox
                    v-model="formData.simple_tax_regime"
                    class="mt-2"
                    :label="$t('customers.simple_tax_regime')"
                    @change="changeFiscalResponsibility"
                    :disabled="
                      formData.not_applicable_others ||
                      this.type_vat_regime.value == '0'
                    "
                  />
                  <label class="mt-3 sm:ml-4"> 0 - 47 </label>

                </div>

                <div class="mt-5">
                  <label class="text-muted">
                    {{ $tc('customers.responsabilidad_fiscal_desc') }}
                  </label>
                </div>
              </sw-input-group>
            </div>
          </div>
        </div>
      </form>
    </base-page>
  </sw-card>
</template>

<script>
import draggable from 'vuedraggable'
import { mapActions, mapGetters } from 'vuex'
// import { required, requiredIf } from 'vuelidate/lib/validators';
import {
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
  TrashIcon,
} from '@vue-hero-icons/solid'
const { required, minLength, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    draggable,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
    TrashIcon,
  },
  data() {
    return {
      countries: [],
      foreing: [
        {
          value: 'yes',
          text: 'Yes',
        },
        {
          value: 'no',
          text: 'No',
        },
      ],
      isLoading: false,
      title: 'Add Retention',
      states: [],
      type: [
        {
          value: 'percentage',
          text: 'Percentage',
        },
        {
          value: 'fixed',
          text: 'Fixed',
        },
      ],
      type_vat_regime: {
        value: '1',
        text: 'VAT Manager',
      },
      defaultInput: {
        decimal: '.',
        thousands: ',',
        prefix: '% ',
        precision: 2,
        masked: false,
      },
      formData: {
        concept: '',
        country_id: '',
        countries: [],
        foreing: null,
        great_contributor: false,
        minimium_base_per_unit: null,
        minimium_base_in_currency: null,
        percentage: null,
        self_retaining: false,
        simple_tax_regime: false,
        state_name: '',
        type: {
          value: null,
          text: null,
        },
        type_vat_regime: {
          value: '1',
          text: 'VAT Manager',
        },
        vat_withholding_agent: false,
      },
      type_vat_regime_options: [
        { value: '1', text: 'VAT Manager' },
        { value: '0', text: 'Not responsible for VAT' },
      ],
    }
  },
  validations: {
    formData: {
      concept: {
        maxLength: maxLength(255),
        required,
      },
      type: {
        required,
      },
      minimium_base_per_unit: {
        required,
      },
      minimium_base_in_currency: {
        required,
      },
      percentage: {
        required,
      },
      foreing: {
        required,
      },
      country_id: {
        required,
      },
    },
  },
  computed: {
    
    ...mapGetters('user', ['currentUser']),
   
    // ...mapGetters('company', ['defaultCurrency']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    pageTitle() {
      if (this.$route.name === 'retentions.edit') {
        return this.$t('settings.retentions.edit_retention')
      }
      return this.$t('settings.retentions.new_retention')
    },

    isEdit() {
      if (this.$route.name === 'retentions.edit') {
        return true
      }
      return false
    },

    conceptError() {
      if (!this.$v.formData.concept.$error) {
        return ''
      }
      if (!this.$v.formData.concept.required) {
        return this.$t('validation.required')
      }
    },
    minimiunBasePerUnitError() {
      if (!this.$v.formData.minimium_base_per_unit.$error) {
        return ''
      }
      if (!this.$v.formData.minimium_base_per_unit.required) {
        return this.$t('validation.required')
      }
    },
    minimiunBaseInCurrencyError() {
      if (!this.$v.formData.minimium_base_in_currency.$error) {
        return ''
      }
      if (!this.$v.formData.minimium_base_in_currency.required) {
        return this.$t('validation.required')
      }
    },
    typeError() {
      if (!this.$v.formData.type.$error) {
        return ''
      }
      if (!this.$v.formData.type.required) {
        return this.$t('validation.required')
      }
    },
    percentageError() {
      if (!this.$v.formData.percentage.$error) {
        return ''
      }
      if (!this.$v.formData.percentage.required) {
        return this.$t('validation.required')
      }
    },
    foreingError() {
      if (!this.$v.formData.foreing.$error) {
        return ''
      }
      if (!this.$v.formData.foreing.required) {
        return this.$t('validation.required')
      }
    },
    countryError() {
      if (!this.$v.formData.country_id.$error) {
        return ''
      }
      if (!this.$v.formData.country_id.required) {
        return this.$t('validation.required')
      }
    },
  },
  created() {
    // this.loadTaxMembership()
    this.fetchInitDataCountry()
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
    if (this.isEdit) {
      this.loadEditRetention()
    }
  },
  mounted() {
    // this.fetchTax()
    this.$v.formData.$reset()
  },
  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('retentions', [
      'addRetention',
      'fetchRetention',
      'updateRetention',
      /*'fetchTaxMembership',*/
    ]),

    changeFiscalResponsibility() {
      if (
        this.formData.not_applicable_others ||
        this.type_vat_regime.value == '0'
      ) {
        this.formData.great_contributor = false
        this.formData.self_retaining = false
        this.formData.vat_withholding_agent = false
        this.formData.simple_tax_regime = false
      }
    },

    async countrySeleted(val) {
      /*  let res = await window.axios.get('/api/v1/states/' + val.code)
      if (res) {
        this.states = res.data.states
      } */
      this.formData.countries = val
      this.formData.country_id = val.id

    },
    async fetchInitDataCountry() {
      this.initLoad = true
      let res = await window.axios.get('/api/v1/countries')
      if (res) {
        this.countries = res.data.countries
      }
      this.initLoad = false
    },

    filterByReference(arr1, arr2) {
      let res = []
      res = arr1.filter((el) => {
        return !arr2.find((element) => {
          return element.id === el.id
        })
      })
      return res
    },
    async loadEditRetention() {
      let response = await this.fetchRetention(this.$route.params.id)

      // console.log('response: ', response);
      this.formData = response.data.retention
      this.formData.type = this.type.find(
        (t) => t.value == this.formData.type_of_minimium_base_in_currency
      )
      this.formData.countries = this.countries.find(
        (c) => c.id == this.formData.country_id
      )
      // responsability fiscal data
      this.formData.great_contributor = response.data.retention.great_contributor
        ? true
        : false
      this.formData.self_retaining = response.data.retention.self_retaining
        ? true
        : false
      this.formData.vat_withholding_agent = response.data.retention
        .vat_withholding_agent
        ? true
        : false
      this.formData.simple_tax_regime = response.data.retention.simple_tax_regime
        ? true
        : false
      // foering
      if (response.data.retention.foreing === 1 ) {
        this.formData.foreing = {
          value: 'yes',
          text: 'Yes',
        }
      } else {
        this.formData.foreing = {
          value: 'no',
          text: 'No',
        }
      }
    },
    async submitRetention() {
      //console.log('submit retentions')
      this.$v.formData.$touch()
      
      if (this.$v.$invalid) {
        return true
      }

      if (this.formData.countries) {
        this.formData.country_id = this.formData.countries.id
      }
      //console.log('this form data countries')
      
      
      this.formData.foreing = this.formData.foreing.value;
      this.formData.type_of_minimium_base_in_currency = this.formData.type.value
      //console.log('this form data countries')
      try {
        let response
        this.isLoading = true

        const data = {
          module: "retentions" 
        }
        const permissions = await this.getUserModules(data)
        // valida que el usuario tenga permiso para ingresar al modulo
        if(permissions.super_admin == false){
          if(permissions.exist == false ){
            this.$router.push('/admin/dashboard')
          }else {
          const modulePermissions = permissions.permissions[0]
            if(modulePermissions.create == 0 && this.isEdit == false){
              this.$router.push('/admin/dashboard')
            }else if(modulePermissions.update == 0 && this.isEdit == true ){
              this.$router.push('/admin/dashboard')
            }
          }
        }
        if (this.isEdit) {
          response = await this.updateRetention(this.formData)
          if (response.status === 200) {
            window.toastr['success'](
              this.$t('settings.retentions.updated_message')
            )
            this.$router.push('/admin/settings/retentions')
          }
          if (response.data.error) {
            window.toastr['error'](response.data.error)
          }
        } else {
          response = await this.addRetention(this.formData)
          //console.log(response)
          if (response.status === 200) {
            window.toastr['success'](
              this.$tc('settings.retentions.created_message')
            )
            this.$router.push('/admin/settings/retentions')
          }
          if (response.data.error) {
            window.toastr['error'](response.data.error)
          }
        }

        this.isLoading = false
        return true
      } catch (err) {
        //console.log(err)
      }
    },
  },
}
</script>


