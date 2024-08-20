export const providers = (state) => state.providers
export const selectAllField = (state) => state.selectAllField
export const selectedProviders = (state) => state.selectedProviders
export const totalProviders = (state) => state.totalProviders
export const getProvider = (state) => (id) => {
  let CstId = parseInt(id)
  return state.providers.find((provider) => provider.id === CstId)
}
export const selectedViewProvider = (state) => state.selectedViewProvider