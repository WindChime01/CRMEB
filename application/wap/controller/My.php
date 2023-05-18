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

use Api\Express;
use app\wap\model\activity\EventSignUp;
use app\wap\model\activity\EventWriteOffUser;
use app\wap\model\special\SpecialRecord;
use app\wap\model\special\SpecialRelation;
use app\wap\model\user\SmsCode;
use app\wap\model\store\StoreOrderCartInfo;
use app\wap\model\recommend\Recommend;
use app\wap\model\store\StoreOrder;
use app\wap\model\user\PhoneUser;
use app\wap\model\user\User;
use app\wap\model\user\UserBill;
use app\wap\model\user\UserExtract;
use app\wap\model\user\UserAddress;
use app\wap\model\user\UserSign;
use service\GroupDataService;
use service\JsonService;
use service\SystemConfigService;
use service\UtilService;
use think\Cookie;
use think\Request;
use think\Session;
use think\Url;


/**my 控制器
 * Class My
 * @package app\wap\controller
 */
class My extends AuthController
{

    /*
     * 白名单
     * */
    public static function WhiteList()
    {
        return [
            'index',
            'about_us',
            'getPersonalCenterMenu',
            'questionModule'
        ];
    }

    /**
     * 退出手机号码登录
     */
    public function logout()
    {
        Cookie::delete('is_bg');
        Cookie::delete('is_login');
        Cookie::delete('__login_phone');
        Session::clear('wap');
        return JsonService::successful('已退出登录');
    }

    /**
     * 获取获取个人中心菜单
     */
    public function getPersonalCenterMenu()
    {
        $store_brokerage_statu = SystemConfigService::get('store_brokerage_statu');
        if ($store_brokerage_statu == 1) {
            if (isset($this->userInfo['is_promoter'])) $is_statu = $this->userInfo['is_promoter'] > 0 ? 1 : 0;
            else $is_statu = 0;
        } else if ($store_brokerage_statu == 2) {
            $is_statu = 1;
        }
        $is_write_off = isset($this->userInfo['is_write_off']) ? $this->userInfo['is_write_off'] : 0;
        $business = isset($this->userInfo['business']) ? $this->userInfo['business'] : 0;
        return JsonService::successful(Recommend::getPersonalCenterMenuList($is_statu, $is_write_off, $business, $this->uid));
    }

    /**
     * 题库模块
     */
    public function questionModule()
    {
        $question = GroupDataService::getData('question_bank_module', 2);
        return JsonService::successful($question);
    }

    /**我的赠送
     * @return mixed
     */
    public function my_gift()
    {
        return $this->fetch();
    }

    /**我的报名
     * @return mixed
     */
    public function sign_list()
    {
        return $this->fetch();
    }

    /**获取核销订单信息
     * @param int $type
     * @param string $order_id
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sign_order($type = 2, $order_id = '')
    {
        if ($type == 2 && !$this->userInfo['is_write_off'])  $this->failed('您没有权限!','verify_activity');
        $order = EventSignUp::setWhere()->where('order_id', $order_id)->find();
        if (!$order) $this->failed('订单不存在','verify_activity');
        if(!EventWriteOffUser::be(['event_id' => $order['activity_id'], 'uid' => $this->uid, 'is_del' => 0])) $this->failed('您没有该活动的核销权限','verify_activity');
        $this->assign(['type' => $type, 'order_id' => $order_id]);
        return $this->fetch();
    }

    /**报名详情
     * @param string $order_id
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sign_my_order($order_id = '')
    {
        $this->assign(['order_id' => $order_id]);
        return $this->fetch('order_verify');
    }

    /**我的信息
     * @return mixed
     */
    public function user_info()
    {
        return $this->fetch();
    }

    public function verify_activity()
    {
        return $this->fetch();
    }

    /**
     * 手机号验证
     */
    public function validate_code()
    {
        list($phone, $code,) = UtilService::getMore([
            ['phone', ''],
            ['code', ''],
        ], $this->request, true);
        if (!$phone) return JsonService::fail('请输入手机号码');
        if (!$code) return JsonService::fail('请输入验证码');
        $code = md5('is_phone_code' . $code);
        if (!SmsCode::CheckCode($phone, $code)) return JsonService::fail('验证码验证失败');
        SmsCode::setCodeInvalid($phone, $code);
        return JsonService::successful('验证成功');
    }

    /**
     * 信息保存
     */
    public function save_user_info()
    {
        $data = UtilService::postMore([
            ['avatar', ''],
            ['nickname', ''],
            ['grade_id', 0]
        ], $this->request);
        if ($data['nickname'] != strip_tags($data['nickname'])) {
            $data['nickname'] = htmlspecialchars($data['nickname']);
        }
        if (!$data['nickname']) return JsonService::fail('用户昵称不能为空');
        if (User::update($data, ['uid' => $this->uid]))
            return JsonService::successful('保存成功');
        else
            return JsonService::fail('保存失败');
    }

    /**
     * 保存手机号码
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function save_phone()
    {
        if ($this->request->isAjax()) {
            list($phone, $code, $type) = UtilService::getMore([
                ['phone', ''],
                ['code', ''],
                ['type', 0],
            ], $this->request, true);
            if (!$phone) return JsonService::fail('请输入手机号码');
            if (!$code) return JsonService::fail('请输入验证码');
            $code = md5('is_phone_code' . $code);
            if (!SmsCode::CheckCode($phone, $code)) return JsonService::fail('验证码验证失败');
            SmsCode::setCodeInvalid($phone, $code);
            $user = User::where(['phone' => $phone, 'is_h5user' => 0])->find();
            if ($type && $user) {
                if ($user['uid'] == $this->uid) {
                    return JsonService::fail('不能绑定相同手机号');
                } else if ($user['uid'] != $this->uid) {
                    return JsonService::fail('当前手机号码已绑定微信用户');
                }
            } else if ($type == 0 && $user) {
                if ($user) return JsonService::fail('当前手机号码已绑定微信用户');
            }
            //查找H5手机号码账户
            $phoneUser = PhoneUser::where(['phone' => $phone])->find();
            //H5页面有注册过
            if ($phoneUser && $phoneUser['uid'] != $this->uid) {
                //检测当前用户是否是H5用户
                if (User::where('uid', $phoneUser['uid'])->value('is_h5user')) {
                    $res = User::setUserRelationInfos($phone, $phoneUser['uid'], $this->uid);
                    if ($res === false) return JsonService::fail(User::getErrorInfo());
                }
            } else if ($phoneUser && $phoneUser['uid'] == $this->uid) {
                return JsonService::fail('不能绑定相同手机号');
            }
            if (!isset($res)) User::update(['phone' => $phone], ['uid' => $this->uid]);
            return JsonService::successful('绑定成功');
        } else {
            $this->assign('user_phone', $this->userInfo['phone']);
            return $this->fetch();
        }
    }

    /**
     * 个人中心
     * @return mixed
     * @throws \think\Exception
     */
    public function index()
    {
        $store_brokerage_statu = SystemConfigService::get('store_brokerage_statu');
        if ($store_brokerage_statu == 1) {
            if (isset($this->userInfo['is_promoter'])) $is_statu = $this->userInfo['is_promoter'] > 0 ? 1 : 0;
            else $is_statu = 0;
        } else if ($store_brokerage_statu == 2) {
            $is_statu = 1;
        }
        if (isset($this->userInfo['overdue_time'])) $overdue_time = date('Y-m-d', $this->userInfo['overdue_time']);
        else $overdue_time = 0;
        $this->assign([
            'store_switch' => SystemConfigService::get('store_switch'),
            'collectionNumber' => SpecialRelation::where('uid', $this->uid)->count(),
            'recordNumber' => SpecialRecord::where('uid', $this->uid)->count(),
            'overdue_time' => $overdue_time,
            'is_statu' => $is_statu,
        ]);
        return $this->fetch();
    }

    /**虚拟币明细
     * @return mixed
     */
    public function gold_coin()
    {
        $gold_name = SystemConfigService::get('gold_name');//虚拟币名称
        $this->assign(compact('gold_name'));
        return $this->fetch('coin_detail');
    }

    /**签到
     * @return mixed
     */
    public function sign_in()
    {
        $urls = SystemConfigService::get('site_url') . '/';
        $gold_name = SystemConfigService::get('gold_name');//虚拟币名称
        $gold_coin = SystemConfigService::get('single_gold_coin');//签到获得虚拟币
        $signed = UserSign::checkUserSigned($this->uid);//今天是否签到
        $sign_image = $urls . "uploads/" . "poster_sign_" . $this->uid . ".png";
        $signCount = UserSign::userSignedCount($this->uid);//累记签到天数
        $this->assign(compact('signed', 'signCount', 'gold_name', 'gold_coin', 'sign_image'));
        return $this->fetch();
    }


    /**签到明细
     * @return mixed
     */
    public function sign_in_list()
    {
        return $this->fetch();
    }

    /**地址列表
     * @return mixed
     */
    public function address()
    {
        $address = UserAddress::getUserValidAddressList($this->uid, 'id,real_name,phone,province,city,district,detail,is_default');
        $this->assign([
            'address' => json_encode($address)
        ]);
        return $this->fetch();
    }

    /**修改或添加地址
     * @param string $addressId
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit_address($addressId = '', $cartId = 0)
    {
        if ($addressId && is_numeric($addressId) && UserAddress::be(['is_del' => 0, 'id' => $addressId, 'uid' => $this->uid])) {
            $addressInfo = UserAddress::find($addressId)->toArray();
        } else {
            $addressInfo = [];
        }
        $addressInfo = json_encode($addressInfo);
        $this->assign(compact('addressInfo', 'cartId'));
        return $this->fetch();
    }

    /**订单详情
     * @param string $uni
     * @return mixed|void
     */
    public function order($uni = '')
    {
        if (!$uni || !$order = StoreOrder::getUserOrderDetail($this->uid, $uni)) $this->failed('查询订单不存在!');
        $this->assign([
            'gold_name' => SystemConfigService::get('gold_name'),
            'order' => StoreOrder::tidyOrder($order, true)
        ]);
        return $this->fetch();
    }

    public function orderPinkOld($uni = '')
    {
        if (!$uni || !$order = StoreOrder::getUserOrderDetail($this->uid, $uni)) $this->failed('查询订单不存在!');
        $this->assign([
            'order' => StoreOrder::tidyOrder($order, true)
        ]);
        return $this->fetch('order');
    }

    /**获取订单
     * @param int $type
     * @param int $page
     * @param int $limit
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_order_list($type = -1, $page = 1, $limit = 10)
    {
        return JsonService::successful(StoreOrder::getSpecialOrderList((int)$type, (int)$page, (int)$limit, $this->uid));
    }

    /**我的拼课订单
     * @return mixed
     */
    public function order_list()
    {
        return $this->fetch();
    }

    /**申请退款
     * @param string $order_id
     * @return mixed
     */
    public function refund_apply($order_id = '')
    {
        if (!$order_id || !$order = StoreOrder::getUserOrderDetail($this->uid, $order_id)) $this->failed('查询订单不存在!');
        $this->assign([
            'order' => StoreOrder::tidyOrder($order, true, true),
            'order_id' => $order_id
        ]);
        return $this->fetch();
    }

    /**评论页面
     * @param string $unique
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function order_reply($unique = '')
    {
        if (!$unique || !StoreOrderCartInfo::be(['unique' => $unique]) || !($cartInfo = StoreOrderCartInfo::where('unique', $unique)->find())) $this->failed('评价产品不存在!');
        $this->assign(['cartInfo' => $cartInfo]);
        return $this->fetch();
    }

    /**物流查询
     * @param string $uni
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function express($uni = '')
    {
        if (!$uni || !($order = StoreOrder::getUserOrderDetail($this->uid, $uni))) $this->failed('查询订单不存在!');
        if ($order['delivery_type'] != 'express' || !$order['delivery_id']) $this->failed('该订单不存在快递单号!');
        $this->assign(['order' => StoreOrder::tidyOrder($order, true, true)]);
        return $this->fetch();
    }


    public function commission()
    {
        $uid = (int)Request::instance()->get('uid', 0);
        if (!$uid) $this->failed('用户不存在!');
        $this->assign(['uid' => $uid]);
        return $this->fetch();
    }

    /**
     * 关于我们
     * @return mixed
     */
    public function about_us()
    {
        $this->assign([
            'content' => get_config_content('about_us'),
            'title' => '关于我们'
        ]);
        return $this->fetch('index/agree');
    }

    public function getUserGoldBill()
    {
        $user_info = $this->userInfo;
        list($page, $limit) = UtilService::getMore([
            ['page', 1],
            ['limit', 20],
        ], $this->request, true);
        $where['uid'] = $user_info['uid'];
        $where['category'] = "gold_num";
        return JsonService::successful(UserBill::getUserGoldBill($where, $page, $limit));
    }


    /**
     * 余额明细
     */
    public function bill_detail()
    {
        return $this->fetch();
    }

    /**
     * 我的关注
     */
    public function follow_lecturer()
    {
        return $this->fetch();
    }
}
