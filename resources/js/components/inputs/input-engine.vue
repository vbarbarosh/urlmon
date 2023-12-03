<template>
    <input-select @update:modelValue="input" :model-value="modelValue" :options="options" label="Engine" />
</template>

<script>
    import InputSelect from './input-select.vue';

    const input_engine = {
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

    export default input_engine;
</script>
