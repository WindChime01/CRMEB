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

namespace app\admin\controller\download;

use app\admin\controller\AuthController;
use service\JsonService as Json;
use app\admin\model\download\DataDownloadCategpry;
use app\admin\model\download\DataDownload as DownloadModel;
use app\admin\model\download\DataDownloadRecords;
use service\SystemConfigService;
use app\admin\model\system\Recommend;
use app\admin\model\system\RecommendRelation;
use app\admin\model\system\WebRecommend;
use app\admin\model\system\WebRecommendRelation;
use app\admin\model\merchant\Merchant;
use service\FormBuilder as Form;
use think\Url;

/**资料控制器
 * Class DataDownload
 * @package app\admin\controller\download
 */
class DataDownload extends AuthController
{
    /**
     * 资料下载列表
     */
    public function index()
    {
        $cate_list = DataDownloadCategpry::specialCategoryAll(2);
        $mer_list = Merchant::getMerchantList();
        $this->assign([
            'cate_list' => $cate_list,
            'mer_list' => $mer_list
        ]);
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
            ['status', 1],
            ['page', 1],
            ['limit', 20],
            ['cate_id', 0],
            ['mer_id', 0],
            ['is_show', ''],
            ['title', '']
        ]);
        return Json::successlayui(DownloadModel::get_download_list($where));
    }

    /**资料审核
     * @return mixed
     */
    public function examine()
    {
        $cate_list = DataDownloadCategpry::specialCategoryAll(2);
        $mer_list = Merchant::getMerchantList();
        $this->assign([
            'cate_list' => $cate_list,
            'mer_list' => $mer_list
        ]);
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
            ['status', ''],
            ['page', 1],
            ['limit', 20],
            ['cate_id', 0],
            ['mer_id', 0],
            ['title', '']
        ]);
        return Json::successlayui(DownloadModel::get_download_examine_list($where));
    }

    public function examineDetails($id)
    {
        if (!$id) return Json::fail('参数错误');
        $details = DownloadModel::get($id);
        if (!$details) return Json::fail('资料不存在');
        $this->assign(['details' => json_encode($details)]);
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
        if (!DownloadModel::be(['id' => $id, 'status' => 0])) return Json::fail('操作记录不存在或状态错误!');
        $download = DownloadModel::get($id);
        if (!$download) return Json::fail('操作记录不存!');
        if ($download->status != 0) return Json::fail('您已审核,请勿重复操作');
        DownloadModel::beginTrans();
        $res = DownloadModel::changeFail($id, $download['mer_id'], $fail_msg['message']);
        if ($res) {
            DownloadModel::commitTrans();
            return Json::successful('操作成功!');
        } else {
            DownloadModel::rollbackTrans();
            return Json::fail('操作失败!');
        }
    }

    /**通过
     * @param $id
     * @throws \think\exception\DbException
     */
    public function succ($id)
    {
        if (!DownloadModel::be(['id' => $id, 'status' => 0])) return Json::fail('操作记录不存在或状态错误!');
        $download = DownloadModel::get($id);
        if (!$download) return Json::fail('操作记录不存!');
        if ($download->status != 0) return Json::fail('您已审核,请勿重复操作');
        DownloadModel::beginTrans();
        $res = DownloadModel::changeSuccess($id, $download['mer_id']);
        if ($res) {
            DownloadModel::commitTrans();
            return Json::successful('操作成功!');
        } else {
            DownloadModel::rollbackTrans();
            return Json::fail('操作失败!');
        }
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
        if ($data['type'] == 1) {
            if (!$data['link']) return Json::fail('请上传文件');
            $data['network_disk_link'] = '';
            $data['network_disk_pwd'] = '';
        } else if ($data['type'] == 2) {
            if (!$data['network_disk_link']) return Json::fail('请输入百度网盘文件链接');
            if (!$data['network_disk_pwd']) return Json::fail('请输入百度网盘文件获取密码');
            $data['link'] = '';
        } else {
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
                $res = DownloadModel::edit($data, $id, 'id');
                if ($res) {
                    DownloadModel::commitTrans();
                    return Json::successful('修改成功');
                } else {
                    DownloadModel::rollbackTrans();
                    return Json::fail('添加失败');
                }
            } else {
                $data['is_show'] = 1;
                $data['add_time'] = time();
                if (DownloadModel::be(['title' => $data['title'], 'is_del' => 0])) return Json::fail('资料已存在');
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

    /**
     * 添加推荐
     * @param int $special_id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function recommend($data_id = 0)
    {
        if (!$data_id) $this->failed('缺少参数');
        $download = DownloadModel::get($data_id);
        if (!$download) $this->failed('没有查到此资料');
        if ($download->is_del) $this->failed('此资料已删除');
        $form = Form::create(Url::build('save_recommend', ['data_id' => $data_id]), [
            Form::select('recommend_id', '推荐')->setOptions(function () {
                $model = Recommend::where(['is_show' => 1, 'is_fixed' => 0, 'type' => 14]);
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
    public function save_recommend($data_id = 0)
    {
        if (!$data_id) return Json::fail('缺少参数');
        $data = parent::postMore([
            ['recommend_id', 0],
            ['sort', 0],
        ]);
        if (!$data['recommend_id']) return Json::fail('请选择推荐');
        $recommend = Recommend::get($data['recommend_id']);
        if (!$recommend) return Json::fail('导航菜单不存在');
        $data['add_time'] = time();
        $data['type'] = $recommend->type;
        $data['link_id'] = $data_id;
        if (RecommendRelation::be(['type' => $recommend->type, 'link_id' => $data_id, 'recommend_id' => $data['recommend_id']])) return Json::fail('已推荐,请勿重复推荐');
        if (RecommendRelation::set($data))
            return Json::successful('推荐成功');
        else
            return Json::fail('推荐失败');
    }

    /**取消推荐
     * @param int $id
     */
    public function cancel_recommendation($id = 0, $data_id = 0)
    {
        if (!$id || !$data_id) return Json::fail('缺少参数');
        if (RecommendRelation::be(['id' => $id, 'link_id' => $data_id])) {
            $res = RecommendRelation::where(['id' => $id, 'link_id' => $data_id])->delete();
            if ($res)
                return Json::successful('取消推荐成功');
            else
                return Json::fail('取消推荐失败');
        } else {
            return Json::fail('推荐不存在');
        }
    }

    /**
     * 添加推荐
     * @param int $data_id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function web_recommend($data_id = 0)
    {
        if (!$data_id) $this->failed('缺少参数');
        $download = DownloadModel::get($data_id);
        if (!$download) $this->failed('没有查到此资料');
        if ($download->is_del) $this->failed('此资料已删除');
        $form = Form::create(Url::build('save_web_recommend', ['data_id' => $data_id]), [
            Form::select('recommend_id', '推荐')->setOptions(function () {
                $model = WebRecommend::where(['is_show' => 1, 'type' => 3]);
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
    public function save_web_recommend($data_id = 0)
    {
        if (!$data_id) $this->failed('缺少参数');
        $data = parent::postMore([
            ['recommend_id', 0],
            ['sort', 0],
        ]);
        if (!$data['recommend_id']) return Json::fail('请选择推荐');
        $recommend = WebRecommend::get($data['recommend_id']);
        if (!$recommend) return Json::fail('导航菜单不存在');
        $data['add_time'] = time();
        $data['type'] = $recommend->type;
        $data['link_id'] = $data_id;
        if (WebRecommendRelation::be(['type' => $recommend->type, 'link_id' => $data_id, 'recommend_id' => $data['recommend_id']])) return Json::fail('已推荐,请勿重复推荐');
        if (WebRecommendRelation::set($data))
            return Json::successful('推荐成功');
        else
            return Json::fail('推荐失败');
    }

    /**取消推荐
     * @param int $id
     */
    public function cancel_web_recommendation($id = 0, $data_id = 0)
    {
        if (!$id || !$data_id) return Json::fail('缺少参数');
        if (WebRecommendRelation::be(['id' => $id, 'link_id' => $data_id])) {
            $res = WebRecommendRelation::where(['id' => $id, 'link_id' => $data_id])->delete();
            if ($res)
                return Json::successful('取消推荐成功');
            else
                return Json::fail('取消推荐失败');
        } else {
            return Json::fail('推荐不存在');
        }
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
        return Json::successlayui(DataDownloadRecords::specialLearningRecordsLists($where, $where['id']));
    }

    /**资料转增
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function increase($id = 0)
    {
        if (!$id) $this->failed('缺少参数');
        $download = DownloadModel::get($id);
        if (!$download) $this->failed('没有查到此资料');
        if ($download->is_del) $this->failed('此资料已删除');
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
        $form->setMethod('post')->setTitle('资料转增')->setSuccessScript('parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},800);');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 资料转增
     * @param int $id
     * @throws \think\exception\DbException
     */
    public function change_increase($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        $data = parent::postMore([
            ['mer_id', 0],
        ]);
        $res = DownloadModel::edit($data, $id, 'id');
        if ($res)
            return Json::successful('资料转增成功');
        else
            return Json::fail('资料转增失败');
    }
}
