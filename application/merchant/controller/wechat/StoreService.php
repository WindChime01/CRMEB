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

namespace app\merchant\controller\wechat;

use app\merchant\controller\AuthController;
use app\merchant\controller\download\DataDownload;
use app\merchant\model\merchant\Merchant;
use app\admin\model\user\User;
use service\FormBuilder as Form;
use service\JsonService as Json;
use service\UploadService as Upload;
use service\SystemConfigService;
use think\Request;
use think\Url;
use app\merchant\model\wechat\StoreService as ServiceModel;
use app\merchant\model\wechat\StoreServiceLog as StoreServiceLog;
use app\admin\model\wechat\WechatUser as UserModel;

/**
 * 客服管理
 * Class StoreService
 * @package app\merchant\controller\wechat
 */
class StoreService extends AuthController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->fetch();
    }

    public function get_store_service_list()
    {
        $where = parent::getMore([
            ['status', ''],
            ['page', 1],
            ['limit', 20],
            ['title', '']
        ]);
        $where['mer_id'] = $this->merchantId;
        return Json::successlayui(ServiceModel::getList($where));
    }

    /**客服配置
     * @return mixed
     */
    public function service()
    {
        $merchant = Merchant::where('id', $this->merchantId)->field('id,is_phone_service,service_phone')->find();
        $configuration = SystemConfigService::get('customer_service_configuration');
        $this->assign(['merchat' => json_encode($merchant),'configuration'=>$configuration]);
        return $this->fetch();
    }

    /**保存客服电话
     * @param $id
     * @return void
     */
    public function save_phone($id)
    {
        $data = parent::postMore([
            ['is_phone_service', 0],
            ['service_phone', '']
        ]);
        if (!$id) return Json::fail('缺少参数');
        $res = Merchant::edit($data, $id);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
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
        if ($field == 'sort' && bcsub($value, 0, 0) < 0) return Json::fail('排序不能为负数');
        $res = ServiceModel::where('id', $id)->update([$field => $value]);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return $this->fetch();
    }

    /**
     * 获取用户列表
     */
    public function user_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['nickname', ''],
            ['identitys', 1],
            ['order', '']
        ]);
        return Json::successlayui(User::add_teacher_user_list($where));
    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save()
    {
        $data = parent::postMore([
            ['uid', 0]
        ]);
        if (!$data['uid']) return Json::fail('请选择要添加的用户!');
        if (ServiceModel::where(['mer_id' => $this->merchantId, 'uid' => $data['uid']])->count()) return Json::fail('添加用户中存在已有的客服!');
        $now_user = User::where('uid', $data['uid'])->field('uid,avatar,nickname,is_h5user')->find();
        $data['mer_id'] = $this->merchantId;
        $data["avatar"] = $now_user["avatar"];
        $data["nickname"] = $now_user["nickname"];
        $data["is_h5user"] = $now_user["is_h5user"] > 0 ? 1 : 0;
        if (!$data["is_h5user"]) {
            $data["subscribe"] = UserModel::where('uid', $data['uid'])->value('subscribe');
        }
        $data["add_time"] = time();
        $res = ServiceModel::set($data);
        if ($res) {
            return Json::successful('添加成功!');
        } else {
            return Json::fail('添加失败!');
        }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $service = ServiceModel::get($id);
        if (!$service) return Json::fail('数据不存在!');
        $f = array();
        $f[] = Form::frameImageOne('avatar', '客服头像', Url::build('admin/widget.images/index', array('fodder' => 'avatar')), $service['avatar'])->icon('image')->width('100%')->height('500px')->spin(0);
        $f[] = Form::input('nickname', '客服名称', $service["nickname"]);
        $f[] = Form::input('kefu_id', '客服ID', $service["kefu_id"]);
        $f[] = Form::radio('notify', '订单通知', $service['notify'])->options([['value' => 1, 'label' => '开启'], ['value' => 0, 'label' => '关闭']]);
        $f[] = Form::radio('status', '客服状态', $service['status'])->options([['value' => 1, 'label' => '显示'], ['value' => 0, 'label' => '隐藏']]);
        $form = Form::make_post_form('修改数据', $f, Url::build('update', compact('id')), 2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $params = $request->post();
        if (empty($params["nickname"])) return Json::fail("客服名称不能为空！");
        $data = array("avatar" => $params["avatar"]
        , "nickname" => $params["nickname"]
        , 'kefu_id' => $params['kefu_id']
        , 'status' => $params['status']
        , 'notify' => $params['notify']
        );
        ServiceModel::edit($data, $id);
        return Json::successful('修改成功!');
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (!ServiceModel::del($id))
            return Json::fail(ServiceModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = Upload::image('file', 'store/service');
        $thumbPath = Upload::thumb($res->dir);
        if ($res->status == 200)
            return Json::successful('图片上传成功!', ['name' => $res->fileInfo->getSaveName(), 'url' => Upload::pathToUrl($thumbPath)]);
        else
            return Json::fail($res->error);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function chat_user($id)
    {
        $now_service = ServiceModel::get($id);
        if (!$now_service) return Json::fail('数据不存在!');
        $this->assign(compact('now_service'));
        return $this->fetch();
    }

    public function chat_user_list()
    {
        $data = parent::getMore([
            ['uid', 0],
            ['mer_id', 0],
            ['page', 1],
            ['limit', 20],
        ]);
        $data = ServiceModel::getChatUser($data['uid'], $data['mer_id'], $data['page'], $data['limit']);
        return Json::successlayui($data);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function chat_list($uid, $to_uid)
    {
        $this->assign(['uid' => $uid, 'to_uid' => $to_uid]);
        return $this->fetch();
    }

    public function get_chat_list()
    {
        $data = parent::getMore([
            ['uid', 0],
            ['to_uid', 0],
            ['mer_id', 0],
            ['page', 1],
            ['limit', 20],
        ]);
        $data = StoreServiceLog::getChatList($data['uid'], $data['to_uid'], $data['mer_id'], $data['page'], $data['limit']);
        return Json::successlayui($data);
    }
}
