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

use traits\ModelTrait;
use basic\ModelBasic;

/**会员 model
 * Class MemberShip
 * @package app\wap\model\user
 */
class MemberShip extends ModelBasic
{
    use ModelTrait;

    /**条件处理
     * @return MemberShip
     */
    public static function setWhere()
    {
        return self::where(['is_publish' => 1, 'is_del' => 0, 'type' => 1]);
    }

    /**会员套餐列表
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function membershipList($uid)
    {
        $free = self::setWhere()->where('is_free', 1)->find();
        $record = MemberRecord::where('uid', $uid)->where('is_free', 1)->find();
        $list = self::setWhere()->where('is_free', 0)->order('sort DESC,id DESC')->select();
        $list = $list ? $list->toArray() : [];
        foreach ($list as &$vc) {
            $vc['sale'] = bcsub($vc['original_price'], $vc['price'], 2);
        }
        if ($free && !$record) {
            array_unshift($list, $free);
        }
        return $list;
    }

    /**修改用户会员信息
     * @param $order
     * @param $userInfo
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUserMember($order, $userInfo)
    {
        $member = self::setWhere()->where('id', $order['member_id'])->find();
        if (!$member) return false;
        $is_permanent = 0;
        if ($member['is_permanent']) {
            $is_permanent = 1;
            $overdue_time = User::where('uid', $order['uid'])->value('overdue_time');
        } else {
            switch ($userInfo['level']) {
                case 1:
                    $overdue_time = bcadd(bcmul($member['vip_day'], 86400, 0), $userInfo['overdue_time'], 0);
                    break;
                case 0:
                    $overdue_time = bcadd(bcmul($member['vip_day'], 86400, 0), time(), 0);
                    break;
            }
        }
        $data = [
            'oid' => $order['id'],
            'uid' => $order['uid'],
            'price' => $member['price'],
            'validity' => $member['vip_day'],
            'purchase_time' => time(),
            'is_permanent' => $is_permanent,
            'is_free' => $member['is_free'],
            'overdue_time' => $overdue_time,
            'add_time' => time(),
        ];
        $res = MemberRecord::set($data);
        if ($res) {
            switch ($userInfo['level']) {
                case 1:
                    $res1 = User::edit(['overdue_time' => $overdue_time, 'is_permanent' => $is_permanent], $order['uid'], 'uid');
                    break;
                case 0:
                    $res1 = User::edit(['level' => 1, 'member_time' => time(), 'overdue_time' => $overdue_time, 'is_permanent' => $is_permanent], $order['uid'], 'uid');
                    break;
            }
        }
        $res2 = $res && $res1;
        return $res2;
    }


    /**
     * 会员过期
     */
    public static function memberExpiration($uid)
    {
        $user = User::where('uid', $uid)->find();
        if ($user['level'] && $user['is_permanent'] == 0 && bcsub($user['overdue_time'], time(), 0) <= 0) {
            User::edit(['level' => 0, 'member_time' => 0], $uid, 'uid');
        }
        return true;
    }

    /**
     * 获取单独分销设置
     */
    public static function getIndividualDistributionSettings($member_id)
    {
        $data = self::where('id', $member_id)->field('is_alone,brokerage_ratio,brokerage_two')->find();
        if ($data) return $data;
        else return [];
    }

    /**获取会员标题
     * @param $id
     * @return float|mixed|string
     */
    public static function getName($id = 0)
    {
        if (!$id) return '';
        return self::where(['id' => $id])->value('title');
    }
}
