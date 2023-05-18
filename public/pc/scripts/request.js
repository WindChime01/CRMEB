define([
    'axios',
    'ELEMENT'
], function (axios, ELEMENT) {
    var instance = axios.create({
        baseURL: window.location.origin + '/web',
        timeout: 10000,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        withCredentials: true
    });
    var loadingInstance = null;
    instance.interceptors.request.use(function (config) {
        loadingInstance = ELEMENT.Loading.service({
            background: 'transparent'
        });
        return config;
    }, function (error) {
        return Promise.reject(error);
    });
    instance.interceptors.response.use(function (response) {
        loadingInstance.close();
        if (response.data.code === 200) {
            return response.data;
        }
        return Promise.reject(response.data);
    }, function (error) {
        return Promise.reject(error);
    });
    return instance;
});