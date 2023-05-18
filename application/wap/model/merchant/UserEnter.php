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

namespace app\wap\model\merchant;

use traits\ModelTrait;
use basic\ModelBasic;
use think\Url;
use app\wap\model\special\Lecturer;
use service\SystemConfigService;
use app\wap\model\user\User;

/**
 * Class UserEnter 讲师申请
 * @package app\wap\model\merchant
 */
class UserEnter extends ModelBasic
{
    use ModelTrait;

    public static function getLabelAttr($value)
    {
        return is_string($value) ? json_decode($value, true) : $value;
    }

    public static function getCharterAttr($value)
    {
        return is_string($value) ? json_decode($value, true) : $value;
    }

    /**保存申请
     * @param $data
     * @param $uid
     * @return object
     */
    public static function setUserEnter($data, $uid)
    {
        $data['charter'] = json_encode($data['charter']);
        $data['label'] = json_encode($data['label']);
        if (self::be(['uid' => $uid])) {
            $data['status'] = 0;
            return self::edit($data, $uid, 'uid');
        } else {
            if (self::be(['link_tel' => $data['link_tel']])) return self::setErrorInfo('该手机号已使用，不可重复使用!');
            $data['uid'] = $uid;
            $data['add_time'] = time();
            return self::set($data);
        }
    }

    /**检查是否申请
     * @param $uid
     */
    public static function inspectStatus($uid)
    {
        return self::where(['uid' => $uid, 'is_del' => 0])->field('status,fail_message')->find();
    }

    /**申请数据
     * @param $uid
     */
    public static function inspectUserEnter($uid)
    {
        return self::where(['uid' => $uid, 'is_del' => 0])->find();

    }
}
