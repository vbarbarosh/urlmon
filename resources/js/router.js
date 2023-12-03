import PageAbout from './components/page-about.vue';
import PageHome from './components/page-home.vue';
import PageParsersList from './components/page-parsers-list.vue';
import PageParsersNew from './components/page-parsers-new.vue';
import PageParsersUpdate from './components/page-parsers-update.vue';
import PageUrlsNew from './components/page-urls-new.vue';
import PageUrlsUpdate from './components/page-urls-update.vue';
import pageUrlsList from './components/page-urls-list.vue';
import {createRouter, createWebHashHistory} from 'vue-router';

const routes = [
    {path: '/', component: PageHome},
    {path: '/parsers', component: PageParsersList},
    {path: '/parsers/new', component: PageParsersNew},
    {path: '/parsers/:parser_uid', component: PageParsersUpdate},
    {path: '/urls', component: pageUrlsList},
    {path: '/urls/new', component: PageUrlsNew},
    {path: '/urls/:url_uid', component: PageUrlsUpdate},
    {path: '/about', component: PageAbout},
];

const history = createWebHashHistory();
const router = createRouter({
    history,
    routes,
});

export {history, router};
