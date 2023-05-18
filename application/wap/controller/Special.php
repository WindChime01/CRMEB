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

use app\admin\model\special\SpecialBarrage;
use app\wap\model\activity\EventRegistration;
use app\wap\model\activity\EventSignUp;
use app\wap\model\activity\EventPrice;
use app\wap\model\live\LiveStudio;
use app\wap\model\special\Lecturer;
use app\wap\model\special\Special as SpecialModel;
use app\wap\model\special\LearningRecords;
use app\wap\model\live\LivePlayback;
use app\wap\model\special\SpecialBuy;
use app\wap\model\special\SpecialContent;
use app\wap\model\special\SpecialCourse;
use app\wap\model\special\SpecialRecord;
use app\wap\model\special\SpecialRelation;
use app\wap\model\special\SpecialSource;
use app\wap\model\special\SpecialSubject;
use app\wap\model\special\SpecialTask;
use app\wap\model\special\SpecialWatch;
use app\wap\model\special\SpecialReply;
use app\wap\model\special\SpecialExchange;
use app\wap\model\special\SpecialBatch;
use app\wap\model\store\StoreCart;
use app\wap\model\store\StoreOrder;
use app\wap\model\store\StorePink;
use app\wap\model\user\User;
use service\CanvasService;
use service\JsonService;
use service\SystemConfigService;
use service\UtilService;
use think\cache\driver\Redis;
use think\Cookie;
use think\exception\HttpException;
use think\response\Json;
use think\Session;
use think\Url;
use think\Db;
use think\Request;
use service\VodService;
use app\wap\model\routine\RoutineTemplate;
use app\wap\model\topic\TestPaper;
use app\wap\model\topic\TestPaperOrder;
use app\wap\model\topic\CertificateRelated;
use app\wap\model\topic\CertificateRecord;
use app\wap\model\material\DataDownload;
use app\wap\model\material\DataDownloadBuy;
use app\wap\model\material\DataDownloadOrder;
use app\wap\model\topic\Relation;
use think\Config;

/**专题
 * Class Special
 * @package app\wap\controller
 */
class Special extends AuthController
{
    /**
     * 白名单
     * */
    public static function WhiteList()
    {
        return [
            'details',
            'single_details',
            'get_pink_info',
            'get_course_list',
            'play',
            'play_num',
            'grade_list',
            'set_barrage_index',
            'get_barrage_list',
            'special_cate',
            'get_grade_cate',
            'get_subject_cate',
            'get_special_list',
            'get_cloumn_task',
            'activity_details',
            'isMember',
            'activityType',
            'groupLists',
            'groupProjectList',
            'learningRecords',
            'numberCourses',
            'addLearningRecords',
            'groupWork',
            'source_detail',
            'getSourceDetail',
            'relatedCourses',
            'group_list',
            'pinkIngLists',
            'get_video_playback_credentials',
            'getTemplateIds',
            'special_reply_list',
            'special_reply_data',
            'inspect',
            'SpecialDataDownload',
            'data_details',
            'exchange',
            'question_index',
            'special_validity'
        ];
    }

    /**获取视频上传地址和凭证
     * @param string $videoId
     * @param int $type
     */
    public function get_video_playback_credentials($type = 1, $videoId = '')
    {
        $url = VodService::videoUploadAddressVoucher('', $type, $videoId);
        return JsonService::successful($url);
    }

    /**获取用户相关的订阅消息模版ID
     * @param $pay_type_num
     * @param $special_id
     */
    public function getTemplateIds($pay_type_num, $special_id)
    {
        $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
        if ($wechat_notification_message == 1) {
            $templateIds = '';
        } else {
            $templateIds = RoutineTemplate::getTemplateIdList($pay_type_num, $special_id);
        }
        return JsonService::successful($templateIds);
    }

    /**获取专题价格
     * @param $id
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSpecialPrice($id = 0)
    {
        if (!$id) return JsonService::fail('缺少参数');
        $special_money = SpecialModel::where('id', $id)->field('money,pay_type,member_money,member_pay_type')->find();
        if (!$special_money) return JsonService::fail('专题价格获取失败');
        return JsonService::successful($special_money);
    }

    /**
     * 专题详情
     * @param $id int 专题id
     * @param $pinkId int 拼团id
     * @param $gift_uid int 赠送礼物用户
     * @param $gift_order_id string 礼物订单号
     * @return
     */
    public function details($id = 0, $pinkId = 0, $gift_uid = 0, $gift_order_id = null, $link_pay_uid = 0, $partake = 0, $gift = 0, $link_pay = 0)
    {
        if (!$id) $this->failed('缺少参数,无法访问', Url::build('index/index'));
        if ($gift_uid && $gift_order_id) {
            if ($gift_uid == $this->uid) $this->failed('您不能领取自己的礼物', Url::build('special/grade_special'));
            if (!User::get($gift_uid)) $this->failed('赠送礼物的用户不存在', Url::build('my/my_gift'));
            $order = StoreOrder::where(['is_del' => 0, 'order_id' => $gift_order_id])->find();
            if (!$order) $this->failed('赠送的礼物订单不存在', Url::build('my/my_gift'));
            if ($order->total_num == $order->gift_count) $this->failed('礼物已被领取完', Url::build('special/grade_special'));
        }
        $special = SpecialModel::getOneSpecial($this->uid, $id);
        if ($special === false) $this->failed(SpecialModel::getErrorInfo('无法访问'), Url::build('index/index'));
        $is_member = isset($this->userInfo['level']) ? $this->userInfo['level'] : 0;
        if (!isset($special['special'])) $this->failed('专题信息未获得', Url::build('index/index'));
        $specialinfo = $special['special'];
        $specialinfo = is_string($specialinfo) ? json_decode($specialinfo, true) : $specialinfo;
        if (!$is_member && $specialinfo['is_mer_visible'] == 1) $this->failed('专题仅会员可以获得，请充值会员', Url::build('special/member_recharge'));
        if (in_array($specialinfo['money'], [0, 0.00]) || in_array($specialinfo['pay_type'], [PAY_NO_MONEY, PAY_PASSWORD])) {
            $isPay = 1;
        } else {
            $isPay = (!$this->uid || $this->uid == 0) ? false : SpecialBuy::PaySpecial($id, $this->uid);
        }
        $isBatch = SpecialBatch::isBatch($id);//专题是否开启兑换活动
        $isPink = false;
        if (!$isPay && $this->uid && !$pinkId) {
            $pinkId = StorePink::where(['cid' => $id, 'status' => '1', 'uid' => $this->uid])->order('add_time desc')->value('id');
            if ($pinkId) {
                $isPink = true;
            } else {
                $pinkId = 0;
            }
        }
        if ((float)$specialinfo['money'] < 0) {
            $isPink = true;
        }
        $liveInfo = [];
        if ($specialinfo['type'] == SPECIAL_LIVE) {
            $liveInfo = LiveStudio::where('special_id', $specialinfo['id'])->find();
            if (!$liveInfo) $this->failed('直播间尚未查到！', Url::build('index/index'));
            if ($liveInfo->is_del) $this->failed('直播间已经删除！', Url::build('index/index'));
        }
        if ($isPay && $this->uid && $specialinfo['type'] == SPECIAL_COLUMN) SpecialBuy::update_column($id, $this->uid);
        $user_level = !$this->uid ? 0 : $this->userInfo;
        $site_url = SystemConfigService::get('site_url') . Url::build('special/details') . '?id=' . $id . '&spread_uid=' . $this->uid;
        $cookie = Config::get('cookie', '');
        $this->assign($special);
        $this->assign('pinkId', $pinkId);
        $this->assign('prefix', $cookie['prefix']);
        $this->assign('isBatch', $isBatch);
        $this->assign('site_url', $site_url);
        $this->assign('is_member', isset($user_level['level']) ? $user_level['level'] : 0);
        $this->assign('isPink', $isPink);
        $this->assign('isPay', $isPay);
        $this->assign('liveInfo', json_encode($liveInfo));
        $this->assign('orderId', $gift_order_id);
        $this->assign('link_pay', (int)$link_pay);
        $this->assign('gift', (int)$gift);
        $this->assign('link_pay_uid', $link_pay_uid);
        $this->assign('comment_switch', SystemConfigService::get('special_comment_switch'));//专题评论开关
        $this->assign('BarrageShowTime', SystemConfigService::get('barrage_show_time'));
        $this->assign('barrage_index', Cookie::get('barrage_index'));
        return $this->fetch();
    }

    /**获取课程可以使用时间
     * @param $id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function special_validity($id)
    {
        $special_money = SpecialModel::where('id', $id)->field('money,pay_type,member_money,member_pay_type,is_mer_visible')->find();
        $is_member = isset($this->userInfo['level']) ? $this->userInfo['level'] : 0;
        $validity = -1;
        if (in_array($special_money['money'], [0, 0.00]) || in_array($special_money['pay_type'], [PAY_NO_MONEY, PAY_PASSWORD])) {
            $validity = 0;
        } else {
            $isPay = (!$this->uid || $this->uid == 0) ? false : SpecialBuy::PaySpecial($id, $this->uid);
            if ($isPay) $validity = SpecialBuy::getSpecialEndTime($id, $this->uid);
        }
        if (in_array($special_money['member_money'], [0, 0.00]) || in_array($special_money['member_pay_type'], [PAY_NO_MONEY])) {
            if ($validity == -1 && $is_member) {
                $validity = bcsub($this->userInfo['overdue_time'], time(), 0);
            }
        }
        return JsonService::successful(['validity' => $validity]);
    }

    /**轻专题详情
     * @param int $id
     * @param int $pinkId
     * @param int $gift_uid
     * @param null $gift_order_id
     * @param int $link_pay_uid
     * @param int $partake
     * @param int $gift
     * @param int $link_pay
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function single_details($id = 0, $pinkId = 0, $gift_uid = 0, $gift_order_id = null, $link_pay_uid = 0, $partake = 0, $gift = 0, $link_pay = 0)
    {
        if (!$id) $this->failed('缺少参数,无法访问', Url::build('index/index'));
        if ($gift_uid && $gift_order_id) {
            if ($gift_uid == $this->uid) $this->failed('您不能领取自己的礼物', Url::build('special/grade_special'));
            if (!User::get($gift_uid)) $this->failed('赠送礼物的用户不存在', Url::build('my/my_gift'));
            $order = StoreOrder::where(['is_del' => 0, 'order_id' => $gift_order_id])->find();
            if (!$order) $this->failed('赠送的礼物订单不存在', Url::build('my/my_gift'));
            if ($order->total_num == $order->gift_count) $this->failed('礼物已被领取完', Url::build('special/grade_special'));
        }
        $is_member = isset($this->userInfo['level']) ? $this->userInfo['level'] : 0;
        $special = SpecialModel::getSingleOneSpecial($this->uid, $id);
        if ($special === false) $this->failed(SpecialModel::getErrorInfo('无法访问'), Url::build('index/index'));
        if (!isset($special['special'])) $this->failed('专题信息未获得', Url::build('index/index'));
        $specialinfo = $special['special'];
        $specialinfo = is_string($specialinfo) ? json_decode($specialinfo, true) : $specialinfo;
        if (!$is_member && $specialinfo['is_mer_visible'] == 1) $this->failed('专题仅会员可以获得，请充值会员', Url::build('special/member_recharge'));
        if (in_array($specialinfo['money'], [0, 0.00]) || in_array($specialinfo['pay_type'], [PAY_NO_MONEY, PAY_PASSWORD])) {
            $isPay = 1;
        } else {
            $isPay = (!$this->uid || $this->uid == 0) ? false : SpecialBuy::PaySpecial($id, $this->uid);
        }
        $isBatch = SpecialBatch::isBatch($id); //专题是否开启兑换活动
        $isPink = false;
        if (!$isPay && $this->uid && !$pinkId) {
            $pinkId = StorePink::where(['cid' => $id, 'status' => '1', 'uid' => $this->uid])->order('add_time desc')->value('id');
            if ($pinkId) {
                $isPink = true;
            } else {
                $pinkId = 0;
            }
        }
        if ((float)$specialinfo['money'] < 0) {
            $isPink = true;
        }

        $user_level = !$this->uid ? 0 : $this->userInfo;
        $site_url = SystemConfigService::get('site_url') . Url::build('special/single_details') . '?id=' . $id . '&spread_uid=' . $this->uid;
        $this->assign($special);
        $this->assign('pinkId', $pinkId);
        $this->assign('isBatch', $isBatch);
        $this->assign('site_url', $site_url);
        $this->assign('is_member', isset($user_level['level']) ? $user_level['level'] : 0);
        $this->assign('isPink', $isPink);
        $this->assign('isPay', $isPay);
        $this->assign('orderId', $gift_order_id);
        $this->assign('link_pay', (int)$link_pay);
        $this->assign('gift', (int)$gift);
        $this->assign('link_pay_uid', $link_pay_uid);
        $this->assign('comment_switch', SystemConfigService::get('special_comment_switch'));//专题评论开关
        $this->assign('BarrageShowTime', SystemConfigService::get('barrage_show_time'));
        $this->assign('barrage_index', Cookie::get('barrage_index'));
        return $this->fetch();
    }

    /**轻专题 图文内容
     * @param $try
     * @param $id
     * @return mixed
     */
    public function single_text_detail($try, $id)
    {
        $this->assign(['try' => $try, 'id' => $id]);
        return $this->fetch();
    }

    /**获取图文轻专题的内容
     * @param int $id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function single_img_content($id = 0)
    {
        if (!$id) return JsonService::fail('缺少参数,无法访问');
        $is_member = isset($this->userInfo['level']) ? $this->userInfo['level'] : 0;
        $data = SpecialModel::getSingleImgSpecialContent($id);
        if (in_array($data['money'], [0, 0.00]) || in_array($data['pay_type'], [PAY_NO_MONEY, PAY_PASSWORD]) || ($is_member > 0 && $data['member_pay_type'] == 0)) {
            $isPay = 1;
        } else {
            $isPay = (!$this->uid || $this->uid == 0) ? false : SpecialBuy::PaySpecial($id, $this->uid);
        }
        if (!$isPay) unset($data['content']);
        $site_url = SystemConfigService::get('site_url') . Url::build('special/single_details') . '?id=' . $id . '&spread_uid=' . $this->uid;
        $viewing_time = 0;
        if ($this->uid && $id) {
            $viewing_time = SpecialWatch::where(['uid' => $this->uid, 'special_id' => $id, 'task_id' => 0])->value('viewing_time');
            $viewing_time = $viewing_time ? $viewing_time : 0;
        }
        $data['viewing_time'] = $viewing_time;
        $data['is_member'] = $is_member;
        $data['isPay'] = $isPay;
        $data['link_url'] = $site_url;
        return JsonService::successful($data);
    }

    /**轻专题 音视频内容
     * @param $try
     * @param $id
     * @return mixed
     */
    public function single_con_detail($try, $id)
    {
        $this->assign(['try' => $try, 'id' => $id]);
        return $this->fetch();
    }

    /**获取音视频轻专题的内容
     * @param int $id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function single_con_content($id = 0)
    {
        if (!$id) return JsonService::fail('缺少参数,无法访问');
        $is_member = isset($this->userInfo['level']) ? $this->userInfo['level'] : 0;
        $taskInfo = SpecialModel::getSingleSpecialContent($id);
        if (in_array($taskInfo['money'], [0, 0.00]) || in_array($taskInfo['pay_type'], [PAY_NO_MONEY, PAY_PASSWORD]) || ($is_member > 0 && $taskInfo['member_pay_type'] == 0)) {
            $isPay = 1;
        } else {
            $isPay = (!$this->uid || $this->uid == 0) ? false : SpecialBuy::PaySpecial($id, $this->uid);
        }
        if ($isPay == false && !$taskInfo['singleProfile']['is_try']) {
            unset($taskInfo['singleProfile']['videoId'], $taskInfo['singleProfile']['link']);
        }
        $viewing_time = 0;
        if ($this->uid && $id) {
            $viewing_time = SpecialWatch::where(['uid' => $this->uid, 'special_id' => $id, 'task_id' => 0])->value('viewing_time');
            $viewing_time = $viewing_time ? $viewing_time : 0;
        }
        $site_url = SystemConfigService::get('site_url') . Url::build('special/single_details') . '?id=' . $id . '&spread_uid=' . $this->uid;
        $taskInfo['link_url'] = $site_url;
        $taskInfo['isPay'] = $isPay;
        $taskInfo['is_member'] = $is_member;
        $taskInfo['viewing_time'] = $viewing_time;
        return JsonService::successful($taskInfo);
    }

    /**专题下课程数量
     * @param $id
     */
    public function numberCourses($id)
    {
        $special = SpecialModel::PreWhere()->find($id);
        $count = 0;
        if ($special) {
            $count = SpecialModel::numberChapters($special->type, $id);
            if($special['quantity']!=$count) {
                SpecialModel::PreWhere()->where(['id' => $id, 'type' => $special->type])->update(['quantity' => $count]);
            }
        }
        return JsonService::successful($count);
    }


    /**获取拼团信息
     * @param int $id
     * @param int $pinkId
     */
    public function pinkIngLists($id = 0, $pinkId = 0)
    {
        $pinkIngList = StorePink::getPinkAll($id, 0, 0);
        foreach ($pinkIngList as &$item) {
            $item['difftime'] = [];
            $pinkAll = StorePink::getPinkMember($item['k_id'] ? $item['k_id'] : $item['id']);
            $pinkAll = StorePink::getPinkTFalseList($pinkAll, $item['k_id'] ? $item['k_id'] : $item['id'], $id);
            $pinkAllCount = count($pinkAll);
            $pinkT = $item['k_id'] ? StorePink::getPinkUserOne($item['k_id']) : $item;
            $item['num'] = bcsub($pinkT['people'], bcadd($pinkAllCount, 1, 0), 0);
        }
        return JsonService::successful($pinkIngList);
    }

    /**获取浏览人
     * @param $id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function learningRecords($id)
    {
        $uids = LearningRecords::where(['special_id' => $id])->column('uid');
        $fake_sales = SpecialModel::where('id', $id)->value('fake_sales');
        $uids = array_unique($uids);
        $recordCoujnt = bcadd($fake_sales, count($uids), 0);
        if (count($uids) < 5) {
            if ($recordCoujnt >= 5) {
                $ic = bcsub(5, count($uids), 0);
            } else {
                $ic = bcsub($recordCoujnt, count($uids), 0);
            }
            if ($ic) {
                $maxid = User::where('status', 1)->max('uid');
                $minid = User::where('status', 1)->min('uid');
                for ($i = 0; $i < $ic; $i++) {
                    $uid = rand($minid, $maxid);
                    array_push($uids, $uid);
                }
            }
        } else {
            $uids = array_slice($uids, 0, 5);
        }
        $record = [];
        foreach ($uids as $key => $value) {
            $user = $this->userdata($value);
            array_push($record, $user);
        }
        $data['record'] = $record;
        $data['recordCoujnt'] = processingData($recordCoujnt);;
        return JsonService::successful($data);
    }

    public function userdata($uid)
    {
        $avatar = User::where('uid', $uid)->value('avatar');
        if ($avatar) {
            $user['avatar'] = $avatar;
        } else {
            $user['avatar'] = '/system/images/user_log.jpg';
        }
        return $user;
    }

    /**记录专题浏览人
     * @param $id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addLearningRecords($id)
    {
        $special = SpecialModel::PreWhere()->find($id);
        SpecialModel::where('id', $id)->setInc('browse_count');
        if ($this->uid) SpecialRecord::record($id, $this->uid);
        if ($this->uid) {
            $time = strtotime('today');
            LearningRecords::recordLearning($id, $this->uid, $time);
            if ($special->lecturer_id) {
                Lecturer::where('id', $special->lecturer_id)->setInc('study');
            }
        }
        return JsonService::successful('ok');
    }

    /**用户专题评价
     * @param int $special_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function user_comment_special($special_id = 0)
    {
        if (!$special_id) return JsonService::fail('参数错误!');
        $uid = $this->uid;
        if (SpecialReply::be(['special_id' => $special_id, 'uid' => $uid, 'is_del' => 0])) return JsonService::fail('该专题已评价!');
        $group = UtilService::postMore([
            ['comment', ''], ['pics', []], ['satisfied_score', 5]
        ]);
        if ($group['comment'] == '') return JsonService::fail('请填写评价内容');
        $group['comment'] = htmlspecialchars(trim($group['comment']));
        if (sensitive_words_filter($group['comment'])) return JsonService::fail('请注意您的用词，谢谢！！');
        if ($group['satisfied_score'] < 1) return JsonService::fail('请为专题满意度评分');
        $group = array_merge($group, [
            'uid' => $uid,
            'special_id' => $special_id
        ]);
        SpecialReply::beginTrans();
        $res = SpecialReply::reply($group);
        if (!$res) {
            SpecialReply::rollbackTrans();
            return JsonService::fail('评价失败!');
        }
        SpecialReply::uodateScore($special_id);
        SpecialReply::commitTrans();
        return JsonService::successful('评价成功!');
    }

    /**获取专题评价列表
     * @param string $special_id
     * @param int $page
     * @param int $limit
     * @param string $filter
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function special_reply_list($special_id = '', $page = 1, $limit = 8, $filter = 'all')
    {
        if (!$special_id || !is_numeric($special_id)) return JsonService::fail('参数错误!');
        $list = SpecialReply::getSpecialReplyList($special_id, $page, $limit, $filter);
        return JsonService::successful($list);
    }

    /**
     * 评价数据
     */
    public function special_reply_data($special_id = '')
    {
        if (!$special_id || !is_numeric($special_id)) return JsonService::fail('参数错误!');
        $data = SpecialReply::getSpecialReplyData($special_id);
        return JsonService::successful($data);
    }

    /**
     * 礼物领取
     *
     * */
    public function receive_gift($orderId = '')
    {
        if (!$orderId) return JsonService::fail('缺少参数');
        if (StoreOrder::createReceiveGift($orderId, $this->uid) == false)
            return JsonService::fail(StoreOrder::getErrorInfo('领取失败'));
        else
            return JsonService::successful('领取成功');
    }

    /**
     * 查看单个拼团状态
     * @param $pink_id int 拼团id
     * @return html
     * */
    public function order_pink($pink_id = '', $is_help = 0)
    {
        if (!$pink_id) $this->failed('缺少订单号', Url::build('my/order_list'));
        $this->assign([
            'pink_id' => $pink_id,
            'is_help' => $is_help,
        ]);
        return $this->fetch();
    }

    /**
     * 拼团支付完成后页面
     * @param null $orderId
     * @return mixed|void
     */
    public function pink($pink_id = 0, $special_id = 0, $is_help = 0, $orderId = null)
    {
        if (is_null($orderId) && $is_help == 0) {
            $orderId = StorePink::where(['id' => $pink_id, 'cid' => $special_id, 'uid' => $this->uid])->order('add_time desc')->value('order_id');
        } else if (is_null($orderId) && $is_help == 1) {
            $orderId = StorePink::where(['id' => $pink_id, 'cid' => $special_id, 'k_id' => 0])->order('add_time desc')->value('order_id');
            if (StorePink::be(['cid' => $special_id, 'uid' => $this->uid])) {
                $pink = StorePink::where(['cid' => $special_id, 'uid' => $this->uid])->field('id,k_id')->find();
                if ($pink && $pink_id == $pink['k_id'] || $pink_id == $pink['id']) {
                    $is_help = 0;
                } else {
                    $this->failed('您已参与该专题的拼团，不能多次参与');
                }
            }
        }
        $info = StoreOrder::getOrderSpecialInfo($orderId, $this->uid);
        if ($info === false) $this->failed(StoreOrder::getErrorInfo(), Url::build('special/special_cate'));
        $site_url = SystemConfigService::get('site_url') . Url::build('special/pink') . '?pink_id=' . $info['pinkT']['id'] . '&special_id=' . $info['special']['id'] . '&is_help=1&spread_uid=' . $this->uid;
        $special = SpecialModel::PreWhere()->find($info['special_id']);
        if (!$special) $this->failed('专题不存在', Url::build('index/index'));
        $this->assign(['special_id' => $info['special_id'], 'site_url' => $site_url, 'info' => json_encode($info), 'pink_id' => $pink_id, 'is_help' => $is_help, 'is_light' => $special['is_light']]);
        return $this->fetch();
    }

    /**
     * 拼团专题列表
     */
    public function groupProjectList()
    {
        $where = UtilService::getMore([
            ['page', 1],
            ['limit', 10]
        ]);
        return JsonService::successful(SpecialModel::getPinkSpecialList($where['page'], $where['limit']));
    }

    /**
     * 拼团数量
     */
    public function groupWork()
    {
        $data['count'] = StorePink::where(['status' => 2])->count();
        $data['avatar'] = StorePink::where(['p.status' => 2])->alias('p')->join('User u', 'p.uid=u.uid')->field('u.avatar')->limit(0, 3)->select();
        return JsonService::successful($data);
    }

    /**
     * 获取单个拼团详情
     * $pinkId 拼团id
     * */
    public function get_pink_info($pinkId = 0)
    {
        $is_ok = 0;//判断拼团是否完成
        $userBool = 0;//判断当前用户是否在团内  0未在 1在
        $pinkBool = 0;//判断当前用户是否在团内  0未在 1在
        if (!$this->uid) return JsonService::fail('请先登录！');
        $pink = StorePink::getPinkUserOne($pinkId);
        if (isset($pink['is_refund']) && $pink['is_refund']) {
            return JsonService::fail('订单已退款', ['special_id' => $pink['cid']]);
        }
        if (!$pink) return JsonService::fail('参数错误', ['url' => Url::build('my/index')]);
        list($pinkAll, $pinkT, $count, $idAll, $uidAll) = StorePink::getPinkMemberAndPinkK($pink);
        if ($pinkT['status'] == 2)
            $pinkBool = 1;
        else {
            if (!$count || $count < 0) {//组团完成
                $pinkBool = StorePink::PinkComplete($uidAll, $idAll, $this->uid, $pinkT);
            } else {//拼团失败 退款
                $pinkBool = StorePink::PinkFail($this->uid, $idAll, $pinkAll, $pinkT, (int)$count, $pinkBool, $uidAll);
            }
        }
        if ($pinkBool === false) return JsonService::fail(StorePink::getErrorInfo());
        foreach ($pinkAll as $v) {
            if ($v['uid'] == $this->uid) $userBool = 1;
        }
        if ($pinkT['uid'] == $this->uid) $userBool = 1;
        $data['pinkBool'] = $pinkBool;
        $data['is_ok'] = $is_ok;
        $data['userBool'] = $userBool;
        $data['pinkT'] = $pinkT;
        $data['pinkAll'] = $pinkAll;
        $data['count'] = $count;
        $data['current_pink_order'] = StorePink::getCurrentPink($pinkId);
        $data['special'] = SpecialModel::getPinkSpecialInfo($pinkT['order_id'], $pinkId, $this->uid);
        return JsonService::successful($data);
    }

    /**
     * 专题收藏
     * @param $id int 专题id
     * @return json
     */
    public function collect($id = 0)
    {
        if (!$id) return JsonService::fail('缺少参数');
        if (SpecialRelation::SetCollect($this->uid, $id))
            return JsonService::successful('成功');
        else
            return JsonService::fail('失败');
    }

    /**
     * 获取某个专题的素材列表
     * @return json
     * */
    public function get_course_list()
    {
        list($page, $limit, $special_id) = UtilService::getMore([
            ['page', 1],
            ['limit', 10],
            ['special_id', 0],
        ], null, true);
        $is_member = isset($this->userInfo['level']) ? $this->userInfo['level'] : 0;
        //不登录也能查看
        $task_list = SpecialCourse::getSpecialSourceList($special_id, $limit, $page, $this->uid, $is_member);
        if (!$task_list['list']) return JsonService::successful([]);
        foreach ($task_list['list'] as $k => $v) {
            $task_list['list'][$k]['type_name'] = SPECIAL_TYPE[$v['type']];
            if (!isset($task_list['list'][$k]['special_task'])) {
                $task_list['list'][$k]['watch'] = SpecialWatch::whetherWatch($this->uid, $special_id, $v['id']);
            }
        }
        return JsonService::successful($task_list);
    }

    /**
     * 获取专栏套餐 专栏关联的专题
     */
    public function get_cloumn_task()
    {
        list($page, $limit, $special_id, $source_id) = UtilService::getMore([
            ['page', 1],
            ['limit', 10],
            ['special_id', 0],
            ['source_id', 0],
        ], null, true);
        $is_member = isset($this->userInfo['level']) ? $this->userInfo['level'] : 0;
        $task_list = SpecialCourse::get_cloumn_special($special_id, $source_id, $limit, $page, $this->uid, $is_member);
        if (!$task_list['list']) return JsonService::successful([]);
        foreach ($task_list['list'] as $k => $v) {
            $task_list['list'][$k]['type_name'] = SPECIAL_TYPE[$v['type']];
            if ($v['is_light']) {
                $task_list['list'][$k]['type'] = SpecialModel::lightType($v['light_type']);
            }
        }
        return JsonService::successful($task_list);
    }

    /**
     * 播放数量增加
     * @param int $task_id 任务id
     * @return json
     * */
    public function play_num($task_id = 0, $special_id = 0)
    {
        if ($task_id == 0 || $special_id == 0) return JsonService::fail('缺少参数');
        try {
            $add_task_play_count = SpecialTask::bcInc($task_id, 'play_count', 1);
            if ($add_task_play_count) {
                $special_source = SpecialSource::getSpecialSource((int)$special_id, [$task_id]);
                if ($special_source) {
                    SpecialSource::where(['special_id' => $special_id, 'source_id' => $task_id])->setInc('play_count', 1);
                }
                return JsonService::successful('ok');
            } else {
                return JsonService::fail('err');
            }
        } catch (\Exception $e) {
            return JsonService::fail('err');
        }
    }

    /**
     * 播放任务
     * @param int $task_id 任务id
     * @return string
     * */
    public function play($task_id = 0)
    {
        if (!$task_id) $this->failed('无法访问', Url::build('index/index'));
        Session::set('video_token_' . $task_id, md5(time() . $task_id), 'wap');
        $tash = SpecialTask::get($task_id);
        if (!$tash) $this->failed('您查看的资源不存在', Url::build('index/index'));
        if ($tash->is_show == 0) $this->failed('您查看的资源已下架', Url::build('index/index'));
        $this->assign('link', Trust($tash->link));
        $this->assign('task_id', $task_id);
        return $this->fetch();
    }

    public function go_video($task_id = 0)
    {
        if (Cookie::has('video_token_count_' . $task_id)) {
            Cookie::set('video_token_count_' . $task_id, Cookie::get('video_token_count_' . $task_id) + 1);
        } else {
            Cookie::set('video_token_count_' . $task_id, 1);
        }
        if (Session::has('video_token_' . $task_id)) {
            $tash = SpecialTask::get($task_id);
            if (Cookie::get('video_token_count_' . $task_id) >= 2) {
                Session::delete('video_token_' . $task_id);
            }
            exit(file_get_contents($tash->link));
        } else {
            throw new HttpException(404, '您查看的链接不存在');
        }
    }

    /**支付接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create_order()
    {
        list($special_id, $pay_type_num, $payType, $from, $useGold, $pinkId, $total_num, $link_pay_uid, $key, $mark, $price_id, $event) = UtilService::PostMore([
            ['special_id', 0],
            ['pay_type_num', -1],
            ['payType', 'weixin'],
            ['from', 'weixin'],
            ['useGold', 0],
            ['pinkId', 0],
            ['total_num', 1],
            ['link_pay_uid', 0],
            ['key', ''],
            ['mark', ''],
            ['price_id', 0],
            ['event', []],
        ], $this->request, true);
        switch ($pay_type_num) {
            case 10://会员支付
                $this->create_member_order($special_id, $payType, $from);
                break;
            case 20://报名支付
                $this->create_activity_order($special_id, $payType, $price_id, $event, $from);
                break;
            case 30://虚拟币充值
                $auth_api = new AuthApi();
                $auth_api->user_wechat_recharge($special_id, $payType, $from);
                break;
            case 40: //商品购买
                $this->create_goods_order($special_id, $payType, $key, $useGold, $mark, $from);
                break;
            case 50: //订单再次支付
                $this->pay_order($special_id, $payType, $from);
                break;
            case 60: //试卷购买
                $this->create_test_paper_order($special_id, $payType, $from);
                break;
            case 70: //资料购买
                $this->create_data_download_order($special_id, $payType, $from);
                break;
            default://专题支付
                $this->create_special_order($special_id, $pay_type_num, $payType, $pinkId, $total_num, $link_pay_uid, $from);
        }
    }

    /**创建试卷支付订单
     * @param $test_id
     * @param $payType
     */
    public function create_data_download_order($data_id, $payType, $from = 'weixin')
    {
        $data = DataDownload::PreWhere()->find($data_id);
        if (!$data) return JsonService::status('ORDER_ERROR', '购买的资料不存在');
        $order = DataDownloadOrder::createDataDownloadOrder($data, $this->uid, $payType, 1);
        $orderId = $order['order_id'];
        $info = compact('orderId');
        if ($orderId) {
            $orderInfo = DataDownloadOrder::where('order_id', $orderId)->where('is_del', 0)->find();
            if (!$orderInfo || !isset($orderInfo['paid'])) return JsonService::status('pay_error', '支付订单不存在!');
            if ($orderInfo['paid']) return JsonService::status('pay_error', '支付已支付!');
            if (bcsub((float)$orderInfo['pay_price'], 0, 2) <= 0) {
                if (DataDownloadOrder::jsPayDataDownloadPrice($orderId, $this->uid))
                    return JsonService::status('success', '支付成功', $info);
                else
                    return JsonService::status('pay_error', DataDownloadOrder::getErrorInfo());
            } else {
                switch ($payType) {
                    case 'weixin':
                        try {
                            if ($from == 'weixinh5') {
                                $jsConfig = DataDownloadOrder::h5DataDownloadPay($orderId);
                            } else {
                                $jsConfig = DataDownloadOrder::jsDataDownloadPay($orderId);
                            }
                        } catch (\Exception $e) {
                            return JsonService::status('pay_error', $e->getMessage(), $info);
                        }
                        $info['jsConfig'] = $jsConfig;
                        if ($from == 'weixinh5') {
                            return JsonService::status('wechat_h5_pay', '订单创建成功', $info);
                        } else {
                            return JsonService::status('wechat_pay', '订单创建成功', $info);
                        }
                        break;
                    case 'yue':
                        if (DataDownloadOrder::yueDataDownloadPay($orderId, $this->uid))
                            return JsonService::status('success', '余额支付成功', $info);
                        else
                            return JsonService::status('pay_error', DataDownloadOrder::getErrorInfo());
                        break;
                    case 'zhifubao':
                        $info['pay_price'] = $orderInfo['pay_price'];
                        $info['orderName'] = '资料购买';
                        return JsonService::status('zhifubao_pay', '订单创建成功', base64_encode(json_encode($info)));
                        break;
                }
            }
        } else {
            return JsonService::fail(DataDownloadOrder::getErrorInfo('订单生成失败!'));
        }
    }


    /**创建试卷支付订单
     * @param $test_id
     * @param $payType
     */
    public function create_test_paper_order($test_id, $payType, $from = 'weixin')
    {
        $testPaper = TestPaper::PreExercisesWhere()->find($test_id);
        if (!$testPaper) return JsonService::status('ORDER_ERROR', '购买的试卷不存在');
        $order = TestPaperOrder::createTestPaperOrder($testPaper, $this->uid, $payType, 1);
        $orderId = $order['order_id'];
        $info = compact('orderId');
        if ($orderId) {
            $orderInfo = TestPaperOrder::where('order_id', $orderId)->where('is_del', 0)->find();
            if (!$orderInfo || !isset($orderInfo['paid'])) return JsonService::status('pay_error', '支付订单不存在!');
            if ($orderInfo['paid']) return JsonService::status('pay_error', '支付已支付!');
            if (bcsub((float)$orderInfo['pay_price'], 0, 2) <= 0) {
                if (TestPaperOrder::jsPayTestPaperPrice($orderId, $this->uid))
                    return JsonService::status('success', '支付成功', $info);
                else
                    return JsonService::status('pay_error', TestPaperOrder::getErrorInfo());
            } else {
                switch ($payType) {
                    case 'weixin':
                        try {
                            if ($from == 'weixinh5') {
                                $jsConfig = TestPaperOrder::h5TestPaperPay($orderId);
                            } else {
                                $jsConfig = TestPaperOrder::jsTestPaperPay($orderId);
                            }
                        } catch (\Exception $e) {
                            return JsonService::status('pay_error', $e->getMessage(), $info);
                        }
                        $info['jsConfig'] = $jsConfig;
                        if ($from == 'weixinh5') {
                            return JsonService::status('wechat_h5_pay', '订单创建成功', $info);
                        } else {
                            return JsonService::status('wechat_pay', '订单创建成功', $info);
                        }
                        break;
                    case 'yue':
                        if (TestPaperOrder::yueTestPaperPay($orderId, $this->uid))
                            return JsonService::status('success', '余额支付成功', $info);
                        else
                            return JsonService::status('pay_error', TestPaperOrder::getErrorInfo());
                        break;
                    case 'zhifubao':
                        $info['pay_price'] = $orderInfo['pay_price'];
                        $info['orderName'] = '试卷购买';
                        return JsonService::status('zhifubao_pay', '订单创建成功', base64_encode(json_encode($info)));
                        break;
                }
            }
        } else {
            return JsonService::fail(TestPaperOrder::getErrorInfo('订单生成失败!'));
        }
    }

    /**
     * 创建专题支付订单
     * @param int $special_id 专题id
     * @param int $pay_type 购买类型 1=礼物,2=普通购买,3=开团或者拼团
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create_special_order($special_id, $pay_type_num, $payType, $pinkId, $total_num, $link_pay_uid, $from = 'weixin')
    {
        if (!$special_id) return JsonService::fail('缺少购买参数');
        if ($pay_type_num == -1) return JsonService::fail('选择购买方式');
        if ($pinkId && $pay_type_num != 1) {
            $orderId = StoreOrder::getStoreIdPink($pinkId);
            if (StorePink::getIsPinkUid($pinkId)) return JsonService::status('ORDER_EXIST', '订单生成失败，你已经在该团内不能再参加了', ['orderId' => $orderId]);
            if (StoreOrder::getIsOrderPink($pinkId)) return JsonService::status('ORDER_EXIST', '订单生成失败，你已经参加该团了，请先支付订单', ['orderId' => $orderId]);
            if (StorePink::getPinkStatusIng($pinkId)) return JsonService::status('ORDER_EXIST', '拼团已完成或者已过期无法参团', ['orderId' => $orderId]);
            if (StorePink::be(['uid' => $this->uid, 'type' => 1, 'cid' => $special_id, 'status' => 1])) return JsonService::status('ORDER_EXIST', '您已参见本专题的拼团,请结束后再进行参团');
            if (SpecialBuy::be(['uid' => $this->uid, 'special_id' => $special_id, 'is_del' => 0])) return JsonService::status('ORDER_EXIST', '您已获得此专题,不能再进行参团!');
            //处理拼团完成
            try {
                if ($pink = StorePink::get($pinkId)) {
                    list($pinkAll, $pinkT, $count, $idAll, $uidAll) = StorePink::getPinkMemberAndPinkK($pink);
                    if ($pinkT['status'] == 1) {
                        if (!$count || $count < 0) {
                            StorePink::PinkComplete($uidAll, $idAll, $pinkT['uid'], $pinkT);
                            return JsonService::status('ORDER_EXIST', '当前拼团已完成，无法参团');
                        } else
                            StorePink::PinkFail($pinkT['uid'], $idAll, $pinkAll, $pinkT, $count, 0, $uidAll);
                    } else if ($pinkT['status'] == 2) {
                        return JsonService::status('ORDER_EXIST', '当前拼团已完成，无法参团');
                    } else if ($pinkT['status'] == 3) {
                        return JsonService::status('ORDER_EXIST', '拼团失败，无法参团');
                    }
                }
            } catch (\Exception $e) {
                return JsonService::status('ORDER_EXIST', $e->getMessage());
            }
        }
        $special = SpecialModel::PreWhere()->find($special_id);
        if (!$special) return JsonService::status('ORDER_ERROR', '购买的专题不存在');
        $order = StoreOrder::createSpecialOrder($special, $pinkId, $pay_type_num, $this->uid, $payType, $link_pay_uid, $total_num);
        $orderId = $order['order_id'];
        $info = compact('orderId');
        if ($orderId) {
            $orderInfo = StoreOrder::where('order_id', $orderId)->find();
            if (!$orderInfo || !isset($orderInfo['paid'])) return JsonService::status('pay_error', '支付订单不存在!');
            if ($orderInfo['paid']) return JsonService::status('pay_error', '支付已支付!');
            if (bcsub((float)$orderInfo['pay_price'], 0, 2) <= 0) {
                if (StoreOrder::jsPayPrice($orderId, $this->uid))
                    return JsonService::status('success', '支付成功', $info);
                else
                    return JsonService::status('pay_error', StoreOrder::getErrorInfo());
            } else {
                switch ($payType) {
                    case 'weixin':
                        try {
                            if ($from == 'weixinh5') {
                                $jsConfig = StoreOrder::h5SpecialPay($orderId);
                            } else {
                                $jsConfig = StoreOrder::jsSpecialPay($orderId);
                            }
                        } catch (\Exception $e) {
                            return JsonService::status('pay_error', $e->getMessage(), $info);
                        }
                        $info['jsConfig'] = $jsConfig;
                        if ($from == 'weixinh5') {
                            return JsonService::status('wechat_h5_pay', '订单创建成功', $info);
                        } else {
                            return JsonService::status('wechat_pay', '订单创建成功', $info);
                        }
                        break;
                    case 'yue':
                        if (StoreOrder::yuePay($orderId, $this->uid))
                            return JsonService::status('success', '余额支付成功', $info);
                        else
                            return JsonService::status('pay_error', StoreOrder::getErrorInfo());
                        break;
                    case 'zhifubao':
                        $info['pay_price'] = $orderInfo['pay_price'];
                        $info['orderName'] = '专题购买';
                        return JsonService::status('zhifubao_pay', '订单创建成功', base64_encode(json_encode($info)));
                        break;
                }
            }
        } else {
            return JsonService::fail(StoreOrder::getErrorInfo('订单生成失败!'));
        }
    }

    /**会员订单创建
     * @param $id
     * @param $payType
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create_member_order($id, $payType, $from = 'weixin')
    {
        if (!$id) return JsonService::fail('参数错误!');
        $order = StoreOrder::cacheMemberCreateOrder($this->uid, $id, $payType);
        $orderId = $order['order_id'];
        $info = compact('orderId');
        if ($orderId) {
            $orderInfo = StoreOrder::where('order_id', $orderId)->find();
            if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
            if ($orderInfo['paid']) exception('支付已支付!');
            if (bcsub((float)$orderInfo['pay_price'], 0, 2) <= 0) {
                if (StoreOrder::jsPayMePrice($orderId, $this->uid))
                    return JsonService::status('success', '领取成功', $info);
                else
                    return JsonService::status('pay_error', StoreOrder::getErrorInfo());
            } else {
                switch ($payType) {
                    case 'weixin':
                        try {
                            if ($from == 'weixinh5') {
                                $jsConfig = StoreOrder::h5PayMember($orderId);
                            } else {
                                $jsConfig = StoreOrder::jsPayMember($orderId);
                            }
                        } catch (\Exception $e) {
                            return JsonService::status('pay_error', $e->getMessage(), $info);
                        }
                        $info['jsConfig'] = $jsConfig;
                        if ($from == 'weixinh5') {
                            return JsonService::status('wechat_h5_pay', '订单创建成功', $info);
                        } else {
                            return JsonService::status('wechat_pay', '订单创建成功', $info);
                        }
                        break;
                    case 'zhifubao':
                        $info['pay_price'] = $orderInfo['pay_price'];
                        $info['orderName'] = '会员购买';
                        return JsonService::status('zhifubao_pay', '订单创建成功', base64_encode(json_encode($info)));
                        break;
                }
            }
        } else {
            return JsonService::fail(StoreOrder::getErrorInfo('领取失败!'));
        }
    }

    /**
     * 用户提交报名
     */
    public function create_activity_order($id, $payType, $price_id, $event, $from = 'weixin')
    {
        if (!$id) JsonService::fail('参数有误');
        $order = EventSignUp::userEventSignUp($id, $price_id, json_encode($event), $payType, $this->uid);
        $orderId = $order['order_id'];
        $info = compact('orderId');
        if ($orderId) {
            $orderInfo = EventSignUp::where('order_id', $orderId)->find();
            if (!$orderInfo || !isset($orderInfo['paid'])) return JsonService::status('pay_error', '支付订单不存在!');
            if ($orderInfo['paid']) return JsonService::status('pay_error', '支付已支付!');
            if (bcsub((float)$orderInfo['pay_price'], 0, 2) <= 0) {
                if (EventSignUp::jsPayPrice($orderId, $this->uid))
                    return JsonService::status('success', '支付成功', $info);
                else
                    return JsonService::status('pay_error', EventSignUp::getErrorInfo());
            } else {
                switch ($payType) {
                    case 'weixin':
                        try {
                            if ($from == 'weixinh5') {
                                $jsConfig = EventSignUp::h5Pay($orderId);
                            } else {
                                $jsConfig = EventSignUp::jsPay($orderId);
                            }
                        } catch (\Exception $e) {
                            return JsonService::status('pay_error', $e->getMessage(), $info);
                        }
                        $info['jsConfig'] = $jsConfig;
                        if ($from == 'weixinh5') {
                            return JsonService::status('wechat_h5_pay', '订单创建成功', $info);
                        } else {
                            return JsonService::status('wechat_pay', '订单创建成功', $info);
                        }
                        break;
                    case 'yue':
                        if (EventSignUp::yuePay($orderId, $this->uid))
                            return JsonService::status('success', '余额支付成功', $info);
                        else
                            return JsonService::status('pay_error', EventSignUp::getErrorInfo());
                        break;
                    case 'zhifubao':
                        $info['pay_price'] = $orderInfo['pay_price'];
                        $info['orderName'] = '活动报名';
                        return JsonService::status('zhifubao_pay', '订单创建成功', base64_encode(json_encode($info)));
                        break;
                }
            }
        } else {
            return JsonService::fail(EventSignUp::getErrorInfo('订单生成失败!'));
        }
    }

    /**
     * 购买完成后送礼物页面
     * @param string $orderId 订单id
     * @return strign
     * */
    public function gift_special($orderId = null)
    {
        if (is_null($orderId)) $this->failed('缺少订单号,无法进行赠送', Url::build('my/my_gift'));
        if (!$this->uid) $this->failed('未获取到用户信息！', Url::build('index/index'));
        $special = StoreOrder::getOrderIdToSpecial($orderId, $this->uid);
        if ($special === false) $this->failed(StoreOrder::getErrorInfo(), Url::build('my/my_gift'));
        if ($special['is_light']) {
            $site_url = SystemConfigService::get('site_url') . Url::build('special/single_details') . '?id=' . $special['id'] . '&gift_uid=' . $this->uid . '&gift_order_id=' . $orderId . '&gift=1&spread_uid=' . $this->uid;
        } else {
            $site_url = SystemConfigService::get('site_url') . Url::build('special/details') . '?id=' . $special['id'] . '&gift_uid=' . $this->uid . '&gift_order_id=' . $orderId . '&gift=1&spread_uid=' . $this->uid;
        }
        $this->assign([
            'orderId' => $orderId,
            'title' => '赠送礼物',
            'site_url' => $site_url,
            'special' => $special
        ]);
        return $this->fetch();
    }

    /**
     * 查看领取记录
     * @param $orderId string 订单id
     * @return html
     * */
    public function gift_receive($orderId = null)
    {
        if (is_null($orderId)) $this->failed('缺少订单号,无法查看领取记录', Url::build('my/my_gift'));
        $special = StoreOrder::getOrderIdGiftReceive($orderId);
        if ($special === false) $this->failed(StoreOrder::getErrorInfo(), Url::build('my/my_gift'));
        $this->assign($special);
        return $this->fetch();
    }

    /**
     * 购买失败删除订单
     * @param string $orderId 订单id
     * @return json
     * */
    public function del_order($orderId = '')
    {
        if (StoreOrder::where('order_id', $orderId)->update(['is_del' => 1]))
            return JsonService::successful();
        else
            return JsonService::fail();
    }

    public function grade_list($type = 0)
    {
        $this->assign(compact('type'));
        return $this->fetch();
    }

    public function grade_special($type = 0)
    {
        return $this->fetch();
    }

    /**
     * 获取我的收藏
     * @param int $type 课程类型
     * @param int $page 分页
     * @param int $limit 一页显示多少条
     * @return json
     * */
    public function get_grade_list()
    {
        list($page, $limit, $active) = UtilService::GetMore([
            ['page', 1],
            ['limit', 10],
            ['active', 0],
        ], $this->request, true);
        $is_member = isset($this->userInfo['level']) ? $this->userInfo['level'] : 0;
        return JsonService::successful(SpecialModel::getGradeList((int)$page, (int)$limit, $this->uid, $is_member, $active));
    }

    /**
     * 获取我购买的课程
     * @param int $type 课程类型
     * @param int $page 分页
     * @param int $limit 一页显示多少条
     * @return json
     * */
    public function get_my_grade_list()
    {
        list($page, $limit, $active) = UtilService::GetMore([
            ['page', 1],
            ['limit', 10],
            ['active', 0],
        ], $this->request, true);
        $is_member = isset($this->userInfo['level']) ? $this->userInfo['level'] : 0;
        return JsonService::successful(SpecialModel::getMyGradeList((int)$page, (int)$limit, $this->uid, $is_member, $active));
    }

    /**
     * 拼团成功朋友圈海报展示
     * @param $special_id int 专题id
     * @return html
     * */
    public function poster_show($special_id = 0, $pinkId = 0, $is_help = 0)
    {
        if (!$special_id || !$pinkId) $this->failed('您查看的朋友去圈海报不存在', Url::build('spread/special'));
        $special = SpecialModel::getSpecialInfo($special_id);
        if ($special === false) $this->failed(SpecialModel::getErrorInfo(), Url::build('spread/special'));
        if (!$special['poster_image']) $this->failed('您查看的海报不存在', Url::build('spread/special'));
        $site_url = SystemConfigService::get('site_url') . Url::build('special/pink') . '?pink_id=' . $pinkId . '&special_id=' . $special['id'] . '&is_help=1&spread_uid=' . $this->uid;
        $this->assign(['url' => $site_url, 'is_help' => $is_help, 'special' => json_encode($special)]);
        return $this->fetch();
    }

    /**
     * 获取专题弹幕
     * @param int $special_id 专题id
     * @return json
     * */
    public function get_barrage_list($special_id = 0)
    {
        if (!$special_id) return JsonService::fail('确实参数！');
        if (SystemConfigService::get('open_barrage')) {
            $barrage = SpecialBarrage::where('is_show', 1)->order('sort desc,id desc')->field(['nickname', 'avatar', 'action'])->select();
            $barrage = count($barrage) ? $barrage->toArray() : [];
            foreach ($barrage as &$item) {
                $item['status_name'] = $item['action'] == 1 ? '1秒前发起了拼团' : '1秒前成功参团';
                unset($item['action']);
            }
            $special = SpecialModel::where('id', $special_id)->find();
            if (!$special) return JsonService::fail('确实参数！');
            if ($special['is_pink']) {
                $pinkList = StoreOrder::where(['o.cart_id' => $special_id, 'p.is_refund' => 0, 'o.refund_status' => 0, 'o.paid' => 1, 'p.is_false' => 0])
                    ->join("__STORE_PINK__ p", 'p.order_id=o.order_id')
                    ->join('__USER__ u', 'u.uid=o.uid')
                    ->field(['u.nickname', 'u.avatar', 'p.status', 'p.k_id'])
                    ->group('o.order_id')
                    ->order('o.add_time desc')
                    ->alias('o')
                    ->select();
                $pinkList = count($pinkList) ? $pinkList->toArray() : [];
                foreach ($pinkList as &$item) {
                    if ($item['status'] == 2 && $item['k_id'] == 0) {
                        $item['status_name'] = '1秒前拼团成功';
                    } else if ($item['status'] == 1 && $item['k_id'] == 0)
                        $item['status_name'] = '1秒前发起了拼团';
                    else if ($item['status'] == 2 && $item['k_id'] != 0)
                        $item['status_name'] = '1秒前拼团成功';
                    else if ($item['status'] == 1 && $item['k_id'] != 0)
                        $item['status_name'] = '1秒前发起了拼团';
                    else if ($item['status'] == 3)
                        $item['status_name'] = '1秒前参团成功';
                    unset($item['status'], $item['k_id']);
                }
                $barrageList = array_merge($pinkList, $barrage);
                shuffle($barrageList);
            } else {
                $barrageList = [];
            }
        } else $barrageList = [];
        return JsonService::successful($barrageList);
    }

    /**
     * 拼团列表
     */
    public function groupLists($special_id = 0)
    {
        if (!$special_id) return JsonService::fail('确实参数！');
        $special = SpecialModel::where('id', $special_id)->find();
        if (!$special) return JsonService::fail('确实参数！');
        if ($special['is_pink']) {
            $pinkList = StoreOrder::where(['o.cart_id' => $special_id, 'p.is_refund' => 0, 'o.refund_status' => 0, 'o.paid' => 1, 'p.is_false' => 0])
                ->join("__STORE_PINK__ p", 'p.order_id=o.order_id')
                ->join('__USER__ u', 'u.uid=o.uid')
                ->field(['u.nickname', 'u.avatar', 'p.status', 'p.k_id'])
                ->group('o.order_id')
                ->order('o.add_time desc')
                ->alias('o')
                ->select();
            $pinkList = count($pinkList) ? $pinkList->toArray() : [];
        } else {
            $pinkList = [];
        }
        return JsonService::successful($pinkList);
    }

    /**
     * 获取滚动index
     * @param int $index
     */
    public function set_barrage_index($index = 0)
    {
        return JsonService::successful(Cookie::set('barrage_index', $index));
    }

    /**
     * 专题分类
     * @return mixed
     */
    public function special_cate($cate_id = 0, $subject_id = 0)
    {
        $this->assign([
            'homeLogo' => SystemConfigService::get('home_logo'),
            'cate_id' => (int)$cate_id,
            'subject_id' => (int)$subject_id
        ]);
        return $this->fetch();
    }

    /**
     * 获取课程分类
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_grade_cate()
    {
        $cateogry = SpecialSubject::with('children')->where(['is_show' => 1, 'is_del' => 0])->order('sort desc,id desc')->where('grade_id', 0)->select();
        return JsonService::successful($cateogry->toArray());
    }

    /**
     * 获取专题
     * @param int $grade_id 一级分类ID
     * @param int $subject_id 二级分类ID
     * @param string $search
     * @param int $page
     * @param int $limit
     * @param int $type 学习记录获取专题使用
     */
    public function get_special_list($grade_id = 0, $subject_id = 0, $search = '', $page = 1, $limit = 10, $type = 0)
    {
        $uid = $this->uid;
        $is_member = isset($this->userInfo['level']) ? $this->userInfo['level'] : 0;
        return JsonService::successful(SpecialModel::getSpecialList(compact('grade_id', 'subject_id', 'search', 'page', 'limit', 'type', 'uid', 'is_member')));
    }

    /**
     * 获取拼团专题
     */
    public function get_pink_special_list()
    {
        list($page, $limit) = UtilService::PostMore([
            ['page', 1],
            ['limit', 10]
        ], $this->request, true);
        return JsonService::successful(SpecialModel::getPinkSpecialList($page, $limit));
    }

    /**
     * 学习记录
     * @return mixed
     */
    public function record()
    {
        $this->assign(['homeLogo' => SystemConfigService::get('home_logo')]);
        return $this->fetch();
    }

    /**
     * 是否可以播放
     * @param int $task_id 任务id
     * @return string
     * */
    public function get_task_link($task_id = 0, $special_id = 0)
    {
        if (!$special_id || !$task_id) return JsonService::fail('缺少参数');
        $special_source = SpecialSource::getSpecialSource($special_id, [$task_id]);
        $tash = $special_source ? $special_source->toArray() : [];
        if (!$tash) {
            return JsonService::fail('您查看的视频已经下架');
        } else {
            return JsonService::successful($tash);
        }
    }

    /**
     * 课程详情
     * @param $id
     * @return mixed|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function task_info($id = 0, $specialId = 0)
    {
        if (!$id) $this->failed('缺少课程id,无法查看', Url::build('index/index'));
        $this->assign(['specialId' => $specialId, 'task_id' => $id]);
        return $this->fetch();
    }

    /**检测用户身份
     * @throws \Exception
     */
    public function isMember()
    {
        $user_level = !$this->uid ? 0 : $this->userInfo;
        $data['is_member'] = isset($user_level['level']) ? $user_level['level'] : 0;
        $data['now_money'] = isset($user_level['now_money']) ? $user_level['now_money'] : 0;
        return JsonService::successful($data);
    }

    /**
     * 图文素材详情
     */
    public function task_text_info($id = 0, $specialId = 0)
    {
        if (!$id) $this->failed('缺少课程id,无法查看', Url::build('index/index'));
        $this->assign(['specialId' => $specialId, 'task_id' => $id]);
        return $this->fetch('text_detail');
    }

    /**
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * special_id 专题ID task_id素材ID
     */
    public function getTaskInfo()
    {
        $data = UtilService::PostMore([
            ['special_id', 0],
            ['task_id', 0]
        ], $this->request);
        $taskInfo = SpecialTask::defaultWhere()->where('id', $data['task_id'])->find();
        $special = SpecialModel::PreWhere()->where('id', $data['special_id'])->field('pay_type,money,member_pay_type,member_money')->find();
        if (!$special) return JsonService::fail('您查看的专题不存在');
        if (!$taskInfo) return JsonService::fail('课程信息不存在无法观看');
        if ($taskInfo['is_show'] == 0) return JsonService::fail('该课程已经下架');
        $isPay = SpecialBuy::PaySpecial($data['special_id'], $this->uid);
        if ($taskInfo['type'] == 1) {
            $content = htmlspecialchars_decode($taskInfo->content ? $taskInfo->content : "");
        } else {
            $special_content = SpecialContent::where('special_id', $data['special_id'])->value("content");
            $content = htmlspecialchars_decode($taskInfo->detail ? $taskInfo->detail : $special_content);
        }
        $taskInfo->content = $content;
        $is_member = isset($this->userInfo['level']) ? $this->userInfo['level'] : 0;
        if ($isPay || $special->pay_type == 0 || ($is_member > 0 && $special->member_pay_type == 0)) {
            $isPay = true;
            $isSourcePay = true;
        } else {
            $isPay = false;
            $special_source = SpecialSource::where(['special_id' => $data['special_id'], 'source_id' => $data['task_id'], 'pay_status' => 0])->find();
            if (!$special_source) {
                $isSourcePay = false;
                if ($isPay == false && $taskInfo['is_try']) {
                    unset($taskInfo['content']);
                } else if ($isPay == false && !$taskInfo['is_try']) {
                    unset($taskInfo['content'], $taskInfo['videoId'], $taskInfo['link']);
                }
            } else {
                $isSourcePay = true;
                $special_source = $special_source->toArray();
                $taskInfo = SpecialTask::defaultWhere()->where('id', $special_source['source_id'])->find();
                if (!$taskInfo) return JsonService::fail('该素材无法观看');
                $taskInfo->content = $content;
            }
        }

        $site_url = SystemConfigService::get('site_url') . Url::build('special/details') . '?id=' . $data['special_id'] . '&spread_uid=' . $this->uid;
        $array['link_url'] = $site_url;
        $array['taskInfo'] = $taskInfo ? $taskInfo->toArray() : [];
        $array['specialInfo'] = $special->toArray();
        $array['is_member'] = $is_member;
        $array['isPay'] = $isPay;
        $array['isSourcePay'] = $isSourcePay;
        return JsonService::successful($array);
    }

    /**
     * 会员页
     * @return mixed
     */
    public function member_manage($type = 1, $bid = 0)
    {
        $this->assign(['type' => $type, 'bid' => $bid, 'userInfo' => $this->userInfo]);
        return $this->fetch('member/member_manage');
    }

    /**
     * 会员购买页
     * @return mixed
     */
    public function member_recharge()
    {
        $servicePhone = SystemConfigService::get('site_phone') ?: '';
        $spread_poster_url = SystemConfigService::get('spread_poster_url');
        $url = SystemConfigService::get('site_url') . Url::build('special/member_recharge', ['spread_uid' => $this->uid]);
        $this->assign([
            'url' => $url,
            'servicePhone' => $servicePhone,
            'spread_poster_url' => $spread_poster_url
        ]);
        return $this->fetch('member/member_recharge');
    }

    /**
     * 充值页面
     */
    public function recharge_index($from = 'my', $stream_name = "")
    {
        $user_info = $this->userInfo;
        $gold_info = SystemConfigService::more("gold_name,gold_rate,gold_image");
        $recharge_price_list = [60, 100, 300, 500, 980, 1980, 2980, 5180, 15980];
        $gold_name = SystemConfigService::get('gold_name');//虚拟币名称
        $this->assign(compact('gold_name'));
        $this->assign('from', $from);
        $this->assign('stream_name', $stream_name);
        $this->assign('gold_info', json_encode($gold_info));
        $this->assign('recharge_price_list', json_encode($recharge_price_list));
        return $this->fetch('my/gold_coin');
    }

    /**
     * 获取我的虚拟币数量
     */
    public function my_user_gold_num()
    {
        $user_info = $this->userInfo;
        return JsonService::successful(['user_gold_num' => $user_info['gold_num']]);
    }

    /**
     * 储存素材观看时间
     */
    public function viewing()
    {
        $data = UtilService::PostMore([
            ['special_id', 0],
            ['task_id', 0],
            ['viewing_time', 0],
            ['percentage', 0],
            ['total', 0]
        ], $this->request);
        $res = SpecialWatch::materialViewing($this->uid, $data);
        return JsonService::successful($res);
    }

    /**
     * 专题检测是否达到领取证书标准
     * $special_id 专题ID
     * $is_light 是否为轻专题
     */
    public function inspect($special_id = 0, $is_light = 0)
    {
        if (!$this->uid) return JsonService::fail('err');
        $res = CertificateRelated::getCertificateRelated($special_id, $is_light, 1, $this->uid);
        if ($res) {
            return JsonService::successful('ok');
        } else {
            return JsonService::fail('err');
        }
    }

    /**用户领取证书
     * $special_id 专题ID
     */
    public function getTheCertificate($special_id)
    {
        $res = CertificateRecord::getUserTheCertificate($special_id, 1, $this->uid);
        if ($res) return JsonService::successful($res);
        else return JsonService::fail('领取失败');
    }

    /**
     * 活动报名情况
     */
    public function activityType($id)
    {
        $activity = EventRegistration::oneActivitys($id);
        if (!$activity) return JsonService::fail('您查看的活动不存在');
        $data['is_pay'] = 0;//是否购买报名过
        $data['is_restrictions'] = 0; //是否超过限购
        if ($this->uid) {
            $signCount = EventSignUp::setWhere()->where(['activity_id' => $id, 'uid' => $this->uid])->count();
            if ($signCount) {
                $data['is_pay'] = 1;
                if (bcsub($signCount, $activity['restrictions'], 0) >= 0 && $activity['restrictions'] > 0) $data['is_restrictions'] = 1;
            }
        }
        return JsonService::successful($data);
    }

    /**活动报名
     * @param int $id
     * @return mixed
     * @throws \think\Exception
     */
    public function activity_details($id = 0)
    {
        $activity = EventRegistration::oneActivitys($id);
        if (!$activity) $this->failed('您查看的活动不存在', Url::build('activity/index'));
        $this->assign([
            'is_member' => isset($this->userInfo['level']) ? $this->userInfo['level'] : 0,
            'is_fill' => $activity['is_fill'],
            'title' => $activity['title'],
            'activity' => json_encode($activity),
            'activity_rules' => htmlspecialchars_decode($activity['activity_rules']),
            'content' => htmlspecialchars_decode($activity['content'])
        ]);
        return $this->fetch('activity/index');
    }

    /**活动报名信息填写
     * @return mixed
     */
    public function event()
    {
        return $this->fetch('activity/event');
    }

    /**商品订单提交
     * @param string $cartId
     * @return mixed|void
     * @throws \Exception
     */
    public function confirm_order($cartId = '')
    {
        if (!is_string($cartId) || !$cartId) {
            $this->failed('请提交购买的商品!', Url::build('store/index'));
        }
        $user = $this->userInfo;
        $cartGroup = StoreCart::getUserProductCartList($this->uid, $cartId, 1, $user['level']);

        if (count($cartGroup['invalid']))
            $this->failed($cartGroup['invalid'][0]['productInfo']['store_name'] . '已失效!', Url::build('store/index'));
        if (!$cartGroup['valid']) {
            $this->redirect(Url::build('store/index'));
        }
        $cartInfo = $cartGroup['valid'];
        $priceGroup = StoreOrder::getOrderPriceGroup($cartInfo);
        $ratio = SystemConfigService::get('deduction_proportion_ratio');
        $ratio = bcdiv($ratio, 100, 2);
        $gold_name = SystemConfigService::get('gold_name');//虚拟币名称
        $this->assign([
            'level' => $user['level'],
            'gold_num' => $user['gold_num'],
            'gold_name' => $gold_name,
            'cartInfo' => json_encode($cartInfo),
            'cartId' => $cartId,
            'priceGroup' => json_encode($priceGroup),
            'orderKey' => StoreOrder::cacheOrderInfo($this->uid, $cartInfo, $priceGroup),
            'ratio' => $ratio
        ]);
        return $this->fetch('store/order_confirm');
    }

    /**订单提交页修改商品数量
     * @param string $cartId
     * @param int $cateNum
     */
    public function getOrderPrice($cartId = '', $cateNum = 1)
    {
        $res = StoreCart::changeUserCartNum($cartId, $cateNum, $this->uid);
        if (!$res) return JsonService::fail('商品数量修改失败!');
        $user = $this->userInfo;
        $cartGroup = StoreCart::getUserProductCartList($this->uid, $cartId, 1, $user['level']);
        if (count($cartGroup['invalid'])) return JsonService::fail($cartGroup['invalid'][0]['productInfo']['store_name'] . '已失效!');
        if (!$cartGroup['valid']) return JsonService::fail('请提交购买的商品!');
        $cartInfo = $cartGroup['valid'];
        $data['priceGroup'] = StoreOrder::getOrderPriceGroup($cartInfo);
        $data['orderKey'] = StoreOrder::cacheOrderInfo($this->uid, $cartInfo, $data['priceGroup']);
        return JsonService::successful($data);

    }

    /**
     * 创建商品订单
     * @param string $key
     * @return \think\response\Json
     */
    public function create_goods_order($addressId, $payType, $key, $useGold, $mark, $from = 'weixin')
    {
        if (!$key) return JsonService::fail('参数错误!');
        if (StoreOrder::be(['order_id|unique' => $key, 'uid' => $this->uid, 'is_del' => 0, 'type' => 2]))
            return JsonService::status('extend_order', '订单已生成', ['orderId' => $key, 'key' => $key]);
        $payType = strtolower($payType);
        $order = StoreOrder::cacheKeyCreateOrder($this->uid, $key, $addressId, $payType, $useGold, $mark);
        $orderId = $order['order_id'];
        $info = compact('orderId', 'key');
        if ($orderId) {
            $orderInfo = StoreOrder::where('order_id', $orderId)->where('type', 2)->find();
            if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
            if ($orderInfo['paid']) exception('支付已支付!');
            if (bcsub((float)$orderInfo['pay_price'], 0, 2) <= 0) {
                if (StoreOrder::jsPayGoodsPrice($orderId, $this->uid))
                    return JsonService::status('success', '支付成功', $info);
                else
                    return JsonService::status('pay_error', StoreOrder::getErrorInfo());
            } else {
                switch ($payType) {
                    case 'weixin':
                        try {
                            if ($from == 'weixinh5') {
                                $jsConfig = StoreOrder::h5Pay($orderId);
                            } else {
                                $jsConfig = StoreOrder::jsPay($orderId);
                            }
                        } catch (\Exception $e) {
                            return JsonService::status('pay_error', $e->getMessage(), $info);
                        }
                        $info['jsConfig'] = $jsConfig;
                        if ($from == 'weixinh5') {
                            return JsonService::status('wechat_h5_pay', '订单创建成功', $info);
                        } else {
                            return JsonService::status('wechat_pay', '订单创建成功', $info);
                        }
                        break;
                    case 'yue':
                        if (StoreOrder::yueGoodsPay($orderId, $this->uid))
                            return JsonService::status('success', '余额支付成功', $info);
                        else
                            return JsonService::status('pay_error', StoreOrder::getErrorInfo());
                        break;
                    case 'zhifubao':
                        $info['pay_price'] = $orderInfo['pay_price'];
                        $info['orderName'] = '商品购买';
                        return JsonService::status('zhifubao_pay', '订单创建成功', base64_encode(json_encode($info)));
                        break;
                }
            }
        } else {
            return JsonService::fail(StoreOrder::getErrorInfo('订单生成失败!'));
        }
    }

    /**
     * 我的商品订单
     */
    public function order_store_list($type = 9)
    {
        $this->assign(['type' => $type]);
        return $this->fetch('my/order_store_list');
    }

    /**
     * 订单详情
     * @return mixed
     */
    public function order($uni = '')
    {
        if (!$uni || !$order = StoreOrder::getUserOrderDetail($this->uid, $uni)) return $this->redirect(Url::build('wap/my/order_list'));
        $this->assign([
            'gold_name' => SystemConfigService::get('gold_name'),
            'order' => StoreOrder::tidyOrder($order, true, true)
        ]);
        return $this->fetch('my/order');
    }

    /**
     * 支付订单
     * @param string $uni
     * @return \think\response\Json
     */
    public function pay_order($id, $payType, $from = 'weixin')
    {
        if (!$id) return JsonService::fail('参数错误!');
        $order = StoreOrder::where('id', $id)->where('type', 2)->where('uid', $this->uid)->where('is_del', 0)->find();
        if (!$order) return JsonService::fail('订单不存在!');
        if ($order['paid']) return JsonService::fail('该订单已支付!');
        $info['orderId'] = $order['order_id'];
        if ($payType != $order['pay_type']) {
            $res = StoreOrder::where('id', $id)->where('type', 2)->where('uid', $this->uid)->where('is_del', 0)->update(['pay_type' => $payType]);
            if (!$res) return JsonService::fail('订单支付方式修改失败!');
        }
        $order['pay_type'] = $payType;
        if ($payType != 'yue') {
            if ($from == 'weixin' || $from == 'weixinh5') {
                $order['order_id'] = mt_rand(100, 999) . '_' . $order['order_id'];
            }
        }
        if ($payType == 'weixin') {
            try {
                if ($from == 'weixinh5') {
                    $jsConfig = StoreOrder::h5Pay($order);
                } else {
                    $jsConfig = StoreOrder::jsPay($order);
                }
            } catch (\Exception $e) {
                return JsonService::fail($e->getMessage());
            }
            $info['jsConfig'] = $jsConfig;
            if ($from == 'weixinh5') {
                return JsonService::status('wechat_h5_pay', '订单创建成功', $info);
            } else {
                return JsonService::status('wechat_pay', '订单创建成功', $info);
            }
        } else if ($payType == 'yue') {
            if ($res = StoreOrder::yueGoodsPay($order['order_id'], $this->uid))
                return JsonService::status('success', '余额支付成功', $info);
            else
                return JsonService::status('pay_error', StoreOrder::getErrorInfo());
        } else if ($payType == 'zhifubao') {
            $info['pay_price'] = $order['pay_price'];
            $info['orderName'] = '商品购买';
            return JsonService::status('zhifubao_pay', '订单创建成功', base64_encode(json_encode($info)));
        }
    }


    /**
     * 素材详情
     */
    public function source_detail($id = 0)
    {
        if (!$id) return JsonService::fail('缺少参数');
        $this->assign('id', $id);
        return $this->fetch();
    }

    /**获取素材详情
     * @param int $source_id
     */
    public function getSourceDetail($source_id = 0)
    {
        if (!$source_id) return JsonService::fail('缺少参数');
        $taskInfo = SpecialTask::defaultWhere()->where('id', $source_id)->find();
        SpecialTask::bcInc($source_id, 'play_count', 1);
        return JsonService::successful($taskInfo);
    }

    /**相关课程
     * @param int $source_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function relatedCourses($source_id = 0)
    {
        if (!$source_id) return JsonService::fail('缺少参数');
        $specialList = SpecialSource::where('source_id', $source_id)->column('special_id');
        $array = [];
        foreach ($specialList as $key => $value) {
            $special = SpecialModel::PreWhere()->where('id', $value)->find();
            if (!$special) continue;
            $special['count'] = 0;
            $specialSourceId = SpecialSource::getSpecialSource($value);
            if ($specialSourceId) $special['count'] = count($specialSourceId);
            $count = SpecialModel::learning_records($value);
            $special['record'] = processingData(bcadd($count, $special['fake_sales'], 0));
            array_push($array, $special);
        }
        return JsonService::successful($array);
    }

    /**
     * 拼团列表
     */
    public function group_list()
    {
        $this->assign('group_background', SystemConfigService::get('group_background'));
        return $this->fetch();
    }

    /**考试详情
     * @return mixed
     */
    public function question_index($id)
    {
        if (!$id) $this->failed('缺少参数,无法访问');
        $title = TestPaper::PreExercisesWhere()->where('id', $id)->value('title');
        $user_level = !$this->uid ? 0 : $this->userInfo;
        $this->assign(['uid' => $this->uid, 'titles' => $title, 'id' => $id, 'is_member' => isset($user_level['level']) ? $user_level['level'] : 0]);
        return $this->fetch('topic/question_index');
    }

    /**
     * 兑换码兑换专题
     */
    public function exchange($special_id = 0)
    {
        $this->assign('special_id', $special_id);
        return $this->fetch();
    }

    /**
     * 兑换码提交兑换
     */
    public function exchangeSubmit()
    {
        list($special_id, $code) = UtilService::PostMore([
            ['special_id', 0],
            ['code', '']
        ], $this->request, true);
        if (!$special_id || !$code) return JsonService::fail('缺少参数');
        $data = SpecialExchange::userExchangeSubmit($this->uid, $special_id, $code);
        if ($data)
            return JsonService::successful($data);
        else
            return JsonService::fail(SpecialExchange::getErrorInfo('兑换失败!'));
    }

    /**
     * 资料详情
     * @param $id int 资料id
     * @return
     */
    public function data_details($id = 0)
    {
        if (!$id) $this->failed('缺少参数,无法访问', Url::build('index/index'));
        $data = DataDownload::getOneDataDownload($this->uid, $id);
        if ($data === false) $this->failed(DataDownload::getErrorInfo('无法访问'), Url::build('index/index'));
        $is_member = isset($this->userInfo['level']) ? $this->userInfo['level'] : 0;
        $data_money = DataDownload::where('id', $id)->field('money,pay_type')->find();
        if (in_array($data_money['money'], [0, 0.00]) || in_array($data_money['pay_type'], [PAY_NO_MONEY, PAY_PASSWORD])) {
            $isPay = 1;
        } else {
            $isPay = (!$this->uid || $this->uid == 0) ? false : DataDownloadBuy::PayDataDownload($id, $this->uid);
        }
        $site_url = SystemConfigService::get('site_url') . Url::build('special/data_details') . '?id=' . $id . '&spread_uid=' . $this->uid;
        $this->assign($data);
        $this->assign('title', $data['title']);
        $this->assign('site_url', $site_url);
        $this->assign('is_member', $is_member);
        $this->assign('isPay', $isPay);
        return $this->fetch();
    }

    /**专题关联的资料
     * @param int $id
     */
    public function SpecialDataDownload($special_id = 0)
    {
        if (!$special_id) return JsonService::fail('缺少参数,无法访问');
        $data_ids = Relation::setWhere(4, $special_id)->column('relation_id');
        $data = DataDownload::PreWhere()->where('id', 'in', $data_ids)->order('sort desc,id desc')->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        return JsonService::successful($data);
    }
}
