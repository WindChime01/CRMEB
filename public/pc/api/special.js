define([
    'scripts/request'
], function (request) {
    return {
        /**
         * 专题详情
         * @param {object} params 
         * @returns 
         */
        get_special_details: function (params) {
            return request({
                url: '/special/get_special_details',
                params: params
            });
        },
        /**
         * 轻专题详情
         * @param {*} params 
         * @returns 
         */
        get_special_single_details: function (params) {
            return request({
                url: '/special/get_special_single_details',
                params: params
            });
        },
        /**
         * 专题的课程数量
         * @param {object} params 
         * @returns 
         */
        numberCourses: function (params) {
            return request({
                url: '/special/numberCourses',
                params: params
            });
        },
        /**
         * 专题关联的资料
         * @param {object} params 
         * @returns 
         */
        special_data_download: function (params) {
            return request({
                url: '/special/special_data_download',
                params: params
            });
        },
        /**
         * 专题页推荐课程
         * @param {object} params 
         * @returns 
         */
        recommended_courses: function (params) {
            return request({
                url: '/special/recommended_courses',
                params: params
            });
        },
        /**
         * 专题的素材
         * @param {object} params 
         * @returns 
         */
        get_course_list: function (params) {
            return request({
                url: '/special/get_course_list',
                params: params
            });
        },
        /**
         * 专题页套餐
         * @param {object} params 
         * @returns 
         */
        get_cloumn_task: function (params) {
            return request({
                url: '/special/get_cloumn_task',
                params: params
            });
        },
        /**
         * 评价概况
         * @param {object} params 
         * @returns 
         */
        special_reply_data: function (params) {
            return request({
                url: '/special/special_reply_data',
                params: params
            });
        },
        /**
         * 评价列表
         * @param {object} params 
         * @returns 
         */
        special_reply_list: function (params) {
            return request({
                url: '/special/special_reply_list',
                params: params
            });
        },
        /**
         * 评价专题
         * @param {string} special_id 
         * @param {object} data 
         * @returns 
         */
        user_comment_special: function (special_id, data) {
            return request({
                url: '/special/user_comment_special?special_id=' + special_id,
                method: 'post',
                data: data
            });
        },
        /**
         * 专题分类
         * @returns 
         */
        get_grade_cate: function () {
            return request({
                url: '/special/get_grade_cate'
            });
        },
        /**
         * 分类
         * @param {object} params 
         * @returns 
         */
         get_all_special_cate: function (params) {
            return request({
                url: '/special/get_all_special_cate',
                params: params
            });
        },
        /**
         * 专题分类列表
         * @param {object} params 
         * @returns 
         */
        get_special_list: function (params) {
            return request({
                url: '/special/get_special_list',
                params: params
            });
        },
        /**
         * 素材详情
         * @param {object} data 
         * @returns 
         */
        getTaskInfo: function (data) {
            return request({
                url: '/special/getTaskInfo',
                method: 'post',
                data: data
            });
        },
        /**
         * 视频上传凭证
         * @param {object} params 
         * @returns 
         */
        get_video_playback_credentials: function (params) {
            return request({
                url: '/special/get_video_playback_credentials',
                params: params
            });
        },
        /**
         * 所有直播课
         * @param {object} params 
         * @returns 
         */
        get_live_special_list: function (params) {
            return request({
                url: '/special/get_live_special_list',
                params: params
            });
        },
        /**
         * 专题收藏
         * @param {*} params 
         * @returns 
         */
        collect: function (params) {
            return request({
                url: '/special/collect',
                params: params
            });
        },
        /**
         * 记录观看素材时间
         * @param {*} data 
         * @returns 
         */
        viewing: function (data) {
            return request({
                url: '/special/viewing',
                method: 'post',
                data: data
            });
        },
        /**
         * 记录专题浏览人数
         * @param {*} params 
         * @returns 
         */
        addLearningRecords: function (params) {
            return request({
                url: '/special/addLearningRecords',
                params: params
            });
        },
        /**
         * 提交兑换码
         * @param {*} data 
         * @returns 
         */
        exchange_submit: function (data) {
            return request({
                url: '/special/exchange_submit',
                method: 'post',
                data: data
            });
        },
        /**
         * 是否可以播放
         * @param {*} params 
         * @returns 
         */
        get_task_link: function (params) {
            return request({
                url: '/special/get_task_link',
                params: params
            });
        },
        /**
         * 播放数量增加
         * @param {*} params 
         * @returns 
         */
        play_num: function (params) {
            return request({
                url: '/special/play_num',
                params: params
            });
        }
    };
});