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

namespace app\merchant\model\merchant;

use app\merchant\model\user\User;
use traits\ModelTrait;
use basic\ModelBasic;

class MerchantFollow extends ModelBasic
{
    use ModelTrait;

    public static function systemPage($mid)
    {
        $model = self::alias('A')->field('A.follow_time,A.unfollow_time,A.is_follow,B.uid,B.avatar,B.nickname')->where('A.mer_id',$mid)
            ->join('User B','A.uid = B.uid')
            ->order('A.follow_time DESC')->group('A.uid');
        return self::page($model);
    }

}
