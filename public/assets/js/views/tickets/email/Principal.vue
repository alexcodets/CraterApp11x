<template >
  <!-- <base-page v-if="isSuperAdmin" > -->
  <div :class="{ 'xl:pl-96': showSideBar}">
    <sw-page-header :title="$t('tickets.email.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="#" :title="$tc('tickets.email.title', 2)" active />
      </sw-breadcrumb>

      <template slot="actions">
        <div class="mr-3 hidden xl:block">
          <sw-button
            class=""
            variant="primary-outline"
            @click="toggleListCustomers"
          >
            {{ $t('tickets.departaments.menu') }}
            <component :is="listIcon" class="w-4 h-4 ml-2 -mr-1" />
          </sw-button>
        </div>
        
      </template>
    </sw-page-header>

     <slide-x-left-transition>
      <Sidebart-departaments v-show="showSideBar" />
    </slide-x-left-transition>
     

    <div class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 list-none border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
        </p>
      </div>
        
      <form action="" class="mt-6" @submit.prevent="updateEmail">
   

      <div class="tabs mb-5 grid col-span-12">
                <div class="border-b tab">
                  <div class="border-l-2 border-transparent relative">
                    <input
                      class="
                        w-full
                        absolute
                        z-10
                        cursor-pointer
                        opacity-0
                        h-5
                        top-6
                      "
                      type="checkbox"
                      id="chck1"
                    />
                    <header
                      class="
                        col-span-5
                        flex
                        justify-between
                        items-center
                        p-3
                        pl-0
                        pr-8
                        cursor-pointer
                        select-none
                        tab-label
                      "
                      for="chck1"
                    >
                      <span class="text-grey-darkest font-thin text-xl">
                        {{ $t('tickets.template') }}
                      </span>
                      <div
                        class="
                          rounded-full
                          border border-grey
                          w-7
                          h-7
                          flex
                          items-center
                          justify-center
                          test
                        "
                      >
                        <!-- icon by feathericons.com -->
                        <svg
                          aria-hidden="true"
                          class=""
                          data-reactid="266"
                          fill="none"
                          height="24"
                          stroke="#606F7B"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          viewbox="0 0 24 24"
                          width="24"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                      </div>
                    </header>
                    <div class="tab-content">
                      <div class="pl-0 pr-8 pb-5 text-grey-darkest">
                        <ul class="pl-0">
                          <li class="pb-2">
                            <sw-tabs :active-tab="activeTab">
                              <sw-tab-item :title="$t('tickets.creation_client')">
                                <base-custom-input
                                  v-model="emails.ticket_creatio_customer"
                                  :fields="InvoiceMailFields"
                                  class="mt-2"
                                />
                              </sw-tab-item>
                              <sw-tab-item :title="$t('tickets.creation_user')">
                                <base-custom-input
                                  v-model="emails.ticket_creatio_user"
                                  :fields="InvoiceMailFields"
                                  class="mt-2"
                                />
                              </sw-tab-item>
                              <sw-tab-item :title="$t('tickets.update_customer')">
                                <base-custom-input
                                  v-model="emails.ticket_update_customer"
                                  :fields="InvoiceMailFields"
                                  class="mt-2"
                                />
                              </sw-tab-item>
                              <sw-tab-item :title="$t('tickets.update_user')">
                                <base-custom-input
                                  v-model="emails.ticket_update_user"
                                  :fields="InvoiceMailFields"
                                  class="mt-2"
                                />
                              </sw-tab-item>
                            </sw-tabs>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          <sw-button
              :loading="isLoading"
              :disabled="isLoading"
              variant="primary"
              type="submit"
              class="mt-4"
            >
              <save-icon v-if="!isLoading" class="mr-2" />
              {{ $t('settings.customization.save') }}
            </sw-button>
      </form>
    </div>
  </div>
  </div>
 
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import SidebartDepartaments from '../SidebartTickets'
import CapsuleIcon from '@/components/icon/CapsuleIcon'
import {
  PlusIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  ClipboardListIcon,
  PencilIcon,
  TrashIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    CapsuleIcon,
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    ClipboardListIcon,
    SidebartDepartaments,
    EyeIcon,
    PencilIcon,
    TrashIcon,
  },

  data() {
    return {

      activeTab: 'Creation of ticket for the client',


      emails: {
        ticket_creatio_customer: null, 
        ticket_creatio_user: null, 
        ticket_update_customer: null, 
        ticket_update_user: null, 
      },
    
      isLoading: false,
      InvoiceMailFields: [
        'customer',
        'customerCustom',
        'ticket',
        'invoiceCustom',
        'company',
      ],

      isRequestOngoing: true,
      users_select:[],
     
       showSideBar: true,
    }
  },

  computed: {
    ...mapGetters('customer', ['customers']),
    ...mapGetters('users', ['users']),
    ...mapGetters('ticketDepartament', ['departaments']),
    isRequestOngoing(){},
    listIcon() {
        return this.showSideBar ? 'x-icon' : 'clipboard-list-icon'
      },
  },
  watch: {
   /*  filters: {
      handler: 'setFilters',
      deep: true,
    }, */
  },

  mounted() {
  },

  destroyed() {
  },

   async created() {
     this.isRequestOnGoing = true
      let res = await this.fetchCompanySettings([
          'ticket_creatio_customer',
          'ticket_creatio_user',
          'ticket_update_customer',
          'ticket_update_user'
      ]);


      this.emails.ticket_creatio_customer= res ? res.data.ticket_creatio_customer: null
      this.emails.ticket_creatio_user= res ? res.data.ticket_creatio_user: null 
      this.emails.ticket_update_customer= res ? res.data.ticket_update_customer: null 
      this.emails.ticket_update_user= res ? res.data.ticket_update_user: null
      this.isRequestOnGoing = false

  
  },

  methods: {

    ...mapActions('company', ['fetchCompanySettings']),
    ...mapActions('company', ['updateCompanySettings']),
    

    ...mapActions('ticketDepartament', ['fetchDepartaments','fetchDepartament']),
    ...mapActions('users', ['fetchUsers']),


    toggleListCustomers() {
      this.showSideBar = !this.showSideBar
    },

     async updateEmail() {

      let data = {
        settings: {
          ticket_creatio_customer: this.emails.ticket_creatio_customer, 
          ticket_creatio_user: this.emails.ticket_creatio_user, 
          ticket_update_customer: this.emails.ticket_update_customer, 
          ticket_update_user: this.emails.ticket_update_user,
        },
      }

      if (this.updateSetting(data)) {
        window.toastr['success'](
          this.$t('tickets.email.email_updated')
        )
      }
    },

    async updateSetting(data) {
      this.isLoading = true
      let res = await this.updateCompanySettings(data)

      if (res.data.success) {
        this.isLoading = false
        return true
      }

      return false
    },

  }, 
    
}
</script>