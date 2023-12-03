<template>

    <div class="mb-3">
        <label :for="id_uid" class="form-label">uid</label>
        <input v-model="modelValue.uid" :id="id_uid" type="text" class="form-control" disabled />
    </div>

    <div class="mb-3">
        <label :for="id_label" class="form-label">Label</label>
        <input v-model="modelValue.label" :id="id_label" type="text" class="form-control">
        <div class="form-text">
            Short text used to introduce a parser.
        </div>
    </div>

    <div class="mb-3">
        <label :for="id_engine" class="form-label">Engine</label>
        <select v-model="modelValue.engine" :id="id_engine" class="form-select">
            <option disabled>Open this select menu</option>
            <option value="http_status">http_status</option>
            <option value="http_head">http_head</option>
            <option value="puppeteer/meta">puppeteer/meta</option>
            <option value="puppeteer/pages">puppeteer/pages</option>
            <option value="wget/meta">wget/meta</option>
            <option value="wget/pages">wget/pages</option>
        </select>
    </div>

    <div class="mb-3">
        <label :for="id_match" class="form-label">Match (regexp)</label>
        <input v-model="modelValue.match" :id="id_match" type="text" class="form-control" />
    </div>

    <div class="mb-3">
        <label :for="id_config" class="form-label">Config (individual for each engine)</label>
        <textarea v-model="modelValue.config" :id="id_config" class="form-control font-monospace" rows="3" />
    </div>

</template>

<script>
    import cuid2 from '../../helpers/cuid2';

    const form_parser = {
        props: ['modelValue'],
        data: function () {
            return {
                id_uid: cuid2(),
                id_label: cuid2(),
                id_engine: cuid2(),
                id_match: cuid2(),
                id_config: cuid2(),
            };
        },
    };

    export default form_parser;
</script>
