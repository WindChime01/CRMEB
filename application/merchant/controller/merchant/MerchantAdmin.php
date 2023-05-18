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

namespace app\merchant\controller\merchant;

use app\merchant\controller\AuthController;
use service\FormBuilder as Form;
use service\JsonService as Json;
use think\Request;
use think\Url;
use app\merchant\model\merchant\MerchantAdmin as MerchantAdminModel;
use app\merchant\model\merchant\MerchantMenus;

/**
 * 管理员列表控制器
 * Class SystemAdmin
 * @package app\merchant\controller\merchant
 */
class MerchantAdmin extends AuthController
{
    /**管理员列表
     * @return mixed
     */
    public function index()
    {
        $where = parent::getMore([
            ['name', ''],
        ], $this->request);
        $this->assign('where', $where);
        $this->assign(MerchantAdminModel::systemPage($where, $this->adminInfo['level']));
        return $this->fetch();
    }

    /**
     * @return mixed
     */
    public function create($uid = 0)
    {
        $this->assign(['title' => '添加管理员', 'menus' => json(MerchantAdminModel::getRule($this->adminInfo['rules']))->getContent(), 'action' => Url::build('save')]);
        $this->assign('uid', $uid);
        return $this->fetch();
    }

    /**
     * @param Request $request
     * @return \think\response\Json
     */
    public function save(Request $request)
    {
        $data = parent::postMore([
            'account',
            'conf_pwd',
            'pwd',
            'real_name',
            'phone',
            'email',
            ['checked_menus', [], '', 'rules'],
            ['status', 0]
        ], $request);
        if (!$data['account']) return Json::fail('请输入管理员账号');
        if (MerchantAdminModel::getAccount($data['account'])) return Json::fail('管理员账号已存在,请重新填写');
        if (!$data['pwd']) return Json::fail('请输入管理员登陆密码');
        if ($data['pwd'] != $data['conf_pwd']) return Json::fail('两次输入密码不想同');
        $data['pwd'] = md5($data['pwd']);
        unset($data['conf_pwd']);
        $data['add_time'] = time();
        $data['mer_id'] = $this->adminInfo['mer_id'];
        $data['level'] = $this->adminInfo['level'] + 1;
        foreach ($data['rules'] as &$v) {
            $pid = MerchantMenus::where('id', $v)->value('pid');
            if (!in_array($pid, $data['rules'])) $data['rules'][] = $pid;
        }
        $data['rules'] = implode(',', $data['rules']);
        $data['account'] = MerchantAdminModel::where('level', 0)->where('mer_id', $this->adminInfo['mer_id'])->value('account') . '@' . $data['account'];
        MerchantAdminModel::set($data);
        return Json::successful('添加管理员成功!');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        if (!$id) return Json::fail('参数错误');
        $role = MerchantAdminModel::get($id);
        $this->assign(['title' => '编辑管理员', 'roles' => $role->toJson(), 'menus' => json(MerchantAdminModel::getRule($this->adminInfo['rules']))->getContent(), 'action' => Url::build('update', array('id' => $id))]);
        return $this->fetch();
    }

    /**
     * @param Request $request
     * @param $id
     * @return \think\response\Json
     */
    public function update(Request $request, $id)
    {
        if (!$id) return Json::fail('参数错误');
        $data = parent::postMore([
            'real_name',
            'phone',
            'email',
            ['checked_menus', [], '', 'rules'],
            ['status', 0]
        ], $request);
        foreach ($data['rules'] as &$v) {
            $pid = MerchantMenus::where('id', $v)->value('pid');
            if (!in_array($pid, $data['rules'])) $data['rules'][] = $pid;
        }
        $data['rules'] = implode(',', $data['rules']);
        MerchantAdminModel::edit($data, $id);
        return Json::successful('修改成功!');
    }

    /**
     * 删除当前管理员
     * @param $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        if (!$id) return Json::fail('参数错误');
        $data['is_del'] = 1;
        if (!MerchantAdminModel::edit($data, $id))
            return Json::fail('删除失败,请稍后再删除');
        else
            return Json::successful('删除成功');
    }

    /**
     * 重置密码  666888666
     * @param $id
     * @return \think\response\Json
     */
    public function reset_pwd($id)
    {
        if (!$id) return Json::fail('参数错误');
        if (!MerchantAdminModel::edit(['pwd' => md5(666888666)], $id, 'id'))
            return Json::fail('重置失败,请稍后再删除');
        else
            return Json::successful('重置成功');
    }


    /**
     * @param $id
     */
    public function edit_pwd($id)
    {
        $this->assign(['title' => '修改密码', 'rules' => $this->readPwd($id)->getContent(), 'action' => Url::build('updatePwd', array('id' => $id))]);
        return $this->fetch('public/common_form');
    }

    /**
     * @param $id
     */
    public function readPwd($id)
    {
        if (!$id) return Json::fail('数据错误');
        $merchant = MerchantAdminModel::get($id);
        if (!$merchant) return Json::fail('数据错误');
        $form = Form::create(Url::build('updatePwd', array('id' => $id)), [
            Form::input('account', '管理员账号', $merchant->getData('account'))->disabled(1),
            Form::input('real_name', '管理员名称', $merchant->getData('real_name'))->disabled(1),
            Form::input('pwd', '管理员新密码'),
            Form::input('conf_pwd', '确认密码'),
        ]);
        $form->setMethod('post')->setTitle('修改密码')->setSuccessScript('parent.$(".J_iframe:visible")[0].contentWindow.location.reload();');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * @param $id
     */
    public function updatePwd(Request $request, $id)
    {
        $data = parent::postMore([
            'conf_pwd',
            'pwd'
        ], $request);
        if (!$id) return Json::fail('数据错误');
        $merchant = MerchantAdminModel::get($id);
        if (!$merchant) return Json::fail('数据错误');
        if (!$data['pwd']) return Json::fail('请输入商户新密码');
        if ($data['pwd'] != $data['conf_pwd']) return Json::fail('两次输入密码不想同');
        $data['pwd'] = md5($data['pwd']);
        unset($data['conf_pwd']);
        MerchantAdminModel::edit($data, $id);
        return Json::successful('修改成功!');
    }

    /**
     * 个人资料 展示
     * @return string
     */
    public function admin_info()
    {
        $adminInfo = $this->adminInfo;//获取当前登录的管理员
        $this->assign('adminInfo', $adminInfo);
        return $this->fetch();
    }

    /**
     * 保存信息
     */
    public function setAdminInfo()
    {
        $adminInfo = $this->adminInfo;//获取当前登录的管理员
        if ($this->request->isPost()) {
            $data = parent::postMore([
                ['new_pwd', ''],
                ['new_pwd_ok', ''],
                ['pwd', ''],
                'real_name',
            ]);
            if ($data['pwd'] != '') {
                $pwd = md5($data['pwd']);
                if ($adminInfo['pwd'] != $pwd) return Json::fail('原始密码错误');
            }
            if ($data['new_pwd'] != '') {
                if (!$data['new_pwd_ok']) return Json::fail('请输入确认新密码');
                if ($data['new_pwd'] != $data['new_pwd_ok']) return Json::fail('俩次密码不一样');
            }
            if ($data['pwd'] != '' && $data['new_pwd'] != '') {
                $data['pwd'] = md5($data['new_pwd']);
            } else {
                unset($data['pwd']);
            }
            unset($data['new_pwd']);
            unset($data['new_pwd_ok']);
            if (!MerchantAdminModel::edit($data, $adminInfo['id'])) return Json::fail('修改失败');
            MerchantAdminModel::clearLoginInfo();
            return Json::successful('修改成功!,请重新登录');
        }
    }
}
