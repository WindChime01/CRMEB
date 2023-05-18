define([
    'text!./index.html',
    'css!./index.css'
], function(html) {
    return {
        props: {
            materialList: {
                type: Array,
                default: function () {
                    return [];
                }
            },
            typeSetting: {
                type: Number,
                default: 2
            },
            allLink: {
                type: String,
                default: 'javascript:'
            },
            cellLink: {
                type: String,
                default: 'javascript:'
            },
            materialTitle: {
                type: String,
                default: '资料下载'
            }
        },
        template: html
    };
});