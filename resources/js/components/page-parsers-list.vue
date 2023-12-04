<template>
    <div class="mg15">
        <button-success @click="$router.push('/parsers/new')">
            Add new parser
        </button-success>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Label</th>
                <th scope="col">Targets</th>
                <th scope="col">updated</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in items" v-bind:key="item.uid">
                <th scope="row">1</th>
                <td>
                    <router-link :to="`/parsers/${item.uid}`">
                        {{ item.label }}
                    </router-link>
                </td>
                <td>
                    <router-link v-if="item.targets_count" :to="`/targets?parser=${item.uid}`">
                        <badge-secondary>
                            {{ item.targets_count }}
                        </badge-secondary>
                    </router-link>
                </td>
                <td v-bind:title="item.updated_at">
                    {{ format_date(item.updated_at) }}
                </td>
                <td>
                    <button-group>
                        <button-primary @click="$router.push(`/parsers/${item.uid}`)">Update</button-primary>
                    </button-group>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import BadgeSecondary from './badges/badge-secondary.vue';
    import ButtonGroup from './button-groups/button-group.vue';
    import ButtonPrimary from './buttons/button-primary.vue';
    import ButtonSuccess from './buttons/button-success.vue';
    import api_parsers_list from '../helpers/api/api_parsers_list';
    import format_date from '../helpers/format_date';

    const page_parsers = {
        methods: {format_date},
        components: {BadgeSecondary, ButtonSuccess, ButtonPrimary, ButtonGroup},
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
