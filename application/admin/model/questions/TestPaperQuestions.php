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

namespace app\admin\model\questions;

use traits\ModelTrait;
use basic\ModelBasic;
use service\UtilService as Util;
use app\admin\model\questions\Questions as QuestionsModel;
use app\admin\model\questions\QuestionsCategpry;

/**
 * 试卷试题 Model
 * Class TestPaperQuestions
 * @package app\admin\model\questions
 */
class TestPaperQuestions extends ModelBasic
{
    use ModelTrait;

    /**手动组题
     * @param $id
     * @param $number
     * @param $data
     */
    public static function addTestPaperQuestions($id, $type, $number, $data, $score)
    {
        if (!count($data) && $number <= 0) return true;
        foreach ($data as $key => $value) {
            $item['type'] = $type;
            $item['question_type'] = $value->question_type;
            $item['test_id'] = $id;
            $item['questions_id'] = $value->id;
            if (self::be($item)) continue;
            $item['sort'] = $value->sort;
            $number = $number - 1;
            $item['score'] = $score;
            if ($number < 0) continue;
            self::set($item);
        }
        return true;
    }

    /**试题列表
     * @param $id
     */
    public static function getTestPaperList($where, $id, $type)
    {
        $data = self::where(['type' => $type, 'test_id' => $id])->order('sort desc,id desc')->page($where['page'], $where['limit'])->select();
        foreach ($data as $key => &$item) {
            $item['title'] = QuestionsModel::where('id', $item['questions_id'])->value('stem');
            switch ($item['question_type']) {
                case 1:
                    $item['types'] = '单选题';
                    break;
                case 2:
                    $item['types'] = '多选题';
                    break;
                case 3:
                    $item['types'] = '判断题';
                    break;
            }
        }
        $count = self::where(['type' => $type, 'test_id' => $id])->count();
        return compact('data', 'count');
    }

    /**条件处理
     * @param $test_id
     * @param $record_id
     * @param $question_type
     * @return TestPaperQuestions
     */
    public static function setRecordWhere($test_id,$record_id,$question_type)
    {
        $model =self::alias('tq')->where('tq.test_id',$test_id)->where('q.question_type',$question_type)
            ->join('ExaminationTestRecord e','e.questions_id=tq.questions_id and e.e_id='.$record_id,'LEFT')
            ->join('Questions q','q.id=tq.questions_id')
            ->join('TestPaper t','t.id=tq.test_id')
            ->field('t.single_number,t.single_score,t.many_number,t.many_score,t.judge_number,t.judge_score,q.stem,q.image,q.is_img,q.option,q.answer,q.difficulty,q.question_type,e.user_answer,e.is_correct,e.score');
        return $model;
    }

    /**用户答题结果
     * @param $where
     */
    public static function getExaminationRecordAnswers($test_id,$record_id,$question_type)
    {
        $model=self::setRecordWhere($test_id,$record_id,$question_type);
        $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        foreach ($data as $key=>&$item){
            if($item['is_correct']!=2)  $item['score']=0;
        }
        return $data;
    }

    /** 随机组题
     * @param int $id
     * @param int $type 1=练习 2=考试
     * @param $questions 试卷
     * @param int $cate_id 试题分类ID
     */
    public static function addRandomGroupQuestions($id, $type, $question_type, $cate_id, $number, $score)
    {
        if (!$cate_id) return false;
        $cateIds = QuestionsCategpry::categoryId($cate_id);
        $data = QuestionsModel::getCateIds($question_type, $cateIds);
        $res = self::setTestPaper($id, $type, $number, $data, $score);
        if ($res) return true;
        else return false;
    }

    public static function setTestPaper($id, $type, $number, $data, $score)
    {
        if (!count($data) || $number <= 0) return true;
        if (count($data) < $number) $number = count($data);
        if (count($data) >= $number && $number == 1) {
            $ids = ['0' => 0];
        } else {
            $ids = array_rand($data, $number);
        }
        foreach ($ids as $key => $value) {
            $item['type'] = $type;
            $item['question_type'] = $data[$value]['question_type'];
            $item['test_id'] = $id;
            $item['questions_id'] = $data[$value]['id'];
            $item['score'] = $score;
            if (self::be($item)) continue;
            self::set($item);
        }
        return true;
    }

    public static function testPaperQuestions($id)
    {
        if (!$id) return [];
        return self::where(['test_id' => $id])->order('sort desc,id desc')->field('id,questions_id')->select();
    }

    /**试卷中各类试题
     * @param $id
     * @param $question_type
     */
    public static function gettestPaperQuestions($id, $question_type)
    {
        if (!$id) return [];
        return self::alias('t')->where(['t.test_id' => $id, 't.question_type' => $question_type, 'q.is_del' => 0])->order('sort desc,id desc')
            ->join('Questions q', 't.questions_id=q.id')
            ->field('t.sort,t.test_id,q.id,q.question_type,q.pid,q.stem,q.is_del,q.is_img')
            ->select();
    }

    /**
     * 试卷中各类试题数量
     */
    public static function testPaperQuestionsNumber($id, $question_type)
    {
        return self::where(['test_id' => $id, 'question_type' => $question_type])->count();
    }
}
