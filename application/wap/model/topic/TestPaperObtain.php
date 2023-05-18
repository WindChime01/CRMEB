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

namespace app\wap\model\topic;

use traits\ModelTrait;
use basic\ModelBasic;
use app\wap\model\special\Special;
use app\wap\model\special\SpecialSource;
use app\wap\model\topic\TestPaper;

/**
 * 获得试卷 Model
 */
class TestPaperObtain extends ModelBasic
{
    use ModelTrait;

    /**记录用户获取试卷
     * @param $order_id
     * @param $test_id
     * @param $uid
     * @param $type
     * @param $source
     * @return object
     */
    public static function setUserTestPaper($order_id, $test_id, $uid, $type, $source)
    {
        $add_time = time();
        if (self::be(['uid' => $uid, 'test_id' => $test_id, 'type' => $type, 'is_del' => 0])) return false;
        return self::set(compact('order_id', 'test_id', 'uid', 'type', 'source', 'add_time'));
    }

    /**判断是否获得试卷
     * @param $test_id
     * @param $uid
     * @param $type
     * @return bool
     * @throws \think\Exception
     */
    public static function PayTestPaper($test_id, $uid, $type)
    {
        return self::where(['uid' => $uid, 'test_id' => $test_id, 'type' => $type, 'is_del' => 0])->count() ? true : false;
    }

    /**购买专题获得试卷
     * @param $order_id
     * @param $uid
     * @param $special_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function setTestPaper($order_id, $uid, $special_id, $source)
    {
        if (!$uid || !$special_id) return false;
        $special = Special::PreWhere()->where(['id'=>$special_id])->find();
        if ($special['type'] == SPECIAL_COLUMN) {
            $special_source = SpecialSource::getSpecialSource($special['id']);
            if (!$special_source) return false;
            foreach ($special_source as $k => $v) {
                $task_special = Special::PreWhere()->where(['id'=>$v['source_id']])->find();
                if (!$task_special) continue;
                if ($task_special['is_show'] != 1) continue;
                $test_ids = Relation::setWhere(2, $task_special['id'])->column('relation_id');
                if (count($test_ids) <= 0) continue;
                foreach ($test_ids as $ks => $value) {
                    self::setUserTestPaper($order_id, $value, $uid, 2, $source);
                }
            }
        }
        $test_ids = Relation::setWhere(2, $special_id)->column('relation_id');
        if (count($test_ids) <= 0) return false;
        foreach ($test_ids as $kf => $value) {
            self::setUserTestPaper($order_id, $value, $uid, 2, $source);
        }
    }

    /**获取用户的试卷
     * @param $type
     * @param $uid
     */
    public static function getUserTestPaper($type, $uid, $page, $limit)
    {
        $list = self::alias('b')->join('TestPaper t', 'b.test_id=t.id')
            ->where(['b.type' => $type, 'b.uid' => $uid, 'b.is_del' => 0, 't.is_del' => 0, 't.is_show' => 1, 't.status' => 1])
            ->field('t.id,b.uid,b.test_id,b.type,b.source,b.number,b.add_time,t.title,t.image,t.item_number')
            ->page((int)$page, (int)$limit)->order('b.add_time DESC')
            ->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        return $list;
    }
}
