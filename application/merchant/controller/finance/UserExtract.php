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


namespace app\merchant\controller\finance;

use app\merchant\controller\AuthController;
use service\FormBuilder as Form;
use app\merchant\model\user\UserExtract as UserExtractModel;
use app\merchant\model\merchant\Merchant;
use app\merchant\model\merchant\MerchantBill;
use service\JsonService;
use service\SystemConfigService;
use think\Request;
use think\Url;

/**
 * 用户提现管理
 * Class UserExtract
 */
class UserExtract extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    public function get_mer_user_extract()
    {
        $where = parent::getMore([
            ['status', ''],
            ['extract_type', ''],
        ], $this->request);
        $where['mer_id'] = $this->merchantId;
        return JsonService::successlayui(UserExtractModel::systemPage($where));
    }

    /**编辑提现
     * @param $id
     * @return mixed|void
     * @throws \think\exception\DbException
     */
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

    public function forward_list()
    {
        $where = parent::getMore([
            ['status', 'all'],
            ['uid', $this->merchantInfo['uid']],
            ['mer_id', $this->merchantId]
        ]);
        $where['act'] = $this->request->param('act', 'my');
        $adminInfo = $this->adminInfo;
        if ($adminInfo['uid'] == $this->merchantInfo['uid']) {
            $is_son_admin = 2;
        } else {
            $is_son_admin = 1;
        }
        $this->assign([
            'is_son_admin' => $is_son_admin,
            'gold_name' => SystemConfigService::get("gold_name"),
            'gold_coin_ratio' => SystemConfigService::get("gold_coin_ratio"),
            'money' => Merchant::where('id', $this->merchantId)->field('now_money,gold_num')->find(),
            'where' => $where
        ]);
        return $this->fetch();
    }

    public function extract()
    {
        $this->assign('merchat', json_encode($this->merchantInfo));
        return $this->fetch();
    }

    /**
     * 提现申请
     */
    public function save_extract()
    {
        $extractInfo = parent::postMore([
            ['extract_type', ''],
            ['extract_price', 0],
            ['real_name', ''],
            ['bank_code', ''],
            ['bank_address', ''],
            ['weixin', ''],
            ['alipay_code', '']
        ]);
        $res = UserExtractModel::userExtract($this->merchantId, $extractInfo, SystemConfigService::get('extract_price'));
        if ($res)
            return JsonService::successful('申请提现成功!');
        else
            return JsonService::fail(UserExtractModel::getErrorInfo());
    }

    public function save_gold()
    {
        $where = parent::getMore([
            ['gold', 0]
        ]);
        $merchant = Merchant::where('id', $this->merchantId)->find();
        if ($merchant['gold_num'] < $where['gold']) return JsonService::fail('余额不足');
        $gold_name = SystemConfigService::get("gold_name");
        $gold_coin_ratio = SystemConfigService::get("gold_coin_ratio");
        $gold_coin_ratio = bcdiv($gold_coin_ratio, 100, 2);
        $price = bcmul($gold_coin_ratio, $where['gold'], 2);
        Merchant::beginTrans();
        $mark = '提现' . $where['gold'] . '个' . $gold_name . '成余额';
        $mark1 = '提现' . $where['gold'] . '个' . $gold_name . '获得' . $price . '余额';
        $res1 = MerchantBill::expend($gold_name . '提现', 0, $merchant['id'], 'gold_num', 'gold_turn_balance', $where['gold'], bcsub($merchant['gold_num'], $where['gold'], 0), $mark);
        $res2 = MerchantBill::income($gold_name . '提现', 0, $merchant['id'], 'now_money', 'gold_extract', $price, bcadd($merchant['now_money'], $price, 2), $mark1);
        $res3 = Merchant::bcDec($merchant['id'], 'gold_num', $where['gold'], 'id', true);
        $res4 = Merchant::bcInc($merchant['id'], 'now_money', $price, 'id');
        $res = $res1 && $res2 && $res3 && $res4;
        if ($res) {
            Merchant::commitTrans();
            return JsonService::successful('提现成功!');
        } else {
            Merchant::rollbackTrans();
            return JsonService::fail('提现失败');
        }
    }
}
