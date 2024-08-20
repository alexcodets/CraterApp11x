import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
    contacts: 0,
    invoices: 0,
    invoicespaid: 0,
    invoicesunpaid: 0,
    invoicesppaid: 0,
    invoicesCountDeleted: 0,
    invoicesCountSend: 0,
    invoicesCountView: 0,
    invoicesCountOverdue: 0,
    invoicesCountCompleted: 0,
    invoicesCountDraft: 0,
    estimatesCount: 0,
    estimatesCountDraft: 0,
    estimatesCountSent: 0,
    estimatesCountViewed: 0,
    estimatesCountExpired: 0,
    estimatesCountAccepted: 0,
    estimatesCountRejected: 0,
    estimates: 0,
    estimatesCount: 0,
    estimatesCountDraft: 0,
    estimatesCountSent: 0,
    estimatesCountViewed: 0,
    estimatesCountExpired: 0,
    estimatesCountAccepted: 0,
    estimatesCountRejected: 0,
    expenses: 0,
    totalDueAmount: [],
    isDashboardDataLoaded: false,

    weeklyInvoices: {
        days: [],
        counter: [],
    },

    chartData: {
        months: [],
        invoiceTotals: [],
        expenseTotals: [],
        netProfits: [],
        receiptTotals: [],
    },

    salesTotal: null,
    totalReceipts: null,
    totalExpenses: null,
    netProfit: null,
    totalCredit: null,
    balancenocobradototal: null,

    dueInvoices: [],
    recentEstimates: [],
    newContacts: [],
}

export default {
    namespaced: true,

    state: initialState,

    getters: getters,

    actions: actions,

    mutations: mutations,
}