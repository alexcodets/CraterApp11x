<template class="p-0">
  <base-page>
    <!-- <sw-page-header :title="$t('core_pos.dashboard.title')">
     
    </sw-page-header> -->
    <div class="grid grid-cols-10 rounded bg-white shadow overflow-auto" style="height:90vh;">
      <div class="flex flex-col col-span-4 p-3 overflow-auto">
        <div style="height:15%;" class="flex ">
          <div class="w-1/2 mr-2">
            <sw-input-group :error="customerError" :label="$t('core_pos.dashboard.customers')" required>
              <sw-select @input="changeCustomer" v-model="customer" :options="customer_options" :searchable="true"
                :show-labels="false" :allow-empty="false" :placeholder="$t('core_pos.dashboard.search_customer')"
                class="mt-2" label="name" track-by="id" :tabindex="11" />
            </sw-input-group>
          </div>
          <div class=" w-1/2 mr-2">
            <sw-input-group :label="$t('core_pos.select_cash_register')" required>
              <sw-select @input="changeCashRegister" v-model="cash_register_selected" :options="cash_register_options"
                :searchable="true" :show-labels="false" :allow-empty="false"
                :placeholder="$t('core_pos.select_cash_register')" class="mt-2" label="name" track-by="id"
                :tabindex="11" />
            </sw-input-group>

          </div>
          <div class=" w-1/2">
            <sw-input-group :label="$t('core_pos.store')" required>
              <sw-select @input="changeStore" v-model="store_selected" :options="store_options"
                :searchable="true" :show-labels="false" :allow-empty="false"
                :placeholder="$t('core_pos.store')" class="mt-2" label="name" track-by="id"
                :tabindex="11" />
            </sw-input-group>

          </div>
        </div>
        <div style="height:45%;" class="p-3 px-1 overflow-auto ">
          <table ref="table" class="table-fixed w-full text-center border-spacing-y-px">
            <thead>
              <tr>
                <th width="25%"> {{ $t('core_pos.dashboard.table.product') }} </th>
                <th width="23%"> {{ $t('core_pos.dashboard.table.quantity') }} </th>
                <th width="20%"> {{ $t('core_pos.dashboard.table.price') }} </th>
                <th width="20%"> {{ $t('core_pos.dashboard.table.subtotal') }} </th>
                <th width="5%"></th>

              </tr>
            </thead>
            <tbody class="">
              <tr class="mt-2" height="50px" v-for="item in shopping_cart" :key="item.id">
                <td> {{ item.name }} </td>
                <td>
                  <span class="text-sm w-1/12">
                    <sw-button :disabled="item.quantity == 0" class="ml-2 w-1/12" variant="primary-outline"
                      @click="decrementQuantity(item)">
                      -
                    </sw-button>
                  </span>
                  {{ item.quantity }}
                  <span class="text-sm w-1/12">
                    <sw-button class="ml-2 w-1/12" variant="primary-outline" @click="incrementQuantity(item)">
                      +
                    </sw-button>
                  </span>
                </td>
                <td>
                  <span v-html="$utils.formatMoney(item.price, defaultCurrency)" />
                </td>
                <td>
                  <span v-html="$utils.formatMoney(item.sub_total, defaultCurrency)" />
                </td>
                <td>
                  <div @click="removeItemShoppingCart(item)">
                    <trash-icon class="h-5 mr-3 text-gray-600 text-xl" />
                  </div>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
        <div style="height:30%;" class="grid grid-cols-2 overflow-auto">
          <!-- DIV for use with inputs -->
          <div class="flex flex-col items-center justify-center">
            <!-- Taxes -->
            <div class=" items-center w-full mt-2">
              <label class="text-xs font-semibold leading-5 text-gray-500 uppercase">
                {{ $t('core_pos.tax') }}
              </label>
              <sw-input-group style="width: 100%">
                <sw-select open-direction="below" @input="calculateTaxesSelected" v-model="tax_selected"
                  :options="tax_options" :searchable="true" :show-labels="false" :allow-empty="true"
                  :placeholder="$t('core_pos.dashboard.search_customer')" class="mt-2" label="name" track-by="id"
                  :multiple="true">
                  <template slot-scope="row">
                    <span>{{ row.name }} - {{ row.percent }}%</span>
                  </template>
                </sw-select>
              </sw-input-group>

            </div>

            <!-- End taxes -->

            <!-- DISCOUNT -->
            <div class=" items-center  w-full mt-2">
              <label class="text-xs font-semibold leading-5 text-gray-500 uppercase">
                {{ $t('core_pos.discount') }}
              </label>
              <div class="flex" style="width: 100%" role="group">
                <sw-input v-model="discount" class="border-r-0 rounded-tr-sm rounded-br-sm" @keyup="calculateDiscount" />
                <sw-dropdown position="bottom-end">
                  <sw-button slot="activator" type="button" data-toggle="dropdown" size="discount" aria-haspopup="true"
                    aria-expanded="false" style="height: 43px" variant="white">
                    <span class="flex">
                      {{
                        discount_type == 'fixed'
                        ? '$'
                        : '%'
                      }}
                      <chevron-down-icon class="h-5" />
                    </span>
                  </sw-button>

                  <sw-dropdown-item @click="selectFixed">
                    {{ $t('general.fixed') }}
                  </sw-dropdown-item>

                  <sw-dropdown-item @click="selectPercentage">
                    {{ $t('general.percentage') }}
                  </sw-dropdown-item>
                </sw-dropdown>
              </div>
            </div>
            <!-- END DISCOUNT -->
            <!-- TIPS -->
            <div class=" items-center  w-full mt-2">
              <label class="text-xs font-semibold leading-5 text-gray-500 uppercase">
                {{ $t('core_pos.tips') }}
              </label>
              <div class="flex" style="width: 100%" role="group">
                <sw-input v-model="tip" class="border-r-0 rounded-tr-sm rounded-br-sm" @keyup="calculateTip" />
                <sw-dropdown position="bottom-end">
                  <sw-button slot="activator" type="button" data-toggle="dropdown" size="tip" aria-haspopup="true"
                    aria-expanded="false" style="height: 43px" variant="white">
                    <span class="flex">
                      {{
                        tip_type == 'fixed'
                        ? '$'
                        : '%'
                      }}
                      <chevron-down-icon class="h-5" />
                    </span>
                  </sw-button>

                  <sw-dropdown-item @click="selectFixedTip">
                    {{ $t('general.fixed') }}
                  </sw-dropdown-item>

                  <sw-dropdown-item @click="selectPercentageTip">
                    {{ $t('general.percentage') }}
                  </sw-dropdown-item>
                </sw-dropdown>
              </div>
            </div>
            <!-- END TIPS -->
          </div>
          <div class="flex flex-col content-end justify-center px-5 ">
            <div class="text-right text-base">
              <span>
                {{ $t('core_pos.quantity_items') + ': ' }}
              </span>
              <span class="text-gray-700">
                {{ this.quantity_item_shopping_cart }}
              </span>
            </div>
            <div class="text-right text-base">
              <span>
                {{ $t('core_pos.subtotal') + ': ' }}
              </span>
              <span class="text-gray-700">
                <span v-html="$utils.formatMoney(this.sub_total_shopping_cart, defaultCurrency)" />
              </span>
            </div>
            <div class="text-right text-base">
              <span>
                {{ $t('core_pos.discount_total') + ': ' }}
              </span>
              <span class="text-gray-700">
                <span v-html="$utils.formatMoney(this.discount_val, defaultCurrency)" />
              </span>
            </div>
            <div class="text-right text-base">
              <span>
                {{ $t('core_pos.tip_total') + ': ' }}
              </span>
              <span class="text-gray-700">
                <span v-html="$utils.formatMoney(this.tip_val, defaultCurrency)" />
              </span>
            </div>
            <div v-if="existsTaxes" class="text-right text-base font-semibold"> {{ $t('core_pos.tax_total') }}</div>
            <div v-for="(tax, index) in taxes_list" :key="index" class="text-right text-base">
              <span>
                {{ tax.name + ' (' + tax.percent + '%): ' }}
              </span>
              <span class="text-gray-700">
                <span v-html="$utils.formatMoney(tax.amount, defaultCurrency)" />
              </span>

            </div>

            <div class="text-right text-base font-semibold">
              <span>
                {{ $t('core_pos.total') + ': ' }}
              </span>
              <span class="text-gray-700">
                <span v-html="$utils.formatMoney(this.total_shopping_cart, defaultCurrency)" />
              </span>
            </div>
          </div>
        </div>
        <div style="height:10%;" class=" flex items-center justify-center">
          <button class="h-3/4 w-20 rounded-lg flex justify-center place-items-center "
            style="background-color: rgb(246, 41, 71);" @click="clearFields">
            <!-- <p class="text-center text-xl font-bold">{{ $t('core_pos.clear_shopping_cart') }}</p> -->
            <trash-icon class="h-16 text-white  " />
          </button>
          <button class="h-3/4 w-1/3 rounded-lg ml-2" style="background-color: rgb(10, 192, 116);"
            @click="submitShoppingCart">
            <p class="text-center text-xl font-bold">{{ $t('core_pos.button_pay') }}</p>
          </button>
          <button class="h-3/4 w-20 rounded-lg ml-2 " style="background-color: rgb(255, 103, 155);"
            @click="openHoldReferenceModal">
            <p class="text-center text-xl font-bold">{{ $t('core_pos.hold_invoice_button') }}</p>
          </button>
          <button class="h-3/4 w-20 rounded-lg ml-2 flex justify-center place-items-center "
            style="background-color: rgb(255, 103, 155);" @click="printHoldInvoice">
            <!-- <p class="text-center text-xl font-bold">{{ $t('core_pos.hold_invoice_button') }}</p> -->
            <printer-icon class="h-16 text-white  " />
          </button>
        </div>
      </div>
      <div class=" col-span-6 p-7">
        <div class=" mb-4 flex w-full">

          <div class="w-3/4">
            <sw-input-group :label="$t('core_pos.search_item')" class="">
              <sw-input @keyup="loadItems" v-model="filters.item_name" type="text" />
            </sw-input-group>
          </div>
          <div class="flex place-items-center">
            <sw-button class="ml-2" style="background-color: rgb(101, 113, 255);" @click="openTablesModal">
             <img class="h-10" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIiA/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHZlcnNpb249IjEuMSIgd2lkdGg9IjEwODAiIGhlaWdodD0iMTA4MCIgdmlld0JveD0iMCAwIDEwODAgMTA4MCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxkZXNjPkNyZWF0ZWQgd2l0aCBGYWJyaWMuanMgNS4yLjQ8L2Rlc2M+CjxkZWZzPgo8L2RlZnM+CjxnIHRyYW5zZm9ybT0ibWF0cml4KDEgMCAwIDEgNTQwIDU0MCkiIGlkPSI2YjQzNDZlOC05YTMwLTQ5ZmQtYmU0YS00MDVmNjA3ODQyMjQiICA+CjxyZWN0IHN0eWxlPSJzdHJva2U6IG5vbmU7IHN0cm9rZS13aWR0aDogMTsgc3Ryb2tlLWRhc2hhcnJheTogbm9uZTsgc3Ryb2tlLWxpbmVjYXA6IGJ1dHQ7IHN0cm9rZS1kYXNob2Zmc2V0OiAwOyBzdHJva2UtbGluZWpvaW46IG1pdGVyOyBzdHJva2UtbWl0ZXJsaW1pdDogNDsgZmlsbDogcmdiKDI1NSwyNTUsMjU1KTsgZmlsbC1ydWxlOiBub256ZXJvOyBvcGFjaXR5OiAxOyB2aXNpYmlsaXR5OiBoaWRkZW47IiB2ZWN0b3ItZWZmZWN0PSJub24tc2NhbGluZy1zdHJva2UiICB4PSItNTQwIiB5PSItNTQwIiByeD0iMCIgcnk9IjAiIHdpZHRoPSIxMDgwIiBoZWlnaHQ9IjEwODAiIC8+CjwvZz4KPGcgdHJhbnNmb3JtPSJtYXRyaXgoMSAwIDAgMSA1NDAgNTQwKSIgaWQ9ImM3Mzk0YjZmLTQ2OTktNDE0Yy1hZGJkLWEzMjdhMzg1NDViNyIgID4KPC9nPgo8ZyB0cmFuc2Zvcm09Im1hdHJpeCgxIDAgMCAxIDU0MCA1NDApIiBpZD0iN2VkY2FlODItNjliZC00OTQzLWFlMTEtNjhkODVhMzhiMDdmIiAgPgo8cGF0aCBzdHlsZT0ic3Ryb2tlOiByZ2IoMCwwLDApOyBzdHJva2Utd2lkdGg6IDA7IHN0cm9rZS1kYXNoYXJyYXk6IG5vbmU7IHN0cm9rZS1saW5lY2FwOiBidXR0OyBzdHJva2UtZGFzaG9mZnNldDogMDsgc3Ryb2tlLWxpbmVqb2luOiBtaXRlcjsgc3Ryb2tlLW1pdGVybGltaXQ6IDQ7IGZpbGw6IHJnYigyNTUsMjU1LDI1NSk7IGZpbGwtcnVsZTogbm9uemVybzsgb3BhY2l0eTogMTsiIHZlY3Rvci1lZmZlY3Q9Im5vbi1zY2FsaW5nLXN0cm9rZSIgIHRyYW5zZm9ybT0iIHRyYW5zbGF0ZSgtNDgwLCA0ODApIiBkPSJNIDE3MyAtNjAwIEwgNzg3IC02MDAgTCA3NTMgLTcyMCBMIDIwOCAtNzIwIEwgMTczIC02MDAgWiBNIDQ4MCAtNjYwIFogTSA2NzIgLTUyMCBMIDI4OSAtNTIwIEwgMjc4IC00NDAgTCA2ODIgLTQ0MCBMIDY3MiAtNTIwIFogTSAxNjAgLTE2MCBMIDIwOSAtNTIwIEwgMTIwIC01MjAgUSAxMDAgLTUyMCA4OC41IC01MzYgUSA3NyAtNTUyIDgyIC01NzEgTCAxMzkgLTc3MSBRIDE0MyAtNzg0IDE1MyAtNzkyIFEgMTYzIC04MDAgMTc3IC04MDAgTCA3ODMgLTgwMCBRIDc5NyAtODAwIDgwNyAtNzkyIFEgODE3IC03ODQgODIxIC03NzEgTCA4NzggLTU3MSBRIDg4MyAtNTUyIDg3MS41IC01MzYgUSA4NjAgLTUyMCA4NDAgLTUyMCBMIDc1MiAtNTIwIEwgODAwIC0xNjAgTCA3MjAgLTE2MCBMIDY5MyAtMzYwIEwgMjY3IC0zNjAgTCAyNDAgLTE2MCBMIDE2MCAtMTYwIFoiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgLz4KPC9nPgo8L3N2Zz4=" alt="SVG Image">

            </sw-button>
          </div>
          <div class="flex place-items-center">
            <sw-button class="ml-2" style="background-color: rgb(101, 113, 255);" @click="openNotesModal">
              <document-text-icon class="h-10 text-white " />
            </sw-button>
          </div>
          <div class="flex place-items-center">
            <sw-button class="ml-2" style="background-color: rgb(101, 113, 255);" @click="openContactModal">
              <user-icon class="h-10 text-white " />
            </sw-button>
          </div>
          <div class="flex place-items-center">
            <sw-button class="ml-2" style="background-color: rgb(255, 103, 155);" @click="openHoldInvoicesModal">

              <menu-icon class="h-10 text-white " />
            </sw-button>
          </div>
          <div class="flex place-items-center">
            <sw-button class="ml-2" style="background-color: rgb(10, 192, 116);" @click="redirectDashboard">

              <view-grid-icon class="h-10 text-white " />
            </sw-button>
          </div>
        </div>
        <div class="flex flex-wrap">
          <sw-button class="ml-2" variant="primary-outline" @click="filterCategory('All')">
            {{ $t('core_pos.all') }}
          </sw-button>
          <div v-for="(category, index) in  categories_items_pos" :key="index">
            <sw-button class="ml-2" variant="primary-outline" @click="filterCategory(category.item_category_id)">
              {{ category.name }}
            </sw-button>
          </div>
        </div>
        <div class="flex flex-wrap ">

          <!-- item -->
          <div v-for="item in item_list.slice(0, 25)" :key="item.id"
            class="flex h-36 w-44 items-center rounded-3xl bg-white border-gray-300 border p-3 m-2">
            <div class="flex flex-col" @click="addItemShopping(item)">
              <div class="h-6 text-sm " style="z-index=1;">
                <span class="bg-blue-600 p-1.5 text-white rounded-lg"
                  v-html="$utils.formatMoney(item.price, defaultCurrency)" />

              </div>
              <div class="flex justify-center">
                <div class="h-2/4 w-2/4 bg-white">
                  <img v-if="item.picture != 0" :src="item.picture" alt="">
                  <img v-else class="m-0 scale-50 p-0"
                    src="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEBLAEsAAD/2wBDAAMCAgMCAgMDAwMEAwMEBQgFBQQEBQoHBwYIDAoMDAsKCwsNDhIQDQ4RDgsLEBYQERMUFRUVDA8XGBYUGBIUFRT/2wBDAQMEBAUEBQkFBQkUDQsNFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBT/wAARCAZABkADASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD9UuaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOajkk8uOopC8au+/O3+CgCzzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzUcknlx0ASc0c1WkLxq7787f4Ks80AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHNABzRzRzRzQAc0c0c0c0AHNHNHNHN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jmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjm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HNAHifwo/Y7+DnwRuIr3wd4A0vT9TgOY9SuEe7u0Pqk85d0/4Aa9s5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOa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o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmjmjmgA5o5o5o5oAOaOaOaOaADmjmm+Yv95fzo8xf7y/nTJ5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/AHl/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/wB5fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv8AeX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/AHl/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/wB5fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv8AeX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/AHl/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/wB5fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv8AeX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/AHl/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/wB5fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv8AeX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/AHl/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/wB5fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv8AeX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/AHl/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/wB5fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv8AeX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/AHl/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/wB5fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv8AeX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/AHl/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdx3NHNN8xf7y/nR5i/wB5fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv95fzoDmXcdzRzTfMX+8v50eYv8AeX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/eX86A5l3Hc0c03zF/vL+dHmL/AHl/OgOZdx3NHNN8xf7y/nR5i/3l/OgOZdz/2Q==" />
                </div>
              </div>

              <div class="h-6 p-0 text-xs text-ellipsis overflow-hidden break-normal">
                <p class="text-ellipsis overflow-hidden">
                  {{ item.name }}
                </p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </base-page>
</template>

<script>
import moment from 'moment'
import Swal from 'sweetalert2'
import {
  PrinterIcon,
  ShoppingCartIcon,
  PlusIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  MenuIcon,
  UserIcon,
  ViewGridIcon,
  DocumentTextIcon,
} from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'
const {
  required,
  minLength,
  email,
  numeric,
  minValue,
  maxLength,
  requiredIf,
} = require("vuelidate/lib/validators");
export default {
  components: {
    PrinterIcon,
    ShoppingCartIcon,
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    EyeIcon,
    PencilIcon,
    TrashIcon,
    MenuIcon,
    UserIcon,
    ViewGridIcon,
    DocumentTextIcon,
  },
  data: () => ({
    store_selected: null,
    store_options: [],
    showSideBar: false,
    item_list: [],
    shopping_cart: [],
    sub_total_shopping_cart: 0,
    total_shopping_cart: 0,
    quantity_item_shopping_cart: 0,
    cash_register_selected: {},
    customer: {},
    customer_options: [],
    search_items: '',
    invoice_prefix: '',
    invoice_next_number: '',
    filters: {
      item_name: '',
      categories_items_pos: []
    },
    discount: 0,
    discount_type: 'fixed',
    discount_val: 0,
    tip: 0,
    tip_type: 'fixed',
    tip_val: 0,
    tax_options: [],
    tax_selected: [],
    tax_total: 0,
    taxes_list: null,
    existsTaxes: false,
    selectedCurrency: '',
    cash_register_options: [],
    categories_items_pos: [],
    disabledDecrement: false,
    note_invoice: "",
    contact: {},
    reference: '',
    tables_selected: [],
    hold_tables_selected: [],
    is_hold_invoice: false,
    hold_invoice_id: null
  }),
  validations: {
    customer: {
      required
    }
  },

  computed: {
    ...mapGetters('user', ['currentUser']),

    ...mapGetters('company', ['defaultCurrency', 'defaultCurrencyForInput']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    currency() {
      return this.selectedCurrency
    },

    currencyFiveDecimal() {
      return {
        ...this.selectedCurrency,
        precision: 5,
      }
    },

    customerError() {
      if (!this.$v.customer.$error) {
        return "";
      }
      if (!this.$v.customer.required) {
        return this.$t("validation.required");
      }
    },
  },

  mounted() {
    window.hub.$on('clear_data_emit', this.getClearDataEmit);
    window.hub.$on('cash_register_emit', this.getCashRegisterEmit);
    window.hub.$on('confirm_open_cash_register_emit', this.getConfirmOpenCashRegisterEmit);
    window.hub.$on('note_invoice_emit', this.getNoteInvoiceEmit);
    window.hub.$on('contact_invoice_emit', this.getContactInvoiceEmit);
    window.hub.$on('get_hold_invoice', this.getHoldInvoiceEmit);
    window.hub.$on('tables_selected_emit', this.getTablesSelectedEmit);
  },

  created() {
    this.loadData()
  },

  methods: {
    ...mapActions('item', ['fetchItems']),
    ...mapActions('modal', ['openModal', 'clodeModal']),
    ...mapActions('customer', ['fetchCustomers']),
    ...mapActions('invoice', ['getInvoiceNumberSendPrefix', 'addInvoice']),
    ...mapActions('taxType', ['fetchTaxTypes']),
    ...mapActions('corePos', ['fetchCashRegisterUser', 'fetchPosItemCategory', 'holdInvoice', 'deleteHoldInvoice', 'fetchStores']),

    cashRegisterModal(row, resCashHistory) {

      row.cash_history = resCashHistory.data.cash_history
      row.is_confirmed = true
      this.openModal({
        title: this.$t('core_pos.confirm_open_cash_register'),
        componentName: 'openCloseCashRegisterModal',
        data: row
      })
    },

    getCashRegisterEmit(data) {
      this.cash_register_selected = data
      this.customer = this.customer_options.find(item => item.id === this.cash_register_selected.customer_id)
    },

    getConfirmOpenCashRegisterEmit(data) {
      this.openModalConfirmCashRegister()
    },

    getClearDataEmit(data) {
      if (data.clear_data) {
        this.clearFields()
      }
    },

    getTablesSelectedEmit(data) {
      this.tables_selected = data

    },

    changeCustomer() {

      if (this.$route.query.customer !== undefined) {
        const currentPath = this.$route.fullPath;
        const pathWithoutQuery = currentPath.split('?')[0];
        this.$router.replace(pathWithoutQuery);
      }
    },

    getNoteInvoiceEmit(data) {
      this.note_invoice = data
    },

    getContactInvoiceEmit(data) {
      this.contact = data
    },

    calculateTotals(){
      this.totalShopping()
      this.subTotalShopping()
      this.calculateQuantityItems()
      this.calculateDiscount()
      this.calculateTip()
    },

    getHoldInvoiceEmit(data) {
      this.clearFields()
      data.hold_items.forEach(item => {
        const qty = parseFloat(item.quantity)
        const quantityTemp = parseInt(qty.toFixed(0))
        let temp = {
          name: item.name,
          description: item.description,
          discount: item.discount,
          discount_val: item.discount_val,
          discount_type: item.discount_type,
    
          quantity: quantityTemp,
          price: item.price,
          sub_total: quantityTemp * item.price,
          total: quantityTemp * item.price,
          tax: item.tax,
          unit_name: item.unit_name,
          retention_amount: 0,
          retention_concept: item.retention_concept,
          retention_percentage: item.retention_percentage,
          retentions_id: item.retentions_id,
          item_id: item.item_id
        }

        this.shopping_cart.push(temp)
        temp = {}

      })
      // this.tax_selected = data.hold_taxes
      let taxes_totals = []

      data.hold_taxes.forEach(function (tax) {
        taxes_totals.push({
          amount: tax.amount,
          compound_tax: 0,
          id: tax.tax_type_id,
          name: tax.name,
          percent: tax.percent,
          tax_type_id: tax.tax_type_id
        })
      });

      this.tax_selected = taxes_totals

      this.note_invoice = data.notes
      this.contact = data.hold_contact[0]
      this.discount = data.discount
      this.discount_val = data.discount_val
      this.is_hold_invoice = data.is_hold_invoice
      this.hold_invoice_id = data.hold_invoice_id
      this.reference = data.description
      this.tip = data.tip
      this.tip_val = data.tip_val
      this.tip_type = data.tip_type
      this.hold_tables_selected = data.hold_tables
      this.store_selected = this.store_options.find(item => item.id == data.store_id)
      this.calculateTotals()

      if (data.print) {
        const resultData = this.formatData()
        resultData.print_pdf = true
        this.openHoldInvoicesPdfModal(resultData)
        this.clearFields()
      }

    },

    async deleteInvoiceHold() {

      const data = {
        id: this.hold_invoice_id
      }
      const response = await this.deleteHoldInvoice(data)
      return response
    },

    clearFields() {
      this.shopping_cart = [],
        this.sub_total_shopping_cart = 0,
        this.total_shopping_cart = 0,
        this.quantity_item_shopping_cart = 0,
        this.discount = 0,
        this.discount_val = 0,
        this.tip = 0,
        this.tip_val = 0,
        this.tax_selected = [],
        this.tax_total = 0,
        this.taxes_list = null,
        this.existsTaxes = false,
        this.cash_register_options = [],
        this.disabledDecrement = false,
        this.note_invoice = "",
        this.contact = {}
      this.is_hold_invoice = false
      this.hold_invoice_id = null
      this.reference = ''
      this.tables_selected = []
      this.hold_tables_selected = []
    },

    async loadData() {

      const responseCashRegistersUser = await this.fetchCashRegisterUser()
      if (responseCashRegistersUser.data.data.length === 0) {
        swal({
          icon: 'error',
          title: 'Error',
          text: this.$t('core_pos.user_without_cash_register'),
          timer: 5000
        })
        this.$router.push(`/admin/dashboard`)
        this.closeModal()
      }
      this.cash_register_options = responseCashRegistersUser.data.data

      const responseCategoriesItemsPos = await this.fetchPosItemCategory()
      this.categories_items_pos = responseCategoriesItemsPos.data.data
      this.filters.categories_items_pos = this.categories_items_pos.map(item => item.item_category_id)
      const responseTaxes = await this.fetchTaxTypes({ limit: 100 });
      this.tax_options = responseTaxes.data.taxTypes.data

      let data = { limit: 'all' }
      const responseCustomer = await this.fetchCustomers(data)
      this.customer_options = responseCustomer.data.customers.data

      let cash_register_selected = JSON.parse(sessionStorage.getItem("cash_register"));

      
      let dataStore = {
          limit: 'all'
        }

      const responseStore = await this.fetchStores(dataStore)
      this.store_options = responseStore.data.stores.data

      if (cash_register_selected == null) {

        this.openCashRegisterModal()
      } else {
        this.cash_register_selected = cash_register_selected
        if (this.$route.query.customer !== undefined) {
          this.customer = this.customer_options.find(item => item.id == this.$route.query.customer)
        } else {
          this.customer = this.customer_options.find(item => item.id === this.cash_register_selected.customer_id)
        }

        this.openModalConfirmCashRegister()
      }
      this.store_selected = this.store_options.find(item => item.id == this.cash_register_selected.store_id)

      this.loadItems()
    },

    async openModalConfirmCashRegister() {
      const resCashHistory = await window.axios.get(`/api/v1/core-pos/cash-history/${this.cash_register_selected.cash_history_id}`)
      let isConfirmed = resCashHistory.data.cash_history[0].confirmed

      if (!isConfirmed) {
        this.cashRegisterModal(this.cash_register_selected, resCashHistory)
      }
    },

    async loadItems() {
      const responseItems = await this.fetchItems({
        search: this.filters.item_name !== null ? this.filters.item_name : '',
        orderByField: 'created_at',
        orderBy: 'desc',
        avalara_bool: false,
        is_pos: 1,
        categories_id: this.filters.categories_items_pos,
        store_id: this.store_selected.id,
        limit: 20
      })
      const itemTemp = responseItems.data.items.data

      this.item_list = itemTemp.filter((obj, index, self) => {
        return index === self.findIndex((o) => (
          o.id === obj.id && o.name === obj.name
        ));
      });


    },

    openCashRegisterModal() {
      this.openModal({
        title: this.$t('core_pos.cash_register'),
        componentName: 'selectCashRegisterModal',
      })
    },

    openNotesModal() {

      this.openModal({
        title: this.$t('core_pos.notes'),
        componentName: 'noteInvoiceModal',
        data: this.note_invoice
      })
    },

    openTablesModal() {
      const data = {
        cash_register: this.cash_register_selected,
        tables: this.hold_tables_selected

      }
      this.openModal({
        title: this.$t('core_pos.tables_title'),
        componentName: 'tablesModal',
        data: data
      })
    },

    openContactModal() {
      const data = {
        contact: this.contact,
        contact_invoice: this.customer
      }
      this.openModal({
        title: this.$t('core_pos.contact_title'),
        componentName: 'contactModal',
        data: data
      })
    },

    openHoldReferenceModal() {

      if (this.shopping_cart.length == 0) {
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        })

        Toast.fire({
          icon: 'error',
          title: this.$t('core_pos.error_items_empty')
        })

        return
      }
      this.openModal({
        // title: this.$t('core_pos.contactdtitle'),
        componentName: 'holdReferenceModal',
        data: this.formatData()
      })
    },

    openHoldInvoicesModal() {
      this.openModal({
        title: this.$t('core_pos.hold_invoice_title'),
        componentName: 'holdInvoicesModal',
      })
    },

    openHoldInvoicesPdfModal(data) {

      this.openModal({
        title: this.$t('core_pos.hold_invoices_title'),
        componentName: 'holdInvoicePdfModal',
        data: data
      })
    },

    redirectDashboard() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('core_pos.message_back'),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          this.$router.push(`/admin/dashboard`)
        }
      })
    },

    addItemShopping(item) {
      item.item_id = item.id
      const itemExists = this.shopping_cart.find(obj => obj.item_id == item.item_id)
      if (itemExists !== undefined) {
        let index = this.shopping_cart.findIndex(obj => obj.item_id === item.item_id)
        this.shopping_cart[index].quantity = itemExists.quantity + 1
        item.total = item.price * item.quantity
      } else {
        item['quantity'] = 1
        item['item_id'] = item.item_id
        item['total'] = item.price
        item['discount_type'] = "fixed"
        item['discount'] = 0
        item['discount_val'] = 0
        this.shopping_cart.push(item)
      }
      this.calculateSubtotalItem(item)
      this.calculateTotals()
    },

    removeItemShoppingCart(item) {
      const id = item.item_id

      let index = this.shopping_cart.findIndex(obj => obj.item_id === id)
      if (index !== -1) {
        this.shopping_cart.splice(index, 1);
      }

      this.calculateSubtotalItem(item)
      this.calculateTotals()
    },

    calculateSubtotalItem(item) {
      let index = this.shopping_cart.findIndex(obj => obj.id === item.id)

      if (index != -1) {
        this.shopping_cart[index].sub_total = this.shopping_cart[index].price * this.shopping_cart[index].quantity
        this.shopping_cart[index].total = this.shopping_cart[index].sub_total
      }
    },

    calculateTaxesSelected() {
      this.totalShopping()
      this.subTotalShopping()
      this.calculateQuantityItems()
    },

    submitShoppingCart() {

      this.$v.$touch();
      if (this.$v.$invalid) {
        return true;
      }

      if (Object.entries(this.shopping_cart).length === 0) {
        window.toastr["error"](this.$t('core_pos.error_items_empty'));
        return true
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.message_pay_invoice'),
        icon: '/assets/icon/file-alt-solid.svg',

        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          const responseInvoiceNumPrefix = await this.getInvoiceNumberSendPrefix({ key: 'invoice' })
          this.invoice_next_number = responseInvoiceNumPrefix.data.nextNumber
          this.invoice_prefix = responseInvoiceNumPrefix.data.prefix
          const data = this.formatData()

          this.addInvoice(data)
            .then((res) => {
              window.toastr['success'](this.$t('general.created_successfully'))
              if (this.is_hold_invoice) {
                this.deleteInvoiceHold()
              }
              this.$router.push(
                `/admin/payments/multiple/customer/${this.customer.id}/invoice/${res.data.invoice.id}/create`
              )
            })
            .catch((err) => {
              window.toastr["success"](this.$t('general.action_failed'));
            })
        }
      })
    },



    // submitHoldInvoice(){
    //   this.$v.$touch();
    //   if (this.$v.$invalid) {
    //     return true;
    //   }

    //   if (Object.entries(this.shopping_cart).length === 0) {
    //     window.toastr["error"](this.$t('core_pos.error_items_empty'));
    //     return true
    //   }
    //   const descriptionInput = ''

    //   Swal.fire({
    //     title: this.$t('general.are_you_sure'),
    //     text: this.$t('core_pos.hold_invoice_message'),
    //     icon: '/assets/icon/file-alt-solid.svg',
    //     buttons: true,
    //     input: 'text',
    //     inputLabel: this.$t('core_pos.description_input'),
    //     inputPlaceholder: this.$t('core_pos.description_input'),
    //     inputAttributes: {
    //       autocapitalize: 'off'
    //     },
    //     showCancelButton: true,
    //     showDenyButton: true, 
    //     denyButtonText: this.$t('core_pos.print'), 
    //     cancelButtonText: this.$t('core_pos.cancel_alert'),
    //     confirmButtonText: this.$t('core_pos.accept_alert'),
    //     showLoaderOnConfirm: true,
    //     inputValidator: (value) => {

    //       return new Promise((resolve) => {
    //         if (!value == '') {
    //           resolve()
    //         } else {
    //           resolve(this.$t('core_pos.message_error_input'))
    //         }
    //       })
    //     },
    //     preConfirm: (description) => {
    //      this.reference = description
    //     },
    //     allowOutsideClick: () => !Swal.isLoading()
    //   }).then((result) => {

    //     if (result.isConfirmed) {
    //       const data = this.formatData()
    //       this.holdInvoice(data)
    //         .then((res) => {
    //           window.toastr['success'](this.$t('general.created_successfully'))
    //           this.clearFields()
    //         })
    //         .catch((err) => {
    //           window.toastr["success"](this.$t('general.action_failed'));
    //         })
    //     }  else if (result.isDenied) {
    //       const resultData = this.formatData()
    //       resultData.print_pdf = true
    //       this.openHoldInvoicesPdfModal(resultData)
    //       }
    //   })
    // },

    printHoldInvoice() {
      try {

        const resultData = this.formatData()
        resultData.print_pdf = true
        resultData.save_print = false
        this.openHoldInvoicesPdfModal(resultData)
      } catch (error) {
      }
    },

    formatData() {
      const data = {
        print_pdf: false,
        is_invoice_pos: 1,
        is_pdf_pos: true,
        avalara_bool: false,
        send_email: false,
        save_as_draft: false,
        not_charge_automatically: false,
        package_bool: false,
        invoice_date: moment().format('YYYY-MM-DD'),
        due_date: moment().add(7, 'd').format('YYYY-MM-DD'),
        invoice_number: this.invoice_prefix + '-' + this.invoice_next_number,
        user_id: this.customer.id,
        total: this.total_shopping_cart,
        due_amount: this.total_shopping_cart,
        sub_total: this.sub_total_shopping_cart,
        tax: this.tax_total,
        discount_type: this.discount_type,
        discount: this.discount ? this.discount : 0,
        discount_val: this.discount_val ? this.discount_val : 0,
        tip_type: this.tip_type,
        tip: this.tip ? this.tip : 0,
        tip_val: this.tip_val ? this.tip_val : 0,
        discount_per_item: 'NO',
        items: this.shopping_cart,
        invoice_template_id: 1,
        banType: true,
        invoice_pbx_modify: 0,
        packages: [],
        cash_register_id: this.cash_register_selected.id,
        taxes: this.taxes_list,
        notes: this.note_invoice,
        contact: this.contact,
        description: this.reference,
        tables_selected: this.hold_tables_selected,
        hold_invoice_id: this.hold_invoice_id,
        is_hold_invoice: this.is_hold_invoice,
        store_id: this.store_selected.id
      }
      return data
    },

    totalShopping() {
      let total = 0
      if (this.shopping_cart.length != 0) {
        total = this.shopping_cart.reduce((acc, obj) => {
          return acc + obj.sub_total
        }, 0)

      } else {
        total = 0
        this.discount_val = 0
        this.tip_val = 0
        this.tax = {}
        this.taxes_list = {}
        this.tax_total = 0
        this.existsTaxes = false
      }

      this.calculateTaxes(total - this.discount_val)
      this.total_shopping_cart = total - this.discount_val + this.tip_val + this.tax_total
    },

    subTotalShopping() {
      try {
        if (this.shopping_cart.length != 0) {
          this.sub_total_shopping_cart = this.shopping_cart.reduce((acc, obj) => {
            return acc + obj.sub_total
          }, 0)
        } else {
          this.sub_total_shopping_cart = 0
        }
      } catch (error) {
      }
    },

    calculateTaxes(total) {
      let tax_total = 0
      let taxes_totals = []
      if (this.tax_selected.length === 0) {
        this.taxes_list = {}
        this.existsTaxes = false
        return tax_total
      }
      this.tax_selected.forEach(function (tax) {
        taxes_totals.push({
          name: tax.name,
          amount: (tax.percent * total) / 100,
          compound_tax: 0,
          percent: tax.percent,
          tax_type_id: tax.id
        })
      });

      tax_total = taxes_totals.reduce((acc, obj) => {
        return acc + obj.amount
      }, 0)
      this.taxes_list = taxes_totals
      this.tax_total = tax_total
      this.existsTaxes = true
    },

    calculateQuantityItems() {
      this.quantity_item_shopping_cart = this.shopping_cart.reduce((acc, obj) => {
        return acc + obj.quantity
      }, 0)
    },

    calculateTip() {
      if (this.tip_type === 'fixed') {
        this.tip_val = this.tip * 100
      } else {
        this.tip_val = (this.sub_total_shopping_cart * this.tip) / 100
      }
      this.totalShopping()
    },
    
    calculateDiscount() {
      if (this.discount_type === 'fixed') {
        this.discount_val = this.discount * 100
      } else {
        this.discount_val = (this.sub_total_shopping_cart * this.discount) / 100
      }
      this.totalShopping()
    },


    selectFixedTip() {
      this.tip_type = "fixed"
      this.calculateTip()
    },

    selectPercentageTip() {
      this.tip_type = 'percentage'
      this.calculateTip()
    },

    selectFixed() {
      this.discount_type = "fixed"
      this.calculateDiscount()
    },

    selectPercentage() {
      this.discount_type = 'percentage'
      this.calculateDiscount()
    },

    filterCategory(id) {

      if (id == 'All') {
        this.filters.categories_items_pos = this.categories_items_pos.map(item => item.item_category_id)
      } else {
        this.filters.categories_items_pos = [id]
      }
      this.loadItems()
    },

    changeCashRegister() {
      sessionStorage.removeItem('cash_register');
      sessionStorage.setItem("cash_register", JSON.stringify(this.cash_register_selected));
      this.customer = this.customer_options.find(item => item.id === this.cash_register_selected.customer_id)
      this.openModalConfirmCashRegister()
    },

    decrementQuantity(item) {
      let index = this.shopping_cart.findIndex(obj => obj.item_id === item.item_id)
      let quantity = this.shopping_cart[index].quantity
      this.shopping_cart[index].quantity = quantity - 1
      
      this.calculateSubtotalItem(item)
      this.calculateTotals()
      this.$forceUpdate()
    },
    
    incrementQuantity(item) {
      let index = this.shopping_cart.findIndex(obj => obj.item_id === item.item_id)
      let quantity = this.shopping_cart[index].quantity
      this.shopping_cart[index].quantity = quantity + 1

      this.calculateSubtotalItem(item)
      this.calculateTotals()
      this.$forceUpdate()
    },

    changeStore(){
      this.loadItems()
    }
  },

}
</script>

