export const departaments = (state) => state.departaments
export const selectAllField = (state) => state.selectAllField
export const selectedDepartaments = (state) => state.selectedDepartaments
export const totalDepartaments = (state) => state.totaldepartaments
export const getDepartament = (state) => (id) => {
  let CstId = parseInt(id)
  return state.departaments.find((departament) => departament.id === CstId)
}
export const selectedViewDepartament = (state) => state.selectedViewDepartament