export const invoices = (state) => state.invoices
export const archived = (state) => state.archived
export const selectAllField = (state) => state.selectAllField
export const getTemplateId = (state) => state.invoiceTemplateId
export const selectedInvoices = (state) => state.selectedInvoices
export const totalInvoices = (state) => state.totalInvoices
export const totalArchived = (state) => state.totalArchived
export const selectedCustomer = (state) => state.selectedCustomer
export const selectedNote = (state) => state.selectedNote
export const selectedItem = (state) => state.selectedItem
export const getInvoice = (state) => (id) => {
  let invId = parseInt(id)
  return state.invoices.find((invoice) => invoice.id === invId)
}
