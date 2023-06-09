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


namespace app\wap\model\user;

use basic\ModelBasic;
use service\WechatService;
use service\WechatSubscribe;
use service\CacheService as Cache;
use think\Session;
use traits\ModelTrait;

/**微信信息表
 * Class WechatUser
 * @package app\wap\model\user
 */
class WechatUser extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    public static function setAddTimeAttr($value)
    {
        return time();
    }

    /**
     * 添加一个新用户
     * @param array $wechatInfo
     * @return boolen
     * */
    public static function setNewUserInfo($wechatInfo)
    {
        if (self::be(['openid' => $wechatInfo['openid']])) {
            self::where(['openid' => $wechatInfo['openid']])->update(['uid' => $wechatInfo['uid']]);
        } else {
            self::set($wechatInfo);
        }
    }

    /**
     * .添加新用户
     * @param $openid
     * @return object
     */
    public static function setNewUser($openid)
    {
        $userInfo = WechatService::getUserInfo($openid);
        $userInfo['nickname'] = '';
        $userInfo['headimgurl'] = '/system/images/user_log.jpg';
        if (!isset($userInfo['subscribe']) || !$userInfo['subscribe'] || !isset($userInfo['openid']))
            exception('请关注公众号!');
        $userInfo['tagid_list'] = implode(',', $userInfo['tagid_list']);
        self::beginTrans();
        $wechatUser = User::setWechatUser($userInfo);
        if (!$wechatUser) {
            self::rollbackTrans();
            exception('用户信息储存失败!');
        }
        $wechatUser = self::set($wechatUser);
        if (!$wechatUser) {
            self::rollbackTrans();
            exception('用户储存失败!');
        }

        self::commitTrans();
        return $wechatUser;
    }

    /**
     * 更新用户信息
     * @param $openid
     * @return bool
     */
    public static function updateUser($openid)
    {
        $userInfo = WechatService::getUserInfo($openid);
        $userInfo['tagid_list'] = implode(',', $userInfo['tagid_list']);
        return self::edit($userInfo, $openid, 'openid');
    }

    /**
     * 用户存在就更新 不存在就添加
     * @param $openid
     */
    public static function saveUser($openid)
    {
        self::be($openid, 'openid') == true ? self::updateUser($openid) : self::setNewUser($openid);
    }

    /**
     * 用户关注
     * @param $openid
     * @return bool
     */
    public static function subscribe($openid)
    {
        return self::edit(['subscribe' => 1], $openid, 'openid');
    }

    /**
     * 用户取消关注
     * @param $openid
     * @return bool
     */
    public static function unSubscribe($openid)
    {
        return self::edit(['subscribe' => 0], $openid, 'openid');
    }

    /**
     * 用uid获得openid
     * @param $uid
     * @return mixed
     */
    public static function uidToOpenid($uid, $update = false)
    {
        $cacheName = 'openid_' . $uid;
        $openid = Cache::get($cacheName);
        if ($openid && !$update) return $openid;
        $openid = self::where('uid', $uid)->value('openid');
        Cache::set($cacheName, $openid, 0);
        return $openid;
    }

    /**
     * 用uid获得Unionid
     * @param $uid
     * @return mixed
     */
    public static function uidToUnionid($uid, $update = false)
    {
        $cacheName = 'unionid_' . $uid;
        $unionid = Cache::get($cacheName);
        if ($unionid && !$update) return $unionid;
        $unionid = self::where('uid', $uid)->value('unionid');
        if (!$unionid) exception('对应的unionid不存在!');
        Cache::set($cacheName, $unionid, 0);
        return $unionid;
    }

    /**
     * 用openid获得uid
     * @param $uid
     * @return mixed
     */
    public static function openidToUid($openid, $update = false)
    {
        $cacheName = 'uid_' . $openid;
        $uid = Cache::get($cacheName);
        if ($uid && !$update) return $uid;
        $uid = self::where('openid', $openid)->value('uid');
        if (!$uid) exception('对应的uid不存在!');
        Cache::set($cacheName, $uid, 0);
        return $uid;
    }

    /**
     * 获取用户信息
     * @param $openid
     * @return array
     */
    public static function getWechatInfo($openid)
    {
        if (is_numeric($openid)) $openid = self::uidToOpenid($openid);
        $wechatInfo = self::where('openid', $openid)->find();
        if (!$wechatInfo) {
            self::setNewUser($openid);
            $wechatInfo = self::where('openid', $openid)->find();
        }
        if (!$wechatInfo) exception('获取用户信息失败!');
        return $wechatInfo->toArray();
    }

}
