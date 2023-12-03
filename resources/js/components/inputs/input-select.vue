<template>
    <div>
        <select v-on:input="input" v-bind:value="modelValue" v-bind:id="id" class="form-select" aria-label="Default select example">
            <template v-if="options">
                <option v-for="item in options" v-bind:value="item.value">
                    {{ item.label }}
                </option>
            </template>
            <template v-else>
                <option selected>Open this select menu</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
            </template>
        </select>
    </div>
</template>

<script>
    import cuid2 from '../../helpers/cuid2';

    const input_select = {
        props: ['modelValue', 'options'],
        emits: ['update:modelValue'],
        data: function () {
            return {
                id: cuid2(),
            };
        },
        methods: {
            input: function (event) {
                this.$emit('update:modelValue', event.currentTaret.value);
            },
        }
    };

    export default input_select;
</script>
