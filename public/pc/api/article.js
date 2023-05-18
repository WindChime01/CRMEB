define([
    'scripts/request'
], function(request) {
    return {
        /**
         * 新闻分类
         * @returns 
         */
        get_article_cate: function () {
            return request({
                url: '/article/get_article_cate'
            });
        },
        /**
         * 新闻列表
         * @param {*} params 
         * @returns 
         */
        get_article_list: function (params) {
            return request({
                url: '/article/get_article_list',
                params: params
            });
        },
        /**
         * 新闻广告
         * @returns 
         */
        news_data: function () {
            return request({
                url: '/article/news_data'
            });
        },
        /**
         * 资讯详情
         * @param {*} params 
         * @returns 
         */
        article_details: function (params) {
            return request({
                url: '/article/article_details',
                params: params
            });
        }
    };
});