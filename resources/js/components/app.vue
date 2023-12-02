<template>
    <div>
        <ul>
            <li><router-link to="/">home</router-link></li>
            <li><router-link to="/parsers">parsers</router-link></li>
            <li><router-link to="/urls">urls</router-link></li>
            <li><router-link to="/about">about</router-link></li>
        </ul>
        <router-view />
    </div>
</template>

<script>
    const app = {
    };

    export default app;
</script>
