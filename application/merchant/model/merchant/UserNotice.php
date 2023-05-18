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

use app\admin\model\wechat\WechatUser;
use app\admin\model\user\UserNoticeSee;
use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 用户通知 model
 * Class UserNotice
 * @package app\merchant\model\merchant
 */
class UserNotice extends ModelBasic
{
    use ModelTrait;

    /**
     * @return array
     */
    public static function getList($where = [])
    {
        $model = new self;
        $model = $model->where('type', 1);
        $model = $model->order('id desc');
        if (!empty($where)) {
            $data = ($data = $model->page((int)$where['page'], (int)$where['limit'])->select()) && count($data) ? $data->toArray() : [];
            foreach ($data as &$item) {
                $item['send_time'] = date('Y-m-d H:i:s', $item['send_time']);
            }
            $count = self::count();
            return compact('data', 'count');
        }
    }

    /**
     * 获取用户通知
     * @param array $where
     * @return array
     */
    public static function getUserList($where = array())
    {
        $model = new self;
        if (isset($where['title']) && $where['title'] != '') $model = $model->where('title', 'LIKE', "%" . $where['title'] . "%");
        $model = $model->where('type', 2);
        $model = $model->where('is_send', 0);
        $model = $model->order('id desc');
        return self::page($model, $where);
    }
}
