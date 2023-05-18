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

namespace app\wap\controller;

use think\Request;


class Callback extends AuthController
{
    /**
     * @param $type 1=专题 2=商品 3=报名 4=金币充值 5=会员 6=考试 7=轻专题 8=资料
     * @return mixed
     */
    public function pay_success_synchro($type = 0, $id = 0)
    {
        $this->assign(['type' => $type, 'id' => $id]);
        return $this->fetch();
    }

}


