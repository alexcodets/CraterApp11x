export const authorizations = (state) => state.authorizations
export const selectAllField = (state) => state.selectAllField
export const selectedAuthorizations = (state) => state.selectedAuthorizations
export const totalAuthorizations = (state) => state.totalAuthorizations
export const getAuthorization = (state) => (id) => {
  let CstId = parseInt(id)
  return state.authorizations.find((authorization) => authorization.id === CstId)
}
export const selectedViewAuthorization = (state) => state.selectedViewAuthorization