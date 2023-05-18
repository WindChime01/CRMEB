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

use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;
use app\wap\model\material\DataDownloadCategpry;

/**资料 model
 * Class DataDownload
 * @package app\wap\model\material
 */
class DataDownload extends ModelBasic
{
    use ModelTrait;

    public static function PreWhere($alias = '', $model = null)
    {
        if (is_null($model)) $model = new self();
        if ($alias) {
            $model = $model->alias($alias);
            $alias .= '.';
        }
        return $model->where([$alias . 'is_show' => 1, $alias . 'status' => 1, $alias . 'is_del' => 0]);
    }

    /**列表
     * @param int $page
     * @param int $limit
     * @param $tid
     * @return array
     */
    public static function getDataDownloadExercisesList($page, $limit, $pid, $cate_id, $search)
    {
        $model = self::PreWhere();
        if ($cate_id) {
            $model = $model->where(['cate_id' => $cate_id]);
        } else if ($pid && !$cate_id) {
            $cate_ids = DataDownloadCategpry::where('pid', $pid)->column('id');
            $model = $model->where('cate_id', 'in', $cate_ids);
        }
        if ($search) $model = $model->where('title', 'LIKE', "%$search%");
        $list = $model->order('sort desc,id desc')->page($page, $limit)->select();
        $list = count($list) ? $list->toArray() : [];
        return $list;
    }

    /**
     * 获取单个资料的详细信息
     * @param $uid 用户id
     * @param $id 资料id
     * */
    public static function getOneDataDownload($uid, $id)
    {
        $data = self::PreWhere()->find($id);
        if (!$data) return self::setErrorInfo('您要查看的资料不存在!');
        if ($data->is_show == 0) return self::setErrorInfo('您要查看的资料已下架!');
        $title = $data->title;
        $data->collect = self::getDb('special_relation')->where(['link_id' => $id, 'type' => 1, 'uid' => $uid, 'category' => 1])->count() ? true : false;
        $data->abstract = htmlspecialchars_decode($data->abstract);
        $data = json_encode($data->toArray());
        return compact('data', 'title');
    }

    /**讲师名下资料
     * @param $mer_id
     * @param $page
     * @param $limit
     */
    public static function getLecturerDataDownloadList($mer_id, $page, $limit)
    {
        if ($mer_id) {
            $model = self::PreWhere();
            $model = $model->where(['mer_id' => $mer_id])->order('sort desc,id desc');
            $list = $model->page($page, $limit)->select();
            $list = count($list) ? $list->toArray() : [];
        } else {
            $list = [];
        }
        return $list;
    }

    /**获取资料标题
     * @param $id
     * @return float|mixed|string
     */
    public static function getName($id = 0)
    {
        if (!$id) return '';
        return self::where(['id' => $id])->value('title');
    }

}
