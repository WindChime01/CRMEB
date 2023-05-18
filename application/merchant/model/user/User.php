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


namespace app\merchant\model\user;


use app\admin\model\order\StoreOrder;
use app\admin\model\user\UserExtract;
use traits\ModelTrait;
use app\wap\model\user\UserBill;
use basic\ModelBasic;
use app\admin\model\wechat\WechatUser;
use service\SystemConfigService;
use app\admin\model\order\StoreOrderStatus;
use app\admin\model\system\SystemMenus;
use service\PhpSpreadsheetService;

/**
 * 用户管理 model
 * Class User
 * @package app\merchant\model\user
 */
class User extends ModelBasic
{
    use ModelTrait;

    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where)
    {
        $model = new self;
        if ($where['status'] != '') $model = $model->where('status', $where['status']);
        if ($where['is_promoter'] != '') $model = $model->where('is_promoter', $where['is_promoter']);
        if ($where['nickname'] != '') $model = $model->where('nickname|uid', 'like', "%$where[nickname]%");
        if (isset($where['uids']) && count($where['uids'])) $model->where('uid', 'in', $where['uids']);
        $model = $model->order('uid desc');
        return self::page($model, function ($item) {
            if ($item['spread_uid']) {
                $item['spread_uid_nickname'] = self::where('uid', $item['spread_uid'])->value('nickname');
            } else {
                $item['spread_uid_nickname'] = '无';
            }
        }, $where);
    }

    public static function getSpreadUidTwo($uid)
    {
        if (is_array($uid)) $spread_uid = self::where('spread_uid', 'in', $uid)->column('uid');
        else $spread_uid = self::where('spread_uid', $uid)->column('uid');
        return self::where('spread_uid', 'in', $spread_uid)->column('uid');
    }

    /*
     * 设置搜索条件
     *
     */
    public static function setWhere($where)
    {
        if ($where['order'] != '') {
            $model = self::order(self::setOrder($where['order']));
        } else {
            $model = self::order('u.uid desc');
        }
        if ($where['nickname'] != '') {
            $model = $model->where('u.nickname|u.uid|u.phone', 'LIKE', "%$where[nickname]%");
        }
        if ($where['status'] != '') {
            $model = $model->where('status', $where['status']);
        }
        if ($where['user_time_type'] == 'visitno' && $where['user_time'] != '') {
            list($startTime, $endTime) = explode(' - ', $where['user_time']);
            $model = $model->where('u.last_time', ['>', strtotime($endTime) + 24 * 3600], ['<', strtotime($startTime)], 'or');
        }
        if ($where['user_time_type'] == 'visit' && $where['user_time'] != '') {
            list($startTime, $endTime) = explode(' - ', $where['user_time']);
            $model = $model->where('u.last_time', '>', strtotime($startTime));
            $model = $model->where('u.last_time', '<', strtotime($endTime) + 24 * 3600);
        }
        if ($where['user_time_type'] == 'add_time' && $where['user_time'] != '') {
            list($startTime, $endTime) = explode(' - ', $where['user_time']);
            $model = $model->where('u.add_time', '>', strtotime($startTime));
            $model = $model->where('u.add_time', '<', strtotime($endTime) + 24 * 3600);
        }
        if ($where['pay_count'] !== '') {
            if ($where['pay_count'] == '-1') $model = $model->where('pay_count', 0);
            else $model = $model->where('pay_count', '>', $where['pay_count']);
        }
        switch ($where['is_promoter']) {
            case '1':
                $model = $model->where('u.is_promoter', 1);
                break;
            case '2':
                $model = $model->where('u.level', 1);
                break;
        }
        return $model;
    }

    /**
     * 异步获取当前用户 信息
     * @param $where
     * @return array
     */
    public static function getUserList($where)
    {
        $model = self::setWhere($where);
        $list = $model->alias('u')->field('u.*')->page((int)$where['page'], (int)$where['limit'])
            ->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        $isPhoneMenu = is_phone_menu();
        foreach ($list as $k => &$item) {
            if ($item['spread_uid']) {
                $nickname = self::where('uid', $item['spread_uid'])->value('nickname');
                if (check_phone($nickname) && !$isPhoneMenu) {
                    $nickname = substr_replace($nickname, '****', 3, 4);
                }
                $item['spread_uid_nickname'] = $nickname . '/' . $item['spread_uid'];
            } else {
                $item['spread_uid_nickname'] = '无';
            }

            if ($item['level']) {
                $item['levelType'] = '会员';
            } else {
                $item['levelType'] = '非会员';
            }
            if ($item['is_h5user'] == 1) {
                $item['user_type'] = 'H5';
            } else if ($item['is_h5user'] == 2) {
                $item['user_type'] = 'PC';
            } else if (!$item['is_h5user']) {
                $item['user_type'] = '公众号';
            }
            if ($item['valid_time']) $item['_valid_time'] = date('Y-m-d H:i:s', $item['valid_time']);

            if (!$isPhoneMenu && $item['phone']) {
                $item['phone'] = substr_replace($item['phone'], '****', 3, 4);
            }
            if (check_phone($item['nickname']) && !$isPhoneMenu) {
                $item['nickname'] = substr_replace($item['nickname'], '****', 3, 4);
            }
        }
        $count = self::setWhere($where)->alias('u')->count();
        return ['count' => $count, 'data' => $list];
    }

    /**
     * 异步获取当前用户 信息
     * @param $where
     * @return array
     */
    public static function add_get_user_list($where)
    {
        $model = self::setWhere($where);
        $list = $model->alias('u')
            ->field('u.*')
            ->page((int)$where['page'], (int)$where['limit'])
            ->select();
        $count = self::setWhere($where)->alias('u')->count();
        return ['count' => $count, 'data' => $list];
    }

    /**
     *  修改用户状态
     * @param $uids 用户uid
     * @param $status 修改状态
     * @return array
     */
    public static function destrSyatus($uids, $status)
    {
        if (empty($uids) && !is_array($uids)) return false;
        if ($status == '') return false;
        self::beginTrans();
        try {
            $res = self::where('uid', 'in', $uids)->update(['status' => $status]);
            self::checkTrans($res);
            return true;
        } catch (\Exception $e) {
            self::rollbackTrans();
            return Json::fail($e->getMessage());
        }
    }

    /*
     *  获取某季度,某年某年后的时间戳
     *
     * self::getMonth('n',1) 获取当前季度的上个季度的时间戳
     * self::getMonth('n') 获取当前季度的时间戳
     */
    public static function getMonth($time = '', $ceil = 0)
    {
        if (empty($time)) {
            $firstday = date("Y-m-01", time());
            $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));
        } else if ($time == 'n') {
            if ($ceil != 0)
                $season = ceil(date('n') / 3) - $ceil;
            else
                $season = ceil(date('n') / 3);
            $firstday = date('Y-m-01', mktime(0, 0, 0, ($season - 1) * 3 + 1, 1, date('Y')));
            $lastday = date('Y-m-t', mktime(0, 0, 0, $season * 3, 1, date('Y')));
        } else if ($time == 'y') {
            $firstday = date('Y-01-01');
            $lastday = date('Y-12-31');
        } else if ($time == 'h') {
            $firstday = date('Y-m-d', strtotime('this week +' . $ceil . ' day')) . ' 00:00:00';
            $lastday = date('Y-m-d', strtotime('this week +' . ($ceil + 1) . ' day')) . ' 23:59:59';
        }
        return array($firstday, $lastday);
    }

    public static function getcount()
    {
        return self::alias('a')->join('wechat_user u', 'u.uid=a.uid', 'left')->count();
    }

    /*
    *获取用户某个时间段的消费信息
    *
    * reutrn Array || number
    */
    public static function consume($where, $status = '', $keep = '')
    {
        $model = new self;
        $user_id = [];
        if (is_array($where)) {
            if ($where['is_promoter'] != '') $model = $model->where('is_promoter', $where['is_promoter']);
            if ($where['status'] != '') $model = $model->where('status', $where['status']);
            switch ($where['date']) {
                case null:
                case 'today':
                case 'week':
                case 'year':
                    if ($where['date'] == null) {
                        $where['date'] = 'month';
                    }
                    if ($keep) {
                        $model = $model->whereTime('add_time', $where['date'])->whereTime('last_time', $where['date']);
                    } else {
                        $model = $model->whereTime('add_time', $where['date']);
                    }
                    break;
                case 'quarter':
                    $quarter = self::getMonth('n');
                    $startTime = strtotime($quarter[0]);
                    $endTime = strtotime($quarter[1]);
                    if ($keep) {
                        $model = $model->where('add_time', '>', $startTime)->where('add_time', '<', $endTime)->where('last_time', '>', $startTime)->where('last_time', '<', $endTime);
                    } else {
                        $model = $model->where('add_time', '>', $startTime)->where('add_time', '<', $endTime);
                    }
                    break;
                default:
                    //自定义时间
                    if (strstr($where['date'], '-') !== FALSE) {
                        list($startTime, $endTime) = explode('-', $where['date']);
                        $model = $model->where('add_time', '>', strtotime($startTime))->where('add_time', '<', strtotime($endTime));
                    } else {
                        $model = $model->whereTime('add_time', 'month');
                    }
                    break;
            }
        } else {
            if (is_array($status)) {
                $model = $model->where('add_time', '>', $status[0])->where('add_time', '<', $status[1]);
            }
        }
        if ($keep === true) {
            return $model->count();
        }
        if ($status === 'default') {
            return $model->group('from_unixtime(add_time,\'%Y-%m-%d\')')->field('count(uid) num,from_unixtime(add_time,\'%Y-%m-%d\') add_time,uid')->select()->toArray();
        }

        $uid = $model->field('uid')->select()->toArray();
        foreach ($uid as $val) {
            $user_id[] = $val['uid'];
        }
        if (empty($user_id)) {
            $user_id = [0];
        }
        if ($status === 'xiaofei') {
            $list = UserBill::where('uid', 'in', $user_id)
                ->group('type')
                ->field('sum(number) as top_number,title')
                ->select()
                ->toArray();
            $series = [
                'name' => isset($list[0]['title']) ? $list[0]['title'] : '',
                'type' => 'pie',
                'radius' => ['40%', '50%'],
                'data' => []
            ];
            foreach ($list as $key => $val) {
                $series['data'][$key]['value'] = $val['top_number'];
                $series['data'][$key]['name'] = $val['title'];
            }
            return $series;
        } else if ($status === 'form') {
            $list = WechatUser::where('uid', 'in', $user_id)->group('city')->field('count(city) as top_city,city')->limit(0, 10)->select()->toArray();
            $count = self::getcount();
            $option = [
                'legend_date' => [],
                'series_date' => []
            ];
            foreach ($list as $key => $val) {
                $num = $count != 0 ? (bcdiv($val['top_city'], $count, 2)) * 100 : 0;
                $t = ['name' => $num . '%  ' . (empty($val['city']) ? '未知' : $val['city']), 'icon' => 'circle'];
                $option['legend_date'][$key] = $t;
                $option['series_date'][$key] = ['value' => $num, 'name' => $t['name']];
            }
            return $option;
        } else {
            $number = UserBill::where('uid', 'in', $user_id)->where('type', 'pay_product')->sum('number');
            return $number;
        }
    }

    /*
     * 获取 用户某个时间段的钱数或者TOP20排行
     *
     * return Array  || number
     */
    public static function getUserSpend($date, $status = '')
    {
        $model = new self();
        $model = $model->alias('A');
        switch ($date) {
            case null:
            case 'today':
            case 'week':
            case 'year':
                if ($date == null) $date = 'month';
                $model = $model->whereTime('A.add_time', $date);
                break;
            case 'quarter':
                list($startTime, $endTime) = self::getMonth('n');
                $model = $model->where('A.add_time', '>', strtotime($startTime));
                $model = $model->where('A.add_time', '<', strtotime($endTime));
                break;
            default:
                list($startTime, $endTime) = explode('-', $date);
                $model = $model->where('A.add_time', '>', strtotime($startTime));
                $model = $model->where('A.add_time', '<', strtotime($endTime));
                break;
        }
        if ($status === true) {
            return $model->join('user_bill B', 'B.uid=A.uid')->where('B.type', 'pay_product')->where('B.pm', 0)->sum('B.number');
        }
        $list = $model->join('user_bill B', 'B.uid=A.uid')
            ->where('B.type', 'pay_product')
            ->where('B.pm', 0)
            ->field('sum(B.number) as totel_number,A.nickname,A.avatar,A.now_money,A.uid,A.add_time')
            ->order('totel_number desc')
            ->limit(0, 20)
            ->select()
            ->toArray();
        if (!isset($list[0]['totel_number'])) {
            $list = [];
        }
        return $list;
    }

    /*
     * 获取 相对于上月或者其他的数据
     *
     * return Array
     */
    public static function getPostNumber($date, $status = false, $field = 'A.add_time', $t = '消费')
    {
        $model = new self();
        if (!$status) $model = $model->alias('A');
        switch ($date) {
            case null:
            case 'today':
            case 'week':
            case 'year':
                if ($date == null) {
                    $date = 'last month';
                    $title = '相比上月用户' . $t . '增长';
                }
                if ($date == 'today') {
                    $date = 'yesterday';
                    $title = '相比昨天用户' . $t . '增长';
                }
                if ($date == 'week') {
                    $date = 'last week';
                    $title = '相比上周用户' . $t . '增长';
                }
                if ($date == 'year') {
                    $date = 'last year';
                    $title = '相比去年用户' . $t . '增长';
                }
                $model = $model->whereTime($field, $date);
                break;
            case 'quarter':
                $title = '相比上季度用户' . $t . '增长';
                list($startTime, $endTime) = self::getMonth('n', 1);
                $model = $model->where($field, '>', $startTime);
                $model = $model->where($field, '<', $endTime);
                break;
            default:
                list($startTime, $endTime) = explode('-', $date);
                $title = '相比' . $startTime . '-' . $endTime . '时间段用户' . $t . '增长';
                $Time = strtotime($endTime) - strtotime($startTime);
                $model = $model->where($field, '>', strtotime($startTime) + $Time);
                $model = $model->where($field, '<', strtotime($endTime) + $Time);
                break;
        }
        if ($status) {
            return [$model->count(), $title];
        }
        $number = $model->join('user_bill B', 'B.uid=A.uid')->where('B.type', 'pay_product')->where('B.pm', 0)->sum('B.number');
        return [$number, $title];
    }

    //获取用户新增,头部信息
    public static function getBadgeList($where)
    {
        $user_count = self::setWherePage(self::getModelTime($where, new self), $where, ['is_promoter', 'status'])->count();
        $user_count_old = self::getOldDate($where)->count();
        $fenxiao = self::setWherePage(self::getModelTime($where, new self), $where, ['is_promoter', 'status'])->where('spread_uid', '<>', 0)->count();
        $fenxiao_count = self::getOldDate($where)->where('spread_uid', 'neq', 0)->count();
        $newFemxiao_count = bcsub($fenxiao, $fenxiao_count, 0);
        $order_count = bcsub($user_count, $user_count_old, 0);
        return [
            [
                'name' => '会员人数',
                'field' => '个',
                'count' => $user_count,
                'content' => '会员总人数',
                'background_color' => 'layui-bg-blue',
                'sum' => self::count(),
                'class' => 'fa fa-bar-chart',
            ],
            [
                'name' => '会员增长',
                'field' => '个',
                'count' => $order_count,
                'content' => '会员增长率',
                'background_color' => 'layui-bg-cyan',
                'sum' => $user_count_old ? bcdiv($order_count, $user_count_old, 2) * 100 : 0,
                'class' => 'fa fa-line-chart',
            ],
            [
                'name' => '分销人数',
                'field' => '个',
                'count' => $fenxiao,
                'content' => '分销总人数',
                'background_color' => 'layui-bg-green',
                'sum' => self::where('spread_uid', 'neq', 0)->count(),
                'class' => 'fa fa-bar-chart',
            ],
            [
                'name' => '分销增长',
                'field' => '个',
                'count' => $newFemxiao_count,
                'content' => '分销总人数',
                'background_color' => 'layui-bg-orange',
                'sum' => $fenxiao_count ? bcdiv($newFemxiao_count, $fenxiao_count, 2) * 100 : 0,
                'class' => 'fa fa-cube',
            ],
        ];
    }

    /*
     * 获取会员增长曲线图和分布图
     *  $where 查询条件
     *  $limit 显示条数,是否有滚动条
     */
    public static function getUserChartList($where, $limit = 20)
    {
        $list = self::setWherePage(self::getModelTime($where, new self), $where, ['is_promoter', 'status'])
            ->where('add_time', 'neq', 0)
            ->field(['FROM_UNIXTIME(add_time,"%Y-%m-%d") as _add_time', 'count(uid) as num'])
            ->order('_add_time asc')
            ->group('_add_time')
            ->select();
        count($list) && $list = $list->toArray();
        $seriesdata = [];
        $xdata = [];
        $Zoom = '';
        foreach ($list as $item) {
            $seriesdata[] = $item['num'];
            $xdata[] = $item['_add_time'];
        }
        (count($xdata) > $limit) && $Zoom = $xdata[$limit - 5];
        //多次购物会员数量饼状图
        $count = self::setWherePage(self::getModelTime($where, new self), $where, ['is_promoter'])->count();
        $user_count = self::setWherePage(self::getModelTime($where, self::alias('a')->join('store_order r', 'r.uid=a.uid'), 'a.add_time'), $where, ['is_promoter'])
            ->where('r.paid', 1)->count('a.uid');
        $shop_xdata = ['多次购买数量占比', '无购买数量占比'];
        $shop_data = [];
        $count > 0 && $shop_data = [
            [
                'value' => bcdiv($user_count, $count, 2) * 100,
                'name' => $shop_xdata[0],
                'itemStyle' => [
                    'color' => '#D789FF',
                ]
            ],
            [
                'value' => bcdiv($count - $user_count, $count, 2) * 100,
                'name' => $shop_xdata[1],
                'itemStyle' => [
                    'color' => '#7EF0FB',
                ]
            ]
        ];

        return compact('shop_data', 'shop_xdata', 'fenbu_data', 'fenbu_xdata', 'seriesdata', 'xdata', 'Zoom');
    }

    //获取$date的前一天或者其他的时间段
    public static function getOldDate($where, $moedls = null)
    {
        $model = $moedls === null ? self::setWherePage(new self(), $where, ['is_promoter', 'status']) : $moedls;
        switch ($where['data']) {
            case 'today':
                $model = $model->whereTime('add_time', 'yesterday');
                break;
            case 'week':
                $model = $model->whereTime('add_time', 'last week');
                break;
            case 'month':
                $model = $model->whereTime('add_time', 'last month');
                break;
            case 'year':
                $model = $model->whereTime('add_time', 'last year');
                break;
            case 'quarter':
                $time = self::getMonth('n', 1);
                $model = $model->where('add_time', 'between', $time);
                break;
        }
        return $model;
    }

    //获取用户属性和性别分布图
    public static function getEchartsData($where)
    {
        $model = self::alias('a');
        $data = self::getModelTime($where, $model, 'a.add_time')
            ->join('wechat_user r', 'r.uid=a.uid')
            ->group('r.province')
            ->field('count(r.province) as count,province')
            ->order('count desc')
            ->limit(15)
            ->select();
        if (count($data)) $data = $data->toArray();
        $legdata = [];
        $dataList = [];
        foreach ($data as $value) {
            $value['province'] == '' && $value['province'] = '未知省份';
            $legdata[] = $value['province'];
            $dataList[] = $value['count'];
        }
        $model = self::alias('a');
        $sex = self::getModelTime($where, $model, 'a.add_time')
            ->join('wechat_user r', 'r.uid=a.uid')
            ->group('r.sex')
            ->field('count(r.uid) as count,sex')
            ->order('count desc')
            ->select();
        if (count($sex)) $sex = $sex->toArray();
        $sexlegdata = ['男', '女', '未知'];
        $sexcount = self::getModelTime($where, new self())->count();
        $sexList = [];
        $color = ['#FB7773', '#81BCFE', '#91F3FE'];
        foreach ($sex as $key => $item) {
            if ($item['sex'] == 1) {
                $item_date['name'] = '男';
            } else if ($item['sex'] == 2) {
                $item_date['name'] = '女';
            } else {
                $item_date['name'] = '未知性别';
            }
            $item_date['value'] = bcdiv($item['count'], $sexcount, 2) * 100;
            $item_date['itemStyle']['color'] = $color[$key];
            $sexList[] = $item_date;
        }
        return compact('sexList', 'sexlegdata', 'legdata', 'dataList');
    }

    //获取佣金记录列表
    public static function getCommissionList($where)
    {
        $list = self::setCommissionWhere($where)
            ->page((int)$where['page'], (int)$where['limit'])
            ->select();
        count($list) && $list = $list->toArray();
        foreach ($list as &$value) {
            $value['ex_price'] = db('user_extract')->where(['uid' => $value['uid']])->sum('extract_price');
            $value['extract_price'] = db('user_extract')->where(['uid' => $value['uid'], 'status' => 1])->sum('extract_price');
        }
        $count = self::setCommissionWhere($where)->count();
        return ['data' => $list, 'count' => $count];
    }

    //获取佣金记录列表的查询条件
    public static function setCommissionWhere($where)
    {
        $models = self::setWherePage(self::alias('A'), $where, [], ['A.nickname', 'A.uid'])
            ->join('user_bill B', 'B.uid=A.uid')
            ->group('A.uid')
            ->where(['B.category' => 'now_money'])
            ->where('B.type', 'in', ['brokerage', 'brokerage_return'])
            ->field(['B.number', 'A.nickname', 'A.uid', 'A.now_money', 'A.brokerage_price']);
        if ($where['order'] == '') {
            $models = $models->order('A.brokerage_price desc');
        } else {
            $models = $models->order($where['order'] == 1 ? 'A.brokerage_price desc' : 'A.brokerage_price asc');
        }
        if ($where['price_max'] > 0 && $where['price_min'] > 0) {
            $models = $models->where('A.brokerage_price', 'between', [$where['price_min'], $where['price_max']]);
        }
        return $models;
    }

    //获取某人用户推广信息
    public static function getUserinfo($uid)
    {
        $userinfo = self::where(['uid' => $uid])->field(['nickname', 'spread_uid', 'now_money', 'add_time'])->find()->toArray();
        $userinfo['number'] = UserBill::getCommissionAmount($uid);
        $userinfo['spread_name'] = $userinfo['spread_uid'] ? self::where(['uid' => $userinfo['spread_uid']])->value('nickname') : '';
        return $userinfo;
    }

    /**用户信息
     * @param $uid
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAllUserinfo($uid)
    {
        $userinfo = self::where(['uid' => $uid])->find()->toArray();
        return $userinfo;
    }

    public static function getUserinfoV1($uid)
    {
        $isPhoneMenu = is_phone_menu();
        $userinfo = self::where(['uid' => $uid])->find()->toArray();
        if (check_phone($userinfo['nickname']) && !$isPhoneMenu) {
            $userinfo['nickname'] = substr_replace($userinfo['nickname'], '****', 3, 4);
        }
        $userinfo['spread_name'] = $userinfo['spread_uid'] ? self::where(['uid' => $userinfo['spread_uid']])->value('nickname') : '';
        $spread = self::where(['spread_uid' => $uid])->where('is_promoter', 'neq', 0)->column('uid');
        $userinfo['spread_count'] = count($spread);
        $userinfo['spread_one'] = UserBill::where(['o.paid' => 1, 'a.uid' => $uid, 'a.category' => 'now_money'])
            ->where('a.type', 'in', ['brokerage'])->alias('a')->join('__STORE_ORDER__ o', 'a.link_id=o.id')->sum('o.pay_price');
        $userinfo['bill_sum'] = UserBill::where(['category' => 'now_money', 'uid' => $userinfo['uid']])->where('type', 'in', ['brokerage'])->sum('number');
        return $userinfo;
    }

    public static function getPayPrice($uid, $type = ['brokerage'])
    {
        return UserBill::where(['o.paid' => 1, 'a.uid' => $uid, 'a.category' => 'now_money'])
            ->where('a.type', 'in', $type)->alias('a')->join('__STORE_ORDER__ o', 'a.link_id=o.id')->sum('o.pay_price');
    }

    public static function getLinkCount($uid, $type = ['brokerage'])
    {
        return UserBill::where(['o.paid' => 1, 'a.uid' => $uid, 'a.category' => 'now_money'])
            ->where('a.type', 'in', $type)->alias('a')->join('__STORE_ORDER__ o', 'a.link_id=o.id')->count();
    }

    public static function getSpreadListV1($where)
    {
        $spread = self::where(['spread_uid' => $where['uid']])->column('uid') ?: [0];
        $list = self::where('uid', 'in', $spread)->order('add_time desc')->page((int)$where['page'], (int)$where['limit'])->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as &$item) {
            $item['order_count'] = self::getLinkCount($item['uid'], ['brokerage']);
            $item['sum_pay_price'] = self::getPayPrice($item['uid'], ['brokerage']);
            $item['sum_number'] = UserBill::where('uid', $item['uid'])->where('category', 'now_money')->where('type', 'in', ['brokerage'])->sum('number');
        }
        return $list;
    }

    //获取某用户的详细信息
    public static function getUserDetailed($uid)
    {
        $isPhoneMenu = is_phone_menu();
        $gold_name = SystemConfigService::get('gold_name');//虚拟币名称
        $key_field = ['real_name', 'phone', 'province', 'city', 'district', 'detail', 'post_code'];
        $Address = ($thisAddress = db('user_address')->where(['uid' => $uid, 'is_default' => 1])->field($key_field)->find()) ?
            $thisAddress :
            db('user_address')->where(['uid' => $uid])->field($key_field)->find();
        $UserInfo = self::get($uid);
        if ($UserInfo['last_time']) $UserInfo['last_time'] = date('Y-m-d H:i:s', $UserInfo['last_time']);//最近一次访问日期
        else $UserInfo['last_time'] = '无访问';//最近一次访问日期
        $UserInfo['add_time'] = date('Y-m-d H:i:s', $UserInfo['add_time']);
        $time = '首次:' . $UserInfo['add_time'] . '最近:' . $UserInfo['last_time'];
        if (check_phone($UserInfo['nickname']) && !$isPhoneMenu) {
            $UserInfo['nickname'] = substr_replace($UserInfo['nickname'], '****', 3, 4);
        }
        if (check_phone($UserInfo['phone']) && !$isPhoneMenu) {
            $UserInfo['phone'] = substr_replace($UserInfo['phone'], '****', 3, 4);
        }
        return [
            ['col' => 12, 'name' => '默认收货地址', 'value' => $thisAddress ? '收货人:' . $thisAddress['real_name'] . ' 收货人电话:' . $thisAddress['phone'] . ' 地址:' . $thisAddress['province'] . ' ' . $thisAddress['city'] . ' ' . $thisAddress['district'] . ' ' . $thisAddress['detail'] : ''],
            ['name' => 'ID', 'value' => $uid],
            ['name' => '手机号码', 'value' => $UserInfo['phone']],
            ['name' => '微信昵称', 'value' => $UserInfo['nickname']],
            ['name' => '购买次数', 'value' => StoreOrder::getUserCountPay($uid)],
            ['name' => "$gold_name" . '余额', 'value' => $UserInfo['gold_num']],
            ['name' => '上级推广人', 'value' => $UserInfo['spread_uid'] ? self::where(['uid' => $UserInfo['spread_uid']])->value('nickname') : ''],
            ['name' => '账户余额', 'value' => $UserInfo['now_money']],
            ['name' => '佣金总收入', 'value' => UserBill::getCommissionAmount($uid)],
            ['name' => '提现总金额', 'value' => db('user_extract')->where(['uid' => $uid, 'status' => 1])->sum('extract_price')],
            ['name' => '访问日期', 'value' => $time]
        ];
    }

    //获取某用户的订单个数,消费明细
    public static function getHeaderList($uid)
    {
        return [
            [
                'title' => '总计订单',
                'value' => StoreOrder::where(['uid' => $uid, 'paid' => 1])->count(),
                'key' => '笔',
                'class' => '',
            ],
            [
                'title' => '总消费金额',
                'value' => StoreOrder::where(['uid' => $uid, 'paid' => 1])->sum('pay_price'),
                'key' => '元',
                'class' => '',
            ],
            [
                'title' => '本月订单',
                'value' => StoreOrder::where(['uid' => $uid, 'paid' => 1])->whereTime('add_time', 'month')->count(),
                'key' => '笔',
                'class' => '',
            ],
            [
                'title' => '本月消费金额',
                'value' => StoreOrder::where(['uid' => $uid, 'paid' => 1])->whereTime('add_time', 'month')->sum('pay_price'),
                'key' => '元',
                'class' => '',
            ]
        ];
    }

    /*
     * 获取 会员 订单个数,积分明细,优惠劵明细
     *
     * $uid 用户id;
     *
     * return array
     */
    public static function getCountInfo($uid)
    {
        $order_count = StoreOrder::where(['uid' => $uid])->count();
        $integral_count = UserBill::where(['uid' => $uid, 'category' => 'integral'])->where('type', 'in', ['deduction', 'system_add'])->count();
        $sign_count = UserBill::where(['uid' => $uid, 'category' => 'integral', 'type' => 'sign'])->count();
        $balanceChang_count = UserBill::where(['uid' => $uid, 'category' => 'now_money'])
            ->where('type', 'in', ['system_add', 'pay_product', 'extract', 'extract_fail', 'pay_goods', 'pay_sign_up', 'pay_product_refund', 'system_sub'])
            ->count();
        $coupon_count = 0;
        $spread_count = self::where(['spread_uid' => $uid])->count();
        $pay_count = self::getDb('special_buy')->where('uid', $uid)->where('is_del', 0)->count();
        return compact('order_count', 'integral_count', 'sign_count', 'balanceChang_count', 'coupon_count', 'spread_count', 'pay_count');
    }

    /*
     * 获取 会员业务的
     * 购物会员统计
     *  会员访问量
     *
     * 曲线图
     *
     * $where 查询条件
     *
     * return array
     */
    public static function getUserBusinessChart($where, $limit = 20)
    {
        //获取购物会员人数趋势图
        $list = self::getModelTime($where, self::where('a.status', 1)->alias('a')->join('store_order r', 'r.uid=a.uid'), 'a.add_time')
            ->where(['r.paid' => 1, 'a.is_promoter' => 0])
            ->where('a.add_time', 'neq', 0)
            ->field(['FROM_UNIXTIME(a.add_time,"%Y-%m-%d") as _add_time', 'count(r.uid) as count_user'])
            ->group('_add_time')
            ->order('_add_time asc')
            ->select();
        count($list) && $list = $list->toArray();
        $seriesdata = [];
        $xdata = [];
        $zoom = '';
        foreach ($list as $item) {
            $seriesdata[] = $item['count_user'];
            $xdata[] = $item['_add_time'];
        }
        count($xdata) > $limit && $zoom = $xdata[$limit - 5];
        //会员访问量
        $visit = self::getModelTime($where, self::alias('a')->join('store_visit t', 't.uid=a.uid'), 't.add_time')
            ->where('a.is_promoter', 0)
            ->field(['FROM_UNIXTIME(t.add_time,"%Y-%m-%d") as _add_time', 'count(t.uid) as count_user'])
            ->group('_add_time')
            ->order('_add_time asc')
            ->select();
        count($visit) && $visit = $visit->toArray();
        $visit_data = [];
        $visit_xdata = [];
        $visit_zoom = '';
        foreach ($visit as $item) {
            $visit_data[] = $item['count_user'];
            $visit_xdata[] = $item['_add_time'];
        }
        count($visit_xdata) > $limit && $visit_zoom = $visit_xdata[$limit - 5];
        //多次购物会员数量饼状图
        $count = self::getModelTime($where, self::where('is_promoter', 0))->count();
        $user_count = self::getModelTime($where, self::alias('a')->join('store_order r', 'r.uid=a.uid'), 'a.add_time')
            ->where('a.is_promoter', 0)
            ->where('r.paid', 1)
            ->group('a.uid')
            ->count();
        $shop_xdata = ['多次购买数量占比', '无购买数量占比'];
        $shop_data = [];
        $count > 0 && $shop_data = [
            [
                'value' => bcdiv($user_count, $count, 2) * 100,
                'name' => $shop_xdata[0],
                'itemStyle' => [
                    'color' => '#D789FF',
                ]
            ],
            [
                'value' => bcdiv($count - $user_count, $count, 2) * 100,
                'name' => $shop_xdata[1],
                'itemStyle' => [
                    'color' => '#7EF0FB',
                ]
            ]
        ];
        return compact('seriesdata', 'xdata', 'zoom', 'visit_data', 'visit_xdata', 'visit_zoom', 'shop_data', 'shop_xdata');
    }

    /*
     * 获取用户
     * 积分排行
     * 会员余额排行榜
     * 分销商佣金总额排行榜
     * 购物笔数排行榜
     * 购物金额排行榜
     * 分销商佣金提现排行榜
     * 上月消费排行榜
     * $limit 查询多少条
     * return array
     */
    public static function getUserTop10List($limit = 10, $is_promoter = 0)
    {
        //积分排行
        $integral = self::where('status', 1)
            ->where('is_promoter', $is_promoter)
            ->order('integral desc')
            ->field(['nickname', 'phone', 'integral', 'FROM_UNIXTIME(add_time,"%Y-%m-%d") as add_time'])
            ->limit($limit)
            ->select();
        count($integral) && $integral = $integral->toArray();
        //会员余额排行榜
        $now_money = self::where('status', 1)
            ->where('is_promoter', $is_promoter)
            ->order('now_money desc')
            ->field(['nickname', 'phone', 'now_money', 'FROM_UNIXTIME(add_time,"%Y-%m-%d") as add_time'])
            ->limit($limit)
            ->select();
        count($now_money) && $now_money = $now_money->toArray();
        //购物笔数排行榜
        $shopcount = self::alias('a')
            ->join('store_order r', 'r.uid=a.uid')
            ->where(['r.paid' => 1, 'a.is_promoter' => $is_promoter])
            ->group('r.uid')
            ->field(['a.nickname', 'a.phone', 'count(r.uid) as sum_count', 'FROM_UNIXTIME(a.add_time,"%Y-%m-%d") as add_time'])
            ->order('sum_count desc')
            ->limit($limit)
            ->select();
        count($shopcount) && $shopcount = $shopcount->toArray();
        //购物金额排行榜
        $order = self::alias('a')
            ->join('store_order r', 'r.uid=a.uid')
            ->where(['r.paid' => 1, 'a.is_promoter' => $is_promoter])
            ->group('r.uid')
            ->field(['a.nickname', 'a.phone', 'sum(r.pay_price) as sum_price', 'FROM_UNIXTIME(a.add_time,"%Y-%m-%d") as add_time', 'r.uid'])
            ->order('sum_price desc')
            ->limit($limit)
            ->select();
        count($order) && $order = $order->toArray();
        //上月消费排行
        $lastorder = self::alias('a')
            ->join('store_order r', 'r.uid=a.uid')
            ->where(['r.paid' => 1, 'a.is_promoter' => $is_promoter])
            ->whereTime('r.pay_time', 'last month')
            ->group('r.uid')
            ->field(['a.nickname', 'a.phone', 'sum(r.pay_price) as sum_price', 'FROM_UNIXTIME(a.add_time,"%Y-%m-%d") as add_time', 'r.uid'])
            ->order('sum_price desc')
            ->limit($limit)
            ->select();
        return compact('integral', 'now_money', 'shopcount', 'order', 'lastorder');
    }

    /*
     * 获取 会员业务
     * 会员总余额 会员总积分
     * $where 查询条件
     *
     * return array
     */
    public static function getUserBusinesHeade($where)
    {
        return [
            [
                'name' => '会员总余额',
                'field' => '元',
                'count' => self::getModelTime($where, self::where('status', 1))->sum('now_money'),
                'background_color' => 'layui-bg-cyan',
                'col' => 6,
            ],
            [
                'name' => '会员总积分',
                'field' => '分',
                'count' => self::getModelTime($where, self::where('status', 1))->sum('integral'),
                'background_color' => 'layui-bg-cyan',
                'col' => 6
            ]
        ];
    }

    /*
     * 分销会员头部信息查询获取
     *
     * 分销商总佣金余额
     * 分销商总提现佣金
     * 本月分销商业务佣金
     * 本月分销商佣金提现金额
     * 上月分销商业务佣金
     * 上月分销商佣金提现金额
     * $where array 时间条件
     *
     * return array
     */
    public static function getDistributionBadgeList($where)
    {
        return [
            [
                'name' => '分销商总佣金',
                'field' => '元',
                'count' => self::getModelTime($where, UserBill::where('category', 'now_money')->where('type', 'brokerage'))->where('uid', 'in', function ($query) {
                    $query->name('user')->where('status', 1)->where('is_promoter', 1)->whereOr('spread_uid', 'neq', 0)->field('uid');
                })->sum('number'),
                'background_color' => 'layui-bg-cyan',
                'col' => 3,
            ],
            [
                'name' => '分销商总佣金余额',
                'field' => '元',
                'count' => self::getModelTime($where, self::where('status', 1)->where('is_promoter', 1))->sum('now_money'),
                'background_color' => 'layui-bg-cyan',
                'col' => 3,
            ],
            [
                'name' => '分销商总提现佣金',
                'field' => '元',
                'count' => self::getModelTime($where, UserExtract::where('status', 1))->sum('extract_price'),
                'background_color' => 'layui-bg-cyan',
                'col' => 3,
            ],
            [
                'name' => '本月分销商业务佣金',
                'field' => '元',
                'count' => self::getModelTime(['data' => 'month'], UserBill::where('category', 'now_money')->where('type', 'brokerage'))
                    ->where('uid', 'in', function ($query) {
                        $query->name('user')->where('status', 1)->where('is_promoter', 1)->whereOr('spread_uid', 'neq', 0)->field('uid');
                    })->sum('number'),
                'background_color' => 'layui-bg-cyan',
                'col' => 3,
            ],
            [
                'name' => '本月分销商佣金提现金额',
                'field' => '元',
                'count' => self::getModelTime(['data' => 'month'], UserExtract::where('status', 1))
                    ->where('uid', 'in', function ($query) {
                        $query->name('user')->where('status', 1)->where('is_promoter', 1)->field('uid');
                    })->sum('extract_price'),
                'background_color' => 'layui-bg-cyan',
                'col' => 4,
            ],
            [
                'name' => '上月分销商业务佣金',
                'field' => '元',
                'count' => self::getOldDate(['data' => 'year'], UserBill::where('category', 'now_money')->where('uid', 'in', function ($query) {
                    $query->name('user')->where('status', 1)->where('is_promoter', 1)->whereOr('spread_uid', 'neq', 0)->field('uid');
                })->where('type', 'brokerage'))->sum('number'),
                'background_color' => 'layui-bg-cyan',
                'col' => 4,
            ],
            [
                'name' => '上月分销商佣金提现金额',
                'field' => '元',
                'count' => self::getOldDate(['data' => 'year'], UserBill::where('category', 'now_money')->where('uid', 'in', function ($query) {
                    $query->name('user')->where('status', 1)->where('is_promoter', 1)->whereOr('spread_uid', 'neq', 0)->field('uid');
                })->where('type', 'brokerage'))->sum('number'),
                'background_color' => 'layui-bg-cyan',
                'col' => 4,
            ],
        ];
    }

    /*
     * 分销会员
     * 分销数量 饼状图
     * 分销商会员访问量 曲线
     * 获取购物会员人数趋势图 曲线
     * 多次购物分销会员数量 饼状图
     * $where array 条件
     * $limit int n条数据后出拖动条
     * return array
     */
    public static function getUserDistributionChart($where, $limit = 20)
    {
        //分销数量
        $fenbu_user = self::getModelTime($where, new self)->field(['count(uid) as num'])->group('is_promoter')->select();
        count($fenbu_user) && $fenbu_user = $fenbu_user->toArray();
        $sum_user = 0;
        $fenbu_data = [];
        $fenbu_xdata = ['分销商', '非分销商'];
        $color = ['#81BCFE', '#91F3FE'];
        foreach ($fenbu_user as $item) {
            $sum_user += $item['num'];
        }
        foreach ($fenbu_user as $key => $item) {
            $value['value'] = bcdiv($item['num'], $sum_user, 2) * 100;
            $value['name'] = isset($fenbu_xdata[$key]) ? $fenbu_xdata[$key] . '  %' . $value['value'] : '';
            $value['itemStyle']['color'] = $color[$key];
            $fenbu_data[] = $value;
        }
        //分销商会员访问量
        $visit = self::getModelTime($where, self::alias('a')->join('store_visit t', 't.uid=a.uid'), 't.add_time')
            ->where('a.is_promoter', 1)
            ->field(['FROM_UNIXTIME(t.add_time,"%Y-%m-%d") as _add_time', 'count(t.uid) as count_user'])
            ->group('_add_time')
            ->order('_add_time asc')
            ->select();
        count($visit) && $visit = $visit->toArray();
        $visit_data = [];
        $visit_xdata = [];
        $visit_zoom = '';
        foreach ($visit as $item) {
            $visit_data[] = $item['count_user'];
            $visit_xdata[] = $item['_add_time'];
        }
        count($visit_xdata) > $limit && $visit_zoom = $visit_xdata[$limit - 5];
        //获取购物会员人数趋势图
        $list = self::getModelTime($where, self::where('a.status', 1)->alias('a')->join('store_order r', 'r.uid=a.uid'), 'a.add_time')
            ->where(['r.paid' => 1, 'a.is_promoter' => 1])
            ->where('a.add_time', 'neq', 0)
            ->field(['FROM_UNIXTIME(a.add_time,"%Y-%m-%d") as _add_time', 'count(r.uid) as count_user'])
            ->group('_add_time')
            ->order('_add_time asc')
            ->select();
        count($list) && $list = $list->toArray();
        $seriesdata = [];
        $xdata = [];
        $zoom = '';
        foreach ($list as $item) {
            $seriesdata[] = $item['count_user'];
            $xdata[] = $item['_add_time'];
        }
        count($xdata) > $limit && $zoom = $xdata[$limit - 5];
        //多次购物分销会员数量饼状图
        $count = self::getModelTime($where, self::where('is_promoter', 1))->count();
        $user_count = self::getModelTime($where, self::alias('a')
            ->join('store_order r', 'r.uid=a.uid'), 'a.add_time')
            ->where('a.is_promoter', 1)
            ->where('r.paid', 1)
            ->group('a.uid')
            ->count();
        $shop_xdata = ['多次购买数量占比', '无购买数量占比'];
        $shop_data = [];
        $count > 0 && $shop_data = [
            [
                'value' => bcdiv($user_count, $count, 2) * 100,
                'name' => $shop_xdata[0] . $user_count . '人',
                'itemStyle' => [
                    'color' => '#D789FF',
                ]
            ],
            [
                'value' => bcdiv($count - $user_count, $count, 2) * 100,
                'name' => $shop_xdata[1] . ($count - $user_count) . '人',
                'itemStyle' => [
                    'color' => '#7EF0FB',
                ]
            ]
        ];
        return compact('fenbu_data', 'fenbu_xdata', 'visit_data', 'visit_xdata', 'visit_zoom', 'seriesdata', 'xdata', 'zoom', 'shop_xdata', 'shop_data');
    }

    /*
     * 分销商佣金提现排行榜
     * 分销商佣金总额排行榜
     * $limit 截取条数
     * return array
     */
    public static function getUserDistributionTop10List($limit)
    {
        //分销商佣金提现排行榜
        $extract = self::alias('a')
            ->join('user_extract t', 'a.uid=t.uid')
            ->where(['t.status' => 1, 'a.is_promoter' => 1])
            ->group('t.uid')
            ->field(['a.nickname', 'a.phone', 'sum(t.extract_price) as sum_price', 'FROM_UNIXTIME(a.add_time,"%Y-%m-%d") as add_time', 't.uid'])
            ->order('sum_price desc')
            ->limit($limit)
            ->select();
        count($extract) && $extract = $extract->toArray();
        //分销商佣金总额排行榜
        $commission = UserBill::alias('l')
            ->join('user a', 'l.uid=a.uid')
            ->where(['l.status' => '1', 'l.category' => 'now_money', 'l.type' => 'brokerage', 'a.is_promoter' => 1])
            ->group('l.uid')
            ->field(['a.nickname', 'a.phone', 'sum(number) as sum_number', 'FROM_UNIXTIME(a.add_time,"%Y-%m-%d") as add_time'])
            ->order('sum_number desc')
            ->limit($limit)
            ->select();
        count($commission) && $commission = $commission->toArray();
        return compact('extract', 'commission');
    }

    public static function getSpreadList($uid, $page, $limit)
    {
        $list = self::where(['spread_uid' => $uid])->field(['uid', 'nickname', 'now_money', 'gold_num', 'add_time'])
            ->order('uid desc')->page((int)$page, (int)$limit)->select();
        count($list) && $list = $list->toArray();
        $isPhoneMenu = is_phone_menu();
        foreach ($list as &$item) {
            if (check_phone($item['nickname']) && !$isPhoneMenu) {
                $item['nickname'] = substr_replace($item['nickname'], '****', 3, 4);
            }
            $item['add_time'] = date('Y-m-d H', $item['add_time']);
        }
        return $list;
    }

    /**
     * 设置推广人查询条件
     * @param $where
     * @param string $alias
     * @param int $spread_type
     * @param null $model
     * @return $this
     */
    public static function setSpreadBadgeWhere($where, $alias = '', $spread_type = 0, $model = null)
    {
        $model = is_null($model) ? new self() : $model;
        $alias = $alias ? $alias . '.' : '';
        if ($spread_type) $where['spread_type'] = $spread_type;
        if ($where['nickname'] && ($uids = self::where('nickname|phone|uid', 'like', "%$where[nickname]%")->column('uid'))) {
            $model = $model->where($alias . 'spread_uid', 'in', $uids);
        }
        if ($where['phone']) $model = $model->where("{$alias}nickname|{$alias}phone", 'like', "%$where[phone]%");
        if ($where['start_time'] && $where['stop_time']) {
            $model = $model->whereTime("{$alias}add_time", 'between', [$where['start_time'], $where['stop_time']]);
        }
        $storeBrokerageStatu = SystemConfigService::get('store_brokerage_statu') ?: 1;//获取后台分销类型
        if ($storeBrokerageStatu == 1) {
            $model = $model->where("{$alias}is_promoter", 1);
        }
        return $model;
    }

    public static function getextractPrice($uid, $where = [])
    {
        if (is_array($uid)) {
            if (!count($uid)) return 0;
        } else
            $uid = [$uid];
        $brokerage = UserBill::getBrokerage($uid, 'now_money', 'brokerage', $where);//获取总佣金
        $return = UserBill::getReturnBrokerage($uid, 'now_money', 'brokerage_return', $where);//获取返还佣金
        $brokerage = bcsub($brokerage, $return, 2);
        $recharge = UserBill::getBrokerage($uid, 'now_money', 'recharge', $where);//累计充值
        $extractTotalPrice = UserExtract::userExtractTotalPrice($uid, 1, $where);//累计提现
        if ($brokerage > $extractTotalPrice) {
            $orderYuePrice = self::getModelTime($where, StoreOrder::where('uid', 'in', $uid)->where(['paid' => 1, 'refund_status' => 0, 'is_del' => 0])->where('type', 'in', '0,1'))->sum('pay_price');//余额累计消费
            $systemAdd = UserBill::getBrokerage($uid, 'now_money', 'system_add', $where);//后台添加余额
            $yueCount = bcadd($recharge, $systemAdd, 2);// 后台添加余额 + 累计充值  = 非佣金的总金额
            $orderYuePrice = $yueCount > $orderYuePrice ? 0 : bcsub($orderYuePrice, $yueCount, 2);// 余额累计消费（使用佣金消费的金额）
            $brokerage = bcsub($brokerage, $extractTotalPrice, 2);//减去已提现金额
            $extract_price = UserExtract::userExtractTotalPrice($uid, 0, $where);
            $brokerage = $extract_price < $brokerage ? bcsub($brokerage, $extract_price, 2) : 0;//减去审核中的提现金额
            $brokerage = $brokerage > $orderYuePrice ? bcsub($brokerage, $orderYuePrice, 2) : 0;//减掉余额支付
        } else {
            $brokerage = 0;
        }
        $num = (float)bcsub($brokerage, $extractTotalPrice, 2);
        return $num > 0 ? $num : 0;//可提现
    }

    /**
     * 获取推广人列表
     * @param array $where 查询条件
     * @return array
     * */
    public static function SpreadList($where)
    {
        $model = self::setSpreadBadgeWhere($where)->field('phone,uid,nickname,add_time,spread_uid,is_promoter,is_senior');
        if ($where['export']) $data = $model->select();
        else $data = $model->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['spread_nickname'] = self::where('uid', $item['spread_uid'])->value('nickname');
            $item['spread_name'] = '普通';
            //直推订单
            $uids = self::where('spread_uid', $item['uid'])->column('uid');
            if (count($uids)) {
                $item['sum_pay_price'] = StoreOrder::whereIn('uid', $uids)->where(['paid' => 1, 'refund_status' => 0, 'is_del' => 0])->where('type', 'in', '0,1')->sum('pay_price');
                $ids = self::whereIn('spread_uid', $uids)->column('uid');
                if (count($ids)) {
                    $item['pay_price'] = StoreOrder::whereIn('uid', $ids)->where(['paid' => 1, 'refund_status' => 0, 'is_del' => 0])->where('type', 'in', '0,1')->sum('pay_price');
                }
            } else {
                $item['sum_pay_price'] = 0;
                $item['pay_price'] = 0;
            }
            unset($ids, $uids);
            $item['rake_back'] = UserBill::getCommissionAmount($item['uid']);
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }

        if ($where['export']) self::SaveExcel($data);
        $count = self::setSpreadBadgeWhere($where)->count();
        return compact('data', 'count');
    }

    /*
   * 保存并下载excel
   * $list array
   * return
   */
    public static function SaveExcel($list)
    {
        $export = [];
        foreach ($list as $index => $item) {
            $export[] = [
                $item['spread_name'],
                $item['nickname'],
                $item['spread_nickname'],
                $item['phone'],
                $item['add_time'],
                $item['sum_pay_price'],
                $item['rake_back']
            ];
        }
        $filename = '推广人列表导出' . time() . '.xlsx';
        $head = ['推广人身份', '昵称', '所属上级', '手机号码', '加入时间', '订单金额', '佣金'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }


    public static function guestWhere($where, $guest, $model = null)
    {
        if ($model == null) $model = new self;
        if (isset($where['guest_name']) && $where['guest_name'] != '') $model = $model->where('nickname|uid', 'LIKE', "%$where[guest_name]%");
        $model = $model->where('uid', 'IN', $guest->guest);
        return $model;
    }

    //分销列表

    public static function setSpreadWhere($where = [], $alias = 'u', $model = null)
    {
        $model = is_null($model) ? new  self() : $model;
        if ($alias) {
            $model = $model->alias($alias)->order("u.uid desc");
            $alias .= '.';
        }
        $status = (int)SystemConfigService::get('store_brokerage_statu');
        if ($status == 1) {
            $model = $model->where("{$alias}is_promoter", 1);
        }
        if ($where['nickname'] !== '') $model = $model->where("{$alias}nickname|{$alias}uid|{$alias}phone", 'LIKE', "%$where[nickname]%");
        if ((isset($where['start_time']) && isset($where['end_time'])) && $where['start_time'] !== '' && $where['end_time'] !== '') {
            $model = $model->where("{$alias}add_time", 'between', [strtotime($where['start_time']), strtotime($where['end_time'])]);
        }
        if (isset($where['order']) && $where['order'] != '') $model = $model->order($where['order']);
        if (isset($where['is_time']) && isset($where['data']) && $where['data']) $model = self::getModelTime($where, $model, $alias . 'add_time');
        return $model;
    }

    /**
     * 获取推广人数
     * @param $uid //用户的uid
     * @param int $spread
     * $spread 0 一级推广人数  1 二级推广人数
     * @return int|string
     */
    public static function getUserSpreadUidCount($uid, $spread = 1)
    {
        $userStair = self::where('spread_uid', $uid)->column('uid', 'uid');//获取一级推家人
        if ($userStair) {
            if (!$spread) return count($userStair);//返回一级推人人数
            else return self::where('spread_uid', 'IN', implode(',', $userStair))->count();//二级推荐人数
        } else return 0;
    }

    /**
     * 获取推广人的订单
     * @param $uid
     * @param int $spread
     * $spread 0 一级推广总订单  1 所有推广总订单
     * @return int|string
     */
    public static function getUserSpreadOrderCount($uid, $spread = 1)
    {
        $userStair = self::where('spread_uid', $uid)->column('uid', 'uid');//获取一级推家人uid
        if ($userStair) {
            if (!$spread) {
                return StoreOrder::where('uid', 'IN', implode(',', $userStair))->where(['paid' => 1, 'refund_status' => 0, 'is_del' => 0])->where('type', 'in', '0,1')->count();//获取一级推广人订单数
            } else {
                $userSecond = self::where('spread_uid', 'IN', implode(',', $userStair))->column('uid', 'uid');//二级推广人的uid
                if ($userSecond) {
                    return StoreOrder::where('uid', 'IN', implode(',', $userSecond))->where(['paid' => 1, 'refund_status' => 0, 'is_del' => 0])->where('type', 'in', '0,1')->count();//获取二级推广人订单数
                } else return 0;
            }
        } else return 0;
    }

    /**
     * 获取分销用户
     * @param array $where
     * @return array
     */
    public static function agentSystemPage($where = array())
    {
        $where['is_time'] = 1;
        $model = self::setSpreadWhere($where, 'u');
        $status = SystemConfigService::get('store_brokerage_statu');
        if (isset($where['excel']) && $where['excel'] == 1) {
            $list = $model->field(['u.uid', 'u.phone', 'u.nickname', 'u.brokerage_price'])->select()->toArray();
            $export = [];
            foreach ($list as $index => $item) {
                $Listuids = self::getModelTime($where, self::where('spread_uid', $item['uid']))->field('uid')->select();
                $newUids = [];
                foreach ($Listuids as $val) {
                    $newUids[] = $val['uid'];
                }
                $uids = $newUids;
                unset($uid, $newUids);
                $item['spread_count'] = count($uids);
                if (count($uids)) {
                    $ListUidTwo = self::where('spread_uid', 'in', $uids)->field('uid')->select();
                    $newUids = [];
                    foreach ($ListUidTwo as $val) {
                        $newUids[] = $val['uid'];
                    }
                    $uidTwo = $newUids;
                    unset($uid, $newUids);
                    $uids = array_merge($uids, $uidTwo);
                    $uids = array_unique($uids);
                    $uids = array_merge($uids);
                }
                $item['extract_sum_price'] = self::getModelTime($where, UserExtract::where('uid', $item['uid']))->sum('extract_price');
                $item['extract_count_price'] = UserExtract::getUserCountPrice($item['uid']);//累计提现金额
                $item['extract_count_num'] = UserExtract::getUserCountNum($item['uid'], $where);//提现次数
                $item['order_price'] = count($uids) ? StoreOrder::where('uid', 'in', $uids)->where(['paid' => 1, 'refund_status' => 0, 'is_del' => 0])->where('type', 'in', '0,1')->sum('pay_price') : 0;//订单金额
                $item['order_count'] = count($uids) ? StoreOrder::where('uid', 'in', $uids)->where(['paid' => 1, 'refund_status' => 0, 'is_del' => 0])->where('type', 'in', '0,1')->count() : 0;//订单数量
                //可提现佣金
                $item['new_money'] = $item['brokerage_price'];
                //总共佣金
                $income = self::getModelTime($where, UserBill::where(['uid' => $item['uid'], 'category' => 'now_money', 'type' => 'brokerage', 'pm' => 1, 'status' => 1]))->sum('number');
                $return = self::getModelTime($where, UserBill::where(['uid' => $item['uid'], 'category' => 'now_money', 'type' => 'brokerage_return', 'pm' => 0, 'status' => 1]))->sum('number');

                $item['brokerage_money'] = bcsub($income, $return, 2);
                $item['spread_name'] = '暂无';
                if ($spread_uid = self::where('uid', $item['uid'])->value('spread_uid')) {
                    if ($user = self::where('uid', $spread_uid)->field(['uid', 'nickname'])->find()) {
                        $item['spread_name'] = $user['nickname'] . '/' . $user['uid'];
                    }
                }
                $export[] = [
                    $item['uid'],
                    $item['nickname'],
                    $item['phone'],
                    $item['spread_count'],
                    $item['order_count'],
                    $item['order_price'],
                    $item['brokerage_money'],
                    $item['extract_count_price'],
                    $item['extract_count_num'],
                    $item['new_money'],
                    $item['spread_name']
                ];
            }
            $filename = '推广用户导出' . time() . '.xlsx';
            $head = ['用户编号', '昵称', '电话号码', '推广用户数量', '订单数量', '推广订单金额', '佣金金额', '已提现金额', '提现次数', '未提现金额', '上级推广人'];
            PhpSpreadsheetService::outdata($filename, $export, $head);
        }
        $data = $model->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            if ((int)$status == 2) $item['is_show'] = false;
            else $item['is_show'] = true;
            $Listuids = self::getModelTime($where, self::where('spread_uid', $item['uid']))->field('uid')->select();
            $newUids = [];
            foreach ($Listuids as $val) {
                $newUids[] = $val['uid'];
            }
            $uids = $newUids;
            unset($uid, $newUids);
            $item['spread_count'] = count($uids);
            if (count($uids)) {
                $ListUidTwo = self::where('spread_uid', 'in', $uids)->field('uid')->select();
                $newUids = [];
                foreach ($ListUidTwo as $val) {
                    $newUids[] = $val['uid'];
                }
                $uidTwo = $newUids;
                unset($uid, $newUids);
                $uids = array_merge($uids, $uidTwo);
                $uids = array_unique($uids);
                $uids = array_merge($uids);
            }
            $item['extract_sum_price'] = self::getModelTime($where, UserExtract::where('uid', $item['uid']))->sum('extract_price');
            $item['extract_count_price'] = UserExtract::getUserCountPrice($item['uid']);//累计提现金额
            $item['extract_count_num'] = UserExtract::getUserCountNum($item['uid'], $where);//提现次数
            $item['order_price'] = count($uids) ? StoreOrder::where('uid', 'in', $uids)->where(['paid' => 1, 'refund_status' => 0, 'is_del' => 0])->where('type', 'in', '0,1')->sum('pay_price') : 0;//订单金额
            $item['order_count'] = count($uids) ? StoreOrder::where('uid', 'in', $uids)->where(['paid' => 1, 'refund_status' => 0, 'is_del' => 0])->where('type', 'in', '0,1')->count() : 0;//订单数量
            $item['spread_name'] = '暂无';
            if ($spread_uid = self::where('uid', $item['uid'])->value('spread_uid')) {
                if ($user = self::where('uid', $spread_uid)->field(['uid', 'nickname'])->find()) {
                    $item['spread_name'] = $user['nickname'] . '/' . $user['uid'];
                }
            }
            $income = self::getModelTime($where, UserBill::where(['uid' => $item['uid'], 'category' => 'now_money', 'type' => 'brokerage', 'pm' => 1, 'status' => 1]))->sum('number');
            $return = self::getModelTime($where, UserBill::where(['uid' => $item['uid'], 'category' => 'now_money', 'type' => 'brokerage_return', 'pm' => 0, 'status' => 1]))->sum('number');

            //总共佣金
            $item['brokerage_money'] = bcsub($income, $return, 2);
            //可提现佣金
            $item['new_money'] = $item['brokerage_price'];
        }
        $count = self::setSpreadWhere($where)->count();
        return compact('data', 'count');
    }

    public static function getSpreadBadge($where)
    {
        $where['is_time'] = 1;
        $listuids = self::setSpreadWhere($where)->field('u.uid')->select();
        $newUids = [];
        foreach ($listuids as $item) {
            $newUids[] = $item['uid'];
        }
        $uids = $newUids;
        unset($uid, $newUids);
        //分销员人数
        $data['sum_count'] = count($uids);
        $data['spread_sum'] = 0;
        $data['order_count'] = 0;
        $data['pay_price'] = 0;
        $data['number'] = 0;
        $data['extract_count'] = 0;
        $data['extract_price'] = 0;
        if ($data['sum_count']) {
            //发展会员人数
            $data['spread_sum'] = self::where('spread_uid', 'in', $uids)->count();
            //订单总数
            $data['order_count'] = StoreOrder::where('uid', 'in', $uids)->where(['paid' => 1, 'refund_status' => 0, 'is_del' => 0])->where('type', 'in', '0,1')->count();
            //订单金额
            $data['pay_price'] = StoreOrder::where('uid', 'in', $uids)->where(['paid' => 1, 'refund_status' => 0, 'is_del' => 0])->where('type', 'in', '0,1')->sum('pay_price');
            //可提现金额
            $data['number'] = self::where('uid', 'in', $uids)->sum('brokerage_price');
            //提现次数
            $data['extract_count'] = UserExtract::where('uid', 'in', $uids)->count();
            //获取某个用户可提现金额
            $data['extract_price'] = self::getextractPrice($uids, $where);
        }

        return [
            [
                'name' => '分销员数量',
                'field' => '人',
                'count' => $data['sum_count'],
                'background_color' => 'layui-bg-cyan',
                'col' => 2,
            ],
            [
                'name' => '发展人数',
                'field' => '人',
                'count' => $data['spread_sum'],
                'background_color' => 'layui-bg-cyan',
                'col' => 2,
            ],
            [
                'name' => '总订单数',
                'field' => '单',
                'count' => $data['order_count'],
                'background_color' => 'layui-bg-cyan',
                'col' => 2,
            ],
            [
                'name' => '总订单金额',
                'field' => '元',
                'count' => $data['pay_price'],
                'background_color' => 'layui-bg-cyan',
                'col' => 2,
            ],
            [
                'name' => '可提现金额',
                'field' => '元',
                'count' => $data['number'],
                'background_color' => 'layui-bg-cyan',
                'col' => 2,
            ],
            [
                'name' => '提现次数',
                'field' => '次',
                'count' => $data['extract_count'],
                'background_color' => 'layui-bg-cyan',
                'col' => 2,
            ]
        ];
    }

    public static function getStairList($where)
    {
        if (!isset($where['uid'])) return [];
        $data = self::setSairWhere($where, new self())->order('add_time desc')->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['spread_count'] = self::where('spread_uid', $item['uid'])->count();
            $item['order_count'] = StoreOrder::where('uid', $item['uid'])->where(['paid' => 1, 'refund_status' => 0, 'is_del' => 0])->where('type', 'in', '0,1')->count();
            $item['promoter_name'] = $item['is_promoter'] ? '是' : '否';
            $item['add_time'] = date("Y-m-d H:i:s", $item['add_time']);
        }
        $count = self::setSairWhere($where, new User())->count();
        return compact('data', 'count');
    }

    /**
     * 设置查询条件
     * @param array $where
     * @param object $model
     * @param string $alias
     * */
    public static function setSairWhere($where, $model = null, $alias = '')
    {
        $model = $model === null ? new self() : $model;
        if (!isset($where['uid'])) return $model;
        if ($alias) {
            $model = $model->alias($alias);
            $alias .= '.';
        }
        if (isset($where['type'])) {
            switch ((int)$where['type']) {
                case 1:
                    $uids = self::where('spread_uid', $where['uid'])->column('uid');
                    if (count($uids))
                        $model = $model->where("{$alias}uid", 'in', $uids);
                    else
                        $model = $model->where("{$alias}uid", 0);
                    break;
                case 2:
                    $uids = self::where('spread_uid', $where['uid'])->column('uid');
                    if (count($uids))
                        $spread_uid_two = self::where('spread_uid', 'in', $uids)->column('uid');
                    else
                        $spread_uid_two = [0];
                    if (count($spread_uid_two))
                        $model = $model->where("{$alias}uid", 'in', $spread_uid_two);
                    else
                        $model = $model->where("{$alias}uid", 0);
                    break;
                default:
                    $uids = self::where('spread_uid', $where['uid'])->column('uid');
                    if (count($uids)) {
                        if ($spread_uid_two = self::where('spread_uid', 'in', $uids)->column('uid')) {
                            $uids = array_merge($uids, $spread_uid_two);
                            $uids = array_unique($uids);
                            $uids = array_merge($uids);
                        }
                        $model = $model->where("{$alias}uid", 'in', $uids);
                    } else
                        $model = $model->where("{$alias}uid", 0);
                    break;
            }
        }
        if (isset($where['data']) && $where['data']) $model = self::getModelTime($where, $model, "{$alias}add_time");
        if (isset($where['nickname']) && $where['nickname']) $model = $model->where("{$alias}phone|{$alias}nickname|{$alias}name|{$alias}uid", 'LIKE', "%$where[nickname]%");
        return $model->where($alias . 'status', 1);
    }

    public static function getSairBadge($where)
    {
        $data['number'] = self::setSairWhere($where, new self())->count();
        $where['type'] = 1;
        $data['one_number'] = self::setSairWhere($where, new self())->count();
        $where['type'] = 2;
        $data['two_number'] = self::setSairWhere($where, new self())->count();
        $col = $data['two_number'] > 0 ? 4 : 6;
        return [
            [
                'name' => '总人数',
                'field' => '人',
                'count' => $data['number'],
                'background_color' => 'layui-bg-cyan',
                'col' => $col,
            ],
            [
                'name' => '一级人数',
                'field' => '人',
                'count' => $data['one_number'],
                'background_color' => 'layui-bg-cyan',
                'col' => $col,
            ],
            [
                'name' => '二级人数',
                'field' => '人',
                'count' => $data['two_number'],
                'background_color' => 'layui-bg-cyan',
                'col' => $col,
            ],
        ];
    }

    /**
     * 推广订单
     * @param array $where
     * @return array
     * */
    public static function getStairOrderList($where)
    {
        if (!isset($where['uid'])) return [];
        $data = self::setSairOrderWhere($where, new StoreOrder())->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        $Info = self::where('uid', $where['uid'])->find();
        foreach ($data as &$item) {
            $userInfo = self::where('uid', $item['uid'])->find();
            $item['user_info'] = '';
            $item['avatar'] = '';
            if ($userInfo) {
                $item['user_info'] = $userInfo->nickname . '|' . ($userInfo->phone ? $userInfo->phone : '');
                $item['avatar'] = $userInfo->avatar;
            }
            $item['spread_info'] = $Info->nickname . "|" . ($Info->phone ? $Info->phone . "|" : '') . $Info->uid;
            $brokerage = UserBill::where(['category' => 'now_money', 'type' => 'brokerage', 'link_id' => $item['id'], 'uid' => $where['uid']])->value('number');
            $brokerage_return = UserBill::where(['category' => 'now_money', 'type' => 'brokerage_return', 'link_id' => $item['id'], 'uid' => $where['uid']])->value('number');
            $item['number_price'] = bcsub($brokerage, $brokerage_return, 2);
            $item['_pay_time'] = date('Y-m-d H:i:s', $item['pay_time']);
            $item['_add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['take_time'] = ($change_time = StoreOrderStatus::where(['change_type' => 'user_take_delivery', 'oid' => $item['id']])->value('change_time')) ?
                date('Y-m-d H:i:s', $change_time) : '暂无';
        }
        $count = self::setSairOrderWhere($where, new StoreOrder())->count();
        return compact('data', 'count');
    }

    public static function setSairOrderWhere($where, $model = null, $alias = '')
    {
        $model = $model === null ? new self() : $model;
        if (!isset($where['uid'])) return $model;
        if ($alias) {
            $model = $model->alias($alias);
            $alias .= '.';
        }
        if (isset($where['type'])) {
            switch ((int)$where['type']) {
                case 1:
                    $uids = self::where('spread_uid', $where['uid'])->column('uid');
                    if (count($uids))
                        $model = $model->where("{$alias}uid", 'in', $uids);
                    else
                        $model = $model->where("{$alias}uid", 0);
                    break;
                case 2:
                    $uids = self::where('spread_uid', $where['uid'])->column('uid');
                    if (count($uids))
                        $spread_uid_two = self::where('spread_uid', 'in', $uids)->column('uid');
                    else
                        $spread_uid_two = [0];
                    if (count($spread_uid_two))
                        $model = $model->where("{$alias}uid", 'in', $spread_uid_two);
                    else
                        $model = $model->where("{$alias}uid", 0);
                    break;
                default:
                    $uids = self::where('spread_uid', $where['uid'])->column('uid');
                    if (count($uids)) {
                        if ($spread_uid_two = self::where('spread_uid', 'in', $uids)->column('uid')) {
                            $uids = array_merge($uids, $spread_uid_two);
                            $uids = array_unique($uids);
                            $uids = array_merge($uids);
                        }
                        $model = $model->where("{$alias}uid", 'in', $uids);
                    } else
                        $model = $model->where("{$alias}uid", 0);
                    break;
            }
        }
        if (isset($where['data']) && $where['data']) $model = self::getModelTime($where, $model, "{$alias}add_time");
        return $model->where("{$alias}is_del", 0)->where($alias . 'paid', 1)->where($alias . 'refund_status', 0)->where($alias . 'type', 'in', '0,1')->order($alias . 'add_time desc');
    }

    /*
     *  推广订单统计
     * @param array $where
     * @return array
     * */
    public static function getStairOrderBadge($where)
    {
        if (!isset($where['uid'])) return [];
        $data['order_count'] = self::setSairOrderWhere($where, new StoreOrder())->count();
        $data['order_price'] = self::setSairOrderWhere($where, new StoreOrder())->sum('pay_price');
        $ids = self::setSairOrderWhere($where, new StoreOrder())->where(['paid' => 1, 'is_del' => 0, 'refund_status' => 0])->where('type', 'in', '0,1')->column('id');
        $data['number_price'] = 0;
        if (count($ids)) {
            $brokerage = UserBill::where(['category' => 'now_money', 'type' => 'brokerage', 'uid' => $where['uid']])->where('link_id', 'in', $ids)->sum('number');
            $brokerage_return = UserBill::where(['category' => 'now_money', 'type' => 'brokerage_return', 'uid' => $where['uid']])->where('link_id', 'in', $ids)->sum('number');
            $data['number_price'] = bcsub($brokerage, $brokerage_return, 2);
        }
        $where['type'] = 1;
        $data['one_price'] = self::setSairOrderWhere($where, new StoreOrder())->sum('pay_price');
        $data['one_count'] = self::setSairOrderWhere($where, new StoreOrder())->count();
        $where['type'] = 2;
        $data['two_price'] = self::setSairOrderWhere($where, new StoreOrder())->sum('pay_price');
        $data['two_count'] = self::setSairOrderWhere($where, new StoreOrder())->count();
        return [
            [
                'name' => '总金额',
                'field' => '元',
                'count' => $data['order_price'],
                'background_color' => 'layui-bg-cyan',
                'col' => 3,
            ],
            [
                'name' => '订单总数',
                'field' => '单',
                'count' => $data['order_count'],
                'background_color' => 'layui-bg-cyan',
                'col' => 3,
            ],
            [
                'name' => '返佣总金额',
                'field' => '元',
                'count' => $data['number_price'],
                'background_color' => 'layui-bg-cyan',
                'col' => 3,
            ],
            [
                'name' => '一级总金额',
                'field' => '元',
                'count' => $data['one_price'],
                'background_color' => 'layui-bg-cyan',
                'col' => 3,
            ],
            [
                'name' => '一级订单数',
                'field' => '单',
                'count' => $data['one_count'],
                'background_color' => 'layui-bg-cyan',
                'col' => 3,
            ],
            [
                'name' => '二级总金额',
                'field' => '元',
                'count' => $data['two_price'],
                'background_color' => 'layui-bg-cyan',
                'col' => 3,
            ],
            [
                'name' => '二级订单数',
                'field' => '单',
                'count' => $data['two_count'],
                'background_color' => 'layui-bg-cyan',
                'col' => 3,
            ],
        ];
    }

}
