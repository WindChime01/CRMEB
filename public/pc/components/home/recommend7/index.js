define([
    'require',
    'text!./index.html',
    'css!./index.css'
], function (require, html) {
    return {
        props: {
            recommend: {
                type: Object,
                default: function () {
                    return {};
                }
            }
        },
        template: html
    };
});