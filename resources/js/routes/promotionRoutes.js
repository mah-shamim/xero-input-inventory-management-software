module.exports = [
    {
        path: '/inventory/product-bonuses/create',
        name: 'productBonusCreate',
        component: require('../views/inventory/promotion/product-bonus/create.vue').default
    },
    {
        path: '/inventory/product-bonuses/list',
        name: 'productBonusList',
        component: require('../views/inventory/promotion/product-bonus/index.vue').default
    },
    {
        path: '/inventory/product-tires-prices/create',
        name: 'tierPriceCreate',
        component: require('../views/inventory/promotion/tier-price/create.vue').default
    },
    {
        path: '/inventory/product-tires-prices/list',
        name: 'tierPriceList',
        component: require('../views/inventory/promotion/tier-price/index.vue').default
    }

]