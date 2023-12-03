<template>
    <div class="fix-f flex-row oa modal">
        <div class="ma">
            <div class="xm modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <template v-if="(blocking_statuses.length === 0)">
                            <spinner-border-primary />
                        </template>
                        <template v-else>
                            <ul class="xls xp xm bn-color-black">
                                <li v-for="item in blocking_statuses" v-bind:key="item.uid">
                                    {{ item.message }}
                                </li>
                            </ul>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop show z1n" />
    </div>
</template>

<script>
    import SpinnerBorderPrimary from '../spinners/spinner-border-primary.vue';

    const modal_loading = {
        props: ['value'],
        inject: ['modal'],
        components: {
            SpinnerBorderPrimary,
        },
        data: function () {
            return {
                blocking_statuses: function () {
                    return this.value || [];
                },
            };
        },
    };

    export default modal_loading;
</script>
