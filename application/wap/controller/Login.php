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
use app\wap\model\user\PhoneUser;
use app\wap\model\user\WechatUser;
use app\wap\model\user\User;
use basic\WapBasic;
use service\SystemConfigService;
use service\UtilService;
use service\JsonService;
use think\Cache;
use think\Cookie;
use think\Request;
use think\Session;
use think\Url;

/**移动端登录控制器
 * Class Login
 * @package app\wap\controller
 */
class Login extends WapBasic
{
    public function index($ref = '', $spread_uid = 0)
    {
        $isWechat = UtilService::isWechatBrowser();
        $appid = SystemConfigService::get('wechat_appid');
        $ref && $ref = htmlspecialchars_decode(base64_decode($ref));
        $this->assign(['appid' => $appid,'ref' => $ref,'spread_uid' => $spread_uid,'isWechat' => $isWechat, 'Auth_site_name' => SystemConfigService::get('site_name')]);
        return $this->fetch();
    }

    /**微信登录操作
     * @param $spread_uid
     * @return void
     */
    public function weixin_check($spread_uid = 0, $code = '')
    {
        Cookie::set('is_bg', 1);
        $this->_logout();
        $openid = $this->oauth($spread_uid, $code);
        if ($openid) {
            Cookie::delete('_oen');
            return JsonService::successful('微信登录成功', $openid);
        } else {
            return JsonService::fail(WechatUser::getErrorInfo());
        }
    }

    /**
     * 短信登陆
     * @param Request $request
     */
    public function phone_check(Request $request)
    {
        list($phone, $code) = UtilService::postMore([
            ['phone', ''],
            ['code', ''],
        ], $request, true);
        if (!$phone || !$code) return JsonService::fail('请输入登录账号');
        if (!$code) return JsonService::fail('请输入验证码');
        $code = md5('is_phone_code' . $code);
        if (!SmsCode::CheckCode($phone, $code)) return JsonService::fail('验证码验证失败');
        SmsCode::setCodeInvalid($phone, $code);
        if (($info = PhoneUser::UserLogIn($phone, $request)) !== false)
            return JsonService::successful('登录成功', $info);
        else
            return JsonService::fail(PhoneUser::getErrorInfo('登录失败'));
    }

    /**账号密码登录
     * @param Request $request
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function check(Request $request)
    {
        list($account, $pwd) = UtilService::postMore(['account', 'pwd'], $request, true);
        if (!$account || !$pwd) return JsonService::fail('请输入登录账号');
        if (!PhoneUser::be(['phone' => $account])) return JsonService::fail('登陆账号不存在!');
        $phoneInfo = PhoneUser::where('phone', $account)->find();
        $errorInfo = Session::get('login_error_info', 'wap') ?: ['num' => 0];
        $now = time();
        if ($errorInfo['num'] > 5 && $errorInfo['time'] < ($now - 900))
            return JsonService::fail('错误次数过多,请稍候再试!');
        if ($phoneInfo['pwd'] != $pwd) {
            Session::set('login_error_info', ['num' => $errorInfo['num'] + 1, 'time' => $now], 'wap');
            return JsonService::fail('账号或密码输入错误!');
        }
        $userinfo = User::where('uid', $phoneInfo['uid'])->find();
        if (!$userinfo) return JsonService::fail('账号异常!');
        if (!$userinfo['status']) return JsonService::fail('账号已被锁定,无法登陆!');
        $this->_logout();
        $name = '__login_phone_number';
        Session::set('loginUid', $userinfo['uid'], 'wap');
        $phoneInfo['last_time'] = time();
        $phoneInfo['last_ip'] = $request->ip();
        $phoneInfo->save();
        unset($userinfo['pwd']);
        Session::delete('login_error_info', 'wap');
        Cookie::set('is_login', 1);
        Cookie::set('__login_phone', 1);
        Session::set($name, $userinfo['phone'], 'wap');
        Session::set('__login_phone_num' . $userinfo['uid'], $userinfo['phone'], 'wap');
        $qrcode_url = SystemConfigService::get('wechat_qrcode');
        $info = ['userinfo' => $userinfo, 'url' => $qrcode_url, 'qcode_id' => 0, 'isfollow' => false];
        return JsonService::successful('登录成功', $info);
    }

    /**账号密码注册/找回密码
     * @param Request $request
     * @param $account 账号
     * @param $pwd 密码
     * @param $code 验证码
     * @param $type 1=注册 2=找回密码
     */
    public function register(Request $request)
    {
        list($account, $pwd, $code, $type) = UtilService::postMore([
            ['account', ''],
            ['pwd', ''],
            ['code', ''],
            ['type', 1]
        ], $request, true);
        if (!$account || !$pwd || !$code) return JsonService::fail('参数有误！');
        if (!$code) return JsonService::fail('请输入验证码');
        $code = md5('is_phone_code' . $code);
        if (!SmsCode::CheckCode($account, $code)) return JsonService::fail('验证码验证失败');
        SmsCode::setCodeInvalid($account, $code);
        $msg = $type == 1 ? '注册' : '找回密码';
        if (($info = PhoneUser::userRegister($account, $pwd, $type, $request)) !== false)
            return JsonService::successful($msg . '成功');
        else
            return JsonService::fail(PhoneUser::getErrorInfo(PhoneUser::getErrorInfo($msg . '失败')));
    }

    /**
     * 退出登陆
     */
    public function logout()
    {
        $this->_logout();
        $this->successful('退出登陆成功', Url::build('Index/index'));
    }

    /**
     * 清除缓存
     */
    private function _logout()
    {
        Session::clear('wap');
        Cookie::delete('is_bg');
        Cookie::delete('is_login');
        Cookie::delete('__login_phone');
    }

}
