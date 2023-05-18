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

namespace app\admin\controller\wechat;

use app\admin\controller\AuthController;
use app\admin\model\wechat\WechatMessage as MessageModel;

/**
 * 用户扫码点击事件
 * Class WechatMessage
 * @package app\admin\controller\wechat
 */
class WechatMessage extends AuthController
{

    /**
     * 显示操作记录
     */
    public function index()
    {
        $where = parent::getMore([
            ['nickname', ''],
            ['type', ''],
            ['data', ''],
        ], $this->request);
        $this->assign('where', $where);
        $this->assign('mold', MessageModel::$mold);
        $this->assign(MessageModel::systemPage($where));
        return $this->fetch();
    }


}

