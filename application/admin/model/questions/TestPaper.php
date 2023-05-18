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

namespace app\admin\model\questions;

use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\questions\TestPaperCategory as TestPaperCategoryModel;
use app\admin\model\system\RecommendRelation;
use app\admin\model\questions\TestPaperObtain;
use service\SystemConfigService;
use app\admin\model\merchant\Merchant;
use service\WechatTemplateService;
use app\wap\model\routine\RoutineTemplate;
use app\admin\model\wechat\WechatUser;
use app\admin\model\system\WebRecommendRelation;

/**
 * 试卷列表 Model
 * Class TestPaper
 * @package app\admin\model\questions
 */
class TestPaper extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where)
    {
        $model = self::order('sort desc,add_time desc')->where(['is_del' => 0]);
        if (isset($where['pid']) && $where['pid']) $model = $model->where('tid', $where['pid']);
        if (isset($where['type']) && $where['type']) $model = $model->where('type', $where['type']);
        if (isset($where['is_show']) && $where['is_show'] != '') $model = $model->where('is_show', $where['is_show']);
        if ($where['title'] != '') $model = $model->where('title', 'like', "%$where[title]%");
        if (isset($where['mer_id']) && $where['mer_id'] != '') {
            $model = $model->where('mer_id', $where['mer_id']);
        }
        if (isset($where['status']) && $where['status'] != '') {
            $model = $model->where('status', $where['status']);
        } else {
            $model = $model->where('status', 'in', [-1, 0]);
        }
        return $model;
    }

    /**试卷列表
     * @param $where
     */
    public static function testPaperExercisesList($where)
    {
        $data = self::setWhere($where)->page($where['page'], $where['limit'])->select();
        foreach ($data as $key => &$value) {
            $value['cate'] = TestPaperCategoryModel::where('id', $value['tid'])->value('title');
            $value['recommend'] = RecommendRelation::where('a.link_id', $value['id'])->where('a.type', 'in', '11,12')->alias('a')
                ->join('__RECOMMEND__ r', 'a.recommend_id=r.id')->column('a.id,r.title');
            $value['web_recommend'] = WebRecommendRelation::where('a.link_id', $value['id'])->where('a.type', 'in', '7,8')->alias('a')
                ->join('__WEB_RECOMMEND__ r', 'a.recommend_id=r.id')->column('a.id,r.title');
            $value['types'] = $value['type'] == 1 ? '练习' : '考试';
            if ($value['mer_id']) {
                $value['mer_name'] = Merchant::where('id', $value['mer_id'])->value('mer_name');
            } else {
                $value['mer_name'] = '总平台';
            }
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**发送试卷列表
     * @param $where
     */
    public static function sendTestPaperExercisesList($where)
    {
        $data = self::setWhere($where)->page($where['page'], $where['limit'])->select();
        foreach ($data as $key => &$value) {
            $value['cate'] = TestPaperCategoryModel::where('id', $value['tid'])->value('title');
            $value['types'] = $value['type'] == 1 ? '练习' : '考试';
            $value['fail_time'] = date('Y-m-d H:i:s', $value['fail_time']);
            if ($value['mer_id']) {
                $value['mer_name'] = Merchant::where('id', $value['mer_id'])->value('mer_name');
            } else {
                $value['mer_name'] = '总平台';
            }
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**试卷审核列表
     * @param $where
     */
    public static function testPaperExercisesExamineList($where)
    {
        $data = self::setWhere($where)->page($where['page'], $where['limit'])->select();
        foreach ($data as $key => &$value) {
            $value['cate'] = TestPaperCategoryModel::where('id', $value['tid'])->value('title');
            $value['types'] = $value['type'] == 1 ? '练习' : '考试';
            $value['fail_time'] = date('Y-m-d H:i:s', $value['fail_time']);
            if ($value['mer_id']) {
                $value['mer_name'] = Merchant::where('id', $value['mer_id'])->value('mer_name');
            } else {
                $value['mer_name'] = '总平台';
            }
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**试卷列表
     * @param $type
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function testPaperList($type)
    {
        return self::order('sort desc,add_time desc')->where(['is_del' => 0, 'type' => $type])
            ->field('id,title')
            ->select();
    }

    /**获取练习、试卷
     * @param $where
     */
    public static function testPaperLists($where, $source)
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

    public static function getUserWhere($where)
    {
        return self::alias('t')->join('TestPaperObtain o', 't.id=o.test_id')
            ->where(['o.uid' => $where['uid'], 't.is_del' => 0, 'o.is_del' => 0, 'o.source' => 3])
            ->field('t.title,t.type,t.id');
    }

    /**已获得试卷
     * @param $where
     * @return array
     * @throws \think\Exception
     */
    public static function getUserTestPaperList($where)
    {
        $data = self::getUserWhere($where)->page($where['page'], $where['limit'])->select();
        foreach ($data as $key => &$value) {
            $value['types'] = $value['type'] == 1 ? '练习' : '考试';
        }
        $count = self::getUserWhere($where)->count();
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
                    'first' => '尊敬的讲师，您添加的试卷审核结果已出。',
                    'keyword1' => '审核失败',
                    'keyword2' => $fail_message,
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '感恩您的努力付出，谢谢！'
                ], '');
            } else {
                $dat['phrase5']['value'] = '试卷审核失败';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '试卷失败原因:' . $fail_message;
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
                    'first' => '尊敬的讲师，您添加的试卷审核结果已出。',
                    'keyword1' => '审核成功',
                    'keyword2' => '试卷信息符合标准',
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '感恩您的努力付出，谢谢！'
                ], '');
            } else {
                $dat['phrase5']['value'] = '试卷审核成功';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '您添加的试卷审核结果已出！';
                RoutineTemplate::sendExamineResult($dat, $uid, '');
            }
        } catch (\Exception $e) {
        }
        return self::edit(compact('status', 'success_time'), $id);
    }
}
