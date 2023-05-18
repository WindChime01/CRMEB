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
 * 获取用户名称
 * @param $uid
 * @return mixed
 */
function getUserNickname($uid)
{
    return \app\admin\model\user\User::where('uid', $uid)->value('nickname');
}

/**删除缓存hash数据
 * @param $name
 * @param $key
 * @return bool
 */
function del_redis_hash($name, $key)
{
    if (!$name || !$key) return false;
    try {
        $redisModel = new \think\cache\driver\Redis();
        $exists_recommend_redis = $redisModel->HEXISTS($name, $key);
        if ($exists_recommend_redis) $redisModel->hdel($name, $key);
        return true;
    } catch (\Exception $e) {
        $basic = new \basic\WapBasic;
        return $basic->serRedisPwd($e->getMessage());
    }
}

/**
 * 获取产品名称
 * @param $id
 * @return mixed
 */
function getProductName($id)
{
    return \app\admin\model\store\StoreProduct::where('id', $id)->value('store_name');
}


/**
 * 获取订单编号
 * @param $id
 */
function getOrderId($id)
{
    return \app\admin\model\order\StoreOrder::where('id', $id)->value('order_id');
}


/**
 * 根据用户uid获取订单数
 * @param $uid
 * @return int|string
 */
function getOrderCount($uid)
{
    return \app\admin\model\order\StoreOrder::where('uid', $uid)->where('paid', 1)->where('refund_status', 0)->where('status', 2)->count();
}

/**
 * 格式化属性
 * @param $arr
 * @return array
 */
function attrFormat($arr)
{
    $data = [];
    $res = [];
    if (count($arr) > 1) {
        for ($i = 0; $i < count($arr) - 1; $i++) {
            if ($i == 0) $data = $arr[$i]['detail'];
            //替代变量1
            $rep1 = [];
            foreach ($data as $v) {
                foreach ($arr[$i + 1]['detail'] as $g) {
                    //替代变量2
                    $rep2 = ($i != 0 ? '' : $arr[$i]['value'] . "_") . $v . "-" . $arr[$i + 1]['value'] . "_" . $g;
                    $tmp[] = $rep2;
                    if ($i == count($arr) - 2) {
                        foreach (explode('-', $rep2) as $k => $h) {
                            //替代变量3
                            $rep3 = explode('_', $h);
                            //替代变量4
                            $rep4['detail'][$rep3[0]] = $rep3[1];
                        }
                        $res[] = $rep4;
                    }
                }
            }
            $data = $tmp;
        }
    } else {
        $dataArr = [];
        foreach ($arr as $k => $v) {
            foreach ($v['detail'] as $kk => $vv) {
                $dataArr[$kk] = $v['value'] . '_' . $vv;
                $res[$kk]['detail'][$v['value']] = $vv;
            }
        }
        $data[] = implode('-', $dataArr);
    }
    return [$data, $res];
}

/**
 * 格式化月份
 * @param string $time
 * @param int $ceil
 * @return array
 */
function getMonth($time = '', $ceil = 0)
{
    if (empty($time)) {
        $firstday = date("Y-m-01", time());
        $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));
    } else if ($time == 'n') {
        if ($ceil != 0)
            $season = ceil(date('n') / 3) - $ceil;
        else
            $season = ceil(date('n') / 3);
        $firstday = date('Y-m-01', mktime(0, 0, 0, ($season - 1) * 3 + 1, 1, date('Y')));
        $lastday = date('Y-m-t', mktime(0, 0, 0, $season * 3, 1, date('Y')));
    } else if ($time == 'y') {
        $firstday = date('Y-01-01');
        $lastday = date('Y-12-31');
    } else if ($time == 'h') {
        $firstday = date('Y-m-d', strtotime('this week +' . $ceil . ' day')) . ' 00:00:00';
        $lastday = date('Y-m-d', strtotime('this week +' . ($ceil + 1) . ' day')) . ' 23:59:59';
    }
    return array($firstday, $lastday);
}

/**删除目录下所有文件
 * @param $path 目录或者文件路径
 * @param string $ext
 * @return bool
 */
function clearfile($path, $ext = '*.log')
{
    $files = (array)glob($path . DS . '*');
    foreach ($files as $path) {
        if (is_dir($path)) {
            $matches = glob($path . '/' . $ext);
            if (is_array($matches)) {
                array_map('unlink', $matches);
            }
            rmdir($path);
        } else {
            unlink($path);
        }
    }
    return true;
}

function get_server_ip()
{
    if (isset($_SERVER)) {
        if (array_key_exists("SERVER_ADDR", $_SERVER)) {
            $server_ip = $_SERVER['SERVER_ADDR'];
        } else {
            $server_ip = $_SERVER['LOCAL_ADDR'];
        }
    } else {
        $server_ip = getenv('SERVER_ADDR');
    }
    return $server_ip;
}

/**
 * 获取CRMEB系统版本号
 * @param string $default
 * @return string
 */
function get_crmeb_version($default = 'v1.0.0')
{
    try {
        $version = parse_ini_file(app()->getRootPath() . '.version');
        return $version['version'] ?? $default;
    } catch (\Throwable $e) {
        return $default;
    }
}

if (!function_exists('check_phone')) {
    /**
     * 手机号验证
     * @param $phone
     * @return false|int
     */
    function check_phone($phone)
    {
        return preg_match("/^1[3456789]\d{9}$/", $phone);
    }
}
/**检查是否有看手机号权限
 * @return bool
 */
function is_phone_menu()
{
    return \app\admin\model\system\SystemMenus::isMenu('查看手机号', 'is_phone');
}
