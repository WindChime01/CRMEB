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

namespace app\merchant\model\merchant;

use traits\ModelTrait;
use basic\ModelBasic;
use service\HookService;
use service\SystemConfigService;

/**
 * Class Merchant
 * @package app\merchant\model\merchant
 */
class Merchant extends ModelBasic
{
    use ModelTrait;

    public static function getMerchatMoney($mer_id)
    {
        return self::where(['id' => $mer_id])->value('now_money');
    }

    public static function getMerId($uid)
    {
        return self::where(['uid' => $uid])->value('id');
    }

    public static function getMerStatus($mer_id)
    {
        if (!$mer_id) return false;
        return (int)self::where(['id' => $mer_id])->value('status') == 0 ? true : false;
    }

    public static function getMerWhere()
    {
        return self::where(['is_del' => 0, 'estate' => 1, 'status' => 1]);
    }
}
