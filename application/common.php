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


// 应用公共文件

/**
 * 敏感词过滤
 *
 * @param string
 * @return string
 */
function sensitive_words_filter($str)
{
    header('content-type:text/html;charset=utf-8');
    if (!$str) return '';
    $file = ROOT_PATH . 'public/static/plug/censorwords/CensorWords';
    $words = file($file);
    foreach ($words as $word) {
        $word = str_replace(array("\r\n", "\r", "\n", " "), '', $word);
        if (!$word) continue;
        $ret = @preg_match("/$word/", $str, $match);
        if ($ret) {
            return $match[0];
        }
    }
    return '';
}

function getController()
{
    return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', think\Request::instance()->controller()));
}

function getModule()
{
    return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', think\Request::instance()->module()));
}

function processingData($browse_count)
{
    if ($browse_count > 9999) {
        $browse_count = bcdiv($browse_count, 10000, 1) . 'W';
    }
    return $browse_count;
}

/**
 * 获取图片库链接地址
 * @param $key
 * @return string
 */
function get_image_Url($key)
{
    return think\Url::build('admin/widget.images/index', ['fodder' => $key]);
}


/**
 * 获取链接对应的key
 * @param $value
 * @param bool $returnType
 * @param string $rep
 * @return array|string
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function get_key_attr($value, $returnType = true, $rep = '')
{
    if (!$value) return '';
    $inif = \app\admin\model\system\SystemAttachment::where('att_dir', $value)->find();
    if ($inif) {
        return [
            'key' => $inif->name,
            'pic' => $value,
        ];
    } else {
        if ($returnType) {
            return [
                'key' => '',
                'pic' => $value,
            ];
        } else {
            return [
                'key' => '',
                'pic' => '',
            ];
        }
    }
}

/**
 * 获取系统配置内容
 * @param $name
 * @param string $default
 * @return string
 */
function get_config_content($name, $default = '')
{
    try {
        return \app\admin\model\system\SystemConfigContent::getValue($name);
    } catch (\Throwable $e) {
        return $default;
    }
}

/**
 * 打印日志
 * @param $name
 * @param $data
 * @param int $type
 */
function live_log($name, $data, $type = 8)
{
    file_put_contents($name . '.txt', '[' . date('Y-m-d H:i:s', time()) . ']' . print_r($data, true) . "\r\n", $type);
}

/**获取当前登录用户的角色信息
 * @return mixed
 */
function get_login_role()
{
    $role['role_id'] = \think\Session::get("adminInfo")['roles'];
    $role['role_sign'] = \think\Session::get("adminInfo")['role_sign'];
    return $role;
}

/**获取登录用户账户信息
 * @return mixed
 */
function get_login_id()
{
    $admin['admin_id'] = \think\Session::get("adminId");
    return $admin;
}


function money_rate_num($money, $type)
{
    if (!$money) $money = 0;
    if (!$type) return \service\JsonService::fail('非法参数2');
    switch ($type) {
        case "gold":
            $goldRate = \service\SystemConfigService::get("gold_rate");
            $num = bcmul($money, $goldRate, 0);
            return $num;
        default:
            return \service\JsonService::fail('汇率类型缺失');

    }
}

function getUrlToDomain()
{
    $site_url = \service\SystemConfigService::get('site_url');
    if ($site_url == '') $site_url = $_SERVER['PHP_SELF'];
    $arr = parse_url($site_url);
    if (!isset($arr['host'])) $arr['host'] = $arr['path'];
    $array = explode('.', $arr['host']);
    return implode('_', $array);
}

if (!function_exists('filter_emoji')) {

    // 过滤掉emoji表情
    function filter_emoji($str)
    {
        $str = preg_replace_callback(    //执行一个正则表达式搜索并且使用一个回调进行替换
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);
        return $str;
    }
}

function lightTypeNmae($light_type)
{
    switch ($light_type) {
        case 1:
            $type = '图文';
            break;
        case 2:
            $type = '音频';
            break;
        case 3:
            $type = '视频';
            break;
    }
    return $type;
}

//读取版本号
function getversion()
{
    $version_arr = [];
    $curent_version = @file(dirname(__DIR__) . '/.version');
    foreach ($curent_version as $val) {
        list($k, $v) = explode('=', $val);
        $version_arr[$k] = trim($v);
    }
    return $version_arr;
}
