import './bootstrap';

import App from './components/app.vue';
import modal_error from './helpers/modal/modal_error';
import {createApp} from 'vue';
import {history, router} from './router';

const vm = createApp(App);

vm.config.errorHandler = function (error, ...other) {
    console.log('errorHandler', error);
    modal_error(error);
    console.log(other);
};
vm.use(router);
vm.mount('#app');

window.h = history;
window.r = router;
window.vm = vm;

/*
// https://github.com/webpack/webpack/issues/625
// https://webpack.js.org/guides/dependency-management/#require-context
const require_components = require.context('./components', true, /\.vue$/);
require_components.keys().forEach(function (key) {
    const [, name] = key.match(/([^/]+)\.vue$/);
    Vue.component(name, require_components(key).default);
});
*/
