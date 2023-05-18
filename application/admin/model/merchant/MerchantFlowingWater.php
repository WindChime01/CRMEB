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
use service\PhpSpreadsheetService;
use app\admin\model\merchant\Merchant;
use app\admin\model\order\StoreOrder;
use app\admin\model\order\DataDownloadOrder;
use app\admin\model\ump\EventSignUp;

/**
 * Class MerchantFlowingWater
 * @package app\admin\model\merchant
 */
class MerchantFlowingWater extends ModelBasic
{
    use ModelTrait;

    /**条件处理
     * @param $where
     * @return mixed
     */
    public static function setWhereList($where)
    {
        $time['data'] = '';
        if ($where['start_time'] != '' && $where['end_time'] != '') {
            $time['data'] = $where['start_time'] . ' - ' . $where['end_time'];
        }
        $model = self::getModelTime($time, self::alias('f')->join('Merchant m', 'm.id=f.mer_id')->order('f.add_time desc'), 'f.add_time');
        if (isset($where['mer_id']) && $where['mer_id']) $model = $model->where('f.mer_id', $where['mer_id']);
        if (isset($where['nickname']) && $where['nickname'] != '') {
            $model = $model->where('m.mer_name|m.real_name', 'like', "%$where[nickname]%");
        }
        $model = $model->where('f.price', '<>', 0);
        return $model->field(['f.*', 'FROM_UNIXTIME(f.add_time,"%Y-%m-%d %H:%i:%s") as add_time', 'm.mer_name']);
    }

    /**讲师流水
     * @param $where
     * @return array
     */
    public static function getBillList($where)
    {
        $data = ($data = self::setWhereList($where)->page((int)$where['page'], (int)$where['limit'])->select()) && count($data) ? $data->toArray() : [];
        foreach ($data as &$v) {
            $v['title'] = self::get_order_type($v['type']);
        }
        $count = self::setWhereList($where)->count();
        return compact('data', 'count');
    }

    /**流水导出
     * @param $where
     */
    public static function SaveExport($where)
    {
        $data = ($data = self::setWhereList($where)->select()) && count($data) ? $data->toArray() : [];
        $export = [];
        foreach ($data as $value) {
            $title = self::get_order_type($value['type']);
            $export[] = [
                $value['mer_id'],
                $value['mer_name'],
                $title,
                $value['total_price'],
                $value['pay_price'],
                $value['refund_price'],
                $value['status'] == 0 ? '-' . $value['price'] : $value['price'],
                $value['add_time'],
            ];
        }
        $filename = '分成记录' . time() . '.xlsx';
        $head = ['讲师ID', '讲师昵称', '订单类型', '订单总价', '实际金额', '退款金额', '分成/退还', '创建时间'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }

    /**订单类型
     * @param $type
     * @return string
     */
    public static function get_order_type($type)
    {
        switch ($type) {
            case 0:
                $title = '课程订单';
                break;
            case 1:
                $title = '会员订单';
                break;
            case 2:
                $title = '商品订单';
                break;
            case 3:
                $title = '资料订单';
                break;
            case 4:
                $title = '报名订单';
                break;
            case 5:
                $title = '考试订单';
                break;
        }
        return $title;
    }

    /**订单退款
     * @param $oid
     */
    public static function orderRefund($oid, $mer_id, $type = 0)
    {
        if (!$oid) return true;
        $mer_divide = Merchant::where('id', $mer_id)->field('now_money,mer_special_divide,mer_store_divide,mer_event_divide,mer_data_divide')->find();//讲师分成
        $water = self::where(['mer_id' => $mer_id, 'oid' => $oid, 'status' => 1])->find();
        if (!$water) return true;
        switch ($type) {
            case 0://课程订单
                $divide = bcdiv($mer_divide['mer_special_divide'], 100, 2);//百分比
                if ($divide <= 0) return true;
                $order = StoreOrder::where(['id' => $oid])->find();
                $price = bcmul($order['refund_price'], $divide, 2);
                break;
            case 2://商品订单
                $divide = bcdiv($mer_divide['mer_store_divide'], 100, 2);//百分比
                if ($divide <= 0) return true;
                $order = StoreOrder::where(['id' => $oid])->find();
                $price = bcmul($order['total_price'], $divide, 2);
                $price = bcadd($price, $order['total_postage'], 2);
                break;
            case 3://资料订单
                $divide = bcdiv($mer_divide['mer_data_divide'], 100, 2);//百分比
                if ($divide <= 0) return true;
                $order = DataDownloadOrder::where(['id' => $oid])->find();
                $price = bcmul($order['refund_price'], $divide, 2);
                break;
            case 4://报名订单
                $divide = bcdiv($mer_divide['mer_event_divide'], 100, 2);//百分比
                if ($divide <= 0) return true;
                $order = EventSignUp::where(['id' => $oid])->find();
                $price = bcmul($order['refund_price'], $divide, 2);

                break;
        }
        if ($price <= 0) return true;
        Merchant::beginTrans();
        $res = Merchant::decMerchantNowMoney($order['mer_id'], $price);
        $data['total_price'] = $water['total_price'];
        $data['pay_price'] = $water['pay_price'];
        $data['refund_price'] = $order['refund_price'];
        $data['price'] = $price;
        $data['oid'] = $oid;
        $data['mer_id'] = $order['mer_id'];
        $data['type'] = $type;
        $data['add_time'] = time();
        $res1 = self::set($data);
        if ($res && $res1) {
            MerchantBill::expend('订单退款', $oid, $order['mer_id'], 'now_money', 'user_refund', $price, bcsub($mer_divide['now_money'], $price, 2), '订单退款' . floatval($data['pay_price']) . '元');
            Merchant::commitTrans();
            return true;
        } else {
            Merchant::rollbackTrans();
            return false;
        }
    }
}
