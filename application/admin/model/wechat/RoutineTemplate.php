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

namespace app\admin\model\wechat;

use traits\ModelTrait;
use basic\ModelBasic;
use service\RoutineTemplateService;
use app\wap\model\special\Special;

/**
 * 微信订阅消息model
 * Class RoutineTemplate
 * @package app\admin\model\wechat
 */
class RoutineTemplate extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取系统分页数据   分类
     * @param array $where
     * @return array
     */
    public static function systemPage($where = array())
    {
        $model = new self;
        if ($where['name'] !== '') $model = $model->where('name', 'LIKE', "%$where[name]%");
        if ($where['status'] !== '') $model = $model->where('status', $where['status']);
        return self::page($model);
    }

    public static function create_template($type, $id = 0)
    {
        $model = new self;
        switch ($type) {
            case 1://提现
                $model = $model->where('tempkey', 'in', [RoutineTemplateService::USER_BALANCE_CHANGE]);
                break;
            case 10://会员
            case 60://试卷
                $model = $model->where('tempkey', 'in', [RoutineTemplateService::ORDER_PAY_SUCCESS]);
                break;
            case 20://报名
                $model = $model->where('tempkey', 'in', [RoutineTemplateService::ORDER_PAY_SUCCESS, RoutineTemplateService::ORDER_USER_SIGN_UP_SUCCESS]);
                break;
            case 40://商品
            case 50://商品订单再次支付
                $model = $model->where('tempkey', 'in', [RoutineTemplateService::ORDER_PAY_SUCCESS, RoutineTemplateService::ORDER_POSTAGE_SUCCESS, RoutineTemplateService::ORDER_REFUND_STATUS, RoutineTemplateService::ORDER_TAKE_SUCCESS]);
                break;
            default://专题支付
                $special = Special::where('id', $id)->find();
                if ($type == 3) {
                    if ($special['type'] == 4) {
                        $model = $model->where('tempkey', 'in', [RoutineTemplateService::ORDER_PAY_SUCCESS, RoutineTemplateService::LIVE_BROADCAST, RoutineTemplateService::PINK_ORDER_REMIND, RoutineTemplateService::ORDER_USER_GROUPS_SUCCESS, RoutineTemplateService::ORDER_USER_GROUPS_LOSE]);
                    } else {
                        $model = $model->where('tempkey', 'in', [RoutineTemplateService::ORDER_PAY_SUCCESS, RoutineTemplateService::PINK_ORDER_REMIND, RoutineTemplateService::ORDER_USER_GROUPS_SUCCESS, RoutineTemplateService::ORDER_USER_GROUPS_LOSE]);
                    }
                } else {
                    if ($special['type'] == 4) {
                        $model = $model->where('tempkey', 'in', [RoutineTemplateService::ORDER_PAY_SUCCESS, RoutineTemplateService::LIVE_BROADCAST]);
                    } else {
                        $model = $model->where('tempkey', 'in', [RoutineTemplateService::ORDER_PAY_SUCCESS]);
                    }
                }

        }
        return $model->where('status', 1)->column('tempid');
    }
}
