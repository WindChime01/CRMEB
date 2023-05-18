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
use app\admin\model\educational\Teacher as TeacherModel;
use app\admin\model\educational\TeacherCategpry;
use app\admin\model\user\User;

/**
 * 老师控制器
 * Class Teacher
 */
class Teacher extends AuthController
{
    /**
     * 老师列表
     */
    public function index()
    {
        $this->assign(['category' => TeacherCategpry::taskCategoryAll(2)]);
        return $this->fetch();
    }

    /**
     * 获取老师列表
     */
    public function getTeacherList()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['pid', 0],
            ['title', ''],
        ]);
        return Json::successlayui(TeacherModel::getTeacherLists($where));
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
        $res = parent::getDataModification('teacher', $id, $field, $value);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**添加/编辑
     * @param int $id
     * @return mixed
     */
    public function create($id = 0, $uid = 0)
    {
        $teacher = $id > 0 ? TeacherModel::get($id) : [];
        $this->assign(['id' => $id, 'uid' => $uid, 'teacher' => json_encode($teacher)]);
        return $this->fetch();
    }

    /**
     * 获取老师分类
     */
    public function get_teacher_cate()
    {
        $category = TeacherCategpry::taskCategoryAll(2);
        return Json::successful($category);
    }

    /**添加/编辑老师
     * @param int $id
     */
    public function save_add($id = 0)
    {
        $data = parent::postMore([
            ['name', ''],
            ['uid', 0],
            ['pid', 0],
            ['image', ''],
            ['position', ''],
            ['phone', ''],
            ['sort', 0]
        ]);
        if ($data['pid'] <= 0) return Json::fail('请选择老师分类');
        if (!$data['name']) return Json::fail('请输入老师名称');
        if (!$data['image']) return Json::fail('请选择老师头像');
        if (!$data['position']) return Json::fail('请输入老师职位');
        if (!$data['phone']) return Json::fail('请输入老师手机号');
        if (!check_phone($data['phone'])) return Json::fail('手机号不正确');
        TeacherModel::beginTrans();
        if ($id) {
            $res = TeacherModel::edit($data, $id);
        } else {
            $user = User::where('uid', $data['uid'])->field('identitys,nickname')->find();
            if ($user['identitys'] == 2) return Json::fail('该用户已是老师');
            if ($user['identitys'] == 1) return Json::fail('该用户已是学员');
            $data['nickname'] = $user['nickname'];
            $data['add_time'] = time();
            if (!TeacherModel::be(['uid' => $data['uid'], 'is_del' => 0])) {
                $res = TeacherModel::set($data);
                if ($res) User::where('uid', $data['uid'])->update(['identitys' => 2]);
            } else {
                TeacherModel::rollbackTrans();
                return Json::fail('该用户已是老师');
            }
        }
        if ($res) {
            TeacherModel::commitTrans();
            return Json::successful('添加/编辑成功');
        } else {
            TeacherModel::rollbackTrans();
            return Json::fail('添加/编辑失败');
        }
    }

    /**删除老师
     * @param int $id
     */
    public function delete($id = 0)
    {
        if (!$id) return Json::fail('参数错误');
        $teacher = TeacherModel::get($id);
        if (!$teacher) return Json::fail('要删除的老师不存在');
        $res = parent::getDataModification('teacher', $id, 'is_del', 1);
        if ($res) {
            User::where('uid', $teacher['uid'])->update(['identitys' => 0]);
            return Json::successful('删除成功');
        } else {
            return Json::fail('删除失败');
        }
    }

    /**获取用户
     * @return mixed
     */
    public function user()
    {
        return $this->fetch();
    }

    /**
     * 获取用户列表
     */
    public function user_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['nickname', ''],
            ['identitys', 0],
            ['order', '']
        ]);
        return Json::successlayui(User::add_teacher_user_list($where));
    }
}
