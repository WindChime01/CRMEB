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

use app\merchant\model\merchant\Merchant;
use app\merchant\model\merchant\MerchantAdmin;
use app\merchant\model\merchant\MerchantMenus;
use basic\AuthBasic;
use service\HookService;
use think\Url;

/**
 * 基类 所有控制器继承的类
 * Class AuthController
 * @package app\merchant\controller
 */
class AuthController extends AuthBasic
{
    /**
     * 当前登陆管理员信息
     * @var
     */
    protected $adminInfo;

    /**
     * 当前登陆管理员ID
     * @var
     */
    protected $adminId;

    /**
     * 是否需要审核
     * @var
     */
    protected $isAudit;

    /**
     * 讲师id
     * @var
     */
    protected $merchantId;

    /**
     * 讲师id
     * @var
     */
    protected $lecturerId;

    /**
     * 商户信息
     * @var
     */
    protected $merchantInfo;

    /**
     * 当前管理员权限
     * @var array
     */
    protected $auth = [];

    protected $skipLogController = ['index', 'common'];

    protected function _initialize()
    {
        parent::_initialize();
        if (!MerchantAdmin::hasActiveAdmin()) return $this->redirect('Login/index');
        try {
            $adminInfo = MerchantAdmin::activeAdminInfoOrFail();
            $merchantInfo = MerchantAdmin::activeMerchantInfoOrFail();
        } catch (\Exception $e) {
            return $this->failed(MerchantAdmin::getErrorInfo($e->getMessage()), Url::build('Login/index'));
        }
        $this->adminInfo = $adminInfo;
        $this->adminId = $adminInfo['id'];
        $this->merchantInfo = $merchantInfo;
        $this->merchantId = $merchantInfo['id'];
        $this->lecturerId = $merchantInfo['lecturer_id'];
        $this->isAudit = Merchant::where('id', $merchantInfo['id'])->value('is_audit');
        $this->getActiveAdminInfo();
        $this->auth = MerchantMenus::rulesByAuth($this->adminInfo['rules']);
        $this->checkAuth();
        $this->assign('_admin', $this->adminInfo);
        if ($merchantInfo['is_del'] == 1 || $merchantInfo['status'] == 0) {
            $this->failed('讲师删除或者已被禁止登陆', Url::build('Login/index'));
        }
    }

    protected function checkAuth($action = null, $controller = null, $module = null, array $route = [])
    {
        static $allAuth = null;
        if ($allAuth === null) $allAuth = MerchantMenus::getAllAuth();
        if ($module === null) $module = $this->request->module();
        if ($controller === null) $controller = $this->request->controller();
        if ($action === null) $action = $this->request->action();
        if (!count($route)) $route = $this->request->route();
        if (in_array(strtolower($controller), $this->skipLogController, true)) return true;
        $nowAuthName = MerchantMenus::getAuthName($action, $controller, $module, $route);
        $baseNowAuthName = MerchantMenus::getAuthName($action, $controller, $module, []);
        if ((in_array($nowAuthName, $allAuth) && !in_array($nowAuthName, $this->auth)) || (in_array($baseNowAuthName, $allAuth) && !in_array($baseNowAuthName, $this->auth)))
            exit($this->authFail('没有权限访问!'));
        return true;
    }


    /**
     * 获得当前用户最新信息
     * @return MerchantAdmin
     */
    protected function getActiveAdminInfo()
    {
        $adminId = $this->adminId;
        $adminInfo = MerchantAdmin::getValidAdminInfoOrFail($adminId);
        if (!$adminInfo) $this->failed(MerchantAdmin::getErrorInfo('请登陆!'));
        $this->adminInfo = $adminInfo;
        MerchantAdmin::setLoginInfo($adminInfo->toArray());
        return $adminInfo;
    }
}
