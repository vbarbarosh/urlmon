<template>
    <form v-if="target" v-on:submit.prevent="submit" class="hsplit mi15">
        <div class="fluid">
            <form-target v-model="target" />
            <button-group-right>
                <button-danger v-on:click="click_delete">Delete</button-danger>
                <span class="mla" />
                <button-info @click="click_parse" class="ph25">Parse</button-info>
                <span class="ml25"></span>
                <button-warning @click="$router.back()">Cancel</button-warning>
                <button-primary type="submit">Update</button-primary>
            </button-group-right>
        </div>
        <div class="mg15">
            <div class="sticky-t">
                <div v-for="item in target.artifacts.filter(v => !v.is_image)" v-bind:key="item.uid">
                    <a :href="item.url" target="_blank">
                        <figure class="figure">
                            <template v-if="item.is_image">
                                <img :src="item.url" class="figure-img img-fluid rounded max-w100 max-h100" alt="...">
                            </template>
                            <figcaption class="figure-caption">{{ item.name }} <small class="fs8">{{ format_size(item.size) }}</small></figcaption>
                        </figure>
                    </a>
                </div>
                <!-- XXX vue3-picture-swipe uses <button /> without [type=button], which leads to form.submit -->
                <form v-on:submit.stop class="db">
                    <vue3-picture-swipe v-bind:items="photoswipe_items" v-bind:options="{shareEl: false}" class="vue3-picture-swipe2" />
                </form>
            </div>
        </div>
    </form>
</template>

<script>
    import ButtonDanger from './buttons/button-danger.vue';
    import ButtonGroupRight from './button-groups/button-group-right.vue';
    import ButtonInfo from './buttons/button-info.vue';
    import ButtonPrimary from './buttons/button-primary.vue';
    import ButtonSuccess from './buttons/button-success.vue';
    import ButtonWarning from './buttons/button-warning.vue';
    import FormTarget from './forms/form-target.vue';
    import Vue3PictureSwipe from 'vue3-picture-swipe';
    import api_targets_fetch from '../helpers/api/api_targets_fetch';
    import api_targets_parse from '../helpers/api/api_targets_parse';
    import api_targets_patch from '../helpers/api/api_targets_patch';
    import blocking from '../helpers/blocking';
    import format_size from '../helpers/format_size';
    import modal_target_delete from '../helpers/modal/modal_target_delete';

    const page_targets_update = {
        components: {
            ButtonInfo, ButtonDanger, ButtonPrimary, ButtonSuccess, ButtonWarning, ButtonGroupRight,
            FormTarget, Vue3PictureSwipe,
        },
        data: function () {
            return {
                target: null,
            };
        },
        computed: {
            photoswipe_items: function () {
                return this.target.artifacts.filter(v => v.is_image).map(function (artifact) {
                    return {
                        src: artifact.url,
                        thumbnail: `http://127.0.0.1:9002/fit?url=${encodeURIComponent(artifact.url)}&width=400&height=400`,
                        w: artifact.meta.width,
                        h: artifact.meta.height,
                        alt: artifact.name,
                    };
                });
            },
        },
        methods: {
            format_size,
            refresh: async function () {
                // XXX Found no way to tell photoswipe not to use location url
                const target_uid = this.$route.params.target_uid.replace(/&.*/, '');
                this.target = await api_targets_fetch({target_uid});
            },
            submit: async function () {
                await blocking(api_targets_patch({target: this.target}));
                this.$router.back();
            },
            click_parse: async function () {
                await blocking(api_targets_patch({target: this.target}));
                await blocking(this.refresh());
                await blocking(api_targets_parse({target: this.target}));
                await blocking(this.refresh());
            },
            click_delete: async function () {
                if (await modal_target_delete({target: this.target}).promise()) {
                    this.$router.back();
                }
            },
        },
        created: async function () {
            await blocking(this.refresh());
        },
    };

    export default page_targets_update;
</script>

<style lang="sass">
.vue3-picture-swipe2 [itemprop=thumbnail]
    display: block
    max-width: 100px
    max-height: 100px
</style>
