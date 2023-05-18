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
use app\admin\model\user\UserRecharge as UserRechargeModel;
use app\admin\model\user\UserBill;
use behavior\wap\StoreProductBehavior;
use service\JsonService as Json;
use think\Url;
use service\FormBuilder as Form;
use think\Request;
use service\HookService;
use behavior\wechat\PaymentBehavior;
use service\WechatTemplateService;
use service\SystemConfigService;
use service\AlipayTradeWapService;
use app\admin\model\user\User;

/**
 * 微信充值记录
 * Class UserRecharge
 */
class UserRecharge extends AuthController
{
    /**
     * 显示操作记录
     */
    public function index()
    {
        $this->assign([
            'year' => getMonth('y'),
            'real_name' => $this->request->get('real_name', ''),
            'orderCount' => UserRechargeModel::orderCount()
        ]);
        return $this->fetch();
    }

    /**
     * 获取头部订单金额等信息
     * return json
     *
     */
    public function getBadge()
    {
        $where = parent::postMore([
            ['status', ''],
            ['real_name', ''],
            ['data', ''],
        ]);
        return Json::successful(UserRechargeModel::getBadge($where));
    }

    public function get_user_recharge_list()
    {
        $where = parent::getMore([
            ['status', ''],
            ['real_name', $this->request->param('real_name', '')],
            ['data', ''],
            ['order', ''],
            ['page', 1],
            ['limit', 20],
            ['excel', 0]
        ]);
        return Json::successlayui(UserRechargeModel::systemPage($where));
    }

    /**订单删除
     * @param int $id
     */
    public function delete($id = 0)
    {
        if (!$id) return Json::fail('参数错误!');
        $data['is_del'] = 1;
        UserRechargeModel::edit($data, $id);
        return Json::successful('删除成功!');
    }

    /**订单详情
     * @param string $oid
     * @return mixed|void
     * @throws \think\exception\DbException
     */
    public function order_info($oid = '')
    {
        if (!$oid || !($orderInfo = UserRechargeModel::get($oid)))
            return $this->failed('订单不存在!');
        $userInfo = User::getAllUserinfo($orderInfo['uid']);
        $this->assign(compact('orderInfo', 'userInfo'));
        return $this->fetch();
    }

    /**退款
     * @param $id
     * @return mixed|void
     */
    public function edit($id)
    {
        if (!$id) return $this->failed('数据不存在');
        $UserRecharge = UserRechargeModel::get($id);
        if (!$UserRecharge) return Json::fail('数据不存在!');
        if ($UserRecharge['paid'] == 1) {
            $f = array();
            $f[] = Form::input('order_id', '退款单号', $UserRecharge->getData('order_id'))->disabled(1);
            $f[] = Form::number('refund_price', '退款金额', $UserRecharge->getData('price'))->precision(2)->min(0)->max($UserRecharge->getData('price'));
            $form = Form::make_post_form('编辑', $f, Url::build('updateRefundY', array('id' => $id)), 4);
            $this->assign(compact('form'));
            return $this->fetch('public/form-builder');
        } else return Json::fail('数据不存在!');
    }

    /**退款更新
     * @param Request $request
     * @param $id
     */
    public function updateRefundY(Request $request, $id)
    {
        $data = parent::postMore([
            'refund_price',
        ], $request);
        if (!$id) return $this->failed('数据不存在');
        $UserRecharge = UserRechargeModel::get($id);
        if (!$UserRecharge) return Json::fail('数据不存在!');
        if ($UserRecharge['price'] == $UserRecharge['refund_price']) return Json::fail('已退完支付金额!不能再退款了');
        if (!$data['refund_price']) return Json::fail('请输入退款金额');
        $refund_price = $data['refund_price'];
        $data['refund_price'] = bcadd($data['refund_price'], $UserRecharge['refund_price'], 2);
        $bj = bccomp((float)$UserRecharge['price'], (float)$data['refund_price'], 2);
        if ($bj < 0) return Json::fail('退款金额大于支付金额，请修改退款金额');
        $refund_data['pay_price'] = $UserRecharge['price'];
        $refund_data['refund_price'] = $refund_price;
        $data['refund_status'] = 2;
        if ($UserRecharge['recharge_type'] == 'weixin') {
            try {
                HookService::listen('user_recharge_refund', $UserRecharge['order_id'], $refund_data, true, PaymentBehavior::class);
            } catch (\Exception $e) {
                return Json::fail($e->getMessage());
            }
        } else if ($UserRecharge['recharge_type'] == 'yue') {
            UserRechargeModel::beginTrans();
            $res = User::bcInc($UserRecharge['uid'], 'now_money', $refund_price, 'uid');
            UserRechargeModel::checkTrans($res);
            if (!$res) return Json::fail('余额退款失败!');
        } else if ($UserRecharge['recharge_type'] == 'zhifubao') {
            $res = AlipayTradeWapService::init()->AliPayRefund($UserRecharge['order_id'], $UserRecharge['trade_no'], $refund_price, '虚拟币充值退款', 'refund');
            if (empty($res) || $res != 10000) {
                return Json::fail('支付宝退款失败!');
            }
        }
        $data['refund_reason_time'] = time();
        $resEdit = UserRechargeModel::edit($data, $id);
        if ($resEdit) {
            $goldNum = money_rate_num($refund_price, 'gold');
            $gold_name = SystemConfigService::get('gold_name');//虚拟币名称
            User::bcDec($UserRecharge['uid'], 'gold_num', $goldNum, 'uid');
            UserBill::expend($gold_name . '充值退款', $UserRecharge['uid'], 'gold_num', 'return', $goldNum, $UserRecharge['id'], 0, '退' . floatval($goldNum) . $gold_name);
            $recharge_type = $UserRecharge['recharge_type'] == 'yue' ? 'now_money' : $UserRecharge['recharge_type'];
            UserBill::income('虚拟币充值退款', $UserRecharge['uid'], $recharge_type, 'user_recharge_refund', $refund_price, $UserRecharge['id'], 0, '订单退款' . floatval($refund_price) . '元');
            return Json::successful('修改成功!');
        } else {
            return Json::successful('修改失败!');
        }
    }
}
