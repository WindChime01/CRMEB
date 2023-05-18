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
                total: 0,
                materialList: [],
                finished: false
            };
        },
        watch: {
            isLogin: function (value) {
                if (value) {
                    this.my_material_list();
                }
            }
        },
        methods: {
            // 资料列表
            my_material_list: function () {
                var vm = this;
                authApi.my_material_list({
                    page: this.page,
                    limit: this.limit
                }).then(function (res) {
                    var data = res.data;
                    vm.total = data.count;
                    vm.materialList = data.data;
                    vm.finished = vm.limit > data.data.length;
                });
            }
        },
        template: html
    };
});