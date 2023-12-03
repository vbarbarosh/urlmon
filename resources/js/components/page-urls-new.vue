<template>
    <form v-on:submit.prevent="submit">
        <form-url v-model="url" />
        <button-group-right>
            <button-warning @click="$router.back()">Cancel</button-warning>
            <button-success type="submit">Add new url</button-success>
        </button-group-right>
    </form>
</template>

<script>
    import api_urls_create from '../helpers/api/api_urls_create';
    import blocking from '../helpers/blocking';
    import ButtonGroupRight from './button-groups/button-group-right.vue';
    import ButtonSuccess from './buttons/button-success.vue';
    import ButtonWarning from './buttons/button-warning.vue';
    import FormParser from './forms/form-parser.vue';
    import FormUrl from './forms/form-url.vue';

    const page_urls_new = {
        components: {
            ButtonSuccess, ButtonWarning, ButtonGroupRight,
            FormUrl,
            FormParser,
        },
        data: function () {
            return {
                url: {},
            };
        },
        methods: {
            submit: async function () {
                await blocking(api_urls_create({url: this.url}));
                this.$router.replace('/urls');
            },
        }
    };

    export default page_urls_new;
</script>
