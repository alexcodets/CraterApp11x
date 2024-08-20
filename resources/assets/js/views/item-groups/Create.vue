<template>
  <!-- Base  -->
  <base-page v-if="isSuperAdmin" class="option-group-create">
    <!--------- Form ---------->
    <form action="" @submit.prevent="submitItemGroup">
      <!-- Header  -->
      <sw-page-header class="mb-3" :title="pageTitle">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            to="/admin/dashboard"
            :title="$t('general.home')"
          />
          <sw-breadcrumb-item
            to="/admin/item-groups"
            :title="$t('item_groups.item_group')"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'item-groups.edit'"
            to="#"
            :title="$t('item_groups.edit_items_group')"
            active
          />
          <sw-breadcrumb-item
            v-else
            to="#"
            :title="$t('item_groups.new_items_group')"
            active
          />
        </sw-breadcrumb>

        <template slot="actions">
          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary-outline"
            type="button"
            class="mr-3 text-sm hidden sm:flex"
            size="lg"
            @click="cancelForm()"
          >
            <x-circle-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ $t('general.cancel') }}
          </sw-button>
          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="hidden sm:flex"
            tabindex="6"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{
              isEdit
                ? $t('item_groups.update_items_group')
                : $t('item_groups.save_items_group')
            }}
          </sw-button>
        </template>
      </sw-page-header>

      <div class="grid grid-cols-12">
        <div class="col-span-12">
          <sw-card class="mb-8">
            <sw-input-group
              :label="$t('item_groups.name')"
              :error="nameError"
              class="mb-4"
              required
            >
              <sw-input
                v-model.trim="formData.name"
                :invalid="$v.formData.name.$error"
                class="mt-2"
                focus
                type="text"
                name="name"
                tabindex="1"
                @input="$v.formData.name.$touch()"
              />
            </sw-input-group>

            <sw-input-group :label="$t('general.category')" class="mb-4">
              <sw-select
                v-model="formData.item_categories"
                :options="getItemCategories"
                :searchable="true"
                :show-labels="false"
                class="mt-2"
                track-by="id"
                label="name"
                :placeholder="'Select an Item Category'"
                :tabindex="10"
                :multiple="true"
              />
            </sw-input-group>
            <sw-divider class="mb-5 md:mb-8" />

            <h6 class="col-span-5 sw-section-title lg:col-span-1">
              {{ $t('items.section_pos') }}
            </h6>
            <div class="flex my-8 mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.allow_pos"
                  class="absolute"
                  style="top: -20px"
                  :tabindex="6"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('items.allow_pos') }}
                </p>
              </div>
            </div>

            <sw-input-group
                :label="$t('core_pos.store')"
                class="mb-4"
              >
                <sw-select
                  v-model="formData.item_store"
                  :options="store_options"
                  :searchable="true"  
                  :show-labels="false"
                  :allow-empty="true"
                  :multiple="true"
                  class="mt-2"
                  track-by="id"
                  label="name"
                  :tabindex="1"
                />
              </sw-input-group>

            <sw-divider class="mb-5 md:mb-8" />

            <sw-input-group :label="$tc('items.image')" class="mb-4">
              <div
                id="logo-box"
                class="relative flex items-center justify-center h-24 p-5 mt-2 bg-transparent border-2 border-gray-200 border-dashed rounded-md image-upload-box"
              >
                <img
                  v-if="previewPicture"
                  :src="previewPicture"
                  class="absolute opacity-100 preview-logo"
                  style="max-height: 80%; animation: fadeIn 2s ease"
                />
                <div v-else class="flex flex-col items-center">
                  <cloud-upload-icon
                    class="h-5 mb-2 text-xl leading-6 text-gray-400"
                  />
                  <p class="text-xs leading-4 text-center text-gray-400">
                    Drag a file here or
                    <span
                      id="pick-avatar"
                      class="cursor-pointer text-primary-500"
                    >
                      browse
                    </span>
                    to choose a file
                  </p>
                </div>
              </div>

              <sw-avatar
                trigger="#logo-box"
                :preview-avatar="previewPicture"
                @changed="onChange"
                @uploadHandler="onUploadHandler"
                @handleUploadError="onHandleUploadError"
              >
                <template v-slot:icon>
                  <cloud-upload-icon
                    class="h-5 mb-2 text-xl leading-6 text-gray-400"
                  />
                </template>
              </sw-avatar>
            </sw-input-group>
            <sw-input-group
              :label="$t('item_groups.description')"
              :error="descriptionError"
              class="mb-4"
            >
              <sw-editor
                v-model="formData.description"
                :set-editor="formData.description"
                rows="2"
                name="description"
                tabindex="2"
                @input="$v.formData.description.$touch()"
              />
            </sw-input-group>
            <div class="flex my-8">
              <div class="relative w-12">
                <sw-checkbox
                  v-model="formData.no_taxable"
                  class="absolute"
                  tabindex="3"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('items.no_taxable') }}
                </p>

                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('items.no_tax_description') }}
                </p>
              </div>
            </div>
          </sw-card>

          <!-- Items -->
          <table class="w-full text-center item-table">
            <colgroup>
              <col style="width: 60%" />
              <col style="width: 40%" />
            </colgroup>
            <thead class="bg-white border border-gray-200 border-solid">
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span class="pl-12">
                  {{ $tc('items.item', 2) }}
                </span>
              </th>
            
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span class="pr-10">
                  {{ $t('item_groups.item.price') }}
                </span>
              </th>
            </thead>

            <draggable
              v-model="formData.items"
              class="item-body"
              tag="tbody"
              handle=".handle"
            >
              <items-group-item
                v-for="(item, index) in formData.items"
                :key="item.id"
                :index="index"
                :item-data="item"
                :group-items="formData.items"
                :currency="currency"
                @remove="removeItem"
                @update="updateItem"
                @itemValidate="checkItemsData"
                @checkExists="checkExistItem"
              />
            </draggable>
          </table>
          <div
            class="flex items-center justify-center w-full px-6 py-3 text-base border-b border-gray-200 border-solid cursor-pointer text-primary-400 hover:bg-gray-200"
            @click="addItem"
          >
            <shopping-cart-icon class="h-5 mr-2" />
            {{ $t('item_groups.add_item') }}
          </div>
        </div>
      </div>


      <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary-outline"
            type="button"
            class="mr-3 flex w-full mt-4 sm:hidden md:hidden"
            size="lg"
            @click="cancelForm()"
          >
            <x-circle-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ $t('general.cancel') }}
          </sw-button>
          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="flex w-full mt-4 mb-2 mb-md-0 sm:hidden md:hidden"
            tabindex="6"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{
              isEdit
                ? $t('item_groups.update_items_group')
                : $t('item_groups.save_items_group')
            }}
          </sw-button>
    </form>
  </base-page>
</template>

<script>
import draggable from 'vuedraggable'
import ItemsGroupItem from './Item'
import ItemGroupStub from '../../stub/itemGroup'
import { mapActions, mapGetters } from 'vuex'
import Guid from 'guid'
import {
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
  CloudUploadIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'

const { required, minLength, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    ItemsGroupItem,
    draggable,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
    CloudUploadIcon,
    XCircleIcon,
  },
  data() {
    return {
      isLoading: false,
      title: 'Add Items Group',
      store_options: [],
      formData: {
        name: '',
        description: '',
        no_taxable: false,
        allow_pos: false,
        item_store: [],
        items: [
          {
            ...ItemGroupStub,
            id: Guid.raw(),
          },
        ],
        item_categories: [],
      },
      selectedCurrency: '',
      previewPicture: null,
      fileObject: null,
      cropperOutputMime: '',
      getItemCategories: [],
    }
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
        maxLength: maxLength(120),
      },

      description: {
        maxLength: maxLength(65000),
      },
    },
  },
  computed: {
    ...mapGetters('user', ['currentUser']),

    ...mapGetters('company', ['defaultCurrency']),

    currency() {
      return this.selectedCurrency
    },

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    pageTitle() {
      if (this.$route.name === 'item-groups.edit') {
        return this.$t('item_groups.edit_items_group')
      }
      return this.$t('item_groups.new_items_group')
    },

    isEdit() {
      if (this.$route.name === 'item-groups.edit') {
        return true
      }
      return false
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }

      if (!this.$v.formData.name.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },

    descriptionError() {
      if (!this.$v.formData.description.$error) {
        return ''
      }

      if (!this.$v.formData.description.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },

    async getStores(){
      if(this.isEdit){
        const items = this.formData.items
        const data =  items.map(objeto => objeto.id);
        const responseStores = await this.getStoreItems({items: data})
        this.formData.item_store = responseStores.data.stores 
      }
    },
  },

  watch:{
    async getStores(){
      if(this.isEdit){
        const items = this.formData.items
        const data =  items.map(objeto => objeto.id);
        const responseStores = await this.getStoreItems({items: data})
        this.formData.item_store = responseStores.data.stores 
      }
    },
  },
  async created() {

    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }

    let dataStore = {
          limit: 'all'
        }

      const responseStore = await this.fetchStores(dataStore)
      this.store_options = responseStore.data.stores.data

    const response = await this.fetchItemCategories()
    if (response.data.item_categories.length > 0) {
      this.getItemCategories = [...response.data.item_categories]
    }

    if (this.isEdit) {
      this.loadEditItemGroup()

     
    }
    this.fetchItems({
      filter: {},
      orderByField: '',
      orderBy: '',
      limit: 'all',
    })

    

    
  },
  mounted() {
    this.$v.formData.$reset()
  },
  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('itemGroups', [
      'addItemGroup',
      'fetchItemGroup',
      'updateItemGroup',
      'uploadPicture',
      'fetchItemCategories',
    ]),

    ...mapActions('corePos', ['fetchStores', 'getStoreItems']),
    ...mapActions('item', ['fetchItems']),

    async loadEditItemGroup() {
      let response = await this.fetchItemGroup(this.$route.params.id)

      if (response.data) {
        this.formData = { ...this.formData, ...response.data.itemGroup }

        this.previewPicture = response.data.itemGroup.picture

        response.data.itemGroup.no_taxable === 1
          ? (this.formData.no_taxable = true)
          : (this.formData.no_taxable = false)
      }

      
    },

  async  getItemCategory(id) {
      let category = this.getItemCategories.filter(
        (category) => category.id == id
      )
      

      return category.length > 0 ? category[0] : null
    },

    addItem() {
      this.formData.items.push({
        ...ItemGroupStub,
        id: Guid.raw(),
      })
    },

    removeItem(index) {
      this.formData.items.splice(index, 1)
    },

    updateItem(data) {
      Object.assign(this.formData.items[data.index], { ...data.item })
    },

    checkItemsData(index, isValid) {
      this.formData.items[index].valid = isValid
    },

    checkExistItem(index, newItem) {
      let pos = this.formData.items.findIndex(
        (_item) => _item.item_id === newItem.id
      )
      if (pos !== -1) {
        this.formData.items.splice(index, 1)
      }
    },

    cancelForm() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.lose_unsaved_information'),
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          this.$router.go(-1)
        }
      })
    },

    async submitItemGroup() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      let text = ''
      if (this.isEdit) {
        text = 'items.edit_item_text'
      } else {
        text = 'items.create_item_text'
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc(text),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          // item categories
          this.formData.item_categories = this.formData.item_categories.map(
            (category) => category.id
          )

          try {
            let response
            this.isLoading = true
            if (this.isEdit) {
              response = await this.updateItemGroup(this.formData)
              if (response.data.success) {
                if (this.fileObject && this.previewPicture) {
                  let pictureData = new FormData()
                  pictureData.append(
                    'picture',
                    JSON.stringify({
                      name: this.fileObject.name,
                      data: this.previewPicture,
                      item_group_id: response.data.itemGroup.id,
                    })
                  )
                  await this.uploadPicture(pictureData)
                }
                window.toastr['success'](this.$t('item_groups.updated_message'))
                this.$router.push('/admin/item-groups')
              }
              if (response.data.error) {
                this.isLoading = false
                window.toastr['error'](response.data.error)
                return true
              }
            } else {
              response = await this.addItemGroup(this.formData)
              if (response.data.success) {
                if (this.fileObject && this.previewPicture) {
                  let pictureData = new FormData()
                  pictureData.append(
                    'picture',
                    JSON.stringify({
                      name: this.fileObject.name,
                      data: this.previewPicture,
                      item_group_id: response.data.itemGroup.id,
                    })
                  )
                  await this.uploadPicture(pictureData)
                }
                window.toastr['success'](
                  this.$tc('item_groups.created_message')
                )
                this.$router.push('/admin/item-groups')
              }
              if (response.data.error) {
                this.isLoading = false
                window.toastr['error'](response.data.error)
                return true
              }
            }
          } catch (err) {
            this.isLoading = false
          }
        }
      })
    },

    onChange(file) {
      this.cropperOutputMime = file.type
      this.fileObject = file
    },

    onUploadHandler(cropper) {
      this.previewPicture = cropper
        .getCroppedCanvas()
        .toDataURL(this.cropperOutputMime)
    },

    onHandleUploadError() {
      window.toastr['error']('Oops! Something went wrong...')
    },
  },
}
</script>

<style scoped>
</style>