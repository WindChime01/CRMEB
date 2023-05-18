define([
    'scripts/request'
], function(request) {
    return {
        /**
         * 获取活动列表
         * @param {*} params 
         * @returns 
         */
        activityList: function (params) {
            return request({
                url: '/activity/activityList',
                params: params
            });
        },
        /**
         * 活动报名
         * @param {*} params 
         * @returns 
         */
        activity_details: function (params) {
            return request({
                url: '/activity/activity_details',
                params: params
            });
        },
        getActivityEventData: function (params) {
            return request({
                url: '/activity/getActivityEventData',
                params: params
            });
        },
        /**
         * 获取活动人数
         * @param {*} params 
         * @returns 
         */
        getActivityEventPrice: function (params) {
            return request({
                url: '/activity/getActivityEventPrice',
                params: params
            });
        },
        /**
         * 用户报名活动列表
         * @param {*} params 
         * @returns 
         */
        activitySignInList: function (params) {
            return request({
                url: '/activity/activitySignInList',
                params: params
            });
        },
        /**
         * 相关活动
         * @param {*} params 
         * @returns 
         */
        relatedActivities: function (params) {
            return request({
                url: '/activity/relatedActivities',
                params: params
            });
        }
    };
});