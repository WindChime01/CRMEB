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

namespace app\admin\model\special;

use basic\ModelBasic;
use traits\ModelTrait;
use think\Db;
use app\admin\model\special\SpecialWatch;
use app\admin\model\special\SpecialBuy;
use app\admin\model\special\Special;
use service\PhpSpreadsheetService;

/**浏览记录
 * Class LearningRecords
 * @package app\admin\model\special
 */
class LearningRecords extends ModelBasic
{
    use ModelTrait;

    /**最后学习时间
     * @param $id
     * @param $uid
     * @return mixed
     */
    public static function lastStudyTime($id, $uid)
    {
        return self::where(['special_id' => $id, 'uid' => $uid])->value('add_time');
    }

    /**条件处理
     * @param $where
     */
    public static function getOrderWhere($where, $id)
    {
        $model = self::alias('l')->join('User u', 'l.uid=u.uid')
            ->join('Special s', 'l.special_id=s.id');
        if ($id) $model = $model->where('l.special_id', $id);
        if (isset($where['uid']) && $where['uid']) $model = $model->where('l.uid', $where['uid']);
        $model = $model->group('l.uid');
        if (isset($where['status']) && $where['status'] == 1) {
            $model = $model->join('SpecialBuy b', 'l.special_id=b.special_id and l.uid=b.uid')->where('b.is_del',0);
        }
        if (isset($where['data']) && $where['data'] != '') {
            $model = self::getModelTime($where, $model, 'l.add_time');
        }
        $model = $model->order('l.add_time desc')->field('l.uid,l.special_id,l.add_time,s.id,s.type,s.title,s.is_light,u.nickname,u.phone,u.level');
        return $model;
    }

    /**学习记录
     * @param $where
     * @param $id
     */
    public static function specialLearningRecordsLists($where, $id)
    {
        $model = self::getOrderWhere($where, $id);
        if (isset($where['excel']) && $where['excel'] == 1) {
            $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        } else {
            $data = ($data = $model->page((int)$where['page'], (int)$where['limit'])->select()) && count($data) ? $data->toArray() : [];
        }
        foreach ($data as $key => &$value) {
            $value['last_study_time'] = date('Y-m-d', $value['add_time']);
            if($where['excel'] == 1){
                $value['percentage'] = SpecialWatch::where(['uid'=>$value['uid'],'special_id'=>$value['special_id']])->avg('percentage');
            }
        }
        if (isset($where['excel']) && $where['excel'] == 1) {
            self::SaveExcel($data);
        }
        $count = self::getOrderWhere($where, $id)->count();
        return compact('count', 'data');
    }

    /**
     * 保存并下载excel
     * $list array
     * return
     */
    public static function SaveExcel($list)
    {
        $export = [];
        foreach ($list as $index => $item) {
            $export[] = [
                $item['uid'],
                $item['nickname'],
                $item['phone'],
                $item['level'] == 1 ? '会员' : '非会员',
                $item['title'],
                $item['percentage'].'%',
                $item['last_study_time']
            ];
        }
        $filename = '学习记录' . time() . '.xlsx';
        $head = ['UID', '昵称', '电话', '身份', '专题名称', '观看进度%', '最后学习时间'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }
}
