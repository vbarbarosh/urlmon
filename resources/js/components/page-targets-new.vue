<template>
    <form v-on:submit.prevent="submit">
        <form-target v-model="target" />
        <button-group-right>
            <button-warning @click="$router.back()">Cancel</button-warning>
            <button-success type="submit">Add new target</button-success>
        </button-group-right>
    </form>
</template>

<script>
    import api_targets_create from '../helpers/api/api_targets_create';
    import blocking from '../helpers/blocking';
    import ButtonGroupRight from './button-groups/button-group-right.vue';
    import ButtonSuccess from './buttons/button-success.vue';
    import ButtonWarning from './buttons/button-warning.vue';
    import FormTarget from './forms/form-target.vue';

    const page_targets_new = {
        components: {
            ButtonSuccess, ButtonWarning, ButtonGroupRight, FormTarget,
        },
        data: function () {
            return {
                target: {},
            };
        },
        methods: {
            submit: async function () {
                await blocking(api_targets_create({target: this.target}));
                this.$router.replace('/targets');
            },
        }
    };

    export default page_targets_new;
</script>
