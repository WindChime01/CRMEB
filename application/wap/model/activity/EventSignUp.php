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

namespace app\wap\model\activity;

use app\wap\model\activity\EventRegistration;
use app\wap\model\activity\EventPrice;
use app\wap\model\merchant\MerchantFlowingWater;
use app\wap\model\user\User;
use app\wap\model\user\UserBill;
use service\HookService;
use traits\ModelTrait;
use basic\ModelBasic;
use think\Db;
use service\SystemConfigService;
use service\WechatService;
use service\WechatTemplateService;
use app\wap\model\user\WechatUser;
use behavior\wechat\PaymentBehavior;
use service\AlipayTradeWapService;
use think\Url;
use app\wap\model\routine\RoutineTemplate;
use app\wap\model\wap\SmsTemplate;

/**报名订单 model
 * Class EventSignUp
 * @package app\wap\model\activity
 */
class EventSignUp extends ModelBasic
{
    use ModelTrait;
    protected $insert = ['add_time'];

    protected static $payType = ['weixin' => '微信支付', 'yue' => '余额支付', 'offline' => '线下支付', 'zhifubao' => '支付宝'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    /**条件处理
     * @param string $alias
     * @param null $model
     * @return EventSignUp
     */
    public static function setWhere($alias = '', $model = null)
    {
        if (is_null($model)) $model = new self();
        if ($alias) {
            $model = $model->alias($alias);
            $alias .= '.';
        }
        return $model->where([$alias . 'paid' => 1, $alias . 'is_del' => 0, $alias . 'refund_status' => 0]);
    }

    public static function getNewOrderId()
    {
        $count = (int)self::where('add_time', ['>=', strtotime(date("Y-m-d"))], ['<', strtotime(date("Y-m-d", strtotime('+1 day')))])->count();
        return 'su' . date('YmdHis', time()) . (10000 + $count + 1);
    }

    /**用户提交报名
     * @param $id
     * @param $userName
     * @param $userPhone
     */
    public static function userEventSignUp($id, $price_id, $event, $payType, $uid)
    {
        if (!array_key_exists($payType, self::$payType)) return self::setErrorInfo('选择支付方式有误!');
        $userInfo = User::getUserData($uid);
        if (!$userInfo) return self::setErrorInfo('用户不存在!');
        $activity = EventRegistration::oneActivitys($id);
        if (!$activity) return self::setErrorInfo('活动不存在!');
        if (bcsub($activity['number'], $activity['count'], 0) <= 0) return self::setErrorInfo('活动报名结束!');
        $userCount = self::setWhere()->where(['uid' => $uid, 'activity_id' => $id])->count();//用户该活动报名次数
        if ($activity['restrictions'] && bcsub($activity['restrictions'], $userCount, 0) <= 0) return self::setErrorInfo('您的活动报名已超过限额!');
        $priceData = EventPrice::where(['id' => $price_id])->find();
        $payPrice = 0;
        if (isset($userInfo['level']) && $userInfo['level'] > 0 && $priceData['event_mer_price'] > 0) {
            $payPrice = $priceData['event_mer_price'];
        } elseif ($userInfo['level'] == 0 && $priceData['event_price'] > 0) {
            $payPrice = $priceData['event_price'];
        }
        if ($priceData['event_number'] > $activity['number']) return self::setErrorInfo('选择的活动人数过大!');
        if ($priceData['event_number'] > bcsub($activity['number'], $activity['count'], 0)) return self::setErrorInfo('剩余报名人数不足!');
        $order_id = self::getNewOrderId();
        $write_off_code = self::qrcodes_url($order_id);
        $data = [
            'order_id' => $order_id,
            'uid' => $uid,
            'mer_id' => $activity['mer_id'],
            'user_info' => $event,
            'activity_id' => $id,
            'pay_price' => $payPrice,
            'pay_type' => $payType,
            'number' => $priceData['event_number'],
            'write_off_code' => $write_off_code,
            'code' => self::codes()
        ];
        $order = self::set($data);
        if (!$order) return self::setErrorInfo('报名订单生成失败!');
        return $order;
    }

    /**
     * 微信支付 为 0元时
     * @param $order_id
     * @param $uid
     * @return bool
     */
    public static function jsPayPrice($order_id, $uid)
    {
        $orderInfo = self::where('uid', $uid)->where('order_id', $order_id)->where('is_del', 0)->find();
        if (!$orderInfo) return self::setErrorInfo('订单不存在!');
        if ($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        $userInfo = User::getUserData($uid);
        self::beginTrans();
        $res1 = UserBill::expend('活动报名成功', $uid, 'now_money', 'pay_sign_up', $orderInfo['pay_price'], $orderInfo['id'], $userInfo['now_money'], '支付' . floatval($orderInfo['pay_price']) . '元活动报名');
        $res2 = self::paySuccess($order_id);
        $res = $res1 && $res2;
        self::checkTrans($res);
        return $res;
    }

    /**微信js支付
     * @param $orderId
     * @param string $field
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function jsPay($orderId, $field = 'order_id')
    {
        if (is_string($orderId))
            $orderInfo = self::where($field, $orderId)->find();
        else
            $orderInfo = $orderId;
        if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
        if ($orderInfo['paid']) exception('支付已支付!');
        if ($orderInfo['pay_price'] <= 0) exception('该支付无需支付!');
        $openid = WechatUser::uidToOpenid($orderInfo['uid']);
        return WechatService::jsPay($openid, $orderInfo['order_id'], $orderInfo['pay_price'], 'signup', SystemConfigService::get('site_name'));
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
    public static function h5Pay($orderId, $field = 'order_id')
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
        return WechatService::paymentPrepare(null, $orderInfo['order_id'], $orderInfo['pay_price'], 'signup', self::getSubstrUTf8($site_name . '-活动报名', 30), '', 'MWEB');
    }

    /**余额支付
     * @param $order_id
     * @param $uid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function yuePay($order_id, $uid)
    {
        $orderInfo = self::where('uid', $uid)->where('order_id', $order_id)->where('is_del', 0)->find();
        if (!$orderInfo) return self::setErrorInfo('订单不存在!');
        if ($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        if ($orderInfo['pay_type'] != 'yue') return self::setErrorInfo('该订单不能使用余额支付!');
        $userInfo = User::getUserData($uid);
        if ($userInfo['now_money'] < $orderInfo['pay_price']) return self::setErrorInfo('余额不足' . floatval($orderInfo['pay_price']));
        self::beginTrans();
        $res1 = false !== User::bcDec($uid, 'now_money', $orderInfo['pay_price'], 'uid');
        $res2 = UserBill::expend('活动报名', $uid, 'now_money', 'pay_sign_up', $orderInfo['pay_price'], $orderInfo['id'], bcsub($userInfo['now_money'], $orderInfo['pay_price'], 2), '余额支付' . floatval($orderInfo['pay_price']) . '元活动报名');
        $res3 = self::paySuccess($order_id);
        $res = $res1 && $res2 && $res3;
        self::checkTrans($res);
        return $res;
    }

    /**
     * //TODO 支付成功后
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function paySuccess($orderId)
    {
        $order = self::where('order_id', $orderId)->find();
        $res1 = self::where('order_id', $orderId)->update(['paid' => 1, 'pay_time' => time()]);
        $site_url = SystemConfigService::get('site_url');
        $res2 = true;
        $res3 = true;
        try {
            if ($res1 && $order['pay_type'] != 'yue') {
                $res2 = UserBill::expend('活动报名', $order['uid'], $order['pay_type'], 'pay_sign_up', $order['pay_price'], $order['id'], 0, '支付' . floatval($order['pay_price']) . '元活动报名');
            }
            if ($res1) {
                $res3 = MerchantFlowingWater::setMerchantFlowingWater($order, 4);
            }
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            $event = EventRegistration::where('id', $order['activity_id'])->field('title,start_time,end_time,province,city,district,detail')->find();
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate(WechatUser::where('uid', $order['uid'])->value('openid'), WechatTemplateService::ORDER_USER_SIGN_UP_SUCCESS, [
                    'first' => '亲，你已成功报名了活动:'.$event['title'],
                    'keyword1' => date('y/m/d/H/i', $event['start_time']) . '~' . date('y/m/d/H/i', $event['end_time']),
                    'keyword2' => $event['province'] . $event['city'] . $event['district'] . $event['detail'],
                    'remark' => '点击查看报名详情'
                ], $site_url . Url::build('wap/my/sign_list'));
                WechatTemplateService::sendAdminNoticeTemplate($order['mer_id'],[
                    'first' => "亲,您有一个新的活动报名订单",
                    'keyword1' => $event['title'],
                    'keyword2' => $orderId,
                    'keyword3' => $order['pay_price'],
                    'keyword4' => date('Y-m-d H:i:s', time()),
                    'remark' => '请及时查看'
                ]);
            } else {
                $data['thing1']['value'] = $event['title'];
                $data['time2']['value'] = date('y/m/d/H/i', $event['start_time']) . '~' . date('y/m/d/H/i', $event['end_time']);
                $data['thing6']['value'] = $event['province'] . $event['city'] . $event['district'] . $event['detail'];
                RoutineTemplate::sendSignUpSuccess($data, $order['uid'], $site_url . Url::build('wap/my/sign_list'));
                $dataAdmin['character_string1']['value'] = $orderId;
                $dataAdmin['amount3']['value'] = $order['pay_price'];
                $dataAdmin['time2']['value'] = date('Y-m-d H:i:s', time());
                $dataAdmin['thing6']['value'] = '您有一个新的活动报名订单';
                RoutineTemplate::sendAdminNoticeTemplate($dataAdmin);
            }
            $dat['title'] = $event['title'];
            $dat['code'] = $order['code'];
            SmsTemplate::sendSms($order['uid'], $dat, 'ORDER_USER_SIGN_UP_SUCCESS');
        } catch (\Throwable $e) {
        }
        $res = $res1 && $res2 && $res3;
        return false !== $res;
    }

    /**报名二维码
     * @param string $order_id
     * @param int $size
     * @return string
     */
    public static function qrcodes_url($order_id = '', $size = 5)
    {
        vendor('phpqrcode.phpqrcode');
        $urls = SystemConfigService::get('site_url') . '/';
        $url = $urls . 'wap/my/sign_order/type/2/order_id/' . $order_id;
        $value = $url;            //二维码内容
        $errorCorrectionLevel = 'H';    //容错级别
        $matrixPointSize = $size;            //生成图片大小
        //生成二维码图片
        $filename = 'public/qrcode/' . 'su' . rand(10000000, 99999999) . '.png';
        \QRcode::png($value, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        return $urls . $filename;
    }

    /**
     * 核销码
     */
    public static function codes()
    {
        return rand(100000, 999999);
    }

    /**活动报名次数
     * @param $activity_id
     */
    public static function signUpCount($activity_id)
    {
        $count = self::setWhere()->where(['activity_id' => $activity_id])->sum('number');
        $orderCount = self::setWhere()->where(['activity_id' => $activity_id, 'number' => 0])->count();
        $count = bcadd($count, $orderCount, 0);
        return $count;
    }
}
