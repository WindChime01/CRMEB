define([
    'api/auth',
    'api/special',
    'api/material',
    'text!./index.html',
    'css!./index.css'
], function(authApi, specialApi, materialApi, html) {
    return {
        props: {
            activeName: {
                type: String,
                default: 'favor'
            },
            isLogin: {
                type: Boolean,
                default: false
            }
        },
        data: function () {
            return {
                page1: 1,
                page2: 1,
                limit: 16,
                active: '0',
                list1: [],
                list2: [],
                finished1: false,
                finished2: false,
                count1: 0,
                count2: 0
            };
        },
        watch: {
            isLogin: function (value) {
                if (value) {
                    this.get_grade_list1();
                    this.get_grade_list2();
                }
            }
        },
        methods: {
            // 课程
            get_grade_list1: function () {
                var vm = this;
                authApi.get_grade_list({
                    page: this.page1,
                    limit: this.limit,
                    active: 0
                }).then(function (res) {
                    var data = res.data;
                    vm.count1 = data.count;
                    vm.list1 = data.list;
                    // vm.finished1 = vm.limit > data.list.length;
                }).catch(function (err) {
                    vm.$message.error(err.msg);
                });
            },
            // 资料
            get_grade_list2: function () {
                var vm = this;
                authApi.get_grade_list({
                    page: this.page2,
                    limit: this.limit,
                    active: 1
                }).then(function (res) {
                    var data = res.data;
                    vm.count2 = data.count;
                    vm.list2 = data.list;
                    // vm.finished2 = vm.limit > data.list.length;
                }).catch(function (err) {
                    vm.$message.error(err.msg);
                });
            },
            // 取消课程收藏
            specialCollect: function (id) {
                var vm = this;
                specialApi.collect({
                    id: id
                }).then(function () {
                    vm.$message.success('取消收藏成功');
                    if (!(vm.list1.length - 1)) {
                        if (vm.page1 > 1) {
                            vm.page1--;
                        }
                    }
                    vm.get_grade_list1();
                }).catch(function (err) {
                    vm.$message.error(err.msg);
                });
            },
            // 取消资料收藏
            materialCollect: function (id) {
                var vm = this;
                materialApi.collect({
                    id: id
                }).then(function () {
                    vm.$message.success('取消收藏成功');
                    if (!(vm.list2.length - 1)) {
                        if (vm.page2 > 1) {
                            vm.page2--;
                        }
                    }
                    vm.get_grade_list2();
                }).catch(function (err) {
                    vm.$message.error(err.msg);
                });
            },
            tabClick: function (params) {
            }
        },
        template: html
    };
});