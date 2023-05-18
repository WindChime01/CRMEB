{extend name="public/container" /}
{block name="head"}
<style>
    .layui-form-label {
        width: 100px;
        padding: 9px 15px;
    }

    .layui-input-block {
        margin-left: 100px;
    }
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" lay-filter="form" action="">
                <div class="layui-form-item">
                    <label class="layui-form-label">企业名称：</label>
                    <div class="layui-input-block">
                        <input type="text" name="company_name" required lay-verify="required" placeholder="请输入您的企业名称" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">企业域名：</label>
                    <div class="layui-input-block">
                        <input type="text" name="domain_name" required lay-verify="required" placeholder="注：区分二级域名，申请通过后只能使用当前提交的域名" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">订单号：</label>
                    <div class="layui-input-block">
                        <input type="text" name="order_id" required lay-verify="required" placeholder="请输入授权码/购买源码订单号" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">手机号：</label>
                    <div class="layui-input-block">
                        <input type="text" name="phone" required lay-verify="required|phone" placeholder="请输入负责人手机号" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-normal" lay-submit lay-filter="*">立即提交</button>
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

        form.on('submit(*)', function (data) {
            $.post("{:url('user_auth_apply')}", data.field, function (data) {
                layer.msg(data.msg, {icon: data.code === 200 ? 1 : 5}, function () {
                    if (data.code === 200) {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.location.reload();
                    }
                });
            }, 'json');
        });
    });
</script>
{/block}