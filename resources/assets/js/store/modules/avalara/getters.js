export const avalaraConfigs = (state) => state.avalara.configs
export const avalaraConfigsGroup = (state) => state.group
export const avalaraLocationSaved = (state) => state.avalaraLocationSaved
export const avalaraLocationToSave = (state) => state.avalaraLocationToSave
export const selectAllField = (state) => state.selectAllField
export const getTemplateId = (state) => state.avalaraConfigTemplateId
export const selectedAvalaraConfigs = (state) => state.selectedAvalaraConfigs
export const totalAvalaraConfigs = (state) => state.totalAvalaraConfigs
export const selectedCustomer = (state) => state.selectedCustomer
export const selectedNote = (state) => state.selectedNote
export const selectedItem = (state) => state.selectedItem
export const avalaraConfigNameGroup = (state) => state.avalaraConfigGroupName

export const getAvalaraConfig = (state) => (id) => {
  let invId = parseInt(id)
  return state.avalara.configs.find((item) => item.id === invId)
}

export const avalaraItems = (state) => state.avalaraItems