require('./bootstrap');

// Importing InertiaJS
import { InertiaApp } from '@inertiajs/inertia-vue';

// Importing Vue JS
import Vue from 'vue';

// Importing and injecting vue-cookies (https://www.npmjs.com/package/vue-cookies) - not to confuse with vue-cookie
import VueCookies from 'vue-cookies';
Vue.use(VueCookies);
// set default config
Vue.$cookies.config('30d');

// Use laravel Routes in your application (https://github.com/tightenco/ziggy)
Vue.prototype.$route = (...args) => route(...args).url();

// Make InertiaApp as a view component available?
Vue.use(InertiaApp);

// See InertiaJS Docs
const app = document.getElementById('app');

// See InertiaJS Docs
new Vue({
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => import(`./Pages/${name}`).then((module) => module.default),
            },
        }),
}).$mount(app);

// Prevents to do a url on boot
let justBooted = true;

// Does every 10 minute a get request to trigger middleware LastActivity
function activityCheck() {
    if (justBooted !== true) {
        require('axios').default.post('/activity-check');
    }
    justBooted = false;
    setTimeout(activityCheck, 600000);
}

activityCheck();
