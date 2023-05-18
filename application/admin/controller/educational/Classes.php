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

namespace app\admin\controller\educational;

use app\admin\controller\AuthController;
use service\JsonService as Json;
use app\admin\model\educational\Classes as ClassesModel;
use app\admin\model\educational\Teacher as TeacherModel;
use app\admin\model\educational\Student as StudentModel;
use app\admin\model\merchant\Merchant;
use service\FormBuilder as Form;
use think\Db;
use think\Url;

/**
 * 班级管理
 * Class Classes
 */
class Classes extends AuthController
{
    /**
     * 班级列表
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 获取班级列表
     */
    public function getClassesList()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['status', ''],
            ['title', ''],
        ]);
        return Json::successlayui(ClassesModel::getClassesLists($where));
    }

    /**
     * 老师列表
     */
    public function getTeacherList()
    {
        $list = TeacherModel::where(['is_del' => 0])->field('name,id')->order('sort desc,add_time desc')->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        return Json::successful($list);
    }

    /**关联专题
     * @param int $id
     */
    public function relation($id = 0)
    {
        if (!$id) Json::fail('缺少参数');
        $questions = ClassesModel::get($id);
        if (!$questions) Json::fail('班级不存在');
        $mer_list = Merchant::getMerchantList();
        $this->assign(['id' => $id, 'relation_ids' => $questions['relation'],'mer_list' => $mer_list]);
        return $this->fetch('relation');
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
        if ($field == 'sort' && bcsub($value, 0, 0) < 0) return Json::fail('排序不能为负数');
        $res = parent::getDataModification('classes', $id, $field, $value);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**添加/编辑
     * @param int $id
     * @return mixed
     */
    public function create($id = 0)
    {
        $classes = $id > 0 ? ClassesModel::get($id) : [];
        $this->assign(['id' => $id, 'classes' => json_encode($classes)]);
        return $this->fetch();
    }

    /**添加/编辑班级
     * @param int $id
     */
    public function save_add($id = 0)
    {
        $data = parent::postMore([
            ['title', ''],
            ['status', 1],
            ['upper_limit', 0],
            ['teacher_id', ''],
            ['start_time', ''],
            ['end_time', ''],
            ['sort', 0]
        ]);
        if (!$data['title']) return Json::fail('请输入班级名称');
        if ($data['upper_limit'] <= 0) return Json::fail('请输入班级学员数量上限');
        if ($data['teacher_id'] == '') return Json::fail('请选择班级老师');
        if ($data['start_time'] == '') return Json::fail('请选择班级开班时间');
        if ($data['end_time'] == '') return Json::fail('请选择班级结班时间');
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        if ($data['end_time'] <= $data['start_time']) return Json::fail('结班时间不能小于等于开班时间');
        if ($id) {
            if (ClassesModel::where(['title' => $data['title'], 'is_del' => 0])->where('id', '<>', $id)->count() >= 1) return Json::fail('班级名称已存在');
            $res = ClassesModel::edit($data, $id);
        } else {
            $data['add_time'] = time();
            if (!ClassesModel::be(['title' => $data['title'], 'is_del' => 0])) {
                $res = ClassesModel::set($data);
            } else {
                return Json::fail('班级已存在');
            }
        }
        if ($res) {
            return Json::successful('添加/编辑成功');
        } else {
            return Json::fail('添加/编辑失败');
        }
    }

    /**删除班级
     * @param int $id
     */
    public function delete($id = 0)
    {
        if (!$id) return Json::fail('参数错误');
        $classes = ClassesModel::get($id);
        if (!$classes) return Json::fail('要删除的班级不存在');
        $res = parent::getDataModification('classes', $id, 'is_del', 1);
        if ($res) {
            $count = StudentModel::where('classes_id', $classes['id'])->count();
            if ($count) StudentModel::where('classes_id', $classes['id'])->update(['is_del' => 1]);
            return Json::successful('删除成功');
        } else {
            return Json::fail('删除失败');
        }
    }
}
