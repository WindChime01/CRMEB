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

namespace app\admin\model\educational;

use traits\ModelTrait;
use basic\ModelBasic;
use service\UtilService as Util;
use app\admin\model\educational\Teacher;

/**
 * 学员联系方式
 * Class ContactPhone
 * @package app\admin\model\educational
 */
class ContactPhone extends ModelBasic
{
    use ModelTrait;

    /**添加学员联系方法
     * @param array $data
     */
    public static function contactPhoneAdd($id = 0, $data = [])
    {
        if (!$id) return false;
        self::where('sid', $id)->delete();
        foreach ($data as $k => &$time) {
            $time['sid'] = $id;
            self::set($time);
        }
        return true;
    }

    /**
     * 学员联系方法列表
     */
    public static function contactPhoneList($id = 0)
    {
        return self::where(['sid' => $id])->order('id asc')->select();
    }
}
