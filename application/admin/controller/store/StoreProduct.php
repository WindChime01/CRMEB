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

namespace app\admin\controller\store;

use app\admin\controller\AuthController;
use app\admin\model\system\Recommend;
use app\admin\model\system\RecommendRelation;
use service\FormBuilder as Form;
use app\admin\model\store\StoreProductRelation;
use app\admin\model\system\SystemConfig;
use service\JsonService as Json;
use service\SystemConfigService;
use traits\CurdControllerTrait;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\store\StoreCategory as CategoryModel;
use app\admin\model\store\StoreProduct as ProductModel;
use think\Url;
use app\admin\model\system\SystemAttachment;
use app\admin\model\questions\Relation;
use app\admin\model\merchant\Merchant;

/**
 * 产品管理
 * Class StoreProduct
 * @package app\admin\controller\store
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
        //获取分类
        $this->assign('cate', CategoryModel::getTierList());
        $this->assign('mer_list', Merchant::getMerchantList());
        //出售中产品
        $onsale = ProductModel::where(['is_show' => 1, 'status' => 1, 'is_del' => 0])->count();
        //待上架产品
        $forsale = ProductModel::where(['is_show' => 0, 'status' => 1, 'is_del' => 0])->count();
        //仓库中产品
        $warehouse = ProductModel::where(['is_del' => 0, 'status' => 1])->count();
        //已经售馨产品
        $outofstock = ProductModel::getModelObject()->where(ProductModel::setData(4))->count();
        //警戒库存
        $policeforce = ProductModel::getModelObject()->where(ProductModel::setData(5))->count();
        //回收站
        $recycle = ProductModel::where(['is_del' => 1, 'status' => 1])->count();

        $this->assign(compact('type', 'onsale', 'forsale', 'warehouse', 'outofstock', 'policeforce', 'recycle'));
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
            ['mer_id', ''],
            ['excel', 0],
            ['status', 1],
            ['type', $this->request->param('type')]
        ]);
        return Json::successlayui(ProductModel::ProductList($where));
    }

    /**商品审核
     * @return mixed
     */
    public function examine()
    {
        $this->assign('cate', CategoryModel::getTierList());
        $this->assign('mer_list', Merchant::getMerchantList());
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
            ['mer_id', ''],
            ['status', ''],
        ]);
        return Json::successlayui(ProductModel::productExamineList($where));
    }

    /**不通过
     * @param $id
     * @throws \think\exception\DbException
     */
    public function fail($id)
    {
        $fail_msg = parent::postMore([
            ['message', ''],
        ]);
        if (!ProductModel::be(['id' => $id, 'status' => 0])) return Json::fail('操作记录不存在或状态错误!');
        $special = ProductModel::get($id);
        if (!$special) return Json::fail('操作记录不存!');
        if ($special->status != 0) return Json::fail('您已审核,请勿重复操作');
        ProductModel::beginTrans();
        $res = ProductModel::changeFail($id, $special['mer_id'], $fail_msg['message']);
        if ($res) {
            ProductModel::commitTrans();
            return Json::successful('操作成功!');
        } else {
            ProductModel::rollbackTrans();
            return Json::fail('操作失败!');
        }
    }

    /**通过
     * @param $id
     * @throws \think\exception\DbException
     */
    public function succ($id)
    {
        if (!ProductModel::be(['id' => $id, 'status' => 0])) return Json::fail('操作记录不存在或状态错误!');
        $special = ProductModel::get($id);
        if (!$special) return Json::fail('操作记录不存!');
        if ($special->status != 0) return Json::fail('您已审核,请勿重复操作');
        ProductModel::beginTrans();
        $res = ProductModel::changeSuccess($id, $special['mer_id']);
        if ($res) {
            ProductModel::commitTrans();
            return Json::successful('操作成功!');
        } else {
            ProductModel::rollbackTrans();
            return Json::fail('操作失败!');
        }
    }

    public function examineDetails($id)
    {
        if (!$id) return Json::fail('参数错误');
        $details = ProductModel::get($id);
        if (!$details) return Json::fail('商品不存在');
        $this->assign(['details' => json_encode($details)]);
        return $this->fetch('product');
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
            'give_gold_num',
            'ficti',
            'description',
            ['is_show', 0],
            ['cost', 0],
            ['is_postage', 0],
            ['member_pay_type', 0],
            ['is_alone', 0],
            ['brokerage_ratio', 0],
            ['brokerage_two', 0]
        ], $request);
        if ($data['cate_id'] == "") return Json::fail('请选择商品分类');
        if (!$data['store_name']) return Json::fail('请输入商品名称');
        if (!$data['description']) return Json::fail('请输入商品详情');
        if (count($data['image']) < 1) return Json::fail('请上传商品图片');
        if (count($data['slider_image']) < 1) return Json::fail('请上传商品轮播图');
        if ($data['price'] == '' || $data['price'] < 0) return Json::fail('请输入商品售价');
        if ($data['ot_price'] == '' || $data['ot_price'] < 0) return Json::fail('请输入商品原价');
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
        if ($data['is_alone'] && bcadd($data['brokerage_ratio'], $data['brokerage_two'], 2) > 100) return Json::fail('两级返佣比例之和不能大于100');
        if ($id) {
            ProductModel::edit($data, $id);
            return Json::successful('修改产品成功!');
        } else {
            $data['add_time'] = time();
            ProductModel::set($data);
            return Json::successful('添加产品成功!');
        }
    }

    /**
     * 添加推荐
     * @param int $product_id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function recommend($product_id = 0)
    {
        if (!$product_id) $this->failed('缺少参数');
        $special = ProductModel::get($product_id);
        if (!$special) $this->failed('没有查到此专题');
        if ($special->is_del) $this->failed('此专题已删除');
        $form = Form::create(Url::build('save_recommend', ['product_id' => $product_id]), [
            Form::select('recommend_id', '推荐')->setOptions(function () use ($product_id) {
                $list = Recommend::where(['is_show' => 1, 'type' => 4])->where('is_fixed', 0)->field('title,id')->order('sort desc,add_time desc')->select();
                $menus = [];
                foreach ($list as $menu) {
                    $menus[] = ['value' => $menu['id'], 'label' => $menu['title']];
                }
                return $menus;
            })->filterable(1),
            Form::number('sort', '排序'),
        ]);
        $form->setMethod('post')->setTitle('推荐设置')->setSuccessScript('parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},800);');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存推荐
     * @param int $special_id
     * @throws \think\exception\DbException
     */
    public function save_recommend($product_id = 0)
    {
        if (!$product_id) $this->failed('缺少参数');
        $data = parent::postMore([
            ['recommend_id', 0],
            ['sort', 0],
        ]);
        if (!$data['recommend_id']) return Json::fail('请选择推荐');
        $recommend = Recommend::get($data['recommend_id']);
        if (!$recommend) return Json::fail('导航菜单不存在');
        $data['add_time'] = time();
        $data['type'] = $recommend->type;
        $data['link_id'] = $product_id;
        if (RecommendRelation::be(['type' => $recommend->type, 'link_id' => $product_id, 'recommend_id' => $data['recommend_id']])) return Json::fail('已推荐,请勿重复推荐');
        if (RecommendRelation::set($data))
            return Json::successful('推荐成功');
        else
            return Json::fail('推荐失败');
    }

    /**取消推荐
     * @param int $id
     */
    public function cancel_recommendation($id = 0, $product_id = 0)
    {
        if (!$id || !$product_id) $this->failed('缺少参数');
        if (RecommendRelation::be(['id' => $id, 'link_id' => $product_id])) {
            $res = RecommendRelation::where(['id' => $id, 'link_id' => $product_id])->delete();
            if ($res)
                return Json::successful('取消推荐成功');
            else
                return Json::fail('取消推荐失败');
        } else {
            return Json::fail('推荐不存在');
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
        $mer_list = Merchant::getMerchantList();
        $this->assign(['id' => $id,'mer_list' => $mer_list]);
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

    /**商品转增
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function increase($id = 0)
    {
        if (!$id) $this->failed('缺少参数');
        $product = ProductModel::get($id);
        if (!$product) $this->failed('没有查到此商品');
        if ($product->is_del) $this->failed('此商品已删除');
        $form = Form::create(Url::build('change_increase', ['id' => $id]), [
            Form::select('mer_id', '讲师')->setOptions(function () {
                $model = Merchant::getMerWhere();
                $list = $model->field('mer_name,id')->order('sort desc,add_time desc')->select();
                $menus = [['value' => 0, 'label' => '总后台']];
                foreach ($list as $menu) {
                    $menus[] = ['value' => $menu['id'], 'label' => $menu['mer_name']];
                }
                return $menus;
            })->filterable(1),
        ]);
        $form->setMethod('post')->setTitle('商品转增')->setSuccessScript('parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},800);');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 商品转增
     * @param int $id
     * @throws \think\exception\DbException
     */
    public function change_increase($id = 0)
    {
        if (!$id) $this->failed('缺少参数');
        $data = parent::postMore([
            ['mer_id', 0],
        ]);
        $res = ProductModel::edit($data, $id, 'id');
        if ($res)
            return Json::successful('商品转增成功');
        else
            return Json::fail('商品转增失败');
    }
}
