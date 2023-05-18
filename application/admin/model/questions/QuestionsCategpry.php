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
use app\admin\model\questions\Questions as QuestionsModel;

/**
 * 试题分类 Model
 * Class QuestionsCategpry
 * @package app\admin\model\questions
 */
class QuestionsCategpry extends ModelBasic
{
    use ModelTrait;

    /**
     * 全部试题分类
     */
    public static function taskCategoryAll($type = 0)
    {
        $model = self::where(['is_del' => 0, 'mer_id' => 0]);
        if ($type == 1) {
            $model = $model->where('pid', 0);
        }
        $list = $model->order('sort desc,add_time desc')->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        $list = Util::sortListTier($list);
        return $list;
    }

    /**
     * 试题分类列表
     */
    public static function getAllList($where)
    {
        $data = self::setWhere($where)->column('id,pid');
        $list = [];
        foreach ($data as $ket => $item) {
            $cate = self::where('id', $ket)->find();
            if ($cate) {
                $cate = $cate->toArray();
                if ($item > 0) {
                    $cate['sum'] = QuestionsModel::where(['is_del' => 0, 'mer_id' => 0])->where('pid', $ket)->count();
                } else {
                    $pids = self::categoryId($ket);
                    $cate['sum'] = QuestionsModel::where(['is_del' => 0, 'mer_id' => 0])->where('pid', 'in', $pids)->count();
                }
                array_push($list, $cate);
                unset($cate);
            }
            if ($item > 0 && !array_key_exists($item, $data)) {
                $cate = self::where('id', $item)->find();
                if ($cate) {
                    $cate = $cate->toArray();
                    $pids = self::categoryId($item);
                    $cate['sum'] = QuestionsModel::where(['is_del' => 0, 'mer_id' => 0])->where('pid', 'in', $pids)->count();
                    array_push($list, $cate);
                }
            }
        }
        return $list;
    }

    public static function setWhere($where)
    {
        $model = self::order('sort desc,add_time desc')->where(['is_del' => 0, 'mer_id' => 0]);
        if ($where['pid']) $model = $model->where('id', $where['pid']);
        if ($where['title'] != '') $model = $model->where('title', 'like', "%$where[title]%");
        return $model;
    }

    /**获取一个分类下的所有分类ID
     * @param int $pid
     */
    public static function categoryId($pid = 0)
    {
        $data = self::where(['is_del' => 0, 'mer_id' => 0])->where('pid', $pid)->column('id');
        array_push($data, $pid);
        return $data;
    }

}
