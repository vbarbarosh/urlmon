<template>
    <div class="fix-f oa flex-row modal-loading" style="background: rgba(0, 0, 0, 0.25)">
        <div class="ma">
            <div class="w500 ma mv15 p15 mg15 white border bs15">
                <p class="xm">Loading...</p>
                <template v-if="(blocking_statuses.length > 0)">
                    <ul class="xls xp xm bn-color-black">
                        <li v-for="item in blocking_statuses" v-bind:key="item.uid">
                            {{ item.message }}
                        </li>
                    </ul>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    const modal_loading = {
        props: ['value'],
        inject: ['modal'],
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

<style lang="sass">
.modal-loading
    z-index: 1000001 !important
</style>
