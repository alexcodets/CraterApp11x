export const customers = (state) => state.customers
export const selectAllField = (state) => state.selectAllField
export const selectedCustomers = (state) => state.selectedCustomers
export const totalCustomers = (state) => state.totalCustomers
export const getCustomer = (state) => (id) => {
    let CstId = parseInt(id)
    return state.customers.find((customer) => customer.id === CstId)
}
export const selectedViewCustomer = (state) => state.selectedViewCustomer
export const packagesList = (state) => state.packagesList
export const packageParameters = (state) => state.packageParameters
export const invoicesList = (state) => state.invoicesList
export const estimatesList = (state) => state.estimatesList

/* Core pbx */
export const corePbxServicesParameters = (state) => state.corePbxServicesParameters
export const corePbxServicesIncludedData = (state) => state.corePbxServicesIncludedData
export const pbxServiceSaved = (state) => state.pbxServiceSaved
export const daysToRenewal = (state) => state.daysToRenewal
export const pbxDIDSaved = (state) => state.pbxDIDSaved
export const pbxEXTSaved = (state) => state.pbxEXTSaved
/* tenant */
export const pbxTenantSaved = (state) => state.pbxTenantSaved

/* did */
export const did = (state) => state.did
export const didInclude = (state) => state.didInclude
export const selectAllFieldDID = (state) => state.selectAllFieldDID
/* export const selectAllFieldDIDInclude = (state) => state.selectAllFieldDIDInclude */
export const selectAllFieldDIDInclude = (state) => state.selectAllFieldDIDToInclude
export const selectedPbxDID = (state) => state.selectedPbxDID
export const selectedPbxDIDToInclude = (state) => state.selectedPbxDIDToInclude
export const selectedDID = (state) => state.selectedDID
export const totalDIDInclude = (state) => state.totalDIDInclude
/* extension */
export const extensions = (state) => state.extensions
export const extensionsInclude = (state) => state.extensionsInclude
export const selectAllFieldExtensions = (state) => state.selectAllFieldExtensions
/* export const selectAllFieldExtInclude = (state) => state.selectAllFieldExtInclude */
export const selectAllFieldExtInclude = (state) => state.selectAllFieldExtensionsToInclude
export const selectedPbxExtensions = (state) => state.selectedPbxExtensions
export const selectedPbxExtensionsToInclude = (state) => state.selectedPbxExtensionsToInclude
export const totalExtension = (state) => state.totalExtension
export const totalExtensionInclude = (state) => state.totalExtensionInclude