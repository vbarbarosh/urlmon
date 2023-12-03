<template>
    <input-plaintext v-bind:model-value="modelValue" label="uid" />
</template>

<script>
    import InputPlaintext from './input-plaintext.vue';

    const input_uid = {
        props: ['modelValue'],
        components: {
            InputPlaintext,
        },
    };

    export default input_uid;
</script>
