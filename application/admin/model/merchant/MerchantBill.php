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

use service\SystemConfigService;
use traits\ModelTrait;
use basic\ModelBasic;
use service\PhpSpreadsheetService;
use app\admin\model\merchant\Merchant;

/**
 * 讲师金额明细 model
 * Class UserBill
 * @package app\admin\model\merchant
 */
class MerchantBill extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }


    public static function income($title, $link_id, $mer_id, $category, $type, $number, $balance, $mark = '')
    {
        $pm = 1;
        return self::set(compact('title', 'link_id', 'mer_id', 'category', 'type', 'number', 'balance', 'mark', 'pm'));
    }

    public static function expend($title, $link_id, $mer_id, $category, $type, $number, $balance, $mark = '')
    {
        $pm = 0;
        return self::set(compact('title', 'link_id', 'mer_id', 'category', 'type', 'number', 'balance', 'mark', 'pm'));
    }

    public static function setWhere($where)
    {
        $model = new self();
        $model = $model->alias('b');
        if (isset($where['mer_id']) && $where['mer_id']) $model = $model->where('b.mer_id', $where['mer_id']);
        if ($where['start_time'] && $where['end_time']) $model = $model->where('b.add_time', 'between', [strtotime($where['start_time']), strtotime($where['end_time'])]);
        if ($where['category'] && $where['category'] != '') $model = $model->where('b.category', $where['category']);
        if ($where['category'] == 'now_money') {
            $model = $model->where('b.type', 'in', ['extract', 'user_refund', 'user_pay', 'gold_extract']);
        } else {
            $model = $model->where('b.type', 'in', ['extract', 'gold_turn_balance']);
        }
        $model = $model->join('merchant m', 'b.mer_id=m.id')->field('b.*,m.real_name');
        return $model;
    }

    /*
     *  获取佣金记录
     * */
    public static function getBillList($where)
    {
        $model = self::setWhere($where)->order('b.add_time desc');
        $list = $model->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($list) ? $list->toArray() : [];
        foreach ($data as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['_type'] = $item['pm'] ? '收入' : '支出';
            $item['category'] = self::get_category($item['category']);
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    public static function get_category($category)
    {
        $gold_name = SystemConfigService::get("gold_name");
        switch ($category) {
            case 'now_money':
                $title = '余额';
                break;
            case 'gold_num':
                $title = $gold_name;
                break;
        }
        return $title;
    }

    /**流水导出
     * @param $where
     */
    public static function SaveExport($where)
    {
        $gold_name = SystemConfigService::get("gold_name");
        $data = ($data = self::setWhere($where)->select()) && count($data) ? $data->toArray() : [];
        $export = [];
        foreach ($data as $value) {
            $export[] = [
                $value['mer_id'],
                $value['pm'] ? '收入' : '支出',
                $value['title'],
                $value['number'],
                date('Y-m-d H:i:s', $value['add_time']),
                $value['mark'],
            ];
        }
        if ($where['category'] == 'now_money') {
            $filename = '讲师资金流水' . time() . '.xlsx';
        } else {
            $filename = '讲师' . $gold_name . '流水' . time() . '.xlsx';
        }
        $head = ['讲师ID', '类型', '标题', '金额', '创建时间', '备注'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }
}
