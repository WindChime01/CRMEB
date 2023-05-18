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

/**用户浏览记录 model
 * Class LearningRecords
 * @package app\wap\model\special
 */
class LearningRecords extends ModelBasic
{
    use ModelTrait;

    /**
     * 记录用户浏览记录
     * @param $specialId
     * @param $uid
     * @return false|int|object
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function recordLearning($specialId, $uid, $time)
    {
        $info = self::where(['uid' => $uid, 'special_id' => $specialId, 'add_time' => $time])->find();
        $res = true;
        if (!$info) {
            $res = self::set([
                'add_time' => $time,
                'uid' => $uid,
                'special_id' => $specialId
            ]);
        }
        return $res;
    }

}
