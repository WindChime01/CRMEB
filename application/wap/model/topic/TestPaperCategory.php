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
use service\UtilService as Util;

/**
 * 试卷分类 Model
 * Class TestPaperCategory
 */
class TestPaperCategory extends ModelBasic
{
    use ModelTrait;

    public function children()
    {
        return $this->hasMany('TestPaperCategory', 'pid', 'id')->where(['is_del' => 0, 'is_show' => 1])->order('sort DESC,id DESC');
    }
}
