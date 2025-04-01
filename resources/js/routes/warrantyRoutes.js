module.exports = [

    {
        path: '/inventory/warranty/index',
        name: 'warrantyIndex',
        component: require('../views/inventory/warranty/index.vue').default
    },
    {
        path:'/inventory/warranty/create',
        name:'warrantyCreate',
        component: require('../views/inventory/warranty/create.vue').default
    },
    {
        path: '/inventory/warranty/:id/edit',
        name: 'warrantyEdit',
        component: require('../views/inventory/warranty/create.vue').default
    },

];