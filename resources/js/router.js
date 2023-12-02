import PageAbout from './components/page-about.vue';
import PageHome from './components/page-home.vue';
import PageParsers from './components/page-parsers.vue';
import PageUrls from './components/page-urls.vue';
import {createRouter, createWebHashHistory} from 'vue-router';

const routes = [
    {path: '/', component: PageHome},
    {path: '/parsers', component: PageParsers},
    {path: '/urls', component: PageUrls},
    {path: '/about', component: PageAbout},
];

const history = createWebHashHistory();
const router = createRouter({
    history,
    routes,
});

export {history, router};
