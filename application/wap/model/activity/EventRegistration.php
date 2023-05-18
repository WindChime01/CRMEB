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

namespace app\wap\model\activity;

use traits\ModelTrait;
use basic\ModelBasic;
use think\Db;

/**活动 model
 * Class EventRegistration
 * @package app\wap\model\activity
 */
class EventRegistration extends ModelBasic
{
    use ModelTrait;

    /**活动列表
     * @param int $page
     * @param int $limit
     * @return array
     */
    public static function eventRegistrationList($page = 1, $limit = 10)
    {
        $list = self::PreWhere()->order('sort DESC,add_time DESC')->page((int)$page, (int)$limit)->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        foreach ($list as &$v) {
            $v = self::singleActivity($v);
            $start_time = date('y/m/d H:i', $v['start_time']);
            $end_time = date('y/m/d H:i', $v['end_time']);
            $v['time'] = $start_time . '~' . $end_time;
            $v['count'] = EventSignUp::signUpCount($v['id']);
        }
        return $list;
    }

    /*可以显示的活动数量
     * @return int|string
     * @throws \think\Exception
     */
    public static function homeCount()
    {
        return self::PreWhere()->count();
    }

    /**获取单个活动
     * @param int $id
     */
    public static function oneActivitys($id = false)
    {
        $activity = self::PreWhere()->find($id ? $id : true);
        if ($activity) {
            $activity = self::singleActivity($activity->toArray());
            $activity['count'] = EventSignUp::signUpCount($id);
            $activity['surplus'] = bcsub($activity['number'], $activity['count'], 0);
            $activity['surplus'] = $activity['surplus'] < 0 ? 0 : $activity['surplus'];
            if ($activity['surplus'] <= 0) {
                $activity['statu'] = 2;
                self::where('id', $activity['id'])->update(['statu' => 2]);
            }
            $activity['signup_start_time'] = date('Y-m-d H:i', $activity['signup_start_time']);
            $activity['signup_end_time'] = date('Y-m-d H:i', $activity['signup_end_time']);
            $activity['start_time'] = date('Y-m-d H:i', $activity['start_time']);
            $activity['end_time'] = date('Y-m-d H:i', $activity['end_time']);
        }
        return $activity;
    }

    /**活动过滤
     * @return EventRegistration
     */
    public static function PreWhere()
    {
        return self::where(['is_show' => 1, 'is_del' => 0, 'status' => 1]);
    }

    /**判断活动状态
     * @param $activity
     * @return mixed
     */
    public static function singleActivity($activity)
    {
        if (bcsub($activity['signup_start_time'], time(), 0) > 0) {
            $statu = 0;//报名尚未开始
        } elseif (bcsub($activity['signup_start_time'], time(), 0) <= 0 && bcsub($activity['signup_end_time'], time(), 0) > 0) {
            $statu = 1;//报名开始
        } elseif (bcsub($activity['signup_end_time'], time(), 0) <= 0 && bcsub($activity['start_time'], time(), 0) > 0) {
            $statu = 2;//报名结束 活动尚未开始
        } elseif (bcsub($activity['start_time'], time(), 0) <= 0 && bcsub($activity['end_time'], time(), 0) > 0) {
            $statu = 3;//活动中
        } elseif (bcsub($activity['end_time'], time(), 0) < 0) {
            $statu = 4;//活动结束
        } else {
            $statu = -1;
        }
        if ($activity['statu'] != $statu) {
            $activity['statu'] = $statu;
            self::where('id', $activity['id'])->update(['statu' => $statu]);
        }
        return $activity;
    }

    /**讲师名下资料
     * @param $mer_id
     * @param $page
     * @param $limit
     */
    public static function getLecturerEventList($mer_id, $page, $limit)
    {
        $list = [];
        if (!$mer_id) return $list;
        $model = self::PreWhere();
        $model = $model->where(['mer_id' => $mer_id])->order('sort desc,id desc');
        $list = $model->page($page, $limit)->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as &$v) {
            $v = self::singleActivity($v);
            $start_time = date('y/m/d H:i', $v['start_time']);
            $end_time = date('y/m/d H:i', $v['end_time']);
            $v['time'] = $start_time . '~' . $end_time;
            $v['count'] = EventSignUp::signUpCount($v['id']);
        }
        return $list;
    }

}
