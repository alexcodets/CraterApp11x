export const groups = (state) => state.groups
export const selectAllField = (state) => state.selectAllField
export const selectedGroups = (state) => state.selectedGroups
export const totalGroups = (state) => state.totalGroups
export const getGroup = (state) => (id) => {
  let CstId = parseInt(id)
  return state.groups.find((group) => group.id === CstId)
}
export const selectedViewGroup = (state) => state.selectedViewGroup

export const groupPackages = (state) => state.package