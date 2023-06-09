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


namespace app\admin\model\wechat;

use app\admin\model\system\SystemConfig;
use traits\ModelTrait;
use basic\ModelBasic;
use service\HookService;
use service\UtilService;
use service\WechatService;
use think\Url;

/**
 * 关键字 model
 * Class WechatReply
 * @package app\admin\model\wechat
 */
class WechatReply extends ModelBasic
{
    use ModelTrait;

    public static $reply_type = ['text', 'image', 'news', 'voice'];

    public function getUrlAttr($value, $data)
    {
        return $value == '' ? \think\Url::build('index/index/news', ['id' => $data['id']]) : $value;
    }

    /**
     * @param $data
     * @param $key
     * @param $type
     * @param int $status
     * @return bool
     */
    public static function redact($data, $key, $type, $status = 1)
    {
        $method = 'tidy' . ucfirst($type);
        $res = self::$method($data, $key);
        if (!$res) return false;
        $count = self::where('key', $key)->count();
        if ($count) {
            $res = self::edit(['type' => $type, 'data' => json_encode($res), 'status' => $status], $key, 'key');
            if (!$res) return self::setErrorInfo('保存失败!');
        } else {
            $res = self::set([
                'key' => $key,
                'type' => $type,
                'data' => json_encode($res),
                'status' => $status,
            ]);
            if (!$res) return self::setErrorInfo('保存失败!');
        }
        return true;
    }

    /**
     * @param $key
     * @param string $field
     * @param int $hide
     * @return bool
     */
    public static function changeHide($key, $field = 'id', $hide = 0)
    {
        return self::edit(compact('hide'), $key, $field);
    }


    /**
     * 整理文本输入的消息
     * @param $data
     * @param $key
     * @return array|bool
     */
    public static function tidyText($data, $key)
    {
        $res = [];
        if (!isset($data['content']) || $data['content'] == '')
            return self::setErrorInfo('请输入回复信息内容');
        $res['content'] = $data['content'];
        return $res;
    }

    /**
     * 整理图片资源
     * @param $data
     * @param $key
     * @return array|bool|mixed
     */
    public static function tidyImage($data, $key)
    {
        if (!isset($data['src']) || $data['src'] == '')
            return self::setErrorInfo('请上传回复的图片');
        $reply = self::get(['key' => $key]);
        if ($reply) $reply['data'] = json_decode($reply['data'], true);
        if ($reply && isset($reply['data']['src']) && $reply['data']['src'] == $data['src']) {
            $res = $reply['data'];
        } else {
            $res = [];
            //TODO 图片转media
            $res['src'] = $data['src'];
            $material = (WechatService::materialService()->uploadImage(UtilService::urlToPath($data['src'])));
            $res['media_id'] = $material->media_id;
            HookService::afterListen('wechat_material',
                ['media_id' => $material->media_id, 'path' => $res['src'], 'url' => $material->url], 'image');
        }
        return $res;
    }

    /**
     * 整理声音资源
     * @param $data
     * @param $key
     * @return array|bool|mixed
     */
    public static function tidyVoice($data, $key)
    {
        if (!isset($data['src']) || $data['src'] == '')
            return self::setErrorInfo('请上传回复的声音');
        $reply = self::get(['key' => $key]);
        if ($reply) $reply['data'] = json_decode($reply['data'], true);
        if ($reply && isset($reply['data']['src']) && $reply['data']['src'] == $data['src']) {
            $res = $reply['data'];
        } else {
            $res = [];
            //TODO 声音转media
            $res['src'] = $data['src'];
            $material = (WechatService::materialService()->uploadVoice(UtilService::urlToPath($data['src'])));
            $res['media_id'] = $material->media_id;
            HookService::afterListen('wechat_material', ['media_id' => $material->media_id, 'path' => $res['src']], 'voice');
        }
        return $res;
    }

    /**
     * 整理图文资源
     * @param $data
     * @param $key
     * @return bool
     */
    public static function tidyNews($data, $key = '')
    {
        if (!count($data))
            return self::setErrorInfo('请选择图文消息');
        $siteUrl = SystemConfig::getValue('site_url');
        foreach ($data as $k => $v) {
            if (empty($v['url'])) $data[$k]['url'] = $siteUrl . Url::build('wap/article/details', ['id' => $v['id']]);
            if ($v['image']) $data[$k]['image'] = $v['image'];
        }
        return $data;
    }

    /**
     * 获取所有关键字
     * @param array $where
     * @return array
     */
    public static function getKeyAll($where = array())
    {
        $model = new self;
        if ($where['key'] !== '') $model = $model->where('key', 'LIKE', "%$where[key]%");
        if ($where['type'] !== '') $model = $model->where('type', $where['type']);
        $model = $model->where('key', '<>', 'subscribe');
        $model = $model->where('key', '<>', 'default');
        return self::page($model);
    }

    /**
     * 获取关键字
     * @param $key
     * @param string $default
     * @return array|\EasyWeChat\Message\Image|\EasyWeChat\Message\News|\EasyWeChat\Message\Text|\EasyWeChat\Message\Voice
     */
    public static function reply($key, $default = '')
    {
        $res = self::where('key', $key)->where('status', '1')->find();
        if (empty($res)) $res = self::where('key', 'default')->where('status', '1')->find();
        if (empty($res)) {
            die("success");
            return WechatService::textMessage($default);
        }
        $res['data'] = json_decode($res['data'], true);
        if ($res['type'] == 'text') {
            return WechatService::textMessage($res['data']['content']);
        } else if ($res['type'] == 'image') {
            return WechatService::imageMessage($res['data']['media_id']);
        } else if ($res['type'] == 'news') {
            return WechatService::newsMessage($res['data']);
        } else if ($res['type'] == 'voice') {
            return WechatService::voiceMessage($res['data']['media_id']);
        }
    }


}
