module.exports = [
    {
        path: '/inventory/units',
        name:'unitsIndex',
        component: require('../views/inventory/unit/index.vue').default
    },
    {
        path:'/inventory/unitconversions',
        name:'unitsMapping',
        component: require('../views/inventory/unit/mapping.vue').default
    },
    {
        path: '/inventory/productunit',
        name: 'productUnitCreate',
        component: require('../views/inventory/unit/productunit.vue').default
    }
];