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


namespace app\admin\model\ump;

use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\merchant\Merchant;
use service\WechatTemplateService;
use app\wap\model\routine\RoutineTemplate;
use app\admin\model\wechat\WechatUser;
use service\SystemConfigService;
use think\Db;

class EventRegistration extends ModelBasic
{
    use ModelTrait;


    public static function systemPage($where = array())
    {
        $model = self::setWherePage(self::setWhere($where));
        $model = $model->order('add_time DESC');
        $list = $model->page((int)$where['page'], (int)$where['limit'])->select();
        foreach ($list as $key => &$item) {
            $item['address'] = $item['province'] . $item['city'] . $item['district'] . $item['detail'];
            if ($item['mer_id']) {
                $item['mer_name'] = Merchant::where('id', $item['mer_id'])->value('mer_name');
            } else {
                $item['mer_name'] = '总平台';
            }
            if (bcsub($item['signup_start_time'], time(), 0) > 0) {
                $statu = 0;//报名尚未开始
            } elseif (bcsub($item['signup_start_time'], time(), 0) <= 0 && bcsub($item['signup_end_time'], time(), 0) > 0) {
                $statu = 1;//报名开始
            } elseif (bcsub($item['signup_end_time'], time(), 0) <= 0 && bcsub($item['start_time'], time(), 0) > 0) {
                $statu = 2;//报名结束 活动尚未开始
            } elseif (bcsub($item['start_time'], time(), 0) <= 0 && bcsub($item['end_time'], time(), 0) > 0) {
                $statu = 3;//活动中
            } elseif (bcsub($item['end_time'], time(), 0) < 0) {
                $statu = 4;//活动结束
            } else {
                $statu = -1;
            }
            if ($item['statu'] != $statu) {
                $item['statu'] = $statu;
                self::where('id', $item['id'])->update(['statu' => $statu]);
            }
        }
        $count = self::setWherePage(self::setWhere($where))->count();
        return ['count' => $count, 'data' => $list];
    }

    /**
     * 设置搜索条件
     *
     */
    public static function setWhere($where)
    {
        $model = new self;
        if ($where['title'] != '') {
            $model = $model->where('title', 'like', "%$where[title]%");
        }
        if (isset($where['mer_id']) && $where['mer_id'] != '') {
            $model = $model->where('mer_id', $where['mer_id']);
        }
        if (isset($where['status']) && $where['status'] != '') {
            $model = $model->where('status', $where['status']);
        } else {
            $model = $model->where('status', 'in', [-1, 0]);
        }
        if (isset($where['is_show']) && $where['is_show'] !== '') $model = $model->where('is_show', $where['is_show']);
        $model = $model->where('is_del', 0);
        return $model;
    }

    /**
     * 获取连表MOdel
     * @param $model
     * @return object
     */
    public static function getModelObject($where = [])
    {
        $model = new self();
        $model = $model->alias('p');
        if (!empty($where)) {
            $model = $model->group('p.id');
            if (isset($where['title']) && $where['title'] != '') {
                $model = $model->where('p.title|p.id', 'LIKE', "%$where[title]%");
            }
            if (isset($where['mer_id']) && trim($where['mer_id']) != '') {
                $model = $model->where('p.mer_id', $where['mer_id']);
            }
            if (isset($where['order']) && $where['order'] != '') {
                $model = $model->order(self::setOrder($where['order']));
            } else {
                $model = $model->order('p.sort DESC,p.add_time DESC');
            }
        }
        return $model->where('p.status', 1);
    }

    /**
     * 获取活动
     */
    public static function storeEventList($where, $special_source)
    {
        $model = self::getModelObject($where)->where('p.id', 'not in', $special_source)->field(['p.*']);
        $model = $model->page((int)$where['page'], (int)$where['limit']);
        $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        foreach ($data as $key => &$value) {
            if ($value['mer_id']) {
                $value['mer_name'] = Merchant::where('id', $value['mer_id'])->value('mer_name');
            } else {
                $value['mer_name'] = '总平台';
            }
        }
        $count = self::getModelObject($where)->where('p.id', 'not in', $special_source)->count();
        return compact('count', 'data');
    }

    /**删除
     * @param $id
     * @return bool
     */
    public static function delArticleCategory($id)
    {
        $data['is_del'] = 1;
        return self::edit($data, $id);
    }

    /**获取活动
     * @param $id
     */
    public static function eventRegistrationOne($id)
    {
        $event = self::where('id', $id)->find();
        if (!$event) return [];
        $event['signup_start_time'] = date('Y-m-d H:i:s', $event['signup_start_time']);
        $event['signup_end_time'] = date('Y-m-d H:i:s', $event['signup_end_time']);
        $event['start_time'] = date('Y-m-d H:i:s', $event['start_time']);
        $event['end_time'] = date('Y-m-d H:i:s', $event['end_time']);
        $event['activity_rules'] = htmlspecialchars_decode($event['activity_rules']);
        $event['content'] = htmlspecialchars_decode($event['content']);
        return $event;
    }

    public static function eventExamineList($where = array())
    {
        $model = self::setWherePage(self::setWhere($where));
        $model = $model->order('add_time DESC');
        $list = $model->page((int)$where['page'], (int)$where['limit'])->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        foreach ($list as $key => &$item) {
            $item['fail_time'] = date('Y-m-d H:i:s', $item['fail_time']);
            $item['address'] = $item['province'] . $item['city'] . $item['district'] . $item['detail'];
            if ($item['mer_id']) {
                $item['mer_name'] = Merchant::where('id', $item['mer_id'])->value('mer_name');
            } else {
                $item['mer_name'] = '总平台';
            }
        };
        $count = self::setWherePage(self::setWhere($where))->count();
        return ['count' => $count, 'data' => $list];
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
                    'first' => '尊敬的讲师，您添加的活动审核结果已出。',
                    'keyword1' => '审核失败',
                    'keyword2' => $fail_message,
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '感恩您的努力付出，谢谢！'
                ], '');
            } else {
                $dat['phrase5']['value'] = '活动审核失败';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '活动失败原因:' . $fail_message;
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
                    'first' => '尊敬的讲师，您添加的活动审核结果已出。',
                    'keyword1' => '审核成功',
                    'keyword2' => '活动信息符合标准',
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '感恩您的努力付出，谢谢！'
                ], '');
            } else {
                $dat['phrase5']['value'] = '活动审核成功';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '您添加的活动审核结果已出！';
                RoutineTemplate::sendExamineResult($dat, $uid, '');
            }
        } catch (\Exception $e) {
        }
        return self::edit(compact('status', 'success_time'), $id);
    }

}
