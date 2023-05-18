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

namespace app\merchant\model\questions;

use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\questions\TestPaperCategory as TestPaperCategoryModel;
use app\merchant\model\questions\TestPaperObtain;

/**
 * 试卷列表 Model
 * Class TestPaper
 * @package app\merchant\model\questions
 */
class TestPaper extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where)
    {
        $model = self::order('sort desc,add_time desc')->where(['is_del' => 0]);
        if (isset($where['pid']) && $where['pid']) $model = $model->where('tid', $where['pid']);
        if (isset($where['type']) && $where['type']) $model = $model->where('type', $where['type']);
        if (isset($where['is_show']) && $where['is_show'] != '') $model = $model->where('is_show', $where['is_show']);
        if (isset($where['title']) && $where['title'] != '') $model = $model->where('title', 'like', "%$where[title]%");
        if (isset($where['mer_id']) && $where['mer_id']) $model = $model->where('mer_id', $where['mer_id']);
        return $model;
    }

    /**试卷列表
     * @param $where
     */
    public static function testPaperExercisesList($where)
    {
        $data = self::setWhere($where)->page($where['page'], $where['limit'])->select();
        foreach ($data as $key => &$value) {
            $value['cate'] = TestPaperCategoryModel::where('id', $value['tid'])->value('title');
            $value['types'] = $value['type'] == 1 ? '练习' : '考试';
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**试卷列表
     * @param $type
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function testPaperList($type,$mer_id)
    {
        return self::order('sort desc,add_time desc')->where(['is_del' => 0,'mer_id' => $mer_id, 'type' => $type])->field('id,title')
            ->select();
    }

    /**获取练习、试卷
     * @param $where
     */
    public static function testPaperLists($where, $source)
    {
        $data = self::setWhere($where)->where('id', 'not in', $source)->page($where['page'], $where['limit'])->select();
        $count = self::setWhere($where)->where('id', 'not in', $source)->count();
        return compact('data', 'count');
    }

    public static function getUserWhere($where)
    {
        return self::alias('t')->join('TestPaperObtain o', 't.id=o.test_id')
            ->where(['o.uid' => $where['uid'], 't.is_del' => 0, 'o.is_del' => 0, 'o.source' => 3])
            ->field('t.title,t.type,t.id');
    }

    /**已获得试卷
     * @param $where
     * @return array
     * @throws \think\Exception
     */
    public static function getUserTestPaperList($where)
    {
        $data = self::getUserWhere($where)->page($where['page'], $where['limit'])->select();
        foreach ($data as $key => &$value) {
            $value['types'] = $value['type'] == 1 ? '练习' : '考试';
        }
        $count = self::getUserWhere($where)->count();
        return compact('data', 'count');
    }

}
