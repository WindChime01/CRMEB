define([
    'text!./index.html'
], function(html) {
    return {
        props: {
            visible: {
                type: Boolean,
                default: false
            }
        },
        methods: {
            guideClick: function () {
                this.$emit('update:visible', false);
            }
        },
        template: html
    };
});