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


namespace app\admin\model\special;

use traits\ModelTrait;
use basic\ModelBasic;
use service\UtilService as Util;

/**
 * Class SpecialSubject 二级分类
 * @package app\admin\model\special
 */
class SpecialSubject extends ModelBasic
{
    use ModelTrait;

    public static function specialCategoryAll($type = 0)
    {
        $model = self::where(['is_del' => 0, 'is_show' => 1]);
        if ($type == 1) {
            $model = $model->where('grade_id', 0);
        }
        $list = $model->order('sort desc,add_time desc')->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        $list = Util::sortListTier($list, 0, 'grade_id');
        return $list;
    }

    public static function get_subject_list($where)
    {
        $data = self::setWhere($where)->column('id,grade_id');
        $list = [];
        foreach ($data as $ket => $item) {
            $cate = self::where('id', $ket)->where('is_del', 0)->find();
            if ($cate) {
                $cate = $cate->toArray();
                if ($item > 0) {
                    $cate['special_count'] = Special::where(['subject_id' => $ket, 'is_del' => 0])->count();
                } else {
                    $pids = self::categoryId($ket);
                    $cate['special_count'] = Special::where(['is_del' => 0])->where('subject_id', 'in', $pids)->count();
                }
                array_push($list, $cate);
                unset($cate);
            }
            if ($item > 0 && !array_key_exists($item, $data)) {
                $cate = self::where('id', $item)->where('is_del', 0)->find();
                if ($cate) {
                    $cate = $cate->toArray();
                    $cate['special_count'] = 0;
                    array_push($list, $cate);
                }
            }
        }
        return $list;
    }

    public static function setWhere($where)
    {
        $model = self::order('sort desc,add_time desc')->where('is_del', 0);
        if ($where['name']) $model = $model->where('name', 'like', "%$where[name]%");
        if ($where['pid']) $model = $model->where('grade_id', $where['pid']);
        return $model;
    }

    public static function getSubjectAll()
    {
        return self::order('sort desc,add_time desc')->where(['is_show' => 1, 'is_del' => 0])->field('name,id')->select();
    }

    /**获取一个分类下的所有分类ID
     * @param int $pid
     */
    public static function categoryId($pid = 0)
    {
        $data = self::where('is_del', 0)->where('grade_id', $pid)->column('id');
        array_push($data, $pid);
        return $data;
    }
}
