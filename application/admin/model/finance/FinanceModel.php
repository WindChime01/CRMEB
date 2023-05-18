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


namespace app\admin\model\finance;

use traits\ModelTrait;
use basic\ModelBasic;
use service\ExportService;
use app\admin\model\user\UserBill;
use app\admin\model\user\User;
use service\PhpSpreadsheetService;
use service\SystemConfigService;

/**数据统计处理
 * Class FinanceModel
 * @package app\admin\model\finance
 */
class FinanceModel extends ModelBasic
{
    protected $name = 'user_bill';

    use ModelTrait;

    const bill = 'user_bill';

    /**
     * 处理金额
     * @param $where
     * @return array
     */
    public static function systemPage($where)
    {
        $model = new self;
        //翻页
        $limit = $where['limit'];
        $offset = $where['offset'];
        $limit = $offset . ',' . $limit;
        //排序
        $order = '';
        if (!empty($where['sort']) && !empty($where['sortOrder'])) {
            $order = $where['sort'] . ' ' . $where['sortOrder'];
        }
        unset($where['limit']);
        unset($where['offset']);
        unset($where['sort']);
        unset($where['sortOrder']);
        if (!empty($where['add_time'])) {
            list($startTime, $endTime) = explode(' - ', $where['add_time']);
            $where['add_time'] = array('between', [strtotime($startTime), strtotime($endTime)]);
        } else {
            $where['add_time'] = array('between', [strtotime(date('Y/m') . '/01'), strtotime(date('Y/m') . '/' . date('t'))]);
        }
        if (empty($where['title'])) {
            unset($where['title']);
        }

        $total = $model->where($where)->count();
        $rows = $model->where($where)->order($order)->limit($limit)->select()->each(function ($e) {
            return $e['add_time'] = date('Y-m-d H:i:s', $e['add_time']);
        })->toArray();
        return compact('total', 'rows');
    }

    public static function getBillList($where)
    {
        $data = ($data = self::setWhereList($where)->page((int)$where['page'], (int)$where['limit'])->select()) && count($data) ? $data->toArray() : [];
        if ($data) {
            foreach ($data as &$v) {
                if ($where['category'] != 'brokerage_price') {
                    $v['category'] = self::get_category($v['category']);
                } else {
                    $v['category'] = '佣金';
                }
            }
        }
        $count = self::setWhereList($where)->count();
        return compact('data', 'count');
    }

    public static function get_category($category)
    {
        $gold_name = SystemConfigService::get("gold_name");
        switch ($category) {
            case 'now_money':
                $title = '余额';
                break;
            case 'gold_num':
                $title = $gold_name;
                break;
            case 'weixin':
                $title = '微信';
                break;
            case 'zhifubao':
                $title = '支付宝';
                break;
        }
        return $title;
    }

    public static function SaveExport($where)
    {
        $data = ($data = self::setWhereList($where)->select()) && count($data) ? $data->toArray() : [];
        $export = [];
        foreach ($data as $value) {
            $category = self::get_category($value['category']);
            $export[] = [
                $value['uid'],
                $value['nickname'],
                $category,
                $value['pm'] == 0 ? '-' . $value['number'] : $value['number'],
                $value['title'],
                $value['mark'],
                $value['add_time'],
            ];
        }
        if ($where['category'] == 'now_money') {
            $type = '余额';
        } else {
            $type = '金币';
        }
        $filename = '资金监控' . time() . '.xlsx';
        $head = ['用户ID', '昵称', '支付类型', $type, '账单标题', '备注', '创建时间'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }

    public static function setWhereList($where)
    {
        $time['data'] = '';
        if ($where['start_time'] != '' && $where['end_time'] != '') {
            $time['data'] = $where['start_time'] . ' - ' . $where['end_time'];
        }
        $model = self::getModelTime($time, self::alias('A')
            ->join('user B', 'B.uid=A.uid')
            ->order('A.add_time desc'), 'A.add_time');
        if ($where['category']) {
            $bill_where_op = self::bill_where_op($where['category']);
            if ($bill_where_op) {
                $model = $model->where('A.category', $bill_where_op['category']['op'], $bill_where_op['category']['condition']);
            }
        }
        if (trim($where['type']) != '') {
            $model = $model->where('A.type', $where['type']);
        } else {
            $model = $model->where('A.type', $bill_where_op['type']['op'], $bill_where_op['type']['condition']);
        }
        if ($where['nickname'] != '') {
            $model = $model->where('B.nickname|B.uid', 'like', "%$where[nickname]%");
        }
        $model = $model->where('A.number', '<>', 0);
        return $model->field(['A.*', 'FROM_UNIXTIME(A.add_time,"%Y-%m-%d %H:%i:%s") as add_time', 'B.uid', 'B.nickname', 'B.name']);
    }

    /**
     * @param $category
     * @return array|bool
     */
    public static function bill_where_op($category)
    {
        if (!$category || !in_array($category, ['now_money', 'gold_num', 'weixin', 'zhifubao', 'brokerage_price'])) {
            return false;
        }
        switch ($category) {
            case "now_money" :
                $bill_where_op['category']['op'] = 'in';
                $bill_where_op['category']['condition'] = 'now_money';
                $bill_where_op['type']['op'] = 'not in';
                $bill_where_op['type']['condition'] = 'extract,extract_fail,gain,deduction,sign,pay_vip,extract_success,brokerage,brokerage_return';
                break;
            case "brokerage_price" :
                $bill_where_op['category']['op'] = 'in';
                $bill_where_op['category']['condition'] = 'now_money';
                $bill_where_op['type']['op'] = 'in';
                $bill_where_op['type']['condition'] = 'extract,extract_fail,brokerage,brokerage_return';
                break;
            case "gold_num" :
                $bill_where_op['category']['op'] = 'in';
                $bill_where_op['category']['condition'] = 'gold_num';
                $bill_where_op['type']['op'] = 'in';
                $bill_where_op['type']['condition'] = 'sign,recharge,live_reward,gain,return';
                break;
            case "weixin" :
                $bill_where_op['category']['op'] = 'in';
                $bill_where_op['category']['condition'] = 'weixin';
                $bill_where_op['type']['op'] = 'in';
                $bill_where_op['type']['condition'] = 'pay_product,pay_vip,recharge,pay_sign_up,pay_goods,pay_test_paper,pay_data_download,pay_product_refund,pay_data_download_refund,pay_test_paper_refund,user_recharge_refund,pay_sign_up_refund';
                break;
            case "zhifubao" :
                $bill_where_op['category']['op'] = 'in';
                $bill_where_op['category']['condition'] = 'zhifubao';
                $bill_where_op['type']['op'] = 'in';
                $bill_where_op['type']['condition'] = 'pay_product,pay_vip,recharge,pay_sign_up,pay_goods,pay_test_paper,pay_data_download,pay_product_refund,pay_data_download_refund,pay_test_paper_refund,user_recharge_refund,pay_sign_up_refund';
                break;
        }
        return $bill_where_op;
    }

    /**
     * 获取营业数据
     */
    public static function getOrderInfo($where)
    {
        $orderinfo = self::getTimeWhere($where)
            ->field('sum(total_price) total_price,sum(cost) cost,sum(pay_postage) pay_postage,sum(pay_price) pay_price,sum(coupon_price) coupon_price,sum(deduction_price) deduction_price,from_unixtime(pay_time,\'%Y-%m-%d\') pay_time')->order('pay_time')->group('from_unixtime(pay_time,\'%Y-%m-%d\')')->select()->toArray();
        $price = 0;
        $postage = 0;
        $deduction = 0;
        $coupon = 0;
        $cost = 0;
        foreach ($orderinfo as $info) {
            $price = bcadd($price, $info['total_price'], 2);//应支付
            $postage = bcadd($postage, $info['pay_postage'], 2);//邮费
            $deduction = bcadd($deduction, $info['deduction_price'], 2);//抵扣
            $coupon = bcadd($coupon, $info['coupon_price'], 2);//优惠券
            $cost = bcadd($cost, $info['cost'], 2);//成本
        }
        return compact('orderinfo', 'price', 'postage', 'deduction', 'coupon', 'cost');
    }

    /**
     * 处理where条件
     */
    public static function statusByWhere($status, $model = null)
    {
        if ($model == null) $model = new self;
        if ('' === $status)
            return $model;
        else if ($status == 'weixin')//微信支付
            return $model->where('pay_type', 'weixin');
        else if ($status == 'yue')//余额支付
            return $model->where('pay_type', 'yue');
        else if ($status == 'offline')//线下支付
            return $model->where('pay_type', 'offline');
        else
            return $model;
    }

    public static function getTimeWhere($where, $model = null)
    {
        return self::getTime($where)->where('paid', 1)->where('refund_status', 0);
    }

    /**
     * 获取时间区间
     */
    public static function getTime($where, $model = null, $prefix = 'add_time')
    {
        if ($model == null) $model = new self;
        if ($where['data'] == '') {
            switch ($where['date']) {
                case 'today':
                case 'week':
                case 'month':
                case 'year':
                    $model = $model->whereTime($prefix, $where['date']);
                    break;
                case 'quarter':
                    list($startTime, $endTime) = User::getMonth('n');
                    $model = $model->where($prefix, '>', strtotime($startTime));
                    $model = $model->where($prefix, '<', strtotime($endTime));
                    break;
            }
        } else {
            list($startTime, $endTime) = explode(' - ', $where['data']);
            $model = $model->where($prefix, '>', strtotime($startTime));
            $model = $model->where($prefix, '<', strtotime($endTime));
        }
        return $model;
    }

    /**
     * 获取新增消费
     */
    public static function getConsumption($where)
    {
        $consumption = self::getTime($where, new UserBill, 'b.add_time')->alias('a')->join('user b', 'a.uid = b.uid')
            ->field('sum(a.number) number')
            ->where('a.type', 'pay_product')->find()->toArray();
        return $consumption;
    }

    /**
     * 获取普通商品数
     */
    public static function getOrdinary($where)
    {
        $ordinary = self::getTimeWhere($where)->where('pink_id', 'eq', 0)->where('seckill_id', 'eq', '0')->sum('pay_price');
        return $ordinary;
    }

    /**
     * 获取用户充值
     */
    public static function getRecharge($where)
    {
        $Recharge = self::getTime($where, new UserBill)->where('type', 'system_add')->where('category', 'now_money')->sum('number');
        return $Recharge;
    }

    /**
     * 获取推广金
     */
    public static function getExtension($where)
    {
        $brokerage = self::getTime($where, new UserBill)->where('type', 'brokerage')->where('category', 'now_money')->sum('number');
        $brokerage_return = self::getTime($where, new UserBill)->where('type', 'brokerage_return')->where('category', 'now_money')->sum('number');
        $extension = bcsub($brokerage, $brokerage_return, 2);
        return $extension;
    }

    /**
     * 最近交易
     */
    public static function trans()
    {
        $trans = self::alias('a')
            ->join('user b', 'a.uid=b.uid')
            ->join('store_order_cart_info c', 'a.id=c.oid')
            ->join('store_product d', 'c.product_id=d.id')
            ->field('b.nickname,a.pay_price,d.store_name')
            ->order('a.add_time DESC')
            ->limit('6')
            ->select()->toArray();
        return $trans;
    }
}
