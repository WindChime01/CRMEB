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

namespace app\admin\model\download;

use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\download\DataDownloadCategpry;
use app\admin\model\system\RecommendRelation;
use app\admin\model\system\WebRecommendRelation;
use service\SystemConfigService;
use app\admin\model\merchant\Merchant;
use service\WechatTemplateService;
use app\wap\model\routine\RoutineTemplate;
use app\admin\model\wechat\WechatUser;

/**资料 model
 * Class DataDownload
 * @package app\admin\model\download
 */
class DataDownload extends ModelBasic
{
    use ModelTrait;

    /**字段过滤
     * @param string $alias
     * @param null $model
     * @return DataDownload
     */
    public static function PreWhere($alias = '', $model = null)
    {
        if (is_null($model)) $model = new self();
        if ($alias) {
            $model = $model->alias($alias);
            $alias .= '.';
        }
        return $model->where([$alias . 'is_show' => 1, $alias . 'is_del' => 0, $alias . 'status' => 1]);
    }

    /**条件处理
     * @param $where
     * @return DataDownload
     */
    public static function setWhere($where)
    {
        $model = new self();
        $time['data'] = '';
        if (isset($where['start_time']) && isset($where['end_time']) && $where['start_time'] != '' && $where['end_time'] != '') {
            $time['data'] = $where['start_time'] . ' - ' . $where['end_time'];
            $model = $model->getModelTime($time, $model, 'add_time');
        }
        if (isset($where['title']) && $where['title']) {
            $model = $model->where('title|id|abstract', 'like', "%$where[title]%");
        }
        if (isset($where['cate_id']) && $where['cate_id']) {
            $model = $model->where('cate_id', $where['cate_id']);
        }
        if (isset($where['mer_id']) && $where['mer_id'] != '') {
            $model = $model->where('mer_id', $where['mer_id']);
        }
        if (isset($where['is_show']) && $where['is_show'] != '') {
            $model = $model->where('is_show', $where['is_show']);
        }
        if (isset($where['status']) && $where['status'] != '') {
            $model = $model->where('status', $where['status']);
        } else {
            $model = $model->where('status', 'in', [-1, 0]);
        }
        return $model->where('is_del', 0);
    }

    /**获取列表
     * @param $where
     * @return array
     * @throws \think\Exception
     */
    public static function get_download_list($where)
    {
        $data = self::setWhere($where)->order('sort DESC,id DESC')
            ->page((int)$where['page'], (int)$where['limit'])->select();
        foreach ($data as $key => $item) {
            $item['recommend'] = RecommendRelation::where('a.link_id', $item['id'])->where('a.type', 14)->alias('a')
                ->join('__RECOMMEND__ r', 'a.recommend_id=r.id')->column('a.id,r.title');
            $item['web_recommend'] = WebRecommendRelation::where('a.link_id', $item['id'])->where('a.type', 3)->alias('a')
                ->join('__WEB_RECOMMEND__ r', 'a.recommend_id=r.id')->column('a.id,r.title');
            $item['add_time'] = ($item['add_time'] != 0 || $item['add_time']) ? date('Y-m-d H:i:s', $item['add_time']) : '';
            $item['cate_name'] = DataDownloadCategpry::where('id', $item['cate_id'])->value('title');
            if ($item['mer_id']) {
                $item['mer_name'] = Merchant::where('id', $item['mer_id'])->value('mer_name');
            } else {
                $item['mer_name'] = '总平台';
            }
        }
        $data = count((array)$data) ? $data->toArray() : [];
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**获取资料
     * @param $where
     */
    public static function dataDownloadLists($where, $source)
    {
        $data = self::setWhere($where)->where('id', 'not in', $source)->page($where['page'], $where['limit'])->select();
        foreach ($data as $key => $item) {
            if ($item['mer_id']) {
                $item['mer_name'] = Merchant::where('id', $item['mer_id'])->value('mer_name');
            } else {
                $item['mer_name'] = '总平台';
            }
        }
        $count = self::setWhere($where)->where('id', 'not in', $source)->count();
        return compact('data', 'count');
    }

    /**获取列表
     * @param $where
     * @return array
     * @throws \think\Exception
     */
    public static function get_download_examine_list($where)
    {
        $data = self::setWhere($where)->order('sort DESC,id DESC')
            ->page((int)$where['page'], (int)$where['limit'])->select();
        foreach ($data as $key => $item) {
            $item['fail_time'] = date('Y-m-d H:i:s', $item['fail_time']);
            $item['add_time'] = ($item['add_time'] != 0 || $item['add_time']) ? date('Y-m-d H:i:s', $item['add_time']) : '';
            $item['cate_name'] = DataDownloadCategpry::where('id', $item['cate_id'])->value('title');
            if ($item['mer_id']) {
                $item['mer_name'] = Merchant::where('id', $item['mer_id'])->value('mer_name');
            } else {
                $item['mer_name'] = '总平台';
            }
        }
        $data = count((array)$data) ? $data->toArray() : [];
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**审核失败
     * @param $id
     * @param $fail_msg
     * @return bool
     * @throws \think\exception\DbException
     */
    public static function changeFail($id, $mer_id, $fail_message)
    {
        $fail_time = time();
        $status = -1;
        $uid = Merchant::where('id', $mer_id)->value('uid');
        try {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), WechatTemplateService::EXAMINE_RESULT, [
                    'first' => '尊敬的讲师，您添加的资料审核结果已出。',
                    'keyword1' => '审核失败',
                    'keyword2' => $fail_message,
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '感恩您的努力付出，谢谢！'
                ], '');
            } else {
                $dat['phrase5']['value'] = '资料审核失败';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '资料失败原因:' . $fail_message;
                RoutineTemplate::sendExamineResult($dat, $uid, '');
            }
        } catch (\Exception $e) {
        }
        return self::edit(compact('fail_time', 'fail_message', 'status'), $id);
    }

    /**审核成功
     * @param $id
     * @return bool
     */
    public static function changeSuccess($id, $mer_id)
    {
        $success_time = time();
        $status = 1;
        $uid = Merchant::where('id', $mer_id)->value('uid');
        try {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), WechatTemplateService::EXAMINE_RESULT, [
                    'first' => '尊敬的讲师，您添加的资料审核结果已出。',
                    'keyword1' => '审核成功',
                    'keyword2' => '资料信息符合标准',
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '感恩您的努力付出，谢谢！'
                ], '');
            } else {
                $dat['phrase5']['value'] = '资料审核成功';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '您添加的资料审核结果已出！';
                RoutineTemplate::sendExamineResult($dat, $uid, '');
            }
        } catch (\Exception $e) {
        }
        return self::edit(compact('status', 'success_time'), $id);
    }
}
