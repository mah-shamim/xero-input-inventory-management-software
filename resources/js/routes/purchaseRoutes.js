module.exports = [
    {
        path:'/inventory/purchases',
        name:'purchase.index',
        component: require('../views/inventory/product/purchase/index.vue').default
    },
    {
        path:'/inventory/purchases/create',
        name: 'purchase.create',
        component: require('../views/inventory/product/purchase/create.vue').default
    },
    {
        path: '/inventory/purchases/:id',
        name: 'purchase.show',
        component: require('../views/inventory/product/purchase/show.vue').default
    },
    {
        path:'/inventory/purchases/:id/edit',
        name:'purchase.edit',
        component: require('../views/inventory/product/purchase/create.vue').default
    }
];
