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

namespace app\merchant\model\download;

use traits\ModelTrait;
use basic\ModelBasic;
use app\merchant\model\system\Relation;
use app\merchant\model\download\DataDownload;
use app\merchant\model\order\DataDownloadOrder;
use app\merchant\model\special\Special;
use app\merchant\model\special\SpecialSource;

/**
 * 获得资料 Model
 */
class DataDownloadBuy extends ModelBasic
{
    use ModelTrait;


    /**购买专题获得资料
     * @param $order_id
     * @param $uid
     * @param $special_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function setDataDownload($order_id, $uid, $special_id, $type = 0)
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
                $data_ids = Relation::setWhere(4, $task_special['id'])->column('relation_id');
                if (count($data_ids) <= 0) continue;
                foreach ($data_ids as $ks => $value) {
                    self::setUserDataDownload($order_id, $value, $uid, $type);
                }
            }
        } else {
            $data_ids = Relation::setWhere(4, $special_id)->column('relation_id');
            if (count($data_ids) <= 0) return false;
            foreach ($data_ids as $kf => $value) {
                self::setUserDataDownload($order_id, $value, $uid, $type);
            }
        }
    }

    /** 用户获得资料
     * @param $order_id
     * @param $data_id
     * @param $uid
     * @param $type
     * @return bool|object
     */
    public static function setUserDataDownload($order_id, $data_id, $uid, $type)
    {
        $add_time = time();
        if (self::be(['uid' => $uid, 'data_id' => $data_id, 'type' => $type, 'is_del' => 0])) return false;
        return self::set(compact('order_id', 'data_id', 'uid', 'type', 'add_time'));
    }

    /**删除专题时清除资料
     * @param $order_id
     * @param $uid
     * @param $special_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function delDataDownload($order_id, $uid, $special_id, $type)
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
                $data_ids = Relation::setWhere(4, $task_special['id'])->column('relation_id');
                if (count($data_ids) <= 0) continue;
                foreach ($data_ids as $ks => $value) {
                    self::delUserDataDownload($order_id, $value, $uid, $type);
                }
            }
        } else {
            $data_ids = Relation::setWhere(4, $special_id)->column('relation_id');
            if (count($data_ids) <= 0) return false;
            foreach ($data_ids as $kf => $value) {
                self::delUserDataDownload($order_id, $value, $uid, $type);
            }
        }
    }

    /** 清除资料
     * @param $order_id
     * @param $data_id
     * @param $uid
     * @param $type
     * @return bool|object
     */
    public static function delUserDataDownload($order_id, $data_id, $uid, $type)
    {
        if (!self::be(['order_id' => $order_id, 'uid' => $uid, 'data_id' => $data_id, 'type' => $type, 'is_del' => 0])) return false;
        return self::where(['order_id' => $order_id, 'uid' => $uid, 'data_id' => $data_id, 'type' => $type, 'is_del' => 0])->update(['is_del' => 1]);
    }
}
