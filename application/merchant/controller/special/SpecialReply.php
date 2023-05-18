<?php

namespace app\merchant\controller\special;

use app\merchant\controller\AuthController;
use traits\CurdControllerTrait;
use service\JsonService as Json;
use think\Request;
use app\merchant\model\special\SpecialReply as SpecialReplyModel;
use app\merchant\model\special\Special;
use think\Url;

/**
 * 评论管理 控制器
 * Class SpecialReply
 * @package app\merchant\controller\special
 */
class SpecialReply extends AuthController
{

    use CurdControllerTrait;

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index($special_id = 0)
    {
        $this->assign('special_id', $special_id);
        return $this->fetch();
    }

    /**
     * 评论列表
     */
    public function getSpecialReplyList()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['special_name', ''],
            ['is_reply', ''],
            ['special_id', 0],
            ['title', ''],
            ['comment', '']
        ], $this->request);
        $where['mer_id'] = $this->merchantId;
        return Json::successlayui(SpecialReplyModel::specialReplyList($where));
    }

    /**
     * @param $id
     * @return \think\response\Json|void
     */
    public function delete($id)
    {
        if (!$id) return $this->failed('数据不存在');
        $data['is_del'] = 1;
        if (!SpecialReplyModel::edit($data, $id))
            return Json::fail(SpecialReplyModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    /**评论回复
     * @param Request $request
     */
    public function set_reply(Request $request)
    {
        $data = parent::postMore([
            'id',
            'content',
        ], $request);
        if (!$data['id']) return Json::fail('参数错误');
        if ($data['content'] == '') return Json::fail('请输入回复内容');
        $save['merchant_reply_content'] = $data['content'];
        $save['merchant_reply_time'] = time();
        $save['is_reply'] = 1;
        $res = SpecialReplyModel::edit($save, $data['id']);
        if (!$res)
            return Json::fail(SpecialReplyModel::getErrorInfo('回复失败,请稍候再试!'));
        else
            return Json::successful('回复成功!');
    }

    /**回复加精
     * @param int $id
     */
    public function refining_reply($id = 0)
    {
        if (!$id) return Json::fail('参数错误');
        $save['is_selected'] = 1;
        $res = SpecialReplyModel::edit($save, $id);
        if (!$res)
            return Json::fail(SpecialReplyModel::getErrorInfo('加精失败,请稍候再试!'));
        else
            return Json::successful('加精成功!');
    }

    /**
     * 创建虚拟评论
     *
     * */
    public function create_false()
    {
        $this->assign('list', Special::PreWhere()->field('id,title')->select());
        return $this->fetch();
    }

    /**
     * 提交虚拟评论
     */
    public function save_false()
    {
        $data = parent::postMore([
            ['nickname', 0],
            ['avatar', ''],
            ['special_id', 0],
            ['satisfied_score', 1],
            ['comment', ''],
            ['pics', []]
        ]);
        $data['type'] = 0;
        $special_id = $data['special_id'];
        $banner = [];
        foreach ($data['pics'] as $item) {
            $banner[] = $item['pic'];
        }
        if (!$data['nickname']) return Json::fail('请输入昵称');
        if (!$data['avatar']) return Json::fail('请上传头像');
        if (!$data['special_id']) return Json::fail('请选择专题');
        if (!$data['comment']) return Json::fail('请编辑评论内容');
        $res = SpecialReplyModel::helpeFalse($data, $banner);
        if ($res === false)
            return Json::fail(SpecialReplyModel::getErrorInfo());
        else {
            SpecialReplyModel::uodateScore($special_id);
            return Json::successful('虚拟评论成功');
        }
    }

    public function specialList()
    {
        $list = Special::PreWhere()->where('mer_id', $this->merchantId)->where('type', 'not in', 4)->field('id,title')->select();
        return Json::successful($list);
    }

    /**
     * 快速编辑
     * @param string $field 字段名
     * @param int $id 修改的主键
     * @param string value 修改后的值
     * @return json
     */
    public function set_value($field = '', $id = '', $value = '')
    {
        if (!$field || !$id || $value == '') Json::fail('缺少参数3');

        $res = SpecialReplyModel::where('id', $id)->update([$field => $value]);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

}
