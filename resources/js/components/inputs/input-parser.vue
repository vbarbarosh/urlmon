<template>
    <input-select
        v-on:update:model-value="input"
        v-bind:model-value="modelValue"
        v-bind:options="options"
        label="Parser" />
</template>

<script>
    import InputSelect from './input-select.vue';

    const input_parser = {
        props: ['modelValue'],
        emits: ['update:modelValue'],
        components: {
            InputSelect,
        },
        data: function () {
            return {
                options: [],
            };
        },
        methods: {
            input: function (value) {
                this.$emit('update:modelValue', value);
            },
        },
    };

    export default input_parser;
</script>
