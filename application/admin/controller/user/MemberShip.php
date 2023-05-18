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
use service\JsonService as Json;
use service\FormBuilder as Form;
use think\Url;
use app\admin\model\user\MemberShip as MembershipModel;

/**
 * 会员设置控制器
 * Class MemberShip
 * @package app\admin\controller\user
 */
class MemberShip extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 会员列表
     */
    public function membership_vip_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['is_publish', ''],
            ['title', ''],
        ]);
        return Json::successlayui(MembershipModel::getSytemVipList($where));
    }

    public function add_vip($id = 0)
    {
        $membership = [];
        if ($id) {
            $membership = MembershipModel::get($id);
            if ($membership) $membership['sorts'] = $membership['sort'];
            if ($membership['is_free']) $membership['free_day'] = $membership['vip_day'];
        }
        $this->assign(['id' => $id, 'membership' => json_encode($membership)]);
        return $this->fetch();
    }

    /**编辑会员
     * @param int $id
     */
    public function save_sytem_vip($id = 0)
    {
        $post = parent::postMore([
            ['title', ''],
            ['img', ''],
            ['vip_day', 0],
            ['free_day', 0],
            ['original_price', 0],
            ['price', 0],
            ['sort', 0],
            ['is_permanent', 0],
            ['is_publish', 0],
            ['is_free', 0],
            ['is_alone', 0],
            ['brokerage_ratio', 0],
            ['brokerage_two', 0]
        ]);
        if ($post['title'] == '') return Json::fail('请输入会员标题');
        if ($post['is_permanent'] == 0 && $post['vip_day'] <= 0 && $post['is_free'] == 0) return Json::fail('会员有有效期时,请设置会员有效期');
        if ($post['is_free'] == 1 && $post['free_day'] <= 0) return Json::fail('免费会员有有效期时,请设置会员有效期');
        if (bcsub($post['original_price'], 0, 0) < 0) return Json::fail('请输入会员原价');
        if (bcsub($post['price'], 0, 0) < 0) return Json::fail('请输入会员原价');
        if ($post['is_free'] == 1) {
            $post['vip_day'] = $post['free_day'];
            $post['is_alone'] = 0;
            $post['brokerage_ratio'] = 0;
            $post['brokerage_two'] = 0;
            unset($post['free_day']);
        }
        if ($post['is_alone'] && bcadd($post['brokerage_ratio'], $post['brokerage_two'], 2) > 100) return Json::fail('两级返佣比例之和不能大于100');
        MembershipModel::beginTrans();
        try {
            if ($id) {
                MembershipModel::update($post, ['id' => $id]);
                MembershipModel::commitTrans();
                return Json::successful('修改成功');
            } else {
                $post['add_time'] = time();
                MembershipModel::set($post);
                MembershipModel::commitTrans();
                return Json::successful('添加成功');
            }
        } catch (\Exception $e) {
            MembershipModel::rollbackTrans();
            return Json::fail($e->getMessage());
        }
    }

    /**会员状态设置
     * @param string $is_publish
     * @param string $id
     */
    public function set_publish($is_publish = '', $id = '')
    {
        if ($is_publish == '' || $id == '') return Json::fail('缺少参数');
        $res = parent::getDataModification('ship', $id, 'is_publish', $is_publish);
        if ($res)
            return Json::successful($is_publish == 1 ? '发布成功' : '隐藏成功');
        else
            return Json::fail($is_publish == 1 ? '发布失败' : '隐藏失败');
    }

    /**会员删除
     * @param string $id
     */
    public function delete($id = '')
    {
        if ($id == '') return Json::fail('缺少参数');
        $res = parent::getDataModification('ship', $id, 'is_del', 1);
        if ($res)
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }


}
