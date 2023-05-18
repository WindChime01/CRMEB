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

namespace app\wap\model\topic;

use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\questions\TestPaperScoreGrade;

/**
 * 用户考试记录 Model
 * Class ExaminationRecord
 */
class ExaminationRecord extends ModelBasic
{
    use ModelTrait;

    /**添加考试记录
     * @param $id
     * @param $type
     * @param $uid
     */
    public static function addExaminationRecord($id, $type, $uid, $txamination_time)
    {
        $data['test_id'] = $id;
        $data['type'] = $type;
        $data['uid'] = $uid;
        $data['add_time'] = time();
        if ($type == 2) {
            $data['start_time'] = time();
            $data['end_time'] = bcadd(time(), bcmul($txamination_time, 60, 0), 0);
        }
        return self::insertGetId($data);
    }

    /**清除上次考试结果
     * @param $id
     * @param $type
     * @param $uid
     */
    public static function clearLastExamResults($id, $type, $uid)
    {
        $record = self::where(['test_id' => $id, 'type' => $type, 'uid' => $uid])->order('id desc')->find();
        if (!$record) return true;
        $testCount = ExaminationTestRecord::where(['e_id' => $record['id'], 'type' => $record['type']])->count();
        if (!$testCount) return true;
        self::where(['test_id' => $id, 'type' => $type, 'uid' => $uid, 'is_submit' => 0])->update(['is_submit' => 1]);
        $res = ExaminationTestRecord::where(['e_id' => $record['id'], 'type' => $record['type']])->delete();
        return $res;
    }

    /**提交考试记录
     * @param $data
     * @param $uid
     */
    public static function submitExaminationRecord($data, $uid)
    {
        $record = self::where(['id' => $data['examination_id'], 'type' => $data['type'], 'uid' => $uid])->find();
        if (!$record || $record['is_submit']) return self::setErrorInfo('记录不存在或已提交!');
        $yes_questions = ExaminationTestRecord::where(['e_id' => $record['id'], 'type' => $record['type'], 'is_correct' => 2])->count();
        $wrong_question = ExaminationTestRecord::where(['e_id' => $record['id'], 'type' => $record['type'], 'is_correct' => 1])->count();
        $testPaper = TestPaper::where(['id' => $record['test_id'], 'is_show' => 1, 'is_del' => 0])->field('is_score,item_number')->find();
        $array['accuracy'] = bcmul(bcdiv($yes_questions, $testPaper['item_number'], 2), 100, 0);
        $array['yes_questions'] = $yes_questions;
        $array['wrong_question'] = $wrong_question;
        $array['is_submit'] = 1;
        $array['duration'] = $data['duration'];
        $array['score'] = ExaminationTestRecord::where(['e_id' => $record['id'], 'type' => $record['type'], 'is_correct' => 2])->sum('score');
        $array['grade'] = TestPaperScoreGrade::getTestPaperScoreGrade($record['test_id'], $array['score']);
        $res = self::edit($array, $record['id']);
        if (!$res) return self::setErrorInfo('记录修改错误!');
        $res1 = ExaminationWrongBank::addWrongBank($record['id'], $record['test_id'], $record['type'], $uid);
        $res2 = TestPaper::PreExercisesWhere()->where(['id' => $record['test_id']])->setInc('answer');
        $res3 = TestPaperObtain::where(['test_id' => $record['test_id'], 'type' => $data['type'], 'uid' => $uid, 'is_del' => 0])->setInc('number');
        if ($res && $res1 && $res2 && $res3) return true;
        else return false;
    }
}
