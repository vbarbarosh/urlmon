<template>

    <div class="mb-3">
        <label :for="id_uid" class="form-label">uid</label>
        <input v-model="modelValue.uid" :id="id_uid" type="text" class="form-control" disabled />
    </div>

    <div class="mb-3">
        <label :for="id_label " class="form-label">Label</label>
        <input v-model="modelValue.label" :id="id_label" type="text" class="form-control" />
    </div>

    <div class="mb-3">
        <label :for="id_parser" class="form-label">
            Parser
            <template v-if="modelValue.parser_uid">
                <router-link :to="`/parsers/${modelValue.parser_uid}`">(edit)</router-link>
            </template>
        </label>
        <select v-model="modelValue.parser_uid" :id="id_parser" class="form-select">
            <option disabled>Open this select menu</option>
            <template v-for="item in parsers || []" v-bind:key="item.uid">
                <option v-bind:value="item.uid">{{ item.label }}</option>
            </template>
        </select>
    </div>

    <div class="mb-3">
        <label :for="id_url " class="form-label">Url</label>
        <input v-model="modelValue.url" :id="id_url" type="text" class="form-control" />
    </div>

    <div class="mb-3">
        <label :for="id_meta" class="form-label">Meta</label>
        <textarea ref="textarea" :id="id_meta" disabled class="form-control font-monospace" rows="3">{{ modelValue.meta }}</textarea>
    </div>

    <div class="mb-3">
        <label :for="id_created" class="form-label">Created</label>
        <input v-model="modelValue.created_at" :id="id_created" disabled class="form-control font-monospace" />
    </div>

    <div class="mb-3">
        <label :for="id_updated" class="form-label">Updated</label>
        <input v-model="modelValue.updated_at" :id="id_updated" disabled class="form-control font-monospace" />
    </div>

</template>

<script>
    import api_parsers_list from '../../helpers/api/api_parsers_list';
    import autosize from 'autosize/dist/autosize';
    import blocking from '../../helpers/blocking';
    import cuid2 from '../../helpers/cuid2';

    const form_url = {
        props: ['modelValue'],
        data: function () {
            return {
                id_uid: cuid2(),
                id_parser: cuid2(),
                id_label: cuid2(),
                id_url: cuid2(),
                id_meta: cuid2(),
                id_created: cuid2(),
                id_updated: cuid2(),
                parsers: null,
            };
        },
        watch: {
            'modelValue.meta': function () {
                this.$nextTick(function () {
                    autosize.update(this.$refs.textarea);
                });
            },
        },
        created: function () {
            const _this = this;
            setTimeout(async function () {
                _this.parsers = await blocking(api_parsers_list());
            }, 0);
        },
        mounted: function () {
            autosize(this.$refs.textarea);
        },
    };

    export default form_url;
</script>
