<template>
    <input-text v-on:update:model-value="input" v-bind:model-value="modelValue" label="Url" />
</template>

<script>
    import InputText from './input-text.vue';

    const input_url = {
        props: ['modelValue'],
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

    export default input_url;
</script>
