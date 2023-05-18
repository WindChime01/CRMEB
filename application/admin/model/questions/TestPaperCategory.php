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

namespace app\admin\model\questions;

use traits\ModelTrait;
use basic\ModelBasic;
use service\UtilService as Util;
use app\admin\model\questions\TestPaper;

/**
 * 试卷分类 Model
 * Class TestPaperCategory
 * @package app\admin\model\questions
 */
class TestPaperCategory extends ModelBasic
{
    use ModelTrait;

    /**
     * 全部试卷分类
     */
    public static function taskCategoryAll($n = 0, $type = 1)
    {
        $model = self::where(['is_del' => 0, 'type' => $type]);
        if ($n == 1) {
            $model = $model->where('pid', 0);
        }
        $list = $model->order('sort desc,add_time desc')->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        $list = Util::sortListTier($list);
        return $list;
    }

    /**
     * 试卷分类列表
     */
    public static function getAllList($where, $type)
    {
        $data = self::setWhere($where, $type)->column('id,pid');
        $list = [];
        foreach ($data as $ket => $item) {
            $cate = self::where('id', $ket)->find();
            if ($cate) {
                $cate = $cate->toArray();
                if ($item > 0) {
                    $cate['sum'] = TestPaper::where('is_del', 0)->where('tid', $ket)->count();
                } else {
                    $pids = self::categoryId($ket);
                    $cate['sum'] = TestPaper::where('is_del', 0)->where('tid', 'in', $pids)->count();
                }
                array_push($list, $cate);
                unset($cate);
            }
            if ($item > 0 && !array_key_exists($item, $data)) {
                $cate = self::where('id', $item)->find();
                if ($cate) {
                    $cate = $cate->toArray();
                    $pids = self::categoryId($item);
                    $cate['sum'] = TestPaper::where('is_del', 0)->where('tid', 'in', $pids)->count();
                    array_push($list, $cate);
                }
            }
        }
        return $list;
    }

    public static function setWhere($where, $type)
    {
        $model = self::order('sort desc,add_time desc')->where(['is_del' => 0, 'type' => $type]);
        if ($where['tid']) $model = $model->where('id', $where['tid']);
        if ($where['title'] != '') $model = $model->where('title', 'like', "%$where[title]%");
        return $model;
    }

    /**获取一个分类下的所有分类ID
     * @param int $pid
     */
    public static function categoryId($pid = 0)
    {
        $data = self::where('is_del', 0)->where('pid', $pid)->column('id');
        array_push($data, $pid);
        return $data;
    }

}
