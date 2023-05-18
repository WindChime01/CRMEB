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

namespace app\merchant\model\merchant;


use app\merchant\model\merchant\Merchant as MerchantModel;
use behavior\system\SystemBehavior;
use think\Request;
use traits\ModelTrait;
use basic\ModelBasic;
use service\HookService;
use think\Session;

/**
 * Class MerchantAdmin
 * @package app\merchant\model\merchant
 */
class MerchantAdmin extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    public static function setAddTimeAttr($value)
    {
        return time();
    }

    public static function setRolesAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }


    /**
     * 用户登陆
     * @param string $account 账号
     * @param string $pwd 密码
     * @param string $verify 验证码
     * @return bool 登陆成功失败
     */
    public static function login($account, $pwd)
    {
        $adminInfo = self::where(['account' => $account, 'status' => 1, 'is_del' => 0])->find();
        if (!$adminInfo) {
            $adminList = self::where(['status' => 1, 'is_del' => 0])->select();
            foreach ($adminList as $k => $v) {
                $arr = explode('@', $v['account']);
                if (count($arr) <= 1) continue;
                if ($account != $arr[1]) continue;
                $adminInfo = self::where('id', $v['id'])->find();
                break;
            }
        }
        if (!$adminInfo) return self::setErrorInfo('账号不存在！');
        if ($adminInfo['pwd'] != $pwd) return self::setErrorInfo('账号或密码错误，请重新输入');
        if (!$adminInfo['status']) return self::setErrorInfo('该账号已被关闭!');
        $merchantInfo = MerchantModel::get($adminInfo['mer_id']);
        if (!$merchantInfo) return self::setErrorInfo('该商户不存在!');
        if (!$merchantInfo['status']) return self::setErrorInfo('该商户已被关闭!');
        if ($merchantInfo['is_del']) return self::setErrorInfo('该商户已被删除!');
        self::setLoginInfo($adminInfo->toArray());
        self::setMerchantInfo($merchantInfo->toArray());
        $adminInfo['last_ip'] = Request::instance()->ip();
        $adminInfo['last_time'] = time();
        $adminInfo['login_count'] += 1;
        $adminInfo->save();
        HookService::afterListen('merchant_admin_login', $adminInfo, $merchantInfo, false, SystemBehavior::class);
        return true;
    }

    /**
     * 用户登陆
     * @param string $account 账号
     * @param string $pwd 密码
     * @param string $verify 验证码
     * @return bool 登陆成功失败
     */
    public static function adminMerLogin($account)
    {
        $adminInfo = self::where(['account' => $account, 'status' => 1, 'is_del' => 0])->find();
        if (!$adminInfo) {
            $adminList = self::where(['status' => 1, 'is_del' => 0])->select();
            foreach ($adminList as $k => $v) {
                $arr = explode('@', $v['account']);
                if (count($arr) <= 1) continue;
                if ($account != $arr[1]) continue;
                $adminInfo = self::where('id', $v['id'])->find();
                break;
            }
        }
        if (!$adminInfo) return self::setErrorInfo('账号不存在！');
        if (!$adminInfo['status']) return self::setErrorInfo('该账号已被关闭!');
        $merchantInfo = MerchantModel::get($adminInfo['mer_id']);
        if (!$merchantInfo) return self::setErrorInfo('该商户不存在!');
        if (!$merchantInfo['status']) return self::setErrorInfo('该商户已被关闭!');
        if ($merchantInfo['is_del']) return self::setErrorInfo('该商户已被删除!');
        self::setLoginInfo($adminInfo->toArray());
        self::setMerchantInfo($merchantInfo->toArray());
        $adminInfo['last_ip'] = Request::instance()->ip();
        $adminInfo['last_time'] = time();
        $adminInfo['login_count'] += 1;
        $adminInfo->save();
        HookService::afterListen('merchant_admin_login', $adminInfo, $merchantInfo, false, SystemBehavior::class);
        return true;
    }

    /**
     *  保存当前登陆用户信息
     */
    public static function setLoginInfo($adminInfo)
    {
        Session::set('merAdminId', $adminInfo['id'], 'merchant');
        Session::set('merAdminInfo', $adminInfo, 'merchant');
    }

    /**
     * 保存当前登陆用户信息商户
     * @param $merchantInfo
     */
    public static function setMerchantInfo($merchantInfo)
    {
        Session::set('merchantInfo', $merchantInfo, 'merchant');
        Session::set('merchantId', $merchantInfo['id'], 'merchant');
    }

    /**
     * 清空当前登陆用户信息
     */
    public static function clearLoginInfo()
    {
        Session::delete('merAdminInfo', 'merchant');
        Session::delete('merAdminId', 'merchant');
        Session::delete('merchantInfo', 'merchant');
        Session::delete('merchantId', 'merchant');
        Session::clear('merchant');
    }

    /**
     * 检查用户登陆状态
     * @return bool
     */
    public static function hasActiveAdmin()
    {
        return Session::has('merAdminInfo', 'merchant') && Session::has('merAdminId', 'merchant');
    }

    /**
     * 获得登陆用户信息
     * @return mixed
     */
    public static function activeAdminInfoOrFail()
    {
        $adminInfo = Session::get('merAdminInfo', 'merchant');
        if (!$adminInfo) exception('请登陆');
        if (!$adminInfo['status']) exception('该账号已被关闭!');
        return $adminInfo;
    }

    /**
     * 获得登陆用户Id 如果没有直接抛出错误
     * @return mixed
     */
    public static function activeAdminIdOrFail()
    {
        $adminId = Session::get('merAdminInfo', 'merchant');
        if (!$adminId) exception('访问用户为登陆登陆!');
        return $adminId;
    }

    public static function activeMerchantInfoOrFail()
    {
        $merchantInfo = Session::get('merchantInfo', 'merchant');
        if (!$merchantInfo) exception('访问用户为登陆登陆!');
        return $merchantInfo;
    }

    public static function activeMerchantIdOrFail()
    {
        $merchantId = Session::get('merchantId', 'merchant');
        if (!$merchantId) exception('访问用户为登陆登陆!');
        return $merchantId;
    }

    /**获取讲师权限
     * @return array
     */
    public static function activeAdminAuthOrFail()
    {
        $adminInfo = self::activeAdminIdOrFail();
        return MerchantMenus::rulesByAuth($adminInfo['rules']);
    }

    /**
     * 获得有效管理员信息
     * @param $id
     * @return
     */
    public static function getValidAdminInfoOrFail($id)
    {
        $adminInfo = self::where(compact('id'))->find();
        if (!$adminInfo) exception('用户不能存在!');
        if (!$adminInfo['status']) exception('该账号已被关闭!');
        return $adminInfo;
    }

    /**
     * @param $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getOrdAdmin($field = 'real_name,id')
    {
        return self::where('level', '>', 0)->field($field)->select();
    }

    /**
     * 管理员操作记录
     * @param string $field
     * @param $level
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getAdminLog($field = 'real_name,id', $level)
    {
        return self::where('level', '>=', $level)->field($field)->select();
    }

    public static function getTopAdmin($field = 'real_name,id')
    {
        return self::where('level', 0)->field($field)->select();
    }

    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where, $level = 0)
    {
        $model = new self;
        if ($where['name'] != '') {
            $model = $model->where('account|real_name', 'LIKE', "%$where[name]%");
        }
        $merchant = self::activeMerchantInfoOrFail();
        $model = $model->where('mer_id', $merchant['id']);
        $model = $model->where('level', '>', $level);
        $model = $model->where('is_del', 0);
        return self::page($model, $where);
    }

    /**
     * 获取可用权限
     * @param $merId '当前登录的id'
     * @return array
     */
    public static function getRule($rules)
    {
        $ruleList = MerchantMenus::where('id', 'IN', $rules)->whereOr('pid', 0)->order('sort DESC')->select();
        return MerchantMenus::tidyMenuTier(false, $ruleList);
    }

    public static function getAccount($account)
    {
        $merchant = self::activeMerchantInfoOrFail();
        $merchant_account = self::where('mer_id', $merchant['id'])->where('level', 0)->value('account');
        $account_vif = $merchant_account . '@' . $account;
        return self::where('mer_id', $merchant['id'])->where('account', $account_vif)->count();
    }

    /**
     * 获取商户管理员
     */
    public static function getMerchantAdmin($mer_id)
    {
        if ($mer_id) return self::where('mer_id', $mer_id)->field('account,id')->select()->toArray();
    }
}
