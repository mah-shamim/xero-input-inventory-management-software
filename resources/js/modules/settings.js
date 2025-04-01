import {Errors} from "../ERPFramework/Errors";

export default {
    namespaced: true,
    state: {
        company_detail: {
            address1: '',
            address2: '',
            code: '',
            name: '',
            web_url: '',
            contact_name: '',
            contact_phone: [
                '123123123'
            ],
        },
        settings: {
            currency: 'USD',
            site_name: "testing",
            default_date_format:'YYYY-MM-DD',
            timezone:'est',
            default_email: "",
            design: {
                topbar_color: null,
                sidebar_color: null
            },
            inventory: {
                account_method: "avg",
                profit_percent: false,
                quantity_label: "Quantity",
                shipping_cost_label: "Shipping Cost",
                purchase: {
                    default_payment_mood: 1
                },
                sale: {
                    default_payment_mood: 1,
                    stock_out_sale: true,
                },
            }
        }
    },
    getters: {
        getDefaultDateFormat: state=>state.settings.default_date_format,
        getCompanyDetail: state=>state.company_detail,
        getDefaultTimeZone: state=>state.settings.timezone,
        getTopBarColor: state => state.settings.design.topbar_color,
        getSideBarColor: state => state.settings.design.sidebar_color,
        getShippingLabel: state => state.settings.inventory.shipping_cost_label,
        getQuantityLabel: state => state.settings.inventory.quantity_label,
        getAccountingMethod: state => state.settings.inventory.account_method,
        isProfitPercent: state => state.settings.inventory.profit_percent,
        getPurchaseDefaultPaymentMethod: state => state.settings.inventory.purchase.default_payment_mood,
        getSaleDefaultPaymentMethod: state => state.settings.inventory.sale.default_payment_mood,
        isStockOutSaleAllowed: state => state.settings.inventory.sale.stock_out_sale,
        getCurrency: state => state.settings.currency
    },
    mutations: {
        addNumber(state) {
            if (state.company_detail.contact_phone.length < 4)
                state.company_detail.contact_phone.push('')
            else
                swal.fire({
                    type: 'warning',
                    text: 'You can not add more than 4',
                    timer: 4000
                })
        },
        changeCompanyDetail(state, payload) {
            state.company_detail = payload
        },
        changeSettings(state, payload) {
            state.settings = payload
        },
        deleteNumber(state, index) {
            if (state.company_detail.contact_phone.length > 1)
                state.company_detail.contact_phone.splice(index, 1)
            else
                swal.fire({
                    type: 'warning',
                    text: 'You must keep at least one phone number',
                    timer: 4000
                })
        }
    },

    actions: {
        getSettings({commit}) {
            axios.get('/api/inventory/settings')
                .then(res => {
                    commit('changeSettings', res.data.settings.settings)
                    commit('changeCompanyDetail', res.data.company_detail)
                })
        },
        postSettings({state, commit}, payload) {
            return new Promise((resolve, reject) => {
                axios.post('/api/inventory/settings', state.settings)
                    .then(res => {
                        if (res.status === 200) {
                            commit('changeSettings', res.data.settings.settings)
                            swal.fire({
                                type: 'success',
                                text: 'Settings has been successfully updated',
                                timer: 3000
                            }).then(r => {
                                if (r) window.location.reload()
                            })
                        }
                    })
                    .catch(error => {
                        /* important note: error has been returned
                         because of vee-validation is not working
                        in vuex store */
                        new Errors(error.response.data)
                        reject(error.response.data)
                    })
            });
        },

        postCompanyDetail({state, commit}, payload) {
            return new Promise((resolve, reject) => {
                axios.post('/api/inventory/settings',
                    state.company_detail,
                    {
                        params: {
                            company_detail: 1
                        }
                    })
                    .then(res => {
                        if (res.status === 200) {
                            commit('changeCompanyDetail', res.data.settings.company_detail)
                            swal.fire({
                                type: 'success',
                                text: 'Company Detail has been successfully updated',
                                timer: 3000
                            }).then(r => {
                                if (r) window.location.reload()
                            })
                        }
                    })
                    .catch(error => {
                        /* important note: error has been returned
                         because of vee-validation is not working
                        in vuex store */
                        new Errors(error.response.data)
                        reject(error.response.data)
                    })
            });
        },
    }
}