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

namespace app\merchant\controller\merchant;


use app\merchant\controller\AuthController;
use app\merchant\model\merchant\MerchantFollow as MerchantFollowModel;

class MerchantFollow extends AuthController
{
    public function index()
    {
        $this->assign(MerchantFollowModel::systemPage($this->merchantId));
        return $this->fetch();
    }

}
