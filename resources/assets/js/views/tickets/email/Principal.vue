<template >
  <!-- <base-page v-if="isSuperAdmin" > -->

  <div :class="{ 'xl:pl-64': showSideBar }">

    <sw-page-header :title="$t('tickets.email.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="#" :title="$tc('tickets.email.title', 2)" active />
      </sw-breadcrumb>

      <template slot="actions">
        <div class="mr-3 hidden xl:block">
          <sw-button class="" variant="primary-outline" @click="toggleListCustomers">
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
      <div class="relative flex items-center justify-between h-10 list-none border-b-2 border-gray-200 border-solid">
        <p class="text-sm">
        </p>
      </div>

      <form action="" class="" @submit.prevent="updateEmail">

        <!-- Email -->
        <sw-input-group :label="'Ticket BCC Email'" class="md:col-span- mt-2 mb-2" :error="emailError">
          <sw-input style="max-width: 30%" :invalid="$v.formData.email.$error" v-model="formData.email" type="text" name="email"
            @input="$v.formData.email.$touch()" />
        </sw-input-group>
        <!-- Email -->

        <!-- Email -->
        <sw-input-group :label="'Ticket Prefix'" class="md:col-span- mt-2 mb-2" :error="ticketError">
          <sw-input style="max-width: 30%" :invalid="$v.formData.ticket_prefix.$error" v-model="formData.ticket_prefix" type="text"
            name="prefix" @input="$v.formData.ticket_prefix.$touch()" />
        </sw-input-group>
        <!-- Email -->


        <div class="tabs mb-5 grid col-span-12">
          <div class="border-b tab">
            <div class="border-l-2 border-transparent relative">              
              <header class="
                            col-span-5
                            flex
                            justify-between
                            items-center
                            p-3
                            pl-0
                            pr-8
                            select-none
                            tab-label
                          " for="chck1">
                <span class="text-grey-darkest font-thin text-xl">
                  {{ $t('tickets.template') }}
                </span>
                <!--
                <div class="
                              rounded-full
                              border border-grey
                              w-7
                              h-7
                              flex
                              justify-between
                              items-center
                              p-3
                              pl-0
                              pr-8
                              cursor-pointer
                              select-none
                              tab-label
                            " for="chck1">
                  <span class="text-grey-darkest font-thin text-xl">
                    {{ $t('tickets.template') }}
                  </span>
                  <div class="
                                rounded-full
                                border border-grey
                                w-7
                                h-7
                                flex
                                items-center
                                justify-center
                                test
                              ">
                    
                    <svg aria-hidden="true" class="" data-reactid="266" fill="none" height="24" stroke="#606F7B"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24"
                      xmlns="http://www.w3.org/2000/svg">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </div>
                </div>
                -->
              </header>
              <hr class="mb-3">
              <div class="">
                <div class="pl-0 pr-8 pb-5 text-grey-darkest">
                  <ul class="pl-0">
                    <li class="pb-2">
                      <sw-tabs :active-tab="activeTab">
                        <sw-tab-item :title="$t('tickets.creation_client')">
                          <base-custom-input v-model="emails.ticket_creatio_customer" :fields="InvoiceMailFields"
                            class="mt-2" />
                        </sw-tab-item>
                        <sw-tab-item :title="$t('tickets.creation_user')">
                          <base-custom-input v-model="emails.ticket_creatio_user" :fields="InvoiceMailFields"
                            class="mt-2" />
                        </sw-tab-item>
                        <sw-tab-item :title="$t('tickets.update_customer')">
                          <base-custom-input v-model="emails.ticket_update_customer" :fields="InvoiceMailFields"
                            class="mt-2" />
                        </sw-tab-item>
                        <sw-tab-item :title="$t('tickets.update_user')">
                          <base-custom-input v-model="emails.ticket_update_user" :fields="InvoiceMailFields"
                            class="mt-2" />
                        </sw-tab-item>
                      </sw-tabs>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <sw-button :loading="isLoading" :disabled="isLoading" variant="primary" type="submit" class="mt-4">
          <save-icon v-if="!isLoading" class="mr-2" />
          {{ $t('settings.customization.save') }}
        </sw-button>
      </form>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { email, required } = require('vuelidate/lib/validators')
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
      activeTab: 'Creation of ticket for the customer',
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
      users_select: [],
      showSideBar: true,
      formData: {
        email: null,
        ticket_prefix: null
      }      
    }
  },

  computed: {
    emailError() {
      if (!this.$v.formData.email.$error) {
        return ''
      }

      if (!this.$v.formData.email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },
    ticketError() {
      if (!this.$v.formData.ticket_prefix.$error) {
        return ''
      }
    },

    ...mapGetters('customer', ['customers']),
    ...mapGetters('users', ['users']),
    ...mapGetters('ticketDepartament', ['departaments']),
    
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

  mounted() { },

  destroyed() { },

  async created() {
    this.isRequestOnGoing = true
    let res = await this.fetchCompanySettings([
      'ticket_creatio_customer',
      'ticket_creatio_user',
      'ticket_update_customer',
      'ticket_update_user',
      'ticket_bbc_email',
      'TTW_prefix'
    ])

    this.emails.ticket_creatio_customer = res
      ? res.data.ticket_creatio_customer
      : null
    this.emails.ticket_creatio_user = res ? res.data.ticket_creatio_user : null
    this.emails.ticket_update_customer = res
      ? res.data.ticket_update_customer
      : null
    this.emails.ticket_update_user = res ? res.data.ticket_update_user : null
    this.formData.email = res.data.ticket_bbc_email
    this.formData.ticket_prefix = res.data.TTW_prefix
    this.isRequestOnGoing = false


  },

  validations: {
    formData: {
      email: {
        email,
      },
      ticket_prefix: {
        required,
      },
    }    
  },

  methods: {
    ...mapActions('company', ['fetchCompanySettings']),
    ...mapActions('company', ['updateCompanySettings']),

    ...mapActions('ticketDepartament', [
      'fetchDepartaments',
      'fetchDepartament',
    ]),
    ...mapActions('users', ['fetchUsers']),

    toggleListCustomers() {
      this.showSideBar = !this.showSideBar
    },

    async updateEmail() {

      this.$v.formData.$touch()
      if (this.$v.$invalid) {
          return true
      }

      let data = {
        settings: {
          ticket_creatio_customer: this.emails.ticket_creatio_customer,
          ticket_creatio_user: this.emails.ticket_creatio_user,
          ticket_update_customer: this.emails.ticket_update_customer,
          ticket_update_user: this.emails.ticket_update_user,
          // email
          ticket_bbc_email: this.formData.email,
          TTW_prefix: this.formData.ticket_prefix
        },
      }

      if (this.updateSetting(data)) {
        window.toastr['success'](this.$t('tickets.email.email_updated'))
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