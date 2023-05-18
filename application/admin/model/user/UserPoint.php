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


namespace app\admin\model\user;

use app\admin\model\user\User;
use app\admin\model\user\UserBill;
use traits\ModelTrait;
use basic\ModelBasic;


class UserPoint extends ModelBasic
{
    use ModelTrait;

    /*
     * 获取积分信息
     * */
    public static function systemPage($where)
    {
        $model = new UserBill();
        if ($where['status'] != '') $model = $model->where('status', $where['status']);
        if ($where['title'] != '') $model = $model->where('title', 'like', "%$where[status]%");
        $model = $model->where('category', 'integral')->select();
        return $model::page($model);
    }


    public static function setWhere($where)
    {
        $model = UserBill::alias('a')->join('__USER__ b', 'a.uid=b.uid', 'left')->where('a.category', 'integral');
        $time['data'] = '';
        if ($where['start_time'] != '' && $where['end_time'] != '') {
            $time['data'] = $where['start_time'] . ' - ' . $where['end_time'];
        }
        $model = self::getModelTime($time, $model, 'a.add_time');
        if ($where['nickname'] != '') {
            $model = $model->where('b.nickname|b.uid', 'like', $where['nickname']);
        }
        return $model;
    }
}
