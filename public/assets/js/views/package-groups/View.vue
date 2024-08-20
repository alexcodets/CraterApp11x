<template>
  <base-page>
    <sw-page-header class="mb-3" :title="$tc('groups.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item to="/admin/groups" :title="$tc('groups.group', 2)" />
      </sw-breadcrumb>      
      <template slot="actions">
        <sw-button
          tag-name="router-link"
          :to="`/admin/groups/${$route.params.id}/edit`"
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
    <sw-card class="flex flex-col mt-3">
      <div
        class="pt-6 mt-5 "
      >
        <div class="col-span-12">
          <p class="text-gray-500 uppercase sw-section-title">
            {{ $t('groups.basic_info') }}
          </p>
          <div
            class="grid grid-cols-1 gap-4 mt-5 "
          >
            <div>
              <p
                class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('groups.name') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{
                  formData.name
                }}
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('groups.description') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic" v-html="formData.description">
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('groups.allow_upgrades_downgrades') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.allow_upgrades ? $t('groups.allow_upgrades_yes') : $t('groups.allow_upgrades_no') }}
              </p>
            </div>

            <div v-show="showPackages">
              <p class="text-gray-500 uppercase sw-section-title">
                {{ $t('packages.title') }}
              </p>

              <sw-table-component
                ref="table"
                :show-filter="false"
                :data="packages"
                table-class="table"
              >

                <sw-table-column
                  :sortable="true"
                  :filterable="true"
                  :label="$t('groups.name')"
                  show="name"
                >
                  <template slot-scope="row">
                    <span>{{ $t('groups.name') }}</span>
                    <router-link
                      :to="`/admin/packages/${row.id}/view`"
                      class="font-medium text-primary-500"
                    >
                      {{ row.name }}
                    </router-link>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown"
                  :label="$t('groups.action')"
                  show="action"
                >
                  <template slot-scope="row">
                    <span> {{ $t('groups.action') }} </span>

                    <sw-dropdown>
                      <dot-icon slot="activator" />

                      <sw-dropdown-item
                        :to="`/admin/packages/${row.id}/view`"
                        tag-name="router-link"
                      >
                        <eye-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.view') }}
                      </sw-dropdown-item>

                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>
    </sw-card>
  </base-page>
</template>

<script>
import { EyeIcon } from '@vue-hero-icons/outline'
import { mapActions, mapGetters } from 'vuex'
export default {
  components: {
    EyeIcon
  },
  data() {
    return {
      group: null,
      isPackages: false,

      formData: {
        name: '',
        description: '',
        allow_upgrades: false,
      },
      packages: []
    }
  },
  computed: {
    ...mapGetters('group', ['selectedViewGroup']),
    showPackages() {
      return this.isPackages
    },
  },
  
  created() {
    this.loadData()
  },

  watch: {
    $route(to, from) {
      this.group = this.selectedViewGroup  
    },    
  },
  methods: {
    ...mapActions('group', [
      'fetchGroup',
      'deleteGroup',
    ]),

    async loadData() {      
      let response = await this.fetchGroup(this.$route.params.id)

      console.log('response loaddata view', response);

      if (response.data) {
        this.formData = { ...this.formData, ...response.data.groups }

        if (response.data.packages.length > 0) {
          this.packages = response.data.packages
          this.isPackages = true
        }
            
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
          let res = await this.deleteGroup({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('groups.deleted_message', 1))
            this.$router.push('/admin/groups')
          }
          return true
        }
      })
    },
  },
}
</script>
