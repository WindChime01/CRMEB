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

namespace app\merchant\controller\store;

use app\merchant\controller\AuthController;
use app\admin\model\system\SystemConfig;
use service\JsonService as Json;
use service\SystemConfigService;
use traits\CurdControllerTrait;
use think\Request;
use app\merchant\model\store\StoreCategory as CategoryModel;
use app\merchant\model\store\StoreProduct as ProductModel;
use think\Url;
use app\merchant\model\system\Relation;

/**
 * 产品管理
 * Class StoreProduct
 * @package app\merchant\controller\store
 */
class StoreProduct extends AuthController
{

    use CurdControllerTrait;

    protected $bindModel = ProductModel::class;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

        $type = $this->request->param('type');
        $mer_id = $this->merchantId;
        //获取分类
        $this->assign('cate', CategoryModel::getTierList());
        //全部产品
        $all = ProductModel::where(['is_del' => 0, 'mer_id' => $mer_id])->count();
        //出售中产品
        $onsale = ProductModel::where(['is_show' => 1, 'is_del' => 0, 'status' => 1, 'mer_id' => $mer_id])->count();
        //待上架产品
        $forsale = ProductModel::where(['is_show' => 0, 'is_del' => 0, 'status' => 1, 'mer_id' => $mer_id])->count();
        //仓库中产品
        $warehouse = ProductModel::where(['is_del' => 0, 'status' => 1, 'mer_id' => $mer_id])->count();
        //已经售馨产品
        $outofstock = ProductModel::getModelObject(['mer_id' => $mer_id])->where(ProductModel::setData(4))->count();
        //警戒库存
        $policeforce = ProductModel::getModelObject(['mer_id' => $mer_id])->where(ProductModel::setData(5))->count();
        //回收站
        $recycle = ProductModel::where(['is_del' => 1, 'status' => 1, 'mer_id' => $mer_id])->count();

        $this->assign(compact('type', 'all', 'onsale', 'forsale', 'warehouse', 'outofstock', 'policeforce', 'recycle'));
        return $this->fetch();
    }

    /**
     * 异步查找产品
     *
     * @return json
     */
    public function product_ist()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['store_name', ''],
            ['cate_id', ''],
            ['excel', 0],
            ['status', ''],
            ['type', $this->request->param('type')]
        ]);
        $where['mer_id'] = $this->merchantId;
        return Json::successlayui(ProductModel::ProductList($where));
    }

    /**商品审核
     * @return mixed
     */
    public function examine()
    {
        $this->assign('cate', CategoryModel::getTierList());
        return $this->fetch();
    }

    /**
     * 异步查找产品
     *
     * @return json
     */
    public function product_examine_ist()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['store_name', ''],
            ['cate_id', ''],
        ]);
        $where['mer_id'] = $this->merchantId;
        return Json::successlayui(ProductModel::productExamineList($where));
    }

    /**
     * 设置单个产品上架|下架
     *
     * @return json
     */
    public function set_show($is_show = '', $id = '')
    {
        ($is_show == '' || $id == '') && Json::fail('缺少参数');
        $res = parent::getDataModification('product', $id, 'is_show', (int)$is_show);
        if ($res) {
            return Json::successful($is_show == 1 ? '上架成功' : '下架成功');
        } else {
            return Json::fail($is_show == 1 ? '上架失败' : '下架失败');
        }
    }

    /**
     * 快速编辑
     *
     * @return json
     */
    public function set_product($field = '', $id = '', $value = '')
    {
        ($field == '' || $id == '' || $value == '') && Json::fail('缺少参数');
        $res = parent::getDataModification('product', $id, $field, $value);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**
     * 设置批量产品上架
     *
     * @return json
     */
    public function product_show()
    {
        $post = parent::postMore([
            ['ids', []]
        ]);
        if (empty($post['ids'])) {
            return Json::fail('请选择需要上架的产品');
        } else {
            $res = ProductModel::where('id', 'in', $post['ids'])->update(['is_show' => 1]);
            if ($res)
                return Json::successful('上架成功');
            else
                return Json::fail('上架失败');
        }
    }

    public function create($id = 0)
    {
        $gold_name = SystemConfigService::get('gold_name');//虚拟币名称
        if ($id) {
            $product = ProductModel::get($id);
            if (!$product) return Json::fail('数据不存在!');
            $slider_image = [];
            if ($product['slider_image']) {
                foreach (json_decode($product['slider_image']) as $key => $value) {
                    $image['pic'] = $value;
                    $image['is_show'] = false;
                    array_push($slider_image, $image);
                }

            }
            $product['slider_image'] = $slider_image;
        } else {
            $product = [];
        }
        $this->assign(['id' => $id, 'product' => json_encode($product), 'gold_name' => $gold_name]);
        return $this->fetch();
    }

    /**商品分类
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCateList()
    {
        $list = CategoryModel::where(['is_show' => 1, 'pid' => 0])->order('sort desc,add_time desc')->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        return Json::successful($list);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request, $id = 0)
    {
        $data = parent::postMore([
            ['cate_id', ""],
            'store_name',
            'store_info',
            'keyword',
            ['unit_name', '件'],
            ['image', []],
            ['slider_image', []],
            'postage',
            'price',
            'vip_price',
            'ot_price',
            'free_shipping',
            'sort',
            'stock',
            'ficti',
            'description',
            ['is_show', 0],
            ['cost', 0],
            ['is_postage', 0],
            ['member_pay_type', 0]
        ], $request);
        $data['mer_id'] = $this->merchantId;
        if ($data['cate_id'] == "") return Json::fail('请选择商品分类');
        if (!$data['store_name']) return Json::fail('请输入商品名称');
        if (!$data['description']) return Json::fail('请输入商品详情');
        if (count($data['image']) < 1) return Json::fail('请上传商品图片');
        if (count($data['slider_image']) < 1) return Json::fail('请上传商品轮播图');
        if ($data['price'] == '' || $data['price'] < 0) return Json::fail('请输入商品售价');
        if ($data['ot_price'] == '' || $data['ot_price'] < 0) return Json::fail('请输入商品划线价');
        if ($data['vip_price'] == '' || $data['vip_price'] < 0) return Json::fail('请输入商品会员价');
        if ($data['postage'] == '' || $data['postage'] < 0) return Json::fail('请输入邮费');
        if ($data['stock'] == '' || $data['stock'] < 0) return Json::fail('请输入商品库存');
        if ($data['cost'] == '' || $data['ot_price'] < 0) return Json::fail('请输入商品成本价');
        $data['image'] = $data['image'][0];
        $slider_image = [];
        foreach ($data['slider_image'] as $item) {
            $slider_image[] = $item['pic'];
        }
        $data['slider_image'] = json_encode($slider_image);
        if ($id) {
            $data['status'] = $this->isAudit == 1 ? 0 : 1;
            ProductModel::edit($data, $id);
            return Json::successful('修改产品成功!');
        } else {
            $data['add_time'] = time();
            $data['status'] = $this->isAudit == 1 ? 0 : 1;
            ProductModel::set($data);
            return Json::successful('添加产品成功!');
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (!$id) return $this->failed('数据不存在');
        if (!ProductModel::be(['id' => $id])) return $this->failed('产品数据不存在');
        if (ProductModel::be(['id' => $id, 'is_del' => 1])) {
            $res = parent::getDataModification('product', $id, 'is_del', 0);
            if (!$res)
                return Json::fail(ProductModel::getErrorInfo('恢复失败,请稍候再试!'));
            else
                return Json::successful('成功恢复产品!');
        } else {
            $res = parent::getDataModification('product', $id, 'is_del', 1);
            if (!$res)
                return Json::fail(ProductModel::getErrorInfo('删除失败,请稍候再试!'));
            else
                return Json::successful('删除成功!');
        }
    }


    /**
     * 点赞
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function collect($id)
    {
        if (!$id) return $this->failed('数据不存在');
        $product = ProductModel::get($id);
        if (!$product) return Json::fail('数据不存在!');
        $this->assign(StoreProductRelation::getCollect($id));
        return $this->fetch();
    }

    /**
     * 收藏
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function like($id)
    {
        if (!$id) return $this->failed('数据不存在');
        $product = ProductModel::get($id);
        if (!$product) return Json::fail('数据不存在!');
        $this->assign(StoreProductRelation::getLike($id));
        return $this->fetch();
    }

    /**
     * 修改产品价格
     * @param Request $request
     */
    public function edit_product_price(Request $request)
    {
        $data = parent::postMore([
            ['id', 0],
            ['price', 0],
        ], $request);
        if (!$data['id']) return Json::fail('参数错误');
        $res = ProductModel::edit(['price' => $data['price']], $data['id']);
        if ($res) return Json::successful('修改成功');
        else return Json::fail('修改失败');
    }

    /**
     * 修改产品库存
     * @param Request $request
     */
    public function edit_product_stock(Request $request)
    {
        $data = parent::postMore([
            ['id', 0],
            ['stock', 0],
        ], $request);
        if (!$data['id']) return Json::fail('参数错误');
        $res = ProductModel::edit(['stock' => $data['stock']], $data['id']);
        if ($res) return Json::successful('修改成功');
        else return Json::fail('修改失败');
    }

    /**关联课程
     * @param int $id
     * @return mixed
     */
    public function knowledge($id = 0)
    {
        if (!$id) Json::fail('缺少参数');
        $this->assign(['id' => $id]);
        return $this->fetch();
    }

    /**获取商品关联的专题
     * @param int $id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function knowledge_points($id = 0)
    {
        if (!$id) Json::fail('缺少参数');
        $data = Relation::getQuestionsRelationSpecial($id, 5);
        foreach ($data['data'] as $k => &$v) {
            if ($v['type'] == 6) $v['type'] = $v['light_type'];
            $v['types'] = parent::specialTaskType($v['type']);
        }
        return Json::successlayui($data);
    }

    /**关联专题
     * @param int $id
     */
    public function relation($id = 0)
    {
        if (!$id) Json::fail('缺少参数');
        $this->assign(['id' => $id]);
        return $this->fetch('relation');
    }

    /**商品关联课程
     * @param int $id
     */
    public function add_knowledge_points($id, $special_ids)
    {
        if (!$id) Json::fail('缺少参数');
        $res = Relation::setRelations($id, $special_ids, 5);
        if ($res)
            return Json::successful('关联成功');
        else
            return Json::fail('关联失败');
    }

    /**商品关联课程排序
     * @param int $id
     * @param int $special_id
     * @param $value
     */
    public function up_knowledge_points_sort($id, $special_id, $value)
    {
        if (!$id || !$special_id) Json::fail('缺少参数');
        $res = Relation::updateRelationSort($id, $special_id, 5, $value);
        if ($res)
            return Json::successful('修改成功');
        else
            return Json::fail('修改失败');
    }

    /**删除关联专题
     * @param int $id
     * @param int $special_id
     * @throws \think\exception\DbException
     */
    public function delete_knowledge_points($id = 0, $special_id = 0)
    {
        if (!$id || !$special_id) Json::fail('缺少参数');
        $res = Relation::delRelation($id, $special_id, 5);
        if ($res)
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }

}
