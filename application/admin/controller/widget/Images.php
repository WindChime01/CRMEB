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

namespace app\admin\controller\widget;

use Api\AliyunOss;
use app\admin\model\system\SystemAttachment as SystemAttachmentModel;
use app\admin\model\system\SystemAttachmentCategory as Category;
use app\admin\controller\AuthController;
use service\SystemConfigService;
use service\JsonService as Json;
use service\FormBuilder as Form;
use think\Url;

/**
 * TODO 附件控制器
 * Class Images
 * @package app\admin\controller\widget
 */
class Images extends AuthController
{

    protected static $AccessKeyId = ''; //阿里云AccessKeyId

    protected static $accessKeySecret = ''; //阿里云AccessKeySecret

    protected static $end_point = ''; //EndPoint（地域节点）

    protected static $OssBucket = ''; //存储空间名称

    protected static $uploadUrl = ''; //空间域名 Domain
    /**
     * 初始化
     */
    protected function init()
    {
        self::$AccessKeyId = SystemConfigService::get('accessKeyId');//阿里云AccessKeyId
        self::$accessKeySecret = SystemConfigService::get('accessKeySecret');//阿里云AccessKeySecret
        self::$end_point = SystemConfigService::get('end_point');//EndPoint
        self::$OssBucket = SystemConfigService::get('OssBucket');//存储空间名称
        self::$uploadUrl = SystemConfigService::get('uploadUrl');//空间域名 Domain
        if (self::$AccessKeyId == '' || self::$accessKeySecret == '') return Json::fail('阿里云AccessKeyId或阿里云AccessKeySecret没有配置');
        if (self::$end_point == '') return Json::fail('EndPoint没有配置');
        if (self::$OssBucket == '') return Json::fail('存储空间名称没有配置');
        if (self::$uploadUrl == '') return Json::fail('空间域名没有配置');
        return AliyunOss::instance([
            'AccessKey' => self::$AccessKeyId,
            'AccessKeySecret' => self::$accessKeySecret,
            'OssEndpoint' => self::$end_point,
            'OssBucket' => self::$OssBucket,
            'uploadUrl' => self::$uploadUrl
        ]);
    }

    /**
     * 附件列表
     * @return \think\response\Json
     */
    public function index()
    {
        $pid = request()->param('pid');
        if ($pid === NULL) {
            $pid = session('pid') ? session('pid') : 0;
        }
        session('pid', $pid);
        $this->assign('pid', $pid);
        $this->assign('maxLength', $this->request->get('max_count', 0));
        $this->assign('fodder', $this->request->param('fodder', $this->request->get('fodder', '')));
        return $this->fetch('widget/images');
    }

    /**
     * 获取图片列表
     */
    public function get_image_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 18],
            ['pid', 0],
            ['title', '']
        ]);
        return Json::successful(SystemAttachmentModel::getImageList($where));
    }

    /**获取分类
     * @param string $name
     */
    public function get_image_cate($name = '')
    {
        return Json::successful(Category::getAll($name));
    }

    /**
     * 图片管理上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $pid = input('pid') != NULL ? input('pid') : session('pid');
        $title = input('title') != NULL ? input('title') : '';
        try {
            $aliyunOss = $this->init();
            $res = $aliyunOss->upload('file');
            if ($res) {
                SystemAttachmentModel::attachmentAdd($res['key'], $title, 0, 'image/jpg', $res['url'], $res['url'], $pid, 1, time());
                return Json::successful(['url' => $res]);
            } else {
                return Json::fail($aliyunOss->getErrorInfo()['msg']);
            }
        } catch (\Exception $e) {
            return Json::fail('上传失败:' . $e->getMessage());
        }
    }

    /**
     * ajax 提交删除
     */
    public function delete()
    {
        $post = $this->request->post();
        if (empty($post['imageid']))
            Json::fail('还没选择要删除的图片呢？');
        foreach ($post['imageid'] as $v) {
            if ($v) self::deleteimganddata($v);
        }
        Json::successful('删除成功');
    }

    /**删除图片和数据记录
     * @param $att_id
     */
    public function deleteimganddata($att_id)
    {
        $attinfo = SystemAttachmentModel::get($att_id);
        if ($attinfo) {
            try {
                $this->init()->delOssFile($attinfo->name);
            } catch (\Throwable $e) {
            }
            $attinfo->delete();
        }
    }

    /**
     * 修改图片名称
     */
    public function updateImageTitle()
    {
        $data = parent::postMore([
            ['att_id', 0],
            ['title', '']
        ]);
        $res = SystemAttachmentModel::attachmentTitle($data);
        if ($res)
            Json::successful('修改成功');
        else
            Json::fail('修改失败！');
    }

    /**
     * 移动图片分类显示
     */
    public function moveimg($imgaes)
    {
        $formbuider = [];
        $formbuider[] = Form::hidden('imgaes', $imgaes);
        $formbuider[] = Form::select('pid', '选择分类')->setOptions(function () {
            $list = Category::getCateList();
            $options = [['value' => 0, 'label' => '所有分类']];
            foreach ($list as $id => $cateName) {
                $options[] = ['label' => $cateName['html'] . $cateName['name'], 'value' => $cateName['id']];
            }
            return $options;
        })->filterable(1);
        $form = Form::make_post_form('编辑分类', $formbuider, Url::build('moveImgCecate'), 5);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 移动图片分类操作
     */
    public function moveImgCecate()
    {
        $data = parent::postMore([
            'pid',
            'imgaes'
        ]);
        if ($data['imgaes'] == '') return Json::fail('请选择图片');
        if (!$data['pid']) return Json::fail('请选择分类');
        $res = SystemAttachmentModel::where('att_id', 'in', $data['imgaes'])->update(['pid' => $data['pid']]);
        if ($res)
            Json::successful('移动成功');
        else
            Json::fail('移动失败！');
    }

    /**
     * ajax 添加分类
     */
    public function addcate($id = 0)
    {
        $formbuider = [];
        $formbuider[] = Form::select('pid', '上级分类', (string)$id)->setOptions(function () {
            $list = Category::getCateList(0);
            $options = [['value' => 0, 'label' => '所有分类']];
            foreach ($list as $id => $cateName) {
                $options[] = ['label' => $cateName['html'] . $cateName['name'], 'value' => $cateName['id']];
            }
            return $options;
        })->filterable(1);
        $formbuider[] = Form::input('name', '分类名称')->maxlength(6);
        $jsContent = <<<SCRIPT
parent.SuccessCateg();
parent.layer.close(parent.layer.getFrameIndex(window.name));
SCRIPT;
        $form = Form::make_post_form('添加分类', $formbuider, Url::build('saveCate'), $jsContent);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 添加分类
     */
    public function saveCate()
    {
        $post = $this->request->post();
        $data['pid'] = $post['pid'];
        $data['name'] = $post['name'];
        if (empty($post['name'])) return Json::fail('分类名称不能为空！');
        if (mb_strlen($data['name'], 'utf-8') > 6) return Json::fail('分类名称过长');
        $res = Category::create($data);
        if ($res)
            return Json::successful('添加成功');
        else
            return Json::fail('添加失败！');

    }

    /**
     * 编辑分类
     */
    public function editcate($id)
    {
        $Category = Category::get($id);
        if (!$Category) return Json::fail('数据不存在!');
        $formbuider = [];
        $formbuider[] = Form::hidden('id', $id);
        $formbuider[] = Form::select('pid', '上级分类', (string)$Category->getData('pid'))->setOptions(function () use ($id) {
            $list = Category::getCateList();
            $options = [['value' => 0, 'label' => '所有分类']];
            foreach ($list as $id => $cateName) {
                $options[] = ['label' => $cateName['html'] . $cateName['name'], 'value' => $cateName['id']];
            }
            return $options;
        })->filterable(1);
        $formbuider[] = Form::input('name', '分类名称', $Category->getData('name'))->maxlength(6);
        $jsContent = <<<SCRIPT
parent.SuccessCateg();
parent.layer.close(parent.layer.getFrameIndex(window.name));
SCRIPT;
        $form = Form::make_post_form('编辑分类', $formbuider, Url::build('updateCate'), $jsContent);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 更新分类
     * @param $id
     */
    public function updateCate($id)
    {
        $data = parent::postMore([
            'pid',
            'name'
        ]);
        if ($data['pid'] == '') return Json::fail('请选择父类');
        if (!$data['name']) return Json::fail('请输入分类名称');
        if (mb_strlen($data['name'], 'utf-8') > 6) return Json::fail('分类名称过长');
        Category::edit($data, $id);
        return Json::successful('分类编辑成功!');
    }

    /**
     * 删除分类
     */
    public function deletecate($id)
    {
        $chdcount = Category::where('pid', $id)->count();
        if ($chdcount) return Json::fail('有子栏目不能删除');
        $chdcount = SystemAttachmentModel::where('pid', $id)->count();
        if ($chdcount) return Json::fail('栏目内有图片不能删除');
        if (Category::del($id)) {
            SystemAttachmentModel::where(['pid' => $id])->update(['pid' => 0]);
            return Json::successful('删除成功!');
        } else
            return Json::fail('删除失败');
    }

    /**
     * 获取签名
     */
    public function get_signature()
    {
        return Json::successful($this->init()->getSignature());
    }

    /**
     * 删除阿里云oss
     * @param $key
     */
    public function del_oss_key($key = '', $url = '')
    {
        if (!$key && !$url) {
            return Json::fail('删除失败');
        }
        if ($url) {
            $key = SystemAttachmentModel::where(['att_dir' => $url])->value('name');
        }
        $res = $this->init()->delOssFile($key);
        if ($res) {
            return Json::successful('删除成功');
        } else {
            return Json::fail('删除失败');
        }
    }
}
