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

/**
 * 考试试题记录 Model
 * Class ExaminationTestRecord
 */
class ExaminationTestRecord extends ModelBasic
{
    use ModelTrait;

    /**保存试题答题结果
     * @param $data
     * @param $uid
     */
    public static function addExaminationTestRecord($data, $uid)
    {
        $id = self::where(['e_id' => $data['e_id'], 'type' => $data['type'], 'questions_id' => $data['questions_id'], 'uid' => $uid])->value('id');
        if ($data['is_correct'] != 2) $data['score'] = 0;
        if ($id) {
            $dat['user_answer'] = $data['user_answer'];
            $dat['is_correct'] = $data['is_correct'];
            $dat['score'] = $data['score'];
            $res = self::edit($dat, $id);
        } else {
            $data['uid'] = $uid;
            $res = self::set($data);
        }
        return $res;
    }

    /**检测是否答题
     * @param $e_id
     * @param $uid
     * @param $qid
     */
    public static function checkWhetherAnswerQuestions($e_id, $type, $uid, $qid)
    {
        $test = self::where(['e_id' => $e_id, 'type' => $type, 'questions_id' => $qid, 'uid' => $uid])->find();
        if (!$test) return [];
        else return ['is_correct' => $test['is_correct'], 'user_answer' => $test['user_answer']];
    }
}
