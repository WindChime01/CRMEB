define([
    'scripts/request'
], function (request) {
    return {
        /**
         * 上传
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
        /**
         * 修改头像、昵称
         * @param {*} data 
         * @returns 
         */
        saveUserInfo: function (data) {
            return request({
                url: '/my/save_user_info',
                method: 'post',
                data: data
            });
        },
        /**
         * 修改手机号-验证旧手机号
         * @param {*} params 
         * @returns 
         */
        validate_code: function (params) {
            return request({
                url: '/my/validate_code',
                params: params
            });
        },
        /**
         * 修改手机号-保存新手机号
         * @param {*} params 
         * @returns 
         */
        save_phone: function (params) {
            return request({
                url: '/my/save_phone',
                params: params
            });
        }
    };
});