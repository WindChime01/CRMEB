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

namespace app\admin\model\order;

use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\download\DataDownload;
use app\admin\model\user\UserBill;
use service\PhpSpreadsheetService;

/**
 * 资料订单 model
 * Class DataDownloadOrder
 */
class DataDownloadOrder extends ModelBasic
{
    use ModelTrait;

    public static function orderCount()
    {
        $data['wz'] = self::statusByWhere(0, new self(), '')->count();
        $data['wf'] = self::statusByWhere(1, new self(), '')->count();
        $data['tk'] = self::statusByWhere(-1, new self(), '')->count();
        $data['yt'] = self::statusByWhere(-2, new self(), '')->count();
        return $data;
    }

    public static function statusByWhere($status, $model = null, $alert = '')
    {
        if ($model == null) $model = new self;
        $model = $model->where($alert . 'is_system_del', 0);
        if ('' === $status)
            return $model;
        else if ($status == 0)//未支付
            return $model->where($alert . 'paid', 0)->where($alert . 'status', 0)->where($alert . 'refund_status', 0);
        else if ($status == 1)//已支付
            return $model->where($alert . 'paid', 1)->where($alert . 'status', 0)->where($alert . 'refund_status', 0);
        else if ($status == -1)//退款中
            return $model->where($alert . 'paid', 1)->where($alert . 'refund_status', 1);
        else if ($status == -2)//已退款
            return $model->where($alert . 'paid', 1)->where($alert . 'refund_status', 2);
        else
            return $model;
    }

    public static function getBadge($where)
    {
        $price = self::getOrderPrice($where);
        return [
            [
                'name' => '订单数量',
                'field' => '件',
                'count' => $price['order_sum'],
                'background_color' => 'layui-bg-blue',
                'col' => 3
            ],
            [
                'name' => '出售资料',
                'field' => '套',
                'count' => $price['total_num'],
                'background_color' => 'layui-bg-blue',
                'col' => 3
            ],
            [
                'name' => '订单金额',
                'field' => '元',
                'count' => $price['pay_price'],
                'background_color' => 'layui-bg-blue',
                'col' => 3
            ],
            [
                'name' => '退款金额',
                'field' => '元',
                'count' => $price['refund_price'],
                'background_color' => 'layui-bg-blue',
                'col' => 3
            ],
            [
                'name' => '微信支付金额',
                'field' => '元',
                'count' => $price['pay_price_wx'],
                'background_color' => 'layui-bg-blue',
                'col' => 3
            ],
            [
                'name' => '余额支付金额',
                'field' => '元',
                'count' => $price['pay_price_yue'],
                'background_color' => 'layui-bg-blue',
                'col' => 3
            ],
            [
                'name' => '支付宝支付金额',
                'field' => '元',
                'count' => $price['pay_price_zhifubao'],
                'background_color' => 'layui-bg-blue',
                'col' => 3
            ]
        ];
    }

    /**
     * 处理订单金额
     * @param $where
     * @return array
     */
    public static function getOrderPrice($where)
    {
        $price = array();
        $price['pay_price'] = 0;//支付金额
        $price['refund_price'] = 0;//退款金额
        $price['pay_price_wx'] = 0;//微信支付金额
        $price['pay_price_yue'] = 0;//余额支付金额
        $price['pay_price_zhifubao'] = 0;//支付宝支付金额
        $price['pay_price_other'] = 0;//其他支付金额
        $list = self::getOrderWhere($where, self::alias('a')->join('user r', 'r.uid=a.uid', 'LEFT'), 'a.', 'r')->field([
            'sum(a.total_num) as total_num',
            'sum(a.pay_price) as pay_price',
            'sum(a.refund_price) as refund_price'])->find()->toArray();
        $price['total_num'] = $list['total_num'];//资料总数
        $price['pay_price'] = $list['pay_price'];//支付金额
        $price['refund_price'] = $list['refund_price'];//退款金额
        $list = self::getOrderWhere($where, self::alias('a')->join('user r', 'r.uid=a.uid', 'LEFT'), 'a.', 'r')->field('sum(a.pay_price) as pay_price,a.pay_type')->group('a.pay_type')->select()->toArray();
        foreach ($list as $v) {
            if ($v['pay_type'] == 'weixin') {
                $price['pay_price_wx'] = $v['pay_price'];
            } elseif ($v['pay_type'] == 'yue') {
                $price['pay_price_yue'] = $v['pay_price'];
            } elseif ($v['pay_type'] == 'zhifubao') {
                $price['pay_price_zhifubao'] = $v['pay_price'];
            } else {
                $price['pay_price_other'] = $v['pay_price'];
            }
        }
        $price['order_sum'] = self::getOrderWhere($where, self::alias('a')->join('user r', 'r.uid=a.uid', 'LEFT'), 'a.', 'r')->count();
        return $price;
    }

    /**
     * 处理where条件
     * @param $where
     * @param $model
     * @return mixed
     */
    public static function getOrderWhere($where, $model, $aler = '', $join = '')
    {
        if ($where['status'] != '') {
            $model = self::statusByWhere($where['status'], $model, $aler);
        } else {
            $model = $model->where($aler . 'is_system_del', 0);
        }
        if ($where['real_name'] != '') {
            $model = $model->where($aler . 'order_id|' . $aler . 'uid' . ($join ? '|' . $join . '.nickname|' . $join . '.uid|' . $join . '.phone' : ''), 'LIKE', "%$where[real_name]%");
        }
        if ($where['data'] !== '') {
            $model = self::getModelTime($where, $model, $aler . 'add_time');
        }
        return $model;
    }

    public static function orderList($where)
    {
        $model = self::getOrderWhere($where, self::alias('a')->join('user r', 'r.uid=a.uid', 'LEFT'), 'a.', 'r')->field('a.*,r.nickname,r.phone');
        if ($where['order'] != '') {
            $model = $model->order(self::setOrder($where['order']));
        } else {
            $model = $model->order('a.id desc');
        }
        if (isset($where['excel']) && $where['excel'] == 1) {
            $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        } else {
            $data = ($data = $model->page((int)$where['page'], (int)$where['limit'])->select()) && count($data) ? $data->toArray() : [];
        }
        foreach ($data as &$item) {
            $item['_info'] = DataDownload::where('id', $item['data_id'])->find();
            $item['order_name'] = '[资料订单]';
            $item['color'] = '#895612';
            if ($item['paid'] == 1) {
                switch ($item['pay_type']) {
                    case 'weixin':
                        $item['pay_type_name'] = '微信支付';
                        break;
                    case 'yue':
                        $item['pay_type_name'] = '余额支付';
                        break;
                    case 'zhifubao':
                        $item['pay_type_name'] = '支付宝支付';
                        break;
                    default:
                        $item['pay_type_name'] = '其他支付';
                        break;
                }
            } else {
                switch ($item['pay_type']) {
                    case 'offline':
                        $item['pay_type_name'] = '线下支付';
                        $item['pay_type_info'] = 1;
                        break;
                    default:
                        $item['pay_type_name'] = '未支付';
                        break;
                }
            }
            if ($item['paid'] == 0 && $item['status'] == 0) {
                $item['status_name'] = '未支付';
            } else if ($item['paid'] == 1 && $item['status'] == 0 && $item['refund_status'] == 0) {
                $item['status_name'] = '已支付';
            } else if ($item['paid'] == 1 && $item['refund_status'] == 1) {
                $item['status_name'] = <<<HTML
<b style="color:#f124c7">申请退款</b><br/>
HTML;
            } else if ($item['paid'] == 1 && $item['refund_status'] == 2) {
                $item['status_name'] = '已退款';
            }
            if ($item['paid'] == 0 && $item['status'] == 0 && $item['refund_status'] == 0) {
                $item['_status'] = 1;
            } else if ($item['paid'] == 1 && $item['status'] == 0 && $item['refund_status'] == 0) {
                $item['_status'] = 2;
            } else if ($item['paid'] == 1 && $item['refund_status'] == 1) {
                $item['_status'] = 3;
            } else if ($item['paid'] == 1 && $item['refund_status'] == 2) {
                $item['_status'] = 7;
            }
        }
        if (isset($where['excel']) && $where['excel'] == 1) {
            self::SaveExcel($data);
        }
        $count = self::getOrderWhere($where, self::alias('a')->join('user r', 'r.uid=a.uid', 'LEFT'), 'a.', 'r')->count();
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
            $_info = DataDownload::where('id', $item['data_id'])->find();
            if ($_info) {
                $goodsName = $_info['title'] . '| ' . $_info['money'];
            } else {
                $goodsName = '资料被删除';
            }
            if ($item['paid'] == 0 && $item['status'] == 0) {
                $item['status_name'] = '未支付';
            } else if ($item['paid'] == 1 && $item['status'] == 0 && $item['refund_status'] == 0) {
                $item['status_name'] = '已支付';
            } else if ($item['paid'] == 1 && $item['refund_status'] == 1) {
                $item['status_name'] = '退款中';
            } else if ($item['paid'] == 1 && $item['refund_status'] == 2) {
                $item['status_name'] = '已退款';
            }
            $export[] = [
                $item['order_id'], $item['pay_type_name'],
                $item['total_num'], $item['total_price'], $item['pay_price'], $item['refund_price'],
                $goodsName,
                $item['paid'] == 1 ? '已支付' : '未支付' . '/支付时间: ' . ($item['pay_time'] > 0 ? date('Y/m/d H:i', $item['pay_time']) : '暂无'),
                $item['status_name'],
                $item['nickname'] . '/' . $item['uid'],
                $item['phone']
            ];
        }
        $filename = '资料订单导出' . time() . '.xlsx';
        $head = ['订单号', '支付方式', '资料总数', '资料总价', '支付金额', '退款金额', '资料信息', '支付状态', '订单状态', '微信昵称/UID', '手机号'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }

}
