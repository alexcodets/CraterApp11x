export const roles = (state) => state.roles;
export const getRoles = (state) => (id) => {
  let invId = parseInt(id)
  return state.roles.find((item) => item.id === invId)
}
