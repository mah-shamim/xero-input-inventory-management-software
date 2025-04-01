import Vue from 'vue';
import VueHtmlToPaper from "vue-html-to-paper";

const options = {
    name  : '_blank',
    autoClose: false,
    specs : [],
    styles: [
        '/css/bootstrap.min.css',
    ]
}
Vue.use(VueHtmlToPaper, options);
