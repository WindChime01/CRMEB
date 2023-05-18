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

namespace app\merchant\model\live;

/**
 * 直播审核
 */

use basic\ModelBasic;
use traits\ModelTrait;
use app\merchant\model\live\LiveStudio;

class LiveAudit extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取连表MOdel
     * @param $model
     * @return object
     */
    public static function getModelExamine($where = [])
    {
        $model = new self();
        $model = $model->alias('p');
        if (isset($where['store_name']) && $where['store_name'] != '') {
            $model = $model->where('p.live_title|p.stream_name', 'LIKE', "%$where[store_name]%");
        }
        if (isset($where['mer_id']) && $where['mer_id']) {
            $model = $model->where('p.mer_id', $where['mer_id']);
        }
        if (isset($where['order']) && $where['order'] != '') {
            $model = $model->order(self::setOrder($where['order']));
        } else {
            $model = $model->order('p.add_time DESC');
        }
        $model = $model->join('LiveStudio l', 'p.live_id=l.id');
        return $model;
    }

    /*
     * 获取直播审核列表
     * @param $where array
     * @return array
     *
     */
    public static function liveExamineList($where)
    {
        $model = self::getModelExamine($where)->field('p.*');
        $model = $model->page((int)$where['page'], (int)$where['limit']);
        $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        foreach ($data as $key => &$volue) {
            $volue['live_strar_time'] = date('Y-m-d H:i:s', $volue['live_strar_time']);
            $volue['live_end_time'] = date('Y-m-d H:i:s', $volue['live_end_time']);
            $volue['fail_time'] = date('Y-m-d H:i:s', $volue['fail_time']);
        }
        $count = self::getModelExamine($where)->count();
        return compact('count', 'data');
    }
}
