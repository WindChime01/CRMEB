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

namespace app\wap\model\material;

use traits\ModelTrait;
use basic\ModelBasic;
use app\wap\model\topic\TestPaper;
use app\wap\model\merchant\MerchantFlowingWater;
use app\wap\model\user\User;
use app\wap\model\user\UserBill;
use app\wap\model\user\WechatUser;
use app\wap\model\routine\RoutineTemplate;
use behavior\wap\StoreProductBehavior;
use behavior\wechat\PaymentBehavior;
use service\AlipayTradeWapService;
use service\HookService;
use service\SystemConfigService;
use service\WechatService;
use service\WechatTemplateService;
use app\wap\model\wap\SmsTemplate;
use think\Cache;
use think\Url;

/**
 * 资料订单 model
 * Class DataDownloadOrder
 */
class DataDownloadOrder extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected static $payType = ['weixin' => '微信支付', 'yue' => '余额支付', 'offline' => '线下支付', 'zhifubao' => '支付宝'];


    protected function setAddTimeAttr()
    {
        return time();
    }

    public static function getNewOrderId()
    {
        $count = (int)self::where('add_time', ['>=', strtotime(date("Y-m-d"))], ['<', strtotime(date("Y-m-d", strtotime('+1 day')))])->count();
        return 'wx' . date('YmdHis', time()) . (10000 + $count + 1);
    }

    /**
     * 创建订单资料订单
     * @param $special
     * @param $pinkId
     * @param $pay_type
     * @param $uid
     * @param $payType
     * @param int $link_pay_uid
     * @param int $total_num
     * @return bool|object
     */
    public static function createDataDownloadOrder($data, $uid, $payType, $total_num = 1)
    {
        if (!array_key_exists($payType, self::$payType)) return self::setErrorInfo('选择支付方式有误!');
        $userInfo = User::getUserData($uid);
        if (!$userInfo) return self::setErrorInfo('用户不存在!');
        $total_price = $data->money;
        if (isset($userInfo['level']) && $userInfo['level'] > 0 && $data->member_pay_type == 1 && $data->member_money > 0) {
            $total_price = $data->member_money;
        } else if (isset($userInfo['level']) && $userInfo['level'] > 0 && $data->member_pay_type == 0) {
            return self::setErrorInfo('会员免费，该资料您不需要支付!');
        }
        $res = DataDownloadBuy::PayDataDownload($data->id, $uid);
        if ($res) return self::setErrorInfo('您已购买资料，无需再次购买!');
        $orderInfo = [
            'uid' => $uid,
            'mer_id' => $data->mer_id,
            'order_id' => self::getNewOrderId(),
            'data_id' => $data->id,
            'total_num' => $total_num,
            'total_price' => $total_price,
            'pay_price' => $total_price,
            'pay_type' => $payType,
            'paid' => 0,
            'unique' => md5(time() . '' . $uid . $data->id),
            'is_del' => 0
        ];
        $order = self::set($orderInfo);
        if (!$order) return self::setErrorInfo('订单生成失败!');
        return $order;
    }

    /**
     * 微信支付 为 0元时 资料
     * @param $order_id
     * @param $uid
     * @return bool
     */
    public static function jsPayDataDownloadPrice($order_id, $uid)
    {
        $orderInfo = self::where('uid', $uid)->where('order_id', $order_id)->where('is_del', 0)->find();
        if (!$orderInfo) return self::setErrorInfo('订单不存在!');
        if ($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        $userInfo = User::getUserData($uid);
        if (!$userInfo) return self::setErrorInfo('用户不存在!');
        self::beginTrans();
        $res1 = UserBill::expend('购买资料', $uid, 'now_money', 'pay_data_download', $orderInfo['pay_price'], $orderInfo['id'], $userInfo['now_money'], '支付' . floatval($orderInfo['pay_price']) . '元购买资料');
        $res2 = self::payDataDownloadSuccess($order_id);
        $res = $res1 && $res2;
        self::checkTrans($res);
        return $res;
    }

    /**资料微信支付
     * @param $orderId
     * @param string $field
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function jsDataDownloadPay($orderId, $field = 'order_id')
    {
        if (is_string($orderId))
            $orderInfo = self::where($field, $orderId)->where('is_del', 0)->find();
        else
            $orderInfo = $orderId;
        if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
        if ($orderInfo['paid']) exception('支付已支付!');
        if ($orderInfo['pay_price'] <= 0) exception('该支付无需支付!');
        $openid = WechatUser::uidToOpenid($orderInfo['uid']);
        return WechatService::jsPay($openid, $orderInfo['order_id'], $orderInfo['pay_price'], 'datadownload', SystemConfigService::get('site_name'));
    }

    /**
     * 微信h5支付
     * @param $orderId
     * @param string $field
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function h5DataDownloadPay($orderId, $field = 'order_id')
    {
        if (is_string($orderId))
            $orderInfo = self::where($field, $orderId)->find();
        else
            $orderInfo = $orderId;
        if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
        if ($orderInfo['paid']) exception('支付已支付!');
        if ($orderInfo['pay_price'] <= 0) exception('该支付无需支付!');
        $site_name = SystemConfigService::get('site_name');
        if (!$site_name) exception('支付参数缺少：请前往后台设置->系统设置-> 填写 网站名称');
        return WechatService::paymentPrepare(null, $orderInfo['order_id'], $orderInfo['pay_price'], 'datadownload', self::getSubstrUTf8($site_name . '-资料购买', 30), '', 'MWEB');
    }

    /**资料余额支付
     * @param $order_id
     * @param $uid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function yueDataDownloadPay($order_id, $uid)
    {
        $orderInfo = self::where('uid', $uid)->where('order_id', $order_id)->where('is_del', 0)->find();
        if (!$orderInfo) return self::setErrorInfo('订单不存在!');
        if ($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        if ($orderInfo['pay_type'] != 'yue') return self::setErrorInfo('该订单不能使用非余额方式支付!');
        $userInfo = User::getUserData($uid);
        if (!$userInfo) return self::setErrorInfo('用户不存在!');
        if ($userInfo['now_money'] < $orderInfo['pay_price']) return self::setErrorInfo('余额不足' . floatval($orderInfo['pay_price']));
        self::beginTrans();
        $res1 = false !== User::bcDec($uid, 'now_money', $orderInfo['pay_price'], 'uid');
        $res2 = UserBill::expend('购买资料', $uid, 'now_money', 'pay_data_download', $orderInfo['pay_price'], $orderInfo['id'], bcsub($userInfo['now_money'], $orderInfo['pay_price'], 2), '余额支付' . floatval($orderInfo['pay_price']) . '元购买资料');
        $res3 = self::payDataDownloadSuccess($order_id);
        $res = $res1 && $res2 && $res3;
        self::checkTrans($res);
        return $res;
    }

    /**
     * //TODO 资料支付成功后
     * @param $orderId
     */
    public static function payDataDownloadSuccess($orderId)
    {
        $order = self::where('order_id', $orderId)->where('is_del', 0)->find();
        if (!$order) return false;
        $res1 = self::where('order_id', $orderId)->where('is_del', 0)->update(['paid' => 1, 'pay_time' => time()]);
        $res2 = true;
        $res3 = true;
        if ($res1) {
            try {
                if ($order['pay_type'] != 'yue') {
                    $res2 = UserBill::expend('购买资料', $order['uid'], $order['pay_type'], 'pay_data_download', $order['pay_price'], $order['id'], 0, '支付' . floatval($order['pay_price']) . '元购买资料');
                }
                $res3 = MerchantFlowingWater::setMerchantFlowingWater($order, 3);
                DataDownloadBuy::setUserDataDownload($orderId, $order['data_id'], $order['uid'], 0);
            } catch (\Throwable $e) {
            }
        }
        $site_url = SystemConfigService::get('site_url');
        try {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                $title = DataDownload::getName($order['data_id']);
                WechatTemplateService::sendTemplate(WechatUser::where('uid', $order['uid'])->value('openid'), WechatTemplateService::ORDER_PAY_SUCCESS, [
                    'first' => '亲，您购买的资料已支付成功',
                    'keyword1' => $title,
                    'keyword2' => $orderId,
                    'keyword3' => $order['pay_price'],
                    'keyword4' => date('Y-m-d H:i:s', time()),
                    'remark' => '点击查看订单详情'
                ], $site_url . Url::build('wap/material/my_material'));
                WechatTemplateService::sendAdminNoticeTemplate($order['mer_id'], [
                    'first' => "亲,您有一个新的资料购买订单",
                    'keyword1' => $title,
                    'keyword2' => $orderId,
                    'keyword3' => $order['pay_price'],
                    'keyword4' => date('Y-m-d H:i:s', time()),
                    'remark' => '请及时处理'
                ]);
            } else {
                $data['character_string1']['value'] = $orderId;
                $data['amount3']['value'] = $order['pay_price'];
                $data['time2']['value'] = date('Y-m-d H:i:s', time());
                $data['thing6']['value'] = '您购买的资料已支付成功！';
                RoutineTemplate::sendOrderSuccess($data, $order['uid'], $site_url . Url::build('wap/material/my_material'));
                $dataAdmin['character_string1']['value'] = $orderId;
                $dataAdmin['amount3']['value'] = $order['pay_price'];
                $dataAdmin['time2']['value'] = date('Y-m-d H:i:s', time());
                $dataAdmin['thing6']['value'] = '您有一个新的资料购买订单！';
                RoutineTemplate::sendAdminNoticeTemplate($dataAdmin);
            }
        } catch (\Throwable $e) {
        }
        $res = $res1 && $res2 && $res3;
        return false !== $res;
    }

}
