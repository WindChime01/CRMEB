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

namespace app\admin\controller;

use app\admin\model\store\StoreProduct;
use app\admin\model\system\SystemConfig;
use app\admin\model\system\SystemMenus;
use app\admin\model\system\SystemRole;
use app\admin\model\order\StoreOrder as StoreOrderModel;//订单
use app\admin\model\user\UserExtract as UserExtractModel;//分销
use app\admin\model\user\MemberRecord as MemberRecordModel;//会员购买记录
use app\admin\model\user\User as UserModel;//用户
use app\admin\model\special\LearningRecords;
use app\admin\model\order\DataDownloadOrder;
use app\admin\model\order\TestPaperOrder;
use app\admin\model\user\UserRecharge;
use app\admin\model\ump\EventSignUp;
use FormBuilder\Json;
use service\JsonService;
use service\SystemConfigService;
use think\Config;
use think\Cache;
use think\DB;

/**
 * 首页控制器
 * Class Index
 * @package app\admin\controller
 *
 */
class Index extends AuthController
{
    public function index()
    {
        //获取当前登录后台的管理员信息
        $adminInfo = $this->adminInfo;
        $roles = explode(',', $adminInfo['roles']);
        $this->assign([
            'menuList' => SystemMenus::menuList(),
            'site_logo' => SystemConfigService::get('site_logo'),
            'Auth_site_name' => SystemConfigService::get('site_name'),
            'role_name' => SystemRole::where('id', $roles[0])->field('role_name')->find()
        ]);
        return $this->fetch();
    }

    //后台首页内容
    public function main()
    {
        /*首页第一行统计*/
        $now_day = strtotime(date('Y-m-d'));//今日
        $pre_day = strtotime(date('Y-m-d', strtotime('-1 day')));//昨天时间戳

        //新增学员->日
        $now_day_user = UserModel::where('add_time', 'gt', $now_day)->count();
        $pre_day_user = UserModel::where('add_time', 'gt', $pre_day)->where('add_time', 'lt', $now_day)->count();
        $pre_day_user = $pre_day_user ? $pre_day_user : 0;
        $_user = abs($now_day_user - $pre_day_user);
        $first_line['day'] = [
            'data' => $now_day_user ? $now_day_user : 0,
            'percent' => bcmul($pre_day_user > 0 ? bcdiv($_user, $pre_day_user, 3) : abs($now_day_user), 100, 2),
            'is_plus' => $now_day_user - $pre_day_user > 0 ? 1 : ($now_day_user - $pre_day_user == 0 ? -1 : 0)
        ];
        //学习次数->日
        $now_day_user = LearningRecords::where('add_time', 'egt', $now_day)->count();
        $pre_day_user = LearningRecords::where('add_time', 'egt', $pre_day)->where('add_time', 'lt', $now_day)->count();
        $pre_day_user = $pre_day_user ? $pre_day_user : 0;
        $_user = abs($now_day_user - $pre_day_user);
        $first_line['records'] = [
            'data' => $now_day_user ? $now_day_user : 0,
            'percent' => bcmul($pre_day_user > 0 ? bcdiv($_user, $pre_day_user, 3) : abs($now_day_user), 100, 2),
            'is_plus' => $now_day_user - $pre_day_user > 0 ? 1 : ($now_day_user - $pre_day_user == 0 ? -1 : 0)
        ];

        //课程订单数->今日
        $now_day_order_p = StoreOrderModel::where('paid', 1)->where(['is_del' => 0, 'type' => 0])->where('pay_time', 'gt', $now_day)->count();
        $pre_day_order_p = StoreOrderModel::where('paid', 1)->where(['is_del' => 0, 'type' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->count();
        $_order_p = abs($now_day_order_p - $pre_day_order_p);
        $first_line['d_num'] = [
            'data' => $now_day_order_p ? $now_day_order_p : 0,
            'percent' => bcmul($pre_day_order_p > 0 ? bcdiv($_order_p, $pre_day_order_p, 2) : abs($now_day_order_p), 100, 2),
            'is_plus' => $now_day_order_p - $pre_day_order_p > 0 ? 1 : ($now_day_order_p - $pre_day_order_p == 0 ? -1 : 0)
        ];

        //课程交易额->今天
        $now_month_order_p = StoreOrderModel::where('paid', 1)->where(['is_del' => 0, 'type' => 0])->where('pay_time', 'gt', $now_day)->value('sum(pay_price)');
        $pre_month_order_p = StoreOrderModel::where('paid', 1)->where(['is_del' => 0, 'type' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->value('sum(pay_price)');
        $m_order_p = abs($now_month_order_p - $pre_month_order_p);
        $first_line['d_price'] = [
            'data' => $now_month_order_p > 0 ? $now_month_order_p : 0,
            'percent' => bcmul($pre_month_order_p > 0 ? bcdiv($m_order_p, $pre_month_order_p, 2) : abs($now_month_order_p), 100, 2),
            'is_plus' => $now_month_order_p - $pre_month_order_p > 0 ? 1 : ($now_month_order_p - $pre_month_order_p == 0 ? -1 : 0)
        ];

        //商品订单数->今日
        $now_day_order_p = StoreOrderModel::where('paid', 1)->where(['is_del' => 0, 'type' => 2])->where('pay_time', 'gt', $now_day)->count();
        $pre_day_order_p = StoreOrderModel::where('paid', 1)->where(['is_del' => 0, 'type' => 2])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->count();
        $_order_p = abs($now_day_order_p - $pre_day_order_p);
        $first_line['d_store_num'] = [
            'data' => $now_day_order_p ? $now_day_order_p : 0,
            'percent' => bcmul($pre_day_order_p > 0 ? bcdiv($_order_p, $pre_day_order_p, 2) : abs($now_day_order_p), 100, 2),
            'is_plus' => $now_day_order_p - $pre_day_order_p > 0 ? 1 : ($now_day_order_p - $pre_day_order_p == 0 ? -1 : 0)
        ];

        //商品交易额->今天
        $now_month_order_p = StoreOrderModel::where('paid', 1)->where(['is_del' => 0, 'type' => 2])->where('pay_time', 'gt', $now_day)->value('sum(pay_price)');
        $pre_month_order_p = StoreOrderModel::where('paid', 1)->where(['is_del' => 0, 'type' => 2])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->value('sum(pay_price)');
        $m_order_p = abs($now_month_order_p - $pre_month_order_p);
        $first_line['d_store_price'] = [
            'data' => $now_month_order_p > 0 ? $now_month_order_p : 0,
            'percent' => bcmul($pre_month_order_p > 0 ? bcdiv($m_order_p, $pre_month_order_p, 2) : abs($now_month_order_p), 100, 2),
            'is_plus' => $now_month_order_p - $pre_month_order_p > 0 ? 1 : ($now_month_order_p - $pre_month_order_p == 0 ? -1 : 0)
        ];

        //新增会员->今日
        $now_day_order_p = UserModel::where('level', 1)->where('member_time', 'gt', $now_day)->count();
        $pre_day_order_p = UserModel::where('level', 1)->where('member_time', 'gt', $pre_day)->where('member_time', 'lt', $now_day)->count();
        $_order_p = abs($now_day_order_p - $pre_day_order_p);
        $first_line['d_vip_num'] = [
            'data' => $now_day_order_p ? $now_day_order_p : 0,
            'percent' => bcmul($pre_day_order_p > 0 ? bcdiv($_order_p, $pre_day_order_p, 2) : abs($now_day_order_p), 100, 2),
            'is_plus' => $now_day_order_p - $pre_day_order_p > 0 ? 1 : ($now_day_order_p - $pre_day_order_p == 0 ? -1 : 0)
        ];

        //会员充值->今天
        $now_month_order_p = StoreOrderModel::where('paid', 1)->where(['is_del' => 0, 'type' => 1])->where('pay_time', 'gt', $now_day)->value('sum(pay_price)');
        $pre_month_order_p = StoreOrderModel::where('paid', 1)->where(['is_del' => 0, 'type' => 1])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->value('sum(pay_price)');
        $m_order_p = abs($now_month_order_p - $pre_month_order_p);
        $first_line['d_vip_price'] = [
            'data' => $now_month_order_p > 0 ? $now_month_order_p : 0,
            'percent' => bcmul($pre_month_order_p > 0 ? bcdiv($m_order_p, $pre_month_order_p, 2) : abs($now_month_order_p), 100, 2),
            'is_plus' => $now_month_order_p - $pre_month_order_p > 0 ? 1 : ($now_month_order_p - $pre_month_order_p == 0 ? -1 : 0)
        ];

        //资料订单数->今日
        $now_day_order_p = DataDownloadOrder::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $now_day)->count();
        $pre_day_order_p = DataDownloadOrder::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->count();
        $_order_p = abs($now_day_order_p - $pre_day_order_p);
        $first_line['d_data_num'] = [
            'data' => $now_day_order_p ? $now_day_order_p : 0,
            'percent' => bcmul($pre_day_order_p > 0 ? bcdiv($_order_p, $pre_day_order_p, 2) : abs($now_day_order_p), 100, 2),
            'is_plus' => $now_day_order_p - $pre_day_order_p > 0 ? 1 : ($now_day_order_p - $pre_day_order_p == 0 ? -1 : 0)
        ];

        //资料交易额->今天
        $now_month_order_p = DataDownloadOrder::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $now_day)->value('sum(pay_price)');
        $pre_month_order_p = DataDownloadOrder::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->value('sum(pay_price)');
        $m_order_p = abs($now_month_order_p - $pre_month_order_p);
        $first_line['d_data_price'] = [
            'data' => $now_month_order_p > 0 ? $now_month_order_p : 0,
            'percent' => bcmul($pre_month_order_p > 0 ? bcdiv($m_order_p, $pre_month_order_p, 2) : abs($now_month_order_p), 100, 2),
            'is_plus' => $now_month_order_p - $pre_month_order_p > 0 ? 1 : ($now_month_order_p - $pre_month_order_p == 0 ? -1 : 0)
        ];

        //活动订单数->今日
        $now_day_order_p = EventSignUp::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $now_day)->count();
        $pre_day_order_p = EventSignUp::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->count();
        $_order_p = abs($now_day_order_p - $pre_day_order_p);
        $first_line['d_event_num'] = [
            'data' => $now_day_order_p ? $now_day_order_p : 0,
            'percent' => bcmul($pre_day_order_p > 0 ? bcdiv($_order_p, $pre_day_order_p, 2) : abs($now_day_order_p), 100, 2),
            'is_plus' => $now_day_order_p - $pre_day_order_p > 0 ? 1 : ($now_day_order_p - $pre_day_order_p == 0 ? -1 : 0)
        ];

        //活动交易额->今天
        $now_month_order_p = EventSignUp::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $now_day)->value('sum(pay_price)');
        $pre_month_order_p = EventSignUp::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->value('sum(pay_price)');
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

        //充值订单数->今日
        $now_day_order_p = UserRecharge::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $now_day)->count();
        $pre_day_order_p = UserRecharge::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->count();
        $_order_p = abs($now_day_order_p - $pre_day_order_p);
        $first_line['d_recharge_num'] = [
            'data' => $now_day_order_p ? $now_day_order_p : 0,
            'percent' => bcmul($pre_day_order_p > 0 ? bcdiv($_order_p, $pre_day_order_p, 2) : abs($now_day_order_p), 100, 2),
            'is_plus' => $now_day_order_p - $pre_day_order_p > 0 ? 1 : ($now_day_order_p - $pre_day_order_p == 0 ? -1 : 0)
        ];

        //充值交易额->今天
        $now_month_order_p = UserRecharge::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $now_day)->value('sum(price)');
        $pre_month_order_p = UserRecharge::where(['paid' => 1, 'is_del' => 0])->where('pay_time', 'gt', $pre_day)->where('pay_time', 'lt', $now_day)->value('sum(price)');
        $m_order_p = abs($now_month_order_p - $pre_month_order_p);
        $first_line['d_recharge_price'] = [
            'data' => $now_month_order_p > 0 ? $now_month_order_p : 0,
            'percent' => bcmul($pre_month_order_p > 0 ? bcdiv($m_order_p, $pre_month_order_p, 2) : abs($now_month_order_p), 100, 2),
            'is_plus' => $now_month_order_p - $pre_month_order_p > 0 ? 1 : ($now_month_order_p - $pre_month_order_p == 0 ? -1 : 0)
        ];

        $this->assign([
            'ip' => get_server_ip(),
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
                    case '1':
                    case '2':
                        $order_list = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->where('type', $type)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '3':
                        $order_list = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '4':
                        $order_list = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '5':
                        $order_list = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '6':
                        $order_list = UserRecharge::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(price) as price")
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
                    case '1':
                    case '2':
                        $pre_total = StoreOrderModel::where('add_time', 'between time', [$pre_datebefor, $pre_dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where('type', $type)->where('is_del', 0)->where('paid', 1)
                            ->find();
                        break;
                    case '3':
                        $pre_total = DataDownloadOrder::where('add_time', 'between time', [$pre_datebefor, $pre_dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where('is_del', 0)->where('paid', 1)
                            ->find();
                        break;
                    case '4':
                        $pre_total = TestPaperOrder::where('add_time', 'between time', [$pre_datebefor, $pre_dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where('is_del', 0)->where('paid', 1)
                            ->find();
                        break;
                    case '5':
                        $pre_total = EventSignUp::where('add_time', 'between time', [$pre_datebefor, $pre_dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where('is_del', 0)->where('paid', 1)
                            ->find();
                        break;
                    case '6':
                        $pre_total = UserRecharge::where('add_time', 'between time', [$pre_datebefor, $pre_dateafter])
                            ->field("count(*) as count,sum(price) as price")->where('is_del', 0)->where('paid', 1)
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
                    case '1':
                    case '2':
                        $total = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where('type', $type)->where('is_del', 0)->where('paid', 1)
                            ->find();
                        break;
                    case '3':
                        $total = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where('is_del', 0)->where('paid', 1)
                            ->find();
                        break;
                    case '4':
                        $total = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where('is_del', 0)->where('paid', 1)
                            ->find();
                        break;
                    case '5':
                        $total = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->field("count(*) as count,sum(pay_price) as price")->where('is_del', 0)->where('paid', 1)
                            ->find();
                        break;
                    case '6':
                        $total = UserRecharge::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->field("count(*) as count,sum(price) as price")->where('is_del', 0)->where('paid', 1)
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
                    case '1':
                    case '2':
                        $order_list = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->where('type', $type)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '3':
                        $order_list = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '4':
                        $order_list = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '5':
                        $order_list = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '6':
                        $order_list = UserRecharge::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(price) as price")
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
                    case '1':
                    case '2':
                        $now_order_list = StoreOrderModel::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->where('type', $type)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '3':
                        $now_order_list = DataDownloadOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '4':
                        $now_order_list = TestPaperOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '5':
                        $now_order_list = EventSignUp::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '6':
                        $now_order_list = UserRecharge::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(price) as price")
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
                    case '1':
                    case '2':
                        $pre_total = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->where('type', $type)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '3':
                        $pre_total = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '4':
                        $pre_total = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '5':
                        $pre_total = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '6':
                        $pre_total = UserRecharge::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(price) as price")
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
                    case '1':
                    case '2':
                        $total = StoreOrderModel::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->where('type', $type)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '3':
                        $total = DataDownloadOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '4':
                        $total = TestPaperOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '5':
                        $total = EventSignUp::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '6':
                        $total = UserRecharge::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(price) as price")
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
                    case '1':
                    case '2':
                        $order_list = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->where('type', $type)->field("FROM_UNIXTIME(add_time,'%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '3':
                        $order_list = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '4':
                        $order_list = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '5':
                        $order_list = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '6':
                        $order_list = UserRecharge::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(price) as price")
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
                    case '1':
                    case '2':
                        $now_order_list = StoreOrderModel::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->where('type', $type)->field("FROM_UNIXTIME(add_time,'%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '3':
                        $now_order_list = DataDownloadOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '4':
                        $now_order_list = TestPaperOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '5':
                        $now_order_list = EventSignUp::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '6':
                        $now_order_list = UserRecharge::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(price) as price")
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
                $series = ['normal' => ['label' => ['show' => true, 'position' => 'top']]];
                $chartdata['series'][] = ['name' => $chartdata['legend'][0], 'type' => 'bar', 'itemStyle' => $series, 'data' => $data['pre']['price']];//分类1值
                $chartdata['series'][] = ['name' => $chartdata['legend'][1], 'type' => 'bar', 'itemStyle' => $series, 'data' => $data['now']['price']];//分类1值
                $chartdata['series'][] = ['name' => $chartdata['legend'][2], 'type' => 'line', 'itemStyle' => $series, 'data' => $data['pre']['count']];//分类2值
                $chartdata['series'][] = ['name' => $chartdata['legend'][3], 'type' => 'line', 'itemStyle' => $series, 'data' => $data['now']['count']];//分类2值

                //统计总数上期
                switch ($type) {
                    case '0':
                    case '1':
                    case '2':
                        $pre_total = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->where('type', $type)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '3':
                        $pre_total = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '4':
                        $pre_total = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '5':
                        $pre_total = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '6':
                        $pre_total = UserRecharge::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(price) as price")
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
                    case '1':
                    case '2':
                        $total = StoreOrderModel::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->where('type', $type)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '3':
                        $total = DataDownloadOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '4':
                        $total = TestPaperOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '5':
                        $total = EventSignUp::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '6':
                        $total = UserRecharge::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(price) as price")
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
                    case '1':
                    case '2':
                        $order_list = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->where('type', $type)->field("FROM_UNIXTIME(add_time,'%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '3':
                        $order_list = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '4':
                        $order_list = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '5':
                        $order_list = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '6':
                        $order_list = UserRecharge::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%m-%d') as day,count(*) as count,sum(price) as price")
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
                    case '1':
                    case '2':
                        $now_order_list = StoreOrderModel::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->where('type', $type)->field("FROM_UNIXTIME(add_time,'%d') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '3':
                        $now_order_list = DataDownloadOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '4':
                        $now_order_list = TestPaperOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '5':
                        $now_order_list = EventSignUp::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(pay_price) as price")
                            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")->order('add_time asc')->select()->toArray();
                        break;
                    case '6':
                        $now_order_list = UserRecharge::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("FROM_UNIXTIME(add_time,'%w') as day,count(*) as count,sum(price) as price")
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
                    case '1':
                    case '2':
                        $pre_total = StoreOrderModel::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->where('type', $type)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '3':
                        $pre_total = DataDownloadOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '4':
                        $pre_total = TestPaperOrder::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '5':
                        $pre_total = EventSignUp::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '6':
                        $pre_total = UserRecharge::where('add_time', 'between time', [$datebefor, $dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(price) as price")
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
                    case '1':
                    case '2':
                        $total = StoreOrderModel::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->where('type', $type)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '3':
                        $total = DataDownloadOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '4':
                        $total = TestPaperOrder::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '5':
                        $total = EventSignUp::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(pay_price) as price")
                            ->find();
                        break;
                    case '6':
                        $total = UserRecharge::where('add_time', 'between time', [$now_datebefor, $now_dateafter])
                            ->where('is_del', 0)->where('paid', 1)->field("count(*) as count,sum(price) as price")
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

    /**
     * 用户图表
     */
    public function userchart()
    {
        header('Content-type:text/json');
        $starday = date('Y-m-d', strtotime('-30 day'));
        $yesterday = date('Y-m-d');
        $user_list = UserModel::where('add_time', 'between time', [$starday, $yesterday])
            ->field("FROM_UNIXTIME(add_time,'%m-%e') as day,count(*) as count")
            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")
            ->order('add_time asc')
            ->select();
        $user_list = count($user_list) > 0 ? $user_list->toArray() : [];
        $chartdata = [];
        $data = [];
        $chartdata['legend'] = ['用户数'];//分类
        $chartdata['yAxis']['maxnum'] = 0;//最大值数量
        if (empty($user_list)) return JsonService::fail('无数据');
        foreach ($user_list as $k => $v) {
            $data['day'][] = $v['day'];
            $data['count'][] = $v['count'];
            if ($chartdata['yAxis']['maxnum'] < $v['count']) $chartdata['yAxis']['maxnum'] = $v['count'];
        }
        $chartdata['xAxis'] = $data['day'];//X轴值
        $chartdata['series'] = $data['count'];//分类1值
        return JsonService::successful('ok', $chartdata);
    }

    /**待办事统计
     * @param Request|null $request
     */
    public function Jnotice()
    {
        header('Content-type:text/json');
        $data = [];
        $data['reflectnum'] = UserExtractModel::where(['status' => 0, 'mer_id' => 0])->count();;//用户提现
        $data['mer_reflectnum'] = UserExtractModel::where(['status' => 0])->where('mer_id', '>', 0)->count();;//讲师提现
        $data['msgcount'] = bcadd($data['reflectnum'], $data['mer_reflectnum'], 0);
        return JsonService::successful('ok', $data);
    }

    public function check_auth()
    {
        return JsonService::successful('ok', $this->checkAuthDecrypt());
    }

    /**
     * @return mixed
     */
    public function auth()
    {
        $curent_version = getversion();
        $this->assign(['curent_version' => $curent_version]);
        return $this->fetch();
    }

    public function verify_dialog()
    {
        return $this->fetch();
    }

    public function apply_dialog()
    {
        return $this->fetch();
    }

    public function copyright_dialog()
    {
        return $this->fetch();
    }

    /**获取授权码
     * @return false|string
     */
    public function auth_data()
    {
        return JsonService::successful($this->getAuth());
    }

    public function get_auth_data()
    {
        return JsonService::successful($this->__u05qFaFgCglbkbV9eHWOEiidrJbsm());
    }

    /**
     * 获取授权产品
     */
    public function get_zsff_store()
    {
        return JsonService::successful('ok', $this->__lskbEPbOLZ0cTaZ0XIGUusdFJsVI4yW('zsff'));
    }

    /**
     * 下单
     */
    public function pay_order()
    {
        $data = parent::postMore([
            ['phone', ''],
            ['product_type', 'zsff'],
            ['domain_name', ''],
            ['company_name', '']
        ], $this->request);
        if (!$data['company_name']) {
            return JsonService::fail('请填写公司名称');
        }
        if (!$data['domain_name']) {
            return JsonService::fail('请填写授权域名');
        }
        return JsonService::successful('ok', $this->__nWr5aRIbtPUWlNWFfD1cctaF9G3aiC($data['phone'], $data['product_type'], $data['company_name'], $data['domain_name']));
    }

    /**
     * 获取去授权产品
     */
    public function get_zsff_copyright()
    {
        return JsonService::successful('ok', $this->__lskbEPbOLZ0cTaZ0XIGUusdFJsVI4yW('copyright'));
    }

    /**
     * 查询是否购买版权
     */
    public function check_copyright()
    {
        $data = parent::postMore([
            ['phone', ''],
            ['orderId', '']
        ], $this->request);
        if (!$data['phone']) {
            return JsonService::fail('请填写登录手机号');
        }
        if (!$data['orderId']) {
            return JsonService::fail('请填写购买去版权订单号');
        }
        return JsonService::successful('ok', $this->__6j3nfcwmWqrsDx8F0MjZGeQyWvLsqeFXww($data['phone'], $data['orderId']));
    }

    /**
     * 查询是购买版权订单
     */
    public function check_copyright_order()
    {
        $data = parent::postMore([
            ['phone', ''],
            ['orderId', '']
        ], $this->request);
        if (!$data['phone']) {
            return JsonService::fail('请填写登录手机号');
        }
        if (!$data['orderId']) {
            return JsonService::fail('请填写购买去版权订单号');
        }
        return JsonService::successful('ok', $this->__xz8MHDCmMcIbe1HkWr0RM0FGz3gnYIX($data['phone'], $data['orderId']));
    }

    /**
     * 发送短息
     */
    public function get_code()
    {
        $data = parent::postMore([
            ['phone', ''],
        ], $this->request);
        return JsonService::successful('ok', $this->__6u44FLR2RPvjtC3t7fV8mADyfrn37RZj2($data['phone']));
    }

    /**
     * 手机号授权登录
     */
    public function user_auth_login()
    {
        $data = parent::postMore([
            ['phone', ''],
            ['code', '']
        ], $this->request);
        return JsonService::successful('ok', $this->__k0dUcnKjRUs9lfEllqO9J($data['phone'], $data['code'], true));
    }

    /**
     * 申请授权
     */
    public function user_auth_apply()
    {
        $data = parent::postMore([
            ['company_name', ''],
            ['domain_name', ''],
            ['order_id', ''],
            ['phone', ''],
            ['label', 4],
            ['captcha', '']
        ], $this->request);
        if (!$data['company_name']) {
            return JsonService::fail('请填写公司名称');
        }
        if (!$data['domain_name']) {
            return JsonService::fail('请填写授权域名');
        }

        if (!$data['phone']) {
            return JsonService::fail('请填写手机号码');
        }
        if (!$data['order_id']) {
            return JsonService::fail('请填写订单id');
        }
        $res = $this->auth_apply($data);
        if (isset($res['status']) && $res['status'] === 200) {
            return JsonService::successful('申请授权成功!');
        } else {
            return JsonService::fail(isset($res['msg']) ? $res['msg'] : '申请授权失败');
        }
    }

    /**
     * 保存版权信息
     */
    public function save_copyright()
    {
        $data = parent::postMore([
            ['copyrightContent', ''],
            ['copyrightLogo', '']
        ], $this->request);
        if (!$data['copyrightContent'] && !$data['copyrightLogo']) {
            return JsonService::fail('请添加版权信息');
        }
        return JsonService::successful('ok', $this->__qsG71NREI01vix2OkjH($data));
    }

    /**宝塔命令
     * @return mixed
     */
    public function command()
    {
        $queue_name = Config::get('queue_name', '') ? Config::get('queue_name', '') : 'doPinkJobQueue';
        $this->assign(['name' => $queue_name]);
        return $this->fetch();
    }
}


