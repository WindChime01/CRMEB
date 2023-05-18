<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <title>登录{$Auth_site_name}讲师管理系统</title>
    <meta name="generator" content="CRMEB! v2.5" />
    <meta name="author" content="CRMEB! Team and CRMEB UI Team" />
    <meta name="copyright" content="2001-2013 CRMEB Inc." />
    <link href="{__FRAME_PATH}css/bootstrap.min.css?v=3.4.0" rel="stylesheet">
    <link href="{__FRAME_PATH}css/font-awesome.min.css?v=4.3.0" rel="stylesheet">
    <link href="{__FRAME_PATH}css/animate.min.css" rel="stylesheet">
    <link href="{__FRAME_PATH}css/style.min.css?v=3.0.0" rel="stylesheet">
    <link href="{__STATIC_PATH}plug/layer/mobile/need/layer.css" rel="stylesheet">
    <link rel="stylesheet" href="{__PLUG_PATH}captcha/css/verify.css">
    <script src="{__STATIC_PATH}plug/layer/mobile/layer.js"></script>
    <script>
        top != window && (top.location.href = location.href);
    </script>
    <style>
        .nav-tabs {
            border-bottom: none;
            margin-bottom: 10px;
        }

        .nav-tabs>li {
            width: 50%;
            margin-bottom: -1px !important;
        }

        .nav-tabs>li>a {
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            margin-right: 0;
            text-align: center;
            font-weight: normal;
            color: #999;
        }

        .nav-tabs>li.active>a,
        .nav-tabs>li.active>a:focus,
        .nav-tabs>li.active>a:hover {
            border-bottom: 1px solid transparent;
            background-color: transparent;
            color: #fff;
        }

        .nav>li>a:focus,
        .nav>li>a:hover {
            border: none;
            border-bottom: 1px solid #ddd;
            background-color: transparent;
            color: #fff;
        }

        .btn.disabled,
        .btn[disabled],
        fieldset[disabled] .btn {
            background-color: #265a88;
        }

        .btn-primary[disabled]:hover {
            background-color: #265a88;
        }

        .form-group {
            display: none;
        }

        body {
            background: url("{__ADMIN_PATH}images/login_mer_bg.jpg") center/cover no-repeat;
        }

        .dialog {
            position: fixed;
            top: 50%;
            left: 50%;
            display: flex;
            border-radius: 6px;
            margin-top: -36px;
            overflow: hidden;
            transform: translate(-50%, -50%);
        }

        .dialog .poster {
            width: 286px;
            height: 400px;
        }

        .dialog .form {
            width: 384px;
            height: 400px;
            padding: 0 52px;
            background-color: #FFFFFF;
        }

        .dialog .logo {
            height: 98px;
        }

        .dialog .logo img {
            display: block;
            width: 98px;
            height: 98px;
            margin: 0 auto;
            object-fit: contain;
        }

        .dialog input {
            width: 100%;
            height: 40px;
            padding: 0 12px;
            border: 1px solid #DDDDDD;
            border-radius: 3px;
            outline: none;
        }

        .dialog .input-wrap {
            margin-bottom: 20px;
        }

        .dialog .login-btn {
            width: 100%;
            height: 40px;
            border: none;
            border-radius: 3px;
            background-color: #338BFB;
            font-size: 14px;
            color: #FFFFFF;
            outline: none;
        }

        .dialog input::placeholder {
            color: #999999;
        }

        .dialog .tab {
            margin-bottom: 10px;
            text-align: center;
            font-size: 0;
        }

        .dialog .tab a {
            display: inline-block;
            width: 70px;
            padding-bottom: 10px;
            border-bottom: 2px solid transparent;
            margin-left: 70px;
            font-size: 14px;
            line-height: 17px;
            color: #282828;
        }

        .dialog .tab a:first-child {
            margin-left: 0;
        }

        .dialog .tab a.active {
            border-bottom-color: #1495ED;
            font-weight: bold;
            font-size: 16px;
            color: #1495ED;
        }

        .dialog .verify-code {
            display: flex;
            border: 1px solid #DDDDDD;
            border-radius: 3px;
            margin: 20px 0;
            overflow: hidden;
            font-size: 14px;
        }

        .dialog .verify-code input {
            flex: 1;
            height: 38px;
            border: none;
        }

        .dialog .verify-code button {
            width: 80px;
            height: 38px;
            padding: 0;
            border: none;
            border-left: 1px solid #DDDDDD;
            background: none;
            font-size: 12px;
            line-height: 40px;
            color: #1495ED;
        }

        .dialog .verify-code button:disabled {
            cursor: not-allowed;
            color: #999999;
        }

        .dialog .verify-code button img {
            display: block;
            width: 116px;
            height: 40px;
        }

        .login-label {
            position: absolute;
            top: 0;
            right: 0;
            width: 109px;
        }

        .footer a {
            text-decoration: none;
            color: #333333;
        }
    </style>
</head>

<body>
    <div class="dialog">
        <img class="login-label" src="{__ADMIN_PATH}images/login_mer_label.png" alt="">
        <div>
            <img class="poster" src="{__ADMIN_PATH}images/login_mer_poster.png">
        </div>
        <div class="form">
            <div class="logo">
                <img src="{$login_logo}" alt="">
            </div>
            <div class="tab">
                <a data-name="account" href="javascript:">密码登录</a>
                <a data-name="message" href="javascript:">短信登录</a>
            </div>
            <div class="input-wrap">
                <input type="text" name="account" placeholder="账号">
            </div>
            <div class="input-wrap">
                <input type="password" name="pwd" placeholder="密码">
            </div>
            <div class="input-wrap">
                <input type="text" name="phone" placeholder="手机号">
            </div>
            <div class="verify-code">
                <input type="text" name="code" placeholder="验证码">
                <button id="getCode">获取验证码</button>
            </div>
            <div>
                <button class="login-btn">登录</button>
            </div>
        </div>
    </div>
    <div class="footer" style="position: fixed;bottom: 0;width: 100%;left: 0;margin: 0;opacity: 0.8;">
        <div class="pull-right">Copyright © 2014-2023 <span id="copyright"></span> 版本:<a id="zsff" href="http://www.crmeb.com/" target="_blank">CRMEB-ZSFF-v2.1.4</a></div>
    </div>
    <div id="captcha"></div>
    <script src="{__PLUG_PATH}jquery-1.10.2.min.js"></script>
    <script src="{__FRAME_PATH}js/bootstrap.min.js?v=3.4.0"></script>
    <script src="{__MODULE_PATH}login/ios-parallax.js"></script>
    <script src="{__PLUG_PATH}md5.min.js"></script>
    <script src="{__PLUG_PATH}captcha/js/crypto-js.js"></script>
    <script src="{__PLUG_PATH}captcha/js/ase.js"></script>
    <script src="{__PLUG_PATH}captcha/js/verify.js"></script>
    <script>
        $(function () {
            var num = Number(localStorage.getItem('mer_verify_num'));
            var data = {};
            var inputList = [];
            var timer;
            // 版权信息
            $.ajax({
                url: "{:url('admin/login/get_copyright')}",
                dataType: 'json',
                success: function (res) {
                    if (res.code === 200) {
                        $('#copyright').text(function () {
                            $('#zsff').removeAttr('href');
                            $('#zsff').removeAttr('target');
                            return res.data.nncnL_crmeb_copyright || 'CRMEB';
                        });
                    } else {
                        $('#copyright').text('CRMEB');
                    }
                },
                error: function () {
                    $('#copyright').text('CRMEB');
                }
            });
            function getCode(captchaVerification) {
                var phone = $.trim($('input[name="phone"]').val());
                if (!phone) {
                    return layer.open({
                        content: '请输入手机号码',
                        skin: 'msg',
                        time: 1
                    });
                }
                if (!/^1[3456789]\d{9}$/.test(phone)) {
                    return layer.open({
                        content: '请输入正确的手机号码',
                        skin: 'msg',
                        time: 1
                    });
                }
                var count = 60;
                $('#getCode').html('重新获取' + count + 's');
                timer = setInterval(function () {
                    count--;
                    $('#getCode').html('重新获取' + count + 's');
                    if (!count) {
                        clearInterval(timer);
                        timer = null;
                        $('#getCode').html('获取验证码');
                    }
                }, 1000);
                $.post("{:url('code')}", {
                    phone: phone,
                    captchaVerification: captchaVerification,
                    captchaType: 'blockPuzzle'
                }, function (data) {
                    layer.open({
                        content: data.msg,
                        skin: 'msg',
                        time: 1
                    });
                    if (data.code === 400 && timer) {
                        clearInterval(timer);
                        timer = null;
                        $('#getCode').html('获取验证码');
                    }
                }, 'json');
            }

            function login(data) {
                $.ajax({
                    url: "{:url('verify')}",
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    success: function (res) {
                        switch (res.code) {
                            case 1:
                                localStorage.removeItem('mer_verify_num');
                                window.location.replace("{:url('index/index')}");
                                break;
                            case 3:
                                if (data.captchaType === undefined) {
                                    num = 2;
                                    localStorage.setItem('mer_verify_num', num);
                                    $('.login-btn').trigger('click');
                                } else {
                                    layer.open({
                                        content: '滑块验证错误',
                                        skin: 'msg',
                                        time: 1
                                    });
                                }
                                break;
                            case 4:
                                layer.open({
                                    content: '请求方式错误',
                                    skin: 'msg',
                                    time: 1
                                });
                                break;
                            default:
                                num = res.num || 0;
                                localStorage.setItem('mer_verify_num', num);
                                layer.open({
                                    content: res.msg,
                                    skin: 'msg',
                                    time: 1
                                });
                                break;
                        }
                    }
                });
            }

            var tabActive = '#account';
            // 登录
            $('.login-btn').on('click', function () {
                // 密码登录
                if (tabActive === 'account') {
                    var account = $.trim($('input[name="account"]').val());
                    var pwd = $.trim($('input[name="pwd"]').val());
                    if (!account) {
                        return layer.open({
                            content: '请输入账号',
                            skin: 'msg',
                            time: 1
                        });
                    }
                    if (!pwd) {
                        return layer.open({
                            content: '请输入密码',
                            skin: 'msg',
                            time: 1
                        });
                    }
                    data.account = account;
                    data.pwd = md5(pwd);
                    if (num >= 2) {
                        $('#getCode').trigger('click');
                    } else {
                        login(data);
                    }
                }
                // 短信登录
                if (tabActive === 'message') {
                    var phone = $.trim($('input[name="phone"]').val());
                    var code = $.trim($('input[name="code"]').val());
                    if (!phone) {
                        return layer.open({
                            content: '请输入手机号码',
                            skin: 'msg',
                            time: 1
                        });
                    }
                    if (!/^1[3456789]\d{9}$/.test(phone)) {
                        return layer.open({
                            content: '请输入正确的手机号码',
                            skin: 'msg',
                            time: 1
                        });
                    }
                    if (!code) {
                        return layer.open({
                            content: '请输入验证码',
                            skin: 'msg',
                            time: 1
                        });
                    }
                    $.ajax({
                        url: "{:url('phone_check')}",
                        type: 'post',
                        data: {
                            phone: phone,
                            code: code
                        },
                        dataType: 'json',
                        success: function (res) {
                            if (res.code === 200) {
                                window.location.replace("{:url('index/index')}");
                            } else {
                                layer.open({
                                    content: res.msg,
                                    skin: 'msg',
                                    time: 2
                                });
                            }
                        }
                    });
                }
            });
            // 切换登录方式
            $('.tab a').on('click', function (e) {
                e.preventDefault()
                if ($(this).hasClass('active')) {
                    return;
                }
                $(this).addClass('active').siblings().removeClass('active');
                tabActive = $(this).data('name');
                inputList = ['phone', 'code'];
                if (tabActive === 'account') {
                    inputList = ['account', 'pwd'];
                    if (timer) {
                        clearInterval(timer);
                        timer = null;
                        $('#getCode').html('获取验证码');
                    }
                }
                $('input').each(function () {
                    if (inputList.indexOf($(this).attr('name')) === -1) {
                        $(this).parent().hide();
                    } else {
                        $(this).parent().show();
                    }
                });
            });
            $('.tab a:first').trigger('click');

            $('#captcha').slideVerify({
                baseUrl: '/merchant/login',
                mode: 'pop',
                containerId: 'getCode',
                beforeCheck: function () {
                    var flag = true;
                    return flag
                },
                ready: function () { },
                success: function (params) {
                    if (tabActive === 'message') {
                        getCode(params.captchaVerification);
                    } else {
                        data.captchaVerification = params.captchaVerification;
                        data.captchaType = 'blockPuzzle';
                        login(data);
                    }
                },
                error: function () { },
                beforeShow: function () {
                    var flag = true;
                    if (tabActive === 'message') {
                        var phone = $.trim($('input[name="phone"]').val());
                        if (!phone) {
                            flag = false;
                            layer.open({
                                content: '请输入手机号码',
                                skin: 'msg',
                                time: 1
                            });
                        } else if (!/^1[3456789]\d{9}$/.test(phone)) {
                            flag = false;
                            layer.open({
                                content: '请输入正确的手机号码',
                                skin: 'msg',
                                time: 1
                            });
                        }
                    }
                    return flag;
                }
            });
        });
    </script>
</body>

</html>
