<template>
  <sw-card variant="setting-card">
    <base-page>
      <div class="flex justify-end">
        <sw-button
          tag-name="router-link"
          :to="`/admin/settings/stripe`"
          variant="primary-outline"
        >
          {{ $t('general.go_back') }}
        </sw-button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <sw-page-header class="mb-3" :title="$tc('stripe.title')">
          <sw-breadcrumb slot="breadcrumbs">
            <sw-breadcrumb-item
              to="/admin/settings/payment-gateways"
              :title="$tc('settings.payment_gateways.title')"
            />
            <sw-breadcrumb-item
              to="/admin/settings/stripe"
              :title="$tc('stripe.title', 2)"
            />
          </sw-breadcrumb>
        </sw-page-header>

        <div class="flex flex-wrap items-center justify-end">
          <sw-button
            tag-name="router-link"
            :to="`/admin/settings/stripe/${$route.params.id}/edit`"
            class="w-full md:w-auto md:ml-2 mb-2 md:mb-0"
            variant="primary-outline"
          >
            <pencil-icon class="mr-1" />
            {{ $t('general.edit') }}
          </sw-button>
          <sw-button
            slot="activator"
            variant="primary"
            class="w-full md:w-auto md:ml-2 mb-2 md:mb-0"
            @click="removeStripe($route.params.id)"
          >
            <trash-icon class="mr-1" />
            {{ $t('general.delete') }}
          </sw-button>
        </div>
      </div>

      <sw-card class="flex flex-col mt-3">
        <div>
          <div class="col-span-12">
            <p class="text-gray-500 uppercase sw-section-title">
              {{ $t('stripe.basic_info') }}
            </p>
            <div class="grid grid-cols-1 gap-4 mt-5">
              <div>
                <span
                  class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
                >
                  {{ $t('stripe.public_key') }}
                </span>
                <p
                  class="text-sm font-bold font-bold leading-5 text-black non-italic"
                >
                  {{ formData.public_key }}
                </p>
              </div>
              <div>
                <span
                  class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
                >
                  {{ $t('stripe.secret_key') }}
                  <sw-button size="sm" class="mr-2" slot="activator" v-on:click="isHidden = !isHidden">
                    {{ $t('general.show') }}
                  </sw-button>
                </span>
                <p
                  class="text-sm font-bold font-bold leading-5 text-black non-italic"
                  v-if="isHidden"
                >
                  {{ formData.secret_key }}
                </p>
                <p
                  class="text-sm font-bold font-bold leading-5 text-black non-italic"
                  v-if="!isHidden"
                >
                  **************
                </p>
              </div>
              <div>
                <span
                  class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
                >
                  {{ $t('stripe.enviroment') }}
                </span>
                <p
                  class="text-sm font-bold font-bold leading-5 text-black non-italic"
                >
                  {{ formData.environment }}
                </p>
              </div>
              <div>
                <span
                  class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
                >
                  {{ $t('stripe.status') }}
                </span>
                <div v-if="formData.status == 'A'">
                <sw-badge
                  :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
                  :color="$utils.getBadgeStatusColor('COMPLETED').color"
                  class="px-3 py-1"
                >
                  {{ $t('general.active') }}
                </sw-badge>
              </div>
              <div v-if="formData.status == 'I'">
                <sw-badge
                  :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
                  :color="$utils.getBadgeStatusColor('OVERDUE').color"
                  class="px-3 py-1"
                >
                  {{ $t('general.inactive') }}
                </sw-badge>
              </div>
              </div>
              <div>
                <span
                  class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
                >
                  {{ $t('stripe.currency') }}
                </span>
                <p class="text-sm font-bold leading-5 text-black non-italic">
                  {{ formData.currency }}
                </p>
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
import { TrashIcon, PencilIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    TrashIcon,
    PencilIcon,
  },
  data() {
    return {
      isHidden: false,
      formData: {},
    }
  },
  created() {
    this.fethData()
  },
  methods: {
    ...mapActions('stripes', ['fetchStripe', 'deleteStripe']),

    async fethData() {
      try {
        this.isRequestOngoing = true
        let response = await this.fetchStripe(this.$route.params.id)
        this.formData = response.data
      } catch (e) {
       // console.log(e)
      } finally {
        this.isRequestOngoing = false
      }
    },

    async removeStripe(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteStripe(id)
            window.toastr['success'](this.$tc('stripe.deleted_message', 1))
            this.$router.push('/admin/settings/stripe')
          return true
        }
      })
    },
  },
}
</script>
