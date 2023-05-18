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

use app\admin\controller\AuthController;
use service\JsonService as Json;
use service\FormBuilder as Form;
use think\Url;
use app\admin\model\user\MemberRecord as MemberRecordModel;

/**会员获取记录
 * Class MemberRecord
 * @package app\admin\controller\user
 */
class MemberRecord extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 会员获取记录列表
     */
    public function member_record_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['title', ''],
            ['type', ''],
            ['excel', 0]
        ]);
        return Json::successlayui(MemberRecordModel::getPurchaseRecordList($where));
    }

}
