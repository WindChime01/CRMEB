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

namespace app\merchant\model\merchant;


use traits\ModelTrait;
use basic\ModelBasic;
use service\PhpSpreadsheetService;
use app\admin\model\merchant\Merchant;
use app\admin\model\order\StoreOrder;

/**
 * Class MerchantFlowingWater
 * @package app\merchant\model\merchant
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
        $model = self::getModelTime($time, self::alias('f')
            ->join('Merchant m', 'm.id=f.mer_id')
            ->order('f.add_time desc'), 'f.add_time');
        if (isset($where['mer_id']) && $where['mer_id']) $model = $model->where('f.mer_id', $where['mer_id']);
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
}
