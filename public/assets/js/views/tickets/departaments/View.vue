<template>
    <!-- Base  -->
    <base-page class="tckets-departaments-view">
        <!-- Header  -->
        <sw-page-header class="mb-3" :title="$t('tickets.departaments.view_departaments')">
            <sw-breadcrumb slot="breadcrumbs">
                <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
                <sw-breadcrumb-item to="/admin/tickets/departaments" :title="$t('tickets.menu_title.departaments')" />
            <!--    <sw-breadcrumb-item to="#" :title="itemGroup ? itemGroup.name : ''" active/>  -->
            </sw-breadcrumb>
             <template slot="actions">
                <sw-button
                    tag-name="router-link"
                    :to="`/admin/tickets/departaments/${$route.params.id}/edit`"
                    class="mr-3"
                    variant="primary-outline"
                >
                    {{ $t('general.edit') }}
                </sw-button>
                <sw-button slot="activator" variant="primary" @click="removeticketdepart($route.params.id)">
                    {{ $t('general.delete') }}
                </sw-button>
             </template>

        </sw-page-header>

        <sw-card>
            <div class="col-span-12">
                <p class="text-gray-500 uppercase sw-section-title">
                    {{ $t('item_groups.basic_info') }}
                </p>
                <div class="grid grid-cols-1 gap-4 mt-5">
                    <div>
                        <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                            Name
                        </p>
                        <p class="text-sm font-bold leading-5 text-black non-italic">
                            {{ ticketDepart ? ticketDepart.name : '' }}
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 mt-5">
                    <div>
                        <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                            {{ $t("general.description") }}
                        </p>
                        <p class="text-sm font-bold leading-5 text-black non-italic" v-html="ticketDepart ? ticketDepart.description : '' ">
                            {{ ticketDepart ? ticketDepart.description : '' }}
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 mt-5">
                    <div>
                        <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                            Email
                        </p>
                        <p class="text-sm font-bold leading-5 text-black non-italic">
                            {{ ticketDepart ? ticketDepart.email : '' }}
                        </p>
                    </div>
                </div>
<!-- Oculto -->
            <div v-if="contenido">
                <div class="grid grid-cols-1 gap-4 mt-5">
                    <div>
                        <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                            Default Priority
                        </p>
                        <p v-if="band" class="text-sm font-bold leading-5 text-black non-italic">
                            {{ ticketDepart.default_priority.text}}
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 mt-5">
                    <div>
                        <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                            Email handling
                        </p>
                        <p v-if="band" class="text-sm font-bold leading-5 text-black non-italic">
                            {{ ticketDepart.email_handling.text}}
                        </p>
                    </div>
                </div>
                 <div class="grid grid-cols-1 gap-4 mt-5">
                    <div>
                        <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                            Automatically Close Tickets 
                        </p>
                        <p class="text-sm font-bold leading-5 text-black non-italic">
                            {{ ticketDepart ? ticketDepart.automatically_close+" Days" : '' }}
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 mt-5">
                    <div>
                        <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                            Automatically Delete Tickets 
                        </p>
                        <p  class="text-sm font-bold leading-5 text-black non-italic">
                            {{ ticketDepart ? ticketDepart.automatically_delete+" Days" : '' }}
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 mt-5">
                    <div>
                        <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                            Status 
                        </p>
                        <p v-if="band"  class="text-sm font-bold leading-5 text-black non-italic">
                            {{  ticketDepart.status.text }}
                        </p>
                    </div>
                </div>
            </div>
                <sw-divider class="my-8" />


                <!-- Items -->
               
            </div>
        </sw-card>

    </base-page>
</template>

<script>

import {mapActions, mapGetters} from "vuex";

export default {
    data() {
        return {
            contenido:false,
            band:false,
            ticketDepart: {
                items: []
            },
        }
    },
    computed: {
        ...mapGetters('ticketDepartament', ['selectedViewDepartament']),

        ...mapGetters('company', ['defaultCurrency']),

    },
    created() {
        this.loadticketDepart();
    },
    methods: {
        ...mapActions('ticketDepartament', [
            'fetchViewDepartament',
            'deleteDepartament'
            
            ]),

        async loadticketDepart() {

            let response = await this.fetchViewDepartament({ id: this.$route.params.id });
            if (response.status == 200) {
                this.ticketDepart = response.data.departaments;
                this.band=true;
            }
        },

        async removeticketdepart(id) { 
      this.id = id      
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteDepartament({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('tickets.departaments.deleted_message', 1))
            this.$router.push('/admin/tickets/departaments')
          }
          return true
        }
      })
     },
    }
}
</script>