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

namespace app\wap\model\topic;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 证书 Model
 * Class Certificate
 */
class Certificate extends ModelBasic
{
    use ModelTrait;

    /**获取单个证书内容
     * @param $id
     * @param $obtain
     */
    public static function getone($id, $obtain)
    {
        return self::where(['id' => $id, 'obtain' => $obtain, 'status' => 1, 'is_del' => 0])->find();
    }

}
