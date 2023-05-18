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

namespace app\wap\controller;

use app\admin\model\special\RecommendBanner;
use app\admin\model\wechat\WechatQrcode;
use app\wap\model\activity\EventRegistration;
use app\wap\model\live\LiveStudio;
use app\wap\model\special\Lecturer;
use app\wap\model\user\SmsCode;
use app\wap\model\recommend\Recommend;
use app\wap\model\special\SpecialSubject;
use app\wap\model\user\PhoneUser;
use app\wap\model\user\User;
use app\wap\model\wap\Search;
use service\GroupDataService;
use service\JsonService;
use service\SystemConfigService;
use service\UploadService as Upload;
use service\UtilService;
use think\cache\driver\Redis;
use think\Config;
use think\Session;
use think\Cookie;
use think\Url;
use think\Db;

/**首页控制器
 * Class Index
 * @package app\wap\controller
 */
class Index extends AuthController
{

    /*
     * 白名单
     * */
    public static function WhiteList()
    {
        return [
            'agree',
            'index',
            'get_recommend',
            'get_content_recommend',
            'get_host_search',
            'go_search',
            'login',
            'user_login',
            'search',
            'get_unifiend_list',
            'get_recommend_info',
            'more_list',
            'get_more_list',
            'unified_list',
            'qcode_login',
            'get_search_history',
        ];
    }

    /**
     * @return mixed
     */
    public function agree()
    {
        $this->assign('title', SystemConfigService::get('site_name') . '用户付费协议');
        $this->assign('content', get_config_content('user_agreement'));
        return $this->fetch();
    }

    /**
     * 主页
     * @return mixed
     */
    public function index()
    {
        $keep_on_record = SystemConfigService::get('yd_keep_on_record');//网站备案信息
        $live_one_id = Session::get('live_one_id');
        $is_show_or_hide = SystemConfigService::get('is_show_or_hide');
        $activity = [];
        if ($is_show_or_hide == 1) {
            $activity = GroupDataService::getData('home_activity');
        }
        $this->assign([
            'banner' => json_encode(GroupDataService::getData('store_home_banner') ?: []),//首页轮播
            'title' => SystemConfigService::get('site_name'),
            'keep_on_record' => $keep_on_record,
            'activity' => json_encode($activity),
            'liveOne' => json_encode(LiveStudio::getLiveOne($live_one_id)),
        ]);
        return $this->fetch();
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload($name = 'file', $link = 'master')
    {
        $res = Upload::image($name, $link);
        $thumbPath = Upload::thumb($res->dir);
        if ($res->status == 200)
            return JsonService::successful('图片上传成功!', ['name' => $res->fileInfo->getSaveName(), 'url' => Upload::pathToUrl($thumbPath)]);
        else
            return JsonService::fail($res->error);
    }

    /**
     * @param int $qcode_id
     * @throws \think\exception\DbException
     */
    public function qcode_login($qcode_id = 0)
    {
        $qcodeInfo = WechatQrcode::get($qcode_id);
        if ($qcodeInfo) {
            if ($qcodeInfo->scan_id) {
                Session::set('loginUid', $qcodeInfo->scan_id, 'wap');
                Cookie::set('__login_phone', 1);
                Session::set('__login_phone_num' . $qcodeInfo->scan_id, User::where('uid', $qcodeInfo->scan_id)->value('phone'),'wap');
                return JsonService::successful();
            }
        }
        return JsonService::fail('');
    }

    /**
     * 手机号码登录
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login()
    {
        list($phone, $code) = UtilService::postMore([
            ['phone', ''], ['code', '']
        ], $this->request, true);
        if (!$phone) return JsonService::fail('请输入手机号码');
        $userphone = User::where(['uid' => $this->uid])->value('phone');
        if ($userphone) {
            if ($userphone != $phone) return JsonService::fail('当前手机号码尚未绑定此用户');
        }
        if (!$code) return JsonService::fail('请输入验证码');
        $code = md5('is_phone_code' . $code);
        if (!SmsCode::CheckCode($phone, $code)) return JsonService::fail('验证码验证失败');
        SmsCode::setCodeInvalid($phone, $code);
        if (!$userphone) {
            //检查手机号码不存在时
            $phoneUser = PhoneUser::where(['phone' => $phone])->find();
            //H5页面有注册过
            if ($phoneUser) {
                //检测当前用户是否是H5用户
                if (User::where('uid', $phoneUser['uid'])->value('is_h5user')) {
                    $res = User::setUserRelationInfos($phone, $phoneUser['uid'], $this->uid);
                    if ($res === false) return JsonService::fail(User::getErrorInfo());
                }
            }
            if (!isset($res)) {
                if (!user::be(['phone' => $phone])) {
                    User::update(['phone' => $phone], ['uid' => $this->uid]);
                } else {
                    return JsonService::fail('手机号已被使用');
                }
            }
        }
        $name = '__login_phone_number';
        Cookie::set('__login_phone', 1);
        Cookie::set('is_login', 1);
        Session::set('__login_phone_num' . $this->uid, $phone, 'wap');
        Session::set($name, $phone, 'wap');
        return JsonService::successful('登录成功');
    }

    /**
     * 获取手机号码登录状态
     * */
    public function user_login()
    {
        if ($this->phone || $this->force_binding == 2 && $this->isWechat) {
            return JsonService::successful('登录中');
        } else {
            return JsonService::fail('请先登录!');
        }
    }

    public function login_user()
    {
        if ($this->uid)
            return JsonService::successful('登录中');
        else
            return JsonService::fail('请登录!');
    }

    /**
     * 获取主页导航图标
     */
    public function get_recommend()
    {
        return JsonService::successful(Recommend::getRecommend());
    }

    /**
     * 获取主页推荐列表
     * @param int $page
     * @param int $limit
     */
    public function get_content_recommend()
    {
        try {
            //获取推荐列表
            $exists_recommend_reids = $this->redisModel->HEXISTS($this->subjectUrl . "wap_index_has", "recommend_list");
            if (!$exists_recommend_reids) {
                $is_member = isset($this->userInfo['level']) ? $this->userInfo['level'] : 0;
                $recommend_list = json_encode(Recommend::getContentRecommend($this->uid, $is_member));
                $this->redisModel->hset($this->subjectUrl . "wap_index_has", "recommend_list", $recommend_list);
                $this->redisModel->expire($this->subjectUrl . "wap_index_has", 120);
            } else {
                $recommend_list = $this->redisModel->hget($this->subjectUrl . "wap_index_has", "recommend_list");
            }
            return JsonService::successful(json_decode($recommend_list, true));
        } catch (\Exception $e) {
            return JsonService::fail(parent::serRedisPwd($e->getMessage()));
        }
    }

    /**
     * 获取热搜词
     */
    public function get_host_search()
    {
        return JsonService::successful(Search::getHostSearch());
    }

    /**
     * 清除缓存
     */
    public function del_search_history()
    {
        if (!$this->uid) return JsonService::successful('ok');
        $res = Db::name('search_history')->where('uid', $this->uid)->delete();
        try {
            $res1 = $this->redisModel->hdel($this->subjectUrl . "wap_index_has", "search_history_" . $this->uid);
        } catch (\Exception $e) {
            return JsonService::fail(parent::serRedisPwd($e->getMessage()));
        }
        if ($res && $res1) {
            return JsonService::successful('清除成功！');
        } else {
            return JsonService::fail('清除失败！');
        }
    }

    /**
     * 查找搜索历史内容
     * */
    public function get_search_history($search = '', $limit = 0)
    {
        if ($this->uid) {
            try {
                $exists_search_reids = $this->redisModel->HEXISTS($this->subjectUrl . "wap_index_has", "search_history_" . $this->uid);
                if (!$exists_search_reids) {
                    $search_list = Search::userSearchHistory($this->uid);
                    $search_list = count($search_list) > 0 ? json_encode($search_list) : [];
                    if ($search_list) {
                        $this->redisModel->hset($this->subjectUrl . "wap_index_has", "search_history_" . $this->uid, $search_list);
                        $this->redisModel->expire($this->subjectUrl . "wap_index_has", 120);
                    } else {
                        $this->redisModel->hdel($this->subjectUrl . "wap_index_has", "search_history_" . $this->uid);
                    }
                } else {
                    $search_list = $this->redisModel->hget($this->subjectUrl . "wap_index_has", "search_history_" . $this->uid);
                }
            } catch (\Exception $e) {
                return JsonService::fail(parent::serRedisPwd($e->getMessage()));
            }
        } else {
            $search_list = [];
        }
        return JsonService::successful($search_list ? json_decode($search_list, true) : []);
    }

    /**
     * 查找搜索内容
     * */
    public function go_search($search = '', $limit = 0)
    {
        return JsonService::successful(Search::getSearchContent($search, $limit, $this->uid));
    }


    /**
     * 搜索页面
     * */
    public function search()
    {
        return $this->fetch();
    }

    /**
     * 搜索页面查看更多
     * */
    public function more_list($type = 0, $search = '')
    {
        if ($search == '') $this->failed('没有查找相关数据,点我返回上一页', Url::build('index/index'));
        $this->assign(compact('type', 'search'));
        return $this->fetch();
    }

    /**
     * 分页获取搜索更多内容
     * */
    public function get_more_list()
    {
        $where = UtilService::getMore([
            ['type', 0],
            ['search', ''],
            ['page', 1],
            ['limit', 10],
        ]);
        return JsonService::successful(Search::getMoerList($where));
    }

    /**
     * @param int $recommend_id
     * @throws \think\exception\DbException
     */
    public function get_recommend_info($recommend_id = 0)
    {
        return JsonService::successful(Recommend::get($recommend_id));
    }

    /**
     * 主页标签详情
     * @param int $type
     * @param string $title
     * @param int $recommend_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function unified_list($type = 0, $title = '', $recommend_id = 0)
    {
        if (!$recommend_id) $this->failed('您查看的页面走丢了', Url::build('index/index'));
        $recommend = Recommend::get($recommend_id);
        if (!$recommend) $this->failed('您查看的栏目不存在', Url::build('index/index'));
        if ($recommend->is_show == 0) $this->failed('您查看的栏目不存在', Url::build('index/index'));
        $banner = RecommendBanner::valiWhere()->where('recommend_id', $recommend_id)->select();
        $Recommendlist = SpecialSubject::where('is_show', 1)->where('is_del', 0)->where('grade_id', $recommend['grade_id'])->field(['name as title', 'id'])->order('sort desc')->select();
        if ($recommend->typesetting == 4) {
            $recommend->typesetting = 3;
        }
        $this->assign([
            'type' => (int)$type,
            'title' => $title,
            'grade_id' => (int)$recommend->grade_id,
            'image' => $recommend->image,
            'recommend_id' => (int)$recommend_id,
            'typesetting' => (int)$recommend->typesetting,
            'banner' => json_encode($banner),
            'Recommendlist' => json_encode($Recommendlist),
        ]);
        return $this->fetch();
    }

    /**
     * 标签详情列表获取
     * */
    public function get_unifiend_list()
    {
        $where = UtilService::getMore([
            ['page', 1],
            ['limit', 10],
            ['recommend_id', 0],
            ['type', 0],
            ['typesetting', 0],
            ['subject_id', 0],
        ]);
        $is_member = isset($this->userInfo['level']) ? $this->userInfo['level'] : 0;
        return JsonService::successful(Search::getUnifiendList($where, $is_member));
    }

    public function date_empty()
    {
        \think\Session::clear();
        \think\Cookie::clear();
    }
}
