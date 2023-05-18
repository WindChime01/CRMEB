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
use app\merchant\model\questions\Questions as QuestionsModel;
use app\merchant\model\questions\Relation;
use app\merchant\model\questions\QuestionsCategpry;
use app\merchant\model\questions\TestPaperQuestions;
use app\merchant\model\questions\ExaminationTestRecord;

/**
 * 试题
 * Class Questions
 */
class Questions extends AuthController
{
    /**
     * 题库列表
     */
    public function index()
    {
        $this->assign(['category' => QuestionsCategpry::taskCategoryAll(2, $this->merchantId)]);
        return $this->fetch();
    }

    /**
     * 获取题库列表
     */
    public function getQuestionsList()
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['type', ''],
            ['pid', 0],
            ['title', ''],
            ['excel', 0]
        ]);
        $where['mer_id'] = $this->merchantId;
        return Json::successlayui(QuestionsModel::questionsList($where));
    }

    /**
     * 试题导入
     */
    public function importTestQuestions()
    {
        $where = parent::postMore([
            ['link', '']
        ]);
        $data = QuestionsModel::GetExcelData($where['link'], 4);
        if (count($data) <= 0) return Json::fail('导入文件内容为空');
        $res = QuestionsModel::importQuestions($data, $this->merchantId);
        if ($res)
            return Json::successful('导入成功');
        else
            return Json::fail('导入失败');
    }

    /**
     * 下载试题导入文件
     */
    public function downloadExcel()
    {
        QuestionsModel::getExcel();
    }

    /**
     * @return mixed
     */
    public function imports()
    {
        return $this->fetch('import');
    }

    /**
     * 上传文件
     * @return string
     */
    public function file_import_upload()
    {
        $res = Upload::file('file', 'config/file');
        if (!$res->status) return Json::fail($res->error);
        return Json::successful('上传成功!', ['filePath' => $res->filePath]);
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
        $res = parent::getDataModification('questions', $id, $field, $value);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**关联试题知识点
     * @param int $id
     * @return mixed
     */
    public function knowledge($id = 0)
    {
        if (!$id) Json::fail('缺少参数');
        $this->assign(['id' => $id]);
        return $this->fetch();
    }

    /**获取试题关联的专题
     * @param int $id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function knowledge_points($id = 0)
    {
        if (!$id) Json::fail('缺少参数');
        $data = Relation::getQuestionsRelationSpecial($id, 3);
        foreach ($data['data'] as $k => &$v) {
            if ($v['type'] == 6) $v['type'] = $v['light_type'];
            $v['types'] = parent::specialTaskType($v['type']);
        }
        return Json::successlayui($data);
    }

    /**试题关联专题
     * @param int $id
     */
    public function add_knowledge_points($id, $special_ids)
    {
        if (!$id) Json::fail('缺少参数');
        $res = Relation::setRelations($id, $special_ids, 3);
        if ($res)
            return Json::successful('关联成功');
        else
            return Json::fail('关联失败');
    }

    /**试题知识点排序
     * @param int $id
     * @param int $special_id
     * @param $value
     */
    public function up_knowledge_points_sort($id, $special_id, $value)
    {
        if (!$id || !$special_id) Json::fail('缺少参数');
        $res = Relation::updateRelationSort($id, $special_id, 3, $value);
        if ($res)
            return Json::successful('修改成功');
        else
            return Json::fail('修改失败');
    }

    /**删除关联专题
     * @param int $id
     * @param int $special_id
     * @throws \think\exception\DbException
     */
    public function delete_knowledge_points($id = 0, $special_id = 0)
    {
        if (!$id || !$special_id) Json::fail('缺少参数');
        $res = Relation::delRelation($id, $special_id, 3);
        if ($res)
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }

    /**关联专题
     * @param int $id
     */
    public function relation($id = 0)
    {
        if (!$id) Json::fail('缺少参数');
        $this->assign(['id' => $id]);
        return $this->fetch('relation');
    }

    /**添加/编辑
     * @param int $id
     * @return mixed
     */
    public function add($id = 0)
    {
        $questions = $id > 0 ? QuestionsModel::get($id) : [];
        $this->assign(['id' => $id, 'questions' => json_encode($questions)]);
        return $this->fetch('subject');
    }

    /**
     * 获取试题分类
     */
    public function get_subject_list()
    {
        $category = QuestionsCategpry::taskCategoryAll(2, $this->merchantId);
        return Json::successful($category);
    }

    /**添加/编辑试题
     * @param int $id
     */
    public function save_add($id = 0)
    {
        $data = parent::postMore([
            ['question_type', 1],
            ['is_img', 0],
            ['pid', 0],
            ['stem', ''],
            ['image', ''],
            ['option', ''],
            ['answer', ''],
            ['difficulty', ''],
            ['analysis', ''],
            ['sort', 0]
        ]);
        if ($data['pid'] <= 0) return Json::fail('请选择试题分类');
        if (!$data['stem']) return Json::fail('请输入试题题干');
        if (!$data['option']) return Json::fail('请输入试题选项');
        if (!$data['answer']) return Json::fail('请设置正确答案');
        if ($id) {
            $res = QuestionsModel::edit($data, $id);
        } else {
            $data['add_time'] = time();
            $data['mer_id'] = $this->merchantId;
            if (QuestionsModel::be(['stem' => $data['stem'], 'mer_id' => $this->merchantId, 'is_del' => 0])) {
                return Json::fail('试题题干已存在！');
            }
            $res = QuestionsModel::set($data);
        }
        if ($res) {
            return Json::successful('添加/编辑成功');
        } else {
            return Json::fail('添加/编辑失败');
        }
    }

    /**删除试题
     * @param int $id
     */
    public function delete($id = 0)
    {
        if (!$id) return Json::fail('参数错误');
        $questions = QuestionsModel::get($id);
        if (!$questions) return Json::fail('要删除的试题不存在');
        if (TestPaperQuestions::be(['questions_id' => $id])) return Json::fail('试题在试卷中使用，不能删除；请先在试卷中删除');
        $res = parent::getDataModification('questions', $id, 'is_del', 1);
        if ($res) {
            return Json::successful('删除成功');
        } else {
            return Json::fail('删除失败');
        }
    }

    /**获取答题情况
     * @param int $id
     */
    public function getQuestionsAnswer($id = 0)
    {
        $data = ExaminationTestRecord::testRecord($id);
        return Json::successful($data);
    }
}
