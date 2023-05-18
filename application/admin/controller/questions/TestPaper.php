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
use app\admin\model\questions\TestPaper as TestPaperModel;
use app\admin\model\questions\TestPaperCategory;
use app\admin\model\questions\QuestionsCategpry;
use app\admin\model\questions\Questions;
use app\admin\model\questions\TestPaperQuestions;
use app\admin\model\questions\TestPaperScoreGrade;
use app\admin\model\questions\TestPaperObtain;
use app\admin\model\questions\Certificate;
use app\admin\model\questions\CertificateRelated;
use app\admin\model\questions\ExaminationRecord;
use app\admin\model\special\Special;
use app\admin\model\system\Recommend;
use app\admin\model\system\RecommendRelation;
use app\admin\model\merchant\Merchant;
use app\admin\model\system\WebRecommend;
use app\admin\model\system\WebRecommendRelation;
use app\admin\model\user\User;

/**
 * 试卷
 * Class TestPaper
 */
class TestPaper extends AuthController
{
    /**
     * 试卷列表
     */
    public function index($type = 1)
    {
        $this->assign([
            'type' => $type,
            'mer_list' => Merchant::getMerchantList(),
            'category' => TestPaperCategory::taskCategoryAll(2, $type)
        ]);
        return $this->fetch();
    }

    /**
     * 获取试卷列表
     */
    public function getTestPaperExercisesList($type = 1)
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['pid', 0],
            ['is_show', ''],
            ['status', 1],
            ['mer_id', 0],
            ['title', '']
        ]);
        $where['type'] = $type;
        return Json::successlayui(TestPaperModel::testPaperExercisesList($where));
    }

    /**资料审核
     * @return mixed
     */
    public function examine($type = 1)
    {
        $category = TestPaperCategory::taskCategoryAll(2, $type);
        $mer_list = Merchant::getMerchantList();
        $this->assign([
            'type' => $type,
            'category' => $category,
            'mer_list' => $mer_list
        ]);
        return $this->fetch();
    }

    /**获得审核资料
     * @throws \think\Exception
     */
    public function get_test_paper_examine_list($type = 1)
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['pid', 0],
            ['is_show', ''],
            ['status', ''],
            ['mer_id', 0],
            ['title', '']
        ]);
        $where['type'] = $type;
        return Json::successlayui(TestPaperModel::testPaperExercisesExamineList($where));
    }

    /**审核
     * @param $id
     * @param $type
     * @return mixed|void
     * @throws \think\exception\DbException
     */
    public function examineDetails($id,$type)
    {
        if (!$id) return Json::fail('参数错误');
        $details = TestPaperModel::get($id);
        if (!$details) return Json::fail('试卷不存在');
        $grade = [];
        if ($details && $id) {
            $single_tmp_list = TestPaperQuestions::gettestPaperQuestions($id, 1);
            $many_tmp_list = TestPaperQuestions::gettestPaperQuestions($id, 2);
            $judge_tmp_list = TestPaperQuestions::gettestPaperQuestions($id, 3);
            if ($type == 2) $grade = TestPaperScoreGrade::testPaperScoreGradeList($id);
        } else {
            $single_tmp_list = [];
            $many_tmp_list = [];
            $judge_tmp_list = [];
        }
        $this->assign([
            'type' => $type,
            'details' => json_encode($details),
            'grade' => json_encode($grade),
            'single_tmp_list' => json_encode($single_tmp_list),
            'many_tmp_list' => json_encode($many_tmp_list),
            'judge_tmp_list' => json_encode($judge_tmp_list)
        ]);
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
        if (!TestPaperModel::be(['id' => $id, 'status' => 0])) return Json::fail('操作记录不存在或状态错误!');
        $test = TestPaperModel::get($id);
        if (!$test) return Json::fail('操作记录不存!');
        if ($test->status != 0) return Json::fail('您已审核,请勿重复操作');
        TestPaperModel::beginTrans();
        $res = TestPaperModel::changeFail($id, $test['mer_id'], $fail_msg['message']);
        if ($res) {
            TestPaperModel::commitTrans();
            return Json::successful('操作成功!');
        } else {
            TestPaperModel::rollbackTrans();
            return Json::fail('操作失败!');
        }
    }

    /**通过
     * @param $id
     * @throws \think\exception\DbException
     */
    public function succ($id)
    {
        if (!TestPaperModel::be(['id' => $id, 'status' => 0])) return Json::fail('操作记录不存在或状态错误!');
        $test = TestPaperModel::get($id);
        if (!$test) return Json::fail('操作记录不存!');
        if ($test->status != 0) return Json::fail('您已审核,请勿重复操作');
        TestPaperModel::beginTrans();
        $res = TestPaperModel::changeSuccess($id, $test['mer_id']);
        if ($res) {
            TestPaperModel::commitTrans();
            return Json::successful('操作成功!');
        } else {
            TestPaperModel::rollbackTrans();
            return Json::fail('操作失败!');
        }
    }


    /**
     * 用户答题记录
     */
    public function answerNotes($type = 1, $test_id = 0, $uid = 0)
    {
        $this->assign(['type' => $type, 'test_id' => $test_id, 'uid' => $uid, 'testPaper' => TestPaperModel::testPaperList($type)]);
        return $this->fetch('record');
    }

    /**
     * 获取试卷列表
     */
    public function getExaminationRecords($type = 1, $testId = 0, $uid = 0)
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['test_id', 0],
            ['title', ''],
            ['excel', 0]
        ]);
        $where['type'] = $type;
        if ($testId) $where['test_id'] = $testId;
        if ($uid) $where['uid'] = $uid;
        return Json::successlayui(ExaminationRecord::getExaminationRecord($where));
    }

    /**
     * 用户答题
     */
    public function answers($record_id = 0, $test_id = 0, $type = 1, $uid =0)
    {
        $dat=TestPaperModel::where('id',$test_id)->field('single_sort,many_sort,judge_sort')->find();
        $this->assign(['record_id' => $record_id, 'test_id' => $test_id, 'type' => $type, 'uid' => $uid, 'single_sort' => $dat['single_sort'], 'many_sort' => $dat['many_sort'], 'judge_sort' => $dat['judge_sort']]);
        return $this->fetch();
    }

    /**答题信息
     * @param $uid
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserInformation($uid)
    {
        if(!$uid) return Json::fail('参数错误');
        $data=User::where(['uid'=>$uid])->field('nickname,name,uid,avatar')->find();
        return Json::successful($data);
    }
    /**成绩
     * @param int $record_id
     * @param int $test_id
     * @param int $uid
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserAchievement($record_id=0,$test_id=0,$uid=0)
    {
        if(!$record_id || !$test_id || !$uid) return Json::fail('参数错误');
        $dat=TestPaperModel::where('id',$test_id)->field('title,item_number,total_score')->find();
        $record=ExaminationRecord::where(['id'=>$record_id,'test_id'=>$test_id,'uid'=>$uid,'type'=>2])->find();
        $data['title']=$dat['title'];
        $data['item_number']=$dat['item_number'];
        $data['total_score']=$dat['total_score'];
        $data['yes_questions']=$record['yes_questions'];
        $data['score']=$record['score'];
        $data['start_time']=date('Y-m-d H:i',$record['start_time']);
        return Json::successful($data);
    }
    /**试卷中的试题答题情况
     * @param int $id
     * @param int $type
     */
    public function getTestPaperAnswers($test_id=0,$record_id=0,$question_type=1)
    {
        if(!$test_id || !$record_id) return Json::fail('参数错误');
        return Json::successful(TestPaperQuestions::getExaminationRecordAnswers($test_id,$record_id,$question_type));
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
        if ($test) {
            $model_type = 'paper';
        } else {
            $model_type = 'test';
        }
        $res = parent::getDataModification($model_type, $id, $field, $value);
        if ($res)
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**关联试题 手动组题
     * @param int $id
     */
    public function questions($question_type = 0, $id = 1)
    {
        $this->assign(['id' => $id, 'question_type' => $question_type, 'cateList' => QuestionsCategpry::taskCategoryAll(2)]);
        return $this->fetch('questions');
    }

    /**
     * 获取题库列表
     */
    public function getTestQuestionsList($question_type = '', $id = 0)
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['pid', 0],
            ['title', '']
        ]);
        $where['type'] = $question_type;
        $arrays = [];
        if ($id) {
            $arrays = TestPaperQuestions::where(['test_id' => $id])->column('questions_id');
        }
        $list = Questions::questionsList($where, $arrays);
        return Json::successlayui($list);
    }

    /**试题分类
     * @param int $id
     * @param int $type
     */
    public function cate_questions()
    {
        $list = QuestionsCategpry::taskCategoryAll(2);
        return Json::successful($list);
    }

    /**
     * 查看试卷
     */
    public function test_paper($id = 0, $type = 1)
    {
        $this->assign(['id' => $id, 'type' => $type]);
        return $this->fetch();
    }

    /**试卷中的试题
     * @param int $id
     * @param int $type
     */
    public function getTestPaperList($id = 0, $type = 1)
    {
        $where = parent::getMore([
            ['page', 1],
            ['limit', 20],
        ]);
        return Json::successlayui(TestPaperQuestions::getTestPaperList($where, $id, $type));
    }

    /**添加/编辑
     * @param int $id
     * @return mixed
     */
    public function add($id = 0, $type = 1)
    {
        $test = $id > 0 ? TestPaperModel::get($id) : [];
        $grade = [];
        if ($test && $id) {
            $single_tmp_list = TestPaperQuestions::gettestPaperQuestions($id, 1);
            $many_tmp_list = TestPaperQuestions::gettestPaperQuestions($id, 2);
            $judge_tmp_list = TestPaperQuestions::gettestPaperQuestions($id, 3);
            if ($type == 2) $grade = TestPaperScoreGrade::testPaperScoreGradeList($id);
        } else {
            $single_tmp_list = [];
            $many_tmp_list = [];
            $judge_tmp_list = [];
        }
        $this->assign([
            'id' => $id,
            'type' => $type,
            'test' => json_encode($test),
            'grade' => json_encode($grade),
            'single_tmp_list' => json_encode($single_tmp_list),
            'many_tmp_list' => json_encode($many_tmp_list),
            'judge_tmp_list' => json_encode($judge_tmp_list)
        ]);
        return $this->fetch();
    }

    /**
     * 获取试题分类
     */
    public function add_cate_list($type = 1)
    {
        $category = TestPaperCategory::taskCategoryAll(2, $type);
        return Json::successful($category);
    }

    /**添加/编辑试题
     * @param int $id
     */
    public function save_add($id = 0, $type = 1)
    {
        $data = parent::postMore([
            ['title', ''],
            ['image', ''],
            ['tid', 0],
            ['is_show', 1],
            ['item_number', 0],
            ['total_score', 0],
            ['single_number', 0],
            ['single_score', 0],
            ['many_number', 0],
            ['many_score', 0],
            ['judge_number', 0],
            ['judge_score', 0],
            ['single_sort', 0],
            ['many_sort', 0],
            ['judge_sort', 0],
            ['txamination_time', 0],
            ['fake_sales', 0],
            ['difficulty', 1],
            ['pay_type', 0],
            ['money', 0],
            ['member_pay_type', 0],
            ['member_money', 0],
            ['is_score', 0],
            ['is_group', 1],
            ['cate_id', 0],
            ['frequency', 1],
            ['singleIds', ''],
            ['manyIds', ''],
            ['judgeIds', ''],
            ['grade', ''],
            ['sort', 0]
        ]);
        if ($data['tid'] <= 0) return Json::fail('请选择试题分类');
        if (!$data['title']) return Json::fail('请输入试卷标题');
        if ($type == 2 && !$data['image']) return Json::fail('请添加试卷封面图');
        if ($data['single_number'] < 0 || $data['many_number'] < 0 || $data['judge_number'] < 0) return Json::fail('各类型题目数量不能小于0');
        $data['item_number'] = bcadd($data['single_number'], bcadd($data['many_number'], $data['judge_number'], 0), 0);
        $total_single_score = bcmul($data['single_number'], $data['single_score'], 0);
        $total_many_score = bcmul($data['many_number'], $data['many_score'], 0);
        $total_judge_score = bcmul($data['judge_number'], $data['judge_score'], 0);
        $data['total_score'] = bcadd($total_single_score, bcadd($total_many_score, $total_judge_score, 0), 0);
        if ($data['item_number'] > 100) return Json::fail('试卷最多100道');
        if ($type == 1) {
            unset(
                $data['txamination_time'],
                $data['image'],
                $data['pay_type'],
                $data['money'],
                $data['member_pay_type'],
                $data['member_money']
            );
        }
        $singleIds = json_decode($data['singleIds']);
        $manyIds = json_decode($data['manyIds']);
        $judgeIds = json_decode($data['judgeIds']);
        $grade = json_decode($data['grade'], true);
        TestPaperModel::beginTrans();
        if ($id) {
            $res = TestPaperModel::edit($data, $id);
            $res1 = true;
            if ($type == 2) {
                $res1 = TestPaperScoreGrade::testPaperScoreGradeAdd($id, $grade);
            }
            TestPaperQuestions::where('test_id', $id)->delete();
            if ($data['is_group'] == 1) {
                $res2 = TestPaperQuestions::addTestPaperQuestions($id, $type, (int)$data['single_number'], $singleIds, $data['single_score']);
                $res3 = TestPaperQuestions::addTestPaperQuestions($id, $type, (int)$data['many_number'], $manyIds, $data['many_score']);
                $res4 = TestPaperQuestions::addTestPaperQuestions($id, $type, (int)$data['judge_number'], $judgeIds, $data['judge_score']);
            } else {
                $res2 = TestPaperQuestions::addRandomGroupQuestions($id, $type, 1, $data['cate_id'], (int)$data['single_number'], $data['single_score']);
                $res3 = TestPaperQuestions::addRandomGroupQuestions($id, $type, 2, $data['cate_id'], (int)$data['many_number'], $data['many_score']);
                $res4 = TestPaperQuestions::addRandomGroupQuestions($id, $type, 3, $data['cate_id'], (int)$data['judge_number'], $data['judge_score']);
            }
            $res5 = $this->inspectTestQuestions($id);
        } else {
            $data['type'] = $type;
            $data['add_time'] = time();
            if (TestPaperModel::be(['title' => $data['title'], 'is_del' => 0])) {
                return Json::fail('标题已存在！');
            }
            $res = TestPaperModel::insertGetId($data);
            $res1 = true;
            if ($type == 2) {
                $res1 = TestPaperScoreGrade::testPaperScoreGradeAdd($res, $grade);
            }
            if ($data['is_group'] == 1) {
                $res2 = TestPaperQuestions::addTestPaperQuestions($res, $type, (int)$data['single_number'], $singleIds, $data['single_score']);
                $res3 = TestPaperQuestions::addTestPaperQuestions($res, $type, (int)$data['many_number'], $manyIds, $data['many_score']);
                $res4 = TestPaperQuestions::addTestPaperQuestions($res, $type, (int)$data['judge_number'], $judgeIds, $data['judge_score']);
            } else {
                $res2 = TestPaperQuestions::addRandomGroupQuestions($res, $type, 1, $data['cate_id'], (int)$data['single_number'], $data['single_score']);
                $res3 = TestPaperQuestions::addRandomGroupQuestions($res, $type, 2, $data['cate_id'], (int)$data['many_number'], $data['many_score']);
                $res4 = TestPaperQuestions::addRandomGroupQuestions($res, $type, 3, $data['cate_id'], (int)$data['judge_number'], $data['judge_score']);
            }
            $res5 = $this->inspectTestQuestions($res);
        }
        if ($res && $res1 && $res2 && $res3 && $res4 && $res5) {
            TestPaperModel::commitTrans();
            return Json::successful('添加/编辑成功');
        } else {
            TestPaperModel::rollbackTrans();
            return Json::fail('添加/编辑失败');
        }
    }

    /**
     * 检查试卷试题数量
     */
    public function inspectTestQuestions($id)
    {
        if (!$id) return Json::fail('参数错误');
        $test = TestPaperModel::get($id);
        if (!$test) return Json::fail('试卷不存在');
        $single_number = TestPaperQuestions::testPaperQuestionsNumber($id, 1);
        $many_number = TestPaperQuestions::testPaperQuestionsNumber($id, 2);
        $judge_number = TestPaperQuestions::testPaperQuestionsNumber($id, 3);
        if ($single_number < $test['single_number'] || $many_number < $test['many_number'] || $judge_number < $test['judge_number']) {
            $total = bcadd($single_number, bcadd($many_number, $judge_number, 0), 0);
            $total_single_score = bcmul($single_number, $test['single_score'], 0);
            $total_many_score = bcmul($many_number, $test['many_score'], 0);
            $total_judge_score = bcmul($judge_number, $test['judge_score'], 0);
            $total_score = bcadd($total_single_score, bcadd($total_many_score, $total_judge_score, 0), 0);
            $data['single_number'] = $single_number;
            $data['many_number'] = $many_number;
            $data['judge_number'] = $judge_number;
            $data['item_number'] = $total;
            $data['total_score'] = $total_score;
            return TestPaperModel::edit($data, $id);
        } else {
            return true;
        }
    }

    /**删除试卷
     * @param int $id
     */
    public function delete($id = 0)
    {
        if (!$id) return Json::fail('参数错误');
        $test = TestPaperModel::get($id);
        if (!$test) return Json::fail('要删除的试卷不存在');
        $res = parent::getDataModification('test', $id, 'is_del', 1);
        if ($res) {
            TestPaperQuestions::where('test_id', $id)->delete();
            return Json::successful('删除成功');
        } else {
            return Json::fail('删除失败');
        }
    }

    /**删除试题
     * @param int $id
     */
    public function TestPaperDelete($id = 0)
    {
        if (!$id) return Json::fail('参数错误');
        $paperQuestion = TestPaperQuestions::where('id', $id)->find();
        if (!$paperQuestion) return Json::fail('要删除的试题不存在');
        TestPaperQuestions::beginTrans();
        $res = TestPaperQuestions::where('id', $id)->delete();
        if ($res) {
            $res1 = $this->inspectTestQuestions($paperQuestion['test_id']);
            TestPaperQuestions::checkTrans($res1);
            if ($res1) {
                return Json::successful('删除成功');
            } else {
                return Json::fail('删除失败');
            }
        } else {
            return Json::fail('删除失败');
        }
    }

    /**关联证书
     * @param int $id
     */
    public function certificate($related_id = 0)
    {
        if (!$related_id) return Json::fail('参数错误');
        $certificate = CertificateRelated::where(['related' => $related_id, 'obtain' => 2])->find();
        if ($certificate) {
            $id = $certificate['id'];
        } else {
            $id = 0;
            $certificate = [];
        }
        $this->assign(['related_id' => $related_id, 'id' => $id, 'certificate' => json_encode($certificate)]);
        return $this->fetch();
    }

    /**获取对应证书
     * @param int $obtain
     */
    public function certificateLists($obtain = 1)
    {
        $list = Certificate::where(['is_del' => 0, 'obtain' => $obtain])->order('sort desc,add_time desc')->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        return Json::successful($list);
    }

    /**试卷关联证书
     * @param int $id
     * @param int $obtain
     */
    public function certificateRecord($id = 0, $obtain = 1)
    {
        $data = parent::postMore([
            ['cid', 0],
            ['condition', ''],
            ['related', 0],
            ['is_show', 0]
        ]);
        $data['obtain'] = $obtain;
        $res = CertificateRelated::addCertificateRelated($data, $id);
        if ($res) {
            return Json::successful('关联成功');
        } else {
            return Json::fail('关联失败');
        }
    }

    /**
     * 添加推荐
     * @param int $product_id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function recommend($test_id = 0)
    {
        if (!$test_id) $this->failed('缺少参数');
        $testPaper = TestPaperModel::get($test_id);
        if (!$testPaper) $this->failed('没有查到此试卷');
        if ($testPaper->is_del) $this->failed('此试卷已删除');
        $type = $testPaper->type;
        $form = Form::create(Url::build('save_recommend', ['test_id' => $test_id]), [
            Form::select('recommend_id', '推荐')->setOptions(function () use ($type) {
                $types = $type == 1 ? 11 : 12;
                $list = Recommend::where(['is_show' => 1, 'type' => $types])->where('is_fixed', 0)->field('title,id')->order('sort desc,add_time desc')->select();
                $menus = [];
                foreach ($list as $menu) {
                    $menus[] = ['value' => $menu['id'], 'label' => $menu['title']];
                }
                return $menus;
            })->filterable(1),
            Form::number('sort', '排序')->min(0)
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
    public function save_recommend($test_id = 0)
    {
        if (!$test_id) $this->failed('缺少参数');
        $data = parent::postMore([
            ['recommend_id', 0],
            ['sort', 0],
        ]);
        if (!$data['recommend_id']) return Json::fail('请选择推荐');
        $recommend = Recommend::get($data['recommend_id']);
        if (!$recommend) return Json::fail('导航菜单不存在');
        $data['add_time'] = time();
        $data['type'] = $recommend->type;
        $data['link_id'] = $test_id;
        if (RecommendRelation::be(['type' => $recommend->type, 'link_id' => $test_id, 'recommend_id' => $data['recommend_id']])) return Json::fail('已推荐,请勿重复推荐');
        if (RecommendRelation::set($data))
            return Json::successful('推荐成功');
        else
            return Json::fail('推荐失败');
    }

    /**取消推荐
     * @param int $id
     */
    public function cancel_recommendation($id = 0, $test_id = 0)
    {
        if (!$id || !$test_id) $this->failed('缺少参数');
        if (RecommendRelation::be(['id' => $id, 'link_id' => $test_id])) {
            $res = RecommendRelation::where(['id' => $id, 'link_id' => $test_id])->delete();
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
    public function web_recommend($test_id = 0)
    {
        if (!$test_id) $this->failed('缺少参数');
        $testPaper = TestPaperModel::get($test_id);
        if (!$testPaper) $this->failed('没有查到此试卷');
        if ($testPaper->is_del) $this->failed('此试卷已删除');
        $type = $testPaper->type;
        $form = Form::create(Url::build('save_web_recommend', ['test_id' => $test_id]), [
            Form::select('recommend_id', '推荐')->setOptions(function () use ($type) {
                $types = $type == 1 ? 7 : 8;
                $model = WebRecommend::where(['is_show' => 1, 'type' => $types]);
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
    public function save_web_recommend($test_id = 0)
    {
        if (!$test_id) $this->failed('缺少参数');
        $data = parent::postMore([
            ['recommend_id', 0],
            ['sort', 0],
        ]);
        if (!$data['recommend_id']) return Json::fail('请选择推荐');
        $recommend = WebRecommend::get($data['recommend_id']);
        if (!$recommend) return Json::fail('导航菜单不存在');
        $data['add_time'] = time();
        $data['type'] = $recommend->type;
        $data['link_id'] = $test_id;
        if (WebRecommendRelation::be(['type' => $recommend->type, 'link_id' => $test_id, 'recommend_id' => $data['recommend_id']])) return Json::fail('已推荐,请勿重复推荐');
        if (WebRecommendRelation::set($data))
            return Json::successful('推荐成功');
        else
            return Json::fail('推荐失败');
    }

    /**取消推荐
     * @param int $id
     */
    public function cancel_web_recommendation($id = 0, $test_id = 0)
    {
        if (!$id || !$test_id) return Json::fail('缺少参数');
        if (WebRecommendRelation::be(['id' => $id, 'link_id' => $test_id])) {
            $res = WebRecommendRelation::where(['id' => $id, 'link_id' => $test_id])->delete();
            if ($res)
                return Json::successful('取消推荐成功');
            else
                return Json::fail('取消推荐失败');
        } else {
            return Json::fail('推荐不存在');
        }
    }
}
