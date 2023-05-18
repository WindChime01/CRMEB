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
use app\admin\model\educational\Classes as ClassesModel;
use app\admin\model\educational\Student as StudentModel;
use app\admin\model\educational\ContactPhone as ContactPhoneModel;
use app\admin\model\questions\TestPaper as TestPaperModel;
use app\admin\model\download\DataDownloadBuy;
use app\admin\model\questions\TestPaperObtain;
use app\admin\model\user\User;
use app\admin\model\special\Special;
use app\admin\model\special\SpecialSource;
use app\admin\model\special\SpecialBuy;
use app\admin\model\merchant\Merchant;

/**
 * 学员
 * Class Student
 */
class Student extends AuthController
{
    /**
     * 学员列表
     */
    public function index($cid = 0)
    {
        $this->assign(['cid' => $cid, 'classes' => ClassesModel::classesList()]);
        return $this->fetch();
    }

    /**
     * 获取学员列表
     */
    public function getStudentList($c_id = 0)
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['cid', 0],
            ['title', ''],
        ]);
        if ($c_id > 0 && $where['cid'] == 0) $where['cid'] = $c_id;
        return Json::successlayui(StudentModel::getStudentLists($where));
    }

    /**
     * 班级列表
     */
    public function classesList()
    {
        $classes = ClassesModel::classesList();
        return Json::successful($classes);
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
        $res = parent::getDataModification('student', $id, $field, $value);
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
        $student = $id > 0 ? StudentModel::get($id) : [];
        if ($student && $id) $phone = ContactPhoneModel::contactPhoneList($id);
        else $phone = [];
        $this->assign(['id' => $id, 'uid' => $uid, 'student' => json_encode($student), 'phone' => json_encode($phone)]);
        return $this->fetch();
    }

    /**添加/编辑学员
     * @param int $id
     */
    public function save_add($id = 0)
    {
        $data = parent::postMore([
            ['name', ''],
            ['uid', 0],
            ['classes_id', 0],
            ['sex', 0],
            ['image', ''],
            ['province', ''],
            ['city', ''],
            ['district', ''],
            ['detail', ''],
            ['contact', ''],
            ['sort', 0]
        ]);
        if ($data['classes_id'] <= 0) return Json::fail('请选择所在班级');
        if (!$data['name']) return Json::fail('请输入学员名称');
        if (!$data['image']) return Json::fail('请选择学员头像');
        if (!$data['province'] || !$data['city'] || !$data['district'] || !$data['detail']) return Json::fail('请选择地址');
        $contact = json_decode($data['contact'], true);
        StudentModel::beginTrans();
        if ($id) {
            $res = StudentModel::edit($data, $id);
            $res1 = ContactPhoneModel::contactPhoneAdd($id, $contact);
        } else {
            $user = User::where('uid', $data['uid'])->field('identitys,nickname')->find();
            if ($user['identitys'] == 2) return Json::fail('该用户已是老师');
            $data['nickname'] = $user['nickname'];
            $data['add_time'] = time();
            $res = true;
            if (!StudentModel::be(['uid' => $data['uid'], 'classes_id' => $data['classes_id'], 'is_del' => 0])) {
                $upper_limit = ClassesModel::where('id', $data['classes_id'])->value('upper_limit');
                $count = StudentModel::where('classes_id', $data['classes_id'])->where('is_del', 0)->count();
                if (bcsub($count, $upper_limit, 0) >= 0) return Json::fail('班级学员数量已达上限，不能添加！');
                $id = StudentModel::insertGetId($data);
                $res1 = ContactPhoneModel::contactPhoneAdd($id, $contact);
                if ($id && $res1) User::edit(['identitys' => 1], $data['uid'], 'uid');
            } else {
                StudentModel::rollbackTrans();
                return Json::fail('该用户已在班级中');
            }
        }
        if ($res && $id && $res1) {
            StudentModel::commitTrans();
            return Json::successful('添加/编辑成功');
        } else {
            StudentModel::rollbackTrans();
            return Json::fail('添加/编辑失败');
        }
    }

    /**删除学员
     * @param int $id
     */
    public function delete($id = 0)
    {
        if (!$id) return Json::fail('参数错误');
        $student = StudentModel::get($id);
        if (!$student) return Json::fail('要删除的学员不存在');
        $res = parent::getDataModification('student', $id, 'is_del', 1);
        if ($res) {
            User::edit(['identitys' => 0], $student['uid'], 'uid');
            ContactPhoneModel::where('sid', $id)->delete();
            return Json::successful('删除成功');
        } else {
            return Json::fail('删除失败');
        }
    }

    /**给学员发送试卷
     * @param string $uid
     * @return mixed
     */
    public function send()
    {
        $mer_list = Merchant::getMerchantList();
        $this->assign(['mer_list' => $mer_list]);
        return $this->fetch();
    }

    /**
     *试卷列表
     */
    public function getTestPaperList()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['pid', ''],
            ['type', ''],
            ['is_show', 1],
            ['status', 1],
            ['mer_id', 0],
            ['title', '']
        ]);
        return Json::successlayui(TestPaperModel::sendTestPaperExercisesList($where));
    }

    /**发送试卷
     * @param $sid
     * @param $tid
     */
    public function sendTestPaper($sid, $tid)
    {
        if (!$sid || !$tid) return Json::fail('缺少参数无法赠送');
        $sid = explode(',', $sid);
        $tid = explode(',', $tid);
        $res = false;
        foreach ($sid as $k => $item) {
            $res = TestPaperObtain::addsend($item, $tid);
        }
        if ($res) {
            return Json::successful('发送成功');
        } else {
            return Json::fail('发送失败');
        }
    }

    /**
     *移除已发送的试卷
     */
    public function removeTestPaper($uid, $test_id)
    {
        if (!$uid || !$test_id) return Json::fail('参数错误');
        $res = TestPaperObtain::where(['uid' => $uid, 'test_id' => $test_id])->update(['is_del' => 1]);
        if ($res) {
            return Json::successful('移除成功');
        } else {
            return Json::fail('移除失败');
        }
    }

    /**给学员发送课程
     * @param string $uid
     * @return mixed
     */
    public function special()
    {
        $mer_list = Merchant::getMerchantList();
        $this->assign(['mer_list' => $mer_list]);
        return $this->fetch();
    }

    /**发送专题
     * @param $sid
     * @param $tid
     */
    public function sendSpecial($uid, $tid)
    {
        if (!$uid || !$tid) return Json::fail('缺少参数无法赠送');
        $uid = explode(',', $uid);
        $tid = explode(',', $tid);
        $res = false;
        foreach ($uid as $k => $item) {
            foreach ($tid as $h => $value) {
                $res = $this->save_give($item, $value);
            }
        }
        if ($res) {
            return Json::successful('赠送成功');
        } else {
            return Json::fail('赠送失败');
        }
    }

    /**赠送专题
     * @param $uid
     * @param $special_id
     * @return bool|object
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function save_give($uid, $special_id)
    {
        if (!$uid || !$special_id) return false;
        $special = Special::PreWhere()->where(['id'=>$special_id])->find();
        if (SpecialBuy::be(['uid' => $uid, 'special_id' => $special_id, 'is_del' => 0, 'type' => 3])) return true;
        if ($special['type'] == SPECIAL_COLUMN) {
            $special_source = SpecialSource::getSpecialSource($special['id']);
            if ($special_source) {
                foreach ($special_source as $k => $v) {
                    $task_special = Special::PreWhere()->where(['id'=>$v['source_id']])->find();
                    if ($task_special['is_show'] == 1) {
                        SpecialBuy::setBuySpecial('', $uid, $v['source_id'], 3, $task_special['validity'], $special_id);
                    }
                }
            }
        }
        $res = SpecialBuy::setBuySpecial('', $uid, $special_id, 3, $special['validity']);
        if ($res) {
            TestPaperObtain::setTestPaper('', $uid, $special_id, 3);
            DataDownloadBuy::setDataDownload('', $uid, $special_id, 2);
        }
        return $res;
    }


    /**已获得的课程
     * @param int $uid
     * @return mixed
     */
    public function special_list($uid = 0)
    {
        $this->assign('uid', $uid);
        return $this->fetch();
    }

    /**获取已获得课程
     * @param $uid
     */
    public function getUserSpecialList($uid)
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20]
        ]);
        $where['uid'] = $uid;
        $special_task = Special::getUserSpecialList($where);
        if (isset($special_task['data']) && $special_task['data']) {
            foreach ($special_task['data'] as $k => $v) {
                if (isset($where['is_light']) && $v['is_light'] && $v['type'] == 6) {
                    $special_task['data'][$k]['types'] = lightTypeNmae($v['light_type']);
                } else {
                    $special_task['data'][$k]['types'] = parent::specialTaskType($v['type']);
                }
            }
        }
        return Json::successlayui($special_task);
    }

    /**移除专题
     * @param int $id
     * @throws \think\exception\DbException
     */
    public function del_special_buy($uid = 0, $special_id = 0)
    {
        if (!$uid || !$special_id) return Json::fail('缺少参数');
        $type = Special::PreWhere()->where(['id' => $special_id])->value('type');
        $res = SpecialBuy::where(['uid' => $uid, 'type' => 3, 'special_id' => $special_id])->update(['is_del' => 1]);
        if ($type == SPECIAL_COLUMN) {
            $res2 = SpecialBuy::where(['uid' => $uid, 'type' => 3, 'column_id' => $special_id])->update(['is_del' => 1]);
            $res = $res && $res2;
        }
        if ($res) {
            TestPaperObtain::delTestPaper('', $uid, $special_id, 3);
            DataDownloadBuy::delDataDownload('', $uid, $special_id, 2);
            return Json::successful('移除成功');
        } else
            return Json::fail('移除失败');
    }

    /**已获得试卷
     * @param int $uid
     * @return mixed
     */
    public function test_paper($uid = 0)
    {
        $this->assign('uid', $uid);
        return $this->fetch();
    }

    /**获取已获得试卷
     * @param $uid
     */
    public function getUserTestPaperList($uid)
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20]
        ]);
        $where['uid'] = $uid;
        return Json::successlayui(TestPaperModel::getUserTestPaperList($where));
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
            ['identitys', 1],
            ['order', '']
        ]);
        return Json::successlayui(User::add_teacher_user_list($where));
    }
}
