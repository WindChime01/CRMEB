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

namespace app\admin\model\questions;

use traits\ModelTrait;
use basic\ModelBasic;
use service\UtilService as Util;
use app\admin\model\questions\Certificate;
use app\admin\model\user\User;
use app\admin\model\special\Special;
use app\admin\model\questions\TestPaper;
use service\PhpSpreadsheetService;

/**
 * 证书获取记录 Model
 * Class CertificateRecord
 * @package app\admin\model\questions
 */
class CertificateRecord extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where)
    {
        $model = new self();
        $model = $model->alias('r')->join('Certificate c', 'c.id=r.cid')
            ->join('User u', 'u.uid=r.uid')->where('r.is_del', 0);
        if ($where['cid'] != 0) $model = $model->where(['r.cid' => $where['cid']]);
        if ($where['title'] != '') $model = $model->where('r.uid|r.nickname', 'like', "%$where[title]%");
        return $model;
    }

    /**证书获取记录列表
     * @param $where
     */
    public static function getCertificateRecordList($where)
    {
        $model = self::setWhere($where)->order('r.add_time desc')->field('r.*,c.title');
        if (isset($where['excel']) && $where['excel'] == 1) {
            $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        } else {
            $data = ($data = $model->page((int)$where['page'], (int)$where['limit'])->select()) && count($data) ? $data->toArray() : [];
        }
        foreach ($data as $key => &$value) {
            switch ($value['obtain']) {
                case 1:
                    $value['obtains'] = '课程';
                    $value['source'] = Special::where('id', $value['source_id'])->value('title');
                    break;
                case 2:
                    $value['obtains'] = '考试';
                    $value['source'] = TestPaper::where('id', $value['source_id'])->value('title');
                    break;
            }
            switch ($value['status']) {
                case 1:
                    $value['statu'] = '已获得';
                    break;
                case 0:
                    $value['statu'] = '已撤销';
                    break;
            }
            $value['uids'] = $value['nickname'] . '/' . $value['uid'];
            $value['addTime'] = date('Y-m-d H:i:s', $value['add_time']);
        }
        if (isset($where['excel']) && $where['excel'] == 1) {
            self::SaveExcel($data);
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
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
                $item['id'],
                $item['title'],
                $item['source'],
                $item['uids'],
                $item['obtains'],
                $item['statu'],
                $item['addTime']
            ];
        }
        $filename = '证书记录' . time() . '.xlsx';
        $head = ['编号', '证书标题', '来源', '昵称/UID', '获取方式', '状态', '获得时间'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }
}
