define([
    'scripts/request'
], function (request) {
    return {
        /**
         * 是否登录
         * @returns 
         */
        user_login: function () {
            return request({
                url: '/index/login_user'
            });
        },
        /**
         * 首页固定数据
         * @returns
         */
        index_data: function () {
            return request({
                url: '/index/index_data'
            });
        },
        /**
         * 首页推荐数据
         * @returns
         */
        get_content_recommend: function () {
            return request({
                url: '/index/get_content_recommend'
            });
        },
        courseRanking: function () {
            return request({
                url: '/auth_api/get_course_ranking'
            });
        },
        newCourseFirst: function () {
            return request({
                url: '/auth_api/get_new_course_first'
            });
        },
        articleList: function (params) {
            return request({
                url: '/auth_api/get_article_unifiend_list',
                params: params
            });
        },
        get_good_class_recommend: function () {
            return request({
                url: '/auth_api/get_good_class_recommend'
            });
        },
        /**
         * 付款页数据
         * @param {*} params
         * @returns
         */
        pay_data: function (params) {
            return request({
                url: '/index/pay_data',
                params: params
            });
        },
        get_unifiend_list: function (params) {
            return request({
                url: '/index/get_unifiend_list',
                params: params
            });
        }
    };
});