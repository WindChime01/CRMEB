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


namespace app\admin\model\wechat;

use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\article\Article as ArticleModel;

/**
 * 图文消息 model
 * Class WechatNewsCategory
 * @package app\admin\model\wechat
 */
class WechatNewsCategory extends ModelBasic
{

    use ModelTrait;

    /**
     * 获取配置分类
     * @param array $where
     * @return array
     */
    public static function getAll($where = array())
    {
        $model = new self;
        if ($where['cate_name'] !== '') $model = $model->where('cate_name', 'LIKE', "%$where[cate_name]%");
        $model = $model->where('status', 1);
        return self::page($model, function ($item) {
            $new = ArticleModel::where('id', 'in', $item['new_id'])->where('hide', 1)->select();
            $item['new'] = $new;
        });
    }

    /**
     * 获取一条图文
     * @param int $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getWechatNewsItem($id = 0)
    {
        if (!$id) return [];
        $list = self::where('id', $id)->where('status', 1)->field('cate_name as title,new_id')->find();
        if ($list) {
            $list = $list->toArray();
            $new = ArticleModel::where('id', 'in', $list['new_id'])->where('hide', 1)->select();
            if ($new) $new = $new->toArray();
            $list['new'] = $new;
        }
        return $list;

    }
}
