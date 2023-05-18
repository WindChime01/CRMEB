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
use service\JsonService as Json;
use app\merchant\model\merchant\MerchantFlowingWater;
use app\merchant\model\merchant\Merchant;
use app\merchant\model\merchant\MerchantBill;
use service\SystemConfigService;
use service\FormBuilder as Form;
use service\HookService;
use think\Url;
use app\merchant\model\user\User;
use app\merchant\model\user\UserExtract;

/**
 * 微信充值记录
 * Class Finance
 */
class Finance extends AuthController
{

    /**讲师流水
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function merbill()
    {
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
            ['limit', 20],
            ['page', 1],
            ['category', 'now_money'],
        ]);
        $where['mer_id'] = $this->merchantId;
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
            ['category', 'now_money'],
        ]);
        $where['mer_id'] = $this->merchantId;
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
        ]);
        $where['mer_id'] = $this->merchantId;
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
        ]);
        $where['mer_id'] = $this->merchantId;
        MerchantFlowingWater::SaveExport($where);
    }
}

