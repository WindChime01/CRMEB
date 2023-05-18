define([
    'require',
    'helper',
    'text!./index.html',
    'css!./index.css',
], function(require, $h, html) {
    return {
        props: ['show', 'status', 'fail'],
        data: function () {
            return {
                images: [
                    require.toUrl('./images/1.png'),
                    require.toUrl('./images/2.png'),
                    require.toUrl('./images/3.png')
                ],
                page: window.location.href
            };
        },
        methods: {
            goApply: function () {
                window.location.assign($h.U({
                    c: 'merchant',
                    a: 'index'
                }));
            },
            goBack: function () {
                window.history.back();
            }
        },
        template: html
    };
});