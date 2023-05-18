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

namespace app\admin\controller\setting;

use app\admin\controller\AuthController;
use service\FormBuilder as Form;
use app\admin\model\system\SystemMessage as MessageModel;
use app\admin\model\wechat\WechatTemplate as WechatTemplateModel;
use service\JsonService;
use service\WechatTemplateService;
use service\SystemConfigService;
use think\Request;
use think\Url;

/**
 * 消息通知 控制器
 * Class SystemMessage
 * @package app\admin\controller\setting
 */
class SystemMessage extends AuthController
{
    /**
     * 消息通知展示
     * @return
     * */
    public function index()
    {
        $data = [];
        if (SystemConfigService::get('wechat_appid') && SystemConfigService::get('wechat_appsecret')) {
            try {
                $data = WechatTemplateService::getIndustry();
                $data = count($data) > 0 ? $data->toArray() : [];
            } catch (\Exception $e) {
            }
        }
        $this->assign('data', $data);
        return $this->fetch();
    }

    /**
     * 同步微信模版消息
     */
    public function synchronousWechatTemplate()
    {
        if (!SystemConfigService::get('wechat_appid') || !SystemConfigService::get('wechat_appsecret')) return JsonService::fail('请先配置公众号');
        try {
            $data = WechatTemplateService::getIndustry();
            $data = count($data) > 0 ? $data->toArray() : [];
        } catch (\Exception $e) {
            return JsonService::fail($e->getMessage());
        }
        if ($data['primary_industry']['first_class'] == 'IT科技' && $data['primary_industry']['second_class'] == '互联网|电子商务' &&
            $data['secondary_industry']['first_class'] == '其它' && $data['secondary_industry']['second_class'] == '其它') {
            $this->set_template();
        } else {
            try {
                WechatTemplateService::setIndustry(1, 41);
            } catch (\Exception $e) {
                return JsonService::fail($e->getMessage());
            }
            $this->set_template();
        }
        return JsonService::successful('同步完成');
    }

    /**模版消息同步
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function set_template()
    {
        $list = WechatTemplateModel::where('status', 1)->select();
        foreach ($list as $key => $value) {
            try {
                $res = WechatTemplateService::addTemplate($value['tempkey']);
            } catch (\Exception $e) {
                continue;
            }
            if ($res['errcode'] == 0 && $res['errmsg'] == 'ok' && $res['template_id']) {
                $data['tempid'] = $res['template_id'];
                WechatTemplateModel::edit($data, $value['tempkey'], 'tempkey');
            }
        }
        return JsonService::successful('模版消息同步完成');
    }

    /**
     * 消息通知获取
     * @return
     * */
    public function system_message_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['title', '']
        ]);
        return JsonService::successlayui(MessageModel::systemMessageList($where));
    }

    /**添加/编辑
     * @return mixed|void
     * @throws \think\exception\DbException
     */
    public function create()
    {
        return $this->fetch();
    }

    /**
     * 添加和修改讲师
     * @param int $id 修改
     * @return JsonService
     * */
    public function save_message()
    {
        $data = parent::postMore([
            ['name', ''],
            ['template_const', ''],
            ['tempkey', ''],
            ['temp_id', ''],
            ['sms_content', ''],
            ['is_wechat', 1],
            ['is_sms', 1],
        ]);
        if (!$data['name']) return JsonService::fail('请输入消息名称');
        if (!$data['template_const']) return JsonService::fail('请输入模版常数');
        if (!$data['tempkey']) return JsonService::fail('请编辑模板编号');
        $data['add_time'] = time();
        if (!MessageModel::be(['name' => $data['name']])) {
            $res = MessageModel::set($data);
        } else {
            return JsonService::fail('消息已存在');
        }
        if ($res)
            return JsonService::successful('添加成功');
        else
            return JsonService::fail('添加失败');
    }

    /**
     * 编辑模板消息
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function edit($tempkey)
    {
        if (!$tempkey) return $this->failed('数据不存在');
        $template = WechatTemplateModel::where('tempkey', $tempkey)->find();
        $status = MessageModel::where('tempkey', $tempkey)->value('is_wechat');
        $f = array();
        $f[] = Form::input('tempkey', '模板编号', $template->getData('tempkey'))->disabled(1);
        $f[] = Form::input('name', '模板名', $template->getData('name'))->disabled(1);
        $f[] = Form::input('content', '内容', $template->getData('content'))->type('textarea')->disabled(1);
        $f[] = Form::input('tempid', '模板ID', $template->getData('tempid'));
        $f[] = Form::radio('status', '状态', $status)->options([['label' => '开启', 'value' => 1], ['label' => '关闭', 'value' => 0]]);
        $form = Form::make_post_form('编辑模板消息', $f, Url::build('update', compact('tempkey')), 2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**微信模版保存
     * @param Request $request
     * @param $tempkey
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function update(Request $request, $tempkey)
    {
        $data = parent::postMore([
            'tempid',
            ['status', 0]
        ], $request);
        if ($data['tempid'] == '') return JsonService::fail('请输入模板ID');
        if (!$tempkey) return JsonService::fail('数据不存在');
        $template = WechatTemplateModel::where('tempkey', $tempkey)->find();
        if (!$template) return JsonService::fail('数据不存在!');
        WechatTemplateModel::edit($data, $tempkey, 'tempkey');
        MessageModel::where('tempkey', $tempkey)->update(['is_wechat' => $data['status']]);
        return JsonService::successful('修改成功!');
    }

    /**
     * 编辑短信模板消息
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function sms($id)
    {
        if (!$id) return $this->failed('数据不存在');
        $template = MessageModel::where('id', $id)->find();
        $f = array();
        $f[] = Form::input('temp_id', '模板编号', $template->getData('temp_id'))->disabled(1);
        $f[] = Form::input('name', '模板名', $template->getData('name'))->disabled(1);
        $f[] = Form::input('sms_content', '内容', $template->getData('sms_content'))->type('textarea')->disabled(1);
        $f[] = Form::radio('is_sms', '状态', $template->getData('is_sms'))->options([['label' => '开启', 'value' => 1], ['label' => '关闭', 'value' => 0]]);
        $form = Form::make_post_form('编辑短信模板消息', $f, Url::build('sms_update', compact('id')), 2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**短信状态保存
     * @param Request $request
     * @param $id
     */
    public function sms_update(Request $request, $id)
    {
        $data = parent::postMore([
            ['is_sms', 0]
        ], $request);
        if (!$id) return JsonService::fail('数据不存在');
        MessageModel::edit($data, $id);
        return JsonService::successful('修改成功!');
    }

    /**
     * 快速编辑
     * @param string $field 字段名
     * @param int $id 修改的主键
     * @param string value 修改后的值
     * @return JsonService
     */
    public function set_value($field = '', $id = '', $value = '')
    {
        ($field == '' || $id == '' || $value == '') && JsonService::fail('缺少参数');
        $message = MessageModel::where('id', $id)->find();
        if ($field == 'is_wechat' && $value == 1 && $message['tempkey'] == '') {
            return JsonService::fail('微信模板编号不能为空');
        }
        if ($field == 'is_sms' && $value == 1 && $message['temp_id'] == '') {
            return JsonService::fail('短信模板ID不能为空');
        }
        $res = MessageModel::where('id', $id)->update([$field => $value]);
        if ($res)
            return JsonService::successful('保存成功');
        else
            return JsonService::fail('保存失败');
    }

    /**
     * 删除讲师
     * @param int $id 修改的主键
     * @return json
     * */
    public function delete($id = 0)
    {
        if (!$id) return JsonService::fail('缺少参数');
        if (MessageModel::del($id))
            return JsonService::successful('删除成功');
        else
            return JsonService::fail(MessageModel::getErrorInfo('删除失败'));
    }
}
