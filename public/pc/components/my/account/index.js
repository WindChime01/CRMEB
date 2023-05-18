define([
    'store/index',
    'api/my',
    'text!./index.html',
    'css!./index.css'
], function(store, myApi, html) {
    return {
        filters: {
            phoneEncrypt: function (phone) {
                if (!phone) {
                    return '';
                }
                return phone.replace(/(\d{3})\d*(\d{4})/, '$1****$2');
            }
        },
        inject: ['getUserInfo', 'logout', 'getUserLogin'],
        props: {
            isLogin: {
                type: Boolean,
                default: false
            },
            userInfo: {
                type: Object,
                default: function () {
                    return {};
                }
            }
        },
        data: function () {
            return {
                nicknameReadonly: true,
                avatar: '',
                nickname: ''
            };
        },
        watch: {
            'userInfo.avatar': function () {
                this.avatar = this.userInfo.avatar;
            },
            'userInfo.nickname': function () {
                this.nickname = this.userInfo.nickname;
            }
        },
        methods: {
            handleBeforeUpload: function (file) {
                if (file.type == 'image/jpeg') {
                    return true;
                }
                if (file.type == 'image/jpg') {
                    return true;
                }
                if (file.type == 'image/png') {
                    return true;
                }
                this.$message.error('上传头像图片只能是 JPEG、JPG、PNG 格式!');
                return false;
            },
            handleSuccess: function (res) {
                var vm = this;
                if (typeof res == 'string') {
                    return vm.$message.error(res);
                }
                if (res.code == 400) {
                    vm.$message.error(res.msg)
                    return;
                }
                vm.avatar = res.data.url;
                vm.$message.success(res.msg);
            },
            // 点击保存
            save: function () {
                var vm = this;
                myApi.saveUserInfo({
                    avatar: this.avatar || this.userInfo.avatar,
                    nickname: this.nickname
                }).then(function (res) {
                    vm.$message.success(res.msg);
                    vm.nicknameReadonly = true;
                    vm.getUserInfo();
                }).catch(function (err) {
                    window.location.replace(vm.$router.home);
                });
            },
            // 点击头像的修改
            updateAvatar: function () {
                this.$refs.upload.$refs['upload-inner'].$refs.input.click();
            },
            accountOpen: function (value) {
                this.getUserLogin();
                store.setIsAccountAction(value);
                store.setAccountAction(true);
            },
            handleLogin: function () {
                this.getUserLogin();
            },
            handleUpdate: function () {
                this.getUserLogin();
                this.nicknameReadonly = !this.nicknameReadonly;
            }
        },
        template: html
    };
});