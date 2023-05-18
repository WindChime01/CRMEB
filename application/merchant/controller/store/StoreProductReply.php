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
use traits\CurdControllerTrait;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use app\merchant\model\store\StoreProductReply as ProductReplyModel;
use app\merchant\model\store\StoreProduct;
use think\Url;

/**
 * 评论管理 控制器
 * Class StoreProductReply
 * @package app\merchant\controller\store
 */
class StoreProductReply extends AuthController
{

    use CurdControllerTrait;

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index($product_id = 0)
    {
        $this->assign('product_id', $product_id);
        return $this->fetch();
    }

    /**
     * 评论列表
     */
    public function productReplyList()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['store_name', ''],
            ['is_reply', ''],
            ['product_id', 0],
            ['title', ''],
            ['comment', '']
        ], $this->request);
        $where['mer_id'] = $this->merchantId;
        return Json::successlayui(ProductReplyModel::storeProductReplyList($where));
    }

    /**
     * @param $id
     * @return \think\response\Json|void
     */
    public function delete($id)
    {
        if (!$id) return $this->failed('数据不存在');
        $data['is_del'] = 1;
        if (!ProductReplyModel::edit($data, $id))
            return Json::fail(ProductReplyModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    /**评论回复
     * @param Request $request
     */
    public function set_reply(Request $request)
    {
        $data = parent::postMore([
            'id',
            'content',
        ], $request);
        if (!$data['id']) return Json::fail('参数错误');
        if ($data['content'] == '') return Json::fail('请输入回复内容');
        $save['merchant_reply_content'] = $data['content'];
        $save['merchant_reply_time'] = time();
        $save['is_reply'] = 1;
        $res = ProductReplyModel::edit($save, $data['id']);
        if (!$res)
            return Json::fail(ProductReplyModel::getErrorInfo('回复失败,请稍候再试!'));
        else
            return Json::successful('回复成功!');
    }

    /**回复加精
     * @param int $id
     */
    public function refining_reply($id = 0)
    {
        if (!$id) return Json::fail('参数错误');
        $save['is_selected'] = 1;
        $res = ProductReplyModel::edit($save, $id);
        if (!$res)
            return Json::fail(ProductReplyModel::getErrorInfo('加精失败,请稍候再试!'));
        else
            return Json::successful('加精成功!');
    }

    /**
     * 创建虚拟评论
     *
     * */
    public function create_false()
    {
        return $this->fetch();
    }

    /**
     * 提交虚拟评论
     */
    public function save_false()
    {
        $data = parent::postMore([
            ['nickname', 0],
            ['avatar', ''],
            ['product_id', 0],
            ['product_score', 1],
            ['service_score', 1],
            ['delivery_score', 1],
            ['comment', ''],
            ['pics', []]
        ]);
        $data['type'] = 1;
        $banner = [];
        foreach ($data['pics'] as $item) {
            $banner[] = $item['pic'];
        }
        if (!$data['nickname']) return Json::fail('请输入昵称');
        if (!$data['avatar']) return Json::fail('请上传头像');
        if (!$data['product_id']) return Json::fail('请选择商品');
        if (!$data['comment']) return Json::fail('请编辑评论内容');
        $res = ProductReplyModel::helpeFalse($data, $banner);
        if ($res === false)
            return Json::fail(ProductReplyModel::getErrorInfo());
        else
            return Json::successful('虚拟评论成功');
    }

    public function productList()
    {
        $list = StoreProduct::PreWhere()->where('mer_id', $this->merchantId)->field('id,store_name')->select();
        return Json::successful($list);
    }

    /**
     * 快速编辑
     * @param string $field 字段名
     * @param int $id 修改的主键
     * @param string value 修改后的值
     * @return json
     */
    public function set_value($field = '', $id = '', $value = '')
    {
        if (!$field || !$id || $value == '') Json::fail('缺少参数3');

        $res = ProductReplyModel::where('id', $id)->update([$field => $value]);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

}
