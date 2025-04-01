const inventoryPrefix = '/report/inventory/'
const payrollPrefix = '/report/payroll/'
const expensePrefix = '/report/expense/'
module.exports = [
    {
        path: '/report/home',
        name:'homeIndex',
        component: require('../views/home').default
    },
    {
        path: inventoryPrefix + 'purchase',
        name: 'purchaseReportIndex',
        component: require('../views/inventory/report/purchaseReport.vue').default
    },
    {
        path: inventoryPrefix + 'supplier/purchase/:id',
        name: 'purchasesIndexBySupplierId',
        component: require('../views/inventory/report/purchaseReport.vue').default
    },
    {
        path: inventoryPrefix + 'sale',
        name: 'saleReportIndex',
        component: require('../views/inventory/report/saleReport.vue').default
    },
    {
        path: inventoryPrefix + 'customer/sale/:id',
        name: 'salesIndexByCustomerId',
        component: require('../views/inventory/report/saleReport.vue').default
    },
    {
        path: inventoryPrefix + 'customer',
        name: 'customerReportIndex',
        component: require('../views/inventory/report/customerReport.vue').default
    },
    {
        path: inventoryPrefix + 'supplier',
        name: 'supplierReportIndex',
        component: require('../views/inventory/report/supplierReport.vue').default
    },
    {
        path: inventoryPrefix + 'actualpurchase',
        name: 'actualPurchaseReportIndex',
        component: require('../views/inventory/report/actualPurchaseReport.vue').default
    },
    {
        path: inventoryPrefix + 'product',
        name: 'productReportIndex',
        component: require('../views/inventory/report/productReport.vue').default
    },
    {
        path: inventoryPrefix + 'warehouse',
        name: 'warehouseReportIndex',
        component: require('../views/inventory/report/warehouseReport.vue').default
    },
    {
        path: expensePrefix + 'index',
        name: 'report.expense.index',
        component: require('../views/expense/report/expenseReport.vue').default
    },
    {
        path: '/report/overall',
        name: 'report.overall',
        component: require('../views/inventory/report/overallReport.vue').default
    }
];
