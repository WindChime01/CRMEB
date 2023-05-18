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

namespace app\wap\model\special;

use basic\ModelBasic;
use traits\ModelTrait;

/**专题获得
 * Class SpecialBuy
 * @package app\wap\model\special
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
        $special = Special::PreWhere()->where(['id' => $special_id])->find();
        if ($special['type'] == SPECIAL_COLUMN) {
            $special_source = SpecialSource::getSpecialSource($special['id']);
            if (!$special_source) return false;
            foreach ($special_source as $k => $v) {
                $task_special = Special::PreWhere()->where(['id' => $v['source_id']])->find();
                if (!$task_special) continue;
                self::setBuySpecial($order_id, $uid, $v['source_id'], $type, $task_special['validity'], $special_id);
            }
        }
        self::setBuySpecial($order_id, $uid, $special_id, $type, $special['validity']);
    }

    /**记录
     * @param $order_id
     * @param $uid
     * @param $special_id
     * @param int $type
     * @param int $column_id
     * @return bool|object
     */
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

    /**检查专题是否获得
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

    /**获取购买专题的有效时间
     * @param $special_id
     * @param $uid
     */
    public static function getSpecialEndTime($special_id, $uid)
    {
        $res = self::where(['uid' => $uid, 'special_id' => $special_id, 'is_del' => 0])->where('validity_time', 0)->find();
        if ($res) return 0;
        $buy = self::where(['uid' => $uid, 'special_id' => $special_id, 'is_del' => 0])->order('validity_time desc')->find();
        if ($buy) {
            return bcsub($buy['validity_time'], time(), 0);
        } else {
            return -1;
        }
    }

    /**用户名下专栏更新
     * @param $id
     * @return bool|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function update_column($id, $uid)
    {
        if (!$id || !$uid) return true;
        $column = self::where(['is_del' => 0, 'uid' => $uid, 'special_id' => $id, 'is_update' => 1])->field('id,order_id,column_id,uid,special_id,is_del,type as types')->select();
        $column = count($column) > 0 ? $column->toArray() : [];
        if (!$column) return true;
        foreach ($column as $key => $value) {
            $sourceList = self::where(['order_id' => $value['order_id'], 'is_del' => 0, 'uid' => $value['uid'], 'column_id' => $id, 'type' => $value['types']])->select();
            $sourceList = count($sourceList) > 0 ? $sourceList->toArray() : [];
            if (count($sourceList) > 0) {
                $res = self::where(['order_id' => $value['order_id'], 'is_del' => 0, 'uid' => $value['uid'], 'column_id' => $id, 'type' => $value['types']])->delete();
                if (!$res) continue;
            }
            $special_source = SpecialSource::getSpecialSource($id);
            if (!$special_source) continue;
            foreach ($special_source as $k => $v) {
                $task_special = Special::PreWhere()->where(['id' => $v['source_id']])->find();
                if (!$task_special) continue;
                self::setBuySpecial($value['order_id'], $value['uid'], $v['source_id'], $value['types'], $task_special['validity'], $id);
            }
        }
        self::where(['is_del' => 0, 'uid' => $uid, 'special_id' => $id, 'is_update' => 1])->update(['is_update' => 0]);
    }
}
