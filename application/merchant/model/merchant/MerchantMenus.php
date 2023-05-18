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

use traits\ModelTrait;
use basic\ModelBasic;
use think\Url;

/**
 * 菜单管理 model
 * Class MerchantMenus
 * @package app\merchant\model\merchant
 */
class MerchantMenus extends ModelBasic
{
    use ModelTrait;

    public static $isShowStatus = [1 => '显示', 0 => '不显示'];

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
            if ($data) {
                $data = $data->toArray();
                return $data['menu_name'];
            } else {
                return '';
            }
        }
    }

    public static function getParentMenu($field = '*', $filter = false)
    {
        $where = ['pid' => 0];
        $query = self::field($field);
        $query = $filter ? $query->where(self::legalWhere($where)) : $query->where($where);
        return $query->order('sort DESC')->select();
    }

    public static function sortMenusTier($menus, $pid = 0, $level = 1, $html = '|-----', $clear = true)
    {
        static $list = [];
        if ($clear) $list = [];
        foreach ($menus as $k => $menu) {
            if ($menu->getData('pid') == $pid) {
                $menu['html'] = str_repeat($html, $level);
                $list[] = $menu;
                unset($menus[$k]);
                self::sortMenusTier($menus, $menu['id'], $level + 1, $html, false);
            }
        }
        return $list;
    }

    public static function menuList()
    {
        $menusList = self::where('is_show', '1')->order('sort DESC')->select();
        return self::tidyMenuTier(true, $menusList);
    }

    public static function ruleList()
    {
        $ruleList = self::order('sort DESC')->select();
        return self::tidyMenuTier(false, $ruleList);
    }

    public static function getAuthName($action, $controller, $module, $route)
    {
        return strtolower($module . '/' . $controller . '/' . $action . '/' . self::paramStr($route));
    }

    public static function tidyMenuTier($adminFilter = false, $menusList, $pid = 0, $navList = [])
    {
        static $allAuth = null;
        static $adminAuth = null;
        if ($allAuth === null) $allAuth = $adminFilter == true ? self::getAllAuth() : [];
        if ($adminAuth === null) $adminAuth = $adminFilter == true ? MerchantAdmin::activeAdminAuthOrFail() : [];
        foreach ($menusList as $k => $menu) {
            $menu = $menu->getData();
            if ($menu['pid'] == $pid) {
                unset($menusList[$k]);
                $params = json_decode($menu['params'], true);
                $authName = self::getAuthName($menu['action'], $menu['controller'], $menu['module'], $params);
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
        if (self::where('pid', $id)->count()) return self::setErrorInfo('请先删除该菜单下的子菜单!');
        return self::del($id);
    }

    public static function getAdminPage($params)
    {
        $model = new self;
        if ($params['is_show'] !== '') $model = $model->where('is_show', $params['is_show']);
        if ($params['pid'] !== '' && !$params['keyword']) $model = $model->where('pid', $params['pid']);
        if ($params['keyword'] !== '') $model = $model->where('menu_name', 'LIKE', "%$params[keyword]%");
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
            ->where('params', ['=', $params], ['=', '[]'], 'or')->value('menu_name');
    }

    public static function getAllAuth()
    {
        static $auth = null;
        $auth === null && ($auth = self::tidyAuth(self::all(function ($query) {
            $query->where('controller|action', '<>', '')->field('module,controller,action,params');
        }) ?: []));
        return $auth;
    }

    public static function rulesByAuth($rules)
    {
        if (empty($rules)) return [];
        $_auth = self::all(function ($query) use ($rules) {
            $query->where('id', 'IN', $rules)
                ->where('controller|action', '<>', '')
                ->field('module,controller,action,params');
        });
        return self::tidyAuth($_auth ?: []);
    }

    protected static function tidyAuth($_auth)
    {
        $auth = [];
        foreach ($_auth as $k => $val) {
            $auth[] = self::getAuthName($val['action'], $val['controller'], $val['module'], $val['params']);
        }
        return $auth;
    }

}
