define([
    'scripts/request'
], function (request) {
    return {
        /**
         * 用户信息
         * @returns 
         */
        user_info: function () {
            return request({
                url: '/auth_api/user_info'
            });
        },
        /**
         * 上传图片到阿里云
         * @param {*} data 
         * @returns 
         */
        upload: function (data) {
            return request({
                url: '/auth_api/upload',
                method: 'post',
                data: data
            });
        },
        // 验证码
        code: function (data) {
            return request({
                url: '/auth_api/code',
                method: 'post',
                data: data
            });
        },
        /**
         * 新闻
         * @returns 
         */
        get_article_unifiend_list: function (params) {
            return request({
                url: '/auth_api/get_article_unifiend_list',
                params: params
            });
        },
        /**
         * 会员中心权益、说明
         * @param {*} data 
         * @returns 
         */
        merber_data: function () {
            return request({
                url: '/auth_api/merber_data'
            });
        },
        /**
         * 会员套餐
         * @returns 
         */
        member_ship_lists: function () {
            return request({
                url: '/auth_api/member_ship_lists'
            });
        },
        /**
         * 支付
         * @param {*} data 
         * @returns 
         */
        create_order: function (data) {
            return request({
                url: '/auth_api/create_order',
                method: 'post',
                data: data
            });
        },
        /**
         * 会员卡激活
         * @param {*} data 
         * @returns 
         */
        confirm_activation: function (data) {
            return request({
                url: '/auth_api/confirm_activation',
                method: 'post',
                data: data
            });
        },
        /**
         * 我的金币信息
         * @returns 
         */
        get_gold_coins: function () {
            return request({
                url: '/auth_api/get_gold_coins'
            });
        },
        /**
         * 金币明细
         * @param {*} params 
         * @returns 
         */
        user_gold_num_list: function (params) {
            return request({
                url: '/auth_api/user_gold_num_list',
                params: params
            });
        },
        /**
         * 余额信息
         * @returns 
         */
        get_user_balance: function () {
            return request({
                url: '/auth_api/get_user_balance'
            });
        },
        /**
         * 余额明细
         * @param {*} params 
         * @returns 
         */
        get_user_balance_list: function (params) {
            return request({
                url: '/auth_api/get_user_balance_list',
                params: params
            });
        },
        /**
         * 我购买的课程
         * @param {*} params 
         * @returns 
         */
        my_special_list: function (params) {
            return request({
                url: '/auth_api/my_special_list',
                params: params
            });
        },
        /**
         * 我的资料
         * @param {*} params 
         * @returns 
         */
        my_material_list: function (params) {
            return request({
                url: '/auth_api/my_material_list',
                params: params
            });
        },
        /**
         * 我收藏的课程、资料
         * @param {*} params 
         * @returns 
         */
        get_grade_list: function (params) {
            return request({
                url: '/auth_api/get_grade_list',
                params: params
            });
        },
        /**
         * 讲师详情
         * @param {*} params 
         * @returns 
         */
        lecturer_details: function (params) {
            return request({
                url: '/auth_api/lecturer_details',
                params: params
            });
        },
        /**
         * 讲师的专题
         * @param {object} data 
         * @returns 
         */
        lecturer_special_list: function (data) {
            return request({
                url: '/auth_api/lecturer_special_list',
                method: 'post',
                data: data
            });
        },
        /**
         * 扫码支付回调
         * @param {*} params 
         * @returns 
         */
        testing_order_state: function (params) {
            return request({
                url: '/auth_api/testing_order_state',
                params: params
            });
        },
        /**
         * 详情页讲师信息
         * @param {*} params 
         * @returns 
         */
        getLecturer: function (params) {
            return request({
                url: '/auth_api/getLecturer',
                params: params
            });
        }
    };
});