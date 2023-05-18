define([
    'store/index',
    'api/auth',
    'api/login',
    'plugins/blueimp-md5/js/md5',
    'text!./index.html',
    'css!./index.css'
], function (store, authApi, loginApi, md5, html) {
    return {
        inject: ['getUserInfo'],
        props: {
            publicData: {
                type: Object,
                default: function () {
                    return {};
                }
            },
            agreeContent: {
                type: Object,
                default: function () {
                    return {};
                }
            }
        },
        data: function () {
            return {
                state: 3, // 1 注册，2 找回密码，3 登录
                type: 1,  // 1 账号登录，2 快速登录，3 扫码登录
                phone: '',
                code: '',
                pwd: '',
                agree: false,
                count: -1,
                TIME_COUNT: 60,
                scanCount: 0,
                scanTimer: null,
                qrcodeSrc: '',
                storeState: store.state
            };
        },
        watch: {
            'storeState.loginVisible': function (value) {
                this.$nextTick(function () {
                    var vm = this;
                    if (value) {
                        $('#captcha').slideVerify({
                            baseUrl: '/web/auth_api',
                            mode: 'pop',
                            containerId: 'getCode',
                            beforeCheck: function () {
                                var flag = true;
                                return flag
                            },
                            ready: function () { },
                            success: function (params) {
                                vm.getCode(params.captchaVerification);
                            },
                            error: function () { },
                            beforeShow: function () {
                                var flag = true;
                                if (!vm.phone) {
                                    flag = false;
                                    vm.$message.warning('请输入手机号');
                                } else if (!/^1[3456789]\d{9}$/.test(vm.phone)) {
                                    flag = false;
                                    vm.$message.warning('手机号错误');
                                }
                                return flag;
                            }
                        });
                    }
                });
            },
            state: function () {
                this.phone = '';
                this.code = '';
                this.pwd = '';
                this.agree = false;
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = null;
                    this.count = -1;
                }
            },
            type: function (val) {
                var vm = this;
                if (val == 3) {
                    loginApi.loginQrcode().catch(function (res) {
                        vm.qrcodeSrc = res.url;
                        vm.setScanLogin(res);
                    });
                } else {
                    if (this.scanTimer) {
                        clearInterval(this.scanTimer);
                        this.scanTimer = null;
                    }
                }
            },
            loginVisible: function (val) {
                if (val) {
                    this.state = 3;
                    this.type = 1;
                } else {
                    if (this.scanTimer) {
                        clearInterval(this.scanTimer);
                        this.scanTimer = null;
                    }
                }
            }
        },
        created: function () {
            var vm = this;
            window.addEventListener('keydown', function (event) {
                if (event.key == 'Enter' && vm.loginVisible) {
                    vm.login();
                }
            });
        },
        methods: {
            // 获取验证码
            getCode: function (captchaVerification) {
                var vm = this;
                if (!this.phone) {
                    return this.$message.warning('请输入手机号');
                }
                if (!/^1[3456789]\d{9}$/.test(this.phone)) {
                    return this.$message.warning('手机号错误');
                }
                this.count = this.TIME_COUNT;
                this.timer = setInterval(function () {
                    vm.count--;
                    if (vm.count < 0) {
                        clearInterval(vm.timer);
                        vm.timer = null;
                    }
                }, 1000);
                authApi.code({
                    phone: this.phone,
                    captchaVerification: captchaVerification,
                    captchaType: 'blockPuzzle'
                }).then(function (res) {
                    vm.$message.success(res.msg);
                }).catch(function (err) {
                    vm.$message.error(err.msg);
                    clearInterval(vm.timer);
                    vm.timer = null;
                    vm.count = -1;
                });
            },
            // 登录
            login: function () {
                this.type === 1 ? this.pwdLogin() : this.smsLogin();
            },
            // 短信登录、注册
            smsLogin: function () {
                var vm = this;
                if (!this.phone) {
                    return this.$message.warning('请输入手机号');
                }
                if (!/^1[3456789]\d{9}$/.test(this.phone)) {
                    return this.$message.warning('手机号错误');
                }
                if (!this.code) {
                    return this.$message.warning('请输入验证码');
                }
                if (!/^\d{6}$/.test(this.code)) {
                    return this.$message.warning('验证码错误');
                }
                if (!this.agree) {
                    return this.$message.warning('请勾选用户协议');
                }
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = null;
                }
                loginApi.phoneCheck({
                    phone: this.phone,
                    code: this.code
                }).then(function (res) {
                    vm.$message.success(res.msg);
                    vm.loginClose();
                    window.location.reload();
                    vm.getUserInfo();
                    vm.phone = '';
                    vm.code = '';
                    vm.count = -1;
                }).catch(function (err) {
                    vm.$message.error(err.msg);
                    vm.count = -1;
                });
            },
            // 账号密码登录
            pwdLogin: function () {
                var vm = this;
                if (!this.phone) {
                    return this.$message.warning('请输入手机号');
                }
                if (!/^1[3456789]\d{9}$/.test(this.phone)) {
                    return this.$message.warning('手机号错误');
                }
                if (!this.pwd) {
                    return this.$message.warning('请输入密码');
                }
                if (!this.agree) {
                    return this.$message.warning('请勾选用户协议');
                }
                loginApi.heck({
                    account: this.phone,
                    pwd: md5(this.pwd)
                }).then(function (res) {
                    vm.$message.success(res.msg);
                    vm.loginClose();
                    window.location.reload();
                    vm.getUserInfo();
                }).catch(function (err) {
                    vm.$message.error(err.msg);
                });
            },
            // 账号注册、找回密码
            register: function () {
                var vm = this;
                if (!this.phone) {
                    return this.$message.warning('请输入手机号');
                }
                if (!/^1[3456789]\d{9}$/.test(this.phone)) {
                    return this.$message.warning('手机号错误');
                }
                if (!this.code) {
                    return this.$message.warning('请输入验证码');
                }
                if (!/^\d{6}$/.test(this.code)) {
                    return this.$message.warning('验证码错误');
                }
                if (!this.pwd) {
                    return this.$message.warning('请输入密码');
                }
                if (!/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{8,16}$/.test(this.pwd)) {
                    return this.$message.warning('请输入8-16位字母加数字组合密码');
                }
                if (this.state === 1 && !this.agree) {
                    return this.$message.warning('请勾选用户协议');
                }
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = null;
                }
                loginApi.register({
                    account: this.phone,
                    pwd: md5(this.pwd),
                    code: this.code,
                    type: this.state
                }).then(function (res) {
                    vm.$message.success(res.msg);
                    vm.phone = '';
                    vm.pwd = '';
                    vm.code = '';
                    vm.state = 3;
                }).catch(function (err) {
                    vm.$message.error(err.msg);
                    vm.count = -1;
                });
            },
            setScanLogin: function (data) {
                var vm = this;
                this.scanTimer = setInterval(function () {
                    if (vm.scanCount < 40) {
                        vm.scanCount++;
                        loginApi.setScanLogin(data).then(function () {
                            clearInterval(vm.scanTimer);
                            vm.scanTimer = null;
                            window.location.reload();
                        });
                    } else {
                        clearInterval(vm.scanTimer);
                        vm.scanTimer = null;
                    }
                }, 3000);
            },
            loginClose: function () {
                store.setLoginAction(false);
            },
            agreeOpen: function () {
                store.setAgreeAction(true);
            }
        },
        template: html
    };
});