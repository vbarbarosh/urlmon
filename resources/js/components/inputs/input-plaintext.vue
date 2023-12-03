<template>
    <div class="form-floating">
        <input v-bind:value="modelValue" v-bind:id="id" placeholder="placeholder" type="text" class="form-control-plaintext">
        <label v-bind:for="id">
            {{ label }}
        </label>
    </div>
</template>

<script>
    import cuid2 from '../../helpers/cuid2';

    const input_plaintext = {
        props: ['modelValue', 'label', 'placeholder'],
        data: function () {
            return {
                id: cuid2(),
            };
        },
    };

    export default input_plaintext;
</script>
