<template>
    <div class="form-floating">
        <input @input="input" :value="modelValue" :id="id" :readonly="readonly" placeholder="placeholder" type="email" class="form-control" />
        <label v-bind:for="id">
            {{ label || 'Email' }}
        </label>
    </div>
</template>

<script>
    import cuid2 from '../../helpers/cuid2';

    const input_email = {
        props: ['modelValue', 'label', 'placeholder', 'readonly'],
        emits: ['update:modelValue'],
        data: function () {
            return {
                id: cuid2(),
            };
        },
        methods: {
            input: function (event) {
                this.$emit('update:modelValue', event.currentTarget.value);
            },
        },
    };

    export default input_email;
</script>
