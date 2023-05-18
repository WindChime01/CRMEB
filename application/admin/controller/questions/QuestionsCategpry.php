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
use app\admin\model\questions\QuestionsCategpry as QuestionsCategpryModel;
use app\admin\model\questions\Questions as QuestionsModel;

/**
 * 试题分类
 * Class QuestionsCategpry
 */
class QuestionsCategpry extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    public function get_category_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['pid', 0],
            ['title', '']
        ]);
        return Json::successful(QuestionsCategpryModel::getAllList($where));
    }

    /**
     * 创建分类
     * @param int $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function create($id = 0)
    {
        $cate = $id > 0 ? QuestionsCategpryModel::get($id) : [];
        $this->assign(['cate' => json_encode($cate), 'id' => $id]);
        return $this->fetch();
    }

    public function get_cate_list()
    {
        $category = QuestionsCategpryModel::taskCategoryAll(2);
        return Json::successful($category);
    }

    public function add_cate_list()
    {
        $category = QuestionsCategpryModel::where(['pid' => 0, 'is_del' => 0, 'mer_id' => 0])->select();
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
     *
     * @return json
     */
    public function set_value($field = '', $id = '', $value = '')
    {
        $field == '' || $id == '' || $value == '' && Json::fail('缺少参数');
        $res = parent::getDataModification('categpry', $id, $field, $value);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**
     * 新增或者修改
     *
     * @return json
     */
    public function save($id = 0)
    {
        $post = parent::postMore([
            ['title', ''],
            ['pid', ''],
            ['sort', 0]
        ]);
        if (!$post['title']) return Json::fail('请输入分类名称');
        if ($id) {
            $cate = QuestionsCategpryModel::get($id);
            if (!$cate['pid'] && $post['pid'] && QuestionsCategpryModel::be(['pid' => $id, 'is_del' => 0, 'mer_id' => 0])) return Json::fail('无法移动有下级的分类');
            if (QuestionsCategpryModel::where(['title' => $post['title'], 'is_del' => 0, 'mer_id' => 0])->where('id', '<>', $id)->count() >= 1) return Json::fail('分类名称已存在');
            $res = QuestionsCategpryModel::edit($post, $id);
            if ($res)
                return Json::successful('修改成功');
            else
                return Json::fail('修改失败');
        } else {
            $post['add_time'] = time();
            $res = QuestionsCategpryModel::set($post);
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
        $cate = QuestionsCategpryModel::get($id);
        if (!$cate['pid']) {
            $count = QuestionsCategpryModel::where('pid', $id)->where(['is_del' => 0, 'mer_id' => 0])->count();
            if ($count) return Json::fail('暂无法删除,请删除下级分类');
        }
        if (QuestionsModel::where('pid', $id)->where(['is_del' => 0, 'mer_id' => 0])->count()) return Json::fail('暂无法删除,请先删除题目');
        $res = parent::getDataModification('categpry', $id, 'is_del', 1);
        if ($res)
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }
}
