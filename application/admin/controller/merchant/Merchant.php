<?php

namespace app\admin\controller\merchant;

use app\admin\controller\AuthController;
use app\admin\model\questions\TestPaper;
use app\admin\model\user\User;
use app\admin\model\merchant\UserEnter;
use app\merchant\model\merchant\MerchantMenus;
use service\HookService;
use service\JsonService;
use service\SystemConfigService;
use service\UploadService;
use think\Request;
use think\Url;
use app\admin\model\merchant\Merchant as MerchantModel;
use app\merchant\model\merchant\MerchantAdmin as MerchantAdminModel;
use app\admin\model\special\Lecturer;
use app\admin\model\special\Special;
use app\admin\model\download\DataDownload as DownloadModel;
use app\admin\model\store\StoreProduct as ProductModel;
use app\admin\model\ump\EventRegistration as EventRegistrationModel;

/**
 * Class Merchant
 * @package app\admin\controller\merchant
 */
class Merchant extends AuthController
{
    /**讲师后台列表
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 讲师列表获取
     * @return
     * */
    public function lecturer_merchant_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['title', ''],
        ]);
        return JsonService::successlayui(MerchantModel::getLecturerMerchantList($where));
    }

    /**
     * 删除讲师后台
     * @param int $id 修改的主键
     * @return json
     * */
    public function delete($id = 0)
    {
        if (!$id) return JsonService::fail('缺少参数');
        $merchant = MerchantModel::get($id);
        if (!$merchant) return JsonService::fail('讲师后台不存在');
        if (MerchantModel::delMerchant($id)) {
            Lecturer::where('mer_id', $id)->update(['is_del' => 1]);
            User::where('uid', $merchant['uid'])->update(['business' => 0]);
            return JsonService::successful('删除成功');
        } else
            return JsonService::fail(UserEnterModel::getErrorInfo('删除失败'));
    }

    /**编辑讲师信息
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $role = MerchantModel::get($id);
        $this->assign(['title' => '编辑讲师后台', 'roles' => $role->toJson(), 'menus' => json(MerchantMenus::ruleList())->getContent(), 'action' => Url::build('update', array('id' => $id))]);
        return $this->fetch('edit');
    }

    /**
     * @param Request $request
     * @param $id
     * @return \think\response\Json
     */
    public function update(Request $request, $id)
    {
        $data = parent::postMore([
            'mer_name',
            'real_name',
            'pwd',
            'mer_phone',
            'mer_address',
            'mer_special_divide',
            'mer_store_divide',
            'mer_event_divide',
            'mer_data_divide',
            'mer_test_divide',
            'gold_divide',
            'mark',
            'is_source',
            'is_audit',
            'status',
            ['checked_menus', [], '', 'rules'],
            'uid'
        ], $request);
        if (!$id) return JsonService::fail('数据错误');
        if (!is_array($data['rules']) || !count($data['rules'])) return JsonService::fail('请选择最少一个权限');
        foreach ($data['rules'] as &$v) {
            $pid = MerchantMenus::where('id', $v)->value('pid');
            if (!in_array($pid, $data['rules'])) $data['rules'][] = $pid;
        }
        $status = $data['status'];
        $data['rules'] = implode(',', $data['rules']);
        $merchant = MerchantModel::get($id);
        if (!$merchant) return JsonService::fail('讲师后台不存在');
        if (!$data['mer_name']) return JsonService::fail('请输入讲师后台名称');
        MerchantModel::beginTrans();
        $res1 = MerchantModel::edit($data, $id);
        $update = array();
        $update['rules'] = $data['rules'];
        $rules = MerchantAdminModel::where('level', 0)->where('mer_id', $id)->value('rules');
        if ($update['rules'] == $rules) $res2 = true;
        else $res2 = false !== MerchantAdminModel::where('level', 0)->where('mer_id', $id)->update($update);
        $res = false;
        if ($res1 && $res2) $res = true;
        MerchantModel::checkTrans($res);
        if ($res) {
            $dat['lecturer_name'] = $data['mer_name'];
            if (!$status) {
                Special::where(['is_del' => 0, 'mer_id' => $id])->update(['is_show' => 0]);
                DownloadModel::where(['is_del' => 0, 'mer_id' => $id])->update(['is_show' => 0]);
                ProductModel::where(['is_del' => 0, 'mer_id' => $id])->update(['is_show' => 0]);
                EventRegistrationModel::where(['is_del' => 0, 'mer_id' => $id])->update(['is_show' => 0]);
                TestPaper::where(['is_del' => 0, 'mer_id' => $id])->update(['is_show' => 0]);
                Lecturer::where(['id' => $merchant['lecturer_id']])->update(['is_show' => 0]);
                $dat['is_show'] = 0;
                Lecturer::edit($dat, $merchant['lecturer_id'], 'id');
            } else {
                $dat['is_show'] = 1;
                Lecturer::edit($dat, $merchant['lecturer_id'], 'id');
            }
            return JsonService::successful('修改成功!');
        } else
            return JsonService::fail('修改失败!');
    }

    /**
     * 修改状态
     * @param $id
     * @return \think\response\Json
     */
    public function modify($id, $status)
    {
        if (!$id) return JsonService::fail('数据错误');
        $merchantInfo = MerchantModel::where('id', $id)->where('is_del', 0)->find();
        if (!$merchantInfo) return JsonService::fail('数据错误');
        $data['status'] = $status;
        if (!MerchantModel::edit($data, $id)) {
            return JsonService::fail(MerchantModel::getErrorInfo('修改失败,请稍候再试!'));
        } else {
            $dat['is_show'] = $status;
            Lecturer::edit($dat, $merchantInfo['lecturer_id'], 'id');
            return JsonService::successful('修改成功!');
        }
    }

    /**登录
     * @param $id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login($id)
    {
        $merchantInfo = MerchantModel::where('id', $id)->where('is_del', 0)->find();
        if (!$merchantInfo) return $this->failed('登陆的讲师后台不存在!');
        $adminInfo = MerchantAdminModel::where('level', 0)->where('mer_id', $merchantInfo->id)->find();
        if (!$adminInfo) return $this->failed('登陆的讲师后台不存在!');
        MerchantAdminModel::setLoginInfo($adminInfo->toArray());
        MerchantAdminModel::setMerchantInfo($merchantInfo->toArray());
        return $this->redirect(Url::build('/merchant/index/index'));
    }

    /**重置密码
     * @param $id
     */
    public function reset_pwd($id)
    {
        if (!$id) return JsonService::fail('参数错误失败!');
        $pwd = md5(1234567);
        $adminPwd = MerchantAdminModel::where('mer_id', $id)->where('level', 0)->value('pwd');
        if ($pwd == $adminPwd) return JsonService::fail('您的密码无需重置!');
        if (MerchantAdminModel::where('mer_id', $id)->where('level', 0)->update(['pwd' => md5(1234567)]))
            return JsonService::successful('重置成功!');
        else
            return JsonService::fail('重置失败!');
    }
}
