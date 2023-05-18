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

namespace app\wap\model\wap;

use service\sms\storage\Sms;
use app\admin\model\system\SystemMessage;
use app\wap\model\user\User;
use service\SystemConfigService;

/**
 * 发送短信消息
 * Class SmsTemplate
 * @package app\wap\model\wap
 */
class SmsTemplate
{
    /**发送短信
     * @param $uid
     * @param $data
     * @param $template_const
     * @return bool
     * @throws \Exception
     */
    public static function sendSms($uid, $data, $template_const)
    {
        $sms_platform_selection = SystemConfigService::get('sms_platform_selection');
        if ($sms_platform_selection == 1) return true;
        $message = SystemMessage::getSystemMessage($template_const);
        if ($message['is_sms'] && $message['temp_id']) {
            $user = User::where('uid', $uid)->field('phone,nickname')->find();
            $phone = $user['phone'];
            if ($template_const == 'ORDER_POSTAGE_SUCCESS') {
                $data['nickname'] = $user['nickname'];
            } elseif ($template_const == 'ORDER_POSTAGE_SUCCESS' || $template_const == 'ORDER_TAKE_SUCCESS') {
                if (isset($data['phone']) && $data['phone'] != '') {
                    $phone = $data['phone'];
                    unset($data['phone']);
                }
            } else if ($template_const == 'SETTLED_THROUGH' || $template_const == 'ENTRY_FAILED') {
                $phone = $data['phone'];
                if ($template_const == 'ENTRY_FAILED') unset($data['phone']);
            }
            $smsHandle = new Sms();
            $res = $smsHandle->send($phone, $message['temp_id'], $data);
            if ($res['Code'] == 'OK') {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}
