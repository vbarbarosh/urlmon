<template>
    <div class="mg15">
        <button-success @click="$router.push('/targets/new')">
            Add new target
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
                    <div class="hsplit">
                        <div>
                            <template v-if="item.label">
                                <div class="ww">{{ item.label }}</div>
                                <div class="fs10 fw3"><a v-bind:href="item.url" target="_blank">{{ item.url }}</a></div>
                            </template>
                            <template    v-else>
                                <div><a v-bind:href="item.url" target="_blank">{{ item.url }}</a></div>
                            </template>
                        </div>
                        <div class="fluid rel oh">
                            <vue3-picture-swipe v-bind:items="render_item_images(item)" v-bind:options="{shareEl: false}" class="abs-r vue3-picture-swipe" />
                        </div>
                    </div>
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
    import ButtonGroup from './button-groups/button-group.vue';
    import ButtonInfo from './buttons/button-info.vue';
    import ButtonLight from './buttons/button-light.vue';
    import ButtonPrimary from './buttons/button-primary.vue';
    import ButtonSuccess from './buttons/button-success.vue';
    import api_targets_list from '../helpers/api/api_targets_list';
    import api_targets_parse from '../helpers/api/api_targets_parse';
    import format_date from '../helpers/format_date';
    import m_blocking from '../helpers/m_blocking';
    import Vue3PictureSwipe from 'vue3-picture-swipe';

    const page_targets_list = {
        components: {ButtonSuccess, ButtonGroup, ButtonInfo, ButtonLight, ButtonPrimary, Vue3PictureSwipe},
        data: function () {
            return {
                items: null,
            };
        },
        methods: {
            format_date,
            refresh: async function () {
                this.items = await api_targets_list();
            },
            click_parse: m_blocking(async function (item) {
                await api_targets_parse({target: item});
                await this.refresh();
            }),
            click_update: async function (item) {
                this.$router.push(`/targets/${item.uid}`);
            },
            render_item_images: function (item) {
                return item.images.map(function (image) {
                    return {
                        src: image.url,
                        thumbnail: `http://127.0.0.1:9002/fit?url=${encodeURIComponent(image.url)}&width=400&height=400`,
                        w: image.width,
                        h: image.height,
                        alt: 'xxx'
                    };
                });
            },
        },
        created: async function () {
            await this.refresh();
        },
    };

    export default page_targets_list;
</script>

<style lang="sass">
.vue3-picture-swipe
    white-space: nowrap
.vue3-picture-swipe [itemprop=thumbnail]
    max-width: 40px
    max-height: 40px
</style>
