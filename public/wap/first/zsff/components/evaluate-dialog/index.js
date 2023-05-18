define([
    'text!./index.html',
    'css!./index.css'
], function(html) {
    return {
        filters: {
            rateCovert: function (value) {
                return ['非常差', '差', '一般', '好', '非常好'][value - 1];
            }
        },
        props: {
            dialogShow: {
                type: Boolean,
                default: false
            },
            rateValue: {
                type: Number,
                default: 5
            },
            imageList: {
                type: Array,
                default: function () {
                    return [];
                }
            }
        },
        data: function () {
            return {
                textHeight: '',
                textValue: ''
            };
        },
        watch: {
            textValue: {
                handler: function () {
                    this.$nextTick(this.textResize);
                },
                immediate: true
            }
        },
        methods: {
            textResize: function () {
                this.textHeight = 'auto';
                this.$nextTick(function () {
                    this.textHeight = this.$refs.textarea.scrollHeight + 'px'; 
                });
            },
            rateChange: function (value) {
                this.$emit('rate-change', value);
            },
            imageUpload: function (event) {
                var files = event.target.files;
                if (!files.length) {
                    return;
                }
                this.$emit('image-upload', files[0]);
            },
            imageDelete: function (index) {
                this.$emit('image-delete', index);
            },
            evaluateSubmit: function () {
                this.$emit('evaluate-submit', this.textValue);
            }
        },
        template: html
    };
});