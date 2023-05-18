define([
    'text!./index.html',
    'css!./index.css'
], function (html) {
    return {
        props: {
            recommend: {
                type: Object,
                default: function () {
                    return {};
                }
            }
        },
        data: function () {
            return {};
        },
        methods: {},
        template: html
    };
});