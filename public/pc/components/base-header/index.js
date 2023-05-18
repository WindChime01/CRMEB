define([
    'require',
    'store/index',
    'api/merchant',
    'scripts/util',
    'text!./index.html',
    'css!./index.css'
], function (require, store, merchantApi, util, html) {
    return {
        props: {
            publicData: {
                type: Object,
                default: function () {
                    return {};
                }
            },
            userInfo: {
                type: Object,
                default: function () {
                    return {
                        avatar: ''
                    };
                }
            }
        },
        data: function () {
            return {
                selected: 1,
                options: [
                    {
                        label: '专题',
                        value: 1
                    },
                    {
                        label: '资料',
                        value: 2
                    }
                ],
                searchValue: '',
                activeIndex: '1',
                active: 1,
                currentURL: window.location.pathname,
                URLSearch: window.location.search,
                categoryVisible: false,
                menuOn: -1,
                pageName: false,
                code_url: code_url,
                sharedState: store.state,
                applyStatus: null,
                applyVisible: false,
                failMessage: '',
                applyStatusIcon: [
                    require.toUrl('./images/1.png'),
                    require.toUrl('./images/2.png'),
                    require.toUrl('./images/2.png')
                ],
                application_switch: application_switch
            };
        },
        created: function () {
            for (var key in this.$router) {
                if (Object.hasOwnProperty.call(this.$router, key)) {
                    if (this.$router[key].indexOf(this.currentURL) !== -1) {
                        this.pageName = key;
                    }
                }
            }
            if (['material_list', 'material_detail', 'special_cate'].indexOf(this.pageName) !== -1) {
                this.setSearchValue();
            }
            if (['material_list', 'material_detail'].indexOf(this.pageName) !== -1) {
                this.selected = 2;
            }
            if (code_url) {
                this.code_url = code_url;
            }
        },
        methods: {
            goPage: function (page) {
                var vm = this;
                var params = '';
                // 判断路由结构
                if (Array.isArray(page)) {
                    if (page[1].constructor.toString().indexOf('Object') !== -1) {
                        params = Object.keys(page[1]).map(function (key) {
                            return key + '=' + page[1][key];
                        }).join('&');
                    }
                    page = page[0];
                }
                // 判断是否登录
                if (page !== 'home' && (!this.userInfo || !this.userInfo.uid)) {
                    return store.setLoginAction(true);
                }
                // 讲师申请状态
                if (page === 'teacher_apply') {
                    if (this.userInfo && this.userInfo.business) {
                        window.open('/merchant/index.html');
                        return false;
                    }
                    merchantApi.is_apply().then(function (res) {
                        if (res.data == null) {
                            window.location.assign(vm.$router[page] + (params ? '?' + params : ''));
                        } else {
                            if (res.data.status === null) {
                                window.location.assign(vm.$router[page] + (params ? '?' + params : ''));
                            } else {
                                if (res.data.status === 2) {
                                    window.location.assign(vm.$router[page] + (params ? '?' + params : ''));
                                } else {
                                    vm.applyStatus = res.data.status;
                                    vm.applyVisible = true;
                                    if (res.data.status === -1) {
                                        vm.failMessage = res.data.fail_message;
                                    }
                                }
                            }
                        }
                    });
                    return false;
                }
                window.location.assign(this.$router[page] + (params ? '?' + params : ''));
            },
            onSearch: function (hot) {
                if (!hot && !this.searchValue) {
                    return;
                }
                if (hot) {
                    this.searchValue = hot;
                }
                if (this.currentURL.includes('/web/special/special_cate')) {
                    if (this.selected === 1) {
                        this.$emit('submit-search', this.searchValue);
                    } else {
                        window.location.assign(this.$router.material_list + '?search=' + this.searchValue);
                    }
                } else if (this.currentURL.includes('/web/material/material_list')) {
                    if (this.selected === 2) {
                        this.$emit('submit-search', this.searchValue);
                    } else {
                        window.location.assign(this.$router.special_cate + '?search=' + this.searchValue);
                    }
                } else {
                    window.location.assign((this.selected === 1 ? this.$router.special_cate : this.$router.material_list) + '?search=' + this.searchValue);
                }
            },
            categoryMouseenter: function () {
                this.categoryMouse = true;
                this.categoryVisible = true;
            },
            categoryMouseleave: function () {
                this.categoryMouse = false;
                if (!(this.contentMouse || this.menuMouse)) {
                    this.menuOn = -1;
                    this.categoryVisible = false;
                }
            },
            menuMouseenter: function () {
                this.menuMouse = true;
            },
            menuMouseleave: function () {
                this.menuMouse = false;
                if (!(this.contentMouse || this.categoryMouse)) {
                    this.menuOn = -1;
                    this.categoryVisible = false;
                }
            },
            contentMouseenter: function () {
                this.contentMouse = true;
            },
            contentMouseleave: function () {
                this.contentMouse = false;
                if (!(this.menuMouse || this.categoryMouse)) {
                    this.menuOn = -1;
                    this.categoryVisible = false;
                }
            },
            setSearchValue: function () {
                var search = util.getParmas('search');
                if (search) {
                    this.searchValue = search;
                }
            },
            // 收藏本站
            addFavorite: function () {
                try {
                    window.external.addFavorite(window.location.origin + this.$router.home, this.publicData.site_name);
                } catch (error) {
                    try {
                        window.sidebar.addPanel(this.publicData.site_name, window.location.origin + this.$router.home, '');
                    } catch (error) {
                        this.$message('抱歉，您所使用的浏览器无法完成此操作，请使用Ctrl+D进行添加！');
                    }
                }
            },
            loginOpen: function () {
                store.setLoginAction(true);
            },
            goApply: function () {
                window.location.assign(this.$router.teacher_apply);
            }
        },
        template: html
    };
});
