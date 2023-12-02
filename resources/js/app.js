import './bootstrap';

import { createApp } from 'vue';
import App from './components/app.vue';
import {history} from './router';
import {router} from './router';

const vm = createApp(App);

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
