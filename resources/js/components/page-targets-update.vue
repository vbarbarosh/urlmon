<template>
    <form v-if="(target && artifacts)" v-on:submit.prevent="submit">
        <form-target v-model="target" />
        <button-group-right>
            <button-danger v-on:click="click_delete">Delete</button-danger>
            <span class="mla" />
            <button-info @click="click_parse" class="ph25">Parse</button-info>
            <span class="ml25"></span>
            <button-warning @click="$router.back()">Cancel</button-warning>
            <button-primary type="submit">Update</button-primary>
        </button-group-right>
        <br>
        <hr>
        <br>
        <pre>{{ artifacts }}</pre>
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
    import api_artifacts_list from '../helpers/api/api_artifacts_list';
    import api_targets_fetch from '../helpers/api/api_targets_fetch';
    import api_targets_parse from '../helpers/api/api_targets_parse';
    import api_targets_patch from '../helpers/api/api_targets_patch';
    import blocking from '../helpers/blocking';
    import modal_target_delete from '../helpers/modal/modal_target_delete';

    const page_targets_update = {
        components: {ButtonInfo, ButtonDanger, ButtonPrimary, ButtonSuccess, ButtonWarning, ButtonGroupRight, FormTarget},
        data: function () {
            return {
                target: null,
                artifacts: null,
            };
        },
        methods: {
            refresh: async function () {
                [this.target, this.artifacts] = await Promise.all([
                    api_targets_fetch({target_uid: this.$route.params.target_uid}),
                    api_artifacts_list({target: this.$route.params.target_uid}),
                ]);
            },
            submit: async function () {
                await blocking(api_targets_patch({target: this.target}));
                this.$router.back();
            },
            click_parse: async function () {
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
