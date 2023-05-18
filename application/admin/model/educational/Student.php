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
use app\admin\model\educational\Classes;

/**
 * 学员 Model
 * Class Student
 * @package app\admin\model\educational
 */
class Student extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where)
    {
        $model = self::order('sort desc,add_time desc')->where('is_del', 0);
        if (isset($where['cid']) && $where['cid']) $model = $model->where('classes_id', $where['cid']);
        if (isset($where['title']) && $where['title'] != '') $model = $model->where('name|nickname|id', 'like', "%$where[title]%");
        return $model;
    }

    /**学员列表
     * @param $where
     */
    public static function getStudentLists($where)
    {
        $data = self::setWhere($where)->page($where['page'], $where['limit'])->select();
        foreach ($data as $key => &$value) {
            $value['title'] = Classes::where('id', $value['classes_id'])->value('title');
            $value['address'] = $value['province'] . $value['city'] . $value['district'] . $value['detail'];
            $value['sex'] = $value['sex'] == 1 ? '男' : '女';
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }


}
