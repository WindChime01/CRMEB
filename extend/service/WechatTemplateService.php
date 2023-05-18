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


namespace service;

use app\wap\model\user\WechatUser;
use app\admin\model\wechat\WechatTemplate as WechatTemplateModel;
use app\admin\model\wechat\StoreService as ServiceModel;
use app\admin\model\system\SystemMessage;

class WechatTemplateService
{
    /**
     * 主营行业：IT科技 互联网|电子商务
     * 副营行业：其他 其他
     */

    //订单生成通知 改
    const ORDER_CREATE = 'OPENTM201682460';

    //订单支付成功 改
    const ORDER_PAY_SUCCESS = 'OPENTM201752540';

    //订单发货提醒(快递) 改
    const ORDER_POSTAGE_SUCCESS = 'OPENTM408918196';

    //订单收货通知 改
    const ORDER_TAKE_SUCCESS = 'OPENTM418528119';

    //退款成功通知 改
    const ORDER_REFUND_STATUS = 'OPENTM411627204';

    //资金变动通知 改
    const USER_BALANCE_CHANGE = 'OPENTM405575524';

    //开播通知
    const LIVE_START_NOTICE = 'OPENTM405456204';

    //拼单进度提醒
    const PINK_ORDER_REMIND = 'OPENTM415198906';

    //拼团成功通知
    const ORDER_USER_GROUPS_SUCCESS = 'OPENTM407456411';

    //拼团失败通知
    const ORDER_USER_GROUPS_LOSE = 'OPENTM401113750';

    //活动报名成功通知 改
    const ORDER_USER_SIGN_UP_SUCCESS = 'OPENTM406210305';

    //审核结果通知 改
    const EXAMINE_RESULT = 'OPENTM412612226';

    //讲师入驻审核通过通知 改
    const SETTLED_THROUGH = 'OPENTM412612226';

    //讲师入驻审核未通过通知 改
    const ENTRY_FAILED = 'OPENTM412612226';

    public static function sendTemplate($openid, $templateId, array $data, $url = null, $defaultColor = '')
    {
        $message = SystemMessage::where('tempkey', $templateId)->field('tempkey,is_wechat')->find();
        if (!$message['is_wechat']) return true;
        $tempid = WechatTemplateModel::where('tempkey', $templateId)->where('status', 1)->value('tempid');
        if (!$tempid) return false;
        try {
            return WechatService::sendTemplate($openid, $tempid, $data, $url, $defaultColor);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**服务进度通知
     * @param array $data
     * @param null $url
     * @param string $defaultColor
     * @return bool
     */
    public static function sendAdminNoticeTemplate($mer_id, array $data, $url = null, $defaultColor = '')
    {
        $kefuIds = ServiceModel::where(['notify' => 1])->where('mer_id','in',[0,$mer_id])->column('uid');
        $adminList = array_unique($kefuIds);
        if (!is_array($adminList) || empty($adminList)) return false;
        foreach ($adminList as $uid) {
            try {
                $openid = WechatUser::uidToOpenid($uid);
            } catch (\Exception $e) {
                continue;
            }
            self::sendTemplate($openid, self::ORDER_PAY_SUCCESS, $data, $url, $defaultColor);
        }
    }

    /**
     * 返回所有支持的行业列表
     * @return \EasyWeChat\Support\Collection
     */
    public static function getIndustry()
    {
        return WechatService::noticeService()->getIndustry();
    }

    /**
     * 修改账号所属行业
     * 主行业    副行业    代码
     * IT科技    互联网/电子商务    1
     * IT科技    IT软件与服务    2
     * IT科技    IT硬件与设备    3
     * IT科技    电子技术    4
     * IT科技    通信与运营商    5
     * IT科技    网络游戏    6
     * 金融业    银行    7
     * 金融业    基金|理财|信托    8
     * 金融业    保险    9
     * 餐饮    餐饮    10
     * 酒店旅游    酒店    11
     * 酒店旅游    旅游    12
     * 运输与仓储    快递    13
     * 运输与仓储    物流    14
     * 运输与仓储    仓储    15
     * 教育    培训    16
     * 教育    院校    17
     * 政府与公共事业    学术科研    18
     * 政府与公共事业    交警    19
     * 政府与公共事业    博物馆    20
     * 政府与公共事业    公共事业|非盈利机构    21
     * 医药护理    医药医疗    22
     * 医药护理    护理美容    23
     * 医药护理    保健与卫生    24
     * 交通工具    汽车相关    25
     * 交通工具    摩托车相关    26
     * 交通工具    火车相关    27
     * 交通工具    飞机相关    28
     * 房地产    建筑    29
     * 房地产    物业    30
     * 消费品    消费品    31
     * 商业服务    法律    32
     * 商业服务    会展    33
     * 商业服务    中介服务    34
     * 商业服务    认证    35
     * 商业服务    审计    36
     * 文体娱乐    传媒    37
     * 文体娱乐    体育    38
     * 文体娱乐    娱乐休闲    39
     * 印刷    印刷    40
     * 其它    其它    41
     * @param $industryId1
     * @param $industryId2
     * @return \EasyWeChat\Support\Collection
     */
    public static function setIndustry($industryId1, $industryId2)
    {
        return WechatService::noticeService()->setIndustry($industryId1, $industryId2);
    }

    /**
     * 获取所有模板列表
     * @return \EasyWeChat\Support\Collection
     */
    public static function getPrivateTemplates()
    {
        return WechatService::noticeService()->getPrivateTemplates();
    }

    /**
     * 删除指定ID的模板
     * @param $templateId
     * @return \EasyWeChat\Support\Collection
     */
    public static function deletePrivateTemplate($templateId)
    {
        return WechatService::noticeService()->deletePrivateTemplate($templateId);
    }


    /**
     * 添加模板并获取模板ID
     * @param $shortId
     * @return \EasyWeChat\Support\Collection
     */
    public static function addTemplate($shortId)
    {
        return WechatService::noticeService()->addTemplate($shortId);
    }
}
