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

namespace app\admin\model\merchant;

use traits\ModelTrait;
use basic\ModelBasic;
use think\Url;
use app\admin\model\special\Lecturer;
use app\admin\model\wechat\WechatUser;
use service\SystemConfigService;
use service\WechatTemplateService;
use app\wap\model\routine\RoutineTemplate;
use app\admin\model\user\User;
use app\wap\model\wap\SmsTemplate;
use service\AliMessageService;


/**
 * Class UserEnter 讲师申请
 * @package app\admin\model\merchant
 */
class UserEnter extends ModelBasic
{
    use ModelTrait;

    //设置where条件
    public static function setWhere($where, $alirs = '', $model = null)
    {
        $model = $model === null ? new self() : $model;
        $model = $alirs !== '' ? $model->alias($alirs) : $model;
        $alirs = $alirs === '' ? $alirs : $alirs . '.';
        $model = $model->where("{$alirs}is_del", 0);
        if ($where['title'] && $where['title']) $model = $model->where("{$alirs}merchant_name", 'LIKE', "%$where[title]%");
        return $model;
    }

    /**讲师列表
     * @param $where
     * @return array
     * @throws \think\Exception
     */
    public static function getLecturerList($where)
    {
        $data = self::setWhere($where)->order('id desc')->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as $key => &$value) {
            $value['address'] = $value['province'] . $value['city'] . $value['district'] . $value['address'];
            $value['success_time'] = date('Y-m-d H:i:s', $value['success_time']);
            $value['fail_time'] = date('Y-m-d H:i:s', $value['fail_time']);
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**
     * 删除讲师申请
     * @param $id
     * @return bool|int
     * @throws \think\exception\DbException
     */
    public static function delLecturer($id)
    {
        $lecturer = self::get($id);
        if (!$lecturer) return self::setErrorInfo('删除的数据不存在');
        return self::where('id', $id)->delete();
    }

    /**审核失败
     * @param $id
     * @param $fail_msg
     * @return bool
     * @throws \think\exception\DbException
     */
    public static function changeFail($id, $uid, $fail_message, $enter)
    {
        $fail_time = time();
        $status = -1;
        try {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), WechatTemplateService::EXAMINE_RESULT, [
                    'first' => '尊敬的用户，您提交讲师入住申请审核结果已出。',
                    'keyword1' => '审核失败',
                    'keyword2' => $fail_message,
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '谢谢支持！'
                ], Url::build('wap/spread/spread', [], true, true));
            } else {
                $dat['phrase5']['value'] = '审核失败';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '失败原因:' . $fail_message;
                RoutineTemplate::sendExamineResult($dat, $uid, Url::build('wap/spread/spread', [], true, true));
            }
        } catch (\Exception $e) {
        }
        $site_name = SystemConfigService::get('site_name');
        $sms_platform_selection = SystemConfigService::get('sms_platform_selection');
        if ($sms_platform_selection == 1) {
            $approval_failed_template_id = SystemConfigService::get('approval_failed_template_id');//审核未通过模版ID
            if ($approval_failed_template_id) {
                $data['site_name'] = $site_name;
                AliMessageService::sendmsg($enter['link_tel'], $approval_failed_template_id, $data);
            }
        } else {
            $data['site_name'] = $site_name;
            $data['phone'] = $enter['link_tel'];
            SmsTemplate::sendSms($uid, $data, 'ENTRY_FAILED');
        }
        return self::edit(compact('fail_time', 'fail_message', 'status'), $id);
    }

    /**审核成功
     * @param $id
     * @return bool
     */
    public static function changeSuccess($id, $uid, $enter)
    {
        $success_time = time();
        $status = 1;
        try {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), WechatTemplateService::EXAMINE_RESULT, [
                    'first' => '尊敬的用户，您提交讲师入住申请审核结果已出。',
                    'keyword1' => '审核成功',
                    'keyword2' => '提交的信息符合标准',
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '感恩您的参与和支持，谢谢！'
                ], Url::build('wap/spread/spread', [], true, true));
            } else {
                $dat['phrase5']['value'] = '审核成功';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '感恩您的参与和支持，谢谢！';
                RoutineTemplate::sendExamineResult($dat, $uid, Url::build('wap/spread/spread', [], true, true));
            }
        } catch (\Exception $e) {
        }
        $site_name = SystemConfigService::get('site_name');
        $sms_platform_selection = SystemConfigService::get('sms_platform_selection');
        if ($sms_platform_selection == 1) {
            $approved_template_id = SystemConfigService::get('approved_template_id');//审核通过模版ID
            if ($approved_template_id) {
                $data['site_name'] = $site_name;
                $data['phone'] = $enter['link_tel'];
                $data['pwd'] = '123456';
                AliMessageService::sendmsg($enter['link_tel'], $approved_template_id, $data);
            }
        } else {
            $site_name = SystemConfigService::get('site_name');
            $data['site_name'] = $site_name;
            $data['phone'] = $enter['link_tel'];
            $data['pwd'] = '123456';
            SmsTemplate::sendSms($uid, $data, 'SETTLED_THROUGH');
        }
        return self::edit(compact('status', 'success_time'), $id);
    }
}
