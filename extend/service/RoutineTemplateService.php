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

use app\wap\model\routine\RoutineServer;
use think\Db;
use basic\AuthBasic;

/**
 * 公众号订阅消息
 * Class RoutineTemplate
 * @package app\routine\model\routine
 */
class RoutineTemplateService extends AuthBasic
{


    //订单支付成功
    const ORDER_PAY_SUCCESS = '6271';
    //订单发货提醒(快递)
    const ORDER_POSTAGE_SUCCESS = '3637';
    //直播开播提醒
    const LIVE_BROADCAST = '16172';
    //拼单进度提醒
    const PINK_ORDER_REMIND = '5275';
    //拼团成功通知
    const ORDER_USER_GROUPS_SUCCESS = '3098';
    //拼团失败通知
    const ORDER_USER_GROUPS_LOSE = '4534';
    //开团成功通知
    const ORDER_USER_PINGT_SUCCESS = '4533';
    //账户资金变动提醒
    const USER_BALANCE_CHANGE = '4148';
    //退款进度通知
    const ORDER_REFUND_STATUS = '1451';
    //收货提醒通知
    const ORDER_TAKE_SUCCESS = '3510';
    //活动报名成功通知
    const ORDER_USER_SIGN_UP_SUCCESS = '13684';
    //审核结果通知
    const EXAMINE_RESULT = '5545';
    //检查模版
    const TEMPLATE_CHECK = 1;

    /**
     * 根据模板编号获取模板ID
     * @param string $tempKey
     * @return mixed|string
     */
    public static function setTemplateId($tempKey = '')
    {
        if ($tempKey == '') return '';
        return Db::name('RoutineTemplate')->where('tempkey', $tempKey)->where('status', 1)->value('tempid');
    }

    /**
     * 从公共模板库中选用模板，到私有模板库中
     */
    public static function addTemplate($tid, $kidList = [], $sceneDesc = '')
    {
        $accessToken = RoutineServer::get_access_token();
        $url = "https://api.weixin.qq.com/wxaapi/newtmpl/addtemplate?access_token=" . $accessToken;
        $data['access_token'] = $accessToken;
        $data['tid'] = $tid;
        $data['kidList'] = $kidList;
        $data['sceneDesc'] = $sceneDesc;
        if (self::TEMPLATE_CHECK != 1) return false;
        return json_decode(parent::curlPost($url, json_encode($data)), true);
    }

    /**
     * 获取公众号所属类目，可用于查询类目下的公共模板
     */
    public static function getCategory()
    {
        $accessToken = RoutineServer::get_access_token();
        $url = "https://api.weixin.qq.com/wxaapi/newtmpl/addtemplate?access_token=" . $accessToken;
        if (self::TEMPLATE_CHECK != 1) return false;
        return json_decode(parent::curlGet($url), true);
    }

    /**
     * 获取公共模板下的关键词列表
     */
    public static function getPubTemplateKeyWordsById($tid)
    {
        $accessToken = RoutineServer::get_access_token();
        $url = "https://api.weixin.qq.com/wxaapi/newtmpl/getpubtemplatekeywords?access_token=" . $accessToken;
        $data['access_token'] = $accessToken;
        $data['tid'] = $tid;
        if (self::TEMPLATE_CHECK != 1) return false;
        return json_decode(parent::curlGet($url, json_encode($data)), true);
    }

    /**
     * 获取类目下的公共模板，可从中选用模板使用
     */
    public static function getPubTemplateTitleList($ids, $start = 0, $limit = 10)
    {
        $accessToken = RoutineServer::get_access_token();
        $url = "https://api.weixin.qq.com/wxaapi/newtmpl/getpubtemplatetitles?access_token=" . $accessToken;
        $data['access_token'] = $accessToken;
        $data['ids'] = $ids;
        $data['start'] = $start;
        $data['limit'] = $limit;
        if (self::TEMPLATE_CHECK != 1) return false;
        return json_decode(parent::curlGet($url, json_encode($data)), true);
    }

    /**
     * 获取私有的模板列表
     * @param int $offset
     * @param int $count
     * @return mixed
     */
    public static function getTemplateList()
    {
        $accessToken = RoutineServer::get_access_token();
        $url = "https://api.weixin.qq.com/wxaapi/newtmpl/gettemplate?access_token=" . $accessToken;
        if (self::TEMPLATE_CHECK != 1) return false;
        return json_decode(parent::curlGet($url), true);
    }

    /**
     * 删除公众号中的某个订阅消息
     * @param string $templateId
     * @return bool|mixed
     */
    public static function delTemplate($priTmplId = '')
    {
        if ($priTmplId == '') return false;
        $accessToken = RoutineServer::get_access_token();
        $url = "https://api.weixin.qq.com/wxaapi/newtmpl/deltemplate?access_token=" . $accessToken;
        $data['access_token'] = $accessToken;
        $data['priTmplId'] = $priTmplId;
        if (self::TEMPLATE_CHECK != 1) return false;
        return json_decode(AuthBasic::curlPost($url, json_encode($data)), true);
    }

    /**
     * 发送订阅消息
     * @param string $openId 接收者（用户）的 openid
     * @param string $templateId 所需下发的模板消息的id
     * @param string $link 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
     * @param array $dataKey 模板内容，不填则下发空模板
     * @return bool|mixed
     */
    public static function sendTemplate($openId = '', $templateId = '', $link = '', $dataKey = array())
    {
        if ($openId == '' || $templateId == '') return false;
        $accessToken = RoutineServer::get_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/message/subscribe/bizsend?access_token=" . $accessToken;
        $data['touser'] = $openId;//接收者（用户）的 openid
        $data['template_id'] = $templateId; //所需下发的模板消息的id
        $data['page'] = $link; //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
        $data['data'] = $dataKey;  //模板内容，不填则下发空模板
        if (self::TEMPLATE_CHECK != 1) return false;
        return json_decode(parent::curlPost($url, json_encode($data)), true);
    }
}
