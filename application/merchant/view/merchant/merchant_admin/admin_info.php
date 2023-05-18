{extend name="public/container"}
{block name="head"}
<link href="{__FRAME_PATH}css/plugins/iCheck/custom.css" rel="stylesheet">
<script src="{__ADMIN_PATH}plug/validate/jquery.validate.js"></script>
<script src="{__ADMIN_PATH}frame/js/plugins/iCheck/icheck.min.js"></script>
<script src="{__ADMIN_PATH}frame/js/ajaxfileupload.js"></script>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">个人资料</div>
        <div class="layui-card-body">
            <div class="layui-row">
                <div class="layui-col-md4">
                    <form class="layui-form" lay-filter="form" action="">
                        <input type="hidden" name="id" value="{$adminInfo.id}">
                        <div class="layui-form-item">
                            <label class="layui-form-label">账号：</label>
                            <div class="layui-input-block">
                                <input type="text" name="account" value="{$adminInfo.account}" readonly class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">姓名：</label>
                            <div class="layui-input-block">
                                <input type="text" name="real_name" value="{$adminInfo.real_name}" required lay-verify="required" placeholder="请输入姓名" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">原始密码：</label>
                            <div class="layui-input-block">
                                <input type="password" name="pwd" placeholder="请输入原始密码" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">新密码：</label>
                            <div class="layui-input-block">
                                <input type="text" name="new_pwd" placeholder="请输入新密码" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">确认密码：</label>
                            <div class="layui-input-block">
                                <input type="text" name="new_pwd_ok" placeholder="请再次输入新密码" autocomplete="off" class="layui-input">
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
    </div>
</div>
{/block}
{block name="script"}
<script>
    (function () {
        var form = layui.form,
            layer = layui.layer;

        form.on('submit(*)', function (data) {
            for (const key in data.field) {
                if (Object.hasOwnProperty.call(data.field, key)) {
                    data.field[key] = data.field[key].trim();
                }
            }
            if (data.field.new_pwd !== data.field.new_pwd_ok) {
                return layer.msg('两次输入的新密码不一致', { icon: 5, time: 2000 });
            }
            $.post("{:url('setAdminInfo')}", data.field, function (data) {
                if(data.code == 400)
                    layer.msg(data.msg, { icon:5, time: 2000 });
                else{
                    layer.msg(data.msg,function () {
                        location.reload();
                    });
                }
            }, 'json');
            return false;
        });
    })();
</script>
{/block}
