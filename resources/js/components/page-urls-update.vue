<template>
    <form v-if="url" v-on:submit.prevent="submit">
        <form-url v-model="url" />
        <button-group-right>
            <button-danger v-on:click="click_delete">Delete</button-danger>
            <span class="mla" />
            <button-info @click="click_parse" class="ph25">Parse</button-info>
            <span class="ml25"></span>
            <button-warning @click="$router.back()">Cancel</button-warning>
            <button-primary type="submit">Update</button-primary>
        </button-group-right>
    </form>
</template>

<script>
    import ButtonDanger from './buttons/button-danger.vue';
    import ButtonGroupRight from './button-groups/button-group-right.vue';
    import ButtonInfo from './buttons/button-info.vue';
    import ButtonPrimary from './buttons/button-primary.vue';
    import ButtonSuccess from './buttons/button-success.vue';
    import ButtonWarning from './buttons/button-warning.vue';
    import FormUrl from './forms/form-url.vue';
    import api_urls_fetch from '../helpers/api/api_urls_fetch';
    import api_urls_parse from '../helpers/api/api_urls_parse';
    import api_urls_patch from '../helpers/api/api_urls_patch';
    import blocking from '../helpers/blocking';
    import modal_url_delete from '../helpers/modal/modal_url_delete';

    const page_urls_update = {
        components: {ButtonInfo, ButtonDanger, ButtonPrimary, ButtonSuccess, ButtonWarning, ButtonGroupRight, FormUrl},
        data: function () {
            return {
                url: null,
            };
        },
        methods: {
            refresh: async function () {
                this.url = await api_urls_fetch({url_uid: this.$route.params.url_uid});
            },
            submit: async function () {
                await blocking(api_urls_patch({url: this.url}));
                this.$router.back();
            },
            click_parse: async function () {
                await blocking(api_urls_parse({url: this.url}));
                await blocking(this.refresh());
            },
            click_delete: async function () {
                if (await modal_url_delete({url: this.url}).promise()) {
                    this.$router.back();
                }
            },
        },
        created: async function () {
            await blocking(this.refresh());
        },
    };

    export default page_urls_update;
</script>
