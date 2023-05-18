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
use think\Db;

class EventRegistration extends ModelBasic
{
    use ModelTrait;


    public static function systemPage($where = array())
    {
        $model = self::setWherePage(self::setWhere($where));
        $model = $model->order('add_time DESC');
        $list = $model->page((int)$where['page'], (int)$where['limit'])->select();
        foreach ($list as $key => &$item) {
            $item['address'] = $item['province'] . $item['city'] . $item['district'] . $item['detail'];
            if (bcsub($item['signup_start_time'], time(), 0) > 0) {
                $statu = 0;//报名尚未开始
            } elseif (bcsub($item['signup_start_time'], time(), 0) <= 0 && bcsub($item['signup_end_time'], time(), 0) > 0) {
                $statu = 1;//报名开始
            } elseif (bcsub($item['signup_end_time'], time(), 0) <= 0 && bcsub($item['start_time'], time(), 0) > 0) {
                $statu = 2;//报名结束 活动尚未开始
            } elseif (bcsub($item['start_time'], time(), 0) <= 0 && bcsub($item['end_time'], time(), 0) > 0) {
                $statu = 3;//活动中
            } elseif (bcsub($item['end_time'], time(), 0) < 0) {
                $statu = 4;//活动结束
            } else {
                $statu = -1;
            }
            if ($item['statu'] != $statu) {
                $item['statu'] = $statu;
                self::where('id', $item['id'])->update(['statu' => $statu]);
            }
        }
        $count = self::setWherePage(self::setWhere($where))->count();
        return ['count' => $count, 'data' => $list];
    }

    /**
     * 设置搜索条件
     *
     */
    public static function setWhere($where)
    {
        $model = new self;
        if (isset($where['title']) && $where['title'] != '') {
            $model = $model->where('title', 'like', "%$where[title]%");
        }
        if (isset($where['mer_id']) && $where['mer_id'] != '') $model = $model->where('mer_id', $where['mer_id']);
        if (isset($where['status']) && $where['status'] != '') {
            $model = $model->where('status', $where['status']);
        } else {
            $model = $model->where('status', 'in', [1, -1, 0]);
        }
        if (isset($where['is_show']) && $where['is_show'] !== '') $model = $model->where('is_show', $where['is_show']);
        $model = $model->where('is_del', 0);
        return $model;
    }

    /**删除
     * @param $id
     * @return bool
     */
    public static function delArticleCategory($id)
    {
        $data['is_del'] = 1;
        return self::edit($data, $id);
    }

    /**获取活动
     * @param $id
     */
    public static function eventRegistrationOne($id)
    {
        $event = self::where('id', $id)->find();
        if (!$event) return [];
        $event['signup_start_time'] = date('Y-m-d H:i:s', $event['signup_start_time']);
        $event['signup_end_time'] = date('Y-m-d H:i:s', $event['signup_end_time']);
        $event['start_time'] = date('Y-m-d H:i:s', $event['start_time']);
        $event['end_time'] = date('Y-m-d H:i:s', $event['end_time']);
        $event['activity_rules'] = htmlspecialchars_decode($event['activity_rules']);
        $event['content'] = htmlspecialchars_decode($event['content']);
        return $event;
    }

    public static function eventExamineList($where = array())
    {
        $model = self::setWherePage(self::setWhere($where));
        $model = $model->order('add_time DESC');
        $list = $model->page((int)$where['page'], (int)$where['limit'])->select()->each(function ($item) {
            $item['address'] = $item['province'] . $item['city'] . $item['district'] . $item['detail'];
            $item['fail_time'] = date('Y-m-d H:i:s', $item['fail_time']);
        });
        $count = self::setWherePage(self::setWhere($where))->count();
        return ['count' => $count, 'data' => $list];
    }

    /**
     * 获取连表MOdel
     * @param $model
     * @return object
     */
    public static function getModelObject($where = [])
    {
        $model = new self();
        $model = $model->alias('p');
        if (!empty($where)) {
            $model = $model->group('p.id');
            if (isset($where['title']) && $where['title'] != '') {
                $model = $model->where('p.title|p.id', 'LIKE', "%$where[title]%");
            }
            if (isset($where['mer_id']) && trim($where['mer_id']) != '') {
                $model = $model->where('p.mer_id', $where['mer_id']);
            }
            if (isset($where['order']) && $where['order'] != '') {
                $model = $model->order(self::setOrder($where['order']));
            } else {
                $model = $model->order('p.sort DESC,p.add_time DESC');
            }
        }
        return $model->where('p.status', 1);
    }

    /**
     * 获取活动
     */
    public static function storeEventList($where, $special_source)
    {
        $model = self::getModelObject($where)->where('p.id', 'not in', $special_source)->field(['p.*']);
        $model = $model->page((int)$where['page'], (int)$where['limit']);
        $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        $count = self::getModelObject($where)->where('p.id', 'not in', $special_source)->count();
        return compact('count', 'data');
    }
}
