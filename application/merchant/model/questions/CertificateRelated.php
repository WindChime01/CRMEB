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

namespace app\merchant\model\questions;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 证书关联记录 Model
 * Class CertificateRelated
 * @package app\merchant\model\questions
 */
class CertificateRelated extends ModelBasic
{
    use ModelTrait;

    /**添加/修改关联信息
     * @param $data
     * @param $id
     */
    public static function addCertificateRelated($data, $id)
    {
        if ($id) {
            return self::edit($data, $id);
        } else {
            return self::set($data);
        }
    }
}
