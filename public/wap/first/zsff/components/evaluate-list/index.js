define([
    'moment',
    'text!./index.html',
    'css!./index.css'
], function(moment, html) {
    return {
        props: {
            evaluateList: {
                type: Array,
                default: function () {
                    return [];
                }
            }
        },
        filters: {
            convertName: function (value) {
                return value.replace(/^(.).+(.)$/g, '$1**$2');
            },
            convertTime: function (value) {
                return moment(value).fromNow();
            }
        },
        methods: {},
        template: html
    };
});