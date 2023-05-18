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

namespace app\admin\controller\special;

use app\admin\controller\AuthController;
use app\admin\model\special\Special;
use app\admin\model\system\WebRecommend;
use app\admin\model\system\WebRecommendRelation;
use app\admin\model\system\Recommend;
use app\admin\model\system\RecommendRelation;
use app\admin\model\special\Lecturer as LecturerModel;
use app\admin\model\merchant\Merchant as MerchantModel;
use app\admin\model\merchant\MerchantAdmin as MerchantAdminModel;
use app\merchant\model\merchant\MerchantMenus;
use app\admin\model\user\User;
use service\JsonService;
use service\FormBuilder as Form;
use think\Url;

/**
 * 讲师控制器
 */
class Lecturer extends AuthController
{
    /**
     * 讲师列表展示
     * @return
     * */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 讲师列表获取
     * @return
     * */
    public function lecturer_list()
    {
        $where = parent::getMore([
            ['page', 1],
            ['is_show', ''],
            ['limit', 20],
            ['title', ''],
        ]);
        return JsonService::successlayui(LecturerModel::getLecturerList($where));
    }

    /**添加/编辑
     * @param int $id
     * @return mixed|void
     * @throws \think\exception\DbException
     */
    public function create($id = 0)
    {
        if ($id) {
            $lecturer = LecturerModel::get($id);
            $lecturer['label'] = json_decode($lecturer['label']);
            $lecturer['introduction'] = htmlspecialchars_decode($lecturer['introduction']);
            if (!$lecturer) return JsonService::fail('讲师信息不存在！');
        } else {
            $lecturer = [];
        }
        $this->assign(['lecturer' => json_encode($lecturer), 'id' => $id]);
        return $this->fetch();
    }

    /**
     * 添加和修改讲师
     * @param int $id 修改
     * @return JsonService
     * */
    public function save_lecturer($id = 0)
    {
        $data = parent::postMore([
            ['lecturer_name', ''],
            ['lecturer_head', ''],
            ['label', []],
            ['phone', ''],
            ['explain', ''],
            ['introduction', ''],
            ['sort', 0],
            ['is_show', 1],
        ]);
        $data['lecturer_name'] = preg_replace("#(^( |\s)+|( |\s)+$)#", "", $data['lecturer_name']);
        if (!$data['lecturer_name']) return JsonService::fail('请输入讲师名称');
        if (mb_strlen($data['lecturer_name']) > 8) return JsonService::fail('讲师名称不能超过8个字');
        if (!$data['lecturer_head']) return JsonService::fail('请输入讲师头像');
        if (!count($data['label'])) return JsonService::fail('请输入标签');
        if (!$data['explain']) return JsonService::fail('请编辑讲师说明');
        if (!$data['introduction']) return JsonService::fail('请编辑讲师介绍');
        $data['label'] = json_encode($data['label']);
        $data['introduction'] = htmlspecialchars($data['introduction']);
        if ($id) {
            LecturerModel::edit($data, $id);
            return JsonService::successful('修改成功');
        } else {
            $data['add_time'] = time();
            if (!LecturerModel::be(['lecturer_name' => $data['lecturer_name'], 'lecturer_head' => $data['lecturer_head'], 'label' => $data['label'], 'phone' => $data['phone'], 'is_del' => 0])) {
                $res = LecturerModel::set($data);
            } else {
                return JsonService::fail('讲师已存在');
            }
            if ($res)
                return JsonService::successful('添加成功');
            else
                return JsonService::fail('添加失败');
        }
    }

    /**
     * 设置单个产品上架|下架
     * @param int $is_show 是否显示
     * @param int $id 修改的主键
     * @return JsonService
     */
    public function set_show($is_show = '', $id = '')
    {
        ($is_show == '' || $id == '') && JsonService::fail('缺少参数');
        $res = parent::getDataModification('lecturer', $id, 'is_show', (int)$is_show);
        if ($res) {
            $mer_id = LecturerModel::where('id', $id)->value('mer_id');
            if ($mer_id) {
                $data['estate'] = $is_show;
                MerchantModel::edit($data, $mer_id, 'id');
            }
            return JsonService::successful($is_show == 1 ? '显示成功' : '隐藏成功');
        } else {
            return JsonService::fail($is_show == 1 ? '显示失败' : '隐藏失败');
        }
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
        $res = parent::getDataModification('lecturer', $id, $field, $value);
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
        if (LecturerModel::delLecturer($id))
            return JsonService::successful('删除成功');
        else
            return JsonService::fail(LecturerModel::getErrorInfo('删除失败'));
    }

    /**
     * 讲师课程订单
     */
    public function lecturer_order($id = 0)
    {
        $this->assign([
            'year' => getMonth('h'),
            'lecturer_id' => $id,
        ]);
        return $this->fetch();
    }

    /**讲师课程购买记录
     * @throws \think\exception\DbException
     */
    public function lecturer_order_list()
    {
        $where = parent::getMore([
            ['lecturer_id', 0],
            ['page', 1],
            ['limit', 10],
            ['data', ''],
        ]);
        if (!$where['lecturer_id']) return JsonService::fail('缺少参数！');
        $lecturer = LecturerModel::get($where['lecturer_id']);
        if (!$lecturer) return JsonService::fail('讲师不存在！');
        $list = LecturerModel::lecturerOrderList($where);
        return JsonService::successlayui($list);
    }

    /**
     * 讲师盈利
     */
    public function getBadge()
    {
        $where = parent::postMore([
            ['lecturer_id', 0],
            ['data', ''],
        ]);
        if (!$where['lecturer_id']) return JsonService::fail('缺少参数！');
        $lecturer = LecturerModel::get($where['lecturer_id']);
        if (!$lecturer) return JsonService::fail('讲师不存在！');
        $list = LecturerModel::getBadge($where);
        return JsonService::successful($list);
    }

    /**
     * 添加推荐
     * @param int $special_id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function web_recommend($lecturer_id = 0)
    {
        if (!$lecturer_id) $this->failed('缺少参数');
        $lecturer = LecturerModel::get($lecturer_id);
        if (!$lecturer) $this->failed('没有查到此讲师');
        if ($lecturer->is_del) $this->failed('此讲师已删除');
        $form = Form::create(Url::build('save_web_recommend', ['lecturer_id' => $lecturer_id]), [
            Form::select('recommend_id', '推荐')->setOptions(function () {
                $model = WebRecommend::where(['is_show' => 1, 'type' => 2]);
                $list = $model->field('title,id')->order('sort desc,add_time desc')->select();
                $menus = [];
                foreach ($list as $menu) {
                    $menus[] = ['value' => $menu['id'], 'label' => $menu['title']];
                }
                return $menus;
            })->filterable(1),
            Form::number('sort', '排序'),
        ]);
        $form->setMethod('post')->setTitle('推荐设置')->setSuccessScript('parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},800);');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存推荐
     * @param int $special_id
     * @throws \think\exception\DbException
     */
    public function save_web_recommend($lecturer_id = 0)
    {
        if (!$lecturer_id) $this->failed('缺少参数');
        $data = parent::postMore([
            ['recommend_id', 0],
            ['sort', 0],
        ]);
        if (!$data['recommend_id']) return JsonService::fail('请选择推荐');
        $recommend = WebRecommend::get($data['recommend_id']);
        if (!$recommend) return JsonService::fail('导航菜单不存在');
        $data['add_time'] = time();
        $data['type'] = $recommend->type;
        $data['link_id'] = $lecturer_id;
        if (WebRecommendRelation::be(['type' => $recommend->type, 'link_id' => $lecturer_id, 'recommend_id' => $data['recommend_id']])) return JsonService::fail('已推荐,请勿重复推荐');
        if (WebRecommendRelation::set($data))
            return JsonService::successful('推荐成功');
        else
            return JsonService::fail('推荐失败');
    }

    /**取消推荐
     * @param int $id
     */
    public function cancel_web_recommendation($id = 0, $lecturer_id = 0)
    {
        if (!$id || !$lecturer_id) return JsonService::fail('缺少参数');
        if (WebRecommendRelation::be(['id' => $id, 'link_id' => $lecturer_id])) {
            $res = WebRecommendRelation::where(['id' => $id, 'link_id' => $lecturer_id])->delete();
            if ($res)
                return JsonService::successful('取消推荐成功');
            else
                return JsonService::fail('取消推荐失败');
        } else {
            return JsonService::fail('推荐不存在');
        }
    }

    /**
     * 添加推荐
     * @param int $special_id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function recommend($lecturer_id = 0)
    {
        if (!$lecturer_id) $this->failed('缺少参数');
        $lecturer = LecturerModel::get($lecturer_id);
        if (!$lecturer) $this->failed('没有查到此讲师');
        if ($lecturer->is_del) $this->failed('此讲师已删除');
        $form = Form::create(Url::build('save_recommend', ['lecturer_id' => $lecturer_id]), [
            Form::select('recommend_id', '推荐')->setOptions(function () {
                $model = Recommend::where(['is_show' => 1, 'type' => 6]);
                $list = $model->field('title,id')->order('sort desc,add_time desc')->select();
                $menus = [];
                foreach ($list as $menu) {
                    $menus[] = ['value' => $menu['id'], 'label' => $menu['title']];
                }
                return $menus;
            })->filterable(1),
            Form::number('sort', '排序'),
        ]);
        $form->setMethod('post')->setTitle('推荐设置')->setSuccessScript('parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},800);');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存推荐
     * @param int $special_id
     * @throws \think\exception\DbException
     */
    public function save_recommend($lecturer_id = 0)
    {
        if (!$lecturer_id) $this->failed('缺少参数');
        $data = parent::postMore([
            ['recommend_id', 0],
            ['sort', 0],
        ]);
        if (!$data['recommend_id']) return JsonService::fail('请选择推荐');
        $recommend = Recommend::get($data['recommend_id']);
        if (!$recommend) return JsonService::fail('导航菜单不存在');
        $data['add_time'] = time();
        $data['type'] = $recommend->type;
        $data['link_id'] = $lecturer_id;
        if (RecommendRelation::be(['type' => $recommend->type, 'link_id' => $lecturer_id, 'recommend_id' => $data['recommend_id']])) return JsonService::fail('已推荐,请勿重复推荐');
        if (RecommendRelation::set($data))
            return JsonService::successful('推荐成功');
        else
            return JsonService::fail('推荐失败');
    }

    /**取消推荐
     * @param int $id
     */
    public function cancel_recommendation($id = 0, $lecturer_id = 0)
    {
        if (!$id || !$lecturer_id) return JsonService::fail('缺少参数');
        if (RecommendRelation::be(['id' => $id, 'link_id' => $lecturer_id])) {
            $res = RecommendRelation::where(['id' => $id, 'link_id' => $lecturer_id])->delete();
            if ($res)
                return JsonService::successful('取消推荐成功');
            else
                return JsonService::fail('取消推荐失败');
        } else {
            return JsonService::fail('推荐不存在');
        }
    }

    /**生成讲师后台
     * @return mixed
     */
    public function mercreate($id)
    {
        $lecturer = LecturerModel::get($id);
        $this->assign([
            'title' => '添加讲师后台',
            'lecturer' => json_encode($lecturer),
            'action' => Url::build('save'),
            'menus' => json(MerchantMenus::ruleList())->getContent()
        ]);
        return $this->fetch();
    }

    /**
     * 添加讲师商户
     */
    public function save()
    {
        $data = parent::postMore([
            'account',
            ['id', 0],
            ['uid', 0],
            'conf_pwd',
            'pwd',
            'mer_name',
            'real_name',
            'mer_phone',
            'mer_avatar',
            'mer_special_divide',
            'mer_store_divide',
            'mer_event_divide',
            'mer_data_divide',
            'mer_test_divide',
            'gold_divide',
            'mark',
            'mer_address',
            ['checked_menus', [], '', 'rules'],
            ['is_source', 0],
            ['is_audit', 0],
            ['status', 0]
        ]);
        if (!is_array($data['rules']) || !count($data['rules'])) return JsonService::fail('请选择最少一个权限');
        $data['rules'] = implode(',', $data['rules']);
        if (!$data['account']) return JsonService::fail('请输入讲师后台账号');
        if (MerchantAdminModel::where('account', trim($data['account']))->where('is_del', 0)->count()) return JsonService::fail('商户账号已存在,请使用别的商户账号注册');
        if (!$data['pwd']) return JsonService::fail('请输入讲师后台登陆密码');
        if ($data['pwd'] != $data['conf_pwd']) return JsonService::fail('两次输入密码不想同');
        if (!$data['mer_name']) return JsonService::fail('请输入讲师后台名称');
        if (!$data['uid']) return JsonService::fail('请输入绑定的用户ID');
        $user = User::where('uid', $data['uid'])->find();
        if (!$user) {
            return JsonService::fail('绑定的用户不存在');
        } else {
            if ($user['business'] == 1) {
                return JsonService::fail('该用户已是讲师');
            }
        }
        $id = $data['id'];
        $data['pwd'] = trim(md5($data['pwd']));
        $data['reg_time'] = time();
        $data['add_time'] = time();
        $data['reg_admin_id'] = $this->adminId;
        $data['lecturer_id'] = $id;
        $data['estate'] = 1;
        $admin = array();
        $admin['account'] = trim($data['account']);
        $admin['pwd'] = $data['pwd'];
        unset($data['id']);
        unset($data['conf_pwd']);
        unset($data['account']);
        unset($data['pwd']);
        MerchantModel::beginTrans();
        $res = MerchantModel::set($data);
        $res1 = false;
        if ($res) {
            $admin['uid'] = $data['uid'];
            $admin['mer_id'] = $res->id;
            $admin['real_name'] = $data['mer_name'];
            $admin['rules'] = $data['rules'];
            $admin['phone'] = $data['mer_phone'];
            $admin['add_time'] = time();
            $admin['status'] = 1;
            $admin['level'] = 0;
            $res1 = MerchantAdminModel::set($admin);
        }
        $bool = false;
        if ($res1 && $res) $bool = true;
        MerchantModel::checkTrans($bool);
        if ($bool) {
            LecturerModel::where('id', $id)->update(['mer_id' => $res->id, 'lecturer_name' => $data['mer_name'], 'phone' => $data['mer_phone']]);
            Special::where('lecturer_id', $id)->update(['mer_id' => $res->id]);
            User::where('uid', $data['uid'])->update(['business' => 1]);
            return JsonService::successful('添加讲师后台成功!');
        } else {
            return JsonService::successful('添加讲师后台失败!');
        }

    }
}
