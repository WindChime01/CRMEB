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

use app\wap\model\activity\EventRegistration;
use app\wap\model\live\LiveStudio;
use app\wap\model\special\Lecturer;
use app\wap\model\special\Special;
use app\wap\model\article\Article;
use app\wap\model\special\SpecialTask;
use app\wap\model\topic\TestPaper;
use basic\ModelBasic;
use service\GroupDataService;
use traits\ModelTrait;
use app\wap\model\store\StoreProduct;

/**推荐内容 model
 * Class RecommendRelation
 * @package app\wap\model\recommend
 */
class RecommendRelation extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取主页推荐列表下的专题和图文内容
     * @param int $recommend_id 推荐id
     * @param int $type 类型 0=专题,1=图文
     * @param int $imagetype 图片显示类型
     * @param int $limit 显示多少条
     * @return array
     * */
    public static function getRelationList($recommend_id, $type, $imagetype, $limit, $is_member)
    {
        $limit = $limit ? $limit : 4;
        if ($type == 0 || $type == 8) {
            $model = self::where('a.recommend_id', $recommend_id)
                ->alias('a')->order('a.sort desc,a.add_time desc')
                ->join("__SPECIAL__ p", 'p.id=a.link_id')
                ->join('__SPECIAL_SUBJECT__ j', 'j.id=p.subject_id', 'LEFT')
                ->where(['p.is_show' => 1, 'p.status' => 1, 'p.is_del' => 0]);
            if (!$is_member) $model = $model->where(['p.is_mer_visible' => 0]);
            $list = $model->limit($limit)->field(['p.id', 'p.pink_money', 'p.status', 'p.is_light', 'p.light_type', 'p.is_mer_visible', 'p.is_pink', 'p.sort', 'p.title', 'p.image', 'p.abstract', 'p.label', 'p.image', 'p.money', 'p.pay_type', 'p.type as special_type', 'a.link_id', 'a.add_time', 'p.browse_count', 'p.fake_sales', 'p.member_pay_type', 'p.member_money'])
                ->select();
        } elseif ($type == 4) {
            $list = self::where('a.recommend_id', $recommend_id)
                ->alias('a')->order('a.sort desc,a.add_time desc')->limit($limit)
                ->join("StoreProduct p", 'p.id=a.link_id')
                ->where(['p.is_show' => 1, 'p.status' => 1, 'p.is_del' => 0])
                ->field(['p.id', 'p.price', 'p.store_name', 'p.status', 'p.image', 'p.sales', 'p.ficti', 'p.store_info', 'p.keyword', 'p.vip_price', 'p.member_pay_type', 'a.link_id', 'a.add_time'])
                ->select();
        } elseif ($type == 5) {
            //热门直播
            $list = LiveStudio::getLiveList(10, $is_member);
        } elseif ($type == 6) {
            //讲师
            $list = self::where('a.recommend_id', $recommend_id)
                ->alias('a')->order('a.sort desc,a.add_time desc')->limit($limit)
                ->join("Lecturer l", 'l.id=a.link_id')
                ->where(['l.is_show' => 1, 'l.is_del' => 0])->where('l.mer_id','>',0)
                ->field(['l.id', 'l.mer_id', 'l.lecturer_name', 'l.lecturer_head', 'l.label', 'l.is_show', 'l.is_del', 'a.link_id', 'a.add_time'])
                ->select();
        } elseif ($type == 7) {
            //线下活动
            $list = EventRegistration::eventRegistrationList(1, 1);
        } elseif ($type == 1) {
            $list = self::alias('a')->join('__ARTICLE__ e', 'e.id=a.link_id')
                ->where(['a.recommend_id' => $recommend_id, 'e.is_show' => 1])
                ->field(['e.title', 'e.image_input as image', 'e.synopsis as abstract', 'e.label', 'a.link_id', 'e.visit as browse_count', 'a.add_time'])
                ->limit($limit)->order('a.sort desc,a.add_time desc')->select();
        } elseif ($type == 10) {
            $list = self::alias('a')->join('SpecialTask t', 't.id=a.link_id')
                ->where(['a.recommend_id' => $recommend_id, 't.is_show' => 1, 't.is_del' => 0])
                ->field(['t.title', 't.image', 't.abstract', 't.type as task_type', 'a.link_id', 't.play_count as browse_count', 'a.add_time'])
                ->limit($limit)->order('a.sort desc,a.add_time desc')->select();
        } elseif ($type == 11 || $type == 12) {
            if ($type == 11) $types = 1;
            else  $types = 2;
            $list = self::where('a.recommend_id', $recommend_id)
                ->alias('a')->order('a.sort desc,a.add_time desc')->limit($limit)
                ->join("TestPaper t", 't.id=a.link_id')
                ->where(['t.is_show' => 1, 't.status' => 1, 't.is_del' => 0, 't.type' => $types])
                ->field(['t.id', 't.title', 't.type', 't.status', 't.image', 't.item_number', 't.answer', 't.money', 't.pay_type', 't.is_show', 't.fake_sales', 't.mer_id', 'a.link_id', 'a.add_time'])
                ->select();
        } elseif ($type == 13) {
            //首页广告
            $list = GroupDataService::getData('homepage_ads') ?: [];
        } elseif ($type == 14) {
            $list = self::where('a.recommend_id', $recommend_id)
                ->alias('a')->order('a.sort desc,a.add_time desc')->limit($limit)
                ->join("DataDownload d", 'd.id=a.link_id')
                ->where(['d.is_show' => 1, 'd.status' => 1, 'd.is_del' => 0])
                ->field(['d.id', 'd.title', 'd.image', 'd.status', 'd.sales', 'd.ficti', 'd.money', 'd.pay_type', 'd.is_show', 'd.member_pay_type', 'd.member_money', 'd.is_del', 'a.link_id', 'a.add_time'])
                ->select();
        }
        $list = (count($list) && !in_array($type, [5, 6, 7, 13])) ? $list->toArray() : $list;
        foreach ($list as &$item) {
            if ($type == 0 || $type == 8) {
                if (!isset($item['money'])) $item['money'] = 0;
                if ($type == 0 || $type == 8) {
                    $item['count'] = Special::numberChapters($item['special_type'], $item['id']);
                } else $item['count'] = 0;
                $item['image'] = isset($item['image']) ? get_oss_process($item['image'], $imagetype) : "";
                $item['label'] = (isset($item['label']) && $item['label'] && !is_array($item['label'])) ? json_decode($item['label']) : [];
                $special_type_name = "";
                if (isset($item['special_type']) && SPECIAL_TYPE[$item['special_type']] && $item['special_type'] != 6) {
                    $special_type_name = explode("专题", SPECIAL_TYPE[$item['special_type']]) ? explode("专题", SPECIAL_TYPE[$item['special_type']])[0] : "";
                } else {
                    if ($item['is_light']) {
                        $special_type_name = lightTypeNmae($item['light_type']);
                    }
                }
                $item['special_type_name'] = $special_type_name;
                $count = Special::learning_records($item['id']);
                $item['browse_count'] = processingData(bcadd($item['fake_sales'], $count, 0));
            } else if ($type == 4) {
                $item['title'] = $item['store_name'];
                $item['money'] = $item['price'];
                $item['image'] = get_oss_process($item['image'], $imagetype);
                $item['label'] = explode(',', $item['keyword']);
                $item['special_type_name'] = '商品';
                $item['count'] = bcadd($item['sales'], $item['ficti'], 0);
            } else if ($type == 10) {
                if (!isset($item['money'])) $item['money'] = 0;
                $item['label'] = [];
                if ($item['task_type'] == 1) {
                    $item['special_type_name'] = '图文';
                } elseif ($item['task_type'] == 2) {
                    $item['special_type_name'] = '音频';
                } elseif ($item['task_type'] == 3) {
                    $item['special_type_name'] = '视频';
                }
                $item['count'] = 0;
            } else if ($type == 11 || $type == 12) {
                $item['image'] = get_oss_process($item['image'], $imagetype);
                if ($type == 11) $item['special_type_name'] = '练习';
                else  $item['special_type_name'] = '考试';
                $item['count'] = bcadd($item['answer'], $item['fake_sales'], 0);
            } else if ($type == 1) {
                $item['label'] = json_decode($item['label'], true);
            } else if ($type == 14) {
                $item['image'] = get_oss_process($item['image'], $imagetype);
                $item['special_type_name'] = '资料';
                $item['count'] = bcadd($item['sales'], $item['ficti'], 0);
            }
        }
        return $list;
    }

    /**
     * 获取主页推荐下图文或者专题的总条数
     * @param int $recommend_id 推荐id
     * @param int $type 类型
     * @return int
     * */
    public static function getRelationCount($recommend_id, $type)
    {
        if ($type == 0) {
            $count = self::where('a.recommend_id', $recommend_id)->alias('a')->join("__SPECIAL__ p", 'p.id=a.link_id')
                ->join('__SPECIAL_SUBJECT__ j', 'j.id=p.subject_id', 'LEFT')->where(['p.is_show' => 1, 'p.status' => 1, 'p.is_del' => 0])->count();
        } else if ($type == 1) {
            $count = self::alias('a')->join('__ARTICLE__ e', 'e.id=a.link_id')->where(['a.recommend_id' => $recommend_id, 'e.is_show' => 1])->count();
        } else if ($type == 4) {
            $count = self::where('a.recommend_id', $recommend_id)->alias('a')->join("StoreProduct p", 'p.id=a.link_id')
                ->where(['p.is_del' => 0, 'p.status' => 1, 'p.is_show' => 1,])->count();
        } else if ($type == 5) {
            $count = Special::PreWhere()->where(['type' => 4])->count();
        } else if ($type == 6) {
            $count = self::where('a.recommend_id', $recommend_id)->alias('a')->join("Lecturer l", 'l.id=a.link_id')
                ->where(['l.is_show' => 1, 'l.is_del' => 0])->where('l.mer_id','>',0)->count();
        } else if ($type == 7) {
            $count = EventRegistration::homeCount() > 0 ? 1 : 0;
        } else if ($type == 8) {
            $count = self::where('a.recommend_id', $recommend_id)->alias('a')->join("__SPECIAL__ p", 'p.id=a.link_id')
                ->join('__SPECIAL_SUBJECT__ j', 'j.id=p.subject_id', 'LEFT')->where(['p.is_show' => 1, 'p.status' => 1, 'p.is_pink' => 1, 'p.is_del' => 0])->count();
        } else if ($type == 10) {
            $count = self::where('a.recommend_id', $recommend_id)->alias('a')->join("__SPECIAL_TASK__ t", 't.id=a.link_id')
                ->where(['t.is_show' => 1, 't.is_del' => 0])->count();
        } else if ($type == 11) {
            $count = self::where('a.recommend_id', $recommend_id)->alias('a')->join("TestPaper t", 't.id=a.link_id')
                ->where(['t.is_show' => 1, 't.status' => 1, 't.is_del' => 0, 't.type' => 1])->count();
        } else if ($type == 12) {
            $count = self::where('a.recommend_id', $recommend_id)->alias('a')->join("TestPaper t", 't.id=a.link_id')
                ->where(['t.is_show' => 1, 't.status' => 1, 't.is_del' => 0, 't.type' => 2])->count();
        } else if ($type == 13) {
            $ads = GroupDataService::getData('homepage_ads');
            $count = count($ads);
        } else if ($type == 14) {
            $count = self::where('a.recommend_id', $recommend_id)->alias('a')->join("DataDownload d", 'd.id=a.link_id')
                ->where(['d.is_show' => 1, 'd.status' => 1, 'd.is_del' => 0])->count();
        } else {
            $count = 0;
        }
        return $count;
    }
}
