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

namespace app\wap\model\special;

use basic\ModelBasic;
use traits\ModelTrait;
use app\wap\model\merchant\Merchant;
use app\wap\model\merchant\MerchantFollow;

/**讲师 model
 * Class Lecturer
 * @package app\wap\model\special
 */
class Lecturer extends ModelBasic
{
    use ModelTrait;

    /**讲师列表
     * @param int $page
     * @param int $limit
     * @return array
     */
    public static function getLecturerList($uid, $page = 1, $limit = 10, $search = '')
    {
        $model = new self();
        if ($search != '') {
            $model = $model->where('lecturer_name', 'LIKE', "%$search%");
        }
        $data = $model->where(['is_del' => 0, 'is_show' => 1])->where('mer_id','>',0)->page((int)$page, (int)$limit)
            ->field('id,mer_id,lecturer_name,lecturer_head,label,curriculum,explain,study,sort,is_show,is_del')
            ->order('sort DESC,id DESC')->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        foreach ($data as $key => &$value) {
            $value['is_follow'] = $uid > 0 ? MerchantFollow::isFollow($uid, $value['mer_id']) : ['code'=>0];
            $value['label'] = json_decode($value['label']);
        }
        return $data;
    }

    /**讲师详情
     * @param int $id
     */
    public static function details($id = 0)
    {
        $details = self::where(['is_del' => 0, 'is_show' => 1])->where('id', $id)->find();
        if ($details) {
            $details['label'] = json_decode($details['label']);
            $details['introduction'] = htmlspecialchars_decode($details['introduction']);
            return $details;
        } else {
            return null;
        }
    }

    public static function information($mer_id)
    {
        if (!$mer_id) return null;
        $lecturer_id = Merchant::where('id', $mer_id)->value('lecturer_id');
        $details = self::where(['is_del' => 0, 'is_show' => 1])->where('id', $lecturer_id)
            ->field('id,lecturer_name,lecturer_head,label')->find();
        if ($details) {
            $details['label'] = json_decode($details['label']);
            return $details;
        } else {
            return null;
        }
    }
}
