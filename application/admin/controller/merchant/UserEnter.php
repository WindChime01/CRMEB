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

namespace app\admin\controller\merchant;

use app\admin\controller\AuthController;
use app\admin\model\special\Lecturer;
use app\admin\model\special\Lecturer as LecturerModel;
use app\admin\model\merchant\UserEnter as UserEnterModel;
use app\admin\model\merchant\Merchant as MerchantModel;
use app\admin\model\merchant\MerchantAdmin as MerchantAdminModel;
use app\merchant\model\merchant\MerchantMenus;
use app\admin\model\user\User;
use service\JsonService;
use service\FormBuilder as Form;
use think\Url;

/**
 * 讲师申请控制器
 */
class UserEnter extends AuthController
{
    /**
     * 讲师申请列表展示
     * @return
     * */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 讲师申请列表获取
     * @return
     * */
    public function lecturer_enter_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['title', ''],
        ]);
        return JsonService::successlayui(UserEnterModel::getLecturerList($where));
    }

    /**
     * 删除讲师申请
     * @param int $id 修改的主键
     * @return json
     * */
    public function delete($id = 0)
    {
        if (!$id) return JsonService::fail('缺少参数');
        if (UserEnterModel::delLecturer($id))
            return JsonService::successful('删除成功');
        else
            return JsonService::fail(UserEnterModel::getErrorInfo('删除失败'));
    }

    /**查看申请
     * @param int $id
     * @return mixed
     */
    public function see($id = 0)
    {
        $this->assign(['id' => $id]);
        return $this->fetch();
    }

    /**申请数据
     * @param $id
     * @throws \think\exception\DbException
     */
    public function getUserEnter($id)
    {
        $data = UserEnterModel::get($id);
        $data['address'] = $data['province'] . $data['city'] . $data['district'] . $data['address'];
        $data['label'] = json_decode($data['label']);
        if ($data['charter']) {
            $data['charter'] = json_decode($data['charter']);
        } else {
            $data['charter'] = [];
        }
        return JsonService::successful($data);
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
        if (!UserEnterModel::be(['id' => $id, 'status' => 0])) return JsonService::fail('操作记录不存在或状态错误!');
        $enter = UserEnterModel::get($id);
        if (!$enter) return JsonService::fail('操作记录不存!');
        if ($enter->status != 0) return Json::fail('您已审核,请勿重复操作');
        UserEnterModel::beginTrans();
        $res = UserEnterModel::changeFail($id, $enter['uid'], $fail_msg['message'], $enter);
        if ($res) {
            UserEnterModel::commitTrans();
            return JsonService::successful('操作成功!');
        } else {
            UserEnterModel::rollbackTrans();
            return JsonService::fail('操作失败!');
        }
    }

    /**通过
     * @param $id
     * @throws \think\exception\DbException
     */
    public function succ($id)
    {
        if (!UserEnterModel::be(['id' => $id, 'status' => 0])) return JsonService::fail('操作记录不存在或状态错误!');
        $enter = UserEnterModel::get($id);
        if (!$enter) return JsonService::fail('操作记录不存!');
        if ($enter->status != 0) return Json::fail('您已审核,请勿重复操作');
        UserEnterModel::beginTrans();
        $res = UserEnterModel::changeSuccess($id, $enter['uid'], $enter);
        if ($res) {
            UserEnterModel::commitTrans();
            return JsonService::successful('操作成功!');
        } else {
            UserEnterModel::rollbackTrans();
            return JsonService::fail('操作失败!');
        }
    }

    /**生成讲师后台
     * @return mixed
     */
    public function create($id)
    {
        $enter = UserEnterModel::get($id);
        $this->assign([
            'title' => '添加讲师后台',
            'enter' => json_encode($enter),
            'action' => Url::build('save'),
            'menus' => json(MerchantMenus::ruleList())->getContent()
        ]);
        return $this->fetch();
    }

    /**
     * 添加讲师商户
     */
    public function save()
    {
        $data = parent::postMore([
            'account',
            ['id', 0],
            ['uid', 0],
            'conf_pwd',
            'pwd',
            'mer_name',
            'real_name',
            'mer_phone',
            'mer_special_divide',
            'mer_store_divide',
            'mer_event_divide',
            'mer_data_divide',
            'mer_test_divide',
            'gold_divide',
            'mark',
            'mer_address',
            ['checked_menus', [], '', 'rules'],
            ['is_source', 0],
            ['is_audit', 0],
            ['status', 0]
        ]);
        if (!is_array($data['rules']) || !count($data['rules'])) return JsonService::fail('请选择最少一个权限');
        foreach ($data['rules'] as &$v) {
            $pid = MerchantMenus::where('id', $v)->value('pid');
            if (!in_array($pid, $data['rules'])) $data['rules'][] = $pid;
        }
        $data['rules'] = implode(',', $data['rules']);
        if (!$data['account']) return JsonService::fail('请输入讲师后台账号');
        if (MerchantAdminModel::where('account', trim($data['account']))->where('is_del', 0)->count()) return JsonService::fail('商户账号已存在,请使用别的商户账号注册');
        if (!$data['pwd']) return JsonService::fail('请输入讲师后台登陆密码');
        if ($data['pwd'] != $data['conf_pwd']) return JsonService::fail('两次输入密码不想同');
        if (!$data['mer_name']) return JsonService::fail('请输入讲师后台名称');
        if (!$data['uid']) return JsonService::fail('请输入绑定的用户ID');
        $user = User::where('uid', $data['uid'])->find();
        if (!$user) {
            return JsonService::fail('绑定的用户不存在');
        } else {
            if ($user['business'] == 1) {
                return JsonService::fail('该用户已是讲师');
            }
        }
        $data['pwd'] = trim(md5($data['pwd']));
        $data['reg_time'] = time();
        $data['add_time'] = time();
        $data['reg_admin_id'] = $this->adminId;
        $admin = array();
        $admin['account'] = trim($data['account']);
        $admin['pwd'] = $data['pwd'];
        $enter = UserEnterModel::get($data['id']);
        $data['mer_avatar'] = $enter['merchant_head'];
        $data['estate'] = 1;
        UserEnterModel::where('id', $data['id'])->update(['status' => 2]);
        unset($data['id']);
        unset($data['conf_pwd']);
        unset($data['account']);
        unset($data['pwd']);
        MerchantModel::beginTrans();
        $res = MerchantModel::set($data);
        $res1 = false;
        if ($res) {
            $admin['uid'] = $data['uid'];
            $admin['mer_id'] = $res->id;
            $admin['real_name'] = $data['mer_name'];
            $admin['rules'] = $data['rules'];
            $admin['phone'] = $data['mer_phone'];
            $admin['add_time'] = time();
            $admin['status'] = 1;
            $admin['level'] = 0;
            $res1 = MerchantAdminModel::set($admin);
        }
        $res2 = false;
        if ($res1 && $res) {
            $res2 = Lecturer::addLecturer($enter, $res->id);
        }
        $bool = false;
        if ($res1 && $res && $res2) $bool = true;
        MerchantModel::checkTrans($bool);
        if ($bool) {
            MerchantModel::where('id', $res->id)->update(['lecturer_id' => $res2->id]);
            User::where('uid', $data['uid'])->update(['business' => 1]);
            return JsonService::successful('添加讲师后台成功!');
        } else {
            return JsonService::successful('添加讲师后台失败!');
        }

    }
}
