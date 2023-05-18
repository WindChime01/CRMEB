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

use app\wap\model\special\Special;
use app\wap\model\special\SpecialBuy;
use app\wap\model\user\User;
use app\wap\model\user\UserAddress;
use app\wap\model\user\UserBill;
use app\wap\model\user\WechatUser;
use basic\ModelBasic;
use behavior\wap\StoreProductBehavior;
use behavior\wechat\PaymentBehavior;
use service\AlipayTradeWapService;
use service\HookService;
use service\SystemConfigService;
use service\WechatService;
use service\WechatTemplateService;
use think\Cache;
use think\Url;
use traits\ModelTrait;
use app\wap\model\user\MemberShip;
use service\GroupDataService;
use app\wap\model\routine\RoutineTemplate;
use app\wap\model\wap\SmsTemplate;
use app\wap\model\topic\TestPaperObtain;
use app\wap\model\material\DataDownloadBuy;
use app\wap\model\merchant\MerchantFlowingWater;


/**订单表
 * Class StoreOrder
 * @package app\wap\model\store
 */
class StoreOrder extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected static $payType = ['weixin' => '微信支付', 'yue' => '余额支付', 'offline' => '线下支付', 'zhifubao' => '支付宝'];

    protected static $deliveryType = ['send' => '商家配送', 'express' => '快递配送'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    protected function setCartIdAttr($value)
    {
        return is_array($value) ? json_encode($value) : $value;
    }

    protected function getCartIdAttr($value)
    {
        return json_decode($value, true);
    }

    /**购物车价格计算
     * @param $cartInfo
     * @return array
     */
    public static function getOrderPriceGroup($cartInfo)
    {
        $storePostage = 0;
        $totalPrice = self::getOrderTotalPrice($cartInfo);
        $costPrice = self::getOrderCostPrice($cartInfo);
        foreach ($cartInfo as $cart) {
            if (!$cart['productInfo']['is_postage']) {
                $storePostage = bcadd($storePostage, $cart['productInfo']['postage'], 2);
                if (bcsub($cart['productInfo']['free_shipping'], $cart['cart_num'], 0) <= 0 && $cart['productInfo']['free_shipping'] > 0) {
                    $storePostage = 0;
                }
            }
        }

        return compact('storePostage', 'totalPrice', 'costPrice');
    }

    /**计算总价
     * @param $cartInfo
     * @return int|string
     */
    public static function getOrderTotalPrice($cartInfo)
    {
        $totalPrice = 0;
        foreach ($cartInfo as $cart) {
            $totalPrice = bcadd($totalPrice, bcmul($cart['cart_num'], $cart['truePrice'], 2), 2);
        }
        return $totalPrice;
    }

    /**计算成本价
     * @param $cartInfo
     * @return int|string
     */
    public static function getOrderCostPrice($cartInfo)
    {
        $costPrice = 0;
        foreach ($cartInfo as $cart) {
            $costPrice = bcadd($costPrice, bcmul($cart['cart_num'], $cart['costPrice'], 2), 2);
        }
        return $costPrice;
    }

    public static function getPinkOrderId($id)
    {
        return self::where('id', $id)->value('order_id');
    }


    public static function cacheOrderInfo($uid, $cartInfo, $priceGroup, $cacheTime = 600)
    {
        $subjectUrl = getUrlToDomain();
        $key = md5(time());
        Cache::store("redis")->set($subjectUrl . 'user_order_' . $uid . $key, compact('cartInfo', 'priceGroup'), $cacheTime);
        return $key;
    }

    public static function getCacheOrderInfo($uid, $key)
    {
        $subjectUrl = getUrlToDomain();
        $cacheName = $subjectUrl . 'user_order_' . $uid . $key;
        if (!Cache::store("redis")->has($cacheName)) return null;
        return Cache::store("redis")->get($cacheName);
    }

    public static function clearCacheOrderInfo($uid, $key)
    {
        $subjectUrl = getUrlToDomain();
        Cache::store("redis")->clear($subjectUrl . 'user_order_' . $uid . $key);
    }

    public static function getSpecialIds($uid)
    {
        return self::where(['is_del' => 0, 'paid' => 1, 'uid' => $uid, 'is_gift' => 0])->column('cart_id');
    }

    /**
     * 获取专题订单列表
     * @param $type
     * @param $page
     * @param $limit
     * @param $uid
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getSpecialOrderList($type, $page, $limit, $uid)
    {
        $model = self::where(['a.is_del' => 0, 's.is_del' => 0, 'a.uid' => $uid, 'a.paid' => 1])->order('a.add_time desc')->alias('a')->join('__SPECIAL__ s', 'a.cart_id=s.id');
        switch ($type) {
            case 1:
                $model = $model->where(['a.is_gift' => 1, 'a.combination_id' => 0, 'a.pink_id' => 0, 'a.type' => 0]);
                break;
            case 2:
                $model = $model->where(['a.is_gift' => 0, 'a.combination_id' => 0, 'a.pink_id' => 0, 'a.type' => 0]);
                break;
            case 3:
                $model = $model->where('a.is_gift', 0)->where('a.type', 0)->where('a.combination_id', 'NEQ', 0)->where('a.pink_id', 'NEQ', 0);
                break;
        }
        $list = $model->field(['a.*', 's.title', 's.image', 's.money', 's.pink_number', 's.is_light'])->page($page, $limit)->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as &$item) {
            $item['image'] = get_oss_process($item['image'], 4);
            $item['pink'] = [];
            $item['stop_time'] = 0;
            $item['is_draw'] = false;
            if ($type === 3 && $item['pink_id'] != 0) {
                if ($pink = StorePink::where('order_id', $item['order_id'])->find()) {
                    $pink = $pink->toArray();
                    $item['pink_status'] = $pink['is_refund'] ? 4 : $pink['status'];
                    $item['stop_time'] = date('Y-m-d H:i:s', $pink['stop_time']);
                    $item['pink_id'] = $pink['id'];
                    StorePink::setPinkIng($pink, $item['uid']);
                } else {
                    $item['pink_status'] = 5;
                }
            } else if ($type === 1) {
                if ($uid = self::where(['gift_order_id' => $item['order_id']])->value('uid')) {
                    $item['is_draw'] = true;
                    $userAvatar = User::where('uid', $uid)->field(['nickname', 'avatar'])->find();
                    if ($userAvatar) {
                        $item['gift_user'] = $userAvatar->toArray();
                    } else {
                        $item['gift_user'] = ['nickname' => '', 'avatar' => ''];
                    }

                }
            }
        }
        $page++;
        return compact('list', 'page');
    }

    /**
     * 获取礼物领取记录
     * @param $order_id 订单号
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getOrderIdGiftReceive($order_id)
    {
        $is_gift = false;
        $user = [];
        $order = self::where(['is_del' => 0, 'order_id' => $order_id])->find();
        if (!$order) return self::setErrorInfo('订单不存在');
        if (!$order->cart_id) return self::setErrorInfo('订单专题不存在!');
        $add_time = date('m-d H:i', $order->add_time);
        $data = Special::PreWhere()->where(['id' => $order->cart_id])->field('image,title')->find();
        $image = $data['image'];
        $title = $data['title'];
        $uid = self::where(['is_del' => 0, 'gift_order_id' => $order->order_id])->value('uid');
        if ($uid) {
            $is_gift = true;
            $user = User::where('uid', $uid)->field(['avatar', 'nickname'])->find();
        }
        return compact('user', 'is_gift', 'image', 'add_time', 'title');
    }

    /**
     * 获取订单的专题详情信息
     * @param $order_id 订单号
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getOrderIdToSpecial($order_id, $uid)
    {
        $order = self::where(['is_del' => 0, 'order_id' => $order_id, 'uid' => $uid])->find();
        if (!$order) return self::setErrorInfo('订单不存在或给订单不是您的!');
        if (!$order->cart_id) return self::setErrorInfo('订单专题不存在!');
        $special = Special::PreWhere()->where(['id' => $order->cart_id])->find();
        if (!$special) return self::setErrorInfo('赠送的专题已下架,或已被删除!');
        $special->abstract = self::HtmlToMbStr($special->abstract);
        return $special->toArray();
    }

    /**
     * 创建领取礼物订单
     * @param $orderId 订单号
     * @param $uid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function createReceiveGift($orderId, $uid)
    {
        $order = self::where(['is_del' => 0, 'order_id' => $orderId, 'paid' => 1])->find();
        if (!$order) return self::setErrorInfo('赠送的礼物订单不存在');
        if ($order->total_num == $order->gift_count) return self::setErrorInfo('礼物已被领取完');
        if (SpecialBuy::be(['special_id' => $order['cart_id'], 'uid' => $uid])) return self::setErrorInfo('您已拥有此专题无法,进行领取');
        $data = [
            'uid' => $uid,
            'order_id' => self::getNewOrderId(),
            'cart_id' => $order->cart_id,
            'total_num' => 1,
            'total_price' => $order->total_price,
            'gift_count' => 1,
            'pay_price' => 0,
            'paid' => 1,
            'pay_time' => time(),
            'is_receive_gift' => 1,
            'mark' => '礼物领取订单',
            'unique' => md5(time() . '' . $uid . $order->cart_id),
            'cost' => $order->total_price,
            'pay_type' => $order->pay_type,
            'gift_order_id' => $orderId
        ];
        $order->gift_count += 1;
        if ($order->save() && ($order = self::set($data))) {
            SpecialBuy::setAllBuySpecial($order['order_id'], $order['uid'], $order['cart_id'], 2);
            StoreOrderStatus::status($order['id'], 'cache_key_create_order', '订单生成');
            TestPaperObtain::setTestPaper($order['order_id'], $order['uid'], $order['cart_id'], 2);
            DataDownloadBuy::setDataDownload($order['order_id'], $order['uid'], $order['cart_id'], 0);
            return true;
        } else
            return self::setErrorInfo('领取礼物订单生成失败');
    }


    /**
     * 创建订单专题订单
     * @param $special
     * @param $pinkId
     * @param $pay_type
     * @param $uid
     * @param $payType
     * @param int $link_pay_uid
     * @param int $total_num
     * @return bool|object
     */
    public static function createSpecialOrder($special, $pinkId, $pay_type, $uid, $payType, $link_pay_uid = 0, $total_num = 1)
    {
        if (!array_key_exists($payType, self::$payType)) return self::setErrorInfo('选择支付方式有误!');
        $userInfo = User::getUserData($uid);
        if (!$userInfo) return self::setErrorInfo('用户不存在!');
        $total_price = 0;
        $combination_id = 0;
        switch ((int)$pay_type) {
            case 1:
                //送朋友
                $total_price = $special->money;
                if (isset($userInfo['level']) && $userInfo['level'] > 0 && $special->member_pay_type == 1 && $special->member_money > 0) {
                    $total_price = $special->member_money;
                } else if (isset($userInfo['level']) && $userInfo['level'] > 0 && $special->member_pay_type == 0) {
                    return self::setErrorInfo('会员免费，该专题您不需要支付!');
                }
                break;
            case 2:
                //自己买
                $total_price = $special->money;
                if (isset($userInfo['level']) && $userInfo['level'] > 0 && $special->member_pay_type == 1 && $special->member_money > 0) {
                    $total_price = $special->member_money;
                } else if (isset($userInfo['level']) && $userInfo['level'] > 0 && $special->member_pay_type == 0) {
                    return self::setErrorInfo('会员免费，该专题您不需要支付!');
                }
                $res = SpecialBuy::PaySpecial($special->id, $uid);
                if ($res) return self::setErrorInfo('您已获得专题，无需再次购买!');
                break;
            case 3:
                //参与拼团
                if (!$special->is_pink && $special->pink_end_time < date('Y-m-d H:i:s', time())) return self::setErrorInfo('拼团已结束或暂没有开团');
                $total_price = $special->pink_money;
                $combination_id = $special->id;
                $res = SpecialBuy::PaySpecial($special->id, $uid);
                if ($res) return self::setErrorInfo('您已获得专题，不能参与拼团!');
                $order = self::where(['uid' => $uid, 'cart_id' => $special->id, 'paid' => 1, 'combination_id' => $combination_id, 'pink_id' => $pinkId, 'is_gift' => 0, 'type' => 0, 'status' => 0, 'refund_status' => 0, 'is_del' => 0])->find();
                if ($order) return self::setErrorInfo('您已参与拼团，不能参与拼团!');
                break;
        }
        $orderInfo = [
            'uid' => $uid,
            'mer_id' => $special->mer_id,
            'order_id' => self::getNewOrderId(),
            'cart_id' => $special->id,
            'total_num' => $total_num,
            'total_price' => $total_price,
            'pay_price' => $total_price,
            'pay_type' => $payType,
            'combination_id' => $combination_id,
            'is_gift' => $pay_type == 1 ? 1 : 0,
            'pink_time' => $pay_type == 3 ? $special->pink_time : 0,
            'paid' => 0,
            'pink_id' => $pinkId,
            'unique' => md5(time() . '' . $uid . $special->id),
            'cost' => $total_price,
            'link_pay_uid' => $userInfo['spread_uid'] ? 0 : $link_pay_uid,
            'spread_uid' => $userInfo['spread_uid'] ? $userInfo['spread_uid'] : 0,
            'is_del' => 0,
        ];
        $order = self::set($orderInfo);
        if (!$order) return self::setErrorInfo('订单生成失败!');
        StoreOrderStatus::status($order['id'], 'cache_key_create_order', '订单生成');
        return $order;
    }

    /**
     * 创建商品订单
     * @param $uid
     * @param $key
     * @param $addressId
     * @param $payType
     * @param string $mark
     * @return bool|object
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function cacheKeyCreateOrder($uid, $key, $addressId, $payType, $useGold, $mark = '')
    {
        if (!array_key_exists($payType, self::$payType)) return self::setErrorInfo('选择支付方式有误!');
        if (self::be(['unique' => $key, 'uid' => $uid])) return self::setErrorInfo('请勿重复提交订单');
        $userInfo = User::getUserData($uid);
        if (!$userInfo) return self::setErrorInfo('用户不存在!');
        $cartGroup = self::getCacheOrderInfo($uid, $key);
        if (!$cartGroup) return self::setErrorInfo('订单已过期,请刷新当前页面!');
        $cartInfo = $cartGroup['cartInfo'];
        $priceGroup = $cartGroup['priceGroup'];
        $payPrice = $priceGroup['totalPrice'];
        $payPostage = $priceGroup['storePostage'];
        if (!$addressId) return self::setErrorInfo('请选择收货地址!');
        if (!UserAddress::be(['uid' => $uid, 'id' => $addressId, 'is_del' => 0]) || !($addressInfo = UserAddress::find($addressId)))
            return self::setErrorInfo('地址选择有误!');
        $payPrice = bcadd($payPrice, $payPostage, 2);
        $cartIds = [];
        $totalNum = 0;
        $giveGoldNum = 0;
        $mer_id = 0;
        foreach ($cartInfo as $cart) {
            $cartIds[] = $cart['id'];
            $totalNum += $cart['cart_num'];
            $giveGoldNum = bcadd($giveGoldNum, $cart['productInfo']['give_gold_num'], 2);
            $mer_id = $cart['productInfo']['mer_id'];
        }
        //虚拟币抵扣
        $res2 = true;
        $gold_name = SystemConfigService::get('gold_name');
        if ($useGold && $userInfo['gold_num'] > 0 && $payPrice > 0) {
            $ratio = SystemConfigService::get('deduction_proportion_ratio');
            $ratio = bcdiv($ratio, 100, 2);
            $deductionPrice = bcmul($userInfo['gold_num'], $ratio, 2);
            if ($deductionPrice < $payPrice) {
                $payPrice = bcsub($payPrice, $deductionPrice, 2);
                $usedGold = $userInfo['gold_num'];
                $res2 = false !== User::edit(['gold_num' => 0], $userInfo['uid'], 'uid');
            } else {
                $deductionPrice = $payPrice;
                $usedGold = bcdiv($payPrice, $ratio, 2);
                $res2 = false !== User::bcDec($userInfo['uid'], 'gold_num', $usedGold, 'uid');
                $payPrice = 0;
            }
            $res2 = $res2 && false != UserBill::expend($gold_name . '抵扣', $uid, 'gold_num', 'deduction', $usedGold, $key, $userInfo['gold_num'], '购买商品使用' . floatval($usedGold) . $gold_name . '抵扣' . floatval($deductionPrice) . '元');
        } else {
            $deductionPrice = 0;
            $usedGold = 0;
        }
        if (!$res2) return self::setErrorInfo($gold_name . '抵扣失败!');
        $orderInfo = [
            'uid' => $uid,
            'order_id' => self::getNewOrderId(),
            'mer_id' => $mer_id,
            'type' => 2,
            'real_name' => $addressInfo['real_name'],
            'user_phone' => $addressInfo['phone'],
            'user_address' => $addressInfo['province'] . ' ' . $addressInfo['city'] . ' ' . $addressInfo['district'] . ' ' . $addressInfo['detail'],
            'cart_id' => $cartIds,
            'total_num' => $totalNum,
            'total_price' => $priceGroup['totalPrice'],
            'total_postage' => $priceGroup['storePostage'],
            'pay_price' => $payPrice,
            'pay_postage' => $payPostage,
            'paid' => 0,
            'pay_type' => $payType,
            'gain_gold_num' => $giveGoldNum,
            'use_gold' => $usedGold,
            'deduction_price' => $deductionPrice,
            'mark' => htmlspecialchars($mark),
            'cost' => $priceGroup['costPrice'],
            'unique' => $key
        ];
        $order = self::set($orderInfo);
        if (!$order) return self::setErrorInfo('订单生成失败!');
        $res5 = true;
        foreach ($cartInfo as $cart) {
            //减库存加销量
            $res5 = $res5 && StoreProduct::decProductStock($cart['cart_num'], $cart['productInfo']['id'], isset($cart['productInfo']['attrInfo']) ? $cart['productInfo']['attrInfo']['unique'] : '');
        }
        //保存购物车商品信息
        $res4 = false !== StoreOrderCartInfo::setCartInfo($order['id'], $cartInfo);
        //购物车状态修改
        $res6 = false !== StoreCart::where('id', 'IN', $cartIds)->update(['is_pay' => 1]);
        if (!$res4 || !$res5 || !$res6) return self::setErrorInfo('订单生成失败!');
        try {
            HookService::listen('store_product_order_create', $order, compact('cartInfo', 'addressId'), false, StoreProductBehavior::class);
        } catch (\Exception $e) {
            return self::setErrorInfo($e->getMessage());
        }
        self::clearCacheOrderInfo($uid, $key);
        self::commitTrans();
        StoreOrderStatus::status($order['id'], 'cache_key_create_order', '订单生成');
        return $order;
    }

    /**创建会员订单
     * @param $uid
     * @param $kid
     * @return bool|object
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function cacheMemberCreateOrder($uid, $id, $payType)
    {
        if (!array_key_exists($payType, self::$payType)) return self::setErrorInfo('选择支付方式有误!');
        $userInfo = User::getUserData($uid);
        if (!$userInfo) return self::setErrorInfo('用户不存在!');
        if ($userInfo['level'] && $userInfo['is_permanent']) return self::setErrorInfo('您是永久会员，无需续费!');
        $member = MemberShip::where('id', $id)->where('is_publish', 1)->where('is_del', 0)->where('type', 1)->find();
        if ($member['is_free']) {
            if (self::be(['uid' => $uid, 'member_id' => $id, 'is_del' => 0])) return self::setErrorInfo('免费会员不能重复领取!');
        }
        $orderInfo = [
            'uid' => $uid,
            'order_id' => self::getNewOrderId(),
            'type' => 1,
            'member_id' => $id,
            'total_num' => 1,
            'total_price' => $member['original_price'],
            'pay_price' => $member['price'],
            'pay_type' => $payType,
            'combination_id' => 0,
            'is_gift' => 0,
            'pink_time' => 0,
            'paid' => 0,
            'pink_id' => 0,
            'unique' => md5(time() . '' . $uid . $id),
            'cost' => $member['original_price'],
            'link_pay_uid' => 0,
            'spread_uid' => $userInfo['spread_uid'] ? $userInfo['spread_uid'] : 0,
            'is_del' => 0,
        ];
        $order = self::set($orderInfo);
        if (!$order) return self::setErrorInfo('订单生成失败!');
        StoreOrderStatus::status($order['id'], 'cache_key_create_order', '订单生成');
        return $order;
    }

    public static function getNewOrderId()
    {
        $count = (int)self::where('add_time', ['>=', strtotime(date("Y-m-d"))], ['<', strtotime(date("Y-m-d", strtotime('+1 day')))])->count();
        return 'wx' . date('YmdHis', time()) . (10000 + $count + 1);
    }

    public static function changeOrderId($orderId)
    {
        $ymd = substr($orderId, 2, 8);
        $key = substr($orderId, 16);
        return 'wx' . $ymd . date('His') . $key;
    }

    /**
     * 微信支付 为 0元时 商品
     * @param $order_id
     * @param $uid
     * @return bool
     */
    public static function jsPayGoodsPrice($order_id, $uid)
    {
        $orderInfo = self::where('uid', $uid)->where('order_id', $order_id)->where('type', 2)->where('is_del', 0)->find();
        if (!$orderInfo) return self::setErrorInfo('订单不存在!');
        if ($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        $userInfo = User::getUserData($uid);
        self::beginTrans();
        $res1 = UserBill::expend('购买商品', $uid, 'now_money', 'pay_goods', $orderInfo['pay_price'], $orderInfo['id'], $userInfo['now_money'], '支付' . floatval($orderInfo['pay_price']) . '元购买商品');
        $res2 = self::payGoodsSuccess($order_id,'');
        $res = $res1 && $res2;
        self::checkTrans($res);
        return $res;
    }

    /**商品微信
     * @param $orderId
     * @param string $field
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function jsPay($orderId, $field = 'order_id')
    {
        if (is_string($orderId))
            $orderInfo = self::where($field, $orderId)->where('type', 2)->find();
        else
            $orderInfo = $orderId;
        if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
        if ($orderInfo['paid']) exception('支付已支付!');
        if ($orderInfo['pay_price'] <= 0) exception('该支付无需支付!');
        $openid = WechatUser::uidToOpenid($orderInfo['uid']);
        return WechatService::jsPay($openid, $orderInfo['order_id'], $orderInfo['pay_price'], 'goods', SystemConfigService::get('site_name'));
    }

    /**
     * 微信h5支付
     * @param $orderId
     * @param string $field
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function h5Pay($orderId, $field = 'order_id')
    {
        if (is_string($orderId))
            $orderInfo = self::where($field, $orderId)->find();
        else
            $orderInfo = $orderId;
        if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
        if ($orderInfo['paid']) exception('支付已支付!');
        if ($orderInfo['pay_price'] <= 0) exception('该支付无需支付!');
        $site_name = SystemConfigService::get('site_name');
        if (!$site_name) exception('支付参数缺少：请前往后台设置->系统设置-> 填写 网站名称');
        return WechatService::paymentPrepare(null, $orderInfo['order_id'], $orderInfo['pay_price'], 'goods', self::getSubstrUTf8($site_name . '-商品购买', 30), '', 'MWEB');
    }

    /**商品余额支付
     * @param $order_id
     * @param $uid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function yueGoodsPay($order_id, $uid)
    {
        $orderInfo = self::where('uid', $uid)->where('order_id', $order_id)->where('type', 2)->where('is_del', 0)->find();
        if (!$orderInfo) return self::setErrorInfo('订单不存在!');
        if ($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        if ($orderInfo['pay_type'] != 'yue') return self::setErrorInfo('该订单不能使用余额支付!');
        $userInfo = User::getUserData($uid);
        if ($userInfo['now_money'] < $orderInfo['pay_price']) return self::setErrorInfo('余额不足' . floatval($orderInfo['pay_price']));
        self::beginTrans();
        $res1 = false !== User::bcDec($uid, 'now_money', $orderInfo['pay_price'], 'uid');
        $res2 = UserBill::expend('购买商品', $uid, 'now_money', 'pay_goods', $orderInfo['pay_price'], $orderInfo['id'], bcsub($userInfo['now_money'], $orderInfo['pay_price'], 2), '余额支付' . floatval($orderInfo['pay_price']) . '元购买商品');
        $res3 = self::payGoodsSuccess($order_id,'');
        try {
            HookService::listen('yue_pay_product', $userInfo, $orderInfo, false, PaymentBehavior::class);
        } catch (\Exception $e) {
            self::rollbackTrans();
            return self::setErrorInfo($e->getMessage());
        }
        $res = $res1 && $res2 && $res3;
        self::checkTrans($res);
        return $res;
    }

    /**专题微信支付
     * @param $orderId
     * @param string $field
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function jsSpecialPay($orderId, $field = 'order_id')
    {
        if (is_string($orderId))
            $orderInfo = self::where($field, $orderId)->find();
        else
            $orderInfo = $orderId;
        if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
        if ($orderInfo['paid']) exception('支付已支付!');
        if ($orderInfo['pay_price'] <= 0) exception('该支付无需支付!');
        $site_name = SystemConfigService::get('site_name');
        $openid = WechatUser::uidToOpenid($orderInfo['uid']);
        return WechatService::jsPay($openid, $orderInfo['order_id'], $orderInfo['pay_price'], 'special', self::getSubstrUTf8($site_name, 30));
    }

    /**
     * 微信h5支付
     * @param $orderId
     * @param string $field
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function h5SpecialPay($orderId, $field = 'order_id')
    {
        if (is_string($orderId))
            $orderInfo = self::where($field, $orderId)->find();
        else
            $orderInfo = $orderId;
        if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
        if ($orderInfo['paid']) exception('支付已支付!');
        if ($orderInfo['pay_price'] <= 0) exception('该支付无需支付!');
        $site_name = SystemConfigService::get('site_name');
        if (!$site_name) exception('支付参数缺少：请前往后台设置->系统设置-> 填写 网站名称');
        return WechatService::paymentPrepare(null, $orderInfo['order_id'], $orderInfo['pay_price'], 'special', self::getSubstrUTf8($site_name . '-专题购买', 30), '', 'MWEB');
    }

    /**专题余额支付
     * @param $order_id
     * @param $uid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function yuePay($order_id, $uid)
    {
        $orderInfo = self::where('uid', $uid)->where('order_id', $order_id)->where('is_del', 0)->find();
        if (!$orderInfo) return self::setErrorInfo('订单不存在!');
        if ($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        if ($orderInfo['pay_type'] != 'yue') return self::setErrorInfo('该订单不能使用余额支付!');
        $userInfo = User::getUserData($uid);
        if ($userInfo['now_money'] < $orderInfo['pay_price']) return self::setErrorInfo('余额不足' . floatval($orderInfo['pay_price']));
        self::beginTrans();
        $res1 = false !== User::bcDec($uid, 'now_money', $orderInfo['pay_price'], 'uid');
        $res2 = UserBill::expend('购买专题', $uid, 'now_money', 'pay_product', $orderInfo['pay_price'], $orderInfo['id'], bcsub($userInfo['now_money'], $orderInfo['pay_price'], 2), '余额支付' . floatval($orderInfo['pay_price']) . '元购买专题');
        $res3 = self::paySuccess($order_id);
        try {
            HookService::listen('yue_pay_product', $userInfo, $orderInfo, false, PaymentBehavior::class);
        } catch (\Exception $e) {
            self::rollbackTrans();
            return self::setErrorInfo($e->getMessage());
        }
        $res = $res1 && $res2 && $res3;
        self::checkTrans($res);
        return $res;
    }

    /**
     * 微信支付 为 0元时
     * @param $order_id
     * @param $uid
     * @return bool
     */
    public static function jsPayPrice($order_id, $uid)
    {
        $orderInfo = self::where('uid', $uid)->where('order_id', $order_id)->where('is_del', 0)->find();
        if (!$orderInfo) return self::setErrorInfo('订单不存在!');
        if ($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        $userInfo = User::getUserData($uid);
        self::beginTrans();
        $res1 = UserBill::expend('购买专题', $uid, 'now_money', 'pay_product', $orderInfo['pay_price'], $orderInfo['id'], $userInfo['now_money'], '支付' . floatval($orderInfo['pay_price']) . '元购买专题');
        $res2 = self::paySuccess($order_id);
        $res = $res1 && $res2;
        self::checkTrans($res);
        return $res;
    }

    /**
     * 微信支付 为 0元时
     * @param $order_id
     * @param $uid
     * @return bool
     */
    public static function jsPayMePrice($order_id, $uid)
    {
        $orderInfo = self::where('uid', $uid)->where('order_id', $order_id)->where('is_del', 0)->find();
        if (!$orderInfo) return self::setErrorInfo('订单不存在!');
        if ($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        $userInfo = User::getUserData($uid);
        self::beginTrans();
        $res1 = UserBill::expend('购买会员', $uid, 'now_money', 'pay_vip', $orderInfo['pay_price'], $orderInfo['id'], $userInfo['now_money'], '支付' . floatval($orderInfo['pay_price']) . '元购买会员');
        $res2 = self::payMeSuccess($order_id);
        $res = $res1 && $res2;
        self::checkTrans($res);
        return $res;
    }

    /**会员微信支付
     * @param $orderId
     * @param string $field
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function jsPayMember($orderId, $field = 'order_id')
    {
        if (is_string($orderId))
            $orderInfo = self::where($field, $orderId)->find();
        else
            $orderInfo = $orderId;
        if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
        if ($orderInfo['paid']) exception('支付已支付!');
        if ($orderInfo['pay_price'] <= 0) exception('该支付无需支付!');
        $openid = WechatUser::uidToOpenid($orderInfo['uid']);
        return WechatService::jsPay($openid, $orderInfo['order_id'], $orderInfo['pay_price'], 'member', SystemConfigService::get('site_name'));
    }

    /**
     * 微信h5支付
     * @param $orderId
     * @param string $field
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function h5PayMember($orderId, $field = 'order_id')
    {
        if (is_string($orderId))
            $orderInfo = self::where($field, $orderId)->find();
        else
            $orderInfo = $orderId;
        if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
        if ($orderInfo['paid']) exception('支付已支付!');
        if ($orderInfo['pay_price'] <= 0) exception('该支付无需支付!');
        $site_name = SystemConfigService::get('site_name');
        if (!$site_name) exception('支付参数缺少：请前往后台设置->系统设置-> 填写 网站名称');
        return WechatService::paymentPrepare(null, $orderInfo['order_id'], $orderInfo['pay_price'], 'member', self::getSubstrUTf8($site_name . '-会员购买', 30), '', 'MWEB');
    }

    public static function yuePayMember($order_id, $uid)
    {
        $orderInfo = self::where('uid', $uid)->where('order_id', $order_id)->where('is_del', 0)->find();
        if (!$orderInfo) return self::setErrorInfo('订单不存在!');
        if ($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        if ($orderInfo['pay_type'] != 'yue') return self::setErrorInfo('该订单不能使用余额支付!');
        $userInfo = User::getUserData($uid);
        if ($userInfo['now_money'] < $orderInfo['pay_price']) return self::setErrorInfo('余额不足' . floatval($orderInfo['pay_price']));
        self::beginTrans();
        $res1 = false !== User::bcDec($uid, 'now_money', $orderInfo['pay_price'], 'uid');
        $res3 = self::payMeSuccess($order_id);
        $res = $res1 && $res3;
        self::checkTrans($res);
        return $res;
    }

    public static function yueRefundAfter($order)
    {

    }

    /**
     * 用户申请退款
     * @param $uni
     * @param $uid
     * @param string $refundReasonWap
     * @return bool
     */
    public static function orderApplyRefund($uni, $uid, $data)
    {
        $order = self::getUserOrderDetail($uid, $uni);
        if (!$order) return self::setErrorInfo('支付订单不存在!');
        if ($order['refund_status'] == 2) return self::setErrorInfo('订单已退款!');
        if ($order['refund_status'] == 1) return self::setErrorInfo('正在申请退款中!');
        if ($order['status'] == 1) return self::setErrorInfo('订单当前无法退款!');
        self::beginTrans();
        $res1 = false !== StoreOrderStatus::status($order['id'], 'apply_refund', '用户申请退款，原因：' . $data['refund_reason']);
        $res2 = false !== self::edit(['refund_status' => 1, 'refund_application_time' => time(), 'refund_reason_wap' => $data['refund_reason'], 'refund_reason_wap_img' => json_encode($data['pics']), 'mark' => $data['remarks']], $order['id'], 'id');
        $res = $res1 && $res2;
        self::checkTrans($res);
        if (!$res)
            return self::setErrorInfo('申请退款失败!');
        else {
            return $res;
        }
    }

    /**
     * 自动退款
     * @param array $order
     * */
    public static function autoRefundY($order)
    {
        if (!$order['pink_id']) return true;
        $refund_data = [
            'pay_price' => $order['pay_price'],
            'refund_price' => $order['pay_price'],
        ];
        switch ($order['pay_type']) {
            case 'weixin':
                if ($order['is_channel']) {
                    try {
                        HookService::listen('routine_pay_order_refund', $order['order_id'], $refund_data, true, PaymentBehavior::class);
                    } catch (\Exception $e) {
                        return self::setErrorInfo($e->getMessage());
                    }
                } else {
                    try {
                        HookService::listen('wechat_pay_order_refund', $order['order_id'], $refund_data, true, PaymentBehavior::class);
                    } catch (\Exception $e) {
                        return self::setErrorInfo($e->getMessage());
                    }
                }
                break;
            case 'yue':
                ModelBasic::beginTrans();
                $res1 = User::bcInc($order['uid'], 'now_money', $refund_data['pay_price'], 'uid');
                $res2 = $res2 = UserBill::income('商品退款', $order['uid'], 'now_money', 'pay_product_refund', $refund_data['pay_price'], $order['id'], $order['pay_price'], '订单退款到余额' . floatval($refund_data['pay_price']) . '元');
                try {
                    HookService::listen('store_order_yue_refund', $order, $refund_data, false, StoreProductBehavior::class);
                } catch (\Exception $e) {
                    ModelBasic::rollbackTrans();
                    return self::setErrorInfo($e->getMessage());
                }
                $res = $res1 && $res2;
                ModelBasic::checkTrans($res);
                if (!$res) return self::setErrorInfo('余额退款失败!');
                break;
            case 'zhifubao':
                AlipayTradeWapService::init()->AliPayRefund($order['order_id'], $order['trade_no'], $order['pay_price'], '拼团失败退款', 'refund');
                break;
        }
        $data = [
            'refund_status' => 2,
            'refund_reason_time' => time(),
            'refund_price' => $order['pay_price'],
            'status' => -1,
        ];
        self::edit($data, $order['id'], 'id');
        StorePink::setRefundPink($order['pink_id']);
        HookService::afterListen('store_product_order_refund_y', $data, $order['id'], false, StoreProductBehavior::class);
        StoreOrderStatus::status($order['id'], 'refund_price', '自动发起退款,退款给用户' . $order['pay_price'] . '元');
        return true;
    }

    /**
     * //TODO 专题支付成功后
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function paySuccess($orderId)
    {
        $order = self::where('order_id', $orderId)->where('type', 0)->find();
        if (!$order) return false;
        $resPink = true;
        $res2 = true;
        $res3 = true;
        User::bcInc($order['uid'], 'pay_count', 1, 'uid');
        $res1 = self::where('order_id', $orderId)->where('type', 0)->update(['paid' => 1, 'pay_time' => time()]);
        if ($res1 && $order['pay_type'] != 'yue') {
            $res2 = UserBill::expend('购买专题', $order['uid'], $order['pay_type'], 'pay_product', $order['pay_price'], $order['id'], 0, '支付' . floatval($order['pay_price']) . '元购买专题');
        }
        if ($res1) {
            $res3 = MerchantFlowingWater::setMerchantFlowingWater($order, 0);
        }
        if ($order['combination_id'] && $res1 && !$order['refund_status']) {
            $resPink = StorePink::createPink($order);//创建拼团
        } else {
            if (!$order['is_gift']) {
                //如果是专栏，记录专栏下所有专题购买。
                SpecialBuy::setAllBuySpecial($orderId, $order['uid'], $order['cart_id']);
                TestPaperObtain::setTestPaper($orderId, $order['uid'], $order['cart_id'], 2);
                DataDownloadBuy::setDataDownload($orderId, $order['uid'], $order['cart_id'], 0);
                try {
                    //专题返佣
                    User::backOrderBrokerage($order);
                } catch (\Throwable $e) {
                }
            }
        }
        StoreOrderStatus::status($order->id, 'pay_success', '用户付款成功');
        $site_url = SystemConfigService::get('site_url');
        try {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                $title = Special::getName($order['cart_id']);
                WechatTemplateService::sendTemplate(WechatUser::where('uid', $order['uid'])->value('openid'), WechatTemplateService::ORDER_PAY_SUCCESS, [
                    'first' => '亲，您购买的专题已支付成功',
                    'keyword1' => $title,
                    'keyword2' => $orderId,
                    'keyword3' => $order['pay_price'],
                    'keyword4' => date('Y-m-d H:i:s', time()),
                    'remark' => '点击查看订单详情'
                ], $site_url . Url::build('wap/special/grade_special'));
                WechatTemplateService::sendAdminNoticeTemplate($order['mer_id'], [
                    'first' => "亲,您有一个新的课程订单",
                    'keyword1' => $title,
                    'keyword2' => $orderId,
                    'keyword3' => $order['pay_price'],
                    'keyword4' => date('Y-m-d H:i:s', time()),
                    'remark' => '请及时查看'
                ]);
            } else {
                $data['character_string1']['value'] = $orderId;
                $data['amount3']['value'] = $order['pay_price'];
                $data['time2']['value'] = date('Y-m-d H:i:s', time());
                $data['thing6']['value'] = '您购买的专题已支付成功！';
                RoutineTemplate::sendOrderSuccess($data, $order['uid'], $site_url . Url::build('wap/special/grade_special'));
                $dataAdmin['character_string1']['value'] = $orderId;
                $dataAdmin['amount3']['value'] = $order['pay_price'];
                $dataAdmin['time2']['value'] = date('Y-m-d H:i:s', time());
                $dataAdmin['thing6']['value'] = '您有一个新的课程订单！';
                RoutineTemplate::sendAdminNoticeTemplate($dataAdmin);
            }
        } catch (\Throwable $e) {
        }
        $res = $res1 && $resPink && $res2 && $res3;
        return false !== $res;
    }

    /**
     * //TODO 会员支付成功后
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function payMeSuccess($orderId)
    {
        $order = self::where('order_id', $orderId)->where('type', 1)->find();
        if (!$order) return false;
        $resMer = true;
        $res2 = true;
        $res1 = self::where('order_id', $orderId)->where('type', 1)->update(['paid' => 1, 'pay_time' => time()]);
        $userInfo = User::getUserData($order['uid']);
        if ($order['type'] == 1 && $res1 && !$order['refund_status']) {
            if ($order['pay_type'] != 'yue') {
                $res2 = UserBill::expend('购买会员', $order['uid'], $order['pay_type'], 'pay_vip', $order['pay_price'], $order['id'], 0, '支付' . floatval($order['pay_price']) . '元购买会员');
            }
            $resMer = MemberShip::getUserMember($order, $userInfo);
            try {
                //会员返佣
                User::backOrderBrokerageMember($order);
            } catch (\Throwable $e) {
            }
        }
        $site_url = SystemConfigService::get('site_url');
        try {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                $title = MemberShip::getName($order['member_id']);
                WechatTemplateService::sendTemplate(WechatUser::where('uid', $order['uid'])->value('openid'), WechatTemplateService::ORDER_PAY_SUCCESS, [
                    'first' => '亲，您充值会员已支付成功',
                    'keyword1' => $title . '会员',
                    'keyword2' => $orderId,
                    'keyword3' => $order['pay_price'],
                    'keyword4' => date('Y-m-d H:i:s', time()),
                    'remark' => '点击查看会员详情'
                ], $site_url . Url::build('wap/special/member_recharge'));
                WechatTemplateService::sendAdminNoticeTemplate(0, [
                    'first' => "亲,您有一个新的会员购买订单",
                    'keyword1' => $title . '会员',
                    'keyword2' => $orderId,
                    'keyword3' => $order['pay_price'],
                    'keyword4' => date('Y-m-d H:i:s', time()),
                    'remark' => '请及时查看'
                ]);
            } else {
                $data['character_string1']['value'] = $orderId;
                $data['amount3']['value'] = $order['pay_price'];
                $data['time2']['value'] = date('Y-m-d H:i:s', time());
                $data['thing6']['value'] = '您充值会员已支付成功！';
                RoutineTemplate::sendOrderSuccess($data, $order['uid'], $site_url . Url::build('wap/special/member_recharge'));
                $dataAdmin['character_string1']['value'] = $orderId;
                $dataAdmin['amount3']['value'] = $order['pay_price'];
                $dataAdmin['time2']['value'] = date('Y-m-d H:i:s', time());
                $dataAdmin['thing6']['value'] = '您有一个新的会员购买订单！';
                RoutineTemplate::sendAdminNoticeTemplate($dataAdmin);
            }
        } catch (\Throwable $e) {
        }
        StoreOrderStatus::status($order['id'], 'pay_success', '用户付款成功');
        $res = $res1 && $res2 && $resMer;
        return false !== $res;
    }

    /**
     * //TODO 商品支付成功后
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function payGoodsSuccess($orderId, $trade_no)
    {
        $order = self::where('order_id', $orderId)->where('type', 2)->find();
        if (!$order) return false;
        $res1 = self::where('order_id', $orderId)->where('type', 2)->update(['trade_no' => $trade_no, 'paid' => 1, 'pay_time' => time()]);
        $site_url = SystemConfigService::get('site_url');
        $res2 = true;
        $res3 = true;
        try {
            if ($res1 && $order['pay_type'] != 'yue') {
                $res2 = UserBill::expend('购买商品', $order['uid'], $order['pay_type'], 'pay_goods', $order['pay_price'], $order['id'], 0, '支付' . floatval($order['pay_price']) . '元购买商品');
            }
            if ($res1) {
                $res3 = MerchantFlowingWater::setMerchantFlowingWater($order, 2);
            }
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                $title = implode(',', StoreOrderCartInfo::getProductNameList($order['id']));
                WechatTemplateService::sendTemplate(WechatUser::where('uid', $order['uid'])->value('openid'), WechatTemplateService::ORDER_PAY_SUCCESS, [
                    'first' => '亲，您购买的商品已支付成功',
                    'keyword1' => $title,
                    'keyword2' => $orderId,
                    'keyword3' => $order['pay_price'],
                    'keyword4' => date('Y-m-d H:i:s', time()),
                    'remark' => '点击查看订单详情'
                ], $site_url . Url::build('wap/special/order_store_list'));
                WechatTemplateService::sendAdminNoticeTemplate($order['mer_id'], [
                    'first' => "亲,您有一个新的商品购买订单",
                    'keyword1' => $title,
                    'keyword2' => $orderId,
                    'keyword3' => $order['pay_price'],
                    'keyword4' => date('Y-m-d H:i:s', time()),
                    'remark' => '请及时处理'
                ]);
            } else {
                $data['character_string1']['value'] = $orderId;
                $data['amount3']['value'] = $order['pay_price'];
                $data['time2']['value'] = date('Y-m-d H:i:s', time());
                $data['thing6']['value'] = '您购买的商品已支付成功！';
                RoutineTemplate::sendOrderSuccess($data, $order['uid'], $site_url . Url::build('wap/special/order_store_list'));
                $dataAdmin['character_string1']['value'] = $orderId;
                $dataAdmin['amount3']['value'] = $order['pay_price'];
                $dataAdmin['time2']['value'] = date('Y-m-d H:i:s', time());
                $dataAdmin['thing6']['value'] = '您有一个新的商品购买订单！';
                RoutineTemplate::sendAdminNoticeTemplate($dataAdmin);
            }
            $dat['pay_price'] = $order['pay_price'];
            $dat['order_id'] = $orderId;
            SmsTemplate::sendSms($order['uid'], $dat, 'ORDER_PAY_SUCCESS');
        } catch (\Throwable $e) {
        }
        StoreOrderStatus::status($order['id'], 'pay_success', '用户付款成功');
        $res = $res1 && $res2 && $res3;
        return false !== $res;
    }

    /**
     * 计算普通裂变推广人返佣金额
     * @param int $is_promoter 推广人级别
     * @param float $money 返佣金额
     * @return float
     * */
    public static function getBrokerageMoney($is_promoter, $money)
    {
        $is_promoter = !is_int($is_promoter) ? (int)$is_promoter : $is_promoter;
        $systemName = 'store_brokerage_three_[###]x';
        //配置星级字段和设置如： store_brokerage_three_0x store_brokerage_three_1x
        //后台设置字段从零星开始 $is_promoter 应 -1 才能对应字段
        $store_brokerage_three = $is_promoter ? SystemConfigService::get(str_replace('[###]', $is_promoter - 1, $systemName)) : 100;
        //返佣比例为0则不返佣
        $store_brokerage_three = $store_brokerage_three == 0 ? 0 : bcdiv($store_brokerage_three, 100, 2);
        return bcmul($money, $store_brokerage_three, 2);
    }

    /**获取订单详情
     * @param $uid
     * @param $key
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUserOrderDetail($uid, $key)
    {
        return self::where('order_id|unique', $key)->where('uid', $uid)->where('is_del', 0)->find();
    }

    /**
     * TODO 订单发货
     * @param array $postageData 发货信息
     * @param string $oid orderID
     */
    public static function orderPostageAfter($postageData, $oid)
    {
        $order = self::where('id', $oid)->find();
        $openid = WechatUser::uidToOpenid($order['uid']);
        $url = Url::build('wap/special/order', ['uni' => $order['order_id']], true, true);
        $group = [
            'first' => '亲,您的订单已发货,请注意查收',
            'remark' => '点击查看订单详情'
        ];
        if ($postageData['delivery_type'] == 'express') {//发货
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                $group = array_merge($group, [
                    'keyword1' => $order['order_id'],
                    'keyword2' => $postageData['delivery_name'],
                    'keyword3' => $postageData['delivery_id']
                ]);
                WechatTemplateService::sendTemplate($openid, WechatTemplateService::ORDER_POSTAGE_SUCCESS, $group, $url);
            } else {
                $data['character_string2']['value'] = $order['order_id'];
                $data['thing4']['value'] = $postageData['delivery_name'];
                $data['character_string5']['value'] = $postageData['delivery_id'];
                RoutineTemplate::sendOrderGoods($data, $order['uid'], Url::build('wap/special/order', ['uni' => $order['order_id']], true, true));
            }
            $dat['store_name'] = implode(',', StoreOrderCartInfo::getProductNameList($order['id']));
            $dat['order_id'] = $order['order_id'];
            $dat['phone'] = $order['user_phone'];
            SmsTemplate::sendSms($order['uid'], $dat, 'ORDER_POSTAGE_SUCCESS');
        }
    }

    public static function orderTakeAfter($order)
    {
        $openid = WechatUser::uidToOpenid($order['uid']);
        $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
        if ($wechat_notification_message == 1) {
            WechatTemplateService::sendTemplate($openid, WechatTemplateService::ORDER_TAKE_SUCCESS, [
                'first' => '亲，您的订单已成功签收！',
                'keyword1' => $order['order_id'],
                'keyword2' => implode(',', StoreOrderCartInfo::getProductNameList($order['id'])),
                'keyword3' => $order['pay_price'],
                'keyword4' => date('Y/m/d H:i', time()),
                'remark' => '点击查看订单详情'
            ], Url::build('wap/special/order', ['uni' => $order['order_id']], true, true));
        } else {
            $data['character_string9']['value'] = $order['order_id'];
            $data['thing2']['value'] = mb_substr(implode(',', StoreOrderCartInfo::getProductNameList($order['id'])), 0, 10, 'utf-8');
            $data['date8']['value'] = date('Y/m/d H:i', time());
            RoutineTemplate::sendReceivingGoods($data, $order['uid'], Url::build('wap/special/order', ['uni' => $order['order_id']], true, true));
        }
        $dat['order_id'] = $order['order_id'];
        $dat['store_name'] = implode(',', StoreOrderCartInfo::getProductNameList($order['id']));
        $dat['phone'] = $order['user_phone'];
        SmsTemplate::sendSms($order['uid'], $dat, 'ORDER_TAKE_SUCCESS');
    }

    /**
     * 删除订单
     * @param $uni
     * @param $uid
     * @return bool
     */
    public static function removeOrder($uni, $uid)
    {
        $order = self::getUserOrderDetail($uid, $uni);
        if (!$order) return self::setErrorInfo('订单不存在!');
        $order = self::tidyOrder($order);
//        if ($order['_status']['_type'] != 0 && $order['_status']['_type'] != -2 && $order['_status']['_type'] != 4)
//            return self::setErrorInfo('该订单无法删除!');
        if (false !== self::edit(['is_del' => 1], $order['id'], 'id') &&
            false !== StoreOrderStatus::status($order['id'], 'remove_order', '删除订单'))
            return true;
        else
            return self::setErrorInfo('订单删除失败!');
    }


    /**
     * //TODO 用户确认收货
     * @param $uni
     * @param $uid
     */
    public static function takeOrder($uni, $uid)
    {
        $order = self::getUserOrderDetail($uid, $uni);
        if (!$order) return self::setErrorInfo('订单不存在!');
        $order = self::tidyOrder($order);
        if ($order['_status']['_type'] != 2) return self::setErrorInfo('订单状态错误!');
        self::beginTrans();
        if (false !== self::edit(['status' => 2], $order['id'], 'id') &&
            false !== StoreOrderStatus::status($order['id'], 'user_take_delivery', '用户已收货')) {
            try {
                HookService::listen('store_product_order_user_take_delivery', $order, $uid, false, StoreProductBehavior::class);
            } catch (\Exception $e) {
                return self::setErrorInfo($e->getMessage());
            }
            self::commitTrans();
            return true;
        } else {
            self::rollbackTrans();
            return false;
        }
    }

    public static function tidyOrder($order, $detail = false, $isPic = false)
    {
        if ($detail == true && isset($order['id'])) {
            $cartInfo = self::getDb('StoreOrderCartInfo')->where('oid', $order['id'])->column('cart_info', 'unique') ?: [];
            foreach ($cartInfo as $k => $cart) {
                $cartInfo[$k] = json_decode($cart, true);
                $cartInfo[$k]['unique'] = $k;
            }
            $order['cartInfo'] = $cartInfo;
        }

        $status = [];
        if (!$order['paid'] && $order['pay_type'] == 'offline' && !$order['status'] >= 2) {
            $status['_type'] = 9;
            $status['_title'] = '线下付款';
            $status['_msg'] = '等待商家处理,请耐心等待';
            $status['_class'] = 'nobuy';
        } else if (!$order['paid']) {
            $status['_type'] = 0;
            $status['_title'] = '未支付';
            $status['_msg'] = '立即支付订单吧';
            $status['_class'] = 'nobuy';
        } else if ($order['refund_status'] == 1) {
            $status['_type'] = -1;
            $status['_title'] = '申请退款中';
            $status['_msg'] = '商家审核中,请耐心等待';
            $status['_class'] = 'state-sqtk';
        } else if ($order['refund_status'] == 2) {
            $status['_type'] = -2;
            $status['_title'] = '已退款';
            $status['_msg'] = '已为您退款,感谢您的支持';
            $status['_class'] = 'state-sqtk';
        } else if (!$order['status']) {
            $status['_type'] = 1;
            $status['_title'] = '未发货';
            $status['_msg'] = '商家未发货,请耐心等待';
            $status['_class'] = 'state-nfh';
        } else if ($order['status'] == 1) {
            $status['_type'] = 2;
            $status['_title'] = '待收货';
            $status['_msg'] = date('m月d日H时i分', StoreOrderStatus::getTime($order['id'], 'delivery_goods')) . '服务商已发货';
            $status['_class'] = 'state-ysh';
        } else if ($order['status'] == 2) {
            $status['_type'] = 3;
            $status['_title'] = '待评价';
            $status['_msg'] = '收货完成,请您评价订单';
            $status['_class'] = 'state-ytk';
        } else if ($order['status'] == 3) {
            $status['_type'] = 4;
            $status['_title'] = '交易完成';
            $status['_msg'] = '交易完成,感谢您的支持';
            $status['_class'] = 'state-ytk';
        }
        if ($order['refund_reason_time']) $order['refund_reason_time'] = date('Y-m-d H:i:s', $order['refund_reason_time']);
        if ($order['refund_application_time']) $order['refund_application_time'] = date('Y-m-d H:i:s', $order['refund_application_time']);
        if (isset($order['pay_type']))
            $status['_payType'] = isset(self::$payType[$order['pay_type']]) ? self::$payType[$order['pay_type']] : '其他方式';
        if (isset($order['delivery_type']))
            $status['_deliveryType'] = isset(self::$deliveryType[$order['delivery_type']]) ? self::$deliveryType[$order['delivery_type']] : '其他方式';
        $order['_status'] = $status;
        if ($isPic) {
            $order_details_images = GroupDataService::getData('order_details_images') ?: [];
            foreach ($order_details_images as $image) {
                if (isset($image['order_status']) && $image['order_status'] == $order['_status']['_type']) {
                    $order['status_pic'] = $image['pic'];
                    break;
                }
            }
        }
        return $order;
    }

    public static function statusByWhere($status, $model = null)
    {
        $orderId = StorePink::where('uid', User::getActiveUid())->where('status', 1)->column('order_id', 'id');//获取正在拼团的订单编号
        if ($model == null) $model = new self;
        if ('' === $status)
            return $model;
        else if ($status == 0)
            return $model->where('paid', 0)->where('status', 0)->where('refund_status', 0);
        else if ($status == 1)//待发货
            return $model->where('paid', 1)->where('order_id', 'NOT IN', implode(',', $orderId))->where('status', 0)->where('refund_status', 0);
        else if ($status == 2)
            return $model->where('paid', 1)->where('status', 1)->where('refund_status', 0);
        else if ($status == 3)
            return $model->where('paid', 1)->where('status', 2)->where('refund_status', 0);
        else if ($status == 4)
            return $model->where('paid', 1)->where('refund_status', '>', 0);
        else if ($status == -1)
            return $model->where('paid', 1)->where('refund_status', 1);
        else if ($status == -2)
            return $model->where('paid', 1)->where('refund_status', 2);
        else if ($status == 11) {
            return $model->where('order_id', 'IN', implode(',', $orderId));
        } else
            return $model;
    }

    /**商品订单列表
     * @param $uid
     * @param string $status
     * @param int $first
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUserOrderList($uid, $status = '', $first = 0, $limit = 8)
    {
        $list = self::statusByWhere($status)->where('type', 2)->where('is_del', 0)->where('uid', $uid)
            ->field('is_gift,combination_id,id,order_id,pay_price,total_num,total_price,pay_postage,total_postage,paid,status,refund_status,pay_type,coupon_price,deduction_price,pink_id,delivery_type,refund_reason_time,refund_application_time')
            ->order('add_time DESC')->page($first, $limit)->select()->toArray();
        foreach ($list as $k => $order) {
            $list[$k] = self::tidyOrder($order, true);
        }
        return $list;
    }

    public static function searchUserOrder($uid, $order_id)
    {
        $order = self::where('uid', $uid)->where('order_id', $order_id)->where('is_del', 0)->field('is_gift,combination_id,id,order_id,pay_price,total_num,total_price,pay_postage,total_postage,paid,status,refund_status,pay_type,coupon_price,deduction_price,delivery_type')
            ->order('add_time DESC')->find();
        if (!$order)
            return false;
        else
            return self::tidyOrder($order->toArray(), true);

    }

    public static function orderOver($oid)
    {
        $res = self::edit(['status' => '3'], $oid, 'id');
        if (!$res) exception('评价后置操作失败!');
        StoreOrderStatus::status($oid, 'check_order_over', '用户评价');
    }

    public static function checkOrderOver($oid)
    {
        $uniqueList = StoreOrderCartInfo::where('oid', $oid)->column('unique');
        if (StoreProductReply::where('unique', 'IN', $uniqueList)->where('oid', $oid)->count() == count($uniqueList)) {
            HookService::listen('store_product_order_over', $oid, null, false, StoreProductBehavior::class);
            self::orderOver($oid);
        }
    }

    /**
     * 用户订单数据
     */
    public static function getOrderStatusNum($uid)
    {
        $noBuy = self::where('uid', $uid)->where('paid', 0)->where('type', 2)->where('is_del', 0)->where('refund_status', 0)->count();//未支付订单数量
        $noDelivered = self::where('uid', $uid)->where('paid', 1)->where('type', 2)->where('is_del', 0)->where('status', 0)->where('refund_status', 0)->count();//待发货订单数量
        $noTake = self::where('uid', $uid)->where('paid', 1)->where('type', 2)->where('is_del', 0)->where('status', 1)->where('refund_status', 0)->count();//待收货订单数量
        $noReply = self::where('uid', $uid)->where('paid', 1)->where('type', 2)->where('is_del', 0)->where('status', 2)->where('refund_status', 0)->count();//已完成订单数量
        $sum = self::where('uid', $uid)->where('is_del', 0)->where('type', 2)->count();//订单总数
        $sumPrice = self::where('uid', $uid)->where('paid', 1)->where('type', 2)->where('refund_status', 0)->where('is_del', 0)->sum('pay_price');//订单总消费
        $refund = self::where('uid', $uid)->where('paid', 1)->where('type', 2)->where('is_del', 0)->where('refund_status', '>', 0)->count();//退款订单数量
        return compact('noBuy', 'noDelivered', 'noTake', 'noReply', 'sum', 'sumPrice', 'refund');
    }

    /**购买商品赠送虚拟币
     * @param $order
     * @return bool
     * @throws \Exception
     */
    public static function gainUserGoldNum($order)
    {
        $gold_name = SystemConfigService::get('gold_name');//虚拟币名称
        if ($order['gain_gold_num'] <= 0) return true;
        $userInfo = User::getUserData($order['uid']);
        if (!$userInfo) return false;
        ModelBasic::beginTrans();
        $res1 = false != User::where('uid', $userInfo['uid'])->update(['gold_num' => bcadd($userInfo['gold_num'], $order['gain_gold_num'], 2)]);
        $res2 = false != UserBill::income('购买商品赠送' . $gold_name, $order['uid'], 'gold_num', 'gain', $order['gain_gold_num'], $order['id'], $userInfo['gold_num'], '购买商品赠送' . floatval($order['gain_gold_num']) . $gold_name);
        $res = $res1 && $res2;
        ModelBasic::checkTrans($res);
        return $res;

    }

    /**
     * 获取当前订单中有没有拼团存在
     * @param $pid
     * @return int|string
     */
    public static function getIsOrderPink($pid)
    {
        $uid = User::getActiveUid();
        return self::where('uid', $uid)->where('pink_id', $pid)->where('refund_status', 0)->where('is_del', 0)->count();
    }

    /**
     * 获取order_id
     * @param $pid
     * @return mixed
     */
    public static function getStoreIdPink($pid)
    {
        $uid = User::getActiveUid();
        return self::where('uid', $uid)->where('pink_id', $pid)->where('is_del', 0)->value('order_id');
    }

    /**
     * 删除当前用户拼团未支付的订单
     */
    public static function delCombination()
    {
        self::where('combination', 'GT', 0)->where('paid', 0)->where('uid', User::getActiveUid())->delete();
    }

    public static function getPinkT($pink_id)
    {
        $pink = StorePink::getPinkUserOne($pink_id);
        if (isset($pink['is_refund']) && $pink['is_refund']) {
            if ($pink['is_refund'] != $pink['id']) {
                $id = $pink['is_refund'];
                return self::getPinkT($id);
            } else {
                return self::setErrorInfo('订单已退款');
            }
        }
        return $pink;
    }

    public static function getOrderSpecialInfo($orderId, $uid)
    {
        $is_ok = 0;//判断拼团是否完成
        $userBool = 0;//判断当前用户是否在团内  0未在 1在
        $pinkBool = 0;//判断拼团状态 0未完成 1 已完成 2拼团时间已到,退款中 3拼团已结束,请您参加别的拼团 6 拼团人数已满
        $pink_id = self::where('order_id', $orderId)->value('pink_id');
        $pink = self::getPinkT($pink_id);
        if (!$pink) return self::setErrorInfo('没有查到拼团信息');
        if ($pink['is_refund']) return self::setErrorInfo('订单已退款,无法查看');
        list($pinkAll, $pinkT, $count, $idAll, $uidAll) = StorePink::getPinkMemberAndPinkK($pink);
        if ($pinkT['status'] == 2) {
            $pinkBool = 1;
        } else {
            if (!$count) {//组团完成
                $pinkBool = StorePink::PinkComplete($uidAll, $idAll, $uid, $pinkT);
            } else {//拼团失败 退款
                $pinkBool = StorePink::PinkFail($uid, $idAll, $pinkAll, $pinkT, (int)$count, $pinkBool, $uidAll);
            }
        }
        if (!empty($pinkAll)) {
            foreach ($pinkAll as $v) {
                if ($v['uid'] == $uid) $userBool = 1;
            }
        }
        if ($pinkT['uid'] == $uid) $userBool = 1;
        $data['pinkBool'] = $pinkBool;
        $data['is_ok'] = $is_ok;
        $data['userBool'] = $userBool;
        $data['pinkT'] = $pinkT;
        $data['pinkAll'] = $pinkAll;
        $data['count'] = $count;
        $data['current_pink_order'] = StorePink::getCurrentPink($pink_id);
        $data['special'] = Special::PreWhere()->where('id', self::where('order_id', $pinkT['order_id'])->value('cart_id'))->field(['id', 'image', 'title', 'money', 'pink_money'])->find();
        if (!$data['special']) return self::setErrorInfo('专题未查找到');
        $data['special_id'] = $data['special']['id'];
        return $data;
    }
}
