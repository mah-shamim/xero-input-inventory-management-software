module.exports = [
    {
        path: '/inventory/suppliers',
        name: 'suppliersIndex',
        component: require('../views/inventory/supplier/index.vue').default
    },
    {
        path: '/inventory/customers',
        name: 'customersIndex',
        component: require('../views/inventory/customer/index.vue').default
    },
    {
        path: '/home/settings',
        name: 'setting.index',
        component: require('../views/settings.vue').default
    },
]