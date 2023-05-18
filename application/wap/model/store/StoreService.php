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


use basic\ModelBasic;
use traits\ModelTrait;

/**客服表
 * Class StoreService
 * @package app\wap\model\store
 */
class StoreService extends ModelBasic
{
    use ModelTrait;

    /**随机获得CRMchat客服 客服id
     * @param $mer_id
     * @return int|mixed
     */
    public static function get_crmeb_random_service_kefu_id($mer_id = 0)
    {
        $model = new self();
        if ($mer_id > 0) {
            $model = $model->where('mer_id', 'in', [0, $mer_id]);
        } else {
            $model = $model->where('mer_id', 0);
        }
        $data = $model->where(['status' => 1])->where('kefu_id', '>', 0)->order('mer_id desc,id desc')->column('kefu_id');
        if (count($data) <= 0) return 0;
        $key = array_rand($data, 1);
        return $data[$key];
    }

    /**获取微信客服列表
     * @param $where
     * @return array
     */
    public static function get_weixin_service_list($where)
    {
        $model = new self();
        if (isset($where['mer_id']) && $where['mer_id'] != '') $model = $model->where('mer_id', 'in', [0, $where['mer_id']]);
        $list = $model->field('uid,avatar,nickname')->where(['status' => 1, 'is_h5user' => 0])->page($where['page'], $where['limit'])->order('sort desc,id desc')->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        return $list;
    }
}
