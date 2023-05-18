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

namespace app\merchant\model\user;


use app\merchant\model\merchant\Merchant;
use app\merchant\model\merchant\MerchantBill;
use app\merchant\model\user\User;
use think\Url;
use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;
use service\WechatTemplateService;
use service\WechatService;

/**
 * 用户提现管理 model
 * Class User
 * @package app\merchant\model\user
 */
class UserExtract extends ModelBasic
{
    use ModelTrait;
    //审核中
    const AUDIT_STATUS = 0;
    //未通过
    const FAIL_STATUS = -1;
    //已提现
    const SUCCESS_STATUS = 1;

    protected static $extractType = ['alipay', 'bank', 'weixin'];

    protected static $extractTypeMsg = ['alipay' => '支付宝', 'bank' => '银行卡', 'weixin' => '微信'];

    protected static $status = array(
        -1 => '未通过',
        0 => '审核中',
        1 => '已提现'
    );

    //商户提现
    public static function userExtract($mer_id, $data, $extract_price = 100)
    {
        $userInfo = Merchant::where('id', $mer_id)->find();
        if (!in_array($data['extract_type'], self::$extractType)) return self::setErrorInfo('提现方式不存在');
        if ($userInfo['now_money'] < $data['extract_price']) return self::setErrorInfo('余额不足');
        if ($data['extract_price'] < $extract_price) return self::setErrorInfo('提现金额不能小于' . $extract_price);
        $balance = bcsub($userInfo['now_money'], $data['extract_price'], 2);
        $insertData = [
            'uid' => $userInfo['uid'],
            'real_name' => $data['real_name'],
            'extract_type' => $data['extract_type'],
            'extract_price' => $data['extract_price'],
            'add_time' => time(),
            'balance' => $balance,
            'status' => self::AUDIT_STATUS,
            'mer_id' => $userInfo['id']
        ];
        if ($data['extract_type'] == 'weixin') {
            if (!$data['weixin']) return self::setErrorInfo('请输入微信账号');
            $insertData['wechat'] = $data['weixin'];
            $mark = '使用微信提现' . $insertData['extract_price'] . '元';
        } else if ($data['extract_type'] == 'alipay') {
            if (!$data['alipay_code']) return self::setErrorInfo('请输入支付宝账号');
            $insertData['alipay_code'] = $data['alipay_code'];
            $mark = '使用支付宝提现' . $insertData['extract_price'] . '元';
        } else {
            if (!$data['real_name']) return self::setErrorInfo('输入姓名有误');
            if (!$data['bank_code']) return self::setErrorInfo('请输入银行卡账号');
            if (!$data['bank_address']) return self::setErrorInfo('请输入开户地址');
            $insertData['bank_code'] = $data['bank_code'];
            $insertData['bank_address'] = $data['bank_address'];
            $mark = '使用银联卡' . $insertData['bank_code'] . '提现' . $insertData['extract_price'] . '元';
        }
        self::beginTrans();
        $res1 = self::set($insertData);
        if (!$res1) return self::setErrorInfo('提现失败');
        Merchant::where(['id' => $userInfo['id']])->update(['now_money' => $balance]);
        $res2 = MerchantBill::expend('余额提现', 0, $userInfo['id'], 'now_money', 'extract', $data['extract_price'], $balance, $mark);
        $res = $res1 && $res2;
        if ($res) {
            self::commitTrans();
            return true;
        } else {
            self::rollbackTrans();
            return false;
        }
    }

    /**条件处理
     * @param $where
     */
    public static function setWhere($where)
    {
        $model = new self();
        $model = $model->alias('a');
        if (isset($where['mer_id']) && $where['mer_id'] != 0) $model = $model->where('a.mer_id', $where['mer_id']);
        if (isset($where['status']) && $where['status'] != '') $model = $model->where('a.status', $where['status']);
        if (isset($where['extract_type']) && $where['extract_type'] != '') $model = $model->where('a.extract_type', $where['extract_type']);
        return $model;
    }

    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where)
    {
        $data = self::setWhere($where)->field('a.*')->order('a.id desc')->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

}
