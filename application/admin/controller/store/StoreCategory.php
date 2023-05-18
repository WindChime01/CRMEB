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
use service\FormBuilder as Form;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\store\StoreCategory as CategoryModel;
use think\Url;
use app\admin\model\system\SystemAttachment;

/**
 * 产品分类控制器
 * Class StoreCategory
 * @package app\admin\controller\store
 */
class StoreCategory extends AuthController
{

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $this->assign('pid', $this->request->get('pid', 0));
        $this->assign('cate', CategoryModel::getTierList());
        return $this->fetch();
    }

    /**
     *  异步获取分类列表
     * @return json
     */
    public function category_list()
    {
        $where = parent::getMore([
            ['is_show', ''],
            ['pid', $this->request->param('pid', '')],
            ['cate_name', ''],
            ['page', 1],
            ['limit', 20],
            ['order', '']
        ]);
        return Json::successlayui(CategoryModel::CategoryList($where));
    }

    /**
     * 设置单个产品上架|下架
     *
     * @return json
     */
    public function set_show($is_show = '', $id = '')
    {
        ($is_show == '' || $id == '') && Json::fail('缺少参数');
        $res = parent::getDataModification('store_cate', $id, 'is_show', (int)$is_show);
        if ($res) {
            return Json::successful($is_show == 1 ? '显示成功' : '隐藏成功');
        } else {
            return Json::fail($is_show == 1 ? '显示失败' : '隐藏失败');
        }
    }

    /**
     * 快速编辑
     *
     * @return json
     */
    public function set_category($field = '', $id = '', $value = '')
    {
        ($field == '' || $id == '' || $value == '') && Json::fail('缺少参数');
        $res = parent::getDataModification('store_cate', $id, $field, $value);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create($id = 0)
    {
        $cate = [];
        if ($id) {
            $cate = CategoryModel::get($id);
        }
        $this->assign(['cate' => json_encode($cate), 'id' => $id]);
        return $this->fetch();
    }

    /**
     * 一级分类
     */
    public function get_cate_list()
    {
        $list = CategoryModel::where('pid', 0)->where('is_show', 1)->select();
        return Json::successful($list);
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = Upload::image('file', 'store/category' . date('Ymd'));
        $thumbPath = Upload::thumb($res->dir);
        //产品图片上传记录
        $fileInfo = $res->fileInfo->getinfo();
        SystemAttachment::attachmentAdd($res->fileInfo->getSaveName(), $fileInfo['size'], $fileInfo['type'], $res->dir, $thumbPath, 1);

        if ($res->status == 200)
            return Json::successful('图片上传成功!', ['name' => $res->fileInfo->getSaveName(), 'url' => Upload::pathToUrl($thumbPath)]);
        else
            return Json::fail($res->error);
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
            'pid',
            'cate_name',
            ['pic', []],
            'sort',
            ['is_show', 0]
        ], $request);
        if ($data['pid'] == '') return Json::fail('请选择父类');
        if (!$data['cate_name']) return Json::fail('请输入分类名称');
        if ($data['pid'] > 0) {
            if (count($data['pic']) < 1) return Json::fail('请上传分类图标');
        }
        if ($data['sort'] < 0) $data['sort'] = 0;
        $data['pic'] = $data['pic'][0];
        if ($id) {
            if (CategoryModel::where(['cate_name' => $data['cate_name']])->where('id', '<>', $id)->count() >= 1) return Json::fail('分类名称已存在');
            CategoryModel::edit($data, $id);
            return Json::successful('修改成功!');
        } else {
            $data['add_time'] = time();
            if (CategoryModel::be(['cate_name' => $data['cate_name']])) {
                return Json::fail('分类名称已存在！');
            }
            CategoryModel::set($data);
            return Json::successful('添加分类成功!');
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
        if (!CategoryModel::delCategory($id))
            return Json::fail(CategoryModel::getErrorInfo('删除失败,请删除字分类后再试!'));
        else
            return Json::successful('删除成功!');
    }
}
