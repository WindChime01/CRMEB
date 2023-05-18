{extend name="public/container" /}
{block name="title"}基本设置{/block}
{block name="head"}
<style>
    #label .layui-btn {
        cursor: auto;
    }

    #label .layui-icon {
        cursor: pointer;
        visibility: hidden;
    }

    #label .layui-btn:hover .layui-icon {
        visibility: visible;
    }
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" lay-filter="form" action="">
                <div class="layui-tab layui-tab-brief" lay-filter="tab">
                    <ul class="layui-tab-title">
                        <li class="layui-this">基本设置</li>
                        <li>介绍设置</li>
                        <li>分成介绍</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <div class="layui-form-item">
                                <label class="layui-form-label required">姓名：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="mer_name" required lay-verify="required" placeholder="请输入姓名" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label required">手机号码：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="mer_phone" required lay-verify="required|phone" placeholder="请输入电话" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">电子邮箱：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="mer_email" placeholder="请输入邮箱" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label required">头像：</label>
                                <div class="layui-input-block">
                                    <button type="button" id="avatar" class="layui-btn layui-btn-primary" style="width: 100px;height: 100px;padding: 0;line-height: 30px;"></button>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label required">领域：</label>
                                <div class="layui-input-inline" style="width: auto;">
                                    <div class="layui-btn-container" id="label"></div>
                                </div>
                                <div class="layui-input-inline" style="width: 8em;">
                                    <input type="text" name="label" placeholder="添加领域" autocomplete="off" maxlength="6" class="layui-input" style="height: 30px;">
                                </div>
                                <div class="layui-form-mid layui-word-aux" style="padding: 5px 0 !important;">每个领域1-6个字，最多添加2个领域</div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label required">地址：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="mer_address" required lay-verify="required" placeholder="请输入地址" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label required">简介：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="explain" required lay-verify="required" maxlength="20" placeholder="最多20个字" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">银行卡号：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="bank_number" placeholder="请输入银行卡号" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">持卡人姓名：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="bank_name" placeholder="请输入持卡人姓名" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">开户银行：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="bank" placeholder="请输入开户银行" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">银行地址：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="bank_address" placeholder="请输入银行地址" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">状态：</label>
                                <div class="layui-input-block">
                                    <input type="checkbox" name="estate" lay-skin="switch" lay-text="开启|关闭" value="1">
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <div class="layui-form-item">
                                <label class="layui-form-label required">介绍：</label>
                                <div class="layui-input-block">
                                    <script id="editor" name="mer_info" type="text/plain"></script>
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <table class="layui-table">
                                <thead>
                                    <tr>
                                        <th>类型</th>
                                        <th>分成(%)</th>
                                    </tr>
                                </thead>
                                <tbody id="divide"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-normal" lay-submit lay-filter="*">提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script id="avatarTpl" type="text/html">
    {{# if (d.mer_avatar) { }}
    <img src="{{ d.mer_avatar }}" style="width: 100%;height: 100%;">
    {{# } else { }}
    <i class="layui-icon layui-icon-addition" style="font-size: 20px;"></i>
    <p>上传图片</p>
    {{# } }}
</script>
<script id="labelTpl" type="text/html">
    {{# layui.each(d.label, function (index, item) { }}
    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" style="padding: 0 0 0 18px;">{{ item }}<i class="layui-icon layui-icon-close" style="margin-left: 3px;"></i></button>
    {{# }); }}
</script>
<script id="divideTpl" type="text/html">
    <tr>
        <td>专题</td>
        <td>{{ d.mer_special_divide }}</td>
    </tr>
    <tr>
        <td>商品</td>
        <td>{{ d.mer_store_divide }} + 运费</td>
    </tr>
    <tr>
        <td>活动</td>
        <td>{{ d.mer_event_divide }}</td>
    </tr>
    <tr>
        <td>资料</td>
        <td>{{ d.mer_data_divide }}</td>
    </tr>
    <tr>
        <td>试卷</td>
        <td>{{ d.mer_test_divide }}</td>
    </tr>
    <tr>
        <td>直播</td>
        <td>{{ d.gold_divide }}</td>
    </tr>
</script>
{/block}
{block name="foot"}
<script src="{__ADMIN_PATH}plug/ueditor/ueditor.config.js"></script>
<script src="{__ADMIN_PATH}plug/ueditor/ueditor.all.min.js"></script>
<script>
    require(['{__ADMIN_PATH}plug/ueditor/third-party/zeroclipboard/ZeroClipboard.js', 'request', 'OssUpload'], function (ZeroClipboard) {
        var merchat = {$merchat},
            lecturer = {$lecturer},
            form = layui.form,
            layer = layui.layer,
            laytpl = layui.laytpl,
            element = layui.element;

        lecturer.label = lecturer.label ? JSON.parse(lecturer.label) : [];

        window['ZeroClipboard'] = ZeroClipboard;
        window['insertEditor'] = function (list) {
            ue.execCommand('insertimage', list);
        };

        function renderTpl(id, data) {
            laytpl(document.getElementById(id + 'Tpl').innerHTML).render(data, function (html) {
                document.getElementById(id).innerHTML = html;
            });
        }

        this.changeIMG = function (name, image) {
            merchat[name] = image;
            renderTpl('avatar', merchat);
        }

        merchat.explain = lecturer.explain;

        renderTpl('avatar', merchat);
        renderTpl('label', lecturer);
        renderTpl('divide', merchat);

        if (lecturer.label && lecturer.label.length === 3) {
            $('#label').parent().next().hide();
        }

        UE.registerUI('选择图片', function (editor, uiName) {
            return new UE.ui.Button({
                name: uiName,
                title: uiName,
                cssRules: 'background-position: -380px 0;',
                onclick: function () {
                    ossUpload.createFrame(uiName, {fodder: editor.key}, {w: 800, h: 550});
                }
            });
        });

        var ue = UE.getEditor('editor');
        ue.ready(function () {
            ue.setContent(lecturer.introduction);
        });

        form.val('form', merchat);

        form.on('submit(*)', function (data) {
            data.field.label = lecturer.label;
            data.field.mer_avatar = merchat.mer_avatar;
            if (!data.field.mer_info) {
                return layer.msg('介绍不能为空', {icon: 5});
            }
            $.post("{:url('edit_merchant')}", data.field, function (data) {
                layer.msg(data.msg, {
                    icon: data.code === 200 ? 1 : 5,
                    time: 2000
                })
            }, 'json');
            return false;
        });

        // 点击头像
        $('#avatar').on('click', function (event) {
            ossUpload.createFrame('请选择图片', {
                fodder: 'mer_avatar',
                max_count: 0
            }, {
                w: 800,
                h: 550
            });
        });

        $('[name="label"]').on('blur', function (event) {
            var value = $.trim($(this).val());
            for (var index = 0; index < lecturer.label.length; index++) {
                if (lecturer.label[index] === value) {
                    return layer.msg('请勿重复添加', {
                        icon: 5,
                        time: 2000
                    });
                }
            }
            $(this).val('');
            if (value) {
                lecturer.label.push(value);
                if (lecturer.label.length === 3) {
                    $(this).parent().hide();
                }
                renderTpl('label', lecturer);
            }
        });

        $('#label').on('click', '.layui-icon', function (event) {
            lecturer.label.splice($('#label .layui-icon').index($(this)), 1);
            $(this).parents('.layui-input-inline').next().show();
            renderTpl('label', lecturer);
        });
    });
</script>
{/block}