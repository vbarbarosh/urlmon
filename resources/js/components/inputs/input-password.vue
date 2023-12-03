<template>
    <div class="form-floating">
        <input v-on:input="input" v-bind:value="modelValue" v-bind:id="id" v-bind:readonly="readonly" placeholder="placeholder" type="password" class="form-control">
        <label v-bind:for="id">
            {{ label || 'Password' }}
        </label>
    </div>
</template>

<script>
    import cuid2 from '../../helpers/cuid2';

    const input_password = {
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

    export default input_password;
</script>
