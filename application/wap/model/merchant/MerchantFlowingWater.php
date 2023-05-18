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

namespace app\wap\model\merchant;


use traits\ModelTrait;
use basic\ModelBasic;
use app\wap\model\merchant\MerchantBill;

/**
 * Class MerchantFlowingWater
 * @package app\admin\model\merchant
 */
class MerchantFlowingWater extends ModelBasic
{
    use ModelTrait;

    /**
     * @param $order
     * @param int $type
     */
    public static function setMerchantFlowingWater($order, $type = 0)
    {
        if ($order['mer_id'] <= 0) return true;
        $mer_id = $order['mer_id'];//讲师ID
        $mer_divide = Merchant::where('id', $mer_id)->field('now_money,mer_special_divide,mer_store_divide,mer_event_divide,mer_data_divide,mer_test_divide')->find();//讲师分成
        switch ($type) {
            case 0://课程订单
                $divide = bcdiv($mer_divide['mer_special_divide'], 100, 2);//百分比
                if ($divide <= 0) return true;
                $data['total_price'] = $order['total_price'];
                $data['pay_price'] = $order['pay_price'];
                $data['price'] = bcmul($data['total_price'], $divide, 2);
                $title = '购买课程';
                $mark = $title . '支付' . floatval($data['pay_price']) . '元';
                break;
            case 2://商品订单
                $divide = bcdiv($mer_divide['mer_store_divide'], 100, 2);//百分比
                if ($divide <= 0) return true;
                $data['total_price'] = $order['total_price'];
                $data['pay_price'] = $order['pay_price'];
                $price = bcmul($data['total_price'], $divide, 2);
                $data['price'] = bcadd($price, $order['total_postage'], 2);
                $title = '购买商品';
                $mark = $title . '支付' . floatval($data['pay_price']) . '元';
                break;
            case 3://资料订单
                $divide = bcdiv($mer_divide['mer_data_divide'], 100, 2);//百分比
                if ($divide <= 0) return true;
                $data['total_price'] = $order['total_price'];
                $data['pay_price'] = $order['pay_price'];
                $data['price'] = bcmul($data['total_price'], $divide, 2);
                $title = '购买资料';
                $mark = $title . '支付' . floatval($data['pay_price']) . '元';
                break;
            case 4://报名订单
                $divide = bcdiv($mer_divide['mer_event_divide'], 100, 2);//百分比
                if ($divide <= 0) return true;
                $data['total_price'] = $order['pay_price'];
                $data['pay_price'] = $order['pay_price'];
                $data['price'] = bcmul($data['total_price'], $divide, 2);
                $title = '活动报名';
                $mark = $title . '支付' . floatval($data['pay_price']) . '元';
                break;
            case 5://试卷订单
                $divide = bcdiv($mer_divide['mer_test_divide'], 100, 2);//百分比
                if ($divide <= 0) return true;
                $data['total_price'] = $order['total_price'];
                $data['pay_price'] = $order['pay_price'];
                $data['price'] = bcmul($data['total_price'], $divide, 2);
                $title = '购买试卷';
                $mark = $title . '支付' . floatval($data['pay_price']) . '元';
                break;
        }

        Merchant::beginTrans();
        $data['oid'] = $order['id'];
        $data['mer_id'] = $mer_id;
        $data['type'] = $type;
        $data['status'] = 1;
        $data['add_time'] = time();
        $price = $data['price'];
        $res = self::set($data);
        $res1 = true;
        if ($price > 0 && $mer_id) {
            $res1 = Merchant::setMerchantNowMoney($mer_id, $price);
        }
        if ($res && $res1) {
            MerchantBill::income($title, $order['id'], $mer_id, 'now_money', 'user_pay', $price, bcadd($mer_divide['now_money'], $price, 2), $mark);
            Merchant::commitTrans();
            return true;
        } else {
            Merchant::rollbackTrans();
            return false;
        }
    }

    /**收益统计
     * @param int $mer_id
     * @return mixed
     */
    public static function get_merchant_data($mer_id = 0)
    {
        $now_day = strtotime(date('Y-m-d'));//今日
        $profit = MerchantBill::where(['mer_id' => $mer_id, 'pm' => 1, 'category' => 'now_money'])->where('type', 'in', ['gold_extract', 'user_pay'])->sum('number');
        $return = MerchantBill::where(['mer_id' => $mer_id, 'pm' => 0, 'category' => 'now_money'])->where('type', 'in', ['user_refund'])->sum('number');
        $data['total'] = bcsub($profit, $return, 2);
        $today_profit = MerchantBill::where(['mer_id' => $mer_id, 'pm' => 1, 'category' => 'now_money'])->where('add_time', 'gt', $now_day)->where('type', 'in', ['gold_extract', 'user_pay'])->sum('number');
        $today_return = MerchantBill::where(['mer_id' => $mer_id, 'pm' => 0, 'category' => 'now_money'])->where('add_time', 'gt', $now_day)->where('type', 'in', ['user_refund'])->sum('number');
        $data['today'] = bcsub($today_profit, $today_return, 2);
        return $data;
    }
}
