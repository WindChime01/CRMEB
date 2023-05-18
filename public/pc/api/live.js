define([
    'scripts/request'
], function (request) {
    return {
        /**
         * 直播间信息
         * @param {object} params 
         * @returns 
         */
        live_studio_index: function (params) {
            return request({
                url: '/live/live_studio_index',
                params: params
            });
        },
        /**
         * 助教评论
         * @param {object} params 
         * @returns 
         */
        get_comment_list: function (params) {
            return request({
                url: '/live/get_comment_list',
                params: params
            });
        },
        /**
         * 讲师、主教、普通人评论
         * @param {object} params 
         * @returns 
         */
        get_open_comment_list: function (params) {
            return request({
                url: '/live/get_open_comment_list',
                params: params
            });
        },
        /**
         * 直播间录制的内容
         * @param {object} params 
         * @returns 
         */
        get_live_record_list: function (params) {
            return request({
                url: '/live/get_live_record_list',
                params: params
            });
        },
        /**
         * 礼物
         * @returns 
         */
        live_gift_list: function () {
            return request({
                url: '/live/live_gift_list'
            });
        },
        /**
         * 打赏
         * @param {object} data 
         * @returns 
         */
        live_reward: function (data) {
            return request({
                url: '/live/live_reward',
                method: 'post',
                data: data
            });
        },
        /**
         * 带货商品
         * @param {object} params 
         * @returns 
         */
        live_goods_list: function (params) {
            return request({
                url: '/live/live_goods_list',
                params: params
            });
        },
        /**
         * 礼物排行榜
         * @param {object} data 
         * @returns 
         */
        get_live_reward: function (data) {
            return request({
                url: '/live/get_live_reward',
                method: 'post',
                data: data
            });
        }
    };
});