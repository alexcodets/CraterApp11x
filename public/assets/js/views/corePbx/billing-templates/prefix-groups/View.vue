<template>
  <div class="relative">
    <!-- Page Header -->
    <sw-page-header
      :title="$t('corePbx.prefix_groups.view_prefixes_group')"
      class="mb-3"
    >
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          :title="$t('corePbx.corePbx')"
          to="/admin/corePBX"
        />
        <sw-breadcrumb-item
          :title="$tc('corePbx.prefix_groups.prefix_group', 2)"
          to="/admin/corePBX/billing-templates/prefix-groups"
        />
        <sw-breadcrumb-item
          to="#"
          :title="prefixGroup ? prefixGroup.name : ''"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          tag-name="router-link"
          :to="`/admin/corePBX/billing-templates/prefix-groups`"
          class="mr-2"
          variant="primary-outline"
        >
          <arrow-left-icon class="h-4 mr-2 -ml-1" />
          {{ $t('general.back') }}
        </sw-button>
        <sw-button
          tag-name="router-link"
          :to="`/admin/corePBX/billing-templates/prefix-groups/${$route.params.id}/edit`"
          class="mr-2"
          variant="primary-outline"
        >
          {{ $t('general.edit') }}
        </sw-button>
        <sw-button
          slot="activator"
          variant="primary"
          @click="removeGroup($route.params.id)"
        >
          {{ $t('general.delete') }}
        </sw-button>
      </template>
    </sw-page-header>

    <sw-card>
      <div class="col-span-12">
        <p class="text-gray-500 uppercase sw-section-title">
          {{ $t('corePbx.prefix_groups.basic_info') }}
        </p>

        <div
          class="
            grid grid-cols-1
            gap-4
            mt-5
            lg:grid-cols-3
            md:grid-cols-2
            sm:grid-cols-1
          "
        >
          <div>
            <p
              class="
                mb-1
                text-sm
                font-normal
                leading-5
                non-italic
                text-primary-800
              "
            >
              Name
            </p>
            <p class="text-sm font-bold leading-5 text-black non-italic">
              {{ prefixGroup ? prefixGroup.name : '' }}
            </p>
          </div>
          <div>
            <p
              class="
                mb-1
                text-sm
                font-normal
                leading-5
                non-italic
                text-primary-800
              "
            >
              Status
            </p>
            <p class="text-sm font-bold leading-5 text-black non-italic">
              {{ prefixGroup ? prefixGroup.status : '' }}
            </p>
          </div>
          <div>
            <p
              class="
                mb-1
                text-sm
                font-normal
                leading-5
                non-italic
                text-primary-800
              "
            >
              Type
            </p>
            <p class="text-sm font-bold leading-5 text-black non-italic">
              {{ prefixGroup ? prefixGroup.type : '' }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 mt-5">
          <div>
            <p
              class="
                mb-1
                text-sm
                font-normal
                leading-5
                non-italic
                text-primary-800
              "
            >
              {{ $t("general.description") }}
            </p>
            <p
              class="text-sm font-bold leading-5 text-black non-italic"
              v-html="prefixGroup ? prefixGroup.description : ''"
            >
              {{ prefixGroup ? prefixGroup.description : '' }}
            </p>
          </div>
        </div>

        <sw-divider class="my-8" />

        <p class="text-gray-500 uppercase sw-section-title">
          {{ $t('corePbx.prefix_groups.prefixes') }}
        </p>

        <sw-empty-table-placeholder
          v-show="showEmptyTable"
          :title="$t('corePbx.prefix_groups.no_prefixes')"
          :description="$t('corePbx.prefix_groups.list_of_prefixes')"
        >
        </sw-empty-table-placeholder>

        <!-- Prefixes -->
        <div v-show="!showEmptyTable" class="mt-5">
          <div v-for="(category, key) in getGroupedPrefixes" :key="key">
            <label
              class="
                text-sm
                not-italic
                font-medium
                leading-5
                text-primary-800 text-sm
              "
            >
              {{ categoryName(key) }}
            </label>
            <table class="w-full text-center item-table mb-5 mt-2">
              <colgroup>
                <col style="" />
                <col style="" />
                <col style="" />
                <col style="" />
              </colgroup>
              <thead class="bg-white border border-gray-200 border-solid">
                <th
                  class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-left text-gray-700
                    border-t border-b border-gray-200 border-solid
                  "
                >
                  <span class="">
                    {{ $t('corePbx.prefix_groups.prefix') }}
                  </span>
                </th>
                <th
                  class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-center text-gray-700
                    border-t border-b border-gray-200 border-solid
                  "
                >
                  {{ $t('corePbx.prefix_groups.prefix_name') }}
                </th>
                <th
                  class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-center text-gray-700
                    border-t border-b border-gray-200 border-solid
                  "
                >
                  {{ $t('corePbx.prefix_groups.country') }}
                </th>
                <th
                  class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-right text-gray-700
                    border-t border-b border-gray-200 border-solid
                  "
                >
                  <span>
                    {{ $t('corePbx.prefix_groups.rate_per_minutes') }}
                  </span>
                </th>
              </thead>
              <tbody>
                <tr
                  v-for="(prefix, index) in category"
                  :key="index"
                  class="border"
                >
                  <td
                    class="
                      px-5
                      py-4
                      text-left
                      align-top
                      border-b border-gray-200 border-solid
                    "
                  >
                    <div class="items-center text-sm">
                      <span class="">
                        {{ prefix.prefix }}
                      </span>
                    </div>
                  </td>
                  <td
                    class="
                      px-5
                      py-4
                      text-center
                      border-b border-gray-200 border-solid
                    "
                  >
                    <div class="items-center text-sm">
                      <span>
                        {{ prefix.name }}
                      </span>
                    </div>
                  </td>
                  <td
                    class="
                      px-5
                      py-4
                      text-center
                      border-b border-gray-200 border-solid
                    "
                  >
                    <div class="items-center text-sm">
                      <span>
                        {{ prefix.countryName }}
                      </span>
                    </div>
                  </td>
                  <td
                    class="
                      px-5
                      py-4
                      text-right
                      align-top
                      border-b border-gray-200 border-solid
                    "
                  >
                    <div class="items-center text-sm">
                      <span>
                        {{ prefix.rate_per_minute }}
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
import { mapActions, mapGetters } from 'vuex'
import { ArrowLeftIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    ArrowLeftIcon,
  },
  data() {
    return {
      prefixGroup: {
        prefixes: [],
        status: null,
        prefix_group: [],
      },
      categories: [
        { name: 'Custom', value: 'C' },
        { name: 'International', value: 'I' },
        { name: 'Toll Free', value: 'T' },
      ],
    }
  },
  computed: {
    ...mapGetters('prefixGroup', ['selectedViewPrefixGroup']),

    showEmptyTable() {
      return !this.prefixGroup.prefix_group.length
    },

    getGroupedPrefixes() {
      let groupedPrefixes = {}

      this.prefixGroup.prefix_group.forEach((prefix) => {
        // Si la categoria no existe en groupedPrefixes se inicializa
        if (!groupedPrefixes.hasOwnProperty(prefix.category)) {
          groupedPrefixes[prefix.category] = []
        }
        // Agregamos los datos de prefixes.
        groupedPrefixes[prefix.category].push({ ...prefix })
      })

      return groupedPrefixes
    },
  },
  created() {
    this.loadPrefixGroup()
  },
  methods: {
    ...mapActions('prefixGroup', ['fetchViewPrefixGroup', 'deletePrefixGroup']),

    async loadPrefixGroup() {
      let response = await this.fetchViewPrefixGroup({
        id: this.$route.params.id,
      })
      if (response.data.success) {
        this.prefixGroup = { ...response.data.prefixGroup }
        this.prefixGroup.status =
          response.data.prefixGroup.status === 'A' ? 'Active' : 'Inactive'
      }
    },

    categoryName(key) {
      let category = {}
      category = this.categories.find((category) => key === category.value)
      return category ? category.name : ''
    },

    async removeGroup(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('corePbx.prefix_groups.confirm_delete', 1),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deletePrefixGroup({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](
              this.$tc('corePbx.prefix_groups.deleted_message', 1)
            )
            this.$router.push('/admin/corePBX/billing-templates/prefix-groups')
            return true
          }

          window.toastr['error'](res.data.error)
          return true
        }
      })
    },
  },
}
</script>

<style>
</style>