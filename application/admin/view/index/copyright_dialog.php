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
                        <input type="text" name="domain_name" required lay-verify="required" placeholder="请输入您的企业域名" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">手机号码：</label>
                    <div class="layui-input-block">
                        <input type="text" name="phone" required lay-verify="required|phone" placeholder="请输入负责人手机号码" autocomplete="off" class="layui-input">
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
            data.field.product_type = 'copyright';
            parent.phone = data.field.phone;
            $.post("{:url('pay_order')}", data.field, function (data) {
                var index = parent.layer.getFrameIndex(window.name);
                parent.layer.close(index);
                if (data.code === 200) {
                    parent.vm.invokeQRCode(data.data.content.code_url, data.data.order_id);
                } else {
                    layer.msg(data.msg, {icon: 5}, function () {
                        parent.isCopyright = true;
                        parent.vm.invokeVerify();
                    });
                }
            }, 'json');
        });
    });
</script>
{/block}