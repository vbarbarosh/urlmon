<template>
    <div>
        page-urls
        <button v-on:click="click_modal">modal</button>
        <ul>
            <li v-for="item in items" v-bind:key="item.uid">
                <pre>{{ item }}</pre>
                <button v-on:click="click_parse(item)">parse</button>
            </li>
        </ul>
    </div>
</template>

<script>
    import api_urls_list from '../helpers/api/api_urls_list';
    import api_urls_parse from '../helpers/api/api_urls_parse';
    import m_blocking from '../helpers/m_blocking';
    import modal_hello from '../helpers/modal/modal_hello';

    const page_urls = {
        data: function () {
            return {
                items: null,
            };
        },
        methods: {
            refresh: async function () {
                this.items = await api_urls_list();
            },
            click_parse: m_blocking(async function (url) {
                await api_urls_parse({url});
                await this.refresh();
            }),
            click_modal: async function () {
                await modal_hello().promise();
            },
        },
        created: async function () {
            await this.refresh();
        },
    };

    export default page_urls;
</script>
