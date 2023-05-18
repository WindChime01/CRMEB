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

namespace app\admin\controller\ump;

use app\admin\controller\AuthController;
use app\admin\model\ump\EventPrice;
use service\JsonService as Json;
use think\Request;
use think\Url;
use app\admin\model\user\User;
use app\admin\model\user\UserBill;
use service\FormBuilder as Form;
use app\admin\model\ump\EventRegistration as EventRegistrationModel;
use app\admin\model\ump\EventWriteOffUser;
use app\admin\model\ump\EventSignUp as EventSignUpModel;
use app\admin\model\ump\EventData as EventDataModel;
use app\admin\model\ump\EventPrice as EventPriceModel;
use app\admin\model\merchant\MerchantFlowingWater;
use app\admin\model\merchant\Merchant;
use service\AlipayTradeWapService;
use behavior\wechat\PaymentBehavior;
use service\HookService;

/**活动控制器
 * Class EventRegistration
 * @package app\admin\controller\ump
 */
class EventRegistration extends AuthController
{
    public function index()
    {
        $mer_list = Merchant::getMerchantList();
        $this->assign(['mer_list' => $mer_list]);
        return $this->fetch();
    }

    /**
     * 活动列表
     */
    public function event_registration_list()
    {
        $where = parent::getMore([
            ['title', ''],
            ['status', 1],
            ['is_show', ''],
            ['mer_id', ''],
            ['page', 1],
            ['limit', 20],
        ], $this->request);
        return Json::successlayui(EventRegistrationModel::systemPage($where));
    }

    /**活动审核
     * @return mixed
     */
    public function examine()
    {
        $mer_list = Merchant::getMerchantList();
        $this->assign(['mer_list' => $mer_list]);
        return $this->fetch();
    }

    /**
     * 异步查找产品
     *
     * @return json
     */
    public function event_examine_ist()
    {
        $where = parent::getMore([
            ['title', ''],
            ['page', 1],
            ['limit', 20],
            ['mer_id', ''],
            ['status', ''],
        ]);
        return Json::successlayui(EventRegistrationModel::eventExamineList($where));
    }

    public function examineDetails($id)
    {
        if (!$id) return Json::fail('参数错误');
        $details = EventRegistrationModel::get($id);
        if (!$details) return Json::fail('活动不存在');
        $details->activity_rules = htmlspecialchars_decode($details->activity_rules);
        $details->content = htmlspecialchars_decode($details->content);
        $event = EventDataModel::eventDataList($id);
        $price = EventPriceModel::eventPriceList($id);
        $this->assign(['details' => json_encode($details), 'event' => json_encode($event), 'price' => json_encode($price)]);
        return $this->fetch('activity');
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
        if (!EventRegistrationModel::be(['id' => $id, 'status' => 0])) return Json::fail('操作记录不存在或状态错误!');
        $special = EventRegistrationModel::get($id);
        if (!$special) return Json::fail('操作记录不存!');
        if ($special->status != 0) return Json::fail('您已审核,请勿重复操作');
        EventRegistrationModel::beginTrans();
        $res = EventRegistrationModel::changeFail($id, $special['mer_id'], $fail_msg['message']);
        if ($res) {
            EventRegistrationModel::commitTrans();
            return Json::successful('操作成功!');
        } else {
            EventRegistrationModel::rollbackTrans();
            return Json::fail('操作失败!');
        }
    }

    /**通过
     * @param $id
     * @throws \think\exception\DbException
     */
    public function succ($id)
    {
        if (!EventRegistrationModel::be(['id' => $id, 'status' => 0])) return Json::fail('操作记录不存在或状态错误!');
        $special = EventRegistrationModel::get($id);
        if (!$special) return Json::fail('操作记录不存!');
        if ($special->status != 0) return Json::fail('您已审核,请勿重复操作');
        EventRegistrationModel::beginTrans();
        $res = EventRegistrationModel::changeSuccess($id, $special['mer_id']);
        if ($res) {
            EventRegistrationModel::commitTrans();
            return Json::successful('操作成功!');
        } else {
            EventRegistrationModel::rollbackTrans();
            return Json::fail('操作失败!');
        }
    }

    /**编辑
     * @param string $is_show
     * @param string $id
     */
    public function set_show($is_show = '', $id = '')
    {
        if ($is_show == '' || $id == '') return Json::fail('缺少参数');
        $res = parent::getDataModification('event', $id, 'is_show', (int)$is_show);
        if ($res)
            return Json::successful($is_show == 1 ? '显示成功' : '隐藏成功');
        else
            return Json::fail($is_show == 1 ? '显示失败' : '隐藏失败');
    }

    /**获得添加
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create($id = 0)
    {
        $news = [];
        $event = [];
        $price = [];
        if ($id) {
            $news = EventRegistrationModel::eventRegistrationOne($id);
            $event = EventDataModel::eventDataList($id);
            $price = EventPriceModel::eventPriceList($id);
            if (!count($price)) {
                $price[0] = [
                    'event_id' => $id,
                    'event_number' => 1,
                    'event_price' => $news['price'],
                    'event_mer_price' => $news['member_price'],
                    'sort' => 0
                ];
            }
        }
        $this->assign(['id' => $id, 'news' => json_encode($news), 'event' => json_encode($event), 'price' => json_encode($price)]);
        return $this->fetch();
    }

    /**
     * 删除活动
     * */
    public function delete($id)
    {
        $res = EventRegistrationModel::delArticleCategory($id);
        if (!$res)
            return Json::fail(EventRegistrationModel::getErrorInfo('删除失败,请稍候再试!'));
        else {
            EventDataModel::delEventData($id);
            EventPriceModel::delEventPrice($id);
            return Json::successful('删除成功!');
        }
    }

    /**
     * 添加和修改活动
     */
    public function add_new()
    {
        $data = parent::postMore([
            ['id', 0],
            'title',
            'image',
            'qrcode_img',
            'activity_rules',
            'content',
            'number',
            'province',
            'city',
            'district',
            'detail',
            'signup_start_time',
            'signup_end_time',
            'start_time',
            'end_time',
            ['sort', 0],
            ['event', ''],//资料
            ['event_price', ''],//价格
            ['restrictions', 0],
            ['is_fill', 1],
            ['is_show', 0],
            ['pay_type', 1],
            'price',
            ['member_pay_type', 1],
            'member_price'
        ]);
        $data['signup_start_time'] = strtotime($data['signup_start_time']);
        $data['signup_end_time'] = strtotime($data['signup_end_time']);
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        if (bcsub($data['signup_end_time'], $data['signup_start_time'], 0) <= 0) return Json::fail('报名结束时间不能小于等于开始时间');
        if (bcsub($data['start_time'], $data['signup_end_time'], 0) <= 0) return Json::fail('活动开始时间不能小于等于报名结束时间');
        if (bcsub($data['end_time'], $data['start_time'], 0) <= 0) return Json::fail('活动结束时间不能小于等于开始时间');
        $data['content'] = htmlspecialchars($data['content']);
        $data['activity_rules'] = htmlspecialchars($data['activity_rules']);
        if (isset($data['event']) && $data['event'] != '') {
            $event = json_decode($data['event'], true);
        } else {
            $event = [];
        }
        if (isset($data['event_price']) && $data['event_price'] != '') {
            $price = json_decode($data['event_price'], true);
        } else {
            $price = [];
        }
        $number = $data['number'];
        if ($data['id']) {
            $id = $data['id'];
            unset($data['id'], $data['event'], $data['event_price']);
            EventRegistrationModel::beginTrans();
            $res1 = EventRegistrationModel::edit($data, $id, 'id');
            $res2 = EventDataModel::eventDataAdd($id, $event);
            $res3 = EventPriceModel::eventPriceAdd($id, $price, $number);
            $res = $res1 && $res2 && $res3;
            EventRegistrationModel::checkTrans($res);
            if ($res) {
                return Json::successful('修改活动成功!', $id);
            } else
                return Json::fail('修改活动失败，您并没有修改什么!', $id);
        } else {
            $data['add_time'] = time();
            $data['statu'] = 0;
            EventRegistrationModel::beginTrans();
            $id = EventRegistrationModel::insertGetId($data);
            $res2 = EventDataModel::eventDataAdd($id, $event);
            $res3 = EventPriceModel::eventPriceAdd($id, $price, $number);
            $res = $id && $res2 && $res3;
            EventRegistrationModel::checkTrans($res);
            if ($res)
                return Json::successful('添加活动成功!', $id);
            else
                return Json::successful('添加活动失败!', $id);
        }
    }

    /**
     * 查看报名人员
     */
    public function viewStaff($id)
    {
        if (!$id) return Json::fail('参数错误!');
        $activity = EventRegistrationModel::where('id', $id)->find();
        if (!$activity) return Json::fail('活动不存在!');
        $this->assign(['aid' => $id]);
        return $this->fetch('view_staff');
    }

    /**报名订单订单
     * @return mixed
     */
    public function order()
    {
        $this->assign([
            'year' => getMonth('y'),
            'orderCount' => EventSignUpModel::orderCount()
        ]);
        return $this->fetch();
    }

    /**
     * 查看活动报名订单列表
     */
    public function get_sign_up_list()
    {
        $where = parent::getMore([
            ['id', $this->request->param('id')],
            ['page', 1],
            ['limit', 20],
            ['status', ''],
            ['type', $this->request->param('type')],
            ['real_name', ''],
            ['data', ''],
            ['excel', 0],
        ]);
        return Json::successlayui(EventSignUpModel::getUserSignUpAll($where));
    }

    /**
     * 统计
     */
    public function getBadge()
    {
        $where = parent::postMore([
            ['id', $this->request->param('id')],
            ['status', ''],
            ['type', ''],
            ['data', ''],
            ['real_name', '']
        ]);
        return Json::successful(EventSignUpModel::getBadge($where));
    }

    /**用户活动核销
     * @param string $order_id
     * @param int $aid
     * @param string $code
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function scanCodeSignIn($id)
    {
        if (!$id) $this->failed('参数有误！');
        $order = EventSignUpModel::where('id', $id)->find();
        if (!$order) $this->failed('订单不存在！');
        if ($order['status']) $this->failed('订单已核销！');
        $res = EventSignUpModel::where(['id' => $id, 'paid' => 1, 'is_del' => 0])->update(['status' => 1]);
        if ($res) return Json::successful('核销成功');
        else return Json::fail('核销失败');
    }

    /**
     * 修改退款状态
     * @param $id
     * @return \think\response\Json|void
     */
    public function refund_y($id)
    {
        if (!$id) return $this->failed('数据不存在');
        $product = EventSignUpModel::get($id);
        if (!$product) return Json::fail('数据不存在!');
        if ($product['paid'] == 1) {
            $f = array();
            $f[] = Form::input('order_id', '退款单号', $product->getData('order_id'))->disabled(1);
            $f[] = Form::number('refund_price', '退款金额', $product->getData('pay_price'))->precision(2)->min(0.01);
            $form = Form::make_post_form('退款处理', $f, Url::build('updateRefundY', array('id' => $id)), 4);
            $this->assign(compact('form'));
            return $this->fetch('public/form-builder');
        } else return Json::fail('数据不存在!');
    }

    /**退款处理
     * @param Request $request
     * @param $id
     */
    public function updateRefundY(Request $request, $id)
    {
        $data = parent::postMore([
            'refund_price',
        ], $request);
        if (!$id) return Json::fail('数据不存在');
        $product = EventSignUpModel::get($id);
        if (!$product) return Json::fail('数据不存在!');
        if ($product['pay_price'] == $product['refund_price']) return Json::fail('已退完支付金额!不能再退款了');
        if (!$data['refund_price']) return Json::fail('请输入退款金额');
        $refund_price = $data['refund_price'];
        $data['refund_price'] = bcadd($data['refund_price'], $product['refund_price'], 2);
        $bj = bccomp((float)$product['pay_price'], (float)$data['refund_price'], 2);
        if ($bj < 0) return Json::fail('退款金额大于支付金额，请修改退款金额');
        $data['refund_status'] = 2;
        $refund_data['pay_price'] = $product['pay_price'];
        $refund_data['refund_price'] = $refund_price;
        if ($product['pay_type'] == 'weixin') {
            try {
                HookService::listen('wechat_pay_order_refund', $product['order_id'], $refund_data, true, PaymentBehavior::class);
            } catch (\Exception $e) {
                return Json::fail($e->getMessage());
            }
        } else if ($product['pay_type'] == 'yue') {
            EventSignUpModel::beginTrans();
            $res = User::bcInc($product['uid'], 'now_money', $refund_price, 'uid');
            EventSignUpModel::checkTrans($res);
            if (!$res) return Json::fail('余额退款失败!');
        } else if ($product['pay_type'] == 'zhifubao') {
            $res = AlipayTradeWapService::init()->AliPayRefund($product['order_id'], $product['trade_no'], $refund_price, '活动订单退款', 'refund');
            if (empty($res) || $res != 10000) {
                return Json::fail('支付宝退款失败!');
            }
        }
        $data['refund_reason_time'] = time();
        $resEdit = EventSignUpModel::edit($data, $id);
        if ($resEdit) {
            $pay_type = $product['pay_type'] == 'yue' ? 'now_money' : $product['pay_type'];
            if ($product['pay_type'] == 'yue') {
                $balance = User::where(['uid' => $product['uid']])->value('now_money');
            } else {
                $balance = 0;
            }
            UserBill::income('活动订单退款', $product['uid'], $pay_type, 'pay_sign_up_refund', $refund_price, $product['id'], $balance, '活动订单退款' . floatval($refund_price) . '元');
            MerchantFlowingWater::orderRefund($id, $product['mer_id'], 4);
            return Json::successful('修改成功!');
        } else {
            return Json::successful('修改失败!');
        }
    }

    /**订单详情
     * @param string $oid
     * @return mixed|void
     * @throws \think\exception\DbException
     */
    public function order_info($oid = '')
    {
        if (!$oid || !($orderInfo = EventSignUpModel::get($oid)))
            return $this->failed('订单不存在!');
        $userInfo = User::getAllUserinfo($orderInfo['uid']);
        $this->assign(compact('orderInfo', 'userInfo'));
        return $this->fetch();
    }

    /**订单删除
     * @param int $id
     */
    public function order_delete($id = 0)
    {
        if (!$id) return Json::fail('参数错误!');
        $data['is_del'] = 1;
        EventSignUpModel::edit($data, $id);
        return Json::successful('删除成功!');
    }

    /**活动转增
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function increase($id = 0)
    {
        if (!$id) $this->failed('缺少参数');
        $event = EventRegistrationModel::get($id);
        if (!$event) $this->failed('没有查到此活动');
        if ($event->is_del) $this->failed('此活动已删除');
        $form = Form::create(Url::build('change_increase', ['id' => $id]), [
            Form::select('mer_id', '讲师')->setOptions(function () {
                $model = Merchant::getMerWhere();
                $list = $model->field('mer_name,id')->order('sort desc,add_time desc')->select();
                $menus = [['value' => 0, 'label' => '总后台']];
                foreach ($list as $menu) {
                    $menus[] = ['value' => $menu['id'], 'label' => $menu['mer_name']];
                }
                return $menus;
            })->filterable(1),
        ]);
        $form->setMethod('post')->setTitle('活动转增')->setSuccessScript('parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},800);');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 活动转增
     * @param int $id
     * @throws \think\exception\DbException
     */
    public function change_increase($id = 0)
    {
        if (!$id) $this->failed('缺少参数');
        $data = parent::postMore([
            ['mer_id', 0],
        ]);
        $res = EventRegistrationModel::edit($data, $id, 'id');
        if ($res)
            return Json::successful('资料转增成功');
        else
            return Json::fail('资料转增失败');
    }

    /**活动核销列表
     * @param $event_id
     * @return mixed
     */
    public function write_off_user($event_id = 0)
    {
        $this->assign(compact('event_id'));
        return $this->fetch();
    }

    public function user($event_id = 0)
    {
        $this->assign(compact('event_id'));
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

    /**活动添加核销用户
     * @param $event_id
     * @return void
     */
    public function set_write_off_user()
    {
        $data = parent::getMore([
            ['uid', 0],
            ['event_id', 0],
        ]);
        if (!$data['event_id'] || !$data['uid']) $this->failed('缺少参数');
        $res = EventWriteOffUser::set_event_write_off_user($data);
        if ($res) {
            $dat['is_write_off'] = 1;
            User::edit($dat, $data['uid'], 'uid');
            return Json::successful('添加成功');
        } else
            return Json::fail('添加失败');
    }

    /**删除活动核销员
     * @return void
     */
    public function del_write_off_user()
    {
        $data = parent::getMore([
            ['uid', 0],
            ['event_id', 0],
        ]);
        if (!$data['event_id'] || !$data['uid']) $this->failed('缺少参数');
        $res = EventWriteOffUser::del_event_write_off_user($data);
        if ($res) {
            $count = EventWriteOffUser::where(['uid' => $data['uid'], 'is_del' => 0])->count();
            if ($count <= 0) {
                $dat['is_write_off'] = 0;
                User::edit($dat, $data['uid'], 'uid');
            }
            return Json::successful('删除成功');
        } else
            return Json::fail('删除失败');
    }

    /**获取核销用户列表
     * @return void
     */
    public function get_write_off_user_list()
    {
        $where = parent::getMore([
            ['event_id', $this->request->param('event_id')],
            ['page', 1],
            ['limit', 20]
        ]);
        return Json::successlayui(EventWriteOffUser::get_event_write_off_user_list($where));
    }
}

