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

namespace app\merchant\model\live;

/**
 * 直播间礼物
 */

use basic\ModelBasic;
use service\SystemConfigService;
use traits\ModelTrait;
use app\merchant\model\user\User;
use app\merchant\model\live\LiveGift;
use app\merchant\model\merchant\Merchant;

class LiveReward extends ModelBasic
{

    use ModelTrait;

    public static function getLiveRewardList($where)
    {
        $data = self::setWhere($where, 'g')->field('g.total_price,g.uid,g.gift_id,g.id,g.live_id,from_unixtime(g.add_time) as add_time,g.gift_price,g.gift_num,u.avatar,u.nickname,l.live_title,s.mer_id')->order('g.add_time desc')
            ->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $live_gift = LiveGift::liveGiftOne($item['gift_id']);
            $item['gift_name'] = $live_gift ? $live_gift['live_gift_name'] : "";
            $item['gift_image'] = $live_gift ? $live_gift['live_gift_show_img'] : "";
        }
        $count = self::setWhere($where, 'g')->count();
        return compact('data', 'count');
    }

    //设置条件
    public static function setWhere($where, $alert = '', $model = null)
    {
        $model = $model === null ? new self() : $model;
        if ($alert) $model = $model->alias($alert);
        $alert = $alert ? $alert . '.' : '';
        $model->whereIn($alert . 'is_show', [0, 1]);
        $model->join('__LIVE_STUDIO__ l', $alert . 'live_id = l.id');
        $model->join('__USER__ u', $alert . 'uid = u.uid');
        $model->join('__SPECIAL__ s', 's.id = l.special_id');
        if (isset($where['live_id']) && $where['live_id']) {
            $model->where($alert . 'live_id', $where['live_id']);
        }
        if (isset($where['gift_id']) && $where['gift_id']) {
            $model->where($alert . 'gift_id', $where['gift_id']);
        }
        if (isset($where['mer_id']) && $where['mer_id']) {
            $model->where('s.mer_id', $where['mer_id']);
        }
        if (isset($where['user_info']) && $where['user_info']) {
            $userinfo = User::whereLike('nickname', "%" . $where['user_info'] . "%")->whereOr('phone', $where['user_info'])->field('uid')->find();
            $model->where($alert . 'uid', $userinfo ? $userinfo['uid'] : 0);
        }
        if (isset($where['date']) && ($where['date'] != '' || $where['date'] != 0)) {
            $where['data'] = $where['date'];
            $model = self::getModelTime($where, $model, $alert . 'add_time');
        }
        return $model;
    }

    public static function getBadge($where)
    {
        $data = self::setWhere($where, 'g')->field(['sum(g.total_price) as total_price', 'sum(g.gift_num) as gift_num'])->find();
        $data = $data ? $data->toArray() : [];
        return [
            [
                'name' => '虚拟币总额',
                'field' => '个',
                'count' => $data['total_price'],
                'background_color' => 'layui-bg-blue',
                'col' => 3
            ],
            [
                'name' => '礼物数量',
                'field' => '件',
                'count' => $data['gift_num'],
                'background_color' => 'layui-bg-blue',
                'col' => 3
            ]
        ];
    }

    //设置条件
    public static function setRewardWhere($where, $alert = '', $model = null)
    {
        $model = $model === null ? new self() : $model;
        if ($alert) $model = $model->alias($alert);
        $alert = $alert ? $alert . '.' : '';
        $model->whereIn($alert . 'is_show', [0, 1]);
        $model->where($alert . 'is_extract', 0);
        $model->join('__LIVE_STUDIO__ l', $alert . 'live_id = l.id');
        $model->join('__SPECIAL__ s', 's.id = l.special_id');
        if (isset($where['live_id']) && $where['live_id']) {
            $model->where($alert . 'live_id', $where['live_id']);
        }
        if (isset($where['mer_id']) && $where['mer_id']) {
            $model->where('s.mer_id', $where['mer_id']);
        }
        if (isset($where['date']) && ($where['date'] != '' || $where['date'] != 0)) {
            $where['data'] = $where['date'];
            $model = self::getModelTime($where, $model, $alert . 'add_time');
        }
        return $model->group('g.live_id');
    }

    public static function get_mer_live_reward_list($where)
    {
        $data = self::setRewardWhere($where, 'g')->field('sum(g.total_price) as total_price,g.live_id,l.stream_name,l.live_title,l.live_image')->order('g.add_time desc')
            ->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        $gold_divide = Merchant::where('id', $where['mer_id'])->value('gold_divide');
        $gold_divide = bcdiv($gold_divide, 100, 2);
        foreach ($data as $key => &$value) {
            $value['rawal'] = bcmul($value['total_price'], $gold_divide, 0);
        }
        $count = self::setRewardWhere($where, 'g')->count();
        return compact('data', 'count');
    }

    public static function get_mer_live_reward($where)
    {
        $data = self::setRewardWhere($where, 'g')->field('sum(g.total_price) as total_price')->find();
        return $data;
    }

}
