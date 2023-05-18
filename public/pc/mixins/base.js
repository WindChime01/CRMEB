define([
    'api/home',
    'api/auth',
    'api/public',
    'api/special'
], function (homeApi, authApi, publicApi, specialApi) {
    return {
        provide: function () {
            return {
                getUserLogin: this.getUserLogin,
                getUserInfo: this.getUserInfo
            };
        },
        data: function () {
            return {
                userInfo: null,
                publicData: null,
                agreeContent: null,
                agreeVisible: false,
                isLogin: false,
                currentRoute: window.location.pathname,
                serviceUrl: '',
                serviceQuery: {
                    token: '',
                    nickName: '',
                    phone: '',
                    avatar: '',
                    kefu_id: 0
                }
            };
        },
        computed: {
            serviceSrc: function () {
                var query = [];
                for (var name in this.serviceQuery) {
                    if (Object.hasOwnProperty.call(this.serviceQuery, name)) {
                        query.push(name + '=' + this.serviceQuery[name]);
                    }
                }
                return this.serviceUrl + '/chat/index?' + query.join('&');
            }
        },
        watch: {
            serviceSrc: function (value) {
                this.setPublicData({serviceSrc: value});
            }
        },
        router: this.routes,
        created: function () {
            this.getUserLogin();
            this.getUserInfo();
            this.getPublicData();
            this.getHostSearch();
            this.getHomeNavigation();
            this.getGradeCate();
            this.getAgreeContent();
            this.createWebsiteStatistics();
            this.setPublicData({serviceSrc: this.serviceSrc});
            this.getCopyright();
        },
        methods: {
            get_site_service_phone: function (mer_id) {
                publicApi.get_site_service_phone({mer_id: mer_id}).then(function (res) {
                    this.setPublicData({sitePhone: Array.isArray(res.data) ? null : res.data});
                }.bind(this));
            },
            get_kefu_id: function (mer_id) {
                publicApi.get_kefu_id({mer_id: mer_id}).then(function (res) {
                    this.serviceQuery.kefu_id = res.data.kefu_id;
                }.bind(this));
            },
            getUserLogin: function () {
                homeApi.user_login().then(function () {
                    this.isLogin = true;
                }.bind(this)).catch(function () {
                    this.isLogin = false;
                    if (window.location.pathname.indexOf('/web/my/index') != -1) {
                        window.location.replace(this.$router.home);
                    }
                }.bind(this));
            },
            getPublicData: function () {
                publicApi.public_data().then(function (res) {
                    var data = res.data;
                    this.setPublicData(data);
                    this.serviceUrl = data.service_url;
                    this.serviceQuery.token = data.kefu_token;
                }.bind(this));
            },
            getUserInfo: function () {
                authApi.user_info().then(function (res) {
                    this.userInfo = res.data;
                }.bind(this));
            },
            getHostSearch: function () {
                publicApi.get_host_search().then(function (res) {
                    this.setPublicData({host_search: res.data});
                }.bind(this));
            },
            getHomeNavigation: function () {
                publicApi.get_home_navigation().then(function (res) {
                    this.setPublicData({navList: res.data});
                }.bind(this));
            },
            getGradeCate: function () {
                specialApi.get_grade_cate().then(function (res) {
                    this.setPublicData({grade_cate: res.data});
                }.bind(this));
            },
            getAgreeContent: function () {
                publicApi.agree().then(function (res) {
                    this.agreeContent = res.data;
                }.bind(this));
            },
            setPublicData: function (obj) {
                this.publicData = Object.assign({}, this.publicData || {}, obj);
            },
            createWebsiteStatistics: function () {
                var request = new XMLHttpRequest();
                request.onreadystatechange = function () {
                    if (request.readyState === request.DONE) {
                        if (request.status === 200) {
                            var text = request.responseText;
                            if (text) {
                                var markStart = text.indexOf('<script>');
                                if (markStart !== -1) {
                                    text = text.slice(markStart + 8);
                                    var markEnd = text.indexOf('</script>');
                                    if (markEnd !== -1) {
                                        text = text.slice(0, markEnd);
                                    }
                                }
                                var scriptEl = document.createElement('script');
                                scriptEl.innerHTML = text;
                                document.body.appendChild(scriptEl);
                            }
                        }
                    }
                };
                request.open('GET', '../public_api/get_website_statistics');
                request.send();
            },
            getCopyright: function () {
                publicApi.getCopyright().then(function (res) {
                    this.setPublicData({
                        hasCopyright: true,
                        nncnL_crmeb_copyright: res.data.nncnL_crmeb_copyright
                    });
                }.bind(this));
            }
        }
    }
});