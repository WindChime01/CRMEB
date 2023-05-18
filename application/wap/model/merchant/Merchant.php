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
use service\HookService;
use service\SystemConfigService;

/**
 * Class Merchant
 * @package app\wap\model\merchant
 */
class Merchant extends ModelBasic
{
    use ModelTrait;

    /**增加讲师余额
     * @param $mer_id
     * @param $price
     */
    public static function setMerchantNowMoney($mer_id, $price)
    {
        return self::where('id', $mer_id)->setInc('now_money', $price);
    }

    /**减讲师余额
     * @param $mer_id
     * @param $price
     * @return int|true
     * @throws \think\Exception
     */
    public static function decMerchantNowMoney($mer_id, $price)
    {
        return self::where('id', $mer_id)->setDec('now_money', $price);
    }

    /**获取$mer_id
     * @param $uid
     * @return mixed
     */
    public static function getMerId($uid)
    {
        return self::where(['uid' => $uid, 'status' => 1, 'is_del' => 0, 'estate' => 1])->value('id');
    }
}
