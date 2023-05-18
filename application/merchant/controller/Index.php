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

namespace app\merchant\controller;

use app\merchant\model\merchant\MerchantMenus;
use app\admin\model\system\SystemConfig;
use app\merchant\model\order\StoreOrder as StoreOrderModel;
use app\merchant\model\download\DataDownload as DownloadModel;
use app\merchant\model\order\DataDownloadOrder;
use app\merchant\model\order\TestPaperOrder;
use app\merchant\model\ump\EventSignUp;
use app\merchant\model\merchant\Merchant;
use service\JsonService;

/**
 * 首页控制器
 * Class Index
 * @package app\merchant\controller
 *
 */
class Index extends AuthController
{
    public function index()
    {
        //获取当前登录后台的管理员信息
        $this->assign([
            'menuList' => MerchantMenus::menuList(),
            'site_logo' => SystemConfig::getValue('site_logo'),
            'Auth_site_name' => SystemConfig::getValue('site_name')
        ]);
        return $this->fetch();
    }

    //后台首页内容
    public function main()
    {
        /*首页第一行统计*/
        $now_day = strtotime(date('Y-m-d'));//今日
        $pre_day = strtotime(date('Y-m-d', strtotime('-1 day')));//昨天时间戳
        $mer_id = $this->merchantId;

        //课程订单数->今日
        $now_day_order_p = StoreOrderModel::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0, 'type' => 0])->where('pay_time', 'gt', $now_day)->count();
        $pre_day_order_p = StoreOrderModel::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0, 'type' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->count();
        $_order_p = abs($now_day_order_p - $pre_day_order_p);
        $first_line['d_num'] = [
            'data' => $now_day_order_p ? $now_day_order_p : 0,
            'percent' => bcmul($pre_day_order_p > 0 ? bcdiv($_order_p, $pre_day_order_p, 2) : abs($now_day_order_p), 100, 2),
            'is_plus' => $now_day_order_p - $pre_day_order_p > 0 ? 1 : ($now_day_order_p - $pre_day_order_p == 0 ? -1 : 0)
        ];

        //课程交易额->今天
        $now_month_order_p = StoreOrderModel::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0, 'type' => 0])->where('pay_time', 'gt', $now_day)->value('sum(pay_price)');
        $pre_month_order_p = StoreOrderModel::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0, 'type' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->value('sum(pay_price)');
        $m_order_p = abs($now_month_order_p - $pre_month_order_p);
        $first_line['d_price'] = [
            'data' => $now_month_order_p > 0 ? $now_month_order_p : 0,
            'percent' => bcmul($pre_month_order_p > 0 ? bcdiv($m_order_p, $pre_month_order_p, 2) : abs($now_month_order_p), 100, 2),
            'is_plus' => $now_month_order_p - $pre_month_order_p > 0 ? 1 : ($now_month_order_p - $pre_month_order_p == 0 ? -1 : 0)
        ];

        //商品订单数->今日
        $now_day_order_p = StoreOrderModel::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0, 'type' => 2])->where('pay_time', 'gt', $now_day)->count();
        $pre_day_order_p = StoreOrderModel::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0, 'type' => 2])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->count();
        $_order_p = abs($now_day_order_p - $pre_day_order_p);
        $first_line['d_store_num'] = [
            'data' => $now_day_order_p ? $now_day_order_p : 0,
            'percent' => bcmul($pre_day_order_p > 0 ? bcdiv($_order_p, $pre_day_order_p, 2) : abs($now_day_order_p), 100, 2),
            'is_plus' => $now_day_order_p - $pre_day_order_p > 0 ? 1 : ($now_day_order_p - $pre_day_order_p == 0 ? -1 : 0)
        ];

        //商品交易额->今天
        $now_month_order_p = StoreOrderModel::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0, 'type' => 2])->where('pay_time', 'gt', $now_day)->value('sum(pay_price)');
        $pre_month_order_p = StoreOrderModel::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0, 'type' => 2])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->value('sum(pay_price)');
        $m_order_p = abs($now_month_order_p - $pre_month_order_p);
        $first_line['d_store_price'] = [
            'data' => $now_month_order_p > 0 ? $now_month_order_p : 0,
            'percent' => bcmul($pre_month_order_p > 0 ? bcdiv($m_order_p, $pre_month_order_p, 2) : abs($now_month_order_p), 100, 2),
            'is_plus' => $now_month_order_p - $pre_month_order_p > 0 ? 1 : ($now_month_order_p - $pre_month_order_p == 0 ? -1 : 0)
        ];

        //资料订单数->今日
        $now_day_order_p = DataDownloadOrder::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $now_day)->count();
        $pre_day_order_p = DataDownloadOrder::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->count();
        $_order_p = abs($now_day_order_p - $pre_day_order_p);
        $first_line['d_data_num'] = [
            'data' => $now_day_order_p ? $now_day_order_p : 0,
            'percent' => bcmul($pre_day_order_p > 0 ? bcdiv($_order_p, $pre_day_order_p, 2) : abs($now_day_order_p), 100, 2),
            'is_plus' => $now_day_order_p - $pre_day_order_p > 0 ? 1 : ($now_day_order_p - $pre_day_order_p == 0 ? -1 : 0)
        ];

        //资料交易额->今天
        $now_month_order_p = DataDownloadOrder::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $now_day)->value('sum(pay_price)');
        $pre_month_order_p = DataDownloadOrder::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->value('sum(pay_price)');
        $m_order_p = abs($now_month_order_p - $pre_month_order_p);
        $first_line['d_data_price'] = [
            'data' => $now_month_order_p > 0 ? $now_month_order_p : 0,
            'percent' => bcmul($pre_month_order_p > 0 ? bcdiv($m_order_p, $pre_month_order_p, 2) : abs($now_month_order_p), 100, 2),
            'is_plus' => $now_month_order_p - $pre_month_order_p > 0 ? 1 : ($now_month_order_p - $pre_month_order_p == 0 ? -1 : 0)
        ];

        //活动订单数->今日
        $now_day_order_p = EventSignUp::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $now_day)->count();
        $pre_day_order_p = EventSignUp::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->count();
        $_order_p = abs($now_day_order_p - $pre_day_order_p);
        $first_line['d_event_num'] = [
            'data' => $now_day_order_p ? $now_day_order_p : 0,
            'percent' => bcmul($pre_day_order_p > 0 ? bcdiv($_order_p, $pre_day_order_p, 2) : abs($now_day_order_p), 100, 2),
            'is_plus' => $now_day_order_p - $pre_day_order_p > 0 ? 1 : ($now_day_order_p - $pre_day_order_p == 0 ? -1 : 0)
        ];

        //活动交易额->今天
        $now_month_order_p = EventSignUp::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $now_day)->value('sum(pay_price)');
        $pre_month_order_p = EventSignUp::where(['mer_id' => $mer_id, 'paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->value('sum(pay_price)');
        $m_order_p = abs($now_month_order_p - $pre_month_order_p);
        $first_line['d_event_price'] = [
            'data' => $now_month_order_p > 0 ? $now_month_order_p : 0,
            'percent' => bcmul($pre_month_order_p > 0 ? bcdiv($m_order_p, $pre_month_order_p, 2) : abs($now_month_order_p), 100, 2),
            'is_plus' => $now_month_order_p - $pre_month_order_p > 0 ? 1 : ($now_month_order_p - $pre_month_order_p == 0 ? -1 : 0)
        ];
        //考试订单数->今日
        $now_day_order_p = TestPaperOrder::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $now_day)->count();
        $pre_day_order_p = TestPaperOrder::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->count();
        $_order_p = abs($now_day_order_p - $pre_day_order_p);
        $first_line['d_test_num'] = [
            'data' => $now_day_order_p ? $now_day_order_p : 0,
            'percent' => bcmul($pre_day_order_p > 0 ? bcdiv($_order_p, $pre_day_order_p, 2) : abs($now_day_order_p), 100, 2),
            'is_plus' => $now_day_order_p - $pre_day_order_p > 0 ? 1 : ($now_day_order_p - $pre_day_order_p == 0 ? -1 : 0)
        ];

        //考试交易额->今天
        $now_month_order_p = TestPaperOrder::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $now_day)->value('sum(pay_price)');
        $pre_month_order_p = TestPaperOrder::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->value('sum(pay_price)');
        $m_order_p = abs($now_month_order_p - $pre_month_order_p);
        $first_line['d_test_price'] = [
            'data' => $now_month_order_p > 0 ? $now_month_order_p : 0,
            'percent' => bcmul($pre_month_order_p > 0 ? bcdiv($m_order_p, $pre_month_order_p, 2) : abs($now_month_order_p), 100, 2),
            'is_plus' => $now_month_order_p - $pre_month_order_p > 0 ? 1 : ($now_month_order_p - $pre_month_order_p == 0 ? -1 : 0)
        ];
        $this->assign([
            'first_line' => $first_line,
        ]);
        return $this->fetch();
    }

    /**
     * 订单图表
     */
    public function orderchart($cycle = 'thirtyday', $type = 0)
    {
        $datalist = [];
        $mer_id = $this->merchantId;
        switch ($cycle) {
            case 'thirtyday':
                $datebefor = date('Y-m-d', strtotime('-30 day'));
                $dateafter = date('Y-m-d');
                //上期
                $pre_datebefor = date('Y-m-d', strtotime('-60 day'));
                $pre_dateafter = date('Y-m-d', strtotime('-30 day'));
                for ($i = -30; $i < 0; $i++) {
                    $datalist[date('m-d', strtotime($i . ' day'))] = date('m-d', strtotime($i . ' day'));
                }
                switch ($type) {
                    case '0':
                    case '2':
                        $order_list = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'type' => $type, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '3':
                        $order_list = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '4':
                        $order_list = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '5':
                        $order_list = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                }
                if (empty($order_list)) {
                    return JsonService::successful([]);
                }
                foreach ($order_list as $k => &$v) {
                    $order_list[$v['day']] = $v;
                }
                $cycle_list = [];
                foreach ($datalist as $dk => $dd) {
                    if (!empty($order_list[$dd])) {
                        $cycle_list[$dd] = $order_list[$dd];
                    } else {
                        $cycle_list[$dd] = ['count' => 0, 'day' => $dd, 'price' => ''];
                    }
                }
                $chartdata = [];
                $data = [];//临时
                $chartdata['yAxis']['maxnum'] = 0;//最大值数量
                $chartdata['yAxis']['maxprice'] = 0;//最大值金额
                foreach ($cycle_list as $k => $v) {
                    $data['day'][] = $v['day'];
                    $data['count'][] = $v['count'];
                    $data['price'][] = round($v['price'], 2);
                    if ($chartdata['yAxis']['maxnum'] < $v['count'])
                        $chartdata['yAxis']['maxnum'] = $v['count'];//日最大订单数
                    if ($chartdata['yAxis']['maxprice'] < $v['price'])
                        $chartdata['yAxis']['maxprice'] = $v['price'];//日最大金额
                }
                $chartdata['legend'] = ['订单金额', '订单数'];//分类
                $chartdata['xAxis'] = $data['day'];//X轴值
                $series = ['normal' => ['label' => ['show' => true, 'position' => 'top']]];
                $chartdata['series'][] = ['name' => $chartdata['legend'][0], 'type' => 'bar', 'itemStyle' => $series, 'data' => $data['price']];//分类1值
                $chartdata['series'][] = ['name' => $chartdata['legend'][1], 'type' => 'bar', 'itemStyle' => $series, 'data' => $data['count']];//分类2值
                //统计总数上期
                switch ($type) {
                    case '0':
                    case '2':
                        $pre_total = StoreOrderModel::where('add_time', 'between time', [$pre_datebefor, $pre_dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where(['is_del' => 0, 'paid' => 1, 'type' => $type, 'mer_id' => $mer_id])
                            ->find();
                        break;
                    case '3':
                        $pre_total = DataDownloadOrder::where('add_time', 'between time', [$pre_datebefor, $pre_dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])
                            ->find();
                        break;
                    case '4':
                        $pre_total = TestPaperOrder::where('add_time', 'between time', [$pre_datebefor, $pre_dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])
                            ->find();
                        break;
                    case '5':
                        $pre_total = EventSignUp::where('add_time', 'between time', [$pre_datebefor, $pre_dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])
                            ->find();
                        break;
                }
                if ($pre_total) {
                    $chartdata['pre_cycle']['count'] = [
                        'data' => $pre_total['count'] ?: 0
                    ];
                    $chartdata['pre_cycle']['price'] = [
                        'data' => $pre_total['price'] ?: 0
                    ];
                }
                //统计总数
                switch ($type) {
                    case '0':
                    case '2':
                        $total = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where(['is_del' => 0, 'paid' => 1, 'type' => $type, 'mer_id' => $mer_id])
                            ->find();
                        break;
                    case '3':
                        $total = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])
                            ->find();
                        break;
                    case '4':
                        $total = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where('is_del', 0)->where('paid', 1)
                            ->find();
                        break;
                    case '5':
                        $total = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])
                            ->find();
                        break;
                }
                if ($total) {
                    $cha_count = intval($pre_total['count']) - intval($total['count']);
                    $pre_total['count'] = $pre_total['count'] == 0 ? 1 : $pre_total['count'];
                    $chartdata['cycle']['count'] = [
                        'data' => $total['count'] ?: 0,
                        'percent' => round((abs($cha_count) / intval($pre_total['count']) * 100), 2),
                        'is_plus' => $cha_count > 0 ? -1 : ($cha_count == 0 ? 0 : 1)
                    ];
                    $cha_price = round($pre_total['price'], 2) - round($total['price'], 2);
                    $pre_total['price'] = $pre_total['price'] == 0 ? 1 : $pre_total['price'];
                    $chartdata['cycle']['price'] = [
                        'data' => $total['price'] ?: 0,
                        'percent' => round(abs($cha_price) / $pre_total['price'] * 100, 2),
                        'is_plus' => $cha_price > 0 ? -1 : ($cha_price == 0 ? 0 : 1)
                    ];
                }
                return JsonService::successful('ok', $chartdata);
                break;
            case 'week':
                $weekarray = array(['周日'], ['周一'], ['周二'], ['周三'], ['周四'], ['周五'], ['周六']);
                $datebefor = date('Y-m-d', strtotime('-2 monday', time()));
                $dateafter = date('Y-m-d', strtotime('-1 sunday', time()));
                switch ($type) {
                    case '0':
                    case '2':
                        $order_list = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'type' => $type, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '3':
                        $order_list = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '4':
                        $order_list = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '5':
                        $order_list = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                }
                //数据查询重新处理
                $new_order_list = [];
                foreach ($order_list as $k => $v) {
                    $new_order_list[$v['day']] = $v;
                }
                $now_datebefor = date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600));
                $now_dateafter = date('Y-m-d', strtotime("+1 day"));
                switch ($type) {
                    case '0':
                    case '2':
                        $now_order_list = StoreOrderModel::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'type' => $type, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '3':
                        $now_order_list = DataDownloadOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '4':
                        $now_order_list = TestPaperOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '5':
                        $now_order_list = EventSignUp::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                }
                //数据查询重新处理 key 变为当前值
                $new_now_order_list = [];
                foreach ($now_order_list as $k => $v) {
                    $new_now_order_list[$v['day']] = $v;
                }
                foreach ($weekarray as $dk => $dd) {
                    if (!empty($new_order_list[$dk])) {
                        $weekarray[$dk]['pre'] = $new_order_list[$dk];
                    } else {
                        $weekarray[$dk]['pre'] = ['count' => 0, 'day' => $weekarray[$dk][0], 'price' => '0'];
                    }
                    if (!empty($new_now_order_list[$dk])) {
                        $weekarray[$dk]['now'] = $new_now_order_list[$dk];
                    } else {
                        $weekarray[$dk]['now'] = ['count' => 0, 'day' => $weekarray[$dk][0], 'price' => '0'];
                    }
                }
                $chartdata = [];
                $data = [];//临时
                $chartdata['yAxis']['maxnum'] = 0;//最大值数量
                $chartdata['yAxis']['maxprice'] = 0;//最大值金额
                foreach ($weekarray as $k => $v) {
                    $data['day'][] = $v[0];
                    $data['pre']['count'][] = $v['pre']['count'];
                    $data['pre']['price'][] = round($v['pre']['price'], 2);
                    $data['now']['count'][] = $v['now']['count'];
                    $data['now']['price'][] = round($v['now']['price'], 2);
                    if ($chartdata['yAxis']['maxnum'] < $v['pre']['count'] || $chartdata['yAxis']['maxnum'] < $v['now']['count']) {
                        $chartdata['yAxis']['maxnum'] = $v['pre']['count'] > $v['now']['count'] ? $v['pre']['count'] : $v['now']['count'];//日最大订单数
                    }
                    if ($chartdata['yAxis']['maxprice'] < $v['pre']['price'] || $chartdata['yAxis']['maxprice'] < $v['now']['price']) {
                        $chartdata['yAxis']['maxprice'] = $v['pre']['price'] > $v['now']['price'] ? $v['pre']['price'] : $v['now']['price'];//日最大金额
                    }
                }
                $chartdata['legend'] = ['上周金额', '本周金额', '上周订单数', '本周订单数'];//分类
                $chartdata['xAxis'] = $data['day'];//X轴值
                $series = ['normal' => ['label' => ['show' => true, 'position' => 'top']]];
                $chartdata['series'][] = ['name' => $chartdata['legend'][0], 'type' => 'bar', 'itemStyle' => $series, 'data' => $data['pre']['price']];//分类1值
                $chartdata['series'][] = ['name' => $chartdata['legend'][1], 'type' => 'bar', 'itemStyle' => $series, 'data' => $data['now']['price']];//分类1值
                $chartdata['series'][] = ['name' => $chartdata['legend'][2], 'type' => 'line', 'itemStyle' => $series, 'data' => $data['pre']['count']];//分类2值
                $chartdata['series'][] = ['name' => $chartdata['legend'][3], 'type' => 'line', 'itemStyle' => $series, 'data' => $data['now']['count']];//分类2值

                //统计总数上期
                switch ($type) {
                    case '0':
                    case '2':
                        $pre_total = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'type' => $type, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '3':
                        $pre_total = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '4':
                        $pre_total = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '5':
                        $pre_total = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                }
                if ($pre_total) {
                    $chartdata['pre_cycle']['count'] = [
                        'data' => $pre_total['count'] ?: 0
                    ];
                    $chartdata['pre_cycle']['price'] = [
                        'data' => $pre_total['price'] ?: 0
                    ];
                }
                //统计总数
                switch ($type) {
                    case '0':
                    case '2':
                        $total = StoreOrderModel::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'type' => $type, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '3':
                        $total = DataDownloadOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '4':
                        $total = TestPaperOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '5':
                        $total = EventSignUp::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                }
                if ($total) {
                    $cha_count = intval($pre_total['count']) - intval($total['count']);
                    $pre_total['count'] = $pre_total['count'] == 0 ? 1 : $pre_total['count'];
                    $chartdata['cycle']['count'] = [
                        'data' => $total['count'] ?: 0,
                        'percent' => round((abs($cha_count) / intval($pre_total['count']) * 100), 2),
                        'is_plus' => $cha_count > 0 ? -1 : ($cha_count == 0 ? 0 : 1)
                    ];
                    $cha_price = round($pre_total['price'], 2) - round($total['price'], 2);
                    $pre_total['price'] = $pre_total['price'] == 0 ? 1 : $pre_total['price'];
                    $chartdata['cycle']['price'] = [
                        'data' => $total['price'] ?: 0,
                        'percent' => round(abs($cha_price) / $pre_total['price'] * 100, 2),
                        'is_plus' => $cha_price > 0 ? -1 : ($cha_price == 0 ? 0 : 1)
                    ];
                }
                return JsonService::successful('ok', $chartdata);
                break;
            case 'month':
                $weekarray = array('01' => ['1'], '02' => ['2'], '03' => ['3'], '04' => ['4'], '05' => ['5'], '06' => ['6'], '07' => ['7'], '08' => ['8'], '09' => ['9'], '10' => ['10'], '11' => ['11'], '12' => ['12'], '13' => ['13'], '14' => ['14'], '15' => ['15'], '16' => ['16'], '17' => ['17'], '18' => ['18'], '19' => ['19'], '20' => ['20'], '21' => ['21'], '22' => ['22'], '23' => ['23'], '24' => ['24'], '25' => ['25'], '26' => ['26'], '27' => ['27'], '28' => ['28'], '29' => ['29'], '30' => ['30'], '31' => ['31']);

                $datebefor = date('Y-m-01', strtotime('-1 month'));
                $dateafter = date('Y-m-d', strtotime(date('Y-m-01')));
                switch ($type) {
                    case '0':
                    case '2':
                        $order_list = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'type' => $type, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '3':
                        $order_list = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '4':
                        $order_list = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '5':
                        $order_list = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                }
                //数据查询重新处理
                $new_order_list = [];
                foreach ($order_list as $k => $v) {
                    $new_order_list[$v['day']] = $v;
                }
                $now_datebefor = date('Y-m-01');
                $now_dateafter = date('Y-m-d', strtotime("+1 day"));
                switch ($type) {
                    case '0':
                    case '2':
                        $now_order_list = StoreOrderModel::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'type' => $type, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '3':
                        $now_order_list = DataDownloadOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '4':
                        $now_order_list = TestPaperOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '5':
                        $now_order_list = EventSignUp::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                }
                //数据查询重新处理 key 变为当前值
                $new_now_order_list = [];
                foreach ($now_order_list as $k => $v) {
                    $new_now_order_list[$v['day']] = $v;
                }
                foreach ($weekarray as $dk => $dd) {
                    if (!empty($new_order_list[$dk])) {
                        $weekarray[$dk]['pre'] = $new_order_list[$dk];
                    } else {
                        $weekarray[$dk]['pre'] = ['count' => 0, 'day' => $weekarray[$dk][0], 'price' => '0'];
                    }
                    if (!empty($new_now_order_list[$dk])) {
                        $weekarray[$dk]['now'] = $new_now_order_list[$dk];
                    } else {
                        $weekarray[$dk]['now'] = ['count' => 0, 'day' => $weekarray[$dk][0], 'price' => '0'];
                    }
                }
                $chartdata = [];
                $data = [];//临时
                $chartdata['yAxis']['maxnum'] = 0;//最大值数量
                $chartdata['yAxis']['maxprice'] = 0;//最大值金额
                foreach ($weekarray as $k => $v) {
                    $data['day'][] = $v[0];
                    $data['pre']['count'][] = $v['pre']['count'];
                    $data['pre']['price'][] = round($v['pre']['price'], 2);
                    $data['now']['count'][] = $v['now']['count'];
                    $data['now']['price'][] = round($v['now']['price'], 2);
                    if ($chartdata['yAxis']['maxnum'] < $v['pre']['count'] || $chartdata['yAxis']['maxnum'] < $v['now']['count']) {
                        $chartdata['yAxis']['maxnum'] = $v['pre']['count'] > $v['now']['count'] ? $v['pre']['count'] : $v['now']['count'];//日最大订单数
                    }
                    if ($chartdata['yAxis']['maxprice'] < $v['pre']['price'] || $chartdata['yAxis']['maxprice'] < $v['now']['price']) {
                        $chartdata['yAxis']['maxprice'] = $v['pre']['price'] > $v['now']['price'] ? $v['pre']['price'] : $v['now']['price'];//日最大金额
                    }

                }
                $chartdata['legend'] = ['上月金额', '本月金额', '上月订单数', '本月订单数'];//分类
                $chartdata['xAxis'] = $data['day'];//X轴值
                //,'itemStyle'=>$series
                $series = ['normal' => ['label' => ['show' => true, 'position' => 'top']]];
                $chartdata['series'][] = ['name' => $chartdata['legend'][0], 'type' => 'bar', 'itemStyle' => $series, 'data' => $data['pre']['price']];//分类1值
                $chartdata['series'][] = ['name' => $chartdata['legend'][1], 'type' => 'bar', 'itemStyle' => $series, 'data' => $data['now']['price']];//分类1值
                $chartdata['series'][] = ['name' => $chartdata['legend'][2], 'type' => 'line', 'itemStyle' => $series, 'data' => $data['pre']['count']];//分类2值
                $chartdata['series'][] = ['name' => $chartdata['legend'][3], 'type' => 'line', 'itemStyle' => $series, 'data' => $data['now']['count']];//分类2值

                //统计总数上期
                switch ($type) {
                    case '0':
                    case '2':
                        $pre_total = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'type' => $type, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '3':
                        $pre_total = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '4':
                        $pre_total = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '5':
                        $pre_total = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                }
                if ($pre_total) {
                    $chartdata['pre_cycle']['count'] = [
                        'data' => $pre_total['count'] ?: 0
                    ];
                    $chartdata['pre_cycle']['price'] = [
                        'data' => $pre_total['price'] ?: 0
                    ];
                }
                //统计总数
                switch ($type) {
                    case '0':
                    case '2':
                        $total = StoreOrderModel::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'type' => $type, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '3':
                        $total = DataDownloadOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '4':
                        $total = TestPaperOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '5':
                        $total = EventSignUp::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                }
                if ($total) {
                    $cha_count = intval($pre_total['count']) - intval($total['count']);
                    $pre_total['count'] = $pre_total['count'] == 0 ? 1 : $pre_total['count'];
                    $chartdata['cycle']['count'] = [
                        'data' => $total['count'] ?: 0,
                        'percent' => round((abs($cha_count) / intval($pre_total['count']) * 100), 2),
                        'is_plus' => $cha_count > 0 ? -1 : ($cha_count == 0 ? 0 : 1)
                    ];
                    $cha_price = round($pre_total['price'], 2) - round($total['price'], 2);
                    $pre_total['price'] = $pre_total['price'] == 0 ? 1 : $pre_total['price'];
                    $chartdata['cycle']['price'] = [
                        'data' => $total['price'] ?: 0,
                        'percent' => round(abs($cha_price) / $pre_total['price'] * 100, 2),
                        'is_plus' => $cha_price > 0 ? -1 : ($cha_price == 0 ? 0 : 1)
                    ];
                }
                return JsonService::successful('ok', $chartdata);
                break;
            case 'year':
                $weekarray = array('01' => ['一月'], '02' => ['二月'], '03' => ['三月'], '04' => ['四月'], '05' => ['五月'], '06' => ['六月'], '07' => ['七月'], '08' => ['八月'], '09' => ['九月'], '10' => ['十月'], '11' => ['十一月'], '12' => ['十二月']);
                $datebefor = date('Y-01-01', strtotime('-1 year'));
                $dateafter = date('Y-12-31', strtotime('-1 year'));
                switch ($type) {
                    case '0':
                    case '2':
                        $order_list = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'type' => $type, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '3':
                        $order_list = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '4':
                        $order_list = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '5':
                        $order_list = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                }
                //数据查询重新处理
                $new_order_list = [];
                foreach ($order_list as $k => $v) {
                    $new_order_list[$v['day']] = $v;
                }
                $now_datebefor = date('Y-01-01');
                $now_dateafter = date('Y-m-d');
                switch ($type) {
                    case '0':
                    case '2':
                        $now_order_list = StoreOrderModel::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'type' => $type, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '3':
                        $now_order_list = DataDownloadOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '4':
                        $now_order_list = TestPaperOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '5':
                        $now_order_list = EventSignUp::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                }
                //数据查询重新处理 key 变为当前值
                $new_now_order_list = [];
                foreach ($now_order_list as $k => $v) {
                    $new_now_order_list[$v['day']] = $v;
                }
                foreach ($weekarray as $dk => $dd) {
                    if (!empty($new_order_list[$dk])) {
                        $weekarray[$dk]['pre'] = $new_order_list[$dk];
                    } else {
                        $weekarray[$dk]['pre'] = ['count' => 0, 'day' => $weekarray[$dk][0], 'price' => '0'];
                    }
                    if (!empty($new_now_order_list[$dk])) {
                        $weekarray[$dk]['now'] = $new_now_order_list[$dk];
                    } else {
                        $weekarray[$dk]['now'] = ['count' => 0, 'day' => $weekarray[$dk][0], 'price' => '0'];
                    }
                }
                $chartdata = [];
                $data = [];//临时
                $chartdata['yAxis']['maxnum'] = 0;//最大值数量
                $chartdata['yAxis']['maxprice'] = 0;//最大值金额
                foreach ($weekarray as $k => $v) {
                    $data['day'][] = $v[0];
                    $data['pre']['count'][] = $v['pre']['count'];
                    $data['pre']['price'][] = round($v['pre']['price'], 2);
                    $data['now']['count'][] = $v['now']['count'];
                    $data['now']['price'][] = round($v['now']['price'], 2);
                    if ($chartdata['yAxis']['maxnum'] < $v['pre']['count'] || $chartdata['yAxis']['maxnum'] < $v['now']['count']) {
                        $chartdata['yAxis']['maxnum'] = $v['pre']['count'] > $v['now']['count'] ? $v['pre']['count'] : $v['now']['count'];//日最大订单数
                    }
                    if ($chartdata['yAxis']['maxprice'] < $v['pre']['price'] || $chartdata['yAxis']['maxprice'] < $v['now']['price']) {
                        $chartdata['yAxis']['maxprice'] = $v['pre']['price'] > $v['now']['price'] ? $v['pre']['price'] : $v['now']['price'];//日最大金额
                    }
                }
                $chartdata['legend'] = ['去年金额', '今年金额', '去年订单数', '今年订单数'];//分类
                $chartdata['xAxis'] = $data['day'];//X轴值
                $series = ['normal' => ['label' => ['show' => true, 'position' => 'top']]];
                $chartdata['series'][] = ['name' => $chartdata['legend'][0], 'type' => 'bar', 'itemStyle' => $series, 'data' => $data['pre']['price']];//分类1值
                $chartdata['series'][] = ['name' => $chartdata['legend'][1], 'type' => 'bar', 'itemStyle' => $series, 'data' => $data['now']['price']];//分类1值
                $chartdata['series'][] = ['name' => $chartdata['legend'][2], 'type' => 'line', 'itemStyle' => $series, 'data' => $data['pre']['count']];//分类2值
                $chartdata['series'][] = ['name' => $chartdata['legend'][3], 'type' => 'line', 'itemStyle' => $series, 'data' => $data['now']['count']];//分类2值

                //统计总数上期
                switch ($type) {
                    case '0':
                    case '2':
                        $pre_total = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'type' => $type, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '3':
                        $pre_total = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '4':
                        $pre_total = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '5':
                        $pre_total = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                }
                if ($pre_total) {
                    $chartdata['pre_cycle']['count'] = [
                        'data' => $pre_total['count'] ?: 0
                    ];
                    $chartdata['pre_cycle']['price'] = [
                        'data' => $pre_total['price'] ?: 0
                    ];
                }
                //统计总数
                switch ($type) {
                    case '0':
                    case '2':
                        $total = StoreOrderModel::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'type' => $type, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '3':
                        $total = DataDownloadOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '4':
                        $total = TestPaperOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '5':
                        $total = EventSignUp::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where(['is_del' => 0, 'paid' => 1, 'mer_id' => $mer_id])->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                }
                if ($total) {
                    $cha_count = intval($pre_total['count']) - intval($total['count']);
                    $pre_total['count'] = $pre_total['count'] == 0 ? 1 : $pre_total['count'];
                    $chartdata['cycle']['count'] = [
                        'data' => $total['count'] ?: 0,
                        'percent' => round((abs($cha_count) / intval($pre_total['count']) * 100), 2),
                        'is_plus' => $cha_count > 0 ? -1 : ($cha_count == 0 ? 0 : 1)
                    ];
                    $cha_price = round($pre_total['price'], 2) - round($total['price'], 2);
                    $pre_total['price'] = $pre_total['price'] == 0 ? 1 : $pre_total['price'];
                    $chartdata['cycle']['price'] = [
                        'data' => $total['price'] ?: 0,
                        'percent' => round(abs($cha_price) / $pre_total['price'] * 100, 2),
                        'is_plus' => $cha_price > 0 ? -1 : ($cha_price == 0 ? 0 : 1)
                    ];
                }
                return JsonService::successful($chartdata);
                break;
            default:
                return JsonService::successful([]);
                break;
        }
    }

    /*
     * 删除附件
     * */
    public function delete_file($name = '')
    {
        if ($name == '') return JsonService::fail('缺少需要删除的资源');
        $name = $name[0] == '/' ? substr($name, 1) : $name;
        if (file_exists($name)) unlink($name);
        if (strstr($name, 's_') && ($name = str_replace('s_', '', $name))) file_exists($name) && unlink($name);
        return JsonService::successful('删除成功');
    }
}
