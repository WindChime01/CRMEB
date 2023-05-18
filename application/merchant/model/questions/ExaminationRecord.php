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
use app\merchant\model\questions\TestPaperScoreGrade;
use service\PhpSpreadsheetService;

/**
 * 用户考试记录 Model
 * Class ExaminationRecord
 * @package app\merchant\model\questions
 */
class ExaminationRecord extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where)
    {
        $model = self::alias('r')->join('TestPaper t', 'r.test_id=t.id')
            ->join('User u', 'r.uid=u.uid')->order('r.add_time desc')->where(['r.is_submit' => 1, 't.is_del' => 0]);
        if (isset($where['test_id']) && $where['test_id']) $model = $model->where('r.test_id', $where['test_id']);
        if (isset($where['type']) && $where['type']) $model = $model->where('r.type', $where['type']);
        if (isset($where['uid']) && $where['uid']) $model = $model->where('r.uid', $where['uid']);
        if (isset($where['mer_id']) && $where['mer_id']) $model = $model->where('t.mer_id', $where['mer_id']);
        if (isset($where['title']) && $where['title'] != '') $model = $model->where('r.uid', 'like', "%$where[title]%");
        return $model;
    }

    /**用户答题结果
     * @param $where
     * @return array
     * @throws \think\Exception
     */
    public static function getExaminationRecord($where)
    {
        $model = self::setWhere($where)->field('r.id,r.test_id,r.type,r.uid,r.score,r.accuracy,t.title,u.nickname,r.add_time');
        if (isset($where['excel']) && $where['excel'] == 1) {
            $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        } else {
            $data = ($data = $model->page((int)$where['page'], (int)$where['limit'])->select()) && count($data) ? $data->toArray() : [];
        }
        foreach ($data as $key => &$value) {
            if ($where['type'] == 2) {
                $value['grade'] = TestPaperScoreGrade::getTestPaperScoreGrade($value['test_id'], $value['score']);
            }
            $value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
        }
        if (isset($where['excel']) && $where['excel'] == 1) {
            self::SaveExcel($data, $where['type']);
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }


    /**
     * 保存并下载excel
     * $list array
     * return
     */
    public static function SaveExcel($list, $type)
    {
        $export = [];
        $filename = '答题记录' . time() . '.xlsx';
        if ($type == 1) {
            $array = ['ID', 'UID', '昵称', '标题', '正确率%', '提交时间'];
            foreach ($list as $index => $item) {
                $export[] = [
                    $item['id'],
                    $item['uid'],
                    $item['nickname'],
                    $item['title'],
                    $item['accuracy'],
                    $item['add_time']
                ];
            }
            $filename = '练习记录' . time() . '.xlsx';
        } else if ($type == 2) {
            $array = ['ID', 'UID', '昵称', '标题', '分数', '正确率%', '分数等级', '提交时间'];
            foreach ($list as $index => $item) {
                $export[] = [
                    $item['id'],
                    $item['uid'],
                    $item['nickname'],
                    $item['title'],
                    $item['score'],
                    $item['accuracy'],
                    $item['grade'],
                    $item['add_time']
                ];
            }
            $filename = '考试记录' . time() . '.xlsx';
        }
        PhpSpreadsheetService::outdata($filename, $export, $array);
    }
}
