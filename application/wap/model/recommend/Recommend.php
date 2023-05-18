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

namespace app\wap\model\recommend;

use app\wap\model\user\User;
use basic\ModelBasic;
use traits\ModelTrait;

/**移动端推荐、导航
 * Class Recommend
 * @package app\wap\model\recommend
 */
class Recommend extends ModelBasic
{
    use ModelTrait;

    /**首页导航
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getRecommend()
    {
        return self::where(['is_fixed' => 1, 'is_show' => 1])->order('sort desc,add_time desc')
            ->field(['title', 'icon', 'type', 'link', 'grade_id', 'id'])->select();
    }

    /**个人中心菜单
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getPersonalCenterMenuList($is_statu, $is_write_off, $business)
    {
        $model = self::where(['is_fixed' => 2, 'is_show' => 1]);
        if (!$is_statu) {
            $model = $model->where('is_promoter', 0);
        }
        if (!$is_write_off) {
            $model = $model->where('is_write_off', 0);
        }
        if (!$business) {
            $model = $model->where('is_lecturer', 'in', [0, 1]);
        } else {
            $model = $model->where('is_lecturer', 'in', [0, 2]);
        }
        return $model->order('sort desc,add_time desc')->field(['title', 'icon', 'type', 'link', 'is_promoter', 'id'])->select();
    }

    /**
     * @param $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getRecommendIdAll()
    {
        $model = self::where(['is_fixed' => 0, 'is_show' => 1]);
        $all = $model->field('id,type')->select();
        $all = count($all) > 0 ? $all->toArray() : [];
        $idsAll = [];
        foreach ($all as $item) {
            if (RecommendRelation::getRelationCount($item['id'], (int)$item['type'])) {
                array_push($idsAll, $item['id']);
            }
        }
        return $idsAll;
    }

    /**
     * 获取主页推荐列表
     *  $page 分页
     *  $limit
     * */
    public static function getContentRecommend($uid, $is_member)
    {
        $idsAll = self::getRecommendIdAll();
        $model = self::where(['is_fixed' => 0, 'is_show' => 1])->where('id', 'in', $idsAll)
            ->field(['id', 'typesetting', 'title', 'type', 'icon', 'image', 'grade_id', 'show_count'])
            ->order('sort desc,add_time desc');
        $recommend = $model->select();
        $recommend = count($recommend) ? $recommend->toArray() : [];
        foreach ($recommend as &$item) {
            $item['sum_count'] = RecommendRelation::getRelationCount($item['id'], (int)$item['type']);
            $item['list'] = RecommendRelation::getRelationList($item['id'], (int)$item['type'], $item['typesetting'], $item['show_count'], $is_member);
            if ($item['typesetting'] == 4 && count($item['list']) > 0) {
                list($ceilCount, $data) = self::getTypesettingList($item['list']);
                $item['data'] = $data;
                $item['ceilCount'] = $ceilCount;
            } else {
                $item['data'] = [];
                $item['ceilCount'] = 0;
            }
            $item['courseIndex'] = 1;
        }
        return compact('recommend');
    }

    /**
     * 获取数组
     * @param array $list
     * @return array
     */
    public static function getTypesettingList(array $list)
    {
        $ceilCount = ceil(count($list) / 3);
        $data = [];
        for ($i = 0; $i < $ceilCount; $i++) {
            $data[] = ['value' => array_slice($list, $i * 3, 3)];
        }
        return [$ceilCount, $data];
    }

}
