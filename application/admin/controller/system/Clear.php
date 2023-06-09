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

namespace app\admin\controller\system;

use app\admin\controller\AuthController;
use service\CacheService;
use service\JsonService as Json;
use think\Log;
use think\Cache;

/**
 * 首页控制器
 * Class Clear
 */
class Clear extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 刷新数据缓存
     */
    public function refresh_cache()
    {
        `php think optimize:schema`;
        `php think optimize:autoload`;
        `php think optimize:route`;
        `php think optimize:config`;
        return Json::successful('数据缓存刷新成功!');
    }

    /**
     * 清除首页缓存
     */
    public function del_home_redis()
    {
        $subjectUrl = getUrlToDomain();
        del_redis_hash($subjectUrl . "wap_index_has", "recommend_list");
        del_redis_hash($subjectUrl . "web_index_has", "recommend_list");
        return Json::successful('清除首页缓存成功!');
    }

    /**
     * 删除缓存
     */
    public function delete_cache()
    {
        Cache::clear();
        array_map('unlink', glob(TEMP_PATH . '/*.php'));
        return Json::successful('清除缓存成功!');
    }

    /**
     * 删除日志
     */
    public function delete_log()
    {
        array_map('unlink', glob(LOG_PATH . '/*.log'));
        $this->delDirAndFile(LOG_PATH);
        return Json::successful('清除日志成功!');
    }

    function delDirAndFile($dirName, $subdir = true)
    {
        if ($handle = opendir("$dirName")) {
            while (false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    if (is_dir("$dirName/$item"))
                        $this->delDirAndFile("$dirName/$item", false);
                    else
                        @unlink("$dirName/$item");
                }
            }
            closedir($handle);
            if (!$subdir) @rmdir($dirName);
        }
    }
}


