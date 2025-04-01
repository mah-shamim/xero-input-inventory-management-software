module.exports = [
    {
        path: '/payroll/employee',
        name: 'payroll.employee',
        component: require('../views/payroll/employee/index.vue').default
    },
    {
        path: '/payroll/salary',
        name: 'payroll.salary',
        component: require('../views/payroll/salary/index.vue').default
    },
    {
        path: '/payroll/department',
        name: 'payroll.department',
        component: require('../views/payroll/department/index.vue').default
    }
];

