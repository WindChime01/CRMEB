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

/**
 * 证书 Model
 * Class Certificate
 * @package app\merchant\model\questions
 */
class Certificate extends ModelBasic
{
    use ModelTrait;

    /**条件
     * @param $where
     */
    public static function setWhere($where = [])
    {
        $model = self::where(['is_del' => 0]);
        if (isset($where['obtain']) && $where['obtain'] > 0) $model = $model->where('obtain', $where['obtain']);
        if (isset($where['title']) && $where['title'] != '') $model = $model->where('title', 'like', "%$where[title]%");
        if (isset($where['mer_id']) && $where['mer_id'] != '') $model = $model->where('mer_id', $where['mer_id']);
        return $model;
    }

    /**证书列表
     * @param $where 条件
     */
    public static function getCertificateList($where)
    {
        $data = self::setWhere($where)->page((int)$where['page'], (int)$where['limit'])->order('sort desc,add_time desc')->select();
        foreach ($data as $key => &$value) {
            switch ($value['obtain']) {
                case 1:
                    $value['obtains'] = '课程';
                    break;
                case 2:
                    $value['obtains'] = '考试';
                    break;
            }
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**
     * 证书列表
     */
    public static function certificateList($mer_id)
    {
        $list = self::where(['is_del' => 0, 'mer_id' => $mer_id])->order('sort desc,add_time desc')->select();
        return $list;
    }
}
