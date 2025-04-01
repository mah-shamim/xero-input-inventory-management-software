import store from "../js/store";

require('../js/bootstrap')
window.Vue = require('vue')
import Vuetify from 'vuetify'
import VueRouter from 'vue-router'

Vue.use(Vuetify, {iconfont: 'mdi'})
Vue.use(VueRouter)

import {default as documentationRoute} from './documentationRoute.js'

const router = new VueRouter({
    documentationRoute,
    linkActiveClass: 'is-active',
    mode: 'history',
    scrollBehavior(to, from, savedPosition) {
        return {x: 0, y: 0}
    }
})
new Vue({
    el: '#app',
    router,
    store,
    vuetify: new Vuetify()
})