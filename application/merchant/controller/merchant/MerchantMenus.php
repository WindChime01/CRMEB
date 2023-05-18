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

namespace app\merchant\controller\merchant;

use service\FormBuilder as Form;
use traits\CurdControllerTrait;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use think\Url;
use app\merchant\model\merchant\MerchantMenus as MenusModel;
use app\merchant\controller\AuthController;

/**
 * 菜单管理控制器
 * Class SystemMenus
 * @package app\merchant\controller\merchant
 */
class MerchantMenus extends AuthController
{
    use CurdControllerTrait;

    public $bindModel = MenusModel::class;

    public function upload()
    {
        $res = Upload::Image('file', 'config');
        if (!$res->status) return Json::fail($res->error);
        $thumbPath = Upload::thumb($res->dir);
        return Json::successful('图片上传成功!', ['name' => $res->fileInfo->getSaveName(), 'url' => Upload::pathToUrl($thumbPath)]);
    }

    public function attribute()
    {
        $limit = 15;
        $total = MenusModel::count();
        $head = ['id' => '编号', 'pid' => '上级菜单', 'menu_name' => '按钮名称', 'module' => '模块', 'action' => '方法', 'is_show' => '是否显示', 'access' => '管理员可用', '_handle' => ['edit', 'del']];
        return Json::successful(compact('limit', 'total', 'head'));
    }

    public function page()
    {
        $limit = (int)$_GET['limit'];
        $first = (int)$_GET['first'];
        $menu = new MenusModel;
        $list = $menu->limit($first, $limit)->select();
        return Json::successful($list);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $params = parent::getMore([
            ['is_show', ''],
            ['access', ''],
            ['keyword', '']
        ], $this->request);
        $this->assign(MenusModel::getAdminPage($params));
        $this->assign(compact('params'));
        return $this->fetch();
    }


    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $this->assign(['title' => '编辑菜单', 'rules' => $this->rules()->getContent(), 'action' => Url::build('save')]);
        return $this->fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data = parent::postMore([
            'menu_name',
            'controller',
            ['module', 'admin'],
            'action',
            'icon',
            'params',
            ['pid', 0],
            ['sort', 0],
            ['is_show', 0]], $request);
        if (!$data['menu_name']) return Json::fail('请输入按钮名称');
        MenusModel::set($data);
        return Json::successful('添加菜单成功!');
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $this->assign(['title' => '编辑菜单', 'rules' => $this->read($id)->getContent(), 'action' => Url::build('update', array('id' => $id))]);
        return $this->fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $data = parent::postMore([
            'menu_name',
            'controller',
            ['module', 'admin'],
            'action',
            'params',
            'icon',
            ['sort', 0],
            ['pid', 0],
            ['is_show', 0]], $request);
        if (!$data['menu_name']) return Json::fail('请输入按钮名称');
        if (!MenusModel::get($id)) return Json::fail('编辑的记录不存在!');
        MenusModel::edit($data, $id);
        return Json::successful('修改成功!');
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $res = MenusModel::delMenu($id);
        if (!$res)
            return Json::fail(MenusModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    public function edit_content($id)
    {
        $this->assign(['field' => 'action', 'action' => Url::build('change_field', ['id' => $id, 'field' => 'action'])]);
        return $this->fetch();
    }

    /**
     * ICON图标展示页面
     *
     */
    public function icon()
    {
        return $this->fetch();
    }
}
