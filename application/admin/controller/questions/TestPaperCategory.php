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

namespace app\admin\controller\questions;

use app\admin\controller\AuthController;
use service\JsonService as Json;
use app\admin\model\questions\TestPaperCategory as TestPaperCategoryModel;
use app\admin\model\questions\TestPaper;

/**
 * 试卷分类
 * Class TestPaperCategory
 */
class TestPaperCategory extends AuthController
{
    public function index($type = 1)
    {
        $this->assign(['type' => $type]);
        return $this->fetch();
    }

    public function get_category_list($type = 1)
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['tid', 0],
            ['title', ''],
        ]);
        return Json::successful(TestPaperCategoryModel::getAllList($where, $type));
    }

    /**
     * 创建分类
     * @param int $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function create($id = 0, $type = 1)
    {
        $cate = $id > 0 ? TestPaperCategoryModel::get($id) : [];
        $this->assign(['cate' => json_encode($cate), 'id' => $id, 'type' => $type]);
        return $this->fetch();
    }

    public function get_cate_list($type = 1)
    {
        $category = TestPaperCategoryModel::taskCategoryAll(2, $type);
        return Json::successful($category);
    }

    /**一级分类
     * @param int $type
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add_cate_list($type = 1)
    {
        $category = TestPaperCategoryModel::where(['pid' => 0, 'is_del' => 0, 'type' => $type])->select();
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
        ($field == '' || $id == '' || $value == '') && Json::fail('缺少参数');
        $res = parent::getDataModification('test_paper', $id, $field, $value);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**
     * 新增或者修改
     * @return json
     */
    public function save($id = 0, $type = 1)
    {
        $post = parent::postMore([
            ['title', ''],
            ['pid', ''],
            ['sort', 0],
        ]);
        if (!$post['title']) return Json::fail('请输入分类名称');
        if ($id) {
            $cate = TestPaperCategoryModel::get($id);
            if (!$cate['pid'] && $post['pid'] && TestPaperCategoryModel::be(['pid' => $id, 'type' => $type, 'is_del' => 0])) return Json::fail('无法移动有下级的分类');
            if (TestPaperCategoryModel::where(['title' => $post['title'], 'is_del' => 0])->where('id', '<>', $id)->count() >= 1) return Json::fail('分类名称已存在');
            $res = TestPaperCategoryModel::edit($post, $id);
            if ($res)
                return Json::successful('修改成功');
            else
                return Json::fail('修改失败');
        } else {
            $post['add_time'] = time();
            $post['type'] = $type;
            $res = TestPaperCategoryModel::set($post);
            if ($res)
                return Json::successful('添加成功');
            else
                return Json::fail('添加失败');
        }
    }

    /**
     * 删除
     * @return json
     */
    public function delete($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        $cate = TestPaperCategoryModel::get($id);
        if (!$cate['pid']) {
            $count = TestPaperCategoryModel::where('pid', $id)->where('is_del', 0)->count();
            if ($count) return Json::fail('暂无法删除,请删除下级分类');
        }
        if (TestPaper::where('tid', $id)->where('is_del', 0)->count()) return Json::fail('暂无法删除,请先删除试卷');
        $res = parent::getDataModification('test_paper', $id, 'is_del', 1);
        if ($res)
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }
}
