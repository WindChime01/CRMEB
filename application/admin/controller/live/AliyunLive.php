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

namespace app\admin\controller\live;

use app\admin\controller\AuthController;
use app\admin\model\live\LiveGoods;
use app\admin\model\live\LiveGift;
use app\admin\model\live\LiveReward;
use app\admin\model\order\StoreOrder as StoreOrderModel;
use app\admin\model\live\LiveBarrage;
use app\admin\model\live\LiveHonouredGuest;
use app\admin\model\live\LiveStudio;
use app\admin\model\live\LiveUser;
use app\admin\model\live\LiveAudit;
use app\admin\model\live\LivePlayback;
use app\admin\model\special\Special as SpecialModel;
use app\admin\model\special\Special;
use app\admin\model\special\SpecialCourse;
use app\admin\model\special\SpecialSubject;
use app\admin\model\special\SpecialTask;
use app\admin\model\system\SystemAdmin;
use app\admin\model\merchant\Merchant;
use app\admin\model\user\User;
use app\wap\model\user\WechatUser;
use service\JsonService as Json;
use service\SystemConfigService;
use service\WechatTemplateService;
use think\Exception;
use service\FormBuilder as Form;
use Api\AliyunLive as ApiAliyunLive;
use think\Session;
use think\Url;
use \GatewayWorker\Lib\Gateway;
use app\admin\model\system\SystemRole as RoleModel;
use app\wap\model\routine\RoutineTemplate;
use app\wap\model\wap\SmsTemplate;
use app\index\controller\PushJob;

/**直播
 * Class AliyunLive
 * @package app\admin\controller\live
 */
class AliyunLive extends AuthController
{
    /**
     * 阿里云直播句柄
     * @var \Api\AliyunLive
     */
    protected $aliyunLive;

    protected function _initialize()
    {
        parent::_initialize();
        $this->aliyunLive = \Api\AliyunLive::instance([
            'AccessKey' => SystemConfigService::get('accessKeyId'),
            'AccessKeySecret' => SystemConfigService::get('accessKeySecret'),
            'OssEndpoint' => SystemConfigService::get('aliyun_live_end_point'),
            'OssBucket' => SystemConfigService::get('aliyun_live_oss_bucket'),
            'appName' => SystemConfigService::get('aliyun_live_appName'),
            'payKey' => SystemConfigService::get('aliyun_live_play_key'),
            'key' => SystemConfigService::get('aliyun_live_push_key'),
            'playLike' => SystemConfigService::get('aliyun_live_playLike'),
            'rtmpLink' => SystemConfigService::get('aliyun_live_rtmpLink'),
        ]);
    }

    /**
     * 直播间管理
     * */
    public function index()
    {
        $this->assign([
            'special_list' => SpecialModel::where('type', SPECIAL_LIVE)->where(['is_del' => 0])->field(['id', 'title'])->select(),
            'mer_list' => Merchant::getMerchantList(),
            'type' => $this->request->param('type', 1),
        ]);
        return $this->fetch();
    }

    /**
     * 快速设置直播的某个字段的值
     * @param string $field 键值
     * @param string $value 值
     * @param int $id 直播id
     * @return json
     * */
    public function set_live_value($field = '', $value = '', $id = 0)
    {
        if (!$field || !$id) return Json::fail('缺少参数');
        $value = str_replace('免密', '', $value);
        $res = parent::getDataModification('studio', $id, $field, $value);
        if ($res)
            return Json::successful('修改成功');
        else
            return Json::fail('修改失败');
    }

    /**
     * 编辑直播间
     * */
    public function update_live($id = 0)
    {
        if (!$id) return $this->failed('缺少参数');
        $liveInfo = LiveStudio::get($id);
        if (!$liveInfo) return $this->failed('未查到直播间信息');
        $f[] = Form::input('stream_name', '直播间号:', $liveInfo->getData('stream_name'))->disabled(true);
        $f[] = Form::input('live_title', '直播间标题:', $liveInfo->getData('live_title'));
        $f[] = Form::frameImageOne('live_image', '直播封面:', Url::build('admin/widget.images/index', array('fodder' => 'live_image')), $liveInfo->getData('live_image'))->icon('image')->width('100%')->height('500px');
        $f[] = Form::textarea('live_introduction', '直播间简介:', $liveInfo->getData('live_introduction'));
        $f[] = Form::number('online_num', '虚拟在线人数:', $liveInfo->getData('online_num'))->min(0);
        $f[] = Form::number('sort', '排序:', $liveInfo->getData('sort'))->min(0);
        $f[] = Form::radio('is_remind', '开播提醒:', $liveInfo->getData('is_remind'))->options([
            ['value' => 1, 'label' => '是'],
            ['value' => 0, 'label' => '否']
        ]);
        $f[] = Form::number('remind_time', '开播前提醒时间(分):', $liveInfo->getData('remind_time'));
        $f[] = Form::radio('is_recording', '自动录制:', $liveInfo->getData('is_recording'))->options([
            ['value' => 1, 'label' => '是'],
            ['value' => 0, 'label' => '否']
        ]);
        $f[] = Form::radio('is_playback', '直播回放:', $liveInfo->getData('is_playback'))->options([
            ['value' => 1, 'label' => '是'],
            ['value' => 0, 'label' => '否']
        ]);
        $f[] = Form::radio('is_chat', '直播间聊天:', $liveInfo->getData('is_chat'))->options([
            ['value' => 1, 'label' => '开启'],
            ['value' => 0, 'label' => '关闭']
        ]);
        $f[] = Form::radio('is_class_chat', '直播间课堂:', $liveInfo->getData('is_class_chat'))->options([
            ['value' => 1, 'label' => '开启'],
            ['value' => 0, 'label' => '关闭']
        ]);
        $f[] = Form::radio('is_live_recommend', '直播间推荐:', $liveInfo->getData('is_live_recommend'))->options([
            ['value' => 1, 'label' => '开启'],
            ['value' => 0, 'label' => '关闭']
        ]);
        $f[] = Form::radio('is_ranking', '直播间排行:', $liveInfo->getData('is_ranking'))->options([
            ['value' => 1, 'label' => '开启'],
            ['value' => 0, 'label' => '关闭']
        ]);
        $form = Form::make_post_form('修改直播间', $f, Url::build('save_live', array('id' => $id)), 2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 修改直播间
     * @param int $id
     * @return json
     * */
    public function save_live($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        $data = parent::postMore([
            ['live_title', ''],
            ['live_image', ''],
            ['live_introduction', ''],
            ['online_num', 0],
            ['sort', 0],
            ['is_remind', 0],
            ['remind_time', 0],
            ['is_recording', 0],
            ['is_playback', 0],
            ['is_chat', 0],
            ['is_class_chat', 0],
            ['is_live_recommend', 0],
            ['is_ranking', 0]
        ]);
        if (!$data['live_title']) return Json::fail('直播间标题不能为空');
        if (!$data['live_introduction']) return Json::fail('直播间简介不能为空');
        if ($data['is_remind'] && !$data['remind_time']) return Json::fail('请输入直播前提醒时间！');
        if (LiveStudio::edit($data, $id))
            return Json::successful('修改成功');
        else
            return Json::fail('修改失败或者您没有修改内容');
    }

    /**
     * 直播间用户管理
     * @param int $id
     * */
    public function live_user($id = 0)
    {
        if (!$id) return $this->failed('缺少参数');
        $liveInfo = LiveStudio::get($id);
        if (!$liveInfo) return $this->failed('未查到直播间');
        $this->assign([
            'stream_name' => $liveInfo->stream_name,
            'live_id' => $id
        ]);
        return $this->fetch();
    }

    /**
     * 获取直播间用户列表
     * */
    public function get_live_user_list()
    {
        $where = parent::getMore([
            ['live_id', 0],
            ['page', 0],
            ['limit', 20],
            ['nickname', ''],
            ['start_time', ''],
            ['end_time', ''],
        ]);
        if (!$where['live_id']) return Json::fail('缺少参数');
        return Json::successlayui(LiveUser::getLiveUserList($where));
    }

    /**
     * 设置直播用户
     * @param string $field
     * @param string $id
     * @param string $value
     */
    public function set_live_user_value($field = '', $id = '', $value = '')
    {
        if (!$field || !$id) return Json::fail('缺少参数');
        $res = parent::getDataModification('live_user', $id, $field, $value);
        if ($res)
            return Json::successful('修改成功');
        else
            return Json::fail('修改失败');
    }

    /**礼物
     * @param string $field
     * @param string $id
     * @param string $value
     */
    public function set_live_gift_value($field = '', $id = '', $value = '')
    {
        if (!$field || !$id) return Json::fail('缺少参数');
        $res = parent::getDataModification('live_gift', $id, $field, $value);
        if ($res)
            return Json::successful('修改成功');
        else
            return Json::fail('修改失败');
    }

    /**
     * 禁止发言
     */
    public function live_no_speaking($id)
    {
        if (!$id) return $this->failed('缺少参数');
        $liveInfo = LiveUser::get($id);
        if (!$liveInfo) return $this->failed('未查到直播间信息');
        $f[] = Form::radio('is_ban', '是否禁言', $liveInfo->getData('is_ban'))->options([
            ['value' => 1, 'label' => '是'],
            ['value' => 0, 'label' => '否'],
        ]);
        $f[] = Form::number('ban_time', '禁言时间:分');
        $form = Form::make_post_form('禁止发言', $f, Url::build('save_no_speaking', array('id' => $id)), 2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function save_no_speaking($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        $data = parent::postMore([
            ['is_ban', 0],
            ['ban_time', 0]
        ]);
        if ($data['is_ban']) {
            $workerman = \think\Config::get('workerman.channel', []);
            Gateway::$registerAddress = $workerman['ip'] . ':' . $workerman['port'];
            $uid = LiveUser::where('id', $id)->value('uid');
            if (Gateway::isUidOnline($uid)) {
                Gateway::sendToUid($uid, json_encode([
                    'type' => 'ban',
                    'value' => 1
                ]));
            }
        }
        if ($data['is_ban'] && $data['ban_time'] <= 0) return Json::fail('请输入禁言时间');
        $data['ban_time'] = bcadd(time(), bcmul($data['ban_time'], 60, 0), 0);
        if (LiveUser::edit($data, $id)) {
            return Json::successful('修改成功');
        } else {
            return Json::fail('修改失败');
        }
    }

    /**
     * 禁止进入
     */
    public function live_no_entry($id)
    {
        if (!$id) return $this->failed('缺少参数');
        $liveInfo = LiveUser::get($id);
        if (!$liveInfo) return $this->failed('未查到直播间信息');
        $f[] = Form::radio('is_open_ben', '是否禁止进入直播间', $liveInfo->getData('is_open_ben'))->options([
            ['value' => 1, 'label' => '是'],
            ['value' => 0, 'label' => '否'],
        ]);
        $f[] = Form::number('open_ben_time', '禁止时间:分');
        $form = Form::make_post_form('禁止进入', $f, Url::build('save_no_entry', array('id' => $id)), 2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function save_no_entry($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        $data = parent::postMore([
            ['is_open_ben', 0],
            ['open_ben_time', 0]
        ]);
        if ($data['is_open_ben'] && $data['open_ben_time'] <= 0) return Json::fail('请输入禁止时间');
        $data['open_ben_time'] = bcadd(time(), bcmul($data['open_ben_time'], 60, 0), 0);
        if (LiveUser::edit($data, $id)) {
            return Json::successful('修改成功');
        } else {
            return Json::fail('修改失败');
        }
    }

    /**
     * 直播间管理
     */
    public function get_live_list()
    {
        $where = parent::getMore([
            ['stream_name', ''],
            ['start_time', ''],
            ['end_time', ''],
            ['page', 1],
            ['limit', 20],
            ['special_id', 0],
        ]);
        return Json::successlayui(LiveStudio::getLiveList($where));
    }


    /**
     * 直播专题列表
     * @param int $subject_id
     * @return mixed
     */
    public function special_live()
    {
        $special_type = $this->request->param("special_type");
        if (!$special_type || !is_numeric($special_type)) return $this->failed('专题类型参数缺失');
        $subjectlist = SpecialSubject::specialCategoryAll();
        $this->assign([
            'type' => 1,
            'special_type' => $special_type,
            'special_title' => SPECIAL_TYPE[$special_type],
            'subject_list' => $subjectlist,
            'mer_list' => Merchant::getMerchantList(),
            'is_live' => 1,
        ]);
        return $this->fetch();
    }

    /**
     * 获取直播推流地址
     * @param int $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function go_live($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        $specialInfo = SpecialModel::get($id);
        if (!$specialInfo) return Json::fail('直播专题暂未查到');
        if ($specialInfo->type != SPECIAL_LIVE) return Json::fail('此专题不授予直播专题，无法获取推流地址！');
        $liveInfo = LiveStudio::where('special_id', $specialInfo->id)->find();
        if (!$liveInfo) return Json::fail('没有查到此直播间');
        if ($liveInfo->is_del) return Json::fail('直播间已删除无法推流');
        $url = $this->aliyunLive->getPushSteam($liveInfo->stream_name);
        return Json::successful($url);
    }

    /**
     * 直播消息提醒
     * @param int $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function send_remind($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        $specialInfo = SpecialModel::get($id);
        if (!$specialInfo) return Json::fail('直播专题暂未查到，无法进行提醒');
        $liveInfo = LiveStudio::where('special_id', $specialInfo->id)->find();
        if (!$liveInfo) return Json::fail('没有查到此直播间，无法提醒');
        if ($liveInfo->is_del) return Json::fail('直播间已删除无法提醒');
        $liveInfo = $liveInfo->toArray();
        if ($liveInfo['is_reminded'] == 0) {
            LiveStudio::where('id', $liveInfo['id'])->update(['is_reminded' => 1]);
        }
        if ($specialInfo['pay_type'] == 1 && $specialInfo['member_pay_type'] == 1) {
            $orderList = StoreOrderModel::where(['cart_id' => $id, 'type' => 0])->column("uid");
        } elseif ($specialInfo['pay_type'] == 1 && $specialInfo['member_pay_type'] == 0) {
            $order = StoreOrderModel::where(['cart_id' => $id, 'type' => 0])->column("uid");
            $user = User::where('is_h5user', 0)->where('level', 1)->column("uid");
            $orderList = array_merge($order, $user);
        } else {
            $orderList = User::where('is_h5user', 0)->column("uid");
        }
        if (!$orderList) {
            return Json::fail('暂无合适用户，无需提醒');
        }
        $site_url = SystemConfigService::get('site_url');
        $orderList = array_unique($orderList);
        $data['id'] = $id;
        $data['site_url'] = $site_url;
        $data['live_title'] = $specialInfo['title'];
        $data['start_play_time'] = $liveInfo['start_play_time'];
        foreach ($orderList as $k => $v) {
            $data['uid'] = $v;
            PushJob::actionWithDoPinkJob($data, 'doLiveStudioJob');
        }
        return Json::successful("发送成功");
    }

    /**直播审核
     * @return mixed
     */
    public function live_audit()
    {
        return $this->fetch();
    }

    /**
     * 审核列表
     */
    public function get_live_audit_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['store_name', ''],
        ]);
        return Json::successlayui(LiveAudit::liveExamineList($where));
    }

    /**不通过
     * @param $id
     * @throws \think\exception\DbException
     */
    public function fail($id)
    {
        $fail_msg = parent::postMore([
            ['message', ''],
        ]);
        if (!LiveAudit::be(['id' => $id, 'status' => 0])) return Json::fail('操作记录不存在或状态错误!');
        $audit = LiveAudit::get($id);
        if (!$audit) return Json::fail('操作记录不存!');
        if ($audit->status != 0) return Json::fail('您已审核,请勿重复操作');
        LiveAudit::beginTrans();
        $res = LiveAudit::changeFail($id, $audit['mer_id'], $fail_msg['message']);
        if ($res) {
            LiveAudit::commitTrans();
            return Json::successful('操作成功!');
        } else {
            LiveAudit::rollbackTrans();
            return Json::fail('操作失败!');
        }
    }

    /**通过
     * @param $id
     * @throws \think\exception\DbException
     */
    public function succ($id)
    {
        if (!LiveAudit::be(['id' => $id, 'status' => 0])) return Json::fail('操作记录不存在或状态错误!');
        $audit = LiveAudit::get($id);
        if (!$audit) return Json::fail('操作记录不存!');
        if ($audit->status != 0) return Json::fail('您已审核,请勿重复操作');
        LiveAudit::beginTrans();
        $res = LiveAudit::changeSuccess($id, $audit['mer_id']);
        if ($res) {
            LiveAudit::commitTrans();
            return Json::successful('操作成功!');
        } else {
            LiveAudit::rollbackTrans();
            return Json::fail('操作失败!');
        }
    }

    /**
     * 获取专题直播列表
     */
    public function special_list()
    {
        $where = parent::getMore([
            ['subject_id', 0],
            ['mer_id', 0],
            ['page', 1],
            ['limit', 20],
            ['store_name', ''],
            ['title', ''],
            ['start_time', ''],
            ['end_time', ''],
            ['order', ''],
            ['is_show', ''],
            ['special_type', 4],
        ]);
        if (isset($where['special_type'])) {
            $where['type'] = $where['special_type'];
        }
        $special_list = SpecialModel::getSpecialList($where);
        return Json::successlayui($special_list);
    }

    /**
     * 删除专题和直播间
     * @param int $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delete($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        $specialInfo = SpecialModel::get($id);
        if (!$specialInfo) return Json::fail('查找的专题不存在');
        if ($specialInfo->is_del) return Json::fail('专题已经删除');
        $liveInfo = LiveStudio::where(['special_id' => $id])->find();
        if (!$liveInfo) return Json::fail('删除的专题直播不存在');
        if ($liveInfo->is_del) return Json::fail('直播间已删除');
        $specialInfo->is_del = 1;
        $liveInfo->is_del = 1;
        if ($specialInfo->save() && $liveInfo->save()) {
            return Json::successful('删除成功');
        } else {
            return Json::fail('删除失败');
        }

    }

    public function recommend($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        $specialInfo = SpecialModel::get($id);
        if (!$specialInfo) return Json::fail('专题不存在');
        Session::set('live_one_id', $id, 'wap');
        return Json::successful('推荐成功');
    }

    /**
     * 嘉宾列表
     * @param $live_id
     * @return mixed
     */
    public function guest_list($live_id = 0)
    {
        $this->assign('live_id', $live_id);
        return $this->fetch();
    }

    /**
     * 添加嘉宾
     * @param int $live_id
     * @return mixed|void
     * @throws \FormBuilder\exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function user($live_id = 0)
    {
        $this->assign('live_id', $live_id);
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
            ['status', ''],
            ['pay_count', ''],
            ['is_promoter', ''],
            ['order', ''],
            ['data', ''],
            ['country', ''],
            ['province', ''],
            ['city', ''],
            ['user_time_type', ''],
            ['user_time', ''],
            ['sex', ''],
        ]);
        return Json::successlayui(User::add_get_user_list($where));
    }

    /**
     * 添加嘉宾
     * @param int $live_id
     */
    public function save_guest($uid = 0, $live_id = 0, $type = 0)
    {
        if (!$live_id) return Json::fail('缺少直播间id');
        if (!$uid) return Json::fail('请选择用户');
        if ($type === '') return Json::fail('请选择类型');
        if ($type && LiveHonouredGuest::be(['live_id' => $live_id, 'type' => 1])) return Json::fail('讲师只能添加一个');
        $data['uid'] = $uid;
        $data['type'] = $type;
        $data['live_id'] = $live_id;
        $data['nickname'] = User::where('uid', $uid)->value('nickname');
        $data['add_time'] = time();
        if (LiveHonouredGuest::set($data))
            return Json::successful('添加成功');
        else
            return Json::fail('添加失败');
    }

    /**
     * 获取嘉宾列表
     */
    public function get_guest_list()
    {
        $where = parent::getMore([
            ['live_id', 0],
            ['nickname', ''],
            ['page', 1],
            ['limit', 20],
        ]);
        return Json::successlayui(LiveHonouredGuest::getGuestList($where));
    }

    /**
     * 删除嘉宾
     * @param int $id
     */
    public function del_guest($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        if (LiveHonouredGuest::del($id))
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }


    /**
     * 回放设置和下载列表
     * @param int $special_id
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function download_list($special_id = 0)
    {
        if (!$special_id) return $this->failed('缺少参数');
        $specialInfo = SpecialModel::get($special_id);
        if (!$specialInfo) return $this->failed('查找的专题不存在');
        if ($specialInfo->is_del) return $this->failed('专题已经删除');
        $liveInfo = LiveStudio::where(['special_id' => $special_id])->find();
        if (!$liveInfo) return $this->failed('直播不存在！');
        $livePlayBack = LivePlayback::where(['stream_name' => $liveInfo->stream_name, 'is_record' => 1])->find();
        $this->assign([
            'stream_name' => $liveInfo->stream_name,
            'record_id' => $livePlayBack ? $livePlayBack->RecordId : 0,
            'is_playback' => 1
        ]);
        return $this->fetch();
    }

    /**
     * 获取直播间下的录制内容
     * @param string $record_id
     * @param string $stream_name
     * @param string $start_time
     * @param string $end_time
     * @param int $page
     * @param int $limit
     */
    public function get_live_record()
    {
        $start_time = $this->request->param('syn_start_time');
        $end_time = $this->request->param('syn_end_time');
        $stream_name = $this->request->param('stream_name');
        $record_id = $this->request->param('record_id');
        if (!$stream_name) return Json::fail('缺少直播间id');
        $aliyunLive = $this->aliyunLive;
        $beginToday = mktime(0, 0, 0, date('m'), date('d') - 3, date('Y'));
        if ($start_time && $end_time) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            if ($start_time > $end_time) return Json::fail('开始时间不能大于结束时间');
            $time = bcsub($end_time, $start_time, 0) / 86400;
            if ($time > 4) return Json::fail('开始和结束的时间不能间隔4天');
        }
        try {
            $res = $aliyunLive->queryLiveRecordFiles(
                $stream_name,
                $start_time ? ApiAliyunLive::setTimeFormat($start_time) : ApiAliyunLive::setTimeFormat($beginToday),
                $end_time ? ApiAliyunLive::setTimeFormat($end_time) : ApiAliyunLive::setTimeFormat(time()),
                1,
                10
            );
            $data = [];
            $count = 0;
            if ($res) {
                if (isset($res['RecordIndexInfoList']['RecordIndexInfo'])) {
                    foreach ($res['RecordIndexInfoList']['RecordIndexInfo'] as $item) {
                        LivePlayback::livePlaybackAdd($item);
                        $data [] = [
                            'StreamName' => $item['StreamName'],
                            'RecordId' => $item['RecordId'],
                            'playback_record_id' => $record_id,
                            'RecordUrl' => $item['RecordUrl'],
                            'StartTime' => $item['StartTime'],
                            'EndTime' => $item['EndTime'],
                        ];
                    }
                }
                if (isset($res['TotalNum'])) $count = $res['TotalNum'];
                return Json::successlayui($count, $data);
            } else {
                return Json::fail('未拉取到直播回放');
            }
        } catch (Exception $exception) {
            return Json::fail($exception->getMessage());
        }

    }

    /**回放列表
     * @param string $stream_name
     * @param string $start_time
     * @param string $end_time
     * @param int $page
     * @param int $limit
     */
    public function get_live_record_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['stream_name', ''],
            ['start_time', ''],
            ['end_time', ''],
        ]);
        $data = LivePlayback::getLivePlaybackList($where);
        return Json::successlayui($data);
    }

    /**
     * 删除直播回放
     */
    public function delete_live_playback($id = 0)
    {
        $res = parent::getDataModification('play_back', $id, 'is_del', 1);
        if ($res) {
            return Json::successful('删除成功');
        } else {
            return Json::fail('删除失败');
        }
    }

    /**设置显示
     * @param string $is_show
     * @param string $id
     */
    public function set_show($is_show = '', $id = '')
    {
        ($is_show == '' || $id == '') && Json::fail('缺少参数');
        $res = parent::getDataModification('play_back', $id, 'is_show', (int)$is_show);
        if ($res) {
            return Json::successful($is_show == 1 ? '显示成功' : '隐藏成功');
        } else {
            return Json::fail($is_show == 1 ? '显示失败' : '隐藏失败');
        }
    }

    /**
     * 设置某个字段
     * @param int $stream_name
     * @param string $field
     * @param string $value
     */
    public function set_playback_value($field = '', $id = 0, $value = '')
    {
        if (!$id || !$field) return Json::fail('缺少参数');
        $res = parent::getDataModification('play_back', $id, $field, $value);
        if ($res)
            return Json::successful('编辑成功');
        else
            return Json::fail('编辑失败');
    }

    /**
     * 设置某个字段
     * @param int $stream_name
     * @param string $field
     * @param string $value
     */
    public function set_value($stream_name = 0, $field = '', $value = '')
    {
        if (!$stream_name || !$field) return Json::fail('缺少参数');
        $res = LiveStudio::where('stream_name', $stream_name)->update([$field => $value]);
        if ($res)
            return Json::successful('设置成功');
        else
            return Json::fail('设置失败');
    }

    public function set_record($stream_name = 0, $record_id = 0, $field = '', $value = '')
    {
        if (!$stream_name || !$record_id || !$field) return Json::fail('缺少参数');
        $res = LivePlayback::where(['stream_name' => $stream_name, 'RecordId' => $record_id])->update([$field => $value]);
        if ($value == 1) {
            LiveStudio::where(['stream_name' => $stream_name])->update(['playback_record_id' => $record_id, 'is_playback' => 1]);
            LivePlayback::where(['stream_name' => $stream_name])->where('RecordId', "neq", $record_id)->update([$field => 0]);
        } else {
            LiveStudio::where(['stream_name' => $stream_name])->update(['playback_record_id' => "", 'is_playback' => 0]);
        }
        if ($res)
            return Json::successful('设置成功');
        else
            return Json::fail('设置失败');
    }

    /**
     * 直播回放
     * @param string $record_url
     * @return mixed|void
     */
    public function live_record_look($record_url = '')
    {
        if (!$record_url) return $this->failed('缺少回放地址');
        $this->assign('record_url', $record_url);
        return $this->fetch();
    }

    /**
     * 下载引导页面
     * @param string $record_url
     * @return mixed|void
     */
    public function download($record_url = '')
    {
        if (!$record_url) return $this->failed('缺少回放地址');
        $this->assign('record_url', $record_url);
        return $this->fetch();
    }

    /**
     * 评论页面
     * @param int $special_id
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function comment_list($special_id = 0)
    {
        if (!$special_id) return $this->failed('缺少参数');
        $specialInfo = SpecialModel::get($special_id);
        if (!$specialInfo) return $this->failed('查找的专题不存在');
        if ($specialInfo->is_del) return $this->failed('专题已经删除');
        $liveInfo = LiveStudio::where(['special_id' => $special_id])->find();
        if (!$liveInfo) return $this->failed('直播不存在！');
        if ($liveInfo->is_del) return $this->failed('直播间已删除！');
        $this->assign('live_id', $liveInfo->id);
        return $this->fetch();
    }

    /**
     * 获取直播评论列表
     */
    public function get_live_comment_list()
    {
        $where = parent::getMore([
            ['live_id', 0],
            ['page', 1],
            ['limit', 20],
            ['nickname', ''],
            ['start_time', ''],
            ['end_time', ''],
        ]);
        if (!$where['live_id']) return Json::fail('缺少直播间id');
        return Json::successlayui(LiveBarrage::getLiveCommentList($where));
    }

    /**
     * 设置评论隐藏显示
     *
     * @return json
     */
    public function set_show_comment($is_show = '', $id = '')
    {
        ($is_show == '' || $id == '') && Json::fail('缺少参数');
        $res = LiveBarrage::where(['id' => $id])->update(['is_show' => (int)$is_show]);
        if ($res) {
            return Json::successful('设置成功');
        } else {
            return Json::fail('设置失败');
        }
    }

    /**
     * 任务关联列表
     * @param int $special_id
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function relation_task($special_id = 0)
    {
        $specialInfo = SpecialModel::get($special_id);
        if (!$specialInfo) return $this->failed('专题不存在');
        $liveInfo = LiveStudio::where('special_id', $special_id)->find();
        if (!$liveInfo) return $this->failed('直播间不存在');
        if ($liveInfo->is_del) return $this->failed('直播间已删除！');
        $this->assign('live_id', $liveInfo->id);
        return $this->fetch();
    }

    /**
     * 任务关联列表展示
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_relation_task()
    {
        $where = parent::getMore([
            ['live_id', 0],
            ['title', ''],
            ['start_time', ''],
            ['end_time', ''],
            ['page', 1],
            ['limit', 20],
        ]);
        if (!$where['live_id']) return Json::fail('缺少参数');
        return Json::successlayui(SpecialTask::getRelationTask($where));
    }

    /**
     * 保存直播任务关联
     * @param int $live_id
     * @param int $special_id
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function save_task($live_id = 0, $special_id = 0)
    {
        if (!$live_id) return $this->failed('缺少参数');
        $liveInfo = LiveStudio::get($live_id);
        if (!$liveInfo) return $this->failed('直播间不存在');
        if ($special_id) {
            $list = SpecialCourse::where('is_show', 1)->where('special_id', $special_id)->field(['id', 'course_name'])->select();
            $opens = [];
            foreach ($list as $item) {
                $opens [] = ['value' => $item['id'], 'label' => $item['course_name']];
            }
            $f[] = Form::input('special_id', '专题id', $special_id)->disabled(true);
            $f[] = Form::input('title', '任务标题', $liveInfo->getData('live_title'));
            $f[] = Form::frameImageOne('image', '任务封面图', Url::build('admin/widget.images/index', array('fodder' => 'image')), $liveInfo->getData('live_image'))->icon('image')->width('100%')->height('550px');
            $f[] = Form::number('sort', '排序', 0);
            $form = Form::make_post_form('关联任务', $f, Url::build('set_task', array('live_id' => $live_id)), 3);
            $this->assign(compact('form'));
            return $this->fetch('public/form-builder');
        } else {
            $this->assign('grade_list', SpecialSubject::specialCategoryAll(1));
            $this->assign('live_id', $live_id);
            return $this->fetch();
        }
    }

    /**
     * @param int $grade_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_subject_list($grade_id = 0)
    {
        if (!$grade_id) return Json::fail('缺少参数');
        $subjectlist = SpecialSubject::where(['grade_id' => $grade_id, 'is_show' => 1, 'is_del' => 0])->order('sort desc')->select();
        return Json::successful($subjectlist);
    }

    /**
     * @param int $subject_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_special_list($subject_id = false, $live_goods_list = false)
    {
        if ($subject_id) $where['subject_id'] = $subject_id;
        if (!$live_goods_list) {
            $dataList = Special::PreWhere()->where($where)->order('sort desc')->select();
        } else {
            $dataList = array();
            $specialList = Special::PreWhere()->where($where)->whereIn('type', [1, 2, 3, 5])->order('type desc')->select();
            foreach ($specialList as $k => $v) {
                $dataList[$k]['value'] = $v['id'];
                $dataList[$k]['title'] = SPECIAL_TYPE[$v['type']] . "--" . $v['title'];
            }
        }
        return Json::successful($dataList);
    }

    public function move_live_admin()
    {
        $parm = parent::getMore([
            'special_id',
            'admin_id'
        ]);
        if (!$parm['special_id'] || !$parm['admin_id']) {
            Json::fail('缺少参数');
        }
        $special_info = Special::get(['id' => $parm['special_id']]);
        if (!$special_info) Json::fail('直播间不存在');
        if ($special_info['admin_id'] == $parm['admin_id']) return Json::successful('转移成功');
        $special_info->save(['admin_id' => $parm['admin_id']]);
        return Json::successful('转移成功');
    }

    /**
     * 新增直播任务
     * @param int $live_id
     */
    public function set_task($live_id = 0)
    {
        if (!$live_id) return Json::fail('缺少直播id');
        $data = parent::postMore([
            ['coures_id', ''],
            ['title', ''],
            ['image', ''],
            ['sort', 0],
            ['is_show', 1],
            ['special_id', 0]
        ]);
        if (!$data['special_id']) return Json::fail('关联专题Id缺失');
        if (!$data['title']) return Json::fail('请输入任务标题！');
        if (!$data['image']) return Json::fail('请上传图片');
        $data['add_time'] = time();
        $data['live_id'] = $live_id;
        $special_id = LiveStudio::where('id', $live_id)->value('special_id');
        $data['is_pay'] = SpecialModel::where(['type' => SPECIAL_LIVE, 'id' => $special_id])->value('pay_type');
        if (SpecialTask::set($data))
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**直播推荐（课程）
     * @return mixed
     */
    public function live_goods($live_id = 0, $special_id = 0)
    {
        $this->assign(['live_id' => $live_id, 'special_id' => $special_id]);
        return $this->fetch();
    }

    /**添加直播的专题课程推荐
     * @param int $id
     * @param string $ids
     */
    public function add_live_special($special_id = 0, $ids = '', $type = 0)
    {
        if (!$special_id || !$ids) return Json::fail('参数错误');
        $res = LiveGoods::saveLiveGoods($ids, $special_id, $type);
        if ($res)
            return Json::successful('添加成功');
        else
            return Json::fail('添加失败');
    }

    /**直播推荐列表数据
     * @return mixed
     */
    public function live_goods_list($live_id = false)
    {
        $where = parent::getMore([
            ['page', 1],
            ['live_id', 0],
            ['store_name', ""],
            ['limit', 20],
            ['order', ''],
            ['is_show', ''],
        ]);
        if ($live_id) $where['live_id'] = $live_id;
        return Json::successlayui(LiveGoods::getLiveGoodsList($where));
    }

    /**直播推荐（商品）
     * @return mixed
     */
    public function live_store($live_id = 0, $special_id = 0)
    {
        $this->assign(['live_id' => $live_id, 'special_id' => $special_id]);
        return $this->fetch();
    }

    /**直播推荐列表数据
     * @return mixed
     */
    public function live_store_list($live_id = false)
    {
        $where = parent::getMore([
            ['page', 1],
            ['live_id', 0],
            ['store_name', ""],
            ['limit', 20],
            ['order', ''],
            ['is_show', ''],
        ]);
        if ($live_id) $where['live_id'] = $live_id;
        return Json::successlayui(LiveGoods::getLiveStoreProductList($where));
    }

    /**直播推荐（活动）
     * @return mixed
     */
    public function live_event($live_id = 0, $special_id = 0)
    {
        $this->assign(['live_id' => $live_id, 'special_id' => $special_id]);
        return $this->fetch();
    }

    /**直播推荐列表数据
     * @return mixed
     */
    public function live_event_list($live_id = false)
    {
        $where = parent::getMore([
            ['page', 1],
            ['live_id', 0],
            ['store_name', ""],
            ['limit', 20],
            ['order', ''],
            ['is_show', ''],
        ]);
        if ($live_id) $where['live_id'] = $live_id;
        return Json::successlayui(LiveGoods::getLiveEventList($where));
    }

    /**直播打赏
     * @return mixed
     */
    public function live_reward($live_id = 0)
    {
        $gold_info = SystemConfigService::more(['gold_name', 'gold_image']);
        $where['l.is_del'] = 0;
        $live_studio = LiveStudio::alias('l')->where($where)->join('__SPECIAL__ s', 'l.special_id = s.id')->field('l.id,l.live_title')->select();
        $live_studio = $live_studio ? $live_studio->toArray() : [];
        $this->assign("year", getMonth('y'));
        $this->assign("live_studio", json_encode($live_studio));
        $this->assign("gold_info", $gold_info);
        $this->assign("live_id", $live_id);
        return $this->fetch();
    }

    /**礼物管理
     * @return mixed
     */
    public function live_gift()
    {
        $list = LiveGift::liveGiftList();
        $this->assign("list", $list);
        return $this->fetch();
    }

    /**添加礼物
     * @return mixed
     * @throws \FormBuilder\exception\FormBuilderException
     * @throws \think\exception\DbException
     */
    public function create($id = 0)
    {
        if ($id) $gift = LiveGift::get($id);
        $form = Form::create(Url::build('save', ['id' => $id]), [
            Form::input('live_gift_name', '礼物名称', isset($gift) ? $gift->live_gift_name : ''),
            Form::number('live_gift_price', '礼物价格(虚拟货币)', isset($gift) ? $gift->live_gift_price : '')->min(0),
            Form::checkbox('live_gift_num', '赠送数量列表', isset($gift) ? json_decode($gift->live_gift_num) : [])->options([
                ['label' => '1', 'value' => 1],
                ['label' => '5', 'value' => 5],
                ['label' => '10', 'value' => 10],
                ['label' => '20', 'value' => 20],
                ['label' => '66', 'value' => 66],
                ['label' => '99', 'value' => 99],
                ['label' => '520', 'value' => 520],
                ['label' => '999', 'value' => 999],
                ['label' => '1314', 'value' => 1314]
            ]),
            Form::frameImageOne('live_gift_show_img', '图标(100*100px)', Url::build('admin/widget.images/index', array('fodder' => 'live_gift_show_img')), isset($gift) ? $gift->live_gift_show_img : '')->icon('image')->width('70%')->height('500px'),
            Form::number('sort', '排序', isset($gift) ? $gift->sort : 0)->min(0)->max(99999),
            Form::radio('is_show', '状态', isset($gift) ? $gift->is_show : 1)->options([['label' => '显示', 'value' => 1], ['label' => '隐藏', 'value' => 0]])
        ]);
        $form->setMethod('post')->setTitle($id ? '修改礼物' : '添加礼物')->setSuccessScript('parent.layer.close(parent.layer.getFrameIndex(window.name));parent.$(".J_iframe:visible")[0].contentWindow.location.reload()');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存数据
     */
    public function save($id)
    {
        $post = parent::postMore([
            ['live_gift_name', ''],
            ['live_gift_price', 0],
            ['live_gift_num', []],
            ['live_gift_show_img', ''],
            ['is_show', 1],
            ['sort', 0],
        ]);
        if (!$post['live_gift_name']) return Json::fail('请输入礼物名称');
        if (bcsub($post['live_gift_price'], 0, 0) <= 0) return Json::fail('请输入礼物价格');
        if (count($post['live_gift_num']) < 1) return Json::fail('请选择赠送数量列表');
        if (!$post['live_gift_show_img']) return Json::fail('请选择礼物图标');
        $post['live_gift_num'] = json_encode($post['live_gift_num']);
        $post['add_time'] = time();
        if (!$id) {
            $res = LiveGift::set($post);
            if ($res) {
                return Json::successful('添加成功');
            } else {
                return Json::fail('添加失败');
            }
        } else {
            $res = LiveGift::edit($post, $id);
            if ($res) {
                return Json::successful('修改成功');
            } else {
                return Json::fail('修改失败');
            }
        }
    }

    /**礼物是否显示快捷操作
     * @param string $is_show
     * @param string $id
     * @return mixed
     */
    public function set_gift_show($is_show = '', $id = '')
    {
        ($is_show == '' || $id == '') && Json::fail('缺少参数');
        $res = LiveGift::where(['id' => $id])->update(['is_show' => (int)$is_show]);
        if ($res) {
            return Json::successful($is_show == 1 ? '显示成功' : '隐藏成功');
        } else {
            return Json::fail($is_show == 1 ? '显示失败' : '隐藏失败');
        }
    }

    /**
     * 直播礼物列表
     */
    public function live_gift_list()
    {
        return Json::successlayui(LiveGift::liveGiftList());
    }

    /**直播礼物
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function liveGiftList()
    {
        $data = LiveGift::order('sort DESC,id DESC')->select();
        return Json::successful($data);
    }

    /**直播推荐列表数据
     * @return mixed
     */
    public function live_reward_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['user_info', ""],
            ['limit', 20],
            ['order', ''],
            ['live_id', 0],
            ['gift_id', 0],
            ['date', 0],
        ]);
        //根据账号身份获取各自的列表数据
        $role_sign = get_login_role();
        if (isset($role_sign['role_sign']) && $role_sign['role_sign'] == "anchor") {
            $where['admin_id'] = get_login_id()['admin_id'];
        }
        return Json::successlayui(LiveReward::getLiveRewardList($where));
    }

    /**
     * 获取头部订单金额等信息
     * return json
     */
    public function getBadge()
    {
        $where = parent::postMore([
            ['page', 1],
            ['user_info', ''],
            ['limit', 20],
            ['order', ''],
            ['live_id', 0],
            ['gift_id', 0],
            ['date', 0],
        ]);
        return Json::successful(LiveReward::getBadge($where));
    }

}
