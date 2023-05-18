define([
    'api/auth',
    'text!./index.html',
    'css!./index.css'
], function(authApi, html) {
    return {
        props: {
            isLogin: {
                type: Boolean,
                default: false
            }
        },
        data: function () {
            return {
                page: 1,
                limit: 16,
                count: 0,
                specialList: [],
                finished: false
            };
        },
        watch: {
            isLogin: function (value) {
                if (value) {
                    this.my_special_list();
                }
            }
        },
        methods: {
            // 课程列表
            my_special_list: function () {
                var vm = this;
                authApi.my_special_list({
                    page: this.page,
                    limit: this.limit
                }).then(function (res) {
                    var data = res.data;
                    vm.count = data.count;
                    vm.specialList = data.list;
                    vm.finished = vm.limit > data.list.length;
                }).catch(function (err) {
                    vm.$message.error(err.msg);
                });
            }
        },
        template: html
    };
});