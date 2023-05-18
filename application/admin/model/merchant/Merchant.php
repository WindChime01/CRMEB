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
use service\HookService;
use service\SystemConfigService;

/**
 * Class Merchant
 * @package app\merchant\model\merchant
 */
class Merchant extends ModelBasic
{
    use ModelTrait;

    //设置where条件
    public static function setWhere($where, $alirs = '', $model = null)
    {
        $model = $model === null ? new self() : $model;
        $model = $alirs !== '' ? $model->alias($alirs) : $model;
        $alirs = $alirs === '' ? $alirs : $alirs . '.';
        $model = $model->where("{$alirs}is_del", 0);
        if ($where['title'] && $where['title']) $model = $model->where("{$alirs}mer_name", 'LIKE', "%$where[title]%");
        return $model;
    }

    /**讲师列表
     * @param $where
     * @return array
     * @throws \think\Exception
     */
    public static function getLecturerMerchantList($where)
    {
        $data = self::setWhere($where)->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**
     * 删除讲师后台
     * @param $id
     * @return bool|int
     * @throws \think\exception\DbException
     */
    public static function delMerchant($id)
    {
        return self::where('id', $id)->update(['is_del' => 1]);
    }

    public static function getMerWhere()
    {
        return self::where(['is_del' => 0, 'estate' => 1, 'status' => 1]);
    }

    /**减讲师余额
     * @param $mer_id
     * @param $price
     * @return int|true
     * @throws \think\Exception
     */
    public static function decMerchantNowMoney($mer_id, $price)
    {
        return self::where('id', $mer_id)->setDec('now_money', $price);
    }

    /**讲师列表
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getMerchantList()
    {
        return self::getMerWhere()->field('id,mer_name')->select();
    }
}
