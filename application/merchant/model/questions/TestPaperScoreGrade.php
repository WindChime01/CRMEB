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

namespace app\merchant\model\questions;

use traits\ModelTrait;
use basic\ModelBasic;
use service\UtilService as Util;
use app\merchant\model\questions\TestPaper as TestPaperModel;

/**
 * 试卷分数等级划分 Model
 * Class TestPaperScoreGrade
 * @package app\merchant\model\questions
 */
class TestPaperScoreGrade extends ModelBasic
{
    use ModelTrait;

    /**添加/修改试卷分数等级
     * @param array $data
     */
    public static function testPaperScoreGradeAdd($id = 0, $data = [])
    {
        if (!$id || count($data) <= 0) return false;
        self::where('test_id', $id)->delete();
        foreach ($data as $k => $time) {
            $time['test_id'] = $id;
            self::set($time);
        }
        return true;
    }

    /**
     * 试卷分数等级列表
     */
    public static function testPaperScoreGradeList($id = 0)
    {
        return self::where(['test_id' => $id])->order('id asc')->select();
    }

    /**获得分数对应的等级
     * @param $score
     */
    public static function getTestPaperScoreGrade($test_id, $score)
    {
        $grade = self::where(['test_id' => $test_id])->order('id asc')->select();
        $grade = count($grade) > 0 ? $grade->toArray() : [];
        if (!count($grade)) return '无';
        foreach ($grade as $key => $value) {
            $arr = explode('~', $value['grade_standard']);
            if ($score >= $arr[0] && $score <= $arr[1]) {
                return $value['grade_name'];
            }
        }
        return '无';
    }

}
