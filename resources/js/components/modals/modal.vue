<template>
    <component v-bind:is="is" v-bind:value="value" />
</template>

<script>
    import Promise from 'bluebird';
    import jQuery from 'jquery';

    const modal = {
        props: ['is', 'value', 'vm'],
        provide: function () {
            return {modal: this};
        },
        data: function () {
            const xxx = {
                promise_shown: null,
                promise_hidden: null,

                promise_resolve: null,
                promise_destroyed_resolve: null,
                promise: null,
                promise_destroyed: null,
            };
            xxx.promise = new Promise(resolve => xxx.promise_resolve = resolve);
            xxx.promise_destroyed = new Promise(resolve => xxx.promise_destroyed_resolve = resolve);
            return {
                xxx
            };
        },
        methods: {
            hide: function () {
                if (promise_hidden) {
                    return this;
                }
                this.xxx.promise_shown = null;
                this.xxx.promise_hidden = new Promise(resolve => jQuery(this.$el).stop().fadeOut('fast', resolve));
                return this;
            },
            show: function () {
                if (promise_shown) {
                    return this;
                }
                this.xxx.promise_shown = new Promise(resolve => jQuery(this.$el).stop().fadeIn('fast', resolve));
                this.xxx.promise_hidden = null;
                return this;
            },
            show_if_pending: function () {
                return promise_resolve ? this.show() : undefined;
            },
            end: function (retval) {
                if (!promise_resolve) {
                    // XXX Simulate old behavior when `end` fn was able to call several times
                    return;
                }
                // XXX Deprecated in favor of `return`
                return this.return(retval);
            },
            return: function (retval) {
                // This method should be called no more than one time
                this.xxx.promise_resolve(retval);
                this.xxx.promise_resolve = null;
                // `this.$destroy` cannot be called without this
                // Uncaught TypeError: Cannot read property 'beforeDestroy' of undefined
                jQuery(this.$el).fadeOut('fast', () => this.vm.unmount());
                return this;
            },
            promise: function () {
                return this.xxx.promise;
            },
            promise_destroyed: function () {
                return this.xxx.promise_destroyed;
            },
        },
        mounted: function () {
            // Case when component calls `this.modal.hide()` from its `created` method.
            if (this.xxx.promise_hidden) {
                jQuery(this.$el).hide();
                return;
            }
            try { document.activeElement.blur(); } catch (error) {}
            jQuery(this.$el).hide().fadeIn('fast', () => autofocus(this.$el));
        },
        beforeUnmount: function () {
            this.$el.parentElement.remove();
        },
        unmounted: function () {
            if (this.xxx.promise_resolve) {
                this.xxx.promise_resolve();
                this.xxx.promise_resolve = null;
            }
            this.xxx.promise_destroyed_resolve(this.xxx.promise);
            this.xxx.promise_destroyed_resolve = null;
        },
    };

    function autofocus(parent)
    {
        return jQuery(parent).addBack().find('[autofocus]a, [autofocus] a, [autofocus]button, [autofocus] button, [autofocus]input, [autofocus] input, [autofocus]textarea, [autofocus] textarea').filter(':visible').first().focus().select();
    }

    export default modal;
</script>
