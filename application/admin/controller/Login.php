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

namespace app\admin\controller;

use app\admin\model\system\SystemAdmin;
use app\admin\model\system\SystemConfig;
use basic\SystemBasic;
use service\CacheService;
use service\UtilService;
use service\FastknifeService;
use service\JsonService;
use basic\AuthBasic;
use think\Request;
use think\Response;
use think\Session;
use think\Url;

/**
 * 登录验证控制器
 * Class Login
 * @package app\admin\controller
 */
class Login extends AuthBasic
{
    public function index()
    {
        $this->assign([
            'login_logo' => SystemConfig::getValue('login_logo'),
            'login_left_image' => SystemConfig::getValue('login_image_general_platform'),
            'Auth_site_name' => SystemConfig::getValue('site_name')
        ]);
        return $this->fetch();
    }

    /**
     * 读取版权信息
     */
    public function get_copyright()
    {
        return JsonService::successful('ok', $this->__z6uxyJQ4xYa5ee1mx5());
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
        $data = parent::postMore([
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
     * 登录验证 + 验证码验证
     */
    public function verify(Request $request)
    {
        if (!$request->isPost()) return ['code' => 4];
        $array = $request->Post();
        $account = $array['account'];
        $pwd = $array['pwd'];
        $error = Session::get('login_error', 'admin') ?: ['num' => 0, 'time' => time()];
        if ($error['num'] > 2) {
            try {
                if (!$array['captchaType'] || !$array['captchaVerification']) return ['code' => 3];
                FastknifeService::aj_captcha_check_two($array['captchaType'], $array['captchaVerification']);
            } catch (\Throwable $e) {
                return ['code' => 3];
            }
        }
        //检验帐号密码
        $res = SystemAdmin::login($account, $pwd);
        if ($res) {
            Session::set('login_error', null, 'admin');
            return ['code' => 1];
        } else {
            $error['num'] += 1;
            $error['time'] = time();
            Session::set('login_error', $error, 'admin');
            return ['code' => 0, 'num' => $error['num'], 'msg' => SystemAdmin::getErrorInfo()];
        }
    }

    public function captcha()
    {
        ob_clean();
        $captcha = new \think\captcha\Captcha([
            'codeSet' => '0123456789',
            'length' => 4,
            'fontSize' => 30
        ]);
        return $captcha->entry();
    }

    /**
     * 退出登陆
     */
    public function logout()
    {
        SystemAdmin::clearLoginInfo();
        $this->redirect('Login/index');
    }
}
