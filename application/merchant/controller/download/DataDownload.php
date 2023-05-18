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

namespace app\merchant\controller\download;

use app\merchant\controller\AuthController;
use service\JsonService as Json;
use app\merchant\model\download\DataDownloadCategpry;
use app\merchant\model\download\DataDownload as DownloadModel;
use app\merchant\model\download\DataDownloadRecords;
use service\SystemConfigService;
use think\Url;

/**资料控制器
 * Class DataDownload
 * @package app\merchant\controller\download
 */
class DataDownload extends AuthController
{
    /**
     * 资料下载列表
     */
    public function index()
    {
        $cate_list = DataDownloadCategpry::specialCategoryAll(2);
        $this->assign('cate_list', $cate_list);
        return $this->fetch();
    }

    /**
     * 获取资料下载列表
     */
    public function data_download_list()
    {
        $where = parent::getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['status', ''],
            ['page', 1],
            ['limit', 20],
            ['cate_id', 0],
            ['is_show', ''],
            ['title', '']
        ]);
        $where['mer_id'] = $this->merchantId;
        return Json::successlayui(DownloadModel::get_download_list($where));
    }

    /**资料审核
     * @return mixed
     */
    public function examine()
    {
        $cate_list = DataDownloadCategpry::specialCategoryAll(2);
        $this->assign('cate_list', $cate_list);
        return $this->fetch();
    }

    /**获得审核资料
     * @throws \think\Exception
     */
    public function data_download_examine_list()
    {
        $where = parent::getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['page', 1],
            ['limit', 20],
            ['cate_id', 0],
            ['is_show', ''],
            ['title', '']
        ]);
        $where['mer_id'] = $this->merchantId;
        return Json::successlayui(DownloadModel::get_download_examine_list($where));
    }

    public function get_cate_list()
    {
        $cate_list = DataDownloadCategpry::specialCategoryAll(2);
        return Json::successful($cate_list);
    }

    /**资料编辑、添加
     * @param int $id
     */
    public function add($id = 0)
    {
        if ($id) {
            $download = DownloadModel::get($id);
            if (!$download) return Json::fail('资料不存在');
            $this->assign(['download' => json_encode($download)]);
        }
        $this->assign(['id' => $id]);
        return $this->fetch();
    }

    /**保存、编辑轻专题
     * @param int $id
     */
    public function save_data($id = 0)
    {
        $data = parent::postMore([
            ['title', ''],
            ['abstract', ''],
            ['cate_id', 0],
            ['sales', 0],
            ['image', ''],
            ['poster_image', ''],
            ['money', 0],
            ['sort', 0],
            ['member_money', 0],
            ['member_pay_type', 0],
            ['pay_type', 0],//支付方式：免费、付费
            ['type', 0],
            ['link', ''],
            ['network_disk_link', ''],
            ['network_disk_pwd', '']
        ]);
        if (!$data['cate_id']) return Json::fail('请选择分类');
        if (!$data['title']) return Json::fail('请输入资料标题');
        if (!$data['abstract']) return Json::fail('请输入资料简介');
        if (!$data['image']) return Json::fail('请上传资料封面图');
        if (!$data['poster_image']) return Json::fail('请上传推广海报');
        if($data['type'] == 1){
            if (!$data['link']) return Json::fail('请上传文件');
            $data['network_disk_link'] = '';
            $data['network_disk_pwd'] = '';
        }else if($data['type'] == 2){
            if (!$data['network_disk_link']) return Json::fail('请输入百度网盘文件链接');
            if (!$data['network_disk_pwd']) return Json::fail('请输入百度网盘文件获取密码');
            $data['link'] = '';
        }else{
            if (!$data['link']) return Json::fail('请上传文件');
            if (!$data['network_disk_link']) return Json::fail('请输入百度网盘文件链接');
            if (!$data['network_disk_pwd']) return Json::fail('请输入百度网盘文件获取密码');
        }
        if ($data['pay_type'] == PAY_MONEY && ($data['money'] == '' || $data['money'] == 0.00 || $data['money'] < 0)) return Json::fail('购买金额未填写或者金额非法');
        if ($data['member_pay_type'] == MEMBER_PAY_MONEY && ($data['member_money'] == '' || $data['member_money'] == 0.00 || $data['member_money'] < 0)) return Json::fail('会员购买金额未填写或金额非法');
        if ($data['pay_type'] != PAY_MONEY) {
            $data['money'] = 0;
        }
        if ($data['member_pay_type'] != MEMBER_PAY_MONEY) {
            $data['member_money'] = 0;
        }
        DownloadModel::beginTrans();
        try {
            if ($id) {
                $data['status'] = $this->isAudit == 1 ? 0 : 1;
                $res = DownloadModel::update($data, ['id' => $id]);
                if ($res) {
                    DownloadModel::commitTrans();
                    return Json::successful('修改成功');
                } else {
                    DownloadModel::rollbackTrans();
                    return Json::fail('添加失败');
                }
            } else {
                $data['is_show'] = 1;
                $data['status'] = $this->isAudit == 1 ? 0 : 1;
                $data['mer_id'] = $this->merchantId;
                $data['add_time'] = time();
                if (DownloadModel::be(['title' => $data['title'], 'mer_id' => $this->merchantId, 'is_del' => 0])) return Json::fail('资料已存在');
                $res = DownloadModel::set($data);
                if ($res) {
                    DownloadModel::commitTrans();
                    return Json::successful('添加成功');
                } else {
                    DownloadModel::rollbackTrans();
                    return Json::fail('添加失败');
                }
            }
        } catch (\Exception $e) {
            DownloadModel::rollbackTrans();
            return Json::fail($e->getMessage());
        }
    }

    public function sliceFileUpload()
    {
        $aliyunOss = \Api\AliyunOss::instance([
            'AccessKey' => SystemConfigService::get('accessKeyId'),
            'AccessKeySecret' => SystemConfigService::get('accessKeySecret'),
            'OssEndpoint' => SystemConfigService::get('end_point'),
            'OssBucket' => SystemConfigService::get('OssBucket'),
            'uploadUrl' => SystemConfigService::get('uploadUrl'),
        ]);
        $res = $aliyunOss->sliceFileUpload('file');
        if ($res) {
            return Json::successful('上传成功', ['url' => $res['url']]);
        } else {
            return Json::fail('上传失败');
        }
    }

    /**
     * 快速编辑
     * @param string $field 字段名
     * @param int $id 修改的主键
     * @param string value 修改后的值
     * @return json
     */
    public function set_value($field = '', $id = '', $value = '')
    {
        if (!$field || !$id || $value == '') Json::fail('缺少参数3');
        if ($field == 'sort' && bcsub($value, 0, 0) < 0) return Json::fail('排序不能为负数');
        if ($field == 'ficti' && bcsub($value, 0, 0) < 0) return Json::fail('虚拟下载量不能为负数');
        $res = DownloadModel::where('id', $id)->update([$field => $value]);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**资料删除
     * @param int $id
     */
    public function delete($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        $download = DownloadModel::get($id);
        if (!$download) return Json::fail('没有查到此资料');
        if ($download->is_del) return Json::fail('此资料已删除');
        $data['is_del'] = 1;
        $res = DownloadModel::edit($data, $id);
        if ($res)
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }

    /**下载记录
     * @param int $id
     * @throws \think\exception\DbException
     */
    public function records($id = 0)
    {
        $this->assign(['id' => $id, 'year' => getMonth('y')]);
        return $this->fetch();
    }

    public function get_download_records_list($id)
    {
        $where = parent::getMore([
            ['id', 0],
            ['page', 1],
            ['limit', 20],
            ['excel', 0],
            ['data', '']
        ]);
        $where['id'] = $where['id'] >= 0 ? $where['id'] : $id;
        $where['mer_id'] = $this->merchantId;
        return Json::successlayui(DataDownloadRecords::specialLearningRecordsLists($where, $where['id']));
    }

}
