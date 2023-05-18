define([
    'api/activity',
    'moment',
    'text!./index.html',
    'css!./index.css',
    'css!../../../../wap/first/zsff/iconfont/iconfont.css',
], function(activityApi, moment, html) {
    return {
        props: {
            activeName: {
                type: String,
                default: 'activity'
            },
            isLogin: {
                type: Boolean,
                default: false
            }
        },
        filters: {
            timeFormat: function (value) {
                return moment(value * 1000).format('YYYY-MM-DD HH:mm');
            }
        },
        data: function () {
            return {
                active: '0',
                limit: 10,
                page0: 1,
                page1: 1,
                page2: 1,
                total0: 10,
                total1: 10,
                total2: 10,
                list0: [],
                list1: [],
                list2: []
            };
        },
        watch: {
            isLogin: function (val) {
                if (val) {
                    this.activitySignInList0();
                    this.activitySignInList1();
                    this.activitySignInList2();
                }
            }
        },
        methods: {
            activitySignInList0: function () {
                var vm = this;
                activityApi.activitySignInList({
                    page: this.page0,
                    limit: this.limit,
                    navActive: 0
                }).then(function (res) {
                    var data = res.data;
                    vm.list0 = data.list;
                    vm.total0 = data.count;
                });
            },
            activitySignInList1: function () {
                var vm = this;
                activityApi.activitySignInList({
                    page: this.page1,
                    limit: this.limit,
                    navActive: 1
                }).then(function (res) {
                    var data = res.data;
                    vm.list1 = data.list;
                    vm.total1 = data.count;
                });
            },
            activitySignInList2: function () {
                var vm = this;
                activityApi.activitySignInList({
                    page: this.page2,
                    limit: this.limit,
                    navActive: 2
                }).then(function (res) {
                    var data = res.data;
                    vm.list2 = data.list;
                    vm.total2 = data.count;
                });
            },
            handleClick: function () {

            }
        },
        template: html
    };
});