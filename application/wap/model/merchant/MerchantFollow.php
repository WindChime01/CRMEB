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

namespace app\wap\model\merchant;

use app\wap\model\special\Lecturer;
use traits\ModelTrait;
use basic\ModelBasic;

class MerchantFollow extends ModelBasic
{
    use ModelTrait;

    /**用户关注 取消 讲师关注
     * @param $uid
     * @param $mer_id
     * @param $is_follow 0 =取消关注  1= 关注
     */
    public static function user_merchant_follow($uid, $mer_id, $is_follow = 0)
    {
        $data['is_follow'] = $is_follow;
        if (self::be(['uid' => $uid, 'mer_id' => $mer_id])) {
            if ($is_follow == 1) {
                $data['follow_time'] = time();
            } else {
                $data['unfollow_time'] = time();
            }
            return self::where(['uid' => $uid, 'mer_id' => $mer_id])->update($data);
        } else {
            $data['uid'] = $uid;
            $data['mer_id'] = $mer_id;
            $data['follow_time'] = time();
            return self::set($data);
        }
    }

    /**讲师关注列表
     * @param $uid
     * @param int $page
     * @param int $limit
     * @return array
     */
    public static function get_user_merchant_follow_list($uid, $page = 1, $limit = 20)
    {
        $data = self::alias('f')->where(['f.uid' => $uid, 'f.is_follow' => 1,'l.is_show' => 1, 'l.is_del' => 0])
            ->join('Lecturer l', 'f.mer_id=l.mer_id')
            ->where('l.mer_id','>',0)->page((int)$page, (int)$limit)
            ->order('f.follow_time desc')->field('f.uid,f.mer_id,f.is_follow,l.id,l.mer_id,l.is_show,l.is_del,l.lecturer_name,l.lecturer_head,l.label,l.introduction,l.study,l.curriculum')
            ->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        return $data;
    }

    /**是否关注
     * @param $uid
     * @param $mer_id
     */
    public static function isFollow($uid, $mer_id)
    {
        $follow=self::where(['uid'=>$uid,'mer_id'=>$mer_id,'is_follow'=>1])->find();
        if($follow) return ['code'=>1];
        else return ['code'=>0];
    }

}
