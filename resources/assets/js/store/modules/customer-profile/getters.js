export const loggedInCustomer = (state) => state.loggedInCustomer
export const balance = state => (state.loggedInCustomer ? state.loggedInCustomer.balance : 0);
export const servicesList = (state) => state.servicesList
