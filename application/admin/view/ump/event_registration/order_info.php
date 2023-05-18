{extend name="public/container"}
{block name="content"}
<div class="ibox-content order-info">

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    用户信息
                </div>
                <div class="panel-body">
                    <div class="row show-grid">
                        <div class="col-xs-6" >用户昵称: {$userInfo.nickname}</div>
                        <div class="col-xs-6">联系电话: {$userInfo.phone}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    订单信息
                </div>
                <div class="panel-body">
                    <div class="row show-grid">
                        <div class="col-xs-6" >订单编号: {$orderInfo.order_id}</div>
                        <div class="col-xs-6" style="color: #8BC34A;">订单状态:
                            {if condition="$orderInfo['paid'] eq 0"}
                            未支付
                            {elseif condition="$orderInfo['paid'] eq 1"/}
                            已支付
                            {elseif condition="$orderInfo['paid'] eq 1 && $orderInfo['refund_status'] eq 2"/}
                            已退款
                            {/if}
                        </div>
                        <div class="col-xs-6">报名人数: {$orderInfo.number}</div>
                        <div class="col-xs-6">订单总价: ￥{$orderInfo.pay_price}</div>
                        <div class="col-xs-6">实际支付: ￥{$orderInfo.pay_price}</div>
                        {if condition="$orderInfo['refund_price'] GT 0"}
                        <div class="col-xs-6" style="color: #f1a417">退款金额: ￥{$orderInfo.refund_price}</div>
                        {/if}
                        <div class="col-xs-6">创建时间: {$orderInfo.add_time|date="Y/m/d H:i",###}</div>
                        <div class="col-xs-6">支付方式:
                            {if condition="$orderInfo['paid'] eq 1"}
                               {if condition="$orderInfo['pay_type'] eq 'weixin'"}
                               微信支付
                               {elseif condition="$orderInfo['pay_type'] eq 'yue'"}
                               余额支付
                               {elseif condition="$orderInfo['pay_type'] eq 'zhifubao'"}
                               支付宝支付
                               {else/}
                               其他支付
                               {/if}
                            {else/}
                              未支付
                            {/if}
                        </div>
                        {notempty name="orderInfo.pay_time"}
                        <div class="col-xs-6">支付时间: {$orderInfo.pay_time|date="Y/m/d H:i",###}</div>
                        {/notempty}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__FRAME_PATH}js/content.min.js?v=1.0.0"></script>
{/block}
{block name="script"}

{/block}
