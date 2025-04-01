
module.exports = [
    {
        path: '/inventory/expenses',
        name: 'expense.index',
        component: require('../views/expense/index.vue').default
    },
    {
        path: '/inventory/expenses/:id',
        name: 'expense.show',
        component: require('../views/expense/show.vue').default
    },
];

