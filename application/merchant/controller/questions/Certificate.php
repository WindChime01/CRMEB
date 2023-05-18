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

namespace app\merchant\controller\questions;

use app\merchant\controller\AuthController;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use think\Url;
use service\FormBuilder as Form;
use app\merchant\model\questions\Certificate as CertificateModel;
use app\merchant\model\questions\TestPaper as TestPaperModel;
use app\merchant\model\questions\CertificateRecord;

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
        ]);
        $where['mer_id'] = $this->merchantId;
        return Json::successlayui(CertificateModel::getCertificateList($where));
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
            $data['status'] = $this->isAudit == 1 ? 0 : 1;
            $res = CertificateModel::edit($data, $id);
        } else {
            $data['add_time'] = time();
            $data['status'] = $this->isAudit == 1 ? 0 : 1;
            $data['mer_id'] = $this->merchantId;
            if (CertificateModel::be(['title' => $data['title'], 'mer_id' => $this->merchantId, 'is_del' => 0])) {
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
        $this->assign(['certificate' => CertificateModel::certificateList($this->merchantId)]);
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
        $where['mer_id'] = $this->merchantId;
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
}
