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

namespace app\admin\model\wechat;

use app\admin\model\wechat\StoreServiceLog as ServiceLogModel;
use app\admin\model\wechat\WechatUser;
use app\admin\model\user\User;
use app\admin\model\merchant\Merchant;
use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 客服管理 model
 * Class StoreService
 * @package app\admin\model\wechat
 */
class StoreService extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where)
    {
        $model = new self();
        if (isset($where['mer_id']) && $where['mer_id']) {
            $model = $model->where('mer_id', $where['mer_id']);
        }
        if (isset($where['status']) && $where['status']) {
            $model = $model->where('status', $where['status']);
        }
        if (isset($where['title']) && $where['title']) {
            $model = $model->where('nickname', 'like', "%$where[title]%");
        }
        return $model;
    }

    /**
     * @return array
     */
    public static function getList($where)
    {
        $data = self::setWhere($where)->page($where['page'], $where['limit'])->order('sort desc,id desc')->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        foreach ($data as $key => &$item) {
            if ($item['mer_id']) {
                $item['mer_name'] = Merchant::where('id', $item['mer_id'])->value('mer_name');
            } else {
                $item['mer_name'] = '总平台';
            }
        }
        $count = self::setWhere($where['mer_id'])->count();
        return compact('data', 'count');
    }

    /**
     * @return array
     */
    public static function getChatUser($uid, $mer_id, $page, $limit)
    {
        $list = [];
        $count = 0;
        $where = 'mer_id = ' . $mer_id . ' AND (uid = ' . $uid . ' OR to_uid=' . $uid . ')';
        $chat_list = ServiceLogModel::field("uid,to_uid")->page($page,$limit)->where($where)->group("uid,to_uid")->select();
        if (!count($chat_list)) return compact('list', 'count');
        $arr_user = $arr_to_user = [];
        foreach ($chat_list as $key => $value) {
            array_push($arr_user, $value["uid"]);
            array_push($arr_to_user, $value["to_uid"]);
        }
        $uids = array_merge($arr_user, $arr_to_user);

        $data = User::field("uid,nickname,avatar")->where(array("uid" => array(array("in", $uids), array("neq", $uid))))->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        foreach ($data as $index => $user) {
            $service = self::field("uid,nickname,avatar")->where(array("uid" => $user["uid"]))->find();
            if ($service) $data[$index] = $service;
        }
        $count = User::where(array("uid" => array(array("in", $uids), array("neq", $uid))))->count();
        return compact('data', 'count');
    }
}
