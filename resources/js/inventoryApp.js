/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import mixins from "./mixins";

require('./bootstrap')
window.Vue = require('vue')

window.moment = require('moment')
window.moment.tz = require('moment-timezone')
moment().tz("Asia/Dhaka").format()
// moment.suppressDeprecationWarnings = true;
Vue.prototype._ = _;
//
import Vuetify from 'vuetify'
import VueRouter from 'vue-router'
import VeeValidate from 'vee-validate'
import './plugins/vueHtmlToPaper'
import {default as staticRoute} from './routes/staticRoutes'
import {default as inventoryRoute} from './routes/inventoryRoutes'
import {default as expenseRoute} from './routes/expenseRoutes'
import {default as reportRoute} from './routes/reportRoutes'
import {default as purchaseRoute} from './routes/purchaseRoutes'
import {default as saleRoute} from './routes/saleRoutes'
import {default as unitRoute} from './routes/unitRoutes'
import {default as userRoute} from './routes/userRoutes'
import {default as orderRoute} from './routes/orderRoutes'
import {default as promotionRoute} from './routes/promotionRoutes'
import {default as vendorRoute} from './routes/vendorRoutes'
import {default as utilityRoute} from './routes/utilityRoutes'
import {default as roleRoutes} from './routes/roleRoutes'
import {default as warrantyRoute} from './routes/warrantyRoutes'

Vue.use(Vuetify,{ iconfont: 'mdi' })
Vue.use(VeeValidate)
import {Forms} from "./ERPFramework/Forms"

import {Erp} from "./ERPFramework/Erp"
import VueTheMask from 'vue-the-mask'
Vue.use(VueTheMask)
//
import store from './store.js'
//
window.Erp = new Erp()
//
Vue.use(VueRouter)
let rout = []
//
require('./filter.js')
Vue.mixin(mixins)
const routes = rout.concat(
    staticRoute,
    inventoryRoute,
    purchaseRoute,
    saleRoute,
    expenseRoute,
    // reportRoute,
    unitRoute,
    // orderRoute,
    promotionRoute,
    userRoute,
    vendorRoute,
    utilityRoute,
    warrantyRoute,
    roleRoutes
)
//
//
const router = new VueRouter({
    routes,
    linkActiveClass: 'is-active',
    mode: 'history',
    scrollBehavior (to, from, savedPosition) {
        return { x: 0, y: 0 }
    }
})
//
// Vue.filter('truncate',function (val,arg)  {
//     if(val){
//         return val.length>arg?val.slice(0,arg)+'...':val.slice(0,arg)
//
//     }
// })
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
Vue.component('payments', require('./components/global/payments.vue').default)
Vue.component('payment-bill-receipt', require('./components/global/payment-bill-receipt.vue').default)


//dropdown components
Vue.component('search-customer-dropdown', require('./components/search-customer-dropdown/search-customer-dropdown.vue').default)
Vue.component('search-supplier-dropdown', require('./components/search-supplier-dropdown/search-supplier-dropdown.vue').default)
Vue.component('search-product-dropdown', require('./components/search-product-dropdown/search-product-dropdown.vue').default)
Vue.component('warehouse-list-dropdown', require('./components/warehouse-list-dropdown/warehouse-list-dropdown.vue').default)

Vue.component('warehouse-dropdown-query', require('./components/dropdowns/warehouseDropdownQuery.vue').default)
Vue.component('category-dropdown-query', require('./components/dropdowns/categoryDropdownQuery.vue').default)

Vue.component('add-warranty', require('./components/add-warranty/add-warranty.vue').default)
Vue.component('add-product-damage', require('./components/add-product-damage/add-product-damage.vue').default)



Vue.component('pagination', require('laravel-vue-pagination'))
Vue.component('category-lists', require('./components/global/inventory/category.vue').default)

Vue.component('payment-crud', require('./components/global/payment-component-crud.vue').default)

Vue.component('conversion-component', require('./components/global/inventory/conversionComponent.vue').default)
Vue.component('print-component', require('./components/global/print/print.vue').default)
Vue.component('pos-print', require('./components/global/print/pos-print.vue').default)
Vue.component('barcode-print', require('./components/global/print/barcodePrint.vue').default)
Vue.component('barcode-print-for-pos', require('./components/global/print/barcodePrintPos.vue').default)
Vue.component('list-print-bootstrap', require('./components/global/print/list-print-bootstrap.vue').default)
Vue.component('has-feature-access', require('./components/global/inventory/hasFeatuerAccess.vue').default)
Vue.component('combo-chart', require('./components/charts/ComboChartComponent').default)
Vue.component('single-chart', require('./components/charts/sinleChartComponent').default)
Vue.component('action-btn', require('./components/global/inventory/action-btn.vue').default)
Vue.component('collapse-btn', require('./components/global/inventory/collapse-btn.vue').default)
Vue.component('simple-tooltip', require('./components/global/simple-tooltip.vue').default)
Vue.component('tooltip-button', require('./components/global/tooltip-button.vue').default)
Vue.component('vuetify-datetime', require('./components/global/vuetify-date-time.vue').default)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import appIcon from './ERPFramework/icons'
import {mapGetters} from "vuex";
new Vue({
    el: '#app',
    router,
    store,
    vuetify: new Vuetify(),

    data: () => ({
        icons:appIcon,
        loading: false,
        color: "#70a046",
        size: "10px",
        forms: new Forms({}),
        erp: new Erp(),
        access: false,
        settings:{},
        companySettings:{},
        dialog: false,
        drawer: null,
        appDense:appDense,
        items: [
            {
                action: 'local_activity',
                title: 'Attractions',
                items: [
                    { title: 'List Item' },
                ],
            },
            {
                action: 'restaurant',
                title: 'Dining',
                active: true,
                items: [
                    { title: 'Breakfast & brunch' },
                    { title: 'New American' },
                    { title: 'Sushi' },
                ],
            },
            {
                action: 'school',
                title: 'Education',
                items: [
                    { title: 'List Item' },
                ],
            },
            {
                action: 'directions_run',
                title: 'Family',
                items: [
                    { title: 'List Item' },
                ],
            },
            {
                action: 'healing',
                title: 'Health',
                items: [
                    { title: 'List Item' },
                ],
            },
            {
                action: 'content_cut',
                title: 'Office',
                items: [
                    { title: 'List Item' },
                ],
            },
            {
                action: 'local_offer',
                title: 'Promotions',
                items: [
                    { title: 'List Item' },
                ],
            },
        ],
    }),
    filters: {
        capitalize(str) {
            return _.upperFirst(str);
        },
        replace(str, input, output) {
            return str.replace(input, output);
        }
    },
    created() {
        if(!(window.location.pathname==='/login' || window.location.pathname==='/register')){
            store.dispatch('settings/getSettings')
        }

    },
    computed:{
        ...mapGetters({company:'settings/getCompanyDetail'}),
        ...mapGetters({topBarColor:'settings/getTopBarColor'}),
        ...mapGetters({defaultDateFormat:'settings/getDefaultDateFormat'}),
        ...mapGetters({sideBarColor:'settings/getSideBarColor'}),
        ...mapGetters({shippingLabel:'settings/getShippingLabel'}),
        ...mapGetters({quantityLabel:'settings/getQuantityLabel'}),
        ...mapGetters({accountingMethod:'settings/getAccountingMethod'}),
        ...mapGetters({isProfitPercent:'settings/isProfitPercent'}),
        ...mapGetters({purchaseDefaultPaymentMethod:'settings/getPurchaseDefaultPaymentMethod'}),
        ...mapGetters({saleDefaultPaymentMethod:'settings/getSaleDefaultPaymentMethod'}),
        ...mapGetters({isStockOutSaleAllowed:'settings/isStockOutSaleAllowed'}),
        ...mapGetters({currency:'settings/getCurrency'})
    },

    methods: {
        getMethod_and_id() {
            let id = this.$route.params;
            let method = this.$route.name;
            if (_.includes(method, 'Edit')) {
                method = 'Edit'
            }
            if (_.includes(method, 'Create')) {
                method = 'Create'
            }
            if (_.includes(method, 'Delete')) {
                method = 'Delete'
            }
            if (_.includes(method, 'Show')) {
                method = 'Show'
            }
            if (_.includes(method, 'Update')) {
                method = 'Update'
            }

            return [id, method];
        },
        getMethodAction() {
            let method = this.$route.name;
            if (_.includes(method, 'Edit') || _.includes(method, 'edit')) {
                method = 'Edit'
            }
            if (_.includes(method, 'Create') || _.includes(method, 'create')) {
                method = 'Create'
            }
            if (_.includes(method, 'Delete') || _.includes(method, 'delete')) {
                method = 'Delete'
            }
            if (_.includes(method, 'Show') || _.includes(method, 'show')) {
                method = 'Show'
            }
            if (_.includes(method, 'Update') || _.includes(method, 'update')) {
                method = 'Update'
            }

            return method;
        },
        timeFormat(value) {
            return moment(value).format("DD-MM-YYYY [at] hh:mm:ss a")
        },
        paymentMethods(value) {
            if (value === 1) {
                return 'cash'
            }
            if (value === 2) {
                return 'credit card'
            }
            if (value === 3) {
                return 'cheque'
            }
        },
        productStatus(value) {
            if (value === 1) {
                return 'received'
            }
            if (value === 2) {
                return 'pending'
            }
            if (value === 3) {
                return 'ordered'
            }
        },

        showSpinner() {
            this.loading = true;
        },
        hideSpinner() {
            this.loading = false;
        },
        printMe(){
            window.print()
        }
    }
})
