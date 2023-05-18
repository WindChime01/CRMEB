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

namespace app\admin\controller\user;

use app\admin\controller\AuthController;
use app\admin\model\special\Special;
use app\admin\model\special\SpecialSubject;
use app\admin\model\special\SpecialSource;
use app\admin\model\special\SpecialBuy;
use service\FormBuilder as Form;
use service\JsonService;
use think\Db;
use traits\CurdControllerTrait;
use service\JsonService as Json;
use think\Request;
use think\Url;
use app\admin\model\user\User as UserModel;
use app\admin\model\user\UserBill;
use basic\ModelBasic;
use service\HookService;
use behavior\wap\UserBehavior;
use app\admin\model\store\StoreVisit;
use app\admin\model\wechat\WechatMessage;
use app\admin\model\order\StoreOrder;
use app\admin\model\order\DataDownloadOrder;
use app\admin\model\download\DataDownload;
use app\wap\model\store\StoreOrder as StoreOrderModel;
use service\SystemConfigService;
use app\admin\model\user\MemberRecord as MemberRecordModel;
use app\admin\model\user\MemberShip;
use app\admin\model\questions\TestPaperObtain;
use app\admin\model\questions\TestPaper;
use app\admin\model\download\DataDownloadBuy;
use app\admin\model\merchant\Merchant;

/**
 * 用户管理控制器
 * Class User
 * @package app\admin\controller\user
 */
class User extends AuthController
{
    use CurdControllerTrait;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $this->assign(['count_user' => UserModel::count(), 'count_vip' => UserModel::where('level', 1)->count()]);
        $this->assign('gold_name', SystemConfigService::get("gold_name"));
        return $this->fetch();
    }

    /**
     * 修改user表状态
     *
     * @return json
     */
    public function set_status($status = '', $uid = 0, $is_echo = 0)
    {
        if ($is_echo == 0) {
            if ($status == '' || $uid == 0) return Json::fail('参数错误');
            UserModel::where(['uid' => $uid])->update(['status' => $status]);
        } else {
            $uids = parent::postMore([
                ['uids', []]
            ]);
            UserModel::destrSyatus($uids['uids'], $status);
        }
        return Json::successful($status == 0 ? '禁用成功' : '解禁成功');
    }

    public function get_member_list()
    {
        $list = MemberShip::where(['is_publish' => 1, 'is_del' => 0])->field('title,id')->order('sort desc,add_time desc')->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        return Json::successful($list);
    }

    /**
     * 赠送会员
     */
    public function members($uid = 0)
    {
        if (!$uid) return Json::fail('参数错误');
        $this->assign('uid', $uid);
        return $this->fetch();
    }

    /**赠送会员
     * @param int $uid
     */
    public function gift_members()
    {
        $data = parent::postMore([
            ['uid', 0],
            ['type', 0],
            ['day', 0],
            ['member', 0],
        ]);
        if (!$data['uid']) return Json::fail('参数错误');
        $uid = $data['uid'];
        $userInfo = UserModel::where('uid', $uid)->find();
        if (!$userInfo) return Json::fail('用户不存在!');
        if ($userInfo['level'] && $userInfo['is_permanent']) return Json::fail('该用户是永久会员，无需续费!');
        if (!$data['type']) {
            if (!$data['member']) return Json::fail('请选择会员等级');
            $member = MemberShip::where(['id' => $data['member'], 'is_publish' => 1, 'is_del' => 0, 'type' => 1])->find();
            $orderInfo = [
                'uid' => $uid,
                'order_id' => StoreOrderModel::getNewOrderId(),
                'type' => 1,
                'member_id' => $data['member'],
                'total_num' => 1,
                'total_price' => $member['original_price'],
                'pay_price' => 0,
                'pay_type' => 'yue',
                'combination_id' => 0,
                'is_gift' => 0,
                'pink_time' => 0,
                'paid' => 0,
                'pink_id' => 0,
                'unique' => md5(time() . '' . $uid . $data['member']),
                'cost' => $member['original_price'],
                'link_pay_uid' => 0,
                'spread_uid' => $userInfo['spread_uid'] ? $userInfo['spread_uid'] : 0,
                'is_del' => 0,
            ];
            $order = StoreOrderModel::set($orderInfo);
            if ($order) {
                $res1 = UserBill::expend('赠送会员', $uid, 'now_money', 'give_vip', $order['pay_price'], $order['id'], 0, '会员价值' . floatval($order['pay_price']) . '元');
                $res2 = StoreOrderModel::payMeSuccess($order['order_id']);
                if ($res1 && $res2) {
                    return Json::successful('赠送成功');
                } else {
                    return Json::fail('赠送失败');
                }
            } else {
                return Json::fail('赠送失败');
            }
        } else {
            if (!$data['day']) return Json::fail('请填写会员天数');
            $resMer = MemberShip::setUserCustomMember($data['day'], $userInfo);
            if ($resMer) {
                return Json::successful('赠送成功');
            } else {
                return Json::fail('赠送失败');
            }
        }
    }

    /**
     * 清除会员等级
     */
    public function del_vip($uid = 0)
    {
        if (!$uid) return Json::fail('参数错误!');
        $user = UserModel::where('uid', $uid)->where('level', 1)->find();
        if (!$user) return Json::fail('用户不存在或不是会员!');
        $res = UserModel::edit(['level' => 0, 'is_permanent' => 0, 'overdue_time' => 0, 'member_time' => 0], $uid, 'uid');
        if ($res) {
            return Json::successful('清除会员成功');
        } else {
            return Json::fail('清除会员失败');
        }
    }

    /**设置用户的推广人
     * @param string $uid
     */
    public function add_superior($uid = '')
    {
        if (!$uid) return Json::fail('参数错误!');
        $form = Form::create(Url::build('set_superior', ['uid' => $uid]), [
            Form::input('spread_uid', '推广人uid')
        ]);
        $form->setMethod('post')->setTitle('设置用户的推广人')->setSuccessScript('parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},800);');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function set_superior($uid = '')
    {
        if (!$uid) return Json::fail('参数错误');
        $data = parent::postMore([
            ['spread_uid', 0],
        ]);
        if (!$data['spread_uid']) return Json::fail('请填写推广人uid');
        $userInfo = UserModel::where('uid', $data['spread_uid'])->find();
        if (!$userInfo) return Json::fail('推广人不存在');
        $uids = explode(',', $uid);
        if (in_array($data['spread_uid'], $uids)) {
            $key = array_search($data['spread_uid'], $uids);
            unset($uids[$key]);
        }
        $res = UserModel::where('uid', 'in', $uids)->update(['spread_uid' => $data['spread_uid']]);
        if ($res) {
            return Json::successful('设置用户的推广人成功');
        } else {
            return Json::fail('设置用户的推广人失败');
        }
    }

    /**发送试卷
     * @param int $uid
     * @return mixed
     */
    public function send($uid = 0)
    {
        $mer_list = Merchant::getMerchantList();
        $this->assign(['uid' => $uid,'mer_list' => $mer_list]);
        return $this->fetch();
    }

    /**发送提交
     * @param $uid
     * @param $tid
     */
    public function sendTestPaper($uid, $tid)
    {
        $tid = explode(',', $tid);
        $res = TestPaperObtain::addUidSend($uid, $tid);
        if ($res) {
            return Json::successful('发送成功');
        } else {
            return Json::fail('发送失败');
        }
    }

    /**赠送专题
     * @param int $uid
     * @return mixed
     */
    public function relation($uid = 0)
    {
        $mer_list = Merchant::getMerchantList();
        $this->assign(['uid' => $uid, 'mer_list' => $mer_list]);
        return $this->fetch();
    }

    /**
     * 图文、音频、视频、专栏专题素材列表获取 关联
     * @return json
     * */
    public function get_relation_source_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['is_show', 1],
            ['limit', 20],
            ['title', ''],
            ['pid', ''],
            ['type', ''],
            ['mer_id', 0],
            ['special_type', 0],
        ]);
        $special_source = [];

        $special_task = Special::getRelationList($where, $special_source);
        if (isset($special_task['data']) && $special_task['data']) {
            foreach ($special_task['data'] as $k => $v) {
                $special_task['data'][$k]['is_check'] = 0;
                $special_task['data'][$k]['pay_status'] = PAY_MONEY;
                if ($v['type'] == 6) $v['type'] = $v['light_type'];
                $special_task['data'][$k]['types'] = parent::specialTaskType($v['type']);
            }
        }
        return Json::successlayui($special_task);
    }

    public function user_data($uid = 0)
    {
        $spread = UserModel::where(['spread_uid' => $uid])->column('uid');
        $count['pay_count'] = SpecialBuy::where(['uid' => $uid, 'is_del' => 0])->group('special_id')->count();
        $count['bill_count'] = UserBill::where(['category' => 'now_money'])->where('uid', $uid)
            ->where('type', 'in', ['extract'])
            ->count();
        $count['order_count'] = UserBill::where('u.uid', $uid)->alias('u')->join('__STORE_ORDER__ a', 'a.id=u.link_id')
            ->where('u.category', 'now_money')->where('u.type', 'in', ['brokerage'])
            ->where(['a.paid' => 1, 'a.is_gift' => 0, 'a.is_receive_gift' => 0])->count();
        $count['spread_count'] = UserModel::where('uid', 'in', $spread)->count();
        $this->assign('gradeList', json_encode(SpecialSubject::specialCategoryAll(1)));
        $this->assign('uid', $uid);
        $this->assign('count', json_encode($count));
        return $this->fetch();
    }

    public function member_record($uid = 0)
    {
        if (!$uid) return Json::fail('缺少参数');
        $this->assign('uid', $uid);
        return $this->fetch();
    }

    public function get_member_record($uid)
    {
        $data = MemberRecordModel::userOneRecord($uid);
        return Json::successlayui($data);
    }

    public function get_subjec_list($grade_id = 0)
    {
        return Json::successful(SpecialSubject::where(['grade_id' => $grade_id, 'is_show' => 1, 'is_del' => 0])->order('sort desc,add_time desc')->field('id,name')->select());
    }

    public function get_special_list($subjec_id = 0)
    {
        return Json::successful(Special::PreWhere()->where('subject_id', $subjec_id)->order('sort desc,add_time desc')->field('id,title')->select());
    }

    /**赠送专题
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function save_give()
    {
        $data = parent::getMore([
            ['uid', 0],
            ['special_id', 0],
        ]);
        if (!$data['uid']) return Json::fail('请选择被赠送的用户');
        if (!$data['special_id']) return Json::fail('请选择要赠送的专题');
        $special = Special::PreWhere()->where(['id' => $data['special_id']])->find();
        if (SpecialBuy::be(['uid' => $data['uid'], 'special_id' => $data['special_id'], 'is_del' => 0, 'type' => 3])) return Json::fail('此用户已经拥有此专题无需赠送');
        if ($special['type'] == SPECIAL_COLUMN) {
            $special_source = SpecialSource::getSpecialSource($special['id']);
            if ($special_source) {
                foreach ($special_source as $k => $v) {
                    $task_special = Special::PreWhere()->where(['id' => $v['source_id']])->find();
                    if ($task_special['is_show'] == 1) {
                        SpecialBuy::setBuySpecial('', $data['uid'], $v['source_id'], 3, $task_special['validity'], $data['special_id']);
                    }
                }
            }
        }
        $res = SpecialBuy::setBuySpecial('', $data['uid'], $data['special_id'], 3, $special['validity']);
        if ($res) {
            TestPaperObtain::setTestPaper('', $data['uid'], $data['special_id'], 3);
            DataDownloadBuy::setDataDownload('', $data['uid'], $data['special_id'], 2);
            return Json::successful('赠送成功');
        } else
            return Json::fail('赠送失败');
    }

    public function get_user_info($uid = 0)
    {
        if (!$uid) return Json::fail('缺少用户参数');
        return Json::successful(UserModel::getUserinfoV1($uid));
    }

    public function get_pay_list()
    {
        $where = parent::getMore([
            ['uid', 0],
            ['limit', 10],
            ['page', 1],
        ]);
        return Json::successful(SpecialBuy::getPayList($where));
    }

    public function get_spread_list()
    {
        $where = parent::getMore([
            ['uid', 0],
            ['limit', 10],
            ['page', 1],
        ]);
        return Json::successful(UserModel::getSpreadListV1($where));
    }

    public function get_order_list()
    {
        $where = parent::getMore([
            ['uid', 0],
            ['limit', 10],
            ['page', 1],
            ['excel', 0],
            ['start_date', ''],
            ['end_date', ''],
        ]);
        return Json::successful(StoreOrder::getOrderList($where));
    }

    public function get_bill_list()
    {
        $where = parent::getMore([
            ['limit', 10],
            ['page', 1],
            ['uid', 0],
            ['excel', 0],
            ['start_date', ''],
            ['end_date', ''],
        ]);
        return Json::successful(UserBill::getBillList($where, $where['uid']));
    }

    public function update_user_spread($uid = 0, $type = 0)
    {
        if (!$uid || !$type) return Json::fail('缺少参数');
        $user = UserModel::get($uid);
        switch ($type) {
            case '1':
            case "2":
            case "3":
            case '4':
                $user->is_promoter = (int)$type;
                break;
            case "5":
                $user->is_promoter = 1;
                $user->spread_uid = 0;
                break;
        }
        if ($user->save())
            return Json::successful('修改成功');
        else
            return Json::fail('修改失败');
    }

    /**
     * 获取user表
     * @return json
     */
    public function get_user_list()
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
        ]);
        return Json::successlayui(UserModel::getUserList($where));
    }

    /**
     * 编辑模板消息
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function edit($uid)
    {
        if (!$uid) return $this->failed('数据不存在');
        $user = UserModel::get($uid);
        if (!$user) return Json::fail('数据不存在!');
        $f = array();
        $f[] = Form::input('uid', '用户编号', $user->getData('uid'))->disabled(1);
        $f[] = Form::input('nickname', '用户姓名', $user->getData('nickname'));
        $f[] = Form::radio('money_status', '修改余额', 1)->options([['value' => 1, 'label' => '增加'], ['value' => 2, 'label' => '减少']]);
        $f[] = Form::number('nowMoney', '当前余额', $user->getData('now_money'))->disabled(1);
        $f[] = Form::number('money', '金额')->min(0);
        $f[] = Form::radio('is_promoter', '推广员', $user->getData('is_promoter'))->options([
            ['value' => 0, 'label' => '关闭'],
            ['value' => 1, 'label' => '推广员'],
        ]);
        $f[] = Form::radio('is_write_off', '核销员', $user->getData('is_write_off'))->options([
            ['value' => 0, 'label' => '关闭'],
            ['value' => 1, 'label' => '开启'],
        ]);
        $f[] = Form::radio('status', '状态', $user->getData('status'))->options([['value' => 1, 'label' => '开启'], ['value' => 0, 'label' => '锁定']]);
        $form = Form::make_post_form('添加用户通知', $f, Url::build('update', array('id' => $uid)), 2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function update(Request $request, $uid)
    {
        $data = parent::postMore([
            ['money_status', 0],
            ['is_promoter', 1],
            ['is_write_off', 1],
            ['is_senior', 0],
            ['money', 0],
            ['nickname', ''],
            ['integration_status', 0],
            ['integration', 0],
            ['status', 0],
        ], $request);
        if (!$uid) return $this->failed('数据不存在');
        $user = UserModel::get($uid);
        if (!$user) return Json::fail('数据不存在!');
        ModelBasic::beginTrans();
        $res1 = false;
        $res2 = false;
        $edit = array();
        if ($data['money_status'] && $data['money']) {//余额增加或者减少
            if ($data['money_status'] == 1) {//增加
                $edit['now_money'] = bcadd($user['now_money'], $data['money'], 2);
                $res1 = UserBill::income('系统增加余额', $user['uid'], 'now_money', 'system_add', $data['money'], $this->adminId, $edit['now_money'], '系统增加了' . floatval($data['money']) . '余额');
                try {
                    HookService::listen('admin_add_money', $user, $data['money'], false, UserBehavior::class);
                } catch (\Exception $e) {
                    ModelBasic::rollbackTrans();
                    return Json::fail($e->getMessage());
                }
            } else if ($data['money_status'] == 2) {//减少
                $edit['now_money'] = bcsub($user['now_money'], $data['money'], 2);
                $res1 = UserBill::expend('系统减少余额', $user['uid'], 'now_money', 'system_sub', $data['money'], $this->adminId, $edit['now_money'], '系统扣除了' . floatval($data['money']) . '余额');
                try {
                    HookService::listen('admin_sub_money', $user, $data['money'], false, UserBehavior::class);
                } catch (\Exception $e) {
                    ModelBasic::rollbackTrans();
                    return Json::fail($e->getMessage());
                }
            }
        } else {
            $res1 = true;
        }
        if ($data['integration_status'] && $data['integration']) {//积分增加或者减少
            if ($data['integration_status'] == 1) {//增加
                $edit['integral'] = bcadd($user['integral'], $data['integration'], 2);
                $res2 = UserBill::income('系统增加积分', $user['uid'], 'integral', 'system_add', $data['integration'], $this->adminId, $user['integral'], '系统增加了' . floatval($data['integration']) . '积分');
                try {
                    HookService::listen('admin_add_integral', $user, $data['integration'], false, UserBehavior::class);
                } catch (\Exception $e) {
                    ModelBasic::rollbackTrans();
                    return Json::fail($e->getMessage());
                }
            } else if ($data['integration_status'] == 2) {//减少
                $edit['integral'] = bcsub($user['integral'], $data['integration'], 2);
                $res2 = UserBill::expend('系统减少积分', $user['uid'], 'integral', 'system_sub', $data['integration'], $this->adminId, $user['integral'], '系统扣除了' . floatval($data['integration']) . '积分');
                try {
                    HookService::listen('admin_sub_integral', $user, $data['integration'], false, UserBehavior::class);
                } catch (\Exception $e) {
                    ModelBasic::rollbackTrans();
                    return Json::fail($e->getMessage());
                }
            }
        } else {
            $res2 = true;
        }
        $edit['status'] = $data['status'];
        $edit['nickname'] = $data['nickname'];
        $edit['is_promoter'] = $data['is_promoter'];
        $edit['is_write_off'] = $data['is_write_off'];
        $edit['is_senior'] = $data['is_senior'];
        if ($edit) $res3 = UserModel::edit($edit, $uid);
        else $res3 = true;
        if ($res1 && $res2 && $res3) $res = true;
        else $res = false;
        ModelBasic::checkTrans($res);
        if ($res) return Json::successful('修改成功!');
        else return Json::fail('修改失败');
    }

    /**
     * 用户图表
     * @return mixed
     */
    public function user_analysis()
    {
        $where = parent::getMore([
            ['nickname', ''],
            ['status', ''],
            ['is_promoter', ''],
            ['date', ''],
            ['export', 0]
        ], $this->request);
        $user_count = UserModel::consume($where, '', true);
        //头部信息
        $header = [
            [
                'name' => '新增用户',
                'class' => 'fa-line-chart',
                'value' => $user_count,
                'color' => 'red'
            ],
            [
                'name' => '用户留存',
                'class' => 'fa-area-chart',
                'value' => $this->gethreaderValue(UserModel::consume($where, '', true), $where) . '%',
                'color' => 'lazur'
            ],
            [
                'name' => '新增用户总消费',
                'class' => 'fa-bar-chart',
                'value' => '￥' . UserModel::consume($where),
                'color' => 'navy'
            ],
            [
                'name' => '用户活跃度',
                'class' => 'fa-pie-chart',
                'value' => $this->gethreaderValue(UserModel::consume($where, '', true)) . '%',
                'color' => 'yellow'
            ],
        ];
        $name = ['新增用户', '用户消费'];
        $dates = $this->get_user_index($where, $name);
        $user_index = ['name' => json_encode($name), 'date' => json_encode($dates['time']), 'series' => json_encode($dates['series'])];
        //用户浏览分析
        $view = StoreVisit::getVisit($where['date'], ['', 'warning', 'info', 'danger']);
        $view_v1 = WechatMessage::getViweList($where['date'], ['', 'warning', 'info', 'danger']);
        $view = array_merge($view, $view_v1);
        $view_v2 = [];
        foreach ($view as $val) {
            $view_v2['color'][] = '#' . rand(100000, 339899);
            $view_v2['name'][] = $val['name'];
            $view_v2['value'][] = $val['value'];
        }
        $view = $view_v2;
        //消费会员排行用户分析
        $user_null = UserModel::getUserSpend($where['date']);
        //消费数据
        $now_number = UserModel::getUserSpend($where['date'], true);
        list($paren_number, $title) = UserModel::getPostNumber($where['date']);
        if ($paren_number == 0) {
            $rightTitle = [
                'number' => $now_number > 0 ? $now_number : 0,
                'icon' => 'fa-level-up',
                'title' => $title
            ];
        } else {
            $number = (float)bcsub($now_number, $paren_number, 4);
            if ($now_number == 0) {
                $icon = 'fa-level-down';
            } else {
                $icon = $now_number > $paren_number ? 'fa-level-up' : 'fa-level-down';
            }
            $rightTitle = ['number' => $number, 'icon' => $icon, 'title' => $title];
        }
        unset($title, $paren_number, $now_number);
        list($paren_user_count, $title) = UserModel::getPostNumber($where['date'], true, 'add_time', '');
        if ($paren_user_count == 0) {
            $count = $user_count == 0 ? 0 : $user_count;
            $icon = $user_count == 0 ? 'fa-level-down' : 'fa-level-up';
        } else {
            $count = (float)bcsub($user_count, $paren_user_count, 4);
            $icon = $user_count < $paren_user_count ? 'fa-level-down' : 'fa-level-up';
        }
        $leftTitle = [
            'count' => $count,
            'icon' => $icon,
            'title' => $title
        ];
        unset($count, $icon, $title);
        $consume = [
            'title' => '消费金额为￥' . UserModel::consume($where),
            'series' => UserModel::consume($where, 'xiaofei'),
            'rightTitle' => $rightTitle,
            'leftTitle' => $leftTitle,
        ];
        $form = UserModel::consume($where, 'form');
        $grouping = UserModel::consume($where, 'grouping');
        $this->assign(compact('header', 'user_index', 'view', 'user_null', 'consume', 'form', 'grouping', 'where'));
        return $this->fetch();
    }

    public function gethreaderValue($chart, $where = [])
    {
        if ($where) {
            switch ($where['date']) {
                case null:
                case 'today':
                case 'week':
                case 'year':
                    if ($where['date'] == null) {
                        $where['date'] = 'month';
                    }
                    $sum_user = UserModel::whereTime('add_time', $where['date'])->count();
                    if ($sum_user == 0) return 0;
                    $counts = bcdiv($chart, $sum_user, 4) * 100;
                    return $counts;
                    break;
                case 'quarter':
                    $quarter = UserModel::getMonth('n');
                    $quarter[0] = strtotime($quarter[0]);
                    $quarter[1] = strtotime($quarter[1]);
                    $sum_user = UserModel::where('add_time', 'between', $quarter)->count();
                    if ($sum_user == 0) return 0;
                    $counts = bcdiv($chart, $sum_user, 4) * 100;
                    return $counts;
                default:
                    //自定义时间
                    $quarter = explode('-', $where['date']);
                    $quarter[0] = strtotime($quarter[0]);
                    $quarter[1] = strtotime($quarter[1]);
                    $sum_user = UserModel::where('add_time', 'between', $quarter)->count();
                    if ($sum_user == 0) return 0;
                    $counts = bcdiv($chart, $sum_user, 4) * 100;
                    return $counts;
                    break;
            }
        } else {
            $num = UserModel::count();
            $chart = $num != 0 ? bcdiv($chart, $num, 5) * 100 : 0;
            return $chart;
        }
    }

    public function get_user_index($where, $name)
    {
        switch ($where['date']) {
            case null:
                $days = date("t", strtotime(date('Y-m', time())));
                $dates = [];
                $series = [];
                $times_list = [];
                foreach ($name as $key => $val) {
                    for ($i = 1; $i <= $days; $i++) {
                        if (!in_array($i . '号', $times_list)) {
                            array_push($times_list, $i . '号');
                        }
                        $time = $this->gettime(date("Y-m", time()) . '-' . $i);
                        if ($key == 0) {
                            $dates['data'][] = UserModel::where('add_time', 'between', $time)->count();
                        } else if ($key == 1) {
                            $dates['data'][] = UserModel::consume(true, $time);
                        }
                    }
                    $dates['name'] = $val;
                    $dates['type'] = 'line';
                    $series[] = $dates;
                    unset($dates);
                }
                return ['time' => $times_list, 'series' => $series];
            case 'today':
                $dates = [];
                $series = [];
                $times_list = [];
                foreach ($name as $key => $val) {
                    for ($i = 0; $i <= 24; $i++) {
                        $strtitle = $i . '点';
                        if (!in_array($strtitle, $times_list)) {
                            array_push($times_list, $strtitle);
                        }
                        $time = $this->gettime(date("Y-m-d ", time()) . $i);
                        if ($key == 0) {
                            $dates['data'][] = UserModel::where('add_time', 'between', $time)->count();
                        } else if ($key == 1) {
                            $dates['data'][] = UserModel::consume(true, $time);
                        }
                    }
                    $dates['name'] = $val;
                    $dates['type'] = 'line';
                    $series[] = $dates;
                    unset($dates);
                }
                return ['time' => $times_list, 'series' => $series];
            case "week":
                $dates = [];
                $series = [];
                $times_list = [];
                foreach ($name as $key => $val) {
                    for ($i = 0; $i <= 6; $i++) {
                        if (!in_array('星期' . ($i + 1), $times_list)) {
                            array_push($times_list, '星期' . ($i + 1));
                        }
                        $time = UserModel::getMonth('h', $i);
                        if ($key == 0) {
                            $dates['data'][] = UserModel::where('add_time', 'between', [strtotime($time[0]), strtotime($time[1])])->count();
                        } else if ($key == 1) {
                            $dates['data'][] = UserModel::consume(true, [strtotime($time[0]), strtotime($time[1])]);
                        }
                    }
                    $dates['name'] = $val;
                    $dates['type'] = 'line';
                    $series[] = $dates;
                    unset($dates);
                }
                return ['time' => $times_list, 'series' => $series];
            case 'year':
                $dates = [];
                $series = [];
                $times_list = [];
                $year = date('Y');
                foreach ($name as $key => $val) {
                    for ($i = 1; $i <= 12; $i++) {
                        if (!in_array($i . '月', $times_list)) {
                            array_push($times_list, $i . '月');
                        }
                        $t = strtotime($year . '-' . $i . '-01');
                        $arr = explode('/', date('Y-m-01', $t) . '/' . date('Y-m-', $t) . date('t', $t));
                        if ($key == 0) {
                            $dates['data'][] = UserModel::where('add_time', 'between', [strtotime($arr[0]), strtotime($arr[1])])->count();
                        } else if ($key == 1) {
                            $dates['data'][] = UserModel::consume(true, [strtotime($arr[0]), strtotime($arr[1])]);
                        }
                    }
                    $dates['name'] = $val;
                    $dates['type'] = 'line';
                    $series[] = $dates;
                    unset($dates);
                }
                return ['time' => $times_list, 'series' => $series];
            case 'quarter':
                $dates = [];
                $series = [];
                $times_list = [];
                foreach ($name as $key => $val) {
                    for ($i = 1; $i <= 4; $i++) {
                        $arr = $this->gettime('quarter', $i);
                        if (!in_array(implode('--', $arr) . '季度', $times_list)) {
                            array_push($times_list, implode('--', $arr) . '季度');
                        }
                        if ($key == 0) {
                            $dates['data'][] = UserModel::where('add_time', 'between', [strtotime($arr[0]), strtotime($arr[1])])->count();
                        } else if ($key == 1) {
                            $dates['data'][] = UserModel::consume(true, [strtotime($arr[0]), strtotime($arr[1])]);
                        }
                    }
                    $dates['name'] = $val;
                    $dates['type'] = 'line';
                    $series[] = $dates;
                    unset($dates);
                }
                return ['time' => $times_list, 'series' => $series];
            default:
                $list = UserModel::consume($where, 'default');
                $dates = [];
                $series = [];
                $times_list = [];
                foreach ($name as $k => $v) {
                    foreach ($list as $val) {
                        $date = $val['add_time'];
                        if (!in_array($date, $times_list)) {
                            array_push($times_list, $date);
                        }
                        if ($k == 0) {
                            $dates['data'][] = $val['num'];
                        } else if ($k == 1) {
                            $dates['data'][] = UserBill::where(['uid' => $val['uid'], 'type' => 'pay_product'])->sum('number');
                        }
                    }
                    $dates['name'] = $v;
                    $dates['type'] = 'line';
                    $series[] = $dates;
                    unset($dates);
                }
                return ['time' => $times_list, 'series' => $series];
        }
    }

    public function gettime($time = '', $season = '')
    {
        if (!empty($time) && empty($season)) {
            $timestamp0 = strtotime($time);
            $timestamp24 = strtotime($time) + 86400;
            return [$timestamp0, $timestamp24];
        } else if (!empty($time) && !empty($season)) {
            $firstday = date('Y-m-01', mktime(0, 0, 0, ($season - 1) * 3 + 1, 1, date('Y')));
            $lastday = date('Y-m-t', mktime(0, 0, 0, $season * 3, 1, date('Y')));
            return [$firstday, $lastday];
        }
    }

    /**
     * 会员等级首页
     */
    public function group()
    {
        return $this->fetch();
    }

    public function access($uid = 0)
    {
        $this->assign([
            'uid' => $uid,
            'count' => UserModel::getCountData($uid),
        ]);
        return $this->fetch();
    }

    public function getUserBuySpecilList($uid, $page = 1, $limit = 20)
    {
        $list = SpecialBuy::where(['a.uid' => $uid, 'a.is_del' => 0])
            ->join("__SPECIAL__ s", 's.id=a.special_id')->order('a.add_time desc')
            ->alias('a')->field(['a.*', 's.title'])->page((int)$page, (int)$limit)->select();
        $list = count($list) ? $list->toArray() : [];
        return Json::successful($list);
    }

    public function getUserBuyDataList($uid, $page = 1, $limit = 20)
    {
        $list = DataDownloadBuy::where(['a.uid' => $uid, 'a.is_del' => 0])
            ->join("DataDownload d", 'd.id=a.data_id', 'left')->order('a.add_time desc')
            ->alias('a')->field(['a.*', 'd.title'])
            ->page((int)$page, (int)$limit)->select();
        $list = count($list) ? $list->toArray() : [];
        return Json::successful($list);
    }

    public function getUserBuyTestPaperList($uid, $page = 1, $limit = 20)
    {
        $list = TestPaperObtain::where(['a.uid' => $uid, 'a.is_del' => 0])
            ->join("TestPaper t", 't.id=a.test_id', 'left')->order('a.add_time desc')
            ->alias('a')->field(['a.*', 't.title'])
            ->page((int)$page, (int)$limit)->select();
        $list = count($list) ? $list->toArray() : [];
        return Json::successful($list);
    }

    public function del_data_buy($id = 0)
    {
        if ($id == 0) return Json::fail('缺少参数');
        $dataBuy = DataDownloadBuy::get($id);
        if (!$dataBuy) return Json::fail('记录不存在');
        $res = DataDownloadBuy::where('id', $id)->update(['is_del' => 1]);
        if ($res) {
            return Json::successful('删除成功');
        } else
            return Json::fail('删除失败');
    }

    public function del_test_buy($id = 0)
    {
        if ($id == 0) return Json::fail('缺少参数');
        $testBuy = TestPaperObtain::get($id);
        if (!$testBuy) return Json::fail('记录不存在');
        $res = TestPaperObtain::where('id', $id)->update(['is_del' => 1]);
        if ($res) {
            return Json::successful('删除成功');
        } else
            return Json::fail('删除失败');
    }

    public function get_order_buy_list($uid, $page = 1, $limit = 20)
    {
        $data = SpecialBuy::getOrderPayList($uid, $page, $limit);
        return JsonService::successful($data);
    }

    /**
     * 会员详情
     */
    public function see($uid = '')
    {
        $this->assign([
            'uid' => $uid,
            'userinfo' => UserModel::getUserDetailed($uid),
            'is_layui' => true,
            'headerList' => UserModel::getHeaderList($uid),
            'count' => UserModel::getCountInfo($uid),
            'gold_name' => SystemConfigService::get("gold_name")
        ]);
        return $this->fetch();
    }

    public function getBuySpecilList($uid, $page = 1, $limit = 20)
    {
        $list = SpecialBuy::where(['a.uid' => $uid, 'a.is_del' => 0])
            ->join('__STORE_ORDER__ o', 'a.order_id=o.order_id', 'left')
            ->join('__SPECIAL_EXCHANGE__ e', 'a.uid=e.use_uid', 'left')
            ->join("__SPECIAL__ s", 's.id=a.special_id')->order('a.add_time desc')
            ->alias('a')->field(['a.*', 'o.total_num', 'o.pay_price', 's.title', 'e.exchange_code'])->page((int)$page, (int)$limit)->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as &$value) {
            if ($value['type'] == '赠送获得') {
                $value['order_id'] = '赠送获得';
                $value['total_num'] = '1';
                $value['pay_price'] = '0';
            } else if ($value['type'] == '买商品赠送') {
                $value['order_id'] = '买商品赠送';
                $value['total_num'] = '1';
                $value['pay_price'] = '0';
            } else if ($value['type'] == '兑换获得') {
                $value['order_id'] = '兑换码：'.$value['exchange_code'];
                $value['total_num'] = '1';
                $value['pay_price'] = '0';
            }
        }
        return Json::successful($list);
    }

    public function del_special_buy($id = 0)
    {
        if ($id == 0) return Json::fail('缺少参数');
        $specialBuy = SpecialBuy::get($id);
        if (!$specialBuy) return Json::fail('记录不存在');
        $special = Special::get($specialBuy['special_id']);
        $res = SpecialBuy::where('id', $id)->update(['is_del' => 1]);
        if ($special['type'] == SPECIAL_COLUMN) {
            $res2 = SpecialBuy::where(['uid' => $specialBuy['uid'], 'type' => $specialBuy['type'], 'column_id' => $specialBuy['special_id'], 'is_del' => 0])->update(['is_del' => 1]);
            $res = $res && $res2;
        }
        if ($res) {
            switch ($specialBuy['type']) {
                case '支付获得':
                case '拼团获得':
                case '领取礼物获得':
                case '兑换获得':
                    $source = 2;
                    $type = 1;
                    break;
                case '赠送获得':
                case '买商品赠送':
                    $source = 3;
                    $type = 2;
                    break;
            }
            TestPaperObtain::delTestPaper($specialBuy['order_id'], $specialBuy['uid'], $specialBuy['special_id'], $source);
            DataDownloadBuy::delDataDownload($specialBuy['order_id'], $specialBuy['uid'], $specialBuy['special_id'], $type);
            return Json::successful('删除成功');
        } else
            return Json::fail('删除失败');
    }

    /**
     * 获取某个用户的推广下线
     * */
    public function getSpreadList($uid, $page = 1, $limit = 20)
    {
        return Json::successful(UserModel::getSpreadList($uid, (int)$page, (int)$limit));
    }

    /**
     * 获取某用户的订单列表
     */
    public function getOneorderList($uid, $page = 1, $limit = 20)
    {
        return Json::successful(StoreOrder::getOneorderList(compact('uid', 'page', 'limit')));
    }

    /**
     * 获取某用户的签到列表
     */
    public function getOneSignList($uid, $page = 1, $limit = 20)
    {
        return Json::successful(UserBill::getOneSignList(compact('uid', 'page', 'limit')));
    }

    /**
     * 获取某用户的余额变动记录
     */
    public function getOneBalanceChangList($uid, $page = 1, $limit = 20)
    {
        return Json::successful(UserBill::getOneBalanceChangList(compact('uid', 'page', 'limit')));
    }
}
