<template>
    <form v-on:submit.prevent="submit">
        <form-parser v-model="parser" />
        <button-group-right>
            <button-warning @click="$router.back()">Cancel</button-warning>
            <button-success type="submit">Add new parser</button-success>
        </button-group-right>
    </form>
</template>

<script>
    import api_parsers_create from '../helpers/api/api_parsers_create';
    import blocking from '../helpers/blocking';
    import ButtonGroupRight from './button-groups/button-group-right.vue';
    import ButtonPrimary from './buttons/button-primary.vue';
    import ButtonSuccess from './buttons/button-success.vue';
    import ButtonWarning from './buttons/button-warning.vue';
    import FormParser from './forms/form-parser.vue';

    const page_parsers_new = {
        components: {
            ButtonSuccess,
            ButtonWarning,
            ButtonGroupRight,
            ButtonPrimary,
            FormParser,
        },
        data: function () {
            return {
                parser: {},
            };
        },
        methods: {
            submit: async function () {
                await blocking(api_parsers_create({parser: this.parser}));
                this.$router.replace('/parsers');
            },
        }
    };

    export default page_parsers_new;
</script>
