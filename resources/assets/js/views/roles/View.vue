<template>
  <base-page class="item-create">
    <form action="">
      <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
      <sw-page-header :title="$tc('roles.role_view')">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />
          <sw-breadcrumb-item
            :title="$tc('roles.title', 2)"
            to="/admin/roles"
          />
          <sw-breadcrumb-item
            :title="$t('roles.role_view')"
            to="/admin/roles/create"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>
      <sw-card>
        <!-- Basic  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('roles.basic') }}
          </h6>

          <div class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6">
            <sw-divider class="col-span-12" />
            <sw-input-group
              :label="$t('roles.role_name')"
              class="md:col-span-3">
              <div>
                <p
                  class="text-sm font-bold leading-5 text-black non-italic"
                  v-text="formData.name"
                ></p>
              </div>
            </sw-input-group>

            <div class="tabs mb-5 grid col-span-12">
              <div class="border-b tab">
                <div class="border-l-2 border-transparent relative">
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
                      tab-label"
                    for="chck1">
                    <span class="text-grey-darkest font-thin text-xl">
                      {{ $t("general.description") }}
                    </span>
                  </header>
                  <div class="description">
                    <p v-text="formData.description"></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
                <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('roles.permissions') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <sw-divider class="col-span-12" />
            <sw-table-component
              class="col-span-12"
              ref="table"
              :show-filter="false"
              :data="formData.permissions"
              table-class="table"
              variant="gray">
              
              <sw-table-column
                :sortable="true"
                :label="$t('roles.table.name')"
                show="name">
                <template slot-scope="row">
                  <span>{{ $t('roles.table.name') }}</span>
                  <span class="mt-6">{{ row.name }}</span>
                </template>
              </sw-table-column>

               <sw-table-column
                :sortable="true"
                :label="$t('roles.table.title')"
                show="title">
                <template slot-scope="row">
                  <span>{{ $t('roles.table.title') }}</span>
                  <span class="mt-6">{{ row.title }}</span>
                </template>
              </sw-table-column>

            </sw-table-component>
            <div class="col-span-12"></div>
          </div>
        </div>
      </sw-card>
    </form>
  </base-page>
</template>

<script>
import RightArrow from '@/components/icon/RightArrow'
import LeftArrow from '@/components/icon/LeftArrow'
import { mapActions, mapGetters } from 'vuex'

import {
  TrashIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    TrashIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusIcon,
    RightArrow,
    LeftArrow,
  },
  data() {
    return {
        isLoading:true,
        isRequestOnGoing: true,
        formData: {
            name: null,
            description: null,
            permissions:[],
        },
    }
  },
  methods: {
    ...mapActions('roles', ['fetchRole']),
    async firstPermissions(){
      this.formData = await this.fetchRole(this.$route.params.id)
      this.isLoading = false
      this.isRequestOnGoing = false
    },
  },
  mounted() {
    this.firstPermissions()
  },
}
</script>


<style lang="scss">
.package-create-page {
  .package-foot {
    .package-total {
      min-width: 390px;
    }
  }
  @media (max-width: 480px) {
    .package-foot {
      .package-total {
        min-width: 384px;
      }
    }
  }
}

// Dropdown
.tab {
  overflow: hidden;
}
.tab-content {
  max-height: 0;
  transition: all 0.5s;
}
input:checked + .tab-label .test {
  background-color: #000;
}
input:checked + .tab-label .test svg {
  transform: rotate(180deg);
  stroke: #fff;
}
input:checked + .tab-label::after {
  transform: rotate(90deg);
}
input:checked ~ .tab-content {
  max-height: 100vh;
}
</style>