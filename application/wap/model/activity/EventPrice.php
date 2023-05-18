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

class EventPrice extends ModelBasic
{
    use ModelTrait;

    /**
     *获取 活动人数及对应价格列表
     */
    public static function eventPriceList($id = 0)
    {
        $list = self::where(['event_id' => $id])->order('sort ASC,id ASC')->select();
        return count($list) > 0 ? $list->toArray() : [];
    }

    /**
     * 获取单个最低价格
     */
    public static function getminEventPrice($id = 0)
    {
        return self::where(['event_id' => $id])->order('event_number ASC')->find();
    }
}
