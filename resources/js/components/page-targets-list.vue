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
                        <div class="fluid flex-row-center flex-justify-end oh">
                            <vue3-picture-swipe v-bind:items="render_item_images(item)" v-bind:options="{shareEl: false}" class="vue3-picture-swipe" />
<!--
                            <img src="https://i.simpalsmedia.com/999.md/BoardImages/900x900/036d748bb756d5839ee1b93bb25e75cf.jpg" class="max-h40" alt="">
                            <img src="https://i.simpalsmedia.com/999.md/BoardImages/900x900/1887da33cbe0d6778caf36670df93a54.jpg" class="max-h40" alt="">
                            <img src="https://i.simpalsmedia.com/999.md/BoardImages/900x900/1d49be0e00c587256573d7d4cac5221f.jpg" class="max-h40" alt="">
                            <img src="https://i.simpalsmedia.com/999.md/BoardImages/900x900/247eebddebd8a4bf28c2d79246674152.jpg" class="max-h40" alt="">
                            <img src="https://i.simpalsmedia.com/999.md/BoardImages/900x900/29d873a55c0beecef1ef82d529b90d6a.jpg" class="max-h40" alt="">
                            <img src="https://i.simpalsmedia.com/999.md/BoardImages/900x900/3430142204a8f7f76c93decda75dd827.jpg" class="max-h40" alt="">
                            <img src="https://i.simpalsmedia.com/999.md/BoardImages/900x900/34cef7771ad2f7b02b7467ca5fab4e83.jpg" class="max-h40" alt="">
                            <img src="https://i.simpalsmedia.com/999.md/BoardImages/900x900/3c6a0eae5cf6b0179e9cdaff7e2e77e6.jpg" class="max-h40" alt="">
                            <img src="https://i.simpalsmedia.com/999.md/BoardImages/900x900/436d9b46a7bd75e4c3085277ed919375.jpg" class="max-h40" alt="">
                            <img src="https://i.simpalsmedia.com/999.md/BoardImages/900x900/52afd736d5494935c20e22a3c7604521.jpg" class="max-h40" alt="">
                            <img src="https://i.simpalsmedia.com/999.md/BoardImages/900x900/5613e03d5fa3cebe05bd40da8163645c.jpg" class="max-h40" alt="">
                            <img src="https://i.simpalsmedia.com/999.md/BoardImages/900x900/85880c50722b913ee79c2cb67a9f5df3.jpg" class="max-h40" alt="">
                            <img src="https://i.simpalsmedia.com/999.md/BoardImages/900x900/c36e7d60fb99b0bbe37405d2e375d705.jpg" class="max-h40" alt="">
                            <img src="https://i.simpalsmedia.com/999.md/BoardImages/900x900/ffba1c5ff51faf6b1c984d2d959d6440.jpg" class="max-h40" alt="">
-->
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
