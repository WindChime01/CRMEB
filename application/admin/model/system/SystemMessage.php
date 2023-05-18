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
use think\Db;

/**
 * 通知model
 * Class SystemNotice
 * @package app\admin\model\system
 */
class SystemMessage extends ModelBasic
{
    use ModelTrait;

    /**消息管理列表
     * @param $where
     * @return array
     * @throws \think\Exception
     */
    public static function systemMessageList($where)
    {
        $model = new self();
        $data = $model->page($where['page'], $where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        $count = self::count();
        return compact('data', 'count');
    }

    /**获得消息信息
     * @param string $template_const
     */
    public static function getSystemMessage($template_const = '')
    {
        return self::where('template_const', $template_const)->field('tempkey,temp_id,is_wechat,is_sms')->find();
    }

}
