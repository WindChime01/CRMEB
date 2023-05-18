define([
    'swiper',
    'text!./index.html',
    'css!./index.css'
], function(Swiper, html) {
    return {
        props: {
            advertList: {
                type: Array,
                default: function () {
                    return [];
                }
            }
        },
        data: function () {
            return {
                swiperOptions: {
                    autoplay: true,
                    loop: true,
                    pagination: {
                        el: '.advert-pagination'
                    },
                    uniqueNavElements: false
                }
            };
        },
        mounted: function () {
            this.$nextTick(function () {
                this.swiper7 = new Swiper('#swiper7', this.swiperOptions);
            });
        },
        methods: {
            advertClick: function () {
                var url = this.swiper7.clickedSlide.dataset.url;
                if (url.indexOf('http') === -1 || url.indexOf('http')) {
                    return;
                }
                window.location = url;
            }
        },
        template: html
    };
});