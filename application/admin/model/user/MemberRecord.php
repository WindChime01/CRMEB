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

use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\user\MemberCard as MemberCardMode;
use app\admin\model\user\MemberCardBatch;
use app\admin\model\order\StoreOrder;
use service\PhpSpreadsheetService;

/**
 * 会员设置 model
 * Class MemberRecord
 * @package app\admin\model\user
 */
class MemberRecord extends ModelBasic
{
    use ModelTrait;

    public static function getPurchaseRecordList($where)
    {
        $model = self::setWherePage(self::setWhere($where), $where, ['u.nickname', 'u.uid'], ['p.uid']);
        $model = $model->alias('p')->join('user u', 'p.uid=u.uid', 'left')->field('p.*,u.nickname')->order('p.add_time DESC');
        if (isset($where['excel']) && $where['excel'] == 1) {
            $list = $model->select();
        }else{
            $list = $model->page((int)$where['page'], (int)$where['limit'])->select();
        }
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as $key => &$item) {
            $item['overdue_time'] = date('Y-m-d H:i:s', $item['overdue_time']);
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            if ($item['type'] == 1) {
                $item['price'] = '无';
            }
            if (!$item['type']) {
                switch ($item['validity']) {
                    case 30:
                        $item['title'] = '月卡';
                        break;
                    case 90:
                        $item['title'] = '季卡';
                        break;
                    case 365:
                        $item['title'] = '年卡';
                        break;
                    case -1:
                        $item['title'] = '终身卡';
                        break;
                    default:
                        $item['title'] = '免费';
                }
            } else {
                $item['title'] = '卡密';
            }
            if (!$item['code']) {
                $item['code'] = '无';
            }
            if ($item['nickname']) {
                $item['uid'] = $item['nickname'] . '/' . $item['uid'];
            } else {
                $item['uid'] = '暂无昵称/' . $item['uid'];
            }
            if ($item['oid']) {
                $order = StoreOrder::where('id', $item['oid'])->where('type', 1)->field('pay_type')->find();
                if ($order['pay_type'] == 'yue') {
                    $item['source'] = '赠送';
                } else {
                    $item['source'] = '购买';
                }
            } else {
                $item['source'] = '会员卡';
            }
        }
        if (isset($where['excel']) && $where['excel'] == 1) {
            self::SaveExcel($list);
        }
        $count = self::setWherePage(self::setWhere($where), $where, ['u.nickname', 'u.uid'], ['p.uid'])->alias('p')->join('user u', 'p.uid=u.uid', 'left')->count();
        return ['count' => $count, 'data' => $list];
    }

    /**
     * 设置搜索条件
     *
     */
    public static function setWhere($where)
    {
        $model = new self;
        if ($where['title'] != '') {
            $model = $model->where('p.uid|u.nickname', 'like', "%$where[title]%");
        }
        if ($where['type'] != '') {
            switch ($where['type']) {
                case 1:
                    $model = $model->where('p.validity', 30)->where('p.type', 0);
                    break;
                case 2:
                    $model = $model->where('p.validity', 90)->where('p.type', 0);
                    break;
                case 3:
                    $model = $model->where('p.validity', 365)->where('p.type', 0);
                    break;
                case 4:
                    $model = $model->where('p.validity', '<', 0)->where('p.type', 0);
                    break;
                case 5:
                    $model = $model->where('p.type', 1);
                    break;
                case 6:
                    $model = $model->where('p.type', 0)->where('p.is_free', 1);
                    break;
            }

        }
        return $model;
    }

    public static function setMerWhere($uid)
    {
        $model = new self;
        $model = $model->where('uid', $uid);
        return $model;
    }

    public static function userOneRecord($uid = 0)
    {
        $model = self::setMerWhere($uid)->order('id desc');
        $data = $model->select();
        foreach ($data as $key => $item) {
            $item['overdue_time'] = date('Y-m-d H:i:s', $item['overdue_time']);
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            if ($item['type'] == 1) {
                $item['price'] = '无';
            }
            if (!$item['type']) {
                switch ($item['validity']) {
                    case 30:
                        $item['title'] = '月卡';
                        break;
                    case 90:
                        $item['title'] = '季卡';
                        break;
                    case 365:
                        $item['title'] = '年卡';
                        break;
                    case -1:
                        $item['title'] = '终身卡';
                        break;
                    default:
                        $item['title'] = '免费';
                }
            } else {
                $item['title'] = '卡密';
            }
            if (!$item['code']) {
                $item['code'] = '无';
            }
        }
        $count = self::setMerWhere($uid)->count();
        return ['count' => $count, 'data' => $data];
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
                $item['title'],
                $item['source'],
                $item['validity'],
                $item['price'],
                $item['code']
            ];
        }
        $filename = '会员记录导出' . time() . '.xlsx';
        $head = ['编号', '昵称/UID', '类别', '来源', '有效期/天', '优惠价', '卡号'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }

}
