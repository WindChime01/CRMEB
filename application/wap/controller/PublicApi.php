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

namespace app\wap\controller;

use service\JsonService;
use service\SystemConfigService;
use app\wap\model\merchant\Merchant;
use app\wap\model\store\StoreService;

class PublicApi
{

    public function wechat_media_id_by_image($mediaIds = '')
    {
        if (!$mediaIds) return JsonService::fail('参数错误');
        try {
            $mediaIds = explode(',', $mediaIds);
            $temporary = \service\WechatService::materialTemporaryService();
            $pathList = [];
            foreach ($mediaIds as $mediaId) {
                if (!$mediaId) continue;
                try {
                    $content = $temporary->getStream($mediaId);
                } catch (\Exception $e) {
                    continue;
                }
                $name = substr(md5($mediaId), 12, 20) . '.jpg';
                $res = \Api\AliyunOss::instance([
                    'AccessKey' => SystemConfigService::get('accessKeyId'),
                    'AccessKeySecret' => SystemConfigService::get('accessKeySecret'),
                    'OssEndpoint' => SystemConfigService::get('end_point'),
                    'OssBucket' => SystemConfigService::get('OssBucket'),
                    'uploadUrl' => SystemConfigService::get('uploadUrl'),
                ])->stream($content, $name);
                if ($res !== false) {
                    $pathList[] = $res['url'];
                }
            }
            return JsonService::successful($pathList);
        } catch (\Exception $e) {
            return JsonService::fail('上传失败', ['msg' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
        }
    }

    /**网站统计
     * @return bool|mixed
     */
    public function get_website_statistics()
    {
        return SystemConfigService::get('website_statistics');
    }

    /**
     * 公用数据
     */
    public function public_data()
    {
        $customer_service = SystemConfigService::get('customer_service_configuration');//客服配置1=微信客服2=CRMchat客服3=拨打电话
        $data['customer_service'] = $customer_service;//客服配置1=微信客服2=CRMchat客服3=拨打电话
        $data['site_service_phone'] = SystemConfigService::get('site_service_phone');//客服电话
        if ($customer_service == 2) {
            $data['service_url'] = SystemConfigService::get('service_url');
            $data['kefu_token'] = SystemConfigService::get('kefu_token');
        }
        return JsonService::successful($data);
    }

    /**获取客服id
     * @param $mer_id
     * @return void
     */
    public function get_kefu_id($mer_id = 0)
    {
        $data['kefu_id'] = StoreService::get_crmeb_random_service_kefu_id($mer_id);
        return JsonService::successful($data);
    }

    /**讲师客服检查
     * @param $mer_id
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_site_service_phone($mer_id = 0)
    {
        $data = [];
        if (!$mer_id) return JsonService::successful($data);
        $merchant = Merchant::where(['id' => $mer_id, 'status' => 1, 'is_del' => 0, 'estate' => 1])->field('id,is_phone_service,service_phone')->find();
        if ($merchant && $merchant['is_phone_service']) {
            $data['customer_service'] = 3;
            $data['site_service_phone'] = $merchant['service_phone'];
        }
        return JsonService::successful($data);
    }
}
