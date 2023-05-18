define([
    'require',
    'api/merchant',
    'text!./index.html',
    'css!./index.css'
], function (require, merchantApi, html) {
    return {
        props: {
            activeName: {
                type: String,
                default: ''
            }
        },
        data: function () {
            return {
                page: 1,
                limit: 16,
                total: 0,
                followList: [],
                finished: false
            };
        },
        watch: {
            activeName: function (value) {
                if (value === 'lecturer') {
                    this.getFollowList();
                }
            },
            page: function () {
                this.getFollowList();
            }
        },
        methods: {
            getFollowList: function () {
                var vm = this;
                merchantApi.get_user_follow_list({
                    page: this.page,
                    limit: this.limit
                }).then(function (res) {
                    var list = res.data.data;
                    list.forEach(function (item) {
                        item.label = JSON.parse(item.label);
                    });
                    vm.total = res.data.count;
                    vm.followList = list;
                    vm.finished = vm.limit > list.length;
                });
            },
            follow: function (item) {
                var vm = this;
                merchantApi.user_follow({
                    mer_id: item.mer_id,
                    is_follow: 0
                }).then(function (res) {
                    if (vm.followList.length > 1) {
                        vm.getFollowList();
                    } else {
                        if (vm.page > 1) {
                            vm.page -= 1
                            vm.getFollowList();
                        } else {
                            vm.followList = [];
                        }
                    }
                    vm.$message.success('移除成功');
                });
            }
        },
        template: html
    };
});