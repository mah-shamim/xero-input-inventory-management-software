module.exports = [
    {
        path: '/income/create',
        name: 'income.create',
        component: require('../views/income/create.vue').default
    },
    {
        path: '/income/index',
        name: 'income.index',
        component: require('../views/income/index.vue').default
    },
    {
        path: '/income/incomes/:id/edit',
        name: 'income.edit',
        component: require('../views/income/create.vue').default
    },
    {
        path: '/income/incomes/:id',
        name: 'income.show',
        component: require('../views/income/show.vue').default
    },
];

