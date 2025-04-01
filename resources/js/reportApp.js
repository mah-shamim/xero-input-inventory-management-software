/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap')
require('./common')
import {mapGetters} from "vuex";
window.Vue = require('vue')
window.moment = require('moment')
window.moment.tz = require('moment-timezone')
moment().tz("Asia/Dhaka").format()
// moment.suppressDeprecationWarnings = true;
Vue.prototype._ = _;
//
import Vuetify from 'vuetify'
import VueRouter from 'vue-router'

import {default as reportRoute} from './routes/reportRoutes'

Vue.use(Vuetify,{ iconfont: 'mdi' })


import {Erp} from "./ERPFramework/Erp"
import './plugins/vueHtmlToPaper'

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
    reportRoute,
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

Vue.component('pagination', require('laravel-vue-pagination'))

Vue.component('conversion-component', require('./components/global/inventory/conversionComponent.vue').default)
Vue.component('print-component', require('./components/global/print/print.vue').default)
Vue.component('pos-print', require('./components/global/print/pos-print.vue').default)
Vue.component('barcode-print', require('./components/global/print/barcodePrint.vue').default)
Vue.component('list-print-bootstrap', require('./components/global/print/list-print-bootstrap.vue').default)
Vue.component('barcode-print-for-pos', require('./components/global/print/barcodePrintPos.vue').default)
Vue.component('has-feature-access', require('./components/global/inventory/hasFeatuerAccess.vue').default)
Vue.component('combo-chart', require('./components/charts/ComboChartComponent').default)
Vue.component('single-chart', require('./components/charts/sinleChartComponent').default)
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
        erp: new Erp(),
        access: false,
        settings:{},
        companySettings:{},
        dialog: false,
        drawer: null,
        appDense:appDense
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
    computed:{
        ...mapGetters({company: 'settings/getCompanyDetail'}),
        ...mapGetters({topBarColor: 'settings/getTopBarColor'}),
        ...mapGetters({defaultDateFormat: 'settings/getDefaultDateFormat'}),
        ...mapGetters({sideBarColor: 'settings/getSideBarColor'})
    },

    methods: {
        getDefault_id(arrs){
            return arrs.filter(data => parseInt(data.is_default) === 1)[0].id
        },
        getPrimary_id(arrs){
            return arrs.filter(data => parseInt(data.is_primary) === 1)[0].id
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
        printMe(){
            window.print()
        }
    }
})
