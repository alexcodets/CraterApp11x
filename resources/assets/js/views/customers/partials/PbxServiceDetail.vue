<template>
  <div class="pbx-service-details">
    <sw-divider class="my-6" />

    <p class="text-gray-500 uppercase sw-section-title">
      {{ $t('customers.pbxservices_resume') }}
    </p>

    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />

    <div class="grid grid-cols-12">
      <div class="col-span-12">
        <div class="mt-8">
          <div class="
              grid grid-cols-1
              gap-4
              mt-5
              lg:grid-cols-3
              md:grid-cols-2
              sm:grid-cols-1
            ">
            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('customers.service_number') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                    selectedPbxService
                      ? selectedPbxService.pbx_services_number
                      : ''
                }}
              </p>
            </div>
            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('customers.package_name') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                    selectedPbxService && selectedPbxService.pbx_package
                      ? selectedPbxService.pbx_package.pbx_package_name
                      : ''
                }}
              </p>
            </div>
            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('packages.status') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
             

                <div v-if="serviceStatus " > 


                  <div v-if="serviceStatus.value=== 'A'" >

<sw-badge
          :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
          :color="$utils.getBadgeStatusColor('COMPLETED').color"
          class="px-3 py-1"
        >
        {{ $t('general.active') }}
        </sw-badge> 
</div>


<div v-if="serviceStatus.value === 'P'" >
<sw-badge
          :bg-color="$utils.getBadgeStatusColor('VIEWED').bgColor"
          :color="$utils.getBadgeStatusColor('VIEWED').color"
          class="px-3 py-1"
        >
        {{ $t('general.pending') }}
        </sw-badge>
</div>

<div v-if="serviceStatus.value == 'S'">
          <sw-badge
          :bg-color="$utils.getBadgeStatusColor('SENT').bgColor"
          :color="$utils.getBadgeStatusColor('SENT').color"
          class="px-3 py-1"
        >
       {{ $t('general.suspended') }}
        </sw-badge>
      </div>
         
      <div v-if="serviceStatus.value == 'C' ">
          <sw-badge
          :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
          :color="$utils.getBadgeStatusColor('OVERDUE').color"
          class="px-3 py-1"
        >
        {{ $t('general.cancelled') }}
        </sw-badge>
      </div>
                
                </div>
              </p>
            </div>
          </div>

          <div class="
              grid grid-cols-1
              gap-4
              mt-5
              lg:grid-cols-3
              md:grid-cols-2
              sm:grid-cols-1
            ">
            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('customers.term') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ selectedPbxService ? selectedPbxService.term : '' }}
              </p>
            </div>
            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('customers.discount') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
               

                <div v-if="allowDiscount=== 'YES'" >

<sw-badge
          :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
          :color="$utils.getBadgeStatusColor('COMPLETED').color"
          class="px-3 py-1"
        >
        {{ $t('general.yes') }}
        </sw-badge> 
</div>

<div v-if="allowDiscount=== 'NO'">
          <sw-badge
          :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
          :color="$utils.getBadgeStatusColor('OVERDUE').color"
          class="px-3 py-1"
        >
        {{ $t('general.not') }}
        </sw-badge>
      </div>

              </p>
            </div>
            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('customers.date_begin') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ selectedPbxService ? selectedPbxService.date_begin : '' }}
              </p>
            </div>
          </div>

          <div class="
              grid grid-cols-1
              gap-4
              mt-5
              lg:grid-cols-3
              md:grid-cols-2
              sm:grid-cols-1
            ">
            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('pbx_services.type_service') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
              

<div v-if="selectedPbxService" >
  <div v-if="selectedPbxService.pbx_package" > 
  
    <div v-if="selectedPbxService.pbx_package.status_payment  == 'postpaid'" >
      {{ $t('packages.item.postpaid') }}
    </div>
    <div v-if="selectedPbxService.pbx_package.status_payment  == 'prepaid'" > 
      {{ $t('packages.item.prepaid') }}
    </div>
  </div>
</div>
              </p>
            </div>
            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('pbx_services.server') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                    selectedPbxService &&
                      selectedPbxService.pbx_package &&
                      selectedPbxService.pbx_package.server
                      ? selectedPbxService.pbx_package.server.server_label
                      : ''
                }}
              </p>
            </div>
            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('pbx_services.tenant') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                  selectedPbxService && selectedPbxService.tenant
                    ? selectedPbxService.tenant.name
                    : ''
                }}
              </p>
            </div>
          </div>

          <div class="
              grid grid-cols-1
              gap-4
              mt-5
              lg:grid-cols-3
              md:grid-cols-2
              sm:grid-cols-1
            ">

            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('customers.allow_customapprate') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
                    selectedPbxService
                      ? selectedPbxService.custom_app_rate_name
                      : ''
                }}
              </p>
            </div>
           
            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('didFree.item.custom_destination_groups') }}
              </p>
              <!-- -->
                <div v-if="customDestinationsGroupsInbound.length > 0">
                  <p 
                    v-for="customGroupInbound in customDestinationsGroupsInbound"
                    class="text-sm font-bold leading-5 text-black non-italic"
                  >
                      {{ customGroupInbound }}
                  </p>
                </div>
                <div v-else>
                  <p 
                    class="text-sm font-bold leading-5 text-black non-italic"
                  >
                   
                    {{ $t('providers.not_selected') }}
                  </p>
                </div>
              <!-- -->
            </div>

            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('didFree.item.custom_destination_groups_outbound') }}
              </p>
              <!-- -->
                <div v-if="customDestinationsGroupsOutbound.length > 0">
                  <p 
                    v-for="customGroupOutbound in customDestinationsGroupsOutbound"
                    class="text-sm font-bold leading-5 text-black non-italic"
                  >
                      {{ customGroupOutbound }}
                  </p>
                </div>
                <div v-else>
                  <p 
                    class="text-sm font-bold leading-5 text-black non-italic"
                  >
                  {{ $t('providers.not_selected') }}
                  </p>
                </div>
              <!-- -->
            </div>

          </div>

          <div class="
              grid grid-cols-1
              gap-4
              mt-5
              lg:grid-cols-3
              md:grid-cols-2
              sm:grid-cols-1
            ">
            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('pbx_services.package_price') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic" v-html="
  selectedPbxService
    ? $utils.formatMoney(
      selectedPbxService.pbxpackages_price,
      defaultCurrency
    )
    : ''
" />
            </div>
            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('pbx_services.package_number') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{
    selectedPbxService && selectedPbxService.pbx_package
      ? selectedPbxService.pbx_package.packages_number
      : ''
}}
              </p>
            </div>
            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('pbx_services.auto_suspension') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">



<div v-if="selectedPbxService" >
  
<div v-if="selectedPbxService.auto_suspension === 1" >

  <sw-badge
          :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
          :color="$utils.getBadgeStatusColor('COMPLETED').color"
          class="px-3 py-1"
        >
        {{ $t('general.active') }}
        </sw-badge>
</div>

  
<div v-if="selectedPbxService.auto_suspension != 1" >

  <sw-badge
          :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
          :color="$utils.getBadgeStatusColor('OVERDUE').color"
          class="px-3 py-1"
        >
        {{ $t('general.inactive') }}
        </sw-badge>
</div>

</div>
              </p>
            </div>

           

            <!-- DISCOUNT ALLOW -->
            <div v-if="selectedPbxService.allow_discount == '1' " >
              <div v-if="selectedPbxService.discount_term_type == 'D'">
                <div>
                  <p class="
                      mb-1
                      text-sm
                      font-normal
                      leading-5
                      non-italic
                      text-primary-800
                    ">
                    {{ $t('pbx_services.time_period') }}
                  </p>
                  <p class="text-sm font-bold leading-5 text-black non-italic">
                    {{ $t('general.from') }}: {{ selectedPbxService.date_from }}
                  </p>
                </div>
                <div>
                  <p class="
                      mb-1
                      text-sm
                      font-normal
                      leading-5
                      non-italic
                      text-primary-800
                    ">
                  </p>
                  <p class="text-sm font-bold leading-5 text-black non-italic">
                    {{ $t('general.to') }}: {{ selectedPbxService.date_to }}
                  </p>
                </div>
              </div>
              <div v-else>
                  <p class="
                      mb-1
                      text-sm
                      font-normal
                      leading-5
                      non-italic
                      text-primary-800
                    ">
                    {{ $t('pbx_services.time_period') }}
                  </p>
                  <p class="text-sm font-bold leading-5 text-black non-italic">
                    {{selectedPbxService.time_period + ' ' + selectedPbxService.time_period_value}}
                  </p>
              </div>
            </div>
              
            <div v-if="selectedPbxService.allow_discount == '1' " >
              <div>
                <p class="
                    mb-1
                    text-sm
                    font-normal
                    leading-5
                    non-italic
                    text-primary-800
                  ">
                  {{ $t('pbx_services.discount_type') }}
                </p>
                <p class="text-sm font-bold leading-5 text-black non-italic">
                  {{ selectedPbxService.allow_discount_type }}
                </p>
              </div>
            </div>

            <div v-if="selectedPbxService.allow_discount == '1' " >
              <div>
                <p class="
                    mb-1
                    text-sm
                    font-normal
                    leading-5
                    non-italic
                    text-primary-800
                  ">
                  {{ $t('pbx_services.discount_amount') }}
                </p>
                <p v-if="selectedPbxService.allow_discount_type == 'percentage'" class="text-sm font-bold leading-5 text-black non-italic">
                  {{ selectedPbxService.allow_discount_value}} %
                </p>
                <p v-else class="text-sm font-bold leading-5 text-black non-italic" v-html="selectedPbxService? $utils.formatMoney((selectedPbxService.allow_discount_value * 100),defaultCurrency): ''" />

                <p  class="text-sm font-bold leading-5 text-black non-italic" v-html="selectedPbxService? $utils.formatMoney((selectedPbxService.discount_val),defaultCurrency): ''" />
              </div>
            </div>
  
            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('pbx_services.cap_by_extension') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ selectedPbxService ? selectedPbxService.cap_extension : '' }}
              </p>
            </div>

            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('pbx_services.total_by_extension') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ selectedPbxService ? selectedPbxService.cap_total : '' }}
              </p>
            </div>

            <div>
              <p class="
                          mb-1
                          text-sm
                          font-normal
                          leading-5
                          non-italic
                          text-primary-800
                        ">
                {{ $t('pbx_services.inclusive_minutes_consume') }}
              </p>
              <p class="
                          text-sm
                          font-bold
                          leading-5
                          text-black
                          non-italic
                        ">


              <div v-if="this.inclusive_minutes_cosumed == null">
                0
              </div>

              <div v-if="this.inclusive_minutes_cosumed != null">
                {{ this.inclusive_minutes_cosumed.toFixed(2) }}
              </div>


              </p>
            </div>


            <div>
              <p class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                ">
                {{ $t('pbx_services.prorateotal') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ selectedPbxService ? (selectedPbxService.total_prorate / 100) : '' }}
              </p>
            </div>
          </div>

          <!--    CDR Processes      -->
          <p class="text-gray-500 uppercase sw-section-title mt-6">
            {{ $t('pbx_services.cdr_processes') }}
          </p>

          <div
           class="grid grid-cols-1 gap-4 lg:grid-cols-4 mt-5  md:grid-cols-2 sm:grid-cols-1"
           >

            <div>
              <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                {{ $t('pbx_services.calculated') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ commandosDataCdr.calculated }}
              </p>
            </div>
            <div>
              <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                {{ $t('pbx_services.calculated_today') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ commandosDataCdr.calculated_today }}
              </p>
            </div>
            <div>
              <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                {{ $t('pbx_services.uncalculated') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ commandosDataCdr.unCalculated }}
              </p>
            </div>
            
            <!-- button for manager the jobs -->
            <div class="w-1/4">
              <sw-dropdown class="ms-4" v-if="false">
                <sw-button  class=" right-5" slot="activator" variant="primary" >
                  <p>Tools Jobs</p>
                </sw-button>
                <sw-dropdown-item @click="jobInfoService">
                      <pencil-icon class="h-5 mr-3 text-gray-600"/>
                      {{ $t('pbx_services.job_info') }}
                </sw-dropdown-item>
                <sw-dropdown-item @click="softDeleteService">
                    <pencil-icon class="h-5 mr-3 text-gray-600"/>
                    {{ $t('pbx_services.soft_delete') }}
                </sw-dropdown-item>
                <sw-dropdown-item @click="hardDeleteService">
                  <pencil-icon class="h-5 mr-3 text-gray-600"/>
                  {{ $t('pbx_services.hard_delete') }}
                </sw-dropdown-item>
              </sw-dropdown>
            </div>
        
          </div>

          <!--    JOBs Processes      -->
          <p class="text-gray-500 uppercase sw-section-title mt-6">
            {{ $t('pbx_services.jobs_processes') }}
          </p>

          <div class="grid grid-cols-1 gap-4 mt-5 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1">
            <div>
              <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                {{ $t('pbx_services.calculate') }}
              </p>
              <sw-badge :bg-color="$utils.getBadgeStatusColor(backgroundCalculateStatus).bgColor"
                :color="$utils.getBadgeStatusColor(backgroundCalculateStatus).color">
                {{ commandosDataJobs.calculate
    ? $t('general.bg_pro')
    :$t('general.nbg_pro')
}}
              </sw-badge>
            </div>
            <div>
              <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                {{ $t('pbx_services.import') }}
              </p>
              <sw-badge :bg-color="$utils.getBadgeStatusColor(backgroundImportStatus).bgColor"
                :color="$utils.getBadgeStatusColor(backgroundImportStatus).color">
                  {{ commandosDataJobs.import
                   ? $t('general.bg_pro')
    :$t('general.nbg_pro')
                }}
              </sw-badge>
            </div>
            <div>
              <sw-button variant="primary" type="button" size="lg" @click="openDateModal">
                {{ $t('general.update_cdrs') }}
              </sw-button>
            </div>
          </div>

          <!--    Prepaid information      -->
          <p v-if="isPrepaid" class="text-gray-500 uppercase sw-section-title mt-6">
            {{ $t('pbx_services.prepaid_information') }}
          </p>

          <div v-if="isPrepaid" class="grid grid-cols-1 gap-4 mt-5 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1">
            <div>
              <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                {{ $t('pbx_services.total_consume') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ defaultCurrency.symbol + ' ' + total_consume }}
              </p>
            </div>

            <div>
              <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                {{ $t('pbx_services.paid_consume') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ defaultCurrency.symbol + ' ' + paid_consume }}
              </p>
            </div>

            <div>
              <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                {{ $t('pbx_services.unpaid_consume') }}
              </p>

              <sw-badge v-if="unpaid_consume > 0.00" 
                :bg-color="$utils.getBadgeStatusColor('I').bgColor"
                :color="$utils.getBadgeStatusColor('I').color"
                class="text-sm font-bold leading-5 text-black non-italic"
              >
                {{ defaultCurrency.symbol + ' ' + unpaid_consume }}               
              </sw-badge>

              <p v-if="unpaid_consume == 0.00" class="text-sm font-bold leading-5 text-black non-italic">
                {{ defaultCurrency.symbol + ' ' + unpaid_consume }}    
              </p>        

            </div>

            <div>
              <p class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800">
                {{ $t('pbx_services.deb_consume') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ defaultCurrency.symbol + ' ' + total_deb }}
              </p>
            </div>

          </div>

          <sw-divider class="my-8" />

          <!------------------- PBXWARE INFO ------------------->
          <div class="tabs mb-8 pb-3 grid col-span-12">
            <div class="border-b tab">
              <div class="relative">
                <input class="
                    w-full
                    absolute
                    z-10
                    cursor-pointer
                    opacity-0
                    h-5
                    top-4
                  " type="checkbox" />
                <header class="
                    col-span-5
                    flex
                    justify-between
                    items-center
                    py-3
                    cursor-pointer
                    select-none
                    tab-label
                  ">
                  <span class="text-gray-500 uppercase sw-section-title">
                    {{ $t('pbx_services.pbxware_info') }}
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
                    <!-- icon by feathericons.com -->
                    <svg aria-hidden="true" class="" data-reactid="266" fill="none" height="24" stroke="#606F7B"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24"
                      xmlns="http://www.w3.org/2000/svg">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </div>
                </header>

                <div class="tab-content-slide">
                  <div class="text-grey-darkest">
                    <div class="flex base-tabs"></div>
                  </div>

                  <div class="
                      grid grid-cols-1
                      gap-4
                      mt-5
                      lg:grid-cols-4
                      md:grid-cols-2
                      sm:grid-cols-1
                    ">
                    <div>
                      <p class="
                          mb-1
                          text-sm
                          font-normal
                          leading-5
                          non-italic
                          text-primary-800
                        ">
                        {{ $t('pbx_services.act_extensions') }}
                      </p>
                      <p class="
                          text-sm
                          font-bold
                          leading-5
                          text-black
                          non-italic
                        ">
 

<div v-if="package.extensions === 1" >

<sw-badge
          :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
          :color="$utils.getBadgeStatusColor('COMPLETED').color"
          class="px-3 py-1"
        >
        {{ $t('general.yes') }}
        </sw-badge> 
</div>

<div v-if="package.extensions != 1">
          <sw-badge
          :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
          :color="$utils.getBadgeStatusColor('OVERDUE').color"
          class="px-3 py-1"
        >
        {{ $t('general.not') }}
        </sw-badge>

      </div>
                      </p>
                    </div>
                    <div>
                      <p class="
                          mb-1
                          text-sm
                          font-normal
                          leading-5
                          non-italic
                          text-primary-800
                        ">
                        {{ $t('pbx_services.act_did') }}
                      </p>
                      <p class="
                          text-sm
                          font-bold
                          leading-5
                          text-black
                          non-italic
                        ">




<div v-if=" package.did === 1" >

<sw-badge
          :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
          :color="$utils.getBadgeStatusColor('COMPLETED').color"
          class="px-3 py-1"
        >
        {{ $t('general.yes') }}
        </sw-badge> 
</div>

<div v-if=" package.did  != 1">
          <sw-badge
          :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
          :color="$utils.getBadgeStatusColor('OVERDUE').color"
          class="px-3 py-1"
        >
        {{ $t('general.not') }}
        </sw-badge>
      </div>

                      </p>
                    </div>
                    <div>
                      <p class="
                          mb-1
                          text-sm
                          font-normal
                          leading-5
                          non-italic
                          text-primary-800
                        ">
                        {{ $t('pbx_services.act_call_rating') }}
                      </p>
                      <p class="
                          text-sm
                          font-bold
                          leading-5
                          text-black
                          non-italic
                        ">


<div v-if="package.call_ratings === 1" >

<sw-badge
          :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
          :color="$utils.getBadgeStatusColor('COMPLETED').color"
          class="px-3 py-1"
        >
        {{ $t('general.yes') }}
        </sw-badge> 
</div>

<div v-if="package.call_ratings  != 1">
          <sw-badge
          :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
          :color="$utils.getBadgeStatusColor('OVERDUE').color"
          class="px-3 py-1"
        >
        {{ $t('general.not') }}
        </sw-badge>
      </div>
                      </p>
                    </div>
                    <div>
                      <p class="
                          mb-1
                          text-sm
                          font-normal
                          leading-5
                          non-italic
                          text-primary-800
                        ">
                        {{ $t('pbx_services.fixed_server') }}
                      </p>
                      <p class="
                          text-sm
                          font-bold
                          leading-5
                          text-black
                          non-italic
                        ">



<div v-if=" package.modify_server=== 1" >

<sw-badge
          :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
          :color="$utils.getBadgeStatusColor('COMPLETED').color"
          class="px-3 py-1"
        >
        {{ $t('general.yes') }}
        </sw-badge> 
</div>

<div v-if=" package.modify_server != 1">
          <sw-badge
          :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
          :color="$utils.getBadgeStatusColor('OVERDUE').color"
          class="px-3 py-1"
        >
        {{ $t('general.not') }}
        </sw-badge>
      </div>
                      </p>
                    </div>
                  </div>

                  <div v-if="package.extensions || package.did" class="
                      grid grid-cols-1
                      gap-4
                      mt-5
                      lg:grid-cols-4
                      md:grid-cols-2
                      sm:grid-cols-1
                    ">
                    <div v-if="package.extensions">
                      <p class="
                          mb-1
                          text-sm
                          font-normal
                          leading-5
                          non-italic
                          text-primary-800
                        ">
                        {{ $t('pbx_services.pro_ext_name') }}
                      </p>
                      <p class="
                          text-sm
                          font-bold
                          leading-5
                          text-black
                          non-italic
                        ">
                        {{ package.profile_extensions.name }}
                      </p>
                    </div>
                    <div v-if="package.extensions">
                      <p class="
                          mb-1
                          text-sm
                          font-normal
                          leading-5
                          non-italic
                          text-primary-800
                        ">
                        {{ $t('pbx_services.pro_ext_price') }}
                      </p>
                      <p class="
                          text-sm
                          font-bold
                          leading-5
                          text-black
                          non-italic
                        ">
                        {{
    defaultCurrency.symbol +
    ' ' +
    package.profile_extensions.rate
}}
                      </p>
                    </div>
                    <div v-if="package.did">
                      <p class="
                          mb-1
                          text-sm
                          font-normal
                          leading-5
                          non-italic
                          text-primary-800
                        ">
                        {{ $t('pbx_services.pro_ext_name2') }}
                      </p>
                      <p class="
                          text-sm
                          font-bold
                          leading-5
                          text-black
                          non-italic
                        ">
                        {{ package.profile_did.name }}
                      </p>
                    </div>
                    <div v-if="package.did">
                      <p class="
                          mb-1
                          text-sm
                          font-normal
                          leading-5
                          non-italic
                          text-primary-800
                        ">
                        {{ $t('pbx_services.pro_ext_price2') }}
                      </p>
                      <p class="
                          text-sm
                          font-bold
                          leading-5
                          text-black
                          non-italic
                        ">
                        {{
    defaultCurrency.symbol +
    ' ' +
    package.profile_did.did_rate
}}
                      </p>
                    </div>
                  </div>

                  <div v-if="package.call_ratings" class="
                      grid grid-cols-1
                      gap-4
                      mt-5
                      lg:grid-cols-4
                      md:grid-cols-2
                      sm:grid-cols-1
                    ">
                    <div>
                      <p class="
                          mb-1
                          text-sm
                          font-normal
                          leading-5
                          non-italic
                          text-primary-800
                        ">
                        {{ $t('pbx_services.int_dialing_code') }}
                      </p>
                      <p class="
                          text-sm
                          font-bold
                          leading-5
                          text-black
                          non-italic
                        ">
                        {{ package.international_dialing_code }}
                      </p>
                    </div>
                    <div>
                      <p class="
                          mb-1
                          text-sm
                          font-normal
                          leading-5
                          non-italic
                          text-primary-800
                        ">
                        {{ $t('pbx_services.nac_dialing_code') }}
                      </p>
                      <p class="
                          text-sm
                          font-bold
                          leading-5
                          text-black
                          non-italic
                        ">
                        {{ package.national_dialing_code }}
                      </p>
                    </div>

                    <div v-if="package.call_ratings">
                      <div>
                        <p class="
                          mb-1
                          text-sm
                          font-normal
                          leading-5
                          non-italic
                          text-primary-800
                        ">
                          {{ $t('pbx_services.rate_per_min') }}
                        </p>
                        <p class="
                          text-sm
                          font-bold
                          leading-5
                          text-black
                          non-italic
                        ">
                          {{
    defaultCurrency.symbol +
    ' ' +
    package.rate_per_minutes
}}
                        </p>
                      </div>

                    </div>

                    <div v-if="package.call_ratings">

                      <div>
                        <p class="
                          mb-1
                          text-sm
                          font-normal
                          leading-5
                          non-italic
                          text-primary-800
                        ">
                          {{ $t('pbx_services.min_increments') }}
                        </p>
                        <p class="
                          text-sm
                          font-bold
                          leading-5
                          text-black
                          non-italic
                        ">
                          {{
    package.minutes_increments +
    ' ' +
    package.type_time_increment
}}
                        </p>
                      </div>

                    </div>

                  </div>



                  <div class="mb-5 pb-3"></div>
                </div>
              </div>
            </div>
          </div>

          <!------------------- STATS ------------------->
          <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-9 xl:gap-8">
            <!-- Total Calls -->
            <router-link slot="item-title" class="
                relative
                flex
                justify-between
                p-2
                bg-white
                rounded
                shadow
                hover:bg-gray-100
                lg:col-span-2
                xl:p-4
              " to="">
              <div>
                <span class="
                text-sm font-semibold leading-tight text-black xl:text-2xl
                  ">
                  <span v-html="selectedPbxService ? $utils.formatMoney(totalPbxService,defaultCurrency): ''" />
                </span>
                <span class="
                    block
                    mt-1
                    text-sm
                    leading-tight
                    text-gray-500
                    xl:text-lg
                  ">
                  {{ recurring_charge }}
                </span>
              </div>
              <div class="flex items-center">
                <estimate-icon class="w-9 h-9 xl:w-12 xl:h-12" />
              </div>
            </router-link>

            <!-- Call Rating -->
            <router-link slot="item-title" class="
                relative
                flex
                justify-between
                p-2
                bg-white
                rounded
                shadow
                hover:bg-gray-100
                lg:col-span-3
                xl:p-4
              " to="">
              <div>
                <span class="
                text-sm font-semibold leading-tight text-black xl:text-2xl
                  ">
                  {{ defaultCurrency.symbol + ' ' + total_consume }}
                </span>
                <span class="
                    block
                    mt-1
                    text-sm
                    leading-tight
                    text-gray-500
                    xl:text-lg
                  ">
                  {{ additional_charge }}
                </span>
              </div>
              <div class="flex items-center">
                <contact-icon class="w-9 h-9 xl:w-12 xl:h-12" />
              </div>
            </router-link>

            <!-- Renew Date -->
            <router-link slot="item-title" class="
                relative
                flex
                justify-between
                p-2
                bg-white
                rounded
                shadow
                hover:bg-gray-100
                lg:col-span-2
                xl:p-4
              " to="">
              <div>
                <span class="text-sm font-semibold leading-tight text-black xl:text-2xl">
                  <span>
                    {{
    selectedPbxService ? selectedPbxService.renewal_date : ''
}}
                  </span>
                </span>
                <span class="
                    block
                    mt-1
                    text-sm
                    leading-tight
                    text-gray-500
                    xl:text-lg
                  ">
                  {{ $t('pbx_services.renewal_date') }}
                </span>
              </div>
              <div class="flex items-center">
                <invoice-icon class="w-9 h-9 xl:w-12 xl:h-12" />
              </div>
            </router-link>

            <!-- Credit -->
            <router-link v-if="getStatusPayment == 'prepaid'" slot="item-title" class="
                relative
                flex
                justify-between
                p-2
                bg-white
                rounded
                shadow
                hover:bg-gray-100
                lg:col-span-2
                xl:p-4
              " to="">
              <div>
                <span class="
                text-sm font-semibold leading-tight text-black xl:text-2xl
                  ">
                  {{
    selectedViewCustomer && selectedViewCustomer.customer
      ? defaultCurrency.symbol +
      ' ' +
      selectedViewCustomer.customer.balance.toFixed(2)
      : ''
}}
                </span>
                <span class="
                    block
                    mt-1
                    text-sm
                    leading-tight
                    text-gray-500
                    xl:text-lg
                  ">
                  {{ $t('pbx_services.credit') }}
                </span>
              </div>
              <div class="flex items-center">
                <dollar-icon class="w-9 h-9 xl:w-12 xl:h-12" />
              </div>
            </router-link>
          </div>

          <!------------------- EXTENSIONS ------------------->
          <div class="tabs mb-5 grid col-span-12 pt-8">
            <div class="border-b tab">
              <div class="relative">
                <input class="
                    w-full
                    absolute
                    z-10
                    cursor-pointer
                    opacity-0
                    h-5
                    top-4
                  " type="checkbox" />
                <header class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label">
                  <span class="text-gray-500 uppercase sw-section-title">
                    {{ $t('corePbx.dashboard.extensions') }}
                  </span>
                  <div class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test">
                    <!-- icon by feathericons.com -->
                    <svg aria-hidden="true" class="" data-reactid="266" fill="none" height="24" stroke="#606F7B"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24"
                      xmlns="http://www.w3.org/2000/svg">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </div>
                </header>
                
                

                <div class="tab-content-slide">

                  <div class="flex flex-wrap items-center justify-end">
                    <sw-button
                      variant="primary"
                      size="lg"
                      class="w-full md:w-auto mb-2 md:mb-0"
                      @click="openAddExtModal()"
                    >
                      <plus-icon class="w-6 h-6 mr-1 -ml-2" />
                      {{ $t('corePbx.tenants.add') }}
                    </sw-button>
                  </div>

                  <div class="text-grey-darkest">
                    <div class="flex base-tabs"></div>
                  </div>

                  <sw-table-component ref="ext_table" :show-filter="false" :data="fetchExtensionsData"
                    table-class="table">
                    <sw-table-column :sortable="true" :label="$t('pbx_services.name')" show="name">

                      <template slot-scope="row">
                        {{ row.name }}


                        <div v-if="row.invoice_prorate == 0 && row.date_prorate != null">
                          <p><b>{{ $t('general.warning_service9') }}: </b> {{ row.date_prorate }}</p>
                          <p><b>{{ $t('general.warning_service10') }}: </b> {{ row.prorate / 100 }}</p>
                        </div>
                      </template>
                    </sw-table-column>

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.number')" show="ext" />

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.location')" show="location" />

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.status')" show="status" />

                    <sw-table-column :sortable="false" :label="$tc('pbx_services.pro_ext_name')" show="profile_name" />

                    <sw-table-column :sortable="false" :label="$tc('pbx_services.pro_ext_price')" show="profile_rate">
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.pro_ext_price') }}</span>
                     
                        <span>
                          {{
                            defaultCurrency.symbol +
                            ' ' +
                            row.price.toFixed(2)
                          }}
                        </span>
                      </template>
                    </sw-table-column>


                    <sw-table-column :sortable="false" :filterable="false" cell-class="action-dropdown no-click">
                      <template slot-scope="row">
                        <span>{{ $t('general.actions') }}</span>

                        <sw-dropdown>
                          <dot-icon slot="activator" />

 <!-- nuevo edit  -->
                          <sw-dropdown-item
                  @click="openUpdateExtModal(row)"
                  v-if="editextension == true"
                >
                  <pencil-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.edit') }}
                </sw-dropdown-item>
                          <!-- seccion para cambiar el precio -->
                          <sw-dropdown-item v-if="editextension == true" @click="openUpdatePriceExtModal(row)">
                            <refresh-icon class="h-5 mr-3 text-gray-600" />
                            {{ $t('general.modify_price') }}
                          </sw-dropdown-item>
                        </sw-dropdown>
                      </template>
                    </sw-table-column>
                  </sw-table-component>

                  <!------------  TOTALS  ------------>
                  <div class="
                      block
                      my-10
                      table-foot
                      lg:justify-between
                      lg:flex
                      lg:items-start
                    ">
                    <div class="w-full lg:w-1/2"></div>

                    <div class="
                        px-5
                        py-4
                        mt-6
                        bg-white
                        border border-gray-200 border-solid
                        rounded
                        table-total
                        lg:mt-0
                      ">
                      <!------- COUNT ------->
                      <div class="flex items-center justify-between w-full">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">
                          {{ $t('pbx_services.count') }}
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            m-0
                            text-lg text-black
                            uppercase
                          ">
                          <span>{{ this.extensionCount }}</span>
                        </label>
                      </div>

                      <!------- AMOUNT ------->
                      <div class="
                          flex
                          items-center
                          justify-between
                          w-full
                          pt-2
                          mt-5
                          border-t border-gray-200 border-solid
                        ">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">
                          {{ $t('pbx_services.total') }}
                          {{ $t('pbx_services.amount') }}:
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            text-lg
                            uppercase
                            text-primary-400
                          ">
                          <!--<div v-html="$utils.formatMoney(this.extensionTotal, defaultCurrency)" />-->
                          <span>
                            {{
                                defaultCurrency.symbol +
                                ' ' +
                                this.extensionTotal.toFixed(2)
                            }}
                          </span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!--------------------- DIDs ----------------------->
          <div class="tabs mb-5 grid col-span-12 pt-6">
            <div class="border-b tab">
              <div class="relative">
                <input class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4" type="checkbox" />
                <header class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label">
                  <span class="text-gray-500 uppercase sw-section-title">
                    {{ $t('corePbx.dashboard.did') }}
                  </span>
                  <div class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test">
                    <!-- icon by feathericons.com -->
                    <svg aria-hidden="true" class="" data-reactid="266" fill="none" height="24" stroke="#606F7B"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24"
                      xmlns="http://www.w3.org/2000/svg">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </div>
                </header>

                <div class="tab-content-slide">


                  <div class="flex flex-wrap items-center justify-end">
                    <sw-button
                      variant="primary"
                      size="lg"
                      class="w-full md:w-auto mb-2 md:mb-0"
                      @click="openAddDidModal()"
                    >
                      <plus-icon class="w-6 h-6 mr-1 -ml-2" />
                      {{ $t('corePbx.tenants.add') }}
                    </sw-button>
                  </div>

                  <div class="text-grey-darkest">
                    <div class="flex base-tabs"></div>
                  </div>

                  <sw-table-component ref="table" :show-filter="false" :data="fetchDIDsData" table-class="table">
                    <sw-table-column :sortable="true" :label="$t('pbx_services.did_channel')" show="number">


                      <template slot-scope="row">
                        {{ row.number }}


                        <div v-if="row.invoice_prorate == 0 && row.date_prorate != null">
                          <p><b>{{ $t('general.warning_service9') }}: </b> {{ row.date_prorate }}</p>
                          <p><b>{{ $t('general.warning_service10') }}: </b> {{ row.prorate / 100 }}</p>
                        </div>
                      </template>
                    </sw-table-column>

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.destination')" show="ext" />

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.type')" show="type" />

                    <sw-table-column :sortable="false" :label="$t('general.did_plantilla')" show="profile_name">


                      <template slot-scope="row">

                        <div v-if="row.name_prefix">
                          <div v-if="row.name_prefix != null">
                            {{ row.name_prefix }}
                          </div>
                        </div>

                        <div v-if="!row.name_prefix">
                          <div v-if="row.name_prefix == null">
                            {{ row.profile_name }}
                          </div>
                        </div>



                      </template>
                    </sw-table-column>
                    <sw-table-column :sortable="false" :label="$tc('pbx_services.pro_ext_price2')" show="profile_rate">
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.pro_ext_price2') }}</span>
                            <!--  <div
                                                        v-html="$utils.formatMoney(row.profile_rate, defaultCurrency)"
                                                    /> -->
                                                    <span>{{
                                defaultCurrency.symbol +
                                ' ' +
                                row.profile_rate
                            }}</span>
                      </template>
                    </sw-table-column>

                    <!-- <sw-table-column
                                            :sortable="true"
                                            :label="$tc('pbx_services.call_rating')"
                                            show="call_rating"
                                        >
                                            <template slot-scope="row">
                                                <span>{{ $tc('pbx_services.call_rating') }}</span>
                                                <span>{{ 0 }}</span>
                                            </template>
                                        </sw-table-column> -->

                    <sw-table-column :sortable="false" :filterable="false" cell-class="action-dropdown no-click">
                      <template slot-scope="row">
                        <span>{{ $t('general.actions') }}</span>

                        <sw-dropdown>
                          <dot-icon slot="activator" />

                          <!-- seccion para editar dids -->
                          <sw-dropdown-item  v-if="editextension == true" 
                  @click="openUpdateDIDModal(row)"
                >
                  <pencil-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.edit') }}
                </sw-dropdown-item>
                          
                          <!-- seccion para cambiar el precio -->
                          <sw-dropdown-item v-if="editextension == true" @click="openUpdatePriceDIDModal(row)">
                            <refresh-icon class="h-5 mr-3 text-gray-600" />
                            {{ $t('general.modify_price') }}
                          </sw-dropdown-item>

                        </sw-dropdown>

                        
                      </template>
                    </sw-table-column>
                  </sw-table-component>

                  <!------------  TOTALS  ------------>
                  <div class="
                      block
                      my-10
                      table-foot
                      lg:justify-between
                      lg:flex
                      lg:items-start
                    ">
                    <div class="w-full lg:w-1/2"></div>

                    <div class="
                        px-5
                        py-4
                        mt-6
                        bg-white
                        border border-gray-200 border-solid
                        rounded
                        table-total
                        lg:mt-0
                      ">
                      <!------- COUNT ------->
                      <div class="flex items-center justify-between w-full">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">
                          {{ $t('pbx_services.count') }}
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            m-0
                            text-lg text-black
                            uppercase
                          ">
                          <span>{{ this.didCount }}</span>
                        </label>
                      </div>

                      <!------- AMOUNT ------->
                      <div class="
                          flex
                          items-center
                          justify-between
                          w-full
                          pt-2
                          mt-5
                          border-t border-gray-200 border-solid
                        ">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">
                          {{ $t('pbx_services.total') }}
                          {{ $t('pbx_services.amount') }}:
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            text-lg
                            uppercase
                            text-primary-400
                          ">
                          <!--<div v-html="$utils.formatMoney(this.didTotal, defaultCurrency)" />-->
                          <span>{{
    defaultCurrency.symbol +
    ' ' +
    this.didTotal.toFixed(2)
}}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!--------------------- ITEMS ----------------------->
          <div class="tabs mb-5 grid col-span-12 pt-6">
            <div class="border-b tab">
              <div class="relative">
                <input class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4" type="checkbox" />
                <header class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label">
                  <span class="text-gray-500 uppercase sw-section-title">
                    {{ $t('corePbx.items') }}
                  </span>
                  <div class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test">
                    <!-- icon by feathericons.com -->
                    <svg aria-hidden="true" class="" data-reactid="266" fill="none" height="24" stroke="#606F7B"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24"
                      xmlns="http://www.w3.org/2000/svg">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </div>
                </header>

                <div class="tab-content-slide">

                  <div class="flex flex-wrap items-center justify-end">
                    <sw-button
                      variant="primary"
                      size="lg"
                      class="w-full md:w-auto mb-2 md:mb-0"
                      @click="openAddItemModal()"
                    >
                      <plus-icon class="w-6 h-6 mr-1 -ml-2" />
                      {{ $t('corePbx.tenants.add') }}
                    </sw-button>
                  </div>

                  <div class="text-grey-darkest">
                    <div class="flex base-tabs"></div>
                  </div>

                  <sw-table-component ref="table" :show-filter="false" :data="fetchItemsData" table-class="table">
                    <sw-table-column :sortable="true" :label="$t('pbx_services.name')" show="name" >
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.name') }}</span>
                        <span> <b> {{ row.name }} </b></span>
                        <div v-if="row.end_period_act == 1 || row.end_period_act == true " style="font-size:12px">
                          <p> <b>{{ $tc('packages.end_period_act') }}: </b> {{ $tc('packages.discount_enabled') }}</p>
                          <p> <b>{{ $tc('packages.end_period_num') }}: </b> {{ row.end_period_number }} </p>
                        </div>
                      </template>
                    </sw-table-column>

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.description')" show="description" />

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.quantity')" show="quantity" />

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.discount')" show="discount">
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.discount') }}</span>
                        <div class="flex flex-auto" role="group">
                          <span class="flex" v-if="row.discount_type == 'fixed'">
                            {{  defaultCurrency.symbol }}
                          </span>
                          <span>{{ row.discount }}</span>
                          <span class="flex" v-if="row.discount_type == 'percentage'">
                            %
                          </span>
                        </div>
                      </template>
                    </sw-table-column>

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.total')" show="total">
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.total') }}</span>
                        <span>
                          <div v-html="
  $utils.formatMoney(row.total, defaultCurrency)
" />
                        </span>
                      </template>
                    </sw-table-column>

             
                  </sw-table-component>

                  <!------------  TOTALS  ------------>
                  <div class="
                      block
                      my-10
                      table-foot
                      lg:justify-between
                      lg:flex
                      lg:items-start
                    ">
                    <div class="w-full lg:w-1/2"></div>

                    <div class="
                        px-5
                        py-4
                        mt-6
                        bg-white
                        border border-gray-200 border-solid
                        rounded
                        table-total
                        lg:mt-0
                      ">
                      <!------- COUNT ------->
                      <div class="flex items-center justify-between w-full">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">
                          {{ $t('pbx_services.count') }}
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            m-0
                            text-lg text-black
                            uppercase
                          ">
                          <span>{{ this.itemCount }}</span>
                        </label>
                      </div>

                      <!------- AMOUNT ------->
                      <div class="
                          flex
                          items-center
                          justify-between
                          w-full
                          pt-2
                          mt-5
                          border-t border-gray-200 border-solid
                        ">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">
                          {{ $t('pbx_services.total') }}
                          {{ $t('pbx_services.amount') }}:
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            text-lg
                            uppercase
                            text-primary-400
                          ">
                          <div v-html="
  $utils.formatMoney(
    this.itemTotal,
    defaultCurrency
  )
" />
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!--------------- ADDITIONAL CHARGES --------------->
          <div class="tabs mb-5 grid col-span-12 pt-6">
            <div class="border-b tab">
              <div class="relative">
                <input class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4" type="checkbox" />
                <header class=" col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label">
                  <span class="text-gray-500 uppercase sw-section-title">
                    {{ $t('corePbx.charges') }}
                  </span>
                  <div class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test">
                    <!-- icon by feathericons.com -->
                    <svg aria-hidden="true" class="" data-reactid="266" fill="none" height="24" stroke="#606F7B"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24"
                      xmlns="http://www.w3.org/2000/svg">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </div>
                </header>

                <div class="tab-content-slide">
                  <div class="text-grey-darkest">
                    <div class="flex base-tabs"></div>
                  </div>

                  <sw-table-component ref="table" :show-filter="false" :data="fetchAdditionalChargesData"
                    table-class="table">
                    <sw-table-column :sortable="true" :label="$t('pbx_services.name')" show="description" />

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.price')" show="amount">
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.price') }}</span>
                        <span>{{
    defaultCurrency.symbol + ' ' + row.amount
}}</span>
                      </template>
                    </sw-table-column>

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.type_from')" show="type_from" />

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.profile_name')" show="profile_name" />

                    <!-- data aditional -->
                    <sw-table-column 
                      :sortable="true"
                      :label="$tc('pbx_services.quantity')"
                      show="quantity"
                    />

                    <sw-table-column 
                      :sortable="true"
                      :label="$tc('pbx_services.total')"
                      show="total"
                    >
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.total') }}</span>
                        <span>{{
                          defaultCurrency.symbol + ' ' + row.total
                        }}</span>
                      </template>
                    </sw-table-column>
                    <!--  -->

              
                  </sw-table-component>

                  <!------------  TOTALS  ------------>
                  <div class="
                      block
                      my-10
                      table-foot
                      lg:justify-between
                      lg:flex
                      lg:items-start
                    ">
                    <div class="w-full lg:w-1/2"></div>

                    <div class="
                        px-5
                        py-4
                        mt-6
                        bg-white
                        border border-gray-200 border-solid
                        rounded
                        table-total
                        lg:mt-0
                      ">
                      <!------- COUNT ------->
                      <div class="flex items-center justify-between w-full">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">
                          {{ $t('pbx_services.count') }}
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            m-0
                            text-lg text-black
                            uppercase
                          ">
                          <span>{{ this.chargesCount }}</span>
                        </label>
                      </div>

                      <!------- AMOUNT ------->
                      <div class="
                          flex
                          items-center
                          justify-between
                          w-full
                          pt-2
                          mt-5
                          border-t border-gray-200 border-solid
                        ">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">
                          {{ $t('pbx_services.total') }}
                          {{ $t('pbx_services.amount') }}:
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            text-lg
                            uppercase
                            text-primary-400
                          ">
                          <!--<div v-html="$utils.formatMoney(this.extensionTotal, defaultCurrency)" />-->
                          <span>{{
    defaultCurrency.symbol +
    ' ' +
    this.chargesTotal.toFixed(2)
}}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- CUSTOM APP RATE -->
          <div class="tabs mb-5 grid col-span-12 pt-8">
            <div class="border-b tab">
              <div class="relative">
                <input class="
                    w-full
                    absolute
                    z-10
                    cursor-pointer
                    opacity-0
                    h-5
                    top-4
                  " type="checkbox" />
                <header class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label">
                  <span class="text-gray-500 uppercase sw-section-title">
                    {{ $t('general.warning_service15') }}
                  </span>
                  <div class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test">
                    <!-- icon by feathericons.com -->
                    <svg aria-hidden="true" class="" data-reactid="266" fill="none" height="24" stroke="#606F7B"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24"
                      xmlns="http://www.w3.org/2000/svg">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </div>
                </header>

                <div class="tab-content-slide">
                  <div class="text-grey-darkest">
                    <div class="flex base-tabs"></div>
                  </div>

                  <sw-table-component ref="ext_table" :show-filter="false" :data="fetchCustomAppRateData"
                    table-class="table">
                    <sw-table-column :sortable="true" :label="$t('pbx_services.name')" show="app_name">
                    </sw-table-column>

                    <sw-table-column :sortable="true" :label="$t('general.quantity')" show="quantity" />

                    <sw-table-column :sortable="true" :label="$t('general.price')" sort-as="total">
                      <template slot-scope="row">
                        <span>

                          {{ $t('general.price') }}
                        </span>

                        <div v-html="$utils.formatMoney(row.costo * 100)" />

                      </template>
                    </sw-table-column>


                  </sw-table-component>

                  <!------------  TOTALS  ------------>
                  <div class="
                      block
                      my-10
                      table-foot
                      lg:justify-between
                      lg:flex
                      lg:items-start
                    ">
                    <div class="w-full lg:w-1/2"></div>

                    <div class="
                        px-5
                        py-4
                        mt-6
                        bg-white
                        border border-gray-200 border-solid
                        rounded
                        table-total
                        lg:mt-0
                      ">
                      <!------- COUNT ------->
                      <div class="flex items-center justify-between w-full">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">
                          

                          {{ $t('general.tquantity') }}
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            m-0
                            text-lg text-black
                            uppercase
                          ">
                          <span>{{ this.totalQuantity }}</span>
                        </label>
                      </div>

                      <!------- AMOUNT ------->
                      <div class="
                          flex
                          items-center
                          justify-between
                          w-full
                          pt-2
                          mt-5
                          border-t border-gray-200 border-solid
                        ">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">

                          {{ $t('general.tprice') }}
                         
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            text-lg
                            uppercase
                            text-primary-400
                          ">
                          <!--<div v-html="$utils.formatMoney(this.extensionTotal, defaultCurrency)" />-->
                          <div v-html="$utils.formatMoney(totalCosto * 100)" />
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- TAXES -->
          <div class="tabs mb-5 grid col-span-12 pt-8">
            <div class="border-b tab">
              <div class="relative">
                <input class="
                    w-full
                    absolute
                    z-10
                    cursor-pointer
                    opacity-0
                    h-5
                    top-4
                  " type="checkbox" />
                <header class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label">
                  <span class="text-gray-500 uppercase sw-section-title">
                    

                    {{ $t('general.taxes') }}
                  </span>
                  <div class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test">
                    <!-- icon by feathericons.com -->
                    <svg aria-hidden="true" class="" data-reactid="266" fill="none" height="24" stroke="#606F7B"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24"
                      xmlns="http://www.w3.org/2000/svg">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </div>
                </header>

                <div class="tab-content-slide">
                  <div class="text-grey-darkest">
                    <div class="flex base-tabs"></div>
                  </div>

                  <sw-table-component ref="taxes_table" :show-filter="false" :data="taxes"
                    table-class="table">
                    <sw-table-column :sortable="true" :label="$t('pbx_services.name')" show="name">
                    </sw-table-column>
                    <sw-table-column :sortable="true" :label="$t('tax_types.percent')" show="percent" />
                    <sw-table-column :sortable="true" :label="$t('general.type')" show="type_name" />
                    <sw-table-column :sortable="true" :label="$t('general.amount')" sort-as="amount">
                      <template slot-scope="row">
                        <span>{{ $t('general.amount') }}</span>
                        <div v-if="row.type != 'cdr'" v-html="$utils.formatMoney(row.amount ,  defaultCurrency )" />
                        <span v-else >N/A</span>

                      </template>
                    </sw-table-column>
                  </sw-table-component>
                  
                  <!------------  TOTALS  ------------>
                  <div class="
                      block
                      my-10
                      table-foot
                      lg:justify-between
                      lg:flex
                      lg:items-start
                    ">
                    <div class="w-full lg:w-1/2"></div>

                    <div class="
                        px-5
                        py-4
                        mt-6
                        bg-white
                        border border-gray-200 border-solid
                        rounded
                        table-total
                        lg:mt-0
                      ">
                      <!------- COUNT ------->
                      <div class="flex items-center justify-between w-full">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">
                          {{ $t('general.tquantity') }}
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            m-0
                            text-lg text-black
                            uppercase
                          ">
                          <span>{{ this.taxes.length }}</span>
                        </label>
                      </div>

                      <!------- AMOUNT ------->
                      <div class="
                          flex
                          items-center
                          justify-between
                          w-full
                          pt-2
                          mt-5
                          border-t border-gray-200 border-solid
                        ">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">
                          {{ $t('general.tprice') }}
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            text-lg
                            uppercase
                            text-primary-400
                          ">
                          <!--<div v-html="$utils.formatMoney(this.extensionTotal, defaultCurrency)" />-->
                          <div v-html="$utils.formatMoney( totalTaxes  )" />
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!------------------ CALL HISTORY ------------------>
          <div class="tabs mb-5 grid col-span-12 pt-6">
            <div class="border-b tab">
              <div class="relative">
                <input class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4" type="checkbox" />
                <header class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label">
                  <span class="text-gray-500 uppercase sw-section-title">
                    {{ $t('corePbx.dashboard.call_history') }}
                  </span>
                  <div class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test">
                    <!-- icon by feathericons.com -->
                    <svg aria-hidden="true" class="" data-reactid="266" fill="none" height="24" stroke="#606F7B"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24"
                      xmlns="http://www.w3.org/2000/svg">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </div>
                </header>

                <div class="tab-content-slide">
                  <div class="text-grey-darkest">
                    <div class="flex base-tabs"></div>
                  </div>

                  <sw-table-component ref="cdr_table" :show-filter="false" :data="fetchCallHistoryData"
                    table-class="table">
                    <sw-table-column :sortable="true" :label="$t('corePbx.dashboard.from')" show="from" />

                    <sw-table-column :sortable="true" :label="$tc('corePbx.dashboard.to')" show="to" />

                    <sw-table-column :sortable="true" :label="$tc('corePbx.dashboard.date')"
                      show="formatted_start_date" />

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.type')" show="type">
                      <template slot-scope="row">
                        <span>{{ $t('general.cdr_type') }}</span>

                        <div v-if="row.type == 0">{{ $t('general.inbound') }}</div>
                        <div v-if="row.type == 1">{{ $t('general.outbound') }}</div>
                      </template>
                    </sw-table-column>

                    <sw-table-column :sortable="true" :label="$tc('corePbx.dashboard.totald')"
                      show="formatted_duration" />

                    <sw-table-column :sortable="true" :label="$tc('corePbx.dashboard.totalr')"
                      show="formatted_round_duration" />

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.amount')" show="cost">
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.amount') }}</span>

                        <div v-if="row.billed_at == null">{{ $t('general.pending') }}</div>

                        <div v-if="row.billed_at != null">
                          <div v-if="row.exclusive_seconds == 0">
                            0
                          </div>

                          <div v-if="row.exclusive_seconds != 0">
                            {{ row.getFormattedExcusiveCost }}
                          </div>
                        </div>
                      </template>
                    </sw-table-column>

             
                  </sw-table-component>

                  <div class="w-full flex justify-end">
                    <sw-button tag-name="router-link"
                      :to="`/admin/customers/${$route.params.id}/pbx-service/${$route.params.pbx_service_id}/callHistory`"
                      variant="primary-outline">
                      {{ $t('general.search') }}
                      <!-- {{ $t('general.filter') }} -->
                      <!-- <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" /> -->
                    </sw-button>
                  </div>

                  <!------------  TOTALS  ------------>
                  <div class="
                      block
                      mt-5
                      mb-10
                      table-foot
                      lg:justify-between
                      lg:flex
                      lg:items-start
                    ">
                    <div class="w-full lg:w-1/2"></div>

                    <div class="
                        px-5
                        py-4
                        mt-6
                        bg-white
                        border border-gray-200 border-solid
                        rounded
                        table-total
                        lg:mt-0
                      ">
                      <!------- COUNT ALL CDR------->
                      <div class="flex items-center justify-between w-full">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">
                          {{ $t('pbx_services.all_cdr') }}
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            m-0
                            text-lg text-black
                            uppercase
                          ">
                          <span>{{ this.callHistoryCount }}</span>
                        </label>
                      </div>

                      <!------- COUNT ALL CDR------->
                      <div class="flex items-center justify-between w-full">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">
                          {{ $t('pbx_services.billed_cdr') }}
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            m-0
                            text-lg text-black
                            uppercase
                          ">
                          <span>{{ this.billed_cdr.toFixed(2) }}</span>
                        </label>
                      </div>

                      <!------- COUNT ALL CDR------->
                      <div class="flex items-center justify-between w-full">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">
                          {{ $t('pbx_services.billed_time') }}
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            m-0
                            text-lg text-black
                            uppercase
                          ">
                          <span>{{ this.billed_time }}</span>
                        </label>
                      </div>

                      <!------- AMOUNT ------->
                      <div class="
                          flex
                          items-center
                          justify-between
                          w-full
                          pt-2
                          mt-5
                          border-t border-gray-200 border-solid
                        ">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                          ">
                          {{ $t('pbx_services.billed_cost') }}:
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            text-lg
                            uppercase
                            text-primary-400
                          ">
                          <!--<div v-html="$utils.formatMoney(this.extensionTotal, defaultCurrency)" />-->
                          <span> $ {{ this.callHistoryTotal.toFixed(2) }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!------------------ CUSTOM DESTINATION ------------------>
          <div class="tabs mb-5 grid col-span-12 pt-6">
            <div class="border-b tab">
              <div class="relative">
                <input class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4" type="checkbox" />
                <header class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label">
                  <span class="text-gray-500 uppercase sw-section-title">
                    {{ $t('pbx_services.custom_destinations') }}
                  </span>
                  <div class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test">
                    <svg aria-hidden="true" class="" data-reactid="266" fill="none" height="24" stroke="#606F7B"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24"
                      xmlns="http://www.w3.org/2000/svg">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </div>
                </header>

                <div class="tab-content-slide">
                  <div class="text-grey-darkest">
                    <div class="flex base-tabs"></div>
                  </div>

                  <sw-table-component ref="table" :show-filter="false" :data="fetchCustomDestinationData"
                    table-class="table">
                    <sw-table-column :sortable="true" :label="$t('corePbx.dashboard.from')" show="from" />

                    <sw-table-column :sortable="true" :label="$tc('corePbx.dashboard.to')" show="to" />

                    <sw-table-column :sortable="true" :label="$tc('corePbx.dashboard.date')"
                      show="formatted_start_date" />

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.type')" show="type">
                      <template slot-scope="row">
                        <span>{{ $t('general.cdr_type') }}</span>

                        <div v-if="row.type == 0">{{ $t('general.inbound') }}</div>
                        <div v-if="row.type == 1">{{ $t('general.outbound') }}</div>
                      </template>
                    </sw-table-column>

                    <sw-table-column :sortable="true" :label="$tc('corePbx.dashboard.totald')"
                      show="formatted_duration" />

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.rate')" show="rate">
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.amount') }}</span>
                        <div v-if="row.custom_rate != null">
                          {{ row.custom_rate.rate_per_minute }}
                        </div>
                      </template>
                    </sw-table-column>

                    <sw-table-column :sortable="true" :label="$tc('corePbx.dashboard.totalr')"
                      show="formatted_round_duration" />

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.amount')" show="cost">
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.amount') }}</span>

                        <div v-if="row.billed_at == null">{{ $t('general.pending') }}</div>

                        <div v-if="row.billed_at != null">
                          <div v-if="row.exclusive_seconds == 0">
                            0
                          </div>

                          <div v-if="row.exclusive_seconds != 0">
                            {{ row.getFormattedExcusiveCost }}
                          </div>
                        </div>
                      </template>
                    </sw-table-column>
>

                  </sw-table-component>

                  <!------------  TOTALS  ------------>
                  <div class="block my-10 table-foot lg:justify-between lg:flex lg:items-start">
                    <div class="w-full lg:w-1/2"></div>

                    <div
                      class="px-5 py-4 mt-6 bg-white border border-gray-200 border-solid rounded table-total lg:mt-0">
                      <!------- COUNT ALL CDR------->
                      <div class="flex items-center justify-between w-full">
                        <label class="text-sm font-semibold leading-5 text-gray-500 uppercase">
                          {{ $t('pbx_services.all_cdr') }}
                        </label>
                        <label class="flex items-center justify-center m-0 text-lg text-black uppercase">
                          <span>{{ this.customDestinationCount }}</span>
                        </label>
                      </div>

                      <!------- COUNT ALL CDR------->
                      <div class="flex items-center justify-between w-full">
                        <label class="text-sm font-semibold leading-5 text-gray-500 uppercase">
                          {{ $t('pbx_services.billed_cdr') }}
                        </label>
                        <label class="flex items-center justify-center m-0 text-lg text-black uppercase">
                          <span>{{ this.ctmDestBilledCDR.toFixed(2) }}</span>
                        </label>
                      </div>

                      <!------- COUNT ALL CDR------->
                      <div class="flex items-center justify-between w-full">
                        <label class="text-sm font-semibold leading-5 text-gray-500 uppercase">
                          {{ $t('pbx_services.billed_time') }}
                        </label>
                        <label class="flex items-center justify-center m-0 text-lg text-black uppercase">
                          <span>{{ this.ctmDestBilledTime }}</span>
                        </label>
                      </div>

                      <!------- AMOUNT ------->
                      <div
                        class="flex items-center justify-between w-full pt-2 mt-5 border-t border-gray-200 border-solid">
                        <label class="text-sm font-semibold leading-5 text-gray-500 uppercase">
                          {{ $t('pbx_services.billed_cost') }}:
                        </label>
                        <label class="flex items-center justify-center text-lg uppercase text-primary-400">
                          <!--<div v-html="$utils.formatMoney(this.extensionTotal, defaultCurrency)" />-->
                          <span> $ {{ this.customDestinationTotal.toFixed(2) }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!------------------ PREPAID CHARGES ------------------>
          <div v-if="getStatusPayment == 'prepaid'" class="tabs mb-5 grid col-span-12 pt-6">
            <div class="border-b tab">
              <div class="relative">
                <input class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4" type="checkbox" />
                <header class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label">
                  <span class="text-gray-500 uppercase sw-section-title">
                    {{ $t('pbx_services.prepaid_charges') }}
                  </span>
                  <div class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test">
                    <!-- icon by feathericons.com -->
                    <svg aria-hidden="true" class="" data-reactid="266" fill="none" height="24" stroke="#606F7B"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24"
                      xmlns="http://www.w3.org/2000/svg">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </div>
                </header>

                <div class="tab-content-slide">
                  <div class="text-grey-darkest">
                    <div class="flex base-tabs"></div>
                  </div>

                  <sw-table-component ref="table" :show-filter="false" :data="fetchPrepaidChargesData"
                    table-class="table">
                    <sw-table-column :sortable="true" :label="$t('pbx_services.name')" show="name" />

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.type')" show="type" />

                    <sw-table-column :sortable="true" :label="$tc('corePbx.dashboard.date')" show="date" />

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.price')" show="amout">
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.price') }}</span>
                        <span>
                          {{ defaultCurrency.symbol + ' ' + row.amout }}
                        </span>
                      </template>
                    </sw-table-column>

                    <sw-table-column :sortable="true" :label="$tc('pbx_services.tax_amount')" show="taxamount">
                      <template slot-scope="row">
                        <span>{{ $tc('pbx_services.tax_amount') }}</span>
                        <span>
                          {{ defaultCurrency.symbol + ' ' + row.taxamount }}
                        </span>
                      </template>
                    </sw-table-column>

                  
                  </sw-table-component>

                  <!------------  TOTALS  ------------>
                  <div class="block my-10 table-foot lg:justify-between lg:flex lg:items-start">
                    <div class="w-full lg:w-1/2"></div>

                    <div
                      class="px-5 py-4 mt-6 bg-white border border-gray-200 border-solid rounded table-total lg:mt-0">

                      <!------- COUNT ALL CDR------->
                      <div class="flex items-center justify-between w-full">
                        <label class="text-sm font-semibold leading-5 text-gray-500 uppercase">
                          {{ $tc('general.currenttotalprepaidcharges') }}:
                        </label>
                        <label class="flex items-center justify-center m-0 text-lg text-black uppercase">
                          <span>{{ this.prepaidtotalcurrent.toFixed(2) }}</span>
                        </label>
                      </div>

                      <!------- AMOUNT ------->
                      <div
                        class="flex items-center justify-between w-full pt-2 mt-5 border-t border-gray-200 border-solid">
                        <label class="text-sm font-semibold leading-5 text-gray-500 uppercase">
                          {{ $tc('general.currenttotalprepaidtaxes') }}:
                        </label>
                        <label class="flex items-center justify-center text-lg uppercase text-primary-400">
                          <!--<div v-html="$utils.formatMoney(this.extensionTotal, defaultCurrency)" />-->
                          <span> $ {{ this.prepaidtotalcurrenttax.toFixed(2) }}</span>
                        </label>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

          <!------------------- INVOICES ------------------->
          <div class="tabs mb-5 grid col-span-12 pt-5">
            <div class="border-b tab">
              <div class="relative">
                <input class="
                    w-full
                    absolute
                    z-10
                    cursor-pointer
                    opacity-0
                    h-5
                    top-4
                  " type="checkbox" />
                <header class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label">
                  <span class="text-gray-500 uppercase sw-section-title">
                    
                    {{ $tc('general.invoices') }}
                  </span>
                  <div class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test">
                    <!-- icon by feathericons.com -->
                    <svg aria-hidden="true" class="" data-reactid="266" fill="none" height="24" stroke="#606F7B"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24"
                      xmlns="http://www.w3.org/2000/svg">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </div>
                </header>

                <div class="tab-content-slide">
                  <div class="text-grey-darkest">
                    <div class="flex base-tabs"></div>
                  </div>

                  <sw-table-component ref="table" :show-filter="false" :data="fetchInvoicesPerServicePbxData"
                    table-class="table">
                    <sw-table-column :sortable="true" :label="$t('invoices.number')" show="invoice_number">
                      <template slot-scope="row">
                        <span>{{ $t('invoices.number') }}</span>
                        <router-link :to="`/admin/invoices/${row.id}/view`" class="font-medium text-primary-500">
                          {{ row.invoice_number }}
                        </router-link>
                      </template>
                    </sw-table-column>

                    <sw-table-column :sortable="true" :label="$t('invoices.date')" show="invoice_date" />

                    <sw-table-column
          :sortable="true"
          :label="$t('invoices.status')"
          sort-as="status"
        >
          <template slot-scope="row">
            <span> {{ $t('invoices.status') }}</span>

            <sw-badge
              :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
              :color="$utils.getBadgeStatusColor(row.status).color"
            >
              {{ row.status.replace('_', ' ') }}
            </sw-badge>
          </template>
        </sw-table-column>

          <sw-table-column
          :sortable="true"
          :label="$t('invoices.paid_status')"
          sort-as="paid_status"
        >
          <template slot-scope="row">
            <span>{{ $t('invoices.paid_status') }}</span>

            <sw-badge
              :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
              :color="$utils.getBadgeStatusColor(row.status).color"
            >
              {{ row.paid_status.replace('_', ' ') }}
            </sw-badge>
          </template>
        </sw-table-column>

                    <sw-table-column :sortable="true" :label="$t('invoices.total')" sort-as="total">
                      <template slot-scope="row">
                        <span>{{$t('general.total')}}</span>

                        <div v-html="$utils.formatMoney(row.total)" />
                      </template>
                    </sw-table-column>

                    <sw-table-column :sortable="true" :label="$t('invoices.amount_due')" sort-as="total">
                      <template slot-scope="row">
                        <span>{{$t('invoices.amount_due')}}</span>

                        <div v-html="$utils.formatMoney(row.due_amount)" />
                      </template>
                    </sw-table-column>

                    <sw-table-column :sortable="false" :filterable="false">
                      <template slot-scope="row">
                        <span>{{ $t('invoices.action') }}</span>

                        <sw-dropdown>
                          <dot-icon slot="activator" />
                          <span v-if="IsArchivedActived != true">
                            <sw-dropdown-item v-if="isSuperAdmin && row.noeditable == 0" tag-name="router-link"
                              :to="`/admin/invoices/${row.id}/edit`">
                              <pencil-icon class="h-5 mr-3 text-gray-600" />
                              {{ $t('general.edit') }}
                            </sw-dropdown-item>

                            <sw-dropdown-item v-if="isSuperAdmin" tag-name="router-link"
                              :to="`/admin/invoices/${row.id}/view`">
                              <eye-icon class="h-5 mr-3 text-gray-600" />
                              {{ $t('invoices.view') }}
                            </sw-dropdown-item>

                            <sw-dropdown-item v-else tag-name="router-link" :to="`/admin/invoices/${row.id}/view`">
                              <eye-icon class="h-5 mr-3 text-gray-600" />
                              {{ $t('invoices.view') }}
                            </sw-dropdown-item>

                            <sw-dropdown-item v-if="row.status == 'DRAFT' && isSuperAdmin" @click="sendInvoice(row)">
                              <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                              {{ $t('invoices.send_invoice') }}
                            </sw-dropdown-item>

                            <sw-dropdown-item v-if="
  row.status === 'SENT' ||
  (row.status === 'VIEWED' && isSuperAdmin)
" @click="sendInvoice(row)">
                              <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                              {{ $t('invoices.resend_invoice') }}
                            </sw-dropdown-item>

                            <sw-dropdown-item v-if="row.status == 'DRAFT' && isSuperAdmin"
                              @click="markInvoiceAsSent(row.id)">
                              <check-circle-icon class="h-5 mr-3 text-gray-600" />
                              {{ $t('invoices.mark_as_sent') }}
                            </sw-dropdown-item>

                            <sw-dropdown-item v-if="
  row.status === 'SENT' ||
  row.status === 'VIEWED' ||
  (row.status === 'OVERDUE' && isSuperAdmin)
" tag-name="router-link" :to="`/admin/payments/${row.id}/create`">
                              <credit-card-icon class="h-5 mr-3 text-gray-600" />
                              {{ $t('payments.record_payment') }}
                            </sw-dropdown-item>

                            <sw-dropdown-item v-if="!isSuperAdmin" tag-name="router-link" :to="`#`">
                              <credit-card-icon class="h-5 mr-3 text-gray-600" />
                              {{ $t('payments.record_payment') }}
                            </sw-dropdown-item>

                            <sw-dropdown-item v-if="isSuperAdmin && row.status != 'COMPLETED' &&
  row.paid_status === 'UNPAID'" @click="removeInvoice(row.id)">
                              <trash-icon class="h-5 mr-3 text-gray-600" />
                              {{ $t('general.delete') }}
                            </sw-dropdown-item>
                          </span>
                          <span v-else>
                            <sw-dropdown-item @click="Restore(row)">
                              <save-icon class="h-5 mr-3 text-gray-600" />
                              {{ $t('general.restore') }}
                            </sw-dropdown-item>
                          </span>
                        </sw-dropdown>
                      </template>
                    </sw-table-column>
                  </sw-table-component>

                  <!------------  TOTALS  ------------>
                  <div class="
                      block
                      my-10
                      table-foot
                      lg:justify-between
                      lg:flex
                      lg:items-start
                    ">
                    <div class="w-full lg:w-1/2"></div>

                    <div class="
                        px-5
                        py-4
                        mt-6
                        bg-white
                        border border-gray-200 border-solid
                        rounded
                        table-total
                        lg:mt-0
                      ">
                      <!------- COUNT ------->
                      <div class="flex items-center justify-between w-full">
                        <label class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                            mr-12
                          ">
                          {{ $t('pbx_services.count') }}
                        </label>
                        <label class="
                            flex
                            items-center
                            justify-center
                            m-0
                            text-lg text-black
                            uppercase   
                            ml-12                         
                          ">
                          <span>{{ this.invoicesCount }}</span>
                        </label>
                      </div>

                      <!------- AMOUNT ------->

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import DollarIcon from '../../../components/icon/DollarIcon'
import ContactIcon from '../../../components/icon/ContactIcon'
import InvoiceIcon from '../../../components/icon/InvoiceIcon'
import EstimateIcon from '../../../components/icon/EstimateIcon'
import moment from 'moment'
import { selectedViewCustomer } from '../../../store/modules/customer/getters'

import {
  PencilIcon,
  RefreshIcon,
  EyeIcon,
  CreditCardIcon,
  PaperAirplaneIcon,
  TrashIcon,
  CheckCircleIcon,
  PlusIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    DollarIcon,
    ContactIcon,
    InvoiceIcon,
    EstimateIcon,
    PencilIcon,
    RefreshIcon,
    EyeIcon,
    CreditCardIcon,
    PaperAirplaneIcon,
    TrashIcon,
    CheckCircleIcon,
    PlusIcon,
  },

  data() {
    return {
      taxes: [],
      totalTaxes: 0,
      recurring_charge: '',
      additional_charge: '',
      totalPbxService: 0,
      customDestinationsGroupsInbound: [],
      customDestinationsGroupsOutbound: [],
      //
      isRequestOnGoing: false,
      status: [
        { name: 'Active', value: 'A' },
        { name: 'Pending', value: 'P' },
        { name: 'Suspend', value: 'S' },
        { name: 'Cancelled', value: 'C' },
      ],
      extensionTotal: 0,
      extensionCount: 0,
      didTotal: 0,
      didCount: 0,
      itemTotal: 0,
      itemCount: 0,
      chargesTotal: 0,
      chargesCount: 0,
      callHistoryTotal: 0,
      callHistoryTotalcustom: 0,
      callHistoryCount: 0,
      billed_cost: 0,
      billed_cdr: 0,
      prepaidtotalcurrent: 0,
      prepaidtotalcurrenttax: 0,
      inclusive_minutes_cosumed: 0,
      billed_time: 0,
      customDestinationTotal: 0,
      AddmonthTotal: 0,
      customDestinationCount: 0,
      ctmDestBilledCost: 0,
      ctmDestBilledCDR: 0,
      ctmDestBilledTime: 0,
      ctmDestBilledIncMin: 0,
      package: {
        extensions: 0,
        did: 0,
        modify_server: 0,
        call_ratings: 0,
        profile_did: {
          name: '',
          did_rate: 0,
        },
        profile_extensions: {
          name: '',
          rate: 0,
        },
        international_dialing_code: '',
        national_dialing_code: '',
        inclusive_minutes: 0,
        rate_per_minutes: 0,
        minutes_increments: 0,
        type_time_increment: '',
      },
      commandosDataCdr: {
        calculated: 0,
        calculated_today: 0,
        unCalculated: 0,
      },
      commandosDataJobs: {
        calculate: 0,
        import: 0,
      },
      total_consume: 0,
      paid_consume: 0,
      unpaid_consume: 0,
      total_deb: 0,
      editextension: true,
      invoicesCount: 0,
      IsArchivedActived: false,
      totalCosto: 0,
      totalQuantity: 0,
      formdataExtensions: {
        isNew: true,
      },
    }
  },

  computed: {
    ...mapGetters('customer', ['selectedViewCustomer']),
    ...mapGetters('pbxService', ['selectedPbxService']),
    ...mapGetters('company', ['defaultCurrency']),
    ...mapGetters('user', ['currentUser']),

    serviceStatus() {
      return this.status.find(
        (_status) => this.selectedPbxService.status === _status.value
      )
    },

    allowDiscount() {
      return this.selectedPbxService.allow_discount === 1 ? 'YES' : 'NO'
    },

    getStatusPayment() {
      return this.selectedViewCustomer && this.selectedViewCustomer.customer
        ? this.selectedViewCustomer.customer.status_payment
        : ''
    },

    backgroundCalculateStatus() {
      return this.commandosDataJobs.calculate ? 'I' : 'A'
    },

    backgroundImportStatus() {
      return this.commandosDataJobs.import ? 'I' : 'A'
    },

    isPrepaid() {
      return this.package.status_payment === 'prepaid'
    },

    isSuperAdmin() {
      return this.currentUser.role == 'super admin' ? true : false
    },
  },

  created() {
    window.hub.$on('newCalls', this.reloadCdrTable)
    window.hub.$on('updateExt', this.reloadExtTable)
    this.loadService()
  },
  watch: {
    selectedPbxService(newVal) {
      this.$emit('status', this.serviceStatus.value)
    },
  },
  methods: {
    ...mapActions('pbxService', [
      'fetchPbxService',
      'fetchExtensions',
      'fetchDIDs',
      'fetchItemsPbxService',
      'fetchAdditionalCharges',
      'fetchCallHistory',
      'fetchCommandos',
      'fetchPrepaidCharges',
      'fetchInvoicesPerServicePbx',
      'fetchCustomAppRate',
      'jobInfoPBXService',
      'softDeletePBXService',
      'hardDeletePBXService',
      'fetchAdditionalChargesService',
    ]),

    ...mapActions('modal', ['openModal']),

    ...mapActions('invoice', ['markAsSent', 'deleteInvoice']),

    openAddExtModal() {
      this.formdataExtensions.from = 'viewPBX'

      // Validar si `selectedPbxService` existe y tiene contenido
      if (this.selectedPbxService && this.selectedPbxService.tenant) {
        // Validar si el objeto `tenant` tiene el campo `pbxservertenant_id`
       // console.log(this.selectedPbxService)
        this.formdataExtensions.pbx_service_id = this.selectedPbxService.id
        if (
          this.selectedPbxService.tenant.pbxservertenant_id &&
          this.selectedPbxService.tenant.pbxservertenant_id !== null
        ) {
          // Asignar el valor a `extension.pbxservertenant_id`
          this.formdataExtensions.pbxservertenant_id =
            this.selectedPbxService.tenant.pbxservertenant_id
        } else {
          // Asignar null a `extension.pbxservertenant_id`
          this.formdataExtensions.pbxservertenant_id = null
        }
      } else {
        // Si `selectedPbxService` no existe o no tiene un objeto `tenant`, asignar null
        this.formdataExtensions.pbxservertenant_id = null
      }
      this.openModal({
        title: this.$t('pbx_services.add_new_extension'),
        componentName: 'CreateModalExtensions',
        data: this.formdataExtensions,
      })
    },

    openAddDidModal() {
      this.formdataExtensions.from = 'viewPBX'

      // Validar si `selectedPbxService` existe y tiene contenido
      if (this.selectedPbxService && this.selectedPbxService.tenant) {
        // Validar si el objeto `tenant` tiene el campo `pbxservertenant_id`
        //console.log(this.selectedPbxService)
        this.formdataExtensions.pbx_service_id = this.selectedPbxService.id
        if (
          this.selectedPbxService.tenant.pbxservertenant_id &&
          this.selectedPbxService.tenant.pbxservertenant_id !== null
        ) {
          // Asignar el valor a `extension.pbxservertenant_id`
          this.formdataExtensions.pbxservertenant_id =
            this.selectedPbxService.tenant.pbxservertenant_id
        } else {
          // Asignar null a `extension.pbxservertenant_id`
          this.formdataExtensions.pbxservertenant_id = null
        }
      } else {
        // Si `selectedPbxService` no existe o no tiene un objeto `tenant`, asignar null
        this.formdataExtensions.pbxservertenant_id = null
      }
      this.openModal({
        title: this.$t('pbx_services.add_new_did'),
        componentName: 'CreateDidModal',
        data: this.formdataExtensions,
      })
    },

    openAddItemModal() { 
      this.formdataExtensions.from = 'viewPBX'
      this.formdataExtensions.pbx_service_id = this.selectedPbxService.id
      this.openModal({
        title: this.$t('pbx_services.add_new_item'),
        componentName: 'CreateItemPbxservice',
        data: this.formdataExtensions,
      })
    },

    async loadService() {
      let response = await this.fetchPbxService(
        this.$route.params.pbx_service_id
      )

      this.formatDataTaxes(response.data.response)
      const term = response.data.response.pbx_service.term

      switch (term) {
        case 'daily':
          this.recurring_charge = this.$tc(
            'customers.options_recurring_charge.daily_recurring_charge'
          )
          this.additional_charge = this.$tc(
            'customers.options_additional_charge.daily_additional_charge'
          )
          break
        case 'weekly':
          this.recurring_charge = this.$tc(
            'customers.options_recurring_charge.weekly_recurring_charge'
          )
          this.additional_charge = this.$tc(
            'customers.options_additional_charge.weekly_additional_charge'
          )
          break
        case 'monthly':
          this.recurring_charge = this.$tc(
            'customers.options_recurring_charge.monthly_recurring_charge'
          )
          this.additional_charge = this.$tc(
            'customers.options_additional_charge.monthly_additional_charge'
          )
          break
        case 'bimonthly':
          this.recurring_charge = this.$tc(
            'customers.options_recurring_charge.bimonthly_recurring_charge'
          )
          this.additional_charge = this.$tc(
            'customers.options_additional_charge.bimonthly_additional_charge'
          )
          break
        case 'quarterly':
          this.recurring_charge = this.$tc(
            'customers.options_recurring_charge.quarterly_recurring_charge'
          )
          this.additional_charge = this.$tc(
            'customers.options_additional_charge.quarterly_additional_charge'
          )
          break
        case 'biannual':
          this.recurring_charge = this.$tc(
            'customers.options_recurring_charge.biannual_recurring_charge'
          )
          this.additional_charge = this.$tc(
            'customers.options_additional_charge.biannual_additional_charge'
          )
          break
        case 'yearly':
          this.recurring_charge = this.$tc(
            'customers.options_recurring_charge.yearly_recurring_charge'
          )
          this.additional_charge = this.$tc(
            'customers.options_additional_charge.yearly_additional_charge'
          )
          break
      }

      //Custom Destinations Groups (Inbound or Outbound)
      if (response.data.response.custom_destination_groups.length > 0) {
        response.data.response.custom_destination_groups.forEach((group) => {
          if (group.type == 'Inbound') {
            this.customDestinationsGroupsInbound.push(group.name)
          }
          if (group.type == 'Outbound') {
            this.customDestinationsGroupsOutbound.push(group.name)
          }
        })
      }

      this.package = response.data.response.pbx_service.pbx_package

      let commandosResponse = await this.fetchCommandos(
        this.$route.params.pbx_service_id
      )

      this.commandosDataCdr = {
        ...this.commandosDataCdr,
        ...commandosResponse.data.response.data.cdr,
      }
      this.commandosDataJobs = {
        ...this.commandosDataJobs,
        ...commandosResponse.data.response.data.jobs,
      }

      this.total_consume = this.selectedPbxService.total_consume
      this.paid_consume = this.selectedPbxService.paid_consume
      this.unpaid_consume = this.selectedPbxService.unpaid_consume

      this.total_deb = this.selectedPbxService.total_deb

      if (this.selectedPbxService.status != 'A') {
        this.editextension = false
      }

      const totalAdditionalCharges =
        this.selectedPbxService.TotalAddicionalCharges != undefined
          ? parseInt(this.selectedPbxService.TotalAddicionalCharges)
          : 0
      this.totalPbxService = response.data.response.pbx_service.total
    },

    async fetchInvoicesPerServicePbxData({ page, filter, sort }) {
      let data = {
        pbx_service_id: this.$route.params.pbx_service_id,
        order_by: sort.fieldName || 'invoice_number',
        order: sort.order || 'desc',
        page,
        limit: 10,
      }

      let response = await this.fetchInvoicesPerServicePbx(data)

      this.invoicesCount = response.data.totals.count

      return {
        data: response.data.invoices.data,
        pagination: {
          totalPages: response.data.invoices.data.last_page,
          currentPage: page,
          count: response.data.totals.count,
        },
      }
    },

    async fetchCustomAppRateData({ page, filter, sort }) {
      let data = {
        pbx_service_id: this.$route.params.pbx_service_id,
        order_by: sort.fieldName || 'app_name',
        order: sort.order || 'desc',
      }

      let response = await this.fetchCustomAppRate(data)

      this.totalCosto = response.data.totals.total_cost
      this.totalQuantity = response.data.totals.total_quant

      return {
        data: response.data.custom_app_rate,
      }
    },

    async fetchExtensionsData({ page, filter, sort }) {
      let data = {
        pbx_service_id: this.$route.params.pbx_service_id,
        order_by: sort.fieldName || 'created_at',
        order: sort.order || 'desc',
        page,
        limit: 10,
      }

      let response = await this.fetchExtensions(data)

      this.extensionCount =
        response.data.response.pbx_services_extensions.count_extensions
      this.extensionTotal =
        response.data.response.pbx_services_extensions.total_extensions

      return {
        data: response.data.response.service_extensions.data,
        pagination: {
          totalPages: response.data.response.service_extensions.last_page,
          currentPage: page,
          count: response.data.response.service_extensions.count,
        },
      }
    },

    async fetchDIDsData({ page, filter, sort }) {
      let data = {
        pbx_service_id: this.$route.params.pbx_service_id,
        order_by: sort.fieldName || 'created_at',
        order: sort.order || 'desc',
        page,
        limit: 10,
      }

      let response = await this.fetchDIDs(data)
      this.didCount = response.data.response.totals.did
      this.didTotal = response.data.response.totals.cost

      return {
        data: response.data.response.service_did.data,
        pagination: {
          totalPages: response.data.response.service_did.last_page,
          currentPage: page,
          count: response.data.response.service_did.count,
        },
      }
    },

    async fetchItemsData({ page, filter, sort }) {
      let data = {
        pbx_service_id: this.$route.params.pbx_service_id,
        order_by: sort.fieldName || 'created_at',
        order: sort.order || 'desc',
        page,
        limit: 10,
      }

      let response = await this.fetchItemsPbxService(data)

      this.itemTotal = response.data.response.totals.total_amount
      this.itemCount = response.data.response.totals.count

      return {
        data: response.data.response.service_items.data,
        pagination: {
          totalPages: response.data.response.service_items.last_page,
          currentPage: page,
          count: response.data.response.service_items.count,
        },
      }
    },

    async fetchAdditionalChargesData({ page, filter, sort }) {
      let data = {
        all: true,
        service_id: this.$route.params.pbx_service_id,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
        limit: 10,
      }
      // let response = await this.fetchAdditionalCharges(data)
      const response = await this.fetchAdditionalChargesService(data)
      this.chargesTotal = 0
      this.chargesCount = response.data.total

      // let charges = response.data.charges.data.map((charge) => {
      //   return {
      //     ...charge,
      //     type_from: charge.profile_did_id ? 'DID' : 'Extension',
      //   }
      // })

      for (const data of response.data.data) {
        this.chargesTotal = this.chargesTotal + parseFloat(data.total)
      }
      let charges = response.data.data
      return {
        data: charges,
        pagination: {
          totalPages: response.data.last_page,
          currentPage: page,
          count: response.data.total,
        },
      }
    },

    async fetchCallHistoryData({ page, filter, sort }) {
      let data = {
        pbx_service_id: this.$route.params.pbx_service_id,
        order_by: sort.fieldName || 'created_at',
        order: sort.order || 'desc',
        page,
        limit: 10,
        custom: 0,
      }

      let response = await this.fetchCallHistory(data)

      this.callHistoryCount = response.data.response.totals.billed_cdr
      this.callHistoryTotal =
        response.data.response.totals.current.exclusive_cost
      this.callHistoryTotalcustom = this.callHistoryTotal
      this.billed_cost = response.data.response.totals.total_cost
      this.inclusive_minutes_cosumed =
        response.data.response.totals.inclusive_minutes_consumed
      this.billed_cdr = response.data.response.totals.current.exclusive_cost
      this.billed_time = response.data.response.totals.current.exclusive_time

      this.AddmonthTotal = this.AddmonthTotal + this.callHistoryTotal
      return {
        data: response.data.response.service_cdrs.data,
        pagination: {
          totalPages: response.data.response.service_cdrs.last_page,
          currentPage: page,
          count: response.data.response.service_cdrs.count,
        },
      }
    },

    async fetchCustomDestinationData({ page, filter, sort }) {
      let data = {
        pbx_service_id: this.$route.params.pbx_service_id,
        order_by: sort.fieldName || 'created_at',
        order: sort.order || 'desc',
        page,
        limit: 10,
        only_custom: 1,
      }

      let response = await this.fetchCallHistory(data)
      let values = response.data.response

      this.customDestinationCount = values.totals.billed_cdr
      this.customDestinationTotal = values.totals.current.exclusive_cost
      this.callHistoryTotalcustom =
        this.callHistoryTotalcustom + this.customDestinationTotal
      this.ctmDestBilledCost = values.totals.total_cost
      this.ctmDestBilledIncMin = values.totals.inclusive_minutes_consumed
      this.ctmDestBilledCDR = values.totals.current.exclusive_cost
      this.ctmDestBilledTime = values.totals.current.exclusive_time
      this.AddmonthTotal = this.AddmonthTotal + this.customDestinationTotal

      return {
        data: values.service_cdrs.data,
        pagination: {
          totalPages: values.service_cdrs.last_page,
          currentPage: page,
          count: values.service_cdrs.count,
        },
      }
    },

    async fetchPrepaidChargesData({ page, filter, sort }) {
      let data = {
        pbx_service_id: this.$route.params.pbx_service_id,
        order_by: sort.fieldName || 'created_at',
        order: sort.order || 'desc',
        page,
        limit: 10,
      }
      let response = await this.fetchPrepaidCharges(data)

      if (response.data) {
        if (response.data.totals) {
          if (response.data.totals) {
            if (response.data.totals.current) {
              this.prepaidtotalcurrent = response.data.totals.current.amout
              this.prepaidtotalcurrenttax =
                response.data.totals.current.taxamount
            }
          }
        }
      }
      return {
        data: response.data.prepaid_charges.data,
        pagination: {
          totalPages: response.data.prepaid_charges.last_page,
          currentPage: page,
          count: response.data.prepaid_charges.count,
        },
      }
    },

    async removeInvoice(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let res = await this.deleteInvoice({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('invoices.deleted_message'))
            this.$router.go()
            this.$refs.table.refresh()
            return true
          }

          if (res.data.error === 'payment_attached') {
            window.toastr['error'](
              this.$t('invoices.payment_attached_message'),
              this.$t('general.action_failed')
            )
            return true
          }

          window.toastr['error'](res.data.error)
          return true
        }
        this.resetSelectedInvoices()
      })
    },

    async markInvoiceAsSent(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.invoice_mark_as_sent'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          const data = {
            id: id,
            status: 'SENT',
          }
          let response = await this.markAsSent(data)
          // console.log(response)
          this.refreshTable()
          if (response.data) {
            this.$router.go()
            window.toastr['success'](
              this.$tc('invoices.mark_as_sent_successfully')
            )
          }
        }
      })
    },

    async sendInvoice(invoice) {
      this.openModal({
        title: this.$t('invoices.send_invoice'),
        componentName: 'SendInvoiceModal',
        id: invoice.id,
        data: invoice,
        variant: 'lg',
      })
    },

    refreshTable() {
      this.$refs.table.refresh()
    },

    dateConvert(val) {
      return moment(val).format('YYYY-MM-DD HH:mm:ss')
    },

    openDateModal() {
      this.openModal({
        title: this.$t('pbx_services.download_calls'),
        componentName: 'DownloadCdrsModal',
        data: {},
      })
    },

    reloadCdrTable() {
      this.$refs.cdr_table.refresh()
    },

    openUpdateExtModal1(extension) {
      this.openModal({
        title: this.$t('pbx_services.edit_extension'),
        componentName: 'UpdateExtensionModal',
        id: extension.id,
        data: extension,
      })
    },
    openUpdatePriceExtModal(extension) {
      this.openModal({
        title: this.$t('pbx_services.modify_price_extension'),
        componentName: 'UpdatePriceExtensionModal',
        id: extension.id,
        data: extension,
      })
    },
    openUpdatePriceDIDModal(did) {
      this.openModal({
        title: this.$t('pbx_services.modify_price_did'),
        componentName: 'UpdatePriceDidModal',
        id: did.pbx_service_did_id,
        data: did,
      })
    },

    openUpdateExtModal(extension) {
      extension.from = 'viewPBX'

      // Validar si `selectedPbxService` existe y tiene contenido
      if (this.selectedPbxService && this.selectedPbxService.tenant) {
        // Validar si el objeto `tenant` tiene el campo `pbxservertenant_id`
        if (
          this.selectedPbxService.tenant.pbxservertenant_id &&
          this.selectedPbxService.tenant.pbxservertenant_id !== null
        ) {
          // Asignar el valor a `extension.pbxservertenant_id`
          extension.pbxservertenant_id =
            this.selectedPbxService.tenant.pbxservertenant_id
        } else {
          // Asignar null a `extension.pbxservertenant_id`
          extension.pbxservertenant_id = null
        }
      } else {
        // Si `selectedPbxService` no existe o no tiene un objeto `tenant`, asignar null
        extension.pbxservertenant_id = null
      }
      this.openModal({
        title: this.$t('pbx_services.edit_extension'),
        componentName: 'EditModalExtensions',
        id: extension.id,
        data: extension,
      })
    },

    openUpdateDIDModal(did) {
      did.from = 'viewPBX'

      // Validar si `selectedPbxService` existe y tiene contenido
      if (this.selectedPbxService && this.selectedPbxService.tenant) {
        // Validar si el objeto `tenant` tiene el campo `pbxservertenant_id`
        if (
          this.selectedPbxService.tenant.pbxservertenant_id &&
          this.selectedPbxService.tenant.pbxservertenant_id !== null
        ) {
          // Asignar el valor a `extension.pbxservertenant_id`
          did.pbxservertenant_id =
            this.selectedPbxService.tenant.pbxservertenant_id
        } else {
          // Asignar null a `extension.pbxservertenant_id`
          did.pbxservertenant_id = null
        }
      } else {
        // Si `selectedPbxService` no existe o no tiene un objeto `tenant`, asignar null
        did.pbxservertenant_id = null
      }
      this.openModal({
        title: this.$t('pbx_services.edit_did'),
        componentName: 'UpdateDidModal',
        id: did.id,
        data: did,
      })
    },

    reloadExtTable() {
      //console.log('entra qui en reloadExtTable ')
      this.$refs.ext_table.refresh()
    },

    closeModal() {},

    async jobInfoService() {
      let data = {
        pbx_service: this.$route.params.pbx_service_id,
      }
      let response = await this.jobInfoPBXService(data)
      if (response.data.success) {
        window.toastr['success']('Success Job Info')
      } else {
        window.toastr['error']('Error Job Info')
      }
    },

    async softDeleteService() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('pbx_services.soft_delete_jobs_message'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let data = {
            id: this.$route.params.pbx_service_id,
          }
          let response = await this.softDeletePBXService(data)
          if (response.data.success) {
            window.toastr['success'](response.data.message)
          } else {
            window.toastr['error'](response.data.message)
          }
        }
      })
    },

    async hardDeleteService() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('pbx_services.hard_delete_jobs_message'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let data = {
            id: this.$route.params.pbx_service_id,
          }
          let response = await this.hardDeletePBXService(data)
          if (response.data.success) {
            window.toastr['success'](response.data.message)
          } else {
            window.toastr['error'](response.data.message)
          }
        }
      })
    },

    formatDataTaxes(data) {
      let tempTaxesCdr = []
      let tempTaxesService = []

      if (data.pbx_service.tax_types_cdr.length !== 0) {
        tempTaxesCdr.push(...data.pbx_service.tax_types_cdr)
        let taxesCdr = tempTaxesCdr.map((obj) => ({
          ...obj,
          type_name: 'For Cdr',
          type: 'cdr',
        }))
        this.taxes.push(...taxesCdr)
      }

      if (data.pbx_service.pbx_service_tax_types.length !== 0) {
        tempTaxesService.push(...data.pbx_service.pbx_service_tax_types)
        let taxesService = tempTaxesService.map((obj) => ({
          ...obj,
          type_name: 'General',
          type: 'general',
        }))
        this.taxes.push(...taxesService)
      }

      if (data.pbx_service.get_items.length !== 0) {
        for (const item of data.pbx_service.get_items) {
          item.taxes = item.taxes_per_item
          if (item.taxes.length !== 0) {
            let tempTaxesItem = []
            tempTaxesItem.push(...item.taxes)
            let taxesItems = tempTaxesItem.map((obj) => ({
              ...obj,
              type_name: 'For Item',
              type: 'item',
            }))
            this.taxes.push(...taxesItems)
          }
        }
      }

      if (this.taxes.length !== 0) {
        this.totalTaxes = this.taxes.reduce(
          (acc, value) => (value.type !== 'cdr' ? acc + value.amount : acc),
          0
        )
      }
    },
  },
}
</script>

<style lang="scss">
.pbx-service-details {
  .table-foot {
    .table-total {
      min-width: 390px;
    }
  }

  @media (max-width: 480px) {
    .table-foot {
      .table-total {
        min-width: 384px;
      }
    }
  }
}

// Dropdown
.tab {
  overflow: hidden;
}

.tab-content-slide {
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

input:checked ~ .tab-content-slide {
  max-height: 400vh;
}
</style>
