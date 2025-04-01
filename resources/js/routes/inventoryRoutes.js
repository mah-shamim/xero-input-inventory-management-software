module.exports = [
    {
        path: '/inventory/warehouses',
        name: 'warehousesIndex',
        component: require('../views/inventory/warehouse/index.vue').default
    },
    {
        path:'/inventory/products',
        name:'productsIndex',
        component: require('../views/inventory/product/index.vue').default
    },
    {
        path:'/inventory/products/:id/edit',
        name:'productsEdit',
        component: require('../views/inventory/product/edit.vue').default
    },
    {
        path:'/inventory/products/:id',
        name:'productsShow',
        component: require('../views/inventory/product/show.vue').default
    },
    {
        path:'/inventory/products/import',
        name:'productsImport',
        component: require('../views/inventory/product/import.vue').default
    },
    {
        path: '/inventory/brands',
        name: 'brandsIndex',
        component: require('../views/inventory/brand/index.vue').default
    },
    {
        path: '/inventory/categories',
        name: 'categoriesIndex',
        component: require('../views/inventory/category/index.vue').default
    },
    {
        path: '/inventory/settings',
        name: 'settings.index',
        component: require('../views/settings.vue').default
    },
    {
        path: '/inventory/productdamages',
        name: 'productdamagesIndex',
        component: require('../views/inventory/productdamage/index.vue').default
    },
    {
        path: '/inventory/productdamages/create',
        name: 'productdamagesCreate',
        component: require('../views/inventory/productdamage/create.vue').default
    },
    {
        path: '/inventory/productdamages/:id/edit',
        name: 'productdamagesEdit',
        component: require('../views/inventory/productdamage/create.vue').default
    }

];

