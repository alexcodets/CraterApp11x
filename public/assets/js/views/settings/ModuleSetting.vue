<template>
  <sw-card variant="setting-card">
    <div slot="header" class="flex flex-wrap justify-between lg:flex-nowrap">
      <div>
        <h6 class="sw-section-title">
          {{ $t('settings.customization.modules.title') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.customization.modules.description') }}
        </p>
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            to="/admin/dashboard"
            :title="$t('general.home')"
          />
          <sw-breadcrumb-item
            to="#"
            :title="$t('settings.customization.modules.title')"
            active
          />
        </sw-breadcrumb>
      </div>
    </div>

    <sw-table-component
      ref="table"
      :show-filter="false"
      :data="fetchData"
      table-class="table"
      variant="gray"
    >
      <sw-table-column
        :sortable="true"
        :label="$t('settings.customization.modules.module')"
        show="name"
      >
        <template slot-scope="row">
          <span class="mt-6">{{
            $t('settings.customization.modules.module')
          }}</span>
          <span class=""><img :src="row.image" width="150px" /></span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :filterable="true"
        :label="$t('settings.customization.modules.module_name')"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.customization.modules.module_name') }}</span>
          <span class="">{{ row.name }}</span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :filterable="true"
        :label="$t('settings.customization.modules.module_description')"
      >
        <template slot-scope="row">
          <span>{{
            $t('settings.customization.modules.module_description')
          }}</span>
          <span class="mt-2 text-sm leading-snug text-gray-500">{{
            row.description
          }}</span
          ><br />
          <span class="mt-2 text-sm leading-snug text-gray-500">{{
            row.version
          }}</span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="false"
        :filterable="false"
        cell-class="action-dropdown"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.modules.action') }}</span>
          <sw-dropdown>
            <dot-icon slot="activator" />

            <sw-dropdown-item @click="RedirectToModule(row.slug)">
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.manage') }}
            </sw-dropdown-item>

         
          </sw-dropdown>
        </template>
      </sw-table-column>
    </sw-table-component>
  </sw-card>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, maxLength, alpha } = require('vuelidate/lib/validators')
import { TrashIcon, PencilIcon, PlusIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    TrashIcon,
    PencilIcon,
    PlusIcon,
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('modules', ['fetchModules']),

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchModules(data)

      return {
        data: response.data.modules,
        pagination: {
          totalPages: response.data.modules.last_page,
          currentPage: page,
          count: response.data.modules.count,
        },
      }
    },
    async RedirectToModule(slug) {
      // this.$router.push('/admin/module/' + slug)
      var getUrl = window.location;
      var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + slug
      window.location = baseUrl
      //console.log(slug)
    },

    async UninstallModule(id, index) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.modules.module_confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.deleteTaxType(id)
          if (response.data.success) {
            window.toastr['success'](
              this.$t('settings.modules.deleted_message')
            )
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](this.$t('settings.modules.already_in_use'))
        }
      })
    },
  },
}
</script>
