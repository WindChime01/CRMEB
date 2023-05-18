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

namespace app\admin\model\store;

use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\user\User;

/**
 * 评论管理 model
 * Class StoreProductReply
 * @package app\admin\model\store
 */
class StoreProductReply extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where)
    {
        $model = new self;
        $model = $model->alias('r');
        if ($where['comment'] != '') $model = $model->where('r.comment', 'LIKE', "%$where[comment]%");
        if ($where['is_reply'] != '') {
            if ($where['is_reply'] >= 0) {
                $model = $model->where('r.is_reply', $where['is_reply']);
            } else {
                $model = $model->where('r.is_reply', 'GT', 0);
            }
        }
        if ($where['product_id'] > 0) $model = $model->where('r.product_id', $where['product_id']);
        if ($where['title']) {
            $model = $model->join('User u', 'u.uid=r.uid');
            $model = $model->where('r.uid|u.nickname', 'LIKE', "%$where[title]%");
        }
        if ($where['store_name']) $model = $model->where('p.store_name', 'LIKE', "%$where[store_name]%");
        $model = $model->join('__STORE_PRODUCT__ p', 'p.id=r.product_id');
        $model = $model->where('r.is_del', 0)->order('r.add_time DESC');
        return $model;
    }

    /**评论列表
     * @param $where
     * @return array
     */
    public static function storeProductReplyList($where)
    {
        $data = self::setWhere($where)->page((int)$where['page'], (int)$where['limit'])->field('r.*,p.store_name')->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        foreach ($data as $key => &$value) {
            $score = bcadd(bcadd($value['product_score'], $value['service_score'], 2), $value['delivery_score'], 2);
            $value['score'] = bcdiv($score, 3, 2);
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
                'oid' => 0,
                'unique' => md5(time()),
                'product_id' => $data['product_id'],
                'product_score' => $data['product_score'],
                'service_score' => $data['service_score'],
                'delivery_score' => $data['delivery_score'],
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
}
