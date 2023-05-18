define([
    'text!./index.html',
    'css!./index.css'
], function(html) {
    return {
        props: {
            obj: {
                type: Object,
                default: function () {
                    return {};
                }
            }
        },
        template: html
    };
});