import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
    /*COSTUMER*/
    customers: [],
    totalCustomers: 0,
    selectAllField: false,
    selectedCustomers: [],
    selectedViewCustomer: {},
    /*PBX SERVICES*/
    packageParameters: {},
    corePbxServicesParameters: {},
    corePbxServicesIncludedData: {},
    daysToRenewal: {},
    pbxServiceSaved: {},
    pbxDIDSaved: {},
    pbxEXTSaved: {},
    /* PBX TENANT */
    pbxTenantSaved: {},
    /**DID**/
    did: [],
    totalDID: 0,
    selectedDID: [],
    didInclude:[],
    selectedPbxDID: [],
    selectedPbxDIDToInclude: [],
    selectAllFieldDID: false,
    selectAllFieldDIDToInclude: false,
    /**EXTENSION**/
    extensions: [],
    totalExtensions: 0,
    totalExtensionsInclude:0,
    extensionsInclude:[],
    selectedPbxExtensions: [],
    selectedPbxExtensionsToInclude: [],
    selectAllFieldExtensions: false,
    selectAllFieldExtensionsToInclude: false,

}

export default {
    namespaced: true,

    state: initialState,

    getters: getters,

    actions: actions,

    mutations: mutations,
}