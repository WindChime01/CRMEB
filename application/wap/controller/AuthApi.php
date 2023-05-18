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


namespace app\wap\controller;


use app\wap\model\user\SmsCode;
use app\wap\model\store\StoreOrderCartInfo;
use app\wap\model\store\StorePink;
use app\wap\model\store\StoreProductReply;
use app\wap\model\store\StoreService;
use app\wap\model\store\StoreServiceLog;
use app\wap\model\store\StoreCart;
use app\wap\model\store\StoreCategory;
use app\wap\model\store\StoreOrder;
use app\wap\model\store\StoreProduct;
use app\wap\model\special\Lecturer;
use app\wap\model\store\StoreProductRelation;
use app\wap\model\special\Special as SpecialModel;
use app\wap\model\user\User;
use app\wap\model\user\UserAddress;
use app\wap\model\user\UserBill;
use app\wap\model\user\UserExtract;
use app\wap\model\user\UserRecharge;
use app\wap\model\user\UserNotice;
use app\wap\model\user\UserSign;
use app\wap\model\user\SignPoster;
use app\wap\model\user\WechatUser;
use behavior\wap\StoreProductBehavior;
use service\AliMessageService;
use service\FastknifeService;
use service\WechatTemplateService;
use service\CacheService;
use service\HookService;
use service\JsonService;
use service\SystemConfigService;
use service\GroupDataService;
use service\UtilService;
use service\WechatService;
use think\Cache;
use think\Request;
use think\Session;
use think\Url;
use app\wap\model\user\MemberShip;
use app\wap\model\user\MemberCard;
use app\wap\model\user\MemberCardBatch;
use service\sms\storage\Sms;
use service\express\storage\Express;
use app\admin\model\system\Express as ExpressModel;
use think\Config;

/**接口
 * Class AuthApi
 * @package app\wap\controller
 */
class AuthApi extends AuthController
{

    public static function WhiteList()
    {
        return [
            'code',
            'ajcaptcha',
            'ajcheck',
            'query',
            'getLecturer',
            'merberDatas',
            'suspensionButton',
            'rebateAmount',
            'getVersion',
            'product_reply_list',
            'product_reply_data',
            'captcha',
            'del_redis_phone'
        ];
    }

    /**订单物流查询
     * @param string $delivery_id
     */
    public function query($delivery_id = '', $phone = '')
    {
        if (!$delivery_id) return JsonService::fail('参数错误');
        $expressHandle = new Express();
        $sf = substr($delivery_id, 0, 2);
        if ($sf == 'SF') {
            $phone = substr($phone, -4);
            $delivery_id = $delivery_id . ':' . $phone;
        }
        $res = $expressHandle->query('', $delivery_id);
        if ($res['status'] == 200) {
            return JsonService::successful($res['msg'], $res['data']);
        } else {
            return JsonService::fail($res['msg']);
        }
    }

    /**详情页讲师信息
     * @param $mer_id
     */
    public function getLecturer($mer_id)
    {
        if (!$mer_id) return JsonService::successful(null);
        $lecturer = Lecturer::information($mer_id);
        return JsonService::successful($lecturer);
    }

    /**获取单个专题、商品、会员的返佣金额
     * @param int $id
     * @param string $type 0:专题 1:会员 2:商品
     */
    public function rebateAmount($id = 0, $type = 0)
    {
        if (!$id) return JsonService::fail('参数错误!');
        $brokerageRatio = 0;
        switch ($type) {
            case 0:
                $data = SpecialModel::where('id', $id)->field('pay_type,money,is_alone,brokerage_ratio,brokerage_two')->find();
                if (isset($data['pay_type']) && $data['pay_type'] == 1 && $data['money'] > 0) {
                    if (isset($data['is_alone']) && $data['is_alone']) {
                        if (isset($data['brokerage_ratio']) && $data['brokerage_ratio']) {
                            $brokerageRatio = bcdiv($data['brokerage_ratio'], 100, 2);
                        }
                    } else {
                        $course_distribution_switch = SystemConfigService::get('course_distribution_switch');//课程分销开关
                        if ($course_distribution_switch == 1) {
                            $brokerageRatio = bcdiv(SystemConfigService::get('store_brokerage_ratio'), 100, 2);
                        }
                    }
                }
                $brokeragePrice = bcmul($data['money'], $brokerageRatio, 2);
                return JsonService::successful(['brokeragePrice' => $brokeragePrice]);
                break;
            case 1:
                $data = MemberShip::where('id', $id)->field('is_free,price,is_alone,brokerage_ratio,brokerage_two')->find();
                if (isset($data['is_free']) && $data['is_free'] == 0 && $data['price'] > 0) {
                    if (isset($data['is_alone']) && $data['is_alone']) {
                        if (isset($data['brokerage_ratio']) && $data['brokerage_ratio']) {
                            $brokerageRatio = bcdiv($data['brokerage_ratio'], 100, 2);
                        }
                    } else {
                        $member_distribution_switch = SystemConfigService::get('member_distribution_switch');//会员分销开关
                        if ($member_distribution_switch == 1) {
                            $brokerageRatio = bcdiv(SystemConfigService::get('member_brokerage_ratio'), 100, 2);
                        }
                    }
                }
                $brokeragePrice = bcmul($data['price'], $brokerageRatio, 2);
                return JsonService::successful(['brokeragePrice' => $brokeragePrice]);
                break;
            case 2:
                $data = StoreProduct::where('id', $id)->field('price,is_alone,brokerage_ratio,brokerage_two')->find();
                if ($data['price'] > 0) {
                    if (isset($data['is_alone']) && $data['is_alone']) {
                        if (isset($data['brokerage_ratio']) && $data['brokerage_ratio']) {
                            $brokerageRatio = bcdiv($data['brokerage_ratio'], 100, 2);
                        }
                    } else {
                        $course_distribution_switch = SystemConfigService::get('goods_distribution_switch');//商品分销开关
                        if ($course_distribution_switch == 1) {
                            $brokerageRatio = bcdiv(SystemConfigService::get('goods_brokerage_ratio'), 100, 2);
                        }
                    }
                }
                $brokeragePrice = bcmul($data['price'], $brokerageRatio, 2);
                return JsonService::successful(['brokeragePrice' => $brokeragePrice]);
                break;
        }
    }

    public function upload()
    {
        $aliyunOss = \Api\AliyunOss::instance([
            'AccessKey' => SystemConfigService::get('accessKeyId'),
            'AccessKeySecret' => SystemConfigService::get('accessKeySecret'),
            'OssEndpoint' => SystemConfigService::get('end_point'),
            'OssBucket' => SystemConfigService::get('OssBucket'),
            'uploadUrl' => SystemConfigService::get('uploadUrl'),
        ]);
        $res = $aliyunOss->upload('file');
        if ($res && isset($res['url'])) {
            return JsonService::successful('上传成功', ['url' => $res['url']]);
        } else {
            return JsonService::fail('上传失败');
        }
    }

    public function getVersion()
    {
        $version = getversion();
        return JsonService::successful($version);
    }

    /**
     * @return mixed
     */
    public function ajcaptcha()
    {
        $captchaType = $this->request->get('captchaType', 'blockPuzzle');
        return JsonService::successful('ok', FastknifeService::aj_captcha_create($captchaType));
    }

    /**
     * 一次验证
     * @return mixed
     */
    public function ajcheck()
    {
        $data = UtilService::postMore([
            ['token', ''],
            ['pointJson', ''],
            ['captchaType', '']
        ]);
        try {
            FastknifeService::aj_captcha_check_one($data['captchaType'], $data['token'], $data['pointJson']);
            return JsonService::successful('滑块验证成功');
        } catch (\Throwable $e) {
            return JsonService::fail('滑块验证失败');
        }
    }

    /**
     * 发送短信验证码
     * @param string $phone
     */
    public function code()
    {
        $data = UtilService::postMore([
            ['phone', ''],
            ['captchaType', ''],
            ['captchaVerification', '']
        ]);
        $phone = $data['phone'];
        $name = "is_phone_code" . $phone;
        if ($phone == '') return JsonService::fail('请输入手机号码!');
        if (!$data['captchaType'] || !$data['captchaVerification']) return JsonService::fail('滑块验证参数错误！');
        try {
            FastknifeService::aj_captcha_check_two($data['captchaType'], $data['captchaVerification']);
        } catch (\Throwable $e) {
            return JsonService::fail('滑块验证失败');
        }
        $time = Session::get($name, 'wap');
        if ($time < time() + 60) Session::delete($name, 'wap');
        if (Session::has($name, 'wap') && $time < time()) return JsonService::fail('您发送验证码的频率过高,请稍后再试!');
        $code = AliMessageService::getVerificationCode();
        SmsCode::set(['tel' => $phone, 'code' => md5('is_phone_code' . $code), 'last_time' => time() + 300, 'uid' => $this->uid]);
        Session::set($name, time() + 60, 'wap');
        $smsHandle = new Sms();
        $sms_platform_selection = SystemConfigService::get('sms_platform_selection');
        $smsSignName = SystemConfigService::get('smsSignName');//短信签名
        $smsTemplateCode = SystemConfigService::get('smsTemplateCode');//短信模板ID
        if ($sms_platform_selection == 1) {
            if (!$smsSignName || !$smsTemplateCode) return JsonService::fail('系统后台短信没有配置，请稍后在试!');
            $res = AliMessageService::sendmsg($phone, $smsTemplateCode, ['code' => $code]);
        } else {
            if (!(int)$smsTemplateCode) return JsonService::fail('请正确的填写系统后台短信配置!');
            $res = $smsHandle->send($phone, $smsTemplateCode, ['code' => $code]);
        }
        if ($res['Code'] == 'OK') {
            return JsonService::successful('发送成功', $res);
        } else {
            return JsonService::fail($res['Message']);
        }
    }

    /**
     * 悬浮按钮
     */
    public function suspensionButton()
    {
        $suspension = GroupDataService::getData('suspension_button');
        return JsonService::successful($suspension);
    }

    /**
     * 用户签到信息
     */
    public function getUserList()
    {
        $signList = UserSign::userSignInlist($this->uid, 1, 3);
        return JsonService::successful($signList);
    }

    /**
     * 签到明细
     */
    public function getUserSignList($page, $limit)
    {
        $signList = UserSign::userSignInlist($this->uid, $page, $limit);
        return JsonService::successful($signList);
    }

    /**
     * 签到
     */
    public function user_sign()
    {
        $gold_name = SystemConfigService::get('gold_name');//虚拟币名称
        $signed = UserSign::checkUserSigned($this->uid);
        if ($signed) return JsonService::fail('已签到');
        if (false !== $gold_coin = UserSign::sign($this->userInfo, $gold_name)) {
            return JsonService::successful('签到获得' . floatval($gold_coin) . $gold_name);
        } else
            return JsonService::fail('签到失败!');
    }

    /**
     * 获取前端海报信息
     */
    public function get_user_sign_poster()
    {
        $poster = SignPoster::todaySignPoster($this->uid);
        if ($poster) {
            return JsonService::successful($poster);
        } else {
            return JsonService::fail('生成海报失败!');
        }
    }

    /**
     * 用户信息
     */
    public function userInfo()
    {
        $user = $this->userInfo;
        $surplus = 0; //会员剩余天数
        $time = bcsub($user['overdue_time'], time(), 0);
        if ($user['level'] > 0 && $time > 0) $surplus = bcdiv($time, 86400, 2);
        $user['surplus'] = ceil($surplus);
        return JsonService::successful($user);
    }

    /**
     * 商品退款原因
     */
    public function refund_reason()
    {
        $goods_order_refund_reason = GroupDataService::getData('goods_order_refund_reason') ?: [];
        return JsonService::successful($goods_order_refund_reason);
    }

    /**
     * 会员页数据
     */
    public function merberDatas()
    {
        $interests = GroupDataService::getData('membership_interests', 3) ?: [];
        $description = GroupDataService::getData('member_description') ?: [];
        $data['interests'] = $interests;
        $data['description'] = $description;
        return JsonService::successful($data);
    }

    /**
     * 会员设置列表
     */
    public function membershipLists()
    {
        $meList = MemberShip::membershipList($this->uid);
        return JsonService::successful($meList);
    }

    /**
     * 会员卡激活
     */
    public function confirm_activation()
    {
        $request = Request::instance();
        if (!$request->isPost()) return JsonService::fail('参数错误!');
        $data = UtilService::postMore([
            ['member_code', ''],
            ['member_pwd', ''],
        ], $request);
        $res = MemberCard::confirmActivation($data, $this->userInfo);
        if ($res)
            return JsonService::successful('激活成功');
        else
            return JsonService::fail(MemberCard::getErrorInfo('激活失败!'));
    }

    /**
     * 用户领取免费会员
     */
    public function memberPurchase($id = 0)
    {
        if (!$id) return JsonService::fail('参数错误!');
        $order = StoreOrder::cacheMemberCreateOrder($this->uid, $id, 'weixin');
        $orderId = $order['order_id'];
        $info = compact('orderId');
        if ($orderId) {
            $orderInfo = StoreOrder::where('order_id', $orderId)->find();
            if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
            if ($orderInfo['paid']) exception('支付已支付!');
            if (bcsub((float)$orderInfo['pay_price'], 0, 2) <= 0) {
                if (StoreOrder::jsPayMePrice($orderId, $this->uid))
                    return JsonService::status('success', '领取成功', $info);
                else
                    return JsonService::status('pay_error', StoreOrder::getErrorInfo());
            } else {
                try {
                    $jsConfig = StoreOrder::jsPayMember($orderId);
                } catch (\Exception $e) {
                    return JsonService::status('pay_error', $e->getMessage(), $info);
                }
                $info['jsConfig'] = $jsConfig;
                return JsonService::status('wechat_pay', '领取成功', $info);
            }
        } else {
            return JsonService::fail(StoreOrder::getErrorInfo('领取失败!'));
        }
    }

    /**加入购物车
     * @param string $productId
     * @param int $cartNum
     * @param string $uniqueId
     */
    public function set_cart($productId = '', $cartNum = 1, $uniqueId = '')
    {
        if (!$productId || !is_numeric($productId)) return $this->failed('参数错误!');
        $res = StoreCart::setCart($this->uid, $productId, $cartNum, $uniqueId, 'product');
        if (!$res)
            return $this->failed(StoreCart::getErrorInfo('加入购物车失败!'));
        else {
            HookService::afterListen('store_product_set_cart_after', $res, $this->userInfo, false, StoreProductBehavior::class);
            return $this->successful('ok', ['cartId' => $res->id]);
        }
    }

    /**加入购物车立即购买
     * @param int $productId
     * @param int $cartNum
     * @param string $uniqueId
     */
    public function now_buy($productId = 0, $cartNum = 1, $uniqueId = '')
    {
        if ($productId == '' || $productId < 0) return $this->failed('参数错误!');
        $res = StoreCart::setCart($this->uid, $productId, $cartNum, $uniqueId, 'product', 1);
        if (!$res)
            return $this->failed(StoreCart::getErrorInfo('加入购物车失败!'));
        else {
            return $this->successful('ok', ['cartId' => $res->id]);
        }
    }

    /**点赞
     * @param string $productId
     * @param string $category
     */
    public function like_product($productId = '', $category = 'product')
    {
        if (!$productId || !is_numeric($productId)) return $this->failed('参数错误!');
        $res = StoreProductRelation::productRelation($productId, $this->uid, 'like', $category);
        if (!$res)
            return $this->failed(StoreProductRelation::getErrorInfo('点赞失败!'));
        else
            return $this->successful();
    }

    /**取消点赞
     * @param string $productId
     * @param string $category
     */
    public function unlike_product($productId = '', $category = 'product')
    {
        if (!$productId || !is_numeric($productId)) return $this->failed('参数错误!');
        $res = StoreProductRelation::unProductRelation($productId, $this->uid, 'like', $category);
        if (!$res)
            return $this->failed(StoreProductRelation::getErrorInfo('取消点赞失败!'));
        else
            return $this->successful();
    }

    /**商品收藏
     * @param $productId
     * @param string $category
     */
    public function collect_product($productId, $category = 'product')
    {
        if (!$productId || !is_numeric($productId)) return $this->failed('参数错误!');
        $res = StoreProductRelation::productRelation($productId, $this->uid, 'collect', $category);
        if (!$res)
            return $this->failed(StoreProductRelation::getErrorInfo('收藏失败!'));
        else
            return $this->successful();
    }

    /**商品取消收藏
     * @param $productId
     * @param string $category
     */
    public function uncollect_product($productId, $category = 'product')
    {
        if (!$productId || !is_numeric($productId)) return $this->failed('参数错误!');
        $res = StoreProductRelation::unProductRelation($productId, $this->uid, 'collect', $category);
        if (!$res)
            return $this->failed(StoreProductRelation::getErrorInfo('取消收藏失败!'));
        else
            return $this->successful();
    }

    public function get_cart_num()
    {
        return JsonService::successful('ok', StoreCart::getUserCartNum($this->uid, 'product'));
    }

    public function get_cart_list()
    {
        return JsonService::successful('ok', StoreCart::getUserProductCartList($this->uid));
    }

    public function change_cart_num($cartId = '', $cartNum = '')
    {
        if (!$cartId || !$cartNum || !is_numeric($cartId) || !is_numeric($cartNum)) return JsonService::fail('参数错误!');
        StoreCart::changeUserCartNum($cartId, $cartNum, $this->uid);
        return JsonService::successful();
    }

    public function remove_cart($ids = '')
    {
        if (!$ids) return JsonService::fail('参数错误!');
        StoreCart::removeUserCart($this->uid, $ids);
        return JsonService::successful();
    }

    public function get_user_collect_product($first = 0, $limit = 8)
    {
        $list = StoreProductRelation::where('A.uid', $this->uid)
            ->field('B.id pid,B.store_name,B.price,B.ot_price,B.sales,B.image,B.is_del,B.is_show')->alias('A')
            ->where('A.type', 'collect')->where('A.category', 'product')
            ->order('A.add_time DESC')->join('__STORE_PRODUCT__ B', 'A.product_id = B.id')
            ->limit($first, $limit)->select()->toArray();
        foreach ($list as $k => $product) {
            if ($product['pid']) {
                $list[$k]['is_fail'] = $product['is_del'] && $product['is_show'];
            } else {
                unset($list[$k]);
            }
        }
        return JsonService::successful($list);
    }

    public function remove_user_collect_product($productId = '')
    {
        if (!$productId || !is_numeric($productId)) return JsonService::fail('参数错误!');
        StoreProductRelation::unProductRelation($productId, $this->uid, 'collect', 'product');
        return JsonService::successful();
    }

    /**设置默认地址
     * @param string $addressId
     */
    public function set_user_default_address($addressId = '')
    {
        if (!$addressId || !is_numeric($addressId)) return JsonService::fail('参数错误!');
        if (!UserAddress::be(['is_del' => 0, 'id' => $addressId, 'uid' => $this->uid]))
            return JsonService::fail('地址不存在!');
        $res = UserAddress::setDefaultAddress($addressId, $this->uid);
        if (!$res)
            return JsonService::fail('地址不存在!');
        else
            return JsonService::successful();
    }

    /**
     * 添加和修改地址
     */
    public function edit_user_address()
    {
        $request = Request::instance();
        if (!$request->isPost()) return JsonService::fail('参数错误!');
        $addressInfo = UtilService::postMore([
            ['address', []],
            ['is_default', false],
            ['real_name', ''],
            ['post_code', ''],
            ['phone', ''],
            ['detail', ''],
            ['id', 0]
        ], $request);
        $addressInfo['province'] = $addressInfo['address']['province'];
        $addressInfo['city'] = $addressInfo['address']['city'];
        $addressInfo['district'] = $addressInfo['address']['district'];
        $addressInfo['is_default'] = $addressInfo['is_default'] == true ? 1 : 0;
        $addressInfo['uid'] = $this->uid;
        unset($addressInfo['address']);

        if ($addressInfo['id'] && UserAddress::be(['id' => $addressInfo['id'], 'uid' => $this->uid, 'is_del' => 0])) {
            $id = $addressInfo['id'];
            unset($addressInfo['id']);
            if (UserAddress::edit($addressInfo, $id, 'id')) {
                if ($addressInfo['is_default'])
                    UserAddress::setDefaultAddress($id, $this->uid);
                return JsonService::successful();
            } else
                return JsonService::fail('编辑收货地址失败!');
        } else {
            if ($address = UserAddress::set($addressInfo)) {
                if ($addressInfo['is_default'])
                    UserAddress::setDefaultAddress($address->id, $this->uid);
                return JsonService::successful();
            } else
                return JsonService::fail('添加收货地址失败!');
        }
    }

    /**
     * 获取用户所有地址
     */
    public function user_address_list()
    {
        $list = UserAddress::getUserValidAddressList($this->uid, 'id,real_name,phone,province,city,district,detail,is_default');
        if ($list)
            return JsonService::successful('ok', $list);
        else
            return JsonService::successful('empty', []);
    }

    /**
     * 获取默认地址
     */
    public function user_default_address()
    {
        $defaultAddress = UserAddress::getUserDefaultAddress($this->uid, 'id,real_name,phone,province,city,district,detail,is_default');
        if ($defaultAddress)
            return JsonService::successful('ok', $defaultAddress);
        else
            return JsonService::successful('empty', []);
    }

    /**删除地址
     * @param string $addressId
     */
    public function remove_user_address($addressId = '')
    {
        if (!$addressId || !is_numeric($addressId)) return JsonService::fail('参数错误!');
        if (!UserAddress::be(['is_del' => 0, 'id' => $addressId, 'uid' => $this->uid]))
            return JsonService::fail('地址不存在!');
        if (UserAddress::edit(['is_del' => '1'], $addressId, 'id'))
            return JsonService::successful();
        else
            return JsonService::fail('删除地址失败!');
    }


    /**获取用户的商品订单列表
     * @param string $type
     * @param int $first
     * @param int $limit
     */
    public function get_user_order_list($first = 0, $limit = 8, $type = '')
    {
        $list = StoreOrder::getUserOrderList($this->uid, $type, $first, $limit);
        return JsonService::successful($list);
    }

    /**
     * 用户订单数据
     */
    public function userOrderDate()
    {
        $data = StoreOrder::getOrderStatusNum($this->uid);
        return JsonService::successful($data);
    }

    /**删除订单
     * @param string $uni
     */
    public function user_remove_order($uni = '')
    {
        if (!$uni) return JsonService::fail('参数错误!');
        $res = StoreOrder::removeOrder($uni, $this->uid);
        if ($res)
            return JsonService::successful();
        else
            return JsonService::fail(StoreOrder::getErrorInfo());
    }


    /**提交商品退款
     * @param string $uni
     * @param string $text
     */
    public function apply_order_refund($uni = '')
    {
        if (!$uni) return JsonService::fail('参数错误!');
        $request = Request::instance();
        if (!$request->isPost()) return JsonService::fail('参数错误!');
        $data = UtilService::postMore([
            ['pics', []],
            ['refund_reason', ''],
            ['remarks', ''],
        ], $request);
        $res = StoreOrder::orderApplyRefund($uni, $this->uid, $data);
        if ($res)
            return JsonService::successful();
        else
            return JsonService::fail(StoreOrder::getErrorInfo());
    }

    /**用户确认收货
     * @param string $uni
     */
    public function user_take_order($uni = '')
    {
        if (!$uni) return JsonService::fail('参数错误!');
        $res = StoreOrder::takeOrder($uni, $this->uid);
        if ($res)
            return JsonService::successful();
        else
            return JsonService::fail(StoreOrder::getErrorInfo());
    }

    /**虚拟币充值
     * @param int $price
     * @param int $payType
     */
    public function user_wechat_recharge($price = 0, $payType = 0, $from = 'weixin')
    {
        if (!$price || $price <= 0 || !is_numeric($price)) return JsonService::fail('参数错误');
        if (!isset($this->uid) || !$this->uid) return JsonService::fail('用户不存在');
        try {
            //充值记录
            $rechargeOrder = UserRecharge::addRecharge($this->uid, $price, $payType);
            if (!$rechargeOrder) return JsonService::fail('充值订单生成失败!');
            $orderId = $rechargeOrder['order_id'];
            $goldName = SystemConfigService::get("gold_name");
            switch ($payType) {
                case 'weixin':
                    try {
                        if ($from == 'weixinh5') {
                            $jsConfig = UserRecharge::h5RechargePay($orderId);
                        } else {
                            $jsConfig = UserRecharge::jsRechargePay($orderId);
                        }
                    } catch (\Exception $e) {
                        return JsonService::status('pay_error', $e->getMessage(), $rechargeOrder);
                    }
                    $rechargeOrder['jsConfig'] = $jsConfig;
                    if ($from == 'weixinh5') {
                        return JsonService::status('wechat_h5_pay', '订单创建成功', $rechargeOrder);
                    } else {
                        return JsonService::status('wechat_pay', '订单创建成功', $rechargeOrder);
                    }
                    break;
                case 'yue':
                    if (UserRecharge::yuePay($orderId, $this->userInfo))
                        return JsonService::status('success', '余额支付成功', $rechargeOrder);
                    else
                        return JsonService::status('pay_error', UserRecharge::getErrorInfo());
                    break;
                case 'zhifubao':
                    $rechargeOrder['orderName'] = $goldName . "充值";
                    $rechargeOrder['orderId'] = $orderId;
                    $rechargeOrder['pay_price'] = $price;
                    return JsonService::status('zhifubao_pay', '订单创建成功', base64_encode(json_encode($rechargeOrder)));
                    break;
            }
        } catch (\Exception $e) {
            return JsonService::fail($e->getMessage());
        }
    }

    /**余额明细
     * @param int $index
     * @param int $first
     * @param int $limit
     */
    public function user_balance_list($index = 0, $first = 0, $limit = 8)
    {
        $model = UserBill::where('uid', $this->uid)->where('category', 'now_money')
            ->where('type', 'not in', 'gain,deduction,extract,extract_fail,brokerage,brokerage_return,sign,pay_vip,extract_success')
            ->field('title,mark,pm,number,add_time')
            ->where('status', 1)->where('number', '>', 0);
        switch ($index) {
            case 1:
                $model = $model->where('pm', 0);
                break;
            case 2:
                $model = $model->where('pm', 1);
                break;
        }
        $list = $model->order('add_time DESC')->page((int)$first, (int)$limit)->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        foreach ($list as &$v) {
            $v['add_time'] = date('Y/m/d H:i', $v['add_time']);
        }
        return JsonService::successful($list);
    }

    /**金币明细
     * @param int $index
     * @param int $first
     * @param int $limit
     */
    public function user_gold_num_list($index = 0, $first = 0, $limit = 8)
    {
        $model = UserBill::where('uid', $this->uid)->where('category', 'gold_num')
            ->field('title,mark,pm,number,add_time')
            ->where('status', 1)->where('number', '>', 0);
        switch ($index) {
            case 1:
                $model = $model->where('pm', 0);
                break;
            case 2:
                $model = $model->where('pm', 1);
                break;
        }
        $list = $model->order('add_time DESC')->page((int)$first, (int)$limit)->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        foreach ($list as &$v) {
            $v['add_time'] = date('Y/m/d H:i', $v['add_time']);
        }
        return JsonService::successful($list);
    }

    /**用户商品评价
     * @param string $unique
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function user_comment_product($unique = '')
    {
        if (!$unique) return JsonService::fail('参数错误!');
        $cartInfo = StoreOrderCartInfo::where('unique', $unique)->find();
        $uid = $this->uid;
        if (!$cartInfo || $uid != $cartInfo['cart_info']['uid']) return JsonService::fail('评价产品不存在!');
        if (StoreProductReply::be(['oid' => $cartInfo['oid'], 'unique' => $unique]))
            return JsonService::fail('该产品已评价!');
        $group = UtilService::postMore([
            ['comment', ''], ['pics', []], ['product_score', 5], ['service_score', 5], ['delivery_score', 5]
        ]);
        $group['comment'] = htmlspecialchars(trim($group['comment']));
        if (sensitive_words_filter($group['comment'])) return JsonService::fail('请注意您的用词，谢谢！！');
        if ($group['product_score'] < 1) return JsonService::fail('请为产品评分');
        else if ($group['service_score'] < 1) return JsonService::fail('请为服务评分');
        else if ($group['delivery_score'] < 1) return JsonService::fail('请为物流评分');
        $group = array_merge($group, [
            'uid' => $uid,
            'oid' => $cartInfo['oid'],
            'unique' => $unique,
            'product_id' => $cartInfo['product_id'],
            'reply_type' => 'product'
        ]);
        StoreProductReply::beginTrans();
        $res = StoreProductReply::reply($group, 'product');
        if (!$res) {
            StoreProductReply::rollbackTrans();
            return JsonService::fail('评价失败!');
        }
        try {
            HookService::listen('store_product_order_reply', $group, $cartInfo, false, StoreProductBehavior::class);
        } catch (\Exception $e) {
            StoreProductReply::rollbackTrans();
            return JsonService::fail($e->getMessage());
        }
        StoreProductReply::commitTrans();
        return JsonService::successful('评价成功!');
    }

    /**获取商品评价列表
     * @param string $productId
     * @param int $first
     * @param int $limit
     * @param string $filter
     */
    public function product_reply_list($productId = '', $page = 0, $limit = 8, $score = 4, $filter = 'all')
    {
        if (!$productId || !is_numeric($productId)) return JsonService::fail('参数错误!');
        $list = StoreProductReply::getProductReplyList($productId, $page, $limit, $score, $filter);
        return JsonService::successful($list);
    }

    /**
     * 评价数据
     */
    public function product_reply_data($productId = '')
    {
        if (!$productId || !is_numeric($productId)) return JsonService::fail('参数错误!');
        $data = StoreProductReply::getProductReplyData($productId);
        return JsonService::successful($data);
    }

    public function get_spread_list($first = 0, $limit = 20)
    {
        $list = User::where('spread_uid', $this->uid)->field('uid,nickname,avatar,add_time')->limit($first, $limit)->order('add_time DESC')->select()->toArray();
        foreach ($list as $k => $user) {
            $list[$k]['add_time'] = date('Y/m/d', $user['add_time']);
        }
        return JsonService::successful($list);
    }

    public function refresh_msn(Request $request)
    {
        $params = $request->post();
        $remind_where = "mer_id = " . $params["mer_id"] . " AND uid = " . $params["uid"] . " AND to_uid = " . $params["to_uid"] . " AND type = 0 AND remind = 0";
        $remind_list = StoreServiceLog::where($remind_where)->order("add_time asc")->select();
        foreach ($remind_list as $key => $value) {
            if (time() - $value["add_time"] > 3) {
                StoreServiceLog::edit(array("remind" => 1), $value["id"]);
                if ($params["to_uid"]) {
                    $userInfo = WechatUser::where('uid', $params["to_uid"])->field(['openid', 'subscribe'])->find();
                    if ($userInfo && $userInfo['openid'] && $userInfo['subscribe']) {
                        $head = '客服提醒';
                        $description = '您有新的消息，请注意查收！';
                        $url = SystemConfigService::get('site_url') . '/wap/service/service_ing/to_uid/' . $this->uid . '/mer_id/0';
                        $message = WechatService::newsMessage($head, $description, $url, $this->userInfo['avatar']);
                        try {
                            WechatService::staffService()->message($message)->to($userInfo['openid'])->send();
                        } catch (\Exception $e) {
                            \think\Log::error($userInfo['nickname'] . '发送失败' . $e->getMessage());
                        }
                    }
                }
            }
        }
        $where = "mer_id = " . $params["mer_id"] . " AND uid = " . $params["to_uid"] . " AND to_uid = " . $params["uid"] . " AND type = 0";
        $list = StoreServiceLog::where($where)->order("add_time asc")->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        $ids = [];
        foreach ($list as $key => $value) {
            //设置发送人与接收人区别
            if ($value["uid"] == $params["uid"])
                $list[$key]['my'] = "my";
            else
                $list[$key]['my'] = "to";

            array_push($ids, $value["id"]);
        }

        //设置这些消息为已读
        StoreServiceLog::where(array("id" => array("in", $ids)))->update(array("type" => 1, "remind" => 1));
        return JsonService::successful($list);
    }

    public function add_msn(Request $request)
    {
        $params = $request->post();
        if ($params["type"] == "html")
            $data["msn"] = htmlspecialchars_decode($params["msn"]);
        else
            $data["msn"] = $params["msn"];
        $data["uid"] = $params["uid"];
        $data["to_uid"] = $params["to_uid"];
        $data["mer_id"] = $params["mer_id"] > 0 ? $params["mer_id"] : 0;
        $data["add_time"] = time();
        StoreServiceLog::set($data);
        return JsonService::successful();
    }

    public function get_msn(Request $request)
    {
        $params = $request->post();
        $size = 10;
        $page = $params["page"] >= 0 ? $params["page"] : 1;
        $where = "(mer_id = " . $params["mer_id"] . " AND uid = " . $params["uid"] . " AND to_uid = " . $params["to_uid"] . ") OR (mer_id = " . $params["mer_id"] . " AND uid = " . $params["to_uid"] . " AND to_uid = " . $params["uid"] . ")";
        $list = StoreServiceLog::where($where)->limit(($page - 1) * $size, $size)->order("add_time desc")->select()->toArray();
        foreach ($list as $key => $value) {
            //设置发送人与接收人区别
            if ($value["uid"] == $params["uid"])
                $list[$key]['my'] = "my";
            else
                $list[$key]['my'] = "to";

            //设置这些消息为已读
            if ($value["uid"] == $params["to_uid"] && $value["to_uid"] == $params["uid"]) StoreServiceLog::edit(array("type" => 1, "remind" => 1), $value["id"]);
        }
        $list = array_reverse($list);
        return JsonService::successful($list);
    }

    public function refresh_msn_new(Request $request)
    {
        $params = $request->post();
        $now_user = $this->userInfo;
        if ($params["last_time"] > 0)
            $where = "(uid = " . $now_user["uid"] . " OR to_uid = " . $now_user["uid"] . ") AND add_time>" . $params["last_time"];
        else
            $where = "uid = " . $now_user["uid"] . " OR to_uid = " . $now_user["uid"];

        $msn_list = StoreServiceLog::where($where)->order("add_time desc")->select()->toArray();
        $info_array = $list = [];
        foreach ($msn_list as $key => $value) {
            $to_uid = $value["uid"] == $now_user["uid"] ? $value["to_uid"] : $value["uid"];
            if (!in_array(["to_uid" => $to_uid, "mer_id" => $value["mer_id"]], $info_array)) {
                $info_array[count($info_array)] = ["to_uid" => $to_uid, "mer_id" => $value["mer_id"]];

                $to_user = StoreService::field("uid,nickname,avatar")->where(array("uid" => $to_uid))->find();
                if (!$to_user) $to_user = User::field("uid,nickname,avatar")->where(array("uid" => $to_uid))->find();
                $to_user["mer_id"] = $value["mer_id"];
                $to_user["mer_name"] = '';
                $value["to_info"] = $to_user;
                $value["count"] = StoreServiceLog::where(array("mer_id" => $value["mer_id"], "uid" => $to_uid, "to_uid" => $now_user["uid"], "type" => 0))->count();
                $list[count($list)] = $value;
            }
        }
        return JsonService::successful($list);
    }

    public function get_user_brokerage_list($uid, $first = 0, $limit = 8)
    {
        if (!$uid) return JsonService::fail('用户不存在');
        $list = UserBill::field('A.mark,A.add_time,A.number,A.pm')->alias('A')->limit($first, $limit)
            ->where('A.category', 'now_money')->where('A.type', 'brokerage')
            ->where('A.uid', $this->uid)
            ->join('__STORE_ORDER__ B', 'A.link_id = B.id AND B.uid = ' . $uid)->select()->toArray();
        return JsonService::successful($list);
    }

    public function user_extract()
    {
        if (UserExtract::userExtract($this->userInfo, UtilService::postMore([
            ['type', '', '', 'extract_type'], 'real_name', 'alipay_code', 'bank_code', 'bank_address', ['price', '', '', 'extract_price']
        ])))
            return JsonService::successful('申请提现成功!');
        else
            return JsonService::fail(UserExtract::getErrorInfo());
    }

    public function clear_cache($uni = '')
    {
        if ($uni) CacheService::clear();
    }

    /**
     * 获取今天正在拼团的人的头像和名称
     * @return \think\response\Json
     */
    public function get_pink_second_one()
    {
        $addTime = mt_rand(time() - 30000, time());
        $storePink = StorePink::where('p.add_time', 'GT', $addTime)->alias('p')->where('p.status', 1)->join('User u', 'u.uid=p.uid')->field('u.nickname,u.avatar as src')->find();
        return JsonService::successful($storePink);
    }

}
