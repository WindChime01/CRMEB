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

namespace app\merchant\model\wechat;

use app\merchant\model\wechat\StoreService as ServiceModel;
use app\wap\model\user\User;
use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 客服管理 model
 * Class StoreServiceLog
 * @package app\merchant\model\wechat
 */
class StoreServiceLog extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($uid, $to_uid, $mer_id)
    {
        $model = new self;
        $where = "mer_id = " . $mer_id . " AND ((uid = " . $uid . " AND to_uid = " . $to_uid . ") OR (uid = " . $to_uid . " AND to_uid = " . $uid . "))";
        $model = $model->where($where)->order("add_time desc");
        return $model;
    }

    /**
     * @return array
     */
    public static function getChatList($uid, $to_uid, $mer_id, $page, $limit)
    {
        $data = self::setWhere($uid, $to_uid, $mer_id)->page($page, $limit)->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        foreach ($data as $key => &$item) {
            $user = StoreService::field("nickname,avatar")->where('mer_id', $mer_id)->where(array("uid" => $item["uid"]))->find();
            if (!$user) $user = User::field("nickname,avatar")->where(array("uid" => $item["uid"]))->find();
            $item["nickname"] = $user["nickname"];
            $item["avatar"] = $user["avatar"];
            $item["add_time"] = date('Y-m-d H:i:s',$item["add_time"]);
        }
        $count = self::setWhere($uid, $to_uid, $mer_id)->count();
        return compact('data', 'count');
    }
}
