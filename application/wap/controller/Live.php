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


use Api\AliyunLive as ApiAliyunLive;
use app\wap\model\live\LiveBarrage;
use app\wap\model\live\LiveGoods;
use app\wap\model\live\LiveHonouredGuest;
use app\wap\model\live\LiveReward;
use app\wap\model\live\LiveStudio;
use app\wap\model\live\LivePlayback;
use app\wap\model\live\LiveUser;
use app\wap\model\live\LiveGift;
use app\wap\model\special\SpecialBuy;
use app\wap\model\special\Special;
use app\wap\model\special\SpecialTask;
use app\wap\model\special\SpecialContent;
use app\wap\model\user\User;
use app\wap\model\user\UserBill;
use service\SystemConfigService;
use service\UtilService;
use think\Config;
use service\JsonService;
use think\Cookie;
use think\Db;
use think\Session;
use think\Url;

/**直播间控制器
 * Class Live
 * @package app\wap\controller
 */
class Live extends AuthController
{

    /*
     * 白名单
     * */
    public static function WhiteList()
    {
        return [
            'get_live_record_list',
        ];

    }

    /**
     * 阿里云直播句柄
     * @var \Api\AliyunLive
     */
    protected $aliyunLive;

    protected function _initialize()
    {
        parent::_initialize();
        $this->aliyunLive = ApiAliyunLive::instance([
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
     * 直播间主页
     * @param string $stream_name
     * @return mixed|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index($stream_name = '', $record_id = 0)
    {
        if (!$stream_name) $this->failed('缺少在直播间号！', Url::build('index/index'));
        $liveInfo = LiveStudio::where('stream_name', $stream_name)->find();
        if (!$liveInfo) $this->failed('直播间不存在', Url::build('index/index'));
        if ($liveInfo->is_del) $this->failed('直播间已被删除', Url::build('index/index'));
        $userInfo = LiveUser::setLiveUser($this->uid, $liveInfo->id);
        if ($userInfo === false) $this->failed(LiveUser::getErrorInfo('用户写入不成功'), Url::build('index/index'));
        $specialLive = Special::PreWhere()->where(['id' => $liveInfo->special_id])->find();
        if (!$specialLive) $this->failed('专题不存在或者已被删除', Url::build('index/index'));
        $user_level = !$this->uid ? 0 : $this->userInfo;
        if ($specialLive->pay_type == 1 && !SpecialBuy::PaySpecial($specialLive->id, $this->uid)) {
            if ($specialLive->member_pay_type == 1 || ($user_level['level'] <= 0 && $specialLive->member_pay_type == 0)) {
                $this->failed('您还没有支付请支付后再进行观看', Url::build('special/details', ['id' => $liveInfo->special_id]));
            }
        }
        if ($specialLive->pay_type == 2) {
            $cookie_value = Cookie::get($stream_name . "studio_pwd");
            if (!$cookie_value || $cookie_value != $liveInfo['studio_pwd']) {
                $this->failed('您需要先获得密码后再进行观看', Url::build('special/details', ['id' => $liveInfo->special_id]));
            }
        }
        $AliyunLive = $this->aliyunLive;
        if ($liveInfo->is_play) {
            $site_url = SystemConfigService::get('site_url');
            $PullUrl = $AliyunLive->getPullSteam($site_url, $liveInfo->stream_name);
        } else {
            $record_id = $record_id ? $record_id : $liveInfo->playback_record_id;
            if ($liveInfo->is_playback && $record_id) {
                $livePlayBack = LivePlayback::where(['RecordId' => $record_id, 'stream_name' => $liveInfo->stream_name])->find();
                $PullUrl = $livePlayBack ? $livePlayBack->playback_url : false;
            } else
                $PullUrl = false;
        }
        $live_status = 0;
        $datatime = strtotime($liveInfo->start_play_time);
        $endTime = strtotime($liveInfo->stop_play_time);
        if ($datatime < time() && $endTime > time())
            $live_status = 1;//正在直播
        else if ($datatime < time() && $endTime < time())
            $live_status = 2;//直播结束
        else if ($datatime > time())
            $live_status = 0;//尚未直播
        $user_type = LiveHonouredGuest::where(['uid' => $this->uid, 'live_id' => $liveInfo->id])->value('type');
        if (is_null($user_type)) $user_type = 2;
        $uids = LiveHonouredGuest::where(['live_id' => $liveInfo->id])->column('uid');
        $liveInfo['abstract'] = $specialLive->abstract;
        $content = SpecialContent::where(['special_id' => $specialLive->id])->value('content');
        $liveInfo['content'] = htmlspecialchars_decode($content);
        $this->assign([
            'goldInfo' => json_encode(SystemConfigService::more("gold_name,gold_rate,gold_image")),
            'liveInfo' => json_encode($liveInfo),
            'UserSum' => bcadd(LiveUser::where(['live_id' => $liveInfo->id, 'is_open_ben' => 0, 'is_online' => 1])->sum('visit_num'), $liveInfo->online_num, 0),
            'live_title' => $liveInfo->live_title,
            'PullUrl' => $PullUrl,
            'is_ban' => $userInfo->is_ban,
            'room' => $liveInfo->id,
            'datatime' => $datatime,
            'workerman' => json_encode(Config::get('workerman.chat', [])),
            'phone_type' => UtilService::getDeviceType(),
            'live_status' => $live_status,
            'user_type' => $user_type,
            'OpenCommentCount' => LiveBarrage::where(['live_id' => $liveInfo->id, 'is_show' => 1])->count(),
            'OpenCommentTime' => LiveBarrage::where(['live_id' => $liveInfo->id, 'is_show' => 1])->order('add_time asc')->value('add_time'),
            'CommentCount' => LiveBarrage::where(['live_id' => $liveInfo->id, 'is_show' => 1])->where('uid', 'in', $uids)->count(),
            'CommentTime' => LiveBarrage::where(['live_id' => $liveInfo->id, 'is_show' => 1])->where('uid', 'in', $uids)->order('add_time asc')->value('add_time'),
        ]);
        return $this->fetch();
    }

    /**
     * 获取助教评论
     */
    public function get_comment_list()
    {
        list($page, $limit, $live_id, $add_time) = UtilService::getMore([
            ['page', 0],
            ['limit', 20],
            ['live_id', 0],
            ['add_time', 0],
        ], $this->request, true);
        if (!$live_id) return JsonService::fail('缺少参数!');
        $uids = LiveHonouredGuest::where(['live_id' => $live_id])->column('uid');
        if (!$uids) {
            $ystemConfig = SystemConfigService::more(['site_name', 'home_logo']);
            $data = [
                'nickname' => $ystemConfig['site_name'],
                'avatar' => $ystemConfig['home_logo'],
                'user_type' => 2,
                'content' => LiveStudio::where('id', $live_id)->value('auto_phrase'),
                'id' => 0,
                'type' => 1,
                'uid' => 0
            ];
            return JsonService::successful(['list' => [$data], 'page' => 0]);
        }
        return JsonService::successful(LiveBarrage::getCommentList($uids, $live_id, $page, $limit, $add_time));
    }

    /**
     * 获取助教，讲师，普通人的评论
     */
    public function get_open_comment_list()
    {
        list($page, $limit, $live_id, $add_time) = UtilService::getMore([
            ['page', 0],
            ['limit', 20],
            ['live_id', 0],
            ['add_time', 0],
        ], $this->request, true);
        if (!$live_id) return JsonService::fail('缺少参数!');
        return JsonService::successful(LiveBarrage::getCommentList(false, $live_id, $page, $limit, $add_time));
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
    public function get_live_record_list($special_id = '', $start_time = '', $end_time = '', $page = 1, $limit = 10)
    {
        if (!$special_id) return JsonService::fail('参数缺失');
        $specialInfo = Special::get($special_id);
        if (!$specialInfo) return JsonService::fail('直播专题不存在');
        $liveStudio = LiveStudio::where(['special_id' => $specialInfo['id']])->find();
        if (!$liveStudio) return JsonService::fail('缺少直播间');
        if (!$liveStudio['stream_name']) return JsonService::fail('缺少直播间id');
        $data = [];
        $count = 0;
        $data = compact('data', 'count');
        if ($liveStudio['is_playback'] == 1) {
            $where['stream_name'] = $liveStudio['stream_name'];
            $where['page'] = $page;
            $where['limit'] = $limit;
            $where['start_time'] = $start_time;
            $where['end_time'] = $end_time;
            $data = LivePlayback::getLivePlaybackList($where);
        }
        $data['page'] = $page++;
        return JsonService::successful($data);

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
    public function get_live_record_list1($special_id = '', $start_time = '', $end_time = '', $page = 1, $limit = 10)
    {
        if (!$special_id) return JsonService::fail('参数缺失');
        $specialInfo = Special::get($special_id);
        if (!$specialInfo) return JsonService::fail('直播专题不存在');
        $liveStudio = LiveStudio::where(['special_id' => $specialInfo['id']])->find();
        if (!$liveStudio) return JsonService::fail('缺少直播间');
        if (!$liveStudio['stream_name']) return JsonService::fail('缺少直播间id');
        $aliyunLive = $this->aliyunLive;
        $beginToday = mktime(0, 0, 0, date('m'), date('d') - 3, date('Y'));
        if ($start_time && $end_time) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            if ($start_time > $end_time) return JsonService::fail('开始时间不能大于结束时间');
            $time = bcsub($end_time, $start_time, 0) / 86400;
            if ($time > 4) return JsonService::fail('开始和结束的时间不能间隔4天');
        }
        $res = $aliyunLive->queryLiveRecordFiles(
            $liveStudio['stream_name'],
            $start_time ? ApiAliyunLive::setTimeFormat($start_time) : ApiAliyunLive::setTimeFormat($beginToday),
            $end_time ? ApiAliyunLive::setTimeFormat($end_time) : ApiAliyunLive::setTimeFormat(time()),
            $page,
            $limit
        );
        $data = [];
        $count = 0;
        if ($res) {
            if (isset($res['RecordIndexInfoList']['RecordIndexInfo'])) {
                foreach ($res['RecordIndexInfoList']['RecordIndexInfo'] as $item) {
                    $data['list'][] = [
                        'StreamName' => $item['StreamName'],
                        'RecordId' => $item['RecordId'],
                        'playback_record_id' => $liveStudio['playback_record_id'],
                        'RecordUrl' => $item['RecordUrl'],
                        'StartTime' => $item['StartTime'],
                        'EndTime' => $item['EndTime'],
                    ];
                }
            }
            if (isset($res['TotalNum'])) $count = $res['TotalNum'];
            $data['page'] = $page++;
            $data['count'] = $count;
            return JsonService::successful($data);
        } else {
            return JsonService::fail("网络错误");
        }
    }

    /**
     * 打赏礼物列表
     */
    public function live_gift_list()
    {
        $live_gift = LiveGift::liveGiftList();
        return JsonService::successful($live_gift);
    }

    /**打赏接口
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function live_reward()
    {
        list($uid, $live_gift_id, $live_gift_num, $stream_name, $special_id) = UtilService::postMore([
            ['uid', 0],
            ['live_gift_id', 0],
            ['live_gift_num', 0],
            ['stream_name', 0],
            ['special_id', 0],
        ], $this->request, true);
        if (!$uid) return JsonService::fail('用户id缺失');
        if (!$live_gift_id || !is_numeric($live_gift_id)) return JsonService::fail('礼物id缺失');
        if (!$stream_name || !is_numeric($stream_name)) return JsonService::fail('直播间号缺失');
        if (!$special_id || !is_numeric($special_id)) return JsonService::fail('直播专题ID缺失');
        $user_info = $this->userInfo;
        if ($uid != $user_info['uid']) return JsonService::fail('非法用户');
        if (!$live_gift_num || !is_numeric($live_gift_num) || $live_gift_num < 0) return JsonService::fail('请选择礼物数量');
        //获取礼物配置列表
        $live_gift = LiveGift::liveGiftOne($live_gift_id);
        if (!$live_gift) return JsonService::fail('礼物不存在');
        //查看直播间是否存在
        $live_studio = LiveStudio::where(['stream_name' => $stream_name])->find();
        if (!$live_studio) return JsonService::fail('直播间不存在');
        $live_studio = $live_studio->toarray();
        if ($live_studio['special_id'] != $special_id) return JsonService::fail('直播专题有误');
        //看金币是否足够
        $gift_price = $live_gift['live_gift_price'] * $live_gift_num;
        $gold_name = SystemConfigService::get('gold_name');
        if ($user_info['gold_num'] <= 0 || $gift_price > $user_info['gold_num']) return JsonService::fail('您当前' . $gold_name . '不够，请充值', [], 406);
        try {
            User::beginTrans();
            //插入打赏数据
            $add_gift['uid'] = $uid;
            $add_gift['live_id'] = $live_studio['id'];
            $add_gift['nickname'] = $user_info['nickname'];
            $add_gift['gift_id'] = $live_gift_id;
            $add_gift['gift_name'] = $live_gift['live_gift_name'];
            $add_gift['gift_price'] = $live_gift['live_gift_price'];
            $add_gift['total_price'] = $gift_price;
            $add_gift['gift_num'] = $live_gift_num;
            $add_gift['add'] = time();
            $live_reward_id = LiveReward::insertLiveRewardData($add_gift);
            //插入聊天记录
            $add_barrage['uid'] = $uid;
            $add_barrage['to_uid'] = 0;
            $add_barrage['type'] = 4;//礼物
            $add_barrage['barrage'] = $live_gift_id;//礼物ID
            $add_barrage['live_id'] = $live_reward_id;
            $add_barrage['is_show'] = 1;
            LiveBarrage::set($add_barrage);
            //插入虚拟货币支出记录（资金监管记录表）
            $gold_name = SystemConfigService::get("gold_name");
            $mark = '用户赠送' . $stream_name . "号直播间" . $live_gift_num . '个' . $live_gift["live_gift_name"] . ',扣除' . $gold_name . $gift_price . '金币';
            UserBill::expend('用户打赏扣除金币', $uid, 'gold_num', 'live_reward', $gift_price, 0, $this->userInfo['gold_num'], $mark);
            User::bcDec($uid, 'gold_num', $gift_price, 'uid');
            User::commitTrans();
            return JsonService::successful($mark);
        } catch (\Exception $e) {
            User::rollbackTrans();
            return JsonService::fail('网路异常，打赏失败');
        }

    }

    /**
     * 带货商品列表
     */
    public function live_goods_list()
    {
        list($live_id) = UtilService::getMore([
            ['live_id', 0],
        ], $this->request, true);
        $is_member = isset($this->userInfo['level']) ? $this->userInfo['level'] : 0;
        return JsonService::successful(LiveGoods::getLiveGoodsList(['is_show' => 1, 'live_id' => $live_id], $is_member, 0, 0));
    }

    /**直播礼物排行榜
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_live_reward()
    {
        list($uid, $live_id, $page, $limit) = UtilService::postMore([
            ['uid', 0],
            ['live_id', 0],
            ['page', 1],
            ['limit', 20],
        ], $this->request, true);
        if (!$uid) return JsonService::fail('用户参数缺失');
        $user_info = $this->userInfo;
        if ($uid != $user_info['uid']) return JsonService::fail('非法用户');
        if (!$live_id) return JsonService::fail('直播间参数缺失');
        $live_info = LiveStudio::where('id', $live_id)->find();
        if (!$live_info) return JsonService::fail('直播间不存在');
        $data = LiveReward::getLiveRewardList(['live_id' => $live_id], $page, $limit);
        $now_user_reward = LiveReward::getLiveRewardOne(['live_id' => $live_id, 'uid' => $uid]);
        $data['now_user_reward'] = $now_user_reward;
        return JsonService::successful($data);
    }

}
