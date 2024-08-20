export const stripes = (state) => state.stripes
export const selectAllField = (state) => state.selectAllField
export const selectedStripes = (state) => state.selectedStripes
export const totalStripes = (state) => state.totalStripes
export const getStripes = (state) => (id) => {
  let CstId = parseInt(id)
  return state.stripes.find((stripe) => stripe.id === CstId)
}
export const selectedViewStripe = (state) => state.selectedViewStripe