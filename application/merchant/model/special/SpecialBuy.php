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

namespace app\merchant\model\special;

use basic\ModelBasic;
use traits\ModelTrait;

/**专题获得
 * Class SpecialBuy
 * @package app\merchant\model\special
 */
class SpecialBuy extends ModelBasic
{
    use ModelTrait;

    protected function setAddTimeAttr()
    {
        return time();
    }

    protected function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    protected function getTypeAttr($value)
    {
        $name = '';
        switch ($value) {
            case 0:
                $name = '支付获得';
                break;
            case 1:
                $name = '拼团获得';
                break;
            case 2:
                $name = '领取礼物获得';
                break;
            case 3:
                $name = '赠送获得';
                break;
            case 4:
                $name = '兑换获得';
                break;
            case 5:
                $name = '买商品赠送';
                break;
        }
        return $name;
    }

    /**获得专题
     * @param $order_id
     * @param $uid
     * @param $special_id
     * @param int $type
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function setAllBuySpecial($order_id, $uid, $special_id, $type = 0)
    {
        if (!$order_id || !$uid || !$special_id) return false;
        //如果是专栏，记录专栏下所有专题购买。
        $special = Special::PreWhere()->where(['id'=>$special_id])->find();
        if ($special['type'] == SPECIAL_COLUMN) {
            $special_source = SpecialSource::getSpecialSource($special['id']);
            if (!$special_source) return false;
            foreach ($special_source as $k => $v) {
                $task_special = Special::PreWhere()->where(['id'=>$v['source_id']])->find();
                if (!$task_special) continue;
                if ($task_special['is_show'] != 1) continue;
                self::setBuySpecial($order_id, $uid, $v['source_id'], $type, $task_special['validity'], $special_id);
            }
        }
        self::setBuySpecial($order_id, $uid, $special_id, $type, $special['validity']);
    }

    public static function setBuySpecial($order_id, $uid, $special_id, $type = 0, $validity = 0, $column_id = 0)
    {
        $add_time = time();
        if (self::be(['uid' => $uid, 'special_id' => $special_id, 'column_id' => $column_id, 'type' => $type, 'is_del' => 0])) return false;
        $validity_time = 0;
        if ($validity > 0) {
            $validity_time = (int)bcadd(time(), bcmul($validity, 86400, 0), 0);
        }
        return self::set(compact('order_id', 'column_id', 'uid', 'special_id', 'type', 'validity_time', 'add_time'));
    }

    /**专栏更新数据
     * @param $special_id
     */
    public static function columnUpdate($special_id)
    {
        if (!self::be(['special_id' => $special_id, 'is_del' => 0])) return true;
        self::where(['special_id' => $special_id, 'is_del' => 0])->update(['is_update'=>1]);
    }

    /**专题获得情况
     * @param $special_id
     * @param $uid
     * @return bool
     * @throws \think\Exception
     */
    public static function PaySpecial($special_id, $uid)
    {
        self::where(['uid' => $uid, 'special_id' => $special_id, 'is_del' => 0])->where('validity_time', ['>', 0], ['<', time()], 'and')->update(['is_del' => 1]);
        return self::where(['uid' => $uid, 'special_id' => $special_id, 'is_del' => 0])->count() ? true : false;
    }

    /**购买列表
     * @param $where
     * @return mixed
     */
    public static function getPayList($where)
    {
        $list = self::where(['a.uid' => $where['uid'], 'a.is_del' => 0])->alias('a')->group('a.special_id')->join('__SPECIAL__ s', 's.id=a.special_id')
            ->field('a.*,s.title')->order('a.add_time desc')->page((int)$where['page'], (int)$where['limit'])->select();
        foreach ($list as &$item) {
            $pay_price = self::getDb('store_order')->where('order_id', $item['order_id'])->value('pay_price');
            $item['pay_price'] = $pay_price > 0 ? $pay_price : 0;
        }
        return $list;
    }
}
