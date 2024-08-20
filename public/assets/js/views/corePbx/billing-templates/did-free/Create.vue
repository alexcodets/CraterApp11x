<template>
  <base-page class="item-create">
    <form action="" @submit.prevent="submitDID">
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
              focus
              type="text"
              name="prefijo"
              tabindex="1"
              placer
              @input="$v.formData.prefijo.$touch()"
            />
          </sw-input-group>
          <!-- STATUS required class="md:col-span-3 ml-2"-->
          <sw-input-group
            :label="$t('packages.status')"
            
          >
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

            <!-- class="md:col-span-3" -->
            <sw-input-group
                :label="$t('corePbx.packages.price')"
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
          {{ isEdit ? $t('didFree.update_did') : $t('didFree.save_did') }}
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
      isLoading: false,
      rate_per_minutes_selected:0,
      status: [
        { value: 'A', text: 'Active' },
        { value: 'I', text: 'Inactive' },
      ],
      formData: {
        id: '',
        prefijo: '',
        statu: { value: 'A', text: 'Active' },
        status: '',
        toll_free_category_id:null,
        rate_per_minutes:null,
      },
    }
  },
  computed: {
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
    ...mapActions('didtollfree', ['fetchOneDIDTOLLFREE', 'updateDIDTOLLFREE', 'addDIDTOLLFREE']),
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

        let { prefijo,status,toll_free_category_id,rate_per_minute } = res.data.ProfileDidTollFree
        this.rate_per_minutes_selected= parseFloat(rate_per_minute)
        
        this.formData.prefijo= prefijo;
        this.formData.statu = this.status.filter(
          (element) => element.value == status
        )[0]

        if (toll_free_category_id) {
          this.category = Cat.data.categories.data.filter(
            (element) => element.id == toll_free_category_id
          )[0]          
        }
      }

      this.isRequestOnGoing = false 
    },


    async submitDID() {
  
      this.$v.category.$touch()
      /* console.log( this.formData.toll_free_category_id); */
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
  
      try {
        let res
        this.isLoading = true  

        this.isEdit ? this.rate_per_minutes_selected= this.rate_per_minutes_selected.toString() : this.rate_per_minutes_selected
        let valor = this.rate_per_minutes_selected
        ? this.rate_per_minutes_selected.split(',') 
        : 0
        this.formData.rate_per_minutes =
        valor.length > 1
          ? this.rate_per_minutes_selected.replace(',', '.')
          : this.rate_per_minutes_selected
        if (this.isEdit) {
          this.formData.id = this.$route.params.id
          res = await this.updateDIDTOLLFREE(this.formData)
          window.toastr['success'](res.data.message)
          this.$router.push('/admin/corePBX/billing-templates/toll-free')
          return true
        } else {
          console.log("submitDID",this.formData);
          res = await this.addDIDTOLLFREE(this.formData);
          this.isLoading = false
          if (!this.isEdit) {
            window.toastr['success'](res.data.message);
            this.$router.push('/admin/corePBX/billing-templates/toll-free')
            return true
          }
        }
      } catch (error) {
        console.log("Esto es el errer",error);
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
    }, 

  },

  watch: {
    category(newValue) {
      console.log(newValue);
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
    rate_per_minutes_selected:{
      required
    },
     category: {
      required,
    },

    formData: {
      prefijo: {
        minLength: minLength(3),
        minValue: minValue(0.0),
        required
      }
    },
  },
}
</script>
