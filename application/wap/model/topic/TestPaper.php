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
use app\wap\model\topic\TestPaperCategory;

/**
 * 试卷列表 Model
 * Class TestPaper
 */
class TestPaper extends ModelBasic
{
    use ModelTrait;

    /**
     * 设置专题显示条件
     * @param string $alias 别名
     * @param null $model model
     * @param bool $isAL 是否起别名,默认执行
     * @return $this
     */
    public static function PreExercisesWhere($alias = '', $model = null, $isAL = false)
    {
        if (is_null($model)) $model = new self();
        if ($alias) {
            $isAL || $model = $model->alias($alias);
            $alias .= '.';
        }
        return $model->where(["{$alias}is_del" => 0, "{$alias}is_show" => 1, "{$alias}status" => 1]);
    }

    /**练习试卷列表
     * @param int $page
     * @param int $limit
     * @param $tid
     * @return array
     */
    public static function getTestPaperExercisesList($type, $page, $limit, $pid, $tid, $search)
    {
        $model = self::PreExercisesWhere();
        if ($tid) {
            $model = $model->where(['tid' => $tid]);
        } else if ($pid && !$tid) {
            $tids = TestPaperCategory::where('pid', $pid)->column('id');
            $model = $model->where('tid', 'in', $tids);
        }
        if ($search) $model = $model->where('title', 'LIKE', "%$search%");
        $list = $model->where('type', $type)->order('sort desc,id desc')->page($page, $limit)->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as $key => &$item) {
            $item['count'] = bcadd($item['answer'], $item['fake_sales'], 0);
        }
        return $list;
    }

    /**练习试卷列表
     * @param int $page
     * @param int $limit
     * @param $tid
     * @return array
     */
    public static function getMerTestPaperList($mer_id,$type, $page, $limit)
    {
        $list = [];
        if (!$mer_id) return $list;
        $model = self::PreExercisesWhere();
        $list = $model->where(['type'=>$type,'mer_id'=>$mer_id])->order('sort desc,id desc')->page($page, $limit)->select();
        $list = count($list) ? $list->toArray() : [];
        return $list;
    }

    /**获取考试标题
     * @param $id
     * @return float|mixed|string
     */
    public static function getName($id=0)
    {
        if(!$id) return '';
        return self::where(['id'=>$id])->value('title');
    }

}
