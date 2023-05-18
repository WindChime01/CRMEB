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

use app\admin\model\special\Grade;
use app\admin\model\special\SpecialSubject;
use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\ump\EventRegistration;
use app\admin\model\special\Lecturer;
use app\admin\model\special\Special;
use app\admin\model\special\SpecialTask;
use app\admin\model\store\StoreProduct;
use app\admin\model\article\Article;
use app\admin\model\questions\TestPaper;
use service\GroupDataService;

/**
 * Class Recommend
 * @package app\admin\model\system
 */
class Recommend extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    public static function setAddTimeAttr($value)
    {
        return time();
    }

    public static function getTypeseTingAttr($value, $data)
    {
        $name = '';
        switch ($data['typesetting']) {
            case 1:
                $name = '大图';
                break;
            case 2:
                $name = '宫图';
                break;
            case 3:
                $name = '小图';
                break;
            case 4:
                $name = '左右切换';
                break;
            default:
                $name = '其他';
                break;
        }
        return $name;
    }

    public static function getTypeNameAttr($value, $data)
    {
        $name = '';
        switch ($data['type']) {
            case 0:
                $name = '专题';
                break;
            case 1:
                $name = '新闻';
                break;
            case 2:
                $name = '直播';
                break;
            case 3:
                $name = '自定义';
                break;
            case 4:
                $name = '商品';
                break;
            case 5:
                $name = '直播[内置]';
                break;
            case 6:
                $name = '讲师';
                break;
            case 7:
                $name = '活动[内置]';
                break;
            case 8:
                $name = '拼团';
                break;
            case 10:
                $name = '素材';
                break;
            case 11:
                $name = '练习';
                break;
            case 12:
                $name = '考试';
                break;
            case 13:
                $name = '广告[内置]';
                break;
            case 14:
                $name = '资料';
                break;
        }
        return $name;
    }

    public static function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public static function getIconKeyAttr($value, $data)
    {
        if (!$data['icon']) return '';
        $value = parse_url($data['icon']);
        $value = isset($value['path']) ? substr($value['path'], 1) : '';
        return $value;
    }

    public static function getImageKeyAttr($value, $data)
    {
        if (!$data['image']) return '';
        $value = parse_url($data['image']);
        $value = isset($value['path']) ? substr($value['path'], 1) : '';
        return $value;
    }

    public static function fixedList()
    {
        $list = self::where('is_fixed', 1)->order('sort desc,add_time desc')->select();
        foreach ($list as &$item) {
            $item['number'] = RecommendRelation::where(['recommend_id' => $item['id']])->count();
        }
        return $list;
    }

    public static function getRecommendList($where)
    {
        $model = self::where('is_fixed', $where['is_fixed']);
        if ($where['order']) {
            $model->order(self::setOrder($where['order']));
        } else $model->order('sort desc,add_time desc');
        $data = $model->page((int)$where['page'], (int)$where['limit'])->select();
        foreach ($data as $item) {
            $item['type_name'] = self::getTypeNameAttr('', $item);
            $item['type_ting'] = self::getTypeseTingAttr('', $item);
            $item['number'] = 0;
            switch ($item['type']) {
                case 0:
                case 8:
                    $item['number'] = RecommendRelation::where(['r.recommend_id' => $item['id']])->alias('r')->join('Special s', 's.id=r.link_id')->where(['s.is_del' => 0, 's.is_show' => 1, 's.status' => 1])->count();
                    break;
                case 4:
                    $item['number'] = RecommendRelation::where(['r.recommend_id' => $item['id']])->alias('r')->join('StoreProduct p', 'p.id=r.link_id')->where(['p.is_del' => 0, 'p.is_show' => 1, 'p.status' => 1])->count();
                    break;
                case 1:
                    $item['number'] = RecommendRelation::where(['recommend_id' => $item['id']])->alias('r')->join('Article a', 'a.id=r.link_id')->where(['a.hide' => 0, 'a.is_show' => 1])->count();
                    break;
                case 10:
                    $item['number'] = RecommendRelation::where(['recommend_id' => $item['id']])->alias('r')->join('SpecialTask t', 't.id=r.link_id')->where(['t.is_del' => 0, 't.is_show' => 1])->count();
                    break;
                case 5:
                    $item['number'] = Special::where(['type' => 4, 'is_show' => 1, 'is_del' => 0])->count();
                    break;
                case 6:
                    $item['number'] = RecommendRelation::where(['recommend_id' => $item['id']])->alias('r')->join('Lecturer l', 'l.id=r.link_id')->where(['l.is_del' => 0, 'l.is_show' => 1])->count();
                    break;
                case 7:
                    $item['number'] = 1;
                    break;
                case 11:
                    $item['number'] = RecommendRelation::where(['recommend_id' => $item['id']])->alias('r')->join('TestPaper t', 't.id=r.link_id')->where(['t.is_del' => 0, 't.is_show' => 1, 't.status' => 1])->count();
                    break;
                case 12:
                    $item['number'] = RecommendRelation::where(['recommend_id' => $item['id']])->alias('r')->join('TestPaper t', 't.id=r.link_id')->where(['t.is_del' => 0, 't.is_show' => 1, 't.status' => 1])->count();
                    break;
                case 13:
                    $ads = GroupDataService::getData('homepage_ads');
                    $item['number'] = count($ads);
                    break;
                case 14:
                    $item['number'] = RecommendRelation::where(['recommend_id' => $item['id']])->alias('r')->join('DataDownload d', 'd.id=r.link_id')->where(['d.is_del' => 0, 'd.is_show' => 1, 'd.status' => 1])->count();
                    break;
            }
            if ($item['type'] != 7) $item['number'] = $item['show_count'] < $item['number'] ? $item['show_count'] : $item['number'];
            $item['grade_title'] = $item['grade_id'] > 0 ? SpecialSubject::where(['id' => $item['grade_id'], 'is_del' => 0])->value('name') : '无';
        }
        $count = self::where('is_fixed', $where['is_fixed'])->count();
        return compact('data', 'count');
    }
}
