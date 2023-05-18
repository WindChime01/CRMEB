<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <title>登录{$Auth_site_name}管理系统</title>
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
        body {
            background: url("{__ADMIN_PATH}images/login_bg.jpg") center/cover no-repeat;
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
            height: 117px;
        }

        .dialog .logo img {
            display: block;
            width: 117px;
            height: 117px;
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
            margin-bottom: 22px;
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
        <img class="login-label" src="{__ADMIN_PATH}images/login_label.png" alt="">
        <div>
            <img id="poster" class="poster" src="">
        </div>
        <div class="form">
            <div class="logo">
                <img id="logo" src="" alt="">
            </div>
            <div class="input-wrap">
                <input type="text" id="account" name="account" placeholder="账号">
            </div>
            <div class="input-wrap">
                <input type="password" id="pwd" name="pwd" placeholder="密码">
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
    <div id="verify" hidden></div>
    <script src="{__PLUG_PATH}jquery-1.10.2.min.js"></script>
    <script src="{__FRAME_PATH}js/bootstrap.min.js?v=3.4.0"></script>
    <script src="{__MODULE_PATH}login/ios-parallax.js"></script>
    <script src="{__PLUG_PATH}md5.min.js"></script>
    <script src="{__PLUG_PATH}captcha/js/crypto-js.js"></script>
    <script src="{__PLUG_PATH}captcha/js/ase.js"></script>
    <script src="{__PLUG_PATH}captcha/js/verify.js"></script>
    <script>
        $(function () {
            var num = Number(localStorage.getItem('verify_num'));
            var data = {};
            $('#poster').attr('src', function () {
                return "{$login_left_image}" || '{__ADMIN_PATH}images/login_poster.png';
            });
            $('#logo').attr('src', function () {
                return "{$login_logo}" || '{__ADMIN_PATH}images/login_logo.gif';
            });
            // 版权信息
            $.ajax({
                url: "{:url('login/get_copyright')}",
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

            function handleSubmit() {
                var account = $.trim($('#account').val());
                var pwd = $.trim($('#pwd').val());
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
                    $('#verify').trigger('click');
                } else {
                    handleLogin(data);
                }
            }

            function handleLogin(data) {
                $.ajax({
                    url: "{:url('verify')}",
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    success: function (res) {
                        switch (res.code) {
                            case 1:
                                localStorage.removeItem('verify_num');
                                window.location.replace("{:url('index/index')}");
                                break;
                            case 3:
                                if (data.captchaType === undefined) {
                                    num = 2;
                                    localStorage.setItem('verify_num', num);
                                    handleSubmit();
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
                                localStorage.setItem('verify_num', num);
                                layer.open({
                                    content: res.msg,
                                    skin: 'msg',
                                    time: 1
                                });
                                break;
                        }
                    },
                    error: function (err) {
                        layer.open({
                            content: '登录失败！',
                            skin: 'msg',
                            time: 1
                        });
                    }
                });
            }

            $('.login-btn').on('click', handleSubmit);
            $(document).on('keydown', function (event) {
                if (event.keyCode === 13) {
                    handleSubmit();
                }
            });

            $('#captcha').slideVerify({
                baseUrl: '/admin/login',
                mode: 'pop',
                containerId: 'verify',
                beforeCheck: function () {
                    var flag = true;
                    return flag
                },
                ready: function () { },
                success: function (params) {
                    data.captchaVerification = params.captchaVerification;
                    data.captchaType = 'blockPuzzle';
                    handleLogin(data);
                },
                error: function () { },
                beforeShow: function () {
                    var flag = true;
                    return flag;
                }
            });
        });
    </script>
</body>

</html>
