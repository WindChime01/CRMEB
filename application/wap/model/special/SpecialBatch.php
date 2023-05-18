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

namespace app\wap\model\special;

use traits\ModelTrait;
use basic\ModelBasic;

/**专题兑换记录 model
 * Class SpecialBatch
 * @package app\wap\model\special
 */
class SpecialBatch extends ModelBasic
{
    use ModelTrait;

    /**专题是否开启兑换活动
     * @param $special_id
     */
    public static function isBatch($special_id)
    {
        $batch = self::where(['special_id' => $special_id, 'status' => 1])->find();
        if ($batch) return true;
        else return false;
    }


}
