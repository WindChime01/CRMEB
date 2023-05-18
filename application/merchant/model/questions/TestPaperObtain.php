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
use service\UtilService as Util;
use app\merchant\model\questions\Questions as QuestionsModel;
use app\merchant\model\questions\TestPaper as TestPaperModel;
use app\merchant\model\special\Special;
use app\merchant\model\special\SpecialSource;

/**
 * 获得试卷 Model
 * Class TestPaperObtain
 * @package app\merchant\model\questions
 */
class TestPaperObtain extends ModelBasic
{
    use ModelTrait;


    /**给用户单独发送试卷
     * @param $uid
     * @param $data
     * @return bool
     */
    public static function addUidSend($uid, $data)
    {
        foreach ($data as $key => $value) {
            $type = TestPaperModel::where(['id' => $value])->value('type');
            $item['uid'] = $uid;
            $item['test_id'] = $value;
            $item['type'] = $type;
            if (self::be($item)) continue;
            $item['source'] = 3;
            $item['add_time'] = time();
            self::set($item);
        }
        return true;
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
        if ($special['type'] == 5) {
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

    /**删除专题时清除试卷
     * @param $order_id
     * @param $uid
     * @param $special_id
     * @param $source
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function delTestPaper($order_id, $uid, $special_id, $source)
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
                    self::delUserTestPaper($order_id, $value, $uid, 2, $source);
                }
            }
        }
        $test_ids = Relation::setWhere(2, $special_id)->column('relation_id');
        if (count($test_ids) <= 0) return false;
        foreach ($test_ids as $kf => $value) {
            self::delUserTestPaper($order_id, $value, $uid, 2, $source);
        }
    }

    /**删除试卷
     * @param $order_id
     * @param $test_id
     * @param $uid
     * @param $type
     * @param $source
     * @return bool|object
     */
    public static function delUserTestPaper($order_id, $test_id, $uid, $type, $source)
    {
        if (!self::be(['order_id' => $order_id, 'uid' => $uid, 'test_id' => $test_id, 'type' => $type, 'is_del' => 0, 'source' => $source])) return false;
        return self::where(['order_id' => $order_id, 'uid' => $uid, 'test_id' => $test_id, 'is_del' => 0, 'type' => $type, 'source' => $source])->update(['is_del' => 1]);
    }

}
