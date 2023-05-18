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


namespace service;

use Fastknife\Service\BlockPuzzleCaptchaService;
use Fastknife\Service\ClickWordCaptchaService;
use Fastknife\Service\Service;
use think\Config;
use service\JsonService;
use think\exception\ValidateException;

class FastknifeService
{
    /**
     * 验证滑块1次验证
     * @param string $token
     * @param string $pointJson
     * @return bool
     */
    public static function aj_captcha_check_one(string $captchaType, string $token, string $pointJson)
    {
        self::aj_get_serevice($captchaType)->check($token, $pointJson);
        return true;
    }

    /**
     * 验证滑块2次验证
     * @param string $token
     * @param string $pointJson
     * @return bool
     */
    public static function aj_captcha_check_two(string $captchaType, string $captchaVerification)
    {
        self::aj_get_serevice($captchaType)->verificationByEncryptCode($captchaVerification);
        return true;
    }


    /**
     * 创建验证码
     * @return array
     */
    public static function aj_captcha_create(string $captchaType)
    {
        return self::aj_get_serevice($captchaType)->get();
    }


    /**
     * @param string $captchaType
     */
    public static function aj_get_serevice(string $captchaType)
    {
        $config = require APP_PATH . "captcha.php";
        switch ($captchaType) {
            case "clickWord":
                $service = new ClickWordCaptchaService($config);
                break;
            case "blockPuzzle":
                $service = new BlockPuzzleCaptchaService($config);
                break;
            default:
                throw new ValidateException('captchaType参数不正确！');
        }
        return $service;
    }
}
