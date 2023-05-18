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

namespace app\wap\model\routine;

use app\wap\model\user\WechatUser;
use service\RoutineTemplateService;
use app\admin\model\wechat\StoreService as ServiceModel;
use app\admin\model\wechat\RoutineTemplate as RoutineTemplateModel;

/**
 * 发送订阅消息
 * Class RoutineTemplate
 * @package app\wap\model\routine
 */
class RoutineTemplate
{

    /**
     * 订单支付成功发送模板消息
     * @param string $formId
     * @param string $orderId
     */
    public static function sendOrderSuccess(array $data, $uid, $link = '')
    {
        RoutineTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), RoutineTemplateService::setTemplateId(RoutineTemplateService::ORDER_PAY_SUCCESS), $link, $data);
    }

    /**管理员通知
     * @param array $data
     * @param null $url
     * @param string $defaultColor
     * @return bool
     */
    public static function sendAdminNoticeTemplate(array $data, $url = null, $defaultColor = '')
    {
        $kefuIds = ServiceModel::where('notify', 1)->column('uid');
        $adminList = array_unique($kefuIds);
        if (!is_array($adminList) || empty($adminList)) return false;
        foreach ($adminList as $uid) {
            try {
                $openid = WechatUser::uidToOpenid($uid);
            } catch (\Exception $e) {
                continue;
            }
            RoutineTemplateService::sendTemplate($openid, RoutineTemplateService::setTemplateId(RoutineTemplateService::ORDER_PAY_SUCCESS), '', $data);
        }
    }

    /**
     * 账户变动订阅消息
     * $userinfo 用户消息
     * */
    public static function sendAccountChanges(array $data, $uid, $link = '')
    {
        RoutineTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), RoutineTemplateService::setTemplateId(RoutineTemplateService::USER_BALANCE_CHANGE), $link, $data);
    }

    /**审核结果通知
     * @param array $data
     * @param $uid
     * @param string $link
     */
    public static function sendExamineResult(array $data, $uid, $link = '')
    {
        RoutineTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), RoutineTemplateService::setTemplateId(RoutineTemplateService::EXAMINE_RESULT), $link, $data);
    }

    /**
     * 订单发货提醒
     * @param int $oid
     * @param array $postageData
     * @return bool
     */
    public static function sendOrderGoods(array $data, $uid, $link = '')
    {
        RoutineTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), RoutineTemplateService::setTemplateId(RoutineTemplateService::ORDER_POSTAGE_SUCCESS), $link, $data);
    }

    /**订单收货提醒
     * @param array $data
     * @param $uid
     * @param string $link
     */
    public static function sendReceivingGoods(array $data, $uid, $link = '')
    {
        RoutineTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), RoutineTemplateService::setTemplateId(RoutineTemplateService::ORDER_TAKE_SUCCESS), $link, $data);
    }

    /**
     * 退款成功发送消息
     * @param array $order
     */
    public static function sendOrderRefundSuccess($data = array(), $uid, $link = '')
    {
        RoutineTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), RoutineTemplateService::setTemplateId(RoutineTemplateService::ORDER_REFUND_STATUS), $link, $data);
    }

    /**
     * 活动报名成功发送消息
     * @param array $order
     */
    public static function sendSignUpSuccess($data = array(), $uid, $link = '')
    {
        RoutineTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), RoutineTemplateService::setTemplateId(RoutineTemplateService::ORDER_USER_SIGN_UP_SUCCESS), $link, $data);
    }

    /**开播提醒
     * @param array $data
     * @param $uid
     * @param string $link
     */
    public static function sendBroadcastReminder($data = array(), $uid, $link = '')
    {
        RoutineTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), RoutineTemplateService::setTemplateId(RoutineTemplateService::LIVE_BROADCAST), $link, $data);
    }

    /**拼团进度提醒
     * @param array $data
     * @param $uid
     * @param string $link
     */
    public static function sendListProgress($data = array(), $uid, $link = '')
    {
        RoutineTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), RoutineTemplateService::setTemplateId(RoutineTemplateService::PINK_ORDER_REMIND), $link, $data);
    }

    /**拼团成功提醒
     * @param array $data
     * @param $uid
     * @param string $link
     */
    public static function sendOrderSuccessfully($data = array(), $uid, $link = '')
    {
        RoutineTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), RoutineTemplateService::setTemplateId(RoutineTemplateService::ORDER_USER_GROUPS_SUCCESS), $link, $data);
    }

    /**拼团失败提醒
     * @param array $data
     * @param $uid
     * @param string $link
     */
    public static function sendOrderFail($data = array(), $uid, $link = '')
    {
        RoutineTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), RoutineTemplateService::setTemplateId(RoutineTemplateService::ORDER_USER_GROUPS_LOSE), $link, $data);
    }

    /**获取用户相关的订阅消息模版ID
     * @param $type
     * @param int $id
     * @return string
     */
    public static function getTemplateIdList($type, $id = 0)
    {
        $list = RoutineTemplateModel::create_template($type, $id);
        $templateIds = implode(',', $list);
        return $templateIds;
    }
}
