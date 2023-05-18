define([
    'text!./index.html',
    'css!./index.css'
], function(html) {
    return {
        props: {
            href: {
                type: String,
                default: ''
            }
        },
        template: html
    };
});