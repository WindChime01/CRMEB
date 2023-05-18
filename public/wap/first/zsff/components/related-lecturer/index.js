define([
    'require',
    'helper',
    'text!./index.html',
    'css!./index.css'
], function(require, $h, html) {
    return {
        props: {
            lecturer: Object
        },
        data: function () {
            return {

            };
        },
        methods: {
            goLecture: function () {
                window.location.assign($h.U({
                    c: 'merchant',
                    a: 'teacher_detail',
                    q: {
                        id: this.lecturer.id
                    }
                }));
            }
        },
        template: html
    };
});