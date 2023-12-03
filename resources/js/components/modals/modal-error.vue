<template>
    <div class="fix-f flex-row oa modal">
        <div class="mt25 mha">
            <div class="xm modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Error</h5>
                        <button v-on:click="modal.return(false)" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <pre>{{ value.message }}</pre>
                        <template v-if="(value?.response?.data)">
                            <pre>{{ value.response.data }}</pre>
                        </template>
                    </div>
                    <div class="modal-footer">
                        <button v-on:click="modal.return(false)" type="button" class="btn btn-secondary">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop show z1n" />
    </div>
</template>

<script>
    const modal_error = {
        props: ['value'],
        inject: ['modal'],
    };

    export default modal_error;
</script>
