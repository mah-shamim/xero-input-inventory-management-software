module.exports = [
    {
        path: '/inventory/orders/create',
        name: 'ordersCreate',
        component: require('../views/inventory/order/create.vue').default
    },
    {
        path: '/inventory/orders/list',
        name: 'ordersList',
        component: require('../views/inventory/order/index.vue').default
    },
    {
        path: '/inventory/orders/:id/edit',
        name: 'ordersEdit',
        component: require('../views/inventory/order/create.vue').default
    },
    {
        path: '/inventory/orders/:id',
        name: 'ordersShow',
        component: require('../views/inventory/order/show.vue').default
    },

    {
        path: '/inventory/orders/purchase/create',
        name: 'ordersPurchaseCreate',
        component: require('../views/inventory/order/purchase/create.vue').default
    },
    {
        path: '/inventory/orders/purchase/list',
        name: 'ordersPurchaseList',
        component: require('../views/inventory/order/purchase/index.vue').default
    },
    {
        path: '/inventory/orders/purchase/:id/edit',
        name: 'ordersPurchaseEdit',
        component: require('../views/inventory/order/purchase/create.vue').default
    },
    {
        path: '/inventory/orders/purchase/:id',
        name: 'ordersPurchaseShow',
        component: require('../views/inventory/order/purchase/show.vue').default
    },
];
