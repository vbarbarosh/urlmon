<template>
    <input-textarea @update:model-value="input" :model-value="modelValue" :label="(label || 'JSON')" />
</template>

<script>
    import InputTextarea from './input-textarea.vue';

    const input_json = {
        props: ['modelValue', 'label'],
        emits: ['update:modelValue'],
        components: {
            InputTextarea,
        },
        methods: {
            input: function (value) {
                this.$emit('update:modelValue', value);
            },
        },
    };

    export default input_json;
</script>
