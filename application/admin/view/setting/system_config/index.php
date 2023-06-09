<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?=$form->getTitle()?></title>

    <link href="{__FRAME_PATH}/css/bootstrap.min.css?v=3.4.0" rel="stylesheet">
    <link href="{__ADMIN_PATH}/css/layui-admin.css" rel="stylesheet">
    <link href="{__FRAME_PATH}/css/style.min.css?v=3.0.0" rel="stylesheet">
    <link href="{__FRAME_PATH}css/font-awesome.min.css?v=4.3.0" rel="stylesheet">
    <script src="{__PLUG_PATH}vue/dist/vue.min.js"></script>
    <link href="{__PLUG_PATH}iview/dist/styles/iview.css" rel="stylesheet">
    <script src="{__PLUG_PATH}iview/dist/iview.min.js"></script>
    <script src="{__PLUG_PATH}jquery/jquery.min.js"></script>
    <script src="{__PLUG_PATH}form-create/province_city.js"></script>
    <script src="{__PLUG_PATH}form-create/form-create.min.js"></script>
    <link href="{__PLUG_PATH}layui/css/layui.css" rel="stylesheet">
    <script src="{__PLUG_PATH}layui/layui.all.js"></script>
    <style>
        /*弹框样式修改*/
        .ivu-modal{top: 20px;}
        .ivu-modal .ivu-modal-body{padding: 10px;}
        .ivu-modal .ivu-modal-body .ivu-modal-confirm-head{padding:0 0 10px 0;}
        .ivu-modal .ivu-modal-body .ivu-modal-confirm-footer{display: none;padding-bottom: 10px;}
        .ivu-date-picker {display: inline-block;line-height: normal;width: 280px;}
        .ivu-modal-footer{display: none;}
        .ivu-poptip-popper{text-align: left;}
        .ivu-icon{padding-left: 5px;}
        .ivu-btn-long{width: 10%;min-width:100px;margin-left: 18%;}
        .layui-fluid{padding:15px;}
        .layui-tab-brief>.layui-tab-more li.layui-this:after, .layui-tab-brief>.layui-tab-title .layui-this:after{border-bottom-color: #0092DC;}
        .layui-tab-brief>.layui-tab-title .layui-this, .layui-tab-brief>.layui-tab-title .layui-this a {color: #0092DC;}
    </style>
</head>
<body class="gray-bg">
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-tab layui-tab-brief">
                {if condition="$config_tab eq null"}
                <ul class="layui-tab-title">
                    <li class="layui-this">系统配置</li>
                </ul>
                {else/}
                <ul class="layui-tab-title">
                    {volist name="config_tab" id="vo"}
                    {if condition="$vo['value'] eq $tab_id"}
                    <li class="layui-this">
                        <a href="{:Url('index',array('tab_id'=>$vo['value'],'type'=>$vo['type']))}"><i class="fa fa-{$vo.icon}"></i>{$vo.label}</a>
                    </li>
                    {else/}
                    <li>
                        <a href="{:Url('index',array('tab_id'=>$vo['value'],'type'=>$vo['type']))}"><i class="fa fa-{$vo.icon}"></i>{$vo.label}</a>
                    </li>
                    {/if}
                    {/volist}
                </ul>
                {/if}
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">
                        <div class="layui-row layui-col-space15">
                            <div class="layui-col-md12">
                                {if condition="$tab_id eq '17'"}
                                <div>若使用crmeb短信平台;请到[<b style="color: #2d8cf0;">设置</b>]中[<b style="color: #2d8cf0;">短信设置</b>]里配置[<a href="{:Url('setting.system_plat/index')}" style="color: #2d8cf0;">短信账户</a>]</div>
                                {elseif condition="$tab_id eq '19'"/}
                                <div>若没有配置oss上传，请到[<b style="color: #2d8cf0;">设置</b>]中[<b style="color: #2d8cf0;">阿里云管理</b>]里配置[<a href="{:Url('setting.system_bucket/index')}" style="color: #2d8cf0;">对象存储</a>]</div>
                                {elseif condition="$tab_id eq '21'"/}
                                <div>若没有配置阿里云直播，请到[<b style="color: #2d8cf0;">设置</b>]中[<b style="color: #2d8cf0;">阿里云管理</b>]里配置[<a href="{:Url('setting.system_broadcast/index')}" style="color: #2d8cf0;">直播配置</a>]</div>
                                {/if}
                            </div>
                            <div class="layui-col-md12">
                                <div id="configboay"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

<script>
    var element = layui.element;
    formCreate.formSuccess = function(form,$r){
        <?=$form->getSuccessScript()?>
        $r.btn.loading(false);
    };

    (function () {
        var create = (function () {
            var getRule = function () {
                var rule = <?=json_encode($form->getRules())?>;
                rule.forEach(function (c) {
                    if ((c.type == 'cascader' || c.type == 'tree') && Object.prototype.toString.call(c.props.data) == '[object String]') {
                        if (c.props.data.indexOf('js.') === 0) {
                            c.props.data = window[c.props.data.replace('js.', '')];
                        }
                    }
                });
                return rule;
            }, vm = new Vue,name = 'formBuilderExec<?= !$form->getId() ? '' : '_'.$form->getId() ?>';
            var _b = false;
            window[name] =  function create(el, callback) {
                if(_b) return ;
                _b = true;
                if (!el) el = document.getElementById('configboay');
                var $f = formCreate.create(getRule(), {
                    el: el,
                    form:<?=json_encode($form->getConfig('form'))?>,
                    row:<?=json_encode($form->getConfig('row'))?>,
                    submitBtn:<?=$form->isSubmitBtn() ? '{}' : 'false'?>,
                    resetBtn:<?=$form->isResetBtn() ? 'true' : '{}'?>,
                    iframeHelper:true,
                    global:{
                        upload: {
                            props:{
                                onExceededSize: function (file) {
                                    vm.$Message.error(file.name + '超出指定大小限制');
                                },
                                onFormatError: function () {
                                    vm.$Message.error(file.name + '格式验证失败');
                                },
                                onError: function (error) {
                                    vm.$Message.error(file.name + '上传失败,(' + error + ')');
                                },
                                onSuccess: function (res, file) {
                                    if (res.code == 200) {
                                        file.url = res.data.filePath;
                                    } else {
                                        vm.$Message.error(res.msg);
                                    }
                                },
                            },
                        },
                    },
                    //表单提交事件
                    onSubmit: function (formData) {
                        $f.btn.loading(true);
                        $.ajax({
                            url: '<?=$form->getAction()?>',
                            type: '<?=$form->getMethod()?>',
                            dataType: 'json',
                            data: formData,
                            success: function (res) {
                                if (res.code == 200) {
                                    vm.$Message.success(res.msg);
                                    $f.btn.loading(false);
                                    formCreate.formSuccess && formCreate.formSuccess(res, $f, formData);
                                    callback && callback(0, res, $f, formData);
                                    //TODO 表单提交成功!
                                } else {
                                    vm.$Message.error(res.msg || '表单提交失败');
                                    $f.btn.loading(false);
                                    callback && callback(1, res, $f, formData);
                                    //TODO 表单提交失败
                                }
                            },
                            error: function () {
                                vm.$Message.error('表单提交失败');
                                $f.btn.loading(false);
                            }
                        });
                    }
                });
                return $f;
            };
            return window[name];
        }());
        window.$f = create();
    })();
</script>
</html>
