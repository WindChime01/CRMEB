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

namespace app\merchant\model\system;

use traits\ModelTrait;
use basic\ModelBasic;
use service\UtilService;

/**
 * 文件检验model
 * Class SystemAttachmentCategory
 * @package app\merchant\model\system
 */
class SystemAttachmentCategory extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取分类图
     * */
    public static function getAll($mer_id = 0)
    {
        $model = new self;
        if ($mer_id > 0) $model = $model->where('mer_id', $mer_id);
        return self::tidyMenuTier($model->select(), 0);
    }

    public static function tidyMenuTier($menusList, $pid = 0, $navList = [])
    {

        foreach ($menusList as $k => $menu) {
            $menu = $menu->getData();
            if ($menu['pid'] == $pid) {
                unset($menusList[$k]);
                $menu['child'] = self::tidyMenuTier($menusList, $menu['id']);
                $navList[] = $menu;
            }
        }
        return $navList;
    }

    /**获取分类下拉列表
     * @return array
     */
    public static function getCateList($id = 10000, $mer_id = 0)
    {
        $model = new self();
        if ($id == 0) $model->where('pid', $id);
        if ($mer_id > 0) $model = $model->where('mer_id', $mer_id);
        return UtilService::sortListTier($model->select()->toArray());
    }

    /**
     * 获取单条信息
     * */
    public static function getinfo($att_id)
    {
        $model = new self;
        $where['att_id'] = $att_id;
        return $model->where($where)->select()->toArray()[0];
    }

}
