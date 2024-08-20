export const categoriesTollFree = (state) => state.categoriesTollFree

export const getCategoryById = (state) => (id) => {
  return state.categoriesTollFree.find(category => category.id === id)
}