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

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 会员设置 model
 * Class MemberShip
 * @package app\admin\model\user
 */
class MemberShip extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where)
    {
        $model = self::where('is_del', 0);
        if ($where['is_publish'] != '') $model->where('is_publish', $where['is_publish']);
        if ($where['title'] != '') $model->where('title', 'like', "%$where[title]%");
        return $model;
    }

    public static function getSytemVipList($where)
    {
        $model = self::setWhere($where)->order('sort DESC,add_time DESC');
        $data = ($list = $model->page((int)$where['page'], (int)$where['limit'])
            ->select()) && count($list) ? $list->toArray() : [];
        foreach ($data as &$item) {
            if ($item['vip_day'] == -1) $item['vip_day'] = '永久';
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    public static function getSytemVipSelect()
    {
        return self::where('mer_id', 0)->where('is_del', 0)->where('is_show', 1)->column('title', 'id');
    }


    /**自定义设置会员
     * @param $time
     * @param $user
     */
    public static function setUserCustomMember($day, $userInfo)
    {
        switch ($userInfo['level']) {
            case 1:
                $overdue_time = bcadd(bcmul($day, 86400, 0), $userInfo['overdue_time'], 0);
                $res = User::edit(['overdue_time' => $overdue_time], $userInfo['uid'], 'uid');
                break;
            case 0:
                $overdue_time = bcadd(bcmul($day, 86400, 0), time(), 0);
                $res = User::edit(['level' => 1, 'member_time' => time(), 'overdue_time' => $overdue_time], $userInfo['uid'], 'uid');
                break;
        }
        return $res;
    }
}
