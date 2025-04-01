module.exports = [
    {
        path: '/inventory/sale/:id/return',
        name: 'salesReturn',
        component: require('../views/inventory/product/sales/salesReturn.vue').default
    },
    {
        path: '/inventory/sales',
        name: 'salesIndex',
        component: require('../views/inventory/product/sales/index.vue').default
    },

];
