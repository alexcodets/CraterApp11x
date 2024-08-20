export const getContacts = (state) => state.contacts
export const getInvoices = (state) => state.invoices
export const getInvoicespaid = (state) => state.invoicespaid
export const getinvoicesunpaid = (state) => state.invoicesunpaid
export const getinvoicesCountppaid = (state) => state.invoicesppaid
export const getinvoicesCountDeleted = (state) => state.invoicesCountDeleted
export const getinvoicesCountSend = (state) => state.invoicesCountSend
export const getinvoicesCountView = (state) => state.invoicesCountView
export const getinvoicesCountOverdue = (state) => state.invoicesCountOverdue
export const getinvoicesCountCompleted = (state) => state.invoicesCountCompleted
export const getinvoicesCountDraft = (state) => state.invoicesCountDraft

// estimates
export const getEstimates = (state) => state.estimates
export const getEstimatesCount = (state) => state.estimatesCount
export const getEstimatesDraft = (state) => state.estimatesCountDraft
export const getEstimatesSent = (state) => state.estimatesCountSent
export const getEstimatesViewed = (state) => state.estimatesCountViewed
export const getEstimatesExpired = (state) => state.estimatesCountExpired
export const getEstimatesAccepted = (state) => state.estimatesCountAccepted
export const getEstimatesRejected = (state) => state.estimatesCountRejected
    // estimates

export const getExpenses = (state) => state.expenses
export const getRecentInvoices = (state) => state.recentInvoices
export const getNewContacts = (state) => state.newContacts
export const getTotalDueAmount = (state) => state.totalDueAmount

export const getDueInvoices = (state) => state.dueInvoices
export const getRecentEstimates = (state) => state.recentEstimates

export const getDashboardDataLoaded = (state) => state.isDashboardDataLoaded

export const getWeeklyInvoicesCounter = (state) => state.weeklyInvoices.counter
export const getWeeklyInvoicesDays = (state) => state.weeklyInvoices.days

export const getChartMonths = (state) => state.chartData.months
export const getChartInvoices = (state) => state.chartData.invoiceTotals
export const getChartExpenses = (state) => state.chartData.expenseTotals
export const getNetProfits = (state) => state.chartData.netProfits
export const getReceiptTotals = (state) => state.chartData.receiptTotals

export const getTotalSales = (state) => state.salesTotal
export const getTotalReceipts = (state) => state.totalReceipts
export const getTotalExpenses = (state) => state.totalExpenses
export const getNetProfit = (state) => state.netProfit
export const gettotalCredit = (state) => state.totalCredit
export const getbalancenocobradototal = (state) => state.balancenocobradototal