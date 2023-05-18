define([
    'store/index',
    'text!./index.html',
    'css!./index.css'
], function(store, html) {
    return {
        props: {
            agreeContent: {
                type: Object,
                default: function () {
                    return {};
                }
            }
        },
        data: function () {
            return store.state;
        },
        methods: {
            agreeClose: function () {
                store.setAgreeAction(false);
            }
        },
        template: html
    };
});