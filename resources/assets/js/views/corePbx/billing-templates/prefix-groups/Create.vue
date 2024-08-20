<style>
.table-responsive-item2 {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.tablemin {
  min-width: 900px;
}

/* Additional media query for finer control (optional) */
@media (max-width: 768px) {
  .table-responsive-item2 {
    /* Adjust table width as needed for smaller screens */
    width: 100%; /* Example adjustment */
  }
}
</style>

<template>
  <div class="relative">
    <!-- Page Header -->
    <sw-page-header :title="pageTitle" class="mb-3">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          :title="$t('corePbx.corePbx')"
          to="/admin/corePBX"
        />
        <sw-breadcrumb-item
          :title="$tc('corePbx.prefix_groups.prefix_group', 2)"
          to="/admin/corePBX/billing-templates/prefix-groups"
        />
        <sw-breadcrumb-item
          v-if="isEdit"
          :title="$t('corePbx.prefix_groups.edit_prefix_group')"
          to="#"
          active
        />
        <sw-breadcrumb-item
          v-else-if="isCopy"
          :title="$t('corePbx.prefix_groups.copy_prefix_group')"
          to="#"
          active
        />
        <sw-breadcrumb-item
          v-else
          :title="$t('corePbx.prefix_groups.new_prefix_group')"
          to="#"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-if="isEdit"
          :loading="isLoading"
          type="submit"
          variant="primary"
          size="lg"
          class=""
          @click="submitForm"
        >
          <save-icon class="w-6 h-6 mr-1 -ml-2 mr-2" v-if="!isLoading" />
          {{ $t('corePbx.prefix_groups.update_prefix_group') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="`/admin/corePBX/billing-templates/prefix-groups`"
          variant="primary-outline"
          type="button"
          size="lg"
          class="ml-4 hidden sm:flex"
        >
          <x-circle-icon class="w-6 h-6 mr-1 -ml-2" v-if="!isLoading" />
          {{ $t('general.cancel') }}
        </sw-button>
      </template>
    </sw-page-header>

    <!--  Form  -->
    <div v-if="!isEdit">
      <div class="grid grid-cols-12">
        <div class="col-span-12">
          <!-- desde aqui-->
          <form action="" @submit.prevent="submitPrefixGroup">
            <sw-input-group
              :label="$t('corePbx.prefix_groups.name')"
              :error="nameError"
              class="mb-4"
              required
            >
              <sw-input
                v-model.trim="formData.name"
                :invalid="$v.formData.name.$error"
                class="mt-2"
                focus
                type="text"
                name="name"
                @input="$v.formData.name.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('corePbx.prefix_groups.status')"
              class="mb-4"
            >
              <sw-select
                v-model="formData.status"
                :options="statuses"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('corePbx.prefix_groups.select_a_status')"
                class="mt-2"
                label="name"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('corePbx.prefix_groups.type')"
              class="mb-4"
              :error="typeError"
              required
            >
              <sw-select
                :invalid="$v.formData.type.$error"
                v-model="formData.type"
                :options="types"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('corePbx.prefix_groups.select_a_type')"
                class="mt-2"
                label="name"
                @input="$v.formData.type.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('corePbx.prefix_groups.description')"
              :error="descriptionError"
              class="mb-4"
            >
              <sw-textarea
                v-model="formData.description"
                rows="2"
                name="description"
                @input="$v.formData.description.$touch()"
              />
            </sw-input-group>

            <!-- PREFIXES -->
            <label
              class="text-sm not-italic font-medium leading-5 text-primary-800 text-sm"
            >
              {{ $t('corePbx.prefix_groups.prefixes') }}
            </label>

            <!-- desde aqui-->
            <div class="table-responsive-item2">
              <div class="tablemin">
                <table class="w-full text-center item-table mt-2">
                  <colgroup>
                    <col style="width: 15%" />
                    <col style="width: 25%" />
                    <col style="width: 15%" />
                    <col style="width: 15%" />
                    <col style="width: 15%" />
                    <col style="width: 15%" />
                  </colgroup>
                  <thead class="bg-white border border-gray-200 border-solid">
                    <!-- Type -->
                    <th
                      class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                    >
                      <span class="pl-12">
                        {{ $t('corePbx.internacional.type') }}
                      </span>
                    </th>
                    <th
                      class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                    >
                      <span class="pl-12">
                        {{ $t('corePbx.prefix_groups.prefix_name') }}
                      </span>
                    </th>
                    <!-- From/To -->
                    <th
                      class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                    >
                      <span class="pl-12 whitespace-nowrap">
                        {{ $t('corePbx.internacional.fromto') }}
                      </span>
                    </th>
                    <th
                      class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid"
                    >
                      {{ $t('corePbx.prefix_groups.prefix') }}
                    </th>
                    <th
                      class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid"
                    >
                      {{ $t('corePbx.prefix_groups.country') }}
                    </th>
                    <th
                      class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
                    >
                      <span class="pr-5">
                        {{ $t('corePbx.prefix_groups.rate_per_minutes') }}
                      </span>
                    </th>
                  </thead>
                  <draggable
                    v-model="formData.prefixes"
                    class="item-body"
                    tag="tbody"
                    handle=".handle"
                  >
                    <prefix
                      v-for="(prefix, index) in formData.prefixes"
                      :key="prefix.id"
                      :index="index"
                      :prefix-data="prefix"
                      :prefix-group="formData.prefixes"
                      @remove="removePrefix"
                      @update="updatePrefix"
                      @checkExists="checkExistPrefix"
                    />
                  </draggable>
                </table>
              </div>
            </div>
            <div
              class="flex items-center justify-center w-full px-6 py-3 text-base border-b border-gray-200 border-solid cursor-pointer text-primary-400 hover:bg-gray-200"
              @click="addPrefix"
            >
              <shopping-cart-icon class="h-5 mr-2" />
              {{ $t('corePbx.prefix_groups.add_prefix') }}
            </div>

            <div class="pt-8 py-2 flex flex-col md:flex-row md:space-x-4">
              <sw-button
                :loading="isLoading"
                variant="primary"
                type="submit"
                size="lg"
                class="w-full md:w-auto"
              >
                <save-icon class="mr-2 -ml-1" v-if="!isLoading" />
                {{
                  isEdit
                    ? $t('corePbx.prefix_groups.update_prefix_group')
                    : $t('corePbx.prefix_groups.save_prefix_group')
                }}
              </sw-button>

              <sw-button
                tag-name="router-link"
                :to="`/admin/corePBX/billing-templates/prefix-groups`"
                variant="primary-outline"
                type="button"
                size="lg"
                class="w-full md:w-auto mt-2 md:mt-0"
              >
                <x-circle-icon class="w-6 h-6 mr-1 -ml-2" v-if="!isLoading" />
                {{ $t('general.cancel') }}
              </sw-button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!--  Form  -->
    <div v-if="isEdit">
      <div class="grid grid-cols-12">
        <div class="col-span-12">
          <!-- desde aqui-->
          <form action="" @submit.prevent="submitPrefixGroup">
            <sw-input-group
              :label="$t('corePbx.prefix_groups.name')"
              :error="nameError"
              class="mb-4"
              required
            >
              <sw-input
                v-model.trim="formData.name"
                :invalid="$v.formData.name.$error"
                class="mt-2"
                focus
                type="text"
                name="name"
                @input="$v.formData.name.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('corePbx.prefix_groups.status')"
              class="mb-4"
            >
              <sw-select
                v-model="formData.status"
                :options="statuses"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('corePbx.prefix_groups.select_a_status')"
                class="mt-2"
                label="name"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('corePbx.prefix_groups.type')"
              class="mb-4"
              :error="typeError"
              required
            >
              <sw-select
                :invalid="$v.formData.type.$error"
                v-model="formData.type"
                :options="types"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('corePbx.prefix_groups.select_a_type')"
                class="mt-2"
                label="name"
                @input="$v.formData.type.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('corePbx.prefix_groups.description')"
              :error="descriptionError"
              class="mb-4"
            >
              <sw-textarea
                v-model="formData.description"
                rows="2"
                name="description"
                @input="$v.formData.description.$touch()"
              />
            </sw-input-group>
          </form>

          <!-- PREFIXES -->
          <p class="text-gray-500 uppercase sw-section-title">
            {{ $t('corePbx.prefix_groups.prefixes') }}
          </p>

          <br />

          <!-- Botones de filtro y add prefix-->

          <div class="float-right ml-4">
            <sw-dropdown
              style="
                padding-top: 7px;
                text-align: center;
                border: solid 1px black;
                width: 80px;
                height: 42px;
                border-radius: 10%;
              "
            >
              <span
                slot="activator"
                class="text-sm font-medium cursor-pointer select-none"
                style="font-size: 16px !important"
              >
                {{ $t('general.actions') }}
              </span>
              <sw-dropdown-item @click="modifySelected">
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                Modify Selected
              </sw-dropdown-item>
              <sw-dropdown-item @click="modifyAll">
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                Modify All
              </sw-dropdown-item>
            </sw-dropdown>
          </div>

          <div class="float-right">
            <sw-button
              v-if="!showFilters"
              class="ml-4"
              size="lg"
              variant="primary-outline"
              @click="addPrefixModal"
            >
              <plus-icon class="w-6 h-6 mr-1 -ml-2" />
              Add a prefix
            </sw-button>
          </div>

          <div class="float-right">
            <sw-button
              size="lg"
              variant="primary-outline"
              class="ml-4"
              @click="toggleFilter"
            >
              {{ $t('general.filter') }}
              <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
            </sw-button>
          </div>

          <div class="float-left">
            <select
              class="border border-dark"
              style="
                text-align: center;
                border: solid 1px black;
                width: 77px;
                height: 40px;
                border-radius: 10%;
              "
              v-model="selected"
              @change="refreshTable()"
            >
              <option
                style="text-align: center"
                v-for="option in options"
                :value="option.value"
              >
                {{ option.name }}
              </option>
            </select>
          </div>

          <br />

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
                    <sw-input v-model="filters.name"> </sw-input>
                  </sw-input-group>

                  <sw-input-group
                    v-if="filters.prefix_type.value == 'P'"
                    :label="'Prefix'"
                    class="mt-2"
                    style="min-width: 275px"
                  >
                    <sw-input v-model="filters.prefix"> </sw-input>
                  </sw-input-group>

                  <sw-input-group
                    v-if="filters.prefix_type.value == 'FT'"
                    :label="'From'"
                    class="mt-2"
                    style="min-width: 275px"
                  >
                    <sw-input v-model="filters.from"> </sw-input>
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
                    <sw-input v-model="filters.to"> </sw-input>
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

          <!-- desde aqui-->

          <div
            class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12"
          >
            <sw-checkbox
              v-model="selectAllFieldStatus"
              variant="primary"
              size="sm"
              class="hidden md:inline"
              @change="selectAllInternacionals"
            />

            <sw-checkbox
              v-model="selectAllFieldStatus"
              :label="$t('general.select_all')"
              variant="primary"
              size="sm"
              class="md:hidden"
              @change="selectAllInternacionals"
            />
          </div>

          <div>
            <sw-table-component
              ref="table"
              :show-filter="false"
              :data="fetchData"
              table-class="table"
            >
              <sw-table-column
                :sortable="false"
                :filterable="false"
                cell-class="no-click"
              >
                <div slot-scope="row" class="relative block">
                  <sw-checkbox
                    :id="row.id"
                    v-model="selectField"
                    :value="row.id"
                    variant="primary"
                    size="sm"
                  />
                </div>
              </sw-table-column>
              <sw-table-column :sortable="false" :label="'Name'" sort-as="name">
                <template slot-scope="row">
                  <p class="text-sm leading-5 text-black non-italic">
                    {{ row.name }}
                  </p>
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="false"
                :label="'Prefix'"
                sort-as="prefix"
              >
                <template slot-scope="row">
                  <p class="text-sm leading-5 text-black non-italic">
                    {{ row.prefix }}
                  </p>
                </template>
              </sw-table-column>

              <sw-table-column :sortable="false" :label="'From'" sort-as="from">
                <template slot-scope="row">
                  <p class="text-sm leading-5 text-black non-italic">
                    {{ row.from }}
                  </p>
                </template>
              </sw-table-column>

              <sw-table-column :sortable="false" :label="'To'" sort-as="to">
                <template slot-scope="row">
                  <p class="text-sm leading-5 text-black non-italic">
                    {{ row.to }}
                  </p>
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="false"
                :label="'Category'"
                sort-as="category"
              >
                <template slot-scope="row">
                  <p
                    v-if="row.category == 'C'"
                    class="text-sm leading-5 text-black non-italic"
                  >
                    Custom
                  </p>
                  <p
                    v-if="row.category == 'T'"
                    class="text-sm leading-5 text-black non-italic"
                  >
                    Toll Free
                  </p>
                  <p
                    v-if="row.category == 'I'"
                    class="text-sm leading-5 text-black non-italic"
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
                  <p class="text-sm leading-5 text-black non-italic">
                    {{ getCountry(row.country_id ? row.country_id : 0) }}
                  </p>
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="false"
                :label="'Rate'"
                sort-as="rate_per_minute"
              >
                <template slot-scope="row">
                  <p class="text-sm leading-5 text-black non-italic">
                    {{ defaultCurrency.symbol + ' ' + row.rate_per_minute }}
                  </p>
                </template>
              </sw-table-column>

              <!-- 3 puntos-->
              <sw-table-column :sortable="false" :filterable="false">
                <template slot-scope="row">
                  <span>{{ $t('invoices.action') }}</span>

                  <sw-dropdown>
                    <dot-icon slot="activator" />
                    <span>
                      <sw-dropdown-item @click="editPrefixModal(row)">
                        <pencil-icon class="h-5 mr-3 text-gray-600" />
                        Edit
                      </sw-dropdown-item>

                      <sw-dropdown-item @click="deletePrefix(row.id)">
                        <trash-icon class="h-5 mr-3 text-gray-600" />
                        Delete
                      </sw-dropdown-item>
                    </span>
                  </sw-dropdown>
                </template>
              </sw-table-column>
            </sw-table-component>
          </div>

          <!-- 
            <div
              class="flex items-center justify-center w-full px-6 py-3 text-base border-b border-gray-200 border-solid cursor-pointer text-primary-400 hover:bg-gray-200"
              @click="addPrefix"
            >
              <shopping-cart-icon class="h-5 mr-2" />
              {{ $t('corePbx.prefix_groups.add_prefix') }}
            </div>

            <div class="mt-6 mb-4">
              <sw-button
                :loading="isLoading"
                variant="primary"
                type="submit"
                size="lg"
                class="flex justify-center w-full md:w-auto"
              >
                <save-icon class="mr-2 -ml-1" v-if="!isLoading" />
                {{
                  isEdit
                    ? $t('corePbx.prefix_groups.update_prefix_group')
                    : $t('corePbx.prefix_groups.save_prefix_group')
                }}
              </sw-button>
            </div>
             -->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { maxLength, minLength, required } from 'vuelidate/lib/validators'
import draggable from 'vuedraggable'
import Prefix from './Prefix'
import Guid from 'guid'
import {
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
  TrashIcon,
  PlusIcon,
  FilterIcon,
  XIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
import { DotsHorizontalIcon } from '@vue-hero-icons/outline'
export default {
  components: {
    Prefix,
    draggable,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
    DotsHorizontalIcon,
    TrashIcon,
    PlusIcon,
    FilterIcon,
    XIcon,
    XCircleIcon,
  },
  data() {
    return {
      isSelected: false,
      isLoading: false,
      formData: {
        name: '',
        description: '',
        status: { name: 'Active', value: 'A' },
        type: {},
        prefixes: [
          {
            id: Guid.raw(),
            prefix_id: null,
            prefix_group_id: null,
            prefix: null,
            name: null,
            country_name: null,
            rate_per_minute: null,
          },
        ],
      },
      statuses: [
        { name: 'Active', value: 'A' },
        { name: 'Inactive', value: 'T' },
      ],
      types: [
        { name: 'Inbound', value: 'Inbound' },
        { name: 'Outbound', value: 'Outbound' },
      ],
      // DESDE AQUI
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
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
        maxLength: maxLength(120),
      },

      description: {
        maxLength: maxLength(65000),
      },

      type: {
        required,
      },
    },
  },
  computed: {
    ...mapGetters(['countries']),

    ...mapGetters('internacionalrate', [
      'selectAllField',
      'selectedInternacionals',
    ]),

    ...mapGetters('company', ['defaultCurrency']),

    selectField: {
      get: function () {
        return this.selectedInternacionals
      },
      set: function (val) {
        this.selectInternational(val)
      },
    },

    selectAllFieldStatus: {
      get: function () {
        return this.selectAllField
      },
      set: function (val) {
        this.isSelected = val
        this.setSelectAllState(val)
      },
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    pageTitle() {
      if (this.isEdit) {
        return this.$t('corePbx.prefix_groups.edit_prefix_group')
      } else if (this.isCopy) {
        return this.$t('corePbx.prefix_groups.copy_prefix_group')
      }
      return this.$t('corePbx.prefix_groups.new_prefix_group')
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }

      if (!this.$v.formData.name.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },

    descriptionError() {
      if (!this.$v.formData.description.$error) {
        return ''
      }

      if (!this.$v.formData.description.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },

    typeError() {
      if (!this.$v.formData.type.$error) {
        return ''
      }
      if (!this.$v.formData.type.required) {
        return this.$t('validation.required')
      }
    },

    isEdit() {
      return this.$route.name === 'corepbx.prefix-group.edit'
    },
    isCopy() {
      if (this.$route.name === 'corepbx.prefix-group.copy') {
        return true
      }
      return false
    },
  },

  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },

  destroyed() {
    if (this.selectAllField) {
      this.selectAllInternacionals()
    }
  },

  created() {
    if (this.isEdit || this.isCopy) {
      this.loadEditPrefixGroup()
    }
    this.fetchInternacionals()
  },
  mounted() {
    this.$v.formData.$reset()
  },
  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('internacionalrate', [
      'fetchPrefixInternational',
      'fetchInternacionals',
      'deletePrefixInternational',
      'setSelectedState',
      'setSelectAllState',
      'selectAllInternacionals',
      'selectInternational',
    ]),
    ...mapActions('prefixGroup', [
      'addPrefixGroup',
      'fetchPrefixGroup',
      'updatePrefixGroup',
    ]),

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
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchPrefixInternational(data)
      this.isRequestOngoing = false

      return {
        data: response.data.international_rate.data,
        pagination: {
          totalPages: response.data.international_rate.last_page,
          currentPage: page,
        },
      }
    },

    async submitForm() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      try {
        let response
        response = await this.updatePrefixGroup(this.formData)
        if (response.data.success) {
          window.toastr['success'](
            this.$tc('corePbx.prefix_groups.created_message')
          )
          this.$router.push('/admin/corePBX/billing-templates/prefix-groups')
        }
        if (response.data.error) {
          this.isLoading = false
          window.toastr['error'](response.data.error)
          return true
        }
      } catch (err) {
        this.isLoading = false
      }
    },

    addPrefixModal() {
      this.openModal({
        title: 'Add a prefix',
        componentName: 'Prefix',
        refreshData: this.$refs.table.refresh,
      })
    },

    editPrefixModal(data) {
      this.openModal({
        title: 'Edit a Prefix',
        componentName: 'Prefix',
        id: data.id,
        data: data,
        refreshData: this.$refs.table.refresh,
      })
    },

    deletePrefix(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('You will not be able to recover this prefix'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let data = {
            id_prefix_group: this.$route.params.id,
            id_prefix: id,
          }
          let res = await this.deletePrefixInternational(data)
          window.toastr['success'](this.$t(res.data.message))
          this.$refs.table.refresh()
          return true
        }
      })
    },

    async modifySelected() {
      let data = {
        type: 'Selected',
        id: this.$route.params.id,
      }

      if (this.selectField.length > 0) {
        this.openModal({
          title: 'Modify Selected',
          componentName: 'PrefixModify',
          refreshData: this.$refs.table.refresh,
          data: data,
        })
      } else {
        window.toastr['error'](this.$tc('general.actions_modify_selected'))
      }
    },
    async modifyAll() {
      let data = {
        type: 'All',
        id: this.$route.params.id,
      }

      if (this.selectAllFieldStatus) {
        this.openModal({
          title: 'Modify All',
          componentName: 'PrefixModify',
          refreshData: this.$refs.table.refresh,
          data: data,
        })
      } else {
        window.toastr['error'](this.$tc('general.actions_modify_all'))
      }
    },

    setFilters() {
      clearTimeout(this.timeout)
      this.timeout = setTimeout(() => {
        this.refreshTable()
      }, 900)
    },

    getCountry(id) {
      let country = this.countries.filter((country) => country.id == id)
      return country.length > 0 ? country[0].name : 'None'
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

    addPrefix() {
      this.formData.prefixes.push({
        id: Guid.raw(),
        prefix_id: null,
        prefix_group_id: null,
        prefix: null,
        name: null,
        country_name: null,
        rate_per_minute: null,
      })
    },

    async loadEditPrefixGroup() {
      let response = await this.fetchPrefixGroup(this.$route.params.id)

      if (response.data) {
        let prefix_group = response.data.prefixGroup
        this.formData = {
          ...this.formData,
          ...prefix_group,
          prefixes: prefix_group.prefix_group.map((prfGr) => {
            return {
              ...prfGr,
              prefix_id: prfGr.id,
              country_name:
                this.countries.filter(
                  (country) => country.id == prfGr.country_id
                ).length > 0
                  ? this.countries.filter(
                      (country) => country.id == prfGr.country_id
                    )[0].name
                  : 'N/A',
            }
          }),
        }

        this.formData.status = this.statuses.find(
          (_status) => prefix_group.status === _status.value
        )

        this.formData.type = this.types.find(
          (_type) => prefix_group.type === _type.value
        )
      }
    },

    checkExistPrefix(index, newPrefix) {
      let pos = this.formData.prefixes.findIndex(
        (_prefix) => _prefix.prefix_id === newPrefix.id
      )
      if (pos !== -1) {
        this.formData.prefixes.splice(index, 1)
      }
    },

    updatePrefix(data) {
      this.formData.prefixes[data.index] = data.prefix
    },

    removePrefix(index) {
      this.formData.prefixes.splice(index, 1)
    },

    async submitPrefixGroup() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      try {
        let response
        this.isLoading = true
        if (this.isEdit) {
          response = await this.updatePrefixGroup(this.formData)
          if (response.data.success) {
            window.toastr['success'](
              this.$t('corePbx.prefix_groups.updated_message')
            )
            this.$router.push('/admin/corePBX/billing-templates/prefix-groups')
          }
          if (response.data.error) {
            this.isLoading = false
            window.toastr['error'](response.data.error)
            return true
          }
        } else {
          response = await this.addPrefixGroup(this.formData)
          if (response.data.success) {
            window.toastr['success'](
              this.$tc('corePbx.prefix_groups.created_message')
            )
            this.$router.push('/admin/corePBX/billing-templates/prefix-groups')
          }
          if (response.data.error) {
            this.isLoading = false
            window.toastr['error'](response.data.error)
            return true
          }
        }
      } catch (err) {
        this.isLoading = false
      }
    },
  },
}
</script>

<style scoped>
</style>