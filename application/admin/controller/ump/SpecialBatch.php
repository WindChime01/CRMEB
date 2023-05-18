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

namespace app\admin\controller\ump;

use app\admin\controller\AuthController;
use app\admin\model\ump\SpecialExchange;
use app\admin\model\ump\SpecialBatch as SpecialBatchModel;
use service\JsonService as Json;
use app\admin\model\special\Special;

/**
 * 专题兑换码管理控制器
 * Class SpecialBatch
 * @package app\admin\controller\ump
 */
class SpecialBatch extends AuthController
{
    public function index()
    {
        $list = Special::PreWhere()->field('id,title')->select();
        $this->assign(['activity_type' => 1, 'special' => $list]);
        return $this->fetch('batch_index');
    }

    public function specialList()
    {
        $list = Special::PreWhere()->field('id,title')->select();
        return Json::successful($list);
    }

    public function batch_list()
    {
        $where = parent::getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['title', ''],
            ['special_id', 0],
            ['page', 1],
            ['limit', 20]
        ]);
        $batch_list = SpecialBatchModel::getBatchList($where);
        return Json::successlayui($batch_list);
    }

    public function add_batch()
    {
        return $this->fetch();
    }

    public function save_batch()
    {
        $data = parent::postMore([
            ['title', ''],
            ['special_id', 0],
            ['total_num', 1],
            ['status', 0],
            ['remark', '']
        ]);
        if (!isset($data['special_id']) || $data['special_id'] <= 0 || !is_numeric($data['special_id'])) return Json::fail('请选择专题');
        if (!isset($data['total_num']) || $data['total_num'] <= 0 || !is_numeric($data['total_num'])) return Json::fail('制卡未填写或不合法');
        if ($data['total_num'] > 6000) return Json::fail('单次制卡数量最高不得超过6000张');
        try {
            SpecialBatchModel::beginTrans();
            $special_id = $data['special_id'];
            $data['add_time'] = time();
            $batch_id = SpecialBatchModel::addBatch($data);
            $batch_card = SpecialExchange::addCard($batch_id, $data['total_num'], $special_id);
            if ($batch_id && $batch_card) {
                $qrcodeUrl = SpecialExchange::qrcodes_url($special_id, 5);
                SpecialBatchModel::where('id', $batch_id)->update(['qrcode' => $qrcodeUrl]);
            }
            SpecialBatchModel::commitTrans();
            return Json::successful('添加成功');
        } catch (\Exception $e) {
            SpecialBatchModel::rollbackTrans();
            return Json::fail('添加失败');
        }
    }

    /**
     * 快速编辑
     * @param string $field 字段名
     * @param int $id 修改的主键
     * @param string value 修改后的值
     * @return json
     */
    public function set_value($field, $id, $value, $model_type)
    {
        if ($model_type == 'special_batch' && $field != 'remark') {
            $use = SpecialExchange::where('card_batch_id', $id)->where('use_uid', '>', 0)->count();
            if ($use) return Json::fail('此批次卡片已经在使用当中，无法进行此非法操作');
        }
        if ($model_type == 'special_exchange' && $id) {
            $card = SpecialExchange::where(['id' => $id, 'use_uid' => ['>', 0]])->find();
            if ($card) return Json::fail('此卡片已经在使用当中，无法进行此非法操作');
        }
        $res1 = true;
        if ($model_type == 'special_batch') {
            $res = SpecialBatchModel::saveFieldByWhere(['id' => $id], [$field => $value]);
            if ($res && $field == 'status') {
                $res1 = SpecialExchange::saveFieldByWhere(['card_batch_id' => $id], [$field => $value]);
            }
        } else {
            $res = SpecialExchange::saveFieldByWhere(['id' => $id], [$field => $value]);
        }
        if ($res && $res1)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**兑换码列表
     * @return mixed
     */
    public function card_index()
    {
        $data = parent::getMore([
            ['activity_type', 2],
            ['card_batch_id', 0],

        ]);
        $batch_list = SpecialBatchModel::getBatchAll([]);
        $this->assign([
            'activity_type' => $data['activity_type'],
            'card_batch_id' => $data['card_batch_id'],
            'batch_list' => $batch_list ? $batch_list->toArray() : []
        ]);
        return $this->fetch();
    }

    /**
     * 获取兑换码
     */
    public function card_list()
    {
        $card_batch_id = $this->request->param('card_batch_id', 0);
        $excel = $this->request->param('excel', 0);
        $where = parent::getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['exchange_code', ''],
            ['phone', ''],
            ['card_batch_id', 0],
            ['is_use', ''],
            ['is_status', ''],
            ['page', 1],
            ['limit', 20],
            ['excel', $excel],
        ]);
        $where['card_batch_id'] = $where['card_batch_id'] > 0 ? $where['card_batch_id'] : $card_batch_id;
        $card_list = SpecialExchange::getCardList($where);
        return Json::successlayui($card_list);
    }

    /**删除
     * @param int $id
     */
    public function delete($id = 0)
    {
        $res = SpecialBatchModel::delSpecialBatch($id);
        if (!$res)
            return Json::fail(SpecialBatchModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }
}
