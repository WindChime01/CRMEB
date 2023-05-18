define([
    'require',
    'scripts/request',
], function (require, request) {
    'use strict';
    return {
        /**
         * 讲师名下课程
         * @param {*} data 
         * @returns 
         */
        lecturer_special_list: function (data) {
            return request({
                url: '/merchant/lecturer_special_list',
                method: 'post',
                data: data
            });
        },
        /**
         * 讲师名下资料
         * @param {*} data 
         * @returns 
         */
        lecturer_download_list: function (data) {
            return request({
                url: '/merchant/lecturer_download_list',
                method: 'post',
                data: data
            });
        },
        /**
         * 讲师名下活动
         * @param {*} data 
         * @returns 
         */
        lecturer_event_list: function (data) {
            return request({
                url: '/merchant/lecturer_event_list',
                method: 'post',
                data: data
            });
        },
        /**
         * 讲师申请
         * @param {*} data 
         * @returns 
         */
        apply: function (data) {
            return request({
                url: '/merchant/apply',
                method: 'post',
                data: data
            });
        },
        /**
         * 检查是否提交申请
         * @returns 
         */
        is_apply: function () {
            return request({
                url: '/merchant/is_apply'
            });
        },
        /**
         * 获得申请数据
         * @returns 
         */
        apply_data: function () {
            return request({
                url: '/merchant/apply_data'
            });
        },
        /**
         * 讲师列表
         * @param {*} params 
         * @returns 
         */
        get_lecturer_list: function (params) {
            return request({
                url: '/merchant/get_lecturer_list',
                params: params
            });
        },
        /**
         * 讲师入驻协议
         * @returns 
         */
        lecturer_agree: function () {
            return request({
                url: '/merchant/lecturer_agree'
            });
        },
        /**
         * 是否关注
         * @param {*} params 
         * @returns 
         */
        is_follow: function (params) {
            return request({
                url: '/merchant/is_follow',
                params: params
            });
        },
        /**
         * 关注、取消关注
         * @param {*} params 
         * @returns 
         */
        user_follow: function (params) {
            return request({
                url: '/merchant/user_follow',
                params: params
            });
        },
        /**
         * 我的关注
         * @param {*} params 
         * @returns 
         */
        get_user_follow_list: function (params) {
            return request({
                url: '/merchant/get_user_follow_list',
                params: params
            });
        },
        /**
         * 讲师的练习和考试
         * @param {*} data 
         * @returns 
         */
        lecturer_test_list: function (data) {
            return request({
                url: '/merchant/lecturer_test_list',
                method: 'post',
                data: data
            });
        },
    };
});