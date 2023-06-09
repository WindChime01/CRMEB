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

namespace app\admin\model\system;

use traits\ModelTrait;
use basic\ModelBasic;
use think\Url;

/**
 * 菜单  model
 * Class SystemMenus
 * @package app\admin\model\system
 */
class SystemMenus extends ModelBasic
{
    use ModelTrait;

    public static $isShowStatus = [1 => '显示', 0 => '不显示'];

    public static $accessStatus = [1 => '管理员可用', 0 => '管理员不可用'];

    public static function legalWhere($where = [])
    {
        $where['is_show'] = 1;
    }

    public function setParamsAttr($value)
    {
        $value = $value ? explode('/', $value) : [];
        $params = array_chunk($value, 2);
        $data = [];
        foreach ($params as $param) {
            if (isset($param[0]) && isset($param[1])) $data[$param[0]] = $param[1];
        }
        return json_encode($data);
    }

    protected function setControllerAttr($value)
    {
        return lcfirst($value);
    }

    public function getParamsAttr($_value)
    {
        return json_decode($_value, true);
    }

    public function getPidAttr($value)
    {
        if (!$value) {
            return '顶级';
        } else {
            $data = self::get($value);
            return empty($data) ? '' : $data->menu_name;
        }
    }

    public static function getParentMenu($field = '*', $filter = false)
    {
        $where = ['pid' => 0];
        $query = self::field($field);
        $query = $filter ? $query->where(self::legalWhere($where)) : $query->where($where);
        return $query->order('sort DESC,id DESC')->select();
    }

    public static function menuList()
    {
        $menusList = self::where('is_show', '1')->where('access', '1')->order('sort DESC,id DESC')->select();
        return self::tidyMenuTier(true, $menusList);
    }

    public static function ruleList()
    {
        $ruleList = self::order('sort DESC,id DESC')->select();
        return self::tidyMenuTier(false, $ruleList);
    }

    public static function rolesByRuleList($rules)
    {
        $res = SystemRole::where('id', 'IN', $rules)->field('GROUP_CONCAT(rules) as ids')->find();
        $ruleList = self::where('id', 'IN', $res['ids'])->whereOr('pid', 0)->order('sort DESC,id DESC')->select();
        return self::tidyMenuTier(false, $ruleList);
    }

    public static function getAuthName($action, $controller, $module, $route)
    {
        return strtolower($module . '/' . $controller . '/' . $action . '/' . SystemMenus::paramStr($route));
    }

    public static function tidyMenuTier($adminFilter = false, $menusList, $pid = 0, $navList = [])
    {
        static $allAuth = null;
        static $adminAuth = null;
        if ($allAuth === null) $allAuth = $adminFilter == true ? SystemRole::getAllAuth() : [];//所有的菜单
        if ($adminAuth === null) $adminAuth = $adminFilter == true ? SystemAdmin::activeAdminAuthOrFail() : [];//当前登录用户的菜单
        foreach ($menusList as $k => $menu) {
            $menu = $menu->getData();
            if ($menu['pid'] == $pid) {
                unset($menusList[$k]);
                if (in_array($menu['id'], ['148', '349', '273', '419', '278', '478', '477', '464', '476'])) continue;
                $params = json_decode($menu['params'], true);//获取参数
                $authName = self::getAuthName($menu['action'], $menu['controller'], $menu['module'], $params);// 按钮链接
                if ($pid != 0 && $adminFilter && in_array($authName, $allAuth) && !in_array($authName, $adminAuth)) continue;
                $menu['child'] = self::tidyMenuTier($adminFilter, $menusList, $menu['id']);
                if ($pid != 0 && !count($menu['child']) && !$menu['controller'] && !$menu['action']) continue;
                $menu['url'] = !count($menu['child']) ? Url::build($menu['module'] . '/' . $menu['controller'] . '/' . $menu['action'], $params) : 'javascript:void(0);';
                if ($pid == 0 && !count($menu['child'])) continue;
                $navList[] = $menu;
            }
        }
        return $navList;
    }

    public static function delMenu($id)
    {
        if (self::where('pid', $id)->count())
            return self::setErrorInfo('请先删除该菜单下的子菜单!');
        return self::del($id);
    }

    public static function getAdminPage($params)
    {
        $model = new self;
        if ($params['is_show'] !== '') $model = $model->where('is_show', $params['is_show']);
        if ($params['pid'] !== '' && !$params['keyword']) $model = $model->where('pid', $params['pid']);
        if ($params['keyword'] !== '') $model = $model->where('menu_name|id|pid', 'LIKE', "%$params[keyword]%");
        $model = $model->order('sort DESC,id DESC');
        return self::page($model, $params);
    }

    public static function paramStr($params)
    {
        if (!is_array($params)) $params = json_decode($params, true) ?: [];
        $p = [];
        foreach ($params as $key => $param) {
            $p[] = $key;
            $p[] = $param;
        }
        return implode('/', $p);
    }

    public static function getVisitName($action, $controller, $module, array $route = [])
    {
        $params = json_encode($route);
        return self::where('action', $action)
            ->where('controller', lcfirst($controller))
            ->where('module', lcfirst($module))
            ->where('params', ['=', $params], ['=', '[]'], 'or')->order('id DESC')->value('menu_name');
    }

    /**检查权限是否获得
     * @param $menu_name
     * @param $controller
     */
    public static function isMenu($menu_name, $controller)
    {
        $role_sign = get_login_role();
        $id = self::where(['menu_name' => $menu_name, 'controller' => $controller])->value('id');
        if ($role_sign['role_sign'] == 'admin') return true;
        $rules = SystemRole::where(['id' => $role_sign['role_id'], 'sign' => $role_sign['role_sign']])->value('rules');
        $rules_array = explode(',', $rules);
        if (in_array($id, $rules_array)) {
            return true;
        } else {
            return false;
        }
    }
}
