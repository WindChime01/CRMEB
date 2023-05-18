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

namespace app\merchant\controller;

use service\JsonService;
use service\SystemConfigService;
use app\merchant\model\merchant\Merchant;
use app\merchant\model\special\Lecturer;
use service\GroupDataService;
use app\merchant\model\special\Special;
use app\merchant\model\download\DataDownload as DownloadModel;
use app\merchant\model\store\StoreProduct as ProductModel;
use app\merchant\model\ump\EventRegistration as EventRegistrationModel;

class System extends AuthController
{

    public function index()
    {
        $merchant = Merchant::where('id', $this->merchantId)->find();
        $lecturer = Lecturer::where('id', $this->merchantInfo['lecturer_id'])->field('label,explain,introduction')->find();
        $lecturer['introduction'] = htmlspecialchars_decode($lecturer['introduction']);
        $this->assign(['merchat' => json_encode($merchant), 'lecturer' => json_encode($lecturer)]);
        return $this->fetch();
    }

    /**
     * 保存商户资料
     */
    public function edit_merchant()
    {
        $post = parent::postMore([
            ['mer_name', ''],
            ['mer_email', ''],
            ['mer_phone', ''],
            ['mer_address', ''],
            ['mer_avatar', ''],
            ['mer_info', ''],
            ['estate', 0],
            ['card_id', ''],
            ['bank', ''],
            ['bank_number', ''],
            ['bank_name', ''],
            ['bank_address', ''],
            ['label', []],
            ['explain', '']
        ]);
        $data['label'] = json_encode($post['label']);
        $data['explain'] = $post['explain'];
        $data['introduction'] = htmlspecialchars($post['mer_info']);
        $data['lecturer_name'] = $post['mer_name'];
        $data['lecturer_head'] = $post['mer_avatar'];
        $data['phone'] = $post['mer_phone'];
        Merchant::beginTrans();
        try {
            unset($post['label'], $post['explain']);
            Merchant::edit($post, $this->merchantId, 'id');
            if (!$post['estate']) {
                Special::where(['is_del' => 0, 'mer_id' => $this->merchantId])->update(['is_show' => 0]);
                DownloadModel::where(['is_del' => 0, 'mer_id' => $this->merchantId])->update(['is_show' => 0]);
                ProductModel::where(['is_del' => 0, 'mer_id' => $this->merchantId])->update(['is_show' => 0]);
                EventRegistrationModel::where(['is_del' => 0, 'mer_id' => $this->merchantId])->update(['is_show' => 0]);
                $data['is_show'] = 0;
                Lecturer::edit($data, $this->merchantInfo['lecturer_id'], 'id');
            } else {
                $data['is_show'] = 1;
                Lecturer::edit($data, $this->merchantInfo['lecturer_id'], 'id');
            }
            Merchant::commitTrans();
            return JsonService::successful('保存成功');
        } catch (\Exception $e) {
            Merchant::rollbackTrans();
            return JsonService::fail($e->getMessage());
        }
    }
}
