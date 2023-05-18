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

namespace app\admin\controller\questions;

use app\admin\controller\AuthController;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use think\Url;
use service\FormBuilder as Form;
use app\admin\model\questions\Certificate as CertificateModel;
use app\admin\model\questions\TestPaper as TestPaperModel;
use app\admin\model\questions\CertificateRecord;
use app\admin\model\merchant\Merchant;

/**
 * 证书
 * Class Certificate
 */
class Certificate extends AuthController
{
    /**
     * 证书列表
     */
    public function index()
    {
        $mer_list = Merchant::getMerchantList();
        $this->assign([
            'mer_list' => $mer_list
        ]);
        return $this->fetch();
    }

    /**
     * 获取证书列表
     */
    public function getCertificateList()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['obtain', 0],
            ['title', ''],
            ['status', 1],
            ['mer_id', 0],
        ]);
        return Json::successlayui(CertificateModel::getCertificateList($where));
    }

    /**证书审核
     * @return mixed
     */
    public function examine()
    {
        $mer_list = Merchant::getMerchantList();
        $this->assign([
            'mer_list' => $mer_list
        ]);
        return $this->fetch();
    }

    /**获得审核证书
     * @throws \think\Exception
     */
    public function get_certificate_examine_list()
    {
        $where = parent::getMore([
            ['status', ''],
            ['page', 1],
            ['limit', 20],
            ['obtain', 0],
            ['mer_id', 0],
            ['title', '']
        ]);
        return Json::successlayui(CertificateModel::get_certificate_examine_list($where));
    }

    public function examineDetails($id)
    {
        if (!$id) return Json::fail('参数错误');
        $certificate = CertificateModel::get($id);
        if (!$certificate) return Json::fail('证书不存在');
        $this->assign(['certificate' => json_encode($certificate)]);
        return $this->fetch('material');
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
        if (!CertificateModel::be(['id' => $id, 'status' => 0])) return Json::fail('操作记录不存在或状态错误!');
        $certificate = CertificateModel::get($id);
        if (!$certificate) return Json::fail('操作记录不存!');
        if ($certificate->status != 0) return Json::fail('您已审核,请勿重复操作');
        CertificateModel::beginTrans();
        $res = CertificateModel::changeFail($id, $certificate['mer_id'], $fail_msg['message']);
        if ($res) {
            CertificateModel::commitTrans();
            return Json::successful('操作成功!');
        } else {
            CertificateModel::rollbackTrans();
            return Json::fail('操作失败!');
        }
    }

    /**通过
     * @param $id
     * @throws \think\exception\DbException
     */
    public function succ($id)
    {
        if (!CertificateModel::be(['id' => $id, 'status' => 0])) return Json::fail('操作记录不存在或状态错误!');
        $certificate = CertificateModel::get($id);
        if (!$certificate) return Json::fail('操作记录不存!');
        if ($certificate->status != 0) return Json::fail('您已审核,请勿重复操作');
        CertificateModel::beginTrans();
        $res = CertificateModel::changeSuccess($id, $certificate['mer_id']);
        if ($res) {
            CertificateModel::commitTrans();
            return Json::successful('操作成功!');
        } else {
            CertificateModel::rollbackTrans();
            return Json::fail('操作失败!');
        }
    }


    /**
     * 快速编辑
     * @param string $field 字段名
     * @param int $id 修改的主键
     * @param string value 修改后的值
     * @return json
     */
    public function set_value($field = '', $id = '', $value = '', $test = 0)
    {
        if (!$field || !$id || $value == '') Json::fail('缺少参数3');
        if ($field == 'sort' && bcsub($value, 0, 0) < 0) return Json::fail('排序不能为负数');
        $res = parent::getDataModification('certificate', $id, $field, $value);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**添加/编辑
     * @param int $id
     * @return mixed
     */
    public function add($id = 0)
    {
        $certificate = $id > 0 ? CertificateModel::get($id) : [];
        $this->assign(['id' => $id, 'certificate' => json_encode($certificate)]);
        return $this->fetch('create');
    }

    /**添加/编辑证书
     * @param int $id
     */
    public function save_add($id = 0)
    {
        $data = parent::postMore([
            ['title', ''],
            ['background', ''],
            ['qr_code', ''],
            ['obtain', 0],
            ['explain', ''],
            ['sort', 0]
        ]);
        if ($data['title'] == '') return Json::fail('请输入证书标题');
        if ($data['background'] == '') return Json::fail('请上传证书背景');
        if (mb_strlen($data['explain'], "utf-8") > 30 || mb_strlen($data['explain'], "utf-8") <= 0) return Json::fail('证书说明不能大于30个字或者为空');
        if ($id) {
            $res = CertificateModel::edit($data, $id);
        } else {
            $data['add_time'] = time();
            if (CertificateModel::be(['title' => $data['title'], 'is_del' => 0])) {
                return Json::fail('证书标题已存在！');
            }
            $res = CertificateModel::set($data);
        }
        if ($res) {
            return Json::successful('添加/编辑成功');
        } else {
            return Json::fail('添加/编辑失败');
        }
    }

    /**删除证书
     * @param int $id
     */
    public function delete($id = 0)
    {
        if (!$id) return Json::fail('参数错误');
        $test = CertificateModel::get($id);
        if (!$test) return Json::fail('要删除的证书不存在');
        $res = parent::getDataModification('certificate', $id, 'is_del', 1);
        if ($res) {
            return Json::successful('删除成功');
        } else {
            return Json::fail('删除失败');
        }
    }

    /**
     *试卷列表
     */
    public function getTestPaperList()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['pid', ''],
            ['type', 2],
            ['title', '']
        ]);
        return Json::successlayui(TestPaperModel::testPaperExercisesList($where));
    }

    /**证书获取记录
     * @return mixed
     */
    public function record()
    {
        $this->assign(['certificate' => CertificateModel::certificateList()]);
        return $this->fetch();
    }

    /**
     * 证书获取记录列表
     */
    public function getCertificateRecord()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['cid', 0],
            ['title', ''],
            ['excel', 0]
        ]);
        return Json::successlayui(CertificateRecord::getCertificateRecordList($where));
    }

    /**证书获取记录删除
     * @param int $id
     * @throws \think\exception\DbException
     */
    public function deleteRecord($id = 0)
    {
        if (!$id) Json::fail('缺少参数');
        $test = CertificateRecord::get($id);
        if (!$test) return Json::fail('要删除的记录不存在');
        $res = parent::getDataModification('record', $id, 'is_del', 1);
        if ($res) {
            return Json::successful('删除成功');
        } else {
            return Json::fail('删除失败');
        }
    }

    /**证书获取记录撤销
     * @param int $id
     * @throws \think\exception\DbException
     */
    public function revokeRecord($id = 0)
    {
        if (!$id) Json::fail('缺少参数');
        $test = CertificateRecord::get($id);
        if (!$test) return Json::fail('要撤销的记录不存在');
        $res = parent::getDataModification('record', $id, 'status', 0);
        if ($res) {
            return Json::successful('撤销成功');
        } else {
            return Json::fail('撤销失败');
        }
    }

    /**证书转增
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function increase($id = 0)
    {
        if (!$id) $this->failed('缺少参数');
        $Certificate = CertificateModel::get($id);
        if (!$Certificate) $this->failed('没有查到此荣誉证书');
        if ($Certificate->is_del) $this->failed('此荣誉证书已删除');
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
        $form->setMethod('post')->setTitle('证书转增')->setSuccessScript('parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},800);');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 证书转增
     * @param int $id
     * @throws \think\exception\DbException
     */
    public function change_increase($id = 0)
    {
        if (!$id) $this->failed('缺少参数');
        $data = parent::postMore([
            ['mer_id', 0],
        ]);
        $res = CertificateModel::edit($data, $id, 'id');
        if ($res)
            return Json::successful('证书转增成功');
        else
            return Json::fail('证书转增失败');
    }
}
