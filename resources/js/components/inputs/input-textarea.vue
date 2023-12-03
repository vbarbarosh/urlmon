<template>
    <div class="form-floating">
        <textarea ref="textarea" v-on:input="input" v-bind:value="modelValue" v-bind:id="id" v-bind:placeholder="placeholder" class="input-textarea-minheight form-control" />
        <label v-bind:for="id">
            {{ label }}
        </label>
    </div>
</template>

<script>
    import autosize from 'autosize/dist/autosize';
    import cuid2 from '../../helpers/cuid2';

    const input_textarea = {
        props: ['modelValue', 'label', 'placeholder'],
        emits: ['update:modelValue'],
        data: function () {
            return {
                id: cuid2(),
            };
        },
        watch: {
            value: function () {
                this.$nextTick(function () {
                    autosize.update(this.$refs.textarea);
                });
            },
        },
        methods: {
            input: function (event) {
                this.$emit('update:modelValue', event.currentTarget.value);
            },
        },
        mounted: function () {
            autosize(this.$refs.textarea);
        },
    };

    export default input_textarea;
</script>

<style lang="sass">
    .input-textarea-minheight
        min-height: 100px !important
</style>
