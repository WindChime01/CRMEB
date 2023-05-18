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

namespace app\wap\model\topic;

use traits\ModelTrait;
use basic\ModelBasic;
use app\wap\model\special\Special;
use app\wap\model\topic\TestPaper;
use app\wap\model\material\DataDownload;

/**关联表
 * Class Relation
 * @package app\wap\model\topic
 */
class Relation extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($relationship = 0, $relationship_id = 0)
    {
        return self::where(['is_del' => 0, 'relationship' => $relationship, 'relationship_id' => $relationship_id]);
    }

    /**获取试题关联的专题
     * @param int $relationship
     * @param int $relationship_id
     */
    public static function getRelationSpecial($relationship = 0, $relationship_id = 0, $page = false, $limit = false)
    {
        $model = self::alias('r')->join('Special s', 'r.relation_id=s.id')
            ->where(['r.is_del' => 0, 's.is_show' => 1, 's.is_del' => 0, 'r.relationship' => $relationship, 'r.relationship_id' => $relationship_id]);
        if ($page) {
            $model = $model->page((int)$page, !$limit ? 20 : (int)$limit);
        }
        $data = $model->field('s.id,s.title,s.is_light,s.light_type,s.image,s.label,s.money,s.type,r.id as rid,r.sort')
            ->order('r.sort DESC,rid DESC')->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        return $data;
    }

    public static function getRelationTestPaper($relationship = 0, $relationship_id = 0)
    {
        $data = self::alias('r')->join('TestPaper t', 'r.relation_id=t.id')
            ->where(['r.is_del' => 0, 't.is_show' => 1, 't.is_del' => 0, 'r.relationship' => $relationship, 'r.relationship_id' => $relationship_id])
            ->field('t.id,t.title,r.id as rid,r.sort')->order('r.sort DESC,rid DESC')->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        return $data;
    }

    public static function getRelationDataDownload($relationship = 0, $relationship_id = 0)
    {
        $data = self::alias('r')->join('DataDownload d', 'r.relation_id=d.id')
            ->where(['r.is_del' => 0, 'd.is_show' => 1, 'd.is_del' => 0, 'r.relationship' => $relationship, 'r.relationship_id' => $relationship_id])
            ->field('d.id,d.title,r.id as rid,r.sort')->order('r.sort DESC,rid DESC')->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        return $data;
    }

}
