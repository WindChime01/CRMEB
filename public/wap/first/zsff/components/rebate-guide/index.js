define([
    'text!./index.html',
    'css!./index.css'
], function(html) {
    return {
        props: ['rebateMoney'],
        data: function () {
            return {
                top: 0,
                ticking: false,
                innerHeight: window.innerHeight,
                maxHeight: 0
            };
        },
        mounted: function () {
            this.$nextTick(function () {
                this.top = this.maxHeight = this.innerHeight - this.$refs.rebate.offsetHeight - this.$refs.close.offsetHeight + this.$refs.close.offsetTop;
            });
        },
        methods: {
            rebateAction: function (value) {
                this.$emit('rebate-action', value);
            },
            touchMove: function (event) {
                if (!this.ticking) {
                    window.requestAnimationFrame(function () {
                        this.ticking = false;
                        if (event.changedTouches[0].clientY < this.$refs.close.offsetHeight - this.$refs.close.offsetTop) {
                            this.top = this.$refs.close.offsetHeight - this.$refs.close.offsetTop;
                        } else if (event.changedTouches[0].clientY > this.maxHeight) {
                            this.top = this.maxHeight;
                        } else {
                            this.top = event.changedTouches[0].clientY;
                        }
                    }.bind(this));
                }
                this.ticking = true;
            }
        },
        template: html
    };
});