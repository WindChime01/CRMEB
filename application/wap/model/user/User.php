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


namespace app\wap\model\user;


use app\admin\model\wechat\WechatQrcode;
use basic\ModelBasic;
use service\SystemConfigService;
use think\Cookie;
use think\Request;
use think\response\Redirect;
use think\Session;
use think\Url;
use traits\ModelTrait;
use app\wap\model\user\WechatUser;
use service\WechatTemplateService;
use app\wap\model\routine\RoutineTemplate;
use app\wap\model\special\Special;
use app\wap\model\store\StoreProduct;
use app\wap\model\user\MemberShip;

/**用户表
 * Class User
 * @package app\wap\model\user
 */
class User extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time', 'add_ip', 'last_time', 'last_ip'];

    protected function setAddTimeAttr($value)
    {
        return time();
    }

    protected function setAddIpAttr($value)
    {
        return Request::instance()->ip();
    }

    protected function setLastTimeAttr($value)
    {
        return time();
    }

    protected function setLastIpAttr($value)
    {
        return Request::instance()->ip();
    }

    public static function ResetSpread($openid)
    {
        $uid = WechatUser::openidToUid($openid);
        if (self::be(['uid' => $uid, 'is_promoter' => 0])) self::where('uid', $uid)->update(['spread_uid' => 0]);
    }

    /**
     * 绑定用户手机号码修改手机号码用户购买的专题和其他数据
     * @param $bindingPhone 绑定手机号码
     * @param $uid 当前用户id
     * @param $newUid 切换用户id
     * @param bool $isDel 是否删除
     * @param int $qcodeId 扫码id
     * @return bool
     * @throws \think\exception\PDOException
     */
    public static function setUserRelationInfos($bindingPhone, $uid, $newUid, $isDel = true, $qcodeId = 0)
    {
        self::startTrans();
        try {
            //修改下级推广人关系
            self::where('spread_uid', $uid)->update(['spread_uid' => $newUid]);
            //修改用户金额变动记录表
            self::getDb('user_bill')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改签到记录表
            self::getDb('user_sign')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改虚拟币充值记录表
            self::getDb('user_recharge')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改收货地址表
            self::getDb('user_address')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改提现记录用户
            self::getDb('user_extract')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改专题购买记录表
            self::getDb('special_buy')->where('uid', $uid)->update(['uid' => $newUid]);
            self::getDb('special_watch')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改直播嘉宾表
            self::getDb('live_user')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改购物车记录表
            self::getDb('store_cart')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改用户订单记录
            self::getDb('store_order')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改拼团用户记录
            self::getDb('store_pink')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改手机用户表记录
            self::getDb('phone_user')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改会员记录表记录
            self::getDb('member_record')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改搜索记录表记录
            self::getDb('search_history')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改用户报名表记录
            self::getDb('event_sign_up')->where('uid', $uid)->update(['uid' => $newUid]);
            self::getDb('event_write_off_user')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改资料订单表记录
            self::getDb('data_download_buy')->where('uid', $uid)->update(['uid' => $newUid]);
            self::getDb('data_download_order')->where('uid', $uid)->update(['uid' => $newUid]);
            self::getDb('data_download_records')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改证书记录表记录
            self::getDb('certificate_record')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改考试相关表记录
            self::getDb('examination_record')->where('uid', $uid)->update(['uid' => $newUid]);
            self::getDb('examination_test_record')->where('uid', $uid)->update(['uid' => $newUid]);
            self::getDb('examination_wrong_bank')->where('uid', $uid)->update(['uid' => $newUid]);
            self::getDb('test_paper_obtain')->where('uid', $uid)->update(['uid' => $newUid]);
            self::getDb('test_paper_order')->where('uid', $uid)->update(['uid' => $newUid]);

            //修改讲师相关表
            self::getDb('merchant')->where('uid', $uid)->update(['uid' => $newUid]);
            self::getDb('merchant_follow')->where('uid', $uid)->update(['uid' => $newUid]);
            self::getDb('user_enter')->where('uid', $uid)->update(['uid' => $newUid]);

            //删除用户表H5用户记录
            $user = self::where('uid', $uid)->find();
            if ($isDel) self::where('uid', $uid)->delete();
            //修改上级推广关系和绑定手机号码
            self::where('uid', $newUid)->update(['phone' => $bindingPhone,
                'spread_uid' => $user['spread_uid'],
                'valid_time' => $user['valid_time'],
                'now_money' => $user['now_money'],
                'gold_num' => $user['gold_num'],
                'brokerage_price' => $user['brokerage_price'],
                'is_permanent' => $user['is_permanent'],
                'member_time' => $user['member_time'],
                'overdue_time' => $user['overdue_time'],
                'level' => $user['level']
            ]);
            if ($qcodeId) WechatQrcode::where('id', $qcodeId)->update(['scan_id' => $newUid]);
            self::commit();
            Session::clear('wap');
            Session::set('loginUid', $newUid, 'wap');
            return true;
        } catch (\Exception $e) {
            self::rollback();
            return self::setErrorInfo($e->getMessage());
        }
    }

    /**
     * 保存微信用户信息
     * @param $wechatUser 用户信息
     * @param int $spread_uid 上级用户uid
     * @return mixed
     */
    public static function setWechatUser($wechatUser, $spread_uid = 0)
    {
        if (isset($wechatUser['uid']) && $wechatUser['uid'] == $spread_uid) $spread_uid = 0;
        $data = [
            'account' => 'wx' . date('YmdHis'),
            'pwd' => md5(123456),
            'nickname' => $wechatUser['nickname'] ?: '',
            'avatar' => $wechatUser['headimgurl'] ?: '',
            'user_type' => 'wechat'
        ];
        //处理推广关系
        if ($spread_uid) {
            $spreadUserInfo = self::getUserData($spread_uid);
            if($spreadUserInfo) {
                $data = self::manageSpread($spread_uid, $data, $spreadUserInfo['is_promoter']);
            }
        }
        $res = self::set($data);
        if ($res) $wechatUser['uid'] = (int)$res['uid'];
        return $wechatUser;
    }

    /**
     * 设置上下级推广人关系
     * 普通推广人星级关系由字段 is_promoter 区分， is_promoter = 1 为 0 星， is_promoter = 2 为 1 星，依次类推
     * @param $spread_uid 上级推广人
     * @param array $data 更新字段
     * @param bool $isForever
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function manageSpread($spread_uid, $data = [], $is_promoter)
    {
        $storeBrokerageStatu = SystemConfigService::get('store_brokerage_statu') ?: 1;//获取后台分销类型
        if ($storeBrokerageStatu == 1) {
            if ($is_promoter) {
                $data['spread_uid'] = $spread_uid;
            } else {
                $data['spread_uid'] = 0;
            }
        } else {
            $data['spread_uid'] = $spread_uid;
        }
        $data['spread_time'] = time();
        return $data;
    }

    /**
     * 更新用户数据并绑定上下级关系
     * @param $wechatUser
     * @param $uid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function updateWechatUser($wechatUser, $uid)
    {
        $name = '__login_phone_num' . $uid;
        $userinfo = self::where('uid', $uid)->find();
        //检查是否有此字段
        $spread_uid = isset($wechatUser['spread_uid']) ? $wechatUser['spread_uid'] : 0;
        //自己不能成为自己的下线
        $spread_uid = $spread_uid == $userinfo->uid ? 0 : $spread_uid;
        //手机号码存在直接登陆
        if ($userinfo['phone']) {
            Cookie::set('__login_phone', 1);
            Session::set($name, $userinfo['phone'], 'wap');
            Session::set('__login_phone_number', $userinfo['phone'], 'wap');
        }
        //有推广人直接更新
        $editData = [];
        //不是推广人，并且有上级id绑定关系
        if (!$userinfo->is_promoter && $spread_uid && !$userinfo->spread_uid && $spread_uid != $uid) {
            $spreadUserInfo = self::getUserData($spread_uid);
            if($spreadUserInfo){
                $editData = self::manageSpread($spread_uid, $editData, $spreadUserInfo['is_promoter']);
            }
        }
        if ($userinfo['nickname'] == '' || $userinfo['avatar'] == '') {
            $editData['nickname'] = $wechatUser['nickname'];
            $editData['avatar'] = $wechatUser['headimgurl'];
        }
        return self::edit($editData, $uid, 'uid');
    }

    public static function setSpreadUid($uid, $spreadUid)
    {
        return self::where('uid', $uid)->update(['spread_uid' => $spreadUid]);
    }


    public static function getUserInfo($uid)
    {
        $userInfo = self::where('uid', $uid)->find();
        if (!Session::has('__login_phone_num' . $uid) && $userInfo['phone']) {
            Cookie::set('__login_phone', 1);
            Session::set('__login_phone_num' . $uid, $userInfo['phone'], 'wap');
        }
        unset($userInfo['pwd']);
        if (!$userInfo) {
            Session::delete(['loginUid', 'loginOpenid']);
            throw new \Exception('未查询到此用户');
        }
        return $userInfo->toArray();
    }

    public static function getUserData($uid)
    {
        $userInfo = self::where('uid', $uid)->find();
        if (!$userInfo) return false;
        return $userInfo->toArray();
    }

    /**
     * 获得当前登陆用户UID
     * @return int $uid
     */
    public static function getActiveUid()
    {
        $uid = null;
        if (!Cookie::get('is_login')) exit(exception('请登陆!'));
        if (Session::has('loginUid', 'wap')) $uid = Session::get('loginUid', 'wap');
        if (!$uid && Session::has('loginOpenid', 'wap') && ($openid = Session::get('loginOpenid', 'wap')))
            $uid = WechatUser::openidToUid($openid);
        if (!$uid) exit(exception('请登陆!'));
        return $uid;
    }

    /**
     * 获取登陆的手机号码
     * @param int $uid 用户id
     * @param string $phone 用户号码
     * @return string
     * */
    public static function getLogPhone($uid, $phone = null)
    {
        $name = '__login_phone_num' . $uid;
        if (!Cookie::get('__login_phone') && $uid) {
            Cookie::set('__login_phone', 1);
        } else if (!Cookie::get('__login_phone') && !$uid) {
            return null;
        }
        if (Session::has($name, 'wap')) $phone = Session::get($name, 'wap');
        if (is_null($phone)) {
            if (Session::has('__login_phone_number', 'wap')) $phone = Session::get('__login_phone_number', 'wap');
        }
        return $phone;
    }

    /**
     * 一级推广 专题
     * @param $orderInfo
     * @return bool
     */
    public static function backOrderBrokerage($orderInfo)
    {
        $userInfo = self::getUserData($orderInfo['uid']);
        if (!$userInfo || !$userInfo['spread_uid']) return true;
        $storeBrokerageStatu = SystemConfigService::get('store_brokerage_statu') ?: 1;//获取后台分销类型
        if ($storeBrokerageStatu == 1) {
            if (!self::be(['uid' => $userInfo['spread_uid'], 'is_promoter' => 1])) return true;
        }
        $data = Special::getIndividualDistributionSettings($orderInfo['cart_id']);
        if (isset($data['is_alone']) && $data['is_alone']) {
            if (!isset($data['brokerage_ratio']) || !$data['brokerage_ratio']) return true;
            $brokerageRatio = bcdiv($data['brokerage_ratio'], 100, 2);
        } else {
            $course_distribution_switch = SystemConfigService::get('course_distribution_switch');//课程分销开关
            if ($course_distribution_switch == 0) return true;
            $brokerageRatio = bcdiv(SystemConfigService::get('store_brokerage_ratio'), 100, 2);
        }
        if ($brokerageRatio <= 0) return true;
        $brokeragePrice = bcmul($orderInfo['pay_price'], $brokerageRatio, 2);
        if ($brokeragePrice <= 0) return true;
        $mark = '一级推广人' . $userInfo['nickname'] . '消费' . floatval($orderInfo['pay_price']) . '元购买专题,奖励推广佣金' . floatval($brokeragePrice);
        self::beginTrans();
        $res1 = UserBill::income('购买专题返佣', $userInfo['spread_uid'], 'now_money', 'brokerage', $brokeragePrice, $orderInfo['id'], 0, $mark);
        $res2 = self::bcInc($userInfo['spread_uid'], 'brokerage_price', $brokeragePrice, 'uid');
        $User = self::getUserData($userInfo['spread_uid']);
        if(!$User) {
            self::checkTrans(false);
            return true;
        }
        if ($openid = WechatUser::where('uid', $userInfo['spread_uid'])->value('openid')) {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate($openid, WechatTemplateService::USER_BALANCE_CHANGE, [
                    'first' => '叮！您收到一笔专题返佣，返佣金额'.$brokeragePrice.'元',
                    'keyword1' => '返佣金额',
                    'keyword2' => $User['brokerage_price'],
                    'remark' => '点击查看详情'
                ], Url::build('wap/spread/commission', [], true, true));
            } else {
                $dat['thing8']['value'] = '返佣金额';
                $dat['date4']['value'] = date('Y-m-d H:i:s', time());
                $dat['amount1']['value'] = $brokeragePrice;
                $dat['amount2']['value'] = $User['brokerage_price'];
                $dat['thing5']['value'] = '您收到一笔专题返佣!';
                RoutineTemplate::sendAccountChanges($dat, $userInfo['spread_uid'], Url::build('wap/spread/commission', [], true, true));
            }
        }
        $res = $res1 && $res2;
        self::checkTrans($res);
        if ($res) self::backOrderBrokerageTwo($orderInfo);
        return $res;
    }

    /**
     * 二级推广 专题
     * @param $orderInfo
     * @return bool
     */
    public static function backOrderBrokerageTwo($orderInfo)
    {
        $userInfo = self::getUserData($orderInfo['uid']);
        $userInfoTwo = self::getUserData($userInfo['spread_uid']);
        if (!$userInfoTwo || !$userInfoTwo['spread_uid']) return true;
        $storeBrokerageStatu = SystemConfigService::get('store_brokerage_statu') ?: 1;//获取后台分销类型
        if ($storeBrokerageStatu == 1) {
            if (!self::be(['uid' => $userInfoTwo['spread_uid'], 'is_promoter' => 1])) return true;
        }
        $data = Special::getIndividualDistributionSettings($orderInfo['cart_id']);
        if (isset($data['is_alone']) && $data['is_alone']) {
            if (!isset($data['brokerage_two']) || !$data['brokerage_two']) return true;
            $brokerageRatio = bcdiv($data['brokerage_two'], 100, 2);
        } else {
            $course_distribution_switch = SystemConfigService::get('course_distribution_switch');//课程分销开关
            if ($course_distribution_switch == 0) return true;
            $brokerageRatio = bcdiv(SystemConfigService::get('store_brokerage_two'), 100, 2);
        }
        if ($brokerageRatio <= 0) return true;
        $brokeragePrice = bcmul($orderInfo['pay_price'], $brokerageRatio, 2);
        if ($brokeragePrice <= 0) return true;
        $mark = '二级推广人' . $userInfo['nickname'] . '消费' . floatval($orderInfo['pay_price']) . '元购买专题,奖励推广佣金' . floatval($brokeragePrice);
        self::beginTrans();
        $res1 = UserBill::income('购买专题返佣', $userInfoTwo['spread_uid'], 'now_money', 'brokerage', $brokeragePrice, $orderInfo['id'], 0, $mark);
        $res2 = self::bcInc($userInfoTwo['spread_uid'], 'brokerage_price', $brokeragePrice, 'uid');
        $User = self::getUserData($userInfoTwo['spread_uid']);
        if(!$User) {
            self::checkTrans(false);
            return true;
        }
        if ($openid = WechatUser::where('uid', $userInfoTwo['spread_uid'])->value('openid')) {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate($openid, WechatTemplateService::USER_BALANCE_CHANGE, [
                    'first' => '叮！您收到一笔专题返佣，返佣金额'.$brokeragePrice.'元',
                    'keyword1' => '返佣金额',
                    'keyword2' => $User['brokerage_price'],
                    'remark' => '点击查看详情'
                ], Url::build('wap/spread/commission', [], true, true));
            } else {
                $dat['thing8']['value'] = '返佣金额';
                $dat['date4']['value'] = date('Y-m-d H:i:s', time());
                $dat['amount1']['value'] = $brokeragePrice;
                $dat['amount2']['value'] = $User['brokerage_price'];
                $dat['thing5']['value'] = '您收到一笔专题返佣!';
                RoutineTemplate::sendAccountChanges($dat, $userInfoTwo['spread_uid'], Url::build('wap/spread/commission', [], true, true));
            }
        }
        $res = $res1 && $res2;
        self::checkTrans($res);
        return $res;
    }

    /**
     * 一级推广 商品
     * @param $orderInfo
     * @return bool
     */
    public static function backGoodsOrderBrokerage($orderInfo)
    {
        $userInfo = self::getUserData($orderInfo['uid']);
        if (!$userInfo || !$userInfo['spread_uid']) return true;
        $storeBrokerageStatu = SystemConfigService::get('store_brokerage_statu') ?: 1;//获取后台分销类型
        if ($storeBrokerageStatu == 1) {
            if (!self::be(['uid' => $userInfo['spread_uid'], 'is_promoter' => 1])) return true;
        }
        $data = StoreProduct::getIndividualDistributionSettings($orderInfo['id']);
        if (isset($data['is_alone']) && $data['is_alone']) {
            if (!isset($data['brokerage_ratio']) || !$data['brokerage_ratio']) return true;
            $brokerageRatio = bcdiv($data['brokerage_ratio'], 100, 2);
        } else {
            $course_distribution_switch = SystemConfigService::get('goods_distribution_switch');//商品分销开关
            if ($course_distribution_switch == 0) return true;
            $brokerageRatio = bcdiv(SystemConfigService::get('goods_brokerage_ratio'), 100, 2);
        }
        if ($brokerageRatio <= 0) return true;
        $brokeragePrice = bcmul($orderInfo['pay_price'], $brokerageRatio, 2);
        if ($brokeragePrice <= 0) return true;
        $mark = '一级推广人' . $userInfo['nickname'] . '消费' . floatval($orderInfo['pay_price']) . '元购买商品,奖励推广佣金' . floatval($brokeragePrice);
        self::beginTrans();
        $res1 = UserBill::income('购买商品返佣', $userInfo['spread_uid'], 'now_money', 'brokerage', $brokeragePrice, $orderInfo['id'], 0, $mark);
        $res2 = self::bcInc($userInfo['spread_uid'], 'brokerage_price', $brokeragePrice, 'uid');
        $User = self::getUserData($userInfo['spread_uid']);
        if(!$User) {
            self::checkTrans(false);
            return true;
        }
        if ($openid = WechatUser::where('uid', $userInfo['spread_uid'])->value('openid')) {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate($openid, WechatTemplateService::USER_BALANCE_CHANGE, [
                    'first' => '叮！您收到一笔商品返佣，返佣金额'.$brokeragePrice.'元',
                    'keyword1' => '返佣金额',
                    'keyword2' => $User['brokerage_price'],
                    'remark' => '点击查看详情'
                ], Url::build('wap/spread/commission', [], true, true));
            } else {
                $dat['thing8']['value'] = '返佣金额';
                $dat['date4']['value'] = date('Y-m-d H:i:s', time());
                $dat['amount1']['value'] = $brokeragePrice;
                $dat['amount2']['value'] = $User['brokerage_price'];
                $dat['thing5']['value'] = '您收到一笔商品返佣!';
                RoutineTemplate::sendAccountChanges($dat, $userInfo['spread_uid'], Url::build('wap/spread/commission', [], true, true));
            }
        }
        $res = $res1 && $res2;
        self::checkTrans($res);
        if ($res) self::backGoodsOrderBrokerageTwo($orderInfo);
        return $res;
    }

    /**
     * 二级推广 商品
     * @param $orderInfo
     * @return bool
     */
    public static function backGoodsOrderBrokerageTwo($orderInfo)
    {
        $userInfo = self::getUserData($orderInfo['uid']);
        $userInfoTwo = self::getUserData($userInfo['spread_uid']);
        if (!$userInfoTwo || !$userInfoTwo['spread_uid']) return true;
        $storeBrokerageStatu = SystemConfigService::get('store_brokerage_statu') ?: 1;//获取后台分销类型
        if ($storeBrokerageStatu == 1) {
            if (!self::be(['uid' => $userInfoTwo['spread_uid'], 'is_promoter' => 1])) return true;
        }
        $data = StoreProduct::getIndividualDistributionSettings($orderInfo['id']);
        if (isset($data['is_alone']) && $data['is_alone']) {
            if (!isset($data['brokerage_two']) || !$data['brokerage_two']) return true;
            $brokerageRatio = bcdiv($data['brokerage_two'], 100, 2);
        } else {
            $course_distribution_switch = SystemConfigService::get('goods_distribution_switch');//商品分销开关
            if ($course_distribution_switch == 0) return true;
            $brokerageRatio = bcdiv(SystemConfigService::get('goods_brokerage_two'), 100, 2);
        }
        if ($brokerageRatio <= 0) return true;
        $brokeragePrice = bcmul($orderInfo['pay_price'], $brokerageRatio, 2);
        if ($brokeragePrice <= 0) return true;
        $mark = '二级推广人' . $userInfo['nickname'] . '消费' . floatval($orderInfo['pay_price']) . '元购买商品,奖励推广佣金' . floatval($brokeragePrice);
        self::beginTrans();
        $res1 = UserBill::income('购买商品返佣', $userInfoTwo['spread_uid'], 'now_money', 'brokerage', $brokeragePrice, $orderInfo['id'], 0, $mark);
        $res2 = self::bcInc($userInfoTwo['spread_uid'], 'brokerage_price', $brokeragePrice, 'uid');
        $User = self::getUserData($userInfoTwo['spread_uid']);
        if(!$User) {
            self::checkTrans(false);
            return true;
        }
        if ($openid = WechatUser::where('uid', $userInfoTwo['spread_uid'])->value('openid')) {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate($openid, WechatTemplateService::USER_BALANCE_CHANGE, [
                    'first' => '叮！您收到一笔专题商品，返佣金额'.$brokeragePrice.'元',
                    'keyword1' => '返佣金额',
                    'keyword2' => $User['brokerage_price'],
                    'remark' => '点击查看详情'
                ], Url::build('wap/spread/commission', [], true, true));
            } else {
                $dat['thing8']['value'] = '返佣金额';
                $dat['date4']['value'] = date('Y-m-d H:i:s', time());
                $dat['amount1']['value'] = $brokeragePrice;
                $dat['amount2']['value'] = $User['brokerage_price'];
                $dat['thing5']['value'] = '您收到一笔商品返佣!';
                RoutineTemplate::sendAccountChanges($dat, $userInfoTwo['spread_uid'], Url::build('wap/spread/commission', [], true, true));
            }
        }
        $res = $res1 && $res2;
        self::checkTrans($res);
        return $res;
    }

    /**
     * 一级推广 会员
     * @param $orderInfo
     * @return bool
     */
    public static function backOrderBrokerageMember($orderInfo)
    {
        $userInfo = self::getUserData($orderInfo['uid']);
        if (!$userInfo || !$userInfo['spread_uid']) return true;
        $storeBrokerageStatu = SystemConfigService::get('store_brokerage_statu') ?: 1;//获取后台分销类型
        if ($storeBrokerageStatu == 1) {
            if (!self::be(['uid' => $userInfo['spread_uid'], 'is_promoter' => 1])) return true;
        }
        $data = MemberShip::getIndividualDistributionSettings($orderInfo['member_id']);
        if (isset($data['is_alone']) && $data['is_alone']) {
            if (!isset($data['brokerage_ratio']) || !$data['brokerage_ratio']) return true;
            $brokerageRatio = bcdiv($data['brokerage_ratio'], 100, 2);
        } else {
            $member_distribution_switch = SystemConfigService::get('member_distribution_switch');//会员分销开关
            if ($member_distribution_switch == 0) return true;
            $brokerageRatio = bcdiv(SystemConfigService::get('member_brokerage_ratio'), 100, 2);
        }
        if ($brokerageRatio <= 0) return true;
        $brokeragePrice = bcmul($orderInfo['pay_price'], $brokerageRatio, 2);
        if ($brokeragePrice <= 0) return true;
        $mark = '一级推广人' . $userInfo['nickname'] . '消费' . floatval($orderInfo['pay_price']) . '元购买会员,奖励推广佣金' . floatval($brokeragePrice);
        self::beginTrans();
        $res1 = UserBill::income('购买会员返佣', $userInfo['spread_uid'], 'now_money', 'brokerage', $brokeragePrice, $orderInfo['id'], 0, $mark);
        $res2 = self::bcInc($userInfo['spread_uid'], 'brokerage_price', $brokeragePrice, 'uid');
        $User = self::getUserData($userInfo['spread_uid']);
        if(!$User) {
            self::checkTrans(false);
            return true;
        }
        if ($openid = WechatUser::where('uid', $userInfo['spread_uid'])->value('openid')) {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate($openid, WechatTemplateService::USER_BALANCE_CHANGE, [
                    'first' => '叮！您收到一笔会员返佣，返佣金额'.$brokeragePrice.'元',
                    'keyword1' => '返佣金额',
                    'keyword2' => $User['brokerage_price'],
                    'remark' => '点击查看详情'
                ], Url::build('wap/spread/commission', [], true, true));
            } else {
                $dat['thing8']['value'] = '返佣金额';
                $dat['date4']['value'] = date('Y-m-d H:i:s', time());
                $dat['amount1']['value'] = $brokeragePrice;
                $dat['amount2']['value'] = $User['brokerage_price'];
                $dat['thing5']['value'] = '您收到一笔会员返佣!';
                RoutineTemplate::sendAccountChanges($dat, $userInfo['spread_uid'], Url::build('wap/spread/commission', [], true, true));
            }
        }
        $res = $res1 && $res2;
        self::checkTrans($res);
        if ($res) self::backOrderBrokerageTwoMember($orderInfo);
        return $res;
    }

    /**
     * 二级推广 会员
     * @param $orderInfo
     * @return bool
     */
    public static function backOrderBrokerageTwoMember($orderInfo)
    {
        $userInfo = self::getUserData($orderInfo['uid']);
        $userInfoTwo = self::getUserData($userInfo['spread_uid']);
        if (!$userInfoTwo || !$userInfoTwo['spread_uid']) return true;
        $storeBrokerageStatu = SystemConfigService::get('store_brokerage_statu') ?: 1;//获取后台分销类型
        if ($storeBrokerageStatu == 1) {
            if (!self::be(['uid' => $userInfoTwo['spread_uid'], 'is_promoter' => 1])) return true;
        }
        $data = MemberShip::getIndividualDistributionSettings($orderInfo['member_id']);
        if (isset($data['is_alone']) && $data['is_alone']) {
            if (!isset($data['brokerage_two']) || !$data['brokerage_two']) return true;
            $brokerageRatio = bcdiv($data['brokerage_two'], 100, 2);
        } else {
            $member_distribution_switch = SystemConfigService::get('member_distribution_switch');//会员分销开关
            if ($member_distribution_switch == 0) return true;
            $brokerageRatio = bcdiv(SystemConfigService::get('member_brokerage_two'), 100, 2);
        }
        if ($brokerageRatio <= 0) return true;
        $brokeragePrice = bcmul($orderInfo['pay_price'], $brokerageRatio, 2);
        if ($brokeragePrice <= 0) return true;
        $mark = '二级推广人' . $userInfo['nickname'] . '消费' . floatval($orderInfo['pay_price']) . '元购买会员,奖励推广佣金' . floatval($brokeragePrice);
        self::beginTrans();
        $res1 = UserBill::income('购买会员返佣', $userInfoTwo['spread_uid'], 'now_money', 'brokerage', $brokeragePrice, $orderInfo['id'], 0, $mark);
        $res2 = self::bcInc($userInfoTwo['spread_uid'], 'brokerage_price', $brokeragePrice, 'uid');
        $User = self::getUserData($userInfoTwo['spread_uid']);
        if(!$User) {
            self::checkTrans(false);
            return true;
        }
        if ($openid = WechatUser::where('uid', $userInfoTwo['spread_uid'])->value('openid')) {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate($openid, WechatTemplateService::USER_BALANCE_CHANGE, [
                    'first' => '叮！您收到一笔会员返佣，返佣金额'.$brokeragePrice.'元',
                    'keyword1' => '返佣金额',
                    'keyword2' => $User['brokerage_price'],
                    'remark' => '点击查看详情'
                ], Url::build('wap/spread/commission', [], true, true));
            } else {
                $dat['thing8']['value'] = '返佣金额';
                $dat['date4']['value'] = date('Y-m-d H:i:s', time());
                $dat['amount1']['value'] = $brokeragePrice;
                $dat['amount2']['value'] = $User['brokerage_price'];
                $dat['thing5']['value'] = '您收到一笔会员返佣！';
                RoutineTemplate::sendAccountChanges($dat, $userInfoTwo['spread_uid'], Url::build('wap/spread/commission', [], true, true));
            }
        }
        $res = $res1 && $res2;
        self::checkTrans($res);
        return $res;
    }


    /**
     * 获取推广人列表
     * @param $where array 查询条件
     * @param $uid int 用户uid
     * @return array
     * */
    public static function getSpreadList($where, $uid)
    {
        $uids = self::getSpeadUids($uid, true);
        if (!count($uids)) return ['list' => [], 'page' => 2];
        $model = self::where('uid', 'in', $uids)->field(['nickname', 'avatar', 'phone', 'uid']);
        if ($where['search']) $model = $model->where('nickname|uid|phone', 'like', "%$where[search]%");
        $list = $model->page((int)$where['page'], (int)$where['limit'])->select();
        $list = count($list) ? $list->toArray() : [];
        $page = $where['page'] + 1;
        foreach ($list as $key => &$item) {
            $item['sellout_count'] = UserBill::where(['a.paid' => 1, 'a.is_del' => 0, 'u.category' => 'now_money', 'u.type' => 'brokerage', 'u.uid' => $item['uid']])->alias('u')->join('__STORE_ORDER__ a', 'a.id=u.link_id')
                ->count();
            $item['sellout_money'] = UserBill::where(['a.paid' => 1, 'a.is_del' => 0, 'u.category' => 'now_money', 'u.type' => 'brokerage', 'u.uid' => $item['uid']])->alias('u')->join('__STORE_ORDER__ a', 'a.id=u.link_id')
                ->sum('a.total_price');
        }
        return compact('list', 'page');
    }

    /**
     * 获取当前用户的下两级
     * @param int $uid 用户uid
     * @return array
     * */
    public static function getSpeadUids($uid, $isOne = false)
    {
        $uids = User::where('spread_uid', $uid)->column('uid');
        if ($isOne) return $uids;
        $two_uids = count($uids) ? User::where('spread_uid', 'in', $uids)->column('uid') : [];
        return array_merge($uids, $two_uids);
    }
}
