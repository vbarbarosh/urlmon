<template>
    <div class="fix-f flex-row oa modal">
        <div class="mt25 mha">
            <div class="xm modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete url</h5>
                        <button v-on:click="modal.return(false)" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete the following url?</p>
                        <p><code>{{ value.url.url }}</code></p>
                    </div>
                    <div class="modal-footer text-end">
                        <button-secondary v-on:click="modal.return(false)">Cancel</button-secondary>
                        <button-danger v-on:click="click_delete">Delete</button-danger>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop show z1n" />
    </div>
</template>

<script>
    import api_urls_delete from '../../helpers/api/api_urls_delete';
    import ButtonDanger from '../buttons/button-danger.vue';
    import ButtonSecondary from '../buttons/button-secondary.vue';
    import blocking from '../../helpers/blocking';
    import m_modal_hide from '../../helpers/m_modal_hide';

    const modal_url_delete = {
        components: {ButtonSecondary, ButtonDanger},
        props: ['value'],
        inject: ['modal'],
        methods: {
            click_delete: m_modal_hide(async function () {
                let out = false;
                try {
                    await blocking(api_urls_delete({url: this.value.url}));
                    out = true;
                }
                finally {
                    this.modal.return(out);
                }
            }),
        },
    };

    export default modal_url_delete;
</script>
