export const itemGroups = (state) => state.itemGroups
export const selectAllField = (state) => state.selectAllField
export const selectedItemGroups = (state) => state.selectedItemGroups
export const totalItemGroups = (state) => state.totalItemGroups
export const getItemGroup = (state) => (id) => {
    let itGrp = parseInt(id)
    return state.itemGroups.find((ItemGroup) => ItemGroup.id === itGrp)
}
export const selectedViewItemGroup = (state) => state.selectedViewItemGroup
export const extensionStatusPayment = (state) => state.status_payment
export const totalExtensions = (state) => state.totalExtensions
export const extensions = (state) => state.extensions
export const selectedExtensions = (state) => state.selectedExtensions
