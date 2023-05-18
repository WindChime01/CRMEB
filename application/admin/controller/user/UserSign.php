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

namespace app\admin\controller\user;

use app\admin\model\user\UserSign as UserSignModel;
use app\admin\controller\AuthController;
use service\JsonService as Json;
use think\Url;
use think\Request;

/**用户签到
 * Class UserSign
 * @package app\admin\controller\user
 */
class UserSign extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    public function getUserSignList()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['title', ''],
            ['excel', 0]
        ]);
        return Json::successlayui(UserSignModel::getUserSignList($where));
    }

}
