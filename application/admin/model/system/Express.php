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
use service\CacheService;

/**
 * Class Express
 * @package app\admin\model\system
 */
class Express extends ModelBasic
{
    use ModelTrait;

    public static function systemPage($params)
    {
        $model = new self;
        if ($params['keyword'] !== '') $model = $model->where('name|code', 'LIKE', "%$params[keyword]%");
        $model = $model->order('sort DESC,id DESC');
        return self::page($model, $params);
    }

    public static function expressList()
    {
        $key = md5('zsff_plat_express_list');
        $list = CacheService::get($key);
        if (!$list) {
            try {
                $list = self::where(['is_show' => 1])->order('sort desc')->select();
                $list = count($list) > 0 ? $list->toArray() : [];
                CacheService::set($key, $list, 86400);
            } catch (\Throwable $e) {
                $list = [];
            }
        }
        return $list;
    }
}
