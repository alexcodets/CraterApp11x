<template>
  <div class="relative">
    <!-- Page Header -->
    <sw-page-header :title="$t('corePbx.custom_did_groups.view_custom_did_groups')" class="mb-3">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('corePbx.corePbx')" to="/admin/corePBX"/>
        <sw-breadcrumb-item :title="$t('corePbx.custom_did_groups.title')" to="/admin/corePBX/billing-templates/custom-did-groups"/>
        <sw-breadcrumb-item to="#" :title="customDidGroup ? customDidGroup.name : ''" active/>
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          tag-name="router-link"
          :to="`/admin/corePBX/billing-templates/custom-did-groups`"
          class="mr-2"
          variant="primary-outline"
        >
          <arrow-left-icon class="h-4 mr-2 -ml-1" />
          {{ $t('general.back') }}
        </sw-button>
        <sw-button
          tag-name="router-link"
          :to="`/admin/corePBX/billing-templates/custom-did-groups/${$route.params.id}/edit`"
          class="mr-2"
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
          {{ $t('corePbx.custom_did_groups.basic_info') }}
        </p>

        <div class="grid grid-cols-1 gap-4 mt-5 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1">
          <div>
            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
              Name
            </p>
            <p class="text-sm font-bold leading-5 text-black non-italic">
              {{ customDidGroup ? customDidGroup.name : '' }}
            </p>
          </div>
          <div>
            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
              Status
            </p>
            <p class="text-sm font-bold leading-5 text-black non-italic">
              {{ customDidGroup ? customDidGroup.status : '' }}
            </p>
          </div>
          <div>
            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
              Type
            </p>
            <p class="text-sm font-bold leading-5 text-black non-italic">
              {{ customDidGroup ? customDidGroup.type : '' }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 mt-5">
          <div>
            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
              {{ $t("general.description") }}
            </p>
            <p class="text-sm font-bold leading-5 text-black non-italic" v-html="customDidGroup ? customDidGroup.description : '' ">
              {{ customDidGroup ? customDidGroup.description : '' }}
            </p>
          </div>
        </div>

        <sw-divider class="my-8" />

        <p class="text-gray-500 uppercase sw-section-title">
          {{ $t('corePbx.custom_did_groups.custom_dids') }}
        </p>

        <sw-empty-table-placeholder
          v-show="showEmptyTable"
          :title="$t('corePbx.custom_did_groups.no_custom_dids')"
          :description="$t('corePbx.custom_did_groups.list_of_custom_dids')"
        >
        </sw-empty-table-placeholder>

        <!-- Custom DIDs -->
        <div v-show="!showEmptyTable" class="mt-5">
          <div v-for="(category, key) in getSortedObject" :key="key">
            <label
              class="text-sm not-italic font-medium leading-5 text-primary-800 text-sm"
            >
              {{ key }}
            </label>
            <table class="w-full text-center item-table mb-5 mt-2">
              <colgroup>
                <col style=""/>
                <col style=""/>
                <col style=""/>
              </colgroup>
              <thead class="bg-white border border-gray-200 border-solid">
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid">
                <span class="">
                  {{ $t('corePbx.custom_did_groups.prefix') }}
                </span>
              </th>
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid">
                {{ $t('corePbx.custom_did_groups.category') }}
              </th>
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid">
                <span>
                 Price
                </span>
              </th>
              </thead>
              <tbody>
              <tr v-for="(did, index) in category" class="border">
                <td class="px-5 py-4 text-left align-top border-b border-gray-200 border-solid">
                  <div class="items-center text-sm">
                  <span class="">
                      {{ did.prefijo }}
                  </span>
                  </div>
                </td>
                <td class="px-5 py-4 text-center border-b border-gray-200 border-solid">
                  <div class="items-center text-sm">
                  <span>
                    {{ did.category_name }}
                  </span>
                  </div>
                </td>
                <td class="px-5 py-4 text-right align-top border-b border-gray-200 border-solid">
                  <div class="items-center text-sm">
                  <span>
                    {{ did.rate_per_minute }}
                  </span>
                  </div>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </sw-card>
  </div>
</template>

<script>

import { mapActions, mapGetters } from "vuex";
import { ArrowLeftIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    ArrowLeftIcon
  },
  data() {
    return {
      customDidGroup: {
        custom_dids: [],
        status: null
      },
    }
  },
  computed: {
    ...mapGetters('customDidGroup', ['selectedViewCustomDidGroup']),

    showEmptyTable() {
      return !this.customDidGroup.custom_dids.length
    },

    getGroupedDIDs() {
      let groupedDIDs = {}

      this.customDidGroup.custom_dids.forEach((did) => {
        if (!groupedDIDs.hasOwnProperty(did.category_name)) {
          groupedDIDs[did.category_name] = []
        }
        groupedDIDs[did.category_name].push({ ...did })
      })

      return groupedDIDs
    },

    getSortedObject() {
      let object = this.getGroupedDIDs
      // New object which will be returned with sorted keys
      var sortedObject = {};

      // Get array of keys from the old/current object
      var keys = Object.keys(object);
      // Sort keys (in place)
      keys.sort();

      // Use sorted keys to copy values from old object to the new one
      for (var i = 0, size = keys.length; i < size; i++) {
        let key = keys[i];
        let value = object[key];
        sortedObject[key] = value;
      }

      // Return the new object
      return sortedObject;
    }

  },
  created() {
    this.loadCustomDidGroup()
  },
  methods: {
    ...mapActions('customDidGroup', [
      'fetchViewCustomDidGroup',
      'deleteCustomDidGroup'
    ]),

    async loadCustomDidGroup() {
      let response = await this.fetchViewCustomDidGroup({ id: this.$route.params.id });
      if (response.data.success) {
        this.customDidGroup = { ...response.data.customDidGroup };
        this.customDidGroup.status = response.data.customDidGroup.status === 'A' ? 'Active' : 'Inactive'
        this.customDidGroup.type = response.data.customDidGroup.type === 'TF' ? 'Toll free' : 'International'
      }
    },

    async removeGroup(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('corePbx.custom_did_groups.confirm_delete', 1),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteCustomDidGroup({ids: [id]})

          if (res.data.success) {
            window.toastr['success'](this.$tc('corePbx.custom_did_groups.deleted_message', 1))
            this.$router.push('/admin/corePBX/billing-templates/custom-did-groups')
            return true;
          }

          window.toastr['error'](res.data.error)
          return true
        }
      })
    },

  }

}

</script>

<style scoped>

</style>