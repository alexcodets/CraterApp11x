export const taxGroups = (state) => state.taxGroups
export const selectAllField = (state) => state.selectAllField
export const selectedTaxGroups = (state) => state.selectedTaxGroups
export const totalTaxGroups = (state) => state.totalTaxGroups
export const getTaxGroup = (state) => (id) => {
  let CstId = parseInt(id)
  return state.tax_groups.find((group) => tax_group.id === CstId)
}
export const selectedViewTaxGroup = (state) => state.selectedViewTaxGroup

export const groupTaxes = (state) => state.taxes