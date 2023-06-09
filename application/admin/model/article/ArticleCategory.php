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

namespace app\admin\model\article;

use traits\ModelTrait;
use app\admin\model\article\Article as ArticleModel;
use basic\ModelBasic;
use service\UtilService as Util;

/**
 * 文章分类model
 * Class ArticleCategory
 * @package app\admin\model\article
 */
class ArticleCategory extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取系统分页数据   分类
     * @param array $where
     * @return array
     */
    public static function systemPage($where = array())
    {
        $model = new self;
        if ($where['title'] !== '') $model = $model->where('title', 'LIKE', "%$where[title]%");
        if ($where['status'] !== '') $model = $model->where('status', $where['status']);
        $model = $model->where('is_del', 0);
        $model = $model->where('hidden', 0)->order('sort desc');
        return self::page($model);
    }

    /**
     * 删除分类
     * @param $id
     * @return bool
     */
    public static function delArticleCategory($id)
    {
        if (count(self::getArticle($id, '*')) > 0)
            return self::setErrorInfo('请先删除该分类下的文章!');
        return self::edit(['is_del' => 1], $id, 'id');
    }

    /**
     * 获取分类名称和id     field
     * @param $field
     * @return array
     */
    public static function getField($field)
    {
        return self::where('is_del', 'eq', 0)->where('status', 'eq', 1)->where('hidden', 'eq', 0)->column($field);
    }

    /**
     * 分级排序列表
     * @param null $model
     * @return array
     */
    public static function getTierList($model = null)
    {
        if ($model === null) $model = new self();
        return Util::sortListTier($model->where('is_del', 0)->select()->toArray());
    }

    /**
     * 获取分类底下的文章
     * id  分类表中的分类id
     * return array
     * */
    public static function getArticle($id, $field)
    {
        $res = ArticleModel::where('hide', 0)->column($field, 'id');
        $new_res = array();
        foreach ($res as $k => $v) {
            $cid_arr = explode(',', $v['cid']);
            if (in_array($id, $cid_arr)) {
                $new_res[$k] = $res[$k];
            }
        }
        return $new_res;
    }

}
