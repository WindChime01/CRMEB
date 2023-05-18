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


use basic\ModelBasic;
use service\QrcodeService;
use think\Cookie;
use think\Session;
use traits\ModelTrait;
use service\SystemConfigService;

/**h5 用户记录表
 * Class PhoneUser
 * @package app\wap\model\user
 */
class PhoneUser extends ModelBasic
{
    use ModelTrait;

    /**获取手机号
     * @param $uid
     * @return mixed
     */
    public static function UidToPhone($uid)
    {
        return self::where('uid', $uid)->value('phone');
    }

    /**手机号登录、注册
     * @param $phone
     * @param $request
     * @return array|bool
     */
    public static function UserLogIn($phone, $request)
    {
        $isfollow = false;
        self::startTrans();
        try {
            $name = '__login_phone_number';
            if (self::be(['phone' => $phone])) {
                $user = self::where('phone', $phone)->find();
                $user->last_ip = $request->ip();
                $user->last_time = time();
                if ($user->uid) {
                    Session::set('loginUid', $user->uid, 'wap');
                }
                $userinfo = User::where('uid', $user->uid)->find();
                if (!$userinfo->status) return self::setErrorInfo('账户已被禁止登录');
                $user->save();
            } else {
                $userinfo = User::where(['phone' => $phone])->find();
                if ($userinfo && !$userinfo->status) return self::setErrorInfo('账户已被禁止登录');
                if (!$userinfo) $userinfo = User::set([
                    'nickname' => $phone,
                    'pwd' => md5(123456),
                    'avatar' => '/system/images/user_log.jpg',
                    'account' => $phone,
                    'phone' => $phone,
                    'is_h5user' => 1,
                ]);
                if (!$userinfo) return self::setErrorInfo('用户信息写入失败', true);
                Session::set('loginUid', $userinfo['uid'], 'wap');
                $user = self::set([
                    'phone' => $phone,
                    'avatar' => '/system/images/user_log.jpg',
                    'nickname' => $phone,
                    'uid' => $userinfo['uid'],
                    'add_ip' => $request->ip(),
                    'add_time' => time(),
                    'pwd' => md5(123456),
                ]);
                if (!$user) return self::setErrorInfo('手机用户信息写入失败', true);
            }
            $isfollow = $userinfo['is_h5user'] ? false : true;
            Cookie::set('__login_phone', 1);
            Cookie::set('is_login', 1);
            Session::set($name, $phone, 'wap');
            Session::set('__login_phone_num' . $userinfo['uid'], $phone, 'wap');
            $res['url'] = SystemConfigService::get('wechat_qrcode');
            $res['id'] = 0;
            self::commit();
            return ['userinfo' => $user, 'url' => $res['url'], 'qcode_id' => $res['id'], 'isfollow' => $isfollow];
        } catch (\Exception $e) {
            return self::setErrorInfo($e->getMessage());
        }
    }

    /**用户注册账号、找回密码
     * @param $account
     * @param $pwd
     * @param $type
     * @param $request
     * @return bool
     */
    public static function userRegister($account, $pwd, $type, $request)
    {
        self::beginTrans();
        try {
            if ($type == 2) {
                $user = self::where('phone', $account)->find();
                if (!$user) return self::setErrorInfo('您需要先注册h5或pc账号', true);
                $userinfo = User::where(['phone' => $account])->find();
                if (!$userinfo) return self::setErrorInfo('您要找回的账号不存在', true);
                if (!$userinfo->status) return self::setErrorInfo('账户已被禁止登录');
                if ($user['pwd'] == $pwd || $userinfo['pwd'] == $pwd) return self::setErrorInfo('新密码和旧密码重复', true);
                $res1 = User::where(['phone' => $account])->update(['pwd' => $pwd]);
                $res2 = self::where(['phone' => $account])->update(['pwd' => $pwd]);
                $res = $res1 && $res2;
                self::checkTrans($res);
                if ($res) {
                    return true;
                } else {
                    return false;
                }
            } else if ($type == 1) {
                $userinfo = User::where(['phone' => $account])->find();
                if ($userinfo && !$userinfo->status) return self::setErrorInfo('账户已被禁止登录');
                if (!$userinfo) $userinfo = User::set([
                    'nickname' => $account,
                    'pwd' => $pwd,
                    'avatar' => '/system/images/user_log.jpg',
                    'account' => $account,
                    'phone' => $account,
                    'is_h5user' => 1,
                ]);
                if (!$userinfo) return self::setErrorInfo('用户信息写入失败', true);
                $user = self::where('phone', $account)->find();
                if ($user) return self::setErrorInfo('账号已被使用', true);
                $user = self::set([
                    'phone' => $account,
                    'avatar' => '/system/images/user_log.jpg',
                    'nickname' => $account,
                    'uid' => $userinfo['uid'],
                    'add_ip' => $request->ip(),
                    'add_time' => time(),
                    'pwd' => $pwd,
                ]);
                if (!$user) return self::setErrorInfo('手机用户信息写入失败', true);
                self::commitTrans();
                return true;
            }
        } catch (\Exception $e) {
            return self::setErrorInfo($e->getMessage());
        }
    }
}
