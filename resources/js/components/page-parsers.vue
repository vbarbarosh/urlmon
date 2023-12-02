<template>
    <div>
        parsers
        <pre>{{ items }}</pre>
    </div>
</template>

<script>
    import api_parsers_list from '../helpers/api/api_parsers_list';

    const page_parsers = {
        data: function () {
            return {
                items: null,
            };
        },
        created: async function () {
            this.items = await api_parsers_list();
        },
    };

    export default page_parsers;
</script>
