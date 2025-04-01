import Vue from 'vue'
import Vuex from 'vuex'
import settings from './modules/settings'
import {
    warehousePickingType,
    warehouseStorageType,
    warehouseType,
    accountingMethod,
    paymentMethods
} from './enums'

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        settings
    },
    state: {
        warehouseType: warehouseType,
        warehousePickingType: warehousePickingType,
        warehouseStorageType: warehouseStorageType,
        accountingMethod: accountingMethod,
        paymentMethods: paymentMethods,
        permissions: [],
        timePermissions: [],
        appColor: {
            topbar: 'purple',
            sidebar: 'grey lighten-2'
        },
        itemsPerPage: 10,
        itemsPerPageOptions: [5, 10, 15, 20, 25, 30, 40, 50, 60, 70, 80, 90, 100],
        delivery_statuses: [
            {
                id: 1,
                name: 'Received'
            },
            {
                id: 2,
                name: 'Pending'
            },
        ],
        payroll_contact_types: [
            'permanent',
            'temporary',
            'contractual',
            'others'
        ],
        bank_types: [{
            value: null,
            text: 'generic'
        },
            {
                value: 'cr',
                text: 'cash register'
            },
        ],
        // settings: {
        //     measurement: false
        // },
        quantity_label: 'Quantity',
        hasTable: false,
        requestedBarcode: '',
    },
    mutations: {
        addPermissions(state, data) {
            state.permissions = data
        },
        addTimePermissions(state, data) {
            state.timePermissions = data
        },
        quantity_label(state, value) {
            state.quantity_label = value
        },
        hasTable(state, value) {
            state.hasTable = value
        },
        barcodeValue(state, value) {
            state.requestedBarcode = value
        },
        measurement(state, value) {
            state.settings.measurement = true
        },
        itemsPerPage(state, value) {
            state.itemsPerPage = Number(value)
        }
    },
})