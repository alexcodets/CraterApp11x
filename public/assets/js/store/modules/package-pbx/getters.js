export const packages = (state) => state.packages
export const packagesGroup = (state) => state.group
export const selectAllField = (state) => state.selectAllField
export const getTemplateId = (state) => state.packageTemplateId
export const selectedPackages = (state) => state.selectedPackages
export const totalPackages = (state) => state.totalPackages
export const selectedCustomer = (state) => state.selectedCustomer
export const selectedNote = (state) => state.selectedNote
export const selectedItem = (state) => state.selectedItem
export const packageNameGroup = (state) => state.packageGroupName
export const getPackage = (state) => (id) => {
  let invId = parseInt(id)
  return state.packages.find((item) => item.id === invId)
}
