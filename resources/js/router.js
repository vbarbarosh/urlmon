import PageAbout from './components/page-about.vue';
import PageHome from './components/page-home.vue';
import PageParsersList from './components/page-parsers-list.vue';
import PageParsersNew from './components/page-parsers-new.vue';
import PageParsersUpdate from './components/page-parsers-update.vue';
import PageTargetsList from './components/page-targets-list.vue';
import PageTargetsNew from './components/page-targets-new.vue';
import PageTargetsUpdate from './components/page-targets-update.vue';
import {createRouter, createWebHashHistory} from 'vue-router';

const routes = [
    {path: '/', component: PageHome},
    {path: '/parsers', component: PageParsersList},
    {path: '/parsers/new', component: PageParsersNew},
    {path: '/parsers/:parser_uid', component: PageParsersUpdate},
    {path: '/targets', component: PageTargetsList},
    {path: '/targets/new', component: PageTargetsNew},
    {path: '/targets/:target_uid', component: PageTargetsUpdate},
    {path: '/about', component: PageAbout},
];

const history = createWebHashHistory();
const router = createRouter({
    history,
    routes,
});

export {history, router};
