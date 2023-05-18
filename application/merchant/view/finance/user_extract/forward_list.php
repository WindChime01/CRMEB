{extend name="public/container" /}
{block name="title"}提现管理{/block}
{block name="head"}
<style>
    .money {
        font-size: 32px;
    }
    .withdraw {
        margin-left: 30px;
    }
    .layui-btn-group .layui-btn-normal {
        border: 1px solid #0092DC;
        border-left: none;
    }
    .layui-btn-group .layui-btn-normal:first-child {
        border-left: 1px solid #0092DC;
    }
</style>
{/block}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">提现管理</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space30">
                <div class="layui-col-md6">
                    可提现余额（元）：<div class="layui-inline money">{$money.now_money}</div>
                    <button v-if="isSonAdmin !== 1" type="button" class="layui-btn layui-btn-normal layui-btn-sm withdraw" @click="withdraw(1)">提现</button>
                </div>
                <div class="layui-col-md6">
                    可提现{$gold_name}：<div class="layui-inline money">{$money.gold_num}</div>
                    <button v-if="isSonAdmin !== 1" type="button" class="layui-btn layui-btn-normal layui-btn-sm withdraw" @click="withdraw(2)">提现</button>
                </div>
                <div class="layui-col-md12">
                    提现状态：
                    <div class="layui-btn-group">
                        <button type="button" :class="[status === '' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn',  'layui-btn-sm']" @click="getExtract('')">全部</button>
                        <button type="button" :class="[status === 0 ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn', 'layui-btn-sm']" @click="getExtract(0)">待审核</button>
                        <button type="button" :class="[status === 1 ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn', 'layui-btn-sm']" @click="getExtract(1)">提现成功</button>
                        <button type="button" :class="[status === -1 ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn', 'layui-btn-sm']" @click="getExtract(-1)">提现失败</button>
                    </div>
                </div>
                <div class="layui-col-md12">
                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" @click="refresh"><i class="layui-icon layui-icon-refresh"></i>刷新</button>
                </div>
                <div class="layui-col-md12">
                    <table id="table"></table>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="foot"}
<script>
    require(['vue'], function (Vue) {
        var isSonAdmin = {$is_son_admin},
            money = {$money},
            table = layui.table,
            layer = layui.layer,
            util = layui.util;
        new Vue({
            el: '#app',
            data: {
                isSonAdmin: isSonAdmin,
                money: money,
                status: ''
            },
            mounted: function () {
                this.$nextTick(function () {
                    table.render({
                        elem: '#table',
                        url: "{:url('get_mer_user_extract')}",
                        cols: [[
                            {field: 'id', title: 'ID', align: 'center'},
                            {field: 'add_time', title: '申请时间', align: 'center', templet: function (d) {
                                return util.toDateString(d.add_time * 1000);
                            }},
                            {field: 'extract_price', title: '实际提现金额', align: 'center'},
                            {field: 'extract_type', title: '提现方式/账号', align: 'center', templet: function (d) {
                                if (d.extract_type === 'bank') {
                                    return '银行卡/'+d.bank_code;
                                }
                                if (d.extract_type === 'alipay') {
                                    return '支付宝/'+d.alipay_code;
                                }
                                if (d.extract_type === 'weixin') {
                                    return '微信/'+d.wechat;
                                }
                                if (d.extract_type === 'yue') {
                                    return '余额';
                                }
                            }},
                            {field: 'status', title: '状态', align: 'center', templet: function (d) {
                                if (d.status === -1) {
                                    return '未通过';
                                }
                                if (d.status === 0) {
                                    return '待审核';
                                }
                                if (d.status === 1) {
                                    return '已提现';
                                }
                            }},
                            {title: '失败原因', align: 'left', templet: function (d) {
                                if (d.status === -1) {
                                    return d.fail_msg;
                                }
                                return '—';
                            }},
                            {field: 'mark', title: '备注', align: 'left', templet: function (d) {
                                    if (d.mark) {
                                        return d.mark;
                                    }
                                    return '';
                                }}
                        ]]
                    });
                });
            },
            methods: {
                getExtract: function (status) {
                    this.status = status;
                    table.reload('table', {
                        where: {
                            status: status
                        },
                        page: {
                            curr: 1
                        }
                    });
                },
                withdraw: function (value) {
                    if (value === 1) {
                        if ({$money.now_money} === 0) {
                            return layer.msg('可提现余额：' + {$money.now_money}, { icon: 5 });
                        }
                    }
                    if (value === 2) {
                        if (!{$money.gold_num}) {
                            return layer.msg('可提现'+ '{$gold_name}：' + {$money.gold_num}, { icon: 5 });
                        }
                    }
                    layer.open({
                        type: 2,
                        title: '提现',
                        area: ['50%', '60%'],
                        content: "{:url('extract')}",
                        success: function (layero, index) {
                            if (value === 2) {
                                var body = layer.getChildFrame('body', index);
                                var iframeWin = window[layero.find('iframe')[0]['name']];
                                body.find('[name="gold_num"]').val({$money.gold_num});
                                body.find('[name="gold_coin_ratio"]').val({$gold_coin_ratio});
                                iframeWin.gold_name = '{$gold_name}';
                                iframeWin.gold_num = {$money.gold_num};
                            }
                        }
                    });
                },
                refresh: function () {
                    window.location.reload();
                }
            }
        });
    });
</script>
{/block}
