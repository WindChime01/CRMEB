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

namespace app\admin\model\special;

use app\admin\model\live\LiveGoods;
use app\admin\model\live\LiveStudio;
use app\admin\model\order\StoreOrder;
use app\admin\model\special\SpecialSource;
use app\admin\model\system\RecommendRelation;
use app\admin\model\system\WebRecommendRelation;
use think\cache\driver\Redis;
use traits\ModelTrait;
use basic\ModelBasic;
use think\Url;
use app\admin\model\special\SpecialBuy;
use app\wap\model\routine\RoutineTemplate;
use app\admin\model\wechat\WechatUser;
use app\admin\model\merchant\Merchant;
use service\SystemConfigService;
use service\WechatTemplateService;

/**
 * Class Special 专题
 * @package app\admin\model\special
 */
class Special extends ModelBasic
{
    use ModelTrait;

    protected static function init()
    {
        self::afterUpdate(function () {
            $subjectUrl = getUrlToDomain();
            del_redis_hash($subjectUrl . "wap_index_has", "recommend_list");
            del_redis_hash($subjectUrl . "web_index_has", "recommend_list");
        });
    }

    public function profile()
    {
        return $this->hasOne('SpecialContent', 'special_id', 'id')->field('content');
    }

    public function singleProfile()
    {
        return $this->hasOne('SpecialContent', 'special_id', 'id')->field('is_try,try_content,try_time,link,videoId,file_name,file_type,content');
    }

    public static function PreWhere($alert = '')
    {
        $alert = $alert ? $alert . '.' : '';
        return self::where([$alert . 'is_show' => 1, $alert . 'is_del' => 0, $alert . 'status' => 1]);
    }

    //动态赋值
    public static function getPinkStrarTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public static function getPinkEndTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public static function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public static function getBannerAttr($value)
    {
        return is_string($value) ? json_decode($value, true) : $value;
    }

    public static function getLabelAttr($value)
    {
        return is_string($value) ? json_decode($value, true) : $value;
    }
    //end
    //设置
    public static function getBannerKeyAttr($banner)
    {
        if (is_string($banner)) $banner = json_decode($banner, true);
        if ($banner === false) return [];
        $value = [];
        foreach ($banner as $item) {
            $value[] = [
                'is_show' => false,
                'pic' => $item
            ];
        }
        return $value;
    }

    //获取单个专题
    public static function getOne($id, $is_live = false)
    {
        $special = self::where(['id' => $id, 'is_del' => 0])->find();
        if (!$special) return false;
        if ($special->is_del) return false;
        $special->banner = self::getBannerKeyAttr($special->banner);
        $special->profile->content = htmlspecialchars_decode($special->profile->content);
        if ($is_live) {
            $liveInfo = LiveStudio::where('special_id', $special->id)->find();
            if (!$liveInfo) return self::setErrorInfo('暂未查到直播间');
            if ($liveInfo->is_del) return self::setErrorInfo('直播间已删除无法编辑');
            $liveInfo->live_duration = (strtotime($liveInfo->stop_play_time) - strtotime($liveInfo->start_play_time)) / 60;
            $liveInfo = $liveInfo->toArray();
        } else {
            $liveInfo = [];
        }
        return [$special->toArray(), $liveInfo];
    }

    //获取单个轻专题
    public static function getsingleOne($id)
    {
        $special = self::where(['id' => $id, 'is_del' => 0])->find();
        if (!$special) return false;
        if ($special->is_del) return false;
        $special->singleProfile->content = htmlspecialchars_decode($special->singleProfile->content);
        return $special->toArray();
    }

    //设置条件
    public static function setWhere($where, $alert = '', $model = null)
    {
        $model = $model === null ? new self() : $model;
        if ($alert) $model = $model->alias($alert);
        $alert = $alert ? $alert . '.' : '';
        if (isset($where['order']) && $where['order'])
            $model = $model->order($alert . self::setOrder($where['order']));
        else
            $model = $model->order($alert . 'sort desc,' . $alert . 'id desc');
        if (isset($where['subject_id']) && $where['subject_id'] > 0) $model = $model->where($alert . 'subject_id', $where['subject_id']);
        if (isset($where['mer_id']) && $where['mer_id'] >= 0 && $where['mer_id']!='') $model = $model->where($alert . 'mer_id', $where['mer_id']);
        if (isset($where['store_name']) && $where['store_name'] != '') $model = $model->where($alert . 'title|' . $alert . 'abstract|' . $alert . 'phrase|' . $alert . 'id', "LIKE", "%$where[store_name]%");
        if (isset($where['is_show']) && $where['is_show'] != '') $model = $model->where($alert . 'is_show', $where['is_show']);
        if (isset($where['type']) && $where['type'] && $where['type'] < 7) {
            $model = $model->where($alert . 'type', $where['type']);
        } else if (isset($where['type']) && $where['type'] && $where['type'] == 7) {
            $where['is_light'] = 1;
        }
        if (isset($where['is_light']) && $where['is_light']) $model = $model->where($alert . 'is_light', $where['is_light']);
        if (isset($where['special_type']) && $where['special_type'] != '') {
            $model = $model->where($alert . 'type', $where['special_type']);
        }
        if (isset($where['admin_id']) && $where['admin_id'] != '') $model = $model->where($alert . 'admin_id', $where['admin_id']);
        if (isset($where['start_time']) && $where['start_time'] && isset($where['end_time']) && $where['end_time']) $model = $model->whereTime($alert . 'add_time', 'between', [strtotime($where['start_time']), strtotime($where['end_time'])]);
        return $model->where($alert . 'is_del', 0)->where($alert . 'status', 1);
    }


    public static function getRelationList($where, $special_source)
    {
        if (isset($where['special_type']) && $where['special_type'] == SPECIAL_COLUMN) {
            unset($where['special_type']);
            $where['store_name'] = $where['title'];
            if ($where['type'] == '') {
                $data = self::setWhere($where)->where('id', 'not in', $special_source)->whereIn('type', [SPECIAL_IMAGE_TEXT, SPECIAL_AUDIO, SPECIAL_VIDEO, SPECIAL_OTHER])->page((int)$where['page'], (int)$where['limit'])->select();
                $data = count($data) ? $data->toArray() : [];
                $count = self::setWhere($where)->where('id', 'not in', $special_source)->whereIn('type', [SPECIAL_IMAGE_TEXT, SPECIAL_AUDIO, SPECIAL_VIDEO, SPECIAL_OTHER])->count();
            } else {
                $data = self::setWhere($where)->where('id', 'not in', $special_source)->page((int)$where['page'], (int)$where['limit'])->select();
                $data = count($data) ? $data->toArray() : [];
                $count = self::setWhere($where)->where('id', 'not in', $special_source)->count();
            }
        } elseif (isset($where['special_type']) && $where['special_type'] == SPECIAL_LIVE) {
            unset($where['special_type']);
            $where['store_name'] = $where['title'];
            if ($where['type'] == '') {
                $data = self::setWhere($where)->where('id', 'not in', $special_source)->whereIn('type', [SPECIAL_IMAGE_TEXT, SPECIAL_AUDIO, SPECIAL_VIDEO, SPECIAL_COLUMN, SPECIAL_OTHER])->page((int)$where['page'], (int)$where['limit'])->select();
                $data = count($data) ? $data->toArray() : [];
                $count = self::setWhere($where)->whereIn('type', [SPECIAL_IMAGE_TEXT, SPECIAL_AUDIO, SPECIAL_VIDEO, SPECIAL_COLUMN, SPECIAL_OTHER])->count();
            } else {
                $data = self::setWhere($where)->where('id', 'not in', $special_source)->page((int)$where['page'], (int)$where['limit'])->select();
                $data = count($data) ? $data->toArray() : [];
                $count = self::setWhere($where)->where('id', 'not in', $special_source)->count();
            }
        } elseif (isset($where['special_type']) && $where['special_type'] == SPECIAL_SEVEN) {
            unset($where['special_type']);
            $where['store_name'] = $where['title'];
            $data = self::setWhere($where)->where('id', 'not in', $special_source)->where('type', 'in', [SPECIAL_IMAGE_TEXT, SPECIAL_AUDIO, SPECIAL_VIDEO, SPECIAL_COLUMN, SPECIAL_OTHER])->page((int)$where['page'], (int)$where['limit'])->select();
            $data = count($data) ? $data->toArray() : [];
            $count = self::setWhere($where)->where('id', 'not in', $special_source)->where('type', 'in', [SPECIAL_IMAGE_TEXT, SPECIAL_AUDIO, SPECIAL_VIDEO, SPECIAL_COLUMN, SPECIAL_OTHER])->count();
        } elseif (isset($where['special_type']) && $where['special_type'] == SPECIAL_STORE) {
            unset($where['special_type']);
            $where['store_name'] = $where['title'];
            $data = self::setWhere($where)->where('id', 'not in', $special_source)->whereIn('type', [SPECIAL_IMAGE_TEXT, SPECIAL_AUDIO, SPECIAL_VIDEO, SPECIAL_LIVE, SPECIAL_COLUMN, SPECIAL_OTHER])->page((int)$where['page'], (int)$where['limit'])->select();
            $data = count($data) ? $data->toArray() : [];
            $count = self::setWhere($where)->where('id', 'not in', $special_source)->whereIn('type', [SPECIAL_IMAGE_TEXT, SPECIAL_AUDIO, SPECIAL_VIDEO, SPECIAL_LIVE, SPECIAL_COLUMN, SPECIAL_OTHER])->count();
        } else {
            $where['store_name'] = $where['title'];
            $data = self::setWhere($where)->where('id', 'not in', $special_source)->page((int)$where['page'], (int)$where['limit'])->select();
            $data = count($data) ? $data->toArray() : [];
            $count = self::setWhere($where)->where('id', 'not in', $special_source)->count();
        }
        foreach ($data as $key => &$value) {
            if ($value['mer_id']) {
                $value['mer_name'] = Merchant::where('id', $value['mer_id'])->value('mer_name');
            } else {
                $value['mer_name'] = '总平台';
            }
        }
        return compact('data', 'count');
    }

    /**拼团专题列表
     * @param $where
     */
    public static function getPinkList($where)
    {
        $data = self::setWhere($where, 'A')->field('A.*,S.name as subject_name')
            ->join('__SPECIAL_SUBJECT__ S', 'S.id=A.subject_id', 'LEFT')
            ->page((int)$where['page'], (int)$where['limit'])->where('A.is_pink', 1)->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['recommend'] = RecommendRelation::where('a.link_id', $item['id'])->where('a.type', 'in', [0, 2, 8])->alias('a')
                ->join('__RECOMMEND__ r', 'a.recommend_id=r.id')->column('a.id,r.title');
            $item['pink_end_time'] = $item['pink_end_time'] ? strtotime($item['pink_end_time']) : 0;
            if (!$item['quantity']) {
                $quantity = SpecialSource::getSpecialSourceCount($item['id']);
                $item['quantity'] = $quantity;
                $dat['quantity'] = $quantity;
                self::edit($dat, $item['id'], 'id');
            }
            if ($item['quantity'] > 0 && $item['sum'] == 0 || $item['quantity'] > 0 && $item['sum'] > 0 && $item['quantity'] > $item['sum']) {
                $item['sum'] = $item['quantity'];
                $dat['sum'] = $item['quantity'];
                self::edit($dat, $item['id'], 'id');
            }
            if ($item['mer_id']) {
                $item['mer_name'] = Merchant::where('id', $item['mer_id'])->value('mer_name');
            } else {
                $item['mer_name'] = '总平台';
            }
            //查看拼团状态,如果已结束关闭拼团
            if ($item['is_pink'] && $item['pink_end_time'] < time()) {
                self::update(['is_pink' => 0], ['id' => $item['id']]);
                $item['is_pink'] = 0;
            }
            if (!$item['is_pink']) {
                $item['pink_money'] = 0;
            }
            if ($item['type'] == 4) {
                $studio = LiveStudio::where('special_id', $item['id'])->field('id,stream_name,start_play_time,online_num,is_play')->find();
                if ($studio) {
                    $item['stream_name'] = $studio['stream_name'];
                    $item['live_id'] = $studio['id'];
                    $item['start_play_time'] = $studio['start_play_time'];
                    $item['online_num'] = $studio['online_num'];
                    $item['is_play'] = $studio['is_play'] ? 1 : 0;
                }
            }
        }
        $count = self::setWhere($where, 'A')->join('__SPECIAL_SUBJECT__ S', 'S.id=A.subject_id', 'LEFT')->where('is_pink', 1)->count();
        return compact('data', 'count');
    }

    //查找专题列表
    public static function getSpecialList($where)
    {
        $data = self::setWhere($where, 'A')->field('A.*,S.name as subject_name')
            ->join('__SPECIAL_SUBJECT__ S', 'S.id=A.subject_id', 'LEFT')
            ->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['recommend'] = RecommendRelation::where('a.link_id', $item['id'])->where('a.type', 'in', [0, 2, 8])->alias('a')
                ->join('__RECOMMEND__ r', 'a.recommend_id=r.id')->column('a.id,r.title');
            $item['web_recommend'] = WebRecommendRelation::where('a.link_id', $item['id'])->where('a.type', 'in', [0, 1])->alias('a')
                ->join('__WEB_RECOMMEND__ r', 'a.recommend_id=r.id')->column('a.id,r.title');
            $item['pink_end_time'] = $item['pink_end_time'] ? strtotime($item['pink_end_time']) : 0;
            if (!$item['quantity']) {
                $quantity = SpecialSource::getSpecialSourceCount($item['id']);
                $item['quantity'] = $quantity;
                $dat['quantity'] = $quantity;
                self::edit($dat, $item['id'], 'id');
            }
            if ($item['quantity'] > 0 && $item['sum'] == 0 || $item['quantity'] > 0 && $item['sum'] > 0 && $item['quantity'] > $item['sum']) {
                $item['sum'] = $item['quantity'];
                $dat['sum'] = $item['quantity'];
                self::edit($dat, $item['id'], 'id');
            }
            if ($item['mer_id']) {
                $item['mer_name'] = Merchant::where('id', $item['mer_id'])->value('mer_name');
            } else {
                $item['mer_name'] = '总平台';
            }
            //查看拼团状态,如果已结束关闭拼团
            if ($item['is_pink'] && $item['pink_end_time'] < time()) {
                self::update(['is_pink' => 0], ['id' => $item['id']]);
                $item['is_pink'] = 0;
            }
            if (!$item['is_pink']) {
                $item['pink_money'] = 0;
            }
            if ($where['type'] == 4) {
                $studio = LiveStudio::where('special_id', $item['id'])->field('id,stream_name,start_play_time,online_num,is_play')->find();
                if ($studio) {
                    $item['stream_name'] = $studio['stream_name'];
                    $item['live_id'] = $studio['id'];
                    $item['start_play_time'] = $studio['start_play_time'];
                    $item['online_num'] = $studio['online_num'];
                    $item['is_play'] = $studio['is_play'] ? 1 : 0;
                }
            }
        }
        $count = self::setWhere($where, 'A')->join('__SPECIAL_SUBJECT__ S', 'S.id=A.subject_id', 'LEFT')->count();
        return compact('data', 'count');
    }

    /**审核条件筛选
     * @param $where
     * @param string $alert
     * @param null $model
     * @return Special
     */
    public static function setExamineWhere($where, $alert = '', $model = null)
    {
        $model = $model === null ? new self() : $model;
        if ($alert) $model = $model->alias($alert);
        $alert = $alert ? $alert . '.' : '';
        if (isset($where['order']) && $where['order'])
            $model = $model->order($alert . self::setOrder($where['order']));
        else
            $model = $model->order($alert . 'sort desc,' . $alert . 'id desc');
        if (isset($where['subject_id']) && $where['subject_id']) $model = $model->where($alert . 'subject_id', $where['subject_id']);
        if (isset($where['mer_id']) && $where['mer_id'] != '') $model = $model->where($alert . 'mer_id', $where['mer_id']);
        if (isset($where['store_name']) && $where['store_name'] != '') $model = $model->where($alert . 'title|' . $alert . 'abstract|' . $alert . 'phrase|' . $alert . 'id', "LIKE", "%$where[store_name]%");
        if (isset($where['type']) && $where['type'] && $where['type'] < 7) {
            $model = $model->where($alert . 'type', $where['type']);
        } else if (isset($where['type']) && $where['type'] && $where['type'] == 7) {
            $where['is_light'] = 1;
        }
        if (isset($where['status']) && $where['status'] != '') {
            $model = $model->where($alert . 'status', $where['status']);
        } else {
            $model->where($alert . 'status', 'in', [0, -1]);
        }
        if (isset($where['is_light']) && $where['is_light']) $model = $model->where($alert . 'is_light', $where['is_light']);
        if (isset($where['start_time']) && $where['start_time'] && isset($where['end_time']) && $where['end_time']) $model = $model->whereTime($alert . 'add_time', 'between', [strtotime($where['start_time']), strtotime($where['end_time'])]);
        return $model->where($alert . 'is_del', 0);
    }

    /**专题审核列表
     * @param $where
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getSpecialExamineList($where)
    {
        $data = self::setExamineWhere($where, 'A')->field('A.*,S.name as subject_name')
            ->join('__SPECIAL_SUBJECT__ S', 'S.id=A.subject_id', 'LEFT')
            ->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as $key => &$value) {
            $value['fail_time'] = date('Y-m-d H:i:s', $value['fail_time']);
            if ($value['mer_id']) {
                $value['mer_name'] = Merchant::where('id', $value['mer_id'])->value('mer_name');
            } else {
                $value['mer_name'] = '总平台';
            }
        }
        $count = self::setExamineWhere($where, 'A')->join('__SPECIAL_SUBJECT__ S', 'S.id=A.subject_id', 'LEFT')->count();
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
                    'first' => '尊敬的讲师，您添加的专题审核结果已出。',
                    'keyword1' => '审核失败',
                    'keyword2' => $fail_message,
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '感恩您的努力付出，谢谢！'
                ], '');
            } else {
                $dat['phrase5']['value'] = '专题审核失败';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '专题失败原因:' . $fail_message;
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
                    'first' => '尊敬的讲师，您添加的专题审核结果已出。',
                    'keyword1' => '审核成功',
                    'keyword2' => '专题信息符合标准',
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '感恩您的努力付出，谢谢！'
                ], '');
            } else {
                $dat['phrase5']['value'] = '专题审核成功';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '您添加的专题审核结果已出！';
                RoutineTemplate::sendExamineResult($dat, $uid, '');
            }
        } catch (\Exception $e) {
        }
        return self::edit(compact('status', 'success_time'), $id);
    }

    /**获取试题关联的专题
     * @param $relation_ids
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getKnowledgePoints($relation_ids)
    {
        $data = self::PreWhere()->where('id', 'in', $relation_ids)->select();
        $data = count($data) > 0 ? $data->toArray() : [];
        $count = self::PreWhere()->where('id', 'in', $relation_ids)->count();
        return compact('data', 'count');
    }

    public static function getUserWhere($where)
    {
        return self::alias('s')->join('SpecialBuy b', 's.id=b.special_id')
            ->where(['b.uid' => $where['uid'], 's.is_del' => 0, 'b.is_del' => 0, 'b.type' => 3])
            ->field('s.title,s.type,s.id,s.image');
    }

    /**已获得专题
     * @param $where
     * @return array
     * @throws \think\Exception
     */
    public static function getUserSpecialList($where)
    {
        $data = self::getUserWhere($where)->page($where['page'], $where['limit'])->select();
        $count = self::getUserWhere($where)->count();
        return compact('data', 'count');
    }

    public static function getUserSpecialLists($where, $special_source, $type)
    {
        if ($type == 0) {
            $data = self::setWhere($where)->where('id', 'not in', $special_source)->whereIn('type', [SPECIAL_IMAGE_TEXT, SPECIAL_AUDIO, SPECIAL_VIDEO, SPECIAL_COLUMN, SPECIAL_OTHER])->page((int)$where['page'], (int)$where['limit'])->select();
            $data = count($data) ? $data->toArray() : [];
            $count = self::setWhere($where)->where('id', 'not in', $special_source)->whereIn('type', [SPECIAL_IMAGE_TEXT, SPECIAL_AUDIO, SPECIAL_VIDEO, SPECIAL_COLUMN, SPECIAL_OTHER])->count();
        } elseif ($type == 1) {
            $data = self::setWhere($where)->where('id', 'not in', $special_source)->whereIn('type', [SPECIAL_LIVE])->page((int)$where['page'], (int)$where['limit'])->select();
            $data = count($data) ? $data->toArray() : [];
            $count = self::setWhere($where)->where('id', 'not in', $special_source)->whereIn('type', [SPECIAL_LIVE])->count();
        }
        return compact('data', 'count');
    }

}
