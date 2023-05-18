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
use traits\ModelTrait;

/**商品购物车
 * Class StoreCart
 * @package app\wap\model\store
 */
class StoreCart extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    /**加入购物车
     * @param $uid
     * @param $product_id
     * @param int $cart_num
     * @param string $product_attr_unique
     * @param string $type
     * @param int $is_new
     * @param int $combination_id
     * @param int $seckill_id
     * @param int $integral_id
     * @return array|bool|false|object|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function setCart($uid, $product_id, $cart_num = 1, $product_attr_unique = '', $type = 'product', $is_new = 0, $combination_id = 0, $seckill_id = 0, $integral_id = 0)
    {
        if ($cart_num < 1) $cart_num = 1;
        if (!StoreProduct::isValidProduct($product_id))
            return self::setErrorInfo('该产品已下架或删除');
        if (StoreProduct::getProductStock($product_id, $product_attr_unique) < $cart_num)
            return self::setErrorInfo('该产品库存不足');
        $where = [
            'type' => $type,
            'uid' => $uid,
            'product_id' => $product_id,
            'product_attr_unique' => $product_attr_unique,
            'is_new' => $is_new,
            'is_pay' => 0,
            'is_del' => 0,
            'combination_id' => $combination_id,
            'seckill_id' => $seckill_id,
            'integral_id' => $integral_id
        ];
        if ($cart = self::where($where)->find()) {
            $cart->cart_num = $cart_num;
            $cart->add_time = time();
            $cart->save();
            return $cart;
        } else {
            return self::set(compact('uid', 'product_id', 'cart_num', 'product_attr_unique', 'is_new', 'type', 'combination_id', 'seckill_id', 'integral_id'));
        }

    }

    /**删除购物车
     * @param $uid
     * @param $ids
     * @return StoreCart
     */
    public static function removeUserCart($uid, $ids)
    {
        return self::where('uid', $uid)->where('id', 'IN', $ids)->update(['is_del' => 1]);
    }

    /**获取购物车数量
     * @param $uid
     * @param $type
     * @return int|string
     * @throws \think\Exception
     */
    public static function getUserCartNum($uid, $type)
    {
        return self::where('uid', $uid)->where('type', $type)->where('is_pay', 0)->where('is_del', 0)->where('is_new', 0)->count();
    }

    /**修改购物车商品数量
     * @param $cartId
     * @param $cartNum
     * @param $uid
     * @return StoreCart|bool
     */
    public static function changeUserCartNum($cartId, $cartNum, $uid)
    {
        if (!self::be(['uid' => $uid, 'id' => $cartId, 'cart_num' => $cartNum])) {
            return self::where('uid', $uid)->where('id', $cartId)->update(['cart_num' => $cartNum]);
        } else {
            return true;
        }
    }

    /**获取购物车数据
     * @param $uid
     * @param string $cartIds
     * @param int $status
     * @param $is_vip
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUserProductCartList($uid, $cartIds = '', $status = 0, $is_vip)
    {
        $productInfoField = 'id,image,slider_image,price,cost,ot_price,vip_price,postage,mer_id,give_gold_num,free_shipping,cate_id,sales,stock,store_name,unit_name,is_show,is_del,is_postage';
        $model = new self();
        $valid = $invalid = [];
        if ($cartIds)
            $model = $model->where('uid', $uid)->where('type', 'product')->where('is_pay', 0)
                ->where('is_del', 0);
        else
            $model = $model->where('uid', $uid)->where('type', 'product')->where('is_pay', 0)->where('is_new', 0)
                ->where('is_del', 0);
        if ($cartIds) $model->where('id', 'IN', $cartIds);
        $list = $model->select()->toArray();
        if (!count($list)) return compact('valid', 'invalid');
        foreach ($list as $k => $cart) {
            $product = StoreProduct::field($productInfoField)
                ->find($cart['product_id'])->toArray();
            $cart['productInfo'] = $product;
            //商品不存在
            if (!$product) {
                $model->where('id', $cart['id'])->update(['is_del' => 1]);
                //商品删除或无库存
            } else if (!$product['is_show'] || $product['is_del'] || !$product['stock']) {
                $invalid[] = $cart;
            } else {
                $cart['truePrice'] = $is_vip ? (isset($cart['productInfo']['vip_price']) ? (float)$cart['productInfo']['vip_price'] : (float)$cart['productInfo']['price']) : (float)$cart['productInfo']['price'];
                $cart['costPrice'] = (float)$cart['productInfo']['cost'];
                $cart['trueStock'] = $cart['productInfo']['stock'];
                $valid[] = $cart;
            }
        }
        foreach ($valid as $k => $cart) {
            if ($cart['trueStock'] < $cart['cart_num']) {
                $cart['cart_num'] = $cart['trueStock'];
                $model->where('id', $cart['id'])->update(['cart_num' => $cart['cart_num']]);
                $valid[$k] = $cart;
            }
        }
        return compact('valid', 'invalid');
    }

}
