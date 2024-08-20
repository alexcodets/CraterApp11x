<template>
  <div class="relative">
    <!-- Page Header -->
    <sw-page-header :title="$t('corePbx.prefix_groups.view_prefixes_group')" class="mb-3">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('corePbx.corePbx')" to="/admin/corePBX" />
        <sw-breadcrumb-item :title="$tc('corePbx.prefix_groups.prefix_group', 2)"
          to="/admin/corePBX/billing-templates/prefix-groups" />
        <sw-breadcrumb-item to="#" :title="prefixGroup ? prefixGroup.name : ''" active />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button tag-name="router-link" :to="`/admin/corePBX/billing-templates/prefix-groups`" class="mr-2"
          variant="primary-outline">
          <arrow-left-icon class="h-4 mr-2 -ml-1" />
          {{ $t('general.back') }}
        </sw-button>
        <sw-button tag-name="router-link"
          :to="`/admin/corePBX/billing-templates/prefix-groups/${$route.params.id}/edit`" class="mr-2"
          variant="primary-outline">
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
          {{ $t('corePbx.prefix_groups.basic_info') }}
        </p>

        <div class="
            grid grid-cols-1
            gap-4
            mt-5
            lg:grid-cols-3
            md:grid-cols-2
            sm:grid-cols-1
          ">
          <div>
            <p class="
                mb-1
                text-sm
                font-normal
                leading-5
                non-italic
                text-primary-800
              ">
              Name
            </p>
            <p class="text-sm font-bold leading-5 text-black non-italic">
              {{ prefixGroup ? prefixGroup.name : '' }}
            </p>
          </div>
          <div>
            <p class="
                mb-1
                text-sm
                font-normal
                leading-5
                non-italic
                text-primary-800
              ">
              Status
            </p>
            <p class="text-sm font-bold leading-5 text-black non-italic">
              {{ prefixGroup ? prefixGroup.status : '' }}
            </p>
          </div>
          <div>
            <p class="
                mb-1
                text-sm
                font-normal
                leading-5
                non-italic
                text-primary-800
              ">
              Type
            </p>
            <p class="text-sm font-bold leading-5 text-black non-italic">
              {{ prefixGroup ? prefixGroup.type : '' }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 mt-5">
          <div>
            <p class="
                mb-1
                text-sm
                font-normal
                leading-5
                non-italic
                text-primary-800
              ">
              {{ $t("general.description") }}
            </p>
            <p class="text-sm font-bold leading-5 text-black non-italic"
              v-html="prefixGroup ? prefixGroup.description : ''">
              {{ prefixGroup ? prefixGroup.description : '' }}
            </p>
          </div>
        </div>

        <sw-divider class="my-8" />

        <p class="text-gray-500 uppercase sw-section-title">
          {{ $t('corePbx.prefix_groups.prefixes') }}
        </p>

        <!-- PREFIX GROUP-->

        <div class="float-left mt-1">
          <select class="border border-dark"
                  style="
                  text-align: center;
                  border: solid 1px black;
                  width: 77px;
                  height: 39px;
                  border-radius: 10%;"
                  v-model="selected"
                  @change="refreshTable()">
            <option style="text-align: center;" v-for="option in options" :value="option.value" >
              {{ option.name }}
            </option>
          </select>
        </div>       

        <div class="float-right">
          <sw-button
            v-show="fetchData"
            size="lg"
            variant="primary-outline"
            class="ml-4"
            @click="toggleFilter"
          >
            {{ $t('general.filter') }}
            <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
          </sw-button>        
        </div>
       
        <br/>
        
       <!-- Filtros -->
        <div>
          <slide-y-up-transition>
          <sw-filter-wrapper
            v-show="showFilters"
            class="relative grid grid-flow-col grid-rows"
          >      
            <div class="w-50" style="margin-left: 1em; margin-right: 1em">
              <sw-input-group :label="'Categories'" class="mt-2">
                <sw-select
                  v-model="filters.category"
                  :options="categories"
                  :group-select="false"
                  :searchable="true"
                  :show-labels="false"
                  :placeholder="'Select a category'"
                  :allow-empty="false"
                  group-values="options"
                  group-label="label"
                  track-by="name"
                  label="name"
                  @remove="clearStatusSearch()"               
                />
              </sw-input-group>

              <div style="min-width: 275px">
                <sw-input-group :label="'Prefix Type'" class="mt-2">
                  <sw-select
                  v-model="filters.prefix_type"
                  :options="prefix_type"
                  :group-select="false"
                  :searchable="true"
                  :show-labels="false"
                  :placeholder="'Select a prefix type'"
                  :allow-empty="false"
                  group-values="options"
                  group-label="label"
                  track-by="name"
                  label="name"
                  @remove="clearStatusSearch()"               
                  />
                </sw-input-group>
              </div>
            </div>

            <div class="w-50" style="margin-left: 1em; margin-right: 1em">            
              <sw-input-group
                :label="'Name'"
                class="mt-2"
                style="min-width: 275px"
              >
                <sw-input v-model="filters.name">                
                </sw-input>
              </sw-input-group>

              <sw-input-group
                v-if="filters.prefix_type.value == 'P'"
                :label="'Prefix'"
                class="mt-2"
                style="min-width: 275px"
                >
                  <sw-input v-model="filters.prefix">                
                  </sw-input>
              </sw-input-group> 
              
              <sw-input-group
                v-if="filters.prefix_type.value == 'FT'"
                :label="'From'"
                class="mt-2"
                style="min-width: 275px"
                >
                  <sw-input v-model="filters.from">                
                  </sw-input>
              </sw-input-group>
                          
            </div>

            <div class="w-50" style="margin-left: 1em; margin-right: 1em">
              
              <sw-input-group :label="'Countries'" class="mt-2">
                <sw-select
                  v-model="filters.country_id"
                  :options="countries"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="false"
                  :placeholder="$t('general.select_country')"
                  label="name"
                  track-by="id"            
                />
              </sw-input-group>

              <sw-input-group
                v-if="filters.prefix_type.value == 'FT'"
                :label="'To'"
                class="mt-2"
                style="min-width: 275px"
                >
                  <sw-input v-model="filters.to">                
                  </sw-input>
              </sw-input-group>

            </div>

            <label
              class="absolute text-sm leading-snug text-black cursor-pointer"
              @click="clearFilter"
              style="top: 20px; right: 30px"
              >{{ $t('general.clear_all') }}</label
            >
          </sw-filter-wrapper>
          </slide-y-up-transition>
        </div>        

        <!-- Table -->
        <div>
          <sw-table-component
          ref="table"
          :show-filter="false"
          :data="fetchData"
          table-class="table"
        >
          <sw-table-column              
              :sortable="false"
              :label="'Name'"
              sort-as="name"                        
          >
              <template slot-scope="row">
                <p class="text-sm  leading-5 text-black non-italic">
                  {{row.name}}
                </p>
              </template>           
           </sw-table-column>

            <sw-table-column
              :sortable="false"
              :label="'Prefix'"
              sort-as="prefix"
            >
              <template slot-scope="row">
                <p class="text-sm  leading-5 text-black non-italic">
                  {{row.prefix}}
                </p>
              </template>           
           </sw-table-column>

            <sw-table-column
              :sortable="false"
              :label="'From'"
              sort-as="from"
            >
              <template slot-scope="row">
                <p class="text-sm  leading-5 text-black non-italic">
                  {{row.from}}
                </p>
              </template>           
           </sw-table-column>
           
           <sw-table-column
              :sortable="false"
              :label="'To'"
              sort-as="to"
            >
              <template slot-scope="row">
                <p class="text-sm  leading-5 text-black non-italic">
                  {{row.to}}
                </p>
              </template>           
           </sw-table-column>

            <sw-table-column
              :sortable="false"
              :label="'Category'"
              sort-as="category"
            >
              <template slot-scope="row">
                
                <p v-if="row.category == 'C'" 
                   class="text-sm  leading-5 text-black non-italic"
                >
                  Custom
                </p>
                <p v-if="row.category == 'T'" 
                   class="text-sm  leading-5 text-black non-italic"
                >
                  Toll Free
                </p>
                <p v-if="row.category == 'I'" 
                   class="text-sm  leading-5 text-black non-italic"
                >
                  International
                </p>

              </template>           
           </sw-table-column>

            <sw-table-column
              :sortable="false"
              :label="'Country'"
              sort-as="country_id"
            >
              <template slot-scope="row">
                <p class="text-sm  leading-5 text-black non-italic">
                  {{ getCountry(row.country_id ? row.country_id : 0 ) }}
                </p>
              </template>           
           </sw-table-column>

            <sw-table-column
              :sortable="false"
              :label="'Rate Per Minute'"
              sort-as="rate_per_minute" 
            >      
              <template slot-scope="row">
                <p class="text-sm  leading-5 text-black non-italic">
                  {{ defaultCurrency.symbol + ' ' + row.rate_per_minute }}
                </p>
              </template>              
           </sw-table-column> 
        </sw-table-component>
        </div>
        

        <!-- / PREFIX GROUP-->

        <!-- 
        <sw-empty-table-placeholder v-show="showEmptyTable" :title="$t('corePbx.prefix_groups.no_prefixes')"
          :description="$t('corePbx.prefix_groups.list_of_prefixes')">
        </sw-empty-table-placeholder>

       
        <div v-show="!showEmptyTable" class="mt-5">
          <div v-for="(category, key) in getGroupedPrefixes" :key="key">
            <label class="
                text-sm
                not-italic
                font-medium
                leading-5
                text-primary-800 text-sm
              ">
              {{ categoryName(key) }}
            </label>
            <table class="w-full text-center item-table mb-5 mt-2">
              <colgroup>
                <col style="" />
                <col style="" />
                <col style="" />
                <col style="" />
                <col style="" />
                <col style="" />
              </colgroup>
              <thead class="bg-white border border-gray-200 border-solid">
                <th class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-left text-gray-700
                    border-t border-b border-gray-200 border-solid
                  ">
                  <span class="">
                    {{ $t('corePbx.internacional.type') }}
                  </span>
                </th>
                <th class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-left text-gray-700
                    border-t border-b border-gray-200 border-solid
                  ">
                  <span class="">
                    {{ $t('corePbx.prefix_groups.prefix') }}
                  </span>
                </th>
                <th class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-left text-gray-700
                    border-t border-b border-gray-200 border-solid
                    whitespace-nowrap
                  ">
                  <span class="">
                    {{ $t('corePbx.internacional.fromto') }}
                  </span>
                </th>
                <th class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-center text-gray-700
                    border-t border-b border-gray-200 border-solid
                  ">
                  {{ $t('corePbx.prefix_groups.prefix_name') }}
                </th>
                <th class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-center text-gray-700
                    border-t border-b border-gray-200 border-solid
                  ">
                  {{ $t('corePbx.prefix_groups.country') }}
                </th>
                <th class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-right text-gray-700
                    border-t border-b border-gray-200 border-solid
                  ">
                  <span>
                    {{ $t('corePbx.prefix_groups.rate_per_minutes') }}
                  </span>
                </th>
              </thead>
              <tbody>
                
                <tr v-for="(prefix, index) in category" :key="index" class="border">
                  <td class="
                      px-5
                      py-4
                      text-left
                      align-top
                      border-b border-gray-200 border-solid
                    ">
                    <div class="items-center text-sm">
                      <span class="">
                        {{ prefix.typecustom == 'P' ? 'Prefix' : 'From / To' }}
                      </span>
                    </div>
                  </td>
                  <td class="
                      px-5
                      py-4
                      text-left
                      align-top
                      border-b border-gray-200 border-solid
                    ">
                    <div class="items-center text-sm">
                      <span class="">
                        {{ prefix.prefix }}
                      </span>
                    </div>
                  </td>
                  <td class="
                      px-5
                      py-4
                      text-left
                      align-top
                      border-b border-gray-200 border-solid
                    ">
                    <div class="items-center text-sm">
                      <span class="">
                        {{ `${prefix.from || '' }${prefix.to ? ' / ':''}${prefix.to || ''}` }}
                      </span>
                    </div>
                  </td>
                  <td class="
                      px-5
                      py-4
                      text-center
                      border-b border-gray-200 border-solid
                    ">
                    <div class="items-center text-sm">
                      <span>
                        {{ prefix.name }}
                      </span>
                    </div>
                  </td>
                  <td class="
                      px-5
                      py-4
                      text-center
                      border-b border-gray-200 border-solid
                    ">
                    <div class="items-center text-sm">
                      <span>
                        {{ prefix.countryName }}
                      </span>
                    </div>
                  </td>
                  <td class="
                      px-5
                      py-4
                      text-right
                      align-top
                      border-b border-gray-200 border-solid
                    ">
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

        -->
       
        
        
      </div>
    </sw-card>
  </div>
</template>

<script>

import { mapActions, mapGetters } from 'vuex'
import { ArrowLeftIcon, FilterIcon, XIcon, HashtagIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    ArrowLeftIcon,
    FilterIcon,
    XIcon,
    HashtagIcon
  },
  data() {
    return {
      prefixGroup: {
        prefixes: [],
        status: null,
        prefix_group: [],
      },
      //
      showFilters: false,      
      isRequestOngoing: true,   
      timeout: null,
      filters: {
        category: { name: '', value: '' },
        name: '',
        country_id: '',
        prefix_type: { name: '', value: '' },  
        prefix: '',
        from: '',
        to: '',
      },           
      prefix_type: [
        {
          label: 'Type',
          isDisable: true,
          options: [
          { name: 'Prefix', value: 'P' },
          { name: 'From / To', value: 'FT' },
          ],
        },
      ],
      categories: [
        {
          label: 'Category',
          isDisable: true,
          options: [
            { name: 'Custom', value: 'C' },
            { name: 'International', value: 'I' },
            { name: 'Toll Free', value: 'T' },
          ],
        },
      ],      
      selected: 10,   
      options: [
        { name: '10', value: 10 },
        { name: '20', value: 20 },
        { name: '50', value: 50 },
        { name: '100', value: 100 },
      ],     
      //      
    }
  },
  computed: {
    ...mapGetters(['countries']),

    ...mapGetters('prefixGroup', ['selectedViewPrefixGroup']),

    ...mapGetters('company', ['defaultCurrency']),

    showEmptyTable() {
      return !this.prefixGroup.prefix_group.length
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
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

  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },

  created() {
    this.loadPrefixGroup()
  },  
  methods: {
    ...mapActions('prefixGroup', ['fetchViewPrefixGroup', 'deletePrefixGroup', 'fetchViewPrefixGroupNew']),

    async fetchData({ page }) {    

      let data = {
        id: this.$route.params.id,        
        category: this.filters.category.value,   
        name: this.filters.name,
        country_id: this.filters.country_id.id || '',
        prefix_type: this.filters.prefix_type.value,
        prefix: this.filters.prefix,   
        from: this.filters.from,  
        to: this.filters.to,      
        limit: this.selected,
        page
      }    

      this.isRequestOngoing = true
      let response = await this.fetchViewPrefixGroupNew(data)
      this.isRequestOngoing = false
     
      return {
        data: response.data.international_rate.data,
        pagination: {
          totalPages: response.data.international_rate.last_page,
          currentPage: page,
        },
      }

    },

    setFilters() {     
      clearTimeout(this.timeout)
      this.timeout = setTimeout(() => {    
        this.refreshTable()
      }, 900)     
    },

    getCountry(id){
      let country = this.countries.filter((country)=>country.id==id);
      return country.length > 0 ? country[0].name : "None" 
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }
      this.showFilters = !this.showFilters
    },

    clearFilter() {
      this.filters = {
        category: { name: '', value: '' },
        name: '',
        country_id: '',
        prefix_type: { name: '', value: '' },  
        prefix: '',
        from: '',
        to: '',
      }      
    },

    refreshTable() {
      this.$refs.table.refresh()
    }, 
    //

    async clearStatusSearch(removedOption, id) {
      this.filters.status = ''
      this.refreshTable()
    },

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