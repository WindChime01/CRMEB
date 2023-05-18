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

namespace app\merchant\model\special;

use basic\ModelBasic;
use traits\ModelTrait;
use think\Db;
use app\merchant\model\special\SpecialSource;
use app\merchant\model\special\SpecialTask;
use app\merchant\model\special\Special;

/**素材观看记录
 * Class SpecialWatch
 * @package app\merchant\model\special
 */
class SpecialWatch extends ModelBasic
{
    use ModelTrait;

    /**平均值
     * @param $special_id
     * @param $uid
     * @return float|int
     */
    public static function percentageSpecial($special_id, $uid, $type)
    {
        if ($type == 5) {
            $special_ids = SpecialSource::where('special_id', $special_id)->column('source_id');
            $sourceCount = SpecialSource::where('special_id', 'in', $special_id)->count();
            $count = self::where(['uid' => $uid])->where('special_id', 'in', $special_ids)->count();
            if ($sourceCount <= $count) {
                return self::where(['uid' => $uid])->where('special_id', 'in', $special_ids)->avg('percentage');
            } else {
                $sum = self::where(['uid' => $uid])->where('special_id', 'in', $special_ids)->sum('percentage');
                return bcmul(bcdiv($sum, bcmul($sourceCount, 100, 0), 2), 100, 0);
            }
        } else {
            $sourceCount = SpecialSource::where('special_id', $special_id)->count();
            $count = self::where(['uid' => $uid, 'special_id' => $special_id])->count();
            if ($sourceCount <= $count) {
                return self::where(['uid' => $uid, 'special_id' => $special_id])->avg('percentage');
            } else {
                $sum = self::where(['uid' => $uid, 'special_id' => $special_id])->sum('percentage');
                return bcmul(bcdiv($sum, bcmul($sourceCount, 100, 0), 2), 100, 0);
            }
        }
    }

    public static function setWhere($special_id, $type)
    {
        if ($type == 5) {
            $special_ids = SpecialSource::where('special_id', $special_id)->column('source_id');
            $model = SpecialSource::where('s.special_id', 'in', $special_ids)->alias('s')
                ->join('SpecialTask t', 's.source_id=t.id');
        } else {
            $model = SpecialSource::where('s.special_id', $special_id)->alias('s')
                ->join('SpecialTask t', 's.source_id=t.id');
        }
        return $model->field('t.title,s.special_id,s.source_id');
    }

    /**获取素材的学习进度
     * @param $special_id
     * @param $uid
     * @param $type
     * @param $page
     * @param $limit
     * @return array
     * @throws \think\Exception
     */
    public static function percen_tage_specials($where)
    {
        if (!$where['is_light']) {
            $data = self::setWhere($where['special_id'], $where['type'])->page($where['page'], $where['limit'])->select();
            foreach ($data as $key => &$value) {
                $percentage = self::percentage($where['uid'], $value['source_id'], $value['special_id']);
                if ($percentage) $value['percentage'] = $percentage;
                else $value['percentage'] = 0;
            }
            $count = self::setWhere($where['special_id'], $where['type'])->count();
        } else {
            $data[0]['title'] = Special::where('id', $where['special_id'])->value('title');
            $percentage = self::percentage($where['uid'], 0, $where['special_id']);
            if ($percentage) $data[0]['percentage'] = $percentage;
            else $data[0]['percentage'] = 0;
            $count = 1;
        }
        return compact('count', 'data');
    }

    public static function percentage($uid, $task_id, $special_id)
    {
        return self::where(['uid' => $uid, 'task_id' => $task_id, 'special_id' => $special_id])->value('percentage');
    }
}
