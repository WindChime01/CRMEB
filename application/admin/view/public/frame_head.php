
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
        layui.extend({
            treeTable: '{__PLUG_PATH}treeTable',
            dropdown: '{__PLUG_PATH}layui-dropdown/dropdown',
            dropMenu: '{__PLUG_PATH}dropMenu/dropMenu'
        });
        layui.laydate.set({
            trigger: 'click',
            theme: '#0092DC'
        });
        layui.table.set({
            page: {
                theme: '#0092DC'
            }
        });
    </script>
    <style>
        :root {
            --main-theme-color: #0092DC;
            --main-border-color: #0092DC;
            --main-background-color: #0092DC;
            --main-color: #0092DC;
        }

        [v-cloak] {
            display: none !important;
        }

        .layui-btn-normal {
            background-color: var(--main-background-color);
        }

        .layui-form-switch {
            -webkit-box-sizing: content-box;
            -moz-box-sizing: content-box;
            box-sizing: content-box;
        }

        .layui-form-onswitch {
            border-color: var(--main-border-color);
            background-color: var(--main-background-color);
        }

        .layui-form-radio>i:hover, .layui-form-radioed>i {
            color: var(--main-color);
        }

        .layui-tab-brief>.layui-tab-title .layui-this, .layui-tab-brief>.layui-tab-title .layui-this a {
            color: var(--main-color);
        }

        .layui-tab-brief>.layui-tab-more li.layui-this:after, .layui-tab-brief>.layui-tab-title .layui-this:after {
            border-bottom-color: var(--main-border-color);
        }

        .layui-btn-group .layui-btn-primary:hover {
            color: var(--main-color);
        }

        .layui-form-checked span, .layui-form-checked:hover span {
            background-color: var(--main-color);
        }

        .layui-form-checkbox i {
            box-sizing: content-box;
        }

        .layui-form-checked i, .layui-form-checked:hover i {
            box-sizing: content-box;
            color: var(--main-color);
        }

        .layui-table-cell {
            word-break: break-all;
        }

        .layui-laydate-footer span:hover {
            color: var(--main-color);
        }
        .layui-laydate-footer span[lay-type=date] {
            color: var(--main-color);
        }
        .layui-laypage .layui-laypage-curr .layui-laypage-em {
            background-color: var(--main-color);
        }
        .layui-elem-quote {
            border-left-color: var(--main-color);
            font-size: 14px;
        }
    </style>