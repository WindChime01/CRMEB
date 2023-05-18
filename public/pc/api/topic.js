define([
    'scripts/request'
], function (request) {
    return {
        /**
         * 试卷分类
         * @param {*} params 
         * @returns 
         */
        testPaperCate: function (params) {
            return request({
                url: '/topic/testPaperCate',
                params: params
            });
        },
        /**
         * 试卷列表
         * @param {*} params 
         * @returns 
         */
        practiceList: function (type, data) {
            return request({
                url: '/topic/practiceList?type=' + type,
                method: 'post',
                data: data
            });
        },
        /**
         * 试卷信息
         * @param {*} params 
         * @returns 
         */
        testPaperDetails: function (params) {
            return request({
                url: '/topic/testPaperDetails',
                params: params
            });
        },
        /**
         * 试卷状态
         * @param {*} params 
         * @returns 
         */
        situationRecord: function (params) {
            return request({
                url: '/topic/situationRecord',
                params: params
            });
        },
        /**
         * 开始答题
         * @param {*} params 
         * @returns 
         */
        userAnswer: function (params) {
            return request({
                url: '/topic/userAnswer',
                params: params
            });
        },
        /**
         * 再次答题
         * @param {*} params 
         * @returns 
         */
        takeTheTestAgain: function (params) {
            return request({
                url: '/topic/takeTheTestAgain',
                params: params
            });
        },
        /**
         * 继续答题
         * @param {*} params 
         * @returns 
         */
        continueAnswer: function (params) {
            return request({
                url: '/topic/continueAnswer',
                params: params
            });
        },
        /**
         * 试卷内容
         * @param {*} params 
         * @returns 
         */
        testPaperQuestions: function (params) {
            return request({
                url: '/topic/testPaperQuestions',
                params: params
            });
        },
        /**
         * 提交单题
         * @param {*} data 
         * @returns 
         */
        submitQuestions: function (data) {
            return request({
                url: '/topic/submitQuestions',
                method: 'post',
                data: data
            });
        },
        /**
         * 答题卡
         * @param {*} params 
         * @returns 
         */
        answerSheet: function (params) {
            return request({
                url: '/topic/answerSheet',
                params: params
            });
        },
        /**
         * 提交试卷
         * @param {*} data 
         * @returns 
         */
        submitTestPaper: function (data) {
            return request({
                url: '/topic/submitTestPaper',
                method: 'post',
                data: data
            });
        },
        /**
         * 考试结果
         * @param {*} params 
         * @returns 
         */
        examinationResults: function (params) {
            return request({
                url: '/topic/examinationResults',
                params: params
            });
        },
        /**
         * 我的试卷
         * @param {*} data 
         * @returns 
         */
        myTestPaper: function (data) {
            return request({
                url: '/topic/myTestPaper',
                method: 'post',
                data: data
            });
        },
        /**
         * 我的错题
         * @param {*} data 
         * @returns 
         */
        userWrongBank: function (data) {
            return request({
                url: '/topic/userWrongBank',
                method: 'post',
                data: data
            });
        },
        /**
         * 我的错题id
         * @param {*} data 
         * @returns 
         */
        userWrongBankIdArr: function (data) {
            return request({
                url: '/topic/userWrongBankIdArr',
                method: 'post',
                data: data
            });
        },
        /**
         * 单个错题
         * @param {*} params 
         * @returns 
         */
        oneWrongBank: function (params) {
            return request({
                url: '/topic/oneWrongBank',
                params: params
            });
        },
        /**
         * 掌握错题
         * @param {*} data 
         * @returns 
         */
        submitWrongBank: function (data) {
            return request({
                url: '/topic/submitWrongBank',
                method: 'post',
                data: data
            });
        },
        /**
         * 删除错题
         * @param {*} params 
         * @returns 
         */
        delWrongBank: function (params) {
            return request({
                url: '/topic/delWrongBank',
                params: params
            });
        },
        /**
         * 专题的练习和考试
         * @param {*} data 
         * @returns 
         */
        specialTestPaper: function (data) {
            return request({
                url: '/topic/specialTestPaper',
                method: 'post',
                data: data
            });
        },
    };
});