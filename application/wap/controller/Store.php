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


namespace app\wap\controller;

use app\wap\model\store\StoreCategory;
use app\wap\model\store\StoreProduct;
use service\GroupDataService;
use service\SystemConfigService;
use app\wap\model\topic\Relation;
use think\Cache;
use think\Request;
use think\Url;
use service\JsonService;

/**商品控制器
 * Class Store
 * @package app\wap\controller
 */
class Store extends AuthController
{

    /*
     * 白名单
     * */
    public static function WhiteList()
    {
        return [
            'index',
            'getCategory',
            'getProductList',
            'getAssociatedTopics',
            'detail',
        ];
    }

    /**商城列表
     * @param string $keyword
     * @return mixed
     */
    public function index($cId = 0)
    {
        $banner = json_encode(GroupDataService::getData('product_list_carousel') ?: []);
        $this->assign(compact('banner','cId'));
        return $this->fetch();
    }

    /**获取分类
     * @throws \think\exception\DbException
     */
    public function getCategory()
    {
        $parentCategory = StoreCategory::pidByCategory(0, 'id,cate_name');
        $parentCategory = count($parentCategory) > 0 ? $parentCategory->toArray() : [];
        return JsonService::successful($parentCategory);
    }

    /**商品列表
     * @param string $keyword
     * @param int $cId
     * @param int $first
     * @param int $limit
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getProductList($page = 1, $limit = 8, $cId = 0)
    {
        if (!empty($keyword)) $keyword = base64_decode(htmlspecialchars($keyword));
        $model = StoreProduct::validWhere();
        if (!empty($cId)) $model = $model->where('cate_id', $cId);
        if (!empty($keyword)) $model->where('keyword|store_name', 'LIKE', "%$keyword%");
        $model->order('sort DESC, add_time DESC');
        $list = $model->page((int)$page, (int)$limit)->field('id,mer_id,store_name,image,sales,price,stock,IFNULL(sales,0) + IFNULL(ficti,0) as sales,keyword')->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        return JsonService::successful($list);
    }

    /**商品详情
     * @param int $id
     * @return mixed|void
     */
    public function detail($id = 0)
    {
        if (!$id) $this->failed('参数错误!', Url::build('store/index'));
        $storeInfo = StoreProduct::getValidProduct($id);
        if (!$storeInfo) $this->failed('商品不存在或已下架!', Url::build('store/index'));
        $site_url = SystemConfigService::get('site_url') . Url::build('store/detail') . '?id=' . $id . '&spread_uid=' . $this->uid;
        $this->assign(['storeInfo' => $storeInfo, 'site_url' => $site_url]);
        return $this->fetch();
    }

    /**获取关联专题
     * @param int $id
     */
    public function getAssociatedTopics($id = 0, $page = 1, $list = 10)
    {
        if (!$id) return JsonService::fail('参数错误!');
        $data = Relation::getRelationSpecial(5, $id, $page, $list);
        foreach ($data as $key => &$item) {
            if (is_string($item['label'])) $item['label'] = json_decode($item['label'], true);
        }
        return JsonService::successful($data);
    }

}
