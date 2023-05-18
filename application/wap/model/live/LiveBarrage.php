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

namespace app\wap\model\live;


use basic\ModelBasic;
use service\SystemConfigService;
use traits\ModelTrait;
use app\wap\model\user\User;
use app\wap\model\live\LiveGift;

/**直播间评论表
 * Class LiveBarrage
 * @package app\wap\model\live
 */
class LiveBarrage extends ModelBasic
{

    use ModelTrait;

    /**助教评论列表
     * @param $uids
     * @param $live_id
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCommentList($uids, $live_id, $page = 0, $limit = 10)
    {
        $model = self::where('live_id', $live_id)->where('is_show', 1);
        if ($uids) $model = $model->where('uid', 'in', $uids);
        $list = $model->field('type,barrage as content,uid,live_id,id')->order('add_time desc')->page($page, $limit)->select();
        $list = count($list) ? $list->toArray() : [];
        $commentList = [];
        foreach ($list as &$item) {
            $userinfo = User::where('uid', $item['uid'])->field(['nickname', 'avatar'])->find();
            if ($userinfo) {
                $item['nickname'] = $userinfo['nickname'];
                $item['avatar'] = $userinfo['avatar'];
            } else {
                $item['nickname'] = '';
                $item['avatar'] = '';
            }
            $type = LiveHonouredGuest::where(['uid' => $item['uid'], 'live_id' => $item['live_id']])->value('type');
            if (is_null($type))
                $item['user_type'] = 2;
            else
                $item['user_type'] = $type;
            if ($item['type'] == 4) {
                $live_reward_list = LiveReward::where(['id' => $item['content']])->find();
                if ($live_reward_list ? $live_reward_list = $live_reward_list->toArray() : []) {
                    $live_gift = LiveGift::liveGiftOne($live_reward_list['gift_id']);
                    if ($live_gift) {
                        $item['content'] = "赠送给主播";
                        $item['gift_num'] = $live_reward_list['gift_num'];
                        $item['gift_image'] = $live_gift ? $live_gift['live_gift_show_img'] : "";
                        $item['gift_name'] = $live_reward_list['gift_name'];
                        array_push($commentList, $item);
                    }
                }
            } else {
                array_push($commentList, $item);
            }
        }
        $page--;
        if (count($commentList) == 0 || count($commentList) < $limit) {
            $ystemConfig = SystemConfigService::more(['site_name', 'home_logo']);
            $data = [
                'nickname' => $ystemConfig['site_name'],
                'avatar' => $ystemConfig['home_logo'],
                'user_type' => 2,
                'content' => LiveStudio::where('id', $live_id)->value('auto_phrase'),
                'id' => 0,
                'type' => 1,
                'uid' => 0
            ];
            array_push($commentList, $data);
        }
        return ['list' => $commentList, 'page' => $page];
    }


}
