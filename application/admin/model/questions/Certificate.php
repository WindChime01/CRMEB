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

namespace app\admin\model\questions;

use traits\ModelTrait;
use basic\ModelBasic;
use service\SystemConfigService;
use app\admin\model\merchant\Merchant;
use service\WechatTemplateService;
use app\wap\model\routine\RoutineTemplate;
use app\admin\model\wechat\WechatUser;

/**
 * 证书 Model
 * Class Certificate
 * @package app\admin\model\questions
 */
class Certificate extends ModelBasic
{
    use ModelTrait;

    /**条件
     * @param $where
     */
    public static function setWhere($where = [])
    {
        $model = self::where(['is_del' => 0]);
        if (isset($where['obtain']) && $where['obtain'] > 0) $model = $model->where('obtain', $where['obtain']);
        if (isset($where['title']) && $where['title'] != '') $model = $model->where('title', 'like', "%$where[title]%");
        if (isset($where['mer_id']) && $where['mer_id'] != '') {
            $model = $model->where('mer_id', $where['mer_id']);
        }
        if (isset($where['status']) && $where['status'] != '') {
            $model = $model->where('status', $where['status']);
        } else {
            $model = $model->where('status', 'in', [-1, 0]);
        }
        return $model;
    }

    /**证书列表
     * @param $where 条件
     */
    public static function getCertificateList($where)
    {
        $data = self::setWhere($where)->page((int)$where['page'], (int)$where['limit'])->order('sort desc,add_time desc')->select();
        foreach ($data as $key => &$value) {
            switch ($value['obtain']) {
                case 1:
                    $value['obtains'] = '课程';
                    break;
                case 2:
                    $value['obtains'] = '考试';
                    break;
            }
            if ($value['mer_id']) {
                $value['mer_name'] = Merchant::where('id', $value['mer_id'])->value('mer_name');
            } else {
                $value['mer_name'] = '总平台';
            }
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**证书列表
     * @param $where 条件
     */
    public static function get_certificate_examine_list($where)
    {
        $data = self::setWhere($where)->page((int)$where['page'], (int)$where['limit'])->order('sort desc,add_time desc')->select();
        foreach ($data as $key => &$value) {
            switch ($value['obtain']) {
                case 1:
                    $value['obtains'] = '课程';
                    break;
                case 2:
                    $value['obtains'] = '考试';
                    break;
            }
            if ($value['mer_id']) {
                $value['mer_name'] = Merchant::where('id', $value['mer_id'])->value('mer_name');
            } else {
                $value['mer_name'] = '总平台';
            }
            $value['fail_time'] = date('Y-m-d H:i:s', $value['fail_time']);
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**
     * 证书列表
     */
    public static function certificateList()
    {
        $list = self::where(['is_del' => 0])->order('sort desc,add_time desc')->select();
        return $list;
    }

    /**审核失败
     * @param $id
     * @param $fail_msg
     * @return bool
     * @throws \think\exception\DbException
     */
    public static function changeFail($id, $mer_id, $fail_message)
    {
        $fail_time = time();
        $status = -1;
        $uid = Merchant::where('id', $mer_id)->value('uid');
        try {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), WechatTemplateService::EXAMINE_RESULT, [
                    'first' => '尊敬的讲师，您添加的荣誉证书审核结果已出。',
                    'keyword1' => '审核失败',
                    'keyword2' => $fail_message,
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '感恩您的努力付出，谢谢！'
                ], '');
            } else {
                $dat['phrase5']['value'] = '荣誉证书审核失败';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '荣誉证书失败原因:' . $fail_message;
                RoutineTemplate::sendExamineResult($dat, $uid, '');
            }
        } catch (\Exception $e) {
        }
        return self::edit(compact('fail_time', 'fail_message', 'status'), $id);
    }

    /**审核成功
     * @param $id
     * @return bool
     */
    public static function changeSuccess($id, $mer_id)
    {
        $success_time = time();
        $status = 1;
        $uid = Merchant::where('id', $mer_id)->value('uid');
        try {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), WechatTemplateService::EXAMINE_RESULT, [
                    'first' => '尊敬的讲师，您添加的荣誉证书审核结果已出。',
                    'keyword1' => '审核成功',
                    'keyword2' => '荣誉证书信息符合标准',
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '感恩您的努力付出，谢谢！'
                ], '');
            } else {
                $dat['phrase5']['value'] = '荣誉证书审核成功';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '您添加的荣誉证书审核结果已出！';
                RoutineTemplate::sendExamineResult($dat, $uid, '');
            }
        } catch (\Exception $e) {
        }
        return self::edit(compact('status', 'success_time'), $id);
    }
}
