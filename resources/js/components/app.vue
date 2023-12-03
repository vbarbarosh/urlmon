<template>
    <app-nav />
    <br>
    <div class="container">
        <router-view />
    </div>
</template>

<script>
    import AppNav from './app-nav.vue';

    const app = {
        components: {AppNav},
    };

    export default app;
</script>
