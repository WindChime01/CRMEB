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
 * 直播带货商品
 */

use app\admin\model\order\StoreOrder;
use app\admin\model\store\StoreProduct;
use app\admin\model\ump\EventRegistration;
use app\admin\model\special\Special;
use basic\ModelBasic;
use traits\ModelTrait;

class LiveGoods extends ModelBasic
{
    use ModelTrait;

    public static function setSpecialWhere($where)
    {
        $model = self::alias('g');
        $model = $model->where('g.is_delete', 0)->where('g.type', 0);
        $model = $model->join('special s', 'g.special_id=s.id')->join('__SPECIAL_SUBJECT__ J', 'J.id=s.subject_id');
        if ($where['store_name'] && isset($where['store_name'])) {
            $model = $model->whereLike('g.special_name', "%" . $where['store_name'] . "%");
        }
        if ($where['is_show'] != "" && isset($where['is_show'])) {
            $model = $model->where('g.is_show', $where['is_show']);
        }
        if ($where['live_id'] != 0 && isset($where['live_id'])) {
            $model = $model->where('g.live_id', $where['live_id']);
        }
        return $model;
    }

    /*
     * 查询直播间用户列表
     * @param array $where
     * */
    public static function getLiveGoodsList($where)
    {
        $data = self::setSpecialWhere($where);
        $data = $data->field('g.id as live_goods_id, g.sort as gsort, g.fake_sales as gfake_sales, g.is_show as gis_show, g.sales as gsales, s.*, J.name as subject_name');
        $data = $data->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['_add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['pink_end_time'] = $item['pink_end_time'] ? strtotime($item['pink_end_time']) : 0;
            $item['sales'] = StoreOrder::where(['paid' => 1, 'type' => 0, 'cart_id' => $item['id'], 'refund_status' => 0])->count();
            //查看拼团状态,如果已结束关闭拼团
            if ($item['is_pink'] && $item['pink_end_time'] < time()) {
                self::update(['is_pink' => 0], ['id' => $item['id']]);
                $item['is_pink'] = 0;
            }
        }
        $count = self::setSpecialWhere($where)->count();
        return compact('data', 'count');
    }

    public static function setStoreWhere($where)
    {
        $model = self::alias('g');
        $model = $model->where('g.is_delete', 0)->where('g.type', 1);
        if ($where['store_name'] && isset($where['store_name'])) {
            $model = $model->whereLike('g.special_name', "%" . $where['store_name'] . "%");
        }
        if ($where['is_show'] != "" && isset($where['is_show'])) {
            $model = $model->where('g.is_show', $where['is_show']);
        }
        if ($where['live_id'] != 0 && isset($where['live_id'])) {
            $model = $model->where('g.live_id', $where['live_id']);
        }
        $model = $model->join('StoreProduct s', 'g.special_id=s.id')->where(['s.is_del' => 0, 's.is_show' => 1])->join('StoreCategory c', 'c.id=s.cate_id');
        return $model;
    }

    public static function getLiveStoreProductList($where)
    {

        $model = self::setStoreWhere($where)->field('g.live_id as live_id, g.id as live_goods_id, g.sort as gsort, g.fake_sales as gfake_sales, g.is_show as gis_show, g.sales as gsales, s.*,c.cate_name');
        $data = $model->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['_add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }
        $count = self::setStoreWhere($where)->count();
        return compact('data', 'count');
    }

    public static function setEventWhere($where)
    {
        $model = self::alias('g');
        $model = $model->where('g.is_delete', 0)->where('g.type', 2);
        if ($where['store_name'] && isset($where['store_name'])) {
            $model = $model->whereLike('g.special_name', "%" . $where['store_name'] . "%");
        }
        if ($where['is_show'] != "" && isset($where['is_show'])) {
            $model = $model->where('g.is_show', $where['is_show']);
        }
        if ($where['live_id'] != 0 && isset($where['live_id'])) {
            $model = $model->where('g.live_id', $where['live_id']);
        }
        $model = $model->join('EventRegistration e', 'g.special_id=e.id')->where(['e.is_del' => 0, 'e.is_show' => 1]);
        return $model;
    }

    public static function getLiveEventList($where)
    {
        $model = self::setEventWhere($where)->field('g.live_id as live_id, g.id as live_goods_id, g.sort as gsort, g.fake_sales as gfake_sales, g.is_show as gis_show, g.sales as gsales, e.*');
        $data = $model->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['_add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }
        $count = self::setEventWhere($where)->count();
        return compact('data', 'count');
    }

    /**直播带课
     * @param $live_id
     * @param int $type
     * @return LiveGoods|array|false|\PDOStatement|string|\think\Collection
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getLiveGoodsLists($live_id, $type = 0)
    {
        $data = self::alias('g');
        $data = $data->where('g.is_delete', 0);
        $data = $data->where('g.live_id', $live_id);
        $data = $data->where('g.type', $type);
        $data = $data->join('special s', 'g.special_id=s.id')->where(['s.is_del' => 0, 's.is_show' => 1])->join('__SPECIAL_SUBJECT__ J', 'J.id=s.subject_id')->field('g.live_id as live_id, g.id as live_goods_id, g.sort, g.fake_sales as gfake_sales, g.is_show as gis_show, g.sales as gsales,
         s.id,s.title,s.image,s.add_time,s.pink_end_time,s.is_pink,J.name as subject_name');
        $data = $data->order('g.sort DESC')->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['_add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['pink_end_time'] = $item['pink_end_time'] ? strtotime($item['pink_end_time']) : 0;
            $item['sales'] = StoreOrder::where(['paid' => 1, 'cart_id' => $item['id'], 'refund_status' => 0])->count();
            //查看拼团状态,如果已结束关闭拼团
            if ($item['is_pink'] && $item['pink_end_time'] < time()) {
                self::update(['is_pink' => 0], ['id' => $item['id']]);
                $item['is_pink'] = 0;
            }
        }
        return $data;
    }

    /**直播带货
     * @param $live_id
     * @param int $type
     * @return LiveGoods|array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getLiveProductLists($live_id, $type = 0)
    {
        $data = self::alias('g');
        $data = $data->where('g.is_delete', 0);
        $data = $data->where('g.live_id', $live_id);
        $data = $data->where('g.type', $type);
        $data = $data->join('StoreProduct s', 'g.special_id=s.id')->where(['s.is_del' => 0, 's.is_show' => 1])->field('g.live_id as live_id, g.id as live_goods_id, g.sort, g.fake_sales as gfake_sales, g.is_show as gis_show, g.sales as gsales,
         s.id,s.store_name,s.image,s.price,s.sales,s.add_time');
        $data = $data->order('g.sort DESC')->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['_add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }
        return $data;
    }

    /**直播带货
     * @param $live_id
     * @param int $type
     * @return LiveGoods|array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getLiveEventLists($live_id, $type = 0)
    {
        $data = self::alias('g');
        $data = $data->where('g.is_delete', 0);
        $data = $data->where('g.live_id', $live_id);
        $data = $data->where('g.type', $type);
        $data = $data->join('EventRegistration e', 'g.special_id=e.id')->where(['e.is_del' => 0, 'e.is_show' => 1])->field('g.live_id as live_id, g.id as live_goods_id, g.sort, g.fake_sales as gfake_sales, g.is_show as gis_show, g.sales as gsales,
         e.id,e.title,e.image,e.add_time');
        $data = $data->order('g.sort DESC')->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['_add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }
        return $data;
    }

    /**插入带货商品
     * @param array $data
     * @return bool|int|string
     */
    public static function insterLiveGoods(array $data)
    {
        if (!$data) return false;
        return self::insertGetId($data);
    }

    /**获取单个
     * @param array $where
     * @return array|bool|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getOne(array $where)
    {
        if (!$where) return false;
        return self::where($where)->find();
    }

    /**添加带货专题
     * @param $source_list_ids  一维数组，素材id
     * @param int $special_id 专题id
     * @return bool
     */
    public static function saveLiveGoods($source_list_ids, $special_id = 0, $type = 0)
    {
        if (!$special_id || !$source_list_ids) return false;
        $source_list_ids = explode(',', $source_list_ids);
        $live_id = LiveStudio::where('special_id', $special_id)->value('id');
        if (!$live_id) return false;
        try {
            $inster['live_id'] = $live_id;
            foreach ($source_list_ids as $sk => $sv) {
                $inster['special_id'] = $sv;
                $inster['type'] = $type;
                if ($type == 1) {
                    $inster['special_name'] = StoreProduct::where('id', $sv)->value('store_name');
                } else if ($type == 2) {
                    $inster['special_name'] = EventRegistration::where('id', $sv)->value('title');
                } else {
                    $inster['special_name'] = Special::where('id', $sv)->value('title');
                }
                $inster['sort'] = 0;
                $inster['add_time'] = time();
                self::set($inster);
            }
            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**添加带货
     * @param $source_list_ids  一维数组，素材id
     * @param int $special_id 专题id
     * @return bool
     */
    public static function saveAddLiveGoods($source_list_ids, int $special_id, $type = 0)
    {
        if (!$special_id || !is_numeric($special_id)) {
            return false;
        }
        $live_id = LiveStudio::where('special_id', $special_id)->field('id')->find();
        if (!$live_id) return false;
        if (!$source_list_ids) {
            self::where(['live_id' => $live_id->id, 'type' => $type])->delete();
            return true;
        }
        $where['live_id'] = $live_id->id;
        $liveGoodsAll = self::getOne($where);
        if ($liveGoodsAll) {
            self::where(['live_id' => $live_id->id, 'type' => $type])->delete();
        }
        $inster['live_id'] = $live_id->id;
        foreach ($source_list_ids as $sk => $sv) {
            $inster['special_id'] = $sv->id;
            $inster['type'] = $type;
            if ($type == 1) {
                $inster['special_name'] = $sv->store_name;
            } else {
                $inster['special_name'] = $sv->title;
            }
            $inster['sort'] = $sv->sort;
            $inster['add_time'] = time();
            self::set($inster);
        }
        return true;
    }

    /**清空带货
     * @param int $special_id 专题id
     * @return bool
     */
    public static function delLiveGoods($special_id, $type = 0)
    {
        if (!$special_id || !is_numeric($special_id)) {
            return false;
        }
        $live_id = LiveStudio::where('special_id', $special_id)->field('id')->find();
        if (!$live_id) return false;
        self::where(['live_id' => $live_id->id, 'type' => $type])->delete();
        return true;
    }

}
