define([
    'scripts/request'
], function (request) {
    return {
        /**
         * 页面公共数据
         * @returns 
         */
        public_data: function () {
            return request({
                url: '/public_api/public_data'
            });
        },
        /**
         * 用户协议
         * @returns 
         */
        agree: function () {
            return request({
                url: '/public_api/agree'
            });
        },
        /**
         * 热搜词
         * @returns 
         */
        get_host_search: function () {
            return request({
                url: '/public_api/get_host_search'
            });
        },
        /**
         * 顶部导航
         * @returns 
         */
        get_home_navigation: function () {
            return request({
                url: '/public_api/get_home_navigation'
            });
        },
        /**
         * 获取客服id
         * @param {*} params 
         * @returns 
         */
        get_kefu_id: function (params) {
            return request({
                url: '/public_api/get_kefu_id',
                params: params
            });
        },
        /**
         * 讲师客服检查
         * @param {*} params 
         * @returns 
         */
        get_site_service_phone: function (params) {
            return request({
                url: '/public_api/get_site_service_phone',
                params: params
            });
        },
        getCopyright: function () {
            return request({
                url: '../admin/login/get_copyright'
            });
        },
    };
});