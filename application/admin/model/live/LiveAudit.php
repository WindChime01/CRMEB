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

namespace app\admin\model\live;

/**
 * 直播审核
 */

use basic\ModelBasic;
use traits\ModelTrait;
use app\admin\model\merchant\Merchant;
use service\SystemConfigService;
use service\WechatTemplateService;
use app\wap\model\routine\RoutineTemplate;
use app\admin\model\wechat\WechatUser;

class LiveAudit extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取连表MOdel
     * @param $model
     * @return object
     */
    public static function getModelExamine($where = [])
    {
        $model = new self();
        $model = $model->alias('p');
        if (isset($where['store_name']) && $where['store_name'] != '') {
            $model = $model->where('p.live_title|p.stream_name', 'LIKE', "%$where[store_name]%");
        }
        if (isset($where['order']) && $where['order'] != '') {
            $model = $model->order(self::setOrder($where['order']));
        } else {
            $model = $model->order('p.add_time DESC');
        }
        $model = $model->join('LiveStudio l', 'p.live_id=l.id');
        return $model;
    }

    /*
     * 获取直播审核列表
     * @param $where array
     * @return array
     *
     */
    public static function liveExamineList($where)
    {
        $model = self::getModelExamine($where)->field('p.*');
        $model = $model->page((int)$where['page'], (int)$where['limit']);
        $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        foreach ($data as $key => &$volue) {
            $volue['live_strar_time'] = date('Y-m-d H:i:s', $volue['live_strar_time']);
            $volue['live_end_time'] = date('Y-m-d H:i:s', $volue['live_end_time']);
            $volue['fail_time'] = date('Y-m-d H:i:s', $volue['fail_time']);
        }
        $count = self::getModelExamine($where)->count();
        return compact('count', 'data');
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
                    'first' => '尊敬的讲师，您的直播申请审核结果已出。',
                    'keyword1' => '直播审核失败',
                    'keyword2' => $fail_message,
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '感恩您的努力付出，谢谢！'
                ], '');
            } else {
                $dat['phrase5']['value'] = '直播审核失败';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '直播失败原因:' . $fail_message;
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
                    'first' => '尊敬的讲师，您的直播申请审核结果已出。',
                    'keyword1' => '直播审核成功',
                    'keyword2' => '直播信息符合标准',
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '感恩您的努力付出，谢谢！'
                ], '');
            } else {
                $dat['phrase5']['value'] = '直播审核成功';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '您的直播申请审核结果已出！';
                RoutineTemplate::sendExamineResult($dat, $uid, '');
            }
        } catch (\Exception $e) {
        }
        return self::edit(compact('status', 'success_time'), $id);
    }

}
