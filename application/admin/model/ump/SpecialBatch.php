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

namespace app\admin\model\ump;


use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\special\Special;
use app\admin\model\ump\SpecialExchange;

/**
 * 活动批次 model
 * Class MemberCard
 * @package app\admin\model\ump
 */
class SpecialBatch extends ModelBasic
{
    use ModelTrait;


    /**增加批次表
     * @param array $insert_data
     * @return bool|int|string
     */
    public static function addBatch(array $insert_data)
    {
        if (!$insert_data) {
            return false;
        }
        return self::insertGetId($insert_data);
    }

    /**批量获取活动批次卡
     * @param array $where
     * @return array|bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getBatchList(array $where)
    {
        if (!is_array($where)) {
            return false;
        }
        $batch_where = array();
        if (isset($where['title']) && $where['title']) {
            $batch_where['title'] = ['like', '%' . $where['title']];
        }
        if (isset($where['special_id']) && $where['special_id']) {
            $batch_where['special_id'] = $where['special_id'];
        }
        $time['data'] = '';
        if ($where['start_time'] != '' && $where['end_time'] != '') {
            $time['data'] = $where['start_time'] . ' - ' . $where['end_time'];
        }
        $data = self::getModelTime($time)->where($batch_where)->order('id DESC')
            ->page((int)$where['page'], (int)$where['limit'])
            ->select()
            ->each(function ($item) {
                $item['add_time'] = ($item['add_time'] != 0 || $item['add_time']) ? date('Y-m-d H:i:s', $item['add_time']) : '';
                $item['special_title'] = Special::where('id', $item['special_id'])->value('title');
            });
        $data = count((array)$data) ? $data->toArray() : [];
        $count = self::where($batch_where)->count();
        return compact('data', 'count');
    }


    public function getCreateTimeAttr($time)
    {
        return $time;//返回create_time原始数据，不进行时间戳转换。
    }

    public static function getBatchAll(array $where)
    {
        if (!$where || !is_array($where)) {
            $where = array();
        }
        return self::where($where)->select();
    }

    public static function delSpecialBatch($id)
    {
        $res = self::where('id', $id)->delete();
        $res1 = false;
        if ($res) {
            $res1 = SpecialExchange::where('card_batch_id', $id)->delete();
        }
        $res2 = $res && $res1;
        return $res2;
    }
}
