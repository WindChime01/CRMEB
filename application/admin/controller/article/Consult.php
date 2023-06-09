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

namespace app\admin\controller\article;

use app\admin\controller\AuthController;
use service\FormBuilder as Form;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use think\Url;
use app\admin\model\article\ArticleContent as ArticleContentContentModel;
use app\admin\model\article\Article as WechatNewsModel;
use traits\CurdControllerTrait;

/**
 * 活动咨询控制器
 * Class Consult
 * @package app\admin\controller\wechat
 */
class Consult extends AuthController
{

    use CurdControllerTrait;

    protected $bindModel = ArticleContentContentModel::class;

    public function edit_content($id, $type = 'content')
    {
        if (!$id) return $this->failed('数据不存在');
        $news = WechatNewsModel::get($id);
        if (!$news) return Json::fail('数据不存在!');
        $this->assign([
            'content' => ArticleContentContentModel::where('nid', $id)->value($type),
            'field' => $type,
            'action' => Url::build('consult_field', ['id' => $id, 'field' => $type])
        ]);
        return $this->fetch('public/edit_content');
    }

    public function index()
    {
        $where = parent::getMore([
            ['consult_type', 0],
            ['title', ''],
        ], $this->request);
        $this->assign('where', $where);
        $this->assign(WechatNewsModel::getConsultList($where));
        return $this->fetch();
    }

    public function create()
    {
        $field = [
            Form::text('title', '文章标题'),
            Form::frameImages('consult_image', '产品轮播图(640*640px)', Url::build('admin/widget.images/index', array('fodder' => 'consult_image')))->maxLength(5)->icon('images')->width('100%')->height('550px')->spin(0),
            Form::number('visit', '浏览量', 0),
            Form::number('sort', '排序', 0),
            Form::radio('status', '状态', 0)->options([['label' => '显示', 'value' => 1], ['label' => '隐藏', 'value' => 0]])->col(8)
        ];
        $form = Form::create(Url::build('save'));
        $form->setMethod('post')->setTitle('编辑文章')->components($field)->setSuccessScript('parent.$(".J_iframe:visible")[0].contentWindow.location.reload();');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * s上传图片
     * */
    public function upload()
    {
        $res = Upload::image('file', 'article');
        $thumbPath = Upload::thumb($res->dir);
        if ($res->status == 200)
            return Json::successful('图片上传成功!', ['name' => $res->fileInfo->getSaveName(), 'url' => Upload::pathToUrl($thumbPath)]);
        else
            return Json::fail($res->error);
    }

    public function save(Request $request)
    {
        $data = parent::postMore([
            ['title', ''],
            ['consult_image', []],
            ['visit', 0],
            ['sort', 0],
            ['consult_type', 1],
            ['status', 0],
        ], $request);
        if (!strlen(trim($data['title']))) return Json::fail('请输入文章名称');
        if (!count($data['consult_image'])) return Json::fail('请上传图片');
        if ($data['sort'] < 0) return Json::fail('排序不能是负数');
        $data['add_time'] = time();
        $data['is_consult'] = 1;
        $data['hide'] = 0;
        $data['is_hot'] = 1;
        $data['status'] = (int)$data['status'];
        $data['consult_image'] = implode(',', $data['consult_image']);
        $res = WechatNewsModel::set($data);
        if (!$res) return Json::fail('文章添加失败');
        return Json::successful('添加文章成功!');
    }

    public function edit($id)
    {
        $article = WechatNewsModel::get($id);
        if (!$article) return Json::fail('数据不存在!');
        $form = Form::create(Url::build('update', array('id' => $id)), [
            Form::text('title', '文章标题', $article->getData('title')),
            Form::frameImages('consult_image', '产品轮播图(640*640px)', Url::build('admin/widget.images/index', array('fodder' => 'consult_image')), explode(',', $article->getData('consult_image')))->maxLength(5)->icon('images')->width('100%')->height('550px')->spin(0),
            Form::number('visit', '浏览量', $article->getData('visit')),
            Form::number('sort', '排序', $article->getData('sort')),
            Form::radio('status', '状态', $article->getData('status'))->options([['label' => '显示', 'value' => 1], ['label' => '隐藏', 'value' => 0]])->col(8)
        ]);
        $form->setMethod('post')->setTitle('编辑文章')->setSuccessScript('parent.$(".J_iframe:visible")[0].contentWindow.location.reload();');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function update(Request $request, $id)
    {
        $data = parent::postMore([
            ['title', ''],
            ['consult_image', []],
            ['visit', 0],
            ['sort', 0],
            ['consult_type', 1],
            ['status', 0],
        ], $request);
        if (!strlen(trim($data['title']))) return Json::fail('请输入文章名称');
        if (!count($data['consult_image'])) return Json::fail('请上传图片');
        if ($data['sort'] < 0) return Json::fail('排序不能是负数');
        $data['consult_image'] = implode(',', $data['consult_image']);
        $data['status'] = (int)$data['status'];
        if (!WechatNewsModel::get($id)) return Json::fail('编辑的记录不存在!');
        $res = WechatNewsModel::edit($data, $id);
        if (!$res) return Json::fail('修改失败');
        return Json::successful('修改成功!');
    }

    public function delete($id)
    {
        $res = WechatNewsModel::edit(['hide' => 1], $id);
        if (!$res) return Json::fail('删除失败,请稍候再试!');
        else return Json::successful('删除成功!');
    }
}

