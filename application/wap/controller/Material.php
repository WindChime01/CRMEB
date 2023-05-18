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

use service\JsonService;
use service\SystemConfigService;
use think\Url;
use app\wap\model\user\User;
use app\wap\model\material\DataDownloadCategpry;
use app\wap\model\material\DataDownload;
use app\wap\model\material\DataDownloadBuy;
use app\wap\model\material\DataDownloadRecords;
use service\UtilService;
use app\wap\model\special\SpecialRelation;

/**资料控制器
 * Class Material
 * @package app\wap\controller
 */
class Material extends AuthController
{

    /**
     * 白名单
     * */
    public static function WhiteList()
    {
        return [
            'material_list',
            'get_material_cate',
            'get_material_list'
        ];
    }

    /**资料列表
     * @param int $pid
     * @param int $cate_id
     * @return mixed
     */
    public function material_list($pid = 0, $cate_id = 0)
    {
        $this->assign([
            'homeLogo' => SystemConfigService::get('home_logo'),
            'pid' => (int)$pid,
            'cate_id' => (int)$cate_id
        ]);
        return $this->fetch();
    }

    /**我的资料
     * @return mixed
     */
    public function my_material()
    {
        $this->assign(['title' => '我的资料']);
        return $this->fetch();
    }

    /**
     * 资料分类
     */
    public function get_material_cate()
    {
        $cateogry = DataDownloadCategpry::with('children')->where(['is_show' => 1, 'is_del' => 0])->order('sort desc,id desc')->where('pid', 0)->select();
        return JsonService::successful($cateogry->toArray());
    }

    /**
     * 资料列表
     */
    public function get_material_list()
    {
        list($page, $limit, $pid, $cate_id, $search) = UtilService::PostMore([
            ['page', 1],
            ['limit', 10],
            ['pid', 0],
            ['cate_id', 0],
            ['search', '']
        ], $this->request, true);
        return JsonService::successful(DataDownload::getDataDownloadExercisesList($page, $limit, $pid, $cate_id, $search));
    }

    /**
     * 我的资料
     */
    public function my_material_list()
    {
        list($page, $limit) = UtilService::PostMore([
            ['page', 1],
            ['limit', 10]
        ], $this->request, true);
        return JsonService::successful(DataDownloadBuy::getUserDataDownload($this->uid, $page, $limit));
    }

    /**
     * 资料收藏
     * @param $id int 资料id
     * @return json
     */
    public function collect($id = 0)
    {
        if (!$id) return JsonService::fail('缺少参数');
        if (SpecialRelation::SetCollect($this->uid, $id, 1))
            return JsonService::successful('成功');
        else
            return JsonService::fail('失败');
    }

    /**用户下载记录
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userDownload($id)
    {
        if (!$id) return JsonService::fail('缺少参数');
        $res = DataDownloadRecords::addDataDownloadRecords($id, $this->uid);
        if ($res) {
            DataDownload::where('id', $id)->setInc('sales');
            return JsonService::successful('');
        } else
            return JsonService::fail();
    }

}
