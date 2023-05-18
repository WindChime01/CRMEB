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


namespace app\wap\model\store;


use basic\ModelBasic;
use traits\ModelTrait;

/**订单操作记录表
 * Class StoreOrderStatus
 * @package app\wap\model\store
 */
class StoreOrderStatus extends ModelBasic
{
    use ModelTrait;

    public static function status($oid, $change_type, $change_message, $change_time = null)
    {
        if ($change_time == null) $change_time = time();
        return self::set(compact('oid', 'change_type', 'change_message', 'change_time'));
    }

    public static function getTime($oid, $change_type)
    {
        return self::where('oid', $oid)->where('change_type', $change_type)->value('change_time');
    }

}
