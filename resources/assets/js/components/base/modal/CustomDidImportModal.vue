<template>
  <div class="custom-destination">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <form action="" @submit.prevent="'submitData'">

      <!--  Carga de datos  -->
      <div v-if="!showPreview">
        <div class="py-4 px-6 form-group-row">
          <sw-input-group
            :label="$t('general.file')"
            class="mb-4 md:col-span-2"
            variant="horizontal"
            :error="fileError"
            required
          >
            <div class="flex"  role="group">
              <sw-input
                :invalid="$v.formData.file.$error || !this.isValidFile"
                type="file"
                name="file"
                ref="file"
                @change="onFileChange"
                @input="$v.formData.file.$touch()"
              />
              <a
                class="font-medium text-primary-500 ml-6 cursor-pointer"
                @click="downloadTemplate"
              >
              {{ $t('expenses.download_exmaple') }}
              </a>
            </div>
          </sw-input-group>

          <sw-input-group
            variant="horizontal"
            class="font-normal md:col-span-2"
          >
            <sw-radio
              :label="$t('corePbx.custom_did_groups.import_to_existing_group')"
              v-model="formData.type_group"
              size="sm"
              name="filter"
              value="existing_group"
              @change="'onSearch'"
            />
          </sw-input-group>

          <div v-if="formData.type_group === 'existing_group'">
            <sw-input-group
              class="my-4"
              variant="horizontal"
              :error="customError"
              required
            >
              <sw-select
                :invalid="$v.formData.prefixrate_groups_id.$error"
                v-model="formData.prefixrate_groups_id"
                :options="getCustomDidGroups"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :max-height="200"
                :placeholder="$t('corePbx.custom_did_groups.select_a_custom_did_group')"
                label="name"
                @input="$v.formData.prefixrate_groups_id.$touch()"
              />
            </sw-input-group>
          </div>

          <sw-input-group
            variant="horizontal"
            class="font-normal"
          >
            <sw-radio
              :label="$t('corePbx.custom_did_groups.import_to_new_group')"
              v-model="formData.type_group"
              size="sm"
              name="filter"
              value="new_group"
              @change="'onSearch'"
            />
          </sw-input-group>

          <div v-if="formData.type_group === 'new_group'">
            <sw-input-group
              :error="nameError"
              class="my-4"
              variant="horizontal"
              required
            >
              <sw-input
                v-model.trim="formData.name"
                :invalid="$v.formData.name.$error"
                class="mt-2"
                :placeholder="$t('corePbx.custom_did_groups.name')"
                type="text"
                name="name"
                @input="$v.formData.name.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              class="mb-4"
              :error="typeError"
              variant="horizontal"
              required
            >
              <sw-select
                :invalid="$v.formData.type.$error"
                v-model="formData.type"
                :options="types"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$t('corePbx.custom_did_groups.select_a_type')"
                class="mt-2"
                label="name"
                @input="$v.formData.type.$touch()"
              />
            </sw-input-group>
          </div>

        </div>

        <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
          <sw-button
            class="mr-3"
            variant="primary-outline"
            type="button"
            @click="closeImportModal"
          >
            {{ $t('general.cancel') }}
          </sw-button>
          <sw-button
            variant="primary"
            type="button"
            @click="uploadFile"
          >
            {{ $t('general.next') }}
            <arrow-right-icon class="h-5 ml-2 -mr-1" />
          </sw-button>
        </div>
      </div>

      <!--  Asociacion de columnas  -->
      <div v-if="showPreview">
        <div class="py-4 px-6 form-group-row">
          <label class="text-sm not-italic font-medium leading-5 text-primary-800 text-sm">
            {{ $t('corePbx.custom_did_groups.column_matching') }}
          </label>

          <table class="w-full text-center item-table mb-5 mt-2">
            <thead class="bg-gray-400 border border-gray-200 border-solid">
              <th
                v-for="(field, key) in table_headers"
                :key="key"
                class="px-5 py-3 text-center text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span class="">{{ field }}</span>
              </th>
            </thead>
            <tbody>
              <tr v-for="(row, index) in view_data">
                <td
                  v-for="(value, i) in row"
                  class="px-5 py-3 text-center align-top border-b border-gray-200 border-solid"
                >
                  {{ value }}
                </td>
              </tr>
              <tr>
                <td
                  v-for="(field, key) in table_headers"
                  :key="key"
                  class="px-5 py-4 text-left align-top"
                >
                  <sw-select
                    ref="field_selects"
                    :invalid="$v.field_selects.$each[key].value.$error"
                    v-model="field_selects[key]"
                    :options="db_fields"
                    :searchable="true"
                    :show-labels="false"
                    :allow-empty="true"
                    :max-height="200"
                    :placeholder="$t('general.select_an_option')"
                    label="name"
                    @input="$v.field_selects.$each[key].value.$touch()"
                    @select="(item) => matchColumn(item, field)"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
          <sw-button
            class="mr-3"
            variant="primary-outline"
            type="button"
            @click="back"
          >
            <arrow-left-icon class="h-5 mr-2 -ml-1" />
            {{ $t('general.back') }}
          </sw-button>
          <sw-button
            variant="primary"
            type="button"
            @click="submitData"
          >
            <save-icon class="mr-2"/>
            {{ $t('general.save') }}
          </sw-button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { ArrowRightIcon, ArrowLeftIcon } from '@vue-hero-icons/solid'
import * as types from "../../../store/modules/Logs/mutation-types";
const {
  required,
  minLength,
  numeric,
  maxLength,
  minValue,
  requiredIf
} = require('vuelidate/lib/validators')

export default {
  components: {
    ArrowRightIcon,
    ArrowLeftIcon
  },
  data() {
    return {
      isLoading: false,
      isRequestOnGoing: false,
      preview: false,
      formData: {
        file: null,
        type_group: 'existing_group',
        prefixrate_groups_id: null,
        name: null,
        type: null,
      },
      types: [
        { name: 'International', value: 'IN' },
        { name: 'Local', value: 'LO' },
        { name: 'Toll free', value: 'TF' },
      ],
      parse_header: [],
      parse_csv: [],
      sortOrders: {},
      sortKey: '',
      table_headers: null,
      db_fields: [],
      field_selects: [],
      paired_columns: {},
      view_data: [],
    }
  },
  filters: {
    capitalize: function (str) {
      return str.charAt(0).toUpperCase() + str.slice(1)
    }
  },
  validations: {
    formData: {
      file: {
        required
      },
      prefixrate_groups_id: {
        required: requiredIf(function () {
          return this.formData.type_group === 'existing_group'
        }),
      },
      name: {
        required: requiredIf(function () {
          return this.formData.type_group === 'new_group'
        }),
        minLength: minLength(3),
        maxLength: maxLength(120),
      },
      type: {
        required: requiredIf(function () {
          return this.formData.type_group === 'new_group'
        }),
      },
    },
    field_selects: {
      $each: {
        name: {
          required,
        },
        value: {
          required,
        },
      }
    }
  },
  computed: {
    ...mapGetters('customDidGroup', ['customDidGroups']),

    showPreview() {
      return this.preview
    },

    getCustomDidGroups() {
      return this.customDidGroups.map((group) => {
        return {
          ...group,
        }
      })
    },

    isValidFile() {
      if (!this.formData.file)
        return true

      let fileName = this.formData.file.name;
      let allowedExtensions = /(\.csv|\.xls|\.xlsx)$/i;

      if (!allowedExtensions.exec(fileName)) {
        return false;
      }
      return true
    },

    fileError() {
      if (!this.$v.formData.file.$error && this.isValidFile) {
        return ''
      }
      if (!this.$v.formData.file.required) {
        return this.$t('validation.required')
      }
      if (!this.isValidFile) {
        return this.$t('validation.allowed_extensions')
      }
    },

    customError() {
      if (!this.$v.formData.prefixrate_groups_id.$error) {
        return ''
      }
      if (!this.$v.formData.prefixrate_groups_id.required) {
        return this.$tc('validation.required')
      }
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

    typeError() {
      if (!this.$v.formData.type.$error) {
        return ''
      }
      if (!this.$v.formData.type.required) {
        return this.$t('validation.required')
      }
    },
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('customDidGroup' , ['importParse', 'importProcess']),

    sortBy: function (key) {
      let vm = this
      vm.sortKey = key
      vm.sortOrders[key] = vm.sortOrders[key] * -1
    },

    onFileChange() {
      //console.log(this.$refs.file.$refs.baseInput.files[0])
      this.formData.file = this.$refs.file.$refs.baseInput.files[0]
    },

    csvJSON(csv) {
      let vm = this
      let lines = csv.split("\n")
      let result = []
      let headers = lines[0].split(",")
      vm.parse_header = lines[0].split(",")
      lines[0].split(",").forEach(function (key) {
        vm.sortOrders[key] = 1
      })

      lines.map(function (line, indexLine) {
        if (indexLine < 1)
          return // Jump header line

        let obj = {}
        let currentLine = line.split(",")

        headers.map(function (header, indexHeader) {
          obj[header] = currentLine[indexHeader]
        })

        result.push(obj)
      })

      result.pop() // remove the last item because undefined values
      return result // JavaScript Object
    },

    loadCSV() {
      this.$v.formData.$touch()
      if (this.$v.formData.$invalid || !this.isValidFile) {
        return true
      }

      let vm = this
      if (window.FileReader) {
        let reader = new FileReader()
        reader.readAsText(this.formData.file)
        // Handle errors load
        reader.onload = function (event) {
          let csv = event.target.result
          vm.parse_csv = vm.csvJSON(csv)
        }
        reader.onerror = function (evt) {
          if (evt.target.error.name == "NotReadableError") {
            alert("Can't read file!");
          }
        }
      } else {
        alert("FileReader are not supported in this browser.");
      }
    },

    async uploadFile() {
      this.$v.formData.$touch()
      if (this.$v.formData.$invalid || !this.isValidFile) {
        return true
      }

      let datafile = new FormData()

      datafile.append(
        'import_data',
        this.formData.file
      )

      let response = await this.importParse(datafile)
      //console.log(response.data)
      if (response.data.success) {
        this.table_headers = response.data.header
        this.view_data = response.data.view_data

        for (let i in response.data.db_fields) {
          this.db_fields.push({
            name: response.data.db_fields[i],
            value: i
          })
        }

        for (let index in this.table_headers) {
          this.field_selects.push({
            name: '',
            value: ''
          })
        }

        this.preview = true
      }
    },

    closeImportModal() {
      this.resetFormData()
      this.closeModal()
      this.resetModalData()
    },

    back() {
      this.table_headers = null
      this.db_fields = []
      this.field_selects = []
      this.paired_columns = {}
      this.preview = false
    },

    matchColumn(item, field) {
      let index = this.field_selects.findIndex( (_fld) => _fld && _fld.value ===  item.value )

      if (index >= 0) {
        this.field_selects[index] = { name: '', value: '' }
        //delete this.paired_columns[item.value]
      }
      this.paired_columns[item.value] = field
      //console.log(this.paired_columns)
    },

    async submitData() {
      this.$v.field_selects.$each.$touch()
      if (this.$v.field_selects.$each.$invalid) {
        return true
      }

      let data = new FormData()

      data.append('formData', JSON.stringify(this.formData))
      data.append('paired_columns', JSON.stringify(this.paired_columns))
      data.append('import_data', this.formData.file)

      try {
        this.isRequestOnGoing = true
        let response = await this.importProcess(data)
        if (response.data.success) {
          window.toastr['success'](this.$tc('corePbx.custom_did_groups.load_file_message'))
          window.hub.$emit('newLoad')
          this.isRequestOnGoing = false
          this.resetModalData()
          this.resetFormData()
          this.closeModal()
          return true
        }
        window.toastr['error'](response.data.error)
      } catch (err) {
        this.isRequestOnGoing = false
      }

    },

    resetFormData() {
      this.formData = {
        file: null,
        type_group: 'existing_group',
        prefixrate_groups_id: null,
        name: null,
        type: null,
      }
      this.$v.$reset()
    },

    async downloadTemplate() {
      window.axios
        .get(`/api/v1/custom-did-groups/export-process`, {responseType: 'arraybuffer'})
        .then((response) => {
          let fileUrl = window.URL.createObjectURL(new Blob([response.data]))
          let fileLink = document.createElement('a')
          fileLink.href = fileUrl
          fileLink.setAttribute('download', 'CustomDid.xlsx')
          document.body.appendChild(fileLink)
          fileLink.click()
          fileLink.remove()
        })
    }

  }
}
</script>

<style scoped>

</style>