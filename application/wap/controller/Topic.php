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

use service\GroupDataService;
use service\SystemConfigService;
use think\Cache;
use think\Request;
use think\Url;
use service\JsonService;
use service\UtilService;
use app\wap\model\topic\TestPaperCategory;
use app\wap\model\topic\TestPaper;
use app\wap\model\topic\TestPaperQuestions;
use app\wap\model\special\Special as SpecialModel;
use app\wap\model\topic\Questions;
use app\wap\model\topic\ExaminationRecord;
use app\wap\model\topic\ExaminationTestRecord;
use app\wap\model\topic\ExaminationWrongBank;
use app\wap\model\topic\TestPaperObtain;
use app\wap\model\topic\CertificateRecord;
use app\wap\model\topic\CertificateRelated;
use app\wap\model\topic\Relation;
use app\wap\model\merchant\Merchant as MerchantModel;

/**题库控制器
 * Class Topic
 * @package app\wap\controller
 */
class Topic extends AuthController
{
    /**
     * 白名单
     * */
    public static function WhiteList()
    {
        return [
            'question_category',
            'testPaperCate',
            'practiceList',
            'problem_index',
            'specialTestPaper',
            'testPaperDetails',
            'situationRecord',
            'userAnswer',
            'takeTheTestAgain',
            'continueAnswer'
        ];
    }

    /**练习详情
     * @return mixed
     */
    public function problem_index($id = 0)
    {
        if (!$id) $this->failed('缺少参数,无法访问', Url::build('index/index'));
        $title = TestPaper::PreExercisesWhere()->where('id', $id)->value('title');
        $this->assign(['uid' => $this->uid, 'titles' => $title, 'id' => $id]);
        return $this->fetch();
    }

    /**练习答题
     * @return mixed
     */
    public function problem_detail($test_id)
    {
        if (!$test_id) $this->failed('缺少参数,无法访问', Url::build('index/index'));
        $title = TestPaper::PreExercisesWhere()->where('id', $test_id)->value('title');
        $isPay = (!$this->uid || $this->uid == 0) ? false : TestPaperObtain::PayTestPaper($test_id, $this->uid, 1);
        if (!$isPay) $this->failed('您暂未获得练习试题', Url::build('topic/problem_index', ['id' => $test_id]));
        $this->assign(['uid' => $this->uid, 'titles' => $title]);
        return $this->fetch();
    }

    /**练习答题卡
     * @return mixed
     */
    public function problem_sheet()
    {
        return $this->fetch();
    }

    /**考试答题
     * @return mixed
     */
    public function question_detail($test_id)
    {
        if (!$test_id) $this->failed('缺少参数,无法访问', Url::build('index/index'));
        $test = TestPaper::PreExercisesWhere()->where('id', $test_id)->field('title,txamination_time')->find();
        $isPay = (!$this->uid || $this->uid == 0) ? false : TestPaperObtain::PayTestPaper($test_id, $this->uid, 2);
        if (!$isPay) $this->failed('您还未购买考试试卷', Url::build('special/question_index', ['id' => $test_id]));
        $this->assign(['uid' => $this->uid, 'titles' => $test['title'], 'txamination_time' => $test['txamination_time']]);
        return $this->fetch();
    }

    /**考试答题卡
     * @return mixed
     */
    public function question_sheet()
    {
        return $this->fetch();
    }

    /**考试评测
     * @return mixed
     */
    public function question_user()
    {
        return $this->fetch();
    }

    /**我的错题
     * @return mixed
     */
    public function question_wrong()
    {
        return $this->fetch();
    }

    /**
     * 练习、考试 列表
     */
    public function question_category($type = 1)
    {
        $this->assign([
            'homeLogo' => SystemConfigService::get('home_logo'),
            'type' => $type
        ]);
        return $this->fetch();
    }

    /**
     * 我的证书
     */
    public function certificate_list()
    {
        return $this->fetch();
    }

    /**
     * 荣誉证书
     */
    public function certificate_detail()
    {
        return $this->fetch();
    }

    /**
     * 考试结果
     */
    public function question_result()
    {
        return $this->fetch();
    }

    /**
     * 练习结果
     */
    public function problem_result()
    {
        return $this->fetch();
    }

    /**
     * 错题详情
     */
    public function question_detail_wrong()
    {
        return $this->fetch();
    }

    /**
     * 试卷分类
     * @param int $type 1=练习 2=考试
     */
    public function testPaperCate($type = 1)
    {
        $cateogry = TestPaperCategory::with('children')->where(['is_show' => 1, 'is_del' => 0, 'type' => $type])->order('sort desc,id desc')->where('pid', 0)->select();
        return JsonService::successful($cateogry->toArray());
    }

    /**
     * 试卷列表
     */
    public function practiceList($type = 1)
    {
        list($page, $limit, $pid, $tid, $search) = UtilService::PostMore([
            ['page', 1],
            ['limit', 10],
            ['pid', 0],
            ['tid', 0],
            ['search', ''],
        ], $this->request, true);
        return JsonService::successful(TestPaper::getTestPaperExercisesList($type, $page, $limit, $pid, $tid, $search));
    }

    /**试卷详情
     * @param int $id
     */
    public function testPaperDetails($id = 0)
    {
        if (!$id) return JsonService::fail('缺少参数,无法访问');
        $testPaper = TestPaper::PreExercisesWhere()->where('id', $id)->find();
        if (!$testPaper) return JsonService::fail('试卷不存在');
        $isPay = 0;
        $surplus = -1; //剩余考试次数 -1 无限次 0 无法答题
        if ($this->uid) {
            if ($testPaper['type'] == 1) {
                $isPay = 1;
                if (!TestPaperObtain::PayTestPaper($id, $this->uid, 1)) {
                    TestPaperObtain::setUserTestPaper('', $id, $this->uid, 1, 2);
                }
            } else if ($testPaper['type'] == 2) {
                if (in_array($testPaper['money'], [0, 0.00]) || in_array($testPaper['pay_type'], [PAY_NO_MONEY])) {
                    $res = true;
                    if (!TestPaperObtain::PayTestPaper($id, $this->uid, 2)) {
                        $res = TestPaperObtain::setUserTestPaper('', $id, $this->uid, 2, 2);
                    }
                    if ($res) $isPay = 1;
                } else {
                    $business = isset($this->userInfo['business']) ? $this->userInfo['business'] : 0;
                    $isPay = TestPaperObtain::PayTestPaper($id, $this->uid, 2);
                    if (!$isPay && $business) {
                        $mer_id = MerchantModel::getMerId($this->uid);
                        $res = false;
                        if ($mer_id == $testPaper['mer_id']) {
                            $res = TestPaperObtain::setUserTestPaper('', $id, $this->uid, 2, 2);
                        }
                        if ($res) $isPay = 1;
                    }
                }
                $surplus = $testPaper['frequency'] == 0 ? -1 : $testPaper['frequency'];
                if ($isPay && $testPaper['frequency'] > 0) {
                    $number = TestPaperObtain::where(['uid' => $this->uid, 'test_id' => $id, 'type' => 2, 'is_del' => 0])->value('number');
                    $surplus = bcsub($testPaper['frequency'], $number, 0);
                }
            }
        }
        $testPaper['surplus'] = $surplus;
        $testPaper['isPay'] = $isPay;
        return JsonService::successful($testPaper);
    }

    /**检查试卷答题情况
     * @param $id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function situationRecord($id)
    {
        if (!$id) return JsonService::fail('缺少参数,无法访问');
        $testPaper = TestPaper::PreExercisesWhere()->where('id', $id)->find();
        if (!$testPaper) return JsonService::fail('试卷不存在');
        $record = 0;
        if (!$this->uid) return JsonService::successful('ok', $record);
        $count = ExaminationRecord::where(['test_id' => $testPaper['id'], 'type' => $testPaper['type'], 'uid' => $this->uid])->count();
        if ($count) {
            $userRecord = ExaminationRecord::where(['test_id' => $testPaper['id'], 'type' => $testPaper['type'], 'uid' => $this->uid, 'is_submit' => 0])->order('id desc')->find();
            if ($userRecord) {
                $record = 2;//继续答题
                if ($testPaper['type'] == 2) {
                    if (bcsub($userRecord['end_time'], time(), 0) <= 0) {
                        $data = [
                            'examination_id' => $userRecord['id'],
                            'type' => $testPaper['type'],
                            'duration' => $testPaper['txamination_time'],
                            'score' => 0
                        ];
                        $res = ExaminationRecord::submitExaminationRecord($data, $this->uid);
                        if (!$res) {
                            ExaminationRecord::clearLastExamResults($id, $testPaper['type'], $this->uid);
                        }
                        $record = 1;
                    }
                }
            } else {
                $record = 1;//再次答题
            }
        } else {
            $record = 0; //没有答过题
        }
        return JsonService::successful('ok', $record);
    }

    /**用户开始答题
     * @param int $id
     */
    public function userAnswer($test_id = 0, $type = 1)
    {
        if (!$test_id) return JsonService::fail('缺少参数,无法访问');
        $testPaper = TestPaper::PreExercisesWhere()->where('id', $test_id)->find();
        if (!$testPaper) return JsonService::fail('试卷不存在');
        $uid = $this->uid;
        if ($type == 2 && $uid && $testPaper['type'] == 2) {
            $number = TestPaperObtain::where(['uid' => $uid, 'test_id' => $test_id, 'type' => 2, 'is_del' => 0])->value('number');
            if ($number >= $testPaper['frequency'] && $testPaper['frequency'] > 0) return JsonService::fail('您的考试次数已用完');
        }
        $examination_id = ExaminationRecord::addExaminationRecord($test_id, $type, $uid, $testPaper['txamination_time']);
        if ($examination_id) {
            return JsonService::successful('ok', $examination_id);
        } else {
            return JsonService::fail('开始答题失败');
        }
    }

    /**再考一次
     * @param int $test_id
     * @param int $type
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function takeTheTestAgain($test_id = 0, $type = 1)
    {
        if (!$test_id) return JsonService::fail('缺少参数,无法访问');
        $testPaper = TestPaper::PreExercisesWhere()->where('id', $test_id)->find();
        if (!$testPaper) return JsonService::fail('试卷不存在');
        $uid = $this->uid;
        if ($type == 2 && $uid && $testPaper['type'] == 2) {
            $number = TestPaperObtain::where(['uid' => $uid, 'test_id' => $test_id, 'type' => 2, 'is_del' => 0])->value('number');
            if ($number >= $testPaper['frequency'] && $testPaper['frequency'] > 0) return JsonService::fail('您的考试次数已用完');
        }
        $examination_id = ExaminationRecord::addExaminationRecord($test_id, $type, $uid, $testPaper['txamination_time']);
        if ($examination_id) {
            $res = ExaminationRecord::clearLastExamResults($test_id, $type, $uid);
            if (!$res) {
                return JsonService::fail('再次答题失败');
            } else {
                return JsonService::successful('ok', $examination_id);
            }
        } else {
            return JsonService::fail('再次答题失败');
        }
    }

    /**继续答题
     * @param int $test_id
     * @param int $type
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function continueAnswer($test_id = 0, $type = 1)
    {
        if (!$test_id) return JsonService::fail('缺少参数,无法访问');
        $testPaper = TestPaper::PreExercisesWhere()->where('id', $test_id)->find();
        if (!$testPaper) return JsonService::fail('试卷不存在');
        $record = ExaminationRecord::where(['test_id' => $test_id, 'type' => $type, 'uid' => $this->uid, 'is_submit' => 0])->order('id desc')->find();
        if ($record) {
            return JsonService::successful('ok', $record['id']);
        } else {
            return JsonService::fail('继续答题失败');
        }
    }

    /**获取试卷中的试题
     * @param $test_id 试卷ID
     * @param $type
     * @param $record_id 继续答题传入
     * @param is_correct 0=未答 1=答错 2=正确
     */
    public function testPaperQuestions($test_id, $type, $record_id = 0)
    {
        if (!$test_id) return JsonService::fail('缺少参数,无法访问');
        $testPaper = TestPaper::PreExercisesWhere()->where('id', $test_id)->find();
        if (!$testPaper) return JsonService::fail('试卷不存在');
        $arr = ['1' => $testPaper['single_sort'], '2' => $testPaper['many_sort'], '3' => $testPaper['judge_sort']];
        arsort($arr);
        $question = [];
        foreach ($arr as $key => $item) {
            $single = TestPaperQuestions::getQuestionslist($test_id, $type, $key);
            $question = array_merge($question, $single);
        }
        if (count($question) <= 0) return JsonService::fail('试卷中试题为空');
        if ($record_id) {
            $record = ExaminationRecord::where(['id' => $record_id, 'test_id' => $test_id, 'type' => $type, 'uid' => $this->uid])->order('id desc')->find();
        } else {
            $record = false;
        }
        foreach ($question as $key => &$value) {
            if ($record) {
                $value['userAnswer'] = ExaminationTestRecord::checkWhetherAnswerQuestions($record['id'], $type, $this->uid, $value['questions_id']);
            } else {
                $value['userAnswer'] = [];
            }
            $value['option'] = json_decode($value['option']);
            $value['special'] = Relation::getRelationSpecial(3, $value['questions_id']);
        }
        return JsonService::successful($question);
    }

    /**答题卡
     * @param $test_id 试卷ID
     * @param $type
     * @param int $record_id 答题记录ID
     */
    public function answerSheet($test_id, $type, $record_id = 0)
    {
        if (!$test_id || !$record_id) return JsonService::fail('缺少参数,无法访问');
        $testPaper = TestPaper::PreExercisesWhere()->where('id', $test_id)->find();
        if (!$testPaper) return JsonService::fail('试卷不存在');
        $arr = ['1' => $testPaper['single_sort'], '2' => $testPaper['many_sort'], '3' => $testPaper['judge_sort']];
        arsort($arr);
        $testPaperQuestion = [];
        foreach ($arr as $key => $item) {
            $single = TestPaperQuestions::getQuestionslist($test_id, $type, $key);
            $testPaperQuestion = array_merge($testPaperQuestion, $single);
        }
        $record = ExaminationRecord::where(['id' => $record_id, 'test_id' => $test_id, 'type' => $type, 'uid' => $this->uid])->order('id desc')->find();
        if (!$record) return JsonService::fail('考试记录不存在');
        foreach ($testPaperQuestion as $key => &$value) {
            $value['userAnswer'] = ExaminationTestRecord::checkWhetherAnswerQuestions($record['id'], $type, $this->uid, $value['questions_id']);
        }
        return JsonService::successful($testPaperQuestion);
    }

    /**考试结果
     * @param $test_id
     * @param $type
     */
    public function examinationResults($test_id, $type)
    {
        if (!$test_id) return JsonService::fail('缺少参数,无法访问');
        $testPaper = TestPaper::PreExercisesWhere()->where('id', $test_id)->find();
        if (!$testPaper) return JsonService::fail('试卷不存在');
        $arr = ['1' => $testPaper['single_sort'], '2' => $testPaper['many_sort'], '3' => $testPaper['judge_sort']];
        arsort($arr);
        $testPaperQuestion = [];
        foreach ($arr as $key => $item) {
            $single = TestPaperQuestions::getQuestionslist($test_id, $type, $key);
            $testPaperQuestion = array_merge($testPaperQuestion, $single);
        }
        $record = ExaminationRecord::where(['test_id' => $test_id, 'type' => $type, 'uid' => $this->uid, 'is_submit' => 1])->order('id desc')->find();
        if (!$record) return JsonService::fail('考试记录不存在');
        foreach ($testPaperQuestion as $key => &$value) {
            $value['userAnswer'] = ExaminationTestRecord::checkWhetherAnswerQuestions($record['id'], $type, $this->uid, $value['questions_id']);
        }
        $record['title'] = $testPaper['title'];
        $record['is_score'] = $testPaper['is_score'];
        $record['test_paper_question'] = $testPaperQuestion;
        $record['not_questions'] = bcsub($testPaper['item_number'], bcadd($record['yes_questions'], $record['wrong_question'], 0), 0);
        return JsonService::successful($record);
    }

    /**保存试题答题结果
     * @param int $id
     */
    public function submitQuestions()
    {
        $data = UtilService::PostMore([
            ['e_id', 0],
            ['type', 1],
            ['questions_id', 0],
            ['user_answer', ''],
            ['answer', ''],
            ['is_correct', 0],
            ['score', 0]
        ], $this->request);
        if ($data['e_id'] <= 0) return JsonService::fail('参数错误');
        $res = ExaminationTestRecord::addExaminationTestRecord($data, $this->uid);
        if ($res) return JsonService::successful('提交成功');
        else return JsonService::fail('提交失败');
    }

    /**
     * 提交试卷
     */
    public function submitTestPaper()
    {
        $data = UtilService::PostMore([
            ['examination_id', 0],
            ['type', 1],
            ['duration', ''],
            ['score', '']
        ], $this->request);
        $res = ExaminationRecord::submitExaminationRecord($data, $this->uid);
        if ($res) return JsonService::successful('提交成功');
        else return JsonService::fail(ExaminationRecord::getErrorInfo('提交失败'));
    }

    /**检测是否提交
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function is_submit()
    {
        $data = UtilService::PostMore([
            ['examination_id', 0],
            ['type', 1],
        ], $this->request);
        $record = ExaminationRecord::where(['id' => $data['examination_id'], 'type' => $data['type'], 'uid' => $this->uid])->find();
        if(!$record) return JsonService::fail('记录不存在');
        return JsonService::successful($record['is_submit']);
    }

    /**专题下关联的练习、考试 post提交
     * @param $special_id
     */
    public function specialTestPaper()
    {
        list($special_id, $type) = UtilService::PostMore([
            ['special_id', 0],
            ['type', 1],
        ], $this->request, true);
        if (!$special_id) return JsonService::fail('缺少参数,无法访问');
        $data = [];
        switch ($type) {
            case 1:
                $relationship = 1;
                break;
            case 2:
                $relationship = 2;
                break;
        }
        $test_ids = Relation::setWhere($relationship, $special_id)->column('relation_id');
        if (count($test_ids)) {
            $data = TestPaper::PreExercisesWhere()->where('type', $type)->where('id', 'in', $test_ids)->order('sort desc,id desc')->select();
            $data = count($data) > 0 ? $data->toArray() : [];
            foreach ($data as $key => &$value) {
                if ($type == 1) {
                    $record = ExaminationRecord::where(['test_id' => $value['id'], 'uid' => $this->uid, 'type' => 1])->order('id desc')->find();
                    if (!$record) $value['done'] = 0;
                    else $value['done'] = ExaminationTestRecord::where(['e_id' => $record['id'], 'uid' => $this->uid, 'type' => 1])->count();
                } else if ($type == 2) {
                    $value['is_pay'] = (!$this->uid || $this->uid == 0) ? false : TestPaperObtain::PayTestPaper($value['id'], $this->uid, 2);
                }
            }
        }
        return JsonService::successful($data);
    }

    /**
     * 我的错题库列表
     */
    public function userWrongBank()
    {
        list($page, $limit, $is_master) = UtilService::PostMore([
            ['page', 1],
            ['limit', 10],
            ['is_master', ''],
        ], $this->request, true);
        $list = ExaminationWrongBank::userWrongBankList($this->uid, $page, $limit, $is_master);
        return JsonService::successful($list);
    }

    /**
     * 我的错题库id列表
     */
    public function userWrongBankIdArr()
    {
        list($id, $is_master, $order) = UtilService::PostMore([
            ['id', 0],
            ['is_master', ''],
            ['order', 1],
        ], $this->request, true);
        $list = ExaminationWrongBank::getUserWrongBankIDList($this->uid, $is_master, $id, $order);
        return JsonService::successful($list);
    }

    /**删除错题
     * @param $id
     */
    public function delWrongBank($id)
    {
        if (!$id) return JsonService::fail('缺少参数');
        $res = ExaminationWrongBank::delUserWrongBank($id);
        return JsonService::successful($res);
    }

    /**获取错题库的试题 未使用
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function lookTopicWrongBank()
    {
        list($is_master) = UtilService::PostMore([
            ['is_master', 0],
        ], $this->request, true);
        $list = ExaminationWrongBank::getUserWrongBankListAll($this->uid, $is_master);
        return JsonService::successful($list);
    }

    /**获取单个错题
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function oneWrongBank($id)
    {
        if (!$id) return JsonService::fail('缺少参数,无法访问');
        $wrong = ExaminationWrongBank::getUserWrongBankListOne($this->uid, $id);
        $wrong['special'] = Relation::getRelationSpecial(3, $wrong['questions_id']);
        return JsonService::successful($wrong);
    }

    /**
     * 错题库未掌握试题提交
     */
    public function submitWrongBank()
    {
        $data = UtilService::PostMore([
            ['wrong_id', 0],
            ['questions_id', 0],
            ['is_master', 0]
        ], $this->request);
        if ((int)$data['wrong_id'] <= 0 || (int)$data['questions_id'] <= 0) return JsonService::fail('参数错误');
        $res = ExaminationWrongBank::userSubmitWrongBank($data, $this->uid);
        if ($res) return JsonService::successful('ok');
        else return JsonService::fail('err');
    }

    /**
     * 考试检测是否达到领取标准
     * $test_id 试卷ID
     */
    public function inspect($test_id = 0)
    {
        $res = CertificateRelated::getCertificateRelated($test_id, 0, 2, $this->uid);
        if ($res) {
            return JsonService::successful('ok');
        } else {
            return JsonService::fail('err');
        }
    }

    /**用户领取证书
     * $test_id 试卷ID
     */
    public function getTheCertificate($test_id)
    {
        $res = CertificateRecord::getUserTheCertificate($test_id, 2, $this->uid);
        if ($res) {
            return JsonService::successful($res);
        } else {
            return JsonService::fail('领取失败');
        }
    }

    /**
     * 我的试卷
     * @param int $type 1=练习 2=考试
     */
    public function myTestPaper()
    {
        list($page, $limit, $type) = UtilService::PostMore([
            ['page', 1],
            ['limit', 10],
            ['type', 1]
        ], $this->request, true);
        $list = TestPaperObtain::getUserTestPaper($type, $this->uid, $page, $limit);
        return JsonService::successful($list);
    }

    /**
     * 我的证书
     */
    public function getUserCertificate()
    {
        list($page, $limit) = UtilService::PostMore([
            ['page', 1],
            ['limit', 10]
        ], $this->request, true);
        $list = CertificateRecord::getUserCertificate($this->uid, $page, $limit);
        return JsonService::successful($list);
    }

    /**查看证书
     * @param $id
     * @param $obtain
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function viewCertificate($id, $obtain)
    {
        $data = CertificateRecord::getCertificate($id, $obtain, $this->uid);
        if ($data) {
            return JsonService::successful($data);
        } else {
            return JsonService::fail('关联证书已被移除');
        }
    }
}
