<template>
    <!-- Base  -->
    <base-page class="options-group-view">
        <!-- Header  -->
        <sw-page-header class="mb-3" :title="$t('item_groups.view_items_group')">
            <sw-breadcrumb slot="breadcrumbs">
                <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
                <sw-breadcrumb-item to="/admin/item-groups" :title="$t('item_groups.item_group')" />
                <sw-breadcrumb-item to="#" :title="itemGroup ? itemGroup.name : ''" active/>
            </sw-breadcrumb>
            <template slot="actions">
                <sw-button
                    tag-name="router-link"
                    :to="`/admin/item-groups/${$route.params.id}/edit`"
                    class="mr-3"
                    variant="primary-outline"
                >
                    {{ $t('general.edit') }}
                </sw-button>
                <sw-button slot="activator" variant="primary" @click="removeGroup($route.params.id)">
                    {{ $t('general.delete') }}
                </sw-button>
            </template>

        </sw-page-header>

        <sw-card>
            <div class="col-span-12">
                <p class="text-gray-500 uppercase sw-section-title">
                    {{ $t('item_groups.basic_info') }}
                </p>
                <div class="grid grid-cols-1 gap-4 mt-5">
                    <div>
                        <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                            Name
                        </p>
                        <p class="text-sm font-bold leading-5 text-black non-italic">
                            {{ itemGroup ? itemGroup.name : '' }}
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 mt-5">
                    <div>
                        <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                            {{ $t("general.description") }}
                        </p>
                        <p class="text-sm font-bold leading-5 text-black non-italic" v-html="itemGroup ? itemGroup.description : '' ">
                            {{ itemGroup ? itemGroup.description : '' }}
                        </p>
                    </div>
                </div>

                <sw-divider class="my-8" />

                <p class="text-gray-500 uppercase sw-section-title">
                    {{ $t('items.title') }}
                </p>

                <sw-empty-table-placeholder
                    v-show="showEmptyTable"
                    :title="$t('item_groups.no_items')"
                    :description="$t('item_groups.list_of_items')"
                >
                </sw-empty-table-placeholder>

                <!-- Items -->
                <div v-show="!showEmptyTable" class="mt-5">
                    <table class="w-full text-center item-table">
                        <colgroup>
                            <col style="width: 50%"/>
                            <col style="width: 20%"/>
                            <col style="width: 30%"/>
                        </colgroup>
                        <thead class="bg-white border border-gray-200 border-solid">
                            <th class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid">
                                <span class="pl-12">
                                    {{ $tc('items.item', 2) }}
                                </span>
                            </th>
                            <th class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid">
                                {{ $t('item_groups.item.unit') }}
                            </th>
                            <th class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid">
                                <span class="pr-10">
                                    {{ $t('item_groups.item.price') }}
                                </span>
                            </th>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in itemGroup.items" class="border">
                                <td class="px-5 py-4 text-left align-top border-b border-gray-200 border-solid">
                                    <div class="items-center text-sm">
                                        <span class="pl-12">
                                            {{ item.name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center border-b border-gray-200 border-solid">
                                    <div class="items-center text-sm">
                                        <span>
                                            {{ item.unit_name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right align-top border-b border-gray-200 border-solid">
                                    <div class="items-center text-sm">
                                        <span v-html="$utils.formatMoney(item.price, defaultCurrency)" class="pr-10">
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </sw-card>

    </base-page>
</template>

<script>

import {mapActions, mapGetters} from "vuex";

export default {
    data() {
        return {
            itemGroup: {
                items: []
            },
        }
    },
    computed: {
        ...mapGetters('itemGroups', ['selectedViewItemGroup']),

        ...mapGetters('company', ['defaultCurrency']),

        showEmptyTable() {
            return !this.itemGroup.items.length
        },
    },
    created() {
        this.loadItemGroup();
    },
    methods: {
        ...mapActions('itemGroups', [
            'fetchViewItemGroup',
            'deleteItemGroup'
            ]),

        async loadItemGroup() {
            let response = await this.fetchViewItemGroup({ id: this.$route.params.id });
            
            if (response.data.success) {
                this.itemGroup = response.data.itemGroup;
            }
        },

        async removeGroup(id) {
            this.id = id
            swal({
                title: this.$t('general.are_you_sure'),
                text: this.$tc('items.confirm_delete'),
                icon: '/assets/icon/trash-solid.svg',
                buttons: true,
                dangerMode: true,
            }).then(async (willDelete) => {
                if (willDelete) {
                    let res = await this.deleteItemGroup({ids: [id]})

                    if (res.data.success) {
                        window.toastr['success'](this.$tc('item_groups.deleted_message', 1))
                        this.$router.push('/admin/item-groups')
                    }
                    return true
                }
            })
        },
    }
}
</script>