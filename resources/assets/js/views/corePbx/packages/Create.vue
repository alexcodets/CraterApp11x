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
  <!-- Base  -->
  <base-page class="item-create">
    <!-- Form  -->
    <form action="" @submit.prevent="submitPackage">
      <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />

      <!-- page header -->
      <sw-page-header :title="pageTitle">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$tc('corePbx.menu_title.packages', 2)"
            to="/admin/corePBX/packages"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'corepbx.packages.edit'"
            :title="$t('packages.edit_package')"
            to="#"
            active
          />
          <sw-breadcrumb-item
            v-else-if="$route.name === 'corepbx.packages.copy'"
            :title="$t('packages.copy_package')"
            to="#"
            active
          />
          <sw-breadcrumb-item
            v-else
            :title="$t('packages.new_package')"
            to="#"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>

      <!-- Cuerpo -->
      <sw-card>
        <!-- Basic  -->

        <h6 class="col-span-5 sw-section-title lg:col-span-1">
          {{ $t('packages.basic') }}
        </h6>

        <sw-divider class="col-span-12" />
        <!-- NAME PACKAGE -->
        <h1 class="col-span-12" v-text="$t('packages.lang_name')"></h1>

        <br />
        <!-- camppos priemra fila  -->
        <div class="container mx-auto">
          <!-- Fila para los input-groups -->
          <div class="flex flex-col md:flex-row md:-mx-2">
            <!-- Input Group 1 -->
            <div class="w-full md:w-1/3 md:px-2 mb-4 md:mb-0">
              <sw-input-group
                :label="$t('packages.name_package')"
                :error="nameError"
                required
              >
                <sw-input
                  v-model="formData.pbx_package_name"
                  :invalid="$v.formData.pbx_package_name.$error"
                  focus
                  type="text"
                  name="pbx_package_name"
                  :tabindex="1"
                  @input="$v.formData.pbx_package_name.$touch()"
                />
              </sw-input-group>
            </div>
            <!-- Input Group 2 -->
            <div class="w-full md:w-1/3 md:px-2 mb-4 md:mb-0">
              <sw-input-group
                :label="$t('corePbx.packages.type')"
                :error="statusPaymentError"
                required
              >
                <sw-select
                  v-model.trim="formData.status_payment"
                  :options="status_payment"
                  :invalid="$v.formData.status_payment.$error"
                  :searchable="true"
                  :show-labels="false"
                  :tabindex="2"
                  :disabled="bandServices"
                  :allow-empty="true"
                  :placeholder="$t('general.select_status')"
                  label="text"
                  track-by="value"
                  @input="$v.formData.status_payment.$touch()"
                />
              </sw-input-group>
            </div>
            <!-- Input Group 3 -->
            <div class="w-full md:w-1/3 md:px-2">
              <sw-input-group
                :label="$t('packages.status')"
                :error="statusError"
                required
              >
                <sw-select
                  v-model="formData.status"
                  :invalid="$v.formData.status.$error"
                  :options="status"
                  :searchable="true"
                  :show-labels="false"
                  :tabindex="3"
                  :allow-empty="true"
                  :placeholder="$t('general.select_status')"
                  label="text"
                  track-by="value"
                />
              </sw-input-group>
            </div>
          </div>
        </div>

        <!-- camppos priemra fila  -->

        <br />

        <!-- camppos segunda fila  -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="col-span-1 md:col-span-1">
            <!-- ... contenido del servidor pbx ... -->

            <sw-input-group
              :label="$t('corePbx.packages.dropdown_server')"
              :error="dropdownError"
              class="md:col-span-3"
              required
            >
              <sw-select
                v-model="formData.dropdown_server"
                :options="dropdown_server"
                :invalid="$v.formData.dropdown_server.$error"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :tabindex="4"
                :disabled="bandServices"
                :placeholder="$t('corePbx.packages.select_server')"
                label="server_label"
                track-by="id"
                @select="DropServerSeleted"
                @input="$v.formData.dropdown_server.$touch()"
              />
            </sw-input-group>

            <div v-if="formData.dropdown_server">
              <span
                v-if="formData.dropdown_server.status == 'A'"
                class="text-success"
                >{{ $t('settings.customization.modules.server_online') }}</span
              >
              <span v-else class="text-danger">{{
                $t('settings.customization.modules.server_offline')
              }}</span>
            </div>
          </div>

          <!-- select con checklist -->
          <div class="col-span-1 md:col-span-1">
            <!-- ... contenido del select con checklist ... -->

            <sw-input-group
              :label="$t('corePbx.packages.status_cdrs')"
              class="md:col-span-3 mb-4"
            >
              <sw-select :options="[]" :searchable="false" placeholder="">
                <template v-slot:selection>
                  <p>{{ labelStatusCdr }}</p>
                </template>
                <!-- template beforeList -->
                <template v-slot:beforeList>
                  <div class="flex flex-wrap">
                    <sw-checkbox
                      v-for="(item, index) in cdrStatusOptions"
                      :key="index"
                      v-model="formData.cdrStatus"
                      :variant="item.color"
                      :label="item.label"
                      :value="item.value"
                      class="w-full p-2 px-4 hover:bg-gray-100"
                      @change="changeStatus"
                    />
                  </div>
                </template>
              </sw-select>
            </sw-input-group>
          </div>

          <!-- RATE -->
          <div class="col-span-1 md:col-span-1">
            <!-- ... contenido del RATE ... -->
            <sw-input-group
              :label="$t('corePbx.packages.price')"
              :error="rateError"
              class="md:col-span-3"
            >
              <sw-money
                v-model="formData.rate"
                :currency="customerCurrency"
                :invalid="$v.formData.rate.$error"
                @input="$v.formData.rate.$touch()"
                name="rate"
                tabindex="5"
              />
            </sw-input-group>
          </div>

          <!-- apply_tax_type -->
          <div class="col-span-1 md:col-span-1">
            <!-- ... contenido del apply_tax_type ... -->
            <sw-input-group
              :label="$t('corePbx.packages.apply_tax_type')"
              class="md:col-span-3"
            >
              <sw-select
                ref="baseSelect"
                v-model="formData.apply_tax_type"
                :options="apply_tax_type_options"
                :searchable="true"
                :show-labels="false"
                :allow-empty="false"
                class="mt-1"
                label="text"
                track-by="value"
              />
            </sw-input-group>
          </div>
        </div>
        <!-- camppos Segunda fila  -->

        <br />

        <!-- camppos tercera fila  -->

        <div class="flex flex-col md:flex-row">
          <div class="md:w-1/2">
            <!-- Primera etiqueta -->
            <div>
              <sw-input-group
                :label="$t('didFree.item.custom_destination_groups')"
                class="relative"
                style="min-width: 300px"
              >
                <sw-select
                  v-model="prefixrate_groups_id"
                  :options="prefixrate_groups"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :placeholder="$t('didFree.item.custom_destination_groups')"
                  label="name"
                  track-by="id"
                  :tabindex="10"
                  :multiple="true"
                ></sw-select>
              </sw-input-group>
            </div>
          </div>

          <div class="md:w-1/2">
            <!-- Segunda etiqueta -->
            <div>
              <sw-input-group
                :label="$t('didFree.item.custom_destination_groups_outbound')"
                class="relative"
                style="min-width: 300px"
              >
                <sw-select
                  v-model="prefixrate_groups_outbound_id"
                  :options="prefixrate_groups_outboubd"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :placeholder="
                    $t('didFree.item.custom_destination_groups_outbound')
                  "
                  label="name"
                  track-by="id"
                  :tabindex="10"
                  :multiple="true"
                ></sw-select>
              </sw-input-group>
            </div>
          </div>
        </div>

        <!-- camppos tercera fila  -->

        <br />

        <!-- camppos caurto fila  -->
        <div class="w-full md:w-1/2">
          <sw-input-group
            :label="$tc('corePbx.submenu_title.custom_app_rate')"
            class="relative"
            style="min-width: 300px"
          >
            <span
              v-if="formData.custom_app_rate"
              class="absolute text-gray-400 cursor-pointer"
              style="top: 55%; right: 5%; z-index: 999999"
              @click="formData.custom_app_rate = null"
            >
              <x-circle-icon class="h-5" />
            </span>
            <sw-select
              v-model="formData.custom_app_rate"
              :options="customAppRateOptions"
              :searchable="true"
              :show-labels="false"
              :allow-empty="false"
              :placeholder="$t('corePbx.submenu_title.custom_app_rate')"
              label="name"
              track-by="id"
              :tabindex="7"
            ></sw-select>
          </sw-input-group>
        </div>

        <!-- camppos cuarta fila  -->

        <div class="grid grid-cols-5 gap-4 mb-8">
          <div class="grid col-span-12 gap-y-6 gap-x-4 md:grid-cols-6">
            <div class="flex mb-5 col-span-12"></div>

            <div class="flex mb-5 col-span-12">
              <div class="ml-4"></div>
            </div>

            <!-- DESCRIPTION -->
            <div class="tabs mb-5 grid col-span-12">
              <div class="border-b tab">
                <div class="border-l-2 border-transparent relative">
                  <input
                    class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-6"
                    type="checkbox"
                    id="chck1"
                  />
                  <header
                    class="col-span-5 flex justify-between items-center p-3 pl-0 pr-8 cursor-pointer select-none tab-label"
                    for="chck1"
                  >
                    <span class="text-grey-darkest font-thin text-xl">
                      {{ $t('general.description') }}
                    </span>
                    <div
                      class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test"
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
                            <sw-tab-item title="HTML">
                              <base-custom-input
                                v-model="formData.html"
                                :fields="[]"
                              />
                            </sw-tab-item>
                            <sw-tab-item title="Text">
                              <base-custom-input
                                v-model="formData.text"
                                :fields="[]"
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
            <!--DROP-->
            <div class="flex mb-5 col-span-12"></div>
            <!-- switches 1 -->
            <div class="flex mt-2 col-span-12">
              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('corePbx.packages.automatic_suspension') }}
                </p>
                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('corePbx.packages.automatic_suspension') }}
                </p>
              </div>
              <div class="relative w-12">
                <sw-switch
                  v-model="automatic_suspension"
                  class="absolute"
                  style="top: -20px"
                />
              </div>
              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('corePbx.packages.unmetered') }}
                </p>
                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('corePbx.packages.unmetered') }}
                </p>
              </div>
              <div class="relative w-12">
                <sw-switch
                  v-model="unmetered"
                  class="absolute"
                  style="top: -20px"
                />
              </div>
              <div v-if="isAvalaraAvailable" class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('corePbx.packages.avalara_options') }}
                </p>
                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('corePbx.packages.avalara_options') }}
                </p>
              </div>
              <div v-if="isAvalaraAvailable" class="relative w-12">
                <sw-switch
                  v-model="avalara_options"
                  class="absolute"
                  style="top: -20px"
                  @input="avalaraOptionsChange"
                />
              </div>
              <!-- ALL CDRS  -->
              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('corePbx.packages.all_cdrs') }}
                </p>

                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('corePbx.packages.all_cdrs') }}
                </p>
              </div>
              <div class="relative w-12">
                <sw-switch
                  v-model="all_cdrs"
                  class="absolute"
                  style="top: -20px"
                />
              </div>
            </div>
            <!-- SWITCHES -->
            <div class="flex mt-2 col-span-12">
              <!-- EXTENSIONS-->
              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('corePbx.packages.extensions') }}
                </p>

                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('corePbx.packages.extensions') }}
                </p>
              </div>
              <div class="relative w-12">
                <sw-switch
                  v-model="extensions"
                  class="absolute"
                  style="top: -20px"
                />
              </div>

              <!--DID-->
              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('corePbx.packages.did') }}
                </p>
                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('corePbx.packages.did') }}
                </p>
              </div>
              <div class="relative w-12">
                <sw-switch v-model="did" class="absolute" style="top: -20px" />
              </div>
              <!--Fixed Server / Modify Server-->
              <!-- <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('corePbx.packages.fixed_server') }}
                </p>

                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('corePbx.packages.fixed_server') }}
                </p>
              </div>
              <div class="relative w-12">
                <sw-switch
                  v-model="modify_server"
                  class="absolute"
                  style="top: -20px"
                />
              </div> -->
              <!--Call Ratings-->
              <div v-if="!unmetered" class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('corePbx.packages.call_ratings') }}
                </p>

                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('corePbx.packages.call_ratings') }}
                </p>
              </div>
              <div v-if="!unmetered" class="relative w-12">
                <sw-switch
                  v-model="call_ratings"
                  class="absolute"
                  style="top: -20px"
                  @change="slideChange"
                />
              </div>
            </div>

            <div class="flex mt-2 col-span-12">
              <!--Update Child Services-->
              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('corePbx.packages.update_child_services') }}
                </p>

                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('corePbx.packages.update_child_services') }}
                </p>
              </div>
              <div class="relative w-12">
                <sw-switch
                  v-model="update_child_services"
                  class="absolute"
                  style="top: -20px"
                  @change="slideChange"
                />
              </div>
            </div>

            <!-- Avalara Options -->
            <div class="col-span-12" v-if="avalara_options">
              <div class="flex mb-5 col-span-12"></div>
              <h6 class="sw-section-title col-span-12">
                {{ $t('corePbx.packages.avalara_options') }}
              </h6>

              <div
                class="flex mt-2 col-span-12 grid grid-cols-6 gap-4 items-start"
              >
                <!-- avalara_bundle -->
                <div class="col-span-6 flex grid grid-cols-6 items-start">
                  <div class="col-span-1">
                    <p
                      class="p-0 mb-1 text-base leading-snug text-black box-title"
                    >
                      {{ $t('corePbx.packages.avalara_bundle') }}
                    </p>
                    <p class="p-0 m-0 text-xs leading-4 text-gray-500">
                      {{ $t('corePbx.packages.avalara_bundle') }}
                    </p>
                  </div>
                  <div class="col-span-1 relative">
                    <sw-switch
                      v-model="formData.avalaraBundle"
                      class="absolute"
                      style="top: -20px"
                      @change="slideChangeAvalaraBundle"
                    />
                  </div>

                  <sw-input-group
                    :label="$t('corePbx.packages.avalara_transaction')"
                    class="md:col-span-2 pr-1"
                    v-if="formData.avalaraBundle"
                  >
                    <sw-input
                      type="number"
                      v-model="formData.bundleTransaction"
                      :show-labels="false"
                    />
                  </sw-input-group>

                  <sw-input-group
                    :label="$t('corePbx.packages.avalara_service')"
                    class="md:col-span-2 pl-1"
                    v-if="formData.avalaraBundle"
                  >
                    <sw-input
                      type="number"
                      v-model="formData.bundleService"
                      :show-labels="false"
                    />
                  </sw-input-group>
                  <sw-divider class="col-span-12 mt-2" />
                </div>

                <!-- Items -->
                <div
                  v-if="!formData.avalaraBundle"
                  class="col-span-6 flex grid grid-cols-6 items-start"
                >
                  <div class="col-span-1">
                    <p
                      class="p-0 mb-1 text-base leading-snug text-black box-title"
                    >
                      {{ $t('corePbx.packages.items') }}
                    </p>
                    <p class="p-0 m-0 text-xs leading-4 text-gray-500">
                      {{ $t('corePbx.packages.items') }}
                    </p>
                  </div>
                  <div class="col-span-1 relative">
                    <sw-switch
                      v-model="avalara_items"
                      class="absolute"
                      style="top: -20px"
                    />
                  </div>
                </div>
                <!-- Extension -->
                <div
                  v-if="!formData.avalaraBundle && extensions"
                  class="col-span-6 flex grid grid-cols-6 items-start"
                >
                  <div class="col-span-1">
                    <p
                      class="p-0 mb-1 text-base leading-snug text-black box-title"
                    >
                      {{ $t('corePbx.packages.extension') }}
                    </p>
                    <p class="p-0 m-0 text-xs leading-4 text-gray-500">
                      {{ $t('corePbx.packages.extension') }}
                    </p>
                  </div>
                  <div class="col-span-1 relative">
                    <sw-switch
                      v-model="avalara_extension"
                      class="absolute"
                      style="top: -20px"
                    />
                  </div>
                  <sw-input-group
                    :label="$t('customers.extension_items')"
                    class="md:col-span-2"
                    v-if="avalara_extension"
                  >
                    <sw-select
                      v-model="avalara_extension_item_id"
                      :options="itemsTypeLineOptions"
                      :tabindex="2"
                      :allow-empty="false"
                      label="name"
                      track-by="id"
                    />
                  </sw-input-group>
                </div>
                <!-- DID -->
                <div
                  v-if="!formData.avalaraBundle && did"
                  class="col-span-6 flex grid grid-cols-6 items-start"
                >
                  <div class="col-span-1">
                    <p
                      class="p-0 mb-1 text-base leading-snug text-black box-title"
                    >
                      {{ $t('corePbx.packages.did') }}
                    </p>
                    <p class="p-0 m-0 text-xs leading-4 text-gray-500">
                      {{ $t('corePbx.packages.did') }}
                    </p>
                  </div>
                  <div class="col-span-1 relative">
                    <sw-switch
                      v-model="avalara_did"
                      class="absolute"
                      style="top: -20px"
                    />
                  </div>
                  <sw-input-group
                    :label="$t('customers.did_items')"
                    class="md:col-span-2"
                    v-if="avalara_did"
                  >
                    <sw-select
                      v-model="avalara_did_item_id"
                      :options="itemsTypeTaxableAmountOptions"
                      :tabindex="2"
                      :allow-empty="false"
                      label="name"
                      track-by="id"
                    />
                  </sw-input-group>
                </div>
                <!-- Call rating -->
                <div
                  v-if="!formData.avalaraBundle && call_ratings"
                  class="col-span-6 flex grid grid-cols-6 items-start gap-2"
                >
                  <div class="col-span-1">
                    <p
                      class="p-0 mb-1 text-base leading-snug text-black box-title"
                    >
                      {{ $t('corePbx.packages.callrating') }}
                    </p>
                    <p class="p-0 m-0 text-xs leading-4 text-gray-500">
                      {{ $t('corePbx.packages.callrating') }}
                    </p>
                  </div>
                  <div class="col-span-1 relative">
                    <sw-switch
                      v-model="avalara_callrating"
                      class="absolute"
                      style="top: -20px"
                    />
                  </div>
                  <sw-input-group
                    :label="$t('customers.cdr_items')"
                    class="md:col-span-2"
                    v-if="avalara_callrating"
                  >
                    <sw-select
                      v-model="cdr_items_id"
                      :options="itemsTypeTaxableAmountOptions"
                      :tabindex="2"
                      :allow-empty="false"
                      label="name"
                      track-by="id"
                    />
                  </sw-input-group>

                  <sw-input-group
                    :label="$t('pbx_services.custom_destinations')"
                    class="md:col-span-2"
                    v-if="avalara_callrating"
                  >
                    <sw-select
                      v-model="custom_destinations_item_id"
                      :options="itemsTypeTaxableAmountOptions"
                      :tabindex="2"
                      :allow-empty="false"
                      label="name"
                      track-by="id"
                    />
                  </sw-input-group>

                  <div class="col-span-2 relative"></div>

                  <sw-input-group
                    :label="$t('pbx_services.custom_destinations_inter')"
                    class="md:col-span-2"
                    v-if="avalara_callrating"
                  >
                    <sw-select
                      v-model="inter_destinations_item_id"
                      :options="itemsTypeTaxableAmountOptions"
                      :tabindex="2"
                      :allow-empty="false"
                      label="name"
                      track-by="id"
                    />
                  </sw-input-group>

                  <sw-input-group
                    :label="$t('pbx_services.custom_destinations_toll')"
                    class="md:col-span-2"
                    v-if="avalara_callrating"
                  >
                    <sw-select
                      v-model="toll_free_destinations_item_id"
                      :options="itemsTypeTaxableAmountOptions"
                      :tabindex="2"
                      :allow-empty="false"
                      label="name"
                      track-by="id"
                    />
                  </sw-input-group>
                </div>
                <!-- custom_app_rate_items -->
                <div
                  v-if="
                    !formData.avalaraBundle &&
                    call_ratings &&
                    formData.custom_app_rate !== null
                  "
                  class="col-span-6 flex grid grid-cols-6 items-start"
                >
                  <div class="col-span-1">
                    <p
                      class="p-0 mb-1 text-base leading-snug text-black box-title"
                    >
                      {{ $t('pbx_services.custom_app_rate_items') }}
                    </p>
                    <p class="p-0 m-0 text-xs leading-4 text-gray-500">
                      {{ $t('pbx_services.custom_app_rate_items') }}
                    </p>
                  </div>
                  <div class="col-span-1 relative">
                    <sw-switch
                      v-model="avalara_custom_app_rate_items"
                      class="absolute"
                      style="top: -20px"
                    />
                  </div>
                  <sw-input-group
                    :label="$t('pbx_services.custom_app_rate_items')"
                    class="md:col-span-2"
                    v-if="avalara_custom_app_rate_items"
                  >
                    <sw-select
                      v-model="avalara_custom_app_rate_item_id"
                      :options="itemsTypeTaxableAmountOptions"
                      :tabindex="2"
                      :allow-empty="false"
                      label="name"
                      track-by="id"
                    />
                  </sw-input-group>
                </div>
                <!-- Services Price Item -->
                <div
                  v-if="!formData.avalaraBundle"
                  class="col-span-6 flex grid grid-cols-6 items-start"
                >
                  <div class="col-span-1">
                    <p
                      class="p-0 mb-1 text-base leading-snug text-black box-title"
                    >
                      {{ $t('pbx_services.services_price_item') }}
                    </p>
                    <p class="p-0 m-0 text-xs leading-4 text-gray-500">
                      {{ $t('pbx_services.services_price_item') }}
                    </p>
                  </div>
                  <div class="col-span-1 relative">
                    <sw-switch
                      v-model="avalara_services_price_item"
                      class="absolute"
                      style="top: -20px"
                    />
                  </div>
                  <sw-input-group
                    :label="$t('pbx_services.services_price_item')"
                    class="md:col-span-2"
                    v-if="avalara_services_price_item"
                  >
                    <sw-select
                      v-model="avalara_services_price_item_id"
                      :options="avalaraItemsOptions"
                      :tabindex="2"
                      :allow-empty="false"
                      label="name"
                      track-by="id"
                    />
                  </sw-input-group>
                </div>
                <!-- Additional Charges Item -->
                <div
                  v-if="!formData.avalaraBundle"
                  class="col-span-6 flex grid grid-cols-6 items-start"
                >
                  <div class="col-span-1">
                    <p
                      class="p-0 mb-1 text-base leading-snug text-black box-title"
                    >
                      {{ $t('pbx_services.additional_charges_item') }}
                    </p>
                    <p class="p-0 m-0 text-xs leading-4 text-gray-500">
                      {{ $t('pbx_services.additional_charges_item') }}
                    </p>
                  </div>
                  <div class="col-span-1 relative">
                    <sw-switch
                      v-model="avalara_additional_charges_item"
                      class="absolute"
                      style="top: -20px"
                    />
                  </div>
                  <sw-input-group
                    :label="$t('pbx_services.additional_charges_item')"
                    class="md:col-span-2"
                    v-if="avalara_additional_charges_item"
                  >
                    <sw-select
                      v-model="avalara_additional_charges_item_id"
                      :options="avalaraItemsOptions"
                      :tabindex="2"
                      :allow-empty="false"
                      label="name"
                      track-by="id"
                    />
                  </sw-input-group>
                </div>
              </div>
            </div>

            <sw-divider
              v-if="!formData.avalaraBundle"
              class="my-4 col-span-12"
            />

            <!-- <sw-input-group v-if="isExtensionSl" 
              :label="$t('packages.taxes')"
              class="md:col-span-3"
          >
            <sw-select
              ref="baseSelect"
              v-model="extension_i"
              :options="extensions"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('expenses.item_select')"
              class="mt-1"
              label="name"
              track-by="id"
            />
          </sw-input-group> -->
            <!-- STATUS -->
            <sw-input-group
              v-if="automatic_suspension"
              :label="$t('corePbx.packages.suspension_type')"
              class="md:col-span-2 ml-4"
            >
              <sw-select
                v-model="suspension_type"
                :options="suspensionTypeOptions"
                :tabindex="2"
                :allow-empty="false"
                label="text"
                track-by="value"
              />
            </sw-input-group>

            <!-- EXTENSION AND DID TEMPLATE-->

            <div class="flex mb-0 col-span-12">
              <div class="ml-4" v-if="isExtensionSl">
                <sw-input-group
                  :label="$t('corePbx.menu_title.extensions')"
                  class="relative"
                  style="min-width: 300px"
                >
                  <sw-select
                    v-model="extension_i"
                    :options="extension_ar"
                    :searchable="true"
                    :show-labels="false"
                    :tabindex="16"
                    :allow-empty="true"
                    :disabled="bandServices"
                    :placeholder="$t('corePbx.menu_title.extensions')"
                    label="name"
                    track-by="id"
                  />
                </sw-input-group>
              </div>

              <div class="ml-4" v-if="isDidSl">
                <sw-input-group
                  :label="$t('corePbx.menu_title.did')"
                  class="relative"
                  style="min-width: 300px"
                >
                  <sw-select
                    ref="baseSelect"
                    v-model="did_i"
                    :options="did_ar"
                    :searchable="true"
                    :show-labels="false"
                    :disabled="bandServices"
                    :placeholder="$t('corePbx.menu_title.did')"
                    class="mt-1"
                    label="name"
                    track-by="id"
                  />
                </sw-input-group>
              </div>
            </div>

            <!-- -->

            <!-- National dialing code / International dialing code -->
            <div class="mt-2 col-span-12 mx-6">
              <div class="grid grid-cols-2 gap-5">
                <sw-input-group
                  v-if="isAddCallRating"
                  :label="$t('corePbx.packages.international_dialing_code')"
                >
                  <sw-input
                    v-model="international_dialing_code_selected"
                    :options="international_dialing_code"
                    :searchable="true"
                    :show-labels="false"
                    label="name"
                  >
                  </sw-input>
                </sw-input-group>

                <sw-input-group
                  v-if="isAddCallRating"
                  :label="$t('corePbx.packages.national_dialing_code')"
                >
                  <sw-input
                    v-model="national_dialing_code_selected"
                    :options="national_dialing_code"
                    :searchable="true"
                    :show-labels="false"
                    label="name"
                  >
                  </sw-input>
                </sw-input-group>
              </div>
              <!-- RATE PER MINUTES -->
              <div class="grid grid-cols-2 gap-5">
                <!-- Inclusive Minutes -->
                <sw-input-group
                  v-if="isAddCallRating"
                  :label="$t('packages.inclusive_minutes')"
                  :error="InMinError"
                >
                  <sw-input
                    v-model="formData.inclusive_minutes"
                    :invalid="$v.formData.inclusive_minutes.$error"
                    focus
                    type="number"
                    name="inclusive_minutes"
                    tabindex="1"
                    @input="$v.formData.inclusive_minutes.$touch()"
                    numeric
                  />
                </sw-input-group>

                <!-- :invalid="$v.rate_per_minutes_selected.$error" -->
                <sw-input-group
                  :label="$t('corePbx.packages.rate_per_minutes')"
                  v-if="isAddCallRating"
                >
                  <sw-money
                    v-model="rate_per_minutes_selected"
                    :currency="customerCurrencyPerMinute"
                    name="rate_per_minutes_selected"
                    tabindex="1"
                  />
                </sw-input-group>
              </div>
              <div class="grid grid-cols-2 gap-5">
                <sw-input-group
                  :label="$t('corePbx.packages.minutes_increments')"
                  v-if="isAddCallRating"
                >
                  <sw-input
                    v-model="minutes_increments_selected"
                    :options="minutes_increments"
                    :invalid="$v.minutes_increments_selected.$error"
                    :searchable="true"
                    :show-labels="false"
                    label="name"
                    numeric
                  />
                </sw-input-group>

                <sw-input-group
                  :label="$t('corePbx.packages.select')"
                  v-if="isAddCallRating"
                >
                  <sw-select
                    v-model.trim="formData.type_time_increment"
                    :options="type_time_increment"
                    :searchable="true"
                    :show-labels="false"
                    :tabindex="16"
                    :allow-empty="true"
                    :placeholder="$t('corePbx.extensions.select')"
                    label="text"
                    track-by="value"
                  />
                </sw-input-group>
              </div>
            </div>
          </div>
        </div>

        <sw-divider class="my-0 col-span-12 opacity-0" />

        <!-- Tax  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <!-- General Tax  -->
          <div class="grid col-span-12 gap-y-1 gap-x-4 md:grid-cols-6">
            <sw-divider class="col-span-12 my-8" />
            <div class="col-span-12">
              <h3 class="text-lg font-medium">
                {{ $t('packages.general_taxes') }}
              </h3>
            </div>

            <sw-table-component
              class="col-span-12"
              ref="table"
              :show-filter="false"
              :data="paralelo"
              table-class="table"
              variant="gray"
            >
              <sw-table-column
                :sortable="true"
                :label="$t('settings.tax_types.tax_name')"
                show="name"
              >
                <template slot-scope="row">
                  <span>{{ $t('settings.tax_types.tax_name') }}</span>
                  <span class="mt-6">{{ row.name }}</span>
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :filterable="true"
                :label="$t('settings.tax_types.compound_tax')"
              >
                <template slot-scope="row">
                  <span>{{ $t('settings.tax_types.compound_tax') }}</span>
                  <sw-badge
                    :bg-color="
                      $utils.getBadgeStatusColor(
                        row.compound_tax ? 'YES' : 'NO'
                      ).bgColor
                    "
                    :color="
                      $utils.getBadgeStatusColor(
                        row.compound_tax ? 'YES' : 'NO'
                      ).color
                    "
                  >
                    {{ row.compound_tax ? 'Yes' : 'No'.replace('_', ' ') }}
                  </sw-badge>
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :filterable="true"
                :label="$t('settings.tax_types.percent')"
              >
                <template slot-scope="row">
                  <span>{{ $t('settings.tax_types.percent') }}</span>
                  {{ row.percent }} %
                </template>
              </sw-table-column>

              <sw-table-column :sortable="true" :filterable="true">
                <template slot-scope="row">
                  <trash-icon
                    @click="removeTax(row)"
                    class="h-5 mr-3 text-gray-600"
                  />
                </template>
              </sw-table-column>
            </sw-table-component>
            <div class="col-span-12"></div>

            <!-- TAXES SELECT-->

            <sw-input-group :label="$t('packages.taxes')" class="md:col-span-3">
              <sw-select
                v-model="tax"
                :options="taxes"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$tc('packages.add_tax')"
                class="mt-2"
                label="name_por"
                track-by="id"
                @select="taxSeleted"
                :tabindex="9"
              />
            </sw-input-group>

            <!-- TAXES SELECT-->

            <!-- DESDE AQUI TAX_GROUPS SELECT-->

            <sw-input-group
              :label="$t('packages.member_tax_groups')"
              class="md:col-span-3"
            >
              <sw-select
                :disabled="isNoGeneralTaxes"
                v-model="groupTax"
                :options="groupTaxes"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$tc('packages.add_group_tax')"
                :multiple="true"
                class="mt-2"
                label="name"
                track-by="id"
                @select="groupTaxSeleted"
                @remove="removeTaxGroup"
                :tabindex="10"
              />
            </sw-input-group>

            <!-- TAX_GROUPS SELECT-->
          </div>

          <!-- Cdr  Tax -->
          <div class="grid col-span-12 gap-y-1 gap-x-4 md:grid-cols-6">
            <sw-divider class="col-span-12 my-8" />
            <div class="col-span-12">
              <h3 class="text-lg font-medium">
                {{ $t('corePbx.packages.cdr_taxes') }}
              </h3>
            </div>

            <sw-table-component
              class="col-span-12"
              ref="table"
              :show-filter="false"
              :data="paraleloTaxesCdr"
              table-class="table"
              variant="gray"
            >
              <sw-table-column
                :sortable="true"
                :label="$t('settings.tax_types.tax_name')"
                show="name"
              >
                <template slot-scope="row">
                  <span>{{ $t('settings.tax_types.tax_name') }}</span>
                  <span class="mt-6">{{ row.name }}</span>
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :filterable="true"
                :label="$t('settings.tax_types.compound_tax')"
              >
                <template slot-scope="row">
                  <span>{{ $t('settings.tax_types.compound_tax') }}</span>
                  <sw-badge
                    :bg-color="
                      $utils.getBadgeStatusColor(
                        row.compound_tax ? 'YES' : 'NO'
                      ).bgColor
                    "
                    :color="
                      $utils.getBadgeStatusColor(
                        row.compound_tax ? 'YES' : 'NO'
                      ).color
                    "
                  >
                    {{ row.compound_tax ? 'Yes' : 'No'.replace('_', ' ') }}
                  </sw-badge>
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :filterable="true"
                :label="$t('settings.tax_types.percent')"
              >
                <template slot-scope="row">
                  <span>{{ $t('settings.tax_types.percent') }}</span>
                  {{ row.percent }} %
                </template>
              </sw-table-column>

              <sw-table-column :sortable="true" :filterable="true">
                <template slot-scope="row">
                  <trash-icon
                    @click="removeTaxCdr(row)"
                    class="h-5 mr-3 text-gray-600"
                  />
                </template>
              </sw-table-column>
            </sw-table-component>
            <div class="col-span-12"></div>
            <sw-input-group :label="$t('packages.taxes')" class="md:col-span-3">
              <sw-select
                v-model="taxCdr"
                :options="taxesCdr"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$tc('packages.add_tax')"
                class="mt-2"
                label="name_por"
                track-by="id"
                @select="taxCdrSeleted"
                :tabindex="9"
              />
            </sw-input-group>
            <!-- <sw-input-group
              :label="$t('packages.member_tax_groups')"
              class="md:col-span-3"
            >
              <sw-select
                :disabled="isNoGeneralTaxes"
                v-model="groupTax"
                :options="groupTaxes"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$tc('packages.add_group_tax')"
                class="mt-2"
                label="name"
                track-by="id"
                @select="groupTaxCdrSeleted"
                :tabindex="10"
              />
            </sw-input-group> -->
          </div>
        </div>

        <br /><br />

        <!-- Group discount -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.title_discount') }}
          </h6>

          <div class="grid col-span-12 gap-y-6 gap-x-4 md:grid-cols-6">
            <div class="col-span-3">
              <sw-divider class="col-span-12" />
              <div class="flex mt-3 mb-4">
                <div class="relative w-12">
                  <sw-switch
                    v-model="formData.package_discount"
                    class="absolute"
                    style="top: -20px"
                    @change="onDiscounts"
                  />
                </div>
                <div class="ml-4">
                  <p
                    class="p-0 mb-1 text-base leading-snug text-black box-title"
                  >
                    {{ $t('packages.apply_general_discount') }}
                  </p>
                </div>
              </div>
            </div>
            <div class="col-span-3">
              <div class="flex mt-3 mb-4">
                <div class="relative" style="width: 8em">
                  <label>
                    {{ $t('packages.title_discounts') }}
                  </label>
                  <sw-select
                    :disabled="showSelectdiscounts"
                    v-model="formData.type"
                    :options="discounts"
                    :searchable="true"
                    :show-labels="false"
                    :tabindex="16"
                    :allow-empty="true"
                    :placeholder="$t('packages.title_discounts')"
                    label="text"
                    track-by="value"
                  />
                </div>
                <div class="relative" style="width: 8em; margin-left: 30px">
                  <label>
                    {{ $t('packages.value_discount') }}
                  </label>
                  <sw-money
                    v-model="value_discount"
                    :currency="customPrefixDescount"
                    :invalid="$v.value_discount.$error"
                    name="value_discount"
                    tabindex="5"
                    @input="checkValueDiscount"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="formData.package_discount">
          <sw-input-group
            :label="$t('customers.discount_term_type')"
            class="mb-4"
          >
            <sw-select
              v-model="formData.discount_term_type"
              :options="discount_term_type"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('customers.select_a_discount_term')"
              class="mt-2"
              label="name"
              :tabindex="9"
            />
          </sw-input-group>

          <div
            v-if="discountBetweenDates"
            class="relative grid grid-flow-col grid-rows"
          >
            <sw-input-group :label="$t('general.from')" class="mt-2">
              <base-date-picker
                v-model="formData.discount_start_date"
                :calendar-button="true"
                calendar-button-icon="calendar"
              />
            </sw-input-group>

            <div
              class="hidden w-8 h-0 ml-8 border border-gray-400 border-solid xl:block"
              style="margin-top: 3.5rem"
            />

            <sw-input-group :label="$t('general.to')" class="mt-2">
              <base-date-picker
                v-model="formData.discount_end_date"
                :calendar-button="true"
                calendar-button-icon="calendar"
              />
            </sw-input-group>
          </div>
          <sw-input-group
            v-if="!discountBetweenDates"
            :label="$t('customers.time_unit_number')"
            class="mt-2"
          >
            <div class="flex" style="width: 50%" role="group">
              <sw-input
                v-model="formData.discount_time_units"
                :invalid="$v.formData.discount_time_units.$error"
                class="border-r-0 rounded-tr-sm rounded-br-sm"
                @input="$v.formData.discount_time_units.$touch()"
              />
              <sw-select
                v-model="formData.discount_term"
                :options="discount_term"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('customers.select_a_term')"
                label="name"
              />
            </div>
          </sw-input-group>
        </div>

        <!-- Items -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <sw-divider class="col-span-12" />

          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.packages_items') }}
          </h6>
          <div class="grid col-span-12 gap-y-6 gap-x-4 md:grid-cols-6">
            <sw-input-group
              :label="$t('packages.item_groups')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="item_group"
                :options="item_groups"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$tc('packages.item_groups_select')"
                class="mt-2"
                label="name"
                track-by="id"
                @select="itemGroupSelected"
                :tabindex="11"
              />
            </sw-input-group>
            <div
              class="col-span-12"
              v-if="
                undefined !== formData.item_groups &&
                formData.item_groups.length > 0
              "
            >
              <div class="flex flex-wrap justify-start">
                <div
                  v-for="(item, index) in formData.item_groups"
                  :key="index"
                  class="flex justify-center items-center m-1 font-medium py-1 px-2 bg-white rounded-full text-indigo-100 bg-indigo-700 border border-indigo-700"
                >
                  <div
                    class="text-xs text-base leading-none max-w-full flex-initial py-2 pl-2"
                    v-text="item.name"
                  />
                  <div class="flex flex-auto flex-row-reverse">
                    <div>
                      <svg
                        @click="removeItemGroup(item)"
                        xmlns="http://www.w3.org/2000/svg"
                        width="100%"
                        height="100%"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="4"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-x cursor-pointer hover:text-indigo-400 rounded-full w-6 h-4 ml-2 pr-1"
                      >
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="grid col-span-12">
              <!-- Items -->

              <div class="table-responsive-item2">
                <div class="tablemin">
                  <table class="w-full text-center item-table">
                    <colgroup>
                      <col style="width: 40%" />
                      <col style="width: 10%" />
                      <col style="width: 15%" />
                      <col
                        v-if="discountPerItem === 'YES'"
                        style="width: 15%"
                      />
                      <col style="width: 15%" />
                    </colgroup>
                    <thead class="bg-white border border-gray-200 border-solid">
                      <tr>
                        <th
                          class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                        >
                          <span class="pl-12">
                            {{ $tc('items.item', 1) }}
                          </span>
                        </th>
                        <th
                          class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
                        >
                          <span>
                            {{ $t('estimates.item.quantity') }}
                          </span>
                        </th>
                        <th
                          class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                        >
                          <span>
                            {{ $t('estimates.item.price') }}
                          </span>
                        </th>
                        <th
                          v-if="discountPerItem === 'YES'"
                          class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
                        >
                          <span>
                            {{ $t('estimates.item.discount') }}
                          </span>
                        </th>
                        <th
                          class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
                        >
                          <span class="pr-10 column-heading">
                            {{ $t('estimates.item.amount') }}
                          </span>
                        </th>
                      </tr>
                    </thead>
                    <draggable
                      v-model="formData.items"
                      class="item-body"
                      tag="tbody"
                      handle=".handle"
                    >
                      <package-item
                        v-for="(item, index) in formData.items"
                        :key="item.id"
                        :index="index"
                        :item-data="item"
                        :currency="currency"
                        :isView="false"
                        :isNoGeneralTaxes="isNoGeneralTaxes"
                        :package-items="formData.items"
                        :tax-per-item="taxPerItem"
                        :discount-per-item="discountPerItem"
                        @remove="removeItem"
                        @update="updateItem"
                        @itemValidate="checkItemsData"
                      />
                    </draggable>
                  </table>
                  <div
                    class="flex items-center justify-center w-full px-6 py-3 text-base border-b border-gray-200 border-solid cursor-pointer text-primary-400 hover:bg-gray-200"
                    @click="addItem"
                  >
                    <shopping-cart-icon class="h-5 mr-2" />
                    {{ $t('estimates.add_item') }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="py-2">
          <sw-button
            :loading="isLoading"
            type="submit"
            variant="primary"
            size="lg"
            class=""
          >
            <save-icon class="w-6 h-6 mr-1 -ml-2 mr-2" v-if="!isLoading" />
            {{
              isEdit
                ? $t('packages.update_package')
                : $t('packages.save_package')
            }}
          </sw-button>

          <sw-button
            variant="primary-outline"
            type="button"
            size="lg"
            class="mr-4"
            @click="cancelForm()"
          >
            <x-circle-icon class="w-6 h-6 mr-1 -ml-2" />
            {{ $t('general.cancel') }}
          </sw-button>
        </div>
      </sw-card>
    </form>
  </base-page>
</template>

<script>
import RightArrow from '@/components/icon/RightArrow'
import MoreIcon from '@/components/icon/MoreIcon'
import LeftArrow from '@/components/icon/LeftArrow'
import draggable from 'vuedraggable'
import PackageItem from './Item'
import PackageStub from '../../../stub/package'
import Guid from 'guid'
import TaxStub from '../../../stub/tax'
import moment from 'moment'

import { mapActions, mapGetters } from 'vuex'
import {
  TrashIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
const {
  required,
  minLength,
  email,
  numeric,
  minValue,
  between,
} = require('vuelidate/lib/validators')
export default {
  components: {
    MoreIcon,
    draggable,
    TrashIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusIcon,
    RightArrow,
    LeftArrow,
    PackageItem,
    XCircleIcon,
  },
  data() {
    return {
      isAvalaraAvailable: false,
      isNoGeneralTaxes: false,
      add_call_rating: false,
      showSelectdiscounts: true,
      discountPerItemStore: null,
      discountPerItem: null,
      taxPerItem: 'YES',
      selectedCurrency: '',
      prefixrate_groups: [],
      prefixrate_groups_outboubd: [],
      tax: null,
      taxCdr: null,
      taxesFetch: [],

      taxesFetchcdr: [],
      taxes: [],
      taxesCdr: [],
      dropdown_server: [],
      itemsF: [],
      isRequestOnGoing: false,
      isLoading: false,
      EstimateFields: [],
      activeTab: 'Text',
      apply_tax_type_pre: {
        value: 'general',
        text: 'General',
      },
      discounts: [
        {
          value: 'fixed',
          text: 'Fixed',
        },
        {
          value: 'percentage',
          text: 'Percentage',
        },
      ],
      status: [
        {
          value: 'A',
          text: 'Active',
        },
        {
          value: 'I',
          text: 'Inactive',
        },
      ],
      apply_tax_type_options: [
        {
          value: 'general',
          text: 'General',
        },
        {
          value: 'item',
          text: 'Item',
        },
      ],
      discount_term_type: [
        { name: 'Between dates', value: 'D' },
        { name: 'Time units', value: 'U' },
      ],
      client_limit: '',
      qty_available: '',
      value_discount: 0,
      extensions: false,
      did: false,
      call_ratings: false,
      unmetered: false,
      //
      avalara_options: 0,
      update_child_services: 0,
      avalara_price: 0,
      avalara_extension: 0,
      avalara_did: 0,
      avalara_callrating: 0,
      avalara_items: 0,
      avalara_custom_app_rate_items: 0,
      avalara_services_price_item: 0,
      avalara_additional_charges_item: 0,
      //
      avalara_extension_item_id: null,
      avalara_did_item_id: null,
      cdr_items_id: null,
      custom_destinations_item_id: null,
      inter_destinations_item_id: null,
      toll_free_destinations_item_id: null,
      avalara_custom_app_rate_item_id: null,
      avalara_services_price_item_id: null,
      avalara_additional_charges_item_id: null,
      automatic_suspension: false,
      modify_server: false,
      type_time_increment: [
        { value: 'sec', text: 'Seconds' },
        { value: 'min', text: 'Minutes' },
      ],
      status_payment: [
        { value: 'prepaid', text: 'Prepaid' },
        { value: 'postpaid', text: 'Postpaid' },
      ],
      suspensionTypeOptions: [
        { value: 'T', text: 'Tenant' },
        { value: 'E', text: 'Extension' },
      ],
      suspension_type: {
        value: 'T',
        text: 'Tenant',
      },
      discount_term: [
        { name: 'Days', value: 'days' },
        { name: 'Weeks', value: 'weeks' },
        { name: 'Months', value: 'months' },
        { name: 'Years', value: 'years' },
      ],
      national_dialing_code_selected: null,
      international_dialing_code_selected: null,
      national_dialing_code: null,
      international_dialing_code: null,
      rate_per_minutes_selected: 0,
      minutes_increments_selected: 1,
      rate_per_minutes: null,
      minutes_increments: 1,
      groupRight: [],
      groupTax: null,
      groupTaxes: [],
      //
      prefixrate_groups_id: [],
      prefixrate_groups_outbound_id: [],
      //
      groupLeft: [],
      groupLeftTax: [],
      groupLeftTaxFetch: [],
      groupLeftTaxModel: null,
      did_i: null,
      extension_i: null,
      did_ar: [],
      extension_ar: [],
      item_group: null,
      itemGroupsFetch: [],
      item_groups: [],
      paralelo: [],
      paraleloTaxesCdr: [],
      bandServices: false,
      cdrStatusOptions: [
        {
          label: 'ALL',
          value: 0,
          color: 'dark',
        },
        {
          label: 'Failed',
          value: 1,
          color: 'danger',
        },
        {
          label: 'Busy',
          value: 2,
          color: 'warning',
        },
        {
          label: 'Unanswered',
          value: 4,
          color: 'primary',
        },
        {
          label: 'Answered',
          value: 8,
          color: 'success',
        },
      ],
      clearTimeOutDescount: null,
      formData: {
        id: null,
        apply_tax_type: {
          value: 'general',
          text: 'General',
        },
        cdrStatus: [],

        apply_discount_type: null,
        discount_val: 0,
        pbx_package_name: '',
        html: ' ',
        text: ' ',
        status: null,
        type: { value: 'fixed', text: 'Fixed' },
        value_discount: 0,
        status_payment: null,
        client_limit: null,
        qty_available: null,
        extensions: false,
        did: false,
        call_ratings: false,
        unmetered: false,
        //
        avalara_options: 0,
        update_child_services: 0,
        avalara_price: 0,
        avalara_extension: 0,
        avalara_did: 0,
        avalara_callrating: 0,
        avalara_items: 0,
        avalara_custom_app_rate_items: 0,
        avalara_services_price_item: 0,
        avalara_additional_charges_item: 0,
        //
        avalaraBundle: 0,
        bundleTransaction: 0,
        bundleService: 0,
        //
        avalara_extension_item_id: null,
        avalara_did_item_id: null,
        cdr_items_id: null,
        custom_destinations_item_id: null,
        inter_custom_destinations_item_id: null,
        toll_free_custom_destinations_item_id: null,
        avalara_custom_app_rate_item_id: null,
        avalara_services_price_item_id: null,
        avalara_additional_charges_item_id: null,
        automatic_suspension: false,
        prefixrate_groups_id: [],
        prefixrate_groups_outbound_id: [],
        custom_destination_groups: [],
        custom_app_rate: null,
        modify_server: false,
        package_discount: false,
        taxes: [],
        tax_groups: [],
        taxesCdr: [],
        items: [],
        itemsPre: [
          {
            ...PackageStub,
            id: Guid.raw(),
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          },
        ],
        groupLeftTax: [],
        item_group: null,
        item_groups: [],
        itemGroupsFetch: [],
        items: [],
        dropdown_server: null,
        rate: 0,
        inclusive_minutes: 0,
        national_dialing_code: null,
        international_dialing_code: null,
        rate_per_minutes: null,
        minutes_increments: 1,
        type_time_increment: { value: 'sec', text: 'Seconds' },
        template_did_id: null,
        template_extension_id: null,
        discount_term_type: { name: 'Between dates', value: 'D' },
        discount_time_units: 0,
        discount_start_date: null,
        discount_end_date: null,
        discount_term: { name: 'Days', value: 'days' },
      },
      formData2: {
        id: null,
        apply_tax_type: {
          value: 'general',
          text: 'General',
        },
        cdrStatus: [],

        apply_discount_type: null,
        discount_val: 0,
        pbx_package_name: '',
        html: ' ',
        text: ' ',
        status: null,
        type: { value: 'fixed', text: 'Fixed' },
        value_discount: 0,
        status_payment: null,
        client_limit: null,
        qty_available: null,
        extensions: false,
        did: false,
        call_ratings: false,
        unmetered: false,
        //
        avalara_options: 0,
        update_child_services: 0,
        avalara_price: 0,
        avalara_extension: 0,
        avalara_did: 0,
        avalara_callrating: 0,
        avalara_items: 0,
        avalara_custom_app_rate_items: 0,
        avalara_services_price_item: 0,
        avalara_additional_charges_item: 0,
        //
        avalaraBundle: 0,
        bundleTransaction: 0,
        bundleService: 0,
        //
        avalara_extension_item_id: null,
        avalara_did_item_id: null,
        cdr_items_id: null,
        custom_destinations_item_id: null,
        inter_custom_destinations_item_id: null,
        toll_free_custom_destinations_item_id: null,
        avalara_custom_app_rate_item_id: null,
        avalara_services_price_item_id: null,
        avalara_additional_charges_item_id: null,
        automatic_suspension: false,
        prefixrate_groups_id: [],
        prefixrate_groups_outbound_id: [],
        custom_destination_groups: [],
        custom_app_rate: null,
        modify_server: false,
        package_discount: false,
        taxes: [],
        tax_groups: [],
        taxesCdr: [],
        items: [],
        itemsPre: [
          {
            ...PackageStub,
            id: Guid.raw(),
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          },
        ],
        groupLeftTax: [],
        item_group: null,
        item_groups: [],
        itemGroupsFetch: [],
        items: [],
        dropdown_server: null,
        rate: 0,
        inclusive_minutes: 0,
        national_dialing_code: null,
        international_dialing_code: null,
        rate_per_minutes: null,
        minutes_increments: 1,
        type_time_increment: { value: 'sec', text: 'Seconds' },
        template_did_id: null,
        template_extension_id: null,
        discount_term_type: { name: 'Between dates', value: 'D' },
        discount_time_units: 0,
        discount_start_date: null,
        discount_end_date: null,
        discount_term: { name: 'Days', value: 'days' },
      },
      all_cdrs: false,
      customAppRateOptions: [],
    }
  },

  computed: {
    ...mapGetters('pack', { packageNameGroup: 'packageNameGroup' }),
    ...mapGetters('avalara', ['avalaraItems']),

    avalaraItemsOptions() {
      return this.avalaraItems.map((item) => {
        return {
          ...item,
        }
      })
    },
    itemsTypeLineOptions() {
      return this.avalaraItems.filter((item) => {
        if (item.avalara_bool) {
          return item
        }
      })
    },
    itemsTypeTaxableAmountOptions() {
      return this.avalaraItems.filter((item) => {
        if (item.avalara_bool) {
          return item
        }
      })
    },

    pageTitle() {
      if (this.isEdit) {
        return this.$t('packages.edit_package')
      } else if (this.isCopy) {
        return this.$t('packages.copy_package')
      }
      return this.$t('packages.new_package')
    },

    labelStatusCdr() {
      const statusLabelArr = this.formData.cdrStatus.map((item) => {
        return this.cdrStatusOptions.find((status) => status.value === item)
          .label
      })
      // si inlcluye en 0 muestra All
      if (statusLabelArr.includes('ALL')) {
        return 'All'
      } else {
        return statusLabelArr.join(' - ')
      }
    },

    currency() {
      return this.selectedCurrency
    },
    ...mapGetters('company', ['defaultCurrencyForInput']),
    customerCurrency() {
      return this.defaultCurrencyForInput
    },
    customPrefixDescount() {
      return {
        ...this.customerCurrency,
        prefix:
          this.formData.type?.value == 'percentage'
            ? '% '
            : this.customerCurrency.prefix,
      }
    },
    customerCurrencyPerMinute() {
      let currency = JSON.parse(JSON.stringify(this.defaultCurrencyForInput))
      if (currency.precision) {
        currency.precision = 5
      }
      return currency
    },

    isEdit() {
      if (this.$route.name === 'corepbx.billingtemplates.edit') {
        return true
      }
      return false
    },
    isCopy() {
      if (this.$route.name === 'corepbx.billingtemplates.copy') {
        return true
      }
      return false
    },
    nameError() {
      if (!this.$v.formData.pbx_package_name.$error) {
        return ''
      }
      if (!this.$v.formData.pbx_package_name.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.pbx_package_name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.pbx_package_name.$params.minLength.min,
          { count: this.$v.formData.pbx_package_name.$params.minLength.min }
        )
      }
    },
    statusPaymentError() {
      if (!this.$v.formData.status_payment.$error) {
        return ''
      }
      if (!this.$v.formData.status_payment.required) {
        return this.$t('validation.required')
      }
    },
    statusError() {
      if (!this.$v.formData.status.$error) {
        return ''
      }
      if (!this.$v.formData.status.required) {
        return this.$t('validation.required')
      }
    },
    dropdownError() {
      if (!this.$v.formData.dropdown_server.$error) {
        return ''
      }
      if (!this.$v.formData.dropdown_server.required) {
        return this.$t('validation.required')
      }
    },
    inclusive_minutesError() {
      if (!this.$v.formData.inclusive_minutes.$error) {
        return ''
      }
      if (!this.$v.formData.inclusive_minutes.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
    rateError() {
      if (!this.$v.formData.rate.$error) {
        return ''
      }
      if (!this.$v.formData.rate.minValue.min) {
        return this.$tc('validation.numbers_only')
      }
    },
    InMinError() {
      if (!this.$v.formData.inclusive_minutes.$error) {
        return ''
      }
      if (!this.$v.formData.inclusive_minutes.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
    qtyError() {
      if (!this.$v.qty_available.$error) {
        return ''
      }
      if (!this.$v.qty_available.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
    clientQtyError() {
      if (!this.$v.client_limit.$error) {
        return ''
      }
      if (!this.$v.client_limit.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
    isAddCallRating() {
      if (!this.unmetered) {
        this.add_call_rating = this.call_ratings
        return this.add_call_rating
      }
    },
    isExtensionSl() {
      return this.extensions ? 1 : 0
    },
    isDidSl() {
      return this.did ? 1 : 0
    },
    discountBetweenDates() {
      if (this.formData.discount_term_type.value === 'D') {
        this.formData.discount_term = { name: 'Days', value: 'days' }
        this.formData.discount_time_units = 0
        return this.formData.discount_term_type.value === 'D'
      } else {
        ;(this.formData.discount_start_date = null),
          (this.formData.discount_end_date = null)
      }
    },
  },
  watch: {
    packageNameGroup(val) {
      this.groupRight.push({ name: val })
    },
    subtotal(newValue) {
      if (this.formData.discount_type === 'percentage') {
        this.formData.discount_val = (this.formData.discount * newValue) / 100
      }
    },
  },
  methods: {
    ...mapActions('pbx', [
      'addPackage',
      'fetchPackage',
      'fetchItemGroups',
      'updatePackage',
      'fetchGroupMembership',
      'saveGroup',
      'fetchGroupTaxMembership',
    ]),
    ...mapActions('taxType', [
      'indexLoadData',
      'deleteTaxType',
      'fetchTaxType',
      'fetchTaxTypes',
      'packageGroup',
    ]),

    ...mapActions('internacionalrate', ['CargarCustomDestination']),

    ...mapActions('modules', ['fetchPbxServer', 'getModules']),

    ...mapGetters('company', ['itemDiscount']),

    ...mapGetters('company', ['defaultCurrency']),

    ...mapActions('item', ['fetchItems']),

    ...mapActions('extensions', ['fetchExtensionsnp']),

    ...mapActions('did', ['fetchDIDsnp']),

    ...mapActions('taxType', ['fetchTaxTypes']),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('modal', ['openModal']),
    ...mapActions('user', ['getUserModules']),

    ...mapActions('avalara', [
      'fetchAvalaraDefault',
      'fetchAvalaraItems',
      'checkStatusAvalara',
    ]),

    ...mapActions('customAppRate', ['fetchCustomAppRate']),

    changeStatus(status) {
      if (status.includes(0)) {
        const statusArr = this.cdrStatusOptions.map((item) => item.value)
        this.formData.cdrStatus = statusArr
      }
    },

    async getCustomAppRate() {
      try {
        const res = await this.fetchCustomAppRate({ limit: 10000000 })
        this.customAppRateOptions = res.data.customAppRates.data
      } catch (e) {}
    },

    calTax(taxPercent, amount) {
      return Math.round((amount * taxPercent) / 100)
    },
    onSelectApplyTaxType(type) {
      this.taxPerItem = 'NO'
      setTimeout(() => {
        if (type.value == 'none' || type.value == 'general') {
          this.taxPerItem = 'YES'
          this.isNoGeneralTaxes = type.value == 'none' ? true : false
          setTimeout(() => {
            this.recomputeTotal = true
            this.taxPerItem = 'NO'
          }, 50)
        } else {
          this.recomputeTotal = true
          this.taxPerItem = 'YES'
          this.isNoGeneralTaxes = true
        }
      }, 50)
    },
    onSelectTax(selectedTax) {
      let amount = 0

      if (selectedTax.compound_tax && this.subtotalWithDiscount) {
        amount = Math.round(
          ((this.subtotalWithDiscount + this.totalSimpleTax) *
            selectedTax.percent) /
            100
        )
      } else if (this.subtotalWithDiscount && selectedTax.percent) {
        amount = Math.round(
          (this.subtotalWithDiscount * selectedTax.percent) / 100
        )
      }

      this.formData.taxes.push({
        ...TaxStub,
        id: Guid.raw(),
        name: selectedTax.name,
        percent: selectedTax.percent,
        compound_tax: selectedTax.compound_tax,
        tax_type_id: selectedTax.id,
        amount,
      })

      if (this.$refs) {
        this.$refs.taxModal.close()
      }
    },

    checkValueDiscount(value) {
      if (this.formData.type.value == 'percentage' && value > 100) {
        this.value_discount = 100
      } else if (this.formData.type.value == 'percentage' && value < 0) {
        this.value_discount = 0
      }
    },
    addItem() {
      this.formData.items.push({
        ...PackageStub,
        id: Guid.raw(),
        end_period_act: false,
        end_period_number: 1,
        taxes: [{ ...TaxStub, id: Guid.raw() }],
      })
    },

    removeItem(index) {
      this.formData.items.splice(index, 1)
    },

    updateItem(data) {
      Object.assign(this.formData.items[data.index], { ...data.item })
    },

    checkItemsData(index, isValid) {
      this.formData.items[index].valid = isValid
    },

    async fetchInitialItems() {
      this.isLoadingData = true

      // if (!this.isEdit) {
      let response = await this.fetchCompanySettings([
        'discount_per_item',
        'tax_per_item',
      ])

      if (response.data) {
        this.discountPerItemStore = response.data.discount_per_item
        this.discountPerItem = response.data.discount_per_item
        this.taxPerItem = response.data.tax_per_item
      }
      // }

      Promise.all([
        this.fetchItems({
          filter: {},
          orderByField: '',
          orderBy: '',
          limit: 'all',
        }),
        this.fetchCompanySettings(['estimate_auto_generate']),
      ])
        .then(async ([res1, res2]) => {
          this.itemsF = res1.data.items.data
        })
        .catch((error) => {})
    },

    onDiscounts(val) {
      this.showSelectdiscounts = !val
    },
    /*
    removeGroupTax(tax) {
      let myArray = this.formData.groupLeftTax

      for (var i = myArray.length - 1; i >= 0; --i) {
        if (myArray[i].id == tax.id) {
          myArray.splice(i, 1)
        }
      }

      this.formData.groupLeftTax = myArray

      const filterByReference = (arr1, arr2) => {
        let res = []
        res = arr1.filter((el) => {
          return !arr2.find((element) => {
            return element.id === el.id
          })
        })
        return res
      }

      this.groupLeftTax = filterByReference(
        this.groupLeftTaxFetch,
        this.formData.groupLeftTax
      )
      this.groupLeftTaxModel = null
      // console.log('Format Taxes', this.formData.groupLeftTax)
    },
    */
    removeTax(tax) {
      let myArray = this.formData.taxes

      for (var i = myArray.length - 1; i >= 0; --i) {
        if (myArray[i].id == tax.id) {
          myArray.splice(i, 1)
        }
      }

      this.formData.taxes = myArray
      this.paralelo = [...myArray]

      const filterByReference = (arr1, arr2) => {
        let res = []
        res = arr1.filter((el) => {
          return !arr2.find((element) => {
            return element.id === el.id
          })
        })
        return res
      }

      this.taxes = filterByReference(this.taxesFetch, this.formData.taxes)

      if (this.paralelo.length == 0) this.groupTax = null

      this.tax = null
    },
    removeTaxGroup(val) {
      this.paralelo = this.filterByReference(
        this.paralelo,
        val.tax_groups_tax_types
      )
      this.taxes = this.filterByReference(this.taxesFetch, this.paralelo)

      this.formData.taxes = [...this.paralelo]
    },

    removeTaxCdr(tax) {
      let myArray = this.formData.taxesCdr

      for (var i = myArray.length - 1; i >= 0; --i) {
        if (myArray[i].id == tax.id) {
          myArray.splice(i, 1)
        }
      }

      this.formData.taxesCdr = myArray
      this.paraleloTaxesCdr = [...myArray]

      const filterByReference = (arr1, arr2) => {
        let res = []
        res = arr1.filter((el) => {
          return !arr2.find((element) => {
            return element.id === el.id
          })
        })
        return res
      }

      this.taxesCdr = filterByReference(
        this.taxesFetchcdr,
        this.formData.taxesCdr
      )
      this.taxCdr = null
    },
    removeItemGroup(item) {
      let myArray = this.formData.item_groups

      for (var i = myArray.length - 1; i >= 0; --i) {
        if (myArray[i].id == item.id) {
          myArray.splice(i, 1)
        }
      }

      this.formData.item_groups = myArray

      const filterByReference = (arr1, arr2) => {
        let res = []
        res = arr1.filter((el) => {
          return !arr2.find((element) => {
            return element.id === el.id
          })
        })
        return res
      }

      this.item_groups = filterByReference(
        this.itemGroupsFetch,
        this.formData.item_groups
      )
      this.item_group = null

      for (var i = this.formData.items.length - 1; i >= 0; --i) {
        if (this.formData.items[i].item_group_id == item.id) {
          this.formData.items.splice(i, 1)
        }
      }
      // console.log('Format Taxes', this.formData.taxes)
    },
    filterDuplicate(arrayWithDuplicates) {
      const uniqByProp_map = (prop) => (arr) =>
        Array.from(
          arr
            .reduce(
              (acc, item) => (
                item && item[prop] && acc.set(item[prop], item), acc
              ), // using map (preserves ordering)
              new Map()
            )
            .values()
        )

      // usage (still the same):

      const uniqueById = uniqByProp_map('id')

      const unifiedArray = uniqueById(arrayWithDuplicates)
      return unifiedArray
    },

    filterByReference(arr1, arr2) {
      let res = []
      res = arr1.filter((el) => {
        return !arr2.find((element) => {
          return element.id === el.id
        })
      })
      return res
    },
    itemGroupSelected(val) {
      let vm = this
      const isId = (element) => element.id == val.id

      const index = vm.formData.item_groups.findIndex(isId)
      if (index == -1) {
        vm.formData.item_groups.push(val)
      } else {
        window.toastr['error']('This item group was already selected')
        return false
      }

      vm.item_groups = vm.filterByReference(
        vm.itemGroupsFetch,
        vm.formData.item_groups
      )
      vm.formData.item_groups.forEach((item_group) => {
        item_group.items.forEach((item) => {
          item.item_id = item.id
          item.id = Guid.raw()
          ;(item.discount_type = 'fixed'),
            (item.end_period_act = false),
            (item.end_period_number = 1),
            (item.quantity = 1),
            (item.discount_val = 0),
            (item.discount = 0),
            (item.total = item.price),
            (item.totalTax = 0),
            (item.totalSimpleTax = 0),
            (item.totalCompoundTax = 0),
            (item.tax = 0),
            (item.item_group_id = item_group.id)
          item.taxes = [{ ...TaxStub, id: Guid.raw() }]
          vm.formData.items.push(item)
        })
        vm.formData.items = this.filterDuplicate(vm.formData.items)
      })
      /* console.log("Prueba itemGroupSelected ",vm.formData.items);
      return */
      setTimeout(() => {
        this.item_group = null
      }, 100)
    },
    taxSeleted(val) {
      const isId = (element) => element.id == val.id
      const index = this.formData.taxes.findIndex(isId)
      //console.log('find tax', index)
      if (index == -1) {
        this.formData.taxes.push(val)
        this.paralelo.push(val)
      } else {
        window.toastr['error']('This tax was already selected')
        return false
      }

      this.taxes = this.filterByReference(this.taxesFetch, this.formData.taxes)
      setTimeout(() => {
        this.tax = null
      }, 100)
    },
    taxCdrSeleted(val) {
      const isId = (element) => element.id == val.id
      const index = this.formData.taxesCdr.findIndex(isId)
      if (index == -1) {
        this.formData.taxesCdr.push(val)
        this.paraleloTaxesCdr.push(val)
      } else {
        window.toastr['error']('This tax was already selected')
        return false
      }

      this.taxesCdr = this.filterByReference(
        this.taxesFetchcdr,
        this.formData.taxesCdr
      )
      setTimeout(() => {
        this.taxCdr = null
      }, 100)
    },
    groupLeftTaxSeleted(val) {
      const isId = (element) => element.id == val.id
      const index = this.formData.groupLeftTax.findIndex(isId)
      if (index == -1) {
        this.formData.groupLeftTax.push(val)
      } else {
        window.toastr['error']('This group Tax was already selected')
        return false
      }

      this.groupLeftTax = this.filterByReference(
        this.groupLeftTaxFetch,
        this.formData.groupLeftTax
      )

      setTimeout(() => {
        this.groupLeftTaxModel = null
      }, 100)
    },
    groupTaxSeleted(val) {
      const vm = this
      let taxC = []
      const isId = (element) => element.id == val.id
      const index = vm.groupLeftTax.findIndex(isId)

      if (index != -1) {
        // vm.formData.taxes = vm.groupTaxes[index].tax_groups_tax_types; ??
        vm.groupTaxes[index].tax_groups_tax_types.forEach((tax) => {
          if (!tax.for_cdr) {
            const found = !this.paralelo.find(
              (element) => element.id === tax.id
            )

            if (found) {
              taxC = { ...tax, id: tax.pivot.taxes_id }
              this.formData.taxes.push(taxC)
              this.paralelo.push(taxC)
            }
          }
        })
        vm.taxes = vm.filterByReference(
          this.taxes,
          vm.groupTaxes[index].tax_groups_tax_types
        )
      } else {
        vm.formData.taxes = []
        this.paralelo = []
      }

      // this.groupLeftTax = this.filterByReference(this.groupLeftTaxFetch, this.formData.groupLeftTax)
      /*
      setTimeout(() => {
        vm.groupTax = null
      }, 100)
      */
    },

    groupTaxCdrSeleted(val) {
      const vm = this
      let taxC = []
      const isId = (element) => element.id == val.id
      const index = vm.groupLeftTax.findIndex(isId)
      if (index != -1) {
        vm.groupTaxes[index].tax_groups_tax_types.forEach((tax) => {
          if (tax.for_cdr) {
            taxC = { ...tax, id: tax.pivot.tax_types_id }
            this.formData.taxesCdr.push(taxC)
            this.paraleloTaxesCdr.push(taxC)
          }
        })
        this.formData.taxesCdr = vm.filterDuplicate(this.formData.taxesCdr)
        this.paraleloTaxesCdr = [...this.formData.taxesCdr]

        vm.taxesCdr = vm.filterByReference(
          this.taxesCdr,
          vm.groupTaxes[index].tax_groups_tax_types
        )
      } else {
        vm.formData.taxesCdr = []
        this.paraleloTaxesCdr = []
      }

      setTimeout(() => {
        vm.groupTax = null
      }, 100)
    },

    async DropServerSeleted(val) {
      this.initLoad = true
      //  console.log('valor drops', val)
      let response = await this.fetchPbxServer(val.id)
      //console.log('dropsselected', response.data.pbxServer)
      this.formData.dropdown_server = response.data.pbxServer
      this.national_dialing_code_selected =
        response.data.pbxServer.national_dialing_code
      this.international_dialing_code_selected =
        response.data.pbxServer.international_dialing_code
      this.national_dialing_code = response.data.pbxServer.national_dialing_code
      this.international_dialing_code =
        response.data.pbxServer.international_dialing_code

      this.initLoad = false

      this.formData.cdrStatus = response.data.pbxServer.cdr_status.map(
        (cdr) => cdr.status
      )
    },
    async fetchTax() {
      let data = {
        orderByField: 'created_at',
        orderBy: 'asc',
        limit: 1000,
      }

      let response = await this.fetchTaxTypes(data)

      // console.log('fetchTax', response.data.taxTypes.data)
      let taxes = response.data.taxTypes.data
      taxes.forEach((element) => {
        element.name_por = `${element.name} - ${element.percent}%`
      })

      this.taxes = taxes.filter((tax) => tax.for_cdr === 0)
      this.taxesCdr = taxes.filter((tax) => tax.for_cdr === 1)
      this.taxesFetch = this.taxes
      this.taxesFetchcdr = this.taxesCdr
    },
    async fetchInitialCustomDestination() {
      let inftemp = []
      let cargarCustomD = await this.CargarCustomDestination()

      inftemp = [...cargarCustomD.data.internacional]
      this.prefixrate_groups = [
        ...inftemp.filter((ex) => ex.type === 'Inbound'),
      ]
      this.prefixrate_groups_outboubd = [
        ...inftemp.filter((ex) => ex.type === 'Outbound'),
      ]
    },
    async fetchInitialItemGroups() {
      let data = {
        orderByField: 'created_at',
        orderBy: 'asc',
      }

      let res = await this.fetchItemGroups(data)

      this.item_groups = res.data.response
      this.itemGroupsFetch = res.data.response
    },
    async loadPbxServers() {
      this.initLoad = true
      let ext = await this.fetchExtensionsnp()
      //  console.log("DIDsssss")
      let extdid = await this.fetchDIDsnp()
      //  console.log("DIDsssss", extdid.data.profileDID)
      this.did_ar = [...extdid.data.profileDID]
      //    console.log("DIDsssss2")
      this.extension_ar = [...ext.data.profileExtensions]
      let res = await window.axios.get('/api/v1/pbx/servers', {
        params: { limit: 100000, status: 'A' },
      })
      if (res) {
        this.dropdown_server = res.data.pbxServers.data
      }

      this.initLoad = false
    },

    async loadPackage() {
      let vm = this
      let res = await this.fetchPackage(this.$route.params.id)
      // console.log('LOAD PACKAGE CARGANDO EL PAQUETE: ', res)
      let ext = await this.fetchExtensionsnp()
      let extdid = await this.fetchDIDsnp()
      this.did_ar = [...extdid.data.profileDID]
      this.extension_ar = [...ext.data.profileExtensions]

      const data = {
        module: 'pbx_packages',
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if (permissions.super_admin == false) {
        if (permissions.exist == false) {
          this.$router.push('/admin/dashboard')
        } else {
          const modulePermissions = permissions.permissions[0]
          if (modulePermissions.create == 0 && this.isEdit == false) {
            this.$router.push('/admin/dashboard')
          } else if (modulePermissions.update == 0 && this.isEdit == true) {
            this.$router.push('/admin/dashboard')
          }
        }
      }

      if (this.isEdit || this.isCopy) {
        // load tax_groups
        this.groupTax = res.data.tax_groups

        // load Custom Destinations Groups
        this.prefixrate_groups_id = res.data.custom_destination_groups.filter(
          (group) => group.type == 'Inbound'
        )

        this.prefixrate_groups_outbound_id =
          res.data.custom_destination_groups.filter(
            (group) => group.type == 'Outbound'
          )

        this.formData.package_discount = res.data.pbxPackage.package_discount
        this.formData.value_discount = res.data.pbxPackage.value_discount
        this.discount = res.data.pbxPackage.discount
        this.value_discount = res.data.pbxPackage.value_discount
        /* if (res.data.pbxPackage.type || this.value_discount) {
          this.showSelectdiscounts = false
        } */
        if (this.formData.package_discount) {
          this.showSelectdiscounts = false
        }
      }

      this.formData = res.data.pbxPackage
      let {
        qty_available,
        client_limit,
        html,
        text,
        tax_types,
        tax_types_cdr,
        status,
        extensions,
        status_payment,
        did,
        package_discount,
        call_ratings,
        unmetered,
        automatic_suspension,
        all_cdrs,
        prefixrate_groups_id,
        prefixrate_groups_outbound_id,
        custom_app_rate,
        discount_term,
        apply_tax_type,
        discount_time_units,
        discount_end_date,
        discount_start_date,
        discount_term_type,
        modify_server,
        pbx_server_id,
        server,
        national_dialing_code,
        international_dialing_code,
        rate_per_minutes,
        minutes_increments,
        type_time_increment,
        template_did_id,
        template_extension_id,
        items,
        item_groups,
        bandServices,
        type,
        avalara_options,
        update_child_services,
        avalara_price,
        avalara_extension,
        avalara_did,
        avalara_callrating,
        avalara_items,
        rate,
        suspension_type,
        avalara_custom_app_rate_items,
        avalara_services_price_item,
        avalara_additional_charges_item,
        avalara_custom_app_rate_item_id,
        avalara_services_price_item_id,
        avalara_additional_charges_item_id,
        avalara_extension_item_id,
        avalara_bundle,
        bundle_transaction,
        bundle_service,
        avalara_did_item_id,
        cdr_items_id,
        custom_destinations_item_id,
        inter_custom_destinations_item_id,
        toll_free_custom_destinations_item_id,
      } = res.data.pbxPackage

      this.formData.rate = parseFloat(rate)

      if (avalara_options) {
        await this.fetchAvalaraItems()
      }

      if (suspension_type) {
        this.suspension_type = this.suspensionTypeOptions.find(
          (ex) => ex.value === suspension_type
        )
      }

      if (bandServices > 0 && this.isEdit) {
        this.bandServices = true
        window.toastr['success'](
          'Some attributes cannot be edited because services have already been created'
        )
        /* window.toastr['success']("Algunos atributos no pueden ser editados porque ya se crearon servicios"); */
      }

      if (template_did_id) {
        this.did_i = this.did_ar.find((extd) => extd.id === template_did_id)
        //console.log('did_id', this.did_i)
      }

      if (template_extension_id) {
        this.extension_i = this.extension_ar.find(
          (ex) => ex.id === template_extension_id
        )
      }

      /*
      if (prefixrate_groups_id) {        
        this.formData.prefixrate_groups_id = this.prefixrate_groups.find(
          (ex) => ex.id === prefixrate_groups_id
        )
      }
      if (prefixrate_groups_outbound_id) {
        this.formData.prefixrate_groups_outbound_id =
          this.prefixrate_groups_outboubd.find(
            (ex) => ex.id === prefixrate_groups_outbound_id
          )
      }
      */

      if (apply_tax_type) {
        this.formData.apply_tax_type = this.apply_tax_type_options.find(
          (ex) => ex.value === apply_tax_type
        )
      }

      if (package_discount) {
        this.formData.discount_term_type = this.discount_term_type.find(
          (_type) => discount_term_type === _type.value
        )
        if (discount_term_type === 'U') {
          this.formData.discount_term = this.discount_term.find(
            (_term) => discount_term === _term.value
          )
          this.formData.discount_time_units = discount_time_units
        } else {
          this.formData.discount_start_date =
            moment(discount_start_date).format('YYYY-MM-DD')

          this.formData.discount_end_date =
            moment(discount_end_date).format('YYYY-MM-DD')
          this.formData.discount_term = this.discount_term.find(
            (_term) => 'days' === _term.value
          )
        }
      } else {
        this.formData.discount_term_type = this.discount_term_type.find(
          (_type) => discount_term_type === _type.value
        )
      }

      //console.log('taxes', this.taxes)
      const filterByReference = (arr1, arr2) => {
        let res = []
        res = arr1.filter((el) => {
          return !arr2.find((element) => {
            return element.id === el.id
          })
        })
        return res
      }
      this.avalara_did_item_id = this.avalaraItemsOptions.find(
        (item) => item.id === avalara_did_item_id
      )
      this.avalara_extension_item_id = this.avalaraItemsOptions.find(
        (item) => item.id === avalara_extension_item_id
      )

      this.cdr_items_id = this.avalaraItemsOptions.find(
        (item) => item.id === cdr_items_id
      )

      this.custom_destinations_item_id = this.avalaraItemsOptions.find(
        (item) => item.id === custom_destinations_item_id
      )
      this.inter_destinations_item_id = this.avalaraItemsOptions.find(
        (item) => item.id === inter_custom_destinations_item_id
      )
      this.toll_free_destinations_item_id = this.avalaraItemsOptions.find(
        (item) => item.id === toll_free_custom_destinations_item_id
      )
      this.avalara_custom_app_rate_item_id = this.avalaraItemsOptions.find(
        (item) => item.id === avalara_custom_app_rate_item_id
      )
      this.avalara_services_price_item_id = this.avalaraItemsOptions.find(
        (item) => item.id === avalara_services_price_item_id
      )
      this.avalara_additional_charges_item_id = this.avalaraItemsOptions.find(
        (item) => item.id === avalara_additional_charges_item_id
      )

      this.taxes = filterByReference(this.taxes, tax_types)

      this.taxesCdr = filterByReference(this.taxesCdr, tax_types_cdr)
      // this.groupLeftTax = filterByReference(this.taxes, groupLeftTax)
      // console.log('filterByReference', filterByReference(this.taxes, tax_types));

      this.qty_available = qty_available
      this.client_limit = client_limit
      this.national_dialing_code = national_dialing_code
      this.national_dialing_code_selected = national_dialing_code
      this.international_dialing_code = international_dialing_code
      this.international_dialing_code_selected = international_dialing_code
      this.rate_per_minutes_selected = parseFloat(rate_per_minutes)
      this.minutes_increments_selected = minutes_increments
      this.minutes_increments = minutes_increments
      this.rate_per_minutes = parseFloat(rate_per_minutes)
      this.formData.status = this.status.filter(
        (element) => element.value == status
      )[0]
      this.formData.status_payment = this.status_payment.filter(
        (element) => element.value == status_payment
      )[0]
      this.formData.type_time_increment = this.type_time_increment.filter(
        (element) => element.value == type_time_increment
      )[0]

      this.formData.type = this.discounts.filter(
        (element) => element.value === type
      )[0]
      this.formData.html = html
      this.formData.text = text
      this.formData.taxes = tax_types
      this.formData.taxesCdr = tax_types_cdr || []
      this.paralelo = [...this.formData.taxes]

      this.paraleloTaxesCdr = [...this.formData.taxesCdr]
      this.extensions = extensions == 1 ? true : false
      this.did = did == 1 ? true : false
      this.package_discount = package_discount == 1 ? true : false
      this.call_ratings = call_ratings == 1 ? true : false

      this.avalara_options = avalara_options == 1 ? true : false
      this.update_child_services = update_child_services ? true : false
      this.avalara_price = avalara_price == 1 ? true : false
      this.avalara_extension = avalara_extension == 1 ? true : false
      this.avalara_did = avalara_did == 1 ? true : false
      this.avalara_callrating = avalara_callrating == 1 ? true : false
      this.avalara_items = avalara_items == 1 ? true : false
      this.avalara_custom_app_rate_items =
        avalara_custom_app_rate_items == 1 ? true : false
      ;(this.unmetered = unmetered == 1 ? true : false),
        (this.automatic_suspension = automatic_suspension == 1 ? true : false),
        (this.modify_server = modify_server == 1 ? true : false)
      this.all_cdrs = all_cdrs === 1 ? true : false

      this.avalara_services_price_item =
        avalara_services_price_item == 1 ? true : false
      this.avalara_additional_charges_item =
        avalara_additional_charges_item == 1 ? true : false

      this.isRequestOnGoing = false

      let itemsArray = []
      this.formData.items = []
      items.forEach((item) => {
        /* if (item.taxes.length == 0) {
          item.taxes = [{ ...TaxStub, id: Guid.raw() }]
        } */
        item.quantity = item.quantity
        // item.id = item.id
        item.item_id = item.items_id
        item.item_group_id = item.item_group_id
        item.price = item.price
        item.discount_type = item.discount_type
        item.discount = item.discount
        item.discount_val = item.discount_val
        item.tax = item.tax
        item.description = item.description
        item.package_id = item.package_id
        item.total = item.total
        item.totalTax = 0
        item.totalSimpleTax = 0
        item.totalCompoundTax = 0
        item.no_taxable = item.no_taxable

        if (!item.taxes) {
          this.formData.items.push({
            ...item,
            id: Guid.raw(),
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          })
        } else {
          item.taxes.push({ ...TaxStub, id: Guid.raw() })
          this.formData.items.push({
            ...item,
          })
        }
        itemsArray.push(item)
      })

      let arrayItemGroups = [],
        arrayItemGroupsItems = [],
        arrayItemGroupsSelect = []

      if (item_groups.length > 0) {
        item_groups.forEach((itemG) => {
          for (let i = 0; i < vm.itemGroupsFetch.length; i++) {
            const element = vm.itemGroupsFetch[i]
            if (element.id === itemG.item_group_id) {
              for (let h = 0; h < itemsArray.length; h++) {
                const item = itemsArray[h]
                if (item.item_group_id == itemG.item_group_id) {
                  arrayItemGroupsItems.push(item)
                }
              }
              element.items = arrayItemGroupsItems
              arrayItemGroups.push(element)
              arrayItemGroupsItems = []
            } else {
              arrayItemGroupsSelect.push(element)
            }
          }
        })
        vm.formData.item_groups = arrayItemGroups

        var array = []
        for (var i = 0; i < vm.itemGroupsFetch.length; i++) {
          var igual = false
          for (var j = 0; (j < item_groups.length) & !igual; j++) {
            if (vm.itemGroupsFetch[i]['id'] == item_groups[j]['item_group_id'])
              igual = true
          }
          if (!igual) array.push(vm.itemGroupsFetch[i])
        }

        vm.item_groups = array
      }

      // dropdown server
      // verificar si el server existe en el arreglo de servers
      if (server) {
        const SearchServerIndex = this.dropdown_server.findIndex(
          (element) => element.id == server.id
        )
        if (SearchServerIndex > -1) {
          this.formData.dropdown_server = JSON.parse(
            JSON.stringify(this.dropdown_server[SearchServerIndex])
          )
        } else {
          this.dropdown_server.push(server)
          this.formData.dropdown_server = JSON.parse(JSON.stringify(server))
        }
      }
    },
    slideChangeAvalaraBundle(value) {
      if (value) {
        this.avalara_items = false
        this.avalara_services_price_item = false
        this.avalara_additional_charges_item = false
        this.avalara_extension = false
        this.avalara_did = false
        this.avalara_callrating = false
        this.avalara_custom_app_rate_items = false
      }
    },
    async loadGroupMembership() {
      this.groupRight = await this.fetchGroupMembership()
    },
    async loadGroupTax() {
      this.groupTaxes = await this.fetchGroupTaxMembership()
      this.groupLeftTax = await this.fetchGroupTaxMembership()
      this.groupLeftTaxFetch = await this.fetchGroupTaxMembership()
    },
    async submitPackage() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      if (this.avalara_options && this.formData.avalaraBundle) {
        if (
          this.formData.bundleTransaction == null ||
          this.formData.bundleTransaction <= 0
        ) {
          window.toastr['error']('Bundle Transaction must be greater than 0')
          return false
        }
        if (
          this.formData.bundleService == null ||
          this.formData.bundleService <= 0
        ) {
          window.toastr['error']('Bundle Service must be greater than 0')
          return false
        }
      }

      if (this.formData.package_discount) {
        if (this.formData.discount_term_type.value === 'D') {
          if (
            this.formData.discount_end_date < this.formData.discount_start_date
          ) {
            window.toastr['error']('confirm date from')
            return true
          } else if (
            this.formData.discount_end_date ===
            this.formData.discount_start_date
          ) {
            window.toastr['error']('confirm date from and to')
            return true
          } else if (
            this.formData.discount_end_date != null &&
            this.formData.discount_start_date === null
          ) {
            window.toastr['error']('confirm date from')
            return true
          } else if (
            this.formData.discount_start_date != null &&
            this.formData.discount_end_date === null
          ) {
            window.toastr['error']('confirm date to')
            return true
          }
        }
      }

      //se iguala formData con formdata2
      this.formData2.all_cdrs = this.formData.all_cdrs
      this.formData2.apply_tax_type = this.formData.apply_tax_type
      this.formData2.avalaraBundle = this.formData.avalaraBundle
      this.formData2.pbx_package_name = this.formData.pbx_package_name
      this.formData2.status_payment = this.formData.status_payment
      this.formData2.status = this.formData.status
      this.formData2.cdrStatus = this.formData.cdrStatus
      this.formData2.dropdown_server = this.formData.dropdown_server
      this.formData2.apply_tax_type = this.formData.apply_tax_type
      this.formData2.rate = this.formData.rate
      this.formData2.item_groups = this.formData.item_groups
      this.formData2.items = this.formData.items
      this.formData2.itemsPre = this.formData.itemsPre
      this.formData2.tax_groups = this.formData.tax_groups
      this.formData2.taxes = this.formData.taxes
      this.formData2.taxesCdr = this.formData.taxesCdr
      this.formData2.package_discount = this.formData.package_discount
      this.formData2.discount_end_date = this.formData.discount_end_date
      this.formData2.discount_start_date = this.formData.discount_start_date

      this.formData2.discount_term_type =
        this.formData.discount_term_type.value ||
        this.formData.discount_term_type
      /* ;(this.formData.discount_term_type = this.formData.discount_term_type.value), */
      this.formData2.discount_term =
        this.formData.discount_term.value || this.formData.discount_term

      if (this.value_discount > 100) {
        window.toastr['error']('Discount value cannot be greater than 100')
        return false
      }

      if (this.formData.dropdown_server == null) {
        this.formData2.dropdown_server = { id: null }
      }
      if (this.formData.type_time_increment == null) {
        this.formData2.type_time_increment = { value: null }
      }

      if (this.formData.type == null) {
        this.formData2.type = { value: 'percentage' }
      }

      this.formData2.status =
        this.formData.status.value ||
        this.status.filter(
          (element) => element.value == this.formData.status
        )[0]

      this.formData2.suspension_type = this.suspension_type?.value

      /*
      this.formData.prefixrate_groups_id = this.formData.prefixrate_groups_id
      //  ? this.formData.prefixrate_groups_id.id
      //  : null

      this.formData.prefixrate_groups_outbound_id = this.formData.prefixrate_groups_outbound_id
        //? this.formData.prefixrate_groups_outbound_id.id
        //: null
      */
      this.formData2.custom_destination_groups = [
        ...this.prefixrate_groups_id,
        ...this.prefixrate_groups_outbound_id,
      ]
      //

      this.formData2.custom_app_rate_id = this.formData.custom_app_rate
        ? this.formData.custom_app_rate.id
        : null
      this.formData2.status_payment =
        this.formData.status_payment.value ||
        this.status_payment.filter(
          (element) => element.value == this.formData.status_payment
        )[0]
      this.formData2.qty_available =
        this.qty_available == '' ? 0 : this.qty_available
      this.formData2.client_limit =
        this.client_limit == '' ? 0 : this.client_limit

      this.formData2.extensions = this.extensions ? 1 : 0

      if (this.formData2.extensions) {
        if (this.extension_i) {
          this.formData2.template_extension_id = this.extension_i.id
        } else {
          window.toastr['error']('Confirm active extensions')
          return false
        }
      } else {
        this.formData2.template_extension_id = 0
      }
      /* this.formData.extension_id = this.extension_i ? this.extension_i.id : 0 */
      this.formData2.did = this.did ? 1 : 0
      if (this.formData2.did) {
        if (this.did_i) {
          this.formData2.template_did_id = this.did_i.id
        } else {
          window.toastr['error']('Confirm active DID')
          return false
        }
      } else {
        this.formData2.template_did_id = 0
      }

      this.formData2.call_ratings = this.call_ratings ? 1 : 0
      this.formData2.unmetered = this.unmetered ? 1 : 0

      this.formData2.avalara_options = this.avalara_options ? 1 : 0
      this.formData2.update_child_services = this.update_child_services ? 1 : 0
      this.formData2.avalara_price = this.avalara_price ? 1 : 0

      this.formData2.avalara_extension = this.avalara_extension ? 1 : 0
      this.formData2.avalara_did = this.avalara_did ? 1 : 0
      this.formData2.avalara_callrating = this.avalara_callrating ? 1 : 0
      this.formData2.avalara_items = this.avalara_items ? 1 : 0
      this.formData2.avalara_custom_app_rate_items = this
        .avalara_custom_app_rate_items
        ? 1
        : 0
      this.formData2.avalara_services_price_item = this
        .avalara_services_price_item
        ? 1
        : 0
      this.formData2.avalara_additional_charges_item = this
        .avalara_additional_charges_item
        ? 1
        : 0

      this.formData2.automatic_suspension = this.automatic_suspension ? 1 : 0
      this.formData2.modify_server = this.modify_server ? 1 : 0
      this.formData2.dropdown_server = this.formData.dropdown_server.id
      this.formData2.all_cdrs = this.all_cdrs

      this.formData2.national_dialing_code = this.national_dialing_code_selected
      this.formData2.international_dialing_code =
        this.international_dialing_code_selected
      this.formData2.rate = parseFloat(this.formData.rate)
      this.formData2.inclusive_minutes = this.formData.inclusive_minutes

      let valor = this.rate_per_minutes_selected
        ? this.rate_per_minutes_selected
        : 0

      this.formData2.rate_per_minutes =
        valor.length > 1
          ? this.rate_per_minutes_selected.replace(',', '.')
          : this.rate_per_minutes_selected
      this.formData2.minutes_increments = this.minutes_increments_selected
      this.formData2.type_time_increment =
        this.formData.type_time_increment.value
      /* this.formData.discounts = this.formData.discounts.value */
      this.formData2.type = this.formData.type.value
      this.formData2.value_discount = this.value_discount

      if (this.formData.items.length > 0) {
        this.formData2.items.forEach((item, index) => {
          if (item.name == '' || item.name == null) {
            this.formData.items.splice(index, 1)
          }
        })
      }
      this.formData2.avalara_extension_item_id =
        this.avalara_extension_item_id?.id
      this.formData2.avalara_did_item_id = this.avalara_did_item_id?.id
      this.formData2.cdr_items_id = this.cdr_items_id?.id
      this.formData2.custom_destinations_item_id =
        this.custom_destinations_item_id?.id
      ;(this.formData2.inter_custom_destinations_item_id =
        this.inter_destinations_item_id?.id),
        (this.formData2.toll_free_custom_destinations_item_id =
          this.toll_free_destinations_item_id?.id),
        (this.formData2.avalara_custom_app_rate_item_id =
          this.avalara_custom_app_rate_item_id?.id)
      this.formData2.avalara_services_price_item_id =
        this.avalara_services_price_item_id?.id
      this.formData2.avalara_additional_charges_item_id =
        this.avalara_additional_charges_item_id?.id

      this.formData2.apply_tax_type =
        this.formData.apply_tax_type.value || this.formData.apply_tax_type

      // tax_groups
      this.formData2.tax_groups = this.groupTax
      // console.log('FormData', this.formData)
      //console.log('FormData2', this.formData2)
      //return

      let text = ''
      if (this.isEdit) {
        text = 'packages.edit_packages_text'
      } else {
        text = 'packages.create_packages_text'
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc(text),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          try {
            let res
            this.isLoading = true

            if (this.isEdit) {
              this.formData2.id = this.$route.params.id
              res = await this.updatePackage(this.formData2)
              window.toastr['success'](res.data.message)
              this.$router.push('/admin/corePBX/packages')
              return true
            } else {
              res = await this.addPackage(this.formData2)
              this.isLoading = false
              if (!this.isEdit) {
                window.toastr['success'](res.data.message)
                this.$router.push('/admin/corePBX/packages')
                return true
              }
            }
          } catch (error) {
            window.toastr['error'](error.response.data.pbxPackage)
            this.status = [
              {
                value: 'A',
                text: 'Active',
              },
              {
                value: 'I',
                text: 'Inactive',
              },
              {
                value: 'R',
                text: 'Restricted',
              },
            ]
            this.formData.status = {
              value: 'A',
              text: 'Active',
            }
            this.isLoading = false
            return false
          }
        }
      })
    },

    slideChange() {
      this.national_dialing_code_selected = this.add_call_rating
        ? this.national_dialing_code
        : ''
      this.international_dialing_code_selected = this.add_call_rating
        ? this.international_dialing_code
        : ''
      this.national_dialing_code_selected = this.add_call_rating
        ? this.national_dialing_code
        : ''
      this.international_dialing_code_selected = this.add_call_rating
        ? this.international_dialing_code
        : ''
    },
    async avalaraOptionsChange(val) {
      try {
        if (val) {
          await this.fetchAvalaraItems()
        }
        if (val && !this.isEdit) {
          const res = await this.fetchAvalaraDefault()

          console.log('Response items', res)

          this.avalara_did_item_id = this.avalaraItemsOptions.find(
            (item) => item.id === res.data.data.item_did_id
          )
          this.avalara_extension_item_id = this.avalaraItemsOptions.find(
            (item) => item.id === res.data.data.item_extension_id
          )

          this.cdr_items_id = this.avalaraItemsOptions.find(
            (item) => item.id === res.data.data.item_cdr_id
          )

          this.custom_destinations_item_id = this.avalaraItemsOptions.find(
            (item) => item.id === res.data.data.item_international_id
          )
          this.inter_destinations_item_id = this.avalaraItemsOptions.find(
            (item) => item.id === res.data.data.item_international_id
          )
          this.toll_free_destinations_item_id = this.avalaraItemsOptions.find(
            (item) => item.id === res.data.data.item_toll_free_id
          )
          this.avalara_custom_app_rate_item_id = this.avalaraItemsOptions.find(
            (item) => item.id === res.data.data.custom_app_rate_item_id
          )
          this.avalara_services_price_item_id = this.avalaraItemsOptions.find(
            (item) => item.id === res.data.data.services_price_item_id
          )
          this.avalara_additional_charges_item_id =
            this.avalaraItemsOptions.find(
              (item) => item.id === res.data.data.additional_charges_item_id
            )
        }
      } catch (e) {}
    },
    async getStatusModuleAvalara() {
      const response = await this.checkStatusAvalara()

      const modules = ['Avalara']
      const modulesArray = await this.getModules(modules)

      const moduleAvalara = modulesArray.modules.find(
        (element) => element.name === 'Avalara'
      )

      if (moduleAvalara && moduleAvalara.status == 'A') {
        if (response.data.success) {
          this.isAvalaraAvailable = true
        }
      }
    },

    cancelForm() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.cancel_text'),
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          this.$router.go(-1)
        }
      })
    },
  },
  created() {
    this.loadPbxServers()
    this.getStatusModuleAvalara()
  },

  mounted() {
    this.getCustomAppRate()
    this.fetchTax()
    this.fetchInitialItemGroups()
    this.fetchInitialItems()
    this.fetchInitialCustomDestination()
    if (this.isEdit || this.isCopy) {
      this.isRequestOnGoing = true
      this.loadPackage()
    }
    this.loadGroupMembership()
    this.loadGroupTax()
    setTimeout(() => {
      this.onSelectApplyTaxType(this.formData.apply_tax_type)
    }, 500)
  },
  validations() {
    return {
      qty_available: {
        numeric,
      },
      client_limit: {
        numeric,
      },
      minutes_increments_selected: {
        numeric,
      },
      value_discount: {
        numeric,
      },
      formData: {
        pbx_package_name: {
          required,
          minLength: minLength(3),
        },
        status: {
          required,
        },
        client_limit: {
          numeric,
        },
        discount_time_units: {
          between: between(0, 1000000),
        },
        rate: {
          minValue: minValue(0.0),
        },
        inclusive_minutes: {
          numeric,
        },
        status_payment: {
          required,
        },
        dropdown_server: {
          required,
        },
      },
    }
  },
}
</script>


<style lang="scss">