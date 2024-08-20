<template>
    <tr class="box-border bg-white border border-gray-200 border-solid rounded-b">
        <td colspan="5" class="p-0 text-left align-top">
            <table class="w-full">
                <colgroup>
                    <col style="width: 50%" />
                    <col style="width: 20%" />
                    <col style="width: 30%" />
                </colgroup>
                <tbody>
                <tr>
                    <td class="px-5 py-4 text-left align-top">
                        <div class="flex justify-start">
                            <div
                                class="flex items-center justify-center w-12 h-5 mt-2 text-gray-400 cursor-move handle"
                            >
                                <drag-icon />
                            </div>
                            <item-select
                                ref="itemSelect"
                                :invalid="$v.item.name.$error"
                                :item="item"
                                @search="searchVal"
                                @select="onSelectItem"
                                @deselect="deselectItem"
                                @onSelectItem="isSelected = true"
                            />
                        </div>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <div class="items-center text-sm">
                            <span>
                                <div>{{ item.unit_name }}</div>
                            </span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-right align-top">
                        <div class="flex items-center justify-end text-sm">
                            <span>
                                <div v-html="$utils.formatMoney(item.price, currency)" />
                            </span>

                            <div class="flex items-center justify-center w-6 h-10 mx-2 cursor-pointer">
                                <trash-icon
                                    v-if="showRemoveItemIcon"
                                    class="h-5 text-gray-700"
                                    @click="removeItem"
                                />
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
</template>

<script>

import { mapActions, mapGetters } from 'vuex'
import ItemGroupStub from '../../../stub/itemGroup'
import ItemSelect from './ItemSelect'
import { TrashIcon, ViewGridIcon, ChevronDownIcon } from '@vue-hero-icons/solid'
import DragIcon from '@/components/icon/DragIcon'
const {
    required,
    minValue,
    between,
    maxLength,
} = require('vuelidate/lib/validators')

export default {
    components: {
        ItemSelect,
        TrashIcon,
        ViewGridIcon,
        ChevronDownIcon,
        DragIcon,
    },
    props: {
        itemData: {
            type: Object,
            default: null,
        },
        index: {
            type: Number,
            default: null,
        },
        type: {
            type: String,
            default: '',
        },
        currency: {
            type: [Object, String],
            required: true,
        },
        GroupItems: {
            type: Array,
            default: null,
        },
    },
    data() {
        return {
            isClosePopup: false,
            itemSelect: null,
            item: { ...this.itemData },
            maxDiscount: 0,
            isSelected: false,
        }
    },
    computed: {
        ...mapGetters('item', ['items']),
        ...mapGetters('modal', ['modalActive']),
        ...mapGetters('company', ['defaultCurrencyForInput']),
        showRemoveItemIcon() {
            if (this.GroupItems.length == 1) {
                return false
            }
            return true
        },
        price: {
            get: function () {
                if (parseFloat(this.item.price) > 0) {
                    //console.log(this.item.price / 100)
                    return this.item.price / 100
                }

                return this.item.price
            },
            set: function (newValue) {
                if (parseFloat(newValue) > 0) {
                    this.item.price = Math.round(newValue * 100)
                    this.maxDiscount = this.item.price
                } else {
                    this.item.price = newValue
                }
            },
        },
    },
    watch: {
        item: {
            handler: 'updateItem',
            deep: true,
        },
        modalActive(val) {
            if (!val) {
                this.isSelected = false
            }
        },
    },
    validations() {
        return {
            item: {
                name: {
                    required,
                },
                price: {
                    required,
                    minValue: minValue(1),
                    maxLength: maxLength(20),
                },
                description: {
                    maxLength: maxLength(65000),
                },
            },
        }
    },
    mounted() {
        this.$v.item.$reset()
    },
    created() {
        window.hub.$on('checkItems', this.validateItem)
        window.hub.$on('newItem', (val) => {
            if (!this.item.item_id && this.modalActive && this.isSelected) {
                this.onSelectItem(val)
            }
        })
    },
    methods: {
        searchVal(val) {
            this.item.name = val
        },
        deselectItem() {
            this.item = {
                ...ItemGroupStub,
                id: this.item.id
            }
            this.$nextTick(() => {
                this.$refs.itemSelect.$refs.baseSelect.$refs.search.focus()
            })
        },
        onSelectItem(item) {
            this.item.name = item.name
            this.item.price = item.price
            this.item.total = item.price
            this.item.item_id = item.id
            this.item.description = item.description
            this.item.unit_name = item.unit_name

            this.$emit('checkExists', this.index, item)

        },
        updateItem() {
            this.$emit('update', {
                index: this.index,
                item: {
                    ...this.item
                },
            })
        },
        removeItem() {
            //console.log("REMOVER ITEM", this.index)
            this.$emit('remove', this.index)
        },
        validateItem() {
            this.$v.item.$touch()

            if (this.item !== null) {
                this.$emit('itemValidate', this.index, !this.$v.$invalid)
            } else {
                this.$emit('itemValidate', this.index, false)
            }
        },
    },
}
</script>
