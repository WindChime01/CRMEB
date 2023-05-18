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

namespace app\wap\controller;

use app\wap\model\article\Article as ArticleModel;
use app\wap\model\wap\ArticleCategory;
use app\wap\model\wap\Search;
use basic\WapBasic;
use service\JsonService;
use service\UtilService;
use think\Db;
use think\Url;
use service\GroupDataService;

/**
 * 新闻控制器
 * Class Article
 * @package app\wap\controller
 */
class Article extends AuthController
{

    /**
     * 白名单
     */
    public static function WhiteList()
    {
        return [
            'get_unifiend_list',
            'news_bulletin',
            'unified_list',
            'news_list',
            'articleDetails',
            'details',
            'news_detail',
            'getArticleCate'
        ];
    }

    /**新闻列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function unified_list()
    {
        $title = '新闻列表';
        $category = ArticleCategory::where(['status' => 1, 'is_del' => 0])->order('sort DESC,add_time DESC')->select();
        $category = count($category) > 0 ? $category->toArray() : [];
        $this->assign([
            'title' => $title,
            'category' => json_encode($category),
        ]);
        return $this->fetch();
    }

    /**新闻分类列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleCate()
    {
        $category = ArticleCategory::where(['status' => 1, 'is_del' => 0])->order('sort DESC,add_time DESC')->select();
        $category = count($category) > 0 ? $category->toArray() : [];
        return JsonService::successful($category);
    }

    /**
     *新闻列表
     */
    public function get_unifiend_list()
    {
        $where = UtilService::getMore([
            ['page', 1],
            ['limit', 10],
            ['cid', ''],
        ]);
        return JsonService::successful(ArticleModel::getUnifiendList($where));
    }

    /**
     * 首页新闻简报
     */
    public function news_bulletin()
    {
        $news_bulletin = GroupDataService::getData('news_bulletin');
        return JsonService::successful($news_bulletin);
    }

    /**
     * 资讯详情
     */
    public function details($id = 0)
    {
        $this->assign('id', $id);
        return $this->fetch('news_detail');
    }

    /**
     * 新闻详情
     */
    public function articleDetails($id = 0)
    {
        $article = ArticleModel::where(['id' => $id, 'is_show' => 1])->find();
        if (!$article) return JsonService::fail('您查看的文章不存在');
        $content = Db::name('articleContent')->where('nid', $article["id"])->value('content');
        $article["content"] = htmlspecialchars_decode($content);
        //增加浏览次数
        $article["visit"] = $article["visit"] + 1;
        $article["add_time"] = date('Y-m-d', $article["add_time"]);
        ArticleModel::where('id', $id)->update(["visit" => $article["visit"]]);
        return JsonService::successful($article);
    }

    /**
     * 新闻
     */
    public function news_list()
    {
        return $this->fetch();
    }

    /**
     * 资讯详情
     */
    public function news_detail($id = 0)
    {
        $title = ArticleModel::where(['id' => $id, 'is_show' => 1])->value('title');
        $this->assign(['id' => $id, 'title' => $title]);
        return $this->fetch();
    }
}
