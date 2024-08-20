<template>
  <base-page>
    <!-- Page Header -->
    <sw-page-header class="mb-3" title="">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="#" />
        <!--Modificación breadcumbs corePOS Alejandro 11 Julio 2023-->        
        <sw-breadcrumb-item :title="$t('core_pos.title')" to="#" active />
        <!--<sw-breadcrumb-item :title="$tc('items.item', 2)" to="#" />-->
        <!--<sw-breadcrumb-item :title="$t('items.new_item')" to="#" active />-->
      </sw-breadcrumb>
    </sw-page-header>
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <div class="grid grid-cols-12 ">
      <div class="col-span-12">
        <sw-card variant="setting-card h-full">
          <div>
            <form class="relative h-full" @submit.prevent="saveData">
              <template slot="header">
                <h6 class="sw-section-title">
                  {{ $t("settings.core_pos.core_pos") }}
                </h6>
              </template>
              <div class="grid gap-12 grid-col-1 md:grid-cols-3 mt-4">
                <sw-input-group :label="$t('settings.core_pos.core_pos_prefix')">
                  <sw-input v-model="formData.core_pos_prefix" style="max-width: 30%"
                    @keyup="changeToUppercase('PREFIX')" />
                </sw-input-group>
              </div>
              <sw-button :loading="isLoading" :disabled="isLoading" class="mt-10" variant="primary">
                <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
                {{ $tc("settings.account_settings.save") }}
              </sw-button>
            </form>
          </div>
          <sw-divider class="mt-6 mb-8" />
          <div>
   
          </div>
          <div class="mb-4">
            <!-- SAVE ITEMS CATEGORIES -->
            <form class="relative  mt-6" style="z-index: 10;" action="" @submit.prevent="saveItemsCategory">
              <h3 class="mb-5 text-lg font-medium text-black">
                    {{ $t("settings.core_pos.items_category") }}
                  </h3>
              <div>
                <div class="flex mb-3">
                  <sw-input-group :label="$t('settings.core_pos.items_category')" class="mb-1">
                    <sw-select v-model="item_categories" :options="item_categories_options" :searchable="true"
                      :show-labels="false" :allow-empty="true" :multiple="true" class="mt-2" track-by="id" label="name"
                      :tabindex="4" />
                  </sw-input-group>
                </div>
                <sw-button :loading="isLoading" :disabled="isLoading" variant="primary"  class="mt-4">
                  <save-icon v-if="!isLoading" class="mr-2" />
                  {{ $t("settings.customization.save") }}
                </sw-button>
              </div>
            </form>
          </div>

          <div class="mb-4">
            <!-- SAVE ITEMS CATEGORIES -->
            <form class="relative  mt-6" style="z-index: 1;" action="" @submit.prevent="savePaymentMethods">
              <h3 class="mb-5 text-lg font-medium text-black">
                    {{ $t("settings.core_pos.payment_methods") }}
                  </h3>
              <div>
                <div class="flex mb-3">
                  <sw-input-group :label="$t('settings.core_pos.payment_methods')" class="mb-1">
                    <sw-select
                      v-model="payment_methods"
                      :options="payment_methods_options"
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
                </div>
                <sw-button :loading="isLoading" :disabled="isLoading" variant="primary"  class="mt-4">
                  <save-icon v-if="!isLoading" class="mr-2" />
                  {{ $t("settings.customization.save") }}
                </sw-button>
              </div>
            </form>
          </div>

          <div class="mt-4">
            <form action="" class=" relative h-full mt-6" @submit.prevent="saveConfig">
              <!-- PDF INVOICE -->
              <div>
                  <h3 class="mb-5 text-lg font-medium text-black">
                    {{ $t("settings.customization.invoices.pos_invoice") }}
                  </h3>

                <div class="flex mb-6">
                  <div class="relative w-12">
                    <sw-switch v-model="allow_invoice_form_pos" class="absolute" style="top: -20px" />
                  </div>

                  <div class="ml-4">
                    <p class="p-0 mb-1 text-base leading-snug text-black">
                      {{ $t("settings.customization.invoices.pos_invoice") }}
                    </p>
                    <p class="p-0 m-0 text-xs leading-tight text-gray-500" style="max-width: 480px">
                      {{ $t("settings.customization.invoices.allow_invoice_form_pos") }}
                    </p>
                  </div>
                </div>
              </div>
              <!-- PDF POS FORMAT -->
              <div>
                <h3 class="mb-5 text-lg font-medium text-black">
                  {{ $t("settings.customization.invoices.pdf_pos_format") }}
                </h3>
                <div class="flex mb-6">
                  <div class="relative w-12">
                    <sw-switch v-model="pdf_format_pos" class="absolute" style="top: -20px" />
                  </div>

                  <div class="ml-4">
                    <p class="p-0 mb-1 text-base leading-snug text-black">
                      {{ $t("settings.customization.invoices.pdf_pos_format") }}
                    </p>
                    <p class="p-0 m-0 text-xs leading-tight text-gray-500" style="max-width: 480px">
                      {{ $t("settings.customization.invoices.change_pdf_pos") }}
                    </p>
                  </div>
                </div>
              </div>
              <!-- ACTIVATE PAY BUTTON -->
              <div class="mt-5">
                <h3 class="mb-5 text-lg font-medium text-black">
                  {{ $t("settings.core_pos.activate_pay_button") }}
                </h3>
                <div class="flex mb-6">
                  <div class="relative w-12">
                    <sw-switch v-model="activate_pay_button" class="absolute" style="top: -20px" />
                  </div>

                  <div class="ml-4">
                    <p class="p-0 mb-1 text-base leading-snug text-black">
                      {{ $t("settings.core_pos.activate_pay_button") }}
                    </p>
                    <p class="p-0 m-0 text-xs leading-tight text-gray-500" style="max-width: 480px">
                      {{ $t("settings.core_pos.activate_pay_button") }}
                    </p>
                  </div>
                </div>
                <!-- PARTIAL PAY ALEJANDRO 12 jUL23-->
                <h3 class="mb-5 text-lg font-medium text-black">
                  {{ $t("settings.core_pos.allow_partial_pay") }}
                </h3>
                <div class="flex mb-6">
                  <div class="relative w-12">
                    <sw-switch v-model="allow_partial_pay" class="absolute" style="top: -20px" />
                  </div>

                  <div class="ml-4">
                    <p class="p-0 mb-1 text-base leading-snug text-black">
                      {{ $t("settings.core_pos.allow_partial_pay") }}
                    </p>
                    <p class="p-0 m-0 text-xs leading-tight text-gray-500" style="max-width: 480px">
                      {{ $t("settings.core_pos.allow_partial_pay") }}
                    </p>
                  </div>
                </div>
                <!-- AUTO PRINT ALEJANDRO 12 jUL23-->
                <h3 class="mb-5 text-lg font-medium text-black">
                  {{ $t("settings.core_pos.autoprint_pdf_pos") }}
                </h3>
                <div class="flex mb-6">
                  <div class="relative w-12">
                    <sw-switch v-model="autoprint_pdf_pos" class="absolute" style="top: -20px" />
                  </div>

                  <div class="ml-4">
                    <p class="p-0 mb-1 text-base leading-snug text-black">
                      {{ $t("settings.core_pos.autoprint_pdf_pos") }}
                    </p>
                    <p class="p-0 m-0 text-xs leading-tight text-gray-500" style="max-width: 480px">
                      {{ $t("settings.core_pos.autoprint_pdf_pos") }}
                    </p>
                  </div>
                </div>
                <!---->
                <sw-button :loading="isLoading" :disabled="isLoading" variant="primary" type="submit" class="mt-4">
                  <save-icon v-if="!isLoading" class="mr-2" />
                  {{ $t("settings.customization.save") }}
                </sw-button>
              </div>
            </form>
          </div>

<!-- apartados start -->

     <sw-divider class="mt-6 mb-8" />
      <h1>{{$t('settings.core_pos.core_pos_section')}}</h1>

    <div class="flex flex-wrap justify-end mt-8 lg:flex-nowrap"  >
      <sw-button size="lg" variant="primary-outline" @click="addSection">
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('settings.core_pos.core_pos_create_section') }}
      </sw-button>
    </div>

    <sw-table-component
      ref="tableSection"
      variant="gray"
      :data="fetchDataSections"
      :show-filter="false"
    >
      <sw-table-column
        :sortable="true"
        :label="$t('settings.core_pos.core_pos_section_name')"
        show="name"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.core_pos.core_pos_section_name') }}</span>
          <span class="mt-6">{{ row.name }}</span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="false"
        :filterable="false"
        cell-class="action-dropdown"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.tax_types.action') }}</span>
          <sw-dropdown>
            <dot-icon slot="activator" class="h-5 mr-3 text-primary-800" />

            <sw-dropdown-item @click="editSection(row)" >
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.edit') }}
            </sw-dropdown-item>

            <!-- <sw-dropdown-item @click="removeItemUnit(row.id)" v-if="permission.delete" >
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item> -->
          </sw-dropdown>
        </template>
      </sw-table-column>
    </sw-table-component>

<!-- apartados end -->
          
        </sw-card>
      </div>
    </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters, mapState } from 'vuex'
import { CloudUploadIcon, PlusIcon } from '@vue-hero-icons/solid'
import VueTailwindColorPicker from 'vue-tailwind-color-picker'
import {
  DocumentDuplicateIcon,
  EyeOffIcon,
  EyeIcon,
  CheckIcon,
  TrashIcon,
} from '@vue-hero-icons/solid'

const {
  required,
  maxLength,
  alpha,
  requiredIf,
  email,
} = require('vuelidate/lib/validators')
export default {
  components: {
    CloudUploadIcon,
    VueTailwindColorPicker,
    PlusIcon
  },

  data() {
    return {
      allow_invoice_form_pos: true,
      pdf_format_pos: false,
      activate_pay_button: false,
      allow_partial_pay: false,
      autoprint_pdf_pos: false,
      formData: {
        core_pos_prefix: null,
      },
      isLoading: false,
      isRequestOnGoing: false,
      item_categories: [],
      item_categories_options: [],
      //
      payment_methods: [],
      payment_methods_options: [],
    }
  },
  created() {
    this.loadData()
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('company', ['updateCompanySettings', 'fetchCompanySettings']),
    ...mapActions('item', ['fetchItemCategories']),
    ...mapActions('payment', ['fetchPaymentModes']),    
    ...mapActions('corePos', ['addPosItemCategories', 'fetchPosItemCategory','addPosPaymentMethods', 'fetchPosPaymentMethods','fetchSections']),

    async loadData() {
      this.isRequestOnGoing = true
      let res = await this.fetchCompanySettings([
        'core_pos_prefix',
        'pdf_format_pos',
        'activate_pay_button',
        'allow_partial_pay',
        'autoprint_pdf_pos',
        'allow_invoice_form_pos',
      ])
      this.formData.core_pos_prefix = res.data.core_pos_prefix
      this.isRequestOnGoing = false

      this.pdf_format_pos = res.data.pdf_format_pos == '0' ? false : true
      this.activate_pay_button =
        res.data.activate_pay_button == '0' ? false : true
      this.allow_partial_pay = res.data.allow_partial_pay == '0' ? false : true 
      this.autoprint_pdf_pos = res.data.autoprint_pdf_pos == '0' ? false : true
      this.allow_invoice_form_pos = res.data.allow_invoice_form_pos == '0' ? false : true

      let responseItemCategoriesOptions = await this.fetchItemCategories()
      this.item_categories_options = responseItemCategoriesOptions.data.item_categories

      let responsePaymentMethodsOption = await this.fetchPaymentModes({limit : "all"})
      this.payment_methods_options = [ ...responsePaymentMethodsOption.data.paymentMethods.data]
      
      //
      let responseItemCategories = await this.fetchPosItemCategory()

      responseItemCategories.data.data.map(
        (item) => {
          try{
            let temp = this.item_categories_options.find(element => element.id == item.item_category_id )
            this.item_categories.push(temp)
          }catch(error){
            
          }
        }
      )
      let responsePaymentMethods = await this.fetchPosPaymentMethods()

      responsePaymentMethods.data.data.map(
        (pm) => {
          try{
            let temp = this.payment_methods_options.find(element => element.id == pm.payment_method_id )
            this.payment_methods.push(temp)
          }catch(error){
            
          }
        }
      )
      //

    },

    async editSection(data) {
      this.openModal({
        title: this.$t('settings.customization.items.edit_item_unit'),
        componentName: 'SectionModal',
        id: data.id,
        data: data,
        refreshData: this.$refs.tableSection.refresh,
      })
    },

    async addSection() {
      this.openModal({
        title: this.$t('settings.core_pos.core_pos_create_section'),
        componentName: 'SectionModal',
        refreshData: this.$refs.tableSection.refresh,
      })
    },

    async fetchDataSections({ page, filter, sort }){
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      const response = await this.fetchSections(data)

      return {
        data: response.data.sections.data,
        pagination: {
          totalPages: response.data.sections.last_page,
          currentPage: page,
          count: response.data.sections.count,
        },
      }
    },

    changeToUppercase(currentTab) {
      if (currentTab === 'PREFIX') {
        this.formData.core_pos_prefix =
          this.formData.core_pos_prefix.toUpperCase()
        return true
      }
    },

    async saveData() {
      let data = {
        settings: {
          core_pos_prefix: this.formData.core_pos_prefix,
        },
      }

      if (this.updateSetting(data)) {
        window.toastr['success'](
          this.$t('settings.core_pos.update_prefix_message')
        )
      }
    },

    async saveItemsCategory() {

      const response = await this.addPosItemCategories({data: this.item_categories})
      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },

    async savePaymentMethods() {
      const response = await this.addPosPaymentMethods({data: this.payment_methods})
      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },

    async updateSetting(data) {
      this.isLoading = true
      let res = await this.updateCompanySettings(data)

      if (res.data.success) {
        this.isLoading = false
        return true
      }

      return false
    },

    async saveConfig() {
      let data = {
        settings: {
          pdf_format_pos: this.pdf_format_pos,
          activate_pay_button: this.activate_pay_button,
          allow_partial_pay: this.allow_partial_pay,
          autoprint_pdf_pos: this.autoprint_pdf_pos,         
          allow_invoice_form_pos: this.allow_invoice_form_pos,
        },
      }

      let response = await this.updateCompanySettings(data)

      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
      this.$router.go()
    },

    changeToUppercase(currentTab) {
      if (currentTab === 'INVOICES') {
        this.invoices.invoice_prefix =
          this.invoices.invoice_prefix.toUpperCase()

        return true
      }
    },
  },
}
</script>
<style scoped>
.inputFile {
  opacity: 0;
  overflow: hidden;
  position: absolute;
  z-index: -1;
}

.logo {
  height: 300px;
  width: 300px;
  border-radius: 150px;
  margin-left: 19%;
}
</style>
