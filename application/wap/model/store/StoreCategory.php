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


namespace app\wap\model\store;


use basic\ModelBasic;

/**商品分类
 * Class StoreCategory
 * @package app\wap\model\store
 */
class StoreCategory extends ModelBasic
{
    /**获取分类
     * @param $pid
     * @param string $field
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function pidByCategory($pid, $field = '*', $limit = 0)
    {
        $model = self::where('pid', $pid)->where('is_show', 1)->field($field);
        if ($limit) $model->limit($limit);
        return $model->order('sort DESC')->select();
    }

    /**获取分类的上级id
     * @param $cateId
     * @return mixed
     */
    public static function cateIdByPid($cateId)
    {
        return self::where('id', $cateId)->value('pid');
    }

}
