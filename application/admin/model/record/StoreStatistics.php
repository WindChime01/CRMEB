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

namespace app\admin\model\record;

use traits\ModelTrait;
use basic\ModelBasic;
use service\ExportService;
use app\admin\model\user\UserBill;
use app\admin\model\user\User;

/**
 * Class StoreStatistics
 * @package app\admin\model\record
 */
class StoreStatistics extends ModelBasic
{
    protected $name = 'store_order';

    use ModelTrait;

    const order = 'StoreStatistics';

    /**
     * 处理金额
     * @param $where
     * @return array
     */
    public static function getOrderPrice($where)
    {
        $model = new self;
        $price = array();
        $price['pay_price_wx'] = 0;//微信支付金额
        $price['pay_price_yue'] = 0;//余额支付金额
        $price['pay_price_offline'] = 0;//线下支付金额
        $list = self::getTimeWhere($where, $model)->field('pay_price,total_price,deduction_price,coupon_price,total_postage,pay_type,pay_time')->select()->toArray();
        if (empty($list)) {
            $price['pay_price_wx'] = 0;
            $price['pay_price_yue'] = 0;
            $price['pay_price_offline'] = 0;
        }
        foreach ($list as $v) {
            if ($v['pay_type'] == 'weixin') {
                $price['pay_price_wx'] = bcadd($price['pay_price_wx'], $v['pay_price'], 2);
            } elseif ($v['pay_type'] == 'yue') {
                $price['pay_price_yue'] = bcadd($price['pay_price_yue'], $v['pay_price'], 2);
            } elseif ($v['pay_type'] == 'offline') {
                $price['pay_price_offline'] = bcadd($price['pay_price_offline'], $v['pay_price'], 2);
            }
        }
        return $price;
    }

    /**
     * 获取营业数据
     */
    public static function getOrderInfo($where)
    {
        $orderinfo = self::getTimeWhere($where)
            ->field('sum(total_price) total_price,sum(cost) cost,sum(pay_postage) pay_postage,sum(pay_price) pay_price,sum(coupon_price) coupon_price,sum(deduction_price) deduction_price,from_unixtime(pay_time,\'%Y-%m-%d\') pay_time')->order('pay_time')->group('from_unixtime(pay_time,\'%Y-%m-%d\')')->select()->toArray();
        $price = 0;
        $postage = 0;
        $deduction = 0;
        $coupon = 0;
        $cost = 0;
        foreach ($orderinfo as $info) {
            $price = bcadd($price, $info['total_price'], 2);//应支付
            $postage = bcadd($postage, $info['pay_postage'], 2);//邮费
            $deduction = bcadd($deduction, $info['deduction_price'], 2);//抵扣
            $coupon = bcadd($coupon, $info['coupon_price'], 2);//优惠券
            $cost = bcadd($cost, $info['cost'], 2);//成本
        }
        return compact('orderinfo', 'price', 'postage', 'deduction', 'coupon', 'cost');
    }

    /**
     * 处理where条件
     */
    public static function statusByWhere($status, $model = null)
    {
        if ($model == null) $model = new self;
        if ('' === $status)
            return $model;
        else if ($status == 'weixin')//微信支付
            return $model->where('pay_type', 'weixin');
        else if ($status == 'yue')//余额支付
            return $model->where('pay_type', 'yue');
        else if ($status == 'offline')//线下支付
            return $model->where('pay_type', 'offline');
        else
            return $model;
    }

    public static function getTimeWhere($where, $model = null)
    {
        return self::getTime($where)->where('paid', 1)->where('refund_status', 0);
    }

    /**
     * 获取时间区间
     */
    public static function getTime($where, $model = null, $prefix = 'add_time')
    {
        if ($model == null) $model = new self;
        if ($where['data'] == '') {
            switch ($where['date']) {
                case 'today':
                case 'week':
                case 'month':
                case 'year':
                    $model = $model->whereTime($prefix, $where['date']);
                    break;
                case 'quarter':
                    list($startTime, $endTime) = User::getMonth('n');
                    $model = $model->where($prefix, '>', strtotime($startTime));
                    $model = $model->where($prefix, '<', strtotime($endTime));
                    break;
            }
        } else {
            list($startTime, $endTime) = explode(' - ', $where['data']);
            $model = $model->where($prefix, '>', strtotime($startTime));
            $model = $model->where($prefix, '<', strtotime($endTime));
        }
        return $model;
    }

    /**
     * 获取新增消费
     */
    public static function getConsumption($where)
    {
        $consumption = self::getTime($where, new UserBill, 'b.add_time')->alias('a')->join('user b', 'a.uid = b.uid')
            ->field('sum(a.number) number')
            ->where('a.type', 'pay_product')->find()->toArray();
        return $consumption;
    }

    /**
     * 获取拼团商品
     */
    public static function getPink($where)
    {
        $pink = self::getTimeWhere($where)->where('pink_id', 'neq', 0)->sum('pay_price');
        return $pink;
    }

    /**
     * 获取秒杀商品
     */
    public static function getSeckill($where)
    {
        $seckill = self::getTimeWhere($where)->where('seckill_id', 'neq', 0)->sum('pay_price');
        return $seckill;
    }

    /**
     * 获取普通商品数
     */
    public static function getOrdinary($where)
    {
        $ordinary = self::getTimeWhere($where)->where('pink_id', 'eq', 0)->where('seckill_id', 'eq', '0')->sum('pay_price');
        return $ordinary;
    }

    /**
     * 获取用户充值
     */
    public static function getRecharge($where)
    {
        $Recharge = self::getTime($where, new UserBill)->where('type', 'system_add')->where('category', 'now_money')->sum('number');
        return $Recharge;
    }

    /**
     * 获取推广金
     */
    public static function getExtension($where)
    {
        $rake_back = self::getTime($where, new UserBill)->where('type', 'brokerage')->where('category', 'now_money')->sum('number');
        $return = self::getTime($where, new UserBill)->where('type', 'brokerage_return')->where('category', 'now_money')->sum('number');
        $extension = bcsub($rake_back, $return, 2);
        return $extension;
    }

    /**
     * 最近交易
     */
    public static function trans()
    {
        $trans = self::alias('a')
            ->join('user b', 'a.uid=b.uid')
            ->join('store_order_cart_info c', 'a.id=c.oid')
            ->join('store_product d', 'c.product_id=d.id')
            ->field('b.nickname,a.pay_price,d.store_name')
            ->order('a.add_time DESC')
            ->limit('6')
            ->select()->toArray();
        return $trans;
    }
}
