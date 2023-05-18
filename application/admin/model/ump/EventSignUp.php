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


namespace app\admin\model\ump;

use traits\ModelTrait;
use basic\ModelBasic;
use think\Db;
use app\admin\model\user\User;
use app\admin\model\ump\EventData as EventDataModel;
use service\PhpSpreadsheetService;

/**活动报名
 * Class EventSignUp
 * @package app\admin\model\ump
 */
class EventSignUp extends ModelBasic
{
    use ModelTrait;

    public static function orderCount()
    {
        $data['wz'] = self::statusByWhere('', new self(), '', '')->count();
        $data['wf'] = self::statusByWhere('', new self(), '', 1)->count();
        $data['yt'] = self::statusByWhere('', new self(), '', -2)->count();
        return $data;
    }

    public static function getUserSignUpAll($where)
    {
        $model = self::getOrderWhere($where, self::alias('s')->join('user r', 'r.uid=s.uid', 'LEFT'), 's.', 'r')->field('s.*,r.nickname,r.phone');
        $model = $model->order('s.add_time DESC');
        if (isset($where['excel']) && $where['excel'] >= 1) {
            $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        } else {
            $data = $model->page((int)$where['page'], (int)$where['limit'])->select();
            $data = count($data) ? $data->toArray() : [];
        }
        foreach ($data as &$v) {
            $v['addTime'] = date('Y-m-d H:i:s', $v['add_time']);
            $v['refund_reason_time'] = date('Y-m-d H:i:s', $v['refund_reason_time']);
            $event = EventRegistration::where('id', $v['activity_id'])->field('is_fill,title,image')->find();
            $v['order_name'] = '[活动订单]';
            $v['_info'] = $event;
            $v['color'] = '#895612';
            $v['is_fill'] = $event['is_fill'];
            $v['number'] = $v['number'] <= 0 ? 1 : $v['number'];
            if ($v['pay_type'] == 'weixin') {
                $v['pay_type'] = '微信支付';
            } elseif ($v['pay_type'] == 'zhifubao') {
                $v['pay_type'] = '支付宝支付';
            } elseif ($v['pay_type'] == 'yue') {
                $v['pay_type'] = '余额支付';
            } else {
                $v['pay_type'] = '其他支付';
            }
            if ($v['status']) {
                $v['write_off'] = '已核销';
            } else {
                $v['write_off'] = '未核销';
            }
            if ($v['user_info'] && $v['is_fill']) {
                $user_info = json_decode($v['user_info']);
                if (is_array($user_info)) {
                    $userInfo = '';
                    foreach ($user_info as $ks => $item) {
                        if(is_array($item->event_value)) $item->event_value=implode(',',$item->event_value);
                        $userInfo .= '<b >' . $item->event_name . ':' . $item->event_value . '</b><br/>';
                    }
                    $v['userInfo'] = $userInfo;
                } else {
                    if ($user_info->sex == 1) {
                        $sex = '男';
                    } elseif ($user_info->sex == 2) {
                        $sex = '女';
                    } else {
                        $sex = '保密';
                    }
                    $v['userInfo'] = <<<HTML
                    <b >姓名：$user_info->name</b><br/>
                    <b >电话：$user_info->phone</b><br/>
                    <b >性别：$sex</b><br/>
                    <b >年龄：$user_info->age</b><br/>
                    <b >公司：$user_info->company</b><br/>
                    <b >备注：$user_info->remarks</b><br/>
HTML;
                }
            } else {
                $v['userInfo'] = '无';
            }
            if ($v['paid'] == 0) {
                $v['status_name'] = '未支付';
            } else if ($v['paid'] == 1 && $v['refund_status'] == 0) {
                $v['status_name'] = '已支付';
            } else if ($v['paid'] == 1 && $v['refund_status'] == 2) {
                $v['status_name'] = '已退款';
            }
            if ($v['paid'] == 0 && $v['refund_status'] == 0) {
                $v['_status'] = 1;
            } else if ($v['paid'] == 1 && $v['refund_status'] == 0) {
                $v['_status'] = 2;
            } else if ($v['paid'] == 1 && $v['refund_status'] == 2) {
                $v['_status'] = 7;
            }
        }
        if (isset($where['excel']) && $where['excel'] == 1) {
            self::SaveExcel1($data);
        }
        if (isset($where['excel']) && $where['excel'] == 2) {
            self::SaveExcel2($data);
        }
        $count = self::getOrderWhere($where, self::alias('s')->join('user r', 'r.uid=s.uid', 'LEFT'), 's.', 'r')->count();
        return compact('data', 'count');
    }

    public static function statusByWhere($status, $model = null, $alert = '', $type)
    {
        if ($model == null) $model = new self;
        switch ($type) {
            case 1:
                $model = $model->where($alert . 'refund_status', 0);
                break;
            case -2:
                $model = $model->where($alert . 'refund_status', 2);
                break;
        }
        $model = $model->where($alert . 'is_del', 0);
        if ('' === $status)
            return $model->where($alert . 'paid', 1);
        else if ($status == 1)//已支付 未核销
            return $model->where($alert . 'paid', 1)->where($alert . 'status', 0);
        else if ($status == 2)//已支付 已核销
            return $model->where($alert . 'paid', 1)->where($alert . 'status', 1);
        else
            return $model->where($alert . 'paid', 1);
    }

    /**
     * 保存并下载excel
     * $list array
     * return
     */
    public static function SaveExcel1($list)
    {
        $export = [];
        foreach ($list as $index => $item) {
            $title = $item['_info']['title'];
            if ($item['user_info'] && $item['is_fill']) {
                $user_info = json_decode($item['user_info']);
                if (is_array($user_info)) {
                    $userInfo = '';
                    foreach ($user_info as $ks => $value) {
                        $userInfo .= $value->event_name . ':' . $value->event_value . "\n";
                    }
                } else {
                    if ($user_info->sex == 1) {
                        $sex = '男';
                    } elseif ($user_info->sex == 2) {
                        $sex = '女';
                    } else {
                        $sex = '保密';
                    }
                    $userInfo = '姓名：' . $user_info->name . "\n"
                        . '电话：' . $user_info->phone . "\n"
                        . '性别：' . $sex . "\n"
                        . '年龄：' . $user_info->age . "\n"
                        . '公司：' . $user_info->company . "\n"
                        . '备注：' . $user_info->remarks;
                }
            } else {
                $userInfo = '无';
            }
            $refund_status = '';
            if ($item['refund_status'] == 2) {
                $refund_status = '金额:' . $item['refund_price'] . '元/时间:' . $item['refund_reason_time'];
            }
            $export[] = [
                $item['order_id'],
                $title,
                $userInfo,
                $item['pay_type'],
                $item['pay_price'],
                $item['status'] == 1 ? '已核销' : '未核销',
                $item['addTime'],
                $item['status_name'],
                $refund_status
            ];
        }
        $filename = '活动报名导出' . time() . '.xlsx';
        $head = ['订单号', '活动标题', '报名信息', '支付方式', '支付金额', '核销状态', '报名时间', '订单状态', '退款金额'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }

    /**
     * 保存并下载excel
     * $list array
     * return
     */
    public static function SaveExcel2($list)
    {
        $export = [];
        foreach ($list as $index => $item) {
            $title = $item['_info']['title'];
            $refund_status = '';
            if ($item['refund_status'] == 2) {
                $refund_status = '金额:' . $item['refund_price'] . '元/时间:' . $item['refund_reason_time'];
            }
            $export[] = [
                $item['order_id'],
                $item['nickname'] . '/' . $item['uid'],
                $title . '/' . $item['activity_id'],
                $item['pay_type'],
                $item['pay_price'],
                $item['addTime'],
                $item['status_name'],
                $refund_status
            ];
        }
        $filename = '报名订单导出' . time() . '.xlsx';
        $head = ['订单号', '用户信息', '活动标题', '支付方式', '支付金额', '报名时间', '订单状态', '退款金额'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
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
                'col' => 2
            ],
            [
                'name' => '订单总金额',
                'field' => '元',
                'count' => $price['pay_price'],
                'background_color' => 'layui-bg-blue',
                'col' => 2
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
                'col' => 2
            ],
            [
                'name' => '余额支付金额',
                'field' => '元',
                'count' => $price['pay_price_yue'],
                'background_color' => 'layui-bg-blue',
                'col' => 2
            ],
            [
                'name' => '支付宝支付金额',
                'field' => '元',
                'count' => $price['pay_price_zhifubao'],
                'background_color' => 'layui-bg-blue',
                'col' => 2
            ]
        ];
    }

    /**
     * 处理where条件
     * @param $where
     * @param $model
     * @return mixed
     */
    public static function getOrderWhere($where, $model, $aler = '', $join = '')
    {
        $model = self::statusByWhere($where['status'], $model, $aler, $where['type']);
        if (isset($where['id']) && $where['id'] > 0) {
            $model = $model->where($aler . 'activity_id', $where['id']);
        }
        if (isset($where['real_name']) && $where['real_name'] != '') {
            $model = $model->where($aler . 'order_id|' . $aler . 'code|' . $aler . 'uid' . ($join ? '|' . $join . '.nickname|' . $join . '.uid|' . $join . '.phone' : ''), 'LIKE', "%$where[real_name]%");
        }
        if (isset($where['data']) && $where['data'] !== '') {
            $model = self::getModelTime($where, $model, $aler . 'add_time');
        }
        return $model;
    }

    /**
     * 处理订单金额
     * @param $where
     * @return array
     */
    public static function getOrderPrice($where)
    {
        $price = [];
        $price['pay_price'] = 0;//支付金额
        $price['pay_price_wx'] = 0;//微信支付金额
        $price['pay_price_yue'] = 0;//余额支付金额
        $price['pay_price_zhifubao'] = 0;//支付宝支付金额
        $price['refund_price'] = 0;//退款金额
        $list = self::getOrderWhere($where, self::alias('s')->join('user r', 'r.uid=s.uid', 'LEFT'), 's.', 'r')->field(['sum(s.pay_price) as pay_price', 'sum(s.refund_price) as refund_price'])->find()->toArray();
        $price['pay_price'] = $list['pay_price'];//支付金额
        $price['refund_price'] = $list['refund_price'];//退款金额
        $list = self::getOrderWhere($where, self::alias('s')->join('user r', 'r.uid=s.uid', 'LEFT'), 's.', 'r')->field('sum(s.pay_price) as pay_price,s.pay_type')->group('s.pay_type')->select()->toArray();
        foreach ($list as $v) {
            if ($v['pay_type'] == 'weixin') {
                $price['pay_price_wx'] = $v['pay_price'];
            } elseif ($v['pay_type'] == 'yue') {
                $price['pay_price_yue'] = $v['pay_price'];
            } elseif ($v['pay_type'] == 'zhifubao') {
                $price['pay_price_zhifubao'] = $v['pay_price'];
            }
        }
        $price['order_sum'] = self::getOrderWhere($where, self::alias('s')->join('user r', 'r.uid=s.uid', 'LEFT'), 's.', 'r')->count();
        return $price;
    }
}
