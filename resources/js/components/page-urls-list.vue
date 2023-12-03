<template>
    <div class="mg15">
        <button-success @click="$router.push('/urls/new')">
            Add new url
        </button-success>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">url</th>
                <th scope="col">updated</th>
                <th scope="col" />
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in items" v-bind:key="item.uid">
                <th scope="row">1</th>
                <td>
                    <a v-bind:href="item.url" target="_blank">{{ item.url }}</a>
                </td>
                <td v-bind:title="item.updated_at">
                    {{ item.updated_at}}
                </td>
                <td>
                    <button-group>
                        <button-info v-on:click="click_parse(item)">Parse</button-info>
                        <button-primary v-on:click="click_update(item)">Update</button-primary>
                    </button-group>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import ButtonGroup from './button-groups/button-group.vue';
    import ButtonInfo from './buttons/button-info.vue';
    import ButtonLight from './buttons/button-light.vue';
    import ButtonPrimary from './buttons/button-primary.vue';
    import ButtonSuccess from './buttons/button-success.vue';
    import api_urls_list from '../helpers/api/api_urls_list';
    import api_urls_parse from '../helpers/api/api_urls_parse';
    import m_blocking from '../helpers/m_blocking';

    const page_urls = {
        components: {ButtonSuccess, ButtonGroup, ButtonInfo, ButtonLight, ButtonPrimary},
        data: function () {
            return {
                items: null,
            };
        },
        methods: {
            refresh: async function () {
                this.items = await api_urls_list();
            },
            click_parse: m_blocking(async function (item) {
                await api_urls_parse({url: item});
                await this.refresh();
            }),
            click_update: async function (item) {
                this.$router.push(`/urls/${item.uid}`);
            },
        },
        created: async function () {
            await this.refresh();
        },
    };

    export default page_urls;
</script>
