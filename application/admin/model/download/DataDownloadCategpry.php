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


namespace app\admin\model\download;

use traits\ModelTrait;
use basic\ModelBasic;
use service\UtilService as Util;
use app\admin\model\download\DataDownload;

/**
 * Class DataDownloadCategpry 二级分类
 * @package app\admin\model\download
 */
class DataDownloadCategpry extends ModelBasic
{
    use ModelTrait;

    public static function specialCategoryAll($type = 0)
    {
        $model = self::where(['is_del' => 0, 'is_show' => 1]);
        if ($type == 1) {
            $model = $model->where('pid', 0);
        }
        $list = $model->order('sort desc,add_time desc')->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        $list = Util::sortListTier($list, 0, 'pid');
        return $list;
    }

    public static function get_download_cate_list($where)
    {
        $data = self::setWhere($where)->column('id,pid');
        $list = [];
        foreach ($data as $ket => $item) {
            $cate = self::where('id', $ket)->find();
            if ($cate) {
                $cate = $cate->toArray();
                $cate['data_count'] = 0;
                if ($item > 0) {
                    $cate['data_count'] = DataDownload::where('is_del', 0)->where('cate_id', $ket)->count();
                } else {
                    $pids = self::categoryId($ket);
                    $cate['data_count'] = DataDownload::where('is_del', 0)->where('cate_id', 'in', $pids)->count();
                }
                array_push($list, $cate);
                unset($cate);
            }
            if ($item > 0 && !array_key_exists($item, $data)) {
                $cate = self::where('id', $item)->find();
                if ($cate) {
                    $cate = $cate->toArray();
                    $cate['data_count'] = 0;
                    array_push($list, $cate);
                }
            }
        }
        return $list;
    }

    public static function setWhere($where)
    {
        $model = self::order('sort desc,add_time desc')->where('is_del', 0);
        if ($where['title']) $model = $model->where('title', 'like', "%$where[title]%");
        if ($where['pid']) $model = $model->where('pid', $where['pid']);
        return $model;
    }

    public static function getSubjectAll()
    {
        return self::order('sort desc,add_time desc')->where(['is_show' => 1, 'is_del' => 0])->field('title,id')->select();
    }

    /**获取一个分类下的所有分类ID
     * @param int $pid
     */
    public static function categoryId($pid = 0)
    {
        $data = self::where('is_del', 0)->where('pid', $pid)->column('id');
        array_push($data, $pid);
        return $data;
    }
}
