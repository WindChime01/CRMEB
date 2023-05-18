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
use service\UtilService as Util;
use app\wap\model\topic\Questions;
use app\wap\model\topic\ExaminationRecord;
use app\wap\model\topic\ExaminationTestRecord;

/**
 * 试卷试题 Model
 * Class TestPaperQuestions
 */
class TestPaperQuestions extends ModelBasic
{
    use ModelTrait;

    /**获取试卷中的试题
     * @param $test_id
     * @param $type
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getQuestionslist($test_id, $type, $question_type = 0)
    {
        $model = self::alias('p')->join('Questions q', 'p.questions_id=q.id')
            ->where(['p.type' => $type, 'p.test_id' => $test_id, 'q.is_del' => 0]);
        if ($question_type) {
            $model = $model->where('p.question_type', $question_type);
        }
        $list = $model->field('p.*,q.option,q.stem,q.image,q.answer,q.difficulty,q.analysis,q.relation,q.is_img,q.is_del')
            ->order('p.sort desc')->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        return $list;
    }
}
