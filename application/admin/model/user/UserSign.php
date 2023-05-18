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

namespace app\admin\model\user;

use service\PhpSpreadsheetService;
use traits\ModelTrait;
use basic\ModelBasic;


class UserSign extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where)
    {
        $model = self::alias('s')->join('User u', 's.uid=u.uid');
        if (isset($where['title']) && $where['title']) {
            $model = $model->where('s.uid|u.nickname', 'like', '%' . $where['title'] . '%');
        }
        return $model;
    }

    public static function getUserSignList($where)
    {
        $model = self::setWhere($where)->field('s.*,u.nickname')->order('s.add_time DESC');
        if (isset($where['excel']) && $where['excel'] == 1) {
            $data = $model->select();
        }else{
            $data = $model->page((int)$where['page'], (int)$where['limit'])->select();
        }
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
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
                $item['uid'],
                $item['nickname'],
                $item['title'],
                $item['balance'],
                $item['number'],
                $item['add_time']
            ];
        }
        $filename = '签到记录导出' . time() . '.xlsx';
        $head = ['编号', 'UID', '微信昵称', '标题', '金币余量', '明细数字', '签到时间'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }
}
