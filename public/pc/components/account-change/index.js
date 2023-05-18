define([
    'store/index',
    'api/login',
    'api/my',
    'api/auth',
    'plugins/blueimp-md5/js/md5',
    'text!./index.html',
    'css!./index.css'
], function (store, loginApi, myApi, authApi, md5, html) {
    return {
        inject: ['logout'],
        props: {
            currentPhone: {
                type: String,
                default: ''
            }
        },
        data: function () {
            return {
                state: true,
                phone: '',
                code: '',
                pwd: '',
                count: -1,
                TIME_COUNT: 60,
                storeState: store.state
            };
        },
        watch: {
            'storeState.accountVisible': function (value) {
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
                                return flag;
                            }
                        });
                        if (this.timer) {
                            clearInterval(this.timer);
                            this.timer = null;
                            this.count = -1;
                        }
                    }
                });
            }
        },
        methods: {
            // 获取验证码
            getCode: function (captchaVerification) {
                var vm = this;
                this.count = this.TIME_COUNT;
                this.timer = setInterval(function () {
                    vm.count--;
                    if (vm.count < 0) {
                        clearInterval(vm.timer);
                        vm.timer = null;
                    }
                }, 1000);
                loginApi.code({
                    phone: this.currentPhone,
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
            submit: function () {
                var vm = this;
                if (!this.state) {
                    if (!this.phone) {
                        return this.$message.warning('请输入手机号');
                    }
                    if (!/^1[3456789]\d{9}$/.test(this.phone)) {
                        return this.$message.warning('手机号错误');
                    }
                }
                if (!this.code) {
                    return this.$message.warning('请输入验证码');
                }
                if (!/^\d{6}$/.test(this.code)) {
                    return this.$message.warning('验证码错误');
                }
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = null;
                }
                if (this.phonePassword) {
                    if (this.state) {
                        // 验证旧手机号
                        myApi.validate_code({
                            phone: this.currentPhone,
                            code: this.code
                        }).then(function () {
                            vm.state = false;
                            vm.phone = '';
                            vm.code = '';
                        }).catch(function (err) {
                            vm.$message.error(err.msg);
                            vm.count = -1;
                        });
                        return;
                    }
                    // 保存新手机号
                    myApi.save_phone({
                        phone: this.phone,
                        code: this.code
                    }).then(function (res) {
                        vm.$message.success(res.msg);
                        vm.accountClose();
                        vm.logout();
                    }).catch(function (err) {
                        vm.$message.error(err.msg);
                        vm.count = -1;
                    });
                }
                if (!this.pwd) {
                    return this.$message.warning('请输入密码');
                }
                if (!/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{8,16}$/.test(this.pwd)) {
                    return this.$message.warning('请输入8-16位字母加数字组合新密码');
                }
                // 修改密码
                loginApi.register({
                    account: this.currentPhone,
                    code: this.code,
                    pwd: md5(this.pwd),
                    type: 2
                }).then(function (res) {
                    vm.$message.success(res.msg);
                    vm.accountClose();
                    vm.logout();
                }).catch(function (err) {
                    vm.$message.error(err.msg);
                    vm.count = -1;
                });
            },
            accountClose: function () {
                store.setAccountAction(false);
            }
        },
        template: html
    };
});