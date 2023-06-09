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

namespace app\admin\model\live;

/**
 * 直播间用户表
 */

use basic\ModelBasic;
use traits\ModelTrait;

class LiveUser extends ModelBasic
{
    use ModelTrait;

    /*
     *  设置查询条件
     * */
    public static function setUserWhere($where, $model = null, $alias = 'a', $jsonField = 'u.nickname')
    {
        $model = is_null($model) ? new self() : $model;
        if ($alias) {
            $model = $model->alias($alias);
            $alias .= '.';
        }
        if ($where['nickname']) $model = $model->where("{$alias}uid" . ($jsonField ? '|' . $jsonField : ''), 'LIKE', "$where[nickname]");
        if ($where['start_time'] && $where['end_time']) $model = $model->where("{$alias}add_time", 'between', [strtotime($where['start_time']), strtotime($where['end_time'])]);
        return $model->order("{$alias}add_time desc")->where("{$alias}live_id", $where['live_id']);
    }

    /*
     * 查询直播间用户列表
     * @param array $where
     * */
    public static function getLiveUserList($where)
    {
        $data = self::setUserWhere($where)->join('user u', 'a.uid=u.uid')->field(['a.*', 'u.nickname', 'u.avatar'])
            ->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['_add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['_last_time'] = $item['last_time'] ? date('Y-m-d H:i:s', $item['last_time']) : '暂无';
            $item['ban_time'] = $item['ban_time'] ? date('Y-m-d H:i:s', $item['ban_time']) : '0';
            $item['open_ben_time'] = $item['open_ben_time'] ? date('Y-m-d H:i:s', $item['open_ben_time']) : '0';
        }
        $count = self::setUserWhere($where)->join('user u', 'a.uid=u.uid')->count();
        return compact('data', 'count');
    }

}
