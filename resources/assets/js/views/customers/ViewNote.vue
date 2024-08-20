<template>
  <base-page class="customer-note-view">
    <sw-page-header class="mb-3" :title="$t('customer_notes.view_note')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item to="/admin/customers" :title="$t('customers.title')" />
        <sw-breadcrumb-item :to="`/admin/customers/${$route.params.id}/note`" :title="$t('customer_notes.title')" />
        <sw-breadcrumb-item to="#" :title="$t('customer_notes.view_note')" active/>
      </sw-breadcrumb>

      <template slot="actions">
         <sw-button 
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/note`"           
            variant="primary-outline"              
            class="mr-4"     
          >
            {{ $t('general.go_back') }}
         </sw-button>
         <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/${$route.params.note_id}/edit-note`"
          class="mr-3"
          variant="primary-outline"
         >
          {{ $t('general.edit') }}
        </sw-button>
        <sw-button slot="activator" variant="primary" @click="removeNote($route.params.id)">
          {{ $t('general.delete') }}
        </sw-button>
      </template>
    </sw-page-header>

    <sw-card>
      <div class="col-span-12">
        <p class="text-gray-500 uppercase sw-section-title">
          {{ $t('customer_notes.basic_info') }}
        </p>

        <div class="grid md:grid-cols-2 grid-cols-1 gap-4 mt-5">
          <div>
            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
              {{ $t("customer_notes.summary_note") }}
            </p>
            <p class="text-sm font-bold leading-5 text-black non-italic">
              {{ note.summary }}
            </p>
          </div>
          <div>
            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
              {{ $t("customer_notes.pinned") }}
            </p>
            <p class="text-sm font-bold leading-5 text-black non-italic">
              {{ isSticky }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 mt-5">
          <div>
            <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
              {{ $t("customers.note") }}
            </p>
            <p class="text-sm font-bold leading-5 text-black non-italic">
              {{ note.note }}
            </p>
          </div>
        </div>

      </div>

    </sw-card>
  </base-page>
</template>

<script>
import { mapActions } from "vuex";

export default {
  data() {
    return {
      note: {
        summary: null,
        note: null,
        stiky: false
      }
    }
  },
  created() {
    this.loadNote();
  },
  computed: {
    isSticky() {
      return this.note.stiky ? 'Yes' : 'No'
    }
  },
 methods: {
   ...mapActions('customerNote', [
     'fetchCustomerNote',
     'deleteCustomerNote',
   ]),

   async loadNote() {
     let response = await this.fetchCustomerNote(this.$route.params.note_id);

     if (response.data) {
       this.note = { ...this.note, ...response.data.customerNote };
     }
   },

   async removeNote(id) {
     swal({
       title: this.$t('general.are_you_sure'),
       text: this.$tc('customer_notes.confirm_delete'),
       icon: '/assets/icon/trash-solid.svg',
       buttons: true
     }).then(async (willDelete) => {
       if (willDelete) {
         let res = await this.deleteCustomerNote({ ids: [id] })

         if (res.data.success) {
           window.toastr['success'](
             this.$tc('customer_notes.deleted_message', 1)
           )
           return true
         }
         window.toastr['error'](res.data.message)
         return true
       }
     })
   },
 }
}
</script>

<style scoped>

</style>