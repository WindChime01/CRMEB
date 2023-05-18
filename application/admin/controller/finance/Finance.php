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
use app\admin\model\user\UserBill;
use service\JsonService as Json;
use app\admin\model\finance\FinanceModel;
use app\admin\model\merchant\MerchantFlowingWater;
use app\admin\model\merchant\MerchantBill;
use app\admin\model\merchant\Merchant;
use service\SystemConfigService;
use service\FormBuilder as Form;
use service\HookService;
use think\Url;
use app\admin\model\user\User;
use app\admin\model\user\UserExtract;

/**
 * 微信充值记录
 * Class Finance
 */
class Finance extends AuthController
{

    /**
     * 显示资金记录
     */
    public function bill()
    {
        $category = $this->request->param('category', 'now_money');
        $bill_where_op = FinanceModel::bill_where_op($category);
        $list = UserBill::where('type', $bill_where_op['type']['op'], $bill_where_op['type']['condition'])
            ->where('category', $bill_where_op['category']['op'], $bill_where_op['category']['condition'])
            ->field(['title', 'type'])
            ->group('type')
            ->distinct(true)
            ->select()
            ->toArray();
        $this->assign([
            'selectList' => $list,
            'category' => $category,
            'gold_name' => $category == "gold_num" ? SystemConfigService::get("gold_name") : '金额'
        ]);
        return $this->fetch();
    }

    /**
     * 显示资金记录ajax列表
     */
    public function billlist()
    {
        $where = parent::getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['nickname', ''],
            ['limit', 20],
            ['page', 1],
            ['type', ''],
            ['category', 'now_money']
        ]);
        return Json::successlayui(FinanceModel::getBillList($where));
    }

    /**
     *保存资金监控的excel表格
     */
    public function save_bell_export()
    {
        $where = parent::getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['nickname', ''],
            ['type', ''],
            ['category', 'now_money'],
        ]);
        FinanceModel::SaveExport($where);
    }

    /**
     * 显示佣金记录
     */
    public function commission_list()
    {
        $this->assign('is_layui', true);
        return $this->fetch();
    }

    /**
     * 佣金记录异步获取
     */
    public function get_commission_list()
    {
        $get = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['nickname', ''],
            ['price_max', ''],
            ['price_min', ''],
            ['order', '']
        ]);
        return Json::successlayui(User::getCommissionList($get));
    }


    /**
     * 佣金详情
     */
    public function content_info($uid = '')
    {
        if ($uid == '') return $this->failed('缺少参数');
        $this->assign('userinfo', User::getUserinfo($uid));
        $this->assign('uid', $uid);
        return $this->fetch();
    }

    /**
     * 佣金提现记录个人列表
     */
    public function get_extract_list($uid = '')
    {
        if ($uid == '') return Json::fail('缺少参数');
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['start_time', ''],
            ['end_time', ''],
            ['nickname', '']
        ]);
        return Json::successlayui(UserBill::getExtrctOneList($where, $uid));
    }

    /**讲师流水
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function merbill()
    {
        $list = Merchant::getMerWhere()->select();
        $this->assign(['selectList' => $list]);
        return $this->fetch();
    }

    /**
     * 显示资金记录ajax列表
     */
    public function merbilllist()
    {
        $where = parent::getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['category', 'now_money'],
            ['limit', 20],
            ['page', 1],
            ['mer_id', 0],
            ['excel', 0],
        ]);
        return Json::successlayui(MerchantBill::getBillList($where));
    }

    /**
     *保存资金监控的excel表格
     */
    public function save_mer_bell_export()
    {
        $where = parent::getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['mer_id', 0],
            ['category', 'now_money'],
        ]);
        MerchantBill::SaveExport($where);
    }

    /**金币流水
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function mer_gold_bill()
    {
        $list = Merchant::getMerWhere()->select();
        $this->assign(['selectList' => $list]);
        return $this->fetch('mer_gold_bill');
    }

    /**订单分成
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function merOrderBill()
    {
        $list = Merchant::getMerWhere()->select();
        $this->assign(['selectList' => $list]);
        return $this->fetch('mer_order_bill');
    }

    /**
     * 订单分成列表
     */
    public function merOrderBillList()
    {
        $where = parent::getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['nickname', ''],
            ['limit', 20],
            ['page', 1],
            ['mer_id', 0],
        ]);
        return Json::successlayui(MerchantFlowingWater::getBillList($where));
    }

    /**
     *保存资金监控的excel表格
     */
    public function save_mer_order_bell_export()
    {
        $where = parent::getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['nickname', ''],
            ['mer_id', 0],
        ]);
        MerchantFlowingWater::SaveExport($where);
    }
}

