<template>
  <form action="" @submit.prevent="submitData">
    <sw-page-header class="mb-3" :title="title"></sw-page-header>
    <div class="grid grid-cols-12">
      <div class="col-span-12">
        <sw-card class="mb-8">
          <sw-input-group :label="$t('core_pos.name')" class="mb-4" :error="nameError" required>
            <sw-input v-model="formData.name" type="text" @input="$v.formData.name.$touch()" />
          </sw-input-group>
          <sw-input-group :label="$t('core_pos.description')" class="mb-4" :error="descriptionError" required>
            <sw-input v-model="formData.description" type="text" @input="$v.formData.description.$touch()" />
          </sw-input-group>
            <!--------- Buttons Cancel and Submit ---------->
          <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
            <sw-button class="mr-3" variant="primary-outline" type="button" @click="cancel()">
              {{ $t('general.cancel') }}
            </sw-button>
            <sw-button variant="primary" type="submit">
              <save-icon class="mr-2" />
              {{ $t('general.save') }}
            </sw-button>
          </div>
            <!--------- /Buttons Cancel and Submit  ---------->
        </sw-card>
      </div>
    </div>
    <!-- TABS  -->
    <div>
      <div class="tab-content">
        <div class="text-grey-darkest">
          <ul class="pl-0">
            <li class="pb-2">
              <sw-tabs :active-tab="activeTab">
                <sw-tab-item title="ITEMS">
                  <!--  -->
                  <!-- Items -->
                  <table class="w-full text-center item-table">
                    <colgroup>
                      <col style="width: 50%" />
                      <col style="width: 20%" />
                      <col style="width: 30%" />
                    </colgroup>
                    <thead class="bg-white border border-gray-200 border-solid">
                      <th
                        class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid">
                        <span class="pl-12">
                          {{ $tc('items.item', 2) }}
                        </span>
                      </th>
                      <th
                        class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid">
                        {{ $t('item_groups.item.unit') }}
                      </th>
                      <th
                        class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid">
                        <span class="pr-10">
                          {{ $t('item_groups.item.price') }}
                        </span>
                      </th>
                    </thead>

                    <draggable v-model="formData.items" class="item-body" tag="tbody" handle=".handle">
                      <items-group-item v-for="(items, index) in formData.items" :key="items.id" :index="index"
                        :item-data="items" :group-items="formData.items" :currency="currency" @remove="removeItem"
                        @update="updateItem" @itemValidate="checkItemsData" @checkExists="checkExistItem" />
                    </draggable>
                  </table>
                  <div
                    class="flex items-center justify-center w-full px-6 py-3 text-base border-b border-gray-200 border-solid cursor-pointer text-primary-400 hover:bg-gray-200"
                    @click="addItem">
                    <shopping-cart-icon class="h-5 mr-2" />
                    {{ $t('item_groups.add_item') }}
                  </div>
                  <!--  -->
                </sw-tab-item>
                <sw-tab-item title="ITEMS GROUP">
                  <!--  -->

                  <!-- Items -->
                  <div class="grid grid-cols-5 gap-4 mb-8">
                    <sw-divider class="col-span-12" />

                    <h6 class="col-span-5 sw-section-title lg:col-span-1">
                      {{ $t('packages.packages_items') }}
                    </h6>
                    <div class="grid col-span-12 gap-y-6 gap-x-4 md:grid-cols-6">
                      <sw-input-group :label="$t('packages.item_groups')" class="md:col-span-3">
                        <sw-select v-model="item_group" :options="item_groups" :searchable="true" :show-labels="false"
                          :allow-empty="true" :placeholder="$tc('packages.item_groups_select')" class="mt-2" label="name"
                          track-by="id" @select="itemGroupSelected" :tabindex="11" />
                      </sw-input-group>


                      <div class="col-span-12" v-if="undefined !== formData.item_groups &&
                        formData.item_groups.length > 0
                        ">
                        <div class="flex flex-wrap justify-start">
                          <div class="relative table-container">
                            <div
                              class="relative flex items-center justify-between h-10 mt-5 list-none border-b-2 border-gray-200 border-solid">
                              <p class="text-sm"></p>
                            </div>
                            <sw-table-component ref="table" :data="formData.item_groups" :show-filter="false"
                              table-class="table">

                              <sw-table-column :sortable="true" :label="$t('core_pos.name')" show="name"
                                style="width: 50%">
                                <template slot-scope="row">
                                  <span>{{ $t('core_pos.name') }}</span>
                                  {{ row.name }}
                                </template>
                              </sw-table-column>
                              <sw-table-column :sortable="true" :label="$t('core_pos.action')" style="width: 50%">
                                <template slot-scope="row">
                                  <span>{{ $t('core_pos.action') }}</span>
                                  <svg @click="removeItemGroup(row)" xmlns="http://www.w3.org/2000/svg" width="100%"
                                    height="100%" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-x cursor-pointer hover:text-indigo-400 rounded-full w-6 h-4 ml-2 pr-1">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                  </svg>
                                </template>
                              </sw-table-column>



                            </sw-table-component>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </sw-tab-item>
              </sw-tabs>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </form>
</template>
<script>
import draggable from 'vuedraggable'
import ItemsGroupItem from './Item'
import PackageItem from './PackageItem'
import ItemGroupStub from '../../../stub/itemGroup'
import TaxStub from '../../../stub/tax'
import { mapActions, mapGetters } from 'vuex'
import Guid from 'guid'
import {
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
  CloudUploadIcon,
} from '@vue-hero-icons/solid'

const {
  required,
  minLength,
  numeric,
  maxLength,
  minValue,
  email,
} = require('vuelidate/lib/validators')

export default {
  components: {
    ItemsGroupItem,
    draggable,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
    CloudUploadIcon,
  },
  data() {
    return {
      discountPerItem: null,
      taxPerItem: 'YES',
      isNoGeneralTaxes: false,
      activeTab: 'ITEMS',
      isEdit: false,
      isLoading: false,
      isRequestOnGoing: false,
      title: '',
      selectedCurrency: '',
      formData: {
        name: '',
        description: '',
        items: [
          {
            ...ItemGroupStub,
            id: Guid.raw(),
          },
        ],
        item_groups: [],
      },
      item_groups: [],
      itemGroupsFetch: [],
    }
  },

  created() {
    this.fetchItems({
      filter: {},
      orderByField: '',
      orderBy: '',
      limit: 'all'

    })

    if (this.$route.name == 'store.edit') {
      this.isEdit = true
      this.loadData()
    }


  },

  mounted() {
    this.fetchInitialItemGroups()
  },

  computed: {
    ...mapGetters('company', ['defaultCurrency']),

    currency() {
      return this.selectedCurrency
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },
    descriptionError() {
      if (!this.$v.formData.description.$error) {
        return ''
      }

      if (!this.$v.formData.description.required) {
        return this.$tc('validation.required')
      }
    },
  },
  validations: {
    formData: {
      name: {
        required,
      },
      description: {
        required,
      },
    },
  },

  methods: {
    ...mapActions('corePos', ['addStore', 'fetchStore', 'updateStore']),
    ...mapActions('item', ['fetchItems']),
    ...mapActions('pbx', ['fetchItemGroupsPos']),

    async loadData() {

      const response = await this.fetchStore(this.$route.params)
      const store = response.data.store
      this.formData.description = store.description
      this.formData.name = store.name
      this.formData.items = store.items
      this.formData.item_groups = store.items_groups

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
    ////////////// Confirmación agregar tienda //////////////
    async submitData() {
      let response = null
      let message = ''
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('core_pos.save_store_message'),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons:  [this.$t('core_pos.cancel'), true],
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          if (this.isEdit) {
            this.isRequestOnGoing = true
            let data = {
              id: this.$route.params.id,
              data: this.formData
            }
            response = await this.updateStore(data)
            message = this.$tc('core_pos.update_store_message')

          } else {
            this.isRequestOnGoing = true
            response = await this.addStore(this.formData)
            message = this.$tc('core_pos.add_store_message')
          }
          if (response.data.success) {
            window.toastr['success'](message)
            this.isRequestOnGoing = false
            this.$router.push(`/admin/corePOS/stores`)
          }

          this.isRequestOnGoing = false
          if (response.data.error) {
            window.toastr['error'](response.data.error)
          }
        }
      })
    },
    ////////////// /Confirmación agregar tienda //////////////
    addItem() {
      this.formData.items.push({
        ...ItemGroupStub,
        id: Guid.raw(),
      })
    },

    cancel() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('core_pos.back_cash_register'),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons:  [this.$t('core_pos.cancel'), true],
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          this.$router.push(`/admin/corePOS/stores`)
        }
      })
    },

    itemGroupSelected(val) {
      let vm = this
      const isId = (element) => element.id == val.id

      const index = vm.formData.item_groups.findIndex(isId)
      if (index == -1) {
        vm.formData.item_groups.push(val)
      } else {
        window.toastr['error']('This item group was already selected')
        return false
      }

      vm.item_groups = vm.filterByReference(
        vm.itemGroupsFetch,
        vm.formData.item_groups
      )
      vm.formData.item_groups.forEach((item_group) => {
        item_group.items.forEach((item) => {
          item.item_id = item.id
          item.id = Guid.raw()
            ; (item.discount_type = 'fixed'),
              (item.quantity = 1),
              (item.discount_val = 0),
              (item.discount = 0),
              (item.total = item.price),
              (item.totalTax = 0),
              (item.totalSimpleTax = 0),
              (item.totalCompoundTax = 0),
              (item.tax = 0),
              (item.item_group_id = item_group.id)
          item.taxes = [{ ...TaxStub, id: Guid.raw() }]
          vm.formData.items.push(item)
        })
        vm.formData.items = this.filterDuplicate(vm.formData.items)
      })

      setTimeout(() => {
        this.item_group = null
      }, 100)
    },

    filterDuplicate(arrayWithDuplicates) {
      const uniqByProp_map = (prop) => (arr) =>
        Array.from(
          arr
            .reduce(
              (acc, item) => (
                item && item[prop] && acc.set(item[prop], item), acc
              ), // using map (preserves ordering)
              new Map()
            )
            .values()
        )

      // usage (still the same):

      const uniqueById = uniqByProp_map('id')

      const unifiedArray = uniqueById(arrayWithDuplicates)
      return unifiedArray
    },

    async fetchInitialItemGroups() {
      let data = {
        orderByField: 'created_at',
        orderBy: 'asc',
      }

      let res = await this.fetchItemGroupsPos(data)

      this.item_groups = res.data.response
      this.itemGroupsFetch = res.data.response
    },

    removeItemGroup(item) {
      let myArray = this.formData.item_groups

      for (var i = myArray.length - 1; i >= 0; --i) {
        if (myArray[i].id == item.id) {
          myArray.splice(i, 1)
        }
      }

      this.formData.item_groups = myArray

      const filterByReference = (arr1, arr2) => {
        let res = []
        res = arr1.filter((el) => {
          return !arr2.find((element) => {
            return element.id === el.id
          })
        })
        return res
      }

      this.item_groups = filterByReference(
        this.itemGroupsFetch,
        this.formData.item_groups
      )
      this.item_group = null

      for (var i = this.formData.items.length - 1; i >= 0; --i) {
        if (this.formData.items[i].item_group_id == item.id) {
          this.formData.items.splice(i, 1)
        }
      }
      // console.log('Format Taxes', this.formData.taxes)
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
  },
}
</script>
