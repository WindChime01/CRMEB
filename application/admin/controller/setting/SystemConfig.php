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

namespace app\admin\controller\setting;

use think\Url;
use service\FormBuilder as Form;
use think\Request;
use service\JsonService as Json;
use service\UploadService as Upload;
use app\admin\controller\AuthController;
use app\admin\model\system\SystemConfig as ConfigModel;

/**
 *  配置列表控制器
 * Class SystemConfig
 * @package app\admin\controller\setting
 */
class SystemConfig extends AuthController
{
    /**
     * 基础配置
     * */
    public function index()
    {
        list($type, $pid, $tab_id, $children_tab_id) = parent::getMore([
            ['type', $this->request->param('type', 0)],//配置类型
            ['paid', 0],//父级分类ID
            ['tab_id', $this->request->param('tab_id', 0)],//当前分类ID
            ['children_tab_id', null],//当前子集分类ID
        ], null, true);

        $config_tab = null;//顶级分类
        $children_config_tab = null;//二级分类

        if ($type == 3) {//其它分类
            $config_tab = null;
        } else {
            $config_tab = ConfigModel::getConfigTabAll($type);
        }
        if ($pid) {
            $children_config_tab = ConfigModel::getConfigChildrenTabAll($pid);
            foreach ($children_config_tab as $kk => $vv) {
                $arr = ConfigModel::getAll($vv['id'])->toArray();
                if (empty($arr)) {
                    unset($children_config_tab[$kk]);
                }
            }
            $tab_id = $pid;
            //表单字段
            $list = ConfigModel::getAll($children_tab_id);
        } else {
            $children_config_tab = ConfigModel::getConfigChildrenTabAll($tab_id);
            foreach ($children_config_tab as $kk => $vv) {
                $arr = ConfigModel::getAll($vv['id'])->toArray();
                if (empty($arr)) {
                    unset($children_config_tab[$kk]);
                }
            }
            if (!$children_tab_id && $children_config_tab) $children_tab_id = $children_config_tab[0]['id'];
            //表单字段
            $list = ConfigModel::getAll($tab_id);
        }
        $this->assign('pid', $pid);
        $this->assign('children_tab_id', $children_tab_id);
        $this->assign('tab_id', $tab_id);
        $this->assign('config_tab', $config_tab);
        $this->assign('children_config_tab', $children_config_tab);
        $formbuider = [];
        foreach ($list as $data) {
            switch ($data['type']) {
                case 'text'://文本框
                    switch ($data['input_type']) {
                        case 'input':
                            $data['value'] = json_decode($data['value'], true) ?: '';
                            $formbuider[] = Form::input($data['menu_name'], $data['info'], $data['value'])->info($data['desc'])->placeholder($data['desc'])->col(13);
                            break;
                        case 'number':
                            $data['value'] = json_decode($data['value'], true) ?: 0;
                            $formbuider[] = Form::number($data['menu_name'], $data['info'], $data['value'])->info($data['desc'])->min(0);
                            break;
                        case 'dateTime':
                            $formbuider[] = Form::dateTime($data['menu_name'], $data['info'], $data['value'])->info($data['desc']);
                            break;
                        case 'color':
                            $data['value'] = json_decode($data['value'], true) ?: '';
                            $formbuider[] = Form::color($data['menu_name'], $data['info'], $data['value'])->info($data['desc']);
                            break;
                        default:
                            $data['value'] = json_decode($data['value'], true) ?: '';
                            $formbuider[] = Form::input($data['menu_name'], $data['info'], $data['value'])->info($data['desc'])->placeholder($data['desc'])->col(13);
                            break;
                    }
                    break;
                case 'textarea'://多行文本框
                    $data['value'] = json_decode($data['value'], true) ?: '';
                    $formbuider[] = Form::textarea($data['menu_name'], $data['info'], $data['value'])->placeholder($data['desc'])->info($data['desc'])->rows(6)->col(13);
                    break;
                case 'radio'://单选框
                    $data['value'] = json_decode($data['value'], true) ?: '0';
                    $parameter = explode("\n", $data['parameter']);
                    $options = [];
                    if ($parameter) {
                        foreach ($parameter as $v) {
                            if (strstr($v, '=>') !== false) {
                                $pdata = explode("=>", $v);
                            } else if (strstr($v, '=') !== false) {
                                $pdata = explode("=", $v);
                            }
                            $options[] = ['label' => $pdata[1], 'value' => $pdata[0]];
                        }
                        $formbuider[] = Form::radio($data['menu_name'], $data['info'], $data['value'])->options($options)->info($data['desc'])->col(13);
                    }
                    break;
                case 'upload'://文件上传
                    switch ($data['upload_type']) {
                        case 1:
                            $data['value'] = json_decode($data['value'], true) ?: '';
                            $formbuider[] = Form::frameImageOne($data['menu_name'], $data['info'], Url::build('admin/widget.images/index', array('fodder' => $data['menu_name'])), $data['value'])->icon('image')->width('70%')->height('500px')->info($data['desc'])->col(13);
                            break;
                        case 2:
                            $data['value'] = json_decode($data['value'], true) ?: [];
                            $formbuider[] = Form::frameImages($data['menu_name'], $data['info'], Url::build('admin/widget.images/index', array('fodder' => $data['menu_name'])), $data['value'])->maxLength(5)->icon('image')->width('70%')->height('500px')->info($data['desc'])->col(13);
                            break;
                        case 3:
                            $data['value'] = json_decode($data['value'], true);
                            $formbuider[] = Form::uploadFileOne($data['menu_name'], $data['info'], Url::build('file_upload'), $data['value'])->name('file')->info($data['desc'])->col(13);
                            break;
                    }

                    break;
                case 'checkbox'://多选框
                    $data['value'] = json_decode($data['value'], true) ?: [];
                    $parameter = explode("\n", $data['parameter']);
                    $options = [];
                    if ($parameter) {
                        foreach ($parameter as $v) {
                            if (strstr($v, '=>') !== false) {
                                $pdata = explode("=>", $v);
                            } else if (strstr($v, '=') !== false) {
                                $pdata = explode("=", $v);
                            }
                            $options[] = ['label' => $pdata[1], 'value' => $pdata[0]];
                        }
                        if (!is_array($data['value'])) {
                            $value = [$data['value']];
                        } else {
                            $value = $data['value'];
                        }
                        $formbuider[] = Form::checkbox($data['menu_name'], $data['info'], $value)->options($options)->info($data['desc'])->col(13);
                    }
                    break;
                case 'select'://多选框
                    $data['value'] = json_decode($data['value'], true) ?: [];
                    $parameter = explode("\n", $data['parameter']);
                    $options = [];
                    if ($parameter) {
                        foreach ($parameter as $v) {
                            if (strstr($v, '=>') !== false) {
                                $pdata = explode("=>", $v);
                            } else if (strstr($v, '=') !== false) {
                                $pdata = explode("=", $v);
                            }
                            $options[] = ['label' => $pdata[1], 'value' => $pdata[0]];
                        }
                        $formbuider[] = Form::select($data['menu_name'], $data['info'], $data['value'])->options($options)->info($data['desc'])->col(13);
                    }
                    break;
            }
        }
        $form = Form::make_post_form('编辑配置', $formbuider, Url::build('save_basics'), 2);
        $this->assign(compact('form'));
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 添加字段
     * */
    public function create(Request $request)
    {
        $data = parent::getMore(['type'], $request);//接收参数
        $tab_id = $request->param('tab_id', 1);
        $formbuider = array();
        switch ($data['type']) {
            case 0://文本框
                $formbuider = ConfigModel::createInputRule($tab_id);
                break;
            case 1://多行文本框
                $formbuider = ConfigModel::createTextAreaRule($tab_id);
                break;
            case 2://单选框
                $formbuider = ConfigModel::createRadioRule($tab_id);
                break;
            case 3://文件上传
                $formbuider = ConfigModel::createUploadRule($tab_id);
                break;
            case 4://多选框
                $formbuider = ConfigModel::createCheckboxRule($tab_id);
                break;
        }
        $form = Form::make_post_form('添加字段', $formbuider, Url::build('save'), 2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存字段
     * */
    public function save(Request $request)
    {
        $data = parent::postMore([
            'menu_name',
            'type',
            'config_tab_id',
            'parameter',
            'upload_type',
            'required',
            'width',
            'high',
            'value',
            'info',
            'desc',
            'sort',
            'status',], $request);
        if (!$data['info']) return Json::fail('请输入配置名称');
        if (!$data['menu_name']) return Json::fail('请输入字段名称');
        if ($data['menu_name']) {
            $oneConfig = ConfigModel::getOneConfig('menu_name', $data['menu_name']);
            if (!empty($oneConfig)) return Json::fail('请重新输入字段名称,之前的已经使用过了');
        }
        if (!$data['desc']) return Json::fail('请输入配置简介');
        if ($data['sort'] < 0) {
            $data['sort'] = 0;
        }
        if ($data['type'] == 'text') {
            if (!ConfigModel::valiDateTextRole($data)) return Json::fail(ConfigModel::getErrorInfo());
        }
        if ($data['type'] == 'textarea') {
            if (!ConfigModel::valiDateTextareaRole($data)) return Json::fail(ConfigModel::getErrorInfo());
        }
        if ($data['type'] == 'radio' || $data['type'] == 'checkbox') {
            if (!$data['parameter']) return Json::fail('请输入配置参数');
            if (!ConfigModel::valiDateRadioAndCheckbox($data)) return Json::fail(ConfigModel::getErrorInfo());
            $data['value'] = json_encode($data['value']);
        }
        ConfigModel::set($data);
        return Json::successful('添加菜单成功!');
    }

    /**
     * @param Request $request
     * @param $id
     * @return \think\response\Json
     */
    public function update_config(Request $request, $id)
    {
        $data = parent::postMore(['status', 'info', 'desc', 'sort', 'config_tab_id', 'required', 'parameter', 'value', 'upload_type'], $request);
        if (!ConfigModel::get($id)) return Json::fail('编辑的记录不存在!');
//        $data['value'] = rtrim($data['value'], '"');
//        $data['value'] = ltrim($data['value'], '"');
        ConfigModel::edit($data, $id);
        return Json::successful('修改成功!');
    }

    /**
     * 修改是否显示子子段
     * @param $id
     * @return mixed
     */
    public function edit_cinfig($id)
    {
        $menu = ConfigModel::get($id)->getData();
        if (!$menu) return Json::fail('数据不存在!');
        $formbuider = array();
        $formbuider[] = Form::input('menu_name', '字段变量', $menu['menu_name'])->disabled(1);
        $formbuider[] = Form::select('config_tab_id', '分类', (string)$menu['config_tab_id'])->setOptions(ConfigModel::getConfigTabAll(-1));
        $formbuider[] = Form::input('info', '配置名称', $menu['info'])->autofocus(1);
        $formbuider[] = Form::input('desc', '配置简介', $menu['desc']);
        //输入框验证规则
        if (!empty($menu['required'])) {
            $formbuider[] = Form::input('value', '默认值', $menu['value']);
            $formbuider[] = Form::number('width', '文本框宽(%)', $menu['width']);
            $formbuider[] = Form::input('required', '验证规则', $menu['required'])->placeholder('多个请用,隔开例如：required:true,url:true');
        }
        //多行文本
        if (!empty($menu['high'])) {
            $formbuider[] = Form::textarea('value', '默认值', $menu['value'])->rows(5);
            $formbuider[] = Form::number('width', '文本框宽(%)', $menu['width']);
            $formbuider[] = Form::number('high', '多行文本框高(%)', $menu['high']);
        } else {
            $formbuider[] = Form::input('value', '默认值', $menu['value']);
        }
        //单选和多选参数配置
        if (!empty($menu['parameter'])) {
            $formbuider[] = Form::textarea('parameter', '配置参数', $menu['parameter'])->placeholder("参数方式例如:\n1=白色\n2=红色\n3=黑色");
        }
        //上传类型选择
        if (!empty($menu['upload_type'])) {
            $formbuider[] = Form::radio('upload_type', '上传类型', $menu['upload_type'])->options([['value' => 1, 'label' => '单图'], ['value' => 2, 'label' => '多图'], ['value' => 3, 'label' => '文件']]);
        }
        $formbuider[] = Form::number('sort', '排序', $menu['sort']);
        $formbuider[] = Form::radio('status', '状态', $menu['status'])->options([['value' => 1, 'label' => '显示'], ['value' => 2, 'label' => '隐藏']]);

        $form = Form::make_post_form('编辑字段', $formbuider, Url::build('update_config', array('id' => $id)), 2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 删除子字段
     * @return \think\response\Json
     */
    public function delete_cinfig()
    {
        $id = input('id');
        if (!ConfigModel::del($id))
            return Json::fail(ConfigModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    /**
     * 保存数据    true
     * */
    public function save_basics()
    {
        $request = Request::instance();
        if ($request->isPost()) {
            $post = $request->post();
            if (isset($post['tab_id'])) unset($post['tab_id']);
            foreach ($post as $k => $v) {
                if (is_array($v)) {
                    $res = ConfigModel::where('menu_name', $k)->column('type,upload_type');
                    foreach ($res as $kk => $vv) {
                        if ($kk == 'upload') {
                            if ($vv == 1 || $vv == 3) {
                                $post[$k] = $v[0];
                            }
                        }
                    }
                }
            }
            foreach ($post as $k => $v) {
                if ($k == 'site_url' || $k == 'service_url') {
                    $http = "/^(http(s)?:\/\/)([0-9a-z-]{1,}.)?[0-9a-z-]{2,}.([0-9a-z-]{2,}.)?[a-z]{2,}$/i";
                    if ($k == 'service_url' && $v != '' || $k == 'site_url') {
                        if (!preg_match($http, $v) || substr_count($v, '/') > 2) {
                            return Json::fail('域名有误！应如：http://crmeb.net');
                        }
                    }
                }
                if ($k == 'gold_rate' || $k == 'single_gold_coin' || $k == 'extract_min_money' || $k == 'store_brokerage_ratio' || $k == 'store_brokerage_two' || $k == 'member_brokerage_ratio' || $k == 'deduction_proportion_ratio' || $k == 'member_brokerage_two' || $k == 'goods_brokerage_ratio' || $k == 'goods_brokerage_two' || $k == 'gold_coin_ratio' || $k == 'barrage_show_time' || $k == 'store_stock' || $k == 'order_clear_time' || $k == 'automatic_receiving_time' || $k == 'extract_price') {
                    if ($k == 'store_stock') {
                        $v = bcadd($v, 0, 0);
                    } else {
                        $v = bcadd($v, 0, 2);
                    }
                    if (bcsub($v, 0, 2) <= 0) {
                        switch ($k) {
                            case 'gold_rate':
                                return Json::fail('人民币与金币换算率不能小于等于0');
                                break;
                            case 'single_gold_coin':
                                return Json::fail('单次签到虚拟币数不能小于等于0');
                                break;
                            case 'extract_min_money':
                                return Json::fail('提现最低金额不能小于等于0');
                                break;
                            case 'barrage_show_time':
                                return Json::fail('专题弹幕停留时间不能小于等于0');
                                break;
                            case 'store_stock':
                                return Json::fail('警戒库存不能小于等于0');
                                break;
                        }
                    }
                    if (bcsub($v, 100, 2) > 0 || bcsub($v, 0, 2) < 0) {
                        switch ($k) {
                            case 'store_brokerage_ratio':
                                return Json::fail('课程一级推广人返佣比例不能大于100或小于0');
                                break;
                            case 'store_brokerage_two':
                                return Json::fail('课程二级推广人返佣比例不能大于100或小于0');
                                break;
                            case 'member_brokerage_ratio':
                                return Json::fail('会员一级推广人返佣比例不能大于100或小于0');
                                break;
                            case 'member_brokerage_two':
                                return Json::fail('会员二级推广人返佣比例不能大于100或小于0');
                                break;
                            case 'goods_brokerage_ratio':
                                return Json::fail('商品一级推广人返佣比例不能大于100或小于0');
                                break;
                            case 'goods_brokerage_two':
                                return Json::fail('商品二级推广人返佣比例不能大于100或小于0');
                                break;
                        }
                    }
                }
                ConfigModel::edit(['value' => json_encode($v)], $k, 'menu_name');
            }
            return Json::successful('修改成功');
        }
    }

    /**
     * 模板表单提交
     * */
    public function view_upload()
    {
        if ($_POST['type'] == 3) {
            $res = Upload::file($_POST['file'], 'config/file');
        } else {
            $res = Upload::Image($_POST['file'], 'config/image');
        }
        if (!$res->status) return Json::fail($res->error);
        return Json::successful('上传成功!', ['url' => $res->filePath]);
    }

    /**
     * 基础配置  单个
     * @return mixed|void
     */
    public function index_alone()
    {
        $tab_id = input('tab_id');
        if (!$tab_id) return $this->failed('参数错误，请重新打开');
        $this->assign('tab_id', $tab_id);
        $list = ConfigModel::getAll($tab_id);
        $config_tab = ConfigModel::getConfigTabAll();
        foreach ($config_tab as $kk => $vv) {
            $arr = ConfigModel::getAll($vv['value'])->toArray();
            if (empty($arr)) {
                unset($config_tab[$kk]);
            }
        }
        $this->assign('config_tab', $config_tab);
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 保存数据  单个
     * @return mixed
     */
    public function save_basics_alone()
    {
        $request = Request::instance();
        if ($request->isPost()) {
            $post = $request->post();
            $tab_id = $post['tab_id'];
            unset($post['tab_id']);
            foreach ($post as $k => $v) {
                ConfigModel::edit(['value' => json_encode($v)], $k, 'menu_name');
            }
            return $this->successfulNotice('修改成功');
        }
    }

    /**
     * 获取文件名
     * */
    public function getImageName()
    {
        $request = Request::instance();
        $post = $request->post();
        $src = $post['src'];
        $data['name'] = basename($src);
        exit(json_encode($data));
    }

    /**
     * 上传文件
     * @return string
     */
    public function file_upload()
    {
        $res = Upload::file('file', 'config/file');
        if (!$res->status) return Json::fail($res->error);
        return Json::successful('上传成功!', ['filePath' => $res->filePath]);
    }
}
