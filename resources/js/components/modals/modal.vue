<template>
    <component v-bind:is="is" v-bind:value="value" />
</template>

<script>
    import Promise from 'bluebird';
    import jQuery from 'jquery';

    const __anim_speed__ = 'fast';
    const animations = {
        show(inst) {
            return new Promise(resolve => jQuery(inst.$el).stop().fadeIn(__anim_speed__, resolve));
        },
        show_mounted(inst) {
            return new Promise(resolve => jQuery(inst.$el).hide().fadeIn(__anim_speed__, resolve));
        },
        hide(inst) {
            return new Promise(resolve => jQuery(inst.$el).stop().fadeOut(__anim_speed__, resolve));
        },
        hide_return(inst) {
            return new Promise(resolve => jQuery(inst.$el).stop().fadeOut(__anim_speed__, resolve));
        }
    };

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
                if (this.xxx.promise_hidden) {
                    return this;
                }
                this.xxx.promise_shown = null;
                this.xxx.promise_hidden = animations.hide(this);
                return this;
            },
            show: function () {
                if (this.xxx.promise_shown) {
                    return this;
                }
                this.xxx.promise_shown = animations.show(this);
                this.xxx.promise_hidden = null;
                return this;
            },
            show_if_pending: function () {
                return this.xxx.promise_resolve ? this.show() : undefined;
            },
            end: function (retval) {
                if (!this.xxx.promise_resolve) {
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
                animations.hide_return(this).then(() => this.vm.unmount());
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
            jQuery(this.$el).hide();
            // Case when component calls `this.modal.hide()` from its `created` method.
            if (this.xxx.promise_hidden) {
                jQuery(this.$el).hide();
                return;
            }
            try { document.activeElement.blur(); } catch (error) {}
            this.$nextTick(function () {
                animations.show_mounted(this).then(() => autofocus(this.$el));
            });
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
