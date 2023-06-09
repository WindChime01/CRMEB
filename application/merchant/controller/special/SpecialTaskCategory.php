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

namespace app\merchant\controller\special;

use think\Url;
use service\FormBuilder as Form;
use service\JsonService as Json;
use app\merchant\controller\AuthController;
use app\merchant\model\special\SpecialTaskCategory as SpecialTaskCategoryModel;
use app\merchant\model\special\SpecialTask;

/**
 * 素材分类控制器
 * Class SpecialTaskCategory
 * @package app\merchant\controller\special
 */
class SpecialTaskCategory extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 素材列表
     */
    public function get_category_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['pid', 0],
            ['cate_name', ''],
        ]);
        $where['mer_id'] = $this->merchantId;
        return Json::successful(SpecialTaskCategoryModel::getAllList($where));
    }

    /**
     * 创建分类
     * @param int $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function create($id = 0)
    {
        $cate = $id > 0 ? SpecialTaskCategoryModel::get($id) : [];
        $this->assign(['cate' => json_encode($cate), 'id' => $id]);
        return $this->fetch();
    }

    public function get_cate_list()
    {
        $mer_id = $this->merchantId;
        $category = SpecialTaskCategoryModel::taskCategoryAll(2, $mer_id);
        return Json::successful($category);
    }

    public function add_cate_list()
    {
        $category = SpecialTaskCategoryModel::where(['pid' => 0, 'mer_id' => $this->merchantId, 'is_del' => 0])->select();
        $category = count($category) > 0 ? $category->toArray() : [];
        $array = [];
        $oneCate['id'] = 0;
        $oneCate['title'] = '顶级分类';
        array_push($array, $oneCate);
        foreach ($category as $key => $value) {
            array_push($array, $value);
        }
        return Json::successful($array);
    }

    /**
     * 快速编辑
     * @return json
     */
    public function set_value($field = '', $id = '', $value = '')
    {
        $field == '' || $id == '' || $value == '' && Json::fail('缺少参数');
        $res = parent::getDataModification('task_category', $id, $field, $value);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**
     * 新增或者修改
     * @return json
     */
    public function save($id = 0)
    {
        $post = parent::postMore([
            ['title', ''],
            ['pid', ''],
            ['sort', 0],
        ]);
        if (!$post['title']) return Json::fail('请输入分类名称');
        if ($id) {
            $cate = SpecialTaskCategoryModel::get($id);
            if (!$cate['pid'] && $post['pid'] && SpecialTaskCategoryModel::be(['pid' => $id, 'mer_id' => $this->merchantId, 'is_del' => 0])) return Json::fail('无法移动有下级的分类');
            if (SpecialTaskCategoryModel::where(['title' => $post['title'], 'mer_id' => $this->merchantId, 'is_del' => 0])->where('id', '<>', $id)->count() >= 1) return Json::fail('分类名称已存在');
            $res = SpecialTaskCategoryModel::edit($post, $id);
            if ($res)
                return Json::successful('修改成功');
            else
                return Json::fail('修改失败');
        } else {
            $post['add_time'] = time();
            $post['mer_id'] = $this->merchantId;
            $res = SpecialTaskCategoryModel::set($post);
            if ($res)
                return Json::successful('添加成功');
            else
                return Json::fail('添加失败');
        }
    }

    /**
     * 删除
     *
     * @return json
     */
    public function delete($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        $cate = SpecialTaskCategoryModel::get($id);
        if (!$cate['pid']) {
            $count = SpecialTaskCategoryModel::where(['pid' => $id, 'mer_id' => $this->merchantId, 'is_del' => 0])->count();
            if ($count) return Json::fail('暂无法删除,请删除下级分类');
        }
        if (SpecialTask::where(['pid' => $id, 'mer_id' => $this->merchantId, 'is_del' => 0])->count()) return Json::fail('暂无法删除,请先去除素材');
        $res = parent::getDataModification('task_category', $id, 'is_del', 1);
        if ($res)
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }
}
