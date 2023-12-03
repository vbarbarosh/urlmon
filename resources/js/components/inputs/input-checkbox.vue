<template>
    <div class="form-check">
        <input @input="input" :value="modelValue" :id="id" class="form-check-input" type="checkbox">
        <label v-bind:for="id" class="form-check-label">
            {{ label }}
        </label>
    </div>
</template>

<script>
    import cuid2 from '../../helpers/cuid2';

    const input_checkbox = {
        props: ['modelValue', 'label'],
        emits: ['update:modelValue'],
        data: function () {
            return {
                id: cuid2(),
            };
        },
        methods: {
            input: function (event) {
                this.$emit('update:modelValue', event.currentTarget.checked);
            },
        },
    };

    export default input_checkbox;
</script>
