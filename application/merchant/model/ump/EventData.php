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


namespace app\merchant\model\ump;

use traits\ModelTrait;
use basic\ModelBasic;

class EventData extends ModelBasic
{
    use ModelTrait;

    /**添加活动资料
     * @param array $data
     */
    public static function eventDataAdd($id = 0, $data = [])
    {
        if (!$id) return false;
        self::where('event_id', $id)->delete();
        if (count($data) <= 0) return true;
        foreach ($data as $k => &$time) {
            $time['event_id'] = $id;
            self::set($time);
        }
        return true;
    }

    /**
     * 活动资料列表
     */
    public static function eventDataList($id = 0)
    {
        $list = self::where(['event_id' => $id])->order('sort DESC,id ASC')->select();
        return count($list) > 0 ? $list->toArray() : [];
    }

    /**删除活动资料
     * @param $event_id
     */
    public static function delEventData($event_id)
    {
        return self::where('event_id', $event_id)->delete();
    }
}
