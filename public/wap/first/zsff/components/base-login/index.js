define([
    'helper',
    'axios',
    'store',
    'text!./index.html',
    'css!./index.css',
    'wap/first/zsff/js/md5'
], function (helper, axios, store, html) {
    return {
        props: {
            loginShow: {
                type: Boolean,
                default: false
            },
            siteName: {
                type: String,
                default: ''
            },
            cancelBtn: {
                type: Boolean,
                default: true
            }
        },
        data: function () {
            return {
                state: 3,  // 1 注册 2 找回密码 3 登录
                type: 1,  // 1 账号登录 2 手机登录
                phone: '',
                code: '',
                pwd: '',
                agree: false,
                TIME_COUNT: 60,
                count: -1,
                isWeChat: false,
                hasCopyright: false,
                nncnL_crmeb_copyright: ''
            };
        },
        computed: {
            pwdPlaceholder: function () {
                if (this.state == 1) {
                    return '请输入8-16位字母加数字组合密码';
                } else if (this.state == 2) {
                    return '请输入8-16位字母加数字组合新密码';
                } else if (this.type == 1) {
                    return '请填写密码';
                }
            }
        },
        watch: {
            loginShow: function (val) {
                if (val) {
                    this.state = 3;
                    this.type = this.isWeChat ? 2 : 1;
                    this.phone = '';
                    this.code = '';
                    this.pwd = '';
                    this.agree = false;
                    if (this.timer) {
                        clearInterval(this.timer);
                        this.timer = null;
                        this.count = -1;
                    }
                    this.$nextTick(function () {
                        var vm = this;
                        $('#captcha').slideVerify({
                            baseUrl: '/wap/auth_api',
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
                            error: function () {
                                console.error('slideVerify');
                            },
                            beforeShow: function () {
                                var flag = true;
                                if (!vm.phone) {
                                    flag = false;
                                    layer.msg('请输入手机号');
                                } else if (!/^1[3456789]\d{9}$/.test(vm.phone)) {
                                    flag = false;
                                    layer.msg('手机号错误');
                                }
                                return flag;
                            }
                        });
                    });
                }
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
            }
        },
        created: function () {
            var ua = navigator.userAgent.toLowerCase();
            if (ua.match(/MicroMessenger/i) == 'micromessenger') {
                this.isWeChat = true;
                this.type = 2;
            }
            this.getCopyright();
        },
        methods: {
            // 获取验证码
            getCode: function (captchaVerification) {
                var vm = this;
                if (!this.phone) {
                    helper.pushMsg('请输入手机号');
                    return;
                }
                if (!/^1[3456789]\d{9}$/.test(this.phone)) {
                    helper.pushMsg('手机号错误');
                    return;
                }
                this.count = this.TIME_COUNT;
                this.timer = setInterval(function () {
                    vm.count--;
                    if (vm.count < 0) {
                        clearInterval(vm.timer);
                        vm.timer = null;
                    }
                }, 1000);
                var index = layer.load(1);
                axios.post('../auth_api/code', {
                    phone: this.phone,
                    captchaVerification: captchaVerification,
                    captchaType: 'blockPuzzle'
                }).then(function (res) {
                    layer.msg(res.data.msg);
                    if (res.data.code === 400 && vm.timer) {
                        clearInterval(vm.timer);
                        vm.timer = null;
                        vm.count = -1;
                    }
                }).catch(function (err) {
                    console.error(err);
                }).then(function () {
                    layer.close(index);
                });
            },
            // 注册账号、忘记密码
            register: function () {
                var vm = this;
                if (!this.phone) {
                    helper.pushMsg('请填写手机号');
                    return;
                }
                if (!/^1[3456789]\d{9}$/.test(this.phone)) {
                    helper.pushMsg('手机号错误');
                    return;
                }
                if (!this.code) {
                    helper.pushMsg('请填写验证码');
                    return;
                }
                if (!/^\d{6}$/.test(this.code)) {
                    helper.pushMsg('验证码错误');
                    return;
                }
                if (!this.pwd) {
                    helper.pushMsg('请填写密码');
                    return;
                }
                if (!/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{8,16}$/.test(this.pwd)) {
                    helper.pushMsg('请填写8-16位字母加数字组合密码');
                    return;
                }
                if (this.state == 1) {
                    if (!this.agree) {
                        helper.pushMsg('请勾选用户协议');
                        return;
                    }
                }
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = null;
                    vm.count = -1;
                }
                helper.loadFFF();
                store.basePost(helper.U({
                    c: 'login',
                    a: 'register'
                }), {
                    account: this.phone,
                    pwd: hex_md5(this.pwd),
                    code: this.code,
                    type: this.state
                }, function (res) {
                    helper.loadClear();
                    helper.pushMsg(res.data.msg, function () {
                        vm.phone = '';
                        vm.pwd = '';
                        vm.code = '';
                        vm.state = 3;
                        vm.type = 1;
                    });
                }, function () {
                    helper.loadClear();
                });
            },
            // 立即登录
            login: function () {
                if (!this.phone) {
                    helper.pushMsg('请填写手机号');
                    return;
                }
                if (!/^1[3456789]\d{9}$/.test(this.phone)) {
                    helper.pushMsg('手机号错误');
                    return;
                }
                if (this.type == 1) {
                    if (!this.pwd) {
                        helper.pushMsg('请填写密码');
                        return;
                    }
                } else {
                    if (!this.code) {
                        helper.pushMsg('请填写验证码');
                        return;
                    }
                    if (!/^\d{6}$/.test(this.code)) {
                        helper.pushMsg('验证码错误');
                        return;
                    }
                }
                if (!this.agree) {
                    helper.pushMsg('请勾选用户协议');
                    return;
                }
                this.type == 1 ? this.pwdLogin() : this.smsLogin();
            },
            // 账号登录
            pwdLogin: function () {
                var vm = this;
                helper.loadFFF();
                store.basePost(helper.U({
                    c: 'login',
                    a: 'check'
                }), {
                    account: this.phone,
                    pwd: hex_md5(this.pwd)
                }, function (res) {
                    helper.loadClear();
                    helper.pushMsg(res.data.msg, function () {
                        vm.phone = '';
                        vm.pwd = '';
                        vm.$emit('login-close', res.data.data);
                    });
                }, function () {
                    helper.loadClear();
                });
            },
            // 手机登录
            smsLogin: function () {
                var vm = this;
                var url = this.isWeChat ? helper.U({c: 'index', a: 'login'}) : helper.U({c: 'login', a: 'phone_check'});
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = null;
                    vm.count = -1;
                }
                helper.loadFFF();
                store.basePost(url, {
                    phone: this.phone,
                    code: this.code
                }, function (res) {
                    helper.loadClear();
                    helper.pushMsg(res.data.msg, function () {
                        vm.phone = '';
                        vm.code = '';
                        vm.$emit('login-close', res.data.data);
                    });
                }, function () {
                    helper.loadClear();
                });
            },
            // 点击协议
            goAgree: function () {
                window.location.assign(helper.U({
                    c: 'index',
                    a: 'agree'
                }));
            },
            del_redis_phone: function () {
                var vm = this;
                if (!this.phone) {
                    return layer.msg('请输入手机号');
                }
                if (!/^1[3456789]\d{9}$/.test(this.phone)) {
                    return layer.msg('手机号错误');
                }
                if (!this.code) {
                    return layer.msg('请填写验证码');
                }
                var index = layer.load(1);
                axios.get('../auth_api/del_redis_phone', {
                    params: {
                        phone: this.phone,
                        verify: this.code
                    }
                }).then(function (res) {
                    layer.msg(res.data.msg);
                    if (res.data.code === 200) {
                        vm.code = '';
                    }
                }).catch(function (err) {
                    console.error(err);
                }).then(function () {
                    layer.close(index);
                });
            },
            getCopyright: function () {
                var vm = this;
                axios.get('/admin/login/get_copyright').then(function (res) {
                    vm.hasCopyright = res.data.code === 200;
                    if (res.data.code === 200) {
                        vm.nncnL_crmeb_copyright = res.data.data.nncnL_crmeb_copyright;
                    }
                });
            }
        },
        template: html
    };
});