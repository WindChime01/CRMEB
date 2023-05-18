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

namespace app\wap\controller;

use app\wap\model\activity\EventRegistration;
use app\wap\model\activity\EventWriteOffUser;
use app\wap\model\activity\EventSignUp;
use app\wap\model\activity\EventData;
use app\wap\model\activity\EventPrice;
use service\JsonService;
use service\SystemConfigService;
use think\Url;

/**
 * 活动控制器
 * Class Activity
 */
class Activity extends AuthController
{

    /**
     * 白名单
     */
    public static function WhiteList()
    {
        return [
            'index',
            'activityList'
        ];
    }

    /**活动列表
     * @return mixed
     */
    public function index()
    {
        return $this->fetch('activity_list');
    }

    /**
     * 获取活动列表
     */
    public function activityList($page = 1, $limit = 20)
    {
        $list = EventRegistration::eventRegistrationList($page, $limit);
        return JsonService::successful($list);
    }

    /**获取活动需要填写的资料
     * @param $id
     */
    public function getActivityEventData($id)
    {
        $event = EventData::eventDataList($id);
        return JsonService::successful($event);
    }

    /**获取活动的价格
     * @param $id
     */
    public function getActivityEventPrice($id)
    {
        $price = EventPrice::eventPriceList($id);
        if (!count($price)) {
            $activity = EventRegistration::where('id', $id)->field('price,member_price')->find();
            $price[0] = [
                'event_id' => $id,
                'event_number' => 1,
                'event_price' => $activity['price'],
                'event_mer_price' => $activity['member_price'],
                'sort' => 0
            ];
        }
        return JsonService::successful($price);
    }

    /**
     * 核销码 搜索
     */
    public function getWriteOffCode($code = '')
    {
        if (!$code) return JsonService::fail('参数有误');
        if (!$this->userInfo['is_write_off']) return JsonService::fail('您没有权限核销!');
        $order = EventSignUp::setWhere()->where('code', $code)->find();
        if (!$order) return JsonService::fail('订单不存在');
        if(!EventWriteOffUser::be(['event_id' => $order['activity_id'], 'uid' => $this->uid, 'is_del' => 0])) return JsonService::fail('您没有该活动的核销权限');
        return JsonService::successful($order);
    }

    /**核销员 核销活动订单
     * @param string $order_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function scanCodeSignIn($order_id = '')
    {
        if (!$order_id) return JsonService::fail('参数有误');
        if (!$this->userInfo['is_write_off']) return JsonService::fail('没有权限核销!');
        $order = EventSignUp::setWhere()->where('order_id', $order_id)->find();
        if (!$order) return JsonService::fail('订单不存在');
        if ($order['status']) return JsonService::fail('该订单已核销');
        if(!EventWriteOffUser::be(['event_id' => $order['activity_id'], 'uid' => $this->uid, 'is_del' => 0])) return JsonService::fail('您没有该活动的核销权限');
        $res = EventSignUp::setWhere()->where('order_id', $order_id)->update(['status' => 1]);
        if ($res) return JsonService::successful('核销成功');
        else return JsonService::fail('核销失败');
    }

    /**获取核销订单详情
     * @param string $order_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function writeOffSignIn($order_id = '')
    {
        if (!$order_id) return JsonService::fail('参数有误');
        if (!$this->userInfo['is_write_off']) return JsonService::fail('没有权限核销!');
        $order = EventSignUp::setWhere()->where(['order_id' => $order_id])->find();
        if (!$order) return JsonService::fail('订单不存在');
        if (!$order['activity_id']) return JsonService::fail('订单有误');
        $activity = EventRegistration::oneActivitys($order['activity_id']);
        if (!$activity) return JsonService::fail('活动不存在');
        $order['activity'] = $activity;
        $order['pay_time'] = date('y/m/d H:i', $order['pay_time']);
        return JsonService::successful($order);
    }

    /**
     * 用户报名活动列表
     */
    public function activitySignInList($page = 1, $limit = 20, $navActive = 0)
    {
        $model = EventSignUp::setWhere('s')->where('s.uid', $this->uid)->page((int)$page, (int)$limit);
        switch ($navActive) {
            case 1:
                $model = $model->where('s.status', 0);
                break;
            case 2:
                $model = $model->where('s.status', 1);
                break;
        }
        $model = $model->join('EventRegistration r', 'r.id = s.activity_id');
        $orderList = $model->order('s.add_time DESC')->field('s.order_id,s.status,s.pay_price,s.activity_id,s.user_info,s.uid,s.code,s.number as upUnmber,r.title,r.image,r.province,r.city,r.statu,r.district,r.detail')->select();
        $orderList = count($orderList) > 0 ? $orderList->toArray() : [];
        return JsonService::successful($orderList);
    }

    /**活动订单详情
     * @param string $order_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function activitySignIn($order_id = '')
    {
        if (!$order_id) return JsonService::fail('参数有误');
        $order = EventSignUp::setWhere()->where(['order_id' => $order_id, 'uid' => $this->uid])->find();
        if (!$order) return JsonService::fail('订单不存在');
        if (!$order['activity_id']) return JsonService::fail('订单有误');
        $activity = EventRegistration::where('id', $order['activity_id'])->field('id,title,image,province,city,statu,district,detail,start_time,end_time,signup_start_time,signup_end_time,price')->find();
        if (!$activity) return JsonService::fail('活动不存在');
        $activity = EventRegistration::singleActivity($activity);
        $start_time = date('y/m/d H:i', $activity['start_time']);
        $end_time = date('y/m/d H:i', $activity['end_time']);
        $activity['time'] = $start_time . '~' . $end_time;
        $order['activity'] = $activity;
        $order['pay_time'] = date('y/m/d H:i', $order['pay_time']);
        if (!$order['write_off_code']) {
            $write_off_code = EventSignUp::qrcodes_url($order_id, 5);
            EventSignUp::where('order_id', $order_id)->update(['write_off_code' => $write_off_code]);
            $order['write_off_code'] = $write_off_code;
        }
        if (!$order['code']) {
            $code = EventSignUp::codes();
            EventSignUp::where('order_id', $order_id)->update(['code' => $code]);
            $order['code'] = $code;
        }
        return JsonService::successful($order);
    }

    /**检测活动状态
     * @param string $order_id
     */
    public function orderStatus($order_id = '')
    {
        if (!$order_id) return JsonService::fail('参数有误');
        $order = EventSignUp::setWhere()->where('order_id', $order_id)->find();
        if (!$order) return JsonService::fail('订单不存在');
        return JsonService::successful($order['status']);
    }
}
