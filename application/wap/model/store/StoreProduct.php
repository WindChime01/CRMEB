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


use app\admin\model\store\StoreProductAttrValue;
use app\wap\model\store\StoreCart;
use basic\ModelBasic;
use traits\ModelTrait;
use think\Db;

/**商品表
 * Class StoreProduct
 * @package app\wap\model\store
 */
class StoreProduct extends ModelBasic
{
    use  ModelTrait;

    protected function getSliderImageAttr($value)
    {
        return json_decode($value, true) ?: [];
    }

    public static function getValidProduct($productId, $field = 'id,mer_id,store_name,image,slider_image,store_info,keyword,ot_price,description,is_postage,give_gold_num,free_shipping,postage,price,vip_price,stock,IFNULL(sales,0) + IFNULL(ficti,0) as sales')
    {
        return self::validWhere()->where('id', $productId)->field($field)->find();
    }

    public static function validWhere()
    {
        return self::where(['is_del' => 0, 'is_show' => 1, 'status' => 1]);
    }

    /**
     * 新品产品
     * @param string $field
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getNewProduct($field = '*', $limit = 0)
    {
        $model = self::validWhere()->where('is_new', 1)->where('stock', '>', 0)->field($field)
            ->order('sort DESC, id DESC');
        if ($limit) $model->limit($limit);
        return $model->select();
    }

    /**
     * 热卖产品
     * @param string $field
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getHotProduct($field = '*', $limit = 0)
    {
        $model = self::validWhere()->where('is_hot', 1)
            ->where('stock', '>', 0)->field($field)
            ->order('sort DESC, id DESC');
        if ($limit) $model->limit($limit);
        return $model->select();
    }

    /**
     * 精品产品
     * @param string $field
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getBestProduct($field = '*', $limit = 0)
    {
        $model = self::validWhere()->where('is_best', 1)
            ->where('stock', '>', 0)->field($field)
            ->order('sort DESC, id DESC');
        if ($limit) $model->limit($limit);
        return $model->select();
    }


    /**
     * 优惠产品
     * @param string $field
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getBenefitProduct($field = '*', $limit = 0)
    {
        $model = self::validWhere()->where('is_benefit', 1)->where('stock', '>', 0)
            ->field($field)->order('sort DESC, id DESC');
        if ($limit) $model->limit($limit);
        return $model->select();
    }

    public static function cateIdBySimilarityProduct($cateId, $field = '*', $limit = 0)
    {
        $pid = StoreCategory::cateIdByPid($cateId) ?: $cateId;
        $cateList = StoreCategory::pidByCategory($pid, 'id') ?: [];
        $cid = [$pid];
        foreach ($cateList as $cate) {
            $cid[] = $cate['id'];
        }
        $model = self::where('cate_id', 'IN', $cid)->where('is_show', 1)->where('is_del', 0)
            ->field($field)->order('sort DESC,id DESC');
        if ($limit) $model->limit($limit);
        return $model->select();
    }

    public static function isValidProduct($productId)
    {
        return self::be(['id' => $productId, 'is_del' => 0, 'is_show' => 1, 'status' => 1]) > 0;
    }

    /**获取商品库存
     * @param $productId
     * @param string $uniqueId
     * @return int
     */
    public static function getProductStock($productId, $uniqueId = '')
    {
        return self::where('id', $productId)->value('stock');
    }

    public static function decProductStock($num, $productId, $unique = '')
    {
        $res = false !== self::where('id', $productId)->dec('stock', $num)->inc('sales', $num)->update();
        return $res;
    }

    /**
     * 获取单独分销设置
     */
    public static function getIndividualDistributionSettings($oid)
    {
        $product_id = Db::name('store_order_cart_info')->where('oid', $oid)->value('product_id');
        if ($product_id) {
            $data = self::validWhere()->where('id', $product_id)->field('is_alone,brokerage_ratio,brokerage_two')->find();
            if ($data) return $data;
            else return [];
        } else {
            return [];
        }
    }

    /**讲师名下商品
     * @param $mer_id
     * @param $page
     * @param $limit
     */
    public static function getLecturerStoreList($mer_id, $page, $limit)
    {
        if ($mer_id) {
            $model = self::validWhere();
            $model = $model->where(['mer_id' => $mer_id])->order('sort desc,id desc');
            $list = $model->page($page, $limit)->select();
            $list = count($list) ? $list->toArray() : [];
        } else {
            $list = [];
        }
        return $list;
    }
}
