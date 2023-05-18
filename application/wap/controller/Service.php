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

use app\wap\model\store\StoreService;
use service\SystemConfigService;
use app\wap\model\user\User;
use service\JsonService;
use service\UtilService;
use think\Request;

/**客服控制器
 * Class Service
 * @package app\wap\controller
 */
class Service extends AuthController
{
    /**微信客服列表
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function service_list()
    {
        $customer_service_configuration = SystemConfigService::get('customer_service_configuration');
        $service_url = SystemConfigService::get('service_url');
        $kefu_token = SystemConfigService::get('kefu_token');
        $this->assign([
            'service_configuration' => $customer_service_configuration,
            'service_url' => $service_url,
            'kefu_token' => $kefu_token,
            'userInfo' => json_encode($this->userInfo)
        ]);
        return $this->fetch();
    }

    /**
     * 获取微信客服
     */
    public function get_service_list()
    {
        $where = UtilService::getMore([
            ['mer_id', ''],
            ['page', 1],
            ['limit', 10]
        ]);
        $list = StoreService::get_weixin_service_list($where);
        return JsonService::successful($list);
    }

    /**聊天
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function service_ing(Request $request)
    {
        $params = Request::instance()->param();
        $to_uid = $params['to_uid'];
        if (!isset($to_uid) || empty($to_uid)) $this->failed('未获取到接收用户信息！');
        if ($this->uid == $to_uid) $this->failed('您不能进行自言自语！');

        //发送用户信息
        $now_user = StoreService::where(['uid' => $this->uid])->find();
        if (!$now_user) $now_user = $this->userInfo;
        $this->assign('user', $now_user);

        //接收用户信息
        $to_user = StoreService::where(['uid' => $to_uid])->find();
        if (!$to_user) $to_user = User::getUserData($to_uid);
        if (!$to_user) $this->failed('未获取到接收用户信息！');
        $this->assign(['to_user' => $to_user]);
        return $this->fetch();
    }

    /**聊天记录
     * @return mixed
     */
    public function service_new()
    {
        return $this->fetch();
    }
}
