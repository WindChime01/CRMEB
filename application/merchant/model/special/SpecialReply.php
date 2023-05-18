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

namespace app\merchant\model\special;

use traits\ModelTrait;
use basic\ModelBasic;
use app\merchant\model\user\User;

/**
 * 评论管理 model
 * Class SpecialReply
 * @package app\merchant\model\special
 */
class SpecialReply extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where)
    {
        $model = new self;
        $model = $model->alias('r');
        if (isset($where['comment']) && $where['comment'] != '') $model = $model->where('r.comment', 'LIKE', "%$where[comment]%");
        if (isset($where['is_reply']) && $where['is_reply'] != '') {
            if ($where['is_reply'] >= 0) {
                $model = $model->where('r.is_reply', $where['is_reply']);
            } else {
                $model = $model->where('r.is_reply', 'GT', 0);
            }
        }
        if (isset($where['special_id']) && $where['special_id'] > 0) $model = $model->where('r.special_id', $where['special_id']);
        if (isset($where['title']) && $where['title']) {
            $model = $model->join('User u', 'u.uid=r.uid');
            $model = $model->where('r.uid|u.nickname', 'LIKE', "%$where[title]%");
        }
        if (isset($where['mer_id']) && $where['mer_id'] > 0) $model = $model->where('s.mer_id', $where['mer_id'])->where('s.status', 1);
        if (isset($where['special_name']) && $where['special_name']) $model = $model->where('s.title', 'LIKE', "%$where[special_name]%");
        $model = $model->join('Special s', 's.id=r.special_id');
        $model = $model->where('r.is_del', 0)->order('r.add_time DESC');
        return $model;
    }

    /**评论列表
     * @param $where
     * @return array
     */
    public static function specialReplyList($where)
    {
        $data = self::setWhere($where)->page((int)$where['page'], (int)$where['limit'])->field('r.*,s.title')->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        foreach ($data as $key => &$value) {
            $value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
            $value['pics'] = json_decode($value['pics']);
            if ($value['uid']) {
                $nickname = User::where('uid', $value['uid'])->value('nickname');
            } else {
                $nickname = self::getDb('reply_false')->where('reply_id', $value['id'])->value('nickname');
            }
            $value['nickname'] = $nickname . '/' . $value['uid'];
        }
        $count = self::setWhere($where)->count();
        return compact('count', 'data');
    }

    public static function helpeFalse($data, $banner)
    {
        self::beginTrans();
        try {
            $type = $data['type'];
            $nickname = $data['nickname'];
            $avatar = $data['avatar'];
            $data = [
                'uid' => 0,
                'special_id' => $data['special_id'],
                'satisfied_score' => $data['satisfied_score'],
                'comment' => $data['comment'],
                'pics' => json_encode($banner),
                'add_time' => time()
            ];
            $false = self::set($data);
            if (!$false) return self::setErrorInfo('写入虚拟评论失败', true);
            $res = self::getDb('reply_false')->insert(['reply_id' => $false['id'], 'type' => $type, 'nickname' => $nickname, 'avatar' => $avatar, 'add_time' => time()]);
            if (!$res) return self::setErrorInfo('写入虚拟评论用户失败', true);
            self::commitTrans();
            return true;
        } catch (\Exception $e) {
            self::rollbackTrans();
            return self::setErrorInfo($e->getMessage());
        }
    }

    public static function uodateScore($special_id)
    {
        $score = round(self::where('is_del', 0)->where('special_id', $special_id)->avg('satisfied_score'), 1);
        $data['score'] = $score;
        return Special::edit($data, $special_id, 'id');
    }

}
