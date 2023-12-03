<template>
    <div class="mg15">
        <button-success @click="$router.push('/urls/new')">
            Add new url
        </button-success>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Label + URL</th>
                <th scope="col">Updated</th>
                <th scope="col" />
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in items" v-bind:key="item.uid">
                <th class="w0 nowrap" scope="row">1</th>
                <td>
                    <template v-if="item.label">
                        <div class="ww">{{ item.label }}</div>
                        <div class="fs10 fw3"><a v-bind:href="item.url" target="_blank">{{ item.url }}</a></div>
                    </template>
                    <template    v-else>
                        <div><a v-bind:href="item.url" target="_blank">{{ item.url }}</a></div>
                    </template>
                </td>
                <td class="w0 nowrap" v-bind:title="item.updated_at">
                    {{ format_date(item.updated_at) }}
                </td>
                <td class="w0 nowrap">
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
    import format_date from '../helpers/format_date';
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
            format_date,
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
