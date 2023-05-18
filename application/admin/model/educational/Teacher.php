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
use app\admin\model\educational\TeacherCategpry;

/**
 * 老师 Model
 * Class Teacher
 * @package app\admin\model\educational
 */
class Teacher extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where)
    {
        $model = self::order('sort desc,add_time desc')->where('is_del', 0);
        if ($where['pid']) $model = $model->where('pid', $where['pid']);
        if ($where['title'] != '') $model = $model->where('name|nickname|uid', 'like', "%$where[title]%");
        return $model;
    }

    /**老师列表
     * @param $where
     */
    public static function getTeacherLists($where)
    {
        $data = self::setWhere($where)->page($where['page'], $where['limit'])->select();
        foreach ($data as $key => &$value) {
            $value['cate'] = TeacherCategpry::where('id', $value['pid'])->value('title');
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }


}
