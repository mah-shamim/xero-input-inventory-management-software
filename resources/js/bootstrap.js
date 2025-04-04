window._ = require('lodash');
/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
    window.swal=require('sweetalert2');
    window.chart = require('chart.js')
    window.api_base_url    = '/api/inventory'
    window.api_payroll_url = '/api/payroll'
} catch (e) {}
window.appDense=true
/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });

axios.interceptors.response.use((response) => {
    return response
}, error => {
    if(error.response.status === 403) {
        swal.fire({
            icon             : 'error',
            timer            : 2000,
            text             : 'you are not authorized',
            showCancelButton : false,
            showConfirmButton: false,
        })
            .then((result) => {
                document.getElementById('logout-form').submit();
            })
    }
    // if (error.response.status === 500) {
    //     console.log(error.response)
    //     swal.fire({
    //         icon: 'error',
    //         timer: 2000,
    //         text: 'you are not authorized',
    //         showCancelButton: false,
    //         showConfirmButton: false,
    //     })
    //         .then((result) => {
    //             swal.fire({
    //                 icon: 'error',
    //                 timer: 2000,
    //                 text: 'you are not authorized',
    //                 showCancelButton: false,
    //                 showConfirmButton: false,
    //             })
    //         })
    // }
    if(error.response.status === 500) {
        swal.fire({
            icon             : 'mdi-alert',
            timer            : 8000,
            text             : error.response.statusText,
            showCancelButton : false,
            showConfirmButton: false,
        })
    }
    if(error.response.status === 401 || error.response.status === 419) {
        swal.fire({
            icon             : 'error',
            timer            : 2000,
            text             : 'you are not authorized',
            showCancelButton : false,
            showConfirmButton: false,
        })
            .then((result) => {
                window.location.href = '/'
            })
    }
    return Promise.reject(error)
})