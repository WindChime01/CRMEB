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

namespace app\admin\model\educational;

use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\educational\Teacher;

/**
 * 班级管理 Model
 * Class Classes
 * @package app\admin\model\educational
 */
class Classes extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where)
    {
        $model = self::order('sort desc,add_time desc')->where('is_del', 0);
        if ($where['status'] != '') $model = $model->where('status', $where['status']);
        if ($where['title'] != '') $model = $model->where('title', 'like', "%$where[title]%");
        return $model;
    }

    /**试题列表
     * @param $where
     */
    public static function getClassesLists($where)
    {
        $data = self::setWhere($where)->page($where['page'], $where['limit'])->select();
        foreach ($data as $key => &$value) {
            if ($value['status'] == 1) {
                $value['status'] = '开班';
            } else {
                $value['status'] = '结班';
            }
            if ($value['teacher_id']) {
                $title = Teacher::where('id', 'in', explode(',', $value['teacher_id']))->column('name');
                $value['teacher'] = implode(',', $title);
            } else {
                $value['teacher'] = '请在右侧操作中关联老师';
            }
            $value['start_time'] = date('Y-m-d H:i:s', $value['start_time']);
            $value['end_time'] = date('Y-m-d H:i:s', $value['end_time']);
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**
     * 全部班级
     */
    public static function classesList()
    {
        $list = self::order('sort desc,add_time desc')->where(['is_del' => 0])->field('id,title')->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        return $list;
    }

}
