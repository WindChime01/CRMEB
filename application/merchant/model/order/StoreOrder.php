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

namespace app\merchant\model\order;

use app\merchant\model\user\User;
use app\admin\model\user\UserBill;
use app\admin\model\wechat\WechatUser;
use traits\ModelTrait;
use basic\ModelBasic;
use service\WechatTemplateService;
use think\Url;
use think\Db;
use service\HookService;
use EasyWeChat\Core\Exception;
use service\SystemConfigService;
use app\wap\model\routine\RoutineTemplate;
use service\PhpSpreadsheetService;

/**
 * 订单管理Model
 * Class StoreOrder
 * @package app\merchant\model\order
 */
class StoreOrder extends ModelBasic
{
    use ModelTrait;


    public static function getOrderList($where)
    {
        $model = UserBill::where('u.uid', $where['uid'])->alias('u')->join('__STORE_ORDER__ a', 'a.id=u.link_id')
            ->where('u.category', 'now_money')->where('u.type', 'brokerage')
            ->where(['a.paid' => 1, 'a.is_gift' => 0, 'a.is_receive_gift' => 0])->order('a.add_time desc')->field('a.*');
        if ($where['start_date'] && $where['end_date']) $model = $model->where('a.add_time', 'between', [strtotime($where['start_date']), strtotime($where['end_date'])]);
        if ($where['excel']) {
            $list = $model->select();
            $excel = [];
            foreach ($list as $item) {
                $item['title'] = self::get_title($item);
                $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
                $item['nickname'] = self::getDb('user')->where('uid', $item['uid'])->value('nickname');
                $excel[] = [$item['add_time'], $item['order_id'], $item['nickname'], $item['title'], $item['pay_price']];
            }
            $filename = '直推订单导出' . time() . '.xlsx';
            $head = ['时间', '订单号', '用户名', '名称', '订单金额'];
            PhpSpreadsheetService::outdata($filename, $excel, $head);
        } else
            $list = $model->page((int)$where['page'], (int)$where['limit'])->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        foreach ($list as &$item) {
            $item['title'] = self::get_title($item);
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['nickname'] = self::getDb('user')->where('uid', $item['uid'])->value('nickname');
        }
        return $list;
    }

    public static function get_title($item)
    {
        switch ($item['type']) {
            case 0:
                return self::getDb('special')->where('id', $item['cart_id'])->value('title');
                break;
            case 1:
                return self::getDb('member_ship')->where('id', $item['member_id'])->value('title');
                break;
            case 2:
                $product_id = self::getDb('store_order_cart_info')->where('oid', $item['id'])->value('product_id');
                return self::getDb('store_product')->where('id', $product_id)->value('store_name');
                break;
        }
    }

    public static function orderCount($type = 0, $mer_id = 0)
    {
        $data['wz'] = self::statusByWhere(0, new self(), '', $type, $mer_id)->count();
        $data['wf'] = self::statusByWhere(1, new self(), '', $type, $mer_id)->count();
        $data['sh'] = self::statusByWhere(2, new self(), '', $type, $mer_id)->count();
        $data['pj'] = self::statusByWhere(3, new self(), '', $type, $mer_id)->count();
        $data['wc'] = self::statusByWhere(4, new self(), '', $type, $mer_id)->count();
        $data['pt'] = self::statusByWhere(5, new self(), '', $type, $mer_id)->count();
        $data['pu'] = self::statusByWhere(6, new self(), '', $type, $mer_id)->count();
        $data['lw'] = self::statusByWhere(7, new self(), '', $type, $mer_id)->count();
        $data['sp'] = self::statusByWhere(8, new self(), '', $type, $mer_id)->count();
        $data['vip'] = self::statusByWhere(9, new self(), '', $type, $mer_id)->count();
        $data['tk'] = self::statusByWhere(-1, new self(), '', $type, $mer_id)->count();
        $data['yt'] = self::statusByWhere(-2, new self(), '', $type, $mer_id)->count();
        return $data;
    }

    public static function OrderList($where)
    {
        $model = self::getOrderWhere($where, self::alias('a')->join('User r', 'r.uid=a.uid', 'LEFT'), 'a.', 'r')->field('a.*,r.nickname,r.phone');
        if ($where['order'] != '') {
            $model = $model->order(self::setOrder($where['order']));
        } else {
            $model = $model->order('a.id desc');
        }
        if (isset($where['mer_id']) && $where['mer_id']) $model->where('mer_id', $where['mer_id']);
        if (isset($where['excel']) && $where['excel'] == 1) {
            $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        } else {
            $data = ($data = $model->page((int)$where['page'], (int)$where['limit'])->select()) && count($data) ? $data->toArray() : [];
        }
        foreach ($data as &$item) {
            switch ($item['type']) {
                case 0:
                    $item['_info'] = db('special')->where('id', $item['cart_id'])->find();
                    if ($item['pink_id']) {
                        $item['pink_name'] = '[拼团订单]';
                        $item['color'] = '#895612';
                    } else if ($item['is_gift'] && !$item['pink_id']) {
                        $item['pink_name'] = '[送礼物订单]';
                        $item['color'] = '#895612';
                    } else if (!$item['is_gift'] && !$item['pink_id'] && $item['gift_order_id']) {
                        $item['pink_name'] = '[领礼物订单]';
                        $item['color'] = '#895612';
                    } else {
                        $item['pink_name'] = '[普通订单]';
                        $item['color'] = '#895612';
                    }
                    if (!$item['_info']) {
                        $item['_info']['title'] = '专题被删除';
                    } else {
                        $len = strlen($item['_info']['title']);
                        $item['_info']['title'] = $len > 16 ? mb_substr($item['_info']['title'], 0, 16) . '...' : $item['_info']['title'];
                    }
                    break;
                case 1:
                    $item['_info'] = db('member_ship')->where('id', $item['member_id'])->find();
                    $item['pink_name'] = '[会员订单]';
                    $item['color'] = '#895612';
                    if (!$item['_info']) $item['_info']['title'] = '会员被删除';
                    break;
                case 2:
                    $_info = Db::name('store_order_cart_info')->where('oid', $item['id'])->field('cart_info')->select();
                    foreach ($_info as $k => &$v) {
                        $cart_info = json_decode($v['cart_info'], true);
                        if (!isset($cart_info['productInfo'])) $cart_info['productInfo'] = [];
                        $len = strlen($cart_info['productInfo']['store_name']);
                        $cart_info['productInfo']['store_name'] = $len > 30 ? mb_substr($cart_info['productInfo']['store_name'], 0, 30) . '...' : $cart_info['productInfo']['store_name'];
                        $v['cart_info'] = $cart_info;
                        unset($cart_info);
                    }
                    $item['_info'] = $_info;
                    $item['pink_name'] = '[商品订单]';
                    $item['color'] = '#895612';
                    break;
            }
            if ($item['paid'] == 1) {
                switch ($item['pay_type']) {
                    case 'weixin':
                        $item['pay_type_name'] = '微信支付';
                        break;
                    case 'yue':
                        $item['pay_type_name'] = '余额支付';
                        break;
                    case 'offline':
                        $item['pay_type_name'] = '线下支付';
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
            } else if ($item['paid'] == 1 && $item['status'] == 0 && $item['refund_status'] == 0 && $item['type'] != 2) {
                $item['status_name'] = '已支付';
            } else if ($item['paid'] == 1 && $item['status'] == 0 && $item['refund_status'] == 0 && $item['type'] == 2) {
                $item['status_name'] = '待发货';
            } else if ($item['paid'] == 1 && $item['status'] == 1 && $item['refund_status'] == 0 && $item['type'] == 2) {
                $item['status_name'] = '待收货';
            } else if ($item['paid'] == 1 && $item['status'] == 2 && $item['refund_status'] == 0 && $item['type'] == 2) {
                $item['status_name'] = '待评价';
            } else if ($item['paid'] == 1 && $item['status'] == 3 && $item['refund_status'] == 0 && $item['type'] == 2) {
                $item['status_name'] = '已完成';
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
            } else if ($item['paid'] == 1 && $item['status'] == 1 && $item['refund_status'] == 0) {
                $item['_status'] = 4;
            } else if ($item['paid'] == 1 && $item['status'] == 2 && $item['refund_status'] == 0) {
                $item['_status'] = 5;
            } else if ($item['paid'] == 1 && $item['status'] == 3 && $item['refund_status'] == 0) {
                $item['_status'] = 6;
            } else if ($item['paid'] == 1 && $item['refund_status'] == 2) {
                $item['_status'] = 7;
            }
            $item['spread_name'] = '';
            $item['spread_name_two'] = '';
            if ($item['type'] == 0 || $item['type'] == 1) {
                if ($item['link_pay_uid']) {
                    $spread_name = User::where('uid', $item['link_pay_uid'])->value('nickname');
                    $item['spread_name'] = $spread_name ? $spread_name . '/' . $item['link_pay_uid'] : '无';
                    $spread_uid_two = User::where('uid', $item['link_pay_uid'])->value('spread_uid');
                    if ($spread_uid_two) {
                        $spread_name_two = User::where('uid', $spread_uid_two)->value('nickname');
                        $item['spread_name_two'] = $spread_name_two ? $spread_name_two . '/' . $spread_uid_two : '无';
                    } else {
                        $item['spread_name_two'] = '无';
                    }
                } else if ($item['spread_uid']) {
                    $spread_name = User::where('uid', $item['spread_uid'])->value('nickname');
                    $item['spread_name'] = $spread_name ? $spread_name . '/' . $item['spread_uid'] : '无';
                    $spread_uid_two = User::where('uid', $item['spread_uid'])->value('spread_uid');
                    if ($spread_uid_two) {
                        $spread_name_two = User::where('uid', $spread_uid_two)->value('nickname');
                        $item['spread_name_two'] = $spread_name_two ? $spread_name_two . '/' . $spread_uid_two : '无';
                    } else {
                        $item['spread_name_two'] = '无';
                    }
                } else {
                    $item['spread_name'] = '无';
                    $item['spread_name_two'] = '无';
                }
            } else {
                $item['spread_name'] = '不参与分销';
                $item['spread_name_two'] = '不参与分销';
            }
        }
        if (isset($where['excel']) && $where['excel'] == 1) {
            self::SaveExcel($data, $where['types']);
        }
        $count = self::getOrderWhere($where, self::alias('a')->join('user r', 'r.uid=a.uid', 'LEFT'), 'a.', 'r')->count();
        return compact('count', 'data');
    }

    /*
     * 保存并下载excel
     * $list array
     * return
     */
    public static function SaveExcel($list, $type)
    {
        $export = [];
        foreach ($list as $index => $item) {
            if ($item['paid'] == 0 && $item['status'] == 0) {
                $item['status_name'] = '未支付';
            } else if ($item['paid'] == 1 && $item['status'] == 0 && $item['refund_status'] == 0 && $item['type'] != 2) {
                $item['status_name'] = '已支付';
            } else if ($item['paid'] == 1 && $item['status'] == 0 && $item['refund_status'] == 0 && $item['type'] == 2) {
                $item['status_name'] = '待发货';
            } else if ($item['paid'] == 1 && $item['status'] == 1 && $item['refund_status'] == 0 && $item['type'] == 2) {
                $item['status_name'] = '待收货';
            } else if ($item['paid'] == 1 && $item['status'] == 2 && $item['refund_status'] == 0 && $item['type'] == 2) {
                $item['status_name'] = '已完成';
            } else if ($item['paid'] == 1 && $item['refund_status'] == 1) {
                $item['status_name'] = '退款中';
            } else if ($item['paid'] == 1 && $item['refund_status'] == 2) {
                $item['status_name'] = '已退款';
            }
            if ($type == 0) {
                $special = self::getDb('special')->where('id', $item['cart_id'])->field('title,money')->find();
                if ($special) {
                    $goodsName = $special['title'] . '| ' . $special['money'];
                } else {
                    $goodsName = '专题被删除';
                }
                $export[] = [
                    $item['order_id'], $item['pay_type_name'],
                    $item['total_num'], $item['total_price'], $item['pay_price'], $item['refund_price'],
                    $goodsName,
                    $item['spread_name'],
                    $item['spread_name_two'],
                    $item['paid'] == 1 ? '已支付' : '未支付' . '/支付时间: ' . ($item['pay_time'] > 0 ? date('Y/m/d H:i', $item['pay_time']) : '暂无'),
                    $item['status_name'],
                    $item['nickname'] . '/' . $item['uid'],
                    $item['phone']
                ];
            } else if ($type == 2) {
                $_info = Db::name('store_order_cart_info')->where('oid', $item['id'])->column('cart_info');
                $goodsName = [];
                foreach ($_info as $k => $v) {
                    $v = json_decode($v, true);
                    $goodsName = implode(
                        [$v['productInfo']['store_name'],
                            isset($v['productInfo']['attrInfo']) ? '(' . $v['productInfo']['attrInfo']['suk'] . ')' : '',
                            "[{$v['cart_num']} * {$v['truePrice']}]"
                        ], ' ');
                }
                $item['cartInfo'] = $_info;
                $export[] = [
                    $item['order_id'], $item['pay_type_name'],
                    $item['total_num'], $item['total_price'], $item['total_postage'], $item['pay_price'], $item['refund_price'],
                    $goodsName,
                    $item['spread_name'],
                    $item['spread_name_two'],
                    $item['paid'] == 1 ? '已支付' : '未支付' . '/支付时间: ' . ($item['pay_time'] > 0 ? date('Y/m/d H:i', $item['pay_time']) : '暂无'),
                    $item['status_name'],
                    $item['nickname'] . '/' . $item['uid'],
                    '收货人：' . $item['real_name'],
                    '联系电话：' . $item['user_phone'],
                    '收货地址：' . $item['user_address']
                ];

            } else if ($type == 1) {
                $_info = db('member_ship')->where('id', $item['member_id'])->find();
                if ($_info) {
                    $goodsName = $_info['title'] . '会员| ' . $_info['price'];
                } else {
                    $goodsName = '会员信息被删除';
                }
                $export[] = [
                    $item['order_id'], $item['pay_type_name'],
                    $item['total_price'], $item['pay_price'], $item['refund_price'],
                    $goodsName,
                    $item['spread_name'],
                    $item['spread_name_two'],
                    $item['paid'] == 1 ? '已支付' : '未支付' . '/支付时间: ' . ($item['pay_time'] > 0 ? date('Y/m/d H:i', $item['pay_time']) : '暂无'),
                    $item['status_name'],
                    $item['nickname'] . '/' . $item['uid'],
                    $item['phone']
                ];
            }
        }
        switch ($type) {
            case 0:
                $filename = '专题课程订单导出' . time() . '.xlsx';
                $head = ['订单号', '支付方式', '专题总数', '专题总价', '支付金额', '退款金额', '专题信息', '推广人', '推广人上级', '支付状态', '订单状态', '微信昵称/UID', '手机号'];
                break;
            case 1:
                $filename = '会员订单导出' . time() . '.xlsx';
                $head = ['订单号', '支付方式', '会员总价', '支付金额', '退款金额', '会员信息', '推广人', '推广人上级', '支付状态', '订单状态', '微信昵称/UID', '手机号'];
                break;
            case 2:
                $filename = '商品订单导出' . time() . '.xlsx';
                $head = ['订单号', '支付方式', '商品总数', '商品总价', '邮费', '支付金额', '退款金额', '商品信息', '推广人', '推广人上级', '支付状态', '订单状态', '收货人', '联系电话', '收货地址'];
                break;
        }
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }

    public static function statusByWhere($status, $model = null, $alert = '', $type = 0, $mer_id = 0)
    {
        if ($model == null) $model = new self;
        switch ($type) {
            case 0:
                $model = $model->where($alert . 'type', 0);
                break;
            case 1:
                $model = $model->where($alert . 'type', 1);
                break;
            case 2:
                $model = $model->where($alert . 'type', 2);
                break;
        }
        if ($mer_id) $model = $model->where($alert . 'mer_id', $mer_id);
        if ('' === $status)
            return $model;
        else if ($status == 0)//未支付
            return $model->where($alert . 'paid', 0)->where($alert . 'is_system_del', 0)->where($alert . 'status', 0)->where($alert . 'refund_status', 0);
        else if ($status == 1)//已支付 待发货
            return $model->where($alert . 'paid', 1)->where($alert . 'is_system_del', 0)->where($alert . 'status', 0)->where($alert . 'refund_status', 0);
        else if ($status == 2)//已支付 待收货
            return $model->where($alert . 'paid', 1)->where($alert . 'is_system_del', 0)->where($alert . 'status', 1)->where($alert . 'refund_status', 0);
        else if ($status == 3)//已支付 待评价
            return $model->where($alert . 'paid', 1)->where($alert . 'is_system_del', 0)->where($alert . 'status', 2)->where($alert . 'refund_status', 0);
        else if ($status == 4)//已支付 已完成
            return $model->where($alert . 'paid', 1)->where($alert . 'is_system_del', 0)->where($alert . 'status', 3)->where($alert . 'refund_status', 0);
        else if ($status == 5)//课程订单
            return $model->where($alert . 'combination_id', 0)->where($alert . 'is_system_del', 0)->where($alert . 'is_gift', 0);
        else if ($status == 6)// 拼团订单
            return $model->where($alert . 'combination_id', '>', 0)->where($alert . 'is_system_del', 0)->where($alert . 'is_gift', 0);
        else if ($status == 7)// 礼物订单
            return $model->where($alert . 'combination_id', 0)->where($alert . 'is_system_del', 0)->where($alert . 'is_gift', '>', 0);
        else if ($status == 8)//商品订单
            return $model->where($alert . 'combination_id', 0)->where($alert . 'is_system_del', 0)->where($alert . 'is_gift', 0);
        else if ($status == 9)//会员订单
            return $model->where($alert . 'combination_id', 0)->where($alert . 'is_system_del', 0)->where($alert . 'is_gift', 0);
        else if ($status == -1)//退款中
            return $model->where($alert . 'paid', 1)->where($alert . 'is_system_del', 0)->where($alert . 'refund_status', 1);
        else if ($status == -2)//已退款
            return $model->where($alert . 'paid', 1)->where($alert . 'is_system_del', 0)->where($alert . 'refund_status', 2);
        else
            return $model;
    }

    public static function timeQuantumWhere($startTime = null, $endTime = null, $model = null)
    {
        if ($model === null) $model = new self;
        if ($startTime != null && $endTime != null)
            $model = $model->where('add_time', '>', strtotime($startTime))->where('add_time', '<', strtotime($endTime));
        return $model;
    }

    public static function changeOrderId($orderId)
    {
        $ymd = substr($orderId, 2, 8);
        $key = substr($orderId, 16);
        return 'wx' . $ymd . date('His') . $key;
    }

    /**
     * 线下付款
     * @param $id
     * @return $this
     */
    public static function updateOffline($id)
    {
        $orderId = self::where('id', $id)->value('order_id');
        $res = self::where('order_id', $orderId)->update(['paid' => 1, 'pay_time' => time()]);
        return $res;
    }

    /**
     * 退款发送模板消息
     * @param $oid
     * $oid 订单id  key
     */
    public static function refundTemplate($data, $oid)
    {
        $order = self::where('id', $oid)->find();
        $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
        if ($wechat_notification_message == 1) {
            WechatTemplateService::sendTemplate(WechatUser::uidToOpenid($order['uid']), WechatTemplateService::ORDER_REFUND_STATUS, [
                'first' => '亲，您的订单已退款!',
                'keyword1' => $order['order_id'],
                'keyword2' => $data['refund_price'],
                'remark' => '请查看账单'
            ], Url::build('wap/special/order', ['uni' => $order['order_id']], true, true));
        } else {
            $dataAdmin['character_string7']['value'] = $order['order_id'];
            $dataAdmin['time8']['value'] = date('Y-m-d H:i:s', $order['add_time']);
            $dataAdmin['amount3']['value'] = $data['refund_price'];
            RoutineTemplate::sendOrderRefundSuccess($dataAdmin, $order['uid'], Url::build('wap/special/order', ['uni' => $order['order_id']], true, true));
        }
    }

    /**
     * 处理where条件
     * @param $where
     * @param $model
     * @return mixed
     */
    public static function getOrderWhere($where, $model, $aler = '', $join = '')
    {
        if (isset($where['status']) && $where['status'] != '') {
            $model = self::statusByWhere($where['status'], $model, $aler, $where['types']);
        } else {
            $model = $model->where($aler . 'is_system_del', 0);
        }
        if (isset($where['real_name']) && $where['real_name'] != '') {
            $model = $model->where($aler . 'order_id|' . $aler . 'real_name|' . $aler . 'user_phone' . ($join ? '|' . $join . '.nickname|' . $join . '.uid|' . $join . '.phone' : ''), 'LIKE', "%$where[real_name]%");
        }
        if (isset($where['mer_id']) && $where['mer_id']) {
            $model = $model->where($aler . 'mer_id', $where['mer_id']);
        }
        $model = $model->where($aler . 'type', $where['types']);
        if ($where['type'] != '') {
            switch ($where['type']) {
                case 5:
                    $model = $model->where($aler . 'combination_id', 0)->where($aler . 'is_gift', 0);
                    break;
                case 6:
                    $model = $model->where($aler . 'combination_id', '>', 0)->where($aler . 'is_gift', 0);
                    break;
                case 7:
                    $model = $model->where($aler . 'combination_id', 0)->where($aler . 'is_gift', '>', 0);
                    break;
                case 8:
                    $model = $model->where($aler . 'combination_id', 0)->where($aler . 'is_gift', 0);
                    break;
                case 9:
                    $model = $model->where($aler . 'combination_id', 0)->where($aler . 'is_gift', 0);
                    break;
            }
        }
        if ($where['data'] !== '') {
            $model = self::getModelTime($where, $model, $aler . 'add_time');
        }
        return $model;
    }

    public static function getBadge($where)
    {
        $price = self::getOrderPrice($where);
        switch ($where['types']) {
            case 0:
                $name = '售出专题';
                break;
            case 1:
                $name = '售出会员';
                break;
            case 2:
                $name = '售出商品';
                break;
        }
        return [
            [
                'name' => '订单数量',
                'field' => '件',
                'count' => $price['order_sum'],
                'background_color' => 'layui-bg-blue',
                'col' => 3
            ],
            [
                'name' => $name,
                'field' => '件',
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
        $price['pay_price_offline'] = 0;//线下支付金额
        $price['pay_price_zhifubao'] = 0;//支付宝支付金额
        $price['pay_price_other'] = 0;//其他支付金额

        $list = self::getOrderWhere($where, self::alias('a')->join('User r', 'r.uid=a.uid', 'LEFT'), 'a.', 'r')->field([
            'sum(a.total_num) as total_num',
            'sum(a.pay_price) as pay_price',
            'sum(a.refund_price) as refund_price'])->find()->toArray();
        $price['total_num'] = $list['total_num'];//商品总数
        $price['pay_price'] = $list['pay_price'];//支付金额
        $price['refund_price'] = $list['refund_price'];//退款金额
        $list = self::getOrderWhere($where, self::alias('a')->join('User r', 'r.uid=a.uid', 'LEFT'), 'a.', 'r')->field('sum(a.pay_price) as pay_price,a.pay_type')->group('a.pay_type')->select()->toArray();
        foreach ($list as $v) {
            if ($v['pay_type'] == 'weixin') {
                $price['pay_price_wx'] = $v['pay_price'];
            } elseif ($v['pay_type'] == 'yue') {
                $price['pay_price_yue'] = $v['pay_price'];
            } elseif ($v['pay_type'] == 'offline') {
                $price['pay_price_offline'] = $v['pay_price'];
            } elseif ($v['pay_type'] == 'zhifubao') {
                $price['pay_price_zhifubao'] = $v['pay_price'];
            } else {
                $price['pay_price_other'] = $v['pay_price'];
            }
        }
        $price['order_sum'] = self::getOrderWhere($where, self::alias('a')->join('User r', 'r.uid=a.uid', 'LEFT'), 'a.', 'r')->count();
        return $price;
    }

}
