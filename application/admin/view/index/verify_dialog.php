{extend name="public/container" /}
{block name="head"}{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" lay-filter="form" action="">
                <div class="layui-form-item">
                    <div class="layui-input-inline" style="width: 306px;margin-left: 0;">
                        <input type="text" name="phone" placeholder="手机号" required lay-verify="required|phone" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-inline" style="float: left;width: 190px;margin-right: 10px;margin-left: 0;">
                        <input type="text" name="code" placeholder="验证码" required lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <button type="button" class="layui-btn layui-btn-normal" style="width: 106px;padding: 0;" id="codeBtn">获取验证码</button>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-inline" style="width: 306px;margin-left: 0;">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-fluid" lay-submit lay-filter="*">立即提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
    $(function () {
        var form = layui.form;
        var layer = layui.layer;

        // 立即提交
        form.on('submit(*)', function (data) {
            $.each(data.field, function (key, value) {
                data.field[key] = $.trim(value);
            });
            $.post("{:url('user_auth_login')}", data.field, function (data) {
                if (data.code === 200) {
                    if (data.data.status === 200) {
                        var d = new Date();
                        d.setTime(d.getTime() + 3600000);
                        document.cookie = 'auth_token=' + data.data.data.token + '; expires=' + d.toUTCString();
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.phone = $.trim($('input[name="phone"]').val());
                        parent.layer.close(index);
                        if (parent.isCopyright) {
                            parent.vm.invokeCopyright();
                        } else {
                            parent.vm.invokeApply();
                        }
                    } else {
                        layer.msg(data.data.msg, {icon: 5});
                    }
                } else {
                    layer.msg(data.msg, {icon: 5});
                }
            }, 'json');
        });

        // 获取验证码
        $('#codeBtn').on('click', function () {
            var btn = $(this);
            var phone = $.trim(form.val('form').phone);
            if (btn.hasClass('layui-btn-disabled')) {
                return false;
            }
            btn.removeClass('layui-btn-normal').addClass('layui-btn-disabled');
            if (!phone) {
                btn.removeClass('layui-btn-disabled').addClass('layui-btn-normal');
                return layer.msg('请输入手机号', {icon: 5});
            }
            $.post("{:url('get_code')}", { phone: phone }, function (data) {
                if (data.code === 200) {
                    layer.msg(data.msg, {icon: 1});
                    var count = 60;
                    btn.text('重新获取(' + count + 's)');
                    var timer = setInterval(function () {
                        count--;
                        btn.text('重新获取(' + count + 's)');
                        if (!count) {
                            clearInterval(timer);
                            timer = null;
                            btn.text('获取验证码').removeClass('layui-btn-disabled').addClass('layui-btn-normal');
                        }
                    }, 1e3);
                } else {
                    btn.removeClass('layui-btn-disabled').addClass('layui-btn-normal');
                    layer.msg(data.msg, {icon: 5});
                }
            }, 'json');
        });
    });
</script>
{/block}