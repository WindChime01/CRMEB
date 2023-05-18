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

use basic\ModelBasic;
use traits\ModelTrait;
use think\Db;
use app\merchant\model\download\DataDownload;
use service\PhpSpreadsheetService;

/**资料下载
 * Class DataDownloadRecords
 * @package app\merchant\model\download
 */
class DataDownloadRecords extends ModelBasic
{
    use ModelTrait;


    /**条件处理
     * @param $where
     */
    public static function getOrderWhere($where, $id)
    {
        $model = self::alias('r')->join('User u', 'r.uid=u.uid', 'left')
            ->join('DataDownload d', 'r.data_id=d.id', 'left')
            ->where(['r.data_id' => $id, 'd.mer_id' => $where['mer_id']]);
        if ($where['data'] != '') {
            $model = self::getModelTime($where, $model, 'r.add_time');
        }
        $model = $model->order('r.add_time desc')->field('r.uid,r.data_id,r.add_time,r.update_time,r.number,d.id,d.mer_id,d.title,d.money,d.member_money,u.nickname,u.phone,u.level');
        return $model;
    }

    /**下载记录
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
            $value['last_study_time'] = $value['update_time'];
            $value['price'] = $value['level'] > 0 ? $value['member_money'] : $value['money'];
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
                $item['number'],
                $item['price'],
                $item['last_study_time']
            ];
        }
        $filename = '下载记录' . time() . '.xlsx';
        $head = ['UID', '昵称', '电话', '身份', '资料名称', '下载次数', '价格', '最后学习时间'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }
}
