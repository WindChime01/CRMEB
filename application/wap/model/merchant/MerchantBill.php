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

use service\SystemConfigService;
use traits\ModelTrait;
use basic\ModelBasic;
use service\PhpSpreadsheetService;

/**
 * 讲师金额明细 model
 * Class UserBill
 * @package app\wap\model\merchant
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

    /**讲师流水
     * @param $where
     * @param $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function get_user_flowing_water_list($where)
    {
        $model = self::where('mer_id', $where['mer_id'])->order('add_time desc')->where('number', '<>', 0);
        $model = $model->where('category', $where['category']);
        if ($where['category'] == 'now_money') {
            if ($where['is_extract']) {
                $model = $model->where('type', 'in', ['extract', 'extract_fail']);
            } else {
                $model = $model->where('type', 'in', ['user_refund', 'user_pay', 'gold_extract']);
            }
        } else {
            $model = $model->where('type', 'in', ['extract', 'gold_turn_balance']);
        }
        $model = $model->field('FROM_UNIXTIME(add_time,"%Y-%m") as time,group_concat(id SEPARATOR ",") ids')
            ->group('time');
        $model = $model->page((int)$where['page'], (int)$where['limit']);
        $list = ($list = $model->select()) ? $list->toArray() : [];
        $data = [];
        foreach ($list as $item) {
            $value['time'] = $item['time'];
            $value['list'] = self::where('id', 'in', $item['ids'])->field('FROM_UNIXTIME(add_time,"%Y-%m-%d %H:%i") as add_time,mer_id,title,pm,number,mark')->order('add_time DESC')->select();
            array_push($data, $value);
        }
        $page = $where['page'] + 1;
        return compact('data', 'page');
    }

    /**获取讲师金币收益
     * @param $mer_id
     */
    public static function userMerGoldPrice($mer_id)
    {
        return self::where(['mer_id' => $mer_id, 'category' => 'gold_num'])->where('type', 'in', ['extract'])->value('SUM(number)') ?: 0;
    }
}
