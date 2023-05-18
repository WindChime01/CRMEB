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


namespace app\admin\model\ump;

use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\user\User;

class EventWriteOffUser extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where)
    {
        $model = self::alias('w')->where(['w.event_id' => $where['event_id'], 'w.is_del' => 0])
            ->join('user u', 'w.uid=u.uid');
        return $model;
    }

    /**获取核销用户列表
     * @param $where
     * @return void
     */
    public static function get_event_write_off_user_list($where)
    {
        $list = self::setWhere($where)->field('w.id,w.event_id,w.uid,u.nickname,u.avatar')
            ->order('w.add_time desc')->select();
        $data = count($list) > 0 ? $list->toArray() : [];
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**活动添加核销用户
     * @param $data
     * @return bool|object
     */
    public static function set_event_write_off_user($data)
    {
        if (self::be(['event_id' => $data['event_id'], 'uid' => $data['uid'], 'is_del' => 0])) return true;
        $data['add_time'] = time();
        return self::set($data);
    }

    /**删除核销员
     * @param $data
     * @return EventWriteOffUser|false
     */
    public static function del_event_write_off_user($data)
    {
        if (!self::be(['event_id' => $data['event_id'], 'uid' => $data['uid'], 'is_del' => 0])) return false;
        return  self::where(['event_id' => $data['event_id'], 'uid' => $data['uid'], 'is_del' => 0])->update(['is_del' => 1]);
    }
}
