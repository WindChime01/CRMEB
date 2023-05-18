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

namespace app\admin\controller\widget;

use app\admin\controller\AuthController;

/**
 * 文件校验控制器
 * Class Widgets
 * @package app\admin\controller\widget
 *
 */
class Widgets extends AuthController
{

    /**
     * icon
     * @return \think\response\Json
     */
    public function icon()
    {
        return $this->fetch('widget/icon');
    }

    /**
     * 会员列页面
     * @return \think\response\Json
     */
    public function userlist()
    {
        return $this->fetch('widget/icon');
    }

    /**
     * 产品列表页
     * @return \think\response\Json
     */
    public function productlist()
    {
        return $this->fetch('widget/icon');
    }

    /**
     * 图文列表页
     * @return \think\response\Json
     */
    public function newtlist()
    {
        return $this->fetch('widget/icon');
    }


}
