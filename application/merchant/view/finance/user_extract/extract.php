{extend name="public/container" /}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" lay-filter="form" action="">
                <input type="hidden" name="gold_num">
                <input type="hidden" name="gold_coin_ratio">
                <div v-if="goldName" class="layui-form-item">
                    <label class="layui-form-label">提现{{ goldName }}：</label>
                    <div class="layui-input-inline">
                        <input v-model.trim="goldNum" type="text" name="gold" :placeholder="'请输入提现' + goldName" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline">
                        <input :value="transfer" type="text" readonly class="layui-input">
                    </div>
                    <div class="layui-form-mid layui-word-aux">元</div>
                </div>
                <template v-else>
                    <div class="layui-form-item">
                        <label class="layui-form-label">提现方式：</label>
                        <div class="layui-input-block">
                            <input type="radio" name="extract_type" value="bank" title="银行卡" lay-filter="type">
                            <input type="radio" name="extract_type" value="alipay" title="支付宝" lay-filter="type">
                            <input type="radio" name="extract_type" value="weixin" title="微信" lay-filter="type">
                        </div>
                    </div>
                    <div v-if="merchat.extract_type === 'bank'" class="layui-form-item">
                        <label class="layui-form-label">银行卡号：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="bank_code" required lay-verify="required|number|cardNumber" placeholder="请输入银行卡号" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div v-if="merchat.extract_type === 'bank'" class="layui-form-item">
                        <label class="layui-form-label">持卡人姓名：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="real_name" required lay-verify="required" placeholder="请输入持卡人姓名" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div v-if="merchat.extract_type === 'bank'" class="layui-form-item">
                        <label class="layui-form-label">开户行地址：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="bank_address" required lay-verify="required" placeholder="请输入开户行地址" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div v-if="merchat.extract_type === 'weixin'" class="layui-form-item">
                        <label class="layui-form-label">微信号：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="weixin" required lay-verify="required|WeChatNumber" placeholder="请输入微信号" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div v-if="merchat.extract_type === 'alipay'" class="layui-form-item">
                        <label class="layui-form-label">支付宝账号：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="alipay_code" required lay-verify="required" placeholder="请输入支付宝账号" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">提现金额：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="extract_price" required lay-verify="required|money" placeholder="请输入提现金额" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                </template>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-normal" lay-submit lay-filter="*">提现</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{/block}
{block name="foot"}
<script>
    require(['vue'], function (Vue) {
        var merchat = {$merchat},
            form = layui.form,
            layer = layui.layer,
            parentLayer = parent.layui.layer;
        merchat.extract_type = 'bank';
        new Vue({
            el: '#app',
            data: {
                merchat: merchat,
                goldName: window.gold_name,
                transfer: 0,
                goldNum: window.gold_num
            },
            watch: {
                'merchat.extract_type': function (value) {
                    this.$nextTick(function () {
                        if (value === 'bank') {
                            form.val('form', merchat);
                        }
                    });
                },
                goldNum: {
                    handler: function (value) {
                        this.transfer = value * $('[name="gold_coin_ratio"]').val() / 100;
                    },
                    immediate: true
                }
            },
            mounted: function () {
                this.$nextTick(function () {
                    var vm = this;
                    // 判断是虚拟币提现
                    if (!this.goldName) {
                        form.val('form', merchat);
                        form.render();
                        form.on('radio(type)', function (data) {
                            merchat.extract_type = data.value;
                        });
                    }
                    form.on('submit(*)', function (data) {
                        // 判断虚拟币提现
                        if (vm.goldName) {
                            $.getJSON("{:url('save_gold')}", data.field, function (data) {
                                layer.msg(data.msg, {
                                    icon: data.code === 200 ? 1 : 5,
                                    time: 2000
                                }, function () {
                                    if (data.code === 200) {
                                        parentLayer.close(parentLayer.getFrameIndex(window.name));
                                        parent.location.reload();
                                    }
                                });
                            });
                        } else {
                            $.post("{:url('save_extract')}", data.field, function (data) {
                                layer.msg(data.msg, {
                                    icon: data.code === 200 ? 1 : 5,
                                    time: 2000
                                }, function () {
                                    if (data.code === 200) {
                                        parentLayer.close(parentLayer.getFrameIndex(window.name));
                                        parent.location.reload();
                                    }
                                });
                            }, 'json');
                        }
                        return false;
                    });
                });
            }
        });
    });
</script>
{/block}
