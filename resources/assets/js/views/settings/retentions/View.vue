<template>
  <sw-card variant="setting-card">
  <base-page>
    <sw-page-header class="mb-3" :title="$tc('tax_groups.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item to="/admin/settings/tax-groups" :title="$tc('tax_groups.tax_group', 2)" />
      </sw-breadcrumb>      
      <template slot="actions">
        <sw-button
          tag-name="router-link"
          :to="`/admin/settings/tax-groups/${$route.params.id}/edit`"
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('general.edit') }}
        </sw-button>
          <sw-button slot="activator" variant="primary" @click="removeTaxGroup($route.params.id)">
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
            {{ $t('tax_groups.basic_info') }}
          </p>
          <div
            class="grid grid-cols-1 gap-4 mt-5 "
          >
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('tax_groups.name') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  formData.name
                }}
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('tax_groups.description') }}
              </p>
              <p class="text-sm font-bold font-bold leading-5 text-black non-italic">
                {{
                  formData.description
                }}
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('tax_groups.country') }}
              </p>
              <p v-if="formData.country_name" class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  formData.country_name
                }}
              </p>
              <p v-else class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  $t('tax_groups.not_selected')
                }}
              </p>
            </div>
            <div>
              <p
                class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
              >
                {{ $t('tax_groups.state') }}
              </p>
              <p v-if="formData.state_name" class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  formData.state_name
                }}
              </p>
              <p v-else class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  $t('tax_groups.not_selected')
                }}
              </p>
            </div>

            <div v-show="showTaxes">
              <p class="text-gray-500 uppercase sw-section-title">
                {{ $t('tax_groups.tax_type') }}
              </p>

              <sw-table-component
                ref="table"
                :show-filter="false"
                :data="taxes"
                table-class="table"
              >

                <sw-table-column
                  :sortable="true"
                  :filterable="true"
                  :label="$t('tax_groups.name')"
                  show="name"
                >
                  <template slot-scope="row">
                    <span>{{ $t('tax_groups.name') }}</span>
                      {{ row.name }}
                  </template>
                </sw-table-column>

              </sw-table-component>
            </div>
          </div>
        </div>
      </div>
    </sw-card>
  </base-page>
  </sw-card>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
  data() {
    return {
      isTaxes: false,
      tax_group: null,

      formData: {
        name: '',
        description: '',
        country_name: '',
        state_name: '',
      },
      taxes: []
    }
  },
  computed: {
    ...mapGetters('taxGroups', ['selectedViewTaxGroup']),
    showTaxes() {
      return this.isTaxes
    },
  },
  
  created() {
    this.loadData()
  },

  watch: {
    $route(to, from) {
      this.taxGroup = this.selectedViewTaxGroup  
    },    
  },
  methods: {
    ...mapActions('taxGroups', [
      'fetchTaxGroup',
      'deleteTaxGroup',
    ]),

    async loadData() {      
      let response = await this.fetchTaxGroup(this.$route.params.id) 
      
      if (response.data) {
        this.formData = { ...this.formData, ...response.data.tax_groups }    

        if (response.data.taxes.length > 0) {
          this.taxes = response.data.taxes  
          this.isTaxes = true
        }
      }
      
    },

    async removeTaxGroup(id) { 
      this.id = id      
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteTaxGroup({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('tax_groups.deleted_message', 1))
            this.$router.push('/admin/settings/tax-groups')
          }
          return true
        }
      })
    },
  },
}
</script>
