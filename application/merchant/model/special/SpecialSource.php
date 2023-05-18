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

namespace app\merchant\model\special;

use app\merchant\model\special\Special as SpecialModel;
use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class SpecialSource 专题素材关联表
 * @package app\merchant\model\special
 */
class SpecialSource extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($special_id)
    {
        $model = self::alias('s')->where(['s.special_id' => $special_id, 't.is_del' => 0, 't.is_show' => 1])
            ->join('SpecialTask t', 's.source_id=t.id')->field('t.title,t.image,t.is_del,t.is_show,s.*');
        return $model;
    }

    /**查看专题下的素材
     * @param int $special_id
     * @param int $page
     * @param int $limit
     */
    public static function getSpecialSourceList($special_id = 0, $page = 1, $limit = 20, $order = 0)
    {
        $data = self::setWhere($special_id)->page($page, $limit);
        if ($order) {
            $data = $data->order('s.sort asc,s.id asc')->select();
        } else {
            $data = $data->order('s.sort desc,s.id desc')->select();
        }
        $count = self::setWhere($special_id)->count();
        return compact('data', 'count');
    }

    public static function setSpecialWhere($special_id, $mer_id)
    {
        $model = self::alias('s')->where(['s.special_id' => $special_id, 'l.mer_id' => $mer_id, 'l.is_del' => 0, 'l.is_show' => 1, 'l.status' => 1])
            ->join('Special l', 's.source_id=l.id')->field('l.title,l.image,l.type,l.mer_id,l.is_show,l.is_del,l.light_type,l.is_light,s.special_id,s.source_id,s.sort,s.pay_status,s.id');
        return $model;
    }

    /**查看专栏专题下的专题
     * @param int $special_id
     * @param int $page
     * @param int $limit
     */
    public static function getSpecialList($special_id = 0, $mer_id = 0, $page = 1, $limit = 20, $order = 0)
    {
        $model = self::setSpecialWhere($special_id, $mer_id)->page($page, $limit);
        if ($order) {
            $data = $model->order('s.sort asc,s.id asc')->select();
        } else {
            $data = $model->order('s.sort desc,s.id desc')->select();
        }
        $data = count($data) > 0 ? $data->toArray() : [];
        $count = self::setSpecialWhere($special_id, $mer_id)->count();
        return compact('data', 'count');
    }

    /**新增素材
     * @param $ids
     * @param int $special_id
     * @param int $special_type
     * @return bool
     */
    public static function addSpecialSource($ids, $special_id = 0, $special_type = 1)
    {
        if (!$ids || !$special_id) return false;
        try {
            $source_list_ids = explode(',', $ids);
            $inster['special_id'] = $special_id;
            $data = SpecialModel::where('id', $special_id)->field('pay_type,member_pay_type,validity')->find();
            if (!$data) return false;
            foreach ($source_list_ids as $sk => $id) {
                if ($special_type == SPECIAL_COLUMN) {
                    $special = SpecialModel::where('id', $id)->field('pay_type,member_pay_type')->find();
                    if ($data['pay_type'] == 1 && $data['member_pay_type'] == 0) {
                        if ($special['pay_type'] == 1 && $special['member_pay_type'] == 1) {
                            SpecialModel::where('id', $id)->update(['member_pay_type' => 0, 'member_money' => 0]);
                        }
                        $inster['pay_status'] = 1;
                    } else if ($data['pay_type'] == 0) {
                        if ($special['pay_type'] == 1) {
                            SpecialModel::where('id', $id)->update(['member_pay_type' => 0, 'member_money' => 0, 'pay_type' => 0, 'money' => 0]);
                        }
                        $inster['pay_status'] = 0;
                    }
                    SpecialModel::where('id', $id)->update(['validity' => $data['validity']]);
                } else {
                    $inster['pay_status'] = 1;
                }
                $inster['type'] = $special_type;
                $inster['source_id'] = $id;
                $inster['add_time'] = time();
                $res = self::set($inster);
                if ($res) {
                    SpecialModel::where('id', $special_id)->setInc('quantity', 1);
                } else {
                    continue;
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**获取专题素材
     * @param bool $special_id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getSpecialSource($special_id = false, $source_id = false, $order = 0)
    {
        $where = array();
        $data = self::where($where);
        if ($special_id && is_numeric($special_id)) {
            $where['special_id'] = $special_id;
            $data->where($where);
        }
        if ($source_id) {
            if (!is_array($source_id)) {
                $where['source_id'] = $source_id;
                $data->where($where);
            } else {
                $data->whereIn('source_id', $source_id);
            }
        }
        if ($order) {
            $data->order('sort asc,id asc');
        } else {
            $data->order('sort desc,id desc');
        }
        return $data->select();
    }

    /**获取专题下素材数量
     * @param bool $special_id
     * @param bool $source_id
     */
    public static function getSpecialSourceCount($special_id = false, $source_id = false)
    {
        $where = array();
        $data = self::where($where);
        if ($special_id && is_numeric($special_id)) {
            $where['special_id'] = $special_id;
            $data->where($where);
        }
        if ($source_id) {
            if (!is_array($source_id)) {
                $where['source_id'] = $source_id;
                $data->where($where);
            } else {
                $data->whereIn('source_id', $source_id);
            }
        }
        return $data->count();
    }

    /**更新及添加专题素材
     * @param $source_list_ids  一维数组，素材id
     * @param int $special_id 专题id
     * @return bool
     */
    public static function saveSpecialSource($source_list_ids, $special_id = 0, $special_type = 1, $data = [])
    {
        if (!$special_id || !is_numeric($special_id)) {
            return false;
        }
        if (!$source_list_ids || !is_array($source_list_ids)) {
            return false;
        }
        try {
            $specialSourceAll = self::getSpecialSource($special_id);
            $specialSourceAll = count($specialSourceAll) > 0 ? $specialSourceAll->toArray() : [];
            if ($specialSourceAll) {
                self::where(['special_id' => $special_id])->delete();
            }
            $inster['special_id'] = $special_id;
            foreach ($source_list_ids as $sk => $sv) {
                if ($special_type == SPECIAL_COLUMN) {
                    $special = SpecialModel::where('id', $sv->id)->field('pay_type,member_pay_type')->find();
                    if ($data['pay_type'] == 1 && $data['member_pay_type'] == 0) {
                        if ($special['pay_type'] == 1 && $special['member_pay_type'] == 1) {
                            SpecialModel::where('id', $sv->id)->update(['member_pay_type' => 0, 'member_money' => 0]);
                        }
                        $inster['pay_status'] = 1;
                    } else if ($data['pay_type'] == 0) {
                        if ($special['pay_type'] == 1) {
                            SpecialModel::where('id', $sv->id)->update(['member_pay_type' => 0, 'member_money' => 0, 'pay_type' => 0, 'money' => 0]);
                        }
                        $inster['pay_status'] = 0;
                    }
                    $dat['validity'] = $data['validity'];
                    $dat['is_mer_visible'] = $data['is_mer_visible'];
                    SpecialModel::edit($dat, $sv->id, 'id');
                } else {
                    $inster['pay_status'] = $sv->pay_status;
                }
                $inster['source_id'] = $sv->id;
                $inster['type'] = $special_type;
                $inster['sort'] = $sv->sort;
                $inster['add_time'] = time();
                $res = self::set($inster);
                if (!$res) continue;
                SpecialModel::where('id', $special_id)->setInc('quantity', 1);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**清空专题素材
     * @param int $special_id 专题id
     */
    public static function delSpecialSource($special_id = 0)
    {
        if (!$special_id || !is_numeric($special_id)) {
            return false;
        }
        try {
            $specialSourceAll = self::getSpecialSource($special_id);
            $specialSourceAll = count($specialSourceAll) > 0 ? $specialSourceAll->toArray() : [];
            if ($specialSourceAll) {
                self::where(['special_id' => $special_id])->delete();
                SpecialModel::where('id', $special_id)->update(['quantity' => 0]);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
