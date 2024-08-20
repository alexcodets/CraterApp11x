<template>
  <base-page class="item-create">
    <form action="" @submit.prevent="submitRATE">
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
      <div class="px-4 py-5 sm:p-8">
        <div class="grid gap-6 grid-col-1 md:grid-cols-2">
          <!--PREFIJO required class="md:col-span-3"-->
          <sw-input-group
            :label="$t('didFree.item.prefijo')"
            :error="prefijoError"
            required
          >
            <sw-input
              v-model="formData.prefijo"
              :placeholder="$t('didFree.item.prefijo')"
              :invalid="$v.formData.prefijo.$error"
              focus
              type="text"
              name="prefijo"
              pattern="[0-9*|A-Za-z *|#|+]+"
              title="Numbers, letters, blank space and  special characters (* # +)"
              tabindex="1"
              placer
              @input="$v.formData.prefijo.$touch()"
            />
          </sw-input-group>
<!--  :error="countryError" required :class="{ error: $v.formData.country_id.$error }"-->
          <sw-input-group
            :label="$tc('settings.company_info.country')"
            >
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
              :allow-empty="true"
              :placeholder="$t('general.select_status')"
              label="text"
              track-by="value"

            />
          </sw-input-group>

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
                  class="mt-2"
                  track-by="id"
                  label="name" 
                  :tabindex="4"
              />
          </sw-input-group>

           <sw-input-group
            :label="$t('didFree.item.name')"
            :error='displayNameError'
            required
          >
            <sw-input
              v-model="formData.name"
              :invalid="$v.formData.name.$error"
              :placeholder="$t('didFree.item.name')"
              focus
              type="text"
              name="name"
              tabindex="1"
              placer
              @input="$v.formData.prefijo.$touch()"
            />
          </sw-input-group>

          <sw-input-group
                :label="$t('corePbx.packages.rate_per_minutes')"
                class="md:col-span-3"
                :error="ratePerError"
                required
              >
                <sw-input
                  v-model="rate_per_minutes_selected"
                  :invalid="$v.rate_per_minutes_selected.$error"
                  :searchable="true"
                  :show-labels="false"
                  label="name"
                  numeric
                />
          </sw-input-group>
        
        </div>
      </div>

      <div class="mt-6 mb-4">
        <sw-button
          :loading="isLoading"
          variant="primary"
          type="submit"
          size="lg"
          class="flex justify-center w-full md:w-auto"
        >
          <save-icon class="mr-2 -ml-1" v-if="!isLoading" />
          {{ isEdit ? $t('corePbx.internacional.update_internacional') : $t('corePbx.internacional.save_internacional') }}
        </sw-button>
      </div>
    </form>
  </base-page>
</template>

<script>
import RightArrow from '@/components/icon/RightArrow'
import MoreIcon from '@/components/icon/MoreIcon'
import LeftArrow from '@/components/icon/LeftArrow'
import draggable from 'vuedraggable'
import AddChargesStub from '../../../../stub/additionalChargesDID'

import { mapActions, mapGetters } from 'vuex'
import {
  TrashIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
} from '@vue-hero-icons/solid'
const {
  required,
  minLength,
  maxLength,
  minValue,
} = require('vuelidate/lib/validators')

export default {
  components: {
  //  MoreIcon,
 //   draggable,
  //  TrashIcon,
 //   PencilIcon,
    ShoppingCartIcon,
  //  PlusIcon,
   // RightArrow,
   // LeftArrow,
  },


  data() {
    return {
      showSelect: false,
      isRequestOnGoing: false,
      category:null,
      prefixrate_groups:[],
      isLoading: false,
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
      formData: {
        id:'',
        prefijo: '',
        name:'',
        prefixrate_groups_id:[],
        country_id: null,
        status:{ value: 'A', text: 'Active' },
        category:'',
        rate_per_minutes:null,
      },
    }
  },
  computed: {
    ...mapGetters(['countries']),

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
    
    prefijoError() {
  
      if (!this.$v.formData.prefijo.$error) {
        return ''
      }
      if (!this.$v.formData.prefijo.required) {
        return this.$t('validation.required')
      }

      /* if (!this.$v.formData.prefijo.minLength) {
        return this.$tc(
          'validation.prefijo_min_length_character',
          this.$v.formData.prefijo.$params.minLength.min,
          { count: this.$v.formData.prefijo.$params.minLength.min }
        )
      } */
      if (!this.$v.formData.prefijo.maxLength) {
        return this.$tc(
          'validation.prefijo_max_length_character',
          this.$v.formData.prefijo.$params.maxLength,
          { count: 32 }
        )
      }
      
      /* if (!this.$v.formData.prefijo.minValue.min) {
        return this.$tc('validation.numbers_only')
      } */
    },

    displayNameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$t('validation.required')
      }
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
    ratePerError() {
      if (this.$v.rate_per_minutes_selected.$error) {
         return ''
      }
       if (!this.$v.rate_per_minutes_selected.required) {
        return this.$tc('validation.required')
      }
    },

    getDestinationGroups() {
            return this.prefixrate_groups.map((group) => {
                return {
                    ...group,
                    id: group.id,
                    name: group.name
                }
            })
        },
  },

  methods: {
    ...mapActions('internacionalrate', ['fetchInternacional', 'updateInternacional', 'addInternacional','CargarCustomDestination']),

    async loadDID() {
      this.isRequestOnGoing = true
      let res = await this.CargarCustomDestination();
      this.prefixrate_groups=[...res.data.internacional];
   
      if (this.isEdit) {
        let res = await this.fetchInternacional(this.$route.params.id)
        let { prefix,rate_per_minute,status,category,country_id,name, rate_prefix_groups} = res.data.internacional
        this.formData.prefijo = prefix
        this.formData.name = name
        /* this.formData.prefixrate_groups_id = this.prefixrate_groups.filter(item => item.id=prefixrate_groups_id)[0] */
        this.formData.status = this.status.filter(
          (element) => element.value == status
        )[0]
        this.formData.category = this.category.filter(
          (element) => element.value == category
        )[0]
        this.rate_per_minutes_selected= parseFloat(rate_per_minute)


           this.formData.country_id = this.countries.find(
            (county) =>
              county.id === country_id
          )
       if (rate_prefix_groups) {
          this.formData.prefixrate_groups_id = rate_prefix_groups.map((itemGroup) => {
              return {
                  ...itemGroup,
                  id: itemGroup.id,
                  name: itemGroup.name
              }
          });
        }
        
      }

      this.isRequestOnGoing = false 
    },


    async submitRATE() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      
      this.formData.country_id= this.formData.country_id ?  this.formData.country_id.id : this.formData.country_id 
      /* this.formData.prefixrate_groups_id = this.formData.prefixrate_groups_id.id */
      /* this.formData.country_id=this.country.id; */
      this.isEdit ? this.rate_per_minutes_selected= this.rate_per_minutes_selected.toString() : this.rate_per_minutes_selected
      let valor = this.rate_per_minutes_selected
        ? this.rate_per_minutes_selected.split(',') 
        : 0
      this.formData.rate_per_minutes =
        valor.length > 1
          ? this.rate_per_minutes_selected.replace(',', '.')
          : this.rate_per_minutes_selected
      this.formData.status=this.formData.status.value
      this.formData.category=this.formData.category.value
      
      try {
        let res
        this.isLoading = true  

        if (this.isEdit) {
          this.formData.id=this.$route.params.id;
          res = await this.updateInternacional(this.formData)
          window.toastr['success'](res.data.message)
          this.$router.push('/admin/corePBX/billing-templates/international-rate')
          return true
        } else {
          res = await this.addInternacional(this.formData);
          this.isLoading = false
          window.toastr['success'](res.data.success);
          this.$router.push('/admin/corePBX/billing-templates/international-rate')
          return true
        }
      } catch (error) {
        /* window.toastr['error'](error.ReferenceError) */
        window.toastr['error']("vaya error")
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
    }, 

  },


  mounted() {
    this.$v.formData.$reset()  
      this.loadDID()
  },
  validations: {
    rate_per_minutes_selected:{
      required
    },
    formData: {
      prefijo: {
        minLength: minLength(1),
        maxLength: maxLength(32),
       /*  minValue: minValue(0.0), */
        required
      },
      prefixrate_groups_id:{
        required,
      },
      name: {
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
