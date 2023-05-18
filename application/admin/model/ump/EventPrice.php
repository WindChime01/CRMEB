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

class EventPrice extends ModelBasic
{
    use ModelTrait;

    /**添加 活动人数及对应价格
     * @param array $data
     */
    public static function eventPriceAdd($id = 0, $data = [], $number = 0)
    {
        if (!$id) return false;
        self::where('event_id', $id)->delete();
        if (count($data) <= 0) return true;
        foreach ($data as $k => &$time) {
            $time['event_id'] = $id;
            if ($time['event_number'] >= $number) continue;
            self::set($time);
        }
        return true;
    }

    /**
     *获取 活动人数及对应价格列表
     */
    public static function eventPriceList($id = 0)
    {
        $list = self::where(['event_id' => $id])->order('sort ASC')->select();
        return count($list) > 0 ? $list->toArray() : [];
    }

    /**删除 活动人数及对应价格列表
     * @param $event_id
     */
    public static function delEventPrice($event_id)
    {
        return self::where('event_id', $event_id)->delete();
    }
}
