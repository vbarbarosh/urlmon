<template>
    <input-text v-on:update:model-value="input" v-bind:model-value="modelValue" v-bind:label="(label || 'RegEx')" />
</template>

<script>
    import InputText from './input-text.vue';

    const input_regex = {
        props: ['modelValue', 'label'],
        emits: ['update:modelValue'],
        components: {
            InputText,
        },
        methods: {
            input: function (value) {
                this.$emit('update:modelValue', value);
            },
        },
    };

    export default input_regex;
</script>
