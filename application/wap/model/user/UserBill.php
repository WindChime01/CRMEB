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


namespace app\wap\model\user;

use basic\ModelBasic;
use traits\ModelTrait;

/**记录
 * Class UserBill
 * @package app\wap\model\user
 */
class UserBill extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    public static function income($title, $uid, $category, $type, $number, $link_id = 0, $balance = 0, $mark = '', $status = 1, $get_uid = 0)
    {
        $pm = 1;
        return self::set(compact('title', 'uid', 'link_id', 'category', 'type', 'number', 'balance', 'mark', 'status', 'pm', 'get_uid'));
    }

    public static function expend($title, $uid, $category, $type, $number, $link_id = 0, $balance = 0, $mark = '', $status = 1)
    {
        $pm = 0;
        return self::set(compact('title', 'uid', 'link_id', 'category', 'type', 'number', 'balance', 'mark', 'status', 'pm'));
    }

    public static function getSginDay($year, $month, $uid)
    {

        $model = self::where('uid', $uid)->where(['category' => 'integral', 'type' => 'sign', 'status' => 1, 'pm' => 1]);
        if (!$year && !$month) {
            $model->whereTime('add_time', 'm');
        } else {
            $t = date('t', strtotime($year . '-' . $month));
            $model->whereTime('add_time', 'between', [strtotime($year . '-' . $month), strtotime($year . '-' . $month . '-' . $t)]);
        }
        $list = $model->field(['from_unixtime(add_time,\'%d\') as time'])->order('time asc')->select();
        count($list) && $list = $list->toArray();
        foreach ($list as &$item) {
            $item['day'] = ltrim($item['time'], '\0');
        }
        return $list;
    }

    /**
     * 获取提现记录或者佣金记录
     * @param $where arrat 查询条件
     * @param $uid int 用户uid
     * @return array
     *
     * */
    public static function getSpreadList($where, $uid)
    {
        $uids = User::where('spread_uid', $uid)->column('uid');
        $uids1 = User::where('spread_uid', 'in', $uids)->group('uid')->column('uid');
        $model = self::where('a.uid', $uid)->alias('a')->join('__USER__ u', 'u.uid=a.uid')->where('a.link_id', 'neq', 0)->order('a.add_time desc');
        switch ((int)$where['type']) {
            case 0:
                $model = $model->join('store_order o', 'o.id = a.link_id')->whereIn('o.uid', $uids);
                $model = $model->where('a.category', 'now_money')->order('a.add_time desc')->where('a.number', '<>', 0)
                    ->field('FROM_UNIXTIME(a.add_time,"%Y-%m") as time,group_concat(a.id SEPARATOR ",") ids')
                    ->where('a.type', 'in', 'brokerage,brokerage_return')
                    ->group('time');
                break;
            case 1:
                $model = $model->join('store_order o', 'o.id = a.link_id')->whereIn('o.uid', $uids1);
                $model = $model->where('a.category', 'now_money')->order('a.add_time desc')->where('a.number', '<>', 0)
                    ->field('FROM_UNIXTIME(a.add_time,"%Y-%m") as time,group_concat(a.id SEPARATOR ",") ids')
                    ->where('a.type', 'in', 'brokerage,brokerage_return')
                    ->group('time');
                break;
        }
        $model = $model->page((int)$where['page'], (int)$where['limit']);
        $list = ($list = $model->select()) ? $list->toArray() : [];
        $data = [];
        foreach ($list as &$item) {
            $value['time'] = $item['time'];
            $value['list'] = self::where('id', 'in', $item['ids'])->field('FROM_UNIXTIME(add_time,"%Y-%m-%d %H:%i") as add_time,uid,title,number,mark,pm')->order('add_time DESC')->select();
            array_push($data, $value);
        }
        $page = $where['page'] + 1;
        return compact('data', 'page');
    }

    public static function get_user_withdrawal_list($where, $uid)
    {
        $model = self::where('uid', $uid)->where('category', 'now_money')->order('add_time desc')->where('number', '<>', 0)
            ->field('FROM_UNIXTIME(add_time,"%Y-%m") as time,group_concat(id SEPARATOR ",") ids')
            ->where('type', 'in', 'extract,extract_fail')
            ->group('time');
        $model = $model->page((int)$where['page'], (int)$where['limit']);
        $list = ($list = $model->select()) ? $list->toArray() : [];
        $data = [];
        foreach ($list as $item) {
            $value['time'] = $item['time'];
            $value['list'] = self::where('id', 'in', $item['ids'])->field('FROM_UNIXTIME(add_time,"%Y-%m-%d %H:%i") as add_time,title,number,pm')->order('add_time DESC')->select();
            array_push($data, $value);
        }
        $page = $where['page'] + 1;
        return compact('data', 'page');
    }

    /*
     * 获得某年某月的天数
     * */
    public static function DaysInMonth($month, $year)
    {
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }

    /*
         * 获取总佣金
         * */
    public static function getBrokerage($uid, $category = 'now_money', $type = 'brokerage', $where)
    {
        return self::getModelTime($where, self::where('uid', 'in', $uid)->where('category', $category)
            ->where('type', $type)->where('pm', 1)->where('status', 1))->sum('number');
    }

    /*
     * 获取返还佣金
     * */
    public static function getReturnBrokerage($uid, $category = 'now_money', $type = 'brokerage_return', $where)
    {
        return self::getModelTime($where, self::where('uid', 'in', $uid)->where('category', $category)
            ->where('type', $type)->where('pm', 0)->where('status', 1))->sum('number');
    }

    /**获取用户佣金金额
     * @param int $uid
     */
    public static function getCommissionAmount($uid = 0)
    {
        $brokerage = self::where('uid', 'in', $uid)->where('category', 'now_money')
            ->where('type', 'brokerage')->where('pm', 1)->where('status', 1)->sum('number');
        $brokerage_return = self::where('uid', 'in', $uid)->where('category', 'now_money')
            ->where('type', 'brokerage_return')->where('pm', 0)->where('status', 1)->sum('number');
        $commission = bcsub($brokerage, $brokerage_return, 2);
        return $commission;
    }

    public static function getUserGoldBill(array $where, $page = 0, $limit = 10)
    {
        $model = self::where('status', 1);
        if ($where) {
            $list = $model->where($where);
        }
        $list = $model->order('add_time desc')->page((int)$page, (int)$limit)->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as &$item) {
            $item['_add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }
        $page--;
        return ['list' => $list, 'page' => $page];
    }

}
