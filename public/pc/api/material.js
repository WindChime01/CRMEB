define([
    'scripts/request'
], function(request) {
    return {
        /**
         * 资料分类
         * @returns 
         */
        get_material_cate: function () {
            return request({
                url: '/material/get_material_cate'
            });
        },
        /**
         * 资料列表
         * @param {*} data 
         * @returns 
         */
        get_material_list: function (data) {
            return request({
                url: '/material/get_material_list',
                method: 'post',
                data: data
            });
        },
        /**
         * 资料详情
         * @param {*} params 
         * @returns 
         */
        get_data_details: function (params) {
            return request({
                url: '/material/get_data_details',
                params: params
            });
        },
        /**
         * 资料收藏
         * @param {*} params 
         * @returns 
         */
        collect: function (params) {
            return request({
                url: '/material/collect',
                params: params
            });
        },
        /**
         * 增加下载记录
         * @param {*} params 
         * @returns 
         */
        user_download: function (params) {
            return request({
                url: '/material/user_download',
                params: params
            });
        },
        /**
         * 获取下载链接
         * @param {*} params 
         * @returns 
         */
        get_data_download_link: function (params) {
            return request({
                url: '/material/get_data_download_link',
                params: params
            });
        }
    };
});