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

namespace app\admin\controller\special;

use app\admin\model\special\SpecialSubject;
use app\admin\model\special\Special;
use think\Url;
use service\FormBuilder as Form;
use service\JsonService as Json;
use app\admin\controller\AuthController;

/**
 * 课程分类控制器
 * Class Subject
 * @package app\admin\controller\special
 */
class Subject extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    public function get_subject_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['pid', $this->request->param('pid', '')],
            ['name', ''],
        ]);
        return Json::successful(SpecialSubject::get_subject_list($where));
    }

    /**
     * 创建分类
     * @param int $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function create($id = 0)
    {
        $cate = $id > 0 ? SpecialSubject::get($id) : [];
        $this->assign(['cate' => json_encode($cate), 'id' => $id]);
        return $this->fetch();
    }

    /**获取一级分类
     * @param int $sid
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_cate_list()
    {
        $cate = SpecialSubject::specialCategoryAll(1);
        $array = [];
        $oneCate['id'] = 0;
        $oneCate['name'] = '顶级分类';
        array_push($array, $oneCate);
        foreach ($cate as $key => $value) {
            array_push($array, $value);
        }
        return Json::successful($array);
    }

    /**
     * 新增或者修改
     *
     * @return json
     */
    public function save($id = 0)
    {
        $post = parent::postMore([
            ['name', ''],
            ['pic', ''],
            ['grade_id', 0],
            ['sort', 0],
            ['is_show', 0],
        ]);
        if (!$post['name']) return Json::fail('请输入分类名称');
        if ($post['grade_id'] && !$post['pic']) return Json::fail('请选择分类图标');
        if ($id) {
            $cate = SpecialSubject::get($id);
            if (!$cate['grade_id'] && $post['grade_id'] && SpecialSubject::be(['grade_id' => $id, 'is_del' => 0])) return Json::fail('无法移动有下级的分类');
            if (SpecialSubject::where(['name' => $post['name'], 'is_del' => 0])->where('id', '<>', $id)->count() >= 1) return Json::fail('分类名称已存在');
            $res = SpecialSubject::edit($post, $id);
            if ($res)
                return Json::successful('修改成功');
            else
                return Json::fail('修改失败');
        } else {
            $post['add_time'] = time();
            if (SpecialSubject::be(['name' => $post['name'], 'is_del' => 0])) {
                return Json::fail('分类名称已存在！');
            }
            $res = SpecialSubject::set($post);
            if ($res)
                return Json::successful('添加成功');
            else
                return Json::fail('添加失败');
        }
    }

    /**
     * 快速编辑
     *
     * @return json
     */
    public function set_value($field = '', $id = '', $value = '')
    {
        ($field == '' || $id == '' || $value == '') && Json::fail('缺少参数');
        $res = parent::getDataModification('subject', $id, $field, $value);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**二级分是否显示快捷操作
     * @param string $is_show
     * @param string $id
     * @return mixed
     */
    public function set_show($is_show = '', $id = '')
    {
        ($is_show == '' || $id == '') && Json::fail('缺少参数');
        $res = parent::getDataModification('subject', $id, 'is_show', (int)$is_show);
        if ($res) {
            return Json::successful($is_show == 1 ? '显示成功' : '隐藏成功');
        } else {
            return Json::fail($is_show == 1 ? '显示失败' : '隐藏失败');
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
        $subject = SpecialSubject::get($id);
        if ($subject['grade_id']) {
            if (Special::where('subject_id', $id)->where('is_del', 0)->count()) return Json::fail('暂无法删除,请先去除专题关联');
        } else {
            if (SpecialSubject::where('grade_id', $id)->where('is_del', 0)->count()) return Json::fail('暂无法删除,请删除下级分类');
        }
        $res = parent::getDataModification('subject', $id, 'is_del', 1);
        if ($res)
            return Json::successful('删除成功');
        else
            return Json::fail('删除成功');
    }
}
