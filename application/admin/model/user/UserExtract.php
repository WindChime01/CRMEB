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

use app\admin\model\user\User;
use app\admin\model\user\UserBill;
use app\admin\model\wechat\WechatUser;
use app\admin\model\merchant\Merchant;
use app\admin\model\merchant\MerchantBill;
use think\Url;
use traits\ModelTrait;
use basic\ModelBasic;
use service\WechatTemplateService;
use app\wap\model\routine\RoutineTemplate;
use service\SystemConfigService;

/**
 * 用户提现管理 model
 * Class UserExtract
 * @package app\admin\model\user
 */
class UserExtract extends ModelBasic
{
    use ModelTrait;

    /**条件处理
     * @param $where
     * @return DataDownload
     */
    public static function setWhere($where)
    {
        $model = new self();
        $model = $model->alias('a');
        $time['data'] = '';
        if (isset($where['start_time']) && isset($where['end_time']) && $where['start_time'] != '' && $where['end_time'] != '') {
            $time['data'] = $where['start_time'] . ' - ' . $where['end_time'];
            $model = $model->getModelTime($time, $model, 'a.add_time');
        }
        if (isset($where['nireid']) && $where['nireid']) {
            $model = $model->where('a.real_name|a.id|b.nickname|a.bank_code|a.alipay_code|a.wechat', 'like', "%$where[nireid]%");
        }
        if (isset($where['status']) && $where['status'] != '') $model = $model->where('a.status', $where['status']);
        if (isset($where['extract_type']) && $where['extract_type'] != '') $model = $model->where('a.extract_type', $where['extract_type']);
        $model = $model->join('__USER__ b', 'b.uid=a.uid', 'LEFT');
        return $model->where('a.mer_id', 0);
    }


    public static function get_user_extract_list($where)
    {
        $data = self::setWhere($where)->field('a.*,b.nickname')->order('a.id DESC')->page((int)$where['page'], (int)$where['limit'])->select();
        foreach ($data as $key => &$datum) {
            $datum['name'] = $datum['nickname'] . '/' . $datum['uid'];
            $datum['add_time'] = date('Y-m-d H:i:s', $datum['add_time']);
            $datum['fail_time'] = date('Y-m-d H:i:s', $datum['fail_time']);
        }
        $data = count((array)$data) ? $data->toArray() : [];
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**条件处理
     * @param $where
     * @return DataDownload
     */
    public static function setMerWhere($where)
    {
        $model = new self();
        $model = $model->alias('a');
        $time['data'] = '';
        if (isset($where['start_time']) && isset($where['end_time']) && $where['start_time'] != '' && $where['end_time'] != '') {
            $time['data'] = $where['start_time'] . ' - ' . $where['end_time'];
            $model = $model->getModelTime($time, $model, 'a.add_time');
        }
        if (isset($where['nireid']) && $where['nireid']) {
            $model = $model->where('a.real_name|a.id|m.mer_name|a.bank_code|a.alipay_code|a.wechat', 'like', "%$where[nireid]%");
        }
        if (isset($where['status']) && $where['status'] != '') $model = $model->where('a.status', $where['status']);
        if (isset($where['mer_id']) && $where['mer_id'] != '') $model = $model->where('a.mer_id', '>', 0);
        if (isset($where['extract_type']) && $where['extract_type'] != '') $model = $model->where('a.extract_type', $where['extract_type']);
        $model = $model->join('Merchant m', 'm.id=a.mer_id', 'LEFT');
        return $model;
    }


    public static function get_mer_user_extract_list($where)
    {
        $data = self::setMerWhere($where)->field('a.*,m.mer_name')->order('a.id DESC')->page((int)$where['page'], (int)$where['limit'])->select();
        foreach ($data as $key => &$datum) {
            $datum['name'] = $datum['mer_name'] . '/' . $datum['uid'];
            $datum['add_time'] = date('Y-m-d H:i:s', $datum['add_time']);
            $datum['fail_time'] = date('Y-m-d H:i:s', $datum['fail_time']);
        }
        $data = count((array)$data) ? $data->toArray() : [];
        $count = self::setMerWhere($where)->count();
        return compact('data', 'count');
    }

    public static function changeFail($id, $fail_msg, $data)
    {
        $fail_time = time();
        $extract_number = $data['extract_price'];
        $mark = '提现失败,退回佣金' . $extract_number . '元';
        $uid = $data['uid'];
        $status = -1;
        $User = User::where(['uid' => $uid])->find();
        if (!$User) return false;
        UserBill::income('提现失败', $uid, 'now_money', 'extract_fail', $extract_number, $id, bcadd($User['brokerage_price'], $extract_number, 2), $mark);
        User::bcInc($uid, 'brokerage_price', $extract_number, 'uid');
        try {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), WechatTemplateService::USER_BALANCE_CHANGE, [
                    'first' => $mark,
                    'keyword1' => '佣金提现',
                    'keyword2' => bcadd($User['brokerage_price'], $extract_number, 2),
                    'remark' => '错误原因:' . $fail_msg
                ], Url::build('wap/spread/spread', [], true, true));
            } else {
                $dat['thing8']['value'] = '佣金提现';
                $dat['date4']['value'] = date('Y-m-d H:i:s', time());
                $dat['amount1']['value'] = $extract_number;
                $dat['amount2']['value'] = bcadd($User['brokerage_price'], $extract_number, 2);
                $dat['thing5']['value'] = '错误原因:' . $fail_msg;
                RoutineTemplate::sendAccountChanges($dat, $uid, Url::build('wap/spread/spread', [], true, true));
            }
        } catch (\Exception $e) {

        }
        return self::edit(compact('fail_time', 'fail_msg', 'status'), $id);
    }

    public static function changeSuccess($id, $data)
    {
        $status = 1;
        $extract_number = $data['extract_price'];
        $mark = '成功提现佣金' . $extract_number . '元';
        $uid = $data['uid'];
        $User = User::where(['uid' => $uid])->find();
        if (!$User) return false;
        UserBill::expend('提现成功', $uid, 'now_money', 'extract_success', $extract_number, $id, $User['brokerage_price'], $mark);
        if ($data['extract_type'] == 'yue') {
            User::bcInc($uid, 'now_money', $extract_number, 'uid');
            UserBill::income('提现到零钱', $uid, 'now_money', 'extract_to_yue', $extract_number, $id, bcadd($User['now_money'], $extract_number, 2), $mark);
        }
        try {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), WechatTemplateService::USER_BALANCE_CHANGE, [
                    'first' => $mark,
                    'keyword1' => '佣金提现',
                    'keyword2' => $User['brokerage_price'],
                    'remark' => '点击查看我的佣金明细！'
                ], Url::build('wap/spread/spread', [], true, true));
            } else {
                $dat['thing8']['value'] = '佣金提现';
                $dat['date4']['value'] = date('Y-m-d H:i:s', time());
                $dat['amount1']['value'] = $extract_number;
                $dat['amount2']['value'] = $User['brokerage_price'];
                $dat['thing5']['value'] = $mark;
                RoutineTemplate::sendAccountChanges($dat, $uid, Url::build('wap/spread/spread', [], true, true));
            }
        } catch (\Exception $e) {

        }
        return self::edit(compact('status'), $id);
    }

    public static function changeMerFail($id, $fail_msg, $data)
    {
        $fail_time = time();
        $extract_number = $data['extract_price'];
        $mark = '提现失败,退回' . $extract_number . '元';
        $uid = $data['uid'];
        $status = -1;
        $merchant = Merchant::where(['id' => $data['mer_id']])->find();
        if (!$merchant) return false;
        MerchantBill::income('提现失败', 0, $data['mer_id'], 'now_money', 'extract_fail', $extract_number, bcadd($merchant['now_money'], $extract_number, 2), $mark);
        Merchant::bcInc($data['mer_id'], 'now_money', $extract_number, 'id');
        try {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), WechatTemplateService::USER_BALANCE_CHANGE, [
                    'first' => $mark,
                    'keyword1' => '余额提现',
                    'keyword2' => bcadd($merchant['now_money'], $extract_number, 2),
                    'remark' => '错误原因:' . $fail_msg
                ], Url::build('wap/merchant/income', ['active' => 2, 'mer_id' => $data['mer_id']], true, true));
            } else {
                $dat['thing8']['value'] = '余额提现';
                $dat['date4']['value'] = date('Y-m-d H:i:s', time());
                $dat['amount1']['value'] = $extract_number;
                $dat['amount2']['value'] = bcadd($merchant['now_money'], $extract_number, 2);
                $dat['thing5']['value'] = '错误原因:' . $fail_msg;
                RoutineTemplate::sendAccountChanges($dat, $uid, Url::build('wap/merchant/income', ['active' => 2, 'mer_id' => $data['mer_id']], true, true));
            }
        } catch (\Exception $e) {

        }
        return self::edit(compact('fail_time', 'fail_msg', 'status'), $id);
    }

    public static function changeMerSuccess($id, $data)
    {
        $status = 1;
        $extract_number = $data['extract_price'];
        $mark = '成功提现' . $extract_number . '元';
        $uid = $data['uid'];
        $merchant = Merchant::where(['id' => $data['mer_id']])->find();
        if (!$merchant) return false;
        try {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), WechatTemplateService::USER_BALANCE_CHANGE, [
                    'first' => $mark,
                    'keyword1' => '余额提现',
                    'keyword2' => $merchant['now_money'],
                    'remark' => '点击查看我的佣金明细！'
                ], Url::build('wap/merchant/income', ['active' => 2, 'mer_id' => $data['mer_id']], true, true));
            } else {
                $dat['thing8']['value'] = '余额提现';
                $dat['date4']['value'] = date('Y-m-d H:i:s', time());
                $dat['amount1']['value'] = $extract_number;
                $dat['amount2']['value'] = $merchant['now_money'];
                $dat['thing5']['value'] = $mark;
                RoutineTemplate::sendAccountChanges($dat, $uid, Url::build('wap/merchant/income', ['active' => 2, 'mer_id' => $data['mer_id']], true, true));
            }
        } catch (\Exception $e) {

        }
        return self::edit(compact('status'), $id);
    }

    //测试数据
    public static function test()
    {
        $uids = User::order('uid desc')->limit(2, 20)->field(['uid', 'nickname'])->select()->toArray();
        $type = ['bank', 'alipay', 'weixin'];
        foreach ($uids as $item) {
            $data = [
                'uid' => $item['uid'],
                'real_name' => $item['nickname'],
                'extract_type' => isset($type[rand(0, 2)]) ? $type[rand(0, 2)] : 'alipay',
                'bank_code' => rand(1000000, 999999999),
                'bank_address' => '中国',
                'alipay_code' => rand(1000, 9999999),
                'extract_price' => rand(100, 9999),
                'mark' => '测试数据',
                'add_time' => time(),
                'status' => 1,
                'wechat' => rand(999, 878788) . $item['uid'],
            ];
            self::set($data);
        }
    }

    //获取头部提现信息
    public static function getExtractHead()
    {
        //本月提现人数
        $month = self::getModelTime(['data' => 'month'], self::where(['status' => 1]))->group('uid')->count();
        //本月提现笔数
        $new_month = self::getModelTime(['data' => 'month'], self::where(['status' => 1]))->distinct(true)->count();
        //上月提现人数
        $last_month = self::whereTime('add_time', 'last month')->where('status', 1)->group('uid')->distinct(true)->count();
        //上月提现笔数
        $last_count = self::whereTime('add_time', 'last month')->where('status', 1)->count();
        //本月提现金额
        $extract_price = self::getModelTime(['data' => 'month'], self::where(['status' => 1]))->sum('extract_price');
        //上月提现金额
        $last_extract_price = self::whereTime('add_time', 'last month')->where('status', 1)->sum('extract_price');

        return [
            [
                'name' => '总提现人数',
                'field' => '个',
                'count' => self::where(['status' => 1])->group('uid')->count(),
                'content' => '',
                'background_color' => 'layui-bg-blue',
                'sum' => '',
                'class' => 'fa fa-bar-chart',
            ],
            [
                'name' => '总提现笔数',
                'field' => '笔',
                'count' => self::where(['status' => 1])->distinct(true)->count(),
                'content' => '',
                'background_color' => 'layui-bg-cyan',
                'sum' => '',
                'class' => 'fa fa-line-chart',
            ],
            [
                'name' => '本月提现人数',
                'field' => '人',
                'count' => $month,
                'content' => '',
                'background_color' => 'layui-bg-orange',
                'sum' => '',
                'class' => 'fa fa-line-chart',
            ],
            [
                'name' => '本月提现笔数',
                'field' => '笔',
                'count' => $new_month,
                'content' => '',
                'background_color' => 'layui-bg-green',
                'sum' => '',
                'class' => 'fa fa-line-chart',
            ],
            [
                'name' => '本月提现金额',
                'field' => '元',
                'count' => $extract_price,
                'content' => '提现总金额',
                'background_color' => 'layui-bg-cyan',
                'sum' => self::where(['status' => 1])->sum('extract_price'),
                'class' => 'fa fa-line-chart',
            ],
            [
                'name' => '上月提现人数',
                'field' => '个',
                'count' => $last_month,
                'content' => '环比增幅',
                'background_color' => 'layui-bg-blue',
                'sum' => $last_month == 0 ? '100%' : bcdiv($month, $last_month, 2) * 100,
                'class' => $last_month == 0 ? 'fa fa-level-up' : 'fa fa-level-down',
            ],
            [
                'name' => '上月提现笔数',
                'field' => '笔',
                'count' => $last_count,
                'content' => '环比增幅',
                'background_color' => 'layui-bg-black',
                'sum' => $last_count == 0 ? '100%' : bcdiv($new_month, $last_count, 2) * 100,
                'class' => $last_count == 0 ? 'fa fa-level-up' : 'fa fa-level-down',
            ],
            [
                'name' => '上月提现金额',
                'field' => '元',
                'count' => $last_extract_price,
                'content' => '环比增幅',
                'background_color' => 'layui-bg-gray',
                'sum' => $last_extract_price == 0 ? '100%' : bcdiv($extract_price, $last_extract_price, 2) * 100,
                'class' => $last_extract_price == 0 ? 'fa fa-level-up' : 'fa fa-level-down',
            ],
        ];
    }

    //获取提现分布图和提现人数金额曲线图
    public static function getExtractList($where, $limit = 15)
    {
        $legdata = ['提现人数', '提现金额'];
        $list = self::getModelTime($where, self::where('status', 1))
            ->field([
                'FROM_UNIXTIME(add_time,"%Y-%c-%d") as un_time',
                'count(uid) as count',
                'sum(extract_price) as sum_price',
            ])->group('un_time')->order('un_time asc')->select();
        if (count($list)) $list = $list->toArray();
        $xdata = [];
        $itemList = [0 => [], 1 => []];
        $chatrList = [];
        $zoom = '';
        foreach ($list as $value) {
            $xdata[] = $value['un_time'];
            $itemList[0][] = $value['count'];
            $itemList[1][] = $value['sum_price'];
        }
        foreach ($legdata as $key => $name) {
            $item['name'] = $name;
            $item['type'] = 'line';
            $item['data'] = $itemList[$key];
            $chatrList[] = $item;
        }
        unset($item, $name, $key);
        if (count($xdata) > $limit) $zoom = $xdata[$limit - 5];
        //饼状图
        $cake = ['支付宝', '银行卡', '微信'];
        $fenbulist = self::getModelTime($where, self::where('status', 1))
            ->field(['count(uid) as count', 'extract_type'])->group('extract_type')->order('count asc')->select();
        if (count($fenbulist)) $fenbulist = $fenbulist->toArray();
        $sum_count = self::getModelTime($where, self::where('status', 1))->count();
        $color = ['#FB7773', '#81BCFE', '#91F3FE'];
        $fenbudata = [];
        foreach ($fenbulist as $key => $item) {
            if ($item['extract_type'] == 'bank') {
                $item_date['name'] = '银行卡';
            } else if ($item['extract_type'] == 'alipay') {
                $item_date['name'] = '支付宝';
            } else if ($item['extract_type'] == 'weixin') {
                $item_date['name'] = '微信';
            }
            $item_date['value'] = bcdiv($item['count'], $sum_count, 2) * 100;
            $item_date['itemStyle']['color'] = $color[$key];
            $fenbudata[] = $item_date;
        }
        return compact('xdata', 'chatrList', 'legdata', 'zoom', 'cake', 'fenbudata');
    }

    /**
     * 获取用户累计提现金额
     * @param int $uid
     * @return int|mixed
     */
    public static function getUserCountPrice($uid = 0)
    {
        if (!$uid) return 0;
        $price = self::where('uid', $uid)->where('status', 1)->field('sum(extract_price) as price')->find()['price'];
        return $price ? $price : 0;
    }

    /**
     * 获取用户累计提现次数
     * @param int $uid
     * @return int|string
     */
    public static function getUserCountNum($uid = 0)
    {
        if (!$uid) return 0;
        return self::where('uid', $uid)->count();
    }

    /**
     * 获得用户提现总金额
     * @param $uid
     * @return mixed
     */
    public static function userExtractTotalPrice($uid, $status = 1, $where = [])
    {
        return self::getModelTime($where, self::where('uid', 'in', $uid)->where('status', $status))->sum('extract_price') ?: 0;
    }
}
