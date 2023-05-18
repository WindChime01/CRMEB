define(function() {
    return function install(Vue) {
        Vue.mixin({
            beforeCreate: function () {
                var self = this;
                if (this.$options.router) {
                    Object.defineProperty(Vue.prototype, '$router', {
                        get: function () {
                            return self.$options.router;
                        }
                    });
                }
            }
        });
    };
});