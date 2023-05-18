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

use app\admin\model\special\Special;
use app\admin\model\special\Lecturer;
use app\admin\model\article\Article;
use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\system\WebRecommend;
use app\admin\model\special\SpecialTask;
use app\admin\model\download\DataDownload;
use app\admin\model\questions\TestPaper;

/**
 * Class RecommendRelation
 * @package app\admin\model\system
 */
class WebRecommendRelation extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    public static function setAddTimeAttr($value)
    {
        return time();
    }

    public static function setWhere($id)
    {
        $recommend = WebRecommend::where('id', $id)->find();
        $model = self::where('r.recommend_id', $id)->alias('r')->order('r.sort desc,r.add_time desc');
        switch ($recommend['type']) {
            case 0:
                $model = $model->join('Special s', 'r.link_id=s.id')->field('r.*,s.is_show,s.is_del')->where(['s.is_show' => 1,'s.status' => 1, 's.is_del' => 0]);
                break;
            case 1:
                $model = $model->join('Special s', 'r.link_id=s.id')->field('r.*,s.is_show,s.is_del')->where(['s.is_show' => 1,'s.status' => 1, 's.type' => 4, 's.is_del' => 0]);
                break;
            case 2:
                $model = $model->join('Lecturer l', 'r.link_id=l.id')->field('r.*,l.is_show,l.is_del')->where(['l.is_show' => 1, 'l.is_del' => 0]);
                break;
            case 3:
                $model = $model->join('DataDownload d', 'r.link_id=d.id')->field('r.*,d.is_show,d.is_del')->where(['d.is_show' => 1,'d.status' => 1, 'd.is_del' => 0]);
                break;
            case 4:
                $model = $model->join('Article a', 'r.link_id=a.id')->field('r.*,a.is_show,a.hide')->where(['a.is_show' => 1, 'a.hide' => 0]);
                break;
            case 7:
                $model = $model->join('TestPaper t', 'r.link_id=t.id')->field('r.*,t.is_show,t.is_del')->where(['t.is_show' => 1,'t.status' => 1, 't.is_del' => 0]);
                break;
            case 8:
                $model = $model->join('TestPaper t', 'r.link_id=t.id')->field('r.*,t.is_show,t.is_del')->where(['t.is_show' => 1,'t.status' => 1, 't.is_del' => 0]);
                break;
        }
        return $model;
    }

    public static function getAll($where, $id)
    {
        $data = self::setWhere($id)->page((int)$where['page'], (int)$where['limit'])->select();
        foreach ($data as &$itme) {
            if ($itme['type'] == 0) {
                $itme['type_name'] = '专题';
                $link = Special::PreWhere()->where('id', $itme['link_id'])->field('is_light,title')->find();
                if ($link['is_light']) {
                    $itme['count'] = 1;
                } else {
                    $itme['count'] = SpecialTask::getTaskCount($itme['link_id']);
                }
                $itme['title'] = $link['title'];
            } else if ($itme['type'] == 1) {
                $itme['type_name'] = '直播';
                $itme['title'] = Special::PreWhere()->where('id', $itme['link_id'])->where('type', 4)->value('title');
            } else if ($itme['type'] == 2) {
                $itme['type_name'] = '讲师';
                $itme['title'] = Lecturer::where('id', $itme['link_id'])->where(['is_del' => 0, 'is_show' => 1])->value('lecturer_name');
            } else if ($itme['type'] == 3) {
                $itme['type_name'] = '资料';
                $itme['title'] = DataDownload::where(['id' => $itme['link_id'], 'is_del' => 0, 'is_show' => 1,'status' => 1])->value('title');
            } else if ($itme['type'] == 4) {
                $itme['type_name'] = '新闻';
                $itme['title'] = Article::where(['id' => $itme['link_id'], 'hide' => 0, 'is_show' => 1])->value('title');
            }else if ($itme['type'] == 7) {
                $itme['type_name'] = '练习';
                $itme['title'] = TestPaper::where(['id' => $itme['link_id'], 'is_del' => 0, 'is_show' => 1, 'status' => 1])->value('title');
            } else if ($itme['type'] == 8) {
                $itme['type_name'] = '考试';
                $itme['title'] = TestPaper::where(['id' => $itme['link_id'], 'is_del' => 0, 'is_show' => 1, 'status' => 1])->value('title');
            }
        }
        $count = self::setWhere($id)->count();
        return compact('data', 'count');
    }

    public static function addDataDownload($id, $ids)
    {
        $recommend = WebRecommend::where('id', $id)->find();
        if (!$recommend) return false;
        $ids = explode(',', $ids);
        if (count($ids) <= 0) return false;
        foreach ($ids as $key => $value) {
            $data['type'] = $recommend['type'];
            $data['recommend_id'] = $id;
            $data['link_id'] = $value;
            if (self::be($data)) continue;
            $data['add_time'] = time();
            self::set($data);
        }
        return true;
    }

    public static function userDelRecemmend($id, $data_id)
    {
        return self::where(['recommend_id' => $id, 'link_id' => $data_id])->delete();
    }

    public static function updateRecommendSort($id, $value)
    {
        return self::where(['id' => $id])->update(['sort' => $value]);
    }
}
