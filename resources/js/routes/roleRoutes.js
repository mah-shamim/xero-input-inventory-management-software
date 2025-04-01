module.exports = [
    {
        path: '/inventory/roles/create',
        name: 'role.create',
        component: require('../views/inventory/role/createRole').default
    },
    {
        path: '/inventory/roles',
        name: 'role.index',
        component: require('../views/inventory/role/indexRole').default
    }
]