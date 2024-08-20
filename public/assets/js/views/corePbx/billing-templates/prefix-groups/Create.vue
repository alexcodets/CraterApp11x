<template>
  <div class="relative">
    <!-- Page Header -->
    <sw-page-header :title="pageTitle" class="mb-3">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('corePbx.corePbx')" to="/admin/corePBX"/>
        <sw-breadcrumb-item :title="$tc('corePbx.prefix_groups.prefix_group', 2)" to="/admin/corePBX/billing-templates/prefix-groups"/>
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
          tag-name="router-link"
          :to="`/admin/corePBX/billing-templates/prefix-groups`"
          class=""
          variant="primary-outline"
        >
          {{ $t('general.cancel') }}
        </sw-button>
      </template>
    </sw-page-header>

    <!--  Form  -->
    <div class="grid grid-cols-12">
      <div class="col-span-12">
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
          <label class="text-sm not-italic font-medium leading-5 text-primary-800 text-sm">
            {{ $t('corePbx.prefix_groups.prefixes') }}
          </label>

          <table class="w-full text-center item-table mt-2">
            <colgroup>
              <col style="width: 30%" />
              <col style="width: 20%" />
              <col style="width: 30%" />
              <col style="width: 20%" />
            </colgroup>
            <thead class="bg-white border border-gray-200 border-solid">
              <th class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid">
                <span class="pl-12">
                    {{ $t('corePbx.prefix_groups.prefix_name') }}
                </span>
              </th>
              <th class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid">
                {{ $t('corePbx.prefix_groups.prefix') }}
              </th>
              <th class="px-5 py-3 text-sm not-italic font-medium leading-5 text-center text-gray-700 border-t border-b border-gray-200 border-solid">
                {{ $t('corePbx.prefix_groups.country') }}
              </th>
              <th class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid">
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
        </form>
      </div>
    </div>
  </div>
</template>

<script>

import { mapActions, mapGetters } from 'vuex'
import {maxLength, minLength, required} from "vuelidate/lib/validators";
import draggable from 'vuedraggable'
import Prefix from './Prefix'
import Guid from 'guid'
import {
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    Prefix,
    draggable,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
  },
  data() {
    return {
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
            country_name: null
          }
        ]
      },
      statuses: [
        { name: 'Active', value: 'A' },
        { name: 'Inactive', value: 'T' },
      ],
      types: [
        { name: 'Inbound', value: 'Inbound' },
        { name: 'Outbound', value: 'Outbound' },
      ],
    }
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
        maxLength: maxLength(120)
      },

      description: {
        maxLength: maxLength(65000)
      },

      type: {
        required
      }
    },
  },
  computed: {
    ...mapGetters(['countries']),
    pageTitle() {
      if (this.isEdit) {
        return this.$t('corePbx.prefix_groups.edit_prefix_group')
      }else if (this.isCopy) {
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
      return this.$route.name === 'corepbx.prefix-group.edit';
    },
    isCopy() {
      if (this.$route.name === 'corepbx.prefix-group.copy') {
        return true
      }
      return false
    },
  },
  created() {
    if (this.isEdit || this.isCopy) {
      this.loadEditPrefixGroup()
    }
    this.fetchInternacionals()
  },
  mounted() {
    this.$v.formData.$reset();
  },
  methods: {
    ...mapActions('internacionalrate', ['fetchInternacionals']),
    ...mapActions('prefixGroup', [
      'addPrefixGroup',
      'fetchPrefixGroup',
      'updatePrefixGroup'
    ]),

    addPrefix() {
      this.formData.prefixes.push({
        id: Guid.raw(),
        prefix_id: null,
        prefix_group_id: null,
        prefix: null,
        name: null,
        country_name: null
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
              country_name: this.countries.filter((country)=>country.id==prfGr.country_id).length>0 ? (this.countries.filter((country)=>country.id==prfGr.country_id))[0].name : 'N/A'
            }
          })
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
      let pos = this.formData.prefixes.findIndex( (_prefix) => _prefix.prefix_id === newPrefix.id )
      if (pos !== -1) {
        this.formData.prefixes.splice(index, 1);
      }
    },

    updatePrefix(data) {
      Object.assign(this.formData.prefixes[data.index], { ...data.prefix })
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
        let response;
        this.isLoading = true;
        if (this.isEdit) {
          response = await this.updatePrefixGroup(this.formData)
          if (response.data.success) {
            window.toastr['success'](this.$t('corePbx.prefix_groups.updated_message'));
            this.$router.push('/admin/corePBX/billing-templates/prefix-groups');
          }
          if (response.data.error) {
            this.isLoading = false;
            window.toastr['error'](response.data.error);
            return true;
          }
        } else {
          response = await this.addPrefixGroup(this.formData);
          if (response.data.success) {
            window.toastr['success'](this.$tc('corePbx.prefix_groups.created_message'));
            this.$router.push('/admin/corePBX/billing-templates/prefix-groups');
          }
          if (response.data.error) {
            this.isLoading = false;
            window.toastr['error'](response.data.error);
            return true;
          }
        }

      } catch (err) {
        this.isLoading = false;
      }
    }
  }
}
</script>

<style scoped>

</style>