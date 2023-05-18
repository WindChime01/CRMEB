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

namespace app\wap\model\special;

use basic\ModelBasic;
use traits\ModelTrait;
use think\Db;

/**专题观看记录
 * Class SpecialWatch
 * @package app\wap\model\special
 */
class SpecialWatch extends ModelBasic
{
    use ModelTrait;

    /**
     * 素材观看时间
     */
    public static function materialViewing($uid, $data)
    {
        $viewing = self::where(['uid' => $uid, 'special_id' => $data['special_id'], 'task_id' => $data['task_id']])->find();
        if ($viewing) {
            $dat['viewing_time'] = $data['viewing_time'];
            $dat['percentage'] = $data['percentage'];
            $dat['total'] = $data['total'];
            if ($data['percentage'] > $viewing['percentage']) {
                return self::edit($dat, $viewing['id']);
            } else {
                return true;
            }
        } else {
            $data['uid'] = $uid;
            $data['add_time'] = time();
            return self::set($data);
        }
    }

    /**
     * 查看素材是否观看
     */
    public static function whetherWatch($uid, $special_id = 0, $task_id = 0)
    {
        return self::where(['uid' => $uid, 'special_id' => $special_id, 'task_id' => $task_id])->find();
    }

}
