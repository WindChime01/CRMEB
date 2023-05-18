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

namespace app\wap\model\material;

use basic\ModelBasic;
use traits\ModelTrait;
use think\Db;

/**资料下载记录 model
 * Class DataDownloadRecords
 * @package app\wap\model\material
 */
class DataDownloadRecords extends ModelBasic
{
    use ModelTrait;

    /**
     * 添加资料下载记录
     * @param $data_id
     * @param $uid
     * @return false|int|object
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function addDataDownloadRecords($data_id, $uid)
    {
        $info = self::where(['uid' => $uid, 'data_id' => $data_id])->find();
        if ($info) {
            $info->number = $info->number + 1;
            $info->update_time = time();
            $res = $info->save();
        } else {
            $res = self::set([
                'number' => 1,
                'add_time' => time(),
                'update_time' => time(),
                'uid' => $uid,
                'data_id' => $data_id
            ]);
        }
        return $res;
    }

}
