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


namespace app\wap\model\store;


use app\wap\model\user\User;
use basic\ModelBasic;
use service\UtilService;
use traits\ModelTrait;

/**商品评论
 * Class StoreProductReply
 * @package app\wap\model\store
 */
class StoreProductReply extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    protected function setPicsAttr($value)
    {
        return is_array($value) ? json_encode($value) : $value;
    }

    protected function getPicsAttr($value)
    {
        return json_decode($value, true);
    }

    public static function reply($group, $type = 'product')
    {
        $group['reply_type'] = $type;
        return self::set($group);
    }

    public static function productValidWhere($alias = '')
    {
        $model = new self;
        if ($alias) {
            $model->alias($alias);
            $alias .= '.';
        }
        return $model->where("{$alias}is_del", 0)->where("{$alias}reply_type", 'product');
    }

    public static function getProductReplyList($productId, $page = 0, $limit = 8, $score = 4, $order = 'All')
    {
        $model = self::productValidWhere('A')->where('A.product_id', $productId)
            ->field('A.product_score,A.service_score,A.delivery_score,A.comment,A.pics,A.add_time,A.merchant_reply_content,A.is_selected,A.uid,A.id');
        $baseOrder = 'A.is_selected DESC,A.add_time DESC,A.product_score DESC,A.service_score DESC,A.delivery_score DESC';
        $model = $model->order($baseOrder);
        switch ($score) {
            case 3:
                $model = $model->where('A.product_score', 5);
                break;
            case 2:
                $model = $model->where('A.product_score', '>', 1)->where('A.product_score', '<', 5);
                break;
            case 1:
                $model = $model->where('A.product_score', 1);
                break;
        }
        $list = $model->page($page, $limit)->select()->toArray() ?: [];
        foreach ($list as $k => $reply) {
            $list[$k] = self::tidyProductReply($reply);
        }
        return $list;
    }

    public static function userData($uid, $reply_id)
    {
        if ($uid) {
            $user = User::where('uid', $uid)->field('nickname,avatar')->find();
        } else {
            $user = self::getDb('reply_false')->where('reply_id', $reply_id)->field('nickname,avatar')->find();
        }
        return $user;
    }

    public static function tidyProductReply($res)
    {
        $user = self::userData($res['uid'], $res['id']);
        $res['nickname'] = UtilService::anonymity($user['nickname']);
        $res['avatar'] = $user['avatar'];
        $res['add_time'] = date('Y-m-d H:i', $res['add_time']);
        $res['star'] = round(($res['product_score'] + $res['service_score'] + $res['delivery_score']) / 3);
        $res['comment'] = $res['comment'] ?: '此用户没有填写评价';
        return $res;
    }

    /**评论数据
     * @param $productId
     * @return mixed
     * @throws \think\Exception
     */
    public static function getProductReplyData($productId)
    {
        $data['whole'] = self::productValidWhere()->where('product_id', $productId)->count();//全部评论
        $data['praise'] = self::productValidWhere()->where('product_id', $productId)->where('product_score', '>=', 5)->count();//好评评论
        $data['review'] = self::productValidWhere()->where('product_id', $productId)->where('product_score', '>', 1)->where('product_score', '<', 5)->count();//中评评论
        $data['difference'] = self::productValidWhere()->where('product_id', $productId)->where('product_score', '=', 1)->count();//差评评论
        if ($data['whole'] > 0) {
            $data['positive_rate'] = bcmul(bcdiv($data['praise'], $data['whole'], 2), 100, 0);//好评率
        } else {
            $data['positive_rate'] = 0;
        }
        $product_score = self::productValidWhere()->where('product_id', $productId)->avg('product_score');
        $service_score = self::productValidWhere()->where('product_id', $productId)->avg('service_score');
        $delivery_score = self::productValidWhere()->where('product_id', $productId)->avg('delivery_score');
        $data['star'] = round(($product_score + $service_score + $delivery_score) / 3);//评分
        return $data;
    }

    public static function isReply($unique, $reply_type = 'product')
    {
        return self::be(['unique' => $unique, 'reply_type' => $reply_type]);
    }

    public static function getRecProductReply($productId)
    {
        $res = self::productValidWhere('A')->where('A.product_id', $productId)
            ->field('A.product_score,A.service_score,A.delivery_score,A.comment,A.pics,A.add_time,B.nickname,B.avatar,C.cart_info')
            ->join('__USER__ B', 'A.uid = B.uid')
            ->join('__STORE_ORDER_CART_INFO__ C', 'A.unique = C.unique')
            ->order('A.product_score DESC,A.service_score DESC,A.delivery_score DESC, A.add_time DESC')->find();
        if (!$res) return null;
        return self::tidyProductReply($res->toArray());
    }

}
