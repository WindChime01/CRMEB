<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016～2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace behavior\wechat;

use app\wap\model\store\StoreOrder as StoreOrderRoutineModel;
use app\wap\model\store\StoreOrder as StoreOrderWapModel;
use app\wap\model\user\UserRecharge;
use service\HookService;
use service\WechatService;
use app\wap\model\activity\EventSignUp;
use app\wap\model\topic\TestPaperOrder;
use app\wap\model\material\DataDownloadOrder;

class PaymentBehavior
{

    /**
     * 下单成功之后
     * @param $order
     * @param $prepay_id
     */
    public static function wechatPaymentPrepare($order, $prepay_id)
    {

    }

    /**
     * 支付成功后
     * @param $notify
     * @return bool|mixed
     */
    public static function wechatPaySuccess($notify)
    {
        if (isset($notify->attach) && $notify->attach) {
            return HookService::listen('wechat_pay_success_' . strtolower($notify->attach), $notify->out_trade_no, $notify, true, self::class);
        }
        return false;
    }

    /**
     * 商品订单支付成功后  微信公众号
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessGoods($orderId, $notify)
    {
        try {
            if (StoreOrderWapModel::be(['order_id' => $orderId, 'paid' => 1, 'type' => 2])) return true;
            return StoreOrderWapModel::payGoodsSuccess($orderId,$notify->transaction_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 专题订单支付成功后  微信公众号 支付宝
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessSpecial($orderId, $notify)
    {
        try {
            if (StoreOrderWapModel::be(['order_id' => $orderId, 'paid' => 1])) return true;
            return StoreOrderWapModel::paySuccess($orderId);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 会员订单支付成功后  微信公众号 支付宝
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessMember($orderId, $notify)
    {
        try {
            if (StoreOrderWapModel::be(['order_id' => $orderId, 'paid' => 1])) return true;
            return StoreOrderWapModel::payMeSuccess($orderId);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 活动报名订单支付成功后  微信公众号 支付宝
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessSignup($orderId, $notify)
    {
        try {
            if (EventSignUp::be(['order_id' => $orderId, 'paid' => 1])) return true;
            return EventSignUp::paySuccess($orderId);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 试卷订单支付成功后  微信公众号 支付宝
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessTestpaper($orderId, $notify)
    {
        try {
            if (TestPaperOrder::be(['order_id' => $orderId, 'paid' => 1])) return true;
            return TestPaperOrder::payTestPaperSuccess($orderId);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 资料订单支付成功后  微信公众号 支付宝
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessDatadownload($orderId, $notify)
    {
        try {
            if (DataDownloadOrder::be(['order_id' => $orderId, 'paid' => 1])) return true;
            return DataDownloadOrder::payDataDownloadSuccess($orderId);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 商品订单支付成功后  小程序
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessProductr($orderId, $notify)
    {
        try {
            if (StoreOrderRoutineModel::be(['order_id' => $orderId, 'paid' => 1])) return true;
            return StoreOrderRoutineModel::paySuccess($orderId);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 用户充值成功后
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessRecharge($orderId, $notify)
    {
        try {
            if (UserRecharge::be(['order_id' => $orderId, 'paid' => 1])) return true;
            return UserRecharge::rechargeSuccess($orderId);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 使用余额支付订单时
     * @param $userInfo
     * @param $orderInfo
     */
    public static function yuePayProduct($userInfo, $orderInfo)
    {


    }

    /**
     * 微信支付订单退款
     * @param $orderNo
     * @param array $opt
     */
    public static function wechatPayOrderRefund($orderNo, array $opt)
    {
        WechatService::payOrderRefund($orderNo, $opt);
    }

    /**
     * 微信支付充值退款
     * @param $orderNo
     * @param array $opt
     */

    public static function userRechargeRefund($orderNo, array $opt)
    {
        WechatService::payOrderRefund($orderNo, $opt);
    }
}
