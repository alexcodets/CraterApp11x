<template>
  <base-page v-if="isSuperAdmin" class="items">

    <sw-page-header :title="$t('avalara.title_glossary')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item to="#" :title="$tc('avalara.title', 2)" active />
      </sw-breadcrumb>

      <!-- <template slot="actions">
        <sw-button
          v-show="totalAvalaraConfigs"
          variant="primary-outline"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="/admin/avalara/config/logs"
          variant="primary"
          class="ml-4"
        >
          <eye-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('avalara.logs') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="/admin/avalara/config/create"
          variant="primary"
          class="ml-4"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('avalara.add_new_config') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="/admin/avalara/config/create"
          variant="primary"
          class="ml-4"
        >
          {{ $t('avalara.glossary') }}
        </sw-button>
      </template> -->
    </sw-page-header>
<!-- 
    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">
        <sw-input-group
          :label="$tc('avalara.filter.conexion')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-input
            v-model="filters.conexion"
            type="text"
            name="conexion"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('avalara.filter.user_name')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-input
            v-model="filters.user_name"
            type="text"
            name="user_name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <label
          class="absolute text-sm leading-snug text-gray-900 cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
        >
          {{ $t('general.clear_all') }}</label
        >
      </sw-filter-wrapper>
    </slide-y-up-transition> -->

    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('avalara.no_avalara_config')"
      :description="$t('avalara.list_of_avalara_configs')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/avalara/config/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('avalara.add_new_config') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div class="relative table-container" v-show="!showEmptyScreen">
      <div
        class="
          relative
          flex
          items-center
          justify-between
          h-10
          mt-5
          list-none
          border-b-2 border-gray-200 border-solid
        "
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ avalaraConfigs.length }}</b>

          {{ $t('general.of') }}

          <b>{{ totalAvalaraConfigs }}</b>
        </p>

        <sw-transition type="fade">
          <sw-dropdown v-if="selectedAvalaraConfigs.length">
            <span
              slot="activator"
              class="
                flex
                block
                text-sm
                font-medium
                cursor-pointer
                select-none
                text-primary-400
              "
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item @click="removeMultipleAvalaraConfigs">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>


      <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
      >

        <sw-table-column
          :sortable="true"
          :label="$t('avalara.item.transactions')"
          show="transaction"
        >
        <template slot-scope="row">            
            {{ row.transaction }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('avalara.item.services')"
          show="services"
        >
          <template slot-scope="row">            
            <pre class="font-base">{{ row.services }}</pre>
            <!-- <p><span v-html="row.services"></span></p> -->
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('avalara.item.types')"
          show="type"
        >
          <template slot-scope="row">
            <pre class="font-base">{{ row.type }}</pre>
            <!-- {{ row.type }} -->
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('avalara.item.apply_to')"
          show="apply_to"
        >
          <template slot-scope="row">
            <!-- {{ row.apply_to }} -->
            <pre class="font-base">{{ row.apply_to }}</pre>
          </template>
        </sw-table-column>

      </sw-table-component>
    </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import AstronautIcon from '@/components/icon/AstronautIcon'
import {
  EyeIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon
} from '@vue-hero-icons/solid'

export default {
  components: {
    EyeIcon,
    AstronautIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    PlusIcon,
  },

  data() {
    return {
      id: null,
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      filters: {
        conexion: '',
      },
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('avalara', [
      'selectedAvalaraConfigs',
      'totalAvalaraConfigs',
      'avalaraConfigs',
      'selectAllField',
    ]),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    showEmptyScreen() {
      return !this.totalAvalaraConfigs && !this.isRequestOngoing
    },

    selectField: {
      get: function () {
        return this.selectedAvalaraConfigs
      },
      set: function (val) {
        this.selectedUser(val)
      },
    },

    selectAllFieldStatus: {
      get: function () {
        return this.selectAllField
      },
      set: function (val) {
        this.setSelectAllState(val)
      },
    },
  },
  created() {
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
  },
  watch: {
    /* filters: {
      handler: 'setFilters',
      deep: true,
    }, */
  },

  destroyed() {
    /* if (this.selectAllField) {
      this.selectAllAvalaraConfigs()
    } */
  },

  methods: {
    /* ...mapActions('avalara', [
      'fetchAvalaraConfigs',
      'selectAvalaraConfig',
      'resetSelectedAvalaraConfigs',
      'selectAllAvalaraConfigs',
      'deleteAvalaraConfig',
      'cloneAvalaraConfig',
      'setSelectAllState',
      'setAvalaraConfigDefault'
    ]),
 */

    refreshTable() {
      this.$refs.table.refresh()
    },

    getTypeOfPaymentbyService(service_type_name){
      let payment_type = '';
      switch (service_type_name) {
          case 'Access Charge':
              payment_type = 'TAXABLE_AMOUNT'
              break
          case 'Access-Local Only Service':
              payment_type = 'TAXABLE_AMOUNT'
              break
          case 'Activation':
              payment_type = 'TAXABLE_AMOUNT'
              break
          case 'Enhanced Feature Charge':
              payment_type = 'TAXABLE_AMOUNT'
              break
          case 'Equipment Rental':
              payment_type = 'TAXABLE_AMOUNT'
              break
          case 'Equipment Repair':
              payment_type = 'TAXABLE_AMOUNT'
              break
          case 'Install':
              payment_type = 'TAXABLE_AMOUNT'
              break
          case 'Invoice':
              payment_type = 'NONE'
              break
          case 'Late Charge':
              payment_type = 'TAXABLE_AMOUNT'
              break
          case 'Lines':
              payment_type = 'LINES'
              break
          case 'LNP (Local Number Portability)':
              payment_type = 'TAXABLE_AMOUNT'
              break
          case 'Local Feature Charge':
              payment_type = 'TAXABLE_AMOUNT'
              break
          case 'PBX':
              payment_type = 'LINES'
              break
          case 'PBX Extension':
              payment_type = 'LINES'
              break
          case 'PBX High Capacity':
              payment_type = 'LINES'
              break
          case 'PBX Outbound Channel':
              payment_type = 'LINES'
              break
          case 'Toll-Free Number':
              payment_type = 'TAXABLE_AMOUNT'
              break
          case 'Wireless Access Charge':
              payment_type = 'TAXABLE_AMOUNT'
              break
          case 'Wireless Lines':
              payment_type = 'LINES'
              break
          default:
              break
      }

      return payment_type;
    },

    async fetchData({ page, filter, sort }) {
      let services_one = '', services_two = '', services_three = '', services_four = '';
      let type_one = '', type_two = '', type_three = '', type_four = '';
      let apply_one = '', apply_two = '', apply_three = '', apply_four = '';

      let res = await window.axios.get('/api/v1/avalara-service-types/19')
      if (res) {
        res.data.avalara_service_types.forEach(element => {
          services_one += element.service_type_name+'\n'
          type_one += this.getTypeOfPaymentbyService(element.service_type_name)+'\n'
          apply_one += (this.getTypeOfPaymentbyService(element.service_type_name) === 'TAXABLE_AMOUNT' ? 'Cdrs, custom destination and apps' : 'DID and Extensions')+'\n'
          // convert html string into DOM
          // services_one = parser.parseFromString(htmlStr, "text/html");
        });
      }

      let res2 = await window.axios.get('/api/v1/avalara-service-types/20')
      if (res2) {
        res2.data.avalara_service_types.forEach(element => {
          services_two += element.service_type_name+'\n'
          type_two += this.getTypeOfPaymentbyService(element.service_type_name)+'\n'
          apply_two += (this.getTypeOfPaymentbyService(element.service_type_name) === 'TAXABLE_AMOUNT' ? 'Cdrs, custom destination and apps' : 'DID and Extensions')+'\n'

        });
      }

      res = await window.axios.get('/api/v1/avalara-service-types/59')
      if (res) {
        res.data.avalara_service_types.forEach(element => {
          services_three += element.service_type_name+'\n'
          type_three += this.getTypeOfPaymentbyService(element.service_type_name)+'\n'
          apply_three += (this.getTypeOfPaymentbyService(element.service_type_name) === 'TAXABLE_AMOUNT' ? 'Cdrs, custom destination and apps' : 'DID and Extensions')+'\n'

        });
      }

      res = await window.axios.get('/api/v1/avalara-service-types/65')
      if (res) {
        res.data.avalara_service_types.forEach(element => {
          services_four += element.service_type_name+'\n'
          type_four += this.getTypeOfPaymentbyService(element.service_type_name)+'\n'
          apply_four += (this.getTypeOfPaymentbyService(element.service_type_name) === 'TAXABLE_AMOUNT' ? 'Cdrs, custom destination and apps' : 'DID and Extensions')+'\n'

        });
      }
      

      let data = [
        { 
          transaction: 'VoIP',
          services: services_one,
          type: type_one,
          apply_to: apply_one
        },
        { 
          transaction: 'VoIPA',
          services: services_two,
          type: type_two,
          apply_to: apply_two
        },
        { 
          transaction: '(VoIP- Nomadic)',
          services: services_three,
          type: type_three,
          apply_to: apply_three
        },
        { 
          transaction: '(Non-Interconnected VoIP)',
          services: services_four,
          type: type_four,
          apply_to: apply_four
        }
      ]

      return {
        data,
        pagination: {
          totalPages: 1,
          currentPage: 1,
        },
      }
    },

  },
}
</script>
