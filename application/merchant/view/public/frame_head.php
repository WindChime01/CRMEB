
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {empty name='is_layui'}
    <link href="{__FRAME_PATH}css/bootstrap.min.css?v=3.4.0" rel="stylesheet">
    {/empty}
    <link href="{__PLUG_PATH}layui/css/layui.css" rel="stylesheet">
    <link href="{__ADMIN_PATH}css/layui-admin.css" rel="stylesheet">
    <link rel="stylesheet" href="{__FRAME_PATH}font-awesome/css/font-awesome.min.css">
    <link href="{__FRAME_PATH}css/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{__FRAME_PATH}iconfont/iconfont.css">
    <link rel="stylesheet" href="{__FRAME_PATH}css/style.min.css">
    <script src="{__FRAME_PATH}js/jquery.min.js"></script>
    <script src="{__FRAME_PATH}js/bootstrap.min.js"></script>
    <script src="{__PLUG_PATH}xm-select.js"></script>
    <script src="{__PLUG_PATH}layui/layui.all.js"></script>
    <script>
        $eb = parent._mpApi;
        window.controlle="{:getController()}";
        window.module="{:getModule()}";
        
        layui.laydate.set({
            trigger: 'click',
            theme: '#0092DC'
        });
        layui.table.set({
            page: {
                theme: '#0092DC'
            }
        });
        layui.form.verify({
            // 正整数，包含0
            integer: function (value, item) {
                if (Number(value) && !/^\+?[1-9]\d*$/.test(value)) {
                    return '必须是不小于0的整数';
                }
            },
            // 金额
            money: [
                /(?:^[1-9]([0-9]+)?(?:\.[0-9]{1,2})?$)|(?:^(?:0)$)|(?:^[0-9]\.[0-9](?:[0-9])?$)/,
                '请正确输入金额'
            ],
            // 银行卡号
            cardNumber: [
                /^[1-9]\d{9,29}$/,
                '请正确输入银行卡号'
            ],
            // 微信号
            WeChatNumber: [
                /^[a-zA-Z][-_a-zA-Z0-9]{5,19}$/,
                '请正确输入微信号'
            ]
        });
    </script>
    <style>
        .layui-form-switch {
            -webkit-box-sizing: content-box;
            -moz-box-sizing: content-box;
            box-sizing: content-box;
        }
        .layui-tab-brief>.layui-tab-title .layui-this, .layui-tab-brief>.layui-tab-title .layui-this a {
            color: #0092DC;
        }
        .layui-tab-brief>.layui-tab-more li.layui-this:after, .layui-tab-brief>.layui-tab-title .layui-this:after {
            border-color: #0092DC;
        }
        .layui-laydate-footer span:hover {
            color: #0092DC;
        }
        .layui-laydate-footer span[lay-type=date] {
            color: #0092DC;
        }
        .layui-table-cell {
            word-break: break-all;
        }
    </style>


