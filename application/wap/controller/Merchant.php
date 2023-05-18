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

use app\wap\model\merchant\UserEnter;
use service\JsonService;
use service\UtilService;
use service\SystemConfigService;
use think\Request;
use think\Url;
use app\wap\model\user\UserExtract;
use app\wap\model\material\DataDownload;
use app\wap\model\merchant\Merchant as MerchantModel;
use app\wap\model\merchant\MerchantAdmin as MerchantAdminModel;
use app\wap\model\merchant\MerchantFlowingWater;
use app\wap\model\merchant\MerchantFollow;
use app\wap\model\merchant\MerchantBill;
use app\wap\model\special\Special as SpecialModel;
use app\wap\model\special\Lecturer;
use app\wap\model\activity\EventRegistration;
use app\wap\model\store\StoreProduct;
use app\wap\model\user\SmsCode;
use app\wap\model\topic\TestPaper;

/**
 * Class Merchant
 * @package app\wap\controller
 */
class Merchant extends AuthController
{
    /*
      * 白名单
      * */
    public static function WhiteList()
    {
        return [
            'teacher_detail',
            'teacher_list',
            'get_lecturer_details',
            'lecturer_special_list',
            'get_lecturer_list',
            'lecturer_income',
            'lecturer_special_list',
            'lecturer_download_list',
            'lecturer_event_list',
            'lecturer_store_list',
            'lecturer_test_list',
            'is_follow',
        ];
    }

    /**讲师申请
     * @return mixed
     */
    public function index()
    {
        $this->assign([
            'content' => get_config_content('lecturer_entry'),
            'title' => '讲师入驻协议'
        ]);
        return $this->fetch();
    }

    /**讲师信息
     * @return mixed
     */
    public function info($mer_id)
    {
        if (!$mer_id) $this->failed('参数有误!');
        $business = isset($this->userInfo['business']) ? $this->userInfo['business'] : 0;
        if (!$business) $this->failed('您不是讲师，请先申请!');
        $merchant = MerchantModel::where(['id' => $mer_id, 'uid' => $this->uid, 'status' => 1, 'is_del' => 0, 'estate' => 1])->field('id,uid,status,is_del,estate,mer_special_divide,mer_store_divide,mer_event_divide,mer_data_divide,mer_test_divide,gold_divide')->find();
        $admin = MerchantAdminModel::where(['mer_id' => $mer_id, 'uid' => $this->uid, 'status' => 1, 'is_del' => 0, 'level' => 0])->field('mer_id,uid,account,level,status,is_del')->find();
        if (!$merchant) $this->failed('讲师信息有误!');
        if (!$admin) $this->failed('登录账号不存在!');
        $this->assign([
            'mer_id' => $mer_id,
            'url' => SystemConfigService::get('site_url') . '/merchant/login/index',
            'merchant' => $merchant,
            'admin' => $admin,
        ]);
        return $this->fetch();
    }

    /**讲师明细
     * @return mixed
     */
    public function income()
    {
        return $this->fetch();
    }

    /**
     * 讲师详情
     * @return mixed
     */
    public function teacher_detail($id = 0)
    {
        $business = isset($this->userInfo['business']) ? $this->userInfo['business'] : 0;
        $mer_id = 0;
        if ($business) {
            $data = MerchantModel::where(['status' => 1, 'is_del' => 0, 'estate' => 1, 'uid' => $this->uid])->field('id,lecturer_id')->find();
            if (!$data && !$id) $this->failed('请检查您的讲师后台状态是否关闭!');
            $mer_id = $data ? $data['id'] : 0;
            $lecturer_id = $data ? $data['lecturer_id'] : 0;
        }
        $id = $id ? $id : $lecturer_id;
        if (!$id) $this->failed('您不是讲师，请先申请!');
        $lecturer = Lecturer::details($id);
        if (!$lecturer) $this->failed('讲师信息无法访问!');
        $this->assign(['lecturer' => $lecturer, 'mer_id' => $mer_id]);
        return $this->fetch();
    }

    /**收益及提现统计
     * @param int $mer_id
     */
    public function lecturer_income($mer_id = 0)
    {
        $data = MerchantFlowingWater::get_merchant_data($mer_id);
        $data['extract'] = UserExtract::userMerExtractTotalPrice($mer_id);
        $data['gold'] = MerchantBill::userMerGoldPrice($mer_id);
        return JsonService::successful($data);
    }

    /**
     * 讲师列表
     * @return mixed
     */
    public function teacher_list()
    {
        return $this->fetch();
    }

    /**
     * 讲师列表
     */
    public function get_lecturer_list()
    {
        list($page, $limit, $search) = UtilService::getMore([
            ['page', 1],
            ['limit', 10],
            ['search', '']
        ], $this->request, true);
        $uid = $this->uid ? $this->uid : 0;
        $list = Lecturer::getLecturerList($uid, $page, $limit, $search);
        return JsonService::successful($list);
    }

    /**
     * 讲师名下课程
     */
    public function lecturer_special_list()
    {
        list($mer_id, $page, $limit) = UtilService::postMore([
            ['mer_id', 0],
            ['page', 1],
            ['limit', 10]
        ], $this->request, true);
        $list = SpecialModel::getLecturerSpecialList($mer_id, $page, $limit);
        return JsonService::successful($list);
    }

    /**
     * 讲师名下资料
     */
    public function lecturer_download_list()
    {
        list($mer_id, $page, $limit) = UtilService::postMore([
            ['mer_id', 0],
            ['page', 1],
            ['limit', 10]
        ], $this->request, true);
        $list = DataDownload::getLecturerDataDownloadList($mer_id, $page, $limit);
        return JsonService::successful($list);
    }

    /**
     * 讲师名下活动
     */
    public function lecturer_event_list()
    {
        list($mer_id, $page, $limit) = UtilService::postMore([
            ['mer_id', 0],
            ['page', 1],
            ['limit', 10]
        ], $this->request, true);
        $list = EventRegistration::getLecturerEventList($mer_id, $page, $limit);
        return JsonService::successful($list);
    }

    /**
     * 讲师名下商品
     */
    public function lecturer_store_list()
    {
        list($mer_id, $page, $limit) = UtilService::postMore([
            ['mer_id', 0],
            ['page', 1],
            ['limit', 10]
        ], $this->request, true);
        $list = StoreProduct::getLecturerStoreList($mer_id, $page, $limit);
        return JsonService::successful($list);
    }

    /**
     * 讲师名下试卷
     */
    public function lecturer_test_list()
    {
        list($mer_id, $type, $page, $limit) = UtilService::postMore([
            ['mer_id', 0],
            ['type', 1],
            ['page', 1],
            ['limit', 10]
        ], $this->request, true);
        $list = TestPaper::getMerTestPaperList($mer_id, $type, $page, $limit);
        return JsonService::successful($list);
    }

    /**讲师流水明细
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_flowing_water_list()
    {
        $where = UtilService::getMore([
            ['category', 'now_money'],
            ['is_extract', 0],
            ['page', 1],
            ['limit', 10]
        ]);
        $where['mer_id'] = MerchantModel::getMerId($this->uid);
        return JsonService::successful(MerchantBill::get_user_flowing_water_list($where));
    }

    /**
     * 讲师申请
     */
    public function apply()
    {
        $data = UtilService::postMore([
            ['province', ''],
            ['city', ''],
            ['district', ''],
            ['address', ''],
            ['merchant_name', ''],
            ['link_tel', ''],
            ['code', ''],
            ['charter', []],
            ['merchant_head', ''],
            ['label', []],
            ['explain', ''],
            ['introduction', '']
        ]);
        if (!$data['code']) return JsonService::fail('请输入验证码');
        $code = md5('is_phone_code' . $data['code']);
        if (!SmsCode::CheckCode($data['link_tel'], $code)) return JsonService::fail('验证码验证失败');
        SmsCode::setCodeInvalid($data['link_tel'], $code);
        $res = UserEnter::setUserEnter($data, $this->uid);
        if ($res)
            return JsonService::successful('提交成功!');
        else
            return JsonService::fail(UserEnter::getErrorInfo() ?? '提交失败');
    }

    /**
     * 检查是否提交申请
     */
    public function is_apply()
    {
        $data = UserEnter::inspectStatus($this->uid);
        return JsonService::successful($data);
    }

    /**
     * 获得申请数据
     */
    public function apply_data()
    {
        $apply = UserEnter::inspectUserEnter($this->uid);
        return JsonService::successful($apply);
    }

    /**是否关注
     * @param $mer_id
     */
    public function is_follow($mer_id)
    {
        if (!$mer_id) return JsonService::fail('参数错误');
        if (!$this->uid) return JsonService::successful(['code' => 0]);
        $res = MerchantFollow::isFollow($this->uid, $mer_id);
        return JsonService::successful($res);
    }

    /**用户关注 取消关注 讲师
     * @param $mer_id
     * @param $is_follow 0 =取消关注  1= 关注
     */
    public function user_follow($mer_id, $is_follow)
    {
        if (!$mer_id) return JsonService::fail('参数错误');
        $res = MerchantFollow::user_merchant_follow($this->uid, $mer_id, $is_follow);
        return JsonService::successful($res);
    }

    /**讲师关注列表
     * @param int $page
     * @param int $limit
     */
    public function get_user_follow_list($page = 1, $limit = 20)
    {
        $data = MerchantFollow::get_user_merchant_follow_list($this->uid, $page, $limit);
        return JsonService::successful($data);
    }

}
