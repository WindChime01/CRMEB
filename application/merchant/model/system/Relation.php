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

namespace app\merchant\model\system;

use traits\ModelTrait;
use basic\ModelBasic;
use service\UtilService as Util;
use app\merchant\model\user\User;
use app\merchant\model\special\Special;
use app\merchant\model\download\DataDownload;

/**
 * 关联记录 Model
 * Class Relation
 * @package app\merchant\model\questions
 */
class Relation extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($relationship = 0, $relationship_id = 0)
    {
        return self::where(['is_del' => 0, 'relationship' => $relationship, 'relationship_id' => $relationship_id]);
    }

    public static function getSpecialJion($relationship_id, $relationship)
    {
        return self::alias('r')->join('Special s', 'r.relation_id=s.id')
            ->where(['r.is_del' => 0, 's.is_show' => 1, 's.is_del' => 0, 's.status' => 1,'r.relationship' => $relationship, 'r.relationship_id' => $relationship_id])
            ->field('s.type,s.title,s.is_light,s.light_type,s.mer_id,s.image,s.id,r.sort,r.id as rid,r.relationship,r.relationship_id');
    }

    /**获取试题关联的专题
     * @param $id
     */
    public static function getQuestionsRelationSpecial($relationship_id, $relationship)
    {
        $data = self::getSpecialJion($relationship_id, $relationship)->order('r.sort DESC,rid DESC')->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        $count = self::getSpecialJion($relationship_id, $relationship)->count();
        return compact('data', 'count');
    }

    /**关联
     * @param $id
     * @param $special_ids
     * @param $relationship
     */
    public static function setRelations($id, $ids, $relationship)
    {
        $ids = explode(',', $ids);
        if (count($ids) <= 0) return false;
        foreach ($ids as $key => $value) {
            $data['relationship'] = $relationship;
            $data['relationship_id'] = $id;
            $data['relation_id'] = $value;
            if (self::be($data)) continue;
            $data['add_time'] = time();
            self::set($data);
        }
        return true;
    }

    /**修改排序
     * @param $id
     * @param $special_id
     * @param $value
     */
    public static function updateRelationSort($id, $special_id, $relationship, $value)
    {
        return self::where(['is_del' => 0, 'relationship' => $relationship, 'relationship_id' => $id, 'relation_id' => $special_id])->update(['sort' => $value]);

    }

    /**
     * @param $id
     * @param $special_id
     * @param $relationship
     */
    public static function delRelation($id, $special_id, $relationship)
    {
        return self::where(['is_del' => 0, 'relationship' => $relationship, 'relationship_id' => $id, 'relation_id' => $special_id])->delete();
    }

    public static function getTestPaperJion($relationship_id, $relationship)
    {
        return self::alias('r')->join('TestPaper t', 'r.relation_id=t.id')
            ->where(['r.is_del' => 0, 't.is_show' => 1, 't.is_del' => 0, 't.status' => 1,'r.relationship' => $relationship, 'r.relationship_id' => $relationship_id])
            ->field('t.type,t.title,t.id,r.sort,r.id as rid,r.relationship,r.relationship_id');
    }

    /**获取专题关联的考试或练习
     * @param $id
     * @param $relationship
     */
    public static function getRelationTestPaper($id, $relationship, $page, $limit)
    {
        $data = self::getTestPaperJion($id, $relationship)->page($page, $limit)->order('r.sort DESC,rid DESC')->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        foreach ($data as $key => &$value) {
            if ($value['type'] == 1) {
                $value['types'] = '练习';
            } else {
                $value['types'] = '考试';
            }
        }
        $count = self::getTestPaperJion($id, $relationship)->count();
        return compact('data', 'count');
    }

    public static function getDataDownloadJion($relationship_id, $relationship)
    {
        return self::alias('r')->join('DataDownload d', 'r.relation_id=d.id')
            ->where(['r.is_del' => 0, 'd.is_show' => 1, 'd.is_del' => 0,'d.status' => 1, 'r.relationship' => $relationship, 'r.relationship_id' => $relationship_id])
            ->field('d.title,d.id,r.sort,r.id as rid,r.relationship,r.relationship_id');
    }

    /**获取专题关联的资料
     * @param $id
     * @param $relationship
     */
    public static function getRelationDataDownload($id, $relationship, $page, $limit)
    {
        $data = self::getDataDownloadJion($id, $relationship)->page($page, $limit)->order('r.sort DESC,rid DESC')->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        $count = self::getDataDownloadJion($id, $relationship)->count();
        return compact('data', 'count');
    }

}
