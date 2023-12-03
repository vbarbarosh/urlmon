<template>
    <form v-if="parser" v-on:submit.prevent="submit">
        <h2>Update parser</h2>
        <form-parser v-model="parser" />
        <button-group-right>
            <button-danger v-on:click="click_delete">Delete</button-danger>
            <span class="mla" />
            <button-warning v-on:click="click_cancel">Cancel</button-warning>
            <button-primary type="submit">Update</button-primary>
        </button-group-right>
    </form>
</template>

<script>
    import api_parsers_fetch from '../helpers/api/api_parsers_fetch';
    import api_parsers_update from '../helpers/api/api_parsers_update';
    import modal_parser_delete from '../helpers/modal/modal_parser_delete';
    import ButtonGroupRight from './button-groups/button-group-right.vue';
    import ButtonDanger from './buttons/button-danger.vue';
    import ButtonPrimary from './buttons/button-primary.vue';
    import ButtonSuccess from './buttons/button-success.vue';
    import ButtonWarning from './buttons/button-warning.vue';
    import FormParser from './forms/form-parser.vue';
    import blocking from '../helpers/blocking';

    const page_parsers_update = {
        components: {
            ButtonGroupRight,
            ButtonDanger,
            ButtonWarning,
            ButtonSuccess,
            ButtonPrimary,
            FormParser,
        },
        data: function () {
            return {
                parser: null,
            };
        },
        methods: {
            refresh: async function () {
                this.parser = await api_parsers_fetch({parser_uid: this.$route.params.parser_uid});
            },
            submit: async function () {
                await blocking(api_parsers_update({parser: this.parser}));
                this.$router.back();
            },
            click_cancel: async function () {
                this.$router.back();
            },
            click_delete: async function () {
                if (await modal_parser_delete({parser: this.parser}).promise()) {
                    this.$router.back();
                }
            },
        },
        created: async function () {
            await blocking(this.refresh());
        },
    };

    export default page_parsers_update;
</script>
