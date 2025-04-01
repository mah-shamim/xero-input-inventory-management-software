/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import {mapGetters} from "vuex";
import mixins from "./mixins";
require('./bootstrap')
window.Vue = require('vue')

window.moment = require('moment')
window.moment.tz = require('moment-timezone')
moment().tz("Asia/Dhaka").format()
Vue.prototype._ = _;

import Vuetify from 'vuetify'
import VueRouter from 'vue-router'
import VeeValidate from 'vee-validate'
import {default as payrollRoute} from './routes/payrollRoutes'

Vue.use(Vuetify, {iconfont: 'mdi'})
Vue.use(VeeValidate)
Vue.mixin(mixins)

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

const routes = rout.concat(
    payrollRoute
)
//
//
const router = new VueRouter({
    routes,
    linkActiveClass: 'is-active',
    mode: 'history',
    scrollBehavior(to, from, savedPosition) {
        return {x: 0, y: 0}
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
Vue.component('payment-add', require('./components/global/payment-add.vue').default)

//dropdown components
Vue.component('search-customer-dropdown', require('./components/search-customer-dropdown/search-customer-dropdown.vue').default)
Vue.component('search-product-dropdown', require('./components/search-product-dropdown/search-product-dropdown.vue').default)
Vue.component('warehouse-list-dropdown', require('./components/warehouse-list-dropdown/warehouse-list-dropdown.vue').default)

Vue.component('add-warranty', require('./components/add-warranty/add-warranty.vue').default)
Vue.component('add-product-damage', require('./components/add-product-damage/add-product-damage.vue').default)


Vue.component('category-lists', require('./components/global/inventory/category.vue').default)
Vue.component('payment-component', require('./components/global/inventory/paymentComponent.vue').default)
Vue.component('conversion-component', require('./components/global/inventory/conversionComponent.vue').default)
Vue.component('print-component', require('./components/global/print/print.vue').default)
Vue.component('pos-print', require('./components/global/print/pos-print.vue').default)
Vue.component('barcode-print', require('./components/global/print/barcodePrint.vue').default)
Vue.component('barcode-print-for-pos', require('./components/global/print/barcodePrintPos.vue').default)
Vue.component('combo-chart', require('./components/charts/ComboChartComponent').default)
Vue.component('single-chart', require('./components/charts/sinleChartComponent').default)
Vue.component('list-print-bootstrap', require('./components/global/print/list-print-bootstrap.vue').default)
Vue.component('tooltip-button', require('./components/global/tooltip-button.vue').default)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import appIcon from './ERPFramework/icons'
import _ from "lodash";

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
        sidebarDense: true,
        erp: new Erp(),
        access: false,
        settings: {},
        companySettings: {},
        dialog: false,
        drawer: null,
        appDense: appDense,

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
        if (!(window.location.pathname === '/login' || window.location.pathname === '/register')) {
            store.dispatch('settings/getSettings')
        }
    },
    computed: {
        ...mapGetters({company: 'settings/getCompanyDetail'}),
        ...mapGetters({topBarColor: 'settings/getTopBarColor'}),
        ...mapGetters({defaultDateFormat: 'settings/getDefaultDateFormat'}),
        ...mapGetters({sideBarColor: 'settings/getSideBarColor'})
    },
    methods: {
        ownParse(data) {
            return parseFloat(data)
        },
        hideUser() {
            document.getElementById("pullNotification").style.display = "none";
        },
        getDefault_id(arrs) {
            return arrs.filter(data => parseInt(data.is_default) === 1)[0].id
        },
        getPrimary_id(arrs) {
            return arrs.filter(data => parseInt(data.is_primary) === 1)[0].id
        },

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
        printMe() {
            window.print()
        }
    }
})
