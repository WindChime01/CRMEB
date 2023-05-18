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


namespace app\admin\controller\finance;

use app\admin\controller\AuthController;
use service\FormBuilder as Form;
use app\admin\model\user\UserExtract as UserExtractModel;
use service\JsonService;
use think\Request;
use think\Url;

/**
 * 用户提现管理
 * Class UserExtract
 */
class UserExtract extends AuthController
{
    /**用户提现
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 用户提现记录
     */
    public function get_user_extract()
    {
        $where = parent::getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['status', ''],
            ['extract_type', ''],
            ['nireid', ''],
            ['page', 1],
            ['limit', 20],
        ], $this->request);
        return JsonService::successlayui(UserExtractModel::get_user_extract_list($where));
    }

    /**讲师提现
     * @return mixed
     */
    public function merIndex()
    {
        return $this->fetch();
    }

    public function get_mer_user_extract()
    {
        $where = parent::getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['mer_id', 1],
            ['status', ''],
            ['extract_type', ''],
            ['nireid', ''],
            ['page', 1],
            ['limit', 20],
        ], $this->request);
        return JsonService::successlayui(UserExtractModel::get_mer_user_extract_list($where));
    }

    public function edit($id)
    {
        if (!$id) return $this->failed('数据不存在');
        $UserExtract = UserExtractModel::get($id);
        if (!$UserExtract) return JsonService::fail('数据不存在!');
        $f = array();
        if ($UserExtract['extract_type'] == 'alipay') {
            $f[] = Form::input('real_name', '姓名', $UserExtract['real_name']);
            $f[] = Form::input('alipay_code', '支付宝账号', $UserExtract['alipay_code']);
        } else if ($UserExtract['extract_type'] == 'bank') {
            $f[] = Form::input('real_name', '姓名', $UserExtract['real_name']);
            $f[] = Form::input('bank_code', '银行卡号', $UserExtract['bank_code']);
            $f[] = Form::input('bank_address', '开户行', $UserExtract['bank_address']);
        } else if ($UserExtract['extract_type'] == 'weixin') {
            $f[] = Form::input('real_name', '姓名', $UserExtract['real_name']);
            $f[] = Form::input('wechat', '微信号', $UserExtract['wechat']);
        }
        $f[] = Form::number('extract_price', '提现金额', $UserExtract['extract_price'])->precision(2);
        $f[] = Form::input('mark', '备注', $UserExtract['mark'])->type('textarea');
        $form = Form::make_post_form('编辑', $f, Url::build('update', array('id' => $id)), 2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');

    }

    public function update(Request $request, $id)
    {
        $UserExtract = UserExtractModel::get($id);
        if (!$UserExtract) return JsonService::fail('数据不存在!');
        if ($UserExtract['extract_type'] == 'alipay') {
            $data = parent::postMore([
                'real_name',
                'mark',
                'extract_price',
                'alipay_code',
            ], $request);
            if (!$data['real_name']) return JsonService::fail('请输入姓名');
            if ($data['extract_price'] <= -1) return JsonService::fail('请输入提现金额');
            if (!$data['alipay_code']) return JsonService::fail('请输入支付宝账号');
        } else if ($UserExtract['extract_type'] == 'weixin') {
            $data = parent::postMore([
                'real_name',
                'mark',
                'extract_price',
                'wechat',
            ], $request);
            if (!$data['real_name']) return JsonService::fail('请输入姓名');
            if ($data['extract_price'] <= -1) return JsonService::fail('请输入提现金额');
            if (!$data['wechat']) return JsonService::fail('请输入wechat');
        } else if ($UserExtract['extract_type'] == 'bank') {
            $data = parent::postMore([
                'real_name',
                'extract_price',
                'mark',
                'bank_code',
                'bank_address',
            ], $request);
            if (!$data['real_name']) return JsonService::fail('请输入姓名');
            if ($data['extract_price'] <= -1) return JsonService::fail('请输入提现金额');
            if (!$data['bank_code']) return JsonService::fail('请输入银行卡号');
            if (!$data['bank_address']) return JsonService::fail('请输入开户行');
        } else if ($UserExtract['extract_type'] == 'yue') {
            $data = parent::postMore([
                'extract_price',
                'mark',
            ], $request);
            if ($data['extract_price'] <= -1) return JsonService::fail('请输入提现金额');
        }
        if (!UserExtractModel::edit($data, $id))
            return JsonService::fail(UserExtractModel::getErrorInfo('修改失败'));
        else
            return JsonService::successful('修改成功!');
    }

    /**提现回退
     * @param Request $request
     * @param $id
     * @throws \think\exception\DbException
     */
    public function fail($id)
    {
        $fail_msg = parent::postMore([
            ['message', ''],
        ]);
        if (!UserExtractModel::be(['id' => $id, 'status' => 0])) return JsonService::fail('操作记录不存在或状态错误!');
        $extract = UserExtractModel::get($id);
        if (!$extract) return JsonService::fail('操作记录不存!');
        if ($extract->status == 1) return JsonService::fail('已经提现,错误操作');
        if ($extract->status == -1) return JsonService::fail('您的提现申请已被拒绝,请勿重复操作!');
        UserExtractModel::beginTrans();
        $res = UserExtractModel::changeFail($id, $fail_msg['message'], $extract);
        if ($res) {
            UserExtractModel::commitTrans();
            return JsonService::successful('操作成功!');
        } else {
            UserExtractModel::rollbackTrans();
            return JsonService::fail('操作失败!');
        }
    }

    /**提现通过
     * @param $id
     * @throws \think\exception\DbException
     */
    public function succ($id)
    {
        if (!UserExtractModel::be(['id' => $id, 'status' => 0])) return JsonService::fail('操作记录不存在或状态错误!');
        $extract = UserExtractModel::get($id);
        if (!$extract) return JsonService::fail('操作记录不存!');
        if ($extract->status == 1) return JsonService::fail('您已提现,请勿重复提现!');
        if ($extract->status == -1) return JsonService::fail('您的提现申请已被拒绝!');
        UserExtractModel::beginTrans();
        $res = UserExtractModel::changeSuccess($id, $extract);
        if ($res) {
            UserExtractModel::commitTrans();
            return JsonService::successful('操作成功!');
        } else {
            UserExtractModel::rollbackTrans();
            return JsonService::fail('操作失败!');
        }
    }

    /**提现回退
     * @param Request $request
     * @param $id
     * @throws \think\exception\DbException
     */
    public function merFail($id)
    {
        $fail_msg = parent::postMore([
            ['message', ''],
        ]);
        if (!UserExtractModel::be(['id' => $id, 'status' => 0])) return JsonService::fail('操作记录不存在或状态错误!');
        $extract = UserExtractModel::get($id);
        if (!$extract) return JsonService::fail('操作记录不存!');
        if ($extract->status == 1) return JsonService::fail('已经提现,错误操作');
        if ($extract->status == -1) return JsonService::fail('您的提现申请已被拒绝,请勿重复操作!');
        UserExtractModel::beginTrans();
        $res = UserExtractModel::changeMerFail($id, $fail_msg['message'], $extract);
        if ($res) {
            UserExtractModel::commitTrans();
            return JsonService::successful('操作成功!');
        } else {
            UserExtractModel::rollbackTrans();
            return JsonService::fail('操作失败!');
        }
    }

    /**提现通过
     * @param $id
     * @throws \think\exception\DbException
     */
    public function merSucc($id)
    {
        if (!UserExtractModel::be(['id' => $id, 'status' => 0])) return JsonService::fail('操作记录不存在或状态错误!');
        $extract = UserExtractModel::get($id);
        if (!$extract) return JsonService::fail('操作记录不存!');
        if ($extract->status == 1) return JsonService::fail('您已提现,请勿重复提现!');
        if ($extract->status == -1) return JsonService::fail('您的提现申请已被拒绝!');
        UserExtractModel::beginTrans();
        $res = UserExtractModel::changeMerSuccess($id, $extract);
        if ($res) {
            UserExtractModel::commitTrans();
            return JsonService::successful('操作成功!');
        } else {
            UserExtractModel::rollbackTrans();
            return JsonService::fail('操作失败!');
        }
    }

    /**提现备注
     * @param $id
     * @throws \think\exception\DbException
     */
    public function remarks($id)
    {
        $fail_msg = parent::postMore([
            ['message', ''],
        ]);
        $extract = UserExtractModel::get($id);
        if (!$extract) return JsonService::fail('操作记录不存!');
        $res = UserExtractModel::where('id', $id)->update(['mark' => $fail_msg['message']]);
        if ($res) {
            return JsonService::successful('操作成功!');
        } else {
            return JsonService::fail('操作失败!');
        }
    }
}
